<?php

namespace Sis_medico\Services;

use Sis_medico\Ho_Solicitud;

class ClinicalEmergencyVitalSignsService
{
    /**
     * Devuelve los signos vigentes del episodio. Los valores registrados en
     * el paso de signos vitales tienen prioridad; si aun no existen, se usan
     * los del primer triaje realizado para la solicitud.
     */
    public function values(Ho_Solicitud $solicitud, $form008 = null, $historia = null)
    {
        if (is_null($form008)) {
            $form008 = $solicitud->form008()->first();
        }

        if (is_null($historia) && !is_null($form008) && !is_null($form008->agenda)) {
            $historia = $form008->agenda->historia_clinica;
        }

        $triaje = $this->firstTriage($solicitud);

        return array(
            'pressure' => $this->firstMeaningful(array(
                optional($historia)->presion,
                $this->bloodPressure(optional($triaje)->presion_sistolica, optional($triaje)->presion_diastolica),
            )),
            'pulse' => $this->firstMeaningful(array(
                optional($historia)->pulso,
                optional($triaje)->frec_cardiaca,
            )),
            'respiratory' => $this->firstMeaningful(array(
                optional($historia)->frecuencia_respiratoria,
                optional($form008)->frec_respiratoria,
                optional($triaje)->frec_resp,
            )),
            'temperature' => $this->firstMeaningful(array(
                optional($historia)->temperatura,
                optional($form008)->temp_bucal,
                optional($form008)->temp_axilar,
                optional($triaje)->temp,
            )),
            'oxygen' => $this->firstMeaningful(array(
                optional($historia)->o2,
                optional($form008)->pulsioximetria,
                optional($form008)->satura_oxigeno,
                optional($triaje)->sat_oxigeno,
            )),
            'height' => $this->firstMeaningful(array(
                optional($historia)->altura,
                optional($triaje)->talla,
            )),
            'weight' => $this->firstMeaningful(array(
                optional($historia)->peso,
                optional($triaje)->peso,
            )),
        );
    }

    public function summary(Ho_Solicitud $solicitud, $form008 = null, $historia = null)
    {
        $values = $this->values($solicitud, $form008, $historia);

        return array(
            'pressure' => $this->display($values['pressure']),
            'pulse' => $this->display($values['pulse']),
            'respiratory' => $this->display($values['respiratory']),
            'temperature' => $this->display($values['temperature'], ' °C'),
            'oxygen' => $this->display($values['oxygen']),
            'height' => $this->display($values['height']),
            'weight' => $this->display($values['weight']),
        );
    }

    public function firstTriage(Ho_Solicitud $solicitud)
    {
        return $solicitud->manchester()->orderBy('id', 'asc')->first();
    }

    private function firstMeaningful(array $values)
    {
        foreach ($values as $value) {
            if ($this->isMeaningful($value)) {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function bloodPressure($systolic, $diastolic)
    {
        if (!$this->isMeaningful($systolic) && !$this->isMeaningful($diastolic)) {
            return null;
        }

        return trim((string) $systolic) . ' / ' . trim((string) $diastolic);
    }

    private function display($value, $suffix = '')
    {
        return $this->isMeaningful($value) ? trim((string) $value) . $suffix : 'No registrado';
    }

    private function isMeaningful($value)
    {
        if (is_null($value)) {
            return false;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        // En estos registros antiguos 0, 0.00 y 0 / 0 son valores por defecto.
        return !preg_match('/^[\s0\.\,\/\-]+$/', $value);
    }
}
