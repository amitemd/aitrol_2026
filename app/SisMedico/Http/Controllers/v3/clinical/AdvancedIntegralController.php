<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3AdvancedIntegralExportService;
use Sis_medico\Services\V3AdvancedIntegralService;

class AdvancedIntegralController extends Controller
{
    private $integrals;
    private $exporter;

    public function __construct(V3AdvancedIntegralService $integrals, V3AdvancedIntegralExportService $exporter)
    {
        $this->middleware('auth');
        $this->integrals = $integrals;
        $this->exporter = $exporter;
    }

    public function index(Request $request)
    {
        $filters = $this->integrals->filters($request);
        $catalogs = $this->integrals->catalogs();
        return $this->page('v3.clinical.advanced_integrals.index', array_merge($catalogs, array(
            'filters' => $filters,
            'integrals' => $this->integrals->paginate($filters, $this->companyId($request)),
        )), 'Integrales adelantados');
    }

    public function detail(Request $request, $id)
    {
        return $this->page('v3.clinical.advanced_integrals.detail', array(
            'integral' => $this->integrals->find($id, $this->companyId($request)),
        ), 'Detalle del integral adelantado');
    }

    public function export(Request $request)
    {
        $filters = $this->integrals->filters($request);
        return $this->exporter->download($this->integrals->all($filters, $this->companyId($request)));
    }

    public function legacyIndex()
    {
        return redirect()->route('v3.advanced-integrals.index');
    }

    public function legacySearch(Request $request)
    {
        return redirect()->route('v3.advanced-integrals.index', $this->legacyFilters($request));
    }

    public function legacyReport(Request $request)
    {
        return redirect()->route('v3.advanced-integrals.export', $this->legacyFilters($request));
    }

    public function legacyDetail($id)
    {
        return redirect()->route('v3.advanced-integrals.detail', array('id' => $id));
    }

    private function legacyFilters(Request $request)
    {
        return array_filter(array(
            'tipo' => $request->input('proc_consul'), 'desde' => $request->input('fecha'),
            'hasta' => $request->input('fecha_hasta'), 'cedula' => $request->input('cedula'),
            'paciente' => $request->input('nombres'), 'seguro' => $request->input('id_seguro'),
            'doctor' => $request->input('id_doctor1'), 'especialidad' => $request->input('espid'),
            'procedimiento' => $request->input('id_procedimiento'),
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
            'controlador' => 'integrales-adelantados', 'carpeta' => 'clinical',
            'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title,
            'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
