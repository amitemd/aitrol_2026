<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Sis_medico\Bodega;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Importaciones_Compras;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Equipo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inv_Cab_Carga_Inicial;
use Sis_medico\Inv_Carga_Inventario;
use Sis_medico\Inv_Det_Carga_Inicial;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\LogImportaciones;
use Sis_medico\Marca;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Union_Bodega;
use Sis_medico\User;
use Sis_medico\InvKardexNuevo;
use Sis_medico\Log_Importaciones_Movimiento;
use Sis_medico\Venta_Kardex;

class CargaInicialController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $insumos = Inv_Cab_Carga_Inicial::where('estado', 1)->orderby('id', 'desc')->where('id_empresa', Session::get('id_empresa'))->paginate(15);
        return view('insumos/carga_inicial/index', ['insumos' => $insumos]);
    }

    public function storeExcel(Request $request)
    {
        $anio = date('Y');
        $mes = date('m');
        $columna = 1;
        DB::beginTransaction();
        try {
            $observacion = $request->observacion;

            $id_empresa = Session::get('id_empresa');
            $nombre_original = $request['archivo']->getClientOriginalName();

            $extension       = $request['archivo']->getClientOriginalExtension();
            //dd($extension);

            $nuevo_nombre    =   "carga_inicial_" . $id_empresa . '_' . $anio . '_' . $mes . '_' . date('YmdHis') . "." . $extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;

            $excel = [
                'nombre'        => $nombre_original,
                'nuevo_nombre'  => $nuevo_nombre,
                'ruta'          => $rutadelaimagen,
            ];

            if ($r1) {
                // dd($request->all());

                Excel::filter('chunk')->formatDates(true, 'Y-m-d')->load($rutadelaimagen)->chunk(3000, function ($reader) use ($excel, $observacion, $columna) {
                    $id_usuario = Auth::user()->id;
                    // dd($id_usuario);

                    // dd($reader);

                    $ip_cliente   = $_SERVER["REMOTE_ADDR"];
                    $data_cab = [
                        'nombre_excel'      => $excel['nombre'],
                        'nuevo_nombre'      => $excel['nuevo_nombre'],
                        // 'fecha_proceso'     => date('Y-m-d'),
                        // 'total_item'        => ,
                        // 'item_problema',
                        'id_empresa'        => Session::get('id_empresa'),
                        'descripcion'       => $observacion,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    $id_carga_inicial = Inv_Cab_Carga_Inicial::insertGetId($data_cab);
                    $insumos_prob = 0;
                    $total_items = 0;
                    $contador = 1;
                    //dd($id_carga_inicial);
                    // dd($reader);


                    foreach ($reader as $book) {
                        // dd($book);
                        $msj = "";
                        $columna++;
                        if (!is_null($book)) {
                            $verificar = CargaInicialController::verificarProducto(trim($book->codigo), 1, ["nombre" => $book->detalle, "marca" => $book->marca]); //validar el producto 
                            $verificar_bodega = CargaInicialController::verificarProducto(trim($book->bodega), 2); //Valida la bodega 
                            $verificar_serie = CargaInicialController::verificarProducto(trim($book->serie), 3, ["secuencia" => $contador]); //Valida la serie
                            $verificar_cantidad = CargaInicialController::verificarProducto(trim($book->cantidad), 4); //Valida la serie
                            //$verificar_kardex_uso = CargaInicialController::verificarProducto($verificar_bodega, 5, $verificar["valor"] == 0 ? ["cod_producto" => trim($book->codigo)] : []); //Valida kardex por uso

                            $contador++;

                            $serie_guardar = $verificar_serie["serie"];


                            $fecha_exp = date("Y-m-d");

                            if (!is_null($book->fecha_exp) || strtotime($book->fecha_exp) == false || !is_null($book->fecha_exp)) {
                                $unix_date  = ($book->fecha_exp - 25569) * 86400;
                                $excel_date = 25569 + ($unix_date / 86400);
                                $unix_date  = ($excel_date - 25569) * 86400;
                                $fecha_exp  = gmdate("Y-m-d", intval($unix_date));
                            } else if ($book->fecha == "" || is_null($book->fecha) || $book->fecha == ' ' || is_null($book->fecha)) {
                                $fecha_exp = "2022-12-31";
                            }

                            $problema = 0;
                            $id_bodega = null;
                            if ($verificar['valor'] == 1 or $verificar_bodega['valor'] == 1 or $verificar_cantidad['valor'] == 1) {
                                $problema = 1;
                                if ($verificar_bodega["valor"] == 0) {
                                    $id_bodega = $verificar_bodega['id_bodega'];
                                }
                                $insumos_prob++;
                            } else {
                                $problema = 0;
                                $id_bodega = $verificar_bodega['id_bodega'];
                            }


                            $msj = $verificar['msj'] . $verificar_bodega['msj'] . $verificar['val_nombre'] . $verificar_serie['msj'] . $verificar_cantidad['msj'];

                            $aggProducto = Producto::where('codigo', trim($book->codigo))->first();
                            // dd($book->codigo, $aggProducto);
                            if (!is_null($aggProducto) and $aggProducto->kardex_uso == 1) {
                                for ($f = 0; $f < intval($book->cantidad); $f++) {
                                    $data_det = [
                                        'id_cab'                    => $id_carga_inicial,
                                        'marca'                     => trim($book->marca),
                                        'descripcion'               => trim($book->detalle),
                                        // 'descripcion1'              => $book->descripcion2,
                                        'descripcion1'              => trim($book->detalle),
                                        'codigo'                    => trim($book->codigo),
                                        'lote'                      => trim($book->lote),
                                        'cantidad'                  => 1,
                                        'pedido'                    => trim($book->pedido),
                                        'fecha_exp'                 => $fecha_exp,
                                        'serie'                     => str_replace("-", "", trim($serie_guardar)),
                                        'estado'                    => 1,
                                        'tipo'                      => trim(strtoupper($book->tipo)),
                                        'lugar'                     => trim($book->bodega),
                                        'precio'                    => $book->precio,
                                        'id_bodega'                    => $id_bodega,
                                        'problema'                  => $problema,
                                        'id_empresa'                => Session::get('id_empresa'),
                                        'id_usuariocrea'            => $id_usuario,
                                        'id_usuariomod'             => $id_usuario,
                                        'ip_creacion'               => $ip_cliente,
                                        'ip_modificacion'           => $ip_cliente,
                                        'observacion'               => $msj,
                                        'cambiar_nombre'            => $verificar['val_nombre'] != "" ? 1 : 0
                                    ];
                                    $total_items++;
                                    Inv_Det_Carga_Inicial::create($data_det);
                                    // $verificar_serie = CargaInicialController::verificarProducto(trim($book->serie), 3, ["secuencia" => $contador]);//Valida la serie
                                    $verificar = CargaInicialController::verificarProducto(trim($book->codigo), 1, ["nombre" => $book->detalle, "marca" => $book->marca]); //validar el producto
                                    $verificar_bodega = CargaInicialController::verificarProducto(trim($book->bodega), 2); //Valida la bodega
                                    $verificar_serie = CargaInicialController::verificarProducto(trim($book->serie), 3, ["secuencia" => $contador]); //Valida la serie
                                    $verificar_cantidad = CargaInicialController::verificarProducto(trim($book->cantidad), 4); //Valida la serie
                                    $contador++;
                                    $serie_guardar = $verificar_serie["serie"];
                                    $msj = $verificar['msj'] . $verificar_bodega['msj'] . $verificar['val_nombre'] . $verificar_serie['msj'] . $verificar_cantidad['msj'];
                                }
                            } else {
                                $data_det = [
                                    'id_cab'                    => $id_carga_inicial,
                                    'marca'                     => trim($book->marca),
                                    'descripcion'               => trim($book->detalle),
                                    // 'descripcion1'              => $book->descripcion2,
                                    'descripcion1'              => trim($book->detalle),
                                    'codigo'                    => trim($book->codigo),
                                    'lote'                      => trim($book->lote),
                                    'cantidad'                  => intval($book->cantidad),
                                    'pedido'                    => trim($book->pedido),
                                    'fecha_exp'                 => $fecha_exp,
                                    'serie'                     => str_replace("-", "", trim($serie_guardar)),
                                    'estado'                    => 1,
                                    'tipo'                      => trim(strtoupper($book->tipo)),
                                    'lugar'                     => trim($book->bodega),
                                    // 'fecha_ingreso_compras',
                                    // 'fecha_salida_pentax',
                                    // 'caducado',
                                    'precio'                    => $book->precio,
                                    'id_bodega'                    => $id_bodega,
                                    // 'creado'                    => ,
                                    // 'carga',
                                    'problema'                  => $problema,
                                    'id_empresa'                => Session::get('id_empresa'),
                                    // 'id_proveedor'  ,
                                    'id_usuariocrea'            => $id_usuario,
                                    'id_usuariomod'             => $id_usuario,
                                    'ip_creacion'               => $ip_cliente,
                                    'ip_modificacion'           => $ip_cliente,
                                    'observacion'               => $msj,
                                    'cambiar_nombre'            => $verificar['val_nombre'] != "" ? 1 : 0
                                ];
                                $total_items++;
                                Inv_Det_Carga_Inicial::create($data_det);
                            }




                            $cab = Inv_Cab_Carga_Inicial::find($id_carga_inicial);
                            $cab->total_item = $total_items;
                            $cab->item_problema = $insumos_prob;
                            $cab->save();
                        }
                    }
                });
            }
            DB::commit();
            return ["status" => "success", "msj" => "Guardado Correctamente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => 'Ocurrio un error...', "exp" => $e->getMessage(), "columna" => $columna];
        }



        return redirect()->intended('/insumos/carga/inicial/index');
    }

    public static function verificarProducto($codigo, $type, $data = [])
    {
        $msj = "";
        $val_nombre = "";
        if ($type == "1") {
            $producto = Producto::where("codigo", $codigo)->first();
            if (is_null($producto)) {
                $msj = "<span class='label label-danger'>No se encontro el producto</span> <br>";
            } else {
                if (trim(strtoupper($producto->nombre)) != trim(strtoupper($data["nombre"]))) {
                    $val_nombre .= "<span class='label label-info'>Nombre del producto diferente, se cambiara el nombre.<br> Nombre anterior: {$producto->nombre} </span><br>";
                    // $val_nombre = 2;
                }
                if (isset($producto->marca)) {
                    if (trim(strtoupper($producto->marca->nombre)) != trim(strtoupper($data["marca"]))) {
                        $msj .= "<span class='label label-warning'>La marca es diferente </span><br>";
                    }
                } else {
                    $msj .= "<span class='label label-warning'>El producto creado no tiene asociado una marca</span><br>";
                }
            }
            if ($msj != "") {
                return ["msj" => $msj, "valor" => 1, "val_nombre" => $val_nombre];
            } else {
                return ["msj" => "", "valor" => 0, "val_nombre" => $val_nombre];
            }
        }

        if ($type == 2) {
            //  dd($codigo);
            if ($codigo == "") {
                return ["msj" => "<span class='label label-danger'>No se encontro la colunma <b>BODEGA</b></span> <br>", "valor" => 1];
            }
            $bodega = Bodega::find($codigo);
            // dd($bodega, $codigo);
            if (is_null($bodega)) {
                $bodega_nombre = Bodega::where('nombre', 'like', "%{$codigo}%")->where('id_empresa', Session::get('id_empresa'))->first();
                if (is_null($bodega_nombre)) {
                    return ["msj" => "<span class='label label-danger'>No se encontro la bodega</span> <br>", "valor" => 1];
                } else if (strtoupper($codigo) == "COMPRAS") {
                    return ["msj" => "<span class='label label-danger'>No se puede procesar en la bodega de compras</span> <br>", "valor" => 1];
                }
                return ["msj" => "", "valor" => 0, "id_bodega" => $bodega_nombre->id];
            }
            return ["msj" => "", "valor" => 0, "id_bodega" => $bodega->id];
        }

        if ($type == 3) {
            // dd($codigo);
            if (trim($codigo) == "N/A" or is_null($codigo) or trim($codigo) == "") {
                // $serie = date('YmdHis') . $data["secuencia"];
                $serie = date('YmdHis') . rand(1000, 9999);

                // return ["status"=>""];
                return ["msj" => "<span class='label label-info'>No se envio la serie, se creara la serie<br>Nueva Serie: <b>{$serie}<b></span> <br>", "serie" => $serie, "valor" => 0];
            }
            return ["msj" => "", "serie" => $codigo, "valor" => 0];
        }

        if ($type == 4) {
            if (is_null($codigo)) {
                return ["msj" => "<span class='label label-danger'>No se puede procesar con cantidad Vacia</span> <br>", "valor" => 1];
            }
            if (intval($codigo) <= 0) {
                return ["msj" => "<span class='label label-danger'>La cantidad debe ser mayor a 0</span> <br>", "valor" => 1];
            }



            return ["msj" => "", "cantidad" => $codigo, "valor" => 0];
        }

        if ($type == 5) {
            if ($codigo["valor"] == 0 and count($data) > 0) {
                $producto = Producto::where("codigo", $data['cod_producto'])->first();
                $bodega = Bodega::find($codigo['id_bodega']);
                $msj = "";
                if ($producto->kardex_uso == 1 and $bodega->prioridad != 1) {
                    $msj .= "<span class='label label-danger'>El producto es kardex por uso y debe enviarse a la bodega principal</span> <br>";
                    // return ["msj" => "<span class='label label-danger'>El producto es kardex por uso y debe enviarse a la bodega principal</span> <br>", "valor" => 1];
                }
                if ($producto->kardex_uso == 1 and $producto->cantidad_usos_kardex == 1) {
                    $msj .= "<span class='label label-danger'>El producto debe ser configurado como producto normal y no kardex por uso, ya que su uso es 1</span> <br>";
                    // return ["msj" => "<span class='label label-danger'>El producto debe ser configurado como producto normal y no kardex por uso, ya que su uso es 1</span> <br>", "valor" => 1];
                }

                if ($msj != "") {
                    return ["msj" => $msj, "valor" => 1];
                }
            } else {
                return ["msj" => "", "valor" => 1];
            }
            return ["msj" => "", "valor" => 0];
        }
    }

    public function edit($id)
    {
        $cab = Inv_Cab_Carga_Inicial::find($id);
        $problema = Inv_Det_Carga_Inicial::where('estado', 1)->where('problema', 1)->where('id_cab', $id)->get();
        $detalle = Inv_Det_Carga_Inicial::where('estado', 1)->where('problema', 0)->where('id_cab', $id)->get();
        $marca = Inv_Det_Carga_Inicial::where('estado', 1)->where('problema', 0)->where('id_cab', $id)->groupBy('marca')->select(DB::raw('count(*) as contador'), 'marca')->get();
        return view('insumos/carga_inicial/edit', ['cab' => $cab, 'problema' => $problema, 'detalle' => $detalle, "marca" => $marca]);
    }

    public function procesar(Request $request)
    {
        $cabecera = Inv_Cab_Carga_Inicial::find($request->id_cabecera);
        // $detalles = Inv_Det_Carga_Inicial::where('estado', 1)->where('problema', 0)->where('id_cab', $cabecera->id)->get();

        $detalles = Inv_Det_Carga_Inicial::where('estado', 1)->where('problema', 0)->where('id_cab', $cabecera->id)->groupBy('serie', 'codigo', 'id_bodega')
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'codigo', 'marca', 'descripcion', 'lote', 'fecha_exp', 'pedido', 'tipo', 'id_bodega', 'lugar', 'precio')->get();

        //dd(COUNT($detalles),$detalles);
        //   dd($detalles);
        if (count($detalles) == 0) {
            return ['status' => 'error', 'msj' => 'No hay nada que procesar', 'exp' => ""];
        }

        $id_usuario = Auth::user()->id;
        if ($cabecera->procesado == 1) {
            return ['status' => 'error', 'msj' => 'Ya se proceso esta carga', 'exp' => ""];
        }

        // $procesados = CargaInicialController::productosProcesados($cabecera->id);
        // // dd($procesados);
        // if($procesados["status"] == "error"){
        //     return $procesados;
        // }


        DB::beginTransaction();
        try {

            // Vacia la tabla de inv_carga inventario wl-312`

            $visitors = DB::table('inv_carga_inventario')->truncate();

            $prod_cambiado = CargaInicialController::cambiarNombreProducto($cabecera->id);
            if ($prod_cambiado["status"] == "error") {
                return $prod_cambiado;
            }

            foreach ($detalles as $det) {
                //dd($det);
                $producto = Producto::where('codigo', $det->codigo)->first();

                // $kardex   = InvKardex::where('id_empresa', $cabecera->id_empresa)
                //     ->where('id_producto', $producto->id)
                //     ->where('id_bodega', $det->id_bodega)
                //     //->delete();
                //     ->update([
                //         'id_empresa' => '0922729587001',
                //         'ip_modificacion' => "Cambio por carga Inicial: {$det->id_cab}",
                //         'id_usuariomod'   => Auth::user()->id,
                //     ]);



                // $inventario = InvInventario::where('id_empresa', $cabecera->id_empresa)
                //     ->where('id_bodega', $det->id_bodega)
                //     ->where('id_producto', $producto->id)
                //     //->delete();
                //     ->update([
                //         'id_empresa' => '0922729587001',
                //         'ip_modificacion' => "Cambio por carga Inicial: {$det->id_cab}",
                //         'id_usuariomod'   => Auth::user()->id,

                //     ]);

                // $inventario_serie = InvInventarioSerie::where('id_empresa', $cabecera->id_empresa)
                //     ->where('id_bodega', $det->id_bodega)
                //     ->where('id_producto', $producto->id)
                //     //->delete();
                //     ->update([
                //         'id_empresa' => '0922729587001',
                //         'ip_modificacion' => "Cambio por carga Inicial: {$det->id_cab}",
                //         'id_usuariomod'   => Auth::user()->id,
                //     ]);
                DB::table('inv_carga_inventario')->insert([
                    'marca'                     => $det->marca, //marca
                    'descripcion'               => $det->descripcion, //detalle
                    'descripcion1'              => $det->descripcion, //detalle
                    'codigo'                    => $det->codigo, //referencia
                    'lote'                      => $det->lote,
                    'cantidad'                  => $det->cantidad,
                    'fecha_exp'                 => $det->fecha_exp, //fecha vencimiento
                    'serie'                     => $det->serie, //codigo de barras
                    'estado'                    => 1,
                    'pedido'                    => $det->pedido, //numero pedido
                    'tipo'                      => $det->tipo, //facturado o consignado
                    'caducado'                  => $det->fecha_exp, //NO SE
                    'precio'                    => $det->precio, //NO SE
                    'lugar'                     => isset($det->bodega) ? $det->bodega->nombre : $det->lugar,
                    'bodega'                    => $det->id_bodega,
                    'creado'                    => 'REVISAR',
                    'id_empresa'                => $cabecera->id_empresa,
                    'id_cab_carga_inicial'      => $cabecera->id
                ]);
            }
            //dd("aquiii");
            $carga_inicial = CargaInicialController::carga_inicial($cabecera->id);
            // dd($carga_inicial);
            if ($carga_inicial['status'] == "error") {
                DB::rollback();
                return $carga_inicial;
                // return ['status' => 'error', 'msj' => $carga_inicial['msj'] , 'exp'=> $carga_inicial['msj']];
            }
            $cabecera->fecha_proceso        = date('Y-m-d H:i:s');
            $cabecera->procesado            = 1;
            $cabecera->id_usuariomod        = $id_usuario;
            $cabecera->save();
            DB::commit();
            return ['status' => 'success', 'msj' => 'Se ha creado la carga inicial correctamente'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public static function productosProcesados($id_principal)
    {
        //$cabeceras =  Inv_Cab_Carga_Inicial::where('estado', 1)->where('procesado', 1)->get();

        $principal = Inv_Cab_Carga_Inicial::find($id_principal);

        $status = "success";
        $msj = "";

        foreach ($principal->detalles as $detail) {
            if ($detail->problema == 0) {
                $validate = Inv_Det_Carga_Inicial::where('problema', 0)->where('codigo', $detail->codigo)->where('id_cab', '<>', $principal->id)->groupBy('codigo')->select('codigo', "id_cab")->first();
                if (!is_null($validate)) {
                    if (isset($validate->cabecera)) {
                        if ($validate->cabecera->procesado == 1) {
                            $status = 'error';
                            $msj .= "{$validate->codigo} <br>";
                        }
                    }
                }
            }
        }

        if ($msj != "") {
            $msj = "Ya se proceso el estos codigos: {$msj}";
            $status = "error";
        }

        return ["status" => $status, "msj" => $msj, "exp" => ""];
    }


    public static function carga_inicial($id_cab_inicial)
    {
        try {
            $ip_cliente  = $_SERVER["REMOTE_ADDR"];
            $idusuario   = Auth::user()->id;
            $cab_mov_inv = null;
            $id_pedido   = null;
            $bodegas = [];
            $cab = Inv_Carga_Inventario::where('id_cab_carga_inicial', $id_cab_inicial)->groupBy('bodega', 'Lugar')->select('bodega', 'Lugar')->get();
            foreach ($cab as $value) {
                $bodegas[$value->bodega] = $value->Lugar;
            }
            // $bodegas2     = array(1 => 'COMPRAS', 2 => 'PENTAX');
            // dd($bodegas, $bodegas2);
            foreach ($bodegas as $key => $value) {
                // dd($key, $value);
                $inv_carga = DB::table('inv_carga_inventario')
                    ->where('bodega', $key)
                    ->whereNull('carga')
                    ->get();
                // dd($inv_carga);
                $bodega = Bodega::find($key);
                $msjok  = "";
                $msjerr = "";
                $i      = 0;
                foreach ($inv_carga as $row) {
                    // $randon_pedido = $key . date('YmdHis'). rand(1, 200);

                    $row->serie = str_replace('-', '', $row->serie);
                    //  crear la cabecera    //
                    // $cab_mov_inv = DB::table('inv_cab_movimientos as c')
                    //     ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
                    //     ->where('d.abreviatura_documento', 'CIN')
                    //     ->where('c.id_bodega_origen', $key)
                    //     ->where('c.estado', '!=', 0)
                    //     ->where("c.id_empresa", Session::get('id_empresa'))//Lo nuevo que subi
                    // // ->where('c.created_at','>=',date('Y-m-d'))
                    //     ->select('c.*')
                    //     ->first();
                    // dd($cab_mov_inv);
                    // $pedido = Pedido::where('id_proveedor', '0992704152001')

                    $randon_pedido = $key . date('YmdHis') . rand(1, 200);
                    /*if($row->id_empresa == '0999999999001' ){
                        $row->id_empresa = '11312';
                    }*/

                    $pedido = Pedido::where('id_proveedor', $row->id_empresa)
                        // ->where('fecha', date('Y-m-d'))
                        ->where('factura', 'CIN-00' . $randon_pedido)
                        ->where('pedido', 'CIN-00' . $randon_pedido)
                        ->first();

                    if (is_null($pedido)) {
                        $input = [
                            'id_proveedor'    => $row->id_empresa,
                            'pedido'          => 'CIN-00' . $randon_pedido,
                            'tipo'            => 2,
                            'factura'         => 'CIN-00' . $randon_pedido,
                            'fecha'           => date('Y-m-d'),
                            'id_bodega'       => $key,
                            'id_empresa'      => $row->id_empresa,
                            'observaciones'   => 'CARGA INICIAL',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        $id_pedido = Pedido::insertGetId($input);
                        $pedido    = Pedido::find($id_pedido);
                    }

                    // if (!isset($cab_mov_inv->id)) {

                    ##    PEDIDO
                    $documento   = InvDocumentosBodegas::where('abreviatura_documento', 'CIN')->first();
                    if (is_null($documento)) {
                        return ['status' => "error", "msj" => "No tiene documento de bodegas configurados (CIN)", "exp" => ""];
                    }
                    $secuencia   = InvDocumentosBodegas::getSecueciaTipoDocum($key, 'CIN');
                    // dd($documento, $secuencia);
                    if (is_null($documento)) {
                        return ['status' => "error", "msj" => "No se encontro secuencia configuradas", "exp" => ""];
                    }

                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                        ->where('id_bodega', $key)
                        ->first();

                    ##      C A B E C E R A
                    $cab_mov_inv                        = new InvCabMovimientos;
                    $cab_mov_inv->id_documento_bodega   = $documento->id;
                    $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                    $cab_mov_inv->id_bodega_origen      = $key;
                    $cab_mov_inv->numero_documento      = str_pad($secuencia, 10, "0", STR_PAD_LEFT);
                    $cab_mov_inv->observacion           = 'CARGA DE DATOS ' . date('d/m/Y') . " BODEGA: " . $bodega->nombre;
                    $cab_mov_inv->fecha                 = date('Y-m-d');
                    $cab_mov_inv->id_empresa            = $row->id_empresa;
                    $cab_mov_inv->id_pedido             = $pedido->id;
                    $cab_mov_inv->ip_creacion           = $ip_cliente;
                    $cab_mov_inv->ip_modificacion       = $ip_cliente;
                    $cab_mov_inv->id_usuariocrea        = $idusuario;
                    $cab_mov_inv->id_usuariomod         = $idusuario;

                    $cab_mov_inv->save();

                    // }

                    if (trim($row->tipo) == 'CONSIGNA') {
                        $tipo = 'C';
                    } else {
                        $tipo = 'F';
                    }
                    $carga    = 'C';


                    $producto = Producto::where(trim('codigo'), trim($row->codigo))->first();
                    if (!isset($producto->id)) {
                        $movimiento = Movimiento::where('serie', $row->serie)->first();
                        if (isset($movimiento->id_producto)) {
                            $producto = Producto::find($movimiento->id_producto);
                            $carga    = 'F';
                        }
                    }
                    $cant_uso = 0;
                    $iva      = 0;
                    if (isset($producto->id)) {

                        // if ($value=='PENTAX') {dd($producto);}
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($row->cantidad * $row->precio) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }
                        $inventario = InvInventario::getInventario($producto->id, $key, $tipo);
                        if ($inventario == '[]') {
                            $inventario = InvInventario::setNeoInventario($producto->id, $key, $tipo, 0, 0);
                        }
                        if ($row->serie != "") {
                            // if ($row->fecha_exp==null||$row->fecha_exp=="") {
                            //     $row->fecha_exp = '2023-12-31';
                            // }
                            if ($row->fecha_exp == null || $row->fecha_exp == "" || $row->fecha_exp < date('Y-m-d')) {
                                $row->fecha_exp = date("Y-m-d", strtotime(date('Y-m-d') . "+ 1 year"));
                            }
                            if ($row->cantidad == null) {
                                $row->cantidad = 1;
                            }
                            if ($row->lote == null) {
                                $row->lote = rand(100, 999);
                            }
                            $det_mov_inv                         = new InvDetMovimientos;
                            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                            $det_mov_inv->id_producto            = $producto->id;
                            $det_mov_inv->id_inv_inventario      = $inventario->id;
                            $det_mov_inv->cantidad               = $row->cantidad;
                            $det_mov_inv->cant_uso               = $row->cantidad * $cant_uso;
                            $det_mov_inv->serie                  = $row->serie;
                            $det_mov_inv->lote                   = $row->lote;
                            $det_mov_inv->fecha_vence            = date('Y-m-d', strtotime($row->fecha_exp));
                            $det_mov_inv->valor_unitario         = $row->precio;
                            $det_mov_inv->subtotal               = $row->cantidad * $row->precio;
                            $det_mov_inv->descuento              = 0;
                            $det_mov_inv->iva                    = $iva;
                            $det_mov_inv->id_pedido              = $pedido->id;
                            $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                            $det_mov_inv->motivo                 = "CARGA DE DATOS " . date('d/m/Y') . " || BODEGA: " . $bodega->nombre . " || PEDIDO: " . $row->pedido;
                            $det_mov_inv->ip_creacion            = $ip_cliente;
                            $det_mov_inv->ip_modificacion        = "CargaINI";
                            $det_mov_inv->id_usuariocrea         = $idusuario;
                            $det_mov_inv->id_usuariomod          = $idusuario;
                            $det_mov_inv->cantidad_usos_kardex   = $producto->cantidad_usos_kardex;
                            $det_mov_inv->save();
                            // if ($id_pedido==null) {$id_pedido=1428;}

                            $input2 = [
                                'id_producto'       => $producto->id,
                                'cantidad'          => $row->cantidad,
                                'id_encargado'      => $idusuario,
                                'serie'             => $row->serie,
                                'id_bodega'         => $key,
                                'tipo'              => '1',
                                'fecha_vencimiento' => date('Y-m-d', strtotime($row->fecha_exp)),
                                'lote'              => $row->lote,
                                'usos'              => $row->cantidad * $cant_uso,
                                'descuentop'        => 0,
                                'descuento'         => 0,
                                'precio'            => $row->precio,
                                'id_pedido'         => $pedido->id,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariocrea'    => $idusuario,
                                'id_usuariomod'     => $idusuario,
                            ];
                            $id_movimiento = DB::table('movimiento')->insertGetId($input2);
                            // echo "<span style='color:blue'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span>  <br>";
                            DB::table('inv_carga_inventario')
                                ->where('id', $row->id)
                                ->update(['carga' => $carga]);
                        }
                        if (!is_null($cab_mov_inv)) {
                            $kardex = InvKardex::setKardex($cab_mov_inv->id);
                            if ($bodega->prioridad != '1' and $producto->kardex_uso == '1') {
                                $new_kardex = InvKardexNuevo::setKardexNuevo($cab_mov_inv->id);
                            }
                        }
                        // return ['status' => 'success', 'msj'=>'Cargado con exito'];
                    } else {
                        DB::table('inv_carga_inventario')
                            ->where('id', $row->id)
                            ->update(['carga' => 'X']);
                        // echo "<span style='color:red'>SERIE: $row->serie || CODIGO: $row->codigo || $row->cantidad</span> <br>";
                        return ['status' => 'error', 'msj' => 'No se encontro el producto', 'exp' => ""];
                    }
                    InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
                    $i++;
                }
            }
            return ['status' => 'success', 'msj' => 'Cargado con exito'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => 'Error al cargar...', "exp" => $e->getMessage(), "linea" => $e->getLine()];
        }
    }

    public static function ___importacionKardexNovale($id_importacion)
    {
        /*
            1) Inv_Inventario
            2) Inv_cab_Movimiento
            3) Inv_Detalle_Movimiento
            4) Inv_inventario Serie
            5) Inv_Kardex
        */
        $logImportaciones = LogImportaciones::where('id_import_cab', $id_importacion)->where('principal', 2)->first();
        if (is_null($logImportaciones)) {
            return ['status' => "error", "msj" => "No se encontro la factura de importacion", "exp" => ""];
        }
        $cab_compra = Ct_Importaciones_Compras::find($logImportaciones->id_compra);

        $id_empresa = Session::get('id_empresa');

        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();

        $total_asiento = 0;
        $cambia_bodega = 12;

        $id_bodega = $cambia_bodega;

        $id_usuario = Auth::user()->id;
        $numero = "000000001";
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        if (!is_null($cab_movimiento)) {
            $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
        }
        $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'IIM')->where('id_empresa', $id_empresa)->first();

        if (is_null($inv_documento_bod)) {
            return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas", "exp" => ""];
        }

        DB::beginTransaction();
        $count = "";
        $id_serie = 1;

        try {

            // $inv_transacciones_bodegas = InvTransaccionesBodegas::insertGetId([
            //     'id_documento_bodega'   => $inv_documento_bod->id,
            //     // 'id_bodega'             => $id_bodega,//cambiar por bodefa de empresa prioridad_labs
            //     'secuencia'             => 100,
            //     'id_empresa'            => $id_empresa,
            //     'id_usuariocrea'        => $id_usuario,
            //     'id_usuariomod'         => $id_usuario,
            //     'ip_creacion'           => $ip_cliente,
            //     'ip_modificacion'       => $ip_cliente
            // ]);

            // $inv_trans_bodega = InvTransaccionesBodegas::where('id_documento_bodega', 18)->first();
            $data_cab_movimiento = [
                'id_documento_bodega'       => $inv_documento_bod->id,
                // 'id_transaccion_bodega'     => $inv_transacciones_bodegas,
                // 'id_bodega_origen'          => $id_bodega,
                // 'id_bodega_destino'=> null, 
                'numero_documento'          => $numero,
                // 'num_doc_ext'=> '', 
                // 'num_doc_cont'=> '', 
                'observacion'               => "INGRESO POR IMPORTACION: {$cab_compra->numero}, IMP: {$id_importacion}",
                'fecha'                     => date("Y-m-d", strtotime($cab_compra->fecha)),
                'descuento'                 => $cab_compra->descuento,
                'subtotal'                  => $cab_compra->subtotal,
                'subtotal_0'                => $cab_compra->subtotal_0,
                'iva'                       => $cab_compra->iva_total,
                'total'                     => $cab_compra->total_final,
                'estado'                    => 1,
                // 'id_movimiento_estado'=> '', 
                // 'id_pedido'=> '', 
                // 'id_agenda'=> '', 
                // 'id_docum_origen'=> '', 
                // 'id_hc_procedimientos'=> '', 
                // 'id_asiento'=> '', 
                'id_empresa'                => $id_empresa,
                'id_usuariocrea'            => $id_usuario,
                'id_usuariomod'             => $id_usuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
            ];

            $id_cab_movimiento = InvCabMovimientos::insertGetId($data_cab_movimiento);
            foreach ($cab_compra->detalles as $detail) {

                $ligue_bodega = Union_Bodega::where('id_bodega_cont', $detail->bodega)->where('id_empresa', Session::get('id_empresa'))->first();
                if (is_null($ligue_bodega)) {
                    DB::rollback();
                    return ['status' => "error", "msj" => "No se encuentra ligado una bodega", "exp" => ""];
                }
                $id_bodega = $ligue_bodega->id_bodega_compra;
                $count = $detail->codigo;
                // $planilla_detalle_labs = Planilla_Detalle_Labs::find($request['detalles'][$i]);
                $producto_compra = Ct_productos::where('codigo', 'like', "{$detail->codigo}")->where("id_empresa", Session::get('id_empresa'))->first();

                if (is_null($producto_compra)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No se encontro el producto: {$detail->codigo}", "exp" => ""];
                }
                $producto_insumo  = Ct_productos_insumos::where('id_producto', $producto_compra->id)->first();

                if (is_null($producto_insumo)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No se encontro el ligue con producto de Compras <b>{$producto_compra->codigo}</b>", "exp" => ""];
                }

                $inv_inventario = InvInventario::where('id_producto', $producto_insumo->id_insumo)->where('id_bodega', $id_bodega)->where('estado', 1)->where('id_empresa', Session::get('id_empresa'))->latest()->first();
                // dd($inv_inventario, $detail);
                // if(is_null($inv_inventario)){
                //     $data_inv_inventario = [
                //         'id_producto'              => $producto_insumo->id_insumo,
                //         'id_bodega'                => $id_bodega,
                //         'tipo'                     => 'F',
                //         'existencia'               =>  $detail->cantidad ,
                //         'existencia_uso'           =>  $detail->cantidad ,
                //         'existencia_min'           =>  1,
                //         'existencia_max'           =>  999,
                //         'comprometido'             =>  $detail->cantidad,
                //         'comprometido_uso'         =>  $detail->cantidad ,
                //         'costo_promedio'           =>  $detail->costo_unitario ,
                //         // 'ubicacion'=> ,
                //         'estado' => 1,
                //         'id_empresa'               => $id_empresa,
                //         'id_usuariocrea'           => $id_usuario,
                //         'id_usuariomod'            => $id_usuario,
                //         'ip_creacion'              => $ip_cliente,
                //         'ip_modificacion'          => $ip_cliente,
                //     ];
                //     // dd($data_inv_inventario, $detail);
                //     $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);
                // }else{
                $costo_promedio = $detail->costo_unitario;
                $exist_nueva = $detail->cantidad;
                if (!is_null($inv_inventario)) {
                    $exist_nueva = $inv_inventario->existencia + $detail->cantidad;
                    if (!is_null($inv_inventario->costo_promedio)) {
                        // $cantidad_ant       = $inv_inventario->existencia;
                        $cantidad_ant       = $exist_nueva;
                        $precio_ant         = $inv_inventario->costo_promedio;
                        // $suma_ant           = $cantidad_ant * $precio_ant;
                        $suma_ant           = $inv_inventario->existencia * $precio_ant;

                        // $costo_promedio     = ($suma_ant + $detail->costo_total) / $inv_inventario->existencia; 
                        $costo_promedio     = ($suma_ant + $detail->costo_total) / $exist_nueva;
                        // dd("aqii",$costo_promedio, $cantidad_ant,$inv_inventario->existencia, $inv_inventario->costo_promedio);
                    }
                }
                // dd($costo_promedio, $detail, $inv_inventario);
                $data_inv_inventario = [
                    'id_producto'              => $producto_insumo->id_insumo,
                    'id_bodega'                => $id_bodega,
                    'tipo'                     => 'F',
                    // 'existencia'               => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->existencia) + $detail->cantidad,
                    'existencia'               => $exist_nueva,
                    'existencia_uso'           => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->existencia_uso) + $detail->cantidad,
                    'existencia_min'           => is_null($inv_inventario) ? 1 : $inv_inventario->existencia_min,
                    'existencia_max'           => is_null($inv_inventario) ? 999 : $inv_inventario->existencia_max,
                    'comprometido'             => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->comprometido) + $detail->cantidad,
                    'comprometido_uso'         => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->comprometido_uso) + $detail->cantidad,
                    // 'costo_promedio'           => is_null($inv_inventario) ? $detail->costo_unitario : $inv_inventario->costo_promedio,
                    'costo_promedio'           => $costo_promedio,
                    // 'ubicacion'=> ,
                    'estado' => 1,
                    'id_empresa'               => $id_empresa,
                    'id_usuariocrea'           => $id_usuario,
                    'id_usuariomod'            => $id_usuario,
                    'ip_creacion'              => $ip_cliente,
                    'ip_modificacion'          => $ip_cliente,
                ];
                // dd($data_inv_inventario, $detail);
                if (is_null($inv_inventario)) {
                    $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);
                } else {
                    $inv_inventario->update($data_inv_inventario);
                    $id_inv_inventario = $inv_inventario->id;
                }


                // $id_inv_inventario = $inv_inventario->id;
                //}


                /****************************************CREA LA SERIE *********************/
                $serie = '';
                $serie = date('YmdHis') . $id_serie;
                $id_serie++;

                $data_inv_det_movimiento = [
                    'id_inv_cab_movimientos'        => $id_cab_movimiento,
                    'id_producto'                   => $producto_insumo->id_insumo,
                    'serie'                         => $serie,
                    // 'fecha_vence'                   => date('Y-m-d', strtotime($cab_compra->fecha)),
                    'fecha_vence'                   => $detail->fecha_exp,
                    'lote'                          => $detail->lote,
                    'id_inv_inventario'             => $id_inv_inventario,
                    'cantidad'                      => $detail->cantidad,
                    'cant_uso'                      => 1,
                    'valor_unitario'                => $detail->costo_unitario,
                    'subtotal'                      => $detail->costo_total,
                    'descuento'                     => $detail->descuento,
                    'iva'                           => 0,
                    'total'                         => $detail->costo_total,
                    'estado'                        => 1,
                    'kardex'                        => 1,
                    'motivo'                        => "INGRESO DE IMPORTACION (ID): {$cab_compra->id} ",
                    // 'id_detalle_origen'=> '',
                    'id_pedido'                     => null,
                    // 'id_detalle_pedido'=> '',
                    // 'id_procedimiento'=> '',
                    // 'id_movimiento_paciente'=> '',
                    'id_usuariocrea'                => $id_usuario,
                    'id_usuariomod'                 => $id_usuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ];
                // dd($detail);

                $inv_det_movimiento = InvDetMovimientos::insertGetId($data_inv_det_movimiento);
                $inv_serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('serie', $serie)
                    ->where('id_bodega', $id_bodega)->first();
                // InvInventario::movimientoInventario($id_cab_movimiento);

                if (!is_null($inv_serie)) {
                    if ($inv_serie->existencia > 0) {
                        $inv_serie->existencia = $inv_serie->existencia + $detail->cantidad;
                        $inv_serie->existencia_uso = $inv_serie->existencia_uso + $detail->cantidad;
                        $inv_serie->id_usuariomod = $id_usuario;
                        $inv_serie->ip_modificacion = $ip_cliente;
                        $inv_serie->save();
                    }
                } else {
                    $dia    = date("d", strtotime($cab_compra->fecha));
                    $mes    = date("m", strtotime($cab_compra->fecha));
                    $anio   = intval(date("Y", strtotime($cab_compra->fecha))) + 1;
                    $data_inv_inventario = [
                        "id_inv_inventario" => $id_inv_inventario,
                        'id_cab_moviento'   => $id_cab_movimiento,
                        "id_producto"       => $producto_insumo->id_insumo,
                        "id_bodega"         => $id_bodega,
                        "tipo"              => "F",
                        "serie"             => $serie,
                        // "fecha_vence"       => "{$anio}-{$mes}-{$dia}",
                        "fecha_vence"       => $detail->fecha_exp,
                        "lote"              => $detail->lote,
                        "existencia"        => $detail->cantidad,
                        "existencia_uso"    => $detail->cantidad,
                        "comprometido"      => $detail->cantidad,
                        "comprometido_uso"  => $detail->cantidad,
                        "estado"            => 1,
                        "id_empresa"        => $id_empresa,
                        "id_usuariocrea"    => $id_usuario,
                        "id_usuariomod"     => $id_usuario,
                        "ip_creacion"       => $ip_cliente,
                        "ip_modificacion"   => $ip_cliente,
                    ];
                    InvInventarioSerie::create($data_inv_inventario);
                }


                $data_inv_kardex = [
                    'id_inv_inventario' => $id_inv_inventario,
                    'id_bodega' => $id_bodega,
                    'id_producto' => $producto_insumo->id_insumo,
                    'tipo' => 'I',
                    'descripcion' => 'INGR-IMPORTACION ' . $id_importacion,
                    'referencia' => "INGRESO DE IMPORT.: {$id_importacion}",
                    'fecha' => date('Y-m-d'),
                    'cantidad' => $detail->cantidad,
                    'cant_uso' => is_null($inv_serie) ? $detail->cantidad : $inv_serie->existencia,
                    'valor_unitario' => $detail->costo_unitario,
                    // 'iva' => '', 
                    'total' => $detail->costo_total,
                    'exist_cant' => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->existencia) + $detail->cantidad,
                    'exist_uso' => is_null($inv_inventario) ? $detail->cantidad : intval($inv_inventario->existencia) + $detail->cantidad,
                    // 'exist_valor_unitario' => '', 
                    // 'exist_total' => '', 
                    'estado' => 1,
                    'id_documento_bodega' => $inv_documento_bod->id,
                    'id_inv_det_movimientos' => $inv_det_movimiento,
                    'id_empresa' => $id_empresa,
                    'id_usuariocrea' => $id_usuario,
                    'id_usuariomod' => $id_usuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    // 'dar_baja' => '', 
                ];


                $id_inv_kardex = InvKardex::insertGetId($data_inv_kardex);
                $kardex = InvKardex::find($id_inv_kardex);
                // dd($data_inv_kardex, $detail , $kardex );
            }

            DB::commit();
            return ['status' => 'success', "msj" => 'Guardado con exito'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage(), "e" => $e,  "codigo" => $count];
        }
    }

    public static function importacionKardex($id_importacion)
    {
        $logImportaciones = LogImportaciones::where('id_import_cab', $id_importacion)->where('principal', 2)->first();
        if (is_null($logImportaciones)) {
            return ['status' => "error", "msj" => "No se encontro la factura de importacion", "exp" => ""];
        }
        $cab_compra = Ct_Importaciones_Compras::find($logImportaciones->id_compra);
        $id_empresa = Session::get('id_empresa');
        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();

        $total_asiento = 0;
        $cambia_bodega = 12;

        $id_bodega = $cambia_bodega;

        $id_usuario = Auth::user()->id;
        $numero = "000000001";
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        if (!is_null($cab_movimiento)) {
            $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
        }
        $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'IIM')->where('id_empresa', $id_empresa)->first();

        if (is_null($inv_documento_bod)) {
            return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas", "exp" => ""];
        }

        DB::beginTransaction();
        $count = "";
        $id_serie = 1;

        try {
            $doc_bodega = InvDocumentosBodegas::where('abreviatura_documento', 'IIM')->where('id_empresa', $id_empresa)->first();
            if (is_null($doc_bodega)) {
                return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas", "exp" => ""];
            }

            $bodega_cont = Ct_Bodegas::where('id_empresa', $id_empresa)->where('importacion', 1)->first();
            if (is_null($bodega_cont)) {
                DB::rollback();
                return ['status' => 'error', 'msj' => "No tiene una bodega de importaciones", "exp" => ""];
            }
            $union_bodega = Union_Bodega::where('id_bodega_cont',  $bodega_cont->id)->first();
            $tipo_ = $doc_bodega->id;
            $bodega_recibe = $union_bodega->id_bodega_compra;
            $secuencia = InvDocumentosBodegas::getSecuecia($tipo_, $bodega_recibe);
            $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $tipo_)
                ->where('id_bodega', $bodega_recibe)
                ->first();
            $cab_mov_inv                        = new InvCabMovimientos;
            $cab_mov_inv->id_documento_bodega   = $tipo_;
            $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
            $cab_mov_inv->id_bodega_origen      = $bodega_recibe;
            $cab_mov_inv->id_bodega_destino     = $bodega_recibe;
            $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
            // $cab_mov_inv->observacion           = $request['observaciones'];
            $cab_mov_inv->observacion           = "INGRESO POR IMPORTACION: {$cab_compra->numero}, IMP: {$id_importacion}";
            $cab_mov_inv->fecha                 = date("Y-m-d");
            $cab_mov_inv->num_doc_ext           = $numero;
            $cab_mov_inv->num_doc_cont          = $id_importacion;
            // $cab_mov_inv->descuento             = $request['descuentx'];
            // $cab_mov_inv->subtotal              = $request['subtotal_12'];
            // $cab_mov_inv->subtotal_0            = $request['subtotal_0'];
            // $cab_mov_inv->iva                   = $request['iva'];
            // $cab_mov_inv->total                 = $request['total'];
            $cab_mov_inv->descuento             = 0;
            $cab_mov_inv->subtotal              = 0;
            $cab_mov_inv->subtotal_0            = 0;
            $cab_mov_inv->iva                   = 0;
            $cab_mov_inv->total                 = 0;
            $cab_mov_inv->id_pedido             = null;
            $cab_mov_inv->id_empresa            = Session::get('id_empresa');
            $cab_mov_inv->ip_creacion           = $ip_cliente;
            $cab_mov_inv->ip_modificacion       = $ip_cliente;
            $cab_mov_inv->id_usuariocrea        = $id_usuario;
            $cab_mov_inv->id_usuariomod         = $id_usuario;
            $cab_mov_inv->save();

            $total = 0;
            foreach ($cab_compra->detalles as $detail) {

                $ligue_bodega = Union_Bodega::where('id_bodega_cont', $detail->bodega)->where('id_empresa', Session::get('id_empresa'))->first();
                if (is_null($ligue_bodega)) {
                    DB::rollback();
                    return ['status' => "error", "msj" => "No se encuentra ligado una bodega", "exp" => ""];
                }
                $count = $detail->codigo;
                $producto_compra = Ct_productos::where('codigo', 'like', "{$detail->codigo}")->where("id_empresa", Session::get('id_empresa'))->first();

                if (is_null($producto_compra)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No se encontro el producto: {$detail->codigo}", "exp" => ""];
                }
                $producto_insumo  = Ct_productos_insumos::where('id_producto', $producto_compra->id)->first();

                if (is_null($producto_insumo)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No se encontro el ligue con producto de Compras <b>{$producto_compra->codigo}</b>", "exp" => ""];
                }

                /****************************************CREA LA SERIE *********************/
                $serie = '';
                $serie = date('YmdHis') . $id_serie;
                $id_serie++;

                // New Cambios realizados para kardex
                $producto = Producto::find($producto_insumo->id_insumo);
                $cantidad_usos_kardex = 0;
                if ($producto->kardex_uso == 1) {
                    $cantidad_usos_kardex = $producto->cantidad_usos_kardex;
                }
                if ($producto->usos != null) {
                    $cant_uso = $producto->usos;
                }
                if ($cant_uso == null or $cant_uso < 0) {
                    $cant_uso = 0;
                }

                if (isset($documentos->id) and $documentos->tipo != '') {
                    $tipo = $documentos->tipo;
                } else {
                    $tipo = 'F';
                }


                $inventario = InvInventario::getInventario($producto->id, $bodega_recibe, $tipo);
                if ($inventario == '[]') {
                    $inventario = InvInventario::setNeoInventario($producto->id, $bodega_recibe, $tipo, 0, 0);
                }
                $det_mov_inv                         = new InvDetMovimientos;
                $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                $det_mov_inv->id_producto            = $producto_insumo->id_insumo;
                $det_mov_inv->id_inv_inventario      = $inventario->id;
                $det_mov_inv->cantidad               = $detail->cantidad;
                $det_mov_inv->cant_uso               = $detail->cantidad * $cant_uso;
                $det_mov_inv->serie                  = $serie;
                $det_mov_inv->lote                   = $detail->lote;
                $det_mov_inv->fecha_vence            = $detail->fecha_exp;
                $det_mov_inv->valor_unitario         = $detail->costo_unitario;
                $det_mov_inv->subtotal               = $detail->cantidad * $detail->costo_unitario;
                $det_mov_inv->descuento              = $detail->descuento;
                $det_mov_inv->iva                    = 0;
                $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                $det_mov_inv->motivo                 = "INGRESO DE IMPORTACION (ID): {$cab_compra->id} ";
                $det_mov_inv->id_pedido              = null;
                // $det_mov_inv->id_detalle_origen         = $id_movimiento;
                // $det_mov_inv->id_detalle_pedido = $id_movimiento;
                /// KARDEX POR USOS VICTOR
                $det_mov_inv->cantidad_usos_kardex = $cantidad_usos_kardex;
                ////////
                $det_mov_inv->ip_creacion       = $ip_cliente;
                $det_mov_inv->ip_modificacion   = $ip_cliente;
                $det_mov_inv->id_usuariocrea    = $id_usuario;
                $det_mov_inv->id_usuariomod     = $id_usuario;
                $det_mov_inv->save();

                $total += $detail->cantidad * $detail->costo_unitario;
            }
            $kardex_funcion  = InvKardex::setKardex($cab_mov_inv->id);
            InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);

            Log_Importaciones_Movimiento::create([
                'id_importaciones'          => $id_importacion,
                'id_cab_movimiento'         => $cab_mov_inv->id,
                'id_usuariocrea'            => $id_usuario,
                'id_usuariomod'             => $id_usuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
            ]);


            // $cab_movimiento2 = InvCabMovimientos::find($cab_mov_inv->id);
            // $cab_movimiento2->subtotal              = $total;
            // $cab_movimiento2->subtotal_0            = $total;
            // $cab_movimiento2->total                 = $total;
            // $cab_movimiento2->save();
            DB::commit();
            return ['status' => 'success', "msj" => 'Guardado con exito'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage(), "e" => $e,  "codigo" => $count, 'linea' => $e->getLine()];
        }
    }

    public static function cambiarNombreProducto($id_cabecera)
    {

        try {
            $detalles = Inv_Det_Carga_Inicial::where('id_cab', $id_cabecera)->where('cambiar_nombre', 1)->where('problema', 0)->where('estado', 1)->groupBy('codigo', 'descripcion')->select('codigo', 'descripcion')->get();
            foreach ($detalles as $detalle) {
                $producto = Producto::where("codigo", $detalle->codigo)->first();
                if (!is_null($producto)) {

                    $data_cambio = [
                        "id_producto"       => $producto->id,
                        "nombre_ant"        => $producto->nombre,
                        "nombre_nuevo"      => $detalle->descripcion,
                        'id_usuariocrea'    => Auth::user()->id,
                        'id_usuariomod'     => Auth::user()->id,
                        'ip_modificacion'   => $_SERVER["REMOTE_ADDR"],
                        'ip_creacion'       => $_SERVER["REMOTE_ADDR"],
                    ];

                    DB::table("log_cambio_nombre")->insert($data_cambio);

                    $producto->nombre       = $detalle->descripcion;
                    $producto->descripcion  = $detalle->descripcion;
                    $producto->id_usuariomod = Auth::user()->id;
                    $producto->ip_modificacion = $_SERVER['REMOTE_ADDR'];
                    $producto->save();
                } else {
                    return ["status" => "error", "msj" => "No se encontro el producto"];
                }
            }
            return ["status" => "success", "msj" => "Cambiado con exito"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => "No se pudo actualizar...", "exp" => $e->getMessage()];
        }
    }


    public function delete($id)
    {
        $cab = Inv_Cab_Carga_Inicial::find($id);
        if ($cab->procesado == 1) {
            return ["status" => "error", "msj" => "No se pudo eliminar <br>Ya se proceso esta carga"];
        }
        DB::beginTransaction();
        try {
            $cab->estado = 0;
            $cab->ip_modificacion = $_SERVER["REMOTE_ADDR"];
            $cab->id_usuariomod         = Auth::user()->id;
            $cab->save();

            DB::commit();
            return ['status' => "success", "msj" => "Se elimino con exito"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "No se pudo eliminar", "exp" => $e->getMessage()];
        }
    }


    public function crearSerie()
    {
        $cabeceras = Inv_Cab_Carga_Inicial::where('estado', 1)->where('procesado', 1)->where('id_empresa', Session::get('id_empresa'))->get();
        $contador  = 1;
        // dd(Session::get('id_empresa'));
        DB::beginTransaction();
        try {
            foreach ($cabeceras as $cab) {
                $detalles = Inv_Det_Carga_Inicial::where('id_cab', $cab->id)->groupBy('serie', 'id_bodega', 'codigo')->get(['serie', 'id_bodega', 'codigo']);
                // dd($detalles);
                foreach ($detalles as $det) {
                    $serie = "";
                    if (is_null($det->serie)) {
                        $serie = date('YmdHis') . $contador;
                        $contador++;
                    }
                    if ($det->serie == '0') {
                        $serie = date('YmdHis') . $contador;
                        $contador++;
                    }
                    if ($det->serie == "N/A") {
                        $serie = date('YmdHis') . $contador;
                        $contador++;
                    }
                    if (trim($det->serie) == "") {
                        $serie = date('YmdHis') . $contador;
                        $contador++;
                    }
                    if ($serie != '') {
                        $producto = Producto::where('codigo', $det->codigo)->first();
                        if (!is_null($producto)) {
                            $inv_serie = InvInventarioSerie::where('id_producto', $producto->id)->where('id_bodega', $det->id_bodega)->where('serie', $det->serie)->where('id_empresa', $cab->id_empresa)->get();
                            $inv_det = InvDetMovimientos::where('id_producto', $producto->id)->where('serie', $det->serie)->get();

                            foreach ($inv_serie as $val_serie) {
                                $val_serie->serie = $serie;
                                $val_serie->ip_modificacion = "Se creo la serie";
                                $val_serie->save();
                            }

                            foreach ($inv_det as $invdet) {
                                $invdet->serie = $serie;
                                $invdet->ip_modificacion = "Se creo la serie";
                                $invdet->save();
                            }

                            $cam_serie_det = Inv_Det_Carga_Inicial::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('codigo', $det->codigo)->get();

                            foreach ($cam_serie_det as $cambio) {
                                $cambio->serie = $serie;
                                $cambio->observacion = "<span class='label label-info'>No se envio la serie, se creara la serie<br>Nueva Serie: <b>{$serie}<b></span> <br>";
                                $cambio->ip_modificacion = "Se creo la serie";
                                $cambio->save();
                            }
                        }
                    }
                }
            }
            DB::commit();
            return ["status" => "success", "msj" => "Editado con exito"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "No se pudo actualizar", "exp" => $e->getMessage()];
        }
    }




    public function masivoExcelGastritech($nombre)
    {
        Excel::filter('chunk')->load("{$nombre}.xlsx")->chunk(600, function ($reader) {

            $prod_contable = Ct_productos::find(4119);

            $ct_maestro = $prod_contable["original"];
            unset($ct_maestro["id"]);
            foreach ($reader as $book) {
                if (!is_null($book)) {
                    $marca = Marca::where("nombre", $book->marca)->first();
                    $verificar_producto = Ct_productos::where('codigo', $book->codigo)->first();
                    // CREA EN CONTABILIDAD
                    if (!is_null($verificar_producto)) {
                        $verificar_producto->ip_modificacion  = "MSV-CARGA_INICIAL";
                        $verificar_producto->id_empresa       = "0993069299001";
                        $verificar_producto->codigo           = $book->codigo;
                        $verificar_producto->nombre           = $book->nombre;
                        $verificar_producto->codigo_barra     = $book->nombre;
                        $verificar_producto->descripcion      = $book->nombre;
                        $verificar_producto->estado_tabla     = 1;
                        $verificar_producto->marca            = $marca->id;
                        $verificar_producto->save();
                        $id_producto_cont = $verificar_producto->id;
                    } else {
                        $ct_maestro["id_empresa"]       = "0993069299001";
                        $ct_maestro["codigo"]           = $book->codigo;
                        $ct_maestro["nombre"]           = $book->nombre;
                        $ct_maestro["codigo_barra"]     = $book->nombre;
                        $ct_maestro["descripcion"]      = $book->nombre;
                        $ct_maestro["estado_tabla"]     = 1;
                        $ct_maestro["iva"]              = 1;
                        $ct_maestro["marca"]            = $marca->id;
                        $ct_maestro["ip_modificacion"]  = "MSV-CARGA_INICIAL";
                        $id_producto_cont = Ct_productos::insertGetId($ct_maestro);
                    }

                    //CREA EN PRODUCTOS

                    $producto = Producto::where("codigo", $book->codigo)->first();
                    if (is_null($producto)) {
                        $data = [
                            "nombre"                => $book->nombre,
                            "descripcion"           => $book->nombre,
                            "estado"                => 1,
                            "medida"                => "mm",
                            "minimo"                => "1",
                            "codigo"                => $book->codigo,
                            "codigo_iess"           => $book->codigo,
                            "despacho"              => 0,
                            "cantidad"              => 999,
                            "tipo_producto"         => 1,
                            "cantidad_unidad"       => 999,
                            "id_marca"              => $marca->id,
                            "registro_sanitario"    => $book->codigo,
                            "usos"                  => 1,
                            "codigo_siempre"        => 0,
                            "id_usuariocrea"        => "0957258056",
                            "id_usuariomod"         => "0957258056",
                            "ip_creacion"           => "::1",
                            "ip_modificacion"       => "MASIVO_GTECH",
                            "iva"                   => 1,
                            "descuento"             => 0,
                            "tipo"                  => 0,
                        ];
                        $id_producto = Producto::insertGetId($data);
                    } else {
                        $producto->nombre                = $book->nombre;
                        $producto->descripcion           = $book->nombre;
                        $producto->estado                = 1;
                        $producto->codigo                = $book->codigo;
                        $producto->codigo_iess           = $book->codigo;
                        $producto->id_marca              = $marca->id;
                        $producto->registro_sanitario    = $book->codigo;
                        $producto->id_usuariocrea        = "0957258056";
                        $producto->id_usuariomod         = "0957258056";
                        $producto->ip_creacion           = "::1";
                        $producto->ip_modificacion       = "MASIVO_GTECH";
                        $producto->iva                   = 1;
                        $producto->descuento             = 0;
                        $producto->tipo                  = 0;
                        $producto->save();
                        $id_producto = $producto->id;
                    }
                    $ct_prod_insumo = Ct_productos_insumos::where("id_insumo", $id_producto)->where("id_producto", $id_producto_cont)->first();
                    if (is_null($ct_prod_insumo)) {
                        $producto_insumo = Ct_productos_insumos::create([
                            "codigo_producto"   => $book->codigo,
                            "id_insumo"         => $id_producto,
                            "id_producto"       => $id_producto_cont,
                            "id_usuariocrea"    => "0957258056",
                            "id_usuariomod"     => "0957258056",
                            "ip_creacion"       => "::1",
                            "ip_modificacion"   => "MASIVO_GTECH",
                        ]);
                    }
                }
            }
        });
        dd("OK GRACIAS AMIGO");
    }

    public function nuevo_reporte_plantilla(Request $request)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        Excel::create('Plantilla', function ($excel) {
            $excel->sheet('Plantilla', function ($sheet) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L");
                $i = 1;
                $arrTitulos = ["MARCA", "DETALLE", "CODIGO", "CANTIDAD", "LOTE", "FECHA_EXP", "PEDIDO", "SERIE", "TIPO", "PRECIO", "FECHA", "BODEGA"];
                $comienzo = 1;
                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
            });
        })->export('xlsx');
    }

    public function corregirDescripcionProducto()
    {
        $cab_carga = Inv_Cab_Carga_Inicial::where("estado", 1)->where('procesado', 1)->get();
        foreach ($cab_carga as $cc) {
            foreach ($cc->detalles as $cd) {
                $productos = Producto::where('codigo', $cd->codigo)->first();
                if (!is_null($productos)) {
                    $producto_insumos = Ct_productos_insumos::where("id_insumo", $productos->id)->first();
                    if (!is_null($producto_insumos)) {
                        $ct_productos = Ct_productos::find($producto_insumos->id_producto);
                        if (!is_null($ct_productos)) {
                            $productos->nombre = $ct_productos->nombre;
                            $productos->descripcion = $ct_productos->descripcion;
                            $productos->save();

                            $cd->descripcion = $ct_productos->nombre;
                            $cd->descripcion1 = $ct_productos->descripcion;
                            $cd->save();
                        }
                    }
                }
            }
        }

        return ["status" => true, "msj" => "Ok Gracias amigo"];
    }

    public function corregirFechaVenceProducto($id)
    {
        $cab_carga = Inv_Cab_Carga_Inicial::find($id);
        $url = "/storage/app/avatars/{$cab_carga->nuevo_nombre}";

        Excel::filter('chunk')->load($url)->chunk(250, function ($reader) use ($cab_carga) {

            foreach ($reader as $book) {
                if (!is_null($book->fecha_de_vencimiento) || strtotime($book->fecha_de_vencimiento) == false || !is_null($book->fecha_de_vencimiento)) {
                    $unix_date  = ($book->fecha_de_vencimiento - 25569) * 86400;
                    $excel_date = 25569 + ($unix_date / 86400);
                    $unix_date  = ($excel_date - 25569) * 86400;
                    $fecha_de_vencimiento  = gmdate("Y-m-d", intval($unix_date));
                } else if ($book->fecha == "" || is_null($book->fecha) || $book->fecha == ' ' || is_null($book->fecha)) {
                    $fecha_de_vencimiento = date("Y") . "-12-31";
                }

                $det_carga = Inv_Det_Carga_Inicial::where('id_cab', $cab_carga->id)->where('codigo', str_replace("\t", "", str_replace("-", "", $book->codigo)))->where('lugar', $book->bodega)
                    ->where('cantidad', $book->cantidad)->where('lote', $book->lote)->where('serie', str_replace("-", "", $book->serie))->first();
                // if(str_replace("\t", "", str_replace("-", "", $book->serie) == "2022081110564828")){
                //     dd($cab_carga->id, str_replace("\t", "", str_replace("-", "", $book->codigo)), $book->codigo, $book->bodega, $book->cantidad, $book->lote, $book->serie,$det_carga);
                //     // dd(str_replace("\t", "", str_replace("-", "", $book->codigo)));

                // }

                if (!is_null($det_carga)) {

                    $det_movimientos = InvDetMovimientos::where("serie", $det_carga->serie)->where('motivo', 'like', "%CARGA DE DATOS 02/09/2023%");
                    $inv_serie = InvInventarioSerie::where("serie", $det_carga->serie)->where("id_empresa", $cab_carga->id_empresa)->where("existencia", '>', 0);
                    // if (count($det_movimientos) > 0) {
                    // dd($det_movimientos->get());
                    $det_movimientos->update([
                        'fecha_vence' => $fecha_de_vencimiento,
                    ]);
                    // }
                    // if (count($inv_serie) > 0) {
                    $inv_serie->update([
                        'fecha_vence' => $fecha_de_vencimiento,
                    ]);
                    // }
                    $det_carga->ip_creacion = $det_carga->fecha_exp;
                    $det_carga->fecha_exp = $fecha_de_vencimiento;
                    $det_carga->save();
                }
            }
            dd("ok gracias amigo");
        });
    }


    public function verificarSerieVenta()
    {
        $ventas_kardex = Venta_Kardex::where("estado", 1)->get();

        $errores = [];

        foreach ($ventas_kardex as $vk) {
            $ventas = Ct_ventas::find($vk->id_venta);
            foreach ($ventas->detalles as $dt_venta) {
                if (!is_null($dt_venta->id_inv_serie) or $dt_venta->id_inv_serie != "") {
                    $producto = Ct_productos::where("codigo", $dt_venta->id_ct_productos)->first();
                    $prod_insumos = Ct_productos_insumos::where("id_producto", $producto->id)->first();

                    $union_bodega  = Union_Bodega::where('id_bodega_cont', $dt_venta->bodega)->first();

                    $inv_det_movimiento = InvDetMovimientos::where("id_inv_cab_movimientos", $vk->id_cab_moviento)
                        ->where('id_producto', $prod_insumos->id_insumo)->where('cantidad', $dt_venta->cantidad)->get();


                    $inv_serie = InvInventarioSerie::find($dt_venta->id_inv_serie);


                    $validar = false;

                    // dd($inv_det_movimiento, $inv_serie, $vk, $vk->id_cab_movimiento, $prod_insumos->id_insumo, $dt_venta->cantidad);

                    foreach ($inv_det_movimiento as $idm) {
                        if ($idm->serie == $inv_serie->serie) {
                            $validar = true;
                        }
                    }



                    if (!$validar) {
                        dd($inv_serie, $inv_det_movimiento);
                        array_push($errores, ["id_venta" => $vk->id_venta, "id_cab_movimiento" => $vk->id_cab_moviento,  "serie" => $inv_serie->serie, "prod_contable" => $dt_venta->id_ct_productos, "prod_compras" => $prod_insumos->producto_compra->codigo]);
                    }
                }
            }
        }

        dd($errores);
    }

    public function recalcularCargaInicial()
    {
        try {
            $allCargas = InvCabMovimientos::where("observacion", "like", "%CARGA DE DATOS %")->where("estado", 1)->get();
            foreach ($allCargas as $carga) {
                InvCabMovimientos::calcularTotalCabMovimiento($carga->id);
            }
            return ["status" => "success", "msj" => "ok gracias amigo"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }
}
