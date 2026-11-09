<?php
namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\v3\contable\LogConfig;

/** Devolucion fisica explicita: documento, inventario, asiento y saldo atomicos. */
class V3SupplierCreditReturnService
{
    public function save($request)
    {
        return DB::transaction(function () use ($request) {
            $company=session('id_empresa'); $user=Auth::id(); $ip=$request->ip() ?: '127.0.0.1';
            $purchase=DB::table('ct_compras')->where('id',$request->id_factura)->where('id_empresa',$company)->where('estado','<>',0)->lockForUpdate()->first();
            if (!$purchase || (string)$purchase->proveedor !== (string)$request->id_proveedor_) throw new RuntimeException('Compra o proveedor ajenos a la empresa.');
            $series=$request->establecimiento.'-'.$request->emision;
            if (!preg_match('/^\d{3}-\d{3}$/',$series) || !preg_match('/^\d{9}$/',(string)$request->secuencia)) throw new RuntimeException('Serie o secuencia de nota no valida.');
            if (DB::table('ct_credito_acreedores')->where('id_empresa',$company)->where('id_proveedor',$purchase->proveedor)
                ->where('serie',$series)->where('secuencia',$request->secuencia)->where('estado','<>',0)->exists()) throw new RuntimeException('La nota de credito ya esta registrada.');
            $ids=explode(',',(string)$request->a_detalles); $indexes=explode(',',(string)$request->a_row);
            if (!$ids || count($ids)!==count(array_unique($ids)) || count($ids)!==count($indexes)) throw new RuntimeException('Detalles de devolucion invalidos.');
            $rows=[]; $base=0; $tax=0; $groups=[];
            foreach ($ids as $i=>$id) {
                $detail=DB::table('ct_detalle_compra')->where('id',$id)->where('id_ct_compras',$purchase->id)->where('estado',1)->lockForUpdate()->first();
                if (!$detail || (float)$detail->cantidad<=0) throw new RuntimeException('Detalle ajeno a la compra o sin cantidad.');
                $amount=round((float)($request->abono[$indexes[$i]]??0),2);
                $lineBase=round((float)$detail->cantidad*(float)$detail->precio-(float)$detail->descuento,2);
                $credited=(float)DB::table('ct_detalle_credito_acreedores as d')->join('ct_credito_acreedores as n','n.id','=','d.id_debito')
                    ->where('d.id_det_compra',$id)->where('n.estado','<>',0)->sum('d.valor');
                if ($amount<=0 || $amount>$lineBase-$credited+0.005) throw new RuntimeException('La devolucion supera el importe pendiente de la linea.');
                $product=DB::table('ct_productos')->where('codigo',$detail->codigo)->where('id_empresa',$company)->lockForUpdate()->first();
                if (!$product || (int)$product->tipo_producto!==2) throw new RuntimeException('La devolucion fisica requiere productos inventariables.');
                $quantity=round($amount/($lineBase/(float)$detail->cantidad),4);
                $vat=(int)$detail->iva===1?round($amount*(float)$detail->porcentaje,2):0;
                $rows[]=['detail'=>$detail,'amount'=>$amount,'vat'=>$vat]; $base+=$amount; $tax+=$vat;
                $key=$product->id.'|'.$detail->bodega;
                if (!isset($groups[$key])) $groups[$key]=['product'=>$product,'warehouse'=>$detail->bodega,'quantity'=>0,'amount'=>0];
                $groups[$key]['quantity']+=$quantity; $groups[$key]['amount']+=$amount;
            }
            $total=round($base+$tax,2); $balance=round((float)$purchase->valor_contable,2);
            if ($total>$balance+0.005) throw new RuntimeException('La nota supera el saldo pendiente; un reembolso requiere su circuito especifico.');
            $audit=['id_usuariocrea'=>$user,'id_usuariomod'=>$user,'ip_creacion'=>$ip,'ip_modificacion'=>$ip,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
            $number=$series.'-'.$request->secuencia;
            $note=DB::table('ct_credito_acreedores')->insertGetId($audit+[
                'id_compra'=>$purchase->id,'id_empresa'=>$company,'estado'=>1,'fecha'=>$request->fecha_nota,
                'id_proveedor'=>$purchase->proveedor,'serie'=>$series,'secuencia'=>$request->secuencia,'nro_comprobante'=>$number,
                'concepto'=>$request->descripcion,'nota'=>'DEVOLUCION FISICA','subtotal'=>$base,'impuesto'=>$tax,
                'valor_contable'=>$total,'saldo_ant'=>$balance,'secuencia_factura'=>$purchase->numero,
                'fecha_factura'=>$purchase->fecha,'serie_factura'=>$purchase->serie,'autorizacion'=>$request->autorizacion_nota]);
            foreach ($rows as $row) DB::table('ct_detalle_credito_acreedores')->insert($audit+[
                'id_debito'=>$note,'id_det_compra'=>$row['detail']->id,'codigo'=>$row['detail']->codigo,'nombre'=>$row['detail']->nombre,
                'valor'=>$row['amount'],'iva'=>$row['vat']>0?1:0,'valor_iva'=>$row['vat'],'total'=>$row['amount']+$row['vat'],'fecha'=>$request->fecha_nota]);
            ksort($groups,SORT_STRING);
            $movement=DB::table('ct_inv_movimiento')->insertGetId(['id_transaccion'=>2,'id_referencia'=>$note,'detalle'=>'DEVOLUCION COMPRA '.$number,'tipo'=>'DEV_COMPRA','pedido'=>$number,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            foreach ($groups as $group) {
                $product=$group['product']; $quantity=$group['quantity'];
                $inventory=DB::table('ct_inventario')->where('id_empresa',$company)->where('producto_id',$product->id)->where('bodega_id',$group['warehouse'])->lockForUpdate()->first();
                $legacy=DB::table('ct_inv_interno')->where('id_empresa',$company)->where('id_producto',$product->id)->lockForUpdate()->first();
                if (!$inventory || !$legacy || (float)$inventory->existencia+0.0001<$quantity || (float)$legacy->stock+0.0001<$quantity) throw new RuntimeException('Stock insuficiente para devolver al proveedor.');
                $cost=round($group['amount']/$quantity,4); $remaining=round((float)$inventory->existencia-$quantity,4);
                DB::table('ct_inventario')->where('id',$inventory->id)->update(['existencia'=>$remaining,'id_usuariomod'=>$user,'ip_modificacion'=>$ip]);
                DB::table('ct_inv_interno')->where('id',$legacy->id)->update(['stock'=>(float)$legacy->stock-$quantity,'cantidad'=>(float)$legacy->cantidad-$quantity,'updated_at'=>date('Y-m-d H:i:s')]);
                DB::table('ct_inv_kardex')->insert(['id_bodega'=>$group['warehouse'],'id_movimiento'=>$movement,'id_producto'=>$product->id,'serie'=>'',
                    'id_transaccion'=>2,'cantidad'=>$quantity,'precio'=>$cost,'id_inv'=>$legacy->id,'estado'=>1,'fecha'=>$request->fecha_nota,
                    'id_usuariocrea'=>$user,'id_usuariomod'=>$user,'ip_modificacion'=>$ip,'id_modificacion'=>$user,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
                DB::table('ct_kardex')->insert($audit+['fecha'=>$request->fecha_nota,'id_movimiento'=>$note,'movimiento'=>2,'tipo'=>'NCC','numero'=>$number,
                    'producto_id'=>$product->id,'cantidad'=>$quantity,'valor_unitario'=>$cost,'total'=>$group['amount'],
                    'saldo_cantidad'=>$remaining,'saldo_valor_unitario'=>$legacy->costo,'saldo_total'=>round($remaining*(float)$legacy->costo,2),
                    'bodega_id'=>$group['warehouse'],'id_empresa'=>$company]);
            }
            // Revertir las cuentas efectivamente usadas por la compra, no las actuales del proveedor.
            $original=DB::table('ct_asientos_detalle')->where('id_asiento_cabecera',$purchase->id_asiento_cabecera)->where('estado',1)->get();
            $payable=$original->where('haber','>',0);
            $debits=$original->where('debe','>',0);
            if ($payable->count()!==1 || $debits->isEmpty()) throw new RuntimeException('La compra requiere revision de sus cuentas de origen antes de devolver.');
            $lines=[['id_plan_cuenta'=>$payable->first()->id_plan_cuenta,'descripcion'=>'Devolucion al proveedor '.$number,'fecha'=>$request->fecha_nota,'debe'=>$total,'haber'=>0]];
            $inventoryAccount=LogConfig::busqueda('1.01.03.01.02');
            if (!$debits->contains('id_plan_cuenta',$inventoryAccount)) throw new RuntimeException('No se identifica la cuenta de inventario de origen.');
            $lines[]=['id_plan_cuenta'=>$inventoryAccount,'descripcion'=>'Salida por devolucion de compra','fecha'=>$request->fecha_nota,'debe'=>0,'haber'=>$base];
            if ($tax>0) {
                $taxAccounts=$debits->where('id_plan_cuenta','<>',$inventoryAccount);
                if ($taxAccounts->count()!==1) throw new RuntimeException('No se identifica la cuenta de IVA de origen.');
                $lines[]=['id_plan_cuenta'=>$taxAccounts->first()->id_plan_cuenta,'descripcion'=>'Reversion IVA compra','fecha'=>$request->fecha_nota,'debe'=>0,'haber'=>$tax];
            }
            $entry=app(V3AccountingPostingService::class)->post($audit+['id_empresa'=>$company,'fecha_asiento'=>$request->fecha_nota,
                'observacion'=>$request->descripcion,'fact_numero'=>$number,'sucursal'=>$request->establecimiento,'punto_emision'=>$request->emision,'valor'=>$total],
                $lines,['modulo'=>'PROVEEDORES','tipo_origen'=>'NOTA_CREDITO','id_origen'=>(string)$note]);
            DB::table('ct_credito_acreedores')->where('id',$note)->update(['id_asiento_cabecera'=>$entry]);
            $remaining=round($balance-$total,2);
            DB::table('ct_compras')->where('id',$purchase->id)->update(['valor_contable'=>$remaining,'saldo_pendiente'=>$remaining,
                'estado_pago'=>$remaining<=0?'PAGADA':'PARCIAL','estado'=>$remaining<=0?3:2,'id_usuariomod'=>$user,'ip_modificacion'=>$ip]);
            return ['status'=>'success','id'=>$note,'id_asiento'=>$entry,'saldo'=>$remaining,'message'=>'Devolucion, asiento y saldo registrados.'];
        },3);
    }
}
