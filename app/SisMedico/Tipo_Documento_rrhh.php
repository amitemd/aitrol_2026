<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo_documento_rrhh extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_documento_rrhh';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
