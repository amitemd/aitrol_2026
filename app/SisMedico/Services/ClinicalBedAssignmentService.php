<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Cama;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaPacienteLog;
use Sis_medico\CamaTransaccion;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Paciente;

class ClinicalBedAssignmentService
{
    public function assign($patientId, $bedId, $roomTypeId, $observation, array $context)
    {
        if (empty($patientId) || empty($bedId)) {
            return 'DATOS VACIOS POR FAVOR REINGRESE DE NUEVO';
        }

        return DB::transaction(function () use ($patientId, $bedId, $roomTypeId, $observation, $context) {
            // El paciente se bloquea antes que la cama. Así, dos solicitudes
            // simultáneas del mismo paciente se serializan aunque apunten a camas distintas.
            $patient = Paciente::where('id', $patientId)->lockForUpdate()->first();
            if (is_null($patient)) {
                return 'EL PACIENTE SELECCIONADO NO EXISTE.';
            }

            $activeAdmission = Ho_Hospitalizacion::where('id_paciente', $patientId)
                ->where('estado', 1)->lockForUpdate()->first();
            if (!is_null($activeAdmission)) {
                if ((int) $activeAdmission->id_cama === (int) $bedId) {
                    return 'LA ASIGNACIÓN YA HABÍA SIDO PROCESADA CORRECTAMENTE.';
                }

                return 'EL PACIENTE YA TIENE UNA CAMA ACTIVA: ' . $this->activeAssignmentDescription($activeAdmission) .
                    '. LIBERE O TRASLADE ESA CAMA ANTES DE ASIGNAR OTRA.';
            }

            $bed = Cama::where('id', $bedId)->lockForUpdate()->first();
            if (is_null($bed)) {
                return 'LA CAMA SELECCIONADA NO EXISTE.';
            }

            $activeOccupancy = Ho_Hospitalizacion::where('id_cama', $bedId)
                ->where('estado', 1)->lockForUpdate()->first();
            if (!is_null($activeOccupancy)) {
                return 'LA CAMA SELECCIONADA YA ESTA OCUPADA. ELIJA OTRA CAMA LIBRE.';
            }

            $legacyAssignment = CamaPaciente::where('id_paciente', $patientId)->lockForUpdate()->first();
            if (!is_null($legacyAssignment)) {
                if ((int) $legacyAssignment->id_cama === (int) $bedId
                    && (int) $legacyAssignment->estado === 1) {
                    return 'LA ASIGNACIÓN YA HABÍA SIDO PROCESADA CORRECTAMENTE.';
                }

                return 'EL PACIENTE YA SE ENCUENTRA REGISTRADO REINGRESE DE NUEVO';
            }

            $actorId = $context['actor_id'];
            $ip = isset($context['ip']) ? $context['ip'] : null;
            $assignment = array(
                'id_paciente' => $patientId,
                'observacion' => $observation,
                'estado' => '1',
                'id_usuario' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'id_cama' => $bedId,
                'observaciones' => '//SE REGISTRÓ CAMA AL PACIENTE DESDE SISTEMA INICIO//',
            );

            $bed->update(array(
                'estado' => '3',
                'id_usuario' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
            ));

            $actualRoomTypeId = !is_null($bed->habitacion) && !is_null($bed->habitacion->tipo)
                ? (int) $bed->habitacion->tipo->id
                : (int) $roomTypeId;
            $imageId = $actualRoomTypeId === 3 ? '7' : ($actualRoomTypeId === 5 ? '11' : '3');
            CamaTransaccion::where('id_cama', $bedId)->lockForUpdate()->get()->each(function ($transaction) use ($patientId, $imageId) {
                $transaction->update(array('id_paciente' => $patientId, 'id_imagen' => $imageId));
            });

            CamaPaciente::create($assignment);
            CamaPacienteLog::create($assignment);

            return 'EL PACIENTE FUÉ REGISTRADO EN LA CAMA';
        }, 3);
    }

    private function activeAssignmentDescription(Ho_Hospitalizacion $admission)
    {
        $bed = Cama::with(array('habitacion.piso'))->find($admission->id_cama);
        if (is_null($bed)) {
            return 'otra cama activa';
        }

        $parts = array('cama ' . $bed->codigo);
        if (!is_null($bed->habitacion)) {
            $parts[] = 'habitacion ' . $bed->habitacion->codigo;
            if (!is_null($bed->habitacion->piso)) {
                $parts[] = $bed->habitacion->piso->nombre_piso;
            }
        }
        return implode(', ', $parts);
    }
}
