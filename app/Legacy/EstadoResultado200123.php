<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Nomina;
use Sis_medico\Plan_Cuentas;

class EstadoResultado
{

    public static function detalle_estadopg($desde, $hasta, $tipo, $cuentas_detalle = "", $especial = 0, $cierre = 0, $mostrar_acumulado = 0)
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
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }

        if ($cierre == 1) {
            $hasta = $hasta . ' 23:59:58';
        } else {
            $hasta = $hasta . ' 23:59:59';
        }
        if ($cuentas_detalle == "") {
            $plans = Plan_Cuentas::where('p.estado', '<>', 0)
                ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
                ->where('p.id_empresa', session()->get('id_empresa'))
                ->whereRaw('character_length(p.plan) < 7')
                ->where('p.plan', 'like', "$condicion%")
                //->orwhere('id', 'like', "$condicion")
                ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2', 'p.estado')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('p.estado', '<>', 0)
                ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
                ->where('p.id_empresa', session()->get('id_empresa'))
                ->where('p.plan', 'like', "$condicion%")
                //->orwhere('id', 'like', "$condicion")
                ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2', 'p.estado')
                ->orderBy('id', 'asc')
                ->get();
        }

        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));
        //dd("$hasta-$lastDay 23:59:59");
        foreach ($plans as $plan) {
            $data = array();
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                    ->where('p.plan', 'like', $plan->id . '%')
                    ->where('p.id_empresa', $id_empresa)
                    ->where('c.id_empresa', $id_empresa);
                if ($especial == 1) {
                    dd("entra");
                }
                //->where('c.estado', '<>', 0);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.naturaleza', '1');
                    $asiento2 = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                        ->where('p.plan', 'like', $plan->id . '%')
                        ->where('p.id_empresa', $id_empresa)
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.naturaleza', 'like', '0')
                        //->where('c.estado', '<>', 0)
                        ->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta"]);
                    if ($plan->naturaleza_2 == 1) {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                            ->get();
                    } else {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                    }
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }

                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta"]);

                if (Auth::user()->id == '0922729587') {
                    //dd($desde . ' -- ' . $hasta);
                }
                if ($plan->naturaleza_2 == 1) {
                    /*if ($id_empresa == '0993075000001' and $plan->id == '4.1.01.01') {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                    ->toSql();
                    dd($asiento);
                    }*/
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                        ->get();
                } else {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                }

                $saldo = 0;
                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }
                $data['cuenta'] = $plan->id;
                $data['nombre'] = strtoupper($plan->nombre);
                $data['saldo']  = $saldo;
                $balance[]      = $data;
            }
        }

        return $balance;
    }

    public static function detalle_total_cuenta($desde, $hasta, $tipo, $afecha = null, $tipo_2 = 0, $cierre = 0, $mostrar_acumulado = 0)
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

        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }

        $plans = Plan_Cuentas::where('p.estado', '<>', 0)
            ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
            ->whereRaw('character_length(p.plan) = 1')
            ->where('p.plan', "$condicion")
            ->where('p.id_empresa', $id_empresa)
            ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2')
            ->orderBy('p.plan', 'asc')
            ->get();
        $i       = 0;
        $saldo   = 0;
        $lastDay = date('t', strtotime("$hasta-12"));
        //dd("$hasta-$lastDay 23:59:59");
        if ($afecha == '2') {
            $desde = "$desde-01-01";
            $hasta = "$hasta-12-31";
        }

        if ($cierre == 1) {
            $hasta = $hasta . ' 23:59:58';
        } else {
            $hasta = $hasta . ' 23:59:59';
        }
        foreach ($plans as $plan) {
            $saldo = 0;
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                    ->where('p.plan', 'like', $plan->id . '%')
                    ->where('p.id_empresa', $id_empresa)
                    ->where('c.id_empresa', $id_empresa);
                if ($tipo_2 == 1) {
                    $asiento = $asiento->where('aparece_sri', 1);
                }
                //->where('c.estado', '<>', 0);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                    $asiento2 = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                        ->where('p.plan', 'like', $plan->id . '%')
                        ->where('p.id_empresa', $id_empresa)
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.nombre', 'like', '%-)%')
                        //->where('c.estado', '<>', 0)
                        ->whereBetween('c.fecha_asiento', ["$desde 00:00:00", "$hasta"]);
                    if ($tipo_2 == 1) {
                        $asiento2 = $asiento2->where('aparece_sri', 1);
                    }
                    if ($plan->naturaleza_2 == 1) {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                            ->get();
                    } else {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                    }
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta"]);
                if ($plan->naturaleza_2 == 1) {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                        ->get();
                } else {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                }
                $saldo = 0;
                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }
            }
        }

        return $saldo;
    }

    public static function detalle_total_cuenta_general($desde, $hasta, $tipo, $afecha = null, $tipo_2 = 0, $cierre = 0)
    {
        $id_empresa = Session::get('id_empresa');
        $balance    = array();
        $condicion  = $tipo;

        $plans = Plan_Cuentas::where('p.estado', '<>', 0)
            ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
            ->where('p.plan', "$condicion")
            ->where('p.id_empresa', $id_empresa)
            ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2')
            ->orderBy('id', 'asc')
            ->get();



        $i       = 0;
        $saldo   = 0;
        $lastDay = date('t', strtotime("$hasta-12"));
        //dd("$hasta-$lastDay 23:59:59");
        if ($afecha == '2') {
            $desde = "$desde-01-01";
            $hasta = "$hasta-12-31";
        }

        if ($cierre == 1) {
            $hasta = $hasta . ' 23:59:58';
        } else {
            $hasta = $hasta . ' 23:59:59';
        }

        //dd($desde . ' -- ' . $hasta);
        foreach ($plans as $plan) {
            $saldo = 0;
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                    ->where('p.plan', 'like', $plan->id . '%')
                    ->where('p.id_empresa', $id_empresa)
                    ->where('c.id_empresa', $id_empresa);
                if ($tipo_2 == 1) {
                    $asiento = $asiento->where('aparece_sri', 1);
                }
                //->where('c.estado', '<>', 0);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                    $asiento2 = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                        ->where('p.plan', 'like', $plan->id . '%')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.id_empresa', $id_empresa)
                        ->where('p.nombre', 'like', '%-)%')
                        //->where('c.estado', '<>', 0)
                        ->whereBetween('c.fecha_asiento', ["$desde 00:00:00", "$hasta"]);
                    if ($tipo_2 == 1) {
                        $asiento2 = $asiento2->where('aparece_sri', 1);
                    }
                    if ($plan->naturaleza_2 == 1) {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                            ->get();
                    } else {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                    }
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta"]);
                if (Auth::user()->id == '0922729587') {
                    //dd($desde . ' -- ' . $hasta);
                }
                if ($plan->naturaleza_2 == 1) {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                        ->get();
                } else {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                }

                if (Auth::user()->id == '0922729587') {
                    //dd($plan->id);
                }
                if ($condicion == '2.01') {
                    //dd($asiento . $saldo2);
                }
                if (Auth::user()->id == '0922729587') {
                    //dd($row);
                }

                $saldo = 0;
                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }
            }
        }

        return $saldo;
    }

    public static function detalle_total_pyg($desde, $hasta, $afecha = null, $cierre = 0, $mostrar_acumulado = 0)
    {
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }
        $totpyg = 0;
        $toting = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha, 0, $cierre);
        $totcos = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha, 0, $cierre);
        $totgas = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha, 0, $cierre);
        //dd($toting . ' -- ' . $totcos . ' -- ' . $totgas);
        $totpyg = $toting - ($totcos + $totgas);
        return $totpyg;
    }

    public static function trabajadores($desde, $hasta, $id_empresa, $afecha = null, $trabajadores = [], $cierre = 0, $mostrar_acumulado = 0)
    {

        if (Auth::user()->id == '0922729587') {
            //dd($cierre);
        }

        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }
        $anio1 = date('Y', strtotime($desde));
        $anio2 = date('Y', strtotime($hasta));
        if ($afecha != 2) {
            $anio1 = date('Y', strtotime($desde));
            $anio2 = date('Y', strtotime($hasta));
        } else {
            $anio1  = $desde;
            $anio2  = $hasta;
            $desde  = $desde . '-01-01';
            $hasta  = $hasta . '-12-31';
            $afecha = null;
        }

        $anios = $anio2 - $anio1;

        $datos           = 0;
        $anio_inicio     = $anio1;
        $renta_acumulada = 0;
        $t_trabajadores  = 0;
        for ($i = 0; $i <= $anios; $i++) {
            $fecha_valida1 = $anio_inicio . '-01-01';
            $fecha_valida2 = $anio_inicio . '-12-31';
            if ($desde >= $fecha_valida1) {
                $fecha_buscar_desde = $desde;
            } else {
                $fecha_buscar_desde = $fecha_valida1;
            }
            if ($hasta <= $fecha_valida2) {
                $fecha_buscar_hasta = $hasta;
            } else {
                $fecha_buscar_hasta = $fecha_valida2;
            }
            $totpyg = 0;

            if ($cierre == 1 && $fecha_buscar_hasta == $hasta) {
                $toting = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'I', $afecha, 0, $cierre);
                $totcos = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'C', $afecha, 0, $cierre);
                $totgas = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'G', $afecha, 0, $cierre);
            } else {
                $toting = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'I', $afecha);
                $totcos = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'C', $afecha);
                $totgas = EstadoResultado::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'G', $afecha);
            }

            $totpyg_veri = $toting - ($totcos + $totgas);

            $empleados = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $fecha_buscar_hasta)->count();
            $totpyg    = 0;
            if ($empleados > 0) {
                $totpyg = round(($toting - ($totcos + $totgas)) * 0.15, 2);
                $total  = ($toting - ($totcos + $totgas));
                if ($total < 0) {
                    $totpyg = 0;
                }
            }
            $t_trabajadores += $totpyg;
            $anio_inicio++;
        }

        return $t_trabajadores;
    }

    public static function utilidad_gravable($desde, $hasta, $id_empresa, $afecha = null, $trabajadores = null, $cierre = 0, $mostrar_acumulado = 0)
    {

        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01';
        }
        $totpyg = 0;
        $toting = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha, 0, $cierre);
        $totcos = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha, 0, $cierre);
        $totgas = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha, 0, $cierre);
        $totpyg = $toting - ($totcos + $totgas);
        if ($afecha != '2') {
            $empleados = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta)->count();
        } else {
            $empleados = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta . '-12-31')->count();
        }
        //dd($empleados);
        $trabajadores = 0;
        if ($empleados > 0) {
            $trabajadores = EstadoResultado::trabajadores($desde, $hasta, $id_empresa, $afecha);
            //dd($trabajadores);
        }
        $total = $totpyg - $trabajadores;
        //dd($total);

        //dd($totpyg . ' = ' . $toting . ' - (' . $totcos . ' + ' . $totgas . ' )');
        //dd($total);
        return $total;
    }

    public static function utilidad_gravable_2($desde, $hasta, $id_empresa, $afecha = null, $mostrar_cierre = 0, $mostrar_acumulado = 0)
    {
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01';
        }
        $cierre = DB::table('log_cierre_de_anio')
            ->where('id_empresa', $id_empresa)
            ->where('fecha_asiento', '>', $desde . ' 00:00:00')
            ->where('fecha_asiento', '<', $hasta . ' 00:00:00')
            ->orderBy('fecha_asiento', 'Desc')
            ->first();
        if (!is_null($cierre)) {
            $desde = date("Y-m-d", strtotime($cierre->fecha_asiento . "+ 1 days"));
        }
        $totpyg       = 0;
        $toting       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha, 1, $mostrar_cierre);
        $totcos       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha, 1, $mostrar_cierre);
        $totgas       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha, 1, $mostrar_cierre);
        $totpyg       = $toting - ($totcos + $totgas);
        $empleados    = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta)->count();
        $trabajadores = 0;
        if (Auth::user()->id == "0922729587") {
            //dd($toting . ' -- ' . $totcos . ' -- ' . $totgas);
        }
        if ($empleados > 0) {
            $trabajadores = EstadoResultado::trabajadores($desde, $hasta, $id_empresa, $afecha);

            if ($totpyg < 0) {
                $trabajadores = 0;
            }
        }

        $total = $totpyg - $trabajadores;

        $cierre_2 = DB::table('log_cierre_de_anio')
            ->where('id_empresa', $id_empresa)
            ->where('fecha_asiento', $hasta . ' 23:59:59')
            ->orderBy('fecha_asiento', 'Desc')
            ->first();

        if (!is_null($cierre_2) && $mostrar_cierre == 0) {
            return 0;
        }
        return $total;
    }

    public static function impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, $afecha = null, $cierre = 0, $mostrar_acumulado = 0, $ingreso = [])
    {
        $totalIngreso = 0;
        /*if ($fecha_desde == '2010-01-01') {
            $anioHasta = explode('-', $fecha_hasta);
            $anioDesde = explode('-', $fecha_desde);
            $fecha_desde = $anioHasta[0] . '-' . $anioDesde[1] . '-' . $anioDesde[2];
        }*/
        /*if (Auth::user()->id == '0921605895') {
            if ($fecha_desde == '2010-01-01') {
                $anioHasta = explode('-', $fecha_hasta);
                $anioDesde = explode('-', $fecha_desde);
                $fecha_desde = $anioHasta[0] . '-' . $anioDesde[1] . '-' . $anioDesde[2];
            }
            echo $fecha_desde;
            exit;
        }*/
        if ($mostrar_acumulado == 1) {
            $fecha_desde = substr($fecha_desde, 0, 4) . '-01-01';
        }
        if ($afecha != 2) {
            $anio1 = date('Y', strtotime($fecha_desde));
            $anio2 = date('Y', strtotime($fecha_hasta));
        } else {
            $anio1       = $fecha_desde;
            $anio2       = $fecha_hasta;
            $fecha_desde = $fecha_desde . '-01-01';
            $fecha_hasta = $fecha_hasta . '-12-31';
        }
        $anios = $anio2 - $anio1;
        $datos           = 0;
        $anio_inicio     = $anio1;
        $renta_acumulada = 0;
        for ($i = 0; $i <= $anios; $i++) {
            $fecha_valida1 = $anio_inicio . '-01-01';
            $fecha_valida2 = $anio_inicio . '-12-31';
            if ($fecha_desde >= $fecha_valida1) {
                $fecha_buscar_desde = $fecha_desde;
            } else {
                $fecha_buscar_desde = $fecha_valida1;
            }
            if ($fecha_hasta <= $fecha_valida2) {
                $fecha_buscar_hasta = $fecha_hasta;
            } else {
                $fecha_buscar_hasta = $fecha_valida2;
            }
            /*if (Auth::user()->id == '0921605895') {
                echo $fecha_buscar_desde;
                exit;
            }*/
            $total_buscar = EstadoResultado::utilidad_gravable_2($fecha_buscar_desde, $fecha_buscar_hasta, $id_empresa, null, $cierre);
            //dd($fecha_buscar_desde . ' -- ' . $fecha_buscar_hasta);
            //dd($total_buscar);
            $porcentaje_renta = Ct_Porcentaje_Renta::where('anio', $anio_inicio)->where('id_empresa', $id_empresa)->first();
            //dd($id_empresa);
            if (!is_null($porcentaje_renta)) {
                if ($porcentaje_renta->regimen_especial == 1) {
                    if ($id_empresa == '0993094072001') {
                        $toting = EstadoResultado::detalle_total_cuenta_general($fecha_buscar_desde, $fecha_buscar_hasta, '4.1', $afecha, 1, $cierre);
                    } else {
                        $toting = EstadoResultado::detalle_total_cuenta_general($fecha_buscar_desde, $fecha_buscar_hasta, '4.1.01', $afecha, 1, $cierre);
                    }
                    if (Auth::user()->id == '0922729587') {
                        //dd($toting);
                    }
                    $pt_renta        = (($toting * $porcentaje_renta->porcentaje) / 100);
                    $renta_acumulada = $renta_acumulada + round($pt_renta, 2);
                } elseif ($porcentaje_renta->regimen_especial == 3) {
                    $totalIngreso = 0;
                    if ($ingreso != []) {
                        foreach ($ingreso as $row) {
                            if ($row['nombre'] == 'INGRESOS') {
                                $totalIngreso = $row['saldo'];
                                break;
                            }
                        }
                    }
                    $porcentaje = 0;
                    $impuestoCalculado = 0;
                    $impuesto = 0;
                    if ($totalIngreso >= 0.00 && $totalIngreso <= 20000.00) {
                        $porcentaje = 0;
                        $impuesto = 60;
                        $impuestoCalculado = $impuesto;
                    } elseif ($totalIngreso >= 20000.01 && $totalIngreso <= 50000.00) {
                        $porcentaje = 1;
                        $impuesto = 60;
                        $diferencia = $totalIngreso - 20000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($totalIngreso >= 50000.01 && $totalIngreso <= 75000.00) {
                        $porcentaje = 1.25;
                        $impuesto = 360;
                        $diferencia = $totalIngreso - 50000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($totalIngreso >= 75000.01 && $totalIngreso <= 100000.00) {
                        $porcentaje = 1.50;
                        $impuesto = 672.50;
                        $diferencia = $totalIngreso - 70000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($totalIngreso >= 100000.01 && $totalIngreso <= 200000.00) {
                        $porcentaje = 1.75;
                        $impuesto = 1047.50;
                        $diferencia = $totalIngreso - 100000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($totalIngreso >= 200000.01 && $totalIngreso <= 300000.00) {
                        $porcentaje = 2;
                        $impuesto = 2797.52;
                        $diferencia = $totalIngreso - 200000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    }
                    $renta_acumulada = $impuestoCalculado;
                    
                } else {
                    if ($total_buscar > 0) {
                        $pt_renta        = (($total_buscar * $porcentaje_renta->porcentaje) / 100);
                        $renta_acumulada = $renta_acumulada + round($pt_renta, 2);
                    }
                }
            }
            $anio_inicio++;
        }
        /*$utilidad_gravable = EstadoResultado::utilidad_gravable($desde, $hasta, $afecha);
        $anio              = date('Y', strtotime($hasta));
        $porcentaje = Ct_Porcentaje_Impuesto_Renta::where('anio', '<=', $anio)->orderBy('anio', 'Desc')->first();
        $v1         = 0;
        if (!is_null($porcentaje)) {
        $v1 = $utilidad_gravable * ($porcentaje->porcentaje / 100);
        }
        $total = round($v1, 2);*/
        /*if (Auth::user()->id == '0922729587') {
        dd($renta_acumulada);
        }*/
        return $renta_acumulada;
    }
}
