<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos;

class Producto extends Model
{
    //
    protected $table = 'producto';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function marca()
    {
        return $this->belongsTo('Sis_medico\Marca', 'id_marca');
    }
    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'id_proveedor');
    }
    public function tipo()
    {
        return $this->belongsTo('Sis_medico\Tipo', 'id_tipo');
    }
    public function pedidos()
    {
        return $this->belongsTo('Sis_medico\Pedido', 'id_pedido');
    }
    public function tipo_produc()
    {
        return $this->belongsTo('Sis_medico\Tipo', 'tipo_producto');
    }
    public function inv_costo()
    {
        return $this->belongsTo('Sis_medico\InvCosto', 'id', 'id_producto');
    }
    public function producto_contable()
    {

        $producto_insumo = Ct_productos_insumos::where('id_insumo', $this->id)->first();
        // return $producto_insumo;
        if (isset($producto_insumo->id)) {
            $producto_contable = Ct_productos::where('id', $producto_insumo->id_producto)->first();

            if (isset($producto_contable->id)) {
                return $producto_contable;
            } else {
                return '[]';
            }
        }
    }
    public static function buscarProductos($request)
    {
        $producto = [];
        // if (!is_null($request)) {
        //     $producto1 = Producto::where('codigo', 'LIKE', "%{$request}%")
        //     ->select('id as id','codigo','iva','precio_compra as precio','descuento',DB::raw('CONCAT(codigo, " | ", nombre) as text'));
        //     $producto = $producto1->take(20)->get();
        // }
        
        return $producto;
    }
    public function fx_tipo()
    {
        return $this->belongsTo('Sis_medico\Tipo', 'tipo_producto');
    }
}
