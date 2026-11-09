<?php

namespace Sis_medico\Http\Controllers\hospital;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\OncologyConsult;
use Sis_medico\OncologyConsultEvent;

class OncologyConsultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!$this->available()) return redirect()->route('hospital.index')->with('error', 'Aplique la migración de Interconsultas oncológicas.');
        $query = $this->companyConsults()->orderByRaw("CASE priority WHEN 'EMERGENCY' THEN 1 WHEN 'URGENT' THEN 2 ELSE 3 END")
            ->orderBy('due_at')->orderBy('id', 'desc');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('patient')) {
            $term = trim($request->patient);
            $query->where(function ($builder) use ($term) {
                $builder->where('patient_id', 'like', '%' . $term . '%')->orWhere('patient_name', 'like', '%' . $term . '%')->orWhere('cancer_type', 'like', '%' . $term . '%');
            });
        }
        $consults = $query->paginate(15);
        $stats = array(
            'active' => $this->companyConsults()->whereNotIn('status', array('RESPONDED', 'CANCELLED'))->count(),
            'urgent' => $this->companyConsults()->whereIn('priority', array('URGENT', 'EMERGENCY'))->whereNotIn('status', array('RESPONDED', 'CANCELLED'))->count(),
            'overdue' => $this->companyConsults()->whereNotIn('status', array('RESPONDED', 'CANCELLED'))->where('due_at', '<', Carbon::now())->count(),
            'responded' => $this->companyConsults()->where('status', 'RESPONDED')->where('completed_at', '>=', Carbon::now()->startOfMonth())->count(),
        );
        return view('hospital.oncology_consults.index', compact('consults', 'stats'));
    }

    public function store(Request $request)
    {
        $this->validate($request, array(
            'patient_id' => 'required|string|max:20', 'patient_name' => 'required|string|max:191',
            'encounter_id' => 'nullable|string|max:36', 'cancer_type' => 'required|string|max:191',
            'clinical_stage' => 'nullable|string|max:60', 'target_specialty' => 'required|in:Oncología clínica,Cirugía oncológica,Hematología oncológica,Radio-oncología,Cuidados paliativos,Comité oncológico',
            'priority' => 'required|in:ROUTINE,URGENT,EMERGENCY', 'reason' => 'required|string|min:10|max:3000',
            'clinical_summary' => 'required|string|min:20|max:5000', 'multidisciplinary' => 'nullable|boolean',
        ));
        $hours = array('ROUTINE' => 72, 'URGENT' => 24, 'EMERGENCY' => 2);
        $consult = OncologyConsult::create(array_merge($request->only(
            'patient_id', 'patient_name', 'encounter_id', 'cancer_type', 'clinical_stage', 'target_specialty', 'priority', 'reason', 'clinical_summary'
        ), array(
            'id_empresa' => $this->companyId(), 'status' => 'REQUESTED', 'multidisciplinary' => $request->boolean('multidisciplinary'),
            'requested_by' => auth()->user()->id, 'requested_at' => Carbon::now(), 'due_at' => Carbon::now()->addHours($hours[$request->priority]),
            'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id,
        )));
        $this->event($consult, 'CREATED', null, 'REQUESTED', 'Interconsulta oncológica registrada.', $request);
        return back()->with('success', 'Interconsulta oncológica registrada con tiempo objetivo.');
    }

    public function transition(Request $request, $id)
    {
        $this->validate($request, array(
            'status' => 'required|in:TRIAGE,ASSIGNED,IN_REVIEW,RESPONDED,CANCELLED',
            'assigned_to' => 'nullable|string|max:191', 'response_plan' => 'nullable|string|max:5000',
            'next_action_at' => 'nullable|date', 'notes' => 'nullable|string|max:2000',
        ));
        try {
            DB::transaction(function () use ($request, $id) {
                $consult = $this->companyConsults()->lockForUpdate()->findOrFail($id);
                $map = array(
                    'REQUESTED' => array('TRIAGE', 'CANCELLED'), 'TRIAGE' => array('ASSIGNED', 'CANCELLED'),
                    'ASSIGNED' => array('IN_REVIEW', 'RESPONDED', 'CANCELLED'),
                    'IN_REVIEW' => array('RESPONDED', 'CANCELLED'), 'RESPONDED' => array(), 'CANCELLED' => array(),
                );
                $to = $request->status;
                if (!isset($map[$consult->status]) || !in_array($to, $map[$consult->status], true)) throw new \RuntimeException('La transición solicitada no es válida para este caso.');
                if ($to === 'ASSIGNED' && strlen(trim((string) $request->assigned_to)) < 3) throw new \RuntimeException('Indique el profesional o equipo oncológico responsable.');
                if ($to === 'RESPONDED' && strlen(trim((string) $request->response_plan)) < 20) throw new \RuntimeException('La respuesta debe incluir un plan oncológico de al menos 20 caracteres.');
                if ($to === 'CANCELLED' && strlen(trim((string) $request->notes)) < 5) throw new \RuntimeException('La cancelación requiere un motivo.');

                $from = $consult->status;
                $data = array('status' => $to, 'updated_by' => auth()->user()->id);
                if ($to === 'ASSIGNED') $data = array_merge($data, array('assigned_to' => trim($request->assigned_to), 'accepted_at' => Carbon::now()));
                if ($to === 'RESPONDED') $data = array_merge($data, array(
                    'response_plan' => trim($request->response_plan), 'next_action_at' => $request->next_action_at ?: null, 'completed_at' => Carbon::now(),
                ));
                $consult->update($data);
                $this->event($consult, 'STATUS_CHANGED', $from, $to, $request->notes ?: $request->response_plan, $request);
            });
            return back()->with('success', 'El caso oncológico fue actualizado con trazabilidad.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    private function event(OncologyConsult $consult, $type, $from, $to, $notes, Request $request)
    {
        OncologyConsultEvent::create(array(
            'oncology_consult_id' => $consult->id, 'event_type' => $type, 'from_status' => $from,
            'to_status' => $to, 'notes' => $notes, 'actor_id' => auth()->user()->id, 'ip_address' => $request->ip(),
        ));
    }

    private function available()
    {
        return Schema::hasTable('oncology_consults') && Schema::hasTable('oncology_consult_events');
    }

    private function companyId()
    {
        return session('id_empresa') !== null ? (string) session('id_empresa') : null;
    }

    private function companyConsults()
    {
        $query = OncologyConsult::query();
        return $this->companyId() === null ? $query->whereNull('id_empresa') : $query->where('id_empresa', $this->companyId());
    }
}
