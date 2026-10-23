<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Permisos_Tecnicos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permisos_tecnicos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
