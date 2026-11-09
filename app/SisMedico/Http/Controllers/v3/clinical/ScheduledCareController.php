<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3ScheduledCareExportService;
use Sis_medico\Services\V3ScheduledCareService;

class ScheduledCareController extends Controller
{
    private $care;
    private $exporter;

    public function __construct(V3ScheduledCareService $care, V3ScheduledCareExportService $exporter)
    {
        $this->middleware('auth');
        $this->care = $care;
        $this->exporter = $exporter;
    }

    public function index(Request $request)
    {
        $filters = $this->care->filters($request);
        $companyId = $this->companyId($request);
        return $this->page('v3.clinical.scheduled_care.index', array(
            'filters' => $filters, 'rows' => $this->care->paginate($filters, $companyId),
            'summary' => $this->care->summary($filters, $companyId), 'catalogs' => $this->care->selectedCatalogs($filters),
        ), 'Consultas y procedimientos');
    }

    public function catalog(Request $request)
    {
        return response()->json($this->care->searchCatalog(
            (string) $request->input('tipo'),
            (string) $request->input('q', ''),
            max(1, (int) $request->input('page', 1))
        ));
    }

    public function detail(Request $request, $id)
    {
        return $this->page('v3.clinical.scheduled_care.detail', array(
            'care' => $this->care->find($id, $this->companyId($request)),
        ), 'Detalle de la atención');
    }

    public function export(Request $request)
    {
        $filters = $this->care->filters($request);
        return $this->exporter->download($this->care->all($filters, $this->companyId($request)));
    }

    public function exportPatients(Request $request)
    {
        $filters = $this->care->filters($request);
        return $this->exporter->download($this->care->all($filters, $this->companyId($request)), true);
    }

    public function legacyIndex(Request $request) { return redirect()->route('v3.scheduled-care.index', $this->legacyFilters($request)); }
    public function legacySearch(Request $request) { return redirect()->route('v3.scheduled-care.index', $this->legacyFilters($request)); }
    public function legacyReport(Request $request) { return redirect()->route('v3.scheduled-care.export', $this->legacyFilters($request)); }
    public function legacyPatientReport(Request $request) { return redirect()->route('v3.scheduled-care.export-patients', $this->legacyFilters($request)); }
    public function legacyDetail($id) { return redirect()->route('v3.scheduled-care.detail', array('id' => $id)); }

    private function legacyFilters(Request $request)
    {
        return array_filter(array(
            'tipo' => $request->input('proc_consul', $request->input('tipo')), 'unidad' => $request->input('pentax', $request->input('unidad')),
            'desde' => $request->input('fecha', $request->input('desde')), 'hasta' => $request->input('fecha_hasta', $request->input('hasta')),
            'cedula' => $request->input('cedula'), 'paciente' => $request->input('nombres', $request->input('paciente')),
            'doctor' => $request->input('id_doctor1', $request->input('doctor')), 'seguro' => $request->input('id_seguro', $request->input('seguro')),
            'especialidad' => $request->input('espid', $request->input('especialidad')), 'procedimiento' => $request->input('id_procedimiento', $request->input('procedimiento')),
        ), function ($value) { return $value !== null && $value !== ''; });
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function page($view, array $payload, $title)
    {
        $data = array(
            'controlador' => 'consultas-procedimientos', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
