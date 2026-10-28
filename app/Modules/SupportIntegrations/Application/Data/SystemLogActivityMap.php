<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemLogActivityMap
{
    /**
     * @param  list<array{level: string, count: int, percentage: float}>  $levels
     * @param  list<array{id: string, label: string, level: string, count: int, percentage: float, latest_at: string}>  $families
     */
    public function __construct(
        public int $total,
        public array $levels,
        public array $families,
        public int $hiddenFamilyCount,
    ) {}

    public static function empty(): self
    {
        return new self(0, [], [], 0);
    }
}
