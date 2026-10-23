<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Session;
use Sis_medico\AfActivo;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\AfFacturaActivoDetalle;
use Sis_medico\AfGrupo;
use Sis_medico\AfTipo;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Marca;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Af_Bodega_Serie_Color;
use Sis_medico\AfSubTipo;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Globales;
use Sis_medico\LogAsiento;
use Sis_medico\Log_Contable;
use Sis_medico\AfActivo_Accesorios;
use Sis_medico\Af_Activo_Archivos;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Agenda;
use Sis_medico\Log_Ordenes;
use Sis_medico\Utilidades;

class DocumentoFacturaController extends Controller
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
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $documentos  = AfFacturaActivoCabecera::where('id_empresa', $id_empresa)->orderBy('id', 'DESC')->where('estado', '!=', '3')->paginate('20');
        $proveedores = Proveedor::get();
        $usuarios    = User::where('id_tipo_usuario', 1)->get();
        $registros   = array();
        return view('activosfijos/documentos/factura/index', ['documentos' => $documentos, 'empresa' => $empresa, 'proveedores' => $proveedores, 'usuarios' => $usuarios]);
    }

    public function search(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id'             => $request['id'],
            'proveedor'      => $request['nombre_proveedor'],
            'observacion'    => $request['detalle'],
            'secuencia'      => $request['secuencia_f'],
            'fecha_compra'   => $request['fecha'],
            'id_empresa'     => $id_empresa,
            'id_usuariocrea' => $request['id_usuariocrea'],
            'id_asiento'     => $request->asiento
        ];

        $documentos = $this->doSearchingQuery($constraints);

        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $proveedores = Proveedor::get();
        $usuarios    = User::where('id_tipo_usuario', 1)->get();

        return view('activosfijos/documentos/factura/index', ['documentos' => $documentos, 'searchingVals' => $constraints, 'empresa' => $empresa, 'proveedores' => $proveedores, 'usuarios' => $usuarios]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = AfFacturaActivoCabecera::query()->select('af_factura_activo_cab.*');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where('af_factura_activo_cab.' . $fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(20);
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $activo = AfActivo::where('id', '=', $id)->first();
        // $plan           = Plan_Cuentas::all();
        $plan         = array();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', '!=', 2)->get();
        // $responsables   = array();
        $productos = Producto::where('estado', '!=', 0)->get();
        $marcas    = Marca::where('estado', '!=', 0)->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $empleados    = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();



        // Redirect to user list if updating user wasn't existed
        if ($activo == null || count($activo) == 0) {
            return redirect()->intended('/dashboard');
        }
        return view('activosfijos/mantenimientos/activofijo/edit', ['activo' => $activo, 'plan' => $plan, 'tipos' => $tipos, 'responsables' => $responsables, 'productos' => $productos, 'marcas' => $marcas, 'empleados' => $empleados, 'af_colores' => $af_colores, 'af_series' => $af_series, 'sub_tipos' => $sub_tipos, 'af_responsables' => $af_responsables]);
    }

    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $proveedor       = proveedor::where('estado', '1')->get();

        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();

        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();

        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)
            ->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        $tipos_comp = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();
        $c_tributario   = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', 1)->orwhere('id_tipo_usuario', 20)->get();
        $productos    = Ct_productos::where('id_empresa', $id_empresa)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();

        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $empleados    = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $term         = Ct_Termino::where('estado', '1')->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();


        return view('activosfijos/documentos/factura/create', ['divisas' => $divisas, 'sucursales'    => $sucursales, 'punto'    => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, 'proveedor' => $proveedor, 'tipos_comp' => $tipos_comp, 'tipos'  => $tipos, 'grupos'        => $grupos, 'responsables'     => $responsables, 'productos'  => $productos, 'marcas' => $marcas, 'c_tributario' => $c_tributario, 'sub_tipos' => $sub_tipos, 'empleados' => $empleados, 'af_series' => $af_series, 'af_colores' => $af_colores, 'term' => $term, 'af_responsables' => $af_responsables, 'min_date' => $min_date]);
    }

    private function validateInput($request)
    {
        $this->validate($request, []);
    }

    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->fecha_asiento);
        $verifica_cierre = json_decode($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        //dd($request->all());

        // $id_tipos = array_search('mdcodigo', $request->all());
        DB::beginTransaction();
        try {

            // if (!is_null($request->select_orden)) {
            //     $orden_af = Log_Ordenes::where('id_orden', $request->select_orden)->where('tipo', '1')->where('estado', '1')->first();

            //     $log_ord = [
            //         'estado'               => 0,
            //         'id_usuariomod'        =>  $idusuario,
            //     ];

            //     $orden_af->update($log_ord);
            // }
            $numero                   = AfFacturaActivoCabecera::max('id');
            $numero                   = $numero + 1;
            // $request['fecha_asiento'] = str_replace('/', '-', $request['fecha_asiento']);
            // $request['fecha_asiento'] = \Carbon\Carbon::parse($request['fecha_asiento'])->timestamp;
            // $request['fecha_asiento'] = date('Y-m-d', $request['fecha_asiento']);

            // $request['fecha_caduca'] = str_replace('/', '-', $request['fecha_caduca']);
            // $request['fecha_caduca'] = \Carbon\Carbon::parse($request['fecha_caduca'])->timestamp;
            // $request['fecha_caduca'] = date('Y-m-d', $request['fecha_caduca']);

            // $request['fecha_compra'] = str_replace('/', '-', $request['fecha_compra']);
            // $request['fecha_compra'] = \Carbon\Carbon::parse($request['fecha_compra'])->timestamp;
            // $request['fecha_compra'] = date('Y-m-d', $request['fecha_compra']);

            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;

            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;

            $id_asiento_cabecera = $this->store_asiento($request, $c_sucursal, $c_caja);
            if ($id_asiento_cabecera['respuesta'] == "error") {
                return ['respuesta' => 'error', 'msj' => $id_asiento_cabecera['msj']];
            }
            $input               = [
                'numero'           => str_pad($numero, 10, "0", STR_PAD_LEFT),
                'proveedor'        => $request['proveedor'],
                'tipo'             => $request['tipo_transaccion'],
                'id_asiento'       => $id_asiento_cabecera['id_asiento'],
                'fecha_asiento'    => $request['fecha_asiento'],
                'credito_tributario' => $request['credito_tributario'],
                'fecha_caduca'     => $request['fecha_caduca'],
                'divisas'          => $request['divisas'],
                'termino'          => $request['termino'],
                'ord_compra'       => $request['ord_compra'],
                'nro_autorizacion' => $request['nro_autorizacion'],
                'fecha_compra'     => $request['fecha_compra'],
                'serie'            => $request['serie_factura'],
                'secuencia'        => $request['secuencia'],
                'tipo_comprobante' => $request['tipo_comprobante'],
                'subtotal'         => $request['base1'],
                'subtotal0'        => $request['subtotal_01'],
                'subtotal12'       => $request['subtotal_121'],
                'descuento'        => $request['descuento1'],
                'impuesto'         => $request['tarifa_iva1'],
                'total'            => $request['total1'],
                'estado'           => 1,
                'id_empresa'       => $id_empresa,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'sucursal'         => $c_sucursal,
                'punto_emision'    => $c_caja,
                'observacion'      => $request['concepto'],
            ];

            $consulta_fecha = Ct_Termino::where('id', $request['termino'])->first();
            $modfecha = "";
            if ($consulta_fecha != null) {
                $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($request['fecha']));
                $modfecha = date("Y-m-d", $nueva_fecha);
            }



            $numero_factura = "{$request['serie_factura']}-{$request['secuencia']}";

            //$numero_factura = $request['serie_factura'] . '-' . $request['secuencia'];

            $request['factura_id'] = AfFacturaActivoCabecera::insertGetId($input);

            // $base = $request['base1'];
            // $base12 = 0;
            // if ($request['tarifa_iva1'] > 0) {
            //     $base = 0;
            //     $base12 = $request['base1'];
            // }
            $base = $request['subtotal_01'];
            $base12 = $request['subtotal_121'];
            //Guardado en ct_compras del
            //$request['serie_factura'] = "{$c_sucursal}-{$c_caja}";
            $input2 = [
                'id_asiento_cabecera'           => $id_asiento_cabecera['id_asiento'],
                'fecha'                         => $request['fecha_compra'],
                'numero'                        => $numero_factura,
                'archivo_sri'                   => '1',
                'proveedor'                     => $request['proveedor'],
                'termino'                       => $request['termino'],
                'secuencia_f'                   => $numero_factura,
                //'observacion'                   => "AF-" . $numero_factura,
                'observacion'                   => $request['concepto'],
                'tipo'                          => '2',
                'sucursal'                      => $c_sucursal,
                'punto_emision'                 => $c_caja,
                'valor_contable'                => $request['total1'],
                'fecha_termino'                 => $request['fecha_caduca'],
                'orden_compra'                  => $request['ord_compra'],
                'f_caducidad'                   => $request['fecha_caduca'],
                'tipo_gasto'                    => $request['factura_id'],
                'autorizacion'                  => $request['nro_autorizacion'],
                'f_autorizacion'                => $request['fecha_compra'],
                'id_empresa'                    => $id_empresa,
                'serie'                         => $request['serie_factura'],
                'secuencia_factura'             => $request['secuencia'],
                'credito_tributario'            => $request['credito_tributario'],
                'tipo_comprobante'              => $request['tipo_comprobante'],
                'subtotal_0'                    => $base,
                'subtotal_12'                   => $base12,
                'subtotal'                      => $request['base1'],
                'descuento'                     => $request['descuento1'],
                'iva_total'                     => $request['tarifa_iva1'],
                'ice_total'                     => "0",
                'total_final'                   => $request['total1'],
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];

            $id_compra = Ct_compras::insertGetId($input2);

            if ($id_compra != '' or !is_null($id_compra)) {
                DB::commit();
                $request['idCompra'] = $id_compra;
            }
            //$request['idCompra'] = $id_compra;
            //$retornar = $this->store_detalles($request);
            // $request['modulo'] = 'AF';
            $retornar = $this->store_AfActivo($request);
            if ($retornar["respuesta"] == "error") {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => "Error al guardar...", "exp" => $retornar['msj'], "mod" => $retornar["mod"]];
            }
            $det_compras = $this->store_det_compras($request);
            // $accesorios = $this->afAccesorios($request, $retornar["id_af"]);
            if ($retornar["respuesta"] == "success" and $det_compras["respuesta"] == "success") {
                DB::commit();
                return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_asiento' => $id_asiento_cabecera['id_asiento']];
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => $det_compras['msj'], "mod" => $retornar["msj"]];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'mod' => 'store', 'linea' => $e->getLine()];
        }
    }

    public static function store_detalles($request, $activo_id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        DB::beginTransaction();
        try {
            for ($x = 0; $x < count($activo_id); $x++) {

                AfFacturaActivoDetalle::create([
                    'fact_activo_id'  => $request['factura_id'],
                    'activo_id'       => $activo_id[$x],
                    'cantidad'        => $request['cantidad'][$x],
                    'codigo'          => $request['codigo'][$x],
                    'nombre'          => strtoupper($request['descrip_prod'][$x]),
                    'costo'           => $request['precio'][$x],
                    'subtotal'        => ($request['precio'][$x] * $request['cantidad'][$x]) - $request['desc'][$x],
                    'descuento'       => $request['desc'][$x],
                    'porc_descuento'  => $request['descpor'][$x],
                    'total'           => $request['total_valor'][$x],
                    'estado'          => 1,
                    'iva'             => $request['check_iva'][$x],
                    'valor_iva'       => $request['val_iva'][$x],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'observacion'     => $request['observacion'][$x],
                ]);
            }


            // /if (isset($request->transporte) or !is_null($request->transporte)) {
            if (floatval($request->codigo_trans) > 0) {
                for ($y = 0; $y < count($request->codigo_trans); $y++) {
                    $detalle_trans = [
                        'fact_activo_id'       => $request['factura_id'],
                        //'activo_id'            => $activo_id,
                        'codigo'               => $request['codigo_trans'][$y],
                        'nombre'               => strtoupper($request['descrip_prod_trans'][$y]),
                        'subtotal'             => ($request['precio_trans'][$y] * $request['cantidad_trans'][$y]) - $request['desc_trans'][$y],
                        'cantidad'             => $request['cantidad_trans'][$y],
                        'costo'                => $request['precio_trans'][$y],
                        'porc_descuento'       => $request['descpor_trans'][$y],
                        'estado'               => '1',
                        'iva'                  => $request["val_iva_trans"][$y] > 0 ? 1 : 0,
                        'valor_iva'            => $request['val_iva_trans'][$y],
                        'descuento'            => $request['desc_trans'][$y],
                        'total'                => $request['total_valor_trans'][$y],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'observacion'          => $request['observacion_trans'][$y],
                    ];
                    AfFacturaActivoDetalle::create($detalle_trans);
                }
            }



            //if (isset($request->gasto) or !is_null($request->gasto)) {

            if (floatval($request['id_plan']) > 0) {
                for ($z = 0; $z < count($request->id_plan); $z++) {
                    $plan_gasto = Plan_Cuentas_Empresa::where('id_plan', $request['id_plan'][$z])->orwhere('plan', $request['id_plan'][$z])->first();
                    $plan_gasto = Plan_Cuentas::find(is_null($plan_gasto->id_plan) ? $plan_gasto->plan : $plan_gasto->id_plan);
                    $detalle_gasto = [
                        'fact_activo_id'       => $request['factura_id'],
                        //'activo_id'            => $activo_id,
                        'codigo'               => $request['id_plan'][$z],
                        'nombre'               => $plan_gasto->nombre, // revisar
                        'subtotal'             => ($request['precio_gasto'][$z] * $request['cantidad_gasto'][$z]) - $request['desc_gasto'][$z],
                        'cantidad'             => $request['cantidad_gasto'][$z],
                        'costo'                => $request['precio_gasto'][$z],
                        'porc_descuento'       => $request['descpor_gasto'][$z],
                        'estado'               => '1',
                        'iva'                  => $request['val_iva_gasto'][$z] > 0 ? 1 : 0,
                        'valor_iva'            => $request['val_iva_gasto'][$z],
                        'descuento'            => $request['desc_gasto'][$z],
                        'total'                => $request['total_valor_gasto'][$z],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'observacion'          => $request['observacion_gasto'][$z],
                    ];
                    AfFacturaActivoDetalle::create($detalle_gasto);
                }
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), "mod" => "store_detalles"];
        }
    }

    public static function store_AfActivo($request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_activos = array();
        DB::beginTransaction();
        try {

            $q = 0;
            for ($i = 0; $i < count($request['codigo']); $i++) {


                //$a = $i + 1;
                //$id_tipo = $request["mdtipo{$a}"];

                if (isset($request["modulo"]) or $request["modulo"] == "importacion") {
                    if ($request["check_af"][$i] == 1) {

                        $a = $request["mdtiposid"][$q];

                        if (isset($request["pre_orden"]) and $request["pre_orden"] == "1") {
                            $estado = 3;
                        } else {
                            $estado = 1;
                        }

                        $tipo = AfTipo::find($request["mdtipo{$a}"]);
                        $input = [
                            'codigo'          => $request["mdcodigo{$a}"] . '-' . $request["mdcodigo_num{$a}"],
                            'nombre'          => strtoupper($request["mdnombre{$a}"]),
                            'descripcion'     => strtoupper($request["mddescripcion{$a}"]),
                            'tipo_id'         => $request["mdtipo{$a}"],
                            'subtipo_id'      => $request["mdgrupo{$a}"],
                            'responsable'     => strtoupper($request["mdresponsable{$a}"]),
                            'acreedor'        => strtoupper($request['proveedor']),
                            'marca'           => strtoupper($request["mdmarca{$a}"]),
                            'color'           => strtoupper($request["mdcolor{$a}"]),
                            'modelo'          => strtoupper($request["mdmodelo{$a}"]),
                            'serie'           => strtoupper($request["mdserie{$a}"]),
                            'procedencia'     => strtoupper($request["mdprocedencia{$a}"]),
                            'costo'           => $request["precioneto"][$i],
                            'fecha_compra'    => $request['fecha_compra'],
                            'factura'         => $request['serie_factura'] . '-' . $request['secuencia'],
                            'estado'          => $estado,
                            'estado_activo'   => 1,
                            'empresa'         => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'tasa'            => $tipo->tasa,
                            'tipo_tasa'       => $tipo->tipo_tasa,
                            'vida_util'       => $tipo->vidautil,
                            'ubicacion'       => strtoupper($request["mdubicacion{$a}"]),
                            'codigo_text'     => $request["mdcodigo{$a}"],
                            'codigo_num'      => $request["mdcodigo_num{$a}"],
                            'foto_activo'     => $request["fotoactivo{$a}"],
                            'foto_acc'        => $request["fotoacc{$a}"],
                        ];
                        $activo_id = AfActivo::insertGetId($input);

                        $accesorios = DocumentoFacturaController::afAccesorios($request, $activo_id, $a);

                        array_push($id_activos, $activo_id);

                        if ($accesorios["respuesta"] == "error") {
                            DB::rollBack();
                            return $accesorios;
                        }
                        $q++;
                    }
                } else {
                    $a = $request["mdtiposid"][$i];
                    if (isset($request["pre_orden"]) or $request["pre_orden"] == "1") {
                        $estado = 3;
                    } else {
                        $estado = 1;
                    }
                    $tipo = AfTipo::find($request["mdtipo{$a}"]);

                    if (!is_null($tipo)) {

                        $input = [
                            'codigo'          => $request["mdcodigo{$a}"] . '-' . $request["mdcodigo_num{$a}"],
                            'nombre'          => strtoupper($request["mdnombre{$a}"]),
                            'descripcion'     => strtoupper($request["mddescripcion{$a}"]),
                            'tipo_id'         => $request["mdtipo{$a}"],
                            'subtipo_id'      => $request["mdgrupo{$a}"],
                            'responsable'     => strtoupper($request["mdresponsable{$a}"]),
                            'acreedor'        => strtoupper($request['proveedor']),
                            'marca'           => strtoupper($request["mdmarca{$a}"]),
                            'color'           => strtoupper($request["mdcolor{$a}"]),
                            'modelo'          => strtoupper($request["mdmodelo{$a}"]),
                            'serie'           => strtoupper($request["mdserie{$a}"]),
                            'procedencia'     => strtoupper($request["mdprocedencia{$a}"]),
                            'costo'           => $request["precioneto"][$i],
                            'fecha_compra'    => $request['fecha_compra'],
                            'factura'         => $request['serie_factura'] . '-' . $request['secuencia'],
                            'estado'          => $estado,
                            'estado_activo'   => 1,
                            'empresa'         => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'tasa'            => $tipo->tasa,
                            'tipo_tasa'       => $tipo->tipo_tasa,
                            'vida_util'       => $tipo->vidautil,
                            'ubicacion'       => strtoupper($request["mdubicacion{$a}"]),
                            'codigo_text'     => $request["mdcodigo{$a}"],
                            'codigo_num'      => $request["mdcodigo_num{$a}"],
                            'foto_activo'        => $request["fotoactivo{$a}"],
                            'foto_acc'           => $request["fotoacc{$a}"],
                        ];

                        $activo_id = AfActivo::insertGetId($input);
                        $accesorios = DocumentoFacturaController::afAccesorios($request, $activo_id, $a);


                        array_push($id_activos, $activo_id);

                        if ($accesorios["respuesta"] == "error") {
                            DB::rollBack();
                            return $accesorios;
                        }
                    }
                }
            }



            if ($request['modulo'] != 'importacion' or is_null($request['modulo'])) {

                $detalles = DocumentoFacturaController::store_detalles($request, $id_activos);
                if ($detalles["respuesta"] == "error") {
                    DB::rollBack();
                    return $detalles;
                }
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_af' => $activo_id, 'AfActivo' => $id_activos];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), "mod" => "store_AfActivo"];
        }
    }

    public static function store_det_compras($request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            $porIva = Utilidades::porcentajeIva();
            if ($porIva == '') {
                $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
                return view('errors/204', $data);
            }
            for ($i = 0; $i < count($request['codigo']); $i++) {
                $detalle = [
                    'id_ct_compras'        => $request['idCompra'],
                    'codigo'               => $request['codigo'][$i],
                    'nombre'               => strtoupper($request['descrip_prod'][$i]),
                    'cantidad'             => $request['cantidad'][$i],
                    'precio'               => $request['precio'][$i],
                    'descuento_porcentaje' => $request['descpor'][$i],
                    'estado'               => '1',
                    'descuento'            => $request['desc'][$i],
                    'extendido'            => ($request['precio'][$i] * $request['cantidad'][$i]),
                    'detalle'              =>  strtoupper($request['descrip_prod'][$i]),
                    'iva'                  => $request['check_iva'][$i],
                    // 'porcentaje'           => "0.12",
                    'porcentaje'           => $porIva->iva,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];

                Ct_detalle_compra::create($detalle);
            }


            if (intval($request['codigo_trans']) > 0) {
                //if (isset($request->transporte) or !is_null($request->transporte)) {
                for ($c = 0; $c < count($request->codigo_trans); $c++) {
                    $detalle_trans = [
                        'id_ct_compras'        => $request['idCompra'],
                        'codigo'               => $request['codigo_trans'][$c],
                        'nombre'               => strtoupper($request['descrip_prod_trans'][$c]),
                        'cantidad'             => $request['cantidad_trans'][$c],
                        'precio'               => $request['precio_trans'][$c],
                        'descuento_porcentaje' => $request['descpor_trans'][$c],
                        'estado'               => '1',
                        'descuento'            => $request['desc_trans'][$c],
                        'extendido'            => ($request['precio_trans'][$c] * $request['cantidad_trans'][$c]),
                        'detalle'              =>  strtoupper($request['descrip_prod_trans'][$c]),
                        'iva'                  => $request['check_iva'][$c],
                        // 'porcentaje'           => "0",
                        'porcentaje'           => $porIva->iva,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];
                    Ct_detalle_compra::create($detalle_trans);
                }
            }
            if (intval($request['id_plan']) > 0) {

                for ($e = 0; $e < count($request->id_plan); $e++) {

                    $plan_gasto = Plan_Cuentas_Empresa::where('id_plan', $request['id_plan'][$e])->orwhere('plan', $request['id_plan'][$e])->first();
                    $plan_gasto = Plan_Cuentas::find(is_null($plan_gasto->id_plan) ? $plan_gasto->plan : $plan_gasto->id_plan);
                    $detalle_gasto = [
                        'id_ct_compras'        => $request['idCompra'],
                        'codigo'               => $request['id_plan'][$e],
                        'nombre'               => $plan_gasto->nombre,  //revisar
                        'cantidad'             => $request['cantidad_gasto'][$e],
                        'precio'               => $request['precio_gasto'][$e],
                        'descuento_porcentaje' => $request['descpor_gasto'][$e],
                        'estado'               => '1',
                        'descuento'            => $request['desc_gasto'][$e],
                        'extendido'            => ($request['precio_gasto'][$e] * $request['cantidad_gasto'][$e]),
                        'detalle'              => $plan_gasto->nombre, //revisar
                        'iva'                  => $request['check_iva'][$e],
                        // 'porcentaje'           => "0",
                        'porcentaje'           => $porIva->iva,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];
                    Ct_detalle_compra::create($detalle_gasto);
                }
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), "mod" => "store_det_compras"];
        }
    }

    public static function afAccesorios($request, $id_activo, $a)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        try {
            //for($i=0; $i<count($request->cantidad); $i++){
            //$a = $i+1;

            if (!is_null($request["accesorio{$a}"])) {
                for ($x = 0; $x < count($request["accesorio{$a}"]); $x++) {


                    AfActivo_Accesorios::create([
                        'id_activo'            => $id_activo,
                        'nombre'               => $request["nombre_ac{$a}"][$x],
                        'marca'                => $request["marca_ac{$a}"][$x],
                        'modelo'               => $request["modelo_ac{$a}"][$x],
                        'serie'                => $request["serie_ac{$a}"][$x],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ]);
                }
            }


            //}
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), "mod" => "afAccesorios"];
        }
    }


    public function store_asiento($request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = Session::get('id_empresa');
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $globales = Ct_Globales::where('id_modulo', 2)->where('id_empresa', $id_empresa)->first();
        if (is_null($globales)) {
            return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta DEL IVA para el asiento de Activo Fijo'];
        }
        $global_trans = Ct_globales::where('id_modulo', 4)->where('id_empresa', $id_empresa)->first();
        if (is_null($global_trans)) {
            return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta DEL IVA para el transporte de Activo Fijo'];
        }
        $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $c_sucursal = $cod_sucurs->codigo_sucursal;

        $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $c_caja     = $cod_caj->codigo_caja;

        $cabecera   = [
            // 'observacion'     => 'INGRESO ACTIVO FIJO POR FACTURA: ' . $request['serie_factura'] . $request['secuencia'],
            'sucursal'        => $c_sucursal,
            'punto_emision'   => $c_caja,
            'observacion'     => $request['concepto'],
            'fecha_asiento'   => $request['fecha_asiento'],
            'fact_numero'     => $request['serie_factura'] . $request['secuencia'],
            'valor'           => $request['total1'],
            'estado'          => '2',
            'id_empresa'      => $id_empresa,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];



        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);


        if (floatval($request['tarifa_iva1']) > 0) {

            if (isset($request->transporte) or !is_null($request->transporte) or isset($request->gasto) or !is_null($request->gasto)) {

                $plan_cuentas = Plan_Cuentas::find($globales->debe);
                if (is_null($plan_cuentas)) {
                    return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta DEL IVA para el asiento de Activo Fijo'];
                }
                $sum_trans = 0;
                $sum_gastos = 0;
                if (isset($request['val_iva_trans'])) {
                    for ($trans = 0; $trans < count($request['val_iva_trans']); $trans++) {
                        $sum_trans += $request['val_iva_trans'][$trans];
                    }
                }
                if (isset($request['val_iva_gasto'])) {
                    for ($gast = 0; $gast < count($request['val_iva_gasto']); $gast++) {
                        $sum_gastos += $request['val_iva_gasto'][$gast];
                    }
                }

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $request['fecha_compra'],
                    //'debe'                => $request['tarifa_iva1'] - $request['val_iva_trans'] - $request['val_iva_gasto'],
                    'debe'                => $request['tarifa_iva1'] - $sum_trans - $sum_gastos,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);


                $plan_trans = Plan_Cuentas::find($global_trans->debe);
                if (is_null($plan_trans)) {
                    return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta de iva para Transporte del activo fijo'];
                }
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_trans->id,
                    'descripcion'         => $plan_trans->nombre,
                    'fecha'               => $request['fecha_compra'],
                    //'debe'                => $request['val_iva_trans'] + $request['val_iva_gasto'],
                    'debe'                => $sum_trans + $sum_gastos,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            } else {
                $plan_cuentas = Plan_Cuentas::find($globales->debe);
                if (is_null($plan_cuentas)) {
                    return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta DEL IVA para el asiento de Activo Fijo'];
                }
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $request['fecha_compra'],
                    'debe'                => $request['tarifa_iva1'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            if ($request['total1'] != 0) {
                $plan = Ct_Configuraciones::where('id', '101')->first();
                //  for ($i = 0; $i < count($request->codigo); $i++) {

                for ($i = 0; $i < count($request['codigo']); $i++) {

                    $a = $request["mdtiposid"][$i];
                    //$a = $i + 1;
                    $id_tipo = $request["mdtipo{$a}"];


                    $tipo = AfTipo::find($id_tipo);
                    if (!is_null($tipo)) {
                        // $trans = 0;
                        // if (isset($request->transporte) or !is_null($request->transporte) or isset($request->gasto) or !is_null($request->gasto)) {
                        //     $trans = $request['total1'] - $request['tarifa_iva1'] - $request->precioneto_trans - $request->precioneto_gasto;
                        // } else {
                        //     $trans = $request['total1'] - $request['tarifa_iva1'];
                        // }

                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $tipo->cuentamayor,
                            'descripcion'         => strtoupper($tipo->cuenta_mayor->nombre), //aqui puse el nombre de la cuenta del acreedor
                            'fecha'               => $request['fecha_compra'],
                            'debe'                => $request['precioneto'][$i] + $request['desc'][$i],
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                }



                $id_plan_config = LogConfig::busqueda("5.2.02.03.06");

                $plan = Plan_Cuentas_Empresa::where('id_plan', $id_plan_config)->first();
                if (is_null($plan)) {
                    return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta'];
                }
                //if (isset($request->transporte) or !is_null($request->transporte)) {
                if (floatval($request['precioneto_trans']) > 0) {

                    $sum_trans = 0;
                    for ($trans = 0; $trans < count($request['precioneto_trans']); $trans++) {
                        $sum_trans += $request['precioneto_trans'][$trans];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $id_plan_config, //CAMBIAR A CONFIGURACIONWS
                        'descripcion'         => $plan->nombre, //aqui puse el nombre de la cuenta del acreedor
                        'fecha'               => $request['fecha_compra'],
                        'haber'               => '0',
                        'debe'                => $sum_trans,
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $idusuario,
                        'ip_modificacion'     => $idusuario,
                    ]);
                }




                //if (isset($request->gasto) or !is_null($request->gasto)) {
                if (floatval($request['precioneto_gasto']) > 0) {

                    // $sum_gastos = 0;
                    // for ($gas = 0; $gas < count($request['precioneto_gasto']); $gas++) {
                    //     $sum_gastos += $request['precioneto_gasto'][$gas];
                    // }

                    for ($val_gasto = 0; $val_gasto < count($request->id_plan); $val_gasto++) {
                        $plancuenta = Plan_Cuentas_Empresa::where("id_plan", $request->id_plan[$val_gasto])->orwhere("plan", $request->id_plan[$val_gasto])->first();
                        $plancuenta = Plan_Cuentas::find(is_null($plancuenta->id_plan) ? $plancuenta->plan : $plancuenta->id_plan);

                        //$plancuenta = Plan_Cuentas::find($request->id_plan);
                        //aqui ando 33
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plancuenta->id,
                            'descripcion'         => $plancuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                            'fecha'               => $request['fecha_compra'],
                            'haber'               => '0',
                            //'debe'                => $sum_gastos,
                            'debe'                => $request['precioneto_gasto'][$val_gasto],
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                }
            }
        } else {


            if ($request['total1'] != 0) {
                //for ($i = 0; $i < count($request->codigo); $i++) {
                for ($i = 0; $i < count($request->codigo); $i++) {
                    // if(Auth::user()->id == '0957258056'){
                    // }
                    //$a = $i + 1;
                    $a = $request["mdtiposid"][$i];
                    $id_tipo = $request["mdtipo{$a}"];
                    $tipo = AfTipo::find($id_tipo);
                    if (!is_null($tipo)) {
                        $trans = 0;
                        // if (isset($request->transporte) or !is_null($request->transporte) or isset($request->gasto) or !is_null($request->gasto)) {
                        //     $trans = $request['total1'] - $request['tarifa_iva1'] - $request->precioneto_trans - $request->precioneto_gasto;
                        // } else {
                        //     $trans = $request['total1'] - $request['tarifa_iva1'];
                        // }
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $tipo->cuentamayor,
                            'descripcion'         => strtoupper($tipo->cuenta_mayor->nombre), //aqui puse el nombre de la cuenta del acreedor
                            'fecha'               => $request['fecha_compra'],
                            'debe'                => $request['precioneto'][$i] + $request['desc'][$i],
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                }
                $valor_trans = 0;
                if (isset($request->codigo_trans)) {
                    if (count($request->codigo_trans) > 0) {
                        for ($o = 0; $o < count($request->codigo_trans); $o++) {
                            $valor_trans += $request['precioneto_trans'][$o];
                        }
                    }
                }
                //if (isset($request->transporte) or !is_null($request->transporte)) {
                $id_plan_config = LogConfig::busqueda("5.2.02.03.06");

                $plan = Plan_Cuentas_Empresa::where('id_plan', $id_plan_config)->first();
                if (is_null($plan)) {
                    return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta'];
                }
                if ($valor_trans > 0) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $id_plan_config,
                        'descripcion'         => $plan->nombre, //aqui puse el nombre de la cuenta del acreedor
                        'fecha'               => $request['fecha_compra'],
                        'haber'               => '0',
                        'debe'                => $valor_trans,
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $idusuario,
                        'ip_modificacion'     => $idusuario,
                    ]);
                }



                if (isset($request->gasto) or !is_null($request->gasto)) {
                    for ($r = 0; $r < count($request->id_plan); $r++) {
                        $plancuenta = Plan_Cuentas_Empresa::where("id_plan", $request->id_plan[$r])->orwhere("plan", $request->id_plan[$r])->first();


                        $plancuenta = Plan_Cuentas::find(is_null($plancuenta->id_plan) ? $plancuenta->plan : $plancuenta->id_plan);

                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plancuenta->id,
                            'descripcion'         => $plancuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                            'fecha'               => $request['fecha_compra'],
                            'haber'               => '0',
                            'debe'                => $request['precioneto_gasto'][$r],
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
        if ($request['total1'] != 0) {

            $id_plan_config = LogConfig::busqueda("2.01.03.01.01");
            $plan_c = Plan_Cuentas_Empresa::where('id_plan', $id_plan_config)->first();
            if (is_null($plan_c)) {
                return ['respuesta' => 'error', 'msj' => 'No se ha configurado la cuenta'];
            }
            //$plan_c = Plan_Cuentas::where('id', '1430')->first(); 

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $id_plan_config,
                'descripcion'         => strtoupper($plan_c->nombre), //aqui puse el nombre de la cuenta del acreedor
                'fecha'               => $request['fecha_compra'],
                'debe'                => '0',
                'haber'               => $request['total1'],
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $idusuario,
                'ip_modificacion'     => $idusuario,
            ]);

            if ($request['descuento1'] > 0) {
                $id_plan_confg = Ct_Configuraciones::obtener_cuenta("COMPRAS_DESC_VENTA");
                //dd($id_plan_confg);
                $plancuenta = Plan_Cuentas::find($id_plan_confg->cuenta_guardar);
                //dd($plancuenta);
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plancuenta->id,
                    'descripcion'         => strtoupper($plancuenta->nombre), //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => $request['fecha_compra'],
                    'debe'                => '0',
                    'haber'               => $request['descuento1'],
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }
        }

        $request['asiento_id'] = $id_asiento_cabecera;
        // return $id_asiento_cabecera;
        return ['respuesta' => 'success', 'id_asiento' => $id_asiento_cabecera];
    }

    public function _store_detalles($request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $i          = 0;

        foreach ($request->codigo as $value) {
            $tipo = AfTipo::find($request['tipo'][$i]);


            $input = [
                'codigo'          => $request['codigo'][$i],
                'nombre'          => strtoupper($request['descrip_prod'][$i]),
                'descripcion'     => strtoupper($request['descripcion'][$i]),
                'tipo_id'         => $request['tipo'][$i],
                'subtipo_id'      => $request['grupo'][$i],
                'responsable'     => strtoupper($request['responsable'][$i]),
                'acreedor'        => strtoupper($request['proveedor']),
                'producto'        => strtoupper($request['producto'][$i]),
                'marca'           => strtoupper($request['marca'][$i]),
                'color'           => strtoupper($request['color'][$i]),
                'modelo'          => strtoupper($request['modelo'][$i]),
                'serie'           => strtoupper($request['serie'][$i]),
                'procedencia'     => strtoupper($request['procedencia'][$i]),
                'costo'           => $request['costo'][$i],
                'fecha_compra'    => $request['fecha_compra'],
                'factura'         => $request['serie_factura'] . '-' . $request['secuencia'],
                'estado'          => 1,
                'estado_activo'   => 1,
                'empresa'         => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'tasa'            => $tipo->tasa,
                'tipo_tasa'       => $tipo->tipo_tasa,
                'vida_util'       => $tipo->vidautil,
                'ubicacion'       => strtoupper($request['ubicacion'][$i]),
                'codigo_text'     => $request['codigo'][$i],
                'codigo_num'      => $request['codigo_num'][$i],
            ];

            $activo_id = AfActivo::insertGetId($input);
            $detalle = [
                'id_ct_compras'        => $request['idCompra'],
                'codigo'               => $request['codigo'][$i],
                'nombre'               => strtoupper($request['descrip_prod'][$i]),
                'cantidad'             => $request['cantidad'][$i],
                'precio'               => $request['costo'][$i],
                'descuento_porcentaje' => $request['descpor'][$i],
                'estado'               => '1',
                'descuento'            => $request['desc'][$i],
                'extendido'            => ($request['costo'][$i] * $request['cantidad'][$i]),
                'detalle'              =>  strtoupper($request['descrip_prod'][$i]),
                'iva'                  => "0",
                'porcentaje'           => "0.12",
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_detalle_compra::create($detalle);
            AfFacturaActivoDetalle::create([
                'fact_activo_id'  => $request['factura_id'],
                'activo_id'       => $activo_id,
                'cantidad'        => $request['cantidad'][$i],
                'costo'           => $request['costo'][$i],
                'subtotal'        => ($request['costo'][$i] * $request['cantidad'][$i]),
                'descuento'       => $request['desc'][$i],
                'porc_descuento'  => $request['descpor'][$i],
                'total'           => $request['total'][$i],
                'estado'          => 1,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $i++;
        }
    }

    public function anular($id)
    {
        $cabecera = AfFacturaActivoCabecera::find($id);
        $detalle  = AfFacturaActivoDetalle::where('fact_activo_id', $id)->get();
        $compras = Ct_compras::where('id_asiento_cabecera', $cabecera->id_asiento)->first();

        if ($compras->estado == 0) {
            $cabecera->update(array('estado' => '0'));

            foreach ($detalle as $value) {
                if (!is_null($value->activo_id)) {
                    $activo = AfActivo::find($value->activo_id);
                    $activo->estado = 0;
                    $activo->save();
                }
            }
            return redirect()->intended('/afDocumentoFactura');
        } else {
            if (!is_null($compras)) {
                $compras->estado = 0;
                $compras->save();
            }
            $cabecera->update(array('estado' => '0'));

            foreach ($detalle as $value) {
                if (!is_null($value->activo_id)) {
                    $activo = AfActivo::find($value->activo_id);
                    $activo->estado = 0;
                    $activo->save();
                }
            }
            $this->anular_asiento($cabecera->id_asiento);
            return redirect()->intended('/afDocumentoFactura');
        }
    }

    public function anular_fc($id)
    {
        $cabecera = AfFacturaActivoCabecera::find($id);
        $detalle  = AfFacturaActivoDetalle::where('fact_activo_id', $id)->get();
        $compra = Ct_compras::where('id_asiento_cabecera', $cabecera->id_asiento)->first();
        $cab_asiento = Ct_Asientos_Cabecera::find($cabecera->id_asiento);

        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($cab_asiento->fecha_asiento);
        $verifica_cierre = json_decode($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        $tabla_egre        = "";
        $tabla_banca       = "";
        $tabla_cruce       = "";
        $tabla_rete        = "";
        $tabla_egre_masivo = "";
        $id_egreso         = 0;
        $id_deb_ban        = 0;
        $id_cruce_valores  = 0;
        $id_retencion      = 0;
        $id_egreso_masivo  = 0;
        /*********************COMPRAS*****************************************/
        $id    = $compra->id;
        $tiene = "";
        $tabla = "";

        $retenciones = Ct_Retenciones::where('id_compra', $id)->get();

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

        $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce, $tabla_rete, $tabla_egre_masivo];
        $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores, $id_retencion, $id_egreso_masivo];
        //  dd($tablas);
        return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];
    }

    public function anular_asiento($id)
    {
        $asientoc = Ct_Asientos_Cabecera::where('id', $id)->first();

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        $cabecera   = [
            'sucursal'        => $asientoc->sucursal,
            'punto_emision'   => $asientoc->punto_emision,
            'observacion'     => 'ANULACION / REVERSO DE ACTIVO FIJO POR FACTURA: ' . $asientoc->fact_numero,
            'fecha_asiento'   => $asientoc->fecha_asiento,
            'fact_numero'     => $asientoc->fact_numero,
            'valor'           => $asientoc->valor,
            'estado'          => '1',
            'id_empresa'      => $asientoc->id_empresa,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);

        $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id)->orderBy('id', 'asc')->get();
        if ($detalle != null) {
            foreach ($detalle as $value) {

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'descripcion'         => $value->descripcion, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => $value->fecha,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }
            LogAsiento::anulacion("AF", $id_asiento_cabecera, $asientoc->id);
        }
    }

    public function show($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $proveedores = Proveedor::where('estado', '1')->get();
        $divisas     = Ct_Divisas::where('estado', '1')->get();

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)
            ->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();


        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        $c_tributario   = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        $tipos_comp = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', 1)->get();
        $productos    = Producto::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();

        $cabecera = AfFacturaActivoCabecera::where('id', $id)->first();
        $detalles = AfFacturaActivoDetalle::where('fact_activo_id', $id)->get();

        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $empleados    = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();


        return view('activosfijos/documentos/factura/show', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'empresa' => $empresa, 'empre' => $empre, 'productos'  => $productos, 'iva' => $iva, 'cuentas'  => $cuentas, 'proveedores' => $proveedores, 'tipos_comp' => $tipos_comp, 'tipos'   => $tipos, 'grupos' => $grupos, 'responsables' => $responsables, 'productos' => $productos, 'marcas'    => $marcas, 'cabecera' => $cabecera, 'detalles'  => $detalles, 'c_tributario' => $c_tributario, 'sub_tipos' => $sub_tipos, 'empleados' => $empleados, 'af_series' => $af_series, 'af_colores' => $af_colores, 'af_responsables' => $af_responsables]);
    }

    public function guardar_color(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_color = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['mdcolor' . $id] . '%')->where('tipo', '1')->first();

        if (is_null($af_color)) {
            $arr_color = [
                'nombre'            => strtoupper($request['mdcolor' . $id]),
                'tipo'              => 1,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_color);
        }

        return "ok";
    }

    public function guardar_serie(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_serie = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['mdserie' . $id] . '%')->where('tipo', '2')->first();

        if (is_null($af_serie)) {
            $arr_serie = [
                'nombre'            => $request['mdserie' . $id],
                'tipo'              => 2,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_serie);
        }

        return "ok";
    }


    public function guardar_marca(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $af_marca = Marca::where('nombre', 'like', '%' . $request['mdmarca' . $id] . '%')->where('estado', '1')->first();
        if (is_null($af_marca)) {
            $arr_marca = [
                'nombre'            => strtoupper($request['mdmarca' . $id]),
                'descripcion'       => strtoupper($request['mdmarca' . $id]),
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
            Marca::create($arr_marca);
        }
        return "ok";
    }
    public function buscar_proveedor(Request $request)
    {
        $id_proveedor = $request['id_proveedor'];
        $detalle_acreedor   = DB::table('ct_detalle_acreedores')->where('id_proveedor', $id_proveedor)->orderBy('f_caducidad', 'desc')->first();
        if (!is_null($detalle_acreedor)) {
            $serie         = $detalle_acreedor->serie;
            $autorizacion  = $detalle_acreedor->autorizacion;
            $f_caduca      = $detalle_acreedor->f_caducidad;

            return ['serie' => $serie, 'autorizacion' => $autorizacion, 'f_caduca' => $f_caduca];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }


    public function guardar_responsable(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_responsable = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['mdresponsable' . $id] . '%')->where('tipo', '3')->first();

        if (is_null($af_responsable)) {
            $arr_resp = [
                'nombre'            => strtoupper($request['mdresponsable' . $id]),
                'tipo'              => 3,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_resp);
        }

        return "ok";
    }

    public function new_factura(Request $request)
    {
        $porIva = Utilidades::porcentajeIva();
        if ($porIva == '') {
            $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
            return view('errors/204', $data);
        }
        $proveedor       = proveedor::where('estado', '1')->get();
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)
            ->where('estado', 1)->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();
        $empre = Empresa::all();
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        $tipos_comp = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();
        $c_tributario   = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $id_plan_config = LogConfig::busqueda("4.1.01.02");
        $iva = Ct_Configuraciones::find(3);
        $grupos = AfGrupo::where('estado', '!=', 0)->get();
        $tipos = AfTipo::where('estado', '!=', 0)->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', 1)->orwhere('id_tipo_usuario', 20)->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $marcas = Marca::where('estado', '!=', 0)->get();
        $sub_tipos = AfSubTipo::where('estado', '1')->get();
        $empleados = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $term = Ct_Termino::where('estado', '1')->get();
        $plan_cuentas = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('estado', '2')->get();
        $ordenes = Log_Ordenes::where('log_ordenes.aprobado', '1')->where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '1')
            ->join('af_factura_activo_cab as afc', 'afc.id', 'log_ordenes.id_orden')
            ->leftJoin('proveedor as prov', 'prov.id', 'afc.proveedor')
            ->where('afc.id_empresa', $id_empresa)
            ->select('afc.*', 'prov.razonsocial')->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('activosfijos/documentos/factura/new_factura', ['divisas' => $divisas, 'sucursales'    => $sucursales, 'punto'    => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, 'proveedor' => $proveedor, 'tipos_comp' => $tipos_comp, 'tipos'  => $tipos, 'grupos' => $grupos, 'responsables'     => $responsables, 'productos'  => $productos, 'marcas' => $marcas, 'c_tributario' => $c_tributario, 'sub_tipos' => $sub_tipos, 'empleados' => $empleados, 'af_series' => $af_series, 'af_colores' => $af_colores, 'term' => $term, 'af_responsables' => $af_responsables, 'plan_cuentas' => $plan_cuentas, 'ordenes' => $ordenes, 'porIva' => $porIva, 'min_date' => $min_date]);
    }

    public function modal_activo()
    {

        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();

        return view('activosfijos/documentos/factura/modal_activo', ['af_colores' => $af_colores, 'af_series' => $af_series, 'af_responsables' => $af_responsables, 'grupos' => $grupos, 'tipos' => $tipos, 'marcas' => $marcas, 'sub_tipos' => $sub_tipos]);
    }
    public function edit_new_factura(Request $request, $id)
    {

        $proveedores       = proveedor::where('estado', '1')->get();

        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();

        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();

        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)
            ->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        $tipos_comp = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();
        $c_tributario   = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', 1)->orwhere('id_tipo_usuario', 20)->get();
        $productos    = Ct_productos::where('id_empresa', $id_empresa)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();

        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $empleados    = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $term         = Ct_Termino::where('estado', '1')->get();
        $cabecera = AfFacturaActivoCabecera::find($id); //f
        $detalles = AfFacturaActivoDetalle::where('fact_activo_id', $id)->get();
        $fc_compra = Ct_compras::where('id_asiento_cabecera', $cabecera->id_asiento)->first();


        return view('activosfijos/documentos/factura/edit_new_factura', ['divisas' => $divisas, 'sucursales'    => $sucursales, 'punto'    => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, 'proveedores' => $proveedores, 'tipos_comp' => $tipos_comp, 'tipos'  => $tipos, 'grupos' => $grupos, 'responsables' => $responsables, 'productos'  => $productos, 'marcas' => $marcas, 'c_tributario' => $c_tributario, 'sub_tipos' => $sub_tipos, 'empleados' => $empleados, 'af_series' => $af_series, 'af_colores' => $af_colores, 'term' => $term, 'af_responsables' => $af_responsables, 'cabecera' => $cabecera, 'detalles'  => $detalles, 'fc_compra' => $fc_compra]);
    }

    public function search_acive(Request $request)
    {
        $query = AfActivo::find($request['id']);
        $detalles = AfActivo_Accesorios::where('id_activo', $request['id'])->get();
        $subtipo = $query->sub_tipo->nombre;
        $tipo = $query->tipo->nombre;
        return ['res' => $query, 'subtipo' => $subtipo, 'tipo' => $tipo, 'detalles' => $detalles];
    }

    public function subir_archivo(Request $request, $id)
    {

        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::find($id_empresa);
        $imp_archivos = Af_Activo_Archivos::where('id_factura', $id)->where('estado', '1')->get();
        return view('activosfijos/documentos/factura/subir_archivoaf', ['imp_archivos' => $imp_archivos, 'empresa' => $empresa, 'id' => $id]);
    }

    public function guardar_archivo(Request $request)
    {

        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_factura = $request['id_fac_af'];
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();

            $fileName = 'AF' . $id_factura . '_' . date('YmdHis') . '.' . $extension;
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            $factura = AfFacturaActivoCabecera::find($id_factura);
            $tipo = 0;
            if ($factura->estado == 1) {
                $tipo = 1; //factura
            } else if ($factura->estado == 3) {
                $tipo = 2; //orden
            } else {
                $tipo = 0; //indefinido
            }

            $input_archivo = [
                'id_factura'      => $id_factura,
                'nombre_archivo'  => $fileName,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'tipo'            => $tipo,
            ];

            $id_archivo = Af_Activo_Archivos::insertGetId($input_archivo);

            $i = $i + 1;
        }
    }

    public function archivo_descarga($name)
    {
        $imagen          = Af_Activo_Archivos::find($name);
        //$paciente        = paciente::find($imagen->id_paciente);
        $nombre_archivo = null;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre_archivo;
        $nombre_archivo = $nombre_archivo . '_' . $imagen->nombre_archivo;

        if ($nombre_archivo == null) {
            $nombre_archivo = $imagen->nombre_archivo;
        } else {
            $nombre_temporal = $imagen->nombre_archivo;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_archivo . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre_archivo;
                }
            } else {
                $nombre_archivo = $imagen->nombre_archivo;
            }
        }
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function eliminar_archivo($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $imagen = Af_Activo_Archivos::find($id);

        $arr = [
            'estado' => 0,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $imagen->update($arr);
    }

    public function ver_anteprima(Request $request)
    {
        $nombreArchivo = Af_Activo_Archivos::where('id', $request['id_imagen'])->first();
        return view('activosfijos/documentos/factura/modal_anteprima', ['id_imagen' => $nombreArchivo]);
    }

    public function buscar_categoria(Request $request)
    {
        $tipo = $request->opcion;
        if (!is_null($tipo)) {
            $categorias = AfSubTipo::where('tipo_id', $tipo)->get();
            return $categorias;
        }

        return "no";
    }


    public function masivo_observacion()
    {
        $id_empresa = Session::get('id_empresa');
        $fc_activo = AfFacturaActivoCabecera::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        foreach ($fc_activo as $value) {
            $asiento_cab = Ct_Asientos_Cabecera::find($value->id_asiento);
            $fc_compra = Ct_compras::where('id_asiento_cabecera', $asiento_cab->id)->first();
            $arr_fc = [
                'observacion' => $asiento_cab->observacion,
                'ip_modificacion'       => "masivo obs"
            ];

            $value->update($arr_fc);
            $fc_compra->update($arr_fc);
        }


        return "gracias amigo";
    }

    public function arreglar_precios()
    {
        $id_empresa = Session::get('id_empresa');
        $factura = AfFacturaActivoCabecera::where('id_empresa', $id_empresa)->where('estado', '1')->get();
        foreach ($factura as $fac) {
            $detalle = $fac->detalles;
            foreach ($detalle as $det) {

                $activo = AfActivo::find($det->activo_id);
                dd($activo);
                if ($det->costo != $activo->costo) {
                    return $activo;
                }
            }
        }
    }

    public function pre_orden()
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $proveedor       = proveedor::where('estado', '1')->get();
        $id_plan_config = LogConfig::busqueda("4.1.01.02");
        $iva       = Ct_Configuraciones::where('id_plan', $id_plan_config)->where('estado', '1')->first();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();

        $plan_cuentas = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('estado', '2')->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('activosfijos/documentos/orden/create', ['proveedor' => $proveedor, 'iva' => $iva, 'empresa'    => $empresa, 'tipos' => $tipos, 'af_responsables' => $af_responsables, 'af_colores' => $af_colores, 'af_series' => $af_series, 'sub_tipos' => $sub_tipos, 'marcas' => $marcas, 'plan_cuentas' => $plan_cuentas, 'divisas' => $divisas, 'min_date' => $min_date]);
    }

    public function guardar_orden(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        DB::beginTransaction();
        try {
            //code...

            if ($request->estado == "actualizar") {
                $log_orden = Log_Ordenes::where('id_orden', $request->id_orden)->where('tipo', 1)->where('id_empresa', $id_empresa)->first();
                if (!is_null($log_orden)) {
                    $up_log = [
                        'estado'         => 0,
                        'id_usuariomod'  => $idusuario,
                        'ip_modificacion'  => $ip_cliente,
                    ];

                    $log_orden->update($up_log);

                    $fc_af = AfFacturaActivoCabecera::find($request->id_orden);

                    if (!is_null($fc_af)) {
                        $up_cab = [
                            'estado'         => 0,
                            'id_usuariomod'  => $idusuario,
                            'ip_modificacion'  => $ip_cliente,
                        ];

                        $fc_af->update($up_cab);

                        $fc_detalle = $fc_af->detalles;

                        foreach ($fc_detalle as $detalle) {
                            $activo = AfActivo::find($detalle->activo_id);

                            if (!is_null($activo)) {
                                $up_activo = [
                                    'estado'         => 0,
                                    'id_usuariomod'  => $idusuario,
                                    'ip_modificacion'  => $ip_cliente,
                                ];

                                $activo->update($up_activo);
                            }
                        }
                    }
                }
            }

            $base = $request['subtotal_01'];
            $base12 = $request['subtotal_121'];

            $input               = [

                'proveedor'        => $request['proveedor'],
                'tipo'             => $request['tipo_transaccion'],
                'fecha_asiento'    => $request['fecha_compra'],
                'fecha_caduca'     => $request['fecha_compra'],
                'divisas'          => 1,
                'fecha_compra'     => $request['fecha_compra'],
                'serie'            => $request['serie_factura'],
                'secuencia'        => $request['secuencia'],
                'subtotal'         => $request['base1'],
                'subtotal0'        => $base,
                'subtotal12'       => $base12,
                'descuento'        => $request['descuento1'],
                'impuesto'         => $request['tarifa_iva1'],
                'total'            => $request['total1'],
                'estado'           => 3,
                'id_empresa'       => $id_empresa,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'observacion'      => $request['concepto'],
            ];


            $request['factura_id'] = AfFacturaActivoCabecera::insertGetId($input);

            $arr_log = [
                'id_orden'       => $request['factura_id'],
                'tipo'           => 1,
                'id_empresa'     => $id_empresa,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ];

            Log_Ordenes::create($arr_log);


            $retornar = $this->store_AfActivo($request);
            if ($retornar["respuesta"] == "error") {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => "Error al guardar...", "exp" => $retornar['msj'], "mod" => $retornar["mod"]];
            }

            if ($retornar["respuesta"] == "success") {
                DB::commit();
                return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => "{$retornar['msj']}", "mod" => $retornar["mod"]];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function index_ordenes()
    {

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        $log_af = Log_Ordenes::where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '1')
            ->join('af_factura_activo_cab as afc', 'afc.id', 'log_ordenes.id_orden')
            ->join('af_factura_activo_det as afd', 'afd.fact_activo_id', 'afc.id')
            ->leftJoin('proveedor as prov', 'prov.id', 'afc.proveedor')
            ->leftJoin('users as us', 'us.id', 'afc.id_usuariocrea')
            ->where('afc.id_empresa', $id_empresa)
            ->select('log_ordenes.id as id_log', 'log_ordenes.tipo as tipo_log', 'log_ordenes.estado as estado_log', 'afc.fecha_compra as fecha_compra', 'prov.razonsocial as nombre_proveedor', DB::raw('CONCAT(us.apellido1," ", us.nombre1) as nombre_usuario'), DB::raw('CONCAT(afc.serie,"-", afc.secuencia) as secuencia_orden'));

        $log_imp = Log_Ordenes::where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '2')
            ->join('af_factura_activo_cab as icab', 'icab.id', 'log_ordenes.id_orden')
            ->join('ct_importaciones_det as idet', 'idet.id_cab', 'icab.id')
            ->leftJoin('proveedor as prov', 'prov.id', 'icab.id_proveedor')
            ->leftJoin('users as us', 'us.id', 'icab.id_usuariocrea')
            ->where('icab.id_empresa', $id_empresa)
            ->select('log_ordenes.id as id_log', 'log_ordenes.tipo as tipo_log', 'log_ordenes.estado as estado_log', 'icab.fecha as fecha_compra', 'prov.razonsocial as nombre_proveedor', DB::raw('CONCAT(us.apellido1," ", us.nombre1) as nombre_usuario'), 'icab.secuencia_importacion as secuencia_orden');

        $ordenes = $log_af->union($log_imp)->get();

        return view('activosfijos/documentos/orden/index', ['ordenes' => $ordenes]);
    }

    public function buscar_orden(Request $request)
    {
        $id_empresa = Session::get('id_empresa');

        $id_orden = $request['orden'];
        $orden_cab = AfFacturaActivoCabecera::find($id_orden);

        $cab = [
            'id_proveedor'              => $orden_cab->proveedor,
            'nombre_proveedor'          => isset($orden_cab->datosproveedor) ? $orden_cab->datosproveedor->nombrecomercial : '',
            'serie'                     => $orden_cab->serie,
            'secuencia'                 => $orden_cab->secuencia,
            'observacion'               => $orden_cab->observacion,
            'fecha_compra'              => $orden_cab->fecha_compra,
            'divisas'                   => $orden_cab->divisas,
            'subtotal0'                 => $orden_cab->subtotal0,
            'subtotal12'                => $orden_cab->subtotal12,
            'subtotal'                  => $orden_cab->subtotal,
            'descuento'                 => $orden_cab->descuento,
            'impuesto'                  => $orden_cab->impuesto,
            'total'                     => $orden_cab->total,
        ];

        $cab["detalles"] = [];
        foreach ($orden_cab->detalles as $value) {
            $details = [
                "codigo"                => $value->codigo,
                "nombre"                => $value->nombre,
                "cantidad"              => $value->cantidad,
                "precio"                => $value->costo,
                "porct_descuento"       => $value->porc_descuento,
                "descuento"             => $value->descuento,
                "precio_neto"           => $value->subtotal,
                "iva"                   => $value->iva,
                "val_iva"               => $value->valor_iva,
                "total_valor"           => $value->total,
            ];


            if (!is_null($value->activo_id)) {
                $af_activo = AfActivo::find($value->activo_id);
                $details["activo_fijo"] = $af_activo['attributes'];
                $details["activo_fijo"]["accesorios"] = [];

                $accesorios = AfActivo_Accesorios::where('id_activo', $af_activo->id)->get();
                if (count($accesorios) > 0) {
                    foreach ($accesorios as $acce) {
                        //    dd($acce);
                        $data_acce = [
                            "nombre"        => $acce->nombre,
                            "marca"         => $acce->marca,
                            "modelo"        => $acce->modelo,
                            "serie"         => $acce->serie,
                        ];
                        array_push($details["activo_fijo"]["accesorios"], $data_acce);
                    }
                }
            }
            array_push($cab["detalles"], $details);
        }

        return ["cab" => $cab];
    }

    public function guardar_factura(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_orden = $request['orden'];

        DB::beginTransaction();
        try {
            $log_ordenes = Log_Ordenes::find($id_orden);
            $arr_log = [
                'estado'               => 0,
                'id_usuariomod'        => $idusuario,
                'ip_modificacion'      => $ip_cliente,
            ];
            $log_ordenes->update($arr_log);

            $numero                   = AfFacturaActivoCabecera::max('id');
            $numero                   = $numero + 1;
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;
            $orden_cab = AfFacturaActivoCabecera::find($id_orden);

            $arr_cab = [
                'numero'           => str_pad($numero, 10, "0", STR_PAD_LEFT),
                'tipo'             => $request['tipo_transaccion'],
            ];

            // $input               = [

            //     'proveedor'        => $request['proveedor'],
            //     'tipo'             => $request['tipo_transaccion'],
            //     'id_asiento'       => $id_asiento_cabecera,
            //     'fecha_asiento'    => $request['fecha_asiento'],
            //     'credito_tributario' => $request['credito_tributario'],
            //     'fecha_caduca'     => $request['fecha_caduca'],
            //     'divisas'          => $request['divisas'],
            //     'termino'          => $request['termino'],
            //     'ord_compra'       => $request['ord_compra'],
            //     'nro_autorizacion' => $request['nro_autorizacion'],
            //     'fecha_compra'     => $request['fecha_compra'],
            //     'serie'            => $request['serie_factura'],
            //     'secuencia'        => $request['secuencia'],
            //     'tipo_comprobante' => $request['tipo_comprobante'],
            //     'subtotal'         => $request['base1'],
            //     'subtotal0'        => $request['subtotal_01'],
            //     'subtotal12'       => $request['subtotal_121'],
            //     'descuento'        => $request['descuento1'],
            //     'impuesto'         => $request['tarifa_iva1'],
            //     'total'            => $request['total1'],
            //     'estado'           => 1,
            //     'id_empresa'       => $id_empresa,
            //     'id_usuariocrea'   => $idusuario,
            //     'id_usuariomod'    => $idusuario,
            //     'ip_creacion'      => $ip_cliente,
            //     'ip_modificacion'  => $ip_cliente,
            //     'sucursal'         => $c_sucursal,
            //     'punto_emision'    => $c_caja,
            //     'observacion'      => $request['concepto'],
            // ];

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function index_preorden(Request $request)
    {

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);


        $id_orden = "";
        if (isset($request['id'])) {
            $id_orden = $request['id'];
        }

        $id_proveedor = "";
        if (isset($request['id_proveedor'])) {
            $id_proveedor = $request['id_proveedor'];
        }

        $concepto = "";
        if (isset($request['observacion'])) {
            $concepto = $request['observacion'];
        }

        $ordenes = array();
        $ordenes = Log_Ordenes::where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '1')
            ->join('af_factura_activo_cab as afc', 'afc.id', 'log_ordenes.id_orden')
            ->leftJoin('proveedor as prov', 'prov.id', 'afc.proveedor')
            ->where('afc.id_empresa', $id_empresa)
            ->select('afc.*', 'log_ordenes.aprobado', 'prov.nombrecomercial');

        if (!is_null($request['id']) or $request['id'] != "") {
            $ordenes = $ordenes->where('afc.id', $request['id']);
        }

        if (!is_null($request['proveedor']) or $request['proveedor'] != "") {
            $ordenes = $ordenes->where('prov.nombrecomercial', 'like', '%' . $request['proveedor'] . '%');
        }

        if (!is_null($request['concepto']) or $request['concepto'] != "") {
            $ordenes = $ordenes->where('observacion', 'like', '%' . $request['concepto'] . '%');
        }

        $ordenes = $ordenes->get();

        return view('activosfijos/documentos/factura/index_orden', ['ordenes' => $ordenes, 'empresa' => $empresa, 'id_orden' => $id_orden, 'id_proveedor' => $id_proveedor, 'concepto' => $concepto]);
    }



    public function edit_preorden($id)
    {
        $id_empresa = Session::get('id_empresa');
        $orden = AfFacturaActivoCabecera::find($id);
        $empresa = Empresa::find($id_empresa);
        $proveedor       = proveedor::where('estado', '1')->get();
        $id_plan_config = LogConfig::busqueda("4.1.01.02");
        $iva       = Ct_Configuraciones::where('id_plan', $id_plan_config)->where('estado', '1')->first();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();

        $plan_cuentas = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('estado', '2')->get();

        return view('activosfijos/documentos/factura/edit_orden', ['proveedor' => $proveedor, 'iva' => $iva, 'empresa'    => $empresa, 'tipos' => $tipos, 'af_responsables' => $af_responsables, 'af_colores' => $af_colores, 'af_series' => $af_series, 'sub_tipos' => $sub_tipos, 'marcas' => $marcas, 'plan_cuentas' => $plan_cuentas, 'divisas' => $divisas, 'orden' => $orden]);
    }

    public function ver_archivo_imp($id_archivo)
    {
        $nombreArchivo = Af_Activo_Archivos::find($id_archivo);
        if (!is_null($nombreArchivo)) {
            $pathtoFile = storage_path() . '/app/hc_ima/' . $nombreArchivo->nombre_archivo;
            //dd($pathtoFile);
            if (file_exists($pathtoFile)) {
                return response()->file($pathtoFile);
            }
        }
        return "no cargado";
    }

    public function eliminar_orden($id)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = Session::get('id_empresa');
        DB::beginTransaction();
        try {
            //code...

            $log_orden = Log_Ordenes::where('id_orden', $id)->where('tipo', 1)->where('id_empresa', $id_empresa)->first();
            if (!is_null($log_orden)) {
                $up_log = [
                    'estado'         => 0,
                    'id_usuariomod'  => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $log_orden->update($up_log);

                $fc_af = AfFacturaActivoCabecera::find($id);

                if (!is_null($fc_af)) {
                    $up_cab = [
                        'estado'         => 0,
                        'id_usuariomod'  => $idusuario,
                        'ip_modificacion'  => $ip_cliente,
                    ];

                    $fc_af->update($up_cab);

                    $fc_detalle = $fc_af->detalles;

                    foreach ($fc_detalle as $detalle) {
                        $activo = AfActivo::find($detalle->activo_id);

                        if (!is_null($activo)) {
                            $up_activo = [
                                'estado'         => 0,
                                'id_usuariomod'  => $idusuario,
                                'ip_modificacion'  => $ip_cliente,
                            ];

                            $activo->update($up_activo);
                        }
                    }
                }
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Eliminado'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function aprobar_orden($id)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = Session::get('id_empresa');
        DB::beginTransaction();
        try {
            $log_orden = Log_Ordenes::where('id_orden', $id)->where('tipo', 1)->where('id_empresa', $id_empresa)->first();
            if (!is_null($log_orden)) {
                $arr_log = [
                    'aprobado'          => 1,
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $log_orden->update($arr_log);
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }
    public function index_fotos($id)
    {
        $factura = AfFacturaActivoCabecera::find($id);
        return  view('activosfijos/documentos/factura/index_fotos', ['factura' => $factura]);
    }

    public function guardar_fotos(Request $request)
    {
        //dd($request->all());
        $fileName = "";
        $id_activo = $request["id_activo"];
        $activo = AfActivo::find($id_activo);

        if (!is_null($request['fotoaf'])) {
            $filemd      = $request->file('fotoaf');
            $extension     = $filemd->getClientOriginalExtension();
            $fileName = 'Af' . '_' . $activo->codigo_num . '_' . date('YmdHis') . '.' . $extension;
            Storage::disk('archivos_activofijo')->put($fileName, \File::get($filemd));

            $activo->foto_activo = $fileName;
            $activo->id_usuariomod = Auth::user()->id;
            $activo->ip_modificacion = $_SERVER["REMOTE_ADDR"];
            $activo->save();
        }
        $nameacc = "";
        if (!is_null($request->fotoacc)) {
            $fileacc      = $request->file('fotoacc');
            $extensionacc     = $fileacc->getClientOriginalExtension();
            $nameacc = 'Af-Acc' . '_' . $activo->codigo_num . '_' . date('YmdHis') . '.' . $extensionacc;
            Storage::disk('archivos_activofijo')->put($nameacc, \File::get($fileacc));

            $activo->foto_acc = $nameacc;
            $activo->id_usuariomod = Auth::user()->id;
            $activo->ip_modificacion = $_SERVER["REMOTE_ADDR"];
            $activo->save();
        }

        return redirect()->back();
    }

    public function modal_archivo($id)
    {
        $activo = AfActivo::find($id);

        return view('activosfijos/documentos/factura/modal_archivo', ['activo' => $activo]);
    }

    public function load($name)
    {
        //dd($name);
        $path = storage_path() . '/app/archivos_activofijo/' . $name;
        if (file_exists($path)) {
            ob_end_clean();
            return Response::download($path);
        }
    }
}
