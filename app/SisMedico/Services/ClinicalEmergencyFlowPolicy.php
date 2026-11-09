<?php

namespace Sis_medico\Services;

class ClinicalEmergencyFlowPolicy
{
    public static function transferSteps()
    {
        return ['HOSPITALIZATION' => 3, 'SURGERY' => 5, 'ICU' => 6, 'NICU' => 7];
    }

    public static function validTriage($level, $reason)
    {
        return filter_var($level, FILTER_VALIDATE_INT) !== false
            && (int) $level >= 1 && (int) $level <= 5
            && is_string($reason) && strlen(trim($reason)) >= 5;
    }

    public static function assertVersion($expected, $actual)
    {
        if ((!is_string($expected) && !is_int($expected))
            || filter_var($expected, FILTER_VALIDATE_INT) === false || (int) $expected < 0) {
            throw new \InvalidArgumentException('Falta la version vigente del flujo. Recargue la pantalla.');
        }
        if ((int) $expected !== (int) $actual) {
            throw new \DomainException('Otro profesional actualizo el episodio. Recargue la pantalla.');
        }
    }

    public static function rolesFor($action)
    {
        if (in_array($action, ['START', 'START_CRITICAL', 'DECIDE', 'CANCEL_TRANSFER'], true)) return [1, 3];
        if ($action === 'REROUTE_HOSPITALIZATION') return [1, 3, 6];
        if ($action === 'TRIAGE') return [1, 3, 6];
        if ($action === 'MOVE') return [1, 3, 6];
        return [];
    }

    public static function canAct($action, $role, $status, $clinicalState, $closed, $hasTriage, $criticalArrival = false)
    {
        if (!in_array((int) $role, self::rolesFor($action), true)) return false;
        try {
            self::assertAction($action, $status, $clinicalState, $closed, $hasTriage, $criticalArrival);
            return true;
        } catch (\DomainException | \InvalidArgumentException $exception) {
            return false;
        }
    }

    public static function assertAction($action, $status, $clinicalState, $closed, $hasTriage, $criticalArrival = false)
    {
        if ($closed || !in_array($clinicalState, ['EMERGENCY_ACTIVE', 'TRANSFER_PENDING'], true)) {
            throw new \DomainException('El episodio no esta abierto en Emergencia.');
        }
        $allowed = [
            'TRIAGE' => ['PENDING_TRIAGE', 'WAITING_ATTENTION', 'IN_ATTENTION', 'UNDER_OBSERVATION', 'TRANSFER_PENDING'],
            'START' => ['WAITING_ATTENTION'],
            'START_CRITICAL' => ['PENDING_TRIAGE', 'WAITING_ATTENTION'],
            'MOVE' => ['PENDING_TRIAGE', 'WAITING_ATTENTION', 'IN_ATTENTION', 'UNDER_OBSERVATION'],
            'DECIDE' => ['IN_ATTENTION', 'UNDER_OBSERVATION'],
            'REROUTE_HOSPITALIZATION' => ['PENDING_TRIAGE', 'WAITING_ATTENTION'],
            'CANCEL_TRANSFER' => ['TRANSFER_PENDING'],
        ];
        if (!isset($allowed[$action]) || !in_array($status, $allowed[$action], true)
            || ($clinicalState === 'TRANSFER_PENDING'
                && (!in_array($action, ['TRIAGE', 'CANCEL_TRANSFER'], true) || $status !== 'TRANSFER_PENDING'))) {
            throw new \DomainException('La accion no corresponde al estado actual del episodio.');
        }
        if (in_array($action, ['START', 'DECIDE'], true) && !$hasTriage) {
            throw new \InvalidArgumentException('Registre un triaje valido antes de atender o decidir.');
        }
        if ($action === 'REROUTE_HOSPITALIZATION' && $criticalArrival) {
            throw new \InvalidArgumentException('Un ingreso crítico requiere valoración antes de cambiar de módulo.');
        }
        if ($action === 'START_CRITICAL' && !$criticalArrival) {
            throw new \InvalidArgumentException('El inicio critico requiere un ingreso critico registrado.');
        }
    }
}
