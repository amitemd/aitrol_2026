<?php

namespace Sis_medico\Services;

/** Produces standard OOXML packages without a remote Office service. */
class OfficeOpenXml
{
    private const R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/';
    private const S = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const P = 'http://schemas.openxmlformats.org/presentationml/2006/main';
    private const A = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private $files = [];
    private $types = [];

    public function export($kind, array $data)
    {
        $this->files = []; $this->types = [];
        $kind === 'spreadsheet' ? $this->workbook($data) : $this->presentation($data);
        $overrides = '';
        foreach ($this->types as $path => $type) $overrides .= '<Override PartName="/'.$path.'" ContentType="application/vnd.openxmlformats-officedocument.'.$type.'+xml"/>';
        $this->files['[Content_Types].xml'] = $this->xml('<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/><Default Extension="jpeg" ContentType="image/jpeg"/><Default Extension="svg" ContentType="image/svg+xml"/>'.$overrides.'</Types>');
        $path = tempnam(sys_get_temp_dir(), 'office-export-');
        try {
            $zip = new \ZipArchive;
            if ($zip->open($path, \ZipArchive::OVERWRITE) !== true) throw new \RuntimeException('No se pudo generar el archivo.');
            foreach ($this->files as $name => $bytes) $zip->addFromString($name, $bytes);
            $zip->close();
            return file_get_contents($path);
        } finally { if (is_file($path)) unlink($path); }
    }

    private function xml($body) { return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$body; }
    private function e($text) { return htmlspecialchars(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', (string) $text), ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    private function part($path, $type, $body) { $this->types[$path] = $type; $this->files[$path] = $this->xml($body); }
    private function rels($path, $items)
    {
        $xml = '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($items as $id => $item) $xml .= '<Relationship Id="'.$id.'" Type="'.self::R.$item[0].'" Target="'.$this->e($item[1]).'"/>';
        $this->files[$path] = $this->xml($xml.'</Relationships>');
    }

    private function workbook($data)
    {
        $this->rels('_rels/.rels', ['rId1' => ['officeDocument', 'xl/workbook.xml']]);
        $sheets = ''; $rels = ['styles' => ['styles', 'styles.xml']];
        $fonts = ['<font><sz val="11"/><name val="Arial"/></font>'];
        $fills = ['<fill><patternFill patternType="none"/></fill>', '<fill><patternFill patternType="gray125"/></fill>'];
        $xfs = ['<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>']; $styleIds = [];
        foreach ($data['sheets'] as $index => $sheet) {
            $number = $index + 1; $rows = [];
            foreach ($sheet['cells'] as $address => $cell) {
                $key = json_encode(array_diff_key($cell, ['value' => true]));
                if (!isset($styleIds[$key])) {
                    $fontId = count($fonts); $fillId = count($fills); $styleIds[$key] = count($xfs);
                    $fonts[] = '<font>'.(!empty($cell['bold']) ? '<b/>' : '').'<sz val="11"/><color rgb="FF'.substr($cell['color'], 1).'"/><name val="Arial"/></font>';
                    $fills[] = '<fill><patternFill patternType="solid"><fgColor rgb="FF'.substr($cell['background'], 1).'"/><bgColor indexed="64"/></patternFill></fill>';
                    $numFmt = ['general' => 0, 'number' => 2, 'currency' => 164, 'percent' => 10][$cell['format']];
                    $xfs[] = '<xf numFmtId="'.$numFmt.'" fontId="'.$fontId.'" fillId="'.$fillId.'" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1" applyNumberFormat="1"><alignment horizontal="'.$cell['align'].'"/></xf>';
                }
                $value = $cell['value']; $attributes = ' r="'.$address.'" s="'.$styleIds[$key].'"';
                if (str_starts_with($value, '=')) {
                    $formula = str_replace(';', ',', strtoupper(substr($value, 1)));
                    $formula = preg_replace(['/\bSUMA\s*\(/', '/\bPROMEDIO\s*\(/', '/\bCONTAR\s*\(/'], ['SUM(', 'AVERAGE(', 'COUNT('], $formula);
                    $xml = '<c'.$attributes.'><f>'.$this->e($formula).'</f></c>';
                } elseif ($value !== '' && is_numeric($value)) $xml = '<c'.$attributes.'><v>'.$this->e($value).'</v></c>';
                else $xml = '<c'.$attributes.' t="inlineStr"><is><t xml:space="preserve">'.$this->e($value).'</t></is></c>';
                preg_match('/\d+$/', $address, $match); $rows[(int) $match[0]][OfficeSuiteContent::columnNumber(preg_replace('/\d/', '', $address))] = $xml;
            }
            ksort($rows); $body = '';
            foreach ($rows as $row => $cells) { ksort($cells); $body .= '<row r="'.$row.'">'.implode('', $cells).'</row>'; }
            $this->part('xl/worksheets/sheet'.$number.'.xml', 'spreadsheetml.worksheet', '<worksheet xmlns="'.self::S.'"><sheetViews><sheetView workbookViewId="0"/></sheetViews><sheetFormatPr defaultRowHeight="18"/><cols><col min="1" max="'.$sheet['cols'].'" width="18" customWidth="1"/></cols><sheetData>'.$body.'</sheetData></worksheet>');
            $sheets .= '<sheet name="'.$this->e($sheet['name']).'" sheetId="'.$number.'" r:id="sheet'.$number.'"/>';
            $rels['sheet'.$number] = ['worksheet', 'worksheets/sheet'.$number.'.xml'];
        }
        $this->part('xl/workbook.xml', 'spreadsheetml.sheet.main', '<workbook xmlns="'.self::S.'" xmlns:r="'.rtrim(self::R, '/').'"><sheets>'.$sheets.'</sheets><calcPr calcId="0" fullCalcOnLoad="1" forceFullCalc="1"/></workbook>');
        $this->rels('xl/_rels/workbook.xml.rels', $rels);
        $this->part('xl/styles.xml', 'spreadsheetml.styles', '<styleSheet xmlns="'.self::S.'"><numFmts count="1"><numFmt numFmtId="164" formatCode="&quot;$&quot;#,##0.00"/></numFmts><fonts count="'.count($fonts).'">'.implode('', $fonts).'</fonts><fills count="'.count($fills).'">'.implode('', $fills).'</fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="'.count($xfs).'">'.implode('', $xfs).'</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>');
    }

    private function ns() { return ' xmlns:p="'.self::P.'" xmlns:a="'.self::A.'" xmlns:r="'.rtrim(self::R, '/').'"'; }
    private function group() { return '<p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>'; }
    private function colorMap() { return '<p:clrMap accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" bg1="lt1" bg2="lt2" folHlink="folHlink" hlink="hlink" tx1="dk1" tx2="dk2"/>'; }

    private function textBox($id, $text, $x, $y, $width, $height, $size, $color, $bold = false, $family = 'Arial')
    {
        $paragraphs = '';
        foreach (explode("\n", $text) as $line) $paragraphs .= '<a:p><a:r><a:rPr lang="es-EC" sz="'.($size * 100).'" b="'.($bold ? 1 : 0).'"><a:solidFill><a:srgbClr val="'.substr($color, 1).'"/></a:solidFill><a:latin typeface="'.$this->e($family).'"/></a:rPr><a:t>'.$this->e($line).'</a:t></a:r><a:endParaRPr lang="es-EC"/></a:p>';
        return '<p:sp><p:nvSpPr><p:cNvPr id="'.$id.'" name="Texto '.$id.'"/><p:cNvSpPr txBox="1"/><p:nvPr/></p:nvSpPr><p:spPr><a:xfrm><a:off x="'.$x.'" y="'.$y.'"/><a:ext cx="'.$width.'" cy="'.$height.'"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom><a:noFill/></p:spPr><p:txBody><a:bodyPr wrap="square"><a:spAutoFit/></a:bodyPr><a:lstStyle/>'.$paragraphs.'</p:txBody></p:sp>';
    }

    private function presentation($data)
    {
        $this->rels('_rels/.rels', ['rId1' => ['officeDocument', 'ppt/presentation.xml']]);
        $layoutIds = ''; $masterRels = ['layout' => ['slideLayout', '../slideLayouts/slideLayout1.xml'], 'theme' => ['theme', '../theme/theme1.xml']];
        $slideIds = ''; $rels = ['master' => ['slideMaster', 'slideMasters/slideMaster1.xml'], 'props' => ['presProps', 'presProps.xml']];
        foreach ($data['slides'] as $i => $slide) {
            $pictures = !empty($slide['images']) ? $slide['images'] : ($slide['image'] !== '' ? [$slide['image']] : []);
            $n = $i + 1; $hasImage = count($pictures) > 0; $slideRels = ['layout' => ['slideLayout', '../slideLayouts/slideLayout1.xml']];
            $titleY = $slide['layout'] === 'title' ? 1000000 : 400000;
            $shapes = $this->textBox(2, $slide['title'], 550000, $titleY, 11000000, 1300000, 36, $slide['color'], true);
            $shapes .= $this->textBox(3, $slide['body'], 550000, $titleY + 1400000, $hasImage || $slide['layout'] === 'split' ? 5300000 : 11000000, 4200000, $slide['fontSize'], $slide['color']);
            $originalImages = [];
            if (!empty($slide['elements'])) {
                $shapes = ''; $pictures = [];
                foreach ($slide['elements'] as $index => $element) {
                    if ($element['type'] === 'image') { $originalImages[] = $element; $pictures[] = $element['image']; }
                    else $shapes .= $this->textBox(200 + $index, $element['text'], (int) round($element['x'] * 121920), (int) round($element['y'] * 68580), (int) round($element['width'] * 121920), (int) round($element['height'] * 68580), $element['fontSize'], $element['color'], $element['bold'], $element['fontFamily']);
                }
            }
            foreach ($pictures as $j => $picture) {
                preg_match('~^data:image/(png|jpeg|svg\+xml);base64,(.+)$~s', $picture, $m); $bytes = base64_decode($m[2]);
                $extension = $m[1] === 'svg+xml' ? 'svg' : $m[1]; $suffix = $j ? '-'.$j : ''; $rid = $j ? 'image'.$j : 'image';
                $path = 'ppt/media/image'.$n.$suffix.'.'.$extension; $this->files[$path] = $bytes;
                $slideRels[$rid] = ['image', '../media/image'.$n.$suffix.'.'.$extension];
                $columns = count($pictures) > 1 ? 2 : 1; $rows = (int) ceil(count($pictures) / $columns);
                $boxW = (int) (5100000 / $columns); $boxH = (int) (3900000 / $rows);
                if ($extension === 'svg') {
                    $dom = new \DOMDocument; $dom->loadXML($bytes, LIBXML_NONET);
                    $view = preg_split('/[ ,]+/', trim($dom->documentElement->getAttribute('viewBox')));
                    $info = [max(1, (float) ($view[2] ?? $dom->documentElement->getAttribute('width'))), max(1, (float) ($view[3] ?? $dom->documentElement->getAttribute('height')))];
                } else $info = getimagesizefromstring($bytes);
                $scale = min($boxW / $info[0], $boxH / $info[1]); $w = (int) ($info[0] * $scale); $h = (int) ($info[1] * $scale);
                $x = 6500000 + ($j % $columns) * $boxW; $y = 2100000 + (int) floor($j / $columns) * $boxH;
                if (isset($originalImages[$j])) { $element = $originalImages[$j]; $x = (int) round($element['x'] * 121920); $y = (int) round($element['y'] * 68580); $w = (int) round($element['width'] * 121920); $h = (int) round($element['height'] * 68580); }
                $blip = $extension === 'svg' ? '<a:blip><a:extLst><a:ext uri="{96DAC541-7B7A-43D3-8B79-37D633B846F1}"><asvg:svgBlip xmlns:asvg="http://schemas.microsoft.com/office/drawing/2016/SVG/main" r:embed="'.$rid.'"/></a:ext></a:extLst></a:blip>' : '<a:blip r:embed="'.$rid.'"/>';
                $shapes .= '<p:pic><p:nvPicPr><p:cNvPr id="'.(4 + $j).'" name="Imagen"/><p:cNvPicPr><a:picLocks noChangeAspect="1"/></p:cNvPicPr><p:nvPr/></p:nvPicPr><p:blipFill>'.$blip.'<a:stretch><a:fillRect/></a:stretch></p:blipFill><p:spPr><a:xfrm><a:off x="'.$x.'" y="'.$y.'"/><a:ext cx="'.$w.'" cy="'.$h.'"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></p:spPr></p:pic>';
            }
            if (!empty($slide['backgroundLayers'])) {
                $backgroundShapes = ''; $backgroundRels = ['master' => ['slideMaster', '../slideMasters/slideMaster1.xml']];
                foreach ($slide['backgroundLayers'] as $j => $layer) {
                    $x = (int) round($layer['x'] * 121920); $y = (int) round($layer['y'] * 68580);
                    $w = (int) round($layer['width'] * 121920); $h = (int) round($layer['height'] * 68580); $shapeId = 1000 + $j;
                    $transform = '<a:xfrm><a:off x="'.$x.'" y="'.$y.'"/><a:ext cx="'.$w.'" cy="'.$h.'"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom>';
                    if ($layer['image'] === '') {
                        $backgroundShapes .= '<p:sp><p:nvSpPr><p:cNvPr id="'.$shapeId.'" name="Fondo"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr><p:spPr>'.$transform.'<a:solidFill><a:srgbClr val="'.substr($layer['color'], 1).'"/></a:solidFill><a:ln><a:noFill/></a:ln></p:spPr></p:sp>';
                    } else {
                        preg_match('~^data:image/(png|jpeg|svg\+xml);base64,(.+)$~s', $layer['image'], $m);
                        $ext = $m[1] === 'svg+xml' ? 'svg' : $m[1]; $file = 'background'.$n.'-'.$j.'.'.$ext; $rid = 'bg'.$j;
                        $this->files['ppt/media/'.$file] = base64_decode($m[2]); $backgroundRels[$rid] = ['image', '../media/'.$file];
                        $blip = $ext === 'svg' ? '<a:blip><a:extLst><a:ext uri="{96DAC541-7B7A-43D3-8B79-37D633B846F1}"><asvg:svgBlip xmlns:asvg="http://schemas.microsoft.com/office/drawing/2016/SVG/main" r:embed="'.$rid.'"/></a:ext></a:extLst></a:blip>' : '<a:blip r:embed="'.$rid.'"/>';
                        $backgroundShapes .= '<p:pic><p:nvPicPr><p:cNvPr id="'.$shapeId.'" name="Fondo"/><p:cNvPicPr/><p:nvPr/></p:nvPicPr><p:blipFill>'.$blip.'<a:stretch><a:fillRect/></a:stretch></p:blipFill><p:spPr>'.$transform.'</p:spPr></p:pic>';
                    }
                }
                $backgroundPath = 'ppt/slideLayouts/background'.$n.'.xml';
                $this->part($backgroundPath, 'presentationml.slideLayout', '<p:sldLayout'.$this->ns().' type="blank"><p:cSld><p:spTree>'.$this->group().$backgroundShapes.'</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sldLayout>');
                $this->rels('ppt/slideLayouts/_rels/background'.$n.'.xml.rels', $backgroundRels);
                $slideRels['layout'] = ['slideLayout', '../slideLayouts/background'.$n.'.xml'];
                $layoutIds .= '<p:sldLayoutId id="'.(2147483649 + $n).'" r:id="background'.$n.'"/>';
                $masterRels['background'.$n] = ['slideLayout', '../slideLayouts/background'.$n.'.xml'];
            }
            $this->part('ppt/slides/slide'.$n.'.xml', 'presentationml.slide', '<p:sld'.$this->ns().'><p:cSld><p:bg><p:bgPr><a:solidFill><a:srgbClr val="'.substr($slide['background'], 1).'"/></a:solidFill><a:effectLst/></p:bgPr></p:bg><p:spTree>'.$this->group().$shapes.'</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sld>');
            $this->rels('ppt/slides/_rels/slide'.$n.'.xml.rels', $slideRels);
            $slideIds .= '<p:sldId id="'.(256 + $i).'" r:id="slide'.$n.'"/>'; $rels['slide'.$n] = ['slide', 'slides/slide'.$n.'.xml'];
        }
        $this->part('ppt/presentation.xml', 'presentationml.presentation.main', '<p:presentation'.$this->ns().'><p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="master"/></p:sldMasterIdLst><p:sldIdLst>'.$slideIds.'</p:sldIdLst><p:sldSz cx="12192000" cy="6858000"/><p:notesSz cx="6858000" cy="9144000"/></p:presentation>');
        $this->rels('ppt/_rels/presentation.xml.rels', $rels);
        $this->part('ppt/presProps.xml', 'presentationml.presProps', '<p:presentationPr'.$this->ns().'/>');
        $this->part('ppt/slideMasters/slideMaster1.xml', 'presentationml.slideMaster', '<p:sldMaster'.$this->ns().'><p:cSld><p:spTree>'.$this->group().'</p:spTree></p:cSld>'.$this->colorMap().'<p:sldLayoutIdLst><p:sldLayoutId id="2147483649" r:id="layout"/>'.$layoutIds.'</p:sldLayoutIdLst><p:txStyles><p:titleStyle/><p:bodyStyle/><p:otherStyle/></p:txStyles></p:sldMaster>');
        $this->rels('ppt/slideMasters/_rels/slideMaster1.xml.rels', $masterRels);
        $this->part('ppt/slideLayouts/slideLayout1.xml', 'presentationml.slideLayout', '<p:sldLayout'.$this->ns().' type="blank" preserve="1"><p:cSld name="En blanco"><p:spTree>'.$this->group().'</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sldLayout>');
        $this->rels('ppt/slideLayouts/_rels/slideLayout1.xml.rels', ['master' => ['slideMaster', '../slideMasters/slideMaster1.xml']]);
        $colors = ''; foreach (['dk1' => '000000', 'lt1' => 'FFFFFF', 'dk2' => '17465C', 'lt2' => 'EDF5F8', 'accent1' => '087E9C', 'accent2' => 'E6813D', 'accent3' => '27815B', 'accent4' => '8166A5', 'accent5' => '45A9B8', 'accent6' => 'D5AF39', 'hlink' => '0563C1', 'folHlink' => '954F72'] as $name => $color) $colors .= '<a:'.$name.'><a:srgbClr val="'.$color.'"/></a:'.$name.'>';
        $fill = '<a:solidFill><a:schemeClr val="phClr"/></a:solidFill>';
        $line = '<a:ln w="9525">'.$fill.'<a:prstDash val="solid"/></a:ln>';
        $this->part('ppt/theme/theme1.xml', 'theme', '<a:theme xmlns:a="'.self::A.'" name="Oficina V3"><a:themeElements><a:clrScheme name="Oficina">'.$colors.'</a:clrScheme><a:fontScheme name="Arial"><a:majorFont><a:latin typeface="Arial"/><a:ea typeface=""/><a:cs typeface=""/></a:majorFont><a:minorFont><a:latin typeface="Arial"/><a:ea typeface=""/><a:cs typeface=""/></a:minorFont></a:fontScheme><a:fmtScheme name="Oficina"><a:fillStyleLst>'.str_repeat($fill, 3).'</a:fillStyleLst><a:lnStyleLst>'.str_repeat($line, 3).'</a:lnStyleLst><a:effectStyleLst>'.str_repeat('<a:effectStyle><a:effectLst/></a:effectStyle>', 3).'</a:effectStyleLst><a:bgFillStyleLst>'.str_repeat($fill, 3).'</a:bgFillStyleLst></a:fmtScheme></a:themeElements></a:theme>');
    }
}
