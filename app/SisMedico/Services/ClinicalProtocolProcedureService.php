<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_Agenda;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Sala;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;

class ClinicalProtocolProcedureService
{
    private $encounters;
    private $tenants;
    private $hospitals;

    public function __construct(
        ClinicalEncounterService $encounters,
        TenantContext $tenants,
        CurrentHospitalResolver $hospitals
    )
    {
        $this->encounters = $encounters;
        $this->tenants = $tenants;
        $this->hospitals = $hospitals;
    }

    /**
     * Registra de forma atómica una atención de procedimiento y la enlaza al episodio clínico.
     */
    public function create(Request $request, $actorId, $ip)
    {
        return DB::transaction(function () use ($request, $actorId, $ip) {
            $procedures = array_values(array_unique(array_filter(
                (array) $request->input('procedimiento', array()),
                function ($procedureId) {
                    return trim((string) $procedureId) !== '';
                }
            )));
            if (count($procedures) === 0) {
                throw ValidationException::withMessages(array(
                    'procedimiento' => 'Debe seleccionar al menos un procedimiento.',
                ));
            }

            $procedureType = (string) $request->input('tipo_procedimiento', '');
            if (!in_array($procedureType, array('0', '1', '2'), true)) {
                throw ValidationException::withMessages(array(
                    'tipo_procedimiento' => 'El tipo de procedimiento seleccionado no es válido.',
                ));
            }

            $company = $this->tenants->resolve($request, auth()->user());
            if (is_null($company)) {
                throw ValidationException::withMessages(array(
                    'id_empresa' => 'Seleccione una empresa válida antes de crear el procedimiento.',
                ));
            }
            $companyId = $company->id;

            $patient = Paciente::where('id', $request->input('paciente'))->lockForUpdate()->firstOrFail();
            if (empty($patient->id_seguro) || !\Sis_medico\Seguro::query()->where('id', $patient->id_seguro)->exists()) {
                throw ValidationException::withMessages(array(
                    'paciente' => 'El paciente no tiene un seguro válido asignado.',
                ));
            }

            $validProcedures = DB::table('procedimiento')
                ->whereIn('id', $procedures)
                ->where('estado', 1)
                ->pluck('id')
                ->map(function ($procedureId) {
                    return (string) $procedureId;
                })
                ->all();
            $invalidProcedures = array_diff(array_map('strval', $procedures), $validProcedures);
            if (count($invalidProcedures) > 0) {
                throw ValidationException::withMessages(array(
                    'procedimiento' => 'Uno o más procedimientos seleccionados no existen o están inactivos.',
                ));
            }

            $room = $this->resolveRoom($companyId);
            $specialtyId = 4;
            if (!\Sis_medico\Especialidad::query()->where('id', $specialtyId)->where('estado', 1)->exists()) {
                throw ValidationException::withMessages(array(
                    'especialidad' => 'No existe una especialidad activa configurada para procedimientos.',
                ));
            }

            $primaryProcedure = $procedures[0];
            $now = date('Y-m-d H:i:s');

            $agendaId = Agenda::insertGetId(array(
                'fechaini' => $now,
                'fechafin' => $now,
                'id_paciente' => $patient->id,
                'id_doctor1' => $actorId,
                'proc_consul' => '4',
                'estado_cita' => '4',
                'id_empresa' => $companyId,
                'espid' => $specialtyId,
                'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_seguro' => $patient->id_seguro,
                'estado' => '4',
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'id_procedimiento' => $primaryProcedure,
                'id_sala' => $room->id,
            ));

            $additional = '';
            foreach ($procedures as $procedureId) {
                if ((string) $primaryProcedure === (string) $procedureId) continue;
                $additional .= '+'.$procedureId;
                AgendaProcedimiento::create(array(
                    'id_agenda' => $agendaId,
                    'id_procedimiento' => $procedureId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                ));
            }

            Log_Agenda::create(array(
                'id_agenda' => $agendaId,
                'estado_cita_ant' => '0',
                'estado_cita' => '0',
                'fechaini' => $now,
                'fechafin' => $now,
                'estado' => '4',
                'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_doctor1' => $actorId,
                'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'campos_ant' => 'PRO: '.$primaryProcedure.$additional,
                'id_usuariomod' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'ip_creacion' => $ip,
            ));

            $historyId = Historiaclinica::insertGetId(array(
                'parentesco' => $patient->parentesco,
                'id_usuario' => $patient->id_usuario,
                'id_agenda' => $agendaId,
                'id_paciente' => $patient->id,
                'id_seguro' => $patient->id_seguro,
                'id_doctor1' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            $pentaxId = Pentax::insertGetId(array(
                'id_agenda' => $agendaId,
                'hcid' => $historyId,
                'id_sala' => $room->id,
                'id_doctor1' => $actorId,
                'id_seguro' => $patient->id_seguro,
                'observacion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            $procedureList = '';
            foreach ($procedures as $procedureId) {
                PentaxProc::create(array(
                    'id_pentax' => $pentaxId,
                    'id_procedimiento' => $procedureId,
                    'id_usuariocrea' => $actorId,
                    'ip_modificacion' => $ip,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                ));
                $procedureList .= '+'.$procedureId;
            }

            Pentax_log::create(array(
                'id_pentax' => $pentaxId,
                'tipo_cambio' => 'CREADO POR EL DOCTOR',
                'descripcion' => 'EN ESPERA',
                'estado_pentax' => '0',
                'procedimientos' => $procedureList,
                'id_doctor1' => $actorId,
                'observacion' => 'CREADO POR EL DOCTOR',
                'id_sala' => $room->id,
                'id_seguro' => $patient->id_seguro,
                'ip_modificacion' => $ip,
                'ip_creacion' => $ip,
                'id_usuariomod' => $actorId,
                'id_usuariocrea' => $actorId,
            ));

            $clinicalProcedureId = hc_procedimientos::insertGetId(array(
                'id_hc' => $historyId,
                'id_seguro' => $patient->id_seguro,
                'id_empresa' => $companyId,
                'id_doctor_examinador' => $actorId,
                'id_doctor_examinador2' => $actorId,
                'tipo_procedimiento' => $procedureType,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            hc_protocolo::insert(array(
                'id_hc_procedimientos' => $clinicalProcedureId,
                'fecha' => date('Y-m-d'),
                'hora_inicio' => date('H:i:s'),
                'hora_fin' => date('H:i:s'),
                'estado_final' => ' ',
                'tipo_procedimiento' => $procedureType,
                'ip_modificacion' => $ip,
                'hcid' => $historyId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));

            foreach ($procedures as $procedureId) {
                Hc_Procedimiento_Final::create(array(
                    'id_hc_procedimientos' => $clinicalProcedureId,
                    'id_procedimiento' => $procedureId,
                    'id_usuariocrea' => $actorId,
                    'ip_modificacion' => $ip,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                ));
            }

            $encounter = $this->encounters->openOrReuse($patient->id, ClinicalEncounterService::AMBULATORY, array(
                'started_at' => $now,
                'id_empresa' => $companyId,
                'actor_id' => $actorId,
                'ip' => $ip,
            ));
            $this->encounters->link($encounter, array('agenda' => $agendaId, 'historiaclinica' => $historyId));

            return array('agenda_id' => $agendaId, 'history_id' => $historyId, 'procedure_id' => $clinicalProcedureId);
        });
    }

    /**
     * Usa una sala real del catálogo. Se prioriza la sala de procedimientos
     * del hospital actual y se evita depender de identificadores históricos.
     */
    private function resolveRoom($companyId)
    {
        $hospitalId = $this->hospitals->id(array('company_id' => $companyId));
        $roomQuery = Sala::query()->where('estado', 1);

        if (!is_null($hospitalId)) {
            $room = (clone $roomQuery)
                ->where('id_hospital', $hospitalId)
                ->orderByRaw('CASE WHEN proc_consul_sala = 1 THEN 0 ELSE 1 END')
                ->orderBy('id')
                ->first();
            if (!is_null($room)) {
                return $room;
            }
        }

        $room = $roomQuery
            ->orderByRaw('CASE WHEN proc_consul_sala = 1 THEN 0 ELSE 1 END')
            ->orderBy('id')
            ->first();
        if (is_null($room)) {
            throw ValidationException::withMessages(array(
                'id_sala' => 'No existe una sala activa configurada para crear procedimientos.',
            ));
        }

        return $room;
    }
}
