<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemLogFilters
{
    /** @param list<string> $levels */
    public function __construct(
        public ?string $file = null,
        public array $levels = [],
        public ?string $search = null,
    ) {}
}
