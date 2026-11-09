<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3ClinicalAgendaService;

class ClinicalAgendaController extends Controller
{
    private $agenda;

    public function __construct(V3ClinicalAgendaService $agenda)
    {
        $this->middleware('auth');
        $this->agenda = $agenda;
    }

    public function index(Request $request)
    {
        $payload = $this->directoryPayload($request);
        return $this->page('v3.clinical.agenda.index', $payload, 'Agenda clínica');
    }

    public function schedules(Request $request)
    {
        $payload = $this->directoryPayload($request);
        $payload['scheduleDirectory'] = true;
        return $this->page('v3.clinical.agenda.index', $payload, 'Horario de doctores', 'horarios');
    }

    public function updateLimits(Request $request, $doctor)
    {
        if (!in_array((int) Auth::user()->id_tipo_usuario, array(1, 4, 5), true)) abort(403, 'No puede modificar los cupos del profesional.');
        $this->validate($request, array(
            'max_consulta' => 'required|integer|between:1,30',
            'max_procedimiento' => 'required|integer|between:1,30',
        ));
        $updated = $this->agenda->updateLimits($this->companyId($request), $doctor, $request->input('max_consulta'),
            $request->input('max_procedimiento'), Auth::id(), $request->ip());
        if (!$updated) abort(404, 'El médico no pertenece a la empresa activa.');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array(
                'message' => 'Los cupos del profesional se actualizaron correctamente.',
                'doctor' => (string) $doctor,
                'max_consulta' => (int) $request->input('max_consulta'),
                'max_procedimiento' => (int) $request->input('max_procedimiento'),
            ));
        }
        return redirect()->route('v3.clinical-agenda.index')->with('agenda_success', 'Los cupos del profesional se actualizaron correctamente.');
    }

    public function legacy(Request $request)
    {
        return redirect()->route('v3.clinical-agenda.index', array_filter(array(
            'especialidad' => $request->input('id_especialidad'), 'buscar' => $request->input('buscar'),
        ), function ($value) { return $value !== null && $value !== ''; }));
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function directoryPayload(Request $request)
    {
        $filters = array(
            'specialty' => trim((string) $request->input('especialidad')),
            'search' => trim((string) $request->input('buscar')),
        );
        $payload = $this->agenda->directory($this->companyId($request), $filters);
        $payload['filters'] = $filters;
        return $payload;
    }

    private function page($view, array $payload, $title, $submodule = 'index')
    {
        $data = array(
            'controlador' => 'agenda-clinica', 'carpeta' => 'clinical', 'submodulo' => $submodule, 'submodulo_' => $submodule,
            'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
