<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ap_Tipo_Examen extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_tipo_examen';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}