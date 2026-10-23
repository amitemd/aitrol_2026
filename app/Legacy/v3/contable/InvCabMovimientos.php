<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class InvCabMovimientos extends Model
{
    private static $modelo = 'Inv cabecera movimientos';

    protected $table = 'inv_cab_movimientos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';


    public function transaccion_bodega()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Invtransaccionesbodegas', 'id_transaccion_bodega', 'id');
    }

    public function documento_bodega()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Invdocumentosbodegas', 'id_documento_bodega', 'id');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\Invdetmovimientos', 'id_inv_cab_movimientos', 'id')->where('estado', 1);
    }


    public static function anularTransaccionBodega($id_cab_movimiento = null)
    {
        try {
            $cabcera       = InvCabMovimientos::find($id_cab_movimiento);
            $num_documento = $cabcera->numero_documento;
            $tipo_mov      = "";
            $id_bodega     = "";
            if ($cabcera->transaccion_bodega->documentoBodega->tipo_movimiento->tipo == 'I') {
                $tipo_mov  = "EGR";
                $id_bodega = $cabcera->id_bodega_destino;
            } else {
                $tipo_mov  = "ING";
                $id_bodega = $cabcera->id_bodega_origen;
            }
            $documento   = InvDocumentosBodegas::where('abreviatura_documento', $tipo_mov)->first();
            $secuencia   = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, $tipo_mov);
            if (!$secuencia["status"]) {
                throw new Exception($secuencia["message"]);
            }
            $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                ->where('id_bodega', $id_bodega)
                ->first();
            $cab_mov_inv                        = new InvCabMovimientos;
            $cab_mov_inv->id_documento_bodega   = $documento->id;
            $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
            $cab_mov_inv->id_bodega_origen      = $id_bodega;
            $cab_mov_inv->numero_documento = str_pad($secuencia["msj"], 9, "0", STR_PAD_LEFT);
            $cab_mov_inv->observacion      = 'ANULACION DOC: ' . strtoupper($cabcera->documento_bodega->abreviatura_documento) . "-" . $cabcera->numero_documento;
            $cab_mov_inv->fecha            = date('Y-m-d');
            $cab_mov_inv->num_doc_ext      = $cabcera->pedido;
            $cab_mov_inv->num_doc_cont     = $cabcera->factura;
            $cab_mov_inv->descuento        = $cabcera->descuento;
            $cab_mov_inv->subtotal         = $cabcera->subtotal_12;
            $cab_mov_inv->subtotal_0       = $cabcera->subtotal_0;
            $cab_mov_inv->iva              = $cabcera->iva;
            $cab_mov_inv->total            = $cabcera->total;
            $cab_mov_inv->id_docum_origen  = $id_cab_movimiento;
            $cab_mov_inv->id_empresa       = $cabcera->id_empresa;
            $cab_mov_inv->ip_creacion      = $cabcera->ip_creacion;
            $cab_mov_inv->ip_modificacion  = $cabcera->ip_modificacion;
            $cab_mov_inv->id_usuariocrea   = $cabcera->id_usuariocrea;
            $cab_mov_inv->id_usuariomod    = $cabcera->id_usuariomod;
            $cab_mov_inv->save();

            foreach ($cabcera->detalles as $detalle) {
                $det_mov_inv                         = new InvDetMovimientos;
                $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                $det_mov_inv->id_producto            = $detalle->id_producto;
                $det_mov_inv->id_inv_inventario      = $detalle->id_inv_inventario;
                $det_mov_inv->serie                  = $detalle->serie;
                $det_mov_inv->lote                   = $detalle->lote;
                $det_mov_inv->fecha_vence            = $detalle->fecha_vence;
                $det_mov_inv->cantidad               = $detalle->cantidad;
                $det_mov_inv->cant_uso               = $detalle->cant_uso;
                $det_mov_inv->valor_unitario         = $detalle->valor_unitario;
                $det_mov_inv->subtotal               = $detalle->subtotal;
                $det_mov_inv->descuento              = $detalle->descuento;
                $det_mov_inv->iva                    = $detalle->iva;
                $det_mov_inv->total                  = $detalle->total;
                $det_mov_inv->motivo                 = 'ANULACION ' . $num_documento;
                $det_mov_inv->id_detalle_origen      = $detalle->id;
                $det_mov_inv->ip_creacion            = $detalle->ip_creacion;
                $det_mov_inv->ip_modificacion        = $detalle->ip_modificacion;
                $det_mov_inv->id_usuariocrea         = $detalle->id_usuariocrea;
                $det_mov_inv->id_usuariomod          = $detalle->id_usuariomod;
                $det_mov_inv->save();
            }
            $kardex = InvKardex::setKardex($cab_mov_inv->id);
            if (!$kardex["status"]) {
                throw new Exception($secuencia["message"]);
            }
            InvCabMovimientos::logAnulacion($cabcera->id, $cab_mov_inv->id);
            return ["status" => true, "code" => 200, "msj" => "ok"];
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


    public static function logAnulacion($id_ant, $id_new)
    {
        $user = Auth::user();
        $ip_usuario = $_SERVER["REMOTE_ADDR"];
        $data = [
            "id_cab_anterior" => $id_ant,
            "id_cab_new" => $id_new,
            "id_usuariomod" => $user->id,
            "id_usuariocrea" => $user->id,
            "ip_creacion" => $ip_usuario,
            "ip_modificacion" => $ip_usuario,
        ];
        AnulacionCabMov::insert($data);
    }

    public static function calcularTotalCabMovimiento($id)
    {
        try {
            $cab_mov_inv = InvCabMovimientos::find($id);
            if (isset($cab_mov_inv)) {
                $subt  = $cab_mov_inv->detalles->sum('subtotal');
                $desc  = $cab_mov_inv->detalles->sum('descuento');
                $iva   = $cab_mov_inv->detalles->sum('iva');
                $total = $cab_mov_inv->detalles->sum('total');

                $cab_mov_inv->descuento  = $desc;
                $cab_mov_inv->subtotal   = $subt;
                $cab_mov_inv->subtotal_0 = 0;
                $cab_mov_inv->iva        = $iva;
                $cab_mov_inv->total      = $total;
                $cab_mov_inv->save();
                return ['status' => true, "msj" =>"", 'linea' => ""];
            } else {
                return ['status' => false, "msj" => "error", 'linea' => ""];
            }
            
        } catch (Exception $r) {
            return ['status' => false, "msj" => $r->getMessage(), 'linea' => $r->getLine()];
        }
    }
}
