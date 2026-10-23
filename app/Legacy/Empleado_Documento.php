<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Empleado_documento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empleado_documento';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
