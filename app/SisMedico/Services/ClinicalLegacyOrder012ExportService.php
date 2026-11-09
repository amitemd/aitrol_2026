<?php

namespace Sis_medico\Services;

use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;

class ClinicalLegacyOrder012ExportService
{
    public function export($id)
    {
        $order = Orden_012::findOrFail($id);
        $patient = Paciente::findOrFail($order->id_paciente);
        $evolution = $order->evolucion;
        $history = $evolution ? $evolution->historiaclinica : null;
        $agenda = $history ? $history->agenda : null;

        $companyId = $order->id_empresa ?: ($agenda ? $agenda->id_empresa : null);
        $insuranceId = $history && $history->id_seguro ? $history->id_seguro : $patient->id_seguro;
        $company = $companyId ? Empresa::find($companyId) : null;
        $insurance = $insuranceId ? Seguro::find($insuranceId) : null;
        $doctorId = $order->id_doctor_firma ?: $order->id_doctor_solicita;
        $doctor = $doctorId ? User::find($doctorId) : null;
        $diagnoses = $this->diagnoses($order->id);

        $workbook = app(ClinicalOfficialExcelWorkbookService::class)->build012(
            $order,
            $patient,
            $insurance,
            $company,
            $doctor,
            $diagnoses,
            $history ? $history->hcid : ''
        );

        return app(ClinicalOfficialExcelWorkbookService::class)->download(
            $workbook,
            $patient->apellido1 . '_' . $patient->nombre1 . '_FORMATO_012'
        );
    }

    private function diagnoses($orderId)
    {
        return Orden_012_Cie10::where('id_orden_012', $orderId)
            ->orderBy('id')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $catalog = Cie_10_3::find($row->cie10);
                if (!$catalog) {
                    $catalog = Cie_10_4::find($row->cie10);
                }

                return array(
                    'code' => $row->cie10,
                    'description' => $catalog ? $catalog->descripcion : '',
                    'type' => $row->presuntivo_definitivo,
                );
            })
            ->all();
    }
}
