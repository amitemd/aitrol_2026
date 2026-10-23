<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Nivel extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_nivel';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
