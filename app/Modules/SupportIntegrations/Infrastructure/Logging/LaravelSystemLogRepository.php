<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Logging;

use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Application\Data\SystemLogActivityMap;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFlameGraph;
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
                activityMap: SystemLogActivityMap::empty(),
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

        $activityMap = $this->buildActivityMap($filteredEntries);

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
            activityMap: $activityMap,
        );
    }

    /** @param Collection<int, array{id: string, date: string, environment: string, level: string, message: string, details: string}> $entries */
    private function buildActivityMap(Collection $entries): SystemLogActivityMap
    {
        $total = $entries->count();

        if ($total === 0) {
            return SystemLogActivityMap::empty();
        }

        $levelCounts = $entries->countBy('level');
        $levels = collect(self::LEVELS)
            ->map(function (string $level) use ($levelCounts, $total): array {
                $count = (int) $levelCounts->get($level, 0);

                return [
                    'level' => $level,
                    'count' => $count,
                    'percentage' => round(($count / $total) * 100, 1),
                ];
            })
            ->filter(fn (array $level): bool => $level['count'] > 0)
            ->values()
            ->all();

        $groupedFamilies = $entries->groupBy(function (array $entry): string {
            return $entry['level'].'|'.$this->familyLabel($entry['message']);
        });
        $maximumFamilies = min(32, max(6, (int) config('observability.log_activity_families', 18)));
        $families = $groupedFamilies
            ->map(function (Collection $familyEntries, string $key) use ($total): array {
                $sample = $familyEntries->first();
                $count = $familyEntries->count();
                $label = Str::after($key, '|');

                return [
                    'id' => sha1($key),
                    'label' => $label,
                    'level' => (string) $sample['level'],
                    'count' => $count,
                    'percentage' => round(($count / $total) * 100, 1),
                    'latest_at' => (string) $sample['date'],
                ];
            })
            ->sortByDesc('count')
            ->take($maximumFamilies)
            ->values();

        return new SystemLogActivityMap(
            total: $total,
            levels: $levels,
            families: $families->all(),
            hiddenFamilyCount: max(0, $groupedFamilies->count() - $families->count()),
            flameGraph: $this->buildFlameGraph($entries),
        );
    }

    /** @param Collection<int, array{id: string, date: string, environment: string, level: string, message: string, details: string}> $entries */
    private function buildFlameGraph(Collection $entries): SystemLogFlameGraph
    {
        if ($entries->isEmpty()) {
            return SystemLogFlameGraph::empty();
        }

        $foldedStacks = [];
        $tracedSamples = 0;
        $maximumDepth = 0;

        foreach ($entries as $entry) {
            [$frames, $hasTrace] = $this->stackFrames($entry);
            $key = implode("\x1F", $frames);
            $duration = $this->durationFromMessage($entry['message']);

            if (! isset($foldedStacks[$key])) {
                $foldedStacks[$key] = [
                    'frames' => $frames,
                    'level' => $entry['level'],
                    'source' => $hasTrace ? 'trace' : 'derived',
                    'samples' => 0.0,
                    'duration' => 0.0,
                ];
            }

            $foldedStacks[$key]['samples']++;
            $foldedStacks[$key]['duration'] += $duration ?? 0.0;
            $tracedSamples += $hasTrace ? 1 : 0;
            $maximumDepth = max($maximumDepth, count($frames));
        }

        $maximumStacks = min(500, max(24, (int) config('observability.log_flame_max_stacks', 240)));
        $profiles = [
            $this->buildFlameProfile(
                key: 'samples',
                label: 'Frecuencia de eventos',
                unit: 'muestras',
                foldedStacks: $foldedStacks,
                weightKey: 'samples',
                maximumStacks: $maximumStacks,
            ),
        ];

        if (collect($foldedStacks)->sum('duration') > 0) {
            $profiles[] = $this->buildFlameProfile(
                key: 'duration',
                label: 'Duración HTTP registrada',
                unit: 'ms',
                foldedStacks: $foldedStacks,
                weightKey: 'duration',
                maximumStacks: $maximumStacks,
            );
        }

        return new SystemLogFlameGraph(
            samples: $entries->count(),
            tracedSamples: $tracedSamples,
            derivedSamples: $entries->count() - $tracedSamples,
            maximumDepth: $maximumDepth,
            profiles: $profiles,
        );
    }

    /**
     * @param  array<string, array{frames: list<string>, level: string, source: string, samples: float, duration: float}>  $foldedStacks
     * @return array{key: string, label: string, unit: string, total: float, root: array<string, mixed>}
     */
    private function buildFlameProfile(
        string $key,
        string $label,
        string $unit,
        array $foldedStacks,
        string $weightKey,
        int $maximumStacks,
    ): array {
        $stacks = collect($foldedStacks)
            ->filter(fn (array $stack): bool => $stack[$weightKey] > 0)
            ->sortByDesc($weightKey);
        $visibleStacks = $stacks->take($maximumStacks)->values();
        $hiddenStacks = $stacks->slice($maximumStacks);

        if ($hiddenStacks->isNotEmpty()) {
            $visibleStacks->push([
                'frames' => ['AITROL', 'Otras rutas agrupadas'],
                'level' => 'INFO',
                'source' => 'derived',
                'samples' => (float) $hiddenStacks->sum('samples'),
                'duration' => (float) $hiddenStacks->sum('duration'),
            ]);
        }

        $root = $this->newFlameNode('AITROL', 'ROOT', 'mixed');

        foreach ($visibleStacks as $stack) {
            $weight = (float) $stack[$weightKey];
            $root['value'] += $weight;
            $node = &$root;

            foreach (array_slice($stack['frames'], 1) as $frame) {
                $frameKey = sha1($frame);

                if (! isset($node['children'][$frameKey])) {
                    $node['children'][$frameKey] = $this->newFlameNode($frame, $stack['level'], $stack['source']);
                }

                $node = &$node['children'][$frameKey];
                $node['value'] += $weight;
                $node['level'] = $this->mostSevereLevel($node['level'], $stack['level']);
                $node['source'] = $node['source'] === $stack['source'] ? $node['source'] : 'mixed';
            }

            $node['self_value'] += $weight;
            unset($node);
        }

        $total = (float) $root['value'];

        return [
            'key' => $key,
            'label' => $label,
            'unit' => $unit,
            'total' => round($total, 2),
            'root' => $this->finalizeFlameNode($root, $total, 'root', 0),
        ];
    }

    /** @return array{label: string, level: string, source: string, value: float, self_value: float, children: array<string, mixed>} */
    private function newFlameNode(string $label, string $level, string $source): array
    {
        return [
            'label' => $label,
            'level' => $level,
            'source' => $source,
            'value' => 0.0,
            'self_value' => 0.0,
            'children' => [],
        ];
    }

    /**
     * @param  array{label: string, level: string, source: string, value: float, self_value: float, children: array<string, mixed>}  $node
     * @return array{id: string, label: string, level: string, source: string, value: float, self_value: float, percentage: float, depth: int, children: list<array<string, mixed>>}
     */
    private function finalizeFlameNode(array $node, float $total, string $path, int $depth): array
    {
        $children = collect($node['children'])
            ->sortBy(fn (array $child): string => Str::lower($child['label']))
            ->values()
            ->map(fn (array $child): array => $this->finalizeFlameNode(
                $child,
                $total,
                $path.'|'.$child['label'],
                $depth + 1,
            ))
            ->all();

        return [
            'id' => sha1($path),
            'label' => $node['label'],
            'level' => $node['level'],
            'source' => $node['source'],
            'value' => round($node['value'], 2),
            'self_value' => round($node['self_value'], 2),
            'percentage' => $total > 0 ? round(($node['value'] / $total) * 100, 2) : 0.0,
            'depth' => $depth,
            'children' => $children,
        ];
    }

    /**
     * @param  array{id: string, date: string, environment: string, level: string, message: string, details: string}  $entry
     * @return array{0: list<string>, 1: bool}
     */
    private function stackFrames(array $entry): array
    {
        $traceFrames = $this->traceFrames($entry['details']);
        $maximumDepth = min(64, max(8, (int) config('observability.log_flame_max_depth', 32)));

        if ($traceFrames !== []) {
            $orderedFrames = array_reverse($traceFrames);
            $orderedFrames = array_slice($orderedFrames, -($maximumDepth - 1));

            return [array_merge(['AITROL'], $orderedFrames), true];
        }

        $context = $this->structuredContext($entry['message']);
        $frames = ['AITROL', $entry['environment'] !== '' ? $entry['environment'] : 'entorno desconocido'];

        if (isset($context['method'])) {
            $frames[] = 'HTTP '.strtoupper((string) $context['method']);
            $frames[] = (string) ($context['route'] ?? $context['path'] ?? 'ruta sin nombre');

            if (isset($context['controller']) && $context['controller'] !== null) {
                $frames[] = (string) $context['controller'];
            }
        } else {
            $frames[] = $entry['level'];
            $frames[] = $this->familyLabel($entry['message']);
        }

        return [array_slice($frames, 0, $maximumDepth), false];
    }

    /** @return list<string> */
    private function traceFrames(string $details): array
    {
        $frames = [];

        foreach (preg_split('/\R/u', $details) ?: [] as $line) {
            if (preg_match('/^\s*#\d+\s+(.+)$/u', $line, $match) === 1) {
                $frames[] = $this->normalizeTraceFrame($match[1]);

                continue;
            }

            if (preg_match('/^\s*at\s+(.+)$/u', $line, $match) === 1) {
                $frames[] = $this->normalizeTraceFrame($match[1]);
            }
        }

        return array_values(array_filter($frames));
    }

    private function normalizeTraceFrame(string $frame): string
    {
        $normalized = Str::of($this->redact($frame))->replace('\\', '/')->squish()->toString();

        if (preg_match('/^.*?\(\d+\):\s*(.+)$/u', $normalized, $match) === 1) {
            $normalized = $match[1];
        }

        return Str::limit($normalized !== '' ? $normalized : 'frame desconocido', 160, '…');
    }

    /** @return array<string, mixed> */
    private function structuredContext(string $message): array
    {
        $start = strpos($message, '{');

        if ($start === false) {
            return [];
        }

        $context = json_decode(substr($message, $start), true);

        return is_array($context) ? $context : [];
    }

    private function durationFromMessage(string $message): ?float
    {
        $duration = $this->structuredContext($message)['duration_ms'] ?? null;

        if (! is_numeric($duration)) {
            return null;
        }

        $value = (float) $duration;

        return is_finite($value) && $value > 0 ? min($value, 86_400_000.0) : null;
    }

    private function mostSevereLevel(string $current, string $candidate): string
    {
        $currentRank = array_search($current, self::LEVELS, true);
        $candidateRank = array_search($candidate, self::LEVELS, true);

        if ($currentRank === false) {
            return $candidate;
        }

        if ($candidateRank === false) {
            return $current;
        }

        return $candidateRank < $currentRank ? $candidate : $current;
    }

    private function familyLabel(string $message): string
    {
        $label = Str::of($message)
            ->replaceMatches('/https?:\/\/[^\s]+/iu', '[URL]')
            ->replaceMatches('/\b[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}\b/iu', '{id}')
            ->replaceMatches('/\b\d+(?:\.\d+)?\b/u', '{n}')
            ->squish()
            ->toString();

        return Str::limit($label !== '' ? $label : 'Evento sin mensaje', 110, '…');
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
