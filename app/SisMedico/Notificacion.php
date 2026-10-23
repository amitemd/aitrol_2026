<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Notificacion
{
    public static function notificacion()
    {
        $dataNotificacion = [];
        $cantidad = 0;
        $data = complaint::where('notificacion', 1)->get();
        $cantidad += count($data);
        foreach ($data as $d) {
            array_push($dataNotificacion, [
                "name" => "Complaint",
                "message" => "Se ha agregado un nuevo complaint",
                "url" => route('complaint.show', $d->id),
                "time" => Notificacion::dateCount($d->created_at)
            ]);
        }
        $proformaPreAprobada = Notificacion::notificationProformaPreAprobada();
        foreach ($proformaPreAprobada["data"] as $d) {
            array_push($dataNotificacion, $d);
        }
        $cantidad += $proformaPreAprobada["cantidad"];

        return ["cantidad" => $cantidad, "data" => $dataNotificacion];
    }

    public static function notificationProformaPreAprobada()
    {

        if(Auth::user()->id_tipo_usuario == 17){
            return ["cantidad" => 0, "data" => []];
        }

        $proforma = Proforma_Venta_Cab::where('pre_aprobada', 1)->where('id_empresa', Session::get('id_empresa'))->where('estado', 2)->get();

        if (count($proforma) == 0) {
            return ["cantidad" => 0, "data" => []];
        }

        $dataNotificacion = [];
        $cantidad = 0;
        $cantidad += count($proforma);




        foreach ($proforma as $p) {
            $text = "Proforma";

            if ($p->tipo == 3) {
                $text = "Proforma Publica";
            }
            array_push($dataNotificacion, [
                "name" => $text,
                "message" => "Se ha agregado una nueva " . $text,
                "url" => route('insumos.proformaInsumos.editar', $p->id),
                "time" => Notificacion::dateCount($p->created_at)
            ]);
        }
        return ["cantidad" => $cantidad, "data" => $dataNotificacion];
    }

    public static function dateCount($fechaData)
    {
        try {
            $fechaActual = new \DateTime();
            $fechaData = new \DateTime($fechaData);
            $interval = $fechaData->diff($fechaActual);
            if ($interval->y > 0) {
                return "Hace " . intval($interval->y) . " años";
            }
            if ($interval->m > 0) {
                return "Hace " . intval($interval->m) . " meses";
            }

            if ($interval->d > 0) {
                return "Hace " . intval($interval->d) . " dias";
            }
            if ($interval->h > 0) {
                return "Hace " . intval($interval->h) . " horas";
            }
            if ($interval->i > 0) {
                return "Hace " . intval($interval->i) . " minutos";
            }
            if ($interval->s > 0) {
                return "Hace " . intval($interval->s) . " segundos";
            }
        } catch (\Exception $e) {
            return "error date";
        }
    }
}
