<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class HorarioDoctorApp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'horario_doctor_app';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
