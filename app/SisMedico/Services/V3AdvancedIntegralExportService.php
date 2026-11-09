<?php

namespace Sis_medico\Services;

class V3AdvancedIntegralExportService
{
    public function download($rows)
    {
        $name = 'integrales-adelantados-'.date('Ymd-His').'.csv';
        return response()->stream(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, array('Fecha consulta', 'Cédula', 'Paciente', 'Seguro', 'Seguro por gestionar', 'Procedimiento solicitado', 'Fecha programada', 'Médico referente', 'Tipo tratamiento', 'Convenio', 'Responsable', 'Código'), ';');
            foreach ($rows as $row) {
                fputcsv($output, array(
                    $row->created_at, $row->id_paciente, $this->patient($row), $row->seguro_nombre,
                    $row->seguro_gestion_nombre, $this->procedure($row), $row->fechaini,
                    $this->doctor($row), ((string) $row->tipo_cita === '1' ? 'Subsecuente' : 'Primera vez'),
                    $row->empresa_corta ?: $row->empresa_nombre, trim($row->responsable_nombre1.' '.$row->responsable_apellido1), $row->id
                ), ';');
            }
            fclose($output);
        }, 200, array('Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$name.'"', 'Cache-Control' => 'no-store'));
    }

    private function patient($row)
    {
        return trim($row->paciente_apellido1.' '.$this->clean($row->paciente_apellido2).' '.$row->paciente_nombre1.' '.$this->clean($row->paciente_nombre2));
    }

    private function doctor($row)
    {
        return trim($row->doctor_apellido1.' '.$this->clean($row->doctor_apellido2).' '.$row->doctor_nombre1);
    }

    private function procedure($row)
    {
        if ((string) $row->proc_consul === '0') return ((string) $row->espid === '8' ? 'Consulta de cardiología' : 'Consulta');
        $names = array_filter(array($row->procedimiento_observacion ?: $row->procedimiento_nombre));
        foreach ($row->additional_procedures as $procedure) $names[] = $procedure->observacion ?: $procedure->nombre;
        return implode(' + ', array_unique($names));
    }

    private function clean($value)
    {
        return in_array($value, array('N/A', '(N/A)'), true) ? '' : $value;
    }
}
