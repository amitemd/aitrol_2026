<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Sis_medico\Services\V3ScheduledCareExportService;
use Sis_medico\Services\V3ScheduledCareService;
use Symfony\Component\HttpFoundation\Response;

final class ScheduledCareController extends Controller
{
    public function __construct(
        private readonly V3ScheduledCareService $scheduledCare,
        private readonly V3ScheduledCareExportService $exporter,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->scheduledCare->filters($request);
        $companyId = $this->companyId($request);

        return view('new-system.scheduled-care.index', [
            'title' => __('new_system.clinical.scheduled_care'),
            'filters' => $filters,
            'rows' => $this->scheduledCare->paginate($filters, $companyId),
            'summary' => $this->scheduledCare->summary($filters, $companyId),
            'catalogs' => $this->scheduledCare->selectedCatalogs($filters),
        ]);
    }

    public function catalog(Request $request): JsonResponse
    {
        return response()->json($this->scheduledCare->searchCatalog(
            (string) $request->input('tipo'),
            (string) $request->input('q', ''),
            max(1, (int) $request->input('page', 1)),
        ));
    }

    public function detail(Request $request, int $id): View
    {
        return view('new-system.scheduled-care.detail', [
            'title' => __('new_system.clinical.care_detail'),
            'care' => $this->scheduledCare->find($id, $this->companyId($request)),
        ]);
    }

    public function export(Request $request): Response
    {
        $filters = $this->scheduledCare->filters($request);

        return $this->exporter->download(
            $this->scheduledCare->all($filters, $this->companyId($request)),
        );
    }

    public function exportPatients(Request $request): Response
    {
        $filters = $this->scheduledCare->filters($request);

        return $this->exporter->download(
            $this->scheduledCare->all($filters, $this->companyId($request)),
            true,
        );
    }

    private function companyId(Request $request): string
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));

        abort_if($companyId === '', 403, __('new_system.clinical.company_required'));

        return $companyId;
    }
}
