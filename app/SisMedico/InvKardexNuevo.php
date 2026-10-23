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

class InvKardexNuevo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string

     */
    //use SoftDeletes;
    protected $table = 'inv_kardex_nuevo';

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

    public static function setKardexNuevo($id_movimiento = null)
    {
        $kardex     = '[]';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_mov = InvCabMovimientos::find($id_movimiento);
        foreach ($cab_mov->detalles as $row) {
            // OBTENGO EL ULTIMO REGISTRO DEL KARDEX
            if($row->cantidad_usos_kardex > 0 ){
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
                if (str_contains($cab_mov->observacion, 'ANULACION')) {
                    $referencia = $cab_mov->observacion;
                }
                if ($cab_mov->observacion == '') {
                    $referencia = $cab_mov->observacion;
                }
                
                $observacion_elimina = '';$estado = 1;
                $pedido = InvDetMovimientos::where('serie',$row->serie)->whereNotNull('id_pedido')->first();
                if($pedido == null){
                    $estado = 0;$observacion_elimina = 'No encontró detalle del movimiento';
                }
                $tipo_ingreso = '';
                $vt_id_bodega = $cab_mov->id_bodega_destino;
                $vt_documento_bodega = $cab_mov->id_documento_bodega;
                if($cab_mov->documento_bodega->abreviatura_documento == 'CIN'){
                    $tipo_ingreso = 'T';
                    $vt_id_bodega = $cab_mov->id_bodega_origen;
                    $vt_documento_bodega = '4';
                }else{
                    $tipo_ingreso = $cab_mov->documento_bodega->tipo_movimiento->tipo;
                }
                
                $kardex                    = new InvKardexNuevo;
                $kardex->id_inv_inventario = $row->id_inv_inventario;
                //$kardex->id_bodega         = $cab_mov->id_bodega_destino;
                $kardex->id_bodega         = $vt_id_bodega;
                $kardex->descripcion       = $descripcion;
                $kardex->referencia        = $referencia;
                $kardex->serie             = $row->serie;
                $kardex->id_producto       = $row->id_producto;
                $kardex->tipo              = $tipo_ingreso;
                $kardex->fecha             = $cab_mov->fecha;
                $kardex->cantidad          = $row->cantidad_usos_kardex;
            
                $nuevo_valor_unitario = 0;
                if($row->cantidad_usos_kardex == 0){
                    
                    $estado = 0;$observacion_elimina = 'Usos en cero';        
            
                }else{
                    $nuevo_valor_unitario = $pedido->valor_unitario / $row->cantidad_usos_kardex;
                }

                $nuevo_total = $nuevo_valor_unitario * $row->cantidad_usos_kardex;
                $kardex->valor_unitario         = $nuevo_valor_unitario;
                $kardex->total                  = $nuevo_total;
                $kardex->id_documento_bodega    = $vt_documento_bodega;
                $kardex->id_inv_det_movimientos = $row->id; //id_inv_det_movimientos
                $kardex->observacion_elimina    = $observacion_elimina;
                $kardex->estado                 = $estado;
                $kardex->id_empresa             = Session::get('id_empresa');
                $kardex->ip_creacion            = $ip_cliente;
                $kardex->ip_modificacion        = $ip_cliente;
                $kardex->id_usuariocrea         = $idusuario;
                $kardex->id_usuariomod          = $idusuario;
                $kardex->save();
            }
        }
        
        return $kardex;
    }

    public static function reprocesaKardex($id_movimiento = null)
    {
        $kardex     = '[]';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
    }

}
