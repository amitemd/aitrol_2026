<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Mail;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Empresa;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Agenda;
use Sis_medico\Contable;
use Sis_medico\Ct_ventas;
use Sis_medico\Examen_Comision;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Hc_Cie10;
use Sis_medico\UserMembresia;
use Sis_medico\Log_usuario;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Log_Api;
use Sis_medico\Membresia;
use Sis_medico\Pentax;
use Sis_medico\Planilla;
use Sis_medico\Protocolo;
use Sis_medico\TicketPermiso;

class LabsDoctorController extends Controller
{
    public function buscar_examenes_labsdoctor(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $nombre = $request->nombre_examen;
                $examenes  = Examen::where('examen.estado', '1')
                    ->where('examen.valor', '>', 0)
                    ->where('examen.id', '<>', '1124')
                    ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
                    ->where('s.estado', 1);
                if (!is_null($nombre)) {
                    $examenes = $examenes->where('examen.nombre', 'like', '%' . $nombre . '%')->orwhere('examen.descripcion', 'like', '%' . $nombre . '%');
                }

                $examenes =  $examenes->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'), 'examen.nombre_largo', 'examen.sugerencia')
                    ->orderBy('examen.nombre')->get();
                return json_encode(['respuesta' => $examenes, 'status' => 'okay']);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function busqueda_user_paciente(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $cedula = $request->cedula;
                if (!is_null($cedula)) {
                    $usuario = User::find($cedula);
                    if (!is_null($usuario)) {
                        $paciente = Paciente::find($cedula);

                        return response()->json(["respuesta" => "Existe", "usuario" => $usuario, "paciente" => $paciente, "claveUser" => $usuario->password, 'status' => 'okay']);
                    } else {
                        return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
                    }
                } else {
                    return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
                }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function orden_labs_doctor(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $json_request = json_decode($request->getContent());
                $id_paciente = $json_request->id_paciente;
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $id_doctor = $json_request->id_doctor;
                $paciente = Paciente::find($id_paciente);
                $id_protocolo = null;
                $id_nivel     = null;
                $contador     = 0;
                $total             = 0;
                $descuento_detalle = 0;
                // $protocolo = Protocolo::where('estado', '2')->where('id_usuariocrea', $id_doctor)->first();
                // if (!is_null($protocolo)) {
                //     $examen_proto = Examen_Protocolo::where('id_protocolo', $protocolo->id)->get();
                //     $id_protocolo = $protocolo->id;
                // }

                if (is_null($paciente)) {

                    $user = user::find($id_paciente);

                    $user_correo = user::where('email', $json_request->paciente->email)->where('estado', '1')->first();
                    if (!is_null($user_correo)) {
                        return json_encode(["respuesta" => 'El correo ya existe en otro paciente', 'status' => 'no']);
                    }

                    if (!is_null($user)) {
                        $input_pac = [

                            'id'                 => $id_paciente,
                            'id_usuario'         => $id_paciente,
                            'nombre1'            => strtoupper($json_request->paciente->nombre1),
                            'nombre2'            => strtoupper($json_request->paciente->nombre2),
                            'apellido1'          => strtoupper($json_request->paciente->apellido1),
                            'apellido2'          => strtoupper($json_request->paciente->apellido2),
                            'fecha_nacimiento'   => $json_request->paciente->fecha_nacimiento,
                            'sexo'               => $json_request->sexo,
                            'telefono1'          => $json_request->paciente->telefono1,
                            'telefono2'          => $json_request->paciente->telefono2,
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
                            'otro'              => 'LABS DOCTOR',

                        ];
                        Paciente::create($input_pac);
                    } else {
                        $arr_us = [
                            'id'               => $id_paciente,
                            'email'            => $json_request->paciente->email,
                            'nombre1'          => strtoupper($json_request->paciente->nombre1),
                            'id_tipo_usuario'  => '2',
                            'nombre2'          => strtoupper($json_request->paciente->nombre2),
                            'apellido1'        => strtoupper($json_request->paciente->apellido2),
                            'apellido2'        => strtoupper($json_request->paciente->apellido2),
                            'estado'           => '1',
                            'imagen_url'       => ' ',
                            'telefono1'        => $json_request->paciente->telefono1,
                            'telefono2'        => $json_request->paciente->telefono2,
                            'direccion'        => $json_request->paciente->direccion,
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request->paciente->fecha_nacimiento,
                            'password'         => $json_request->paciente_pass,
                            'id_usuariocrea'   => 'CREAUSER',
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_creacion'      => '::1',
                            'ip_modificacion'  => '::1',
                        ];

                        $id_usuario = User::insertGetId($arr_us);

                        $input_pac = [

                            'id'                 => $id_paciente,
                            'id_usuario'         => $id_paciente,
                            'nombre1'            => strtoupper($json_request->paciente->nombre1),
                            'nombre2'            => strtoupper($json_request->paciente->nombre2),
                            'apellido1'          => strtoupper($json_request->paciente->apellido1),
                            'apellido2'          => strtoupper($json_request->paciente->apellido2),
                            'fecha_nacimiento'   => $json_request->paciente->fecha_nacimiento,
                            'sexo'               => $json_request->sexo,
                            'telefono1'          => $json_request->paciente->telefono1,
                            'telefono2'          => $json_request->paciente->telefono2,
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
                            'otro'              => 'LABS DOCTOR',

                        ];
                        Paciente::create($input_pac);

                        $input_log = [
                            'id_usuario'  => 'CREAUSER',
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "CREA NUEVO PACIENTE LABS DOCTOR",
                            'dato_ant1'   => $json_request->id_paciente,
                            'dato1'       => strtoupper($json_request->paciente->nombre1 . " " . $json_request->paciente->nombre2 . " " . $json_request->paciente->apellido1 . " " . $json_request->paciente->apellido2),
                            'dato_ant4'   => " PARENTESCO: Principal",
                            'dato2'       => 'APP',
                        ];

                        Log_usuario::create($input_log);
                    }
                    $paciente = Paciente::find($id_paciente);
                }


                $input_ex = [
                    'id_paciente' => $id_paciente,
                    'anio' => substr(date('Y-m-d'), 0, 4),
                    'mes' => substr(date('Y-m-d'), 5, 2),
                    'id_protocolo' => $id_protocolo,
                    'id_seguro' => '1',
                    'est_amb_hos' => '0',
                    'id_doctor_ieced' => $id_doctor,
                    'cantidad' => '0',
                    'estado' => '-1',
                    'valor' => '0',
                    'codigo' => $json_request->codigo_doc,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'fecha_orden' => date('Y-m-d h:i:s'),
                    'doctor_txt' => 'CREADO EN LABS_DOCTOR WEB',

                ];
                $id_examen_orden = Examen_Orden::insertGetId($input_ex);

                foreach ($json_request->detalles as $pexamen) {
                    //CREA DETALLE
                    $detalle = Examen_Detalle::where('id_examen_orden', $id_examen_orden)->where('id_examen', $pexamen->id)->first();
                    if (is_null($detalle)) {
                        $contador++;
                        $examen = Examen::find($pexamen->id);
                        $valor    = $pexamen->total;
                        $cubre    = 'NO';
                        $ex_nivel = Examen_Nivel::where('id_examen', $pexamen->id)->where('nivel', $id_nivel)->first();
                        if (!is_null($ex_nivel)) {
                            if ($ex_nivel->valor1 != 0) {
                                $valor = $ex_nivel->valor1;
                                $cubre = 'SI';
                            }
                        }
                        $input_det = [
                            'id_examen_orden' => $id_examen_orden,
                            'id_examen'       => $pexamen->id,
                            'valor'           => $valor,
                            'cubre'           => $cubre,
                            'p_descuento'     => $pexamen->pdiscount,
                            'valor_descuento' => $pexamen->discount,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                        ];
                        Examen_detalle::create($input_det);
                        $total += $valor;
                        $descuento_detalle += $pexamen->discount;
                    }
                }

                $total             = $total;
                $recargo_p         = 0;
                $subtotal_pagar    = $total - $descuento_detalle;
                $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
                $recargo_valor     = $recargo_valor;
                $valor_total       = $subtotal_pagar + $recargo_valor;
                $valor_total       = $valor_total;

                $orden     = Examen_Orden::find($id_examen_orden);
                $input_ex2 = [
                    'recargo_valor'   => $recargo_valor,
                    'total_valor'     => $total - $descuento_detalle,
                    'cantidad'        => $contador,
                    'valor'           => $total,
                    'descuento_valor' => $descuento_detalle,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'estado_pago'     => '0',
                    // 'pres_dom'        => $request->pres_dom,
                ];
                $orden->update($input_ex2);


                return json_encode(['respuesta' => $id_examen_orden, 'status' => 'okay']);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function crear_doctor_labs(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {

                $json_request = json_decode($request->getContent());
                $id_usuario = $json_request->id_usuario;
                // DB::beginTransaction();

                // try {
                if ($json_request->tipo_doctor == 1) {
                    $usuario = User::find($id_usuario);
                    if (is_null($usuario)) {
                        $arr_us = [
                            'id'               => $id_usuario,
                            'email'            => $json_request->email,
                            'nombre1'          => strtoupper($json_request->nombre1),
                            'nombre2'          => strtoupper($json_request->nombre2),
                            'apellido1'        => strtoupper($json_request->apellido2),
                            'apellido2'        => strtoupper($json_request->apellido2),
                            'id_tipo_usuario'  => '3', //user doctor
                            'estado'           => '1',
                            'imagen_url'       => 'avatar.jpg',
                            'ocupacion'        => $json_request->ocupacion,
                            'telefono1'        => $json_request->telefono1,
                            'telefono2'        => $json_request->telefono2,
                            'direccion'        => $json_request->direccion,
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request->fecha_nacimiento,
                            'password'         => $json_request->password,
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
                                'email'            => $json_request->email,
                                'nombre1'          => strtoupper($json_request->nombre1),
                                'id_tipo_usuario'  => '3', //user doctor
                                'nombre2'          => strtoupper($json_request->nombre2),
                                'apellido1'        => strtoupper($json_request->apellido1),
                                'apellido2'        => strtoupper($json_request->apellido2),
                                'estado'           => '1',
                                'telefono1'        => $json_request->telefono1,
                                'telefono2'        => $json_request->telefono2,
                                'direccion'        => $json_request->direccion,
                                'ocupacion'        => $json_request->ocupacion,
                                'tipo_documento'   => '1',
                                'id_pais'          => '1', //pendiente revisar
                                'fecha_nacimiento' => $json_request->fecha_nacimiento,
                                'password'         => $json_request->password,
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

                    $codigo_doc = $json_request->codigo_doc;
                    if (strlen($codigo_doc) >10) {
                        return ['respuesta' => 'error', 'msj' => 'codigo doctor externo demasiado largo'];
                    }
                    $doc_externo = Labs_doc_externos::where('id', 'like', '%' . $codigo_doc . '%')->first();
                    if (is_null($doc_externo)) {
                        $doc = [
                            'id'               => $codigo_doc,
                            'nombre1'          => strtoupper($json_request->nombre1),
                            'nombre2'          => strtoupper($json_request->nombre2),
                            'apellido1'        => strtoupper($json_request->apellido1),
                            'apellido2'        => strtoupper($json_request->apellido2),
                            'estado'           => 1,
                            'correo'           => $json_request->email,
                            'telefono'         => $json_request->telefono1,
                            'id_usuariocrea'   => 'CREAUSER',
                            'id_usuariomod'    => 'CREAUSER',
                            'tipo'             => 'COMISION'
                        ];


                        Labs_doc_externos::create($doc);

                        $usuario_ex = User::find($id_usuario);
                        if (is_null($usuario_ex)) {
                            $arr_ex = [
                                'id'               => $id_usuario,
                                'email'            => $json_request->email,
                                'nombre1'          => strtoupper($json_request->nombre1),
                                'nombre2'          => strtoupper($json_request->nombre2),
                                'apellido1'        => strtoupper($json_request->apellido1),
                                'apellido2'        => strtoupper($json_request->apellido2),
                                'id_tipo_usuario'  => '28', //tipo usuario doctor externo
                                'estado'           => '1',
                                'imagen_url'       => 'avatar.jpg',
                                'ocupacion'        => $json_request->ocupacion,
                                'telefono1'        => $json_request->telefono1,
                                'telefono2'        => $json_request->telefono2,
                                'direccion'        => $json_request->direccion,
                                'tipo_documento'   => '1',
                                'id_pais'          => '1', //pendiente revisar
                                'fecha_nacimiento' => $json_request->fecha_nacimiento,
                                'password'         => $json_request->password,
                                'id_usuariocrea'   => 'CREAUSER',
                                'id_usuariomod'    => 'CREAUSER',
                                'ip_creacion'      => '::1',
                                'ip_modificacion'  => '::1',
                                'uso_sistema'      => '1',
                            ];

                            $id_user = User::insertGetId($arr_ex);
                        }
                        return ['respuesta' => 'Success', 'msj' => 'codigo doctor externo creado'];

                    }else{
                        return ['respuesta' => 'Success', 'msj' => 'codigo doctor externo ya existe'];

                    }

                }

                //     DB::commit();
                //     // return ['respuesta' => 'Success', 'msj' => 'usuario creado'];
                // } catch (\Exception $e) {
                //     DB::rollBack();
                //     return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
                // }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function crear_protocolo(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $json_request = json_decode($request->getContent());
                $id_doctor = $json_request->id_doctor;
                $nombre_fav = $json_request->nombre_fav;
                $protocolo = Protocolo::where('estado', '2')->where('id_usuariocrea', $id_doctor)->where('nombre', $nombre_fav)->first();
                if (!is_null($protocolo)) {
                    return ['respuesta' => 'error', 'msj' => 'NOMBRE DEL PERFIL YA SE ENCUENTRA INGRESADO'];
                }

                $arr = [
                    'nombre'          => $nombre_fav,
                    'est_amb_hos'     => '0',
                    'estado'          => '2',
                    'pre_post'        => null,
                    'id_usuariocrea'  => $id_doctor,
                    'id_usuariomod'   => $id_doctor,
                    'ip_creacion'     => '::1',
                    'ip_modificacion' => '::1',
                ];
                $id_protocolo = Protocolo::insertGetId($arr);

                foreach ($json_request->detalles as $examen_pro) {
                    $examen_protocolo = Examen_Protocolo::where('id_protocolo', $id_protocolo)->where('id_examen', $examen_pro->id)->first();

                    if (is_null($examen_protocolo)) {
                        $arr = [
                            'id_protocolo'    => $id_protocolo,
                            'id_examen'       => $examen_pro->id,
                            'ip_creacion'     => '::1',
                            'ip_modificacion' => '::1',
                            'id_usuariocrea'  => $id_doctor,
                            'id_usuariomod'   => $id_doctor,
                        ];

                        Examen_Protocolo::create($arr);
                    }
                }

                return json_encode(['respuesta' => 'success', 'msj' => 'protocolo creado']);
            } else {
                return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
            }
        } else {
            return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
        }
    }

    public function doctor_protocolos(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $protocolos = Protocolo::where('estado', '2')->where('id_usuariocrea', $request->id_doctor)->orderBy('id','DESC')->get();
                $arr_pro = array();
                foreach ($protocolos as $protocolo) {
                    $examen_protocolo = $protocolo->examenes;
                    $det_pro = array();
                    foreach ($examen_protocolo as $value) {
                        $examen = $value->examen;
                        $arr = [
                            'id_examen' => $value->id_examen,
                            'nombre_examen' => $examen->nombre,
                            'valor' => $examen->valor,
                        ];
                        array_push($det_pro, $arr);
                    }

                    $data['favoritos'] = [
                        'id' => $protocolo->id,
                        'nombre' => $protocolo->nombre,
                        'id_usuario' => $protocolo->id_usuariocrea,
                        'detalles' => $det_pro
                    ];

                    array_push($arr_pro, $data);
                }

                return json_encode(['respuesta' => $arr_pro, 'msj' => 'success']);
            } else {
                return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
            }
        } else {
            return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
        }
    }

    public function ordenes_realizadas(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_doctor = $request->id_doctor;
                $tipo = $request->tipo_doctor;
                $codigo_doc = $request->codigo_doc;
                $fecha_desde = $request->fecha_desde;
                $fecha_hasta = $request->fecha_hasta;
                if (is_null($fecha_desde)) {
                    $fecha_desde = date("Y-m-d");
                }
                if (is_null($fecha_hasta)) {
                    $fecha_hasta = date('Y-m-d');
                }
                $examen_orden = [];
                if (!is_null($tipo)) {
                    if ($tipo == 1) {
                        $examen_orden = Examen_Orden::where('pago_online', '0')
                            ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                            ->where('id_doctor_ieced', $id_doctor)
                            ->orderBy('id', 'ASC');

                        if (!is_null($fecha_desde)) {
                            $examen_orden = $examen_orden->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                        } else {
                            $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
                        }
                        $examen_orden = $examen_orden->get();
                    } else if (!is_null($codigo_doc)) {
                        $examen_orden = Examen_Orden::where('pago_online', '0')
                            ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                            ->where('codigo', $codigo_doc)
                            ->orderBy('id', 'ASC');
                        if (!is_null($fecha_desde)) {
                            $examen_orden = $examen_orden->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                        } else {
                            $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
                        }
                        $examen_orden = $examen_orden->get();
                    }
                }


                $arr_ordenes = array();
                foreach ($examen_orden as $value) {

                    $detalles = $value->detalles;
                    $arr_detalles = array();
                    foreach ($detalles as $detalle) {
                        $det = [
                            'nombre_examen' => $detalle->examen->nombre
                        ];

                        array_push($arr_detalles, $det);
                    }

                    $arr = [
                        'id' => $value->id,
                        'id_paciente' => $value->id_paciente,
                        'nombre_paciente' => $value->paciente->nombre1 . ' ' . $value->paciente->apellido1 . ' ' . $value->paciente->apellido2,
                        'estado' => $value->estado,
                        'fecha_orden' => $value->fecha_orden,
                        'id_doctor' => $value->id_doctor_ieced,
                        'nombre_doctor' => $value->doctor->nombre1 . ' ' . $value->doctor->apellido1,
                        'cantidad' => $value->cantidad,
                        'total_valor' => $value->total_valor,
                        'detalles' => $arr_detalles

                    ];
                    array_push($arr_ordenes, $arr);
                }
                return json_encode(['respuesta' => $arr_ordenes, 'msj' => 'success']);
            } else {
                return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
            }
        } else {
            return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
        }
    }

    public function calculo_comision(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $json_request = json_decode($request->getContent());
                $tipo_doctor = $json_request->tipo;
                $id_doctor = $json_request->id_doctor;
                $codigo_doc = $json_request->codigo_doc;
                $comision = 0;
                $examen_comision = Examen_Comision::where('estado', 1);
                if ($tipo_doctor == 1) {
                    $examen_comision = $examen_comision->where('id_doctor', $id_doctor);
                } else {
                    $examen_comision = $examen_comision->where('id_doctor_externo', $codigo_doc);
                }
                $examen_comision = $examen_comision->get();
                $arr_com = array();
                foreach ($json_request->detalles as $detalle) {
                    foreach ($examen_comision as $value) {

                        if ($detalle->id_examen == $value->id_examen) {
                            $comision += ($value->examen->valor * $value->valor_comision) / 100;
                        }
                    }
                }

                return json_encode(['respuesta' => $comision, 'msj' => 'success']);
            } else {
                return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
            }
        } else {
            return json_encode(["respuesta" => 'error', 'msj' => 'CREDENCIALES INVALIDAS']);
        }
    }

    public function ordenes_paciente(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_paciente = $request->id_paciente;
                $id_doctor = $request->id_doctor;
                $codigo_doc = $request->codigo_doc;
                $tipo_doc = $request->tipo_doc;
                $fecha_desde = $request->fecha_desde;
                $fecha_hasta = $request->fecha_hasta;
                if (is_null($fecha_desde)) {
                    $fecha_desde = date("Y-m-d");
                }
                if (is_null($fecha_hasta)) {
                    $fecha_hasta = date('Y-m-d');
                }

                if (!is_null($id_paciente)) {

                    $examen_orden = [];
                    if (!is_null($tipo_doc)) {
                        if ($tipo_doc == 1) {
                            $examen_orden = Examen_Orden::where('examen_orden.pago_online', '0')
                                ->where('examen_orden.doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                                ->where('examen_orden.id_doctor_ieced', $id_doctor)
                                ->where('examen_orden.id_paciente', $id_paciente)
                                ->where('examen_orden.estado', '1')
                                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                                ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced');

                            if (!is_null($fecha_desde)) {
                                $examen_orden = $examen_orden->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                            } else {
                                $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
                            }
                            $examen_orden = $examen_orden->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor')->orderBy('id', 'ASC')->get();

                        } else if (!is_null($codigo_doc)) {
                            $examen_orden = Examen_Orden::where('examen_orden.pago_online', '0')
                                ->where('examen_orden.doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                                ->where('examen_orden.codigo', $codigo_doc)
                                ->where('examen_orden.id_paciente', $id_paciente)
                                ->where('examen_orden.estado', '1')
                                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                                ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced');

                            if (!is_null($fecha_desde)) {
                                $examen_orden = $examen_orden->whereBetween('fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                            } else {
                                $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
                            }
                            $examen_orden = $examen_orden->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor')->orderBy('id', 'ASC')->get();
                        }
                    }

                    return json_encode(['respuesta' => $examen_orden, 'status' => 'success']);
                } else {
                    return json_encode(["respuesta" => 'No existe', 'status' => 'error']);
                }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
    }

    public function total_comision(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_doctor = $request->id_doctor;
                $codigo_doc = $request->codigo_doc;
                $tipo_doc = $request->tipo_doc;
                $anio = $request->anio;
                $mes = $request->mes;
                $examen_orden = [];
                $total = 0;
                if ($tipo_doc == 1) {
                    $examen_orden = Examen_Orden::where('pago_online', '0')
                        ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                        ->where('id_doctor_ieced', $id_doctor)
                        ->where('estado', '1')
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->where('estado_comision2', '1')
                        ->orderBy('id', 'ASC')
                        ->get();
                } else if (!is_null($codigo_doc)) {
                    $examen_orden = Examen_Orden::where('pago_online', '0')
                        ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                        ->where('codigo', $codigo_doc)
                        ->where('estado', '1')
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->where('estado_comision2', '1')
                        ->orderBy('id', 'ASC')
                        ->get();
                }

                if (count($examen_orden) > 0) {
                    foreach ($examen_orden as $value) {
                        $detalles = $value->detalles;
                        foreach ($detalles as $det) {
                            $total += $det->valor_comision;
                        }
                    }
                }

                return ["respuesta" => $total, "status" => "success"];
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }
    }

    public function total_ordenes(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_doctor = $request->id_doctor;
                $codigo_doc = $request->codigo_doc;
                $tipo_doc = $request->tipo_doc;
                $anio = $request->anio;
                $mes = $request->mes;
                $examen_orden = [];
                if ($tipo_doc == 1) {
                    $examen_orden = Examen_Orden::where('pago_online', '0')
                        ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                        ->where('id_doctor_ieced', $id_doctor)
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->groupBy('estado')
                        ->select('estado', DB::raw('COUNT(id) as cantidad'))
                        ->get();
                } else if (!is_null($codigo_doc)) {
                    $examen_orden = Examen_Orden::where('pago_online', '0')
                        ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                        ->where('codigo', $codigo_doc)
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->groupBy('estado')
                        ->select('estado', DB::raw('COUNT(id) as cantidad'))
                        ->get();
                }

                $arr_ordenes  = array();
                $total = 0;
                foreach ($examen_orden as $value) {
                    $total += $value->cantidad;

                    $tot = [
                        "estado" => $value['estado'],
                        "cantidad" => is_null($value['cantidad']) ? 0 : $value['cantidad'],
                    ];

                    if ($value['estado'] == 0) {
                        $data['anuladas'] = $tot;
                    }

                    if ($value['estado'] == '-1') {
                        $data['cotizaciones'] = $tot;
                    }

                    if ($value['estado'] == 1) {
                        $data['ordenes'] = $tot;
                    }
                }
                
                if (!isset($data['anuladas'])) {
                    $data['anuladas'] = [
                        "estado" => 0,
                        "cantidad" => 0,
                    ];
                }
                if (!isset($data['cotizaciones'])) {
                    $data['cotizaciones'] = [
                        "estado" => '-1',
                        "cantidad" => 0,
                    ];
                }
                if (!isset($data['ordenes'])) {
                    $data['ordenes'] = [
                        "estado" => 1,
                        "cantidad" => 0,
                    ];
                }
                $data['total'] = $total;


                return json_encode(['respuesta' => $data, 'status' => 'success']);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }
    }

    public function buscar_protocolos(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {

                $id_doctor = $request->id_doctor;
                $protocolos = Protocolo::where('estado', '2')->where('id_usuariocrea', $id_doctor)->get();

                $arr_pro = array();
                foreach ($protocolos as $protocolo) {
                    $examen_protocolo = $protocolo->examenes;
                    $det_pro = array();
                    foreach ($examen_protocolo as $value) {
                        $examen = $value->examen;
                        $arr = [
                            'id_examen' => $value->id_examen,
                            'nombre_examen' => $examen->nombre,
                            'valor' => $examen->valor,
                        ];
                        array_push($det_pro, $arr);
                    }

                    $data['favoritos'] = [
                        'id' => $protocolo->id,
                        'nombre' => $protocolo->nombre,
                        'id_usuario' => $protocolo->id_usuariocrea,
                        'detalles' => $det_pro
                    ];

                    array_push($arr_pro, $data);
                }

                dd($arr_pro);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }
    }

    public function user_delete(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_doctor = $request->id_doctor;
                $codigo_doc = $request->codigo_doc;
                $tipo_doc = $request->tipo_doc;
                if (!is_null($id_doctor)) {
                    $user = User::find($id_doctor);
                    if (!is_null($user)) {
                        $user->update([
                            'id_tipo_usuario' => 2,
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_modificacion'  => '::1',
                        ]);
                    }
                }


                if ($tipo_doc == 2) {
                    if (!is_null($codigo_doc)) {
                        $doc_externo = Labs_doc_externos::where('id', 'like', '%' . $codigo_doc . '%')->first();
                        if (!is_null($doc_externo)) {
                            $doc_externo->update([
                                'estado' => 0,
                                'id_usuariomod'    => 'CREAUSER',
                            ]);
                        }
                    }
                }

                return  json_encode(['respuesta' => 'ELIMINADO', 'status' => 'success']);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }
    }

    public function comisiones_mes(Request $request)
    {
        if ($request->header('appid') == null) {

            return json_encode(["respuesta" => 'CREDENCIALES INVALIDA VACIO', 'status' => 'error']);
        }


        $id_doctor = $request->id_doctor;
        $codigo_doc = $request->codigo_doc;
        $tipo_doctor = $request->tipo_doctor;
        $anio = $request->anio;
        $mes = $request->mes;
        if (is_null($request->mes)) {
            $mes = ['1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
        }

        if (is_null($request->anio)) {
            $anio = date('Y');
        }
        $arr_ordenes  = array();

        foreach ($mes as $key => $value) {
            $arr = [
                'id_mes' => $key,
                'mes' => $value,
                'total_comision' => 0
            ];

            if ($tipo_doctor == 1) {
                $examen_orden = Examen_Orden::where('pago_online', '0')
                    ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                    ->where('id_doctor_ieced', $id_doctor)
                    ->where('estado', '1')
                    ->where('anio', $anio)
                    ->where('mes', $key)
                    ->where('estado_comision2', '1')
                    ->groupBy('mes')
                    ->select('mes', DB::raw('SUM(total_comision) as total_comision'))
                    ->first();
            } else if (!is_null($codigo_doc)) {
                $examen_orden = Examen_Orden::where('pago_online', '0')
                    ->where('doctor_txt', 'like', '%CREADO EN LABS_DOCTOR WEB%')
                    ->where('codigo', $codigo_doc)
                    ->where('estado', '1')
                    ->where('anio', $anio)
                    ->where('mes', $key)
                    ->where('estado_comision2', '1')
                    ->groupBy('mes')
                    ->select('mes', DB::raw('SUM(total_comision) as total_comision'))
                    ->first();
            }

            if (!is_null($examen_orden)) {
                $arr['total_comision'] = $examen_orden->total_comision;
            }
            array_push($arr_ordenes, $arr);
        }



        // dd($arr_ordenes);

        return  json_encode(['respuesta' => $arr_ordenes, 'status' => 'success']);
    }
    
    public function examenes_agrupados(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            //code...

            $agrupador =  Examen_Agrupador_Labs::where('estado', 1)->get();
            $global = [];
            foreach ($agrupador as $ag) {
                $examenes  = Examen::where('examen.estado', '1')
                    ->where('examen.valor', '>', 0)
                    ->where('examen.id', '<>', '1124')
                    ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
                    ->where('s.id_examen_agrupador_labs', $ag->id)
                    ->where('s.estado', 1)
                    ->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'), 'examen.nombre_largo', 'examen.sugerencia', 's.id_examen_agrupador_labs')
                    ->orderBy('s.id_examen_agrupador_labs', 's.nro_orden')->get();
                $data_ex = [];
                foreach ($examenes as $exam) {
                    $arr = [
                        'id' => $exam->id,
                        'title' => $exam->nombre,
                        'nombre_largo' => $exam->nombre_largo,
                        'price' => $exam->valor,
                        'sugerencia' => $exam->sugerencia,
                        'id_agrupador' => $exam->id_examen_agrupador_labs,
                    ];
                    array_push($data_ex, $arr);
                }

                $data['id_grupo'] = $ag->id;
                $data['grupo'] = $ag->nombre;
                $data['examenes'] = $data_ex;

                array_push($global, $data);
            }

            return $global;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }
}
