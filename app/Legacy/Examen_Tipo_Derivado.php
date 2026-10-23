<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Tipo_Derivado extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_tipo_derivado';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    
}