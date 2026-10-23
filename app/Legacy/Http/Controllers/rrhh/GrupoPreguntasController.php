<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area;
use Sis_medico\TipoSugerencia;
use Sis_medico\Sugerencia;
use Sis_medico\GrupoPregunta;

class GrupoPreguntasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }

    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $pregunta = GrupoPregunta::paginate(25);


        return view('rrhh/grupopregunta/index', ['preguntas' => $pregunta]);
    } 

    public function create()
    {
        $this->rol();
        return view('rrhh/grupopregunta/create' );
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        GrupoPregunta::create([

            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'tipo_calificacion' => $request['crespuesta'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/grupopreguntas');
    }

    private function validateInput($request) {
       $messages = [
        'nombre.required' => 'Agrega la pregunta central del Grupo de Preguntas.',
        'descripcion.required' => 'Agrega la descripcion del Grupo de Preguntas.',
        'crespuesta.required' => 'Agrega el tipo de calificacion del grupo de preguintas.',
        ];
        
        $constraints = [
        	'nombre' => 'required',
        	'descripcion' => 'required',
        	'crespuesta' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id){
    	if($this->rol()){
            return response()->view('errors.404');
        }
        $grupopregunta = GrupoPregunta::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($grupopregunta == null || count($grupopregunta) == 0) {
            return redirect()->intended('/grupopregunta');
        }

        return view('rrhh/grupopregunta/edit', ['grupopregunta' => $grupopregunta]);
    }

    public function update(Request $request, $id){
    	if($this->rol()){
            return response()->view('errors.404');
        }
        $grupopregunta = GrupoPregunta::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'descripcion.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'descripcion' => 'required',           
        'tipo_calificacion' => 'required'
            ];
  
                

        $input = [
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'tipo_calificacion' => $request['tipo_calificacion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        GrupoPregunta::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/grupopreguntas');
    }
}
