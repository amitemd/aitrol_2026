<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sis_medico\InvKardex;
use Sis_medico\InvInventarioSerie;

class InvDetMovimientos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_det_movimientos';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\InvCabMovimientos', 'id_inv_cab_movimientos', 'id');
    }
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'id_producto', 'id');
    }
    public function inventario()
    {
        return $this->belongsTo('Sis_medico\InvInventario', 'id_inv_inventario', 'id');
    }
    public function kardex()
    {
        return $this->belongsTo('Sis_medico\InvKardex', 'id', 'id_inv_det_movimientos');
    }
    public function pedido()
    {
        return $this->belongsTo('Sis_medico\Pedido', 'id_pedido');
    }
    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }
    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }
    public static function actualizarDetalleMovimiento($det_mov_inv) # TRASLADO
    {
        // $det_mov_inv                = InvDetMovimientos::find($id_detalle);
        if (isset($det_mov_inv->id)) {
            $kardex = InvKardex::where('id_inv_det_movimientos', $det_mov_inv->id)->where('estado', 1)->first();
            # diferencia
            // if ($kardex==null)  
            //     dd($id_detalle);
            $diff = $kardex->cantidad - $det_mov_inv->cantidad;
            # actualizar kardex
            $kardex->cantidad = $det_mov_inv->cantidad;
            $kardex->cant_uso = $det_mov_inv->cant_uso;
            $kardex->valor_unitario = $det_mov_inv->valor_unitario;
            $kardex->save();
            # actualizar inventario
            $cab_mov_inv = $det_mov_inv->cabecera;
            $inventario = $det_mov_inv->inventario;
            $inv_serie = InvInventarioSerie::where('serie', $det_mov_inv->serie)
                ->where('id_inv_inventario', $inventario->id)
                ->where('estado', 1)
                ->first();
            if ($diff > 0) {
                $inventario->existencia += abs($diff);
                $inventario->existencia_uso += ($det_mov_inv->producto->usos * abs($diff));
                if (isset($inv_serie->id)) {
                    $inv_serie->existencia += abs($diff);
                    $inv_serie->existencia_uso += ($det_mov_inv->producto->usos * abs($diff));
                    $inv_serie->save();
                }
            } else {
                # CALCULO LA EXISTENCIA DESCONTANDO TODOS LOS USOS
                $inventario->existencia_uso -= abs($diff);
                $exitencia = ceil($inventario->existencia_uso / $det_mov_inv->producto->usos);
                $inventario->existencia = $exitencia;
                if (isset($inv_serie->id)) {
                    $inv_serie->existencia_uso -= abs($diff);
                    $exitencia = ceil($inv_serie->existencia_uso / $det_mov_inv->producto->usos);
                    $inv_serie->existencia = $exitencia;
                    $inv_serie->save();
                }
            }
            if ($inventario->existencia < 0) {
                $inventario->existencia = 0;
            }
            if ($inventario->existencia_uso < 0) {
                $inventario->existencia_uso = 0;
            }
            $inventario->save();
            ## detalle del ingreso por traslado ##
            $det_ing_tra                = InvDetMovimientos::where('id_detalle_origen', $det_mov_inv->id)
                ->where('estado', 1)
                ->first();
            if ($det_ing_tra->id) {
                $det_ing_tra->cantidad = $det_mov_inv->cantidad;
                $det_ing_tra->cant_uso = $det_mov_inv->cant_uso;
                $det_ing_tra->subtotal = $det_mov_inv->subtotal;
                $det_ing_tra->descuento = $det_mov_inv->descuento;
                $det_ing_tra->iva = $det_mov_inv->iva;
                $det_ing_tra->total = $det_mov_inv->total;
                $det_ing_tra->ip_modificacion = $det_mov_inv->ip_modificacion;
                $det_ing_tra->id_usuariomod = $det_mov_inv->id_usuariomod;
                $det_ing_tra->save();
                $kardex = InvKardex::where('id_inv_det_movimientos', $det_ing_tra->id)->where('estado', 1)->first();
                # actualizar kardex
                $kardex->cantidad = $det_ing_tra->cantidad;
                $kardex->cant_uso = $det_ing_tra->cant_uso;
                $kardex->valor_unitario = $det_ing_tra->valor_unitario;
                $kardex->save();
                # actualizar inventario
                $inventario_ing = $det_ing_tra->inventario;
                $inv_serie_ing = InvInventarioSerie::where('serie', $det_ing_tra->serie)
                    ->where('id_inv_inventario', $inventario_ing->id)
                    ->where('estado', 1)
                    ->first();
                if ($diff < 0) {
                    $inventario_ing->existencia += abs($diff);
                    $inventario_ing->existencia_uso += ($det_ing_tra->producto->usos * abs($diff));
                    if (isset($inv_serie_ing->id)) {
                        $inv_serie_ing->existencia += abs($diff);
                        $inv_serie_ing->existencia_uso  += ($det_mov_inv->producto->usos * abs($diff));
                        $inv_serie_ing->save();
                    }
                } else {
                    # CALCULO LA EXISTENCIA DESCONTANDO TODOS LOS USOS
                    $inventario_ing->existencia_uso -= abs($diff);
                    $exitencia = ceil($inventario_ing->existencia_uso / $det_ing_tra->producto->usos);
                    $inventario_ing->existencia = $exitencia;
                    if (isset($inv_serie_ing->id)) {
                        $inv_serie_ing->existencia_uso -= abs($diff);
                        $exitencia = ceil($inv_serie_ing->existencia_uso / $det_mov_inv->producto->usos);
                        $inv_serie_ing->existencia = $exitencia;
                        $inv_serie_ing->save();
                    }
                }
                // dd($inventario_ing);
                if ($inventario_ing->existencia < 0) {
                    $inventario_ing->existencia = 0;
                }
                if ($inventario_ing->existencia_uso < 0) {
                    $inventario_ing->existencia_uso = 0;
                }
                $inventario_ing->save();
            }
        }
    }
    public static function ingresarDetalleMovimientoTransito($cabcera, $detalle, $cab_origen) # TRASLADO
    {
        if (isset($cabcera->documento_bodega->id) and $cabcera->documento_bodega->tipo != '') {
            $tipo = $cabcera->documento_bodega->tipo;
        } else {
            $tipo = 'C';
        }
        $inventario = InvInventario::getInventario($detalle->id_producto, $cabcera->id_bodega_destino);
        if (!isset($inventario->id)) {
            $inventario = InvInventario::setNeoInventario($detalle->id_producto, $cabcera->id_bodega_destino, 0, 0);
        }
        ##       creo los detalles del traslado
        $det_mov_inv = new InvDetMovimientos;
        $det_mov_inv->id_inv_cab_movimientos = $cabcera->id;
        $det_mov_inv->id_producto = $detalle->id_producto;
        $det_mov_inv->serie = $detalle->serie;
        $det_mov_inv->lote = $detalle->lote;
        $det_mov_inv->fecha_vence = $detalle->fecha_vence;
        $det_mov_inv->serie = $detalle->serie;
        $det_mov_inv->id_inv_inventario = $inventario->id;
        $det_mov_inv->cantidad = $detalle->cantidad;
        $det_mov_inv->cant_uso = $detalle->cant_uso;
        $det_mov_inv->valor_unitario = $detalle->valor_unitario;
        $det_mov_inv->subtotal = $detalle->subtotal;
        $det_mov_inv->descuento = $detalle->descuento;
        $det_mov_inv->iva = $detalle->iva;
        $det_mov_inv->total = $detalle->total;
        $det_mov_inv->motivo = 'INGRESO TRASLADO PEDIDO ' . $cab_origen->numero_documento;
        $det_mov_inv->id_detalle_origen = $detalle->id;
        $det_mov_inv->ip_creacion = $detalle->ip_creacion;
        $det_mov_inv->ip_modificacion = $detalle->ip_modificacion;
        $det_mov_inv->id_usuariocrea = $detalle->id_usuariocrea;
        $det_mov_inv->id_usuariomod = $detalle->id_usuariomod;
        $det_mov_inv->save();
        # registar kardex y registra inventario
        $kardex = InvKardex::setKardex($cabcera->id);
    }
    public static function proveedorDetalle($serie)
    {
        $query = DB::select(DB::raw("
            select pr.*
            from inv_det_movimientos  idm
            INNER JOIN pedido p on idm.id_pedido = p.id
            INNER JOIN proveedor pr ON pr.id = p.id_proveedor 
            where idm.serie like '{$serie}' AND id_pedido is not NULL
            LIMIT 1 OFFSET 0
        "));
        return $query;
    }
}
