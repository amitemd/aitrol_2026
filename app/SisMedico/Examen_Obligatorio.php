<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_obligatorio extends Model
{
    protected $table = 'examen_obligatorio_por_proc';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
	