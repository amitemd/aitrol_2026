<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;

class V3DocumentTraceService
{
    public function originsByEntry($companyId, $entryId)
    {
        $definitions=array(
            array('venta','Venta / factura','ct_ventas','id_asiento','nro_comprobante','fecha'),
            array('compra','Compra / factura de proveedor','ct_compras','id_asiento_cabecera','numero','fecha'),
            array(null,'Retención de cliente','ct_cliente_retencion','id_asiento_cabecera','nro_comprobante','fecha'),
            array(null,'Retención de proveedor','ct_retenciones','id_asiento_cabecera','secuencia','fecha'),
            array(null,'Comprobante de ingreso','ct_comprobante_ingreso','id_asiento_cabecera','secuencia','fecha'),
            array(null,'Comprobante de egreso','ct_comprobante_egreso','id_asiento_cabecera','secuencia','fecha_comprobante'),
            array(null,'Comprobante de egreso masivo','ct_comprobante_egreso_masivo','id_asiento_cabecera','secuencia','fecha_comprobante'),
            array(null,'Cruce de valores de clientes','ct_cruce_valores_cliente','id_asiento_cabecera','secuencia','fecha_pago'),
            array(null,'Cruce de cuentas de clientes','ct_cruce_cuentas_clientes','id_asiento_cabecera','secuencia','fecha'),
            array(null,'Cruce de valores de proveedores','ct_cruce_valores','id_asiento_cabecera','secuencia','fecha_pago'),
            array(null,'Cruce de cuentas de proveedores','ct_cruce_cuentas','id_asiento_cabecera','secuencia','fecha'),
            array(null,'Transferencia bancaria','ct_transferencia_bancaria','id_asiento','secuencia','fecha_asiento'),
            array(null,'Depósito bancario','ct_deposito_bancario','id_asiento','secuencia','fecha_asiento'),
            array(null,'Nota de débito bancaria','ct_nota_debito_cabecera','id_asiento','secuencia','fecha'),
            array(null,'Nota de crédito bancaria','ct_nota_credito_cabecera','id_asiento','secuencia','fecha')
        );
        $origins=collect();
        foreach($definitions as $definition){list($traceType,$label,$table,$entryColumn,$numberColumn,$dateColumn)=$definition;
            if(!Schema::hasTable($table)||!Schema::hasColumn($table,$entryColumn)||!Schema::hasColumn($table,'id_empresa'))continue;
            $number=Schema::hasColumn($table,$numberColumn)?$numberColumn:'id';$date=Schema::hasColumn($table,$dateColumn)?$dateColumn:(Schema::hasColumn($table,'created_at')?'created_at':'id');
            $rows=DB::table($table)->where('id_empresa',$companyId)->where($entryColumn,$entryId)->get(array('id',$number.' as numero',$date.' as fecha'));
            foreach($rows as $row)$origins->push(array('tipo'=>$label,'tabla'=>$table,'id'=>$row->id,'numero'=>$row->numero,'fecha'=>$row->fecha,'traza_tipo'=>$traceType));
        }
        return $origins;
    }

    public function search($companyId, $type, $filters)
    {
        $filters = is_array($filters) ? $filters : array('general'=>$filters);
        $hasFilter = false;
        foreach ($filters as $value) if (trim((string)$value) !== '') $hasFilter = true;
        if (!$hasFilter) return collect();
        if ($type === 'venta') {
            return DB::table('ct_ventas as d')->leftJoin('ct_clientes as t', 't.identificacion', '=', 'd.id_cliente')
                ->where('d.id_empresa', $companyId)->where(function($q) use($filters){$this->applyFilters($q,'venta',$filters);})
                ->orderBy('d.id', 'desc')->limit(30)->get(array('d.id', 'd.nro_comprobante as numero', 'd.fecha',
                    'd.total_final as total', 'd.valor_contable as saldo', 'd.estado', 't.nombre as tercero'));
        }
        if ($type === 'compra') {
            return DB::table('ct_compras as d')->leftJoin('proveedor as t', 't.id', '=', 'd.proveedor')
                ->where('d.id_empresa', $companyId)->where(function($q) use($filters){$this->applyFilters($q,'compra',$filters);})
                ->orderBy('d.id', 'desc')->limit(30)->get(array('d.id', 'd.numero', 'd.fecha',
                    'd.total_final as total', 'd.valor_contable as saldo', 'd.estado', 't.razonsocial as tercero'));
        }
        throw new InvalidArgumentException('Tipo de documento no valido.');
    }

    public function detail($companyId, $type, $id)
    {
        return $type === 'venta' ? $this->sale($companyId, $id) : $this->purchase($companyId, $id);
    }

    public function recalculate($companyId, $type, $id)
    {
        return DB::transaction(function () use ($companyId, $type, $id) {
            if ($type === 'venta') {
                $balance = app(V3SalesDocumentBalanceService::class)->recalculate($companyId, $id);
                $document = DB::table('ct_ventas')->where('id_empresa', $companyId)->where('id', $balance['id'])->first();
                $entryId = $document ? $document->id_asiento : null;
            } elseif ($type === 'compra') {
                $balance = $this->purchaseBalance($companyId, $id, true);
                DB::table('ct_compras')->where('id_empresa', $companyId)->where('id', $id)
                    ->update(array('valor_contable' => $balance['saldo_calculado']));
                $document = DB::table('ct_compras')->where('id_empresa', $companyId)->where('id', $id)->first();
                $entryId = $document ? $document->id_asiento_cabecera : null;
            } else throw new InvalidArgumentException('Tipo de documento no valido.');

            $entry = $this->recalculateEntry($companyId, $entryId);
            return array('saldo' => $balance['saldo_calculado'], 'asiento' => $entry);
        });
    }

    private function sale($companyId, $id)
    {
        $document = DB::table('ct_ventas as d')->leftJoin('ct_clientes as t', 't.identificacion', '=', 'd.id_cliente')
            ->where('d.id_empresa', $companyId)->where('d.id', $id)->first(array('d.*', 't.nombre as tercero'));
        if (!$document) throw new InvalidArgumentException('La venta no existe en la empresa activa.');
        $balance = app(V3SalesDocumentBalanceService::class)->snapshot($companyId, $id);
        $movements = collect();
        $this->append($movements, 'Cobro', 'ct_detalle_comprobante_ingreso', 'ct_comprobante_ingreso', 'id_comprobante', 'id_factura', $id, 'total', 'fecha', 'secuencia', $companyId);
        $this->appendSimple($movements, 'Retencion', 'ct_cliente_retencion', 'id_factura', $id, DB::raw('COALESCE(valor_fuente,0)+COALESCE(valor_iva,0)'), 'fecha', 'nro_comprobante', $companyId);
        $this->appendSimple($movements, 'Nota de credito', 'ct_nota_credito_clientes', 'id_factura', $id, 'total_credito', 'fecha', 'nro_comprobante', $companyId);
        $this->appendSimple($movements, 'Nota de debito', 'ct_debito_clientes', 'id_factura', $id, 'valor_contable', 'fecha', 'numero', $companyId);
        $this->appendSimple($movements, 'Cruce de cuentas', 'ct_cruce_cuentas_clientes', 'id_factura', $id, 'total', 'fecha', 'secuencia', $companyId);
        $this->append($movements, 'Cruce de valores', 'ct_detalle_cruce_clientes', 'ct_cruce_valores_cliente', 'id_comprobante', 'id_factura', $id, 'total', 'fecha_pago', 'secuencia', $companyId);
        return $this->package('venta', $document, $balance, $movements, $document->id_asiento);
    }

    private function purchase($companyId, $id)
    {
        $document = DB::table('ct_compras as d')->leftJoin('proveedor as t', 't.id', '=', 'd.proveedor')
            ->where('d.id_empresa', $companyId)->where('d.id', $id)->first(array('d.*', 't.razonsocial as tercero'));
        if (!$document) throw new InvalidArgumentException('La compra no existe en la empresa activa.');
        $balance = $this->purchaseBalance($companyId, $id, false);
        $movements = collect();
        $this->append($movements, 'Pago', 'ct_detalle_comprobante_egreso', 'ct_comprobante_egreso', 'id_comprobante', 'id_compra', $id, 'abono', 'fecha_comprobante', 'secuencia', $companyId);
        $this->append($movements, 'Pago masivo', 'ct_detalle_comprobante_egreso_masivo', 'ct_comprobante_egreso_masivo', 'id_comprobante', 'id_compra', $id, 'abono', 'fecha_comprobante', 'secuencia', $companyId);
        $this->append($movements, 'Debito bancario aplicado', 'ct_debito_bancario_detalle', 'ct_debito_bancario', 'id_debito', 'id_compra', $id, 'abono', 'fecha', 'secuencia', $companyId);
        $this->appendSimple($movements, 'Retencion', 'ct_retenciones', 'id_compra', $id, DB::raw('COALESCE(valor_fuente,0)+COALESCE(valor_iva,0)'), 'fecha', 'secuencia', $companyId);
        $this->appendSimple($movements, 'Nota de credito', 'ct_credito_acreedores', 'id_compra', $id, 'valor_contable', 'fecha', 'secuencia', $companyId);
        $this->appendSimple($movements, 'Cruce de cuentas', 'ct_cruce_cuentas', 'id_factura', $id, 'total', 'fecha', 'secuencia', $companyId);
        $this->append($movements, 'Cruce de valores', 'ct_detalle_cruce', 'ct_cruce_valores', 'id_comprobante', 'id_factura', $id, 'total', 'fecha_pago', 'secuencia', $companyId);
        $this->append($movements, 'Nota de debito de acreedor', 'ct_detalle_debito_acreedores', 'ct_debito_acreedores', 'id_debito_acreedores', 'id_factura', $id, 'total', 'fechand', 'secuencia', $companyId);
        return $this->package('compra', $document, $balance, $movements, $document->id_asiento_cabecera);
    }

    private function purchaseBalance($companyId, $id, $lock)
    {
        $query = DB::table('ct_compras')->where('id_empresa', $companyId)->where('id', $id)->where('estado', '<>', 0);
        if ($lock) $query->lockForUpdate();
        $d = $query->first();
        if (!$d) throw new InvalidArgumentException('La compra no existe o esta anulada.');
        $payments = $this->joinedSum('ct_detalle_comprobante_egreso', 'ct_comprobante_egreso', 'id_comprobante', 'id_compra', $id, 'abono', $companyId);
        $bulkPayments = $this->joinedSum('ct_detalle_comprobante_egreso_masivo', 'ct_comprobante_egreso_masivo', 'id_comprobante', 'id_compra', $id, 'abono', $companyId);
        $bankDebits = $this->joinedSum('ct_debito_bancario_detalle', 'ct_debito_bancario', 'id_debito', 'id_compra', $id, 'abono', $companyId);
        $crossValues = $this->joinedSum('ct_detalle_cruce', 'ct_cruce_valores', 'id_comprobante', 'id_factura', $id, 'total', $companyId);
        $crossAccounts = $this->simpleSum('ct_cruce_cuentas', 'id_factura', $id, 'total', $companyId);
        $retentions = $this->simpleExpressionSum('ct_retenciones', 'id_compra', $id, 'COALESCE(valor_fuente,0)+COALESCE(valor_iva,0)', $companyId);
        $credits = $this->simpleSum('ct_credito_acreedores', 'id_compra', $id, 'valor_contable', $companyId);
        $debits = $this->joinedSum('ct_detalle_debito_acreedores', 'ct_debito_acreedores', 'id_debito_acreedores', 'id_factura', $id, 'total', $companyId);
        $charges = round((float) $d->total_final, 2);
        $applied = round($payments + $bulkPayments + $bankDebits + $crossValues + $crossAccounts + $retentions + $credits + $debits, 2);
        $calc = V3SalesDocumentBalanceService::calculateBalance($charges, $applied);
        return array('total_documento' => round($d->total_final,2), 'notas_debito' => round($debits,2), 'pagos' => round($payments+$bulkPayments+$bankDebits,2),
            'cruces' => round($crossValues+$crossAccounts,2), 'retenciones' => round($retentions,2), 'notas_credito' => round($credits,2),
            'saldo_guardado' => round($d->valor_contable,2), 'saldo_calculado' => $calc['saldo'], 'sobreaplicado' => $calc['sobreaplicado']);
    }

    private function package($type, $document, $balance, $movements, $entryId)
    {
        return array('tipo'=>$type, 'documento'=>$document, 'saldo'=>$balance,
            'movimientos'=>$movements->sortByDesc('fecha')->values(), 'asiento'=>$this->entry($entryId, $document->id_empresa));
    }

    private function entry($id, $companyId)
    {
        if (!$id) return null;
        $h = DB::table('ct_asientos_cabecera')->where('id', $id)->where('id_empresa', $companyId)->first();
        if (!$h) return null;
        $s = DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $id)->where('estado', '<>', 0)
            ->select(DB::raw('COALESCE(SUM(debe),0) debe, COALESCE(SUM(haber),0) haber, COUNT(*) lineas'))->first();
        return array('id'=>$id, 'fecha'=>$h->fecha_asiento, 'modulo'=>isset($h->modulo)?$h->modulo:'Sin módulo', 'observacion'=>$h->observacion, 'valor'=>round($h->valor,2),
            'debe'=>round($s->debe,2), 'haber'=>round($s->haber,2), 'lineas'=>$s->lineas, 'diferencia'=>round($s->debe-$s->haber,2));
    }

    private function recalculateEntry($companyId, $id)
    {
        if (!$id) throw new RuntimeException('El documento no tiene asiento contable asociado.');
        $h = DB::table('ct_asientos_cabecera')->where('id', $id)->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)->lockForUpdate()->first();
        if (!$h) throw new RuntimeException('El asiento no pertenece a la empresa activa.');
        $entry = $this->entry($id, $companyId);
        if ($entry['lineas'] < 2 || abs($entry['diferencia']) > 0.01) throw new RuntimeException('El asiento esta descuadrado; no se modifico. Diferencia: '.number_format($entry['diferencia'],2));
        DB::table('ct_asientos_cabecera')->where('id', $id)->where('id_empresa', $companyId)->update(array('valor'=>$entry['debe']));
        return $this->entry($id, $companyId);
    }

    private function append($c,$label,$detail,$header,$fk,$docFk,$id,$amount,$date,$number,$company)
    {
        if (!Schema::hasTable($detail) || !Schema::hasTable($header)) return;
        if (!Schema::hasColumn($detail,$amount) || !Schema::hasColumn($detail,$docFk) || !Schema::hasColumn($detail,$fk)) return;
        if (!Schema::hasColumn($header,'id_empresa')) return;
        $date = Schema::hasColumn($header,$date) ? $date : (Schema::hasColumn($header,'created_at')?'created_at':'id');
        $number = Schema::hasColumn($header,$number) ? $number : 'id';
        $query=DB::table($detail.' as d')->join($header.' as h','h.id','=','d.'.$fk)->where('d.'.$docFk,$id)->where('h.id_empresa',$company);
        if(Schema::hasColumn($header,'estado'))$query->where('h.estado','<>',0);
        $state=Schema::hasColumn($header,'estado')?'h.estado':DB::raw('1 as estado');
        $entryColumn=$this->entryColumn($header);$columns=array('h.id as documento_id','d.'.$amount.' as valor','h.'.$date.' as fecha','h.'.$number.' as numero',$state);
        $columns[]=$entryColumn?'h.'.$entryColumn.' as asiento_id':DB::raw('NULL as asiento_id');
        $rows=$query->get($columns);
        foreach($rows as $r)$c->push(array('tipo'=>$label,'modulo'=>$header,'documento_id'=>$r->documento_id,'asiento_id'=>$r->asiento_id,'fecha'=>$r->fecha,'numero'=>$r->numero,'valor'=>round($r->valor,2),'estado'=>$r->estado));
    }

    private function appendSimple($c,$label,$table,$fk,$id,$amount,$date,$number,$company)
    {
        if (!Schema::hasTable($table)) return;
        if (!Schema::hasColumn($table,$fk) || !Schema::hasColumn($table,'id_empresa')) return;
        if (is_string($amount) && !Schema::hasColumn($table,$amount)) return;
        $date = Schema::hasColumn($table,$date) ? $date : (Schema::hasColumn($table,'created_at')?'created_at':'id');
        $number = Schema::hasColumn($table,$number) ? $number : 'id';
        $valueColumn = $amount instanceof \Illuminate\Contracts\Database\Query\Expression
            ? DB::raw($amount->getValue(DB::connection()->getQueryGrammar()).' as valor')
            : $amount.' as valor';
        $state=Schema::hasColumn($table,'estado')?'estado':DB::raw('1 as estado');
        $entryColumn=$this->entryColumn($table);$columns=array('id as documento_id',$valueColumn,$date.' as fecha',$number.' as numero',$state);
        $columns[]=$entryColumn?$entryColumn.' as asiento_id':DB::raw('NULL as asiento_id');
        $rows=DB::table($table)->where($fk,$id)->where('id_empresa',$company)->get($columns);
        foreach($rows as $r)$c->push(array('tipo'=>$label,'modulo'=>$table,'documento_id'=>$r->documento_id,'asiento_id'=>$r->asiento_id,'fecha'=>$r->fecha,'numero'=>$r->numero,'valor'=>round($r->valor,2),'estado'=>$r->estado));
    }

    private function entryColumn($table)
    {
        foreach(array('id_asiento_cabecera','id_asiento') as $column) if(Schema::hasColumn($table,$column)) return $column;
        return null;
    }

    private function joinedSum($detail,$header,$fk,$docFk,$id,$amount,$company)
    { if(!Schema::hasTable($detail)||!Schema::hasTable($header))return 0; return (float)DB::table($detail.' as d')->join($header.' as h','h.id','=','d.'.$fk)->where('d.'.$docFk,$id)->where('h.id_empresa',$company)->where('h.estado','<>',0)->sum('d.'.$amount); }
    private function simpleSum($table,$fk,$id,$amount,$company)
    { if(!Schema::hasTable($table))return 0; return (float)DB::table($table)->where($fk,$id)->where('id_empresa',$company)->where('estado','<>',0)->sum($amount); }
    private function simpleExpressionSum($table,$fk,$id,$expression,$company)
    { if(!Schema::hasTable($table))return 0; $r=DB::table($table)->where($fk,$id)->where('id_empresa',$company)->where('estado','<>',0)->select(DB::raw('COALESCE(SUM('.$expression.'),0) total'))->first(); return $r?(float)$r->total:0; }

    private function applyFilters($query,$type,$filters)
    {
        $table=$type==='venta'?'ct_ventas':'ct_compras';
        $used=false;
        if(!empty($filters['id'])){$query->where('d.id',(int)$filters['id']);$used=true;}
        if(!empty($filters['numero'])){$query->where($type==='venta'?'d.nro_comprobante':'d.numero','like','%'.trim($filters['numero']).'%');$used=true;}
        if(!empty($filters['clave'])){
            $columns=$type==='venta'?array('clave_acceso','nro_autorizacion'):array('clave_acceso','autorizacion','nro_autorizacion');
            $available=array();
            foreach($columns as $column) if(Schema::hasColumn($table,$column)) $available[]=$column;
            if(count($available)){
                $value='%'.trim($filters['clave']).'%';
                $query->where(function($q)use($available,$value){
                    foreach($available as $index=>$column){
                        if($index===0)$q->where('d.'.$column,'like',$value);
                        else $q->orWhere('d.'.$column,'like',$value);
                    }
                });
                $used=true;
            }
        }
        if(!empty($filters['tercero'])){
            $value='%'.trim($filters['tercero']).'%';
            $query->where(function($q)use($type,$value){$q->where($type==='venta'?'d.id_cliente':'d.proveedor','like',$value)
                ->orWhere($type==='venta'?'t.nombre':'t.razonsocial','like',$value);});$used=true;
        }
        if(!$used&&!empty($filters['general'])){
            $term='%'.trim($filters['general']).'%';
            $query->where(function($q)use($type,$term){$q->where($type==='venta'?'d.nro_comprobante':'d.numero','like',$term)
                ->orWhere($type==='venta'?'d.id_cliente':'d.proveedor','like',$term)
                ->orWhere($type==='venta'?'t.nombre':'t.razonsocial','like',$term);});
        }
    }
}
