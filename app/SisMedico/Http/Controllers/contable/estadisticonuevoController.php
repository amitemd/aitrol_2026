<?php

namespace Sis_medico\Http\Controllers\contable;

use DateTime;
use Excel;
use Exception;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Session;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Empresa_Relacionada;

class estadisticonuevoController extends Controller
{

    public function nuevo_estadistico(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio = $request['anio'];
        //dd($request->all());
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        //0  es publico
        //1  es privado
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = $i;
            if ($i < 10) {
                $mes = '0' . $i;
            }

            $empresas_relacionadas = Ct_Empresa_Relacionada::where('estado', 1)->get();
            $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

            $arr_e = [];
            foreach ($empresas_relacionadas as $relacionada) {
                array_push($arr_e, $relacionada->id_cliente);
            }

            $ventas_mes = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereMonth('ct_ventas.fecha', $mes)
                ->whereYear('ct_ventas.fecha', $anio)
                ->where('ct_ventas.estado', '<>', '0');

            if (isset($arr_e)) {
                $ventas_mes = $ventas_mes->whereNotIn('id_cliente', $arr_e);
                $venta_clienter = Ct_ventas::where('id_empresa', $empresa->id)
                    ->where('estado', 1)
                    ->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
                    ->whereIn('id_cliente', $arr_e)
                    ->whereMonth('ct_ventas.fecha', $mes)
                    ->whereYear('ct_ventas.fecha', $anio)
                    ->select(
                        DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                        DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                        DB::raw('SUM(ct_ventas.descuento) as descuento'),
                        DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                        DB::raw('SUM(ct_ventas.impuesto) as impuesto')
                    )->get();
            } else {
                $venta_clienter = [];
            }

            $ventas_mes = $ventas_mes->groupBy('s.tipo')->select(
                's.tipo',
                DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                DB::raw('SUM(ct_ventas.descuento) as descuento'),
                DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                DB::raw('SUM(ct_ventas.impuesto) as impuesto')
            )->get();



            $estructure['mes'] = $mes;
            $total0 = [
                "subtotal0" => 0,
                "subtotal12" => 0,
                "descuento" => 0,
                "impuesto"  => 0,
                "cantidad"  => 0
            ];
            $estructure['privado']   = $total0;
            $estructure['publico']   = $total0;
            $estructure['particular']   = $total0;
            $estructure['particular_relacionado'] = $total0;
            

            foreach ($ventas_mes as $value) {
                $total = [
                    "subtotal0" => is_null($value->subtotal0) ? 0 : $value->subtotal0,
                    "subtotal12" => is_null($value->subtotal12) ? 0 : $value->subtotal12,
                    "descuento" => is_null($value->descuento) ? 0 : $value->descuento,
                    "impuesto"  => is_null($value->impuesto) ? 0 : $value->impuesto,
                    "cantidad"  => is_null($value->cantidadf) ? 0 : $value->cantidadf
                ];
                if ($value->tipo == 0) {
                    $estructure['publico'] = $total;
                }
                if ($value->tipo == 1) {
                    $estructure['privado'] = $total;
                }
                if ($value->tipo == 2) {
                    $estructure['particular'] = $total;
                }
            }

            foreach ($venta_clienter as $vr) {
                $total = [
                    "subtotal0" => is_null($vr->subtotal0) ? 0 : $vr->subtotal0,
                    "subtotal12" => is_null($vr->subtotal12) ? 0 : $vr->subtotal12,
                    "descuento" => is_null($vr->descuento) ? 0 : $vr->descuento,
                    "impuesto"  => is_null($vr->impuesto) ? 0 : $vr->impuesto,
                    "cantidad"  => is_null($vr->cantidadf) ? 0 : $vr->cantidadf
                ];

                if (!isset($data["particular"])) {
                    $data["particular"] = $vr;
                }

                if (!isset($data["particular_sin_seguro"])) {
                    $data["particular_sin_seguro"] = $vr;
                }

                if (!isset($data["particular_relacionado"])) {
                    $data["particular_relacionado"] = $vr;
                }

                if (!isset($data["privado"])) {
                    $data["privado"] = $vr;
                }

                if (!isset($data["publico"])) {
                    $data["publico"] = $vr;
                }


                $estructure['particular_relacionado'] = $total;

                array_push($array_agrupado, $estructure);
            }
        }

        //dd($array_agrupado);

        $array_agrupado_anio = array();
        $anios               = [$request->anio];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')

                ->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereYear('ct_ventas.fecha', $z)
                ->where('ct_ventas.estado', '<>', '0')
                ->select(
                    DB::raw('SUM(ct_ventas.total_final) as total_final'),
                    DB::raw('SUM(ingreso.total) as ingresado'),
                    DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'),
                    DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'),
                    DB::raw('SUM(CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'),
                    DB::raw('SUM( CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'),
                    DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'),
                    DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc')
                )->get();
                
            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        $empresa = Empresa::where('estado',1)->get();

        return view('contable/ventas/estadistico_nuevo', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'empresas' => $empresa, 'id_empresa' => $id_empresa, 'request' => $request]);
        //dd($array_agrupado_anio,$array_agrupado);p
    }
}
