<?php

namespace Sis_medico\Services;

use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;

class HospitalPostanesthesiaGuard
{
    private static $depth = 0;

    public static function run($callback)
    {
        ++self::$depth;
        try { return $callback(); }
        finally { --self::$depth; }
    }

    public static function assertMutation($record)
    {
        if (self::$depth > 0) return;
        $histories = [$record->id_hc, $record->getOriginal('id_hc')];
        foreach (array_unique(array_filter([$record->hc_id_procedimiento, $record->getOriginal('hc_id_procedimiento')])) as $procedureId) {
            $procedure = \Sis_medico\hc_procedimientos::find($procedureId);
            if ($procedure) $histories[] = $procedure->id_hc;
        }
        $histories = array_unique(array_filter($histories));
        foreach ($histories as $historyId) {
            $history = Historiaclinica::find($historyId);
            if (!$history) continue;
            $hospital = Ho_Solicitud::where('estado_paso', '>', 0)->where(function ($query) use ($history) {
                $query->where('id_agenda', $history->id_agenda ?: -1);
                if ($history->encounter_id) $query->orWhere('encounter_id', $history->encounter_id);
            })->exists();
            if ($hospital) {
                throw new \InvalidArgumentException('El postanestesico hospitalario se registra desde Documentos del episodio y por procedimiento.');
            }
        }
    }
}
