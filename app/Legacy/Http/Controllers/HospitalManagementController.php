<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;

class HospitalManagementController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/hospital-management';

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
        $hospitales = Hospital::paginate(5);
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('hospital-mgmt/index', ['hospitales' => $hospitales]);
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

        return view('hospital-mgmt/create');
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
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Hospital::create([

            'nombre_hospital' => strtoupper($request['nombre_hospital']),
            'ciudad' => strtoupper($request['ciudad']),
            'direccion' => strtoupper($request['direccion']),
            'telefono1' => $request['telefono1'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return redirect()->intended('/hospital-management');
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
        $hospitales = Hospital::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($hospitales == null || count($hospitales) == 0) {
            return redirect()->intended('/hospital-management');
        }

        return view('hospital-mgmt/edit', ['hospitales' => $hospitales]);
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
        $hospitales = Hospital::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre_hospital.required' => 'Agrega el nombre del hospital.',
        'nombre_hospital.max' =>'El nombre del hospital no puede ser mayor a :max caracteres.',
        'nombre_hospital.unique' =>'El nombre del hospital ya existe.',
        'ciudad.required' => 'Agrega la ciudad.',
        'ciudad.max' => 'la ciudad no puede ser mayor a :max caracteres.',
        'direccion.required' => 'Agrega la dirección.',
        'direccion.max' => 'la direccion no puede ser mayor a :max caracteres.',
        'telefono1.required' => 'Agrega el teléfono.',
        'telefono1.max' =>'El teléfono no puede ser mayor a 10 caracteres.',
        'telefono1.numeric' =>'El telefono debe ser numerico.',
        'estado.required' => 'Agrega el estado.',
  
        ];

        
        $constraints = [
        'nombre_hospital' => 'required|max:100|unique:hospital,nombre_hospital,'.$id,
        'direccion' => 'required|max:255',
        'ciudad' => 'required|max:60',
        'telefono1' => 'required|numeric|max:9999999999',            
        'estado' => 'required'
        ];
         
                

        $input = [
            'nombre_hospital' => strtoupper($request['nombre_hospital']),
            'direccion' => strtoupper($request['direccion']),
            'ciudad' => strtoupper($request['ciudad']),
            'telefono1' => $request['telefono1'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Hospital::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/hospital-management');
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
    }
*/
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
            'nombre_hospital' => $request['nombreunidad'],
            
            ];

       $hospitales = $this->doSearchingQuery($constraints);
     
       return view('hospital-mgmt/index', ['hospitales' => $hospitales, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Hospital::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(5);
    }

    private function validateInput($request) {
        $messages = [
        'nombre_hospital.required' => 'Agrega el nombre del hospital.',
        'nombre_hospital.max' =>'El nombre del hospital no puede ser mayor a :max caracteres.',
        'nombre_hospital.unique' =>'El nombre del hospital ya existe.',
        'ciudad.required' => 'Agrega la ciudad.',
        'ciudad.max' => 'la ciudad no puede ser mayor a :max caracteres.',
        'direccion.required' => 'Agrega la dirección.',
        'direccion.max' => 'la direccion no puede ser mayor a :max caracteres.',
        'telefono1.required' => 'Agrega el teléfono.',
        'telefono1.max' =>'El teléfono no puede ser mayor a 10 caracteres.',
        'telefono1.numeric' =>'El telefono debe ser numerico.',
        'estado.required' => 'Agrega el estado.',
  
        ];

        $constraints = [
        'nombre_hospital' => 'required|max:100|unique:hospital',
        'direccion' => 'required|max:255',
        'ciudad' => 'required|max:60',
        'telefono1' => 'required|numeric|max:9999999999',            
        ];

        $this->validate($request, $constraints, $messages);

    }

    
}
