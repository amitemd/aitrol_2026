<?php

namespace Sis_medico\Services\Sri;

use RuntimeException;

class WithholdingTaxValidator
{
    // Tabla 20 de la ficha técnica SRI: el código no es el porcentaje.
    private const VAT = ['9' => 10, '10' => 20, '1' => 30, '11' => 50,
        '2' => 70, '3' => 100, '7' => 0, '8' => 0];

    public function code($localType, $code, $rate)
    {
        $type = (string) $localType;
        $code = trim((string) $code);
        if (!in_array($type, ['1', '2'], true)) {
            throw new RuntimeException('Tipo de retención inválido: se esperaba IVA (1) o renta (2).');
        }
        if (!is_numeric($rate) || !is_finite((float) $rate) || $rate < 0 || $rate > 100) {
            throw new RuntimeException('Porcentaje de retención inválido.');
        }
        if ($type === '1') {
            if (isset(self::VAT[$code]) && abs(self::VAT[$code] - (float) $rate) < 0.00001) return $code;
            // Reparar únicamente la confusión inequívoca porcentaje/código.
            // 0 no permite distinguir retención cero de no procede retención.
            if (is_numeric($code) && (float) $code === (float) $rate && $rate > 0) {
                foreach (self::VAT as $sriCode => $percentage) {
                    if (abs($percentage - (float) $rate) < 0.00001) return (string) $sriCode;
                }
            }
            throw new RuntimeException("Código IVA {$code} incompatible con la retención {$rate}%. Revise ct_porcentaje_retenciones y su detalle.");
        }
        // Renta usa códigos por concepto, no una equivalencia por porcentaje.
        if (!preg_match('/^[0-9]{3}[0-9A-Z]{0,2}$/D', $code)) {
            throw new RuntimeException('Código de retención de renta inválido: configure el código del concepto SRI, no el porcentaje.');
        }
        return $code;
    }

    public function fromDetail($detail)
    {
        if ($detail->tax_type === null || (string) $detail->tax_type !== (string) $detail->tipo) {
            throw new RuntimeException('El detalle de retención no corresponde a un impuesto del catálogo.');
        }
        $code = $this->code($detail->tipo, $detail->codigo ?: $detail->codigo_nombre, $detail->porcentaje);
        $tax = ['codigo' => (string) $detail->tipo === '1' ? '2' : '1',
            'codigoRetencion' => $code, 'baseImponible' => $detail->base_imponible,
            'porcentajeRetener' => $detail->porcentaje, 'valorRetenido' => $detail->totales];
        $this->validate($tax);
        foreach (['baseImponible', 'porcentajeRetener', 'valorRetenido'] as $field) {
            $tax[$field] = number_format((float) $tax[$field], 2, '.', '');
        }
        return $tax;
    }

    public function validate(array $tax)
    {
        $type = (string) ($tax['codigo'] ?? '');
        if (!in_array($type, ['1', '2'], true)) throw new RuntimeException('Impuesto de retención no soportado.');
        foreach (['baseImponible', 'porcentajeRetener', 'valorRetenido'] as $field) {
            if (!isset($tax[$field]) || !is_numeric($tax[$field]) || !is_finite((float) $tax[$field]) || $tax[$field] < 0) {
                throw new RuntimeException('Valor inválido en retención: '.$field);
            }
        }
        $code = (string) ($tax['codigoRetencion'] ?? '');
        if ($this->code($type === '2' ? '1' : '2', $code, $tax['porcentajeRetener']) !== $code) {
            throw new RuntimeException('El XML contiene un porcentaje en lugar del código SRI. Regenere la retención desde su registro.');
        }
        $expected = (int) round(round((float) $tax['baseImponible'], 2) * (float) $tax['porcentajeRetener']);
        if ($expected !== (int) round((float) $tax['valorRetenido'] * 100)) {
            throw new RuntimeException('El valor retenido no coincide con base × porcentaje / 100.');
        }
    }
}
