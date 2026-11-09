<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;

class ClinicalPatientJourneyService
{
    public function forHospitalization(Ho_Hospitalizacion $admission)
    {
        $events = array();
        $solicitud = $this->solicitud($admission);
        $encounterId = !is_null($solicitud) && isset($solicitud->encounter_id) ? $solicitud->encounter_id : null;

        if (!is_null($solicitud) && Schema::hasTable('ho_log_solicitud')) {
            $logs = DB::table('ho_log_solicitud')
                ->where('id_ho_solicitud', $solicitud->id)
                ->orderBy('fecha_ingreso')->orderBy('id')->get();
            foreach ($logs as $log) {
                $step = $this->legacyStep((int) $log->estado_paso);
                if (is_null($step)) continue;
                $events[] = $this->event($step['key'], $step['label'], $step['detail'], $step['icon'],
                    $log->fecha_ingreso ?: $log->created_at, 'completed');
            }
        }

        if (!is_null($encounterId) && Schema::hasTable('clinical_orders')) {
            $orders = DB::table('clinical_orders')->where('encounter_id', $encounterId)
                ->whereIn('category', array('LABORATORY', 'IMAGING', 'PROCEDURE'))->orderBy('ordered_at')->get();
            foreach ($orders as $order) {
                $category = $this->orderCategory($order->category);
                $detail = trim((string) $order->description);
                if ($detail === '') $detail = $category['detail'];
                $status = in_array($order->status, array('COMPLETED', 'CANCELLED')) ? 'completed' : 'attention';
                $events[] = $this->event($category['key'], $category['label'], $detail, $category['icon'],
                    $order->completed_at ?: ($order->ordered_at ?: $order->created_at), $status);
            }
        }

        if (Schema::hasTable('clinical_bed_transfers')) {
            $transfers = DB::table('clinical_bed_transfers as t')
                ->leftJoin('habitacion as h', 'h.id', '=', 't.to_room_id')
                ->leftJoin('cama as c', 'c.id', '=', 't.to_bed_id')
                ->where('t.hospitalization_id', $admission->id)
                ->select('t.*', 'h.codigo as room_code', 'c.codigo as bed_code')
                ->orderBy('t.transferred_at')->get();
            foreach ($transfers as $transfer) {
                $detail = 'Habitación ' . ($transfer->room_code ?: $transfer->to_room_id)
                    . ' · Cama ' . ($transfer->bed_code ?: $transfer->to_bed_id);
                $events[] = $this->event('bed-transfer-' . $transfer->id, 'Traslado interno', $detail,
                    'fa-exchange', $transfer->transferred_at, 'completed');
            }
        }

        if (!count($events)) {
            $events[] = $this->event('admission', 'Ingreso hospitalario', 'Admisión y asignación inicial',
                'fa-sign-in', $admission->fecha ?: $admission->created_at, 'completed');
        }

        usort($events, function ($left, $right) {
            return strcmp((string) $left['sort_at'], (string) $right['sort_at']);
        });
        $events = $this->deduplicate($events);

        $location = $this->currentLocation($admission);
        $events[] = $this->event('current-location', 'Ubicación actual', $location, 'fa-bed',
            date('Y-m-d H:i:s'), 'current');

        return array(
            'events' => $events,
            'current_location' => $location,
            'encounter_id' => $encounterId,
        );
    }

    private function solicitud(Ho_Hospitalizacion $admission)
    {
        if (empty($admission->id_traspaso)) return null;
        $transfer = Ho_Traspaso_Sala008::find($admission->id_traspaso);
        if (is_null($transfer) || empty($transfer->id_solicitud)) return null;
        return Ho_Solicitud::find($transfer->id_solicitud);
    }

    private function legacyStep($step)
    {
        $steps = array(
            0 => array('key' => 'ambulatory', 'label' => 'Atención ambulatoria', 'detail' => 'Consulta externa', 'icon' => 'fa-user-md'),
            2 => array('key' => 'emergency', 'label' => 'Emergencia', 'detail' => 'Ingreso y estabilización', 'icon' => 'fa-ambulance'),
            3 => array('key' => 'hospitalization', 'label' => 'Hospitalización', 'detail' => 'Ingreso a sala', 'icon' => 'fa-hospital-o'),
            4 => array('key' => 'imaging', 'label' => 'Imágenes', 'detail' => 'Diagnóstico por imagen', 'icon' => 'fa-picture-o'),
            5 => array('key' => 'surgery', 'label' => 'Cirugía', 'detail' => 'Atención quirúrgica', 'icon' => 'fa-medkit'),
            6 => array('key' => 'icu', 'label' => 'UCI', 'detail' => 'Cuidados intensivos', 'icon' => 'fa-heartbeat'),
            7 => array('key' => 'nicu', 'label' => 'UCIN', 'detail' => 'Cuidados intensivos neonatales', 'icon' => 'fa-heartbeat'),
        );
        return isset($steps[$step]) ? $steps[$step] : null;
    }

    private function orderCategory($category)
    {
        if ($category === 'LABORATORY') return array('key' => 'laboratory', 'label' => 'Laboratorio', 'detail' => 'Toma y procesamiento de muestras', 'icon' => 'fa-flask');
        if ($category === 'IMAGING') return array('key' => 'imaging-order', 'label' => 'Imágenes', 'detail' => 'Estudio de imagen', 'icon' => 'fa-picture-o');
        return array('key' => 'procedure', 'label' => 'Procedimiento', 'detail' => 'Procedimiento clínico', 'icon' => 'fa-stethoscope');
    }

    private function currentLocation(Ho_Hospitalizacion $admission)
    {
        $row = DB::table('cama as c')->leftJoin('habitacion as h', 'h.id', '=', 'c.id_habitacion')
            ->leftJoin('piso as p', 'p.id', '=', 'h.id_piso')->where('c.id', $admission->id_cama)
            ->select('c.codigo as bed_code', 'h.codigo as room_code', 'p.nombre_piso as floor_name')->first();
        if (is_null($row)) return 'Hospitalización · ubicación por confirmar';
        $parts = array();
        if (!empty($row->floor_name)) $parts[] = $row->floor_name;
        if (!empty($row->room_code)) $parts[] = 'Habitación ' . $row->room_code;
        if (!empty($row->bed_code)) $parts[] = 'Cama ' . $row->bed_code;
        return count($parts) ? implode(' · ', $parts) : 'Hospitalización';
    }

    private function event($key, $label, $detail, $icon, $timestamp, $status)
    {
        $formatted = 'Hora no registrada';
        $sortAt = $timestamp ?: '0000-00-00 00:00:00';
        if (!empty($timestamp)) {
            $time = strtotime($timestamp);
            if ($time !== false) $formatted = date('d/m/Y H:i', $time);
        }
        return array('key' => $key, 'label' => $label, 'detail' => $detail, 'icon' => $icon,
            'at' => $formatted, 'sort_at' => $sortAt, 'status' => $status);
    }

    private function deduplicate(array $events)
    {
        $result = array();
        foreach ($events as $event) {
            $last = count($result) ? $result[count($result) - 1] : null;
            if (!is_null($last) && $last['key'] === $event['key'] && $last['detail'] === $event['detail']) continue;
            $result[] = $event;
        }
        return $result;
    }
}
