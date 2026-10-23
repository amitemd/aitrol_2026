<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_pedidos_Compra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_pedidos_compra';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Pedido', 'id_ct_compras_pedido');
    }
    public function proveedorf()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }


}
