<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente_Medicamento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paciente_medicamento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}