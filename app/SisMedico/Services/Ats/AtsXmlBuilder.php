<?php

namespace Sis_medico\Services\Ats;

use DOMDocument;
use DomainException;

/** Monthly local ATS. Amounts in detail remain positive; NCs are separate records. */
class AtsXmlBuilder
{
    public const VAT_FIELDS = [10=>'valRetBien10', 20=>'valRetServ20', 30=>'valorRetBienes',
        50=>'valRetServ50', 70=>'valorRetServicios', 100=>'valRetServ100'];

    public function build(array $data): string
    {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $root = $xml->appendChild($xml->createElement('iva'));
        $establishments = array_fill_keys($data['establishments'], 0);
        if (!$establishments) throw new DomainException('Confirme los establecimientos activos inscritos en el RUC.');
        $groups = []; $total = 0;
        foreach ($data['sales'] as $sale) {
            $code = $sale['tipoComprobante'];
            if (!in_array($code, ['18','04','05'], true)) throw new DomainException('Tipo ATS de venta no soportado: '.$code);
            if (!array_key_exists($sale['_establishment'], $establishments)) {
                throw new DomainException('Venta de establecimiento no confirmado: '.$sale['_establishment']);
            }
            $base = 0;
            foreach (['baseNoGraIva','baseImponible','baseImpGrav','montoIva','montoIce','valorRetIva','valorRetRenta'] as $field) {
                $sale[$field] = self::money($sale[$field]);
                if (in_array($field, ['baseNoGraIva','baseImponible','baseImpGrav'], true)) $base += self::cents($sale[$field]);
            }
            // Catálogo ATS: totalVentas sólo facturación física (F), neta de NC.
            if ($sale['tipoEmision'] === 'F') {
                $net = ($code === '04' ? -1 : 1) * $base;
                $total += $net; $establishments[$sale['_establishment']] += $net;
            } elseif ($sale['tipoEmision'] !== 'E') throw new DomainException('Tipo de emisión inválido.');
            $key = implode('|', [$sale['tpIdCliente'],$sale['idCliente'],$code,$sale['tipoEmision'],$sale['parteRelVtas'] ?? '']);
            $payments = self::payments($sale['formasDePago']);
            if (!isset($groups[$key])) {
                $groups[$key] = $sale;
                $groups[$key]['numeroComprobantes'] = 0;
                foreach (['baseNoGraIva','baseImponible','baseImpGrav','montoIva','montoIce','valorRetIva','valorRetRenta'] as $field) $groups[$key][$field] = 0;
                $groups[$key]['formasDePago'] = [];
            }
            $groups[$key]['numeroComprobantes']++;
            foreach (['baseNoGraIva','baseImponible','baseImpGrav','montoIva','montoIce','valorRetIva','valorRetRenta'] as $field) $groups[$key][$field] += self::cents($sale[$field]);
            $groups[$key]['formasDePago'] = array_values(array_unique(array_merge($groups[$key]['formasDePago'], $payments)));
        }
        $name = preg_replace('/[^A-Za-z0-9 ]/', '', \Illuminate\Support\Str::ascii($data['name']));
        $this->nodes($root, ['TipoIDInformante'=>'R','IdInformante'=>$data['company'],'razonSocial'=>$name,
            'Anio'=>(string)$data['year'],'Mes'=>sprintf('%02d', $data['month']),
            'numEstabRuc'=>sprintf('%03d', count($establishments)), 'totalVentas'=>number_format($total / 100, 2, '.', ''), 'codigoOperativo'=>'IVA']);
        if ($data['purchases']) {
            $parent = $root->appendChild($xml->createElement('compras'));
            foreach ($data['purchases'] as $purchase) {
                $child = $parent->appendChild($xml->createElement('detalleCompras'));
                foreach ($purchase as $field=>$value) {
                    if ($field === 'formasDePago') $value = ['formaPago'=>self::payments($value)];
                    if ($field === 'air') $value = ['detalleAir'=>$value];
                    $this->nodes($child, [$field=>$value]);
                }
            }
        }
        if ($groups) {
            $parent = $root->appendChild($xml->createElement('ventas'));
            foreach ($groups as $sale) {
                $child = $parent->appendChild($xml->createElement('detalleVentas'));
                foreach (['tpIdCliente','idCliente','parteRelVtas','tipoComprobante','tipoEmision','numeroComprobantes',
                    'baseNoGraIva','baseImponible','baseImpGrav','montoIva','montoIce','valorRetIva','valorRetRenta'] as $field) {
                    if (!isset($sale[$field])) continue;
                    $value = $sale[$field];
                    if (in_array($field, ['baseNoGraIva','baseImponible','baseImpGrav','montoIva','montoIce','valorRetIva','valorRetRenta'], true)) $value = number_format($value / 100, 2, '.', '');
                    $this->nodes($child, [$field=>$value]);
                }
                $this->nodes($child, ['formasDePago'=>['formaPago'=>$sale['formasDePago']]]);
            }
        }
        $parent = $root->appendChild($xml->createElement('ventasEstablecimiento'));
        foreach ($establishments as $code=>$value) {
            $child = $parent->appendChild($xml->createElement('ventaEst'));
            $this->nodes($child, ['codEstab'=>sprintf('%03d', $code),'ventasEstab'=>number_format($value / 100, 2, '.', '')]);
        }
        $result = $xml->saveXML();
        $this->validate($result);
        return $result;
    }

    public function validate(string $content): void
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            $xml = new DOMDocument();
            if (stripos($content, '<!DOCTYPE') !== false || !$xml->loadXML($content, LIBXML_NONET)
                || !$xml->schemaValidate(resource_path('sri/schemas/ats.xsd'))) {
                $errors = array_map(function ($e) { return 'Línea '.$e->line.': '.trim($e->message); }, libxml_get_errors());
                throw new DomainException('ATS no cumple el XSD oficial: '.implode(' | ', array_slice($errors, 0, 12)));
            }
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }

    private function nodes($parent, array $values): void
    {
        foreach ($values as $name=>$value) {
            $items = is_array($value) && array_is_list($value) ? $value : [$value];
            foreach ($items as $item) {
                $node = $parent->appendChild($parent->ownerDocument->createElement($name));
                if (is_array($item)) $this->nodes($node, $item);
                else $node->appendChild($parent->ownerDocument->createTextNode((string)$item));
            }
        }
    }

    public static function money($value): string
    {
        if (!is_numeric($value) || !is_finite((float)$value) || $value < 0) throw new DomainException('Importe faltante, negativo o inválido en ATS.');
        return number_format((float)$value, 2, '.', '');
    }

    public static function cents($value): int { return (int)round((float)self::money($value) * 100); }

    public static function payments(array $codes): array
    {
        if (!$codes) throw new DomainException('Falta registrar la forma de pago SRI.');
        foreach ($codes as &$code) {
            $code = str_pad(trim((string)$code), 2, '0', STR_PAD_LEFT);
            if (!in_array($code, ['01','15','16','17','18','19','20','21'], true)) throw new DomainException('Forma de pago SRI inválida: '.$code);
        }
        return array_values(array_unique($codes));
    }

    public static function number(string $number): array
    {
        if (!preg_match('/^(\d{3})-(\d{3})-(\d{1,9})$/D', trim($number), $m) || (int)$m[1] === 0 || (int)$m[2] === 0 || (int)$m[3] === 0) {
            throw new DomainException('Numeración inválida: '.$number);
        }
        return [$m[1],$m[2],str_pad($m[3],9,'0',STR_PAD_LEFT)];
    }

    public static function authorization(string $auth, string $ruc, string $type, string $number, string $date): void
    {
        if (preg_match('/^\d{10}$/D', $auth)) return;
        if (!preg_match('/^\d{49}$/D', $auth)) throw new DomainException('Falta autorización válida (10 o 49 dígitos).');
        $parts = self::number($number);
        $d = \DateTimeImmutable::createFromFormat('!Y-m-d', substr($date,0,10));
        if (!$d || $d->format('Y-m-d') !== substr($date,0,10) || substr($auth,0,8) !== $d->format('dmY')
            || substr($auth,8,2) !== $type || substr($auth,10,13) !== $ruc || substr($auth,24,15) !== implode('', $parts)) {
            throw new DomainException('La clave de acceso no coincide con fecha, tipo, RUC o numeración del documento.');
        }
        $sum = 0; $factor = 2;
        for ($i=47; $i>=0; $i--) { $sum += (int)$auth[$i] * $factor; $factor = $factor === 7 ? 2 : $factor + 1; }
        $digit = 11 - $sum % 11; $digit = $digit === 11 ? 0 : ($digit === 10 ? 1 : $digit);
        if ($digit !== (int)$auth[48]) throw new DomainException('Dígito verificador incorrecto en la clave de acceso.');
    }

    public static function air(string $code, $rate, $base, $amount, string $date): array
    {
        $catalog = json_decode(file_get_contents(resource_path('sri/ats-air-catalog.json')), true, 512, JSON_THROW_ON_ERROR);
        $rates = null;
        foreach ($catalog as $from=>$entries) if ($date >= $from) { $rates = $entries; break; }
        if (!$rates || !array_key_exists($code, $rates)) throw new DomainException('Código AIR '.$code.' no vigente en el catálogo ATS para '.$date);
        $expected = $rates[$code];
        // Special ranges/concepts require data not captured by the local ordinary-purchase form.
        if (!is_numeric($expected)) throw new DomainException('Código AIR '.$code.' requiere revisión de su régimen/porcentaje especial.');
        if (abs((float)$rate - (float)$expected) > 0.00001) throw new DomainException('Código AIR '.$code.': porcentaje registrado '.$rate.'%; catálogo vigente '.$expected.'%. Corrija el documento, no sólo el ATS.');
        if (abs(round((float)$base * (float)$rate / 100,2) - (float)$amount) > 0.011) throw new DomainException('Retención AIR: base por porcentaje no coincide con el valor retenido.');
        return ['codRetAir'=>$code,'baseImpAir'=>self::money($base),'porcentajeAir'=>self::money($rate),'valRetAir'=>self::money($amount)];
    }
}
