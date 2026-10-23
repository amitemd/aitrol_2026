<?php

namespace Sis_medico;

use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Contabilidad extends Model
{
    public static function balanceComprobacion($r)
    {
        $hasta = str_replace('/', '-', $r->fecha_hasta);
        $desde = str_replace('/', '-', $r->fecha_desde);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $hasta_ = date('Y-m-d', strtotime($hasta . '+1 days'));


        $sql = "(select p.id_padre,p.id,pe.id_empresa as id_empresa,pe.plan,p.nombre,
        COALESCE(SUM(ad.debe), 0) AS debe,
        COALESCE(SUM(ad.haber), 0) AS haber,
        IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS deudor,
        IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS acreedor
        from plan_cuentas as p
        left join plan_cuentas_empresa as pe on p.id=pe.id_plan
        left join ct_asientos_detalle as ad on p.id=ad.id_plan_cuenta
        left join ct_asientos_cabecera as ac on ad.id_asiento_cabecera=ac.id
        where pe.id_empresa='" . session('id_empresa') . "'
        and ac.id_empresa='" . session('id_empresa') . "'
        AND SUBSTRING(pe.plan, 1, 1) IN ('1', '2', '3')
        and ac.fecha_asiento>='2010-01-01'
        and ac.fecha_asiento<'" . $hasta_ . "'
        and pe.plan is not null
        group by pe.plan
        order by pe.plan,ac.fecha_asiento)
        UNION ALL
        (select p.id_padre,p.id,pe.id_empresa as id_empresa,pe.plan,p.nombre,
        COALESCE(SUM(ad.debe), 0) AS debe,
        COALESCE(SUM(ad.haber), 0) AS haber,
        IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS deudor,
        IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS acreedor
        from plan_cuentas as p
        left join plan_cuentas_empresa as pe on p.id=pe.id_plan
        left join ct_asientos_detalle as ad on p.id=ad.id_plan_cuenta
        left join ct_asientos_cabecera as ac on ad.id_asiento_cabecera=ac.id
        where pe.id_empresa='" . session('id_empresa') . "'
        and ac.id_empresa='" . session('id_empresa') . "'
        AND SUBSTRING(pe.plan, 1, 1) IN ('4','5')
        and `ac`.`fecha_asiento` between '" . $desde . " 00:00:00' and '" . $hasta . " 23:59:59'
        and pe.plan is not null
        group by pe.plan
        order by pe.plan,ac.fecha_asiento);";
        return DB::select($sql);

        /*$app = Ct_Asientos_Detalle::select(
            'pe.plan',
            DB::raw('COALESCE(pe.nombre, pc.nombre) AS nombre'),
            DB::raw('SUM(ct_asientos_detalle.debe) AS debe'),
            DB::raw('SUM(ct_asientos_detalle.haber) AS haber'),
            DB::raw('IF(SUM(ct_asientos_detalle.debe) > SUM(ct_asientos_detalle.haber), SUM(ct_asientos_detalle.debe) - SUM(ct_asientos_detalle.haber), 0) AS deudor'),
            DB::raw('IF(SUM(ct_asientos_detalle.debe) < SUM(ct_asientos_detalle.haber), SUM(ct_asientos_detalle.haber) - SUM(ct_asientos_detalle.debe), 0) AS acreedor')
        )
            ->join('ct_asientos_cabecera AS ac', 'ct_asientos_detalle.id_asiento_cabecera', '=', 'ac.id')
            ->join('plan_cuentas_empresa AS pe', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pe.id_plan')
            ->leftJoin('plan_cuentas AS pc', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pc.id')
            ->where('ac.id_empresa', session('id_empresa'))
            ->whereIn(DB::raw("SUBSTRING(pe.plan, 1, 1)"), [1, 2, 3])
            ->where('ac.fecha_asiento', '>=', '2010-01-01')
            ->where('ac.fecha_asiento', '<', $hasta_)
            ->groupBy('pe.plan');

        $pp = Ct_Asientos_Detalle::select(
            'pe.plan',
            DB::raw('COALESCE(pe.nombre, pc.nombre) AS nombre'),
            DB::raw('SUM(ct_asientos_detalle.debe) AS debe'),
            DB::raw('SUM(ct_asientos_detalle.haber) AS haber'),
            DB::raw('IF(SUM(ct_asientos_detalle.debe) > SUM(ct_asientos_detalle.haber), SUM(ct_asientos_detalle.debe) - SUM(ct_asientos_detalle.haber), 0) AS deudor'),
            DB::raw('IF(SUM(ct_asientos_detalle.debe) < SUM(ct_asientos_detalle.haber), SUM(ct_asientos_detalle.haber) - SUM(ct_asientos_detalle.debe), 0) AS acreedor')
        )
            ->join('ct_asientos_cabecera AS ac', 'ct_asientos_detalle.id_asiento_cabecera', '=', 'ac.id')
            ->join('plan_cuentas_empresa AS pe', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pe.id_plan')
            ->leftJoin('plan_cuentas AS pc', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pc.id')
            ->where('ac.id_empresa', session('id_empresa'))
            ->whereIn(DB::raw("SUBSTRING(pe.plan, 1, 1)"), [4, 5])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');

        $result = $pp->union($app)->orderBy('plan', 'asc')->get();
        return $result;*/
    }
    public static function impuestoGenerado($utilidadGravable, $anio, $desde, $hasta, $ingreso)
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $hasta);
        $desde = str_replace('/', '-', $desde);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $porcentajeRenta = Ct_Porcentaje_Renta::where('id_empresa', $empresa)->where('anio', $anio)->first();
        $impuesto = 0;
        //0:rimpe popular; 1:rimpe emprendedor',2: Regimen General
        if (!empty($porcentajeRenta)) {
            if ($porcentajeRenta->regimen_especial == 0) {
                $deEmpresa = De_Empresa::where('id_empresa', session('id_empresa'))->first();
                if ($deEmpresa->tipo_rimpe == 0) {
                } elseif ($deEmpresa->tipo_rimpe == 1) {
                    $porcentaje = 0;
                    $impuestoCalculado = 0;
                    $impuesto = 0;
                    if ($ingreso < 0) {
                        $ingreso = $ingreso * -1;
                    }
                    if ($ingreso >= 0.00 && $ingreso <= 20000.00) {
                        $porcentaje = 0;
                        $impuesto = 60;
                        $impuestoCalculado = $impuesto;
                    } elseif ($ingreso >= 20000.01 && $ingreso <= 50000.00) {
                        $porcentaje = 1;
                        $impuesto = 60;
                        $diferencia = $ingreso - 20000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($ingreso >= 50000.01 && $ingreso <= 75000.00) {
                        $porcentaje = 1.25;
                        $impuesto = 360;
                        $diferencia = $ingreso - 50000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($ingreso >= 75000.01 && $ingreso <= 100000.00) {
                        $porcentaje = 1.50;
                        $impuesto = 672.50;
                        $diferencia = $ingreso - 70000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($ingreso >= 100000.01 && $ingreso <= 200000.00) {
                        $porcentaje = 1.75;
                        $impuesto = 1047.50;
                        $diferencia = $ingreso - 100000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    } elseif ($ingreso >= 200000.01 && $ingreso > 300000.00) {
                        $porcentaje = 2;
                        $impuesto = 2797.52;
                        $diferencia = $ingreso - 200000.01;
                        $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                    }
                    if ($utilidadGravable <= 0) {
                        $impuestoCalculado = $impuestoCalculado * -1;
                    }
                    return $impuestoCalculado;
                } elseif ($deEmpresa->tipo_rimpe == 2) {
                    $porcentajeRenta = Ct_Porcentaje_Renta::where('id_empresa', session('id_empresa'))->where('anio', $anio)->first();
                    if ($porcentajeRenta != '') {
                        $impuesto = ($utilidadGravable * $porcentajeRenta->porcentaje) / 100;
                    }
                }
            } elseif ($porcentajeRenta->regimen_especial == 1) {
                return $impuesto = ($utilidadGravable * $porcentajeRenta->porcentaje) / 100;
            } elseif ($porcentajeRenta->regimen_especial == 2) {
                $porcentaje = 0;
                $impuestoCalculado = 0;
                $impuesto = 0;
                if ($ingreso < 0) {
                    $ingreso = $ingreso * -1;
                }
                if ($ingreso >= 0.00 && $ingreso <= 20000.00) {
                    $porcentaje = 0;
                    $impuesto = 60;
                    $impuestoCalculado = $impuesto;
                } elseif ($ingreso >= 20000.01 && $ingreso <= 50000.00) {
                    $porcentaje = 1;
                    $impuesto = 60;
                    $diferencia = $ingreso - 20000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 50000.01 && $ingreso <= 75000.00) {
                    $porcentaje = 1.25;
                    $impuesto = 360;
                    $diferencia = $ingreso - 50000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 75000.01 && $ingreso <= 100000.00) {
                    $porcentaje = 1.50;
                    $impuesto = 672.50;
                    $diferencia = $ingreso - 70000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 100000.01 && $ingreso <= 200000.00) {
                    $porcentaje = 1.75;
                    $impuesto = 1047.50;
                    $diferencia = $ingreso - 100000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 200000.01 && $ingreso <= 300000.00) {
                    $porcentaje = 2;
                    $impuesto = 2797.52;
                    $diferencia = $ingreso - 200000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                }
                if ($utilidadGravable <= 0) {
                    $impuestoCalculado = $impuestoCalculado * -1;
                }
                return $impuestoCalculado;
            }
        } else {
            $deEmpresa = De_Empresa::where('id_empresa', session('id_empresa'))->first();
            if ($deEmpresa->tipo_rimpe == 0) {
            } elseif ($deEmpresa->tipo_rimpe == 1) {
                $porcentaje = 0;
                $impuestoCalculado = 0;
                $impuesto = 0;
                if ($ingreso < 0) {
                    $ingreso = $ingreso * -1;
                }
                if ($ingreso >= 0.00 && $ingreso <= 20000.00) {
                    $porcentaje = 0;
                    $impuesto = 60;
                    $impuestoCalculado = $impuesto;
                } elseif ($ingreso >= 20000.01 && $ingreso <= 50000.00) {
                    $porcentaje = 1;
                    $impuesto = 60;
                    $diferencia = $ingreso - 20000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 50000.01 && $ingreso <= 75000.00) {
                    $porcentaje = 1.25;
                    $impuesto = 360;
                    $diferencia = $ingreso - 50000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 75000.01 && $ingreso <= 100000.00) {
                    $porcentaje = 1.50;
                    $impuesto = 672.50;
                    $diferencia = $ingreso - 70000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 100000.01 && $ingreso <= 200000.00) {
                    $porcentaje = 1.75;
                    $impuesto = 1047.50;
                    $diferencia = $ingreso - 100000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso >= 200000.01 && $ingreso <= 300000.00) {
                    $porcentaje = 2;
                    $impuesto = 2797.52;
                    $diferencia = $ingreso - 200000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($ingreso > 300000.00) {
                    $porcentaje = 2;
                    $impuesto = 2797.52;
                    $diferencia = $ingreso - 200000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                }
                if ($utilidadGravable <= 0) {
                    $impuestoCalculado = $impuestoCalculado * -1;
                }
                return $impuestoCalculado;
            } elseif ($deEmpresa->tipo_rimpe == 2) {
                $porcentajeRenta = Ct_Porcentaje_Renta::where('id_empresa', session('id_empresa'))->where('anio', $anio)->first();
                if ($porcentajeRenta != '') {
                    $impuesto = ($utilidadGravable * $porcentajeRenta->porcentaje) / 100;
                }
            }
        }
        return $impuesto;
    }
    public static function gastosNoDeducibles($desde, $hasta)
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $hasta);
        $desde = str_replace('/', '-', $desde);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        return DB::table('ct_asientos_detalle as ad')
            ->join('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', 'ac.id')
            ->leftJoin('plan_cuentas_empresa as pe', function ($join) {
                $join->on('ad.id_plan_cuenta', 'pe.id_plan')
                    ->where('pe.id_empresa',  DB::raw('ac.id_empresa'));
            })
            ->leftJoin('plan_cuentas as pc', 'ad.id_plan_cuenta', 'pc.id')
            ->where('ac.id_empresa',  $empresa)
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('pe.plan', 'like', '5.%')
            ->where('ac.aparece_sri', 0)
            ->select(
                DB::raw('COALESCE(SUM(debe), 0) - COALESCE(SUM(haber), 0) as saldo')
            )
            ->first();
    }
    public static function cuerpoPerdidasGanancias($r)
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->fecha_hasta);
        $desde = str_replace('/', '-', $r->fecha_desde);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        /*$consulta = DB::table('ct_asientos_detalle as ad')
            //->select('pe.plan', 'pe.nombre', 'pc.id_padre', DB::raw('COALESCE(SUM(debe), 0) - COALESCE(SUM(haber), 0) as saldo'));
            ->select(
                'pe.plan',
                'pe.nombre',
                'pe.id_padre',
               
                DB::raw("COALESCE(SUM(ad.debe), 0) - COALESCE(SUM(ad.haber), 0) AS saldo")
            )
            ->leftJoin('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', '=', 'ac.id')
            ->join('plan_cuentas_empresa as pe', function ($join) use ($empresa) {
                $join->on('ad.id_plan_cuenta', '=', 'pe.id_plan')
                    ->where('pe.id_empresa', '=', $empresa);
            })
            ->join('plan_cuentas as p', 'pe.id_plan', '=', 'p.id')
            ->whereRaw("SUBSTRING(pe.plan, 1, 1) IN ('4','5')")
            ->where('pe.id_empresa', '=', $empresa)
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');*/
        /*
        select p.id_padre,p.id,pe.id_empresa as id_empresa,'2020' as anio,pe.plan,pe.nombre,
        COALESCE(SUM(ad.debe), 0) AS debe,
        COALESCE(SUM(ad.haber), 0) AS haber,
        COALESCE(SUM(ad.debe), 0) - COALESCE(SUM(ad.haber), 0) AS saldo,
        '::1' as ip,'::1' as ip,'0921605895' as id_usuario,'0921605895' as id_usuario
        from ct_asientos_detalle ad
        left join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa='0993075000001'
        join plan_cuentas p on pe.id_plan=p.id
        where SUBSTRING(pe.plan, 1, 1) IN ('4','5')
        and pe.id_empresa='0993075000001'
        and ac.fecha_asiento BETWEEN '2020-01-01 00:00:00' and '2020-12-31 23:59:59'
        group by pe.plan
        */
        $consulta = Ct_Asientos_Detalle::join('ct_asientos_cabecera as ac', 'ct_asientos_detalle.id_asiento_cabecera', '=', 'ac.id')
            ->leftJoin('plan_cuentas_empresa as pe', function ($join) use ($empresa) {
                $join->on('ct_asientos_detalle.id_plan_cuenta', '=', 'pe.id_plan')
                    ->where('pe.id_empresa', '=', $empresa);
            })
            ->leftJoin('plan_cuentas as pc', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pc.id')
            ->where('ac.id_empresa', '=', $empresa)
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where(function ($query) {
                $query->where('pe.plan', 'like', '4.%')
                    ->orWhere('pe.plan', 'like', '5.%');
            })
            ->groupBy('pe.plan', 'pe.nombre')
            ->select('pe.plan', 'pe.nombre', 'pc.id_padre', DB::raw('COALESCE(SUM(debe), 0) - COALESCE(SUM(haber), 0) as saldo'));
        $arrayTodo = [];
        for ($i = 0; $i < 7; $i++) {
            $arrayResultado[$i] = Contabilidad::traerPadresPerdidasGanancias($arrayResultado[$i - 1] ?? [], $consulta);
            $arrayTodo = array_merge($arrayTodo, $arrayResultado[$i]);
        }
        $arrayTodo = array_unique($arrayTodo);
        $p = Plan_Cuentas::whereIn('id', $arrayTodo)
            ->selectRaw('id as plan, nombre, id_padre, 0 as saldo')
            ->union($consulta)
            ->orderBy('plan')
            ->get();

        return $p;
    }
    public static function printRows($rows, $tipo = '', $desde = '', $hasta = '')
    {
        $html = '';
        $saldo = '';
        $totalSaldos = 0;
        if ($tipo != 'ingresos' && $tipo != 'costos' && $tipo != 'gastos') {
            foreach ($rows as $row) {
                $totalSaldos += $row->saldo_;
            }
        }
        foreach ($rows as $row) {
            $porcentaje = ($totalSaldos != 0) ? ($row->saldo_ / $totalSaldos) * 100 : 0;
            if ($row->saldo_ != 0) {
                if ($tipo == 'activos') {
                    $saldo = $row->saldo_ < 0 ? '(' . number_format(($row->saldo_ * -1), 2) . ')' : number_format($row->saldo_, 2);
                } elseif ($tipo == 'pasivos') {
                    if ($row->saldo_ < 0)
                        $saldo = number_format(($row->saldo_ * -1), 2);
                    else
                        $saldo = number_format($row->saldo_, 2);
                } else if ($tipo == 'ingresos') {
                    $row->saldo_ = $row->saldo_ * -1;
                    $saldo = $row->saldo_ < 0 ? '(' . number_format(($row->saldo_ * -1), 2) . ')' : number_format($row->saldo_, 2);
                } elseif ($tipo == 'patrimonio') {
                    $saldo = number_format(($row->saldo_ * -1), 2);
                } else {
                    $saldo = number_format(($row->saldo_), 2);
                }
                $sangria = '';
                $numPlan = explode('.', $row->plan);
                $class = '';
                if ($tipo == 'activos' || $tipo == 'pasivos' || $tipo == 'patrimonios') {
                    if (count($numPlan) == 1) {
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 2) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 3) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 4) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 5)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                } else {
                    if (count($numPlan) == 1) {
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 2) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 3) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $class = 'font-weight: bold;';
                    } elseif (count($numPlan) == 4) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    } elseif (count($numPlan) == 5) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    } elseif (count($numPlan) == 6) {
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $url =  $sangria . $row->plan;
                if (count($numPlan) > 3) {
                    $url = '<a href="' . url('/') . '/contable/contabilidad/libromayor?fecha=' . $desde . '&fecha_hasta=' . $hasta . '&cuenta=' . $row->plan . '" target="_new">' . $sangria . $row->plan . '</a>';
                }
                $html .= '<tr>';
                $html .= '<td style="' . $class . '">' . $url . '</td>';
                $html .= '<td style="font-size:9px;' . $class . '">' . $row->nombre . '</td>';
                $html .= '<td style="text-align:right">' . $saldo . '</td>';
                if ($tipo != 'ingresos' && $tipo != 'costos' && $tipo != 'gastos') {
                    $html .= '<td style="text-align:right">' . number_format($porcentaje, 2) . '%</td>';
                }
                $html .= '</tr>';
            }
        }
        return $html;
    }
    public static function cuerpoBalance($r)
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->fecha_hasta);
        $hasta = date('Y-m-d', strtotime($hasta . '+1 days'));

        $hasta_ = str_replace('/', '-', $r->fecha_hasta);
        $hasta_ = date('Y-m-d', strtotime($hasta_)) . ' 23:59:59';

        $desde = str_replace('/', '-', $r->fecha_desde);
        $desde = date('Y-m-d', strtotime($desde)) . ' 00:00:00';
        $sql = "call sp_new_balance (?, ?, ?, ?, ?, ?, ?)";
        DB::select($sql, [$empresa, $desde, $hasta, $hasta_, substr(date('Y-m-d', strtotime(str_replace('/', '-', $r->fecha_hasta))), 0, 4), $_SERVER["REMOTE_ADDR"], Auth::user()->id]);
        $balance = DB::table('new_db_balance')
            ->select('plan', 'nombre', 'id_padre', 'saldo')
            ->where('anio', substr(date('Y-m-d', strtotime(str_replace('/', '-', $r->fecha_hasta))), 0, 4))
            ->where('id_empresa', $empresa)
            ->where('saldo', '!=', 0);
        $arrayTodo = [];
        for ($i = 0; $i < 7; $i++) {
            $arrayResultado[$i] = Contabilidad::traerPadres($arrayResultado[$i - 1] ?? []);
            $arrayTodo = array_merge($arrayTodo, $arrayResultado[$i]);
        }
        $arrayTodo = array_unique($arrayTodo);
        $p = Plan_Cuentas::whereIn('id', $arrayTodo)
            ->selectRaw('id as plan, nombre, id_padre, 0 as saldo')
            ->union($balance)
            ->orderBy('plan')
            ->get();
        return $p;
    }
    public static function traerPadres($padre = [])
    {
        if (empty($padre)) {
            $balance = DB::table('new_db_balance')
                ->groupBy('id_padre')
                ->get(['id_padre']);
            return $balance->pluck('id_padre')->toArray();
        } else {
            $p = Plan_Cuentas::whereIn('id', $padre)
                ->groupBy('id_padre')
                ->get(['id_padre']);
            return $p->pluck('id_padre')->toArray();
        }
    }
    public static function traerPadresPerdidasGanancias($padre = [], $consulta)
    {
        if (empty($padre)) {
            $balance = $consulta
                ->groupBy('id_padre')
                ->get(['id_padre']);
            return $balance->pluck('id_padre')->toArray();
        } else {
            $p = Plan_Cuentas::whereIn('id', $padre)
                ->groupBy('id_padre')
                ->get(['id_padre']);
            return $p->pluck('id_padre')->toArray();
        }
    }
}
