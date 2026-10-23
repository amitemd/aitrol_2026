<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Nomina;
use Sis_medico\Plan_Cuentas;

class EstadoResultado
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
                ->whereRaw('character_length(id) < 7')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre', 'naturaleza', 'naturaleza_2')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre', 'naturaleza', 'naturaleza_2')
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
                $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    ->where('c.id_empresa', $id_empresa)
                //->where('c.estado', '<>', 0);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.naturaleza', '1');
                    $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.naturaleza', 'like', '0')
                    //->where('c.estado', '<>', 0)
                        ->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta 23:59:59"]);
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

                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta 23:59:59"]);
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
                $data['cuenta'] = $plan->id;
                $data['nombre'] = strtoupper($plan->nombre);
                $data['saldo']  = $saldo;
                $balance[]      = $data;
            }

            if ($) {
            }
            return $balance;
        }

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
            ->select('id', 'nombre', 'naturaleza', 'naturaleza_2')
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
        foreach ($plans as $plan) {
            $saldo = 0;
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    ->where('c.id_empresa', $id_empresa)
                    ->where('c.estado', '<>', 0);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                    $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.nombre', 'like', '%-)%')
                        ->where('c.estado', '<>', 0)
                        ->whereBetween('c.fecha_asiento', ["$desde 00:00:00", "$hasta 23:59:59"]);
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
                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta 23:59:59"]);
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

    public static function detalle_total_pyg($desde, $hasta, $afecha = null)
    {
        $totpyg = 0;
        $toting = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha);
        $totcos = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha);
        $totgas = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha);
        $totpyg = $toting - ($totcos + $totgas);
        return $totpyg;
    }

    public static function trabajadores($desde, $hasta, $id_empresa, $afecha = null)
    {
        $totpyg = 0;
        $toting = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha);
        $totcos = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha);
        $totgas = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha);

        $empleados = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta)->count();
        $totpyg    = 0;
        if ($empleados > 0) {
            $totpyg = round(($toting - ($totcos + $totgas)) * 0.15, 2);
            $total  = ($toting - ($totcos + $totgas));
            if ($total < 0) {
                $totpyg = 0;
            }
        }

        return $totpyg;
    }

    public static function utilidad_gravable($desde, $hasta, $id_empresa, $afecha = null)
    {
        $totpyg       = 0;
        $toting       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'I', $afecha);
        $totcos       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'C', $afecha);
        $totgas       = EstadoResultado::detalle_total_cuenta($desde, $hasta, 'G', $afecha);
        $totpyg       = $toting - ($totcos + $totgas);
        $empleados    = Ct_Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta)->count();
        $trabajadores = 0;
        if ($empleados > 0) {
            $trabajadores = round(($totpyg * 0.15), 2);
            if ($totpyg < 0) {
                $trabajadores = 0;
            }
        }
        $total = $totpyg - $trabajadores;
        //dd($total);
        return $total;
    }

    public static function impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, $afecha = null)
    {
        $anio1 = date('Y', strtotime($fecha_desde));
        $anio2 = date('Y', strtotime($fecha_hasta));
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

            $total_buscar     = EstadoResultado::utilidad_gravable($fecha_buscar_desde, $fecha_buscar_hasta, $id_empresa);
            $porcentaje_renta = Ct_Porcentaje_Renta::where('anio', $anio_inicio)->where('id_empresa', $id_empresa)->first();
            if ($total_buscar > 0) {
                if (!is_null($porcentaje_renta)) {
                    $pt_renta        = (($total_buscar * $porcentaje_renta->porcentaje) / 100);
                    $renta_acumulada = $renta_acumulada + round($pt_renta, 2);
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
        return $renta_acumulada;
    }

}
