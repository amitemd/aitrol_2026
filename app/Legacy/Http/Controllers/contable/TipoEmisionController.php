<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Emision;

class TipoEmisionController extends Controller
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
    *****************LISTADO TIPO EMISION************
    /************************************************/
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tip_emision = Ct_Tipo_Emision::where('estado', '!=', null)->orderby('id', 'desc')->paginate(10);

        return view('contable.tipo_emision.index', ['tip_emision' => $tip_emision]); 

    }
    
    /*************************************************
    *****************CREAR TIPO EMISION***************
    /*************************************************/
    public function crear()
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
     
        return view('contable.tipo_emision.create'); 
    }

    /*************************************************
    *****************GUARDA TIPO EMISION**************
    /*************************************************/  
    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Tipo_Emision::create([
            
            'tipo_emision'          => $request['tipo_emision'],
            'codigo'                => $request['cod_tip_emision'],
            'estado'                => $request['estado_tip_emision'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 

        return redirect()->route('tipo_emision.index');
    }

    /*************************************************
    ***************EDITAR TIPO EMISION****************
    /*************************************************/
    public function editar($id)
    {
        
        $tip_emision = Ct_Tipo_Emision::findorfail($id);
     
        return view('contable.tipo_emision.edit',['tip_emision' => $tip_emision]); 
    }


    /*************************************************
    ****************ACTUALIZA TIPO EMISION************
    /*************************************************/    
    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_tipo_emision'];
        $tipo_emision = Ct_Tipo_Emision::findOrFail($id);
        
        $input = [

            'tipo_emision'          => $request['tipo_emision'],
            'codigo'                => $request['cod_tip_emision'],
            'estado'                => $request['estado_tip_emision'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 


        $tipo_emision->update($input); 
        
        return redirect()->route('tipo_emision.index');

    }

    /*************************************************
    ************BUSCAR CODIGO TIPO EMISION***********
    /*************************************************/ 
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo'  => $request['buscar_codigo'],
            'tipo_emision'  => $request['buscar_tipo_emision'],
        ];
        $tip_emision = $this->doSearchingQuery($constraints);
        return view('contable.tipo_emision.index', ['request' => $request, 'tip_emision' => $tip_emision, 'searchingVals' => $constraints]);
    
    }

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Tipo_Emision::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }




}
