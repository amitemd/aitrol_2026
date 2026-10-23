<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;

class IndicadorConsolidadofecha
{
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
        //dd($date);
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
                    if ($hastanew == $date) {
                        $hastanew = str_replace('/', '-', $hasta);
                    }
                    $asiento[$i] = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa);

                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento[$i]  = $asiento[$i]->where('p.nombre', 'not like', '%-)%');
                        $asiento2[$i] = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->whereBetween('c.fecha_asiento', [$i . '-01-01 00:00:00', "$hastanew-$lastDay 23:59:59"]);
                        if ($plan->naturaleza_2 == 1) {
                            $asiento2[$i] = $asiento2[$i]->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                                ->get();
                        } else {
                            $asiento2[$i] = $asiento2[$i]->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                                ->get();
                        }
                        //dd($asiento2);

                        foreach ($asiento2[$i] as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    //dd([$i . '-01-01 00:00:00', "$hastanew-$lastDay 23:59:59"]);
                    //dd($asiento);

                    /*if ($plan->id == '3.06') {
                    dd($asiento2);

                    }*/
                    $asiento[$i] = $asiento[$i]->whereBetween('c.fecha_asiento', [$i . '-01-01 00:00:00', "$hastanew-$lastDay 23:59:59"]);
                    if ($plan->naturaleza_2 == 1) {
                        $asiento[$i] = $asiento[$i]->select(DB::raw('ifnull(SUM(haber - debe),0) as saldo'), DB::raw('YEAR(fecha) as fechag'))
                            ->groupBy("fechag")
                            ->get();
                    } else {
                        $asiento[$i] = $asiento[$i]->select(DB::raw('ifnull(SUM(debe - haber),0) as saldo'), DB::raw('YEAR(fecha) as fechag'))
                            ->groupBy("fechag")
                            ->get();
                    }

                    //termina aqui

                    $saldo = 0;
                    dd($asiento . $saldo2);

                    //dd($asiento);

                    foreach ($asiento[$i] as $row) {
                        if ($row->saldo == 0) {
                            $saldo = $saldo2 * (-1);
                        } elseif ($saldo2 == 0) {
                            $saldo = $row->saldo;
                        } elseif ($saldo2 < 0) {
                            $saldo = $row->saldo + $saldo2;
                        } else {
                            $saldo = $row->saldo - $saldo2;

                        }
                    }

                    $data['cuenta'] = $plan->id;
                    $data['nombre'] = strtoupper($plan->nombre);
                    $data['anio']   = $i;
                    $data['saldo']  = $saldo;
                    $balance[$i][]  = $data;

                }
            }

        }

        $cont  = 0;
        $datac = array();
        foreach ($fechagrupo as $key => $anio) {
            //dd($anio);
            $datai      = array();
            $activoc    = 0;
            $pasivoc    = 0;
            $inventario = 0;
            $patrimonio = 0;
            foreach ($balance[$anio] as $key => $value) {
                if ($value["cuenta"] == "1.01") {$activoc = ($value["saldo"]);}
                if ($value["cuenta"] == "2.01") {$pasivoc = ($value["saldo"]);}
                if ($value["cuenta"] == "1.01.03") {$inventario = ($value["saldo"]);}
            }
            //dd($balance);

            //setlocale(LC_MONETARY, 'en_US');

            //$datai["cuenta"]="1";
            $datai["cuenta"] = "1";
            $datai["nombre"] = "LIQUIDEZ CORRIENTE";
            $datai["saldo"]  = $pasivoc != 0 ? number_format((($activoc / $pasivoc)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][0] = $datai;

            $datai["cuenta"] = "2";
            $datai["nombre"] = "PRUEBA ÁCIDA";
            $datai["saldo"]  = $pasivoc != 0 ? number_format(((($activoc - $inventario) / $pasivoc)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][1] = $datai;

            $datai["cuenta"] = "3";
            $datai["nombre"] = "CAPITAL TRABAJO NETO";
            $datai["saldo"]  = (number_format(($activoc - $pasivoc), 2));
            $datai["costos"] = 0;
            $datac[$anio][2] = $datai;

        }
        //dd($datac);
        dd($datac);

        return $datac;
    }

    public static function detalle_indicador_solvencia($desde, $hasta, $tipo, $cuentas_detalle = "")
    {
        $id_empresa   = Session::get('id_empresa');
        $balance      = array();
        $nombrecuenta = "";

        $cuentaactivo     = "1";
        $cuentaactivoc    = "1.01";
        $cuentaactivonoc  = "1.02";
        $cuentapasivo     = "2";
        $cuentapasivoc    = "2.01";
        $cuentainventario = "1.01.03";
        $cuentapatrimonio = "3";
        $cuentaingresos   = "4";
        $cuentagastos     = "5";

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
        $plans[1] = $plans2[0];
        $plans[2] = $plans3[0];
        $plans[3] = $plans4[0];
        $plans[4] = $plans5[0];
        $plans[5] = $plans6[0];

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

        $cont  = 0;
        $datac = array();
        foreach ($fechagrupo as $key => $anio) {
            //dd($anio);
            $datai      = array();
            $activoc    = 0;
            $pasivoc    = 0;
            $inventario = 0;
            $patrimonio = 0;
            $activo     = 0;
            $pasivo     = 0;
            $activonoc  = 0;
            $ingresos   = 0;
            $gastos     = 0;
            $uaii       = 0;
            foreach ($balance[$anio] as $key => $value) {
                if ($value["cuenta"] == "1") {$activo = ($value["saldo"]);}
                if ($value["cuenta"] == "2") {$pasivo = ($value["saldo"]);}
                if ($value["cuenta"] == "3") {$patrimonio = ($value["saldo"]);}
                if ($value["cuenta"] == "1.02") {$activonoc = ($value["saldo"]);}
                if ($value["cuenta"] == "4") {$ingresos = ($value["saldo"]);}
                if ($value["cuenta"] == "5") {$gastos = ($value["saldo"]);}

            }
            //dd($balance);
            //dd($activo);
            //if ($anio==2020) { dd($activo ); }
            $uai      = (($ingresos - $gastos));
            $utilidad = ($ingresos - ($ingresos * 0.15));
            $uaiT     = $activo > 0 ? (($uai / $activo)) : 0.00;
            $uaiiT    = $patrimonio > 0 ? (($utilidad / $patrimonio)) : 0.00;
            //if ($anio==2020) { dd($uaiT/$uaiiT ); }
            //setlocale(LC_MONETARY, 'en_US');

            //$datai["cuenta"]="1";
            $datai["cuenta"] = "1";
            $datai["nombre"] = "ENDEUDAMIENTO DEL ACTIVO";
            $datai["saldo"]  = $activo != 0 ? number_format((($pasivo / $activo)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][0] = $datai;

            $datai["cuenta"] = "2";
            $datai["nombre"] = "ENDEUDAMIENTO PATRIMONIAL";
            $datai["saldo"]  = $patrimonio != 0 ? number_format(((($pasivo) / $patrimonio)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][1] = $datai;

            $datai["cuenta"] = "3";
            $datai["nombre"] = "ENDEUDAMIENTO DEL ACTIVO FIJO";
            $datai["saldo"]  = $activonoc != 0 ? number_format((($patrimonio / $activonoc)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][2] = $datai;

            $datai["cuenta"] = "4";
            $datai["nombre"] = "APALANCAMIENTO";
            $datai["saldo"]  = $activo != 0 ? number_format((($patrimonio / $activo)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][3] = $datai;

            $datai["cuenta"] = "5";
            $datai["nombre"] = "APALANCAMIENTO FINANCIERO";
            $datai["saldo"]  = $uaiiT != 0 ? number_format((($uaiT / $uaiiT)), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][4] = $datai;

        }
        //dd($datac);
        // dd($fechagrupo);

        return $datac;
    }

    public static function detalle_indicador_gestion($desde, $hasta, $tipo, $cuentas_detalle = "")
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

        $cont  = 0;
        $datac = array();
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
            //dd($activo);
            //if ($anio==2020) { dd($activo ); }
            $uai      = (($ingresos - $gastos));
            $utilidad = ($ingresos - ($ingresos * 0.15));
            $uaiT     = $activo > 0 ? (($uai / $activo)) : 0.00;
            $uaiiT    = $patrimonio > 0 ? (($utilidad / $patrimonio)) : 0.00;

            $ventas = $ingresos;

            $cuentacobrarT   = $cuentacobrarloc + $deterioroinc;
            $pasivoscuentasT = $pasivoxpagar + $pasivoinstfin;
            $rotacioninv     = $inventario > 0 ? (($costo / $inventario)) : 0.00;

            //if ($anio==2020) { dd($costo ); }
            //setlocale(LC_MONETARY, 'en_US');

            //$datai["cuenta"]="1";
            $datai["cuenta"] = "1";
            $datai["nombre"] = "ROTACIÓN DE CARTERA";
            $datai["saldo"]  = $cuentacobrarT != 0 ? number_format(($ingresos / $cuentacobrarT), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][0] = $datai;

            $datai["cuenta"] = "2";
            $datai["nombre"] = "ROTACIÓN DEL ACTIVO FIJO";
            $datai["saldo"]  = $activonoc != 0 ? number_format(($ingresos / $activonoc), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][1] = $datai;

            $datai["cuenta"] = "3";
            $datai["nombre"] = "ROTACIÓN DE VENTAS";
            $datai["saldo"]  = $activo != 0 ? number_format(($ventas / $activo), 2) : '0.00';
            $datai["costos"] = 0;
            $datac[$anio][2] = $datai;

            $datai["cuenta"] = "4";
            $datai["nombre"] = "PERIODO MEDIO COBRANZA";
            $datai["saldo"]  = $ventas != 0 ? number_format(($cuentacobrarloc / $ventas), 2) : '0.00';
            $datai["costos"] = 0.00;
            $datac[$anio][3] = $datai;

            $datai["cuenta"] = "5";
            $datai["nombre"] = "PERIODO MEDIO PAGO";
            $datai["saldo"]  = $costo != 0 ? number_format((($pasivoscuentasT / $costo)), 2) : '0.00';
            $datai["costos"] = 0.00;
            $datac[$anio][4] = $datai;

            $datai["cuenta"] = "6";
            $datai["nombre"] = "IMPACTO GASTO ADMINISTRACIÓN Y VENTAS";
            $datai["saldo"]  = $ventas != 0 ? number_format((($gastoadm / $ventas)), 2) : '0.00';

            $datac[$anio][5] = $datai;

            $datai["cuenta"] = "7";
            $datai["nombre"] = "IMPACTO DE LA CARGA FINANCIERA";
            $datai["saldo"]  = $ventas != 0 ? number_format((($gastofin / $ventas)), 2) : '0.00';

            $datac[$anio][6] = $datai;

            $datai["cuenta"] = "8";
            $datai["nombre"] = "ROTACIÓN DE INVENTARIO";
            $datai["saldo"]  = $inventario != 0 ? number_format((($costo / $inventario)), 2) : '0.00';
            $datac[$anio][7] = $datai;

            $datai["cuenta"] = "9";
            $datai["nombre"] = "PERIODO DE INVENTARIO";
            $datai["saldo"]  = $rotacioninv != 0 ? (number_format((360 / $rotacioninv), 2)) : '0.00';
            $datac[$anio][8] = $datai;
        }
        //dd($datac);
        // dd($fechagrupo);

        return $datac;
    }

    public static function detalle_indicador_rentabilidad($desde, $hasta, $tipo, $cuentas_detalle = "")
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

        $cont  = 0;
        $datac = array();
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

            $ventas = $ingresos;

            $cuentacobrarT   = $cuentacobrarloc + $deterioroinc;
            $pasivoscuentasT = $pasivoxpagar + $pasivoinstfin;
            $rotacioninv     = $inventario > 0 ? (($costo / $inventario)) : 0.00;

            //if ($anio==2020) { dd($costo ); }
            //setlocale(LC_MONETARY, 'en_US');

            //$datai["cuenta"]="1";
            $datai["cuenta"] = "1";
            $datai["nombre"] = "RENTABILIDAD NETA DE ACTIVO";
            $datai["saldo"]  = ($ventas != 0 && $activo != 0) ? number_format(($utilidad / $ventas) * ($ventas / $activo), 2) . ' %' : '0.00 %';
            $datai["costos"] = 0;
            $datac[$anio][0] = $datai;

            $datai["cuenta"] = "2";
            $datai["nombre"] = "MARGEN BRUTO";
            $datai["saldo"]  = $activonoc != 0 ? number_format(($ventas - $gastosventa) / ($activonoc), 2) . ' %' : '0.00 %';
            $datai["costos"] = 0;
            $datac[$anio][1] = $datai;

            $datai["cuenta"] = "3";
            $datai["nombre"] = "MARGEN OPERACIONAL";
            $datai["saldo"]  = $ventas != 0 ? number_format(($utilidadg / $ventas), 2) . ' %' : '0.00 %';
            $datai["costos"] = 0;
            $datac[$anio][2] = $datai;

            $datai["cuenta"] = "4";
            $datai["nombre"] = "RENTA NETA DE VENTAS";
            $datai["saldo"]  = $ventas != 0 ? number_format(($utilidad / $ventas), 2) . ' %' : '0.00 %';
            $datai["costos"] = 0.00;
            $datac[$anio][3] = $datai;

            $datai["cuenta"] = "5";
            $datai["nombre"] = "RENTABILIDAD OPERACIONAL DEL PATRIMONIO";
            $datai["saldo"]  = $patrimonio != 0 ? number_format((($utilidadg / $patrimonio)), 2) . ' %' : '0.00 %';
            $datai["costos"] = 0.00;
            $datac[$anio][4] = $datai;

            $datai["cuenta"] = "6";
            $datai["nombre"] = "RENTABILIDAD FINANCIERA";
            $datai["saldo"]  = ($activo != 0 && $ventas != 0 && $patrimonio != 0 && $ventas != 0 && $uaiiT != 0 && $uaiT != 0) ? number_format((($ventas / $activo) * ($uaiiT / $ventas) * ($activo / $patrimonio) * ($uaiT / $uaiiT) * ($utilidadg / $uaiT)), 2) . ' %' : '0.00 %';

            $datac[$anio][5] = $datai;
        }
        //dd($datac);
        // dd($fechagrupo);

        return $datac;
    }

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
                ->whereRaw('character_length(id) < 7')
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
                        ->where('ct_asientos_detalle.estado', '<>', 0)
                        ->where('p.estado', '<>', 0)
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('ct_asientos_detalle.fecha', [$i . '01-01 00:00:00', "$hastanew-$lastDay 23:59:59"])
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
