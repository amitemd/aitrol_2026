<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class tecnica_procedimiento extends Model
{
    protected $table = 'tecnica_procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
