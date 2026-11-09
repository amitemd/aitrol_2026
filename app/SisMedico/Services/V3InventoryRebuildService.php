<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Reconstruye los saldos de un producto desde sus movimientos persistidos.
 *
 * ct_inv_kardex conserva ajustes, cargas, mermas y movimientos V2. ct_kardex
 * aporta compras/ventas V3 que pudieron quedar sin su equivalente historico.
 * Nunca se solicita ni se acepta un saldo escrito por el usuario.
 */
class V3InventoryRebuildService
{
    const EPSILON = 0.0001;

    public function preview($companyId, $productReference)
    {
        return $this->buildSnapshot($companyId, $productReference, false);
    }

    public function rebuild($companyId, $productReference, $userId, $ip)
    {
        return DB::transaction(function () use ($companyId, $productReference, $userId, $ip) {
            $snapshot = $this->buildSnapshot($companyId, $productReference, true);
            if (!$snapshot['can_rebuild']) {
                throw new RuntimeException(implode(' ', $snapshot['observations']));
            }

            $product = $snapshot['product_model'];
            $internalRows = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
                ->where('id_producto', $product->id)->orderBy('id')->lockForUpdate()->get();
            if (count($internalRows) > 1) {
                throw new RuntimeException('Existen saldos historicos duplicados para el producto. No se modifico ningun registro.');
            }
            if (count($internalRows) === 0) {
                $internalId = DB::table('ct_inv_interno')->insertGetId(array(
                    'id_producto' => $product->id, 'stock' => 0, 'cantidad' => 0,
                    'costo' => 0, 'id_empresa' => $companyId,
                ));
            } else {
                $internalId = $internalRows->first()->id;
            }

            foreach ($snapshot['gaps'] as $gap) {
                $movement = DB::table('ct_inv_movimiento')
                    ->where('id_referencia', $gap['reference_id'])
                    ->where('tipo', $gap['legacy_type'])->orderBy('id')->lockForUpdate()->first();
                if (!$movement) {
                    $movementId = DB::table('ct_inv_movimiento')->insertGetId(array(
                        'id_transaccion' => $gap['transaction_id'],
                        'id_referencia' => $gap['reference_id'],
                        'tipo' => $gap['legacy_type'],
                    ));
                } else {
                    $movementId = $movement->id;
                }

                DB::table('ct_inv_kardex')->insert(array(
                    'id_bodega' => $gap['warehouse_id'],
                    'fecha' => $gap['date'],
                    'id_transaccion' => $gap['transaction_id'],
                    'id_movimiento' => $movementId,
                    'precio' => $gap['unit_cost'],
                    'id_producto' => $product->id,
                    'id_inv' => $internalId,
                    'cantidad' => $gap['quantity'],
                    'estado' => 1,
                ));
            }

            // Se relee despues de incorporar documentos faltantes; el saldo
            // definitivo siempre nace del historial, no de la vista previa.
            $final = $this->buildSnapshot($companyId, $product->id, true);
            if (!$final['can_rebuild'] || count($final['gaps']) > 0) {
                throw new RuntimeException('No fue posible conciliar todos los movimientos del producto. No se aplicaron cambios.');
            }

            $now = date('Y-m-d H:i:s');
            DB::table('ct_inv_interno')->where('id', $internalId)->where('id_empresa', $companyId)
                ->update(array(
                    'stock' => $final['summary']['reconstructed'],
                    'cantidad' => $final['summary']['reconstructed'],
                    'costo' => $final['summary']['average_cost'],
                    'updated_at' => $now,
                ));

            $inventoryRows = DB::table('ct_inventario')->where('id_empresa', $companyId)
                ->where('producto_id', $product->id)->orderBy('bodega_id')->orderBy('id')->lockForUpdate()->get();
            $duplicates = $inventoryRows->groupBy('bodega_id')->first(function ($rows) {
                return count($rows) > 1;
            });
            if ($duplicates) {
                throw new RuntimeException('Existen registros de existencia duplicados por bodega. No se aplicaron cambios.');
            }
            $byWarehouse = $inventoryRows->keyBy('bodega_id');
            foreach ($final['warehouses'] as $warehouse) {
                $audit = array(
                    'existencia' => $warehouse['reconstructed'],
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                );
                $current = $byWarehouse->get($warehouse['id']);
                if ($current) {
                    DB::table('ct_inventario')->where('id', $current->id)->update($audit);
                } else {
                    DB::table('ct_inventario')->insert(array_merge($audit, array(
                        'producto_id' => $product->id,
                        'bodega_id' => $warehouse['id'],
                        'id_empresa' => $companyId,
                        'id_usuariocrea' => $userId,
                        'ip_creacion' => $ip,
                        'created_at' => $now,
                    )));
                }
            }

            Log::info('Inventario V3 reconstruido desde movimientos', array(
                'id_empresa' => $companyId, 'id_producto' => $product->id,
                'codigo' => $product->codigo, 'usuario' => $userId,
                'movimientos' => count($final['events']),
                'saldo' => $final['summary']['reconstructed'], 'ip' => $ip,
            ));

            unset($final['product_model'], $final['gaps']);
            $final['message'] = 'Inventario, saldo historico y existencias por bodega reconstruidos correctamente.';
            return $final;
        });
    }

    private function buildSnapshot($companyId, $productReference, $lock)
    {
        $productQuery = DB::table('ct_productos')->where('id_empresa', $companyId)
            ->where(function ($query) use ($productReference) {
                $query->where('id', $productReference)->orWhere('codigo', trim((string) $productReference));
            });
        if ($lock) $productQuery->lockForUpdate();
        $product = $productQuery->first();
        if (!$product) throw new RuntimeException('No se encontro el producto en la empresa activa.');
        if ((int) $product->tipo_producto !== 2) {
            throw new RuntimeException('Los servicios no controlan existencia y no requieren reconstruccion.');
        }

        $internalQuery = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
            ->where('id_producto', $product->id)->orderBy('id');
        if ($lock) $internalQuery->lockForUpdate();
        $internalRows = $internalQuery->get();

        $inventoryQuery = DB::table('ct_inventario')->where('id_empresa', $companyId)
            ->where('producto_id', $product->id)->orderBy('bodega_id')->orderBy('id');
        if ($lock) $inventoryQuery->lockForUpdate();
        $inventoryRows = $inventoryQuery->get();

        $legacyQuery = DB::table('ct_inv_kardex as k')
            ->join('ct_inv_interno as i', 'i.id', '=', 'k.id_inv')
            ->leftJoin('ct_inv_movimiento as m', 'm.id', '=', 'k.id_movimiento')
            ->where('i.id_empresa', $companyId)->where('k.id_producto', $product->id)
            ->where('k.estado', 1)->orderBy('k.fecha')->orderBy('k.id')
            ->select('k.*', 'm.tipo as source_type', 'm.id_referencia as source_reference');
        if ($lock) $legacyQuery->lockForUpdate();
        $legacyRows = $legacyQuery->get();

        $modernQuery = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('producto_id', $product->id)->orderBy('fecha')->orderBy('id');
        if ($lock) $modernQuery->lockForUpdate();
        $modernRows = $modernQuery->get();

        $activeModern = $this->onlyActiveDocuments($modernRows, $companyId);
        $legacyRows = $this->onlyActiveLegacyDocuments($legacyRows, $companyId);
        $legacyTotals = array();
        $events = array();
        foreach ($legacyRows as $row) {
            $direction = (int) $row->id_transaccion === 3 ? -1 : 1;
            $legacyType = strtoupper(trim((string) $row->source_type));
            $sourceKey = $this->sourceKey($legacyType, $row->source_reference, $row->id_bodega);
            if ($sourceKey) {
                if (!isset($legacyTotals[$sourceKey])) $legacyTotals[$sourceKey] = 0;
                $legacyTotals[$sourceKey] += (float) $row->cantidad;
            }
            $events[] = $this->event($row->fecha, $row->id, $row->id_bodega, $direction,
                (float) $row->cantidad, (float) $row->precio,
                $this->legacyLabel($legacyType, (int) $row->id_transaccion),
                $row->source_reference, 'Histórico');
        }

        $modernTotals = array();
        $modernMeta = array();
        foreach ($activeModern as $row) {
            $legacyType = $this->modernLegacyType($row->tipo);
            if (!$legacyType) continue;
            $key = $this->sourceKey($legacyType, $row->id_movimiento, $row->bodega_id);
            if (!isset($modernTotals[$key])) $modernTotals[$key] = 0;
            $modernTotals[$key] += (float) $row->cantidad;
            $modernMeta[$key] = array(
                'legacy_type' => $legacyType,
                'reference_id' => $row->id_movimiento,
                'warehouse_id' => (int) $row->bodega_id,
                'date' => $row->fecha,
                'unit_cost' => (float) $row->valor_unitario,
                'transaction_id' => $legacyType === 'VENTA' ? 3 : 2,
            );
        }

        $gaps = array();
        $observations = array();
        foreach ($modernTotals as $key => $quantity) {
            $historical = isset($legacyTotals[$key]) ? $legacyTotals[$key] : 0;
            $difference = round($quantity - $historical, 4);
            if ($difference < -self::EPSILON) {
                $observations[] = 'El historial contiene mas cantidad que el documento de origen para '.$key.'.';
                continue;
            }
            if ($difference > self::EPSILON) {
                $gap = $modernMeta[$key];
                $gap['quantity'] = $difference;
                $gaps[] = $gap;
                $direction = $gap['transaction_id'] === 3 ? -1 : 1;
                $events[] = $this->event($gap['date'], 'M-'.$key, $gap['warehouse_id'], $direction,
                    $difference, $gap['unit_cost'], $gap['legacy_type'], $gap['reference_id'], 'Documento V3 pendiente de historial');
            }
        }

        usort($events, function ($a, $b) {
            $date = strcmp((string) $a['date'], (string) $b['date']);
            if ($date !== 0) return $date;
            // Las tablas historicas solo conservan fecha. En un mismo dia se
            // reproducen primero los ingresos para no crear un negativo falso
            // cuando una compra y su consumo ocurrieron durante esa jornada.
            if ($a['direction'] !== $b['direction']) return $a['direction'] === 'Ingreso' ? -1 : 1;
            return strcmp(str_pad((string) $a['order'], 30, '0', STR_PAD_LEFT), str_pad((string) $b['order'], 30, '0', STR_PAD_LEFT));
        });
        $replay = $this->replay($events);
        $observations = array_merge($observations, $replay['observations']);
        if (count($internalRows) > 1) $observations[] = 'Existen saldos historicos duplicados para el producto.';
        $duplicateInventory = $inventoryRows->groupBy('bodega_id')->first(function ($rows) { return count($rows) > 1; });
        if ($duplicateInventory) $observations[] = 'Existen existencias duplicadas para una bodega.';

        $warehouseIds = array_unique(array_merge(array_keys($replay['warehouses']), $inventoryRows->pluck('bodega_id')->all()));
        sort($warehouseIds, SORT_NUMERIC);
        $warehouseNames = DB::table('ct_bodegas')->where('id_empresa', $companyId)
            ->whereIn('id', $warehouseIds ?: array(0))->get()->keyBy('id');
        $currentByWarehouse = $inventoryRows->keyBy('bodega_id');
        $warehouses = array();
        foreach ($warehouseIds as $warehouseId) {
            if ((int) $warehouseId <= 0) {
                $observations[] = 'Existe un movimiento sin bodega válida.';
                continue;
            }
            $warehouse = $warehouseNames->get($warehouseId);
            if (!$warehouse) {
                $observations[] = 'La bodega #'.$warehouseId.' no pertenece a la empresa activa o ya no existe.';
                continue;
            }
            $current = $currentByWarehouse->get($warehouseId);
            $warehouses[] = array(
                'id' => (int) $warehouseId,
                'name' => $warehouse->nombre,
                'current' => round($current ? (float) $current->existencia : 0, 2),
                'reconstructed' => round(isset($replay['warehouses'][$warehouseId]) ? $replay['warehouses'][$warehouseId] : 0, 2),
            );
        }

        $currentModern = round($inventoryRows->sum('existencia'), 2);
        $currentHistorical = round(count($internalRows) === 1 ? (float) $internalRows->first()->stock : 0, 2);
        foreach ($events as &$event) unset($event['order'], $event['signed_quantity']);
        unset($event);

        return array(
            'product_model' => $product,
            'product' => array('id' => $product->id, 'code' => $product->codigo, 'name' => $product->nombre),
            'summary' => array(
                'current_v3' => $currentModern,
                'current_historical' => $currentHistorical,
                'reconstructed' => round($replay['total'], 2),
                'average_cost' => round($replay['average_cost'], 2),
                'entries' => round($replay['entries'], 2),
                'outputs' => round($replay['outputs'], 2),
                'missing_history' => count($gaps),
            ),
            'warehouses' => $warehouses,
            'events' => $events,
            'gaps' => $gaps,
            'observations' => array_values(array_unique($observations)),
            'can_rebuild' => count($observations) === 0,
        );
    }

    private function onlyActiveDocuments($rows, $companyId)
    {
        $purchaseIds = array(); $saleIds = array();
        foreach ($rows as $row) {
            $type = strtoupper(trim((string) $row->tipo));
            if ($type === 'COMP') $purchaseIds[] = $row->id_movimiento;
            if ($type === 'VEN') $saleIds[] = $row->id_movimiento;
        }
        $activePurchases = DB::table('ct_compras')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($purchaseIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();
        $activeSales = DB::table('ct_ventas')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($saleIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();
        return $rows->filter(function ($row) use ($activePurchases, $activeSales) {
            $type = strtoupper(trim((string) $row->tipo));
            if ($type === 'COMP') return $activePurchases->has($row->id_movimiento);
            if ($type === 'VEN') return $activeSales->has($row->id_movimiento);
            return false;
        });
    }

    private function onlyActiveLegacyDocuments($rows, $companyId)
    {
        $purchaseIds = array(); $saleIds = array();
        foreach ($rows as $row) {
            $type = strtoupper(trim((string) $row->source_type));
            if ($type === 'COMP' && $row->source_reference) $purchaseIds[] = $row->source_reference;
            if ($type === 'VENTA' && $row->source_reference) $saleIds[] = $row->source_reference;
        }
        $activePurchases = DB::table('ct_compras')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($purchaseIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();
        $activeSales = DB::table('ct_ventas')->where('id_empresa', $companyId)
            ->whereIn('id', array_unique($saleIds) ?: array(0))->where('estado', '>', 0)->pluck('id')->flip();
        return $rows->filter(function ($row) use ($activePurchases, $activeSales) {
            $type = strtoupper(trim((string) $row->source_type));
            if ($type === 'COMP') return $activePurchases->has($row->source_reference);
            if ($type === 'VENTA') return $activeSales->has($row->source_reference);
            return true;
        });
    }

    private function replay(array $events)
    {
        $warehouses = array(); $entries = 0; $outputs = 0; $stock = 0; $value = 0; $observations = array();
        foreach ($events as &$event) {
            $warehouseId = (int) $event['warehouse_id'];
            if (!isset($warehouses[$warehouseId])) $warehouses[$warehouseId] = 0;
            $quantity = (float) $event['quantity'];
            if ($event['direction'] === 'Ingreso') {
                $entries += $quantity; $stock += $quantity; $warehouses[$warehouseId] += $quantity;
                $value += $quantity * (float) $event['unit_cost'];
            } else {
                $outputs += $quantity; $stock -= $quantity; $warehouses[$warehouseId] -= $quantity;
                if ($stock < -self::EPSILON || $warehouses[$warehouseId] < -self::EPSILON) {
                    $observations[] = 'La secuencia produce saldo negativo el '.$event['date'].' en la bodega #'.$warehouseId.'.';
                }
                $average = ($stock + $quantity) > self::EPSILON ? $value / ($stock + $quantity) : 0;
                $value = max(0, $value - ($quantity * $average));
            }
            $event['balance'] = round($warehouses[$warehouseId], 2);
        }
        unset($event);
        return array('warehouses' => $warehouses, 'entries' => $entries, 'outputs' => $outputs,
            'total' => $stock, 'average_cost' => $stock > self::EPSILON ? $value / $stock : 0,
            'observations' => $observations);
    }

    private function event($date, $order, $warehouseId, $direction, $quantity, $unitCost, $type, $reference, $origin)
    {
        return array('date' => $date, 'order' => $order, 'warehouse_id' => (int) $warehouseId,
            'direction' => $direction > 0 ? 'Ingreso' : 'Salida', 'signed_quantity' => $direction * $quantity,
            'quantity' => round($quantity, 2), 'unit_cost' => round($unitCost, 2),
            'type' => $type ?: ($direction > 0 ? 'INGRESO' : 'EGRESO'),
            'reference' => $reference, 'origin' => $origin, 'balance' => 0);
    }

    private function modernLegacyType($type)
    {
        $type = strtoupper(trim((string) $type));
        if ($type === 'COMP') return 'COMP';
        if ($type === 'VEN') return 'VENTA';
        return null;
    }

    private function sourceKey($legacyType, $reference, $warehouseId)
    {
        if (!in_array($legacyType, array('COMP', 'VENTA'), true) || !$reference) return null;
        return $legacyType.'|'.$reference.'|'.(int) $warehouseId;
    }

    private function legacyLabel($type, $transactionId)
    {
        if ($type) return $type;
        if ($transactionId === 1) return 'SALDO INICIAL';
        return $transactionId === 3 ? 'EGRESO / MERMA' : 'INGRESO / AJUSTE';
    }
}
