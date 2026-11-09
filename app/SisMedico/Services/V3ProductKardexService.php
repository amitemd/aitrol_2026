<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Support\RequestSchema as Schema;

class V3ProductKardexService
{
    public function report($companyId, $productReference, array $filters = array())
    {
        $product = DB::table('ct_productos')->where('id_empresa', $companyId)
            ->where(function ($query) use ($productReference) {
                $query->where('id', $productReference)->orWhere('codigo', trim((string) $productReference));
            })->first();
        if (!$product) throw new RuntimeException('El producto no pertenece a la empresa activa.');

        $from = $this->date($filters, 'desde', date('Y-01-01'));
        $to = $this->date($filters, 'hasta', date('Y-m-d'));
        if ($from > $to) throw new RuntimeException('La fecha desde no puede ser posterior a la fecha hasta.');
        $warehouseName = trim(isset($filters['bodega']) ? (string) $filters['bodega'] : '');
        $serial = trim(isset($filters['serie']) ? (string) $filters['serie'] : '');
        $lot = trim(isset($filters['lote']) ? (string) $filters['lote'] : '');

        $accounting = $this->accountingLedger($companyId, $product, $from, $to, $warehouseName, $lot);
        $hospital = $this->hospitalLedger($companyId, $product, $from, $to, $warehouseName, $serial, $lot);
        $positions = $this->positions($companyId, $product, $warehouseName, $serial, $lot);

        $warehouseNames = array();
        foreach (array_merge($accounting['all_warehouses'], $hospital['all_warehouses'], $positions['all_warehouses']) as $name) {
            $name = trim((string) $name);
            if ($name !== '') $warehouseNames[$name] = $name;
        }
        natcasesort($warehouseNames);

        return array(
            'product' => array('id' => $product->id, 'code' => $product->codigo, 'name' => $product->nombre),
            'filters' => array('desde' => $from, 'hasta' => $to, 'bodega' => $warehouseName, 'serie' => $serial, 'lote' => $lot),
            'warehouses' => array_values($warehouseNames),
            'summary' => $accounting['summary'],
            'movements' => $accounting['rows'],
            'warehouse_positions' => $positions['accounting'],
            'traceability' => $hospital['rows'],
            'series_positions' => $positions['series'],
        );
    }

    private function accountingLedger($companyId, $product, $from, $to, $warehouseName, $lotFilter)
    {
        $rows = DB::table('ct_inv_kardex as k')
            ->leftJoin('ct_inv_movimiento as m', 'm.id', '=', 'k.id_movimiento')
            ->leftJoin('ct_bodegas as b', 'b.id', '=', 'k.id_bodega')
            ->where('k.id_producto', $product->id)->where('k.estado', 1)
            ->orderBy('k.fecha')->orderBy('k.id')
            ->select('k.id', 'k.fecha', 'k.id_bodega', 'k.id_movimiento', 'k.id_transaccion', 'k.cantidad', 'k.precio', 'k.serie',
                'm.tipo as source_type', 'm.id_referencia as source_reference', 'm.pedido as source_number', 'b.nombre as warehouse')->get();

        $purchaseIds = array(); $saleIds = array();
        foreach ($rows as $row) {
            $type = strtoupper(trim((string) $row->source_type));
            if ($type === 'COMP') $purchaseIds[] = $row->source_reference;
            if ($type === 'VENTA') $saleIds[] = $row->source_reference;
        }
        $activePurchases = DB::table('ct_compras')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($purchaseIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();
        $activeSales = DB::table('ct_ventas')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($saleIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();

        $purchaseLots = array();
        if ($purchaseIds) {
            $details = DB::table('ct_detalle_compra')->whereIn('id_ct_compras', array_unique($purchaseIds))
                ->where('codigo', $product->codigo)->where('estado', 1)
                ->select('id_ct_compras', 'bodega', 'lote', 'fecha_exp')->get();
            foreach ($details as $detail) {
                $key = $detail->id_ct_compras.'|'.(int) $detail->bodega;
                if (!isset($purchaseLots[$key])) $purchaseLots[$key] = array('lots' => array(), 'expires' => array());
                if (trim((string) $detail->lote) !== '') $purchaseLots[$key]['lots'][(string) $detail->lote] = (string) $detail->lote;
                if (trim((string) $detail->fecha_exp) !== '') $purchaseLots[$key]['expires'][(string) $detail->fecha_exp] = (string) $detail->fecha_exp;
            }
        }

        $adjustmentDetails = collect();
        if (Schema::hasTable('v3_inventory_adjustment_details') && !$rows->isEmpty()) {
            $adjustmentDetails = DB::table('v3_inventory_adjustment_details')
                ->where('company_id', $companyId)->where('product_id', $product->id)
                ->whereIn('historical_kardex_id', $rows->pluck('id')->all())
                ->get()->keyBy('historical_kardex_id');
        }
        $adjustmentEntries = collect();
        if (Schema::hasTable('v3_accounting_posting_sources')) {
            $movementIds = $rows->filter(function ($row) {
                return in_array(strtoupper(trim((string) $row->source_type)), array('AJUSTE+', 'AJUSTE-'), true);
            })->pluck('id_movimiento')->filter()->unique()->values()->all();
            if ($movementIds) {
                $adjustmentEntries = DB::table('v3_accounting_posting_sources')
                    ->where('id_empresa', $companyId)->where('modulo', 'INVENTARIO_V3')
                    ->where('tipo_origen', 'AJUSTE_INVENTARIO')->whereIn('id_origen', $movementIds)
                    ->where('estado', 'POSTED')->whereNotNull('id_asiento')->pluck('id_asiento', 'id_origen');
            }
        }

        $opening = 0; $entries = 0; $outputs = 0; $balance = 0; $result = array(); $warehouses = array();
        foreach ($rows as $row) {
            $type = strtoupper(trim((string) $row->source_type));
            if ($type === 'COMP' && !$activePurchases->has($row->source_reference)) continue;
            if ($type === 'VENTA' && !$activeSales->has($row->source_reference)) continue;
            $warehouse = trim((string) $row->warehouse) ?: 'Bodega no disponible';
            $warehouses[] = $warehouse;
            if ($warehouseName !== '' && strcasecmp($warehouseName, $warehouse) !== 0) continue;
            $lotData = isset($purchaseLots[$row->source_reference.'|'.(int) $row->id_bodega])
                ? $purchaseLots[$row->source_reference.'|'.(int) $row->id_bodega] : array('lots' => array(), 'expires' => array());
            $adjustmentDetail = $adjustmentDetails->get($row->id);
            if ($adjustmentDetail) {
                $lotData = array(
                    'lots' => trim((string) $adjustmentDetail->lot) !== ''
                        ? array((string) $adjustmentDetail->lot) : array(),
                    'expires' => $adjustmentDetail->expiration_date
                        ? array((string) $adjustmentDetail->expiration_date) : array(),
                );
            }
            $lots = implode(', ', array_values($lotData['lots']));
            if ($lotFilter !== '' && stripos($lots, $lotFilter) === false) continue;
            $signed = (int) $row->id_transaccion === 3 ? -(float) $row->cantidad : (float) $row->cantidad;
            $date = substr((string) $row->fecha, 0, 10);
            if ($date < $from) { $opening += $signed; continue; }
            if ($date > $to) continue;
            $balance += $signed;
            if ($signed >= 0) $entries += $signed; else $outputs += abs($signed);
            $isAdjustment = in_array($type, array('AJUSTE+', 'AJUSTE-'), true);
            $reference = $isAdjustment && trim((string) $row->source_number) !== ''
                ? trim((string) $row->source_number) : (string) $row->source_reference;
            $accountingEntry = $adjustmentEntries->get((string) $row->id_movimiento);
            if ($accountingEntry) $reference .= ' · Asiento #'.$accountingEntry;
            $result[] = array(
                'date' => $date, 'type' => $this->movementLabel($type, (int) $row->id_transaccion),
                'reference' => $reference, 'accounting_entry' => $accountingEntry ? (int) $accountingEntry : null,
                'warehouse' => $warehouse, 'serial' => (string) $row->serie,
                'lot' => $lots, 'expires' => implode(', ', array_values($lotData['expires'])),
                'entry' => $signed >= 0 ? round($signed, 2) : 0,
                'output' => $signed < 0 ? round(abs($signed), 2) : 0,
                'unit_cost' => round((float) $row->precio, 2), 'balance' => round($opening + $balance, 2),
            );
        }
        return array(
            'rows' => $result, 'all_warehouses' => $warehouses,
            'summary' => array('opening' => round($opening, 2), 'entries' => round($entries, 2),
                'outputs' => round($outputs, 2), 'ending' => round($opening + $entries - $outputs, 2)),
        );
    }

    private function hospitalLedger($companyId, $product, $from, $to, $warehouseName, $serialFilter, $lotFilter)
    {
        $ids = DB::table('producto')->where('codigo', $product->codigo)->pluck('id')->all();
        $mapped = DB::table('ct_productos_insumos')->where('id_producto', $product->id)->pluck('id_insumo')->all();
        $ids = array_values(array_unique(array_merge($ids, $mapped)));
        if (!$ids) return array('rows' => array(), 'all_warehouses' => array());

        $rows = DB::table('inv_kardex as k')
            ->leftJoin('inv_det_movimientos as d', 'd.id', '=', 'k.id_inv_det_movimientos')
            ->leftJoin('bodega as b', 'b.id', '=', 'k.id_bodega')
            ->leftJoin('inv_cab_movimientos as c', 'c.id', '=', 'd.id_inv_cab_movimientos')
            ->leftJoin('inv_documentos_bodegas as db', 'db.id', '=', 'k.id_documento_bodega')
            ->where('k.id_empresa', $companyId)->where('k.estado', 1)->whereIn('k.id_producto', $ids)
            ->orderBy('k.fecha')->orderBy('k.id')
            ->select('k.id', 'k.fecha', 'k.tipo', 'k.descripcion', 'k.referencia', 'k.cantidad', 'k.valor_unitario',
                'd.serie', 'd.lote', 'd.fecha_vence', 'b.nombre as warehouse',
                'db.documento as document', 'c.numero_documento')->get();

        $balances = array(); $result = array(); $warehouses = array();
        foreach ($rows as $row) {
            $warehouse = trim((string) $row->warehouse) ?: 'Bodega no disponible';
            $warehouses[] = $warehouse;
            if ($warehouseName !== '' && strcasecmp($warehouseName, $warehouse) !== 0) continue;
            if ($serialFilter !== '' && stripos((string) $row->serie, $serialFilter) === false) continue;
            if ($lotFilter !== '' && stripos((string) $row->lote, $lotFilter) === false) continue;
            // El traslado de salida (T/TRA) resta en la bodega de origen. El
            // ingreso correlativo usa documento INT con tipo I.
            $incoming = in_array(strtoupper(trim((string) $row->tipo)), array('I', 'R', 'G'), true);
            $signed = $incoming ? (float) $row->cantidad : -(float) $row->cantidad;
            $key = $warehouse.'|'.(string) $row->serie.'|'.(string) $row->lote;
            if (!isset($balances[$key])) $balances[$key] = 0;
            $date = substr((string) $row->fecha, 0, 10);
            if ($date < $from) { $balances[$key] += $signed; continue; }
            if ($date > $to) continue;
            $balances[$key] += $signed;
            $result[] = array(
                'date' => $date, 'type' => $incoming ? 'Ingreso' : 'Egreso',
                'document' => trim((string) ($row->document ?? '')) ?: trim((string) ($row->descripcion ?? '')),
                'reference' => trim((string) ($row->numero_documento ?? '')) ?: trim((string) ($row->referencia ?? '')),
                'warehouse' => $warehouse, 'serial' => (string) $row->serie, 'lot' => (string) $row->lote,
                'expires' => $row->fecha_vence, 'entry' => $incoming ? round(abs($signed), 2) : 0,
                'output' => !$incoming ? round(abs($signed), 2) : 0,
                'unit_cost' => round((float) $row->valor_unitario, 2), 'balance' => round($balances[$key], 2),
            );
        }
        return array('rows' => $result, 'all_warehouses' => $warehouses);
    }

    private function positions($companyId, $product, $warehouseName, $serialFilter, $lotFilter)
    {
        $accountingRows = DB::table('ct_inventario as i')->join('ct_bodegas as b', 'b.id', '=', 'i.bodega_id')
            ->where('i.id_empresa', $companyId)->where('i.producto_id', $product->id)
            ->orderBy('b.nombre')->select('b.nombre as warehouse', 'i.existencia')->get();
        $accounting = array(); $warehouses = array();
        foreach ($accountingRows as $row) {
            $warehouses[] = $row->warehouse;
            if ($warehouseName !== '' && strcasecmp($warehouseName, $row->warehouse) !== 0) continue;
            $accounting[] = array('warehouse' => $row->warehouse, 'stock' => round((float) $row->existencia, 2));
        }

        $ids = DB::table('producto')->where('codigo', $product->codigo)->pluck('id')->all();
        $mapped = DB::table('ct_productos_insumos')->where('id_producto', $product->id)->pluck('id_insumo')->all();
        $ids = array_values(array_unique(array_merge($ids, $mapped)));
        $series = array();
        if ($ids) {
            $seriesRows = DB::table('inv_inventario_serie as s')->leftJoin('bodega as b', 'b.id', '=', 's.id_bodega')
                ->where('s.id_empresa', $companyId)->where('s.estado', 1)->whereIn('s.id_producto', $ids)
                ->orderBy('b.nombre')->orderBy('s.lote')->orderBy('s.serie')
                ->select('b.nombre as warehouse', 's.serie', 's.lote', 's.fecha_vence', 's.existencia', 's.existencia_uso')->get();
            foreach ($seriesRows as $row) {
                $warehouse = trim((string) $row->warehouse) ?: 'Bodega no disponible';
                $warehouses[] = $warehouse;
                if ($warehouseName !== '' && strcasecmp($warehouseName, $warehouse) !== 0) continue;
                if ($serialFilter !== '' && stripos((string) $row->serie, $serialFilter) === false) continue;
                if ($lotFilter !== '' && stripos((string) $row->lote, $lotFilter) === false) continue;
                $series[] = array('warehouse' => $warehouse, 'serial' => (string) $row->serie,
                    'lot' => (string) $row->lote, 'expires' => $row->fecha_vence,
                    'stock' => round((float) $row->existencia, 2), 'uses' => round((float) $row->existencia_uso, 2));
            }
        }
        return array('accounting' => $accounting, 'series' => $series, 'all_warehouses' => $warehouses);
    }

    private function date(array $filters, $key, $default)
    {
        $value = isset($filters[$key]) ? trim((string) $filters[$key]) : '';
        if ($value === '') return $default;
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new RuntimeException('La fecha seleccionada no es válida.');
        return $value;
    }

    private function movementLabel($type, $transaction)
    {
        if ($type === 'COMP') return 'Compra';
        if ($type === 'VENTA') return 'Venta';
        if ($type === 'AJUSTE+') return 'Ajuste de entrada';
        if ($type === 'AJUSTE-') return 'Ajuste de egreso';
        if ($type !== '') return $type;
        if ($transaction === 1) return 'Saldo inicial';
        return $transaction === 3 ? 'Egreso / merma' : 'Ingreso / ajuste';
    }
}
