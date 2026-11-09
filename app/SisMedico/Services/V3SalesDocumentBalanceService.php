<?php

namespace Sis_medico\Services;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Fuente unica del saldo por cobrar de una factura de venta.
 *
 * El saldo no se incrementa/decrementa a partir del valor previamente guardado:
 * siempre se reconstruye desde la factura y sus movimientos vigentes. De esta
 * forma una reejecucion, una anulacion o dos peticiones concurrentes no generan
 * saldos residuales.
 */
class V3SalesDocumentBalanceService
{
    const TOLERANCIA = 0.005;

    public function recalculate($companyId, $saleId)
    {
        return DB::transaction(function () use ($companyId, $saleId) {
            $snapshot = $this->snapshot($companyId, $saleId, true);

            DB::table('ct_ventas')
                ->where('id', $snapshot['id'])
                ->where('id_empresa', $companyId)
                ->update(array(
                    'valor_contable' => $snapshot['saldo_calculado'],
                    'estado_pago' => $snapshot['saldo_calculado'] <= self::TOLERANCIA ? 3 : 2,
                ));

            if (!empty($snapshot['documentos_nota_debito'])) {
                DB::table('ct_ventas')
                    ->where('id_empresa', $companyId)
                    ->whereIn('id', $snapshot['documentos_nota_debito'])
                    ->update(array('valor_contable' => 0, 'estado_pago' => 3));
            }

            return $snapshot;
        });
    }

    public function snapshot($companyId, $saleId, $lock = false)
    {
        $saleQuery = DB::table('ct_ventas')
            ->where('id', $saleId)
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0);

        if ($lock) {
            $saleQuery->lockForUpdate();
        }

        $sale = $saleQuery->first();
        if (is_null($sale)) {
            throw new Exception('No se encontro la factura activa dentro de la empresa.');
        }

        // Si el movimiento fue aplicado sobre la representacion de una N/D,
        // reconstruimos la factura raiz para que ambos documentos compartan un
        // solo saldo por cobrar y no aparezcan dos deudas por el mismo valor.
        if ($sale->tipo === 'N-D' && !is_null($sale->orden_venta)) {
            $parentSaleId = DB::table('ct_debito_clientes')
                ->where('id', $sale->orden_venta)
                ->where('id_empresa', $companyId)
                ->value('id_factura');
            if (!is_null($parentSaleId)) {
                $parentQuery = DB::table('ct_ventas')
                    ->where('id', $parentSaleId)
                    ->where('id_empresa', $companyId)
                    ->where('estado', '<>', 0);
                if ($lock) {
                    $parentQuery->lockForUpdate();
                }
                $parentSale = $parentQuery->first();
                if (!is_null($parentSale)) {
                    $sale = $parentSale;
                }
            }
        }

        // Una N/D aumenta la factura original. El registro VEN tipo N-D es solo
        // su representacion documental; no debe volver a sumar el mismo valor.
        $debits = DB::table('ct_debito_clientes')
            ->where('id_factura', $sale->id)
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->get(array('id', 'valor_contable'));

        $documentIds = array((int) $sale->id);
        $debitIds = array();
        $debitTotal = 0.0;
        foreach ($debits as $debit) {
            $debitIds[] = $debit->id;
            $debitTotal += (float) $debit->valor_contable;
        }

        if (count($debitIds) > 0) {
            $debitSales = DB::table('ct_ventas')
                ->where('id_empresa', $companyId)
                ->where('tipo', 'N-D')
                ->where('estado', '<>', 0)
                ->whereIn('orden_venta', $debitIds)
                ->pluck('id')
                ->all();
            $documentIds = array_merge($documentIds, array_map('intval', $debitSales));
        }

        $collections = (float) DB::table('ct_detalle_comprobante_ingreso as d')
            ->join('ct_comprobante_ingreso as c', 'c.id', '=', 'd.id_comprobante')
            ->where('c.id_empresa', $companyId)
            ->where('c.estado', '<>', 0)
            ->where('d.estado', '<>', 0)
            ->whereIn('d.id_factura', $documentIds)
            ->sum('d.total');

        $valueCrossings = (float) DB::table('ct_detalle_cruce_clientes as d')
            ->join('ct_cruce_valores_cliente as c', 'c.id', '=', 'd.id_comprobante')
            ->where('c.id_empresa', $companyId)
            ->where('c.estado', '<>', 0)
            ->where('d.estado', '<>', 0)
            ->whereIn('d.id_factura', $documentIds)
            ->sum('d.total');

        $accountCrossings = (float) DB::table('ct_cruce_cuentas_clientes')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->whereIn('id_factura', $documentIds)
            ->sum('total');

        $creditNotes = (float) DB::table('ct_nota_credito_clientes')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->whereIn('id_factura', $documentIds)
            ->sum('total_credito');

        $withholdingRow = DB::table('ct_cliente_retencion')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->whereIn('id_factura', $documentIds)
            ->select(DB::raw('COALESCE(SUM(COALESCE(valor_fuente, 0) + COALESCE(valor_iva, 0)), 0) AS total'))
            ->first();
        $withholdings = is_null($withholdingRow) ? 0.0 : (float) $withholdingRow->total;

        $charges = $this->money((float) $sale->total_final + $debitTotal);
        $credits = $this->money($collections + $valueCrossings + $accountCrossings + $creditNotes + $withholdings);
        $balance = self::calculateBalance($charges, $credits);

        return array(
            'id' => (int) $sale->id,
            'documento' => $sale->nro_comprobante,
            'total_factura' => $this->money($sale->total_final),
            'notas_debito' => $this->money($debitTotal),
            'cobros' => $this->money($collections),
            'cruces_valores' => $this->money($valueCrossings),
            'cruces_cuentas' => $this->money($accountCrossings),
            'notas_credito' => $this->money($creditNotes),
            'retenciones' => $this->money($withholdings),
            'saldo_guardado' => $this->money($sale->valor_contable),
            'saldo_calculado' => $balance['saldo'],
            'sobreaplicado' => $balance['sobreaplicado'],
            'documentos_nota_debito' => array_values(array_diff($documentIds, array((int) $sale->id))),
        );
    }

    public function auditCompany($companyId, $repair = false)
    {
        $ids = DB::table('ct_ventas')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->where('tipo', '<>', 'N-D')
            ->orderBy('id')
            ->pluck('id');

        $differences = array();
        foreach ($ids as $id) {
            $snapshot = $this->snapshot($companyId, $id, false);
            if (abs($snapshot['saldo_guardado'] - $snapshot['saldo_calculado']) > self::TOLERANCIA) {
                if ($repair) {
                    $snapshot = $this->recalculate($companyId, $id);
                }
                $differences[] = $snapshot;
            }
        }


        // Las filas tipo N-D son la representacion de la nota, no una segunda
        // cuenta por cobrar. El importe ya se incorpora en la factura raiz.
        $duplicatedDebitDocuments = DB::table('ct_ventas')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->where('tipo', 'N-D')
            ->where('valor_contable', '>', self::TOLERANCIA)
            ->get(array('id', 'nro_comprobante', 'valor_contable'));
        foreach ($duplicatedDebitDocuments as $document) {
            if ($repair) {
                DB::table('ct_ventas')->where('id', $document->id)->where('id_empresa', $companyId)
                    ->update(array('valor_contable' => 0, 'estado_pago' => 3));
            }
            $differences[] = array(
                'id' => (int) $document->id,
                'documento' => $document->nro_comprobante,
                'saldo_guardado' => $this->money($document->valor_contable),
                'saldo_calculado' => 0.0,
                'sobreaplicado' => 0.0,
            );
        }

        return $differences;
    }

    private function money($value)
    {
        return round((float) $value, 2);
    }

    public static function calculateBalance($charges, $credits)
    {
        $rawBalance = round((float) $charges - (float) $credits, 2);
        $balance = abs($rawBalance) <= self::TOLERANCIA ? 0.0 : max(0.0, $rawBalance);

        return array(
            'saldo' => round($balance, 2),
            'sobreaplicado' => $rawBalance < -self::TOLERANCIA ? round(abs($rawBalance), 2) : 0.0,
        );
    }
}
