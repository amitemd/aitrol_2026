<?php

namespace Sis_medico\Http\Controllers\tecnicas;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\tecnicas_quirurgicas;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\grupo_procedimiento;


class TecnicasController extends Controller
{
    //
    protected $redirectTo = '/dashboard'; 

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3,11)) == false){//cambio dres 27082018
          return true;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($agenda)
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tecnicas_quirurgicas = procedimiento_completo::OrderBy('nombre_general', 'ASC')->paginate(20);
        $proc_completo = procedimiento_completo::all();
        return view('tecnicas/index', ['tecnicas_quirurgicas' => $tecnicas_quirurgicas, 'proc_completo' => $proc_completo, 'procedimiento_completo' => null, 'agenda' => $agenda]);
    }

    public function search(Request $request, $agenda)
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }

        $procedimiento_completo = null;

        $tecnicas_quirurgicas = procedimiento_completo::OrderBy('nombre_general', 'ASC');

        if($request['proc_com']!=null){
            $tecnicas_quirurgicas = $tecnicas_quirurgicas->where('id',$request['proc_com']);
            $procedimiento_completo = $request['proc_com'];
        }

        $tecnicas_quirurgicas = $tecnicas_quirurgicas->paginate(20);

        $proc_completo = procedimiento_completo::all();
        return view('tecnicas/index', ['tecnicas_quirurgicas' => $tecnicas_quirurgicas, 'proc_completo' => $proc_completo, 'procedimiento_completo' => $procedimiento_completo, 'agenda' => $agenda]);
    }

    public function create($agenda){
    	
        if($this->rol()){
            return response()->view('errors.404');
        }

        $grupo_procedimiento = grupo_procedimiento::all();
        return view('tecnicas/create',['agenda' => $agenda, 'grupo_procedimiento' => $grupo_procedimiento]);

    }

    public function store(Request $request){
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


        return redirect()->intended('/tecnicas/'.$request['agenda']);
    }




    public function edit($agenda, $id){
        $procedimiento_completo =  procedimiento_completo::find($id);
        //dd($procedimiento_completo);
        $grupo_procedimiento = grupo_procedimiento::all();
        return view('tecnicas/edit', ['procedimiento_completo' => $procedimiento_completo, 'agenda' => $agenda, 'grupo_procedimiento' => $grupo_procedimiento]);
    }

    public function update(Request $request, $id){
        $procedimiento_completo = procedimiento_completo::findOrFail($id);
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
  

        procedimiento_completo::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/tecnicas/'.$request['agenda']);
    }
}
 