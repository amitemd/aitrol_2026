<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Support\RequestSchema;

/** Read-only projection of emergency notes into episode form drafts. */
class HospitalEpisodeEmergencySource
{
    public function forForm($solicitudId, $code)
    {
        $result = ['suggestions' => [], 'records' => [], 'evolutions' => []];
        if ((string) $code === '001') return $this->admission($solicitudId, $result);
        if (!in_array((string) $code, ['003', '005', '006', '008'], true)) return $result;

        $latest = [];
        $evolutions = [];
        $notes = collect();
        if (RequestSchema::hasTable('clinical_emergency_care_entries')) {
            $notes = DB::table('clinical_emergency_care_entries')
                ->where('solicitud_id', $solicitudId)->orderBy('id')->get();
            foreach ($notes as $note) {
                if (!isset(ClinicalEmergencyCareService::sections()[$note->section])) continue;
                $latest[$note->section] = $note;
                if ($note->section === 'evolution' && $this->filled($note->content)) $evolutions[] = $note;
            }
        }
        foreach ($latest as $section => $note) {
            if (!$this->filled($note->content)) continue;
            $result['records'][] = $this->record(ClinicalEmergencyCareService::sections()[$section]['title'], $note);
        }
        // Keep previous revisions visible, including a section later cleared.
        foreach ($notes as $note) {
            if (!isset($latest[$note->section]) || !$this->filled($note->content)
                || $latest[$note->section]->id === $note->id) continue;
            $result['records'][] = $this->record('Versión anterior · '.ClinicalEmergencyCareService::sections()[$note->section]['title'], $note);
        }
        $result['evolutions'] = array_map(function ($note) {
            return $this->record('Evolución y plan', $note);
        }, $evolutions);
        usort($result['records'], function ($a, $b) { return $a['id'] <=> $b['id']; });

        $triage = RequestSchema::hasTable('clinical_emergency_triage_assessments')
            ? DB::table('clinical_emergency_triage_assessments')
                ->where('solicitud_id', $solicitudId)->orderByDesc('id')->first() : null;
        $intake = RequestSchema::hasTable('clinical_emergency_intake_updates')
            ? DB::table('clinical_emergency_intake_updates')
                ->where('solicitud_id', $solicitudId)->orderByDesc('id')->first() : null;
        $intakeData = $intake ? (json_decode($intake->clinical_data, true) ?: []) : [];
        if ($intake) {
            $intakeLines = [];
            foreach (ClinicalEmergencyProgress::labels() as $field => $label) {
                $value = $intakeData[$field] ?? null;
                if (is_scalar($value) && trim((string) $value) !== '') {
                    $intakeLines[] = $label.': '.$value;
                }
            }
            if ($intakeLines) {
                array_unshift($result['records'], [
                    'id' => 0,
                    'title' => $intake->stage === 'CONFIRMED' ? 'Ingreso de emergencia confirmado' : 'Ingreso de emergencia pendiente de confirmar',
                    'content' => implode("\n", $intakeLines),
                    'recorded_at' => $intake->created_at, 'recorded_by' => $intake->recorded_by,
                ]);
            }
        }
        $reason = $this->value($latest, 'illness') ?: (string) ($triage->clinical_reason ?? ($intakeData['clinical_reason'] ?? ''));
        $history = $this->value($latest, 'history');
        $examination = $this->value($latest, 'examination');
        $injuries = $this->value($latest, 'injuries');
        $procedures = $this->value($latest, 'procedures');
        $evolution = implode("\n\n", array_map(function ($note) {
            return $note->created_at.' · '.$note->recorded_by."\n".trim($note->content);
        }, $evolutions));
        $vitals = [];
        foreach (['systolic' => 'PA sistólica', 'diastolic' => 'PA diastólica',
            'heart_rate' => 'FC', 'respiratory_rate' => 'FR', 'temperature' => 'Temperatura',
            'oxygen_saturation' => 'SpO₂'] as $field => $label) {
            if ($triage && $triage->$field !== null && $triage->$field !== '') {
                $vitals[] = $label.': '.$triage->$field;
            }
        }
        $triageSummary = $triage ? trim(($triage->priority_name ?: 'Triaje').' · '.implode(' · ', $vitals), " ·") : '';
        if ($triage) {
            array_unshift($result['records'], [
                'id' => 0, 'title' => 'Triaje confirmado',
                'content' => trim(($triage->clinical_reason ?: '')."\n".$triageSummary),
                'recorded_at' => $triage->assessed_at, 'recorded_by' => $triage->assessed_by,
            ]);
        }

        if ($code === '003') {
            $initialReason = trim((string) ($triage->clinical_reason ?? ''))
                ?: trim((string) ($intakeData['clinical_reason'] ?? ''));
            $result['suggestions'] = $this->nonEmpty([
                'motivo_consulta' => $initialReason ?: $reason,
                'antecedentes_personales' => $history,
                'enfermedad_actual' => $this->value($latest, 'illness'),
                'signos_vitales' => implode(' · ', $vitals), 'examen_fisico' => $examination,
            ]);
        } elseif ($code === '005') {
            $latestEvolution = count($evolutions) ? end($evolutions) : null;
            $result['suggestions'] = $this->nonEmpty([
                'fecha_hora' => $latestEvolution && $latestEvolution->created_at
                    ? date('Y-m-d\TH:i', strtotime($latestEvolution->created_at)) : '',
                'evolucion' => $evolution,
            ]);
        } elseif ($code === '006') {
            $result['suggestions'] = $this->nonEmpty([
                'resumen_clinico' => implode("\n\n", array_filter([$reason, $evolution])),
            ]);
        } else {
            $result['suggestions'] = $this->nonEmpty([
                'motivo' => $reason, 'antecedentes' => $history,
                'evaluacion' => $examination,
                'signos_vitales' => $triageSummary, 'trauma' => $injuries,
                'plan' => implode("\n\n", array_filter([$procedures, $this->value($latest, 'evolution')])),
            ]);
        }
        return $result;
    }

    private function admission($solicitudId, array $result)
    {
        $solicitud = Ho_Solicitud::with('paciente')->find($solicitudId);
        if (!$solicitud) return $result;
        $patient = $solicitud->paciente;
        $location = '';
        if (RequestSchema::hasTable('ho_hospitalizacion') && RequestSchema::hasTable('ho_traspaso_sala008')
            && RequestSchema::hasTable('cama') && RequestSchema::hasTable('habitacion')) {
            $stay = DB::table('ho_hospitalizacion as stay')
                ->join('ho_traspaso_sala008 as transfer', 'transfer.id', '=', 'stay.id_traspaso')
                ->join('cama as bed', 'bed.id', '=', 'stay.id_cama')
                ->join('habitacion as room', 'room.id', '=', 'bed.id_habitacion')
                ->where('transfer.id_solicitud', $solicitudId)
                ->where('transfer.id_paciente', $solicitud->id_paciente)
                ->where('stay.id_paciente', $solicitud->id_paciente)
                ->where('stay.estado', 1)
                ->select('room.codigo as room', 'bed.codigo as bed')->orderByDesc('stay.id')->first();
            if ($stay) $location = 'Habitación '.$stay->room.' · Cama '.$stay->bed;
        }
        $result['suggestions'] = $this->nonEmpty([
            'fecha_admision' => $solicitud->fecha_ingreso
                ? date('Y-m-d\TH:i', strtotime($solicitud->fecha_ingreso)) : '',
            'procedencia' => 'Emergencia',
            'direccion_residencia' => $patient ? $patient->direccion : '',
            'telefono_contacto' => $patient ? ($patient->telefono1 ?: $patient->telefono2) : '',
            'ubicacion' => $location,
        ]);
        return $result;
    }

    private function record($title, $note)
    {
        return ['id' => (int) $note->id, 'title' => $title, 'content' => trim($note->content),
            'recorded_at' => $note->created_at, 'recorded_by' => $note->recorded_by];
    }

    private function value(array $latest, $section)
    {
        return $this->filled($latest[$section]->content ?? null)
            ? trim($latest[$section]->content) : '';
    }

    private function filled($value)
    {
        return trim(strip_tags((string) $value)) !== '';
    }

    private function nonEmpty(array $values)
    {
        return array_filter($values, function ($value) { return trim((string) $value) !== ''; });
    }
}
