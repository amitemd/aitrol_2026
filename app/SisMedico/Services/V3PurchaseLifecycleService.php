<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;
use Sis_medico\Inventario;
use Sis_medico\v3\contable\BodegasContable;
use Sis_medico\v3\contable\Globales;
use Sis_medico\v3\contable\LogConfig;

class V3PurchaseLifecycleService
{
    const PAYMENT_PENDING = '20';

    private $posting;
    private $clinicalInventory;

    public function __construct(
        V3AccountingPostingService $posting,
        ClinicalInventoryEntryService $clinicalInventory = null
    )
    {
        $this->posting = $posting;
        $this->clinicalInventory = $clinicalInventory;
    }

    public function ensureSchema()
    {
        foreach (array('estado_pago', 'saldo_pendiente', 'id_caja_banco_pago', 'id_asiento_pago') as $column) {
            if (!Schema::hasColumn('ct_compras', $column)) {
                throw new RuntimeException('Falta aplicar la migración del ciclo de compras y pagos. Ejecute php artisan migrate.');
            }
        }
    }

    public function paymentContext($request, $companyId)
    {
        $method = trim((string) $request->input('forma_pago'));
        if ($method === '') throw new InvalidArgumentException('Seleccione la forma de pago de la compra.');
        // 20 es la forma configurada por el sistema como "Pendiente de pago".
        // Se conserva 00 únicamente para documentos creados durante la transición.
        if ($method === self::PAYMENT_PENDING || $method === '00') {
            return array('method' => $method, 'status' => 'PENDIENTE', 'balance' => round((float) $request->input('total_pagar'), 2), 'cash' => null);
        }

        $cashId = $request->input('id_caja_banco_pago');
        $cash = DB::table('ct_caja_banco')->where('id', $cashId)->where('id_empresa', $companyId)
            ->where('estado', 1)->lockForUpdate()->first();
        if (!$cash) throw new InvalidArgumentException('Para registrar una compra pagada seleccione la cuenta de salida correspondiente.');
        $expectedClass = $method === '01' ? 2 : 1;
        if ((int) $cash->clase !== $expectedClass) {
            throw new InvalidArgumentException($expectedClass === 2
                ? 'El pago en efectivo debe salir de una cuenta configurada como Caja.'
                : 'Esta forma de pago debe salir de una cuenta configurada como Banco.');
        }
        $this->assertCompanyAccount($cash->cuenta_mayor, $companyId, 'la caja o banco de pago');
        return array('method' => $method, 'status' => 'PAGADA', 'balance' => 0, 'cash' => $cash);
    }

    public function postPurchase($request, $provider, array $header, $companyId, $ip)
    {
        $total = round((float) $request->input('total_pagar'), 2);
        $vat = round((float) $request->input('iva') + (float) $request->input('iva_mc'), 2);
        $base = round($total - $vat, 2);
        if ($total <= 0 || $base < 0) throw new InvalidArgumentException('El total de la compra no es válido para contabilizar.');

        $inventoryAccount = LogConfig::busqueda('1.01.03.01.02');
        $payableAccount = !empty($provider->id_cuentas) ? $provider->id_cuentas : LogConfig::busqueda('2.01.03.01.01');
        $this->assertCompanyAccount($inventoryAccount, $companyId, 'inventario/compras');
        $this->assertCompanyAccount($payableAccount, $companyId, 'proveedores por pagar');

        $lines = array();
        if ($base > 0) $lines[] = $this->line($inventoryAccount, 'Compra de bienes y servicios', $header['fecha_asiento'], $base, 0, $header, $ip);
        if ($vat > 0) {
            $vatConfig = Globales::where('id_modulo', 1)->where('id_empresa', $companyId)->first();
            $vatAccount = $vatConfig ? $vatConfig->debe : null;
            $this->assertCompanyAccount($vatAccount, $companyId, 'IVA pagado en compras');
            $lines[] = $this->line($vatAccount, 'IVA pagado en compras', $header['fecha_asiento'], $vat, 0, $header, $ip);
        }
        $lines[] = $this->line($payableAccount, 'Cuenta por pagar al proveedor '.$provider->razonsocial, $header['fecha_asiento'], 0, $total, $header, $ip);

        $header['modulo'] = 'COMPRAS';
        $sourceId = $companyId.'|'.$provider->id.'|'.$header['sucursal'].'-'.$header['punto_emision'].'-'.$header['fact_numero'];
        return array(
            'entry' => $this->posting->post($header, $lines, array('modulo' => 'COMPRAS', 'tipo_origen' => 'COMPRA', 'id_origen' => $sourceId)),
            'payable_account' => $payableAccount,
        );
    }

    public function postExpense($request, $provider, array $header, $companyId, $ip)
    {
        $codes = (array) $request->input('codigo', []);
        $amounts = (array) $request->input('totald', []);
        if (!$codes || count($codes) !== count($amounts)) throw new InvalidArgumentException('Ingrese las cuentas y valores del gasto.');
        $lines = []; $base = 0;
        foreach ($codes as $index => $code) {
            $account = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)
                ->where('plan', $code)->where('estado', 2)->first();
            if (!$account) throw new InvalidArgumentException('La cuenta de gasto '.$code.' no está activa para movimientos en esta empresa.');
            $amount = round((float) ($amounts[$index] ?? -1), 2);
            if ($amount < 0) throw new InvalidArgumentException('El importe del gasto no puede ser negativo.');
            $base += $amount;
            if ($amount > 0) $lines[] = $this->line($account->id_plan, $account->nombre, $header['fecha_asiento'], $amount, 0, $header, $ip);
        }
        $vat = round((float) $request->input('iva') + (float) $request->input('iva_mc'), 2);
        $total = round((float) $request->input('total_pagar'), 2);
        if ($vat < 0 || $total <= 0 || abs(round($base + $vat, 2) - $total) > 0.001) {
            throw new InvalidArgumentException('El detalle del gasto más IVA no coincide con el total a pagar.');
        }
        if ($vat > 0) {
            $config = Globales::where('id_modulo', 1)->where('id_empresa', $companyId)->first();
            $account = $config ? $config->debe : null;
            $this->assertExpenseAccount($account, $companyId, 'IVA de compras');
            $lines[] = $this->line($account, 'IVA de compras', $header['fecha_asiento'], $vat, 0, $header, $ip);
        }
        $payable = $provider->id_cuentas;
        $this->assertExpenseAccount($payable, $companyId, 'cuenta por pagar del proveedor');
        $lines[] = $this->line($payable, 'Cuenta por pagar: '.$provider->razonsocial, $header['fecha_asiento'], 0, $total, $header, $ip);
        $header['modulo'] = 'COMPRAS';
        $entry = $this->posting->post($header, $lines, [
            'modulo' => 'COMPRAS', 'tipo_origen' => 'COMPRA',
            'id_origen' => $companyId.'|'.$provider->id.'|'.$header['sucursal'].'-'.$header['punto_emision'].'-'.$header['fact_numero'],
        ]);
        return ['entry' => $entry, 'payable_account' => $payable];
    }

    private function assertExpenseAccount($account, $companyId, $label)
    {
        if (!$account || !DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)
            ->where('id_plan', $account)->where('estado', 2)->exists()) {
            throw new InvalidArgumentException('Configure la '.$label.' como cuenta de movimiento activa de esta empresa.');
        }
    }

    public function cancelExpense($id, $companyId, $reason, $userId, $ip)
    {
        if (trim((string) $reason) === '') throw new InvalidArgumentException('Ingrese el motivo de anulación.');
        return DB::transaction(function () use ($id, $companyId, $reason, $userId, $ip) {
            $purchase = DB::table('ct_compras')->where('id', $id)->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$purchase) throw new InvalidArgumentException('El gasto no pertenece a esta empresa o no existe.');
            $isLiquidation = isset($purchase->tipo_comprobante) && (string) $purchase->tipo_comprobante === '03';
            if ((int) $purchase->tipo !== 2 && !$isLiquidation) {
                throw new InvalidArgumentException('El documento no corresponde a un gasto o liquidación de compra.');
            }
            $documentLabel = $isLiquidation ? 'La liquidación de compra' : 'El gasto';
            if ((int) $purchase->estado === 0) {
                return ['status' => 'success', 'msj' => $documentLabel.' ya estaba anulado; no se generó otro reverso.'];
            }
            // Dependent documents must be reversed through their own workflow first.
            foreach (['ct_retenciones' => 'id_compra', 'ct_credito_acreedores' => 'id_compra',
                'ct_detalle_comprobante_egreso' => 'id_compra', 'ct_debito_bancario_detalle' => 'id_compra',
                'ct_detalle_cruce' => 'id_factura'] as $table => $foreign) {
                if (!Schema::hasTable($table)) continue;
                $query = DB::table($table)->where($foreign, $id);
                if (Schema::hasColumn($table, 'estado')) $query->where('estado', '<>', 0);
                if ($query->exists()) throw new RuntimeException($documentLabel.' tiene pagos, retenciones, notas de crédito o cruces asociados. Reviértalos antes de anular el documento.');
            }
            if (!empty($purchase->id_asiento_pago) || ($purchase->estado_pago ?? '') === 'PAGADA') {
                throw new RuntimeException('Debe reversar el pago antes de anular '.($isLiquidation ? 'la liquidación de compra.' : 'el gasto.'));
            }
            $original = DB::table('ct_asientos_cabecera')->where('id', $purchase->id_asiento_cabecera)
                ->where('id_empresa', $companyId)->where('estado', 1)->lockForUpdate()->first();
            if (!$original) throw new RuntimeException('No existe el asiento activo del gasto en esta empresa.');
            app(V3AccountingIntegrityService::class)->assertBalancedEntry($original->id);
            $date = date('Y-m-d');
            $header = ['id_empresa' => $companyId, 'fecha_asiento' => $date,
                'observacion' => ($isLiquidation ? 'Reverso liquidación de compra #' : 'Reverso gasto #').$id.': '.trim($reason),
                'fact_numero' => $original->fact_numero,
                'valor' => $original->valor, 'sucursal' => $original->sucursal, 'punto_emision' => $original->punto_emision,
                'modulo' => 'COMPRAS', 'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip];
            $lines = [];
            foreach (DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $original->id)->where('estado', 1)->get() as $line) {
                $lines[] = $this->line($line->id_plan_cuenta, $line->descripcion, $date, $line->haber, $line->debe, $header, $ip);
            }
            $reverseType = $isLiquidation ? 'ANULACION_LIQUIDACION_COMPRA' : 'ANULACION_GASTO';
            $reverse = $this->posting->post($header, $lines, ['modulo' => 'COMPRAS', 'tipo_origen' => $reverseType, 'id_origen' => (string) $id]);
            app(V3AccountingIntegrityService::class)->assertBalancedEntry($reverse);
            $purchaseUpdate = ['estado' => 0, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip];
            if (Schema::hasColumn('ct_compras', 'estado_pago')) $purchaseUpdate['estado_pago'] = 'ANULADA';
            if (Schema::hasColumn('ct_compras', 'saldo_pendiente')) $purchaseUpdate['saldo_pendiente'] = 0;
            if (Schema::hasColumn('ct_compras', 'id_asiento_reverso')) $purchaseUpdate['id_asiento_reverso'] = $reverse;
            if (Schema::hasColumn('ct_compras', 'motivo_anulacion')) $purchaseUpdate['motivo_anulacion'] = trim($reason);
            if (Schema::hasColumn('ct_compras', 'fecha_anulacion')) $purchaseUpdate['fecha_anulacion'] = date('Y-m-d H:i:s');
            if (Schema::hasColumn('ct_compras', 'updated_at')) $purchaseUpdate['updated_at'] = date('Y-m-d H:i:s');
            DB::table('ct_compras')->where('id', $id)->where('id_empresa', $companyId)
                ->update($purchaseUpdate);
            return [
                'status' => 'success',
                'msj' => ($isLiquidation ? 'Liquidación de compra anulada' : 'Gasto anulado').' y asiento de reverso cuadrado correctamente.',
                'id_asiento_reverso' => $reverse,
            ];
        }, 3);
    }

    public function postPayment($purchaseId, $request, $provider, array $header, array $payment, $payableAccount, $companyId, $ip)
    {
        if ($payment['status'] !== 'PAGADA') return null;
        $total = round((float) $request->input('total_pagar'), 2);
        $lines = array(
            $this->line($payableAccount, 'Cancelación de cuenta por pagar '.$provider->razonsocial, $header['fecha_asiento'], $total, 0, $header, $ip),
            $this->line($payment['cash']->cuenta_mayor, 'Pago de compra '.$header['fact_numero'], $header['fecha_asiento'], 0, $total, $header, $ip),
        );
        $paymentHeader = $header;
        $paymentHeader['observacion'] = 'Pago de compra: '.$header['observacion'];
        $paymentHeader['modulo'] = 'PAGOS_COMPRA';
        return $this->posting->post($paymentHeader, $lines, array(
            'modulo' => 'PAGOS_COMPRA', 'tipo_origen' => 'PAGO_COMPRA', 'id_origen' => (string) $purchaseId,
        ));
    }

    public function moveInventory($purchaseId, $companyId, $userId, $ip)
    {
        // La empresa solo aporta configuracion. No se bloquea la fila global
        // para permitir compras paralelas; los candados efectivos se toman en
        // la compra y en los productos afectados, siempre en orden estable.
        $company = DB::table('empresa')->where('id', $companyId)->first();
        if (!$company) throw new RuntimeException('No existe la empresa del movimiento de inventario.');
        $purchase = DB::table('ct_compras')->where('id', $purchaseId)
            ->where('id_empresa', $companyId)->lockForUpdate()->first();
        if (!$purchase) throw new RuntimeException('La compra no existe o no pertenece a la empresa.');
        $modernMovementRows = DB::table('ct_kardex')->where('id_empresa', $companyId)
            ->where('tipo', 'COMP')->where('id_movimiento', $purchaseId)
            ->orderBy('id')->lockForUpdate()->get();
        $movementExists = count($modernMovementRows) > 0;
        $details = DB::table('ct_detalle_compra')->where('id_ct_compras', $purchaseId)->where('estado', 1)->get();
        if ($details->isEmpty()) throw new RuntimeException('La compra no contiene detalles para mover inventario.');

        $codes = array_values(array_unique($details->pluck('codigo')->map(function ($code) {
            return (string) $code;
        })->all()));
        sort($codes, SORT_STRING);
        // Cargar y bloquear los maestros en un orden estable evita N+1 y reduce
        // la posibilidad de interbloqueos entre compras concurrentes.
        $products = DB::table('ct_productos')->where('id_empresa', $companyId)
            ->whereIn('codigo', $codes)->orderBy('codigo')->lockForUpdate()->get()->keyBy('codigo');
        // Repara catalogos sincronizados por versiones antiguas que marcaron
        // medicamentos como servicio. Solo se corrige una relacion directa:
        // mismo codigo contable y hospitalario, no paquetes con varios insumos.
        $directInventoryProductIds = collect();
        if (Schema::hasTable('ct_productos_insumos') && Schema::hasTable('producto') && !$products->isEmpty()) {
            $directInventoryProductIds = DB::table('ct_productos_insumos as relation')
                ->join('ct_productos as accounting_product', 'accounting_product.id', '=', 'relation.id_producto')
                ->join('producto as clinical_product', 'clinical_product.id', '=', 'relation.id_insumo')
                ->where('accounting_product.id_empresa', $companyId)
                ->whereIn('accounting_product.id', $products->pluck('id')->all())
                ->whereRaw('UPPER(TRIM(accounting_product.codigo)) = UPPER(TRIM(clinical_product.codigo))')
                ->pluck('accounting_product.id');
        }
        foreach ($products as $product) {
            if ((int) $product->tipo_producto !== 2 && $directInventoryProductIds->contains($product->id)) {
                DB::table('ct_productos')->where('id', $product->id)->where('id_empresa', $companyId)
                    ->update(array('tipo_producto' => 2, 'id_usuariomod' => $userId,
                        'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s')));
                $product->tipo_producto = 2;
            }
        }
        $warehouses = DB::table('ct_bodegas')->where('id_empresa', $companyId)->where('estado', 1)
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
        $principalId = BodegasContable::principalIdParaEmpresa($companyId);
        $principal = $principalId > 0 ? $warehouses->get($principalId) : null;
        if (!$principal && $warehouses->count() === 1) $principal = $warehouses->first();

        $groups = array();
        $skipped = 0;
        foreach ($details as $detail) {
            $product = $products->get($detail->codigo);
            if (!$product) throw new RuntimeException('No existe el producto '.$detail->codigo.' en la empresa.');
            if ((int) $product->tipo_producto !== 2) { $skipped++; continue; }
            $warehouseId = (int) $detail->bodega;
            if ($warehouseId <= 0 && $principal) {
                $warehouseId = (int) $principal->id;
                DB::table('ct_detalle_compra')->where('id', $detail->id)
                    ->where('id_ct_compras', $purchaseId)->update(array('bodega' => $warehouseId));
            }
            $warehouse = $warehouses->get($warehouseId);
            if (!$warehouse) {
                $mensaje = $warehouses->isEmpty()
                    ? 'No existen bodegas activas para la empresa.'
                    : 'Seleccione una bodega o configure una como principal en Configuraciones > Bodegas.';
                throw new RuntimeException('El producto '.$detail->codigo.' requiere una bodega activa. '.$mensaje);
            }
            $key = $product->id.'|'.$warehouseId;
            if (!isset($groups[$key])) $groups[$key] = array('product' => $product, 'warehouse' => $warehouseId, 'quantity' => 0, 'cost' => 0);
            $groups[$key]['quantity'] += (float) $detail->cantidad;
            $groups[$key]['cost'] += max(0, (float) $detail->extendido - (float) $detail->descuento);
        }

        // Una compra de productos no puede alimentar solo el saldo V3 y omitir
        // el historial compatible con V2. Se valida antes de escribir cualquiera
        // de los dos kardex; la transaccion exterior revierte factura y asientos.
        if (count($groups) > 0 && (int) $company->inventario !== 1) {
            throw new RuntimeException(
                'Active Control de inventario en Configuraciones > Empresa antes de guardar compras con productos. '
                .'La compra no fue registrada para evitar saldos sin historial de Kardex.'
            );
        }

        $productIds = array();
        $inventoryWarehouseIds = array();
        foreach ($groups as $group) {
            $productIds[] = $group['product']->id;
            $inventoryWarehouseIds[] = $group['warehouse'];
        }
        $productIds = array_values(array_unique($productIds));
        $inventoryWarehouseIds = array_values(array_unique($inventoryWarehouseIds));
        sort($productIds, SORT_STRING);
        sort($inventoryWarehouseIds, SORT_STRING);
        $inventories = collect();
        $previousByPair = collect();
        if ($productIds) {
            $inventoryRows = DB::table('ct_inventario')->where('id_empresa', $companyId)
                ->whereIn('producto_id', $productIds)->whereIn('bodega_id', $inventoryWarehouseIds)
                ->orderBy('producto_id')->orderBy('bodega_id')->lockForUpdate()->get();
            $duplicateInventory = $inventoryRows->groupBy(function ($row) {
                return $row->producto_id.'|'.$row->bodega_id;
            })->first(function ($rows) {
                return $rows->count() > 1;
            });
            if ($duplicateInventory) {
                throw new RuntimeException('Existen existencias duplicadas para un producto y bodega; corrija la inconsistencia antes de procesar la compra.');
            }
            $inventories = $inventoryRows->keyBy(function ($row) {
                    return $row->producto_id.'|'.$row->bodega_id;
                });
            $latestIds = DB::table('ct_kardex')->where('id_empresa', $companyId)
                ->whereIn('producto_id', $productIds)->whereIn('bodega_id', $inventoryWarehouseIds)
                ->select(DB::raw('MAX(id) AS id'))->groupBy('producto_id', 'bodega_id')->get()->pluck('id')->all();
            if ($latestIds) {
                $previousByPair = DB::table('ct_kardex')->whereIn('id', $latestIds)
                    ->orderBy('producto_id')->orderBy('bodega_id')->lockForUpdate()->get()->keyBy(function ($row) {
                        return $row->producto_id.'|'.$row->bodega_id;
                    });
            }
        }

        if ($movementExists) {
            $this->assertModernPurchaseKardexMatches($modernMovementRows, $groups);
        }

        // El contrato historico se registra primero. Si cualquier conciliacion
        // falla, la transaccion exterior revierte tambien factura y asientos.
        $legacyResult = 'omitido';
        if (count($groups) > 0) {
            $legacyMovementBefore = DB::table('ct_inv_movimiento')
                ->where('id_referencia', $purchaseId)->where('tipo', 'COMP')
                ->orderBy('id')->lockForUpdate()->first();
            $legacyBeforeRows = DB::table('ct_inv_interno')
                ->where('id_empresa', $companyId)->whereIn('id_producto', $productIds)
                ->orderBy('id_producto')->orderBy('id')->lockForUpdate()->get();
            $duplicateLegacy = $legacyBeforeRows->groupBy('id_producto')->first(function ($rows) {
                return $rows->count() > 1;
            });
            if ($duplicateLegacy) {
                throw new RuntimeException('Existen saldos internos duplicados para un producto de la compra.');
            }
            $legacyBefore = $legacyBeforeRows->keyBy('id_producto');
            $legacyResult = Inventario::build_process('C', $purchaseId, $companyId, 1);
            if ($legacyResult !== 'ok') {
                throw new RuntimeException('No se pudo persistir el movimiento compatible con inventario V2.');
            }
            $this->assertLegacyPurchaseRecorded(
                $companyId,
                $purchaseId,
                $groups,
                $legacyBefore,
                !$legacyMovementBefore
            );
        }

        foreach ($groups as $group) {
            if ($group['quantity'] <= 0) throw new RuntimeException('La cantidad comprada debe ser mayor a cero.');
            $pair = $group['product']->id.'|'.$group['warehouse'];
            $inventory = $inventories->get($pair);
            $previous = $previousByPair->get($pair);
            $now = date('Y-m-d H:i:s');

            if ($movementExists) {
                $pairProcessed = DB::table('ct_kardex')->where('id_empresa', $companyId)
                    ->where('tipo', 'COMP')->where('id_movimiento', $purchaseId)
                    ->where('producto_id', $group['product']->id)->where('bodega_id', $group['warehouse'])->exists();
                if (!$pairProcessed || !$previous) {
                    throw new RuntimeException('El movimiento de inventario de la compra está incompleto y requiere revisión antes de recalcularlo.');
                }
                $audit = array('existencia' => round((float) $previous->saldo_cantidad, 4),
                    'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now);
                if ($inventory) {
                    DB::table('ct_inventario')->where('id', $inventory->id)->update($audit);
                } else {
                    DB::table('ct_inventario')->insert(array_merge($audit, array(
                        'producto_id' => $group['product']->id, 'bodega_id' => $group['warehouse'],
                        'id_empresa' => $companyId, 'id_usuariocrea' => $userId,
                        'ip_creacion' => $ip, 'created_at' => $now,
                    )));
                }
                continue;
            }

            $oldQuantity = $inventory ? (float) $inventory->existencia : ($previous ? (float) $previous->saldo_cantidad : 0);
            $oldUnitCost = $previous ? (float) $previous->saldo_valor_unitario : (float) $group['product']->ultima_compra;
            $oldTotal = round($oldQuantity * $oldUnitCost, 2);
            $newQuantity = round($oldQuantity + $group['quantity'], 4);
            $newTotal = round($oldTotal + $group['cost'], 2);
            $average = $newQuantity > 0 ? round($newTotal / $newQuantity, 2) : 0;
            DB::table('ct_kardex')->insert(array(
                'fecha' => $purchase->fecha, 'id_movimiento' => $purchaseId, 'movimiento' => 1, 'tipo' => 'COMP',
                'numero' => $purchase->numero, 'producto_id' => $group['product']->id, 'cantidad' => $group['quantity'],
                'valor_unitario' => round($group['cost'] / $group['quantity'], 2), 'total' => round($group['cost'], 2),
                'saldo_cantidad' => $newQuantity, 'saldo_valor_unitario' => $average, 'saldo_total' => $newTotal,
                'bodega_id' => $group['warehouse'], 'id_empresa' => $companyId, 'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId, 'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                'created_at' => $now, 'updated_at' => $now,
            ));
            $audit = array('existencia' => $newQuantity, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now);
            if ($inventory) {
                DB::table('ct_inventario')->where('id', $inventory->id)->update($audit);
            } else {
                DB::table('ct_inventario')->insert(array_merge($audit, array('producto_id' => $group['product']->id,
                    'bodega_id' => $group['warehouse'], 'id_empresa' => $companyId, 'id_usuariocrea' => $userId,
                    'ip_creacion' => $ip, 'created_at' => $now)));
            }
        }

        $clinicalInventory = $this->clinicalInventory ?: app(ClinicalInventoryEntryService::class);
        $clinicalResult = $clinicalInventory
            ->syncPurchase($purchaseId, $companyId, $userId, $ip);

        return array('movidos' => count($groups), 'servicios_omitidos' => $skipped,
            'ya_procesado' => $movementExists, 'inventario_v2' => $legacyResult,
            'inventario_hospitalario' => $clinicalResult);
    }

    private function assertLegacyPurchaseRecorded($companyId, $purchaseId, array $groups, $legacyBefore, $createdNow)
    {
        $movement = DB::table('ct_inv_movimiento')
            ->where('id_referencia', $purchaseId)->where('tipo', 'COMP')
            ->orderBy('id')->lockForUpdate()->first();
        if (!$movement) throw new RuntimeException('No se registro el movimiento historico de la compra.');

        $rows = DB::table('ct_inv_kardex')->where('id_movimiento', $movement->id)
            ->where('estado', 1)->orderBy('id')->lockForUpdate()->get();
        $actual = array();
        $incomingCostByProduct = array();
        foreach ($rows as $row) {
            $key = (int) $row->id_producto.'|'.(int) $row->id_bodega;
            if (!isset($actual[$key])) $actual[$key] = 0;
            $actual[$key] += (float) $row->cantidad;
            if (!isset($incomingCostByProduct[$row->id_producto])) $incomingCostByProduct[$row->id_producto] = 0;
            $incomingCostByProduct[$row->id_producto] += (float) $row->cantidad * (float) $row->precio;
        }
        if (count($actual) !== count($groups)) {
            throw new RuntimeException('El Kardex historico no coincide con las lineas de la compra.');
        }

        $quantityByProduct = array();
        foreach ($groups as $group) {
            $productId = (int) $group['product']->id;
            $key = $productId.'|'.(int) $group['warehouse'];
            if (!isset($actual[$key]) || abs($actual[$key] - (float) $group['quantity']) > 0.0001) {
                throw new RuntimeException('El Kardex historico no coincide con el producto y la bodega comprados.');
            }
            if (!isset($quantityByProduct[$productId])) $quantityByProduct[$productId] = 0;
            $quantityByProduct[$productId] += (float) $group['quantity'];
        }

        foreach ($quantityByProduct as $productId => $quantity) {
            $internal = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
                ->where('id_producto', $productId)->lockForUpdate()->first();
            if (!$internal) throw new RuntimeException('No se actualizo el saldo interno del producto comprado.');
            if ($createdNow) {
                $before = $legacyBefore->get($productId);
                $oldStock = $before ? (float) $before->stock : 0;
                $oldCost = $before ? (float) $before->costo : 0;
                $expectedStock = round($oldStock + $quantity, 4);
                $incomingCost = isset($incomingCostByProduct[$productId]) ? $incomingCostByProduct[$productId] : 0;
                $expectedCost = $expectedStock > 0
                    ? round((($oldStock * $oldCost) + $incomingCost) / $expectedStock, 2)
                    : 0;
                if (abs((float) $internal->stock - $expectedStock) > 0.0001
                    || abs((float) $internal->cantidad - $expectedStock) > 0.0001
                    || abs((float) $internal->costo - $expectedCost) > 0.009) {
                    throw new RuntimeException('El saldo o costo promedio historico no quedo conciliado con la compra.');
                }
            }
            if (!DB::table('ct_inv_costos')->where('id_empresa', $companyId)
                ->where('id_producto', $productId)->exists()) {
                throw new RuntimeException('No se actualizo el historial de costos del producto comprado.');
            }
        }
    }

    private function assertModernPurchaseKardexMatches($rows, array $groups)
    {
        $actual = array();
        foreach ($rows as $row) {
            $key = (int) $row->producto_id.'|'.(int) $row->bodega_id;
            if (!isset($actual[$key])) $actual[$key] = array('quantity' => 0, 'cost' => 0);
            $actual[$key]['quantity'] += (float) $row->cantidad;
            $actual[$key]['cost'] += (float) $row->total;
        }
        if (count($actual) !== count($groups)) {
            throw new RuntimeException('La compra tiene un Kardex V3 incompleto o duplicado.');
        }
        foreach ($groups as $group) {
            $key = (int) $group['product']->id.'|'.(int) $group['warehouse'];
            if (!isset($actual[$key])
                || abs($actual[$key]['quantity'] - (float) $group['quantity']) > 0.0001
                || abs($actual[$key]['cost'] - round((float) $group['cost'], 2)) > 0.009) {
                throw new RuntimeException('El Kardex V3 no coincide con el producto, bodega o costo de la compra.');
            }
        }
    }

    private function line($account, $description, $date, $debit, $credit, array $header, $ip)
    {
        return array('id_plan_cuenta' => $account, 'descripcion' => $description, 'fecha' => $date,
            'debe' => round($debit, 2), 'haber' => round($credit, 2), 'establecimiento' => $header['sucursal'],
            'ip_creacion' => $ip, 'ip_modificacion' => $ip);
    }

    private function assertCompanyAccount($account, $companyId, $label)
    {
        if (!$account || !DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('id_plan', $account)->exists()) {
            throw new RuntimeException('Configure una cuenta contable válida para '.$label.' antes de guardar la compra.');
        }
    }
}
