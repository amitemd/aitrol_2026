<?php

namespace App\Modules\Administration\Application\Queries;

use App\Modules\Administration\Domain\AdministrationModule;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\InspectOperationalModule;

final readonly class DescribeAdministration
{
    public function __construct(
        private AdministrationModule $module,
        private InspectOperationalModule $inspector,
    ) {}

    public function handle(): OperationalModuleStatus
    {
        return $this->inspector->handle($this->module->definition());
    }
}
