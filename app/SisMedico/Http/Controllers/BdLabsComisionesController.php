<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Ordenes_Ventas;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen_Comision;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\GrupoPregunta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\Paciente;
use Sis_medico\Preguntas_Labs;

class BdLabsComisionesController extends Controller
{

    public function ordenes_externas(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $cod_doctor = $request->cod_doctor;
        $actualizado = false;
        $estado = $request->estado; //0=eliminado, 1= orden realizada, -1= cotizacion

        $ordenes_externos = Examen_Orden::whereNotNull('examen_orden.codigo')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->join('labs_doc_externos as lde', 'lde.id', 'examen_orden.codigo')->join('paciente as p', 'p.id', 'examen_orden.id_paciente')->where('lde.tipo', 'like', '%COMISION%');

        if (!is_null($fecha_desde)) {
            $ordenes_externos = $ordenes_externos->whereBetween('examen_orden.fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $ordenes_externos = $ordenes_externos->where('examen_orden.fecha_orden', '<=', $fecha_hasta . ' 23:59:59');
        }

        if (!is_null($cod_doctor)) {
            $ordenes_externos = $ordenes_externos->where('examen_orden.codigo', $cod_doctor);
        }

        if (!is_null($estado)) {
            $ordenes_externos = $ordenes_externos->where('examen_orden.estado', $estado);
        }


        $ordenes_externos = $ordenes_externos->select('examen_orden.*', 's.nombre as nombre_seguro', 'lde.id as codigo_doc', 'lde.nombre1 as dnombre1', 'lde.apellido1 as dapellido1','lde.apellido2 as dapellido2', 'p.nombre1 as pnombre1', 'p.apellido1 as papellido1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2')->orderBy('examen_orden.fecha_orden', 'desc')->get();
        $arr_ord = [];
        foreach ($ordenes_externos as $value) {
            if ($value->estado == 1) {
                $detalles = $value->detalles;
                $actualizado = false;
                if (count($detalles) > 0) {
                    $total_comision = 0;
                    foreach ($detalles as $detalle) {
                        $comision = Examen_Comision::where('id_examen', $detalle->id_examen)
                            ->where('id_doctor_externo', $value->codigo)->first();

                        if (!is_null($comision)) {
                            $actualizado = true;

                            $valor_comision = $detalle->valor * ($comision->valor_comision / 100); //$comision->valor_comision es el porcentaje de la comision
                            $array_detalle = [
                                'valor_comision' => $valor_comision,
                            ];
                            $total_comision += $valor_comision;
                            $detalle->update($array_detalle);
                        }
                    }

                    if ($actualizado) {
                        $array_orden = [
                            'estado_comision2' => 1,
                            'total_comision' => $total_comision,
                        ];
                        $ordenM = Examen_Orden::find($value->id);
                        if (!is_null($ordenM)) {
                            $ordenM->update($array_orden);
                        }
                    }
                }
            }

            $data['id'] = $value->id;
            $data['id_paciente'] = $value->id_paciente;
            $data['nombre_paciente'] = $value->papellido1 . ' ' . $value->papellido2 . ' ' .  $value->pnombre1  . ' ' . $value->pnombre2;
            $data['anio'] = $value->anio;
            $data['mes'] = $value->mes;
            $data['id_seguro'] = $value->id_seguro;
            $data['nombre_seguro'] = $value->nombre_seguro;
            $data['cantidad'] = $value->cantidad;
            $data['total_valor'] = $value->total_valor;
            $data['fecha_orden'] = $value->fecha_orden;
            $data['doctor_txt'] = $value->doctor_txt;
            $data['estado'] = $value->estado;
            $data['codigo'] = $value->codigo;
            $data['nombre_cod_doc'] =  $value->dapellido1 . ' ' . $value->dapellido2 . ' '. $value->dnombre1;
            $data['estado_comision2'] = $value->estado_comision2;
            $data['total_comision'] = $value->total_comision;

            array_push($arr_ord, $data);
        }

        return $arr_ord;
    }

    public function lista_codigos_externos(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $externos = Labs_doc_externos::where('estado', 1)->where('tipo','like', '%COMISION%')->get();

        return $externos;
    }

    
}
