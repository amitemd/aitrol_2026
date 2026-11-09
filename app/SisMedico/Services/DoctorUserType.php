<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\User;

/**
 * Identifica a los médicos por el nombre estable de su tipo de usuario.
 *
 * Los identificadores de tipousuario pueden variar entre instalaciones, por
 * lo que la agenda clínica nunca debe asumir un id numérico para DOCTOR.
 */
class DoctorUserType
{
    public static function names()
    {
        return array('DOCTOR', 'DOCTORES');
    }

    public static function constrain($query, $userAlias = 'u', $typeAlias = 'doctor_user_type')
    {
        return $query
            ->join('tipousuario as ' . $typeAlias, $typeAlias . '.id', '=', $userAlias . '.id_tipo_usuario')
            ->where($typeAlias . '.estado', 1)
            ->whereIn(DB::raw('UPPER(TRIM(' . $typeAlias . '.nombre))'), self::names());
    }

    public static function matches(User $user)
    {
        return $user->hasUserTypeName(self::names());
    }
}
