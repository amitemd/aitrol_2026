<?php

namespace Sis_medico\Http\Controllers\Apis;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use LDAP\Result;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Convenio;
use Sis_medico\Ct_Ciudad;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Doctor_Tiempo;
use Sis_medico\DoctorConsultorio;
use Sis_medico\DoctorConvenioEspecialidad;
use Sis_medico\DoctorDocumentos;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\HorarioDoctorApp;
use Sis_medico\Hospital;
use Sis_medico\Log_usuario;
use Sis_medico\LogDoctorDocumentos;
use Sis_medico\Paciente;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\User_espe;
use Sis_medico\Nivel;
use DateTime;
use DateInterval;
use DatePeriod;
use Sis_medico\Apps_Agenda;
use Sis_medico\BdPagoEnLinea;
use Sis_medico\Log_Agenda;
use Sis_medico\LogPagoEnLinea;
use Sis_medico\TransaccionPago;

class BdDrNetController extends Controller
{
    public function loginDrnet(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request->token;
        //Dr.N3t@pP
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }

        try {
            $email = $request->email;
            $password = $request->password;

            if (is_null($email) || is_null(trim($password)) || !isset($request['password'])) {
                return ["status" => "error", "msj" => "Campos usuario o contraseña vacios"];
            }
            $passwordUser = "";
            $usuario = User::where('email', $email)->where('estado', '1')->first();

            if (is_null($usuario)) {
                return ["status" => "error", "msj" => "Usuario no existe"];
            }

            if (!is_null($usuario)) {
                $passwordUser = $usuario->password;
            }

            $paciente = Paciente::find($usuario->id);
            $especialidad = null;
            if ($usuario->id_tipo_usuario == 3) {
                $espe = User_espe::where('usuid', $usuario->id)->get();
                $arr = array();
                foreach ($espe as $key => $value) {
                    array_push($arr, $value->espid);
                }
                $especialidad = $arr;
            }

            if (Hash::check($password, $passwordUser)) {
                $arrus = [
                    "id"                => $usuario->id,
                    "cedula"            => $usuario->id,
                    "nombres1"          => $usuario->nombre1,
                    "nombres2"          => $usuario->nombre2,
                    "apellidos1"        => $usuario->apellido1,
                    "apellidos2"        => $usuario->apellido2,
                    "email"             => $usuario->email,
                    "id_tipo_usuario"   => $usuario->id_tipo_usuario,
                    "image"             => url('bd/foto/usuario') . '/' . $usuario->id,
                    "sexo"              => is_null($paciente) ? "" : $paciente->sexo,
                    "birthdate"         => $usuario->fecha_nacimiento,
                    "password"          => $passwordUser,
                    "phone"             => $usuario->telefono1,
                    "address"           => $usuario->direccion,
                    "city_id"           => $usuario->id_ciudad_usuario,
                    "specialty_id"      => $especialidad
                ];
                return ["status" => "success", "msj" => "Credenciales Correctas", "data" => $arrus];
            } else {
                return ["status" => "error", "msj" => "Usuario o Contraseña Incorrecta"];
            }
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function ver_foto_usuario($id_usuario)
    {
        $usuario = User::find($id_usuario);
        if ($usuario->imagen_url == " " or is_null($usuario->imagen_url) or $usuario->imagen_url == "") {
            $pathtoFile = storage_path() . '/app/avatars/avatar.jpg';
        } else {
            $pathtoFile = storage_path() . '/app/avatars/' . $usuario->imagen_url;
        }
        // return $pathtoFile;
        if (file_exists($pathtoFile)) {
            return response()->file($pathtoFile);
        }

        return "no cargado";
    }

    public function datos_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            $usuario = User::find($id_usuario);

            if (is_null($usuario)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }

            $paciente = Paciente::where('id_usuario', $usuario->id)->first();
            $nombre_doc = "";
            if ($usuario->tipo_documento == 1) {
                $nombre_doc = "Cédula";
            }

            $data["id"]                = $usuario->id;
            $data["nombre1"]           = $usuario->nombre1;
            $data["nombre2"]           = $usuario->nombre2;
            $data["apellido1"]         = $usuario->apellido1;
            $data["apellido2"]         = $usuario->apellido2;
            $data["fecha_nacimiento"]  = $usuario->fecha_nacimiento;
            $data["direccion"]         = $usuario->direccion;
            $data["telefono"]          = $usuario->telefono1;
            $data["id_ciudad"]         = $usuario->id_ciudad_usuario;
            $data["ciudad"]            = $usuario->ciudad;
            $data["tipo_documento"]    = $usuario->tipo_documento;
            $data["nombre_doc"]        = $nombre_doc;
            $data["correo"]            = $usuario->email;
            $data["ocupacion"]         = $usuario->ocupacion;
            $data["sexo"]              = is_null($paciente) ? "" : $paciente->sexo;
            $data["id_tipo_usuario"]   = $usuario->id_tipo_usuario;
            $data["imagen_url"]        = url('bd/foto/usuario') . '/' . $usuario->id;
            $data["especialidad"]      = "";
            $data["id_especialidad"]   = "";

            return $data;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function verificar_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            $user =  User::where('id', $id_usuario)->where('estado', 1)->first();
            if (is_null($user)) {
                return ["status" => "error", "msj" => "Usuario no existe"];
            } else {
                return ["status" => "success", "msj" => "Usuario existe"];
            }
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function cambiar_contrasena(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $password_actual = $request['password_actual'];

            $user = User::find($id_usuario);
            if (!Hash::check($password_actual, $user->password)) {
                return ["status" => "error", "msj" => "Contraseña Incorrecta"];
            }
            if (trim($request['password_1']) == '') {
                return ["status" => "error", "msj" => "Contraseña 1 vacia"];
            }
            if (trim($request['password_2']) == '') {
                return ["status" => "error", "msj" => "Contraseña 2 vacia"];
            }
            if (trim($request['password_1']) !=  trim($request['password_2'])) {
                return ["status" => "error", "msj" => "Las contraseñas no coinciden"];
            }

            $user->password = bcrypt($request["password_1"]);
            $user->id_usuariomod = $id_usuario;
            $user->ip_modificacion = $id_usuario;
            $user->save();

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "CAMBIO DE CONTRASEÑA APP",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . ' ' . $user->apellido1),
                'dato_ant4'   => " PARENTESCO: Principal",
                'dato2'       => 'CREADO EN APP',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Contraseña Actualizada"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function crearUserDrnet(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            if (is_null($request['id_usuario'])) {
                return ["status" => "error", "msj" => "La cedula es requerida"];
            }
            if (strlen($request['id_usuario']) >= 13) {
                return ["status" => "error", "msj" => "La cedula es mayor a 13 digitos"];
            }
            $user_correo = User::where('email', $request['email'])->first();
            if (!is_null($user_correo)) {
                return ["status" => "error", "msj" => "El correo ya fue usado en otro usuario"];
            }
            $usuario = User::find($request['id_usuario']);
            if (!is_null($usuario)) {
                return ["status" => "error", "msj" => "El usuario ya existe"];
            }

            $nombre1 = $request['nombre1'];
            $apellido1 = $request['apellido1'];
            $email = $request['email'];
            $fecha_nacimiento = $request['fecha_nacimiento'];
            $sexo = $request['sexo'];
            $tipo_usuario = $request['id_tipo_usuario'];
            if (is_null($nombre1)) {
                return ["status" => "error", "msj" => "Nombre 1 vacio"];
            }
            if (is_null($apellido1)) {
                return ["status" => "error", "msj" => "Apellido 1 vacio"];
            }
            if (is_null($email)) {
                return ["status" => "error", "msj" => "Email vacio"];
            }
            if (is_null($fecha_nacimiento)) {
                return ["status" => "error", "msj" => "Fecha de nacimiento vacio"];
            }
            if (is_null($sexo)) {
                return ["status" => "error", "msj" => "Sexo vacio"];
            }
            $paciente = Paciente::find($request['id_usuario']);
            $nom_ciudad = null;
            $city = Ct_Ciudad::where('id', $request['ciudad'])->where('estado', '1')->first();
            if (!is_null($city)) {
                $nom_ciudad = $city->nombre;
            }
            $app = null;
            if ($tipo_usuario == 3) {
                $app = 1;
            }

            if (is_null($usuario)) {
                $arr_us = [
                    'id'               => $request['id_usuario'],
                    'email'            => $email,
                    'nombre1'          => strtoupper($nombre1),
                    'id_tipo_usuario'  => $tipo_usuario,
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($apellido1),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'estado'           => '1',
                    'telefono1'        => $request['telefono1'],
                    'direccion'        => $request['direccion'],
                    'ciudad'           => $nom_ciudad,
                    'tipo_documento'   => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'password'         => !is_null($request['password']) ? bcrypt($request['password']) : bcrypt($request['id_usuario']),
                    'id_usuariocrea'   => 'CREAUSER',
                    'id_usuariomod'    => 'CREAUSER',
                    'id_ciudad_usuario' => $request['ciudad'],
                    'imagen_url'       => ' ',
                    'ip_creacion'      => '::1',
                    'ip_modificacion'  => '::1',
                    'app'              => $app
                ];
                $id = User::insertGetId($arr_us);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO USUARIO APP DRNET",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP DRNET',
                ];
                Log_usuario::create($input_log);
            } else {
                $usUp = [
                    'email'            => $request['email'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['telefono1'],
                    'direccion'        => $request['direccion'],
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'ciudad'           => $nom_ciudad,
                    'id_ciudad_usuario' => $request['ciudad'],
                    'id_tipo_usuario'  => $tipo_usuario,
                    'id_usuariomod'    => 'CREAUSER',
                    'ip_modificacion'  => '::1',
                    'password'         => !is_null($request['password']) ? bcrypt($request['password']) : bcrypt($request['id_usuario']),

                ];
                $usuario->update($usUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZA USUARIO APP DRNET",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP DRNET',
                ];
                Log_usuario::create($input_log);
            }

            if (is_null($paciente)) {
                $input_pac = [
                    'id'                 => $request['id_usuario'],
                    'id_usuario'         => $request['id_usuario'],
                    'nombre1'            => strtoupper($nombre1),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($apellido1),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $fecha_nacimiento,
                    'ciudad'             => $nom_ciudad,
                    'sexo'               => $sexo,
                    'telefono1'          => $request['telefono1'],
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => 1,
                    'menoredad'          => 0,
                    'ip_creacion'        => '::1',
                    'ip_modificacion'    => '::1',
                    'id_usuariocrea'     => 'CREAUSER',
                    'id_usuariomod'      => 'CREAUSER',
                    'otro'               => 'APP DRNET',
                    'imagen_url'         => ' '
                ];
                Paciente::create($input_pac);
                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP DRNET",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP DRNET',
                ];
                Log_usuario::create($input_log);
            } else {
                $pacUp = [
                    'nombre1'            => strtoupper($request['nombre1']),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($request['apellido1']),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $request['fecha_nacimiento'],
                    'sexo'               => $request['sexo'],
                    'telefono1'          => $request['telefono1'],
                    'ciudad'             => $nom_ciudad,
                    'ip_modificacion'    => '::1',
                    'id_usuariomod'      => 'CREAUSER',
                ];
                $paciente->update($pacUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZAR NUEVO PACIENTE APP DRNET",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP DRNET',
                ];
                Log_usuario::create($input_log);
            }
            return ["status" => "success", "msj" => "Usuario creado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function updateUsuarioDrnet(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $id_usuario = $request['id_usuario'];
        $user = User::find($id_usuario);
        if (is_null($user)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }
        if (trim($request['email']) == '') {
            return ["status" => "error", "msj" => "Falta ingresar correo"];
        }
        if (trim($request['nombre1']) == '') {
            return ["status" => "error", "msj" => "Falta ingresar nombre1"];
        }
        if (trim($request['apellido1']) == '') {
            return ["status" => "error", "msj" => "Falta ingresar apellido1"];
        }
        if (is_null($request['fecha_nacimiento'])) {
            return ["status" => "error", "msj" => "Fecha de nacimiento vacio"];
        }
        if (is_null($request['sexo'])) {
            return ["status" => "error", "msj" => "Sexo vacio"];
        }
        $user_correo = User::where('email', $request['email'])->first();
        if ($user->id != $user_correo->id) {
            return ["status" => "error", "msj" => "El correo ya fue usado en otro usuario"];
        }
        $paciente = Paciente::find($id_usuario);
        $nom_ciudad = null;
        $city = Ct_Ciudad::where('id', $request['ciudad'])->where('estado', '1')->first();
        if (!is_null($city)) {
            $nom_ciudad = $city->nombre;
        }

        try {
            $user->nombre1   = strtoupper($request['nombre1']);
            $user->nombre2   = strtoupper($request['nombre2']);
            $user->apellido1 = strtoupper($request['apellido1']);
            $user->apellido2 = strtoupper($request['apellido2']);
            $user->telefono1 = $request['telefono1'];
            $user->direccion = $request['direccion'];
            $user->fecha_nacimiento = $request['fecha_nacimiento'];
            $user->email = $request['email'];
            $user->ciudad = $nom_ciudad;
            $user->id_ciudad_usuario = $request['ciudad'];
            $user->id_usuariomod = 'CREAUSER';
            $user->ip_modificacion = '::1';
            $user->save();

            if (!is_null($paciente)) {
                $paciente->nombre1   = strtoupper($request['nombre1']);
                $paciente->nombre2   = strtoupper($request['nombre2']);
                $paciente->apellido1 = strtoupper($request['apellido1']);
                $paciente->apellido2 = strtoupper($request['apellido2']);
                $paciente->telefono1 = $request['telefono1'];
                $paciente->fecha_nacimiento = $request['fecha_nacimiento'];
                $paciente->direccion = $request['direccion'];
                $paciente->sexo = $request['sexo'];
                $paciente->ciudad = $nom_ciudad;
                $paciente->id_usuariomod = 'CREAUSER';
                $paciente->ip_modificacion = '::1';
                $paciente->save();
            } else {
                $input_pac = [
                    'id'                 => $request['id_usuario'],
                    'id_usuario'         => $request['id_usuario'],
                    'nombre1'            => strtoupper($request['nombre1']),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($request['apellido1']),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $request['fecha_nacimiento'],
                    'ciudad'             => $nom_ciudad,
                    'sexo'               => $request['sexo'],
                    'telefono1'          => $request['telefono1'],
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => 1,
                    'menoredad'          => 0,
                    'ip_creacion'        => '::1',
                    'ip_modificacion'    => '::1',
                    'id_usuariocrea'     => 'CREAUSER',
                    'id_usuariomod'      => 'CREAUSER',
                    'otro'               => 'APP DRNET',

                ];
                Paciente::create($input_pac);
                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP DRNET",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP DRNET',
                ];
                Log_usuario::create($input_log);
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "ACTUALIZAR USUARIO Y PACIENTE APP DRNET",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato2'       => 'ACTUALIZADO EN APP DRNET',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Usuario Actualizado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }

    public function listadoEspecialidades(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $arr = array();
            $especialidades = Especialidad::get();
            foreach ($especialidades as $especialidad) {
                $data['id'] = $especialidad->id;
                $data['nombre'] = $especialidad->nombre;
                $data['descripcion'] = $especialidad->descripcion;
                array_push($arr, $data);
            }
            return ["status" => "success", "datos" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function eliminarUserDrnet(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $user = User::find($id_usuario);
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $user->estado = 0;
            $user->save();
            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "ELIMINAR USUARIO APP DRNET",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'ELIMINADO EN APP DRNET',
            ];
            Log_usuario::create($input_log);
            return ["status" => "success", "msj" => "Usuario Eliminado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function uploadPhoto(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $user = User::find($id_usuario);
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if (is_null($request['archivo'])) {
                return ["status" => "error", "msj" => "No se ha subido ninguna imagen"];
            }
            $nuevo_nombre = null;
            if (!is_null($request['archivo'])) {
                //$nombre_original = $request['archivo']->getClientOriginalName();
                $extension       = $request['archivo']->getClientOriginalExtension();
                $nuevo_nombre    = "img" . $id_usuario . "." . $extension;

                Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
            }
            $user->imagen_url = $nuevo_nombre;
            $user->save();

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "SUBIR FOTO PERFIL",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'SUBIR FOTO PERFIL',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Foto subida correctamente"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function registrarInfoGeneral(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $user = User::find($id_usuario);
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }

            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $doctor_tiempo = Doctor_Tiempo::where('id_doctor', $id_usuario)->first();
            if (is_null($doctor_tiempo)) {
                Doctor_Tiempo::create([
                    'id_doctor' => $id_usuario,
                    'tiempo'     => '1.5',
                    'precio'        => $request['precio'],
                    'descripcion_medico' => $request['descripcion_medico'],
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $doctor_tiempo->precio = is_null($request['precio']) ? $doctor_tiempo->precio : $request['precio'];
                $doctor_tiempo->descripcion_medico = is_null($request['descripcion_medico']) ? $doctor_tiempo->descripcion_medico : $request['descripcion_medico'];
                $doctor_tiempo->id_usuariomod = 'CREAUSER';
                $doctor_tiempo->ip_modificacion = '::1';
                $doctor_tiempo->save();
            }
            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "REGISTRO INFORMACION GENERAL",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'REGISTRO INFORMACION GENERAL PRECIO: ' . $request['precio'] . ' Descrip:' . $request['descripcion_medico'],
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Informacion general registrada"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function infoGeneralDoc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }
            $doctor_tiempo = Doctor_Tiempo::where('id_doctor', $id_usuario)->first();
            $data['id_doctor'] = is_null($doctor_tiempo) ? "" : $doctor_tiempo->id_doctor;
            $data['nombre1'] = $user->nombre1;
            $data['apellido1'] = $user->apellido1;
            $data['precio'] = is_null($doctor_tiempo) ? "" : $doctor_tiempo->precio;
            $data['descripcion_medico'] = is_null($doctor_tiempo) ? "" : $doctor_tiempo->descripcion_medico;

            return ["status" => "success", "data" => $data];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function listaCiudades(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $ciudades = Ct_Ciudad::where('estado', 1)->get();
            $arr = array();
            foreach ($ciudades as $ciudad) {
                $data['id'] = $ciudad->id;
                $data['nombre'] = $ciudad->nombre;
                array_push($arr, $data);
            }
            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function lista_doctores(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $list   = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('app', 1)->orderBy('nombre1', 'ASC')->get();
            $information = array();
            $fecha = $request['date'];
            $day = date('N', strtotime($fecha));

            foreach ($list as $l) {
                $valor_consulta = Doctor_Tiempo::where('id_doctor', $l->id)->first();
                $h = "";
                $price = !is_null($valor_consulta) ? $valor_consulta->precio : '0.00';
                $data['fecha'] = $fecha;
                $data['id_doctor'] = $l->id;
                //$cant_agendas = BdDrNetController::verificarAgenda($data);

                //if ($cant_agendas['status'] == 'success') {
                $shedule      = HorarioDoctorApp::where('id_doctor', $l->id)->where('tipo', 1)->where('ndia', $day)->where('estado', 1)->first();
                $caso_especial = Agenda::whereDate('fechaini', $request['date'])->where('estado', 1)->where('proc_consul', 2)->where('id_doctor1', $l->id)->first();
                if (!is_null($shedule)) {
                    $h = '' . substr($shedule->hora_ini, 0, 5) . ' - ' . substr($shedule->hora_fin, 0, 5);
                }

                if ($h != "" && is_null($caso_especial)) {
                    //if ($h != "") {
                    $pl = [
                        'name'      => $l->nombre1 . ' ' . $l->apellido1,
                        'id_doctor' => $l->id,
                        'price'     => $price,
                        'img'       => url('bd/foto/usuario') . '/' . $l->id
                    ];
                    array_push($information, $pl);
                }
                // }
            }
            return ["status" => "success", "data" => $information];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public static function verificarAgenda($data)
    {
        try {
            $fecha = $data['fecha'];
            $id_doctor = $data['id_doctor'];
            $validAgenda = Agenda::whereBetween('fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
                ->where('id_doctor1', $id_doctor)
                ->where('estado', 1)
                ->where('proc_consul', 0)
                ->where('observaciones', 'like', '%FUE PAGADO DESDE LA APP%')
                ->count();

            if ($validAgenda >= 3) {
                return ['status' => 'error', 'msj' => 'No es posible agendar en la fecha seleccionada porque se ha llegado al límite establecido de agendas para este doctor'];
            }
            return ['status' => 'success', 'msj' => 'Agenda ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function listado_consultorios(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $lista = DoctorConsultorio::where('doctor_consultorio.id_doctor', $id_usuario)
                ->where('doctor_consultorio.estado', 1)
                ->join('sala as s', 's.id', 'doctor_consultorio.id_sala')
                ->select('doctor_consultorio.*')
                ->get();

            $arr = array();
            foreach ($lista as $key => $value) {
                $data['id'] = $value->id;
                $data['nombre'] = $value->nombre;
                $data['direccion'] = $value->direccion;
                $data['latitud'] = $value->latitud;
                $data['longitud'] = $value->longitud;
                array_push($arr, $data);
            }
            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function crearConsultorios(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $hospital = Hospital::where('nombre_hospital', 'like', '%' . $id_usuario . '%')->first();
            if (is_null($hospital)) {
                $id_hospital = Hospital::insertGetId([
                    'nombre_hospital'   => $id_usuario . ' - ' . $user->nombre1 . ' ' . $user->apellido1,
                    'direccion'         => $user->direccion,
                    'telefono1'         => '1',
                    'id_usuariocrea'    => 'CREAUSER',
                    'id_usuariomod'     => 'CREAUSER',
                    'ip_creacion'       => '::1',
                    'ip_modificacion'   => '::1'
                ]);
            } else {
                $id_hospital = $hospital->id;
            }

            $id_sala = Sala::insertGetId([
                'nombre_sala'       => $request['nombre'],
                'id_hospital'       => $id_hospital,
                'id_usuariocrea'    => 'CREAUSER',
                'id_usuariomod'     => 'CREAUSER',
                'ip_creacion'       => '::1',
                'ip_modificacion'   => '::1'
            ]);

            $id_consultorio = DoctorConsultorio::insertGetId([
                'id_doctor'         => $id_usuario,
                'nombre'            => $request['nombre'],
                'direccion'         => $request['direccion'],
                'latitud'           => $request['latitud'],
                'longitud'          => $request['longitud'],
                'id_sala'           => $id_sala,
                'id_usuariocrea'    => 'CREAUSER',
                'id_usuariomod'     => 'CREAUSER',
                'ip_creacion'       => '::1',
                'ip_modificacion'   => '::1'
            ]);

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "REGISTRO CREAR CONSULTORIO",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'REGISTRO CREAR CONSULTORIO NOMBRE: ' . $request['nombre'] . ' ID_SALA: ' . $id_sala . ' ID_CONSULTORIO: ' . $id_consultorio,
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Consultorio creado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function editarConsultorio(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $consultorio = DoctorConsultorio::find($request['id_consultorio']);
            if (is_null($consultorio)) {
                return ["status" => "error", "msj" => "El consultorio no existe"];
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "REGISTRO UPDATE CONSULTORIO",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->apellido1) . ' REGISTRO ANTERIOR ' . $consultorio->nombre . ' DIREC. ' . $consultorio->direccion . ' lat. ' . $consultorio->latitud . ' lng.' . $consultorio->longitud,
                'dato2'       => 'REGISTRO UPDATE CONSULTORIO REGISTRO ACTUALIZADO: ' . $request['nombre'] . ' DIREC: ' . $request['direccion'] . ' lat: ' . $request['latitud'] . ' lng: ' . $request['longitud'],
            ];
            Log_usuario::create($input_log);

            $sala = Sala::find($consultorio->id_sala);
            if (!is_null($sala)) {
                $sala->nombre_sala = $request['nombre'];
                $sala->id_usuariomod = 'CREAUSER';
                $sala->ip_modificacion = '::1';
                $sala->save();
            }

            $consultorio->nombre = $request['nombre'];
            $consultorio->direccion = $request['direccion'];
            $consultorio->latitud = $request['latitud'];
            $consultorio->longitud = $request['longitud'];
            $consultorio->id_usuariomod = 'CREAUSER';
            $consultorio->ip_modificacion = '::1';
            $consultorio->save();

            return ["status" => "success", "msj" => "Consultorio editado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function eliminarConsultorios(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $consultorio = DoctorConsultorio::find($request['id_consultorio']);
            if (is_null($consultorio)) {
                return ["status" => "error", "msj" => "El consultorio no existe"];
            }
            $consultorio->estado = 0;
            $consultorio->id_usuariomod = 'CREAUSER';
            $consultorio->ip_modificacion = '::1';
            $consultorio->save();

            $sala = Sala::find($consultorio->id_sala);
            if (!is_null($sala)) {
                $sala->estado = 0;
                $sala->id_usuariomod = 'CREAUSER';
                $sala->ip_modificacion = '::1';
                $sala->save();
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "ELIMINAR CONSULTORIO",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'ELIMINAR CONSULTORIO NOMBRE: ' . $consultorio->nombre . ' ID_SALA: ' . $consultorio->id_sala . ' ID_CONSULTORIO: ' . $consultorio->id,
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Consultorio eliminado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function crearHorariosDoctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }
            $horario = HorarioDoctorApp::where('id_doctor', $id_usuario)->where('estado', 1)->where('tipo', 1)->delete();

            if ($request['detalle'] != '' or is_null($request['detalle'])) {
                foreach ($request['detalle'] as $det) {
                    if ($det['horario'] != [] or is_null($det['horario'])) {
                        foreach ($det['horario'] as $value) {
                            $id_horario = HorarioDoctorApp::insertGetId([
                                'id_doctor'         => $id_usuario,
                                'dia'               => substr($det['dia'], 0, 3) . '.',
                                'ndia'              => $det['id_dia'],
                                'hora_ini'          => $value['hora_inicio'],
                                'hora_fin'          => $value['hora_fin'],
                                'tipo'              => 1,
                                'id_usuariocrea'    => 'CREAUSER',
                                'id_usuariomod'     => 'CREAUSER',
                            ]);
                        }
                    }
                }
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "REGISTRO CREAR HORARIO",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'REGISTRO CREAR HORARIO ID_HORARIO',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Horario creado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function HorarioxDoctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $horario = HorarioDoctorApp::where('id_doctor', $id_usuario)->where('estado', 1)->where('tipo', 1)->get();
            $dias = ['1' => 'Lunes', '2' => 'Martes', '3' => 'Miercoles', '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sabado', '7' => 'Domingo'];

            $arr = [];
            foreach ($dias as $key => $value) {
                $dt = [];
                foreach ($horario as $value2) {
                    if ($value2->ndia == $key) {
                        $x['id_horario'] = $value2->id;
                        $x['hora_inicio'] = $value2->hora_ini;
                        $x['hora_fin'] = $value2->hora_fin;
                        array_push($dt, $x);
                    }
                }
                $aux['id_dia'] = $key;
                $aux['dia'] = $value;
                $aux['horario'] = $dt;
                array_push($arr, $aux);
            }

            return $arr;

            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function listaEspexDoctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $especialidades = User_espe::where('user_espe.usuid', $id_usuario)
                ->join('especialidad as e', 'e.id', 'user_espe.espid')
                ->select('user_espe.*', 'e.nombre as nombre_espe')
                ->get();

            $arr = [];
            foreach ($especialidades as $value) {
                $x['id'] = $value->id;
                $x['id_especialidad'] = $value->espid;
                $x['nombre'] = $value->nombre_espe;
                array_push($arr, $x);
            }
            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function creaEspexDoctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            if ($request['id_especialidad'] == '' or is_null($request['id_especialidad'])) {
                return ["status" => "error", "msj" => "Seleccione Especialidad"];
            }
            $x = [];
            $especialidad = User_espe::where('usuid', $id_usuario)->delete();
            foreach ($request['id_especialidad'] as $value) {
                User_espe::insertGetId([
                    'usuid'             => $id_usuario,
                    'espid'             => $value,
                    'id_usuariocrea'    => 'CREAUSER',
                    'id_usuariomod'     => 'CREAUSER',
                    'ip_creacion'       => '::1',
                    'ip_modificacion'   => '::1'
                ]);
                array_push($x, $value);
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "REGISTRO CREAR ESPECIALIDAD",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'REGISTRO CREAR ESPECIALIDAD ID_ESPECIALIDAD: ID_ESPECIALIDAD: ' . implode(' ', $x),
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Registro Guardado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function _____subirDocValidacion___(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }
            $tipo = $request['tipo'];
            if (trim($tipo) == '') {
                return ["status" => "error", "msj" => "Ingrese el tipo de documento"];
            }
            $nuevo_nombre = null;
            $nom_doc = null;
            if ($tipo == 1) {
                $nom_doc = 'Cedula_anverso';
            } else if ($tipo == 2) {
                $nom_doc = 'Cedula_reverso';
            } else if ($tipo == 3) {
                $nom_doc = 'Access';
            }
            // tipo 4 es el check de terminos y condiciones
            $documento = DoctorDocumentos::where('id_doctor', $id_usuario)->where('tipo', $tipo)->first();

            if ($tipo != 4) {
                if (is_null($request['archivo'])) {
                    return ["status" => "error", "msj" => "Ingrese el archivo"];
                }
                if ($tipo == 1 or $tipo == 2) {
                    $reglas   = ['archivo' => 'required|image|mimes:jpeg,png,jpg|max:90000'];
                    $mensajes = [

                        'archivo.required' => 'Agrega una foto.',
                        'archivo.image'    => 'Los archivos permitidos son: jpeg, png, jpg.',
                        'archivo.mimes'    => 'Los archivos permitidos son: jpeg, png, jpg.',
                        'archivo.size'     => 'El peso de la foto no puede ser mayor a :max KB.',
                    ];
                    $this->validate($request, $reglas, $mensajes);
                }

                if (!is_null($request['archivo'])) {
                    $extension       = $request['archivo']->getClientOriginalExtension();
                    $nuevo_nombre    = $nom_doc . '_' . $id_usuario . "." . $extension;

                    Storage::disk('archivos_doctores')->put($nuevo_nombre, \File::get($request['archivo']));
                }

                if (is_null($documento)) {
                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipo,
                        'url_doc'           => $nuevo_nombre,
                        'estado'            => '2',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    LogDoctorDocumentos::create([
                        'id_documento'      => $id_documento,
                        'estado'            => '2',
                        'observacion'       => 'CREA ARCHIVO EN REVISION',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    $input_log = [
                        'id_usuario'  => 'CREAUSER',
                        'ip_usuario'  => '::1',
                        'descripcion' => "REGISTRO CREAR DOCUMENTO DOCTOR" . $nom_doc,
                        'dato_ant1'   => $request['id_usuario'],
                        'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                        'dato2'       => 'REGISTRO CREAR DOCUMENTO DOCTOR' . $nom_doc,
                    ];
                    Log_usuario::create($input_log);
                } else {
                    $documento->update([
                        'url_doc' => $nuevo_nombre,
                        'estado' => '2',
                        'id_usuariomod' => 'CREAUSER',
                        'ip_modificacion' => '::1',
                    ]);
                }
            }
            if ($tipo == 4) {
                if (is_null($documento)) {
                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipo,
                        'estado'            => '3',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    LogDoctorDocumentos::create([
                        'id_documento'      => $id_documento,
                        'estado'            => '3',
                        'observacion'       => 'CREA ARCHIVO EN REVISION',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    $input_log = [
                        'id_usuario'  => 'CREAUSER',
                        'ip_usuario'  => '::1',
                        'descripcion' => "REGISTRO CREAR DOCUMENTO DOCTOR" . $nom_doc,
                        'dato_ant1'   => $request['id_usuario'],
                        'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                        'dato2'       => 'REGISTRO CREAR DOCUMENTO DOCTOR' . $nom_doc,
                    ];
                    Log_usuario::create($input_log);
                } else {
                    $documento->update([
                        'url_doc' => $nuevo_nombre,
                        'estado' => '2',
                        'id_usuariomod' => 'CREAUSER',
                        'ip_modificacion' => '::1',
                    ]);
                }
            }

            return ["status" => "success", "msj" => "Documento subido"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function subirDocValidacion(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }
            $cedula_a = $request['cedula_a'];
            $cedula_r = $request['cedula_r'];
            $access = $request['access'];
            $terminos = $request['terminos'];
            if ($cedula_a == null and $cedula_r == null and $access == null) {
                return ["status" => "error", "msj" => "No se ha seleccionado ningun archivo"];
            }
            if (!is_null($cedula_a)) {
                $tipoca = 1;
                $cedulaa = DoctorDocumentos::where('id_doctor', $id_usuario)->where('tipo', $tipoca)->first();
                if (is_null($cedulaa)) {
                    if ($request->hasFile('cedula_a') && $request->file('cedula_a')->isValid()) {
                        $ext_ceda       = $request['cedula_a']->getClientOriginalExtension();
                        Storage::disk('archivos_doctores')->put('Cedula_anverso_' . $id_usuario . "." . $ext_ceda, \File::get($request['cedula_a']));
                    } else {
                        return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                    }

                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipoca,
                        'url_doc'           => 'Cedula_anverso_' . $id_usuario . "." . $ext_ceda,
                        'estado'            => '2',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    LogDoctorDocumentos::create([
                        'id_documento'      => $id_documento,
                        'estado'            => '2',
                        'observacion'       => 'CREA ARCHIVO EN REVISION',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);
                } else {
                    if ($cedulaa->estado != 3) {
                        if ($request->hasFile('cedula_a') && $request->file('cedula_a')->isValid()) {
                            $ext_ceda       = $request['cedula_a']->getClientOriginalExtension();
                            Storage::disk('archivos_doctores')->put('Cedula_anverso_' . $id_usuario . "." . $ext_ceda, \File::get($request['cedula_a']));
                        } else {
                            return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                        }

                        $cedulaa->update([
                            'url_doc' => 'Cedula_anverso_' . $id_usuario . "." . $ext_ceda,
                            'estado' => '2',
                            'id_usuariomod' => 'CREAUSER',
                            'ip_modificacion' => ':1',
                        ]);
                    }
                }
            }

            if (!is_null($cedula_r)) {
                $tipocr = 2;
                $cedular = DoctorDocumentos::where('id_doctor', $id_usuario)->where('tipo', $tipocr)->first();
                if (is_null($cedular)) {
                    if ($request->hasFile('cedula_r') && $request->file('cedula_r')->isValid()) {
                        $ext_cedr       = $request['cedula_r']->getClientOriginalExtension();
                        Storage::disk('archivos_doctores')->put('Cedula_reverso_' . $id_usuario . "." . $ext_cedr, \File::get($request['cedula_r']));
                    } else {
                        return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                    }

                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipocr,
                        'url_doc'           => 'Cedula_reverso_' . $id_usuario . "." . $ext_cedr,
                        'estado'            => '2',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    LogDoctorDocumentos::create([
                        'id_documento'      => $id_documento,
                        'estado'            => '2',
                        'observacion'       => 'CREA ARCHIVO EN REVISION',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);
                } else {
                    if ($cedular->estado != 3) {
                        if ($request->hasFile('cedula_r') && $request->file('cedula_r')->isValid()) {
                            $ext_cedr       = $request['cedula_r']->getClientOriginalExtension();
                            Storage::disk('archivos_doctores')->put('Cedula_reverso_' . $id_usuario . "." . $ext_cedr, \File::get($request['cedula_r']));
                        } else {
                            return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                        }
                        $cedular->update([
                            'url_doc' => 'Cedula_reverso_' . $id_usuario . "." . $ext_cedr,
                            'estado' => '2',
                            'id_usuariomod' => 'CREAUSER',
                            'ip_modificacion' => ':1',
                        ]);
                    }
                }
            }

            if (!is_null($access)) {
                $tipoac = 3;
                $accesss = DoctorDocumentos::where('id_doctor', $id_usuario)->where('tipo', $tipoac)->first();
                if (is_null($accesss)) {
                    if ($request->hasFile('access') && $request->file('access')->isValid()) {
                        $ext_acce       = $request['access']->getClientOriginalExtension();
                        Storage::disk('archivos_doctores')->put('Access_' . $id_usuario . "." . $ext_acce, \File::get($request['access']));
                    } else {
                        return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                    }
                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipoac,
                        'url_doc'           => 'Access_' . $id_usuario . "." . $ext_acce,
                        'estado'            => '2',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);

                    LogDoctorDocumentos::create([
                        'id_documento'      => $id_documento,
                        'estado'            => '2',
                        'observacion'       => 'CREA ARCHIVO EN REVISION',
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);
                } else {
                    if ($accesss->estado != 3) {
                        if ($request->hasFile('access') && $request->file('access')->isValid()) {
                            $ext_acce       = $request['access']->getClientOriginalExtension();
                            Storage::disk('archivos_doctores')->put('Access_' . $id_usuario . "." . $ext_acce, \File::get($request['access']));
                        } else {
                            return ['status' => 'error', 'msj' => 'No se cargó ningún archivo o el archivo es inválido'];
                        }
                        $accesss->update([
                            'url_doc' => 'Access_' . $id_usuario . "." . $ext_acce,
                            'estado' => '2',
                            'id_usuariomod' => 'CREAUSER',
                            'ip_modificacion' => ':1',
                        ]);
                    }
                }
            }

            if (!is_null($terminos)) {
                $tipot = 4;
                $term = DoctorDocumentos::where('id_doctor', $id_usuario)->where('tipo', $tipot)->first();
                if ($terminos == 1) {
                    $est = 3;
                } else {
                    $est = 2;
                }
                if (is_null($term)) {
                    $id_documento = DoctorDocumentos::insertGetId([
                        'id_doctor'         => $id_usuario,
                        'tipo'              => $tipot,
                        'estado'            => $est,
                        'id_usuariocrea'    => 'CREAUSER',
                        'id_usuariomod'     => 'CREAUSER',
                        'ip_creacion'       => '::1',
                        'ip_modificacion'   => '::1'
                    ]);
                } else {
                    if ($term->estado != 3) {
                        $term->update([
                            'estado' => $est,
                            'id_usuariomod' => 'CREAUSER',
                            'ip_modificacion' => ':1',
                        ]);
                    }
                }
            }
            return ["status" => "success", "msj" => "Documento subido"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function listaDocValidacion(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            if ($user->id_tipo_usuario != 3) {
                return ["status" => "error", "msj" => "El usuario no es un doctor"];
            }

            $documento = DoctorDocumentos::selectRaw("IF(tipo = 1, 'Cédula de Identidad (ANVERSO)', IF(tipo = 2, 'Cédula de Identidad (REVERSO)', IF(tipo = 3, 'Certificado del ACESS', 'Términos y condiciones'))) as nombredoc, tipo,IF(estado = 2, 'EN REVISION', IF(estado = 3, 'VERIFICADO', 'RECHAZADO')) AS estado, estado as id_estado")
                ->where('id_doctor', $id_usuario)
                ->get();

            $tipos_esperados = [1 => 'Cédula de Identidad (ANVERSO)', 2 => 'Cédula de Identidad (REVERSO)', 3 => 'Certificado del ACESS', 4 => 'Términos y condiciones'];
            $arr = [];
            $tipos_encontrados = [];
            $img = null;
            $imagenes = [
                1 => 'cedula_anverso.svg',
                2 => 'cedula_reverso.svg',
                3 => 'certificado_acess.svg',
                4 => 'tyc.svg',
            ];
            foreach ($documento as $value) {
                if (isset($imagenes[$value->tipo])) {
                    $img = url($imagenes[$value->tipo]);
                }

                $x['tipo_doc'] = $value->tipo;
                $x['nombredoc'] = $value->nombredoc;
                $x['id_estado'] = $value->id_estado;
                $x['estado'] = $value->estado;
                $x['img'] = $img;
                array_push($arr, $x);
                $tipos_encontrados[] = $value->tipo;
            }

            foreach ($tipos_esperados as $tipo => $nombre) {
                if (isset($imagenes[$tipo])) {
                    $img = url($imagenes[$tipo]);
                }
                if (!in_array($tipo, $tipos_encontrados)) {
                    $x['tipo_doc'] = $tipo;
                    $x['nombredoc'] = $nombre;
                    $x['id_estado'] = 1; // Pendiente
                    $x['estado'] = 'PENDIENTE';
                    $x['img'] = $img;
                    array_push($arr, $x);
                }
            }

            usort($arr, function ($a, $b) {
                return $a['tipo_doc'] <=> $b['tipo_doc'];
            });

            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function crearProducto(Request $request) //no se usa
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $empresa = Empresa::where('prioridad', 1)->first();
            $codigo = substr($request['nombre'], 0, 3);
            $prod = Ct_productos::where('codigo', $codigo)->where('estado_tabla', 1)->first();
            if (!is_null($prod)) {
                return ["status" => "error", "msj" => "El producto ya existe"];
            }

            $p_id = Ct_productos::insertGetId([
                'codigo'                     => $codigo,
                'nombre'                     => strtoupper($request['nombre']),
                'descripcion'                => $request['nombre'],
                'id_empresa'                 => $empresa->id,
                'iva'                        => $request['iva'] != null ? $request['iva'] : 0,
                'id_usuariocrea'             => 'CREAUSER',
                'id_usuariomod'              => 'CREAUSER',
                'ip_creacion'                => '::1',
                'ip_modificacion'            => '::1'
            ]);
            return ["status" => "success", "msj" => "Producto creado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public static function creaNivel($id_usuario, $id_especialidad, $id_empresa)
    {
        try {
            $usuario = User::find($id_usuario);
            $especialidad = Especialidad::find($id_especialidad);
            if (is_null($especialidad)) {
                return ["status" => "error", "msj" => "La especialidad no existe"];
            }
            $seguro = Seguro::where('nombre', 'like', '%' . $usuario->id . '%')->where('inactivo', 1)->first();
            if (is_null($seguro)) {
                $id_seguro = Seguro::insertGetId([
                    'nombre' => $usuario->id . ' - ' . $usuario->nombre1 . ' ' . $usuario->apellido1,
                    'descripcion' => $usuario->id . ' - ' . $usuario->nombre1 . ' ' . $usuario->apellido1,
                    'tipo' => 2,
                    'color' => '#000000',
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $id_seguro = $seguro->id;
            }
            $nivel = Nivel::where('nombre', 'like', '%' . $usuario->id . '%')->where('nombre_corto', 'like', '%' . $especialidad->nombre . '%')->where('estado', 1)->first();
            if (is_null($nivel)) {
                $id_nivel = Nivel::insertGetId([
                    'nombre' => $usuario->id . ' - ' . $usuario->nombre1 . ' ' . $usuario->apellido1,
                    'nombre_corto' => $especialidad->nombre,
                    'grupo' => 1,
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $id_nivel = $nivel->id;
            }
            $convenio = Convenio::where('id_seguro', $id_seguro)->where('id_nivel', $id_nivel)->where('id_empresa', $id_empresa)->first();
            if (is_null($convenio)) {
                $id_convenio = Convenio::insertGetId([
                    'id_seguro' => $id_seguro,
                    'id_nivel' => $id_nivel,
                    'id_empresa' => $id_empresa,
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $id_convenio = $convenio->id;
            }
            $doctor_convenio = DoctorConvenioEspecialidad::where('id_doctor', $id_usuario)->where('id_especialidad', $id_especialidad)->where('id_convenio', $id_convenio)->where('estado', 1)->first();
            if (is_null($doctor_convenio)) {
                $id_doctor_convenio = DoctorConvenioEspecialidad::insertGetId([
                    'id_doctor' => $id_usuario,
                    'id_especialidad' => $id_especialidad,
                    'id_convenio' => $id_convenio,
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $id_doctor_convenio = $doctor_convenio->id;
            }

            return ["status" => "success", 'msj' => 'ok', 'id_seguro' => $id_seguro, 'id_nivel' => $id_nivel, 'id_convenio' => $id_convenio, 'id_doctor_convenio' => $id_doctor_convenio];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function asignarValorProd(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $id_especialidad = $request['id_especialidad'];
            if (trim($id_especialidad) == '') {
                return ["status" => "error", "msj" => "Ingrese la especialidad"];
            }
            $id_producto = $request['id_producto'];
            if (trim($id_producto) == '') {
                return ["status" => "error", "msj" => "Ingrese el producto"];
            }
            $valor = $request['valor'];
            if (trim($valor) == '') {
                return ["status" => "error", "msj" => "Ingrese el valor"];
            }
            $empresa = Empresa::where('prioridad', 1)->first();

            $seg_niv = BdDrNetController::creaNivel($id_usuario, $id_especialidad, $empresa->id);
            if ($seg_niv['status'] == 'error') {
                return ["status" => "error", "msj" => $seg_niv['msj']];
            }
            $id_seguro = $seg_niv['id_seguro'];
            $id_nivel = $seg_niv['id_nivel'];
            $id_convenio = $seg_niv['id_convenio'];
            $id_doctor_convenio = $seg_niv['id_doctor_convenio'];
            $prod_tarifario = Ct_Productos_Tarifario::where('id_producto', $id_producto)->where('id_seguro', $id_seguro)->where('nivel', $id_nivel)->where('estado', 1)->first();
            if (is_null($prod_tarifario)) {
                $id_prod_tarifario = Ct_Productos_Tarifario::insertGetId([
                    'id_producto' => $id_producto,
                    'id_seguro' => $id_seguro,
                    'nivel' => $id_nivel,
                    'precio_producto' => $request['valor'],
                    'id_usuariocrea' => 'CREAUSER',
                    'id_usuariomod' => 'CREAUSER',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1'
                ]);
            } else {
                $id_prod_tarifario = $prod_tarifario->id;
                Ct_Productos_Tarifario::where('id', $id_prod_tarifario)->update([
                    'precio_producto' => $request['valor'],
                    'id_usuariomod' => 'CREAUSER',
                    'ip_modificacion' => ':1'
                ]);
            }

            return ["status" => "success", "msj" => "Valor asignado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function listado_productos(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credencial
            es Invalidas"];
        }
        try {
            $empresa = Empresa::where('prioridad', 1)->first();
            $productos = Ct_productos::where('estado_tabla', 1)
                ->where('id_empresa', $empresa->id)
                ->select('id', 'nombre', 'codigo')
                ->where('tipo_app', 1)
                ->get();
            return ["status" => "success", "data" => $productos];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function verificarEstadoDocs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $docs = DoctorDocumentos::where('id_doctor', $id_usuario)->get();
            $verify_docs = false;
            $verify_status_docs = null;
            $all_verified = true;

            foreach ($docs as $doc) {
                if ($doc->estado != 3) {
                    $all_verified = false;
                    $name = $doc->estadoDoc;
                    $verify_status_docs = $name->descripcion;
                    break;
                }
            }

            if ($all_verified) {
                $verify_status_docs = 'verified'; // Si todos están verificados, asignamos 'verified'.
            }

            if (count($docs) == 4) {
                $verify_docs = true;
            } else {
                $verify_docs = false;
                $verify_status_docs = 'required';
            }

            $verify_perfil = true;
            $perfil = Doctor_Tiempo::where('id_doctor', $id_usuario)->first();
            $aux_perfil = 0;
            if (!is_null($perfil)) {
                if (!is_null($perfil->precio) and !is_null($perfil->descripcion_medico)) {
                    $aux_perfil = 1;
                }
            }
            $consultorios = DoctorConsultorio::where('id_doctor', $id_usuario)->where('estado', 1)->get();
            $horario = HorarioDoctorApp::where('id_doctor', $id_usuario)->where('estado', 1)->get();
            if ($aux_perfil == 0 or count($consultorios) == 0 or count($horario) == 0) {
                $verify_perfil = false;
            }

            $data['isProfileCompleted'] = $verify_perfil;
            $data['isDocumentCompleted'] = $verify_docs;
            $data['document_status'] = $verify_status_docs;

            return ["status" => "success", "data" => $data];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function listaProdxDoc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $empresa = Empresa::where('prioridad', 1)->first();
            $doctor_convenio = DoctorConvenioEspecialidad::where('id_doctor', $id_usuario)->where('estado', 1)->get();
            $productos = [];
            foreach ($doctor_convenio as $doc_conv) {
                $arr['id_especialidad'] = $doc_conv->id_especialidad;
                $arr['especialidad'] = $doc_conv->especialidad->nombre;
                $arr['detalle'] = [];

                $convenio = $doc_conv->convenio;
                $productos_tarifario = Ct_Productos_Tarifario::where('id_seguro', $convenio->id_seguro)->where('nivel', $convenio->id_nivel)->where('estado', 1)->get();
                foreach ($productos_tarifario as $prod_tar) {
                    $arr['detalle'][] = [
                        'id_producto' => $prod_tar->id_producto,
                        'producto' => $prod_tar->producto->nombre,
                        'valor' => $prod_tar->precio_producto
                    ];
                }
                array_push($productos, $arr);
            }

            return ["status" => "success", "data" => $productos];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function disponibilidadDoctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }
            $fech = $request['date'];
            if (trim($fech) == '') {
                return ["status" => "error", "msj" => "Ingrese la fecha"];
            }
            $day = date('N', strtotime($fech));

            $shedule      = HorarioDoctorApp::where('id_doctor', $id_usuario)->where('tipo', 1)->where('ndia', $day)->where('estado', 1)->get();
            $valor_consulta = Doctor_Tiempo::where('id_doctor', $id_usuario)->first();
            $hoursfree = [];
            $xp = [];
            $contador = 0;
            foreach ($shedule as $sh) {
                $var1 = substr($sh->hora_ini, 0, 5);
                $var2 = substr($sh->hora_fin, 0, 5);
                $fechaInicio = new DateTime($var1);
                $fechaFin = new DateTime($var2);
                $fechaFin = $fechaFin->modify('+30 minutes');
                $rangoFechas = new DatePeriod($fechaInicio, new DateInterval('PT30M'), $fechaFin);
                $contador = 0;
                $maximo = iterator_count($rangoFechas) - 1;

                foreach ($rangoFechas as $fecha) {
                    if ($contador < $maximo) {
                        $minutes_to_add = 30;
                        $time = new DateTime($request['date'] . ' ' . $fecha->format("H:i"));
                        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                        $stamp = $time->format('H:i');
                        $validAgenda = Agenda::whereBetween('fechaini', [$request['date'] . ' ' . $fecha->format("H:i") . ':00', $request['date'] . ' ' .  $stamp . ':00'])
                            ->where('id_doctor1', $id_usuario)
                            ->where('estado', 1)
                            //->where('observaciones', 'like', '%FUE PAGADO DESDE LA APP%')
                            ->where('proc_consul', 0)
                            ->count();

                        $p['inicio'] = $request['date'] . ' ' . $fecha->format("H:i") . ':00';
                        $p['id_doctor1'] = $id_usuario;
                        //$validateMaximos = $this->validateMax1($p);

                        $data['fecha'] = $request['date'];
                        $data['id_doctor'] = $id_usuario;
                        //$cant_agendas = BdIecedController::verificarAgenda($data);
                        /*if ($cant_agendas['status'] == 'error') {
                            return $cant_agendas['msj'];
                        }*/

                        //if ($validAgenda == 0 && $validateMaximos > 0) {
                        if ($validAgenda == 0) {

                            /*if (!is_null($mebresia)) {
                                $porcentaje = 25;
                                $descuento = (($valor_consulta->precio * $porcentaje) / 100);
                                $precio_mem = $valor_consulta->precio - $descuento;
                                $price = $precio_mem;
                            } else {*/
                            $price = !is_null($valor_consulta) ? $valor_consulta->precio : '0.00';
                            //}

                            $ph['inicio'] = $request['date'] . ' ' . $fecha->format("H:i");
                            $ph['fin'] = $request['date'] . ' ' . $stamp;
                            $ph['id_doctor'] = $id_usuario;
                            $user = User::find($id_usuario);
                            $ph['nombre_doctor'] = $user->apellido1 . ' ' . $user->nombre1;
                            $ph['precio'] = $price;
                            $ph['rango'] = $fecha->format("H:i") . ' - ' . $stamp;
                            array_push($hoursfree, $ph);
                        }
                    }
                    $contador++;
                }
            }
            return ["status" => "success", "horarios" => $hoursfree];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function pagoConsulta(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi==') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request['token'];
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
        try {
            $id_usuario = $request['identificacion'];
            if (trim($id_usuario) == '') {
                return ["status" => "error", "msj" => "Ingrese el id del usuario"];
            }
            $user = User::find($id_usuario);
            if (is_null($user)) {
                return ["status" => "error", "msj" => "El usuario no existe"];
            }

            $detalle = $request->all();
            $empresa = Empresa::where('prioridad', 1)->first();
            if ($detalle['datos_orden']['tipopago'] == 'ORD') {
                $datos['fecha_ini'] = $detalle['datos_orden']['dateini'];
                $datos['fecha_fin'] = $detalle['datos_orden']['dateend'];
                $datos['fecha'] = date('Y-m-d', strtotime($detalle['datos_orden']['dateini']));
                $datos['id_doctor'] = $detalle['datos_orden']['id_doctor'];

                if (is_null($detalle['datos_orden']['id_doctor'])) {
                    return ['status' => 'error', 'msj' => 'id doctor vacio'];
                }
                if (is_null($detalle['datos_orden']['dateini'])) {
                    return ['status' => 'error', 'msj' => 'fecha inicio vacio'];
                }
                if (is_null($detalle['datos_orden']['dateend'])) {
                    return ['status' => 'error', 'msj' => 'fecha fin vacio'];
                }
            }

            $detalle['ip_cliente'] = rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256);

            $id_transaccion = TransaccionPago::insertGetId([
                'data_request' => json_encode($detalle),
                'id_empresa' => $empresa->id,
                'id_check_app' => $detalle['requestId']
            ]);
            $detalle['id_transaccion'] = $id_transaccion;
            unset($detalle['datos_orden']);
            //////////////// aun no tenemos datafast
            $data['checkid'] = BdPagoEnLinea::checkId($detalle);
            ///////////////
            $check = json_decode($data['checkid'], true);
            $transaccion = TransaccionPago::find($id_transaccion);
            $transaccion->check_id = $check['id'];
            $transaccion->save();

            return view('bd_pagos.index_pago_datafast', $data);
        } catch (\Exception $e) {
            return ["msj" => $e->getMessage(), 'status' => 'error', 'linea' => $e->getLine()];
        }
    }

    public static function storeConsulta($request)
    {
        try {
            $id_usuario = $request->id_usuario;
            $id_doctor = $request->id_doctor;
            $id_especialidad = $request->id_especialidad;
            $id_consultorio = $request->id_consultorio;
            $fecha_inicio = $request->fecha_ini;
            $fecha_fin = $request->fecha_fin;
            $empresa = Empresa::where('prioridad', 1)->first();
            $consultorio = DoctorConsultorio::find($id_consultorio);
            $modalidad = $request->modalidad;

            $input_agenda = [
                'fechaini'        => $fecha_inicio,
                'fechafin'        => $fecha_fin,
                'id_paciente'     => $id_usuario,
                'id_doctor1'      => $id_doctor,
                'proc_consul'     => '4',
                'estado_cita'     => '3',
                'id_empresa'      => $empresa->id,
                'espid'           => $id_especialidad,
                'tipo_cita'       => 1,
                'tc'              => $modalidad,
                'observaciones'   => 'FUE PAGADO DESDE LA APP DRNET',
                'id_seguro'       =>  1, //change this
                'estado'          => '0', //First state 0 when i confirmed i will change
                'ip_creacion'     => '::1',
                'ip_modificacion' => '::1',
                'id_usuariocrea'  => 'CREAUSER',
                'id_usuariomod'   => 'CREAUSER',
            ];
            $id_agenda = Agenda::insertGetId($input_agenda);
            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => $fecha_inicio,
                'fechafin'        => $fecha_fin,
                'estado'          => '1',
                'observaciones'   => 'FUE PAGADO DESDE LA APP DRNET',
                'id_doctor1'      => $id_doctor,
                'descripcion'     => 'FUE PAGADO DESDE LA APP DRNET',
                'id_usuariomod'   => 'CREAUSER',
                'id_usuariocrea'  => 'CREAUSER',
                'ip_modificacion' => '::1',
                'ip_creacion'     => '::1',
            ];

            Log_Agenda::create($input_log);
            return $id_agenda;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function confirmarPagoConsulta(Request $request)
    {
        try {
            $data = $request->all();
            $detalle_orden = TransaccionPago::where('check_id', $data['id'])->first();

            $arr_datos = json_decode($detalle_orden->data_request, true);
            if ($arr_datos['datos_orden']['tipopago'] == 'ORD') {
                $datos['fecha_ini'] = $arr_datos['datos_orden']['dateini'];
                $datos['fecha_fin'] = $arr_datos['datos_orden']['dateend'];
                $datos['fecha'] = date('Y-m-d', strtotime($arr_datos['datos_orden']['dateini']));
                $datos['id_doctor'] = $arr_datos['datos_orden']['id_doctor'];

                /*$cant_agendas = BdIecedController::verificarAgenda($datos);
                if ($cant_agendas['status'] == 'error') {
                    return $cant_agendas;
                }*/
            }
            //no tenemos datafast
            $confirmar = BdPagoEnLinea::confirmarPago($data['resourcePath']);
            $confirmar = json_decode($confirmar);
            $checkid = $confirmar->ndc;
            ///////////////////////////
            $transaccion = TransaccionPago::where('check_id', $checkid)->first();

            if ($confirmar->result->code != '000.000.000' and $confirmar->result->code != '000.100.112') {
                LogPagoEnLinea::create([
                    'data_array' => json_encode($confirmar),
                    'txt_respuesta' => json_encode($confirmar->result),
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_transaccion' => $transaccion->id,
                ]);
                return ['status' => 'error', 'msj' => $confirmar->result];
            }

            $transaccion->estado = 1;
            $transaccion->save();
            $detalle = json_decode($transaccion->data_request);
            $tipo_pago = $detalle->datos_orden->tipopago;

            $id_orden_venta = "";
            $id_mem = null;
            $id_agenda = null;
            if ($tipo_pago == "ORD") {
                $stringPlain = 'siam_2020_' . random_int(1, 10000);
                $encodeurl   = base64_encode($stringPlain);
                $url         = 'https://videocall.mdc-server.com/?id=' . $encodeurl;
                if ($detalle->datos_orden->mod == "PRESENCIAL") {
                    $url = null;
                }
                $id_agenda = $this->storeConsulta($detalle->datos_orden); //copia de agendPatient de app ieced
                $id_apps = Apps_Agenda::insertGetId([
                    'fecha'          => $detalle->datos_orden->dateini,
                    'estado'         => 1,
                    'url'            => $url,
                    'id_agenda'      => $id_agenda,
                    'total'          => $detalle->datos_orden->total,
                    'id_doctor'      => $detalle->datos_orden->id_doctor,
                    'id_usuariocrea' => $detalle->datos_orden->id_usuario,
                    'tipo'           => $detalle->datos_orden->mod,
                    'online'         => 0,
                    'id_check_app'   => $transaccion->id_check_app
                ]);
                $paciente = Paciente::find($detalle->datos_orden->id_paciente);
                //LOG DEL SISTEMA DE QUIEN CREa LA ORDEN
                Log_usuario::create([
                    'id_usuario'  => '1234517896',
                    'ip_usuario'  => $detalle->ip_cliente,
                    'descripcion' => "GENERA COTIZACION MOVIL",
                    'dato_ant1'   => $detalle->datos_orden->id_paciente,
                    'dato1'       => strtoupper($paciente['nombre1_paciente'] . " " . $paciente['nombre2_paciente'] . " " . $paciente['apellido1_paciente'] . " " . $paciente['apellido2_paciente']),
                ]);
            }

            LogPagoEnLinea::create([
                'data_array' => json_encode($confirmar),
                'txt_respuesta' => json_encode($confirmar->result),
                'fecha' => date('Y-m-d H:i:s'),
                'id_transaccion' => $transaccion->id,
                'id_agenda' => $id_agenda,
                'id_membresia' => $id_mem,
                'id_orden_venta' => $id_orden_venta,
            ]);
            return ['status' => 'success', 'msj' => $confirmar->result];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function ConfirmacionAgendaPayment(Request $request) // confirmacion de la confirmacion
    {
        try {
            $json_request = $request->all();
            $requestId    = $json_request['requestId'];
            $response = TransaccionPago::where('id_check_app', $requestId)->where('estado', 1)->first();
            if (is_null($response)) {
                return ['status' => 'error', 'msj' => 'Transaccion no aprobada'];
            }
            $arr_datos = json_decode($response->data_request, true);
            if ($arr_datos['datos_orden']['tipopago'] == 'ORD') {
                $apps = Apps_Agenda::where("id_check_app", $requestId)->where('online', 0)->first();
                $apps->online = 1;
                $agenda = Agenda::find($apps->id_agenda);
                $apps_agenda = Apps_Agenda::where('id_agenda', $agenda->id)->first();
                $paciente = User::find($apps_agenda->id_usuariocrea);
                $doctor = User::find($apps_agenda->id_doctor);
                $fecha = $agenda->fechaini;
                $asunto = "Cita Médica Confirmada";
                $titulo = "Agendamiento de Cita Médica";
                $url = $apps_agenda->url;
                $agenda->estado = 1;
                $agenda->estado_cita = 0;
                $agenda->proc_consul = 0;
                $agenda->save();
                $apps->save();
                // envia correo??
            }
            return ['status' => 'success', 'msj' => 'Transaccion Aprobada'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => 'Transaccion no aprobada', 'result' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function index_prueba(Request $request)
    {
        return view('encuesta_nueva.prueba');
    }
}
