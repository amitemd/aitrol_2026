<?php

namespace Sis_medico\v3\contable;

use Exception;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InvInventarioSerie extends Model
{
    protected $table = 'inv_inventario_serie';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at_info';

    public function inventario()
    {
        return $this->belongsTo('Sis_medico\v3\contable\InvInventario', 'id_inv_inventario', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Producto', 'id_producto', 'id');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Bodega', 'id_bodega', 'id');
    }

    public function invcosto()
    {
        return $this->belongsTo('Sis_medico\v3\contable\InvCosto', 'id_producto', 'id_producto');
    }

    public static function movimientoInventarioSerie($id_cab_movimiento)
    {

        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $cabcera    = InvCabMovimientos::find($id_cab_movimiento);
            $id_empresa = session("id_empresa");
            foreach ($cabcera->detalles as $detalle) {
                if ($detalle->kardex == 0) {
                    if (isset($cabcera->documento_bodega->tipo) and $cabcera->documento_bodega->tipo != '') {
                        $tipo = $cabcera->documento_bodega->tipo;
                    } else {
                        $tipo = 'C';
                    }
                    $inventario = InvInventarioSerie::where('id_inv_inventario', $detalle->id_inv_inventario)
                        ->where('serie', $detalle->serie)
                        ->where('id_empresa', $id_empresa)
                        ->first();
                    if (isset($inventario->id)) {
                        if ($cabcera->documento_bodega->tipo_movimiento->tipo == 'I' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'R' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'G') {
                            $inventario->existencia += $detalle->cantidad;
                            $inventario->existencia_uso += $detalle->cant_uso;
                        } else {
                            $movimiento = InvInventario::confirmarEgresoProducto($detalle->serie);
                            if(isset($detalle->producto)){
                                if ($detalle->producto->usos == 0 and $movimiento) {
                                    $detalle->kardex = 1;
                                    $detalle->save();
                                    return;
                                }
                            }
                            
                            $inventario->existencia_uso -= $detalle->cant_uso;
                            $inventario->existencia -= $detalle->cantidad;
                        }
                        if ($inventario->existencia < 0) {
                            $inventario->existencia = 0;
                        }
                        if ($inventario->existencia_uso < 0) {
                            $inventario->existencia_uso = 0;
                        }
                        $inventario->save();
                    } else {
                        $inv_serie                    = new InvInventarioSerie;
                        $inv_serie->id_cab_movimientos = $cabcera->id;
                        $inv_serie->id_inv_inventario = $detalle->id_inv_inventario;
                        $inv_serie->serie             = $detalle->serie;
                        $inv_serie->id_producto       = $detalle->id_producto;
                        $inv_serie->id_bodega         = $detalle->inventario->id_bodega;
                        $inv_serie->lote              = $detalle->lote;
                        $inv_serie->fecha_vence       = $detalle->fecha_vence;
                        $inv_serie->existencia        = $detalle->cantidad;
                        $inv_serie->existencia_uso    = $detalle->cant_uso;
                        $inv_serie->tipo              = $tipo;
                        $inv_serie->id_empresa        = session("id_empresa");
                        $inv_serie->ip_creacion       = $ip_cliente;
                        $inv_serie->ip_modificacion   = $ip_cliente;
                        $inv_serie->id_usuariocrea    = $idusuario;
                        //$inv_serie->id_pedido         = $pedido->id;
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

            return ["status" => true, "msj" => "", "line" => ""];
        } catch (Exception $e) {
            return ["status" => false, "msj" => $e->getMessage(), "line" => $e->getLine()];
        }
    }
}
