<?php

namespace Sis_medico\Services\Ats;

use DomainException;
use Illuminate\Support\Facades\DB;

/** Company scoped, read-only collection. Unsupported records block, never disappear. */
class AtsPeriodSource
{
    private $company;
    private $year;
    private $month;
    private $related;
    private $errors = [];

    public function collect(string $company, int $year, int $month, array $establishments, array $related): array
    {
        $this->company = $company; $this->year = $year; $this->month = $month;
        $this->related = $related; $this->errors = [];
        $entity = DB::table('empresa')->where('id', $company)->first();
        if (!$entity) throw new DomainException('No existe la empresa seleccionada.');
        $data = ['company'=>$company,'name'=>$entity->razonsocial,'year'=>$year,'month'=>$month,
            'establishments'=>$establishments,'sales'=>[],'purchases'=>[]];
        $sales = $this->period('ct_ventas')->get();
        foreach ($sales as $sale) $this->record('Venta #'.$sale->id, function () use ($sale, &$data) {
            if ((int)$sale->estado === 0) throw new DomainException('Documento anulado: revise su fecha de anulación y baja SRI antes de incluirlo en el módulo anulados.');
            if (!in_array($sale->tipo, ['VEN-FA','VENFA-CO'], true)) throw new DomainException('Tipo de venta '.$sale->tipo.' necesita clasificación ATS.');
            $data['sales'][] = $this->sale($sale, '18');
        });
        foreach ($this->period('ct_nota_credito_clientes')->get() as $note) $this->record('NC cliente #'.$note->id, function () use ($note, &$data) {
            if ((int)$note->estado === 0) throw new DomainException('Nota anulada: requiere conciliación del módulo anulados.');
            $original = DB::table('ct_ventas')->where('id_empresa', $this->company)->where('id', $note->id_factura)->first();
            if (!$original) throw new DomainException('No existe la factura afectada en esta empresa.');
            if ((string)$original->id_cliente !== (string)$note->id_cliente) throw new DomainException('El cliente no coincide con la factura afectada.');
            $details = DB::table('ct_devolucion_productos')->where('id_nota_credito', $note->id)->where('estado',1)->get();
            if ($details->isEmpty()) throw new DomainException('Falta detalle activo.');
            $zero = 0; $taxable = 0; $vat = 0;
            foreach ($details as $detail) {
                $base = AtsXmlBuilder::cents((float)$detail->cantidad * (float)$detail->precio - (float)$detail->descuento);
                $tax = AtsXmlBuilder::cents($detail->valor_iva);
                if ($tax > 0) $taxable += $base; else $zero += $base;
                $vat += $tax;
            }
            if (abs($zero + $taxable - AtsXmlBuilder::cents($note->subtotal)) > 2) throw new DomainException('Subtotal no coincide con detalle.');
            $note->subtotal_0 = $zero / 100; $note->subtotal_12 = $taxable / 100; $note->iva_total = $vat / 100;
            $note->ice_total = 0;
            $data['sales'][] = $this->sale($note, '04', $original->id);
        });
        $purchases = $this->period('ct_compras')->where('estado','!=',0)->get();
        foreach ($purchases as $purchase) $this->record('Compra #'.$purchase->id, function () use ($purchase, &$data) {
            $data['purchases'][] = $this->purchase($purchase);
        });
        foreach ($this->period('ct_credito_acreedores')->where('estado','!=',0)->get() as $note) $this->record('NC proveedor #'.$note->id, function () use ($note, &$data) {
            $original = DB::table('ct_compras')->where('id_empresa',$this->company)->where('id',$note->id_compra)->first();
            if (!$original || (string)$original->proveedor !== (string)$note->id_proveedor) throw new DomainException('Factura modificada inexistente o proveedor diferente.');
            $row = clone $note;
            $row->proveedor = $note->id_proveedor; $row->tipo_comprobante = '04';
            $row->credito_tributario = $note->id_credito_tributario;
            $row->numero = $note->serie.'-'.$note->secuencia;
            $row->iva_total = $note->impuesto; $row->ice_total = 0;
            $row->forma_pago = $original->forma_pago ?? null;
            $result = $this->purchase($row, false);
            $num = AtsXmlBuilder::number($original->numero);
            $originalType = str_pad($original->tipo_comprobante,2,'0',STR_PAD_LEFT);
            $originalAuth = $originalType === '03' ? ($original->nro_autorizacion ?? $original->autorizacion) : $original->autorizacion;
            AtsXmlBuilder::authorization(trim((string)$originalAuth), $originalType === '03' ? $this->company : (string)$original->proveedor,
                $originalType, $original->numero, $this->emissionDate($original));
            $result += ['docModificado'=>str_pad($original->tipo_comprobante,2,'0',STR_PAD_LEFT),
                'estabModificado'=>$num[0],'ptoEmiModificado'=>$num[1],'secModificado'=>$num[2],
                'autModificado'=>trim((string)$originalAuth)];
            $data['purchases'][] = $result;
        });
        // These modules require additional source mapping; do not silently omit existing rows.
        foreach (['ct_debito_clientes'=>'fecha', 'ct_debito_acreedores'=>'fechand'] as $table=>$date) {
            if (DB::getSchemaBuilder()->hasTable($table) && $this->period($table,$date)->where('estado','!=',0)->exists()) {
                $this->errors[] = $table.': hay notas de débito en el período; requieren clasificación y detalle ATS antes de exportar.';
            }
        }
        // Retentions belonging to other-period purchases/sales need explicit reconciliation.
        foreach (['ct_retenciones'=>['id_compra',$purchases->pluck('id')->all()],
            'ct_cliente_retencion'=>['id_factura',$sales->where('estado','!=',0)->pluck('id')->all()]] as $table=>$info) {
            if ($this->period($table)->where('estado',1)->where(function ($q) use ($info) {
                $q->whereNotIn($info[0],$info[1])->orWhereNull($info[0]);
            })->exists()) $this->errors[] = $table.': existen retenciones del período sin documento activo del mismo mes; concilie su imputación antes de exportar.';
        }
        if ($this->errors) throw new DomainException(implode("\n", $this->errors));
        $seen = [];
        foreach ($data['purchases'] as $purchase) {
            $key = implode('|', array_intersect_key($purchase, array_flip(['idProv','tipoComprobante','establecimiento','puntoEmision','secuencial','codSustento'])));
            if (isset($seen[$key])) throw new DomainException('Compra duplicada en el período: '.$key.'. Revise antes de declarar.');
            $seen[$key] = true;
        }
        return $data;
    }

    private function period(string $table, string $date = 'fecha')
    {
        return DB::table($table)->where('id_empresa',$this->company)->whereYear($date,$this->year)->whereMonth($date,$this->month)->orderBy('id');
    }

    private function record(string $label, callable $callback): void
    {
        try { $callback(); } catch (DomainException $e) { $this->errors[] = $label.': '.$e->getMessage(); }
    }

    private function sale($sale, string $type, $paymentSaleId = null): array
    {
        $auth = trim((string)($sale->nro_autorizacion ?? ''));
        AtsXmlBuilder::authorization($auth,$this->company,$type === '18' ? '01' : $type,$sale->nro_comprobante,$sale->fecha);
        $this->authorized($auth, $type === '18' ? '01' : $type);
        $id = (string)$sale->id_cliente;
        $idType = $id === '9999999999999' ? '07' : (preg_match('/^\d{13}$/D',$id) ? '04' : '05');
        $this->identification($id, $idType === '07');
        $payments = DB::table('ct_forma_pago as f')->leftJoin('ct_tipo_pago as t','t.id','=','f.tipo')
            ->where('f.id_ct_ventas',$paymentSaleId ?? $sale->id)->where('f.estado',1)->pluck('t.codigo_sri')->all();
        $received = ['valorRetIva'=>0,'valorRetRenta'=>0];
        if ($type === '18') {
            $headers = $this->period('ct_cliente_retencion')->where('id_factura',$sale->id)->where('estado',1)->get();
            foreach ($headers as $header) {
                foreach (DB::table('ct_detalle_cliente_retencion')->where('id_cliente_retencion',$header->id)->where('estado',1)->get() as $row) {
                    $field = in_array((string)$row->tipo,['1','IVA'],true) ? 'valorRetIva' : (in_array((string)$row->tipo,['2','RENTA'],true) ? 'valorRetRenta' : null);
                    if (!$field) throw new DomainException('Tipo de retención recibida desconocido.');
                    $received[$field] += AtsXmlBuilder::cents($row->totales);
                }
            }
        }
        $result = ['tpIdCliente'=>$idType,'idCliente'=>$id];
        if ($idType !== '07') $result['parteRelVtas'] = in_array($id,$this->related,true) ? 'SI' : 'NO';
        return $result + ['tipoComprobante'=>$type,'tipoEmision'=>strlen($auth) === 49 ? 'E' : 'F',
            '_establishment'=>AtsXmlBuilder::number($sale->nro_comprobante)[0],
            'baseNoGraIva'=>0,'baseImponible'=>$sale->subtotal_0,'baseImpGrav'=>$sale->subtotal_12,
            'montoIva'=>$sale->iva_total,'montoIce'=>$sale->ice_total ?? 0,
            'valorRetIva'=>$received['valorRetIva']/100,'valorRetRenta'=>$received['valorRetRenta']/100,
            'formasDePago'=>AtsXmlBuilder::payments($payments)];
    }

    private function purchase($row, bool $withholdings = true): array
    {
        $type = str_pad($row->tipo_comprobante,2,'0',STR_PAD_LEFT);
        $support = str_pad($row->credito_tributario,2,'0',STR_PAD_LEFT);
        if ($withholdings && $type === '04') throw new DomainException('Registre la NC en notas de crédito de proveedores con su documento modificado.');
        if (!in_array($type,['01','02','03','04'],true) || !in_array($support,['01','02','03','04','06','07'],true)) {
            throw new DomainException('Tipo '.$type.' / sustento '.$support.' requiere datos ATS especiales; no se exportó parcialmente.');
        }
        $id = (string)$row->proveedor; $this->identification($id);
        $num = AtsXmlBuilder::number($row->numero);
        $date = $this->emissionDate($row);
        if ($date > substr($row->fecha,0,10)) throw new DomainException('Emisión posterior al registro contable.');
        $authorization = trim((string)($type === '03' ? ($row->nro_autorizacion ?? $row->autorizacion) : $row->autorizacion));
        AtsXmlBuilder::authorization($authorization, $type === '03' ? $this->company : $id, $type, $row->numero, $date);
        if ($type === '03') $this->authorized($authorization,'03');
        $data = ['codSustento'=>$support,'tpIdProv'=>strlen($id) === 13 ? '01' : '02','idProv'=>$id,
            'tipoComprobante'=>$type,'parteRel'=>in_array($id,$this->related,true) ? 'SI' : 'NO',
            'fechaRegistro'=>$this->date($row->fecha),'establecimiento'=>$num[0],'puntoEmision'=>$num[1],
            'secuencial'=>$num[2],'fechaEmision'=>$this->date($date),'autorizacion'=>$authorization,
            'baseNoGraIva'=>'0.00','baseImponible'=>AtsXmlBuilder::money($row->subtotal_0),
            'baseImpGrav'=>AtsXmlBuilder::money($row->subtotal_12),'baseImpExe'=>'0.00',
            'montoIce'=>AtsXmlBuilder::money($row->ice_total ?? 0),'montoIva'=>AtsXmlBuilder::money($row->iva_total)];
        $base = (float)$data['baseImponible'] + (float)$data['baseImpGrav'];
        if ($base <= 0) throw new DomainException('La suma de bases imponibles debe ser mayor a cero.');
        foreach (AtsXmlBuilder::VAT_FIELDS as $field) $data[$field] = '0.00';
        $air = []; $retention = null;
        $headers = $withholdings ? DB::table('ct_retenciones')->where('id_empresa',$this->company)->where('id_compra',$row->id)->where('estado',1)->get() : collect();
        if ($headers->count() > 1) throw new DomainException('Más de una retención activa: requiere separar sus registros sin duplicar bases.');
        foreach ($headers as $header) {
            if (substr($header->fecha,0,7) !== sprintf('%04d-%02d',$this->year,$this->month)) throw new DomainException('Retención de otro período; concilie la imputación.');
            if (substr($header->fecha,0,10) < substr($row->fecha,0,10)) throw new DomainException('Retención anterior al registro de la compra.');
            $auth = trim((string)($header->nro_autorizacion ?: $header->autorizacion));
            AtsXmlBuilder::authorization($auth,$this->company,'07',$header->nro_comprobante,$header->fecha);
            $this->authorized($auth, '07');
            $retention = ['number'=>AtsXmlBuilder::number($header->nro_comprobante),'auth'=>$auth,'date'=>$this->date($header->fecha)];
            $details = DB::table('ct_detalle_retenciones as d')->leftJoin('ct_porcentaje_retenciones as p','p.id','=','d.id_porcentaje')
                ->where('d.id_retenciones',$header->id)->where('d.estado',1)->get(['d.*','p.codigo_nombre as catalog_code']);
            if ($details->isEmpty()) throw new DomainException('Retención sin detalle activo.');
            foreach ($details as $detail) {
                if (in_array((string)$detail->tipo,['1','IVA'],true)) {
                    $rate = (float)$detail->porcentaje;
                    if ($rate === 0.0) continue;
                    if ($rate !== (float)(int)$rate || !isset(AtsXmlBuilder::VAT_FIELDS[(int)$rate])) throw new DomainException('Porcentaje IVA de retención inválido.');
                    if (abs(round((float)$detail->base_imponible * $rate / 100,2) - (float)$detail->totales) > 0.011) throw new DomainException('Retención IVA no coincide con base por porcentaje.');
                    $field = AtsXmlBuilder::VAT_FIELDS[(int)$rate];
                    $data[$field] = AtsXmlBuilder::money((float)$data[$field] + (float)$detail->totales);
                } elseif (in_array((string)$detail->tipo,['2','RENTA'],true)) {
                    $air[] = AtsXmlBuilder::air((string)($detail->codigo ?: $detail->catalog_code),$detail->porcentaje,
                        $detail->base_imponible,$detail->totales,substr($header->fecha,0,10));
                } else throw new DomainException('Tipo de retención no reconocido.');
            }
        }
        $data['pagoExterior'] = ['pagoLocExt'=>'01','paisEfecPago'=>'NA','aplicConvDobTrib'=>'NA','pagExtSujRetNorLeg'=>'NA'];
        $threshold = $date >= '2023-12-20' ? 500 : 1000;
        if ($base + (float)$data['montoIva'] + (float)$data['montoIce'] > $threshold) {
            // ct_compras stores the SRI code, not the ct_tipo_pago primary key.
            $code = $row->forma_pago ?? null;
            $data['formasDePago'] = AtsXmlBuilder::payments($code === null ? [] : [$code]);
        }
        // Ficha ATS: compras no sujetas se reportan bajo 332; jamás inventar una retención.
        if (!$air) $air[] = AtsXmlBuilder::air('332',0,$base,0,$date);
        $data['air'] = $air;
        if ($retention) $data += ['estabRetencion1'=>$retention['number'][0],'ptoEmiRetencion1'=>$retention['number'][1],
            'secRetencion1'=>$retention['number'][2],'autRetencion1'=>$retention['auth'],'fechaEmiRet1'=>$retention['date']];
        return $data;
    }

    private function identification(string $id, bool $final = false): void
    {
        if ($final) return;
        if (!preg_match('/^(\d{10}|\d{10}001)$/D',$id)) throw new DomainException('Identificación local inválida. Pasaporte/exterior necesita clasificación adicional.');
    }

    private function authorized(string $key, string $type): void
    {
        if (strlen($key) !== 49) return;
        if (!DB::getSchemaBuilder()->hasTable('sri_electronic_workflows')) throw new DomainException('Falta la tabla de seguimiento SRI para comprobar la autorización.');
        $found = DB::table('sri_electronic_workflows as w')->join('de_documentos_electronicos as d','d.id','=','w.document_id')
            ->where('w.company_id',$this->company)->where('d.ruc_emisor',$this->company)
            ->where('d.clave_acceso',$key)->where('w.document_type',$type)
            ->whereIn('w.status',['authorized','emailed'])->exists();
        if (!$found) throw new DomainException('El comprobante electrónico emitido no consta autorizado en el flujo SRI.');
    }

    private function emissionDate($row): string
    {
        return substr((string)(!empty($row->f_autorizacion) ? $row->f_autorizacion : $row->fecha),0,10);
    }

    private function date(string $input): string
    {
        $value = substr($input,0,10);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d',$value);
        if (!$date || $date->format('Y-m-d') !== $value || $value < '2002-01-01') throw new DomainException('Fecha inválida: '.$value);
        return $date->format('d/m/Y');
    }
}
