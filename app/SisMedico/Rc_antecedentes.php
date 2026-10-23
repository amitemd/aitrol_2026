<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Rc_antecedentes extends Model
{
    protected $table = 'rc_antecedentes';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}