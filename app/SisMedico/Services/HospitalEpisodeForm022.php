<?php

namespace Sis_medico\Services;

use InvalidArgumentException;

/** Structured storage for the medication administration Form 022. */
class HospitalEpisodeForm022
{
    public static function emptyDocument()
    {
        return [
            'alergias' => ['estado' => '', 'descripcion' => '', 'nota_legacy' => ''],
            'medicamentos' => ['fechas' => ['', '', '', ''], 'registros' => [], 'nota_legacy' => ''],
        ];
    }

    public static function forDisplay(array $payload)
    {
        $document = self::emptyDocument();
        foreach (['alergias', 'medicamentos'] as $section) {
            $raw = $payload[$section] ?? '';
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
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
        foreach (['alergias', 'medicamentos'] as $section) {
            $raw = $payload[$section] ?? '';
            if ($raw === '' || $raw === null) continue;
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
            if (!is_array($decoded)) {
                throw new InvalidArgumentException('Los datos estructurados del formulario 022 no tienen un formato válido. Recargue la página e inténtelo nuevamente.');
            }
            $document[$section] = array_replace_recursive($document[$section], $decoded);
        }
        $document = self::cleanDocument($document);

        return [
            'alergias' => self::json($document['alergias']),
            'medicamentos' => self::json($document['medicamentos']),
        ];
    }

    public static function hasData(array $payload)
    {
        $document = self::forDisplay($payload);
        if ($document['alergias']['estado'] !== '' || $document['alergias']['descripcion'] !== '') return true;
        if (count(array_filter($document['medicamentos']['fechas'], function ($date) { return $date !== ''; }))) return true;
        if (count($document['medicamentos']['registros'])) return true;

        return $document['alergias']['nota_legacy'] !== '' || $document['medicamentos']['nota_legacy'] !== '';
    }

    /**
     * Replaces every linked prescription with the server-side medical order.
     * Nursing may record administrations, but cannot change the physician's
     * medication, dose, route or frequency through the Form 022 payload.
     */
    public static function reconcilePrescriptions(array $payload, array $availablePrescriptions)
    {
        $document = self::forDisplay($payload);
        $available = [];
        foreach ($availablePrescriptions as $prescription) {
            if (!is_array($prescription) || empty($prescription['prescripcion'])) continue;
            $available[(string) $prescription['prescripcion']] = $prescription;
        }

        // Toda administración verificada en eMAR forma parte del 022 aunque
        // enfermería aún no haya creado/guardado manualmente un borrador.
        $selected = [];
        foreach ($document['medicamentos']['registros'] as $record) {
            if (!empty($record['prescripcion'])) $selected[(string) $record['prescripcion']] = true;
        }
        foreach ($available as $key => $prescription) {
            if (isset($selected[$key]) || empty($prescription['administraciones'])
                || count($document['medicamentos']['registros']) >= 6) continue;
            $document['medicamentos']['registros'][] = [
                'prescripcion' => $key,
                'medicamento' => '', 'dosis' => '', 'via' => '', 'frecuencia' => '',
                'prescrita_por' => '', 'fecha_prescripcion' => '', 'administraciones' => [],
            ];
        }

        $dates = [];
        foreach ($available as $prescription) {
            foreach (($prescription['administraciones'] ?? []) as $administration) {
                $date = self::text($administration['fecha'] ?? '', 16);
                if ($date !== '' && !in_array($date, $dates, true)) $dates[] = $date;
            }
        }
        sort($dates);
        $dates = array_slice($dates, -4);
        foreach ($document['medicamentos']['fechas'] as $existingDate) {
            $existingDate = self::text($existingDate, 16);
            if ($existingDate !== '' && !in_array($existingDate, $dates, true) && count($dates) < 4) $dates[] = $existingDate;
        }
        sort($dates);
        while (count($dates) < 4) $dates[] = '';
        $document['medicamentos']['fechas'] = $dates;

        foreach ($document['medicamentos']['registros'] as &$record) {
            $key = (string) ($record['prescripcion'] ?? '');
            if ($key === '') continue;
            if (!isset($available[$key])) {
                throw new InvalidArgumentException('Una prescripción seleccionada no pertenece a este episodio o ya no está disponible. Recargue el formulario 022.');
            }
            foreach (['prescripcion', 'medicamento', 'dosis', 'via', 'frecuencia', 'prescrita_por', 'fecha_prescripcion'] as $field) {
                $record[$field] = self::text($available[$key][$field] ?? '', $field === 'medicamento' ? 250 : 191);
            }
            if (array_key_exists('administraciones', $available[$key])) {
                $record['administraciones'] = [];
                foreach ($available[$key]['administraciones'] as $administration) {
                    $date = self::text($administration['fecha'] ?? '', 16);
                    $dateIndex = array_search($date, $document['medicamentos']['fechas'], true);
                    if ($dateIndex === false) continue;
                    $record['administraciones'][] = [
                        'fecha' => (string) $dateIndex,
                        'hora' => self::text($administration['hora'] ?? '', 16),
                        'responsable' => self::text($administration['responsable'] ?? '', 120),
                        'emar_id' => self::text($administration['emar_id'] ?? '', 32),
                        'programada' => self::text($administration['programada'] ?? '', 32),
                    ];
                }
            }
        }
        unset($record);

        return [
            'alergias' => self::json($document['alergias']),
            'medicamentos' => self::json($document['medicamentos']),
        ];
    }

    private static function cleanDocument(array $document)
    {
        $clean = self::emptyDocument();
        $allergy = isset($document['alergias']) && is_array($document['alergias']) ? $document['alergias'] : [];
        $state = mb_strtoupper(self::text($allergy['estado'] ?? '', 2), 'UTF-8');
        $clean['alergias']['estado'] = in_array($state, ['SI', 'NO'], true) ? $state : '';
        $clean['alergias']['descripcion'] = self::text($allergy['descripcion'] ?? '', 1000);
        $clean['alergias']['nota_legacy'] = self::text($allergy['nota_legacy'] ?? '', 4000);

        $medications = isset($document['medicamentos']) && is_array($document['medicamentos']) ? $document['medicamentos'] : [];
        $dates = isset($medications['fechas']) && is_array($medications['fechas']) ? array_values($medications['fechas']) : [];
        for ($index = 0; $index < 4; $index++) $clean['medicamentos']['fechas'][$index] = self::text($dates[$index] ?? '', 16);
        $clean['medicamentos']['nota_legacy'] = self::text($medications['nota_legacy'] ?? '', 4000);

        $records = isset($medications['registros']) && is_array($medications['registros']) ? $medications['registros'] : [];
        foreach (array_slice($records, 0, 6) as $record) {
            if (!is_array($record)) continue;
            $item = [
                'prescripcion' => self::text($record['prescripcion'] ?? '', 120),
                'medicamento' => self::text($record['medicamento'] ?? '', 250),
                'dosis' => self::text($record['dosis'] ?? '', 120),
                'via' => self::text($record['via'] ?? '', 120),
                'frecuencia' => self::text($record['frecuencia'] ?? '', 120),
                'prescrita_por' => self::text($record['prescrita_por'] ?? '', 191),
                'fecha_prescripcion' => self::text($record['fecha_prescripcion'] ?? '', 32),
                'administraciones' => [],
            ];
            $administrations = isset($record['administraciones']) && is_array($record['administraciones']) ? $record['administraciones'] : [];
            $dateCounts = [0, 0, 0, 0];
            foreach (array_slice($administrations, 0, 48) as $administration) {
                if (!is_array($administration)) continue;
                $dateIndex = (string) ($administration['fecha'] ?? '');
                if (!in_array($dateIndex, ['0', '1', '2', '3'], true)) $dateIndex = '';
                if ($dateIndex !== '' && $dateCounts[(int) $dateIndex] >= 12) continue;
                $admin = [
                    'fecha' => $dateIndex,
                    'hora' => self::text($administration['hora'] ?? '', 16),
                    'responsable' => self::text($administration['responsable'] ?? '', 120),
                    'emar_id' => self::text($administration['emar_id'] ?? '', 32),
                    'programada' => self::text($administration['programada'] ?? '', 32),
                ];
                if ($admin['fecha'] !== '' || $admin['hora'] !== '' || $admin['responsable'] !== '') {
                    $item['administraciones'][] = $admin;
                    if ($dateIndex !== '') $dateCounts[(int) $dateIndex]++;
                }
            }
            if ($item['prescripcion'] === '') {
                $item['prescrita_por'] = '';
                $item['fecha_prescripcion'] = '';
            }
            if ($item['medicamento'] !== '' || $item['dosis'] !== '' || $item['via'] !== '' || $item['frecuencia'] !== '' || count($item['administraciones'])) {
                $clean['medicamentos']['registros'][] = $item;
            }
        }

        return $clean;
    }

    private static function text($value, $limit)
    {
        if (is_array($value) || is_object($value)) return '';
        return mb_substr(trim((string) $value), 0, $limit, 'UTF-8');
    }

    private static function json(array $value)
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) throw new InvalidArgumentException('No fue posible preparar los datos del formulario 022.');
        return $json;
    }
}
