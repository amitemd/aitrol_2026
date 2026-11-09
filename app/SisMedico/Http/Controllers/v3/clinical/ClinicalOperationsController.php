<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3ClinicalWorklistService;
use Sis_medico\Services\V3FallRiskService;

class ClinicalOperationsController extends Controller
{
    private $worklists;
    private $fallRisk;

    public function __construct(V3ClinicalWorklistService $worklists, V3FallRiskService $fallRisk)
    {
        $this->middleware('auth');
        $this->worklists = $worklists;
        $this->fallRisk = $fallRisk;
    }

    public function nursing(Request $request)
    {
        $filters = $this->worklists->nursingFilters($request);
        return $this->page('v3.clinical.operations.nursing', array('filters' => $filters, 'rows' => $this->worklists->nursing($filters, $this->companyId($request))), 'Signos vitales y preparación', 'pacientes-enfermeria');
    }

    public function daily(Request $request)
    {
        $filters = $this->worklists->dailyFilters($request);
        return $this->page('v3.clinical.operations.daily', array('filters' => $filters, 'rows' => $this->worklists->daily($filters, $this->companyId($request)), 'catalogs' => $this->worklists->catalogs()), 'Pacientes del día', 'pacientes-dia');
    }

    public function fallRisk(Request $request)
    {
        $filters = $this->fallRisk->filters($request);
        $beds = $this->fallRisk->beds($filters);
        return $this->page('v3.clinical.operations.fall_risk', array('filters' => $filters, 'beds' => $beds, 'hospitals' => $this->fallRisk->hospitals(), 'summary' => $this->fallRisk->summary($beds)), 'Riesgo de caída y camillas', 'riesgo-caida');
    }

    public function fallRiskRecords(Request $request)
    {
        $filters = $this->fallRisk->recordFilters($request);
        $records = $this->fallRisk->records($filters);
        return $this->page('v3.clinical.operations.fall_risk_records', array(
            'filters' => $filters,
            'records' => $records,
        ), 'Registro de camillas', 'riesgo-caida');
    }

    public function legacyNursing(Request $request)
    {
        return redirect()->route('v3.clinical-operations.nursing', $this->only($request, array('fecha', 'fechafin', 'nombres', 'cedula')));
    }

    public function legacyDaily(Request $request)
    {
        return redirect()->route('v3.clinical-operations.daily', $this->only($request, array('fecha', 'fecha_hasta', 'nombres', 'cedula', 'espid', 'id_doctor1', 'id_seguro')));
    }

    public function legacyFallRisk(Request $request)
    {
        return redirect()->route('v3.clinical-operations.fall-risk', array_filter(array(
            'hospital' => $request->input('hospital_tipo'), 'buscar' => $request->input('buscar'),
        ), function ($value) { return $value !== null && $value !== ''; }));
    }

    public function legacyFallRiskRecords(Request $request)
    {
        return redirect()->route('v3.clinical-operations.fall-risk-records', $this->only($request, array('desde', 'hasta', 'buscar')));
    }

    private function only(Request $request, array $keys)
    {
        $values = array();
        foreach ($keys as $key) {
            if ($request->input($key) !== null && $request->input($key) !== '') $values[$key] = $request->input($key);
        }
        return $values;
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function page($view, array $payload, $title, $module)
    {
        $data = array('controlador' => $module, 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
