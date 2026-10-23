<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\contable\ComprasController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;

date_default_timezone_set('America/Guayaquil');

class FacturaLabsController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20)) == false) {
            return true;
        }
    }

    public function datos_factura($id)
    {

        $orden   = Examen_Orden::find($id);
        $cliente = Ct_Clientes::where('identificacion', $orden->id_paciente)->where('estado', '1')->first();
        $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        $valor_forma   = $orden->detalle_forma_pago->sum('valor');
        $total_forma   = $valor_forma + $recargo_valor;
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', session('id_empresa'))
            ->get();
        return view('laboratorio/facturalabs/datos_facturas', ['orden' => $orden, 'cliente' => $cliente, 'total_forma' => $total_forma, 'sucursales' => $sucursales]);
    }

    public function humanlabs_enviar_sri($id)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $orden = Examen_Orden::find($id);
            $data['empresa'] = '0993075000001'; // humanlabs
            //$data['empresa']      = '0992704152001';   //gastroclinica
            //$data['empresa'] = '1391914857001'; // grlabs
            $cliente['cedula'] = $orden->cedula_factura;
            $cliente['tipo'] = $orden->tipo_documento; //eduardo dice q el lo calcula
            $cliente['nombre'] = $orden->nombre_factura;
            $cliente['apellido'] = '';
            $explode = explode(" ", $orden->nombre_factura);
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
            $cliente['email']     = $orden->email_factura;
            $cliente['telefono']  = $orden->telefono_factura;
            $direccion['calle']   = $orden->direccion_factura;
            $direccion['ciudad']  = $orden->ciudad_factura;
            $cliente['direccion'] = $direccion;
            $data['cliente'] = $cliente;
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
                $flag_error = true;
                $msn_error  = 'Error en Ciudad';
            }
            $cant = 0;
            foreach ($orden->detalles as $value) {
                //se envian los productos
                $producto_precio   = $value->valor;
                $producto_subtotal = $value->valor - $value->valor_descuento;
                if ($orden->cobrar_pac_pct < 100) {
                    $producto_precio   = $value->valor_con_oda;
                    $producto_subtotal = $value->valor_con_oda - $value->valor_descuento;
                }
                $producto['sku'] = "LABS-" . $value->examen->id; //ID EXAMEN
                $producto['nombre'] = $value->examen->nombre; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = "1";
                $producto['precio'] = $producto_precio; //DETALLE
                $producto['descuento'] = $value->valor_descuento;
                $producto['subtotal'] = $producto_subtotal; //precio-descuento
                $producto['tax'] = "0";
                $producto['total'] = $producto_subtotal; //SUBTOTAL
                $producto['copago'] = "0";
                $productos[$cant] = $producto;
                $cant++;
            }
            if ($orden->recargo_valor > 0) {
                $producto['sku']       = "LABS-FEE";
                $producto['nombre']    = 'FEE-ADMINISTRATIVO';
                $producto['cantidad']  = "1";
                $producto['precio']    = $orden->recargo_valor; //DETALLE
                $producto['descuento'] = '0';
                $producto['subtotal']  = $orden->recargo_valor; //precio-descuento
                $producto['tax']       = "0";
                $producto['total']     = $orden->recargo_valor; //SUBTOTAL
                $productos[$cant]      = $producto;
                $cant++;
            }
            $data['productos'] = $productos;
            /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
            15  COMPENSACIÓN DE DEUDAS
            16  TARJETA DE DÉBITO
            17  DINERO ELECTRÓNICO
            18  TARJETA PREPAGO
            19  TARJETA DE CRÉDITO
            20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
            21  ENDOSO DE TÍTULOS
            */
            $info_adicional['nombre'] = "AGENTES_RETENCION";
            $info_adicional['valor']  = "Resolucion 1";
            $info[0]                  = $info_adicional;
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $info[1]                  = $info_adicional;
            $info_adicional['nombre'] = "MAIL";
            $info_adicional['valor']  = $orden->email_factura; //EMAIL
            $info[2]                  = $info_adicional;
            $info_adicional['nombre'] = "CIUDAD";
            $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
            $info[3]                  = $info_adicional;
            $info_adicional['nombre'] = "DIRECCION";
            $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
            $info[4]                  = $info_adicional;
            $info_adicional['nombre'] = "ORDEN";
            $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
            $info[5]                  = $info_adicional;
            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
            $info[6] = $info_adicional;
            $cuenta_forma = $orden->detalle_forma_pago->count();
            //dd($cuenta_forma);
            if ($cuenta_forma > 1) {
                $pago['forma_pago']       = '20';
                $info_adicional['nombre'] = "FORMA_PAGO";
                $texto                    = '';
                foreach ($orden->detalle_forma_pago as $fp) {
                    $total = $fp->valor + $fp->p_fi;
                    $total = round($total, 2);
                    $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
                }
                $info_adicional['valor'] = $texto;
                $info[7]                 = $info_adicional;
            } else {
                $forma_pago = $orden->detalle_forma_pago->first();
                $tipo       = $forma_pago->id_tipo_pago;
                if ($tipo == '1') {
                    $pago['forma_pago'] = '01';
                } elseif ($tipo == '2') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '3') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '4') {
                    $pago['forma_pago'] = '19';
                } elseif ($tipo == '5') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '7') {
                    $pago['forma_pago'] = '01';
                } else {
                    $pago['forma_pago'] = '16';
                }
            }
            $pago['informacion_adicional'] = $info;
            $pago['dias_plazo'] = '10';
            $data['pago'] = $pago;
            $data['contable'] = 1; //si se marca 1 crea factura, 0 no crea factura
            $data['laboratorio'] = 1;
            $data['paciente'] = $orden->id_paciente;
            $data['concepto'] = 'Factura Electronica -' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $data['copago'] = 0;
            $data['id_seguro'] = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
            if ($orden->cobrar_pac_pct < 100) {
                $data['total_factura'] = $orden->total_con_oda;
            } else {
                $data['total_factura'] = $orden->total_valor;
            }
            $fp_cant = 0;
            foreach ($orden->detalle_forma_pago as $fp) {
                $tipos_pago['id_tipo'] = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha'] = substr($orden->fecha_orden, 0, 10);
                $tipos_pago['tipo_tarjeta'] = $fp->tipo_tarjeta; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                $tipos_pago['id_banco'] = $fp->banco; //si es efectivo no se envia
                $tipos_pago['cuenta'] = $fp->cuenta; //si es efectivo no se envia
                $tipos_pago['giradoa'] = null; //si es efectivo no se envia
                $tipos_pago['valor'] = $fp->valor + $fp->p_fi; //valor a pagar de total
                $tipos_pago['valor_base'] = $fp->valor + $fp->p_fi; //valor a pagar de base
                $tipos_pago['info_banco'] = Ct_Bancos::find($fp->banco);
                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            }
            $data['nota_electronica'] = $orden->observacion_app;
            $data['formas_pago'] = $pagos;
            $data['seguro'] = Seguro::find($orden->seguro->id);
            $data['idUsuario'] = $idusuario;
            $establecimiento = Ct_Sucursales::find($orden->sucursal);
            $puntoEmision = Ct_Caja::find($orden->punto_emision);
            $data['sucursal'] =  $establecimiento->codigo_sucursal;
            $data['punto_emision'] = $puntoEmision->codigo_caja;
            $data['emisor'] = 'MD';

            $data['establecimiento'] = $establecimiento->codigo_sucursal;
            $data['ptoEmision'] = $puntoEmision->codigo_caja;
            $data['externo']      = 1;
            if ($orden->fecha_envio != null) {
                $flag_error = true;
                $msn_error  = 'Ya enviado al SRI';
            }
            if ($flag_error) {
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LABORATORIO",
                    'dato_ant1'   => $orden->id,
                    'dato1'       => $orden->id_paciente,
                    'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                    'dato_ant4'   => $msn_error,
                ]);
                return "error";
            }
            $orden->update([
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);
            $envio = ApiFacturacionController::envio_factura($data);
            if (Auth::user()->id_tipo_usuario == 1) {
                //dd($envio);
            }
            $envio = json_decode($envio);
            $orden->update([
                'comprobante' => $envio->comprobante,
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);
            $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ENVIAR AL SRI",
                'dato_ant4'   => $manage,
            ]);
            DB::commit();
            return "ok";
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'Try' => 'Principal',
                'Controlador' => 'FacturaLabsController',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ];
            return $arrayError;
        }
        //dd($envio->comprobante);
    }

    public function pagoenlinea_factura()
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $año    = '2021';
        $mes     = '06';
        $ordenes = Examen_Orden::where('estado', '1')->where('estado_pago', '1')->where('pago_online', '1')->whereNull('comprobante')->whereNull('fecha_envio')->where('anio', $año)->where('mes', $mes)->get();
        //dd($ordenes);
        /*$fecha = '2021-04-09';

        $ordenes    = Examen_Orden::where('estado','1')->where('estado_pago','1')->where('pago_online','1')->whereNull('comprobante')->whereNull('fecha_envio')->whereBetween('fecha_orden',[$fecha . ' 00:00', $fecha . ' 23:59'])->get();*/

        //dd($ordenes);
        foreach ($ordenes as $orden) {
            $data           = array();
            $cliente        = array();
            $direccion      = array();
            $producto       = array();
            $info_adicional = array();
            $pago           = array();
            $info           = array();
            $tipos_pago     = array();
            $productos      = array();

            $pagoonline = DB::table('pagosenlinea')->where('clave', $orden->id)->first();
            if (!is_null($pagoonline)) {
                if ($pagoonline->nro_comprobante != null) {
                    $datavh['empresa']     = '0993075000001';
                    $datavh['tipo']        = 'comprobante';
                    $datavh['comprobante'] = $pagoonline->nro_comprobante;

                    $comprascontroller = new ComprasController();
                    $finaly            = json_decode($comprascontroller->estado_comprobante($datavh), true);
                    $f_emision         = $finaly['details']['fecha'];
                    $f_emision         = date('Y-m-d H:i:s', strtotime($f_emision));

                    //dd(date('Y-m-d',strtotime($f_emision)));
                    //dd($pagoonline);
                    $data['empresa']     = '0993075000001';
                    $data['fecha']       = date('Y-m-d', strtotime($f_emision));
                    $data['electronica'] = '1';
                    //dd($data);
                    $cliente['cedula'] = $orden->cedula_factura;
                    //ruc 4 - cedula 5 - pass 6
                    $cliente['tipo'] = '6'; //eduardo dice q el lo calcula y luego se
                    if (strlen($orden->cedula_factura) == 13 && substr($orden->cedula_factura, -3) == '001') {
                        $cliente['tipo'] = '4';
                    } elseif (strlen($orden->cedula_factura) == 10) {
                        $cliente['tipo'] = '5';
                    }

                    $cliente['nombre']   = $orden->nombre_factura;
                    $cliente['apellido'] = '';

                    //dd($orden);

                    $explode = explode(" ", $orden->nombre_factura);
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
                    $cliente['email']            = $orden->email_factura;
                    $cliente['telefono']         = $orden->telefono_factura;
                    $direccion['calle']          = $orden->direccion_factura;
                    $direccion['ciudad']         = $orden->ciudad_factura;
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
                        $direccion['ciudad'] = 'GUAYAQUIL';
                        //$flag_error=true;
                        //$msn_error='Error en Ciudad';
                    }
                    //dd($orden->detalles);

                    $cant = 0;
                    foreach ($orden->detalles as $value) {
                        //se envian los productos
                        $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
                        $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
                        $producto['cantidad']  = "1";
                        $producto['precio']    = $value->valor; //DETALLE
                        $producto['descuento'] = $value->valor_descuento;
                        $producto['subtotal']  = $value->valor - $value->valor_descuento; //precio-descuento
                        $producto['tax']       = "0";
                        $producto['total']     = $value->valor - $value->valor_descuento; //SUBTOTAL
                        $producto['copago']    = "0";
                        $productos[$cant]      = $producto;
                        $cant++;
                    }

                    $data['productos'] = $productos;
                    /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                    15  COMPENSACIÓN DE DEUDAS
                    16  TARJETA DE DÉBITO
                    17  DINERO ELECTRÓNICO
                    18  TARJETA PREPAGO
                    19  TARJETA DE CRÉDITO
                    20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                    21  ENDOSO DE TÍTULOS
                     */
                    $info_adicional['nombre'] = "AGENTES_RETENCION";
                    $info_adicional['valor']  = "Resolucion 1";
                    $info[0]                  = $info_adicional;

                    $info_adicional['nombre'] = "PACIENTE";
                    $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
                    $info[1]                  = $info_adicional;

                    $info_adicional['nombre'] = "MAIL";
                    $info_adicional['valor']  = $orden->email_factura; //EMAIL
                    $info[2]                  = $info_adicional;

                    $info_adicional['nombre'] = "CIUDAD";
                    $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
                    $info[3]                  = $info_adicional;

                    $info_adicional['nombre'] = "DIRECCION";
                    $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
                    $info[4]                  = $info_adicional;

                    $info_adicional['nombre'] = "ORDEN";
                    $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
                    $info[5]                  = $info_adicional;

                    $info_adicional['nombre'] = "SEGURO";
                    $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
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
                    $data['paciente']              = $orden->id_paciente;
                    $data['concepto']              = 'Ingreso de Factura Electronica por Pago en Linea';
                    $data['copago']                = 0;
                    $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                    $data['total_factura']         = $orden->total_valor;

                    $tipos_pago['id_tipo']            = 5; //metodo de pago efectivo, tarjeta, etc
                    $tipos_pago['fecha']              = substr($orden->fecha_orden, 0, 10);
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
                    $tipos_pago['valor']      = $orden->total_valor; //valor a pagar de total
                    $tipos_pago['valor_base'] = $orden->total_valor; //valor a pagar de base
                    $pagos['tipos_pago']      = $tipos_pago;
                    $data['formas_pago']      = $pagos;

                    if ($orden->fecha_envio != null) {
                        $flag_error = true;
                        $msn_error  = 'Ya enviado al SRI';
                    }

                    if ($flag_error) {
                        Log_usuario::create([
                            'id_usuario'  => $idusuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "LABORATORIO",
                            'dato_ant1'   => $orden->id,
                            'dato1'       => $orden->id_paciente,
                            'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                            'dato_ant4'   => $msn_error,
                        ]);
                        return "error";
                    }
                    //dd($data);

                    $orden->update([
                        'fecha_envio' => date('Y-m-d H:i:s'),
                    ]);

                    //dd($data);
                    $envio = ApiFacturacionController::crea_factura_noelec($data, $pagoonline->nro_comprobante);

                    $orden->update([
                        'comprobante' => $pagoonline->nro_comprobante,
                        'fecha_envio' => date('Y-m-d H:i:s'),
                    ]);

                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => "FACTURA PAGO EN LINEA",
                        'dato_ant4'   => $pagoonline->nro_comprobante,
                    ]);
                }
            }
        }

        return "ok";
    }
    public function cuadrar($id)
    {

        $orden         = Examen_Orden::find($id);
        $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        $valor_forma   = $orden->detalle_forma_pago->sum('valor');
        $total_forma   = $valor_forma + $recargo_valor;
        //dd($orden->total_valor,$total_forma);
        if ($orden->cobrar_pac_pct < 100) {
            if (round($orden->total_con_oda, 2) == round($total_forma, 2)) {
                return 'ok';
            } else {
                return 'no';
            }
        } else {
            if (round($orden->total_valor, 2) == round($total_forma, 2)) {
                return 'ok';
            } else {
                return 'no';
            }
        }
    }
    public function forma_pago($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        $pagos = Ct_Tipo_Pago::where('estado', '1')->get();
        //$forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden',$id_orden)->where('estado','1')->get();
        $forma_pago = $orden->detalle_forma_pago;

        return view('laboratorio/orden/forma_pago', ['pagos' => $pagos, 'id_orden' => $id_orden, 'forma_pago' => $forma_pago, 'orden' => $orden]);
    }
    public function forma_pago_ajax($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        $pagos = Ct_Tipo_Pago::where('estado', '1')->get();
        //$forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden',$id_orden)->where('estado','1')->get();
        $forma_pago = $orden->detalle_forma_pago;

        return view('laboratorio/orden/forma_pago_ajax', ['pagos' => $pagos, 'id_orden' => $id_orden, 'forma_pago' => $forma_pago, 'orden' => $orden]);
    }
    public function forma_gastro_ajax($id_ordv)
    {

        $orden_venta = Ct_Orden_Venta::find($id_ordv);

        return view('laboratorio/orden/forma_pago_gastro_ajax', ['orden_venta' => $orden_venta]);
    }
    public function calculo_oda($id_ordv)
    {

        $orden_venta         = Ct_Orden_Venta::find($id_ordv);
        $orden_venta_detalle = $orden_venta->detalles->first();

        return view('laboratorio/agrupada/calcula_oda_ajax', ['orden_venta' => $orden_venta, 'orden_venta_detalle' => $orden_venta_detalle]);
    }
    public function guardar_forma(Request $request)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $fecha           = date('Y-m-d');
        $id_examen_orden = $request['id_orden'];
        //FALTA CUENTA, P_FI
        $fi       = 0;
        $fi_valor = 0;
        if ($request['id_pago'] == '4') {
            //$fi = 0.07;

        }
        if ($request['id_pago'] == '6') {
            //$fi = 0.045;
        }
        $fi_valor = round($request['valor'] * $fi, 2);
        $arr      = [
            'id_examen_orden' => $id_examen_orden,
            'id_tipo_pago'    => $request['id_pago'],
            'banco'           => $request['id_banco'],
            'tipo_tarjeta'    => $request['id_tipo_tarjeta'],
            'numero'          => $request['transaccion'],
            'valor'           => $request['valor'],
            'posee_fi'        => $fi,
            'p_fi'            => $fi_valor,
            'fecha'           => $fecha,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Examen_Detalle_Forma_Pago::create($arr);

        $orden         = Examen_Orden::find($id_examen_orden);
        $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        $total         = $orden->valor + $recargo_valor;
        $total         = $orden->valor + $recargo_valor - $orden->descuento_valor;
        $total_con_oda = 0;
        if ($orden->cobrar_pac_pct < 100) {
            $total_con_oda = $orden->valor_con_oda + $recargo_valor - $orden->descuento_valor;
        }

        $arr2 = [
            'recargo_valor' => $recargo_valor,
            'total_valor'   => $total,
            'total_con_oda' => $total_con_oda,
        ];
        $orden->update($arr2);

        return ['estado' => 'ok', 'id_orden' => $request['id_orden']];
    }
    public function guardar_forma_gastro(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha      = date('Y-m-d');
        //dd($request->all());
        //FALTA CUENTA, P_FI
        $fi       = 0;
        $fi_valor = 0;
        if ($request['id_pago_gc'] == '4') {
            //$fi       = 0.07;
            //$fi_valor = 1;

        }
        if ($request['id_pago_gc'] == '6') {
            //$fi       = 0.02;
            //$fi_valor = 1;
        }
        $total_fi = round($request['valor_gc'] * $fi, 2);
        //dd($total_fi);
        $arr = [
            'tipo'            => $request['id_pago_gc'],
            'id_orden'        => $request['id_orden_venta'],
            'banco'           => $request['id_banco_gc'],
            'tipo_tarjeta'    => $request['id_tipo_tarjeta_gc'],
            'numero'          => $request['transaccion_gc'],
            'valor'           => $request['valor_gc'],
            'posee_fi'        => $fi_valor,
            'p_fi'            => $fi,
            'fecha'           => $fecha,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        //dd($arr);

        Ct_Orden_Venta_Pago::create($arr);

        $orden_venta = Ct_Orden_Venta::find($request['id_orden_venta']);

        $orden_detalle = $orden_venta->detalles->where('descripcion', '=', 'FEE ADMINISTRATIVO')->first();
        if ($request['id_pago_gc'] == '4' || $request['id_pago_gc'] == '6') {
            if (is_null($orden_detalle)) {
                //dd("111");

                $arr_det_f = [
                    'id_orden'        => $request['id_orden_venta'],
                    'descripcion'     => 'FEE ADMINISTRATIVO',
                    'cod_prod'        => 'FEE-',
                    'cantidad'        => '1',
                    'precio'          => $total_fi,
                    'total'           => $total_fi,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'tipo_oda'        => '%',
                    'p_oda'           => 0,
                    'valor_oda'       => 0,
                    'tipo_dcto'       => '%',
                    'p_dcto'          => 0,
                    'descuento'       => 0,
                    'iva'             => 0,
                    'valor_iva'       => 0,
                    'ident_paquete'   => null,
                    'valor_deducible' => 0,
                    'valor_oda_hum'   => 0,
                    'id_producto'     => null,
                ];
                Ct_Orden_Venta_Detalle::create($arr_det_f);

                $arr_cab = [
                    'total'      => round($orden_venta->total + $total_fi, 2),
                    'subtotal_0' => round($orden_venta->total + $total_fi, 2),
                ];

                $orden_venta->update($arr_cab);
            } else {
                //dd($orden_detalle, "222");
                $orden_pago = $orden_venta->pagos;
                //  dd($orden_pago);
                $total = 0;
                foreach ($orden_pago as $orden_p) {
                    if ($orden_p->tipo = '4' || $orden_p->tipo = '6') {
                        $recargo = $orden_p->valor * $orden_p->p_fi;
                        //dd($recargo);
                        $total = round($recargo + $orden_detalle->total, 2);
                    }
                }

                //dd($recargo);

                $arr_det_act = [
                    'precio' => $total,
                    'total'  => $total,
                ];
                $orden_detalle->update($arr_det_act);

                $arr_cab_up = [
                    'total'      => round($orden_venta->total + $recargo, 2),
                    'subtotal_0' => round($orden_venta->total + $recargo, 2),
                ];
                $orden_venta->update($arr_cab_up);
            }
        }

        return ['estado' => 'ok', 'id_orden' => $request['id_orden_venta']];
    }
    public function guardar_oda(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request['id_orden_venta']);

        $orden_venta = Ct_Orden_Venta::find($request['id_orden_venta']);
        //dd($orden_venta);

        $detalle = $orden_venta->detalles->first();

        $valor_base            = $detalle->cantidad * $detalle->precio;
        $p_oda                 = $request['oda_gc'];
        $valor_cobrar_paciente = $valor_base * $p_oda / 100;
        $valor_cobrar_paciente = round($valor_cobrar_paciente, 2);
        $valor_oda             = $valor_base - $valor_cobrar_paciente;
        $valor_neto            = $valor_base - $valor_oda;

        $arr_det = [
            'p_oda'          => 100 - $p_oda,
            'valor_oda'      => $valor_oda,
            'total'          => $valor_neto,
            'id_usuariocrea' => $idusuario,
            'tipo_oda'       => '%',
            'cobrar_paciente' => $valor_neto,
            'cobrar_seguro'   => $valor_oda,

        ];

        //dd($arr_det);

        $detalle->update($arr_det);

        if ($detalle->valor_oda > 0) {
            $arr_cab = [
                'oda'                   => '1',
                'valor_oda'             => $valor_oda,
                'id_usuariocrea'        => $idusuario,
                'total'                 => $valor_neto,
                'subtotal_0'            => $valor_neto,
                'total_sin_tarjeta'     => $valor_neto,
                'total_cobrar_seguro'   => $valor_oda,
            ];
        } else {
            $arr_cab = [
                'oda'            => '0',
                'valor_oda'      => '0',
                'id_usuariocrea' => $idusuario,
                'total'          => $valor_neto,
                'subtotal_0'     => $valor_neto,
            ];
        }

        $orden_venta->update($arr_cab);

        return ['estado' => 'ok', 'id_orden' => $orden_venta->id];
    }
    public function revisa_forma($id_orden)
    {

        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id_orden)->where('estado', '1')->get();

        return "ok";
    }
    public function eliminar_forma($id_orden, $id_forma)
    {
        //dd($id_orden, $id_forma);
        $forma_pago    = Examen_Detalle_Forma_Pago::where('id', $id_forma)->delete();
        $orden         = Examen_Orden::find($id_orden);
        $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        $total         = $orden->valor + $recargo_valor - $orden->descuento_valor;

        $total_con_oda = 0;
        if ($orden->cobrar_pac_pct < 100) {
            $total_con_oda = $orden->valor_con_oda + $recargo_valor - $orden->descuento_valor;
        }

        $arr2 = [
            'recargo_valor' => $recargo_valor,
            'total_valor'   => $total,
            'total_con_oda' => $total_con_oda,
        ];
        $orden->update($arr2);
        //return "ok";
        return ['estado' => 'ok', 'id_or' => $id_orden];
    }

    /*
    public function eliminar_forma_gastro_ant($id_orden, $id_forma)
    {
    //dd($id_orden, $id_forma);
    //$venta_pago    = Ct_Orden_Venta_Pago::where('id', $id_forma)->delete();
    $orden_venta         = Ct_Orden_Venta::find($id_orden);
    $tot_recargo= 0;
    if ($orden_venta->pagos->tipo == '4' || $orden_venta->pagos->tipo == '6') {
    foreach ($orden_venta->pagos as $orden_pago) {
    $recargo_valor = $orden_pago->valor*$orden_pago->p_fi;
    $tot_recargo = round($tot_recargo+$recargo_valor,2);
    }
    $total = $orden_venta->total-$tot_recargo;
    //dd($tot_recargo);

    $arr_up_cab = [
    'total' => $total,
    'subtotal_0' => $total,
    ];

    $orden_venta->update($arr_up_cab);

    $detalle = $orden_venta->detalles->where('descripcion','=','FEE ADMINISTRATIVO')->first();
    //dd($detalle);

    $arr_up_det = [
    'precio'    => $total,
    'total'     =>$total,
    ];

    $detalle->update($arr_up_det);

    }else{

    }

    return "ok";
    }
     */

    public function eliminar_forma_gastro($id_orden, $id_forma)
    {
        $pago      = Ct_Orden_Venta_Pago::find($id_forma);
        $tiene_fee = false;
        if ($pago->tipo == '4' || $pago->tipo == '6') {
            $tiene_fee = true;
        }
        $pago->delete();

        if ($tiene_fee) {
            $orden_venta      = Ct_Orden_Venta::find($id_orden);
            $orden_detalle    = $orden_venta->detalles->where('descripcion', '=', 'FEE ADMINISTRATIVO')->first();
            $orden_venta_pago = $orden_venta->pagos;
            $valor_fee        = 0;
            foreach ($orden_venta_pago as $orden_pago) {
                if ($orden_pago->tipo == '4' || $orden_pago->tipo == '6') {
                    $valor_fee += round($orden_pago->valor * $orden_pago->p_fi, 2);
                }
            }
            if ($valor_fee == 0) {

                $orden_detalle->delete();
            } else {

                $orden_detalle->update([
                    'precio' => $valor_fee,
                    'total'                          => $valor_fee
                ]);
            }
        }
        $orden_venta2 = Ct_Orden_Venta::find($id_orden);
        $detalles     = $orden_venta2->detalles;
        $total        = 0;
        foreach ($detalles as $detalle) {
            $total += $detalle->total;
        }

        $arr_cab = [
            'total'      => $total,
            'subtotal_0' => $total,
        ];

        $orden_venta2->update($arr_cab);

        return ['estado' => 'ok', 'id_orden' => $id_orden];
    }

    public function datos_forma($id_orden)
    {
        $pagos    = Ct_Tipo_Pago::where('estado', '1')->get();
        $tarjetas = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $bancos   = Ct_Bancos::where('estado', '1')->get();
        return view('laboratorio/orden/datos_forma', ['pagos' => $pagos, 'id_orden' => $id_orden, 'tarjetas' => $tarjetas, 'bancos' => $bancos]);
    }

    public function guardar_info_factura(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden = Examen_Orden::find($request->id_orden);
        $empresa = Empresa::find('0993075000001');
        $sucursal = explode('|', $request['sucursal_factura']);
        $punto_emision = explode('|', $request['emision_factura']);
        $arr_orden = [
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            'sucursal' => $sucursal[0] != '' ? $sucursal[0] : '001',
            'punto_emision' => $punto_emision[0] != '' ? $punto_emision[0] : '100',
            'observacion_app' => $request['observacion_factura']
        ];

        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $orden->update($arr_orden);

        return ['estado' => 'ok', 'id_orden' => $request['id_orden']];
    }

    public function crear_producto_labs($id_examen, $id_empresa, $cta_gastos, $cta_ventas, $cta_costos, $cta_devolucion, $impuesto_iva_compras, $impuesto_iva_ventas, $impuesto_servicio, $impuesto_ice)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $modulo     = 'LABS-';
        $clave      = $modulo . $id_examen;

        $nombre = '';
        $examen = Examen::find($id_examen);
        if (!is_null($examen)) {
            $nombre = $examen->nombre;
        }

        $producto = Ct_productos::where('codigo', $clave)->first();
        if (is_null($producto)) {
            //Create
            $p_id = Ct_productos::insertGetId([
                'codigo'                     => $clave,
                'nombre'                     => $nombre,
                'codigo_barra'               => null,
                'descripcion'                => $nombre,
                'id_empresa'                 => $id_empresa,
                'clase'                      => null,
                'grupo'                      => '3',
                'proveedor'                  => null,
                'cta_gastos'                 => $cta_gastos,
                'cta_ventas'                 => $cta_ventas,
                'cta_costos'                 => $cta_costos,
                'cta_devolucion'             => $cta_devolucion,
                'reg_serie'                  => null,
                'mod_precio'                 => 0,
                'mod_desc'                   => 1,
                'iva'                        => 0,
                'promedio'                   => 0,
                'reposicion'                 => 0,
                'lista'                      => 0,
                'ultima_compra'              => 0,
                'descuento'                  => 0,
                'financiero'                 => 0,
                'marca'                      => 'LABS',
                'modelo'                     => 'LABS',
                'stock_minimo'               => 0,
                'fecha_expiracion'           => null,
                'impuesto_iva_compras'       => $impuesto_iva_compras,
                'impuesto_iva_ventas'        => $impuesto_iva_ventas,
                'impuesto_servicio'          => $impuesto_servicio,
                'impuesto_ice'               => $impuesto_ice,
                'clasificacion_impuesto_ice' => null,
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
            ]);
        }
    }

    public function crear_producto_labs_masivo()
    {

        $id_empresa           = '0993075000001';
        $cta_gastos           = null;
        $cta_ventas           = null;
        $cta_costos           = null;
        $cta_devolucion       = null;
        $impuesto_iva_compras = null;
        $impuesto_iva_ventas  = null;
        $impuesto_servicio    = null;
        $impuesto_ice         = null;

        $examenes = Examen::all();
        foreach ($examenes as $value) {
            $id_examen = $value->id;
            $this->crear_producto_labs($id_examen, $id_empresa, $cta_gastos, $cta_ventas, $cta_costos, $cta_devolucion, $impuesto_iva_compras, $impuesto_iva_ventas, $impuesto_servicio, $impuesto_ice);
        }
    }

    public function modal_factura_agrupada()
    {

        $agrup2 = session()->get('a_orden');
        $cuenta = count($agrup2);
        //dd($agrup2);

        return view('laboratorio/orden/modal_factura_agrupada', ['agrup2' => $agrup2, 'cuenta' => $cuenta]);
    }

    public function añadir_factura($id_orden, Request $request)
    {
        $agrup2 = session()->get('a_orden');
        $orden  = Examen_Orden::find($id_orden);
        //dd($agrup2);
        if ($agrup2 == null) {
            $agrup2[0] = $id_orden;
        } else {
            $orden1 = Examen_Orden::find($agrup2[0]);

            if ($orden1->id_seguro == $orden->id_seguro && $orden1->id_nivel == $orden->id_nivel) {
                if (!in_array($id_orden, $agrup2)) {

                    $i          = count($agrup2);
                    $agrup2[$i] = $id_orden;
                }
            } else {
                return "no";
            }
        }

        $request->session()->put('a_orden', $agrup2);

        return "ok";
    }

    public function añadir_factura_contabilidad($id_orden, Request $request)
    {
        $rol = Auth::user()->id_tipo_usuario;

        $agrup2 = session()->get('a_orden');
        $orden  = Examen_Orden::find($id_orden);

        if ($agrup2 == null) {
            $agrup2[0] = $id_orden;
        } else {
            $orden1 = Examen_Orden::find($agrup2[0]);
            if (!in_array($id_orden, $agrup2)) {

                $i          = count($agrup2);
                $agrup2[$i] = $id_orden;
            }
        }

        $request->session()->put('a_orden', $agrup2);

        return "ok";
    }

    public function eliminar_orden_sesion($id_orden, Request $request)
    {

        $agrup2 = session()->get('a_orden');
        $request->session()->forget('a_orden');
        $i     = 0;
        $agrup = array();
        foreach ($agrup2 as $value) {
            if ($value != $id_orden) {
                $agrup[$i] = $value;
                $i++;
            }
        }
        $request->session()->put('a_orden', $agrup);

        return $agrup;
    }

    public function leeañadir_factura($id_orden, Request $request)
    {

        //$orden= Examen_Orden::find($id_orden);
        $agrup2 = session()->get('a_orden');
        //dd($agrup2);
        return $agrup2;
    }

    public function eliminar_sesion(Request $request)
    {

        $request->session()->forget('a_orden');

        return "ok";
    }
    /*
    public function guardar_agrupada(Request $request){
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    $agrup2 = session()->get('a_orden');
    $fecha = date('Y-m-d');
    //dd($request->cedula_factura);
    //crea orden falsa
    $orden_id = Examen_Orden::insertGetId([
    'id_paciente'                =>'LabsAgrup',
    'estado'                     => '-2',
    'anio'                       => substr(date('Y-m-d'), 0, 4),
    'mes'                        => substr(date('Y-m-d'), 5, 2),
    'fecha_orden'                => date('Y-m-d'),
    'ip_creacion'                => $ip_cliente,
    'ip_modificacion'            => $ip_cliente,
    'id_usuariocrea'             => $idusuario,
    'id_usuariomod'              => $idusuario,
    ]);

    foreach ($agrup2 as $value) {
    $orden= Examen_Orden::find($value);

    $arr2 = [
    'orden_agrupado'        => $orden_id,
    ];
    $orden->update($arr2);

    foreach ($orden->detalles as $detalle) {
    $orden_falsa = Examen_orden::find($orden_id);
    $detalle_falsa = $orden_falsa->detalles->where('id_examen', $detalle->id_examen)->first();
    //dd($detalle_falsa);
    if (is_null($detalle_falsa)) {
    $arr_det = [
    'id_examen_orden'   => $orden_id,
    'id_examen'         => $detalle->id_examen,
    'valor'             => $detalle->valor,
    'p_descuento'       => $detalle->p_descuento,
    'valor_descuento'   => $detalle->valor_descuento,
    'ip_creacion'       => $ip_cliente,
    'ip_modificacion'   => $ip_cliente,
    'id_usuariocrea'    => $idusuario,
    'id_usuariomod'     => $idusuario,
    ];
    Examen_Detalle::create($arr_det);
    }else{
    $arr_det_act = [
    'cantidad'          => $detalle_falsa->cantidad + 1,
    'valor_descuento'   => $detalle->valor_descuento + $detalle_falsa->valor_descuento,
    ];
    //dd($detalle_falsa);
    $detalle_falsa->update($arr_det_act);
    }
    }
    }

    $recargo = 0;
    $valor_descuento = 0;
    foreach ($agrup2 as $orden_ori) {
    $orden= Examen_Orden::find($orden_ori);
    $recargo = $recargo+$orden->recargo_valor;
    $valor_descuento = $valor_descuento+$orden->descuento_valor;
    }

    $orden_f = Examen_Orden::find($orden_id);
    $valor = 0;
    $cantidad = 0;
    foreach ($orden_f->detalles as $det_falsa) {

    $valor = $valor+($det_falsa->cantidad * $det_falsa->valor);
    $total = $valor-$valor_descuento+$recargo;
    $cantidad = $cantidad+$det_falsa->cantidad;

    $arr_cab= [
    'cantidad'          => $cantidad,
    'valor'             => $valor,
    'descuento_p'       => $det_falsa->p_descuento,
    'descuento_valor'   => $valor_descuento,
    'total_valor'       => $total,
    'recargo_valor'     => $recargo,
    ];
    }
    $orden_f->update($arr_cab);

    //datos factura
    $input_cli_crea = [
    'identificacion'          => $request['cedula_factura'],
    'nombre'                  => $request['nombre_factura'],
    'ciudad_representante'    => $request['ciudad_factura'],
    'direccion_representante' => $request['direccion_factura'],
    'telefono1_representante' => $request['telefono_factura'],
    'email_representante'     => $request['email_factura'],
    'tipo'                    => $request['documento'],
    'clase'                   => '1',
    'cedula_representante'    => $request['cedula_factura'],
    'estado'                  => '1',
    'id_usuariocrea'          => $idusuario,
    'id_usuariomod'           => $idusuario,
    'ip_creacion'             => $ip_cliente,
    'ip_modificacion'         => $ip_cliente,
    'nombre_representante'    => $request['nombre_factura'],
    'pais'                    => 'Ecuador',

    ];

    $input_cli_mod = [
    'identificacion'          => $request['cedula_factura'],
    'nombre'                  => $request['nombre_factura'],
    'ciudad_representante'    => $request['ciudad_factura'],
    'direccion_representante' => $request['direccion_factura'],
    'telefono1_representante' => $request['telefono_factura'],
    'email_representante'     => $request['email_factura'],
    'tipo'                    => $request['documento'],
    'clase'                   => '1',
    'cedula_representante'    => $request['cedula_factura'],
    'estado'                  => '1',
    'id_usuariomod'           => $idusuario,
    'ip_modificacion'         => $ip_cliente,
    'nombre_representante'    => $request['nombre_factura'],
    'pais'                    => 'Ecuador',

    ];

    $arr_orden = [
    'tipo_documento'    => $request['documento'],
    'cedula_factura'    => $request['cedula_factura'],
    'nombre_factura'    => $request['nombre_factura'],
    'direccion_factura' => $request['direccion_factura'],
    'ciudad_factura'    => $request['ciudad_factura'],
    'email_factura'     => $request['email_factura'],
    'telefono_factura'  => $request['telefono_factura'],
    ];

    foreach ($agrup2 as $ordenes) {
    $ord_ori = Examen_Orden::find($ordenes);
    $ord_ori->update($arr_orden);
    }

    $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

    if (!is_null($cliente)) {
    Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
    } else {
    Ct_Clientes::create($input_cli_crea);
    }

    $ord_falsa = Examen_Orden::find($orden_id);
    $ord_falsa->update($arr_orden);

    $agrupada_sri = $this->agrupada_sri($orden_id);

    return "ok";

    }*/

    public function guardar_agrupada(Request $request)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $agrup2     = session()->get('a_orden');
            $fecha      = date('Y-m-d');
            //crea orden falsa
            $establecimiento = Ct_Sucursales::find($request->sucursal_factura);
            $puntoEmision = Ct_Caja::find($request->emision_factura);
            $orden_id = Examen_Orden::insertGetId([
                'id_paciente'     => 'LabsAgrup',
                'estado'          => '-2',
                'anio'            => substr(date('Y-m-d'), 0, 4),
                'mes'             => substr(date('Y-m-d'), 5, 2),
                'fecha_orden'     => date('Y-m-d'),
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal' => isset($establecimiento->id) ? $establecimiento->id : '001',
                'punto_emision' => isset($puntoEmision->id) ? $puntoEmision->id : '002'
            ]);
            foreach ($agrup2 as $value) {
                $orden = Examen_Orden::find($value);
                //02032021 SOLO SI NO HA SIDO ENVIADO A FACTURAR ANTES
                if ($orden->fecha_envio == null && $orden->estado == '1') {
                    $arr2 = [
                        'orden_agrupado' => $orden_id,
                    ];
                    $orden->update($arr2);
                    foreach ($orden->detalles as $detalle) {
                        $orden_falsa   = Examen_orden::find($orden_id);
                        $detalle_falsa = $orden_falsa->detalles->where('id_examen', $detalle->id_examen)->first();
                        //dd($detalle_falsa);
                        if (is_null($detalle_falsa)) {
                            $arr_det = [
                                'id_examen_orden' => $orden_id,
                                'id_examen'       => $detalle->id_examen,
                                'valor'           => $detalle->valor,
                                'p_descuento'     => $detalle->p_descuento,
                                'valor_descuento' => $detalle->valor_descuento,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ];
                            Examen_Detalle::create($arr_det);
                        } else {
                            $arr_det_act = [
                                'cantidad'        => $detalle_falsa->cantidad + 1,
                                'valor_descuento' => $detalle->valor_descuento + $detalle_falsa->valor_descuento,
                            ];
                            //dd($detalle_falsa);
                            $detalle_falsa->update($arr_det_act);
                        }
                    }
                }
            }

            $recargo         = 0;
            $valor_descuento = 0;
            //02032021 SOLO LAS AGRUPADAS QUE MARCO
            $ordenes_agrupadas = Examen_Orden::where('orden_agrupado', $orden_id)->get();
            foreach ($ordenes_agrupadas as $orden) {
                $recargo         = $recargo + $orden->recargo_valor;
                $valor_descuento = $valor_descuento + $orden->descuento_valor;
            }

            $orden_f  = Examen_Orden::find($orden_id);
            $valor    = 0;
            $cantidad = 0;
            foreach ($orden_f->detalles as $det_falsa) {

                $valor    = $valor + ($det_falsa->cantidad * $det_falsa->valor);
                $total    = $valor - $valor_descuento + $recargo;
                $cantidad = $cantidad + $det_falsa->cantidad;

                $arr_cab = [
                    'cantidad'        => $cantidad,
                    'valor'           => $valor,
                    'descuento_p'     => $det_falsa->p_descuento,
                    'descuento_valor' => $valor_descuento,
                    'total_valor'     => $total,
                    'recargo_valor'   => $recargo,
                ];
            }
            $orden_f->update($arr_cab);

            //datos factura
            $input_cli_crea = [
                'identificacion'          => $request['cedula_factura'],
                'nombre'                  => $request['nombre_factura'],
                'ciudad_representante'    => $request['ciudad_factura'],
                'direccion_representante' => $request['direccion_factura'],
                'telefono1_representante' => $request['telefono_factura'],
                'email_representante'     => $request['email_factura'],
                'tipo'                    => $request['documento'],
                'clase'                   => '1',
                'cedula_representante'    => $request['cedula_factura'],
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'nombre_representante'    => $request['nombre_factura'],
                'pais'                    => 'Ecuador',

            ];

            $input_cli_mod = [
                'identificacion'          => $request['cedula_factura'],
                'nombre'                  => $request['nombre_factura'],
                'ciudad_representante'    => $request['ciudad_factura'],
                'direccion_representante' => $request['direccion_factura'],
                'telefono1_representante' => $request['telefono_factura'],
                'email_representante'     => $request['email_factura'],
                'tipo'                    => $request['documento'],
                'clase'                   => '1',
                'cedula_representante'    => $request['cedula_factura'],
                'estado'                  => '1',
                'id_usuariomod'           => $idusuario,
                'ip_modificacion'         => $ip_cliente,
                'nombre_representante'    => $request['nombre_factura'],
                'pais'                    => 'Ecuador',

            ];

            $arr_orden = [
                'tipo_documento'    => $request['documento'],
                'cedula_factura'    => $request['cedula_factura'],
                'nombre_factura'    => $request['nombre_factura'],
                'direccion_factura' => $request['direccion_factura'],
                'ciudad_factura'    => $request['ciudad_factura'],
                'email_factura'     => $request['email_factura'],
                'telefono_factura'  => $request['telefono_factura'],
            ];

            //02032021 LAS AGRUPADAS MARCADAS
            foreach ($ordenes_agrupadas as $orden) {
                $orden->update($arr_orden);
            }

            $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

            if (!is_null($cliente)) {
                Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
            } else {
                Ct_Clientes::create($input_cli_crea);
            }

            $ord_falsa = Examen_Orden::find($orden_id);
            $ord_falsa->update($arr_orden);
            $agrupada_sri = $this->agrupada_sri($orden_id);
            DB::commit();
            return "ok";
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
            return json_encode($arrayError);
        }
    }

    public function guardar_agrupada_contabilidad(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agrup2     = session()->get('a_orden');
        $fecha      = date('Y-m-d');
        //dd($request->cedula_factura);
        //crea orden falsa
        $orden_id = Examen_Orden::insertGetId([
            'id_paciente'     => 'LabsAgrup',
            'estado'          => '-3',
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'fecha_orden'     => date('Y-m-d'),
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        $acum_publico = 0;
        $acum_privado = 0;
        foreach ($agrup2 as $value) {
            $orden       = Examen_Orden::find($value);
            $orden_falsa = Examen_Orden::find($orden_id);

            $arr2 = [
                'orden_agrupado' => $orden_id,
            ];
            $orden->update($arr2);

            if ($orden->seguro->tipo == '0') {
                $valor = $orden->total_nivel2 * 100;
                $valor = round($valor);
                $valor = $valor / 100;

                $acum_publico += $valor;
            } else {
                $valor2 = $orden->total_valor * 100;
                $valor2 = round($valor2);
                $valor2 = $valor2 / 100;
                $acum_privado += $valor2;
            }

            /*if($orden->seguro->tipo=='0'){
        $det_pub = $orden_falsa->detalles->where('id_examen','1216')->first();
        if(is_null($det_pub)){
        $arr_det = [
        'id_examen_orden'   => $orden_id,
        'id_examen'         => '1216',
        'valor'             => round($orden->total_nivel2,2,PHP_ROUND_HALF_DOWN),
        'valor_descuento'   => round($orden->descuento_valor,2,PHP_ROUND_HALF_DOWN),
        'ip_creacion'       => $ip_cliente,
        'ip_modificacion'   => $ip_cliente,
        'id_usuariocrea'    => $idusuario,
        'id_usuariomod'     => $idusuario,
        ];
        Examen_Detalle::create($arr_det);
        }else{
        $arr_det_act = [
        'valor'             => round($orden->total_nivel2,2,PHP_ROUND_HALF_DOWN) + $det_pub->valor,
        'valor_descuento'   => $orden->descuento_valor + $det_pub->valor_descuento,
        ];
        //dd($detalle_falsa);
        $det_pub->update($arr_det_act);
        }
        }else{
        $det_priv = $orden_falsa->detalles->where('id_examen','1217')->first();
        if(is_null($det_priv)){
        $arr_det = [
        'id_examen_orden'   => $orden_id,
        'id_examen'         => '1217',
        'valor'             => $orden->valor,
        'valor_descuento'   => $orden->descuento_valor,
        'ip_creacion'       => $ip_cliente,
        'ip_modificacion'   => $ip_cliente,
        'id_usuariocrea'    => $idusuario,
        'id_usuariomod'     => $idusuario,
        ];
        Examen_Detalle::create($arr_det);
        }else{
        $arr_det_act = [
        'valor'             => $orden->total_valor + $det_priv->valor,
        'valor_descuento'   => $orden->descuento_valor + $det_priv->valor_descuento,
        ];
        //dd($detalle_falsa);
        $det_priv->update($arr_det_act);
        }
        }*/
        }
        //dd($acum_publico);

        $arr_det = [
            'id_examen_orden' => $orden_id,
            'id_examen'       => '1216',
            'valor'           => $acum_publico,
            'valor_descuento' => 0,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        Examen_Detalle::create($arr_det);
        $arr_det = [
            'id_examen_orden' => $orden_id,
            'id_examen'       => '1217',
            'valor'           => $acum_privado,
            'valor_descuento' => 0,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        Examen_Detalle::create($arr_det);

        $orden_falsa = Examen_Orden::find($orden_id);

        $valor_descuento = $orden_falsa->detalles->sum('valor_descuento');
        $valor           = $orden_falsa->detalles->sum('valor');
        $total_valor     = $valor - $valor_descuento;
        $cantidad        = $orden_falsa->detalles->count();

        $arr_cab = [
            'cantidad'        => $cantidad,
            'valor'           => $valor,
            'descuento_p'     => 0,
            'descuento_valor' => $valor_descuento,
            'total_valor'     => $total_valor,
            'recargo_valor'   => 0,
        ];

        $orden_falsa->update($arr_cab);

        //datos factura
        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $arr_orden = [
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
        ];

        foreach ($agrup2 as $ordenes) {
            $ord_ori = Examen_Orden::find($ordenes);
            $ord_ori->update($arr_orden);
        }

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $ord_falsa = Examen_Orden::find($orden_id);
        $ord_falsa->update($arr_orden);

        //$agrupada_sri = $this->agrupada_sri($orden_id);

        return "ok";
    }

    public function datos_factura_agrupada()
    {
        $data['sucursales'] = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', session('id_empresa'))
            ->get();
        return view('laboratorio/facturalabs/datos_agrupada', $data);
    }

    public function agrupada_sri($id_falsa)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $orden = Examen_Orden::find($id_falsa);
            $data['empresa'] = '0993075000001'; // humanlabs
            //$data['empresa']      = '0992704152001';   //gastroclinica
            $cliente['cedula']   = $orden->cedula_factura;
            $cliente['tipo']     = $orden->tipo_documento; //eduardo dice q el lo calcula
            $cliente['nombre']   = $orden->nombre_factura;
            $cliente['apellido'] = '';
            $explode = explode(" ", $orden->nombre_factura);
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
            $cliente['email']     = $orden->email_factura;
            $cliente['telefono']  = $orden->telefono_factura;
            $direccion['calle']   = $orden->direccion_factura;
            $direccion['ciudad']  = $orden->ciudad_factura;
            $cliente['direccion'] = $direccion;
            $data['cliente']      = $cliente;
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
                $flag_error = true;
                $msn_error  = 'Error en Ciudad';
            }
            $cant = 0;
            foreach ($orden->detalles as $value) {
                //se envian los productos
                $valor                 = $value->valor * $value->cantidad;
                $subtotal              = $valor - $value->valor_descuento;
                $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
                $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = $value->cantidad;
                $producto['precio']    = $value->valor; //DETALLE
                $producto['descuento'] = $value->valor_descuento;
                $producto['subtotal']  = $subtotal; //precio-descuento
                $producto['tax']       = "0";
                $producto['total']     = $subtotal; //SUBTOTAL
                $producto['copago']    = "0";
                $productos[$cant]      = $producto;
                $cant++;
            }

            if ($orden->recargo_valor > 0) {
                $producto['sku']       = "LABS-FEE";
                $producto['nombre']    = 'FEE-ADMINISTRATIVO';
                $producto['cantidad']  = "1";
                $producto['precio']    = $orden->recargo_valor; //DETALLE
                $producto['descuento'] = '0';
                $producto['subtotal']  = $orden->recargo_valor; //precio-descuento
                $producto['tax']       = "0";
                $producto['total']     = $orden->recargo_valor; //SUBTOTAL
                $productos[$cant]      = $producto;
                $cant++;
            }

            $data['productos'] = $productos;
            /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
            15  COMPENSACIÓN DE DEUDAS
            16  TARJETA DE DÉBITO
            17  DINERO ELECTRÓNICO
            18  TARJETA PREPAGO
            19  TARJETA DE CRÉDITO
            20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
            21  ENDOSO DE TÍTULOS
            */
            $info_adicional['nombre'] = "AGENTES_RETENCION";
            $info_adicional['valor']  = "Resolucion 1";
            $info[0]                  = $info_adicional;

            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = ""; //eduardo dice q null
            $info[1]                  = $info_adicional;

            $info_adicional['nombre'] = "MAIL";
            $info_adicional['valor']  = $orden->email_factura; //EMAIL
            $info[2]                  = $info_adicional;

            $info_adicional['nombre'] = "CIUDAD";
            $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
            $info[3]                  = $info_adicional;

            $info_adicional['nombre'] = "DIRECCION";
            $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
            $info[4]                  = $info_adicional;

            $info_adicional['nombre'] = "ORDEN";
            $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
            $info[5]                  = $info_adicional;

            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = 'PARTICULAR'; //SEGURO ???
            $info[6]                  = $info_adicional;
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $establecimiento = Ct_Sucursales::find($orden->sucursal);
            $puntoEmision = Ct_Caja::find($orden->punto_emision);
            $data['seguro'] = '';
            $data['idUsuario'] = $idusuario;
            if ($puntoEmision->codigo_caja != '002') {
                $data['sucursal'] = $establecimiento->id;
                $data['punto_emision'] = $puntoEmision->id;
                $data['emisor'] = 'MD';
            }
            $data['establecimiento'] = $establecimiento->codigo_sucursal;
            $data['ptoEmision'] = $puntoEmision->codigo_caja;
            $data['externo']      = 1;
            $texto                    = '';
            $agrup2  = session()->get('a_orden');
            $fp_cant = 0;
            if ($orden->estado == '-3') {
                $texto = 'PENDIENTE PAGO :' . $orden->total_valor;
                $tipos_pago['id_tipo']            = 7; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = date('Y-m-d H:i:s');
                $tipos_pago['tipo_tarjeta']       = ''; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = ''; //si es efectivo no se envia
                $tipos_pago['id_banco']           = ''; //si es efectivo no se envia
                $tipos_pago['cuenta']             = ''; //si es efectivo no se envia
                $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                $tipos_pago['valor']              = $orden->total_valor; //valor a pagar de total
                $tipos_pago['valor_base']         = $orden->total_valor; //valor a pagar de base
                $tipos_pago['info_banco'] = Ct_Bancos::find($fp->banco);
                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            } else {
                $ordenes_agrupadas = Examen_Orden::where('orden_agrupado', $id_falsa)->get();
                foreach ($ordenes_agrupadas as $orden_ag) {
                    foreach ($orden_ag->detalle_forma_pago as $fp) {
                        $total = $fp->valor + $fp->p_fi;
                        $total = round($total, 2);
                        $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
                    }
                    foreach ($orden_ag->detalle_forma_pago as $fp) {

                        $tipos_pago['id_tipo']            = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
                        $tipos_pago['fecha']              = substr($orden_ag->fecha_orden, 0, 10);
                        $tipos_pago['tipo_tarjeta']       = $fp->tipo_tarjeta; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = $fp->banco; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $fp->valor + $fp->p_fi; //valor a pagar de total
                        $tipos_pago['valor_base']         = $fp->valor + $fp->p_fi; //valor a pagar de base
                        $tipos_pago['info_banco'] = Ct_Bancos::find($fp->banco);
                        $pagos[$fp_cant] = $tipos_pago;
                        $fp_cant++;
                    }
                }
            }

            $info_adicional['valor'] = $texto;
            //$info[7]  = $info_adicional;
            $pago['informacion_adicional'] = $info;
            $pago['dias_plazo']            = '10';
            $data['pago']                  = $pago;
            $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
            $data['laboratorio']           = 1;
            $data['paciente']              = '';
            $data['concepto']              = 'Factura Electronica - ' . $orden->nombre_factura;
            $data['copago']                = 0;
            $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
            $data['total_factura']         = $orden->total_valor;
            $data['formas_pago']           = $pagos;
            //dd($orden_ori);
            //dd($data);

            if ($orden->fecha_envio != null) {
                $flag_error = true;
                $msn_error  = 'Ya enviado al SRI';
            }

            if ($flag_error) {
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LABORATORIO",
                    'dato_ant1'   => $orden->id,
                    'dato1'       => $orden->id_paciente,
                    'dato_ant2'   => "ERROR AL ENVIAR AL SRI AGRUPADA",
                    'dato_ant4'   => $msn_error,
                ]);
                return "error";
            }
            $orden->update([
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);

            /*foreach ($agrup2 as $value) {
        $o_ori = Examen_Orden::find($value);
        $o_ori->update([
        'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        }*/
            $hijas = Examen_Orden::where('orden_agrupado', $id_falsa)->where('estado', '1')->get();
            foreach ($hijas as $hija) {
                $hija->update([
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            }
            //dd($data);
            /* ACTIVAR PARA MANDAR A PRODU */
            $envio = ApiFacturacionController::envio_factura($data);
            try {
                $orden->update([
                    'comprobante' => $envio->comprobante,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            } catch (Exception $e) {
                $envio = json_decode($envio);
                $orden->update([
                    'comprobante' => $envio->comprobante,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            }
            foreach ($hijas as $hija) {
                $hija->update([
                    'comprobante' => $envio->comprobante,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            }
            $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ENVIAR AL SRI AGRUPADA",
                'dato_ant4'   => $manage,
            ]);
            DB::commit();
            return "ok";
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
            return json_encode($arrayError);
        }
    }
    public function recupera_ordenes()
    {

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->join('users as cu', 'cu.id', 'eo.id_usuariocrea')->join('users as mu', 'mu.id', 'eo.id_usuariomod')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo');

        return $ordenes;
    }
    public function carga_masivo(Request $request)
    {

        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $facturadas  = $request['facturadas'];

        $ordenes     = $this->recupera_ordenes();
        $ses_ordenes = $this->recupera_ordenes();

        if ($facturadas == 'FACTURADAS') {

            $ordenes     = $ordenes->whereNotNull('eo.fecha_envio');
            $ses_ordenes = $ses_ordenes->whereNotNull('eo.fecha_envio');
        }
        if ($facturadas == 'NO FACTURADAS') {

            $ordenes     = $ordenes->whereNull('eo.fecha_envio');
            $ses_ordenes = $ses_ordenes->whereNull('eo.fecha_envio');
        }
        if ($fecha != null) {

            $ordenes     = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $ses_ordenes = $ses_ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes     = $ordenes->where('eo.id_seguro', $seguro);
            $ses_ordenes = $ses_ordenes->where('s.tipo', 0);
        }
        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

                $ses_ordenes = $ses_ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $ses_ordenes = $ses_ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        $ordenes     = $ordenes->where('eo.estado', '<>', '0')->paginate(40);
        $ses_ordenes = $ses_ordenes->where('eo.estado', '1')->whereNull('eo.fecha_envio')->where('pago_online', '0')->get();
        $ordenes2    = [];

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo', '1')->get();

        $gestiones = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->get();

        foreach ($ses_ordenes as $value) {
            $id_orden = $value->id;
            $agrup2   = session()->get('a_orden');
            $orden    = Examen_Orden::find($id_orden);

            if ($agrup2 == null) {
                $agrup2[0] = $id_orden;
            } else {
                $orden1 = Examen_Orden::find($agrup2[0]);
                if (!in_array($id_orden, $agrup2)) {

                    $i          = count($agrup2);
                    $agrup2[$i] = $id_orden;
                }
            }

            $request->session()->put('a_orden', $agrup2);
        }

        //dd($ses_ordenes);

        return view('laboratorio/orden/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'ordenes2' => $ordenes2, 'gestiones' => $gestiones, 'facturadas' => $facturadas]);
    }
    public function temporal_factura_agrupada($id_falsa)
    {

        $agrupada_sri = $this->agrupada_sri($id_falsa);

        return "Gracias Amigo";
    }

    public function informacion_factura($id)
    {
        $orden = Examen_Orden::find($id);

        return view('laboratorio/orden/informacion_factura', ['orden' => $orden]);
    }

    public function crea_ventas()
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::where('estado', '1')->where('cobrar_pac_pct', '<', '100')->Where('estado_venta', '0')->whereNotNull('fecha_envio')->get();
        //$validar= Examen_Orden::whereMonth('mes',$mes)->whereYear('anio',$anio)->where('estado','1')->first();
        $c_sucursal          = 0;
        $c_caja              = 0;
        $num_comprobante     = 0;
        $nfactura            = 0;
        $pac                 = "";
        $id_asiento_cabecera = 0;
        foreach ($orden as $ord) {
            $ver = Ct_Ven_Orden::where('orden_venta', $ord->id)->where('tipo', 'VEN-LABS')->first();
            if (is_null($ver)) {
                $factura_venta = [
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $ord->comprobante, //numero de comprobante
                    'id_empresa'          => '0993075000001', // id_empresa
                    'tipo'                => 'VEN-LABS', // es un campo varchar puede ser VEN-LABS
                    'fecha'               => $ord->fecha_envio, //fecha de envio
                    'fecha_procedimiento' => $ord->fecha_envio,
                    'divisas'             => '1', // clavale uno
                    'nombre_cliente'      => $ord->nombre_factura, // nombre del cliente en varchar
                    'tipo_consulta'       => '2', // este puede ser 1 o 0 consulta o procedimiento
                    'id_cliente'          => $ord->cedula_factura, //identificacion del cliente
                    'direccion_cliente'   => $ord->direccion_factura, //direccion del cliente
                    'telefono_cliente'    => $ord->telefono_factura, // telefono del cliente
                    'email_cliente'       => $ord->email_factura, //mail del cliene
                    'orden_venta'         => $ord->id, //el numero de orden, el id de la orden de laboratorio
                    'estado_pago'         => '0', // default 0
                    'id_paciente'         => $ord->id_paciente, // datos del paciente
                    'nombres_paciente'    => $ord->paciente->apellido1 . ' ' . $ord->paciente->apellido2 . ' ' . $ord->paciente->nombre1 . ' ' . $ord->paciente->nombre2, //nombre del paciente
                    'seguro_paciente'     => $ord->id_seguro, //seguro del paciente
                    'copago'              => $ord->valor - $ord->valor_con_oda, // valor copago del paciente
                    'subtotal_0'          => $ord->total_valor, //subtotal 0
                    'subtotal_12'         => '0', //subtotal 12
                    //'subtotal'                      => $request['subtotal1'],
                    'descuento'           => $ord->descuento_valor, // descuento
                    'base_imponible'      => $ord->total_valor, //subtotal
                    'impuesto'            => '0', // el valor del iva
                    // 'transporte'                    => $request['transporte'],
                    'total_final'         => $ord->total_valor, //total de la factura
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $id_venta = Ct_Ven_Orden::insertGetId($factura_venta);
                $examen_detalle = $ord->detalles;
                foreach ($examen_detalle as $det) {
                    $detalle = [
                        'id_ct_ven_orden'      => $id_venta,
                        'id_ct_productos'      => "LABS-" . $det->examen->id,
                        'nombre'               => $det->examen->nombre,
                        'cantidad'             => $det->cantidad,
                        'precio'               => $det->valor,
                        'descuento_porcentaje' => $det->p_descuento,
                        'descuento'            => $det->valor_descuento,
                        'extendido'            => $det->valor_con_oda,
                        'detalle'              => "",
                        'copago'               => $det->valor - $det->valor_con_oda,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];
                    Ct_Ven_Orden_Detalle::create($detalle);
                }
                $arr_ord = [
                    'estado_venta'  => '1',
                    'id_usuariomod' => $idusuario,
                ];
                $ord->update($arr_ord);
                //return response()->json(['success'=>'1','id_orden'=>$id_venta]);
            }
            //return response()->json(['success'=>'1','id_orden'=>'0']);
        }
        return "ok";
    }
    public function enviar_sri2($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden           = Examen_Orden::find($id);
        $data['empresa'] = '0993075000001'; // humanlabs
        //$data['empresa']      = '0992704152001';   //gastroclinica
        //$data['empresa'] = '1391914857001'; // grlabs
        $cliente['cedula']   = $orden->cedula_factura;
        $cliente['tipo']     = $orden->tipo_documento; //eduardo dice q el lo calcula
        $cliente['nombre']   = $orden->nombre_factura;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->nombre_factura);
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

        $cliente['email']     = $orden->email_factura;
        $cliente['telefono']  = $orden->telefono_factura;
        $direccion['calle']   = $orden->direccion_factura;
        $direccion['ciudad']  = $orden->ciudad_factura;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

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
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }

        $cant              = 0;
        $producto_precio   = 0;
        $descuento         = 0;
        $producto_subtotal = 0;
        foreach ($orden->detalles as $value) {
            //se envian los productos
            //$producto_precio       += $value->valor;
            //$descuento             += $value->valor_descuento;
            $producto_subtotal = $producto_precio - $descuento;
            if ($orden->cobrar_pac_pct < 100) {
                $producto_precio += $value->valor_con_oda;
                $descuento += $value->valor_descuento;
                $producto_subtotal = $producto_precio - $descuento;
            }
        }
        $tramite = '';
        if ($orden->numero_oda != '') {
            $tramite = "NUMERO DE TRAMITE: " . $orden->numero_oda;
        }
        $producto_precio = round($producto_precio, 2);
        $producto_subtotal = round($producto_subtotal, 2);
        $producto['sku']       = "LABS"; //ID EXAMEN
        $producto['nombre']    = "EXAMENES DE LABORATORIO - COPAGO: " . $orden->cobrar_pac_pct . " % " . $tramite; // NOMBRE DEL EXAMEN
        $producto['cantidad']  = "1";
        $producto['precio']    = $producto_precio; //DETALLE
        $producto['descuento'] = $descuento;
        $producto['subtotal']  = $producto_subtotal; //precio-descuento
        $producto['tax']       = "0";
        $producto['total']     = $producto_subtotal; //SUBTOTAL
        $producto['copago']    = "0";
        $productos[$cant]      = $producto;
        $cant++;

        if ($orden->recargo_valor > 0) {
            $producto['sku']       = "LABS-FEE";
            $producto['nombre']    = 'FEE-ADMINISTRATIVO';
            $producto['cantidad']  = "1";
            $producto['precio']    = $orden->recargo_valor; //DETALLE
            $producto['descuento'] = '0';
            $producto['subtotal']  = $orden->recargo_valor; //precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $orden->recargo_valor; //SUBTOTAL
            $productos[$cant]      = $producto;
            $cant++;
        }
        //dd($productos);
        $data['productos'] = $productos;
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */
        $info_adicional['nombre'] = "AGENTES_RETENCION";
        $info_adicional['valor']  = "Resolucion 1";
        $info[0]                  = $info_adicional;

        $info_adicional['nombre'] = "PACIENTE";
        $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
        $info[1]                  = $info_adicional;

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_factura; //EMAIL
        $info[2]                  = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
        $info[3]                  = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
        $info[4]                  = $info_adicional;

        $info_adicional['nombre'] = "ORDEN";
        $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
        $info[5]                  = $info_adicional;

        $info_adicional['nombre'] = "SEGURO";
        $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
        $info[6]                  = $info_adicional;

        $cuenta_forma = $orden->detalle_forma_pago->count();
        //dd($cuenta_forma);
        if ($cuenta_forma > 1) {
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto = '';
            foreach ($orden->detalle_forma_pago as $fp) {
                $total = $fp->valor + $fp->p_fi;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[7]                 = $info_adicional;
        } else {
            $forma_pago = $orden->detalle_forma_pago->first();
            $tipo       = $forma_pago->id_tipo_pago;
            if ($tipo == '1') {
                $pago['forma_pago'] = '01';
            } elseif ($tipo == '2') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '3') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '4') {
                $pago['forma_pago'] = '19';
            } elseif ($tipo == '5') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '7') {
                $pago['forma_pago'] = '01';
            } else {
                $pago['forma_pago'] = '16';
            }
        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '10';
        $data['sucursal'] = $orden->sucursal;
        $data['punto_emision'] = $orden->punto_emision;
        $data['emisor'] = 'MD';
        $data['establecimiento'] = $orden->sucursal;
        $data['ptoEmision'] = $orden->punto_emision;
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = $orden->id_paciente;
        $data['concepto']              = 'Factura Electronica -' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        if ($orden->cobrar_pac_pct < 100) {
            $data['total_factura'] = $orden->total_con_oda;
        } else {
            $data['total_factura'] = $orden->total_valor;
        }
        $fp_cant = 0;
        foreach ($orden->detalle_forma_pago as $fp) {
            $tipos_pago['id_tipo']            = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha_orden, 0, 10);
            $tipos_pago['tipo_tarjeta']       = $fp->tipo_tarjeta; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
            $tipos_pago['id_banco']           = $fp->banco; //si es efectivo no se envia
            $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
            $tipos_pago['valor']              = $fp->valor + $fp->p_fi; //valor a pagar de total
            $tipos_pago['valor_base']         = $fp->valor + $fp->p_fi; //valor a pagar de base
            $tipos_pago['info_banco'] = Ct_Bancos::find($fp->banco);
            $pagos[$fp_cant] = $tipos_pago;
            $fp_cant++;
        }
        $data['formas_pago'] = $pagos;
        $data['seguro'] = Seguro::find($orden->seguro->id);
        $data['idUsuario'] = $idusuario;
        $establecimiento = Ct_Sucursales::find($orden->sucursal);
        $puntoEmision = Ct_Caja::find($orden->punto_emision);
        //if ($establecimiento->codigo_sucursal != '001' && $puntoEmision->codigo_caja != '002') {
        if ($puntoEmision->codigo_caja != '002') {
            $data['sucursal'] = $establecimiento->codigo_sucursal;
            $data['punto_emision'] = $puntoEmision->codigo_caja;
            $data['emisor'] = 'MD';
        }
        $data['establecimiento'] = $establecimiento->codigo_sucursal;
        $data['ptoEmision'] = $puntoEmision->codigo_caja;
        $data['externo']      = 1;
        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error  = 'Ya enviado al SRI';
        }
        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }
        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        $envio = ApiFacturacionController::envio_factura($data);
        $envio = json_decode($envio);
        $orden->update([
            'comprobante' => $envio->comprobante,
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        $ordenVen = $this->crearOrdenVenta($orden, $data);
        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);
        return "ok";
    }
    public function crearOrdenVenta($orden, $data)
    {
        if ($orden->cobrar_pac_pct < 100) {
            $id_empresa = '0993075000001';
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = '';
            $pac             = "";
            $paciente = Paciente::where('id', $orden->id_paciente)->first();
            $pac = " | " . $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
            $id_asiento_cabecera = 0;
            $idusuario = Auth::user()->id;
            $porcentaje = 100 - $orden->cobrar_pac_pct;
            $valorCopago = ($orden->total_valor * $porcentaje) / 100;
            $factura_venta = [
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => 'VEN-FA',
                'fecha'               => date('Y-m-d H:i:s'),
                'divisas'             => 1,
                'nombre_cliente'      => $data['cliente']['nombre'] . ' ' . $data['cliente']['apellido'],
                'tipo_consulta'       => 1,
                'id_cliente'          => $data['cliente']['cedula'], //nombre_cliente
                'direccion_cliente'   => $data['cliente']['direccion']['calle'] . ' ' . $data['cliente']['direccion']['ciudad'],
                'ruc_id_cliente'      => $data['cliente']['cedula'],
                'telefono_cliente'    => $data['cliente']['telefono'],
                'email_cliente'       => $data['cliente']['email'],
                'orden_venta'         => $orden->id,
                'estado_pago'         => '0',
                'id_paciente'         => $orden->id_paciente,
                'nombres_paciente'    => $pac,
                'seguro_paciente'     => $orden->id_seguro,
                'procedimientos'      => 'EXAMENES',
                'fecha_procedimiento' => date('Y-m-d H:i:s'),
                'copago'              => $valorCopago,
                'subtotal_0'          => $valorCopago,
                'subtotal_12'         => 0,
                'descuento'           => $orden->descuento_valor,
                'base_imponible'      => $valorCopago,
                'impuesto'            => 0,
                'total_final'         => $valorCopago,
                'valor_contable'      => $valorCopago,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            DB::beginTransaction();
            try {
                $id_venta  = Ct_ven_orden::insertGetId($factura_venta);
                $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
                $detalles = Examen_Detalle::join('examen as e', 'id_examen', 'e.id')
                    ->where('id_examen_orden', $orden->id)
                    ->get([
                        'examen_detalle.id',
                        'examen_detalle.id_examen',
                        'examen_detalle.valor',
                        'examen_detalle.cantidad',
                        'e.nombre',
                        'examen_detalle.valor_descuento as descuento',
                        'examen_detalle.p_descuento'

                    ]);
                if (count($detalles) > 0) {
                    foreach ($detalles as $row) {
                        $copago = ((($row->cantidad * $row->valor) - $row->descuento) * $porcentaje) / 100;
                        $detalle = [
                            'id_ct_ven_orden'      => $id_venta,
                            'id_ct_productos'      => 'LABS-' . $row->id_examen,
                            'nombre'               => $row->nombre,
                            'cantidad'             => $row->cantidad,
                            'precio'               => $row->valor,
                            'descuento_porcentaje' => $row->p_descuento,
                            'descuento'            => $row->descuento,
                            'extendido'            => ($row->cantidad * $row->valor) - $row->descuento,
                            'copago'               => $copago,
                            'check_iva'            => 0,
                            'ip_creacion'          => $ip_cliente,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                        ];
                        Ct_ven_orden_detalle::create($detalle);
                    }
                }
                DB::commit();
                return $id_venta;
            } catch (Exception $e) {
                DB::rollBack();
                $arrayError = [
                    'Controller' => 'FacturaLabsController',
                    'Try' => 'Principal',
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine()
                ];
                echo '<pre>';
                print_r($arrayError);
                return 0;
            }
        } else
            return 1;
    }
    public function masivo_facturacion_noviembre()
    {
        $ordenes = Examen_Orden::where('estado', '1')->where('anio', '2021')->where('mes', '11')->whereNull('fecha_envio')->whereNotNull('comprobante')
            //->where('fecha_envio','<=','2021-11-17 00:00:00')
            //->where('fecha_envio','>=','2021-11-01 00:00:00')
            ->get();
        ///VALOR A PROBAR
        $limite   = 10;
        $contador = 0;
        //dd($ordenes);
        foreach ($ordenes as $orden) {
            if ($orden->id == '54376' || $orden->id == '54393' || $orden->id == '54399' || $orden->id == '52048' || $orden->id == '52049' || $orden->id == '52174' || $orden->id == '52176' || $orden->id == '53046' || $orden->id == '53047' || $orden->id == '53126' || $orden->id == '47704' || $orden->id == '47705') {
            } else {
                if ($contador < $limite) {
                    $venta = Ct_ventas::where('id_empresa', '0993075000001')->where('nro_comprobante', $orden->comprobante)->first();
                    //dd($orden);
                    $this->humanlabs_enviar_sri($orden->id);
                    sleep(60);

                    $orden_new = Examen_Orden::find($orden->id);

                    Log_usuario::create([
                        'id_usuario'  => '0922290697',
                        'ip_usuario'  => '1',
                        'descripcion' => 'PROC_MAS',
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->comprobante,
                        'dato_ant2'   => $orden->fecha_envio,
                        'dato2'       => $venta->fecha,
                        'dato_ant3'   => $orden_new->fecha_envio,
                        'dato3'       => $orden_new->comprobante,
                        'dato_ant4'   => '',
                        'dato4'       => '',
                    ]);
                }
                $contador++;
            }
            //dd($orden);
        }
        return "Gracias, Amigo";
    }

    public function reporte_anual(Request $request)
    {

        return view('laboratorio/orden/reporte_anual');
    }
}
