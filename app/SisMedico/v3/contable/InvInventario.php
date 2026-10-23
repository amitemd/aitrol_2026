<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvInventario extends Model
{
    protected $table = 'inv_inventario';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';


    public static function confirmarEgresoProducto($serie)
    {
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
    public static function movimientoInventario($id_cab_movimiento)
    {
        try {
            $cabcera   = InvCabMovimientos::find($id_cab_movimiento);
            foreach ($cabcera->detalles as $detalle) {
                if ($detalle->kardex == 0) {
                    $inventario = InvInventario::find($detalle->id_inv_inventario);
                    if (isset($inventario->id)) {
                        if ($cabcera->documento_bodega->tipo_movimiento->tipo == 'I' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'R' || $cabcera->documento_bodega->tipo_movimiento->tipo == 'G') {
                            $cantidad_ant = $inventario->existencia;
                            $inventario->existencia += $detalle->cantidad;
                            $inventario->existencia_uso += $detalle->cant_uso;
                            if ($inventario->costo_promedio != null and $inventario->costo_promedio != 0) {
                                $precio_ant                 = $inventario->costo_promedio;
                                $suma_ant                   = $cantidad_ant * $precio_ant;
                                $inventario->costo_promedio = ($suma_ant + $detalle->subtotal) / $inventario->existencia;
                            } else {
                                $inventario->costo_promedio = $detalle->valor_unitario;
                            }
                        } else {
                            $movimiento = InvInventario::confirmarEgresoProducto($detalle->serie);
                            $existencia = 0;
                            $existencia_2 = $inventario->existencia;
                            $inventario->existencia_uso =  $existencia_2 - $detalle->cantidad;
                            $inventario->existencia -= $detalle->cantidad;
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
            return ["status" => true, "msj" => "", "exp" => ""];
        } catch (Exception $r) {
            return ["status" => false, "msj" => "Ocurrio un error", "exp" => $r->getMessage()];
        }
    }
}
