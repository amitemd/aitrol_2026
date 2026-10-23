<?php

namespace Sis_medico\reprocesos;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Planilla;
use Sis_medico\InvInventario;
use Sis_medico\Movimiento;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Requests\Request;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvKardex;
use Sis_medico\Plan_Cuentas;

class InvInventarioReproceso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function reprocesarcostopromedio($id_empresa)
    {
        $inventario =  InvInventario::where('existencia', '>', 0)->where('id_empresa', $id_empresa)->join('producto', 'producto.id', '=', 'inv_inventario.id_producto')->whereNull('producto.kardex_uso')->get();
        foreach ($inventario as $key => $value) {

            $detalles = InvDetMovimientos::where('id_inv_inventario', $value->id)->get();
            $costo = 0;
            $existencia = 0;
            $total = 0;
            if ($key == 168) {
                dd($detalles);
            }

            foreach ($detalles as $detalle) {
                $tipo = $detalle->cabecera->documento_bodega->tipo_movimiento->tipo;
                $calculo = 0;
                if ($detalle->subtotal > 0) {
                    if ($detalle->subtotal != 0 && $detalle->cantidad != 0) {
                        $calculo = $detalle->subtotal / $detalle->cantidad;
                    }
                }
                if ($detalle->valor_unitario <> $calculo) {
                    $detalle->valor_unitario = $calculo;
                    $detalle->save();
                    $kardex = InvKardex::where('id_inv_det_movimientos', $detalle->id)->first();
                    if (!is_null($kardex)) {
                        $kardex->valor_unitario = $calculo;
                        $kardex->save();
                    }
                }


                if ($tipo == 'I' or $tipo == 'R' or $tipo == 'G') {
                    $existencia += $detalle->cantidad;
                    $total += $detalle->subtotal;
                } else {
                    $existencia -= $detalle->cantidad;
                    $total -= $detalle->subtotal;
                }
            }
            if ($total >= 1) {
                $costo = $total / $existencia;
            }
            $value->costo_promedio = $costo;
            $value->save();
            //return 'termino primero';
        }
        return "okay gracias amigo";
    }
}
