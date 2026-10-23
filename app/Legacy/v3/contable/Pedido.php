<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Pedido extends Model
{
    private static $modelo = 'Pedido';
    protected $table = 'pedido';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function proveedor()
    {
        return $this->belongsto('Sis_medico\v3\contable\Proveedor', 'id_proveedor');
    }

    public static function getPedidoModal()
    {
        $documentoBodega = Invdocumentosbodegas::where('abreviatura_documento', 'ICG')->first();
        $pedidos = Pedido::join('proveedor', 'proveedor.id', '=', 'pedido.id_proveedor')
            ->join('users', 'users.id', '=', 'pedido.id_usuariocrea')
            ->join('inv_documentos_bodegas', 'inv_documentos_bodegas.id', '=', 'pedido.tipo')
            ->whereIn('pedido.tipo', [3, $documentoBodega->id])
            ->where('pedido.id_empresa', session("id_empresa"))
            ->whereNull('pedido.deleted_at')
            ->where('pedido.estado_contable', 0)
            ->select('pedido.*', 'users.nombre1', 'users.apellido1', 'proveedor.nombrecomercial');
        $ordenes = Pedido::join('proveedor', 'proveedor.id', '=', 'pedido.id_proveedor')
            ->join('users', 'users.id', '=', 'pedido.id_usuariocrea')
            ->where('pedido.id_empresa', session("id_empresa"))
            ->where('pedido.tipo', '=', 3)
            ->where('pedido.estado_contable', 0)
            ->whereNull('pedido.deleted_at')
            ->union($pedidos)
            ->select('pedido.*', 'users.nombre1', 'users.apellido1', 'proveedor.nombrecomercial')
            ->get();

        /*$html = '';
        foreach ($ordenes as $value) {
            $html .= '<tr>
                        <td>' . $value->created_at . '</td>
                        <td>' . $value->nombrecomercial . '</td>
                        <td>' . $value->pedido . '</td>
                        <td>' . $value->factura . '</td>
                        <td>' . $value->nombre1 . ' ' . $value->apellido1 . '</td>
                        <td> Conglomerado </td>
                        <td><button type="button" class="btn btn-info btn-gray" onclick="addPedido(\'' . $value->pedido . '\')">Agregar</button>
                    </tr>';
        }*/
        return $ordenes;
    }
    public static function getPedido($id = '')
    {
        if ($id == '') {
            /* return Pedido::leftjoin('proveedor as pr','pedido.id_proveedor','=','pr.id')
            ->join('users as u', 'u.id', '=', 'pedido.id_usuariocrea')
            ->join('inv_documentos_bodegas as idb', 'idb.id', '=', 'pedido.tipo')
            ->where('idb.abreviatura_documento', 'ICG')
            ->where('pedido.estado_contable', 0)
            ->where('pedido.id_empresa', Session::get('id_empresa'))
            ->whereNull('pedido.deleted_at')
            ->select('pedido.id','pedido.id_proveedor','pedido.pedido','pedido.fecha','pedido.vencimiento','pedido.factura',
            'pedido.created_at','pedido.observaciones','pedido.subtotal_0','pedido.subtotal_12','pedido.iva',
            'pedido.descuento','pedido.total','u.nombre1','u.apellido1','pr.nombrecomercial')
            ->get([
                'pedido.id',
                'pedido.id_proveedor',
                'pedido.pedido',
                'pedido.fecha',
                'pedido.vencimiento',
                'pedido.factura',
                'pedido.observaciones',
                'pedido.subtotal_0',
                'pedido.subtotal_12',
                'pedido.iva',
                'pedido.descuento',
                'pedido.total',
                'pedido.created_at',
                'u.nombre1',
                'u.apellido1',
                'pr.nombrecomercial',
            ]); */

            return Pedido::join('proveedor as prov', 'pedido.id_proveedor', '=', 'prov.id')
                ->join('inv_documentos_bodegas as d', 'pedido.tipo', '=', 'd.id')
                ->join('movimiento as mo', 'pedido.id', '=', 'mo.id_pedido')
                ->join('bodega as b', 'b.id', '=', 'mo.id_bodega')
                ->leftjoin('union_bodega as ub', 'ub.id_bodega_compra', '=', 'b.id')
                ->leftjoin('ct_bodegas as cb', 'cb.id', '=', 'ub.id_bodega_cont')
                ->join('users as u', 'pedido.id_usuariocrea', '=', 'u.id')
                ->leftJoin('producto as pr', 'mo.id_producto', '=', 'pr.id')
                ->leftJoin('ct_productos_insumos as pin', 'pr.id', '=', 'pin.id_insumo')
                ->join('ct_productos as cp', 'pin.id_producto', '=', 'cp.id')
                ->where('mo.estado', 1)
                ->groupBy('pr.codigo', 'mo.serie', 'mo.precio')
                ->select(
                    'mo.id AS cantidad',
                    'pedido.id',
                    'cp.codigo',
                    'cp.nombre',
                    'cp.id as producto_id',
                    'pedido.id_proveedor',
                    'pedido.pedido',
                    'pedido.tipo',
                    'd.documento',
                    'd.abreviatura_documento',
                    'pedido.fecha',
                    DB::raw('sum(mo.cantidad) AS cantidad_total'),
                    'mo.precio',
                    'mo.id_producto',
                    'mo.serie',
                    'mo.subtotal_0',
                    'mo.subtotal_12',
                    'mo.descuento',
                    'mo.iva',
                    'pedido.vencimiento',
                    'pedido.orden_conglomerada',
                    'prov.razonsocial',
                    'prov.id_cuentas',
                    'pin.codigo_producto',
                    'mo.id_bodega',
                    'cp.iva as is_iva',
                    'prov.direccion',
                    'pedido.id_empresa',
                    'mo.descuento as descuentovalor',
                    'mo.descuentop as descuentoporct',
                    'pedido.created_at',
                    'prov.nombrecomercial',
                    'pedido.factura',
                    'u.nombre1',
                    'u.apellido1',
                    'cb.nombre as nombre_bodega',
                    'cb.id as id_bodega'
                )
                ->get();
        } else {
            /* return Pedido::leftjoin('proveedor as pr','pedido.id_proveedor','=','pr.id')
            ->join('users as u', 'u.id', '=', 'pedido.id_usuariocrea')
            ->join('inv_documentos_bodegas as idb', 'idb.id', '=', 'pedido.tipo')
            ->where('pedido.pedido', $id)
            ->where('idb.abreviatura_documento', 'ICG')
            ->where('pedido.estado_contable', 0)
            ->where('pedido.id_empresa', Session::get('id_empresa'))
            ->whereNull('pedido.deleted_at')
            ->select('pedido.id','pedido.id_proveedor','pedido.pedido','pedido.fecha','pedido.vencimiento','pedido.factura',
            'pedido.created_at','pedido.observaciones','pedido.subtotal_0','pedido.subtotal_12','pedido.iva',
            'pedido.descuento','pedido.total','u.nombre1','u.apellido1','pr.nombrecomercial')
            ->get([
                'pedido.id',
                'pedido.id_proveedor',
                'pedido.pedido',
                'pedido.fecha',
                'pedido.vencimiento',
                'pedido.factura',
                'pedido.observaciones',
                'pedido.subtotal_0',
                'pedido.subtotal_12',
                'pedido.iva',
                'pedido.descuento',
                'pedido.total',
                'pedido.created_at',
                'u.nombre1',
                'u.apellido1',
                'pr.nombrecomercial',
            ]); */
            return Pedido::join('proveedor as prov', 'pedido.id_proveedor', '=', 'prov.id')
                ->join('inv_documentos_bodegas as d', 'pedido.tipo', '=', 'd.id')
                ->join('movimiento as mo', 'pedido.id', '=', 'mo.id_pedido')
                ->join('bodega as b', 'b.id', '=', 'mo.id_bodega')
                ->leftjoin('union_bodega as ub', 'ub.id_bodega_compra', '=', 'b.id')
                ->leftjoin('ct_bodegas as cb', 'cb.id', '=', 'ub.id_bodega_cont')
                ->join('users as u', 'pedido.id_usuariocrea', '=', 'u.id')
                ->leftJoin('producto as pr', 'mo.id_producto', '=', 'pr.id')
                ->leftJoin('ct_productos_insumos as pin', 'pr.id', '=', 'pin.id_insumo')
                ->join('ct_productos as cp', 'pin.id_producto', '=', 'cp.id')
                ->where('pedido', $id)
                ->where('mo.estado', 1)
                ->groupBy('pr.codigo', 'mo.serie', 'mo.precio')
                ->select(
                    'mo.id AS cantidad',
                    'pedido.id',
                    'cp.codigo',
                    'cp.nombre',
                    'cp.id as producto_id',
                    'pedido.id_proveedor',
                    'pedido.pedido',
                    'pedido.tipo',
                    'd.documento',
                    'd.abreviatura_documento',
                    'pedido.fecha',
                    DB::raw('sum(mo.cantidad) AS cantidad_total'),
                    'mo.precio',
                    'mo.id_producto',
                    'mo.serie',
                    'mo.subtotal_0',
                    'mo.subtotal_12',
                    'mo.descuento',
                    'mo.iva',
                    'pedido.vencimiento',
                    'pedido.orden_conglomerada',
                    'prov.razonsocial',
                    'prov.id_cuentas',
                    'pin.codigo_producto',
                    'mo.id_bodega',
                    'cp.iva as is_iva',
                    'prov.direccion',
                    'pedido.id_empresa',
                    'mo.descuento as descuentovalor',
                    'mo.descuentop as descuentoporct',
                    'pedido.created_at',
                    'prov.nombrecomercial',
                    'pedido.factura',
                    'u.nombre1',
                    'u.apellido1',
                    'cb.nombre as nombre_bodega',
                    'cb.id as id_bodega'
                )
                ->get();
        }
    }
}
