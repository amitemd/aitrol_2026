<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Triaje_Manchester extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_triaje_manchester';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function ho_prioridad()
    {
        return $this->belongsTo('Sis_medico\Ho_Prioridad_Emergencia','prioridad');
    }

    public function tipoemergencia()
    {
        return $this->belongsTo('Sis_medico\Ho_Tipo_Emergencia','tipo_emergencia');
    }

    public function hosolicitud()
    {
        return $this->belongsTo('Sis_medico\Ho_Solicitud','id_ho_solicitud');
    }


}
