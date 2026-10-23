<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Orden_Toma_Muestra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_orden_toma_muestra';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

    
}