<?php

namespace App\Modules\PatientBiopsy\Application\Queries;

use App\Modules\PatientBiopsy\Domain\PatientBiopsyModule;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\InspectOperationalModule;

final readonly class DescribePatientBiopsy
{
    public function __construct(
        private PatientBiopsyModule $module,
        private InspectOperationalModule $inspector,
    ) {}

    public function handle(): OperationalModuleStatus
    {
        return $this->inspector->handle($this->module->definition());
    }
}
