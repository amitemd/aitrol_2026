<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Services\Sri\ElectronicDocumentStateRecorder;
use Sis_medico\Services\Sri\SriElectronicWorkflowDispatcher;
use Sis_medico\v3\contable\MaestroDocumentos;
use Sis_medico\v3\contable\Ventas;

class V3SalesElectronicDocumentService
{
    /**
     * Prepara una factura para el flujo PHP: XML, firma, SRI y correo.
     */
    public function register(Ventas $sale)
    {
        if (!(int) $sale->electronica || (int) $sale->estado === 0) {
            return null;
        }

        $company = DB::table('empresa')->where('id', $sale->id_empresa)->where('estado', 1)->first();
        $electronicCompany = DB::table('de_empresa')->where('id_empresa', $sale->id_empresa)
            ->where('estado', 1)->first();
        $branch = DB::table('ct_sucursales')->where('id_empresa', $sale->id_empresa)
            ->where('codigo_sucursal', $sale->sucursal)->first();
        $customer = DB::table('ct_clientes')->where('identificacion', $sale->id_cliente)->first();

        if (!$company || !$electronicCompany) {
            throw new RuntimeException('No se puede preparar la factura SRI: falta la configuración electrónica de la empresa.');
        }

        $lines = DB::table('ct_detalle_venta')->where('id_ct_ventas', $sale->id)
            ->where('estado', 1)->orderBy('id')->get();
        if ($lines->isEmpty()) {
            throw new RuntimeException('La factura no contiene detalles para generar el XML.');
        }

        $fallbackRate = $this->fallbackVatRate($sale, $lines);
        $details = array();
        $taxGroups = array();
        $totalWithoutTaxes = 0;
        $totalDiscount = 0;

        foreach ($lines as $line) {
            $quantity = (float) $line->cantidad;
            if ($quantity <= 0) {
                throw new RuntimeException('La factura contiene una línea con cantidad inválida.');
            }

            $discount = max(0, (float) $line->descuento);
            $copay = max(0, (float) $line->copago);
            $lineBase = max(0, ($quantity * (float) $line->precio) - $discount - $copay);
            $rate = (int) $line->check_iva === 1
                ? $this->normalizeRate($line->porcentaje, $fallbackRate) : 0;
            $taxValue = round($lineBase * ($rate / 100), 2);
            $taxCode = $this->vatCode($rate);
            $groupKey = $taxCode.'|'.$this->money($rate);

            if (!isset($taxGroups[$groupKey])) {
                $taxGroups[$groupKey] = array(
                    'codigo' => '2', 'codigoPorcentaje' => $taxCode,
                    'baseImponible' => 0, 'valor' => 0,
                );
            }
            $taxGroups[$groupKey]['baseImponible'] += $lineBase;
            $taxGroups[$groupKey]['valor'] += $taxValue;

            $details[] = array(
                'codigoPrincipal' => substr((string) $line->id_ct_productos, 0, 25),
                'codigoAuxiliar' => substr((string) ($line->codigo ?: $line->id_ct_productos), 0, 25),
                'descripcion' => substr((string) ($line->descripcion_producto ?: $line->nombre), 0, 300),
                'cantidad' => $this->decimal($quantity, 6),
                'precioUnitario' => $this->decimal(max(0, ((float) $line->precio) - ($copay / $quantity)), 6),
                'descuento' => $this->money($discount),
                'precioTotalSinImpuesto' => $this->money($lineBase),
                'impuestos' => array(array(
                    'codigo' => '2', 'codigoPorcentaje' => $taxCode,
                    'tarifa' => $this->money($rate), 'baseImponible' => $this->money($lineBase),
                    'valor' => $this->money($taxValue),
                )),
            );
            $totalWithoutTaxes += $lineBase;
            $totalDiscount += $discount;
        }

        $totalTaxes = array_values(array_map(function ($tax) {
            $tax['baseImponible'] = $this->money($tax['baseImponible']);
            $tax['valor'] = $this->money($tax['valor']);
            return $tax;
        }, $taxGroups));

        $identification = preg_replace('/\D+/', '', (string) ($sale->ruc_id_cliente ?: $sale->id_cliente));
        $customerName = trim((string) ($sale->nombre_cliente ?: ($customer ? $customer->nombre : '')));
        if ($identification === '' || $customerName === '') {
            throw new RuntimeException('La factura no tiene identificación o razón social del comprador.');
        }

        $establishment = str_pad((string) $sale->sucursal, 3, '0', STR_PAD_LEFT);
        $emissionPoint = str_pad((string) $sale->punto_emision, 3, '0', STR_PAD_LEFT);
        $sequential = str_pad((string) $sale->numero, 9, '0', STR_PAD_LEFT);
        $accessKey = $this->accessKey($sale, $electronicCompany, $establishment, $emissionPoint, $sequential);
        $infoTributaria = array(
            'ambiente' => (string) $electronicCompany->ambiente,
            'tipoEmision' => '1',
            'razonSocial' => (string) $company->razonsocial,
            'nombreComercial' => (string) ($company->nombrecomercial ?: $company->razonsocial),
            'ruc' => (string) $company->id,
            'claveAcceso' => $accessKey,
            'codDoc' => '01',
            'estab' => $establishment,
            'ptoEmi' => $emissionPoint,
            'secuencial' => $sequential,
            'dirMatriz' => (string) $company->direccion,
        );

        $buyerAddress = trim((string) ($sale->direccion_cliente
            ?: ($customer ? $customer->direccion_representante : '')));
        $infoFactura = array(
            'fechaEmision' => date('d/m/Y', strtotime($sale->fecha)),
            'dirEstablecimiento' => (string) ($branch && $branch->direccion_sucursal
                ? $branch->direccion_sucursal : $company->direccion),
            'obligadoContabilidad' => !empty($electronicCompany->contabilidad) ? 'SI' : 'NO',
            'tipoIdentificacionComprador' => $this->identificationType($identification, $customer),
            'razonSocialComprador' => $customerName,
            'identificacionComprador' => $identification,
        );
        if ($buyerAddress !== '') $infoFactura['direccionComprador'] = $buyerAddress;
        $infoFactura += array(
            'totalSinImpuestos' => $this->money($totalWithoutTaxes),
            'totalDescuento' => $this->money($totalDiscount),
            'totalConImpuestos' => $totalTaxes,
            'propina' => '0.00',
            'importeTotal' => $this->money($sale->total_final),
            'moneda' => 'DOLAR',
            'pagos' => $this->payments($sale),
        );
        if (!empty($electronicCompany->contribuyente_especial)) {
            $infoFactura = array_slice($infoFactura, 0, 2, true)
                + array('contribuyenteEspecial' => (string) $electronicCompany->contribuyente_especial)
                + array_slice($infoFactura, 2, null, true);
        }

        $additional = array_filter(array_merge(array(
            array('nombre' => 'Dirección', 'valor' => (string) $sale->direccion_cliente),
            array('nombre' => 'Teléfono', 'valor' => (string) $sale->telefono_cliente),
            array('nombre' => 'Correo', 'valor' => trim((string) $sale->email_cliente)),
        ), $this->additionalInformation($sale)), function ($field) { return trim($field['valor']) !== ''; });

        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('01');
        if (!$masterId) {
            throw new RuntimeException('No existe el maestro activo para facturas SRI (01).');
        }
        $existing = De_Documentos_Electronicos::where('clave_acceso', $accessKey)->first();
        if ($existing && ((string) $existing->id_documento !== (string) $sale->id
            || (string) $existing->ruc_emisor !== (string) $company->id)) {
            throw new RuntimeException('La clave de acceso ya está registrada para otra factura; requiere revisión manual para evitar duplicados.');
        }

        $audit = array(
            'id_usuariocrea' => $sale->id_usuariocrea,
            'id_usuariomod' => $sale->id_usuariomod,
            'ip_creacion' => $sale->ip_creacion ?: '127.0.0.1',
            'ip_modificacion' => $sale->ip_modificacion ?: '127.0.0.1',
        );
        $document = De_Documentos_Electronicos::updateOrCreate(
            array('clave_acceso' => $accessKey),
            $audit + array(
                'id_de_pasos' => 1, 'id_maestro_documento' => $masterId,
                'id_documento' => $sale->id, 'ruc_receptor' => $identification,
                'ruc_emisor' => $company->id, 'establecimiento' => $establishment,
                'emision' => $emissionPoint, 'secuencial' => $sequential,
                'total' => (float) $sale->total_final,
                'infoTributaria' => json_encode($infoTributaria, JSON_UNESCAPED_UNICODE),
                'infoFactura' => json_encode($infoFactura, JSON_UNESCAPED_UNICODE),
                'detalles' => json_encode($details, JSON_UNESCAPED_UNICODE),
                'infoAdicional' => $additional
                    ? json_encode(array_values($additional), JSON_UNESCAPED_UNICODE) : null,
                'estado' => 1,
            )
        );

        $sale->clave_acceso = $accessKey;
        $sale->doc_electronico = 1;
        $sale->save();
        app(ElectronicDocumentStateRecorder::class)->record(
            $document, '01', ElectronicDocumentStateRecorder::GENERATED, 'Documento electrónico preparado y XML pendiente de validación.'
        );
        app(SriElectronicWorkflowDispatcher::class)->dispatch($document, '01', $sale->id_empresa);

        return $document;
    }

    /**
     * Las facturas creadas desde recibos no pasan por el generador legado.
     * Generar aquí la clave permite preparar el XML sin depender de ese flujo.
     */
    private function accessKey(Ventas $sale, $electronicCompany, $establishment, $emissionPoint, $sequential)
    {
        $stored = trim((string) ($sale->clave_acceso ?: $sale->nro_autorizacion));
        if (preg_match('/^\d{49}$/D', $stored) && substr($stored, 8, 2) === '01') {
            return $stored;
        }

        $date = $this->saleDate($sale->fecha);
        $generator = new ClaveAcceso();
        $generator->llenarDatos(array(
            'fecha_emision' => $date->format('Y-m-d'),
            'tipo_comprobante' => '01',
            'ruc' => $sale->id_empresa,
            'tipo_ambiente' => $electronicCompany->ambiente,
            'punto_establecimiento' => $establishment,
            'punto_emision' => $emissionPoint,
            'numero_comprobante' => $sequential,
            'codigo_numerico' => $sequential,
            'tipo_emision' => '1',
        ));
        $accessKey = trim((string) $generator->clave_acceso);
        if ($generator->isError || !preg_match('/^\d{49}$/D', $accessKey)
            || substr($accessKey, 8, 2) !== '01'
            || substr($accessKey, 0, 8) !== $date->format('dmY')) {
            throw new RuntimeException('No se pudo generar una clave de acceso tipo 01 válida para la factura.');
        }

        return $accessKey;
    }

    private function saleDate($value)
    {
        $raw = trim((string) $value);
        foreach (array('Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd-m-Y') as $format) {
            $date = \DateTimeImmutable::createFromFormat('!'.$format, $raw);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $date->format($format) === $raw) {
                return $date;
            }
        }
        throw new RuntimeException('La fecha de emisión de la factura es nula o inválida.');
    }

    private function payments(Ventas $sale)
    {
        $rows = DB::table('ct_forma_pago as payment')
            ->leftJoin('ct_tipo_pago as type', 'type.id', '=', 'payment.tipo')
            ->where('payment.id_ct_ventas', $sale->id)->where('payment.estado', 1)
            ->get(array('type.codigo_sri', 'payment.valor'));
        $payments = array();
        foreach ($rows as $row) {
            $code = str_pad((string) ($row->codigo_sri ?: '01'), 2, '0', STR_PAD_LEFT);
            if (!preg_match('/^(0[1-9]|1[0-9]|2[0-1])$/', $code)) $code = '01';
            if (!isset($payments[$code])) $payments[$code] = 0;
            $payments[$code] += max(0, (float) $row->valor);
        }
        if (!$payments) $payments['01'] = (float) $sale->total_final;

        return array_map(function ($code, $total) use ($sale) {
            return array(
                'formaPago' => $code, 'total' => $this->money($total),
                'plazo' => (string) max(0, (int) $sale->dias_plazo), 'unidadTiempo' => 'dias',
            );
        }, array_keys($payments), array_values($payments));
    }

    private function fallbackVatRate(Ventas $sale, $lines)
    {
        $base = 0;
        foreach ($lines as $line) {
            if ((int) $line->check_iva === 1) {
                $base += max(0, ((float) $line->cantidad * (float) $line->precio)
                    - (float) $line->descuento - (float) $line->copago);
            }
        }
        return $base > 0 ? round(((float) $sale->impuesto / $base) * 100, 2) : 0;
    }

    private function normalizeRate($value, $fallback)
    {
        $rate = (float) $value;
        if ($rate > 0 && $rate <= 1) $rate *= 100;
        if ($rate <= 0) $rate = (float) $fallback;
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

    private function identificationType($identification, $customer)
    {
        if ($identification === '9999999999999') return '07';
        $configured = $customer ? str_pad((string) $customer->tipo, 2, '0', STR_PAD_LEFT) : '';
        if (in_array($configured, array('04', '05', '06', '07', '08'), true)) return $configured;
        if (strlen($identification) === 13) return '04';
        if (strlen($identification) === 10) return '05';
        return '06';
    }

    private function notes($value)
    {
        $decoded = json_decode((string) $value, true);
        if (is_string($decoded)) return trim($decoded);
        if (is_array($decoded)) return trim(implode(' ', array_filter($decoded, 'is_scalar')));
        return trim((string) $value);
    }

    private function additionalInformation(Ventas $sale)
    {
        $items = json_decode((string) $sale->nota_array, true);
        $additional = array();
        $hasObservations = false;

        if (is_array($items)) {
            foreach ($items as $item) {
                if (!is_array($item)) continue;
                $name = trim((string) (isset($item['nombre']) ? $item['nombre'] : ''));
                $value = trim((string) (isset($item['valor']) ? $item['valor'] : ''));
                if ($name !== '' && $value !== '') {
                    $additional[] = array('nombre' => $name, 'valor' => $value);
                    if (strcasecmp($name, 'Observaciones') === 0) $hasObservations = true;
                }
            }
        }

        $notes = $this->notes($sale->nota_electronica);
        if ($notes !== '' && !$hasObservations) {
            $additional[] = array('nombre' => 'Observaciones', 'valor' => $notes);
        }

        return $additional;
    }

    private function money($value)
    {
        return $this->decimal($value, 2);
    }

    private function decimal($value, $decimals)
    {
        return number_format((float) $value, $decimals, '.', '');
    }
}
