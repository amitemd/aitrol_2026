<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Logging;

use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Application\Data\SystemLogActivityMap;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFlameGraph;
use App\Modules\SupportIntegrations\Application\Data\SystemLogPage;
use DateTimeImmutable;
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

        $errorStacks = collect($foldedStacks)
            ->filter(fn (array $stack): bool => in_array(
                $stack['level'],
                ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'],
                true,
            ))
            ->all();

        if ($errorStacks !== []) {
            $profiles[] = $this->buildFlameProfile(
                key: 'errors',
                label: 'Errores y excepciones',
                unit: 'eventos',
                foldedStacks: $errorStacks,
                weightKey: 'samples',
                maximumStacks: $maximumStacks,
            );
        }

        $timeline = $this->buildProcessTimeline($entries);

        return new SystemLogFlameGraph(
            samples: $entries->count(),
            tracedSamples: $tracedSamples,
            derivedSamples: $entries->count() - $tracedSamples,
            maximumDepth: $maximumDepth,
            profiles: $profiles,
            timeline: $timeline,
            analysis: $this->buildPerformanceAnalysis($timeline['processes']),
        );
    }

    /**
     * @param  list<array<string, mixed>>  $processes
     * @return array{measured_processes: int, unmeasured_processes: int, error_processes: int, total_duration_ms: float, average_duration_ms: float, p95_duration_ms: float, maximum_duration_ms: float, primary_bottleneck: ?array<string, mixed>, route_bottlenecks: list<array<string, mixed>>, slowest_processes: list<array<string, mixed>>, error_hotspots: list<array<string, mixed>>}
     */
    private function buildPerformanceAnalysis(array $processes): array
    {
        $processCollection = collect($processes);
        $measuredProcesses = $processCollection
            ->filter(fn (array $process): bool => $process['duration_ms'] !== null)
            ->values();
        $durations = $measuredProcesses
            ->pluck('duration_ms')
            ->map(fn (mixed $duration): float => (float) $duration)
            ->sort()
            ->values();
        $totalDuration = (float) $durations->sum();
        $p95Index = max(0, (int) ceil($durations->count() * 0.95) - 1);
        $routeBottlenecks = $measuredProcesses
            ->groupBy(function (array $process): string {
                $route = $process['route'] ?: $process['label'];
                $method = $process['method'] ? $process['method'].' ' : '';

                return $method.$route;
            })
            ->map(function (Collection $routeProcesses, string $route) use ($totalDuration): array {
                $routeDurations = $routeProcesses
                    ->pluck('duration_ms')
                    ->map(fn (mixed $duration): float => (float) $duration)
                    ->sort()
                    ->values();
                $routeTotal = (float) $routeDurations->sum();
                $routeP95Index = max(0, (int) ceil($routeDurations->count() * 0.95) - 1);
                $maximumDuration = (float) $routeDurations->max();

                return [
                    'route' => $route,
                    'controller' => $routeProcesses->pluck('controller')->filter()->first(),
                    'requests' => $routeProcesses->count(),
                    'total_duration_ms' => round($routeTotal, 2),
                    'average_duration_ms' => round($routeTotal / $routeProcesses->count(), 2),
                    'p95_duration_ms' => round((float) $routeDurations->get($routeP95Index, 0.0), 2),
                    'maximum_duration_ms' => round($maximumDuration, 2),
                    'contribution_percentage' => $totalDuration > 0 ? round(($routeTotal / $totalDuration) * 100, 2) : 0.0,
                    'risk' => $this->performanceRisk($maximumDuration),
                ];
            })
            ->sortByDesc('total_duration_ms')
            ->take(10)
            ->values()
            ->all();
        $slowestProcesses = $measuredProcesses
            ->sortByDesc('duration_ms')
            ->take(10)
            ->map(fn (array $process): array => [
                ...$process,
                'risk' => $this->performanceRisk((float) $process['duration_ms']),
            ])
            ->values()
            ->all();
        $errorLevels = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'];
        $errorProcesses = $processCollection
            ->filter(fn (array $process): bool => in_array($process['level'], $errorLevels, true));
        $errorHotspots = $errorProcesses
            ->groupBy(fn (array $process): string => $process['level'].'|'.$process['label'])
            ->map(function (Collection $matchingProcesses): array {
                $latestProcess = $matchingProcesses->sortByDesc('timestamp_ms')->first();

                return [
                    'label' => $latestProcess['label'],
                    'level' => $latestProcess['level'],
                    'occurrences' => $matchingProcesses->count(),
                    'latest_at' => $latestProcess['date'],
                    'route' => $matchingProcesses->pluck('route')->filter()->first(),
                    'controller' => $matchingProcesses->pluck('controller')->filter()->first(),
                    'maximum_trace_depth' => (int) $matchingProcesses->max('trace_depth'),
                ];
            })
            ->sortByDesc('occurrences')
            ->take(8)
            ->values()
            ->all();

        return [
            'measured_processes' => $measuredProcesses->count(),
            'unmeasured_processes' => $processCollection->count() - $measuredProcesses->count(),
            'error_processes' => $errorProcesses->count(),
            'total_duration_ms' => round($totalDuration, 2),
            'average_duration_ms' => $measuredProcesses->isNotEmpty()
                ? round($totalDuration / $measuredProcesses->count(), 2)
                : 0.0,
            'p95_duration_ms' => round((float) $durations->get($p95Index, 0.0), 2),
            'maximum_duration_ms' => round((float) $durations->max(), 2),
            'primary_bottleneck' => $routeBottlenecks[0] ?? null,
            'route_bottlenecks' => $routeBottlenecks,
            'slowest_processes' => $slowestProcesses,
            'error_hotspots' => $errorHotspots,
        ];
    }

    private function performanceRisk(float $duration): string
    {
        $criticalThreshold = max(1.0, (float) config('observability.slow_request_critical_ms', 1000));
        $warningThreshold = min(
            $criticalThreshold,
            max(1.0, (float) config('observability.slow_request_warning_ms', 500)),
        );

        if ($duration >= $criticalThreshold) {
            return 'critical';
        }

        if ($duration >= $warningThreshold) {
            return 'warning';
        }

        return 'normal';
    }

    /**
     * @param  Collection<int, array{id: string, date: string, environment: string, level: string, message: string, details: string}>  $entries
     * @return array{start_at: ?string, end_at: ?string, span_ms: float, total_processes: int, visible_processes: int, truncated: bool, processes: list<array<string, mixed>>}
     */
    private function buildProcessTimeline(Collection $entries): array
    {
        $maximumProcesses = min(1000, max(50, (int) config('observability.log_flame_max_processes', 500)));
        $totalProcesses = $entries->count();
        $processes = $entries
            ->take($maximumProcesses)
            ->map(function (array $entry): ?array {
                $timestamp = $this->timestampMilliseconds($entry['date']);

                if ($timestamp === null) {
                    return null;
                }

                $context = $this->structuredContext($entry['message']);

                return [
                    'id' => $entry['id'],
                    'date' => $entry['date'],
                    'timestamp_ms' => $timestamp,
                    'duration_ms' => $this->durationFromMessage($entry['message']),
                    'label' => $this->familyLabel($entry['message']),
                    'level' => $entry['level'],
                    'environment' => $entry['environment'],
                    'method' => isset($context['method']) ? strtoupper((string) $context['method']) : null,
                    'route' => $context['route'] ?? $context['path'] ?? null,
                    'controller' => $context['controller'] ?? null,
                    'trace_depth' => count($this->traceFrames($entry['details'])),
                ];
            })
            ->filter()
            ->sortBy('timestamp_ms')
            ->values();

        if ($processes->isEmpty()) {
            return [
                'start_at' => null,
                'end_at' => null,
                'span_ms' => 0.0,
                'total_processes' => $totalProcesses,
                'visible_processes' => 0,
                'truncated' => $totalProcesses > 0,
                'processes' => [],
            ];
        }

        $startTimestamp = (float) $processes->min('timestamp_ms');
        $endTimestamp = (float) $processes->max(
            fn (array $process): float => $process['timestamp_ms'] + ($process['duration_ms'] ?? 0.0),
        );
        $startAt = (string) $processes->first()['date'];
        $endAt = (string) $processes->last()['date'];
        $numberedProcesses = $processes
            ->values()
            ->map(fn (array $process, int $index): array => [
                ...$process,
                'process_number' => $index + 1,
                'offset_ms' => round($process['timestamp_ms'] - $startTimestamp, 3),
            ])
            ->all();

        return [
            'start_at' => $startAt,
            'end_at' => $endAt,
            'span_ms' => round(max(1.0, $endTimestamp - $startTimestamp), 3),
            'total_processes' => $totalProcesses,
            'visible_processes' => count($numberedProcesses),
            'truncated' => $totalProcesses > count($numberedProcesses),
            'processes' => $numberedProcesses,
        ];
    }

    private function timestampMilliseconds(string $date): ?float
    {
        try {
            $timestamp = new DateTimeImmutable($date);
        } catch (\Exception) {
            return null;
        }

        return ((float) $timestamp->format('U') * 1000) + ((float) $timestamp->format('u') / 1000);
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
