<?php

namespace Sis_medico\reprocesos;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_compras;
use Sis_medico\Pedido;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\InvDocumentosBodegas;

class Compras extends Model
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

    public static function reprocesarcompras()
    {

        $pedidos  = Pedido::where('created_at', '>', '2023-08-08 00:00:00')->where('estado_contable', 1)->get();

        //1 es consigna
        //3 es factura
        foreach ($pedidos as $pedido) {

            $documento = InvDocumentosBodegas::where('id', $pedido->tipo)->where('abreviatura_documento', 'ICG')->first();
            if (!is_null($documento)) {

                $factura = Ct_compras::where('pedido', $pedido->pedido)->first();
                $id_plan = LogConfig::busqueda('1.01.03.01.02');
                $cuenta_prod_term = Plan_Cuentas::find($id_plan);
                $asientos = Ct_Asientos_Detalle::where('id_asiento_cabecera', $factura->id_asiento_cabecera)->where('id_plan_cuenta', $cuenta_prod_term->id)->get();
                if ($asientos->count() > 0) {
                    $id_plan_confg = LogConfig::busqueda('2.01.03.01.03');
                    $cuenta_prod_term2 = Plan_Cuentas::find($id_plan_confg);
                    foreach ($asientos as $asiento) {
                        $asiento->id_plan_cuenta = $cuenta_prod_term2->id;
                        $asiento->descripcion = $cuenta_prod_term2->descripcion;
                        $asiento->ip_modificacion = "REPROCESO COMPRAS";
                        $asiento->save();
                    }
                }
            } elseif ($pedido->tipo == 3) {
                $factura = Ct_compras::where('pedido', $pedido->pedido)->first();
                if (!is_null($factura->id_asiento_cabecera)) {

                    $id_plan = LogConfig::busqueda('2.01.03.01.03');
                    $cuenta_prod_term = Plan_Cuentas::find($id_plan);
                    $asientos = Ct_Asientos_Detalle::where('id_asiento_cabecera', $factura->id_asiento_cabecera)->where('id_plan_cuenta', $cuenta_prod_term->id)->get();
                    if ($asientos->count() > 0) {
                        $id_plan_confg = LogConfig::busqueda('1.01.03.01.02');
                        $cuenta_prod_term2 = Plan_Cuentas::find($id_plan_confg);
                        foreach ($asientos as $asiento) {
                            $asiento->id_plan_cuenta = $cuenta_prod_term2->id;
                            $asiento->descripcion = $cuenta_prod_term2->descripcion;
                            $asiento->ip_modificacion = "REPROCESO COMPRAS";
                            $asiento->save();
                        }
                    }
                }
            } else {
                dd("no debio ingresar");
            }
        }

        return "okay gracias amigo";
    }
}
