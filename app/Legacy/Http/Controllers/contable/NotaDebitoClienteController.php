<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_acreedores;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_rubros;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Pais;
use Sis_medico\Ct_Clientes;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;
use Exception;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Debito_Acreedores;
use Sis_medico\Ct_Detalle_Debito_Clientes;
use Sis_medico\Ct_Detalle_Rubro_Acreedores;
use Sis_medico\Ct_Rubros_Cliente;
use Sis_medico\Ct_ventas;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\LogConfig;

class NotaDebitoClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $anticipo = Ct_Nota_Debito_Cliente::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        return view('contable/nota_debito_cliente/index', ['comp_egreso' => $anticipo, 'clientes' => $clientes, 'empresa' => $empresa]);
    }

    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        //$iva_param = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        //$iva_param = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva_param = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        //dd($tipo_pago);
        return view('contable/nota_debito_cliente/create', ['tipo_pago' => $tipo_pago, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'min_date' => $min_date]);
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        //$iva_param = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        //$iva_param = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva_param = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $comprobante = Ct_Nota_Debito_Cliente::where('id_empresa', $id_empresa)->where('id', $id)->first();
     
        $detalles_comprobante = Ct_Detalle_Debito_Clientes::where('id_debito', $comprobante->id)->get();

        //dd($tipo_pago);
        return view('contable/nota_debito_cliente/edit', ['tipo_pago' => $tipo_pago, 'comprobante' => $comprobante, 'detalles' => $detalles_comprobante, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario,  'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id' => $request['id'],
            'id_cliente' => $request['id_cliente'],
            'concepto' => $request['concepto'],
            'observaciones' => $request['observaciones'],
            'fecha'        => $request['fecha'],
        ];

        //dd($constraints);
        $acreedores = $this->doSearchingQuery($constraints);
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/nota_debito_cliente/index', ['comp_egreso' => $acreedores, 'searchingVals' => $constraints, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Nota_Debito_Cliente::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(20);
    }
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $objeto_validar = new Validate_Decimals();
        $fechahoy = $request['fecha_hoy'];
        //$iva_param = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        //$iva_param = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva_param = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();
        $ivaf = $iva_param->iva;
        //$total_final = $objeto_validar->set_round($request['total_suma']);
        $total_final = $objeto_validar->set_round($request['total']);
        $id_factura = $request['facturan'];
        $verificar = Ct_Nota_Debito_Cliente::where('id', $id_factura)->first();
        DB::beginTransaction();
        try {
            if (is_null($verificar)) {
                if (!is_null($request['contador'])) {
                    $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
                    if ($verifica_cierre->status == 'error') {
                        return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                    }
                    $id_empresa = $request->session()->get('id_empresa');
                    //$contador_ctv = DB::table('ct_debito_clientes')->where('id_empresa', $id_empresa)->get()->count();
                    $numero_factura = LogAsiento::getSecuencia(5, 2);
                    //$numero_factura = 0;
                    // if ($contador_ctv == 0) {
                    //     $num = '1';
                    //     $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                    // } else {

                    //     //Obtener Ultimo Registro de la Tabla ct_compras
                    //     $max_id = DB::table('ct_debito_clientes')->max('id');

                    //     if (($max_id >= 1) && ($max_id < 10)) {
                    //         $nu = $max_id + 1;
                    //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                    //     }

                    //     if (($max_id >= 10) && ($max_id < 99)) {
                    //         $nu = $max_id + 1;
                    //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                    //     }

                    //     if (($max_id >= 100) && ($max_id < 1000)) {
                    //         $nu = $max_id + 1;
                    //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                    //     }

                    //     if ($max_id == 1000) {
                    //         $numero_factura = $max_id;
                    //     }
                    // }
                    $cabeceraa = [
                        'observacion'                   => "Fact # " . $request['facturano'] . " " . $request['concepto'],
                        'fecha_asiento'                 => $fechahoy,
                        'fact_numero'                   => $numero_factura,
                        'valor'                         => $total_final,
                        'id_empresa'                    => $id_empresa,
                        'estado'                        => '1',
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                    $iva = 0;
                    if ($request['impuesto'] > 0) {
                        $iva = 1;
                    }
                    $input = [
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_cliente'                    => $request['id_cliente'],
                        'fecha'                         => $request['fecha_hoy'],
                        'id_factura'                    => $request['facturan'],
                        'concepto'                      => $request['concepto'],
                        'valor_contable'                => $request['total'],
                        'secuencia'                     => $numero_factura,
                        'estado'                        => '1',
                        'iva'                           => $iva,
                        'serie'                         => $request['facturano'],
                        'numero'                        => $request['facturano'],
                        'id_empresa'                    => $id_empresa,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $id_debito = Ct_Nota_Debito_Cliente::insertGetId($input);
                    $valor = 0;
                    $primerarray = array();
                    for ($i = 0; $i <= $request['contador']; $i++) {
                        if ($request['visibilidad' . $i] == '1') {
                            if (!is_null($request['codigo' . $i])) {
                                $valor += $request['valor' . $i];
                                if ($iva == 1) {
                                    $valor += $request['valor' . $i] * $ivaf;
                                }
                                //$s=0;
                                $consulta_rubro = Ct_Rubros_Cliente::where('codigo', $request['codigo' . $i])->first();
                                if (!is_null($consulta_rubro)) {
                                    $segundoarray = [$consulta_rubro->haber, $request['valor' . $i]];
                                    $key = array_search($consulta_rubro->haber, array_column($primerarray, '0'));
                                    if ($key !== false) {
                                        $valor2 =  $primerarray[$key][1];
                                        $valor2 = $valor2 + $request['valor' . $i];
                                        $primerarray[$key][0] = $consulta_rubro->haber;
                                        $primerarray[$key][1] = $valor2;
                                    } else {
                                        array_push($primerarray, $segundoarray);
                                    }

                                    Ct_Detalle_Debito_Clientes::create([
                                        'id_debito'                     => $id_debito,
                                        'codigo'                        => $request['codigo' . $i],
                                        'nombre'                        => $request['rubro' . $i],
                                        'concepto'                      => $request['detalle_rubro' . $i],
                                        'fecha'                         => $request['fecha' . $i],
                                        'vencimiento'                   => $request['vencimiento' . $i],
                                        'valor'                         => $request['valor' . $i],
                                        'id_usuariocrea'                => $idusuario,
                                        'id_usuariomod'                 => $idusuario,
                                        'ip_creacion'                   => $ip_cliente,
                                        'ip_modificacion'               => $ip_cliente,
                                    ]);

                                    $ivafc = 0;
                                    if ($request['valor' . $i] > 0) {
                                        $ivafc = $request['valor' . $i];
                                    }

                                    $factura_venta = [
                                        'sucursal'            => '999',
                                        'punto_emision'       => '001',
                                        'numero'              => $numero_factura,
                                        'nro_comprobante'     => $numero_factura,
                                        'id_asiento'          => $id_asiento_cabecera,
                                        'id_empresa'          => $id_empresa,
                                        'tipo'                => 'N-D',
                                        'fecha'               => $fechahoy,
                                        'concepto'            => $request['concepto'],
                                        'divisas'             => '1',
                                        'nombre_cliente'      => $request['nombre_cliente'],
                                        'tipo_consulta'       => '',
                                        'id_cliente'          => $request['id_cliente'], //nombre_cliente
                                        'direccion_cliente'   => '',
                                        'ruc_id_cliente'      => $request['id_cliente'],
                                        'telefono_cliente'    => '',
                                        'email_cliente'       => '',
                                        'orden_venta'         => $id_debito,
                                        'nro_autorizacion'    => '',
                                        'id_paciente'         => '3333333333',
                                        'nombres_paciente'    => '',
                                        'id_hc_procedimiento' => '',
                                        'seguro_paciente'     => '',
                                        'procedimientos'      => '',
                                        'fecha_procedimiento' => '',
                                        'copago'              => '',
                                        'subtotal_0'          => $request['valor' . $i],
                                        'subtotal_12'         => $ivafc,
                                        'descuento'           => '',
                                        'base_imponible'      => $request['valor' . $i],
                                        'impuesto'            => $request['valor' . $i],
                                        'total_final'         => $request['valor' . $i],
                                        'valor_contable'      => $request['valor' . $i],
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                    ];
                
                                    $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
                                }
                            }
                        }
                    }
                    $debes = 0;
                    $debe = 0;
                    //dd($primerarray);
                    for ($file = 0; $file < count($primerarray); $file++) {
                        $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
                        $cuenta = $primerarray[$file][0];
                        $debe =  number_format($primerarray[$file][1], 2, '.', '');
                        if ($iva == 1) {
                            $debes = $debe * $ivaf;
                            $debe = $debe + $debes;
                        }
    
    
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $cuenta,
                            'descripcion'                   => $cuent_descrip->nombre,
                            'fecha'                         => $fechahoy,
                            'haber'                         => $debe,
                            'debe'                          => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    //creo una factura de venta
                    $plan_cuentas2 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/D_CLIENTE_CXC_CLIENTES_COMERCIALES');
                    if (!isset($plan_cuentas2->cuenta_guardar)) {
                        DB::rollback();
                        return ["status" => "error", "msj" => "No se pudo guardar, falta configurar la cuenta de N/D_CLIENTE_CXC_CLIENTES_COMERCIALES"];
                    }
                    //$plan_cuentas2 = Plan_Cuentas::where('id', '1.01.02.05.01')->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        //'id_plan_cuenta'                => '1.01.02.05.01',
                        'id_plan_cuenta'                => $plan_cuentas2->cuenta_guardar,
                        'descripcion'                   => $plan_cuentas2->nombre_mostrar,
                        'fecha'                         => $request['fecha_hoy'],
                        'debe'                          => $valor,
                        'haber'                         => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                    DB::commit();

                    return ["status" => "success", "msj" => "Guardado con exito", 'id_debito' => $id_debito];
                    // return $id_debito;
                } else {
                    DB::rollback();
                    return ["status" => "error", "msj" => "Campos vacios"];

                    // return response()->json('error vacios');
                }
            } else {
                DB::rollback();
                return ["status" => "error", "msj" => "Ocurrio un error"];

                // return response()->json('error');
            }
        } catch (\Exeption $e) {
            DB::rollBack();
            // return response()->json('error');
            return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
        }
        return 'error no guardo nada';
    }
    public function obtener_cliente(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_factura = $request['id_factura'];
        $secuencia = $request['secuencia'];
        $validacion = $request['validacion'];
        if ($validacion == "0") {
            if (!is_null($id_factura)) {
                $ventas = Ct_ventas::where('id', $id_factura)->where('id_empresa', $id_empresa)->first();
                if (!is_null($ventas)) {
                    return [$ventas->id_cliente, $ventas->cliente->nombre, 1, $ventas->nro_comprobante, $ventas->valor_contable];
                } else {
                    return response()->json('error');
                }
            } else {
                return response()->json('error');
            }
        } else {
            if (!is_null($secuencia)) {
                $ventas = Ct_ventas::where('nro_comprobante', $secuencia)->where('id_empresa', $id_empresa)->first();
                if (!is_null($ventas)) {
                    return [$ventas->id_cliente, $ventas->cliente->nombre, 0, $ventas->id, $ventas->valor_contable];
                } else {
                    return response()->json('error');
                }
            } else {
                return response()->json('error');
            }
        }
        return response()->json('error');
    }
    public function anular($id, Request $request)
    {
        if (!is_null($id)) {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            $nota_debito = Ct_Nota_Debito_Cliente::where('id', $id)->where('id_empresa', $id_empresa)->first();

            if (!is_null($nota_debito)) {
                $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($nota_debito->fecha));
                if ($verifica_cierre->status == 'error') {
                    return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                }

                $input = [
                    'estado' => '0',
                    //'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    // 'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $nota_debito->update($input);

                $ventas = Ct_ventas::where('id_asiento', $nota_debito->id_asiento)->where('id_empresa', $id_empresa)->get();
                // orden_venta

                foreach ($ventas as $venta) {
                    $venta->estado = '0';
                    $venta->ip_modificacion = $ip_cliente;
                    $venta->id_usuariomod = $idusuario;
                    $venta->save();
                }

                // ahora actualizo el valor y le sumo lo que ya le había restado
                $asiento = Ct_Asientos_Cabecera::findorfail($nota_debito->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $nota_debito->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
                foreach ($detalles as $value) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }

                LogAsiento::anulacion("CL-ND", $id_asiento, $nota_debito->id_asiento_cabecera);

                // Log_Contable::create([
                //     'tipo'           => 'NDC',
                //     'valor_ant'      => $asiento->valor,
                //     'valor'          => $asiento->valor,
                //     'id_usuariocrea' => $idusuario,
                //     'id_usuariomod'  => $idusuario,
                //     'observacion'    => $asiento->concepto,
                //     'id_ant'         => $nota_debito->id_asiento_cabecera,
                //     'id_referencia'  => $id_asiento,
                // ]);

                return redirect()->route('nota_cliente_debito.index');
            }
        } else {
            return redirect()->route('nota_cliente_debito.index');
        }
    }
    public function pdf_nota(Request $request, $id)
    {
        $nota_debito = Ct_Nota_Debito_Cliente::where('id', $id)->first();
        $empresa = Empresa::where('id', $nota_debito->id_empresa)->first();
        $detalle_ingreso = Ct_Detalle_Debito_Clientes::where('id_debito', $nota_debito->id)->first();
        //$iva_param = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        //$iva_param  = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva_param = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();

        //la variable convertir con la clase Numeros Letras
        //$total_str = $letras->convertir(number_format($nota_debito->total_ingreso, 2, '.', ''), "DOLARES", "CTVS");
        //dd($factura_contable);
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $nota_debito->id_asiento_cabecera)->first();
        $asiento_detalle = null;
        if ($asiento_cabecera != null) {
            $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        }
        //dd($asiento_detalle);

        if ($nota_debito != '[]') {

            $vistaurl = "contable.nota_debito_cliente.pdf_nota";
            $view     = \View::make($vistaurl, compact('nota_debito', 'empresa', 'iva_param', 'asiento_cabecera', 'asiento_detalle'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('Comprobante de Ingreso' . $id . '.pdf');
        }

        return 'error';
    }
}
