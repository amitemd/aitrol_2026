<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo_Procedimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_procedimiento';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
