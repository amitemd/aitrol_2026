<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Paciente;
use Sis_medico\Sala;

class AmbulatoryAdmissionService
{
    private $encounters;

    public function __construct(ClinicalEncounterService $encounters)
    {
        $this->encounters = $encounters;
    }

    /**
     * Abre de forma atómica la atención ambulatoria y sus documentos mínimos.
     * La atención ambulatoria puede coexistir con un episodio hospitalario.
     */
    public function admit($agendaId, $actorId, $ip, array $override = array())
    {
        return DB::transaction(function () use ($agendaId, $actorId, $ip, $override) {
            $agenda = Agenda::lockForUpdate()->find($agendaId);
            if (!$agenda) {
                return array('estado' => 'err', 'msn' => 'Sin Agenda');
            }

            $sala = $agenda->id_sala ? Sala::find($agenda->id_sala) : null;
            $encounter = $this->encounters->openOrReuse(
                $agenda->id_paciente,
                ClinicalEncounterService::AMBULATORY,
                array(
                    'id_empresa' => $agenda->id_empresa,
                    'id_hospital' => $sala ? $sala->id_hospital : null,
                    'started_at' => $agenda->fechaini,
                    'actor_id' => $actorId,
                    'ip' => $ip,
                    'override' => isset($override['code']) ? $override['code'] : null,
                    'override_reason' => isset($override['reason']) ? $override['reason'] : null,
                )
            );
            $this->encounters->link($encounter, array('agenda' => $agenda->id));

            $solicitud = Ho_Solicitud::where('id_agenda', $agendaId)
                ->where('estado', 1)
                ->lockForUpdate()
                ->first();

            if (!$solicitud) {
                $solicitudId = Ho_Solicitud::insertGetId(array(
                    'id_paciente' => $agenda->id_paciente,
                    'id_seguro' => $agenda->id_seguro,
                    'id_agenda' => $agenda->id,
                    'fecha_ingreso' => $agenda->fechaini,
                    'fecha_salida' => $agenda->fechaini,
                    'estado_paso' => '0',
                    'estado' => '1',
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ));
                $solicitud = Ho_Solicitud::findOrFail($solicitudId);
                Ho_Log_Solicitud::create(array(
                    'id_ho_solicitud' => $solicitudId,
                    'estado_paso' => '0',
                    'id_agenda' => $agendaId,
                    'fecha_ingreso' => $agenda->fechaini,
                    'fecha_salida' => $agenda->fechaini,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea' => $actorId,
                ));
            }

            $this->encounters->link($encounter, array('ho_solicitud' => $solicitud->id));
            $this->ensureForm008($solicitud, $actorId, $ip);
            $this->ensurePatientData($agenda->id_paciente, $actorId, $ip);

            return array('estado' => 'ok', 'id_solicitud' => $solicitud->id);
        });
    }

    private function ensureForm008(Ho_Solicitud $solicitud, $actorId, $ip)
    {
        $form = Ho_Form008::where('id_solicitud', $solicitud->id)->lockForUpdate()->first();
        if (!$form) {
            Ho_Form008::insert(array(
                'fecha_creacion' => $solicitud->fecha_ingreso,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_agenda' => $solicitud->id_agenda,
                'id_solicitud' => $solicitud->id,
            ));
        }
    }

    private function ensurePatientData($patientId, $actorId, $ip)
    {
        if (!Paciente::find($patientId)) {
            return;
        }
        $data = Ho_Datos_Paciente::where('id_paciente', $patientId)->lockForUpdate()->first();
        if (!$data) {
            Ho_Datos_Paciente::insert(array(
                'id_paciente' => $patientId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));
        }
    }
}
