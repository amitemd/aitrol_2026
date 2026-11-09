<?php

namespace Sis_medico\Services;

class HospitalEpisodePhysicalExam
{
    public static function groups()
    {
        return [
            'Regional' => [
                '1R' => 'Piel - faneras', '2R' => 'Cabeza', '3R' => 'Ojos',
                '4R' => 'Oídos', '5R' => 'Nariz', '6R' => 'Boca',
                '7R' => 'Orofaringe', '8R' => 'Cuello', '9R' => 'Axilas - mamas',
                '10R' => 'Tórax', '11R' => 'Abdomen', '12R' => 'Columna vertebral',
                '13R' => 'Ingle - periné', '14R' => 'Miembros superiores', '15R' => 'Miembros inferiores',
            ],
            'Sistémico' => [
                '1S' => 'Órganos de los sentidos', '2S' => 'Respiratorio', '3S' => 'Cardio - vascular',
                '4S' => 'Digestivo', '5S' => 'Genital', '6S' => 'Urinario',
                '7S' => 'Músculo - esquelético', '8S' => 'Endócrino', '9S' => 'Hemo - linfático',
                '10S' => 'Neurológico',
            ],
        ];
    }

    public static function normalize($selected)
    {
        if ($selected === null || $selected === '') return '';
        $options = array_merge(...array_values(self::groups()));
        if (!is_array($selected) || count($selected) > count($options)) {
            throw new \InvalidArgumentException('Seleccione las casillas válidas del examen físico.');
        }
        foreach ($selected as $code) {
            if (!is_string($code) || !isset($options[$code])) {
                throw new \InvalidArgumentException('Seleccione las casillas válidas del examen físico.');
            }
        }
        $selected = array_values(array_intersect(array_keys($options), $selected));
        return $selected ? json_encode($selected) : '';
    }

    public static function pdfBoxes()
    {
        // White answer cells on the 1700 x 2200 reference page, in pixels.
        $columns = [[454, 33], [666, 33], [913, 33], [1284, 33], [1549, 31]];
        $rows = [[483, 20], [505, 30], [537, 29], [568, 29], [599, 36]];
        $codes = array_keys(array_merge(...array_values(self::groups())));
        $boxes = [];
        foreach ($codes as $index => $code) {
            list($left, $width) = $columns[intdiv($index, 5)];
            list($top, $height) = $rows[$index % 5];
            $boxes[$code] = [$left, $top, $width, $height];
        }
        return $boxes;
    }

    public static function forDisplay($raw)
    {
        try {
            $selected = is_string($raw) && $raw !== '' ? json_decode($raw, true) : $raw;
            $normalized = self::normalize($selected);
            return $normalized === '' ? [] : json_decode($normalized, true);
        } catch (\InvalidArgumentException $exception) {
            return [];
        }
    }

    public static function bodyChecks(array $marks)
    {
        $regions = self::groups()['Regional'];
        $selected = [];
        foreach ($marks as $mark) {
            $region = $mark['region'] ?? HospitalEpisodeBodyDiagram::regionAt($mark['view'], $mark['x'], $mark['y']);
            if (isset($regions[$region])) $selected[] = $region;
        }
        return array_values(array_intersect(array_keys($regions), $selected));
    }

    public static function effectiveChecks($manual, array $marks)
    {
        return self::forDisplay(array_values(array_unique(array_merge(self::forDisplay($manual), self::bodyChecks($marks)))));
    }
}
