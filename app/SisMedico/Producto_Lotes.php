<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos;

class Producto_Lotes extends Model
{
    protected $table = 'producto_lotes';


     public function presentaciones()
    {
        return $this->belongsTo('Sis_medico\Presentaciones', 'presentaciones_id');
    }
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'producto_id');
    }
    
   
}
