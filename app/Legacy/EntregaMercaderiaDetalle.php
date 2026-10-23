<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class EntregaMercaderiaDetalle extends Model
{
    protected $table = 'ct_entregas_mercaderia_detalle';
    protected $guarded = [];

    public function entrega()
    {
        return $this->belongsTo(EntregaMercaderia::class, 'id_entrega');
    }

    public function producto()
    {
        return $this->belongsTo(Ct_productos::class, 'id_producto');
    }

    public function bodega()
    {
        return $this->belongsTo(Ct_Bodegas::class, 'id_bodega_contable');
    }

    public function bodegaInventario()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega_inventario');
    }
}
