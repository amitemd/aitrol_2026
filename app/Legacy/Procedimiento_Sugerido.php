<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Procedimiento_Sugerido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'procedimiento_sugerido';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
