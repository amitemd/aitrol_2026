<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Procedimiento;
use Illuminate\Support\Facades\Auth;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Procedimiento_Sugerido;
use Sis_medico\grupo_procedimiento;

class ProcedimientoController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/procedimiento';

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
        if(in_array($rolUsuario, array(1, 4)) == false){
          return true;
        }
    }
    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $procedimientos = Procedimiento::orderBy("id", "desc")->paginate(40);
        return view('procedimiento/index', ['procedimientos' => $procedimientos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tprocedimientos = grupo_procedimiento::all();
        //dd($tprocedimientos);
      
        return view('procedimiento/create',['tprocedimientos'=> $tprocedimientos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        
        $reglas = [
            'nombre' => 'required|max:100|unique:procedimiento',
            'observacion' => 'required|max:255',
            
        ];

        $mensajes = [
            'nombre.unique' => 'El procedimiento ya existe.',
            'nombre.required' => 'Agrega el procedimiento.',
            'nombre.max' =>'El procedimiento no puede ser mayor a :max caracteres.',
            'observacion.required' => 'Agrega la observacion.',
            'observacion.max' =>'La observacion no puede ser mayor a :max caracteres.',
           
                                   
        ];
        $request["id_grupo_procedimiento"] = $request["id_grupo_procedimiento"] == 0 ? NULL : $request["id_grupo_procedimiento"];
        //dd($request["id_grupo_procedimiento"]);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');
        Procedimiento::create([
           
            'nombre' => strtoupper($request['nombre']),
            'observacion' => strtoupper($request['observacion']),
            'id_grupo_procedimiento'=> $request['id_grupo_procedimiento'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

        ]);
        //dd($request->all());


        return redirect()->intended('/procedimiento');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if($this->rol()){
            return response()->view('errors.404');
        }
        $procedimiento = Procedimiento::find($id);
        $tprocedimientos= grupo_procedimiento::all();
        // Redirect to user list if updating user wasn't existed
        //if ($procedimiento == null || count($procedimiento) == 0) {
        //    return redirect()->intended('/procedimiento');
       // }

        return view('procedimiento/edit', ['procedimiento' => $procedimiento, 'tprocedimientos'=>$tprocedimientos]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $procedimiento = Procedimiento::findOrFail($id);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $mensajes = [
            'nombre.unique' => 'El procedimiento ya existe.',
            'nombre.required' => 'Agrega el procedimiento.',
            'nombre.max' =>'El procedimiento no puede ser mayor a :max caracteres.',
            'observacion.required' => 'Agrega la observacion.',
            'observacion.max' =>'La observacion no puede ser mayor a :max caracteres.',
                                   
        ];

                 
        $constraints = [
            'nombre' => 'required|max:100|unique:procedimiento,id,'.$id,
            'observacion' => 'required|max:255',
            
        ];

        $input = [
        
            'nombre' => $request['nombre'],
            'observacion' => $request['observacion'],
            'id_grupo_procedimiento' => $request['id_grupo_procedimiento'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,

        ];
                

       $this->validate($request, $constraints, $mensajes);
  

        Procedimiento::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/procedimiento');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
        User::where('id', $id)->delete();
         return redirect()->intended('/user-management');
    }*/

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {

        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombre'],

            ];


       $procedimientos = $this->doSearchingQuery($constraints);

       return view('procedimiento/index', ['procedimientos' => $procedimientos, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Procedimiento::query();
        $fields = array_keys($constraints);
        
        $index = 0;
        
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        
        return $query->paginate(40);
    }
    

    private function validateInput($request) {
        


        $this->validate($request,[]);



    }
    public function sugerido($id){

        $actuales = Procedimiento_Sugerido::where('id_procedimiento1', $id);

        $totales = Procedimiento::all();
        $actual = Procedimiento::find($id);
        return view('procedimiento/modal', ['actual' => $actual,'id' => $id, "actuales" => $actuales, "totales" => $totales]);
    }
    public function procedimientoguardar(Request $request){
        $id_procedimiento1 = $request['id_procedimiento1'];
        Procedimiento_Sugerido::where('id_procedimiento1', $id_procedimiento1)->delete();
        $check = $request['lista'];
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        if($request['lista'] !=  null){
            foreach ($check as $value) {
              Procedimiento_Sugerido::create([
                'id_procedimiento1' => $id_procedimiento1,
                'id_procedimiento2' => $value,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
              ]);
            }
        }
        return redirect()->intended('/tecnicas');
    }

}
