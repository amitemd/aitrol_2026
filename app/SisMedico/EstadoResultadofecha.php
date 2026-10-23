<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;

class EstadoResultadofecha
{

    public static function detalle_estadopg($desde, $hasta, $tipo, $cuentas_detalle = "")
    {
        $id_empresa = Session::get('id_empresa');
        $balance    = array();
        if ($tipo == 'I') {
            $condicion = '4';
        } elseif ($tipo == 'C') {
            $condicion = '5';
        } else {
            $condicion = '6';
        }
        if ($cuentas_detalle == "") {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) < 9')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
        }

        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));

        //dd("$hasta-$lastDay 23:59:59");
        foreach ($plans as $plan) {
            $data = array();
            if ($plan->id != "") {
                $date = date_create(str_replace("/", "-", $hasta . '-01'));
                $date = date_format($date, "Y");
                //dd($totpyg);

                $fechagrupo = array();
                for ($i = $desde; $i <= $date; $i++) {
                    array_push($fechagrupo, (int) $i);
                    $lastDay  = date('t', strtotime("$i-01"));
                    $hastanew = $i . '-12';
                    if ($hastanew == 2020) {$hastanew = $hasta;}
                    $asiento[$i] = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('c.fecha_asiento', [$i . '01-01 00:00:00', "$hastanew-$lastDay 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe - haber),0) as saldo'), DB::raw('YEAR(fecha) as fechag'))
                        ->groupBy("fechag")
                        ->get();
                    $saldo = 0;

                    //dd($asiento);

                    foreach ($asiento[$i] as $row) {
                        $saldo = $row->saldo;
                    }
                    $data['cuenta'] = $plan->id;
                    $data['nombre'] = strtoupper($plan->nombre);
                    $data['anio']   = $i;
                    $data['saldo']  = $saldo;
                    $balance[$i][]  = $data;
                }
            }

        }
        return $balance;
    }

    public static function detalle_total_cuenta($desde, $hasta, $tipo, $afecha = null)
    {
        $id_empresa = Session::get('id_empresa');
        $balance    = array();
        if ($tipo == 'I') {
            $condicion = '4';
        } elseif ($tipo == 'C') {
            $condicion = '5';
        } else {
            $condicion = '6';
        }

        $plans = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) = 1')
            ->where('id', "$condicion")
        //->orwhere('id', 'like', "$condicion")
            ->select('id', 'nombre')
            ->orderBy('id', 'asc')
            ->get();
        $i       = 0;
        $saldo   = 0;
        $lastDay = date('t', strtotime("$hasta-01"));
        //dd("$hasta-$lastDay 23:59:59");
        if ($afecha == null) {
            //$desde = "$desde-01-01";
            $hasta = "$hasta-$lastDay";
        }
        foreach ($plans as $plan) {
            $saldo = 0;
            $date  = date_create(str_replace("/", "-", $hasta . '-01'));
            $date  = date_format($date, "Y");
            //dd($desde);
            if ($plan->id != "") {
                $fechagrupo = array();
                for ($i = $desde; $i <= $date; $i++) {
                    array_push($fechagrupo, (int) $i);
                    $lastDay  = date('t', strtotime("$i-01"));
                    $hastanew = $i . '-12';
                    if ($hastanew == 2020) {$hastanew = $hasta;}
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'id_plan_cuenta', 'p.id')
                    //->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('c.fecha_asiento', ["$i-01-01 00:00:00", "$hastanew 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe - haber),0) as saldo'))
                        ->get();
                    $saldo = 0;
                    foreach ($asiento as $row) {
                        $saldo = $row->saldo;
                    }

                    $balance[$i][] = $saldo;
                }

            }

        }

        return $balance;

    }

    public static function detalle_total_pyg($desde, $hasta, $afecha = null)
    {
        $totpyg = array();
        //dd($desde.'');

        $toting = EstadoResultadofecha::detalle_total_cuenta($desde, $hasta, 'I', $afecha);
        $totcos = EstadoResultadofecha::detalle_total_cuenta($desde, $hasta, 'C', $afecha);
        $totgas = EstadoResultadofecha::detalle_total_cuenta($desde, $hasta, 'G', $afecha);
        //dd($totgas);
        $date = date_create(str_replace("/", "-", $hasta . '-01'));
        $date = date_format($date, "Y");
        for ($i = $desde; $i <= $date; $i++) {
            //if(!$totgas) { $totgas[$i][0] }
            $totpyg[$i][] = $toting[$i][0] - ($totcos[$i][0]+@$totgas[$i][0]);
        }
        //dd($totpyg);

        return $totpyg;
    }

}
