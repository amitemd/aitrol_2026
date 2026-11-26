<?php

namespace Sis_medico\Services;

use setasign\Fpdi\Fpdi;

class HospitalSurgeryChecklistPdf
{
    public function render(array $context, array $notes = [], ?string $logo = null): string
    {
        if (! class_exists(Fpdi::class) || ! class_exists(\FPDF::class)) {
            return app('dompdf.wrapper')
                ->setOptions(['isRemoteEnabled' => false])
                ->loadView('hospital.documentos.lista-cirugia-segura-pdf', [
                    'context' => $context,
                    'notes' => $notes,
                    'fields' => HospitalEpisodeFormCatalog::fields('LVCS'),
                    'logo' => $logo,
                ])
                ->setPaper('a4')
                ->output();
        }

        // Positions in points are relative to the bundled, single-page annex.
        $pdf = new Fpdi('P', 'pt', 'A4');
        $pdf->SetMargins(78, 44, 54);
        $pdf->SetAutoPageBreak(false);
        $pdf->setSourceFile(public_path('documentos-hospitalarios/DNCSS-MSP-003-lista-verificacion-cirugia-segura.pdf'));
        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($template);
        $pdf->SetTitle('Lista de verificacion de cirugia segura');

        if ($logo && ($dimensions = getimagesize($logo))) {
            $scale = min(116 / $dimensions[0], 45 / $dimensions[1]);
            $width = $dimensions[0] * $scale;
            $height = $dimensions[1] * $scale;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(51, 30, 132, 81, 'F');
            $pdf->Image($logo, 117 - $width / 2, 70 - $height / 2, $width, $height);
        }

        $pdf->SetFillColor(195, 214, 235);
        $pdf->Rect(185, 30, 247, 30, 'F');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetXY(185, 30);
        $pdf->Cell(247, 30, $this->fit($pdf, $context['institution'] ?? '', 239), 0, 0, 'C');

        $pdf->SetFont('Helvetica', '', 6.4);
        foreach ([
            'establishment' => [172, 202.1, 157],
            'unicodigo' => [379, 202.1, 74],
            'date' => [482, 202.1, 48],
            'patient_name' => [160, 209.3, 169],
            'age' => [362, 209.3, 40],
            'history_number' => [175, 216.2, 154],
            'operating_room' => [388, 216.2, 142],
            'procedure' => [139, 223.3, 190],
        ] as $key => [$x, $baseline, $width]) {
            $value = $key === 'age' && is_numeric($context[$key] ?? null)
                ? (int) $context[$key] : ($context[$key] ?? '');
            $pdf->Text($x, $baseline, $this->fit($pdf, (string) $value, $width));
        }

        $procedure = $this->encode($context['procedure'] ?? '');
        if ($pdf->GetStringWidth($procedure) > 190) {
            $pdf->SetAutoPageBreak(true, 82);
            $pdf->SetXY(78, 708);
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->Cell(0, 11, 'Procedimiento programado (texto completo)', 0, 1);
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->MultiCell(0, 10, $procedure, 0, 'L');
        }

        if ($notes) {
            $pdf->SetAutoPageBreak(true, 44);
            $pdf->AddPage();
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->Cell(0, 20, 'DNCSS-MSP-003 - Registro guardado', 0, 1);
            foreach ($notes as $label => $value) {
                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->MultiCell(0, 14, $this->encode($label));
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->MultiCell(0, 14, $this->encode($value));
                $pdf->Ln(6);
            }
        }

        return $pdf->Output('S');
    }

    private function fit(Fpdi $pdf, string $value, float $width): string
    {
        $value = $this->encode(trim(preg_replace('/\s+/u', ' ', $value) ?? $value));
        if ($pdf->GetStringWidth($value) <= $width) {
            return $value;
        }
        $length = strlen($value);
        while ($length > 0 && $pdf->GetStringWidth(substr($value, 0, $length).'...') > $width) {
            $length--;
        }

        return rtrim(substr($value, 0, $length)).'...';
    }

    private function encode(string $value): string
    {
        return mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    }
}
