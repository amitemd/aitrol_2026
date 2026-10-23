<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ap_Procedimiento extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_procedimiento';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
