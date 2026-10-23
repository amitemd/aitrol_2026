<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Paciente;
use Sis_medico\Cortesia_paciente;
use Illuminate\Support\Facades\Auth;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class CortesiaController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/cortesia';

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
        if(in_array($rolUsuario, array(3)) == false){
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
        $cortesia_pacientes = Cortesia_paciente::paginate(10);

        $cortesia_pacientes = DB::table('cortesia_paciente')
            ->join('paciente', 'cortesia_paciente.id', '=', 'paciente.id')
            ->select('cortesia_paciente.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2' )->paginate(10);
   
        
        return view('cortesia/index', ['cortesia_pacientes' => $cortesia_pacientes, 'id' => '', 'paciente' => null]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('cortesia/create',['id' => '', 'paciente' => null ]);
    }
    public function crear2($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
         

        $cortesia_pacientes = DB::table('cortesia_paciente')
            ->join('paciente', 'cortesia_paciente.id', '=', 'paciente.id')
            ->select('cortesia_paciente.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2' )->paginate(10);

        $paciente = Paciente::find($id);
         
          
        
        return view('cortesia/index', ['cortesia_pacientes' => $cortesia_pacientes, 'id' => $id, 'paciente' => $paciente]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
         
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $mensajes = [
            'id.required' => 'Ingrese el Paciente.',
            'id.unique' => 'Paciente ya ingresado.',
            'id.exists' => 'Paciente no existe en el sistema.',
            'cortesia.in' =>'Seleccione SI o NO.',
            'ilimitado.required' => 'Seleccione SI o NO.',                       
        ];
                 
        $constraints = [
            'id' => 'required|unique:cortesia_paciente|exists:paciente,id',
            'cortesia' => 'in:SI,NO',
            'ilimitado' => 'in:SI,NO',
            
        ];

        $input = [
        
            'id' => $request['id'],
            'cortesia' => $request['cortesia'],
            'ilimitado' => $request['ilimitado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,

        ];
                

       $this->validate($request, $constraints, $mensajes);

       Cortesia_paciente::create($input);
        return redirect()->intended('/cortesia');
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
        // Redirect to user list if updating user wasn't existed
        if ($procedimiento == null || count($procedimiento) == 0) {
            return redirect()->intended('/procedimiento');
        }

        return view('procedimiento/edit', ['procedimiento' => $procedimiento]);
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

    public function editarcortesia($id, $i)
    {
        $cortesia_paciente = Cortesia_paciente::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        
        if($i == 1){
            if($cortesia_paciente->cortesia=="NO"){$cortesia="SI";}
            if($cortesia_paciente->cortesia=="SI"){$cortesia="NO";}
            $input = [
                'cortesia' => $cortesia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];
            $cortesia_paciente->update($input);        
        }
        if($i == 2){
            if($cortesia_paciente->ilimitado=="NO"){$ilimitado="SI";}
            if($cortesia_paciente->ilimitado=="SI"){$ilimitado="NO";}
            $input = [
                'ilimitado' => $ilimitado,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];
            $cortesia_paciente->update($input);        
        }
        
        return redirect()->intended('/cortesia');
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
            'id' => $request['id'],

            ];

        
       $cortesia_pacientes = $this->doSearchingQuery($constraints);  

       return view('cortesia/index', ['cortesia_pacientes' => $cortesia_pacientes, 'id' => '', 'paciente' => null, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
       /* $query = Cortesia_paciente::query(); */
        
               
        
        
        $fields = array_keys($constraints);
        
        $index = 0;

        foreach ($constraints as $constraint)  {
            $query = DB::table('cortesia_paciente')
            ->join('paciente', function ($join) use ($fields,$constraint) {
                $join->on('cortesia_paciente.id', '=', 'paciente.id')
                 ->select('cortesia_paciente.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2' )   
                 ->where('paciente.id', 'like', '%'.$constraint.'%');
            })->paginate(10);
        }
        
        
        return $query ;
    }
    

private function validateInput($request) {
        


    $this->validate($request,[]);



    }

}
