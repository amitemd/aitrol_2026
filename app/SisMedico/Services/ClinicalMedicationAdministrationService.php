<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEmergencyMedicationException;
use Sis_medico\ClinicalMedicationAdministration;
use Sis_medico\ClinicalMedicationOrder;
use Sis_medico\Exceptions\ClinicalMedicationBlockedException;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Medicina;
use Sis_medico\Movimiento;
use Sis_medico\Producto_Medicina;

class ClinicalMedicationAdministrationService
{
    const ADMINISTERED = 'ADMINISTERED';
    const OMITTED = 'OMITTED';

    public function registerLegacyRecipeDetail($detailId, array $data, $actorId, $ipAddress = null)
    {
        return DB::transaction(function () use ($detailId, $data, $actorId, $ipAddress) {
            $detail = hc_receta_detalle::with('hc_receta.historia')->lockForUpdate()->find($detailId);
            $errors = $this->validateRequest($detail, $data);

            if ($errors) {
                throw new ClinicalMedicationBlockedException($errors);
            }

            $history = $detail->hc_receta->historia;
            $encounter = ClinicalEncounter::lockForUpdate()->find($history->encounter_id);
            if (!$encounter || !in_array($encounter->status, array('ACTIVE', 'CLOSURE_PENDING'))) {
                throw new ClinicalMedicationBlockedException(array('La orden no pertenece a un episodio clínico activo.'));
            }
            $this->assertAccess($encounter->id, 'medication.administer', $actorId, $ipAddress);

            $order = ClinicalMedicationOrder::firstOrCreate(
                array('source_type' => 'hc_receta_detalle', 'source_id' => (string) $detail->id),
                array(
                    'encounter_id' => $encounter->id,
                    'patient_id' => $history->id_paciente,
                    'medication_name' => $detail->nombre,
                    'ordered_dose' => $detail->dosis,
                    'status' => 'ACTIVE',
                    'ordered_by' => $detail->hc_receta->id_doctor_examinador,
                    'ordered_at' => $detail->created_at,
                )
            );

            // Serializa la decisión estado + dosis. El índice único es la última
            // barrera, pero el bloqueo evita que dos peticiones lleguen a él a la vez.
            $order = ClinicalMedicationOrder::where('id', $order->id)->lockForUpdate()->first();

            if ($order->status !== 'ACTIVE') {
                throw new ClinicalMedicationBlockedException(array('La orden médica está suspendida, cancelada o finalizada.'));
            }

            $scheduledAt = Carbon::parse($data['scheduled_at']);
            if (ClinicalMedicationAdministration::where('medication_order_id', $order->id)
                ->where('scheduled_at', $scheduledAt->format('Y-m-d H:i:s'))->exists()) {
                throw new ClinicalMedicationBlockedException(array('Esta dosis programada ya fue registrada.'));
            }

            if ($data['status'] === self::ADMINISTERED) {
                $this->validateAndDischargeInventory($detail, $data['series']);
            }

            return ClinicalMedicationAdministration::create(array(
                'encounter_id' => $encounter->id,
                'medication_order_id' => $order->id,
                'patient_id' => $history->id_paciente,
                'medication_name' => $detail->nombre,
                'dose' => trim($data['dose']),
                'route' => trim($data['route']),
                'scheduled_at' => $scheduledAt,
                'administered_at' => Carbon::parse($data['administered_at']),
                'status' => $data['status'],
                'omission_reason' => $data['status'] === self::OMITTED ? trim($data['omission_reason']) : null,
                'clinical_result' => isset($data['clinical_result']) ? trim($data['clinical_result']) : null,
                'inventory_series' => $data['status'] === self::ADMINISTERED ? trim($data['series']) : null,
                'patient_verified' => true,
                'medication_verified' => true,
                'dose_verified' => true,
                'route_verified' => true,
                'time_verified' => true,
                'administered_by' => $actorId,
                'ip_address' => $ipAddress,
            ));
        });
    }

    /**
     * Corrige únicamente la hora real de una administración ya registrada.
     * El movimiento de inventario no se repite: la corrección queda en una
     * bitácora independiente y el Formulario 022 lee la hora rectificada.
     */
    public function correctLegacyAdministrationTime(
        $administrationId,
        $administeredAt,
        $reason,
        $actorId,
        $ipAddress,
        $companyId
    ) {
        return DB::transaction(function () use (
            $administrationId, $administeredAt, $reason, $actorId, $ipAddress, $companyId
        ) {
            $administration = ClinicalMedicationAdministration::lockForUpdate()->find($administrationId);
            if (!$administration || $administration->status !== self::ADMINISTERED) {
                throw new ClinicalMedicationBlockedException(array('La administración no existe o no corresponde a una dosis administrada.'));
            }

            $order = ClinicalMedicationOrder::lockForUpdate()->find($administration->medication_order_id);
            if (!$order || $order->source_type !== 'hc_receta_detalle') {
                throw new ClinicalMedicationBlockedException(array('Solo se puede corregir la hora de una administración asociada a una receta médica.'));
            }

            $detail = hc_receta_detalle::with('hc_receta.historia')->find($order->source_id);
            if (!$detail || !$detail->hc_receta || !$detail->hc_receta->historia) {
                throw new ClinicalMedicationBlockedException(array('No se encontró la receta clínica de esta administración.'));
            }
            $history = $detail->hc_receta->historia;
            if ((string) $history->encounter_id !== (string) $administration->encounter_id
                || (string) $history->id_paciente !== (string) $administration->patient_id) {
                throw new ClinicalMedicationBlockedException(array('La administración no pertenece al episodio clínico de la receta.'));
            }
            if (!DB::table('agenda')->where('id', $history->id_agenda)
                ->where('id_empresa', $companyId)->exists()) {
                throw new ClinicalMedicationBlockedException(array('La administración no pertenece a la empresa activa.'));
            }

            $this->assertAccess($administration->encounter_id, 'medication.administer', $actorId, $ipAddress);
            $reason = trim((string) $reason);
            if (mb_strlen($reason) < 10) {
                throw new ClinicalMedicationBlockedException(array('Documente el motivo de la corrección con al menos 10 caracteres.'));
            }
            if (empty($administeredAt)) {
                throw new ClinicalMedicationBlockedException(array('Seleccione la nueva hora real de entrega.'));
            }
            try {
                $newTime = Carbon::parse($administeredAt);
            } catch (\Exception $exception) {
                throw new ClinicalMedicationBlockedException(array('La nueva hora de entrega no tiene un formato válido.'));
            }
            if ($newTime->gt(Carbon::now()->addMinutes(5))) {
                throw new ClinicalMedicationBlockedException(array('La hora real de entrega no puede estar en el futuro.'));
            }
            if (!Schema::hasTable('clinical_medication_administration_corrections')) {
                throw new ClinicalMedicationBlockedException(array('Aplique las migraciones pendientes antes de corregir la hora de entrega.'));
            }

            $previousTime = Carbon::parse($administration->administered_at);
            if ($previousTime->equalTo($newTime)) return $administration;

            DB::table('clinical_medication_administration_corrections')->insert(array(
                'administration_id' => $administration->id,
                'previous_administered_at' => $previousTime->format('Y-m-d H:i:s'),
                'new_administered_at' => $newTime->format('Y-m-d H:i:s'),
                'reason' => $reason,
                'corrected_by' => $actorId,
                'ip_address' => $ipAddress,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));

            $administration->administered_at = $newTime;
            $administration->save();
            return $administration->fresh();
        });
    }

    public function registerEmergencyException($solicitudId, array $data, $actorId, $ipAddress = null)
    {
        return DB::transaction(function () use ($solicitudId, $data, $actorId, $ipAddress) {
            $solicitud = Ho_Solicitud::lockForUpdate()->find($solicitudId);
            $errors = $this->validateEmergencyException($solicitud, $data);
            if ($errors) throw new ClinicalMedicationBlockedException($errors);

            $encounter = ClinicalEncounter::lockForUpdate()->find($solicitud->encounter_id);
            if (!$encounter || $encounter->status !== 'ACTIVE') {
                throw new ClinicalMedicationBlockedException(array('La excepción requiere un episodio de emergencia activo.'));
            }
            $this->assertAccess($encounter->id, 'medication.emergency', $actorId, $ipAddress);

            $administeredAt = Carbon::parse($data['administered_at']);
            $medicine = Medicina::lockForUpdate()->find($data['medication_id']);
            if (!$medicine) throw new ClinicalMedicationBlockedException(array('Seleccione un medicamento válido.'));
            $movement = $this->validateInventory($medicine->id, $data['series'], true);
            $sourceId = $this->emergencyIdempotencyKey($solicitud->id, $medicine->id, $administeredAt, $data);

            // Ho_Solicitud ya esta bloqueada: los reintentos del mismo evento se
            // resuelven aqui antes de crear una segunda orden/administracion.
            $existingOrder = ClinicalMedicationOrder::where('source_type', 'EMERGENCY_PROTOCOL')
                ->where('source_id', $sourceId)->lockForUpdate()->first();
            if ($existingOrder) {
                $existingException = ClinicalEmergencyMedicationException::where('provisional_order_id', $existingOrder->id)->first();
                $existingAdministration = ClinicalMedicationAdministration::where('medication_order_id', $existingOrder->id)->first();
                if (!$existingException || !$existingAdministration) {
                    throw new ClinicalMedicationBlockedException(array('El reintento encontró un registro de emergencia incompleto.'));
                }
                if (!$this->sameEmergencyPayload($existingException, $existingAdministration, $data)) {
                    throw new ClinicalMedicationBlockedException(array('La misma administración de emergencia ya fue registrada con información diferente.'));
                }
                return array('exception' => $existingException, 'administration' => $existingAdministration, 'movement' => $movement);
            }

            $provisionalOrder = ClinicalMedicationOrder::create(array(
                'encounter_id' => $encounter->id,
                'patient_id' => $solicitud->id_paciente,
                'source_type' => 'EMERGENCY_PROTOCOL',
                'source_id' => $sourceId,
                'medication_name' => $medicine->nombre,
                'ordered_dose' => trim($data['dose']),
                'ordered_route' => trim($data['route']),
                'status' => 'REGULARIZATION_PENDING',
                'ordered_by' => null,
                'ordered_at' => $administeredAt,
            ));

            $administration = ClinicalMedicationAdministration::create(array(
                'encounter_id' => $encounter->id,
                'medication_order_id' => $provisionalOrder->id,
                'patient_id' => $solicitud->id_paciente,
                'medication_name' => $medicine->nombre,
                'dose' => trim($data['dose']),
                'route' => trim($data['route']),
                'scheduled_at' => $administeredAt,
                'administered_at' => $administeredAt,
                'status' => self::ADMINISTERED,
                'clinical_result' => isset($data['clinical_result']) ? trim($data['clinical_result']) : null,
                'inventory_series' => trim($data['series']),
                'patient_verified' => true,
                'medication_verified' => true,
                'dose_verified' => true,
                'route_verified' => true,
                'time_verified' => true,
                'administered_by' => $actorId,
                'ip_address' => $ipAddress,
            ));

            $exception = ClinicalEmergencyMedicationException::create(array(
                'encounter_id' => $encounter->id,
                'solicitud_id' => $solicitud->id,
                'patient_id' => $solicitud->id_paciente,
                'medication_id' => $medicine->id,
                'provisional_order_id' => $provisionalOrder->id,
                'administration_id' => $administration->id,
                'protocol_code' => trim($data['protocol_code']),
                'justification' => trim($data['justification']),
                'status' => 'PENDING',
                'regularization_due_at' => $administeredAt->copy()->addMinutes(60),
                'created_by' => $actorId,
                'ip_address' => $ipAddress,
            ));

            return array('exception' => $exception, 'administration' => $administration, 'movement' => $movement);
        });
    }

    public function regularizeEmergencyException($exceptionId, $recipeDetailId, $actorId)
    {
        return DB::transaction(function () use ($exceptionId, $recipeDetailId, $actorId) {
            $exception = ClinicalEmergencyMedicationException::lockForUpdate()->find($exceptionId);
            if (!$exception || $exception->status !== 'PENDING') {
                throw new ClinicalMedicationBlockedException(array('La excepción no existe o ya fue regularizada.'));
            }
            $this->assertAccess($exception->encounter_id, 'medication.regularize', $actorId, null);

            $detail = hc_receta_detalle::with('hc_receta.historia')->lockForUpdate()->find($recipeDetailId);
            if (!$detail || !$detail->hc_receta || !$detail->hc_receta->historia) {
                throw new ClinicalMedicationBlockedException(array('Seleccione una orden médica válida para regularizar.'));
            }
            if ((string) $detail->hc_receta->historia->encounter_id !== (string) $exception->encounter_id) {
                throw new ClinicalMedicationBlockedException(array('La orden seleccionada pertenece a otro episodio clínico.'));
            }
            if ((string) $detail->id_medicina !== (string) $exception->medication_id) {
                throw new ClinicalMedicationBlockedException(array('La orden seleccionada no corresponde al medicamento administrado.'));
            }
            if (empty($detail->hc_receta->id_doctor_examinador)) {
                throw new ClinicalMedicationBlockedException(array('La regularización requiere una orden emitida por un médico identificado.'));
            }

            $order = $this->findOrCreateOrder($detail, $detail->hc_receta->historia);
            $provisionalOrder = ClinicalMedicationOrder::find($exception->provisional_order_id);
            if ($provisionalOrder) {
                $provisionalOrder->status = 'REGULARIZED';
                $provisionalOrder->save();
            }

            $exception->regularized_order_id = $order->id;
            $exception->regularized_at = Carbon::now();
            $exception->regularized_by = $actorId;
            $exception->status = 'REGULARIZED';
            $exception->save();
            return $exception;
        });
    }

    private function validateRequest($detail, array $data)
    {
        $errors = array();
        if (!$detail || !$detail->hc_receta || !$detail->hc_receta->historia) {
            $errors[] = 'No existe una orden médica válida para administrar este medicamento.';
            return $errors;
        }
        if (empty($detail->hc_receta->historia->encounter_id)) $errors[] = 'La orden no está vinculada a un episodio clínico.';
        if (empty($data['patient_verified'])) $errors[] = 'Debe confirmar la identidad del paciente.';
        if (empty($data['medication_verified'])) $errors[] = 'Debe confirmar el medicamento indicado.';
        if (empty($data['dose'])) $errors[] = 'La dosis administrada es obligatoria.';
        if (empty($data['route'])) $errors[] = 'La vía de administración es obligatoria.';
        if (empty($data['scheduled_at'])) $errors[] = 'La hora programada es obligatoria.';
        if (empty($data['administered_at'])) $errors[] = 'La hora real es obligatoria.';
        if (!in_array(isset($data['status']) ? $data['status'] : null, array(self::ADMINISTERED, self::OMITTED))) {
            $errors[] = 'Seleccione si la dosis fue administrada u omitida.';
        }
        if (isset($data['status']) && $data['status'] === self::ADMINISTERED && empty($data['series'])) {
            $errors[] = 'La serie del medicamento es obligatoria para registrar la administración.';
        }
        if (isset($data['status']) && $data['status'] === self::ADMINISTERED
            && empty(trim(isset($data['clinical_result']) ? $data['clinical_result'] : ''))) {
            $errors[] = 'Registre el resultado clínico posterior a la administración.';
        }
        if (isset($data['status']) && $data['status'] === self::OMITTED && empty(trim(isset($data['omission_reason']) ? $data['omission_reason'] : ''))) {
            $errors[] = 'Debe indicar el motivo de la omisión.';
        }
        try {
            if (!empty($data['scheduled_at'])) Carbon::parse($data['scheduled_at']);
            if (!empty($data['administered_at']) && Carbon::parse($data['administered_at'])->gt(Carbon::now()->addMinutes(5))) {
                $errors[] = 'La hora real no puede estar en el futuro.';
            }
        } catch (\Exception $exception) {
            $errors[] = 'Las fechas de administración no tienen un formato válido.';
        }
        return $errors;
    }

    private function validateEmergencyException($solicitud, array $data)
    {
        $errors = array();
        if (!$solicitud || empty($solicitud->encounter_id)) $errors[] = 'La emergencia no está vinculada a un episodio clínico.';
        if (empty($data['patient_verified'])) $errors[] = 'Debe confirmar la identidad del paciente.';
        if (empty($data['medication_verified'])) $errors[] = 'Debe confirmar el medicamento del protocolo.';
        if (empty($data['medication_id']) || !Medicina::where('id', $data['medication_id'])->exists()) $errors[] = 'Seleccione un medicamento válido.';
        if (empty($data['dose'])) $errors[] = 'La dosis administrada es obligatoria.';
        if (empty($data['route'])) $errors[] = 'La vía de administración es obligatoria.';
        if (empty($data['series'])) $errors[] = 'La serie del medicamento es obligatoria.';
        if (empty($data['protocol_code']) || strlen(trim($data['protocol_code'])) < 3) $errors[] = 'Identifique el protocolo de emergencia aplicado.';
        if (empty($data['justification']) || strlen(trim($data['justification'])) < 10) $errors[] = 'Documente la justificación clínica de la excepción.';
        if (empty(trim(isset($data['clinical_result']) ? $data['clinical_result'] : ''))) $errors[] = 'Registre el resultado clínico posterior a la administración.';
        if (empty($data['administered_at'])) {
            $errors[] = 'La hora real de administración es obligatoria.';
        } else {
            try {
                if (Carbon::parse($data['administered_at'])->gt(Carbon::now()->addMinutes(5))) $errors[] = 'La hora real no puede estar en el futuro.';
            } catch (\Exception $exception) {
                $errors[] = 'La hora real no tiene un formato válido.';
            }
        }
        return $errors;
    }

    private function validateAndDischargeInventory($detail, $series)
    {
        $movement = $this->validateInventory($detail->id_medicina, $series);
        $detail->update(array('id_producto' => $movement->id_producto, 'descargo' => '1', 'serie' => $series));
    }

    private function validateInventory($medicationId, $series, $lock = true)
    {
        $productQuery = Producto_Medicina::where('id_medicina', $medicationId);
        $movementQuery = Movimiento::where('serie', $series);
        if ($lock) {
            $productQuery->lockForUpdate();
            $movementQuery->lockForUpdate();
        }
        $product = $productQuery->first();
        $movement = $movementQuery->first();
        if (!$product || !$movement) throw new ClinicalMedicationBlockedException(array('No se encontró la serie o su relación con el medicamento.'));
        if ((string) $movement->id_producto !== (string) $product->id_producto) {
            throw new ClinicalMedicationBlockedException(array('La serie no corresponde al medicamento indicado.'));
        }
        return $movement;
    }

    private function findOrCreateOrder($detail, $history)
    {
        return ClinicalMedicationOrder::firstOrCreate(
            array('source_type' => 'hc_receta_detalle', 'source_id' => (string) $detail->id),
            array(
                'encounter_id' => $history->encounter_id,
                'patient_id' => $history->id_paciente,
                'medication_name' => $detail->nombre,
                'ordered_dose' => $detail->dosis,
                'status' => 'ACTIVE',
                'ordered_by' => $detail->hc_receta->id_doctor_examinador,
                'ordered_at' => $detail->created_at,
            )
        );
    }

    private function emergencyIdempotencyKey($solicitudId, $medicationId, Carbon $administeredAt, array $data)
    {
        $identity = implode('|', array(
            (string) $solicitudId,
            (string) $medicationId,
            $administeredAt->format('Y-m-d H:i:s'),
            trim((string) $data['series']),
            trim((string) $data['dose']),
            strtoupper(trim((string) $data['route'])),
        ));
        return 'EMR-'.hash('sha256', $identity);
    }

    private function sameEmergencyPayload($exception, $administration, array $data)
    {
        return trim((string) $exception->protocol_code) === trim((string) $data['protocol_code'])
            && trim((string) $exception->justification) === trim((string) $data['justification'])
            && trim((string) $administration->dose) === trim((string) $data['dose'])
            && strtoupper(trim((string) $administration->route)) === strtoupper(trim((string) $data['route']))
            && trim((string) $administration->inventory_series) === trim((string) $data['series'])
            && trim((string) $administration->clinical_result) === trim((string) $data['clinical_result']);
    }

    private function assertAccess($encounterId, $action, $actorId, $ipAddress)
    {
        try {
            app(ClinicalAccessControlService::class)->assertAllowed($encounterId, $action, array(
                'user' => \Sis_medico\User::find($actorId),
                'ip' => $ipAddress,
            ));
        } catch (ClinicalAccessDeniedException $exception) {
            throw new ClinicalMedicationBlockedException(array($exception->getMessage()));
        }
    }
}
