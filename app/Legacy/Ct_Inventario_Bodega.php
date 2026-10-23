<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Inventario_Bodega extends Model
{
    //
    protected $table = 'ct_inventario_bodega';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_pedidos_Compra', 'id_ct_compras_pedido');
    }
    public function bodegap()
    {
        return $this->belongsTo('Sis_medico\Ct_Bodegas','bodega');
    }
    public function usuariomod()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }
}

