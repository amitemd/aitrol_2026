<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Empresa;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HospitalEpisodeFormDraft;
use Sis_medico\Support\RequestSchema;

class HospitalSurgeryChecklistContext
{
    public function forEpisode(Ho_Solicitud $solicitud, ?Empresa $empresa = null)
    {
        $solicitud->loadMissing('paciente');
        $agenda = $this->surgeryAgenda($solicitud);
        if ($agenda) {
            $agenda->loadMissing(['sala.hospital', 'procedimiento']);
        }

        $companyName = trim((string) (($empresa->nombrecomercial ?? null)
            ?: ($empresa->razonsocial ?? null) ?: 'ESTABLECIMIENTO DE SALUD'));
        $hospitalName = trim((string) (($agenda && $agenda->sala && $agenda->sala->hospital
            ? $agenda->sala->hospital->nombre_hospital : null) ?: $companyName));
        $surgery = $agenda && RequestSchema::hasTable('agenda_cirugias')
            ? DB::table('agenda_cirugias')->where('id_agenda', $agenda->id)->first() : null;
        $surgeryDate = $surgery && $surgery->hora_quirurgica
            ? $surgery->hora_quirurgica : ($agenda ? $agenda->fechaini : null);
        $patient = $solicitud->paciente;
        $age = '';
        if ($patient && $patient->fecha_nacimiento) {
            try {
                $reference = $surgeryDate ? Carbon::parse($surgeryDate) : Carbon::now();
                $birthDate = Carbon::parse($patient->fecha_nacimiento);
                if ($birthDate->lte($reference)) {
                    $age = (int) $birthDate->diffInYears($reference);
                }
            } catch (\Exception $exception) {
                $age = '';
            }
        }

        return [
            'institution' => mb_strtoupper($hospitalName, 'UTF-8'),
            'establishment' => mb_strtoupper($companyName, 'UTF-8'),
            'unicodigo' => trim((string) ($empresa->unicodigo ?? '')),
            'date' => $surgeryDate ? date('d/m/Y', strtotime($surgeryDate)) : '',
            'patient_name' => $patient ? trim(($patient->apellido1 ?? '').' '.($patient->apellido2 ?? '').' '
                .($patient->nombre1 ?? '').' '.($patient->nombre2 ?? '')) : '',
            'age' => $age,
            'history_number' => (string) $solicitud->id_paciente,
            'operating_room' => $agenda && $agenda->sala ? trim((string) $agenda->sala->nombre_sala) : '',
            'procedure' => $this->procedureName($solicitud, $agenda),
        ];
    }

    private function surgeryAgenda(Ho_Solicitud $solicitud)
    {
        if ($solicitud->id_agenda) {
            $agenda = Agenda::where('id', $solicitud->id_agenda)->where('proc_consul', 5)->first();
            if ($agenda) {
                return $agenda;
            }
        }
        if (! $solicitud->encounter_id || ! RequestSchema::hasColumn('agenda', 'encounter_id')) {
            return null;
        }

        return Agenda::where('encounter_id', $solicitud->encounter_id)
            ->where('id_paciente', $solicitud->id_paciente)->where('proc_consul', 5)
            ->where('estado', 1)->orderByDesc('id')->first();
    }

    private function procedureName(Ho_Solicitud $solicitud, ?Agenda $agenda = null)
    {
        if ($agenda && $agenda->procedimiento) {
            $name = trim((string) $agenda->procedimiento->nombre);
            if ($name !== '') {
                return mb_strtoupper($name, 'UTF-8');
            }
        }
        if (! RequestSchema::hasTable('hospital_episode_form_drafts')) {
            return '';
        }
        $draft = HospitalEpisodeFormDraft::where('encounter_id', $solicitud->encounter_id)
            ->where('solicitud_id', $solicitud->id)->where('form_code', '017')
            ->orderByDesc('id')->first();
        if (! $draft) {
            return '';
        }
        $payload = (array) $draft->payload;

        return mb_strtoupper(trim((string) ($payload['procedimiento'] ?? '')), 'UTF-8');
    }
}
