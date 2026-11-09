<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3PatientCourtesyService;

class PatientCourtesyController extends Controller
{
    private $courtesy;
    public function __construct(V3PatientCourtesyService $courtesy) { $this->middleware('auth'); $this->courtesy = $courtesy; }

    public function index(Request $request)
    {
        $filters = $this->courtesy->filters($request);
        return $this->page(array('filters' => $filters, 'rows' => $this->courtesy->paginate($filters), 'candidates' => $this->courtesy->candidates($filters['paciente'])));
    }

    public function store(Request $request)
    {
        $this->validate($request, array('paciente_id' => 'required|exists:paciente,id|unique:cortesia_paciente,id', 'cortesia' => 'required|in:SI,NO', 'ilimitado' => 'required|in:SI,NO'));
        $this->courtesy->create($request->only(array('paciente_id', 'cortesia', 'ilimitado')), $request->ip());
        return redirect()->route('v3.patient-courtesy.index')->with('success', 'La configuración de cortesía fue registrada.');
    }

    public function update(Request $request, $patient)
    {
        $this->validate($request, array('cortesia' => 'required|in:SI,NO', 'ilimitado' => 'required|in:SI,NO'));
        $this->courtesy->update($patient, $request->only(array('cortesia', 'ilimitado')), $request->ip());
        return redirect()->route('v3.patient-courtesy.index', $request->only('buscar'))->with('success', 'La configuración fue actualizada.');
    }

    public function legacy(Request $request) { return redirect()->route('v3.patient-courtesy.index', array_filter(array('buscar' => $request->input('id')))); }

    private function page(array $payload)
    {
        $title = 'Cortesía de pacientes'; $view = 'v3.clinical.courtesy.index';
        $data = array('controlador' => 'cortesias-pacientes', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data'), $data))); return view('v3.layouts.view_child');
    }
}
