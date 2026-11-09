<?php

namespace Sis_medico\Services;

use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Orden;
use Sis_medico\Seguro;
use Sis_medico\User;

class ClinicalLegacyOrder053ExportService
{
    public function export($id)
    {
        $data = $this->exportData($id);

        $workbook = app(ClinicalOfficialExcelWorkbookService::class)->build053(
            $data['order'],
            $data['patient'],
            $data['insurance'],
            $data['company'],
            $data['doctor'],
            $data['diagnoses'],
            $data['procedures'],
            $data['history_number']
        );

        return app(ClinicalOfficialExcelWorkbookService::class)->download($workbook, $data['file_name']);
    }

    public function exportPdf($id)
    {
        $data = $this->exportData($id);
        $view = view('pdf_hospital.formulario053_orden', $data)->render();
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHTML($view);

        return $pdf->stream($data['file_name'] . '.pdf');
    }

    private function exportData($id)
    {
        $order = Orden::with(array(
            'paciente',
            'doctor',
            'orden_tipo.orden_procedimiento.procedimiento',
        ))->findOrFail($id);

        $patient = $order->paciente;
        $evolution = $order->id_evolucion
            ? Hc_Evolucion::with('historiaclinica.agenda')->find($order->id_evolucion)
            : null;
        $history = $evolution ? $evolution->historiaclinica : null;
        $agenda = $history ? $history->agenda : null;
        $company = $agenda && $agenda->id_empresa ? Empresa::find($agenda->id_empresa) : null;
        $insuranceId = $history && $history->id_seguro ? $history->id_seguro : ($patient ? $patient->id_seguro : null);
        $insurance = $insuranceId ? Seguro::find($insuranceId) : null;
        $doctorId = $order->id_doctor_firma ?: $order->id_doctor;
        $doctor = $doctorId ? User::find($doctorId) : $order->doctor;

        $procedures = $order->orden_tipo
            ->flatMap(function ($type) {
                return $type->orden_procedimiento;
            })
            ->map(function ($detail) {
                return $detail->procedimiento ? $detail->procedimiento->nombre : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->implode("\n");

        $fileName = $patient
            ? $patient->apellido1 . '_' . $patient->nombre1 . '_FORMULARIO_053'
            : 'PACIENTE_FORMULARIO_053';

        return array(
            'order' => $order,
            'patient' => $patient,
            'insurance' => $insurance,
            'company' => $company,
            'doctor' => $doctor,
            'diagnoses' => $this->diagnoses($order->diagnosticos),
            'procedures' => $procedures,
            'history_number' => $history ? $history->hcid : '',
            'file_name' => $fileName,
        );
    }

    private function diagnoses($text)
    {
        $text = preg_replace('/<br\s*\/?\s*>/i', "\n", (string) $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
        $lines = preg_split('/[\r\n;]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $diagnoses = array();

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $code = '';
            if (preg_match('/\b([A-Z][0-9]{2}(?:\.?[0-9A-Z]{1,2})?)\b/i', $line, $match)) {
                $code = strtoupper(str_replace('.', '', $match[1]));
            }
            $type = '';
            if (stripos($line, 'presunt') !== false) {
                $type = 'PRESUNTIVO';
            } elseif (stripos($line, 'definit') !== false) {
                $type = 'DEFINITIVO';
            }
            $description = preg_replace('/\b(PRESUNTIVO|DEFINITIVO)\b/i', '', $line);
            if ($code !== '') {
                $description = preg_replace('/\b' . preg_quote($match[1], '/') . '\b/i', '', $description);
            }
            $description = trim($description, " :-\t\n\r\0\x0B");

            if ($description === '' && $code !== '') {
                $catalog = Cie_10_3::find($code);
                if (!$catalog) {
                    $catalog = Cie_10_4::find($code);
                }
                $description = $catalog ? $catalog->descripcion : '';
            }

            $diagnoses[] = array('code' => $code, 'description' => $description, 'type' => $type);
            if (count($diagnoses) === 6) {
                break;
            }
        }

        return $diagnoses;
    }
}
