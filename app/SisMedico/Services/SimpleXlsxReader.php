<?php

namespace Sis_medico\Services;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Lector XLSX deliberadamente acotado para cargas iniciales.
 *
 * Lee solamente valores de celdas; no ejecuta formulas, macros, enlaces ni
 * contenido incrustado. Esto permite procesar la plantilla oficial incluso
 * con instalaciones antiguas de PHPExcel.
 */
class SimpleXlsxReader
{
    const MAIN_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    const DOCUMENT_REL_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    const PACKAGE_REL_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';

    public function read($path)
    {
        if (!is_file($path)) throw new RuntimeException('No se encontro el archivo Excel.');

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) throw new RuntimeException('El archivo no es un XLSX valido.');

        try {
            $this->assertSafePackage($zip);
            $relationships = $this->relationships($zip);
            $sharedStrings = $this->sharedStrings($zip);
            $dateStyles = $this->dateStyles($zip);
            $workbook = $this->xml($zip->getFromName('xl/workbook.xml'), 'libro de Excel');
            $sheets = $workbook->xpath('//*[local-name()="sheet"]');

            if (!$sheets || count($sheets) > 40) {
                throw new RuntimeException('El libro no contiene hojas validas o supera el limite permitido.');
            }

            $result = array();
            foreach ($sheets as $sheet) {
                $attributes = $sheet->attributes();
                $relationshipAttributes = $sheet->attributes(self::DOCUMENT_REL_NS);
                $name = trim((string) $attributes['name']);
                $relationshipId = (string) $relationshipAttributes['id'];
                if ($name === '' || !isset($relationships[$relationshipId])) continue;

                $target = ltrim(str_replace('\\', '/', $relationships[$relationshipId]), '/');
                if (strpos($target, 'xl/') !== 0) $target = 'xl/' . ltrim($target, '/');
                $xml = $zip->getFromName($target);
                if ($xml === false) throw new RuntimeException('No se pudo leer la hoja ' . $name . '.');
                $result[$name] = $this->readSheet($xml, $sharedStrings, $dateStyles, $name);
            }

            return $result;
        } finally {
            $zip->close();
        }
    }

    private function assertSafePackage(ZipArchive $zip)
    {
        $totalUncompressed = 0;
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);
            $name = isset($stat['name']) ? str_replace('\\', '/', $stat['name']) : '';
            $totalUncompressed += isset($stat['size']) ? (int) $stat['size'] : 0;

            if ($name === '' || strpos($name, '../') !== false || substr($name, 0, 1) === '/') {
                throw new RuntimeException('El XLSX contiene rutas internas no permitidas.');
            }
            if (preg_match('/(vbaProject\.bin|externalLinks|embeddings|oleObject)/i', $name)) {
                throw new RuntimeException('La plantilla no puede contener macros, enlaces externos ni objetos incrustados.');
            }
        }
        if ($totalUncompressed > 80 * 1024 * 1024) {
            throw new RuntimeException('El contenido descomprimido del XLSX supera el limite permitido.');
        }
        if ($zip->locateName('xl/workbook.xml') === false || $zip->locateName('xl/_rels/workbook.xml.rels') === false) {
            throw new RuntimeException('El archivo no contiene la estructura basica de un libro XLSX.');
        }
    }

    private function relationships(ZipArchive $zip)
    {
        $xml = $this->xml($zip->getFromName('xl/_rels/workbook.xml.rels'), 'relaciones del libro');
        $nodes = $xml->xpath('//*[local-name()="Relationship"]');
        $result = array();
        foreach ($nodes ?: array() as $node) {
            $attributes = $node->attributes();
            $type = (string) $attributes['Type'];
            if (substr($type, -10) !== '/worksheet') continue;
            $result[(string) $attributes['Id']] = (string) $attributes['Target'];
        }
        return $result;
    }

    private function sharedStrings(ZipArchive $zip)
    {
        $contents = $zip->getFromName('xl/sharedStrings.xml');
        if ($contents === false) return array();
        $xml = $this->xml($contents, 'textos compartidos');
        $result = array();
        foreach ($xml->xpath('//*[local-name()="si"]') ?: array() as $item) {
            $text = '';
            foreach ($item->xpath('.//*[local-name()="t"]') ?: array() as $part) $text .= (string) $part;
            $result[] = $text;
        }
        return $result;
    }

    private function readSheet($contents, array $sharedStrings, array $dateStyles, $sheetName)
    {
        $xml = $this->xml($contents, 'hoja ' . $sheetName);
        $rows = array();
        $rowNodes = $xml->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]');
        if (count($rowNodes ?: array()) > 10000) {
            throw new RuntimeException('La hoja ' . $sheetName . ' supera 10.000 filas.');
        }

        foreach ($rowNodes ?: array() as $row) {
            $rowNumber = (int) $row->attributes()['r'];
            if ($rowNumber < 1) continue;
            $values = array();
            foreach ($row->xpath('./*[local-name()="c"]') ?: array() as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) $attributes['r'];
                $column = $this->columnIndex($reference);
                if ($column < 0 || $column > 99) continue;
                $type = (string) $attributes['t'];
                $valueNodes = $cell->xpath('./*[local-name()="v"]');
                $value = count($valueNodes ?: array()) ? (string) $valueNodes[0] : '';

                if ($type === 's') {
                    $value = isset($sharedStrings[(int) $value]) ? $sharedStrings[(int) $value] : '';
                } elseif ($type === 'inlineStr') {
                    $value = '';
                    foreach ($cell->xpath('.//*[local-name()="t"]') ?: array() as $part) $value .= (string) $part;
                } elseif ($type === 'b') {
                    $value = $value === '1' ? 'SI' : 'NO';
                } elseif ($type === '' && $value !== '' && is_numeric($value)) {
                    $style = isset($attributes['s']) ? (int) $attributes['s'] : 0;
                    if (isset($dateStyles[$style])) $value = $this->excelDateValue($value, $dateStyles[$style]);
                }
                $values[$column] = $value;
            }
            if ($values) {
                ksort($values);
                $max = max(array_keys($values));
                $dense = array_fill(0, $max + 1, '');
                foreach ($values as $column => $value) $dense[$column] = $value;
                $rows[$rowNumber] = $dense;
            }
        }
        return $rows;
    }

    private function dateStyles(ZipArchive $zip)
    {
        $contents = $zip->getFromName('xl/styles.xml');
        if ($contents === false) return array();
        $xml = $this->xml($contents, 'estilos del libro');
        $custom = array();
        foreach ($xml->xpath('//*[local-name()="numFmt"]') ?: array() as $node) {
            $attributes = $node->attributes();
            $custom[(int) $attributes['numFmtId']] = (string) $attributes['formatCode'];
        }

        $styles = array();
        $nodes = $xml->xpath('//*[local-name()="cellXfs"]/*[local-name()="xf"]');
        foreach ($nodes ?: array() as $index => $node) {
            $formatId = (int) $node->attributes()['numFmtId'];
            $format = isset($custom[$formatId]) ? $custom[$formatId] : $this->builtInDateFormat($formatId);
            if ($format === '') continue;
            $clean = strtolower(preg_replace('/"[^"]*"|\[[^\]]*\]|\\\\./', '', $format));
            $hasDate = preg_match('/[yd]/', $clean) === 1;
            $hasTime = preg_match('/[hs]/', $clean) === 1;
            if ($hasDate || $hasTime) $styles[$index] = $hasDate && $hasTime ? 'datetime' : ($hasDate ? 'date' : 'time');
        }
        return $styles;
    }

    private function builtInDateFormat($formatId)
    {
        $formats = array(
            14=>'m/d/yyyy', 15=>'d-mmm-yy', 16=>'d-mmm', 17=>'mmm-yy',
            18=>'h:mm AM/PM', 19=>'h:mm:ss AM/PM', 20=>'h:mm', 21=>'h:mm:ss',
            22=>'m/d/yyyy h:mm', 45=>'mm:ss', 46=>'[h]:mm:ss', 47=>'mmss.0',
        );
        return isset($formats[$formatId]) ? $formats[$formatId] : '';
    }

    private function excelDateValue($value, $kind)
    {
        $number = (float) $value;
        if ($kind === 'time') {
            $seconds = (int) round(($number - floor($number)) * 86400);
            $seconds %= 86400;
            return sprintf('%02d:%02d', floor($seconds / 3600), floor(($seconds % 3600) / 60));
        }
        $timestamp = (int) round(($number - 25569) * 86400);
        return gmdate($kind === 'datetime' ? 'Y-m-d H:i' : 'Y-m-d', $timestamp);
    }

    private function columnIndex($reference)
    {
        if (!preg_match('/^([A-Z]+)/i', $reference, $match)) return -1;
        $letters = strtoupper($match[1]);
        $index = 0;
        for ($position = 0; $position < strlen($letters); $position++) {
            $index = ($index * 26) + (ord($letters[$position]) - 64);
        }
        return $index - 1;
    }

    private function xml($contents, $description)
    {
        if ($contents === false || $contents === null) {
            throw new RuntimeException('No se pudo leer ' . $description . '.');
        }
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($contents, SimpleXMLElement::class, LIBXML_NONET | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if ($xml === false) throw new RuntimeException('La estructura XML de ' . $description . ' no es valida.');
        return $xml;
    }
}
