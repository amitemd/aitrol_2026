<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TecnicasAnestesicas extends Model
{
    protected $table = 'tecnicas_anestesicas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
