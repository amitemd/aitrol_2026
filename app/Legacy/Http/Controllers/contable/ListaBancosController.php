<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Bancos;


class ListaBancosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    
    /************************************************
    ****************LISTADO BANCOS*******************
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $list_bancos = Ct_Bancos::where('estado',1)->orderby('id', 'asc')->paginate(6);

        
         return view('contable.tipo_pago.index', ['list_bancos' => $list_bancos]); 
    }
    
    /*************************************************
    ****************CREAR TIPO AMBIENTE***************
    /*************************************************/
    /*public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        return view('contable.tipo_ambiente.create'); 
    }*/

    /*************************************************
    *************GUARDA TIPO AMBIENTE*****************
    /*************************************************/  
    
    /*public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Tipo_Ambiente::create([
            
            'tipo_ambiente'         => $request['tipo_ambiente'],
            'codigo'                => $request['cod_tip_ambiente'],
            'estado'                => $request['estado_tip_ambiente'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 

        return redirect()->route('tipo_ambiente.index');
    }*/

    /*************************************************
    **************EDITAR TIPO AMBIENTE****************
    /*************************************************/
    /*public function editar($id)
    {
        
        $tip_ambiente = Ct_Tipo_Ambiente::findorfail($id);
     
        return view('contable.tipo_ambiente.edit',['tip_ambiente' => $tip_ambiente]); 
    }*/


    /*************************************************
    ************ACTUALIZAR TIPO AMBIENTE**************
    /*************************************************/    
    
    /*public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_tipo_ambiente'];
        $tipo_ambiente = Ct_Tipo_Ambiente::findOrFail($id);
        
        $input = [

            'tipo_ambiente'         => $request['tipo_ambiente'],
            'codigo'                => $request['cod_tip_ambiente'],
            'estado'                => $request['estado_tip_ambiente'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 


        $tipo_ambiente->update($input); 
        
        return redirect()->route('tipo_ambiente.index');

    }*/

    /*************************************************
    ****************BUSCAR TIPO AMBIENTE**************
    /*************************************************/ 
    /*public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo'  => $request['buscar_codigo'],
            'tipo_ambiente'  => $request['buscar_tipo_ambiente'],
        ];
        
        $tip_ambiente = $this->doSearchingQuery($constraints);
        return view('contable.tipo_ambiente.index', ['request' => $request, 'tip_ambiente' => $tip_ambiente, 'searchingVals' => $constraints]);
    
    }*/

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    /*private function doSearchingQuery($constraints)
    {

        $query  = Ct_Tipo_Ambiente::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }*/

}
