<?php

namespace App\Modules\Shared\Domain\Operations;

final readonly class OperationalModule
{
    /**
     * @param  list<string>  $capabilities
     * @param  list<OperationalWorkflow>  $workflows
     */
    public function __construct(
        public string $key,
        public string $eyebrow,
        public string $name,
        public string $summary,
        public string $mission,
        public string $accent,
        public array $capabilities,
        public array $workflows,
    ) {}
}
