<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Detalle_Formato_Descargo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detalle_formato_insumos';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
