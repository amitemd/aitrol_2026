<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Response;
use Sis_medico\Paciente;

class BdAppController extends Controller
{
    public function loginApp(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request->token;

        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas", "msj" => 'error'];
        }
        $email = $request->email;
        $password = $request->password;

        if (is_null($email) || is_null(trim($password)) || !isset($request['password'])) {
            return ["status" => "error", "msj" => "Campos usuario o contraseña vacios"];
        }
        $passwordUser = "";
        $usuario = User::where('email', $email)->where('estado', '1')->first();
        if (!is_null($usuario)) {
            $passwordUser = $usuario->password;
        }
        if (Hash::check($password, $passwordUser)) {
            $arrpac = [
                "id" => $usuario->id,
                "nombres" => $usuario->nombre1 . ' ' . $usuario->nombre2,
                "apellidos" => $usuario->apellido1 . ' ' . $usuario->apellido2,
                "email" => $usuario->email,
                "id_tipo_usuario" => $usuario->id_tipo_usuario,
            ];

            return ["status" => "success", "msj" => "Credenciales Validas", "data" => $arrpac];
        } else {
            return ["status" => "error", "msj" => "Usuario o contraseña incorrecta"];
        }
    }

    public function verificar_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $id_usuario = $request['id_usuario'];

        $user =  User::find($id_usuario);

        if (is_null($user)) {
            return ["status" => "error", "msj" => "Usuario no existe"];
        } else {
            return ["status" => "success", "msj" => "Usuario existe"];
        }
    }

    public function cambiar_contrasena(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }
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

        try {
            $user = User::find($id_usuario);
            $user->password = bcrypt($request["password_1"]);
            $user->id_usuariomod = $id_usuario;
            $user->ip_modificacion = $id_usuario;
            $user->save();

            return ["status" => "success", "msj" => "Contraseña Actualizada"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }

    public function datos_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $id_usuario = $request['id_usuario'];

        $usuario = User::find($id_usuario);

        if (is_null($usuario)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }

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
        $data["tipo_documento"]    = $usuario->tipo_documento;
        $data["nombre_doc"]        = $nombre_doc;
        $data["correo"]            = $usuario->email;
        $data["ocupacion"]         = $usuario->ocupacion;

        return $data;
    }

    public function update_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $id_usuario = $request['id_usuario'];

        $usuario = User::find($id_usuario);

        if (is_null($usuario)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }

        $usuario->nombre1 = $request['nombre1'];
        $usuario->nombre2 = $request['nombre2'];
        $usuario->apellido1 = $request['apellido1'];
        $usuario->apellido2 = $request['apellido2'];
        $usuario->direccion = $request['direccion'];
        $usuario->telefono1 = $request['telefono1'];
        $usuario->fecha_nacimiento = $request['fecha_nacimiento'];
        $usuario->email = $request['email'];
        $usuario->ocupacion = $request['ocupacion'];
        $usuario->id_usuariomod = $id_usuario;
        $usuario->ip_modificacion = $id_usuario;
        $usuario->save();

        return ["status" => "success", "message" => "Usuario Actualizado"];
    }

    public function crear_consulta(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $id_paciente = $request['id_paciente'];
        $paciente = Paciente::find($id_paciente);
    }

    public function crear_pacientes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8f51ecff8060a8f611bbd5d39cf78517' or $token == null) {
            return ["status" => "error", "msj" => "Credenciales Invalidas"];
        }

        $cedula = $request['cedula'];
        $id_usuario = $request['id_usuario'];
        $ip_cliente = $request['ip_cliente'];

        $usuario = User::find($cedula);
        $paciente = Paciente::find($cedula);

        $edad = "";
        $sexo = "";

        if (is_null($usuario) or is_null($paciente)) {
            DB::beginTransaction();
            try {
                $verificar_user = User::find($cedula);
                if (is_null($verificar_user)) {

                    $arr_us = [
                        'id'               => $cedula,
                        'email'            => $request['email'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'id_tipo_usuario'  => 2,
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'imagen_url'       => ' ',
                        'telefono1'        => $request['telefono1'],
                        'telefono2'        => $request['telefono2'],
                        'direccion'        => $request['direccion'],
                        'tipo_documento'   => 1,
                        'id_pais'          => 1,
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'password'         => bcrypt($cedula),
                        'ocupacion'        => $request['ocupacion'],
                        'id_usuariocrea'   => 'CREAUSER',
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                    ];

                    User::create($arr_us);
                }


                if (!is_null($respuesta['paciente'])) {
                    $verificar_paciente = Paciente::find($respuesta['paciente']['id']);
                    if (is_null($verificar_paciente)) {

                        $arr_pac = [
                            'id'                 => $respuesta['paciente']['id'],
                            'id_usuario'         => $respuesta['paciente']['id'],
                            'nombre1'            => strtoupper($respuesta['paciente']['nombre1']),
                            'nombre2'            => strtoupper($respuesta['paciente']['nombre2']),
                            'apellido1'          => strtoupper($respuesta['paciente']['apellido1']),
                            'apellido2'          => strtoupper($respuesta['paciente']['apellido2']),
                            'fecha_nacimiento'   => $respuesta['paciente']['fecha_nacimiento'],
                            'sexo'               => $respuesta['paciente']['sexo'],
                            'telefono1'          => $respuesta['paciente']['telefono1'],
                            'telefono2'          => $respuesta['paciente']['telefono2'],
                            'nombre1familiar'    => strtoupper($respuesta['paciente']['nombre1familiar']),
                            'nombre2familiar'    => strtoupper($respuesta['paciente']['nombre2familiar']),
                            'apellido1familiar'  => strtoupper($respuesta['paciente']['apellido1familiar']),
                            'apellido2familiar'  => strtoupper($respuesta['paciente']['apellido2familiar']),
                            'parentesco'         => $respuesta['paciente']['parentesco'],
                            'parentescofamiliar' => $respuesta['paciente']['parentescofamiliar'],
                            'tipo_documento'     => $respuesta['paciente']['tipo_documento'],
                            'id_seguro'          => $respuesta['paciente']['id_seguro'],
                            'imagen_url'         => ' ',
                            'menoredad'          => $respuesta['paciente']['menoredad'],
                            'estadocivil'        => $respuesta['paciente']['estadocivil'],
                            'gruposanguineo'     => $respuesta['paciente']['gruposanguineo'],
                            'id_pais'            => $respuesta['paciente']['id_pais'],
                            'ciudad'             => $respuesta['paciente']['ciudad'],
                            'direccion'          => $respuesta['paciente']['direccion'],
                            'cedulafamiliar'     => $respuesta['paciente']['cedulafamiliar'],
                            'id_usuariocrea'     => 'CREAUSER',
                            'id_usuariomod'      => 'CREAUSER',
                            'ip_creacion'        => '::1',
                            'ip_modificacion'    => '::1',

                        ];
                        Paciente::create($arr_pac);
                        ApiController::doctor_paciente($respuesta['paciente']['id'], $id_usuario, $ip_cliente);
                    }
                } else {

                    DB::commit();
                    return ["status" => "error", "msj" => "No se pudo crear el paciente", "data" => $cedula];
                }

                DB::commit();
                $paciente = Paciente::find($cedula);
                return ["status" => "success", "msj" => "Guardado con exito", "paciente" => $paciente,  "data" => $cedula];
            } catch (\Exception $e) {
                DB::rollBack();
                return ["status" => "error", "msj" => "No se pudo crear el paciente", "error" => $e->getMessage(), 'linea' => $e->getLine()];
            }
        } else {

            if ($paciente->sexo == 1) {
                $sexo = "MASCULINO";
            }
            if ($paciente->sexo == 2) {
                $sexo = "FEMENINO";
            }

            $fechaNacimiento = $paciente->fecha_nacimiento;
            $fechaNacimientoObj = new DateTime($fechaNacimiento);
            $fechaActualObj = new DateTime();
            $diferencia = $fechaNacimientoObj->diff($fechaActualObj);
            $edad = $diferencia->y;


            $arr_pac = [
                "id" => $paciente->id,
                "nombre1" => $paciente->nombre1,
                "nombre2" => $paciente->nombre2,
                "apellido1" => $paciente->apellido1,
                "apellido2" => $paciente->apellido2,
                "sexo" => $sexo,
                "fecha_nacimiento" => $paciente->fecha_nacimiento,
                "edad" => $edad
            ];
            return ["status" => "success", "msj" => "Paciente ya esta creado", "paciente" => $arr_pac];
        }
    }
}
