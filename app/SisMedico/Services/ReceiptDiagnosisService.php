<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceiptDiagnosisService
{
    const MAX_LENGTH = 299;

    /**
     * Devuelve la copia editable guardada en el recibo o, para recibos
     * anteriores, la construye desde los diagnósticos de la consulta.
     */
    public function forReceipt($receipt)
    {
        $stored = $this->normalize($receipt ? $receipt->getAttribute('diagnosticos_consulta') : '');
        if ($stored !== '') return $stored;

        // Compatibilidad con recibos creados durante la transición del campo.
        $legacy = $this->normalize($receipt ? $receipt->getAttribute('diagnosticos') : '');
        if ($legacy !== '') return $legacy;

        $agendaId = $receipt ? $receipt->getAttribute('id_agenda') : null;
        return $this->fromAgenda($agendaId);
    }

    public function fromAgenda($agendaId)
    {
        if (!$agendaId) return '';

        $rows = DB::table('historiaclinica as receipt_history')
            ->join('hc_cie10 as receipt_diagnosis', 'receipt_diagnosis.hcid', '=', 'receipt_history.hcid')
            ->leftJoin('cie_10_3 as receipt_cie3', 'receipt_cie3.id', '=', 'receipt_diagnosis.cie10')
            ->leftJoin('cie_10_4 as receipt_cie4', 'receipt_cie4.id', '=', 'receipt_diagnosis.cie10')
            ->where('receipt_history.id_agenda', $agendaId)
            ->select('receipt_diagnosis.id', 'receipt_diagnosis.cie10',
                'receipt_diagnosis.presuntivo_definitivo',
                DB::raw("COALESCE(receipt_cie4.descripcion, receipt_cie3.descripcion, '') as descripcion"))
            ->orderBy('receipt_diagnosis.id')->get();

        $lines = array();
        $seen = array();
        foreach ($rows as $row) {
            $code = trim((string) $row->cie10);
            if ($code === '') continue;
            $type = trim((string) $row->presuntivo_definitivo);
            $key = Str::upper($code.'|'.$type);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $line = $code;
            $description = trim((string) $row->descripcion);
            if ($description !== '') $line .= ' - '.$description;
            if ($type !== '') $line .= ' ('.Str::upper($type).')';
            $lines[] = $line;
        }

        return $this->normalize(implode("\n", $lines));
    }

    /**
     * Lee los diagnósticos desde el campo ct_ventas.nota_array.
     */
    public function fromAdditionalInformation($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : array();
        }
        if (!is_array($value)) return '';

        foreach ($value as $item) {
            if (!is_array($item)) continue;
            $name = isset($item['nombre']) ? trim((string) $item['nombre']) : '';
            if (!$this->isDiagnosisName($name)) continue;
            return $this->normalize(isset($item['valor']) ? $item['valor'] : '');
        }

        return '';
    }

    private function isDiagnosisName($value)
    {
        $value = Str::upper(trim((string) $value));
        $value = str_replace(array('Á', 'É', 'Í', 'Ó', 'Ú'), array('A', 'E', 'I', 'O', 'U'), $value);
        return $value === 'DIAGNOSTICO' || $value === 'DIAGNOSTICOS';
    }

    public function normalize($value)
    {
        $value = preg_replace('/\r\n?|\n/', "\n", trim((string) $value));
        $value = preg_replace('/[ \t]+/', ' ', $value);
        $value = preg_replace('/\n{3,}/', "\n\n", $value);
        return Str::limit(trim($value), self::MAX_LENGTH, '');
    }
}
