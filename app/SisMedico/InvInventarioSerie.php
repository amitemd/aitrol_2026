<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Session;
use Sis_medico\InvInventario;

class InvInventarioSerie extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use SoftDeletes;
    protected $table = 'inv_inventario_serie';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function inventario()
    {
        return $this->belongsTo('Sis_medico\InvInventario', 'id_inv_inventario', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'id_producto', 'id');
    }
    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega', 'id');
    }

    public function invcosto()
    {
        return $this->belongsTo('Sis_medico\InvCosto', 'id_producto', 'id_producto');
    }

    public static function movimientoInventarioSerie($id_cab_movimiento)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if(Auth::guest()){
            $idusuario  = 'FACELECTRO';
        }else{
            $idusuario  = Auth::user()->id;
        }

        $cabcera    = InvCabMovimientos::find($id_cab_movimiento);

        if(Auth::guest()){
            $id_empresa  = '0993075000001';
        }else{
            $id_empresa = Session::get('id_empresa');
        }
        foreach ($cabcera->detalles as $detalle) {
            if ($detalle->kardex == 0) {
                if (isset($cabcera->documento_bodega->tipo) and $cabcera->documento_bodega->tipo != '') {
                    $tipo = $cabcera->documento_bodega->tipo;
                } else {
                    $tipo = 'C';
                }
                $inventario = InvInventarioSerie::where('id_inv_inventario', $detalle->id_inv_inventario)
                    ->where('serie', $detalle->serie)
                    //->where('tipo', $tipo)
                    ->where('id_empresa', $id_empresa)
                    ->first();
                if (isset($inventario->id)) {
                    if ($cabcera->documento_bodega->tipo_movimiento->tipo == 'I' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'R' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'G' ) {
                        $inventario->existencia += $detalle->cantidad;
                        $inventario->existencia_uso += $detalle->cant_uso;
                    } else {
                        $movimiento = InvInventario::confirmarEgresoProducto($detalle->serie);
                        if ($detalle->producto->usos == 0 and $movimiento) {
                            $detalle->kardex = 1;
                            $detalle->save();
                            return;
                        }
                        $inventario->existencia_uso -= $detalle->cant_uso;
                        $inventario->existencia -= $detalle->cantidad;
                        // $existencia = 0;
                        // if ($detalle->producto->usos != 0) {
                        //     $exitencia = ceil($inventario->existencia_uso / $detalle->producto->usos);
                        // } else {
                        //     $exitencia = ceil($inventario->existencia_uso);
                        // }
                        // // $exitencia = ceil($inventario->existencia_uso / $detalle->producto->usos);
                        // $inventario->existencia = $exitencia;
                    }
                    if ($inventario->existencia < 0) {$inventario->existencia = 0;}
                    if ($inventario->existencia_uso < 0) {$inventario->existencia_uso = 0;}
                    $inventario->save();

                } else {
                    $inv_serie                    = new InvInventarioSerie;
                    $inv_serie->id_cab_movimientos= $cabcera->id;
                    $inv_serie->id_inv_inventario = $detalle->id_inv_inventario;
                    $inv_serie->serie             = $detalle->serie;
                    $inv_serie->id_producto       = $detalle->id_producto;
                    $inv_serie->id_bodega         = $detalle->inventario->id_bodega;
                    $inv_serie->lote              = $detalle->lote;
                    $inv_serie->fecha_vence       = $detalle->fecha_vence;
                    $inv_serie->existencia        = $detalle->cantidad;
                    $inv_serie->existencia_uso    = $detalle->cant_uso;
                    $inv_serie->tipo              = $tipo;
                    $inv_serie->id_empresa        = Auth::guest() ? '0993075000001' : Session::get('id_empresa');
                    $inv_serie->ip_creacion       = $ip_cliente;
                    $inv_serie->ip_modificacion   = $ip_cliente;
                    $inv_serie->id_usuariocrea    = $idusuario;
                    //// USOS KARDEX VICTOR
                    $inv_serie->cantidad_usos_kardex = $detalle->cantidad_usos_kardex;
                    $inv_serie->id_usuariomod     = $idusuario;
                    $inv_serie->updated_at        = date('Y-m-d H:i:s');
                    $inv_serie->save();
                }
                $detalle->kardex = 1;
                $detalle->save();
            }

        }

    }

    public static function incrementarInventarioSerie($serie, $id_bodega, $uso = 1)
    {
        $exitencia = 0;
        $inv_serie = InvInventarioSerie::where('serie', $serie)
            ->where('id_bodega', $id_bodega)
            ->first();
        if (isset($inv_serie->id)) {
            # RECALCULO INVENTARIO SERIE #
            $inventario = InvInventario::find($inv_serie->id_inv_inventario);
            $inv_serie->existencia_uso += $uso;
            $existencia = 0;
            if ($detalle->producto->usos != 0) {
                $exitencia = ceil($inv_serie->existencia_uso / $inventario->producto->usos);
            } else {
                $exitencia = ceil($inv_serie->existencia_uso);
            }
            // $exitencia  = ceil($inv_serie->existencia_uso / $inventario->producto->usos);
            $inv_serie->existencia = $exitencia;
            $inv_serie->save();
            # RECALCULO #
            $inv_existencia_uso = InvInventarioSerie::where('id_inv_inventario', $inventario->id)->sum('existencia_uso');
            if ($inv_existencia_uso >= 0) {
                $exitencia                  = ceil($inv_existencia_uso / $inventario->producto->usos);
                $inventario->existencia_uso = $inv_existencia_uso;
                $inventario->existencia     = $exitencia;
                $inventario->save();

            }

        }
    }

    public static function inventarioSerie($serie)
    {
        $existencia = 0;
        $inv_serie  = InvInventarioSerie::where('serie', $serie)
            ->get();
        if (!$inv_serie->isEmpty()) {
            $existencia = $inv_serie->sum('existencia');
        }
        return $existencia;
    }

    public static function comprometer($inv_serie, $cant)
    {   
        try{ 
            $comprometido_uso = 0;
            $producto         = $inv_serie->producto;
            $movimiento       = InvInventario::confirmarEgresoProducto($inv_serie->serie);
            if ($producto->usos == 0 and $movimiento) {
                return;
            }
            $inv_serie->comprometido_uso += $cant;
            $comprometido = 0;
            if ($producto->usos != 0) {
                $comprometido = ceil($inv_serie->comprometido_uso / $producto->usos);
            } else {
                $comprometido = ceil($inv_serie->comprometido_uso);
            }
            $inv_serie->comprometido = $comprometido;
            $inv_serie->save();
            # calculo el comprometido por inventario y actualizo en el inventario #
            $inventario  = $inv_serie->inventario;
            
            $inventarios = InvInventarioSerie::where('id_inv_inventario', $inv_serie->id_inv_inventario)
                ->where('estado', 1)
                ->get();
            if ($inventarios->first()) {
                foreach ($inventarios as $value) {
                    $comprometido_uso += $value->comprometido_uso;
                    // $comprometido               = ceil($comprometido_uso / $producto->usos);
                    if ($producto->usos != 0) {
                        $comprometido = ceil($comprometido_uso / $producto->usos);
                    } else {
                        $comprometido = ceil($comprometido_uso);
                    }
                }
                $inventario->save();
            }
            return ['status' => 'error', 'message' => 'Guardado con exito'];

        }catch(\Exception $e){ 
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
      

    }

}
