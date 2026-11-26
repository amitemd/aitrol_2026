<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ClinicalEncounterClosure extends Model
{
    protected $table = 'clinical_encounter_closures';

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(ClinicalEncounterClosureOrder::class, 'closure_id');
    }
}
