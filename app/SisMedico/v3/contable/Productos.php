<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Productos extends Model
{
    protected $table = 'ct_productos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getPrecios($r)
    {
        $producto_id = $r->id;
        $id_seguro   = $r->id_seguro;
        $id_nivel    = $r->id_nivel;
        $precios = PrecioProducto::where('codigo_producto', $producto_id)
            ->where('estado', 1)
            ->get();
        return $precios;

        $inf_prod_tar = ProductosTarifario::where('id_producto', $producto_id)
            ->where('id_seguro', $id_seguro)
            ->where('nivel', $id_nivel)
            ->where('estado', 1)
            ->first();

        return $inf_prod_tar;
    }
    public static function getProductos($id = '')
    {
        if ($id == '') {
            return Productos::selectRaw(
                'ct_productos.codigo,
                ct_productos.nombre,
                ct_productos.descripcion,
                IFNULL(ct_productos.stock_minimo,0) as stock_minimo,
                m.nombre as marca,
                ct_productos.estado_tabla'
            )
                ->leftjoin('producto as p', 'ct_productos.codigo', 'p.codigo')
                ->leftjoin('marca as m', 'p.id_marca', 'm.id')
                ->get();
        } else {
            return Productos::where('codigo', $id)->first();
        }
    }
    public static function getGastoCombo($term)
    {
        return PlanCuentas::selectRaw('CONCAT(id_plan,"|",IFNULL(plan,""),"|",IFNULL(nombre,""),"|",0,"|",0) as id, CONCAT(plan," | ",nombre) as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('plan', 'like', '%' . $term . '%');
            })
            ->where('id_empresa', session('id_empresa'))
            ->where('estado', 2)
            ->get();
    }
    public static function getProductoCombo($term)
    {
        
        return Productos::selectRaw('CONCAT(IFNULL(id,""),"|",IFNULL(codigo,""),"|",IFNULL(nombre,""),"|",IFNULL(iva,0),"|",tipo_producto) as id, CONCAT(codigo," | ",nombre) as text')
            ->where(function ($q) use ($term) {

                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('codigo', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })
            ->where('id_empresa',session('id_empresa'))
            ->where('estado_tabla', 1)
            ->get();
    }
    public static function getPaqueteCombo($term)
    {
        return Productos::select('id', 'codigo', 'nombre as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('codigo', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })
            ->where('estado_tabla', 1)
            ->get();
    }
    public static function getMovimientos($codigo)
    {
        $codigo = ltrim($codigo);
        $sql = "select *from (     
            (select k.fecha,ifnull(cm.id_asiento,pc.id_asiento_cabecera) as id_asiento_cabecera,cp.codigo,ma.nombre as marca,cp.nombre as producto,m.serie,m.lote,m.fecha_vence,pe.pedido,b.nombre as bodega,k.descripcion,db.documento,m.cantidad,m.valor_unitario,
            m.iva,m.subtotal,m.total,'I' as tipo, pc.id as id_planilla, pc.id_hc_procedimiento as id_hc_procedimiento, cm.id_documento_bodega, cm.id id_cab_mov
            from ct_productos cp
            join producto p on cp.codigo=p.codigo
            left join inv_det_movimientos m on p.id=m.id_producto
            left join inv_kardex k on m.id=k.id_inv_det_movimientos
            join bodega b on k.id_bodega=b.id
            left join marca ma on p.id_marca=ma.id
            left join pedido pe on m.id_pedido=pe.id
            left join inv_cab_movimientos cm on m.id_inv_cab_movimientos=cm.id
            left join inv_documentos_bodegas db on cm.id_documento_bodega=db.id
            left join planilla_detalle pd on m.serie=pd.serie and m.id_movimiento_paciente=pd.id_movimiento_paciente
            left join planilla_cabecera pc on pd.id_planilla_cabecera=pc.id
            where COALESCE(db.id_inv_tipo_movimiento, 0) IN (0, 1)
            and cp.codigo='$codigo')
            UNION ALL 
            (select k.fecha,ifnull(cm.id_asiento,pc.id_asiento_cabecera) as id_asiento_cabecera,cp.codigo,ma.nombre as marca,cp.nombre as producto,
            m.serie,m.lote,m.fecha_vence,pe.pedido,b.nombre as bodega,k.descripcion,db.documento,m.cantidad,m.valor_unitario,
            m.iva,m.subtotal,m.total,'E' as tipo, pc.id as id_planilla, pc.id_hc_procedimiento as id_hc_procedimiento, cm.id_documento_bodega, cm.id id_cab_mov
            from ct_productos cp
            join producto p on cp.codigo=p.codigo
            left join inv_det_movimientos m on p.id=m.id_producto
            left join inv_kardex k on m.id=k.id_inv_det_movimientos
            join bodega b on k.id_bodega=b.id
            left join marca ma on p.id_marca=ma.id
            left join pedido pe on m.id_pedido=pe.id
            left join inv_cab_movimientos cm on m.id_inv_cab_movimientos=cm.id
            left join inv_documentos_bodegas db on cm.id_documento_bodega=db.id
            left join planilla_detalle pd on m.serie=pd.serie and m.id_movimiento_paciente=pd.id_movimiento_paciente
            left join planilla_cabecera pc on pd.id_planilla_cabecera=pc.id
            where db.id_inv_tipo_movimiento<>1
            and cp.codigo='$codigo')) as movi order BY movi.fecha;";
        return DB::select(DB::raw($sql));


        return Productos::selectRaw('ct_productos.id,ct_productos.codigo,ct_productos.nombre,ct_productos.codigo_barra,ct_productos.descripcion')
            ->where('ct_productos.codigo', ltrim($codigo))
            ->leftjoin('ct_productos_insumos as pi', 'ct_productos.id', 'pi.id_producto')
            ->leftjoin('producto as p', 'pi.id_insumo', 'p.id')
            ->leftjoin('inv_det_movimientos as m', 'p.id', 'm.id_producto')
            ->leftjoin('inv_kardex as k', 'm.id', 'k.id_inv_det_movimientos')
            ->get();

        return Productos::select(
            'k.fecha',
            Productos::raw('IFNULL(cm.id_asiento, pc.id_asiento_cabecera) as id_asiento_cabecera'),
            'cp.codigo',
            'ma.nombre',
            'cp.nombre as nombre_contable',
            'm.serie',
            'm.lote',
            'm.fecha_vence',
            'pe.pedido',
            'b.nombre as bodega',
            'k.descripcion',
            'db.documento',
            'm.cantidad',
            'm.valor_unitario',
            'm.iva',
            'm.subtotal',
            'm.total',
            Productos::raw('"I" as tipo'),
            'pc.id as id_planilla',
            'pc.id_hc_procedimiento as id_hc_procedimiento',
            'cm.id_documento_bodega',
            'cm.id as id_cab_mov'
        )
            ->join('producto as p', 'cp.codigo', 'p.codigo')
            ->leftJoin('inv_det_movimientos as m', 'p.id', 'm.id_producto')
            ->leftJoin('inv_kardex as k', 'm.id', 'k.id_inv_det_movimientos')
            ->join('bodega as b', 'k.id_bodega', 'b.id')
            ->leftJoin('marca as ma', 'p.id_marca', 'ma.id')
            ->leftJoin('pedido as pe', 'm.id_pedido', 'pe.id')
            ->leftJoin('inv_cab_movimientos as cm', 'm.id_inv_cab_movimientos', 'cm.id')
            ->leftJoin('inv_documentos_bodegas as db', 'cm.id_documento_bodega', 'db.id')
            ->leftJoin('planilla_detalle as pd', function ($join) {
                $join->on('m.serie', 'pd.serie')
                    ->on('m.id_movimiento_paciente', 'pd.id_movimiento_paciente');
            })
            ->leftJoin('planilla_cabecera as pc', 'pd.id_planilla_cabecera', 'pc.id')
            ->where(function ($query) use ($codigo) {
                $query->where(Productos::raw('COALESCE(db.id_inv_tipo_movimiento, 0)'), 'IN', [0, 1])
                    ->orWhere('db.id_inv_tipo_movimiento', '<>', 1);
            })
            ->where('cp.codigo', $codigo)
            ->orderBy('k.fecha')
            ->get();
    }
}
