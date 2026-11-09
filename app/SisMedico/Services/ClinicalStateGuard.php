<?php

namespace Sis_medico\Services;

use Closure;
use Sis_medico\Exceptions\ClinicalStateTransitionException;

class ClinicalStateGuard
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

    public static function assertEncounterMutation($encounter)
    {
        if (self::$depth > 0 || !$encounter->exists) return;
        if ($encounter->isDirty('clinical_state')) {
            throw new ClinicalStateTransitionException(
                'El estado clínico solo puede cambiar mediante ClinicalStateTransitionService.'
            );
        }
    }
}
