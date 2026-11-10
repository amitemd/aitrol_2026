<?php

namespace Sis_medico\Services;

use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class V3ScheduledCareExportService
{
    public function download($rows, $patientDetail = false)
    {
        if ($patientDetail) {
            return $this->downloadPatientsWorkbook($rows);
        }

        $name = $patientDetail ? 'PACIENTES_ATENCIONES_' : 'AGENDA_CLINICA_';
        $fileName = $name.date('Ymd_His').'.csv';

        return new StreamedResponse(function () use ($rows, $patientDetail) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            if ($patientDetail) {
                fputcsv($output, ['Fecha', 'Cédula', 'Paciente', 'Nacimiento', 'Teléfono', 'Dirección', 'Seguro', 'Doctor', 'Especialidad', 'Tipo', 'Estado', 'Alergias', 'Antecedentes patológicos', 'Antecedentes familiares', 'Antecedentes quirúrgicos'], ';');
            } else {
                fputcsv($output, ['Fecha', 'Hora', 'Cédula', 'Paciente', 'Doctor', 'Especialidad', 'Sala', 'Procedimientos', 'Seguro', 'Empresa', 'Responsable', 'Estado', 'Origen', 'Documentos'], ';');
            }
            foreach ($rows as $row) {
                $patient = trim($row->paciente_apellido1.' '.$row->paciente_apellido2.' '.$row->paciente_nombre1.' '.$row->paciente_nombre2);
                $doctor = trim($row->doctor_apellido1.' '.$row->doctor_apellido2.' '.$row->doctor_nombre1);
                $procedures = [];
                if ($row->procedimiento_nombre) {
                    $procedures[] = $row->procedimiento_nombre;
                }
                foreach ($row->additional_procedures as $procedure) {
                    if (! in_array($procedure->nombre, $procedures, true)) {
                        $procedures[] = $procedure->nombre;
                    }
                }
                $origin = (string) $row->omni === 'OM' ? 'Hospitalario / emergencia' : ((string) $row->proc_consul === '1' ? 'Procedimiento' : 'Ambulatorio');
                if ($patientDetail) {
                    fputcsv($output, [$this->date($row->fechaini), $row->id_paciente, $patient, $this->date($row->fecha_nacimiento), trim($row->telefono1.' '.$row->telefono2), $row->direccion, $row->seguro_nombre, $doctor, $row->especialidad_nombre, $origin, $row->status_label, $row->alergias, $row->antecedentes_pat, $row->antecedentes_fam, $row->antecedentes_quir], ';');
                } else {
                    fputcsv($output, [$this->date($row->fechaini), $this->time($row->fechaini), $row->id_paciente, $patient, $doctor, $row->especialidad_nombre, $row->sala_nombre, implode(' | ', $procedures), $row->seguro_nombre, $row->empresa_nombre, trim($row->responsable_apellido1.' '.$row->responsable_nombre1), $row->status_label, $origin, $row->document_count], ';');
                }
            }
            fclose($output);
        }, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$fileName.'"', 'Cache-Control' => 'no-store, no-cache']);
    }

    private function downloadPatientsWorkbook($rows)
    {
        $workbook = $this->patientsWorkbook($rows);
        $fileName = 'PACIENTES_ATENCIONES_'.date('Ymd_His').'.xlsx';
        $path = tempnam(sys_get_temp_dir(), 'aitrol_patients_');
        $this->saveWorkbook($workbook, $path);

        return response()->download($path, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store, no-cache',
        ])->deleteFileAfterSend(true);
    }

    private function saveWorkbook(PHPExcel $workbook, $path)
    {
        set_error_handler(function ($severity, $message, $file) {
            if (strpos((string) $file, 'PHPExcel') !== false
                && in_array($severity, [E_WARNING, E_NOTICE, E_DEPRECATED, E_USER_DEPRECATED], true)) {
                return true;
            }

            return false;
        });

        try {
            PHPExcel_IOFactory::createWriter($workbook, 'Excel2007')->save($path);
        } finally {
            restore_error_handler();
        }
    }

    private function patientsWorkbook($rows)
    {
        $workbook = new PHPExcel;
        $workbook->getProperties()
            ->setCreator('Aitrol Hospitalario')
            ->setTitle('Pacientes y atenciones')
            ->setSubject('Reporte clínico administrativo de pacientes');

        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle('Pacientes');
        $sheet->setShowGridlines(false);
        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A1', 'REPORTE DE PACIENTES Y ATENCIONES');
        $sheet->setCellValue('A2', 'Generado: '.date('d/m/Y').' · Registros: '.count($rows));

        $headers = [
            'Fecha', 'Cédula', 'Paciente', 'Nacimiento', 'Teléfono', 'Dirección', 'Seguro', 'Doctor',
            'Especialidad', 'Tipo de atención', 'Estado', 'Alergias', 'Antecedentes patológicos',
            'Antecedentes familiares', 'Antecedentes quirúrgicos',
        ];
        foreach ($headers as $column => $header) {
            $sheet->setCellValueByColumnAndRow($column, 4, $header);
        }

        $rowNumber = 5;
        foreach ($rows as $row) {
            $patient = trim($row->paciente_apellido1.' '.$row->paciente_apellido2.' '.$row->paciente_nombre1.' '.$row->paciente_nombre2);
            $doctor = trim($row->doctor_apellido1.' '.$row->doctor_apellido2.' '.$row->doctor_nombre1);
            $origin = (string) $row->omni === 'OM' ? 'Hospitalario / emergencia' : ((string) $row->proc_consul === '1' ? 'Procedimiento' : 'Consulta');

            $this->excelDate($sheet, 'A'.$rowNumber, $row->fechaini);
            $this->excelText($sheet, 'B'.$rowNumber, $row->id_paciente);
            $this->excelText($sheet, 'C'.$rowNumber, $patient);
            $this->excelDate($sheet, 'D'.$rowNumber, $row->fecha_nacimiento);
            $this->excelText($sheet, 'E'.$rowNumber, trim($row->telefono1.' '.$row->telefono2));
            $this->excelText($sheet, 'F'.$rowNumber, $row->direccion);
            $this->excelText($sheet, 'G'.$rowNumber, $row->seguro_nombre);
            $this->excelText($sheet, 'H'.$rowNumber, $doctor);
            $this->excelText($sheet, 'I'.$rowNumber, $row->especialidad_nombre);
            $this->excelText($sheet, 'J'.$rowNumber, $origin);
            $this->excelText($sheet, 'K'.$rowNumber, $row->status_label);
            $this->excelText($sheet, 'L'.$rowNumber, $row->alergias);
            $this->excelText($sheet, 'M'.$rowNumber, $row->antecedentes_pat);
            $this->excelText($sheet, 'N'.$rowNumber, $row->antecedentes_fam);
            $this->excelText($sheet, 'O'.$rowNumber, $row->antecedentes_quir);
            $rowNumber++;
        }

        $lastRow = max(4, $rowNumber - 1);
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '0B4F71']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2:O2')->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '436A7E']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'EAF6F8']],
            'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A4:O4')->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '118BA7']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
            'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'D5E7EC']]],
        ]);
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:O'.$lastRow)->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => '24475B']],
                'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true],
                'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => ['rgb' => 'DCE9EE']]],
            ]);
            for ($dataRow = 6; $dataRow <= $lastRow; $dataRow += 2) {
                $sheet->getStyle('A'.$dataRow.':O'.$dataRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F9FB');
            }
            $sheet->getStyle('A5:A'.$lastRow)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->getStyle('D5:D'.$lastRow)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->setAutoFilter('A4:O'.$lastRow);
        }

        $widths = ['A' => 12, 'B' => 16, 'C' => 32, 'D' => 13, 'E' => 20, 'F' => 34, 'G' => 22, 'H' => 30, 'I' => 24, 'J' => 22, 'K' => 16, 'L' => 30, 'M' => 36, 'N' => 36, 'O' => 36];
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->getRowDimension(1)->setRowHeight(27);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(8);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->freezePane('A5');
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1, 4);
        $sheet->getPageMargins()->setTop(0.45)->setRight(0.3)->setLeft(0.3)->setBottom(0.45);
        $sheet->getHeaderFooter()->setOddFooter('&LAitrol Hospitalario&CReporte de pacientes&RPágina &P de &N');
        $sheet->setSelectedCell('A1');

        return $workbook;
    }

    private function excelText($sheet, $cell, $value)
    {
        $sheet->setCellValueExplicit($cell, trim((string) $value), PHPExcel_Cell_DataType::TYPE_STRING);
    }

    private function excelDate($sheet, $cell, $value)
    {
        $timestamp = $value ? strtotime($value) : false;
        if ($timestamp) {
            $sheet->setCellValue($cell, PHPExcel_Shared_Date::FormattedPHPToExcel(
                (int) date('Y', $timestamp),
                (int) date('m', $timestamp),
                (int) date('d', $timestamp)
            ));
        } else {
            $sheet->setCellValueExplicit($cell, '', PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }

    private function date($value)
    {
        $timestamp = $value ? strtotime($value) : false;

        return $timestamp ? date('d/m/Y', $timestamp) : '';
    }

    private function time($value)
    {
        $timestamp = $value ? strtotime($value) : false;

        return $timestamp ? date('H:i', $timestamp) : '';
    }
}
