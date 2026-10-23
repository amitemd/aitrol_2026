<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use LDAP\Result;
use Sis_medico\Examen_Orden;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Orden;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\User;
use Sis_medico\Users_Terminos;
use Response;

class BdConsultasProcController extends Controller
{
    public function datos_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_usuario = $request->id_usuario;
        $usuario = User::find($id_usuario);
        if (is_null($usuario)) {
            return ['status' => 'error', 'msj' => 'Usuario no existe'];
        }

        $paciente = Paciente::find($id_usuario);
        $sexo = "";

        if (!is_null($paciente)) {
            $sexo = $paciente->sexo;
        }

        $consulta_terminos = Users_Terminos::where('id_user', $id_usuario)->first();


        $arr = [
            'id' => $usuario->id,
            'nombre1' => $usuario->nombre1,
            'nombre2' => $usuario->nombre2,
            'apellido1' => $usuario->apellido1,
            'apellido2' => $usuario->apellido2,
            'email' => $usuario->email,
            'telefono1' => $usuario->telefono1,
            'direccion' => $usuario->direccion,
            'ciudad' => $usuario->ciudad,
            'sexo' => $sexo,
            'fecha_nacimiento' => $usuario->fecha_nacimiento,
            'notificacion' => isset($consulta_terminos->notificacion) ? $consulta_terminos->notificacion : 0,
            'terminos' => isset($consulta_terminos->terminos) ? $consulta_terminos->terminos : 0,
        ];

        return ['status' => 'success', "respuesta" => $arr];
    }

    public function update_datos(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_usuario = $request->id_usuario;
        if (strlen($id_usuario) > 13) {
            return ['msj' => 'ID de usuario invalido mayor a 13 digitos', 'status' => 'error'];
        }

        $ip_cliente = $request->ip_cliente;
        $usuario = User::find($id_usuario);
        $paciente = Paciente::find($id_usuario);

        if (!is_null($usuario)) {
            $us_up = [
                'email'            => $request->email,
                'nombre1'          => strtoupper($request->nombre1),
                'nombre2'          => strtoupper($request->nombre2),
                'apellido1'        => strtoupper($request->apellido1),
                'apellido2'        => strtoupper($request->apellido2),
                'telefono1'        => $request->telefono1,
                'direccion'        => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'ciudad'           => $request->ciudad,
                'id_usuariomod'    => 'CREAUSER',
                'ip_modificacion'  => $ip_cliente,
            ];
            $usuario->update($us_up);

            if (!is_null($paciente)) {
                $pac_up = [
                    'id'                 => $id_usuario,
                    'id_usuario'         => $id_usuario,
                    'nombre1'            => strtoupper($request->nombre1),
                    'nombre2'            => strtoupper($request->nombre2),
                    'apellido1'          => strtoupper($request->apellido1),
                    'apellido2'          => strtoupper($request->apellido2),
                    'fecha_nacimiento'   => $request->fecha_nacimiento,
                    'ciudad'             => $request->ciudad,
                    'sexo'               => is_null($request->sexo) ? $paciente->sexo : $request->sexo,
                    'telefono1'          => $request->telefono1,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariomod'      => 'CREAUSER',
                ];
                $paciente->update($pac_up);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LANDING UPDATE DATOS FX UPDATE",
                    'dato_ant1'   => $request->id_paciente,
                    'dato1'       => strtoupper($request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2),
                    'dato_ant4'   => "PARENTESCO: Principal",
                    'dato2'       => 'LANDING UPDATE DATOS',
                ];

                Log_usuario::create($input_log);
            } else {
                $input_pac = [
                    'id'                 => $id_usuario,
                    'id_usuario'         => $id_usuario,
                    'nombre1'            => strtoupper($request->nombre1),
                    'nombre2'            => strtoupper($request->nombre2),
                    'apellido1'          => strtoupper($request->apellido1),
                    'apellido2'          => strtoupper($request->apellido2),
                    'fecha_nacimiento'   => $request->fecha_nacimiento,
                    'sexo'               => $request->sexo,
                    'telefono1'          => $request->telefono1,
                    'telefono2'          => $request->telefono2,
                    'ciudad'           => $request->ciudad,
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
                    'otro'               => 'LANDING UPDATE DATOS',

                ];
                Paciente::create($input_pac);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LANDING UPDATE DATOS FX CREATE",
                    'dato_ant1'   => $request->id_paciente,
                    'dato1'       => strtoupper($request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2),
                    'dato_ant4'   => "PARENTESCO: Principal",
                    'dato2'       => 'LANDING UPDATE DATOS',
                ];

                Log_usuario::create($input_log);
            }
        } else {
            $usuario_correo = User::where('email', $request->email)->first();
            if (!is_null($usuario_correo)) {
                return ['msj' => 'Correo ya existe en otro paciente', 'status' => 'error'];
            }
            $arr_us = [
                'id'               => $id_usuario,
                'email'            => $request->email,
                'nombre1'          => strtoupper($request->nombre1),
                'id_tipo_usuario'  => '2',
                'nombre2'          => strtoupper($request->nombre2),
                'apellido1'        => strtoupper($request->apellido1),
                'apellido2'        => strtoupper($request->apellido2),
                'estado'           => '1',
                'imagen_url'       => ' ',
                'telefono1'        => $request->telefono1,
                'direccion'        => $request->direccion,
                'tipo_documento'   => '1',
                'id_pais'          => '1', //pendiente revisar
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'ciudad'           => $request->ciudad,
                'password'         => bcrypt($id_usuario),
                'id_usuariocrea'   => 'CREAUSER',
                'id_usuariomod'    => 'CREAUSER',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ];

            $user = User::insertGetId($arr_us);

            if (!is_null($paciente)) {
                $pac_up = [
                    'id'                 => $id_usuario,
                    'id_usuario'         => $id_usuario,
                    'nombre1'            => strtoupper($request->nombre1),
                    'nombre2'            => strtoupper($request->nombre2),
                    'apellido1'          => strtoupper($request->apellido1),
                    'apellido2'          => strtoupper($request->apellido2),
                    'fecha_nacimiento'   => $request->fecha_nacimiento,
                    'ciudad'             => $request->ciudad,
                    'sexo'               => is_null($request->sexo) ? $paciente->sexo : $request->sexo,
                    'telefono1'          => $request->telefono1,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariomod'      => 'CREAUSER',
                ];
                $paciente->update($pac_up);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LANDING UPDATE DATOS FX UPDATE",
                    'dato_ant1'   => $request->id_paciente,
                    'dato1'       => strtoupper($request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2),
                    'dato_ant4'   => "PARENTESCO: Principal",
                    'dato2'       => 'LANDING UPDATE DATOS',
                ];

                Log_usuario::create($input_log);
            } else {
                $input_pac = [
                    'id'                 => $id_usuario,
                    'id_usuario'         => $id_usuario,
                    'nombre1'            => strtoupper($request->nombre1),
                    'nombre2'            => strtoupper($request->nombre2),
                    'apellido1'          => strtoupper($request->apellido1),
                    'apellido2'          => strtoupper($request->apellido2),
                    'fecha_nacimiento'   => $request->fecha_nacimiento,
                    'sexo'               => $request->sexo,
                    'telefono1'          => $request->telefono1,
                    'ciudad'             => $request->ciudad,
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
                    'otro'               => 'LANDING UPDATE DATOS',

                ];
                Paciente::create($input_pac);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LANDING UPDATE DATOS FX CREATE",
                    'dato_ant1'   => $request->id_paciente,
                    'dato1'       => strtoupper($request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2),
                    'dato_ant4'   => "PARENTESCO: Principal",
                    'dato2'       => 'LANDING UPDATE DATOS',
                ];

                Log_usuario::create($input_log);
            }
        }

        $terminos = Users_Terminos::where('id_user', $id_usuario)->first();
        if (is_null($terminos)) {
            $input_terminos = [
                'id_user' => $id_usuario,
                'notificacion' => $request->notificacion,
                'terminos' => $request->terminos,
            ];
            Users_Terminos::create($input_terminos);
        } else {
            $terminos->notificacion = $request->notificacion;
            $terminos->terminos = $request->terminos;
            $terminos->save();
        }

        return ['msj' => 'Datos actualizados', 'status' => 'success'];
    }

    public function info_consulta_proc(Request $request, $ci_paciente = '', $tipoc = '')
    {
        if ($ci_paciente != '') {
            if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        }

        $id_paciente = $ci_paciente == '' ? $request['id_paciente'] : $ci_paciente;

        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ["respuesta" => 'PACIENTE NO ENCONTRADO', 'status' => 'error'];
        }

        $tipo = $tipoc == '' ? $request->tipo : $tipoc;
        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $data['id_paciente'] = $id_paciente;

        if ($tipo == 1) {

            $consultas = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
                ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                ->leftjoin('especialidad as e', 'e.id', 'a.espid')
                ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
                ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
                ->where('hc_p.id_procedimiento_completo', '40')
                ->where('a.estado_cita', '<>', '3')
                ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                ->select('historiaclinica.hcid', 'a.id as id_agenda', 'a.fechaini', 'hc_proto.motivo', 'hc_proto.cuadro_clinico', 'hc_p.id_doctor_examinador', 'e.nombre as espe_nombre', DB::raw('CONCAT(u.nombre1," ",u.apellido1) as nombre_doctor'))
                ->OrderBy('historiaclinica.created_at', 'desc')
                ->get();

            $arr_consulta = [];
            foreach ($consultas as $consulta) {
                $dia = Date('N', strtotime($consulta['fechaini']));
                $mes = Date('n', strtotime($consulta['fechaini']));
                $aux = intval($dia) - 1;
                $ms = intval($mes) - 1;
                $arr = [
                    'fechaini' => $consulta->fechaini,
                    'fecha_letras' => $dias[$aux] . ' ' . substr($consulta['fechaini'], 8, 2) . ' de ' . $meses[$ms] . ' del ' . substr($consulta['fechaini'], 0, 4),
                    'id_agenda' => $consulta->id_agenda,
                    'hcid' => $consulta->hcid,
                    'id_doctor_examinador' => $consulta->id_doctor_examinador,
                    'nombre_doctor' => $consulta->nombre_doctor,
                    'espe_nombre' => $consulta->espe_nombre,
                    'motivo' => $consulta->motivo,
                    'cuadro_clinico' => $consulta->cuadro_clinico,
                ];

                array_push($arr_consulta, $arr);
            }
            $data['status'] = 'success';
            $data['consultas'] = $arr_consulta;
        }

        if ($tipo == 2) {

            $procedimientos = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
                ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
                ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                ->where('hc_p.estado', '1')
                ->select(DB::raw('CONCAT(u.nombre1," ",u.apellido1) as nombre_doctor'), 'hc_p.id_doctor_examinador', 'a.fechaini', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos', 'hc_proto.tipo_procedimiento')
                ->OrderBy('a.fechaini', 'desc')
                ->get();
            $arr_procedimiento = [];

            foreach ($procedimientos as $proc) {
                $dia = Date('N', strtotime($proc['fechaini']));
                $mes = Date('n', strtotime($proc['fechaini']));
                $aux = intval($dia) - 1;
                $ms = intval($mes) - 1;
                if ($proc->tipo_procedimiento == 0) {
                    $tipo_procedimiento = 'Procedimiento Endoscopico';
                }
                if ($proc->tipo_procedimiento == 1) {
                    $tipo_procedimiento = 'Procedimiento Funcional';
                }
                if ($proc->tipo_procedimiento == 2) {
                    $tipo_procedimiento = 'Procedimiento Imagen';
                }

                $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $proc->id_procedimiento)->get();
                $mas = true;
                $texto = "";

                foreach ($adicionales as $value2) {
                    if ($mas == true) {
                        $texto = $texto . $value2->procedimiento->nombre;
                        $mas = false;
                    } else {
                        $texto = $texto . ' + ' .  $value2->procedimiento->nombre;
                    }
                }
                $arr = [
                    'fechaini' => $proc->fechaini,
                    'fecha_letras' => $dias[$aux] . ' ' . substr($proc['fechaini'], 8, 2) . ' de ' . $meses[$ms] . ' del ' . substr($proc['fechaini'], 0, 4),
                    'id_agenda' => $proc->id_agenda,
                    'hcid' => $proc->id_hc,
                    'id_doctor_examinador' => $proc->id_doctor_examinador,
                    'nombre_doctor' => $proc->nombre_doctor,
                    'tipo_procedimiento' => $tipo_procedimiento,
                    'nombres_procedimientos' => $texto,
                    'hallazgos' => $proc->hallazgos,
                    'conclusion' => $proc->conclusion,
                    'cuadro_clinico' => $proc->cuadro_clinico,
                    'diagnosticos' => $proc->diagnosticos,
                    'pdf_resultado' => url('ordenes/procedimientos/descarga_estudio_externo') . '/' . $proc->id_protocolo
                ];
                array_push($arr_procedimiento, $arr);
            }

            $data['status'] = 'success';
            $data['procedimientos'] = $arr_procedimiento;
        }

        return $data;
    }

    public function resumen_labs_paciente(Request $request, $ci_paciente = '')
    {
        if ($ci_paciente != '') {
            if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        }
        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $id_paciente = $ci_paciente == '' ? $request['id_paciente'] : $ci_paciente;
        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ["respuesta" => 'PACIENTE NO ENCONTRADO', 'status' => 'error'];
        }
        $examen_orden = Examen_Orden::where('examen_orden.pago_online', '0')
            ->where('examen_orden.id_paciente', $id_paciente)
            ->where('examen_orden.estado', '1')
            ->where('examen_orden.realizado', '1')
            ->select('examen_orden.*')
            ->orderBy('examen_orden.id', 'ASC')
            ->get();
        $arr = [];
        foreach ($examen_orden as $value) {
            $dia = Date('N', strtotime($value['fecha_orden']));
            $mes = Date('n', strtotime($value['fecha_orden']));
            $aux = intval($dia) - 1;
            $ms = intval($mes) - 1;
            $aux = [
                'id_orden' => $value->id,
                'fechaini' => $value->fecha_orden,
                'fecha_letras' => $dias[$aux] . ' ' . substr($value['fecha_orden'], 8, 2) . ' de ' . $meses[$ms] . ' del ' . substr($value['fecha_orden'], 0, 4),
                'url' => url('pagina/resultados/externos/imprimir') . '/' . $value->id
            ];

            array_push($arr, $aux);
        }

        $data_aux['status'] = 'success';
        $data_aux['ordenes'] = $arr;

        return $data_aux;
    }

    public function resumen_ordenes_proc(Request $request, $ci_paciente = '')
    {
        if ($ci_paciente != '') {
            if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        }
        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $id_paciente = $ci_paciente == '' ? $request['id_paciente'] : $ci_paciente;
        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ["respuesta" => 'PACIENTE NO ENCONTRADO', 'status' => 'error'];
        }
        $ordenes = Orden::where('orden.id_paciente', $id_paciente)
            ->where('orden.estado', '1')
            ->orderBy('orden.id', 'DESC')->get();
        $url = "";
        $arr_ordenes = array();
        foreach ($ordenes as $value) {
            $dia = Date('N', strtotime($value['fecha_orden']));
            $mes = Date('n', strtotime($value['fecha_orden']));
            $aux = intval($dia) - 1;
            $ms = intval($mes) - 1;

            if ($value['tipo_procedimiento'] == 0) {
                $url = "ordenes/procedimientos/imprimir_endoscopica_externa";
            }
            if ($value['tipo_procedimiento'] == 1) {
                $url = "ordenes/procedimientos/imprimir_funcional_externa";
            }
            if ($value['tipo_procedimiento'] == 2) {
                $url = "ordenes/procedimientos/imprimir_imagenes_externas";
            }
            $aux = [
                'id' => $value['id'],
                'fecha_letras' => $dias[$aux] . ' ' . substr($value['fecha_orden'], 8, 2) . ' de ' . $meses[$ms] . ' del ' . substr($value['fecha_orden'], 0, 4),
                'url' => url($url) . '/' . $value['id']
            ];
            array_push($arr_ordenes, $aux);
        }
        $data_aux['status'] = 'success';
        $data_aux['ordenes_proc'] = $arr_ordenes;

        return $data_aux;
    }

    public function resumen_doc_externos(Request $request, $ci_paciente = '')
    {
        // dd($request->all());
        if ($ci_paciente != '') {
            if ($request->header('appid') == null or $request->header('appid') != 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        }

        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $id_paciente = $ci_paciente == '' ? $request['id_paciente'] : $ci_paciente;
        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ["respuesta" => 'PACIENTE NO ENCONTRADO', 'status' => 'error'];
        }

        $externos = Paciente_Biopsia::where('id_paciente', $id_paciente)->where('estado', '1')->get();
        $arr_externos = array();
        foreach ($externos as $value) {
            $dia = Date('N', strtotime($value['created_at']));
            $mes = Date('n', strtotime($value['created_at']));
            $aux = intval($dia) - 1;
            $ms = intval($mes) - 1;
            $aux = [
                'id' => $value['id'],
                'fechaini' => Date('Y-m-d', strtotime($value['created_at'])),
                'fecha_letras' => $dias[$aux] . ' ' . substr($value['created_at'], 8, 2) . ' de ' . $meses[$ms] . ' del ' . substr($value['created_at'], 0, 4),
                'url' => url('pdf_doc_externo') . '/' . $value['id']
            ];
            array_push($arr_externos, $aux);
        }

        $data['status'] = 'success';
        $data['externos'] = $arr_externos;

        return $data;
    }

    public function pdf_doc_externo($name)
    {
        $imagen          = paciente_biopsia::find($name);
        $paciente        = paciente::find($imagen->id_paciente);
        $nombre_paciente = null;
        $nombre_paciente = $paciente->apellido1;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre;
        $nombre_paciente = $nombre_paciente . '_' . $paciente->nombre1;
        //dd($path);

        if ($nombre_paciente == null) {
            $nombre_archivo = $imagen->nombre;
        } else {
            $nombre_temporal = $imagen->nombre;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_paciente . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre;
                }
            } else {
                $nombre_archivo = $imagen->nombre;
            }
        }
        if (file_exists($path)) {
            //return "<iframe url='".$path."'> </iframe>";
            //para mostrar el pdf en el navegador cuando esta en el storage
            return response()->file($path);
        }
    }

    public function verificar_info_consultas(Request $request)
    {
        $id_paciente = $request->id_paciente;
        $paciente = Paciente::find($id_paciente);


        $consultas = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->leftjoin('especialidad as e', 'e.id', 'a.espid')
            ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
            ->where('hc_p.id_procedimiento_completo', '40')
            ->where('a.estado_cita', '<>', '3')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->select('historiaclinica.hcid', 'a.id as id_agenda', 'a.fechaini', 'hc_proto.motivo', 'hc_proto.cuadro_clinico', 'hc_p.id_doctor_examinador', 'e.nombre as espe_nombre', DB::raw('CONCAT(u.nombre1," ",u.apellido1) as nombre_doctor'))
            ->OrderBy('historiaclinica.created_at', 'desc')
            ->get();

        $procedimientos = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_p.estado', '1')
            ->select(DB::raw('CONCAT(u.nombre1," ",u.apellido1) as nombre_doctor'), 'hc_p.id_doctor_examinador', 'a.fechaini', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos', 'hc_proto.tipo_procedimiento')
            ->OrderBy('a.fechaini', 'desc')
            ->get();

        return ['consultas' => $consultas, 'procedimientos' => $procedimientos];
    }

    public function InformacionPaciente($id_paciente)
    {
        $paciente = Paciente::find($id_paciente);
        $data['id_paciente'] = $paciente->id;
        $data['nombre'] = $paciente->nombre1 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
        return view('hospital/informacion_paciente', ['paciente' => $paciente, 'data' => $data]);
    }

    public function infoTipo($id_paciente, $id_tipo)
    {
        $request = new Request();
        $request->headers->set('appid', 'TwOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=');
        $request->attributes->add(['id_paciente' => $id_paciente]);
        $request->request->add(['id_paciente' => $id_paciente]);

        if ($id_tipo == 1) { //consultas
            $consultas = $this->info_consulta_proc($request, $id_paciente, $id_tipo);
            $data = $consultas['consultas'];
        } else if ($id_tipo == 2) { //procedimientos
            $data = $this->info_consulta_proc($request, $id_paciente, $id_tipo);
        } else if ($id_tipo == 3) {
            $data = $this->resumen_labs_paciente($request, $id_paciente);
        } else if ($id_tipo == 4) {
            $data = $this->resumen_doc_externos($request, $id_paciente);
        } else if ($id_tipo == 5) {
            $data = $this->resumen_ordenes_proc($request, $id_paciente);
        }


        $tr = "";
        foreach ($data as $key => $value) {
            $tr .= "<div class='w-full border border-gray-100  shadow-lg rounded-lg'>
                        <div class='w-full bg-blue-900 h-12 text-center'>
                                {$value['fecha_letras']}
                        </div> 
                        <div class='flex mb-4'>
                            <div class='w-full h-12'>
                                Doctor: {$value['nombre_doctor']}
                            </div>
                        </div>
                    </div>
                ";
        }

        return $tr;
    }
}
