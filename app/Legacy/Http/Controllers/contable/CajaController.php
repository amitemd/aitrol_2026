<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Caja;
use Sis_medico\Empresa;
use Sis_medico\Ct_Ven_Orden;



class CajaController extends Controller
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
    **********LISTADO DE PUNTO EMISION (CAJA)********
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $caja = Ct_Caja::where('estado','=', 1)->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(5);
        
        /*$caja = DB::table('ct_caja as ct_c')
                //->join('ct_sucursales as ct_su', 'ct_su.id', 'ct_c.id_sucursal')
                ->where('ct_c.estado','=', 1)
                ->where('ct_c.id_empresa', $id_empresa)
                //->select('ct_c.id as id','ct_su.id as id_sucursal','ct_c.codigo_caja','ct_c.nombre_caja','ct_su.id_empresa','ct_c.estado')
                ->select('ct_c.id as id','ct_c.codigo_caja','ct_c.nombre_caja','ct_c.estado','ct_c.id_sucursal as id_sucursal','ct_c.id_empresa')
                ->orderby('ct_c.id', 'desc')->paginate(5);*/
        //dd($caja);
        return view('contable.caja.index', ['caja' => $caja,'empresa' => $empresa]); 
    }
    
    /*************************************************
    *************CREAR PUNTO EMISION (CAJA)***********
    /*************************************************/
    public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        return view('contable.caja.create', ['empresa' => $empresa,'sucursales' => $sucursales]); 
    }

    /*************************************************
    *************GUARDA PUNTO EMISION (CAJA)**********
    /*************************************************/  
    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        
        Ct_Caja::create([
            
            'codigo_caja'           => $request['codigo_punto'],
            'id_empresa'            => $id_empresa,
            'nombre_caja'           => $request['nombre_punto'],
            'estado'                => $request['estado_punto'],
            'id_sucursal'           => $request['id_sucursal'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        
        ]); 

        return redirect()->route('punto_emision.index');
    }

    /*************************************************
    **********EDITAR PUNTO EMISION (SUSCURSALES)******
    /*************************************************/
    public function editar($id,$id_empresa)
    {
        $caja = Ct_Caja::findorfail($id);
        //dd($id);
        /*$caja = DB::table('ct_caja as ct_c')
                ->join('ct_sucursales as ct_su', 'ct_su.id', 'ct_c.id_sucursal')
                ->where('ct_c.id',$id)
                ->where('ct_c.estado', '!=', null)
                ->where('ct_su.id_empresa', $id_empresa)->first();*/
           
        $empresa  = Empresa::where('id', $id_empresa)->first();
        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->get();
     
     
        return view('contable.caja.edit',['caja' => $caja, 'empresa'=> $empresa,'sucursales'=> $sucursales]); 
    }


    /*************************************************
    **********ACTUALIZA PUNTO EMISION (CAJA)**********
    /*************************************************/    
    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id_empresa = $request->session()->get('id_empresa');
        $id = $request['id_punt_emision'];
        $punto_emision = Ct_Caja::findOrFail($id);
        
        $input = [

            'codigo_caja'           => $request['codigo_punto'],
            'id_empresa'            => $id_empresa,
            'nombre_caja'           => $request['nombre_punto'],
            'estado'                => $request['estado_punto'],
            'id_sucursal'           => $request['id_sucursal'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 


        $punto_emision->update($input); 
        
        return redirect()->route('punto_emision.index');

    }

    /*************************************************
    ************BUSCAR CODIGO PUNTO EMISION***********
    /*************************************************/ 
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'codigo_caja'  => $request['buscar_codigo'],
            'nombre_caja'  => $request['buscar_nombre'],
            'estado'       => 1,
            'id_empresa'   => $id_empresa,
        ];
        $caja = $this->doSearchingQuery($constraints,$request);
        return view('contable.caja.index', ['request' => $request, 'caja' => $caja, 'searchingVals' => $constraints,'empresa' => $empresa]);
    
    }

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    private function doSearchingQuery($constraints,Request  $request)
    {

        $query  = Ct_Caja::query();
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

    public function updateEstadoPago(Request $request){
        $id = $request['id'];
     

        $venta_orden = Ct_Ven_Orden::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
      //  dd($punto_emision);
       // dd($idusuario . " " . $ip_cliente);


        $input = [
            'estado_pago'           => 0,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,

        ]; 
        $veri =  $venta_orden->update($input); 
        $respuesta = array();
        if($veri == true){
            $respuesta =[
                'respuesta' => 'si'
            ];
        }else{
            $respuesta =[
                'respuesta' => 'no'
            ];
        }
       return $respuesta;



    }




}
