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
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_detalle_factura_contable;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Empresa;
use Sis_medico\Ct_Divisas;
use Sis_medico\Bodega;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Kardex;
use Sis_medico\Marca;
use laravel\laravel;
use Sis_medico\Validate_Decimals;
use Carbon\Carbon;
use Exception;
use Sis_medico\Ct_Inv_Interno;
use Sis_medico\Ct_Inv_Kardex;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Producto;
use Sis_medico\Inventario;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Utilidades;

class Fact_ContableController extends Controller
{
    private $controlador = 'compras';
    private $porIva = '';
    public function __construct()
    {
        $this->middleware('auth');
        $this->porIva = Utilidades::porcentajeIva();
        if ($this->porIva == '') {
            $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
            return view('errors/204', $data);
        }
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        config(['data' => []]);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $data['controlador'] = $this->controlador;
        $data['page'] = $request->page == '' ? 0 : $request->page;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $compras = DB::table('ct_compras as ct_c')
            ->leftjoin('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->select('ct_c.id', 'ct_c.rutapdf', 'ct_c.numero', 'ct_c.fecha', 'ct_c.observacion', 'ct_c.f_autorizacion', 'u.nombre1', 'u.apellido1', 'p.razonsocial', 'p.id as id_proveedor', 'ct_c.autorizacion', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.id_asiento_cabecera', 'ct_c.doc_electronico', 'ct_c.nro_autorizacion', 'ct_c.electronica')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 2)
            ->orderBy('ct_c.id', 'desc')
            ->paginate(10);
        $proveedor = Proveedor::where('estado', '1')->get();
        config(['data' => $data]);
        return view('contable/factura_contable/index', ['compras' => $compras, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
    }
    public function buscar_proveedor(Request $request)
    {
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('nombrecomercial', 'LIKE', '%' . $request['search'] . '%')->select('id as id', 'nombrecomercial as text')->get();
        }
        return response()->json($proveedor);
    }

    public function verificarStock(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $producto = Ct_Productos::where('codigo', $request->cod_producto)->first();
        $empresa = Empresa::find($id_empresa);
        if ($empresa->inventario == 0 && $empresa->egreso_kardex == 0)
            return ['msj' => 'Si hay existencia', 'respuesta' => 'si', 'cant' => 9999999];
        $cantidad = Inventario::stock($producto->id, $request->bodega, $empresa->id);
        if ($cantidad > 0 and $cantidad >= $request->cantidad) {
            return ['msj' => 'Si hay existencia', 'respuesta' => 'si', 'cant' => $cantidad];
        } else if ($cantidad > 0 and $cantidad < $request->cantidad) {
            return ['msj' => "La bodega solo tiene: {$cantidad} item,  de la cantidad solicitada: {$request->cantidad} ", 'respuesta' => 'no', 'cant' => $cantidad];
        } else {
            return ['msj' => 'No hay productos en existencia', 'respuesta' => 'no', 'cantidad' => $cantidad];
        }
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $bodega = bodega::where('estado', '1')->get();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $tipo_iva = ct_master_tipos::where('estado', '1')->where('tipo', '3')->get();
        $tipo_pago = Ct_Forma_Pago::where('estado', '1')->get();
        $termino = Ct_Termino::where('estado', '1')->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $empresas = DB::table('empresa')->where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('p.plan as id', 'p.nombre as nombre')->get();
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();
        return view('contable/factura_contable/create', ['porIva' => $this->porIva, 'divisas' => $divisas, 'cuentas' => $cuentas, 'termino' => $termino, 'id_empresa' => $id_empresa, 'empresa' => $empresa, 'empresas' => $empresas, 'tipo_pago' => $tipo_pago, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'bodega' => $bodega, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'tipo_iva' => $tipo_iva, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'sucursales' => $sucursales, 'punto' => $punto]);
    }

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fechahoy = $request['f_autorizacion'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $numero_factura = 0;
        $objeto_validar = new Validate_Decimals();
        DB::beginTransaction();
        try {
            $total_final = $objeto_validar->set_round($request['total_final1']);
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            if (!is_null($cod_sucurs)) {
                $sucursal = $cod_sucurs->codigo_sucursal;
            } else {
                $sucursal = "0";
            }
            $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            if (!is_null($cod_caj)) {
                $punto_emision = $cod_caj->codigo_caja;
            } else {
                $punto_emision = "0";
            }
            $total_final = $objeto_validar->set_round($request['total1']);
            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)
                ->get()
                ->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                if ($max_id != '') {
                    $max_id = intval($max_id->secuencia_f);
                    if (strlen($max_id) < 10) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                } else {
                    $nu = 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado = $request['serie'] . '-' . $request['secuencia_factura'];
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('proveedor', $request['proveedor'])
                ->where('tipo_comprobante', $request['tipo_comprobante'])
                ->where('id_empresa', $id_empresa)
                ->where('estado', '!=', '0')
                ->first();
            $subtotalf = $request['base1'];
            if ($comprobacion_compra == '') {
                $input = [
                    'observacion'                   => $request['observacion'],
                    'fecha_asiento'                 => $request['fecha'],
                    'fact_numero'                   => $request['secuencia_factura'],
                    'valor'                         => $total_final,
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'sucursal'                      => $sucursal,
                    'punto_emision'                 => $punto_emision,
                    'aparece_sri'                   => $request['archivosri'],
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                if ($id_empresa != '0992704152001') {
                    $id_proveedor = $request['proveedor'];
                    $proveedor_find = Proveedor::find($id_proveedor);
                    if (!is_null($proveedor_find)) {
                        $cabeceraa = [
                            'observacion'     => $proveedor_find->razonsocial . ' # ' . $numeroconcadenado,
                            'fecha_asiento'   => $request['fecha'],
                            'fact_numero'     => $request['secuencia_factura'],
                            'valor'           => $total_final,
                            'id_empresa'      => $id_empresa,
                            'estado'          => '1',
                            'aparece_sri'     => $request['archivosri'],
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'sucursal'        => $sucursal,
                        ];
                    }
                }
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input);
                $consulta_fecha = Ct_Termino::where('id', $request['termino'])->first();
                $modfecha = "";
                if ($consulta_fecha != null) {
                    $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($request['fecha']));
                    $modfecha = date("Y-m-d", $nueva_fecha);
                }
                $t = null;
                if ($request['tipo_comprobante'] == '03') {
                    $t = 0;
                }
                $sucursal = Ct_Sucursales::find($request['sucursal']);
                $caja = Ct_caja::find($request['punto_emision']);
                $input = [
                    'doc_electronico' => $t,
                    'tipo' => $request['tipo'],
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $request['fecha'],
                    'numero' => $numeroconcadenado,
                    'archivo_sri' => $request['archivosri'],
                    'proveedor' => $request['proveedor'],
                    'termino' => $request['termino'],
                    'secuencia_f' => $numero_factura,
                    'observacion' => $request['observacion'],
                    'tipo' => '2',
                    'sucursal' => $sucursal->codigo_sucursal,
                    'punto_emision' => $caja->codigo_caja,
                    'valor_contable' => $total_final,
                    'fecha_termino' => $modfecha,
                    'orden_compra' => $request['o_compra'],
                    'f_caducidad' => $request['f_caducidad'],
                    'tipo_gasto' => $request['tipo_gasto'],
                    'autorizacion' => $request['autorizacion'],
                    'f_autorizacion' => $request['f_autorizacion'],
                    'id_empresa' => $id_empresa,
                    'serie' => $request['serie'] != '' ? $request['serie'] : $sucursal . '-' . $punto_emision,
                    'secuencia_factura' => $request['secuencia_factura'],
                    'credito_tributario' => $request['credito_tributario'],
                    'tipo_comprobante' => $request['tipo_comprobante'],
                    'subtotal_0' => $request['subtotal_01'],
                    'subtotal_12' => $request['subtotal_121'],
                    'subtotal' => $subtotalf,
                    'descuento' => $request['descuento1'],
                    'iva_total' => $request['tarifa_iva1'],
                    'ice_total' => $request['ice_final1'],
                    'electronica' => $request['tipo_electronica'],
                    'total_final' => $total_final,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'forma_pago' => $request['forma_pago'],
                ];
                $id_compra = Ct_compras::insertGetId($input);
                $arr_total = [];
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $arr = [
                            'nombre' => $request->input("nombre")[$i],
                            'cantidad'       => $request->input("cantidad")[$i],
                            'codigo'         => $request->input("codigo")[$i],
                            'precio'         => $request->input("precio")[$i],
                            'descpor'        => $request->input("descpor")[$i],
                            'descuento'      => $request->input("desc")[$i],
                            'precioneto'     => $request->input("precioneto")[$i],
                            'detalle'        => $request->input("descrip_prod")[$i],
                            'iva'            => $request->input("iva")[$i],
                        ];
                        array_push($arr_total, $arr);
                    }
                }
                $cuentas_iva = [];
                foreach ($arr_total as $valor) {
                    $detalle = [
                        'id_ct_compras'        => $id_compra,
                        'codigo'               => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
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
                    if (Auth::user()->id == "0921605895") {
                        echo '<pre>';
                        print_r($detalle);
                        DB::rollBack();
                        exit;
                    }
                    Ct_detalle_compra::create($detalle);
                    $fc = $valor['cantidad'] * $valor['precio'];
                    $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', "LIKE", "%{$valor['codigo']}%")->orWhere('plan', "LIKE", "%{$valor['codigo']}%")->where('id_empresa', $id_empresa)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        // 'id_plan_cuenta'                => $valor['codigo'],
                        'id_plan_cuenta'                => $plan_empresa->id_plan,
                        'descripcion'                   => $valor['nombre'],
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => $fc,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                /*if (Auth::user()->id == '0921605895') {
                    DB::rollBack();
                    exit;
                }*/
                $valor_iva = $request['tarifa_iva1'];
                $globales  = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
                if ($globales == '') {
                    return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.registre_cuentas_globales') . ' ' . $id_empresa];
                }
                //1.01.05.01.02
                if ($valor_iva > 0) {
                    // $plan_cuentas = Plan_Cuentas::where('id', '5.2.02.16.15')->first();
                    $plan = Plan_Cuentas::find($globales->debe);
                    if ($plan == '') {
                        return ['status' => 'error', 'msj' => '¡Error! ' . trans('contableM.registre_plan_cuentas') . ' ' . $id_empresa];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        // 'id_plan_cuenta'                => '5.2.02.16.15',
                        'id_plan_cuenta'                => $plan->id,
                        'descripcion'                   => $plan->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => number_format($request['tarifa_iva1'], 2, '.', ''),
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $consulta_proovedor = Proveedor::where('id', $request['proveedor'])->first();
                $cuenta_proveedor = $consulta_proovedor->id_cuentas;
                if ($cuenta_proveedor != 0) {
                    $desc_cuenta = Plan_Cuentas::where('id', $cuenta_proveedor)->first();
                    if ($desc_cuenta == '') {
                        return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.falta_cuenta') . ' 2.01.03.01.01 ' . trans('contableM.correspondiente_proveedores')];
                    }
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $cuenta_proveedor,
                        'descripcion'                   => $desc_cuenta->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => $total_final,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $valor_descuento = number_format($request['descuento1'], 2, '.', '');
                if ($valor_descuento > 0) {
                    //Obtenemos el id Plan  de Pago en efectivo de la Tabla Ct_ Configuraciones 
                    // $cuenta_desc = "4.1.08";
                    // if ($id_empresa == "1793135579001") {
                    //     $cuenta_desc = "4.1.07.01.01";
                    // }
                    $cuenta_des_comp = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_DESC_COMP');
                    // $desc_cuenta = Plan_Cuentas::where('id', '4.1.08')->first();
                    if (!is_null($cuenta_des_comp)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $cuenta_des_comp->cuenta_guardar,
                            'descripcion'                   => $cuenta_des_comp->nombre_mostrar,
                            'fecha'                         => $fechahoy,
                            'debe'                          => '0',
                            'haber'                         => $valor_descuento,
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    } else {
                        return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.falta_configuracion') . ' FACTCONTABLE_DESC_COMP ' . trans('contableM.para_empresa') . $id_empresa];
                    }
                    // $desc_cuenta = Plan_Cuentas::where('id', '4.1.08'
                }
                date_default_timezone_set('America/Guayaquil');
                DB::commit();
                return ['status' => 'success', 'id' => $id_compra, 'id_asiento' => $id_asiento_cabecera];
            } else {
                return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.coincidencia_facturas')];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            //echo ['status' => 'error', 'msj' => 'Error: ' . $e->getMessage() . ' ' . trans('contableM.linea') . ' ' . $e->getLine()];
            return ['status' => 'error', 'msj' => 'Error: ' . $e->getMessage() . ' ' . trans('contableM.linea') . ' ' . $e->getLine()];
        }
    }
    public function storeSRI(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fechahoy = $request['f_autorizacion'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $numero_factura = 0;
        $objeto_validar = new Validate_Decimals();
        DB::beginTransaction();
        try {
            $verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
            }
            $total_final = $objeto_validar->set_round($request['total_final1']);
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            if (!is_null($cod_sucurs)) {
                $sucursal = $cod_sucurs->codigo_sucursal;
            } else {
                $sucursal = "0";
            }
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            if (!is_null($cod_caj)) {
                $punto_emision = $cod_caj->codigo_caja;
            } else {
                $punto_emision = "0";
            }
            $total_final = $objeto_validar->set_round($request['total1']);
            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)->get()->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                if ($max_id != '') {
                    $max_id = intval($max_id->secuencia_f);
                    if (strlen($max_id) < 10) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                } else {
                    $nu = 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado = $request['serie'] . '-' . $request['secuencia_factura'];
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('proveedor', $request['proveedor'])
                ->where('tipo_comprobante', $request['tipo_comprobante'])
                ->where('id_empresa', $id_empresa)
                ->where('estado', '!=', '0')
                ->first();
            $subtotalf = $request['base1'];
            if ($comprobacion_compra == '') {
                $input = [
                    'observacion'                   => $request['observacion'],
                    'fecha_asiento'                 => $request['fecha'],
                    'fact_numero'                   => $request['secuencia_factura'],
                    'valor'                         => $total_final,
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'sucursal'                      => $sucursal,
                    'punto_emision'                 => $punto_emision,
                    'aparece_sri'                   => $request['archivosri'],
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                if ($id_empresa != '0992704152001') {
                    $id_proveedor = $request['proveedor'];
                    $proveedor_find = Proveedor::find($id_proveedor);
                    if (!is_null($proveedor_find)) {
                        $cabeceraa = [
                            'observacion'     => $proveedor_find->razonsocial . ' # ' . $numeroconcadenado,
                            'fecha_asiento'   => $request['fecha'],
                            'fact_numero'     => $request['secuencia_factura'],
                            'valor'           => $total_final,
                            'id_empresa'      => $id_empresa,
                            'estado'          => '1',
                            'aparece_sri'     => $request['archivosri'],
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ];
                    }
                }
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input);
                $consulta_fecha = Ct_Termino::where('id', $request['termino'])->first();
                $modfecha = "";
                if ($consulta_fecha != null) {
                    $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($request['fecha']));
                    $modfecha = date("Y-m-d", $nueva_fecha);
                }
                $t = null;
                if ($request['tipo_comprobante'] == '03') {
                    $t = 0;
                }
                $sucursal = Ct_Sucursales::find($request['sucursal']);
                $caja = Ct_caja::where('codigo_caja', $request['p_emision'])->first();
                $input = [
                    'doc_electronico' => $t,
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $request['fecha'],
                    'numero' => $numeroconcadenado,
                    'archivo_sri' => $request['archivosri'],
                    'proveedor' => $request['proveedor'],
                    'termino' => $request['termino'],
                    'secuencia_f' => $numero_factura,
                    'observacion' => $request['observacion'],
                    'tipo' => '2',
                    'sucursal' => $sucursal->codigo_sucursal,
                    'punto_emision' => $caja->codigo_caja,
                    'valor_contable' => $total_final,
                    'fecha_termino' => $modfecha,
                    'orden_compra' => $request['o_compra'],
                    'f_caducidad' => $request['f_caducidad'],
                    'tipo_gasto' => $request['tipo_gasto'],
                    'autorizacion' => $request['autorizacion'],
                    'f_autorizacion' => $request['f_autorizacion'],
                    'id_empresa' => $id_empresa,
                    'serie' => $request['serie'] != '' ? $request['serie'] : $sucursal . '-' . $punto_emision,
                    'secuencia_factura' => $request['secuencia_factura'],
                    'credito_tributario' => $request['credito_tributario'],
                    'tipo_comprobante' => $request['tipo_comprobante'],
                    'subtotal_0' => $request['subtotal_01'],
                    'subtotal_12' => $request['subtotal_121'],
                    'subtotal' => $subtotalf,
                    'descuento' => $request['descuento1'],
                    'iva_total' => $request['tarifa_iva1'],
                    'ice_total' => $request['ice_final1'],
                    'electronica' => $request['tipo_electronica'],
                    'total_final' => $total_final,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'forma_pago' => $request['forma_pago'],
                ];
                $id_compra = Ct_compras::insertGetId($input);
                $arr_total = [];
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $iva_ch = 0;
                        if ($request->input('valoriva')[$i] == 'on')
                            $iva_ch = 1;
                        $arr = [
                            'nombre' => $request->input("nombre")[$i],
                            'cantidad'       => $request->input("cantidad")[$i],
                            'codigo'         => $request->input("codigo")[$i],
                            'precio'         => $request->input("precio")[$i],
                            'descpor'        => $request->input("descpor")[$i],
                            'descuento'      => $request->input("desc")[$i],
                            'precioneto'     => $request->input("precioneto")[$i],
                            'detalle'        => $request->input("descrip_prod")[$i],
                            'iva'            => $iva_ch,
                        ];
                        array_push($arr_total, $arr);
                    }
                }
                $cuentas_iva = [];
                foreach ($arr_total as $valor) {
                    $iva_ch = 0;
                    if ($valor['descpor'] > 0)
                        $iva_ch = 1;
                    $detalle = [
                        'id_ct_compras'        => $id_compra,
                        'codigo'               => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
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
                    $fc = $valor['cantidad'] * $valor['precio'];
                    //$plan_empresa = Plan_Cuentas_Empresa::where('id_plan', "LIKE", "%{$valor['codigo']}%")->orWhere('plan', "LIKE", "%{$valor['codigo']}%")->where('id_empresa', $id_empresa)->first();
                    $plan_empresa = Plan_Cuentas_Empresa::where('nombre', "LIKE", "%{$valor['nombre']}%")->where('id_empresa', $id_empresa)->first();
                    if ($plan_empresa == '') {
                        return ['status' => 'error', 'msj' => '¡Error!, la cuenta principal no esta configurada ' . $id_empresa];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        // 'id_plan_cuenta'                => $valor['codigo'],
                        'id_plan_cuenta'                => $plan_empresa->id_plan,
                        'descripcion'                   => $valor['nombre'],
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => $fc,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $valor_iva = $request['tarifa_iva1'];
                $globales  = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
                if ($globales == '') {
                    return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.registre_cuentas_globales') . ' ' . $id_empresa];
                }
                //1.01.05.01.02
                if ($valor_iva > 0) {
                    // $plan_cuentas = Plan_Cuentas::where('id', '5.2.02.16.15')->first();
                    $plan = Plan_Cuentas::find($globales->debe);
                    if ($plan == '') {
                        return ['status' => 'error', 'msj' => '¡Error! ' . trans('contableM.registre_plan_cuentas') . ' ' . $id_empresa];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        // 'id_plan_cuenta'                => '5.2.02.16.15',
                        'id_plan_cuenta'                => $plan->id,
                        'descripcion'                   => $plan->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => number_format($request['tarifa_iva1'], 2, '.', ''),
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                } else {
                    /*   $plan_cuentas = Plan_Cuentas::where('id', '5.2.02.16.15')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => '5.2.02.16.15',
                        'descripcion'                   => $plan_cuentas->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => number_format($request['tarifa_iva1'], 2, '.', ''),
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]); */
                }
                $consulta_proovedor = Proveedor::where('id', $request['proveedor'])->first();
                // if(Auth::user()->id == "0953905999"){
                //     dd($consulta_proovedor);
                // }
                $cuenta_proveedor = $consulta_proovedor->id_cuentas;
                if ($cuenta_proveedor != 0) {
                    $desc_cuenta = Plan_Cuentas::where('id', $cuenta_proveedor)->first();
                    if ($desc_cuenta == '') {
                        return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.falta_cuenta') . ' 2.01.03.01.01 ' . trans('contableM.correspondiente_proveedores')];
                    }
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $cuenta_proveedor,
                        'descripcion'                   => $desc_cuenta->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => $total_final,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $valor_descuento = number_format($request['descuento1'], 2, '.', '');
                if ($valor_descuento > 0) {
                    //Obtenemos el id Plan  de Pago en efectivo de la Tabla Ct_ Configuraciones 
                    // $cuenta_desc = "4.1.08";
                    // if ($id_empresa == "1793135579001") {
                    //     $cuenta_desc = "4.1.07.01.01";
                    // }
                    $cuenta_des_comp = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_DESC_COMP');
                    // $desc_cuenta = Plan_Cuentas::where('id', '4.1.08')->first();
                    if (!is_null($cuenta_des_comp)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $cuenta_des_comp->cuenta_guardar,
                            'descripcion'                   => $cuenta_des_comp->nombre_mostrar,
                            'fecha'                         => $fechahoy,
                            'debe'                          => '0',
                            'haber'                         => $valor_descuento,
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    } else {
                        return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.falta_configuracion') . ' FACTCONTABLE_DESC_COMP ' . trans('contableM.para_empresa') . $id_empresa];
                    }
                    // $desc_cuenta = Plan_Cuentas::where('id', '4.1.08'
                }
                date_default_timezone_set('America/Guayaquil');
                /*if (Auth::user()->id == "0921605895") {
                    $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id_asiento_cabecera)->get();
                    return ['status' => 'success', 'id' => $id_compra, 'id_asiento' => $id_asiento_cabecera, 'detalle' => $detalle];
                }*/
                $ats = new AtsController();
                $estadoDocSRI = $ats->estadoDocumentoSRI($request['autorizacion']);
                DB::table('log_importacion_sri')->where('clave_acceso', $request['autorizacion'])->update(['estado' => $estadoDocSRI]);
                DB::commit();
                return ['status' => 'success', 'id' => $id_compra, 'id_asiento' => $id_asiento_cabecera];
            } else {
                return ['status' => 'error', 'msj' => '¡Error!, ' . trans('contableM.coincidencia_facturas')];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            //echo ['status' => 'error', 'msj' => 'Error: ' . $e->getMessage() . ' ' . trans('contableM.linea') . ' ' . $e->getLine()];
            return ['status' => 'error', 'msj' => 'Error: ' . $e->getMessage() . ' ' . trans('contableM.linea') . ' ' . $e->getLine()];
        }
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $constraints = [
            'ct_c.id'             => $request['id'],
            'proveedor'        => $request['proveedor'],
            'observacion'         => $request['detalle'],
            'fecha'               => $request['fecha'],
            'id_asiento_cabecera'          => $request['id_asiento_cabecera'],
            'ct_c.tipo_comprobante'   => $request['tipo'],
            'ct_c.numero'              => $request['secuencia_f'],
            'ct_c.id_usuariocrea'              => $request['fac_crea'],
        ];
        //dd($constraints);
        $compras = $this->doSearchingQuery($constraints, $request);
        //dd($compras);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $proveedor = Proveedor::where('estado', '1')->get();
        return view('contable/factura_contable/index', ['compras' => $compras, 'searchingVals' => $constraints, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
    }

    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = DB::table('ct_compras as ct_c')
            ->join('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 2)
            ->select('ct_c.electronica', 'ct_c.nro_autorizacion', 'ct_c.doc_electronico', 'ct_c.id AS id', 'ct_c.rutapdf', 'ct_c.fecha', 'p.razonsocial', 'u.nombre1', 'u.apellido1', 'ct_c.autorizacion', 'ct_c.secuencia_factura', 'ct_c.numero', 'p.id as id_proveedor', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.tipo', 'ct_c.archivo_sri', 'ct_c.proveedor', 'ct_c.orden_compra', 'ct_c.f_caducidad', 'ct_c.tipo_gasto', 'ct_c.f_autorizacion', 'ct_c.serie', 'ct_c.secuencia_factura', 'ct_c.credito_tributario', 'ct_c.observacion', 'ct_c.id_usuariocrea', 'ct_c.id_asiento_cabecera');
        //dd($query->get());

        $fields = array_keys($constraints);
        //dd($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }
        return $query->orderBy('ct_c.fecha', 'desc')->paginate(10);
    }

    public function nombre_proveedor(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',razonsocial) as completo
                  FROM `proveedor`
                  WHERE CONCAT_WS(' ',razonsocial) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function editar($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $compras = Ct_compras::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $detalle_compra = Ct_detalle_compra::where('id_ct_compras', $compras->id)->get();
        $termino = Ct_Termino::where('estado', '1')->get();
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('p.plan as id', 'p.nombre as nombre')->get();
        $empresa = Empresa::find($id_empresa);
        $proveedor = proveedor::where('estado', '1')->get();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        return view('contable/factura_contable/edit', ['porIva' => $this->porIva, 'compras' => $compras, 'c_tributario' => $c_tributario, 'empresa' => $empresa, 'cuentas' => $cuentas, 'termino' => $termino, 't_comprobante' => $t_comprobante, 'proveedor' => $proveedor, 'detalle_compra' => $detalle_compra]);
    }
    public function nombre(Request $request)
    {

        $nombre = $request['term'];

        $data      = array();
        $productos = DB::table('plan_cuentas')->where('nombre', 'like', '%' . $nombre . '%')->where('estado', '2')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function nombre2(Request $request)
    {

        $nombre    = $request['nombre'];

        $data      = null;
        $productos = DB::table('plan_cuentas')->where('nombre', $nombre)->first();
        if (!is_null($productos)) {
            $data = $productos->id;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function codigo(Request $request)
    {

        $codigo = $request['term'];

        $data      = array();
        $productos = DB::table('plan_cuentas')->where('id', 'like', '%' . $codigo . '%')->where('estado', '2')->get();
        //dd($productos);
        foreach ($productos as $product) {
            $data[] = array('value' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function codigo2(Request $request)
    {

        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('plan_cuentas')->where('id', $codigo)->first();
        //dd($productos);
        if (!is_null($productos)) {
            $data = $productos->nombre;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
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
            'estado' => '0',
            'id_usuariocrea'                => $idusuario,
            'id_usuariomod'                 => $idusuario,
            'ip_creacion'                   => $ip_cliente,
            'ip_modificacion'               => $ip_cliente,
        ];
        $fechahoy = Date('Y-m-d H:i:s');
        Ct_compras::where('id', $id)->update($act_estado);
        //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
        $compras = Ct_compras::where('id', $id)->first();
        $contador_ctv = DB::table('ct_compras')->get()->count();
        $id_empresa = $request->session()->get('id_empresa');

        $cabecera = Ct_Asientos_Cabecera::where('id', $compras->id_asiento_cabecera)->first();
        $actualiza = [
            'estado' => '1',
            'id_usuariocrea'                => $idusuario,
            'id_usuariomod'                 => $idusuario,
            'ip_creacion'                   => $ip_cliente,
            'ip_modificacion'               => $ip_cliente,
        ];
        $cabecera->update($actualiza);
        $retencion = $compras->retenciones;
        $detalles = $cabecera->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => $request['observacion'],
            'fecha_asiento'   => $cabecera->fecha_asiento,
            'id_empresa'      => $id_empresa,
            'fact_numero'     => $cabecera->secuencia,
            'valor'           => $cabecera->valor,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'aparece_sri'     => $cabecera->aparece_sri,
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
        LogAsiento::anulacion("COM-FACT", $id_asiento, $cabecera->id);
        /*
        $retencion= Ct_Retenciones::where('id_compra',$compras->id)->first();
        if(!is_null($retencion)){
            $actualiza = [
                'estado' =>'0',    
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ];
            $retencion->update($actualiza);
    
        }
        $detalles = $cabecera->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => 'ANULACIÓN ' . $cabecera->observacion,
            'fecha_asiento'   => date('Y-m-d H:i:s'),
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
                'fecha'               => date('Y-m-d H:i:s'),
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        $retencion= Ct_Retenciones::where('id_compra',$compras->id)->first();
        if(!is_null($retencion) && $retencion!='[]'){
            $actualiza = [
                'estado' =>'0',    
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ];
            $retencion->update($actualiza);
            $asiento_retencion= Ct_Asientos_Cabecera::where('id',$retencion->id_asiento_cabecera)->first();
            $actualiza = [
                'estado' =>'0',    
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ];
            $asiento_retencion->update($actualiza);
            $detalles = $asiento_retencion->detalles;
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
                'fecha_asiento'   => date('Y-m-d H:i:s'),
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $asiento_retencion->secuencia,
                'valor'           => $asiento_retencion->valor,
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
                    'fecha'               => date('Y-m-d H:i:s'),
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }
        }
       

        //anular todo
        $egresos= Ct_Detalle_Comprobante_Egreso::where('id_compra',$compras->id)->get();
        if(!is_null($egresos) && $egresos!='[]'){
            $egresosf= Ct_Comprobante_Egreso::where('id',$egresos[0]->id_comprobante)->first();
            if(!is_null($egresosf)){
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $egresosf->update($actualiza);
                $asiento_retencion= Ct_Asientos_Cabecera::where('id',$egresosf->id_asiento_cabecera)->first();
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $asiento_retencion->update($actualiza);
                $detalles = $asiento_retencion->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
                    'fecha_asiento'   => date('Y-m-d H:i:s'),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $asiento_retencion->secuencia,
                    'valor'           => $asiento_retencion->valor,
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
                        'fecha'               => date('Y-m-d H:i:s'),
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
            }
           
    
        }
        $bancos= Ct_Debito_Bancario_Detalle::where('id_compra',$compras->id)->get();
        if(!is_null($bancos) && $bancos!='[]'){
            $bancosf= Ct_Debito_Bancario::where('id',$bancos[0]->id_debito)->first();
            if(!is_null($bancosf)){
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $bancosf->update($actualiza);
                $asiento_retencion= Ct_Asientos_Cabecera::where('id',$bancosf->id_asiento_cabecera)->first();
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $asiento_retencion->update($actualiza);
                $detalles = $asiento_retencion->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
                    'fecha_asiento'   => date('Y-m-d H:i:s'),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $asiento_retencion->secuencia,
                    'valor'           => $asiento_retencion->valor,
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
                        'fecha'               => date('Y-m-d H:i:s'),
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
            }
        }
        $cruce_valores= Ct_Detalle_Cruce::where('id_factura',$compras->id)->get();
        if(!is_null($cruce_valores) && $cruce_valores!='[]'){
            $cruce_valoresf= Ct_Cruce_Valores::where('id',$cruce_valores[0]->id_comprobante)->first();
            if(!is_null($cruce_valoresf)){
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $cruce_valoresf->update($actualiza);
                $asiento_retencion= Ct_Asientos_Cabecera::where('id',$cruce_valoresf->id_asiento_cabecera)->first();
                $actualiza = [
                    'estado' =>'0',    
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                $asiento_retencion->update($actualiza);
                $detalles = $asiento_retencion->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
                    'fecha_asiento'   => date('Y-m-d H:i:s'),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $asiento_retencion->secuencia,
                    'valor'           => $asiento_retencion->valor,
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
                        'fecha'               => date('Y-m-d H:i:s'),
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
            }
        }*/
        return redirect()->intended('/contable/fact_contable');
    }
}
