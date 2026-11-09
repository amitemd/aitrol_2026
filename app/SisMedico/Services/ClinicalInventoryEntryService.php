<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\InvKardex;

/**
 * Replica entradas contables de productos relacionados hacia el inventario
 * hospitalario por serie/lote. El descargo de Enfermeria consume este ultimo.
 */
class ClinicalInventoryEntryService
{
    private $warehouseService;

    public function __construct(CompanyPrimaryInventoryWarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function syncPurchase($purchaseId, $companyId, $userId, $ip)
    {
        $purchase = DB::table('ct_compras')->where('id', $purchaseId)
            ->where('id_empresa', $companyId)->where('estado', '<>', 0)
            ->lockForUpdate()->first();
        if (!$purchase) {
            throw new RuntimeException('No existe la compra activa que debe ingresar al inventario hospitalario.');
        }

        $details = DB::table('ct_detalle_compra as d')
            ->join('ct_productos as p', function ($join) use ($companyId) {
                $join->on('p.codigo', '=', 'd.codigo')->where('p.id_empresa', '=', $companyId);
            })
            ->where('d.id_ct_compras', $purchaseId)->where('d.estado', 1)
            ->where('p.tipo_producto', 2)
            ->select('d.*', 'p.id as accounting_product_id', 'p.nombre as accounting_product_name')
            ->orderBy('d.id')->lockForUpdate()->get();

        $mapped = array();
        $ambiguous = array();
        foreach ($details as $detail) {
            $relations = DB::table('ct_productos_insumos as relation')
                ->join('producto as legacy_product', 'legacy_product.id', '=', 'relation.id_insumo')
                ->where('relation.id_producto', $detail->accounting_product_id)
                ->where('legacy_product.estado', 1)
                ->get(array('legacy_product.id', 'legacy_product.codigo', 'legacy_product.nombre', 'legacy_product.usos'));
            if ($relations->isEmpty()) continue;
            if ($relations->count() !== 1) {
                $ambiguous[] = $detail->codigo;
                continue;
            }

            $quantity = (float) $detail->cantidad;
            if ($quantity <= 0 || abs($quantity - round($quantity)) > 0.0001) {
                throw new RuntimeException('La cantidad del producto hospitalario '.$detail->codigo.' debe ser un entero mayor a cero.');
            }
            $lot = trim((string) $detail->lote);
            $expiration = trim((string) $detail->fecha_exp);
            if ($lot === '' || $expiration === '') {
                throw new RuntimeException(
                    'El producto '.$detail->codigo.' esta relacionado al inventario hospitalario. '
                    .'Ingrese lote y fecha de vencimiento en la linea de compra.'
                );
            }

            $legacyWarehouseId = $this->legacyWarehouseId(
                $companyId,
                (int) $detail->bodega,
                $userId,
                $ip
            );
            $detail->legacy_product = $relations->first();
            $detail->legacy_warehouse_id = $legacyWarehouseId;
            $detail->clinical_quantity = (int) round($quantity);
            $mapped[] = $detail;
        }

        if ($ambiguous) {
            throw new RuntimeException(
                'Los productos '.implode(', ', array_values(array_unique($ambiguous))).' tienen mas de una relacion '
                .'con insumos hospitalarios. Deje una sola relacion antes de procesar la compra.'
            );
        }

        if (!$mapped) {
            return array(
                'movidos' => 0,
                'ya_procesado' => false,
                'relaciones_ambiguas' => array_values(array_unique($ambiguous)),
            );
        }

        $groups = array();
        foreach ($mapped as $detail) {
            $groups[$detail->legacy_warehouse_id][] = $detail;
        }

        $moved = 0;
        $alreadyProcessed = true;
        foreach ($groups as $legacyWarehouseId => $groupDetails) {
            $result = $this->recordPurchaseWarehouse(
                $purchase,
                $groupDetails,
                (int) $legacyWarehouseId,
                $companyId,
                $userId,
                $ip
            );
            $moved += $result['movidos'];
            $alreadyProcessed = $alreadyProcessed && $result['ya_procesado'];
        }

        return array(
            'movidos' => $moved,
            'ya_procesado' => $alreadyProcessed,
            'relaciones_ambiguas' => array_values(array_unique($ambiguous)),
        );
    }

    /**
     * Replica un ingreso o egreso creado en Inventario V3 al inventario hospitalario.
     * Los productos sin relacion clinica permanecen unicamente en V3.
     */
    public function syncV3Adjustment(
        $movementId,
        $number,
        $direction,
        $date,
        $observation,
        $companyId,
        $accountingWarehouseId,
        array $lines,
        $userId,
        $ip
    ) {
        $direction = strtolower(trim((string) $direction));
        if (!in_array($direction, array('ingreso', 'egreso'), true)) {
            throw new RuntimeException('El tipo de ajuste hospitalario no es valido.');
        }
        $mapped = array();
        foreach ($lines as $line) {
            $relations = DB::table('ct_productos_insumos as relation')
                ->join('producto as legacy_product', 'legacy_product.id', '=', 'relation.id_insumo')
                ->where('relation.id_producto', $line['product']->id)
                ->where('legacy_product.estado', 1)
                ->get(array('legacy_product.id', 'legacy_product.codigo', 'legacy_product.nombre', 'legacy_product.usos'));
            if ($relations->isEmpty()) continue;
            if ($relations->count() !== 1) {
                throw new RuntimeException(
                    'El producto '.$line['product']->codigo.' tiene mas de una relacion con insumos hospitalarios.'
                );
            }
            if (abs((float) $line['quantity'] - round((float) $line['quantity'])) > 0.0001) {
                throw new RuntimeException(
                    'La cantidad de '.$line['product']->nombre.' debe ser entera para moverla por lote en el inventario hospitalario.'
                );
            }
            $line['legacy_product'] = $relations->first();
            $line['clinical_quantity'] = (int) round((float) $line['quantity']);
            $mapped[] = $line;
        }
        if (!$mapped) return array('movidos' => 0, 'ya_procesado' => false);

        $warehouseId = $this->legacyWarehouseId(
            $companyId,
            (int) $accountingWarehouseId,
            $userId,
            $ip
        );
        if ($direction === 'egreso') {
            return $this->recordV3AdjustmentExitWarehouse(
                $movementId, $number, $date, $observation, $mapped,
                $warehouseId, $companyId, $userId, $ip
            );
        }
        return $this->recordV3AdjustmentWarehouse(
            $movementId,
            $number,
            $date,
            $observation,
            $mapped,
            $warehouseId,
            $companyId,
            $userId,
            $ip
        );
    }

    public function syncV3AdjustmentEntry(
        $movementId,
        $number,
        $date,
        $observation,
        $companyId,
        $accountingWarehouseId,
        array $lines,
        $userId,
        $ip
    ) {
        return $this->syncV3Adjustment(
            $movementId, $number, 'ingreso', $date, $observation, $companyId,
            $accountingWarehouseId, $lines, $userId, $ip
        );
    }

    /**
     * Refleja en el saldo V3 una entrada creada desde Ingreso/Egreso Vario.
     * Solo se mueven relaciones univocas con productos fisicos de la empresa.
     */
    public function syncClinicalAdjustmentToAccounting($headerId, $companyId, $userId, $ip)
    {
        $header = DB::table('inv_cab_movimientos')->where('id', $headerId)
            ->where('id_empresa', $companyId)->where('estado', '<>', 0)
            ->lockForUpdate()->first();
        if (!$header) throw new RuntimeException('No existe el ajuste hospitalario que debe sincronizarse.');

        $documentType = DB::table('inv_documentos_bodegas as document')
            ->join('inv_tipo_movimiento as movement_type', 'movement_type.id', '=', 'document.id_inv_tipo_movimiento')
            ->where('document.id', $header->id_documento_bodega)->value('movement_type.tipo');
        if (!in_array($documentType, array('I', 'R', 'G'), true)) {
            return array('movidos' => 0, 'ya_procesado' => false);
        }

        $existing = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('tipo', 'AJUSTE+')->where('id_movimiento', $headerId)
            ->lockForUpdate()->get();
        if (!$existing->isEmpty()) {
            return array('movidos' => $existing->count(), 'ya_procesado' => true);
        }

        $details = DB::table('inv_det_movimientos')->where('id_inv_cab_movimientos', $headerId)
            ->where('estado', 1)->orderBy('id')->lockForUpdate()->get();
        $groups = array();
        foreach ($details as $detail) {
            $products = DB::table('ct_productos_insumos as relation')
                ->join('ct_productos as product', 'product.id', '=', 'relation.id_producto')
                ->where('relation.id_insumo', $detail->id_producto)
                ->where('product.id_empresa', $companyId)->where('product.tipo_producto', 2)
                ->where('product.estado_tabla', 1)
                ->get(array('product.id', 'product.codigo', 'product.nombre'));
            if ($products->isEmpty()) continue;
            if ($products->count() !== 1) {
                throw new RuntimeException('El insumo del ajuste tiene mas de un producto contable relacionado en la empresa.');
            }

            $accountingWarehouses = DB::table('union_bodega')->where('id_empresa', $companyId)
                ->where('id_bodega_compra', $header->id_bodega_destino ?: $header->id_bodega_origen)
                ->whereNotNull('id_bodega_cont')->lockForUpdate()->get();
            if ($accountingWarehouses->count() !== 1) {
                throw new RuntimeException('La bodega del ajuste no tiene una relacion contable unica para la empresa.');
            }

            $product = $products->first();
            $warehouseId = (int) $accountingWarehouses->first()->id_bodega_cont;
            $key = $product->id.'|'.$warehouseId;
            if (!isset($groups[$key])) {
                $groups[$key] = array(
                    'product' => $product,
                    'warehouse_id' => $warehouseId,
                    'quantity' => 0,
                    'value' => 0,
                    'lot' => $detail->lote,
                    'expiration' => $detail->fecha_vence,
                );
            }
            $groups[$key]['quantity'] += (float) $detail->cantidad;
            $groups[$key]['value'] += (float) $detail->subtotal;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($groups as $group) {
            if ($group['quantity'] <= 0) continue;
            $inventoryRows = DB::table('ct_inventario')->where('id_empresa', $companyId)
                ->where('producto_id', $group['product']->id)
                ->where('bodega_id', $group['warehouse_id'])->lockForUpdate()->get();
            if ($inventoryRows->count() > 1) {
                throw new RuntimeException('Existen saldos contables duplicados para el producto ajustado.');
            }
            $inventory = $inventoryRows->first();
            $previous = DB::table('ct_kardex')->where('id_empresa', $companyId)
                ->where('producto_id', $group['product']->id)
                ->where('bodega_id', $group['warehouse_id'])
                ->orderBy('id', 'desc')->lockForUpdate()->first();
            $oldQuantity = $inventory ? (float) $inventory->existencia : ($previous ? (float) $previous->saldo_cantidad : 0);
            $oldUnitCost = $previous ? (float) $previous->saldo_valor_unitario : 0;
            $newQuantity = round($oldQuantity + $group['quantity'], 4);
            $newValue = round(($oldQuantity * $oldUnitCost) + $group['value'], 2);
            $average = $newQuantity > 0 ? round($newValue / $newQuantity, 2) : 0;
            $unitCost = round($group['value'] / $group['quantity'], 2);

            DB::table('ct_kardex')->insert(array(
                'fecha' => $header->fecha,
                'id_movimiento' => $headerId,
                'movimiento' => 1,
                'tipo' => 'AJUSTE+',
                'numero' => $header->numero_documento,
                'producto_id' => $group['product']->id,
                'cantidad' => $group['quantity'],
                'valor_unitario' => $unitCost,
                'total' => round($group['value'], 2),
                'saldo_cantidad' => $newQuantity,
                'saldo_valor_unitario' => $average,
                'saldo_total' => $newValue,
                'bodega_id' => $group['warehouse_id'],
                'id_empresa' => $companyId,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));

            $audit = array(
                'existencia' => $newQuantity,
                'id_usuariomod' => $userId,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            );
            if ($inventory) {
                DB::table('ct_inventario')->where('id', $inventory->id)->update($audit);
            } else {
                DB::table('ct_inventario')->insert(array_merge($audit, array(
                    'producto_id' => $group['product']->id,
                    'bodega_id' => $group['warehouse_id'],
                    'id_empresa' => $companyId,
                    'id_usuariocrea' => $userId,
                    'ip_creacion' => $ip,
                    'created_at' => $now,
                )));
            }
        }

        return array('movidos' => count($groups), 'ya_procesado' => false);
    }

    private function legacyWarehouseId($companyId, $accountingWarehouseId, $userId, $ip)
    {
        $mapping = DB::table('union_bodega')->where('id_empresa', $companyId)
            ->where('id_bodega_cont', $accountingWarehouseId)
            ->whereNotNull('id_bodega_compra')->lockForUpdate()->get();
        if ($mapping->count() === 1) return (int) $mapping->first()->id_bodega_compra;
        if ($mapping->count() > 1) {
            throw new RuntimeException('La bodega de la compra tiene mas de una relacion con inventario hospitalario.');
        }

        $principal = $this->warehouseService->principal($companyId);
        if ((int) $principal->accounting_id !== (int) $accountingWarehouseId) {
            throw new RuntimeException(
                'El producto hospitalario se compro en una bodega sin relacion de inventario. '
                .'Seleccione la bodega principal o configure su correspondencia.'
            );
        }

        return (int) $this->warehouseService
            ->resolveOrProvision($companyId, $userId, $ip, $principal->hospital_id)
            ->inventory_id;
    }

    private function recordPurchaseWarehouse($purchase, array $details, $warehouseId, $companyId, $userId, $ip)
    {
        $reference = 'COMPRA-V3:'.$purchase->id.':'.$warehouseId;
        $existing = DB::table('inv_cab_movimientos')->where('id_empresa', $companyId)
            ->where('data_humanlabs', $reference)->where('estado', '<>', 0)
            ->lockForUpdate()->first();
        if ($existing) {
            $saved = DB::table('inv_det_movimientos')->where('id_inv_cab_movimientos', $existing->id)
                ->where('estado', 1)->orderBy('id_detalle_origen')->lockForUpdate()->get();
            $expected = collect($details)->keyBy(function ($detail) {
                return (string) $detail->id;
            });
            $matches = $saved->count() === count($details) && $saved->every(function ($row) use ($expected) {
                $detail = $expected->get((string) $row->id_detalle_origen);
                return $detail
                    && (int) $row->id_producto === (int) $detail->legacy_product->id
                    && (int) $row->cantidad === (int) $detail->clinical_quantity
                    && trim((string) $row->lote) === trim((string) $detail->lote)
                    && (string) $row->fecha_vence === (string) $detail->fecha_exp;
            });
            if (!$matches) {
                throw new RuntimeException('La compra ya tiene un ingreso hospitalario incompleto; requiere revision.');
            }
            return array('movidos' => $saved->count(), 'ya_procesado' => true);
        }

        $document = $this->ensureEntryDocument($companyId, $userId, $ip);
        $sequence = $this->nextEntrySequence($document->id, $warehouseId, $companyId, $userId, $ip);
        $orderId = $this->purchaseOrder($purchase, $document->id, $warehouseId, $companyId, $userId, $ip);
        $now = date('Y-m-d H:i:s');

        $headerId = DB::table('inv_cab_movimientos')->insertGetId(array(
            'id_documento_bodega' => $document->id,
            'id_transaccion_bodega' => $sequence['transaction_id'],
            'id_bodega_origen' => $warehouseId,
            'id_bodega_destino' => $warehouseId,
            'numero_documento' => str_pad($sequence['number'], 9, '0', STR_PAD_LEFT),
            'num_doc_cont' => substr((string) $purchase->numero, 0, 10),
            'observacion' => 'INGRESO AUTOMATICO POR COMPRA #'.$purchase->id,
            'fecha' => $purchase->fecha,
            'subtotal' => $purchase->subtotal_12,
            'subtotal_0' => $purchase->subtotal_0,
            'iva' => $purchase->iva_total,
            'total' => $purchase->total_final,
            'estado' => 1,
            'id_pedido' => $orderId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'data_humanlabs' => $reference,
        ));

        foreach ($details as $detail) {
            $legacyProduct = $detail->legacy_product;
            if (Schema::hasTable('producto_medicina')
                && DB::table('producto_medicina')->where('id_producto', $legacyProduct->id)->exists()) {
                // Sanea medicamentos creados por sincronizaciones antiguas que
                // quedaron catalogados como reutilizables.
                DB::table('producto')->where('id', $legacyProduct->id)->update(array(
                    'usos' => 0,
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ));
                $legacyProduct->usos = 0;
            }
            $inventoryId = $this->inventoryId(
                $legacyProduct->id,
                $warehouseId,
                $companyId,
                $userId,
                $ip
            );
            $quantity = $detail->clinical_quantity;
            $usesPerUnit = max(0, (int) $legacyProduct->usos);
            $series = 'CP-'.$purchase->id.'-'.$detail->id;
            $extended = (float) $detail->extendido;
            $discount = (float) $detail->descuento;
            $subtotal = round(max(0, $extended - $discount), 4);
            $unitCost = round($subtotal / $quantity, 4);
            $taxRate = (int) $detail->iva === 1 ? (float) $detail->porcentaje : 0;
            $tax = round($subtotal * $taxRate, 4);

            $movementId = DB::table('movimiento')->insertGetId(array(
                'id_producto' => $legacyProduct->id,
                'cantidad' => $quantity,
                'serie' => $series,
                'id_bodega' => $warehouseId,
                'id_pedido' => $orderId,
                'estado' => 1,
                'tipo' => 1,
                'fecha_vencimiento' => $detail->fecha_exp,
                'id_encargado' => $userId,
                'usos' => $quantity * $usesPerUnit,
                'lote' => $detail->lote,
                'precio' => $unitCost,
                // El costo ya se normaliza neto del descuento de la compra.
                'descuento' => 0,
                'descuentop' => 0,
                'consecion_det' => 0,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));

            DB::table('inv_det_movimientos')->insert(array(
                'id_inv_cab_movimientos' => $headerId,
                'id_producto' => $legacyProduct->id,
                'serie' => $series,
                'fecha_vence' => $detail->fecha_exp,
                'lote' => $detail->lote,
                'id_inv_inventario' => $inventoryId,
                'cantidad' => $quantity,
                'cant_uso' => $quantity * $usesPerUnit,
                'valor_unitario' => $unitCost,
                'subtotal' => $subtotal,
                'descuento' => 0,
                'iva' => $tax,
                'total' => round($subtotal + $tax, 4),
                'estado' => 1,
                'kardex' => 0,
                'motivo' => 'COMPRA V3 #'.$purchase->id,
                'id_detalle_origen' => $detail->id,
                'id_pedido' => $orderId,
                'id_detalle_pedido' => $movementId,
                'cantidad_usos_kardex' => $quantity * $usesPerUnit,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        }

        $result = InvKardex::setKardex($headerId);
        if (!is_array($result) || empty($result['status'])) {
            throw new RuntimeException('No se pudo registrar el Kardex hospitalario de la compra.');
        }

        return array('movidos' => count($details), 'ya_procesado' => false);
    }

    private function recordV3AdjustmentWarehouse(
        $movementId,
        $number,
        $date,
        $observation,
        array $lines,
        $warehouseId,
        $companyId,
        $userId,
        $ip
    ) {
        $reference = 'AJUSTE-V3:'.$movementId.':'.$warehouseId;
        $existing = DB::table('inv_cab_movimientos')->where('id_empresa', $companyId)
            ->where('data_humanlabs', $reference)->where('estado', '<>', 0)
            ->lockForUpdate()->first();
        if ($existing) {
            $count = DB::table('inv_det_movimientos')->where('id_inv_cab_movimientos', $existing->id)
                ->where('estado', 1)->count();
            if ($count !== count($lines)) {
                throw new RuntimeException('El ajuste ya tiene un ingreso hospitalario incompleto; requiere revision.');
            }
            return array('movidos' => $count, 'ya_procesado' => true, 'movement_id' => $existing->id);
        }

        $document = $this->ensureEntryDocument($companyId, $userId, $ip);
        $sequence = $this->nextEntrySequence($document->id, $warehouseId, $companyId, $userId, $ip);
        $total = 0;
        foreach ($lines as $line) $total += (float) $line['quantity'] * (float) $line['unit_cost'];
        $orderId = $this->adjustmentOrder(
            $movementId,
            $number,
            $date,
            $observation,
            $document->id,
            $warehouseId,
            $companyId,
            $userId,
            $ip,
            $total
        );
        $now = date('Y-m-d H:i:s');
        $headerId = DB::table('inv_cab_movimientos')->insertGetId(array(
            'id_documento_bodega' => $document->id,
            'id_transaccion_bodega' => $sequence['transaction_id'],
            'id_bodega_origen' => $warehouseId,
            'id_bodega_destino' => $warehouseId,
            'numero_documento' => str_pad($sequence['number'], 9, '0', STR_PAD_LEFT),
            'num_doc_cont' => substr($number, 0, 10),
            'observacion' => substr('AJUSTE V3: '.$observation, 0, 200),
            'fecha' => $date,
            'descuento' => 0,
            'subtotal' => round($total, 2),
            'subtotal_0' => round($total, 2),
            'iva' => 0,
            'total' => round($total, 2),
            'estado' => 1,
            'id_pedido' => $orderId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'data_humanlabs' => $reference,
        ));

        foreach ($lines as $line) {
            $legacyProduct = $line['legacy_product'];
            if (Schema::hasTable('producto_medicina')
                && DB::table('producto_medicina')->where('id_producto', $legacyProduct->id)->exists()) {
                DB::table('producto')->where('id', $legacyProduct->id)->update(array(
                    'usos' => 0,
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ));
                $legacyProduct->usos = 0;
            }
            $inventoryId = $this->inventoryId($legacyProduct->id, $warehouseId, $companyId, $userId, $ip);
            $quantity = (int) $line['clinical_quantity'];
            $usesPerUnit = max(0, (int) $legacyProduct->usos);
            $series = substr('AJ-'.$movementId.'-'.$line['adjustment_detail_id'], 0, 90);
            $unitCost = round((float) $line['unit_cost'], 4);
            $subtotal = round($unitCost * $quantity, 4);
            $legacyMovementId = DB::table('movimiento')->insertGetId(array(
                'id_producto' => $legacyProduct->id,
                'cantidad' => $quantity,
                'serie' => $series,
                'id_bodega' => $warehouseId,
                'id_pedido' => $orderId,
                'estado' => 1,
                'tipo' => 1,
                'fecha_vencimiento' => $line['expiration'],
                'id_encargado' => $userId,
                'usos' => $quantity * $usesPerUnit,
                'lote' => $line['lot'],
                'precio' => $unitCost,
                'descuento' => 0,
                'descuentop' => 0,
                'consecion_det' => 0,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));
            DB::table('inv_det_movimientos')->insert(array(
                'id_inv_cab_movimientos' => $headerId,
                'id_producto' => $legacyProduct->id,
                'serie' => $series,
                'fecha_vence' => $line['expiration'],
                'lote' => $line['lot'],
                'id_inv_inventario' => $inventoryId,
                'cantidad' => $quantity,
                'cant_uso' => $quantity * $usesPerUnit,
                'valor_unitario' => $unitCost,
                'subtotal' => $subtotal,
                'descuento' => 0,
                'iva' => 0,
                'total' => $subtotal,
                'estado' => 1,
                'kardex' => 0,
                'motivo' => 'AJUSTE DE ENTRADA V3 '.$number,
                'id_detalle_origen' => $line['adjustment_detail_id'],
                'id_pedido' => $orderId,
                'id_detalle_pedido' => $legacyMovementId,
                'cantidad_usos_kardex' => $quantity * $usesPerUnit,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        }
        $result = InvKardex::setKardex($headerId);
        if (!is_array($result) || empty($result['status'])) {
            throw new RuntimeException('No se pudo registrar el Kardex hospitalario del ajuste V3.');
        }
        return array('movidos' => count($lines), 'ya_procesado' => false, 'movement_id' => $headerId);
    }

    private function recordV3AdjustmentExitWarehouse(
        $movementId,
        $number,
        $date,
        $observation,
        array $lines,
        $warehouseId,
        $companyId,
        $userId,
        $ip
    ) {
        $reference = 'AJUSTE-V3-E:'.$movementId.':'.$warehouseId;
        $existing = DB::table('inv_cab_movimientos')->where('id_empresa', $companyId)
            ->where('data_humanlabs', $reference)->where('estado', '<>', 0)
            ->lockForUpdate()->first();
        if ($existing) {
            $count = DB::table('inv_det_movimientos')->where('id_inv_cab_movimientos', $existing->id)
                ->where('estado', 1)->distinct()->count('id_detalle_origen');
            if ($count !== count($lines)) {
                throw new RuntimeException('El ajuste ya tiene un egreso hospitalario incompleto; requiere revision.');
            }
            return array('movidos' => $count, 'ya_procesado' => true, 'movement_id' => $existing->id);
        }

        $document = $this->ensureExitDocument($companyId, $userId, $ip);
        $sequence = $this->nextEntrySequence($document->id, $warehouseId, $companyId, $userId, $ip);
        $total = 0;
        foreach ($lines as $line) $total += (float) $line['quantity'] * (float) $line['unit_cost'];
        $orderId = $this->adjustmentOrder(
            $movementId, $number, $date, $observation, $document->id,
            $warehouseId, $companyId, $userId, $ip, $total
        );
        $now = date('Y-m-d H:i:s');
        $headerId = DB::table('inv_cab_movimientos')->insertGetId(array(
            'id_documento_bodega' => $document->id,
            'id_transaccion_bodega' => $sequence['transaction_id'],
            'id_bodega_origen' => $warehouseId,
            'id_bodega_destino' => $warehouseId,
            'numero_documento' => str_pad($sequence['number'], 9, '0', STR_PAD_LEFT),
            'num_doc_cont' => substr($number, 0, 10),
            'observacion' => substr('AJUSTE EGRESO V3: '.$observation, 0, 200),
            'fecha' => $date,
            'descuento' => 0,
            'subtotal' => round($total, 2),
            'subtotal_0' => round($total, 2),
            'iva' => 0,
            'total' => round($total, 2),
            'estado' => 1,
            'id_pedido' => $orderId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'data_humanlabs' => $reference,
        ));

        foreach ($lines as $line) {
            $legacyProduct = $line['legacy_product'];
            $quantity = (int) $line['clinical_quantity'];
            $usesPerUnit = max(0, (int) $legacyProduct->usos);
            $remaining = $quantity;
            $series = DB::table('inv_inventario_serie')
                ->where('id_producto', $legacyProduct->id)->where('id_bodega', $warehouseId)
                ->where('id_empresa', $companyId)->where('estado', 1)
                ->where('lote', $line['lot'])->whereDate('fecha_vence', $line['expiration'])
                ->where('existencia', '>', 0)->orderBy('fecha_vence')->orderBy('id')
                ->lockForUpdate()->get();
            if ((float) $series->sum('existencia') + 0.0001 < $quantity) {
                throw new RuntimeException(
                    'No existe stock hospitalario suficiente del lote '.$line['lot'].' para '.$legacyProduct->nombre.'.'
                );
            }
            foreach ($series as $serie) {
                if ($remaining <= 0) break;
                $allocated = min($remaining, (int) floor((float) $serie->existencia));
                if ($allocated <= 0) continue;
                $unitCost = round((float) $line['unit_cost'], 4);
                $subtotal = round($unitCost * $allocated, 4);
                DB::table('inv_det_movimientos')->insert(array(
                    'id_inv_cab_movimientos' => $headerId,
                    'id_producto' => $legacyProduct->id,
                    'serie' => $serie->serie,
                    'fecha_vence' => $serie->fecha_vence,
                    'lote' => $serie->lote,
                    'id_inv_inventario' => $serie->id_inv_inventario,
                    'cantidad' => $allocated,
                    'cant_uso' => $allocated * $usesPerUnit,
                    'valor_unitario' => $unitCost,
                    'subtotal' => $subtotal,
                    'descuento' => 0,
                    'iva' => 0,
                    'total' => $subtotal,
                    'estado' => 1,
                    'kardex' => 0,
                    'motivo' => 'AJUSTE DE EGRESO V3 '.$number,
                    'id_detalle_origen' => $line['adjustment_detail_id'],
                    'id_pedido' => $orderId,
                    'cantidad_usos_kardex' => $allocated * $usesPerUnit,
                    'id_usuariocrea' => $userId,
                    'id_usuariomod' => $userId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                $remaining -= $allocated;
            }
            if ($remaining > 0) {
                throw new RuntimeException('No fue posible asignar todo el lote hospitalario solicitado.');
            }
        }

        $result = InvKardex::setKardex($headerId);
        if (!is_array($result) || empty($result['status'])) {
            throw new RuntimeException('No se pudo registrar el Kardex hospitalario del egreso V3.');
        }
        return array('movidos' => count($lines), 'ya_procesado' => false, 'movement_id' => $headerId);
    }

    public function ensureEntryDocument($companyId, $userId, $ip)
    {
        $document = DB::table('inv_documentos_bodegas')->where('id_empresa', $companyId)
            ->where('abreviatura_documento', 'INV')->lockForUpdate()->first();
        if ($document) {
            $movementType = DB::table('inv_tipo_movimiento')
                ->where('id', $document->id_inv_tipo_movimiento)->value('tipo');
            if ($movementType !== 'I') {
                throw new RuntimeException('El documento INV de la empresa no esta configurado como ingreso de inventario.');
            }
            return $document;
        }

        $typeId = DB::table('inv_tipo_movimiento')->where('tipo', 'I')->value('id');
        if (!$typeId) {
            $typeId = DB::table('inv_tipo_movimiento')->insertGetId(array(
                'tipo' => 'I',
                'descripcion' => 'INGRESO',
            ));
        }

        $now = date('Y-m-d H:i:s');
        $id = DB::table('inv_documentos_bodegas')->insertGetId(array(
            'documento' => 'INGRESO DE INVENTARIO',
            'abreviatura_documento' => 'INV',
            'id_inv_tipo_movimiento' => $typeId,
            'tipo' => 'F',
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        return DB::table('inv_documentos_bodegas')->where('id', $id)->first();
    }

    private function ensureExitDocument($companyId, $userId, $ip)
    {
        $document = DB::table('inv_documentos_bodegas')->where('id_empresa', $companyId)
            ->where('abreviatura_documento', 'EGR')->lockForUpdate()->first();
        if ($document) {
            $movementType = DB::table('inv_tipo_movimiento')
                ->where('id', $document->id_inv_tipo_movimiento)->value('tipo');
            if ($movementType !== 'E') {
                throw new RuntimeException('El documento EGR de la empresa no esta configurado como egreso de inventario.');
            }
            return $document;
        }
        $typeId = DB::table('inv_tipo_movimiento')->where('tipo', 'E')->value('id');
        if (!$typeId) {
            $typeId = DB::table('inv_tipo_movimiento')->insertGetId(array(
                'tipo' => 'E', 'descripcion' => 'EGRESO',
            ));
        }
        $now = date('Y-m-d H:i:s');
        $id = DB::table('inv_documentos_bodegas')->insertGetId(array(
            'documento' => 'EGRESO DE INVENTARIO',
            'abreviatura_documento' => 'EGR',
            'id_inv_tipo_movimiento' => $typeId,
            'tipo' => 'F',
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        return DB::table('inv_documentos_bodegas')->where('id', $id)->first();
    }

    public function nextEntrySequence($documentId, $warehouseId, $companyId, $userId, $ip)
    {
        $transaction = DB::table('inv_transacciones_bodegas')
            ->where('id_documento_bodega', $documentId)
            ->where('id_bodega', $warehouseId)
            ->where('id_empresa', $companyId)->lockForUpdate()->first();
        $now = date('Y-m-d H:i:s');
        if (!$transaction) {
            $id = DB::table('inv_transacciones_bodegas')->insertGetId(array(
                'id_documento_bodega' => $documentId,
                'id_bodega' => $warehouseId,
                'secuencia' => 1,
                'id_empresa' => $companyId,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));
            return array('transaction_id' => $id, 'number' => 1);
        }

        $number = max(0, (int) $transaction->secuencia) + 1;
        DB::table('inv_transacciones_bodegas')->where('id', $transaction->id)->update(array(
            'secuencia' => $number,
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
            'updated_at' => $now,
        ));
        return array('transaction_id' => $transaction->id, 'number' => $number);
    }

    private function purchaseOrder($purchase, $documentId, $warehouseId, $companyId, $userId, $ip)
    {
        $reference = 'COMPRA-V3-'.$purchase->id.'-'.$warehouseId;
        $existing = DB::table('pedido')->where('id_empresa', $companyId)
            ->where('pedido', $reference)->lockForUpdate()->first();
        if ($existing) return (int) $existing->id;

        $now = date('Y-m-d H:i:s');
        return DB::table('pedido')->insertGetId(array(
            'id_proveedor' => $purchase->proveedor,
            'pedido' => $reference,
            'tipo' => $documentId,
            'factura' => (string) $purchase->numero,
            'fecha' => $purchase->fecha,
            'vencimiento' => $purchase->f_caducidad ?: $purchase->fecha,
            'observaciones' => substr('Ingreso automatico compra V3 #'.$purchase->id, 0, 90),
            'id_bodega' => $warehouseId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'total' => $purchase->total_final,
            'subtotal_0' => $purchase->subtotal_0,
            'subtotal_12' => $purchase->subtotal_12,
            'iva' => $purchase->iva_total,
            'descuento' => $purchase->descuento,
            'estado_compras' => 1,
        ));
    }

    private function adjustmentOrder(
        $movementId,
        $number,
        $date,
        $observation,
        $documentId,
        $warehouseId,
        $companyId,
        $userId,
        $ip,
        $total
    ) {
        $reference = 'AJUSTE-V3-'.$movementId.'-'.$warehouseId;
        $existing = DB::table('pedido')->where('id_empresa', $companyId)
            ->where('pedido', $reference)->lockForUpdate()->first();
        if ($existing) return (int) $existing->id;

        $providerId = DB::table('proveedor')->where('id', '9999999999999')->value('id');
        if (!$providerId) {
            $providerId = DB::table('proveedor')->where('estado', 1)->orderBy('id')->value('id');
        }
        if (!$providerId) {
            throw new RuntimeException('No existe un proveedor activo para documentar el movimiento hospitalario del ajuste.');
        }
        $now = date('Y-m-d H:i:s');
        return DB::table('pedido')->insertGetId(array(
            'id_proveedor' => $providerId,
            'pedido' => $reference,
            'tipo' => $documentId,
            'factura' => $number,
            'fecha' => $date,
            'vencimiento' => $date,
            'observaciones' => substr($observation, 0, 90),
            'id_bodega' => $warehouseId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'total' => round($total, 2),
            'subtotal_0' => round($total, 2),
            'subtotal_12' => 0,
            'iva' => 0,
            'descuento' => 0,
            'estado_compras' => 1,
        ));
    }

    private function inventoryId($productId, $warehouseId, $companyId, $userId, $ip)
    {
        $rows = DB::table('inv_inventario')->where('id_producto', $productId)
            ->where('id_bodega', $warehouseId)->where('id_empresa', $companyId)
            ->whereNull('deleted_at')->lockForUpdate()->get();
        if ($rows->count() > 1) {
            throw new RuntimeException('Existen saldos hospitalarios duplicados para el producto y la bodega.');
        }
        if ($rows->count() === 1) return (int) $rows->first()->id;

        $now = date('Y-m-d H:i:s');
        return DB::table('inv_inventario')->insertGetId(array(
            'id_producto' => $productId,
            'id_bodega' => $warehouseId,
            'tipo' => 'F',
            'existencia' => 0,
            'existencia_uso' => 0,
            'comprometido' => 0,
            'comprometido_uso' => 0,
            'costo_promedio' => 0,
            'estado' => 1,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }
}
