<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Excel;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Log;
use Sis_medico\Log_usuario;
use Sis_medico\Orden;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\Orden_Tipo;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\User;
use Carbon\Carbon;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Orden_Agenda;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;

class OrdenesDoctorController extends Controller
{
    public function lista_proc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {

            $tipop = $request->tipo;
            $nombre_grupo = "";

            if (is_null($tipop)) {
                return ['status' => "error", 'msj' => "enviar tipo"];
            }

            if ($tipop == 0) {
                $nombre_grupo = "Endoscopico";
            }
            if ($tipop == 1) {
                $nombre_grupo = "Funcional";
            }
            if ($tipop == 2) {
                $nombre_grupo = "Imagenes";
            }

            $procedimientos = Procedimiento::join('grupo_procedimiento as gp', 'procedimiento.id_grupo_procedimiento', '=', 'gp.id')
                ->where('procedimiento.estado', '1')
                ->where('gp.tipo_procedimiento', $tipop)
                ->where('gp.id', '!=', '11')
                ->select('procedimiento.id', 'procedimiento.nombre', 'gp.id as id_grupo', 'gp.nombre as grupo', 'gp.tipo_procedimiento', 'procedimiento.cardiologia')->get();

            $global = [];
            $arr_endo = [];
            $arr_func = [];
            $arr_img = [];
            foreach ($procedimientos as $value) {

                $arr = [
                    "id" => $value->id,
                    "nombre" => $value->nombre,
                    "id_grupo" => $value->id_grupo,
                    "grupo" => $value->grupo,
                    "cardiologia" => $value->cardiologia
                ];

                array_push($arr_endo, $arr);
            }

            $grupos_endo = array();
            foreach ($arr_endo as $item) {

                $grupo = $item['grupo'];
                if (!array_key_exists($grupo, $grupos_endo)) {
                    $grupos_endo[$grupo] = array();
                }
                $grupos_endo[$grupo][] = $item;
            }

            $endo2 = array();
            foreach ($grupos_endo as $grupo => $elementos) {
                $endo['grupo'] = $grupo;
                $aux_endo = array();
                foreach ($elementos as $elemento) {
                    $e['id'] = $elemento['id'];
                    $e['nombre'] = $elemento['nombre'];
                    $e['id_grupo'] = $elemento['id_grupo'];
                    $e['cardiologia'] = $elemento['cardiologia'];
                    $e['tipo_procedimiento'] = $tipop;
                    array_push($aux_endo, $e);
                }
                $endo['procedimientos'] = $aux_endo;
                array_push($endo2, $endo);
            }

            $data['nombre_grupo'] = $nombre_grupo;
            $data['valores'] = $endo2;

            return $data;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(),  'linea' => $e->getLine()];
        }
    }

    public function crear_orden_proc(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {

            $id_doctor = $request->id_doctor;
            $id_paciente = $request->id_paciente;
            $ip_cliente = $request->ip_cliente;
            $tipo_orden = $request->tipo_orden;
            $nombre_tipo = "";
            $motivo = $request->motivo;
            $resumen = $request->resumen;
            $diagnostico = $request->diagnostico;
            $observacion_medica = $request->observacion_medica;
            $cardiologia = $request->cardiologia;
            $codigo_doctor = $request->codigo_doctor;

            if ($tipo_orden == 0) {
                $nombre_tipo = "Endoscopico";
            }
            if ($tipo_orden == 1) {
                $nombre_tipo = "Funcional";
            }
            if ($tipo_orden == 2) {
                $nombre_tipo = "Imagenes";
            }

            $paciente = Paciente::find($id_paciente);

            if (is_null($paciente)) {
                $user = User::find($id_paciente);

                $user_correo = User::where('email', $request->paciente->email)->where('estado', '1')->first();
                if (!is_null($user_correo)) {
                    return json_encode(["respuesta" => 'El correo ya existe en otro paciente', 'status' => 'no']);
                }

                if (!is_null($user)) {
                    $input_pac = [

                        'id'                 => $id_paciente,
                        'id_usuario'         => $id_paciente,
                        'nombre1'            => strtoupper($request->paciente->nombre1),
                        'nombre2'            => strtoupper($request->paciente->nombre2),
                        'apellido1'          => strtoupper($request->paciente->apellido1),
                        'apellido2'          => strtoupper($request->paciente->apellido2),
                        'fecha_nacimiento'   => $request->paciente->fecha_nacimiento,
                        'sexo'               => $request->sexo,
                        'telefono1'          => $request->paciente->telefono1,
                        'telefono2'          => $request->paciente->telefono2,
                        'parentesco'         => 'Principal',
                        'parentescofamiliar' => 'Principal',
                        'tipo_documento'     => 1,
                        'id_seguro'          => 1,
                        'imagen_url'         => ' ',
                        'menoredad'          => 0,
                        'ip_creacion'        => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                        'id_usuariocrea'     => 'CREAUSER',
                        'id_usuariomod'      => 'CREAUSER',
                        'otro'               => 'WEB ORDENES DOCTOR',

                    ];
                    Paciente::create($input_pac);
                } else {
                    $arr_us = [
                        'id'               => $id_paciente,
                        'email'            => $request->paciente->email,
                        'nombre1'          => strtoupper($request->paciente->nombre1),
                        'id_tipo_usuario'  => '2',
                        'nombre2'          => strtoupper($request->paciente->nombre2),
                        'apellido1'        => strtoupper($request->paciente->apellido2),
                        'apellido2'        => strtoupper($request->paciente->apellido2),
                        'estado'           => '1',
                        'imagen_url'       => ' ',
                        'telefono1'        => $request->paciente->telefono1,
                        'telefono2'        => $request->paciente->telefono2,
                        'direccion'        => $request->paciente->direccion,
                        'tipo_documento'   => '1',
                        'id_pais'          => '1', //pendiente revisar
                        'fecha_nacimiento' => $request->paciente->fecha_nacimiento,
                        'password'         => $request->paciente_pass,
                        'id_usuariocrea'   => 'CREAUSER',
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                    ];

                    $id_usuario = User::insertGetId($arr_us);

                    $input_pac = [

                        'id'                 => $id_paciente,
                        'id_usuario'         => $id_paciente,
                        'nombre1'            => strtoupper($request->paciente->nombre1),
                        'nombre2'            => strtoupper($request->paciente->nombre2),
                        'apellido1'          => strtoupper($request->paciente->apellido1),
                        'apellido2'          => strtoupper($request->paciente->apellido2),
                        'fecha_nacimiento'   => $request->paciente->fecha_nacimiento,
                        'sexo'               => $request->sexo,
                        'telefono1'          => $request->paciente->telefono1,
                        'telefono2'          => $request->paciente->telefono2,
                        'parentesco'         => 'Principal',
                        'parentescofamiliar' => 'Principal',
                        'tipo_documento'     => 1,
                        'id_seguro'          => 1,
                        'imagen_url'         => ' ',
                        'menoredad'          => 0,
                        'ip_creacion'        => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                        'id_usuariocrea'     => 'CREAUSER',
                        'id_usuariomod'      => 'CREAUSER',
                        'otro'               => 'WEB ORDENES DOCTOR',

                    ];
                    Paciente::create($input_pac);

                    $input_log = [
                        'id_usuario'  => 'CREAUSER',
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "CREA NUEVO PACIENTE WEB ORDENES DOCTOR",
                        'dato_ant1'   => $request->id_paciente,
                        'dato1'       => strtoupper($request->paciente->nombre1 . " " . $request->paciente->nombre2 . " " . $request->paciente->apellido1 . " " . $request->paciente->apellido2),
                        'dato_ant4'   => " PARENTESCO: Principal",
                        'dato2'       => 'WEB DOCTOR EXTERNO',
                    ];

                    Log_usuario::create($input_log);
                }
            }

            $input_orden = [
                'id_paciente' => $id_paciente,
                'id_doctor' => $id_doctor,
                'motivo_consulta' => $motivo,
                'resumen_clinico' => $resumen,
                'diagnosticos' => $diagnostico,
                'fecha_orden' => date('Y-m-d'),
                'tipo_procedimiento' => $tipo_orden,
                'anio' => substr(date('Y-m-d'), 0, 4),
                'mes' => substr(date('Y-m-d'), 5, 2),
                'observacion_medica' => $observacion_medica,
                'necesita_valoracion' => $cardiologia,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $request->codigo_doctor,
                'ip_modificacion' => $request->codigo_doctor,
                'cod_doctor' => $codigo_doctor,
            ];

            $id_orden = Orden::insertGetId($input_orden);


            foreach ($request->detalles as $value) {

                $orden_tipo = Orden_Tipo::where('id_orden', $id_orden)->where('id_grupo_procedimiento', $value['id_grupo'])->first();

                if (is_null($orden_tipo)) {
                    $input_orden_tipo = [
                        'id_orden' => $id_orden,
                        'id_grupo_procedimiento' => $value['id_grupo'],
                        'id_usuariocrea' => $id_doctor,
                        'id_usuariomod' => $id_doctor,
                        'ip_creacion' => $request->codigo_doctor,
                        'ip_modificacion' => $request->codigo_doctor,
                    ];

                    $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
                } else {
                    $id_orden_tipo = $orden_tipo->id;
                }


                $input_orden_procedimiento = [
                    'id_orden_tipo' => $id_orden_tipo,
                    'id_procedimiento' => $value['id_procedimiento'],
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $request->codigo_doctor,
                    'ip_modificacion' => $request->codigo_doctor,
                ];

                Orden_Procedimiento::create($input_orden_procedimiento);
            }


            $orden_act_new = [
                'anterior' => 'ORDEN ' . $nombre_tipo . ' WEB ORDENES EXTERNAS -> El Dr. creo nueva orden de procedimiento ' . $nombre_tipo,
                'nuevo' => 'ORDEN' . $nombre_tipo . ' -> Motivo: ' . $motivo . ' Resumen_Historia_Clinica: ' . $resumen . ' Diagnosticos: ' . $diagnostico . ' Observacion_Medica: ' . $observacion_medica . ' -> Codigo Doctor: ' . $codigo_doctor . 'id_orden' . $id_orden,
                'id_paciente' => $id_paciente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $request->codigo_doctor,
                'ip_modificacion' => $request->codigo_doctor,
            ];

            Hc_Log::create($orden_act_new);

            if ($tipo_orden == 0) {
                $url = url('ordenes/procedimientos/imprimir_endoscopica_externa');
            }
            if ($tipo_orden == 1) {
                $url = url('ordenes/procedimientos/imprimir_funcional_externa');
            }
            if ($tipo_orden == 2) {
                $url = url('ordenes/procedimientos/imprimir_imagenes_externas');
            }


            return ['status' => 'ok', 'msj' => 'Orden creada correctamente', 'id_orden' => $id_orden, 'url' => $url . '/' . $id_orden];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function crear_doctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $json_request = $request->all();
        $id_usuario = $json_request['id_usuario'];
        if (strlen($id_usuario) > 13) {
            return ['respuesta' => 'error', 'msj' => 'id largo'];
        }
        try {
            if ($json_request['tipo_doctor'] == 1) {
                $usuario = User::find($id_usuario);
                if (is_null($usuario)) {
                    $arr_us = [
                        'id'               => $id_usuario,
                        'email'            => $json_request['email'],
                        'nombre1'          => strtoupper($json_request['nombre1']),
                        'nombre2'          => strtoupper($json_request['nombre2']),
                        'apellido1'        => strtoupper($json_request['apellido1']),
                        'apellido2'        => strtoupper($json_request['apellido2']),
                        'id_tipo_usuario'  => '3', //user doctor
                        'estado'           => '1',
                        'imagen_url'       => 'avatar.jpg',
                        'ocupacion'        => $json_request['ocupacion'],
                        'telefono1'        => $json_request['telefono1'],
                        'telefono2'        => $json_request['telefono2'],
                        'direccion'        => $json_request['direccion'],
                        'tipo_documento'   => '1',
                        'id_pais'          => '1', //pendiente revisar
                        'fecha_nacimiento' => $json_request['fecha_nacimiento'],
                        'password'         => $json_request['password'],
                        'id_usuariocrea'   => 'CREAUSER',
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                        'uso_sistema'      => '1',
                    ];

                    $id_user = User::insertGetId($arr_us);
                    return ['respuesta' => 'Success', 'msj' => 'usuario creado Staff'];
                } else {
                    if ($usuario->id_tipo_usuario != '3') {
                        $us_up = [
                            'email'            => $json_request['email'],
                            'nombre1'          => strtoupper($json_request['nombre1']),
                            'id_tipo_usuario'  => '3', //user doctor
                            'nombre2'          => strtoupper($json_request['nombre2']),
                            'apellido1'        => strtoupper($json_request['apellido1']),
                            'apellido2'        => strtoupper($json_request['apellido2']),
                            'estado'           => '1',
                            'telefono1'        => $json_request['telefono1'],
                            'telefono2'        => $json_request['telefono2'],
                            'direccion'        => $json_request['direccion'],
                            'ocupacion'        => $json_request['ocupacion'],
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request['fecha_nacimiento'],
                            'password'         => $json_request['password'],
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_modificacion'  => '::1',
                            'uso_sistema'      => '1',
                        ];

                        $usuario->update($us_up);
                        return ['respuesta' => 'Success', 'msj' => 'usuario actualizado staff'];
                    }
                    return ['respuesta' => 'Success', 'msj' => 'usuario staff ya existe'];
                }
            } else {

                $codigo_doc = $json_request['codigo_doc'];
                if (strlen($codigo_doc) > 150) {
                    return ['respuesta' => 'error', 'msj' => 'codigo doctor externo demasiado largo'];
                }
                $doc_externo = Comisiones_Gastro::where('codigo', 'like', '%' . $codigo_doc . '%')->first();
                if (is_null($doc_externo)) {
                    $doc = [
                        'codigo'            => $codigo_doc,
                        'nombre'            => strtoupper($json_request['nombre1']) . ' ' . strtoupper($json_request['nombre2']) . ' ' . strtoupper($json_request['apellido1']) . ' ' . strtoupper($json_request['apellido2']),
                        'estado'            => 1,
                        'correo'            => $json_request['email'],
                        'telefono'          => $json_request['telefono1'],
                        'id_usuario_crea'   => 'CREAUSER',
                        'id_usuario_mod'    => 'CREAUSER',
                    ];

                    Comisiones_Gastro::create($doc);

                    $usuario_ex = User::find($id_usuario);
                    if (is_null($usuario_ex)) {
                        $arr_ex = [
                            'id'               => $id_usuario,
                            'email'            => $json_request['email'],
                            'nombre1'          => strtoupper($json_request['nombre1']),
                            'nombre2'          => strtoupper($json_request['nombre2']),
                            'apellido1'        => strtoupper($json_request['apellido1']),
                            'apellido2'        => strtoupper($json_request['apellido2']),
                            'id_tipo_usuario'  => '28', //tipo usuario doctor externo
                            'estado'           => '1',
                            'imagen_url'       => 'avatar.jpg',
                            'ocupacion'        => $json_request['ocupacion'],
                            'telefono1'        => $json_request['telefono1'],
                            'telefono2'        => $json_request['telefono2'],
                            'direccion'        => $json_request['direccion'],
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request['fecha_nacimiento'],
                            'password'         => $json_request['password'],
                            'id_usuariocrea'   => 'CREAUSER',
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_creacion'      => '::1',
                            'ip_modificacion'  => '::1',
                            'uso_sistema'      => '1',
                        ];

                        $id_user = User::insertGetId($arr_ex);
                    }
                    return ['respuesta' => 'Success', 'msj' => 'codigo doctor externo creado'];
                } else {
                    return ['respuesta' => 'Success', 'msj' => 'codigo doctor externo ya existe'];
                }
            }
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function ordenes_creadas(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {
            $codigo_doc = $request->codigo_doc;
            $ordenes = Orden::where('orden.cod_doctor', $codigo_doc)->join('paciente as p', 'p.id', 'orden.id_paciente')
                ->join('comisiones_gastro as cg', 'cg.codigo', 'orden.cod_doctor')
                ->select('orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido1 as papellido1', 'p.apellido2 as papellido2', 'cg.nombre as doctor_referido')
                ->orderBy('orden.id', 'DESC')->get();
            $tipo_procedimiento = '';
            $url = "";
            $arr_ordenes = array();
            foreach ($ordenes as $value) {
                if ($value['tipo_procedimiento'] == 0) {
                    $tipo_procedimiento = 'Procedimiento Endoscopico';
                    $url = "ordenes/procedimientos/imprimir_endoscopica_externa";
                }
                if ($value['tipo_procedimiento'] == 1) {
                    $tipo_procedimiento = 'Procedimiento Funcional';
                    $url = "ordenes/procedimientos/imprimir_funcional_externa";
                }
                if ($value['tipo_procedimiento'] == 2) {
                    $tipo_procedimiento = 'Procedimiento Imagenes';
                    $url = "ordenes/procedimientos/imprimir_imagenes_externas";
                }

                $nombre_estado = "";
                if ($value->estado == 1) {
                    $nombre_estado = "Activa";
                }
                if ($value->estado == 0) {
                    $nombre_estado = "Eliminada";
                }
                $data['id_orden'] = $value['id'];
                $data['estado'] = $value['estado'];
                $data['nombre_estado'] = $nombre_estado;
                $data['codigo'] = $value['cod_doctor'];
                $data['fecha_creacion'] = $value['fecha_orden'];
                $data['paciente'] = $value['pnombre1'] . ' ' . $value['pnombre2'] . ' ' . $value['papellido1'] . ' ' . $value['papellido2'];
                $data['doctor_referido'] = $value['doctor_referido'];
                $data['tipo_procedimiento'] = $tipo_procedimiento;
                $data['url'] = $url . '/' . $value['id'];

                array_push($arr_ordenes, $data);
            }
            return $arr_ordenes;
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function imprimir_endoscopica_externa($id)
    {

        $orden_proc_endoscopico = Orden::find($id);

        if ((is_null($orden_proc_endoscopico->check_doctor)) && (is_null($orden_proc_endoscopico->id_doctor_firma))) {

            $doctor_firma = $orden_proc_endoscopico->id_doctor;
        } else {

            $doctor_firma = $orden_proc_endoscopico->id_doctor_firma;
        }

        if (!is_null($orden_proc_endoscopico)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_endoscopico->id_paciente);

        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc_endoscopico->id_doctor)
            ->first();

        $vistaurl = "hc4.ordenes.orden_procedimiento_endoscopico.pdf_orden_endoscopica";
        $view =  \View::make($vistaurl, compact('orden_proc_endoscopico', 'paciente', 'edad', 'doctor_solicitante', 'firma'))->render();


        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function imprimir_funcional_externa($id)
    {

        $orden_proc_funcional = Orden::find($id);

        if ((is_null($orden_proc_funcional->check_doctor)) && (is_null($orden_proc_funcional->id_doctor_firma))) {
            $doctor_firma = $orden_proc_funcional->id_doctor;
        } else {
            $doctor_firma = $orden_proc_funcional->id_doctor_firma;
        }

        if (!is_null($orden_proc_funcional)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_funcional->id_paciente);

        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }
        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc_funcional->id_doctor)
            ->first();

        $vistaurl = "hc4.ordenes.orden_procedimiento_funcional.pdf_orden_funcional";
        $view =  \View::make($vistaurl, compact('orden_proc_funcional', 'paciente', 'edad', 'doctor_solicitante', 'firma'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function imprimir_imagenes_externas($id)
    {
        $orden_proc_imagenes = Orden::find($id);

        if ((is_null($orden_proc_imagenes->check_doctor)) && (is_null($orden_proc_imagenes->id_doctor_firma))) {
            $doctor_firma = $orden_proc_imagenes->id_doctor;
        } else {
            $doctor_firma = $orden_proc_imagenes->id_doctor_firma;
        }

        if (!is_null($orden_proc_imagenes)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_imagenes->id_paciente);

        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc_imagenes->id_doctor)
            ->first();

        $vistaurl = "hc4.ordenes.orden_procedimiento_imagenes.pdf_orden_imagenes";
        $view =  \View::make($vistaurl, compact('orden_proc_imagenes', 'paciente', 'edad', 'doctor_solicitante', 'firma'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function obtener_resultados(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {
            $id_paciente = $request->id_paciente;
            $codigo = $request->codigo;
            $ordenes = Orden_Agenda::where('orden_agenda.estado', '1')
                ->join('orden as ord', 'ord.id', 'orden_agenda.id_orden')
                ->join('agenda as a', 'a.id', 'orden_agenda.id_agenda')
                ->where('ord.id_paciente', $id_paciente)
                ->where('ord.estado', '1')
                ->where('ord.cod_doctor', $codigo)
                ->where('a.estado_cita', '4')
                ->where('a.proc_consul', '1')
                ->where('a.estado', '1')
                ->select('ord.*', 'a.id as id_agenda')
                ->get();

            $ordenes2 = Orden::where('orden.cod_doctor', $codigo)
                ->where('orden.id_paciente', $id_paciente)
                ->leftjoin('orden_agenda as oa', 'oa.id_orden', 'orden.id')
                ->whereNull('oa.id_orden')
                ->select('orden.*')
                ->orderBy('orden.id', 'DESC')->get();

            $global = [];

            foreach ($ordenes as $orden) {
                $agenda = $orden->agenda;
                $historia = $agenda->historia_clinica;

                $arr_protocolo = [];
                if (!is_null($historia)) {
                    $protocolo = hc_protocolo::where('hcid', $historia->hcid)->whereNotNull('conclusion')->get();
                    foreach ($protocolo as $proto) {
                        $arr = [
                            "id_protocolo" => $proto->id,
                            "url" => url('ordenes/procedimientos/descarga_estudio_externo') . '/' . $proto->id,
                        ];
                        array_push($arr_protocolo, $arr);
                    }
                }


                if ($orden->tipo_procedimiento == 0) {
                    $nombre_tipo = "PROCEDIMIENTO ENDOSCOPICO";
                    $url_orden = url('ordenes/procedimientos/imprimir_endoscopica_externa');
                }
                if ($orden->tipo_procedimiento == 1) {
                    $nombre_tipo = "PROCEDIMIENTO FUNCIONAL";
                    $url_orden = url('ordenes/procedimientos/imprimir_funcional_externa');
                }
                if ($orden->tipo_procedimiento == 2) {
                    $nombre_tipo = "PROCEDIMIENTO IMAGENES";
                    $url_orden = url('ordenes/procedimientos/imprimir_imagenes_externas');
                }



                $data['id_orden'] = $orden->id;
                $data['id_paciente'] = $orden->id_paciente;
                $data['fecha_orden'] = $orden->fecha_orden;
                $data['tipo_procedimiento'] = $orden->tipo_procedimiento;
                $data['nombre_tipo'] = $nombre_tipo;
                $data['resultados'] = $arr_protocolo;
                $data['url_orden'] = $url_orden . '/' . $orden->id;
                $data['estado'] = $orden->estado;
                $data['nombre_estado'] = "Realizada";
                array_push($global, $data);
            }

            foreach ($ordenes2 as $orden2) {
                if ($orden2->tipo_procedimiento == 0) {
                    $nombre_tipo = "PROCEDIMIENTO ENDOSCOPICO";
                    $url_orden = url('ordenes/procedimientos/imprimir_endoscopica_externa');
                }
                if ($orden2->tipo_procedimiento == 1) {
                    $nombre_tipo = "PROCEDIMIENTO FUNCIONAL";
                    $url_orden = url('ordenes/procedimientos/imprimir_funcional_externa');
                }
                if ($orden2->tipo_procedimiento == 2) {
                    $nombre_tipo = "PROCEDIMIENTO IMAGENES";
                    $url_orden = url('ordenes/procedimientos/imprimir_imagenes_externas');
                }

                $nombre_estado = "";
                if ($orden2->estado == 1) {
                    $nombre_estado = "Activa";
                }
                if ($orden2->estado == 0) {
                    $nombre_estado = "Eliminada";
                }

                $data['id_orden'] = $orden2->id;
                $data['id_paciente'] = $orden2->id_paciente;
                $data['fecha_orden'] = $orden2->fecha_orden;
                $data['tipo_procedimiento'] = $orden2->tipo_procedimiento;
                $data['nombre_tipo'] = $nombre_tipo;
                $data['resultados'] = [];
                $data['url_orden'] = $url_orden . '/' . $orden2->id;
                $data['estado'] = $orden2->estado;
                $data['nombre_estado'] = $nombre_estado;

                array_push($global, $data);
            }

            return $global;
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function descarga_estudio_externo($id_protocolo)
    {
        $protocolo = hc_protocolo::find($id_protocolo);
        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('seleccionado', '1')->where('estado', '1')->orderBy('id', 'desc')->get();

        $paciente = paciente::find($protocolo->historiaclinica->id_paciente);
        $seguro   = Seguro::find($protocolo->procedimiento->id_seguro);

        if (!is_null($protocolo->procedimiento->id_doctor_examinador2)) {
            $firma = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->get();
            if (!is_null($seguro)) {
                if ($seguro->tipo == '0') {
                    if ($protocolo->procedimiento->id_empresa == '0992704152001') {
                        if ($protocolo->procedimiento->id_doctor_examinador2 == '0924611882') {
                            $firma = Firma_Usuario::where('id_usuario', '094346835')->get();
                        }
                    }
                }
            }
        } else {
            $firma = null;
        }
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);

        $view = \View::make('hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->loadHTML($view);
        $agenda = $historia->agenda;
        $txt_fecha = substr($agenda->fechaini, 8, 2) . '_' . substr($agenda->fechaini, 5, 2) . '_' . substr($agenda->fechaini, 0, 4); //dd($txt_fecha);

        return $pdf->stream('Estudio_' . $paciente->id . '_' . $paciente->apellido1 . '_' . $paciente->nombre1 . '_' . $txt_fecha . '.pdf');
    }

    public function comision_ordenes_mes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {

            $codigo = $request->codigo_doc;

            $dr_comision = Comisiones_Gastro::where('estado', '1')->where('codigo', $codigo)->first();

            if (is_null($dr_comision)) {
                return ['respuesta' => 'error', 'msj' => 'El código no existe'];
            }

            $comisiones = Ct_Orden_Venta::join('comisiones_gastro', 'ct_orden_venta.doctor_referido', 'comisiones_gastro.id')
                ->join('agenda as ag', 'ag.id', 'ct_orden_venta.id_agenda')
                ->leftjoin('seguros', 'ct_orden_venta.id_seguro', 'seguros.id')
                ->where('ct_orden_venta.doctor_referido', '<>', null)
                ->where("ct_orden_venta.estado", 1)
                ->where('ct_orden_venta.doctor_referido', $dr_comision->id)
                ->select(DB::raw('MONTH(ct_orden_venta.fecha_emision) as mes'), DB::raw('SUM(ct_orden_venta.comision_total) as total_comision'))
                ->groupby(DB::raw('MONTH(ct_orden_venta.fecha_emision)'))
                ->get();

            $meses = array(
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            );

            $arr = [];

            foreach ($meses as $mes_num => $nombre_mes) {
                $comision_mes = $comisiones->where('mes', $mes_num)->first();
                $comision_total = $comision_mes ? round($comision_mes->total_comision, 4) : 0;

                $arr[] = [
                    "mes" => $nombre_mes,
                    "id_mes" => $mes_num,
                    "comision_total" => $comision_total,
                ];
            }

            return $arr;
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function comision_mes_actual(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {
            $codigo = $request->codigo_doc;

            $dr_comision = Comisiones_Gastro::where('estado', '1')->where('codigo', $codigo)->first();

            if (is_null($dr_comision)) {
                return ['respuesta' => 'error', 'msj' => 'El código no existe'];
            }

            $comision = Ct_Orden_Venta::join('comisiones_gastro', 'ct_orden_venta.doctor_referido', 'comisiones_gastro.id')
                ->join('agenda as ag', 'ag.id', 'ct_orden_venta.id_agenda')
                ->leftjoin('seguros', 'ct_orden_venta.id_seguro', 'seguros.id')
                ->where('ct_orden_venta.doctor_referido', '<>', null)
                ->where("ct_orden_venta.estado", 1)
                ->whereMonth('ct_orden_venta.fecha_emision', date('n'))
                ->where('ct_orden_venta.doctor_referido', $dr_comision->id)
                ->select(DB::raw('SUM(ct_orden_venta.comision_total) as total_comision'))
                ->first();

            $data['mes'] = date('m');
            $data['total_comision'] = is_null($comision->total_comision) ? 0 : $comision->total_comision;

            return $data;
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function crear_codigo_doctor(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {

            $comision_gastro = Comisiones_Gastro::where('codigo', $request['codigo_doc'])->where('estado', '1')->first();
            if (!is_null($comision_gastro)) {
                return ['respuesta' => 'error', 'msj' => 'El código ya existe'];
            }
            $arr = [
                'codigo' => $request['codigo_doc'],
                'nombre' => $request['nombre1'] . ' ' . $request['nombre2'] . ' ' . $request['apellido1'] . ' ' . $request['apellido2'],
                'correo' => $request['email'],
                'telefono' => $request['telefono1'],
                'id_usuario_crea' => 'CREAUSER',
                'id_usuario_mod' => 'CREAUSER',
                'origen' => 'WEB ORDENES EXTERNAS'
            ];
            Comisiones_Gastro::create($arr);
            return ['respuesta' => 'success', 'msj' => 'Código creado'];
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
}
