<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Drogas_administradas extends Model
{
    protected $table = 'drogas_administradas';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}