<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Rc_indicaciones extends Model
{
    protected $table = 'rc_indicaciones';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}