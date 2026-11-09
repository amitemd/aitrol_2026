<?php

namespace Sis_medico\Services\Sri;

use Sis_medico\De_Documentos_Electronicos;
use RuntimeException;

class ElectronicDocumentPayload
{
    private $sections = [
        'infoTributaria', 'infoFactura', 'infoGuiaRemision', 'infoCompRetencion',
        'docsSustento', 'infoNotaCredito', 'infoNotaDebito', 'infoLiquidacionCompra', 'detalles', 'motivos',
        'destinatarios', 'reembolsos', 'maquinaFiscal', 'infoAdicional',
        'retencionesFactura', 'impuestoretenciones',
    ];

    public function from(De_Documentos_Electronicos $document)
    {
        $payload = [];
        foreach ($this->sections as $section) {
            if ($document->{$section} === null || trim($document->{$section}) === '') continue;
            $decoded = json_decode($document->{$section}, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new RuntimeException('La sección '.$section.' no contiene JSON válido.');
            if ($section === 'infoAdicional' && is_array($decoded) && array_keys($decoded) === range(0, count($decoded) - 1)) {
                $decoded = array_map(function ($item) {
                    return ['@attributes' => ['nombre' => data_get($item, 'nombre', '')], '@value' => data_get($item, 'valor', '')];
                }, $decoded);
            }
            $payload[$section] = $decoded;
        }
        if (empty($payload['infoTributaria'])) throw new RuntimeException('El documento no tiene infoTributaria.');
        // “RUC Proveedor” es un campo adicional (no una etiqueta XML propia
        // del XSD). Se agrega como campoAdicional en todos los comprobantes
        // electrónicos soportados; no se inventa una etiqueta
        // <rucProveedor>, porque el XML dejaría de cumplir la ficha técnica.
        $documentType = str_pad((string) data_get($payload, 'infoTributaria.codDoc'), 2, '0', STR_PAD_LEFT);
        if (array_key_exists($documentType, (array) config('sri.schemas', array()))) {
            $additional = isset($payload['infoAdicional']) && is_array($payload['infoAdicional'])
                ? $payload['infoAdicional'] : [];
            $providerRuc = (string) config('sri.provider_ruc', '');
            if (!preg_match('/^\d{13}$/', $providerRuc)) {
                throw new RuntimeException('Configure SRI_PROVIDER_RUC en el archivo .env con un RUC de 13 dígitos.');
            }
            // El valor persistido no puede ganar al .env: se elimina cualquier
            // RUC Proveedor previo y se agrega únicamente el configurado allí.
            $additional = array_values(array_filter($additional, function ($item) {
                return strcasecmp((string) data_get($item, '@attributes.nombre', ''), 'RUC Proveedor') !== 0;
            }));
            $additional[] = [
                '@attributes' => ['nombre' => 'RUC Proveedor'],
                '@value' => $providerRuc,
            ];
            $payload['infoAdicional'] = $additional;
        }
        return $payload;
    }
}
