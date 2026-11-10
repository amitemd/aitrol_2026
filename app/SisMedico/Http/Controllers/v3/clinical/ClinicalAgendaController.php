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
        return redirect()->route('new-system.clinical-agenda.index', $request->query(), 301);
    }

    public function schedules(Request $request)
    {
        return redirect()->route('new-system.clinical-agenda.schedules', $request->query(), 301);
    }

    public function updateLimits(Request $request, $doctor)
    {
        if (! in_array((int) Auth::user()->id_tipo_usuario, [1, 4, 5], true)) {
            abort(403, 'No puede modificar los cupos del profesional.');
        }
        $this->validate($request, [
            'max_consulta' => 'required|integer|between:1,30',
            'max_procedimiento' => 'required|integer|between:1,30',
        ]);
        $updated = $this->agenda->updateLimits($this->companyId($request), $doctor, $request->input('max_consulta'),
            $request->input('max_procedimiento'), Auth::id(), $request->ip());
        if (! $updated) {
            abort(404, 'El médico no pertenece a la empresa activa.');
        }
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Los cupos del profesional se actualizaron correctamente.',
                'doctor' => (string) $doctor,
                'max_consulta' => (int) $request->input('max_consulta'),
                'max_procedimiento' => (int) $request->input('max_procedimiento'),
            ]);
        }

        return redirect()->route('new-system.clinical-agenda.index')->with('agenda_success', 'Los cupos del profesional se actualizaron correctamente.');
    }

    public function legacy(Request $request)
    {
        return redirect()->route('new-system.clinical-agenda.index', array_filter([
            'especialidad' => $request->input('id_especialidad'), 'buscar' => $request->input('buscar'),
        ], function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') {
            abort(403, 'Seleccione una empresa activa.');
        }

        return $companyId;
    }
}
