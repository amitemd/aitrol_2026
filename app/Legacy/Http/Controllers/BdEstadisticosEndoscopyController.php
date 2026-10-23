<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use FFI\CType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Camilla;
use Sis_medico\Camilla_Gestion;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Empresa_Relacionada;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\Ct_Ordenes_Ventas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Resultado;
use Sis_medico\GrupoPregunta;
use Sis_medico\Habitacion;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Hospital;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\Paciente;
use Sis_medico\Piso;
use Sis_medico\Preguntas_Labs;
use Sis_medico\Sala;
use Sis_medico\Seguro;

class BdEstadisticosEndoscopyController extends Controller
{
    public function consolidado_consultas_proc(Request $request)
    {

        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $tipo = $request->tipo;

        $ordenes = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1');

        if (!is_null($fecha_desde)) {
            $ordenes = $ordenes->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $ordenes = $ordenes->where('agenda.fechaini', '<=', $fecha_hasta);
        }

        if (is_null($tipo)) {

            $ordenes = $ordenes->whereIn('agenda.proc_consul', [0, 1]);
        } else {
            if ($tipo == 0) {
                $ordenes = $ordenes->where('agenda.proc_consul', '0');
            }
            if ($tipo == 1) {
                $ordenes = $ordenes->where('agenda.proc_consul', '1');
            }
        }



        $ordenes = $ordenes->groupBy('st.id')->select("s.id_seguro_tipos", "st.nombre", DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), "agenda.fechaini")->get();

        $total_estimado_new = BigDataController::valor_publico($fecha_desde, $fecha_hasta);

        foreach ($ordenes as $orden) {

            if ($orden->id_seguro_tipos == 2) {
                $data["privado"] = $orden->total;
            }
            if ($orden->id_seguro_tipos == 3) {
                $data["particular"]  = $orden->total;
            }
            if ($orden->id_seguro_tipos == 4) {
                $data["promo"] = $orden->total;
            }
            if ($orden->id_seguro_tipos == 5) {
                $data["paquetes_promo"] = $orden->total;
            }
        }


        if (!isset($data["privado"])) {
            $data["privado"] = 0;
        }

        if (!isset($data["particular"])) {
            $data["particular"] = 0;
        }


        if (!isset($data["promo"])) {
            $data["promo"] = 0;
        }

        if (!isset($data["paquetes_promo"])) {
            $data["paquetes_promo"] = 0;
        }

        $data["publico_estimado"] = round($total_estimado_new, 2);

        return $data;
    }

    public function newConsolidadoConsultasProc(Request $request)
    {
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $tipo = $request->tipo;
        $ordenes = Agenda::selectRaw("s.id_seguro_tipos, st.nombre, st.abreviatura,Ifnull(SUM(Ifnull(ov.total,0)),0) as total , count(agenda.id) as cantidad, Ifnull(SUM(Ifnull(ov.total_cobrar_seguro,0)),0) as copago")
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->whereNotNull("s.id_seguro_tipos")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ov.fecha_emision', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ov.fecha_emision', '<=', $fecha_hasta);
                }
            })
            ->where(function ($r) use ($tipo) {
                if (is_null($tipo)) {

                    $r = $r->whereIn('agenda.proc_consul', [0, 1]);
                } else {
                    if ($tipo == 0) {
                        $r = $r->where('agenda.proc_consul', '0');
                    } else {
                        $r = $r->where('agenda.proc_consul', '1');
                    }
                }
            })
            ->groupBy('st.id')->get();

        $archivo_plano = Archivo_Plano_Cabecera::selectRaw('Ifnull(IF(archivo_plano_cabecera.id_seguro = 5 , SUM(Ifnull(apd.valor_unitario,0) * Ifnull(apd.porcentaje_iva,0) * Ifnull(apd.cantidad,0)),0) + IF(archivo_plano_cabecera.id_seguro = 5 , SUM(Ifnull(apd.subtotal,0) + Ifnull(apd.valor_porcent_clasifi,0)), SUM(Ifnull(apd.subtotal,0))) + IF(archivo_plano_cabecera.id_seguro = 6 OR archivo_plano_cabecera.id_seguro = 3 , SUM(Ifnull(apd.subtotal,0) + Ifnull(apd.porcent_10,0)), SUM(Ifnull(apd.porcentaje10,0))),0) AS total')
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereBetween('archivo_plano_cabecera.fecha_ing', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->orderby('archivo_plano_cabecera.id_paciente')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->first();

        $sumtot = 0;
        $sum_copago = 0;
        foreach ($ordenes as $orden) {
            $sumtot += round($orden->total, 2) + round($orden->copago, 2);
            $sum_copago += round($orden->copago, 2);
            if ($orden->id_seguro_tipos == 2) {
                $data["privado"] = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 3) {
                $data["particular"]  = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 4) {
                $data["promo"] = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 5) {
                $data["paquetes_promo"] = round($orden->total, 2);
            }
        }
        $data['total'] = $sumtot;
        $data['copago'] = $sum_copago;

        if (!isset($data["privado"])) {
            $data["privado"] = 0;
        }
        if (!isset($data["particular"])) {
            $data["particular"] = 0;
        }
        if (!isset($data["promo"])) {
            $data["promo"] = 0;
        }
        if (!isset($data["paquetes_promo"])) {
            $data["paquetes_promo"] = 0;
        }
        $data['total_estimado'] = round($archivo_plano->total, 2);

        return $data;
    }

    public function prueba_estadistico(Request $request)
    {
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $tipo = $request->tipo;
        $ordenes = Agenda::selectRaw("s.id_seguro_tipos, st.nombre, st.abreviatura,Ifnull(SUM(Ifnull(ov.total,0)),0) as total , count(agenda.id) as cantidad, Ifnull(SUM(Ifnull(ov.total_cobrar_seguro,0)),0) as copago")
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->whereNotNull("s.id_seguro_tipos")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ov.fecha_emision', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ov.fecha_emision', '<=', $fecha_hasta);
                }
            })
            ->where(function ($r) use ($tipo) {
                if (is_null($tipo)) {

                    $r = $r->whereIn('agenda.proc_consul', [0, 1]);
                } else {
                    if ($tipo == 0) {
                        $r = $r->where('agenda.proc_consul', '0');
                    } else {
                        $r = $r->where('agenda.proc_consul', '1');
                    }
                }
            })
            ->groupBy('st.id')->get();

        //return $ordenes;

        $archivo_plano = Archivo_Plano_Cabecera::selectRaw('Ifnull(IF(archivo_plano_cabecera.id_seguro = 5 , SUM(Ifnull(apd.valor_unitario,0) * Ifnull(apd.porcentaje_iva,0) * Ifnull(apd.cantidad,0)),0) + IF(archivo_plano_cabecera.id_seguro = 5 , SUM(Ifnull(apd.subtotal,0) + Ifnull(apd.valor_porcent_clasifi,0)), SUM(Ifnull(apd.subtotal,0))) + IF(archivo_plano_cabecera.id_seguro = 6 OR archivo_plano_cabecera.id_seguro = 3 , SUM(Ifnull(apd.subtotal,0) + Ifnull(apd.porcent_10,0)), SUM(Ifnull(apd.porcentaje10,0))),0) AS total')
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereBetween('archivo_plano_cabecera.fecha_ing', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->orderby('archivo_plano_cabecera.id_paciente')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->first();

        $sumtot = 0;
        $sum_copago = 0;
        foreach ($ordenes as $orden) {
            $sumtot += round($orden->total, 2) + round($orden->copago, 2);
            $sum_copago += round($orden->copago, 2);
            if ($orden->id_seguro_tipos == 2) {
                $data["privado"] = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 3) {
                $data["particular"]  = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 4) {
                $data["promo"] = round($orden->total, 2);
            }
            if ($orden->id_seguro_tipos == 5) {
                $data["paquetes_promo"] = round($orden->total, 2);
            }
        }
        $data['total'] = $sumtot;
        $data['copago'] = $sum_copago;

        if (!isset($data["privado"])) {
            $data["privado"] = 0;
        }
        if (!isset($data["particular"])) {
            $data["particular"] = 0;
        }
        if (!isset($data["promo"])) {
            $data["promo"] = 0;
        }
        if (!isset($data["paquetes_promo"])) {
            $data["paquetes_promo"] = 0;
        }
        $data['total_estimado'] = round($archivo_plano->total, 2);

        return $data;
    }

    public function detalle_consultasProc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $tipo = $request->tipo;
        $tipo_seguro = $request->tipo_seguro;
        $ordenes = Agenda::selectRaw("s.id as id_seguro, s.nombre, COUNT(ov.id) as cantidad ,IFNULL(sum(IFNULL(ov.total,0)),0) as total, IFNULL(sum(IFNULL(ov.total_cobrar_seguro,0)),0) as copago, IFNULL(sum(IFNULL(ov.total,0)),0)+IFNULL(sum(IFNULL(ov.total_cobrar_seguro,0)),0) as total_valor")
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->whereNotNull("s.id_seguro_tipos")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1')
            ->where('s.id_seguro_tipos', $tipo_seguro)
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ov.fecha_emision', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ov.fecha_emision', '<=', $fecha_hasta);
                }
            })
            ->where(function ($r) use ($tipo) {
                if (is_null($tipo)) {

                    $r = $r->whereIn('agenda.proc_consul', [0, 1]);
                } else {
                    if ($tipo == 0) {
                        $r = $r->where('agenda.proc_consul', '0');
                    } else {
                        $r = $r->where('agenda.proc_consul', '1');
                    }
                }
            })
            ->groupBy('s.id')
            ->get();

        $arr = array();
        foreach ($ordenes as  $value) {
            $data['id_seguro'] = $value->id_seguro;
            $data['nombre_seguro'] = $value->nombre;
            $data['cantidad'] = $value->cantidad;
            $data['total'] = $value->total;
            if ($tipo_seguro == '2') {
                $data["copago"] = $value->copago;
                $data['total_valor'] = $value->total_valor;
            }
            array_push($arr, $data);
        }

        return $arr;
    }

    public function total_proc_consul(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        $ordenes = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1')
            ->whereIn('agenda.proc_consul', [0, 1])
            ->whereYear('agenda.fechaini', $anio)
            ->groupBy(DB::raw('MONTH(agenda.fechaini)'))
            ->select(DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), DB::raw('MONTH(agenda.fechaini) as mes'), DB::raw('YEAR(agenda.fechaini) as anio'))
            ->get();

        $meses = array(
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        );

        $arr = [];
        foreach ($meses as $mes_num => $nombre_mes) {
            $orden_mes = $ordenes->where('mes', $mes_num)->first();
            $total = $orden_mes ? round($orden_mes->total, 4) : 0;

            $arr[] = [
                "mes" => $nombre_mes,
                "id_mes" => $mes_num,
                "total" => round($total, 2),
            ];
        }

        $sum_total = 0;
        foreach ($arr as $value) {
            $sum_total = $sum_total + $value["total"];
        }

        $data['total']  = round($sum_total, 4);
        $data['detalle'] = $arr;

        return $data;
    }

    public function total_ordenes_labs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        $examen_orden = Examen_Orden::where('examen_orden.realizado', '1')
            ->where('examen_orden.estado', '1')
            ->whereYear('examen_orden.fecha_orden', $anio)
            ->groupBy(DB::raw('MONTH(examen_orden.fecha_orden)'))
            ->select(DB::raw('sum(examen_orden.total_valor) as total'), 'examen_orden.mes', 'examen_orden.anio')
            ->get();

        $meses = array(
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        );

        $arr = [];
        foreach ($meses as $mes_num => $nombre_mes) {
            $orden_mes = $examen_orden->where('mes', $mes_num)->first();
            $total = $orden_mes ? round($orden_mes->total, 4) : 0;

            $arr[] = [
                "mes" => $nombre_mes,
                "id_mes" => $mes_num,
                "total" => round($total, 2),
            ];
        }

        $sum_total = 0;
        foreach ($arr as $value) {
            $sum_total = $sum_total + $value["total"];
        }

        $data['total']  = round($sum_total, 4);
        $data['detalle'] = $arr;

        return $data;
    }

    public function cons_procxseguro(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }

        $ordenes = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1')
            ->whereIn('agenda.proc_consul', [0, 1])
            ->whereYear('agenda.fechaini', $anio)
            ->groupBy(DB::raw('MONTH(agenda.fechaini)'), 'st.id')
            ->select(DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), DB::raw('MONTH(agenda.fechaini) as mes'), DB::raw('YEAR(agenda.fechaini) as anio'), "s.id_seguro_tipos", "st.nombre as tipo_seguro")
            ->get();

        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
        $arr = [];
        foreach ($ordenes as $resultado) {
            $mes = $resultado->mes;
            $nombreMes = $meses[$mes];
            $tipoSeguro = $resultado->tipo_seguro;
            $total = floatval($resultado->total);
            $tot = round(isset($arr[$mes]['detalles'][$tipoSeguro]['total']) ? $arr[$mes]['detalles'][$tipoSeguro]['total'] + $total : $total, 2);
            $detalle = [
                'nombre_tipo_seguro' => $tipoSeguro,
                'total' => round($tot, 2),
            ];

            $arr[$mes]['mes'] = $mes;
            $arr[$mes]['nombre_mes'] = $nombreMes;
            $arr[$mes]['detalles'][] = $detalle;
        }


        return  array_values($arr);
    }

    public function labs_ordenesxseguro(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        $examen_orden = Examen_Orden::where('examen_orden.realizado', '1')
            ->where('examen_orden.estado', '1')
            ->whereYear('examen_orden.fecha_orden', $anio)
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->groupBy(DB::raw('MONTH(examen_orden.fecha_orden)'), 's.tipo')
            ->select(DB::raw('sum(examen_orden.total_valor) as total'), 'examen_orden.mes', 'examen_orden.anio', 's.tipo as tipo_seguro')
            ->get();
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $arr = [];
        foreach ($examen_orden as $resultado) {
            $mes = $resultado->mes;
            $nombreMes = $meses[$mes];

            $tipoSeguro = $resultado->tipo_seguro;
            $total = round($resultado->total, 2);
            $nombre_tipo = "";
            if ($tipoSeguro == 1) {
                $nombre_tipo = "Privado";
            }
            if ($tipoSeguro == 2) {
                $nombre_tipo = "Particular";
            }
            if ($tipoSeguro == 0) {
                $nombre_tipo = "Publico";
            }

            $detalle = [
                'nombre_tipo_seguro' => $nombre_tipo,
                'total' => round(isset($arr[$mes]['detalles'][$tipoSeguro]['total']) ? $arr[$mes]['detalles'][$tipoSeguro]['total'] + $total : $total, 2),
            ];

            $arr[$mes]['mes'] = $mes;
            $arr[$mes]['nombre_mes'] = $nombreMes;
            $arr[$mes]['detalles'][] = $detalle;
        }

        return  array_values($arr);
    }

    public function facturado_cons_proc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $ventas = Ct_ventas::where('id_empresa', $empresa->id)
            ->where('ct_ventas.estado', 1)
            ->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->whereYear('ct_ventas.fecha', $anio)
            ->groupBy(DB::raw('MONTH(ct_ventas.fecha)'))
            ->select(DB::raw('SUM(((ct_ventas.subtotal_12+ct_ventas.subtotal_0) - ct_ventas.descuento)) as total'), DB::raw('MONTH(ct_ventas.fecha) as mes'), DB::raw('YEAR(ct_ventas.fecha) as anio'))
            ->get();

        // return $ventas;

        $meses = array(
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        );

        $arr = [];

        foreach ($meses as $mes_num => $nombre_mes) {
            $ventas_mes = $ventas->where('mes', $mes_num)->first();
            $total = $ventas_mes ? round($ventas_mes->total, 4) : 0;

            $arr[] = [
                "mes" => $nombre_mes,
                "id_mes" => $mes_num,
                "total" => round($total, 2),
            ];
        }

        $sum_total = 0;
        foreach ($arr as $value) {
            $sum_total = $sum_total + $value["total"];
        }

        $data['total']  = round($sum_total, 4);
        $data['detalle'] = $arr;

        return $data;
    }

    public function facturadoxseg(Request $request)
    {
        $anio = $request['anio'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $ventas = Ct_ventas::where('ct_ventas.id_empresa', $empresa->id)
            ->join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
            ->where('ct_ventas.estado', 1)
            ->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->whereYear('ct_ventas.fecha', $anio)
            ->groupBy(DB::raw('MONTH(ct_ventas.fecha)'), 's.tipo')
            ->select(DB::raw('SUM(((ct_ventas.subtotal_12+ct_ventas.subtotal_0) - ct_ventas.descuento)) as total'), DB::raw('MONTH(ct_ventas.fecha) as mes'), DB::raw('YEAR(ct_ventas.fecha) as anio'), 's.tipo', DB::raw("IF(s.tipo = 0, 'PUBLICO', IF(s.tipo = 1, 'SEG. PRIVADO', 'PARTICULAR') ) AS tipo_seguro"))
            ->get();
        // return $ventas;

        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $arr = [];

        // Inicializar el arreglo con valores predeterminados para cada mes
        foreach ($meses as $numMes => $nombreMes) {
            $arr[$numMes] = [
                'mes' => $numMes,
                'nombre_mes' => $nombreMes,
                'detalles' => []
            ];
        }

        // Procesar las ventas
        foreach ($ventas as $resultado) {
            $mes = $resultado->mes;
            $tipoSeguro = $resultado->tipo_seguro;
            $total = floatval($resultado->total);

            // Verificar si el tipo de seguro ya existe para ese mes
            if (!isset($arr[$mes]['detalles'][$tipoSeguro])) {
                $arr[$mes]['detalles'][] = [
                    'nombre_tipo_seguro' => $tipoSeguro,
                    'total' => 0
                ];
            }

            // Sumar el total al tipo de seguro correspondiente para ese mes
            foreach ($arr[$mes]['detalles'] as &$detalle) {
                if ($detalle['nombre_tipo_seguro'] === $tipoSeguro) {
                    $detalle['total'] += $total;
                    break;
                }
            }
        }

        // Redondear los totales
        foreach ($arr as &$mesInfo) {
            foreach ($mesInfo['detalles'] as &$detalle) {
                $detalle['total'] = round($detalle['total'], 2);
            }
        }

        return array_values($arr);
    }

    public function newfacturado_labs(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $venta1 = Ct_ventas::selectRaw("'VENTAS' AS consulta, ct_ventas.sucursal AS codigo_sucursal, cs.nombre_sucursal, ctciu.id AS id_ciudad ,ctciu.nombre AS ciudad, s.tipo as id_tipo , IF(s.tipo = 0, 'PUBLICO', IF(s.tipo = 1, 'SEG. PRIVADO', 'PARTICULAR') ) AS tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
            ->join('ct_sucursales as cs', function ($q) use ($empresa) {
                $q->on('cs.codigo_sucursal', 'ct_ventas.sucursal')->where('cs.estado', 1)->where('cs.id_empresa', $empresa->id);
            })
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('cs.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->leftJoin('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->whereNull('cer.id')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('s.tipo', 'ct_ventas.sucursal');

        // return $venta1->get();

        $venta2 = Ct_ventas::selectRaw("'SIN SEGURO' AS consulta, ct_ventas.sucursal AS codigo_sucursal, cs.nombre_sucursal, ctciu.id AS id_ciudad, ctciu.nombre AS ciudad,'3' AS id_tipo, '3' AS tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->leftJoin('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->join('ct_sucursales as cs', function ($q) use ($empresa) {
                $q->on('cs.codigo_sucursal', 'ct_ventas.sucursal')->where('cs.estado', 1)->where('cs.id_empresa', $empresa->id);
            })
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('cs.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->whereNull('cer.id')->whereNull('ct_ventas.seguro_paciente')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('ct_ventas.sucursal');

        $venta3 = Ct_ventas::selectRaw("'RELACIONADA' AS consulta, ct_ventas.sucursal AS codigo_sucursal, cs.nombre_sucursal, ctciu.id AS id_ciudad, ctciu.nombre AS ciudad,'4' AS id_tipo, '4' AS tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->join('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->join('ct_sucursales as cs', function ($q) use ($empresa) {
                $q->on('cs.codigo_sucursal', 'ct_ventas.sucursal')->where('cs.estado', 1)->where('cs.id_empresa', $empresa->id);
            })
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('cs.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('ct_ventas.sucursal');

        $venta4 = Ct_ventas::selectRaw("'NC' AS consulta, ct_ventas.sucursal AS codigo_sucursal, cs.nombre_sucursal, ctciu.id AS id_ciudad, ctciu.nombre AS ciudad,'5' AS id_tipo, '5' AS tipo,  SUM(cncc.total_credito) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)
            ->join('ct_nota_credito_clientes as cncc', function ($q) {
                $q->on('ct_ventas.id', 'cncc.id_factura')->where('cncc.estado', 1);
            })
            ->join('ct_sucursales as cs', function ($q) use ($empresa) {
                $q->on('cs.codigo_sucursal', 'ct_ventas.sucursal')->where('cs.estado', 1)->where('cs.id_empresa', $empresa->id);
            })
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('cs.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('ct_ventas.sucursal');

        $venta5 = Ct_Nota_Debito_Cliente::selectRaw("'ND' AS consulta,  cv.sucursal AS codigo_sucursal, cs.nombre_sucursal, ctciu.id AS id_ciudad, ctciu.nombre AS ciudad,'6' AS id_tipo, '6' AS tipo,  SUM(ct_debito_clientes.valor_contable) AS total")
            ->join('ct_ventas as cv', function ($q) {
                $q->on('ct_debito_clientes.id_factura', 'cv.id')->where('ct_debito_clientes.estado', 1);
            })
            ->join('ct_sucursales as cs', function ($q) use ($empresa) {
                $q->on('cs.codigo_sucursal', 'cv.sucursal')->where('cs.estado', 1)->where('cs.id_empresa', $empresa->id);
            })
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('cs.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->where('cv.id_empresa', $empresa->id)->where('cv.estado', 1)
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('cv.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('cv.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('cv.sucursal');
        $ventas = $venta1->union($venta2)->union($venta3)->union($venta4)->union($venta5)->get();
        // return $ventas;
        $sucursales = Ct_Sucursales::where('ct_sucursales.estado', 1)->where('ct_sucursales.id_empresa', $empresa->id)
            ->join('ct_ciudad as ctciu', function ($q) {
                $q->on('ct_sucursales.id_ciudad', 'ctciu.id')->where('ctciu.estado', 1);
            })
            ->select('ct_sucursales.id', 'ct_sucursales.id_ciudad', 'ct_sucursales.codigo_sucursal', 'ct_sucursales.nombre_sucursal', 'ctciu.nombre as nombre_ciudad')->get();
        $totales = [
            "sum_privado" => 0,
            "sum_empresasrelacionadas" => 0,
            "sum_particular" => 0,
            "sum_publico" => 0,
            "sum_nc" => 0,
            "sum_total" => 0,
            "data" => []
        ];

        // Índices de tipos de venta en el array de totales
        $indicesTiposVenta = [
            "seg. privado" => "privado",
            "particular" => "particular",
            "publico" => "publico",
            "4" => "empresasrelacionadas",
            "5" => "nc",
        ];

        // Iterar sobre las sucursales
        foreach ($sucursales as $sucursal) {
            // Inicializar los valores de totales para esta sucursal
            $totalesSucursal = [
                "particular" => 0,
                "publico" => 0,
                "privado" => 0,
                "empresasrelacionadas" => 0,
                "nc" => 0,
                "total" => 0
            ];

            // Iterar sobre las ventas
            foreach ($ventas as $venta) {
                if ($venta['codigo_sucursal'] === $sucursal['codigo_sucursal']) {
                    $tipo = strtolower($venta['tipo']);
                    $totalVenta = (float)$venta['total'];

                    if ($tipo === "3" || $tipo === "6") {
                        $tipo = "particular";
                    }

                    if (isset($indicesTiposVenta[$tipo])) {
                        $indice = $indicesTiposVenta[$tipo];
                        $totalesSucursal[$indice] += $totalVenta;
                        $totales['sum_' . $indice] += $totalVenta;
                    }

                    if ($tipo === "nc") {
                        $totalesSucursal['nc'] += $totalVenta;
                        $totales['sum_nc'] += $totalVenta;
                    }
                }
            }

            // Calcular el total para esta sucursal
            $totalesSucursal['total'] = $totalesSucursal['particular'] + $totalesSucursal['publico'] + $totalesSucursal['privado'] + $totalesSucursal['empresasrelacionadas'] - $totalesSucursal['nc'];

            // Actualizar los totales generales
            foreach ($totalesSucursal as $tipo => $valor) {
                if ($tipo !== 'total') {
                    $totales['data'][] = [
                        "id_ciudad" => $sucursal['id_ciudad'],
                        "codigo_sucursal" => $sucursal['codigo_sucursal'],
                        "nombre_sucursal" => $sucursal['nombre_sucursal'],
                        "ciudad" => $sucursal['nombre_ciudad'], // Debes definir cómo obtener la ciudad si no está disponible en los datos de la sucursal
                        "valores" => $totalesSucursal
                    ];
                    break;
                }
            }
            $totales['sum_total'] += $totalesSucursal['total'];
        }

        // Convertir a JSON y mostrar los totales
        return $totales;
    }

    public function newfacturado_endoscopy(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $venta1 = Ct_ventas::selectRaw("'VENTAS' AS consulta, s.tipo as id_tipo ,IF(s.tipo = 0, 'PUBLICO', IF(s.tipo = 1, 'SEG. PRIVADO', 'PARTICULAR') ) AS tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->leftJoin('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->whereNull('cer.id')
            ->join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            })->groupBy('s.tipo');

        $venta2 = Ct_ventas::selectRaw("'SIN_SEGURO' AS consulta, 3 as id_tipo ,'3' as tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->leftJoin('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->whereNull('cer.id')->whereNull('ct_ventas.seguro_paciente')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            });

        $venta3 = Ct_ventas::selectRaw("'RELACIONADA' AS consulta , '4' as id_tipo, '4' as tipo,  Ifnull(SUM(Ifnull(ct_ventas.subtotal_0,0))+SUM(Ifnull(ct_ventas.subtotal_12,0))-SUM(Ifnull(ct_ventas.descuento,0)),0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
            ->join('ct_empresa_relacionada as cer', function ($q) {
                $q->on('cer.id_cliente', 'ct_ventas.id_cliente')->where('cer.estado', 1);
            })
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            });

        $venta4 = Ct_ventas::selectRaw("'NOTAS_CREDITO' AS consulta ,'5' as id_tipo ,'5' as tipo, Ifnull(SUM(Ifnull(cncc.total_credito, 0)), 0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)
            ->join('ct_nota_credito_clientes as cncc', function ($q) {
                $q->on('ct_ventas.id', 'cncc.id_factura')->where('cncc.estado', 1);
            })
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            });

        $venta5 = Ct_ventas::selectRaw("'NOTAS_DEBITO' as consulta, '6' as id_tipo, '6' as tipo, Ifnull(SUM(Ifnull(ct_ventas.total_final, 0)), 0) AS total")
            ->where('ct_ventas.id_empresa', $empresa->id)->where('ct_ventas.estado', 1)->where('ct_ventas.tipo', 'N-D')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
                } else {
                    $q->where('ct_ventas.fecha', '<=', $fecha_hasta);
                }
            });

        $ventas = $venta1->union($venta2)->union($venta3)->union($venta4)->union($venta5)->get();
        $total_particular = 0;
        $total_publico = 0;
        $total_sin_seguro = 0;
        $total_relacionado = 0;
        $total_notas_credito = 0;
        $total_notas_debito = 0;
        $total_privado = 0;
        foreach ($ventas as $key => $value) {
            if ($value->id_tipo == 0) {
                $total_publico = round($value->total, 2);
            }
            if ($value->id_tipo == 1) {
                $total_privado = round($value->total, 2);
            }
            if ($value->id_tipo == 2) {
                $total_particular = round($value->total, 2);
            }
            if ($value->id_tipo == 3) {
                $total_sin_seguro = round($value->total, 2);
            }
            if ($value->id_tipo == 4) {
                $total_relacionado = round($value->total, 2);
            }
            if ($value->id_tipo == 5) {
                $total_notas_credito = round($value->total, 2);
            }
            if ($value->id_tipo == 6) {
                $total_notas_debito = round($value->total, 2);
            }
        }

        $data['particular'] = round($total_particular + $total_sin_seguro + $total_notas_debito, 2);
        $data['publico'] = round($total_publico, 2);
        $data['privado'] = round($total_privado, 2);
        $data['empresasrelacionadas'] = round($total_relacionado, 2);
        $data['nc'] = round($total_notas_credito, 2);
        $data['total'] = round((($total_particular + $total_publico + $total_privado + $total_sin_seguro + $total_relacionado + $total_notas_debito) - $total_notas_credito), 2);
        return $data;
    }

    public function _____reporte_camas(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $pisos = Piso::where('estado', 1)->get();
        $habitacion = Habitacion::where('estado', 1)->get();
        $cama = Cama::all();
        $arr = [];
        $estados_camas = [];

        foreach ($pisos as $piso) {
            $estados_camas_piso = [];
            foreach ($piso->habitacion as $habitacion) {
                if (($habitacion->estado) != 4) {
                    $estados_camas_habitacion = [];
                    foreach ($habitacion->cama as $cama) {
                        $estado_cama = 'disponible';
                        $cama_tran = $cama->transaccion->where('id_cama', $cama->id)->first();
                        if (!is_null($cama_tran)) {
                            $estado_cama = $cama_tran->estado_cama;
                        }
                        $estados_camas_habitacion[] = [
                            'id_cama' => $cama->id,
                            'estado' => $estado_cama
                        ];
                    }
                    $data['piso'] = $piso->nombre_piso;
                    $data['habitacion'] = $habitacion->codigo;
                    $data['det_habitacion'] = $estados_camas_habitacion;
                    array_push($arr, $data);
                    // $estados_camas_piso[$habitacion->codigo] = $estados_camas_habitacion;
                }
            }
            // $estados_camas[$piso->nombre_piso] = $estados_camas_piso;
        }
        return $arr;
    }

    public function __reporte__________camas(Request $request)
    {
        $pisos = Piso::where('estado', 1)->get();
        $arr = [];

        foreach ($pisos as $piso) {
            $cant_habitaciones = $piso->habitacion->where('estado', 1)->count();
            $habitacion = $piso->habitacion;
            $sum_camas = 0;
            foreach ($habitacion as $hab) {
                $cant_camas = $hab->cama->count();
                $sum_camas += $cant_camas;
            }

            $disponibles = 0;
            $ocupadas = 0;

            $estados = CamaTransaccion::join('cama as c', 'c.id', 'cama_transaccion.id_cama')
                ->join('habitacion as h', 'h.id', 'c.id_habitacion')
                ->join('piso as p', 'p.id', 'h.id_piso')
                ->where('p.id', $piso->id)
                ->groupBy('cama_transaccion.estado_cama', 'p.id')
                ->selectRaw('p.nombre_piso, cama_transaccion.estado_cama, COUNT(id_cama) as cantidad')->get();
            if (!is_null($estados)) {
                foreach ($estados as $key => $value) {
                    if ($value->estado_cama == 'disponible') {
                        $disponibles = $value->cantidad;
                    }
                    if ($value->estado_cama == 'ocupada') {
                        $ocupadas = $value->cantidad;
                    }
                }
            }

            $data['piso'] = $piso->nombre_piso;
            $data['habitacion'] = $cant_habitaciones;
            $data['camas'] = $sum_camas;
            $data['disponibles'] = $disponibles;
            $data['ocupadas'] = $ocupadas;
            array_push($arr, $data);
        }

        return $arr;
    }

    public function reporte_camas(Request $request)
    {
        $arr = [];

        $cuadro = Camilla::where('id_hospital', 2)->get();     // camillas
        $cant_camas = $cuadro->count();

        /*$sala = Sala::where('id_hospital', 2)
            ->where('estado', 1)
            ->take(1)
            ->get();   */
        $sala = Hospital::where('id', 2)
            ->take(1)
            ->get();                                // salas

        foreach ($sala as $piso) {

            $cant_habitaciones = $sala->count();
            $disponibles = 0;
            $ocupadas = 0;

            foreach ($cuadro as $value) {

                // ⚠️ AQUÍ TU CONSULTA EXACTA
                $estado = Camilla_Gestion::where('camilla', $value->id)
                    ->whereBetween('estado_uso', [0, 4])
                    ->where('num_atencion', 1)
                    ->orderBy('id', 'desc')  // <-- IMPORTANTE
                    ->first();

                // Si NO existe registro → se considera LIBRE
                if (!$estado) {
                    $disponibles++;
                    continue;
                }

                // Si existe registro
                if (in_array($estado->estado_uso, [0, 1])) {
                    $disponibles++;
                } else {
                    $ocupadas++;
                }
            }

            $arr[] = [
                'piso'        => $piso->nombre_hospital,
                'habitacion'  => $cant_habitaciones,
                'camas'       => $cant_camas,
                'disponibles' => $disponibles,
                'ocupadas'    => $ocupadas,
            ];
        }

        return $arr;
    }



    public function resumen_enfermedades(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $cie10 = Hc_Cie10::selectRaw("hc_cie10.cie10, IFNULL((SELECT descripcion FROM cie_10_3 c WHERE hc_cie10.cie10 = c.id LIMIT 1), (SELECT descripcion FROM cie_10_4 c1 WHERE hc_cie10.cie10 = c1.id LIMIT 1)) as nombre_cie,COUNT(hc_cie10.cie10) as cantidad ")
            ->join('historiaclinica as h', 'h.hcid', 'hc_cie10.hcid')
            ->join('agenda as a', function ($q) {
                $q->on('a.id', 'h.id_agenda')->where('a.estado', 1)->where('a.estado_cita', 4);
            })
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('a.fechaini', '<=', $fecha_hasta . ' 23:59:59');
                }
            })
            ->whereNotNull('hc_cie10.presuntivo_definitivo')
            ->groupBy('hc_cie10.cie10')
            ->orderBy('cantidad', 'DESC')
            ->get();

        return $cie10;
    }

    public function producidoxproc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $fecha_desde = $request->fecha_desde;
            if (is_null($fecha_desde)) {
                $fecha_desde = date('Y-m-d');
            }
            $fecha_hasta = $request->fecha_hasta;
            if (is_null($fecha_hasta)) {
                $fecha_hasta = date('Y-m-d');
            }
            $id_seguro = $request->id_seguro;
            if (is_null($id_seguro)) {
                return ['status' => 'error', 'msj' => 'Seleccione seguro'];
            }
            $ordenes = Agenda::selectRaw("ovd.id_producto, ovd.nombre_producto, s.id as id_seguro ,s.nombre as nombre_seguro, COUNT(ovd.id_producto) as cantidad, SUM(ovd.total+ovd.cobrar_seguro) as total")
                ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                ->join('seguros as s', 's.id', 'ov.id_seguro')
                ->join('ct_orden_venta_detalle as ovd', 'ovd.id_orden', 'ov.id')
                ->where('s.id', $id_seguro)
                ->where('ov.estado', '1')
                ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                    if (!is_null($fecha_desde)) {
                        $q->whereBetween('ov.fecha_emision', [$fecha_desde, $fecha_hasta]);
                    } else {
                        $q->where('ov.fecha_emision', '<=', $fecha_hasta);
                    }
                })
                ->groupBy('ovd.id_producto')
                ->orderBy('total', 'DESC')
                ->get();

            $sum_tot = 0;
            $sum_can = 0;
            foreach ($ordenes as $orden) {
                $sum_tot += $orden->total;
                $sum_can += $orden->cantidad;
            }

            $aux['suma_total'] = round($sum_tot, 2);
            $aux['suma_cantidad'] = $sum_can;
            $aux['detalle'] = $ordenes;
            return $aux;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function producidoxformapago(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $fecha_desde = $request->fecha_desde;
            if (is_null($fecha_desde)) {
                $fecha_desde = date('Y-m-d');
            }
            $fecha_hasta = $request->fecha_hasta;
            if (is_null($fecha_hasta)) {
                $fecha_hasta = date('Y-m-d');
            }
            $id_seguro = $request->id_seguro;
            if (is_null($id_seguro)) {
                return ['status' => 'error', 'msj' => 'Seleccione seguro'];
            }
            $ordenes = Agenda::selectRaw("s.id as id_seguro ,s.nombre as nombre_seguro, COUNT(ovp.tipo) as cantidad, SUM(ovp.valor) as total, ctp.nombre")
                ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                ->join('seguros as s', 's.id', 'ov.id_seguro')
                ->join('ct_orden_venta_pago as ovp', 'ovp.id_orden', 'ov.id')
                ->join('ct_tipo_pago as ctp', 'ctp.id', 'ovp.tipo')
                ->where('ov.estado', '1')
                ->where('s.id', $id_seguro)
                ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                    if (!is_null($fecha_desde)) {
                        $q->whereBetween('ov.fecha_emision', [$fecha_desde, $fecha_hasta]);
                    } else {
                        $q->where('ov.fecha_emision', '<=', $fecha_hasta);
                    }
                })
                ->groupBy('ovp.tipo')
                ->orderBy('total', 'DESC')
                ->get();

            $sum_tot = 0;
            $sum_can = 0;
            foreach ($ordenes as $orden) {
                $sum_tot += $orden->total;
                $sum_can += $orden->cantidad;
            }

            $aux['suma_total'] = round($sum_tot, 2);
            $aux['suma_cantidad'] = $sum_can;
            $aux['detalle'] = $ordenes;
            return $aux;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function prodxmesxseg(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {

            $meses = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');

            $anio = $request['anio'];
            if (is_null($anio)) {
                $anio = date('Y');
            }
            $seguros = Seguro::where('inactivo', 1)->whereIn('tipo', ['1', '2'])->get();
            $global = array();
            foreach ($seguros as $key => $value) {
                $ordenes = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                    ->join('seguros as s', 's.id', 'ov.id_seguro')
                    ->where('ov.estado', 1)
                    ->where('s.inactivo', 1)
                    ->whereYear('ov.fecha_emision', $anio)
                    ->where('ov.id_seguro', $value->id)
                    ->groupBy(DB::raw('MONTH(ov.fecha_emision)'))
                    ->selectRaw('MONTH(ov.fecha_emision) as mes, s.id, s.nombre, COUNT(agenda.id) as cantidad, IFNULL(SUM(IFNULL(ov.total,0)+IFNULL(ov.total_cobrar_seguro,0)),0) as total, sum(ov.total) , sum(ov.total_cobrar_seguro)')
                    ->get();

                $arr = [];
                foreach ($meses as $mes_num => $nombre_mes) {
                    $orden_mes = $ordenes->where('mes', $mes_num)->first();
                    $total = $orden_mes ? round($orden_mes->total, 4) : 0;

                    $arr[] = [
                        "mes" => $nombre_mes,
                        "id_mes" => $mes_num,
                        "total" => number_format(round($total, 2), 2),
                    ];
                }
                $sum_total = 0;
                foreach ($ordenes as $ordn) {
                    $sum_total += $ordn->total;
                }
                $ord['id_seguro'] = $value->id;
                $ord['nombre_seguro'] = $value->nombre;
                $ord['total_seguro'] = number_format($sum_total, 2);
                $ord['data'] = $arr;
                array_push($global, $ord);
            }
            return $global;
        } catch (\Exception $e) {
            return json_encode(["respuesta" => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function lista_seguro(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $seguros = Seguro::where('inactivo', 1)->whereIn('tipo', ['1', '2'])->select('id', 'nombre')->get();
        return $seguros;
    }

    public function reporte_informe_biopsia(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $informes = Hc_informe_biopsia::selectRaw("hc_informe_biopsia.id_paciente, CONCAT(p.nombre1,' ',p.nombre2, ' ', p.apellido1, ' ', p.apellido2) as nombre_paciente, hcb.descripcion_frasco, hcb.observacion, COUNT(hc_informe_biopsia.id_hc_muestra) as cantidad, hc_informe_biopsia.created_at as fecha")
            ->join('paciente as p', 'p.id', 'hc_informe_biopsia.id_paciente')
            ->join('hc4_biopsias as hcb', 'hcb.id', 'hc_informe_biopsia.id_hc_muestra')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('hc_informe_biopsia.created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('hc_informe_biopsia.created_at', '<=', $fecha_hasta . ' 23:59:59');
                }
            })
            ->where('hc_informe_biopsia.estado', 1)
            ->whereNotNull('hc_informe_biopsia.id_hc_muestra')
            ->groupBy('hc_informe_biopsia.id_hc_muestra')
            ->orderBy('hc_informe_biopsia.created_at', 'ASC')
            ->get();
        $cant = 0;
        foreach ($informes as $value) {
            $cant += $value->cantidad;
        }

        $arr['suma_total'] = $cant;
        $arr['detalle'] = $informes;

        return $arr;
    }

    public function reporte_compras(Request $request)
    {
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $tipos = [
            1 => 'COSTO',
            2 => 'GASTO',
        ];

        // Ejecutas tu consulta
        $compras = Ct_compras::selectRaw("
        tipo,
        IF(tipo = 1, 'COSTO', 'GASTO') as tipo_nombre,
        IFNULL(SUM(IFNULL(subtotal_0,0) + IFNULL(subtotal_12,0) - IFNULL(descuento,0)), 0) as total")
            ->where('estado', '!=', 0)
            ->whereIn('tipo', [1, 2])
            ->where('id_empresa', $empresa->id)
            ->when($fecha_desde, function ($q) use ($fecha_desde, $fecha_hasta) {
                $q->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
            }, function ($q) use ($fecha_hasta) {
                $q->where('fecha', '<=', $fecha_hasta);
            })
            ->groupBy('tipo')
            ->get()
            ->keyBy('tipo'); // Indexar por 'tipo' para acceso rápido

        // Asegurar que ambos tipos estén presentes
        $resultado = [];
        foreach ($tipos as $tipo => $nombre) {
            $compra = $compras->get($tipo);

            $resultado[] = [
                'tipo' => $tipo,
                'tipo_nombre' => $nombre,
                'total' => $compra ? number_format((float)$compra->total, 4, '.', '') : '0.0000',
            ];
        }

        return $resultado;
    }

    public function reporte_examenes_realizados(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
            'id_examen' => 'required|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');
        $id_examen = (int) $request->input('id_examen');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));

        try {
            $query = Examen_Orden::join('examen_detalle as ed', 'ed.id_examen_orden', 'examen_orden.id')
                ->whereBetween('examen_orden.fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
                ->where('ed.id_examen', $id_examen)
                ->where('examen_orden.estado', 1)
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->leftJoin('seguros as s', 's.id', 'examen_orden.id_seguro')
                ->join('examen as e', 'e.id', 'ed.id_examen')
                ->whereExists(function ($resultados) {
                    $resultados->select(DB::raw(1))
                        ->from('examen_resultado as er')
                        ->whereRaw('er.id_orden = examen_orden.id')
                        ->whereRaw('er.id_examen = ed.id_examen')
                        ->whereNotNull('er.valor');
                })
                ->select(
                    'examen_orden.id as id_orden',
                    'examen_orden.fecha_orden',
                    'examen_orden.id_paciente',
                    DB::raw("CONCAT(p.apellido1,' ',p.apellido2, ' ',p.nombre1) as nombre_paciente"),
                    'examen_orden.id_seguro',
                    DB::raw("COALESCE(s.nombre, 'Sin seguro') as nombre_seguro"),
                    'ed.id_examen',
                    'e.nombre as nombre_examen'
                )
                ->orderBy('examen_orden.fecha_orden', 'desc')
                ->orderBy('examen_orden.id', 'desc');

            $resultados = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'status' => 'success',
                'data' => $resultados->items(),
                'pagination' => [
                    'page' => $resultados->currentPage(),
                    'per_page' => $resultados->perPage(),
                    'total' => $resultados->total(),
                    'total_pages' => max(1, $resultados->lastPage()),
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error en reporte de exámenes realizados', ['exception' => $e]);
            return response()->json(['status' => 'error', 'msj' => 'No se pudo consultar el reporte de exámenes realizados'], 500);
        }
    }
    public function lista_examenes(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $examenes = Examen::where('estado', 1)->select('id', 'nombre')->where('nombre','NOT LIKE','%DOMICILIO%')->get();
        return response()->json($examenes);
    }
}
