<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_entrega_detalle extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nota_entrega_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
 