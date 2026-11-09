<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\ClinicalEncounterLink;
use Sis_medico\ClinicalEncounterOverride;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
use Sis_medico\Paciente;

class ClinicalEncounterService
{
    const AMBULATORY = 'AMBULATORY';
    const EMERGENCY = 'EMERGENCY';
    const INPATIENT = 'INPATIENT';
    const ACTIVE = 'ACTIVE';

    public function available()
    {
        return Schema::hasTable('clinical_encounters')
            && Schema::hasTable('clinical_encounter_links')
            && Schema::hasTable('clinical_encounter_events')
            && Schema::hasTable('clinical_encounter_overrides');
    }

    public function settingFromLegacyStep($step)
    {
        if ((int) $step === 0) return self::AMBULATORY;
        if ((int) $step === 2) return self::EMERGENCY;
        return self::INPATIENT;
    }

    /**
     * Opens or reuses an encounter under R-01. Emergency to inpatient is a
     * transition inside one episode. Ambulatory and acute care are independent
     * episodes and may remain active at the same time.
     */
    public function openOrReuse($patientId, $setting, array $context = array())
    {
        if (!$this->available()) return null;

        return DB::transaction(function () use ($patientId, $setting, $context) {
            Paciente::where('id', $patientId)->lockForUpdate()->first();

            $companyId = isset($context['id_empresa']) ? trim((string) $context['id_empresa']) : '';
            $active = ClinicalEncounter::where('id_paciente', $patientId)
                ->where('status', self::ACTIVE);

            // Un mismo paciente puede tener episodios en empresas distintas.
            // La compatibilidad y los conflictos R-01 se evalúan únicamente
            // dentro de la empresa que está abriendo la atención.
            if ($companyId !== '') {
                $active->where('id_empresa', $companyId);
            }

            $active = $active
                ->orderBy('started_at')
                ->lockForUpdate()
                ->get();

            $compatible = null;
            $conflict = null;
            foreach ($active as $encounter) {
                if ($this->compatible($encounter->care_setting, $setting)) {
                    if (is_null($compatible)) $compatible = $encounter;
                } elseif ($this->mayCoexist($encounter->care_setting, $setting)) {
                    continue;
                } elseif (is_null($conflict)) {
                    $conflict = $encounter;
                }
            }

            // If this request already belongs to a compatible active episode,
            // reuse it even when another previously authorized overlap exists.
            if (!is_null($compatible)) {
                if ($compatible->care_setting !== $setting) {
                    $previous = $compatible->care_setting;
                    $compatible->care_setting = $setting;
                    $compatible->id_usuariomod = $this->actorId($context);
                    $compatible->ip_modificacion = $this->ip($context);
                    $compatible->save();
                    $this->event($compatible, 'CARE_SETTING_CHANGED', $previous, $setting, 'Transición clínica compatible.', $context);
                }
                return $compatible;
            }

            if (!is_null($conflict) && !$this->overrideAuthorized($context)) {
                throw new ClinicalEncounterConflictException(
                    'El paciente ya tiene un episodio activo de ' . $this->settingLabel($conflict->care_setting)
                    . '. Para abrir otro episodio incompatible se requiere advertencia, motivo y autorización clínica.',
                    $conflict->id
                );
            }

            if (!is_null($conflict)) {
                $encounter = $this->createEncounter($patientId, $setting, $context);
                ClinicalEncounterOverride::create(array(
                    'encounter_id' => $encounter->id,
                    'conflicting_encounter_id' => $conflict->id,
                    'requested_setting' => $setting,
                    'reason' => trim((string) $context['override_reason']),
                    'authorized_by' => $this->actorId($context),
                    'ip_address' => $this->ip($context),
                ));
                $this->event($encounter, 'INCOMPATIBLE_EPISODE_AUTHORIZED', null, $setting, $context['override_reason'], $context);
                return $encounter;
            }

            return $this->createEncounter($patientId, $setting, $context);
        });
    }

    public function link($encounter, array $sources)
    {
        if (is_null($encounter) || !$this->available()) return;

        foreach ($sources as $type => $id) {
            if (is_null($id) || $id === '') continue;
            $link = ClinicalEncounterLink::firstOrCreate(array(
                'source_type' => (string) $type,
                'source_id' => (string) $id,
            ), array('encounter_id' => $encounter->id));
            $this->syncSourceEncounter($type, $id, $link->encounter_id);
        }
    }

    private function syncSourceEncounter($type, $id, $encounterId)
    {
        $sources = array(
            'agenda' => array('table' => 'agenda', 'key' => 'id'),
            'historiaclinica' => array('table' => 'historiaclinica', 'key' => 'hcid'),
            'ho_solicitud' => array('table' => 'ho_solicitud', 'key' => 'id'),
            'ho_form008' => array('table' => 'ho_form008', 'key' => 'id'),
            'ho_log_solicitud' => array('table' => 'ho_log_solicitud', 'key' => 'id'),
        );
        if (!isset($sources[$type])) return;
        $source = $sources[$type];
        if (!Schema::hasTable($source['table']) || !Schema::hasColumn($source['table'], 'encounter_id')) return;
        DB::table($source['table'])->where($source['key'], $id)->update(array('encounter_id' => $encounterId));
        if ($type === 'ho_solicitud') {
            $this->syncSolicitudChildren($id, $encounterId);
        }
    }

    private function syncSolicitudChildren($solicitudId, $encounterId)
    {
        $children = array(
            'ho_form008' => array('foreign' => 'id_solicitud', 'type' => 'ho_form008'),
            'ho_log_solicitud' => array('foreign' => 'id_ho_solicitud', 'type' => 'ho_log_solicitud'),
        );
        foreach ($children as $table => $config) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'encounter_id')) continue;
            $records = DB::table($table)->where($config['foreign'], $solicitudId)->get();
            foreach ($records as $record) {
                DB::table($table)->where('id', $record->id)->update(array('encounter_id' => $encounterId));
                ClinicalEncounterLink::firstOrCreate(array(
                    'source_type' => $config['type'],
                    'source_id' => (string) $record->id,
                ), array('encounter_id' => $encounterId));
            }
        }
    }

    public function findBySource($type, $id)
    {
        if (!$this->available()) return null;
        $sources = array(
            'agenda' => array('table' => 'agenda', 'key' => 'id'),
            'historiaclinica' => array('table' => 'historiaclinica', 'key' => 'hcid'),
            'ho_solicitud' => array('table' => 'ho_solicitud', 'key' => 'id'),
            'ho_form008' => array('table' => 'ho_form008', 'key' => 'id'),
            'ho_log_solicitud' => array('table' => 'ho_log_solicitud', 'key' => 'id'),
        );
        if (isset($sources[$type]) && Schema::hasTable($sources[$type]['table'])
            && Schema::hasColumn($sources[$type]['table'], 'encounter_id')) {
            $encounterId = DB::table($sources[$type]['table'])->where($sources[$type]['key'], $id)->value('encounter_id');
            if ($encounterId) return ClinicalEncounter::find($encounterId);
        }
        $link = ClinicalEncounterLink::where('source_type', (string) $type)
            ->where('source_id', (string) $id)->first();
        return $link ? ClinicalEncounter::find($link->encounter_id) : null;
    }

    public function integrity()
    {
        if(!$this->available())return array('available'=>false,'pending'=>null);
        $unauthorized=0;
        $patients=ClinicalEncounter::where('status',self::ACTIVE)->select('id_paciente','id_empresa')
            ->groupBy('id_paciente','id_empresa')->havingRaw('COUNT(*) > 1')->get();
        foreach($patients as $patient){
            $encounters=ClinicalEncounter::where('id_paciente',$patient->id_paciente)
                ->where('id_empresa',$patient->id_empresa)->where('status',self::ACTIVE)->get();
            for($i=0;$i<$encounters->count();$i++)for($j=$i+1;$j<$encounters->count();$j++){
                $left=$encounters[$i];$right=$encounters[$j];
                if($this->compatible($left->care_setting,$right->care_setting)
                    ||$this->mayCoexist($left->care_setting,$right->care_setting))continue;
                $authorized=ClinicalEncounterOverride::where(function($pair)use($left,$right){
                    $pair->where(function($query)use($left,$right){
                        $query->where('encounter_id',$left->id)->where('conflicting_encounter_id',$right->id);
                    })->orWhere(function($query)use($left,$right){
                        $query->where('encounter_id',$right->id)->where('conflicting_encounter_id',$left->id);
                    });
                })->whereNotNull('authorized_by')->whereRaw('CHAR_LENGTH(TRIM(reason)) >= 10')->exists();
                if(!$authorized)$unauthorized++;
            }
        }
        $invalidOverrides=ClinicalEncounterOverride::whereNull('authorized_by')->orWhereNull('reason')
            ->orWhereRaw('CHAR_LENGTH(TRIM(reason)) < 10')->count();
        return array('available'=>true,'unauthorized_conflicts'=>$unauthorized,'invalid_overrides'=>$invalidOverrides,
            'pending'=>$unauthorized+$invalidOverrides);
    }

    private function createEncounter($patientId, $setting, array $context)
    {
        $data = array(
            'id' => $this->newId(),
            'id_paciente' => $patientId,
            'id_empresa' => isset($context['id_empresa']) ? $context['id_empresa'] : null,
            'id_hospital' => isset($context['id_hospital']) ? $context['id_hospital'] : null,
            'care_setting' => $setting,
            'status' => self::ACTIVE,
            'started_at' => isset($context['started_at']) ? $context['started_at'] : date('Y-m-d H:i:s'),
            'id_usuariocrea' => $this->actorId($context),
            'id_usuariomod' => $this->actorId($context),
            'ip_creacion' => $this->ip($context),
            'ip_modificacion' => $this->ip($context),
        );
        if (Schema::hasColumn('clinical_encounters', 'clinical_state')) {
            $data['clinical_state'] = $this->initialState($setting, $context);
        }
        $encounter = ClinicalEncounter::create($data);
        $this->event($encounter, 'ENCOUNTER_OPENED', null, $setting, 'Apertura de episodio clínico.', $context);
        if (!empty($encounter->clinical_state)) {
            $this->event(
                $encounter,
                'CLINICAL_STATE_INITIALIZED',
                null,
                $encounter->clinical_state,
                'Estado clínico inicial asignado al abrir el episodio.',
                $context
            );
        }
        if (app(ClinicalRecordService::class)->available()) {
            app(ClinicalRecordService::class)->ensureRecord($patientId, $context);
        }
        return $encounter;
    }

    private function event($encounter, $type, $from, $to, $reason, array $context)
    {
        ClinicalEncounterEvent::create(array(
            'encounter_id' => $encounter->id,
            'event_type' => $type,
            'from_value' => $from,
            'to_value' => $to,
            'reason' => $reason,
            'actor_id' => $this->actorId($context),
            'ip_address' => $this->ip($context),
        ));
    }

    private function compatible($current, $requested)
    {
        if ($current === $requested) return true;
        $acute = array(self::EMERGENCY, self::INPATIENT);
        return in_array($current, $acute, true) && in_array($requested, $acute, true);
    }

    /**
     * La consulta ambulatoria no forma parte del flujo hospitalario. Puede
     * coexistir con Emergencia u Hospitalización sin autorización excepcional
     * y sin convertir un episodio en el otro.
     */
    private function mayCoexist($current, $requested)
    {
        $acute = array(self::EMERGENCY, self::INPATIENT);

        return ($current === self::AMBULATORY && in_array($requested, $acute, true))
            || ($requested === self::AMBULATORY && in_array($current, $acute, true));
    }

    private function initialState($setting, array $context)
    {
        if (isset($context['legacy_step'])) {
            return app(ClinicalStateTransitionService::class)->stateForLegacyStep($context['legacy_step']);
        }
        if ($setting === self::AMBULATORY) return ClinicalStateTransitionService::AMBULATORY_ACTIVE;
        if ($setting === self::EMERGENCY) return ClinicalStateTransitionService::EMERGENCY_ACTIVE;
        return ClinicalStateTransitionService::INPATIENT_ACTIVE;
    }

    private function overrideAuthorized(array $context)
    {
        $requested = isset($context['override']) && in_array((string) $context['override'], array('1', 'true', 'on'), true);
        $reason = isset($context['override_reason']) ? trim((string) $context['override_reason']) : '';
        $user = Auth::user();
        $authorizedRole = $user && $user->hasAnyUserTypeName(array('SISTEMAS', 'DOCTORES', 'DOCTOR'));
        return $requested && $authorizedRole && strlen($reason) >= 10;
    }

    private function actorId(array $context)
    {
        if (isset($context['actor_id'])) return $context['actor_id'];
        return Auth::check() ? Auth::user()->id : null;
    }

    private function ip(array $context)
    {
        if (isset($context['ip'])) return $context['ip'];
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    private function settingLabel($setting)
    {
        if ($setting === self::AMBULATORY) return 'atención ambulatoria';
        if ($setting === self::EMERGENCY) return 'emergencia';
        return 'hospitalización';
    }

    private function newId()
    {
        $hex = md5(uniqid((string) mt_rand(), true));
        return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4)
            . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20, 12);
    }
}
