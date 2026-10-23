<?php

namespace Sis_medico\Http\Controllers\cie_10;

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
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Empresa;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use Sis_medico\Cie_10_3;




class Cie_10_3Controller extends Controller
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


        //$examenes = DB::table('cie_10_3 as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->paginate(30);
        $cie_10_3 = DB::table('cie_10_3 as e')->where('e.estado','1')->paginate(30);
        //dd($hospitalizados);
        return view('cie_10/cie_10_3/index', ['cie_10_3' => $cie_10_3]);
    }

    public function parametro($id_examen)
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $examen_parametros = Examen_Parametro::where('id_examen',$id_examen)->paginate(30);
        $examen = Examen::find($id_examen);

        return view('laboratorio/examen/parametro',['examen' => $examen,'examen_parametros' => $examen_parametros]);
    }


    

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        return view('cie_10/cie_10_3/create',['agrupadores' => $agrupadores]);
        //return view('laboratorio/examen/create',['agrupadores' => $agrupadores]);

    }

    public function create_parametro($id_examen){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $examen = Examen::find($id_examen);
        return view('cie_10/cie_10_3/create_parametro',['examen' => $examen]);

    }

     public function store(Request $request)
    {
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);

        
        $input = [    
            'id' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        //$id_examen = Examen::insertGetId($input);           
            Cie_10_3::create($input);
        return redirect()->route('cie_10_3.index');
        //return redirect()->route('examen.parametro',['id_examen' => $id_examen]);
    }

    public function store_parametro(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);

        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['nombre']),
            'texto1' => strtoupper($request['texto1']),
            'texto2' => strtoupper($request['texto2']),
            'texto3' => strtoupper($request['texto3']),
            'texto4' => strtoupper($request['texto4']),
            'valor1' => $request['valor1'],
            'valor2' => $request['valor2'],
            'valor3' => $request['valor3'],
            'valor4' => $request['valor4'],
            'valor1g' => $request['valor1g'],
            'valor2g' => $request['valor2g'],
            'valor3g' => $request['valor3g'],
            'valor4g' => $request['valor4g'],
            'unidad1' => strtoupper($request['unidad1']),
            'unidad2' => strtoupper($request['unidad2']),
            'unidad3' => strtoupper($request['unidad3']),
            'unidad4' => strtoupper($request['unidad4']),
            'id_examen' => $request['id_examen'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        Examen_parametro::create($input);           

       
        return redirect()->route('examen.parametro',['id_examen' => $request['id_examen']]);
    }

    private function validateInput($request) {
      //dd($request->all());
        $rules = [
           
            'nombre' =>  'required|unique:Cie_10_3,id|max:200',
            'descripcion' =>  'required|max:200',
            //'valor' => 'required|numeric',

        ];
         
        $messages= [
       
        'id.required' => 'Ingresa el Nombre.', 
        'id.unique' => 'El código ingresado ya existe.',     
        'id.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
        //'valor.required' => 'Ingresa el Valor.',
        //'valor.numeric' => 'Valor debe ser numérico.',
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request,$id) {

        $rules = [
           
            'nombre' =>  'required|unique:Cie_10_3,id,'.$id.'|max:200',
            'descripcion' =>  'required|max:200',
            

        ];

         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
       
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

        $cie_10_3 = Cie_10_3::find($id);
        if(!is_null($cie_10_3)){

        //$agrupadores = Examen_Agrupador::where('estado','1')->get();
            return view('cie_10/cie_10_3/edit', ['cie_10_3' => $cie_10_3]);

        }


    }

    public function update(Request $request, $id)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput2($request,$id);

        
        $input = [    
            'id' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];

        $cie_10_3 = Cie_10_3::find($id);    


        $cie_10_3->update($input);           

        return redirect()->route('cie_10_3.index');
        //return redirect()->route('examen.parametro',['id_examen' => $examen->id]);
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
        $cie_10_3 = DB::table('cie_10_3 as e')->where('e.descripcion','like','%'.$nombre.'%')->paginate(30);

        //$cie_10_3 = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);
        //dd($hospitalizados); 
        return view('cie_10/cie_10_3/index', ['cie_10_3' => $cie_10_3]);
        
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

    public function log($id){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $hosp = Agenda::find($id);
        $paciente = Paciente::find($hosp->id_paciente);
        $logs = DB::table('log_agenda as l')->where('l.id_agenda',$id)->get();

        
        return view('hospital_iess/hospitalizados/log', ['logs' => $logs, 'paciente' => $paciente]);
        
    }

    



}