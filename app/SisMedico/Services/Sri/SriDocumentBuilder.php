<?php

namespace Sis_medico\Services\Sri;

use DOMDocument;
use InvalidArgumentException;

class SriDocumentBuilder
{
    private $listItems = [
        'detalles' => 'detalle', 'destinatarios' => 'destinatario',
        'totalConImpuestos' => 'totalImpuesto', 'impuestos' => 'impuesto',
        'pagos' => 'pago', 'motivos' => 'motivo', 'docsSustento' => 'docSustento',
        'retenciones' => 'retencion', 'formasDePago' => 'formaPago',
        'impuestosDocSustento' => 'impuestoDocSustento',
        'reembolsos' => 'reembolsoDetalle', 'detalleImpuestos' => 'detalleImpuesto',
        'infoAdicional' => 'campoAdicional',
        'detallesAdicionales' => 'detAdicional',
    ];

    public function build($documentType, array $payload)
    {
        if ((string) $documentType === '07') {
            foreach ($payload['docsSustento'] ?? [] as $support) {
                foreach ($support['retenciones'] ?? [] as $tax) {
                    (new WithholdingTaxValidator())->validate($tax);
                }
            }
        }
        $definition = config('sri.schemas.'.$documentType);
        if (!$definition) {
            throw new InvalidArgumentException('Tipo de comprobante SRI no soportado: '.$documentType);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $root = $document->createElement($definition['root']);
        // El namespace xsi forma parte de la estructura emitida por el
        // facturador oficial y del XML autorizado de referencia. No se agrega
        // noNamespaceSchemaLocation porque la ruta file:/... sería local al
        // servidor y nunca debe enviarse al SRI.
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('id', 'comprobante');
        $root->setAttribute('version', $definition['version']);
        $document->appendChild($root);
        foreach ($payload as $name => $value) {
            $this->append($document, $root, $name, $value);
        }
        $this->validate($document, $definition['file']);
        return $document->saveXML();
    }

    private function append(DOMDocument $document, $parent, $name, $value)
    {
        if (is_int($name)) {
            throw new InvalidArgumentException('Cada lista XML debe usar elementos con nombre explícito.');
        }
        $node = $document->createElement($name);
        $parent->appendChild($node);
        if (is_array($value) && array_key_exists('@attributes', $value)) {
            foreach ($value['@attributes'] as $attribute => $content) {
                $node->setAttribute($attribute, (string) $content);
            }
            unset($value['@attributes']);
        }
        if (is_array($value) && array_key_exists('@value', $value)) {
            $node->appendChild($document->createTextNode((string) $value['@value']));
            unset($value['@value']);
        }
        if (is_array($value)) {
            if ($this->isList($value)) {
                $itemName = isset($this->listItems[$name]) ? $this->listItems[$name] : null;
                if (!$itemName) throw new InvalidArgumentException('No se conoce el elemento XML de la lista '.$name.'.');
                foreach ($value as $item) $this->append($document, $node, $itemName, $item);
                return;
            }
            foreach ($value as $childName => $childValue) {
                $this->append($document, $node, $childName, $childValue);
            }
        } elseif ($value !== null) {
            $node->appendChild($document->createTextNode((string) $value));
        }
    }

    private function isList(array $value)
    {
        return $value && array_keys($value) === range(0, count($value) - 1);
    }

    private function validate(DOMDocument $document, $schema)
    {
        libxml_use_internal_errors(true);
        $path = resource_path('sri/schemas/'.$schema);
        if (!$document->schemaValidate($path)) {
            $messages = array_map(function ($error) { return trim($error->message); }, libxml_get_errors());
            libxml_clear_errors();
            throw new InvalidArgumentException('XML no cumple XSD: '.implode(' | ', array_unique($messages)));
        }
    }
}
