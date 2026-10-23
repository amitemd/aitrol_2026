<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pregunta';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function grupopregunta()
    {
        return $this->belongsTo('Sis_medico\GrupoPregunta', 'id_grupopregunta');
    }
    
    public function pregunta_valor()
    {
        return $this->hasMany('Sis_medico\PreguntasValores', 'id_pregunta');
    }
}
