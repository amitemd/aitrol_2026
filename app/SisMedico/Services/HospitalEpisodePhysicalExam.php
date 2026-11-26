<?php

namespace Sis_medico\Services;

class HospitalEpisodePhysicalExam
{
    public static function fieldGroups()
    {
        return [
            'Regional' => [
                '1R' => ['field' => 'piel_faneras', 'label' => 'Piel - faneras'],
                '2R' => ['field' => 'cabeza', 'label' => 'Cabeza'],
                '3R' => ['field' => 'ojos', 'label' => 'Ojos'],
                '4R' => ['field' => 'oidos', 'label' => 'Oídos'],
                '5R' => ['field' => 'nariz', 'label' => 'Nariz'],
                '6R' => ['field' => 'boca', 'label' => 'Boca'],
                '7R' => ['field' => 'oro_faringe', 'label' => 'Orofaringe'],
                '8R' => ['field' => 'cuello', 'label' => 'Cuello'],
                '9R' => ['field' => 'axilas_mamas', 'label' => 'Axilas - mamas'],
                '10R' => ['field' => 'torax', 'label' => 'Tórax'],
                '11R' => ['field' => 'abdomen', 'label' => 'Abdomen'],
                '12R' => ['field' => 'columna', 'label' => 'Columna vertebral'],
                '13R' => ['field' => 'ingle_perine', 'label' => 'Ingle - periné'],
                '14R' => ['field' => 'miembros_superiores', 'label' => 'Miembros superiores'],
                '15R' => ['field' => 'miembros_inferiores', 'label' => 'Miembros inferiores'],
            ],
            'Sistémico' => [
                '1S' => ['field' => 'organo_sentidos', 'label' => 'Órganos de los sentidos'],
                '2S' => ['field' => 'ex_respiratorio', 'label' => 'Respiratorio'],
                '3S' => ['field' => 'ex_cardio_vascular', 'label' => 'Cardio - vascular'],
                '4S' => ['field' => 'ex_digestivo', 'label' => 'Digestivo'],
                '5S' => ['field' => 'genital', 'label' => 'Genital'],
                '6S' => ['field' => 'urinario', 'label' => 'Urinario'],
                '7S' => ['field' => 'musculo_esqueletico', 'label' => 'Músculo - esquelético'],
                '8S' => ['field' => 'ex_endocrino', 'label' => 'Endócrino'],
                '9S' => ['field' => 'ex_hemo_linfatico', 'label' => 'Hemo - linfático'],
                '10S' => ['field' => 'neurologico', 'label' => 'Neurológico'],
            ],
        ];
    }

    public static function groups()
    {
        $groups = [];
        foreach (self::fieldGroups() as $group => $options) {
            $groups[$group] = array_map(function ($option) {
                return $option['label'];
            }, $options);
        }

        return $groups;
    }

    public static function normalize($selected)
    {
        if ($selected === null || $selected === '') {
            return '';
        }
        $options = array_merge(...array_values(self::groups()));
        if (! is_array($selected) || count($selected) > count($options)) {
            throw new \InvalidArgumentException('Seleccione las casillas válidas del examen físico.');
        }
        foreach ($selected as $code) {
            if (! is_string($code) || ! isset($options[$code])) {
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
            [$left, $width] = $columns[intdiv($index, 5)];
            [$top, $height] = $rows[$index % 5];
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
            if (isset($regions[$region])) {
                $selected[] = $region;
            }
        }

        return array_values(array_intersect(array_keys($regions), $selected));
    }

    public static function effectiveChecks($manual, array $marks)
    {
        return self::forDisplay(array_values(array_unique(array_merge(self::forDisplay($manual), self::bodyChecks($marks)))));
    }
}
