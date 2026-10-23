<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Ordenes_Ventas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen_Orden;
use Sis_medico\GrupoPregunta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Preguntas_Labs;

class BdLabsController extends Controller
{
    public function ordenExamenes(Request $r)
    {
        $fecha_desde = $r->fecha_desde;
        $ordenes = Examen_Orden::select('id', 'comprobante')
            ->where('estado', '1')
            ->whereNotNull('comprobante')
            ->whereYear('fecha_orden',date($fecha_desde))
            //->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->get([
                'examen_orden.id',
                'examen_orden.comprobante'
            ]);
        return $ordenes;
    }
    public function examen_ventas(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        // $anio = $request->anio;
        // $mes = $request->mes;
        try {
            //code...
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $ordenes = Examen_Orden::where('estado', '1')->whereNotNull('comprobante')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->select('examen_orden.*', 's.tipo as tipo_seguro', 's.nombre as nombre_seguro')->where('leido_bd', '0');
            // if (!is_null($anio)) {
            //     $ordenes = $ordenes->where('anio', $anio);
            // }
            // if (!is_null($mes)) {
            //     $ordenes = $ordenes->where('mes', $mes);
            // }
            if (!is_null($fecha_desde)) {
                $ordenes = $ordenes->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $ordenes = $ordenes->where('fecha_orden', '<=', $fecha_hasta);
            }

            $ordenes = $ordenes->get();
            $arr_pub = array();
            $arr_priv = array();
            $arr_part = array();
            $global = [];
            $total_pub = 0;
            $total_priv = 0;
            $total_part = 0;
            $tipo = "";
            foreach ($ordenes as $orden) {
                if ($orden->tipo_seguro == '0') {
                    $lb_agrup = Labs_Factura_Agrupada_Orden::where('id_examen_orden', $orden->id)->first();
                    if (!is_null($lb_agrup)) {
                        $tipo = "publicas";
                        // if ($orden->id_seguro == 2) {
                        //     $desc = $orden->total_nivel2 * 0.07;
                        //     $totalcalculado  = $orden->total_nivel2 - $desc;
                        // } else {
                        //     $totalcalculado = $orden->total_nivel2;
                        // }

                        $totalcalculado = $orden->total_valor;
                    }
                }

                if ($orden->tipo_seguro == '1') {
                    if ($orden->cobrar_pac_pct != 100) {
                        $totalcalculado = $orden->valor_con_oda;
                        $tipo = "privado con_oda";
                    } else {
                        $totalcalculado = $orden->total_valor;
                        $tipo = "privado sin_oda";
                    }
                }

                if ($orden->tipo_seguro == '2') {
                    $tipo = "particular";
                    $totalcalculado = $orden->total_valor;
                }

                array_push($global, [
                    "id" => $orden->id,
                    "anio" => $orden->anio,
                    "mes" => $orden->mes,
                    "fecha_orden" => $orden->fecha_orden,
                    "tipo" => $tipo,
                    "id_seguro" => $orden->id_seguro,
                    "nombre_seguro" => $orden->nombre_seguro,
                    "tipo_seguro" => $orden->tipo_seguro,
                    "comprobante" => $orden->comprobante,
                    "total_valor" => $orden->total_valor,
                    "total" =>  round($totalcalculado, 2),
                ]);

                $orden->update(['leido_bd' => '1']);
            }
            return  $global;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function buscar_en_ventas(Request $request)
    {
        try {
            $arr_con_ventas = [];
            foreach ($request->data as $value) {
                $ventas = Ct_ventas::where('nro_comprobante', $value['comprobante'])->where('estado', '1')->where('id_empresa', '0993075000001')->first();

                $data['id'] = $value['id'];
                $data['id_ciudad'] = $value['id_ciudad'];
                $data['ciudad'] = $value['ciudad'];
                $data['anio'] = $value['anio'];
                $data['mes'] = $value['mes'];
                $data['fecha_orden'] = $value['fecha_orden'];
                $data['tipo'] = $value['tipo'];
                $data['id_seguro'] = $value['id_seguro'];
                $data['nombre_seguro'] = $value['nombre_seguro'];
                $data['tipo_seguro'] = $value['tipo_seguro'];
                $data['comprobante'] = $value['comprobante'];
                $data['total_valor'] = $value['total_valor'];
                $data['total'] = $value['total'];
                $data['id_ventas'] = $ventas['id'];
                $data['total_final_ventas'] = ($ventas['subtotal_0'] + $ventas['subtotal_12']) - $ventas['descuento'];
                $data['id_cliente'] = $ventas['id_cliente'];
                $data['nombre_cliente'] = $ventas['nombre_cliente'];
                $data['fecha_venta'] = $ventas['fecha'];
                $data['subtotal0'] = $ventas['subtotal_0'];
                $data['subtotal12'] = $ventas['subtotal_12'];
                $data['iva'] = $ventas['impuesto'];
                $data['descuento'] = $ventas['descuento'];
                array_push($arr_con_ventas, $data);
            }

            return $arr_con_ventas;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function crea_orden_venta(Request $request)
    {
        return json_encode(["data"  => $request->all()]);
        return $request->all();
    }

    public function master_preguntas(Request $request)
    {
        if ($request->header('appid') == null) {

            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }
        $master = $request['id_master'];
        $grupopregunta = GrupoPregunta::whereNotNull('orden')->orderby('orden','ASC')->get();
        $contador      = 0;
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato     = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->where("tipo_encuesta", $master)->get();
            if ($dato->isNotEmpty()) {
                $arr_p = array();
                foreach ($dato as $d) {
                    $valores = $d->pregunta_valor;
                    $arr_valores = array();
                    foreach ($valores as $val) {
                        $arr_val = [
                            "id" => $val->id,
                            "nombre" => $val->nombre_opcion->nombre,
                            "valor" => $val->valor,
                        ];
                        array_push($arr_valores, $arr_val);
                    }
                    $arr = [
                        "id" => $d->id,
                        "pregunta" => $d->nombre,
                        "id_grupopregunta" => $d->id_grupopregunta,
                        "valores" =>  $arr_valores
                    ];
                    array_push($arr_p, $arr);
                }
                $arreglo[$contador] = $arr_p;
                $contador           = $contador + 1;
            }
        }

        return $arreglo;
    }

    public function guardar_labs_encuesta(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $master = $request['id_master'];
            if (is_null($master)) {
                $master = 2;
            }
            $cedula = $request['cedula'];
            $id_orden = $request['id_orden'];
            if ($cedula == null) {
                return ['status' => 'error', 'msj' => 'La cedula esta vacia'];
            }
            if ($cedula != null) {
                $paciente = Paciente::find($cedula);
                if ($paciente == null) {
                    return ['status' => 'error', 'msj' => "Paciente no registrado"];
                }
            }
            if (is_null($id_orden)) {
                return ['status' => 'error', 'msj' => 'No seleccionó orden'];
            }

            $examen_orden = Examen_Orden::find($id_orden);
            if (is_null($examen_orden)) {
                return ['status' => 'error', 'msj' => 'No existe la orden'];
            }

            $verificar = Encuesta_Labs::where('id_paciente', $cedula)
                ->where("tipo_master", $master)
                ->where('id_orden', $id_orden)
                ->first();

            if (!is_null($verificar)) {
                return ['status' => 'error', 'msj' => 'Ya se encuentra registrada una encuesta'];
            }

            $date = Carbon::now();

            $grupopregunta = DB::table('grupo_pregunta as gp')
                ->join('pregunta_labs as pl', 'gp.id', 'pl.id_grupopregunta')
                ->where('pl.estado', 1)
                ->where("pl.tipo_encuesta", $master)
                ->select("pl.id as id_labs_pregunta", "gp.id as id")
                ->get();

            foreach ($grupopregunta as $value2) {
                $flag = false;
                if (is_null($request[$value2->id_labs_pregunta])) {
                    $flag = true;
                    break;
                }
            }

            if ($flag) {
                return ['respuesta' => 'error', 'msj' => "Complete todo el formulario"];
            }

            $encuesta = Encuesta_Labs::create([
                'id_paciente' => $cedula,
                'mes'         => $date->month,
                'anio'        => $date->year,
                'ip_ingreso'  => "Creada en App",
                'id_orden'    => $id_orden,
                'tipo_master' => $master,
            ]);

            if (!is_null($encuesta)) {
                foreach ($grupopregunta as $value) {
                    $valor = '';
                    if (!is_null($request[$value->id_labs_pregunta])) {
                        $valor = $request[$value->id_labs_pregunta];
                    } else {
                        $valor = $request['14'];
                    }

                    DB::table('encuesta_complementolabs')
                        ->insert([
                            'id_encuesta_labs' => $encuesta->id,
                            'id_pregunta_labs' => $value->id_labs_pregunta,
                            'valor'            => $valor,
                            'id_grupo'         => $value->id
                        ]);
                }
            }
            return ['status' => 'success', 'msj' => 'Guardado con exito'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function valida_encuesta(Request $request)
    {
        $id_orden = $request['id_orden'];
        $id_paciente = $request['cedula'];
        $encuesta = Encuesta_Labs::where('id_orden', $id_orden)
            ->where('id_paciente', $id_paciente)
            // ->where("tipo_master", 2)
            ->first();

        if (!is_null($encuesta)) {
            return ['status' => 'error', 'msj' => 'Encuesta ya registrada'];
        } else {
            return ['status' => 'success', 'msj' => 'no registrada'];
        }
    }

    public function copago_labs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $empresa = Empresa::where('prioridad_labs', 1)->where('estado', 1)->first();
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];

            $ordenes = Ct_Ven_Orden::where('estado', 1)->where('id_empresa', $empresa->id)->whereNotNull('id_ct_venta');

            if (!is_null($fecha_desde)) {
                $ordenes = $ordenes->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
            } else {
                $ordenes = $ordenes->where('fecha', '<=', $fecha_hasta);
            }

            $ordenes = $ordenes->get();
            $global = [];
            foreach ($ordenes as $value) {
                $examen_orden = Examen_Orden::where('examen_orden.id', $value->orden_venta)->join('seguros as s', 's.id', 'examen_orden.id_seguro')->select('examen_orden.*', 's.tipo as tipo_seguro', 's.nombre as nombre_seguro')->first();

                array_push($global, [
                    "id" => $examen_orden->id,
                    "anio" => $examen_orden->anio,
                    "mes" => $examen_orden->mes,
                    "fecha_orden" => $examen_orden->fecha_orden,
                    "tipo" => 'FC ODA',
                    "id_seguro" => $examen_orden->id_seguro,
                    "nombre_seguro" => $examen_orden->nombre_seguro,
                    "tipo_seguro" => $examen_orden->tipo_seguro,
                    "comprobante" => $value->nro_comprobante,
                    "total_valor" => $examen_orden->total_valor - $examen_orden->valor_con_oda,
                    "total" => $examen_orden->total_valor - $examen_orden->valor_con_oda,
                ]);
            }
            return  $global;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function fc_manuales(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $ventas = Ct_ventas::where('ct_ventas.estado', '1')->where('ct_ventas.id_empresa', '0993075000001')->where('ct_ventas.tipo', 'VEN-FA')->whereNull('ct_ventas.id_paciente')->leftjoin('seguros as s', 's.id', 'ct_ventas.seguro_paciente');

        if (!is_null($fecha_desde)) {
            $ventas = $ventas->whereBetween('ct_ventas.fecha', [$fecha_desde, $fecha_hasta]);
        } else {
            $ventas = $ventas->where('ct_ventas.fecha', '<=', $fecha_hasta);
        }

        $ventas = $ventas->select('ct_ventas.*', 's.tipo as tipo_seguro', 's.nombre as nombre_seguro')->get();

        $arr_ventas = array();
        foreach ($ventas as $value) {
            $sucursal = Ct_Sucursales::where('ct_sucursales.id_empresa', '0993075000001')->where('ct_sucursales.codigo_sucursal', $value->sucursal)->where('ct_sucursales.estado', '1')->join('ct_ciudad as c', 'c.id', 'ct_sucursales.id_ciudad')->select('ct_sucursales.id', 'ct_sucursales.codigo_sucursal', 'c.nombre as ciudad')->first();

            $data['id'] = $value->id;
            $data['ciudad'] = $sucursal->ciudad;
            $data['fecha_venta'] = $value->fecha;
            $data['seguro_paciente'] = $value->seguro_paciente;
            $data['nombre_seguro'] = $value->nombre_seguro;
            $data['tipo_seguro'] = $value->tipo_seguro;
            $data['comprobante'] = $value->nro_comprobante;
            $data['subtotal_0'] = $value->subtotal_0;
            $data['subtotal_12'] = $value->subtotal_12;
            $data['descuento'] = $value->descuento;
            $data['id_cliente'] = $value->id_cliente;
            $data['nombre_cliente'] = $value->nombre_cliente;
            $data['impuesto'] = $value->impuesto;
            $data['total'] = $value->total;


            array_push($arr_ventas, $data);
        }

        return $arr_ventas;
    }

    public static function calcula_porcentaje($anio, $mes)
    {
        $ordenes = Examen_Orden::where('anio', $anio)->where('mes', $mes)->where('estado', 1)->get();
        if ($mes == 'TODOS') {
            $ordenes = Examen_Orden::where('anio', $anio)->where('estado', 1)->get();
        }
        foreach ($ordenes as $orden) {
            $valores = BdLabsController::puede_imprimir($orden->id);
            $cantidad = $valores['cantidad'];
            $certificados = $valores['certificados'];
            $cant_par = $valores['cant_par'];
            if ($cant_par == 0) {
                $pct = 0;
            } else {
                $pct = ($certificados / $cant_par) * 100;
            }
            $orden->update(['pct_resultado' => $pct]);
        }
        return "oka 0702";
    }

    public static function puede_imprimir($id) //esta
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = $orden->detalles;
        $resultados = $orden->resultados;
        //$parametros = Examen_parametro::orderBy('orden')->get();

        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                //$xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0')->where('certificado', '1');
                $xpar = $resultados->where('id_examen', '639')->where('certificado', '1');

                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        return ['cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par];
    }

    public function consolidado_labs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $anio = $request['anio'];
        $mes  = $request['mes'];
        if (is_null($anio)) {
            $anio = date('Y');
        }

        $examen_orden = Examen_Orden::where('examen_orden.realizado', '1')
            ->where('examen_orden.estado', '1')
            ->where('examen_orden.anio', $anio)
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro');
        if (isset($mes)) {
            $examen_orden = $examen_orden->whereIn('examen_orden.mes', $mes);
        }
        $examen_orden = $examen_orden->groupBy('examen_orden.mes', 's.tipo')
            ->select(DB::raw('sum(examen_orden.total_valor) as total'), 'examen_orden.mes', 'examen_orden.anio', 's.tipo as tipo_seguro', DB::raw('count(examen_orden.id) as cantidad'))
            ->get();

        $meses = array(
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        );

        if (is_null($mes) || !is_array($mes)) {
            $mes = array_keys($meses);
        }

        $arr = [];
        $ticket = 0;

        foreach ($mes as $mesNum) {
            $arr[$mesNum] = [
                'mes'              => $mesNum,
                'nombre_mes'       => $meses[$mesNum],
                'total'            => 0,
                'ticket_promedio'  => 0,
                'cantidad_Privado' => 0,
                'Privado'          => 0,
                'cantidad_Particular' => 0,
                'Particular'      => 0,
                'cantidad_Publico' => 0,
                'Publico'          => 0,
            ];
        }
        foreach ($examen_orden as $resultado) {
            $mes = $resultado->mes;
            $nombreMes = $meses[$mes];
            $tipoSeguro = $resultado->tipo_seguro;
            $total = round($resultado->total, 2);
            $nombre_tipo = "";
            if ($tipoSeguro == 1) {
                $nombre_tipo = "Privado";
                $totpriv = $total;
                $ticket = $totpriv / $resultado->cantidad;
            } elseif ($tipoSeguro == 2) {
                $nombre_tipo = "Particular";
            } elseif ($tipoSeguro == 0) {
                $nombre_tipo = "Publico";
            }
            $arr[$mes]['mes'] = $mes;
            $arr[$mes]['nombre_mes'] = $nombreMes;
            $arr[$mes]['total'] = round(isset($arr[$mes]['total']) ? $arr[$mes]['total'] + $total : $total, 2);
            $arr[$mes]['ticket_promedio'] = round($ticket, 2);
            $arr[$mes]['cantidad_' . $nombre_tipo] = $resultado->cantidad;


            $arr[$mes]['Privado'] = isset($arr[$mes]['Privado']) ? $arr[$mes]['Privado'] : 0;
            $arr[$mes]['Particular'] = isset($arr[$mes]['Particular']) ? $arr[$mes]['Particular'] : 0;
            $arr[$mes]['Publico'] = isset($arr[$mes]['Publico']) ? $arr[$mes]['Publico'] : 0;

            if ($tipoSeguro == 1) {
                $arr[$mes]['Privado'] = round($total, 2);
            }
            if ($tipoSeguro == 2) {
                $arr[$mes]['Particular'] = round($total, 2);
            }
            if ($tipoSeguro == 0) {
                $arr[$mes]['Publico'] = round($total, 2);
            }

            //    $arr[$mes][$nombre_tipo] = isset($arr[$mes]['detalles'][$tipoSeguro]['total']) ? $arr[$mes]['detalles'][$tipoSeguro]['total'] + $total : $total;
        }

        return array_values($arr);
    }
}
