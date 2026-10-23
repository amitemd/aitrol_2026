<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Empresa;
use Sis_medico\Ct_Rh_Tipo_Aporte;
use Sis_medico\CtLogNomina;


class NominaConfiguracionController extends Controller
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

    
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $lista_configuracion = Ct_Rh_Valores::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(20);

        $empresas = Empresa::all();

        $tipo_aport = Ct_Rh_Tipo_Aporte::all();
       
        
        return view('contable.rh_configuracion_valores.index', ['lista_configuracion' => $lista_configuracion, 'empresas' => $empresas,'tipo_aport' => $tipo_aport,'id_empresa' => $id_empresa,'empresa' => $empresa]);
    }
   

    /************************************************
    ***************Nomina Configuracion*************
    /************************************************/
    public function crear_configuracion_valores(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        //$empresas = Empresa::all();
        $tipo_aport = Ct_Rh_Tipo_Aporte::all();
      
        //return view('contable.rh_configuracion_valores.create', ['empresas' => $empresas,'tipo_aport' => $tipo_aport]);
        return view('contable.rh_configuracion_valores.create', ['tipo_aport' => $tipo_aport,'empresa' => $empresa]);  
    }
    

    /*************************************************
    *****************GUARDAR VALORES******************
    /*************************************************/
    public function store(Request $request){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput2($request);
        
        $id_empresa = $request->session()->get('id_empresa');
        

        Ct_Rh_Valores::create([
           
            //'id_empresa' => $request['id_empresa'],
            'id_empresa'      => $id_empresa,
            'valor'           => $request['valor'],
            'tipo'            => $request['tipo'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente
        
        ]);

    }

    private function validateInput2($request){

        $rules = [

            //'id_empresa' => 'required',
            'tipo' => 'required',
            'valor' => 'required',
        ];
         
        $messages= [
            //'id_empresa.required' =>'Seleccione la Empresa.',
            'tipo.required' =>'Seleccione el tipo.',
            'valor.required' => 'Ingrese un valor para cada tipo.',
           
        
        ]; 

        $this->validate($request, $rules, $messages);   

    }


    /*************************************************
    ***********EDITAR CONFIGURACIONN VALOR*************
    /*************************************************/
    public function edit($id)
    {

        $config_valor = Ct_Rh_Valores::find($id);
        $aporte = Ct_Rh_Tipo_Aporte::find($config_valor->tipo);
        $this->log_rol('EDITA CONFIGURACION VALORES'.' '.$aporte->descripcion);
        $empresa  = Empresa::where('id', $config_valor->id_empresa)->first();

        //$empresas = Empresa::all();

        $tipo_aport = Ct_Rh_Tipo_Aporte::all();
 
        return view('contable.rh_configuracion_valores.edit', ['config_valor' => $config_valor,'empresa' => $empresa,'tipo_aport' => $tipo_aport]);
    }


    
    /*************************************************
    ***********ACTUALIZA CONFIGURACIONN VALOR*********
    /*************************************************/
    public function update(Request $request){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id_config =  $request['id_config_val'];

        $id_empresa = $request->session()->get('id_empresa');
        
        /*$rules = [

            'id_empresa' => 'required',
            'tipo' => 'required',
            'valor' => 'required',
        ];
         
        $messages= [
            'id_empresa.required' =>'Seleccione la Empresa.',
            'tipo.required' =>'Seleccione el tipo.',
            'valor.required' => 'Ingrese un valor para cada tipo.',
           
        
        ]; 

        $this->validate($request, $rules, $messages);*/   
       
        $this->validateInput2($request);
        
        $input = [
            
            //'id_empresa' => $request['id_empresa'],
            'id_empresa'       => $id_empresa,
            'valor'            => $request['valor'],
            'tipo'             => $request['tipo'],
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente

        ];


        Ct_Rh_Valores::where('id', $id_config)->update($input);
        

    }


    public function anular($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fechahoy   = Date('Y-m-d H:i:s');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $act_estado = [
            'estado' => '0',
        ];

        Ct_Rh_Valores::where('id', $id)->update($act_estado);

       
        return redirect()->intended('/contable/configuracion/valores');
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        
        $constraints = [
            'tipo'         => $request['tipo'],
            //'id_empresa'  => $request['id_empresa'],
            'estado'           => 1,
            'id_empresa'       => $id_empresa,
        ];
        
        $lista_configuracion = $this->doSearchingQuery($constraints);
     
        //$empresas = Empresa::all();

        $tipo_aport = Ct_Rh_Tipo_Aporte::all();

        return view('contable.rh_configuracion_valores.index', ['request' => $request,'lista_configuracion' => $lista_configuracion, 'empresa' => $empresa,'searchingVals' => $constraints,'tipo_aport' => $tipo_aport]);


    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rh_Valores::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(20);
    }
    private function log_rol($tipo)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $array = [
            'tipo' => $tipo,
            'id_usuariocrea' => $id_usuario,
            'id_usuariomod'  => $id_usuario,
            'ip_creacion'    => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        CtLogNomina::create($array);
    }




}
