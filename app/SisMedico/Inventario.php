<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Inv_Movimiento;
use Sis_medico\Ct_Usuario_Proceso;
use Sis_medico\InvDetMovimientos;

class Inventario
{
    /* 1: Saldo Inicial
    2: Ingreso
    3: Egreso */
    public static function build_process($type = "", $id = "", $enterprise, $asiento_t = 0, $importaciones = 0)
    {

        $empresa = Empresa::find($enterprise);
        if ($type == "C") {
         
            if ($empresa->inventario == 1) {

                $compras = Inventario::compras($id, $enterprise, $asiento_t, $import = $importaciones);
                // dd($compras);
                return $compras;
            }

        } else {
            if ($empresa->inventario == 1) {
                $ventas = Inventario::ventas($id, $enterprise);
                return $ventas;
            }
        }
        return 'error';
    }
    public static function compras($id = "", $enterprise, $asiento_t = 0, $importaciones = 0)
    {
        if ($id != "") {

            $compra        = Ct_compras::find($id);
            $id_movimiento = Ct_Inv_Movimiento::insertGetId([
                'id_transaccion' => '2',
                'id_referencia'  => $compra->id,
                'pedido'         => $compra->pedido,
                'tipo'           => 'COMP',
            ]);
            $data['mov']  = $id_movimiento;
            $groupProduct = Contable::groupBy($compra->detalles->toArray(), 'codigo');
            $parte        = "";
           
    
            foreach ($groupProduct as $key => $value) {
                $producto = Ct_productos::where('id_empresa', $enterprise)->where('codigo', $key)->first();
               
                if (!is_null($producto)) {
                    $exist = Inventario::verifyexist($producto->id, $enterprise);
                   // dd($exist);
                    if ($exist == 'N') {
                        $id_interno = Ct_Inv_Interno::insertGetId([
                            'id_producto' => $producto->id,
                            'stock'       => 1,
                            'cantidad'    => 1,
                            'costo'       => 0,
                            'id_empresa'  => $enterprise,
                        ]);
                        $cantidad = 0;
                        $precio   = 0;
                        foreach ($value as $x) {
                        //    dd($x,$data, $producto);
                            Ct_Inv_Kardex::create([
                                'id_bodega'      => $x['bodega'],
                                'fecha'          => $compra->fecha,
                                'id_transaccion' => '2', //ingreso
                                'id_movimiento'  => $data['mov'],
                                'precio'         => $x['precio'],
                                'id_producto'    => $producto->id,
                                'id_inv'         => $id_interno,
                                'cantidad'       => $x['cantidad'],
                            ]);
                            $cantidad += $x['cantidad'];
                            $precio += $x['precio'];
                        }
                        $inv           = Ct_Inv_Interno::find($id_interno);
                        $inv->stock    = $cantidad;
                        $inv->cantidad = $cantidad;
                        $inv->costo    = $precio;
                        $inv->save();
                        $id_costos = Ct_Inv_Costos::insertGetId([
                            'costo_promedio' => $precio,
                            'costo_anterior' => $precio,
                            'id_empresa'     => $enterprise,
                            'id_producto'    => $producto->id,
                        ]);
                        Ct_Inv_Costos_Detalle::create([
                            'id_costo'      => $id_costos,
                            'fecha'         => date('Y-m-d H:i:s'),
                            'estado'        => '1',
                            'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                            'costo'         => $precio,
                            'costo_ant'     => $precio,
                        ]);
                    } else {
                        
                        $id_interno = $exist;
                        $cantidad   = 0;
                        $precio     = 0;
                        foreach ($value as $x) {
                            Ct_Inv_Kardex::create([
                                'id_bodega'      => $x['bodega'],
                                'id_transaccion' => '2', //ingreso
                                'id_movimiento'  => $data['mov'],
                                'id_producto'    => $producto->id,
                                'precio'         => $x['precio'],
                                'id_inv'         => $id_interno,
                                'cantidad'       => $x['cantidad'],
                            ]);

                            $cantidad += $x['cantidad'];
                            $precio += $x['precio'];

                        }
                        //dd($value,$data);
                        $interno = Ct_Inv_Interno::find($id_interno);
                        

                        $interno->stock    = $interno->stock + $cantidad;
                        $interno->cantidad = $interno->cantidad + $cantidad;
                        $interno->costo    = ($interno->costo + $precio) / 2;
                        $interno->save();
                        $costos = Ct_Inv_Costos::where('id_producto', $producto->id)->where('id_empresa', $enterprise)->first();
                      
                        if ($costos != null) {
                            $costos->costo_promedio = ($interno->costo + $precio) / 2;
                            $costos->save();
                            Ct_Inv_Costos_Detalle::create([
                                'id_costo'      => $costos->id,
                                'fecha'         => date('Y-m-d H:i:s'),
                                'estado'        => '1',
                                'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                                'costo'         => ($interno->costo + $precio) / 2,
                                'costo_ant'     => $precio,
                            ]);
                        }
                    }

                    //dd($asiento_t);
                    // return [$importaciones, "valor"];
                    if ($importaciones == 0) {
                        //$asiento = Inventario::asiento_compras($id, $producto->id, $asiento_t);
                    }

                    //dd($asiento);
                }
            }
        }

        return 'ok';
    }
    public static function verifyexist($id, $enterprise)
    {
        $message = "N";
        $mex     = Ct_Inv_Interno::where('id_producto', $id)->where('id_empresa', $enterprise)->first();
        if (!is_null($mex)) {
            $message = $mex->id;
        }
        return $message;
    }
    public static function ventas($id = "", $enterprise)
    {
        if ($id != "") {
            $id_movimiento = "";
            $ventas        = Ct_ventas::find($id);
            //dd($ventas);
            if (isset($ventas->ct_orden_venta)) {
                $id_movimiento = Ct_Inv_Movimiento::insertGetId([
                    'id_transaccion' => '2',
                    'id_referencia'  => $ventas->id,
                    'id_agenda'      => $ventas->ct_orden_venta->id_agenda,
                    'id_paciente'    => $ventas->id_paciente,
                    'tipo'           => 'VENTA',
                ]);
            } else {
                $id_movimiento = Ct_Inv_Movimiento::insertGetId([
                    'id_transaccion' => '2',
                    'id_referencia'  => $ventas->id,
                    'id_paciente'    => $ventas->id_paciente,
                    'tipo'           => 'VENTA',
                ]);
            }
            $groupProduct = Contable::groupBy($ventas->detalles->toArray(), 'id_ct_productos');
            $cona         = 0;
            foreach ($groupProduct as $key => $value) {
                $producto = Ct_productos::where('id_empresa', $enterprise)->where('codigo', $key)->first();
                if (!is_null($producto)) {
                    $cantidad = 0;
                    $precio   = 0;
                    if ($producto->ident_paquete != 1) {
                        $interno = Ct_Inv_Interno::where('id_producto', $producto->id)->where('id_empresa', $enterprise)->first();
                        if (!is_null($interno)) {
                            foreach ($value as $p) {
                                Ct_Inv_Kardex::create([
                                    'id_bodega'      => $p['bodega'],
                                    'id_transaccion' => '3', //Egreso
                                    'id_movimiento'  => $id_movimiento,
                                    'fecha'          => $ventas->fecha,
                                    'id_producto'    => $producto->id,
                                    'id_inv'         => $interno->id,
                                    'cantidad'       => $p['cantidad'],
                                    'precio'         => $p['precio'],
                                ]);
                                $cantidad += $p['cantidad'];
                                $precio += $p['precio'];
                            }
                            $costos = Ct_Inv_Costos::where('id_producto', $producto->id)->where('id_empresa', $enterprise)->first();
                            if ($costos != null) {
                                $cneto = $interno->stock - $cantidad;
                                if ($cneto < 0) {
                                    $cneto = 0;
                                }
                                $interno->stock       = $cneto;
                                $interno->cantidad    = $cneto;
                                $interno->costo_venta = $costos->costo_promedio;
                                $pr                   = $precio + $interno->costo;
                                $costo_ant            = $interno->costo;
                                $interno->costo_venta = $pr;
                                $interno->save();
                            } else {
                                $interno->stock       = $interno->stock - $cantidad;
                                $interno->cantidad    = $interno->cantidad - $cantidad;
                                $pr                   = $precio + $interno->costo;
                                $costo_ant            = $interno->costo;
                                $interno->costo_venta = $pr;
                                $interno->save();
                                $id_costos = Ct_Inv_Costos::insertGetId([
                                    'costo_promedio' => $precio,
                                    'costo_anterior' => $costo_ant,
                                    'id_empresa'     => $enterprise,
                                    'id_producto'    => $producto->id,
                                ]);
                                Ct_Inv_Costos_Detalle::create([
                                    'id_costo'      => $id_costos,
                                    'fecha'         => date('Y-m-d H:i:s'),
                                    'estado'        => '1',
                                    'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                                    'costo'         => $precio,
                                    'costo_ant'     => $precio,
                                ]);
                            }
                        }
                    }
                    if ($enterprise == '0992704152001') {
                        //RUC DE GASTROCLINICA - POR SI ACASO NO FUNCIONA EL PROCESO NUEVO DE CARLOS
                        if ($producto->ident_paquete == 1) {
                            $details = Ct_productos_paquete::where('id_producto', $producto->id)->get();
                            foreach ($details as $jk) {
                                $cantidad = 0;
                                $precio   = 0;
                                $interno  = Ct_Inv_Interno::where('id_producto', $jk->id_paquete)->where('id_empresa', $enterprise)->first();
                                if (!is_null($interno)) {
                                    foreach ($value as $p) {
                                        Ct_Inv_Kardex::create([
                                            'id_bodega'      => $p['bodega'],
                                            'id_transaccion' => '3', //Egreso
                                            'id_movimiento'  => $id_movimiento,
                                            'fecha'          => $ventas->fecha,
                                            'id_producto'    => $jk->id_paquete,
                                            'id_inv'         => $interno->id,
                                            'cantidad'       => $p['cantidad'],
                                            'precio'         => $p['precio'],
                                        ]);
                                        $cantidad += $p['cantidad'];
                                        $precio += $p['precio'];
                                    }
                                    $costos = Ct_Inv_Costos::where('id_producto', $jk->id_paquete)->where('id_empresa', $enterprise)->first();
                                    if ($costos != null) {
                                        $cneto = $interno->stock - $cantidad;
                                        if ($cneto < 0) {
                                            $cneto = 0;
                                        }
                                        $interno->stock       = $cneto;
                                        $interno->cantidad    = $cneto;
                                        $interno->costo_venta = $costos->costo_promedio;
                                        $pr                   = $precio + $interno->costo;
                                        $costo_ant            = $interno->costo;
                                        $interno->costo_venta = $pr;
                                        $interno->save();
                                    } else {
                                        $interno->stock       = $interno->stock - $cantidad;
                                        $interno->cantidad    = $interno->cantidad - $cantidad;
                                        $pr                   = $precio + $interno->costo;
                                        $costo_ant            = $interno->costo;
                                        $interno->costo_venta = $pr;
                                        $interno->save();
                                        $id_costos = Ct_Inv_Costos::insertGetId([
                                            'costo_promedio' => $precio,
                                            'costo_anterior' => $costo_ant,
                                            'id_empresa'     => $enterprise,
                                            'id_producto'    => $jk->id_paquete,
                                        ]);
                                        Ct_Inv_Costos_Detalle::create([
                                            'id_costo'      => $id_costos,
                                            'fecha'         => date('Y-m-d H:i:s'),
                                            'estado'        => '1',
                                            'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                                            'costo'         => $precio,
                                            'costo_ant'     => $precio,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                $asientos_ventas = Inventario::asiento_ventas($id, $producto->id, $cantidad);
            }
        } else {
            return 'error';
        }
        return 'ok';
    }
    public static function checkExist($id, $bodega, $cantidad, $enterprise)
    {
        $entp = Empresa::find($enterprise);
        if ($entp->inventario == 1) {
            $producto = Ct_productos::where('id_empresa', $enterprise)->where('estado_tabla', 1)->where('codigo', $id)->first();
            if ($producto != null) {
                $existencia = Ct_Inv_Interno::where('id_producto', $producto->id)->first();
                if ($existencia != null) {
                    return response()->json(['stock' => $existencia->stock]);
                }
            }
            return response()->json(['stock' => '0']);
        } else {
            return response()->json(['stock' => '999999']);
        }

        return response()->json(['stock' => 'no']);
    }
    public static function asiento_compras($id, $id_producto, $asiento_t = 0)
    {
        $id_compra        = Ct_compras::find($id);
        $asiento_cabecera = Ct_Asientos_Cabecera::find($id_compra->id_asiento_cabecera);
        $enterprise       = Empresa::find($id_compra->id_empresa);
        $coste            = Ct_Inv_Interno::where('id_empresa', $enterprise->id)->where('id_producto', $id_producto)->first();
        $configuraciones  = Ct_Globales::where('inventario', 'SI')->where('id_modulo', 1)->where('id_empresa', $enterprise->id)->first();
        $mensajes = "";
        DB::beginTransaction();
       try {
        if ($coste != null && $asiento_t == 0) {
            if (isset($asiento_cabecera->id)) {
                if ($configuraciones != null) {
                    //debe
                    $asiento_detalle                      = new Ct_Asientos_Detalle;
                    $asiento_detalle->id_plan_cuenta      = $configuraciones->debe;
                    $asiento_detalle->descripcion         = 'Asiento de Compras';
                    $asiento_detalle->id_asiento_cabecera = $asiento_cabecera->id;
                    $asiento_detalle->debe                = floatval($coste->costo);
                    $asiento_detalle->haber               = 0;
                    $asiento_detalle->id_usuariocrea      = $id_compra->id_usuariocrea;
                    $asiento_detalle->id_usuariomod       = $id_compra->id_usuariocrea;
                    $asiento_detalle->ip_creacion         = $id_compra->ip_creacion;
                    $asiento_detalle->ip_modificacion     = $id_compra->ip_creacion;
                    $asiento_detalle->save();

                    //haber
                    if(!is_null($configuraciones->haber)){
                        $asiento_detalle2                      = new Ct_Asientos_Detalle;
                        $asiento_detalle2->id_plan_cuenta      = $configuraciones->haber;
                        $asiento_detalle2->descripcion         = 'Asiento de Compras';
                        $asiento_detalle2->id_asiento_cabecera = $asiento_cabecera->id;
                        $asiento_detalle2->haber               = floatval($coste->costo);
                        $asiento_detalle2->debe                = 0;
                        $asiento_detalle2->id_usuariocrea      = $id_compra->id_usuariocrea;
                        $asiento_detalle2->id_usuariomod       = $id_compra->id_usuariocrea;
                        $asiento_detalle2->ip_creacion         = $id_compra->ip_creacion;
                        $asiento_detalle2->ip_modificacion     = $id_compra->ip_modificacion;
                        $asiento_detalle2->save();
                    }
                    
                    DB::commit();
                    return "ok";
                }
            }
        }
       }catch (\Exception $e){
        DB::rollback();
        return $e->getMessage();
       }

       //return($mensajes);
        
    }
    public static function asiento_ventas($id, $id_producto, $cantidad)
    {
        $id_venta         = Ct_ventas::find($id);
        $enterprise       = Empresa::find($id_venta->id_empresa);
        $asiento_cabecera = Ct_Asientos_Cabecera::find($id_venta->id_asiento);
        $coste            = Ct_Inv_Interno::where('id_empresa', $enterprise->id)->where('id_producto', $id_producto)->first();
        $configuraciones  = Ct_Globales::where('inventario', 'SI')->where('id_modulo', 3)->where('id_empresa', $enterprise->id)->first();
        if (isset($asiento_cabecera->id)) {
            if ($configuraciones != null) {
                //debe
                $asiento_detalle                      = new Ct_Asientos_Detalle;
                $asiento_detalle->id_plan_cuenta      = $configuraciones->debe;
                $asiento_detalle->id_asiento_cabecera = $asiento_cabecera->id;
                $asiento_detalle->descripcion         = 'Asiento de Ventas';
                $asiento_detalle->debe                = floatval($coste->costo * $cantidad);
                $asiento_detalle->haber               = 0;
                $asiento_detalle->id_usuariocrea      = $id_venta->id_usuariocrea;
                $asiento_detalle->id_usuariomod       = $id_venta->id_usuariocrea;
                $asiento_detalle->ip_creacion         = $id_venta->ip_creacion;
                $asiento_detalle->ip_modificacion     = $id_venta->ip_creacion;
                $asiento_detalle->save();
                //haber
                $asiento_detalle2                      = new Ct_Asientos_Detalle;
                $asiento_detalle2->id_plan_cuenta      = $configuraciones->haber;
                $asiento_detalle2->descripcion         = 'Asiento de Ventas';
                $asiento_detalle2->id_asiento_cabecera = $asiento_cabecera->id;
                $asiento_detalle2->haber               = floatval($coste->costo * $cantidad);
                $asiento_detalle2->debe                = 0;
                $asiento_detalle2->id_usuariocrea      = $id_venta->id_usuariocrea;
                $asiento_detalle2->id_usuariomod       = $id_venta->id_usuariocrea;
                $asiento_detalle2->ip_creacion         = $id_venta->ip_creacion;
                $asiento_detalle2->ip_modificacion     = $id_venta->ip_creacion;
                $asiento_detalle2->save();
            }
        }
        return 'ok';
    }
    public static function tipo_producto($id)
    {
        $producto = Ct_productos::where('id', $id)->first();
        $tipo     = null;
        if (!is_null($producto)) {
            if ($producto->grupo == 1) {
                $tipo = 'Insumos';
            } elseif ($producto->grupo == 2) {
                $tipo = 'Medicamentos';
            } elseif ($producto->grupo == 3) {
                $tipo = 'Servicios';
            } elseif ($producto->grupo == 4) {
                $tipo = 'Procedimientos';
            } elseif ($producto->grupo == 5) {
                $tipo = 'Otros';
            } elseif ($producto->grupo == 6) {
                $tipo = 'Honorario';
            } elseif ($producto->grupo == 7) {
                $tipo = 'Equipo';
            }
        }
        return $tipo;
    }
    public static function anular_compras($id)
    {
        $compras    = Ct_compras::find($id);
        $movimiento = Ct_Inv_Movimiento::where('id_referencia', $compras->id)->where('tipo', 'COMP')->first();
        if (!is_null($movimiento)) {
            $inventario    = Ct_Inv_Kardex::where('id_movimiento', $movimiento->id)->get();
            $id_inventario = 1;
            foreach ($inventario as $in) {
                $id_inventario = $in->id_inv;
                $in->estado    = 0;
                $in->save();
            }
            $recalcular = Inventario::recalcular_compras($id_inventario);
        }
    }
    public static function anular_ventas($id)
    {
        $ventas     = Ct_ventas::find($id);
        $movimiento = Ct_Inv_Movimiento::where('id_referencia', $ventas->id)->where('tipo')->where('tipo', 'VENTA')->first();
        if (!is_null($movimiento)) {
            $inventario    = Ct_Inv_Kardex::where('id_movimiento', $movimiento->id)->get();
            $id_inventario = 1;
            foreach ($inventario as $in) {
                $id_inventario = $in->id_inv;
                $in->estado    = 0;
                $in->save();
            }
            $recalcular = Inventario::recalcular_ventas($id_inventario);
        }
    }
    public static function recalcular_ventas($id)
    {
        $f           = Ct_Inv_Interno::find($id);
        $detalles    = Ct_Inv_Kardex::where('id_inv', $f->id)->where('estado', 1)->sum('cantidad');
        $costo       = Ct_Inv_Kardex::where('id_inv', $f->id)->where('estado', 1)->sum('precio');
        $f->cantidad = $detalles;
        $f->stock    = $detalles;
        $f->costo    = $costo;
        $f->save();
        return 'ok';
    }
    public static function recalcular_compras($id)
    {
        $f           = Ct_Inv_Interno::find($id);
        $detalles    = Ct_Inv_Kardex::where('id_inv', $f->id)->where('estado', 1)->sum('cantidad');
        $costo       = Ct_Inv_Kardex::where('id_inv', $f->id)->where('estado', 1)->sum('precio');
        $f->cantidad = $detalles;
        $f->stock    = $detalles;
        $f->costo    = $costo;
        $f->save();
        return 'ok';
    }
    public static function stock($id, $bodega, $empresa)
    {
        //ingreso por compras
        $ingreso = Ct_Inv_Kardex::where('id_producto', $id)->where('estado', 1)->where('id_transaccion', 2)->where('id_bodega', $bodega)->with(['inventario' => function ($query) use ($empresa) {
            $query->where('id_empresa', $empresa)->where('estado', 1);
        }])->sum('cantidad');
        //egreso por ventas
        $egreso = Ct_Inv_Kardex::where('id_producto', $id)->where('estado', 1)->where('id_transaccion', 3)->where('id_bodega', $bodega)->with(['inventario' => function ($query) use ($empresa) {
            $query->where('id_empresa', $empresa)->where('estado', 1);
        }])->sum('cantidad');
        if (is_null($ingreso)) {
            $ingreso = 0;
        }
        if (is_null($egreso)) {
            $egreso = 0;
        }
        $cantidad = $ingreso - $egreso;
        if ($cantidad < 0) {
            $cantidad = 0;
        }
        $enterprise = Empresa::find($empresa);
        if ($enterprise->inventario == 0) {
            $cantidad = 99999;
        }

        if ($enterprise->id == '0992704152001') {
            $producto = Ct_productos::where('id', $id)->first();
            //dd($producto,$id);
            if ($producto != null) {
                $ispaquete = $producto->ident_paquete;
                if ($ispaquete == 1) {
                    $cantidad = 999999999999;
                }
            }
        }
        return $cantidad;
    }
    public static function iniciales($id, $enterprise, $fecha_hasta = "")
    {
        $data = [];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $iniciales = Ct_Inv_Kardex::join('ct_inv_interno as interno', 'interno.id', 'ct_inv_kardex.id_inv')->where('interno.id_empresa', $enterprise)->whereYear('ct_inv_kardex.fecha', $fecha_hasta)->where('ct_inv_kardex.estado', 1)->where('ct_inv_kardex.id_transaccion', 1)->where('ct_inv_kardex.id_producto', $id)->select('ct_inv_kardex.*')->first();
        if (!is_null($iniciales)) {
            $cantidad         = $iniciales->cantidad;
            $precio           = $iniciales->precio;
            $totales          = $cantidad * $precio;
            $data['fecha']    = $iniciales->fecha;
            $data['cantidad'] = $cantidad;
            $data['precio']   = $precio;
            $data['total']    = $totales;
        }
        return $data;
    }
    public static function utilidad_insumo($id)
    {
        $valor    = 0;
        $producto = Ct_productos::where('id', $id)->first();
        if (!is_null($producto)) {
            $ligue = Ct_productos_insumos::where('id_producto', $producto->id)->first();
            if (!is_null($ligue)) {
                $insumo = Producto::find($ligue->id_insumo);
                if (!is_null($insumo)) {
                    $qr = InvDetMovimientos::where('id_producto', $insumo->id)->with(['cabecera' => function ($query) {
                        $query->where('id_documento_bodega', 10);
                    }])->get();
                    $cantidad = 0;
                    $precio   = 0;
                    foreach ($qr as $pr) {
                        $cantidad += $pr->cantidad;
                        $precio += $pr->precio;
                    }
                    return $cantidad;
                }
            }
            return $valor;
        }
        return $valor;
    }
    public static function load_insumos()
    {
        $id_empresa      = Session::get('id_empresa');
        $productos       = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->get();
        $array_productos = [];
        foreach ($productos as $p) {
            if ($p->codigo != null) {
                $data['id']          = $p->id;
                $data['nombre']      = $p->nombre;
                $data['descripcion'] = $p->descripcion;
                array_push($array_productos, $data);
            }
        }
        return $array_productos;
    }
    public static function recalcular_masivo()
    {
        $f = Ct_Inv_Interno::where('estado', 1)->get();
        foreach ($f as $k) {
            $detalles    = Ct_Inv_Kardex::where('id_inv', $k->id)->where('estado', 1)->sum('cantidad');
            $costo       = Ct_Inv_Kardex::where('id_inv', $k->id)->where('estado', 1)->sum('precio');
            $k->cantidad = $detalles;
            $k->stock    = $detalles;
            $k->costo    = $costo;
            $k->save();
        }
    }
    public static function permitidos()
    {
        $id_user = Auth::user()->id;
        $user    = User::find($id_user);
        $data    = array();
        $paso    = Ct_Paso_Proceso::where('estado', 1)->get();
        foreach ($paso as $key => $value) {
            $proceso = Ct_Usuario_Proceso::where('id_usuario', $id_user)->where('id_paso', $value->id)->first();
            if (!is_null($proceso) or $user->id_tipo_usuario == 1) {
                $data[$value->nombre] = true;
            } else {
                $data[$value->nombre] = false;
            }
        }
        return $data;
    }

    public static function consigna($id = "", $enterprise)
    {
        if ($id != "") {
            $pedido = Pedido::find($id);
            if (!isset($pedido->id)) {return "error";}
            $detpedido     = Detalle_Pedido::where('id_pedido',$id)->get();
            $id_movimiento = Ct_Inv_Movimiento::insertGetId([
                'id_transaccion' => '2',
                'id_referencia'  => $pedido->id,
                'pedido'         => $pedido->pedido,
                'tipo'           => 'COMP', // CONS
            ]);
            $data['mov'] = $id_movimiento;
            $parte       = "";
            foreach ($detpedido as $value) {  
                $ct_producto = Ct_productos_insumos::where('id_insumo', $value->id_producto)->first();
                $producto = null;
                if (isset($ct_producto->id))  {
                    $producto = Ct_productos::where('id_empresa', $enterprise)->where('id', $ct_producto->id_producto)->first();
                }
                if (isset($producto->id)) { 
                    $exist = Inventario::verifyexist($producto->id, $enterprise);
                    if ($exist == 'N') {
                        $id_interno = Ct_Inv_Interno::insertGetId([
                            'id_producto' => $producto->id,
                            'stock'       => 1,
                            'cantidad'    => 1,
                            'costo'       => 0,
                            'id_empresa'  => $enterprise,
                        ]);
                        $cantidad = 0;
                        $movprecio   = 0;
                        $precio   = 0;
                        $movim    = Movimiento::where('id_pedido',$id)
                                                ->where('id_producto', $value->id_producto)
                                                ->first();
                        if (isset($movim->id)) {
                            $movprecio   = $movim->precio;
                        }
                        // foreach ($value as $x) {
                            Ct_Inv_Kardex::create([
                                'id_bodega'      => 1, // consignacion
                                'fecha'          => $pedido->fecha,
                                'id_transaccion' => '2', //ingreso
                                'id_movimiento'  => $data['mov'],
                                'precio'         => $movprecio,
                                'id_producto'    => $producto->id,
                                'id_inv'         => $id_interno,
                                'cantidad'       => $value->cantidad,
                            ]);
                            $cantidad += $value->cantidad;
                            $precio += $movprecio;
                        // }
                        $inv           = Ct_Inv_Interno::find($id_interno);
                        $inv->stock    = $cantidad;
                        $inv->cantidad = $cantidad;
                        $inv->costo    = $precio;
                        $inv->save();
                        $id_costos = Ct_Inv_Costos::insertGetId([
                            'costo_promedio' => $precio,
                            'costo_anterior' => $precio,
                            'id_empresa'     => $enterprise,
                            'id_producto'    => $producto->id,
                        ]);
                        Ct_Inv_Costos_Detalle::create([
                            'id_costo'      => $id_costos,
                            'fecha'         => date('Y-m-d H:i:s'),
                            'estado'        => '1',
                            'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                            'costo'         => $precio,
                            'costo_ant'     => $precio,
                        ]);
                    } else {
                        $id_interno = $exist;
                        $cantidad   = 0;
                        $precio     = 0;
                        $movprecio  = 0;
                        $movim    = Movimiento::where('id_pedido',$id)
                                                ->where('id_producto', $value->id_producto)
                                                ->first();
                        if (isset($movim->id)) {
                            $movprecio   = $movim->precio;
                        }
                        // foreach ($value as $x) {
                            Ct_Inv_Kardex::create([
                                'id_bodega'      => 1, // consignacion
                                'id_transaccion' => '2', //ingreso
                                'id_movimiento'  => $data['mov'],
                                'id_producto'    => $producto->id,
                                'precio'         => $movprecio,
                                'id_inv'         => $id_interno,
                                'cantidad'       => $value->cantidad,
                            ]);
                            $cantidad += $value->cantidad;
                            $precio += $movprecio;
                        // }
                        $interno           = Ct_Inv_Interno::find($id_interno);
                        $interno->stock    = $interno->stock + $cantidad;
                        $interno->cantidad = $interno->cantidad + $cantidad;
                        $interno->costo    = ($interno->costo + $precio) / 2;
                        $interno->save();
                        $costos = Ct_Inv_Costos::where('id_producto', $producto->id)->where('id_empresa', $enterprise)->first();
                        if ($costos != null) {
                            $costos->costo_promedio = ($interno->costo + $precio) / 2;
                            $costos->save();
                            Ct_Inv_Costos_Detalle::create([
                                'id_costo'      => $costos->id,
                                'fecha'         => date('Y-m-d H:i:s'),
                                'estado'        => '1',
                                'observaciones' => 'FECHA ' . date('Y-m-d H:i:s'),
                                'costo'         => ($interno->costo + $precio) / 2,
                                'costo_ant'     => $precio,
                            ]);
                        }
                    }

                }
            }
        }
        return 'ok';
    }

}
