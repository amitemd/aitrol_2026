<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemLogFlameGraph
{
    /**
     * @param  list<array{key: string, label: string, unit: string, total: float, root: array<string, mixed>}>  $profiles
     * @param  array{start_at: ?string, end_at: ?string, span_ms: float, total_processes: int, visible_processes: int, truncated: bool, processes: list<array<string, mixed>>}  $timeline
     * @param  array{measured_processes: int, unmeasured_processes: int, error_processes: int, total_duration_ms: float, average_duration_ms: float, p95_duration_ms: float, maximum_duration_ms: float, primary_bottleneck: ?array<string, mixed>, route_bottlenecks: list<array<string, mixed>>, slowest_processes: list<array<string, mixed>>, error_hotspots: list<array<string, mixed>>}  $analysis
     */
    public function __construct(
        public int $samples,
        public int $tracedSamples,
        public int $derivedSamples,
        public int $maximumDepth,
        public array $profiles,
        public array $timeline,
        public array $analysis,
    ) {}

    public static function empty(): self
    {
        return new self(0, 0, 0, 0, [], [
            'start_at' => null,
            'end_at' => null,
            'span_ms' => 0.0,
            'total_processes' => 0,
            'visible_processes' => 0,
            'truncated' => false,
            'processes' => [],
        ], [
            'measured_processes' => 0,
            'unmeasured_processes' => 0,
            'error_processes' => 0,
            'total_duration_ms' => 0.0,
            'average_duration_ms' => 0.0,
            'p95_duration_ms' => 0.0,
            'maximum_duration_ms' => 0.0,
            'primary_bottleneck' => null,
            'route_bottlenecks' => [],
            'slowest_processes' => [],
            'error_hotspots' => [],
        ]);
    }
}
