<?php

namespace Sis_medico\v3\clinical;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Services\PatientTenantService;

class Patient extends Model
{
    protected $table = 'paciente';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = array();

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active_company', function ($query) {
            app(PatientTenantService::class)->constrainEloquent($query);
        });
    }
}
