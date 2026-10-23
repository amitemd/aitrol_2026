<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class grupo_procedimiento extends Model
{
    protected $table = 'grupo_procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
