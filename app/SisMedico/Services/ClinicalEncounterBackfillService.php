<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalEncounterBackfillService
{
    private $sourceMap = array(
        'agenda' => array('table' => 'agenda', 'key' => 'id'),
        'historiaclinica' => array('table' => 'historiaclinica', 'key' => 'hcid'),
        'ho_solicitud' => array('table' => 'ho_solicitud', 'key' => 'id'),
        'ho_form008' => array('table' => 'ho_form008', 'key' => 'id'),
        'ho_log_solicitud' => array('table' => 'ho_log_solicitud', 'key' => 'id'),
    );

    public function available()
    {
        if (!Schema::hasTable('clinical_encounters') || !Schema::hasTable('clinical_encounter_links')
            || !Schema::hasTable('clinical_encounter_events')) return false;
        foreach ($this->sourceMap as $source) {
            if (!Schema::hasTable($source['table']) || !Schema::hasColumn($source['table'], 'encounter_id')) return false;
        }
        return true;
    }

    public function run()
    {
        if (!$this->available()) return array('encounters' => 0, 'links' => 0);

        $stats = array('encounters' => 0, 'links' => 0);
        $this->restoreDirectReferences($stats);
        $this->backfillSolicitudes($stats);
        $this->backfillStandaloneHistories($stats);
        $this->propagateChildren($stats);
        return $stats;
    }

    private function restoreDirectReferences(array &$stats)
    {
        DB::table('clinical_encounter_links')->orderBy('id')->chunk(500, function ($links) use (&$stats) {
            foreach ($links as $link) {
                if (!isset($this->sourceMap[$link->source_type])) continue;
                $source = $this->sourceMap[$link->source_type];
                DB::table($source['table'])->where($source['key'], $link->source_id)
                    ->whereNull('encounter_id')->update(array('encounter_id' => $link->encounter_id));
            }
        });
    }

    private function backfillSolicitudes(array &$stats)
    {
        DB::table('ho_solicitud')->whereNull('encounter_id')->orderBy('id')->chunk(250, function ($records) use (&$stats) {
            foreach ($records as $record) {
                $encounterId = $this->encounterFromSource('agenda', $record->id_agenda);
                if (!$encounterId) {
                    $encounterId = $this->createEncounter(
                        $record->id_paciente,
                        $this->settingFromStep($record->estado_paso),
                        $this->stateFromStep($record->estado_paso),
                        $this->isOpenSolicitud($record),
                        isset($record->fecha_ingreso) ? $record->fecha_ingreso : null,
                        isset($record->fecha_salida) ? $record->fecha_salida : null,
                        $record,
                        $stats
                    );
                }

                $this->attach('ho_solicitud', $record->id, $encounterId, $stats);
                if (!empty($record->id_agenda)) $this->attach('agenda', $record->id_agenda, $encounterId, $stats);
                if (!empty($record->id_agenda)) {
                    $histories = DB::table('historiaclinica')->where('id_agenda', $record->id_agenda)->get();
                    foreach ($histories as $history) {
                        if (empty($history->encounter_id)) $this->attach('historiaclinica', $history->hcid, $encounterId, $stats);
                    }
                }
            }
        });
    }

    private function backfillStandaloneHistories(array &$stats)
    {
        DB::table('historiaclinica')->whereNull('encounter_id')->orderBy('hcid')->chunk(250, function ($histories) use (&$stats) {
            foreach ($histories as $history) {
                $encounterId = $this->encounterFromSource('agenda', $history->id_agenda);
                if (!$encounterId) {
                    $encounterId = $this->createEncounter(
                        $history->id_paciente,
                        ClinicalEncounterService::AMBULATORY,
                        ClinicalStateTransitionService::AMBULATORY_ACTIVE,
                        false,
                        isset($history->created_at) ? $history->created_at : null,
                        isset($history->updated_at) ? $history->updated_at : null,
                        $history,
                        $stats
                    );
                }
                $this->attach('historiaclinica', $history->hcid, $encounterId, $stats);
                if (!empty($history->id_agenda)) $this->attach('agenda', $history->id_agenda, $encounterId, $stats);
            }
        });
    }

    private function propagateChildren(array &$stats)
    {
        DB::table('ho_solicitud')->whereNotNull('encounter_id')->orderBy('id')->chunk(500, function ($records) use (&$stats) {
            foreach ($records as $record) {
                $forms = DB::table('ho_form008')->where('id_solicitud', $record->id)->whereNull('encounter_id')->get();
                foreach ($forms as $form) $this->attach('ho_form008', $form->id, $record->encounter_id, $stats);

                $logs = DB::table('ho_log_solicitud')->where('id_ho_solicitud', $record->id)->whereNull('encounter_id')->get();
                foreach ($logs as $log) $this->attach('ho_log_solicitud', $log->id, $record->encounter_id, $stats);
            }
        });
    }

    private function createEncounter($patientId, $setting, $state, $open, $startedAt, $endedAt, $source, array &$stats)
    {
        $id = $this->newId();
        $now = date('Y-m-d H:i:s');
        $data = array(
            'id' => $id,
            'id_paciente' => $patientId,
            'id_empresa' => isset($source->id_empresa) ? $source->id_empresa : null,
            'id_hospital' => null,
            'care_setting' => $setting,
            'status' => $open ? ClinicalEncounterService::ACTIVE : 'CLOSED',
            'started_at' => $startedAt ?: $now,
            'ended_at' => $open ? null : ($endedAt ?: $startedAt ?: $now),
            'close_reason' => $open ? null : 'LEGACY_BACKFILL',
            'id_usuariocrea' => isset($source->id_usuariocrea) ? $source->id_usuariocrea : null,
            'id_usuariomod' => isset($source->id_usuariomod) ? $source->id_usuariomod : null,
            'ip_creacion' => isset($source->ip_creacion) ? $source->ip_creacion : null,
            'ip_modificacion' => isset($source->ip_modificacion) ? $source->ip_modificacion : null,
            'created_at' => $now,
            'updated_at' => $now,
        );
        if (Schema::hasColumn('clinical_encounters', 'clinical_state')) $data['clinical_state'] = $state;
        DB::table('clinical_encounters')->insert($data);
        DB::table('clinical_encounter_events')->insert(array(
            'encounter_id' => $id,
            'event_type' => 'LEGACY_ENCOUNTER_BACKFILLED',
            'from_value' => null,
            'to_value' => $state,
            'reason' => 'Episodio canónico recuperado desde registros clínicos históricos.',
            'actor_id' => null,
            'ip_address' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        $stats['encounters']++;
        return $id;
    }

    private function attach($type, $sourceId, $encounterId, array &$stats)
    {
        if (empty($sourceId) || empty($encounterId) || !isset($this->sourceMap[$type])) return;
        $source = $this->sourceMap[$type];
        DB::table($source['table'])->where($source['key'], $sourceId)->update(array('encounter_id' => $encounterId));
        if (!DB::table('clinical_encounter_links')->where('source_type', $type)->where('source_id', (string) $sourceId)->exists()) {
            DB::table('clinical_encounter_links')->insert(array(
                'encounter_id' => $encounterId,
                'source_type' => $type,
                'source_id' => (string) $sourceId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
            $stats['links']++;
        }
    }

    private function encounterFromSource($type, $sourceId)
    {
        if (empty($sourceId) || !isset($this->sourceMap[$type])) return null;
        $source = $this->sourceMap[$type];
        $direct = DB::table($source['table'])->where($source['key'], $sourceId)->value('encounter_id');
        if ($direct) return $direct;
        return DB::table('clinical_encounter_links')->where('source_type', $type)
            ->where('source_id', (string) $sourceId)->value('encounter_id');
    }

    private function isOpenSolicitud($record)
    {
        $active = !isset($record->estado) || (string) $record->estado !== '0';
        $withoutExit = !isset($record->fecha_salida) || empty($record->fecha_salida);
        return $active && $withoutExit;
    }

    private function settingFromStep($step)
    {
        if ((int) $step === 0) return ClinicalEncounterService::AMBULATORY;
        if ((int) $step === 2) return ClinicalEncounterService::EMERGENCY;
        return ClinicalEncounterService::INPATIENT;
    }

    private function stateFromStep($step)
    {
        try {
            return app(ClinicalStateTransitionService::class)->stateForLegacyStep($step);
        } catch (\Exception $exception) {
            return ClinicalStateTransitionService::INPATIENT_ACTIVE;
        }
    }

    private function newId()
    {
        $hex = md5(uniqid((string) mt_rand(), true));
        return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4)
            . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20, 12);
    }
}
