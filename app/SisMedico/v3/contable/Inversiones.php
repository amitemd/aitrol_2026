<?php

namespace Sis_medico\v3\contable;

use Illuminate\Support\Facades\DB;

class Inversiones
{
    public static function resumen($idEmpresa, $fechaDesde, $fechaHasta)
    {
        $grupos = self::grupos();
        $codigos = [];
        foreach ($grupos as $grupo) {
            $codigos = array_merge($codigos, $grupo['codigos']);
        }
        $codigos = array_values(array_unique($codigos));

        $cuentas = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', $idEmpresa)
            ->whereIn('pe.plan', $codigos)
            ->select(
                'pe.id_plan',
                'pe.plan as codigo',
                DB::raw("IFNULL(NULLIF(pe.nombre, ''), p.nombre) as nombre")
            )
            ->orderBy('pe.plan')
            ->get();

        $cuentasPorCodigo = [];
        $idsPlan = [];
        foreach ($cuentas as $cuenta) {
            $cuentasPorCodigo[$cuenta->codigo] = $cuenta;
            $idsPlan[] = $cuenta->id_plan;
        }

        $registros = collect();
        if (!empty($idsPlan)) {
            $registros = DB::table('ct_asientos_detalle as d')
                ->join('ct_asientos_cabecera as c', 'c.id', '=', 'd.id_asiento_cabecera')
                ->join('plan_cuentas_empresa as pe', function ($join) use ($idEmpresa) {
                    $join->on('pe.id_plan', '=', 'd.id_plan_cuenta')
                        ->where('pe.id_empresa', '=', $idEmpresa);
                })
                ->where('c.id_empresa', $idEmpresa)
                ->whereIn('d.id_plan_cuenta', array_values(array_unique($idsPlan)))
                ->where('c.fecha_asiento', '<=', $fechaHasta . ' 23:59:59')
                ->select(
                    'd.id',
                    'd.debe',
                    'd.haber',
                    'c.fecha_asiento',
                    'pe.plan as codigo'
                )
                ->distinct()
                ->orderBy('c.fecha_asiento')
                ->orderBy('d.id_asiento_cabecera')
                ->orderBy('d.id')
                ->get();
        }

        $resultado = [];
        foreach ($grupos as $grupo) {
            $factor = $grupo['naturaleza'] === 'acreedora' ? -1 : 1;
            $cuentasGrupo = [];
            $saldoAnteriorGrupo = 0.0;
            $entradasGrupo = 0.0;
            $salidasGrupo = 0.0;

            foreach ($grupo['codigos'] as $codigo) {
                $cuenta = isset($cuentasPorCodigo[$codigo]) ? $cuentasPorCodigo[$codigo] : null;
                $registrosCuenta = $registros->filter(function ($registro) use ($codigo) {
                    return $registro->codigo === $codigo;
                });

                $saldoAnteriorContable = 0.0;
                $entradas = 0.0;
                $salidas = 0.0;
                foreach ($registrosCuenta as $registro) {
                    $fecha = substr((string) $registro->fecha_asiento, 0, 10);
                    $debe = (float) $registro->debe;
                    $haber = (float) $registro->haber;
                    if ($fecha < $fechaDesde) {
                        $saldoAnteriorContable += $debe - $haber;
                        continue;
                    }

                    $entradas += $grupo['naturaleza'] === 'acreedora' ? $haber : $debe;
                    $salidas += $grupo['naturaleza'] === 'acreedora' ? $debe : $haber;
                }

                $saldoAnterior = $saldoAnteriorContable * $factor;
                $saldoFinal = $saldoAnterior + $entradas - $salidas;
                $saldoAnteriorGrupo += $saldoAnterior;
                $entradasGrupo += $entradas;
                $salidasGrupo += $salidas;

                $cuentasGrupo[] = [
                    'codigo' => $codigo,
                    'nombre' => $cuenta ? $cuenta->nombre : 'Cuenta no configurada',
                    'configurada' => (bool) $cuenta,
                    'resumen' => [
                        'saldo_anterior' => round($saldoAnterior, 2),
                        'entradas' => round($entradas, 2),
                        'salidas' => round($salidas, 2),
                        'saldo_final' => round($saldoFinal, 2),
                    ],
                ];
            }

            $resultado[] = [
                'id' => $grupo['id'],
                'nombre' => $grupo['nombre'],
                'descripcion' => $grupo['descripcion'],
                'naturaleza' => $grupo['naturaleza'],
                'principal' => $grupo['principal'],
                'cuentas' => $cuentasGrupo,
                'resumen' => [
                    'saldo_anterior' => round($saldoAnteriorGrupo, 2),
                    'entradas' => round($entradasGrupo, 2),
                    'salidas' => round($salidasGrupo, 2),
                    'saldo_final' => round($saldoAnteriorGrupo + $entradasGrupo - $salidasGrupo, 2),
                ],
            ];
        }

        return [
            'empresa' => [
                'id_empresa' => $idEmpresa,
                'nombre' => 'GASTROCLINICA S.A.',
            ],
            'periodo' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ],
            'grupos' => $resultado,
        ];
    }

    private static function grupos()
    {
        return [
            [
                'id' => 'cdp_bancarios',
                'nombre' => 'CDP bancarios',
                'descripcion' => 'Certificados de depósito a plazo en instituciones financieras.',
                'naturaleza' => 'deudora',
                'principal' => true,
                'codigos' => ['1.01.01.04.01', '1.01.01.04.02', '1.01.01.04.03'],
            ],
            [
                'id' => 'acciones_participaciones',
                'nombre' => 'Acciones y participaciones',
                'descripcion' => 'Participaciones de capital registradas en empresas relacionadas.',
                'naturaleza' => 'deudora',
                'principal' => true,
                'codigos' => self::rango('1.02.06.03.', 1, 14),
            ],
            [
                'id' => 'derechos_capital',
                'nombre' => 'Derechos de capital',
                'descripcion' => 'Otros derechos representativos de capital en sociedades.',
                'naturaleza' => 'deudora',
                'principal' => true,
                'codigos' => ['1.02.06.04.01'],
            ],
            [
                'id' => 'provision_valoracion',
                'nombre' => 'PVI - valoración de inversiones',
                'descripcion' => 'Provisiones y ajustes registrados sobre el valor de las inversiones.',
                'naturaleza' => 'acreedora',
                'principal' => false,
                'codigos' => self::rango('1.02.06.05.', 1, 6),
            ],
            [
                'id' => 'vtps',
                'nombre' => 'VTPS',
                'descripcion' => 'Valores y títulos registrados como inversión.',
                'naturaleza' => 'deudora',
                'principal' => true,
                'codigos' => ['1.02.06.06.01'],
            ],
            [
                'id' => 'intereses_inversiones',
                'nombre' => 'Intereses de inversiones',
                'descripcion' => 'Ingresos generados por inversiones financieras.',
                'naturaleza' => 'acreedora',
                'principal' => false,
                'codigos' => ['4.1.02.01.01'],
            ],
            [
                'id' => 'aportes_capital_pagar',
                'nombre' => 'Aportes de capital por pagar',
                'descripcion' => 'Obligaciones pendientes relacionadas con aportes de capital.',
                'naturaleza' => 'acreedora',
                'principal' => false,
                'codigos' => self::rango('2.02.03.02.', 1, 4),
            ],
            [
                'id' => 'dividendos_pagar',
                'nombre' => 'Dividendos por pagar',
                'descripcion' => 'Dividendos reconocidos y pendientes de pago.',
                'naturaleza' => 'acreedora',
                'principal' => false,
                'codigos' => ['2.01.04.06.01'],
            ],
        ];
    }

    private static function rango($prefijo, $desde, $hasta)
    {
        $resultado = [];
        for ($numero = $desde; $numero <= $hasta; $numero++) {
            $resultado[] = $prefijo . str_pad($numero, 2, '0', STR_PAD_LEFT);
        }
        return $resultado;
    }
}


