<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Orden_Venta_Detalle extends Model
{
    //
    protected $table = 'ct_orden_venta_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function orden()
    {
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'id_orden');

    }

    //Nuevo
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto');
    }
}
