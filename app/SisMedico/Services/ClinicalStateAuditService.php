<?php

namespace Sis_medico\Services;

use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterClosure;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\ClinicalTransferReconciliation;
use Sis_medico\Ho_Solicitud;

class ClinicalStateAuditService
{
    public function inspect($repair = false)
    {
        if (!Schema::hasTable('clinical_encounters') || !Schema::hasColumn('clinical_encounters', 'clinical_state')
            || !Schema::hasTable('clinical_encounter_events') || !Schema::hasTable('clinical_transfer_reconciliations')
            || !Schema::hasTable('clinical_encounter_closures')) {
            return array('available' => false, 'pending' => null, 'repaired' => 0, 'items' => array());
        }

        $valid = array(
            ClinicalStateTransitionService::AMBULATORY_ACTIVE,
            ClinicalStateTransitionService::EMERGENCY_ACTIVE,
            ClinicalStateTransitionService::INPATIENT_ACTIVE,
            ClinicalStateTransitionService::IMAGING_ACTIVE,
            ClinicalStateTransitionService::SURGERY_ACTIVE,
            ClinicalStateTransitionService::ICU_ACTIVE,
            ClinicalStateTransitionService::NICU_ACTIVE,
            ClinicalStateTransitionService::TRANSFER_PENDING,
            ClinicalStateTransitionService::CLOSURE_PENDING,
            ClinicalStateTransitionService::CLOSED_DISCHARGED,
            ClinicalStateTransitionService::CLOSED_DECEASED,
        );
        $closed = array(ClinicalStateTransitionService::CLOSED_DISCHARGED, ClinicalStateTransitionService::CLOSED_DECEASED);
        $active = array(
            ClinicalStateTransitionService::AMBULATORY_ACTIVE, ClinicalStateTransitionService::EMERGENCY_ACTIVE,
            ClinicalStateTransitionService::INPATIENT_ACTIVE, ClinicalStateTransitionService::IMAGING_ACTIVE,
            ClinicalStateTransitionService::SURGERY_ACTIVE, ClinicalStateTransitionService::ICU_ACTIVE,
            ClinicalStateTransitionService::NICU_ACTIVE,
        );
        $items = array();
        $repaired = 0;

        ClinicalEncounter::orderBy('created_at')->chunk(200, function ($encounters) use (&$items, &$repaired, $valid, $closed, $active, $repair) {
            foreach ($encounters as $encounter) {
                if (!$encounter->clinical_state && $repair && $encounter->status === ClinicalEncounterService::ACTIVE) {
                    $step = $encounter->care_setting === ClinicalEncounterService::AMBULATORY ? 0
                        : ($encounter->care_setting === ClinicalEncounterService::EMERGENCY ? 2 : 3);
                    app(ClinicalStateTransitionService::class)->initialize($encounter, $step, array(
                        'actor_id' => 'SYSTEM_STATE_AUDIT', 'ip' => '127.0.0.1',
                    ));
                    $encounter->refresh();
                    $repaired++;
                }
                if (!$encounter->clinical_state) $this->issue($items, $encounter, 'MISSING_STATE', 'Episodio sin estado clínico.');
                elseif (!in_array($encounter->clinical_state, $valid, true)) $this->issue($items, $encounter, 'UNKNOWN_STATE', 'Estado clínico no reconocido.');
                elseif ($encounter->status === ClinicalEncounterService::ACTIVE && in_array($encounter->clinical_state, $closed, true)) $this->issue($items, $encounter, 'ACTIVE_CLOSED', 'Episodio activo con estado cerrado.');
                elseif ($encounter->status !== ClinicalEncounterService::ACTIVE && !in_array($encounter->clinical_state, $closed, true)) $this->issue($items, $encounter, 'ENDED_OPEN', 'Episodio finalizado sin estado cerrado.');

                if ($encounter->clinical_state) {
                    $events = ClinicalEncounterEvent::where('encounter_id', $encounter->id)
                        ->whereIn('event_type', array('CLINICAL_STATE_INITIALIZED', 'CLINICAL_STATE_CHANGED'))->orderBy('id')->get();
                    if (!$events->count() && $repair) {
                        ClinicalEncounterEvent::create(array(
                            'encounter_id' => $encounter->id,
                            'event_type' => 'CLINICAL_STATE_INITIALIZED',
                            'from_value' => null,
                            'to_value' => $encounter->clinical_state,
                            'reason' => 'Línea base reconstruida por auditoría; no representa una transición clínica retroactiva.',
                            'actor_id' => 'SYSTEM_STATE_AUDIT',
                            'ip_address' => '127.0.0.1',
                        ));
                        $events = ClinicalEncounterEvent::where('encounter_id', $encounter->id)
                            ->whereIn('event_type', array('CLINICAL_STATE_INITIALIZED', 'CLINICAL_STATE_CHANGED'))->orderBy('id')->get();
                        $repaired++;
                    }
                    if (!$events->count()) $this->issue($items, $encounter, 'MISSING_EVENT', 'Estado actual sin evento de auditoría.');
                    foreach ($events as $event) {
                        if ($event->event_type === 'CLINICAL_STATE_CHANGED'
                            && (!app(ClinicalStateTransitionService::class)->isAllowed($event->from_value, $event->to_value)
                                || empty($event->actor_id) || trim((string) $event->reason) === '')) {
                            $this->issue($items, $encounter, 'INVALID_EVENT', 'Transición sin regla, motivo o actor auditable.');
                            break;
                        }
                    }
                    $lastEvent = $events->last();
                    if ($lastEvent && $lastEvent->to_value !== $encounter->clinical_state) {
                        $this->issue($items, $encounter, 'EVENT_STATE_MISMATCH', 'El último evento no coincide con el estado actual.');
                    }
                }

                if (in_array($encounter->clinical_state, $active, true) && Schema::hasColumn('ho_solicitud', 'encounter_id')) {
                    $solicitud = Ho_Solicitud::where('encounter_id', $encounter->id)->where('estado', '<>', 0)->orderBy('id', 'desc')->first();
                    if ($solicitud) {
                        try { $expected = app(ClinicalStateTransitionService::class)->stateForLegacyStep($solicitud->estado_paso); }
                        catch (\Exception $exception) { $expected = null; }
                        if (!$expected || $expected !== $encounter->clinical_state) {
                            $this->issue($items, $encounter, 'LEGACY_STATE_MISMATCH', 'El módulo heredado no coincide con el estado clínico.');
                        }
                    }
                }

                if ($encounter->clinical_state === ClinicalStateTransitionService::TRANSFER_PENDING
                    && !ClinicalTransferReconciliation::where('encounter_id', $encounter->id)->where('status', 'PENDING')->exists()) {
                    $this->issue($items, $encounter, 'TRANSFER_WITHOUT_RECONCILIATION', 'Traslado pendiente sin conciliación de órdenes.');
                }
                if ($encounter->clinical_state === ClinicalStateTransitionService::CLOSURE_PENDING
                    && !ClinicalEncounterClosure::where('encounter_id', $encounter->id)->where('status', 'PENDING')->exists()) {
                    $this->issue($items, $encounter, 'CLOSURE_WITHOUT_CHECKLIST', 'Cierre pendiente sin checklist transaccional.');
                }
                if (in_array($encounter->clinical_state, $closed, true)) {
                    $closureType = $encounter->clinical_state === ClinicalStateTransitionService::CLOSED_DECEASED ? 'DECEASED' : 'DISCHARGE';
                    if (!ClinicalEncounterClosure::where('encounter_id', $encounter->id)->where('status', 'COMPLETED')
                        ->where('closure_type', $closureType)->exists()) {
                        $this->issue($items, $encounter, 'CLOSED_WITHOUT_TRANSACTION', 'Estado cerrado sin transacción de cierre coincidente.');
                    }
                }
            }
        });

        $byType = array();
        foreach ($items as $item) $byType[$item['type']] = isset($byType[$item['type']]) ? $byType[$item['type']] + 1 : 1;
        return array('available' => true, 'pending' => count($items), 'repaired' => $repaired, 'items' => $items, 'by_type' => $byType);
    }

    private function issue(array &$items, ClinicalEncounter $encounter, $type, $reason)
    {
        $items[] = array('encounter_id' => $encounter->id, 'type' => $type, 'reason' => $reason);
    }
}
