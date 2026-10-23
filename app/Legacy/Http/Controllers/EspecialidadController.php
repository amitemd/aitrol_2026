<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Especialidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class EspecialidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4)) == false){
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
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        $especialidad = DB::table('especialidad')
        ->paginate(10);

        return view('especialidad/index', ['especialidades' => $especialidad]);
    }

    public function search(Request $request) {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombredelaespecialidad']];
        $especialidad = $this->doSearchingQuery($constraints);
        return view('especialidad/index', ['especialidades' => $especialidad, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = DB::table('especialidad');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(10);
    }


    public function create()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('especialidad/create' );
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
         especialidad::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
            
        ]);

        return redirect()->intended('/especialidad');
    }

    private function validateInput($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60|unique:especialidad',
        'descripcion' => 'required',
    ]);
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $especialidad = Especialidad::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($especialidad == null || count($especialidad) == 0) {
            return redirect()->intended('/especialidad');
        }
        return view('especialidad/edit', ['especialidad' => $especialidad]);
    }

    public function update(Request $request, $id)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $especialidad = especialidad::findOrFail($id);

        if(($request['nombre'] != $request['nombre1']) ){
            $this->validateInput($request);    
        }

       	$this->validateInput2($request); 
        
        $input = [
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];

        especialidad::where('id', $id)->update($input);

        return redirect()->intended('/especialidad');
    }

    private function validateInput2($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60',
        'descripcion' => 'required',
    ]);
    }
}
