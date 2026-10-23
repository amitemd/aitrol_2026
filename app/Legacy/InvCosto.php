<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Producto;
use Session;

class InvCosto extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_costo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public static function movimientoCostoInventario($id_cab_movimiento)
    {
        #
        $exitencia = 0;
        $cabcera            = InvCabMovimientos::find($id_cab_movimiento);
        foreach ($cabcera->detalles as $detalle) {
            if ($detalle->kardex == 0) {
                $inv_costo  = InvCosto::where('id_producto', $detalle->id_producto)
                    ->where('estado', 1)
                    ->first();
                if (isset($inv_costo->id)) {
                    $inv_costo->costo_anterior = $inv_costo->costo_promedio;
                    $inv_costo->costo_promedio = ($inv_costo->costo_promedio + $detalle->valor_unitario) / 2;
                    $inv_costo->save();
                } else {
                    $inv_costo                  = new InvCosto;
                    $inv_costo->id_producto     = $detalle->id_producto;
                    $inv_costo->costo_promedio  = $detalle->valor_unitario;
                    $inv_costo->costo_anterior  = $detalle->valor_unitario;
                    $inv_costo->id_empresa      = Session::get('id_empresa');
                    $inv_costo->save();
                }
            }
        }
    }
}
