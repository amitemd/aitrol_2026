<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\ClinicalEmergencyFlow;
use Sis_medico\ClinicalEmergencyLocation;
use Sis_medico\ClinicalEmergencyLocationMovement;
use Sis_medico\ClinicalEmergencyTriageAssessment;
use Sis_medico\ClinicalEmergencyIntakeUpdate;
use Sis_medico\Ho_Prioridad_Emergencia;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Paciente;
use Sis_medico\User;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Hospital;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Illuminate\Support\Facades\Validator;

class ClinicalEmergencyFlowService
{
    const PENDING_TRIAGE = 'PENDING_TRIAGE';
    const WAITING_ATTENTION = 'WAITING_ATTENTION';
    const IN_ATTENTION = 'IN_ATTENTION';
    const UNDER_OBSERVATION = 'UNDER_OBSERVATION';
    const TRANSFER_PENDING = 'TRANSFER_PENDING';
    const DECISION_RECORDED = 'DECISION_RECORDED';

    public function context(Request $request, array $roles = ['SISTEMAS', 'DOCTORES', 'ENFERMERIA'])
    {
        // Flow policy uses legacy role codes; User authorization expects catalog names.
        $aliases = [1=>['SISTEMAS'], 3=>['DOCTORES','DOCTOR'], 6=>['ENFERMERIA','ENFERMERÍA'],
            'DOCTORES'=>['DOCTORES','DOCTOR'], 'ENFERMERIA'=>['ENFERMERIA','ENFERMERÍA']];
        $roles = array_values(array_unique(array_merge(...array_map(function ($role) use ($aliases) {
            return $aliases[$role] ?? [(string)$role];
        }, $roles))));
        $user = Auth::user();
        $isSystemUser = $user && method_exists($user, 'isSystemUser') && $user->isSystemUser();
        if (!$user || (int) $user->estado !== 1
            || (!$isSystemUser && !$user->hasAnyUserTypeName($roles))) {
            abort(403, 'El rol activo no puede realizar esta accion de Emergencia.');
        }
        $companyId = (string) $request->session()->get('id_empresa', '');
        $company = app(TenantContext::class)->availableFor($user)->first(function ($company) use ($companyId) {
            return (string) $company->id === $companyId;
        });
        if ($companyId === '' || !$company) abort(403, 'Seleccione una empresa autorizada.');
        $hospitalId = app(CurrentHospitalResolver::class)->id(['company_id' => $companyId]);
        if (!$hospitalId || !app(CurrentHospitalResolver::class)->activeHospitalId($hospitalId)) {
            throw new \InvalidArgumentException('Configure el hospital activo de la empresa.');
        }
        return ['company_id' => $companyId, 'hospital_id' => $hospitalId, 'actor_id' => $user->id];
    }

    public function assertAccess(Ho_Solicitud $solicitud, Request $request, $action = 'EMERGENCY_VIEW', array $roles = [1, 3, 6])
    {
        $context = $this->context($request, $roles);
        $encounter = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
        $this->assertEncounterContext($solicitud, $encounter, $context);
        app(ClinicalAccessControlService::class)->assertAllowed($encounter->id, $action,
            ['user' => Auth::user(), 'ip' => $request->ip()]);
        return $encounter;
    }

    /** Solo para el listado: contexto validado una vez y episodios cargados en lote. */
    public function assertBoardAccess(Ho_Solicitud $solicitud, Request $request, array $context)
    {
        $encounter = $solicitud->relationLoaded('encounter')
            && app(ClinicalEncounterService::class)->available() ? $solicitud->getRelation('encounter') : null;
        $this->assertEncounterContext($solicitud, $encounter, $context);
        app(ClinicalAccessControlService::class)->assertAllowedForEncounter($encounter, 'EMERGENCY_VIEW',
            ['user' => Auth::user(), 'ip' => $request->ip()]);
        return $encounter;
    }

    private function assertEncounterContext(Ho_Solicitud $solicitud, $encounter, array $context)
    {
        if (!$encounter || (string) $encounter->id_empresa !== $context['company_id']
            || (int) $encounter->id_hospital !== (int) $context['hospital_id']
            || (string) $encounter->id_paciente !== (string) $solicitud->id_paciente) {
            abort(403, 'El episodio no pertenece al contexto clinico activo.');
        }
    }

    public function registerCriticalArrival(array $data, Request $request)
    {
        Validator::make($data, [
            'arrival_mode' => 'required|in:WALK_IN,AMBULANCE,TRANSFER,POLICE',
            'sex' => 'nullable|in:1,2', 'arrival_note' => 'required|string|min:5|max:1000',
        ])->validate();
        $data = array_merge($data, ['id_paciente' => $this->provisionalPatientId(),
            'nombre1' => 'N/A', 'nombre2' => '', 'apellido1' => 'N/A', 'apellido2' => '', 'tipo_documento' => 3,
            'sexo' => $this->value($data, 'sex'), 'emergency_identity_pending'=>true]);
        return $this->registerArrival($data, $request, true);
    }

    public function registerMinimalArrival(array $data, Request $request)
    {
        Validator::make($data, ['critical_arrival'=>'nullable|boolean'] + ClinicalEmergencyProgress::clinicalRules())->validate();
        return $this->registerArrival($data, $request, !empty($data['critical_arrival']), true);
    }

    public function registerArrival(array $data, Request $request, $critical = false, $deferTriage = false)
    {
        $context = $this->context($request, ['SISTEMAS', 'DOCTORES', 'ENFERMERIA']);
        Validator::make($data, [
            'id_paciente' => 'required|string|max:13|regex:/^[A-Za-z0-9-]+$/',
            'nombre1' => 'required|string|max:60', 'apellido1' => 'required|string|max:60',
            'nombre2' => 'nullable|string|max:60', 'apellido2' => 'nullable|string|max:60',
            'sexo' => 'nullable|in:1,2',
        ])->validate();
        return DB::transaction(function () use ($data, $request, $critical, $context, $deferTriage) {
            $actor = $context['actor_id'];
            $audit = ['id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                'ip_creacion' => $request->ip(), 'ip_modificacion' => $request->ip()];
            $registration = app(EmergencyPatientRegistrationService::class);
            $patient = $registration->register($data, $request, $context);
            $patient = $registration->updateAdmissionIdentity($patient, $data, $request, $context);
            $patientId = $patient->id;
            $active = ClinicalEncounter::where('id_paciente', $patientId)
                ->where('id_empresa', $context['company_id'])->where('status', 'ACTIVE')
                ->lockForUpdate()->exists();
            if ($active) {
                throw new \DomainException('El paciente ya tiene un episodio activo en esta empresa. Continúe desde su ingreso vigente.');
            }
            $activeRequest = Ho_Solicitud::where('id_paciente', $patientId)->where('estado', 1)
                ->whereHas('encounter', function ($encounter) use ($context) {
                    $encounter->where('id_empresa', $context['company_id']);
                })->lockForUpdate()->exists();
            if ($activeRequest) {
                throw new \DomainException('Existe un ingreso activo. Regularice su episodio antes de crear otro.');
            }
            $encounters = app(ClinicalEncounterService::class);
            $encounter = $encounters->openOrReuse($patientId, ClinicalEncounterService::EMERGENCY, [
                'id_empresa' => $context['company_id'], 'id_hospital' => $context['hospital_id'],
                'actor_id' => $actor, 'ip' => $request->ip(), 'legacy_step' => 2,
            ]);
            if (!$encounter || (string) $encounter->id_empresa !== $context['company_id']) {
                throw new \DomainException('No se pudo registrar el episodio canonico en la empresa activa.');
            }
            $solicitud = Ho_Solicitud::create($audit + ['id_paciente' => $patientId,
                'id_seguro' => $patient->id_seguro ?: $this->defaultInsuranceId(),
                'encounter_id' => $encounter->id, 'fecha_ingreso' => date('Y-m-d H:i:s'),
                'estado_paso' => 2, 'estado' => 1]);
            $log = Ho_Log_Solicitud::create($audit + ['id_ho_solicitud' => $solicitud->id,
                'estado_paso' => 2, 'fecha_ingreso' => $solicitud->fecha_ingreso, 'encounter_id' => $encounter->id]);
            $encounters->link($encounter, ['ho_solicitud' => $solicitud->id, 'ho_log_solicitud' => $log->id]);
            $flow = $this->ensure($solicitud, $request);
            if ($critical) {
                $flow->critical_arrival = 1;
                $flow->arrival_mode = $data['arrival_mode'] ?? 'UNKNOWN';
                $flow->decision_note = $data['arrival_note'] ?? null;
                $location = $this->locationFor('RESUSCITATION', $flow->hospital_id);
                if ($location) $this->moveLocked($flow, $location, 'Ingreso critico.', $actor, $request->ip());
                $flow->save();
            } elseif (!$deferTriage) {
                $data['version'] = 0;
                $this->recordTriage($solicitud, $data, $request);
            }
            if ($deferTriage) {
                if (!$critical) { $flow->arrival_mode = 'UNKNOWN'; $flow->save(); }
                $this->saveProgress($solicitud, array_replace($data, ['version'=>0]), $request);
            }
            return $solicitud;
        }, 3);
    }

    public function ensure(Ho_Solicitud $solicitud, Request $request = null)
    {
        $request = $request ?: request();
        return DB::transaction(function () use ($solicitud, $request) {
            $solicitud = Ho_Solicitud::where('id', $solicitud->id)->lockForUpdate()->firstOrFail();
            $encounter = $this->assertAccess($solicitud, $request);
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->firstOrFail();
            $this->assertOpen($solicitud, $encounter);
            $flow = ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->lockForUpdate()->first();
            if ($flow) return $flow;
            $latest = $this->latestValidTriage($solicitud);
            $location = $this->locationFor($latest ? 'WAITING' : 'TRIAGE', $encounter->id_hospital);
            $flow = ClinicalEmergencyFlow::create([
                'solicitud_id' => $solicitud->id, 'hospital_id' => $encounter->id_hospital,
                'status' => $latest ? self::WAITING_ATTENTION : self::PENDING_TRIAGE,
                'current_location_id' => $location ? $location->id : null,
                'arrived_at' => $solicitud->fecha_ingreso ?: date('Y-m-d H:i:s'),
                'triaged_at' => $latest ? $latest->created_at : null,
                'version' => 0, 'created_by' => Auth::user()->id, 'updated_by' => Auth::user()->id,
                'ip_address' => $request->ip(),
            ]);
            if ($location) $this->logMovement($flow, null, $location->id, 'Ubicacion inicial.', Auth::user()->id, $request->ip());
            return $flow;
        }, 3);
    }

    public function recordTriage(Ho_Solicitud $solicitud, array $data, Request $request)
    {
        return DB::transaction(function () use ($solicitud, $data, $request) {
            Validator::make($data, $this->triageRules())->validate();
            $flow = $this->lockedFlow($solicitud, $request, 'TRIAGE', $this->value($data, 'version'));
            $priority = Ho_Prioridad_Emergencia::where('id', $data['priority_id'])->where('estado', 1)->first();
            if (!$priority || !ClinicalEmergencyFlowPolicy::validTriage($priority->prioridad, $data['clinical_reason'])) {
                throw new \InvalidArgumentException('Seleccione una prioridad clinica activa y un motivo valido.');
            }
            $latestLegacy = Ho_Triaje_Manchester::where('id_ho_solicitud', $solicitud->id)->orderBy('id', 'desc')->first();
            if (!empty($data['source_triage_id']) && (!$latestLegacy || (int) $latestLegacy->id !== (int) $data['source_triage_id'])) {
                throw new \DomainException('Existe una valoracion mas reciente. Recargue el triaje.');
            }
            $sequence = max((int) ClinicalEmergencyTriageAssessment::where('solicitud_id', $solicitud->id)->max('sequence'),
                $latestLegacy ? (int) $latestLegacy->num_revision : 0) + 1;
            $actor = Auth::user()->id;
            $now = date('Y-m-d H:i:s');

            $legacy = Ho_Triaje_Manchester::create(array(
                'id_ho_solicitud' => $solicitud->id,
                'num_revision' => $sequence,
                'prioridad' => $priority->id,
                'tipo_emergencia' => isset($data['emergency_type_id']) ? $data['emergency_type_id'] : null,
                'motivo_consulta' => $data['clinical_reason'],
                'presion_sistolica' => $this->value($data, 'systolic'),
                'presion_diastolica' => $this->value($data, 'diastolic'),
                'frec_cardiaca' => $this->value($data, 'heart_rate'),
                'frec_resp' => $this->value($data, 'respiratory_rate'),
                'temp' => $this->value($data, 'temperature'),
                'sat_oxigeno' => $this->value($data, 'oxygen_saturation'),
                'estado_conciencia' => $this->value($data, 'conscious'),
                'embarazo_puerperio' => $this->value($data, 'pregnancy'),
                'talla' => $this->value($data, 'height'), 'peso' => $this->value($data, 'weight'),
                'resp_ocular' => ClinicalEmergencyClassification::numericComponent($this->value($data, 'eye_response')),
                'resp_verbal' => ClinicalEmergencyClassification::numericComponent($this->value($data, 'verbal_response')),
                'resp_motora' => ClinicalEmergencyClassification::numericComponent($this->value($data, 'motor_response')),
                'reaccion_pupilar' => $this->value($data, 'pupillary_response'),
                'llenado_capilar' => $this->value($data, 'capillary_refill'),
                'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                'ip_creacion' => $request->ip(), 'ip_modificacion' => $request->ip(),
            ));

            ClinicalEmergencyTriageAssessment::create(array(
                'solicitud_id' => $solicitud->id, 'source_triage_id' => $legacy->id,
                'sequence' => $sequence, 'priority_id' => $priority->id,
                'priority_level' => $priority->prioridad, 'priority_name' => $priority->nombre,
                'priority_color' => ClinicalEmergencyClassification::levels()[(int) $priority->prioridad]['color'],
                'discriminator' => $this->value($data, 'discriminator'),
                'assessment_details' => ClinicalEmergencyClassification::details($data),
                'clinical_reason' => $data['clinical_reason'], 'systolic' => $this->value($data, 'systolic'),
                'diastolic' => $this->value($data, 'diastolic'), 'heart_rate' => $this->value($data, 'heart_rate'),
                'respiratory_rate' => $this->value($data, 'respiratory_rate'),
                'temperature' => $this->value($data, 'temperature'),
                'oxygen_saturation' => $this->value($data, 'oxygen_saturation'),
                'pain_score' => $this->value($data, 'pain_score'),
                'high_risk' => !empty($data['high_risk']) ? 1 : 0,
                'assessed_at' => $now, 'assessed_by' => $actor, 'ip_address' => $request->ip(),
            ));

            if (in_array($flow->status, [self::PENDING_TRIAGE, self::WAITING_ATTENTION], true)) {
                $location = $this->suggestedLocation((int) $priority->prioridad, !empty($data['isolation']), $flow->hospital_id);
                if ($location) $this->moveLocked($flow, $location, 'Ubicacion indicada en triaje.', $actor, $request->ip());
                $flow->status = self::WAITING_ATTENTION;
            }
            if (is_null($flow->triaged_at)) $flow->triaged_at = $now;
            $flow->last_reassessment_at = $now;
            $flow->version = (int) $flow->version + 1;
            $flow->updated_by = $actor; $flow->ip_address = $request->ip(); $flow->save();
            return $flow;
        }, 3);
    }

    public function saveProgress(Ho_Solicitud $solicitud, array $data, Request $request, $confirmTriage = false)
    {
        $clinical = Validator::make($data, ClinicalEmergencyProgress::clinicalRules())->validate();
        $identity = Validator::make($data['patient'] ?? [], ClinicalEmergencyProgress::patientRules())->validate();
        return DB::transaction(function () use ($solicitud, $data, $clinical, $identity, $request, $confirmTriage) {
            $flow = $this->lockedFlow($solicitud, $request, 'TRIAGE', $data['version'] ?? null);
            $latest = ClinicalEmergencyIntakeUpdate::where('solicitud_id', $solicitud->id)->orderByDesc('id')->first();
            $triageId = ClinicalEmergencyTriageAssessment::where('solicitud_id', $solicitud->id)->max('id');
            $clinical = ClinicalEmergencyProgress::merge(ClinicalEmergencyProgress::draft($latest, $triageId), $clinical);
            $clearField = $data['clear_field'] ?? null;
            if ($clearField !== null) {
                if (!ClinicalEmergencyProgress::autosaveFieldAllowed($clearField)) {
                    throw new \InvalidArgumentException('El campo no se puede limpiar automaticamente.');
                }
                if (preg_match('/^patient\[([a-z0-9_]+)\]$/D', $clearField, $match)) {
                    if (!ClinicalEmergencyProgress::clearablePatientField($match[1])) {
                        throw new \InvalidArgumentException('Ese dato de identidad debe corregirse con un valor valido.');
                    }
                    $identity[$match[1]] = null;
                } else {
                    unset($clinical[$clearField]);
                }
            }
            if ($confirmTriage) {
                // This new workflow always requires the documented professional confirmation.
                $clinical['classification_method'] = 'CLINICIAN_MANCHESTER_V1';
                $flow = $this->recordTriage($solicitud, $clinical + [
                    'version'=>$flow->version, 'classification_confirmed'=>$data['classification_confirmed'] ?? null,
                ], $request);
            } else {
                $this->saveAction($flow, $request);
            }
            $patient = Paciente::where('id', $solicitud->id_paciente)->lockForUpdate()->firstOrFail();
            $changes = [];
            foreach ($identity as $field => $value) {
                if ($field === 'sexo' && (string) $value === '0') $value = null;
                if (($value === null || $value === '') && $clearField !== 'patient['.$field.']' && $field !== 'sexo') continue;
                if ($value !== null && in_array($field, ['nombre1','nombre2','apellido1','apellido2'], true)) $value = mb_strtoupper(trim($value), 'UTF-8');
                if ((string)$patient->$field === (string)$value) continue;
                $changes[$field] = ['before'=>$patient->$field, 'after'=>$value];
                $patient->$field = $value;
            }
            if ($changes) {
                if (isset($changes['fecha_nacimiento'])) $patient->menoredad = strtotime($patient->fecha_nacimiento.' +18 years') > time() ? 1 : 0;
                $patient->id_usuariomod = Auth::id(); $patient->ip_modificacion = $request->ip();
                $patient->save();
            }
            ClinicalEmergencyIntakeUpdate::create(['solicitud_id'=>$solicitud->id,
                'triage_assessment_id'=>ClinicalEmergencyTriageAssessment::where('solicitud_id', $solicitud->id)->max('id'),
                'stage'=>$confirmTriage ? 'CONFIRMED' : 'DRAFT', 'clinical_data'=>$clinical,
                'patient_changes'=>$changes, 'recorded_by'=>Auth::id(), 'ip_address'=>$request->ip()]);
            return $flow;
        }, 3);
    }

    public function startAttention(Ho_Solicitud $solicitud, Request $request)
    {
        return DB::transaction(function () use ($solicitud, $request) {
            $flow = $this->lockedFlow($solicitud, $request, 'START', $request->input('version'));
            if ($flow->attention_started_at) throw new \DomainException('La atencion ya fue iniciada.');
            $flow->status = self::IN_ATTENTION;
            $flow->attention_started_at = date('Y-m-d H:i:s');
            $flow->assigned_to = Auth::user()->id;
            $this->saveAction($flow, $request);
            return $flow;
        }, 3);
    }

    public function startCriticalAttention(Ho_Solicitud $solicitud, Request $request)
    {
        $reason = $request->input('critical_reason');
        if (is_string($reason)) $reason = trim($reason);
        Validator::make(['critical_reason' => $reason], ['critical_reason' => 'required|string|min:10|max:1000'])->validate();
        return DB::transaction(function () use ($solicitud, $request, $reason) {
            $flow = $this->lockedFlow($solicitud, $request, 'START_CRITICAL', $request->input('version'));
            if ($flow->attention_started_at || $flow->critical_started_at) {
                throw new \DomainException('La atencion critica ya fue iniciada.');
            }
            $flow->status = self::IN_ATTENTION;
            $flow->attention_started_at = date('Y-m-d H:i:s');
            $flow->assigned_to = Auth::user()->id;
            $flow->critical_reason = $reason;
            $flow->critical_started_by = Auth::user()->id;
            $flow->critical_started_at = $flow->attention_started_at;
            $this->saveAction($flow, $request);
            return $flow;
        }, 3);
    }

    public function move(Ho_Solicitud $solicitud, ClinicalEmergencyLocation $location, $reason, Request $request)
    {
        Validator::make(['reason' => $reason], ['reason' => 'required|string|min:4|max:191'])->validate();
        return DB::transaction(function () use ($solicitud, $location, $reason, $request) {
            $flow = $this->lockedFlow($solicitud, $request, 'MOVE', $request->input('version'));
            $location = ClinicalEmergencyLocation::where('id', $location->id)->lockForUpdate()->firstOrFail();
            if (!$location->is_active || ($location->hospital_id !== null && (int) $location->hospital_id !== (int) $flow->hospital_id)) {
                throw new \InvalidArgumentException('La ubicacion no esta activa en este hospital.');
            }
            $this->moveLocked($flow, $location, $reason, Auth::user()->id, $request->ip());
            $this->saveAction($flow, $request);
            return $flow;
        }, 3);
    }

    public function decide(Ho_Solicitud $solicitud, $disposition, $note, $targetSalaId, Request $request)
    {
        return $this->recordDisposition($solicitud, $disposition, $note, $targetSalaId, $request, 'DECIDE', true);
    }

    public function rerouteToHospitalization(Ho_Solicitud $solicitud, $note, $targetSalaId, Request $request)
    {
        return $this->recordDisposition(
            $solicitud,
            'HOSPITALIZATION',
            $note,
            $targetSalaId,
            $request,
            'REROUTE_HOSPITALIZATION',
            false
        );
    }

    private function recordDisposition(Ho_Solicitud $solicitud, $disposition, $note, $targetSalaId,
        Request $request, $action, $requiresAttention)
    {
        Validator::make(['decision_note' => $note, 'target_sala_id' => $targetSalaId,
            'target_bed_id' => $request->input('target_bed_id')],
            ['decision_note' => 'required|string|min:10|max:2000', 'target_sala_id' => 'nullable|integer|min:1',
                'target_bed_id' => 'nullable|integer|min:1'])->validate();
        if (!is_string($disposition) || !array_key_exists($disposition, $this->dispositions())) {
            throw new \InvalidArgumentException('Seleccione un destino clinico valido.');
        }
        return DB::transaction(function () use ($solicitud, $disposition, $note, $targetSalaId, $request, $action, $requiresAttention) {
            $flow = $this->lockedFlow($solicitud, $request, $action, $request->input('version'));
            if ($requiresAttention && !$flow->attention_started_at) {
                throw new \DomainException('Inicie la atencion medica antes de decidir.');
            }
            if (Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('estado', 1)->lockForUpdate()->exists()) {
                throw new \DomainException('Ya existe un traslado pendiente. Debe resolverse antes de otra decision.');
            }
            $steps = ClinicalEmergencyFlowPolicy::transferSteps();
            $isTransfer = isset($steps[$disposition]);
            $bedId = $request->input('target_bed_id');
            if ($bedId && (!$isTransfer || $disposition === 'SURGERY')) {
                throw new \InvalidArgumentException('Este destino no requiere una cama de hospitalización.');
            }
            if ($isTransfer) {
                if (!$targetSalaId) throw new \InvalidArgumentException('Seleccione la sala receptora configurada.');
                $companyId = (string) $request->session()->get('id_empresa');
                $sala = app(ClinicalHospitalDestinationService::class)->assertSala($targetSalaId, $steps[$disposition], $companyId);
                if ((int) $sala->id_hospital !== (int) $flow->hospital_id) {
                    throw new \InvalidArgumentException('La sala receptora pertenece a otro hospital.');
                }
                $transferNote = $note;
                if ($bedId) {
                    $bed = app(ClinicalHospitalDestinationService::class)->assertAvailableBed($bedId, $sala->id);
                    $transferNote .= "\nUbicación solicitada: ".$sala->nombre_sala
                        .' / Habitación '.$bed->habitacion->codigo.' / Cama '.$bed->codigo.'.';
                }
                $transfer = Ho_Traspaso_Sala008::create([
                    'id_solicitud' => $solicitud->id, 'id_sala' => $sala->id, 'id_paciente' => $solicitud->id_paciente,
                    'paso' => $steps[$disposition], 'seccion' => 4, 'estado' => 1,
                    'fecha' => date('Y-m-d H:i:s'), 'observaciones' => $transferNote,
                    'id_doctor' => Auth::user()->id, 'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id, 'ip_creacion' => $request->ip(), 'ip_modificacion' => $request->ip(),
                ]);
                $flow->requested_transfer_id = $transfer->id;
            } elseif ($targetSalaId) {
                throw new \InvalidArgumentException('Esta decision no requiere una sala de traslado interno.');
            }
            // Reception owns canonical reconciliation and the physical area/bed mutation.
            $flow->disposition = $disposition;
            $flow->decision_note = $note;
            $flow->decision_at = date('Y-m-d H:i:s');
            $flow->status = $disposition === 'OBSERVATION' ? self::UNDER_OBSERVATION
                : ($isTransfer ? self::TRANSFER_PENDING : self::DECISION_RECORDED);
            $flow->bed_request_status = $isTransfer && $disposition !== 'SURGERY' ? 'REQUESTED' : null;
            if ($disposition === 'OBSERVATION') {
                $location = $this->locationFor('OBSERVATION', $flow->hospital_id);
                if ($location) $this->moveLocked($flow, $location, 'Observacion indicada por medico.', Auth::user()->id, $request->ip());
            }
            $this->saveAction($flow, $request);
            return $flow;
        }, 3);
    }

    public function cancelTransfer(Ho_Solicitud $solicitud, $reason, Request $request)
    {
        if (is_string($reason)) $reason = trim($reason);
        Validator::make(['cancel_reason' => $reason], ['cancel_reason' => 'required|string|min:10|max:1000'])->validate();
        return DB::transaction(function () use ($solicitud, $reason, $request) {
            $flow = $this->lockedFlow($solicitud, $request, 'CANCEL_TRANSFER', $request->input('version'));
            $transfer = Ho_Traspaso_Sala008::where('id', $flow->requested_transfer_id)
                ->where('id_solicitud', $solicitud->id)->where('estado', 1)->lockForUpdate()->first();
            if (!$transfer || !$flow->attention_started_at) {
                throw new \DomainException('El traslado ya no esta pendiente o requiere regularizacion del episodio.');
            }
            $transfer->estado = 3;
            $transfer->id_usuariomod = Auth::user()->id;
            $transfer->ip_modificacion = $request->ip();
            $transfer->save();
            $encounter = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
            ClinicalEncounterEvent::create([
                'encounter_id' => $encounter->id, 'event_type' => 'EMERGENCY_TRANSFER_CANCELLED',
                'from_value' => self::TRANSFER_PENDING, 'to_value' => self::IN_ATTENTION,
                'reason' => 'Traslado #' . $transfer->id . ': ' . $reason,
                'actor_id' => Auth::user()->id, 'ip_address' => $request->ip(),
            ]);
            $flow->status = self::IN_ATTENTION;
            $flow->requested_transfer_id = null;
            $flow->bed_request_status = null;
            $flow->disposition = null;
            $flow->decision_note = null;
            $flow->decision_at = null;
            $this->saveAction($flow, $request);
            return $flow;
        }, 3);
    }

    public function annulOpenEpisode(Ho_Solicitud $solicitud, $reason, Request $request)
    {
        $reason = is_string($reason) ? trim($reason) : '';
        Validator::make(['annul_reason' => $reason], [
            'annul_reason' => 'required|string|min:10|max:1000',
        ])->validate();

        $user = Auth::user();
        if (!$user || !method_exists($user, 'isSystemUser') || !$user->isSystemUser()) {
            throw new ClinicalAccessDeniedException('Solo el rol Sistemas puede anular un episodio de prueba.');
        }

        return DB::transaction(function () use ($solicitud, $reason, $request, $user) {
            $context = $this->context($request, ['SISTEMAS']);
            $solicitud = Ho_Solicitud::where('id', $solicitud->id)->lockForUpdate()->firstOrFail();
            $encounter = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
            if (!$encounter || (string) $encounter->id_empresa !== (string) $context['company_id']
                || (int) $encounter->id_hospital !== (int) $context['hospital_id']) {
                throw new ClinicalAccessDeniedException('El episodio no pertenece al contexto clínico activo.');
            }
            if ((int) $solicitud->estado !== 1 || $solicitud->fecha_salida
                || !in_array((int) $solicitud->estado_paso, [1, 2], true)) {
                throw new \DomainException('Solo se pueden anular episodios abiertos de Emergencia.');
            }

            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->firstOrFail();
            if ($encounter->status !== ClinicalEncounterService::ACTIVE || $encounter->ended_at
                || !in_array($encounter->clinical_state, [
                    ClinicalStateTransitionService::EMERGENCY_ACTIVE,
                    ClinicalStateTransitionService::TRANSFER_PENDING,
                ], true)) {
                throw new \DomainException('El episodio ya avanzó a otro estado clínico y no puede anularse desde Emergencia.');
            }

            $flow = ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->lockForUpdate()->first();
            $pendingTransfers = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)
                ->where('estado', 1)->lockForUpdate()->get();
            foreach ($pendingTransfers as $transfer) {
                $transfer->estado = 3;
                $transfer->id_usuariomod = $user->id;
                $transfer->ip_modificacion = $request->ip();
                $transfer->save();
            }

            $now = date('Y-m-d H:i:s');
            $eventReason = 'Anulación administrativa de episodio de prueba: ' . $reason;
            app(ClinicalClosureGuard::class)->run(function () use ($encounter, $solicitud, $flow, $eventReason, $context, $now, $user, $request) {
                $states = app(ClinicalStateTransitionService::class);
                if ($encounter->clinical_state === ClinicalStateTransitionService::TRANSFER_PENDING) {
                    $states->transition($encounter, ClinicalStateTransitionService::EMERGENCY_ACTIVE, $eventReason, $context);
                }
                $states->transition($encounter, ClinicalStateTransitionService::CLOSURE_PENDING, $eventReason, $context);
                $states->transition($encounter, ClinicalStateTransitionService::CLOSED_DISCHARGED, $eventReason, $context);

                $encounter->status = ClinicalStateTransitionService::CLOSED_DISCHARGED;
                $encounter->ended_at = $now;
                $encounter->close_reason = 'CANCEL';
                $encounter->id_usuariomod = $user->id;
                $encounter->ip_modificacion = $request->ip();
                $encounter->save();

                $solicitud->fecha_salida = $now;
                $solicitud->estado = 0;
                $solicitud->id_usuariomod = $user->id;
                $solicitud->ip_modificacion = $request->ip();
                $solicitud->save();

                if ($flow) {
                    $flow->status = 'CANCELLED';
                    $flow->closed_at = $now;
                    $flow->requested_transfer_id = null;
                    $flow->bed_request_status = null;
                    $flow->disposition = 'CANCELLED';
                    $flow->decision_note = $eventReason;
                    $flow->updated_by = $user->id;
                    $flow->ip_address = $request->ip();
                    $flow->version = (int) $flow->version + 1;
                    $flow->save();
                }

                ClinicalEncounterEvent::create([
                    'encounter_id' => $encounter->id,
                    'event_type' => 'EMERGENCY_EPISODE_ANNULLED',
                    'from_value' => 'TRANSFER_PENDING',
                    'to_value' => ClinicalStateTransitionService::CLOSED_DISCHARGED,
                    'reason' => $eventReason,
                    'actor_id' => $user->id,
                    'ip_address' => $request->ip(),
                ]);
            });

            return $solicitud;
        }, 3);
    }

    public function dispositions()
    {
        return ['OBSERVATION' => 'Observacion de emergencia', 'HOSPITALIZATION' => 'Hospitalizacion',
            'SURGERY' => 'Cirugia', 'ICU' => 'UCI', 'NICU' => 'UCI neonatal',
            'TRANSFER' => 'Transferencia a otro establecimiento',
            'DISCHARGE' => 'Alta a domicilio', 'OUTPATIENT' => 'Consulta externa'];
    }

    public function triageRules()
    {
        return ClinicalEmergencyClassification::rules() + [
            'priority_id' => 'required|integer', 'clinical_reason' => 'required|string|min:5|max:1000',
            'discriminator' => 'nullable|string|max:191',
            'emergency_type_id' => 'nullable|integer|exists:ho_tipo_emergencia,id,estado,1',
            'systolic' => 'nullable|numeric|between:20,300', 'diastolic' => 'nullable|numeric|between:10,200',
            'heart_rate' => 'nullable|numeric|between:1,300', 'respiratory_rate' => 'nullable|numeric|between:1,100',
            'temperature' => 'nullable|numeric|between:25,45', 'oxygen_saturation' => 'nullable|numeric|between:0,100',
            'pain_score' => 'nullable|integer|between:0,10', 'high_risk' => 'nullable|boolean', 'isolation' => 'nullable|boolean',
            'conscious' => 'nullable|in:0,1,2', 'pregnancy' => 'nullable|in:0,1,2',
            'height' => 'nullable|numeric|min:0', 'weight' => 'nullable|numeric|min:0',
            'eye_response' => 'nullable|integer|between:1,4', 'verbal_response' => 'nullable|integer|between:1,5',
            'motor_response' => 'nullable|integer|between:1,6', 'pupillary_response' => 'nullable|string|max:191',
            'capillary_refill' => 'nullable|numeric|min:0',
        ];
    }

    private function assertOpen(Ho_Solicitud $solicitud, ClinicalEncounter $encounter)
    {
        if ((int) $solicitud->estado !== 1 || !in_array((int) $solicitud->estado_paso, [1, 2], true)
            || $solicitud->fecha_salida || $encounter->status !== 'ACTIVE' || $encounter->ended_at
            || !in_array($encounter->clinical_state, ['EMERGENCY_ACTIVE', 'TRANSFER_PENDING'], true)) {
            throw new \DomainException('El episodio no esta abierto en Emergencia.');
        }
    }

    private function lockedFlow(Ho_Solicitud $solicitud, Request $request, $action, $version)
    {
        $solicitud = Ho_Solicitud::where('id', $solicitud->id)->lockForUpdate()->firstOrFail();
        $roles = ClinicalEmergencyFlowPolicy::rolesFor($action);
        $encounter = $this->assertAccess($solicitud, $request, 'EMERGENCY_' . $action, $roles);
        $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->firstOrFail();
        $this->assertOpen($solicitud, $encounter);
        $flow = ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->lockForUpdate()->first();
        if (!$flow) $flow = $this->ensure($solicitud, $request);
        if ((int) $flow->hospital_id !== (int) $encounter->id_hospital) abort(403, 'El flujo pertenece a otro hospital.');
        ClinicalEmergencyFlowPolicy::assertVersion($version, $flow->version);
        ClinicalEmergencyFlowPolicy::assertAction($action, $flow->status, $encounter->clinical_state,
            (bool) $flow->closed_at, (bool) $this->latestValidTriage($solicitud), (bool) $flow->critical_arrival);
        if (!in_array($action, ['TRIAGE', 'CANCEL_TRANSFER'], true)
            && Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('estado', 1)->exists()) {
            throw new \DomainException('Existe un traslado pendiente para este episodio.');
        }
        return $flow;
    }

    private function latestValidTriage(Ho_Solicitud $solicitud)
    {
        $triage = ClinicalEmergencyTriageAssessment::where('solicitud_id', $solicitud->id)->orderBy('sequence', 'desc')->first();
        if ($triage) return ClinicalEmergencyFlowPolicy::validTriage($triage->priority_level, $triage->clinical_reason) ? $triage : null;
        $legacy = Ho_Triaje_Manchester::where('id_ho_solicitud', $solicitud->id)->orderBy('id', 'desc')->first();
        if (!$legacy) return null;
        $priority = Ho_Prioridad_Emergencia::find($legacy->prioridad);
        return $priority && ClinicalEmergencyFlowPolicy::validTriage($priority->prioridad, $legacy->motivo_consulta) ? $legacy : null;
    }

    private function saveAction(ClinicalEmergencyFlow $flow, Request $request)
    {
        $flow->version = (int) $flow->version + 1;
        $flow->updated_by = Auth::user()->id;
        $flow->ip_address = $request->ip();
        $flow->save();
    }

    private function suggestedLocation($level, $isolation, $hospitalId = null)
    {
        $code = $isolation ? 'ISOLATION' : ($level === 1 ? 'RESUSCITATION' : ($level === 2 ? 'CRITICAL_CARE'
            : ($level === 3 ? 'EMERGENCY_BOX' : 'WAITING')));
        return $this->locationFor($code, $hospitalId);
    }

    private function locationFor($code, $hospitalId)
    {
        return ClinicalEmergencyLocation::where('code', $code)->where('is_active', 1)
            ->where(function ($query) use ($hospitalId) {
                $query->whereNull('hospital_id');
                if ($hospitalId) $query->orWhere('hospital_id', $hospitalId);
            })->orderByRaw('hospital_id IS NULL')->first();
    }

    private function provisionalPatientId()
    {
        do { $id = 'EMG' . date('ymd') . strtoupper(substr(uniqid(), -4)); }
        while (Paciente::where('id', $id)->exists() || User::where('id', $id)->exists());
        return substr($id, 0, 13);
    }

    private function defaultInsuranceId()
    {
        $id = \Sis_medico\Seguro::query()->where('inactivo', 1)->orderBy('id')->value('id');
        if (!$id) throw new \InvalidArgumentException('Configure un seguro activo antes del ingreso.');
        return $id;
    }

    private function moveLocked(ClinicalEmergencyFlow $flow, ClinicalEmergencyLocation $location, $reason, $actor, $ip)
    {
        $from = $flow->current_location_id;
        if ((int) $from === (int) $location->id) return;
        $flow->current_location_id = $location->id; $flow->updated_by = $actor; $flow->ip_address = $ip; $flow->save();
        $this->logMovement($flow, $from, $location->id, $reason, $actor, $ip);
    }

    private function logMovement($flow, $from, $to, $reason, $actor, $ip)
    {
        ClinicalEmergencyLocationMovement::create(array('solicitud_id' => $flow->solicitud_id,
            'from_location_id' => $from, 'to_location_id' => $to, 'reason' => $reason,
            'moved_at' => date('Y-m-d H:i:s'), 'moved_by' => $actor, 'ip_address' => $ip));
    }

    private function value(array $data, $key) { return isset($data[$key]) && $data[$key] !== '' ? $data[$key] : null; }
}
