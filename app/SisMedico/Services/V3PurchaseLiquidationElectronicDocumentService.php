<?php

namespace Sis_medico\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Services\Sri\ElectronicDocumentStateRecorder;
use Sis_medico\Services\Sri\SriElectronicWorkflowDispatcher;
use Sis_medico\v3\contable\MaestroDocumentos;

class V3PurchaseLiquidationElectronicDocumentService
{
    public function register(Model $purchase)
    {
        if ($purchase->getTable() !== 'ct_compras') {
            throw new RuntimeException('El documento recibido no corresponde a una compra o liquidación.');
        }

        if (!(int) $purchase->electronica || (int) $purchase->estado === 0
            || (string) $purchase->tipo_comprobante !== '03') {
            return null;
        }

        $company = DB::table('empresa')->where('id', $purchase->id_empresa)->where('estado', 1)->first();
        $electronicCompany = DB::table('de_empresa')->where('id_empresa', $purchase->id_empresa)
            ->where('estado', 1)->first();
        $supplier = DB::table('proveedor as proveedor')
            ->leftJoin('personas', 'personas.id', '=', 'proveedor.persona_id')
            ->where('proveedor.id', $purchase->proveedor)
            ->select('proveedor.*', 'personas.email as persona_email', 'personas.direccion as persona_direccion')
            ->first();
        $branch = DB::table('ct_sucursales')->where('id_empresa', $purchase->id_empresa)
            ->where('codigo_sucursal', $purchase->sucursal)->first();
        if (!$company || !$electronicCompany || !$supplier) {
            throw new RuntimeException('No se puede preparar la liquidación SRI: faltan datos de empresa, firma o proveedor.');
        }

        $details = DB::table('ct_detalle_compra as detail')
            ->leftJoin('ct_productos as product', function ($join) use ($purchase) {
                $join->on('product.codigo', '=', 'detail.codigo')
                    ->where('product.id_empresa', '=', $purchase->id_empresa);
            })
            ->where('detail.id_ct_compras', $purchase->id)
            ->where('detail.estado', 1)
            ->orderBy('detail.id')
            ->select('detail.*', 'product.descripcion as producto_descripcion')
            ->get();
        if ($details->isEmpty()) {
            throw new RuntimeException('La liquidación no contiene detalles para generar el XML.');
        }

        $xmlDetails = array();
        $taxGroups = array();
        $totalWithoutTaxes = 0;
        $totalDiscount = 0;
        foreach ($details as $detail) {
            $quantity = (float) $detail->cantidad;
            if ($quantity <= 0) throw new RuntimeException('La liquidación contiene una cantidad inválida.');
            $description = $this->detailDescription($detail);
            if ($description === '') {
                throw new RuntimeException('La liquidación contiene un detalle sin descripción para el XML del SRI.');
            }
            $discount = max(0, (float) $detail->descuento);
            $base = max(0, ($quantity * (float) $detail->precio) - $discount);
            $rate = (int) $detail->iva === 1 ? $this->rate($detail->porcentaje, $purchase, $base) : 0;
            $taxCode = $this->vatCode($rate);
            $tax = round($base * ($rate / 100), 2);
            $group = $taxCode.'|'.$this->money($rate);
            if (!isset($taxGroups[$group])) {
                $taxGroups[$group] = array('codigo' => '2', 'codigoPorcentaje' => $taxCode,
                    'baseImponible' => 0, 'valor' => 0);
            }
            $taxGroups[$group]['baseImponible'] += $base;
            $taxGroups[$group]['valor'] += $tax;
            $xmlDetails[] = array(
                'codigoPrincipal' => substr((string) $detail->codigo, 0, 25),
                'codigoAuxiliar' => substr((string) ($detail->codigoref ?: $detail->codigo), 0, 25),
                'descripcion' => $this->limit($description, 300),
                'cantidad' => $this->decimal($quantity, 6),
                'precioUnitario' => $this->decimal(max(0, (float) $detail->precio), 6),
                'descuento' => $this->money($discount),
                'precioTotalSinImpuesto' => $this->money($base),
                'impuestos' => array(array('codigo' => '2', 'codigoPorcentaje' => $taxCode,
                    'tarifa' => $this->money($rate), 'baseImponible' => $this->money($base),
                    'valor' => $this->money($tax))),
            );
            $totalWithoutTaxes += $base;
            $totalDiscount += $discount;
        }
        $totalTaxes = array_values(array_map(function ($tax) {
            $tax['baseImponible'] = $this->money($tax['baseImponible']);
            $tax['valor'] = $this->money($tax['valor']);
            return $tax;
        }, $taxGroups));

        $identification = preg_replace('/\D+/', '', (string) $supplier->id);
        $supplierName = trim((string) ($supplier->nombrecomercial ?: $supplier->razonsocial));
        if ($identification === '' || $supplierName === '') {
            throw new RuntimeException('La liquidación no tiene identificación o razón social del proveedor.');
        }

        $establishment = str_pad((string) $purchase->sucursal, 3, '0', STR_PAD_LEFT);
        $emissionPoint = str_pad((string) $purchase->punto_emision, 3, '0', STR_PAD_LEFT);
        $sequential = str_pad((string) $purchase->secuencia_f, 9, '0', STR_PAD_LEFT);
        $accessKey = $this->accessKey($purchase, $electronicCompany, $establishment, $emissionPoint, $sequential);
        $infoTributaria = array(
            'ambiente' => (string) $electronicCompany->ambiente, 'tipoEmision' => '1',
            'razonSocial' => (string) $company->razonsocial,
            'nombreComercial' => (string) ($company->nombrecomercial ?: $company->razonsocial),
            'ruc' => (string) $company->id, 'claveAcceso' => $accessKey, 'codDoc' => '03',
            'estab' => $establishment, 'ptoEmi' => $emissionPoint, 'secuencial' => $sequential,
            'dirMatriz' => (string) $company->direccion,
        );
        $supplierAddress = trim((string) ($supplier->direccion ?: $supplier->persona_direccion));
        $infoLiquidation = array(
            'fechaEmision' => date('d/m/Y', strtotime($purchase->fecha)),
            'dirEstablecimiento' => (string) (($branch && $branch->direccion_sucursal)
                ? $branch->direccion_sucursal : $company->direccion),
            'obligadoContabilidad' => !empty($electronicCompany->contabilidad) ? 'SI' : 'NO',
            'tipoIdentificacionProveedor' => $this->identificationType($identification, $supplier),
            'razonSocialProveedor' => $supplierName, 'identificacionProveedor' => $identification,
        );
        if (!empty($electronicCompany->contribuyente_especial)) {
            $infoLiquidation = array_slice($infoLiquidation, 0, 2, true)
                + array('contribuyenteEspecial' => (string) $electronicCompany->contribuyente_especial)
                + array_slice($infoLiquidation, 2, null, true);
        }
        if ($supplierAddress !== '') $infoLiquidation['direccionProveedor'] = $supplierAddress;
        $infoLiquidation += array(
            'totalSinImpuestos' => $this->money($totalWithoutTaxes),
            'totalDescuento' => $this->money($totalDiscount), 'totalConImpuestos' => $totalTaxes,
            'importeTotal' => $this->money($purchase->total_final), 'moneda' => 'DOLAR',
            'pagos' => array(array('formaPago' => $this->paymentCode($purchase->forma_pago),
                'total' => $this->money($purchase->total_final),
                'plazo' => (string) max(0, (int) DB::table('ct_termino')->where('id', $purchase->termino)->value('dias')),
                'unidadTiempo' => 'dias')),
        );

        $email = trim((string) ($supplier->persona_email ?: $supplier->email));
        $additional = array_values(array_filter(array(
            array('nombre' => 'Email', 'valor' => $email),
            array('nombre' => 'Observación', 'valor' => trim((string) $purchase->observacion)),
        ), function ($field) { return $field['valor'] !== ''; }));
        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('03');
        if (!$masterId) throw new RuntimeException('No existe el maestro activo para liquidaciones de compra SRI (03).');
        $existing = De_Documentos_Electronicos::where('clave_acceso', $accessKey)->first();
        if ($existing && ((string) $existing->id_documento !== (string) $purchase->id
            || (string) $existing->ruc_emisor !== (string) $company->id
            || (string) $existing->id_maestro_documento !== (string) $masterId)) {
            throw new RuntimeException('La clave de acceso ya pertenece a otro comprobante electrónico.');
        }

        $document = De_Documentos_Electronicos::updateOrCreate(
            array('clave_acceso' => $accessKey),
            array('id_de_pasos' => 1, 'id_maestro_documento' => $masterId,
                'id_documento' => $purchase->id, 'ruc_receptor' => $identification,
                'ruc_emisor' => $company->id, 'establecimiento' => $establishment,
                'emision' => $emissionPoint, 'secuencial' => $sequential,
                'total' => (float) $purchase->total_final,
                'infoTributaria' => json_encode($infoTributaria, JSON_UNESCAPED_UNICODE),
                'infoLiquidacionCompra' => json_encode($infoLiquidation, JSON_UNESCAPED_UNICODE),
                'detalles' => json_encode($xmlDetails, JSON_UNESCAPED_UNICODE),
                'infoAdicional' => $additional ? json_encode($additional, JSON_UNESCAPED_UNICODE) : null,
                'estado' => 1, 'id_usuariocrea' => $purchase->id_usuariocrea,
                'id_usuariomod' => $purchase->id_usuariomod,
                'ip_creacion' => $purchase->ip_creacion ?: '127.0.0.1',
                'ip_modificacion' => $purchase->ip_modificacion ?: '127.0.0.1')
        );
        $purchase->nro_autorizacion = $accessKey;
        $purchase->clave_acceso = $accessKey;
        $purchase->doc_electronico = 1;
        $purchase->save();
        app(ElectronicDocumentStateRecorder::class)->record(
            $document, '03', ElectronicDocumentStateRecorder::GENERATED,
            'Liquidación electrónica preparada y XML pendiente de validación.'
        );
        app(SriElectronicWorkflowDispatcher::class)->dispatch($document, '03', $purchase->id_empresa);
        return $document;
    }

    private function accessKey($purchase, $electronicCompany, $establishment, $emissionPoint, $sequential)
    {
        $stored = trim((string) ($purchase->clave_acceso ?: $purchase->nro_autorizacion));
        if (preg_match('/^\d{49}$/D', $stored) && substr($stored, 8, 2) === '03') return $stored;
        $generator = new ClaveAcceso();
        $generator->llenarDatos(array('fecha_emision' => date('d-m-Y', strtotime($purchase->fecha)),
            'tipo_comprobante' => '03', 'ruc' => $purchase->id_empresa,
            'tipo_ambiente' => $electronicCompany->ambiente,
            'punto_establecimiento' => $establishment, 'punto_emision' => $emissionPoint,
            'numero_comprobante' => $sequential, 'codigo_numerico' => $sequential, 'tipo_emision' => '1'));
        if ($generator->isError || !preg_match('/^\d{49}$/D', (string) $generator->clave_acceso)) {
            throw new RuntimeException('No se pudo generar la clave de acceso de la liquidación.');
        }
        return $generator->clave_acceso;
    }

    private function rate($stored, $purchase, $base)
    {
        $rate = (float) $stored;
        if ($rate > 0 && $rate <= 1) $rate *= 100;
        if ($rate <= 0 && (float) $purchase->subtotal_12 > 0) {
            $rate = ((float) $purchase->iva_total / (float) $purchase->subtotal_12) * 100;
        }
        return round($rate, 2);
    }

    private function vatCode($rate)
    {
        $codes = array('0' => '0', '5' => '5', '12' => '2', '13' => '10', '14' => '3', '15' => '4');
        $key = (string) (int) round($rate);
        if (!isset($codes[$key]) || abs((float) $rate - (float) $key) > 0.01) {
            throw new RuntimeException('La tarifa IVA '.$this->money($rate).'% no tiene código SRI configurado.');
        }
        return $codes[$key];
    }

    private function identificationType($identification, $supplier)
    {
        $configured = str_pad((string) $supplier->tipo, 2, '0', STR_PAD_LEFT);
        if (in_array($configured, array('04', '05', '06', '07', '08'), true)) return $configured;
        return strlen($identification) === 13 ? '04' : (strlen($identification) === 10 ? '05' : '06');
    }

    private function paymentCode($value)
    {
        $code = str_pad(preg_replace('/\D+/', '', (string) $value), 2, '0', STR_PAD_LEFT);
        return preg_match('/^(0[1-9]|1[0-9]|2[0-1])$/D', $code) ? $code : '20';
    }

    private function detailDescription($detail)
    {
        foreach (array('producto_descripcion', 'nombre', 'detalle', 'codigo') as $property) {
            if (isset($detail->{$property}) && trim((string) $detail->{$property}) !== '') {
                return preg_replace('/[\r\n]+/', ' ', trim((string) $detail->{$property}));
            }
        }

        return '';
    }

    private function limit($value, $length)
    {
        return function_exists('mb_substr')
            ? mb_substr((string) $value, 0, $length, 'UTF-8')
            : substr((string) $value, 0, $length);
    }

    private function money($value) { return $this->decimal($value, 2); }
    private function decimal($value, $places) { return number_format((float) $value, $places, '.', ''); }
}
