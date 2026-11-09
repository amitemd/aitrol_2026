<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3ProcedureProductionService;

class ProcedureProductionController extends Controller
{
    private $production;

    public function __construct(V3ProcedureProductionService $production)
    {
        $this->middleware('auth');
        $this->production = $production;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        $years = $this->production->years($companyId);
        $year = $this->selectedYear($request->input('anio'), $years);
        $rows = $this->production->monthly($companyId, $year);
        return $this->page('v3.clinical.production.index', array(
            'years' => $years, 'year' => $year, 'rows' => $rows,
            'totals' => $this->production->totals($rows),
        ), 'Producción de médicos');
    }

    public function doctors(Request $request, $year, $month)
    {
        $year = (int) $year; $month = (int) $month;
        if ($year < 2000 || $year > 2100 || $month < 1 || $month > 12) abort(404);
        return view('v3.clinical.production._doctors', array(
            'year' => $year, 'month' => $month,
            'doctors' => $this->production->doctors($this->companyId($request), $year, $month),
        ));
    }

    private function selectedYear($requested, array $years)
    {
        $requested = (int) $requested;
        if ($requested && in_array($requested, $years, true)) return $requested;
        return count($years) ? (int) $years[0] : (int) date('Y');
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
            'controlador' => 'produccion-medica', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'menu_id' => 666, 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Consulta de producción médica', 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
