<?php

namespace Sis_medico\Services;

use Sis_medico\ClinicalEncounter;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Support\RequestSchema as Schema;

/**
 * Consolida las evoluciones del F-005 sin mezclar atenciones antiguas.
 *
 * Además de las historias enlazadas directamente al episodio, incluye las
 * evoluciones ambulatorias u hospitalarias creadas para el mismo paciente y
 * empresa mientras el ingreso estuvo abierto. Esto cubre los módulos legacy
 * que todavía crean una historia separada al registrar una visita.
 */
class HospitalEpisodeEvolutionSource
{
    public function forEpisode(Ho_Solicitud $solicitud, Historiaclinica $baseHistory, $companyId = null)
    {
        $encounter = $solicitud->encounter_id
            ? ClinicalEncounter::find($solicitud->encounter_id)
            : null;
        $encounterId = $solicitud->encounter_id ?: optional($encounter)->id;
        $startedAt = optional($encounter)->started_at ?: $solicitud->fecha_ingreso;
        $endedAt = optional($encounter)->ended_at ?: date('Y-m-d H:i:s');
        $companyId = $companyId ?: optional($encounter)->id_empresa;

        $query = Hc_Evolucion::query()
            ->join('historiaclinica as source_history', 'source_history.hcid', '=', 'hc_evolucion.hcid')
            ->leftJoin('agenda as source_agenda', 'source_agenda.id', '=', 'source_history.id_agenda')
            ->where('source_history.id_paciente', $solicitud->id_paciente)
            ->where(function ($scope) use ($baseHistory, $encounterId, $startedAt, $endedAt, $companyId) {
                $scope->where('source_history.hcid', $baseHistory->hcid);

                if ($encounterId && Schema::hasColumn('historiaclinica', 'encounter_id')) {
                    $scope->orWhere('source_history.encounter_id', $encounterId);
                }

                if ($startedAt && $companyId && Schema::hasColumns('agenda', ['id_empresa'])) {
                    $scope->orWhere(function ($duringAdmission) use ($startedAt, $endedAt, $companyId) {
                        $duringAdmission->where('source_agenda.id_empresa', $companyId)
                            ->where('hc_evolucion.created_at', '>=', $startedAt)
                            ->where('hc_evolucion.created_at', '<=', $endedAt);
                    });
                }
            })
            ->select('hc_evolucion.*', 'source_history.id_agenda as form005_agenda_id');

        if (Schema::hasColumn('historiaclinica', 'encounter_id')) {
            $query->addSelect('source_history.encounter_id as form005_encounter_id');
        }
        if (Schema::hasColumn('agenda', 'est_amb_hos')) {
            $query->addSelect('source_agenda.est_amb_hos as form005_est_amb_hos');
        }

        return $query->orderBy('hc_evolucion.created_at')
            ->orderBy('hc_evolucion.id')
            ->get()
            ->each(function ($evolution) use ($baseHistory) {
                $hospitalized = (string) $evolution->hcid === (string) $baseHistory->hcid
                    || (string) $evolution->form005_est_amb_hos === '1';
                $evolution->setAttribute(
                    'form005_origin',
                    $hospitalized ? 'HOSPITALIZACIÓN' : 'AMBULATORIO'
                );
            });
    }
}
