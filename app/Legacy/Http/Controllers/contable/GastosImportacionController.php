<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Importaciones_Compras;
use Sis_medico\Ct_Importaciones_Detalle_Compra;
use Sis_medico\Ct_Importaciones_Gasto_Cab;
use Sis_medico\Ct_Imp_Gastos;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Termino;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Importaciones_Cab;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Importaciones_Archivos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Http\Controllers\contable\ImportacionesController;
use Sis_medico\LogAsiento;
use Sis_medico\Utilidades;

class GastosImportacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $gastos = Ct_Imp_Gastos::where('estado', '1')->paginate(15);

        return view('contable/importaciones/mantenimiento_gastos/index', ['gastos' => $gastos, 'empresa' => $empresa]);
    }

    public function create(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $plan       = Plan_Cuentas::all();

        return view('contable/importaciones/mantenimiento_gastos/create', ['empresa' => $empresa, 'plan' => $plan]);
    }

    public function edit(Request $request, $id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $plan       = Plan_Cuentas::all();
        $gasto      = Ct_Imp_Gastos::find($id);

        return view('contable/importaciones/mantenimiento_gastos/edit', ['empresa' => $empresa, 'plan' => $plan, 'gasto' => $gasto, 'id' => $id]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $ct_datos_productos = Ct_productos::where('codigo', $request['codigo'])->where("id_empresa", $id_empresa)->where("estado_tabla", 1)->first();

        if (!is_null($ct_datos_productos)) {
            return ['respuesta' => 'no', 'msj' => 'Código repetido'];
        }
        DB::beginTransaction();
        try {
            // if (is_null($ct_datos_productos)) {

            $datos_productos = [

                'codigo'                    => $request->codigo,
                'nombre'                    => $request->nombre,
                'id_empresa'                => $request['id_empresa'],
                'grupo'                     => '3',
                'marca'                     => '',
                'estado_tabla'              => '1',
                'modelo'                    => '1',
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ];

            // dd($datos_nomina_inactivos);

            Ct_productos::insert($datos_productos);
            // }
            Ct_Imp_Gastos::insert([
                'nombre'          => $request->nombre,
                'codigo'          => $request->codigo,
                'id_empresa'      => $id_empresa,
                //'id_plan_cuenta'  => $request->id_plan_cuenta,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            DB::commit();

            return ['respuesta' => 'si', 'msj' => 'Guardado Exitosamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'no', 'msj' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $gasto = Ct_Imp_Gastos::find($id);

        $verificar_produto = Ct_productos::where("codigo", $request->codigo)->where("id_empresa", Session::get('id_empresa'))->first();


        DB::beginTransaction();
        try {


            $arr_gastos = [
                'nombre'          => $request->nombre,
                'codigo'          => $request->codigo,
                'id_empresa'      => $id_empresa,
                // 'id_plan_cuenta'  => $request->id_plan_cuenta,
                'id_usuariomod'   => $id_usuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $gasto->update($arr_gastos);

            $data_producto = [
                'codigo'                    => $request->codigo,
                'nombre'                    => $request->nombre,
                'id_empresa'                => Session::get('id_empresa'),
                'grupo'                     => '3',
                'marca'                     => '',
                'estado_tabla'              => '1',
                'modelo'                    => '1',
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            if (is_null($verificar_produto)) {
                Ct_productos::insertGetId($data_producto);
            } else {
                $verificar_produto->update($data_producto);
            }

            DB::commit();
            return ['respuesta' => 'si', 'msj' => 'Guardado Exitosamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'no', 'msj' => $e->getMessage()];
        }
    }

    public function eliminar($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $gasto      = Ct_Imp_Gastos::find($id);

        $arr = [
            'estado'          => '0',
            'id_usuariomod'   => $id_usuario,
            'ip_modificacion' => $ip_cliente,
        ];

        $gasto->update($arr);

        return redirect()->route('gastosimportacion.index');
    }

    public function ingreso_factura(Request $request, $id)
    {
        $porIva = Utilidades::porcentajeIva();
        if ($porIva == '') {
            $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
            return view('errors/204', $data);
        }
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::find($id_empresa);
        $proveedor     = Proveedor::where('estado', 1)->get();
        $bodega        = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $productos     = Ct_productos::where('id_empresa', $id_empresa)->get();
        $tipos_comp    = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();
        $gastos        = Ct_Imp_Gastos::where('estado', '1')->get();
        $termino       = Ct_Termino::where('estado', '1')->get();
        $c_tributario  = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        //dd(ct_master_tipos::all());
        $cab = Ct_Importaciones_Cab::find($id);
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

        $iva       = Ct_Configuraciones::find(3);
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable/importaciones/ingreso_factura', ['cab' => $cab, 'empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'tipos_comp' => $tipos_comp, 'gastos' => $gastos, 'termino' => $termino, 'c_tributario' => $c_tributario, 't_comprobante' => $t_comprobante, 'id' => $id, 'sucursales' => $sucursales, 'punto' => $punto, 'iva' => $iva, 'porIva' => $porIva, 'min_date' => $min_date]);
    }

    public function store_factura(Request $request)
    {

        $id_usuario = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::find($id_empresa);
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_usuario     = Auth::user()->id;
        $objeto_validar = new Validate_Decimals();

        $id_estado = $request['id_estado'];

        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->f_autorizacion);
        $verifica_cierre = json_decode($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        DB::beginTransaction();
        try {
            $errores        = "";
            /* $sucursal       = $request['sucursal'];
            $punto_emision  = $request['serie'];
            $sucursal       = substr($punto_emision, 0, -4);
            $punto_emision  = substr($punto_emision, 4);*/
            $fechahoy       = $request['fecha'];
            $nueva_fecha    = null;
            $modfecha       = null;
            $consulta_fecha = Ct_Termino::where('id', $request['termino'])->first();
            if ($consulta_fecha != null) {
                $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($fechahoy));
                $modfecha    = date("Y-m-d", $nueva_fecha);
            } else {
                $errores .= " la fecha del termino no funciona ";
            }

            $subtotalf = $request['base1'];

            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;

            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;

            $sucursal = $c_sucursal;
            $punto_emision = $c_caja;

            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)->get()->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_importaciones_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                $max_id = intval($max_id->secuencia_f);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }

            //$numeroconcadenado = $request['serie'] . '-' . $request['secuencia_factura'];
            $numeroconcadenado = "{$request['serie']}-{$request['secuencia_factura']}";
            // $request['serie'] = "{$c_sucursal}-{$c_caja}";

            $total_final = $objeto_validar->set_round($request['total1']);

            $cab_asiento = [
                'sucursal'        => $c_sucursal,
                'punto_emision'   => $c_caja,
                'observacion'     => $request['observacion'],
                'fecha_asiento'   => $request['fecha'],
                'fact_numero'     => $request['secuencia_factura'],
                'valor'           => $total_final,
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'aparece_sri'     => $request['archivosri'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cab_asiento);

            $arr_cabecera = [
                'archivo_sri'         => $request->archivosri,
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'f_autorizacion'      => $request->f_autorizacion,
                'fecha'               => $request->fecha,
                'proveedor'           => $request->proveedor,
                'autorizacion'        => $request->autorizacion,
                'serie'               => $request['serie'],
                'secuencia_factura'   => $request->secuencia_factura,
                'tipo'                => 1, //factura gasto
                'estado'              => 1,
                'tipo_comprobante'    => $request->tipo_comprobante,
                'termino'             => $request->termino,
                'f_caducidad'         => $request->f_caducidad,
                'proveedor'           => $request->proveedor,
                'direccion_proveedor' => $request->direccion_proveedor,
                'credito_tributario'  => $request->credito_tributario,
                'tipo_comprobante'    => $request->tipo_comprobante,
                'observacion'         => $request->observacion,
                'subtotal_0'          => $request->subtotal_01,
                'subtotal_12'         => $request->subtotal_121,
                'subtotal'            => $subtotalf,
                'descuento'           => $request->descuento1,
                'iva_total'           => $request->tarifa_iva1,
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'fecha_termino'       => $modfecha,
                'numero'              => $numeroconcadenado,
                'secuencia_f'         => $numero_factura,
                'valor_contable'      => $total_final,
                'total_final'         => $total_final,
                'id_empresa'          => $id_empresa,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            //dd($request->all());

            $id_cabecera = Ct_Importaciones_Compras::insertGetId($arr_cabecera);

            $cab_compras = Ct_Compras::insertGetId($arr_cabecera);



            for ($i = 0; $i < count($request->producto); $i++) {

                $gasto = Ct_Imp_Gastos::find($request->producto[$i]);

                $arr_det = [
                    'id_ct_compras'        => $id_cabecera,
                    'id_gasto'             => $request->producto[$i],
                    'detalle'              => $request->descrip_prod[$i],
                    'cantidad'             => $request->cantidad[$i],
                    'estado'               => 1,
                    'codigo'               => $gasto->codigo,
                    'nombre'               => $gasto->nombre,
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'iva'                  => $request->check_iva[$i],
                    'total'                => $request->precioneto[$i],
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];

                Ct_Importaciones_Detalle_Compra::insertGetId($arr_det);

                $arr_detcompras = [
                    'id_ct_compras'        => $cab_compras,
                    'detalle'              => $request->descrip_prod[$i], //faltan codigo y nombre de productos 
                    'codigo'               => $gasto->codigo,
                    'nombre'               => $gasto->nombre,
                    'cantidad'             => $request->cantidad[$i],
                    'estado'               => 1,
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'extendido'            => ($request->precio[$i] * $request->cantidad[$i]),
                    'iva'                  => $request->check_iva[$i],
                    'total'                => $request->precioneto[$i],
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];

                Ct_detalle_compra::insertGetid($arr_detcompras);
            }

            Ct_Importaciones_Gasto_Cab::create([
                'id_import_cabl'          => $request->id_cab_imp,
                'id_import_compra'        => $id_cabecera,
                'id_ct_compra'            => $cab_compras,
                'tipo'                    => 1, //factura
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
            //f15
            if ($request->id_estado != null) {
                $act_estado = Ct_Importaciones_Gasto_Cab::find($id_estado);
                $act_estado->tipo = 2;
                $act_estado->save();
            }

            ImportacionesController::recalcularImportaciones($request->id_cab_imp);

            $asientos     = GastosImportacionController::asiento_factura($request, $id_asiento_cabecera, $id_usuario, $ip_cliente, $fechahoy, $subtotalf, $request->total1, $request->base1);

            if ($asientos["respuesta" == "error"]) {
                DB::rollBack();
                return $asientos;
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_asiento' => $id_asiento_cabecera];
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error', "e" => $e->getLine()];
        }
    }

    public static function asiento_factura($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $total1, $base1)
    {
        try {
            $id_empresa    = $request->session()->get('id_empresa');
            $globales_iva = Ct_Globales::where('id_modulo', 14)->where('id_empresa', $id_empresa)->first();
            $global_gastos = Ct_Globales::where('id_modulo', 15)->where('id_empresa', $id_empresa)->first();
            $valor_descuento = $request['descuento1'];
            $cuentas_iva = 0;
            $base1 =  $request['base1'];
            $total1 = $request['total1'];
            if ($valor_descuento > 0) {
                $base1 = $base1 + $valor_descuento;
                $total1 = $total1 + $valor_descuento;
            }
            if ($request['tarifa_iva1'] > 0) {
                $plan_cuentas = Plan_Cuentas::find($globales_iva->debe);
                if (is_null($plan_cuentas)) {
                    return ["respuesta" => "error", "msj" => "Configure la cuenta del debe,del IVA importaciones,  del modulo de Globales"];
                }
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


                $plan = Plan_Cuentas::find($global_gastos->debe);

                if (is_null($plan)) {
                    return ["respuesta" => "error", "msj" => "Configure la cuenta del debe,del los gasto de IMPORTACIONES, desde el modulo de Globales"];
                }

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan->id,
                    'descripcion'         => $plan->nombre,
                    'fecha'               => $fechahoy,
                    'debe'                => $base1,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,

                ]);
            } else {

                $plan = Plan_Cuentas::find($global_gastos->debe);

                if (is_null($plan)) {
                    return ["respuesta" => "error", "msj" => "Configure la cuenta del debe,del los gasto de IMPORTACIONES, desde el modulo de Globales"];
                }

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan->id,
                    'descripcion'         => $plan->nombre,
                    'fecha'               => $fechahoy,
                    'debe'                => $total1,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,

                ]);
            }

            $plan = Plan_Cuentas::find($global_gastos->haber);
            if (is_null($plan)) {
                return ["respuesta" => "error", "msj" => "Configure la cuenta del HBER,del los gasto de IMPORTACIONES, desde el modulo de Globales"];
            }

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan->id,
                'descripcion'         => $plan->nombre,
                'fecha'               => $fechahoy,
                'debe'                => '0',
                'haber'               => $request['total1'],
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $idusuario,
                'ip_modificacion'     => $idusuario,

            ]);
        } catch (\Exception $e) {
            return ["error" => "error", ",msj" => "No se creo el asiento de la factura de Gasto"];
        }
    }

    public function edit_factura($id_factura, Request $request)
    {

        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::find($id_empresa);

        $factura_gasto = Ct_Importaciones_Compras::find($id_factura);

        $proveedor     = Proveedor::where('estado', 1)->get();
        $bodega        = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $productos     = Ct_productos::where('id_empresa', $id_empresa)->get();
        $tipos_comp    = Ct_master_tipos::where('tipo', '1')->where('estado', '1')->get();
        $gastos        = Ct_Imp_Gastos::where('estado', '1')->get();
        $termino       = Ct_Termino::where('estado', '1')->get();
        $c_tributario  = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();

        return view('contable/importaciones/edit_factura', ['id_factura' => $id_factura, 'empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'tipos_comp' => $tipos_comp, 'gastos' => $gastos, 'termino' => $termino, 'c_tributario' => $c_tributario, 't_comprobante' => $t_comprobante, 'factura_gasto' => $factura_gasto]);
    }

    public function search_acive(Request $request) {}


    public function subir_archivo(Request $request, $id)
    {
        //dd("entra");
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::find($id_empresa);
        $imp_archivos = Ct_Importaciones_Archivos::where('id_cab_imp', $id)->where('estado', '1')->get();

        return view('contable/importaciones/index_archivos', ['imp_archivos' => $imp_archivos, 'empresa' => $empresa, 'id' => $id]);
    }

    public function guardar_archivo(Request $request)
    {

        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_importacion = $request['id_importacion'];
        $i               = 1;

        foreach ($files as $file) {

            $extension     = $file->getClientOriginalExtension();

            $fileName = 'archivo_importacion' . $id_importacion . '_' . date('YmdHis') . '.' . $extension;
            Storage::disk('hc_ima')->put($fileName, \File::get($file));

            $input_archivo = [
                'id_cab_imp'      => $id_importacion,
                'nombre_archivo'  => $fileName,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];

            $id_archivo = Ct_Importaciones_Archivos::insertGetId($input_archivo);

            $i = $i + 1;
        }
    }

    public function archivo_descarga($name)
    {
        $imagen          = Ct_Importaciones_Archivos::find($name);
        //$paciente        = paciente::find($imagen->id_paciente);
        $nombre_archivo = null;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre_archivo;
        $nombre_archivo = $nombre_archivo . '_' . $imagen->nombre_archivo;
        //dd($path);

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
        $imagen = Ct_Importaciones_Archivos::find($id);

        $arr = [
            'estado' => 0,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $imagen->update($arr);
    }


    public function anteprima(Request $request)
    {

        $nombreArchivo = Ct_Importaciones_Archivos::where('id', $request['id_imagen'])->first();
        return view('contable/importaciones/ver_anteprima', ['id_imagen' => $nombreArchivo]);
    }
    public function ver_archivo_imp($id_archivo)
    {
        $nombreArchivo = Ct_Importaciones_Archivos::find($id_archivo);
        if (!is_null($nombreArchivo)) {
            $pathtoFile = storage_path() . '/app/hc_ima/' . $nombreArchivo->nombre_archivo;
            //dd($pathtoFile);
            return response()->file($pathtoFile);
        }
        return "no cargado";
    }
}
