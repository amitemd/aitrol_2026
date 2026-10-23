<?php

namespace Sis_medico\Http\Controllers\contable;

//mario Tubay karen zuniga 5
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Imp_Asientos_Cabecera;
use Sis_medico\Ct_Imp_Asientos_Detalle;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Importaciones_Cab;
use Sis_medico\Ct_Importaciones_Compras;
use Sis_medico\Ct_Importaciones_Det;
use Sis_medico\Ct_Importaciones_Detalle_Compra;
use Sis_medico\Ct_Importaciones_Gasto_Cab;
use Sis_medico\Ct_Imp_Gastos;
use Sis_medico\Ct_Modulos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario2;
use Sis_medico\LogAsiento;
use Sis_medico\LogImportaciones;
use Sis_medico\Pais;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\AfGrupo;
use Sis_medico\AfTipo;
use Sis_medico\Af_Bodega_Serie_Color;
use Sis_medico\AfActivo;
use Sis_medico\AfActivo_Accesorios;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\AfSubTipo;
use Sis_medico\Ct_Imp_Liquidacion;
use Sis_medico\Marca;
use Sis_medico\Ct_Termino;
use Sis_medico\Http\Controllers\activosfijos\DocumentoFacturaController;
use Sis_medico\Ct_Inv_Kardex;
use Sis_medico\Http\Controllers\Insumos\CargaInicialController;
use Sis_medico\Log_Ordenes;
use Sis_medico\Log_Pre_Orden_Imp;
use Sis_medico\Producto_Precio_Aprobado;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Imp_Orden_Vendedor;
use Sis_medico\Ct_Imp_Orden_Vendedor_Detalle;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Log_Contable;
use Sis_medico\Venta_Kardex;
use Sis_medico\Permiso_Compra;

class ImportacionesController extends Controller
{
    public function __construct()
    {
        //alex
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        // $importaciones    =   Ct_Importaciones_Cab::orderBy('id', 'desc')->where('estado', 1)->paginate(20);
        $proveedor = Proveedor::where('estado', 1)->get();
        //dd($request->all());

        $busqueda = [
            'id'                    => $request->id,
            'observacion'           => $request->concepto,
            'secuencia_importacion' => $request->sec_importacion,
        ];

        $importaciones = $this->doSearchingQuery($busqueda);

        // $id_usuario = Auth::user()->id;
        // if ($id_usuario == "0957258056") {
        //     //ImportacionesController::createProducto();
        // }

        // $empresas = Empresa::all();
        // $modulos = Ct_Modulos::whereIn('id', ['13', '14', '15', '16'])->get();
        // foreach ($modulos as $mod) {
        //     foreach ($empresas as $emp) {
        //         $existe = Ct_Globales::where('id_modulo', $mod->id)->where('id_empresa', '0992704152001')->first();

        //         $buscar = Ct_Globales::where('id_modulo', $mod->id)->where('id_empresa', $emp->id)->first();

        //         if (is_null($buscar)) {
        //             $data = $existe['attributes'];
        //             unset($data['id']);
        //             $data['id_empresa'] = $emp->id;
        //             Ct_Globales::create($data);
        //         }
        //     }
        // }



        return view('contable/importaciones/index', ['empresa' => $empresa, 'importaciones' => $importaciones, 'busqueda' => $busqueda]);
    }
    public static function createProducto()
    {
        $id_empresa = Session::get('id_empresa');

        $productos = Ct_productos::where('id_empresa', '0992704152001')->get();

        foreach ($productos as $value) {
            $data = $value['original'];
            unset($data["id"]);

            $data["id_empresa"] = $id_empresa;

            Ct_productos::create($data);
        }
    }
    public function createBodegaImportacion($id_empresa)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        // $maestra = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->first();
        $bodegas = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->where('tipo', 2)->where('importacion', 1)->first();


        if (is_null($bodegas)) {
            // $data                       = $maestra['original'];
            // unset($data['id']);
            // $data['tipo']               = 2;
            // $data['nombre']             = 'BODEGA PRODUCTO IMPORTADO';
            // $data['departamento']       = 1;
            // $data['id_usuariocrea']     = $id_usuario;
            // $data['id_usuariomod']      = $id_usuario;
            // Ct_Bodegas::create($data);
            // return ['status' => 'success', 'msj' => 'Guardado Coreectamente'];

            $data = [
                'id_hospital'       => '3',
                'nombre'            => 'BODEGA IMPORTACION',
                'color'             => '#000000',
                'departamento'      => '2',
                'id_empresa'        => $id_empresa,
                'estado'            => 1,
                'encargado'         => '0957258056',
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'tipo'              => '2',
                'importacion'       => '1'
            ];
            Ct_Bodegas::create($data);

            return ['status' => 'success', 'msj' => 'Guardado Coreectamente'];
        } else {
            return ['status' => 'success', 'msj' => 'Ya se encuentra creado'];
        }
    }

    public function create(Request $request)
    {
        $proveedor  = [];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //Crear la bodega de importacion
        ImportacionesController::createBodegaImportacion($id_empresa);
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->where('importacion', 1)->get();
        // $productos  = Ct_productos::where('id_empresa', $id_empresa)->get();
        $productos = [];
        //$pais = Pais::where('estado', 1)->get();
        $pais = [];


        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $term         = Ct_Termino::where('estado', '1')->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        // $ordenes      = Log_Pre_Orden_Imp::where('estado', '1')->where('id_empresa', $id_empresa)->groupBy('id_imp_cab')->get();
        $ordenes = Ct_Imp_Orden_Vendedor::where('estado', '2')->where('id_empresa', $id_empresa)->where('aplicada', 0)->get();
        return view('contable/importaciones/create', ['empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'pais' => $pais, 'sucursales' => $sucursales, 'grupos' => $grupos, 'tipos' => $tipos, 'marcas' => $marcas, 'sub_tipos' => $sub_tipos, 'af_colores' => $af_colores, 'af_series' => $af_series, 'af_responsables' => $af_responsables, 'term' => $term, "ordenes" => $ordenes, 'min_date' => $min_date]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        DB::beginTransaction();
        try {
            $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->f_autorizacion);
            $verifica_cierre = json_decode($verifica_cierre);
            if ($verifica_cierre->status == 'error') {
                return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $request['modulo'] = "importacion";
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $sucursal   = $cod_sucurs->codigo_sucursal;
            $cod_caj       = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $punto_emision = $cod_caj->codigo_caja;
            $serie = "{$sucursal}-{$punto_emision}";
            //Crear el asiento de importaciones
            $modulo   = Ct_Modulos::find(13);
            if (is_null($modulo)) {
                DB::rollback();
                return ['respuesta' => 'error', 'msj' => "No tiene un modulo de configuracion", 'titulos' => 'Error'];
            }
            $globales = Ct_Globales::where('id_modulo', $modulo->id)->where('id_empresa', $id_empresa)->first();
            if (is_null($globales)) {
                DB::rollback();
                return ['respuesta' => 'error', 'msj' => "No se creo esta configurado las cuentas globales", 'titulos' => 'Error'];
            }
            // $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
            //dd($modulo, $globales);
            //dd($modulo);
            if (!is_null($modulo)) {

                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'sucursal'        => $sucursal,
                    'punto_emision'   => $punto_emision,
                    'fecha_asiento'   => $request->f_autorizacion,
                    'fact_numero'     => $request->secuencia_factura,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => "IMPORTACION # {$serie}-{$request->secuencia_factura} | {$request->observacion_2}",
                    'valor'           => $request->total1,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);

                if (!is_null($id_asiento)) {

                    if (!isset($globales->debec)) {
                        DB::rollback();
                        return ['respuesta' => 'error', 'msj' => "No esta configurado las cuenta globales del DEBE importaciones", 'titulos' => 'Error'];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $globales->debe,
                        'descripcion'         => $globales->debec->nombre,
                        'fecha'               => $request->f_autorizacion,
                        'debe'                => $request->total1,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);

                    if (!isset($globales->debec)) {
                        DB::rollback();
                        return ['respuesta' => 'error', 'msj' => "No esta configurado las cuenta globales del HABER importaciones", 'titulos' => 'Error'];
                    }

                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $globales->haber,
                        'descripcion'         => $globales->haberc->nombre,
                        'fecha'               => $request->f_autorizacion,
                        'haber'               => $request->total1,
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            }
            //  dd($request->pais);

            //tabla de importaciones
            $pais = Pais::where('id', $request->pais)->orWhere('nombre', strtoupper($request->pais))->first();

            $id_cabecera = Ct_Importaciones_Cab::insertGetId([
                'sucursal'              => $sucursal,
                'punto_emision'         => $punto_emision,
                'id_asiento_cabecera'   => $id_asiento,
                'id_proveedor'          => $request->proveedor,
                'fecha'                 => $request->f_autorizacion,
                'observacion'           => $request->observacion_2,
                'id_cliente'            => $request->id_empresa,
                'subtotal'              => $request->total1,
                'pais'                  => $pais->id,
                'pais_procedencia'      => $request->pais_procedencia,
                'secuencia_importacion' => $request->secuencia_importacion,
                'descuento'             => $request->descuento,
                'estado'                => 1,
                'estado_imp'            => 1,
                'secuencia_factura'     => $request->secuencia_factura,
                'serie'                 => $request->serie,
                'id_empresa'            => $id_empresa,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
            ]);



            for ($i = 0; $i < count($request->producto); $i++) {
                //dd($request->all());
                Ct_Importaciones_Det::create([
                    'id_cab'          => $id_cabecera,
                    'id_producto'     => $request->producto[$i],
                    'cantidad'        => $request->cantidad[$i],
                    'precio'          => $request->precio[$i],
                    'descuento'       => $request->desc[$i],
                    'subtotal'        => $request->precioneto[$i],
                    'peso'            => $request->peso[$i],
                    'id_bodega'          => $request->bodega[$i],
                    //'porcentaje'      => $request->porcentaje[$i],
                    //'precio_desc'     => $request->precio_desc[$i],
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'af'              => $request->check_af[$i],
                    'lote'            => $request->lote[$i],
                    'fecha_vencimiento' => $request->fecha_venc[$i]
                ]);


                $request['fecha_compra'] = $request['f_autorizacion'];
            }

            for ($m = 0; $m < count($request->check_af); $m++) {
                if ($request->check_af[$m] == 1) {

                    $activo = DocumentoFacturaController::store_AfActivo($request);

                    if ($activo["respuesta"] == "error") {
                        DB::rollBack();
                        return $activo;
                    }
                    break;
                }
            }
            //dd($parar, $entro);

            $numero = "{$serie}-{$request->secuencia_factura}";

            // $div= explode("-", trim($request->serie));

            // $sucursal = $div[0];
            // $punto_emision = $div[1];



            $imp_compras = [
                'tipo'                => 1,
                'id_asiento_cabecera' => $id_asiento,
                'observacion'         => $request->observacion_2,
                'fecha'               => $request->f_autorizacion,
                'f_autorizacion'      => $request->f_autorizacion,
                'secuencia_factura'   => $request->secuencia_factura,
                'numero'              => $numero,
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'tipo'                => 1,
                'serie'               => $request->serie,
                'direccion_proveedor' => $request->direccion_proveedor,
                'estado'              => 1,
                'tipo_comprobante'    => '01',
                'proveedor'           => $request->proveedor,
                'id_empresa'          => $id_empresa,
                'subtotal_12'         => 0,
                'descuento'           => 0,
                'subtotal_0'          => $request->subtotal_01,
                'subtotal'            => $request->subtotal_01,
                'total_final'         => $request->total1,
                'valor_contable'      => $request->total1,
                'subtotal_12'         => $request->subtotal_12,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            $id_cabecera_compras = Ct_Importaciones_Compras::insertGetId($imp_compras);

            $id_compras = Ct_Compras::insertGetId($imp_compras);

            for ($i = 0; $i < count($request->producto); $i++) {
                //dd($request->producto);
                //$ct_productos = Ct_productos::find($request->producto[$i]);
                $codigo_producto = Ct_productos::where('id', $request->producto[$i])->first();

                //  dd($codigo_producto, $request->producto[$i]);
                if (is_null($codigo_producto)) {
                    DB::rollback();
                    return ['status' => 'error', 'msj' => 'No existe el producto en Producto Contable'];
                }

                $det_imp = [
                    'id_ct_compras'        => $id_cabecera_compras,
                    'detalle'              => $request->descrip_prod[$i],
                    'codigo'               => $codigo_producto->codigo,
                    'bodega'               => $request->bodega[$i],
                    'cantidad'             => $request->cantidad[$i],
                    'total'                => $request->precioneto[$i],
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'lote'                 => $request->lote[$i],
                    'fecha_exp'            => $request->fecha_venc[$i],
                    'estado'               => 1,
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];

                Ct_Importaciones_Detalle_Compra::insertGetId($det_imp);




                // if($request['producto']){

                // }
                if ($request->codigo[$i] != "TRANS") {
                    $det_compras = [
                        'id_ct_compras'        => $id_compras,
                        'detalle'              => $request->descrip_prod[$i],
                        'codigo'               => $codigo_producto->codigo,
                        'bodega'               => $request->bodega[$i],
                        'cantidad'             => $request->cantidad[$i],
                        'total'                => $request->precioneto[$i],
                        'precio'               => $request->precio[$i],
                        'descuento_porcentaje' => $request->descpor[$i],
                        'descuento'            => $request->desc[$i],
                        'lote'                 => $request->lote[$i],
                        'fecha_exp'            => $request->fecha_venc[$i],
                        'estado'               => 1,
                        'id_usuariocrea'       => $id_usuario,
                        'id_usuariomod'        => $id_usuario,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                    ];

                    Ct_detalle_compra::insertGetId($det_compras);
                }
            }
            LogImportaciones::create([
                "id_import_cab"   => $id_cabecera,
                "id_compra"       => $id_compras,
                "id_usuariocrea"  => $id_usuario,
                "id_usuariomod"   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            if (!is_null($request["pre_orden"]) or $request["pre_orden"] != "") {

                // $id_pre_orden = Log_Pre_Orden_Imp::find($request["pre_orden"]);
                // // if(Auth::user()->id == "0957258056"){
                // //     dd($id_pre_orden);
                // // }
                // //  dd($id_pre_orden);
                // $data_pre = [
                //     "estado"            => 2,
                //     "id_usuariomod"    => $id_usuario,
                //     "ip_modificacion" => $ip_cliente
                // ];
                // $log_pre_orden = Log_Pre_Orden_Imp::where('id_imp_cab', $id_pre_orden->id_imp_cab)->where("estado", 1)->update($data_pre);

                // $log_ordenes = Log_Ordenes::where('id_orden', $id_pre_orden->id_imp_cab)->first();
                // if (!is_null($log_ordenes)) {
                //     $log_ordenes->estado               = 0;
                //     $log_ordenes->id_usuariomod        = $id_usuario;
                //     $log_ordenes->ip_modificacion      = $ip_cliente;
                //     $log_ordenes->save();
                // }
                $orden_vendedor  = Ct_Imp_Orden_Vendedor::find($request["pre_orden"]);
                $orden_vendedor->aplicada = 1;
                $orden_vendedor->id_usuariomod = $id_usuario;
                $orden_vendedor->ip_modificacion = $ip_cliente;
                $orden_vendedor->save();
            }

            DB::commit();
            //$inventario = Inventario2::build_process('C', $id_compras, $id_empresa);
            //dd($inventario);
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id' => $id_cabecera, 'id_asiento' => $id_asiento];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function edit($id, Request $request)
    {
        $proveedor           = Proveedor::where('estado', 1)->get();
        $id_empresa          = $request->session()->get('id_empresa');
        $empresa             = Empresa::find($id_empresa);
        $bodega              = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $productos           = Ct_productos::where('id_empresa', $id_empresa)->get();
        $pais                = Pais::where('estado', 1)->get();
        $datos_importaciones = Ct_Importaciones_Cab::find($id);
        return view('contable/importaciones/edit', ['empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'datos_importaciones' => $datos_importaciones, 'pais' => $pais]);
    }

    public function create_orden($id, Request $request)
    {
        //$cab = Ct_Importaciones_Cab::find($id);
        $proveedor  = Proveedor::where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $productos  = Ct_Imp_Gastos::where('estado', 1)->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/importaciones/create_orden', ['min_date' => $min_date, 'empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'id_importacion' => $id]);
    }

    public function _pdf_importaciones($id, Request $request)
    {
        $proveedor     = Proveedor::all();
        $detalle_gasto = Ct_Imp_Gastos::where('estado', 1)->get();

        $arrRecalcular = ImportacionesController::recalcularImportaciones($id);

        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }
        $gastos                       = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->orderBy('tipo', 'ASC')->get();
        $ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->first();

        $sum_egre_info                = 0;
        $sum_egre_costo               = 0;
        $ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->get();

        $vistaurl = "contable.importaciones.pdf_importaciones";
        $view     = \View::make($vistaurl, compact('arrRecalcular', 'ct_importaciones_cab', 'gastos', 'proveedor', 'detalle_gasto', 'ct_comprobante_egreso_varios', 'sum_egre_info', 'sum_egre_costo'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }


    public function pdf_importaciones($id, Request $request)
    {
        $proveedor = Proveedor::all();
        $otros_gastos = ImportacionesController::recalcularImportaciones($id);

        //dd($otros_gastos);

        // dd($otros_gastos["total_gastos"]);

        $logImportaciones = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first();


        if (is_null($logImportaciones)) {
            return response()->view('errors.404');
        }


        $compras = Ct_Importaciones_Compras::find($logImportaciones->id_compra);

        $detalle_gasto = Ct_Imp_Gastos::where('estado', 1)->get();

        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }

        $gastos = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->orderBy('tipo', 'ASC')->get();

        $sum_egre_info = 0;
        $sum_egre_costo = 0;
        $ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->get();

        foreach ($ct_comprobante_egreso_varios as $value) {
            foreach ($value->detalles as $det) {

                if ($det->tipo_liq == 1) {
                    $sum_egre_info += $det->debe;
                } elseif ($det->tipo_liq == 2) {
                    $sum_egre_costo += $det->debe;
                }
            }
        }

        $vistaurl = "contable.importaciones.pdf_importaciones";
        $view     = \View::make($vistaurl, compact('otros_gastos', 'compras', 'sum_egre_info', 'sum_egre_costo', 'ct_comprobante_egreso_varios', 'ct_importaciones_cab', 'gastos', 'proveedor', 'detalle_gasto', 'id'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function create_recibo($id, Request $request)
    {
        //$proveedor  = Proveedor::where('estado', 1)->get();
        $proveedor = [];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //$productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $productos = Ct_Imp_Gastos::where('estado', 1)->get();
        // dd($productos);
        $cab        = Ct_Importaciones_Cab::find($id);
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/importaciones/create_recibo', ['min_date' => $min_date, 'sucursales' => $sucursales, 'cab' => $cab, 'empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'id_importacion' => $id]);
    }

    public function store_orden(Request $request)
    {
        // dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        //  dd($request->all());
        DB::beginTransaction();
        try {

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['f_autorizacion']));
            if ($verifica_cierre->status == 'error') {
                return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message, 'titulos' => "Error en el cierre"];
            }

            $id_cabecera_compras = Ct_Importaciones_Compras::insertGetId([ //tabla de compras
                'fecha'               => $request->f_autorizacion,
                'tipo'                => 3, //es un recibo
                'direccion_proveedor' => $request->direccion_proveedor,
                'estado'              => 1,
                'observacion'         => $request->observacion,
                'proveedor'           => $request->proveedor,
                'id_empresa'          => $id_empresa,
                'valor_contable'      => $request->total1,
                'subtotal_0'          => $request->subtotal_01,
                'total_final'         => $request->total1,
                'descuento'           => $request->descuento1,
                'subtotal_12'         => $request->subtotal_12,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            // 'subtotal_0'          => $request->subtotal_01,
            // 'descuento'           => $request->descuento1,
            // 'subtotal'            => $request->base1,
            // 'total_final'         => $request->total1,
            // 'valor_contable'      => $request->total1,
            // 'subtotal_12'         => $request->subtotal_12,

            for ($i = 0; $i < count($request->producto); $i++) {
                Ct_Importaciones_Detalle_Compra::insertGetId([
                    'id_ct_compras'        => $id_cabecera_compras,
                    'detalle'              => $request->descrip_prod[$i],
                    'codigo'               => $request->producto[$i],
                    'id_gasto'             => $request->producto[$i],
                    'cantidad'             => $request->cantidad[$i],
                    'total'                => $request->precioneto[$i],
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'estado'               => 1,
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ]);
            }

            Ct_Importaciones_Gasto_Cab::create([
                'id_import_cabl'   => $request->id_importacion,
                'id_import_compra' => $id_cabecera_compras,
                'tipo'             => 3,
                'id_usuariocrea'   => $id_usuario,
                'id_usuariomod'    => $id_usuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ]);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function store_recibo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $id_usuario = Auth::user()->id;
        //dd($request->all());
        DB::beginTransaction();
        try {

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['f_autorizacion']));
            if ($verifica_cierre->status == 'error') {
                return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message, 'titulos' => "Error en el cierre"];
            }


            //  dd(ImportacionesController::recalcularImportaciones($request->id_importacion));
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $sucursal   = $cod_sucurs->codigo_sucursal;

            $cod_caj       = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $punto_emision = $cod_caj->codigo_caja;


            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->get()->count();

            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                $max_id = intval($max_id->secuencia_f);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }


            //$numeroconcadenado = $request['serie'] . '-' . $request['secuencia_factura'];
            $numeroconcadenado = "{$request->serie}-{$request['secuencia_factura']}";
            $cab_asiento       = [
                'sucursal'        => $sucursal,
                'punto_emision'   => $punto_emision,
                'observacion'     => $request['observacion'],
                'fecha_asiento'   => $request['f_autorizacion'],
                'fact_numero'     => $request['secuencia_factura'],
                'valor'           => $request->total1,
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ];


            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cab_asiento);

            //$serie        = "{$sucursal}-{$punto_emision}";
            $arr_cabecera = [ //tabla de compras
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                //'archivo_sri'         => 1,
                'archivo_sri'         => 0,
                'fecha'               => $request->f_autorizacion,
                'f_autorizacion'      => $request->f_autorizacion,
                'secuencia_factura'   => $request->secuencia_factura,
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'tipo'                => 1, //es un recibo
                'serie'               => $request->serie,
                'direccion_proveedor' => $request->direccion_proveedor,
                'estado'              => 1,
                'secuencia_f'         => $numero_factura,
                'proveedor'           => $request->proveedor,
                'id_empresa'          => $id_empresa,
                'subtotal_0'          => $request->base1,
                'descuento'           => $request->descuento1,
                'subtotal'            => $request->base1,
                'total_final'         => $request->total1,
                'valor_contable'      => $request->total1,
                'subtotal_12'         => '0,00',
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'observacion'         => $request->observacion,
                'numero'              => $numeroconcadenado,
            ];

            $id_cabecera = Ct_Importaciones_Compras::insertGetId($arr_cabecera);

            $cab_compras = Ct_Compras::insertGetId($arr_cabecera);

            // dd($cab_compras);
            // dd($request->all());
            for ($i = 0; $i < count($request->producto); $i++) {
                $gasto = Ct_Imp_Gastos::find($request["producto"][$i]);
                $producto = Ct_productos::where("codigo", $gasto->codigo)->first();
                if (is_null($producto)) {
                    // dd($request["producto"][$i]);
                }
                $arr_det = [
                    'id_ct_compras'        => $id_cabecera,
                    'detalle'              => $request->descrip_prod[$i],
                    'nombre'               => $producto->nombre,
                    'codigo'               => $producto->codigo,
                    'cantidad'             => $request->cantidad[$i],
                    'total'                => $request->precioneto[$i],
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'estado'               => 1,
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];

                Ct_Importaciones_Detalle_Compra::insertGetId($arr_det);

                $arr_detcompras = [
                    'id_ct_compras'        => $cab_compras,
                    'detalle'              => $request->descrip_prod[$i],
                    'nombre'               => $producto->nombre,
                    'codigo'               => $producto->codigo,
                    'cantidad'             => $request->cantidad[$i],
                    'total'                => $request->precioneto[$i],
                    'precio'               => $request->precio[$i],
                    'descuento_porcentaje' => $request->descpor[$i],
                    'descuento'            => $request->desc[$i],
                    'estado'               => 1,
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ];
                Ct_detalle_compra::insertGetid($arr_detcompras);
            }
            Ct_Importaciones_Gasto_Cab::create([
                'id_import_cabl'   => $request->id_importacion,
                'id_import_compra' => $id_cabecera,
                'tipo'             => 2,
                'id_ct_compra'     => $cab_compras,
                'id_usuariocrea'   => $id_usuario,
                'id_usuariomod'    => $id_usuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ]);

            $asientos = $this->asiento_recibo($request, $id_asiento_cabecera, $id_usuario, $ip_cliente);
            DB::commit();
            $compra_maestra = ImportacionesController::recalcularImportaciones($request->id_importacion);
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_asiento' => $id_asiento_cabecera, "compra_maestra" => $compra_maestra["id_ct_compra"]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function asiento_recibo($request, $id_asiento_cabecera, $id_usuario, $ip_cliente)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //$global_gastos = Ct_Globales::where('id_modulo', 15)->where('id_empresa', $id_empresa)->first();
        $global_gastos = Ct_Globales::where('id_modulo', 22)->where('id_empresa', $id_empresa)->first();

        // dd($global_gastos);
        if ($request->total1 > 0) {
            $plan = Plan_Cuentas::find($global_gastos->debe);

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan->id,
                'descripcion'         => $plan->nombre,
                'fecha'               => $request['f_autorizacion'],
                'debe'                => $request->total1,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $planc = Plan_Cuentas::find($global_gastos->haber);
            //dd($global_gastos);
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $planc->id,
                'descripcion'         => $planc->nombre,
                'fecha'               => $request['f_autorizacion'],
                'debe'                => '0',
                'haber'               => $request->total1,
                'estado'              => '1',
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }
    }

    public function viewImportaciones($id, Request $request)
    {
        $importacion = Ct_Importaciones_Cab::find($id);
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::find($id_empresa);
        $productos   = Ct_productos::where('id_empresa', $id_empresa)->get();
        $bodega      = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        return view('contable/importaciones/viewImportacion', ['empresa' => $empresa, 'importacion' => $importacion, 'id_importacion', $id, 'bodega' => $bodega]);
    }

    public function liquidacion(Request $request, $id)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::find($id_empresa);
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas     = Ct_divisas::where('estado', '1')->get();
        $sucursales  = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $cuentas = Plan_Cuentas::where('p.estado', 2)
            ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
            ->where('p.id_empresa', $id_empresa)
            ->select('plan_cuentas.id', 'p.plan as plan_id', 'p.nombre as nombre')
            ->get();

        $seteo = ['1.01.03.01.05', '1.01.03.01.02'];

        $cuentas_liq = Ct_Imp_Liquidacion::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        $banco   = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/importaciones/liquidacion', ['min_date' => $min_date, 'divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'sucursales' => $sucursales, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas, 'id' => $id, 'seteo' => $seteo, 'cuentas_liq' => $cuentas_liq]);
    }

    public function store_liquidacion(Request $request)
    {
        // dd($request->all());

        if (!is_null($request['contador'])) {
            $sucursal      = $request['sucursal'];
            $id_empresa    = $request->session()->get('id_empresa');
            $punto_emision = $request['punto_emision'];
            $sucursal      = substr($punto_emision, 0, -4);
            $punto_emision = substr($punto_emision, 4);
            //$contador_ctv = DB::table('ct_comprobante_secuencia')->where('empresa', $id_empresa)->where('tipo', 2)->get()->count();
            $ip_cliente     = $_SERVER["REMOTE_ADDR"];
            $objeto_validar = new Validate_Decimals();
            $numero_factura = 0;
            $idusuario      = Auth::user()->id;
            $id_empresa     = $request->session()->get('id_empresa');
            DB::beginTransaction();
            try {


                $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
                if ($verifica_cierre->status == 'error') {
                    return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                }

                $numero_factura = LogAsiento::getSecuencia(2);

                $input_cabecera = [
                    'observacion'     => $request['concepto'],
                    'fecha_asiento'   => $request['fecha_hoy'],
                    'fact_numero'     => $numero_factura,
                    'valor'           => $objeto_validar->set_round($request['valor_cheque']),
                    'id_empresa'      => $id_empresa,
                    'estado'          => '3',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $banco               = $request['banco'];
                // SE DESCUADRO EL BALANCE
                if (($banco) != null) {
                    $nuevo_saldof      = $objeto_validar->set_round($request['valor_cheque']);
                    $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                    $desc_cuenta       = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
                    //for($y = 0; $y < count($request['haber']) ; $y++ ){
                    if ($request['haber'] > 0) {
                        //  dd("aqiiiii");
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                            'descripcion'         => $consulta_db_cajab->nombre,
                            'fecha'               => $request['fecha_hoy'],
                            'haber'               => $nuevo_saldof,
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    } else {
                        // dd("aqiiiii2");
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                            'descripcion'         => $consulta_db_cajab->nombre,
                            'fecha'               => $request['fecha_hoy'],
                            'debe'                => $nuevo_saldof,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                    //}
                }
                $input_comprobante = [
                    'descripcion'         => $request['concepto'],
                    'estado'              => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'        => 'null',
                    'nota'                => $request['nota'],
                    'fecha_comprobante'   => $request['fecha_hoy'],
                    'beneficiario'        => strtoupper($request['beneficiario']),
                    'check'               => $request['verificar_cheque'],
                    'girado'              => $request['giradoa'],
                    'id_caja_banco'       => $request['banco'],
                    'nro_cheque'          => $request['numero_cheque'],
                    'valor'               => $objeto_validar->set_round($request['valor_cheque']),
                    'fecha_cheque'        => $request['fecha_cheque'],
                    'secuencia'           => $numero_factura,
                    'id_empresa'          => $id_empresa,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'num_liquidacion'     => $request->num_liq,
                    'id_importacion'      => $request->id_importacion,
                ];

                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                for ($i = 0; $i < count($request['debe']); $i++) {

                    $nuevo_saldof = $objeto_validar->set_round($request['debe'][$i]);
                    //dd("asads", $i,  $request['codigo'][$i], $request['debe' . $i]);
                    if (!is_null($request['codigo'][$i])) {
                        // dd("aqui4", $request['codigo' . $i] );
                        $desc_cuenta = Plan_Cuentas::where('id', $request['codigo'][$i])->first();

                        if ($desc_cuenta != null) {
                            if ($request['visibilidad'][$i] == 1 || $request['visibilidad'][$i] == '1') {
                                if (!is_null($request['debe'][$i]) && $request['debe'][$i] > 0) {
                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $request['codigo'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $request['fecha_hoy'],
                                        'debe'                => $nuevo_saldof,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                                if (!is_null($request['haber'][$i]) && $request['haber'][$i] > 0) {
                                    $nuevo_saldof = $objeto_validar->set_round($request['haber'][$i]);

                                    Ct_Asientos_Detalle::create([
                                        'id_asiento_cabecera' => $id_asiento_cabecera,
                                        'id_plan_cuenta'      => $request['codigo'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $request['fecha_hoy'],
                                        'haber'               => $nuevo_saldof,
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                            }
                        }
                    }

                    if (!is_null($request['debe'][$i]) && $request['debe'][$i] > 0) {
                        $cons = Plan_Cuentas::find($request['codigo'][$i]);
                        Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'tipo_liq'              => $request['tipo'][$i],
                            'id_comprobante_varios' => $id_comprobante,
                            'codigo'                => $request['codigo'][$i],
                            'cuenta'                => $cons->nombre,
                            'descripcion'           => $request['observacion'],
                            'debe'                  => $request['debe'][$i],
                            'id_secuencia'          => $numero_factura,
                            'estado'                => '1',
                            'ip_creacion'           => $ip_cliente,
                            'ip_modificacion'       => $ip_cliente,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                        ]);
                    }
                    if (!is_null($request['haber'][$i]) && $request['haber'][$i] > 0) {
                        $cons = Plan_Cuentas::find($request['codigo'][$i]);
                        Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'tipo_liq'              => $request['tipo'][$i],
                            'id_comprobante_varios' => $id_comprobante,
                            'codigo'                => $request['codigo'][$i],
                            'cuenta'                => $cons->nombre,
                            'descripcion'           => $request['observacion'],
                            'debe'                  => $request['haber'][$i],
                            'id_secuencia'          => $numero_factura,
                            'estado'                => '1',
                            'ip_creacion'           => $ip_cliente,
                            'ip_modificacion'       => $ip_cliente,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                        ]);
                    }
                }
                ImportacionesController::recalcularImportaciones($request->id_importacion);

                DB::commit();
                // return $id_comprobante;
                return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_asiento' => $id_asiento_cabecera, "id_comprobante" => $id_comprobante];
            } catch (\Exception $e) {
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
            }
        } else {
            return 'error no guardó nada';
        }
    }
    public function store_pais(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        //dd($request->all());
        $nombrePais = trim(strtoupper($request->pais));
        $pais       = Pais::where('nombre', $nombrePais)->first();

        if (is_null($pais)) {
            if (trim($nombrePais) != '') {
                $id_pais = Pais::insertGetId([
                    'nombre'          => $nombrePais,
                    'estado'          => 1,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
                return 'ok';
            } else {
                return 'no';
            }
        } else {
            return 'no';
        }
    }

    public function crear_agrupada(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $cab        = Ct_Importaciones_Cab::where('estado', 1)->orderBy('id', 'DESC')->where('agrupada', '0')->where("id_empresa", $id_empresa)->get();
        $empresa    = Empresa::find($id_empresa);

        return view('contable/importaciones/crear_agrupada', ['importaciones' => $cab, 'empresa' => $empresa]);
    }

    public function store_agrupada(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $subtotal   = 0;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        // if(!isset($request->checks)) {
        //     return ['respuesta' => 'error', 'title' => 'Error...', 'msj' => 'Debe Seleccionar al menos 2 pedidos para agruparlos'];
        // }
        if (!isset($request->checks) or count($request->checks) <= 1) {
            return ['respuesta' => 'error', 'title' => 'Error...', 'msj' => 'Debe Seleccionar al menos 2 pedidos para agruparlos'];
        }

        for ($i = 0; $i < count($request->checks); $i++) {
            $imp_cabecera = Ct_Importaciones_Cab::find($request['checks'][$i]);
            $gasto_cab    = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $imp_cabecera->id)->first();

            if (!is_null($gasto_cab)) {
                return ['respuesta' => 'error', 'title' => 'Error...', 'msj' => "Ya existen gastos en la importacion: {$imp_cabecera->secuencia_importacion}"];
            }
        }

        DB::beginTransaction();
        try {
            $id_cabecera = Ct_Importaciones_Cab::insertGetId([
                'id_proveedor'          => null,
                'fecha'                 => date('Y-m-d'),
                'observacion'           => $request->concepto,
                'id_cliente'            => null,
                'subtotal'              => 0,
                'pais'                  => null,
                'pais_procedencia'      => null,
                'secuencia_importacion' => $request->serie_agrup,
                'descuento'             => 0,
                'estado'                => 1,
                'estado_imp'            => 1,
                'secuencia_factura'     => null,
                'serie'                 => null,
                'agrupada'              => 1,
                'id_empresa'            => $id_empresa,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
            ]);

            for ($i = 0; $i < count($request->checks); $i++) {
                $cab             = Ct_Importaciones_Cab::find($request['checks'][$i]);
                $cab->estado     = 2;
                $cab->id_cab_imp = $id_cabecera;
                $subtotal += $cab->subtotal;
                $cab->save();
            }

            $agrupada           = Ct_Importaciones_Cab::find($id_cabecera);
            $agrupada->subtotal = $subtotal;
            $agrupada->total = $subtotal;
            $agrupada->save();

            DB::commit();

            return ['respuesta' => 'success', 'title' => 'Exito...', 'msj' => 'Guardado Correctamente'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['respuesta' => 'error', 'title' => 'Error...', 'msj' => 'Error al guardar', 'observacion' => $e->getMessage()];
        }
    }


    public static function validarFecLote($id_importacion)
    {
        $logImportaciones = LogImportaciones::where('id_import_cab', $id_importacion)->where('principal', 2)->first();

        $compra = Ct_Importaciones_Compras::find($logImportaciones->id_compra);

        $msj = "";

        foreach ($compra->detalles as $det_compra) {
            if (is_null($det_compra->lote) or trim($det_compra->lote) == "") {
                $msj = "No se ha ingresado el lote";
            }

            if (is_null($det_compra->fecha_exp)) {
                $msj = "<br>No se ha ingresado la fecha de vencimiento";
            }

            if ($msj != "") {
                return ["status" => "error", "msj" => "{$msj}<br><b>Producto: {$det_compra->producto->nombre}</b>"];
            }
        }


        // $detalles = Ct_Importaciones_Det::where('id_cab', $id_importacion)->get();

        // foreach ($detalles as $value) {
        //     $msj = "";
        //     if (is_null($value->lote) or trim($value->lote) == "") {
        //         $msj = "No se ha ingresado el lote";
        //     }

        //     if (is_null($value->fecha_vencimiento)) {
        //         $msj = "<br>No se ha ingresado la fecha de vencimiento";
        //     }

        //     if ($msj != "") {
        //         return ["status" => "error", "msj" => "{$msj}<br><b>Producto: {$value->productos->nombre}</b>"];
        //     }
        // }

        return ["status" => "success", "msj" => "conmtinuar"];
    }

    public function store_kardex(Request $request)
    {
        $status = "error";
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $impo = Ct_Importaciones_Cab::find($request->id);
        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($impo->fecha);
        $verifica_cierre = json_decode($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        ImportacionesController::recalcularImportaciones($request->id);

        $id_empresa = $request->session()->get('id_empresa');

        $empresa_inventario = Empresa::find($id_empresa);

        if ($empresa_inventario->inventario == 0) {
            return ['status' => 'error', 'msj' => 'La empresa no tiene inventario'];
        }

        if ($impo->inventario == 1) {
            return ['status' => 'error', 'msj' => 'Este pedido ya se ingreso al Kardex'];
        }

        $validarFecLote = ImportacionesController::validarFecLote($impo->id);

        if ($validarFecLote["status"] == "error") {
            return $validarFecLote;
        }

        DB::beginTransaction();
        try {

            $logImportaciones = LogImportaciones::where('id_import_cab', $impo->id)->where('principal', 2)->first();
            if (!is_null($logImportaciones)) {
                $cab_compra = Ct_Importaciones_Compras::find($logImportaciones->id_compra);
                //crea el asiento 
                $id_asiento = ImportacionesController::crearAsientoCompra($cab_compra->id_asiento_cabecera);
                //dd($id_asiento);
                if ($id_asiento["status"] == "error") {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No se creo el asiento", "exp" => $id_asiento["msj"]];
                }
                //Crear en Ct_compras
                // $data = $cab_compra['attributes'];
                // unset($data['id'], $data["created_at"], $data["updated_at"], $data["total_original"]);
                // $data['numero']                 = 'IMPORTACION';
                // $data["f_caducidad"]            = $data['fecha'];
                // $data["id_asiento_cabecera"]    = $id_asiento["id_asiento"];
                // $data["id_usuariocrea"]         = $id_usuario;
                // $data["id_usuariomod"]          = $id_usuario;
                // $data["ip_creacion"]            = $ip_cliente;
                // $data["ip_modificacion"]        = $ip_cliente;


                $data3 = [
                    "numero"                => "IMPORTACION",
                    "secuencia_f"           => $cab_compra->secuencia_f,
                    "punto_emision"         => $cab_compra->punto_emision,
                    "sucursal"              => $cab_compra->sucursal,
                    "id_asiento_cabecera"   => $id_asiento["id_asiento"],
                    "pedido"                => null,
                    "tipo"                  => $cab_compra->tipo,
                    "fecha_termino"         => null,
                    "fecha"                 => $cab_compra->fecha,
                    "estado"                => -5,
                    "archivo_sri"           => null,
                    "proveedor"             => $cab_compra->proveedor,
                    "direccion_proveedor"   => $cab_compra->direccion_proveedor,
                    "termino"               => $cab_compra->termino,
                    "orden_compra"          => $cab_compra->orden_compra,
                    "f_caducidad"           => $cab_compra->fecha,
                    "tipo_gasto"            => $cab_compra->tipo_gasto,
                    "autorizacion"          => $cab_compra->autorizacion,
                    "f_autorizacion"        => $cab_compra->f_autorizacion,
                    "valor_contable"        => $cab_compra->valor_contable,
                    "serie"                 => $cab_compra->serie,
                    "secuencia_factura"     => $cab_compra->secuencia_factura,
                    "credito_tributario"    => $cab_compra->credito_tributario,
                    "tipo_comprobante"      => 1,
                    "observacion"           => $cab_compra->observacion,
                    "subtotal_0"            => $cab_compra->subtotal_0,
                    "subtotal_12"           => $cab_compra->subtotal_12,
                    "subtotal"              => $cab_compra->subtotal,
                    "descuento"             => $cab_compra->descuento,
                    "iva_total"             => $cab_compra->iva_total,
                    "ice_total"             => $cab_compra->ice_total,
                    "transporte"            => $cab_compra->transporte,
                    "total_final"           => $cab_compra->total_final,
                    "id_empresa"            => $cab_compra->id_empresa,
                    "id_usuariocrea"        => $id_usuario,
                    "id_usuariomod"         => $id_usuario,
                    "ip_creacion"           => $ip_cliente,
                    "ip_modificacion"       => $ip_cliente,
                    "rutapdf"               => $cab_compra->rutapdf,
                ];
                // dd($data,$data3, $cab_compra);
                $id_compra = Ct_compras::insertGetId($data3);
                foreach ($cab_compra->detalles as $det_compra) {
                    // $detalle = [];
                    // $detalle = $det_compra["attributes"];

                    // $detalle["total"] = $detalle["costo_total"];
                    // $detalle["precio"] = $detalle["costo_total"];

                    // unset(
                    //     $detalle['id'],
                    //     $detalle["prct_item"],
                    //     $detalle["costo_asignado_total"],
                    //     $detalle["costo_unitario"],
                    //     $detalle['id_gasto'],
                    //     $detalle["costo_total"],
                    //     $detalle["peso_kg"],
                    //     $detalle["costo_asignado_unidad"],
                    //     $detalle["created_at"],
                    //     $detalle["updated_at"],
                    //     $detalle["lote"],
                    //     $detalle["fecha_exp"]
                    // );

                    // $detalle["id_ct_compras"]          = $id_compra;
                    // $detalle["id_usuariocrea"]         = $id_usuario;
                    // $detalle["id_usuariomod"]          = $id_usuario;
                    // $detalle["ip_creacion"]            = $ip_cliente;
                    // $detalle["ip_modificacion"]        = $ip_cliente;

                    $data2 = [
                        "id_ct_compras"         => $id_compra,
                        "detalle"               => $det_compra->detalle,
                        "codigo"                => $det_compra->codigo,
                        "nombre"                => $det_compra->nombre,
                        "bodega"                => $det_compra->bodega,
                        "cantidad"              => $det_compra->cantidad,
                        "empaque"               => $det_compra->empaque,
                        "total"                 => $det_compra->costo_total,
                        // "precio"                => $det_compra->costo_total,["costo_total"],
                        "precio"                => $det_compra->costo_unitario,
                        "descuento_porcentaje"  => $det_compra->descuento_porcentaje,
                        "descuento"             => $det_compra->descuento,
                        "extendido"             => $det_compra->extendido,
                        "iva"                   => $det_compra->iva,
                        "porcentaje"            => $det_compra->porcentaje,
                        "ice"                   => $det_compra->ice,
                        "estado"                => 1,
                        "id_usuariocrea"        => $id_usuario,
                        "id_usuariomod"         => $id_usuario,
                        "ip_creacion"           => $ip_cliente,
                        "ip_modificacion"       => $ip_cliente,
                        "codigoref"             => $det_compra->codigoref,
                    ];

                    // dd($detalle, $data2, $det_compra);


                    // Ct_detalle_compra::create($detalle);
                    Ct_detalle_compra::create($data2);
                }
            } else {
                return ['status' => 'error', 'msj' => 'Ops.. Ocurrio un error'];
            }
            DB::commit();
            // $inventario_a = Inventario2::build_process('C', $id_compra, $impo->id_empresa, 0, 1);
            $inventario = CargaInicialController::importacionKardex($impo->id);
            //dd($inventario);
            if ($inventario["status"] != "error") {
                $status                 = "success";
            }

            if ($status == "success") {

                $impo->inventario      = 1;
                $impo->id_usuariomod   = $id_usuario;
                $impo->ip_modificacion = $ip_cliente;
                $impo->save();


                LogImportaciones::create([
                    "id_import_cab"   => $impo->id,
                    "id_compra"       => $id_compra,
                    'principal'       => 1,
                    "id_usuariocrea"  => $id_usuario,
                    "id_usuariomod"   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
                //DB::commit();
                return ['status' => $status, 'msj' => 'Guardado Correctamente', "id_asiento" => $id_asiento['id_asiento']];
            } else {
                DB::rollBack();
                return $inventario;
                // return ['status' => $status, 'msj' => $inventario['msj'], "exp" => $inventario["exp"]];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => $status, 'msj' => 'Opss.. Ocurrio un error', 'exp' => $e->getMessage()];
        }
    }

    public static function crearAsientoCompra($id)
    {

        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $cab_asiento = Ct_Imp_Asientos_Cabecera::find($id);
        //dd($cab_asiento->detalles);
        $cuandoCrea = [];
        try {
            $data = $cab_asiento["attributes"];
            unset($data["id"], $data["id_ant"], $data["created_at"], $data["updated_at"]);
            $data["id_usuariocrea"] = $id_usuario;
            $data["id_usuariomod"] = $id_usuario;
            $data["ip_creacion"] = $ip_cliente;
            $data["ip_modificacion"] = $ip_cliente;
            $data["estado"]          = 1;

            $id_asiento = Ct_Asientos_Cabecera::insertGetId($data);
            foreach ($cab_asiento->detalles as $det) {
                //dd($det);
                $data_detalle = [];
                $data_detalle = $det["attributes"];

                array_push($cuandoCrea, $data_detalle["id_plan_cuenta"]);
                array_push($cuandoCrea, $data_detalle["descripcion"]);
                unset($data_detalle['id'], $data_detalle["created_at"], $data_detalle["updated_at"]);

                $data_detalle["id_asiento_cabecera"] = $id_asiento;
                $data_detalle["id_usuariocrea"] = $id_usuario;
                $data_detalle["id_usuariomod"] = $id_usuario;
                $data_detalle["ip_creacion"] = $ip_cliente;
                $data_detalle["ip_modificacion"] = $ip_cliente;
                Ct_Asientos_Detalle::create($data_detalle);
            }
            // dd($cuandoCrea, $id_asiento);
            // DB::commit();
            return ["status" => "success", "msj" => "Guardado correctamente", "id_asiento" => $id_asiento];
        } catch (\Exception $e) {
            // DB::rollBack();
            return ['status' => "error", 'msj' => $e->getMessage()];
        }
    }

    private function doSearchingQuery($constraints)
    {
        $id_empresa = Session::get('id_empresa');
        //dd($constraints);
        $query = Ct_Importaciones_Cab::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                if ($fields[$index] == "id" || $fields[$index] == "id_asiento") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->orderBy('id', 'desc')->where('estado', 1)->where('id_empresa', $id_empresa)->paginate(20);
    }

    public static function recalcularImportaciones($id)
    {
        DB::beginTransaction();
        try {


            $log = LogImportaciones::where('id_import_cab', $id)->where("principal", 2)->first();
            $resp = [];
            $id_ct_compra = "";
            $id_importacion_compra = "";
            if (is_null($log)) { //Si no existe la factura maestra se crea la factura
                $resp =  ImportacionesController::crearFacturaMaestra($id);
                if ($resp["status"] == "error") {
                    return ["status" => "success", "msj" => $resp["msj"]];
                } else {
                    $id_importacion_compra = $resp["id_importacion_compra"];
                }
            } else {
                $id_importacion_compra = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first()->id_compra;
            }


            //Si la factura esta creada comienza a calcular con los gastos
            //$principal = Ct_Importaciones_Compras::find($id_ct_compra);
            $secundaria = Ct_Importaciones_Compras::find($id_importacion_compra);
            $total_cabecera = $secundaria->total_original;
            $f_otros_gastos = ImportacionesController::otrosGastos($id);
            $f_total_gastos = ImportacionesController::totalGastos($id);

            $otros_gastos = $f_otros_gastos["otros_gastos"];
            $total_gastos = $f_total_gastos["total_gastos"];
            $total_egre   = $f_total_gastos["egre_varios"];


            $total_gastos = $total_gastos + $total_egre + $otros_gastos;

            // $verImportacion = Ct_Importaciones_Cab::find($id);
            // if ($verImportacion->inventario == 1) {
            //     return ["status" => "success", "id_ct_compra" => $id, "otros_gastos" => $otros_gastos, 'total_gastos' => $total_gastos, "egre_varios" => $total_egre];
            // }

            //variables para el calculo
            $prct_item = 0;
            $costo_asignado_total = 0;
            $costo_asignado_unidad = 0;
            $costo_unitario = 0;
            $total_costo_calculado = 0;
            $validacion = 1;
            $vuelta = 0;
            //    do{
            foreach ($secundaria->detalles as $details) {
                $subtotal_recalculado = $details->precio * $details->cantidad;
                // $prct_item = ($subtotal_recalculado / $total_cabecera);
                $prct_item = ($subtotal_recalculado / $total_cabecera);
                $costo_asignado_total = $prct_item * $total_gastos;
                $costo_asignado_unidad = $costo_asignado_total / $details->cantidad;
                $costo_unitario = $details->precio + $costo_asignado_unidad;
                $costo_total = $details->cantidad *  $costo_unitario;
                // $costo_total2 = $details->cantidad * round($costo_unitario, 2);
                // dd($costo_total, $costo_total2);

                // if($costo_total >  $costo_total2){
                //     $costo_total2 = $details->cantidad * (round($costo_unitario, 2) + 0.01);
                //     $costo_total = $costo_total2;
                //     $costo_asignado_unidad = (round($costo_asignado_unidad, 2) + 0.01);
                //     $costo_unitario = (round($costo_unitario, 2) + 0.01);
                // }
                $details->total                 = $subtotal_recalculado;
                $details->prct_item             = ($prct_item * 100);
                $details->costo_asignado_total  = $costo_asignado_total;
                $details->costo_asignado_unidad = $costo_asignado_unidad;
                $details->costo_unitario        = $costo_unitario;
                $details->costo_total           = $costo_total;
                $total_costo_calculado         += $costo_total;
                $details->save();
            }
            // dd($costo_asignado_total);

            // }while($validacion != 0);

            //dd($secundaria->total_final, $total_costo_calculado);

            //dd(Ct_Asientos_Cabecera::find($secundaria->id_asiento_cabecera));
            $secundaria->total_final        = $total_costo_calculado;
            $secundaria->subtotal_0         = $total_costo_calculado;
            $secundaria->subtotal           = $total_costo_calculado;
            $secundaria->valor_contable     = $total_costo_calculado;
            $secundaria->save();
            DB::commit();

            $var = ImportacionesController::asientoReverso(["id_asiento" => $secundaria->id_asiento_cabecera], ["total" => $total_costo_calculado], "R");
            // dd($var);

            return ["status" => "success", "id_ct_compra" => $id, "otros_gastos" => $otros_gastos, 'total_gastos' => $total_gastos, "egre_varios" => $total_egre];
        } catch (\Exception $e) {
            DB::commit();
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }

    public static function otrosGastos($id)
    {
        $otros_gastos = 0;

        $cab_pedido = Ct_Importaciones_Cab::find($id);
        //return $cab_pedido;
        if ($cab_pedido->agrupada == 1) {
            $cab_pedido = Ct_Importaciones_Cab::where('id_cab_imp', $cab_pedido->id)->get();
            foreach ($cab_pedido as $pedido) {
                foreach ($pedido->detalles as $detalles) {
                    if ($detalles->productos->codigo == "TRANS") {
                        $otros_gastos += $detalles->subtotal;
                    }
                }
            }
        } else {
            foreach ($cab_pedido->detalles as $details) {
                if ($details->productos->codigo == "TRANS") {
                    $otros_gastos += $details->subtotal;
                }
            }
        }

        return ["otros_gastos" => $otros_gastos];
    }

    public static function totalGastos($id)
    {
        $gastos = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->where('tipo', '<>', 3)->get();
        $total_gastos = 0;

        if (count($gastos) > 0) {
            foreach ($gastos as $gasto) {
                if (isset($gasto->ct_compra)) {
                    $total_gastos += $gasto->ct_compra->subtotal;
                }
            }
        }

        $comp_egreso = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->get();
        $sum_egr = 0;

        foreach ($comp_egreso as $egr) {
            foreach ($egr->detalles as $details) {
                $sum_egr = $details->tipo_liq == 2 ? $sum_egr + $details->debe : 0 + $sum_egr;
            }
        }


        return ["total_gastos" => $total_gastos, "egre_varios" => $sum_egr];
    }

    public static function crearFacturaMaestra($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $cab = Ct_Importaciones_Cab::find($id);
            // return $cab;
            $total_detalle = 0;
            $asiento = ImportacionesController::asientoReverso(["id_cab" => $cab->id], [], "C");
            //dd($asiento);
            if ($asiento["status"] == "error") {
                DB::rollBack();
                return ["status" => "error", "msj" => $asiento["msj"]];
            }

            $id_proveedor = '0985465620001';
            $fecha = date("Y-m-d");
            if ($cab->agrupada == 0) {
                $id_proveedor = $cab->id_proveedor;
                $fecha = $cab->fecha;
            }

            $imp_compras = [
                'tipo'                => 10,
                'id_asiento_cabecera' => $asiento["id_asiento"],
                'observacion'         => "{$cab->observacion}",
                'fecha'               => $fecha,
                'f_autorizacion'      => $fecha,
                'secuencia_factura'   => "001-002-000000",
                'numero'              => "IMPORTACION",
                'sucursal'            => "001",
                'punto_emision'       => "002",
                'serie'               => "001-002",
                'direccion_proveedor' => "GUAYAQUIL",
                'estado'              => -5,
                'tipo_comprobante'    => '01',
                'proveedor'           => $id_proveedor,
                'id_empresa'          => $cab->id_empresa,
                'subtotal_0'          => 0,
                'subtotal'            => 0,
                'total_final'         => 0,
                'valor_contable'      => 0,
                'subtotal_12'         => 0,
                'id_usuariocrea'      => $cab->id_usuariocrea,
                'id_usuariomod'       => $cab->id_usuariomod,
                'ip_creacion'         => $cab->ip_creacion,
                'ip_modificacion'     => $cab->ip_modificacion,
            ];
            $id_importacion_compra  = Ct_Importaciones_Compras::insertGetId($imp_compras);

            if ($cab->agrupada == 1) {
                $cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
                foreach ($cab as $cabecera) {
                    $logImportaciones =  LogImportaciones::where('id_import_cab', $cabecera->id)->first();
                    //  dd($logImportaciones);
                    $compra = Ct_compras::find($logImportaciones->id_compra);
                    foreach ($compra->detalles as $det) {
                        if ($det->codigo != "TRANS") {
                            $atributos = $det['attributes'];
                            unset($atributos['id'], $atributos['id_ct_compras']);

                            //$atributos["id_ct_compras"] = $id_compra;
                            $total_detalle += $atributos['total'];
                            //Ct_Importaciones_Detalle_Compra::create($atributos);

                            $atributos["id_ct_compras"] = $id_importacion_compra;
                            Ct_Importaciones_Detalle_Compra::create($atributos);
                        }
                    }
                }
            } else {
                $logImportaciones =  LogImportaciones::where('id_import_cab', $cab->id)->first();
                $compra = Ct_compras::find($logImportaciones->id_compra);

                foreach ($compra->detalles as $det) {
                    if ($det->codigo != "TRANS") {
                        $atributos = $det['attributes']; //Se guarda en un arreglo los detalles de los pedidos para almacenarlos en la factura maestra
                        //guaradar en Ct_detalle_compra
                        unset($atributos['id'], $atributos['id_ct_compras']);

                        //$atributos["id_ct_compras"] = $id_compra;
                        $total_detalle += $atributos['total'];
                        //Ct_Importaciones_Detalle_Compra::create($atributos);

                        //guardar en Ct_detalle_compra

                        $atributos["id_ct_compras"] = $id_importacion_compra;
                        Ct_Importaciones_Detalle_Compra::create($atributos);
                    }
                }
            }

            LogImportaciones::create([
                "id_import_cab"   => $id,
                "id_compra"       => $id_importacion_compra,
                'principal'       => 2,
                "id_usuariocrea"  => $id_usuario,
                "id_usuariomod"   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            DB::commit();

            // $compra_maestra = Ct_Importaciones_Compras::find($id_compra);

            // $compra_maestra->total_final = $total_detalle;
            // $compra_maestra->subtotal_0 = $total_detalle;
            // $compra_maestra->subtotal = $total_detalle;
            // $compra_maestra->valor_contable = $total_detalle;
            // $compra_maestra->save();

            $compra_maestra2 = Ct_Importaciones_Compras::find($id_importacion_compra);
            //dd($compra_maestra2);
            $compra_maestra2->total_final = $total_detalle;
            $compra_maestra2->subtotal_0 = $total_detalle;
            $compra_maestra2->subtotal = $total_detalle;
            $compra_maestra2->valor_contable = $total_detalle;
            $compra_maestra2->total_original = $total_detalle;
            $compra_maestra2->save();

            return ["status" => "success", "id_importacion_compra" => $id_importacion_compra];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "No se pudo crear la factura maestra {$e->getMessage()}", "exeption", $e->getMessage()];
        }
    }

    public static function asientoReverso($ids, $arrValores, $tipo = "R")
    {
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        // dd($ids, $arrValores, $tipo);
        if ($tipo == "R") {
            $asiento = Ct_Imp_Asientos_Cabecera::find($ids["id_asiento"]);
            $asiento->valor = $arrValores["total"];
            $asiento->save();

            foreach ($asiento->detalles as $detalles) {
                if ($detalles->debe > 0) {
                    $detalles->debe = $arrValores["total"];
                    $detalles->save();
                } else {
                    $detalles->haber = $arrValores["total"];
                    $detalles->save();
                }
            }
        } else {
            DB::beginTransaction();
            try {

                $cab = Ct_Importaciones_Cab::find($ids["id_cab"]);
                $observacion = "";
                if ($cab->agrupada == 1) {
                    $observacion = "REVERSO DE LA IMPORTACION AGRUPADA # {$cab->id}";
                } else {
                    $observacion = "REVERSO DE LA IMPORTACION # {$cab->id}";
                }
                $id_asiento = Ct_Imp_Asientos_Cabecera::insertGetId([
                    'estado'          => 10,
                    'sucursal'        => "001",
                    'punto_emision'   => "002",
                    'fecha_asiento'   => $cab->fecha,
                    'fact_numero'     => '',
                    'id_empresa'      => $id_empresa,
                    'observacion'     => "IMPORTACION # {$observacion}",
                    'valor'           => 0,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);

                $globales = Ct_Globales::where('id_modulo', 16)->where('id_empresa', $id_empresa)->first();
                if (is_null($globales)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No esta creado los asientos para el reverso del Kardex"];
                }

                Ct_Imp_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $globales->debe,
                    'descripcion'         => $globales->debec->nombre,
                    'fecha'               => $cab->fecha,
                    'debe'                => 1,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);

                Ct_Imp_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $globales->haber,
                    'descripcion'         => $globales->haberc->nombre,
                    'fecha'               => $cab->fecha,
                    'haber'               => 1,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);

                DB::commit();
                return ["status" => "success", "msj" => "Asiento creado correctamente", "id_asiento" => $id_asiento];
            } catch (\Exception $e) {
                DB::rollback();
                return ["status" => "error", "msj" => $e->getMessage()];
            }
        }
    }

    public function buscarProveedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where(function($query) use ($request) {
                $query->where('nombrecomercial', 'LIKE', "%{$request['search']}%")
                      ->orWhere('razonsocial', 'LIKE', "%{$request['search']}%");
            })
            ->where('estado', 1)
            ->select('id as id', 'razonsocial as text', 'direccion as direccion')
            ->take(10)
            ->get();
        }

        return response()->json($proveedor);
    }



    public function buscarProductos(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $producto = [];

        if (!is_null($request["search"])) {
            $producto1 = Ct_productos::where('estado_tabla', 1)
                ->where('codigo', "LIKE", "%{$request['search']}%")
                ->where('id_empresa', $id_empresa)
                ->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));
            $producto2 = Ct_productos::where('estado_tabla', 1)
                ->where('nombre', 'LIKE', "%{$request['search']}%")
                ->where('id_empresa', $id_empresa)
                ->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));
            $producto = $producto1->union($producto2)->take(20)->get();
        }
        return response()->json($producto);
    }

    public function buscarDireccion(Request $request)
    {
        $proveedor = '';
        if (!is_null($request->proveedor)) {
            $proveedor = Proveedor::where('id', $request->proveedor)->first();
            return ['status' => 'success', 'id' => $proveedor->id, 'direccion' => strtoupper($proveedor->direccion)];
        }

        return ["status" => "error"];
    }

    public function buscarPais(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        $pais = '';
        if (!is_null($request["search"])) {
            $pais = Pais::where('nombre', 'LIKE', "%" . strtoupper($request['search']) . "%")->select('id as id', 'nombre as text')->get();
        }
        //  dd($pais)
        return response()->json($pais);
    }

    public function buscarPrecioBodega(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        $producto = Ct_productos::where('codigo', $request->cod_producto)->where('id_empresa', $id_empresa)->first();
        //dd($producto);
        $precio = ImportacionesController::stock($producto->id, $request->bodega, $id_empresa);

        return $precio;
    }


    public static function stock($id, $bodega, $empresa)
    {
        //ingreso por compras
        $ingreso = Ct_Inv_Kardex::where('id_producto', $id)->where('estado', 1)->where('id_transaccion', 2)->where('id_bodega', $bodega)->with(['inventario' => function ($query) use ($empresa) {
            $query->where('id_empresa', $empresa)->where('estado', 1);
        }])->get();
        //sum('cantidad');
        $option = "";
        foreach ($ingreso as $value) {
            //dd($value->precio);
            $option .= "<option value='{$value->precio}'>{$value->precio}</option>";
        }

        return $option;
    }

    public function pre_orden(Request $request)
    {

        $proveedor  = [];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //Crear la bodega de importacion
        ImportacionesController::createBodegaImportacion($id_empresa);
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $productos = [];
        $pais = [];
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $term         = Ct_Termino::where('estado', '1')->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable/importaciones/create_pre_importacion', ['empresa' => $empresa, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 'pais' => $pais, 'sucursales' => $sucursales, 'grupos' => $grupos, 'tipos' => $tipos, 'marcas' => $marcas, 'sub_tipos' => $sub_tipos, 'af_colores' => $af_colores, 'af_series' => $af_series, 'af_responsables' => $af_responsables, 'term' => $term, 'min_date' => $min_date]);
    }

    public function preOrdenStore(Request $request)
    {
        //dd($request->all());

        $pais = Pais::where('id', $request->pais)->orWhere('nombre', strtoupper($request->pais))->first();
        $id_empresa = $request->session()->get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_det_imp = [];

        DB::beginTransaction();
        try {
            $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->f_autorizacion);
            $verifica_cierre = json_decode($verifica_cierre);
            if ($verifica_cierre->status == 'error') {
                return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }


            if ($request->estado == "actualizar") {
                $log = Log_Pre_Orden_Imp::where('id_imp_cab', $request->orden_id)->get();


                foreach ($log as $value) {
                    if (!is_null($value->id_af_factura)) {
                        $activo = AfActivo::find($value->id_af_factura);
                        $arraf = [
                            'estado'               => 0,
                            'id_usuariomod'        => $id_usuario,
                            'ip_modificacion'      => $ip_cliente,
                        ];

                        $activo->update($arraf);
                    }

                    $cabecera = Ct_Importaciones_Cab::find($value->id_imp_cab);

                    $arr_up = [
                        'estado'               => 0,
                        'id_usuariomod'        => $id_usuario,
                        'ip_modificacion'         => $ip_cliente,
                    ];

                    $value->update($arr_up);
                    $cabecera->update($arr_up);
                }
            }


            $id_cabecera = Ct_Importaciones_Cab::insertGetId([
                // 'sucursal'              => $sucursal,
                // 'punto_emision'         => $punto_emision,
                // 'id_asiento_cabecera'   => $id_asiento,
                'id_proveedor'          => $request->proveedor,
                'fecha'                 => $request->f_autorizacion,
                'observacion'           => $request->observacion_2,
                'id_cliente'            => $request->id_empresa,
                'subtotal'              => $request->total1,
                'total'                 => $request->total1,
                'pais'                  => $pais->id,
                'pais_procedencia'      => $request->pais_procedencia,
                'secuencia_importacion' => $request->secuencia_importacion,
                'descuento'             => $request->descuento1,
                'estado'                => 2,
                'estado_imp'            => 1,
                'secuencia_factura'     => $request->secuencia_factura,
                'serie'                 => $request->serie,
                'id_empresa'            => $id_empresa,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
            ]);
            $retornar = [];
            for ($m = 0; $m < count($request->check_af); $m++) {
                if ($request->check_af[$m] == 1) {
                    $retornar = DocumentoFacturaController::store_AfActivo($request);
                    if ($retornar["respuesta"] == "error") {
                        DB::rollBack();
                        return ['respuesta' => 'error', 'msj' => "Error al guardar...", "exp" => $retornar['msj'], "mod" => $retornar["mod"]];
                    }
                    break;
                }
            }



            $check = 0;
            for ($i = 0; $i < count($request->producto); $i++) {
                $data = [
                    'id_cab'          => $id_cabecera,
                    'id_producto'     => $request->producto[$i],
                    'cantidad'        => $request->cantidad[$i],
                    'precio'          => $request->precio[$i],
                    'descuento'       => $request->desc[$i],
                    'subtotal'        => $request->precioneto[$i],
                    'peso'            => $request->peso[$i],
                    'id_bodega'       => $request->bodega[$i],
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'af'              => $request->check_af[$i],
                    'lote'            => $request->lote[$i],
                    'fecha_vencimiento' => $request->fecha_venc[$i]
                ];

                $request['fecha_compra'] = $request['f_autorizacion'];
                if ($request->check_af[$i] == 1) {
                    $data['id_af_activo'] = $retornar['AfActivo'][$check];
                    $check++;
                }
                Ct_Importaciones_Det::create($data);
            }


            Log_Pre_Orden_Imp::create([
                'id_imp_cab'        => $id_cabecera,
                'estado'            => 1,
                'id_empresa'        => $id_empresa,
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                "ip_modificacion"   => $ip_cliente
            ]);

            if (isset($retornar['AfActivo'])) {
                # code...

                for ($m = 0; $m < count($retornar['AfActivo']); $m++) {
                    // dd("aqui");
                    Log_Pre_Orden_Imp::create([
                        'id_imp_cab'        => $id_cabecera,
                        'id_af_factura'     => $retornar['AfActivo'][$m],
                        'estado'            => 1,
                        'id_empresa'        => $id_empresa,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        "ip_modificacion"   => $ip_cliente
                    ]);
                }
            }

            Log_Ordenes::create([
                "id_orden"          => $id_cabecera,
                "tipo"              => 2,
                'estado'            => 1,
                'id_empresa'        => $id_empresa,
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                "ip_modificacion"   => $ip_cliente
            ]);
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function preOrdenMostrar(Request $request)
    {
        $orden = Log_Pre_Orden_Imp::where("id", $request->id)->get();
        // dd($orden);
        $import_cab = Ct_Importaciones_Cab::find($orden[0]->id_imp_cab);

        $cab = [
            'id_proveedor'              => $import_cab->id_proveedor,
            'nombre_proveedor'          => isset($import_cab->proveedor_da) ? $import_cab->proveedor_da->nombrecomercial : '',
            'id_pais'                   => $import_cab->pais,
            'nombre_pais'               => isset($import_cab->paises) ? $import_cab->paises->nombre : '',
            'pais_procedencia'          => $import_cab->pais_procedencia,
            'serie'                     => $import_cab->serie,
            'secuencia'                 => $import_cab->secuencia_factura,
            'secuencia_importacion'     => $import_cab->secuencia_importacion,
            'observacion'               => $import_cab->observacion,
            'subtotal'                  => $import_cab->subtotal,
            'descuento'                 => $import_cab->descuento,
            'total'                     => $import_cab->total,
        ];
        $cab["detalles"] = [];
        foreach ($import_cab->detalles as $value) {


            // $cab["detalles"] = $details;
            $details = [
                "id_producto"           => $value->id_producto,
                "nombre_producto"       => isset($value->productos) ? "{$value->productos->codigo} | {$value->productos->nombre}" : '',
                "cantidad"              => $value->cantidad,
                "precio"                => $value->precio,
                "peso"                  => $value->peso,
                "porct_descuento"       => $value->porct_descuento,
                "descuento"             => $value->descuento,
                "precio_neto"           => $value->subtotal,
                'id_bodega'             => $value->id_bodega,
                "af"                    => $value->af,
                "lote"                  => $value->lote,
                "fecha_venc"            => date('Y-m-d', strtotime($value->fecha_vencimiento)),
                "activo_fijo"           => []
            ];
            if (!is_null($value->id_af_activo)) {
                $af_activo = AfActivo::find($value->id_af_activo);
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


    public function mostrar(Request $request)
    {
        // dd($request->all());
        $options = "";
        $precios = Producto_Precio_Aprobado::where('id_producto', $request->id_producto)->where("estado", 1)->get();

        foreach ($precios as $value) {
            $selected = "";
            if ($value->importante == 1) {
                $selected = "selected";
            }
            $options .= "<option {$selected} value='{$value->precio}'>{$value->precio}</option>";
        }
        if ($precios == "") {
            $options = "<option selected >No tiene precio...</option>";
        }

        return ["option" => $options];
    }

    public function kardex_resumen($id)
    {

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $otros_gastos = ImportacionesController::recalcularImportaciones($id);

        $logImportaciones = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first();
        $compras = Ct_Importaciones_Compras::find($logImportaciones->id_compra);

        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }

        //dd($ct_importaciones_cab);

        return view('contable/importaciones/kardex_resumen', ['id' => $id, 'ct_importaciones_cab' => $ct_importaciones_cab, 'empresa' => $empresa, 'compras' => $compras]);
    }

    public function edit_preorden($id)
    {

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $pre_orden = Ct_Importaciones_Cab::find($id);
        $log_pre = Log_Pre_Orden_Imp::where('id_imp_cab', $id)->first();

        $grupos       = AfGrupo::where('estado', '!=', 0)->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable/importaciones/edit_pre_importacion', ['pre_orden' => $pre_orden, 'empresa' => $empresa, 'tipos' => $tipos, 'af_responsables' => $af_responsables, 'af_colores' => $af_colores, 'af_responsables' => $af_responsables, 'af_series' => $af_series, 'marcas' => $marcas, 'grupos' => $grupos, 'bodega' => $bodega, 'id' => $id, 'log_pre' => $log_pre, 'min_date' => $min_date]);
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
        //$ordenes = Ct_Importaciones_Cab::where('estado', '2')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();
        $ordenes = array();
        $ordenes = Ct_Importaciones_Cab::where('ct_importaciones_cab.estado', '2')->where('ct_importaciones_cab.id_empresa', $id_empresa);

        if (!is_null($request['id']) or $request['id'] != "") {
            $ordenes = $ordenes->where('id', $request['id']);
        }

        if (!is_null($request['proveedor']) or $request['proveedor'] != "") {
            $ordenes = $ordenes->join('proveedor as prov', 'prov.id', 'ct_importaciones_cab.id_proveedor')->where('prov.nombrecomercial', 'like', '%' . $request['proveedor'] . '%');
        }

        if (!is_null($request['concepto']) or $request['concepto'] != "") {
            $ordenes = $ordenes->where('observacion', 'like', '%' . $request['concepto'] . '%');
        }

        $ordenes = $ordenes->get();
        //dd($request->all(), $id_empresa);

        return view('contable/importaciones/index_pre_importacion', ['ordenes' => $ordenes, 'empresa' => $empresa]);
    }

    public function update_orden(Request $request)
    {
        //dd($request->all());
        $id_empresa = Session::get('id_empresa');
        $pais = Pais::where('id', $request->pais)->orWhere('nombre', strtoupper($request->pais))->first();
        $empresa = Empresa::find($id_empresa);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $id_orden = $request->orden_id;
            $orden = Ct_Importaciones_Cab::find($id_orden);

            if (!is_null($orden)) {
                $cab = [
                    'id_proveedor'          => $request->proveedor,
                    'fecha'                 => $request->f_autorizacion,
                    'observacion'           => $request->observacion_2,
                    'id_cliente'            => $request->id_empresa,
                    'subtotal'              => $request->total1,
                    'total'                 => $request->total1,
                    'pais'                  => $pais->id,
                    'pais_procedencia'      => $request->pais_procedencia,
                    'secuencia_importacion' => $request->secuencia_importacion,
                    'descuento'             => $request->descuento1,
                    'estado'                => 2,
                    'estado_imp'            => 1,
                    'secuencia_factura'     => $request->secuencia_factura,
                    'serie'                 => $request->serie,
                    'id_usuariomod'         => $id_usuario,
                    'ip_modificacion'       => $ip_cliente,
                ];

                $orden->update($cab);

                // if (count($request['producto']) > 0) {
                //     $det = Ct_Importaciones_Det::where('id_cab', $orden->id);
                //     if (!is_null($det)) {
                //         $det->delete();
                //     }

                //     for ($i = 0; $i < count($request->producto); $i++) {
                //         $data = [
                //             'id_cab'          => $orden->id_cab,
                //             'id_producto'     => $request->producto[$i],
                //             'cantidad'        => $request->cantidad[$i],
                //             'precio'          => $request->precio[$i],
                //             'descuento'       => $request->desc[$i],
                //             'subtotal'        => $request->precioneto[$i],
                //             'peso'            => $request->peso[$i],
                //             'id_bodega'       => $request->bodega[$i],
                //             'id_usuariocrea'  => $id_usuario,
                //             'id_usuariomod'   => $id_usuario,
                //             'ip_creacion'     => $ip_cliente,
                //             'ip_modificacion' => $ip_cliente,
                //             'af'              => $request->check_af[$i],
                //             'lote'            => $request->lote[$i],
                //             'fecha_vencimiento' => $request->fecha_venc[$i]
                //         ];

                //         // $request['fecha_compra'] = $request['f_autorizacion'];
                //         // if ($request->check_af[$i] == 1) {
                //         //     $data['id_af_activo'] = $retornar['AfActivo'][$check];
                //         //     $check++;
                //         // }
                //         Ct_Importaciones_Det::create($data);
                //     }
                // }
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function aprobar_orden($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $orden = Ct_Importaciones_Cab::find($id);

            if (!is_null($orden)) {
                $arr = [
                    'proceso'          => 1,
                    'id_usuariomod'    => $id_usuario,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $orden->update($arr);
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function eliminar_orden($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $orden = Ct_Importaciones_Cab::find($id);

            if (!is_null($orden)) {
                $arr = [
                    'estado'          => 0,
                    'id_usuariomod'    => $id_usuario,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $orden->update($arr);
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Eliminado'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function info_fecha_lote($id)
    {

        $id_empresa = Session::get('id_empresa');

        $empresa = Empresa::find($id_empresa);

        ImportacionesController::recalcularImportaciones($id);

        $log = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first();

        $cabecera = Ct_Importaciones_Compras::find($log->id_compra);

        $imp_cabecera = Ct_Importaciones_Cab::find($id);



        return view('contable/importaciones/fecha_lote', ['cabecera' => $cabecera, 'empresa' => $empresa, 'imp_cabecera' => $imp_cabecera]);
    }

    public function guardar_fecha_lote(Request $request)
    {
        $id_cab = "";
        $data_validacion = [];
        $data_ids = [];
        $msj = "";

        $det_compras = Ct_Importaciones_Detalle_Compra::find($request["id"][0]);
        if (is_null($det_compras)) {
            return ["respuesta" => "error", "msj" => "No seleccione ningun producto"];
        }
        $id_compras = $det_compras->id_ct_compras;
        $det_compras = Ct_Importaciones_Detalle_Compra::where('id_ct_compras', $id_compras)->get();
        DB::beginTransaction();
        try {
            for ($m = 0; $m < count($request->id); $m++) {
                if (isset($data_validacion[$request["id"][$m]])) {
                    $data_validacion[$request["id"][$m]] += intval($request["cantidad"][$m]);
                    $data_ids[$request["id"][$m]] += 1;
                } else {
                    $data_validacion[$request["id"][$m]] = intval($request["cantidad"][$m]);
                    $data_ids[$request["id"][$m]] = 1;
                }
            }
            foreach ($det_compras as $det) {
                $val_cant = 0;
                foreach ($data_validacion as $key => $value) {
                    if ($key == $det->id) {
                        $val_cant = $value;
                    }
                }
                if ($val_cant == 0) {
                    $msj .= "La cantidad de producto <b>" . $det->codigo . "</b> no es correcta. <br>";
                } else if ($det->cantidad < $val_cant) {
                    $msj .= "La cantidad de producto <b>" . $det->codigo . "</b> no puede ser mayor a la cantidad de la compra. <br>";
                } else if ($det->cantidad > $val_cant) {
                    $msj .= "La cantidad de producto <b>" . $det->codigo . "</b> no puede ser menor a la cantidad de la compra. <br>";
                }
            }

            // if ($msj != "") {
            //     return ['respuesta' => 'error', 'msj' => $msj];
            // }

            $arrDetalle = [];
            for ($x = 0; $x < count($request->id); $x++) {

                $det = Ct_Importaciones_Detalle_Compra::find($request["id"][$x]);
                $descuento_new = $det->descuento / $data_ids[$det->id];
                $descuento_porct_new = $det->descuento_porcentaje / $data_ids[$det->id];
                $extendido_new = $det->extendido / $data_ids[$det->id];
                $porcentaje_new = $det->porcentaje / $data_ids[$det->id];
                $total_new = $det->total / $data_ids[$det->id];

                $data = [
                    "id_ct_compras"         => $det->id_ct_compras,
                    "detalle"               => $det->detalle,
                    "codigo"                => $det->codigo,
                    "nombre"                => $det->nombre,
                    "bodega"                => $det->bodega,
                    'cantidad'              => $request["cantidad"][$x],
                    "empaque"               => $det->empaque,
                    "total"                 => $total_new,
                    "precio"                => $det->precio,
                    "descuento_porcentaje"  => $descuento_porct_new,
                    "descuento"             => $descuento_new,
                    "extendido"             => $extendido_new,
                    "iva"                   => 0,
                    "porcentaje"            => $porcentaje_new,
                    "ice"                   => $det->ice,
                    "estado"                => $det->estado,
                    "id_usuariocrea"        => $det->id_usuariocrea,
                    "id_usuariomod"         => Auth::user()->id,
                    "ip_creacion"           => $det->ip_creacion,
                    "ip_modificacion"       => $det->ip_modificacion,
                    "codigoref"             => $det->codigoref,
                    "id_gasto"              => $det->id_gasto,
                    "peso_kg"               => $det->peso_kg,
                    "lote"                  => $request["lote"][$x],
                    "fecha_exp"             => $request["fecha_venc"][$x],
                ];
                array_push($arrDetalle, $data);
            }
            for ($p = 0; $p < count($request["id"]); $p++) {
                try {
                    $deleted = DB::table('ct_importaciones_detalle_compra')->where('id', $request["id"][$p])->delete();
                } catch (\Exception $e) {
                }
            }

            for ($w = 0; $w < count($arrDetalle); $w++) {
                Ct_Importaciones_Detalle_Compra::create($arrDetalle[$w]);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function busca_asientos_costo()
    {
        $id_empresa = Session::get('id_empresa');
        $activas = Venta_Kardex::join('ct_ventas as cv', 'cv.id', 'venta_kardex.id_venta')
            ->join('inv_cab_movimientos as icb', 'icb.id', 'venta_kardex.id_cab_moviento')
            ->where('cv.estado', '1')
            ->where('cv.id_empresa', $id_empresa)
            ->groupBy('cv.id')
            ->select('cv.id as id_ventas', 'venta_kardex.id_cab_moviento', 'cv.nro_comprobante', 'cv.id_asiento', 'cv.created_at as fecha_crea_venta', 'icb.created_at as fecha_crea_mov')
            ->get();

        $arr_asientos = [];
        $arr_suma = [];
        $tr = "";
        $tr2 = "";

        foreach ($activas as $value) {
            $suma_asiento = $value->id_asiento + 2;
            $cabecera_asiento = Ct_Asientos_Cabecera::where('id', $suma_asiento)->where('modulo', 'like', '%COSTO_PROCEDIMIENTO%')->where('observacion', 'like', '%' . $value->nro_comprobante . '%')->first();
            if (is_null($cabecera_asiento)) {
                $suma_asiento = $value->id_asiento + 1;
                $cabecera_asiento = Ct_Asientos_Cabecera::where('id', $suma_asiento)->where('modulo', 'like', '%COSTO_PROCEDIMIENTO%')->where('observacion', 'like', '%' . $value->nro_comprobante . '%')->first();
            }

            $asientos = Ct_Asientos_Cabecera::where('modulo', 'like', '%COSTO_PROCEDIMIENTO%')->where('observacion', 'like', '%' . $value->nro_comprobante . '%')->get();
            foreach ($asientos as $asiento) {
                $tr2 .= "<tr>
                    <td>{$value->id_ventas}</td>
                    <td>{$value->nro_comprobante}</td>
                    <td>{$asiento->id}</td>
                    <td>{$asiento->observacion}</td>
                    <td>{$asiento->valor}</td>
                </tr>";
            }

            $table2  = "<table>
                    <thead>
                        <tr>
                        <th>Id Venta</th>
                        <th>Nro Comprobante</th>
                        <th>Id Asiento</th>
                        <th>Observacion</th>
                        <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tr2}
                    </tbody>
                </table>";

            $tr .= "<tr>
                <td>{$value->id_ventas}</td>
                <td>{$value->id_cab_moviento}</td>
                <td>{$value->nro_comprobante}</td>
                <td>{$suma_asiento}</td>";

            if (!is_null($cabecera_asiento)) {

                $tr .= "<td>{$cabecera_asiento->observacion}</td>";
            } else {
                $tr .= "<td></td>";
            }
            $tr .= "<td>{$value->fecha_crea_venta}</td>
                <td>{$value->fecha_crea_mov}</td>
            </tr>";

            $table = "<table>
                <thead>
                    <tr>
                        <th>Id Venta</th>
                        <th>Id Cab Movimiento</th>
                        <th>Nro Comprobante</th>
                        <th>Suma asiento</th>
                        <th>Observacion Suma</th>
                        <th>Fecha crea venta</th>
                        <th>Fecha crea movimiento</th>
                    </tr>
                </thead>
                <tbody>
                    {$tr}
                </tbody>
            </table>";
        }

        return $table;
    }

    public function new_orden_index(Request $request)
    {
        $empresa = Empresa::find(Session::get('id_empresa'));
        $ordenes = Ct_Imp_Orden_Vendedor::where('id_empresa', $empresa->id)->where('estado', '!=', 0);
        if (!is_null($request->observacion)) {
            $ordenes = $ordenes->where('observacion', 'like', "%{$request->observacion}%");
        }
        $ordenes = $ordenes->orderBy('id','DESC')->get();
        $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();


        return view('contable/importaciones/new_orden_index', ['empresa' => $empresa, 'ordenes' => $ordenes, 'permiso_aprobado' => $permiso_aprobado]);
    }

    public function new_orden_store(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_usuario = $_SERVER['REMOTE_ADDR'];

        DB::beginTransaction();
        try {
            $arr = [
                'observacion'     => "",
                'id_empresa'      => $id_empresa,
                'estado'          => 1,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_usuario,
                'ip_modificacion' => $ip_usuario
            ];

            $id_orden = Ct_Imp_Orden_Vendedor::insertGetId($arr);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_orden' => $id_orden];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function new_orden_edit($id)
    {
        $empresa = Empresa::find(Session::get('id_empresa'));
        $orden = Ct_Imp_Orden_Vendedor::find($id);
        $detalle = ImportacionesController::tabla_detalles($orden->id);

        return view('contable/importaciones/new_orden_edit', ['empresa' => $empresa, 'orden' => $orden, 'detalle' => $detalle]);
    }

    public function BuscadorProductos(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $producto = [];

        $producto1 = Ct_productos::where('ct_productos.estado_tabla', 1)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'ct_productos.id')
            ->join('producto as p', 'p.id', 'pi.id_insumo')
            ->where('ct_productos.codigo', "LIKE", "%{$request['search']}%")
            ->where('ct_productos.id_empresa', $id_empresa)
            ->select('ct_productos.id as id', DB::raw('CONCAT(ct_productos.codigo," | ",ct_productos.nombre) as text'), 'p.registro_sanitario', 'ct_productos.codigo', 'ct_productos.nombre');
        $producto2 = Ct_productos::where('ct_productos.estado_tabla', 1)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'ct_productos.id')
            ->join('producto as p', 'p.id', 'pi.id_insumo')
            ->where('ct_productos.nombre', 'LIKE', "%{$request['search']}%")
            ->where('ct_productos.id_empresa', $id_empresa)
            ->select('ct_productos.id as id', DB::raw('CONCAT(ct_productos.codigo," | ",ct_productos.nombre) as text'), 'p.registro_sanitario', 'ct_productos.codigo', 'ct_productos.nombre');
        $producto = $producto1->union($producto2)->take(20)->get();

        return $producto;
    }

    public function guardar_detalles(Request $request)
    {
        DB::beginTransaction();

        try {

            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $id_cab = $request["id_orden"];
            $arr_up = [
                'id_orden' => $id_cab,
                'id_producto' => $request["id_producto"],
                'id_usuariocrea' => $id_usuario,
                'id_usuariomod' => $id_usuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            Ct_Imp_Orden_Vendedor_Detalle::insert($arr_up);
            DB::commit();
            $table = ImportacionesController::tabla_detalles($id_cab);

            return ["status" => "success", "msj" => "Guardado con exito", "table" => $table["table"]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public static function tabla_detalles($id_cab)
    {
        $detalles = Ct_Imp_Orden_Vendedor_Detalle::where('id_orden', $id_cab)->where('estado', 1)->get();
        $tr = "";
        $registro_sanitario = "";

        foreach ($detalles as $detalle) {
            $producto_insumo = Ct_productos_insumos::where('id_producto', $detalle->id_producto)->join('producto as p', 'p.id', 'ct_productos_insumos.id_insumo')->select('p.*')->first();
            if (!is_null($producto_insumo)) {
                $registro_sanitario = $producto_insumo->registro_sanitario;
            }

            $tr .= "<tr id='fila_{$detalle->id}'>
                <td class='text-left'>{$detalle->producto->codigo}</td>
                <td class='text-left'>{$detalle->producto->nombre}</td>
                <td class='text-left'>{$registro_sanitario}</td>
                <td>
                <input onchange='editar_fila({$detalle->id})' type='number' class='form-control' id='cantidad_{$detalle->id}' name='cantidad_{$detalle->id}' value='{$detalle->cantidad}'>
                </td>
                <td style='display:flex; gap:3px; flex-wrap:wrap;'> 
                <button class='btn-general btn-eliminar' onclick='eliminarDetalle({$detalle->id})'>
                <i class='fa fa-trash' aria-hidden='true'></i>
                </button> 
                </td>
                </tr>";
        }
        return ["table" => $tr];
    }

    public function actualizar_cabecera(Request $request)
    {
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $id_usuario = Auth::user()->id;

        $cabecera = Ct_Imp_Orden_Vendedor::find($request->id_orden);
        if (is_null($cabecera)) {
            return ["status" => "error", "msj" => "No se encontro la orden"];
        }

        if ($cabecera->estado == 2) {
            return ["status" => "error", "msj" => "No se puede editar, orden emitida"];
        }
        DB::beginTransaction();
        try {
            $cabecera->observacion = $request->observacion;
            $cabecera->id_usuariomod = $id_usuario;
            $cabecera->ip_modificacion = $ip_usuario;
            $cabecera->save();
            DB::commit();
            return ["status" => "success", "msj" => "Guardado con exito"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function eliminar_detalle(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $detalle = Ct_Imp_Orden_Vendedor_Detalle::find($request->id_detalle);
        DB::beginTransaction();
        try {
            $detalle->estado = 0;
            $detalle->id_usuariomod = $id_usuario;
            $detalle->ip_modificacion = $ip_usuario;
            $detalle->save();
            DB::commit();
            $table = ImportacionesController::tabla_detalles($detalle->id_orden);
            return ["status" => "success", "msj" => "Eliminado con exito", "table" => $table];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function editar_detalle(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $detalle = Ct_Imp_Orden_Vendedor_Detalle::find($request->id_detalle);
        $orden = Ct_Imp_Orden_Vendedor::find($request->id_orden);
        if ($orden->estado == 2) {
            return ["status" => "error", "msj" => "No se puede editar, orden emitida"];
        }

        DB::beginTransaction();
        try {
            $detalle->cantidad = $request->cantidad;
            $detalle->id_usuariomod = $id_usuario;
            $detalle->ip_modificacion = $ip_usuario;
            $detalle->save();
            DB::commit();
            $table = ImportacionesController::tabla_detalles($detalle->id_orden);
            return ["status" => "success", "msj" => "Editado con exito", "table" => $table['table']];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function AprobarOrden($id_orden)
    {
        $id_usuario = Auth::user()->id;
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $orden = Ct_Imp_Orden_Vendedor::find($id_orden);
        if ($orden->estado == 2) {
            return ["status" => "error", "msj" => "No se puede editar, orden emitida"];
        }
        DB::beginTransaction();
        try {
            $orden->estado = 2;
            $orden->id_usuariomod = $id_usuario;
            $orden->ip_modificacion = $ip_usuario;
            $orden->save();
            DB::commit();
            return ["status" => "success", "msj" => "Aprobado"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function EliminarOrden($id_orden)
    {
        $id_usuario = Auth::user()->id;
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $orden = Ct_Imp_Orden_Vendedor::find($id_orden);
        if ($orden->estado == 2) {
            return ["status" => "error", "msj" => "No se puede eliminar, orden emitida"];
        }
        DB::beginTransaction();
        try {
            $orden->estado = 0;
            $orden->id_usuariomod = $id_usuario;
            $orden->ip_modificacion = $ip_usuario;
            $orden->save();
            DB::commit();
            return ["status" => "success", "msj" => "Eliminado"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public function OrdenCargar(Request $request)
    {
        $id_orden = $request->id_orden;
        $orden = Ct_Imp_Orden_Vendedor::find($id_orden);
        $detalles = Ct_Imp_Orden_Vendedor_Detalle::where('id_orden', $id_orden)->where('estado', 1)->get();
        $cab = [
            'id' => $orden->id,
            'observacion' => $orden->observacion,
            'fecha' => $orden->created_at,
        ];
        $cab["detalles"] = [];
        foreach ($detalles as $value) {
            $details = [
                "id_producto"           => $value->id_producto,
                "nombre_producto"       => isset($value->producto) ? "{$value->producto->codigo} | {$value->producto->nombre}" : '',
                "cantidad"              => $value->cantidad
            ];
            array_push($cab["detalles"], $details);
        }
        return ["cab" => $cab];
    }

    public function asientosCompras()
    {
        $id_empresa = Session::get('id_empresa');
        $compras = Ct_compras::where('id_empresa', $id_empresa)->where('estado', '>=', '0')->get();
        $data = array();
        foreach ($compras as $value) {
            $asiento_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $value->id_asiento_cabecera)
                ->where(function ($q) {
                    $q->where('id_plan_cuenta', '2.01.03.01.01')->orWhere('id_plan_cuenta', '2.01.03.02.01')->orWhere('id_plan_cuenta', '2.01.03.01.02');
                })
                ->first();

            if (is_null($asiento_detalle)) {
                $aux = [
                    "id_compra" => $value->id,
                    "id_asiento" => $value->id_asiento_cabecera,
                    "total" => $value->total_final,
                    "msj" => "Cuenta diferente a Proveedores locales, del exterior o de gastos",
                    "tipo" => 0
                ];
                array_push($data, $aux);
            }

            if (!is_null($asiento_detalle)) {

                if (floatval($asiento_detalle->haber) > floatval($value->total_final) or floatval($asiento_detalle->haber)  < floatval($value->total_final)) {
                    $aux1 = [
                        "id_compra" => $value->id,
                        "id_asiento" => $value->id_asiento_cabecera,
                        "total" => $value->total_final,
                        "msj" => "Asiento descuadrado",
                        "tipo" => 1
                    ];
                    array_push($data, $aux1);
                }
            }

            if ($value->estado == 0) {
                $log = Log_Contable::where('id_ant', $value->id_asiento_cabecera)->first();
                if (is_null($log)) {
                    $aux2 = [
                        "id_compra" => $value->id,
                        "id_asiento" => $value->id_asiento_cabecera,
                        "total" => $value->total_final,
                        "msj" => "no tiene asiento de anulacion",
                        "tipo" => 2
                    ];
                    array_push($data, $aux2);
                }
            }
        }
        return view('contable.importaciones.asientos_compras', ['data' => $data]);
    }

    public function buscar_asientos_compras($id)
    {
        $compra = Ct_compras::find($id);
        $fecha_desde = date("Y-m-d", strtotime($compra->updated_at)) . " 00:00:00";
        $fecha_hasta = date("Y-m-d", strtotime($compra->updated_at)) . " 23:59:59";
        $cab = Ct_Asientos_Cabecera::whereBetween('created_at', [$fecha_desde, $fecha_hasta])->where("id_empresa", Session::get("id_empresa"))->get();

        return view('contable.importaciones.modal_asientos', ['cab' => $cab]);
    }
    public function guardarAsientoAnulado(Request $request)
    {
        try {
            $asiento_ant = Ct_Asientos_Cabecera::find($request->id_asiento);
            if (is_null($asiento_ant)) {
                return ["status" => "error", "message" => "Falta el asiento anterior"];
            }

            $asiento_elegido = Ct_Asientos_Cabecera::find($request->id_asiento_elegido);
            if (is_null($asiento_elegido)) {
                return ["status" => "error", "message" => "Falta el asiento nuevo o no corresponde el id"];
            }

            Log_Contable::create([
                "id_ant"        => $asiento_ant->id,
                "id_referencia" => $asiento_elegido->id,
                "id_usuariocrea" => Auth::user()->id,
                "id_usuariomod" => Auth::user()->id,
                "tipo"          => "COM-FA",
                "valor_ant"     => $asiento_ant->valor,
                "valor"         => $asiento_elegido->valor,
            ]);

            return ["status" => "success", "message" => "Guardado con exito"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
