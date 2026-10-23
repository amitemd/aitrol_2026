<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class SegurosPaciente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seguros_paciente';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
}
