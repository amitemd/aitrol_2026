<?php

namespace App\Modules\Shared\Application\Data;

use App\Modules\Shared\Domain\Operations\OperationalModule;

final readonly class OperationalModuleStatus
{
    /** @param list<OperationalWorkflowStatus> $workflows */
    public function __construct(
        public OperationalModule $module,
        public array $workflows,
    ) {}

    public function readyCount(): int
    {
        return count(array_filter(
            $this->workflows,
            fn (OperationalWorkflowStatus $workflow): bool => $workflow->isReady(),
        ));
    }

    public function totalCount(): int
    {
        return count($this->workflows);
    }

    public function readinessPercentage(): int
    {
        return $this->totalCount() === 0
            ? 0
            : (int) round(($this->readyCount() / $this->totalCount()) * 100);
    }

    public function isReady(): bool
    {
        return $this->readyCount() === $this->totalCount();
    }
}
