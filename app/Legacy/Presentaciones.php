<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Presentaciones extends Model
{
    protected $table = 'presentaciones';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
