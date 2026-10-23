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
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_rubros;
use Sis_medico\Contable;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Detalle_Anticipo_Proveedores;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Ct_Detalle_Credito_Acreedores;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\LogConfig;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\AfActivo;
use Sis_medico\Ct_Globales;

class NotaCreditoAcreedoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $proveedor = Proveedor::where('estado', '1')->get();
        $anticipo = Ct_Credito_Acreedores::where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(20);
        if ($id_empresa == "0916293723001") {
            //dd($anticipo);
        }
        //dd($anticipo);
        return view('contable/credito_acreedores/index', ['nota_credito' => $anticipo, 'proveedor' => $proveedor, 'empresa' => $empresa]);
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $constraints = [
            'id'                        => $request['id'],
            'id_proveedor'              => $request['nombre_proveedor'],
            'concepto'                  => $request['nombre_concepto'],
            'secuencia'                 => $request['secuencia_nombre'],
            'fecha'                     => $request['fecha_credito'],
            'autorizacion'              => $request['autorizacion_credito'],
            'id_asiento_cabecera'       => $request['id_asiento_cabecera']
        ];
        $compras = $this->doSearchingQuery($constraints, $request);
        $proveedor = Proveedor::where('estado', '1')->get();
        //dd($constraints);
        return view('contable/credito_acreedores/index', ['proveedor' => $proveedor, 'nota_credito' => $compras, 'searchingVals' => $constraints, 'tipo_comprobante' => $tipo_comprobante, 'empresa' => $empresa]);
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
       // $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');
        $cuenta = LogConfig::busqueda('4.1.01.02'); 
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $iva_param = Ct_Configuraciones::where('id_plan', $cuenta)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $comprobante = Ct_Credito_Acreedores::where('id_empresa', $id_empresa)->where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalles_comprobante = Ct_Detalle_Credito_Acreedores::where('id_debito', $comprobante->id)->get();
        //dd($comprobante);
        return view('contable/credito_acreedores/edit', ['tipo_pago' => $tipo_pago, 'comprobante' => $comprobante, 'detalles' => $detalles_comprobante, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }
    public function edit2($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');
        $cuenta = LogConfig::busqueda('4.1.01.02');
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $iva_param = Ct_Configuraciones::where('id_plan', $cuenta)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $comprobante = Ct_Credito_Acreedores::where('id_empresa', $id_empresa)->where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalles_comprobante = Ct_Detalle_Credito_Acreedores::where('id_debito', $comprobante->id)->get();
        //dd($comprobante);
        return view('contable/credito_acreedores/edit2', ['tipo_pago' => $tipo_pago, 'comprobante' => $comprobante, 'detalles' => $detalles_comprobante, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = Ct_Credito_Acreedores::where('id_empresa', $id_empresa)->where('estado', '1');
        //dd($query->get());

        $fields = array_keys($constraints);
        $index = 0;
        if ($request->id != null) {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], $constraint);
                }
                $index++;
            }
        } else {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                $index++;
            }
        }

        return $query->paginate(10);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');
        $cuenta = LogConfig::busqueda('4.1.01.02');
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $iva_param = Ct_Configuraciones::where('id_plan', $cuenta)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $compraid = Ct_compras::where('id_empresa', $id_empresa)->where('valor_contable', '>', '0')->get();

        $rubros = DB::table('ct_rubros')->where('estado', '1')->get();

        $empresa_general = Empresa::all();
        //dd($tipo_pago);
        return view('contable/credito_acreedores/create', ['tipo_pago' => $tipo_pago, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'compraid' => $compraid, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'rubros' => $rubros]);
    }
    public function bancos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOTACREDITO_BANCO_PACIFICO');
        //$cuentaPlan = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CREARCOMPROBANTE -1.01.01.1.01-');
        $id_plan_config = LogConfig::busqueda('1.01.01.02.01');
        $desc_cuenta  = Plan_Cuentas::where('id', $id_plan_config)->first();
        $id_plan_configS = LogConfig::busqueda('1.01.01.01.01');
        $desc_cuentaS  = Plan_Cuentas::where('id', $id_plan_config)->first();
        if ($request['opciones'] != '1') {
            $banco =  Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
                ->where('pe.id_empresa', $id_empresa)
                ->where('id_padre', $id_plan_config->id)
                ->where('pe.estado', '2')
                ->select('plan_cuentas.*')
                ->get();
            return $banco;
        } else {
            $caja = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
                ->where('pe.id_empresa', $id_empresa)
                ->where('id_padre', $desc_cuentaS->id)
                ->where('pe.estado', '2')
                ->select('plan_cuentas.*')
                ->get();
            return $caja;
        }
        return ['value' => 'error'];
    }
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $consulta_anticipo = null;
        $saldo_redondeado = 0;
        $objeto_validar = new Validate_Decimals();
        $consulta_facturas = 0;
        $total = 0;
        $input_actualiza = null;
        //dd($request->all());
        $contador_ctv = DB::table('ct_credito_acreedores')->get()->count();
        $numero_factura = 0;
        DB::beginTransaction();

        try {


            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_credito_acreedores')->max('id');

                if (($max_id >= 1) && ($max_id < 10)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if (($max_id >= 10) && ($max_id < 100)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if (($max_id >= 100) && ($max_id < 1000)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if ($max_id == 1000) {
                    $numero_factura = $max_id;
                }
            }
            $cabeceraa = [
                'observacion'                   => 'NOTA DE CRÉDITO : ' . $request['concepto'] . 'A:' . $request['id_proveedor'],
                'fecha_asiento'                 => $request['fecha_hoy'],
                'fact_numero'                   => $numero_factura,
                'valor'                         => $request['total'],
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                //'estado_manual'                 => 4,
            ];
            $consulta_compra = Ct_compras::where('id', $request['nro_factura'])
                ->where('id_empresa', $id_empresa)
                ->first();
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
            //dd($id_asiento_cabecera);
            $input = [
                'asiento'                       => $request['asiento'],
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_empresa'                    => $id_empresa,
                'id_compra'                     => $request['id_comp'],
                'fecha'                         => $request['fecha_hoy'],
                'fecha_caducidad'               => $request['fecha_caducidad'],
                'id_proveedor'                  => $request['id_proveedor'],
                'autorizacion'                  => $request['autorizacion'],
                'serie'                         => $request['serie'],
                'secuencia'                     => $request['secuencia'],
                'nro_comprobante'               => $consulta_compra->numero,
                'id_rubro'                      => $request['id_codigo'],
                'fechand'                       => $request['fechand'],
                'concepto'                      => $request['concepto'],
                'id_credito_tributario'         => $request['credito_tributario'],
                'id_tipo_comprobante'           => $request['tipo_comprobante'],
                'fecha_factura'                 => $request['fecha_factura'],
                'serie_factura'                 => $request['serie_factura'],
                'autorizacion_factura'          => $request['autorizacion_factura'],
                'subtotal'                      => $request['subtotal'],
                'subtotal_12'                   => $request['subtotal12'],
                'subtotal_0'                    => $request['subtotal0'],
                'impuesto'                      => $request['impuesto'],
                'valor_contable'                => $request['total'],
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,

            ];
            $id_debito = Ct_Credito_Acreedores::insertGetId($input);
            $valr = 0;
            if ($request['contador'] != null) {
                $primerarray = array();
                for ($i = 0; $i < $request['contador']; $i++) {
                    if ($request['visibilidad' . $i] == 1) {
                        $valr += $request['valor' . $i];
                        $consulta_rubro = Ct_rubros::where('codigo', $request['id_codigo' . $i])->first();

                        //dd($request['contador']);
                        if ($consulta_rubro != '[]' || $consulta_rubro != null) {
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
                            /*    
                          
                            Ct_Asientos_Detalle::create([
                                           
                                'id_asiento_cabecera'           => $id_asiento_cabecera,
                                'id_plan_cuenta'                => $consulta_rubro->debe,
                                'descripcion'                   => $consulta_rubro->nombre,
                                'fecha'                         => $request['fecha_hoy'],
                                'debe'                          => $request['valor'.$i],
                                'haber'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]); */
                            Ct_Detalle_Credito_Acreedores::create([
                                'id_debito'                     => $id_debito,
                                'codigo'                        => $request['id_codigo' . $i],
                                'nombre'                        => $request['detalle_rubro' . $i],
                                'concepto'                      => $request['detalle_rubro' . $i],
                                'fecha'                         => $request['fecha' . $i],
                                'vencimiento'                   => $request['vencimiento' . $i],
                                'valor'                         => $request['valor' . $i],
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        }
                    }
                }
                $pros = Proveedor::find($consulta_compra->proveedor);
                $cuenta_proveedor = $pros->id_cuentas;

                $plan_cuenta = Plan_Cuentas::find($cuenta_proveedor);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'id_plan_cuenta'                => $cuenta_proveedor,
                    'descripcion'                   => $plan_cuenta->nombre,
                    'fecha'                         => $request['fecha_hoy'],
                    'debe'                           => $request['total'],
                    'haber'                          => '0',
                    'estado'                        => '1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ]);
                //descuentos en compras cambio febrero 2021
                //$plan_cuentas2= Plan_Cuentas::where('id','4.1.08')->first();
                //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOTACREDITO_DESC_COMRAS');

                $id_plan_config = LogConfig::busqueda('2.01.10.01.01');
                $desc_cuenta  = Plan_Cuentas::where('id', $id_plan_config)->first();
                //dd($plan_cuentas2);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'id_plan_cuenta'                => $desc_cuenta->id,
                    'descripcion'                   => $desc_cuenta->nombre,
                    'fecha'                         => $request['fecha_hoy'],
                    'haber'                         => $request['total'],
                    'debe'                          => '0',
                    'estado'                        => '1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ]);
                if ($request['total'] > 0) {
                    if ($request['total'] > ($consulta_compra->valor_contable)) {
                        $nuevo_saldo = $request['total'] - $consulta_compra->valor_contable;
                    } else {
                        $nuevo_saldo = $consulta_compra->valor_contable - $request['total'];
                    }

                    $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                    $input_actualiza = null;
                    if ($nuevo_saldof != 0) {
                        $input_actualiza = [
                            'estado'                        => '2', //poner otro estado para que no salga en las consultas
                            'valor_contable'                => $nuevo_saldof,
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariomod'                 => $idusuario,
                        ];
                    } else {
                        $input_actualiza = [
                            'estado'                        => '3', //poner otro estado para que no salga en las consultas
                            'valor_contable'                => $nuevo_saldof,
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariomod'                 => $idusuario,
                        ];
                    }
                    $consulta_compra->update($input_actualiza);
                }

                DB::commit();
                return $id_debito;
            } else {
                return 'error vacios';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }



        return 'error no guardo nada';
    }


    public function _anular(Request $request)
    {
        $id = $request->id;
        if (!is_null($id)) {
            $comp_ingreso = Ct_Credito_Acreedores::where('estado', '1')->where('id', $id)->first();
            //dd($comp_ingreso);
            if (!is_null($comp_ingreso)) {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $id_empresa = $request->session()->get('id_empresa');
                $idusuario  = Auth::user()->id;
                $fact_compra = Ct_compras::find($comp_ingreso->id_compra);
                if (!is_null($fact_compra)) {
                    $valor = $fact_compra->valor_contable;
                    $fact_compra->estado = 1;
                    $fact_compra->id_usuariomod = $idusuario;
                    $fact_compra->valor_contable =  $valor + $comp_ingreso->valor_contable;
                    $fact_compra->save();
                    Contable::recovery_price($comp_ingreso->id_compra, 'C');
                }
                if (!is_null($comp_ingreso)) {
                    $input = [
                        'estado' => '0',
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $comp_ingreso->update($input);
                    // ahora actualizo el valor y le sumo lo que ya le había restado
                    $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                    $asiento->estado = 1;
                    $asiento->save();
                    $detalles = $asiento->detalles;
                    $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                        'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comp_ingreso->secuencia,
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
                    LogAsiento::anulacion("ND-A", $id_asiento, $asiento->id);
                    //  Log_Contable::create([
                    //     'tipo'           => 'ANC',
                    //     'valor_ant'      => $asiento->valor,
                    //     'valor'          => $asiento->valor,
                    //     'id_usuariocrea' => $idusuario,
                    //     'id_usuariomod'  => $idusuario,
                    //     'observacion'    => $asiento->concepto,
                    //     'id_ant'         => $asiento->id,
                    //     'id_referencia'  => $id_asiento,
                    // ]);
                }
            }
            return redirect()->route('creditoacreedores.index');
        } else {
            return 'error';
        }
    }

    public function anular(Request $request){
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        $nota_credito  = Ct_Credito_Acreedores::find($request->id);
        if($nota_credito->estado == 0){
            return ["status"=>'error', 'msj'=>"Ya se encuentra anulada la N/C"];
        }

        DB::beginTransaction();

        try{
            //Devolver el valor contable de la factura de compra
            $compra = Ct_Compras::find($nota_credito->id_compra);
            $compra->valor_contable = $compra->valor_contable + $nota_credito->valor_contable;
            $compra->save();

            //Anular la nota de credito
            $nota_credito->estado = 0;
            $nota_credito->id_usuariomod = $id_usuario;
            $nota_credito->ip_modificacion = $ip_cliente;
            $nota_credito->save();

            $data = LogAsiento::anularAsiento($nota_credito->id_asiento_cabecera, "N/C AC", "ANULACIÓN {$request->observacion}");
            if($data["status"] == "error"){
                DB::rollback();
                return ["status"=>'error', 'msj'=>"No se pudo anular la Nota Credito" , "exp"=> $data["exp"]];
            }

            DB::commit();
            return ["status"=>'success', 'msj'=>"Anulado con Exito", "asiento"=>$data["asiento"]];


        }catch(\Exception $e){
            DB::rollback();
            return ["status"=>'error', 'msj'=>"No se pudo anular la Nota Credito", "exp"=> $e->getMessage() , "mod"=>"NC-Controller"];
        }
        
    }

    public function pdf_comprobante($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $anticipo    = Ct_Cruce_Valores::find($id);
        $tipo_pago = Ct_Tipo_Pago::where('id', $anticipo->id_tipo_pago)->first();
        $empresa = Empresa::where('id', $anticipo->id_empresa)->first();
        $proveedor = Proveedor::where('id', $anticipo->id_proveedor)->first();
        $vistaurl = "contable.anticipos.pdf_comprobante_anticipo";
        $usuario_crea = DB::table('users')->where('id', $anticipo->id_usuariocrea)->first();
        $view     = \View::make($vistaurl, compact('emp', 'proveedor', 'empresa', 'anticipo', 'tipo_pago', 'usuario_crea'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }


    public function template()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('contable.plantilla.app-template');
    }

    /************************************************************/
    /****************BUZQUEDA DE FACTURAS DE COMPRA**************
    /***********************************************************/
    public function obtener_num_fact(Request $request)
    {

        $num = $request['term'];
        $data      = array();

        $id_empresa = $request->session()->get('id_empresa');

        $fact_compra = Ct_compras::where('id', $num)
            ->whereRaw('(estado = 1 OR estado = 2)')
            ->where('valor_contable', '>', '0')
            ->where('id_empresa', $id_empresa)
            ->get();


        foreach ($fact_compra as $fact_compra) {

            $num_comprobante = $fact_compra->numero;
            $variable = explode("-", $num_comprobante);
            $sucursal = 0;
            $punto_emision = 0;
            $serie = 0;
            $secuencia = 0;
            $nombre_proveedor = '';

            if (!is_null($fact_compra->proveedorf)) {
                $nombre_proveedor = $fact_compra->proveedorf->nombrecomercial;
            }

            if (count($variable) > 0) {

                $sucursal = $variable[0];
                $punto_emision = $variable[1];
                $serie = $variable[0] . '-' . $variable[1];
                $secuencia = $variable[2];
            }

            $data[] = array('value' => $fact_compra->id, 'num_asiento' => $fact_compra->id_asiento_cabecera, 'id_compra' => $num, 'serie' => $serie, 'autorizacion' => $fact_compra->autorizacion, 'secuencia' => $secuencia, 'nomb_proveedor' => $nombre_proveedor, 'id_proveedor' =>  $fact_compra->proveedorf->id, 'val_contable' =>  $fact_compra->valor_contable);
        }

        //dd($fact_venta);

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function newNotaCredito(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');

        //dd(date("H:i:s"));

        //VENTA_TARIFA_12 -4.1.01.02-
        $cuenta = LogConfig::busqueda('4.1.01.02');
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();

        $iva_param = Ct_Configuraciones::where('id_plan', $cuenta)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $compraid = Ct_compras::where('id_empresa', $id_empresa)->where('valor_contable', '>', '0')->get();

        $rubros = DB::table('ct_rubros')->where('estado', '1')->get();

        $empresa_general = Empresa::all();
        //dd($tipo_pago);
        return view('contable/credito_acreedores/new_create', ['tipo_pago' => $tipo_pago, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'compraid' => $compraid, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'rubros' => $rubros]);
    }

    public function buscarFacturas(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $compras = Ct_compras::where('id_empresa', $id_empresa)->where('proveedor', $request->id_proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', 0)->get();
        $option = "";
        
        foreach ($compras as $value) {
            $option .= "<option value='{$value->id}'> {$value->numero} | {$value->observacion}</option>";
        }
        if ($option == "") {
            $option = "<option>NO SE ENCONTRO FACTURAS</option>";
        } else {
            $option = "<option>SELECCIONE FACTURA...</option>" . $option;
        }

        return ['option' => $option];
    }

    public function buscarDetalleFacturas(Request $request)
    {
        //dd($request->all());
        $cab = Ct_Compras::find($request->id_compra);
        $data = NotaCreditoAcreedoresController::detalleFactura($cab);

        return ['data' => $data, 'compra' => $cab];
    }

    public function detalleFactura($compra)
    {
        $table = "";
        $idx = 0;
        foreach ($compra->detalles as $value) {
        //    for ($i = 0; $i < $value->cantidad; $i++) {
                $precio = number_format($value->precio, 2);
                $extendido = $value->extendido;
                $valor_iva = 0;
                $descuento = $value->descuento;
                $ivaEstado = 0;
                $checked = 'disabled';

                if ($value->iva == 1) {
                   // dd($extendido, $value->porcentaje);
                    $valor_iva = ($extendido * $value->porcentaje); // valor unitario del iva del producto
                    $checked = 'checked';

                }

                $totalProducto = $valor_iva + $extendido;
                
                $valor_iva = number_format($valor_iva, 2);
                $extendido = number_format($extendido, 2);

                $table .= " <tr class='datos'>
                                <input name='porcentaje[]' class='porcentaje'type='hidden' value='{$value->porcentaje}'>
                                <input name='id_producto[]' id='ivaEstado' type='hidden' value='{$value->id}'>
                                <input name='iva[]' class='iva ivaProducto{$idx}' type='hidden' value='{$value->iva}'>
                                <input name='iva_producto[]' class='iva_producto' type='hidden' value='{$valor_iva}'>
                                <input name='verificar[]' class='verificar checkProducto' id='verificar{$idx}' type='hidden' value='0'>
                                <input name='descuento[]' class='descuento' type='hidden'  value='{$descuento}'>
                                <input name='totalProducto[]' class='totalProducto' type='hidden'  value='{$totalProducto}'>
                                <input name='codigoProducto[]' class='codigoProducto' type='hidden'  value='{$value->codigo}'>
                                <input name='nombreProducto[]' class='nombreProducto' type='hidden'  value='{$value->nombre}'>

                                <!--td style='text-align:center;'>
                                    <input name='verificarx[]' class='checks' id='check{$idx}' onclick='checkProducto({$idx})' type='checkbox'> 
                                </td-->
                                <td>{$value->nombre} <textarea name='nota_producto[]' class='form-control' style='width: 94%;'></textarea></td>
                                <td style='text-align:center;'>{$value->cantidad}</td>
                                <td style='text-align:center;'>{$precio}</td>
                                <td style='text-align:center;'>{$descuento}</td>
                                <td style='text-align:center;'>{$valor_iva}</td>
                                <td style='text-align:center;'>{$extendido}</td>
                                <td> <input onchange='sumaGlobal();' class='abonos' onkeypress='return isNumberKey(event)' onblur='decimal(this)' type='text' name='abonos[]' value='0.00'></td>
                                <td style='text-align:center;'>
                                    <input onclick='cambiosEstadoIva({$idx})' type='checkbox' name='checkIva' id='checkIva{$idx}' {$checked}>
                                </td>
                            </tr>";
                $idx++;
         //   }
        }
        return ["table" => $table];
    }

    public function newCreditoStore(Request $request)
    {
        $validate = 0;
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        foreach ($request['abonos'] as $value) {
            if ($value > 0) {
                $validate = 1;
            }
        }

        if ($validate == 0) {
            return ['status' => 'error', 'msj' => 'No se encontro ningun abono...', 'exp' => ''];
        }

        $compra = Ct_compras::find($request->nro_factura);

        if($request['total'] > $compra->valor_contable){
            return ['status' => 'error', 'msj' => 'El valor de la Nota de credito no debe ser mayor al valor contable de la factura', 'exp'=>'', 'mod' => ''];
        }

       
        $nota = ['status' => 'error', 'msj' => 'Ocurrio un error', 'exp' => "No se proceso nada", 'mod' => ''];
       
        DB::beginTransaction();

        try {
           
            if (!is_null($compra)) {

                $request['id_comp'] = $compra->id;
                $numero_factura = LogAsiento::getSecuencia(7);

                $request['secuencia_nota_credito'] = $numero_factura;
                
                $cabecera = [
                    'observacion'                   => "NOTA DE CRÉDITO : {$request['concepto']}",
                    'fecha_asiento'                 => $request['fechand'],
                    'fact_numero'                   => $numero_factura,
                    'valor'                         => $request['total'],
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $id_usuario,
                    'id_usuariomod'                 => $id_usuario,
                ];
                $request['asiento'] = Ct_Asientos_Cabecera::insertGetId($cabecera);
                //dd($request['asiento']);

                $input = [
                    'id_asiento_cabecera'           => $request['asiento'],
                    'id_empresa'                    => $id_empresa,
                    'id_compra'                     => $compra->id,
                    'fecha'                         => $request['fecha_hoy'],
                    'fecha_caducidad'               => $request['fecha_caducidad'],
                    'id_proveedor'                  => $compra->proveedor,
                    'autorizacion'                  => $request['autorizacion'],
                    'serie'                         => $request['serie'],
                    'secuencia'                     => $request['secuencia'],
                    'nro_comprobante'               => $numero_factura,
                    //'id_rubro'                      => $request['id_codigo'],
                    'fechand'                       => $request['fechand'],
                    'concepto'                      => $request['concepto'],
                    'id_credito_tributario'         => $request['credito_tributario'],
                    'id_tipo_comprobante'           => $request['tipo_comprobante'],
                    'fecha_factura'                 => $compra->fecha,
                    'serie_factura'                 => $compra->serie,
                    'autorizacion_factura'          => $compra->autorizacion,
                    'secuencia_factura'             => $compra->numero,
                    'subtotal'                      => $request['subtotal'],
                    'subtotal_12'                   => $request['subtotal12'],
                    'subtotal_0'                    => $request['subtotal0'],
                    'impuesto'                      => $request['impuesto'],
                    'valor_contable'                => $request['total'],
                    'saldo_ant'                     => $compra->valor_contable,
                    'nota'                          => $request['nota'],
                    'id_usuariocrea'                => $id_usuario,
                    'id_usuariomod'                 => $id_usuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,

                ];
                //dd($input);
                
                $request['id_credito'] = Ct_Credito_Acreedores::insertGetId($input);

                if ($compra->tipo == 1) { //************ Es una factura de producto************

                    $nota = NotaCreditoAcreedoresController::notaCreditoFactProducto($request);

                } else if ($compra->tipo == 2) {

                    $factura_activo = AfFacturaActivoCabecera::where('id_asiento', $compra->id_asiento_cabecera)->first();

                    if (!is_null($factura_activo)) { //*********Entra porque es activo fijo************

                        $request['fact_activo'] = $factura_activo->id;
                        $nota = NotaCreditoAcreedoresController::notaCreditoActivo($request);
                     
                    } else { //************Es una facrtura contable************

                        $nota = NotaCreditoAcreedoresController::notaCreditoFactContable($request);

                    }
                }

                if ($nota['status'] == 'success') {
                    for($i = 0; $i < count($request->abonos); $i++){
                        if($request['abonos'][$i] > 0){
                            $valor_iva = $request['abonos'][$i] * $request['porcentaje'][$i];
                            $details = [
                                'id_debito'         => $request['id_credito'],
                                'codigo'            => $request['codigoProducto'][$i],
                                'nombre'            => $request['nombreProducto'][$i],
                                'fecha'             => $request['fechand'],
                                'vencimiento'       => $request['fecha_caducidad'],
                                'valor'             => $request['abonos'][$i],
                                'concepto'          => $request['nota_producto'][$i],
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'iva'               => $request['iva'][$i],
                                'valor_iva'         => $valor_iva,
                                'total'             => $valor_iva + $request['abonos'][$i],
                                'id_det_compra'     => $request['id_producto'][$i]
                            ];

                            Ct_Detalle_Credito_Acreedores::create($details);
                        }
                    }

                    if($validate == 1){
                        //dd($compra);
                        $compra->valor_contable = $compra->valor_contable - $request['total'];
                        $compra->save(); 

                    }

                    DB::commit();
                    return ['status' => 'success', 'msj' => 'Guardado con exito', 'exp' => 'Sin errores', 'id_asiento'=> $request['asiento'],'id'=> $request['id_credito'], 'secuencia'=> $request['secuencia_nota_credito']];
                }else{
                    DB::rollback();
                    return ['status' => $nota['status'], 'msj' => $nota['msj'], 'exp' => $nota['exp'], 'mod'=>$nota['mod'] ];
                }

            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msj' => 'Ocurrio un error', 'exp' => $e->getMessage(), 'mod' => 'Principal'];
        }

        // dd($compra->tipo, $request->all());
    }

    public static function notaCreditoActivo($request)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_empresa = $request->session()->get('id_empresa');

       // DB::beginTransaction();
        try {

            for ($i = 0; $i < count($request->abonos); $i++) {
                if ($request['abonos'][$i] > 0) {
                    //$id_producto = $request['id_producto'][$i];
                    $det_compra = Ct_detalle_compra::find($request['id_producto'][$i]);
                    $codigo = $det_compra->codigo;

                    $facturaActivo = $det_compra->cabecera->facturaActivo;


                    //dd("Holis");
                    foreach ($facturaActivo->detalles as $det_activo) {
                        //  dd($det_activo->activo_id, $det_activo->codigo ,$codigo);

                        if ($det_activo->codigo == "TRANS" and $det_activo->codigo == $codigo) { //Si es transporte
                            //dd("aqui");
                            $id_plan_confg = LogConfig::busqueda("5.2.02.03.06");
                            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
                            //dd($plan_cuentas);

                            $data = [
                                'id_asiento_cabecera' => $request['asiento'],
                                'id_plan_cuenta'      => $plan_cuentas->id,
                                'debe'                => 0,
                                'haber'               => $request['abonos'][$i],
                                'descripcion'         => $plan_cuentas->nombre,
                                'fecha'               => $request['fechand'],
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                                'id_usuariocrea'      => $id_usuario,
                                'id_usuariomod'       => $id_usuario,
                            ];
                            //dd($data);
                            Ct_Asientos_Detalle::create($data);
                            break;
                        } else if (!is_null($det_activo->activo_id) and $det_activo->codigo == $codigo) { //si es activo baja el valor del costo 
                            //dd("aqui2");
                            $afActivo = AfActivo::where('codigo', $det_activo->codigo)->where('estado', 1)->where('empresa', $id_empresa)->first();
                            //  dd($afActivo);
                            if (!is_null($afActivo)) {
                                //dd($afActivo->tipo);
                                if (isset($afActivo->tipo)) {

                                    $plan_cuentas = Plan_Cuentas::find($afActivo->tipo->cuentamayor);
                                    $data = [
                                        'id_asiento_cabecera' => $request['asiento'],
                                        'id_plan_cuenta'      => $plan_cuentas->id,
                                        'debe'                => 0,
                                        'haber'               => $request['abonos'][$i],
                                        'descripcion'         => $plan_cuentas->nombre,
                                        'fecha'               => $request['fechand'],
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                        'id_usuariocrea'      => $id_usuario,
                                        'id_usuariomod'       => $id_usuario,
                                    ];
                                    // array_push($data, $input);
                                    //dd($data);
                                    Ct_Asientos_Detalle::create($data);
                                    //dd($afActivo);
                                    $afActivo->costo = $afActivo->costo - $request['abonos'][$i];
                                    $afActivo->save();
                                    break;
                                }
                            }
                        } else if (is_null($det_activo->activo_id) and $det_activo->codigo == $codigo) { // es una cuenta contable
                            // dd("aqui3");
                            // $id_plan_confg = LogConfig::busqueda($det_activo->codigo);

                            $plan_gasto = Plan_Cuentas_Empresa::where('id_plan', $det_activo->codigo)->orwhere('plan', $det_activo->codigo)->where('id_empresa', $id_empresa)->first();
                            //dd($plan_gasto);
                            $plan_cuentas = Plan_Cuentas::find($plan_gasto->id_plan);
                            //dd($plan_cuentas);
                            $data = [
                                'id_asiento_cabecera' => $request['asiento'],
                                'id_plan_cuenta'      => $plan_cuentas->id,
                                'debe'                => 0,
                                'haber'               => $request['abonos'][$i],
                                'descripcion'         => $plan_cuentas->nombre,
                                'fecha'               => $request['fechand'],
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                                'id_usuariocrea'      => $id_usuario,
                                'id_usuariomod'       => $id_usuario,
                            ];
                            Ct_Asientos_Detalle::create($data);


                            break;
                        }
                    }
                }
            }
            /// Proveedores locales
            $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
           
            $plan_cuenta = Plan_Cuentas::find($id_plan_confg);
            //dd($plan_cuenta);
            if (Auth::user()->id == "0957258056") {
               // dd($plan_cuenta);
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $request['asiento'],
                'id_plan_cuenta'      => $plan_cuenta->id,
                'haber'               => 0,
                'debe'                => $request['total'],
                'descripcion'         => $plan_cuenta->nombre,
                'fecha'               => $request['fechand'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);


            if ($request['impuesto'] > 0) { //Asiento si tiuene impuesto
                $globales = Ct_Globales::where('id_modulo', 2)->where('id_empresa', $id_empresa)->first();
               
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $request['asiento'],
                    'id_plan_cuenta'      => $globales->debe,
                    'debe'               => 0,
                    'haber'                => $request['impuesto'],
                    'descripcion'         => $globales->debec->nombre,
                    'fecha'               => $request['fechand'],
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                ]);
            }
         //   DB::commit();

            return ['status' => 'success', 'msj' => 'Guardado con exito', 'exp' => ''];
        } catch (\Exception $e) {
           // DB::rollback();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage(), 'mod'=>'creditoActivo'];
        }
    }

    public static function notaCreditoFactContable($request)
    {
        //Se le hace el reverso de las cuentas que se creo
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_empresa = $request->session()->get('id_empresa');
        //DB::beginTransaction();
        //  dd($request->all());
        try {
            for ($i = 0; $i < count($request->abonos); $i++) {
                if ($request['abonos'][$i] > 0) {
                    $det_compra = Ct_detalle_compra::find($request['id_producto'][$i]);
                    $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', "LIKE", "%{$det_compra->codigo}%")->orWhere('plan', "LIKE", "%{$det_compra->codigo}%")->where('id_empresa', $id_empresa)->first();


                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $request['asiento'],
                        'id_plan_cuenta'      => $plan_empresa->id_plan,
                        'debe'                => 0,
                        'haber'               => $request['abonos'][$i],
                        'descripcion'         => $plan_empresa->nombre,
                        'fecha'               => $request['fechand'],
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                    ]);
                }
            }
            /// Proveedores locales
            $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
            $plan_cuenta = Plan_Cuentas::find($id_plan_confg);

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $request['asiento'],
                'id_plan_cuenta'      => $plan_cuenta->id,
                'haber'               => 0,
                'debe'                => $request['total'],
                'descripcion'         => $plan_cuenta->nombre,
                'fecha'               => $request['fechand'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
            //dd($)
            if ($request['impuesto'] > 0) { //Asiento si tiuene impuesto
                $globales  = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $request['asiento'],
                    'id_plan_cuenta'      => $globales->debe,
                    'debe'               => 0,
                    'haber'                => $request['impuesto'],
                    'descripcion'         => $globales->debec->nombre,
                    'fecha'               => $request['fechand'],
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                ]);
            }
           // DB::commit();

            return ['status' => 'success', 'msj' => 'Guardado con exito', 'exp' => ''];
        } catch (\Exception $e) {
            //DB::rollback();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage(), 'mod'=> 'FacContable'];
        }
    }

    public static function notaCreditoFactProducto($request)
    {
        //asientos  inventario mercaderia ---- Proveedores Locales

      //  dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_empresa = $request->session()->get('id_empresa');
        //DB::beginTransaction();
        //  dd($request->all());
        try {
        // for ($i = 0; $i < count($request->abonos); $i++) {
        //     if ($request['abonos'][$i] > 0) {
                // $det_compra = Ct_detalle_compra::find($request['id_producto'][$i]);
                // $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', "LIKE", "%{$det_compra->codigo}%")->orWhere('plan', "LIKE", "%{$det_compra->codigo}%")->where('id_empresa', $id_empresa)->first();
            if($request['subtotal'] > 0){
                $id_plan_confg = LogConfig::busqueda('1.01.03.01.02');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
               // dd($plan_cuentas);
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $request['asiento'],
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'debe'                => 0,
                    'haber'               => $request['subtotal'],
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $request['fechand'],
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                ]);
            }
              

        //         break;
        //     }
        // }

        $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
        $plan_cuenta = Plan_Cuentas::find($id_plan_confg);
            //dd($plan_cuenta);
        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $request['asiento'],
            'id_plan_cuenta'      => $plan_cuenta->id,
            'haber'               => 0,
            'debe'                => $request['total'],
            'descripcion'         => $plan_cuenta->nombre,
            'fecha'               => $request['fechand'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $id_usuario,
            'id_usuariomod'       => $id_usuario,
        ]);
        //dd($)
        if ($request['impuesto'] > 0) { //Asiento si tiuene impuesto
            $globales  = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
            //dd($globales);
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $request['asiento'],
                'id_plan_cuenta'      => $globales->debe,
                'debe'               => 0,
                'haber'                => $request['impuesto'],
                'descripcion'         => $globales->debec->nombre,
                'fecha'               => $request['fechand'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
        }

          //    DB::commit();

            return ['status' => 'success', 'msj' => 'Guardado con exito', 'exp' => ''];
        } catch (\Exception $e) {
         //   DB::rollback();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage(), 'mod'=>'FactProducto'];
        }
    }
}
