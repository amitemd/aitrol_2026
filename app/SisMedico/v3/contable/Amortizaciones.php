<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use Sis_medico\AmortizacionPrestamo;
use Sis_medico\Empresa;

class Amortizaciones
{
    public static function resumen($idEmpresa)
    {
        $hoy = Carbon::today();
        $empresa = Empresa::find($idEmpresa);
        $nombreEmpresa = trim((string) ($empresa->nombrecomercial ?? ''))
            ?: trim((string) ($empresa->razonsocial ?? ''))
            ?: (string) $idEmpresa;
        $limiteProximos = $hoy->copy()->addDays(30);
        $prestamos = AmortizacionPrestamo::where('id_empresa', $idEmpresa)
            ->with(['detalles' => function ($query) {
                $query->orderBy('numero_cuota');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $resumen = [
            'prestamos_activos' => 0,
            'prestamos_cancelados' => 0,
            'capital_financiado' => 0.0,
            'capital_pagado' => 0.0,
            'capital_pendiente' => 0.0,
            'total_pagado' => 0.0,
            'total_pendiente' => 0.0,
            'cuotas_vencidas' => 0,
            'valor_vencido' => 0.0,
            'cuotas_proximos_30_dias' => 0,
            'valor_proximos_30_dias' => 0.0,
        ];
        $resultado = [];

        foreach ($prestamos as $prestamo) {
            $esActivo = $prestamo->estado === 'activo';
            $pagadas = $prestamo->detalles->where('estado', 'pagado');
            $pendientes = $prestamo->detalles->where('estado', 'pendiente');
            $vencidas = $pendientes->filter(function ($cuota) use ($hoy, $esActivo) {
                return $esActivo && Carbon::parse($cuota->fecha_pago)->lt($hoy);
            });
            $proximas = $pendientes->filter(function ($cuota) use ($hoy, $limiteProximos, $esActivo) {
                $fecha = Carbon::parse($cuota->fecha_pago);
                return $esActivo && $fecha->gte($hoy) && $fecha->lte($limiteProximos);
            });
            $proximaCuota = $pendientes->sortBy('fecha_pago')->first();

            $capitalPagado = (float) $pagadas->sum('capital');
            $capitalPendiente = (float) $pendientes->sum('capital');
            $totalPagado = (float) $pagadas->sum('total_cuota');
            $totalPendiente = (float) $pendientes->sum('total_cuota');
            $totalInteres = (float) $prestamo->detalles->sum('interes');
            $interesPagado = (float) $pagadas->sum('interes');
            $interesPendiente = (float) $pendientes->sum('interes');
            $totalSeguro = (float) $prestamo->detalles->sum('seguro');
            $seguroPagado = (float) $pagadas->sum('seguro');
            $seguroPendiente = (float) $pendientes->sum('seguro');
            $cuotasPagadas = $pagadas->count();
            $totalCuotas = $prestamo->detalles->count();
            $porcentaje = $totalCuotas > 0 ? ($cuotasPagadas / $totalCuotas) * 100 : 0;

            if ($prestamo->estado === 'cancelado') {
                $situacion = 'cancelado';
                $resumen['prestamos_cancelados']++;
            } elseif ($pendientes->count() === 0) {
                $situacion = 'completado';
                $resumen['prestamos_activos']++;
            } elseif ($vencidas->count() > 0) {
                $situacion = 'vencido';
                $resumen['prestamos_activos']++;
            } else {
                $situacion = 'al_dia';
                $resumen['prestamos_activos']++;
            }

            if ($esActivo) {
                $resumen['capital_financiado'] += (float) $prestamo->monto;
                $resumen['capital_pagado'] += $capitalPagado;
                $resumen['capital_pendiente'] += $capitalPendiente;
                $resumen['total_pagado'] += $totalPagado;
                $resumen['total_pendiente'] += $totalPendiente;
                $resumen['cuotas_vencidas'] += $vencidas->count();
                $resumen['valor_vencido'] += (float) $vencidas->sum('total_cuota');
                $resumen['cuotas_proximos_30_dias'] += $proximas->count();
                $resumen['valor_proximos_30_dias'] += (float) $proximas->sum('total_cuota');
            }

            $cuotas = [];
            foreach ($prestamo->detalles as $cuota) {
                $fechaPago = Carbon::parse($cuota->fecha_pago);
                $cuotas[] = [
                    'id' => $cuota->id,
                    'numero' => (int) $cuota->numero_cuota,
                    'fecha_pago' => $fechaPago->format('Y-m-d'),
                    'fecha_pago_real' => $cuota->fecha_pago_real ? Carbon::parse($cuota->fecha_pago_real)->format('Y-m-d') : null,
                    'saldo_inicial' => round((float) $cuota->saldo_inicial, 2),
                    'cuota_base' => round((float) $cuota->cuota_base, 2),
                    'interes' => round((float) $cuota->interes, 2),
                    'capital' => round((float) $cuota->capital, 2),
                    'seguro' => round((float) $cuota->seguro, 2),
                    'total_cuota' => round((float) $cuota->total_cuota, 2),
                    'saldo_final' => round((float) $cuota->saldo_final, 2),
                    'estado' => $cuota->estado,
                    'vencida' => $esActivo && $cuota->estado === 'pendiente' && $fechaPago->lt($hoy),
                    'id_asiento_pago' => $cuota->id_asiento_pago,
                ];
            }

            $resultado[] = [
                'id' => $prestamo->id,
                'institucion' => $prestamo->institucion,
                'numero_contrato' => $prestamo->numero_contrato,
                'fecha_desembolso' => Carbon::parse($prestamo->fecha_desembolso)->format('Y-m-d'),
                'fecha_primer_pago' => Carbon::parse($prestamo->fecha_primer_pago)->format('Y-m-d'),
                'monto' => round((float) $prestamo->monto, 2),
                'tasa_anual' => round((float) $prestamo->tasa_anual, 4),
                'plazo_meses' => (int) $prestamo->plazo_meses,
                'metodo' => $prestamo->metodo,
                'seguro_tipo' => $prestamo->seguro_tipo,
                'seguro_valor' => round((float) $prestamo->seguro_valor, 4),
                'estado' => $prestamo->estado,
                'situacion' => $situacion,
                'id_asiento_desembolso' => $prestamo->id_asiento_desembolso,
                'id_asiento_anulacion' => $prestamo->id_asiento_anulacion,
                'cuentas' => [
                    'banco' => $prestamo->cuenta_banco,
                    'deuda' => $prestamo->cuenta_deuda,
                    'interes' => $prestamo->cuenta_interes,
                    'seguro' => $prestamo->cuenta_seguro,
                ],
                'resumen' => [
                    'cuotas_pagadas' => $cuotasPagadas,
                    'cuotas_pendientes' => $pendientes->count(),
                    'cuotas_vencidas' => $vencidas->count(),
                    'porcentaje_pagado' => round($porcentaje, 2),
                    'capital_pagado' => round($capitalPagado, 2),
                    'capital_pendiente' => round($capitalPendiente, 2),
                    'interes_total' => round($totalInteres, 2),
                    'interes_pagado' => round($interesPagado, 2),
                    'interes_pendiente' => round($interesPendiente, 2),
                    'seguro_total' => round($totalSeguro, 2),
                    'seguro_pagado' => round($seguroPagado, 2),
                    'seguro_pendiente' => round($seguroPendiente, 2),
                    'total_pagado' => round($totalPagado, 2),
                    'total_pendiente' => round($totalPendiente, 2),
                    'valor_vencido' => round((float) $vencidas->sum('total_cuota'), 2),
                ],
                'proxima_cuota' => $proximaCuota ? [
                    'numero' => (int) $proximaCuota->numero_cuota,
                    'fecha' => Carbon::parse($proximaCuota->fecha_pago)->format('Y-m-d'),
                    'valor' => round((float) $proximaCuota->total_cuota, 2),
                ] : null,
                'cuotas' => $cuotas,
            ];
        }

        foreach ($resumen as $campo => $valor) {
            if (is_float($valor)) {
                $resumen[$campo] = round($valor, 2);
            }
        }

        $avance = $resumen['capital_financiado'] > 0
            ? ($resumen['capital_pagado'] / $resumen['capital_financiado']) * 100
            : 0;
        $resumen['porcentaje_capital_pagado'] = round($avance, 2);

        return [
            'empresa' => [
                'id_empresa' => $idEmpresa,
                'nombre' => $nombreEmpresa,
            ],
            'fecha_corte' => $hoy->format('Y-m-d'),
            'resumen' => $resumen,
            'prestamos' => $resultado,
        ];
    }
}
