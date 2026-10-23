<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use DateTime;
use DatePeriod;
use DateInterval;
use Mail;
use Sis_medico\Agenda;
use Sis_medico\CierreCaja;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Eliminado;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\HorarioDoctorApp;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\TransaccionPago;
use Sis_medico\User;

class BdAppLabsController extends Controller
{
    public function horario_agendas(Request $request)
    {
        try {
            $tipo = $request['tipo'];
            if ($tipo == 0) {
                // $id_doctor = '444444444A';
                $id_doctor = '4444444444';
                $minutes_to_add = 5;
            }
            if ($tipo == 1) {
                // $id_doctor = '444444444B';
                $id_doctor = '4444444444';
                $minutes_to_add = 40;
            }
            $date = $request['fecha'];
            $dia = date('N', strtotime($date));
            $limit_horario = $request['horario'];
            $horario = HorarioDoctorApp::where('id_doctor', $id_doctor)->where('ndia', $dia)->where('estado', 1)->where('tipo', 2)->get();
            $hoursfree = array();
            foreach ($horario as $value) {
                $ini = date('H:i', strtotime($value->hora_ini));
                $fin = date('H:i', strtotime($value->hora_fin));
                $limiteHora = new DateTime('12:00');
                $fechaInicio = new DateTime($ini);
                $fechaFin = new DateTime($fin);
                if ($limit_horario == 1) {
                    $fechaInicio = new DateTime($ini);
                    $fechaFin = $limiteHora;
                }
                if ($limit_horario == 2) {
                    $fechaInicio = $limiteHora;
                    $fechaFin = new DateTime($fin);
                }
                $rangoFechas = new DatePeriod($fechaInicio, new DateInterval('PT' . $minutes_to_add . 'M'), $fechaFin);
                $validAgenda = Agenda::whereBetween('fechaini', [$date . ' ' . $fechaInicio->format("H:i") . ':00', $date . ' ' .  $fechaFin->format('H:i') . ':00'])
                    ->where('id_doctor1', $id_doctor)
                    ->where('estado', 1)
                    //->where('observaciones', 'like', '%COTIZACION GENERADA EN APP LABS%')
                    ->where('proc_consul', 0)
                    ->where('estado_app', 1)
                    ->orderBy('fechaini', 'asc')
                    ->get();
                $aux = array();
                if (count($validAgenda) > 0) {
                    foreach ($validAgenda as $val2) {
                        array_push($aux, $val2->fechaini);
                    }
                }
                foreach ($rangoFechas as $fecha) {
                    $time = new DateTime($request['fecha'] . ' ' . $fecha->format("H:i"));
                    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                    $stamp = $time->format('H:i');

                    $fechaArr = $date . ' ' . $fecha->format("H:i") . ':00';
                    if (count($validAgenda) > 0) {
                        if (!in_array($fechaArr, $aux)) {
                            $ph['fechaini'] = $date . ' ' . $fecha->format("H:i");
                            $ph['fechafin'] = $date . ' ' . $stamp;
                            $ph['rango'] = $fecha->format("H:i") . ' - ' . $stamp;
                            array_push($hoursfree, $ph);
                        }
                        // foreach ($validAgenda as $ag) {
                        //     if ($ag->fechaini != $date . ' ' . $fecha->format("H:i") . ':00') {
                        //         $ph['fechaini'] = $date . ' ' . $fecha->format("H:i");
                        //         $ph['fechafin'] = $date . ' ' . $stamp;
                        //         $ph['rango'] = $fecha->format("H:i") . ' - ' . $stamp;
                        //         array_push($hoursfree, $ph);
                        //         break;
                        //     }
                        // }
                    } else {
                        $ph['fechaini'] = $date . ' ' . $fecha->format("H:i");
                        $ph['fechafin'] = $date . ' ' . $stamp;
                        $ph['rango'] = $fecha->format("H:i") . ' - ' . $stamp;
                        array_push($hoursfree, $ph);
                    }
                }
            }
            return $hoursfree;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function listaExamenes(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $examenes  = Examen::selectRaw("examen.id, examen.valor as valorOri, examen.nombre, examen.nombre_largo, examen.sugerencia, IF(examen.nombre like '%test%', 1,0) as check_test")
            ->where('examen.estado', '1')
            ->where('examen.valor', '>', 0)
            ->where('examen.id', '<>', '1124')
            ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
            ->where('s.estado', 1)
            ->orderBy('examen.nombre', 'ASC')
            ->get();

        return response()->json($examenes);
    }

    public function new_crearOrdenLabs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {

            $json_request = $request->all();
            $tipo_pago              = $json_request['tipopago']; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
            $membresia_id           = $json_request['membresia_id']; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
            $membresia_detalle_id   = $json_request['membresia_detalle_id'];
            $fecha_inicio           = $json_request['fecha_inicio'];
            $fecha_fin              = $json_request['fecha_fin'];
            $ip_cliente             = $_SERVER["REMOTE_ADDR"];
            if ($membresia_id == 0) {
                $membresia_id = null;
            }
            $id_seguro              = '1';
            $id_protocolo           = null;
            $id_nivel               = null;
            $total                  = 0;
            $contador               = 0;
            $descuento_detalle      = 0;
            $empresa_labs = Empresa::where('prioridad_labs', '1')->first();
            $cedula_paciente  = $json_request['id_paciente'];
            $paciente = Paciente::find($cedula_paciente);

            $valAgenda = BdAppLabsController::disponibilidadAgenda($json_request);
            if ($valAgenda['status'] == "error") {
                return $valAgenda;
            }

            $verifyPatient = BdAppLabsController::verifyPatient($json_request['usuario']);
            if ($verifyPatient['status'] == 'error') {
                return $verifyPatient;
            }
            ///datafast///
            $estado = '-1';
            $realizado = 0;
            $estado_pago = 0;
            $txt_online = "";
            $pago_online = 0;
            if ($json_request['id_forma_pago'] == '10') {
                $estado = 1;
                $realizado = 1;
                $estado_pago = 1;
                $pago_online = 1;
            }
            //////
            $input_ex = [
                'id_paciente'          => $paciente['id'],
                'fecha_tentativa'      => $json_request['fecha_inicio'],
                'cedula_factura'       => $json_request['datos_factura']['id_usuario'],
                'nombre_factura'       => $json_request['datos_factura']['nombre1'] . ' ' . $json_request['datos_factura']['apellido1'] . ' ' . $json_request['datos_factura']['apellido2'],
                'direccion_factura'    => $json_request['datos_factura']['direccion'],
                'ciudad_factura'       => $json_request['datos_factura']['ciudad'],
                'email_factura'        => $json_request['datos_factura']['email_factura'],
                'telefono_factura'     => $json_request['datos_factura']['telefono_factura'],
                'anio'                 => date('Y', strtotime($json_request['fecha_inicio'])),
                'mes'                  => date('m', strtotime($json_request['fecha_inicio'])),
                'fecha_orden'          => $json_request['fecha_inicio'],
                'id_protocolo'         => $id_protocolo,
                'id_seguro'            => $id_seguro,
                'id_nivel'             => $id_nivel,
                'est_amb_hos'          => '0',
                'id_doctor_ieced'      => '1234517896',
                'doctor_txt'           => 'COTIZACION GENERADA EN APP MÒVIL',
                'observacion'          => '',
                'id_empresa'           => $empresa_labs->id,
                'cantidad'             => '0',
                'estado'               => $estado,
                'realizado'            => $realizado,
                'total_valor'          => $json_request['total1'],
                'valor'                => $json_request['total1'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => '1234517896',
                'id_usuariomod'        => '1234517896',
                'motivo_descuento'     => '',
                'membresia_id'         => $membresia_id,
                'membresia_detalle_id' => $membresia_detalle_id,
                'observacion_app'      => $json_request['observacion_app'],
                'observacion_adicional' => 'COTIZACION GENERADA EN APP LABS',
                'motivo_descuento'     => $json_request['motivo_descuento'],
                'descuento_p'          => $json_request['descuento_p'],
                'descuento_valor'      => $json_request['descuento_valor'],
                'pago_online'          => $pago_online
            ];
            $id_orden = Examen_Orden::insertGetId($input_ex);
            foreach ($json_request['detalles'] as $pexamen) {
                //CREA DETALLE
                $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_orden)->first();
                if (is_null($detalle)) {
                    $contador++;
                    $examen = Examen::find($pexamen['id']);
                    $valor    = $pexamen['total']; //AQUI PONERMOS EL VALOR YA CON DESCUENTO DE MEMBRESIA Y TODO DE LA APP
                    $cubre    = 'NO';
                    $ex_nivel = Examen_Nivel::where('id_examen', $pexamen['id'])->where('nivel', $id_nivel)->first();
                    if (!is_null($ex_nivel)) {
                        if ($ex_nivel->valor1 != 0) {
                            $valor = $ex_nivel->valor1;
                            $cubre = 'SI';
                        }
                    }
                    $input_det = [
                        'id_examen_orden' => $id_orden,
                        'id_examen'       => $pexamen['id'],
                        'valor'           => $valor,
                        'cubre'           => $cubre,
                        'p_descuento'     => $pexamen['pdiscount'],
                        'valor_descuento' => $pexamen['discount'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                        'id_usuariomod'   => '1234517896',
                    ];
                    Examen_Detalle::create($input_det);
                    $total += $valor;
                    $descuento_detalle += $pexamen['discount'];
                }
            }

            $total             = $total;
            $recargo_p         = 0;
            $subtotal_pagar    = $total - $descuento_detalle;
            $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor     = $recargo_valor;
            $valor_total       = $subtotal_pagar + $recargo_valor;
            $valor_total       = $valor_total;

            if ($tipo_pago == "ORD") {
                $orden     = Examen_Orden::find($id_orden);
                $input_ex2 = [
                    'recargo_valor'   => $recargo_valor,
                    'total_valor'     => $total - $descuento_detalle,
                    'cantidad'        => $contador,
                    'valor'           => $total,
                    'descuento_valor' => $descuento_detalle,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'estado_pago'     => $estado_pago,
                    'pres_dom'        => $json_request['pres_dom'],
                ];
                $orden->update($input_ex2);
            }

            $apps_orden = DB::table('apps_orden')->insertGetId([
                'id_orden' => $id_orden,
                'id_usuario' => $paciente['id'],
                'estado' => 1,
                'total' => $valor_total
            ]);

            $arr_pago     = [
                'id_examen_orden' => $id_orden,
                'id_tipo_pago'    => $json_request['id_forma_pago'],
                'valor'           => $total - $descuento_detalle,
                'posee_fi'        => 0,
                'p_fi'            => 0,
                'fecha'           => date('Y-m-d', strtotime($json_request['fecha_inicio'])),
                'id_usuariocrea'  => '1234517896',
                'id_usuariomod'   => '1234517896',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            Examen_Detalle_Forma_Pago::create($arr_pago);

            if ($json_request['id_forma_pago'] == '10') {
                $resultados = SinLoginController::crear_resultados($id_orden);
                if ($resultados['status'] == 'error') {
                    return $resultados;
                }
                $txt_online = "RESULTADOS GENERADOS POR APP";
                $orden = Examen_Orden::find($id_orden);
                $idcierre = CierreCaja::insertGetid([
                    'fecha'           => $orden->fecha_orden,
                    'tipo'            => '1',
                    'id_paciente'     => $orden->id_paciente,
                    'id_seguro'       => $orden->id_seguro,
                    'descripcion'     => 'La Orden : ' . $orden->id . ' del paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
                    'valor'           => $orden->total_valor,
                    'saldo'           => $orden->total_valor,
                    'id_orden'        => $orden->id,
                    'estado'          => '1',
                    'ip_creacion'     => '10.0.0.1',
                    'ip_modificacion' => '10.0.0.1',
                    'id_usuariocrea'  => '0000000004',
                    'id_usuariomod'   => '0000000004',
                ]);
            }

            $id_agenda = "";
            if (!is_null($fecha_inicio) && !is_null($fecha_fin)) {
                $id_agenda = BigDataController::crea_agenda_labs($id_orden, $fecha_inicio, $fecha_fin);
            }

            Log_usuario::create([
                'id_usuario'  => 1234517896,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA ORDEN EXAMEN APP LABS",
                'dato_ant1'   => "ID ORDEN: " . $id_orden . "ID AGENDA: " . $id_agenda,
                'dato1'       => $paciente['id'],
                'dato_ant4'   => $txt_online,

            ]);

            return ['id_orden' => $id_orden, 'status' => 'success', 'id_agenda' => $id_agenda, 'msj' => 'Orden Generada'];
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(), 'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public static function verifyPatient($data)
    {
        try {
            $verifiyCorreo = User::where('email', $data['email'])->first();
            // dd($data, $verifiyCorreo);
            $paciente = Paciente::find($data['id']);
            $user = user::find($data['id']);
            if (is_null($paciente)) {
                if (is_null($user)) {
                    if (!is_null($verifiyCorreo)) {
                        return ['status' => 'error', 'msj' => 'Correo registrado en otro usuario'];
                    }
                    $arr_us = [
                        'id'               => $data['id'],
                        'email'            => $data['email'],
                        'nombre1'          => strtoupper($data['nombre1']),
                        'id_tipo_usuario'  => 2,
                        'nombre2'          => strtoupper($data['nombre2']),
                        'apellido1'        => strtoupper($data['apellido1']),
                        'apellido2'        => strtoupper($data['apellido2']),
                        'estado'           => '1',
                        'imagen_url'       => ' ',
                        'telefono1'        => $data['telefono1'],
                        'direccion'        => $data['direccion'],
                        'tipo_documento'   => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $data['fecha_nacimiento'],
                        'password'         => bcrypt($data['id']),
                        'id_usuariocrea'   => 'CREAUSER',
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                    ];
                    $id_usuario = User::insertGetId($arr_us);
                } else {
                    $usUp = [
                        'email'            => is_null($data['email']) ? $user->email : $data['email'],
                        'nombre1'          => is_null($data['nombre1']) ?  $user->nombre1 : strtoupper($data['nombre1']),
                        'nombre2'          => is_null($data['nombre2']) ? $user->nombre2 : strtoupper($data['nombre2']),
                        'apellido1'        => is_null($data['apellido1']) ? $user->apellido1 : strtoupper($data['apellido1']),
                        'apellido2'        => is_null($data['apellido2']) ? $user->apellido2 : strtoupper($data['apellido2']),
                        'telefono1'        => is_null($data['telefono1']) ? $user->telefono1 : $data['telefono1'],
                        'direccion'        => is_null($data['direccion']) ? $user->direccion : $data['direccion'],
                        'fecha_nacimiento' => is_null($data['fecha_nacimiento']) ? $user->fecha_nacimiento : $data['fecha_nacimiento'],
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_modificacion'  => '::1',
                    ];
                    $user->update($usUp);
                }
                if (is_null($data['sexo'])) {
                    return ['status' => 'error', 'msj' => 'Campo sexo vacio'];
                }
                $input_pac = [
                    'id'                 => $data['id'],
                    'id_usuario'         => $data['id'],
                    'nombre1'            => strtoupper($data['nombre1']),
                    'nombre2'            => strtoupper($data['nombre2']),
                    'apellido1'          => strtoupper($data['apellido1']),
                    'apellido2'          => strtoupper($data['apellido2']),
                    'fecha_nacimiento'   => $data['fecha_nacimiento'],
                    'sexo'               => $data['sexo'],
                    'telefono1'          => $data['telefono1'],
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => 1,
                    'imagen_url'         => ' ',
                    'menoredad'          => 0,
                    'ip_creacion'        => '::1',
                    'ip_modificacion'    => '::1',
                    'id_usuariocrea'     => 'CREAUSER',
                    'id_usuariomod'      => 'CREAUSER',
                    'otro'              => 'APP LABS',

                ];
                Paciente::create($input_pac);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP LABS",
                    'dato_ant1'   => $data['id_paciente'],
                    'dato1'       => strtoupper($data['nombre1'] . " " . $data['nombre2'] . " " . $data['apellido1'] . " " . $data['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP LABS',
                ];
                Log_usuario::create($input_log);
                return ['status' => 'success', 'msj' => 'Paciente creado correctamente'];
            } else {
                $pacUp = [
                    'nombre1'            => is_null($data['nombre1']) ? $paciente->nombre1 : strtoupper($data['nombre1']),
                    'nombre2'            => is_null($data['nombre2']) ? $paciente->nombre2 : strtoupper($data['nombre2']),
                    'apellido1'          => is_null($data['apellido1']) ? $paciente->apellido1 : strtoupper($data['apellido1']),
                    'apellido2'          => is_null($data['apellido2']) ? $paciente->apellido2 : strtoupper($data['apellido2']),
                    'fecha_nacimiento'   => is_null($data['fecha_nacimiento']) ? $paciente->fecha_nacimiento : $data['fecha_nacimiento'],
                    'sexo'               => is_null($data['sexo']) ? $paciente->sexo : $data['sexo'],
                    'telefono1'          => is_null($data['telefono1']) ? $paciente->telefono1 : $data['telefono1'],
                    'ip_modificacion'    => '::1',
                    'id_usuariomod'      => 'CREAUSER',
                    'otro'              => 'APP LABS',
                ];
                $paciente->update($pacUp);
                if (is_null($user)) {
                    if (!is_null($verifiyCorreo)) {
                        return ['status' => 'error', 'msj' => 'Correo registrado en otro usuario'];
                    }
                    $arr_us = [
                        'id'               => $data['id'],
                        'email'            => $data['email'],
                        'nombre1'          => strtoupper($data['nombre1']),
                        'id_tipo_usuario'  => 2,
                        'nombre2'          => strtoupper($data['nombre2']),
                        'apellido1'        => strtoupper($data['apellido1']),
                        'apellido2'        => strtoupper($data['apellido2']),
                        'estado'           => '1',
                        'imagen_url'       => ' ',
                        'telefono1'        => $data['telefono1'],
                        'direccion'        => $data['direccion'],
                        'tipo_documento'   => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $data['fecha_nacimiento'],
                        'password'         => bcrypt($data['id_paciente']),
                        'id_usuariocrea'   => 'CREAUSER',
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                    ];
                    $id_usuario = User::insertGetId($arr_us);
                } else {
                    $usUp = [
                        'email'            => is_null($data['email']) ? $user->email : $data['email'],
                        'nombre1'          => is_null($data['nombre1']) ?  $user->nombre1 : strtoupper($data['nombre1']),
                        'nombre2'          => is_null($data['nombre2']) ? $user->nombre2 : strtoupper($data['nombre2']),
                        'apellido1'        => is_null($data['apellido1']) ? $user->apellido1 : strtoupper($data['apellido1']),
                        'apellido2'        => is_null($data['apellido2']) ? $user->apellido2 : strtoupper($data['apellido2']),
                        'telefono1'        => is_null($data['telefono1']) ? $user->telefono1 : $data['telefono1'],
                        'direccion'        => is_null($data['direccion']) ? $user->direccion : $data['direccion'],
                        'fecha_nacimiento' => is_null($data['fecha_nacimiento']) ? $user->fecha_nacimiento : $data['fecha_nacimiento'],
                        'id_usuariomod'    => 'CREAUSER',
                        'ip_modificacion'  => '::1',
                    ];
                    $user->update($usUp);
                }
                return ['status' => 'success', 'msj' => 'Paciente actualizado correctamente'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function formas_pago(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $formas_pago = Ct_Tipo_Pago::where('app', 1)->get();
        $arr = array();
        foreach ($formas_pago as $value) {
            $data['id'] = $value->id;
            $data['nombre'] = $value->descripcion_app;
            array_push($arr, $data);
        }
        return $arr;
    }

    public function detalle_orden(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $tipo = $request['tipo'];
            $id_orden = $request['id_orden'];

            $orden = Examen_orden::where('examen_orden.id', $id_orden)
                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                ->where('s.tipo', $tipo)
                ->where('examen_orden.estado', '-1')
                ->select('examen_orden.*')
                ->first();

            if (is_null($orden)) {
                return ['status' => 'error', 'msj' => 'Orden no encontrada'];
            }

            $data['id'] = $orden->id;
            $data['id_paciente'] = $orden->id_paciente;
            $data['nombre_paciente'] = $orden->paciente->nombre1 . ' ' . $orden->paciente->nombre2;
            $data['apellido_paciente'] =  $orden->paciente->apellido1 . ' ' . $orden->paciente->apellido2;
            $data['anio'] = $orden->anio;
            $data['mes'] = $orden->mes;
            $data['cantidad'] = $orden->cantidad;
            $data['fecha_orden'] = substr($orden->fecha_orden, 0, 10);
            $data['id_seguro'] = $orden->seguro->nombre;
            $data['tipo_seguro'] = $orden->seguro->tipo;
            $data['valor'] = $orden->total_valor;
            $data['doctor'] = $orden->doctor->nombre1 . ' ' . $orden->doctor->apellido1;
            $data['subtotal'] = $orden->valor;
            $data['descuento'] = $orden->descuento_valor;
            $data['total_final'] = $orden->total_valor;
            $data['domicilio'] = is_null($orden->pres_dom) ? 0 : $orden->pres_dom;
            $data['por_desc'] = $orden->descuento_p;
            $data['url'] = url('labs/cotizacion/externos/imprimir') . '/' . $orden->id;
            $data['url_visualizar']  = url('/bd/view_receta') . '/' . $orden->id . '/6';
            $data['mensaje'] = "Orden con seguro privado para mas información contactarse con el departamento comercial";
            $data['num_telef'] = "+593985090215";


            $detallearr = array();
            foreach ($orden->detalles as $value) {
                $arr_det = [
                    'id_examen'       => $value->id_examen,
                    'id_examen_orden' => $value->id_examen_orden,
                    'examen'          => $value->examen->nombre,
                    'cantidad'        => $value->cantidad,
                    'precio_unitario' => $value->valor,
                    'porc_descuento'  => $value->p_descuento,
                    'descuento'       => is_null($value->valor_descuento) ? 0 : $value->valor_descuento,
                ];
                array_push($detallearr, $arr_det);
                $data['examenes'] = $detallearr;
            }

            return $data;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function verificarAgenda(Request $request)
    {
        try {
            $data = $request->all();
            if ($data['pres_dom'] == 0) {
                // $id_doctor = '444444444A';
                $id_doctor = '444444444A'; //sucursales porque no tienen domicilio
            } else {
                $id_doctor = '444444444B';
            }
            $fecha_ini = $data['fecha_inicio'];
            $fecha_fin = $data['fecha_fin'];

            $validar = Agenda::where('fechaini', $fecha_ini)
                ->where('id_doctor1', $id_doctor)
                ->where('estado', 1)
                ->where('proc_consul', 0)
                ->where('observaciones', 'like', '%COTIZACION GENERADA EN APP LABS%')
                ->first();

            if (!is_null($validar)) {
                return response()->json(['status' => 'error', 'msj' => 'Horario no disponible']);
            }
            return response()->json(['status' => 'success', 'msj' => 'Horario disponible']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()]);
        }
    }

    public static function disponibilidadAgenda($data)
    {
        try {
            //$data = $request->all();
            if ($data['pres_dom'] == 0) {
                // $id_doctor = '444444444A';
                $id_doctor = '444444444A'; //sucursales porque no tienen domicilio
            } else {
                $id_doctor = '444444444B';
            }
            $fecha_ini = $data['fecha_inicio'];
            $fecha_fin = $data['fecha_fin'];

            $validar = Agenda::where('fechaini', $fecha_ini)
                ->where('id_doctor1', $id_doctor)
                ->where('estado', 1)
                ->where('proc_consul', 0)
                ->where('observaciones', 'like', '%COTIZACION GENERADA EN APP LABS%')
                ->first();

            if (!is_null($validar)) {
                return ['status' => 'error', 'msj' => 'Horario no disponible'];
            }
            return ['status' => 'success', 'msj' => 'Horario disponible'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function editar_ordenTM(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        try {
            $data = $request->all();
            $orden = Examen_orden::where('id', $data['id_orden'])->first();

            if (is_null($orden)) {
                return ['status' => 'error', 'msj' => 'Orden no encontrada'];
            }
            $orden_agenda = Examen_Orden_Agenda::where('id_orden', $orden->id)->first();

            $cantidad_ex = count($data['examenes']);
            $orden->estado_pago     = "1";
            $orden->estado          = "1";
            $orden->realizado       = "1";
            $orden->cantidad        = $cantidad_ex;
            $orden->valor           = $data['subtotal'];
            $orden->total_valor     = $data['total_final'];
            $orden->descuento_valor = $data['descuento'];
            $orden->fecha_orden     = date('Y-m-d h:i:s');
            $orden->pago_online     = "1";
            $orden->save();

            $idcierre = CierreCaja::insertGetid([
                'fecha'           => $orden->fecha_orden,
                'tipo'            => '1',
                'id_paciente'     => $orden->id_paciente,
                'id_seguro'       => $orden->id_seguro,
                'descripcion'     => 'La Orden : ' . $orden->id . ' del paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
                'valor'           => $orden->total_valor,
                'saldo'           => $orden->total_valor,
                'id_orden'        => $orden->id,
                'estado'          => '1',
                'ip_creacion'     => '10.0.0.1',
                'ip_modificacion' => '10.0.0.1',
                'id_usuariocrea'  => '0000000004',
                'id_usuariomod'   => '0000000004',
            ]);

            if (is_null($data['examenes_eliminados']) || isset($data['examenes_eliminados'])) {
                foreach ($data['examenes_eliminados'] as $value) {
                    $det = Examen_Detalle::where('id_examen', $value['id_examen'])->where('id_examen_orden', $value['id_examen_orden'])->first();

                    $atributos = $det['attributes'];
                    // $atributos['id_usuariomod'] = 'CREAUSER';
                    // $atributos['id_usuariocrea'] = 'CREAUSER';
                    // $atributos['ip_creacion'] = '10.0.0.1';
                    // $atributos['ip_modificacion'] = '10.0.0.1';
                    Examen_Detalle_Eliminado::create($atributos);
                    $det->delete();
                }
            }
            //funcion de victor para resultados
            $resultados = SinLoginController::crear_resultados($orden->id);
            if ($resultados['status'] == 'error') {
                return $resultados;
            }

            $agenda = Agenda::where('id', $orden_agenda->id_agenda)->where('estado', 1)->first();
            $agenda->estado_cita = '4';
            $agenda->save();

            Log_Agenda::create([
                'id_agenda'       => $agenda->id,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => $request->dateini,
                'fechafin'        => $request->dateend,
                'estado'          => '1',
                'observaciones'   => 'FUE PAGADO DESDE LA APP OPCION TOMA DE MUESTRA Y SE CAMBIO EL ESTADO_CITA A 4',
                'descripcion'     => 'FUE PAGADO DESDE LA APP OPCION TOMA DE MUESTRA Y SE CAMBIO EL ESTADO_CITA A 4',
                'id_usuariomod'   => 'CREAUSER',
                'id_usuariocrea'  => 'CREAUSER',
                'ip_modificacion' => '10.0.0.1',
                'ip_creacion'     => '10.0.0.1',
            ]);

            Log_usuario::create([
                'id_usuario'  => $orden->paciente->id_usuario,
                'ip_usuario'  => 'API PAGO DATAFAST',
                'descripcion' => "ACTUALIZA ORDEN Y AGENDA LABORATORIO X TOMA DE MUESTRA EN APP",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN POR DATAFAST" . $orden->id . '--' . $agenda->id,
            ]);

            return ['status' => 'success', 'msj' => 'Orden actualizada correctamente', 'id_orden' => $orden->id, 'id_agenda' => $agenda->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function transaccion_confirmadaTM(Request $request)
    {
        try {
            $json_request = $request->all();
            $requestId    = $json_request['requestId'];
            $response = TransaccionPago::where('id_check_app', $requestId)->where('estado', 1)->first();
            if (is_null($response)) {
                return ['status' => 'error', 'msj' => 'Transaccion no aprobada'];
            }
            $arr_datos = json_decode($response->data_request, true);

            // //funcion de victor para resultados
            // $resultados = SinLoginController::crear_resultados($arr_datos['id_orden']);
            // return $resultados;
            // if ($resultados['status'] == 'error') {
            //     return $resultados;
            // }
            return ['status' => 'success', 'msj' => 'Transaccion Aprobada'];
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }
}
