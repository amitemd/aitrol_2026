<?php

namespace App\Modules\MedicalScheduling\Application\Queries;

use App\Modules\MedicalScheduling\Domain\MedicalSchedulingModule;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\InspectOperationalModule;

final readonly class DescribeMedicalScheduling
{
    public function __construct(
        private MedicalSchedulingModule $module,
        private InspectOperationalModule $inspector,
    ) {}

    public function handle(): OperationalModuleStatus
    {
        return $this->inspector->handle($this->module->definition());
    }
}
