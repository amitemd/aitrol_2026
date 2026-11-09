<?php

namespace Sis_medico\Services;

use RuntimeException;

class V3PentaxProcedureExportService
{
    public function download($rows, $date)
    {
        $fileName = 'REPORTE_PROCEDIMIENTOS_'.date('Ymd', strtotime($date)).'.xlsx';
        $path = tempnam(sys_get_temp_dir(), 'aitrol_procedimientos_');
        if ($path === false) throw new RuntimeException('No se pudo preparar el archivo del reporte.');

        try {
            $this->writeWorkbook($path, $rows, $date);
        } catch (\Exception $exception) {
            if (is_file($path)) unlink($path);
            throw $exception;
        }

        return response()->download($path, $fileName, array(
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache',
        ))->deleteFileAfterSend(true);
    }

    private function writeWorkbook($path, $rows, $date)
    {
        $book = new \PHPExcel();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Procedimientos');

        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'REPORTE GENERAL DE PROCEDIMIENTOS');
        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', 'Fecha: '.$this->format($date, 'd/m/Y').'  ·  Total: '.count($rows).' procedimiento(s)');

        $headers = array(
            'Fecha', 'Hora inicio', 'Hora fin', 'Cédula', 'Paciente', 'Procedimientos',
            'Sala', 'Modalidad', 'Médico asignado', 'Ayudante 1', 'Ayudante 2',
            'Enfermería', 'Anestesiología', 'Convenio', 'Estado', 'N.º agenda',
        );
        $sheet->fromArray($headers, null, 'A4');

        $rowNumber = 5;
        foreach ($rows as $row) {
            $procedures = array();
            foreach ($row->procedures as $procedure) {
                $name = trim((string) $procedure->observacion) ?: trim((string) $procedure->nombre);
                if ($name !== '' && !in_array($name, $procedures, true)) $procedures[] = $name;
            }

            $sheet->fromArray(array(
                $this->format($row->fechaini, 'd/m/Y'),
                $this->format($row->fechaini, 'H:i'),
                $this->format($row->fechafin, 'H:i'),
                '',
                $row->patient_name,
                implode(' | ', $procedures),
                $row->room_name ?: 'Sin sala',
                $row->est_amb_hos ? 'Hospitalizado' : 'Ambulatorio',
                $row->doctor_name ?: 'Sin asignar',
                $this->person($row->assistant1_lastname1, $row->assistant1_name1),
                $this->person($row->assistant2_lastname1, $row->assistant2_name1),
                $this->person($row->nurse_lastname1, $row->nurse_name1),
                $this->person($row->anesthetist_lastname1, $row->anesthetist_name1),
                $row->insurance_name ?: 'Sin convenio',
                $row->status_label,
                $row->agenda_id,
            ), null, 'A'.$rowNumber);
            $sheet->setCellValueExplicit('D'.$rowNumber, (string) $row->patient_id, \PHPExcel_Cell_DataType::TYPE_STRING);
            $rowNumber++;
        }

        $lastRow = max(5, $rowNumber - 1);
        $this->style($sheet, $lastRow);

        $writer = \PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        $writer->save($path);
        $book->disconnectWorksheets();

        if (!is_file($path) || filesize($path) === 0) {
            throw new RuntimeException('El reporte se generó vacío.');
        }
    }

    private function style($sheet, $lastRow)
    {
        $sheet->getStyle('A1:P1')->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '087EAE')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        ));
        $sheet->getStyle('A2:P2')->applyFromArray(array(
            'font' => array('bold' => true, 'color' => array('rgb' => '17465B')),
            'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'DDF3F7')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        ));
        $sheet->getStyle('A4:P4')->applyFromArray(array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '0B6688')),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        ));
        $sheet->getStyle('A4:P'.$lastRow)->applyFromArray(array(
            'borders' => array('allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '9FB7C3'),
            )),
        ));
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:P'.$lastRow)->getAlignment()
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP)
                ->setWrapText(true);
            for ($row = 6; $row <= $lastRow; $row += 2) {
                $sheet->getStyle('A'.$row.':P'.$row)->getFill()
                    ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F0F8FA');
            }
        }

        $widths = array('A'=>13, 'B'=>12, 'C'=>12, 'D'=>16, 'E'=>34, 'F'=>52, 'G'=>28, 'H'=>16,
            'I'=>27, 'J'=>24, 'K'=>24, 'L'=>24, 'M'=>24, 'N'=>22, 'O'=>18, 'P'=>14);
        foreach ($widths as $column => $width) $sheet->getColumnDimension($column)->setWidth($width);

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(32);
        $sheet->freezePane('A5');
        $sheet->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setRight(0.25)->setLeft(0.25)->setBottom(0.4);
    }

    private function format($value, $format)
    {
        $timestamp = $value ? strtotime($value) : false;
        return $timestamp ? date($format, $timestamp) : '';
    }

    private function person($lastName, $firstName)
    {
        $name = trim($lastName.' '.$firstName);
        return $name !== '' ? $name : 'Sin asignar';
    }
}
