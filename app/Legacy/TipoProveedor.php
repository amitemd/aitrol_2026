<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TipoProveedor extends Model
{
     protected $table = 'tipoproveedor';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
