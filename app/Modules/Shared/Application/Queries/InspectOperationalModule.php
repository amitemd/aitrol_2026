<?php

namespace App\Modules\Shared\Application\Queries;

use App\Modules\Shared\Application\Contracts\RouteReadinessGateway;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Domain\Operations\OperationalModule;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

final readonly class InspectOperationalModule
{
    public function __construct(private RouteReadinessGateway $routes) {}

    public function handle(OperationalModule $module): OperationalModuleStatus
    {
        return new OperationalModuleStatus(
            module: $module,
            workflows: array_map(
                fn (OperationalWorkflow $workflow) => $this->routes->inspect($workflow),
                $module->workflows,
            ),
        );
    }
}
