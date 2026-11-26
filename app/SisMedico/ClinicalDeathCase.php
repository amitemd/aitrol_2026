<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ClinicalDeathCase extends Model
{
    protected $table = 'clinical_death_cases';

    protected $guarded = [];

    protected $casts = ['occurred_at' => 'datetime', 'pronounced_at' => 'datetime', 'family_notified_at' => 'datetime', 'morgue_notified_at' => 'datetime', 'completed_at' => 'datetime', 'identity_verified' => 'boolean', 'body_identified' => 'boolean'];

    public function events()
    {
        return $this->hasMany(ClinicalDeathEvent::class, 'clinical_death_case_id');
    }

    public function encounter()
    {
        return $this->belongsTo(ClinicalEncounter::class, 'encounter_id');
    }

    public function closure()
    {
        return $this->belongsTo(ClinicalEncounterClosure::class, 'closure_id');
    }
}
