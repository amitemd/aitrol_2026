<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Emision;
use Sis_medico\Ct_Porcentaje_Impuesto_Renta;
class Porcentaje_Impuesto_RentaController extends Controller
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
    *****************LISTADO PORCENTAJE IR************
    /************************************************/
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $porcentaje_ir = Ct_Porcentaje_Impuesto_Renta::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);

        return view('contable.porcentaje_imp_renta.index', ['porcentaje_ir' => $porcentaje_ir]); 

    }
    
    /*************************************************
    *****************CREAR PORCENTAJE IR***************
    /*************************************************/
    public function crear()
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
     
        return view('contable.porcentaje_imp_renta.create'); 
    }

    /*************************************************
    *****************GUARDA PORCENTAJE IR**************
    /*************************************************/  
    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Porcentaje_Impuesto_Renta::create([
            
            'porcentaje'            => $request['porcentaje'],
            'anio'                  => $request['anio_porcentaje_ir'],
            'estado'                => $request['estado_porcentaje_ir'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 

        return redirect()->route('porcentaje_imp_renta.index');
    }

    /*************************************************
    ***************EDITAR PORCENTAJE IR****************
    /*************************************************/
    public function editar($id)
    {
        
        $porcentaje_ir = Ct_Porcentaje_Impuesto_Renta::findorfail($id);
     
        return view('contable.porcentaje_imp_renta.edit',['porcentaje_ir' => $porcentaje_ir]); 
    }


    /*************************************************
    ****************ACTUALIZA PORCENTAJE IR************
    /*************************************************/    
    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_porcentaje_ir'];
        $porcentaje = Ct_Porcentaje_Impuesto_Renta::findOrFail($id);
        
        $input = [

            'porcentaje'            => $request['porcentaje'],
            'anio'                  => $request['anio_porcentaje_ir'],
            'estado'                => $request['estado_porcentaje_ir'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 


        $porcentaje->update($input); 
        
        return redirect()->route('porcentaje_imp_renta.index');

    }

    /*************************************************
    ************BUSCAR CODIGO PORCENTAJE IR***********
    /*************************************************/ 
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'anio'         => $request['buscar_anio'],
            'porcentaje'  => $request['buscar_porcentaje'],
        ];
        //dd($constraints);
        $porcentaje_ir = $this->doSearchingQuery($constraints);
        return view('contable.porcentaje_imp_renta.index', ['request' => $request, 'porcentaje_ir' => $porcentaje_ir, 'searchingVals' => $constraints]);
    
    }

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Porcentaje_Impuesto_Renta::query();
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
