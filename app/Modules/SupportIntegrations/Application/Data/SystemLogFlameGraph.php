<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemLogFlameGraph
{
    /**
     * @param  list<array{key: string, label: string, unit: string, total: float, root: array<string, mixed>}>  $profiles
     * @param  array{start_at: ?string, end_at: ?string, span_ms: float, total_processes: int, visible_processes: int, truncated: bool, processes: list<array<string, mixed>>}  $timeline
     */
    public function __construct(
        public int $samples,
        public int $tracedSamples,
        public int $derivedSamples,
        public int $maximumDepth,
        public array $profiles,
        public array $timeline,
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
        ]);
    }
}
