<?php

namespace Sis_medico\Services;

use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Solicitud;

class ClinicalEmergencyTriageSyncService
{
    /**
     * Consolida el ultimo triaje del episodio en el Formulario 008 y en la
     * historia clinica. La operacion es idempotente: actualiza los mismos
     * registros y nunca crea una segunda atencion.
     */
    public function sync(Ho_Solicitud $solicitud, Ho_Form008 $form008, Historiaclinica $historia)
    {
        $triaje = $solicitud->manchester()->orderBy('id', 'desc')->first();
        if (is_null($triaje)) {
            return false;
        }

        $glasgow = ClinicalEmergencyClassification::glasgow($triaje->resp_ocular, $triaje->resp_verbal, $triaje->resp_motora);
        $reaccionPupilar = $this->yesNo($triaje->reaccion_pupilar);

        $form008->update(array(
            'motivo' => $triaje->motivo_consulta,
            'frec_respiratoria' => $triaje->frec_resp,
            'pulsioximetria' => $triaje->sat_oxigeno,
            'satura_oxigeno' => $triaje->sat_oxigeno,
            'temp_bucal' => $triaje->temp,
            'ocular' => $triaje->resp_ocular,
            'verbal' => $triaje->resp_verbal,
            'motora' => $triaje->resp_motora,
            'total_glas' => $glasgow,
            'glasgow_inicial' => $glasgow,
            'reac_pupila_der' => $reaccionPupilar,
            'reac_pupila_izq' => $reaccionPupilar,
            't_llenado_capilar' => $triaje->llenado_capilar,
        ));

        $historia->update(array(
            'presion' => $this->bloodPressure($triaje->presion_sistolica, $triaje->presion_diastolica),
            'pulso' => $triaje->frec_cardiaca,
            'frecuencia_respiratoria' => $triaje->frec_resp,
            'temperatura' => $triaje->temp,
            'o2' => $triaje->sat_oxigeno,
            'altura' => $triaje->talla,
            'peso' => $triaje->peso,
            'glasgow' => $glasgow,
            'ocular' => $triaje->resp_ocular,
            'verbal' => $triaje->resp_verbal,
            'motora' => $triaje->resp_motora,
            'reaccion_pupila_der' => $reaccionPupilar,
            'reaccion_pupila_izq' => $reaccionPupilar,
            't_llenado_capilar' => $triaje->llenado_capilar,
        ));

        return true;
    }

    private function bloodPressure($systolic, $diastolic)
    {
        if ($systolic === null && $diastolic === null) {
            return null;
        }

        return trim($systolic) . ' / ' . trim($diastolic);
    }

    private function yesNo($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return in_array(strtolower(trim((string) $value)), array('1', 'si', 'sí', 'yes'), true) ? 'Si' : 'No';
    }
}
