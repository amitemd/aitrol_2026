<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Especialidad;
use Sis_medico\Horario_Doctor;
use Sis_medico\Http\Requests\UsuarioRequest;
use Sis_medico\Log_usuario;
use Sis_medico\Pais;
use Sis_medico\TipoUsuario;
use Sis_medico\User;
use Sis_medico\User_espe;

class UserManagementController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-management';

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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    private function rolusuario_maximos()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 5, 4)) == false) {
            return true;
        }
    }
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $users        = User::paginate(15);
        $tipousuarios = tipousuario::all();

        return view('users-mgmt/index', ['users' => $users, 'tipousuarios' => $tipousuarios]);

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
        $id           = Auth::user()->id;
        $pais         = pais::all();
        $tipousuarios = tipousuario::all();
        $especialidad = especialidad::all();

        return view('users-mgmt/create')->with('pais', $pais)->with('tipousuarios', $tipousuarios)->with('especialidad', $especialidad);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioRequest $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $check = $request['lista'];
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');

        User::create([
            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'id_pais'          => $request['id_pais'],
            'ciudad'           => strtoupper($request['ciudad']),
            'direccion'        => strtoupper($request['direccion']),
            'telefono1'        => $request['telefono1'],
            'telefono2'        => $request['telefono2'],
            'ocupacion'        => strtoupper($request['ocupacion']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'id_tipo_usuario'  => $request['id_tipo_usuario'],
            'email'            => $request['email'],
            'color'            => $request['color'],
            'password'         => bcrypt($request['password']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
        ]);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "CREA NUEVO USUARIO",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            'dato_ant2'   => "TIPO USUARIO: " . $request['id_tipo_usuario'],
        ]);

        if ($request['id_tipo_usuario'] == 3) {
            foreach ($check as $value) {
                user_espe::create([
                    'usuid'           => $request['id'],
                    'espid'           => $value,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return redirect()->intended('/user-management');
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
        $rolusuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if ($user == null || $user == '') {
            return redirect()->intended('/user-management');
        }
        $especialidades = especialidad::all();
        $especialidad   = DB::table('user_espe')->where('usuid', '=', $id)->get();
        $paises         = pais::all();
        $tipousuarios   = tipousuario::all();
        return view('users-mgmt/edit', ['user' => $user])->with('paises', $paises)->with('tipousuarios', $tipousuarios)->with('rolusuario', $rolusuario)->with('especialidad', $especialidad)->with('especialidades', $especialidades)->with('id', $id);
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
        $user       = User::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
            'nombre1.required'          => 'Agrega el primer nombre.',
            'nombre1.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'        => 'Agrega el primer apellido.',
            'apellido1.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'        => 'Agrega el segundo apellido.',
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
            'telefono1.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'         => 'El telefono del domicilio debe ser numérico.',
            'telefono2.required'        => 'Agrega el teléfono celular.',
            'telefono2.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'         => 'El telefono celular debe ser numérico.',
            'ocupacion.required'        => 'Agrega la ocupación.',
            'ocupacion.max'             => 'La ocupación del usuario no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'id_tipo_usuario.required'  => 'Agrega el tipo del usuario.',
            'email.unique'              => 'El Email ya se encuentra registrado.',
            'email.required'            => 'Agrega el Email del usuario.',
            'email.max'                 => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'               => 'El Email tiene error en el formato.',
            'password.required'         => 'Agrega el password.',
            'password.min'              => 'El Password debe ser mayor a :min caracteres.',
            'password.confirmed'        => 'El Password y su confirmación no coinciden.',
        ];
        $constraints = [
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'id'               => 'required|max:10|unique:users,id,' . $id,
            'id_pais'          => 'required',
            'ciudad'           => 'required|max:60',
            'direccion'        => 'required|max:255',
            'telefono1'        => 'required|numeric|max:9999999999',
            'telefono2'        => 'required|numeric|max:9999999999',
            'ocupacion'        => 'required|max:60',
            'fecha_nacimiento' => 'required|date',
            'id_tipo_usuario'  => 'required',
            'email'            => 'required|email|max:191|unique:users,email,' . $id,

        ];

        $tipo_usuario = $request['id_tipo_usuario'];
        if (Auth::user()->id_tipo_usuario != 1) {
            $tipo_usuario = Auth::user()->id_tipo_usuario;
        }
        $input = [
            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'id_pais'          => $request['id_pais'],
            'color'            => $request['color'],
            'ciudad'           => strtoupper($request['ciudad']),
            'direccion'        => strtoupper($request['direccion']),
            'telefono1'        => $request['telefono1'],
            'telefono2'        => $request['telefono2'],
            'ocupacion'        => strtoupper($request['ocupacion']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'id_tipo_usuario'  => $tipo_usuario,
            'email'            => $request['email'],
            'estado'           => $request['estado'],
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
        ];
        $mpass = "";
        if ($request['password'] != null && strlen($request['password']) > 0) {
            $constraints['password'] = 'required|min:6|confirmed';
            $input['password']       = bcrypt($request['password']);
            $mpass                   = "ACTUALIZA CONTRASEÑA ";
        }
        $request['id_tipo_usuario'];

        $this->validate($request, $constraints, $mensajes);

        User::where('id', $id)
            ->update($input);

        $dato_ant2 = $user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2;
        $dato2     = strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']);
        $dato_ant3 = $mpass . "ESTADO: " . $user->estado . " TIPO USUARIO: " . $user->id_tipo_usuario;
        $dato3     = $mpass . "ESTADO: " . $request['estado'] . " TIPO USUARIO: " . $request['id_tipo_usuario'];
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA DATOS DE USUARIO",
            'dato_ant1'   => $user->id,
            'dato1'       => $request['id'],
            'dato_ant2'   => $dato_ant2,
            'dato2'       => $dato2,
            'dato_ant3'   => $dato_ant3,
            'dato3'       => $dato3,
        ]);
        $check = $request['lista'];
        if ($check != null) {
            user_espe::where('usuid', $id)->delete();
            if ($request['id_tipo_usuario'] == 3) {
                foreach ($check as $value) {
                    user_espe::create([
                        'usuid'           => $request['id'],
                        'espid'           => $value,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
            }
        }

        if (Auth::user()->id_tipo_usuario != 1) {
            return redirect()->intended('/dashboard');
        }
        return redirect()->intended('/user-management');
    }

    public function update_paciente_publico(Request $request)
    {
        $id         = Auth::user()->id;
        $user       = User::findOrFail(Auth::user()->id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
            'nombre1.required'          => 'Agrega el primer nombre.',
            'nombre1.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'        => 'Agrega el primer apellido.',
            'apellido1.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'        => 'Agrega el segundo apellido.',
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
            'telefono1.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'         => 'El telefono del domicilio debe ser numérico.',
            'telefono2.required'        => 'Agrega el teléfono celular.',
            'telefono2.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'         => 'El telefono celular debe ser numérico.',
            'ocupacion.required'        => 'Agrega la ocupación.',
            'ocupacion.max'             => 'La ocupación del usuario no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.unique'              => 'El Email ya se encuentra registrado.',
            'email.required'            => 'Agrega el Email del usuario.',
            'email.max'                 => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'               => 'El Email tiene error en el formato.',
            'password.required'         => 'Agrega el password.',
            'password.min'              => 'El Password debe ser mayor a :min caracteres.',
            'password.confirmed'        => 'El Password y su confirmación no coinciden.',
        ];
        $constraints = [
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'id'               => 'required|max:10|unique:users,id,' . $id,
            'id_pais'          => 'required',
            'ciudad'           => 'required|max:60',
            'direccion'        => 'required|max:255',
            'telefono1'        => 'required|numeric|max:9999999999',
            'telefono2'        => 'required|numeric|max:9999999999',
            'ocupacion'        => 'required|max:60',
            'fecha_nacimiento' => 'required|date',
            'email'            => 'required|email|max:191|unique:users,email,' . $id,

        ];

        $input = [
            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'id_pais'          => $request['id_pais'],
            'ciudad'           => strtoupper($request['ciudad']),
            'direccion'        => strtoupper($request['direccion']),
            'telefono1'        => $request['telefono1'],
            'telefono2'        => $request['telefono2'],
            'ocupacion'        => strtoupper($request['ocupacion']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'email'            => $request['email'],
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
        ];
        $mpass = "";
        if ($request['password'] != null && strlen($request['password']) > 0) {
            $constraints['password'] = 'required|min:6|confirmed';
            $input['password']       = bcrypt($request['password']);
            $mpass                   = "ACTUALIZA CONTRASEÑA ";
        }
        $request['id_tipo_usuario'];

        $this->validate($request, $constraints, $mensajes);

        User::where('id', $id)
            ->update($input);

        $dato_ant2 = $user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2;
        $dato2     = strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']);
        $dato_ant3 = $mpass . "ESTADO: " . $user->estado;
        $dato3     = $mpass . "ESTADO: " . $request['estado'];
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA DATOS DE USUARIO",
            'dato_ant1'   => $user->id,
            'dato1'       => $request['id'],
            'dato_ant2'   => $dato_ant2,
            'dato2'       => $dato2,
            'dato_ant3'   => $dato_ant3,
            'dato3'       => $dato3,
        ]);

        return redirect()->intended('/dashboard');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->intended('/user-management');
    }

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellido'],
        ];

        $constraints2 = [
            'apellido2' => $request['apellido'],
        ];

        $apellido2 = "";
        $apellidos = explode(" ", $request['apellido']);

        $apellido1 = $apellidos[0];

        if (count($apellidos) > 1) {
            $apellido2 = $apellidos[1];
        }
        if ($apellido1 == "") {
            $apellido1 = "";
        }

        $users = DB::table('users')->where(function ($query) use ($request, $apellido1) {
            $query->Where('apellido1', 'like', '%' . $apellido1 . '%')
                ->orWhere('apellido2', 'like', '%' . $apellido1 . '%');})
            ->where(function ($query) use ($request, $apellido2) {
                $query->Where('apellido1', 'like', '%' . $apellido2 . '%')
                    ->orWhere('apellido2', 'like', '%' . $apellido2 . '%');})
            ->where('id', 'LIKE', '%' . $request['id'] . '%');
        //->where('id', '!=', '9999999999');

        if ($request['id_tipo_usuario'] != null) {

            $users = $users->where('id_tipo_usuario', $request['id_tipo_usuario']);

        }

        $users = $users->paginate(15);

        $tipousuarios = tipousuario::all();

        return view('users-mgmt/index', ['users' => $users, 'searchingVals' => $constraints, 'tipousuarios' => $tipousuarios]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query   = User::query();
        $fields  = array_keys($constraints);
        $fields2 = array_keys($constraints2);
        $index   = 0;
        $index2  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint2 != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
            }

            $index++;
        }
        return $query->paginate(5);
    }

    private function validateInput($request)
    {

        $this->validate($request, []);

    }

    public function subir_imagen_usuario(Request $request)
    {

        $id       = $request['id_usuario_foto'];
        $reglas   = ['archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:90000'];
        $mensajes = [

            'archivo.required' => 'Agrega una foto.',
            'archivo.image'    => 'Los archivos permitidos son: jpeg, png, jpg, gif y svg.',
            'archivo.mimes'    => 'Los archivos permitidos son: jpeg, png, jpg, gif y svg.',
            'archivo.size'     => 'El peso de la foto no puede ser mayor a :max KB.',
        ];

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
            $usuario                  = User::find($id);
            $usuario->imagen_url      = $rutadelaimagen;
            $usuario->ip_modificacion = $ip_cliente;
            $usuario->id_usuariomod   = $idusuario;
            $r2                       = $usuario->save();
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA DATOS DE USUARIO",
                'dato_ant1'   => $id,
                'dato1'       => "ACTUALIZA IMAGEN",
            ]);
            if (Auth::user()->id_tipo_usuario == 1) {
                return redirect()->intended('/user-management');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

    }

    public function max(Request $request, $id)
    {

        $user       = User::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [

            'max_consulta'      => $request['max_consulta'],
            'max_procedimiento' => $request['max_procedimiento'],

            'ip_modificacion'   => $ip_cliente,

            'id_usuariomod'     => $idusuario,

        ];

        User::where('id', $id)
            ->update($input);

        return redirect()->intended('/agenda');

    }
    public function creahorario(Request $request, $id)
    {
        $user       = User::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $reglas = ['dia' => 'required ',
            'hora_ini'       => 'required',
            'hora_fin'       => 'required',
        ];

        $mensajes = [
            'dia.required'      => 'Selecciona el día.',
            'hora_ini.required' => 'Selecciona la hora de inicio.',
            'hora_fin.required' => 'Selecciona la hora de fin.',
        ];

        $this->validate($request, $reglas, $mensajes);

        if ($request['dia'] == 'TD') {
            for ($x = 1; $x < 6; $x++) {
                if ($x == 1) {
                    $dia = 'Lun.';
                } elseif ($x == 2) {
                    $dia = 'Mar.';
                } elseif ($x == 3) {
                    $dia = 'Mié.';
                } elseif ($x == 4) {
                    $dia = 'Jue.';
                } elseif ($x == 5) {
                    $dia = 'Vie.';
                }

                $this->validatehorario3($request, $id, $dia);
            }

            for ($y = 1; $y < 6; $y++) {

                if ($y == 1) {
                    $dia = 'Lun.';

                } elseif ($y == 2) {
                    $dia = 'Mar.';

                } elseif ($y == 3) {
                    $dia = 'Mié.';

                } elseif ($y == 4) {
                    $dia = 'Jue.';

                } elseif ($y == 5) {
                    $dia = 'Vie.';
                }

                $input = [
                    'dia'             => $dia,
                    'ndia'            => $y,
                    'hora_ini'        => $request['hora_ini'],
                    'hora_fin'        => $request['hora_fin'],
                    'id_doctor'       => $id,

                    'ip_creacion'     => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,

                ];

                Horario_Doctor::create($input);

            }

        } else {
            if ($request['dia'] == 'Lun.') {
                $ndia = 1;
            } elseif ($request['dia'] == 'Mar.') {
                $ndia = 2;
            } elseif ($request['dia'] == 'Mié.') {
                $ndia = 3;
            } elseif ($request['dia'] == 'Jue.') {
                $ndia = 4;
            } elseif ($request['dia'] == 'Vie.') {
                $ndia = 5;
            } elseif ($request['dia'] == 'Sáb.') {
                $ndia = 6;
            } elseif ($request['dia'] == 'Dom.') {
                $ndia = 7;
            }

            $this->validatehorario($request, $id);

            $input = [
                'dia'             => $request['dia'],
                'ndia'            => $ndia,
                'hora_ini'        => $request['hora_ini'],
                'hora_fin'        => $request['hora_fin'],
                'id_doctor'       => $id,

                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            Horario_Doctor::create($input);

        }

        return redirect()->intended('/horario');

    }

    private function validatehorario(Request $request, $id_doctor)
    {

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);

        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin    = date_format($fin, 'H:i:s');

        $dato = Horario_Doctor::where('id_doctor', $id_doctor)->where('dia', $request['dia'])
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN hora_ini and hora_fin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN hora_ini and hora_fin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(hora_ini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("hora_fin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cantidad = $dato->count();

        $reglas = [
            'hora_ini' => 'comparahoras:' . $request['hora_fin'],
            'hora_fin' => 'comparahoras:' . $request['hora_ini'],
            'dia'      => 'unique_doctor:' . $cantidad,

        ];

        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor'     => 'El rango de Horario ya se encuentra incluido .',
        ];

        $this->validate($request, $reglas, $mensajes);

    }

    private function validatehorario3(Request $request, $id_doctor, $dia)
    {

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);

        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin    = date_format($fin, 'H:i:s');

        $dato = Horario_Doctor::where('id_doctor', $id_doctor)->where('dia', $dia)
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN hora_ini and hora_fin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN hora_ini and hora_fin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(hora_ini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("hora_fin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cantidad = $dato->count();

        $reglas = [
            'hora_ini' => 'comparahoras:' . $request['hora_fin'],
            'hora_fin' => 'comparahoras:' . $request['hora_ini'],
            'dia'      => 'unique_doctor:' . $cantidad,

        ];

        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor'     => 'El rango de Horario ya se encuentra incluido .',
        ];

        $this->validate($request, $reglas, $mensajes);

    }

    public function editahorario(Request $request, $id)
    {

        $user       = User::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $horarios = Horario_Doctor::where('id_doctor', $id)->get();
        /* $reglas = ['dia' => 'required|in:LU,MA,MI,JU,VI,SA,DO,TD',
        'hora_ini' => 'required',
        'hora_fin' => 'required'
        ];

        $mensajes = [
        'dia.required' => 'Selecciona el día.',
        'dia.in' => 'Selecciona el día correcto.',
        'hora_ini.required' => 'Selecciona la hora de inicio.',
        'hora_fin.required' => 'Selecciona la hora de fin.',
        ];

        $this->validate($request, $reglas, $mensajes); */

        if (!is_null($horarios)) {
            foreach ($horarios as $horario) {

                $this->validatehorario2($request, $horario->id, $horario->id_doctor, $horario->dia);

                $estado = $request['estado' . $horario->id];
                if (is_null($estado)) {
                    $estado = 0;
                }

                $input = [
                    'hora_ini'        => $request['hora_ini' . $horario->id],
                    'hora_fin'        => $request['hora_fin' . $horario->id],
                    'estado'          => $estado,

                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,

                ];

                Horario_doctor::find($request['hid' . $horario->id])->update($input);
            }

        }

        return redirect()->intended('/horario');

    }

    private function validatehorario2(Request $request, $hid, $id_doctor, $dia)
    {

        if ($request['estado' . $hid] == 1) {
            $ini2 = date_create($request['hora_ini' . $hid]);
            $fin2 = date_create($request['hora_fin' . $hid]);

            $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
            $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

            $inicio = date_format($inicio, 'H:i:s');
            $fin    = date_format($fin, 'H:i:s');

            $dato = Horario_Doctor::where('id_doctor', $id_doctor)->where('dia', $dia)->where('id', '<>', $hid)
                ->where(function ($query) use ($request, $inicio, $fin) {
                    return $query->whereRaw("(('" . $inicio . "' BETWEEN hora_ini and hora_fin)")
                        ->orWhere(function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN hora_ini and hora_fin)");}
                        )
                        ->orWhere(function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("(hora_ini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                        })
                        ->orWhere(function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("hora_fin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                        });
                })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();

            $cantidad = $dato->count();

        } else { $cantidad = 0;}

        $reglas = [
            'hora_ini' . $hid => 'comparahoras:' . $request['hora_fin' . $hid] . '|unique_doctor:' . $cantidad,
            'hora_fin' . $hid => 'comparahoras:' . $request['hora_ini' . $hid] . '|unique_doctor:' . $cantidad,
        ];

        $mensajes = [
            'hora_ini' . $hid . '.comparahoras'  => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin' . $hid . '.comparahoras'  => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'hora_ini' . $hid . '.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
            'hora_fin' . $hid . '.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
        ];

        $this->validate($request, $reglas, $mensajes);

    }

    public function maxsearch($id)
    {
        if ($this->rolusuario_maximos()) {
            return response()->view('errors.404');
        }
        $usuario  = User::find($id);
        $horarios = Horario_Doctor::where('id_doctor', $id)->orderBy('ndia', 'asc')->get();

        return view('users-mgmt/editmax', ['id' => $id, 'usuario' => $usuario, 'horarios' => $horarios]);
    }

}
