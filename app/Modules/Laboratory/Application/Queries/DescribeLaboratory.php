<?php

namespace App\Modules\Laboratory\Application\Queries;

use App\Modules\Laboratory\Domain\LaboratoryModule;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\InspectOperationalModule;

final readonly class DescribeLaboratory
{
    public function __construct(
        private LaboratoryModule $module,
        private InspectOperationalModule $inspector,
    ) {}

    public function handle(): OperationalModuleStatus
    {
        return $this->inspector->handle($this->module->definition());
    }
}
