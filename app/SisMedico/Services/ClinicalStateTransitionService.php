<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\Exceptions\ClinicalStateTransitionException;

class ClinicalStateTransitionService
{
    const AMBULATORY_ACTIVE = 'AMBULATORY_ACTIVE';
    const EMERGENCY_ACTIVE = 'EMERGENCY_ACTIVE';
    const INPATIENT_ACTIVE = 'INPATIENT_ACTIVE';
    const IMAGING_ACTIVE = 'IMAGING_ACTIVE';
    const SURGERY_ACTIVE = 'SURGERY_ACTIVE';
    const ICU_ACTIVE = 'ICU_ACTIVE';
    const NICU_ACTIVE = 'NICU_ACTIVE';
    const TRANSFER_PENDING = 'TRANSFER_PENDING';
    const CLOSURE_PENDING = 'CLOSURE_PENDING';
    const CLOSED_DISCHARGED = 'CLOSED_DISCHARGED';
    const CLOSED_DECEASED = 'CLOSED_DECEASED';

    public function available()
    {
        return Schema::hasTable('clinical_encounters')
            && Schema::hasColumn('clinical_encounters', 'clinical_state')
            && Schema::hasTable('clinical_encounter_events');
    }

    public function stateForLegacyStep($step)
    {
        $states = array(
            0 => self::AMBULATORY_ACTIVE,
            2 => self::EMERGENCY_ACTIVE,
            3 => self::INPATIENT_ACTIVE,
            4 => self::IMAGING_ACTIVE,
            5 => self::SURGERY_ACTIVE,
            6 => self::ICU_ACTIVE,
            7 => self::NICU_ACTIVE,
        );

        if (!isset($states[(int) $step])) {
            throw new ClinicalStateTransitionException('El módulo de destino no tiene un estado clínico configurado.');
        }
        return $states[(int) $step];
    }

    public function initialize($encounter, $legacyStep, array $context = array())
    {
        if (is_null($encounter) || !$this->available()) return $encounter;
        return app(ClinicalStateGuard::class)->run(function () use ($encounter, $legacyStep, $context) {
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->first();
            if (!is_null($encounter->clinical_state)) return $encounter;

            $state = $this->stateForLegacyStep($legacyStep);
            $encounter->clinical_state = $state;
            $encounter->id_usuariomod = $this->actorId($context);
            $encounter->ip_modificacion = $this->ip($context);
            $encounter->save();
            $this->event($encounter, 'CLINICAL_STATE_INITIALIZED', null, $state, 'Estado derivado del flujo clínico vigente.', $context);
            return $encounter;
        });
    }

    public function transition($encounter, $toState, $reason, array $context = array())
    {
        if (is_null($encounter) || !$this->available()) return $encounter;
        return app(ClinicalStateGuard::class)->run(function () use ($encounter, $toState, $reason, $context) {
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->first();
            $fromState = $encounter->clinical_state;

            if ($fromState === $toState) return $encounter;
            if (!$this->isAllowed($fromState, $toState)) {
                throw new ClinicalStateTransitionException('Transición clínica no permitida: ' . $fromState . ' → ' . $toState . '.');
            }

            $encounter->clinical_state = $toState;
            $encounter->id_usuariomod = $this->actorId($context);
            $encounter->ip_modificacion = $this->ip($context);
            $encounter->save();
            $this->event($encounter, 'CLINICAL_STATE_CHANGED', $fromState, $toState, $reason, $context);
            return $encounter;
        });
    }

    public function isAllowed($fromState, $toState)
    {
        if (is_null($fromState)) return in_array($toState, $this->activeStates(), true);
        if (in_array($fromState, $this->activeStates(), true)) {
            return in_array($toState, array(self::TRANSFER_PENDING, self::CLOSURE_PENDING), true);
        }
        if ($fromState === self::TRANSFER_PENDING) return in_array($toState, $this->activeStates(), true);
        return $fromState === self::CLOSURE_PENDING
            && in_array($toState, array(self::CLOSED_DISCHARGED, self::CLOSED_DECEASED), true);
    }

    private function activeStates()
    {
        return array(self::AMBULATORY_ACTIVE, self::EMERGENCY_ACTIVE, self::INPATIENT_ACTIVE,
            self::IMAGING_ACTIVE, self::SURGERY_ACTIVE, self::ICU_ACTIVE, self::NICU_ACTIVE);
    }

    private function event($encounter, $type, $from, $to, $reason, array $context)
    {
        ClinicalEncounterEvent::create(array(
            'encounter_id' => $encounter->id,
            'event_type' => $type,
            'from_value' => $from,
            'to_value' => $to,
            'reason' => $reason,
            'actor_id' => $this->actorId($context),
            'ip_address' => $this->ip($context),
        ));
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
