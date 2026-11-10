<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Sis_medico\Services\V3ClinicalWorklistService;

final class DailyPatientsController extends Controller
{
    public function __construct(private readonly V3ClinicalWorklistService $worklists) {}

    public function __invoke(Request $request): View
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));

        abort_if($companyId === '', 403, __('new_system.clinical.company_required'));

        $filters = $this->worklists->dailyFilters($request);

        return view('new-system.daily-patients.index', [
            'title' => __('new_system.clinical.daily_patients'),
            'filters' => $filters,
            'rows' => $this->worklists->daily($filters, $companyId),
            'catalogs' => $this->worklists->catalogs(),
        ]);
    }
}
