<?php

namespace Sis_medico\Services;

use InvalidArgumentException;

/**
 * Structured payload used by Form 020 while keeping the existing draft table.
 * Older free-text drafts are preserved in the corresponding legacy note.
 */
class HospitalEpisodeForm020
{
    public static function emptyDocument()
    {
        return [
            'constantes' => [
                'registros' => [],
                'antropometria' => [
                    'peso' => '', 'talla' => '', 'perimetro_cefalico' => '',
                    'perimetro_abdominal' => '', 'otro' => '',
                ],
                'nota_legacy' => '',
            ],
            'ingresos' => ['registros' => [], 'nota_legacy' => ''],
            'egresos' => ['registros' => [], 'nota_legacy' => ''],
            'balance' => [
                'resumen' => [
                    'primer_ingresos' => '', 'primer_egresos' => '', 'primer_total' => '',
                    'segundo_ingresos' => '', 'segundo_egresos' => '', 'segundo_total' => '',
                    'total_ingresos' => '', 'total_egresos' => '', 'balance_24h' => '',
                    'diuresis_horaria' => '', 'gasto_urinario' => '', 'gasto_fecal' => '',
                    'perdidas_insensibles' => '',
                ],
                'alimentacion' => [
                    'dieta' => '', 'comidas' => '', 'micciones' => '', 'deposiciones' => '',
                ],
                'cuidados' => [
                    'aseo' => '', 'bano' => '', 'reposo' => '', 'posicion' => '', 'otros' => '',
                ],
                'dispositivos' => [
                    'via_central' => '', 'via_periferica' => '', 'sonda_nasogastrica' => '',
                    'sonda_vesical' => '', 'otros' => '', 'responsable' => '',
                ],
                'observaciones' => '',
                'nota_legacy' => '',
            ],
        ];
    }

    public static function forDisplay(array $payload)
    {
        $document = self::emptyDocument();
        foreach (['constantes', 'ingresos', 'egresos', 'balance'] as $section) {
            $raw = $payload[$section] ?? '';
            if (is_array($raw)) {
                $decoded = $raw;
            } else {
                $decoded = json_decode((string) $raw, true);
            }
            if (is_array($decoded)) {
                $document[$section] = array_replace_recursive($document[$section], $decoded);
            } elseif (trim((string) $raw) !== '') {
                $document[$section]['nota_legacy'] = trim((string) $raw);
            }
        }

        return self::cleanDocument($document);
    }

    public static function normalizePayload(array $payload)
    {
        $document = self::emptyDocument();
        foreach (['constantes', 'ingresos', 'egresos', 'balance'] as $section) {
            $raw = $payload[$section] ?? '';
            if ($raw === '' || $raw === null) continue;
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
            if (!is_array($decoded)) {
                throw new InvalidArgumentException('Los datos estructurados del formulario 020 no tienen un formato válido. Recargue la página e inténtelo nuevamente.');
            }
            $document[$section] = array_replace_recursive($document[$section], $decoded);
        }
        $document = self::cleanDocument($document);

        return [
            'fecha_hora' => self::text($payload['fecha_hora'] ?? '', 32),
            'constantes' => self::json($document['constantes']),
            'ingresos' => self::json($document['ingresos']),
            'egresos' => self::json($document['egresos']),
            'balance' => self::json($document['balance']),
        ];
    }

    public static function totals(array $document)
    {
        $intake = self::sumRows($document['ingresos']['registros'] ?? []);
        $output = self::sumRows($document['egresos']['registros'] ?? []);

        return ['ingresos' => $intake, 'egresos' => $output, 'balance' => $intake - $output];
    }

    public static function hasData(array $payload)
    {
        if (trim((string) ($payload['fecha_hora'] ?? '')) !== '') return true;
        $document = self::forDisplay($payload);
        if (count($document['constantes']['registros']) || count($document['ingresos']['registros']) || count($document['egresos']['registros'])) return true;
        foreach (['antropometria'] as $group) {
            if (count(array_filter($document['constantes'][$group], function ($value) { return $value !== ''; }))) return true;
        }
        foreach (['resumen', 'alimentacion', 'cuidados', 'dispositivos'] as $group) {
            if (count(array_filter($document['balance'][$group], function ($value) { return $value !== ''; }))) return true;
        }
        foreach (['constantes', 'ingresos', 'egresos', 'balance'] as $section) {
            if ($document[$section]['nota_legacy'] !== '') return true;
        }

        return $document['balance']['observaciones'] !== '';
    }

    private static function cleanDocument(array $document)
    {
        $clean = self::emptyDocument();
        $vitalKeys = ['fecha', 'dia_internacion', 'dia_postquirurgico', 'turno', 'hora', 'pulso', 'temperatura', 'frecuencia_respiratoria', 'saturacion', 'presion_sistolica', 'presion_diastolica', 'responsable'];
        $fluidKeys = ['fecha', 'hora', 'turno', 'categoria', 'tipo', 'cantidad', 'responsable'];
        $clean['constantes']['registros'] = self::cleanRows($document['constantes']['registros'] ?? [], $vitalKeys, 21);
        $clean['ingresos']['registros'] = self::cleanRows($document['ingresos']['registros'] ?? [], $fluidKeys, 30);
        $clean['egresos']['registros'] = self::cleanRows($document['egresos']['registros'] ?? [], $fluidKeys, 30);

        foreach (['antropometria', 'resumen', 'alimentacion', 'cuidados', 'dispositivos'] as $group) {
            $section = $group === 'antropometria' ? 'constantes' : 'balance';
            $source = isset($document[$section][$group]) && is_array($document[$section][$group])
                ? $document[$section][$group] : [];
            foreach (array_keys($clean[$section][$group]) as $key) {
                $clean[$section][$group][$key] = self::text($source[$key] ?? '', 180);
            }
        }
        foreach (['constantes', 'ingresos', 'egresos', 'balance'] as $section) {
            $clean[$section]['nota_legacy'] = self::text($document[$section]['nota_legacy'] ?? '', 4000);
        }
        $clean['balance']['observaciones'] = self::text($document['balance']['observaciones'] ?? '', 4000);

        return $clean;
    }

    private static function cleanRows($rows, array $keys, $limit)
    {
        if (!is_array($rows)) return [];
        $clean = [];
        foreach (array_slice($rows, 0, $limit) as $row) {
            if (!is_array($row)) continue;
            $item = [];
            foreach ($keys as $key) $item[$key] = self::text($row[$key] ?? '', 120);
            if (count(array_filter($item, function ($value) { return $value !== ''; }))) $clean[] = $item;
        }
        return $clean;
    }

    private static function sumRows($rows)
    {
        $total = 0.0;
        foreach ((array) $rows as $row) {
            $value = str_replace(',', '.', (string) ($row['cantidad'] ?? ''));
            if (is_numeric($value)) $total += (float) $value;
        }
        return $total;
    }

    private static function text($value, $limit)
    {
        if (is_array($value) || is_object($value)) return '';
        return mb_substr(trim((string) $value), 0, $limit, 'UTF-8');
    }

    private static function json(array $value)
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) throw new InvalidArgumentException('No fue posible preparar los datos del formulario 020.');
        return $json;
    }
}
