<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_productos_procedimientos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_productos_procedimiento';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}