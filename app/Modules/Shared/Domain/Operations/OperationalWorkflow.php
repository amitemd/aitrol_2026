<?php

namespace App\Modules\Shared\Domain\Operations;

final readonly class OperationalWorkflow
{
    /** @param array<string, string|int> $routeParameters */
    public function __construct(
        public string $routeName,
        public string $name,
        public string $summary,
        public string $stage,
        public string $icon,
        public array $routeParameters = [],
    ) {}
}
