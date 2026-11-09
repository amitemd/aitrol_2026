<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Hc_Log;
use Sis_medico\Ho_Ordenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Orden;

class EndoscopicOrderService
{
    public function initialize($patientId, $actorId, $ip, $procedureType = 0)
    {
        return DB::transaction(function () use ($patientId, $actorId, $ip, $procedureType) {
            $evolution = DB::table('historiaclinica as h')
                ->where('h.id_paciente', $patientId)
                ->join('hc_evolucion as e', 'e.hcid', 'h.hcid')
                ->where('e.secuencia', 0)
                ->whereNotNull('e.cuadro_clinico')
                ->orderBy('h.hcid', 'desc')
                ->select('e.*')
                ->first();

            $diagnoses = '';
            if ($evolution) {
                $rows = DB::table('hc_cie10')->where('hc_id_procedimiento', $evolution->hc_id_procedimiento)
                    ->groupBy('cie10')->get();
                $labels = array();
                foreach ($rows as $row) {
                    $catalogue = Cie_10_3::find($row->cie10) ?: Cie_10_4::find($row->cie10);
                    $labels[] = $row->cie10.':'.($catalogue ? $catalogue->descripcion : 'SIN DESCRIPCIÓN')
                        .'-'.$row->presuntivo_definitivo;
                }
                $diagnoses = implode('<br>', $labels);
            }

            Hc_Log::create(array(
                'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
                'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
                'id_paciente' => $patientId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));

            // create() dispara el observador de Orden y sincroniza F-04 inmediatamente.
            return Orden::create(array(
                'id_paciente' => $patientId,
                'id_doctor' => $actorId,
                'id_evolucion' => $evolution ? $evolution->id : null,
                'motivo_consulta' => $evolution ? $evolution->motivo : null,
                'resumen_clinico' => $evolution ? $evolution->cuadro_clinico : null,
                'diagnosticos' => $diagnoses,
                'fecha_orden' => date('Y-m-d H:i:s'),
                'tipo_procedimiento' => $procedureType,
                'anio' => date('Y'),
                'mes' => date('m'),
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));
        });
    }

    public function initializeForEmergency($requestId, $procedureType, $actorId, $ip)
    {
        return DB::transaction(function () use ($requestId, $procedureType, $actorId, $ip) {
            $request = Ho_Solicitud::where('id', $requestId)->lockForUpdate()->firstOrFail();
            if ((int) $procedureType === 0 && (string) $request->estado_paso === '1' && $request->form008->count()) {
                $request->form008->first()->update(array('endoscopia' => 'X'));
            }
            $order = $this->initialize($request->id_paciente, $actorId, $ip, $procedureType);
            Ho_Ordenes::create(array(
                'clave_tipo' => $order->id,
                'id_ho_solicitud' => $requestId,
                'tipo' => 'PROCEDIMIENTO',
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
            ));
            return $order;
        });
    }

    public function cancel($orderId, $actorId, $ip)
    {
        return DB::transaction(function () use ($orderId, $actorId, $ip) {
            $order = Orden::where('id', $orderId)->lockForUpdate()->firstOrFail();
            $order->estado = 0;
            $order->id_usuariomod = $actorId;
            $order->ip_modificacion = $ip;
            $order->save();

            Hc_Log::create(array(
                'anterior' => "ORDEN_PROC_ENDOSCOPICOS -> El Dr. eliminó la orden del procedimiento endoscópico #$orderId",
                'nuevo' => "ORDEN_PROC_ENDOSCOPICOS -> El Dr. eliminó la orden del procedimiento endoscópico #$orderId",
                'id_paciente' => $order->id_paciente,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));

            return $order;
        });
    }
}
