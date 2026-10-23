<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Pedido;
use Sis_medico\Producto;

class UsosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    
    /************************************************
    **********LISTADO TIPO DE PAGO*******************
    /************************************************/
    public function getUses(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }   
        $fecha_dede= $request['fecha_desde'];
        $fecha_hasta= $request['fecha_hasta'];
        $tip_pago = Ct_Tipo_Pago::where('estado', '=', 1)->orderby('id', 'asc')->paginate(5);
        $getProducto= Pedido::join('movimiento as m','m.id_pedido','pedido.id')->join('producto as p','m.id_producto','p.id')->groupBy('pedido.pedido')->select('pedido.id as pedido','pedido.fecha','pedido.observaciones','pedido.total','pedido.subtotal_12','pedido.subtotal_0',DB::raw('COUNT(DISTINCT (m.id)) as cantidad'),'p.nombre','p.id as codigo','pedido.observaciones as observacion')->orderBy('pedido.fecha','ASC')->get()->toArray();
        //dd($detalles);
        $detalles= $this->group_by("nombre",$getProducto);

        //tengo que traerlo de todos los pedidos porque si voy por productos no funciona 12/02/2021 dont forget
         //return response()->json($detalles);
         return view('contable.kardex.estimados', ['estimados' => $detalles,'fecha_desde'=>$fecha_dede,'fecha_hasta'=>$fecha_hasta]); 
    }
    function group_by($key, $data)
    {
        $result = array();
        //result data or group by
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
    

}
