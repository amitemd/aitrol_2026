<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\EstadoResultado;
use Sis_medico\Plan_Cuentas;

class ProyeccionFinanciera
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

        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));

        //dd("$hasta-$lastDay 23:59:59");

        $date = date_create(str_replace("/", "-", $hasta . '-01'));
        $date = date_format($date, "Y");
        //dd($totpyg);

        $fechagrupo = array();
        for ($i = $desde; $i <= $date; $i++) {
            array_push($fechagrupo, (int) $i);
            $lastDay  = date('t', strtotime("$i-01"));
            $hastanew = $i . '-12-31';
            if ($hastanew == $date) {
                $hastanew = $hasta;
            }
            $asiento[$i] = EstadoResultado::detalle_total_cuenta($i . '-01-01', $hastanew, $tipo);
            //dd($asiento);
            $saldo = $asiento[$i];
            if ($tipo == 'I') {
                $data['cuenta'] = '4';
                $data['nombre'] = 'Ingresos';
            } elseif ($tipo == 'C') {
                $data['cuenta'] = '5';
                $data['nombre'] = 'COSTOS';
            } else {
                $data['cuenta'] = '6';
                $data['nombre'] = 'GASTOS';
            }

            $data['anio']  = $i;
            $data['saldo'] = $saldo;
            //dd($asiento);
            //dd($i . '-01-01' . ' -- ' . $hastanew);
            $balance[$i][] = $data;
        }
        //dd($balance);
        return $balance;
    }

    public static function detalle_principal($desde, $hasta, $tipo, $cuentas_detalle = "")
    {
        $id_empresa   = Session::get('id_empresa');
        $balance      = array();
        $nombrecuenta = "";

        $cuentaactivo          = "1";
        $cuentaactivoc         = "1.01";
        $cuentaactivonoc       = "1.02";
        $cuentapasivo          = "2";
        $cuentapasivoc         = "2.01";
        $cuentainventario      = "1.01.03";
        $cuentapatrimonio      = "3";
        $cuentaingresos        = "4";
        $cuentagastos          = "5";
        $cuentaporcobrarloc    = "1.01.02.05";
        $cuentadeterioroinc    = "1.01.02.04";
        $cuentapasivosobligif  = "2.01.04";
        $cuentapasivosporpagar = "2.01.03";
        $cuentacostos          = "5.1";
        $cuentagastos          = "5.2";
        $cuentagastosventa     = "5.2.01";
        $cuentagastosadm       = "5.2.02";
        $cuentagastosfin       = "5.2.03";

        $plans = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 7')->where('id', 'like', "$cuentaactivo%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans2 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 7')->where('id', 'like', "$cuentapasivo%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans3 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentapatrimonio%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans4 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentaactivonoc%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans5 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentaingresos%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans6 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentagastos%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans7 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 11')->where('id', 'like', "$cuentaporcobrarloc%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans8 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 11')->where('id', 'like', "$cuentadeterioroinc%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans9 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentapasivosobligif%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans10 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentapasivosporpagar%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans11 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentacostos%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans12 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentagastos%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans13 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentainventario%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans14 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentagastosventa%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans15 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentagastosadm%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans16 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentagastosfin%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();

        $plans[1]  = $plans2[0];
        $plans[2]  = $plans3[0];
        $plans[3]  = $plans4[0];
        $plans[4]  = $plans5[0];
        $plans[5]  = $plans6[0];
        $plans[6]  = $plans7[0];
        $plans[7]  = $plans8[0];
        $plans[8]  = $plans9[0];
        $plans[9]  = $plans10[0];
        $plans[9]  = $plans10[0];
        $plans[10] = $plans11[0];
        $plans[11] = $plans12[0];
        $plans[12] = $plans13[0];
        $plans[13] = $plans14[0];
        $plans[14] = $plans15[0];
        $plans[15] = $plans16[0];

        //dd($desde);

        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));

        $data = array();
        $date = date_create(str_replace("/", "-", $hasta . '-01'));
        $date = date_format($date, "Y");
        //dd($totpyg);
        $fechagrupo = array();
        foreach ($plans as $plan) {
            $fechagrupo = array();
            if ($plan->id != "") {

                for ($i = $desde; $i <= $date; $i++) {
                    array_push($fechagrupo, (int) $i);
                    $lastDay  = date('t', strtotime("$i-01"));
                    $hastanew = $i . '-12';
                    if ($hastanew == 2020) {$hastanew = $hasta;}
                    $asiento[$i] = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('ct_asientos_detalle.estado', '<>', 0)
                        ->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('ct_asientos_detalle.fecha', [$i . '-01-01 00:00:00', "$hastanew-$lastDay 23:59:59"])
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
        //dd($balance);
        $cont      = 0;
        $datacount = 0;
        $datac     = array();
        $ventasant = 0;
        $gastosant = 0;
        $agastos   = 0;
        $aingresos = 0;

        foreach ($fechagrupo as $key => $anio) {
            //dd($anio);
            $datai           = array();
            $activoc         = 0;
            $pasivoc         = 0;
            $inventario      = 0;
            $patrimonio      = 0;
            $activo          = 0;
            $pasivo          = 0;
            $activonoc       = 0;
            $ingresos        = 0;
            $gastos          = 0;
            $uaii            = 0;
            $ventas          = 0;
            $cuentacobrarloc = 0;
            $deterioroinc    = 0;
            $pasivoxpagar    = 0;
            $pasivoinstfin   = 0;
            $costo           = 0;
            $gastos          = 0;
            $gastosventa     = 0;
            $gastoadm        = 0;
            $gastofin        = 0;
            $impuestos       = 0;
            $nombre          = "";
            //dd($balance['2011']);
            foreach ($balance[$anio] as $key => $value) {

                if ($value["cuenta"] == "1") {$activo = ($value["saldo"]);}
                if ($value["cuenta"] == "2") {$pasivo = ($value["saldo"]);}
                if ($value["cuenta"] == "3") {$patrimonio = ($value["saldo"]);}
                if ($value["cuenta"] == "1.02") {$activonoc = ($value["saldo"]);}
                if ($value["cuenta"] == "4") {$ingresos = ($value["saldo"]);}
                if ($value["cuenta"] == "5") {$gastos = ($value["saldo"]);}
                if ($value["cuenta"] == "1.01.02.05") {$cuentacobrarloc = ($value["saldo"]);}
                if ($value["cuenta"] == "1.01.02.04") {$deterioroinc = ($value["saldo"]);}
                if ($value["cuenta"] == "2.01.03") {$pasivoxpagar = ($value["saldo"]);}
                if ($value["cuenta"] == "2.01.04") {$pasivoinstfin = ($value["saldo"]);}
                if ($value["cuenta"] == "5.1") {$costo = ($value["saldo"]);}
                if ($value["cuenta"] == "5.2") {$gastos = ($value["saldo"]);}
                if ($value["cuenta"] == "1.01.03") {$inventario = ($value["saldo"]);}
                if ($value["cuenta"] == "5.2.01") {$gastosventa = ($value["saldo"]);}
                if ($value["cuenta"] == "5.2.02") {$gastoadm = ($value["saldo"]);}
                if ($value["cuenta"] == "5.2.03") {$gastofin = ($value["saldo"]);}

            }
            //dd($balance);
            //if ($anio==2020) { dd($activo ); }
            $uai       = (($ingresos - $gastos));
            $utilidad  = ($uai - ($ingresos * 0.15));
            $utilidadg = ($ingresos - $gastos);
            //if ($anio==2020) { dd($utilidadg);}
            $uaiT  = $activo > 0 ? (($uai / $activo)) : 0.00;
            $uaiiT = $patrimonio > 0 ? (($utilidad / $patrimonio)) : 0.00;

            $ventas          = $ingresos;
            $cuentacobrarT   = $cuentacobrarloc + $deterioroinc;
            $pasivoscuentasT = $pasivoxpagar + $pasivoinstfin;
            $rotacioninv     = $inventario > 0 ? (($costo / $inventario)) : 0.00;

            //if ($anio==2020) { dd($ingresos ); }
            //setlocale(LC_MONETARY, 'en_US');

            //$datai["cuenta"]="1";
            $datai["cuenta"] = $datacount;
            //$datai["nombre"]=;
            $datai["saldo"]  = 0;
            $datai["costos"] = ($ventas);
            $datai["gastos"] = ($gastos);

            // dd($trabajopart);
            if ($datacount > 0) {
                //dd($datac[$anio-1][0]["costos"]);
                $vventas = $ventasant;
                $vgastos = $gastosant;

            } else {
                $vventas = 0;
                $vgastos = 0;

            }
            $aingresos += $ventas;
            $agastos += $gastos;

            $datai["vingresos"] = ($vventas != 0) ? number_format((($ventas / $vventas) - 1) * 100, 0) : 0.00;
            $datai["vegresos"]  = ($vgastos != 0) ? number_format((($gastos / $vgastos) - 1) * 100, 0) : 0.00;

            $datai["xy"] = (($ventas * $gastos));
            $datai["x2"] = (($ventas * $ventas));
            $datai["y2"] = (($gastos * $gastos));

            $datai["aingresos"] = ($ventas);
            $datai["agastos"]   = ($gastos);

            $datai["avingresos"] = ($vventas != 0) ? (($ventas / $vventas) - 1) * 100 : 0.00;
            $datai["avgastos"]   = ($vgastos != 0) ? (($gastos / $vgastos) - 1) * 100 : 0.00;

            if ($datacount == 0) {
                $datai["vingresos"] = " - ";
                $datai["vegresos"]  = " - ";

            }

            $datac[$anio][0] = $datai;

            $ventasant = $ingresos;
            $gastosant = $gastos;

            $datacount++;
        }

        return $datac;
    }

    public static function detalle_indicadorfinanciero($desde, $hasta, $tipo, $cuentas_detalle = "")
    {
        $id_empresa   = Session::get('id_empresa');
        $balance      = array();
        $nombrecuenta = "";

        $cuentaactivoc    = "1.01";
        $cuentapasivoc    = "2.01";
        $cuentainventario = "1.01.03";

        $plans = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 7')->where('id', 'like', "$cuentaactivoc%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans2 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 7')->where('id', 'like', "$cuentapasivoc%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans3 = Plan_Cuentas::where('estado', '<>', 0)
            ->whereRaw('character_length(id) < 10')->where('id', 'like', "$cuentainventario%")
            ->select('id', 'nombre')->orderBy('id', 'asc')->get();
        $plans[1] = $plans2[0];
        $plans[2] = $plans3[0];

        //dd($desde);

        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));

        //dd("$hasta-$lastDay 23:59:59");
        $data = array();
        $date = date_create(str_replace("/", "-", $hasta . '-01'));
        $date = date_format($date, "Y");
        //dd($totpyg);
        $fechagrupo = array();
        foreach ($plans as $plan) {
            $fechagrupo = array();
            if ($plan->id != "") {

                for ($i = $desde; $i <= $date; $i++) {
                    array_push($fechagrupo, (int) $i);
                    $lastDay  = date('t', strtotime("$i-01"));
                    $hastanew = $i . '-12';
                    if ($hastanew == 2020) {$hastanew = $hasta;}
                    $asiento[$i] = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('ct_asientos_detalle.estado', '<>', 0)
                        ->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('ct_asientos_detalle.fecha', [$i . '-01-01 00:00:00', "$hastanew-$lastDay 23:59:59"])
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
        //dd($balance);

        //dd($datac);
        // dd($fechagrupo);

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
                        ->where('ct_asientos_detalle.estado', '<>', 0)
                    //->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('ct_asientos_detalle.fecha', ["$i-01-01 00:00:00", "$hastanew 23:59:59"])
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
