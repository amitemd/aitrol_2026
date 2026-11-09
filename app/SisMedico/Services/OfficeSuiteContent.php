<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OfficeSuiteContent
{
    public static function blank($kind)
    {
        if ($kind === 'spreadsheet') return json_encode(['sheets' => [['name' => 'Hoja 1', 'rows' => 40, 'cols' => 12, 'cells' => (object) []]]]);
        if ($kind === 'presentation') return json_encode(['slides' => [['title' => 'Título de la presentación', 'body' => 'Escribe aquí el contenido', 'background' => '#ffffff', 'color' => '#17465c', 'fontSize' => 28, 'layout' => 'title', 'image' => '']]], JSON_UNESCAPED_UNICODE);
        return '';
    }

    public function clean($kind, $json)
    {
        $data = json_decode($json, true);
        if (!is_array($data)) $this->invalid('El archivo no contiene datos válidos.');
        if ($kind === 'spreadsheet') {
            Validator::make($data, ['sheets' => 'required|array|min:1|max:20', 'sheets.*.name' => 'required|string|max:31',
                'sheets.*.rows' => 'required|integer|min:1|max:1048576', 'sheets.*.cols' => 'required|integer|min:1|max:16384',
                'sheets.*.cells' => 'present|array|max:100000'])->validate();
            $sheets = []; $names = [];
            foreach ($data['sheets'] as $sheet) {
                $name = trim($sheet['name']);
                if ($name === '' || preg_match('~[\\\\/\[\]:*?]~', $name) || in_array(mb_strtolower($name), $names)) $this->invalid('Los nombres de hojas deben ser únicos y no contener / \\ [ ] : * ?');
                $names[] = mb_strtolower($name); $cells = [];
                foreach ($sheet['cells'] as $address => $cell) {
                    if (!preg_match('/^([A-Z]{1,3})([1-9][0-9]{0,6})$/D', $address, $m) || self::columnNumber($m[1]) > $sheet['cols'] || (int) $m[2] > $sheet['rows']) $this->invalid('Una celda está fuera de la hoja.');
                    Validator::make($cell, ['value' => 'present|string|max:2000', 'bold' => 'sometimes|boolean', 'align' => 'sometimes|in:left,center,right', 'format' => 'sometimes|in:general,number,currency,percent'])->validate();
                    if (str_starts_with($cell['value'], '=') && !self::safeFormula($cell['value'])) $this->invalid('Fórmula no compatible en '.$address.'. Usa referencias y operaciones numéricas, SUMA, PROMEDIO, MIN, MAX o CONTAR.');
                    $cells[$address] = ['value' => $cell['value'], 'bold' => (bool) ($cell['bold'] ?? false), 'align' => $cell['align'] ?? 'left',
                        'format' => $cell['format'] ?? 'general', 'color' => $this->color($cell['color'] ?? '#243b53'), 'background' => $this->color($cell['background'] ?? '#ffffff')];
                }
                $sheets[] = ['name' => $name, 'rows' => (int) $sheet['rows'], 'cols' => (int) $sheet['cols'], 'cells' => (object) $cells];
            }
            $result = ['sheets' => $sheets];
        } elseif ($kind === 'presentation') {
            Validator::make($data, ['slides' => 'required|array|min:1|max:100', 'slides.*.title' => 'present|string|max:300',
                'slides.*.body' => 'present|string|max:10000', 'slides.*.fontSize' => 'required|integer|min:14|max:48',
                'slides.*.layout' => 'required|in:title,content,split', 'slides.*.image' => 'sometimes|nullable|string|max:1400000'])->validate();
            $slides = [];
            foreach ($data['slides'] as $slide) {
                $image = $slide['image'] ?? '';
                if ($image !== '') $image = (new OfficeSlideImage)->clean($image);
                $images = $slide['images'] ?? [];
                if (!is_array($images) || count($images) > 50) $this->invalid('Máximo 50 imágenes por diapositiva.');
                $images = array_map(function ($uri) { return (new OfficeSlideImage)->clean($uri); }, $images);
                $layers = $slide['backgroundLayers'] ?? [];
                Validator::make(['layers' => $layers], ['layers' => 'array|max:100', 'layers.*.x' => 'required|numeric|between:-1000,1000', 'layers.*.y' => 'required|numeric|between:-1000,1000',
                    'layers.*.width' => 'required|numeric|between:0.000001,2000', 'layers.*.height' => 'required|numeric|between:0.000001,2000'])->validate();
                $layers = array_map(function ($layer) {
                    return ['x' => (float) $layer['x'], 'y' => (float) $layer['y'], 'width' => (float) $layer['width'], 'height' => (float) $layer['height'],
                        'color' => $this->color($layer['color'] ?? '#ffffff'), 'image' => empty($layer['image']) ? '' : (new OfficeSlideImage)->clean($layer['image'])];
                }, $layers);
                $elements = $slide['elements'] ?? [];
                Validator::make(['items' => $elements], ['items' => 'array|max:200', 'items.*.type' => 'required|in:text,image',
                    'items.*.x' => 'required|numeric|between:-1000,1000', 'items.*.y' => 'required|numeric|between:-1000,1000',
                    'items.*.width' => 'required|numeric|between:0.000001,2000', 'items.*.height' => 'required|numeric|between:0.000001,2000',
                    'items.*.rotation' => 'sometimes|numeric|between:-360,360'])->validate();
                $elements = array_map(function ($item) {
                    $base = ['type' => $item['type'], 'x' => (float) $item['x'], 'y' => (float) $item['y'], 'width' => (float) $item['width'], 'height' => (float) $item['height'], 'rotation' => (float) ($item['rotation'] ?? 0)];
                    if ($item['type'] === 'image') return $base + ['image' => (new OfficeSlideImage)->clean($item['image'] ?? '')];
                    Validator::make($item, ['text' => 'present|string|max:10000', 'fontSize' => 'required|numeric|between:5,150', 'fontFamily' => 'required|string|max:100', 'bold' => 'required|boolean', 'align' => 'required|in:left,right,center'])->validate();
                    return $base + ['text' => $item['text'], 'fontSize' => (float) $item['fontSize'], 'fontFamily' => preg_replace('/[^\pL\pN _-]/u', '', $item['fontFamily']), 'color' => $this->color($item['color']), 'bold' => (bool) $item['bold'], 'align' => $item['align']];
                }, $elements);
                $slides[] = ['title' => $slide['title'], 'body' => $slide['body'], 'layout' => $slide['layout'], 'fontSize' => (int) $slide['fontSize'],
                    'background' => $this->color($slide['background'] ?? '#ffffff'), 'color' => $this->color($slide['color'] ?? '#17465c'), 'image' => $image, 'images' => $images, 'backgroundLayers' => $layers, 'elements' => $elements];
            }
            $result = ['slides' => $slides];
        } else $this->invalid('Tipo de archivo no compatible.');
        $encoded = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (strlen($encoded) > 20000000) $this->invalid('El archivo supera el tamaño de contenido editable admitido.');
        return $encoded;
    }

    public static function columnNumber($letters)
    {
        $number = 0;
        foreach (str_split($letters) as $letter) $number = $number * 26 + ord($letter) - 64;
        return $number;
    }

    public static function safeFormula($formula)
    {
        $formula = str_replace('$', '', strtoupper(substr($formula, 1)));
        if (strlen($formula) > 300 || !preg_match('/^[A-Z0-9+*\/^().,:;\s\-$]+$/D', $formula)) return false;
        preg_match_all('/[A-Z]+[0-9]*/', $formula, $matches);
        foreach ($matches[0] as $token) {
            if (!in_array($token, ['SUM', 'SUMA', 'AVERAGE', 'PROMEDIO', 'MIN', 'MAX', 'COUNT', 'CONTAR']) && !preg_match('/^[A-Z]{1,3}[1-9][0-9]{0,6}$/D', $token)) return false;
        }
        return true;
    }

    private function color($value)
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-f]{6}$/iD', $value)) $this->invalid('Color no válido.');
        return $value;
    }

    private function invalid($message)
    {
        throw ValidationException::withMessages(['content' => $message]);
    }
}
