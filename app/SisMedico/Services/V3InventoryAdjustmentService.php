<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\Globales;
use Sis_medico\v3\contable\ProductCategoryAccountTemplate;

/**
 * Registra ingresos o egresos por ajuste desde Inventario V3.
 *
 * El movimiento se conserva en los dos contratos contables vigentes
 * (ct_inv_* y ct_kardex/ct_inventario) y, cuando el producto tiene una
 * relacion hospitalaria univoca, tambien en el Kardex clinico por lote.
 */
class V3InventoryAdjustmentService
{
    private $warehouseService;
    private $clinicalInventory;
    private $accountingPosting;
    private $accountFallbackCache = array();
    private $categoryCache = array();

    public function __construct(
        CompanyPrimaryInventoryWarehouseService $warehouseService,
        ClinicalInventoryEntryService $clinicalInventory,
        V3AccountingPostingService $accountingPosting
    ) {
        $this->warehouseService = $warehouseService;
        $this->clinicalInventory = $clinicalInventory;
        $this->accountingPosting = $accountingPosting;
    }

    public function createEntry($companyId, array $input, $userId, $ip)
    {
        return DB::transaction(function () use ($companyId, $input, $userId, $ip) {
            $company = DB::table('empresa')->where('id', $companyId)->lockForUpdate()->first();
            if (!$company) throw new RuntimeException('Seleccione una empresa activa antes de registrar el ajuste.');
            if ((int) $company->inventario !== 1) {
                throw new RuntimeException('Active el control de inventario de la empresa antes de registrar ajustes.');
            }

            $date = $this->validDate(isset($input['fecha']) ? $input['fecha'] : null);
            $direction = strtolower(trim(isset($input['tipo']) ? (string) $input['tipo'] : 'ingreso'));
            if (!in_array($direction, array('ingreso', 'egreso'), true)) {
                throw new RuntimeException('Seleccione si el ajuste es de ingreso o egreso.');
            }
            $isEntry = $direction === 'ingreso';
            $observation = trim(isset($input['observacion']) ? (string) $input['observacion'] : '');
            if ($observation === '') throw new RuntimeException('Ingrese el motivo del ajuste de inventario.');
            if (strlen($observation) > 500) throw new RuntimeException('La observacion admite hasta 500 caracteres.');

            $principal = $this->warehouseService->principal($companyId);
            $warehouseId = (int) $principal->accounting_id;
            $warehouse = DB::table('ct_bodegas')->where('id', $warehouseId)
                ->where('id_empresa', $companyId)->where('estado', 1)->lockForUpdate()->first();
            if (!$warehouse) throw new RuntimeException('La bodega principal de la empresa no esta activa.');

            $lines = $this->lines($companyId, $input);
            $now = date('Y-m-d H:i:s');
            $movementId = DB::table('ct_inv_movimiento')->insertGetId(array(
                'id_transaccion' => $isEntry ? 2 : 3,
                'id_referencia' => 0,
                'detalle' => $observation,
                'tipo' => $isEntry ? 'AJUSTE+' : 'AJUSTE-',
                'pedido' => 'AJUSTE V3',
                'created_at' => $now,
                'updated_at' => $now,
            ));
            $number = ($isEntry ? 'AJI-' : 'AJE-').str_pad((string) $movementId, 8, '0', STR_PAD_LEFT);
            DB::table('ct_inv_movimiento')->where('id', $movementId)->update(array(
                'id_referencia' => $movementId,
                'pedido' => $number,
                'updated_at' => $now,
            ));

            $clinicalLines = array();
            foreach ($lines as $index => $line) {
                $result = $this->recordProduct(
                    $movementId,
                    $number,
                    $date,
                    $companyId,
                    $warehouseId,
                    $line,
                    $userId,
                    $ip,
                    $now,
                    $direction
                );
                $line['adjustment_detail_id'] = $result['detail_id'];
                $line['unit_cost'] = $result['unit_cost'];
                $line['line_number'] = $index + 1;
                $clinicalLines[] = $line;
            }

            $accountingEntryId = $this->postAccountingEntry(
                $companyId, $movementId, $number, $direction, $date,
                $observation, $clinicalLines, $userId, $ip
            );

            $clinical = $this->clinicalInventory->syncV3Adjustment(
                $movementId,
                $number,
                $direction,
                $date,
                $observation,
                $companyId,
                $warehouseId,
                $clinicalLines,
                $userId,
                $ip
            );

            return array(
                'movement_id' => $movementId,
                'number' => $number,
                'warehouse' => $warehouse->nombre,
                'direction' => $direction,
                'products' => count($lines),
                'accounting_entry_id' => $accountingEntryId,
                'clinical_inventory' => $clinical,
            );
        }, 3);
    }

    private function lines($companyId, array $input)
    {
        $productIds = isset($input['producto']) && is_array($input['producto']) ? $input['producto'] : array();
        $quantities = isset($input['cantidad']) && is_array($input['cantidad']) ? $input['cantidad'] : array();
        $costs = isset($input['costo']) && is_array($input['costo']) ? $input['costo'] : array();
        $lots = isset($input['lote']) && is_array($input['lote']) ? $input['lote'] : array();
        $expirations = isset($input['vencimiento']) && is_array($input['vencimiento']) ? $input['vencimiento'] : array();
        $count = count($productIds);
        if ($count < 1) throw new RuntimeException('Agregue al menos un producto al ajuste.');
        if ($count > 100) throw new RuntimeException('El ajuste admite hasta 100 productos.');
        if (count($quantities) !== $count || count($costs) !== $count
            || count($lots) !== $count || count($expirations) !== $count) {
            throw new RuntimeException('Las lineas del ajuste estan incompletas.');
        }

        $ids = array_values(array_unique(array_map('intval', $productIds)));
        sort($ids, SORT_NUMERIC);
        $products = DB::table('ct_productos')->where('id_empresa', $companyId)
            ->where('estado_tabla', 1)
            ->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        $linkedProducts = DB::table('ct_productos_insumos as relation')
            ->join('producto as clinical_product', 'clinical_product.id', '=', 'relation.id_insumo')
            ->whereIn('relation.id_producto', $ids)->where('clinical_product.estado', 1)
            ->pluck('relation.id_producto')->map(function ($id) { return (int) $id; })->flip();
        $modernInventory = DB::table('ct_inventario')->where('id_empresa', $companyId)
            ->whereIn('producto_id', $ids)->pluck('producto_id')
            ->map(function ($id) { return (int) $id; })->flip();
        $historicalInventory = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
            ->whereIn('id_producto', $ids)->pluck('id_producto')
            ->map(function ($id) { return (int) $id; })->flip();

        $lines = array();
        $seen = array();
        foreach ($productIds as $index => $productId) {
            $product = $products->get((int) $productId);
            $isPhysical = $product && (
                (int) $product->tipo_producto === 2
                || strtoupper(trim(isset($product->tipo_objeto) ? (string) $product->tipo_objeto : '')) === 'P'
                || $linkedProducts->has((int) $product->id)
                || $modernInventory->has((int) $product->id)
                || $historicalInventory->has((int) $product->id)
            );
            if (!$isPhysical) throw new RuntimeException('Uno de los productos no existe, no esta activo o no es inventariable.');
            $quantity = round((float) $quantities[$index], 4);
            $cost = round((float) $costs[$index], 4);
            $lot = trim((string) $lots[$index]);
            $expiration = $this->validDate($expirations[$index], 'vencimiento');
            if ($quantity <= 0) throw new RuntimeException('La cantidad de '.$product->nombre.' debe ser mayor a cero.');
            if ($cost < 0) throw new RuntimeException('El costo de '.$product->nombre.' no puede ser negativo.');
            if ($lot === '') throw new RuntimeException('Ingrese el lote de '.$product->nombre.'.');
            if (strlen($lot) > 255) throw new RuntimeException('El lote de '.$product->nombre.' admite hasta 255 caracteres.');
            $key = $product->id.'|'.strtoupper($lot).'|'.$expiration;
            if (isset($seen[$key])) throw new RuntimeException('El producto '.$product->nombre.' tiene el mismo lote repetido.');
            $seen[$key] = true;
            $lines[] = array(
                'product' => $product,
                'quantity' => $quantity,
                'unit_cost' => $cost,
                'lot' => $lot,
                'expiration' => $expiration,
            );
        }
        return $lines;
    }

    private function recordProduct($movementId, $number, $date, $companyId, $warehouseId, array $line, $userId, $ip, $now, $direction)
    {
        $product = $line['product'];
        $quantity = $line['quantity'];
        $isEntry = $direction === 'ingreso';

        $historicalRows = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
            ->where('id_producto', $product->id)->orderBy('id')->lockForUpdate()->get();
        if ($historicalRows->count() > 1) throw new RuntimeException('Existen saldos historicos duplicados para '.$product->nombre.'.');
        $historical = $historicalRows->first();
        if (!$historical) {
            $historicalId = DB::table('ct_inv_interno')->insertGetId(array(
                'id_producto' => $product->id,
                'stock' => 0,
                'cantidad' => 0,
                'costo' => 0,
                'id_empresa' => $companyId,
                'created_at' => $now,
                'updated_at' => $now,
            ));
            $historical = DB::table('ct_inv_interno')->where('id', $historicalId)->lockForUpdate()->first();
        }
        $oldHistoricalStock = (float) $historical->stock;
        $oldHistoricalCost = (float) $historical->costo;
        if (!$isEntry && $oldHistoricalStock + 0.0001 < $quantity) {
            throw new RuntimeException('Existencia histórica insuficiente para egresar '.$product->nombre.'.');
        }

        $inventoryRows = DB::table('ct_inventario')->where('id_empresa', $companyId)
            ->where('producto_id', $product->id)->where('bodega_id', $warehouseId)
            ->orderBy('id')->lockForUpdate()->get();
        if ($inventoryRows->count() > 1) throw new RuntimeException('Existen existencias duplicadas para '.$product->nombre.' en la bodega principal.');
        $inventory = $inventoryRows->first();
        $previous = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('producto_id', $product->id)->where('bodega_id', $warehouseId)
            ->orderBy('id', 'desc')->lockForUpdate()->first();
        $oldQuantity = $inventory ? (float) $inventory->existencia : ($previous ? (float) $previous->saldo_cantidad : 0);
        $oldUnitCost = $previous ? (float) $previous->saldo_valor_unitario : $oldHistoricalCost;
        if (!$isEntry && $oldQuantity + 0.0001 < $quantity) {
            throw new RuntimeException('Existencia insuficiente en la bodega principal para egresar '.$product->nombre.'.');
        }

        $unitCost = $isEntry ? (float) $line['unit_cost'] : ($oldUnitCost > 0 ? $oldUnitCost : $oldHistoricalCost);
        $lineTotal = round($quantity * $unitCost, 2);
        $newHistoricalStock = round($oldHistoricalStock + ($isEntry ? $quantity : -$quantity), 4);
        $newHistoricalCost = $isEntry && $newHistoricalStock > 0
            ? round((($oldHistoricalStock * $oldHistoricalCost) + $lineTotal) / $newHistoricalStock, 2)
            : $oldHistoricalCost;
        DB::table('ct_inv_interno')->where('id', $historical->id)->update(array(
            'stock' => $newHistoricalStock,
            'cantidad' => $newHistoricalStock,
            'costo' => $newHistoricalCost,
            'updated_at' => $now,
        ));
        $historicalKardexId = DB::table('ct_inv_kardex')->insertGetId(array(
            'id_bodega' => $warehouseId,
            'id_movimiento' => $movementId,
            'id_producto' => $product->id,
            'serie' => $line['lot'],
            'id_transaccion' => $isEntry ? 2 : 3,
            'cantidad' => $quantity,
            'precio' => $unitCost,
            'id_inv' => $historical->id,
            'estado' => 1,
            'fecha' => $date,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
            'id_modificacion' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        if ($isEntry) {
            $this->recordHistoricalCost($companyId, $product->id, $oldHistoricalCost, $newHistoricalCost, $number, $now);
        }

        $newQuantity = round($oldQuantity + ($isEntry ? $quantity : -$quantity), 4);
        $newValue = $isEntry
            ? round(($oldQuantity * $oldUnitCost) + $lineTotal, 2)
            : round(max(0, $newQuantity * $unitCost), 2);
        $average = $newQuantity > 0 ? ($isEntry ? round($newValue / $newQuantity, 2) : $unitCost) : $unitCost;
        $modernKardexId = DB::table('ct_kardex')->insertGetId(array(
            'fecha' => $date,
            'id_movimiento' => $movementId,
            'movimiento' => $isEntry ? 1 : 2,
            'tipo' => $isEntry ? 'AJUSTE+' : 'AJUSTE-',
            'numero' => $number,
            'producto_id' => $product->id,
            'cantidad' => $quantity,
            'valor_unitario' => $unitCost,
            'total' => $lineTotal,
            'saldo_cantidad' => $newQuantity,
            'saldo_valor_unitario' => $average,
            'saldo_total' => $newValue,
            'bodega_id' => $warehouseId,
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
                'producto_id' => $product->id,
                'bodega_id' => $warehouseId,
                'id_empresa' => $companyId,
                'id_usuariocrea' => $userId,
                'ip_creacion' => $ip,
                'created_at' => $now,
            )));
        }

        $detailId = DB::table('v3_inventory_adjustment_details')->insertGetId(array(
            'movement_id' => $movementId,
            'historical_kardex_id' => $historicalKardexId,
            'modern_kardex_id' => $modernKardexId,
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'lot' => $line['lot'],
            'expiration_date' => $line['expiration'],
            'company_id' => $companyId,
            'created_by' => $userId,
            'updated_by' => $userId,
            'creation_ip' => $ip,
            'modification_ip' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        return array('detail_id' => $detailId, 'unit_cost' => $unitCost);
    }

    private function postAccountingEntry(
        $companyId,
        $movementId,
        $number,
        $direction,
        $date,
        $observation,
        array $lines,
        $userId,
        $ip
    ) {
        if (!Schema::hasTable('v3_product_category_account_templates')) {
            throw new RuntimeException('CONFIG_CONTABLE: Falta ejecutar la migración de plantillas contables por categoría.');
        }

        $templates = DB::table('v3_product_category_account_templates')
            ->where('id_empresa', $companyId)->where('estado', 1)->get()->keyBy(function ($row) {
                return (string) $row->categoria;
            });
        $grouped = array();
        $total = 0;
        foreach ($lines as $line) {
            $product = $line['product'];
            $category = $this->accountingCategory($product);
            $template = $category !== '' ? $templates->get($category) : null;
            $inventoryId = $template ? trim((string) $template->cuenta_inventario) : '';
            $expenseId = $template ? trim((string) $template->cuenta_gasto) : '';
            if ($expenseId === '' && isset($product->cta_gastos)) {
                $expenseId = trim((string) $product->cta_gastos);
            }
            if ($expenseId === '' && isset($product->cta_costos)) {
                $expenseId = trim((string) $product->cta_costos);
            }

            $inventoryAccount = $inventoryId !== ''
                ? ProductCategoryAccountTemplate::account($companyId, $inventoryId)
                : Globales::cuentaVenta(Globales::VENTA_INVENTARIO, $companyId);
            $adjustmentAccount = $expenseId !== ''
                ? ProductCategoryAccountTemplate::account($companyId, $expenseId)
                : Globales::cuentaVenta(Globales::VENTA_COSTO, $companyId);
            if (!$inventoryAccount) {
                $inventoryAccount = $this->companyAccountFor($companyId, 'inventory');
            }
            if (!$adjustmentAccount) {
                $adjustmentAccount = $this->companyAccountFor($companyId, 'adjustment');
            }
            $categoryLabel = ProductCategoryAccountTemplate::categoryLabel($category);
            if (!$inventoryAccount) {
                throw new RuntimeException('CONFIG_CONTABLE: Configure la cuenta de inventario para la categoría '.$categoryLabel.'.');
            }
            if (!$adjustmentAccount) {
                throw new RuntimeException('CONFIG_CONTABLE: Configure la cuenta de gastos o ajustes para la categoría '.$categoryLabel.'.');
            }
            if ((string) $inventoryAccount->id_plan === (string) $adjustmentAccount->id_plan) {
                throw new RuntimeException('CONFIG_CONTABLE: Las cuentas de inventario y ajuste deben ser diferentes para la categoría '.$categoryLabel.'.');
            }

            $amount = round((float) $line['quantity'] * (float) $line['unit_cost'], 2);
            if ($amount <= 0) {
                throw new RuntimeException('CONFIG_CONTABLE: El producto '.$product->nombre.' no tiene un costo válido para contabilizar el ajuste.');
            }
            $inventorySide = $direction === 'ingreso' ? 'debe' : 'haber';
            $adjustmentSide = $direction === 'ingreso' ? 'haber' : 'debe';
            $this->addAccountingAmount($grouped, $inventoryAccount, $inventorySide, $amount, 'Inventario · '.$categoryLabel);
            $this->addAccountingAmount($grouped, $adjustmentAccount, $adjustmentSide, $amount, 'Ajuste de inventario · '.$categoryLabel);
            $total += $amount;
        }

        $total = round($total, 2);
        if ($total <= 0) throw new RuntimeException('CONFIG_CONTABLE: El ajuste no tiene un valor contable válido.');
        $timestamp = $date.' '.date('H:i:s');
        $journalLines = array();
        foreach ($grouped as $line) {
            $journalLines[] = array(
                'id_plan_cuenta' => $line['account']->id_plan,
                'descripcion' => substr($line['description'].' · '.$number, 0, 255),
                'fecha' => $timestamp,
                'debe' => round($line['debe'], 2),
                'haber' => round($line['haber'], 2),
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            );
        }

        return $this->accountingPosting->post(array(
            'id_empresa' => $companyId,
            'fecha_asiento' => $timestamp,
            'observacion' => substr('AJUSTE DE '.strtoupper($direction).' DE INVENTARIO '.$number.': '.$observation, 0, 255),
            'fact_numero' => $number,
            'valor' => $total,
            'modulo' => 'AJUSTE_INVENTARIO',
            'aparece_sri' => 0,
            'especial' => 1,
            'tipo' => 1,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
        ), $journalLines, array(
            'modulo' => 'INVENTARIO_V3',
            'tipo_origen' => 'AJUSTE_INVENTARIO',
            'id_origen' => (string) $movementId,
        ));
    }

    /**
     * Los productos hospitalarios heredados se crearon antes de que grupo
     * fuera obligatorio. Una relación con Farmacia o el prefijo MED permite
     * utilizar la plantilla de Medicamentos sin alterar silenciosamente la
     * ficha maestra del producto.
     */
    private function accountingCategory($product)
    {
        $category = trim((string) $product->grupo);
        if ($category !== '') return $category;
        $cacheKey = (string) $product->id;
        if (array_key_exists($cacheKey, $this->categoryCache)) return $this->categoryCache[$cacheKey];

        $clinical = DB::table('ct_productos_insumos as relation')
            ->join('producto as clinical_product', 'clinical_product.id', '=', 'relation.id_insumo')
            ->where('relation.id_producto', $product->id)
            ->where('clinical_product.estado', 1)
            ->orderBy('clinical_product.id')
            ->first(array('clinical_product.farmacia'));
        $code = strtoupper(trim((string) $product->codigo));
        if (($clinical && (int) $clinical->farmacia === 1) || strpos($code, 'MED-') === 0) {
            return $this->categoryCache[$cacheKey] = '2';
        }
        if ($clinical) return $this->categoryCache[$cacheKey] = '1';
        return $this->categoryCache[$cacheKey] = '';
    }

    /**
     * Respaldo para empresas antiguas cuya fila ct_globales existe pero no
     * tiene cuenta asignada. Solo considera cuentas de detalle activas de la
     * empresa y exige una coincidencia semántica; nunca toma una cuenta al
     * azar ni cruza información entre empresas.
     */
    private function companyAccountFor($companyId, $purpose)
    {
        $cacheKey = (string) $companyId.'|'.$purpose;
        if (array_key_exists($cacheKey, $this->accountFallbackCache)) {
            return $this->accountFallbackCache[$cacheKey];
        }
        $rows = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', $companyId)
            ->where('pe.estado', 2)
            ->orderBy('pe.plan')
            ->get(array('pe.id_plan', 'pe.plan', 'pe.nombre as company_name', 'p.nombre as account_name'));

        $best = null;
        $bestScore = 0;
        foreach ($rows as $row) {
            $plan = trim((string) $row->plan);
            $name = $this->searchableAccountName($row->company_name.' '.$row->account_name);
            $score = $purpose === 'inventory'
                ? $this->inventoryAccountScore($plan, $name)
                : $this->adjustmentAccountScore($plan, $name);
            if ($score > $bestScore) {
                $best = $row;
                $bestScore = $score;
            }
        }
        if (!$best || $bestScore < 1) return $this->accountFallbackCache[$cacheKey] = null;
        return $this->accountFallbackCache[$cacheKey] = (object) array(
            'id_plan' => $best->id_plan,
            'plan' => $best->plan,
            'nombre' => trim((string) ($best->company_name ?: $best->account_name)),
        );
    }

    private function inventoryAccountScore($plan, $name)
    {
        if (strpos($plan, '1') !== 0) return 0;
        if (strpos($name, 'inventar') !== false) return 100;
        if (strpos($name, 'mercader') !== false) return 80;
        if (strpos($name, 'existencia') !== false) return 70;
        if (strpos($name, 'producto terminado') !== false) return 60;
        return 0;
    }

    private function adjustmentAccountScore($plan, $name)
    {
        if (strpos($plan, '5') !== 0 && strpos($plan, '6') !== 0) return 0;
        if (strpos($name, 'ajuste') !== false && strpos($name, 'inventar') !== false) return 100;
        if (strpos($name, 'diferencia') !== false && strpos($name, 'inventar') !== false) return 90;
        if (strpos($name, 'costo') !== false && strpos($name, 'venta') !== false) return 70;
        if (strpos($name, 'gasto') !== false && strpos($name, 'inventar') !== false) return 60;
        return 0;
    }

    private function searchableAccountName($value)
    {
        $value = strtolower(trim((string) $value));
        return strtr($value, array(
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        ));
    }

    private function addAccountingAmount(array &$grouped, $account, $side, $amount, $description)
    {
        $key = $side.'|'.(string) $account->id_plan;
        if (!isset($grouped[$key])) {
            $grouped[$key] = array(
                'account' => $account,
                'description' => $description,
                'debe' => 0,
                'haber' => 0,
            );
        }
        $grouped[$key][$side] = round($grouped[$key][$side] + $amount, 2);
    }

    private function recordHistoricalCost($companyId, $productId, $oldCost, $newCost, $number, $now)
    {
        $costRows = DB::table('ct_inv_costos')->where('id_empresa', $companyId)
            ->where('id_producto', $productId)->orderBy('id')->lockForUpdate()->get();
        if ($costRows->count() > 1) throw new RuntimeException('Existen costos historicos duplicados para el producto ajustado.');
        $cost = $costRows->first();
        if ($cost) {
            DB::table('ct_inv_costos')->where('id', $cost->id)->update(array(
                'costo_anterior' => $oldCost,
                'costo_promedio' => $newCost,
                'updated_at' => $now,
            ));
            $costId = $cost->id;
        } else {
            $costId = DB::table('ct_inv_costos')->insertGetId(array(
                'id_producto' => $productId,
                'costo_promedio' => $newCost,
                'costo_anterior' => $oldCost,
                'id_empresa' => $companyId,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        }
        DB::table('ct_inv_costos_detalle')->insert(array(
            'id_costo' => $costId,
            'fecha' => $now,
            'estado' => 1,
            'observaciones' => 'AJUSTE DE ENTRADA '.$number,
            'costo' => $newCost,
            'costo_ant' => $oldCost,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    private function validDate($value, $label = 'fecha')
    {
        $value = trim((string) $value);
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new RuntimeException('La '.$label.' del ajuste no es valida.');
        }
        return $value;
    }
}
