<?php

namespace Sis_medico\Services;

use DateTimeImmutable;
use DateTimeInterface;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Alignment;

class ClinicalOfficialExcelWorkbookService
{
    const TEMPLATE_012A = 'excel/clinical/formato_012a_2021.xlsx';
    const TEMPLATE_012B = 'excel/clinical/formato_012b_2021.xlsx';
    const TEMPLATE_053 = 'excel/clinical/formulario_053_2021.xlsx';

    public function build012($order, $patient, $insurance, $company, $doctor, array $diagnoses, $clinicalHistoryNumber = '')
    {
        $workbook = $this->loadTemplate(self::TEMPLATE_012A);
        $reportWorkbook = $this->loadTemplate(self::TEMPLATE_012B);
        $reportSheet = $reportWorkbook->getSheet(0);
        $reportSheet->setTitle('12B Informe');
        $workbook->getSheet(0)->setTitle('12A Solicitud');
        $workbook->addExternalSheet($reportSheet);

        $requestSheet = $workbook->getSheetByName('12A Solicitud');
        $reportSheet = $workbook->getSheetByName('12B Informe');
        $header = $this->headerData($order, $patient, $insurance, $company, $clinicalHistoryNumber);

        $this->fill012PatientHeader($requestSheet, $header, false);
        $this->fill012PatientHeader($reportSheet, $header, true);
        $this->fill012Request($requestSheet, $order, $doctor, $diagnoses);
        $this->fill012Report($reportSheet, $order, $doctor);

        $requestSheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(1);
        $requestSheet->getPageSetup()->setPrintArea('A1:BS43');
        $reportSheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(1);
        $reportSheet->getPageSetup()->setPrintArea('A1:BS57');
        $workbook->setActiveSheetIndex(0);

        return $workbook;
    }

    public function build053($order, $patient, $insurance, $company, $doctor, array $diagnoses, $procedures = '', $clinicalHistoryNumber = '')
    {
        $workbook = $this->loadTemplate(self::TEMPLATE_053);
        $sheet = $workbook->getSheet(0);
        $sheet->setTitle('Formulario 053');

        $this->clear053SampleData($sheet);
        $header = $this->headerData($order, $patient, $insurance, $company, $clinicalHistoryNumber);

        $this->text($sheet, 'A3', $header['institution']);
        $this->text($sheet, 'S3', $header['facility_code']);
        $this->text($sheet, 'Y3', $header['facility']);
        $this->text($sheet, 'AU3', $header['history_number']);
        $this->text($sheet, 'BM3', $header['file_number']);
        $this->text($sheet, 'A6', $header['last_name_1']);
        $this->text($sheet, 'R6', $header['last_name_2']);
        $this->text($sheet, 'AG6', $header['first_name_1']);
        $this->text($sheet, 'AS6', $header['first_name_2']);
        $this->text($sheet, 'BF6', $header['sex']);
        $this->date($sheet, 'BI6', $header['birth_date']);
        $this->text($sheet, 'BP6', $header['age_value']);
        $this->mark($sheet, $this->ageMarkerCell($header['age_unit'], array('H' => 'BT6', 'D' => 'BV6', 'M' => 'BX6', 'A' => 'BZ6')));
        $this->text($sheet, 'A8', $header['phone']);
        $this->mark($sheet, 'AU7');
        $this->text($sheet, 'M12', $header['city']);
        $this->text($sheet, 'X12', '');

        $summary = $this->plain($this->value($order, 'motivo_consulta'));
        $clinicalSummary = $this->plain($this->value($order, 'resumen_clinico'));
        if ($clinicalSummary !== '') {
            $summary .= ($summary !== '' ? "\n\n" : '') . $clinicalSummary;
        }
        if ($this->plain($procedures) !== '') {
            $summary .= ($summary !== '' ? "\n\n" : '') . 'PROCEDIMIENTOS SOLICITADOS: ' . $this->plain($procedures);
        }
        $this->text($sheet, 'G17', $summary);
        $sheet->getStyle('G17')->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->fillTextRows($sheet, array('A26', 'A27', 'A28', 'A29'), $this->value($order, 'observacion_medica'), 180);
        $this->fillDiagnoses($sheet, $diagnoses, array(
            array('description' => 'B32', 'code' => 'AE32', 'pre' => 'AJ32', 'def' => 'AL32'),
            array('description' => 'B33', 'code' => 'AE33', 'pre' => 'AJ33', 'def' => 'AL33'),
            array('description' => 'B34', 'code' => 'AE34', 'pre' => 'AJ34', 'def' => 'AL34'),
            array('description' => 'AO32', 'code' => 'BT32', 'pre' => 'BX32', 'def' => 'BZ32'),
            array('description' => 'AO33', 'code' => 'BT33', 'pre' => 'BX33', 'def' => 'BZ33'),
            array('description' => 'AO34', 'code' => 'BT34', 'pre' => 'BX34', 'def' => 'BZ34'),
        ));
        $this->fillProfessional($sheet, $doctor, $this->value($order, 'fecha_orden'), array(
            'date' => 'A38', 'time' => 'K38', 'first_name' => 'S38',
            'last_name_1' => 'AO38', 'last_name_2' => 'BJ38', 'id' => 'A40',
        ));

        $sheet->getPageSetup()->setPrintArea('A1:CA90');
        $workbook->setActiveSheetIndex(0);

        return $workbook;
    }

    public function download(PHPExcel $workbook, $fileName)
    {
        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $this->plain($fileName));
        $safeName = trim($safeName, '_') ?: 'FORMULARIO_CLINICO';
        $path = tempnam(sys_get_temp_dir(), 'aitrol_excel_');
        PHPExcel_IOFactory::createWriter($workbook, 'Excel2007')->save($path);

        return response()->download($path, $safeName . '.xlsx', array(
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ))->deleteFileAfterSend(true);
    }

    private function loadTemplate($relativePath)
    {
        set_error_handler(function ($severity, $message, $file) {
            if (strpos((string) $file, 'PHPExcel') !== false
                && in_array($severity, array(E_WARNING, E_NOTICE, E_DEPRECATED, E_USER_DEPRECATED), true)) {
                return true;
            }
            return false;
        });

        try {
            return PHPExcel_IOFactory::load(resource_path($relativePath));
        } finally {
            restore_error_handler();
        }
    }

    private function fill012PatientHeader($sheet, array $data, $report)
    {
        $cells = $report ? array(
            'institution' => 'A3', 'facility_code' => 'N3', 'facility' => 'T3',
            'history_number' => 'AL3', 'file_number' => 'BD3',
            'last_name_1' => 'A6', 'last_name_2' => 'P6', 'first_name_1' => 'AC6',
            'first_name_2' => 'AP6', 'sex' => 'AZ6', 'birth_date' => 'BC6', 'age_value' => 'BI6',
        ) : array(
            'institution' => 'A3', 'facility_code' => 'P3', 'facility' => 'V3',
            'history_number' => 'AN3', 'file_number' => 'BF3',
            'last_name_1' => 'A6', 'last_name_2' => 'P6', 'first_name_1' => 'AC6',
            'first_name_2' => 'AP6', 'sex' => 'AZ6', 'birth_date' => 'BC6', 'age_value' => 'BI6',
        );

        foreach ($cells as $key => $cell) {
            if ($key === 'birth_date') {
                $this->date($sheet, $cell, $data[$key]);
            } else {
                $this->text($sheet, $cell, $data[$key]);
            }
        }
        $this->mark($sheet, $this->ageMarkerCell($data['age_unit'], array('H' => 'BL6', 'D' => 'BN6', 'M' => 'BP6', 'A' => 'BR6')));
        if ($report) {
            $this->text($sheet, 'BO3', '1');
        }
    }

    private function fill012Request($sheet, $order, $doctor, array $diagnoses)
    {
        $service = $this->upper($this->value($order, 'servicio'));
        $serviceMarkers = array(
            'EMERGENCIA' => 'F10',
            'CONSULTA EXTERNA' => 'O10',
            'HOSPITALIZACION' => 'X10',
        );
        if (isset($serviceMarkers[$service])) {
            $this->mark($sheet, $serviceMarkers[$service]);
        }

        foreach (array('urgente' => 'BG10', 'rutina' => 'BL10', 'control' => 'BR10') as $field => $cell) {
            if ($this->truthy($this->value($order, $field))) {
                $this->mark($sheet, $cell);
            }
        }

        foreach (array(
            'rx_convencional' => 'G13', 'rx_portatil' => 'M13', 'tomografia' => 'U13',
            'resonancia' => 'AB13', 'ecografia' => 'AI13', 'mamografia' => 'AQ13',
            'procedimiento' => 'AZ13', 'otros' => 'BE13',
        ) as $field => $cell) {
            if ($this->truthy($this->value($order, $field))) {
                $this->mark($sheet, $cell);
            }
        }
        $sedation = $this->value($order, 'sedacion');
        if ($sedation !== '') {
            $this->mark($sheet, $this->truthy($sedation) ? 'BN13' : 'BR13');
        }

        $description = $this->plain($this->value($order, 'descripcion'));
        $other = $this->plain($this->value($order, 'texto_otros'));
        if ($other !== '') {
            $description .= ($description !== '' ? ' - ' : '') . $other;
        }
        $this->text($sheet, 'I14', $description);
        $contaminated = $this->value($order, 'paciente_contaminado');
        if ($contaminated !== '') {
            $this->mark($sheet, $this->truthy($contaminated) ? 'AM19' : 'AQ19');
        }
        $this->fillTextRows($sheet, array('A20', 'A21', 'A22'), $this->value($order, 'motivo'), 180);
        $this->fillTextRows($sheet, array('A25', 'A26', 'A27', 'A28', 'A29', 'A30'), $this->value($order, 'cuadro_clinico'), 180);

        $this->fillDiagnoses($sheet, $diagnoses, array(
            array('description' => 'B33', 'code' => 'AC33', 'pre' => 'AG33', 'def' => 'AI33'),
            array('description' => 'B34', 'code' => 'AC34', 'pre' => 'AG34', 'def' => 'AI34'),
            array('description' => 'B35', 'code' => 'AC35', 'pre' => 'AG35', 'def' => 'AI35'),
            array('description' => 'AL33', 'code' => 'BL33', 'pre' => 'BP33', 'def' => 'BR33'),
            array('description' => 'AL34', 'code' => 'BL34', 'pre' => 'BP34', 'def' => 'BR34'),
            array('description' => 'AL35', 'code' => 'BL35', 'pre' => 'BP35', 'def' => 'BR35'),
        ));
        $this->fillProfessional($sheet, $doctor, $this->value($order, 'fecha_orden'), array(
            'date' => 'A39', 'time' => 'I39', 'first_name' => 'P39',
            'last_name_1' => 'AK39', 'last_name_2' => 'BD39', 'id' => 'A41',
        ));
    }

    private function fill012Report($sheet, $order, $doctor)
    {
        $service = $this->upper($this->value($order, 'servicio'));
        $markers = array('EMERGENCIA' => 'AJ10', 'CONSULTA EXTERNA' => 'AS10', 'HOSPITALIZACION' => 'BB10');
        if (isset($markers[$service])) {
            $this->mark($sheet, $markers[$service]);
        }
        $this->text($sheet, 'T10', $this->personName($doctor));

        foreach (array(
            'rx_convencional' => 'G13', 'rx_portatil' => 'M13', 'tomografia' => 'U13',
            'resonancia' => 'AB13', 'ecografia' => 'AI13', 'mamografia' => 'AQ13',
            'procedimiento' => 'AZ13', 'otros' => 'BE13',
        ) as $field => $cell) {
            if ($this->truthy($this->value($order, $field))) {
                $this->mark($sheet, $cell);
            }
        }
        $sedation = $this->value($order, 'sedacion');
        if ($sedation !== '') {
            $this->mark($sheet, $this->truthy($sedation) ? 'BN13' : 'BR13');
        }

        $description = $this->plain($this->value($order, 'descripcion'));
        $other = $this->plain($this->value($order, 'texto_otros'));
        if ($other !== '') {
            $description .= ($description !== '' ? ' - ' : '') . $other;
        }
        $this->text($sheet, 'I14', $description);
    }

    private function fillDiagnoses($sheet, array $diagnoses, array $targets)
    {
        foreach ($targets as $target) {
            foreach ($target as $cell) {
                $this->text($sheet, $cell, '');
            }
        }

        foreach (array_slice($diagnoses, 0, count($targets)) as $index => $diagnosis) {
            $target = $targets[$index];
            $this->text($sheet, $target['description'], isset($diagnosis['description']) ? $diagnosis['description'] : '');
            $this->text($sheet, $target['code'], isset($diagnosis['code']) ? $diagnosis['code'] : '');
            $type = $this->upper(isset($diagnosis['type']) ? $diagnosis['type'] : '');
            if (strpos($type, 'PRESUNT') !== false) {
                $this->mark($sheet, $target['pre']);
            } elseif (strpos($type, 'DEFINIT') !== false) {
                $this->mark($sheet, $target['def']);
            }
        }
    }

    private function fillProfessional($sheet, $doctor, $dateTime, array $cells)
    {
        $parts = $this->dateTimeParts($dateTime);
        $this->date($sheet, $cells['date'], $parts['date']);
        $this->time($sheet, $cells['time'], $parts['time']);
        $this->text($sheet, $cells['first_name'], $this->value($doctor, 'nombre1'));
        $this->text($sheet, $cells['last_name_1'], $this->value($doctor, 'apellido1'));
        $this->text($sheet, $cells['last_name_2'], $this->optionalName($this->value($doctor, 'apellido2')));
        $this->text($sheet, $cells['id'], $this->value($doctor, 'id'));
    }

    private function headerData($order, $patient, $insurance, $company, $clinicalHistoryNumber)
    {
        $age = $this->ageData($this->value($patient, 'fecha_nacimiento'), $this->value($order, 'fecha_orden'));
        $historyNumber = $this->plain($clinicalHistoryNumber);
        if ($historyNumber === '') {
            $historyNumber = $this->plain($this->value($patient, 'id'));
        }

        return array(
            'institution' => $this->value($insurance, 'nombre'),
            'facility_code' => $this->value($company, 'unicodigo'),
            'facility' => $this->value($company, 'nombrecomercial') ?: $this->value($company, 'razonsocial'),
            'history_number' => $historyNumber,
            'file_number' => $this->value($patient, 'id'),
            'last_name_1' => $this->value($patient, 'apellido1'),
            'last_name_2' => $this->optionalName($this->value($patient, 'apellido2')),
            'first_name_1' => $this->value($patient, 'nombre1'),
            'first_name_2' => $this->optionalName($this->value($patient, 'nombre2')),
            'sex' => $this->sex($this->value($patient, 'sexo')),
            'birth_date' => $this->value($patient, 'fecha_nacimiento'),
            'age_value' => $age['value'],
            'age_unit' => $age['unit'],
            'phone' => $this->value($patient, 'telefono1') ?: $this->value($patient, 'telefono2'),
            'city' => $this->value($patient, 'ciudad'),
            'address' => $this->value($patient, 'direccion'),
        );
    }

    private function clear053SampleData($sheet)
    {
        $cells = array(
            'A3', 'S3', 'Y3', 'AO3', 'AU3', 'BM3',
            'A6', 'R6', 'AG6', 'AS6', 'BF6', 'BI6', 'BP6', 'BT6', 'BV6', 'BX6', 'BZ6',
            'A8', 'AU7', 'BH7', 'BY12', 'A12', 'M12', 'X12', 'G17',
            'A26', 'A27', 'A28', 'A29',
            'B32', 'AE32', 'AJ32', 'AL32', 'B33', 'AE33', 'AJ33', 'AL33', 'B34', 'AE34', 'AJ34', 'AL34',
            'AO32', 'BT32', 'BX32', 'BZ32', 'AO33', 'BT33', 'BX33', 'BZ33', 'AO34', 'BT34', 'BX34', 'BZ34',
            'A38', 'K38', 'S38', 'AO38', 'BJ38', 'A40', 'S40', 'AT40',
            'AD43', 'AI43', 'BU43', 'BZ43', 'S45', 'BG45',
            'A49', 'S49', 'Y49', 'AO49', 'AU49', 'BM49', 'A53', 'S53', 'AM53', 'AV53',
            'A56', 'A57', 'A58', 'A59', 'A62', 'A63', 'A64', 'A65', 'A68', 'A69', 'A70', 'A71',
            'B74', 'AE74', 'AJ74', 'AL74', 'B75', 'AE75', 'AJ75', 'AL75', 'B76', 'AE76', 'AJ76', 'AL76',
            'AO74', 'BT74', 'BX74', 'BZ74', 'AO75', 'BT75', 'BX75', 'BZ75', 'AO76', 'BT76', 'BX76', 'BZ76',
            'A79', 'A80', 'A81', 'A82', 'A86', 'K86', 'S86', 'AO86', 'BJ86', 'A88', 'S88', 'AT88',
        );
        foreach ($cells as $cell) {
            $this->text($sheet, $cell, '');
        }
    }

    private function fillTextRows($sheet, array $cells, $value, $limit)
    {
        $lines = $this->wrap($this->plain($value), $limit, count($cells));
        foreach ($cells as $index => $cell) {
            $this->text($sheet, $cell, isset($lines[$index]) ? $lines[$index] : '');
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF000000');
        }
    }

    private function wrap($text, $limit, $maxLines)
    {
        if ($text === '') {
            return array();
        }
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines = array();
        $line = '';
        foreach ($words as $word) {
            $candidate = $line === '' ? $word : $line . ' ' . $word;
            if ($line !== '' && mb_strlen($candidate, 'UTF-8') > $limit) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }
        if (count($lines) > $maxLines) {
            $tail = implode(' ', array_slice($lines, $maxLines - 1));
            $lines = array_slice($lines, 0, $maxLines - 1);
            $lines[] = $tail;
        }
        return $lines;
    }

    private function text($sheet, $cell, $value)
    {
        if (!$cell) {
            return;
        }
        $sheet->setCellValueExplicit($cell, $this->plain($value), PHPExcel_Cell_DataType::TYPE_STRING);
    }

    private function date($sheet, $cell, $value)
    {
        $date = $this->parseDate($value);
        if (!$date) {
            $this->text($sheet, $cell, '');
            return;
        }
        $sheet->setCellValueExplicit(
            $cell,
            PHPExcel_Shared_Date::PHPToExcel($date->getTimestamp()),
            PHPExcel_Cell_DataType::TYPE_NUMERIC
        );
        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    }

    private function time($sheet, $cell, $value)
    {
        if ($value === '') {
            $this->text($sheet, $cell, '');
            return;
        }
        $date = DateTimeImmutable::createFromFormat('H:i', $value);
        if (!$date) {
            $this->text($sheet, $cell, $value);
            return;
        }
        $seconds = ((int) $date->format('H') * 3600) + ((int) $date->format('i') * 60);
        $sheet->setCellValueExplicit($cell, $seconds / 86400, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('hh:mm');
    }

    private function mark($sheet, $cell)
    {
        if ($cell) {
            $this->text($sheet, $cell, 'X');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
    }

    private function ageMarkerCell($unit, array $cells)
    {
        return isset($cells[$unit]) ? $cells[$unit] : $cells['A'];
    }

    private function ageData($birthDate, $referenceDate)
    {
        $birth = $this->parseDate($birthDate);
        $reference = $this->parseDate($referenceDate) ?: new DateTimeImmutable('today');
        if (!$birth || $birth > $reference) {
            return array('value' => '', 'unit' => 'A');
        }

        $interval = $birth->diff($reference);
        if ($interval->y > 0) {
            return array('value' => $interval->y, 'unit' => 'A');
        }
        if ($interval->m > 0) {
            return array('value' => $interval->m, 'unit' => 'M');
        }
        if ($interval->d > 0) {
            return array('value' => $interval->d, 'unit' => 'D');
        }
        return array('value' => 0, 'unit' => 'H');
    }

    private function dateTimeParts($value)
    {
        $date = $this->parseDate($value);
        if (!$date) {
            return array('date' => '', 'time' => '');
        }
        return array('date' => $date, 'time' => $date->format('H:i'));
    }

    private function parseDate($value)
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }
        $value = trim((string) $value);
        if ($value === '' || strpos($value, '0000-00-00') === 0) {
            return null;
        }
        try {
            return new DateTimeImmutable($value);
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function personName($person)
    {
        return trim(implode(' ', array_filter(array(
            $this->value($person, 'nombre1'),
            $this->optionalName($this->value($person, 'nombre2')),
            $this->value($person, 'apellido1'),
            $this->optionalName($this->value($person, 'apellido2')),
        ))));
    }

    private function optionalName($value)
    {
        $value = $this->plain($value);
        return in_array($this->upper($value), array('(N/A)', 'N/A', 'NA'), true) ? '' : $value;
    }

    private function sex($value)
    {
        $value = $this->upper($value);
        if (in_array($value, array('1', 'M', 'MASCULINO', 'HOMBRE'), true)) {
            return 'M';
        }
        if (in_array($value, array('2', 'F', 'FEMENINO', 'MUJER'), true)) {
            return 'F';
        }
        return $value;
    }

    private function truthy($value)
    {
        return in_array($this->upper($value), array('1', 'SI', 'SÍ', 'TRUE', 'X'), true);
    }

    private function upper($value)
    {
        return mb_strtoupper($this->plain($value), 'UTF-8');
    }

    private function plain($value)
    {
        return trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8'));
    }

    private function value($object, $key)
    {
        if (is_array($object)) {
            return isset($object[$key]) ? $object[$key] : '';
        }
        if (is_object($object) && isset($object->{$key})) {
            return $object->{$key};
        }
        return '';
    }
}
