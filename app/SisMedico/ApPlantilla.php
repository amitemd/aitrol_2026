<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ApPlantilla extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_plantilla';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
