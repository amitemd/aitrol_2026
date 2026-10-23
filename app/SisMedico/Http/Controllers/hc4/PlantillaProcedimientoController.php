<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Principio_Activo;
use Sis_medico\Medicina;
use Sis_medico\Hc_Log;
use Sis_medico\procedimiento_completo;
use Sis_medico\grupo_procedimiento;
use Sis_medico\Examen_Orden;
use Sis_medico\Medicina_Principio;
use Sis_medico\Opcion_Usuario;
use Response;

class PlantillaProcedimientoController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

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
    public function index(){

       $tecnicas_quirurgicas = procedimiento_completo::OrderBy('nombre_general', 'ASC')->paginate(250);
        $proc_completo = procedimiento_completo::all();
        return view('hc4/plantillas_procedimientos/index', ['tecnicas_quirurgicas' => $tecnicas_quirurgicas, 'proc_completo' => $proc_completo, 'procedimiento_completo' => null]);

    }

     public function search(Request $request)
    { 

      // dd("siii");
        //return $request['proc_com'];

        $procedimiento_completo = null;

        $tecnicas_quirurgicas = procedimiento_completo::OrderBy('nombre_general', 'ASC');

        if($request['proc_com']!=null){
            $tecnicas_quirurgicas = $tecnicas_quirurgicas->where('id',$request['proc_com']);
            $procedimiento_completo = $request['proc_com'];
        }

        $tecnicas_quirurgicas = $tecnicas_quirurgicas->paginate(20);

        $proc_completo = procedimiento_completo::all();
        return view('hc4/plantillas_procedimientos/index', ['tecnicas_quirurgicas' => $tecnicas_quirurgicas, 'proc_completo' => $proc_completo, 'procedimiento_completo' => $procedimiento_completo]);
    }

    public function edit($id){
        $procedimiento_completo =  procedimiento_completo::find($id);
        $grupo_procedimiento = grupo_procedimiento::all();
        return view('hc4/plantillas_procedimientos/editar', ['procedimiento_completo' => $procedimiento_completo, 'grupo_procedimiento' => $grupo_procedimiento]);
    }

      public function create(){
        $grupo_procedimiento = grupo_procedimiento::all();
        return view('hc4/plantillas_procedimientos/crear',['grupo_procedimiento' => $grupo_procedimiento]);

    }

     public function store(Request $request){
        //return $request['nombre_general'];
       if(is_null($request['nombre_general']))
       {
            $this->validateInput2($request);
        }else{
           $this->validateInput($request);
        }


        $reglas = [
            'nombre' => 'required|max:255',
            'contenido' => 'required',
        ];

        $mensajes = [
            'contenido.required' => 'Agrege el informe de la tecnica.',
            'nombre.required' => 'Agrega el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',                        
        ];
 
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        //$this->validate($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');
        /*tecnicas_quirurgicas::create([
            'nombre' => strtoupper($request['nombre']),
            'contenido' => strtoupper($request['contenido']),
            
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

        ]);*/

        $plantilla_proc_new = [
            'anterior' => 'CREACION DE NUEVA PLANTILLA',
            'nuevo' => 'CREAR-PLANTILLA -> NOMBRE: '.$request['nombre_general'].'   GRUPO:'.$request['id_grupo_procedimiento'].'   ESTADO_ANESTESIA:'.$request['estado_anestesia'],
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Hc_Log::create($plantilla_proc_new);
      


        $input = [
            'nombre_general' => $request['nombre_general'],
            'nombre_completo' => $request['nombre_general'],
            'estado' => '1',
            'estado_anestesia' => $request['estado_anestesia'], 
            'id_grupo_procedimiento' => $request['id_grupo_procedimiento'], 
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario

        ];

        procedimiento_completo::create($input);


        //return redirect()->intended('inicio/plantillas/procedimientos');
    }


    public function update(Request $request){
        //return $request->all();
        $procedimiento_completo = procedimiento_completo::findOrFail($request['id']);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [
            'nombre_general' => $request['nombre_general'],
            'nombre_completo' => $request['nombre_completo'],
            'estado' => $request['estado'],
            'estado_anestesia' => $request['estado_anestesia'], 
            'tecnica_quirurgica' => $request['tecnica_quirurgica'],
            'id_grupo_procedimiento' => $request['id_grupo_procedimiento'], 
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario

        ];
  
        procedimiento_completo::where('id', $request['id'])
            ->update($input);
        //return $request->all();
        return "ok";

        //return redirect()->intended('/tecnicas/'.$request['agenda']);
    }




   private function validateInput2($request) {

        $rules = [
        
        'nombre_general' => 'required|max:255',
        
        ];
        
        $mensajes = [
            'nombre_general.required' => 'Ingrese el nombre.',
            'nombre_general.max' =>'El nombre no puede ser mayor a :max caracteres.',
            
        ];
         
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput($request) {
        
        $rules = [
            'nombre_general' => 'required|max:255',
           
            ];
            
        $mensajes = [
            'nombre_general.required' => 'Ingrese el nombre.',
            'nombre_general.max' =>'El nombre no puede ser mayor a :max caracteres.',
        ];
        
      $this->validate($request, $rules, $mensajes);
    }


   
}
 