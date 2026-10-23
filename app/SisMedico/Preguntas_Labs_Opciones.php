<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Preguntas_Labs_Opciones extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preguntas_labs_opciones';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}