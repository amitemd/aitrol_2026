<?php

namespace Sis_medico\Http\Controllers\Nuevo_Insumos;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;

class KardexNuevoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7)) == false) {
            return true;
        }
    }


    public function index()
    {
        $query = DB::select(DB::raw(
            "select *,IFNULL(sum(movi.cantidad_in),0)-IFNULL(sum(movi.cantidad_eg),0) as saldo from ((select p.id,cp.codigo,cp.nombre as producto,sum(m.cantidad) as cantidad_in,0 as cantidad_eg,'I' as tipo
            from ct_productos as cp 
            left join producto as p on cp.codigo=p.codigo
            left join inv_det_movimientos as m on p.id=m.id_producto
            left join inv_cab_movimientos cm on m.id_inv_cab_movimientos=cm.id
            left join inv_documentos_bodegas db on cm.id_documento_bodega=db.id
            where cp.estado_tabla=1
            and COALESCE(db.id_inv_tipo_movimiento, 0) IN (0, 1)
            group by p.codigo)
            UNION ALL
            (select p.id,cp.codigo,cp.nombre as producto,0 as cantidad_in,sum(m.cantidad) as cantidad_eg,'E' as tipo
            from ct_productos as cp 
            left join producto as p on cp.codigo=p.codigo
            join inv_det_movimientos as m on p.id=m.id_producto
            left join inv_cab_movimientos cm on m.id_inv_cab_movimientos=cm.id
            left join inv_documentos_bodegas db on cm.id_documento_bodega=db.id
            where cp.estado_tabla=1
            and db.id_inv_tipo_movimiento<>1
            group by p.codigo)) as movi
            group by movi.codigo;"
        )); 

        return view('nuevo_modulo_kardex/index', ['query' => $query]);
    }
    public function detalle_kardex(Request $request)
    {
        try {
            $sql = "select *from (     
                (select k.fecha,ifnull(cm.id_asiento,pc.id_asiento_cabecera) as id_asiento_cabecera,cp.codigo,ma.nombre,cp.nombre as producto,m.serie,m.lote,m.fecha_vence,pe.pedido,b.nombre as bodega,k.descripcion,db.documento,m.cantidad,m.valor_unitario,
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
                and cp.codigo='$request->codigo')
                UNION ALL 
                (select k.fecha,ifnull(cm.id_asiento,pc.id_asiento_cabecera) as id_asiento_cabecera,cp.codigo,ma.nombre,cp.nombre as producto,
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
                and cp.codigo='$request->codigo')) as movi order BY movi.fecha;";
            /* echo $sql;
            exit;*/
            $query = DB::select(DB::raw($sql));


            return ['status' => true, 'query' => $query];
        } catch (\Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];

        }

    }
}
