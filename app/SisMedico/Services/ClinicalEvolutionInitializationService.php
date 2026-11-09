<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Empresa;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_Agenda;
use Sis_medico\Paciente;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;

class ClinicalEvolutionInitializationService
{
    private $encounters;

    public function __construct(ClinicalEncounterService $encounters)
    {
        $this->encounters = $encounters;
    }

    /**
     * Crea de forma atómica la visita, historia, procedimiento, evolución y receta base.
     */
    public function initialize($patientId, $sourceAgendaId, $actorId, $ip, array $options = array())
    {
        return DB::transaction(function () use ($patientId, $sourceAgendaId, $actorId, $ip, $options) {
            $patient = Paciente::where('id', $patientId)->lockForUpdate()->firstOrFail();
            $sourceAgenda = $sourceAgendaId !== 'no' ? Agenda::find($sourceAgendaId) : null;
            $now = date('Y-m-d H:i:s');
            $mode = isset($options['mode']) ? $options['mode'] : 'admission_evolution';

            $agenda = new Agenda(array(
                'fechaini' => $now,
                'fechafin' => $now,
                'id_paciente' => $patientId,
                'id_doctor1' => $actorId,
                'proc_consul' => '4',
                'estado_cita' => '4',
                'espid' => '4',
                'observaciones' => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_seguro' => $patient->id_seguro,
                'estado' => '4',
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
            ));
            if ($sourceAgenda) {
                $agenda->id_empresa = $sourceAgenda->id_empresa;
                $agenda->id_sala = $sourceAgenda->id_sala;
            }
            $this->applyModeToAgenda($agenda, $mode, $actorId);
            $agenda->save();

            if ($mode === 'consultation' || $mode === 'hospital_visit') {
                $label = $mode === 'hospital_visit' ? 'VISITA' : 'CONSULTA';
                Hc_Log::create(array(
                    'anterior' => $label.': -> El Dr. creo nueva '.strtolower($label).' -> id_agenda: '.$agenda->id,
                    'nuevo' => $label.': -> El Dr. creo nueva '.strtolower($label).' -> id_agenda: '.$agenda->id,
                    'id_paciente' => $patientId,
                    'id_usuariomod' => $actorId,
                    'id_usuariocrea' => $actorId,
                    'ip_modificacion' => $ip,
                    'ip_creacion' => $ip,
                ));
            }

            Log_Agenda::create(array(
                'id_agenda' => $agenda->id,
                'estado_cita_ant' => '0',
                'estado_cita' => '0',
                'fechaini' => $now,
                'fechafin' => $now,
                'estado' => '4',
                'observaciones' => $mode === 'hospital_visit' ? 'VISITA CREADA POR EL DOCTOR' : 'EVOLUCION CREADA POR EL DOCTOR',
                'id_doctor1' => $actorId,
                'descripcion' => $mode === 'hospital_visit' ? 'HOSPITALIZADO' : 'EVOLUCION CREADA POR EL DOCTOR',
                'descripcion2' => $mode === 'hospital_visit' ? 'INGRESO' : null,
                'descripcion3' => $mode === 'hospital_visit' ? '' : null,
                'id_usuariomod' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'ip_creacion' => $ip,
            ));

            $historyId = Historiaclinica::insertGetId(array(
                'parentesco' => $patient->parentesco,
                'id_usuario' => $patient->id_usuario,
                'id_agenda' => $agenda->id,
                'id_paciente' => $patientId,
                'id_seguro' => $patient->id_seguro,
                'id_doctor1' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_modificacion' => $ip,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            $procedureId = hc_procedimientos::insertGetId(array(
                'id_hc' => $historyId,
                'id_seguro' => $patient->id_seguro,
                'id_procedimiento_completo' => '40',
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            $evolutionId = Hc_Evolucion::insertGetId(array(
                'hc_id_procedimiento' => $procedureId,
                'hcid' => $historyId,
                'secuencia' => '0',
                'cuadro_clinico' => $mode === 'admission_evolution' ? $this->defaultClinicalPicture($patient) : null,
                'fecha_ingreso' => ' ',
                'ip_modificacion' => $ip,
                'id_usuariomod' => $actorId,
                'id_usuariocrea' => $actorId,
                'ip_creacion' => $ip,
            ));

            hc_receta::insert(array(
                'id_hc' => $historyId,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
            ));

            $careSetting = $this->careSettingForMode($mode);
            $encounter = $this->encounters->openOrReuse($patientId, $careSetting, array(
                'id_empresa' => $agenda->id_empresa,
                'started_at' => $now,
                'actor_id' => $actorId,
                'ip' => $ip,
                'override' => isset($options['override']) ? $options['override'] : null,
                'override_reason' => isset($options['override_reason']) ? $options['override_reason'] : null,
            ));
            $this->encounters->link($encounter, array('agenda' => $agenda->id, 'historiaclinica' => $historyId));

            return array(
                'evolution_id' => $evolutionId,
                'procedure_id' => $procedureId,
                'agenda_id' => $sourceAgenda ? $sourceAgenda->id : $agenda->id,
                'created_agenda_id' => $agenda->id,
            );
        });
    }

    private function applyModeToAgenda(Agenda $agenda, $mode, $actorId)
    {
        if ($mode !== 'consultation' && $mode !== 'hospital_visit') {
            return;
        }
        $company = Empresa::where('prioridad', 1)->first();
        if ($company) {
            $agenda->id_empresa = $company->id;
        }
        $specialty = DB::table('user_espe')->where('usuid', $actorId)->first();
        $agenda->espid = $specialty ? $specialty->espid : '4';
        if ($mode === 'hospital_visit') {
            $agenda->procedencia = 'OMNI HOSPITAL';
            $agenda->sala_hospital = '';
            $agenda->omni = 'OM';
            $agenda->est_amb_hos = '1';
            $agenda->cortesia = 'NO';
        }
    }

    private function careSettingForMode($mode)
    {
        return $mode === 'hospital_visit'
            ? ClinicalEncounterService::INPATIENT
            : ClinicalEncounterService::AMBULATORY;
    }

    private function defaultClinicalPicture(Paciente $patient)
    {
        if (!$patient->seguro || $patient->seguro->tipo != '0') {
            return null;
        }
        $sex = $patient->sexo == '1' ? 'MASCULINO' : ($patient->sexo == '2' ? 'FEMENINO' : 'NO ESPECIFICADO');
        $age = Carbon::createFromFormat('Y-m-d', substr($patient->fecha_nacimiento, 0, 10))->age;
        return 'PACIENTE DE SEXO '.$sex.' DE '.$age.' AÑOS DE EDAD CON CUADRO CLINICO DE  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MESES DE EVOLUCION CARACTERIZADO POR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; , &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,<br>'
            .'(DESCRIPCION DE SINTOMAS INTENSIDAD, HORARIO DE APARICION QUE LO EXACERBA)<br>'
            .'EN LA ACTUALIDAD SINTOMAS SE INTESIFICAN POR LO QUE ACUDE A CONSULTA.';
    }
}
