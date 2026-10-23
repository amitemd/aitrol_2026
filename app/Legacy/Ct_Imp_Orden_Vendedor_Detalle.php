<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Imp_Orden_Vendedor_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_imp_orden_vendedor_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function producto(){
        return $this->belongsTo('Sis_medico\Ct_productos','id_producto');
    }

}
 