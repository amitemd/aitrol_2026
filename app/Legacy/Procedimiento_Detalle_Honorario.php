<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Procedimiento_Detalle_Honorario extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'procedimiento_detalle_honorario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}

