<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class PresentacionDetalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'presentacion_detalle';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}