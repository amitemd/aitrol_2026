<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_cpre_eco extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_cpre_eco';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function doctor_firma()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor1');
    }

    public function doctor_ayudante()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor2');
    }
}
 