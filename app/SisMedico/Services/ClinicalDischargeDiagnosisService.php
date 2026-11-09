<?php

namespace Sis_medico\Services;

use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Hc_Epicrisis_Final_Cie10;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;

class ClinicalDischargeDiagnosisService
{
    public function hasDefinitiveDischarge(Ho_Solicitud $solicitud = null, Hc_Epicrisis_Final $epicrisis = null)
    {
        if (!$solicitud || !$epicrisis) return false;

        if (Hc_Epicrisis_Final_Cie10::where('id_hc_epicrisis_final', $epicrisis->id)
            ->where('ingreso_egreso', 'EGRESO')
            ->where('presuntivo_definitivo', 'DEFINITIVO')->exists()) {
            return true;
        }

        return $this->legacyDiagnoses($solicitud)->exists();
    }

    /**
     * Lleva a la epicrisis los diagnósticos definitivos que el profesional ya
     * registró en Emergencia. No inventa diagnósticos y es idempotente.
     */
    public function syncToEpicrisis(Ho_Solicitud $solicitud, Hc_Epicrisis_Final $epicrisis)
    {
        $count = 0;
        foreach ($this->legacyDiagnoses($solicitud)->get() as $diagnosis) {
            $record = Hc_Epicrisis_Final_Cie10::firstOrCreate(array(
                'id_hc_epicrisis_final' => $epicrisis->id,
                'cie10' => $diagnosis->cie10,
                'ingreso_egreso' => 'EGRESO',
                'presuntivo_definitivo' => 'DEFINITIVO',
            ), array(
                'id_ho_solicitud' => $solicitud->id,
                'hcid' => $diagnosis->hcid,
                'hc_id_procedimiento' => $diagnosis->hc_id_procedimiento,
                'ip_creacion' => $diagnosis->ip_creacion,
                'ip_modificacion' => $diagnosis->ip_modificacion,
                'id_usuariocrea' => $diagnosis->id_usuariocrea,
                'id_usuariomod' => $diagnosis->id_usuariomod,
            ));
            if ($record->wasRecentlyCreated) $count++;
        }
        return $count;
    }

    private function legacyDiagnoses(Ho_Solicitud $solicitud)
    {
        $histories = Historiaclinica::query();
        if (!empty($solicitud->encounter_id) && Schema::hasColumn('historiaclinica', 'encounter_id')) {
            $histories->where('encounter_id', $solicitud->encounter_id);
        } else {
            $histories->where('id_agenda', $solicitud->id_agenda);
        }
        $historyIds = $histories->pluck('hcid')->all();

        return Hc_Cie10::whereIn('hcid', count($historyIds) ? $historyIds : array(0))
            ->where('ingreso_egreso', 'EGRESO')
            ->where('presuntivo_definitivo', 'DEFINITIVO');
    }
}
