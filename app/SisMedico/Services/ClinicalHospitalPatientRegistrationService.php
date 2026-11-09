<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\User;

class ClinicalHospitalPatientRegistrationService
{
    private function validate(Request $request, array $rules, array $messages = array())
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function registropac(Request $request)
    {
        $patientTypeId = app(\Sis_medico\Services\v3\PatientUserType::class)->id();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = $request['cedula'];

        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if (!is_null($user)) {

            $this->validateInput2($request);
            Paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);

            Cortesia_paciente::create([
                'id'              => $request['cedula'],
                'cortesia'        => $request['cortesia'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        } else {

            $this->validateInput($request);

            User::create([
                'id'               => $request['cedula'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => '1',
                'id_pais'          => '1',
                'id_tipo_usuario'  => $patientTypeId,
                'email'            => $request['email'],
                'password'         => bcrypt($request['cedula']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'fecha_nacimiento' => $request['fecha_nacimiento'],
            ]);
            paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',

                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        }

        $paciente = Paciente::find($id);

        if ($request->cortesia == 'SI') {
            $cortesia_paciente = Cortesia_paciente::find($id);
            if (is_null($cortesia_paciente)) {
                Cortesia_paciente::create([
                    'id'              => $id,
                    'cortesia'        => 'SI',
                    'ilimitado'       => 'NO',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return $paciente->id;
    }

    private function validateInput2($request)
    {

        $rules = [

            'cedula'           => 'required|max:10|unique:paciente,id',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [

            'cedula'           => 'required|max:10|unique:users',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'email'            => 'required|email|max:191|unique:users',
            'cedula'           => 'required|max:10|unique:paciente,id',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

}
