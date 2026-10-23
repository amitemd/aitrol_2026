<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden_Valores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_valores';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


   
}
