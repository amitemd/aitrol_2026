<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'tipo';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
