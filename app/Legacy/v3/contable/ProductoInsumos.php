<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ProductoInsumos extends Model
{
    private static $modelo = 'productoInsumos';
    protected $table = 'ct_productos_insumos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function ct_producto(){
        return $this->belongsTo('Sis_medico\v3\contable\Productos', 'id_producto');
    }

    public function producto_compra(){
        return $this->belongsTo('Sis_medico\v3\contable\Producto', 'id_insumo');
    }
}
