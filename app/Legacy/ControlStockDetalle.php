<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ControlStockDetalle extends Model
{

    protected $table = 'control_stock_detalle';



    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\ControlStockCabecera', 'cabecera_id');
    }
     public function producto_lote()
    {
        return $this->belongsTo('Sis_medico\Producto_Lotes', 'producto_lote_id');
    }

    
}
