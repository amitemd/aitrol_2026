<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Registra el reingreso de productos devueltos por una nota de credito.
 * Debe ejecutarse dentro de la misma transaccion de la nota y del asiento.
 */
class V3CustomerCreditInventoryService
{
    public function returnProducts($companyId, $creditNoteId, $saleId, $number, $date, array $lines, $userId, $ip)
    {
        $groups = array();
        foreach ($lines as $line) {
            if ((int) $line['product']->tipo_producto !== 2) continue;
            $warehouseId = (int) $line['warehouse_id'];
            if ($warehouseId <= 0) {
                throw new RuntimeException('La venta no conserva la bodega de salida de '.$line['product']->nombre.'. Revise el Kardex antes de emitir la nota.');
            }
            $key = (int) $line['product']->id.'|'.$warehouseId;
            if (!isset($groups[$key])) {
                $groups[$key] = array('product' => $line['product'], 'warehouse_id' => $warehouseId, 'quantity' => 0, 'unit_cost' => 0);
            }
            $groups[$key]['quantity'] += (float) $line['quantity'];
        }
        if (!$groups) return 0.0;
        ksort($groups, SORT_STRING);

        $existing = DB::table('ct_inv_movimiento')->where('id_referencia', $creditNoteId)
            ->where('tipo', 'DEV_VENTA')->lockForUpdate()->first();
        if ($existing) {
            throw new RuntimeException('La devolución de esta nota de crédito ya fue registrada en el Kardex.');
        }

        $movementId = DB::table('ct_inv_movimiento')->insertGetId(array(
            'id_transaccion' => 1,
            'id_referencia' => $creditNoteId,
            'detalle' => 'DEVOLUCION DE VENTA '.$number,
            'tipo' => 'DEV_VENTA',
            'pedido' => $number,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $totalCost = 0.0;
        foreach ($groups as &$group) {
            $productId = (int) $group['product']->id;
            $warehouseId = (int) $group['warehouse_id'];
            $quantity = round((float) $group['quantity'], 2);

            $inventory = DB::table('ct_inventario')->where('id_empresa', $companyId)
                ->where('producto_id', $productId)->where('bodega_id', $warehouseId)
                ->orderBy('id')->lockForUpdate()->first();
            $previousQuantity = $inventory ? (float) $inventory->existencia : 0.0;

            $legacy = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
                ->where('id_producto', $productId)->orderBy('id')->lockForUpdate()->first();
            if (!$legacy) {
                throw new RuntimeException('No existe el saldo histórico del producto '.$group['product']->nombre.'. Reconstruya su inventario antes de emitir la nota.');
            }

            $saleKardex = DB::table('ct_kardex')->where('id_empresa', $companyId)
                ->where('id_movimiento', $saleId)->where('tipo', 'VEN')
                ->where('producto_id', $productId)->where('bodega_id', $warehouseId)
                ->orderBy('id', 'desc')->first();
            $unitCost = $saleKardex ? (float) $saleKardex->valor_unitario : (float) $legacy->costo;
            $newQuantity = round($previousQuantity + $quantity, 4);
            $audit = array('id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s'));

            if ($inventory) {
                DB::table('ct_inventario')->where('id', $inventory->id)->update(array_merge($audit, array('existencia' => $newQuantity)));
            } else {
                DB::table('ct_inventario')->insert(array_merge($audit, array(
                    'producto_id' => $productId, 'bodega_id' => $warehouseId, 'existencia' => $newQuantity,
                    'id_empresa' => $companyId, 'id_usuariocrea' => $userId, 'ip_creacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                )));
            }

            DB::table('ct_inv_interno')->where('id', $legacy->id)->update(array(
                'stock' => round((float) $legacy->stock + $quantity, 2),
                'cantidad' => round((float) $legacy->cantidad + $quantity, 2),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
            DB::table('ct_inv_kardex')->insert(array(
                'id_bodega' => $warehouseId, 'id_movimiento' => $movementId, 'id_producto' => $productId,
                'serie' => '', 'id_transaccion' => 1, 'cantidad' => $quantity, 'precio' => round($unitCost, 2),
                'id_inv' => $legacy->id, 'estado' => 1, 'fecha' => $date,
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_modificacion' => $ip, 'id_modificacion' => $userId,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ));
            DB::table('ct_kardex')->insert(array(
                'fecha' => $date, 'id_movimiento' => $creditNoteId, 'movimiento' => 1, 'tipo' => 'NCV',
                'numero' => $number, 'producto_id' => $productId, 'cantidad' => $quantity,
                'valor_unitario' => round($unitCost, 2), 'total' => round($unitCost * $quantity, 2),
                'saldo_cantidad' => $newQuantity, 'saldo_valor_unitario' => round($unitCost, 2),
                'saldo_total' => round($unitCost * $newQuantity, 2), 'bodega_id' => $warehouseId,
                'id_empresa' => $companyId, 'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ));
            $group['unit_cost'] = $unitCost;
            $totalCost += $unitCost * $quantity;
        }
        unset($group);
        return round($totalCost, 2);
    }
}
