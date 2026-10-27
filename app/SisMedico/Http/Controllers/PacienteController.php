<?php

namespace Sis_medico\Http\Controllers;

use App\Modules\ClinicalCare\Application\Data\PatientSearchResult;
use App\Modules\ClinicalCare\Application\Queries\SearchPatientsByName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Agenda_archivo;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Familia;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\Pais;
use Sis_medico\Principio_Activo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Storage;

class PacienteController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20)) == false) {
            return true;
        }
    }

    private function rol_new($opcion)
    {
        //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {

            return true;

        }

    }

    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $paciente = DB::table('paciente')->where('id', '!=', '9999999999')
            ->paginate(25);
        //dd($paciente);

        return view('paciente/index', ['paciente' => $paciente]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('paciente/create');
    }

    public function search(Request $request)
    {
        $nombre2   = "";
        $apellido2 = "";
        $apellidos = explode(" ", $request['apellidos']);
        $nombres   = explode(" ", $request['nombres']);

        $sapellidos = '%';
        $snombres   = '%';

        foreach ($apellidos as $value) {
            $sapellidos = $sapellidos . $value . '%';
        }

        foreach ($nombres as $value) {
            $snombres = $snombres . $value . '%';
        }

        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellidos'],
            'nombre1'   => $request['nombres'],
            'correo'   => $request['correo'],
        ];

        $paciente = DB::table('paciente')->where('paciente.id', '!=', '9999999999');

        if (!is_null($request['id'])) {
            $paciente = $paciente->where('paciente.id', 'LIKE', '%' . $request['id'] . '%');
        }

        if (!is_null($request['apellidos'])) {
            $paciente = $paciente->where(DB::Raw('CONCAT(paciente.apellido1, paciente.apellido2)'), 'like', $sapellidos);
        }

        if (!is_null($request['nombres'])) {
            $paciente = $paciente->where(DB::Raw('CONCAT(paciente.nombre1, paciente.nombre2)'), 'like', $snombres);
        }

        if (!is_null($request['correo'])) {
            $paciente = $paciente->join('users as u','paciente.id','u.id')->where('u.email', 'like', '%' . $request['correo'] . '%')->select('paciente.*', 'u.email');
        }

         //dd($paciente->get());

        $paciente = $paciente->paginate(25);

        return view('paciente/index', ['paciente' => $paciente, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query  = Paciente::query();
        $fields = array_keys($constraints);
        $index  = 0;
        $index2 = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->orwhere($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
            }

            $index2++;
        }
        return $query->where('id', '!=', '9999999999')->paginate(10);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        seguro::create([
            'nombre'          => $request['nombre'],
            'descripcion'     => $request['descripcion'],
            'tipo'            => $request['tipo'],
            'color'           => $request['color'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->intended('/form_enviar_seguro');
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'nombre'      => 'required|max:60|unique:seguros',
            'descripcion' => 'required',
            'tipo'        => 'required',
            'color'       => 'required|unique:seguros',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $paciente = Paciente::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($paciente == null) {
            return redirect()->intended('/paciente');
        }

        $user_aso     = User::find($paciente->id_usuario);
        $repre_opc    = Paciente_Familia::where('id_paciente', $id)->first();
        $paises       = pais::all();
        $seguros      = Seguro::all();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();
        $copia_cedula = Paciente_Biopsia::where('id_paciente', $id)->where('estado', '3')->first();

        //return view('paciente/edit', ['paciente' => $paciente])->with('paises',$paises)->with('seguros',$seguros)->with('rolusuario', $rolusuario);
        return view('paciente/edit', ['paciente' => $paciente, 'alergiasxpac' => $alergiasxpac, 'paises' => $paises, 'seguros' => $seguros, 'user_aso' => $user_aso, 'repre_opc' => $repre_opc, 'copia_cedula' => $copia_cedula]);
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
        $paciente   = Paciente::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $usuario_prin = User::find($id);

        $mensajes = [
            'nombre1.required'          => 'Agrega el primer nombre.',
            'nombre1.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'        => 'Agrega el primer apellido.',
            'apellido1.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            //'apellido2.required'        => 'Agrega el segundo apellido.',
            'apellido2.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'id.unique'                 => 'La cédula ya se encuentra registrada.',
            'id.required'               => 'Agrega la cédula.',
            'id.max'                    => 'La cédula no puede ser mayor a :max caracteres.',
            'id_pais.required'          => 'Agrega el país.',
            'ciudad.required'           => 'Agrega la ciudad.',
            'ciudad.max'                => 'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required'        => 'Agrega la direccion.',
            'direccion.max'             => 'La direccion no puede ser mayor a :max caracteres.',
            'telefono1.required'        => 'Agrega el teléfono del domicilio',
            'telefono1.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono1.numeric'         => 'El telefono del domicilio debe ser numérico.',
            'telefono2.required'        => 'Agrega el teléfono celular.',
            'telefono2.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'telefono2.numeric'         => 'El telefono celular debe ser numérico.',
            'id_seguro.required'        => 'Selecciona el seguro.',
            'fecha_nacimiento.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'fecha_val.required'        => 'Agrega la fecha de validación.',
            'cod_val.required'          => 'Agrega el código de validación.',
            'sexo.required'             => 'Selecciona el sexo.',
            'estadocivil.required'      => 'Selecciona el estado civil.',
            'gruposanguineo.required'   => 'Agrega el grupo sanguineo.',
            'gruposanguineo.max'        => 'El grupo sanguineo no puede ser mayor a :max caracteres.',
            'alergias.max'              => 'Las alergias no pueden ser mayor a :max caracteres.',
            'parentesco.required'       => 'Selecciona el Parentesco.',
            'referido.max'              => 'El referido no puede ser mayor a :max caracteres.',
            'ocupacion.required'        => 'Ingresa la ocupación.',
            'lugar_nacimiento.required' => 'Ingresa el lugar de nacimiento.',
            'ocupacion.max'             => 'La ocupación no pueden ser mayor a :max caracteres.',
            'lugar_nacimiento.max'      => 'El lugar de nacimiento no pueden ser mayor a :max caracteres.',
        ];

        $constraints = [
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'max:60',
            'id'               => 'required|max:10|unique:paciente,id,' . $id,
            'id_pais'          => 'required',
            //'ciudad'           => 'required|max:60',
            //'direccion'        => 'required|max:255',
            'telefono1'        => 'required|max:50',
            'telefono2'        => 'required|max:50',
            'id_seguro'        => 'required',
            'fecha_nacimiento' => 'required|date',
            'sexo'             => 'required',

            //'estadocivil'      => 'required',
            /*'gruposanguineo' => 'required|max:255',*/
            //'ocupacion'        => 'required|max:60',
            //'lugar_nacimiento' => 'required|max:255',
            /*'alergias' => 'max:255', */
            'parentesco'       => 'required',
            'referido'         => 'max:255',
        ];

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();
        foreach ($alergiasxpac as $apac) {
            $apac->delete();
        }

        $alergia_txt = "";
        $ale_flag    = true;
        //dd($request->ale_list);
        if ($request->ale_list != null) {

            foreach ($request->ale_list as $ale) {
                $generico = Principio_Activo::find($ale);
                $pac_ale  = [

                    'id_paciente'         => $id,
                    'id_principio_activo' => $ale,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariomod'       => $idusuario,
                    'id_usuariocrea'      => $idusuario,

                ];
                Paciente_Alergia::create($pac_ale);
                if ($ale_flag) {
                    $alergia_txt = $generico->nombre;
                    $ale_flag    = false;
                } else {
                    $alergia_txt = $alergia_txt . '+' . $generico->nombre;
                }

            }
        }

        $cv  = $request['validacion_cv_msp'];
        $nc  = $request['validacion_nc_msp'];
        $sec = $request['validacion_sec_msp'];

        $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

        $input = [
            'id'                    => $request['id'],
            'nombre1'               => strtoupper($request['nombre1']),
            'nombre2'               => strtoupper($request['nombre2']),
            'apellido1'             => strtoupper($request['apellido1']),
            'apellido2'             => strtoupper($request['apellido2']),
            'id_pais'               => $request['id_pais'],
            'ciudad'                => strtoupper($request['ciudad']),
            'direccion'             => strtoupper($request['direccion']),
            'telefono1'             => $request['telefono1'],
            'telefono2'             => $request['telefono2'],
            'id_seguro'             => $request['id_seguro'],
            'fecha_nacimiento'      => $request['fecha_nacimiento'],
            'menoredad'             => $request['menoredad'],
            'sexo'                  => $request['sexo'],
            'vacuna'                => $request['vacuna'],
            'estadocivil'           => $request['estadocivil'],
            'fecha_val'             => $request['fecha_val'],
            'cod_val'               => $request['cod_val'],
            'validacion_cv_msp'     => $request['validacion_cv_msp'],
            'validacion_nc_msp'     => $request['validacion_nc_msp'],
            'validacion_sec_msp'    => $request['validacion_sec_msp'],
            'codigo_validacion_msp' => $codigo_validacion_msp,
            'gruposanguineo'        => strtoupper($request['gruposanguineo']),
            //'alergias' => strtoupper($request['alergias']),
            'referido'              => strtoupper($request['referido']),
            'ip_modificacion'       => $ip_cliente,
            'id_usuariomod'         => $idusuario,
            'parentesco'            => $request['parentesco'],
            'ocupacion'             => $request['ocupacion'],
            'lugar_nacimiento'      => $request['lugar_nacimiento'],
            'alergias'              => $alergia_txt,
            'mail_opcional'         => $request['email_opc'],
            'religion'              => $request['religion'],
        ];

        $this->validate($request, $constraints, $mensajes);

        if ($usuario_prin != array()) //adicional actualiza a usuarios
        {

            if ($paciente->id != $paciente->id_usuario) {
                $mail = $usuario_prin->email;
            } else {
                $mail = $request['email'];
            }

            $mensajes2 = [
                'email.unique'   => 'El Email ya se encuentra registrado.',
                'email.required' => 'Agrega el Email.',
                'email.max'      => 'El Email no puede ser mayor a :max caracteres.',
                'email.email'    => 'El Email tiene error en el formato.',
            ];

            array_push($mensajes, $mensajes2);

            $constraints2 = [
                'email' => 'required|email|max:191|unique:users,email,' . $id,
            ];

            $input2 = [
                'id'               => $request['id'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'id_pais'          => $request['id_pais'],
                'ciudad'           => strtoupper($request['ciudad']),
                'direccion'        => strtoupper($request['direccion']),
                'email'            => $mail,
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],

                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];

            array_push($constraints, $constraints2);

            $this->validate($request, $constraints, $mensajes);

            User::where('id', $id)->update($input2);

        }

        Paciente::where('id', $id)->update($input);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA DATOS DE PACIENTE",
            'dato_ant1'   => $paciente->id,
            'dato1'       => $request['id'],
            'dato_ant2'   => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato2'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            'dato_ant3'   => "SEGURO: " . $paciente->id_seguro . " PARTENTESCO: " . $paciente->parentesco,
            'dato3'       => "SEGURO: " . $request['id_seguro'] . " PARTENTESCO: " . $request['parentesco'],
        ]);

        //return redirect()->intended('/paciente');
        return "ok";
    }

    public function subir_imagen_usuario(Request $request)
    {
        $id       = $request['id_usuario_foto'];
        $reglas   = ['archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:900'];
        $mensajes = [

            'archivo.required' => 'Agrega una foto.',
            'archivo.image'    => 'Los archivos permitidos son: jpeg, png, jpg, gif y svg.',
            'archivo.mimes'    => 'Los archivos permitidos son: jpeg, png, jpg, gif y svg.',
            'archivo.max'      => 'El peso de la foto no puede ser mayor a :max KB.'];

        $this->validate($request, $reglas, $mensajes);

        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "img" . $id . "." . $extension;

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

        $rutadelaimagen = $nuevo_nombre;

        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $paciente = Paciente::find($id);

            $paciente->imagen_url      = $rutadelaimagen;
            $paciente->ip_modificacion = $ip_cliente;
            $paciente->id_usuariomod   = $idusuario;
            $r2                        = $paciente->save();

            $usuario = User::find($id);

            if ($usuario != array()) {

                $usuario->imagen_url      = $rutadelaimagen;
                $usuario->ip_modificacion = $ip_cliente;
                $usuario->id_usuariomod   = $idusuario;
                $r3                       = $usuario->save();

                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "ACTUALIZA DATOS DE PACIENTE",
                    'dato_ant1'   => $paciente->id,
                    'dato1'       => "ACTUALIZA IMAGEN",

                ]);

            }

            return redirect()->intended('/paciente');
        }
    }

    public function subir_copia(Request $request)
    {
        //return $request->all();
        $id       = $request['id_usuario_foto'];
        $reglas   = ['archivo' => 'required|mimes:jpeg,jpg,pdf|max:900'];
        $mensajes = [
            'archivo.required' => 'Agrega una foto.',
            'archivo.mimes'    => 'Los archivos permitidos son: jpeg, jpg, pdf.',
            'archivo.max'      => 'El peso de la foto no puede ser mayor a :max KB.',
        ];

        $this->validate($request, $reglas, $mensajes);

        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();

        $nuevo_nombre = "copia_" . $id . "." . $extension;
        //return $nombre_original;
        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

        $rutadelaimagen = $nuevo_nombre;

        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $copia_ced = Paciente_Biopsia::where('id_paciente', $id)->where('estado', '3')->first();

            if (is_null($copia_ced)) {
                Paciente_Biopsia::create([
                    'nombre'          => $rutadelaimagen,
                    'id_paciente'     => $id,
                    'estado'          => '3',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ]);
            } else {
                $copia_ced->update([
                    'nombre'          => $rutadelaimagen,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ]);
            }

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA COPIA CEDULA",
                'dato_ant1'   => $id,
                'dato1'       => "ACTUALIZA COPIA CEDULA",

            ]);

            return "ok";
        }

        return 0;
    }

    public function updatefamiliar(Request $request, $id)
    {

        //dd($request->all());
        $paciente   = Paciente::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
            'nombre1familiar.required'    => 'Agrega el primer nombre.',
            'nombre1familiar.max'         => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2familiar.max'         => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1familiar.required'  => 'Agrega el primer apellido.',
            'apellido1familiar.max'       => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2familiar.required'  => 'Agrega el segundo apellido.',
            'apellido2familiar.max'       => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'parentescofamiliar.required' => 'Selecciona el Parentesco.',
            'telefono3.required'          => 'Agrega el teléfono',
            'telefono3.max'               => 'El teléfono no puede ser mayor a :max caracteres.',
            'telefono3.numeric'           => 'El telefono debe ser numérico.',

        ];

        $constraints = [
            'nombre1familiar'    => 'required',
            //'nombre2familiar'    => 'max:60',
            'apellido1familiar'  => 'required',
            //'apellido2familiar'  => 'required|max:60',
            'parentescofamiliar' => 'required',
            'telefono3'          => 'required',
        ];

        $input = [
            'nombre1familiar'    => strtoupper($request['nombre1familiar']),
            'nombre2familiar'    => strtoupper($request['nombre2familiar']),
            'apellido1familiar'  => strtoupper($request['apellido1familiar']),
            'apellido2familiar'  => strtoupper($request['apellido2familiar']),
            'parentescofamiliar' => $request['parentescofamiliar'],
            'telefono3'          => $request['telefono3'],
            'cedulafamiliar'     => $request['cedulafamiliar'],
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
        ];

        $this->validate($request, $constraints, $mensajes);
        Paciente::where('id', $id)->update($input);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA DATOS DE PACIENTE",
            'dato_ant1'   => $paciente->id,
            'dato1'       => "ACTUALIZA FAMILIAR DE CONTACTO",
            'dato_ant2'   => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato2'       => strtoupper($request['nombre1familiar']) . " " . strtoupper($request['nombre2familiar']) . " " . strtoupper($request['apellido1familiar']) . " " . strtoupper($request['apellido2familiar']),

        ]);

        return "ok";
        //return redirect()->intended('/paciente');
    }

    public function guardar_principal(Request $request)
    {
        //$paciente   = Paciente::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $constraints = [
            'apellido1_2'        => 'required',
            //'apellido2_2' => 'max:60',
            'nombre1_2'          => 'required',
            //'nombre2_2'
            'ciudad_2'           => 'required',
            'direccion_2'        => 'required',
            'email_2'            => 'required|email|unique:users,email,' . $request->id_2,
            'fecha_nacimiento_2' => 'required',
            'id_2'               => 'required',
            //'id_pais_2' => 'required',
            'telefono1_2'        => 'required',
            'telefono2_2'        => 'required',
        ];
        //return $constraints;
        $mensajes = [
            'apellido1_2.required'        => 'Agrega el primer nombre.',
            'nombre1_2.required'          => 'Agrega el primer apellido.',
            'ciudad_2.required'           => 'Agrega la ciudad.',
            'email_2.required'            => 'Agrega el mail.',
            'email_2.email'               => 'Error en el formato del correo.',
            'fecha_nacimiento_2.required' => 'Agrega la fecha de nacimiento',
            'id_2.required'               => 'Agrega la cedula',
            'telefono1_2.required'        => 'Agrega el telefono',
            'direccion_2.required'        => 'Agrega la direccion',
            'telefono2_2.required'        => 'Agrega el telefono',
            'email_2.unique'              => 'Mail pertenece a otro usuario.',
        ];
        $this->validate($request, $constraints, $mensajes);

        $user = User::find($request->id_2);
        if (!is_null($user)) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA DATOS DE PACIENTE",
                'dato_ant1'   => $user->id,
                'dato1'       => "ACTUALIZA DATOS DE PRINCIPAL",
                'dato_ant2'   => $user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2,
                'dato2'       => strtoupper($request['apellido1_2']) . " " . strtoupper($request['apellido2_2']) . " " . strtoupper($request['nombre1_2']) . " " . strtoupper($request['nombre2_2']),
            ]);
            $input = [
                'apellido1'        => strtoupper($request['apellido1_2']),
                'apellido2'        => strtoupper($request['apellido2_2']),
                'nombre1'          => strtoupper($request['nombre1_2']),
                'nombre2'          => strtoupper($request['nombre2_2']),
                'ciudad'           => strtoupper($request['ciudad_2']),
                'direccion'        => strtoupper($request['direccion_2']),
                'email'            => $request['email_2'],
                'fecha_nacimiento' => $request['fecha_nacimiento_2'],
                'telefono1'        => $request['telefono1_2'],
                'telefono2'        => $request['telefono2_2'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
            $user->update($input);
        } else {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA DATOS DE PACIENTE",
                //'dato_ant1'   => $user->id,
                'dato_ant1'   => $request['id_2'],
                'dato1'       => "CREA DATOS DE PRINCIPAL",
                'dato_ant2'   => " ",
                'dato2'       => strtoupper($request['apellido1_2']) . " " . strtoupper($request['apellido2_2']) . " " . strtoupper($request['nombre1_2']) . " " . strtoupper($request['nombre2_2']),
            ]);
            $input = [
                'id'               => $request['id_2'],
                'apellido1'        => strtoupper($request['apellido1_2']),
                'apellido2'        => strtoupper($request['apellido2_2']),
                'nombre1'          => strtoupper($request['nombre1_2']),
                'nombre2'          => strtoupper($request['nombre2_2']),
                'ciudad'           => strtoupper($request['ciudad_2']),
                'direccion'        => strtoupper($request['direccion_2']),
                'email'            => $request['email_2'],
                'fecha_nacimiento' => $request['fecha_nacimiento_2'],
                'telefono1'        => $request['telefono1_2'],
                'telefono2'        => $request['telefono2_2'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
            ];
            User::create($input);
        }

        $paciente = Paciente::find($request->id_paciente_pr);
        if (!is_null($paciente)) {
            $paciente->update([
                'papa_mama'  => $request->papa_mama,
                'id_usuario' => $request->id_2,
            ]);
        }
        //Paciente::where('id', $id)->update($input);

        return "ok";
    }

    public function guardar_opcional(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $pac_opcional = Paciente_Familia::where('id_paciente', $request->id_pac)->first();

        if (!is_null($pac_opcional) || $request->id_3 != null) {
            $constraints = [
                'apellido1_3' => 'required',
                'nombre1_3'   => 'required',
                'id_3'        => 'required',
            ];

            $mensajes = [
                'apellido1_3.required' => 'Agrega el primer apellido.',
                'nombre1_3.required'   => 'Agrega el primer nombre.',
                'id_3.required'        => 'Agrega la cedula',
            ];

            $this->validate($request, $constraints, $mensajes);
        }

        if (!is_null($pac_opcional)) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA DATOS DE OPCIONAL",
                'dato_ant1'   => $request->id_paciente,
                'dato1'       => "ACTUALIZA DATOS DE OPCIONAL",
                'dato_ant2'   => $pac_opcional->apellido1 . " " . $pac_opcional->apellido2 . " " . $pac_opcional->nombre1 . " " . $pac_opcional->nombre2,
                'dato2'       => strtoupper($request['apellido1_3']) . " " . strtoupper($request['apellido2_3']) . " " . strtoupper($request['nombre1_3']) . " " . strtoupper($request['nombre2_3']),
            ]);
            $input = [
                'cedula_fam'      => $request->id_3,
                'apellido1'       => strtoupper($request['apellido1_3']),
                'apellido2'       => strtoupper($request['apellido2_3']),
                'nombre1'         => strtoupper($request['nombre1_3']),
                'nombre2'         => strtoupper($request['nombre2_3']),
                'papa_mama'       => $request['papa_mama_3'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $pac_opcional->update($input);
        } else {
            $input = [
                'cedula_fam'      => $request->id_3,
                'apellido1'       => strtoupper($request['apellido1_3']),
                'apellido2'       => strtoupper($request['apellido2_3']),
                'nombre1'         => strtoupper($request['nombre1_3']),
                'nombre2'         => strtoupper($request['nombre2_3']),
                'papa_mama'       => $request['papa_mama_3'],
                'id_paciente'     => $request->id_pac,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,

            ];
            Paciente_Familia::create($input);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA DATOS DE OPCIONAL",
                'dato_ant1'   => $request->id_paciente,
                'dato1'       => "CREA DATOS DE OPCIONAL",
                'dato_ant2'   => strtoupper($request['apellido1_3']) . " " . strtoupper($request['apellido2_3']) . " " . strtoupper($request['nombre1_3']) . " " . strtoupper($request['nombre2_3']),
                'dato2'       => " ",
            ]);
        }
        return "ok";
    }

    public function show($id)
    {
        //
    }

    public function buscaxnombre($id_doc, $fecha, $sala)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $paciente = DB::table('paciente')->where('id', '!=', '9999999999')
            ->paginate(10);

        return view('paciente/buscaxnombre', ['paciente' => $paciente, 'id_doc' => $id_doc, 'fecha' => $fecha, 'sala' => $sala]);
    }
    public function search2(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_doc = $request['id_doc'];
        $fecha  = $request['fecha'];
        $sala   = $request['sala'];

        $constraints = [
            'nombre1' => $request['nombres'],
        ];

        $nombres = explode(" ", $request['nombres']);

        $cantidad = count($nombres);

        $query = Paciente::where('id', '!=', '9999999999');

        if ($nombres[0] != "") {
            if ($cantidad > 0) {

                if ($cantidad >= 4) {
                    $query = $query->Where('nombre1', 'like', '%' . $nombres[0] . '%')
                        ->Where('nombre2', 'like', '%' . $nombres[1] . '%')
                        ->Where('apellido1', 'like', '%' . $nombres[2] . '%')
                        ->Where('apellido2', 'like', '%' . $nombres[3] . '%');
                }
                if ($cantidad == 3) {
                    $query = $query->where('nombre1', 'like', $nombres[0] . '%')
                        ->Where(function ($jquery2) use ($nombres) {
                            $jquery2->orwhere('nombre2', 'like', $nombres[1] . '%')
                                ->orwhere('apellido1', 'like', $nombres[1] . '%');})
                        ->Where(function ($jquery3) use ($nombres) {
                            $jquery3->orwhere('apellido1', 'like', $nombres[2] . '%')
                                ->orwhere('apellido2', 'like', $nombres[2] . '%');});
                }
                if ($cantidad == 2) {
                    $query = $query->Where(function ($jquery1) use ($nombres) {
                        $jquery1->orwhere('nombre1', 'like', $nombres[0] . '%')
                            ->orwhere('apellido1', 'like', $nombres[0] . '%');})
                        ->Where(function ($jquery2) use ($nombres) {
                            $jquery2->orwhere('nombre1', 'like', $nombres[1] . '%')
                                ->orwhere('nombre2', 'like', $nombres[1] . '%')
                                ->orwhere('apellido1', 'like', $nombres[1] . '%')->orwhere('apellido2', 'like', $nombres[1] . '%');});
                }
                if ($cantidad == 1) {
                    $query = $query->Where('nombre1', 'like', '%' . $nombres[0] . '%')
                        ->orWhere('nombre2', 'like', '%' . $nombres[0] . '%')
                        ->orWhere('apellido1', 'like', '%' . $nombres[0] . '%')
                        ->orWhere('apellido2', 'like', '%' . $nombres[0] . '%');

                }

                $query = $query->ORWHERERAW('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', ['%' . $request['nombres'] . '%']);

            }

        }
        $query = $query->paginate(10);

        return view('paciente/buscaxnombre', ['paciente' => $query, 'searchingVals' => $constraints, 'id_doc' => $id_doc, 'fecha' => $fecha, 'sala' => $sala]);
    }

    /** @return list<array{value: string, id: string}>|array{value: string, id: string} */
    public function nombre(Request $request, SearchPatientsByName $searchPatients): array
    {
        $validated = $request->validate([
            'term' => ['required', 'string', 'max:100'],
        ]);

        $patients = $searchPatients->handle($validated['term']);

        if ($patients->isEmpty()) {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $patients
            ->map(fn (PatientSearchResult $patient): array => $patient->toLegacyOption())
            ->all();
    }

    public function nombre2(Request $request)
    {

        $nombre_encargado = $request['nombre1'];

        $data = null;
        /*$nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = "";
        foreach ($nuevo_nombre as $value) {
        $seteo =  $seteo.$value.' ';
        }*/

        /*$query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
        FROM paciente
        WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '".$seteo."'
        ";*/

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) = '" . $nombre_encargado . "'
                  ";

        $nombres = DB::select($query);
        //return $query;
        if ($nombres != array()) {
            $data = $nombres[0]->id;
            return $data;
        } else {
            return '0';
        }

    }

    public function pacientexnombre(Request $request)
    {

        $nombre_encargado = $request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2;

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) = '" . $nombre_encargado . "'
                  ";

        $nombres = DB::select($query);

        if(count($nombres)>0){
            $admin = Paciente_Observaciones::where('id_paciente', $nombres[0]->id)->first();
            if(count($admin)>0){
                $nombres = [
                    'id' => $nombres[0]->id,
                    'observacion' => $admin->observacion
                ];
            }else{
                 $nombres = [
                    'id' => $nombres[0]->id,
                    'observacion' => ''
                ];
            }
           
        }
        if ($nombres != array()) {
            return json_encode($nombres);
        } else {
            return '0';
        }

    }

    public function historiaclinica($id_paciente)
    {
        $paciente = paciente::find($id_paciente);
        //dd($paciente);
        $empresa       = Empresa::find('1391707460001');
        $age           = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $general       = historiaclinica::where('id_paciente', $id_paciente)->orderBy('created_at', 'Desc')->get();
        $agenda_ultima = Agenda::where('id_paciente', $id_paciente)->get()->last();
        $view          = \View::make('paciente.historiaclinica', compact('paciente', 'empresa', 'age', 'general', 'agenda_ultima'))->render();
        $pdf           = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portraid');
        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        return $pdf->stream('historia_clinica_' . $paciente->apellido1 . '_' . $paciente->nombre1 . '.pdf');
    }

    //historial de imagenes en paciente
    public function historial_imagenes($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '1')->get();
        return view('hc_admision/video/historial_imagenes', ['paciente' => $paciente, 'historico' => $historico]);
    }

    //historial de docuemtentos en paciente
    public function historial_documentos($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '2')->get();
        return view('hc_admision/video/historial_documentos', ['paciente' => $paciente, 'historico' => $historico]);
    }

    //historial de docuemtentos en paciente
    public function historial_estudios($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '3')->get();
        return view('hc_admision/video/historial_estudios', ['paciente' => $paciente, 'historico' => $historico]);
    }

    public function consulta()
    {
        $opcion = '3'; //CONSULTA DE PACIENTES

        if ($this->rol_new($opcion)) {
            return response()->view('errors.404');
        }

        $paciente = Paciente::where('id', '!=', '9999999999')
            ->paginate(50);

        return view('paciente/consulta', ['paciente' => $paciente, 'cedula' => null, 'nombres' => null]);
    }
    public function search_consulta(Request $request)
    {
        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $nombres_sql = '';

        $paciente = Paciente::where('id', '!=', '9999999999');

        if (!is_null($nombres)) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {

                $paciente = $paciente->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(nombre1," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $paciente = $paciente->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql]);
                });
            }

        }

        if (!is_null($cedula)) {
            $paciente = $paciente->where('id', 'LIKE', '%' . $cedula . '%');
        }

        $paciente = $paciente->paginate(50);

        return view('paciente/consulta', ['paciente' => $paciente, 'cedula' => $cedula, 'nombres' => $nombres]);
    }

    public function historial_agenda($id_paciente)
    {
        $paciente  = paciente::find($id_paciente);
        $historico = DB::table('agenda as a')->join('agenda_archivo as ah', 'ah.id_agenda', '=', 'a.id')
            ->where('a.id_paciente', $id_paciente)->select('a.*', 'ah.ruta', 'ah.archivo', 'ah.tipo_documento', 'ah.texto', 'ah.id as ahid')
            ->where('ah.archivo', '<>', null)
            ->orderBy('a.created_at', 'desc')->get();
        $historico2 = DB::table('agenda as a')->join('agenda_archivo as ah', 'ah.id_agenda', '=', 'a.id')
            ->where('a.id_paciente', $id_paciente)->select('a.*', 'ah.ruta', 'ah.archivo', 'ah.tipo_documento', 'ah.texto', 'ah.id as ahid')
            ->where('ah.texto', '<>', null)
            ->orderBy('a.created_at', 'desc')->get();

        return view('paciente/historia_agenda', ['paciente' => $paciente, 'historico' => $historico, 'historico2' => $historico2]);
    }

    public function stream_image($id)
    {
        $archivo = Agenda_archivo::find($id);
        $agenda  = Agenda::find($archivo->id_agenda);
        //dd($archivo);

        $view = \View::make('paciente.imagen_paciente', compact('archivo', 'agenda'))->render();
        //return $view;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portraid');

        return $pdf->stream('imagen' . $agenda->paciente->apellido1 . '_' . $agenda->paciente->nombre1 . '.pdf');
    }

    public function ver_copia_cedula($cedula)
    {
        $copia_ced = Paciente_Biopsia::where('id_paciente', $cedula)->where('estado', 3)->first();
        if (!is_null($copia_ced)) {
            $pathtoFile = storage_path() . '/app/avatars/' . $copia_ced->nombre;
            return response()->file($pathtoFile, [
                'Content-Disposition' => 'inline; filename="' . 'D_ANEXOS.pdf' . '"'
            ]);
        }
        return "no cargado";

    }


    public function guardarObservacionAdministrativa(Request $request){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd('ok');

        $id_paciente = $request['id_paciente'];

        $busqueda = Paciente_Observaciones::where('id_paciente', $request['id_paciente'])->first();

        $creacrion = [
                'id_paciente'     => $id_paciente,
                'observacion'     => $request['observacion_admin'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
        ];

        if(count($busqueda)>0){
            Paciente_Observaciones::where('id_paciente', $id_paciente)->update($creacrion);
            
        }else{
            Paciente_Observaciones::create($creacrion);
        }
        return 'ok';
    }

}
