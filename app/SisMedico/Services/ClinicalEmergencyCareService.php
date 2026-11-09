<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\ClinicalEmergencyCareEntry;
use Sis_medico\ClinicalEmergencyFlow;
use Sis_medico\Ho_Solicitud;

class ClinicalEmergencyCareService
{
    public static function sections()
    {
        return [
            'illness' => ['title' => 'Enfermedad actual', 'hint' => 'Motivo, inicio, evolución y síntomas relevantes.'],
            'history' => ['title' => 'Antecedentes y alergias', 'hint' => 'Registre solo lo conocido; identifique lo que queda pendiente de confirmar.'],
            'examination' => ['title' => 'Examen físico', 'hint' => 'Hallazgos observados y exploración por sistemas.'],
            'injuries' => ['title' => 'Lesiones o valoración obstétrica', 'hint' => 'Complete esta sección únicamente cuando corresponda.'],
            'procedures' => ['title' => 'Procedimientos realizados', 'hint' => 'Describa qué se hizo, cuándo y por quién.'],
            'evolution' => ['title' => 'Evolución y plan', 'hint' => 'Cambios clínicos, reevaluaciones, indicaciones y pendientes para el siguiente turno.'],
        ];
    }

    public function access(Ho_Solicitud $solicitud, Request $request, $action = 'EMERGENCY_VIEW')
    {
        $encounter = app(ClinicalEmergencyFlowService::class)->assertAccess($solicitud, $request, $action);
        $flow = ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->firstOrFail();
        $active = $solicitud->estado == 1 && in_array((int) $solicitud->estado_paso, [1, 2], true)
            && !$solicitud->fecha_salida && $encounter->status === 'ACTIVE' && !$encounter->ended_at
            && $encounter->clinical_state === 'EMERGENCY_ACTIVE' && !$flow->closed_at;
        $inCare = in_array($flow->status, [ClinicalEmergencyFlowService::IN_ATTENTION,
            ClinicalEmergencyFlowService::UNDER_OBSERVATION], true) && $flow->attention_started_at;

        return ['flow' => $flow, 'can_edit' => $active && $inCare];
    }

    public function latest($solicitudId)
    {
        return ClinicalEmergencyCareEntry::whereIn('id', function ($query) use ($solicitudId) {
            $query->selectRaw('MAX(id)')->from('clinical_emergency_care_entries')
                ->where('solicitud_id', $solicitudId)->groupBy('section');
        })->get()->keyBy('section');
    }

    public function save(Ho_Solicitud $solicitud, Request $request, array $data)
    {
        return DB::transaction(function () use ($solicitud, $request, $data) {
            $solicitud = Ho_Solicitud::where('id', $solicitud->id)->lockForUpdate()->firstOrFail();
            $access = $this->access($solicitud, $request, 'EMERGENCY_TRIAGE');
            if (!$access['can_edit']) {
                throw new \DomainException('La atención no está abierta para agregar notas.');
            }
            $section = $data['section'];
            if (!array_key_exists($section, self::sections())) {
                throw new \InvalidArgumentException('Sección clínica no válida.');
            }
            $latest = ClinicalEmergencyCareEntry::where('solicitud_id', $solicitud->id)
                ->where('section', $section)->orderByDesc('id')->first();
            if ((int) ($latest ? $latest->id : 0) !== (int) $data['base_entry_id']) {
                throw new \DomainException('Otro profesional actualizó esta sección. Recargue antes de guardar.');
            }
            $content = trim((string) ($data['content'] ?? ''));
            if ($latest && (string) $latest->content === $content) return $latest;
            if (!$latest && $content === '') return null;
            return ClinicalEmergencyCareEntry::create([
                'solicitud_id' => $solicitud->id,
                'section' => $section,
                'content' => $content === '' ? null : $content,
                'recorded_by' => Auth::id(),
                'ip_address' => $request->ip(),
            ]);
        }, 3);
    }

}
