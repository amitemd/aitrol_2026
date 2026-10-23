<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\hc_receta;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Medicina;
use Sis_medico\Log_usuario;
use Sis_medico\Principio_Activo;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Indicacion;

use Excel;


use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;






use Response;

class GenericoController extends Controller
{
    protected $redirectTo = '/';

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 6,11,7)) == false){
          return true;
        }
    }

    public function index($agenda){
        
        

        $genericos = Principio_Activo::where('estado',1)->orderBy('nombre')->paginate(20);


                   

        return view('hc_admision.generico.index', ['genericos' => $genericos, 'nombre' => null, 'agenda' => $agenda]);  
    }

    public function edit($agenda , $id){
        
        

        $generico = Principio_Activo::find($id);

        //dd($medicina);           

        return view('hc_admision.generico.edit', ['generico' => $generico, 'agenda' => $agenda]);  
    }

    public function create($agenda){
        
                  

        return view('hc_admision.generico.create',['agenda' => $agenda]);  
    }

    public function update(Request $request, $id){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $generico = Principio_Activo::findOrFail($id);

        $rules = [
            
            'nombre' => 'required',
            'descripcion' => 'required',
            

            ];

        $mensajes = [
              
            'nombre.required' => 'Ingrese el nombre.',
            'descripcion.required' => 'Ingrese la cantidad.',
            
            ];

        $this->validate($request, $rules, $mensajes);
        

        $input = [

            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            
            'estado' => $request['estado'],
            //'publico_privado' => $request['publico_privado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,    

        ];

        $generico->update($input);


        //return redirect()->route('generico.index');

        $genericos = Principio_Activo::where('estado',1)->where('nombre','like','%'.$request['nombre'].'%')->orderBy('nombre')->paginate(20);  

        return view('hc_admision.generico.index', ['genericos' => $genericos, 'nombre' => $request['nombre'], 'agenda' => $request['agenda'] ]);     
    }

    public function store(Request $request){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $rules = [
            
            'nombre' => 'required',
            'descripcion' => 'required',
            

            ];

        $mensajes = [
              
            'nombre.required' => 'Ingrese el nombre.',
            'descripcion.required' => 'Ingrese la cantidad.',
            
            ];

        $this->validate($request, $rules, $mensajes);
        

        $input = [

            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            
            'estado' => $request['estado'],
            //'publico_privado' => $request['publico_privado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,    

        ];

        Principio_Activo::create($input);


        //return redirect()->route('generico.index'); 
        $genericos = Principio_Activo::where('estado',1)->where('nombre','like','%'.$request['nombre'].'%')->orderBy('nombre')->paginate(20);  

        return view('hc_admision.generico.index', ['genericos' => $genericos, 'nombre' => $request['nombre'], 'agenda' => $request['agenda'] ]);    
    }

    public function search(Request $request, $agenda){
        $nombre = $request['nombre'];

        $genericos = Principio_Activo::where('estado',1)->where('nombre','like','%'.$nombre.'%')->orderBy('nombre')->paginate(20);  

        return view('hc_admision.generico.index', ['genericos' => $genericos, 'nombre' => $nombre, 'agenda' => $agenda]);  
        
    }

    public function find(Request $request)
    {
        
        $term = trim($request->q);
        //return $term;
        if (empty($term)) {
            return \Response::json([]);
        }

        $genericos = Principio_Activo::where('nombre','like','%'.$term.'%')->limit(20)->get();
//return $genericos;
        $formatted_tags = [];

        foreach ($genericos as $generico) {
            $formatted_tags[] = ['id' => $generico->id, 'text' => $generico->nombre];
        }

        return \Response::json($formatted_tags);
    }

    public function store2(Request $request){
        
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if($request['generico']!=null){

            $input = [

                'nombre' => strtoupper($request['generico']),
                'descripcion' => strtoupper($request['generico']),
                'estado' => '1',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,    

            ];

            $id = Principio_Activo::insertGetId($input);

            return ['id' => $id, 'nombre' => strtoupper($request['generico'])];   

        }else{
            
            return "no";

        }

         
    }

    

}
 