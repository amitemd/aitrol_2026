<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Inventario;
use Session;
use Illuminate\Support\Facades\Auth;

class Ct_Kardex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_kardex';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function product()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'producto_id', 'id');
    }

    // public function marca_activo()
    // {
    //     return $this->belongsTo('Sis_medico\Marca', 'marca', 'id');
    // }


    public static function generar_kardex($data)
    {
        // $keys = array('id','tipo');
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        if (isset($data['id']) and isset($data['tipo'])) {

            if ($data['tipo'] == 'VEN-FA' and isset($data['id'])) {
                return Ct_Kardex::kardex_ventas($data);
            }
            if ($data['tipo'] == '1' and isset($data['id'])) {
                return Ct_Kardex::kardex_compras($data);
            }
            if ($data['tipo'] == 'ING-INV' and isset($data['id'])) {
                return Ct_Kardex::kardex_nota_inventario($data);
            }
        } else {
            return $msg['mensaje'] = "Error: parametros incompletos";
        }
    }

    public static function kardex_ventas($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_venta = Ct_ventas::where('id', $data['id'])->first();
        $det_venta = Ct_detalle_venta::where('id_ct_ventas', $data['id'])->get();
        if(isset($data['omni'])){
            if($data['omni']=='1'){
                $det_venta= Ct_Detalle_Venta_Omni::where('id_ct_ventas',$data['id'])->get();
            }
        }
        $input = array();
        if ($det_venta->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_venta as $value) {
            $producto = Ct_productos::where('codigo', $value->id_ct_productos)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Ct_Kardex::where('producto_id', $value->id_ct_productos)
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
                $es_paquete= $producto->ident_paquete;
                if($es_paquete==1){
                    $details= Ct_productos_paquete::where('id_producto',$producto->id)->get();
                  
                    if(!is_null($details)){

                            
                        
                       
                        foreach($details as $bs){
                          
                            $paqut= Ct_productos::where('id',$bs->id_paquete)->first();
                            
                            if(!is_null(($paqut))){
                                $ispaquete= $paqut->ident_paquete;
                                //return 'ok';
                                if($ispaquete=="1"){
                                   
                                    $details2= Ct_productos_paquete::where('id_producto',$paqut->id)->get();
                                    //dd($details);
                                    //return $details2;
                                    foreach($details2 as $s){
                                        $s = Ct_Kardex::create([
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
                                $kardex = Ct_Kardex::create([
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
                 
                }else{
                    $kardex = Ct_Kardex::create([
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
               

                $inventario = Ct_Inventario::where('producto_id', $producto->id)->where('bodega_id', $value->bodega_id)->first();
                // dd($inventario);    
                if ($inventario == '[]' or $inventario == null) {
                    $inv = Ct_Inventario::create([
                        'bodega_id'             => $value->bodega_id,
                        'producto_id'           => $producto->id,
                        'existencia'            => $input['saldo_cantidad'],
                        'id_empresa'            => $cab_venta->id_empresa,
                        'ip_creacion'           => $ip_cliente,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                    ]);
                } else {
                    $inventario->existencia     =  $input['saldo_cantidad'];
                    $inventario->save();
                }
            }
        } //foreach

        return $msg['mensaje'] = "Mensaje: registro ingresado al kardex";
    }

    public static function kardex_compras($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_comp = Ct_compras::where('id', $data['id'])->first();
        $det_comp = Ct_detalle_compra::where('id_ct_compras', $data['id'])->get();
        // dd($det_comp);
        $input = array();
        if ($det_comp->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_comp as $value) {
            $producto = Ct_productos::where('codigo', $value->codigo)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Ct_Kardex::where('producto_id', $producto->id)
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

                $kardex = Ct_Kardex::create([
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

                $inventario = Ct_Inventario::where('producto_id', $producto->id)->where('bodega_id', $value->bodega)->first();
                // dd($inventario);  
                if ($inventario == '[]' or $inventario == null) {
                    $inv = Ct_Inventario::create([
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

    public static function kardex_nota_inventario($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_inv    = Ct_Nota_Inventario::where('id', $data['id'])->first();
        $det_inv    = Ct_Conglomerado_Productos::where('id_inventario', $data['id'])->get();
        $input      = array();
        if ($det_inv->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_inv as $value) {
            $producto = Ct_productos::where('codigo', $value->codigo)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Ct_Kardex::where('producto_id', $producto->id)
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
                $kardex = Ct_Kardex::create([
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

                $inventario = Ct_Inventario::where('producto_id', $producto->id)->where('bodega_id', $value->bodega)->first();
                // dd($inventario);  
                if ($inventario == '[]' or $inventario == null) {
                    $inv = Ct_Inventario::create([
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

    public static function anular_kardex($data)
    {
        // $keys = array('id','tipo');
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $data['anular']   =   1;
        if (isset($data['id']) and isset($data['tipo'])) {
            if ($data['tipo'] == 'VEN-FA' and isset($data['id'])) {
                return Ct_Kardex::kardex_ventas($data);
            } elseif ($data['tipo'] == '1' and isset($data['id'])) {
                return Ct_Kardex::kardex_compras($data);
            } elseif ($data['tipo'] == 'ING-INV' and isset($data['id'])) {
                return Ct_Kardex::kardex_nota_inventario($data);
            }
            // return $msg['mensaje'] = "Mensaje: registro ingresado al kardex";

        } else {
            return $msg['mensaje'] = "Error: parametros incompletos";
        }
    }

    public static function anular_kardex_ventas($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_venta = Ct_ventas::where('id', $data['id'])->first();
        $det_venta = Ct_detalle_venta::where('id_ct_ventas', $data['id'])->get();
        $input = array();
        if ($det_venta->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_venta as $value) {
            $producto = Ct_productos::where('codigo', $value->id_ct_productos)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Ct_Kardex::where('producto_id', $value->id_ct_productos)
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

                $input['saldo_cantidad']             = $saldo_cantidad + $value->cantidad;

                $kardex = Ct_Kardex::create([
                    'fecha'                 => date('Y-m-d'),
                    'id_movimiento'         => $data['id'],
                    'movimiento'            => 1,
                    'tipo'                  => 'ANU-' . $data['tipo'],
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

                $inventario = Ct_Inventario::where('producto_id', $producto->id)->where('bodega_id', $value->bodega_id)->first();
                if ($inventario == '[]') {
                    $inv = Ct_Inventario::create([
                        'bodega_id'             => $value->bodega_id,
                        'producto_id'           => $producto->id,
                        'existencia'            => $input['saldo_cantidad'],
                        'id_empresa'            => $cab_venta->id_empresa,
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

    public static function anular_kardex_compras($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_comp = Ct_compras::where('id', $data['id'])->first();
        $det_comp = Ct_detalle_compra::where('id_ct_compras', $data['id'])->get();
        // dd($det_comp);
        $input = array();
        if ($det_venta->isEmpty()) {
            return $msg['mensaje'] = "Error: El movimiento no registra detalles";
        }
        foreach ($det_comp as $value) {
            $producto = Ct_productos::where('codigo', $value->codigo)->first();
            if (isset($producto->id)) {
                $kardex_ant  = Ct_Kardex::where('producto_id', $producto->id)
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

                $input['saldo_cantidad']             = $saldo_cantidad - $value->cantidad;
                $input['saldo_valor_unitario']       = $saldo_valor_unitario;
                // if($input['saldo_valor_unitario'] != 0) {    $input['saldo_valor_unitario'] = $input['saldo_valor_unitario'] / 2;  }
                $input['saldo_total']                = $input['saldo_valor_unitario'] * $value->cantidad;

                $kardex = Ct_Kardex::create([
                    'fecha'                 => date('Y-m-d'),
                    'id_movimiento'         => $data['id'],
                    'movimiento'            => 1,
                    'tipo'                  => 'ANUL-COMP',
                    'numero'                => $cab_comp->numero,
                    'cantidad'              => $value->cantidad,
                    'valor_unitario'        => $value->precio,
                    'total'                 => ($value->cantidad * $value->precio),
                    'id_empresa'            => $cab_comp->id_empresa,
                    'producto_id'           => $producto->id,
                    'bodega_id'             => $value->bodega,
                    'saldo_cantidad'        => $input['saldo_cantidad'],
                    'saldo_valor_unitario'  => $input['saldo_valor_unitario'],
                    'saldo_total'           => $input['saldo_total'],
                ]);

                $inventario = Ct_Inventario::where('producto_id', $producto->id)->where('bodega_id', $value->bodega)->first();
                if ($inventario == '[]') {
                    $inv = Ct_Inventario::create([
                        'bodega_id'             => $value->bodega,
                        'producto_id'           => $producto->id,
                        'existencia'            => $input['saldo_cantidad'],
                        'id_empresa'            => $cab_comp->id_empresa,
                        'ip_creacion'           => $ip_cliente,
                        'id_usuariocrea'        => $idusuario,
                    ]);
                } else {
                    $inventario->existencia     =  $input['saldo_cantidad'];
                    $inventario->save();
                }
            }
        }

        return $msg['mensaje'] = "Mensaje: registro ingresado al kardex";
    }

    public static function anular_kardex_nota_inventario($data)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
    }
}
