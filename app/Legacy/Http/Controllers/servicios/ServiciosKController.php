<?php

namespace Sis_medico\Http\Controllers\servicios;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use Sis_medico\Contable;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Membresia;
use Sis_medico\MembresiaDetalle;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\UserMembresia;

class ServiciosController extends Controller
{
    public function login(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result'     => '2',
                    'status'     => '',
                    'idPaciente' => '',
                    'idUsuario'  => '',
                ]);
            }
            $email    = $request['email'];
            $password = $request['pass'];
            $usuario  = User::where('email', $email)->first();
            if (!is_null($usuario) && $usuario != '[]') {
                $name2 = "";
                if ($usuario->nombre2 != '(N/A)') {
                    $name2 = $usuario->nombre2;
                }
                $surname2 = "";
                if ($usuario->apellido2 != '(N/A)') {
                    $surname2 = $usuario->apellido2;
                }

                if (!is_null($password)) {
                    if (!Hash::check($password, $usuario->password)) {
                        return response()->json([
                            'result'     => '3', //contraseña incorrecta
                            'status'     => '',
                            'idPaciente' => '',
                            'idUsuario'  => '',
                        ]);
                    }

                    $paciente      = Paciente::where('id', $usuario->id)->first();
                    $userMembresia = UserMembresia::where('user_id', $usuario->id)->where('estado', '1')->first();
                    //dd($userMembresia);
                    if (!is_null($paciente)) {
                        if (!is_null($userMembresia)) {
                            //dd($userMembresia);
                            $userdetails = MembresiaDetalle::where('membresia_id', $userMembresia->membresia->id)->get();
                            //dd($userMembresia->id);
                            return response()->json([
                                'idPaciente'      => $paciente->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $name2,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'date'            => $usuario->fecha_nacimiento,
                                'telefono'        => $usuario->telefono1,
                                'user'            => $usuario,
                                'email'           => $usuario->email,
                                'membresia'       => $userMembresia->membresia,
                                'membresiaDetail' => $userdetails,
                                'checkIn'         => '1',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        } else {
                            return response()->json([
                                'idPaciente'      => $paciente->id,
                                'idUsuario'       => $usuario->id,
                                'email'           => $usuario->email,
                                'user'            => $usuario,
                                'name'            => $usuario->nombre1 . ' ' . $name2,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'membresia'       => '',
                                'membresiaDetail' => '',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        }
                    } else {
                        Paciente::create([
                            'id'               => $usuario->id,
                            'nombre1'          => strtoupper($usuario->nombre1),
                            'nombre2'          => strtoupper($usuario->nombre2),
                            'apellido1'        => strtoupper($usuario->apellido1),
                            'apellido2'        => strtoupper($usuario->apellido2),
                            'tipo_documento'   => '1',
                            'parentesco'       => 'Principal',
                            'id_usuario'       => $usuario->id,
                            'id_pais'          => '1',
                            'direccion'        => $usuario->direccion,
                            'id_seguro'        => '1',
                            'imagen_url'       => '',
                            'fecha_nacimiento' => $usuario->fecha_nacimiento,
                            'telefono1'        => $usuario->telefono1,
                            'telefono2'        => $usuario->telefono2,
                        ]);
                        if (!is_null($userMembresia)) {
                            $userdetails = MembresiaDetalle::where('membresia_id', $userMembresia->membresia->id)->get();
                            //dd($userMembresia->id);
                            return response()->json([
                                'idPaciente'      => $usuario->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $name2,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'email'           => $usuario->email,
                                'user'            => $usuario,
                                'membresia'       => $userMembresia->membresia,
                                'membresiaDetail' => $userdetails,
                                'checkIn'         => '1',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        } else {
                            return response()->json([
                                'idPaciente'      => $usuario->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $name2,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'email'           => $usuario->email,
                                'membresia'       => '',
                                'email'           => '',
                                'user'            => $usuario,
                                'membresiaDetail' => '',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        }
                        return response()->json([
                            'result'     => '5',
                            'status'     => '',
                            'idPaciente' => '',
                            'idUsuario'  => '',
                            'dataLog'    => 'No existe como paciente',
                        ]);
                    }
                } else {
                    return response()->json([
                        'result'     => '2',
                        'status'     => '0',
                        'checkIn'    => '0',
                        'idPaciente' => '',
                        'idUsuario'  => '',
                    ]);
                }
            } else {
                return response()->json([
                    'result'     => '4',
                    'status'     => '0',
                    'idPaciente' => '',
                    'idUsuario'  => '',
                ]);
            }
        } else {
            $post = [
                'token' => '8c0a00ec19933215dc29225e645ea714',
                'email' => $request['email'],
                'pass'  => $request['pass'],
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/login');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
            // close the connection, release resources used

        }
    }
    public function getuserinfo(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }

        if ($global == '1') {
            $idusuario = $request['idUsuario'];
            if (!is_null($idusuario)) {
                $token = $request['token'];
                if ($token != '8c0a00ec19933215dc29225e645ea714') {
                    return response()->json([
                        'result'    => '2',
                        'names'     => '',
                        'surnames'  => '',
                        'email'     => '',
                        'telefono'  => '',
                        'direccion' => '',
                        'status'    => '',
                        'response'  => 'token incorrecto',
                    ]);
                }
                $user = User::where('id', $idusuario)->first();

                if (!is_null($user) && $user != '[]') {
                    $userMembresia = UserMembresia::where('user_id', $user->id)->where('estado', '1')->first();
                    if (!is_null($userMembresia)) {
                        $userdetails = MembresiaDetalle::where('membresia_id', $userMembresia->membresia->id)->get();
                        return response()->json([
                            'result'          => '1',
                            'names'           => $user->nombre1,
                            'surnames'        => $user->apellido1,
                            'email'           => $user->email,
                            'telefono'        => $user->telefono,
                            'membresia'       => $userMembresia->membresia,
                            'membresiaDetail' => $userdetails,
                            'direccion'       => $user->direccion,
                            'status'          => $user->estado,
                        ]);
                    } else {
                        return response()->json([
                            'result'          => '1',
                            'names'           => $user->nombre1,
                            'surnames'        => $user->apellido1,
                            'email'           => $user->email,
                            'telefono'        => $user->telefono,
                            'direccion'       => $user->direccion,
                            'status'          => $user->estado,
                            'membresia'       => '',
                            'membresiaDetail' => '',
                        ]);
                    }
                } else {
                    return response()->json([
                        'result'    => '3',
                        'names'     => '',
                        'surnames'  => '',
                        'email'     => '',
                        'telefono'  => '',
                        'direccion' => '',
                        'status'    => '',
                    ]);
                }
            } else {
                return response()->json([
                    'result'    => '4',
                    'names'     => '',
                    'surnames'  => '',
                    'email'     => '',
                    'telefono'  => '',
                    'direccion' => '',
                    'status'    => '',
                ]);
            }
        } else {
            $post = [
                'token'     => '8c0a00ec19933215dc29225e645ea714',
                'idUsuario' => $request['idUsuario'],
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/getuserinfo');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
        }
    }
    public function getBanners(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result' => '2',
                ]);
            }
            $array_banner_top = array();
            array_push($array_banner_top,'https://mdconsgroup.ec/labsapp/banner1.jpg');
            array_push($array_banner_top,'https://mdconsgroup.ec/labsapp/banner2.jpg');
            array_push($array_banner_top,'https://mdconsgroup.ec/labsapp/banner4.jpg');
            $array_banner_bottom = array();
            array_push($array_banner_bottom,'https://mdconsgroup.ec/labsapp/banner5.jpg');
            array_push($array_banner_bottom,'https://mdconsgroup.ec/labsapp/banner3.jpg');
            array_push($array_banner_bottom,'https://mdconsgroup.ec/labsapp/banner7.jpg');
            return response()->json([
                'result'        => '1',
                'banner_top'    => $array_banner_top,
                'banner_bottom' => $array_banner_bottom,
            ]);
        } else {
            $post = [
                'token' => '8c0a00ec19933215dc29225e645ea714',
            ];
            $ch = curl_init('http://siaam.ec/sis_medico/public/api/getBanners');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
        }
    }

    /*
    ATENCION: POR AHORA EL MISMO USUARIO SERA EL PACIENTE EN LA APP, MAS ADELANTE VALIDAR QUE ESTE EN UN GRUPO FAMILIAR
     */
    public function createOrderGetPayment(Request $request)
    {

        $global = $request['city'];
        if (is_null($global)) {
            $global = '1'; //Guayaquil
        }

        if ($global == '1') {
            //Guayaquil

            /*
            VARIABLES
             */
            //GETS JSON BODY
            $json_request = json_decode($request->getContent(), true);

            //VARIABLES P&F
            $_pasarela_pagos_detalle  = "";
            $_pasarela_pagos_subtotal = 0;
            $_pasarela_pagos_iva      = 0;
            $_pasarela_pagos_total    = 0;

            //VARIABLES REQUEST
            $tipo_pago            = $json_request['tipopago']; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
            $membresia_id         = $json_request['membresia_id']; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
            $membresia_detalle_id = $json_request['membresia_detalle_id'];
            $ip_cliente           = $_SERVER["REMOTE_ADDR"];

            //USUARIO
            $cedula_usuario = $json_request['id_usuario'];
            $email_usuario  = $json_request['email_usuario'];
            if ($membresia_id == 0) {
                $membresia_id = null;
            }

            //PACIENTE
            $cedula_paciente  = $json_request['id_paciente'];
            $celular_paciente = $json_request['celular_paciente'];

            //FACTURA
            $telefono_factura = $json_request['telefono_factura'];
            $email_factura    = $json_request['email_factura'];

            //VARIABLES
            $RUC_LABS          = '0993075000001';
            $ref_id            = "";
            $id_seguro         = '1';
            $id_protocolo      = null;
            $id_nivel          = null;
            $total             = 0;
            $contador          = 0;
            $usuario_mail      = null;
            $paciente          = null;
            $user              = null;
            $id_orden          = 0;
            $descuento_detalle = 0;

            // VALIDACIONES DE USUARIO Y PACIENTE
            $paciente = Paciente::find($cedula_paciente);

            //CREA ORDEN_EXAMEN ú ORDEN_MEMBRESIA
            if ($tipo_pago == "ORD") {
                //Es un pago de examenes
                $input_ex = [
                    //'id_paciente'      => $paciente->id,
                    'id_paciente'          => $paciente['id'],
                    'anio'                 => 0,
                    'mes'                  => 0,
                    'id_protocolo'         => $id_protocolo,
                    'id_seguro'            => $id_seguro,
                    'id_nivel'             => $id_nivel,
                    'est_amb_hos'          => '0',
                    'id_doctor_ieced'      => '1234517896',
                    'doctor_txt'           => 'PAGADO EN APP MOVIL',
                    'observacion'          => '',
                    'id_empresa'           => '0992704152001',
                    'cantidad'             => '0',
                    'estado'               => '0',
                    'realizado'            => '0',
                    'valor'                => '0',
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => '1234517896',
                    'id_usuariomod'        => '1234517896',
                    'motivo_descuento'     => '',
                    //'fecha_orden'      => date('Y-m-d h:i:s'),
                    //'codigo'           => '0001',
                    'membresia_id'         => $membresia_id,
                    'membresia_detalle_id' => $membresia_detalle_id,
                ];
                $id_orden = Examen_Orden::insertGetId($input_ex);
                //ARMA EL REF_ID
                $ref_id = "ORD" . $id_orden; //pivot para p&f
                //ITERATES ORDER DETAILS
                foreach ($json_request['detalles'] as $pexamen) {
                    //CREA DETALLE
                    $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_orden)->first();
                    if (is_null($detalle)) {
                        $contador++;
                        //$examen = Examen::find($pexamen->id_examen);
                        $examen = Examen::find($pexamen['id']);
                        //return $examen;
                        //$valor    = $examen->valor;
                        $valor    = $pexamen['total']; //AQUI PONERMOS EL VALOR YA CON DESCUENTO DE MEMBRESIA Y TODO DE LA APP
                        $cubre    = 'NO';
                        $ex_nivel = Examen_Nivel::where('id_examen', $pexamen['id'])->where('nivel', $id_nivel)->first();
                        if (!is_null($ex_nivel)) {
                            if ($ex_nivel['valor1'] != 0) {
                                $valor = $ex_nivel['valor1'];
                                $cubre = 'SI';
                            }
                        }
                        $input_det = [
                            'id_examen_orden' => $id_orden,
                            //'id_examen'       => $examen->id,
                            'id_examen'       => $pexamen['id'],
                            'valor'           => $valor,
                            'cubre'           => $cubre,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                        ];
                        Examen_detalle::create($input_det);
                        /// orden
                        $total += $valor;
                    }
                }
                if ($request['pres_dom'] == '1') {
                    //[1] Domicilio, [0] Presencial
                    $examen = Examen::find('1203');
                    //dd($value,$examen);
                    if (!is_null($examen)) {
                        $input_det = [
                            'id_examen_orden' => $id_orden,
                            'id_examen'       => $examen['id'],
                            'valor'           => $examen['valor'],
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                            'human_labs'      => 0,
                            'p_descuento'     => 0,
                            'valor_descuento' => 0,
                        ];
                        Examen_detalle::create($input_det);
                        $total = $total + $examen['valor'];
                        //$contador++;
                    }
                }
            } else if ($tipo_pago == "MEM") {
                //Es un pago de membresia
                foreach ($json_request['detalles'] as $pexamen) {
                    $valor = $pexamen['total'];
                    $total += $valor;
                }

                $membresia = Membresia::find($membresia_id);

                if ($membresia != null) {
                    $data_array = array(
                        "tipo"                    => "MEM-VEN", //tipo de de orden de venta
                        "fecha"                   => date('Y-m-d h:i:s'), //fecha de orden
                        "id_empresa"              => $RUC_LABS, //empresa
                        "divisas"                 => "1", //cualquiera
                        "nombre_cliente"          => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], //nombre del cliente
                        "tipo_consulta"           => "1", //esto clavado
                        "identificacion_cliente"  => $cedula_paciente, // ci de cliente
                        "direccion_cliente"       => $paciente['direccion_paciente'], //direccion del cliente
                        "telefono_cliente"        => $paciente['celular_paciente'], // telefono de cliente
                        "mail_cliente"            => $email_usuario, //mail del cliente
                        "orden_venta"             => "0", //idde referencia
                        "identificacion_paciente" => $cedula_paciente, // ci de paciente
                        "nombre_paciente"         => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], // nonbre de paciente
                        "id_seguro"               => "1", //de la tabla seguros,
                        "subtotal_01"             => $total,
                        "subtotal_121"            => $total,
                        "descuento1"              => "0.00",
                        "tarifa_iva1"             => "0.00",
                        "base"                    => $total,
                        "totalc"                  => $total,
                        "valor_contable"          => $total,
                        "details"                 => array(
                            array(
                                "codigo"     => 1,
                                "nombre"     => $membresia['nombre'], ///<<<<<<<---------
                                "cantidad"   => "1",
                                "precio"     => $total,
                                "extendido"  => "0.00",
                                "iva"        => "1",
                                "descpor"    => "0",
                                "descuento"  => "0",
                                "copago"     => "0",
                                "detalle"    => $membresia['nombre'],
                                "precioneto" => $total,

                            ),
                        ),
                        "id_usuario"              => "1316262193",
                    );
                    $response = Contable::build_data($data_array);
                    $id_orden = $response['ven_orden'];
                    //ARMA EL REF_ID
                    $ref_id = "MEM" . $id_orden; //pivot para p&f

                }
            }

            //ACTUALIZA TOTALES
            $total             = round($total, 2);
            $recargo_p         = 0;
            $descuento_detalle = round($descuento_detalle, 2);
            $subtotal_pagar    = $total - $descuento_detalle;
            $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor     = round($recargo_valor, 2);
            $valor_total       = $subtotal_pagar + $recargo_valor;
            $valor_total       = round($valor_total, 2);

            if ($tipo_pago == "ORD") {
                //ES UNA ORDEN DE PAGO
                //ACTUALIZA ORDEN DE EXAMEN
                $orden     = Examen_Orden::find($id_orden);
                $input_ex2 = [
                    'recargo_valor'   => $recargo_valor,
                    'total_valor'     => $valor_total,
                    'cantidad'        => $contador,
                    'valor'           => $total,
                    'descuento_valor' => $descuento_detalle,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'pago_online'     => '1',
                    'estado_pago'     => '0',
                    'pres_dom'        => $request['pres_dom'],
                ];
                $orden->update($input_ex2);
            } else {
                //ES CONTABLE
                //*************** ESTO VA EN POSTPROCESO ***************
                //CHILAN DEBE DARME
                /*$data_array = array(
            "id_orden"    => $id_orden,//tipo de de orden de venta
            "id_usuario"  => $cedula_usuario, //fecha de orden
            );
            $response= Contable::update_data($data_array);
             */
            }

            //LOG DEL SISTEMA DE QUIEN CRE LA ORDEN
            Log_usuario::create([
                'id_usuario'  => '1234517896',
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GENERA COTIZACION MOVIL",
                'dato_ant1'   => $cedula_paciente,
                'dato1'       => strtoupper($paciente['nombre1_paciente'] . " " . $paciente['nombre2_paciente'] . " " . $paciente['apellido1_paciente'] . " " . $paciente['apellido2_paciente']),
            ]);

            /*
            INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
             */
            $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
            $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
            $_pasarela_pagos_subtotal = round($valor_total, 2, PHP_ROUND_HALF_UP);
            $_pasarela_pagos_iva      = 0;
            $_pasarela_pagos_total    = round($valor_total, 2, PHP_ROUND_HALF_UP);
            $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
            $pyf_details              = array();

            if ($tipo_pago == "ORD") {
                array_push($pyf_details, array(
                    "sku"      => '' . $id_orden . '',
                    "name"     => 'LABS APP Orden de laboratorio #' . $id_orden,
                    "qty"      => 1,
                    "price"    => $_pasarela_pagos_subtotal,
                    "tax"      => 0.00,
                    "discount" => 0.00, //falta revisar
                    "total"    => $_pasarela_pagos_subtotal,
                ));
            } else if ($tipo_pago == "MEM") {
                array_push($pyf_details, array(
                    "sku"      => '' . $id_orden . '',
                    "name"     => 'Membresía a LABS. #' . $id_orden,
                    "qty"      => 1,
                    "price"    => $_pasarela_pagos_subtotal,
                    "tax"      => 0.00,
                    "discount" => 0.00, //falta revisar
                    "total"    => $_pasarela_pagos_subtotal,
                ));
            }

            if (strlen($telefono_factura) < 10) {
                $telefono_factura = '0900000001';
            }

            //CLEANING NAMES AND SURENAMES
            $nombres_factura            = '';
            $apellidos_factura          = '';
            $nombres_apellidos_facturas = explode(" ", $json_request['nombre_factura']);
            $nombres_factura            = $nombres_apellidos_facturas[0];
            for ($i = 1; $i < count($nombres_apellidos_facturas); $i++) {
                $apellidos_factura .= $nombres_apellidos_facturas[$i];
            }
            if (count($nombres_apellidos_facturas) == 4) {
                $nombres_factura   = $nombres_apellidos_facturas[0] . ' ' . $nombres_apellidos_facturas[1];
                $apellidos_factura = $nombres_apellidos_facturas[2] . ' ' . $nombres_apellidos_facturas[3];
            }

            // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
            //json de invocación
            $data_array = array(
                "company"        => $RUC_LABS,
                "person"         => array(
                    "document"     => $request['cedula_factura'],
                    "documentType" => $this->getDocumentType($request['cedula_factura']),
                    //"name"         => $this->cleanNames(strtoupper($request['nombre1']) . ' ' . strtoupper($request['nombre2'])),
                    //"surname"      => $this->cleanNames(strtoupper($request['apellido1']) . ' ' . strtoupper($request['apellido2'])),
                    "name"         => $this->cleanNames(strtoupper($nombres_factura)),
                    "surname"      => $this->cleanNames(strtoupper($apellidos_factura)),
                    "email"        => $email_factura,
                    "mobile"       => $telefono_factura,
                ),
                "paymentRequest" => array(
                    "orderId"     => '' . $ref_id . '',
                    "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                    "items"       => array(
                        "item" => $pyf_details, //pending
                    ),
                    "amount"      => array(

                        "taxes"    => array(
                            array(
                                "kind"   => "Iva",
                                "amount" => 0.00,
                                "base"   => $_pasarela_pagos_subtotal,
                            ),
                        ),

                        "currency" => "USD",
                        "total"    => $_pasarela_pagos_total,
                    ),
                ),
                //"returnUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=" . $ref_id, //URL DE RETORNO
                //"cancelUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=" . $ref_id, //URL DE CANCELACION
                "returnUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/returnUrl?orderid=" . $ref_id, //URL DE RETORNO, orderid not used in link for now
                "cancelUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/cancelUrl?orderid=" . $ref_id, //URL DE CANCELACION, orderid not used in link for now
                "userAgent"      => "labs_ec/1",
            );
            $requestId = '';
            $manage    = json_encode($data_array);
            $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
            $response  = json_decode($make_call, true);
            if ($response['status'] != null) {
                $requestId = $response['requestId'];
                if ($response['status']['status'] == 'success') {
                    $pyf_checkout_url = $response['processUrl'];
                } else {
                    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
                }
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
            }
            /*
            RESPUESTA P&F
            {
            "status": {
            "status": "success",
            "message": "payment request successfully created",
            "reason": "",
            "date": "2021-09-09T14:51:17-05:00"
            },
            "requestId": "7501",
            "processUrl": "https://vpos.accroachcode.com/sandbox/40GesdOhDl6Dg23mavMyHM"
            }
             */

            //RETURNS JSON RESPONSE
            //return ['estado' => 'ok', 'usuario' => $xusuario->id, 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
            return response()->json([
                'estado'          => 'ok',
                'usuario'         => $request['id_usuario'],
                'ref_id'          => $ref_id,
                'total'           => $total,
                'url_vpos'        => $pyf_checkout_url,
                'request_id'      => $requestId,
                'paciente_id'     => $paciente['id'],
                'paciente_id_req' => $cedula_paciente,
            ]);
        } else {
            $post = [
                'token' => '8c0a00ec19933215dc29225e645ea714',
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/createOrderGetPayment');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            //$s= json_decode($response);
            //dd($s->result);
            curl_close($ch);
            return response()->json(json_decode($response));
        }
    }
    public function getPaymentInformation(Request $request)
    {
        //GETS JSON BODY
        $json_request = json_decode($request->getContent(), true);
        $requestId    = $json_request['requestId'];
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);

        return $response; //en el status verificar que status.status=="APPROVED"
    }
    public function registerUser(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result'    => '2',
                    'idUsuario' => '',
                ]);
            }
            $nombre           = $request['names'];
            $idusuario        = $request['id'];
            $apellido         = $request['surnames'];
            $email            = $request['email'];
            $telefono         = $request['telefono'];
            $seguros          = $request['seguros'];
            $fecha_nacimiento = $request['date'];
            $direccion        = $request['direccion'];
            if (!is_null($nombre) && !is_null($apellido) && !is_null($email) && !is_null($request['pass']) && !is_null($idusuario) && !is_null($fecha_nacimiento)) {
                $verificar  = User::where('email', $email)->first();
                $verificar2 = User::find($idusuario);
                if (is_null($seguros)) {
                    $seguros = "1";
                }
                if (!is_null($verificar) && $verificar != '[]' || !is_null($verificar2) && $verificar2 != '[]') {
                    return response()->json([
                        'result'    => '3', //email ya se encuentra registrado
                        'idUsuario' => '',
                        'dataLog'   => 'Ya existe usuario',
                    ]);
                } else {
                    list($nombre1, $nombre2)     = array_pad(explode(' ', $nombre), 30, null);
                    list($apellido1, $apellido2) = array_pad(explode(' ', $apellido), 30, null);

                    if (is_null($apellido2)) {
                        $apellido2 = "(N/A)";
                    }
                    if (is_null($nombre2)) {
                        $nombre2 = "(N/A)";
                    }
                    //'imagen_url'       => 'img000169423.png',
                    User::create([
                        'id'               => $idusuario,
                        'email'            => $email,
                        'nombre1'          => strtoupper($nombre1),
                        'id_tipo_usuario'  => '2',
                        'nombre2'          => strtoupper($nombre2),
                        'apellido1'        => strtoupper($apellido1),
                        'apellido2'        => strtoupper($apellido2),
                        'estado'           => '1',
                        'imagen_url'       => '',
                        'telefono1'        => $telefono,
                        'telefono2'        => $telefono,
                        'direccion'        => $direccion,
                        'tipo_documento'   => '1',
                        'id_pais'          => '1', //pendiente revisar
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'password'         => bcrypt($request['pass']),
                    ]);
                    Paciente::create([
                        'id'               => $idusuario,
                        'nombre1'          => strtoupper($nombre1),
                        'nombre2'          => strtoupper($nombre2),
                        'apellido1'        => strtoupper($apellido1),
                        'apellido2'        => strtoupper($apellido2),
                        'tipo_documento'   => '1',
                        'parentesco'       => 'Principal',
                        'id_usuario'       => $idusuario,
                        'id_pais'          => '1',
                        'direccion'        => $direccion,
                        'id_seguro'        => $seguros,
                        'imagen_url'       => '',
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'telefono1'        => $telefono,
                        'telefono2'        => $telefono,
                    ]);
                    $validate  = $this->findOtherBD($request);
                    $responses = "";
                    if ($validate->getData()->result == 1) {
                        $response = "ya existe en portoviejo";
                    } else {
                        if (is_null($request['seguros'])) {
                            $request['seguros'] = '1';
                        }
                        $response = "se crea en portoviejo";
                        $create   = $this->createOtherBD($request);
                        //dd($create);
                        if ($create->getData()->result == 1) {
                        } else {
                            $response = $create->getData();
                        }
                    }
                    return response()->json([
                        'result'     => '1',
                        'idUsuario'  => $idusuario,
                        'idPaciente' => $idusuario,
                        'response'   => $response,
                    ]);
                }
            } else {
                return response()->json([
                    'result'    => '4', //vacios parametros
                    'idUsuario' => '',
                    'dataLog'   => 'Vacios parametros',
                ]);
            }
        } else {
            $post = [
                'token'     => '8c0a00ec19933215dc29225e645ea714',
                'id'        => $request['id'],
                'names'     => $request['names'],
                'surnames'  => $request['surnames'],
                'email'     => $request['email'],
                'telefono'  => $request['telefono'],
                'seguros'   => $request['seguros'],
                'date'      => $request['date'],
                'direccion' => $request['direccion'],
                'pass'      => $request['pass'],
            ];
            $ch = curl_init('http://siaam.ec/sis_medico/public/api/registeruser');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
        }
    }
    public function createOtherBD(Request $request)
    {
        $post = [
            'token'     => '8c0a00ec19933215dc29225e645ea714',
            'id'        => $request['id'],
            'names'     => $request['names'],
            'surnames'  => $request['surnames'],
            'email'     => $request['email'],
            'telefono'  => $request['telefono'],
            'seguros'   => $request['seguros'],
            'date'      => $request['date'],
            'direccion' => $request['direccion'],
            'pass'      => $request['pass'],
        ];

        $ch = curl_init('http://siaam.ec/sis_medico/public/api/registeruser');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return response()->json(json_decode($response, true));
    }
    public function findOtherBD(Request $request)
    {
        $post = [
            'token'     => '8c0a00ec19933215dc29225e645ea714',
            'idUsuario' => $request['id'],
        ];

        $ch = curl_init('http://siaam.ec/sis_medico/public/api/getuserinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return response()->json(json_decode($response, true));
    }
    public function getSeguros(Request $request)
    {
        $token = $request['token'];
        if ($token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json([
                'result'  => '2',
                'seguros' => '',
            ]);
        }
        $seguros = Seguro::where('inactivo', '1')->select('id', 'nombre')->get();

        if (!is_null($seguros) && $seguros != '[]') {
            return response()->json([
                'result'  => '1',
                'seguros' => $seguros,
            ]);
        } else {
            return response()->json([
                'result'  => '3',
                'seguros' => '',
            ]);
        }
    }
    public function getExam(Request $request)
    {
        if ($request['city'] == '0') {
            $post = [
                'token' => '8c0a00ec19933215dc29225e645ea714',
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/getExam');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
        } else {
            $exam  = Examen::where('examen.estado', '1')->where('examen.valor', '>', 0)->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')->where('s.estado',1)->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'))->orderBy('examen.nombre')->get();
            $juank = array();
            foreach ($exam as $ex) {
                $p['id'] = $ex->id;
                if ($ex->inital == '(') {
                    $p['initial'] = "";
                } else {
                    $p['initial'] = $ex->initial;
                }
                $p['valor']  = $ex->valor;
                $p['nombre'] = $ex->nombre;
                array_push($juank, $p);
            }
            $result = array();
            $key    = "initial";
            foreach ($juank as $val) {
                if (array_key_exists($key, $val)) {
                    $result[$val[$key]][] = $val;
                } else {
                    $result[""][] = $val;
                }
            }
            $juank2 = array();
            foreach ($result as $key => $a) {
                $ps['section'] = $key;
                $kun           = array();
                foreach ($a as $z) {
                    $x['id']    = $z['id'];
                    $x['price'] = $z['valor'];
                    $x['title'] = $z['nombre'];
                    $x['added'] = 0;
                    array_push($kun, $x);
                }
                $ps['data'] = $kun;
                array_push($juank2, $ps);
            }
            //dd($result);
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result' => '2',
                ]);
            }
            return response()->json([
                'result' => '1',
                'exam'   => $juank2,
            ]);
        }
    }
    public function myExam(Request $request)
    {
        //dd("sdada");
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result' => '2',
                ]);
            }
            $id_paciente = $request['idUsuario'];
            //$id_paciente = Auth::user()->id;

            $paciente = Paciente::find($id_paciente);

            $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->join('users as usuario', 'usuario.id', 'p.id_usuario')
                ->where('usuario.id', $id_paciente)
                ->where('examen_orden.estado', '1')
                ->select('examen_orden.*');
            //->orderBy('examen_orden.fecha_orden', 'desc');

            $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
                ->where('gf.id_usuario', $id_paciente)
                ->where('gf.estado', '1')
                ->where('examen_orden.estado', '1')
                ->select('examen_orden.*');
            //->orderBy('examen_orden.fecha_orden', 'desc');
            $ordenes  = $ordenes1->union($ordenes2);
            $querySql = $ordenes->toSql();
            //dd($querySql);
            $ordenes = DB::table(DB::raw("($querySql order by fecha_orden desc) as a"))->mergeBindings($ordenes->getQuery());
            //dd($ordenes->get());
            $ordenes  = $ordenes->get();
            $myPusher = array();
            foreach ($ordenes as $value) {
                $route         = route('api.Loadhtml', ['id' => $value->id]);
                $pr            = array();
                $arrs['route'] = $route;
                array_push($pr, $arrs);
                //dd($pr);
                //$getPDF = $this->getPDF($value->id);
                $paciente = Paciente::find($value->id_paciente);
                $seguro   = Seguro::find($value->id_seguro);
                //$detalle = Examen_Detalle::where('id_examen_orden', $value->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
                //dd($detalle);
                $cabecera['name']    = $paciente->nombre1 . ' ' . $paciente->nombre2;
                $cabecera['surname'] = $paciente->apellido1 . ' ' . $paciente->apellido2;
                $cabecera['anio']    = $value->anio;
                $cabecera['month']   = $value->mes;
                $cabecera['day']     = date('d', strtotime($value->fecha_orden));
                $cabecera['date']    = $value->fecha_orden;
                $cabecera['seguro']  = $seguro->nombre;
                $cabecera['urlPDF']  = $route;
                $getParameters       = $this->puede_imprimir($value->id);
                $pct                 = 0;
                if ($getParameters['cantidad'] == 0) {
                    $pct = 0;
                } else {
                    $pct = $getParameters['certificados'] / $getParameters['cant_par'] * 100;
                }
                $cabecera['percentage'] = $pct;
                array_push($myPusher, $cabecera);
            }
            return response()->json([
                'result' => '1',
                'exam'   => $myPusher,
            ]);
        } else {
            $post = [
                'token'     => '8c0a00ec19933215dc29225e645ea714',
                'idUsuario' => $request['idUsuario'],
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/myExam');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response, true));
        }
    }
    public function pdf(Request $request)
    {
        return view('sinlogin.iframepdf', ['id' => $request['id']]);
    }
    public function returnHtml(Request $request)
    {
        $url = $request['url'];
        //dd($url);
        $html = "#";
        foreach ($url as $key => $x) {
            foreach ($x as $s) {
                $html = $s;
            }
        }

        return 'https://docs.google.com/viewerng/viewer?url=' . $html;
    }
    public function getPDF($id)
    {
        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados = $orden->resultados;
        $parametros = Examen_Parametro::orderBy('orden')->get();

        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }
        //dd($pct);
        //dd($detalle);
        // Fin recalcula Porcentaje

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            //$agrupador = Examen_Agrupador_labs::all();
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }
        $ucreador = $orden->crea;
        $age      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl = "laboratorio.orden.resultados_pdf";
        $view     = \View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        //return $view;
        return $pdf->stream('resultado-' . $id . '.pdf', array("Attachment" => false));
    }
    public function puede_imprimir($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = $orden->detalles;
        $resultados = $orden->resultados;
        //$parametros = Examen_parametro::orderBy('orden')->get();

        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }
        $parameters['cantidad']     = $cantidad;
        $parameters['certificados'] = $certificados;
        $parameters['cant_par']     = $cant_par;

        return $parameters;
    }
    public function loadData(Request $request)
    {
        $parameter   = $request['parameter'];
        $type        = $request['type'];
        $id          = $request['id'];
        $information = Contable::recovery_by_model($parameter, $type, $id);
        return $information;
    }

    /*
    PAGOS&FACTURAS METHODS
     */
    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        //dd($context, $url);
        return file_get_contents($url, false, $context);
    }

    public function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function getDocumentType($document)
    {
        if (strlen($document) == 10) {
            return '05'; //CEDULA
        } else if (strlen($document) == 13) {
            return '04'; //RUC
        } else {
            return '06'; //PASAPORTE
        }
    }

    public function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }
    public function returnUrl(Request $request)
    {
        echo "<html><head></head><body><script type='text/javascript'>window.ReactNativeWebView?.postMessage('irAtras')</script></body></html>";
    }
    public function cancelUrl(Request $request)
    {
        echo "<html><head></head><body><script type='text/javascript'>window.ReactNativeWebView?.postMessage('irAtras')</script></body></html>";
    }
    //////---- PENDIENTE DE USO, AUN USANDOSE LA ANTERIOR EN SINLOGINCONTROLLER.PHP -----
    public function postprocessUrl(Request $request)
    {
    }
    public function loadUserAgent(Request $request)
    {
        return view('sinlogin.reedireccionar');
    }
}
