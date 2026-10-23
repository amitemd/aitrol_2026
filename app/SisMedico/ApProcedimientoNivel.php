<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ApProcedimientoNivel extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_procedimiento_nivel';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
