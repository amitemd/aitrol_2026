<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Agrupador extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_agrupador';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
