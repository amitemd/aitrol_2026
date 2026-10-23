<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Declare_;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Union_Bodega;
use Sis_medico\Venta_Kardex;

class InformeVariosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function costoVsVentas(Request $request)
    {
        return view("contable/informe_varios/costo_vs_venta_utilidad/index");
    }

    public function ventasUtilidad(Request $request)
    {
        $data = InformeVariosController::busquedaFact($request);
        return view("contable/informe_varios/reporte_producto_utilidad/index", ["datas" => $data, "request"=>$request->all()]);
    }


    public function busquedaFact(Request $request)
    {
        $data = [];
        $id_empresa = Session::get('id_empresa');
        $ventas = Ct_ventas::join("venta_kardex as vk", "vk.id_venta", "ct_ventas.id")
            ->where('id_empresa', $id_empresa);


        $fecha_desde = isset($request["fecha_desde"]) ? $request["fecha_desde"] : date("Y-m") . "-01";
        $ventas = $ventas->where('ct_ventas.fecha', '>=', $fecha_desde);

        $fecha_hasta = isset($request["fecha_hasta"]) ? $request["fecha_hasta"] : date("Y-m-d");

        $ventas = $ventas->where('ct_ventas.fecha', '<=', $fecha_hasta);

        if (isset($request["estado"]) and !is_null($request["estado"])) {
            $ventas = $ventas->where('ct_ventas.estado', $request["estado"]);
        }

        if(isset($request['id_cliente']) and !is_null($request['id_cliente'])){
            $ventas = $ventas->where('ct_ventas.id_cliente', $request['id_cliente']);
        }

        if (isset($request["producto"]) and !is_null($request["producto"])) {
            $prod_contable = Ct_productos::find($request["producto"]);
            $ventas = $ventas->join('ct_detalle_venta as cdv', 'cdv.id_ct_ventas', 'ct_ventas.id')->where('cdv.id_ct_productos', $prod_contable->codigo);
        }
        $ventas = $ventas->groupBy('vk.id_venta')->get(['ct_ventas.*']);

        foreach ($ventas as $venta) {
            $cab_array = [
                "nro_comprobante" => $venta->nro_comprobante,
                "id_cliente" => $venta->id_cliente, 
                "cliente" => $venta->cliente->nombre,
                "fecha" => $venta->fecha,
                "detalles" => []
            ];

            $venta_kardex = Venta_Kardex::where('id_venta', $venta->id)->groupBy('id_cab_moviento')->get();

            foreach ($venta->detalles as $det_venta) { // Recorremos todos los detalles de ventas
                $ct_producto = Ct_productos::where('codigo', $det_venta->id_ct_productos)->where('id_empresa', $id_empresa)->first();

                $insertado = false;
                $detalles_array = [
                    "cod_producto" => $det_venta->id_ct_productos,
                    "nombre_producto" => $det_venta->nombre,
                    "cantidad" => $det_venta->cantidad,
                    "precio_venta" => $det_venta->precio,
                    "total_venta" => $det_venta->extendido,
                    "costo" => 0,
                    "total_costo" => 0,
                    "utilidad" => 0,
                    "bodega_contable" => "",
                    "bodega_compra" => "",
                    "cantidad_mov" => 0,
                ];
                if (!is_null($ct_producto)) {
                    $producto_insumos = Ct_productos_insumos::where('id_producto', $ct_producto->id)->first(); // Verificamos el producto si esta vinculado
                    if (!is_null($producto_insumos)) {
                        $union_bodega = Union_Bodega::where('id_bodega_cont', $det_venta->bodega)->where('id_empresa', $id_empresa)->first();
                        if (!is_null($union_bodega)) {
                            foreach ($venta_kardex as $kardex) {
                                $cab_mov = InvCabMovimientos::find($kardex->id_cab_moviento); // Consultamos la cabecera del movimiento
                                if ($cab_mov->id_bodega_origen == $union_bodega->id_bodega_compra) {
                                    foreach ($cab_mov->detalles as $det_cab_mov) {
                                        $inv_serie = InvInventarioSerie::find($det_venta->id_inv_serie);
                                        if (!is_null($inv_serie)) {
                                            if ($inv_serie->serie == $det_cab_mov->serie) {
                                                if ($det_cab_mov->id_producto == $producto_insumos->id_insumo and $det_venta->cantidad == $det_cab_mov->cantidad) {
                                                    $detalles_array["cod_producto"] = $ct_producto->codigo;
                                                    $detalles_array["cantidad"] = $det_venta->cantidad;
                                                    $detalles_array["precio_venta"] = $det_venta->precio;
                                                    $detalles_array["total_venta"] = $det_venta->extendido;
                                                    $detalles_array["costo"] = $det_cab_mov->valor_unitario;
                                                    $detalles_array["total_costo"] = $det_cab_mov->total;
                                                    $detalles_array["bodega_contable"] = $det_venta->bodega;
                                                    $detalles_array["bodega_compra"] = $cab_mov->id_bodega_origen;
                                                    $detalles_array["cantidad_mov"] = $det_cab_mov->cantidad;
                                                    $detalles_array["utilidad"] = $detalles_array["total_venta"] - $detalles_array["total_costo"];
                                                    $cab_array["detalles"][] =  $detalles_array;
                                                    $insertado = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($venta_kardex as $kardex) {
                                $cab_mov = InvCabMovimientos::find($kardex->id_cab_moviento); // Consultamos la cabecera del movimiento
                                foreach ($cab_mov->detalles as $det_cab_mov) {
                                    if ($det_cab_mov->id_producto == $producto_insumos->id_insumo and $det_venta->cantidad == $det_cab_mov->cantidad) {
                                        $detalles_array["cod_producto"] = $ct_producto->codigo;
                                        $detalles_array["cantidad"] = $det_venta->cantidad;
                                        $detalles_array["precio_venta"] = $det_venta->precio;
                                        $detalles_array["total_venta"] = $det_venta->extendido;
                                        $detalles_array["costo"] = $det_cab_mov->valor_unitario;
                                        $detalles_array["total_costo"] = $det_cab_mov->total;
                                        $detalles_array["bodega_contable"] = $det_venta->bodega;
                                        $detalles_array["bodega_compra"] = $cab_mov->id_bodega_origen;
                                        $detalles_array["cantidad_mov"] = $det_cab_mov->cantidad;
                                        $detalles_array["utilidad"] = $detalles_array["total_venta"] - $detalles_array["total_costo"];
                                        $cab_array["detalles"][] =  $detalles_array;
                                        $insertado = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if (!$insertado) {
                    $cab_array["detalles"][] =  $detalles_array;
                }
            }


            array_push($data, $cab_array);
        }

        return $data;
    }

    public function masivoPrecio()
    {
        $ventas_kardex = Venta_Kardex::where('id', '<=', 194)->get();
        foreach ($ventas_kardex as $venta_kardex) {
            $inv_cab_mov = InvCabMovimientos::find($venta_kardex->id_cab_moviento);
            foreach ($inv_cab_mov->detalles as $det_mov) {
                $inv_inventario = InvInventario::find($det_mov->id_inv_inventario);
                $det_mov->valor_unitario = $inv_inventario->costo_promedio;
                $det_mov->total = $det_mov->cantidad * $inv_inventario->costo_promedio;
                $det_mov->subtotal = $det_mov->cantidad * $inv_inventario->costo_promedio;
                $det_mov->save();
            }
            InvCabMovimientos::calcularTotalCabMovimiento($venta_kardex->id_cab_moviento);
        }

        return ["status"=>"success", "message"=> "Ok gracias amigo"];
    }
}
