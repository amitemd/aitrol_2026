<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\ClinicalEmergencyFlow;
use Sis_medico\ClinicalEmergencyLocation;
use Sis_medico\ClinicalEmergencyTriageAssessment;
use Sis_medico\ClinicalEmergencyIntakeUpdate;
use Sis_medico\Services\ClinicalEmergencyProgress;
use Sis_medico\Ho_Prioridad_Emergencia;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Tipo_Emergencia;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Services\ClinicalHospitalDestinationService;
use Sis_medico\Services\ClinicalEmergencyFlowPolicy;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalEmergencyFlowService;
use Sis_medico\Services\CurrentHospitalResolver;

class EmergencyFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, ClinicalEmergencyFlowService $service)
    {
        $context = $service->context($request);
        $hospitalId = $context['hospital_id'];
        $query = Ho_Solicitud::with(['paciente', 'encounter'])
            ->join('clinical_encounters as ce', 'ce.id', '=', 'ho_solicitud.encounter_id')
            ->where('ce.id_empresa', $context['company_id'])->where('ce.id_hospital', $hospitalId)
            ->where('ce.status', 'ACTIVE')->whereNull('ce.ended_at')
            ->whereIn('ce.clinical_state', ['EMERGENCY_ACTIVE', 'TRANSFER_PENDING', 'CLOSURE_PENDING'])
            ->where('ho_solicitud.estado', 1)->whereIn('ho_solicitud.estado_paso', [1, 2])
            ->whereNull('ho_solicitud.fecha_salida')->select('ho_solicitud.*');
        $query->where(function ($query) {
            $query->whereNotIn('ho_solicitud.id', ClinicalEmergencyFlow::whereNotNull('closed_at')->select('solicitud_id'))
                ->orWhere('ce.clinical_state', 'CLOSURE_PENDING');
        });
        $solicitudes = $query->orderBy('fecha_ingreso', 'asc')->limit(250)->get();
        $ids = $solicitudes->pluck('id')->all();
        $flows = ClinicalEmergencyFlow::with('location')->whereIn('solicitud_id', $ids)->get()->keyBy('solicitud_id');
        $progress = ClinicalEmergencyIntakeUpdate::whereIn('id', function ($query) use ($ids) {
            $query->selectRaw('MAX(id)')->from('clinical_emergency_intake_updates')->whereIn('solicitud_id', $ids)->groupBy('solicitud_id');
        })->get()->keyBy('solicitud_id');
        $latest = ClinicalEmergencyTriageAssessment::whereIn('solicitud_id', $ids)
            ->where('sequence', function ($query) {
                $query->selectRaw('MAX(recent.sequence)')->from('clinical_emergency_triage_assessments as recent')
                    ->whereColumn('recent.solicitud_id', 'clinical_emergency_triage_assessments.solicitud_id');
            })->get()->keyBy('solicitud_id');
        $legacyLatest = DB::table('ho_triaje_manchester as t')
            ->whereIn('t.id', function ($query) use ($ids) {
                $query->selectRaw('MAX(id)')->from('ho_triaje_manchester')
                    ->whereIn('id_ho_solicitud', $ids)->groupBy('id_ho_solicitud');
            })
            ->leftJoin('ho_prioridad_emergencia as p', 'p.id', '=', 't.prioridad')
            ->whereIn('t.id_ho_solicitud', $ids)->select('t.*', 'p.prioridad as priority_level', 'p.nombre as priority_name', 'p.color as priority_color')->get()->keyBy('id_ho_solicitud');

        $patients = array();
        foreach ($solicitudes as $solicitud) {
            try {
                $encounter = $service->assertBoardAccess($solicitud, $request, $context);
            } catch (ClinicalAccessDeniedException $e) {
                continue;
            }
            $flow = $flows->get($solicitud->id);
            $triage = $latest->get($solicitud->id);
            if (is_null($triage)) $triage = $legacyLatest->get($solicitud->id);
            if ($triage && !ClinicalEmergencyFlowPolicy::validTriage(
                $triage->priority_level,
                isset($triage->clinical_reason) ? $triage->clinical_reason : $triage->motivo_consulta
            )) $triage = null;
            $level = $triage ? (int) $triage->priority_level : ($flow && $flow->critical_arrival ? 1 : 9);
            $arrived = strtotime($flow ? $flow->arrived_at : $solicitud->fecha_ingreso);
            $elapsed = $arrived ? max(0, (int) floor((time() - $arrived) / 60)) : 0;
            $limits = array(1 => 0, 2 => 10, 3 => 60, 4 => 120, 5 => 240);
            $limit = isset($limits[$level]) ? $limits[$level] : null;
            $patients[] = array(
                'solicitud' => $solicitud,
                'flow' => $flow,
                'triage' => $triage,
                'progress' => $progress->get($solicitud->id),
                'level' => $level,
                'elapsed' => $elapsed,
                'limit' => $limit,
                'version' => $flow ? (int) $flow->version : 0,
                'clinical_state' => $encounter->clinical_state,
                'overdue' => !is_null($limit) && ($level === 1 || $elapsed >= $limit)
            );
        }
        usort($patients, function ($a, $b) {
            if ($a['level'] !== $b['level']) return $a['level'] - $b['level'];
            return $b['elapsed'] - $a['elapsed'];
        });

        $destinationService = app(ClinicalHospitalDestinationService::class);
        $salas = $destinationService->salas([3, 5, 6, 7])->filter(function ($sala) use ($hospitalId) {
            return (int) $sala->id_hospital === (int) $hospitalId;
        })->values();
        $transferBeds = [];
        $transferBedStatus = [];
        if ($destinationService->available()) {
            $locations = DB::table('clinical_hospital_bed_locations')->where('company_id', $context['company_id'])
                ->whereIn('sala_id', $salas->pluck('id'))->get();
            $roomIds = $locations->pluck('habitacion_id')->unique();
            $beds = Cama::with('habitacion.piso')->whereIn('id_habitacion', $roomIds)->where('estado', '<>', 0)
                ->get(['id', 'id_habitacion', 'codigo', 'estado']);
            $bedIds = $beds->modelKeys();
            $occupied = DB::table('ho_hospitalizacion')->whereIn('id_cama', $bedIds)->where('estado', 1)->pluck('id_cama')->all();
            $locations = $locations->groupBy('sala_id');
            $transactions = CamaTransaccion::whereIn('id', CamaTransaccion::selectRaw('MAX(id)')
                ->whereIn('id_cama', $bedIds)->groupBy('id_cama'))->get()->keyBy('id_cama');
            foreach ($salas as $sala) {
                $rooms = collect($locations->get($sala->id, []))->pluck('habitacion_id');
                $transferBeds[$sala->id] = $beds->filter(function ($bed) use ($rooms) {
                    return $bed->habitacion && $rooms->contains($bed->id_habitacion);
                })->values()->map(function ($bed) use ($occupied, $transactions) {
                    $transaction = $transactions->get($bed->id);
                    $isOccupied = in_array($bed->id, $occupied) || (int) $bed->estado === 3
                        || ($transaction && ($transaction->estado_cama === 'ocupada' || $transaction->id_paciente));
                    $isUnavailable = (int) $bed->habitacion->estado === 4 || (int) $bed->estado === 4
                        || ($transaction && $transaction->estado_cama === 'no_disponible');
                    $isPreparing = (int) $bed->estado === 2 || ($transaction && $transaction->estado_cama === 'preparacion');
                    $available = !$isOccupied && !$isUnavailable && !$isPreparing && (int) $bed->estado === 1
                        && $transaction && $transaction->estado_cama === 'disponible';
                    $status = $isOccupied ? 'Ocupada' : ($isUnavailable ? 'No disponible'
                        : ($isPreparing ? 'Preparación' : ($available ? 'Libre' : 'No disponible')));
                    return [
                        'id' => $bed->id,
                        'code' => $bed->codigo,
                        'room_id' => $bed->id_habitacion,
                        'room' => $bed->habitacion ? $bed->habitacion->codigo : 'Habitación',
                        'floor_id' => $bed->habitacion ? $bed->habitacion->id_piso : null,
                        'floor' => $bed->habitacion && $bed->habitacion->piso ? $bed->habitacion->piso->nombre_piso : 'Piso',
                        'status' => $status,
                        'available' => $available
                    ];
                })->all();
                $transferBedStatus[$sala->id] = $rooms->isEmpty() ? 'UNCONFIGURED_ROOMS'
                    : (collect($transferBeds[$sala->id])->contains(function ($bed) {
                        return $bed['available'];
                    })
                        ? 'READY' : 'NO_AVAILABLE_BEDS');
            }
        }

        $priorities = Ho_Prioridad_Emergencia::where('estado', 1)->orderBy('prioridad')->get();
        $emergencyTypes = Ho_Tipo_Emergencia::where('estado', 1)->orderBy('nombre')->get();

        return view('hospital.emergencia.flow.index', array(
            'patients' => $patients,
            'locations' => ClinicalEmergencyLocation::where('is_active', 1)
                ->where(function ($query) use ($hospitalId) {
                    $query->whereNull('hospital_id')->orWhere('hospital_id', $hospitalId);
                })
                ->orderBy('sort_order')->get(),
            'priorities' => $priorities,
            'emergencyTypes' => $emergencyTypes,
            'prioridad' => $priorities,
            'tipos_emergencia' => $emergencyTypes,
            'dispositions' => $service->dispositions(),
            'salas' => $salas,
            'transferBeds' => $transferBeds,
            'transferBedStatus' => $transferBedStatus
        ));
    }

    public function triage(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $service->recordTriage(Ho_Solicitud::findOrFail($solicitud), $request->all(), $request);
            return back()->with('success', 'Nueva valoracion de triaje registrada.');
        });
    }

    public function progress(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        $source = Ho_Solicitud::findOrFail($solicitud);
        $service->assertAccess($source, $request);
        $flow = ClinicalEmergencyFlow::where('solicitud_id', $source->id)->firstOrFail();
        $history = ClinicalEmergencyIntakeUpdate::where('solicitud_id', $source->id)->orderByDesc('id')->limit(30)->get();
        $triage = ClinicalEmergencyTriageAssessment::where('solicitud_id', $source->id)->orderByDesc('id')->first();
        $draft = ClinicalEmergencyProgress::draft($history->first(), $triage ? $triage->id : null);
        if ($request->expectsJson()) return response()->json(['version' => (int)$flow->version, 'draft' => $draft]);
        return view('hospital.emergencia.progress', [
            'source' => $source,
            'flow' => $flow,
            'embedded' => $request->boolean('embedded'),
            'history' => $history,
            'draft' => $draft,
            'triage' => $triage,
            'priorities' => Ho_Prioridad_Emergencia::where('estado', 1)->orderBy('prioridad')->get(),
            'emergencyTypes' => Ho_Tipo_Emergencia::where('estado', 1)->get()
        ]);
    }

    public function saveProgress(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $this->validate($request, ['save_mode' => 'required|in:DRAFT,CONFIRM', 'patient' => 'nullable|array']);
            $flow = $service->saveProgress(Ho_Solicitud::findOrFail($solicitud), $request->all(), $request, $request->input('save_mode') === 'CONFIRM');
            $message = $request->input('save_mode') === 'CONFIRM' ? 'Triaje confirmado. Se conservaron los avances del episodio.' : 'Avances guardados. Puede continuar después o desde otro profesional autorizado.';
            $request->session()->flash('success', $message);
            if ($request->expectsJson()) return response()->json([
                'message' => $message,
                'version' => (int)$flow->version,
                'url' => route('hospital.emergency-flow.progress', array_filter([
                    'solicitud' => $solicitud,
                    'embedded' => $request->boolean('embedded') ? 1 : null,
                ], function ($value) { return $value !== null; }))
            ]);
            return redirect()->route('hospital.emergency-flow.progress', ['solicitud' => $solicitud])->with('success', $message);
        });
    }

    public function autosaveProgress(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $this->validate($request, [
                'version' => 'required|integer|min:0',
                'field' => 'required|string|max:80',
                'value' => 'nullable|string|max:2000'
            ]);
            $field = $request->input('field');
            if (!ClinicalEmergencyProgress::autosaveFieldAllowed($field)) {
                throw new \InvalidArgumentException('Campo no permitido para el guardado automatico.');
            }
            $value = $request->input('value');
            $data = ['version' => $request->input('version')];
            if (preg_match('/^patient\[([a-z0-9_]+)\]$/D', $field, $match)) {
                $data['patient'] = [$match[1] => $value];
            } else {
                $data[$field] = $value;
            }
            if ($value === null || $value === '') $data['clear_field'] = $field;
            $flow = $service->saveProgress(Ho_Solicitud::findOrFail($solicitud), $data, $request);
            return response()->json([
                'version' => (int)$flow->version,
                'field' => $field,
                'saved_at' => date('H:i:s'),
                'message' => 'Avance guardado'
            ]);
        });
    }

    public function criticalArrival(Request $request, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $service) {
            $solicitud = $service->registerCriticalArrival($request->all(), $request);
            return redirect()->route('hospital.emergencia')->with(
                'success',
                'Ingreso critico creado: ' . $solicitud->id_paciente . '. Pendiente de valoracion de triaje.'
            );
        });
    }

    public function start(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $this->validate($request, ['start_mode' => 'nullable|in:STANDARD,CRITICAL']);
            $source = Ho_Solicitud::findOrFail($solicitud);
            if ($request->input('start_mode') === 'CRITICAL') {
                $service->startCriticalAttention($source, $request);
                return redirect()->route('hospital.emergency-care.show', ['solicitud' => $source->id])
                    ->with('success', 'Atención crítica iniciada. Continúe documentando este episodio.');
            }
            $service->startAttention($source, $request);
            return redirect()->route('hospital.emergency-care.show', ['solicitud' => $source->id])
                ->with('success', 'Atención iniciada. Continúe documentando este episodio.');
        });
    }

    public function move(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $this->validate($request, ['location_id' => 'required|integer|min:1']);
            $service->move(
                Ho_Solicitud::findOrFail($solicitud),
                ClinicalEmergencyLocation::findOrFail($request->location_id),
                $request->reason,
                $request
            );
            return back()->with('success', 'Ubicacion del paciente actualizada.');
        });
    }

    public function disposition(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $flow = $service->decide(
                Ho_Solicitud::findOrFail($solicitud),
                $request->disposition,
                $request->decision_note,
                $request->target_sala_id,
                $request
            );
            if (
                $flow->status === ClinicalEmergencyFlowService::DECISION_RECORDED
                && in_array($flow->disposition, ['DISCHARGE', 'OUTPATIENT', 'TRANSFER'], true)
            ) {
                return redirect()->route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud])
                    ->with('success', 'Decision registrada. Pendiente de epicrisis y firma medica; el episodio sigue abierto.');
            }
            if ($flow->requested_transfer_id && in_array($flow->disposition, ['HOSPITALIZATION', 'ICU', 'NICU'], true)) {
                if ($request->filled('target_bed_id')) {
                    return redirect()->route('cuartos.asignar_paciente', [
                        'id' => (int) $request->target_bed_id,
                        'id_s' => $flow->requested_transfer_id,
                    ])->with('success', 'Destino registrado. Confirme la recepción para asignar la cama al paciente.');
                }
                return redirect()->route('hospital.gcuartos', ['traspaso' => $flow->requested_transfer_id])
                    ->with('success', 'Traslado solicitado. Seleccione una cama y confirme la recepción del paciente.');
            }
            $message = $flow->requested_transfer_id
                ? 'Traslado solicitado. El paciente permanece en Emergencia hasta su recepcion.'
                : 'Decision clinica registrada. El episodio permanece abierto.';
            return back()->with('success', $message);
        });
    }

    public function rerouteHospitalization(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $flow = $service->rerouteToHospitalization(
                Ho_Solicitud::findOrFail($solicitud),
                $request->decision_note,
                $request->target_sala_id,
                $request
            );
            if ($request->filled('target_bed_id')) {
                return redirect()->route('cuartos.asignar_paciente', [
                    'id' => (int) $request->target_bed_id,
                    'id_s' => $flow->requested_transfer_id,
                ])->with('success', 'Cambio a Hospitalización solicitado. Confirme la recepción para asignar la cama.');
            }
            return redirect()->route('hospital.gcuartos', ['traspaso' => $flow->requested_transfer_id])
                ->with('success', 'Cambio a Hospitalización solicitado. Seleccione una cama y confirme la recepción.');
        });
    }

    public function cancelTransfer(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $service->cancelTransfer(Ho_Solicitud::findOrFail($solicitud), $request->input('cancel_reason'), $request);
            return back()->with('success', 'Traslado cancelado por medico. El paciente permanece en Emergencia.');
        });
    }

    public function annulEpisode(Request $request, $solicitud, ClinicalEmergencyFlowService $service)
    {
        return $this->perform($request, function () use ($request, $solicitud, $service) {
            $service->annulOpenEpisode(Ho_Solicitud::findOrFail($solicitud), $request->input('annul_reason'), $request);
            return back()->with('success', 'Episodio de prueba anulado y retirado de la cola de Emergencia.');
        });
    }

    private function perform(Request $request, callable $callback)
    {
        try {
            return $callback();
        } catch (ClinicalAccessDeniedException $e) {
            abort(403, $e->getMessage());
        } catch (\DomainException $e) {
            return $this->failure($request, $e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return $this->failure($request, $e->getMessage(), 422);
        }
    }

    private function failure(Request $request, $message, $status)
    {
        if ($request->expectsJson()) return response()->json(['message' => $message, 'errors' => ['flow' => [$message]]], $status);
        return back()->withErrors(['flow' => $message])->withInput();
    }
}
