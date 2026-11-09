<?php

namespace Sis_medico\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class OfficeDocumentImporter
{
    private const WORD = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private $relationships = [];
    private $zip;

    public function convert(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['docx', 'docm', 'dotx', 'dotm', 'html', 'htm', 'txt'])) {
            $this->invalid('Formato no compatible. Sube documentos DOCX, HTML o TXT. Para un archivo DOC, guárdalo primero como DOCX.');
        }
        if ($file->getSize() > 5 * 1024 * 1024) $this->invalid('Cada archivo puede tener hasta 5 MB.');
        if (in_array($extension, ['docx', 'docm', 'dotx', 'dotm'])) {
            $html = $this->docx($file->getRealPath());
        } else {
            $text = file_get_contents($file->getRealPath());
            if (str_starts_with($text, "\xFF\xFE") || str_starts_with($text, "\xFE\xFF")) {
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-16');
            }
            if (str_contains($text, "\0")) $this->invalid('El archivo no contiene texto válido.');
            if (!mb_check_encoding($text, 'UTF-8')) $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
            $html = $extension === 'txt' ? '<p>'.nl2br($this->escape($text)).'</p>' : $text;
        }
        $html = (new OfficeHtmlSanitizer)->clean($html);
        if (strlen($html) > 2000000) $this->invalid('El documento convertido es demasiado grande. Reduce su contenido o sus imágenes.');
        if (trim(strip_tags($html)) === '' && !str_contains($html, '<img')) {
            $this->invalid('No se encontró contenido editable. El archivo puede estar vacío o contener solamente objetos no compatibles.');
        }
        return $html;
    }

    private function docx($path)
    {
        $this->relationships = [];
        $this->zip = new \ZipArchive;
        if ($this->zip->open($path) !== true) $this->invalid('No se pudo abrir el archivo Word. Verifica que sea un DOCX válido sin contraseña.');
        try {
            $size = 0;
            if ($this->zip->numFiles > 1000) $this->invalid('El archivo Word contiene demasiados elementos.');
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $stat = $this->zip->statIndex($i);
                $size += $stat['size'];
                if ($size > 20 * 1024 * 1024) $this->invalid('El contenido descomprimido de Word supera el límite permitido.');
            }
            $xml = $this->entry('word/document.xml', 5 * 1024 * 1024);
            if ($xml === false) $this->invalid('El archivo no contiene un documento Word válido.');
            $document = $this->xml(str_replace(['http://purl.oclc.org/ooxml/wordprocessingml/main', 'http://purl.oclc.org/ooxml/officeDocument/relationships'], [self::WORD, self::REL], $xml));
            if ($document->documentElement->namespaceURI !== self::WORD) $this->invalid('Formato de Word no compatible. Guarda el archivo como DOCX estándar.');
            $rels = $this->entry('word/_rels/document.xml.rels', 1024 * 1024);
            if ($rels !== false) {
                foreach ($this->xml($rels)->getElementsByTagName('Relationship') as $rel) {
                    $this->relationships[$rel->getAttribute('Id')] = ['target' => $rel->getAttribute('Target'), 'external' => $rel->getAttribute('TargetMode') === 'External'];
                }
            }
            $body = $document->getElementsByTagNameNS(self::WORD, 'body')->item(0);
            if (!$body) $this->invalid('No se encontró el contenido del documento.');
            return $this->children($body);
        } finally {
            $this->zip->close();
        }
    }

    private function entry($name, $limit)
    {
        $stat = $this->zip->statName($name);
        if (!$stat) return false;
        if ($stat['size'] > $limit) $this->invalid('Uno de los elementos del documento supera el tamaño permitido.');
        return $this->zip->getFromName($name);
    }

    private function xml($xml)
    {
        if (str_contains($xml, "\0") || stripos($xml, '<!DOCTYPE') !== false || stripos($xml, '<!ENTITY') !== false) $this->invalid('El documento contiene XML no permitido.');
        $dom = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $loaded = $dom->loadXML($xml, LIBXML_NONET);
        } finally {
            libxml_clear_errors(); libxml_use_internal_errors($previous);
        }
        if (!$loaded) $this->invalid('El documento Word está dañado o no tiene un formato válido.');
        return $dom;
    }

    private function children(\DOMNode $node)
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement) $html .= $this->node($child);
            if (strlen($html) > 2000000) $this->invalid('El documento convertido supera el tamaño permitido.');
        }
        return $html;
    }

    private function property(\DOMElement $node, $name)
    {
        $element = $node->getElementsByTagNameNS(self::WORD, $name)->item(0);
        return $element ? $element->getAttributeNS(self::WORD, 'val') : null;
    }

    private function node(\DOMElement $node)
    {
        $name = $node->localName;
        if ($name === 'blip') return $this->image($node->getAttributeNS(self::REL, 'embed'));
        if ($node->namespaceURI !== self::WORD) return $this->children($node);
        if (in_array($name, ['pPr', 'rPr', 'tblPr', 'tblGrid', 'trPr', 'tcPr', 'sectPr', 'del', 'instrText'])) return '';
        if ($name === 't') return $this->escape($node->textContent);
        if (in_array($name, ['br', 'cr'])) return '<br>';
        if ($name === 'tab') return '&emsp;';
        $html = $this->children($node);
        if ($name === 'p') {
            $tag = 'p';
            if (preg_match('/^(?:Heading|T[ií]tulo)([1-6])$/iu', (string) $this->property($node, 'pStyle'), $match)) $tag = 'h'.$match[1];
            $align = $this->property($node, 'jc');
            $align = $align === 'both' ? 'justify' : $align;
            $style = in_array($align, ['left', 'right', 'center', 'justify']) ? ' style="text-align:'.$align.'"' : '';
            if ($node->getElementsByTagNameNS(self::WORD, 'numPr')->length) return '<ul><li>'.$html.'</li></ul>';
            return '<'.$tag.$style.'>'.$html.'</'.$tag.'>';
        }
        if ($name === 'r') {
            foreach (['b' => 'strong', 'i' => 'em', 'u' => 'u', 'strike' => 's'] as $property => $tag) {
                $value = $this->property($node, $property);
                if ($value !== null && !in_array($value, ['0', 'false', 'none', 'off'])) $html = '<'.$tag.'>'.$html.'</'.$tag.'>';
            }
            $styles = [];
            $color = $this->property($node, 'color');
            if (preg_match('/^[a-f0-9]{6}$/iD', (string) $color)) $styles[] = 'color:#'.$color;
            $size = $this->property($node, 'sz');
            if (ctype_digit((string) $size) && (int) $size <= 300) $styles[] = 'font-size:'.((int) $size / 2).'pt';
            return $styles ? '<span style="'.implode(';', $styles).'">'.$html.'</span>' : $html;
        }
        if ($name === 'tbl') return '<table border="1"><tbody>'.$html.'</tbody></table>';
        if ($name === 'tr') return '<tr>'.$html.'</tr>';
        if ($name === 'tc') {
            $span = $this->property($node, 'gridSpan');
            return '<td'.(ctype_digit((string) $span) ? ' colspan="'.(int) $span.'"' : '').'>'.$html.'</td>';
        }
        if ($name === 'hyperlink') {
            $relationship = $this->relationships[$node->getAttributeNS(self::REL, 'id')] ?? null;
            if ($relationship && preg_match('~^(https?://|mailto:)~i', $relationship['target'])) return '<a href="'.$this->escape($relationship['target']).'">'.$html.'</a>';
        }
        return $html;
    }

    private function image($id)
    {
        $relationship = $this->relationships[$id] ?? null;
        if (!$relationship || $relationship['external']) return '';
        $target = $relationship['target'];
        if (!preg_match('~^media/[a-zA-Z0-9_. -]+$~D', $target)) return '';
        $bytes = $this->entry('word/'.$target, 1024 * 1024);
        if ($bytes === false) return '';
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        if (!in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp'])) return '';
        return '<img alt="Imagen importada" src="data:'.$mime.';base64,'.base64_encode($bytes).'">';
    }

    private function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function invalid($message)
    {
        throw ValidationException::withMessages(['files' => $message]);
    }
}
