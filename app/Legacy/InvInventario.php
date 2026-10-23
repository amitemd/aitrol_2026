<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
// use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Producto;

class InvInventario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use SoftDeletes;
    protected $table = 'inv_inventario';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'id_producto', 'id');
    }
    public function det_mov()
    {
        return $this->belongsTo('Sis_medico\InvDetMovimientos', 'id', 'id_inv_inventario');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    public function invcosto()
    {
        return $this->belongsTo('Sis_medico\InvCosto', 'id_producto', 'id_producto');
    }

    public static function getInventario($id_producto = null, $id_bodega = null, $tipo = 'C')
    {
        $id_empresa = Session::get('id_empresa');
        $inventario = InvInventario::where('id_producto', $id_producto)
            ->where('id_bodega', $id_bodega)
           // ->where('tipo', $tipo)
            ->where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->first();
        if (isset($inventario->id)) {
            return $inventario;
        } else {
            $producto                   = Producto::find($id_producto);
            $inventario                 = new InvInventario;
            $inventario->id_bodega      = $id_bodega;
            $inventario->id_producto    = $id_producto;
            $inventario->tipo           = $tipo;
            $inventario->existencia     = 0;
            $inventario->existencia_uso = 0;
            $inventario->existencia_min = $producto->minimo;
            $inventario->existencia_max = 0;
            $inventario->comprometido   = 0;
            $inventario->existencia     = 0;
            $inventario->estado         = '1';
            $inventario->costo_promedio = 0;
            $inventario->id_empresa     = $id_empresa;
            $inventario->save();
            return $inventario;
        }
    }

    public static function setNeoInventario($id_producto = null, $id_bodega = null, $tipo = '', $cant = 0, $costo = 0)
    {

        $id_empresa = Session::get('id_empresa');
        $inventario = InvInventario::where('id_producto', $id_producto)
            ->where('id_bodega', $id_bodega)
            ->where('tipo', $tipo)
            ->where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->first();
        if (!isset($inventario->id) and $id_bodega != null) {
            $producto                   = Producto::find($id_producto);
            $inventario                 = new InvInventario;
            $inventario->id_bodega      = $id_bodega;
            $inventario->id_producto    = $id_producto;
            $inventario->tipo           = $tipo;
            $inventario->existencia     = $cant;
            $inventario->existencia_uso = 0;
            $inventario->existencia_min = $producto->minimo;
            $inventario->existencia_max = 0;
            $inventario->comprometido   = 0;
            $inventario->existencia     = $cant;
            $inventario->estado         = '1';
            $inventario->costo_promedio = $costo;
            $inventario->id_empresa     = $id_empresa;
            $inventario->save();
        }
        return $inventario;
    }

    public static function getInventarioSerie($id_inv_inventario = null, $serie = null)
    {
        $inventario = InvInventarioSerie::where('id_inv_inventario', $id_inv_inventario)
            ->where('serie', $serie)
            ->where('estado', 1)
            ->first();
        if (isset($inventario->id)) {
            return $inventario;
        } else {
            return '[]';
        }
    }

    public static function setNeoInventarioSerie($id_producto = null, $id_bodega = null, $cant = 0, $costo = 0, $tipo = 'C')
    {
        $inventario                 = new InvInventarioSerie;
        $inventario->id_bodega      = $id_bodega;
        $inventario->id_producto    = $id_producto;
        $inventario->existencia     = 0;
        $inventario->existencia_uso = 0;
        $inventario->existencia_min = 0;
        $inventario->existencia_max = 0;
        $inventario->comprometido   = 0;
        $inventario->estado         = '1';
        $inventario->costo_promedio = $costo;
        $inventario->tipo           = $tipo;
        $inventario->id_empresa     = Session::get('id_empresa');
        $inventario->save();
        return $inventario;
    }

    public static function movimientoInventario($id_cab_movimiento)
    {
        #
        $exitencia = 0;
        $cabcera   = InvCabMovimientos::find($id_cab_movimiento);
        foreach ($cabcera->detalles as $detalle) {
            if ($detalle->kardex == 0) {

                $inventario = InvInventario::find($detalle->id_inv_inventario);
                if (isset($inventario->id)) {
                    if ($cabcera->documento_bodega->tipo_movimiento->tipo == 'I' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'R' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'G' ) {
                        $cantidad_ant = $inventario->existencia;

                        $inventario->existencia += $detalle->cantidad;
                        $inventario->existencia_uso += $detalle->cant_uso;
                        # COSTO PROMEDIO X AHORA VALOR UNITARIO DEL PEDIDO
                        if ($inventario->costo_promedio != null and $inventario->costo_promedio != 0) {
                            $precio_ant                 = $inventario->costo_promedio;
                            $suma_ant                   = $cantidad_ant * $precio_ant;
                            $inventario->costo_promedio = ($suma_ant + $detalle->subtotal) / $inventario->existencia;
                            /*if ($key == 1) {
                        dd($precio_ant . ' -- ' . $suma_ant . ' -- ' . $inventario->existencia);
                        }*/
                        } else {
                            $inventario->costo_promedio = $detalle->valor_unitario;
                        }
                    } else {
                        $movimiento = InvInventario::confirmarEgresoProducto($detalle->serie);
                        // if ($detalle->producto->usos == 0 and $movimiento) {
                        //     return;
                        // }
                        # CALCULO LA EXISTENCIA DESCONTANDO TODOS LOS USOS\
                        $existencia = 0;
                        $existencia_2 = $inventario->existencia;
                        $inventario->existencia_uso =  $existencia_2 - $detalle->cantidad;
                        $inventario->existencia -= $detalle->cantidad;
                        # COSTO
                        // $inventario->costo_promedio = $detalle->valor_unitario;
                    }
                    if ($inventario->existencia < 0) {
                        $inventario->existencia = 0;
                    }
                    if ($inventario->existencia_uso < 0) {
                        $inventario->existencia_uso = 0;
                    }
                    $inventario->save();
                }
            }
        }
    }

    public static function confirmarEgresoProducto($serie)
    {
        # confirmar el egreso de producto reutilizable por serie
        # si el producto ya se desconto no se volvera a descontar del inventario
        $detalle = InvDetMovimientos::where('inv_det_movimientos.serie', $serie)
            ->where('inv_documentos_bodegas.id_inv_tipo_movimiento', '=', 2)
            ->where('inv_det_movimientos.estado', '=', 1)
            ->where('inv_cab_movimientos.estado', '=', 1)
            ->join('inv_cab_movimientos', 'inv_det_movimientos.id_inv_cab_movimientos', '=', 'inv_cab_movimientos.id')
            ->join('inv_documentos_bodegas', 'inv_cab_movimientos.id_documento_bodega', '=', 'inv_documentos_bodegas.id')
            ->first();
        if (isset($detalle->id)) {
            return true;
        }
        return false;
    }
}
