<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Hospitalizacion extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_hospitalizacion';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}