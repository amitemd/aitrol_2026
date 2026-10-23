<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class HcEgreso extends Model
{
    protected $table = 'hc_egreso';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','id_hc_procedimientos');
    }

    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_egreso');
    }
}