<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemLogFlameGraph
{
    /**
     * @param  list<array{key: string, label: string, unit: string, total: float, root: array<string, mixed>}>  $profiles
     */
    public function __construct(
        public int $samples,
        public int $tracedSamples,
        public int $derivedSamples,
        public int $maximumDepth,
        public array $profiles,
    ) {}

    public static function empty(): self
    {
        return new self(0, 0, 0, 0, []);
    }
}
