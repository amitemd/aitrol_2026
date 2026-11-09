<?php

namespace Sis_medico\Services;

use Closure;
use Sis_medico\Exceptions\ClinicalClosureBypassException;

class ClinicalClosureGuard
{
    private static $depth = 0;

    public function run(Closure $operation)
    {
        self::$depth++;
        try {
            return $operation();
        } finally {
            self::$depth--;
        }
    }

    public static function assertSolicitudMutation($solicitud)
    {
        if (self::$depth > 0 || !$solicitud->exists) return;
        $wasActive = (string) $solicitud->getOriginal('estado') !== '0';
        $becomesClosed = $solicitud->isDirty('estado') && (string) $solicitud->estado === '0';
        $addsExit = $solicitud->isDirty('fecha_salida') && empty($solicitud->getOriginal('fecha_salida'))
            && !empty($solicitud->fecha_salida);
        if ($wasActive && ($becomesClosed || $addsExit)) {
            throw new ClinicalClosureBypassException('La solicitud solo puede cerrarse mediante ClinicalEncounterClosureService.');
        }
    }

    public static function assertEncounterMutation($encounter)
    {
        if (self::$depth > 0 || !$encounter->exists) return;
        $closed = array(ClinicalStateTransitionService::CLOSED_DISCHARGED, ClinicalStateTransitionService::CLOSED_DECEASED);
        $closesStatus = $encounter->isDirty('status') && in_array($encounter->status, $closed, true);
        $addsEnd = $encounter->isDirty('ended_at') && empty($encounter->getOriginal('ended_at')) && !empty($encounter->ended_at);
        if ($closesStatus || $addsEnd) {
            throw new ClinicalClosureBypassException('El episodio solo puede cerrarse mediante ClinicalEncounterClosureService.');
        }
    }
}
