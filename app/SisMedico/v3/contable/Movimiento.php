<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    private static $modelo = 'movimiento';
    protected $table = 'movimiento';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function pedido()
    {
        return $this->hasOne('Sis_medico\v3\contable\Pedido','id','id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Producto','id_producto');
    }

    public function movimieto_inv()
    {
        return $this->belongsTo('Sis_medico\v3\contable\InvDetMovimientos','id','id_detalle_pedido');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Bodega','id_bodega');
    }
}
