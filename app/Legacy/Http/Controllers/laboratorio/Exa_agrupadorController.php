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
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Empresa;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use Sis_medico\Cie_10_3;
use Sis_medico\Examen_Agrupador_labs;





class Exa_agrupadorController extends Controller
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
        $examen_agrupador = DB::table('examen_agrupador as e')->where('e.estado','1')->paginate(30);
        //dd($hospitalizados);
        return view('laboratorio/exa_agrupadores/index', ['examen_agrupador' => $examen_agrupador]);
    }



    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        return view('laboratorio/exa_agrupadores/create',['agrupadores' => $agrupadores]);
        //return view('laboratorio/examen/create',['agrupadores' => $agrupadores]);

    }

    

     public function store(Request $request)
    {
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);

        
        $input = [    
            //'id' => strtoupper($request['id']),
            'nombre' => strtoupper($request['nombre']),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        //$id_examen = Examen::insertGetId($input);           
            Examen_Agrupador::create($input);
        return redirect()->route('laboratorio/exa_agrupadores.index');
        //return redirect()->route('examen.parametro',['id_examen' => $id_examen]);
    }

    

    private function validateInput($request) {
      //dd($request->all());
        $rules = [
           
            'nombre' =>  'required|unique:Examen_Agrupador,id|max:200',
            //'id' =>  'required|max:200',
            //'valor' => 'required|numeric',

        ];
         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        //'id.required' => 'El ID no puede ser modificado.',
        //'id.max' =>'El ID no puede ser modificado.',
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request,$id) {

        $rules = [
           
            'nombre' =>  'required|unique:Examen_Agrupador,id,'.$id.'|max:200',
            'id' =>  'required|max:200',
            

        ];

         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'id.required' => 'El ID no puede ser modificado.',
        'id.max' =>'El ID no puede ser modificado.',
       
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

        $examen_agrupador = Examen_Agrupador::find($id);
        if(!is_null($examen_agrupador)){

        //$agrupadores = Examen_Agrupador::where('estado','1')->get();
            return view('laboratorio/exa_agrupadores/edit', ['examen_agrupador' => $examen_agrupador]);

        }


    }

    public function update(Request $request, $id)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput2($request,$id);

        
        $input = [    
            'id' => strtoupper($request['id']),
            'nombre' => strtoupper($request['nombre']),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];

        $examen_agrupador = Examen_Agrupador::find($id);    


        $examen_agrupador->update($input);           

        return redirect()->route('laboratorio/exa_agrupadores.index');
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
        $examen_agrupador = DB::table('examen_agrupador as e')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);

        //$cie_10_3 = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);
        //dd($hospitalizados);
        return view('laboratorio/exa_agrupadores/index', ['examen_agrupador' => $examen_agrupador]);
        
    }

    public function agrupador_labs_buscar_aj(Request $request)
    {
        $agrupador_labs = Examen_Agrupador_labs::where('nombre','like','%'.$request->term.'%')->get();
        $arr=null;
        foreach ($agrupador_labs as $value) {
            $arr[] = array('value' => $value->nombre , 'id' => $value->id );
        }

        return $arr;
    }

    



}