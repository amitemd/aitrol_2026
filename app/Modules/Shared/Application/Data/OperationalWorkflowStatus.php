<?php

namespace App\Modules\Shared\Application\Data;

use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final readonly class OperationalWorkflowStatus
{
    /** @param list<string> $methods */
    public function __construct(
        public OperationalWorkflow $workflow,
        public bool $routeExists,
        public bool $controllerCallable,
        public array $methods,
        public ?string $uri,
        public ?string $url,
        public ?string $action,
    ) {}

    public function isReady(): bool
    {
        return $this->routeExists && $this->controllerCallable;
    }
}
