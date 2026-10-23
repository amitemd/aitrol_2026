<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class InvEstadoMovimientos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_estado_movimientos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = []; 

    
    
}