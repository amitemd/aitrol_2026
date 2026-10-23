<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Agenda;
use Sis_medico\Apps_Agenda;
use Sis_medico\BdPagoEnLinea;
use Sis_medico\Contable;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\servicios\ServiciosIecedController;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\LogPagoEnLinea;
use Sis_medico\Membresia;
use Sis_medico\Paciente;
use Sis_medico\TransaccionPago;
use Sis_medico\User;
use Sis_medico\UserMembresia;

class BdPagosController extends Controller
{
    public function bpIeced(Request $request)
    {
        // return $request->all();
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $detalle = $request->all();
            $empresa = Empresa::where('prioridad', 1)->first();
            if ($detalle['datos_orden']['tipopago'] == 'ORD') {
                $datos['fecha_ini'] = $detalle['datos_orden']['dateini'];
                $datos['fecha_fin'] = $detalle['datos_orden']['dateend'];
                $datos['fecha'] = date('Y-m-d', strtotime($detalle['datos_orden']['dateini']));
                $datos['id_doctor'] = $detalle['datos_orden']['id_doctor'];
                $cant_agendas = BdIecedController::verificarAgenda($datos);
                if ($cant_agendas['status'] == 'error') {
                    return $cant_agendas;
                }
                // $verificar = BdIecedController::VerificarDisponibilidad($datos);
                // if ($verificar['status'] == 'error') {
                //     return $verificar;
                // }

                if (is_null($detalle['identificacion'])) {
                    return ['status' => 'error', 'msj' => 'identificacion vacio'];
                }
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
            $data['checkid'] = BdPagoEnLinea::checkId($detalle);
            // return $data['checkid'];

            $check = json_decode($data['checkid'], true);
            // return $check;
            $transaccion = TransaccionPago::find($id_transaccion);
            $transaccion->check_id = $check['id'];
            $transaccion->save();

            return view('bd_pagos.index', $data);
        } catch (\Exception $e) {
            return ["msj" => $e->getMessage(), 'status' => 'error', 'linea' => $e->getLine()];
        }
    }

    public function confirmar_pago(Request $request)
    {
        try {
            //code...

            $data = $request->all();
            $detalle_orden = TransaccionPago::where('check_id', $data['id'])->first();
            DB::table('logData')->insert([
                'texto' => $data['id'] . '-------' . $data['resourcePath']
            ]);
            // return "aqui";
            $arr_datos = json_decode($detalle_orden->data_request, true);
            if ($arr_datos['datos_orden']['tipopago'] == 'ORD') {
                $datos['fecha_ini'] = $arr_datos['datos_orden']['dateini'];
                $datos['fecha_fin'] = $arr_datos['datos_orden']['dateend'];
                $datos['fecha'] = date('Y-m-d', strtotime($arr_datos['datos_orden']['dateini']));
                $datos['id_doctor'] = $arr_datos['datos_orden']['id_doctor'];
                $cant_agendas = BdIecedController::verificarAgenda($datos);
                if ($cant_agendas['status'] == 'error') {
                    return $cant_agendas;
                }
                // $verificar = BdIecedController::VerificarDisponibilidad($datos);
                // if ($verificar['status'] == 'error') {
                //     return $verificar;
                // }
            }

            $confirmar = BdPagoEnLinea::confirmarPago($data['resourcePath']);
            $confirmar = json_decode($confirmar);
            $checkid = $confirmar->ndc;
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
            $id_venta = "";
            $id_orden_venta = "";
            $id_mem = null;
            $id_agenda = null;
            $empresa = Empresa::where('prioridad', 1)->first();
            if ($tipo_pago == "ORD") {
                $stringPlain = 'siam_2020_' . random_int(1, 10000);
                $encodeurl   = base64_encode($stringPlain);
                $url         = 'https://videocall.mdc-server.com/?id=' . $encodeurl;
                if ($detalle->datos_orden->mod == "PRESENCIAL") {
                    $url = null;
                }
                $id_agenda = $this->agendPatient($detalle->datos_orden);
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
            } elseif ($tipo_pago == "MEM") {
                $membresia = Membresia::where('tipo_app', 1)->where('empresa_id', $empresa->id)->first();
                $id_mem = UserMembresia::insertGetId([
                    'user_id'           => $detalle->identificacion,
                    'membresia_id'      => $membresia->id,
                    'fecha_compra'      => date('Y-m-d H:i:s'),
                    'meses'             => $detalle->cart_items[0]->quantity * 12,
                    'valor_pagado'      => $detalle->total,
                    'meses_contratados' => $detalle->cart_items[0]->quantity * 12,
                    'referido'          => '',
                    'id_usuariocrea'    => $detalle->identificacion,
                    'id_usuariomod'     => $detalle->identificacion,
                    'ip_creacion'       => $detalle->ip_cliente,
                    'ip_modificacion'   => $detalle->ip_cliente,
                    'estado'            => 0,
                    'id_check_app'      => $transaccion->id_check_app
                ]);

                //LOG DEL SISTEMA DE QUIEN CREa LA ORDEN
                Log_usuario::create([
                    'id_usuario'  => $detalle->identificacion,
                    'ip_usuario'  => $detalle->ip_cliente,
                    'descripcion' => "GENERA MEMBRESIA APP",
                    'dato_ant1'   => $detalle->identificacion,
                    'dato1'       => $id_mem
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
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    //copia de ServiciosIecedController 
    public function agendPatient($request)
    {
        $id = $request->id_usuario;
        $teleconsulta = 0;
        if ($request->mod == 'ONLINE') {
            $teleconsulta = 1;
        }
        $paciente = Paciente::find($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = $request->id_doctor;
        $empresa = Empresa::where('prioridad', 1)->first();
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();
        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }
        $input_agenda = [
            'fechaini'        => $request->dateini,
            'fechafin'         => $request->dateend,
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '3',
            'id_empresa'      => $empresa->id,
            'espid'           => $espid,
            'tipo_cita'       => 1,
            'tc'              => $teleconsulta,
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_seguro'       =>  1, //change this
            'estado'          => '0', //First state 0 when i confirmed i will change
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => '0000000004',
            'id_usuariomod'   => '0000000004',
        ];
        $id_agenda = Agenda::insertGetId($input_agenda);
        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => $request->dateini,
            'fechafin'        => $request->dateend,
            'estado'          => '1',
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'FUE PAGADO DESDE LA APP',
            'id_usuariomod'   => '0000000004',
            'id_usuariocrea'  => '0000000004',
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Log_Agenda::create($input_log);
        return $id_agenda;
    }

    public function pag_confirmar_pago(Request $request)
    {
        $data = $request->all();
        $checkid = $data->checkid;
        $tipo_pago = $data->datos_orden->tipopago;
        $id_venta = "";
        $id_orden_venta = "";
        $id_mem = "";
        if ($tipo_pago == "ORD") {
            $stringPlain = 'siam_2020_' . random_int(1, 10000);
            $encodeurl   = base64_encode($stringPlain);
            $url         = 'https://videocall.mdc-server.com/?id=' . $encodeurl;
            if ($data->datos_orden->mod == "PRESENCIAL") {
                $url = null;
            }
            $id_agenda = $this->agendPatient($data->datos_orden);
            $id_apps = Apps_Agenda::insertGetId([
                'fecha'          => $data->datos_orden->dateini,
                'estado'         => 1,
                'url'            => $url,
                'id_agenda'      => $id_agenda,
                'total'          => $data->datos_orden->total,
                'id_doctor'      => $data->datos_orden->id_doctor,
                'id_usuariocrea' => $data->datos_orden->id_usuario,
                'tipo'           => $data->datos_orden->mod,
                'online'         => 0,
            ]);
        } else {
            $id_mem = UserMembresia::insertGetId([
                'user_id'           => $data->datos_orden->id_usuario,
                'membresia_id'      => $data->datos_orden->membresia,
                'fecha_compra'      => date('Y-m-d H:i:s'),
                'meses'             => '12',
                'valor_pagado'      => $data->datos_orden->total,
                'meses_contratados' => '12',
                'referido'          => $data->datos_orden->referido,
                'id_usuariocrea'    => $data->datos_orden->id_usuario,
                'id_usuariomod'     => $data->datos_orden->id_usuario,
                'ip_creacion'       => $data->ip_cliente,
                'ip_modificacion'   => $data->ip_cliente,
                'estado'            => 0,
            ]);
        }
        //LOG DEL SISTEMA DE QUIEN CRE LA ORDEN
        $paciente = Paciente::find($data->datos_orden->id_paciente);
        Log_usuario::create([
            'id_usuario'  => '1234517896',
            'ip_usuario'  => $data->ip_cliente,
            'descripcion' => "GENERA COTIZACION MOVIL",
            'dato_ant1'   => $data->datos_orden->id_paciente,
            'dato1'       => strtoupper($paciente['nombre1_paciente'] . " " . $paciente['nombre2_paciente'] . " " . $paciente['apellido1_paciente'] . " " . $paciente['apellido2_paciente']),
        ]);

        LogPagoEnLinea::create([
            'data_array' => json_encode($confirmar->resultDetails),
            'txt_respuesta' => json_encode($confirmar->result),
            'fecha' => date('Y-m-d H:i:s'),
            'id_transaccion' => $transaccion->id,
            'id_agenda' => $id_agenda,
            'id_orden_venta' => $id_orden_venta,
        ]);
        //return $confirmar;

        return ['status' => 'success', 'msj' => $confirmar->result];
    }

    public function registro_web(Request $request)
    {
        $token = $request->token;
        // if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
        //     return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        // }
        // if ($token != '361bd155b6b2b3bd5ff21526678ce7b0') {
        //     return ['status' => 'error', 'msj' => 'Credenciales invalidas'];
        // }
        try {
            $detalle = $request->all();
            $empresa = Empresa::where('prioridad', 1)->first();
            $id_transaccion = TransaccionPago::insertGetId([
                'data_request' => json_encode($detalle),
                'id_empresa' => $empresa->id,
                'check_id' => 'PAG WEB',
                'estado' => 1
            ]);
            return ['status' => 'success', 'msj' => 'Guardado', 'id_transaccion' => $id_transaccion];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }
    // datafast Humanlabs
    public function pagoOrdenLabs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $detalle = $request->all();
            $empresa = Empresa::where('prioridad', 1)->first();

            $validar = BdPagosController::get_disponibilidad($detalle);
            if ($validar['status'] == "error") {
                return $validar;
            }

            $detalle['ip_cliente'] = rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256);
            $detalle['cart_items'] = array();
            foreach ($detalle['detalles'] as $det) {
                $totalv = round($det['price'] - $det['discount'], 2);
                $aux['name'] = $det['nombre'];
                $aux['description'] = $det['nombre'];
                $aux['price'] = number_format($totalv, 2, '.', '');
                $aux['quantity'] = $det['qty'];
                array_push($detalle['cart_items'], $aux);
            }

            $det_tot = round($detalle['total1'] - $detalle['descuento_valor'], 2);
            $detalle['identificacion'] = $detalle['id_paciente'];
            $detalle['nombre1'] = $detalle['usuario']['nombre1'];
            $detalle['nombre2'] = $detalle['usuario']['nombre2'];
            $detalle['apellido'] = $detalle['usuario']['apellido1'];
            $detalle['email'] = $detalle['usuario']['email'];
            $detalle['telefono'] = $detalle['usuario']['telefono1'];
            $detalle['total'] = number_format($det_tot, 2, '.', '');
            $detalle['direccion_cliente'] = $detalle['usuario']['direccion'];
            $detalle['direccion_envio'] = $detalle['usuario']['direccion'];
            $detalle['orden_factura'] = $detalle['datos_factura'];

            $id_transaccion = TransaccionPago::insertGetId([
                'data_request' => json_encode($detalle),
                'id_empresa' => $empresa->id,
                'id_check_app' => $detalle['requestId']
            ]);
            $detalle['id_transaccion'] = $id_transaccion;
            unset($detalle['detalles']);
            $data['checkid'] = BdPagoEnLinea::checkId($detalle);
            $check = json_decode($data['checkid'], true);
            $transaccion = TransaccionPago::find($id_transaccion);
            $transaccion->check_id = $check['id'];
            $transaccion->save();

            return view('bd_pagos.index_labs', $data);
        } catch (\Exception $e) {
            return ["msj" => $e->getMessage(), 'status' => 'error', 'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function confirmar_pago_orden(Request $request)
    {
        try {
            $data = $request->all();
            //return  $data;
            $confirmar = BdPagoEnLinea::confirmarPago($data['resourcePath']);
            $confirmar = json_decode($confirmar);
            $checkid = $confirmar->ndc;
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

            $envio_orden = BdPagosController::envio_orden($transaccion->data_request);
            if ($envio_orden['status'] == 'error') {
                return $envio_orden;
            }
            // return [$envio_orden['id_agenda'], $confirmar, json_encode($confirmar), json_encode($confirmar->result)];

            LogPagoEnLinea::create([
                'data_array' => json_encode($confirmar),
                'txt_respuesta' => json_encode($confirmar->result),
                'fecha' => date('Y-m-d H:i:s'),
                'id_transaccion' => $transaccion->id,
                'id_agenda' => $envio_orden['id_agenda'],
                'id_examen_orden' => $envio_orden['id_orden'],
            ]);
            return ['status' => 'success', 'msj' => $confirmar->result];
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public static function envio_orden($data)
    {
        try {
            $data =  json_decode($data, true);
            $token = $data['token'];
            // return $token;
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return ['status' => 'error', 'msj' => 'credenciales invalidas1'];
            }
            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $elemento = json_encode($data);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,
                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $url     = 'http://bd.aitrol.com/app/CrearOrden';
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $respuesta = json_decode($response, true);
            return $respuesta;
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public static function get_disponibilidad($data)
    {
        try {
            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $elemento = json_encode($data);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,

                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $url     = 'http://bd.aitrol.com/app/verificar/agenda';
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $respuesta = json_decode($response, true);
            return $respuesta;
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function pagoTomaMuestra(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $detalle = $request->all();
            $val_pago = BdPagosController::validar_pagoTm($detalle);
            if ($val_pago['status'] == 'error') {
                return $val_pago;
            }
            $empresa = Empresa::where('prioridad', 1)->first();
            $usuario = User::find($detalle['id_paciente']);
            $detalle['ip_cliente'] = rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256) . '.' . rand(1, 256);
            $detalle['cart_items'] = array();
            foreach ($detalle['examenes'] as $det) {
                $totalv = round($det['precio_unitario'] - $det['descuento'], 2);
                $aux['name'] = $det['examen'];
                $aux['description'] = $det['examen'];
                $aux['price'] = number_format($totalv, 2, '.', '');
                $aux['quantity'] = $det['cantidad'];
                array_push($detalle['cart_items'], $aux);
            }

            $total_final = round($detalle['total_final'], 2);
            $detalle['identificacion'] = $detalle['id_paciente'];
            $detalle['nombre1'] = $usuario->nombre1;
            $detalle['nombre2'] = $usuario->nombre2;
            $detalle['apellido'] = $usuario->apellido1;
            $detalle['email'] = $usuario->email;
            $detalle['telefono'] = $usuario->telefono1;
            $detalle['total'] = number_format($total_final, 2, '.', '');
            $detalle['direccion_cliente'] = $usuario->direccion;
            $detalle['direccion_envio'] = $usuario->direccion;
            $detalle['subtotal_0'] = number_format($total_final, 2, '.', '');
            $detalle['orden_factura'] = [
                "id" => $usuario->id,
                "nombre1" => $usuario->nombre1,
                "nombre2" => $usuario->nombre2,
                "apellido1" => $usuario->apellido1,
                "apellido2" => $usuario->apellido2,
                "ciudad" => $usuario->ciudad,
                "id_usuario" => $usuario->id,
                "telefono_factura" => $usuario->telefono1,
                "email_factura" => $usuario->email,
                "direccion" => $usuario->direccion
            ];


            $id_transaccion = TransaccionPago::insertGetId([
                'data_request' => json_encode($detalle),
                'id_empresa' => $empresa->id,
                'id_check_app' => $detalle['requestId']
            ]);
            $detalle['id_transaccion'] = $id_transaccion;
            unset($detalle['examenes']);
            $data['checkid'] = BdPagoEnLinea::checkId($detalle);
            $check = json_decode($data['checkid'], true);
            $transaccion = TransaccionPago::find($id_transaccion);
            $transaccion->check_id = $check['id'];
            $transaccion->save();

            return view('bd_pagos.index_tm', $data);
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function confirmar_pagoTM(Request $request)
    {
        try {
            $data = $request->all();
            $confirmar = BdPagoEnLinea::confirmarPago($data['resourcePath']);
            $confirmar = json_decode($confirmar);
            $checkid = $confirmar->ndc;
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

            $envio_orden = BdPagosController::update_orden($transaccion->data_request);
            // return $envio_orden;
            if ($envio_orden['status'] == 'error') {
                return $envio_orden;
            }

            LogPagoEnLinea::create([
                'data_array' => json_encode($confirmar),
                'txt_respuesta' => json_encode($confirmar->result),
                'fecha' => date('Y-m-d H:i:s'),
                'id_transaccion' => $transaccion->id,
                'id_agenda' => $envio_orden['id_agenda'],
                'id_examen_orden' => $envio_orden['id_orden'],
            ]);
            return ['status' => 'success', 'msj' => $confirmar->result];
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public static function validar_pagoTm($detalle)
    {
        $logPago = LogPagoEnLinea::where('id_examen_orden', $detalle['id_orden'])->first();
        if (!is_null($logPago)) {
            $transaccion = TransaccionPago::where('id', $logPago->id_transaccion)->first();
            if ($transaccion->estado == 1) {
                return ['status' => 'error', 'msj' => 'Esta orden ya fue pagada'];
            }
        }
    }

    public static function update_orden($data)
    {
        try {
            $data =  json_decode($data, true);
            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $elemento = json_encode($data);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,
                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $url     = 'http://bd.aitrol.com/app/updateOrden';
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $respuesta = json_decode($response, true);
            return $respuesta;
        } catch (\Exception $e) {
            return ['status' => 'error', "msj" => $e->getMessage(),  'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }
}
