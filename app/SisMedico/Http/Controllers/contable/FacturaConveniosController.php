<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_rubros;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Asientos_Detalle;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Ap_Agrupado;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Convenio;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\TipoProveedor;
use Sis_medico\User;
use Sis_medico\Utilidades;
use Sis_medico\Validate_Decimals;
use Svg\Tag\Rect;

class FacturaConveniosController extends Controller
{
    private $porIva = '';
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function (Request $request, \Closure $next): mixed {
            $this->porIva = Utilidades::porcentajeIva();

            if ($this->porIva === null) {
                return response()->view('errors.204', [
                    'mensajes' => 'No se ha configurado el porcentaje del iva',
                ]);
            }

            return $next($request);
        });
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
        //dd($empresa);
        $constraints = null;
        $proveedores = DB::table('ct_acreedores')->join('tipoproveedor', 'ct_acreedores.id_tipoproveedor', '=', 'tipoproveedor.id')->select('ct_acreedores.*', 'tipoproveedor.nombre')->paginate('5');
        $ventas = Ct_ventas::where('tipo', 'VEN-CONVENIO')->orderBy('id', 'desc')->paginate(10);
        if (!is_null($request['id_cliente']) || !is_null($request['id']) || !is_null($request['fecha']) || !is_null($request['id_asiento']) || !is_null($request['nro_comprobante'])) {
            $constraints = [
                'id_cliente' => $request['id_cliente'],
                'id' => $request['id'],
                'fecha' => $request['fecha'],
                'id_asiento' => $request['id_asiento'],
                'nro_comprobante' => $request['nro_comprobante'],
            ];
            $ventas = $this->doSearchingQuery($constraints, $request);
        }
        $clientes = Ct_Clientes::where('estado', '1')->get();
        return view('contable/convenios/index', ['compras' => $proveedores, 'searchingVals' => $constraints, 'clientes' => $clientes, 'empresa' => $empresa, 'ventas' => $ventas]);
    }
    public function create(Request $request)
    {
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        //$iva = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        //change
        $ap_agrupado = Ap_Agrupado::where('valor_cobrado', '>=', '0')->where('estado_pago', '1')->get();
        return view('contable/convenios/create', ['porIva' => $this->porIva, 'divisas' => $divisas, 'ap_agrupado' => $ap_agrupado, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas, 'tipo_tarjeta' => $tipo_tarjeta]);
    }

    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = Ct_ventas::where('tipo', 'VEN-CONVENIO')->where('id_empresa', $id_empresa);
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

    public function obtener_ap(Request $request)
    {
        $codigo = $request['id'];
        if (!is_null($codigo)) {
            $ap = Ap_Agrupado::where('cod_proceso', $codigo)->where('estado_pago', '1')->first();
            if (!is_null($ap)) {
                //show all data 
                return response()->json($ap);
            } else {
                return 'no';
            }
        } else {
            return 'no';
        }
    }

    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $llevaOrden = false;
        $numero     = $request['numero'];
        $punto_emision = $request['punto_emision'];
        $numero_factura = "";
        $sucursal = substr($punto_emision, 0, -4);
        $num = 0;
        $punto_emisions = substr($punto_emision, 4);
        if (!is_null($numero)) {
            $contador_ctv = DB::table('ct_ventas')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emisions)->where('numero', $numero)->get()->count();
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_ventas')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emisions)->where('numero', $numero)->latest()->first();
                $max_id = intval($max_id->numero);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
        } else {
            $contador_ctv = DB::table('ct_ventas')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emisions)->get()->count();
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_ventas')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emisions)->latest()->first();
                $max_id = intval($max_id->numero);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
        }
        $pac = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $numero_factura . '-' . $pac;
        $id_paciente = $request['identificacion_paciente'];
        if (is_null($request['identificacion_paciente'])) {
            $id_paciente = '9999999999';
        }
        //fix contranstrain 
        $pacis = Paciente::find($id_paciente);
        if (is_null($pacis)) {
            $id_paciente = '9999999999';
        }
        //new change punto_emision
        if (!is_null($numero)) {
            $numero_factura = $numero;
        }

        $numero_final = $punto_emision . "-" . $numero_factura;

        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //7******GUARDAdo TABLA ASIENTO CABECERA********
        $input_cabecera = [
            'fecha_asiento'   => $fecha_as,
            'fact_numero'     => $numero_final,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text . " " . $request['concepto'],
            'valor'           => $request['total1'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //$id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $sucursal,
            'punto_emision'       => $punto_emision,
            'numero'              => $numero_factura,
            'nro_comprobante'     => $numero_final,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'nro_autorizacion'    => $request['numero_autorizacion'],
            'id_paciente'         => $id_paciente,
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],
            'concepto'            => $request['concepto'],
            'copago'              => $request['totalc'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            //'nota'                          => $request['nota'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            //'subtotal'                      => $request['subtotal1'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            // 'transporte'                    => $request['transporte'],
            'total_final'         => $request['total1'],
            'valor_contable'      => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];

        // return $factura_venta;

        $id_venta = Ct_ventas::insertGetId($factura_venta);
        //$id_venta = 0;
        $arr_total      = [];
        for ($i = 0; $i < count($request->input("nombre")); $i++) {

            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre' => $request->input("nombre")[$i],
                    'cantidad'       => $request->input("cantidad")[$i],
                    'bodega'         => $request->input("bodega")[$i],
                    'codigo'         => $request->input("codigo")[$i],
                    'precio'         => $request->input("precio")[$i],
                    'descpor'        => $request->input("descpor")[$i],
                    'copago'         => $request->input("copago")[$i],
                    'descuento'      => $request->input("desc")[$i],
                    'precioneto'     => $request->input("precioneto")[$i],
                    'detalle'        => $request->input("descrip_prod")[$i],
                    'iva'            => $request->input("iva")[$i],

                ];
                array_push($arr_total, $arr);
            }
        }
        //kardex
        foreach ($arr_total as $valor) {
            if ($valor['copago'] > 0) {
                //registra orden de venta
                $llevaOrden = true;
            }
            $detalle = [
                'id_ct_ventas'         => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['precioneto'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'porcentaje'           => $request['ivareal'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
            //update values on table ap_agrupado 
            if (!is_null($valor['codigo'])) {
                $ap = Ap_Agrupado::where('cod_proceso', $valor['codigo'])->first();
                if (!is_null($ap)) {
                    if ($valor['precioneto'] <= ($ap->valor_cobrado)) {
                        $resta = floatval($ap->valor_cobrado - $valor['precioneto']);
                        //only values 
                        if ($resta >= 0) {
                            //create values on table ct_detalle_pago_convenios
                            //updates no change valor_ant with new values and i dont know why date: 30 Nov
                            Ct_Detalle_Pago_Convenio::create([
                                'id_comprobante' => $id_venta,
                                'fecha' => $fecha_as,
                                'id_convenio' => $ap->id,
                                'estado' => '1',
                                'valor_ant' => $ap->total_iva,
                                'valor' => $resta,
                                'total' => $valor['precioneto'],
                                'ip_creacion'          => $ip_cliente,
                                'ip_modificacion'      => $ip_cliente,
                                'id_usuariocrea'       => $idusuario,
                                'id_usuariomod'        => $idusuario,
                            ]);
                            $ap->valor_cobrado = $resta;
                            $ap->estado_pago = 2;
                            $ap->save();
                        }
                    } else {

                        $ap->valor_cobrado = 0;
                        $ap->estado_pago = 2;
                        $ap->save();
                    }
                }
            }
        }

        //***MODULO CUENTA POR COBRAR***

        //cUENTAS X COBRAR CLIENTES

        $val_tol = $request['total1'];

        if ($val_tol > 0) {
            $ctas_cobrar_comerciales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - CTAS X COBRAR CLIENTES COMERCIALES');
            //$plan_cuentas = Plan_Cuentas::where('id', $ctas_cobrar_comerciales->cuenta_guardar)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $ctas_cobrar_comerciales->cuenta_guardar,
                'descripcion'         => $ctas_cobrar_comerciales->nombre_mostrar,
                'fecha'               => $fecha_as,
                'debe'                => $request['total1'],
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        //    2.01.07.01.01 iva sobre ventas
        if ($request['tarifa_iva1'] > 0) {
            $iva_ventas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - IVA VENTAS');
            //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $iva_ventas->cuenta_guardar,
                'descripcion'         => $iva_ventas->nombre_mostrar,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $request['tarifa_iva1'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }
        // 4.1.01.02    Ventas Mercaderia Tarifa 12%
        if ($request['subtotal_121'] > 0) {
            $ventas_insumos = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - VENTA INSUMOS 12%');
            //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $ventas_insumos->cuenta_guardar,
                'descripcion'         => $ventas_insumos->nombre_mostrar,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $request['subtotal_121'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        // 4.1.01.01    Ventas Mercaderia Tarifa 0%
        if ($request['subtotal_01'] > 0) {
            $ventas_tarifa_0 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - VENTAS TARIFA 0%');
            //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $ventas_tarifa_0->cuenta_guardar,
                'descripcion'         => $ventas_tarifa_0->nombre_mostrar,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $request['subtotal_01'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        $variable = $request['contador_pago'];

        for ($i = 0; $i < $variable; $i++) {

            $visibilidad_p = $request['visibilidad_pago' . $i];

            if ($visibilidad_p == 1) {

                Ct_Forma_Pago::create([

                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $request['id_tip_pago' . $i],
                    'fecha'           => $request['fecha_pago' . $i],
                    'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                    'numero'          => $request['numero_pago' . $i],
                    'banco'           => $request['id_banco_pago' . $i],
                    'cuenta'          => $request['id_cuenta_pago' . $i],
                    'giradoa'         => $request['giradoa' . $i],
                    'valor'           => $request['valor' . $i],
                    'valor_base'      => $request['valor_base' . $i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ]);
            }
        }

        $arr_p = [];
        for ($i = 0; $i < $variable; $i++) {

            $visibilidad_pa = $request['visibilidad_pago' . $i];

            if ($visibilidad_pa == 1) {

                $arr_pagos = [
                    'id_tip_pago'    => $request['id_tip_pago' . $i],
                    'fecha_pago'     => $request['fecha_pago' . $i],
                    'tipo_tarjeta'   => $request['tipo_tarjeta' . $i],
                    'numero_pago'    => $request['numero_pago' . $i],
                    'id_banco_pago'  => $request['id_banco_pago' . $i],
                    'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                    'giradoa'        => $request['giradoa' . $i],
                    'valor'          => $request['valor' . $i],
                    'valor_base'     => $request['valor_base' . $i],
                ];

                array_push($arr_p, $arr_pagos);
            }
        }
        $errores = "";
        //add paid for differents types examples : #efectivo , #cheque
        if (!is_null($id_venta)) {
            $erf = $this->crearComprobante($numero_factura, $request, $arr_p, $id_venta);
        } else {
            $errores .= "error no guarda comprobante";
        }
        return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'arr_p' => $arr_p, 'erf' => $erf];

        //update values 

    }

    public function crearComprobante($nfactura, Request $request, $array_pagos, $factura_id)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->max('id');
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {
            $total_pagos = $request['valor_totalPagos'];

            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($total_pagos),
                'fecha_asiento'   => $request['fecha_asiento'],
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $ctas_cobrar_comerciales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - CTAS X COBRAR CLIENTES COMERCIALES');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $ctas_cobrar_comerciales->cuenta_guardar,
                'descripcion'         => $ctas_cobrar_comerciales->nombre_mostrar,
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $total_pagos,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $request['fecha_asiento'],
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $request['identificacion_cliente'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => $request['cedula_vendedor'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                if ($valor['fecha_pago'] != "") {
                }
                $val = "";
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : $request['fecha_asiento'];
                Ct_Detalle_Pago_Ingreso::create([
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $fecha_pago, //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero_a'.$i],
                    'id_banco'        => $valor['id_banco_pago'], //$request['banco'.$i],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'], //$request['banco'.$i],
                    'id_tipo'         => $valor['id_tip_pago'], //$request['tipo'.$i],
                    'total'           => $val, //$request['valor'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['cuenta'.$i],
                    'girador'         => $valor['giradoa'],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }

            $caja_general = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACT CONVENIOS - CAJA GENERAL');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $caja_general->cuenta_guardar,
                'descripcion'         => $caja_general->nombre_mostrar,
                'fecha'               => $request['fecha_asiento'],
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //}
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $request['fecha_asiento'],
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $request['total1'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $request['total1']) {
                //aqui creo una orden de venta nueva
            } else {
            }

            $valor_actual = Ct_ventas::where('id', $factura_id)->first();
            $id__ventas   = [

                'valor_contable' => floatVal($valor_actual->valor_contable) - $total_pagos,
                'estado_pago'    => 2,
            ];

            Ct_ventas::where('id', $factura_id)->update($id__ventas);

            return $id__ventas;
        } else {
            return 0;
        }
    }
    //new changes


}
