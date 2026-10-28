<?php

namespace App\Modules\Shared\Application\Contracts;

use App\Modules\Shared\Application\Data\OperationalWorkflowStatus;
use App\Modules\Shared\Domain\Operations\OperationalWorkflow;

interface RouteReadinessGateway
{
    public function inspect(OperationalWorkflow $workflow): OperationalWorkflowStatus;
}
