<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\User;
use Sis_medico\pais;
use Sis_medico\TipoUsuario;
use Illuminate\Support\Facades\Auth;


class Tipo_UsuarioManagementController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/tipo_usuario-management';

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
        if(in_array($rolUsuario, array(1, 7)) == false){
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
        $tipo_usuarios = tipousuario::paginate(5);
      

        return view('tipo_usuario-mgmt/index', ['tipo_usuarios' => $tipo_usuarios]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('tipo_usuario-mgmt/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$clave = '$2y$10$fjJVYdQ0qzjn/Np7SDRvGOAp22CzqN1ZOkW8I38gyKgaL2rJteNC6';
        $contrasena = Crypt::decryptString($clave);
        dd($contrasena);*/
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        tipousuario::create([

            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return redirect()->intended('/tipo_usuario-management');
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
        $tipo_usuarios = tipousuario::find($id);
        // Redirect to user list if updating user wasn't existed
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('tipo_usuario-mgmt/edit', ['tipo_usuarios' => $tipo_usuarios]);
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
        $tipo_usuarios = tipousuario::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del tipo de usuario.',
        'nombre.max' =>'El nombre del tipo de usuario no puede ser mayor a :max caracteres.',
        'nombre.unique' =>'El nombre del tipo de usuario debe ser único.',
        'descripcion.required' => 'Agrega la descripción del tipo de usuario.',
        'descripcion.max' => 'la descripción del tipo de usuario no puede ser mayor a :max caracteres.',
        'estado.required' => 'Agrega el estado del tipo de usuario.',
  
        ];

        if ($request['nombre'] == $request['nombre_db']){
        $constraints = [
            'nombre' => 'required|max:30',
            'descripcion' => 'required|max:255',            
            'estado' => 'required'
            ];}
            else{
        $constraints = [
            'nombre' => 'required|max:30|unique:tipousuario',
            'descripcion' => 'required|max:255',            
            'estado' => 'required'
            ];
            }
                

        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        tipousuario::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/tipo_usuario-management');
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
            'nombre' => $request['nombre'],

            ];


       $tipo_usuarios = $this->doSearchingQuery($constraints);


      

        return view('tipo_usuario-mgmt/index', ['tipo_usuarios' => $tipo_usuarios, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = tipousuario::query();
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
        'nombre.required' => 'Agrega el nombre del tipo de usuario.',
        'nombre.max' =>'El nombre del tipo de usuario no puede ser mayor a :max caracteres.',
        'nombre.unique' =>'El nombre del tipo de usuario debe ser único.',
        'descripcion.required' => 'Agrega la descripción del tipo de usuario.',
        'descripcion.max' => 'la descripción del tipo de usuario no puede ser mayor a :max caracteres.',
  
        ];

        $this->validate($request, [
        'nombre' => 'required|max:30|unique:tipousuario',
        'descripcion' => 'required|max:255',


    ], $messages);

    }

    
}
