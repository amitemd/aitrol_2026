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
use DateTime;
use DateInterval;
use DatePeriod;
use Sis_medico\CodigoContrasena;
use Sis_medico\Paciente;
use Mail;
use Sis_medico\Log_usuario;
use Sis_medico\TipoDireccion;
use Sis_medico\UserAddress;

class BdFarmalinkController extends Controller
{
    public function loginApp(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
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
                    "phone"             => $usuario->telefono1,
                    "address"           => $usuario->direccion,
                    "city_id"           => $usuario->id_ciudad_usuario,
                ];
                return ["status" => "success", "msj" => "Credenciales Correctas", "data" => $arrus];
            } else {
                return ["status" => "error", "msj" => "Usuario o Contraseña Incorrecta"];
            }
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function CreaCodigoContrasena(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }

        try {
            $correo = $request['correo'];
            if (is_null($correo)) {
                return ['status' => 'error', 'msj' => 'Ingrese el correo'];
            }
            $usuario = User::where('email', $correo)->first();
            if (is_null($usuario)) {
                return ['status' => 'error', 'msj' => 'Usuario no encontrado contacte a soporte'];
            }
            /// validar si existe en la tabla 10 min y retornar q ya existe un correo enviado 
            $fechahoy = date('Y-m-d H:i');
            $now = new Datetime();
            $minutes = $now->modify('-10 minutes')->format("Y-m-d H:i");
            $validar = CodigoContrasena::where('email', $correo)->where('estado', 1)->whereBetween('created_at', [$minutes . ':00', $fechahoy . ':00'])->first();

            if (!is_null($validar)) {
                return ['status' => 'success', 'msj' => 'Ya existe un correo enviado, Revise su bandeja de entrada o correo no deseado', 'id_usuario' => $usuario->id];
            }

            $num1 = rand(0, 9);
            $num2 = rand(0, 9);
            $num3 = rand(0, 9);
            $num4 = rand(0, 9);
            $codigo = $num1 . $num2 . $num3 . $num4;
            $asunto = 'Recuperar contraseña';

            Mail::send('app_mails.mail', ['codigo' => $codigo], function ($msj) use ($usuario, $asunto) {
                $msj->subject($asunto);
                $msj->from('no-reply-bd@aitrol.com', 'Sistema Automatico de AITROL');
                $msj->to($usuario->email);
            });

            CodigoContrasena::create([
                'codigo'    => $codigo,
                'email'     => $correo,
            ]);

            return ['status' => 'success', 'msj' => 'Correo enviado, Revise su bandeja de entrada o correo no deseado', 'id_usuario' => $usuario->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function ValCodContrasena(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $codigo = $request['codigo'];
            $email = $request['email'];
            if (is_null($codigo)) {
                return ['status' => 'error', 'msj' => 'Ingrese el codigo'];
            }
            $fechahoy = date('Y-m-d H:i');
            $now = new Datetime();
            $minutes = $now->modify('-10 minutes')->format("Y-m-d H:i");
            $valcod = CodigoContrasena::where('codigo', $codigo)->where('email', $email)->where('estado', 1)->whereBetween('created_at', [$minutes . ':00', $fechahoy . ':00'])->first();

            if (is_null($valcod)) {
                return ['status' => 'error', 'msj' => 'Codigo Invalido'];
            }

            return ['status' => 'success', 'msj' => 'Codigo Valido'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function cambiar_contraseña(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $id_usuario = $request['id_usuario'];
            if (trim($request['password_1']) == '') {
                return ["status" => "error", "msj" => "Contrasena 1 vacia"];
            }
            if (trim($request['password_2']) == '') {
                return ["status" => "error", "msj" => "Contrasena 2 vacia"];
            }
            if (trim($request['password_1']) !=  trim($request['password_2'])) {
                return ["status" => "error", "msj" => "Las contraseñas no coinciden"];
            }
            $user = User::find($id_usuario);
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
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function tipos_direcciones(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $tipos = TipoDireccion::where('estado', 1)->get();
            $arr = [];
            foreach ($tipos as $value) {
                $data['id'] = $value->id;
                $data['nombre'] = $value->nombre;
                $data['image'] = url($value->icon);
                array_push($arr, $data);
            }
            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function DireccionesxUsuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $usuario = User::find($id_usuario);
            if (is_null($usuario)) {
                return ["status" => "error", "msj" => "Usuario no existe"];
            }
            $direcciones = UserAddress::where('user_address.id_user', $id_usuario)
                ->join('tipo_direccion as tp', 'tp.id', '=', 'user_address.id_tipo_direccion')
                ->where('user_address.estado', 1)
                ->select('user_address.id', 'user_address.direccion', 'user_address.id_tipo_direccion', 'user_address.landmark', 'user_address.lat', 'user_address.lng', 'tp.nombre as address_type_name', 'tp.icon as address_type_icon')
                ->get();

            $arr = [];
            foreach ($direcciones as $value) {
                $data['id_direccion'] = $value->id;
                $data['address'] = $value->direccion;
                $data['id_tipo_direccion'] = $value->id_tipo_direccion;
                $data['address_type_name'] = $value->address_type_name;
                $data['address_type_icon'] = url($value->address_type_icon);
                $data['landmark'] = $value->landmark;
                $data['lat'] = $value->lat;
                $data['lng'] = $value->lng;
                array_push($arr, $data);
            }

            return ["status" => "success", "data" => $arr];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function CrearDireccion(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $id_usuario = $request['id_usuario'];
            $usuario = User::find($id_usuario);
            if (is_null($usuario)) {
                return ["status" => "error", "msj" => "Usuario no encontrado"];
            }
            if (trim($request['address']) == '') {
                return ["status" => "error", "msj" => "Ingrese la direccion"];
            }
            if (trim($request['id_tipo_direccion']) == '') {
                return ["status" => "error", "msj" => "Ingrese el tipo de direccion"];
            }
            if (trim($request['lat']) == '') {
                return ["status" => "error", "msj" => "Ingrese la latitud"];
            }
            if (trim($request['lng']) == '') {
                return ["status" => "error", "msj" => "Ingrese la longitud"];
            }

            UserAddress::create([
                'id_user'        => $id_usuario,
                'direccion'         => $request['address'],
                'id_tipo_direccion' => $request['id_tipo_direccion'],
                'landmark'          => $request['landmark'],
                'lat'               => $request['lat'],
                'lng'               => $request['lng'],
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => '::1',
                'ip_modificacion'   => '::1'
            ]);

            return ["status" => "success", "msj" => "Direccion Creada"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public function ActualizarDireccion(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        try {
            $id = $request['id_direccion'];
            $id_usuario = $request['id_usuario'];
            $direccion = UserAddress::where('id', $id)->where('id_user', $id_usuario)->first();
            if (is_null($direccion)) {
                return ["status" => "error", "msj" => "Direccion no encontrada"];
            }
            if (trim($request['address']) == '') {
                return ["status" => "error", "msj" => "Ingrese la direccion"];
            }
            if (trim($request['id_tipo_direccion']) == '') {
                return ["status" => "error", "msj" => "Ingrese el tipo de direccion"];
            }
            if (trim($request['lat']) == '') {
                return ["status" => "error", "msj" => "Ingrese la latitud"];
            }
            if (trim($request['lng']) == '') {
                return ["status" => "error", "msj" => "Ingrese la longitud"];
            }
            if (trim($request['landmark']) == '') {
                return ["status" => "error", "msj" => "Ingrese el landmark"];
            }

            $direccion->update([
                'direccion' => $request['address'],
                'landmark' => $request['landmark'],
                'lat' => $request['lat'],
                'lng' => $request['lng'],
                'id_tipo_direccion' => $request['id_tipo_direccion'],
                'id_usuariomod' => $id_usuario,
                'ip_modificacion' => '::1'
            ]);

            return ["status" => "success", "msj" => "Direccion Actualizada"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function registerUser(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $token = $request->token;
        if ($token != '55049a9e6825609b04557abde2141e1f' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
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
                    'tipo_documento'   => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'password'         => !is_null($request['password']) ? bcrypt($request['password']) : bcrypt($request['id_usuario']),
                    'id_usuariocrea'   => 'CREAUSER',
                    'id_usuariomod'    => 'CREAUSER',
                    'imagen_url'       => ' ',
                    'ip_creacion'      => '::1',
                    'ip_modificacion'  => '::1',
                ];
                $id = User::insertGetId($arr_us);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO USUARIO APP",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP',
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
                    'id_tipo_usuario'  => $tipo_usuario,
                    'id_usuariomod'    => 'CREAUSER',
                    'ip_modificacion'  => '::1',
                    'password'         => !is_null($request['password']) ? bcrypt($request['password']) : bcrypt($request['id_usuario']),

                ];
                $usuario->update($usUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZA USUARIO APP",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP',
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
                    'otro'               => 'APP',
                    'imagen_url'         => ' '
                ];
                Paciente::create($input_pac);
                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP',
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
                    'ip_modificacion'    => '::1',
                    'id_usuariomod'      => 'CREAUSER',
                ];
                $paciente->update($pacUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZAR NUEVO PACIENTE APP",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP',
                ];
                Log_usuario::create($input_log);
            }
            return ["status" => "success", "msj" => "Usuario creado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function datosUsuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
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
            $data["id_usuario"]        = $usuario->id;
            $data["nombre1"]           = $usuario->nombre1;
            $data["nombre2"]           = $usuario->nombre2;
            $data["apellido1"]         = $usuario->apellido1;
            $data["apellido2"]         = $usuario->apellido2;
            $data["fecha_nacimiento"]  = $usuario->fecha_nacimiento;
            $data["direccion"]         = $usuario->direccion;
            $data["telefono"]          = $usuario->telefono1;
            $data["tipo_documento"]    = $usuario->tipo_documento;
            $data["nombre_doc"]        = $nombre_doc;
            $data["correo"]            = $usuario->email;
            $data["ocupacion"]         = $usuario->ocupacion;
            $data["sexo"]              = is_null($paciente) ? "" : $paciente->sexo;
            $data["id_tipo_usuario"]   = $usuario->id_tipo_usuario;
            $data["imagen_url"]        = url('bd/foto/usuario') . '/' . $usuario->id;
            return $data;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }

    public function updateUser(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
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

        try {
            $user->nombre1   = strtoupper($request['nombre1']);
            $user->nombre2   = strtoupper($request['nombre2']);
            $user->apellido1 = strtoupper($request['apellido1']);
            $user->apellido2 = strtoupper($request['apellido2']);
            $user->telefono1 = $request['telefono1'];
            $user->direccion = $request['direccion'];
            $user->fecha_nacimiento = $request['fecha_nacimiento'];
            $user->email = $request['email'];
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
                    'otro'               => 'APP ',

                ];
                Paciente::create($input_pac);
                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP ",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP ',
                ];
                Log_usuario::create($input_log);
            }

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "ACTUALIZAR USUARIO Y PACIENTE APP ",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato2'       => 'ACTUALIZADO EN APP ',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Usuario Actualizado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }
}
