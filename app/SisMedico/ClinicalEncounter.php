<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Services\ClinicalClosureGuard;
use Sis_medico\Services\ClinicalStateGuard;

class ClinicalEncounter extends Model
{
    protected $table = 'clinical_encounters';

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            ClinicalClosureGuard::assertEncounterMutation($model);
            ClinicalStateGuard::assertEncounterMutation($model);
        });
    }

    public function links()
    {
        return $this->hasMany(ClinicalEncounterLink::class, 'encounter_id');
    }

    public function events()
    {
        return $this->hasMany(ClinicalEncounterEvent::class, 'encounter_id');
    }
}
