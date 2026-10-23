<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Codigo_Derivacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'codigo_ derivacion';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}