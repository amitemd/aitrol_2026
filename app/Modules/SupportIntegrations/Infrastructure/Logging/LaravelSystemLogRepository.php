<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Logging;

use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemLogPage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

final class LaravelSystemLogRepository implements SystemLogRepository
{
    private const LEVELS = [
        'EMERGENCY',
        'ALERT',
        'CRITICAL',
        'ERROR',
        'WARNING',
        'NOTICE',
        'INFO',
        'DEBUG',
    ];

    public function read(SystemLogFilters $filters, int $page, int $perPage): SystemLogPage
    {
        $files = $this->availableFiles();
        $selectedFile = $this->selectFile($filters->file, $files);

        if ($selectedFile === null) {
            return new SystemLogPage(
                entries: $this->paginator(collect(), 0, $page, $perPage),
                files: $files,
                levelCounts: array_fill_keys(self::LEVELS, 0),
                selectedFile: null,
                fileSize: 0,
                scannedBytes: 0,
                truncated: false,
            );
        }

        $path = $this->resolvePath($selectedFile);
        $fileSize = (int) filesize($path);
        $maximumBytes = max(1024 * 1024, (int) config('observability.log_max_scan_bytes', 16 * 1024 * 1024));
        $content = $this->readTail($path, $maximumBytes);
        $entries = $this->parse($content);
        $levelCounts = array_fill_keys(self::LEVELS, 0);

        foreach ($entries as $entry) {
            $levelCounts[$entry['level']] = ($levelCounts[$entry['level']] ?? 0) + 1;
        }

        $filteredEntries = $entries
            ->when($filters->levels !== [], fn (Collection $items): Collection => $items->whereIn('level', $filters->levels))
            ->when($filters->search !== null, function (Collection $items) use ($filters): Collection {
                $term = Str::lower($filters->search);

                return $items->filter(
                    fn (array $entry): bool => Str::contains(
                        Str::lower($entry['message'].' '.$entry['details'].' '.$entry['environment']),
                        $term,
                    ),
                );
            })
            ->values();

        $maximumEntries = max(100, (int) config('observability.log_max_entries', 10000));

        if ($filteredEntries->count() > $maximumEntries) {
            $filteredEntries = $filteredEntries->take($maximumEntries)->values();
        }

        return new SystemLogPage(
            entries: $this->paginator($filteredEntries, $filteredEntries->count(), $page, $perPage),
            files: $files,
            levelCounts: $levelCounts,
            selectedFile: $selectedFile,
            fileSize: $fileSize,
            scannedBytes: strlen($content),
            truncated: $fileSize > strlen($content),
        );
    }

    /** @return list<array{name: string, size: int, modified_at: string}> */
    private function availableFiles(): array
    {
        $directory = $this->logDirectory();

        if (! is_dir($directory)) {
            return [];
        }

        $files = glob($directory.DIRECTORY_SEPARATOR.'*.log') ?: [];

        return collect($files)
            ->filter(fn (string $path): bool => is_file($path) && is_readable($path))
            ->map(fn (string $path): array => [
                'name' => basename($path),
                'size' => (int) filesize($path),
                'modified_at' => date('Y-m-d H:i:s', (int) filemtime($path)),
            ])
            ->sortByDesc('modified_at')
            ->values()
            ->all();
    }

    /** @param list<array{name: string, size: int, modified_at: string}> $files */
    private function selectFile(?string $requestedFile, array $files): ?string
    {
        $names = array_column($files, 'name');

        if ($requestedFile !== null && in_array($requestedFile, $names, true)) {
            return $requestedFile;
        }

        return $names[0] ?? null;
    }

    private function resolvePath(string $file): string
    {
        $directory = realpath($this->logDirectory());
        $path = realpath($this->logDirectory().DIRECTORY_SEPARATOR.basename($file));

        if ($directory === false || $path === false || ! str_starts_with($path, $directory.DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('El archivo de log solicitado no es válido.');
        }

        return $path;
    }

    private function logDirectory(): string
    {
        return (string) config('observability.log_directory', storage_path('logs'));
    }

    private function readTail(string $path, int $maximumBytes): string
    {
        $size = (int) filesize($path);
        $offset = max(0, $size - $maximumBytes);
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return '';
        }

        try {
            if ($offset > 0) {
                fseek($handle, $offset);
                fgets($handle);
            }

            return (string) stream_get_contents($handle);
        } finally {
            fclose($handle);
        }
    }

    /** @return Collection<int, array{id: string, date: string, environment: string, level: string, message: string, details: string}> */
    private function parse(string $content): Collection
    {
        if (trim($content) === '') {
            return collect();
        }

        $pattern = '/^\[([^\]\r\n]+)\]\s+([A-Za-z0-9_-]+)\.('.implode('|', self::LEVELS).'):\s*(.*?)(?=^\[[^\]\r\n]+\]\s+[A-Za-z0-9_-]+\.[A-Z]+:|\z)/ms';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        return collect(array_reverse($matches))
            ->map(function (array $match): array {
                $body = trim((string) ($match[4] ?? ''));
                [$message, $details] = array_pad(preg_split('/\R/', $body, 2) ?: [], 2, '');
                $safeMessage = $this->redact($message);
                $safeDetails = Str::limit($this->redact($details), 12000, '…');

                return [
                    'id' => sha1(($match[1] ?? '').'|'.($match[2] ?? '').'|'.($match[3] ?? '').'|'.$body),
                    'date' => (string) ($match[1] ?? ''),
                    'environment' => (string) ($match[2] ?? ''),
                    'level' => strtoupper((string) ($match[3] ?? 'INFO')),
                    'message' => Str::limit($safeMessage, 2000, '…'),
                    'details' => $safeDetails,
                ];
            })
            ->values();
    }

    private function redact(string $value): string
    {
        $patterns = [
            '/\b(Bearer)\s+[A-Za-z0-9._~+\/-]+=*/i' => '$1 [PROTEGIDO]',
            '/(["\']?(?:password|passwd|token|secret|authorization|cookie|api[_-]?key)["\']?\s*[:=]\s*["\']?)([^"\',\s}\]]+)/i' => '$1[PROTEGIDO]',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $value) ?? $value;
    }

    /** @param Collection<int, array<string, mixed>> $entries */
    private function paginator(Collection $entries, int $total, int $page, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $entries->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => route('observability.logs.index'),
                'pageName' => 'log_page',
            ],
        );
    }
}
