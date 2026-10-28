<?php

namespace App\Modules\CashManagement\Application\Queries;

use App\Modules\CashManagement\Domain\CashManagementModule;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\InspectOperationalModule;

final readonly class DescribeCashManagement
{
    public function __construct(
        private CashManagementModule $module,
        private InspectOperationalModule $inspector,
    ) {}

    public function handle(): OperationalModuleStatus
    {
        return $this->inspector->handle($this->module->definition());
    }
}
