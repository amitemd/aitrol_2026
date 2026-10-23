<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;
use Sis_medico\Sala;
use Sis_medico\Empresa;
use Sis_medico\Empleado;
use Sis_medico\Empleado_documento;



class EmpleadosController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        if(in_array($rolUsuario, array(1, 8)) == false){
          return redirect()->intended('/');
        }

    } 

    public function index()
    {
        $this->rol();
        $empleados = DB::table('empleado')->where('estado','!=','0')
        ->paginate(30);

        return view('rrhh/empleados/index', ['empleados' => $empleados]);
    }

    public function documentos($id)
    {
        $this->rol();
        $empleado = Empleado::find($id);
        $documentos = Empleado_documento::where('id_empleado',$id)->get();

        return view('rrhh/empleados/documentos', ['empleado' => $empleado, 'documentos' => $documentos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->rol();

        $paises = Pais::all();
        return view('rrhh/empleados/create',['paises' => $paises] );
    }

    public function edit($id)
    {
        $this->rol();
        $empleado = Empleado::find($id);

        if ($empleado == null || count($empleado) == 0) {
            return redirect()->intended('/empleados');
        }

        $usuario = User::find($id);
        $paises = Pais::all();
        return view('rrhh/empleados/edit',['empleado' => $empleado, 'paises' => $paises, 'usuario' => $usuario ] );
    }

     public function search(Request $request) 
    {
        
        $constraints = [
            'id' => $request['id'],
            'apellido1' => $request['apellidos'],
            'nombre1' => $request['nombres'],
            
            ];

            $constraints2 = [
            'apellido2' => $request['apellidos'],
            'nombre2' => $request['nombres'],   
            ];
       $paciente = $this->doSearchingQuery($constraints, $constraints2);
    
      return view('paciente/index', ['paciente' => $paciente, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $constraints2) {
        $query = Paciente::query();
        $fields = array_keys($constraints);
        $fields2 = array_keys($constraints2);
        $index = 0;
        $index2 = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint2 != null) {
                $query = $query->orwhere( $fields2[$index2], 'like', '%'.$constraint2.'%');
            }

            $index2++;
        }
        return $query->where('id','!=','9999999999')->paginate(10);
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
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $empleado = Empleado::findOrFail($id);
        $usuario=User::find($id);

        $this->ValidateEmpleadoA($request);

        $this->ValidaUsuarioActualizar($request);

        $input = [
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'id_pais' => $request['id_pais'],
            'lugar_nacimiento' => $request['lugar_nacimiento'],
            'direccion' => $request['direccion'],
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'estadocivil' => $request['estadocivil'],
            'licencia' => $request['licencia'],
            'auto' => $request['auto'],
            'visa_trabajo' => $request['visa_trabajo'],
            'contacto' => $request['contacto'],
            'parentesco' => $request['parentesco'],
            'telefono3' => $request['telefono3'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            ];

        $inputus_a = [
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'id_pais' => $request['id_pais'],
            'ciudad' => $request['lugar_nacimiento'],
            'direccion' => $request['direccion'],
            'email' => $request['email'],
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            ];    


        $empleado->update($input);
        
        $usuario->update($inputus_a);    


        return redirect('/empleados');
        
    }

    public function valida_archivo(Request $request)
    {

        $reglas = ['archivo' => 'mimes:pdf|max:1000',];
        $mensajes = [

            'archivo.required' => 'Agrega un archivo.',
            'archivo.mimes' => 'El archivo a seleccionar debe ser *.pdf.',
            'archivo.max' =>'El peso del archivo no puede ser mayor a :max KB.', ];

        $this->validate($request, $reglas, $mensajes);     

    }    
        
    public function subir_archivo_validacion(Request $request, $id_historia, $id_archivo)
    {
        $id=$request['id'];
            
        
        $nombre_original=$request['archivo']->getClientOriginalName();
        
        $extension=$request['archivo']->getClientOriginalExtension();
               
        

        
        $nuevo_nombre="hc_".$id."_VRF_".$id_historia."_".$id_archivo.".".$extension;
       
        $r1=Storage::disk('hc')->put($nuevo_nombre,  \File::get($request['archivo']));
            
        if ($r1){
               
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $archivo_historico=Archivo_historico::find($id_archivo);
            
            $archivo_historico->archivo=$nuevo_nombre;
            $archivo_historico->ip_modificacion=$ip_cliente;
            $archivo_historico->id_usuariomod=$idusuario;
            $r2=$archivo_historico->save();


            }    
            
            
    }
           


    public function load($name) {
         $path = storage_path().'/app/hc/'.$name;
        if (file_exists($path)) {

            return Response::make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$name.'"'
            ]);
        }
    }

    public function show($id)
    {
        //
    }

    public function busca_principal($id_paciente, $id_usuario, $historia)
    {   
            $paciente = Paciente::find($id_paciente);
            if($id_usuario!='0'){
                $user_aso = User::find($id_usuario);    
            }else{
                $user_aso = User::find($paciente->id_usuario);
            }
            
                
                
        return view('admisiones.principal',['paciente' => $paciente, 'user_aso' => $user_aso, 'historia' => $historia]);   
        
    }

    
    public function buscar_usuario($id_usuario)
    {   

        $user_aso = User::find($id_usuario);
        if(!is_null($user_aso)){
            return $user_aso;    
        }else{
            return "null";
        }


           
        
    }
    

    private function ValidateEmpleado(Request $request)
    {
        
        $mensajes = [
            'id.unique' => 'Empleado ya se encuentra registrado.',
            'id.required' => 'Agrega la cédula.',
            'id.max' =>'La cédula no puede ser mayor a :max caracteres.',
            'nombre1.required' => 'Agrega el primer nombre.',
            'nombre1.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.required' => 'Agrega el segundo nombre.',
            'nombre2.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required' => 'Agrega el primer apellido.',
            'apellido1.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required' => 'Agrega el segundo apellido.',
            'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',
            'id_pais.required' => 'Selecciona el pais.',
            'id_pais.exists' => 'Pais Seleccionado no existe.',
            'lugar_nacimiento.required' => 'Ingresa el lugar de nacimiento.',
            'lugar_nacimiento.max' =>'El lugar de nacimiento no pueden ser mayor a :max caracteres.',
            'telefono1.required' => 'Agrega el teléfono del domicilio',
            'telefono1.max' =>'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric' =>'El teléfono del domicilio debe ser numérico.',
            'telefono2.required' => 'Agrega el teléfono celular.',
            'telefono2.max' =>'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric' =>'El teléfono celular debe ser numérico.',
            'direccion.required' => 'Agrega la direccion.',
            'direccion.max' =>'La direccion no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required' =>'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date' =>'La fecha de nacimiento tiene formato incorrecto.',
            'estadocivil.required' => 'Selecciona el estado civil.',
            'estadocivil.in' => 'Selecciona el estado civil correcto.',
            'licencia.required' => 'Selecciona si tiene licencia.',
            'licencia.in' => 'Selecciona si tiene licencia.',
            'auto.required' => 'Selecciona si tiene auto.',
            'auto.in' => 'Selecciona si tiene auto.',
            'visa_trabajo.required' => 'Agrega la visa de trabajo.',
            'visa_trabajo.max' =>'La visa de trabajo no puede ser mayor a :max caracteres.',
            'contacto.required' => 'Agrega contacto.',
            'contacto.max' =>'El contacto no puede ser mayor a :max caracteres.',
            'parentesco.required' => 'Selecciona el Parentesco.',
            'telefono3.required' => 'Agrega el teléfono',
            'telefono3.max' =>'El teléfono no puede ser mayor a 10 caracteres.',
            'telefono3.numeric' =>'El teléfono debe ser numérico.',
            
        ]; 
 
        $constraints = [
            'id' => 'required|max:10|unique:empleado,id',
            'nombre1' => 'required|max:60',
            'nombre2' => 'max:60',
            'apellido1' => 'required|max:60',
            'apellido2' => 'max:60', 
            'id_pais' => 'required|exists:pais,id',
            'lugar_nacimiento' => 'required|max:255',
            'direccion' => 'required|max:255',
            'telefono1' => 'required|max:30',
            'telefono2' => 'required|max:30',
            'fecha_nacimiento' => 'required|date',
            'estadocivil' => 'required|in:1,2,3,4,5',
            'licencia' => 'required|in:SI,NO',
            'auto' => 'required|in:SI,NO',
            'visa_trabajo' => 'max:255',
            'contacto' => 'required|max:255',
            'parentesco' => 'required',
            'telefono3' => 'required|max:30',
        ]; 


        $this->validate($request, $constraints, $mensajes);    

    }
    private function ValidateEmpleadoA(Request $request)
    {
        
        $mensajes = [
            'id.unique' => 'Empleado ya se encuentra registrado.',
            'id.required' => 'Agrega la cédula.',
            'id.max' =>'La cédula no puede ser mayor a :max caracteres.',
            'nombre1.required' => 'Agrega el primer nombre.',
            'nombre1.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.required' => 'Agrega el segundo nombre.',
            'nombre2.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required' => 'Agrega el primer apellido.',
            'apellido1.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required' => 'Agrega el segundo apellido.',
            'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',
            'id_pais.required' => 'Selecciona el pais.',
            'id_pais.exists' => 'Pais Seleccionado no existe.',
            'lugar_nacimiento.required' => 'Ingresa el lugar de nacimiento.',
            'lugar_nacimiento.max' =>'El lugar de nacimiento no pueden ser mayor a :max caracteres.',
            'telefono1.required' => 'Agrega el teléfono del domicilio',
            'telefono1.max' =>'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric' =>'El teléfono del domicilio debe ser numérico.',
            'telefono2.required' => 'Agrega el teléfono celular.',
            'telefono2.max' =>'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric' =>'El teléfono celular debe ser numérico.',
            'direccion.required' => 'Agrega la direccion.',
            'direccion.max' =>'La direccion no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required' =>'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date' =>'La fecha de nacimiento tiene formato incorrecto.',
            'estadocivil.required' => 'Selecciona el estado civil.',
            'estadocivil.in' => 'Selecciona el estado civil correcto.',
            'licencia.required' => 'Selecciona si tiene licencia.',
            'licencia.in' => 'Selecciona si tiene licencia.',
            'auto.required' => 'Selecciona si tiene auto.',
            'auto.in' => 'Selecciona si tiene auto.',
            'visa_trabajo.required' => 'Agrega la visa de trabajo.',
            'visa_trabajo.max' =>'La visa de trabajo no puede ser mayor a :max caracteres.',
            'contacto.required' => 'Agrega contacto.',
            'contacto.max' =>'El contacto no puede ser mayor a :max caracteres.',
            'parentesco.required' => 'Selecciona el Parentesco.',
            'telefono3.required' => 'Agrega el teléfono',
            'telefono3.max' =>'El teléfono no puede ser mayor a 10 caracteres.',
            'telefono3.numeric' =>'El teléfono debe ser numérico.',
            
        ]; 
 
        $constraints = [
            'id' => 'required|max:10|unique:empleado,id,'.$request['id'],
            'nombre1' => 'required|max:60',
            'nombre2' => 'max:60',
            'apellido1' => 'required|max:60',
            'apellido2' => 'max:60', 
            'id_pais' => 'required|exists:pais,id',
            'lugar_nacimiento' => 'required|max:255',
            'direccion' => 'required|max:255',
            'telefono1' => 'required|max:30',
            'telefono2' => 'required|max:30',
            'fecha_nacimiento' => 'required|date',
            'estadocivil' => 'required|in:1,2,3,4,5',
            'licencia' => 'required|in:SI,NO',
            'auto' => 'required|in:SI,NO',
            'visa_trabajo' => 'max:255',
            'contacto' => 'required|max:255',
            'parentesco' => 'required',
            'telefono3' => 'required|max:30',
        ]; 


        $this->validate($request, $constraints, $mensajes);    

    }

    private function ValidaUsuarioCrear(Request $request){
        $mensajes2 = [
            'id.required' => 'Agregar la cédula',
            'id.unique' => 'Cédula ya existe',
            'email.unique' => 'El Email ya se encuentra registrado.',
            'email.required' => 'Agrega el Email.',
            'email.max' =>'El Email no puede ser mayor a :max caracteres.',
            'email.email' =>'El Email tiene error en el formato.',
        ];

        $constraints2 = [
            'id' => 'required|unique:users,id',
            'email' => 'required|email|max:191|unique:users,email',
        ]; 

        $this->validate($request, $constraints2, $mensajes2);   
    }

    private function ValidaUsuarioActualizar(Request $request){
        $mensajes2 = [
            'id.required' => 'Agregar la cédula',
            'id.unique' => 'Cédula ya existe',
            'email.unique' => 'El Email ya se encuentra registrado.',
            'email.required' => 'Agrega el Email.',
            'email.max' =>'El Email no puede ser mayor a :max caracteres.',
            'email.email' =>'El Email tiene error en el formato.',
        ];

        $constraints2 = [
            
            'email' => 'required|email|max:191|unique:users,email,'.$request['id'],
        ]; 

        $this->validate($request, $constraints2, $mensajes2);   
    }


    public function store(Request $request)
    {   
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $usuario=User::find($request['id']);

        $this->ValidateEmpleado($request);    
        
        if(!is_null($usuario)){
            
            $this->ValidaUsuarioCrear($request);

        }else{

            $this->ValidaUsuarioActualizar($request);
        }

        $input = [
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'id_pais' => $request['id_pais'],
            'lugar_nacimiento' => $request['lugar_nacimiento'],
            'direccion' => $request['direccion'],
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'estadocivil' => $request['estadocivil'],
            'licencia' => $request['licencia'],
            'auto' => $request['auto'],
            'visa_trabajo' => $request['visa_trabajo'],
            'contacto' => $request['contacto'],
            'parentesco' => $request['parentesco'],
            'telefono3' => $request['telefono3'],
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            ];

        $inputus_c = [
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'tipo_documento' => '1',
            'id_pais' => $request['id_pais'],
            'ciudad' => $request['lugar_nacimiento'],
            'direccion' => $request['direccion'],
            'email' => $request['email'],
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'password' => bcrypt($request['id']),
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'id_tipo_usuario' => 2,
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            ];

        $inputus_a = [
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'id_pais' => $request['id_pais'],
            'ciudad' => $request['lugar_nacimiento'],
            'direccion' => $request['direccion'],
            'email' => $request['email'],
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            ]; 

        if(!is_null($usuario)){
            
            $usuario->update($inputus_a);

        }else{

            User::create($inputus_c);
        }

        Empleado::create($input);

        return redirect('/empleados');
        
    }


}
