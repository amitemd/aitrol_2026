<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Detalle_Eliminado extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_detalle_eliminado';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
