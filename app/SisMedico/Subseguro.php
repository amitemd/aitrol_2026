<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Subseguro extends Model
{
   protected $table = 'subseguro';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
