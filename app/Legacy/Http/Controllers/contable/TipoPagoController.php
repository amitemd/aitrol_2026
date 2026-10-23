<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Pago;


class TipoPagoController extends Controller
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
    **********LISTADO TIPO DE PAGO*******************
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tip_pago = Ct_Tipo_Pago::where('estado', '=', 1)->orderby('id', 'asc')->paginate(5);

        
         return view('contable.tipo_pago.index', ['tip_pago' => $tip_pago]); 
    }
    
    /*************************************************
    ****************CREAR TIPO PAGO*******************
    /*************************************************/
    public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        return view('contable.tipo_pago.create'); 
    }

    /*************************************************
    *************GUARDA TIPO PAGO*********************
    /*************************************************/  
    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Tipo_Pago::create([
            
            'nombre'                => $request['tipo_pago'],
            'codigo'                => $request['cod_tip_pago'],
            'estado'                => $request['estado_tipo_pago'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 
    

        return redirect()->route('tipo_pago.index');
    }

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
    ****************BUSCAR TIPO PAGO**************
    /*************************************************/ 
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $constraints = [
            'id'  => $request['buscar_codigo'],
            'nombre'  => $request['buscar_tipo_pago'],
            'estado'  => 1,
        ];
        
        $tip_pago = $this->doSearchingQuery($constraints);
        return view('contable.tipo_pago.index', ['request' => $request, 'tip_pago' => $tip_pago, 'searchingVals' => $constraints]);
    
    }

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Tipo_Pago::query();
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

    public function editar($id){

        //$metodo_pago = Ct_Tipo_Pago::find($id);
        $tipo = Ct_Tipo_Pago::where("id",$id)->first();
        return view('contable/tipo_pago/editar',['tipo'=>$tipo]);
    }
    public function actualizar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request['id_tipo_pago'];
        $tipo_pago = Ct_Tipo_Pago::findOrFail($id);
        
        $input = [
            //'id'                  => $request['cod_tip_pago'],
            'nombre'                => $request['tipo_pago'],
            'estado'                => $request['estado_tip_pago'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 

        $tipo_pago->update($input); 
        
        return redirect()->route('tipo_pago.index');

    }

}
