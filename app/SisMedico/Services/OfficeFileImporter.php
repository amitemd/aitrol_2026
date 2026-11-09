<?php

namespace Sis_medico\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/** Converts local uploads only; never evaluates workbook formulas or follows remote relationships. */
class OfficeFileImporter
{
    public static function kind($name)
    {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, ['docx', 'docm', 'dotx', 'dotm', 'html', 'htm', 'txt'])) return 'document';
        if (in_array($ext, ['xls', 'xlsx', 'xlsm', 'xltx', 'xltm', 'csv'])) return 'spreadsheet';
        if (in_array($ext, ['pptx', 'pptm', 'potx', 'potm', 'ppsx', 'ppsm'])) return 'presentation';
        return null;
    }

    public function convert(UploadedFile $file)
    {
        $kind = self::kind($file->getClientOriginalName());
        if (!$kind) $this->invalid('Este formato no tiene editor.');
        if ($file->getSize() > 5 * 1048576) $this->invalid('El archivo supera 5 MB.');
        try {
            if ($kind === 'document') return ['kind' => $kind, 'content' => (new OfficeDocumentImporter)->convert($file)];
            $data = $kind === 'spreadsheet' ? $this->workbook($file) : $this->presentation($file->getRealPath());
            return ['kind' => $kind, 'content' => (new OfficeSuiteContent)->clean($kind, json_encode($data, JSON_THROW_ON_ERROR))];
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->invalid('No se pudo importar el archivo. Verifica su formato y que no tenga contraseña.');
        }
    }

    private function package($path)
    {
        $zip = new \ZipArchive;
        if ($zip->open($path, \ZipArchive::RDONLY) !== true) $this->invalid('Archivo Office inválido o protegido con contraseña.');
        try {
            $total = 0;
            if ($zip->numFiles > 1500) $this->invalid('Demasiados elementos dentro del archivo.');
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $total += $stat['size'];
                if ($total > 20 * 1048576) $this->invalid('El contenido descomprimido supera 20 MB.');
                if (preg_match('/\.(xml|rels)$/i', $stat['name'])) $this->xml($zip->getFromIndex($i));
            }
            return $zip;
        } catch (\Throwable $e) { $zip->close(); throw $e; }
    }

    private function xml($bytes)
    {
        if (!is_string($bytes) || str_contains($bytes, "\0") || preg_match('/<!DOCTYPE|<!ENTITY/i', $bytes)) $this->invalid('XML no permitido.');
        $dom = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try { $ok = $dom->loadXML($bytes, LIBXML_NONET); }
        finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        if (!$ok) $this->invalid('XML dañado.');
        return $dom;
    }

    private function workbook(UploadedFile $file)
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xls', 'csv'])) return $this->xlsx($file->getRealPath());
        $reader = \PHPExcel_IOFactory::createReader($ext === 'xls' ? 'Excel5' : ($ext === 'csv' ? 'CSV' : 'Excel2007'));
        if ($ext === 'csv') {
            $sample = file_get_contents($file->getRealPath());
            $reader->setInputEncoding(mb_check_encoding($sample, 'UTF-8') ? 'UTF-8' : 'Windows-1252');
            $line = strtok($sample, "\r\n") ?: '';
            $reader->setDelimiter(substr_count($line, ';') > substr_count($line, ',') ? ';' : ',');
        }
        $reader->setReadFilter(new class implements \PHPExcel_Reader_IReadFilter {
            private $counts = [];
            public function readCell($column, $row, $worksheetName = '') {
                $this->counts[$worksheetName] = ($this->counts[$worksheetName] ?? 0) + 1;
                if ($row > 1048576 || OfficeSuiteContent::columnNumber($column) > 16384 || $this->counts[$worksheetName] > 100000 || count($this->counts) > 20) {
                    throw ValidationException::withMessages(['files' => 'La hoja supera 100.000 celdas con datos o 20 hojas para edición web.']);
                }
                return true;
            }
        });
        $book = $reader->load($file->getRealPath());
        try {
            if ($book->getSheetCount() > 20) $this->invalid('Máximo 20 hojas por libro.');
            $sheets = [];
            foreach ($book->getWorksheetIterator() as $sheet) {
                $cells = []; $rows = 40; $cols = 12;
                foreach ($sheet->getCellCollection() as $address) {
                    $cell = $sheet->getCell($address); $value = $cell->getValue();
                    if ($value === null || $value === '') continue;
                    if ($value instanceof \PHPExcel_RichText) $value = $value->getPlainText();
                    if ($cell->getDataType() === 'f' && !OfficeSuiteContent::safeFormula((string) $value)) {
                        $value = $cell->getOldCalculatedValue();
                        if ($value === null) $this->invalid('La fórmula de '.$address.' no es compatible y no tiene un resultado guardado. Recalcula y guarda el libro desde Excel antes de importarlo.');
                    }
                    // Literal text beginning with '=' must not become an executable formula.
                    if ($cell->getDataType() !== 'f' && str_starts_with((string) $value, '=')) $value = "'".$value;
                    $style = $sheet->getStyle($address); $format = $style->getNumberFormat()->getFormatCode();
                    if (\PHPExcel_Shared_Date::isDateTime($cell) && is_numeric($value)) $value = \PHPExcel_Style_NumberFormat::toFormattedString($value, $format);
                    $align = $style->getAlignment()->getHorizontal();
                    $cells[$address] = ['value' => (string) $value, 'bold' => (bool) $style->getFont()->getBold(),
                        'align' => in_array($align, ['left', 'center', 'right']) ? $align : 'left',
                        'format' => str_contains($format, '%') ? 'percent' : (str_contains($format, '$') ? 'currency' : 'general'),
                        'color' => $this->rgb($style->getFont()->getColor()->getRGB(), '#243b53'),
                        'background' => $style->getFill()->getFillType() === 'solid' ? $this->rgb($style->getFill()->getStartColor()->getRGB(), '#ffffff') : '#ffffff'];
                    $rows = max($rows, $cell->getRow()); $cols = max($cols, OfficeSuiteContent::columnNumber($cell->getColumn()));
                }
                $sheets[] = ['name' => $sheet->getTitle(), 'rows' => $rows, 'cols' => $cols, 'cells' => (object) $cells];
            }
            return ['sheets' => $sheets];
        } finally { $book->disconnectWorksheets(); }
    }

    private function rgb($value, $default)
    {
        return preg_match('/^[a-f0-9]{6}$/iD', (string) $value) ? '#'.$value : $default;
    }

    private function xlsx($path)
    {
        $zip = $this->package($path);
        try {
            $book = $this->xml($zip->getFromName('xl/workbook.xml'));
            $rels = $this->relationships($zip, 'xl/workbook.xml'); $strings = []; $styles = []; $sheets = [];
            $shared = $zip->getFromName('xl/sharedStrings.xml');
            if ($shared !== false) foreach ($this->xml($shared)->getElementsByTagNameNS('*', 'si') as $si) {
                $text = ''; foreach ($si->getElementsByTagNameNS('*', 't') as $t) $text .= $t->textContent;
                $strings[] = $text;
            }
            $styleXml = $zip->getFromName('xl/styles.xml');
            if ($styleXml !== false) {
                $dom = $this->xml($styleXml); $fonts = $dom->getElementsByTagNameNS('*', 'font'); $fills = $dom->getElementsByTagNameNS('*', 'fill'); $formats = [];
                foreach ($dom->getElementsByTagNameNS('*', 'numFmt') as $fmt) $formats[$fmt->getAttribute('numFmtId')] = $fmt->getAttribute('formatCode');
                $xfs = $dom->getElementsByTagNameNS('*', 'cellXfs')->item(0);
                if ($xfs) foreach ($xfs->childNodes as $xf) {
                    if (!$xf instanceof \DOMElement) continue;
                    $font = $fonts->item((int) $xf->getAttribute('fontId')); $fill = $fills->item((int) $xf->getAttribute('fillId'));
                    $bold = $font ? $font->getElementsByTagNameNS('*', 'b')->item(0) : null;
                    $color = $font ? $font->getElementsByTagNameNS('*', 'color')->item(0) : null;
                    $bg = $fill ? $fill->getElementsByTagNameNS('*', 'fgColor')->item(0) : null;
                    $alignment = $xf->getElementsByTagNameNS('*', 'alignment')->item(0); $align = $alignment ? $alignment->getAttribute('horizontal') : 'left';
                    $num = (int) $xf->getAttribute('numFmtId'); $format = $formats[$num] ?? \PHPExcel_Style_NumberFormat::builtInFormatCode($num);
                    $styles[] = ['bold' => $bold && !in_array($bold->getAttribute('val'), ['0', 'false']),
                        'color' => $color ? $this->rgb(substr($color->getAttribute('rgb'), -6), '#243b53') : '#243b53',
                        'background' => $bg ? $this->rgb(substr($bg->getAttribute('rgb'), -6), '#ffffff') : '#ffffff',
                        'align' => in_array($align, ['left', 'right', 'center']) ? $align : 'left',
                        'format' => str_contains((string) $format, '%') ? 'percent' : (str_contains((string) $format, '$') ? 'currency' : 'general'), '_numberFormat' => $format];
                }
            }
            foreach ($book->getElementsByTagNameNS('*', 'sheet') as $sheet) {
                if (count($sheets) >= 20) $this->invalid('Máximo 20 hojas por libro.');
                $rid = $this->relationshipId($sheet, 'id'); $part = $rels[$rid] ?? null;
                if (!$part) $this->invalid('No se encontró una hoja del libro.');
                $dom = $this->xml($zip->getFromName($part)); $cells = []; $rows = 40; $cols = 12;
                foreach ($dom->getElementsByTagNameNS('*', 'c') as $cell) {
                    $address = $cell->getAttribute('r');
                    $v = $cell->getElementsByTagNameNS('*', 'v')->item(0); $f = $cell->getElementsByTagNameNS('*', 'f')->item(0);
                    $value = $v ? $v->textContent : '';
                    if ($cell->getAttribute('t') === 's') {
                        if (!ctype_digit($value) || !array_key_exists((int) $value, $strings)) $this->invalid('Referencia de texto inválida en '.$address.'.');
                        $value = $strings[(int) $value];
                    }
                    if ($cell->getAttribute('t') === 'inlineStr') { $value = ''; foreach ($cell->getElementsByTagNameNS('*', 't') as $t) $value .= $t->textContent; }
                    if ($f) {
                        if ($f->textContent !== '' && OfficeSuiteContent::safeFormula('='.$f->textContent)) $value = '='.$f->textContent;
                        elseif (!$v || $value === '') $this->invalid('La fórmula de '.$address.' no es compatible y no tiene resultado guardado. Recalcula y guarda el archivo desde Excel.');
                    } elseif (str_starts_with($value, '=')) $value = "'".$value;
                    if ($value === '' && !$f) continue;
                    if (!preg_match('/^([A-Z]{1,3})([1-9][0-9]{0,6})$/D', $address, $m) || (int) $m[2] > 1048576 || OfficeSuiteContent::columnNumber($m[1]) > 16384 || count($cells) >= 100000) $this->invalid('La hoja supera 100.000 celdas con datos o las dimensiones admitidas por Excel.');
                    $style = $styles[(int) $cell->getAttribute('s')] ?? [];
                    if (is_numeric($value) && \PHPExcel_Shared_Date::isDateTimeFormatCode($style['_numberFormat'] ?? 'General')) {
                        if ($book->getElementsByTagNameNS('*', 'workbookPr')->item(0)?->getAttribute('date1904') === '1') $value += 1462;
                        $value = \PHPExcel_Style_NumberFormat::toFormattedString($value, $style['_numberFormat']);
                    }
                    unset($style['_numberFormat']);
                    $cells[$address] = ['value' => (string) $value] + $style;
                    $rows = max($rows, (int) $m[2]); $cols = max($cols, OfficeSuiteContent::columnNumber($m[1]));
                }
                $sheets[] = ['name' => $sheet->getAttribute('name'), 'rows' => $rows, 'cols' => $cols, 'cells' => (object) $cells];
            }
            return ['sheets' => $sheets];
        } finally { $zip->close(); }
    }

    private function relationshipId($element, $attribute)
    {
        return $element->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', $attribute)
            ?: $element->getAttributeNS('http://purl.oclc.org/ooxml/officeDocument/relationships', $attribute);
    }

    private function presentation($path)
    {
        $zip = $this->package($path);
        try {
            $deck = $this->xml($zip->getFromName('ppt/presentation.xml'));
            $rels = $this->relationships($zip, 'ppt/presentation.xml'); $slides = [];
            foreach ($deck->getElementsByTagNameNS('*', 'sldId') as $id) {
                if (count($slides) >= 100) $this->invalid('Máximo 100 diapositivas.');
                $rid = $id->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id') ?: $id->getAttributeNS('http://purl.oclc.org/ooxml/officeDocument/relationships', 'id');
                $part = $rels[$rid] ?? null;
                if (!$part) $this->invalid('No se encontró una diapositiva.');
                $dom = $this->xml($zip->getFromName($part)); $paragraphs = [];
                foreach ($dom->getElementsByTagNameNS('*', 'p') as $p) {
                    $text = '';
                    foreach ($p->getElementsByTagNameNS('*', 't') as $t) $text .= $t->textContent;
                    if ($text !== '') $paragraphs[] = $text;
                }
                $image = ''; $images = []; $slideRels = $this->relationships($zip, $part);
                foreach ($dom->getElementsByTagNameNS('*', 'blip') as $blip) {
                    $rid = $this->relationshipId($blip, 'embed');
                    if (!$rid) { $svg = $blip->getElementsByTagNameNS('*', 'svgBlip')->item(0); if ($svg) $rid = $this->relationshipId($svg, 'embed'); }
                    $bytes = isset($slideRels[$rid]) ? $zip->getFromName($slideRels[$rid]) : false;
                    $info = $bytes ? @getimagesizefromstring($bytes) : false;
                    $mime = $info['mime'] ?? (str_ends_with(strtolower($slideRels[$rid] ?? ''), '.svg') ? 'image/svg+xml' : '');
                    if ($bytes && in_array($mime, ['image/png', 'image/jpeg', 'image/svg+xml']) && strlen($bytes) <= 1000000) {
                        $images[] = (new OfficeSlideImage)->clean('data:'.$mime.';base64,'.base64_encode($bytes));
                    }
                }
                $image = $images[0] ?? '';
                [$background, $backgroundLayers, $elements] = $this->slideBackground($zip, $part, $dom, $deck);
                $slides[] = ['title' => array_shift($paragraphs) ?? '', 'body' => implode("\n", $paragraphs), 'image' => $image, 'images' => $images,
                    'fontSize' => 28, 'layout' => $image ? 'split' : 'content', 'background' => $background, 'backgroundLayers' => $backgroundLayers, 'elements' => $elements, 'color' => '#17465c'];
            }
            return ['slides' => $slides];
        } finally { $zip->close(); }
    }

    private function slideBackground($zip, $part, $slide, $deck)
    {
        $chain = [[$part, $slide]];
        foreach (['/slideLayouts/', '/slideMasters/', '/theme/'] as $folder) {
            $last = $chain[count($chain) - 1];
            foreach ($this->relationships($zip, $last[0]) as $target) {
                if (str_contains('/'.$target, $folder)) { $chain[] = [$target, $this->xml($zip->getFromName($target))]; break; }
            }
        }
        $theme = []; $mapping = ['bg1' => 'lt1', 'bg2' => 'lt2', 'tx1' => 'dk1', 'tx2' => 'dk2'];
        foreach (array_reverse($chain) as [$name, $dom]) {
            $scheme = $dom->getElementsByTagNameNS('*', 'clrScheme')->item(0);
            if ($scheme) foreach ($scheme->childNodes as $entry) if ($entry instanceof \DOMElement) {
                $color = $entry->firstElementChild;
                if ($color) $theme[$entry->localName] = $this->rgb($color->getAttribute('val') ?: $color->getAttribute('lastClr'), '#ffffff');
            }
            foreach (['clrMap', 'overrideClrMapping'] as $tag) {
                $map = $dom->getElementsByTagNameNS('*', $tag)->item(0);
                if ($map) foreach ($map->attributes as $attr) $mapping[$attr->name] = $attr->value;
            }
        }
        $resolve = function ($node) use ($theme, $mapping) {
            $color = $node->getElementsByTagNameNS('*', 'srgbClr')->item(0);
            if ($color) return $this->rgb($color->getAttribute('val'), '#ffffff');
            $color = $node->getElementsByTagNameNS('*', 'schemeClr')->item(0);
            if ($color) { $key = $color->getAttribute('val'); return $theme[$mapping[$key] ?? $key] ?? '#ffffff'; }
            return '#ffffff';
        };
        $size = $deck->getElementsByTagNameNS('*', 'sldSz')->item(0);
        $width = max(1, (int) ($size ? $size->getAttribute('cx') : 12192000)); $height = max(1, (int) ($size ? $size->getAttribute('cy') : 6858000));
        $background = '#ffffff'; $layers = [];
        foreach (array_reverse($chain) as [$name, $dom]) {
            $bg = $dom->getElementsByTagNameNS('*', 'bg')->item(0);
            if ($bg) $background = $resolve($bg);
            if ($name === $part || str_contains($name, '/theme/')) continue;
            if ($slide->documentElement->getAttribute('showMasterSp') === '0') continue;
            $rels = $this->relationships($zip, $name);
            $tree = $dom->getElementsByTagNameNS('*', 'spTree')->item(0);
            if (!$tree) continue;
            foreach ($tree->childNodes as $shape) {
                if (!$shape instanceof \DOMElement || !in_array($shape->localName, ['pic', 'sp'])) continue;
                $props = $shape->getElementsByTagNameNS('*', 'spPr')->item(0);
                if (!$props) continue;
                $xfrm = $props->getElementsByTagNameNS('*', 'xfrm')->item(0);
                if (!$xfrm) continue;
                $off = $xfrm->getElementsByTagNameNS('*', 'off')->item(0); $ext = $xfrm->getElementsByTagNameNS('*', 'ext')->item(0);
                if (!$off || !$ext) continue;
                $layer = ['x' => (float) $off->getAttribute('x') / $width * 100, 'y' => (float) $off->getAttribute('y') / $height * 100,
                    'width' => (float) $ext->getAttribute('cx') / $width * 100, 'height' => (float) $ext->getAttribute('cy') / $height * 100, 'image' => '', 'color' => '#ffffff'];
                if ($shape->localName === 'pic') {
                    $blip = $shape->getElementsByTagNameNS('*', 'svgBlip')->item(0) ?: $shape->getElementsByTagNameNS('*', 'blip')->item(0);
                    $target = $blip ? ($rels[$this->relationshipId($blip, 'embed')] ?? null) : null;
                    $bytes = $target ? $zip->getFromName($target) : false;
                    $info = $bytes ? @getimagesizefromstring($bytes) : false;
                    $mime = $info['mime'] ?? ($target && str_ends_with(strtolower($target), '.svg') ? 'image/svg+xml' : '');
                    if (!$bytes || !in_array($mime, ['image/png','image/jpeg','image/svg+xml'])) continue;
                    $layer['image'] = (new OfficeSlideImage)->clean('data:'.$mime.';base64,'.base64_encode($bytes));
                } else {
                    $fill = $props->getElementsByTagNameNS('*', 'solidFill')->item(0);
                    if (!$fill) continue;
                    $layer['color'] = $resolve($fill);
                }
                if ($layer['width'] > 0 && $layer['height'] > 0) $layers[] = $layer;
            }
        }
        return [$background, $layers, $this->slideElements($zip, $part, $slide, $chain, $resolve, $width, $height)];
    }

    private function slideElements($zip, $part, $slide, $chain, $resolve, $width, $height)
    {
        $tree = $slide->getElementsByTagNameNS('*', 'spTree')->item(0); $result = [];
        if (!$tree) return [];
        $rels = $this->relationships($zip, $part);
        foreach ($tree->childNodes as $shape) {
            if (!$shape instanceof \DOMElement || !in_array($shape->localName, ['sp','pic'])) continue;
            $inherited = null; $placeholder = $shape->getElementsByTagNameNS('*', 'ph')->item(0);
            if ($placeholder) foreach (array_slice($chain, 1) as [$name, $dom]) {
                foreach ($dom->getElementsByTagNameNS('*', 'sp') as $candidate) {
                    $ph = $candidate->getElementsByTagNameNS('*', 'ph')->item(0);
                    if ($ph && ($ph->getAttribute('idx') ?: '0') === ($placeholder->getAttribute('idx') ?: '0') && ($ph->getAttribute('type') ?: 'obj') === ($placeholder->getAttribute('type') ?: 'obj')) { $inherited = $candidate; break 2; }
                }
            }
            $props = $shape->getElementsByTagNameNS('*', 'spPr')->item(0);
            $xfrm = $props ? $props->getElementsByTagNameNS('*', 'xfrm')->item(0) : null;
            if (!$xfrm && $inherited) $xfrm = $inherited->getElementsByTagNameNS('*', 'xfrm')->item(0);
            if (!$xfrm) continue;
            $off = $xfrm->getElementsByTagNameNS('*', 'off')->item(0); $ext = $xfrm->getElementsByTagNameNS('*', 'ext')->item(0);
            if (!$off || !$ext) continue;
            $element = ['x' => (float) $off->getAttribute('x') / $width * 100, 'y' => (float) $off->getAttribute('y') / $height * 100,
                'width' => (float) $ext->getAttribute('cx') / $width * 100, 'height' => (float) $ext->getAttribute('cy') / $height * 100,
                'rotation' => (float) $xfrm->getAttribute('rot') / 60000];
            if ($element['width'] <= 0 || $element['height'] <= 0) continue;
            if ($shape->localName === 'pic') {
                $blip = $shape->getElementsByTagNameNS('*', 'svgBlip')->item(0) ?: $shape->getElementsByTagNameNS('*', 'blip')->item(0);
                $target = $blip ? ($rels[$this->relationshipId($blip, 'embed')] ?? null) : null;
                $bytes = $target ? $zip->getFromName($target) : false; $info = $bytes ? @getimagesizefromstring($bytes) : false;
                $mime = $info['mime'] ?? ($target && str_ends_with(strtolower($target), '.svg') ? 'image/svg+xml' : '');
                if (!$bytes || !in_array($mime, ['image/png','image/jpeg','image/svg+xml'])) continue;
                $element += ['type' => 'image', 'image' => (new OfficeSlideImage)->clean('data:'.$mime.';base64,'.base64_encode($bytes))];
            } else {
                $body = $shape->getElementsByTagNameNS('*', 'txBody')->item(0);
                if (!$body) continue;
                $lines = [];
                foreach ($body->getElementsByTagNameNS('*', 'p') as $p) {
                    $line = '';
                    foreach ($p->childNodes as $child) {
                        if (!$child instanceof \DOMElement) continue;
                        if ($child->localName === 'br') $line .= "\n";
                        else foreach ($child->getElementsByTagNameNS('*', 't') as $t) $line .= $t->textContent;
                    }
                    $lines[] = $line;
                }
                $text = implode("\n", $lines); if (trim($text) === '') continue;
                $style = $body->getElementsByTagNameNS('*', 'rPr')->item(0);
                $default = $inherited ? $inherited->getElementsByTagNameNS('*', 'defRPr')->item(0) : null;
                $font = $style ? $style->getElementsByTagNameNS('*', 'latin')->item(0) : null;
                if (!$font && $default) $font = $default->getElementsByTagNameNS('*', 'latin')->item(0);
                $fill = $style ? $style->getElementsByTagNameNS('*', 'solidFill')->item(0) : null;
                if (!$fill && $default) $fill = $default->getElementsByTagNameNS('*', 'solidFill')->item(0);
                $size = (float) (($style ? $style->getAttribute('sz') : '') ?: ($default ? $default->getAttribute('sz') : '') ?: '2800') / 100;
                $pPr = $body->getElementsByTagNameNS('*', 'pPr')->item(0);
                $element += ['type' => 'text', 'text' => $text, 'fontSize' => max(5, min(150, $size)), 'fontFamily' => $font ? $font->getAttribute('typeface') : 'Arial',
                    'color' => $fill ? $resolve($fill) : '#17465c', 'bold' => $style && $style->getAttribute('b') === '1',
                    'align' => ['ctr'=>'center','r'=>'right'][$pPr ? $pPr->getAttribute('algn') : ''] ?? 'left'];
            }
            $result[] = $element;
        }
        return $result;
    }

    private function relationships($zip, $part)
    {
        $bytes = $zip->getFromName(dirname($part).'/_rels/'.basename($part).'.rels');
        if ($bytes === false) return [];
        $result = [];
        foreach ($this->xml($bytes)->getElementsByTagNameNS('*', 'Relationship') as $rel) {
            if (strcasecmp($rel->getAttribute('TargetMode'), 'External') === 0) continue;
            $target = $rel->getAttribute('Target');
            if (preg_match('~[:\\\\\x00]~', $target)) continue;
            $segments = [];
            foreach (explode('/', str_starts_with($target, '/') ? ltrim($target, '/') : dirname($part).'/'.$target) as $segment) {
                if ($segment === '..') { if (!$segments) $this->invalid('Ruta Office inválida.'); array_pop($segments); }
                elseif ($segment !== '.' && $segment !== '') $segments[] = $segment;
            }
            $result[$rel->getAttribute('Id')] = implode('/', $segments);
        }
        return $result;
    }

    private function invalid($message) { throw ValidationException::withMessages(['files' => $message]); }
}
