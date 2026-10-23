<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Apps_Solicitudes extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps_solicitudes';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
