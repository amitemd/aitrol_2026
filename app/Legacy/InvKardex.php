<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvCosto;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
// use Sis_medico\InvKardex;

class InvKardex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string

     */
    use SoftDeletes;
    protected $table = 'inv_kardex';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'id_producto', 'id');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega');
    }

    // public function estado()
    // {
    //     return $this->belongsTo('Sis_medico\InvEstadoMovimientos', 'id_movimiento_estado', 'id');
    // }

    public function documento_bodega()
    {
        return $this->belongsTo('Sis_medico\InvDocumentosBodegas', 'id_documento_bodega', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    public function documento_origen()
    {
        return $this->belongsTo('Sis_medico\InvDetMovimientos', 'id_inv_det_movimientos', 'id');
    }

    public static function setKardex($id_movimiento = null)
    {
        $kardex     = '[]';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if(Auth::guest()){
            $idusuario = 'FACELECTRO';
        }else{
            $idusuario  = Auth::user()->id;            
        }

        $cab_mov = InvCabMovimientos::find($id_movimiento);
        foreach ($cab_mov->detalles as $row) {
            if ($row->kardex == 0) {
                // OBTENGO EL ULTIMO REGISTRO DEL KARDEX
                $referencia = "";
                if ($cab_mov->num_doc_ext != "") {
                    $referencia .= " PEDIDO O GUIA :" . $cab_mov->num_doc_ext;
                }
                if ($cab_mov->num_doc_cont != "") {
                    $referencia .= " NUMERO FACTURA: " . $cab_mov->num_doc_cont;
                }
                $pedido = Pedido::find($cab_mov->id_pedido);
                if (isset($pedido->id)) {
                    $referencia .= " || PROVEEDOR: " . $pedido->proveedor->nombrecomercial;
                }
                $descripcion = $cab_mov->transaccion_bodega->documentoBodega->abreviatura_documento . "-" . $cab_mov->numero_documento;
                if (strpos((string) $cab_mov->observacion, 'ANULACION') !== false) {
                    $referencia = $cab_mov->observacion;
                }
                if ($cab_mov->observacion == '') {
                    $referencia = $cab_mov->observacion;
                }
                $ultkardex                 = InvKardex::where('id_inv_inventario', $row->id_inv_inventario)->orderBy('id', 'desc')->first();
                $kardex                    = new InvKardex;
                $kardex->id_inv_inventario = $row->id_inv_inventario;
                $kardex->id_bodega         = $cab_mov->transaccion_bodega->id_bodega;
                $kardex->descripcion       = $descripcion;
                $kardex->referencia        = $referencia;
                $kardex->id_producto       = $row->id_producto;
                $kardex->tipo              = $cab_mov->documento_bodega->tipo_movimiento->tipo;
                $kardex->fecha             = $cab_mov->fecha;
                $kardex->cantidad          = $row->cantidad;
                $kardex->cant_uso          = $row->cantidad;
                if (isset($ultkardex->id)) {
                    if ($cab_mov->documento_bodega->tipo_movimiento->tipo == 'I' || $cab_mov->documento_bodega->tipo_movimiento->tipo == 'R' || $cab_mov->documento_bodega->tipo_movimiento->tipo == 'G') {
                        $cantidad_dd = $ultkardex->exist_cant;
                        $kardex->exist_cant += $ultkardex->exist_cant + $row->cantidad;
                        $kardex->exist_uso += $cantidad_dd+ $row->cantidad;
                    } else {
                        $cantidad_dd = $ultkardex->exist_cant;
                        $kardex->exist_cant += $ultkardex->exist_cant - $row->cantidad;
                        $kardex->exist_uso +=  $cantidad_dd - $row->cantidad;
                        if ($kardex->exist_cant < 0) {
                            $kardex->exist_cant = 0;
                        }
                        if ($kardex->exist_uso < 0) {
                            $kardex->exist_uso = 0;
                        }
                    }
                } else {
                    $kardex->exist_cant = $row->cantidad;
                    $kardex->exist_uso  = $row->cant_uso;
                }
                $kardex->valor_unitario         = $row->valor_unitario;
                $kardex->iva                    = $row->iva;
                $kardex->total                  = $row->total;
                $kardex->id_documento_bodega    = $cab_mov->id_documento_bodega;
                $kardex->id_inv_det_movimientos = $row->id; //id_inv_det_movimientos
                $kardex->estado                 = 1;
                $kardex->id_empresa             = Auth::guest() ? '0993075000001' : Session::get('id_empresa');
                $kardex->ip_creacion            = $ip_cliente;
                $kardex->ip_modificacion        = $ip_cliente;
                $kardex->id_usuariocrea         = $idusuario;
                $kardex->id_usuariomod          = $idusuario;
                $kardex->save();
            }

        }
        //ACTUALIZO INVENTARIO
        // InvCosto::movimientoCostoInventario($id_movimiento);//Este hay que cambiar por invinventario
        InvInventario::movimientoInventario($id_movimiento);
        InvInventarioSerie::movimientoInventarioSerie($id_movimiento);
        return $kardex;
    }



    
    public static function setKardex2($id_movimiento = null)
    {
        try{
            $kardex     = '[]';
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            if(Auth::guest()){
                $idusuario = 'FACELECTRO';
            }else{
                $idusuario  = Auth::user()->id;            
            }
    
            $cab_mov = InvCabMovimientos::find($id_movimiento);
            foreach ($cab_mov->detalles as $row) {
                if ($row->kardex == 0) {
                    // OBTENGO EL ULTIMO REGISTRO DEL KARDEX
                    $referencia = "";
                    if ($cab_mov->num_doc_ext != "") {
                        $referencia .= " PEDIDO O GUIA :" . $cab_mov->num_doc_ext;
                    }
                    if ($cab_mov->num_doc_cont != "") {
                        $referencia .= " NUMERO FACTURA: " . $cab_mov->num_doc_cont;
                    }
                    $pedido = Pedido::find($cab_mov->id_pedido);
                    if (isset($pedido->id)) {
                        $referencia .= " || PROVEEDOR: " . $pedido->proveedor->nombrecomercial;
                    }
                    $descripcion = $cab_mov->transaccion_bodega->documentoBodega->abreviatura_documento . "-" . $cab_mov->numero_documento;
                    if (strpos((string) $cab_mov->observacion, 'ANULACION') !== false) {
                        $referencia = $cab_mov->observacion;
                    }
                    if ($cab_mov->observacion != '') {
                        $referencia = $cab_mov->observacion;
                    }
                    $ultkardex                 = InvKardex::where('id_inv_inventario', $row->id_inv_inventario)->orderBy('id', 'desc')->first();
                    $kardex                    = new InvKardex;
                    $kardex->id_inv_inventario = $row->id_inv_inventario;
                    $kardex->id_bodega         = $cab_mov->transaccion_bodega->id_bodega;
                    $kardex->descripcion       = $descripcion;
                    $kardex->referencia        = $referencia;
                    $kardex->id_producto       = $row->id_producto;
                    $kardex->tipo              = $cab_mov->documento_bodega->tipo_movimiento->tipo;
                    $kardex->fecha             = $cab_mov->fecha;
                    $kardex->cantidad          = $row->cantidad;
                    $kardex->cant_uso          = $row->cantidad;
                    if (isset($ultkardex->id)) {
                        if ($cab_mov->documento_bodega->tipo_movimiento->tipo == 'I' || $cab_mov->documento_bodega->tipo_movimiento->tipo == 'R' || $cab_mov->documento_bodega->tipo_movimiento->tipo == 'G') {
                            $cantidad_dd = $ultkardex->exist_cant;
                            $kardex->exist_cant += $ultkardex->exist_cant + $row->cantidad;
                            $kardex->exist_uso += $cantidad_dd+ $row->cantidad;
                        } else {
                            $cantidad_dd = $ultkardex->exist_cant;
                            $kardex->exist_cant += $ultkardex->exist_cant - $row->cantidad;
                            $kardex->exist_uso +=  $cantidad_dd - $row->cantidad;
                            if ($kardex->exist_cant < 0) {
                                $kardex->exist_cant = 0;
                            }
                            if ($kardex->exist_uso < 0) {
                                $kardex->exist_uso = 0;
                            }
                        }
                    } else {
                        $kardex->exist_cant = $row->cantidad;
                        $kardex->exist_uso  = $row->cant_uso;
                    }
                    $kardex->valor_unitario         = $row->valor_unitario;
                    $kardex->iva                    = $row->iva;
                    $kardex->total                  = $row->total;
                    $kardex->id_documento_bodega    = $cab_mov->id_documento_bodega;
                    $kardex->id_inv_det_movimientos = $row->id; //id_inv_det_movimientos
                    $kardex->estado                 = 1;
                    $kardex->id_empresa             = Auth::guest() ? '0993075000001' : Session::get('id_empresa');
                    $kardex->ip_creacion            = $ip_cliente;
                    $kardex->ip_modificacion        = $ip_cliente;
                    $kardex->id_usuariocrea         = $idusuario;
                    $kardex->id_usuariomod          = $idusuario;
                    $kardex->save();
                }
    
            }
            //ACTUALIZO INVENTARIO
            // InvCosto::movimientoCostoInventario($id_movimiento);//Este hay que cambiar por invinventario
            InvInventario::movimientoInventario($id_movimiento);
            InvInventarioSerie::movimientoInventarioSerie($id_movimiento);
            return ["status"=> true, "msj"=>"Se proceso correctamente", "msj_kardex"=>$kardex] ;
        }catch(\Exception $e){
            return ["status"=> false, "msj"=>"Ocurrio un error", "exp"=>$e->getMessage(), "mod"=>"InvKarex"] ;
        }
      
    }

    public static function reprocesaKardex($id_movimiento = null)
    {
        $kardex     = '[]';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
    }

}
