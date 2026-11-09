<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;

class ClinicalEmergencyDiagnosisService
{
    public function listForProcedure($procedureId, $type)
    {
        return DB::table('hc_cie10')->where('hc_id_procedimiento', $procedureId)
            ->where('ingreso_egreso', $type)->get()->map(function ($diagnosis) {
                $catalog = Cie_10_4::find($diagnosis->cie10);
                if (is_null($catalog)) $catalog = Cie_10_3::find($diagnosis->cie10);
                return array(
                    'cie10' => $diagnosis->cie10,
                    'id' => $diagnosis->id,
                    'descripcion' => is_null($catalog) ? 'Código sin descripción vigente' : $catalog->descripcion,
                    'pre_def' => $diagnosis->presuntivo_definitivo,
                    'ingreso_egreso' => $diagnosis->ingreso_egreso,
                );
            })->values()->all();
    }
}
