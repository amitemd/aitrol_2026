<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Concentracion extends Model
{
    protected $table = 'concentracion';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
