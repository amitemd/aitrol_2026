<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogAsiento;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\De_Codigo_Impuestos;
use Sis_medico\De_Empresa;

class RetencionesController extends Controller
{
    private $controlador = 'retenciones';
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
        config(['data' => []]);
        //$data['controlador'] = $this->controlador;
        $data['algo'] = '';
        config(['data' => $data]);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::where('id', $id_empresa)->first();
        $configuracion = Ct_Configuraciones::all();
        $deEmpresa     = De_Empresa::where('id_empresa', $id_empresa)->first();
        if ($deEmpresa == '') {
            $retenciones   = DB::table('ct_retenciones as ct_c')
                ->leftjoin('proveedor as p', 'p.id', 'ct_c.id_proveedor')
                ->join('ct_compras as co', 'co.id', 'ct_c.id_compra')
                ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
                ->select('ct_c.id', 'ct_c.created_at', 'ct_c.descripcion', 'ct_c.punto_emision', 'ct_c.fecha', 'ct_c.electronica', 'ct_c.estado_electronica', 'co.numero', 'ct_c.estado', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia', 'ct_c.valor_fuente', 'p.razonsocial', 'ct_c.id_asiento_cabecera', 'ct_c.valor_iva', 'ct_c.nro_comprobante', 'ct_c.id_compra', 'ct_c.autorizacion')
                ->where('ct_c.id_empresa', $id_empresa)
                ->orderby('ct_c.id', 'desc')
                ->paginate(10);
        } else {
            $retenciones   = DB::table('ct_retenciones as ct_c')
                ->leftjoin('proveedor as p', 'p.id', 'ct_c.id_proveedor')
                ->join('ct_compras as co', 'co.id', 'ct_c.id_compra')
                ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
                ->select('ct_c.id', 'ct_c.created_at', 'ct_c.descripcion', 'ct_c.punto_emision', 'ct_c.fecha', 'ct_c.electronica', 'ct_c.estado_electronica', 'co.numero', 'ct_c.estado', 'u.nombre1', 'u.apellido1', 'ct_c.nro_secuencial', 'ct_c.valor_fuente', 'p.razonsocial', 'ct_c.id_asiento_cabecera', 'ct_c.valor_iva', 'ct_c.nro_comprobante', 'ct_c.id_compra', 'ct_c.nro_comprobante', 'ct_c.doc_electronico', 'ct_c.nro_autorizacion')
                ->where('ct_c.id_empresa', $id_empresa)
                ->orderby('ct_c.id', 'desc')
                ->paginate(10);
        }
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $compras          = Ct_compras::where('estado', '2')->get();
        $proveedores      = Proveedor::all();
        return view('contable/retenciones/index', ['retenciones' => $retenciones, 'proveedores' => $proveedores, 'tipo_comprobante' => $tipo_comprobante, 'compras' => $compras, 'empresa' => $empresa]);
    }
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $constraints      = [
            'ct_c.id'                  => $request['id'],
            'ct_c.id_proveedor'        => $request['id_proveedor'],
            'descripcion'              => $request['detalle'],
            'co.numero'                => $request['secuencia_f'],
            'ct_c.created_at'          => $request['fecha'],
            'ct_c.id_usuariocrea'      => $request['fac_crea'],
            'ct_c.nro_comprobante'     => $request['secuencia'],
            'ct_c.id_asiento_cabecera' => $request['id_asiento'],
            'ct_c.fecha'                => $request['fecha'],
            'ct_c.secuencia'           => $request["secuencia_f"],
        ];
        $proveedores = Proveedor::all();
        $retenciones = $this->doSearchingQuery($constraints, $request);
        $compras     = Ct_compras::where('estado', '2')->get();
        return view('contable.retenciones.index', ['request' => $request, 'proveedores' => $proveedores, 'retenciones' => $retenciones, 'searchingVals' => $constraints, 'tipo_comprobante' => $tipo_comprobante, 'compras' => $compras, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
        if ($deEmpresa == '') {
            $query = DB::table('ct_retenciones as ct_c')
                ->join('proveedor as p', 'p.id', 'ct_c.id_proveedor')
                ->join('ct_compras as co', 'ct_c.id_compra', 'co.id')
                ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
                ->where('ct_c.id_empresa', $id_empresa)
                ->select('ct_c.id', 'p.razonsocial', 'ct_c.fecha', 'ct_c.punto_emision', 'u.nombre1', 'u.apellido1', 'ct_c.descripcion', 'ct_c.estado', 'ct_c.created_at', 'co.numero', 'ct_c.secuencia', 'ct_c.id_asiento_cabecera', 'ct_c.id_usuariocrea', 'ct_c.valor_fuente', 'ct_c.valor_iva', 'ct_c.electronica', 'ct_c.estado_electronica', 'ct_c.nro_comprobante', 'p.id as id_proveedor');
        } else {
            $query = DB::table('ct_retenciones as ct_c')
                ->join('proveedor as p', 'p.id', 'ct_c.id_proveedor')
                ->join('ct_compras as co', 'ct_c.id_compra', 'co.id')
                ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
                ->where('ct_c.id_empresa', $id_empresa)
                ->select('ct_c.id', 'p.razonsocial', 'ct_c.fecha', 'ct_c.punto_emision', 'u.nombre1', 'u.apellido1', 'ct_c.descripcion', 'ct_c.estado', 'ct_c.created_at', 'co.numero', 'ct_c.nro_secuencial', 'ct_c.id_asiento_cabecera', 'ct_c.id_usuariocrea', 'ct_c.valor_fuente', 'ct_c.valor_iva', 'ct_c.electronica', 'ct_c.estado_electronica', 'ct_c.doc_electronico', 'ct_c.nro_comprobante', 'p.id as id_proveedor', 'ct_c.nro_autorizacion');
        }
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }
        return $query->orderby('ct_c.id', 'desc')->paginate(10);
    }
    public function nombre_proveedor(Request $request)
    {

        $codigo       = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombrecomercial) as completo
                  FROM `proveedor`
                  WHERE CONCAT_WS(' ',nombrecomercial) like '" . $seteo . "'
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
    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::all();
        $rfir    = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $acreedores = Proveedor::where('estado', '1')->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        return view('contable/retenciones/create', ['divisas' => $divisas, 'acreedores' => $acreedores, 'rfir' => $rfir, 'rfiva' => $rfiva, 'sucursales' => $sucursales, 'empresa' => $empresa]);
    }
    public function modal_retenciones(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_proveedor = $request['id_proveedor'];

        $id_compra = 0;
        if (isset($request['id_compra'])) {
            $id_compra = $request['id_compra'];
        }
        $id_fact_contable = 0;
        if (isset($request['id_fact_contable'])) {
            $id_fact_contable = $request['id_fact_contable'];
        }
        $secuencia_factura = $request['secuencia'];
        $ivatotal          = $request['total_iva'];
        $proveedor         = Proveedor::where('id', $id_proveedor)->first();
        $consulta_cabecera = Ct_Asientos_Cabecera::where('fact_numero', $secuencia_factura)->first();
        $divisas           = Ct_Divisas::all();
        $id_empresa        = $request->session()->get('id_empresa');
        $empresa_sucurs    = Empresa::findorfail($id_empresa);
        $empresa_general   = Empresa::all();
        $rfir              = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        $rfiva             = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $sucursales        = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/retenciones/modal_retenciones', ['ivatotal' => $ivatotal, 'id_compra' => $id_compra, 'proveedor' => $proveedor, 'id_fact_contable' => $id_fact_contable, 'secuencia_factura' => $secuencia_factura, 'consulta_cabecera' => $consulta_cabecera, 'rfir' => $rfir, 'rfiva' => $rfiva, 'divisas' => $divisas, 'id_empresa' => $id_empresa, 'empresa_sucurs' => $empresa_sucurs, 'sucursales' => $sucursales, 'min_date' => $min_date]);
    }
    public function buscar_codigo(Request $request)
    {

        $id_factura = intval($request['id_factura']);
        $tipo       = 1;
        $id_empresa = $request->session()->get('id_empresa');
        $data       = null;
        $productos  = '[]';
        $deudas     = '[]';
        $idusuario  = Auth::user()->id;
        if ($tipo == 1) {

            $productos = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.id', $id_factura)
                ->where('c.id_empresa', $id_empresa)
                ->select(
                    'co.proveedor',
                    'p.nombrecomercial',
                    'p.direccion',
                    'a.id',
                    'a.descripcion',
                    'p.razonsocial',
                    'co.fecha',
                    'p.id_tipoproveedor',
                    'c.observacion',
                    'c.fecha_asiento',
                    '.c.valor',
                    'co.numero',
                    'p.id_porcentaje_iva',
                    'p.id_porcentaje_ft',
                    'co.id',
                    'co.tipo',
                    'c.fact_numero',
                    'co.autorizacion',
                    'co.subtotal',
                    'co.iva_total',
                    'co.descuento'
                )->get();

            $deudas = [];
            if (count($productos) > 0) {
                $deudas = DB::table('ct_asientos_cabecera as c')
                    ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                    ->join('proveedor as p', 'p.id', 'co.proveedor')
                    ->where('co.proveedor', $productos[0]->proveedor)
                    ->where('co.estado', '1')
                    ->where('co.id', $id_factura)
                    ->where('c.id_empresa', $id_empresa)
                    ->select('c.valor', 'p.id_tipoproveedor', 'p.id_porcentaje_iva', 'p.id_porcentaje_ft', 'c.fact_numero', 'co.numero', 'c.observacion', 'c.fecha_asiento', 'co.proveedor', 'co.secuencia_f', 'co.descuento')
                    ->get();
            }
        }

        if ($productos != '[]') {
            $valor = $productos[0]->valor;
            if ($productos[0]->descuento > 0) {
                $valor = $productos[0]->valor - $productos[0]->descuento;
            }
            $data = [
                $productos[0]->proveedor,
                $productos[0]->id,
                $productos[0]->nombrecomercial,
                $productos[0]->direccion,
                $productos[0]->descripcion,
                $productos[0]->razonsocial,
                $productos,
                $productos[0]->id_tipoproveedor,
                $productos[0]->observacion,
                $productos[0]->fecha_asiento,
                $valor,
                $productos[0]->numero,
                $productos[0]->id_porcentaje_iva,
                $productos[0]->id_porcentaje_ft,
                $productos[0]->id,
                $productos[0]->fact_numero,
                $deudas,
                $productos[0]->autorizacion,
                $productos[0]->subtotal,
                $productos[0]->iva_total,
                $productos[0]->tipo,
            ];
            return response()->json($data);
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function codigo(Request $request)
    {
        $codigo     = $request['term'];
        $validacion = 1;
        if ($validacion == 1) {
            $data       = array();
            $id_empresa = $request->session()->get('id_empresa');
            $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $productos  = DB::table('ct_compras')->where('numero', 'like', '%' . $codigo . '%')->where('id_empresa', $id_empresa)->where('estado', '1')->get();
            foreach ($productos as $product) {

                $data[] = array('value' => $product->numero);
            }
            if (count($data)) {
                return $data;
            } else {
                return ['value' => 'No se encontraron resultados'];
            }
        } else {
        }
        return 'no';
    }
    public function query_cuentas(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];
        $tipo   = $request['tipo'];

        if (!is_null($codigo)) {
            $data = DB::table('ct_porcentaje_retenciones')->where('id', $codigo)->where('tipo', $tipo)->get();
            return $data;
        }
        return 'no';
    }
    public function query_cuentas2(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];

        if (!is_null($codigo)) {
            $data = DB::table('ct_porcentaje_retenciones')->where('id', $codigo)->get();
            return $data;
        }
        return 'no';
    }
    public function secuencia(Request $request)
    {
        $id_empresa     = $request['id_empresa'];
        $punto_emision  = $request['punto_emision'];
        $sucursal       = substr($punto_emision, 0, -4);
        $numero_factura = 0;
        $punto_emision  = substr($punto_emision, 4);
        $max_id         = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->where('secuencia', $request['secuencia'])->first();
        if (is_null($max_id)) {
            return 'ok';
        } else {
            return 'no';
        }
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //consulta para traer la cuenta del proveedor
        $consula_plan_acreedor = Proveedor::where('id', $request['id_proveedor'])->first();
        $objeto_validar        = new Validate_Decimals();
        $total_final           = $request['retencion_total'];

        $consulta_plan_debe      = 0;
        $consulta_acreedor_plan  = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->where('id', $consula_plan_acreedor->id_porcentaje_iva)->first();
        $consulta_acreedor_plan2 = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->where('id', $consula_plan_acreedor->id_porcentaje_ft)->first();
        $nombre_plan             = Plan_Cuentas::where('id', $consula_plan_acreedor->id_cuentas)->first();
        $sucursal                = $request['sucursal'];
        $punto_emision           = $request['punto_emision'];
        $serie = explode('-', $punto_emision);
        $sucursal                = substr($punto_emision, 0, -4);
        $numero_factura          = 0;
        $punto_emision           = substr($punto_emision, 4);
        $contador_ctv            = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $comprobacion = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->where('estado', '!=', '0')->where('id_compra', $request['id_compra'])->first();
        $propiedad = $request['punto_final'];
        if ($idusuario == '1316262193') {
            /*             $data                       = array();
$nombre = $consula_plan_acreedor->razonsocial;
$getNombre = $this->getNombres($nombre);
$nombreCompleto = $getNombre['nombres'];
$apellidoCompleto = $getNombre['apellidos'];
if (stripos($nombre, 'Del')) {
$nombreCompleto = $getNombre['apellidos'];
$apellidoCompleto = $getNombre['nombres'];
}
$data['empresa']            = "13131231321312";
$proveedor['cedula']        = "131313131";
$proveedor['tipo']          = '04'; //04 ruc, 05 cedula /06 pasaporte, 08 identificacion extranjera
$proveedor['nombre']        = $nombreCompleto;
$proveedor['apellido']      = $apellidoCompleto;
$proveedor['email']         = $nombre;
$data['proveedor']          = $proveedor;
dd($data);  */
        }
        if ($comprobacion == '[]' || is_null($comprobacion)) {
            DB::beginTransaction();
            try {
                if (!is_null($propiedad)) {
                    $numero_factura = str_pad($propiedad, 9, "0", STR_PAD_LEFT);
                } else {
                    if ($contador_ctv == 0) {
                        //return 'No Retorno nada';
                        $num            = '1';
                        $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
                    } else {

                        //Obtener Ultimo Registro de la Tabla ct_ventas
                        $max_id = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                        $max_id = intval($max_id->secuencia);

                        if (strlen($max_id) < 10) {
                            $nu             = $max_id + 1;
                            $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                        }
                    }
                }
                $idusuario = Auth::user()->id;
                $id_empresa         = $request->session()->get('id_empresa');
                $total_fuente       = $request['valor_fuente'];
                $total_iva          = $request['valor_iva'];
                $total_t            = $request['valor_retenido'];
                $consulta_plan_debe = DB::table('ct_porcentaje_retenciones')->where('id', $request['porcentaje_retencionf'])->where('tipo', '=', '1')->first();
                $id_asiento_cabecera = null;
                if (($request['id_compra']) != 0) {
                    //comentado x Jorge Zarama
                    $cabeceraa = [
                        'observacion'     => $numero_factura . ' ' . $request['concepto'],
                        'fecha_asiento'   => $request['fecha_retencion'],
                        'fact_numero'     => $numero_factura,
                        'valor'           => $total_final,
                        'estado'          => '1',
                        'id_empresa'      => $id_empresa,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal' => $sucursal,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                    $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
                    if ($deEmpresa == '') { //pyf
                        $input               = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_proveedor'        => $request['id_proveedor'],
                            'id_compra'           => $request['id_compra'],
                            'autorizacion'        => $request['autorizacion'],
                            'nro_comprobante'     => $request['pemision'] . '-' . $numero_factura,
                            'valor_fuente'        => $request['valor_renta'],
                            'fecha'               => $request['fecha'],
                            'valor_iva'           => $request['valor_iva'],
                            'id_empresa'          => $id_empresa,
                            'sucursal'            => $sucursal,
                            'electronica'         => $request['electronica'],
                            'anulado'             => $request['anulado'],
                            'punto_emision'       => $punto_emision,
                            'tipo'                => '1',
                            'id_tipo'             => $request['porcentaje_retencionf'],
                            'descripcion'         => $request['concepto'],
                            'estado'              => '1',
                            'total'               => $request['total'],
                            'secuencia'           => $numero_factura,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ];
                    } else { //docelectronico
                        $input = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_proveedor'        => $request['id_proveedor'],
                            'id_compra'           => $request['id_compra'],
                            'valor_fuente'        => $request['valor_renta'],
                            'fecha'               => $request['fecha'],
                            'valor_iva'           => $request['valor_iva'],
                            'id_empresa'          => $id_empresa,
                            'sucursal'            => $sucursal,
                            'electronica'         => $request['electronica'],
                            'anulado'             => $request['anulado'],
                            'punto_emision'       => $punto_emision,
                            'doc_electronico' => $serie[1] == 100 ? 0 : null,
                            'tipo'                => '1',
                            'id_tipo'             => '1',
                            'descripcion'         => $request['concepto'],
                            'estado'              => '1',
                            'total'               => $request['total'],
                            'nro_secuencial'      => $numero_factura,
                            'nro_comprobante'     => $request['pemision'] . '-' . $numero_factura,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ];
                    }
                    $id_retenciones = Ct_Retenciones::insertGetId($input);

                    for ($i = 1; $i < $request['cont']; $i++) {
                        $porcentaje = Ct_Porcentaje_Retenciones::find($request['id_porcentaje' . $i]);
                        Ct_detalle_retenciones::create([
                            'id_retenciones'  => $id_retenciones,
                            'observacion'     => $request['concepto'],
                            'id_tipo'         => $request['porcentaje_retencion' . $i],
                            'tipo'            => $request['tipor' . $i],
                            'id_porcentaje'   => $request['id_porcentaje' . $i],
                            'codigo'          => $request['codigor' . $i],
                            'base_imponible'  => $request['base_imp' . $i],
                            'porcentaje'      => $porcentaje->valor,
                            'estado'          => '1',
                            'totales'         => $request['valor_retenido' . $i],
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                    $actualizar_fac      = Ct_Compras::where('id', $request['id_compra'])->first();
                    $valord              = (float) $request['retencion_total'];
                    $valor_total_compra  = $actualizar_fac->total_final - $valord;
                    $valor_total_compras = $valor_total_compra;
                    $actualizar          = [
                        'valor_contable'  => $valor_total_compras,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'estado'          => '2',
                    ];
                    $actualizar_fac->update($actualizar);
                    //comentado x Jorge Zarama
                    if (!is_null($request['id_proveedor_modal'])) {
                        $consulta_nombre      = DB::table('proveedor')->where('id', $request['id_proveedor_modal'])->first();
                        $consulta_retenciones = Ct_detalle_retenciones::where('id_retenciones', $id_retenciones)->where('estado', '1')->get();

                        if (!is_null($consulta_nombre)) {
                            $nombre_cuenta = $consulta_nombre->id_cuentas;
                            $valors        = $request['retencion_total'];
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $id_asiento_cabecera,
                                'id_plan_cuenta'      => $consula_plan_acreedor->id_cuentas,
                                'descripcion'         => $nombre_plan->nombre,
                                'fecha'               => $request['fecha_retencion'],
                                'debe'                => $valors,
                                'haber'               => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $idusuario,
                                'ip_modificacion'     => $idusuario,
                            ]);

                            foreach ($consulta_retenciones as $value) {
                                if ($value->codigo != null && $value->id_porcentaje != null) {
                                    if ($value->tipo == 'RENTA') {
                                        $consulta_plan_debe = DB::table('ct_porcentaje_retenciones')->where('codigo', $value->codigo)->where('tipo', '2')->first();
                                        //dd($consulta_plan_debe);
                                        $valors = $value->totales;
                                        Ct_Asientos_Detalle::create([
                                            'id_asiento_cabecera' => $id_asiento_cabecera,
                                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                            'descripcion'         => $consulta_plan_debe->nombre,
                                            'fecha'               => $request['fecha_retencion'],
                                            'haber'               => $valors,
                                            'debe'                => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => $idusuario,
                                            'id_usuariomod'       => $idusuario,
                                            'ip_creacion'         => $idusuario,
                                            'ip_modificacion'     => $idusuario,
                                        ]);
                                    }
                                    if ($value->tipo == 'IVA') {
                                        $consulta_plan_debe = DB::table('ct_porcentaje_retenciones')->where('codigo', $value->codigo)->where('tipo', '1')->first();
                                        $valors             = $value->totales;
                                        Ct_Asientos_Detalle::create([
                                            'id_asiento_cabecera' => $id_asiento_cabecera,
                                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                            'descripcion'         => $consulta_plan_debe->nombre,
                                            'fecha'               => $request['fecha_retencion'],
                                            'haber'               => $valors,
                                            'debe'                => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => $idusuario,
                                            'id_usuariomod'       => $idusuario,
                                            'ip_creacion'         => $idusuario,
                                            'ip_modificacion'     => $idusuario,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                $getSri = "no";
                $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
                if ($deEmpresa == '') { //pyf
                    if ($request['electronica'] == 1) {
                        if ($empresa->electronica == 1) {
                            $getSri = $this->getSri($id_retenciones);
                        }
                    }
                } else {
                    if ($request['electronica'] == 1 && $serie[1] != 100) {
                        if ($empresa->electronica == 1) {
                            $getSri = $this->getSri($id_retenciones);
                        }
                    }
                }
                DB::commit();
                return ['id' => $id_retenciones, 'sri' => $getSri, 'error' => 'no'];
            } catch (\Exception $e) {
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return ['error' => $e->getMessage()];
            }
        } else {
            return 'error';
        }
    }

    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::find($id_empresa);
        $retenciones = Ct_Retenciones::where('id', $id)->first();
        //dd($retenciones);
        $compras  = Ct_compras::where('id', $retenciones->id_compra)->first();
        $detalles = Ct_detalle_retenciones::where('id_retenciones', $id)->get();
        $divisas  = Ct_Divisas::all();
        $rfir     = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();

        $rfiva = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();

        return view('contable/retenciones/edit', ['retenciones' => $retenciones, 'empresa' => $empresa, 'compras' => $compras, 'detalle' => $detalles, 'rfir' => $rfir, 'rfiva' => $rfiva, 'divisas' => $divisas, 'id' => $id]);
    }
    public function imprimir_comprobante_retenciones($id, Request $request)
    {
        $retenciones_all = Ct_Retenciones::find($id);
        $id_empresa      = $request->session()->get('id_empresa');
        //dd($retenciones_all);
        $sucursal = DB::table('ct_caja as c')
            ->join('ct_sucursales as co', 'c.id_sucursal', 'co.id')
            ->where('co.id_empresa', $id_empresa)
            ->select('co.codigo_sucursal', 'c.codigo_caja')
            ->orderby('co.id', 'desc')
            ->get();
        $empresa1            = null;
        $empresa2            = null;
        $proveedor1          = 0;
        $proveedor2          = 0;
        $compras             = DB::table('ct_compras')->where('id', $retenciones_all->id_compra)->first();
        $asiento_cabecera    = DB::table('ct_asientos_cabecera')->where('id', $retenciones_all->id_asiento_cabecera)->first();
        $retenciones_detalle = DB::table('ct_detalle_retenciones')->where('id_retenciones', $retenciones_all->id)->get();
        if ($compras != null) {
            $empresa1   = DB::table('empresa')->where('id', $compras->id_empresa)->first();
            $proveedor1 = DB::table('proveedor')->where('id', $compras->proveedor)->first();
        }
        $iva = 0;
        //dd($retenciones_detalle);
        $consulta_iva   = 0;
        $consulta_renta = 0;
        $renta1         = 0;
        $renta2         = 0;
        /*
        $consulta_renta= DB::table('ct_detalle_retenciones')->where('tipo','RENTA')->where('id_retenciones',$retenciones_all->id)->get();
        //dd($consulta_renta);
        $consulta_iva= DB::table('ct_detalle_retenciones')->where('tipo','IVA')->where('id_retenciones',$retenciones_all->id)->get();
        if(sizeof($consulta_renta)==2){
        $renta1= DB::table('ct_porcentaje_retenciones')->where('id',$consulta_renta[0]->id_tipo)->first();
        $renta2= DB::table('ct_porcentaje_retenciones')->where('id',$consulta_renta[1]->id_tipo)->first();

        }else{
        $renta1=DB::table('ct_porcentaje_retenciones')->where('id',$consulta_renta[0]->id_tipo)->first();
        }*/
        $detalle_retenciones     = Ct_detalle_retenciones::where('tipo', 'RENTA')->where('id_retenciones', $retenciones_all->id)->get();
        $detalle_retenciones_iva = Ct_detalle_retenciones::where('tipo', 'IVA')->where('id_retenciones', $retenciones_all->id)->get();
        $iva = 0;
        $vistaurl = "contable.retenciones.pdf_comprobante_retenciones";
        $view     = \View::make($vistaurl, compact('retenciones_all', 'sucursal', 'consulta_renta', 'consulta_iva', 'compras', 'detalle_retenciones', 'detalle_retenciones_iva', 'iva', 'empresa1', 'empresa2', 'proveedor1', 'proveedor2'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function buscar_proveedor(Request $request)
    {
        if (!is_null($request['nombre_proveedor'])) {
            $proveedor  = $request['nombre_proveedor'];
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = DB::table('ct_retenciones as cr')
                ->join('proveedor as p', 'p.id', 'cr.id_proveedor')
                ->where('p.nombrecomercial', $proveedor)
                ->select('cr.*')->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        } elseif (isset($request['buscar_secuencia'])) {
            $concepto   = '%' . $request['buscar_secuencia'] . '%';
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Retenciones::where('descripcion', 'like', $concepto)->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        }
        return ['value' => 'no resultados'];
    }
    public function buscar_tipo(Request $request)
    {
        $id       = $request['id'];
        $consulta = array();
        if (!is_null($id)) {
            if ($id == '0') {

                $consulta = DB::table('ct_porcentaje_retenciones')->where('tipo', $id)->where('estado', 1)->get();
            } else {

                $consulta = DB::table('ct_porcentaje_retenciones')->where('tipo', $id)->where('estado', 1)->get();
            }
            return $consulta;
        }
        return 'no data';
    }
    public function anular($id, Request $request)
    {
        try {
            $ip_cliente      = $_SERVER["REMOTE_ADDR"];
            $idusuario       = Auth::user()->id;
            $retenciones     = Ct_Retenciones::where('id', $id)->first();
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($retenciones->fecha));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $asiento         = Ct_Asientos_Cabecera::findorfail($retenciones->id_asiento_cabecera);
            $asiento->estado = 1;
            $asiento->save();
            $detalles       = $asiento->detalles;
            $valorestotales = $retenciones->valor_fuente + $retenciones->valor_iva;
            $concepto       = $request['concepto'];
            $id_empresa     = $request->session()->get('id_empresa');
            if ($retenciones != null || $retenciones != '[]') {
                $input_cab = [
                    'estado'          => '0',
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $retenciones->update($input_cab);

                $compra         = Ct_Compras::where('id', $retenciones->id_compra)->first();
                $valor_contable = $compra->valor_contable + $valorestotales;
                if ($compra != null || $compra != '[]') {
                    $input_da = [
                        'estado'          => '1',
                        'valor_contable'  => $valor_contable,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $compra->update($input_da);
                }
                Contable::recovery_price($retenciones->id_compra, 'C');
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => $concepto,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $retenciones->secuencia,
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
            }
            LogAsiento::anulacion("AC-RT", $id_asiento, $asiento->id);
            // Log_Contable::create([
            //     'tipo'           => 'R',
            //     'valor_ant'      => $asiento->valor,
            //     'valor'          => $asiento->valor,
            //     'id_usuariocrea' => $idusuario,
            //     'id_usuariomod'  => $idusuario,
            //     'observacion'    => $asiento->concepto,
            //     'id_ant'         => $asiento->id,
            //     'id_referencia'  => $id_asiento,
            // ]);
            return ['status' => 'success', 'msj' => 'Correcto'];
            //return redirect()->route('retenciones_index');
        } catch (Exception $ex) {
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'line' => $ex->getLine()];
        }
    }
    public function buscarpro(Request $request)
    {
        if (!is_null($request['opcion'])) {
            $id_proveedor = $request['opcion'];
            $tipo         = 1;
            $id_empresa   = $request->session()->get('id_empresa');
            $data         = null;
            $productos    = '[]';
            $deudas       = '[]';
            /*
            if($tipo==1){
            $productos = DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->join('proveedor as p', 'p.id', 'co.proveedor')
            ->where('co.proveedor',$id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->where('co.estado', '1')
            ->select('co.proveedor', 'p.nombrecomercial', 'p.direccion', 'a.id', 'a.descripcion', 'p.razonsocial', 'co.fecha', 'p.id_tipoproveedor',
            'c.observacion', 'c.fecha_asiento', '.c.valor','co.numero', 'p.id_porcentaje_iva', 'p.id_porcentaje_ft', 'co.id','co.tipo', 'c.fact_numero','co.autorizacion','co.subtotal','co.iva_total')->get();

            $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->join('proveedor as p', 'p.id', 'co.proveedor')
            ->where('co.estado', '1')
            ->where('co.proveedor',$id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->select('c.valor','p.id_tipoproveedor', 'p.id_porcentaje_iva', 'p.id_porcentaje_ft', 'c.fact_numero','co.numero', 'c.observacion', 'c.fecha_asiento', 'co.proveedor','co.secuencia_f')
            ->orderby('co.secuencia_f', $id_proveedor)
            ->get();
            }

            if ($productos != '[]') {

            $data = [$productos[0]->proveedor, $productos[0]->id, $productos[0]->nombrecomercial, $productos[0]->direccion,
            $productos[0]->descripcion, $productos[0]->razonsocial, $productos, $productos[0]->id_tipoproveedor, $productos[0]->observacion,
            $productos[0]->fecha_asiento, $productos[0]->valor, $productos[0]->numero, $productos[0]->id_porcentaje_iva, $productos[0]->id_porcentaje_ft,
            $productos[0]->id, $productos[0]->fact_numero,$deudas,$productos[0]->autorizacion,$productos[0]->subtotal,$productos[0]->iva_total,$productos[0]->tipo];
            return response()->json($data);
            } else {
            return ['value' => 'no resultados'];
            }*/
            $compras = Ct_Compras::where('proveedor', $id_proveedor)->where('estado', '1')->where('tipo', '!=', '3')->where('id_empresa', $id_empresa)->get();
            foreach ($compras as $compras) {

                $data[] = array('value' => $compras->numero, 'id' => $compras->id);
            }
            if (count($data)) {
                return $data;
            } else {
                return ['value' => 'No se encontraron resultados'];
            }
        }
        return response()->json("error vacio");
    }
    public function getSri($id)
    {
        //crear retencion
        $impuestosPer = array();
        $idusuario    = Auth::user()->id;
        $retenciones  = Ct_Retenciones::find($id);
        if (!is_null($retenciones)) {
            $detalle_retenciones = Ct_detalle_retenciones::where('id_retenciones', $id)->get();
            if (count($detalle_retenciones) > 0) {
                $tipor = $retenciones->proveedor->tipo;
                if (is_null($tipor)) {
                    $tipor = "4";
                }
                $nombre    = $retenciones->proveedor->razonsocial;
                $correoP   = $retenciones->proveedor->email;
                $getNombre = $this->getNombres($nombre);
                $fecha     = date('d/m/Y', strtotime($retenciones->fecha));
                $periodo   = date('m/Y', strtotime($retenciones->fecha));
                //$tipor = str_pad($tipor, 1, "0", STR_PAD_LEFT);
                $data                = array();
                $data['empresa']     = $retenciones->id_empresa;
                $proveedor['cedula'] = $retenciones->proveedor->id;
                $proveedor['tipo']   = '0' . $tipor; //04 ruc, 05 cedula /06 pasaporte, 08 identificacion extranjera
                $nombreCompleto      = $getNombre['nombres'];
                $apellidoCompleto    = $getNombre['apellidos'];
                if (stripos($nombre, 'Del')) {
                    /*$nombreCompleto   = $getNombre['apellidos'];
                    $apellidoCompleto = $getNombre['nombres'];*/
                }
                $proveedor['nombre']    = $nombreCompleto;
                $proveedor['apellido']  = $apellidoCompleto;
                $proveedor['email']     = $retenciones->proveedor->email;
                $data['proveedor']      = $proveedor;
                $comprobante['fecha']   = $fecha;
                $comprobante['tipo']    = "01"; //01 factura
                $comprobante['periodo'] = $periodo;
                $max_id                 = intval($retenciones->compras->secuencia_factura);
                $numero_factura         = $retenciones->compras->secuencia_factura;
                if (strlen($max_id) < 10) {
                    $nu             = $max_id;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
                $final                      = $retenciones->compras->serie . '-' . $numero_factura;
                $comprobante['comprobante'] = $final;
                $data['comprobante']        = $comprobante;
                //se envian los detalle de la retencion
                foreach ($detalle_retenciones as $value) {
                    $tipo = "";
                    if ($value->tipo == 'RENTA') {
                        $tipo                      = "1";
                        $impuesto['tipo']          = $tipo;
                        $impuesto['impuesto']      = $value->codigo;
                        $impuesto['baseimponible'] = $value->base_imponible;
                        $impuesto['porcentaje']    = $value->porcentajer->valor;
                        $impuesto['valorretenido'] = $value->totales;
                    } elseif ($value->tipo == 'IVA') {
                        $tipo                      = "2";
                        $impuesto['tipo']          = $tipo;
                        $impuesto['impuesto']      = $value->codigo;
                        $impuesto['baseimponible'] = $value->base_imponible;
                        $impuesto['porcentaje']    = $value->porcentajer->valor;
                        $impuesto['valorretenido'] = $value->totales;
                    }
                    array_push($impuestosPer, $impuesto);
                }

                $data['impuesto'] = $impuestosPer;

                $info_adicional['nombre'] = "DIRECCION";
                $info_adicional['valor']  = $retenciones->proveedor->direccion;
                $info[0]                  = $info_adicional;
                $info_adicional['nombre'] = "TELEFONO";
                $info_adicional['valor']  = strval($retenciones->proveedor->telefono1);
                $info[1]                  = $info_adicional;
                $info_adicional['nombre'] = "CORREO";
                $info_adicional['valor']  = $correoP;
                $info[2]                  = $info_adicional;

                $id_empresa_retencion = Session::get('id_empresa');

                if ($id_empresa_retencion == '0993069299001') {
                    $info_adicional['nombre'] = "Agente_de_Retencion";
                    $info_adicional['valor']  = "AGENTE DE RETENCION CONTRIBUYENTE";
                    $info[3]                  = $info_adicional;
                }

                $pago['informacion_adicional'] = $info;
                $data['pago']                  = $pago;

                $envio = ApiFacturacionController::crearRetencion($data);
                if ($idusuario == '1316262193') {
                    //dd($envio);
                }

                $partes                       = explode("-", $envio->comprobante);
                $c_sucursal                   = $partes['0'];
                $c_caja                       = $partes['1'];
                $nfactura                     = $partes['2'];
                $retenciones->punto_emision   = $c_caja;
                $retenciones->sucursal        = $c_sucursal;
                $retenciones->secuencia       = $nfactura;
                $retenciones->nro_comprobante = $envio->comprobante;
                $retenciones->save();
                return response()->json(['result' => $envio]);
            } else {
                return response()->json(['error' => 'vacio detalle']);
            }
        } else {
            return response()->json(['error' => 'vacio']);
        }
    }
    public function reenviar($id)
    {
        //crear retencion
        $impuestosPer = array();
        $idusuario    = Auth::user()->id;
        $retenciones  = Ct_Retenciones::find($id);
        if (!is_null($retenciones)) {
            $detalle_retenciones = Ct_detalle_retenciones::where('id_retenciones', $id)->get();
            if (count($detalle_retenciones) > 0) {
                $tipor = $retenciones->proveedor->tipo;
                if (is_null($tipor)) {
                    $tipor = "4";
                }
                $nombre    = $retenciones->proveedor->razonsocial;
                $correoP   = $retenciones->proveedor->email;
                $getNombre = $this->getNombres($nombre);
                $fecha     = date('d/m/Y', strtotime($retenciones->fecha));
                $periodo   = date('m/Y', strtotime($retenciones->fecha));
                //$tipor = str_pad($tipor, 1, "0", STR_PAD_LEFT);
                $data                   = array();
                $data['empresa']        = $retenciones->id_empresa;
                $proveedor['cedula']    = $retenciones->proveedor->id;
                $proveedor['tipo']      = '0' . $tipor; //04 ruc, 05 cedula /06 pasaporte, 08 identificacion extranjera
                $proveedor['nombre']    = $getNombre['nombres'];
                $proveedor['apellido']  = $getNombre['apellidos'];
                $proveedor['email']     = $retenciones->proveedor->email;
                $data['proveedor']      = $proveedor;
                $comprobante['fecha']   = $fecha;
                $comprobante['tipo']    = "01"; //01 factura
                $comprobante['periodo'] = $periodo;
                $max_id                 = intval($retenciones->compras->secuencia_factura);
                $numero_factura         = $retenciones->compras->secuencia_factura;
                if (strlen($max_id) < 10) {
                    $nu             = $max_id;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
                $final                      = $retenciones->compras->serie . '-' . $numero_factura;
                $comprobante['comprobante'] = $final;
                $data['comprobante']        = $comprobante;
                //se envian los detalle de la retencion
                foreach ($detalle_retenciones as $value) {
                    $tipo = "";
                    if ($value->tipo == 'RENTA') {
                        $tipo                      = "1";
                        $impuesto['tipo']          = $tipo;
                        $impuesto['impuesto']      = $value->codigo;
                        $impuesto['baseimponible'] = $value->base_imponible;
                        $impuesto['porcentaje']    = $value->porcentajer->valor;
                        $impuesto['valorretenido'] = $value->totales;
                    } elseif ($value->tipo == 'IVA') {
                        $tipo                      = "2";
                        $impuesto['tipo']          = $tipo;
                        $impuesto['impuesto']      = $value->codigo;
                        $impuesto['baseimponible'] = $value->base_imponible;
                        $impuesto['porcentaje']    = $value->porcentajer->valor;
                        $impuesto['valorretenido'] = $value->totales;
                    }

                    //$impuestos[0]              = $impuesto;
                    array_push($impuestosPer, $impuesto);
                }

                $data['impuesto'] = $impuestosPer;

                $info_adicional['nombre']      = "DIRECCION";
                $info_adicional['valor']       = $retenciones->proveedor->direccion;
                $info[0]                       = $info_adicional;
                $info_adicional['nombre']      = "TELEFONO";
                $info_adicional['valor']       = strval($retenciones->proveedor->telefono1);
                $info[1]                       = $info_adicional;
                $info_adicional['nombre']      = "CORREO";
                $info_adicional['valor']       = $correoP;
                $info[2]                       = $info_adicional;
                $pago['informacion_adicional'] = $info;
                $data['pago']                  = $pago;
                if ($idusuario == '1316262193') {
                    //dd($data);
                }
                $envio                           = ApiFacturacionController::crearRetencion($data);
                $partes                          = explode("-", $envio->comprobante);
                $c_sucursal                      = $partes['0'];
                $c_caja                          = $partes['1'];
                $nfactura                        = $partes['2'];
                $retenciones->punto_emision      = $c_caja;
                $retenciones->sucursal           = $c_sucursal;
                $retenciones->secuencia          = $nfactura;
                $retenciones->estado_electronica = 2;
                $retenciones->nro_comprobante    = $envio->comprobante;
                $retenciones->save();
                return redirect()->route('retenciones_index');
            } else {
                return response()->json(['error' => 'vacio detalle']);
            }
        } else {
            return response()->json(['error' => 'vacio']);
        }
    }
    public function getNombres($full_name)
    {
        /* separar el nombre completo en espacios */
        $tokens = explode(' ', trim($full_name));
        /* arreglo donde se guardan las "palabras" del nombre */
        $names = array();
        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');

        $prev = "";
        foreach ($tokens as $token) {
            $_token = strtolower($token);
            if (in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev . $token;
                $prev    = "";
            }
        }

        $num_nombres = count($names);
        $nombres     = $apellidos     = "";
        switch ($num_nombres) {
            case 0:
                $nombres = '';
                break;
            case 1:
                $nombres = $names[0];
                break;
            case 2:
                $nombres   = $names[0];
                $apellidos = $names[1];
                break;
            case 3:
                $nombres   = $names[0] . ' ' . $names[1];
                $apellidos = $names[2];
            default:
                $nombres = $names[0] . ' ' . $names[1];
                unset($names[0]);
                unset($names[1]);

                $apellidos = implode(' ', $names);
                break;
        }

        $nombres           = mb_convert_case($nombres, MB_CASE_TITLE, 'UTF-8');
        $apellidos         = mb_convert_case($apellidos, MB_CASE_TITLE, 'UTF-8');
        $data              = array();
        $data['nombres']   = $nombres;
        $data['apellidos'] = $apellidos;
        return $data;
    }
    public function actualizar_fecha(Request $request)
    {

        $fecha        = $request['fecha'];
        $id_asiento   = $request['id_asiento'];
        $id_rentecion = $request['id_rentecion'];
        $retencion    = Ct_Retenciones::where('id', $id_rentecion)->first();
        $cabeceraa    = Ct_Asientos_Cabecera::where('id', $id_asiento)->first();
        $detalle      = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id_asiento)->get();
        $input        = [

            'fecha' => $request['fecha'],
        ];
        $retencion->update($input);
        $input1 = [

            'fecha_asiento' => $request['fecha'],
        ];
        $cabeceraa->update($input1);
        foreach ($detalle as $s) {
            $details        = Ct_Asientos_Detalle::find($s->id);
            $details->fecha = $request['fecha'];
            $details->save();
        }

        return response()->json("ok");
    }
    public function newcreate(Request $request)
    {
        //dd($request);
        $data['controlador'] = $this->controlador;
        config(['data' => $data]);
        $divisas = Ct_Divisas::all();
        $rfir    = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        //dd($rfir);
        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $acreedores = Proveedor::where('estado', '1')->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        $compras = Ct_compras::where('tipo', '<>', 3)->where('estado', '<>', 0)->where('id_empresa', $id_empresa)->where('valor_contable', '>', 0)
            ->get();
        //dd($divisas);
        return view('contable/retenciones/create_retenciones', ['divisas' => $divisas, 'acreedores' => $acreedores, 'compras' => $compras, 'rfir' => $rfir, 'rfiva' => $rfiva, 'sucursales' => $sucursales, 'empresa' => $empresa, 'min_date' => $min_date]);
    }
    public function create_anuladas(Request $request)
    {
        $divisas = Ct_Divisas::all();
        $rfir    = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        //dd($rfir);
        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $acreedores = Proveedor::where('estado', '1')->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $compras = Ct_compras::where('tipo', '<>', 3)->where('estado', '<>', 0)->where('id_empresa', $id_empresa)->where('valor_contable', '>', 0)->get();
        //dd($divisas);
        return view('contable/retenciones/create_anulado', ['divisas' => $divisas, 'acreedores' => $acreedores, 'compras' => $compras, 'rfir' => $rfir, 'rfiva' => $rfiva, 'sucursales' => $sucursales, 'empresa' => $empresa]);
    }
    public function newstore(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $sucursal = $request['sucursal'];
        $punto_emision = $request['pemision'];
        $serie = explode('-', $punto_emision);
        $sucursal = substr($punto_emision, 0, -4);
        $contador_ctv  = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $comprobacion = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->where('estado', '!=', '0')->where('anulado', 0)->where('id_compra', $request['id_compra'])->first();
        $consula_plan_acreedor = Proveedor::find($request['id_proveedor']);
        $nombre_plan = "";
        if (!is_null($consula_plan_acreedor)) {
            $nombre_plan = Plan_Cuentas::find($consula_plan_acreedor->id_cuentas);
        }
        if($nombre_plan == null){
            return ['status' => 'error', 'error' => 'El proveedor no tiene plan de cuentas asignado', 'msj' => 'El proveedor no tiene plan de cuentas asignado'];
        }
        if (is_null($comprobacion)) {
            $propiedad      = $request['secuencial'];
            $numero_factura = 0;
            if (!is_null($propiedad)) {
                $numero_factura = str_pad($propiedad, 9, "0", STR_PAD_LEFT);
            } else {
                if ($contador_ctv == 0) {
                    //return 'No Retorno nada';
                    $num            = '1';
                    $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
                } else {
                    //Obtener Ultimo Registro de la Tabla ct_ventas
                    $max_id = DB::table('ct_retenciones')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                    $max_id = intval($max_id->secuencia);

                    if (strlen($max_id) < 10) {
                        $nu             = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                }
            }
            DB::beginTransaction();
            try {
                $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request->fecha));
                if ($verifica_cierre->status == 'error') {
                    return ['status' => $verifica_cierre->status, 'error' => $verifica_cierre->message];
                }
                $id_asiento_cabecera = null;
                if ($request['anulado'] != 1) {
                    $cabeceraa = [
                        'observacion'     => $request['concepto'],
                        'fecha_asiento'   => $request['fecha'],
                        'fact_numero'     => $numero_factura,
                        'valor'           => $request['total'],
                        'estado'          => '1',
                        'id_empresa'      => $id_empresa,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal' => $sucursal,
                        //'estado_manual'   =>5,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                }
                $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
                if ($deEmpresa == '') {
                    $input = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_proveedor' => $request['id_proveedor'],
                        'id_compra' => $request['id_compra'],
                        'autorizacion' => $request['autorizacion'],
                        'nro_comprobante' => $request['pemision'] . '-' . $numero_factura,
                        'valor_fuente' => $request['valor_renta'],
                        'fecha' => $request['fecha'],
                        'valor_iva' => $request['valor_iva'],
                        'id_empresa' => $id_empresa,
                        'sucursal' => $sucursal,
                        'electronica' => $request['electronica'],
                        'anulado' => $request['anulado'],
                        'punto_emision' => $request['electronica'] == 0 ? $punto_emision : $serie[1],
                        'tipo' => '1',
                        'id_tipo' => '1',
                        'descripcion' => $request['concepto'],
                        'estado' => '1',
                        'total' => $request['total'],
                        'secuencia' => $numero_factura,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'estado_electronica' => 0
                    ];
                } else {
                    $input = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_proveedor' => $request['id_proveedor'],
                        'id_compra' => $request['id_compra'],
                        'valor_fuente' => $request['valor_renta'],
                        'fecha' => $request['fecha'],
                        'valor_iva' => $request['valor_iva'],
                        'id_empresa' => $id_empresa,
                        'sucursal' => $sucursal,
                        'electronica' => $request['electronica'],
                        'doc_electronico' => $serie[1] == 100 ? 0 : null,
                        'anulado' => $request['anulado'],
                        'punto_emision' => $request['electronica'] == 0 ? $punto_emision : ($serie[1] == 100 ?  $serie[1] : $serie[1]),
                        'tipo' => '1',
                        'id_tipo' => '1',
                        'descripcion' => $request['concepto'],
                        'estado' => '1',
                        'total' => $request['total'],
                        'nro_secuencial' => $numero_factura,
                        'nro_comprobante' => $request['pemision'] . '-' . $numero_factura,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ];
                }
                $id_retenciones = Ct_Retenciones::insertGetId($input);
                $getSri = "";
                for ($i = 0; $i < count($request['monto']); $i++) {
                    if ($request['tipo_retencion'][$i] != "") {
                        $a = "RENTA";
                        if ($request['tipo_retencion'][$i] == '1') {
                            $a = "IVA";
                        }
                        Ct_detalle_retenciones::create([
                            'id_retenciones' => $id_retenciones,
                            'observacion' => $request['concepto'],
                            'id_tipo' => $request['id_porcentaje'][$i],
                            'tipo' => $a,
                            'id_porcentaje' => $request['id_porcentaje'][$i],
                            'codigo' => $request['codigo'][$i],
                            'base_imponible' => $request['base_retencion'][$i],
                            'porcentaje' => $request['porcentaje'][$i],
                            'estado' => '1',
                            'totales' => $request['monto'][$i],
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ]);
                    }
                }
                if ($request['anulado'] != 1) {
                    $actualizar_fac = Ct_Compras::where('id', $request['id_compra'])->first();
                    $valord = (float) $request['total'];
                    $valor_total_compra = $actualizar_fac->valor_contable - $valord;
                    $valor_total_compras = $valor_total_compra;
                    $actualizar = [
                        'valor_contable' => $valor_total_compras,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'estado' => '2',
                    ];
                    $actualizar_fac->update($actualizar);
                }
                if ($request['anulado'] != 1) {
                    $consulta_nombre = DB::table('proveedor')->where('id', $request['id_proveedor'])->first();
                    $consulta_retenciones = Ct_detalle_retenciones::where('id_retenciones', $id_retenciones)->where('estado', '1')->get();
                    if (!is_null($consulta_nombre)) {
                        //$nombre_cuenta = $consulta_nombre->id_cuentas;
                        $valors = $request['total'];
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta' => $consula_plan_acreedor->id_cuentas,
                            'descripcion' => $nombre_plan->nombre,
                            'fecha' => $request['fecha'],
                            'debe' => $valors,
                            'haber' => '0',
                            'estado' => '1',
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $idusuario,
                            'ip_modificacion' => $idusuario,
                        ]);

                        foreach ($consulta_retenciones as $value) {
                            if ($value->codigo != null && $value->id_porcentaje != null) {
                                if ($value->tipo == 'RENTA') {
                                    $consulta_plan_debe = DB::table('ct_porcentaje_retenciones')->where('codigo', $value->codigo)->where('estado', 1)->where('tipo', '2')->first();
                                    //dd($consulta_plan_debe);
                                    $valors = $value->totales;
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                        'descripcion'         => $consulta_plan_debe->nombre,
                                        'fecha'               => $request['fecha'],
                                        'haber'               => $valors,
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $idusuario,
                                        'ip_modificacion'     => $idusuario,
                                    ]);
                                }
                                if ($value->tipo == 'IVA') {
                                    $consulta_plan_debe = DB::table('ct_porcentaje_retenciones')->where('codigo', $value->codigo)->where('estado', 1)->where('tipo', '1')->first();
                                    $valors             = $value->totales;
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                        'descripcion'         => $consulta_plan_debe->nombre,
                                        'fecha'               => $request['fecha'],
                                        'haber'               => $valors,
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $idusuario,
                                        'ip_modificacion'     => $idusuario,
                                    ]);
                                }
                            }
                        }
                    }
                }
                $getSri = "no";
                $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
                if ($deEmpresa == '') {
                    if ($request['electronica'] == 1) {
                        if ($empresa->electronica == 1) {
                            $getSri = $this->getSri($id_retenciones);
                        }
                    }
                } else {
                    if ($request['electronica'] == 1 and $serie[1] != 100) {
                        if ($empresa->electronica == 1) {
                            $getSri = $this->getSri($id_retenciones);
                        }
                    }
                }
                DB::commit();
                Contable::recovery_price($request['id_compra'], 'C');
                return ['status' => 'success', 'id' => $id_retenciones, 'sri' => $getSri, 'error' => 'no'];
                //return $id_retenciones;
            } catch (\Exception $e) {
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return ['status' => 'error', 'error' => $e->getMessage(), 'line' => $e->getLine()];
            }
        }
        return response()->json(['error' => 'si']);
    }
    public function send_information(Request $request)
    {
        try {
            $retencion_cabecera = Ct_Retenciones::where('id', $request->id)->first();
            $retencion_cabecera->doc_electronico = 0;
            $retencion_cabecera->update();
            return ['err' => false];
        } catch (\Exception $th) {
            return ['err' => true];
            // echo $th->getMessage().'<br/>';
            // echo $th->getLine();
            // exit;
        }
    }

    public function importar_porcentajes_retenciones()
    {
        try {
            ini_set('max_execution_time', 600);
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            DB::beginTransaction();
            $codigos = De_Codigo_Impuestos::where('estado', 1)->get();
            $cont = 0;
            $cuenta_activo = null;
            $cuenta_pasivo = null;

            foreach ($codigos as $codigo) {
                if ($codigo->codigo_impuesto == 1) { //renta
                    $tipo = 2;
                } else { //iva
                    $tipo = 1;
                }
                // if ($codigo->codigo_impuesto == 1) {//renta
                //     $porcentajeBase = Ct_Porcentaje_Retenciones::where('tipo', $tipo)->where('codigo_interno', $codigo->)->first();
                //     dd($porcentajeBase,$codigo->descripcion);
                // } else {//iva

                // }
                $porcentajeBase = Ct_Porcentaje_Retenciones::where('tipo', $tipo)->where('codigo_interno', $codigo->codigo)->first();

                // if ($cont == 6) {
                //     dd($codigo,$tipo,$codigo->tipo_impuesto,$codigo->codigo,$porcentajeBase,substr($codigo->descripcion,0,strlen($codigo->descripcion)-1));
                // }

                if (!is_null($porcentajeBase)) {
                    $porcentaje = [
                        'codigo' => $codigo->codigo,
                        'codigo_nombre' => $codigo->codigo,
                        'nombre' => $porcentajeBase->nombre,
                        'tipo' => $codigo->tipo_impuesto,
                        'valor' => $codigo->porcentaje,
                        'cuenta_clientes' => $porcentajeBase->cuenta_clientes,
                        'cuenta_acreedores' => $porcentajeBase->cuenta_acreedores,
                        'nota' => $codigo->porcentaje . '%',
                        'estado' => '1',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'cuenta_deudora' => $porcentajeBase->cuenta_deudora,
                        'codigo_interno' => $codigo->codigo,
                    ];
                } else {
                    $plan_cuenta = [];
                    $cuenta_activo = null;
                    $cuenta_pasivo = null;
                    switch ($codigo->codigo_impuesto) {
                        case 1: //RENTA
                            switch ($codigo->porcentaje) {
                                case 22:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%22%')->get();
                                    break;
                                case 10:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%10%')->get();
                                    break;
                                case 8:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%8%')->get();
                                    break;
                                case 2.75:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%2.75%')->get();
                                    break;
                                case 2:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%2%')->get();
                                    break;
                                case 1.75:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%1.75%')->get();
                                    break;
                                case 1:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%1%')->get();
                                    break;
                                case 1:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%RENTA%')->where('nombre', 'like', '%1%')->get();
                                    break;
                                default:
                                    $plan_cuenta = [];
                                    break;
                            }
                            break;
                        case 2: //IVA
                            switch ($codigo->porcentaje) {
                                case 12:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%IVA%')->where('nombre', 'like', '%12%')->get();
                                    break;
                                case 50:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%IVA%')->where('nombre', 'like', '%50%')->get();
                                    break;
                                case 70:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%IVA%')->where('nombre', 'like', '%70%')->get();
                                    break;
                                case 100:
                                    $plan_cuenta = Plan_Cuentas::where('nombre', 'like', '%IVA%')->where('nombre', 'like', '%100%')->get();
                                    break;
                                default:
                                    $plan_cuenta = [];
                                    break;
                            }
                            break;
                        case 3:
                            break;
                        default:
                            $tipo = null;
                            break;
                    }
                    if (count($plan_cuenta) == 1) {
                        $cuenta_activo = $plan_cuenta[0]->id;
                    } else if (count($plan_cuenta) > 1) {
                        foreach ($plan_cuenta as $value) {
                            $cuenta_padre = explode('.', $value->id);
                            if (isset($cuenta_padre[0])) {
                                if ($cuenta_padre[0] == '1') {
                                    $cuenta_activo = $value->id;
                                }
                                if ($cuenta_padre[0] == '2') {
                                    $cuenta_pasivo = $value->id;
                                }
                            }
                        }
                        //dd($plan_cuenta,$cuenta_padre,$cuenta_activo,$cuenta_pasivo);
                    }

                    $porcentaje = [
                        'codigo' => $codigo->codigo,
                        'codigo_nombre' => $codigo->codigo,
                        'nombre' => $codigo->descripcion,
                        'tipo' => $codigo->tipo_impuesto,
                        'valor' => $codigo->porcentaje,
                        'cuenta_clientes' => $cuenta_activo,
                        'cuenta_acreedores' => $cuenta_pasivo,
                        'nota' => $codigo->porcentaje . '%',
                        'estado' => '1',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'cuenta_deudora' => null,
                        'codigo_interno' => $codigo->codigo,
                    ];
                    //dd($cuenta_activo,$cuenta_pasivo,$porcentaje);
                }
                Ct_Porcentaje_Retenciones::create($porcentaje);
                $cont++;
            }
            //DB::commit();
            $result = Ct_Porcentaje_Retenciones::where('id', '>', 30)->get();
            DB::rollBack();
            echo '<pre>';
            print_r($result);
            exit;
            return ['status' => 'Ok', 'respuesta' => 'Realizado con exito'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['respuesta' => 'error', 'mensaje' => $ex->getMessage(), 'linea' => $ex->getLine()];
        }
    }
}
