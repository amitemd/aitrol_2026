<?php

namespace Sis_medico\Services\Sri;

use Illuminate\Support\Facades\Storage;
use Sis_medico\De_Empresa;
use Sis_medico\SriElectronicWorkflow;
use Sis_medico\Support\InvoiceAdditionalInformation;
use Sis_medico\v3\contable\Ventas;

class SriRidePdfGenerator
{
    public function generate(SriElectronicWorkflow $workflow, $xml = null)
    {
        $workflow->loadMissing('document');
        $disk = Storage::disk(config('sri.storage_disk'));
        if ($xml === null) {
            $xml = $this->authorizedXml($workflow);
        }

        $data = $this->data($xml, $workflow);
        $html = view('sri_electronico.rides.factura', $data)->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html)->setPaper('a4', 'portrait');
        $bytes = $pdf->output();
        return ['bytes' => $bytes, 'path' => null];
    }

    public function authorizedXml(SriElectronicWorkflow $workflow)
    {
        $workflow->loadMissing('document');
        $disk = Storage::disk(config('sri.storage_disk'));
        if ($workflow->authorized_path && $disk->exists($workflow->authorized_path)) {
            return $disk->get($workflow->authorized_path);
        }
        $response = json_decode((string) $workflow->document->respuestaSriAutorizacion, true);
        $authorization = data_get($response, 'RespuestaAutorizacionComprobante.autorizaciones.autorizacion');
        if (isset($authorization[0])) $authorization = $authorization[0];
        $xml = (string) data_get($authorization, 'comprobante', '');
        if ($xml === '') throw new \RuntimeException('No existe el XML autorizado en el almacenamiento ni en la respuesta del SRI.');
        return $xml;
    }

    private function data($xml, SriElectronicWorkflow $workflow)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        if (!$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw new \RuntimeException('El XML no se puede leer para generar el RIDE.');
        }
        $xpath = new \DOMXPath($dom);
        $value = function ($query, $default = '') use ($xpath) {
            $nodes = $xpath->query($query);
            return $nodes && $nodes->length ? trim($nodes->item(0)->textContent) : $default;
        };
        $values = function ($query) use ($xpath) {
            $result = [];
            $nodes = $xpath->query($query);
            if ($nodes) foreach ($nodes as $node) $result[] = trim($node->textContent);
            return $result;
        };
        $attrs = function ($query, $name, $default = '') use ($xpath) {
            $nodes = $xpath->query($query);
            return $nodes && $nodes->length && $nodes->item(0)->attributes->getNamedItem($name)
                ? trim($nodes->item(0)->attributes->getNamedItem($name)->nodeValue) : $default;
        };
        $documentType = $value('//*[local-name()="infoTributaria"]/*[local-name()="codDoc"]', '01');
        $documentTitle = $documentType === '04' ? 'Nota de crédito'
            : ($documentType === '05' ? 'Nota de débito'
            : ($documentType === '07' ? 'Retención'
            : ($documentType === '03' ? 'Liquidación de compra' : 'Factura')));
        $withholdings = [];
        $withheldCents = 0;
        foreach ($xpath->query('//*[local-name()="docsSustento"]/*[local-name()="docSustento"]') as $support) {
            foreach ($xpath->query('./*[local-name()="retenciones"]/*[local-name()="retencion"]', $support) as $tax) {
                $amount = $this->child($tax, 'valorRetenido', '0');
                $withheldCents += (int) round((float) $amount * 100);
                $withholdings[] = [
                    'documento' => $this->child($support, 'codDocSustento'),
                    'numero' => $this->child($support, 'numDocSustento'),
                    'fecha' => $this->child($support, 'fechaEmisionDocSustento'),
                    'impuesto' => ['1'=>'RENTA', '2'=>'IVA', '6'=>'ISD'][$this->child($tax, 'codigo')] ?? $this->child($tax, 'codigo'),
                    'codigo' => $this->child($tax, 'codigoRetencion'),
                    'base' => $this->child($tax, 'baseImponible'),
                    'porcentaje' => $this->child($tax, 'porcentajeRetener'), 'valor' => $amount,
                ];
            }
        }

        $details = [];
        $detailNodes = $xpath->query('//*[local-name()="detalles"]/*[local-name()="detalle"]');
        if ($detailNodes) foreach ($detailNodes as $detail) {
            $details[] = [
                'codigo' => $this->child($detail, $documentType === '04' ? 'codigoInterno' : 'codigoPrincipal'),
                'descripcion' => $this->child($detail, 'descripcion'),
                'cantidad' => $this->child($detail, 'cantidad'),
                'precio' => $this->child($detail, 'precioUnitario'),
                'descuento' => $this->child($detail, 'descuento', '0.00'),
                'total' => $this->child($detail, 'precioTotalSinImpuesto'),
            ];
        }
        if ($documentType === '05') {
            $reasonNodes = $xpath->query('//*[local-name()="motivos"]/*[local-name()="motivo"]');
            if ($reasonNodes) foreach ($reasonNodes as $reason) {
                $details[] = array(
                    'codigo' => 'ND',
                    'descripcion' => $this->child($reason, 'razon'),
                    'cantidad' => '1.00',
                    'precio' => $this->child($reason, 'valor', '0.00'),
                    'descuento' => '0.00',
                    'total' => $this->child($reason, 'valor', '0.00'),
                );
            }
        }

        // Se incrusta como data URI para que Dompdf no dependa de una URL HTTP.
        $company = De_Empresa::where('id_empresa', $workflow->company_id)->first();
        $logoData = null;
        if ($company && $company->logo) {
            $logoPath = storage_path('app/logo/' . basename($company->logo));
            if (is_file($logoPath) && is_readable($logoPath)) {
                $mime = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
                if (strpos((string) $mime, 'image/') === 0) {
                    $logoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
                }
            }
        }
        if (!$logoData) {
            $fallbackLogoPath = public_path('assets/images/invoice-logo-placeholder.svg');
            if (is_file($fallbackLogoPath) && is_readable($fallbackLogoPath)) {
                $logoData = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($fallbackLogoPath));
            }
        }

        $additional = $this->additional($xpath);
        if ($documentType === '01') {
            $sale = Ventas::where('id', $workflow->document->id_documento)
                ->where('id_empresa', $workflow->company_id)
                ->first(array('nota_array'));
            if ($sale) {
                $additional = InvoiceAdditionalInformation::merge(
                    $additional,
                    InvoiceAdditionalInformation::fromJson($sale->nota_array)
                );
            }
        }

        return [
            'workflow' => $workflow,
            'company' => $company,
            'logoData' => $logoData,
            'documentTitle' => $documentTitle,
            'documentType' => $documentType,
            'withholdings' => $withholdings,
            'withheldTotal' => $withheldCents / 100,
            'periodoFiscal' => $value('//*[local-name()="periodoFiscal"]'),
            'sujetoRetenido' => [
                'nombre' => $value('//*[local-name()="razonSocialSujetoRetenido"]'),
                'identificacion' => $value('//*[local-name()="identificacionSujetoRetenido"]'),
            ],
            'clave' => $value('//*[local-name()="claveAcceso"]', $workflow->document->clave_acceso),
            'autorizacion' => $workflow->authorization_number ?: $value('//*[local-name()="numeroAutorizacion"]', ''),
            'fechaAutorizacion' => $value('//*[local-name()="fechaAutorizacion"]', (string) $workflow->authorized_at),
            'ambiente' => $value('//*[local-name()="ambiente"]', ''),
            'emision' => $value('//*[local-name()="tipoEmision"]', ''),
            'emisor' => [
                'razonSocial' => $value('//*[local-name()="razonSocial"]', ''),
                'nombreComercial' => $value('//*[local-name()="nombreComercial"]', ''),
                'ruc' => $value('//*[local-name()="ruc"]', $workflow->company_id),
                'direccion' => $value('//*[local-name()="dirMatriz"]', ''),
                'establecimiento' => $value('//*[local-name()="estab"]', ''),
                'puntoEmision' => $value('//*[local-name()="ptoEmi"]', ''),
            ],
            'comprobante' => [
                'fecha' => $value('//*[local-name()="fechaEmision"]', ''),
                'numero' => $value('//*[local-name()="estab"]', '').'-'.$value('//*[local-name()="ptoEmi"]', '').'-'.$value('//*[local-name()="secuencial"]', ''),
                'direccion' => $value('//*[local-name()="dirEstablecimiento"]', ''),
            ],
            'modifiedDocument' => [
                'type' => [
                    '01' => 'Factura',
                    '03' => 'Liquidación de compra',
                    '04' => 'Nota de crédito',
                    '05' => 'Nota de débito',
                ][$value('//*[local-name()="codDocModificado"]', '')] ?? $value('//*[local-name()="codDocModificado"]', ''),
                'number' => $value('//*[local-name()="numDocModificado"]', ''),
                'date' => $value('//*[local-name()="fechaEmisionDocSustento"]', ''),
            ],
            'cliente' => [
                'identificacion' => $value($documentType === '03'
                    ? '//*[local-name()="identificacionProveedor"]' : '//*[local-name()="identificacionComprador"]', ''),
                'nombre' => $value($documentType === '03'
                    ? '//*[local-name()="razonSocialProveedor"]' : '//*[local-name()="razonSocialComprador"]', ''),
                'direccion' => $value($documentType === '03'
                    ? '//*[local-name()="direccionProveedor"]' : '//*[local-name()="direccionComprador"]', ''),
                'telefono' => $value('//*[local-name()="campoAdicional" and @nombre="Teléfono"]', ''),
                'email' => $value('//*[local-name()="campoAdicional" and (@nombre="Correo" or @nombre="Email")]', ''),
            ],
            'details' => $details,
            'totals' => [
                'subtotal0' => $value('//*[local-name()="totalSinImpuestos"]', '0.00'),
                'subtotal12' => $value($documentType === '05'
                    ? '//*[local-name()="infoNotaDebito"]/*[local-name()="impuestos"]/*[local-name()="impuesto"]/*[local-name()="baseImponible"]'
                    : '//*[local-name()="totalImpuesto"]//*[local-name()="baseImponible"]', '0.00'),
                'descuento' => $value('//*[local-name()="totalDescuento"]', '0.00'),
                'iva' => $value($documentType === '05'
                    ? '//*[local-name()="infoNotaDebito"]/*[local-name()="impuestos"]/*[local-name()="impuesto"]/*[local-name()="valor"]'
                    : '//*[local-name()="totalImpuesto"]//*[local-name()="valor"]', '0.00'),
                'total' => $value($documentType === '04' ? '//*[local-name()="valorModificacion"]'
                    : ($documentType === '05' ? '//*[local-name()="valorTotal"]' : '//*[local-name()="importeTotal"]'), '0.00'),
            ],
            'payment' => $value('//*[local-name()="pago"]//*[local-name()="total"]', ''),
            'additional' => $additional,
        ];
    }

    private function child(\DOMNode $parent, $name, $default = '')
    {
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === $name) return trim($child->textContent);
        }
        return $default;
    }

    private function additional(\DOMXPath $xpath)
    {
        $result = [];
        $nodes = $xpath->query('//*[local-name()="campoAdicional"]');
        if ($nodes) foreach ($nodes as $node) {
            $name = $node->attributes->getNamedItem('nombre');
            if ($name) $result[] = ['name' => trim($name->nodeValue), 'value' => trim($node->textContent)];
        }
        return $result;
    }
}
