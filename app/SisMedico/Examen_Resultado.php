<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Resultado extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_resultado';
    

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden','id_orden');
    }

    public function parametro()
    {
        return $this->belongsTo('Sis_medico\Examen_Parametro','id_parametro');
    }
}
 