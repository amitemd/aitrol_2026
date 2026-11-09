<?php

namespace Sis_medico\Services\v3;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Validation\ValidationException;
use Sis_medico\User;

/** Resolve the patient profile from the catalog, never from an assumed numeric ID. */
class PatientUserType
{
    public function id()
    {
        if (!Schema::hasTable('tipousuario')) {
            $this->invalid('No está disponible el catálogo de tipos de usuario. Solicite a Sistemas habilitarlo y crear el tipo PACIENTE antes de registrar pacientes.');
        }
        $types = DB::table('tipousuario')->whereIn(DB::raw('UPPER(TRIM(nombre))'), array('PACIENTE', 'PACIENTES'))
            ->get(array('id', 'nombre', 'estado'));
        if ($types->isEmpty()) {
            $this->invalid('Falta el tipo de usuario PACIENTE. Créelo en Administrar tipos de usuario y déjelo activo antes de registrar al paciente.');
        }
        if ($types->count() !== 1) {
            $this->invalid('Existe más de un tipo PACIENTE/PACIENTES. Solicite a Sistemas unificar el catálogo; no se puede elegir un tipo de forma automática.');
        }
        $type = $types->first();
        if ((int) $type->estado !== 1) {
            $this->invalid('El tipo de usuario PACIENTE está inactivo. Actívelo en Administrar tipos de usuario antes de registrar al paciente.');
        }
        $systemRoleId = User::roleIdForName(array('SISTEMAS', 'SISTEMA'));
        $doctorRoleId = User::roleIdForName(array('DOCTORES', 'DOCTOR'));
        if ((int) $type->id <= 0 || in_array((int) $type->id, array_filter(array($systemRoleId, $doctorRoleId)), true)) {
            $this->invalid('El tipo PACIENTE utiliza un identificador reservado para Sistemas o Doctor. Solicite a Sistemas corregir el catálogo antes de continuar.');
        }
        return (int) $type->id;
    }

    public function problem()
    {
        try {
            $this->id();
            return null;
        } catch (ValidationException $exception) {
            return $exception->errors()['patient_user_type'][0];
        }
    }

    private function invalid($message)
    {
        throw ValidationException::withMessages(array('patient_user_type' => $message));
    }
}
