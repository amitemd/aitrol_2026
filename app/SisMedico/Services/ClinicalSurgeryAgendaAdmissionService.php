<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterLink;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Sala;
use Sis_medico\Support\RequestSchema;

class ClinicalSurgeryAgendaAdmissionService
{
    public static function constrainSurgeryType($query, string $table = 'agenda')
    {
        return $query->where(function ($type) use ($table) {
            $type->where($table.'.proc_consul', 5);
            if (RequestSchema::hasColumn('agenda', 'ho_tipo')) {
                $type->orWhere($table.'.ho_tipo', 5);
            }
            if (RequestSchema::hasTable('agenda_cirugias')) {
                $type->orWhereExists(function ($detail) use ($table) {
                    $detail->selectRaw('1')->from('agenda_cirugias as surgery_detail')
                        ->whereColumn('surgery_detail.id_agenda', $table.'.id');
                });
            }
        });
    }

    public function ensure($agendaId, $actorId = null, $ip = '127.0.0.1', $requireUniqueAdmission = true)
    {
        return DB::transaction(function () use ($agendaId, $actorId, $ip, $requireUniqueAdmission) {
            $agenda = self::constrainSurgeryType(Agenda::whereKey($agendaId))->lockForUpdate()->first();
            if (! $agenda || (int) $agenda->estado !== 1
                || ! $agenda->id_paciente || ! $agenda->id_empresa || ! $agenda->id_sala) {
                return null;
            }

            $actorId = $actorId ?: $agenda->id_usuariocrea ?: $agenda->id_doctor1;
            $sala = Sala::query()->where('sala.id_empresa', $agenda->id_empresa)
                ->whereHas('hospital', fn ($hospital) => $hospital->forCompany((string) $agenda->id_empresa))
                ->findOrFail($agenda->id_sala);
            $admissions = Ho_Solicitud::join('clinical_encounters as surgery_encounter',
                'surgery_encounter.id', '=', 'ho_solicitud.encounter_id')
                ->where('ho_solicitud.id_paciente', $agenda->id_paciente)
                ->where('ho_solicitud.estado', 1)
                ->where('ho_solicitud.estado_paso', '>', 0)
                ->where('surgery_encounter.id_empresa', $agenda->id_empresa)
                ->where('surgery_encounter.id_hospital', $sala->id_hospital)
                ->where('surgery_encounter.id_paciente', $agenda->id_paciente)
                ->where('surgery_encounter.status', ClinicalEncounterService::ACTIVE)
                ->where('surgery_encounter.care_setting', '<>', ClinicalEncounterService::AMBULATORY)
                ->select('ho_solicitud.*')->orderByDesc('ho_solicitud.id')
                ->lockForUpdate();
            $candidates = $admissions->limit($requireUniqueAdmission ? 2 : 1)->get();
            if ($requireUniqueAdmission && $candidates->count() > 1) {
                throw new \InvalidArgumentException('Hay varios ingresos hospitalarios activos para este paciente. Se requiere confirmar cuál corresponde a la cirugía.');
            }
            $solicitud = $candidates->first();

            $createdRequest = false;
            if ($solicitud) {
                $encounter = ClinicalEncounter::where('id', $solicitud->encounter_id)
                    ->lockForUpdate()->firstOrFail();
            } else {
                $encounter = app(ClinicalEncounterService::class)->openOrReuse(
                    $agenda->id_paciente,
                    ClinicalEncounterService::INPATIENT,
                    [
                        'id_empresa' => $agenda->id_empresa,
                        'id_hospital' => $sala->id_hospital,
                        'actor_id' => $actorId,
                        'ip' => $ip,
                        'legacy_step' => 5,
                    ]
                );
                $solicitud = Ho_Solicitud::where('encounter_id', $encounter->id)
                    ->where('id_paciente', $agenda->id_paciente)->where('estado', 1)
                    ->where('estado_paso', '>', 0)->whereNull('fecha_salida')->lockForUpdate()->first();
            }

            if (! $solicitud) {
                $solicitud = Ho_Solicitud::create([
                    'encounter_id' => $encounter->id,
                    'id_paciente' => $agenda->id_paciente,
                    'id_agenda' => $agenda->id,
                    'id_seguro' => $agenda->id_seguro,
                    'fecha_ingreso' => date('Y-m-d H:i:s'),
                    'estado_paso' => 5,
                    'estado' => 1,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);
                $createdRequest = true;
            }

            $agendaLink = ClinicalEncounterLink::where('source_type', 'agenda')
                ->where('source_id', $agenda->id)->lockForUpdate()->first();
            if ($agendaLink && (string) $agendaLink->encounter_id !== (string) $encounter->id) {
                $previous = ClinicalEncounter::findOrFail($agendaLink->encounter_id);
                abort_unless((string) $previous->id_empresa === (string) $agenda->id_empresa
                    && (string) $previous->id_paciente === (string) $agenda->id_paciente, 403);
                if ($previous->care_setting === ClinicalEncounterService::AMBULATORY) {
                    $agendaLink->encounter_id = $encounter->id;
                    $agendaLink->save();
                }
            }
            app(ClinicalEncounterService::class)->link($encounter, ['agenda' => $agenda->id,
                'ho_solicitud' => $solicitud->id]);

            if ($createdRequest) {
                $log = Ho_Log_Solicitud::create([
                    'encounter_id' => $encounter->id,
                    'id_ho_solicitud' => $solicitud->id,
                    'estado_paso' => 5,
                    'id_agenda' => $agenda->id,
                    'fecha_ingreso' => date('Y-m-d H:i:s'),
                    'fecha_salida' => null,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);
                app(ClinicalEncounterService::class)->link($encounter, ['ho_log_solicitud' => $log->id]);

            }

            $hasSurgeryReception = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)
                ->where('paso', 5)->whereIn('estado', [1, 2])->exists();
            if (($createdRequest || (int) $solicitud->estado_paso === 5) && ! $hasSurgeryReception) {
                Ho_Traspaso_Sala008::create([
                    'id_solicitud' => $solicitud->id,
                    'id_paciente' => $agenda->id_paciente,
                    'id_sala' => $agenda->id_sala,
                    'paso' => 5,
                    'seccion' => 4,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_doctor' => $agenda->id_doctor1,
                    'observaciones' => 'Ingreso quirúrgico creado desde Agenda Clínica.',
                    'estado' => 2,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);
            }

            return $solicitud;
        }, 3);
    }
}
