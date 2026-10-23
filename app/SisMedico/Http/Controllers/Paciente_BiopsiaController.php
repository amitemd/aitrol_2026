<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Paciente_Biopsia;

class Paciente_BiopsiaController extends Controller
{
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
        if(in_array($rolUsuario, array(1, 4, 5)) == false){
          return true;
        }
        return false;
    }

    public function index()
    { 
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
    	$paciente_biopsia = paciente_biopsia::all();
    	return view('paciente_biopsia.index', ['paciente_biopsia' => $paciente_biopsia]);
    }

    public function create()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
    	return view('paciente_biopsia.create');
    }

    public function guardar(Request $request){

    	$ip_cliente= $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        
        $this->validateInput($request);
        //seteo de tiempo horario de guayaquil
        date_default_timezone_set('America/Guayaquil');


        ciudad::create([
        	'nombre' => $request['nombre'],
        	'descripcion' => $request['descripcion'],
        	'estado' => $request['estado'],
            'id_provincia' => $request['id_provincia'],
        	'id_usuariocrea' => $idusuario,
        	'id_usuariomod' => $idusuario,
        	'ip_creacion' => $ip_cliente,
        	'ip_modificacion' => $ip_cliente
        ]);

        return redirect()->intended('/ciudad_ingreso');
    }

    private function validateInput($request){

    	$mensajes = [
        'nombre.required' => 'Agrega el nombre del tipo de usuario.',
        'nombre.max' => 'El nombre no puede contener mas de 30 caracteres',
        'descripcion.required' => 'Agrega la descripción del tipo de usuario.',
        'estado.required' => 'Agregar Estado'
        ];
        $reglas = [
            'nombre' => 'required|max:30',
            'descripcion' => 'required',
            'estado' => 'required'
        ];

        $this->validate($request, $reglas, $mensajes);
    }

    public function actualizar($id){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
    	$ciudad = ciudad::find($id);
        $provincias = provincia::all();
    	return view('ciudad.actualizar', ['ciudad' => $ciudad, 'provincias' => $provincias]);
    }

    public function guardar_actualizar(Request $request){
        $provincia = Ciudad::findOrFail($request['id']);
        $id = $request['id'];

        $input = [
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'estado' => $request['estado']
        ];
        Ciudad::where('id', $id)
            ->update($input);
        return redirect()->intended('ciudad_ingreso/');
    }
}
