<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Autorizaciones_Pendientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_autorizaciones_pendientes';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}