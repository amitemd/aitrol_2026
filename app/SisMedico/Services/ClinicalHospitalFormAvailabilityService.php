<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

class ClinicalHospitalFormAvailabilityService
{
    const FORM_001 = '001';
    const FORM_005 = '005';
    const FORM_008 = '008';

    /**
     * Determina qué formularios tienen información clínica suficiente para
     * mostrarse. Los registros creados automáticamente pero todavía vacíos no
     * habilitan el botón de vista previa.
     */
    public function forSolicitudes(array $solicitudIds)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $solicitudIds))));
        $availability = array();
        foreach ($ids as $id) {
            $availability[$id] = array(
                self::FORM_001 => false,
                self::FORM_005 => false,
                self::FORM_008 => false,
            );
        }

        if (!count($ids)) {
            return $availability;
        }

        $evolutions = DB::table('ho_solicitud as solicitud')
            ->join('agenda as agenda', 'agenda.id', '=', 'solicitud.id_agenda')
            ->join('historiaclinica as historia', 'historia.id_agenda', '=', 'agenda.id')
            ->join('hc_evolucion as evolucion', 'evolucion.hcid', '=', 'historia.hcid')
            ->whereIn('solicitud.id', $ids)
            ->select('solicitud.id as solicitud_id', 'evolucion.motivo', 'evolucion.cuadro_clinico')
            ->get();

        foreach ($evolutions as $evolution) {
            if ($this->filled($evolution->motivo) || $this->filled($evolution->cuadro_clinico)) {
                $availability[(int) $evolution->solicitud_id][self::FORM_005] = true;
            }
        }

        $emergencyForms = DB::table('ho_form008')
            ->whereIn('id_solicitud', $ids)
            ->select('id_solicitud', 'motivo', 'condicion')
            ->get();

        foreach ($emergencyForms as $form) {
            // Coincide con los campos clínicos mínimos exigidos al F-008.
            if ($this->filled($form->motivo) && $this->filled($form->condicion)) {
                $availability[(int) $form->id_solicitud][self::FORM_008] = true;
            }
        }

        $admissionIds = DB::table('ho_log_solicitud as ingreso')
            ->join('agenda as agenda', 'agenda.id', '=', 'ingreso.id_agenda')
            ->join('historiaclinica as historia', 'historia.id_agenda', '=', 'agenda.id')
            ->whereIn('ingreso.id_ho_solicitud', $ids)
            ->whereNotNull('ingreso.id_agenda')
            ->distinct()
            ->pluck('ingreso.id_ho_solicitud');

        foreach ($admissionIds as $id) {
            $availability[(int) $id][self::FORM_001] = true;
        }

        return $availability;
    }

    public function isAvailable($solicitudId, $form)
    {
        $availability = $this->forSolicitudes(array($solicitudId));
        return isset($availability[(int) $solicitudId][$form])
            && $availability[(int) $solicitudId][$form] === true;
    }

    private function filled($value)
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
        $text = trim(str_replace("\xC2\xA0", ' ', $text));

        return $text !== '' && strtoupper($text) !== 'SELECCIONE';
    }
}
