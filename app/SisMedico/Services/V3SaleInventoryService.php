<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Reserva y registra el egreso V3 de una venta.
 *
 * Este servicio debe ejecutarse dentro de la misma transaccion que crea la
 * factura. Los bloqueos se conservan hasta el commit para impedir que dos
 * usuarios consuman simultaneamente las mismas unidades.
 */
class V3SaleInventoryService
{
    /**
     * Revierte exclusivamente el Kardex V3 moderno de una venta anulada.
     * El Kardex historico se revierte en Kardex::anular_kardex; mantener ambas
     * operaciones separadas evita duplicar el reingreso en ct_inv_interno.
     */
    public function reverseModernSale($companyId, $saleId, $number, $date, $userId, $ip)
    {
        $saleRows = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('id_movimiento', $saleId)->where('tipo', 'VEN')
            ->orderBy('producto_id')->orderBy('bodega_id')->orderBy('id')
            ->lockForUpdate()->get();
        if (!count($saleRows)) return 0;

        $existing = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('id_movimiento', $saleId)->where('tipo', 'ANU-VEN')
            ->lockForUpdate()->get();
        if (count($existing)) return count($existing);

        $groups = array();
        foreach ($saleRows as $row) {
            $key = (int) $row->producto_id.'|'.(int) $row->bodega_id;
            if (!isset($groups[$key])) $groups[$key] = array('row'=>$row, 'quantity'=>0);
            $groups[$key]['quantity'] += (float) $row->cantidad;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($groups as $group) {
            $row = $group['row'];
            $quantity = round((float) $group['quantity'], 4);
            $inventory = DB::table('ct_inventario')->where('id_empresa', $companyId)
                ->where('producto_id', $row->producto_id)->where('bodega_id', $row->bodega_id)
                ->orderBy('id')->lockForUpdate()->first();
            if (!$inventory) throw new RuntimeException('No existe el saldo V3 que debe recibir la devolucion del producto '.$row->producto_id.'.');
            $newQuantity = round((float) $inventory->existencia + $quantity, 4);
            DB::table('ct_inventario')->where('id', $inventory->id)->update(array(
                'existencia'=>$newQuantity, 'id_usuariomod'=>$userId, 'ip_modificacion'=>$ip, 'updated_at'=>$now));
            DB::table('ct_kardex')->insert(array(
                'fecha'=>$date, 'id_movimiento'=>$saleId, 'movimiento'=>1, 'tipo'=>'ANU-VEN',
                'numero'=>$number, 'producto_id'=>$row->producto_id, 'cantidad'=>$quantity,
                'valor_unitario'=>$row->valor_unitario, 'total'=>round((float)$row->valor_unitario*$quantity,2),
                'saldo_cantidad'=>$newQuantity, 'saldo_valor_unitario'=>$row->valor_unitario,
                'saldo_total'=>round((float)$row->valor_unitario*$newQuantity,2), 'bodega_id'=>$row->bodega_id,
                'id_empresa'=>$companyId, 'id_usuariocrea'=>$userId, 'id_usuariomod'=>$userId,
                'ip_creacion'=>$ip, 'ip_modificacion'=>$ip, 'created_at'=>$now, 'updated_at'=>$now));
        }
        return count($groups);
    }

    public function lockAndValidate($companyId, array $lines)
    {
        $groups = array();
        $requiredByProduct = array();
        $legacyByProduct = array();

        foreach ($lines as $line) {
            $product = $line['product'];
            if ((int) $product->tipo_producto !== 2) {
                continue;
            }

            $quantity = (float) $line['quantity'];
            $warehouseId = (int) $line['warehouse_id'];
            if ($quantity <= 0) {
                throw new RuntimeException('La cantidad de '.$product->nombre.' debe ser mayor a cero.');
            }
            if ($warehouseId <= 0) {
                throw new RuntimeException('Seleccione la bodega para el producto inventariable '.$product->nombre.'.');
            }

            $key = str_pad((string) $product->id, 20, '0', STR_PAD_LEFT).'|'.str_pad((string) $warehouseId, 20, '0', STR_PAD_LEFT);
            if (!isset($groups[$key])) {
                $groups[$key] = array(
                    'product' => $product,
                    'warehouse_id' => $warehouseId,
                    'quantity' => 0,
                );
            }
            $groups[$key]['quantity'] += $quantity;
            if (!isset($requiredByProduct[$product->id])) {
                $requiredByProduct[$product->id] = array('product' => $product, 'quantity' => 0);
            }
            $requiredByProduct[$product->id]['quantity'] += $quantity;
        }

        ksort($groups, SORT_STRING);
        foreach ($groups as $key => &$group) {
            $rows = DB::table('ct_inventario')
                ->where('id_empresa', $companyId)
                ->where('producto_id', $group['product']->id)
                ->where('bodega_id', $group['warehouse_id'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if (count($rows) > 1) {
                throw new RuntimeException('Existen registros duplicados de inventario para '.$group['product']->nombre.' en la bodega seleccionada.');
            }

            $inventory = count($rows) === 1 ? $rows->first() : null;
            $available = $inventory ? (float) $inventory->existencia : 0;
            $required = round((float) $group['quantity'], 4);
            if ($available + 0.0001 < $required) {
                throw new RuntimeException(
                    'Stock insuficiente para '.$group['product']->nombre.'. Disponible: '.number_format($available, 2, '.', '').', solicitado: '.number_format($required, 2, '.', '').'.'
                );
            }

            $group['inventory_id'] = $inventory->id;
            $group['previous_quantity'] = $available;
            $group['new_quantity'] = round($available - $required, 4);
        }
        unset($group);

        ksort($requiredByProduct, SORT_NUMERIC);
        foreach ($requiredByProduct as $required) {
            $legacy = DB::table('ct_inv_interno')
                ->where('id_empresa', $companyId)
                ->where('id_producto', $required['product']->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            if (count($legacy) > 1) {
                throw new RuntimeException('Existen saldos internos duplicados para '.$required['product']->nombre.'.');
            }
            $legacyAvailable = count($legacy) === 1 ? (float) $legacy->first()->stock : 0;
            if ($legacyAvailable + 0.0001 < (float) $required['quantity']) {
                throw new RuntimeException(
                    'El kardex no tiene stock suficiente para '.$required['product']->nombre.'. Disponible: '.
                    number_format($legacyAvailable, 2, '.', '').', solicitado: '.
                    number_format((float) $required['quantity'], 2, '.', '').
                    '. Revise Inventario > Lista de productos > Reconstruir inventario.'
                );
            }
            $legacyRow = $legacy->first();
            $legacyByProduct[$required['product']->id] = array(
                'inventory_id' => $legacyRow->id,
                'previous_quantity' => $legacyAvailable,
                'new_quantity' => round($legacyAvailable - (float) $required['quantity'], 4),
                'unit_cost' => round((float) $legacyRow->costo, 4),
            );
        }

        foreach ($groups as &$group) {
            $legacyData = $legacyByProduct[$group['product']->id];
            $group['legacy_inventory_id'] = $legacyData['inventory_id'];
            $group['legacy_previous_quantity'] = $legacyData['previous_quantity'];
            $group['legacy_new_quantity'] = $legacyData['new_quantity'];
            $group['legacy_unit_cost'] = $legacyData['unit_cost'];
        }
        unset($group);

        return array_values($groups);
    }

    public function recordSale($companyId, $saleId, $number, $date, array $reservation, $userId, $ip)
    {
        $now = date('Y-m-d H:i:s');
        $this->assertLegacySaleRecorded($companyId, $saleId, $reservation);

        $existingModern = DB::table('ct_kardex')
            ->where('id_empresa', $companyId)
            ->where('id_movimiento', $saleId)
            ->where('tipo', 'VEN')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
        if (count($existingModern) > 0) {
            $this->assertModernKardexMatches($existingModern, $reservation);
            return;
        }

        foreach ($reservation as $group) {
            $affected = DB::table('ct_inventario')
                ->where('id', $group['inventory_id'])
                ->where('id_empresa', $companyId)
                ->where('existencia', '>=', $group['quantity'])
                ->update(array(
                    'existencia' => $group['new_quantity'],
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ));

            if ($affected !== 1) {
                throw new RuntimeException('La existencia de '.$group['product']->nombre.' cambio mientras se guardaba la venta. Intente nuevamente.');
            }

            $previous = DB::table('ct_kardex')
                ->where('id_empresa', $companyId)
                ->where('producto_id', $group['product']->id)
                ->where('bodega_id', $group['warehouse_id'])
                ->orderBy('id', 'desc')
                ->first();
            $unitCost = $previous ? (float) $previous->saldo_valor_unitario : 0;
            if ($unitCost <= 0 && isset($group['legacy_unit_cost'])) {
                $unitCost = (float) $group['legacy_unit_cost'];
            }

            DB::table('ct_kardex')->insert(array(
                'fecha' => $date,
                'id_movimiento' => $saleId,
                'movimiento' => 2,
                'tipo' => 'VEN',
                'numero' => $number,
                'producto_id' => $group['product']->id,
                'cantidad' => round((float) $group['quantity'], 2),
                'valor_unitario' => round($unitCost, 2),
                'total' => round($unitCost * (float) $group['quantity'], 2),
                'saldo_cantidad' => round((float) $group['new_quantity'], 2),
                'saldo_valor_unitario' => round($unitCost, 2),
                'saldo_total' => round($unitCost * (float) $group['new_quantity'], 2),
                'bodega_id' => $group['warehouse_id'],
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

    private function assertLegacySaleRecorded($companyId, $saleId, array $reservation)
    {
        $movement = DB::table('ct_inv_movimiento')
            ->where('id_referencia', $saleId)
            ->where('tipo', 'VENTA')
            ->orderBy('id')
            ->lockForUpdate()
            ->first();
        if (!$movement) {
            throw new RuntimeException('No se registro el movimiento historico de inventario de la venta.');
        }

        $legacyRows = DB::table('ct_inv_kardex')
            ->where('id_movimiento', $movement->id)
            ->where('estado', 1)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
        $legacyQuantities = array();
        foreach ($legacyRows as $row) {
            $key = (int) $row->id_producto.'|'.(int) $row->id_bodega;
            if (!isset($legacyQuantities[$key])) $legacyQuantities[$key] = 0;
            $legacyQuantities[$key] += (float) $row->cantidad;
        }

        $expected = array();
        $legacyProducts = array();
        foreach ($reservation as $group) {
            $key = (int) $group['product']->id.'|'.(int) $group['warehouse_id'];
            $expected[$key] = round((float) $group['quantity'], 4);
            $legacyProducts[(int) $group['product']->id] = $group;
        }
        if (count($legacyQuantities) !== count($expected)) {
            throw new RuntimeException('El Kardex historico no coincide con las lineas de inventario de la venta.');
        }
        foreach ($expected as $key => $quantity) {
            if (!isset($legacyQuantities[$key]) || abs($legacyQuantities[$key] - $quantity) > 0.0001) {
                throw new RuntimeException('El Kardex historico no coincide con el producto y la bodega seleccionados.');
            }
        }

        foreach ($legacyProducts as $group) {
            $legacyInventory = DB::table('ct_inv_interno')
                ->where('id', $group['legacy_inventory_id'])
                ->where('id_empresa', $companyId)
                ->lockForUpdate()
                ->first();
            if (!$legacyInventory || abs((float) $legacyInventory->stock - (float) $group['legacy_new_quantity']) > 0.0001) {
                throw new RuntimeException('El saldo interno no quedo conciliado con el movimiento de la venta.');
            }
        }
    }

    private function assertModernKardexMatches($rows, array $reservation)
    {
        $actual = array();
        foreach ($rows as $row) {
            $key = (int) $row->producto_id.'|'.(int) $row->bodega_id;
            if (!isset($actual[$key])) $actual[$key] = 0;
            $actual[$key] += (float) $row->cantidad;
        }
        if (count($actual) !== count($reservation)) {
            throw new RuntimeException('La venta ya tiene un Kardex V3 inconsistente.');
        }
        foreach ($reservation as $group) {
            $key = (int) $group['product']->id.'|'.(int) $group['warehouse_id'];
            if (!isset($actual[$key]) || abs($actual[$key] - (float) $group['quantity']) > 0.0001) {
                throw new RuntimeException('La venta ya tiene un Kardex V3 distinto al inventario solicitado.');
            }
        }
    }
}
