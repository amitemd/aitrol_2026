<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Comprobante;


class TipoComprobanteController extends Controller
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
    **********LISTADO TIPO DE COMPROBANTES***********
    /************************************************/
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tip_comprobante = Ct_Tipo_Comprobante::where('estado', '!=', null)->orderby('id', 'asc')->paginate(10);

        return view('contable.tipo_comprobante.index', ['tip_comprobante' => $tip_comprobante]); 
    }
    
    /*************************************************
    **************CREAR TIPO COMPROBANTE**************
    /*************************************************/
    public function crear()
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        return view('contable.tipo_comprobante.create'); 
    }

    /*************************************************
    *************GUARDA TIPO DE COMPROBANTE**********
    /*************************************************/  
    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Tipo_Comprobante::create([
            
            'nombre_comprobante'    => $request['nomb_comprobante'],
            'codigo'                => $request['codig_comprobante'],
            'estado'                => $request['estado_tip_comprobante'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 

        return redirect()->route('tipo_comprobante.index');
    }

    /*************************************************
    *************EDITAR TIPO COMPROBANTE *************
    /*************************************************/
    public function editar($id)
    {
        
      $tip_comprobante = Ct_Tipo_Comprobante::findorfail($id);
       
      return view('contable.tipo_comprobante.edit',['tip_comprobante' => $tip_comprobante]); 
    }


    /*************************************************
    *************ACTUALIZA TIPO COMPROBANTE***********
    /*************************************************/    
    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_tip_comprobante'];
        $tipo_comprobante = Ct_Tipo_Comprobante::findOrFail($id);
        
        $input = [

            'nombre_comprobante'    => $request['nomb_comprobante'],
            'codigo'                => $request['codig_comprobante'],
            'estado'                => $request['estado_tip_comprobante'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 


        $tipo_comprobante->update($input); 
        
        return redirect()->route('tipo_comprobante.index');

    }

    /*************************************************
    ************BUSCAR TIPO COMPROBANTE***************
    /*************************************************/ 
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

       
        $constraints = [
            'codigo'  => $request['buscar_codigo'],
            'nombre_comprobante'  => $request['buscar_tipo_comprobante'],
        ];
        
        $tip_comprobante = $this->doSearchingQuery($constraints);
        return view('contable.tipo_comprobante.index', ['request' => $request, 'tip_comprobante' => $tip_comprobante, 'searchingVals' => $constraints]);
    
    }

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Tipo_Comprobante::query();
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
