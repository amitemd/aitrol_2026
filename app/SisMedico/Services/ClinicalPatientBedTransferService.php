<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Cama;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaPacienteLog;
use Sis_medico\CamaTransaccion;
use Sis_medico\ClinicalBedTransfer;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Traspaso_Sala008;

class ClinicalPatientBedTransferService
{
    public function transfer($hospitalizationId, $destinationBedId, $reason, array $context)
    {
        if (!Schema::hasTable('clinical_bed_transfers')) {
            throw new \RuntimeException('Falta aplicar la migración de trazabilidad de traslados de cama.');
        }

        $admission = Ho_Hospitalizacion::find($hospitalizationId);
        $this->authorizeHospitalization($admission, 'bed.transfer', $context);

        $reference = Ho_Traspaso_Sala008::findOrFail($admission->id_traspaso);
        return DB::transaction(function () use ($hospitalizationId, $destinationBedId, $reason, $context, $reference) {
            $solicitud = \Sis_medico\Ho_Solicitud::where('id', $reference->id_solicitud)->lockForUpdate()->firstOrFail();
            app(ClinicalHospitalTransferService::class)->authorize($solicitud, 'bed.transfer', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']);
            $admission = Ho_Hospitalizacion::where('id', $hospitalizationId)->first();
            if (is_null($admission) || (int) $admission->estado !== 1) {
                throw new \RuntimeException('El ingreso ya no está activo. Actualice el mapa de camas.');
            }

            $beds = Cama::with('habitacion.tipo')->whereIn('id', [$admission->id_cama, $destinationBedId])
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $sourceBed = $beds->get($admission->id_cama);
            $destinationBed = $beds->get($destinationBedId);
            $lockedAdmission = Ho_Hospitalizacion::where('id', $hospitalizationId)->lockForUpdate()->first();
            if (!$lockedAdmission || (int) $lockedAdmission->estado !== 1
                || (int) $lockedAdmission->id_cama !== (int) $admission->id_cama
                || (int) $lockedAdmission->id_traspaso !== (int) $reference->id) {
                throw new \RuntimeException('El ingreso cambio durante el traslado. Actualice el mapa.');
            }
            $admission = $lockedAdmission;
            if (is_null($sourceBed) || is_null($destinationBed) || is_null($destinationBed->habitacion)) {
                throw new \RuntimeException('No fue posible validar la cama de origen o destino.');
            }
            if ((int) $sourceBed->id === (int) $destinationBed->id) {
                $completedTransfer = ClinicalBedTransfer::where('hospitalization_id', $admission->id)
                    ->where('to_bed_id', $destinationBed->id)
                    ->where('reason', $reason)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();
                if (!is_null($completedTransfer)) {
                    return array(
                        'hospitalization_id' => $admission->id,
                        'bed_id' => $destinationBed->id,
                        'idempotent' => true,
                        'message' => 'El traslado ya había sido procesado. No se generaron movimientos duplicados.',
                    );
                }
                throw new \RuntimeException('Seleccione una cama diferente a la actual.');
            }
            app(ClinicalHospitalDestinationService::class)->assertBed($destinationBed, $reference->id_sala);
            if (Ho_Hospitalizacion::where('id_cama', $sourceBed->id)->where('estado', 1)
                ->where('id', '<>', $admission->id)->exists()) {
                throw new \RuntimeException('La cama de origen contiene otra ocupacion activa.');
            }
            $sourceTransaction = CamaTransaccion::where('id_cama', $sourceBed->id)->orderByDesc('id')->lockForUpdate()->first();
            if (!$sourceTransaction || (string) $sourceTransaction->id_paciente !== (string) $admission->id_paciente) {
                throw new \RuntimeException('La ocupacion actual no coincide con el ingreso hospitalario.');
            }
            if ((int) $destinationBed->habitacion->estado === 4) {
                throw new \RuntimeException('La habitación destino no está disponible.');
            }

            $occupancy = Ho_Hospitalizacion::where('id_cama', $destinationBed->id)
                ->where('estado', 1)->lockForUpdate()->first();
            if (!is_null($occupancy)) {
                throw new \RuntimeException('La cama destino acaba de ser ocupada. Seleccione otra cama libre.');
            }

            $destinationTransaction = CamaTransaccion::where('id_cama', $destinationBed->id)->orderByDesc('id')->lockForUpdate()->first();
            if (!$destinationTransaction || $destinationTransaction->estado_cama !== 'disponible' || $destinationTransaction->id_paciente) {
                throw new \RuntimeException('Confirme la disponibilidad y la limpieza de la cama destino.');
            }
            if (!is_null($destinationTransaction)) {
                $state = strtolower(trim((string) $destinationTransaction->estado_cama));
                if (strpos($state, 'ocup') !== false || strpos($state, 'prepar') !== false
                    || strpos($state, 'bloq') !== false || strpos($state, 'no_dispon') !== false) {
                    throw new \RuntimeException('La cama destino no se encuentra libre para recibir al paciente.');
                }
            }
            if (in_array((int) $destinationBed->estado, array(2, 3, 4))) {
                throw new \RuntimeException('La cama destino no se encuentra habilitada como libre.');
            }

            $actorId = isset($context['actor_id']) ? $context['actor_id'] : null;
            $ip = isset($context['ip']) ? $context['ip'] : null;
            $now = date('Y-m-d H:i:s');
            $fromRoomId = $admission->id_habitacion;

            $this->setBedState($sourceBed, 'preparacion', null, $actorId, $ip);
            $this->setBedState($destinationBed, 'ocupada', $admission->id_paciente, $actorId, $ip);

            $admission->id_cama = $destinationBed->id;
            $admission->id_habitacion = $destinationBed->id_habitacion;
            $admission->id_usuariomod = $actorId;
            $admission->ip_modificacion = $ip;
            $admission->save();

            $activeLegacy = CamaPaciente::where('id_paciente', $admission->id_paciente)->lockForUpdate()->first();
            if (!is_null($activeLegacy)) {
                $activeLegacy->id_cama = $destinationBed->id;
                $activeLegacy->observacion = substr($reason, 0, 100);
                $activeLegacy->estado = 1;
                $activeLegacy->id_usuario = $actorId;
                $activeLegacy->ip_modificacion = $ip;
                $activeLegacy->observaciones = '//TRASLADO INTERNO DE HABITACION Y CAMA//';
                $activeLegacy->save();
            } else {
                CamaPaciente::create(array(
                    'id_paciente' => $admission->id_paciente,
                    'id_cama' => $destinationBed->id,
                    'observacion' => substr($reason, 0, 100),
                    'estado' => 1,
                    'id_usuario' => $actorId,
                    'id_usuariocrea' => $actorId,
                    'ip_modificacion' => $ip,
                    'observaciones' => '//TRASLADO INTERNO DE HABITACION Y CAMA//',
                    'fecha_ingreso' => $now,
                ));
            }

            CamaPacienteLog::create(array(
                'id_paciente' => $admission->id_paciente,
                'id_cama' => $destinationBed->id,
                'observacion' => substr($reason, 0, 100),
                'estado' => 1,
                'id_usuario' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'observaciones' => '//TRASLADO DESDE CAMA ' . $sourceBed->codigo . '//',
            ));

            ClinicalBedTransfer::create(array(
                'hospitalization_id' => $admission->id,
                'patient_id' => $admission->id_paciente,
                'from_room_id' => $fromRoomId,
                'from_bed_id' => $sourceBed->id,
                'to_room_id' => $destinationBed->id_habitacion,
                'to_bed_id' => $destinationBed->id,
                'reason' => $reason,
                'actor_id' => $actorId,
                'ip_address' => $ip,
                'transferred_at' => $now,
            ));

            return array(
                'hospitalization_id' => $admission->id,
                'bed_id' => $destinationBed->id,
                'idempotent' => false,
                'message' => 'Paciente trasladado a la habitación ' . $destinationBed->habitacion->codigo
                    . ', cama ' . $destinationBed->codigo . '. La cama anterior quedó en preparación.',
            );
        }, 3);
    }

    /**
     * Authorize a bed operation against the canonical clinical encounter.
     * The decision is recorded before any occupancy data is changed.
     */
    public function authorizeHospitalization($admission, $action, array $context)
    {
        if (is_null($admission)) {
            throw new \RuntimeException('No se encontró el ingreso hospitalario activo.');
        }

        if (!Schema::hasColumn('ho_solicitud', 'encounter_id')) {
            throw new \Sis_medico\Exceptions\ClinicalAccessDeniedException(
                'Falta aplicar la migración que vincula hospitalización con el episodio clínico.'
            );
        }

        $transfer = Ho_Traspaso_Sala008::find($admission->id_traspaso);
        $encounterId = !is_null($transfer)
            ? DB::table('ho_solicitud')->where('id', $transfer->id_solicitud)->value('encounter_id')
            : null;

        if (empty($encounterId)) {
            throw new \Sis_medico\Exceptions\ClinicalAccessDeniedException(
                'El ingreso no está vinculado a un episodio clínico y no puede modificarse de forma segura.'
            );
        }

        $context['service_code'] = isset($context['service_code'])
            ? $context['service_code'] : 'HOSPITALIZATION_BEDS';
        app(ClinicalAccessControlService::class)->assertAllowed($encounterId, $action, $context);

        return $encounterId;
    }

    private function setBedState(Cama $bed, $state, $patientId, $actorId, $ip)
    {
        $numericState = $state === 'ocupada' ? 3 : 2;
        $roomType = !is_null($bed->habitacion) && !is_null($bed->habitacion->tipo)
            ? (int) $bed->habitacion->tipo->id : 0;
        $image = $state === 'ocupada' ? 3 : 2;
        if ($roomType === 3) $image = $state === 'ocupada' ? 7 : 6;
        if ($roomType === 5) $image = $state === 'ocupada' ? 11 : 10;

        $bed->estado = $numericState;
        $bed->id_usuariomod = $actorId;
        $bed->ip_modificacion = $ip;
        $bed->save();

        $transaction = CamaTransaccion::where('id_cama', $bed->id)->orderByDesc('id')->lockForUpdate()->first();
        $values = array(
            'estado_cama' => $state,
            'id_imagen' => $image,
            'id_paciente' => $patientId,
            'id_usuariomod' => $actorId,
            'ip_modificacion' => $ip,
        );
        if (is_null($transaction)) {
            $values['id_cama'] = $bed->id;
            $values['id_usuario'] = $actorId;
            $values['id_usuariocrea'] = $actorId;
            $values['ip_creacion'] = $ip;
            $values['fecha_ingreso'] = $state === 'ocupada' ? date('Y-m-d H:i:s') : null;
            CamaTransaccion::create($values);
        } else {
            if ($state === 'ocupada') $values['fecha_ingreso'] = date('Y-m-d H:i:s');
            $transaction->update($values);
        }
    }
}
