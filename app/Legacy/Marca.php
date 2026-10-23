<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
     protected $table = 'marca';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
