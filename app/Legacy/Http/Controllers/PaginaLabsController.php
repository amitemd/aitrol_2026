<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Log_usuario;
use Sis_medico\Ct_Bancos;

use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\laboratorio\MembresiasLabsController;

class PaginaLabsController extends Controller
{
    public function examenes(Request $request)
    {
        $token = $request['token'];
        if ($token != 'dd96a4a6c3dd2119f27e3414a06a8ad8') {
            return response()->json([
                'error'   => 'TOKEN INCORRECTO',
                'success' => 'NO',
            ]);
        }
        $examenes = Examen::join('examen_agrupador_sabana', 'examen_agrupador_sabana.id_examen', '=', 'examen.id')
            ->where('examen.estado', 1)->where('examen_agrupador_sabana.estado', '1')->orderBy('nombre')->get();

        return response()->json($examenes);
    }
    public function comprobante_externo($comprobante)
    {
        $comprobante         = base64_decode(base64_decode($comprobante));
        $data['empresa']     = '0993075000001';
        $data['comprobante'] = $comprobante;
        $data['tipo']        = 'pdf';
        return ApiFacturacionController::estado_comprobante_externo($data);
    }
    public function cotizacion_externo($id)
    {
        $cotizador = base64_decode(base64_decode($id));
        $id = $cotizador;
        $orden = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $view = \View::make('laboratorio.orden.cotizacion_pdf', compact('orden', 'detalles', 'age', 'forma_pago'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('cotizador-' . $id . '.pdf');
    }

    public function resultados_externos($id)
    {
        $orden = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
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

    public function resultados_dr_externos($id)
    {
        $orden    = Examen_Orden::find($id);
        if (is_null($orden->codigo)) {
            return response()->view('errors.404');
        }
        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
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
    public function facturacion_externo(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $data = json_decode($elemento, true);
                $data['externo'] = 'recibido';
                $envio = ApiFacturacionController::envio_factura_externo($data);
                //dd($envio);
                return $envio;
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
    public function facturacion_externo_varios(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'Z7RB9RR8XKVNWMR194H') {
                $elemento = $request->getContent();
                $data = json_decode($elemento, true);
                $data['externo'] = 'recibido';
                $envio = ApiFacturacionController::envio_factura_externo_varios($data);
                return $envio;
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
    public function ride_externo(Request $request, $recurso)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $data = json_decode($elemento, true);
                $data['externo'] = 'recibido';
                //dd($request);
                //print_r($data);exit();
                $porciones = explode("_", $recurso);
                $data['empresa']     = $porciones[1];
                $data['comprobante'] = $porciones[2];
                $data['tipo']        = 'pdf';
                $data['externo']     = '1';
                //dd($envio);
                return ApiFacturacionController::estado_comprobante_externo($data);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
    /*public function labs_gestion_servicios(Request $request){
        $idusuario  = 'FACELECTRO';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->getContent(); 
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "LLEGA SOLICITUD",
                    'dato1'       => "-",
                    'dato_ant2'   => "-",
                    'dato2'       => "-",
                    'dato_ant4'   => $elemento,
                ]);
               // return $elemento;
                //var_dump($elemento);exit();
                $data_src = json_decode($elemento, true);//return $data;
                $id_orden    = $data_src['id_orden'];
                $id_empresa  = $data_src['empresa'];
                $sucursal    = $data_src['sucursal'];
                $caja        = $data_src['caja'];//dd($data_src);
                $tproductos  = $data_src['productos'];
                $data           = [];
                $cliente        = [];
                $direccion      = [];
                $producto       = [];
                $info_adicional = [];
                $pago           = [];
                $info           = [];
                $tipos_pago     = [];
                $productos      = [];
                //return [ $id_orden, $id_empresa, $sucursal, $caja ];
                $pagoonline = DB::table('pagosenlinea')->where('clave', $id_orden)->where('empresa_id',$id_empresa)->where('sucursal',$sucursal)->where('caja',$caja)->first(); //return json_encode($pagoonline);
                if (!is_null($pagoonline)) {
                    if ($pagoonline->nro_comprobante != null) {
                        
                        
                        $nonce      = $this->getNonce(12);
                        $empresa    = Empresa::find($id_empresa);//return json_encode($empresa);
                        //compruebo si la empresa esta habilitada como facturacion electronica
                        //extraigo datos de la empresas y creo el token
                        $appId     = $empresa->appid;
                        $appSecret = $empresa->appsecret;
                        $date      = date('c');
                        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
                        $url = "https://api.pagosyfacturas.com/api/billing/FACTURA_" . $id_empresa . "_" . $pagoonline->nro_comprobante;//dd($url);
                        //return json_encode($url);
                        //dd($url);
                        $options = array(
                            'http' => array(
                                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                                'method'  => 'POST',
                                'content' => 'prueba',
                            ),
                        );
                        //dd($options);
                        $context   = stream_context_create($options);
                        //dd($context, $url);
                        $response  = file_get_contents($url, false, $context);
                        $respuesta = json_decode($response, true);//return json_encode($respuesta);
                        if ($respuesta['status']['status'] == 'error') {
                            Log_usuario::create([
                                'id_usuario'  => $idusuario,
                                'ip_usuario'  => $ip_cliente,
                                'descripcion' => "GESTION DE PAGOS EN LINEA",
                                'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                                'dato1'       => "ORDEN:".$id_orden,
                                'dato_ant2'   => "ERROR:",
                                'dato2'       => "NO ENCONTRO DOCUMENTO EN PYF",
                                'dato_ant4'   => "-",
                            ]);
                            return response()->json([ 'estado' => 'error', 'mensaje' => "NO ENCONTRO DOCUMENTO EN PYF" ]);
                        }
                        $f_emision           = $respuesta['details']['fecha'];//dd($f_emision);
                        $f_emision           = date('Y-m-d H:i:s', strtotime($f_emision));
                        $data['empresa']     = $id_empresa;
                        $data['fecha']       = date('Y-m-d ', strtotime($f_emision)); //ESTA FECHA ES LA DE EMISION
                        $data['electronica'] = '1';
                        $cliente['cedula']   = $data_src['cedula'];
                        $cliente['tipo'] = $data_src['tipo']; //eduardo dice q el lo calcula y luego se
                        if (strlen($data_src['cedula']) == 13 && substr($data_src['cedula'], -3) == '001') {
                            $cliente['tipo'] = '4';
                        } elseif (strlen($data_src['cedula']) == 10) {
                            $cliente['tipo'] = '5';
                        }
                        $cliente['nombre']   = $data_src['nombre'];
                        $cliente['apellido'] = '';
                        $explode = explode(" ", $data_src['nombre']);
                        if (count($explode) >= 4) {
                            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
                            for ($i = 2; $i < count($explode); $i++) {
                                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
                            }
                        }
                        if (count($explode) == 3) {
                            $cliente['nombre']   = $explode[0];
                            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
                        }
                        if (count($explode) == 2) {
                            $cliente['nombre']   = $explode[0];
                            $cliente['apellido'] = $explode[1];
                        }
                        //dd($cliente);
                        $cliente['email']            = $data_src['email'];
                        $cliente['telefono']         = $data_src['telefono'];
                        $direccion['calle']          = $data_src['direccion'];
                        $direccion['ciudad']         = $data_src['ciudad'];
                        $cliente['direccion']        = $direccion;
                        $cliente['nro_autorizacion'] = null;
                        $data['cliente']             = $cliente;
                        $msn_error  = '';
                        $flag_error = false;
                        if ($cliente['cedula'] == null) {
                            $flag_error = true;
                            $msn_error  = 'Error en cedula';
                        }
                        if ($cliente['nombre'] == null) {
                            $flag_error = true;
                            $msn_error  = 'Error en Nombre';
                        }
                        if ($cliente['email'] == null) {
                            $flag_error = true;
                            $msn_error  = 'Error en email';
                        }
                        if ($cliente['telefono'] == null) {
                            $flag_error = true;
                            $msn_error  = 'Error en telefono';
                        }
                        if ($direccion['calle'] == null) {
                            $flag_error = true;
                            $msn_error  = 'Error en calle';
                        }
                        if ($direccion['ciudad'] == null) {
                            //$flag_error=true;
                            //$msn_error='Error en Ciudad';
                            $direccion['ciudad'] = 'GUAYAQUIL';
                        }
                        $cant = 0;//dd($tproductos);
                        foreach ($tproductos as $value) {
                            //dd($tproductos, $value);
                            //se envian los productos
                            $producto['sku']       = $value['sku']; //ID EXAMEN
                            $producto['nombre']    = $value['nombre']; // NOMBRE DEL EXAMEN
                            $producto['cantidad']  = $value['cantidad'];
                            $producto['precio']    = $value['precio']; //DETALLE
                            $producto['descuento'] = $value['descuento'];
                            $producto['subtotal']  = $value['subtotal']; //precio-descuento
                            $producto['tax']       = $value['tax'];
                            $producto['total']     = $value['total']; //SUBTOTAL
                            $producto['copago']    = $value['copago'];;
                            $productos[$cant]      = $producto;
                            $cant++;
                        }//dd($productos);
                        $data['productos'] = $productos;
                       
                        $info_adicional['nombre'] = "AGENTES_RETENCION";
                        $info_adicional['valor']  = "Resolucion 1";
                        $info[0]                  = $info_adicional;
                        $info_adicional['nombre'] = "PACIENTE";
                        $info_adicional['valor']  = $data_src['id_paciente'].' '.$data_src['nombre_pac'];
                        $info[1]                  = $info_adicional;
                        $info_adicional['nombre'] = "MAIL";
                        $info_adicional['valor']  = $data_src['email']; //EMAIL
                        $info[2]                  = $info_adicional;
                        $info_adicional['nombre'] = "CIUDAD";
                        $info_adicional['valor']  = $data_src['ciudad']; //EMAIL
                        $info[3]                  = $info_adicional;
                        $info_adicional['nombre'] = "DIRECCION";
                        $info_adicional['valor']  = $data_src['direccion']; //EMAIL
                        $info[4]                  = $info_adicional;
                        $info_adicional['nombre'] = "ORDEN";
                        $info_adicional['valor']  = '' . $data_src['id_orden'] . ''; //EMAIL
                        $info[5]                  = $info_adicional;
                        $info_adicional['nombre'] = "SEGURO";
                        $info_adicional['valor']  = $data_src['seguro']; //SEGURO
                        $info[6]                  = $info_adicional;
                        $info_adicional['nombre'] = "FORMA_PAGO";
                        $info_adicional['valor']  = '';
                        $info[7]                  = $info_adicional;
                        $pago['forma_pago']            = '01';
                        $pago['informacion_adicional'] = $info;
                        $pago['dias_plazo']            = '10';
                        $data['pago']                  = $pago;
                        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                        $data['laboratorio']           = 1;
                        $data['paciente']              = $data_src['id_paciente'];
                        $data['concepto']              = 'Ingreso de Factura Electronica por Pago en Linea';
                        $data['copago']                = 0;
                        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                        $data['total_factura']         = $data_src['total_valor'];
                        $tipos_pago['id_tipo']            = 5; //metodo de pago efectivo, tarjeta, etc
                        $tipos_pago['fecha']              = $data_src['fecha_orden'];
                        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = $pagoonline->pago_auth; //si es efectivo no se envia
                        $banco                            = Ct_Bancos::where('nombre', $pagoonline->issuer)->first();
                        $id_banco                         = '1';
                        if (!is_null($banco)) {
                            $id_banco = $banco->id;
                        }
                        $tipos_pago['id_banco']   = $id_banco; //si es efectivo no se envia
                        $tipos_pago['cuenta']     = $pagoonline->credittype . '-' . $pagoonline->paymentmethod; //si es efectivo no se envia
                        $tipos_pago['giradoa']    = null; //si es efectivo no se envia
                        $tipos_pago['valor']      = $data_src['total_valor']; //valor a pagar de total
                        $tipos_pago['valor_base'] = $data_src['total_valor']; //valor a pagar de base
                        $pagos['tipos_pago']      = $tipos_pago;
                        $data['formas_pago']      = $pagos;
                        if ( $data_src['fecha_envio'] != null ) {
                            $flag_error = true;
                            $msn_error  = 'Ya enviado al SRI';
                        }
                        if ($flag_error) {
                            Log_usuario::create([
                                'id_usuario'  => $idusuario,
                                'ip_usuario'  => $ip_cliente,
                                'descripcion' => "GESTION DE PAGOS EN LINEA",
                                'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                                'dato1'       => "ORDEN:".$id_orden,
                                'dato_ant2'   => "ERROR:",
                                'dato2'       => $msn_error,
                                'dato_ant4'   => "-",
                            ]);
                            return response()->json([ 'estado' => 'error', 'mensaje' => $msn_error ]); 
                        }
                        //return $respuesta;
                        Log_usuario::create([
                            'id_usuario'  => $idusuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "GESTION DE PAGOS EN LINEA",
                            'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                            'dato1'       => "ORDEN:".$id_orden,
                            'dato_ant2'   => "FACTURA:".$pagoonline->nro_comprobante,
                            'dato2'       => "ENVIADO AL API NO ELECT",
                            'dato_ant4'   => json_encode($data),
                        ]);
                        $envio = ApiFacturacionController::crea_factura_noelec($data, $pagoonline->nro_comprobante);
                        return response()->json([ 'estado' => 'ok', 'mensaje' => $pagoonline->nro_comprobante, 'envio' => $envio ]);
                    }else{
                        Log_usuario::create([
                            'id_usuario'  => $idusuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "GESTION DE PAGOS EN LINEA",
                            'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                            'dato1'       => "ORDEN:".$id_orden,
                            'dato_ant2'   => "ERROR:",
                            'dato2'       => "SIN NUMERO DE FACTURA",
                            'dato_ant4'   => "-",
                        ]);
                        return response()->json([ 'estado' => 'error', 'mensaje' => "SIN NUMERO DE FACTURA" ]);      
                    }
                }else{
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "GESTION DE PAGOS EN LINEA",
                        'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                        'dato1'       => "ORDEN:".$id_orden,
                        'dato_ant2'   => "ERROR:",
                        'dato2'       => "PAGO EN LINEA NO ENCONTRADO",
                        'dato_ant4'   => "-",
                    ]);
                    return response()->json([ 'estado' => 'error', 'mensaje' => "PAGO EN LINEA NO ENCONTRADO"]);    
                }
            } else {
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "-",
                    'dato1'       => "-",
                    'dato_ant2'   => "ERROR:",
                    'dato2'       => "CREDENCIALES INVALIDAS",
                    'dato_ant4'   => "-",
                ]);
                return response()->json([ 'estado' => 'error', 'mensaje' => "CREDENCIALES INVALIDAS"]);
            }
        } else {
            Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "-",
                    'dato1'       => "-",
                    'dato_ant2'   => "ERROR:",
                    'dato2'       => "CREDENCIALES INVALIDAS",
                    'dato_ant4'   => "-",
                ]);
            return response()->json([ 'estado' => 'error', 'mensaje' => "CREDENCIALES INVALIDAS"]);
        }
    }*/
    public function labs_gestion_servicios(Request $request)
    {
        $idusuario  = 'FACELECTRO';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->getContent();
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "LLEGA SOLICITUD",
                    'dato1'       => "-",
                    'dato_ant2'   => "-",
                    'dato2'       => "-",
                    'dato_ant4'   => $elemento,
                ]);
                //return $elemento;

                //var_dump($elemento);exit();
                $data_src = json_decode($elemento, true); //return $data;
                $id_orden    = $data_src['id_orden'];
                $id_empresa  = $data_src['empresa'];
                $sucursal    = $data_src['sucursal'];
                $caja        = $data_src['caja']; //dd($data_src);
                $tproductos  = $data_src['productos'];
                $data           = [];
                $cliente        = [];
                $direccion      = [];
                $producto       = [];
                $info_adicional = [];
                $pago           = [];
                $info           = [];
                $tipos_pago     = [];
                $productos      = [];
                //return [ $id_orden, $id_empresa, $sucursal, $caja ];
                //$pagoonline = DB::table('pagosenlinea')->where('clave', $id_orden)->where('empresa_id',$id_empresa)->where('sucursal',$sucursal)->where('caja',$caja)->first(); //return json_encode($pagoonline);
                //if (!is_null($pagoonline)) {
                //if ($pagoonline->nro_comprobante != null) {
                /*
                        $nonce      = $this->getNonce(12);
                        $empresa    = Empresa::find($id_empresa);//return json_encode($empresa);
                        //compruebo si la empresa esta habilitada como facturacion electronica
                        //extraigo datos de la empresas y creo el token
                        $appId     = $empresa->appid;
                        $appSecret = $empresa->appsecret;
                        $date      = date('c');
                        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
                        $url = "https://api.pagosyfacturas.com/api/billing/FACTURA_" . $id_empresa . "_" . $pagoonline->nro_comprobante;//dd($url);
                        //return json_encode($url);
                        //dd($url);
                        $options = array(
                            'http' => array(
                                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                                'method'  => 'POST',
                                'content' => 'prueba',
                            ),
                        );
                        //dd($options);
                        $context   = stream_context_create($options);
                        //dd($context, $url);
                        $response  = file_get_contents($url, false, $context);
                        $respuesta = json_decode($response, true);//return json_encode($respuesta);
                        if ($respuesta['status']['status'] == 'error') {
                            Log_usuario::create([
                                'id_usuario'  => $idusuario,
                                'ip_usuario'  => $ip_cliente,
                                'descripcion' => "GESTION DE PAGOS EN LINEA",
                                'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                                'dato1'       => "ORDEN:".$id_orden,
                                'dato_ant2'   => "ERROR:",
                                'dato2'       => "NO ENCONTRO DOCUMENTO EN PYF",
                                'dato_ant4'   => "-",
                            ]);
                            return response()->json([ 'estado' => 'error', 'mensaje' => "NO ENCONTRO DOCUMENTO EN PYF" ]);
                        }
                        $f_emision           = $respuesta['details']['fecha'];//dd($f_emision);
                        $f_emision           = date('Y-m-d H:i:s', strtotime($f_emision));
                        */
                $f_emision = $data_src['f_emision'];
                $data['empresa']     = $id_empresa;
                $data['fecha']       = date('Y-m-d ', strtotime($f_emision)); //ESTA FECHA ES LA DE EMISION
                $data['electronica'] = '1';
                $cliente['cedula']   = $data_src['cedula'];
                $cliente['tipo'] = $data_src['tipo']; //eduardo dice q el lo calcula y luego se
                if (strlen($data_src['cedula']) == 13 && substr($data_src['cedula'], -3) == '001') {
                    $cliente['tipo'] = '4';
                } elseif (strlen($data_src['cedula']) == 10) {
                    $cliente['tipo'] = '5';
                }
                $cliente['nombre']   = $data_src['nombre'];
                $cliente['apellido'] = '';
                $explode = explode(" ", $data_src['nombre']);
                if (count($explode) >= 4) {
                    $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
                    for ($i = 2; $i < count($explode); $i++) {
                        $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
                    }
                }
                if (count($explode) == 3) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
                }
                if (count($explode) == 2) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1];
                }
                //dd($cliente);
                $cliente['email']            = $data_src['email'];
                $cliente['telefono']         = $data_src['telefono'];
                $direccion['calle']          = $data_src['direccion'];
                $direccion['ciudad']         = $data_src['ciudad'];
                $cliente['direccion']        = $direccion;
                $cliente['nro_autorizacion'] = null;
                $data['cliente']             = $cliente;
                $msn_error  = '';
                $flag_error = false;
                if ($cliente['cedula'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en cedula';
                }
                if ($cliente['nombre'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en Nombre';
                }
                if ($cliente['email'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en email';
                }
                if ($cliente['telefono'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en telefono';
                }
                if ($direccion['calle'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en calle';
                }
                if ($direccion['ciudad'] == null) {
                    //$flag_error=true;
                    //$msn_error='Error en Ciudad';
                    $direccion['ciudad'] = 'GUAYAQUIL';
                }
                $cant = 0; //dd($tproductos);
                foreach ($tproductos as $value) {
                    //dd($tproductos, $value);
                    //se envian los productos
                    $producto['sku']       = $value['sku']; //ID EXAMEN
                    $producto['nombre']    = $value['nombre']; // NOMBRE DEL EXAMEN
                    $producto['cantidad']  = $value['cantidad'];
                    $producto['precio']    = $value['precio']; //DETALLE
                    $producto['descuento'] = $value['descuento'];
                    $producto['subtotal']  = $value['subtotal']; //precio-descuento
                    $producto['tax']       = $value['tax'];
                    $producto['total']     = $value['total']; //SUBTOTAL
                    $producto['copago']    = $value['copago'];;
                    $productos[$cant]      = $producto;
                    $cant++;
                } //dd($productos);

                $data['productos'] = $productos;

                /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                        15  COMPENSACI�N DE DEUDAS
                        16  TARJETA DE D�BITO
                        17  DINERO ELECTR�NICO
                        18  TARJETA PREPAGO
                        19  TARJETA DE CR�DITO
                        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                        21  ENDOSO DE T�TULOS
                         */
                $info_adicional['nombre'] = "AGENTES_RETENCION";
                $info_adicional['valor']  = "Resolucion 1";
                $info[0]                  = $info_adicional;
                $info_adicional['nombre'] = "PACIENTE";
                $info_adicional['valor']  = $data_src['id_paciente'] . ' ' . $data_src['nombre_pac'];
                $info[1]                  = $info_adicional;
                $info_adicional['nombre'] = "MAIL";
                $info_adicional['valor']  = $data_src['email']; //EMAIL
                $info[2]                  = $info_adicional;
                $info_adicional['nombre'] = "CIUDAD";
                $info_adicional['valor']  = $data_src['ciudad']; //EMAIL
                $info[3]                  = $info_adicional;
                $info_adicional['nombre'] = "DIRECCION";
                $info_adicional['valor']  = $data_src['direccion']; //EMAIL
                $info[4]                  = $info_adicional;
                $info_adicional['nombre'] = "ORDEN";
                $info_adicional['valor']  = '' . $data_src['id_orden'] . ''; //EMAIL
                $info[5]                  = $info_adicional;
                $info_adicional['nombre'] = "SEGURO";
                $info_adicional['valor']  = $data_src['seguro']; //SEGURO
                $info[6]                  = $info_adicional;
                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = '';
                $info[7]                  = $info_adicional;
                $pago['forma_pago']            = '01';
                $pago['informacion_adicional'] = $info;
                $pago['dias_plazo']            = '10';
                $data['pago']                  = $pago;
                $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                $data['laboratorio']           = 1;
                $data['paciente']              = $data_src['id_paciente'];
                $data['concepto']              = 'Ingreso de Factura Electronica por Pago en Linea';
                $data['copago']                = 0;
                $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                $data['total_factura']         = $data_src['total_valor'];

                $tipos_pago['id_tipo']            = 5; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = $data_src['fecha_orden'];
                $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $data_src['pago_auth']; //si es efectivo no se envia
                $banco                            = Ct_Bancos::where('nombre', $data_src['issuer'])->first();
                $id_banco                         = '1';
                if (!is_null($banco)) {
                    $id_banco = $banco->id;
                }
                $tipos_pago['id_banco']   = $id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']     = $data_src['credittype'] . '-' . $data_src['paymentmethod']; //si es efectivo no se envia
                $tipos_pago['giradoa']    = null; //si es efectivo no se envia
                $tipos_pago['valor']      = $data_src['total_valor']; //valor a pagar de total
                $tipos_pago['valor_base'] = $data_src['total_valor']; //valor a pagar de base
                $pagos['tipos_pago']      = $tipos_pago;
                $data['formas_pago']      = $pagos;

                if ($data_src['fecha_envio'] != null) {
                    //$flag_error = true;
                    //$msn_error  = 'Ya enviado al SRI';
                }


                if ($flag_error) {

                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "GESTION DE PAGOS EN LINEA",
                        'dato_ant1'   => "EMP:" . $id_empresa . "-SUC:" . $sucursal . "-CAJ:" . $caja,
                        'dato1'       => "ORDEN:" . $id_orden,
                        'dato_ant2'   => "ERROR:",
                        'dato2'       => $msn_error,
                        'dato_ant4'   => "-",
                    ]);

                    return response()->json(['estado' => 'error', 'mensaje' => $msn_error]);
                }

                //return $respuesta;
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "EMP:" . $id_empresa . "-SUC:" . $sucursal . "-CAJ:" . $caja,
                    'dato1'       => "ORDEN:" . $id_orden,
                    'dato_ant2'   => "FACTURA:" . $data_src['nro_comprobante'],
                    'dato2'       => "ENVIADO AL API NO ELECT",
                    'dato_ant4'   => json_encode($data),
                ]);

                //dd("antes del api");
                //return $data;
                $envio = ApiFacturacionController::crea_factura_noelec($data, $data_src['nro_comprobante']);


                return response()->json(['estado' => 'ok', 'mensaje' => $data_src['nro_comprobante'], 'envio' => $envio]);

                /*}else{

                        Log_usuario::create([
                            'id_usuario'  => $idusuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "GESTION DE PAGOS EN LINEA",
                            'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                            'dato1'       => "ORDEN:".$id_orden,
                            'dato_ant2'   => "ERROR:",
                            'dato2'       => "SIN NUMERO DE FACTURA",
                            'dato_ant4'   => "-",
                        ]);
                        return response()->json([ 'estado' => 'error', 'mensaje' => "SIN NUMERO DE FACTURA" ]);      
                    }*/
                /*}else{
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "GESTION DE PAGOS EN LINEA",
                        'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                        'dato1'       => "ORDEN:".$id_orden,
                        'dato_ant2'   => "ERROR:",
                        'dato2'       => "PAGO EN LINEA NO ENCONTRADO",
                        'dato_ant4'   => "-",
                    ]);
                    return response()->json([ 'estado' => 'error', 'mensaje' => "PAGO EN LINEA NO ENCONTRADO"]);    
                }*/
            } else {
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "-",
                    'dato1'       => "-",
                    'dato_ant2'   => "ERROR:",
                    'dato2'       => "CREDENCIALES INVALIDAS",
                    'dato_ant4'   => "-",
                ]);
                return response()->json(['estado' => 'error', 'mensaje' => "CREDENCIALES INVALIDAS"]);
            }
        } else {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GESTION DE PAGOS EN LINEA",
                'dato_ant1'   => "-",
                'dato1'       => "-",
                'dato_ant2'   => "ERROR:",
                'dato2'       => "CREDENCIALES INVALIDAS",
                'dato_ant4'   => "-",
            ]);
            return response()->json(['estado' => 'error', 'mensaje' => "CREDENCIALES INVALIDAS"]);
        }
    }
    public function cotizacion_externos($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalles   = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia($orden->id_paciente);
        $view = \View::make('laboratorio.orden.cotizacion_pdf', compact('orden', 'detalles', 'age', 'forma_pago', 'res'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');
    }
}
