<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Protocolo;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Empresa;
use Sis_medico\Nivel;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;




class ProtocoloController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10)) == false && $id_auth!='1307189140'){
          return true;
        }
        

    }

    

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $protocolos = Protocolo::paginate(30);

       
        return view('laboratorio/protocolo/index', ['protocolos' => $protocolos]);
    }

    

    

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        return view('laboratorio/protocolo/create');

    }

    

     public function store(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
       
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'est_amb_hos' => $request['est_amb_hos'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            
            ];


        Protocolo::create($input);           

        return redirect()->route('protocolo.index');
        
    }

    public function examen($chid,$pr)
    {
        $id = substr($chid,2);
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput($request);

        
        $input = [    
            'id_protocolo' => $pr,
            'id_examen' => $id,
            
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        Examen_Protocolo::create($input);           

       
        return "ok";
    }

    public function eliminar($chid,$pr)
    {
        $id = substr($chid,2);
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput($request);

        
        $input = [    
            'id_usuariomod' => $idusuario,
            'ip_modificacion' => $ip_cliente, 
            ];

        $protocolo = Protocolo::find($pr);
        $protocolo->update($input);

        $detalle = Examen_Protocolo::where('id_protocolo', $pr)->where('id_examen',$id)->first();
        $detalle->Delete();           

       
        return "ok";
    }

    private function validateInput($request) {

        $rules = [
           
            'nombre' =>  'required|unique:examen,nombre|max:200',
            'descripcion' =>  'required|max:200',
            'valor' => 'required|numeric',
            'tarifario' => 'required|unique:examen,tarifario|max:10',

        ];
         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
        'valor.required' => 'Ingresa el Valor.',
        'valor.numeric' => 'Valor debe ser numérico.',
        'tarifario.required' => 'Ingresa el tarifario.', 
        'tarifario.unique' => 'El tarifario ingresado ya existe.',     
        'tarifario.max' =>'El tarifario no puede ser mayor a :max caracteres.', 
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request,$id) {

        $rules = [
           
            'nombre' =>  'required|unique:examen,nombre,'.$id.'|max:200',
            'descripcion' =>  'required|max:200',
            'valor' => 'required|numeric',
            'tarifario' => 'required|unique:examen,tarifario,'.$id.'|max:10',

        ];

         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
        'valor.required' => 'Ingresa el Valor.',
        'valor.numeric' => 'Valor debe ser numérico.',
        'tarifario.required' => 'Ingresa el tarifario.', 
        'tarifario.unique' => 'El tarifario ingresado ya existe.',     
        'tarifario.max' =>'El tarifario no puede ser mayor a :max caracteres.', 
        ];    

        $this->validate($request, $rules, $messages);
    }

    

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        $protocolo = Protocolo::where('id',$id)->where('estado','1')->first();

        $examenes = Examen::where('publico_privado','0')->where('estado','1')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $detalles = Examen_Protocolo::where('id_protocolo',$id)->where('estado','1')->get();
        if(!is_null($protocolo)){

            return view('laboratorio/protocolo/edit', ['protocolo' => $protocolo, 'agrupadores' => $agrupadores, 'examenes' => $examenes, 'detalles' => $detalles ]);

        }


    }

    public function update(Request $request, $id)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $protocolo = Protocolo::find($id); 
        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'est_amb_hos' => $request['est_amb_hos'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            
            ];

        $protocolo->update($input);    
                  

        return redirect()->route('protocolo.index');
    }    

    

    public function buscapaciente($id){
        $paciente = Paciente::find($id);
        if(!is_null($paciente))
        {
            return $paciente;    
        }
        else
        {
            return 'no';
        }    


        
    }

    public function search(Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }    

        $nombre = $request['nombre'];
        $examenes = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);
        //dd($hospitalizados);
        return view('laboratorio/examen/index', ['examenes' => $examenes]);
        
    }

    public function buscar2(Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }    

        $nombre_encargado = $request['paciente'];
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo =  $seteo.$value.'%';
        }

        
        $hospitalizados = DB::table('agenda as a')->where('a.proc_consul','3')->where('a.estado','2')->join('paciente as p','p.id','a.id_paciente')->join('seguros as s','s.id','a.id_seguro')->leftjoin('users as d','d.id','a.id_doctor1')->select('a.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2','s.nombre as snombre','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->selectRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as completo")->whereRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) like '".$seteo."'")->orderBy('a.fechafin','desc')->paginate(30);
        
        return view('hospital_iess/hospitalizados/altas', ['hospitalizados' => $hospitalizados, 'paciente' => $request['paciente']]);
        
    }

    public function buscaexamen($id){

        

        $examenes = Examen_Protocolo::where('id_protocolo',$id)->get();
        
        if(!is_null($examenes)){
            return $examenes;
        }

        return "no";
        
        
    }

    public function buscaexamenid($id,$examen){


        $examen = substr($examen,2);

        $examen = Examen_Protocolo::where('id_protocolo',$id)->where('id_examen',$examen)->first();
        
        

        if(!is_null($examen)){
            return "ok";
        }

        return "no";
        
        
    }

    



}