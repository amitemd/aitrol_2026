<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Detalle extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function examen()
    {
        return $this->belongsTo('Sis_medico\Examen','id_examen');
    }
    public function examen_orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden','id_examen_orden');
    }

    public function agrupador()
    {
        return $this->examen->agrupador; 
    }

    public function parametros()
    {
        return $this->hasMany('Sis_medico\Examen_Parametro','id_examen','id_examen');
    }

    



}
