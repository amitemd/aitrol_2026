<?php

namespace Sis_medico\reprocesos;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Movimiento;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Inv_Movimiento;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Http\Requests\Request;
use Sis_medico\Inv_Cab_Carga_Inicial;
use Sis_medico\Plan_Cuentas;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Venta_Kardex;

class FacturasServiciosProductos extends Model
{
    /*
    Facturas
    Ct_ventas - Ct_detalle_ventas

    Movimientos de kardex
    Venta_kardex  Relacion entre venta y movimiento

    Detalle de los movimientos
    inv_cab_movimientos - inv_detalle_movimientos

    */
    public static function detalleKardex($id)
    {
        $html = '<table><tr><td>No.</td></tr>';
        $ventas = Ct_ventas::where('id', $id)->first();
        if (count($ventas->detalles) > 0) {
            $cont = 0;
            foreach ($ventas->detalles as $det) {
                $msj = "";
                $venKardex = Venta_Kardex::where('id_venta', $id)->get();

                $ct_productos = Ct_productos::where('codigo', $det->id_ct_productos)->first();
                $ct_producto_insumos = Ct_productos_insumos::where('id_producto', $det->id_ct_productos)->first();
                if (is_null($ct_producto_insumos)) {
                    $msj = "No tiene insumos asociados";
                }
                foreach ($venKardex as $venK) {
                    $invdet = InvDetMovimientos::where('id_inv_cab_movimientos', $venK->id_cab_moviento)->where('cantidad', $det->cantidad)->first();
                    if (is_null($invdet)) {
                        $msj = "No se descargo";
                    } else {
                        $msj = $invdet->producto->codigo;
                    }
                }
                $cont++;
                $html .= '<tr>
                    <td style="border-bottom: 1px solid gray;">' . $cont . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $det->id_ct_productos . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $det->nombre . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $det->cantidad . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $det->precio . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $msj . '</td>

                    </tr>';
            }
        }
        // $venKardex = Venta_Kardex::join()->where('id_venta', $id)->get();
        // if (count($venKardex) > 0) {
        //     foreach ($venKardex as $venK) {
        //         $invMov = InvDetMovimientos::where('id_inv_cab_movimientos', $venK->id_cab_moviento)->get();
        //     }
        // }
        $html .= '</table>';
        return $html;
    }
    public static function detalleFactura($id)
    {
    }
    public static function reprocesoProductoServicios()
    {
        return Ct_ventas::where('id_empresa', session('id_empresa'))
            //->where('estado', 1)
            ->where('fecha', '>=', '2023-08-01')
            ->where('fecha', '<', '2023-08-31')
            ->get();
    }
}
