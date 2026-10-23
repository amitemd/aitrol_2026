<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Sis_medico\v3\contable\InvCabMovimientos;
use Sis_medico\v3\contable\InvDocumentosBodegas;
use Sis_medico\v3\contable\InvTransaccionesBodegas;
use Sis_medico\v3\contable\InventarioCt;

class Kardex extends Model
{
    private static $modelo = 'Kardex';
    protected $table = 'ct_kardex';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function generar_kardex($data)
    {
        if (isset($data['id']) and isset($data['tipo'])) {
            if ($data['tipo'] == 'VEN-FA' and isset($data['id'])) {
                return Kardex::kardex_ventas($data);
            }
            if ($data['tipo'] == '1' and isset($data['id'])) {
                return Kardex::kardex_compras($data);
            }
            if ($data['tipo'] == 'ING-INV' and isset($data['id'])) {
                return Kardex::kardex_nota_inventario($data);
            }
        } else {
            return $msg['mensaje'] = "Error: parametros incompletos";
        }
    }


    public static function kardex_nota_inventario($data)
    {
        $empresa    = session('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_inv    = NotaInventario::where('id', $data['id'])->first();
        $det_inv    = ConglomeradoProductos::where('id_inventario', $data['id'])->get();
        $input      = array();
        if ($det_inv->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_inv as $value) {
            $producto = Productos::where('codigo', $value->codigo)->first();
            if (isset($producto->id)) {
                $kardex_ant  = kardex::where('producto_id', $producto->id)
                    ->where('bodega_id', $value->bodega)
                    ->where('id_empresa', $cab_inv->id_empresa)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!isset($kardex_ant->saldo_cantidad)) {
                    $saldo_cantidad = 0;
                } else {
                    $saldo_cantidad = $kardex_ant->saldo_cantidad;
                }
                if (!isset($kardex_ant->saldo_valor_unitario)) {
                    $saldo_valor_unitario = 0;
                } else {
                    $saldo_valor_unitario = $kardex_ant->saldo_valor_unitario;
                }
                if (!isset($kardex_ant->saldo_total)) {
                    $saldo_total = 0;
                } else {
                    $saldo_total = $kardex_ant->saldo_total;
                }

                if (!isset($data['anular'])) {
                    $input['saldo_cantidad']    =   $saldo_cantidad + $value->cantidad;
                    $tipo                       =   'ING-INV';
                    $movimiento                 =   1;
                } else {
                    $input['saldo_cantidad']    =   $saldo_cantidad - $value->cantidad;
                    $tipo                       =   'ANUL-ING-INV';
                    $movimiento                 =   2;
                }
                Kardex::insert([
                    'fecha'                 => $cab_inv->fecha,
                    'id_movimiento'         => $data['id'],
                    'movimiento'            => $movimiento,
                    'tipo'                  => $tipo,
                    'numero'                => $cab_inv->secuencia,
                    'cantidad'              => $value->cantidad,
                    'valor_unitario'        => $value->costo,
                    'total'                 => ($value->cantidad * $value->precio),
                    'id_empresa'            => $cab_inv->id_empresa,
                    'producto_id'           => $producto->id,
                    'bodega_id'             => $value->bodega,
                    'saldo_cantidad'        => $input['saldo_cantidad'],
                    'saldo_valor_unitario'  => $saldo_valor_unitario,
                    'saldo_total'           => $saldo_total,
                ]);
                $inventario = InventarioCt::where('producto_id', $producto->id)->where('bodega_id', $value->bodega)->first();
                if ($inventario == '[]' or $inventario == null) {
                    $inv = InventarioCt::insert([
                        'bodega_id'             => $value->bodega,
                        'producto_id'           => $producto->id,
                        'existencia'            => $input['saldo_cantidad'],
                        'id_empresa'            => $cab_inv->id_empresa,
                        'ip_creacion'           => $ip_cliente,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                    ]);
                } else {
                    $inventario->existencia     =  $input['saldo_cantidad'];
                    $inventario->save();
                }
            }
        }
        return $msg['mensaje'] = "Mensaje: registro ingresado al kardex";
    }


    public static function kardex_compras($data)
    {
        $empresa    = session('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_comp = Compras::where('id', $data['id'])->first();
        $det_comp = CtDetalleCompra::where('id_ct_compras', $data['id'])->get();
        // dd($det_comp);
        $input = array();
        if ($det_comp->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_comp as $value) {
            $producto = Productos::where('codigo', $value->codigo)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Kardex::where('producto_id', $producto->id)
                    ->where('bodega_id', $value->bodega)
                    ->where('id_empresa', $cab_comp->id_empresa)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!isset($kardex_ant->saldo_cantidad)) {
                    $saldo_cantidad = 0;
                } else {
                    $saldo_cantidad = $kardex_ant->saldo_cantidad;
                }
                if (!isset($kardex_ant->saldo_valor_unitario)) {
                    $saldo_valor_unitario = 0;
                } else {
                    $saldo_valor_unitario = $kardex_ant->saldo_valor_unitario;
                }
                if (!isset($kardex_ant->saldo_total)) {
                    $saldo_total = 0;
                } else {
                    $saldo_total = $kardex_ant->saldo_total;
                }

                if (!isset($data['anular'])) {
                    $input['saldo_cantidad']             = $saldo_cantidad + $value->cantidad;
                    $input['saldo_valor_unitario']       = ($saldo_valor_unitario + $value->precio);
                    if ($input['saldo_valor_unitario'] != 0) {
                        $input['saldo_valor_unitario'] = $input['saldo_valor_unitario'] / 2;
                    }
                    $input['saldo_total']                = $input['saldo_valor_unitario'] * $value->cantidad;
                    $tipo                                = 'COMP';
                    $movimiento                          = 1;
                } else {
                    $input['saldo_cantidad']             = $saldo_cantidad - $value->cantidad;
                    $input['saldo_valor_unitario']       = $saldo_valor_unitario;
                    // if($input['saldo_valor_unitario'] != 0) {    $input['saldo_valor_unitario'] = $input['saldo_valor_unitario'] / 2;  }
                    $input['saldo_total']                = $input['saldo_valor_unitario'] * $value->cantidad;
                    $tipo                                = 'ANUL-COMP';
                    $movimiento                          = 2;
                }

                Kardex::insert([
                    'fecha'                 => $cab_comp->fecha,
                    'id_movimiento'         => $data['id'],
                    'movimiento'            => $movimiento,
                    'tipo'                  => $tipo,
                    'numero'                => $cab_comp->numero,
                    'cantidad'              => $value->cantidad,
                    'valor_unitario'        => $value->precio,
                    'total'                 => ($value->cantidad * $value->precio),
                    'id_empresa'            => $cab_comp->id_empresa,
                    'producto_id'           => $producto->id,
                    'bodega_id'             => $value->bodega,
                    'saldo_cantidad'        => $input['saldo_cantidad'],
                    'saldo_valor_unitario'  => $saldo_valor_unitario,
                    'saldo_total'           => $saldo_total,
                ]);

                $inventario = InventarioCt::where('producto_id', $producto->id)->where('bodega_id', $value->bodega)->first();
                if ($inventario == '[]' or $inventario == null) {
                    InventarioCt::insert([
                        'bodega_id'             => $value->bodega,
                        'producto_id'           => $producto->id,
                        'existencia'            => $input['saldo_cantidad'],
                        'id_empresa'            => $cab_comp->id_empresa,
                        'ip_creacion'           => $ip_cliente,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                    ]);
                } else {
                    $inventario->existencia     =  $input['saldo_cantidad'];
                    $inventario->save();
                }
            }
        }

        return $msg['mensaje'] = "Mensaje: registro ingresado al kardex";
    }


    public static function kardex_ventas($data)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $cab_venta = Ventas::where('id', $data['id'])->first();
            $det_venta = DetalleVenta::where('id_ct_ventas', $data['id'])->get();
            if (isset($data['omni'])) {
                if ($data['omni'] == '1') {
                    $det_venta = DetalleVentaOmni::where('id_ct_ventas', $data['id'])->get();
                }
            }
            $input = array();
            if ($det_venta->isEmpty()) {
                throw new Exception("Error: El movimiento no registra detalles");
            }
            foreach ($det_venta as $value) {
                $producto = Productos::where('codigo', $value->id_ct_productos)->first();
                if (isset($producto->id)) {
                    $kardex_ant  = Kardex::where('producto_id', $value->id_ct_productos)
                        ->where('bodega_id', $value->bodega)
                        ->where('id_empresa', $cab_venta->id_empresa)
                        ->orderBy('id', 'desc')
                        ->first();
                    if (!isset($kardex_ant->saldo_cantidad)) {
                        $saldo_cantidad = 0;
                    } else {
                        $saldo_cantidad = $kardex_ant->saldo_cantidad;
                    }
                    if (!isset($kardex_ant->saldo_valor_unitario)) {
                        $saldo_valor_unitario = 0;
                    } else {
                        $saldo_valor_unitario = $kardex_ant->saldo_valor_unitario;
                    }
                    if (!isset($kardex_ant->saldo_total)) {
                        $saldo_total = 0;
                    } else {
                        $saldo_total = $kardex_ant->saldo_total;
                    }
                    if (!isset($data['anular'])) {
                        $input['saldo_cantidad']    = $saldo_cantidad - $value->cantidad;
                        $movimiento                 = 2;
                    } else {
                        $input['saldo_cantidad']    = $saldo_cantidad + $value->cantidad;
                        $movimiento                 = 1;
                        $data['tipo']               = 'ANU-' . $data['tipo'];
                    }
                    $es_paquete = $producto->ident_paquete;
                    if ($es_paquete == 1) {
                        $details = ProductosPaquete::where('id_producto', $producto->id)->get();
                        if (!is_null($details)) {
                            foreach ($details as $bs) {
                                $paqut = Productos::where('id', $bs->id_paquete)->first();
                                if (!is_null(($paqut))) {
                                    $ispaquete = $paqut->ident_paquete;
                                    if ($ispaquete == "1") {
                                        $details2 = ProductosPaquete::where('id_producto', $paqut->id)->get();
                                        foreach ($details2 as $s) {
                                            $s = Kardex::insert([
                                                'fecha'                 => $cab_venta->fecha,
                                                'id_movimiento'         => $data['id'],
                                                'movimiento'            => $movimiento,
                                                'tipo'                  => $data['tipo'],
                                                'numero'                => $cab_venta->nro_comprobante,
                                                'cantidad'              => $s->cantidad,
                                                'valor_unitario'        => $s->precio,
                                                'total'                 => ($s->cantidad * $s->precio),
                                                'id_empresa'            => $cab_venta->id_empresa,
                                                'producto_id'           => $s->id_paquete,
                                                'bodega_id'             => $value->bodega_id,
                                                'saldo_cantidad'        => $input['saldo_cantidad'],
                                                'saldo_valor_unitario'  => $saldo_valor_unitario,
                                                'saldo_total'           => $saldo_total,
                                                'ip_creacion'           => $ip_cliente,
                                                'id_usuariocrea'        => $idusuario,
                                            ]);
                                        }
                                    }
                                    Kardex::insert([
                                        'fecha'                 => $cab_venta->fecha,
                                        'id_movimiento'         => $data['id'],
                                        'movimiento'            => $movimiento,
                                        'tipo'                  => $data['tipo'],
                                        'numero'                => $cab_venta->nro_comprobante,
                                        'cantidad'              => $bs->cantidad,
                                        'valor_unitario'        => $bs->precio,
                                        'total'                 => ($bs->cantidad * $bs->precio),
                                        'id_empresa'            => $cab_venta->id_empresa,
                                        'producto_id'           => $bs->id_paquete,
                                        'bodega_id'             => $value->bodega_id,
                                        'saldo_cantidad'        => $input['saldo_cantidad'],
                                        'saldo_valor_unitario'  => $saldo_valor_unitario,
                                        'saldo_total'           => $saldo_total,
                                        'ip_creacion'           => $ip_cliente,
                                        'id_usuariocrea'        => $idusuario,
                                    ]);
                                }
                            }
                        }
                    } else {
                        Kardex::insert([
                            'fecha'                 => $cab_venta->fecha,
                            'id_movimiento'         => $data['id'],
                            'movimiento'            => $movimiento,
                            'tipo'                  => $data['tipo'],
                            'numero'                => $cab_venta->nro_comprobante,
                            'cantidad'              => $value->cantidad,
                            'valor_unitario'        => $value->precio,
                            'total'                 => ($value->cantidad * $value->precio),
                            'id_empresa'            => $cab_venta->id_empresa,
                            'producto_id'           => $producto->id,
                            'bodega_id'             => $value->bodega_id,
                            'saldo_cantidad'        => $input['saldo_cantidad'],
                            'saldo_valor_unitario'  => $saldo_valor_unitario,
                            'saldo_total'           => $saldo_total,
                            'ip_creacion'           => $ip_cliente,
                            'id_usuariocrea'        => $idusuario,
                        ]);
                    }


                    $inventario = InventarioCt::where('producto_id', $producto->id)->where('bodega_id', $value->bodega_id)->first();
                    if (empty($inventario)) {
                        InventarioCt::insert([
                            'bodega_id'             => $value->bodega_id,
                            'producto_id'           => $producto->id,
                            'existencia'            => $input['saldo_cantidad'],
                            'id_empresa'            => $cab_venta->id_empresa,
                            'ip_creacion'           => $ip_cliente,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                        ]);
                    } else {
                        $inventario->existencia  = $input['saldo_cantidad'];
                        $inventario->save();
                    }
                }
            }

            return [
                'status' => true,
                'code' => 200,
                'message' => $msg['mensaje'] = "Mensaje: registro ingresado al kardex"
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
            ];
        }
    }


    public static function anular_kardex($data)
    {

        try {
            // aqui
            $data['anular']   =   1;
            if (isset($data['id']) and isset($data['tipo'])) {
                if ($data['tipo'] == 'VEN-FA' and isset($data['id'])) {
                    $variable =  Kardex::kardex_ventas($data);
                    if (!$variable["status"]) {
                        throw new Exception($variable['message']);
                    }
                } elseif ($data['tipo'] == '1' and isset($data['id'])) {
                    return Kardex::kardex_compras($data);
                } elseif ($data['tipo'] == 'ING-INV' and isset($data['id'])) {
                    return Kardex::kardex_nota_inventario($data);
                }
            } else {
                throw new Exception("Error: parametros incompletos");
            }

            return [
                'status' => true,
                'code' => 200,
                'message' => "ok",

            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
            ];
        }
    }

    public static function documentoEgresoCliente($inv_serie, $cantidad, $cantidad_uso, $id_bodega_param = 2, $id_detalle_pla, $datos_factura, $detalle_unico = "")
    {
        try {
            $id_bodega = $id_bodega_param;
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $idusuario = Auth::user()->id;
            $documento_bodega_abr = 'EGV';
            $cab_mov_inv = DB::table('inv_cab_movimientos as c')
                ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
                ->where('d.abreviatura_documento', $documento_bodega_abr)
                ->where('c.id_bodega_origen', $id_bodega)
                ->where('c.id_empresa', session("id_empresa"))
                ->where('c.estado', '!=', 0)
                ->where('c.data_humanlabs', $detalle_unico)
                ->select('c.*')
                ->first();
            if (!is_null($inv_serie->inventario)) {
                $valor_precio = $inv_serie->inventario->costo_promedio;
            } else {
                $movimiento = Movimiento::where('serie', $inv_serie->serie)->orderBy('created_at', 'Desc')->first();
                $valor_precio = $movimiento->precio;
            }
            if (!isset($cab_mov_inv->id)) {
                $documento = InvDocumentosBodegas::where('abreviatura_documento', $documento_bodega_abr)->first();
                $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, $documento_bodega_abr);

                if ($secuencia == 0) {
                    throw new Exception("Error en el guarado en InvDocumentosBodegas::getSecueciaTipoDocum");
                }
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $id_bodega)
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $id_bodega;
                $cab_mov_inv->numero_documento      = str_pad($secuencia["msj"], 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = "EGRESO FACT CLIENTE: {$datos_factura['id_cliente']}, ID VENTA: {$datos_factura['id']}";
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->id_empresa            = session("id_empresa");
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->data_humanlabs        = $detalle_unico;
                $cab_mov_inv->save();
            }
            $iva = 0;
            $inicial = InvDetMovimientos::where('serie', $inv_serie->serie)
                ->where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();
            $det_mov_inv                         = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto            = $inv_serie->id_producto;
            $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
            $det_mov_inv->cantidad               = $cantidad;
            $det_mov_inv->cant_uso               = $cantidad_uso;
            $det_mov_inv->serie                  = $inv_serie->serie;
            $det_mov_inv->lote                   = $inv_serie->lote;
            $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
            $det_mov_inv->valor_unitario         = $valor_precio;
            $det_mov_inv->subtotal               = ($valor_precio * $cantidad);
            $det_mov_inv->descuento              = 0;
            $det_mov_inv->iva                    = $iva;
            $det_mov_inv->total                  = ($valor_precio * $cantidad) + $iva;
            $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
            if (isset($inicial->cabecera)) {
                $det_mov_inv->id_pedido = $inicial->cabecera->id_pedido;
            }
            $det_mov_inv->ip_creacion            = $ip_cliente;
            $det_mov_inv->ip_modificacion        = $ip_cliente;
            $det_mov_inv->id_usuariocrea         = $idusuario;
            $det_mov_inv->id_usuariomod          = $idusuario;
            $det_mov_inv->save();
            $serie = $inv_serie->serie;
            $id_inv_det_mov = $det_mov_inv->id;
            $kardex = InvKardex::setKardex2($cab_mov_inv->id);
            if (!$kardex["status"]) {
                throw new Exception("Error al registrar el kardex");
            }
            return ["status" => true, "cab_mov_inv" => $cab_mov_inv->id];
        } catch (\Exception $e) {
            return ["status" => false, "msj" => "Error al registrar el kardex", "exp" => $e->getMessage(), "mod" => "KardexUsoLabs"];
        }
    }

    public static function generaAsientoCosto($data)
    {
        try {
            $debe = Configuraciones::where('id_empresa', session("id_empresa"))->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')->first();
            if (is_null($debe)) {
                throw new Exception("No tiene confugarado la cuenta debe (COSTO_DEBE_PROCEDIMIENTO)");
            }
            $haber = Configuraciones::where('id_empresa', session("id_empresa"))->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')->first();
            if (is_null($haber)) {
                throw new Exception("No tiene confugarado la cuenta haber (COSTO_HABER_PROCEDIMIENTO)");
            }
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_usuario = Auth::user()->id;
            $cab_asiento                  = new AsientosCabecera();
            $cab_asiento->observacion     = "COSTO VENTA: {$data['observacion']}";
            $cab_asiento->fecha_asiento   = date('Y-m-d');
            $cab_asiento->valor           = $data["total"];
            $cab_asiento->estado          = 1;
            $cab_asiento->id_empresa      = session("id_empresa");
            $cab_asiento->modulo          = 'COSTO_PROCEDIMIENTO';
            $cab_asiento->ip_creacion     = $ip_cliente;
            $cab_asiento->ip_modificacion = $ip_cliente;
            $cab_asiento->id_usuariocrea  = $id_usuario;
            $cab_asiento->id_usuariomod   = $id_usuario;
            $cab_asiento->save();
            $plan                             = Plan_Cuentas::where('id', $debe->id_plan)->first();
            $det_asiento                      = new AsientosDetalles();
            $det_asiento->id_asiento_cabecera = $cab_asiento->id;
            $det_asiento->fecha               = date('Y-m-d');
            $det_asiento->id_plan_cuenta      = $debe->id_plan;
            $det_asiento->descripcion         = $plan->nombre;
            $det_asiento->debe                = $data["total"];
            $det_asiento->haber               = 0;
            $det_asiento->estado              = 1;
            $det_asiento->ip_creacion         = $ip_cliente;
            $det_asiento->ip_modificacion     = $ip_cliente;
            $det_asiento->id_usuariocrea      = $id_usuario;
            $det_asiento->id_usuariomod       = $id_usuario;
            $det_asiento->save();
            $plan                             = Plan_Cuentas::where('id', $haber->id_plan)->first();
            $det_asiento                      = new AsientosDetalles();
            $det_asiento->id_asiento_cabecera = $cab_asiento->id;
            $det_asiento->fecha               = date('Y-m-d');
            $det_asiento->id_plan_cuenta      = $haber->id_plan;
            $det_asiento->descripcion         = $plan->nombre;
            $det_asiento->haber               = $data["total"];
            $det_asiento->debe                = 0;
            $det_asiento->estado              = 1;
            $det_asiento->ip_creacion         = $ip_cliente;
            $det_asiento->ip_modificacion     = $ip_cliente;
            $det_asiento->id_usuariocrea      = $id_usuario;
            $det_asiento->id_usuariomod       = $id_usuario;
            $det_asiento->save();
            return ["status" => true, "msj" => "Generado con exito", "id_asiento" => $cab_asiento->id];
        } catch (\Exception $e) {
            return ["status" => false, "msj" => "Ocurrio un error", "exp" => $e->getMessage(), "e" => "NuevoKardexContr linea : {$e->getLine()}"];
        }
    }
    public static function newgenerarKardex($idVenta)
    {
        DB::beginTransaction();
        try {
            $data_asiento["total"] = 0;
            $detalle_unico = "";
            $cab_movimiento = InvCabMovimientos::where('id_empresa', session("id_empresa"))->latest()->first();
            $total_asiento = 0;
            $ventas_cabecera = Ventas::find($idVenta);
            $id_usuario = Auth::user()->id;
            $numero = "000000001";
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            if (!is_null($cab_movimiento)) {
                $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
            }
            $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EGV')->where('id_empresa', session("id_empresa"))->first();
            $data_asiento["observacion"] = "{$ventas_cabecera->concepto} / {$ventas_cabecera->nro_comprobante}";
            if (is_null($inv_documento_bod)) {
                throw new Exception('No tiene configurado el documento de egreso');
            }
            if (count($ventas_cabecera->detalles) > 0) {
                foreach ($ventas_cabecera->detalles as $row) {
                    $ct_productos = Productos::where('codigo', $row->id_ct_productos)->first();
                    if (is_null($ct_productos)) {
                        throw new Exception('Se encontro el producto con el codigo ' . $row->codigo . " | " . $row->nombre);
                    }
                    if ($ct_productos->tipo_producto == 1) {
                        $ct_producto_insumos = ProductoInsumos::where('id_producto', $ct_productos->id)->first();
                        if (is_null($ct_producto_insumos)) {
                            throw new Exception('No existe un ligue con un producto de insumos para el producto con el codigo ' . $row->codigo . " | " . $row->nombre);
                        } else if (is_null($ct_producto_insumos->id_insumo)) {
                            throw new Exception('No existe un ligue con un producto de insumos para el producto con el codigo ' . $row->codigo . " | " . $row->nombre);
                        }
                        $union_bodega = UnionBodega::where('id_bodega_cont', $row->bodega)->first();
                        if (is_null($union_bodega)) {
                            throw new Exception('No existe un ligue con una bodega configure y vuelva a intentarlo');
                        }
                        $producto = Producto::find($ct_producto_insumos->id_insumo);
                        $bodega_compra = Bodega::find($union_bodega->id_bodega_compra);
                        if (is_null($row->id_inv_serie) or $row->id_inv_serie == "") {
                            $inv_serie = InvInventarioSerie::where('id_empresa', session("id_empresa"))
                                ->where('estado', '1')
                                ->where('id_producto', $producto->id)
                                ->where('id_bodega', $bodega_compra->id)
                                ->where('existencia', '>=', $row->cantidad)
                                ->first();
                        } else {
                            $inv_serie = InvInventarioSerie::find($row->id_inv_serie);
                        }
                        if (is_null($inv_serie)) {
                            throw new Exception('No hay suficiente stock para el producto con el codigo ' . $row->codigo . " | " . $row->nombre);
                        }
                        if (isset($producto->id)) {
                            $cantidad_uso = $row->cantidad * $producto->usos;
                        } else {
                            $cantidad_uso = 0;
                        }

                        $datos_factura = ["id_cliente" => $ventas_cabecera->id_cliente, "id" => $ventas_cabecera->id];
                        $data_asiento["total"] += ($inv_serie->inventario->costo_promedio * $row->cantidad);
                        $detalle_unico = "FV: {$ventas_cabecera->id}-{$ventas_cabecera->id_cliente}-{$ventas_cabecera->id_empresa}";
                        if (!is_null($inv_serie)) {

                            $kardex = Kardex::documentoEgresoCliente($inv_serie, $row->cantidad, $cantidad_uso, $bodega_compra->id, $row->id, $datos_factura, $detalle_unico);
                            if (!$kardex["status"] == "error") {
                                return $kardex;
                            }
                            VentaKardex::insert([
                                'id_venta'           => $ventas_cabecera->id,
                                'id_cab_moviento'    => $kardex['cab_mov_inv'],
                                'id_usuariocrea'     => $id_usuario,
                                'id_usuariomod'      => $id_usuario,
                                'ip_creacion'        => $ip_cliente,
                                'ip_modificacion'    => $ip_cliente,
                            ]);
                        }
                    }
                }
                $data['observacion'] = "{$ventas_cabecera->concepto} / {$ventas_cabecera->nro_comprobante}";
                $genera_asiento = kardex::generaAsientoCosto($data_asiento);
                if (!$genera_asiento["status"]) {
                    throw new Exception($genera_asiento["msj"]);
                }
            }
            $invcabmovimientos = InvCabMovimientos::calcularTotalCabMovimiento($kardex['cab_mov_inv']);
            if (!$invcabmovimientos["status"]) {
                throw new Exception($invcabmovimientos["msj"]);
            }
            DB::commit();
            return ["status" => true, "msj" => "Se creo el kardex", "asiento_costo" => $genera_asiento["id_asiento"]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, "msj" => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
}
