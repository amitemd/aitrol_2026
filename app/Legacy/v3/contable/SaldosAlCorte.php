<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaldosAlCorte
{
    public static function porCobrar($empresa, $desde, $corte)
    {
        return self::calcular($empresa, $desde, $corte, 'ct_ventas', "f.tipo <> 'N-D'", [
            ['ct_detalle_comprobante_ingreso d JOIN ct_comprobante_ingreso h ON h.id = d.id_comprobante',
                'd.id_factura', 'd.total', 'h.fecha', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_cliente_retencion h', 'h.id_factura',
                'COALESCE(h.valor_fuente, 0) + COALESCE(h.valor_iva, 0)', 'h.fecha', 'h.estado <> 0', 1],
            ['ct_debito_clientes h', 'h.id_factura', 'h.valor_contable', 'h.fecha', 'h.estado <> 0', -1],
            ['ct_detalle_cruce_clientes d JOIN ct_cruce_valores_cliente h ON h.id = d.id_comprobante',
                'd.id_factura', 'd.total', 'h.fecha_pago', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_cruce_cuentas_clientes h', 'h.id_factura', 'h.total', 'h.fecha', 'h.estado <> 0', 1],
            ['ct_nota_credito_clientes h', 'h.id_factura', 'h.total_credito', 'h.fecha', 'h.estado <> 0', 1],
        ]);
    }

    public static function porPagar($empresa, $desde, $corte)
    {
        return self::calcular($empresa, $desde, $corte, 'ct_compras', 'f.estado > 0', [
            ['ct_detalle_comprobante_egreso d JOIN ct_comprobante_egreso h ON h.id = d.id_comprobante',
                'd.id_compra', 'd.abono', 'h.fecha_comprobante', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_retenciones h', 'h.id_compra',
                'COALESCE(h.valor_fuente, 0) + COALESCE(h.valor_iva, 0)', 'h.fecha', 'h.estado <> 0', 1],
            ['ct_detalle_cruce d JOIN ct_cruce_valores h ON h.id = d.id_comprobante',
                'd.id_factura', 'd.total', 'h.fecha_pago', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_credito_acreedores h', 'h.id_compra', 'h.valor_contable', 'h.fecha', 'h.estado <> 0', 1],
            ['ct_debito_bancario_detalle d JOIN ct_debito_bancario h ON h.id = d.id_debito',
                'd.id_compra', 'd.abono', 'h.fecha', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_detalle_comprobante_egreso_masivo d JOIN ct_comprobante_egreso_masivo h ON h.id = d.id_comprobante',
                'd.id_compra', 'd.abono', 'h.fecha_comprobante', 'h.estado <> 0 AND d.estado <> 0', 1],
            ['ct_detalle_debito_acreedores d JOIN ct_debito_acreedores h ON h.id = d.id_debito_acreedores',
                'd.id_factura', 'd.total', 'h.fechand', 'h.estado <> 0 AND d.estado <> 0', 1],
        ]);
    }

    private static function calcular($empresa, $desde, $corte, $tabla, $filtro, $fuentes)
    {
        $siguienteDia = Carbon::parse($corte)->addDay()->toDateString();
        $documentos = DB::select(
            "SELECT f.id, f.valor_contable FROM {$tabla} f
             WHERE f.id_empresa = ? AND f.estado <> 0 AND {$filtro}
               AND f.fecha >= ? AND f.fecha < ?",
            [$empresa, $desde, $siguienteDia]
        );

        $saldos = [];
        foreach ($documentos as $documento) {
            $saldos[(string) $documento->id] = (float) $documento->valor_contable;
        }

        // Se parte del saldo vigente y se revierten movimientos fechados después del corte.
        // Así también reaparecen facturas que hoy están pagadas pero estaban abiertas al corte.
        foreach ($fuentes as $fuente) {
            list($from, $idFactura, $importe, $fecha, $condicion, $signo) = $fuente;
            $sql = "SELECT {$idFactura} AS id_documento, SUM({$importe}) AS total
                    FROM {$from}
                    JOIN {$tabla} f ON f.id = {$idFactura}
                    WHERE f.id_empresa = ? AND f.estado <> 0 AND {$filtro}
                      AND f.fecha >= ? AND f.fecha < ?
                      AND {$fecha} >= ? AND {$condicion}
                    GROUP BY {$idFactura}";
            foreach (DB::select($sql, [$empresa, $desde, $siguienteDia, $siguienteDia]) as $movimiento) {
                $id = (string) $movimiento->id_documento;
                if (array_key_exists($id, $saldos)) {
                    $saldos[$id] += $signo * (float) $movimiento->total;
                }
            }
        }

        $total = 0.0;
        $cantidad = 0;
        foreach ($saldos as $saldo) {
            $saldoRedondeado = round($saldo, 2);
            if ($saldoRedondeado > 0) {
                $total += $saldoRedondeado;
                $cantidad++;
            }
        }

        return [
            'total' => round($total, 2),
            'documentos' => $cantidad,
            'tipo_saldo' => 'reconstruido_desde_saldo_actual',
            'fecha_desde' => $desde,
            'fecha_corte' => $corte,
        ];
    }
}
