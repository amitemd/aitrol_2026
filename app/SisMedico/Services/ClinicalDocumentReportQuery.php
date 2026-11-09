<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

abstract class ClinicalDocumentReportQuery
{
    protected function carga_documentos_rpt($insuranceId)
    {
        $general = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', null);
        $shared = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', '0')->where('documento.id_seguro', null);
        $specific = DB::table('documento')->where('documento.estado', '1')->where('tipo_seguro', '0')
            ->where('documento.id_seguro', $insuranceId)->where('documento.rpt_prin', '1');

        return $general->union($shared)->union($specific)->orderby('secuencia');
    }
}
