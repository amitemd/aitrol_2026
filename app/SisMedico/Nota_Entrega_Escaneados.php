<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_Entrega_Escaneados extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nota_entrega_escaneados';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
}
 