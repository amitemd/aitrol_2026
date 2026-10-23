<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FlujoCaja
{
    public static function resumen($empresa, $desde, $hasta)
    {
        $fechaCorte = min($hasta, Carbon::today()->toDateString());
        $hastaExclusivo = Carbon::parse($fechaCorte)->addDay()->toDateString();
        $cuentaCajaGeneral = '1.01.01.01.01';
        $porCobrar = SaldosAlCorte::porCobrar($empresa, $desde, $fechaCorte);
        $porPagar = SaldosAlCorte::porPagar($empresa, $desde, $fechaCorte);

        $ingresos = DB::table('ct_comprobante_ingreso as i')
            ->where('i.id_empresa', $empresa)
            ->where('i.estado', 1)
            ->where('i.fecha', '>=', $desde)
            ->where('i.fecha', '<', $hastaExclusivo)
            ->whereRaw("NOT (
                COALESCE(i.tipo, 0) = 2
                AND EXISTS (
                    SELECT 1 FROM ct_comprobante_ingreso original
                    WHERE original.id_asiento_cabecera = i.id_asiento_cabecera
                      AND original.id_empresa = i.id_empresa
                      AND original.id <> i.id
                      AND original.tipo <> 2
                      AND original.estado = 1
                )
            )")
            ->selectRaw('COALESCE(SUM(i.total_ingreso), 0) AS total, COUNT(*) AS documentos')
            ->first();

        $ingresosVarios = DB::table('ct_comprobante_ingreso_varios')
            ->where('id_empresa', $empresa)
            ->where('estado', 1)
            ->where('fecha', '>=', $desde)
            ->where('fecha', '<', $hastaExclusivo)
            ->selectRaw('COALESCE(SUM(total_ingreso), 0) AS total, COUNT(*) AS documentos')
            ->first();

        $egresos = DB::table('ct_comprobante_egreso as e')
            ->where('e.id_empresa', $empresa)
            ->where('e.estado', 1)
            ->whereNotNull('e.id_proveedor')
            ->where('e.valor_pago', '>', 0)
            ->where('e.fecha_comprobante', '>=', $desde)
            ->where('e.fecha_comprobante', '<', $hastaExclusivo)
            ->whereRaw("NOT EXISTS (
                SELECT 1 FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = e.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->whereRaw("NOT EXISTS (
                SELECT 1 FROM ct_debito_bancario b
                WHERE b.id = e.id_referencia
                  AND b.id_empresa = e.id_empresa
                  AND b.estado = 1
            )")
            ->selectRaw('COALESCE(SUM(e.valor_pago), 0) AS total, COUNT(*) AS documentos')
            ->first();

        // Se usa la cabecera una sola vez por comprobante masivo: es la salida registrada.
        $masivos = DB::table('ct_comprobante_egreso_masivo')
            ->where('id_empresa', $empresa)
            ->where('estado', 1)
            ->where('valor_pago', '>', 0)
            ->where('fecha_comprobante', '>=', $desde)
            ->where('fecha_comprobante', '<', $hastaExclusivo)
            ->whereRaw("NOT EXISTS (
                SELECT 1 FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = ct_comprobante_egreso_masivo.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->selectRaw('COALESCE(SUM(valor_pago), 0) AS total, COUNT(*) AS documentos')
            ->first();

        $debitos = DB::table('ct_debito_bancario')
            ->where('id_empresa', $empresa)
            ->where('estado', 1)
            ->whereNotNull('id_acreedor')
            ->where('valor', '>', 0)
            ->where('fecha', '>=', $desde)
            ->where('fecha', '<', $hastaExclusivo)
            ->whereRaw("NOT EXISTS (
                SELECT 1 FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = ct_debito_bancario.id_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->selectRaw('COALESCE(SUM(valor), 0) AS total, COUNT(*) AS documentos')
            ->first();

        $egresosVarios = DB::table('ct_comprobante_egreso_varios')
            ->where('id_empresa', $empresa)
            ->where('estado', 1)
            ->where('valor', '>', 0)
            ->where('fecha_comprobante', '>=', $desde)
            ->where('fecha_comprobante', '<', $hastaExclusivo)
            ->whereRaw("NOT EXISTS (
                SELECT 1 FROM ct_caja_banco cb_caja_general
                WHERE cb_caja_general.id = ct_comprobante_egreso_varios.id_caja_banco
                  AND TRIM(cb_caja_general.cuenta_mayor) = ?
            )", [$cuentaCajaGeneral])
            ->selectRaw('COALESCE(SUM(valor), 0) AS total, COUNT(*) AS documentos')
            ->first();

        $cobrosTotal = (float) $ingresos->total + (float) $ingresosVarios->total;
        $pagosProveedores = (float) $egresos->total + (float) $masivos->total + (float) $debitos->total;
        $pagosTotal = $pagosProveedores + (float) $egresosVarios->total;

        return [
            'id_empresa' => $empresa,
            'periodo' => [
                'fecha_desde' => $desde,
                'fecha_hasta' => $fechaCorte,
                'fecha_hasta_solicitada' => $hasta,
            ],
            'fecha_corte' => $fechaCorte,
            'fecha_consulta' => Carbon::now()->toDateTimeString(),
            'cuentas_por_cobrar' => $porCobrar,
            'cuentas_por_pagar' => $porPagar,
            'cobros_realizados' => [
                'total' => round($cobrosTotal, 2),
                'comprobantes' => (int) $ingresos->documentos + (int) $ingresosVarios->documentos,
                'comprobantes_ingreso' => round((float) $ingresos->total, 2),
                'ingresos_varios' => round((float) $ingresosVarios->total, 2),
            ],
            'pagos_realizados' => [
                'total' => round($pagosTotal, 2),
                'comprobantes' => (int) $egresos->documentos + (int) $masivos->documentos
                    + (int) $debitos->documentos + (int) $egresosVarios->documentos,
                'proveedores' => round($pagosProveedores, 2),
                'egresos_varios' => round((float) $egresosVarios->total, 2),
                'egresos' => round((float) $egresos->total, 2),
                'egresos_masivos' => round((float) $masivos->total, 2),
                'debitos_bancarios' => round((float) $debitos->total, 2),
            ],
            'flujo_registrado' => [
                'entradas' => round($cobrosTotal, 2),
                'salidas' => round($pagosTotal, 2),
                'neto' => round($cobrosTotal - $pagosTotal, 2),
                'conciliado_banco' => false,
            ],
            'prestamos_por_pagar' => ['total' => null, 'estado' => 'sin_fuente'],
        ];
    }
}
