<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sis_medico\Empresa;

class Prestamos
{
    public static function resumen($idEmpresa)
    {
        $fechaCorte = Carbon::today()->format('Y-m-d');
        $empresa = Empresa::find($idEmpresa);
        $nombreEmpresa = trim((string) ($empresa->nombrecomercial ?? ''))
            ?: trim((string) ($empresa->razonsocial ?? ''))
            ?: (string) $idEmpresa;

        $cuentas = DB::table('plan_cuentas_empresa as pe')
            ->leftJoin('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', $idEmpresa)
            ->where(function ($query) {
                $query->whereIn('pe.plan', ['2.01.02.01', '2.02.02.01'])
                    ->orWhere('pe.plan', 'like', '2.01.02.01.%')
                    ->orWhere('pe.plan', 'like', '2.02.02.01.%');
            })
            ->select(
                'pe.id',
                'pe.id_plan',
                'pe.plan as codigo',
                DB::raw("IFNULL(NULLIF(pe.nombre, ''), p.nombre) as nombre")
            )
            ->orderBy('pe.plan')
            ->get();

        if ($cuentas->isEmpty()) {
            return [
                'empresa' => [
                    'id_empresa' => (string) $idEmpresa,
                    'nombre' => $nombreEmpresa,
                ],
                'fecha_corte' => $fechaCorte,
                'mensaje' => 'No hay cuentas de préstamos configuradas para esta empresa.',
                'resumen' => [
                    'saldo_inicial' => 0.0,
                    'aumentos' => 0.0,
                    'reducciones' => 0.0,
                    'saldo_actual' => 0.0,
                    'movimientos' => 0,
                ],
                'bancos' => [],
                'cuentas' => [],
                'movimientos' => [],
            ];
        }

        $porId = [];
        $porIdPlan = [];
        $porCodigo = [];
        $claves = [];
        $resumenCuentas = [];

        foreach ($cuentas as $cuenta) {
            $codigo = trim((string) $cuenta->codigo);
            $id = trim((string) $cuenta->id);
            $idPlan = trim((string) $cuenta->id_plan);
            $banco = self::banco($cuenta->nombre, $codigo);
            $registro = [
                'codigo' => $codigo,
                'nombre' => trim((string) $cuenta->nombre) ?: 'Cuenta sin nombre',
                'plazo' => strpos($codigo, '2.01.02.01') === 0 ? 'Corto plazo' : 'Largo plazo',
                'banco_id' => $banco['id'],
                'banco' => $banco['nombre'],
            ];

            $porId[$id] = $codigo;
            $porIdPlan[$idPlan] = $codigo;
            $porCodigo[$codigo] = $codigo;
            $claves[$id] = true;
            $claves[$idPlan] = true;
            $claves[$codigo] = true;
            $resumenCuentas[$codigo] = array_merge($registro, [
                'resumen' => [
                    'saldo_inicial' => 0.0,
                    'aumentos' => 0.0,
                    'reducciones' => 0.0,
                    'saldo_actual' => 0.0,
                    'movimientos' => 0,
                    'ultimo_movimiento' => null,
                ],
                '_tiene_historial' => false,
            ]);
        }

        $registros = collect();
        if (!empty($claves)) {
            $registros = DB::table('ct_asientos_detalle as d')
                ->join('ct_asientos_cabecera as c', 'c.id', '=', 'd.id_asiento_cabecera')
                ->where('c.id_empresa', $idEmpresa)
                ->where('c.estado', 1)
                ->where('d.estado', 1)
                ->whereIn('d.id_plan_cuenta', array_keys($claves))
                ->where('c.fecha_asiento', '<=', $fechaCorte . ' 23:59:59')
                ->select(
                    'd.id',
                    'd.id_plan_cuenta',
                    'd.descripcion',
                    'd.debe',
                    'd.haber',
                    'c.id as asiento',
                    'c.fecha_asiento',
                    'c.fact_numero',
                    'c.observacion',
                    'c.modulo'
                )
                ->orderBy('c.fecha_asiento', 'desc')
                ->orderBy('c.id', 'desc')
                ->orderBy('d.id', 'desc')
                ->get();
        }

        $movimientos = [];
        foreach ($registros as $registro) {
            $clave = trim((string) $registro->id_plan_cuenta);
            if (strpos($clave, '.') !== false) {
                $codigo = $porCodigo[$clave] ?? null;
            } else {
                $codigo = $porId[$clave] ?? ($porIdPlan[$clave] ?? null);
            }
            if (!$codigo || !isset($resumenCuentas[$codigo])) {
                continue;
            }

            $fecha = substr((string) $registro->fecha_asiento, 0, 10);
            $debe = (float) $registro->debe;
            $haber = (float) $registro->haber;
            $efecto = $haber - $debe;
            $resumenCuentas[$codigo]['_tiene_historial'] = true;

            $texto = trim((string) $registro->observacion . ' ' . (string) $registro->descripcion);
            $tipo = self::tipoMovimiento($texto, $debe, $haber);
            if ($tipo['id'] === 'saldo_inicial') {
                $resumenCuentas[$codigo]['resumen']['saldo_inicial'] += $efecto;
            } else {
                $resumenCuentas[$codigo]['resumen']['aumentos'] += $haber;
                $resumenCuentas[$codigo]['resumen']['reducciones'] += $debe;
            }
            $resumenCuentas[$codigo]['resumen']['movimientos']++;
            if (!$resumenCuentas[$codigo]['resumen']['ultimo_movimiento']) {
                $resumenCuentas[$codigo]['resumen']['ultimo_movimiento'] = $fecha;
            }

            $movimientos[] = [
                'id' => (int) $registro->id,
                'asiento' => (int) $registro->asiento,
                'fecha' => $fecha,
                'banco_id' => $resumenCuentas[$codigo]['banco_id'],
                'banco' => $resumenCuentas[$codigo]['banco'],
                'codigo' => $codigo,
                'cuenta' => $resumenCuentas[$codigo]['nombre'],
                'observacion' => trim((string) $registro->observacion),
                'descripcion' => trim((string) $registro->descripcion),
                'factura' => trim((string) $registro->fact_numero),
                'debe' => round($debe, 2),
                'haber' => round($haber, 2),
                'efecto_saldo' => round($efecto, 2),
                'tipo' => $tipo['id'],
                'tipo_label' => $tipo['nombre'],
                'modulo' => $registro->modulo,
            ];
        }

        $resultadoCuentas = [];
        $bancos = [];
        $resumenGeneral = [
            'saldo_inicial' => 0.0,
            'aumentos' => 0.0,
            'reducciones' => 0.0,
            'saldo_actual' => 0.0,
            'movimientos' => 0,
        ];

        foreach ($resumenCuentas as $cuenta) {
            $cuenta['resumen']['saldo_actual'] = $cuenta['resumen']['saldo_inicial']
                + $cuenta['resumen']['aumentos']
                - $cuenta['resumen']['reducciones'];

            foreach (['saldo_inicial', 'aumentos', 'reducciones', 'saldo_actual'] as $campo) {
                $cuenta['resumen'][$campo] = round($cuenta['resumen'][$campo], 2);
            }

            if (!$cuenta['_tiene_historial'] && abs($cuenta['resumen']['saldo_actual']) < 0.005) {
                continue;
            }
            unset($cuenta['_tiene_historial']);
            $resultadoCuentas[] = $cuenta;
            $bancos[$cuenta['banco_id']] = ['id' => $cuenta['banco_id'], 'nombre' => $cuenta['banco']];

            foreach (['saldo_inicial', 'aumentos', 'reducciones', 'saldo_actual'] as $campo) {
                $resumenGeneral[$campo] += $cuenta['resumen'][$campo];
            }
            $resumenGeneral['movimientos'] += $cuenta['resumen']['movimientos'];
        }

        foreach (['saldo_inicial', 'aumentos', 'reducciones', 'saldo_actual'] as $campo) {
            $resumenGeneral[$campo] = round($resumenGeneral[$campo], 2);
        }
        usort($resultadoCuentas, function ($a, $b) {
            if ($a['banco'] === $b['banco']) {
                return strcmp($a['codigo'], $b['codigo']);
            }
            return strcmp($a['banco'], $b['banco']);
        });
        $bancos = array_values($bancos);
        usort($bancos, function ($a, $b) {
            return strcmp($a['nombre'], $b['nombre']);
        });

        return [
            'empresa' => [
                'id_empresa' => (string) $idEmpresa,
                'nombre' => $nombreEmpresa,
            ],
            'fecha_corte' => $fechaCorte,
            'resumen' => $resumenGeneral,
            'bancos' => $bancos,
            'cuentas' => $resultadoCuentas,
            'movimientos' => $movimientos,
        ];
    }

    private static function banco($nombreCuenta, $codigo)
    {
        $texto = strtoupper(Str::ascii((string) $nombreCuenta));
        $nombre = 'Otras obligaciones';
        if (strpos($texto, 'PACIFI') !== false) {
            $nombre = 'Banco del Pacífico';
        } elseif (strpos($texto, 'GUAYAQUIL') !== false) {
            $nombre = 'Banco de Guayaquil';
        } elseif (strpos($texto, 'BOLIVARIANO') !== false) {
            $nombre = 'Banco Bolivariano';
        } elseif (strpos($texto, 'PRODUBANCO') !== false) {
            $nombre = 'Produbanco';
        } elseif (strpos($texto, 'INTERNACIONAL') !== false) {
            $nombre = 'Banco Internacional';
        } elseif (strpos($texto, 'DINERS') !== false) {
            $nombre = 'Diners Club';
        }

        return [
            'id' => Str::slug($nombre, '_') ?: Str::slug($codigo, '_'),
            'nombre' => $nombre,
        ];
    }

    private static function tipoMovimiento($texto, $debe, $haber)
    {
        $normalizado = strtoupper(Str::ascii((string) $texto));
        if (strpos($normalizado, 'SALDO INICIAL') !== false || strpos($normalizado, 'ASIENTO INICIAL') !== false) {
            return ['id' => 'saldo_inicial', 'nombre' => 'Saldo inicial'];
        }
        foreach (['AJUST', 'COMPENS', 'RECLASIF', 'REGULARIZ', 'REVERS'] as $palabra) {
            if (strpos($normalizado, $palabra) !== false) {
                return ['id' => 'ajuste', 'nombre' => 'Ajuste contable'];
            }
        }
        if ($haber >= $debe) {
            return ['id' => 'aumento', 'nombre' => 'Aumento'];
        }
        return ['id' => 'reduccion', 'nombre' => 'Pago / reducción'];
    }
}


