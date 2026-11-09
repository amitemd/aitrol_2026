<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Agenda;
use Sis_medico\Cama;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaTransaccion;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Log_Agenda;

class ClinicalHospitalTransferService
{
    public function authorize(Ho_Solicitud $solicitud, $action, array $roles, $open = true)
    {
        $user = Auth::user();
        abort_unless($user && (int) $user->estado === 1
            && ($user->isSystemUser() || $user->hasAnyUserTypeName($roles)),
            403, 'No tiene permiso para esta accion hospitalaria.');
        $companyId = app(ClinicalHospitalDestinationService::class)->companyId();
        $encounter = $solicitud->encounter_id ? ClinicalEncounter::find($solicitud->encounter_id) : null;
        abort_unless($encounter && (string) $encounter->id_empresa === $companyId
            && (string) $encounter->id_paciente === (string) $solicitud->id_paciente, 403,
            'El episodio no pertenece a la empresa activa.');
        app(ClinicalAccessControlService::class)->assertAllowed($encounter->id, $action,
            ['user' => $user, 'ip' => request()->ip()]);
        if ($open && ((int) $solicitud->estado !== 1 || $solicitud->fecha_salida || $encounter->ended_at
            || $encounter->status !== 'ACTIVE'
            || !in_array((int) $solicitud->estado_paso, [1, 2, 3, 5, 6, 7], true)
            || $encounter->clinical_state === ClinicalStateTransitionService::CLOSURE_PENDING)) {
            throw new \InvalidArgumentException('El episodio esta cerrado o tiene un alta medica pendiente de salida.');
        }
        return $encounter;
    }

    public function requestTransfer($solicitudId, $step, $salaId, $reason)
    {
        if (!in_array((int) $step, [3, 5, 6, 7], true) || mb_strlen(trim((string) $reason)) < 10) {
            throw new \InvalidArgumentException('Seleccione el destino y documente el motivo clinico del traslado.');
        }
        return DB::transaction(function () use ($solicitudId, $step, $salaId, $reason) {
            $solicitud = Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->firstOrFail();
            $encounter = $this->authorize($solicitud, 'hospital.transfer.request', ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
            if (in_array((int) $solicitud->estado_paso, [1, 2], true)) {
                throw new \InvalidArgumentException('Registre la decision desde la atencion de Emergencia.');
            }
            if ((int) $solicitud->estado_paso === (int) $step) {
                throw new \InvalidArgumentException('El paciente ya esta en esa area. Use el traslado de cama para cambiar de habitacion.');
            }
            $sala = app(ClinicalHospitalDestinationService::class)->assertSala($salaId, $step);
            if ((int) $sala->id_hospital !== (int) $encounter->id_hospital) {
                throw new \InvalidArgumentException('Para otro hospital debe registrar una referencia externa.');
            }
            $pending = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('estado', 1)->lockForUpdate()->first();
            if ($pending) {
                if ((int) $pending->paso === (int) $step && (int) $pending->id_sala === (int) $salaId) return $pending;
                throw new \InvalidArgumentException('Existe otro traslado pendiente para este episodio.');
            }
            if ((int) $solicitud->estado_paso === 5) {
                app(HospitalDocumentService::class)->assertReadyForTransfer($solicitud);
            }
            return Ho_Traspaso_Sala008::create([
                'id_solicitud' => $solicitud->id, 'id_paciente' => $solicitud->id_paciente,
                'id_sala' => $salaId, 'paso' => $step, 'seccion' => 4, 'fecha' => date('Y-m-d H:i:s'),
                'id_doctor' => Auth::id(), 'observaciones' => trim($reason), 'estado' => 1,
                'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip(),
            ]);
        }, 3);
    }

    public function receive($transferId, $patientId, $bedId = null, $note = '')
    {
        $reference = Ho_Traspaso_Sala008::findOrFail($transferId);
        return DB::transaction(function () use ($reference, $patientId, $bedId, $note) {
            $solicitud = Ho_Solicitud::where('id', $reference->id_solicitud)->lockForUpdate()->firstOrFail();
            $transfer = Ho_Traspaso_Sala008::where('id', $reference->id)->lockForUpdate()->firstOrFail();
            $encounter = $this->authorize($solicitud, 'hospital.transfer.receive', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA'], false);
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->firstOrFail();
            if ((string) $patientId !== (string) $solicitud->id_paciente
                || (string) $transfer->id_paciente !== (string) $patientId) {
                throw new \InvalidArgumentException('El paciente no corresponde al traslado.');
            }
            $step = (int) $transfer->paso;
            if (!in_array($step, [3, 5, 6, 7], true)) throw new \InvalidArgumentException('El destino no admite esta recepcion.');
            if ((int) $transfer->estado === 2) {
                $previous = Ho_Hospitalizacion::where('id_traspaso', $transfer->id)->orderByDesc('id')->first();
                if (($step === 5 && !$bedId) || ($previous && (int) $previous->id_cama === (int) $bedId)) {
                    return ['idempotent' => true, 'solicitud_id' => $solicitud->id];
                }
                throw new \InvalidArgumentException('El traslado ya fue recibido en otra ubicacion.');
            }
            $this->authorize($solicitud, 'hospital.transfer.receive', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']);
            if ((int) $transfer->estado !== 1) throw new \InvalidArgumentException('La solicitud de traslado ya no esta pendiente.');
            if ((int) $solicitud->estado_paso === 5) {
                app(HospitalDocumentService::class)->assertReadyForTransfer($solicitud);
            }
            
            $destinations = app(ClinicalHospitalDestinationService::class);
            $sala = $destinations->assertSala($transfer->id_sala, $step);
            if ((int) $sala->id_hospital !== (int) $encounter->id_hospital) {
                throw new \InvalidArgumentException('El destino no pertenece al hospital del episodio.');
            }
            // Cirugia se recibe en una sala/quirofano; la cama se exige al pasar a hospitalizacion o cuidados intensivos.
            if ($step !== 5 && !$bedId) {
                throw new \InvalidArgumentException('Confirme una cama para la hospitalizacion o cuidados intensivos.');
            }

            \Sis_medico\Paciente::where('id', $patientId)->lockForUpdate()->firstOrFail();
            $stays = Ho_Hospitalizacion::where('id_paciente', $patientId)->where('estado', 1)->orderBy('id')->get();
            $ownTransfers = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->pluck('id');
            foreach ($stays as $stay) {
                if (!$ownTransfers->contains($stay->id_traspaso)) throw new \InvalidArgumentException('El paciente tiene una cama activa en otro episodio.');
            }
            $bedIds = $stays->pluck('id_cama')->all();
            if ($bedId) $bedIds[] = $bedId;
            $beds = Cama::whereIn('id', array_unique($bedIds))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $lockedStays = Ho_Hospitalizacion::where('id_paciente', $patientId)->where('estado', 1)->orderBy('id')->lockForUpdate()->get();
            if ($lockedStays->map->only(['id', 'id_cama', 'id_traspaso'])->all() !== $stays->map->only(['id', 'id_cama', 'id_traspaso'])->all()) {
                throw new \InvalidArgumentException('La ocupacion cambio durante la recepcion. Actualice y vuelva a intentar.');
            }
            $stays = $lockedStays;
            foreach ($stays as $stay) {
                if (Ho_Hospitalizacion::where('id_cama', $stay->id_cama)->where('estado', 1)
                    ->whereNotIn('id', $stays->pluck('id'))->exists()) {
                    throw new \InvalidArgumentException('La cama de origen contiene otra ocupacion activa que debe conciliarse.');
                }
                $sourceBed = $beds->get($stay->id_cama);
                $sourceTransaction = CamaTransaccion::where('id_cama', $stay->id_cama)
                    ->orderByDesc('id')->lockForUpdate()->first();
                if (!$sourceBed || !$sourceTransaction
                    || (string) $sourceTransaction->id_paciente !== (string) $patientId
                    || $sourceTransaction->estado_cama !== 'ocupada' || (int) $sourceBed->estado !== 3) {
                    throw new \InvalidArgumentException('La cama reservada de origen no coincide con la ocupacion del paciente.');
                }
            }
            $bed = $bedId ? $beds->get($bedId) : null;
            if ($bedId && !$bed) throw new \InvalidArgumentException('No se encontro la cama seleccionada.');
            $bedHistoryWarning = false;
            if ($bed) {
                $destinations->assertBed($bed, $sala->id);
                if (Ho_Hospitalizacion::where('id_cama', $bedId)->where('estado', 1)
                    ->where('id_paciente', '<>', $patientId)->exists()) {
                    throw new \InvalidArgumentException('La cama esta ocupada por otro paciente.');
                }
                // Los registros anteriores son historial; solo el estado vigente decide la disponibilidad.
                $transactions = CamaTransaccion::where('id_cama', $bedId)
                    ->orderByDesc('id')->limit(2)->lockForUpdate()->get();
                $transaction = $transactions->first();
                $bedHistoryWarning = $transactions->count() > 1;
                $sameStay = $stays->contains('id_cama', $bedId);
                if ($sameStay && (!$transaction || (string) $transaction->id_paciente !== (string) $patientId
                    || $transaction->estado_cama !== 'ocupada' || (int) $bed->estado !== 3)) {
                    throw new \InvalidArgumentException('La ocupacion de la cama actual no coincide con el episodio.');
                }
                if (!$sameStay && (!$transaction || $transaction->estado_cama !== 'disponible'
                    || !empty($transaction->id_paciente) || (int) $bed->estado !== 1)) {
                    throw new \InvalidArgumentException('La cama debe estar disponible y con la limpieza confirmada.');
                }
            }

            $context = ['actor_id' => Auth::id(), 'ip' => request()->ip(), 'reason' => $transfer->observaciones];
            $reconciliation = app(ClinicalTransferReconciliationService::class)->prepare(
                $solicitud->id, $step, ClinicalTransferReconciliationService::PRESERVE, $context
            );
            $now = date('Y-m-d H:i:s');
            $fromEmergency = (int) $solicitud->estado_paso === 2;
            foreach ($stays as $stay) {
                if ($step === 5) continue;
                if ((int) $stay->id_cama !== (int) $bedId) $this->releaseToCleaning($beds->get($stay->id_cama), $patientId, $context);
                $stay->update(['estado' => 2, 'fecha_salida' => $now,
                    'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
            }
            $previousTransfers = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('estado', 2);
            if ($step === 5) $previousTransfers->whereNotIn('id', $stays->pluck('id_traspaso'));
            $previousTransfers->update(['estado' => 3, 'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
            $now = date('Y-m-d H:i:s');
            Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->whereNull('fecha_salida')
                ->update(['fecha_salida' => $now, 'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
            $receptionAgenda = $fromEmergency
                ? $this->createReceptionAgenda($solicitud, $transfer, $encounter, $now)
                : null;
            $solicitud->update(['estado_paso' => $step, 'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
            $log = Ho_Log_Solicitud::create(['id_ho_solicitud' => $solicitud->id, 'estado_paso' => $step,
                'id_agenda' => $receptionAgenda ? $receptionAgenda->id : null,
                'fecha_ingreso' => $now, 'fecha_salida' => null,
                'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip()]);
            app(ClinicalEncounterService::class)->link($encounter, ['ho_log_solicitud' => $log->id]);
            $encounter->care_setting = ClinicalEncounterService::INPATIENT;
            $encounter->save();
            $transfer->update(['estado' => 2, 'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
            if ($bed) {
                $type = $bed->habitacion ? (int) $bed->habitacion->id_tipo : 0;
                $transaction->update(['estado_cama' => 'ocupada',
                    'id_paciente' => $patientId, 'id_imagen' => $type === 3 ? 7 : ($type === 5 ? 11 : 3),
                    'fecha_ingreso' => $now, 'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                    'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip()]);
                $bed->update(['estado' => 3, 'id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()]);
                Ho_Hospitalizacion::create(['id_paciente' => $patientId, 'fecha' => $now, 'fecha_salida' => null,
                    'id_habitacion' => $bed->id_habitacion, 'id_cama' => $bedId,
                    'id_traspaso' => $transfer->id, 'detalle' => trim($note), 'estado' => 1,
                    'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                    'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip()]);
            }
            app(ClinicalTransferReconciliationService::class)->complete($reconciliation, $context);
            if (Schema::hasTable('clinical_emergency_flows')) {
                \Sis_medico\ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->whereNull('closed_at')
                    ->update(['status' => 'TRANSFERRED', 'bed_request_status' => $bed ? 'ASSIGNED' : null,
                        'closed_at' => $now, 'updated_by' => Auth::id(), 'ip_address' => request()->ip()]);
            }
            return ['idempotent' => false, 'solicitud_id' => $solicitud->id,
                'agenda_id' => $receptionAgenda ? $receptionAgenda->id : null]
                + ($bedHistoryWarning ? ['warning' => 'Esta cama tenía movimientos previos; se comprobó que estaba libre antes de asignarla.'] : []);
        }, 3);
    }

    private function createReceptionAgenda(Ho_Solicitud $solicitud, Ho_Traspaso_Sala008 $transfer,
        ClinicalEncounter $encounter, $now)
    {
        $actor = Auth::id();
        $doctor = $transfer->id_doctor ?: $actor;
        $ip = request()->ip();
        $endsAt = date('Y-m-d H:i:s', strtotime($now) + 900);
        $specialty = DB::table('user_espe')->where('usuid', $doctor)->value('espid');
        $agenda = Agenda::create([
            'fechaini' => $now, 'fechafin' => $endsAt,
            'id_paciente' => $solicitud->id_paciente, 'id_doctor1' => $doctor,
            'id_empresa' => $encounter->id_empresa, 'id_sala' => $transfer->id_sala,
            'id_seguro' => $solicitud->id_seguro, 'espid' => $specialty ?: 4,
            'proc_consul' => 0, 'estado_cita' => 0, 'estado' => 1,
            'est_amb_hos' => 1,
            'observaciones' => 'Consulta pendiente por traslado desde Emergencia. Solicitud #'.$solicitud->id,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => $ip, 'ip_modificacion' => $ip,
        ]);
        app(ClinicalEncounterService::class)->link($encounter, ['agenda' => $agenda->id]);
        Log_Agenda::create([
            'id_agenda' => $agenda->id, 'estado_cita_ant' => 0, 'estado_cita' => 0,
            'fechaini' => $now, 'fechafin' => $endsAt, 'estado' => 4,
            'observaciones' => 'Recepción desde Emergencia en sala '.$transfer->id_sala,
            'id_doctor1' => $doctor, 'descripcion' => 'CONSULTA PENDIENTE POR TRASLADO',
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => $ip, 'ip_modificacion' => $ip,
        ]);
        return $agenda;
    }

    private function releaseToCleaning($bed, $patientId, array $context)
    {
        if (!$bed) throw new \InvalidArgumentException('La estancia anterior no tiene una cama valida.');
        $transaction = CamaTransaccion::where('id_cama', $bed->id)
            ->orderByDesc('id')->lockForUpdate()->first();
        if (!$transaction || (string) $transaction->id_paciente !== (string) $patientId) {
            throw new \InvalidArgumentException('La ocupacion de la cama anterior no corresponde al paciente.');
        }
        $type = $bed->habitacion ? (int) $bed->habitacion->id_tipo : 0;
        $transaction->update(['estado_cama' => 'preparacion', 'id_paciente' => null,
            'id_imagen' => $type === 3 ? 6 : ($type === 5 ? 10 : 2),
            'id_usuariomod' => $context['actor_id'], 'ip_modificacion' => $context['ip']]);
        $bed->update(['estado' => 2, 'id_usuariomod' => $context['actor_id'], 'ip_modificacion' => $context['ip']]);
        CamaPaciente::where('id_cama', $bed->id)->where('id_paciente', $patientId)->where('estado', 1)
            ->update(['estado' => 0, 'observaciones' => 'Traslado confirmado: cama pendiente de limpieza.']);
    }
}
