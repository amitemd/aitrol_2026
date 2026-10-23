<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo_Seguro extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_seguro';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
