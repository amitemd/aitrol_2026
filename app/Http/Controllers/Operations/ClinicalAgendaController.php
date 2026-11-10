<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Sis_medico\Services\V3ClinicalAgendaService;

final class ClinicalAgendaController extends Controller
{
    public function __construct(private readonly V3ClinicalAgendaService $clinicalAgenda) {}

    public function index(Request $request): View
    {
        return $this->directory($request, false);
    }

    public function schedules(Request $request): View
    {
        return $this->directory($request, true);
    }

    private function directory(Request $request, bool $scheduleDirectory): View
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));

        abort_if($companyId === '', 403, __('new_system.clinical_agenda.company_required'));

        $filters = [
            'specialty' => trim((string) $request->input('especialidad')),
            'search' => trim((string) $request->input('buscar')),
        ];

        return view('new-system.clinical-agenda.index', [
            ...$this->clinicalAgenda->directory($companyId, $filters),
            'title' => $scheduleDirectory
                ? __('new_system.clinical_agenda.schedule_directory_title')
                : __('new_system.clinical_agenda.title'),
            'filters' => $filters,
            'scheduleDirectory' => $scheduleDirectory,
        ]);
    }
}
