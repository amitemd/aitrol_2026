<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class PreguntasOpciones extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preguntas_opciones';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}