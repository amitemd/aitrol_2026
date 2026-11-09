<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Habitacion;
use Sis_medico\Sala;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalHospitalDestinationService;
use Sis_medico\Services\ClinicalHospitalTransferService;
use Sis_medico\Services\CurrentHospitalResolver;

class HospitalWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function locations(ClinicalHospitalDestinationService $destinations)
    {
        $this->administrator();
        $companyId = $destinations->companyId();
        $hospitalId = app(CurrentHospitalResolver::class)->id(['company_id' => $companyId]);
        return view('hospital.workflow.locations', [
            'available' => $destinations->available(), 'areas' => $destinations::areas(),
            'salas' => $hospitalId ? Sala::where('id_hospital', $hospitalId)->where('estado', 1)->orderBy('nombre_sala')->get() : collect(),
            'destinations' => $destinations->available() ? DB::table('clinical_hospital_destinations')->where('company_id', $companyId)->get()->keyBy('sala_id') : collect(),
            'rooms' => Habitacion::with('piso')->orderBy('codigo')->get(),
            'roomLocations' => $destinations->available() ? DB::table('clinical_hospital_bed_locations')->where('company_id', $companyId)->get()->keyBy('habitacion_id') : collect(),
            'bedSalas' => $destinations->salas([2, 3, 6, 7]),
        ]);
    }

    public function saveLocation(Request $request, ClinicalHospitalDestinationService $destinations)
    {
        $this->administrator();
        $this->validate($request, ['kind' => 'required|in:sala,habitacion', 'sala_id' => 'required|integer',
            'step' => 'required_if:kind,sala|nullable|integer', 'habitacion_id' => 'required_if:kind,habitacion|nullable|integer']);
        try {
            if ($request->kind === 'sala') $destinations->configureSala($request->sala_id, $request->step, Auth::id());
            else $destinations->configureRoom($request->habitacion_id, $request->sala_id, Auth::id());
            return back()->with('success', 'Destino hospitalario guardado.');
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['destination' => $exception->getMessage()]);
        }
    }

    public function receiveSurgery(Request $request, $transfer, ClinicalHospitalTransferService $service)
    {
        
        $this->validate($request, ['id_paciente' => 'required|string|max:32']);
        try {
            $result = $service->receive($transfer, $request->id_paciente, null);
            return redirect()->route('hospital.documents.index', ['id_solicitud' => $result['solicitud_id']])
                ->with('success', 'Recepcion en Cirugia confirmada.');
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['transfer' => $exception->getMessage()]);
        }
    }

    private function administrator()
    {
        abort_unless(Auth::user() && (int) Auth::user()->estado === 1 && Auth::user()->isSystemUser(),
            403, 'Solo Sistemas puede configurar las ubicaciones hospitalarias.');
    }
}
