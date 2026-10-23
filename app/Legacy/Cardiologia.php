<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Cardiologia extends model
{
     //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_cardio';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function evolucion_orden()
    {
        return $this->belongsTo('Sis_medico\Hc_Evolucion', 'id_evo_orden');
    }
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_drsol');
    }

}
