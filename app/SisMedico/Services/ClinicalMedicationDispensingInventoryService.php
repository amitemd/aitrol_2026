<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\Producto;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\ProductCategoryAccountTemplate;

/**
 * Ejecuta el circuito fisico de farmacia para una administracion eMAR:
 * principal -> bodega de administracion -> paciente.
 *
 * Cada paso se registra tanto en inventario hospitalario como en los saldos y
 * Kardex V3. El llamador debe ejecutarlo dentro de la misma transaccion que
 * guarda la administracion clinica.
 */
class ClinicalMedicationDispensingInventoryService
{
    private $primaryWarehouse;
    private $accountingPosting;

    public function __construct(
        CompanyPrimaryInventoryWarehouseService $primaryWarehouse,
        V3AccountingPostingService $accountingPosting
    )
    {
        $this->primaryWarehouse = $primaryWarehouse;
        $this->accountingPosting = $accountingPosting;
    }

    public function ensureAvailable(Producto $product, $companyId, $administrationWarehouse, $procedureId, $userId, $ip)
    {
        $available = $this->availableSeries($product->id, $administrationWarehouse->inventory_id, $companyId);
        if ($available) return $available;

        $primary = $this->primaryWarehouse->resolve($companyId);
        $source = $this->availableSeries($product->id, $primary->inventory_id, $companyId);
        if (!$source) {
            throw new RuntimeException(
                'No existe stock disponible de '.$product->nombre.' en la bodega principal de la empresa.'
            );
        }

        $this->transferClinical(
            $source, $primary->inventory_id, $administrationWarehouse->inventory_id,
            $procedureId, $companyId, $userId, $ip
        );
        $this->transferAccounting(
            $product->id, $source->serie, $companyId, $primary->accounting_id,
            $administrationWarehouse->accounting_id, $procedureId, $userId, $ip
        );

        $available = $this->availableSeries($product->id, $administrationWarehouse->inventory_id, $companyId);
        if (!$available) {
            throw new RuntimeException('El traslado a la bodega de administracion no genero una existencia utilizable.');
        }
        return $available;
    }

    public function dischargeToPatient(
        InvInventarioSerie $series,
        $procedureId,
        $patientMovementId,
        $companyId,
        $administrationWarehouse,
        $userId,
        $ip
    ) {
        if ((int) $series->id_bodega !== (int) $administrationWarehouse->inventory_id
            || (string) $series->id_empresa !== (string) $companyId) {
            throw new RuntimeException('La serie seleccionada no pertenece a la bodega de administracion de la empresa.');
        }

        $this->exitClinical(
            $series, $procedureId, $patientMovementId, $companyId,
            $administrationWarehouse->inventory_id, $userId, $ip
        );
        $this->exitAccounting(
            $series->id_producto, $series->serie, $companyId,
            $administrationWarehouse->accounting_id, $procedureId, $userId, $ip
        );
    }

    private function availableSeries($productId, $warehouseId, $companyId)
    {
        return InvInventarioSerie::where('id_producto', $productId)
            ->where('id_bodega', $warehouseId)->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->whereRaw('(COALESCE(existencia, 0) - COALESCE(comprometido, 0)) > 0')
            ->orderByRaw('CASE WHEN fecha_vence >= ? THEN 0 WHEN fecha_vence IS NULL THEN 1 ELSE 2 END', array(date('Y-m-d')))
            ->orderBy('fecha_vence')->orderBy('id')->lockForUpdate()->first();
    }

    private function transferClinical($source, $sourceWarehouseId, $destinationWarehouseId, $procedureId, $companyId, $userId, $ip)
    {
        $product = Producto::find($source->id_producto);
        $uses = (int) $product->usos > 0 ? 1 : 0;
        $cost = (float) $source->inventario->costo_promedio;
        $outHeader = $this->clinicalHeader(
            'TRA', 'T', 'TRASLADO A BODEGA DE ADMINISTRACION DE MEDICAMENTOS',
            $sourceWarehouseId, $destinationWarehouseId, $procedureId, $companyId, $userId, $ip
        );
        $this->clinicalDetail($outHeader, $source, $source->id_inv_inventario, 1, $uses, $cost, null, $userId, $ip);
        InvCabMovimientos::calcularTotalCabMovimiento($outHeader);
        InvKardex::setKardex($outHeader);

        $destinationInventoryId = $this->clinicalInventoryId(
            $source->id_producto, $destinationWarehouseId, $companyId, $cost, $userId, $ip
        );
        $inHeader = $this->clinicalHeader(
            'INT', 'I', 'INGRESO POR TRASLADO A BODEGA DE ADMINISTRACION DE MEDICAMENTOS',
            $destinationWarehouseId, $destinationWarehouseId, $procedureId, $companyId, $userId, $ip,
            $outHeader
        );
        $this->clinicalDetail($inHeader, $source, $destinationInventoryId, 1, $uses, $cost, null, $userId, $ip);
        InvCabMovimientos::calcularTotalCabMovimiento($inHeader);
        InvKardex::setKardex($inHeader);
    }

    private function exitClinical($series, $procedureId, $patientMovementId, $companyId, $warehouseId, $userId, $ip)
    {
        $product = Producto::find($series->id_producto);
        $uses = (int) $product->usos > 0 ? 1 : 0;
        $cost = (float) $series->inventario->costo_promedio;
        $header = $this->clinicalHeader(
            'EGP', 'E', 'ENTREGA Y ADMINISTRACION DE MEDICAMENTO AL PACIENTE',
            $warehouseId, null, $procedureId, $companyId, $userId, $ip
        );
        $this->clinicalDetail(
            $header, $series, $series->id_inv_inventario, 1, $uses, $cost,
            $patientMovementId, $userId, $ip
        );
        InvCabMovimientos::calcularTotalCabMovimiento($header);
        InvKardex::setKardex($header);
    }

    private function clinicalHeader(
        $abbreviation, $movementType, $observation, $originId, $destinationId,
        $procedureId, $companyId, $userId, $ip, $originDocumentId = null
    ) {
        $document = $this->clinicalDocument($abbreviation, $movementType, $companyId, $userId, $ip);
        $sequence = $this->clinicalSequence($document->id, $originId, $companyId, $userId, $ip);
        $now = date('Y-m-d H:i:s');
        return DB::table('inv_cab_movimientos')->insertGetId(array(
            'id_documento_bodega' => $document->id,
            'id_transaccion_bodega' => $sequence['transaction_id'],
            'id_bodega_origen' => $originId,
            'id_bodega_destino' => $destinationId,
            'numero_documento' => str_pad((string) $sequence['number'], 9, '0', STR_PAD_LEFT),
            'observacion' => substr($observation.' · eMAR #'.$procedureId, 0, 200),
            'fecha' => date('Y-m-d'),
            'estado' => 1,
            'id_docum_origen' => $originDocumentId,
            'id_hc_procedimientos' => $procedureId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
            'data_humanlabs' => 'EMAR:'.$procedureId.':'.$abbreviation.':'.uniqid('', true),
        ));
    }

    private function clinicalDetail($headerId, $series, $inventoryId, $quantity, $uses, $cost, $patientMovementId, $userId, $ip)
    {
        $subtotal = round($quantity * $cost, 4);
        DB::table('inv_det_movimientos')->insert(array(
            'id_inv_cab_movimientos' => $headerId,
            'id_producto' => $series->id_producto,
            'serie' => $series->serie,
            'fecha_vence' => $series->fecha_vence,
            'lote' => $series->lote,
            'id_inv_inventario' => $inventoryId,
            'cantidad' => $quantity,
            'cant_uso' => $uses,
            'valor_unitario' => $cost,
            'subtotal' => $subtotal,
            'descuento' => 0,
            'iva' => 0,
            'total' => $subtotal,
            'estado' => 1,
            'kardex' => 0,
            'motivo' => 'MOVIMIENTO AUTOMATICO DE MEDICACION eMAR',
            'id_movimiento_paciente' => $patientMovementId,
            'cantidad_usos_kardex' => $uses,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    private function clinicalDocument($abbreviation, $movementType, $companyId, $userId, $ip)
    {
        $document = DB::table('inv_documentos_bodegas')->where('abreviatura_documento', $abbreviation)
            ->where(function ($query) use ($companyId) {
                $query->where('id_empresa', $companyId)->orWhereNull('id_empresa');
            })->orderByRaw('CASE WHEN id_empresa = ? THEN 0 ELSE 1 END', array($companyId))
            ->lockForUpdate()->first();
        if ($document) return $document;

        $typeId = DB::table('inv_tipo_movimiento')->where('tipo', $movementType)->value('id');
        if (!$typeId) {
            $typeId = DB::table('inv_tipo_movimiento')->insertGetId(array(
                'tipo' => $movementType,
                'descripcion' => $movementType === 'I' ? 'INGRESO' : ($movementType === 'T' ? 'TRASLADO' : 'EGRESO'),
            ));
        }
        $names = array('TRA' => 'TRASLADO ENTRE BODEGAS', 'INT' => 'INGRESO POR TRASLADO', 'EGP' => 'EGRESO A PACIENTE');
        $now = date('Y-m-d H:i:s');
        $id = DB::table('inv_documentos_bodegas')->insertGetId(array(
            'documento' => $names[$abbreviation],
            'abreviatura_documento' => $abbreviation,
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

    private function clinicalSequence($documentId, $warehouseId, $companyId, $userId, $ip)
    {
        $row = DB::table('inv_transacciones_bodegas')->where('id_documento_bodega', $documentId)
            ->where('id_bodega', $warehouseId)->where('id_empresa', $companyId)
            ->lockForUpdate()->first();
        $now = date('Y-m-d H:i:s');
        if (!$row) {
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
        $number = max(0, (int) $row->secuencia) + 1;
        DB::table('inv_transacciones_bodegas')->where('id', $row->id)->update(array(
            'secuencia' => $number,
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
            'updated_at' => $now,
        ));
        return array('transaction_id' => $row->id, 'number' => $number);
    }

    private function clinicalInventoryId($productId, $warehouseId, $companyId, $cost, $userId, $ip)
    {
        $rows = InvInventario::where('id_producto', $productId)->where('id_bodega', $warehouseId)
            ->where('id_empresa', $companyId)->orderBy('id')->lockForUpdate()->get();
        if ($rows->count() > 1) throw new RuntimeException('Existen saldos clinicos duplicados en la bodega de administracion.');
        if ($rows->first()) return (int) $rows->first()->id;

        return (int) DB::table('inv_inventario')->insertGetId(array(
            'id_producto' => $productId,
            'id_bodega' => $warehouseId,
            'tipo' => 'F',
            'existencia' => 0,
            'existencia_uso' => 0,
            'comprometido' => 0,
            'comprometido_uso' => 0,
            'costo_promedio' => $cost,
            'estado' => 1,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    private function accountingProduct($clinicalProductId, $companyId)
    {
        $products = DB::table('ct_productos_insumos as relation')
            ->join('ct_productos as product', 'product.id', '=', 'relation.id_producto')
            ->where('relation.id_insumo', $clinicalProductId)
            ->where('product.id_empresa', $companyId)->where('product.estado_tabla', 1)
            ->get(array('product.id', 'product.nombre', 'product.grupo'));
        if ($products->count() !== 1) {
            throw new RuntimeException('El medicamento debe tener una sola relacion con un producto fisico de la empresa.');
        }
        return $products->first();
    }

    private function transferAccounting($clinicalProductId, $series, $companyId, $sourceId, $destinationId, $procedureId, $userId, $ip)
    {
        $product = $this->accountingProduct($clinicalProductId, $companyId);
        $movementId = $this->accountingMovement(
            'TRASLADO', 'TRASLADO A BODEGA DE ADMINISTRACION eMAR', $procedureId, $userId
        );
        $source = $this->accountingPosition($product->id, $sourceId, $companyId, $userId, $ip, false);
        if ((float) $source->existencia < 1) {
            throw new RuntimeException('El saldo V3 de la bodega principal no alcanza para trasladar el medicamento.');
        }
        $unitCost = $this->unitCost($product->id, $sourceId, $companyId);
        $this->accountingKardex($movementId, $product->id, $sourceId, $companyId, 3, 1, $unitCost, $series, 'TRASLADO-', $userId, $ip);
        $this->accountingKardex($movementId, $product->id, $destinationId, $companyId, 2, 1, $unitCost, $series, 'TRASLADO+', $userId, $ip);
    }

    private function exitAccounting($clinicalProductId, $series, $companyId, $warehouseId, $procedureId, $userId, $ip)
    {
        $product = $this->accountingProduct($clinicalProductId, $companyId);
        $position = $this->accountingPosition($product->id, $warehouseId, $companyId, $userId, $ip, false);
        if ((float) $position->existencia < 1) {
            throw new RuntimeException('El saldo V3 de la bodega de administracion no alcanza para entregar el medicamento.');
        }
        $movementId = $this->accountingMovement(
            'EMAR', 'ENTREGA Y ADMINISTRACION DE MEDICAMENTO AL PACIENTE', $procedureId, $userId
        );
        $unitCost = $this->unitCost($product->id, $warehouseId, $companyId);
        $this->accountingKardex($movementId, $product->id, $warehouseId, $companyId, 3, 1, $unitCost, $series, 'EMAR', $userId, $ip);
        $this->postPatientConsumption($companyId, $movementId, $product, $unitCost, $userId, $ip);

        $historical = DB::table('ct_inv_interno')->where('id_empresa', $companyId)
            ->where('id_producto', $product->id)->orderBy('id')->lockForUpdate()->first();
        if (!$historical || (float) $historical->stock < 1) {
            throw new RuntimeException('El saldo historico del producto no alcanza para registrar la administracion.');
        }
        DB::table('ct_inv_interno')->where('id', $historical->id)->update(array(
            'stock' => (float) $historical->stock - 1,
            'cantidad' => (float) $historical->cantidad - 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    private function accountingMovement($type, $detail, $procedureId, $userId)
    {
        $context = DB::table('hc_procedimientos as procedure')
            ->join('historiaclinica as history', 'history.hcid', '=', 'procedure.id_hc')
            ->where('procedure.id', $procedureId)
            ->select('history.id_paciente', 'history.id_agenda')->first();
        $now = date('Y-m-d H:i:s');
        $id = DB::table('ct_inv_movimiento')->insertGetId(array(
            'id_transaccion' => $type === 'TRASLADO' ? 4 : 3,
            'id_paciente' => $context ? $context->id_paciente : null,
            'id_agenda' => $context ? $context->id_agenda : null,
            'id_hc_procedimiento' => $procedureId,
            'id_referencia' => 0,
            'detalle' => $detail,
            'tipo' => $type,
            'pedido' => $type.'-EMAR',
            'created_at' => $now,
            'updated_at' => $now,
        ));
        DB::table('ct_inv_movimiento')->where('id', $id)->update(array(
            'id_referencia' => $id,
            'pedido' => $type.'-'.str_pad((string) $id, 8, '0', STR_PAD_LEFT),
        ));
        return $id;
    }

    private function postPatientConsumption($companyId, $movementId, $product, $unitCost, $userId, $ip)
    {
        if (!Schema::hasTable('v3_product_category_account_templates')) {
            throw new RuntimeException('CONFIG_CONTABLE: Falta ejecutar la migracion de plantillas contables por categoria.');
        }
        // Los productos clínicos heredados suelen no tener grupo aunque estén
        // relacionados a medicina. Esa relación los ubica en Medicamentos.
        $category = trim((string) $product->grupo);
        if ($category === '') $category = '2';
        $template = DB::table('v3_product_category_account_templates')
            ->where('id_empresa', $companyId)->where('categoria', $category)
            ->where('estado', 1)->lockForUpdate()->first();
        $label = ProductCategoryAccountTemplate::categoryLabel($category);
        if (!$template) {
            throw new RuntimeException('CONFIG_CONTABLE: Configure la plantilla contable para la categoria '.$label.'.');
        }
        $inventory = ProductCategoryAccountTemplate::account($companyId, $template->cuenta_inventario);
        $expense = ProductCategoryAccountTemplate::account($companyId, $template->cuenta_gasto);
        if (!$inventory) {
            throw new RuntimeException('CONFIG_CONTABLE: Configure la cuenta de inventario para la categoria '.$label.'.');
        }
        if (!$expense) {
            throw new RuntimeException('CONFIG_CONTABLE: Configure la cuenta de gastos para la categoria '.$label.'.');
        }
        if ((string) $inventory->id_plan === (string) $expense->id_plan) {
            throw new RuntimeException('CONFIG_CONTABLE: Las cuentas de inventario y gastos deben ser diferentes.');
        }
        $amount = round((float) $unitCost, 2);
        if ($amount <= 0) {
            throw new RuntimeException('CONFIG_CONTABLE: El medicamento no tiene un costo valido para contabilizar su consumo.');
        }
        $timestamp = date('Y-m-d H:i:s');
        $number = 'EMAR-'.str_pad((string) $movementId, 8, '0', STR_PAD_LEFT);
        $this->accountingPosting->post(array(
            'id_empresa' => $companyId,
            'fecha_asiento' => $timestamp,
            'observacion' => 'CONSUMO DE MEDICAMENTO ENTREGADO AL PACIENTE '.$number,
            'fact_numero' => $number,
            'valor' => $amount,
            'modulo' => 'INVENTARIO_MEDICAMENTOS',
            'aparece_sri' => 0,
            'especial' => 1,
            'tipo' => 1,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
        ), array(
            array(
                'id_plan_cuenta' => $expense->id_plan,
                'descripcion' => 'Gasto por medicamento administrado · '.$number,
                'fecha' => $timestamp,
                'debe' => $amount,
                'haber' => 0,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ),
            array(
                'id_plan_cuenta' => $inventory->id_plan,
                'descripcion' => 'Salida de inventario por administracion · '.$number,
                'fecha' => $timestamp,
                'debe' => 0,
                'haber' => $amount,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ),
        ), array(
            'modulo' => 'INVENTARIO_MEDICAMENTOS',
            'tipo_origen' => 'ADMINISTRACION_EMAR',
            'id_origen' => (string) $movementId,
        ));
    }

    private function accountingPosition($productId, $warehouseId, $companyId, $userId, $ip, $create)
    {
        $rows = DB::table('ct_inventario')->where('producto_id', $productId)
            ->where('bodega_id', $warehouseId)->where('id_empresa', $companyId)
            ->orderBy('id')->lockForUpdate()->get();
        if ($rows->count() > 1) throw new RuntimeException('Existen saldos V3 duplicados para el producto y la bodega.');
        if ($rows->first()) return $rows->first();
        if (!$create) {
            return (object) array('id' => null, 'existencia' => 0);
        }
        $now = date('Y-m-d H:i:s');
        $id = DB::table('ct_inventario')->insertGetId(array(
            'producto_id' => $productId,
            'bodega_id' => $warehouseId,
            'existencia' => 0,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        return DB::table('ct_inventario')->where('id', $id)->first();
    }

    private function accountingKardex(
        $movementId, $productId, $warehouseId, $companyId, $transactionId,
        $quantity, $unitCost, $series, $type, $userId, $ip
    ) {
        $position = $this->accountingPosition($productId, $warehouseId, $companyId, $userId, $ip, true);
        $oldQuantity = (float) $position->existencia;
        $newQuantity = round($oldQuantity + ($transactionId === 3 ? -$quantity : $quantity), 4);
        if ($newQuantity < -0.0001) throw new RuntimeException('El movimiento produciria una existencia negativa.');
        $newQuantity = max(0, $newQuantity);
        $now = date('Y-m-d H:i:s');
        $historicalId = DB::table('ct_inv_interno')->where('id_producto', $productId)
            ->where('id_empresa', $companyId)->orderBy('id')->value('id');

        DB::table('ct_inv_kardex')->insert(array(
            'id_bodega' => $warehouseId,
            'id_movimiento' => $movementId,
            'id_producto' => $productId,
            'serie' => (string) $series,
            'id_transaccion' => $transactionId,
            'cantidad' => $quantity,
            'precio' => $unitCost,
            'id_inv' => $historicalId,
            'estado' => 1,
            'fecha' => date('Y-m-d H:i:s'),
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
            'id_modificacion' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        DB::table('ct_kardex')->insert(array(
            'fecha' => date('Y-m-d'),
            'id_movimiento' => $movementId,
            'movimiento' => $transactionId === 3 ? 2 : 1,
            'tipo' => $type,
            'numero' => $type.'-'.$movementId,
            'producto_id' => $productId,
            'cantidad' => $quantity,
            'valor_unitario' => $unitCost,
            'total' => round($quantity * $unitCost, 2),
            'saldo_cantidad' => $newQuantity,
            'saldo_valor_unitario' => $unitCost,
            'saldo_total' => round($newQuantity * $unitCost, 2),
            'bodega_id' => $warehouseId,
            'id_empresa' => $companyId,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        DB::table('ct_inventario')->where('id', $position->id)->update(array(
            'existencia' => $newQuantity,
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
            'updated_at' => $now,
        ));
    }

    private function unitCost($productId, $warehouseId, $companyId)
    {
        $cost = DB::table('ct_kardex')->where('producto_id', $productId)
            ->where('bodega_id', $warehouseId)->where('id_empresa', $companyId)
            ->orderBy('id', 'desc')->value('saldo_valor_unitario');
        if ((float) $cost > 0) return (float) $cost;
        return (float) DB::table('ct_inv_interno')->where('id_producto', $productId)
            ->where('id_empresa', $companyId)->value('costo');
    }
}
