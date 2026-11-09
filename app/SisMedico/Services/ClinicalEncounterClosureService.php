<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterClosure;
use Sis_medico\ClinicalEncounterClosureOrder;
use Sis_medico\ClinicalEmergencyMedicationException;
use Sis_medico\ClinicalMedicationOrder;
use Sis_medico\ClinicalOrder;
use Sis_medico\Examen_Orden;
use Sis_medico\Exceptions\ClinicalClosureBlockedException;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Hc_Epicrisis_Final_Cie10;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Orden;

class ClinicalEncounterClosureService
{
    const DISCHARGE = 'DISCHARGE';
    const DECEASED = 'DECEASED';
    const NONE = 'NONE';
    const CANCEL = 'CANCEL';
    const FOLLOW_UP = 'FOLLOW_UP';
    const SIGNED = 'SIGNED';

    public function available()
    {
        return Schema::hasTable('clinical_encounter_closures')
            && Schema::hasTable('clinical_encounter_closure_orders')
            && Schema::hasTable('clinical_encounters');
    }

    public function preview($solicitudId, $actorId = null)
    {
        $orders = app(ClinicalTransferReconciliationService::class)->previewActiveOrders($solicitudId);
        $solicitud = Ho_Solicitud::find($solicitudId);
        if ($solicitud && $solicitud->encounter_id && Schema::hasTable('clinical_medication_orders')) {
            foreach (ClinicalMedicationOrder::where('encounter_id', $solicitud->encounter_id)
                ->whereIn('status', array('ACTIVE', 'REGULARIZATION_PENDING'))->get() as $medicationOrder) {
                $orders[] = array(
                    'type' => 'MEDICATION',
                    'id' => $medicationOrder->id,
                    'status' => $medicationOrder->status,
                    'label' => $medicationOrder->medication_name,
                );
            }
        }
        $epicrisis = Hc_Epicrisis_Final::where('id_ho_solicitud', $solicitudId)->first();
        $diagnosisReady = app(ClinicalDischargeDiagnosisService::class)
            ->hasDefinitiveDischarge($solicitud, $epicrisis);
        $actorId = $actorId ?: (Auth::check() ? Auth::user()->id : null);
        $closure = $this->available() ? ClinicalEncounterClosure::where('solicitud_id', $solicitudId)->first() : null;

        return array(
            'available' => $this->available(),
            'hospital' => $solicitud && $this->isHospital($solicitud),
            'medical_discharge_signed' => $closure && $closure->status === self::SIGNED,
            'closure_id' => $closure ? $closure->id : null,
            'signed_at' => $closure ? $closure->signed_at : null,
            'signed_by' => $closure ? $closure->signed_by : null,
            'physical_exit_at' => $closure ? $closure->physical_exit_at : null,
            'encounter_id' => $solicitud ? $solicitud->encounter_id : null,
            'epicrisis_ready' => $epicrisis && $this->filled($epicrisis->cuadro_clinico)
                && $this->filled($epicrisis->ep_resumen_evolucion)
                && $this->filled($epicrisis->hallazgo)
                && $this->filled($epicrisis->resumen)
                && $this->filled($epicrisis->conclusion),
            'diagnosis_ready' => $diagnosisReady,
            'signature_ready' => $actorId && Firma_Usuario::where('id_usuario', $actorId)->exists(),
            'active_orders' => $orders,
            'active_orders_count' => count($orders),
            'pending_emergency_medication_exceptions' => $solicitud && Schema::hasTable('clinical_emergency_medication_exceptions')
                ? ClinicalEmergencyMedicationException::where('encounter_id', $solicitud->encounter_id)->where('status', 'PENDING')->count()
                : 0,
            'already_closed' => $this->available() && ClinicalEncounterClosure::where('solicitud_id', $solicitudId)
                ->where('status', 'COMPLETED')->exists(),
            'death_workflow_available' => app(ClinicalDeathService::class)->available(),
        );
    }

    public function close($solicitudId, array $data, array $context = array())
    {
        if (!$this->available()) throw new ClinicalClosureBlockedException(array('La migración de cierre clínico aún no está aplicada.'));

        return DB::transaction(function () use ($solicitudId, $data, $context) {
            return app(ClinicalClosureGuard::class)->run(function () use ($solicitudId, $data, $context) {
            $solicitud = Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->first();
            if (!$solicitud) throw new ClinicalClosureBlockedException(array('No se encontró la solicitud clínica.'));

            $encounter = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
            if (!$encounter) throw new ClinicalClosureBlockedException(array('La solicitud no está vinculada a un episodio canónico.'));
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->first();
            $hospital = $this->isHospital($solicitud, $encounter);
            if ($hospital && !in_array((int) $solicitud->estado_paso, array(3, 6, 7), true)) {
                throw new ClinicalClosureBlockedException(array(
                    'El alta hospitalaria solo puede iniciarse desde Hospitalizacion, UCI o UCIN.'
                ));
            }
            try {
                if ($hospital) {
                    $this->assertHospitalActor($encounter, array('SISTEMAS', 'DOCTORES', 'DOCTOR'), $context);
                } else app(ClinicalAccessControlService::class)->assertAllowed($encounter->id, 'encounter.close', array(
                    'user' => \Sis_medico\User::find($this->actorId($context)),
                    'ip' => $this->ip($context),
                ));
            } catch (ClinicalAccessDeniedException $exception) {
                throw new ClinicalClosureBlockedException(array($exception->getMessage()));
            }

            $existing = ClinicalEncounterClosure::where('encounter_id', $encounter->id)->first();
            if ($existing && $existing->status === 'COMPLETED') {
                if (!$hospital) $this->closeHospitalization($solicitud, $context);
                return $existing;
            }
            if ($hospital && $existing && $existing->status === self::SIGNED) {
                if ((string) $existing->solicitud_id !== (string) $solicitud->id) {
                    throw new ClinicalClosureBlockedException(array('La firma pertenece a otra solicitud del episodio.'));
                }
                return $existing;
            }
            if ($hospital) {
                $this->requirePhysicalExitSchema();
                if ($encounter->status !== ClinicalEncounterService::ACTIVE || $encounter->ended_at
                    || (string) $solicitud->estado === '0' || $solicitud->fecha_salida) {
                    throw new ClinicalClosureBlockedException(array('El episodio ya no esta abierto.'));
                }
                if (!app(ClinicalRecordService::class)->available() || !app(ClinicalStateTransitionService::class)->available()) {
                    throw new ClinicalClosureBlockedException(array('El registro o estado canonico no esta disponible.'));
                }
            }

            $epicrisis = Hc_Epicrisis_Final::where('id_ho_solicitud', $solicitud->id)->lockForUpdate()->first();
            if ($epicrisis) {
                app(ClinicalDischargeDiagnosisService::class)->syncToEpicrisis($solicitud, $epicrisis);
            }
            $preview = $this->preview($solicitud->id, $this->actorId($context));
            $context['hospital_closure'] = $hospital;
            $blockers = $this->blockers($epicrisis, $preview, $data, $context);
            if (count($blockers)) throw new ClinicalClosureBlockedException($blockers);

            $closureType = $data['closure_type'];
            // Voluntary departure is stored as DISCHARGE, not a new canonical closure type.
            if ($hospital && !($closureType === self::DISCHARGE && $this->isVoluntaryDeparture($epicrisis))) {
                try {
                    app(HospitalDocumentService::class)->assertReadyForDischarge($solicitud, $closureType);
                } catch (\InvalidArgumentException $exception) {
                    throw new ClinicalClosureBlockedException(array($exception->getMessage()));
                } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception) {
                    if ($exception->getStatusCode() !== 403) throw $exception;
                    throw new ClinicalClosureBlockedException(array($exception->getMessage()));
                }
            }
            $decision = $preview['active_orders_count'] ? $data['pending_orders_decision'] : self::NONE;
            $now = date('Y-m-d H:i:s');
            $checklist = array(
                'epicrisis' => true,
                'diagnostico_egreso_definitivo' => true,
                'conciliacion_medicamentos' => true,
                'indicaciones' => $closureType === self::DECEASED ? 'NO_APLICA' : true,
                'firma' => true,
                'ordenes_pendientes' => $decision,
            );

            $closure = $existing ?: new ClinicalEncounterClosure();
            $closure->fill(array(
                'encounter_id' => $encounter->id,
                'solicitud_id' => $solicitud->id,
                'epicrisis_id' => $epicrisis->id,
                'closure_type' => $closureType,
                'medication_reconciliation' => trim($data['medication_reconciliation']),
                'pending_orders_decision' => $decision,
                'pending_orders_reason' => isset($data['pending_orders_reason']) ? trim($data['pending_orders_reason']) : null,
                'follow_up_responsible_id' => isset($data['follow_up_responsible_id']) ? $data['follow_up_responsible_id'] : null,
                'checklist_snapshot' => json_encode($checklist),
                'status' => 'PENDING',
                'signed_by' => $this->actorId($context),
                'signed_at' => $now,
                'ip_address' => $this->ip($context),
            ));
            $closure->save();

            if ($closureType === self::DECEASED) {
                app(ClinicalDeathService::class)->complete($encounter, $closure->id, $data, $context);
            }

            foreach ($preview['active_orders'] as $order) {
                ClinicalEncounterClosureOrder::firstOrCreate(array(
                    'closure_id' => $closure->id,
                    'order_type' => $order['type'],
                    'order_id' => (string) $order['id'],
                ), array(
                    'previous_status' => $order['status'],
                    'disposition' => $decision,
                    'reason' => trim($data['pending_orders_reason']),
                    'responsible_id' => $decision === self::FOLLOW_UP ? $data['follow_up_responsible_id'] : $this->actorId($context),
                ));
                if ($decision === self::CANCEL) $this->cancelPendingOrder($order, $context);
            }

            $states = app(ClinicalStateTransitionService::class);
            $states->transition($encounter, ClinicalStateTransitionService::CLOSURE_PENDING,
                'Checklist de cierre validado y firmado.', $context);
            if ($hospital && $closureType === self::DISCHARGE) {
                $epicrisis->encounter_id = $encounter->id;
                $epicrisis->save();
                $records = app(ClinicalRecordService::class);
                $records->registerEpicrisis($epicrisis, $context);
                $document = $records->signDocument('hc_epicrisis_final', $epicrisis->id,
                    $this->actorId($context), array_merge($context, array('closure_type' => $closureType)));
                if (!$document || !$document->signed_at || (string) $document->signed_by !== (string) $closure->signed_by) {
                    throw new ClinicalClosureBlockedException(array('No se pudo firmar la epicrisis canonica.'));
                }
                $closure->status = self::SIGNED;
                $closure->save();
                $this->hospitalEvent($encounter, 'MEDICAL_DISCHARGE_SIGNED', $closure, $context);
                return $closure;
            }
            $closedState = $closureType === self::DECEASED
                ? ClinicalStateTransitionService::CLOSED_DECEASED
                : ClinicalStateTransitionService::CLOSED_DISCHARGED;
            $states->transition($encounter, $closedState, 'Episodio cerrado mediante transacción clínica única.', $context);

            $encounter->status = $closedState;
            $encounter->ended_at = $now;
            $encounter->close_reason = $closureType;
            $encounter->id_usuariomod = $this->actorId($context);
            $encounter->ip_modificacion = $this->ip($context);
            $encounter->save();

            $solicitud->fecha_salida = $now;
            $solicitud->estado = 0;
            $solicitud->id_usuariomod = $this->actorId($context);
            $solicitud->ip_modificacion = $this->ip($context);
            $solicitud->save();
            Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->whereNull('fecha_salida')
                ->update(array('fecha_salida' => $now, 'id_usuariomod' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));

            if ($hospital) {
                $this->closeHospitalStay($solicitud, $context);
                $this->closeEmergencyFlow($solicitud, $closureType, $now, $context);
            } else {
                $this->closeHospitalization($solicitud, $context);
            }
            if (Schema::hasColumn('hc_epicrisis_final', 'encounter_id')) {
                $epicrisis->encounter_id = $encounter->id;
                $epicrisis->save();
            }
            if (app(ClinicalRecordService::class)->available()) {
                app(ClinicalRecordService::class)->registerEpicrisis($epicrisis, $context);
                $documentContext = array_merge($context, array('closure_type' => $closureType));
                app(ClinicalRecordService::class)->signDocument('hc_epicrisis_final', $epicrisis->id, $this->actorId($context), $documentContext);
            }

            $closure->status = 'COMPLETED';
            $closure->save();
            return $closure;
            });
        });
    }

    public function isHospital($solicitud, $encounter = null)
    {
        if ((int) $solicitud->estado_paso === 0) return false;
        $encounter = $encounter ?: app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
        return !$encounter || $encounter->care_setting !== ClinicalEncounterService::AMBULATORY;
    }

    public function assertHospitalActor($encounter, array $roles, array $context = array())
    {
        $user = \Sis_medico\User::find($this->actorId($context));
        if (!$user || (int) $user->estado !== 1 || !$user->hasAnyUserTypeName($roles)) {
            throw new ClinicalClosureBlockedException(array('No tiene el rol habilitado para esta accion hospitalaria.'));
        }
        if (!$encounter) throw new ClinicalClosureBlockedException(array('No se encontro el episodio canonico.'));
        try {
            app(ClinicalAccessControlService::class)->assertAllowed($encounter->id, 'encounter.close',
                array('user' => $user, 'ip' => $this->ip($context)));
        } catch (ClinicalAccessDeniedException $exception) {
            throw new ClinicalClosureBlockedException(array($exception->getMessage()));
        }
    }

    // The hospital editor and signature share the solicitud lock; ambulatory editors retain their rules.
    public function editEpicrisis($solicitudId, callable $operation, array $context = array())
    {
        return DB::transaction(function () use ($solicitudId, $operation, $context) {
            $solicitud = Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->firstOrFail();
            if ($this->isHospital($solicitud)) {
                $encounter = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
                $this->assertHospitalActor($encounter, array('SISTEMAS', 'DOCTORES', 'DOCTOR'), $context);
                if (ClinicalEncounterClosure::where('encounter_id', $encounter->id)
                    ->whereIn('status', array(self::SIGNED, 'COMPLETED'))->exists()) {
                    throw new ClinicalClosureBlockedException(array('La epicrisis ya esta firmada. Conserve el documento original.'));
                }
            }
            return $operation($solicitud);
        });
    }

    public function confirmPhysicalExit($solicitudId, $closureId, array $context = array())
    {
        $this->requirePhysicalExitSchema();
        return DB::transaction(function () use ($solicitudId, $closureId, $context) {
            return app(ClinicalClosureGuard::class)->run(function () use ($solicitudId, $closureId, $context) {
                $solicitud = Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->first();
                if (!$solicitud) throw new ClinicalClosureBlockedException(array('No se encontro la solicitud.'));
                $linked = app(ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id);
                $encounter = $linked ? ClinicalEncounter::where('id', $linked->id)->lockForUpdate()->first() : null;
                $this->assertHospitalActor($encounter, array('SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA'), $context);
                if (!$this->isHospital($solicitud, $encounter)) {
                    throw new ClinicalClosureBlockedException(array('La atencion ambulatoria conserva su cierre habitual.'));
                }
                $closure = ClinicalEncounterClosure::where('encounter_id', $encounter->id)->lockForUpdate()->first();
                if (!$closure || (string) $closure->id !== (string) $closureId
                    || (string) $closure->solicitud_id !== (string) $solicitud->id) {
                    throw new ClinicalClosureBlockedException(array('La firma no corresponde al episodio indicado.'));
                }
                // Completed legacy closures are historical, never a request to release a bed again.
                if ($closure->status === 'COMPLETED') return $closure;
                if ($closure->status !== self::SIGNED || $closure->closure_type !== self::DISCHARGE
                    || !$closure->signed_by || !$closure->signed_at
                    || $encounter->clinical_state !== ClinicalStateTransitionService::CLOSURE_PENDING
                    || $encounter->status !== ClinicalEncounterService::ACTIVE || $encounter->ended_at
                    || (string) $solicitud->estado === '0' || $solicitud->fecha_salida) {
                    throw new ClinicalClosureBlockedException(array('Se requiere el alta medica firmada, pendiente de salida.'));
                }
                $document = \Sis_medico\ClinicalDocument::where('encounter_id', $encounter->id)
                    ->where('source_type', 'hc_epicrisis_final')->where('source_id', (string) $closure->epicrisis_id)
                    ->whereNotNull('signed_at')->where('signed_by', $closure->signed_by)->first();
                if (!$document || !app(ClinicalStateTransitionService::class)->available()) {
                    throw new ClinicalClosureBlockedException(array('Falta la firma canonica o el servicio de estados.'));
                }
                $now = date('Y-m-d H:i:s');
                $this->closeHospitalStay($solicitud, $context);
                $encounter = app(ClinicalStateTransitionService::class)->transition($encounter,
                    ClinicalStateTransitionService::CLOSED_DISCHARGED, 'Salida fisica confirmada tras alta medica firmada.', $context);
                $encounter->status = ClinicalStateTransitionService::CLOSED_DISCHARGED;
                $encounter->ended_at = $now;
                $encounter->close_reason = self::DISCHARGE;
                $encounter->id_usuariomod = $this->actorId($context);
                $encounter->ip_modificacion = $this->ip($context);
                $encounter->save();
                $solicitud->update(array('fecha_salida' => $now, 'estado' => 0,
                    'id_usuariomod' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
                Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->whereNull('fecha_salida')
                    ->update(array('fecha_salida' => $now, 'id_usuariomod' => $this->actorId($context),
                        'ip_modificacion' => $this->ip($context)));
                $this->closeEmergencyFlow($solicitud, self::DISCHARGE, $now, $context);
                $closure->update(array('status' => 'COMPLETED', 'physical_exit_at' => $now,
                    'physical_exit_by' => $this->actorId($context), 'physical_exit_ip' => $this->ip($context)));
                $this->hospitalEvent($encounter, 'PHYSICAL_EXIT_CONFIRMED', $closure, $context);
                return $closure;
            });
        });
    }

    private function requirePhysicalExitSchema()
    {
        if (!$this->available() || !Schema::hasColumn('clinical_encounter_closures', 'physical_exit_at')
            || !Schema::hasColumn('clinical_encounter_closures', 'physical_exit_by')
            || !Schema::hasColumn('clinical_encounter_closures', 'physical_exit_ip')) {
            throw new ClinicalClosureBlockedException(array('La migracion de salida fisica hospitalaria no esta aplicada.'));
        }
    }

    private function hospitalEvent($encounter, $type, $closure, array $context)
    {
        \Sis_medico\ClinicalEncounterEvent::create(array('encounter_id' => $encounter->id,
            'event_type' => $type, 'from_value' => null, 'to_value' => $closure->status,
            'reason' => 'Cierre hospitalario #' . $closure->id,
            'actor_id' => $this->actorId($context), 'ip_address' => $this->ip($context)));
    }

    private function closeEmergencyFlow($solicitud, $type, $now, array $context)
    {
        if (!Schema::hasTable('clinical_emergency_flows')) return;
        $flows = \Sis_medico\ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)
            ->where('status', '<>', 'CLOSED')->lockForUpdate()->get();
        foreach ($flows as $flow) {
            $flow->update(array('status' => 'CLOSED', 'closed_at' => $flow->closed_at ?: $now, 'disposition' => $type,
                'bed_request_status' => null, 'updated_by' => $this->actorId($context), 'ip_address' => $this->ip($context)));
        }
    }

    private function blockers($epicrisis, array $preview, array $data, array $context)
    {
        $blockers = array();
        $type = isset($data['closure_type']) ? $data['closure_type'] : null;
        if (!in_array($type, array(self::DISCHARGE, self::DECEASED), true)) $blockers[] = 'Seleccione alta clínica o defunción.';
        if (!$preview['epicrisis_ready']) $blockers[] = 'Complete las secciones B, C, D y E, además de la conclusión clínica de la epicrisis.';
        if (!$preview['diagnosis_ready']) $blockers[] = 'Registre al menos un diagnóstico definitivo de egreso.';
        if (!$preview['signature_ready']) $blockers[] = 'El profesional que cierra no tiene una firma configurada.';
        if (!isset($data['medication_reconciliation']) || strlen(trim($data['medication_reconciliation'])) < 5) {
            $blockers[] = 'Documente la conciliación de medicamentos.';
        }
        if ($type === self::DISCHARGE) {
            if (!$epicrisis || !$this->filled($epicrisis->indicaciones_alta)) $blockers[] = 'Complete las indicaciones de alta.';
            if (!$epicrisis || !$this->filled($epicrisis->alta)) $blockers[] = 'Seleccione el tipo de alta.';
            if ($epicrisis && $this->filled($epicrisis->defuncion) && strtoupper(trim($epicrisis->defuncion)) !== 'NO') {
                $blockers[] = 'Una defunción no puede cerrarse como alta común.';
            }
        }
        if ($type === self::DECEASED && (!$epicrisis || !$this->filled($epicrisis->defuncion)
            || strtoupper(trim($epicrisis->defuncion)) === 'NO')) {
            $blockers[] = 'Para cerrar por defunción complete la condición de defunción en la epicrisis.';
        }
        if ($type === self::DECEASED) {
            if (!app(ClinicalDeathService::class)->available()) {
                $blockers[] = 'La migración del circuito específico de defunción no está aplicada.';
            } else {
                $blockers = array_merge($blockers, app(ClinicalDeathService::class)->blockers($data));
            }
        }
        if ($preview['active_orders_count'] > 0) {
            $decision = isset($data['pending_orders_decision']) ? $data['pending_orders_decision'] : null;
            if (!in_array($decision, array(self::CANCEL, self::FOLLOW_UP), true)) {
                $blockers[] = 'Cancele las órdenes pendientes o páselas a seguimiento.';
            }
            if (!isset($data['pending_orders_reason']) || strlen(trim($data['pending_orders_reason'])) < 5) {
                $blockers[] = 'Documente el motivo de la disposición de órdenes pendientes.';
            }
            if ($decision === self::FOLLOW_UP && empty($data['follow_up_responsible_id'])) {
                $blockers[] = 'Seleccione un responsable para el seguimiento de órdenes.';
            }
        }
        if (!empty($preview['pending_emergency_medication_exceptions'])) {
            $blockers[] = 'Regularice las administraciones de emergencia realizadas sin orden antes de cerrar el episodio.';
        }
        if (!empty($preview['encounter_id']) && app(ClinicalDocumentCompletenessService::class)->available()) {
            foreach (app(ClinicalDocumentCompletenessService::class)->summary($preview['encounter_id']) as $item) {
                if ($item['status'] === 'COMPLETE') continue;
                $exceptional = !empty($context['hospital_closure']) && ($type === self::DECEASED
                    || $this->isVoluntaryDeparture($epicrisis));
                if ($exceptional && in_array($item['document']->document_type,
                    array('PROCEDURE_REPORT', 'PREOPERATIVE', 'PREOPERATIVE_ASSESSMENT',
                        'HOSPITAL_PREANESTHETIC', 'HOSPITAL_POSTANESTHETIC'), true)) continue;
                $detail = count($item['missing']) ? ': '.implode(', ', $item['missing']) : '';
                $blockers[] = 'Complete el documento '.$item['document']->title.$detail.'.';
            }
        }
        return $blockers;
    }

    private function isVoluntaryDeparture($epicrisis)
    {
        return $epicrisis && (strtoupper(trim((string) $epicrisis->alta)) === 'VOLUNTARIA'
            || in_array(strtoupper(trim((string) $epicrisis->retiro)), array('VOLUNTARIO', 'ABANDONO'), true));
    }

    private function closeHospitalStay($solicitud, array $context)
    {
        $transferIds = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->pluck('id')->all();
        if (!$transferIds) return;
        $bedIds = Ho_Hospitalizacion::whereIn('id_traspaso', $transferIds)->where('estado', 1)
            ->pluck('id_cama')->unique()->sort()->values()->all();
        // Only currently active stays participate. Closed history can never claim a physical bed.
        $beds = \Sis_medico\Cama::whereIn('id', $bedIds)->orderBy('id')->lockForUpdate()->get();
        foreach ($beds as $bed) {
            $occupants = Ho_Hospitalizacion::where('id_cama', $bed->id)->where('estado', 1)->lockForUpdate()->get();
            if ($occupants->isEmpty()) continue;
            $owned = true;
            foreach ($occupants as $occupant) {
                if (!in_array($occupant->id_traspaso, $transferIds)
                    || (string) $occupant->id_paciente !== (string) $solicitud->id_paciente) $owned = false;
            }
            if (!$owned) continue;
            $legacy = \Sis_medico\CamaPaciente::where('id_cama', $bed->id)->where('estado', 1)->lockForUpdate()->get();
            foreach ($legacy as $row) {
                if ((string) $row->id_paciente !== (string) $solicitud->id_paciente) $owned = false;
            }
            $transactions = \Sis_medico\CamaTransaccion::where('id_cama', $bed->id)->lockForUpdate()->get();
            if ($transactions->count() !== 1) {
                throw new ClinicalClosureBlockedException(array('Concilie la ocupacion de la cama antes de confirmar la salida.'));
            }
            $transaction = $transactions->first();
            if ((string) $transaction->id_paciente !== (string) $solicitud->id_paciente) $owned = false;
            if (!$owned) continue;
            $type = $bed->habitacion ? (int) $bed->habitacion->id_tipo : 0;
            $transaction->update(array('estado_cama' => 'preparacion', 'id_paciente' => null,
                'id_imagen' => $type === 3 ? 6 : ($type === 5 ? 10 : 2),
                'id_usuariomod' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
            $bed->update(array('estado' => 2, 'id_usuariomod' => $this->actorId($context),
                'ip_modificacion' => $this->ip($context)));
            foreach ($legacy as $row) {
                $row->update(array('estado' => 0, 'observaciones' => '//SALIDA FISICA: CAMA EN PREPARACION//'));
                \Sis_medico\CamaPacienteLog::create(array('id_paciente' => $solicitud->id_paciente,
                    'id_cama' => $bed->id, 'estado' => 0, 'observacion' => 'Salida fisica: cama en preparacion',
                    'observaciones' => '//CIERRE HOSPITALARIO//', 'id_usuario' => $this->actorId($context),
                    'id_usuariocrea' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
            }
        }
        Ho_Hospitalizacion::whereIn('id_traspaso', $transferIds)->where('estado', 1)->update(array(
            'estado' => 2, 'id_usuariomod' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
        Ho_Traspaso_Sala008::whereIn('id', $transferIds)->whereIn('estado', array(1, 2))->update(array(
            'estado' => 3, 'id_usuariomod' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
    }

    private function closeHospitalization($solicitud, array $context)
    {
        $transferIds = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->pluck('id')->all();
        if (!count($transferIds)) return;
        $admissions = Ho_Hospitalizacion::whereIn('id_traspaso', $transferIds)
            ->whereIn('estado', array(1, 2))->orderBy('id_cama')->lockForUpdate()->get();
        foreach ($admissions as $admission) {
            $bed = \Sis_medico\Cama::where('id', $admission->id_cama)->lockForUpdate()->first();
            if (!$bed) continue;
            // A retry of an old closure must never release a later occupant.
            if (Ho_Hospitalizacion::where('id_cama', $bed->id)->where('estado', 1)
                ->where('id', '<>', $admission->id)->lockForUpdate()->exists()) continue;
            $transaction = \Sis_medico\CamaTransaccion::where('id_cama', $bed->id)->lockForUpdate()->first();
            if ($transaction && (string) $transaction->id_paciente === (string) $admission->id_paciente) {
                $type = $bed->habitacion ? (int) $bed->habitacion->id_tipo : 0;
                $image = $type === 3 ? 6 : ($type === 5 ? 10 : 2);
                $transaction->update(array('estado_cama' => 'preparacion', 'id_paciente' => null,
                    'id_imagen' => $image, 'id_usuariomod' => $this->actorId($context),
                    'ip_modificacion' => $this->ip($context)));
                $bed->update(array('estado' => 2, 'id_usuariomod' => $this->actorId($context),
                    'ip_modificacion' => $this->ip($context)));
            }
            $legacy = \Sis_medico\CamaPaciente::where('id_paciente', $admission->id_paciente)
                ->where('id_cama', $bed->id)->where('estado', 1)->lockForUpdate()->first();
            if ($legacy) {
                $legacy->update(array('estado' => 0, 'observaciones' => '//CIERRE CLINICO: CAMA EN PREPARACION//'));
                \Sis_medico\CamaPacienteLog::create(array('id_paciente' => $admission->id_paciente,
                    'id_cama' => $bed->id, 'estado' => 0, 'observacion' => 'Cierre del episodio: cama en preparación',
                    'observaciones' => '//CIERRE CLINICO//', 'id_usuario' => $this->actorId($context),
                    'id_usuariocrea' => $this->actorId($context), 'ip_modificacion' => $this->ip($context)));
            }
        }
        Ho_Hospitalizacion::whereIn('id_traspaso', $transferIds)->where('estado', 1)->update(array(
            'estado' => 2,
            'id_usuariomod' => $this->actorId($context),
            'ip_modificacion' => $this->ip($context),
        ));
        Ho_Traspaso_Sala008::whereIn('id', $transferIds)->whereIn('estado', array(1, 2))->update(array(
            'estado' => 3,
            'id_usuariomod' => $this->actorId($context),
            'ip_modificacion' => $this->ip($context),
        ));
    }

    private function cancelPendingOrder(array $order, array $context)
    {
        if (isset($order['source_type']) && isset($order['id'])) {
            $clinicalOrder = ClinicalOrder::find($order['id']);
            if ($clinicalOrder && !in_array($clinicalOrder->status, array(ClinicalOrderService::COMPLETED, ClinicalOrderService::CANCELLED), true)) {
                app(ClinicalOrderService::class)->transition($clinicalOrder, ClinicalOrderService::CANCELLED,
                    'Cancelada durante el cierre clínico del episodio.', $context);
            }
        }

        $legacyId = isset($order['legacy_id']) ? $order['legacy_id'] : $order['id'];
        if ($order['type'] === 'LABORATORY' && (!isset($order['source_type']) || $order['source_type'] === 'examen_orden')) {
            $model = Examen_Orden::find($legacyId);
        } elseif ($order['type'] === 'MEDICATION') {
            $model = ClinicalMedicationOrder::find($legacyId);
            if ($model) {
                $model->status = 'CANCELLED';
                $model->save();
            }
            return;
        } elseif (!isset($order['source_type']) || $order['source_type'] === 'orden') {
            $model = Orden::find($legacyId);
        } else {
            $model = null;
        }
        if ($model) {
            $model->estado = 0;
            $model->save();
        }
    }

    private function filled($value)
    {
        if (is_null($value)) return false;
        $text = trim(str_replace('&nbsp;', ' ', strip_tags((string) $value)));
        return $text !== '' && strtoupper($text) !== 'PENDIENTE DE COMPLETAR';
    }

    private function actorId(array $context)
    {
        if (isset($context['actor_id'])) return $context['actor_id'];
        return Auth::check() ? Auth::user()->id : null;
    }

    private function ip(array $context)
    {
        if (isset($context['ip'])) return $context['ip'];
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }
}
