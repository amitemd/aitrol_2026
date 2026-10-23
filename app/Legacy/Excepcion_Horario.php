<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Excepcion_Horario extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'excepcion_horario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}	