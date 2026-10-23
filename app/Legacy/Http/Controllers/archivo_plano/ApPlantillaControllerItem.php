<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApProcedimiento;
use Sis_medico\ApProcedimientoNivel;


class ApProcedimientosController extends Controller
{
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        return view('archivo_plano/procedimientos/frmcrear');
    }

    public function index(Request $request){
        $procedimiento = $request->input('descripcion');

        $procedimientos = ApProcedimiento::join('ap_procedimiento_nivel', 'ap_procedimiento.id', '=', 'ap_procedimiento_nivel.id_procedimiento')
            ->select('ap_procedimiento.tipo','ap_procedimiento.codigo','ap_procedimiento.descripcion','ap_procedimiento_nivel.cod_conv',
                'ap_procedimiento_nivel.prc1')
            ->where('descripcion', 'LIKE', '%' . $procedimiento . '%')
            ->paginate(5);

            $procedimientos->appends(['search' => $procedimiento]);
        return view('archivo_plano/procedimientos/index', compact('procedimientos'));
    }


    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $pro = new ApProcedimiento();
        $pro->tipo = $request->txttipo;
        $pro->codigo = $request->txtcodigo;
        $pro->descripcion = $request->txtdescripcion;
        $pro->estado = $request->txtestado;
        $pro->id_usuariocrea = $idusuario;
        $pro->id_usuariomod = $idusuario;
        $pro->ip_creacion = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente; 
        $pro->save();
        return redirect('administracion/procedimientos');
    }





//create nivel
    public function store2(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $pro = new ApProcedimientoNivel();
        $pro->id_procedimiento = $request->id_procedimiento;
        $pro->cod_conv = $request->txtnivel;
        $pro->desc_conv = $request->txtdes;
        $pro->anexo = $request->txtanexo;
        $pro->uvr1 = $request->uvr1;
        $pro->uvr2 = $request->uvr2;
        $pro->uvr3 = $request->uvr3;
        $pro->prc1 = $request->prc1;
        $pro->prc2 = $request->prc2;
        $pro->prc3 = $request->prc3;
        $pro->uvr1a = $request->uvr1a;
        $pro->uvr2a = $request->uvr2a;
        $pro->uvr3a = $request->uvr3a;
        $pro->prc1a = $request->prc1a;
        $pro->prc2a = $request->prc2a;
        $pro->prc3a = $request->prc3a;
        $pro->separado = $request->txtseparado;
        $pro->estado = $request->txtestado;

        $pro->id_usuariocrea = $idusuario;
        $pro->id_usuariomod = $idusuario;
        $pro->ip_creacion = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente; 
        $pro->save();
        return redirect('administracion/procedimientos');
    }



    function index4()
    {
     return view('archivo_plano/procedimientos/frmasignonivel');
    }

    function fetch(Request $request)
    {
     if($request->get('query')) 
     {
      $query = $request->get('query');
      $data = ApProcedimiento::where('descripcion', 'LIKE', "%{$query}%")->get();
      $output = '<ul id="cierrate" class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $row)
      {
       $output .= '
       <li><a href="#"> Codigo: '.$row->codigo. '    |    Descripción: ' .$row->descripcion.'</a></li>
       <input type="hidden" name="id_procedimiento" value="'.$row->id.'">
       ';
      }
      $output .= '</ul>';
      echo $output;
     }
    }





    
   
}
