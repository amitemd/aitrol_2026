<?php

namespace Sis_medico\Services;

class HospitalEpisodeBodyDiagram
{
    const MAX_MARKS = 40;

    // Siluetas originales del formulario 003 del PDF de referencia.
    public static function views()
    {
        return [
            'front' => ['label' => 'Frente', 'image' => 'assets/hospital/form003/frente.png', 'width' => 160, 'height' => 253, 'pdf_width' => 96, 'pdf_height' => 152],
            'back' => ['label' => 'Espalda', 'image' => 'assets/hospital/form003/espalda.png', 'width' => 153, 'height' => 255, 'pdf_width' => 91, 'pdf_height' => 152],
            'profile_1' => ['label' => 'Perfil 1', 'image' => 'assets/hospital/form003/perfil-1.png', 'width' => 90, 'height' => 253, 'pdf_width' => 54, 'pdf_height' => 152],
            'profile_2' => ['label' => 'Perfil 2', 'image' => 'assets/hospital/form003/perfil-2.png', 'width' => 90, 'height' => 254, 'pdf_width' => 54, 'pdf_height' => 152],
        ];
    }

    public static function normalize($raw)
    {
        if (trim((string) $raw) === '') return '';

        $marks = json_decode($raw, true);
        if (!is_array($marks) || json_last_error() !== JSON_ERROR_NONE || count($marks) > self::MAX_MARKS) {
            throw new \InvalidArgumentException('El esquema corporal debe contener como máximo '.self::MAX_MARKS.' marcas válidas.');
        }
        $valid = [];
        foreach ($marks as $mark) {
            if (!is_array($mark)
                || !isset($mark['view'], $mark['x'], $mark['y'], $mark['note'])
                || !is_string($mark['view'])
                || !array_key_exists($mark['view'], self::views())
                || !is_int($mark['x']) || $mark['x'] < 0 || $mark['x'] > 1000
                || !is_int($mark['y']) || $mark['y'] < 0 || $mark['y'] > 1000
                || !is_string($mark['note']) || trim($mark['note']) === ''
                || mb_strlen(trim($mark['note']), 'UTF-8') > 250) {
                throw new \InvalidArgumentException('Cada marca corporal necesita una ubicación y una descripción de hasta 250 caracteres.');
            }
            $region = array_key_exists('region', $mark) ? $mark['region'] : self::regionAt($mark['view'], $mark['x'], $mark['y']);
            if (!is_string($region) || ($region !== '' && !isset(HospitalEpisodePhysicalExam::groups()['Regional'][$region]))) {
                throw new \InvalidArgumentException('La región de la marca debe ser una casilla regional del examen físico.');
            }
            $valid[] = [
                'view' => $mark['view'], 'x' => $mark['x'], 'y' => $mark['y'],
                'note' => trim($mark['note']), 'region' => $region,
            ];
        }

        return $valid ? json_encode($valid, JSON_UNESCAPED_UNICODE) : '';
    }

    public static function regionZones()
    {
        // Broad anatomical regions on the supplied silhouettes (coordinates 0–1000).
        // Location identifies a region only; systems and specific facial findings are selected manually.
        $front = [
            ['2R', [[370, 0], [630, 0], [630, 150], [370, 150]]],
            ['8R', [[400, 150], [600, 150], [650, 205], [350, 205]]],
            ['14R', [[350, 165], [230, 200], [190, 340], [110, 470], [20, 520], [50, 610], [220, 585], [290, 380], [340, 300]]],
            ['14R', [[650, 165], [770, 200], [810, 340], [890, 470], [980, 520], [950, 610], [780, 585], [710, 380], [660, 300]]],
            ['10R', [[350, 205], [650, 205], [680, 350], [620, 390], [380, 390], [320, 350]]],
            ['11R', [[380, 390], [620, 390], [650, 485], [350, 485]]],
            ['13R', [[350, 485], [650, 485], [610, 550], [390, 550]]],
            ['15R', [[330, 550], [670, 550], [650, 800], [720, 1000], [260, 1000], [350, 800]]],
        ];
        $back = $front;
        array_splice($back, 4, 0, [['12R', [[460, 205], [540, 205], [550, 490], [450, 490]]]]);
        // There is no abdominal or inguinal region to infer from a posterior mark.
        $back = array_values(array_filter($back, function ($zone) { return !in_array($zone[0], ['11R', '13R'], true); }));
        $profile = [
            ['2R', [[340, 0], [820, 0], [870, 140], [540, 160], [350, 130]]],
            ['8R', [[380, 130], [650, 150], [650, 210], [330, 230]]],
            ['14R', [[330, 170], [470, 220], [300, 355], [260, 490], [300, 580], [50, 610], [50, 495], [110, 300]]],
            ['10R', [[350, 220], [730, 215], [860, 310], [730, 390], [280, 370]]],
            ['11R', [[300, 390], [730, 390], [780, 475], [320, 485]]],
            ['13R', [[320, 485], [740, 475], [650, 580], [290, 580]]],
            ['15R', [[280, 580], [670, 580], [600, 740], [530, 900], [880, 1000], [200, 1000], [250, 820]]],
        ];
        $opposite = array_map(function ($zone) {
            return [$zone[0], array_map(function ($point) { return [1000 - $point[0], $point[1]]; }, $zone[1])];
        }, $profile);
        return ['front' => $front, 'back' => $back, 'profile_1' => $profile, 'profile_2' => $opposite];
    }

    public static function regionAt($view, $x, $y)
    {
        foreach (self::regionZones()[$view] ?? [] as $zone) {
            $inside = false;
            $points = $zone[1];
            for ($i = 0, $j = count($points) - 1; $i < count($points); $j = $i++) {
                list($xi, $yi) = $points[$i];
                list($xj, $yj) = $points[$j];
                if (($yi > $y) !== ($yj > $y) && $x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi) $inside = !$inside;
            }
            if ($inside) return $zone[0];
        }
        return '';
    }

    public static function forDisplay($raw)
    {
        try {
            $normalized = self::normalize($raw);
            return $normalized === '' ? [] : json_decode($normalized, true);
        } catch (\InvalidArgumentException $exception) {
            return [];
        }
    }

    public static function renderPng($viewKey, array $marks)
    {
        $views = self::views();
        if (!isset($views[$viewKey])) throw new \InvalidArgumentException('Vista corporal no reconocida.');

        $source = file_get_contents(public_path($views[$viewKey]['image']));
        $viewMarks = [];
        foreach ($marks as $index => $mark) {
            if ($mark['view'] === $viewKey) $viewMarks[] = ['number' => $index + 1, 'mark' => $mark];
        }
        if (!$viewMarks) return $source;

        $image = imagecreatefromstring($source);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        $white = imagecolorallocate($image, 255, 255, 255);
        $red = imagecolorallocate($image, 179, 37, 37);
        foreach ($viewMarks as $item) {
            $mark = $item['mark'];
            $number = (string) $item['number'];
            $x = (int) round($mark['x'] * (imagesx($image) - 1) / 1000);
            $y = (int) round($mark['y'] * (imagesy($image) - 1) / 1000);
            $diameter = strlen($number) > 1 ? 27 : 22;
            imagefilledellipse($image, $x, $y, $diameter + 4, $diameter + 4, $white);
            imagefilledellipse($image, $x, $y, $diameter, $diameter, $red);
            imagestring($image, 3, $x - (imagefontwidth(3) * strlen($number) / 2),
                $y - (imagefontheight(3) / 2), $number, $white);
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    public static function renderMarkedForm003Page(array $marks)
    {
        $pagePath = public_path('assets/hospital/form003/pdf-003-examen.png');
        if (!$marks) return file_get_contents($pagePath);

        $layout = json_decode(file_get_contents(public_path('assets/hospital/form003/pdf-003-layout.json')), true);
        $image = imagecreatefrompng($pagePath);
        $outline = imagecolorallocate($image, 23, 62, 97);
        $blue = imagecolorallocate($image, 45, 100, 155);
        $white = imagecolorallocate($image, 255, 255, 255);
        foreach ($marks as $index => $mark) {
            if (!isset($layout['second'][$mark['view']])) continue;
            list($left, $top, $width, $height) = $layout['second'][$mark['view']];
            $x = (int) round($left + $width * $mark['x'] / 1000);
            $y = (int) round($top + $height * $mark['y'] / 1000);
            imagesetthickness($image, 9);
            imageline($image, $x - 12, $y - 12, $x + 12, $y + 12, $outline);
            imageline($image, $x + 12, $y - 12, $x - 12, $y + 12, $outline);
            imagesetthickness($image, 5);
            imageline($image, $x - 12, $y - 12, $x + 12, $y + 12, $blue);
            imageline($image, $x + 12, $y - 12, $x - 12, $y + 12, $blue);
            $label = (string) ($index + 1);
            imagefilledellipse($image, $x + 19, $y - 18, 22, 22, $white);
            imageellipse($image, $x + 19, $y - 18, 22, 22, $outline);
            imagestring($image, 2, $x + 19 - (int) (imagefontwidth(2) * strlen($label) / 2),
                $y - 18 - (int) (imagefontheight(2) / 2), $label, $outline);
        }
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $png;
    }
}
