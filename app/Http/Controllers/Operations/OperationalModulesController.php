<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Modules\Administration\Application\Queries\DescribeAdministration;
use App\Modules\CashManagement\Application\Queries\DescribeCashManagement;
use App\Modules\Laboratory\Application\Queries\DescribeLaboratory;
use App\Modules\MedicalScheduling\Application\Queries\DescribeMedicalScheduling;
use App\Modules\PatientBiopsy\Application\Queries\DescribePatientBiopsy;
use App\Modules\Shared\Application\Data\OperationalModuleStatus;
use App\Modules\Shared\Application\Queries\DescribeLegacyArchitecture;
use App\Modules\Shared\Domain\Architecture\BoundedContextDefinition;
use Illuminate\Contracts\View\View;

final class OperationalModulesController extends Controller
{
    public function __construct(
        private readonly DescribeMedicalScheduling $medicalScheduling,
        private readonly DescribePatientBiopsy $patientBiopsy,
        private readonly DescribeLaboratory $laboratory,
        private readonly DescribeAdministration $administration,
        private readonly DescribeCashManagement $cashManagement,
        private readonly DescribeLegacyArchitecture $architecture,
    ) {}

    public function __invoke(): View
    {
        $modules = [
            $this->medicalScheduling->handle(),
            $this->patientBiopsy->handle(),
            $this->laboratory->handle(),
            $this->administration->handle(),
            $this->cashManagement->handle(),
        ];
        $contexts = $this->architecture->handle();

        return view('operations.index', [
            'title' => 'Arquitectura modular',
            'modules' => $modules,
            'contexts' => $contexts,
            'summary' => [
                'legacyModules' => array_sum(array_map(
                    fn (BoundedContextDefinition $context): int => count($context->legacyModules),
                    $contexts,
                )),
                'contexts' => count($contexts),
                'legacyRoutes' => array_sum(array_map(
                    fn (BoundedContextDefinition $context): int => $context->routeCount(),
                    $contexts,
                )),
                'readyWorkflows' => array_sum(array_map(
                    fn (OperationalModuleStatus $module): int => $module->readyCount(),
                    $modules,
                )),
                'totalWorkflows' => array_sum(array_map(
                    fn (OperationalModuleStatus $module): int => $module->totalCount(),
                    $modules,
                )),
            ],
        ]);
    }
}
