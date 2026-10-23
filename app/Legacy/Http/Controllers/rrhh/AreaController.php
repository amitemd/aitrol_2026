<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area;
use Sis_medico\Hospital;

class AreaController extends Controller
{
      /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/area';

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
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $area = Area::paginate(25);
      

        return view('rrhh/area/index', ['area' => $area]);
    }

    public function create()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('rrhh/area/create');
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Area::create([

            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/area');
    }

    private function validateInput($request) {
       $messages = [
        'nombre.required' => 'Agrega el nombre del Area.',
        'descripcion.required' => 'Agrega la descripcion del Area.',
        ];
        
        $constraints = [
        	'nombre' => 'required',
        	'descripcion' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $area = Area::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($area == null || count($area) == 0) {
            return redirect()->intended('/area');
        }

        return view('rrhh/area/edit', ['area' => $area]);
    }

    public function update(Request $request,  $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $area = Area::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'descripcion.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'descripcion' => 'required'
            ];
  
                

        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Area::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/area');
    }
}
