<?php

namespace Sis_medico\Http\Controllers\contable;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_productos;
use Sis_medico\Empresa;
use Session;
use Excel;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Bodegas;
use Sis_medico\ct_Detalle_Pedido;
use Sis_medico\Ct_Inv_Interno;
use Sis_medico\Ct_Inventario;
use Sis_medico\Ct_pedidos_Compra;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Inventario_Bodega;
use Sis_medico\Http\Requests\Request as RequestsRequest;

class KardexInternoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function index(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::find($id_empresa);
        $fecha_desde= $request->fecha_desde;
        $fecha_hasta= $request->fecha_hasta;
       // dd($fecha_desde, $fecha_hasta);
        if(is_null($fecha_hasta)){
            $fecha_hasta= date('Y-m-d');
        }
        if(is_null($fecha_desde)){
            $fecha_desde= date('Y-m-d');
        }

       // dd($fecha_desde, $fecha_hasta);
        $informe= Ct_Inv_Interno::where('id_empresa',$id_empresa);
        if($request->id_producto!=null){
            $informe= $informe->where('id_producto',$request->id_producto);
        }
        // if(is_null($fecha_desde)){
        //     $informe= $informe->with(['detalles' => function ($query) use($fecha_hasta)  {
        //         $query->where('fecha','<=',$fecha_hasta.' 23:59:59')->where('id_transaccion','<>',1);
        //     }]);
        // }
        if(!is_null($fecha_hasta) && !is_null($fecha_desde)){
            $informe= $informe->with(['detalles' => function ($query) use($fecha_hasta,$fecha_desde)  {
                $query->whereBetween('fecha',[$fecha_desde.' 00:00:00',$fecha_hasta.' 23:59:59'])->where('id_transaccion','<>',1);
            }]);
        }
        $productos= Ct_productos::where('id_empresa',$id_empresa)->get();
        $informe= $informe->get();
        return view('contable.kardex.index_kardex',['empresa'=>$empresa,'fecha_desde'=>$fecha_desde,'fecha_hasta'=>$fecha_hasta,'informe'=>$informe,'productos'=>$productos,'request'=>$request]);
    }   

    public function productosearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos  = [];
        if ($request['search'] != null) {
            $productos = Ct_productos::where('nombre', 'LIKE', '%' . $request['search'] . '%')
                        ->where('estado_tabla', '1')
                        ->where('id_empresa', $id_empresa)
                        ->select('ct_productos.id as id', 'ct_productos.nombre as text')->get();
        }

        return response()->json($productos);
    }

    public function pedidos(Request $request){
        $fecha_desde= $request->fecha_desde;
        $fecha_hasta= $request->fecha_hasta;
        if($fecha_hasta==null){
            $fecha_hasta= date('Y-m-d');
        }
        $id_empresa= $request->session()->get('id_empresa');
        $bodega= $request->id_bodega;
        $empresa= Empresa::find($id_empresa);
        /*$pedidos= Ct_pedidos_Compra::where('id_empresa',$id_empresa)->where('estado',1)->get(); */


        //$pedidos = ct_Detalle_Pedido::where('estado',1)->with(['cabecera' => function ($query) use($fecha_hasta,$fecha_desde,$id_empresa,$bodega)  {
        $pedidos= Ct_Inventario_Bodega::where('estado',1)->with(['cabecera' => function ($query) use($fecha_hasta,$fecha_desde,$id_empresa,$bodega)  {
            
            $query->where('id_empresa',$id_empresa);
            if(is_null($fecha_desde)){
                $query= $query->where('fecha','<=',$fecha_hasta);
            }else{
                $query=  $query->whereBetween('fecha',[$fecha_desde.' 00:00:00',$fecha_hasta.' 23:59:59']);
            }
            
        }]);
        if(!is_null($bodega)){
            $pedidos->where('bodega',$bodega);
        }
        $pedidos=$pedidos->get();
        
        $productos= Ct_productos::where('id_empresa',$id_empresa)->get();
        $bodegas= Ct_Bodegas::where('id_empresa',$id_empresa)->get();
        
        return view('contable.kardex.pedidos',['empresa'=>$empresa,'pedidos'=>$pedidos,'productos'=>$productos,'request'=>$request,'fecha_desde'=>$fecha_desde,'fecha_hasta'=>$fecha_hasta,'bodegas'=>$bodegas]);
    }

}