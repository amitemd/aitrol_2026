<?php

namespace Sis_medico\Http\Controllers\contable;

use App\Http\Middleware\Authenticate;
use Excel;
use Faker\Provider\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Response;
// use Session;
use Sis_medico\Bodega;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_rubros;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario;
use Sis_medico\Log_Contable;
use Sis_medico\Pedido;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\InvDocumentosBodegas;

use Sis_medico\Ct_Cruce_Cuentas;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\LogAsiento;
use Sis_medico\Producto;
use Sis_medico\Utilidades;
use Sis_medico\v3\contable\Productos;

class ComprasController extends Controller
{
    private $controlador = 'compras';
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
        if ($request->opcion == '') {
            $id_empresa = $request->session()->get('id_empresa');
            $proveedor = Proveedor::all();
            $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
            $compras = DB::table('ct_compras as ct_c')
                ->select('mt.nombre as nombre_comprobante', 'ct_c.doc_electronico', 'ct_c.serie', 'ct_c.secuencia_f', 'ct_c.id', 'ct_c.rutapdf', 'ct_c.numero', 'ct_c.fecha', 'p.razonsocial', 'ct_c.autorizacion', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.observacion', 'ct_c.id_asiento_cabecera', 'ct_c.nro_autorizacion')
                ->leftjoin('proveedor as p', 'p.id', 'ct_c.proveedor')
                ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
                ->join('ct_master_tipos as mt', function ($q) {
                    $q->on('ct_c.tipo_comprobante', 'mt.codigo');
                })
                ->where('mt.tipo', 1)
                ->where('ct_c.id_empresa', $id_empresa)
                ->where('ct_c.tipo', 1)
                ->orderby('ct_c.id', 'desc')
                ->paginate(10);
            return view('contable/compra/index', ['compras' => $compras, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
        } elseif ($request->opcion == 'sucCompras') {
            $data['compras'] = Ct_Compras::join('ct_asientos_cabecera as cab', 'cab.id', 'ct_compras.id_asiento_cabecera')
                ->where('ct_compras.id_empresa', session('id_empresa'))
                ->whereNull('ct_compras.doc_electronico')
                ->whereYear('ct_compras.fecha', date('Y'))
                ->get([
                    'ct_compras.id',
                    'ct_compras.id_asiento_cabecera',
                    'ct_compras.fecha',
                    'ct_compras.observacion',
                    'ct_compras.sucursal',
                    'ct_compras.punto_emision',
                    'ct_compras.numero',
                    'ct_compras.serie',
                    'ct_compras.secuencia_factura',
                    'ct_compras.autorizacion',
                    'cab.sucursal as sucursal_asiento'
                ]);
            return view('util.view_sucursales_compras', $data);
        } elseif ($request->opcion == 'actualizarSucursal') {
            $arrayAsiento = [
                'sucursal' => $request->sucursalA,
                'punto_emision' => $request->emisionC,
            ];
            Ct_Asientos_Cabecera::where('id', $request->ida)->update($arrayAsiento);
            $arrayCompra = [
                'sucursal' => $request->sucursalC,
                'punto_emision' => $request->emisionC,
            ];
            Ct_compras::where('id', $request->id)->update($arrayCompra);
            return response()->json(['success' => 'ok']);
        } elseif ($request->opcion == 'deudas_pagos') {
            $data['algo'] = 1;
            if ($request->anio != '' && $request->mes != '' && $request->dia != '') {
                $data['datos'] = Ct_compras::getDeudasPagos($request);
            }
            return view('contable/deudasvspagos/view_deudas_pagos', $data);
        } elseif ($request->opcion == 'ver_asiento') {
            return Ct_compras::verAsiento($request->id);
        } elseif ($request->opcion == 'buscarProveedor') {
            return Proveedor::buscarProveedor($request->term);
        } elseif ($request->opcion == 'buscarProductos') {
            echo Producto::buscarProductos($request->term);
        }
    }
    public function traercomprobante(Request $r)
    {
        return EmisionDocumentosController::obtenercomprobanteSRI($r->claveacceso, 'algo');
    }
    public function verificar_documento_sri()
    {
        $data['empresa'] = Empresa::where('id', session('id_empresa'))->where('estado', '1')->first();
        return view('tributacion.view_verificar_documento_sri', $data);
    }
    public function proveedorsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('razonsocial', 'LIKE', '%' . $request['search'] . '%')->where('estado', '1')->select('proveedor.id as id', 'proveedor.razonsocial as text')->get();
        }

        return response()->json($proveedor);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        //dd($divisas);
        $id_empresa     = $request->session()->get('id_empresa');
        $bodega         = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $c_tributario   = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante  = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $tipo_pago      = Ct_Forma_Pago::where('estado', '1')->get();
        $tipo_tarjeta   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $lista_banco    = Ct_Bancos::where('estado', '1')->get();
        //$cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');
        $cuentaiva = LogConfig::busqueda('4.1.01.02');
        // /$cuentacaja = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_CAJA');
        //dd($cuentaiva, $cuentacaja);
        //$iva_param      = Ct_Configuraciones::where('id_plan', $cuentaiva->cuenta_guardar)->first();
        //$iva_param = Ct_Configuraciones::where('id_plan', $cuentaiva)->first();
        //$caja_chica     = Ct_Configuraciones::where('id_plan', $cuentacaja->cuenta_guardar)->first();
        $rubros = Ct_rubros::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $termino = Ct_Termino::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $empresa_sucurs = Empresa::findorfail($id_empresa);
        //dd($empresa_sucurs);
        $empresa_general = Empresa::all();
        $sucursales  = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->get();
        $cierreCabecera = Ct_Cierre_Cabecera::where('id_empresa', $id_empresa)->where('estado', '1')->orderBy('id', 'desc')->first();
        $mindate = null;
        if (!is_null($cierreCabecera)) {
            $mindate = date('Y-m-d', strtotime($cierreCabecera->fecha_cierre));
        }
        return view('contable/compra/create', ['porIva' => $this->porIva, 'divisas' => $divisas, 'empresa' => $empresa, 'rubros' => $rubros, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'tipo_pago' => $tipo_pago, 'productos' => $productos, 'bodega' => $bodega, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'termino' => $termino, 'sucursales' => $sucursales, 'min_date' => $mindate]);
    }

    private function valida_secuencia(Request $request)
    {
        $prules = [
            'secuencia_factura' => 'unique:secuencia_factura',
        ];
        $pmsn = [
            'secuencia_factura.unique' => 'El número de factura ya está registrado',
        ];
        $this->validate($request, $prules, $pmsn);
    }
    private static function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
    public function estado_comprobante($data)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = $this->getNonce(12);
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        if ($data['tipo'] == "comprobante") {
            $url = "https://api.pagosyfacturas.com/api/billing/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "pdf") {
            $url = "https://api.pagosyfacturas.com/api/billing/ride/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "xml") {
            $url = "https://api.pagosyfacturas.com/api/billing/xml/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } else {
            return "no exite el tipo";
        }
        //dd($url);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => 'prueba',
            ),
        );
        //dd($options);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        return $response;
    }
    public function store(Request $request)
    {
        $msj = '';
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $id_empresa      = $request->session()->get('id_empresa');
        $idusuario       = Auth::user()->id;
        $iva_acreditable = $request['iva_final'];
        $objeto_validar  = new Validate_Decimals();
        DB::beginTransaction();
        try {
            $fechahoy = $request['fecha'];
            $verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
            }
            $pedido = Pedido::where('pedido', $request['pedido_nombre'])->first();
            if (!is_null($pedido)) {
                if ($pedido->tipo == 3) {
                    /*$cabecera_c = [
                        'observacion'     => 'Salida de Consignacion Pedido ' . $pedido->id,
                        'fecha_asiento'   => $request['fecha'],
                        'fact_numero'     => $request['secuencia_factura'],
                        'valor'           => $request['base1'],
                        'id_empresa'      => $id_empresa,
                        'estado'          => '1',
                        'aparece_sri'     => $request['archivosri'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                    // $cuenta_cambio = '2.01.03.01.03';
                    // $nombre = "CxP Proveedores locales consignacion";
                    // if($id_empresa == "1793135579001"){
                    //     $cuenta_cambio = "1436";
                    //     $nombre = "PROVISIONES CUENTAS POR PAGAR MERCADERÍA EN CONSIGNACIÓN";
                    // }

                    //$cuentacxpmerc = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_CXPMERCADERIA');
                      //$cuentamerc_consig = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_MERCADERIA_CONSIG');
                    $id_plan_confg = LogConfig::busqueda('2.01.03.01.03');
                    $cuentacxpmerc = Plan_Cuentas::find($id_plan_confg);

                    $id_cabecera_c = Ct_Asientos_Cabecera::insertGetId($cabecera_c);
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_cabecera_c,
                        'id_plan_cuenta'      => $cuentacxpmerc->id,
                        'descripcion'         => $cuentacxpmerc->nombre,
                        'fecha'               => $request['fecha'],
                        'debe'                => $request['base1'],
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);

                    // $cuenta_cambio = '1.01.03.01.01';
                    // if($id_empresa == "1793135579001"){
                    //     $cuenta_cambio = "1.01.03.01.04";
                    // }
                    
                    //$cuentamerc_consig = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_MERCADERIA_CONSIG');
                    $id_plan_confg = LogConfig::busqueda('1.01.03.01.01');
                    $cuentamerc_consig = Plan_Cuentas::find($id_plan_confg);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_cabecera_c,
                        'id_plan_cuenta'      => $cuentamerc_consig->id,
                        'descripcion'         => $cuentamerc_consig->nombre,
                        'fecha'               => $request['fecha'],
                        'debe'                => '0',
                        'haber'               => $request['base1'],
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);*/
                }
                $pedido->estado_contable = 1;
                $pedido->save();
            }
            // $sucursal      = $request['sucursal'];
            $errores       = "";
            //$cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');
            $id_plan_confg = LogConfig::busqueda('4.1.01.02');
            // $cuentaiva = 
            $iva_param     = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();
            // $punto_emision = $request['serie'];
            //$sucursal      = substr($punto_emision, 0, -4);
            //$punto_emision = substr($punto_emision, 4);
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            if (!is_null($cod_sucurs)) {
                $sucursal2 = $cod_sucurs->codigo_sucursal;
            } else {
                $sucursal2 = "0";
            }
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            if (!is_null($cod_caj)) {
                $punto_emision2     = $cod_caj->codigo_caja;
            } else {
                $punto_emision2 = "0";
            }
            $empresa      = Empresa::find($id_empresa);
            $total_final  = $objeto_validar->set_round($request['total1']);
            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal2)->where('punto_emision', $punto_emision2)->get()->count();

            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal2)->where('punto_emision', $punto_emision2)->latest()->first();
                $max_id = intval($max_id->secuencia_f);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado   = $request['serie'] . '-' . $request['secuencia_factura'];

            //$numeroconcadenado   = "{$c_sucursal}-{$c_caja}-{$request['secuencia_factura']}";
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('tipo', '1')->where('proveedor', $request['proveedor'])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
            if (is_null($comprobacion_compra) || $comprobacion_compra == '[]') {
                $fechahoy = $request['fecha'];
                // $comp= Ct_compras::where('pedido',$request->pedido_nombre)->where('estado','<>','0')->first();
                // if(!is_null($comp)){
                //     return response()->json(['errores'=>'Ya existe factura con este pedido']);
                // }
                $cabeceraa = [
                    'observacion'     => $request['observacion'],
                    'fecha_asiento'   => $request['fecha'],
                    'fact_numero'     => $request['secuencia_factura'] != null ? $request['secuencia_factura'] : $numero_factura,
                    'valor'           => $total_final,
                    'id_empresa'      => $id_empresa,
                    'estado'          => '1',
                    'sucursal'            => $sucursal2,
                    'punto_emision'       => $punto_emision2,
                    'aparece_sri'     => $request['archivosri'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                if ($empresa->id != '0992704152001') {
                    $id_proveedor   = $request['proveedor'];
                    $proveedor_find = Proveedor::find($id_proveedor);
                    $cabeceraa      = [
                        'observacion'     => $proveedor_find->razonsocial . ' # ' . $numeroconcadenado,
                        'fecha_asiento'   => $request['fecha'],
                        'fact_numero'     => $request['secuencia_factura'] != null ? $request['secuencia_factura'] : $numero_factura,
                        'valor'           => $total_final,
                        'id_empresa'      => $id_empresa,
                        'estado'          => '1',
                        'aparece_sri'     => $request['archivosri'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal'        => $sucursal2,
                        'punto_emision' => $request['p_emision'],
                    ];
                }

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                $nueva_fecha         = null;
                $modfecha            = null;
                $consulta_fecha      = Ct_Termino::where('id', $request['termino'])->first();
                if ($consulta_fecha != null) {
                    $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($fechahoy));
                    $modfecha    = date("Y-m-d", $nueva_fecha);
                } else {
                    $errores .= " la fecha del termino no funciona ";
                }
                $subtotalf = $request['base1'];
                if ($request['fecha'] != $request['f_autorizacion']) {
                }
                $t = null;
                if ($request['tipo_comprobante'] == '03') {
                    $t = 0;
                }
                $sucursal = Ct_Sucursales::where('id', $request['sucursal'])->first();
                $input = [
                    'electronica'         => $request['tipo_electronica'],
                    'doc_electronico'     => $t,
                    'tipo'                => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => $request['fecha'],
                    'archivo_sri'         => $request['archivosri'],
                    'proveedor'           => $request['proveedor'],
                    'direccion_proveedor' => $request['direccion_proveedor'],
                    'termino'             => $request['termino'],
                    'pedido'              => $request['pedido_nombre'],
                    'orden_compra'        => $request['o_compra'],
                    'f_caducidad'         => $request['f_caducidad'],
                    'tipo_gasto'          => $request['tipo_gasto'],
                    'sucursal'            => $sucursal->codigo_sucursal,
                    'punto_emision'       => $request['p_emision'],
                    'valor_contable'      => $total_final,
                    'fecha_termino'       => $modfecha,
                    'secuencia_f'         => $numero_factura,
                    'estado'              => '1',
                    'autorizacion'        => $request['autorizacion'],
                    'f_autorizacion'      => $request['f_autorizacion'],
                    'serie'               => $request['tipo_comprobante'] != '03' ? $request['serie'] : 0,
                    'id_empresa'          => $id_empresa,
                    'numero'              => $numeroconcadenado,
                    'secuencia_factura'   => $request['tipo_comprobante'] != '03' ? $request['secuencia_factura'] : 0,
                    'credito_tributario'  => $request['credito_tributario'],
                    'tipo_comprobante'    => $request['tipo_comprobante'],
                    'observacion'         => $request['observacion'],
                    'subtotal_0'          => $request['subtotal_01'],
                    'subtotal_12'         => $request['subtotal_121'],
                    'subtotal'            => $subtotalf,
                    'descuento'           => $request['descuento1'],
                    'iva_total'           => $request['tarifa_iva1'],
                    'ice_total'           => $request['ice_final1'],
                    'total_final'         => $total_final,
                    'transporte'          => $request['transporte'],
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $id_compra = Ct_compras::insertGetId($input);
                $arr_total = [];
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $arr = [
                            'nombre'     => $request->input("nombre")[$i],
                            'cantidad'   => $request->input("cantidad")[$i],
                            'bodega'     => $request->input("bodega")[$i],
                            'codigo'     => $request->input("codigo")[$i],
                            'precio'     => $request->input("precio")[$i],
                            'descpor'    => $request->input("descpor")[$i],
                            'copago'     => $request->input("copago")[$i],
                            'descuento'  => $request->input("desc")[$i],
                            'precioneto' => $request->input("precioneto")[$i],
                            'detalle'    => $request->input("descrip_prod")[$i],
                            'iva'        => $request->input("iva")[$i],
                        ];
                        array_push($arr_total, $arr);
                    }
                }
                $cuentas_iva = [];
                foreach ($arr_total as $valor) {
                    $consulta_product = Ct_productos::where('codigo', $valor['codigo'])->first();

                    if ($consulta_product != '') {
                        $cuentas_iva = $consulta_product->impuesto_iva_compras;
                    }
                    $detalle = [
                        'id_ct_compras'        => $id_compra,
                        'codigo'               => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
                        'bodega'               => $valor['bodega'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'estado'               => '1',
                        'descuento'            => $valor['descuento'],
                        'extendido'            => $valor['precioneto'],
                        'detalle'              => $valor['detalle'],
                        'iva'                  => $valor['iva'],
                        'porcentaje'           => $request['ivareal'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];

                    Ct_detalle_compra::create($detalle);
                    /*
                for($j = 0; $j < $valor['cantidad']; $j++){
                $cod_prod = Ct_Movimiento_Producto::where('id_ct_producto',$consulta_product->id)->get();
                if(!is_null($cod_prod)){
                $mov_producto = [
                'estado_producto' => '1',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                ];
                }
                Ct_Movimiento_Producto::where('id',$cod_prod->id)
                ->update($mov_producto);
                }
                 */
                }
                /*if ($pedido->tipo != 3) {*/
                $asientos = $this->asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $request->total1);
                if ($asientos != '') {
                    DB::rollBack();
                    return response()->json(['id' => 0, 'errores' => 'si', 'msj' => $asientos['msj'], 'id_asiento' => 0, 'inventario' => '', 'errorAsiento' => 'No se genero asiento']);
                }
                /*}*/
                $asientos = [];
                $data['id']   = $id_compra;
                $data['tipo'] = '1';
                /*$msj = Ct_Kardex::generar_kardex($data);
                $errores .= " el kardex respuesta:  " . $msj;*/

                $inventario = Inventario::build_process('C', $id_compra, $empresa->id, 1);
		DB::commit();
                return response()->json(['id' => $id_compra, 'errores' => 'no', 'msj' => $errores, 'id_asiento' => $id_asiento_cabecera, 'inventario' => $inventario, 'errorAsiento' => $asientos]);
            } else {
		DB::rollBack();
                return response()->json(['errores' => 'Ya existe factura de compra']);
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            //return $e->getMessage() . ' Linea: ' . $e->getLine();
            return response()->json(['id' => 0, 'errores' => 'si', 'msj' =>  $e->getMessage() . ' Linea: ' . $e->getLine(), 'id_asiento' => 0, 'inventario' => 0, 'errorAsiento' => 0]);
        }
    }
    public function asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $total1)
    {
        //dd($request);
        $id_empresa      = Session::get('id_empresa');
        $globales        = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
        $valor_descuento = $request['descuento1'];
        $cuentas_iva     = 0;
        $base1           = $request['base1'];
        $total1          = $request['total1'];
        if ($valor_descuento > 0) {
            $base1  = $base1 + $valor_descuento;
            $total1 = $total1 + $valor_descuento;
        }
        $id_plan_confg = LogConfig::busqueda('1.01.03.01.02');
        $pedido = Pedido::where('pedido', $request['pedido_nombre'])->first();
        if (!is_null($pedido)) {
            $documento = InvDocumentosBodegas::where('id', $pedido->tipo)->where('abreviatura_documento', 'ICG')->first();
            if (!is_null($documento)) {
                $id_plan_confg = LogConfig::busqueda('2.01.03.01.03');
            }
        }

        $cuenta_prod_term = Plan_Cuentas::find($id_plan_confg);
        /*if ($id_empresa == '2390058384001') {
            echo '<pre>';
            echo $id_plan_confg . ' - ';
            print_r($cuenta_prod_term);
            exit;
        }*/
        if ($cuenta_prod_term != '') {
            if ($request['tarifa_iva1'] > 0) {
                $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                if ($global != '') {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_prod_term->id,
                        'descripcion'         => $cuenta_prod_term->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $base1,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                    $plan_cuentas = Plan_Cuentas::find($globales->debe);
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $request['tarifa_iva1'],
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    return [
                        'msj' => 'No existe plan de cuenta para la empresa 1: ' . $id_empresa
                    ];
                }
            } else {
                $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                //Aqui cambie
                if ($global != '') {
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_prod_term->id,
                        'descripcion'         => $cuenta_prod_term->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $total1,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    return [
                        'msj' => 'No existe plan de cuenta para la empresa 2: ' . $id_empresa
                    ];
                }
            }
        } else {
            return [
                'msj' => 'No existe plan de cuenta para la empresa 3: ' . $id_empresa
            ];
        }
        if ($valor_descuento > 0) {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null && $consulta_en_proveedor != '[]') {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    if ($desc_cuenta == '') {
                        return [
                            'msj' => 'La cuenta del proveedor no esta configurada correctamente para la empresa: ' . $id_empresa
                        ];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $desc_cuenta->id,
                        'descripcion'         => $desc_cuenta->nombre,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['total1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                }
            }
            $global_des = Ct_Globales::where('id_modulo', 21)->where('id_empresa', $id_empresa)->first();
            // $id_plan_confg = LogConfig::busqueda('FACTCONTABLE_DESC_COMP');
            $id_plan_confg = Ct_Configuraciones::obtener_cuenta("FACTCONTABLE_DESC_COMP");
            // $plancuenta = Plan_Cuentas::find($id_plan_confg->cuenta_guardar);
            if (Auth::user()->id == "0957258056") {
                // dd($id_plan_confg);
            }
            $cuenta_desc_ven = Plan_Cuentas::find($id_plan_confg->cuenta_guardar);
            //$desc_cuenta = Plan_Cuentas::where('id', '4.1.06.01')->first();
            //$cuenta_desc_ven = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_DESC_VENTA');
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_desc_ven->id,
                'descripcion'         => $cuenta_desc_ven->nombre,
                'fecha'               => $fechahoy,
                'debe'                => '0',
                'haber'               => $valor_descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        } else {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null) {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    if (!is_null($desc_cuenta)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $desc_cuenta->id,
                            'descripcion'         => $desc_cuenta->nombre,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    } else {
                        $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                        //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                        //$cuenta_locales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_PROV_LOC');
                        $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                        $cuenta_locales = Plan_Cuentas::find($id_plan_confg);
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_locales->id,
                            'descripcion'         => $cuenta_locales->nombre,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                } else {
                    //$errores .= " cuenta por defecto de proveedores ";
                    //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                    $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                    //$cuenta_locales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_PROV_LOC');
                    $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                    $cuenta_locales = Plan_Cuentas::find($id_plan_confg);
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_locales->id,
                        'descripcion'         => $cuenta_locales->nombre,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['descuento1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            } else {
                //$errores .= " no tiene cuenta el proveedor 2";
            }
        }
    }
    public function editar($id, Request $request)
    {
        $compras        = Ct_compras::where('id', $id)->first();
        $punto_emision  = $compras->punto_emision;
        $sucursal       = $compras->sucursal;
        $detalle_compra = Ct_detalle_compra::where('id_ct_compras', $compras->id)->get();
        $proveedor      = proveedor::where('estado', '1')->get();
        $id_empresa     = $request->session()->get('id_empresa');
        $termino        = Ct_Termino::where('estado', '1')->get();
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $bodega         = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $c_tributario   = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante  = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $productos      = Ct_productos::where('id_empresa', $id_empresa)->get();
        return view('contable/compra/edit', ['compras' => $compras, 'c_tributario' => $c_tributario, 'productos' => $productos, 'bodega' => $bodega, 'termino' => $termino, 't_comprobante' => $t_comprobante, 'proveedor' => $proveedor, 'detalle_compra' => $detalle_compra, 'empresa' => $empresa]);
    }

    public function buscar(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $proveedor        = Proveedor::all();
        $empresa          = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $compras = Ct_compras::getBusquedaCompras($request);
        return view('contable/compra/index', ['compras' => $compras, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
    }
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query      = DB::table('ct_compras as ct_c')
            ->join('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderby('ct_c.id', 'desc')
            ->select('ct_c.id as id', 'ct_c.fecha', 'ct_c.rutapdf', 'p.razonsocial', 'u.nombre1', 'u.apellido1', 'ct_c.autorizacion', 'ct_c.secuencia_factura', 'ct_c.numero', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.tipo', 'ct_c.archivo_sri', 'ct_c.proveedor', 'ct_c.orden_compra', 'ct_c.f_caducidad', 'ct_c.tipo_gasto', 'ct_c.f_autorizacion', 'ct_c.serie', 'ct_c.secuencia_factura', 'ct_c.credito_tributario', 'ct_c.observacion', 'ct_c.id_asiento_cabecera');
        //dd($query->get());
        $fields = array_keys($constraints);
        $index  = 0;
        //dd($constraints);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                //$query = $query->where($fields[$index], $constraint);
                if ($fields[$index] == "ct_c.id_asiento_cabecera") {
                    //dd("da");
                    $query = $query->where($fields[$index], $constraint);
                } elseif ($fields[$index] == "ct_c.id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function buscar_pedido(Request $request)
    {
        $pedido = $request['id_pedido'];
        $data = null;
        $inv_pedido = Pedido::where('pedido', $pedido)->where('id_empresa', Session::get('id_empresa'))->whereNull('deleted_at')->first();
        if (is_null($inv_pedido)) {
            return ['value' => 'no resultados'];
        }
        if ($inv_pedido->orden_conglomerada == 1) {
            $sql = Pedido::join('proveedor as prov', 'pedido.id_proveedor', '=', 'prov.id')
                ->join('inv_documentos_bodegas as d', 'pedido.tipo', '=', 'd.id')
                ->join('movimiento as mo', 'pedido.id', '=', 'mo.id_pedido')
                ->leftJoin('producto as pr', 'mo.id_producto', '=', 'pr.id')
                ->leftJoin('ct_productos_insumos as pin', 'pr.id', '=', 'pin.id_insumo')
                ->join('ct_productos as cp', 'pin.id_producto', '=', 'cp.id')
                ->select(
                    'mo.id AS cantidad',
                    'pedido.id',
                    'cp.codigo',
                    'cp.nombre',
                    'pedido.id_proveedor',
                    'pedido.pedido',
                    'pedido.tipo',
                    'd.documento',
                    'd.abreviatura_documento',
                    'pedido.fecha',
                    DB::raw('sum(mo.cantidad) AS cantidad_total'),
                    'mo.precio',
                    'mo.id_producto',
                    'mo.serie',
                    'mo.subtotal_0',
                    'mo.subtotal_12',
                    'mo.descuento',
                    'mo.iva',
                    'pedido.vencimiento',
                    'pedido.orden_conglomerada',
                    'prov.razonsocial',
                    'prov.id_cuentas',
                    'pin.codigo_producto',
                    'mo.id_bodega',
                    'cp.iva',
                    'prov.direccion',
                    'pedido.id_empresa',
                    'mo.descuento as descuentovalor',
                    'mo.descuentop as descuentoporct',
                    'cp.mod_desc',
                    'cp.mod_precio'
                )
                ->where('pedido', $inv_pedido->id)
                ->where('mo.estado', 1)
                ->groupBy('pr.codigo', 'mo.serie', 'mo.precio')
                ->get();
            if ($sql->count() > 0) {
                $productos = $sql;
            } else {
                $sql = Pedido::join('proveedor as prov', 'pedido.id_proveedor', '=', 'prov.id')
                    ->join('inv_documentos_bodegas as d', 'pedido.tipo', '=', 'd.id')
                    ->join('movimiento as mo', 'pedido.id', '=', 'mo.id_pedido')
                    ->leftJoin('producto as pr', 'mo.id_producto', '=', 'pr.id')
                    ->leftJoin('ct_productos_insumos as pin', 'pr.id', '=', 'pin.id_insumo')
                    ->join('ct_productos as cp', 'pin.id_producto', '=', 'cp.id')
                    ->select(
                        'mo.id AS cantidad',
                        'pedido.id',
                        'cp.codigo',
                        'cp.nombre',
                        'pedido.id_proveedor',
                        'pedido.pedido',
                        'pedido.tipo',
                        'd.documento',
                        'd.abreviatura_documento',
                        'pedido.fecha',
                        DB::raw('sum(mo.cantidad) AS cantidad_total'),
                        'mo.precio',
                        'mo.id_producto',
                        'mo.serie',
                        'mo.subtotal_0',
                        'mo.subtotal_12',
                        'mo.descuento',
                        'mo.iva',
                        'pedido.vencimiento',
                        'pedido.orden_conglomerada',
                        'prov.razonsocial',
                        'prov.id_cuentas',
                        'pin.codigo_producto',
                        'mo.id_bodega',
                        'cp.iva',
                        'prov.direccion',
                        'pedido.id_empresa',
                        'mo.descuento as descuentovalor',
                        'mo.descuentop as descuentoporct',
                        'cp.mod_desc',
                        'cp.mod_precio'
                    )
                    ->where('pedido', $pedido)
                    ->where('mo.estado', 1)
                    ->groupBy('pr.codigo', 'mo.serie', 'mo.precio')
                    ->get();
                $productos = $sql;
            }
            if (count($productos) > 0) {
                $data = [$productos[0]->id_proveedor, $productos[0]->fecha, $productos[0]->vencimiento, $productos, $productos[0]->direccion, $productos[0]->id_cuentas, $productos[0]->id_empresa, $productos[0]->razonsocial];
                return $data;
            } else {

                return ['value' => 'no resultados'];
            }
        }
        $sql = "SELECT m.id AS cantidad,
        sum(m.cantidad) AS cantidad_total,
        m.id_producto,
        m.serie,
        cp.codigo,
        cp.nombre,
        p.id_proveedor,
        prov.razonsocial,
        p.fecha,
        p.vencimiento,
        prov.id_cuentas,
        prods.codigo_producto,
        p.id_bodega,
        m.valor_unitario as precio,
        cp.iva,
        prov.direccion,
        p.id_empresa,
        p.pedido,
        p.descuento as descuentovalor,
        p.tipo,
        cp.mod_desc,
        cp.mod_precio
        FROM inv_det_movimientos AS m
        INNER JOIN pedido AS p
        ON p.id = m.id_pedido
        INNER JOIN proveedor AS prov
        ON prov.id = p.id_proveedor
        LEFT JOIN ct_productos_insumos AS prods
        ON prods.id_insumo = m.id_producto
        LEFT OUTER JOIN ct_productos cp
        ON cp.id = prods.id_producto
        LEFT OUTER JOIN producto px
        ON px.id = prods.id_insumo
        WHERE p.id = '$inv_pedido->id'
        AND m.estado =1 AND  m.id_detalle_pedido IS NOT NULL
        GROUP BY prods.codigo_producto,m.serie";
        $productos = DB::select($sql);
        if (count($productos) > 0) {
            $data = [$productos[0]->id_proveedor, $productos[0]->fecha, $productos[0]->vencimiento, $productos, $productos[0]->direccion, $productos[0]->id_cuentas, $productos[0]->id_empresa, $productos[0]->razonsocial];
            return ['value' => $data];
        } else {
            return ['value' => 'no resultados'];
        }
    }

    public function buscar_pedido16112023(Request $request)
    {
        $pedido    = $request['id_pedido'];
        $idusuario = Auth::user()->id;
        $data = null;
        $inv_pedido = Pedido::where('pedido', $pedido)->where('id_empresa', Session::get('id_empresa'))->whereNull('deleted_at')->first();
        if (is_null($inv_pedido)) {
            return ['value' => 'no resultados'];
        }
        if ($inv_pedido->orden_conglomerada == 1) {
            $sql = "SELECT m.id AS cantidad,
                m.cantidad AS cantidad_total,
                m.subtotal_0,
                m.subtotal_12,
                m.id_producto,
                m.serie,
                cp.codigo,
                cp.nombre,
                p.id_proveedor,
                prov.razonsocial,
                p.fecha,
                p.vencimiento,
                prov.id_cuentas,
                prods.codigo_producto,
                m.id_bodega,
                m.precio,
                cp.iva,
                prov.direccion,
                p.id_empresa,
                p.pedido,
                m.descuento as descuentovalor,
                m.descuentop as descuentoporct,
                p.tipo
                FROM movimiento AS m
                INNER JOIN pedido AS p
                ON p.id = m.id_pedido
                INNER JOIN proveedor AS prov
                ON prov.id = p.id_proveedor
                LEFT JOIN ct_productos_insumos AS prods
                ON prods.id_insumo = m.id_producto
                LEFT OUTER JOIN ct_productos cp
                ON cp.id = prods.id_producto
                LEFT OUTER JOIN producto px
                ON px.id = prods.id_insumo
                WHERE p.id =  $inv_pedido->id 
                AND m.estado =1";
            //echo $sql;exit;
            $productos = DB::select(DB::raw($sql));
            $comboProductos = '';
            $comboBodegas = '';
            if (count($productos) > 0) {
                $id_empresa = $request->session()->get('id_empresa');
                $bodega = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
                $table = '';
                $lista_productos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->get();
                foreach ($lista_productos as $value1) {
                    $selected = '';
                    $comboProductos .= '<option ' . $selected . ' value="' . $value1->nombre . '" data-name="' . $value1->nombre . '" data-codigo="' . $value1->codigo . '" data-descuento="' . $value1->mod_desc . '" data-precio="' . $value1->mod_precio . '" data-maxdesc="' . $value1->descuento . '" data-iva="' . $value1->iva . '">' . $value1->codigo . '|' . $value1->nombre . '</option>';
                }
                foreach ($bodega as $value2) {
                    $selected = '';
                    $comboBodegas .= '<option ' . $selected . ' value="' . $value2->id . '">' . $value2->nombre . '</option>';
                }
                $contador = 0;
                $total = 0;
                $totales = 0;
                $subtotal = 0;
                $subtotal0 = 0;
                $subtotal12 = 0;
                $total_iva = 0;
                $sub0 = 0;
                $sub12 = 0;
                $base = 0;
                $descuento = 0;
                $descuento_total = 0;
                $subtotal1 = 0;
                foreach ($productos as $value) {
                    $total = $value->cantidad_total * $value->precio;
                    $descuento = $total * $value->descuentovalor;
                    $descuentop = 0;
                    $descuento_total += $descuento;
                    if (!is_null($value->descuentovalor)) {
                        $descuentop = $value->descuentovalor / 100;
                    }
                    $iva = 0;
                    $subtotal1 += $total - $descuento;
                    $base += $subtotal;
                    $check = '';
                    $subtotal12 += $value->subtotal_12;
                    if ($value->iva == '1') {
                        $check = 'checked';
                        $iva = $subtotal1 * $this->porIva->iva;
                        //$subtotal12 += $total - $descuento;

                    } else {
                        //$subtotal0 += $total - $descuento;
                        $subtotal0 += $value->subtotal_0;
                    }
                    $totales += $subtotal12 + $subtotal0;
                    $total_iva += $iva;
                    $table .= '<tr class="wello">
                        <td style="max-width:100px;">
                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                            <select name="nombre[]" class="form-control select2_cuentas" id="producto_' . $value->id_producto . '" style="width:100%" required onchange="verificar(this)">
                                <option> </option>' . $comboProductos;
                    $table .= '</select>
                            <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">' . 'serie: ' . $value->serie . '</textarea>
                            <input type="hidden" name="iva[]" class="iva" ' . $check . '/>
                        </td>
                        <td>
                            <input class="form-control text-right cneto" id="cantidad_0" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $value->cantidad_total . '" name="cantidad[]" required>
                            <select name="bodega[]" class="form-control select2_bodega bodega" id="bodega_' . $value->id_producto . '" style="width: 80%;margin-top: 5px;" required>
                                <option> </option>' . $comboBodegas;

                    $table .= '</select>
                        </td>
                        <td id="tprecio" style="max-width:100px;">
                            <input type="text" class="form-control pneto" id="precio_0" onblur="calcularCompra(0)" style="width: 80%;height:20px;" name="precio[]" placeholder="0.00" value="' . $value->precio . '">
                        </td>
                        <td>
                            <input class="form-control text-right" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $descuentop . '" name="descpor[]" required>
                            <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $value->descuentovalor . '" name="maxdesc[]" required>
                        </td>
                        <td>
                            <input class="form-control text-right descuento_" id="descuento_0" onchange="descuentoCal(this)" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);calcularCompra(0);" value="' . $value->descuentovalor . '" name="desc[]" required>
                        </td>
                        <td>
                            <input class="form-control px-1 text-right total_" id="total_0" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="' . $subtotal1 . '" onblur="this.value=parseFloat(this.value).toFixed(2);calcularCompra(0);" name="precioneto[]" required>
                        </td>
                        <td>
                            <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" ' . $check . ' disabled>
                        </td>
                        <td>
                            <button id="eliminar1" type="button" class="btn btn-danger btn-gray delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    <tr style="display:none" id="mifila">
                    </tr>';
                    $contador++;
                    $table .= '<script>
                    $(document).ready(function(){
                        $("#producto_' . $value->id_producto . '").val(\'' . $value->nombre . '\').trigger("change");
                        $("#bodega_' . $value->id_producto . '").val(\'' . $value->id_bodega . '\').trigger("change");
                    });
                    </script>';
                }
                $data = [
                    $productos[0]->id_proveedor, //0
                    $productos[0]->fecha, //1
                    $productos[0]->vencimiento, //2 
                    $table, //3 
                    $productos[0]->direccion, //4 
                    $productos[0]->id_cuentas, //5
                    $productos[0]->id_empresa, //6
                    $contador, //7
                    round($subtotal12, 2, PHP_ROUND_HALF_UP), //8
                    round($subtotal0, 2, PHP_ROUND_HALF_UP), //9
                    round($descuento_total, 2, PHP_ROUND_HALF_UP), //10
                    round(($subtotal12 * $this->porIva->iva), 2, PHP_ROUND_HALF_UP), //11
                    round(($subtotal0 + $subtotal12), 2, PHP_ROUND_HALF_UP), //12
                    round(($subtotal0 + $subtotal12) - ($descuento_total) + ($subtotal12 * $this->porIva->iva), 2, PHP_ROUND_HALF_UP) //13
                ];
                return $data;
            } else {
                return ['value' => 'no resultados'];
            }
        }
        $sql = "SELECT m.id AS cantidad,
        sum(m.cantidad) AS cantidad_total,
        m.id_producto,
        m.serie,
        cp.codigo,
        cp.nombre,
        p.id_proveedor,
        prov.razonsocial,
        p.fecha,
        p.vencimiento,
        prov.id_cuentas,
        prods.codigo_producto,
        p.id_bodega,
        m.valor_unitario as precio,
        cp.iva,
        prov.direccion,
        p.id_empresa,
        p.pedido,
        p.descuento as descuentovalor,
        p.tipo
        FROM inv_det_movimientos AS m
        INNER JOIN pedido AS p
        ON p.id = m.id_pedido
        INNER JOIN proveedor AS prov
        ON prov.id = p.id_proveedor
        LEFT JOIN ct_productos_insumos AS prods
        ON prods.id_insumo = m.id_producto
        LEFT OUTER JOIN ct_productos cp
        ON cp.id = prods.id_producto
        LEFT OUTER JOIN producto px
        ON px.id = prods.id_insumo
        WHERE p.id = '$inv_pedido->id'
        AND m.estado =1 AND  m.id_detalle_pedido IS NOT NULL
        GROUP BY prods.codigo_producto,m.serie";

        $productos = DB::select($sql);
        $comboProductos = '';
        $comboBodegas = '';
        if (count($productos) > 0) {
            $id_empresa     = $request->session()->get('id_empresa');
            $bodega = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $table = '';
            //$table = '<tbody id="agregar_cuentas">';
            $lista_productos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->get();
            foreach ($lista_productos as $value1) {
                $selected = '';
                $comboProductos .= '<option ' . $selected . ' value="' . $value1->nombre . '" data-name="' . $value1->nombre . '" data-codigo="' . $value1->codigo . '" data-descuento="' . $value1->mod_desc . '" data-precio="' . $value1->mod_precio . '" data-maxdesc="' . $value1->descuento . '" data-iva="' . $value1->iva . '">' . $value1->codigo . '|' . $value1->nombre . '</option>';
            }
            foreach ($bodega as $value2) {
                $selected = '';
                $comboBodegas .= '<option ' . $selected . ' value="' . $value2->id . '">' . $value2->nombre . '</option>';
            }

            $contador = 0;
            $total = 0;
            $totales = 0;
            $subtotal = 0;
            $subtotal0 = 0;
            $subtotal12 = 0;
            $total_iva = 0;
            $sub0 = 0;
            $sub12 = 0;
            $base = 0;
            $descuento = 0;
            $descuento_total = 0;
            foreach ($productos as $value) {
                // dd($value);
                $subtotal1 = 0;
                $total = $value->cantidad_total * $value->precio;
                $descuento = $total * $value->descuentovalor;
                $descuentop = 0;
                $descuento_total += $descuento;
                if (!is_null($value->descuentovalor)) {
                    $descuentop = $value->descuentovalor / 100;
                }

                $iva = 0;
                $subtotal1 = $total - $descuento;
                $base += $subtotal;
                $check = '';

                if ($value->iva == '1') {
                    $check = 'checked';
                    $iva = $subtotal1 * $this->porIva->iva;
                    $subtotal12 += $total - $descuento + $iva;
                    //$subtotal12 += $value->subtotal_12;
                } else {
                    $subtotal0 += $total - $descuento;
                    //$subtotal0 += $value->subtotal_0;
                }
                $totales += $subtotal12 + $subtotal0;
                $total_iva += $iva; //cambio_pedido
                $table .= '<tr class="wello">
                    <td style="max-width:100px;">
                        <Input type="hidden" name="codigo[]" class="codigo_producto" />
                        <select name="nombre[]" class="form-control select2_cuentas" id="producto_' . $value->id_producto . '" style="width:100%" required onchange="verificar(this)">
                        <option value="">--Seleccione--</option>' . $comboProductos;

                $table .= '</select>
                        <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">' . 'serie: ' . $value->serie . '</textarea>
                        <input type="hidden" name="iva[]" class="iva" ' . $check . '/>
                    </td>
                    <td>
                        <input class="form-control text-right cneto" id="cantidad' . $contador . '" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $value->cantidad_total . '" name="cantidad[]" required>
                        <select name="bodega[]" class="form-control select2_bodega bodega" id="bodega_' . $value->id_producto . '" style="width: 80%;margin-top: 5px;" required>
                            <option value="">--Seleccione--</option>' . $comboBodegas;

                $table .= '</select>
                    </td>
                    <td id="tprecio" style="max-width:100px;">
                        <input type="text" class="form-control pneto" id="precio_0" onblur="calcularCompra(0)" style="width: 80%;height:20px;" name="precio[]" placeholder="0.00" value="' . $value->precio . '">
                    </td>
                    <td>
                        <input class="form-control text-right" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $descuentop . '" name="descpor[]" required>
                        <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="' . $value->descuentovalor . '" name="maxdesc[]" required>
                    </td>
                    <td>
                        <input class="form-control text-right descuento_" id="descuento_0" onchange="descuentoCal(this)" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);calcularCompra(0);" value="' . $value->descuentovalor . '" name="desc[]" required>
                    </td>
                    <td>
                        <input class="form-control px-1 text-right total_" id="total_0" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="' . $subtotal1 . '" onblur="this.value=parseFloat(this.value).toFixed(2);calcularCompra(0);" name="precioneto[]" required>
                    </td>
                    <td>
                        <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" ' . $check . ' disabled>
                    </td>
                    <td>
                        <button id="eliminar1" type="button" class="btn btn-danger btn-gray delete">
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
                <tr style="display:none" id="mifila">
                </tr>';
                $table .= '<script>
                    $(document).ready(function(){
                        $("#producto_' . $value->id_producto . '").val(\'' . $value->nombre . '\').trigger("change");
                        $("#bodega_' . $value->id_producto . '").val(\'' . $value->id_bodega . '\').trigger("change");
                    });
                    </script>';
                $contador++;
            }
            $subtotal12 = $subtotal12 / 1.12;
            $data = [
                $productos[0]->id_proveedor, //0
                $productos[0]->fecha, //1
                $productos[0]->vencimiento, //2
                $table, //3
                $productos[0]->direccion, //4
                $productos[0]->id_cuentas, //5
                $productos[0]->id_empresa, //6
                $contador, //7
                number_format(round($subtotal12, 2, PHP_ROUND_HALF_UP), 2), //8
                number_format(round($subtotal0, 2, PHP_ROUND_HALF_UP), 2), //9
                number_format(round($descuento_total, 2, PHP_ROUND_HALF_UP), 2), //10
                number_format(round(($subtotal12 * $this->porIva->iva), 2, PHP_ROUND_HALF_UP), 2), //11
                number_format(round(($subtotal0 + $subtotal12), 2, PHP_ROUND_HALF_UP), 2), //12
                number_format(round(($subtotal0 + $subtotal12) - ($descuento_total) + ($subtotal12 * $this->porIva->iva), 2, PHP_ROUND_HALF_UP), 2) //13
            ];
            return $data;
        } else {
            return ['no resultados'];
        }
    }

    public static function agregarElement($datas)
    {
        $contador = 0;
        $trHtml = "";
        $detaP = "";
        $productos = Ct_productos::where("id_empresa", Session::get('id_empresa'))->where("estado_tabla", 1)->get();
        foreach ($productos as $value) {
            $detaP .= '<option value="' . $value->codigo . '" data-name="' . $value->nombre . '" data-codigo="' . $value->codigo . '" data-descuento="' . $value->mod_desc . '" data-precio="' . $value->mod_precio . '" data-maxdesc="' . $value->descuento . '" data-iva="' . $value->iva . '">' . $value->codigo . ' | ' . $value->nombre . '</option>';
        }

        //dd($datas[0]);

        //$bodegas = Ct_Bodegas::where("id_empresa", Session::get('id_empresa'));
        $bodegas        = Ct_Bodegas::where('estado', '1')->where('id_empresa', Session::get('id_empresa'))->get();

        $arrBod = "";

        foreach ($datas as $data) {
            $selected = "";
            foreach ($bodegas as $bod) {
                if ($data->id_bodega == $bod->id) {
                    $selected = "selected";
                }
                $arrBod .= "<option {$selected} value='{$bod->id}'>{$bod->nombre}</option>";
            }
            $extendido = $data->precio * $data->cantidad_total;

            $trHtml .= "
            <tr style='' id='mifila'>
                <td style='max-width:100px;'>
                    <input type='hidden' name='codigo[]' class='codigo_producto' />
                        <select id='f{$contador}' name='nombre[]' class='form-control select2s' style='width:100%' required onchange='verificar(this)'>
                            <option value=''> </option>
                            {$detaP}
                        </select>
                    <textarea rows='3' name='descrip_prod[]' class='form-control px-1 desc_producto' placeholder='Detalle del producto'></textarea>
                    <input type='hidden' name='iva[]' class='iva' />
                </td>';
                <td>
                    <input class='form-control text-right cneto' type='text' style='width: 80%;height:20px;' onkeypress='return isNumberKey(event)' onblur='this.value=parseFloat(this.value).toFixed(0);' value='{$data->cantidad_total}' name='cantidad[]' required>
                    <select id='b_{$contador}' name='bodega[]' class='form-control select2_bodega bodega' style='width: 80%;margin-top: 5px;' required >
                        <option> </option>
                        {$arrBod}
                    </select>
                </td>
                <td>
                    <input type='text' class='pneto form-control' name='precio[]' style='width: 80%;height:20px;' value='{$data->precio}' placeholder='0.00'>
                </td>
                <td>
                    <input class='form-control text-right pdesc' type='text' style='width: 80%;height:20px;' onkeypress='return isNumberKey(event)' onblur='this.value=parseFloat(this.value).toFixed(0);' value='0' name='descpor[]' required>
                    <input class='form-control text-right maxdesc' type='hidden' style='width: 80%;height:20px;' onkeypress='return isNumberKey(event)' onblur='this.value=parseFloat(this.value).toFixed(0);' value='{$data->descuentoporct}' name='maxdesc[]' required>
                </td>';
                <td>
                    <input class='form-control text-right desc' type='text' style='width: 80%;height:20px;' onkeypress='return isNumberKey(event)' onblur='this.value=parseFloat(this.value).toFixed(2);' value='{$data->descuentovalor}' name='desc[]' required>
                </td>
                <td>
                    <input class='form-control px-1 text-right' type='text' style='height:20px;' onkeypress='return isNumberKey(event)' onblur='this.value=parseFloat(this.value).toFixed(2);' name='precioneto[]' value='{$extendido}' required>
                </td>
                <td>
                    <input class='form' type='checkbox' style='width: 80%;height:20px;' name='valoriva[]' disabled>
                </td>
                <td>
                    <button type='button' class='btn btn-danger btn-gray delete'>
                        <i class='glyphicon glyphicon-trash' aria-hidden='true'></i>
                    </button>
                </td>
            </tr>";
            $contador++;
        }

        return ["tabla" => $trHtml, "contador" => $contador];

        //dd($trHtml);

    }

    public function codigo2(Request $request)
    {
        $codigo     = $request['codigo'];
        $data       = null;
        $id_empresa = $request['id_empresa'];
        $productos  = DB::table('ct_productos')->where('codigo', $codigo)->where('id_empresa', $id_empresa)->first();

        $iva_producto = DB::table('producto')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->nombre, 'iva' => $productos->iva];
        } else {
            return ['value' => 'no'];
        }
    }
    public function codigo(Request $request)
    {

        $codigo     = $request['term'];
        $data       = array();
        $id_empresa = $request['id_empresa'];
        $productos  = DB::table('ct_productos')->where('codigo', 'like', '%' . $codigo . '%')->where('id_empresa', $id_empresa)->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function buscar_factura(Request $request)
    {
        $factura   = $request['id_factura'];
        $pedido    = $request['id_pedido'];
        $data      = null;

        /*

        SELECT * FROM movimiento m
        LEFT JOIN ct_productos_insumos ctpi ON m.id_producto = ctpi.id_insumo
        JOIN  pedido p ON m.id_pedido=p.id
        JOIN proveedor pro ON p.id_proveedor = pro.id
        JOIN ct_productos AS cp ON ctpi.id_producto=cp.id
        GROUP BY m.id_producto
        */

        $productos = DB::table('movimiento as m')
            ->selectRaw(
                'GROUP_CONCAT(m.id_producto LIMIT 1) as id_producto,
         GROUP_CONCAT(m.serie LIMIT 1) as serie,
         GROUP_CONCAT(cp.codigo LIMIT 1) as codigo,
         GROUP_CONCAT(cp.nombre LIMIT 1) as nombre,
         GROUP_CONCAT(p.id_proveedor LIMIT 1) as id_proveedor,
         GROUP_CONCAT(prov.razonsocial LIMIT 1) as razonsocial,
         GROUP_CONCAT(p.fecha LIMIT 1) as fecha,
         GROUP_CONCAT(p.vencimiento LIMIT 1) as vencimiento,
         GROUP_CONCAT(prov.id_cuentas LIMIT 1) as id_cuentas,
         GROUP_CONCAT(ctpi.codigo_producto LIMIT 1) as codigo_producto,
         GROUP_CONCAT(m.id_bodega LIMIT 1) as id_bodega,
         GROUP_CONCAT(cp.iva LIMIT 1) as iva,
         GROUP_CONCAT(prov.direccion LIMIT 1) as direccion,
         GROUP_CONCAT(p.id_empresa LIMIT 1) as id_empresa,
         GROUP_CONCAT(p.pedido LIMIT 1) as pedido'
            )
            ->leftjoin('ct_productos_insumos as ctpi', 'm.id_producto', '=', 'ctpi.id_insumo')
            ->join('pedido as p', 'm.id_pedido', 'p.id')
            ->join('proveedor as prov', 'p.id_proveedor', '=', 'prov.id')
            ->leftjoin('ct_productos as cp', 'ctpi.id_producto', '=', 'cp.id')
            ->where('p.factura', 'like', '%' . $factura . '%')
            ->groupBy("m.id_producto")
            ->get();
        if (count($productos) > 0) {

            $data = ['id_proveedor' => $productos[0]->id_proveedor, 'fecha' => $productos[0]->fecha, 'fecha_vencimiento' => $productos[0]->vencimiento, 'productosArray' => $productos, 'direccion_producto' => $productos[0]->direccion, 'id_cuenta' => $productos[0]->id_cuentas, 'id_empresa' => $productos[0]->id_empresa];

            return $data;
        } else {

            return ['value' => 'no resultados'];
        }
    }

    public function anular_factura_compras($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $act_estado = [
            'estado'          => '0',
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $fechahoy = Date('Y-m-d H:i:s');
        Ct_compras::where('id', $id)->update($act_estado);
        //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
        $compras      = Ct_compras::where('id', $id)->first();
        $arrayPedido = [
            'estado_contable' => 0
        ];
        Pedido::where('id_empresa', session('id_empresa'))
            ->where('pedido', $compras->pedido)
            ->where('id_proveedor', $compras->proveedor)
            ->update($arrayPedido);
        $contador_ctv = DB::table('ct_compras')->get()->count();
        $id_empresa   = $request->session()->get('id_empresa');

        $cabecera  = Ct_Asientos_Cabecera::where('id', $compras->id_asiento_cabecera)->first();
        $actualiza = [
            'estado'          => 1,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $cabecera->update($actualiza);
        $detalles   = $cabecera->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => strtoupper($request['observacion']),
            'fecha_asiento'   => $cabecera->fecha_asiento,
            'id_empresa'      => $id_empresa,
            'fact_numero'     => $cabecera->secuencia,
            'valor'           => $cabecera->valor,
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
                'fecha'               => $cabecera->fecha_asiento,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        Log_Contable::create([
            'tipo'           => 'C',
            'valor_ant'      => $cabecera->valor,
            'valor'          => $cabecera->valor,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
            'observacion'    => $cabecera->concepto,
            'id_ant'         => $cabecera->id,
            'id_referencia'  => $id_asiento,
        ]);
        return redirect()->intended('/contable/compras');
    }

    public function identificacion(Request $request)
    {

        $codigo      = $request['term'];
        $data        = array();
        $proveedores = DB::table('proveedor')->where('id', 'like', '%' . $codigo . '%')->get();
        foreach ($proveedores as $prov) {
            $data[] = array('value' => $prov->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function buscar_proveedor(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('proveedor')->where('id', $codigo)->first();
        $idusuario = Auth::user()->id;

        if (!is_null($productos)) {
            return ['value' => $productos->razonsocial, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion];
        } else {
            return ['value' => 'no'];
        }
    }

    public function nombre_proveedor(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('proveedor')->where('razonsocial', 'like', '%' . $nombre . '%')->get();
        $idusuario = Auth::user()->id;
        if ($idusuario == "0957258056") {
            //dd($productos);
        }
        foreach ($productos as $product) {
            $data[] = array('value' => $product->razonsocial, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function nombre(Request $request)
    {

        $nombre     = $request['term'];
        $id_empresa = $request['id_empresa'];
        $data       = array();
        $productos  = DB::table('ct_productos')->where('nombre', 'like', '%' . $nombre . '%')->where('id_empresa', $id_empresa)->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function nombre2(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $productos    = DB::table('ct_productos')->where('nombre', 'like', "%$nombre%")->first();
        $iva_producto = DB::table('producto')->where('nombre', $nombre)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->codigo, 'iva' => $productos->iva];
        } else {
            return ['value' => 'no'];
        }
    }

    public function _____buscar_nombreproveedor(Request $request)
    {
        $nombre = $request['nombre'];

        $data      = null;
        $productos = DB::table('proveedor')->where('id', $nombre)->first();
        $idusuario = Auth::user()->id;

        if ($productos != '') {

            $detalle = Ct_Detalle_Acreedores::where('id_proveedor', $productos->id)->first();
            if ($detalle == '') {
                //esto es tu cagada tursi era este if y comentaste lalinea que no
                return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion, 'caducidad' => ''];
            }
            return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion, 'caducidad' => $detalle->f_caducidad];
        } else {
            return ['value' => 'no'];
        }
    }

    public function buscar_nombreproveedor(Request $request)
    {
        $nombre = $request['nombre'];

        $productos = DB::table('proveedor')->where('id', $nombre)->first();
        if (!is_null($productos)) {

            // $detalle = Ct_Detalle_Acreedores::where('id_proveedor', $productos->id)->first();
            $detalle = Ct_Detalle_Acreedores::where('id_proveedor', $productos->id)->get();

            $compras = Ct_compras::where("proveedor", $productos->id)->where("id_empresa", $request->session()->get('id_empresa'))->where("estado", ">", "0")->get();
            $num_mayor = 0;
            foreach ($compras as $compra) {
                if (intval($compra->secuencia_factura) > $num_mayor) {
                    $num_mayor =  intval($compra->secuencia_factura);
                }
            }


            if (count($detalle) == 0) {
                return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => ''];
            }

            foreach ($detalle as $value) {

                if ($num_mayor >= intval($value->sinicia)  and $num_mayor <= intval($value->sfin)) {
                    //dd("entra");
                    return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $value->serie, 'autorizacion' => $value->autorizacion, 'caducidad' => ''];
                }
            }

            return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => ''];

            //return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $detalle->serie, 'autorizacion' => $detalle->autorizacion, 'caducidad' => $detalle->f_caducidad];
        } else {
            return ['value' => 'no'];
        }
    }

    public function subir_masivo(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $data_array = array(
            "tipo"                    => "MEM-VEN", //tipo de de orden de venta
            "fecha"                   => "2021-08-01", //fecha de orden
            "id_empresa"              => "0992704152001", //empresa
            "divisas"                 => "1", //cualquiera
            "nombre_cliente"          => "PAPI CHILAN", //nombre del cliente
            "tipo_consulta"           => "1", //esto clavado
            "identificacion_cliente"  => "1316262193", // ci de cliente
            "direccion_cliente"       => "MUCHO LOTE", //direccion del cliente
            "telefono_cliente"        => "132231", // telefono de cliente
            "mail_cliente"            => "tumarido@mail.com", //mail del cliente
            "orden_venta"             => "321", //idde referencia
            "identificacion_paciente" => "1316262193", // ci de paciente
            "nombre_paciente"         => "PAPI CHILAN", // nonbre de paciente
            "id_seguro"               => "1", //de la tabla seguros,
            "subtotal_01"             => "12.00",
            "subtotal_121"            => "12.00",
            "descuento1"              => "12.00",
            "tarifa_iva1"             => "12.00",
            "base"                    => "12.00",
            "totalc"                  => "12.00",
            "valor_contable"          => "12.00",
            "details"                 => array(
                array(
                    "codigo"     => "001",
                    "nombre"     => "HEY YOU",
                    "cantidad"   => "1",
                    "precio"     => "2.00",
                    "extendido"  => "3.00",
                    "iva"        => "1",
                    "descpor"    => "10",
                    "descuento"  => "10",
                    "copago"     => "10",
                    "detalle"    => "EXAMEN PAPI",
                    "precioneto" => "10",

                ),
            ),
            "id_usuario"              => "1316262193",
        );
        /*  $procedimientos= Procedimiento::where('estado',1)->get();
        $codigo=12;
        foreach($procedimientos as $p){
        $jp= Planilla_Procedimiento::where('id_procedimiento',$p->id)->first();

        if(is_null($jp)){
        $secuencia = str_pad($codigo, 3, "0", STR_PAD_LEFT);
        $insumo_id=Insumo_Plantilla_Control::insertGetId([
        'codigo'=>$secuencia,
        'nombre'=>$p->nombre,
        'estado'=>1,
        ]);
        Planilla_Procedimiento::create([
        'id_planilla'=>$insumo_id,
        'id_procedimiento'=>$p->id
        ]);
        }
        $codigo++;
        } */
        /*   $plan_cuentas= Plan_Cuentas::all();
        $empresa= Empresa::all();
        foreach($plan_cuentas as $p){
        //dd($p);
        foreach($empresa as $emp){
        Plan_Cuentas_Empresa::create([
        'id_plan'=>$p->id,
        'id_padre'        => $p->id_padre,
        'nombre'          => $p->nombre,
        'plan'            => $p->id,
        'estado'          => $p->estado,
        'id_empresa'      => $emp->id,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        }

        } */
        return 'ok, gracias amigo';
    }
    public function verificar_anulacion(Request $request)
    {
        $verificacion = $request['verificar'];
        $tabla        = "";
        $conf         = 0;
        $tiene        = "no";
        switch ($verificacion) {
            case '1':
                /***************************COMPROBANTE DE EGRESOS************************/
                $id = $request['id_compra'];
                $egreso = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $id)->get();
                $tabla_cruce = "";
                $id_cruce    = 0;
                if (count($egreso)) {
                    foreach ($egreso as $value) {
                        $cruce_detalle = DB::table('ct_detalle_cruce')->where('id_factura', $value->id_compra)->get();
                        if (count($cruce_detalle)) {
                            foreach ($cruce_detalle as $values) {
                                $cruce = Ct_Cruce_Valores::where('id', $values->id_comprobante)->where('estado', '1')->get();
                                if (count($cruce)) {
                                    $tiene       = "si";
                                    $tabla_cruce = "Cruce de valores";
                                    $id_cruce    = $cruce[0]->id;
                                }
                            }
                        }
                    }
                }
                $tablas = [$tabla_cruce];
                $ids    = [$id_cruce];
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];
                //return ['existe'=>$conf, 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene, 'id_egreso'];
                break;
            case '2':
                /**************************DEBITO BANCARIO**********************************/
                $id               = $request['id_compra'];
                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_debito', $id)->where('estado', "!=", "0")->get();
                $tiene            = "no";
                $tabla_cruce      = "";
                $id_cruce         = 0;
                if (count($debito_banca_det)) {
                    foreach ($debito_banca_det as $value) {
                        $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $value->id_compra)->get();
                        if (count($detalle_cruce)) {
                            foreach ($detalle_cruce as $values) {
                                $cruce = DB::table('ct_cruce_valores')->where('id', $values->id_comprobante)->where('estado', '1')->get();
                                if (count($cruce)) {
                                    $tiene       = "si";
                                    $tabla_cruce = "Cruce de valores";
                                    $id_cruce    = $cruce[0]->id;
                                }
                            }
                        }
                    }
                }
                $tablas = [$tabla_cruce];
                $ids    = [$id_cruce];
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];
                //return ['existe'=>"si", 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene];
                break;
            case '3':
                /**********************RETENCIONES*********************************/
                $tiene       = "no";
                $tabla       = "";
                $id          = $request['id_compra'];
                $tabla_egre  = "";
                $tabla_banca = "";
                $tabla_cruce = "";
                $tabla_rete  = "";
                $id_egreso   = 0;
                $retenciones = Ct_Retenciones::where('id', $id)->first();
                $com_egre    = Ct_Detalle_Comprobante_Egreso::where('id_compra', $retenciones->id_compra)->get();

                if (count($com_egre) > 0) {
                    foreach ($com_egre as $value) {
                        $egreso = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso) > 0) {
                            $tiene      = "si";
                            $tabla_egre = "Comprobante de Egreso ";
                            $id_egreso  = $egreso[0]->id;
                        }
                    }
                }

                $id_deb_ban       = 0;
                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra', $retenciones->id_compra)->get();

                if (count($debito_banca_det) > 0) {

                    foreach ($debito_banca_det as $value) {

                        $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                        if (count($debit_banca) > 0) {

                            $tiene       = "si";
                            $tabla_banca = "Debito Bancario ";
                            $id_deb_ban  = $debit_banca[0]->id;
                        }
                    }
                }

                $id_cruce_valores = 0;

                $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $retenciones->id_compra)->get();

                if (count($detalle_cruce) > 0) {
                    foreach ($detalle_cruce as $value) {
                        $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                        if (count($cruce_valores) > 0) {
                            $tiene            = "si";
                            $tabla_cruce      = "Cruce Valores ";
                            $id_cruce_valores = $cruce_valores[0]->id;
                        }
                    }
                }

                $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce];
                $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores];

                return ['id' => $retenciones->id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];

                break;
            case '4':
                $tabla_egre        = "";
                $tabla_banca       = "";
                $tabla_cruce       = "";
                $tabla_rete        = "";
                $tabla_egre_masivo = "";
                $tabla_cruce_cuentas = "";
                $id_egreso         = 0;
                $id_deb_ban        = 0;
                $id_cruce_valores  = 0;
                $id_retencion      = 0;
                $id_egreso_masivo  = 0;
                $id_cruce_cuentas  = 0;
                /*********************COMPRAS*****************************************/
                $id    = $request['id_compra'];
                $tiene = "";
                $tabla = "";

                $retenciones = Ct_Retenciones::where('id_compra', $id)->get();
                // if (Auth::user()->id == '0921605895') {
                //     echo '<pre>';
                //     print_r($retenciones);
                //     exit;                    
                // }
                if (count($retenciones) > 0) {
                    foreach ($retenciones as $value) {
                        if ($value->estado == '1') {
                            $tiene        = "si";
                            $id_retencion = $retenciones[0]->id;
                        }
                    }
                    if ($tiene == "si") {
                        $tabla_rete = "Retenciones ";
                    }
                }
                $com_egre = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
                // dd("Hola");

                if (count($com_egre) > 0) {
                    foreach ($com_egre as $value) {
                        $egreso = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso) > 0) {
                            $tiene      = "si";
                            $tabla_egre = "Comprobante de Egreso ";
                            $id_egreso  = $egreso[0]->id;
                        }
                    }
                }

                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra', $id)->get();
                if (count($debito_banca_det) > 0) {
                    foreach ($debito_banca_det as $value) {
                        $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                        if (count($debit_banca) > 0) {
                            $tiene       = "si";
                            $tabla_banca = "Debito Bancario ";
                            $id_deb_ban  = $debit_banca[0]->id;
                        }
                    }
                }

                $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $id)->get();
                if (count($detalle_cruce) > 0) {
                    foreach ($detalle_cruce as $value) {
                        $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                        if (count($cruce_valores) > 0) {
                            $tiene            = "si";
                            $tabla_cruce      = "Cruce Valores ";
                            $id_cruce_valores = $cruce_valores[0]->id;
                        }
                    }
                }

                $det_egreso_masivo = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_compra', $id)->get();

                if (count($det_egreso_masivo) > 0) {

                    foreach ($det_egreso_masivo as $value) {

                        $egreso_masivo = Ct_Comprobante_Egreso_Masivo::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso_masivo) > 0) {

                            $tiene             = "si";
                            $tabla_egre_masivo = "Comprobante de Egreso Masivo";
                            $id_egreso_masivo  = $egreso_masivo[0]->id;
                        }
                    }
                }

                $cruce_cuentas = Ct_Cruce_Cuentas::where('id_factura', $id)->where('estado', 1)->first();

                if (!is_null($cruce_cuentas)) {
                    $tabla_cruce_cuentas = "Cruce de Cuentas";
                    $id_cruce_cuentas = $cruce_cuentas->id;
                }


                $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce, $tabla_rete, $tabla_egre_masivo, $tabla_cruce_cuentas];
                $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores, $id_retencion, $id_egreso_masivo, $id_cruce_cuentas];
                //  dd($tablas);
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];

                break;
        }
    }
    public function nota_credito(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $proveedores = Proveedor::where('estado', 1)->get();
        //dd($request->all());
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor       = $request['id_proveedor'];
        if (is_null($request['desde']) && is_null($request['hasta'])) {
            $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        } else {

            if (!is_null($request['desde']) && !is_null($request['hasta'])) {
                $credito_acreedores = $credito_acreedores->whereBetween('fecha', [str_replace('/', '-', $request['desde']) . ' 00:00:00', str_replace('/', '-', $request['hasta']) . ' 23:59:59']);
            }
            if (!is_null($request['hasta']) && is_null($request['desde'])) {
                $credito_acreedores = $credito_acreedores->where('fecha', '<', $request['hasta']);
            }
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        }

        $credito_acreedores = $credito_acreedores->get();

        $empresa = Empresa::where('id', $id_empresa)->first();
        if ($request['excel'] == 1) {
            $excel = $this->getExcel($request);
        }
        //dd($credito_acreedores);
        return view("contable/acreedores_credito/nueva_vista", ['proveedores' => $proveedores, 'id_proveedor' => $id_proveedor, 'credito_acreedores' => $credito_acreedores, 'fecha_desde' => $request['desde'], 'fecha_hasta' => $request['hasta'], 'empresa' => $empresa]);
    }
    public function carga_logo(Request $request)
    {
        $id_empresa            = $request->session()->get('id_empresa');
        $id_proovedor          = $request['id_proveedor'];
        $desde                 = $request['desde'];
        $hasta                 = $request['hasta'];
        $logo                  = Proveedor::where('id', $id_proovedor)->first();
        $empresa               = Empresa::where('id', $id_empresa)->first();
        $ct_credito_acreedores = Ct_Credito_Acreedores::where('estado', 1)->get();
        //dd($ct_credito_acreedores);
        //dd($ct_credito_acreedores);
        return view("contable/acreedores_credito/logo", ['ct_credito_acreedores' => $ct_credito_acreedores, 'empresa' => $empresa, 'logo' => $logo, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function getExcel(Request $request)
    {
        $id_empresa         = $request->session()->get('id_empresa');
        $fecha_desde        = $request['desde'];
        $proveedor          = $request['id_proveedor'];
        $fecha_hasta        = $request['hasta'];
        $empresa            = Empresa::where('id', $id_empresa)->first();
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor       = $request['id_proveedor'];
        if (is_null($request['desde']) && is_null($request['hasta'])) {
            $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        } else {
            if (!is_null($request['desde']) && !is_null($request['hasta'])) {
                $credito_acreedores = $credito_acreedores->whereBetween('fecha', [str_replace('/', '-', $request['desde']) . ' 00:00:00', str_replace('/', '-', $request['hasta']) . ' 23:59:59']);
                //dd("dsada");
            }
            if (!is_null($request['hasta']) && is_null($request['desde'])) {
                $credito_acreedores = $credito_acreedores->where('fecha', '<', $request['hasta']);
            }
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $credito_acreedores = $credito_acreedores->get();
        Excel::create('INFORME NOTA DE CREDITO ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $credito_acreedores, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $credito_acreedores) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:K2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:K3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME NOTA DE CREDITO");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:K4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . $fecha_desde . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue("Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('NUMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C5:D5');
                $sheet->cell('C5', function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    $cell->setValue('PROVEEDOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('IMPUESTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));
                $i = $this->setDetalles($credito_acreedores, $sheet, 6, $fecha_hasta);
                $sheet->cells('A3:H3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:K5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->setWidth(array(
                    'A' => 17,
                    'B' => 17,
                    'C' => 17,
                    'D' => 17,
                    'E' => 17,
                    'F' => 18,
                    'G' => 17,
                    'H' => 17,
                    'I' => 17,
                    'J' => 17,
                    'K' => 17,
                ));
            });
        })->export('xlsx');
    }
    public function setDetalles($consulta, $sheet, $i, $fecha_hasta)
    {
        $acumulador = 0;
        $subtotal0  = 0;
        $subtotal12 = 0;
        $impuesto   = 0;
        $subtotal   = 0;

        foreach ($consulta as $value) {
            $acumulador += $value->valor_contable;
            $subtotal0 += $value->subtotal_0;
            $subtotal12 += $value->subtotal_12;
            $impuesto += $value->impuesto;
            $subtotal += $value->subtotal;

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(date('d-m-Y', strtotime($value->fecha)));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->secuencia);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('C' . $i . ':D' . $i);
            $sheet->cell('C' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->concepto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->compra->proveedorf->razonsocial);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('F' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->compra->numero);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            /*$sheet->cell('G' . $i, function ($cell) use ($value) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($value->impuesto);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });*/
            $sheet->cell('G' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                if (is_null($value->subtotal_0)) {
                    $cell->setValue("0,00");
                    $cell->setAlignment('right');
                } else {
                    $cell->setValue($value->subtotal_0);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                if (is_null($value->subtotal_12)) {
                    $cell->setValue("0,00");
                    $cell->setAlignment('right');
                } else {
                    $cell->setValue($value->subtotal_12);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('I' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->subtotal);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('J' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->impuesto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('K' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->valor_contable);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $i++;
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue('TOTAL:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('G' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal0);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('H' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal12);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($impuesto);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('K' . $i, function ($cell) use ($acumulador) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($acumulador);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('I5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('J5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });

        return $i;
    }

    public function masivo_subtotal()
    {

        $acreedores = Ct_Credito_Acreedores::all();

        foreach ($acreedores as $value) {
            $credito = Ct_Credito_Acreedores::find($value->id);

            if ($credito->impuesto > 0) {
                $credito->subtotal_12 = $credito->subtotal;
                $credito->subtotal_0  = 0;
            } else {
                $credito->subtotal_0  = $credito->subtotal;
                $credito->subtotal_12 = 0;
            }

            $credito->save();
        }

        return ("ok gracias amigo");
    }

    public function subirpdf($id, $parametro)
    {
        return view("contable/compra/modalpdf", ['id' => $id, 'parametro' => $parametro]);
    }

    public function guardarpdf(Request $request)
    {
        //dd($request->all());
        $id              = $request['id'];
        $nombre_original = $request['file']->getClientOriginalName();
        $extension       = $request['file']->getClientOriginalExtension();
        $nombre_guardar  = $nombre_original . date('YmdHis');
        $r1              = Storage::disk('public')->put($nombre_guardar, \File::get($request['file']));
        $rutadelaimagen  = $nombre_guardar;
        $editar          = DB::table('ct_compras')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]);
        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        if ($request->parametro == 1) {
            return redirect()->route('compras_index');
        } else {
            return redirect()->route('fact_contable_index');
        }
    }
    /* public function pdf_visualizar($id)
    {
    $visualizar = Ct_compras::find($id);
    $ver        = $visualizar->rutapdf;
    $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
    //dd($path);
    return Response::make(file_get_contents($path), 200, [
    'Content-Type'        => 'application/pdf',
    'Content-Disposition' => 'inline; filename="' . $ver . '"',
    ]);
    }*/
    public function pdf_visualizar_nuevo(Request $request)
    {
        $visualizar = Ct_compras::where('id', $request['id'])->first();
        $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
        // $nombreArchivo = TicketPermiso::where('id', $request['id'])->first();
        // $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->ruta_archivo;
        //dd($path);
        return response()->file($path);
    }
    public function anularpdf($id, $validate)
    {
        $visualizar = Ct_compras::find($id);
        $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
        Storage::disk('public')->delete($visualizar->rutapdf);
        $visualizar->rutapdf = null;
        $visualizar->save();
        if ($validate == 1) {
            return redirect()->route('compras_index');
        } else {
            return redirect()->route('fact_contable_index');
        }
    }
    public function buscador_master_tipos(Request $request)
    {
        if ($request['id'] == '03') {
            return (Proveedor::where('estado', '1')->where('tipo', '!=', 4)->get());
        }
        return (Proveedor::where('estado', '1')->get());
    }
    public function buscador_proveedor(Request $request)
    {
        $proveedor = Proveedor::find($request['id']);;
        $arr_proveedor = [
            'tipo' => $request['tipo_identificacion'],
            'id' => $request['id'],
            'nombre' => $request['nombre'],
        ];
        $proveedor->update($arr_proveedor);
        return view('contable/compras/index');
        //return response()->json($cliente);
    }
    public function editar_proveedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $data['controlador'] = $this->controlador;
        $data['id_empresa'] = $request->session()->get('id_empresa');
        $data['empresa']    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $data['compra'] = Ct_compras::find($request->id);
        $data['proveedor'] = Proveedor::find($data['compra']->proveedor);
        //dd($proveedor);
        return view('contable/compra/editarCabeceraCompras', $data);
        //return response()->json($cliente);
    }

    public function masivoAsientoDetalle($anio, $mes = 0, $dia = 0)
    {
        try {
            $idusuario = null;
            $asientos = Ct_Asientos_Cabecera::leftjoin('ct_asientos_detalle as ad', 'ct_asientos_cabecera.id', '=', 'ad.id_asiento_cabecera')
                ->join('ct_compras as c', 'c.id_asiento_cabecera', '=', 'ct_asientos_cabecera.id')
                ->where('ad.id_asiento_cabecera', null)
                ->where(function ($q) use ($anio, $mes, $dia) {
                    if ($anio != 0) {
                        $q->whereYear('ct_asientos_cabecera.fecha_asiento', $anio);
                    }
                    if ($mes != 0) {
                        $q->whereMonth('ct_asientos_cabecera.fecha_asiento', $mes);
                    }
                    if ($dia != 0) {
                        $q->whereDay('ct_asientos_cabecera.fecha_asiento', $dia);
                    }
                })
                ->get(['ct_asientos_cabecera.*']);
            //dd($asientos);
            foreach ($asientos as $asiento) {
                $compra = Ct_compras::where('id_asiento_cabecera', $asiento->id)->first();
                //dd($compra);
                if ($compra != '') {
                    //$id_empresa = $compra->id_empresa;
                    if ($compra->id_usuariocrea != '') {
                        $idusuario = $compra->id_usuariocrea;
                    } else {
                        $idusuario = '0950457978';
                    }

                    if ($compra->ip_creacion != '') {
                        $ip_cliente = $compra->ip_creacion;
                    } else {
                        $ip_cliente = '::1';
                    }
                    $request = [];
                    $request['tarifa_iva1'] = $compra->iva_total;
                    $request['descuento1'] = $compra->descuento;
                    $request['base1'] = $compra->subtotal;
                    $request['total1'] = $compra->total_final;
                    $request['proveedor'] = $compra->proveedor;
                    $subtotalf = $request['base1'];
                    $fechahoy = date('Y-m-d H:i:s');
                    //dd($request);
                    $asientos = $this->asientos($request, $asiento->id, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $request['total1']);
                }
            }
        } catch (Exception $ex) {
            DB::rollBack();
            echo 'Mensaje: ' . $ex->getMessage() . ' Linea: ' . $ex->getLine();
            exit;
        }
    }
}
