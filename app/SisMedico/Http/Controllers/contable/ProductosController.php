<?php

namespace Sis_medico\Http\Controllers\contable;

//use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_configuraciones_pdf;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_procedimientos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Ct_Producto_Tarifario_Paquete;
use Sis_medico\Empresa;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\Insumos\PlantillaController;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Procedimiento_Detalle_Honorario;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\TipoHonorario;
use Sis_medico\User;
use Sis_medico\ControlInventario;
use Sis_medico\Log_usuario;
use Excel;
use Exception;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\InvInventario;


class ProductosController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 22, 7)) == false) {
            return true;
        }
    }
    public function opcionesProductos(Request $r)
    {
        if ($r->opcion == '') {
            echo '<form method="post" action="opcionesProductos/subirExcel" enctype="multipart/form-data">
            ' . csrf_field() . '
            <table>
            <tr>
            <td><input type="file" name="archivo" id="archivo"  /></td>
            </tr>
            <tr>
            <td><input type="submit" value="Subir Excel" /></td>
            </tr>
            </table>
            </form>';
        } elseif ($r->opcion == 'subirExcel') {
            ini_set('max_execution_time', '300');
            $datosImportar = [];
            $arrayTotal = [];
            DB::beginTransaction();
            try {
                //echo '<pre>';print_r($_FILES['archivo']);exit;
                $path = $r->file('archivo')->getRealPath();
                //$path =$_FILES['temp'];
                Excel::filter('chunk')->load($path)->chunk(200, function ($datos) {
                    if (!empty($datos)) {
                        $arrayP = [];
                        $arrayCt = [];
                        $arrayAp = [];
                        $dateActu = 'Actu_Masi_' . date('Y-m-d H:i:s');
                        foreach ($datos as $hoja) {
                            $producto = Producto::where('id', (int)$hoja->id)
                                ->where('estado', 1)
                                ->first();
                            // if(is_null($producto)){
                            //     dd($hoja,(int)$hoja->id,$producto);
                            // }
                            // if ($producto == '') {
                            //     echo '<pre>';
                            //     print_r($hoja);
                            //     exit;
                            // }
                            if (!is_null($producto)) {
                                $arrayProducto = [
                                    'estado' => 0,
                                    'ip_modificacion' => $dateActu,
                                ];
                                Producto::where('id', (int)$hoja->id)->update($arrayProducto);
                                //array_push($arrayP, $arrayProducto);
                                $productosInsumos = Ct_productos_insumos::where('codigo_producto', $producto->codigo)->first();
                                if (!is_null($productosInsumos)) {
                                    $ctProducto = Ct_productos::where('id', $productosInsumos->id_producto)->first();
                                    $arrayCtProducto = [
                                        'estado_tabla' => 0,
                                        'ip_modificacion' => $dateActu,
                                    ];
                                    Ct_productos::where('id', $productosInsumos->id_producto)->update($arrayCtProducto);
                                    //array_push($arrayCt, $arrayCtProducto);
                                    $apProcedimiento = Ap_Procedimiento::where('id_producto', $productosInsumos->id_producto)->first();
                                    $arrayApProcedimiento = [
                                        'estado' => 0,
                                        'ip_modificacion' => $dateActu,
                                    ];
                                    Ap_Procedimiento::where('id_producto', $productosInsumos->id_producto)->update($arrayApProcedimiento);
                                    if ($apProcedimiento != '') {
                                        //array_push($arrayAp, $arrayApProcedimiento);
                                    }
                                    $arrayTotal = [
                                        'Productos' => $arrayP,
                                        'Ct_Productos' => $arrayCt,
                                        'Ap_Procedimiento' => $arrayAp,
                                    ];
                                    //array_push($datosImportar, $arrayTotal);
                                }
                            }
                        }
                    }
                });
                DB::commit();
                echo json_encode(['msj' => 'ok']);
            } catch (Exception $e) {
                DB::rollback();
                $datosImportar = [
                    'msj' => 'Un producto no tiene codigo asigando en su archivo',
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine(),

                ];
            }
            return json_encode($datosImportar);
        } elseif ($r->opcion == 'buscarSecuencia') {
            return json_encode($r->input());
        }
    }
    public function productos_tarifario()
    {
        $seguros   = Ct_Productos_Tarifario::groupby('id_producto')->get();
        $seg       = Ct_Productos_Tarifario::orderby('id_seguro')->orderby('nivel')->groupBy(DB::raw('id_seguro, nivel'))->get();
        $productos = Ct_productos::where('estado_tabla', '1')->paginate(5);
        //dd($seg);

        return view('contable/productos_tarifario/index', ['seguros' => $seguros, 'seg' => $seg, 'productos' => $productos, 'nombre' => '']);
    }

    public function crear()
    {
        $seguros   = Seguro::where('inactivo', '1')->orderBy('nombre')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();

        return view('contable/productos_tarifario/create', ['seguros' => $seguros, 'productos' => $productos]);
    }

    public function configuraciones_pdf(Request $request)
    {

        $empresas       = Empresa::all();
        $id_empresa     = $request->session()->get('id_empresa');
        $nombre_empresa = Empresa::where('id', $id_empresa)->first();
        $empresa        = Empresa::where('id', $id_empresa)->first();
        //dd($nombre_empresa);
        return view('contable/configuracion_pdf/create', ['empresas' => $empresas, 'id_empresa' => $id_empresa, 'nombre_empresa' => $nombre_empresa, 'empresa' => $empresa]);
    }
    public function guardar_confi(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $obj        = [
            'fech_autorizacion' => $request['fecha'],
            'id_empresa'        => $request['empresa'],
            'detalle'           => $request['detalle'],
            'autorizacion'      => $request['autorizacion'],
            'estado'            => $request['estado'],
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        //dd($obj);
        Ct_configuraciones_pdf::create($obj);
        return redirect()->route('configuraciones_pdf_index');
    }
    public function editar_pdf($id, Request $request)
    {

        $edit       = Ct_configuraciones_pdf::find($id);
        $nombre     = Empresa::where('id', $edit->id_empresa)->first();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        return view('contable/configuracion_pdf/edit', ['edit' => $edit, 'nombre' => $nombre, 'empresa' => $empresa]);
    }

    public function actualizar_pdf(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id            = $request['id'];
        $tipo_ambiente = Ct_configuraciones_pdf::findOrFail($id);

        $input = [

            'fech_autorizacion' => $request['fecha'],
            'detalle'           => $request['detalle'],
            'autorizacion'      => $request['autorizacion'],
            'estado'            => $request['estado'],
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,

        ];

        $tipo_ambiente->update($input);

        return redirect()->route('configuraciones_pdf_index');
    }

    public function index_confi(Request $request)
    {

        $empresas   = Empresa::all();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $confi      = Ct_configuraciones_pdf::where('id_empresa', $id_empresa)->paginate(5);
        return view('contable/configuracion_pdf/index', ['empresas' => $empresas, 'confi' => $confi, 'empresa' => $empresa]);
    }

    public function guardar(Request $request)
    {

        //return $request['id_producto'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $id_paquete = $request['id_paq'];

        $existe_prod_tar = Ct_Productos_Tarifario::where('id_seguro', $request['id_seguro'])
            ->where('nivel', $request['id_nivel'])
            ->where('id_producto', $request['id_producto'])
            ->where('estado', '1')
            ->first();

        if (!is_null($existe_prod_tar)) {

            $msj = "ok";

            return ['msj' => $msj];
        } else {

            if ($request['id_seguro'] == 1) {

                Ct_productos::where('id', $request['id_producto'])->update(['valor_total_paq' => $request['precio']]);

                return "ok";
            }

            if ($request['id_seguro'] != 1) {

                $arr = [
                    'id_seguro'       => $request['id_seguro'],
                    'nivel'           => $request['id_nivel'],
                    'id_producto'     => $request['id_producto'],
                    'precio_producto' => $request['precio'],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                $id_prod_tar = Ct_Productos_Tarifario::insertGetId($arr);

                return "ok";
            }
        }

        //Insertamos data a la tabla ct_producto_tarifario_paquete
        /*$input = [

        'id_producto_tarifario'  => $id_prod_tar,
        'id_paquete'             => $id_paquete,
        'id_usuariocrea'         => $idusuario,
        'id_usuariomod'          => $idusuario,
        'ip_creacion'            => $ip_cliente,
        'ip_modificacion'        => $ip_cliente,
        ];*/

        //Ct_Producto_Tarifario_Paquete::create($input);

    }

    //GUARDA TARIFARIO PAQUETE
    public function guardar_tarifario_paquete(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_prod_paq = $request['id_prod_paq'];

        $existe_prod_tar_paq = Ct_Producto_Tarifario_Paquete::where('id_seguro', $request['id_seguro'])
            ->where('id_nivel', $request['id_nivel'])
            ->where('id_producto', $request['id_prod'])
            ->where('id_paquete', $request['id_paq'])
            ->where('estado', '1')
            ->first();

        if (!is_null($existe_prod_tar_paq)) {

            $msj = "ok";

            return ['msj' => $msj];
        } else {

            $input = [

                'id_producto_paquete' => $id_prod_paq,
                'id_producto'         => $request['id_prod'],
                'id_paquete'          => $request['id_paq'],
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'cantidad'            => $request['cantidad'],
                'precio'              => $request['precio'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            Ct_Producto_Tarifario_Paquete::create($input);

            return "ok";
        }
    }

    public function edit($id_producto)
    {

        $prod_tari = Ct_Productos_Tarifario::where('id_producto', $id_producto)->where('estado', '1')->join('ct_productos as prod', 'prod.id', 'ct_producto_tarifario.id_producto')->select('prod.nombre', 'ct_producto_tarifario.id_producto', 'prod.codigo', 'prod.id as id_producto', 'ct_producto_tarifario.id_seguro', 'ct_producto_tarifario.precio_producto', 'ct_producto_tarifario.nivel')->get();
        //dd($prod_tari);

        $seguros = Seguro::where('inactivo', '1')->orderBy('nombre')->get();

        return view('contable/productos_tarifario/edit', ['seguros' => $seguros, 'prod_tari' => $prod_tari]);
    }

    public function nivel(Request $request)
    {

        //dd($request->id_seguro);
        $id_empresa = $request->session()->get('id_empresa');

        $convenios = DB::table('convenio as c')
            ->where('c.id_seguro', $request->id_seguro)
            ->join('nivel as n', 'n.id', 'c.id_nivel')
            ->select('c.*', 'n.nombre', 'n.id as id_nivel')
            ->get();

        //dd($convenios);

        return view('contable/productos_tarifario/niveles', ['convenios' => $convenios]);
    }

    public function precios(Request $request)
    {
        $producto = Ct_productos::where('id', $request->id_producto)->first();

        $precio_prod = DB::table('precio_producto as pre_pro')->where('pre_pro.codigo_producto', $producto->codigo)->get();

        return view('contable/productos_tarifario/precios', ['precio_prod' => $precio_prod]);
    }

    public function update(Request $request, $id_producto)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $prod_tari = Ct_Productos_Tarifario::where('id_producto', $id_producto)->where('id_seguro', $request->id_seguro);
        $arr       = [
            'id_seguro'       => $request['id_seguro'],
            'nivel'           => $request['id_nivel'],
            'id_producto'     => $id_producto,
            'precio_producto' => $request['precio'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $prod_tari->update($arr);

        return "ok";
    }

    public function edit2($id_producto, $id_seguro)
    {
        //dd($prod_tari);
        $productos = Ct_productos::where('id', $id_producto)->where('estado_tabla', '1')->get();
        $seguros   = Seguro::where('id', $id_seguro)->where('inactivo', '1')->orderBy('nombre')->get();

        return view('contable/productos_tarifario/edit2', ['seguros' => $seguros, 'productos' => $productos]);
    }

    public function buscar(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $nombre    = $request['nombre'];
        $productos = Ct_productos::where('nombre', 'like', '%' . $nombre . '%')->where('estado_tabla', '1')->paginate(20);
        $seg       = Ct_Productos_Tarifario::orderby('id_seguro')->orderby('nivel')->groupBy(DB::raw('id_seguro, nivel'))->get();

        return view('contable/productos_tarifario/index', ['nombre' => $nombre, 'productos' => $productos, 'seg' => $seg]);
    }

    //Buscar Codigo Producto
    public function buscar_codigo_producto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $existe = Ct_productos::where('codigo', $request['cod_prod'])
            ->where('estado_tabla', '1')->where('id_empresa', $id_empresa)->first();

        if (!is_null($existe)) {

            $dato = ['existe' => $existe];

            return $dato;
        }
    }
    public function comparar($id, $ix, Request $request)
    {
        $movimiento = Movimiento_Paciente::where('id_hc_procedimientos', '8803')->get();
        //dd($movimiento);
        $fecha       = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }

        $plantilla = Insumo_Plantilla_Control::find('28');
        //http://192.168.59.38/sis_medico_prb/public/enfermeria/insumos/21949#recibir_equipo
        //dd($plantilla);
        $empresa = Empresa::find('0992704152001');
        return view('contable/productos/comparativo', ['id' => $id, 'movimiento' => $movimiento, 'fecha_desde' => $fecha, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'plantilla' => $plantilla]);
    }
    public function storeData(Request $request)
    {
        DB::beginTransaction();
        try {
            $ip_cliente  = $_SERVER["REMOTE_ADDR"];
            $idusuario   = Auth::user()->id;
            $id_planilla = Planilla::insertGetId([
                'codigo'              => $request->codigo,
                'id_agenda'           => $request->id_agenda,
                'fecha'               => date('Y-m-d H:i:s'),
                'id_hc_procedimiento' => $request->id_hc_procedimientos,
                'id_planilla'         => $request->id_planilla,
                'observacion'         => $request->observacion,
                'estado'              => 1,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            for ($i = 0; $i < count($request['cantidad']); $i++) {
                $check = "1";
                if (isset($request['check'][$i])) {
                    $check = 0;
                }
                Planilla_Detalle::create([
                    'id_planilla_cabecera' => $id_planilla,
                    'movimiento'           => $request['movimiento'][$i],
                    'cantidad'             => $request['cantidad'][$i],
                    'serie'                => $request['serie'][$i],
                    'lote'                 => $request['lote'][$i],
                    'check'                => $check,
                    'fecha_vencimiento'    => $request['fecha_vencimiento'][$i],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
        return response()->json('ok');
    }
    public function loadPrice(Request $request)
    {
        if (!is_null($request->price)) {
            $array = [
                'erty'  => $request->erty,
                'check' => $request->check,
                'eque'  => $request->eque,
                'pr'    => $request->pr,

            ];
        }
        return response()->json('ok');
    }
    public function index_comparar(Request $request)
    {
        if ($this->rol()) {

            return response()->view('errors.404');
        }
        // dd("xxx"); procedimientos
        $plantilla = Planilla::where('estado', 1)->where('nota_entrega', 0);
        if ($request->codigo != null) {
            $plantilla = $plantilla->where('codigo', $request->codigo);
        }
        if ($request->nombre != null) {
            $plantilla = $plantilla->where('observacion', 'LIKE', '%' . $request->nombre . '%');
        }
        $fecha_desde = $request->fecha_desde;
        if ($fecha_desde == null) {
            $fecha_desde = date('Y-m-d');
        }
        $fecha_hasta = $request->fecha_hasta;
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }
        if ($fecha_desde == null) {
            $plantilla = $plantilla->where('fecha', '<=', date('Y-m-d'));
        } else {
            $plantilla = $plantilla->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        $plantilla      = $plantilla->orderBy('fecha', 'DESC')->paginate(10);
        $empresa        = Empresa::find('0992704152001');
        $procedimientos = array();
        return view('contable.productos.index_comparativo', [
            'planilla' => $plantilla, 'empresa'       => $empresa, 'request'            => $request,
            'fecha_hasta'                                                   => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'procedimientos' => $procedimientos
        ]);
    }
    public function anular_comparativo($id, Request $request)
    {
        $plantilla = Planilla::find($id);
        if ($plantilla != null) {
            $idusuario                = Auth::user()->id;
            $plantilla->estado        = 0;
            $plantilla->id_usuariomod = $idusuario;
            $plantilla->save();
        }
        return redirect()->back();
    }
    public function anular_detalle(Request $request)
    {

        DB::beginTransaction();


        try {
            $planilla = Planilla_Detalle::find($request->id);
            if (!is_null($planilla)) {
                $cab = Planilla::find($planilla->id_planilla_cabecera);

                if ($cab->aprobado == 1) {
                    DB::rollBack();
                    return ["status" => "error", "msj" => "Ya esta aprobada la planilla", "exp" => ""];
                }

                $planilla->estado        = 0;
                $planilla->id_usuariomod = Auth::user()->id;
                $planilla->save();

                $movi_paciente = Movimiento_Paciente::find($planilla->id_movimiento_paciente);
                if (!is_null($movi_paciente)) {
                    $movi_paciente->delete();
                }
                DB::commit();
                return ["status" => "success", "msj" => "Eliminado con exito"];
            }
            DB::rollBack();
            return ["status" => "error", "msj" => "No se encontro el detalle", "exp" => ""];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
        }
        return response()->json('ok');
    }
    public function edit_comparar($id, Request $request)
    {
        $plantilla = Planilla::find($id);
        if ($plantilla->id_planilla == null || $plantilla->id_planilla == 0) {
            $plantilla->id_planilla = 1;
            $plantilla->save();
        }
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        return view('contable/productos/edit_comparativo', ['planilla' => $plantilla, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function storecheck(Request $request)
    {
        //dd($request->all());
        $check          = $request['check'];
        $id             = $request['id'];
        $detalle        = Planilla_Detalle::find($id);
        $detalle->check = $check;
        $detalle->save();
        return response()->json('ok');
    }
    public function storeAprobado(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $planilla   = Planilla::find($request['id_planilla']);

        $id_empresa = Session::get('id_empresa');
        $control_inventario = ControlInventario::where('id_empresa', $id_empresa)->where('estado', '0')->first();
        if (!is_null($control_inventario)) {
            $resp['status']  = 'error';
            $resp['message'] = "Aprobaci�n se encuentra en proceso, por favor espere a que termine y vuelva a intentar";
            return response()->json($resp);
        }

        if ($planilla->aprobado == 1) {
            $resp['status']  = 'error';
            $resp['message'] = "Ya se encuentra aprobado esta planilla";
            return response()->json($resp);
        }

        //Verificacion de stock de productos
        $verificarStockFun =  ProductosController::verificarStock($planilla->id);
        if ($verificarStockFun["status"] == "error") {
            $resp['status']  = 'error';
            $resp['message'] = $verificarStockFun['problem'];
            return response()->json($resp);
        }
        $array_control = [
            'id_empresa'            => Session::get('id_empresa'),
            'id_planilla_cabecera'  => $planilla->id,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        ];
        $id_control_inventario = ControlInventario::insertGetId($array_control);

        $resp       = array();
        if ($planilla != null and !is_null($request)) {
            if (!is_null($planilla->detalles)) {
                DB::beginTransaction();
                try {
                    $planilla->codigo          = $request->codigo;
                    $planilla->observacion     = $request->observacion;
                    $planilla->id_usuariomod   = $idusuario;
                    $planilla->ip_modificacion = $ip_cliente;
                    $planilla->aprobado        = 1;
                    $planilla->save();
                    foreach ($planilla->detalles_validos as $row) {

                        $this->corrige_existencias($row->serie, $row->id_bodega);
                        // $id_bodega              = env('BODEGA_EGR_PACI1', 2);
                        $id_bodega              = $row->id_bodega;
                        $id_agenda              = $planilla->id_agenda;
                        $id_hc_procedimientos   = $planilla->id_hc_procedimiento;
                        $cantidad               = $row->cantidad;
                        // $movimiento             = $row->tmovimiento;
                        $producto = Producto::where('codigo', $row->codigo)->first();
                        if (isset($producto->id)) {
                            $cantidad_uso = $cantidad * $producto->usos;
                        } else {
                            $cantidad_uso = 0;
                        }
                        $id_movimiento_paciente = null;
                        $movimiento_paciente    = Movimiento_Paciente::find($row->id_movimiento_paciente);
                        if (isset($movimiento_paciente->id)) {
                            $id_movimiento_paciente = $movimiento_paciente->id;
                        }
                        $inv_serie = InvInventarioSerie::where('serie', $row->serie)
                            ->where('id_bodega', $id_bodega)
                            ->where('id_producto', $producto->id)
                            ->where('id_empresa', Session::get('id_empresa'))
                            ->where('estado', 1)
                            ->first();
                        /*if(Auth::user()->id == '0941096372'){
                            $nueva_serie = InvInventarioSerie::where('serie', $row->serie)
                            ->where('id_empresa', Session::get('id_empresa'))
                             ->where('id_bodega', $id_bodega)
                             ->where('existencia', '>=', $cantidad)
                             ->first();
                             $nuevo_pedido = Movimiento::where('movimiento.serie', $nueva_serie->serie)
                             ->join('pedido', 'movimiento.id_pedido', '=', 'pedido.id')
                             ->where('pedido.id_empresa', Session::get('id_empresa'))
                             ->select('movimiento.*')
                             ->first();

                            //dd($nueva_serie);
                            if($nuevo_pedido == null){
                                dd($nueva_serie);
                            }
                           // dd($inv_serie);
                        }*/

                        if (isset($inv_serie->id) and ($id_agenda != null and $id_agenda != "") and ($id_hc_procedimientos != null and $id_hc_procedimientos != "") and $id_movimiento_paciente != null) {
                            //$id_egre_pac = InvCabMovimientos::documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente, $id_bodega);
                            /* VICTOR KARDEX NUEVO */
                            $id_egre_pac = InvCabMovimientos::documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente, $id_bodega, $row->id);
                        }
                        /*else {
                    $resp['status'] = 'error';
                    $resp['message'] = 'Faltan datos! ';
                    return response()->json($resp);
                    }*/
                    }
                    // InvContableCab::setAsientoContableComprarativo($id_egre_pac);

                    DB::commit();
                    $resp['status']  = 'ok';
                    $resp['message'] = 'Plantilla validada con &eacute;xito';
                    $control_inventario = ControlInventario::find($id_control_inventario);
                    if (!is_null($control_inventario)) {
                        $control_inventario->update(['estado' => '1']);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $resp['status'] = 'error';
                    $resp['message'] = 'error: ' . $e->getMessage() . ' line:' . $e->getLine();

                    //return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'mod' => 'store', 'linea' => $e->getLine()];
                    return response()->json($resp);
                }
            } else {
                $resp['status']  = 'error';
                $resp['message'] = 'No se encontraron detalle de la plantilla';
                $control_inventario = ControlInventario::find($id_control_inventario);
                if (!is_null($control_inventario)) {
                    $control_inventario->update(['estado' => '1']);
                }
                return response()->json($resp);
            }
        }
        return response()->json($resp);
    }

    public function antesDeVictoverificarStock($id_planilla)
    {
        $id_empresa = Session::get('id_empresa');
        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega')->get();
        // dd($planilla_detalle);
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {

            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->where('existencia', '>=', $det->cantidad)->first();
            if (is_null($inv_serie)) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
            } else if ($inv_serie->existencia < $det->cantidad) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente | Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }

    public function verificarStock($id_planilla)
    {
        $id_empresa = Session::get('id_empresa');
        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega', 'codigo')->where('estado', 1)
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega', 'codigo')->get();
        // dd($planilla_detalle);
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {
            $producto = Producto::where('codigo', $det->codigo)->first();
            if (is_null($producto)) {
                dd($det);
            }

            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->where('id_producto', $producto->id)->where('existencia', '>=', $det->cantidad)->first();
            if (is_null($inv_serie)) {

                $inv_serie2 = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->first();
                if (!is_null($inv_serie2)) {
                    if ($inv_serie2->cantidad_usos_kardex != null && $inv_serie2->cantidad_usos_kardex > 0) {
                    } else {
                        $status = "error";
                        $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
                    }
                } else {
                    $status = "error";
                    $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
                }
            } else if ($inv_serie->existencia < $det->cantidad) {

                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente | Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }

    public function _verificarStock($id_planilla)
    {
        $planilla_cabecera = Planilla::find($id_planilla);

        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega')->get();
        // dd($planilla_detalle);
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {
            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('existencia', '>=', $det->cantidad)->where('id_empresa', $planilla_cabecera->id_empresa)->first();
            if (is_null($inv_serie)) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
            } else if ($inv_serie->existencia < $det->cantidad) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente | Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }

    public function NO_VALE_verificarStock($id_planilla)
    {
        // $planilla_cabecera = Planilla::find($id_planilla);
        $id_empresa = Session::get('id_empresa');
        // dd($id_empresa);
        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega')->get();
        // dd($planilla_cabecera);
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {
            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->first();
            if (!is_null($inv_serie)) {
                $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)
                    ->where('existencia', '>=', $det->cantidad)->first();
                if (is_null($inv_serie)) {
                    $status = "error";
                    $problemas .= "<span class='label label-danger'>Error:Cantidad Insuficiente | Serie: {$det->serie}</span> <br> <br>";
                }
            } else {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: No se encontro el insumo, realice un traslado<br> <br>| Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }

    public function modalAsiento(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $cuentas    = Plan_Cuentas::where('estado', 2)->get();
        $id         = $request->id;
        $planilla   = Planilla::find($id);
        $errores    = false;
        /*  $cantidad=Planilla_Detalle::where('id_planilla_cabecera',$request->id)->where('check',1)->where('estado',1)->sum('cantidad'); */
        $valor = Planilla_Detalle::where('id_planilla_cabecera', $request->id)->where('check', 1)->where('estado', 1)->get();
        $total = 0;
        foreach ($valor as $p) {
            $iva     = 0;
            $porcent = ((($p->cantidad * $p->precio) * 10) / 100);
            $subt    = ($p->cantidad * $p->precio);
            /*  if($p->producto->iva==1){
            $conf = Ct_Configuraciones::find(3);
            $iva  = ($subt) * $conf->iva;
            $total+= ($subt+$iva);
            }else{
            $total+= ($subt+$iva);
            } */
            $total += ($subt);
        }

        $seguro              = $planilla->agenda->seguro->id;
        $procedimiento       = $planilla->procedimiento->hc_procedimiento_f;
        $valor_procedimiento = 0;
        /*foreach ($procedimiento as $value) {
            $producto_procedimiento = Ct_productos_procedimientos::where('id_procedimiento', $value->id_procedimiento)->where('id_seguro', $seguro)->first();
            if (is_null($producto_procedimiento)) {

            } else {
                $valor_procedimiento = $producto_procedimiento->precio;
                if (is_null($valor_procedimiento)) {
                    $valor_procedimiento = 0;
                }
            }
        }*/
        $total += $valor_procedimiento;

        return view('contable.productos.modalAsiento', ['valor_procedimiento' => $valor_procedimiento, 'seguro' => $seguro, 'procedimiento' => $procedimiento, 'empresa' => $empresa, 'cuentas' => $cuentas, 'valor' => round($total, 2), 'planilla' => $planilla, 'errores' => $errores]);
    }
    public function storeAsiento(Request $request)
    {
        $id_planilla = $request->id;
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $planilla    = Planilla::find($id_planilla);
        $valor       = Planilla_Detalle::where('id_planilla_cabecera', $request->id)->where('check', 1)->get();
        $x           = 0;
        foreach ($valor as $p) {
            $x += $p->cantidad * $p->precio;
        }
        $tox                 = 0;
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId([
            'fecha_asiento'   => date('Y-m-d H:i:s'),
            'valor'           => $x,
            'observacion'     => 'Asiento de Costo Planilla -' . $planilla->planilla->nombre,
            'modulo'          => 'P',
            'id_empresa'      => $request->session()->get('id_empresa'),
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ]);
        //dd($request->all());
        for ($i = 0; $i < count($request->haber); $i++) {
            $debe = Plan_Cuentas::find($request->debe[$i]);
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $debe->id,
                'descripcion'         => $debe->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'haber'               => '0',
                'debe'                => $x,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            $haber = Plan_Cuentas::find($request->haber[$i]);
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $haber->id,
                'descripcion'         => $haber->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'debe'                => '0',
                'haber'               => $x,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        $planilla->id_asiento_cabecera = $id_asiento_cabecera;
        $planilla->save();
        return redirect()->back();
    }
    public function guardar_asiento_honorarios(Request $request)
    {
        //dd($request->all());
        $id_planilla = $request->id;
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $planilla    = Planilla::find($id_planilla);
        $valor_debe  = $request->valor_debe;
        $valor_haber = $request->valor_haber;
        $c_tipo     = TipoHonorario::find($request->tipo);

        $id_hc_procedimiento = $planilla->id_hc_procedimiento;
        $hcp                 = hc_procedimientos::find($id_hc_procedimiento);


        if ($request->debe == null || $request->haber == null) {
            return response()->json(['mensaje' => 'Seleccione una cuenta']);
        }
        if ($request->tipo == '1') {
            if ($planilla->id_asiento_medico != null) {
                return response()->json(['mensaje' => 'Asiento ya Generado']);
            }
        }
        if ($request->tipo == '2') {
            if ($planilla->id_asiento_anestesia != null) {
                return response()->json(['mensaje' => 'Asiento ya Generado']);
            }
        }
        //dd($planilla);

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId([
            'fecha_asiento'   => date('Y-m-d H:i:s'),
            'valor'           => $valor_debe,
            'observacion'     => 'Asiento de Costo  - ' . $c_tipo->nombre . ' - ' . $hcp->id,
            'modulo'          => 'P',
            'id_empresa'      => $request->session()->get('id_empresa'),
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ]);
        //dd($request->all());

        $debe = Plan_Cuentas::find($request->debe);
        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $debe->id,
            'descripcion'         => $debe->nombre,
            'fecha'               => date('Y-m-d H:i:s'),
            'haber'               => '0',
            'debe'                => $valor_debe,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);
        $haber = Plan_Cuentas::find($request->haber);
        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $haber->id,
            'descripcion'         => $haber->nombre,
            'fecha'               => date('Y-m-d H:i:s'),
            'debe'                => '0',
            'haber'               => $valor_haber,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);

        if ($request->tipo == '1') {
            $planilla->id_asiento_medico = $id_asiento_cabecera;
            $planilla->save();
        }
        if ($request->tipo == '2') {
            $planilla->id_asiento_anestesia = $id_asiento_cabecera;
            $planilla->save();
        }

        return response()->json(['mensaje' => 'ok']);
    }
    public function genera_asiento_honorarios($tipo, $id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //$cuentas    = Plan_Cuentas::where('estado', 2)->get();
        $cuentas = Plan_Cuentas::where('p.estado', '>', 0)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('p.plan as id', 'p.nombre as nombre', 'p.descripcion as descripcion', 'p.estado as estado')->get();
        $planilla   = Planilla::find($id);
        $errores    = false;
        $c_tipo     = TipoHonorario::find($tipo);

        $id_hc_procedimiento = $planilla->id_hc_procedimiento;
        $hcp                 = hc_procedimientos::find($id_hc_procedimiento);

        $plantillaController = new PlantillaController;
        $plantillaController->genera_honorarios($id_hc_procedimiento);

        $detalle_pdf = Procedimiento_Detalle_Honorario::where('procedimiento_detalle_honorario.id_hc_procedimientos', $id_hc_procedimiento)->join('procedimiento_honorario as ph', 'ph.id', 'procedimiento_detalle_honorario.id_proc_conve')->where('ph.tipo', $tipo)->where('procedimiento_detalle_honorario.estado', '1')->select('procedimiento_detalle_honorario.*')->get();
        $valor       = $detalle_pdf->sum('valor');
        $valor       = round($valor, 2);
        //dd($valor);

        return view('contable.productos.asiento_honorarios', ['planilla' => $planilla, 'hcp' => $hcp, 'empresa' => $empresa, 'cuentas' => $cuentas, 'valor' => $valor, 'planilla' => $planilla, 'c_tipo' => $c_tipo]);
    }

    public function reprocesar_serie($id)
    {
        $planilla = Planilla::findOrFail($id);
        if ($planilla->aprobado == 1) {
            return response()->json([
                'estado'    => 'error',
                'respuesta' => 'planilla ya aprobada',
                'valores'   => array()
            ]);
        }
        $controlador = false;
        $faltantes = array();
        $detalles = Planilla_Detalle::where('id_planilla_cabecera', $id)->where('check', 1)->where('estado', 1)->get();
        $usados = [];
        foreach ($detalles as $value) {
            $producto = Producto::where('codigo', $value->codigo)->first();
            if (is_null($producto)) {
                dd($value);
            }

            $serie = InvInventarioSerie::where('serie', $value->serie)
                ->where('id_empresa', Session::get('id_empresa'))
                ->where('id_bodega', $value->id_bodega)
                ->where('id_producto', $producto->id)
                ->where('estado', 1)
                ->first();
            

            if (!is_null($serie)) {
                $inv = InvInventario::where('id', $serie->id_inv_inventario)->where('id_producto', $serie->id_producto)->where('id_bodega', $serie->id_bodega)->where('id_empresa', $serie->id_empresa)->where('existencia', '>', '0')->first();
                if ($serie->producto->codigo_siempre == 1) {
                    $nueva_serie = null;
                    $nuevo_costo_pro = null;
                    $inv = null;

                    /*********************CAMBIOS PARA LAS CANTIDADES EN PRODUCTOS******************************/
                    if (isset($usados[$serie->producto->id][$value->serie])) {
                        $restante = $serie->existencia - ($usados[$value->producto->id][$value->serie] + $value->cantidad);

                        if ($restante < 0) {
                            $inv_series = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', Session::get('id_empresa'))
                                ->where('id', '<>', $serie->id)
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->get();

                            foreach ($inv_series as $inv_serie) {
                                $inv = $inv_serie;

                                if (isset($usados[$value->producto->id][$inv_serie->serie])) {
                                    $resta = $inv_serie->existencia - ($usados[$value->producto->id][$inv_serie->serie] + $value->cantidad);

                                    if ($resta >= 0) {
                                        $usados[$value->producto->id][$inv_serie->serie] += $value->cantidad;
                                        $nueva_serie = $inv_serie;
                                    }
                                } else {
                                    $usados[$value->producto->id][$inv_serie->serie] = $value->cantidad;
                                    $nueva_serie = $inv_serie;
                                }
                            }
                            $controlador = true;
                        } else {
                            $usados[$value->producto->id][$value->serie] += $value->cantidad;
                            $nueva_serie = $serie;
                        }
                    } else {
                        if ($serie->existencia >= $value->cantidad) {
                            $nueva_serie = $serie;
                            $usados[$value->producto->id][$nueva_serie->serie] = $value->cantidad;
                        } else {
                            if ($value->codigo == 'EG DES 00079' && Auth::user()->id == '0922729587') {
                                return response()->json([
                                    'estado'    => 'error',
                                    'respuesta' => 'Faltan Datos',
                                    'valores'   => "entra 44"
                                ]);
                            }
                            $nueva_serie = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', Session::get('id_empresa'))
                                // ->where('id', '<>', $serie->id)  
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->first();
                            if (is_null($nueva_serie)) {
                                $dd = array();
                                $dd['serie'] = $value->serie;
                                $dd['error'] = "No exiten productos en la Bodega";
                                $dd['producto'] = $value->codigo;
                                array_push($faltantes, $dd);
                                continue;
                            }

                            $inv = InvInventario::where('id', $nueva_serie->id_inv_inventario)->where('id_producto', $nueva_serie->id_producto)->where('id_bodega', $nueva_serie->id_bodega)->where('id_empresa', $nueva_serie->id_empresa)->where('existencia', '>', '0')->first();
                            $nuevo_costo_pro = $inv->costo_promedio;

                            if (!is_null($nueva_serie)) {
                                $usados[$serie->producto->id][$nueva_serie->serie] = $value->cantidad;
                                $inv = InvInventario::where('id', $nueva_serie->id_inv_inventario)->where('id_producto', $nueva_serie->id_producto)->where('id_bodega', $nueva_serie->id_bodega)->where('id_empresa', $nueva_serie->id_empresa)->where('existencia', '>', '0')->first();
                                $nuevo_costo_pro = $inv->costo_promedio;
                            }
                            $controlador = true;
                        }
                    }

                    if ($controlador) {

                        // if ($serie->existencia < $value->cantidad) {

                        // $nueva_serie = InvInventarioSerie::where('id_producto', $serie->id_producto)
                        //     ->where('id_empresa', Session::get('id_empresa'))
                        //     ->where('id_bodega', $value->id_bodega)
                        //     ->where('existencia', '>=', $value->cantidad)
                        //     ->first();

                        // dd($nueva_serie);

                        if (!is_null($nueva_serie) && (!is_null($inv))) {
                            DB::beginTransaction();

                            $nuevo_pedido = Movimiento::where('movimiento.serie', $nueva_serie->serie)
                                ->join('pedido', 'movimiento.id_pedido', '=', 'pedido.id')
                                ->where('pedido.id_empresa', Session::get('id_empresa'))
                                ->select('movimiento.*')
                                ->first();
                            try {
                                Planilla_Detalle::where('id', $value->id)
                                    ->update(['movimiento' => $nuevo_pedido->id, 'serie' => $nueva_serie->serie, 'precio' => $nuevo_costo_pro, 'ip_modificacion' => 'REPROCESO APROBADO', 'observacion' => 'REPROCESO APROBADO']);
                                Movimiento_Paciente::where('id', $value->id_movimiento_paciente)
                                    ->update(['id_movimiento' => $nuevo_pedido->id, 'ip_modificacion' => 'REPROCESO APROBADO']);
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollBack();

                                return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
                            }
                        } else {
                            $dd = array();
                            $dd['serie'] = $value->serie;
                            $dd['error'] = "No exiten productos en la Bodega";
                            $dd['producto'] = $value->codigo;
                            array_push($faltantes, $dd);
                        }
                        // }
                    }
                }
            } else {
                $dd = array();
                $dd['serie'] = $value->serie;
                $dd['error'] = "No existe la serie";
                $dd['producto'] = $value->codigo;
                array_push($faltantes, $dd);
            }
        }

        if ($faltantes == array()) {
            return response()->json([
                'estado'    => 'okay',
                'respuesta' => 'Faltan Datos',
                'valores'   => $faltantes
            ]);
        } else {
            return response()->json([
                'estado'    => 'error',
                'respuesta' => 'Faltan Datos',
                'valores'   => $faltantes
            ]);
        }
    }

    public function corrige_existencias($serie, $id_bodega)
    {

        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;

        $id_empresa = Session::get('id_empresa');

        $inv_serie = InvInventarioSerie::where('serie', $serie)->where('id_bodega', $id_bodega)->where('id_empresa', $id_empresa)->first();
        if ($inv_serie->cantidad_usos_kardex > 0) {
            if ($inv_serie->existencia == 0) {
                $inv_serie->update(['existencia' => 1]);
                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CORRIGE EXISTENCIAS DESDE APROBACION",
                    'dato_ant1'   => $serie,
                    'dato1'       => $id_bodega,
                    'dato_ant4'   => " ",
                    'dato2'       => ' ',
                ];

                Log_usuario::create($input_log);
            }
        }
    }

    public function masivo_crear_productos($nombre)
    {

        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {

            foreach ($reader as $book) {

                $cod_ant    = $book->cod_act;
                $cod_new    = $book->cod_nuevo;
                $desc       = $book->descripcion;
                $valor_pvp  = $book->pvp;
                $asisken    = $book->asisken;
                $salud3     = $book->salud3;
                $salud4     = $book->salud4;
                $salud5     = $book->salud5;

                $id_empresa = Session::get('id_empresa');

                $ip_cliente  = $_SERVER["REMOTE_ADDR"];
                $idusuario   = Auth::user()->id;

                $productos = Ct_productos::where('codigo', $cod_ant)->get(); //dd($productos);

                if (!is_null($productos)) {
                    foreach ($productos as $producto) {

                        //dd($producto);    
                        $producto->update([
                            'estado_tabla'     => 0,
                            'ip_creacion' => 'ACT_COD_PROD_170723',
                        ]);

                        $producto_codigo = Ct_productos::where('codigo', $cod_new)->first();

                        if (is_null($producto_codigo)) {

                            $id_prod_new = Ct_productos::insertGetId([
                                'codigo'           => $cod_new,
                                'nombre'           => $desc,
                                'tipo'             => $producto->tipo,
                                'grupo'            => $producto->grupo,
                                'reg_serie'        => $producto->reg_serie,
                                'mod_precio'       => $producto->mod_precio,
                                'mod_desc'          => $producto->mod_desc,
                                'stock_minimo'     => $producto->stock_minimo,
                                'impuesto_servicio' => $producto->impuesto_servicio,
                                'iva'              => $producto->iva,
                                'tipo_objeto'      => $producto->tipo_objeto,
                                'descripcion'      => $desc,
                                'estado_tabla'          => 1,
                                'precio1'          => $valor_pvp,
                                'valor_total_paq'  => $valor_pvp,

                                'id_empresa'       => $id_empresa,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     =>  'ACT_COD_PROD_170723',
                                'ip_modificacion' =>    $producto->id,
                            ]);
                        } else {

                            $producto_codigo->update([
                                'descripcion'     => $desc,
                                'precio1'          => $valor_pvp,
                                'valor_total_paq'  => $valor_pvp,

                                'id_usuariomod'   => '0941096372',
                                'ip_modificacion' => 'AC1707-' . $producto->id,
                            ]);

                            $id_prod_new = $producto_codigo->id;
                        }



                        $prod_ligue_ant =  Ct_productos_insumos::where('id_producto', $producto->id)->get();

                        //$prod_ligue = Ct_productos_insumos::where('id_producto',$producto->id)->get();

                        foreach ($prod_ligue_ant as $value) {
                            //dd($value);
                            $value->update([
                                'codigo_producto'  => $value->codigo_producto,
                                'id_insumo'        => $value->id_insumo,
                                'id_producto'      => $id_prod_new,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     =>  'ACT_COD_PROD_170723',
                                'ip_modificacion' =>  $producto->id,
                            ]);
                        }

                        $ap_ism = Ap_Procedimiento::where('id_producto', $producto->id)->get();
                        if (!is_null($ap_ism)) {
                            foreach ($ap_ism as $value1) {

                                $value1->update([
                                    'id_producto'     => $id_prod_new,
                                    'ip_creacion'     =>  $producto->id,

                                ]);
                            }
                        }

                        if ($asisken != 0 or $asisken != null) {
                            Ct_Productos_Tarifario::create([
                                'id_seguro'        => 16,
                                'nivel'            => 9,
                                'id_producto'      => $id_prod_new,
                                'precio_producto'  => $asisken,
                                'estado'           => 1,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $idusuario,
                            ]);
                        }
                        if ($salud3 != 0 or $salud3 != null) {
                            Ct_Productos_Tarifario::create([
                                'id_seguro'        => 7,
                                'nivel'            => 5,
                                'id_producto'      => $id_prod_new,
                                'precio_producto'  => $salud3,
                                'estado'           => 1,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $idusuario,
                            ]);
                        }
                        if ($salud4 != 0 or $salud4 != null) {
                            Ct_Productos_Tarifario::create([
                                'id_seguro'        => 7,
                                'nivel'            => 6,
                                'id_producto'      => $id_prod_new,
                                'precio_producto'  => $salud4,
                                'estado'           => 1,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $idusuario,
                            ]);
                        }
                        if ($salud5 != 0 or $salud5 != null) {
                            Ct_Productos_Tarifario::create([
                                'id_seguro'        => 7,
                                'nivel'            => 7,
                                'id_producto'      => $id_prod_new,
                                'precio_producto'  => $salud5,
                                'estado'           => 1,
                                'id_usuariocrea'  => '0941096372',
                                'id_usuariomod'   => '0941096372',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $idusuario,
                            ]);
                        }
                    }
                } else {
                    $input_log = [
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "MASIVO CREAR PROD 1707",
                        'dato_ant1'   => $cod_ant,
                        'dato1'       => "",
                        'dato_ant4'   => " ",
                        'dato2'       => ' ',
                    ];

                    Log_usuario::create($input_log);
                }
            }
        });

        return "elt";
    }

    public function masivo_crear_productos_nuevos($nombre)
    {

        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {

            foreach ($reader as $book) {

                $cod_ant    = $book->cod_act;
                $cod_new    = $book->cod_nuevo;
                $desc       = $book->descripcion;
                $valor_pvp  = $book->pvp;
                $asisken    = $book->asisken;
                $salud3     = $book->salud3;
                $salud4     = $book->salud4;
                $salud5     = $book->salud5;

                $id_empresa = Session::get('id_empresa');

                $ip_cliente  = $_SERVER["REMOTE_ADDR"];
                $idusuario   = Auth::user()->id;

                $producto = Ct_productos::where('codigo', $cod_new)->where('id_empresa', $id_empresa)->first(); //dd($productos);

                if (is_null($producto)) {

                    /*$id_prod_new = Ct_productos::insertGetId([
                        'codigo'           => $cod_new,
                        'nombre'           => $desc,
                        'tipo'             => $producto->tipo,
                        'grupo'            => $producto->grupo,
                        'reg_serie'        => $producto->reg_serie,
                        'mod_precio'       => $producto->mod_precio,  
                        'mod_desc'          => $producto->mod_desc,  
                        'stock_minimo'     => $producto->stock_minimo,
                        'impuesto_servicio' => $producto->impuesto_servicio,
                        'iva'              => $producto->iva,
                        'tipo_objeto'      => $producto->tipo_objeto,
                        'descripcion'      => $desc,
                        'estado_tabla'          => 1,
                        'precio1'          => $valor_pvp,
                        'valor_total_paq'  => $valor_pvp,

                        'id_empresa'       => $id_empresa,
                        'id_usuariocrea'  => '0941096372',
                        'id_usuariomod'   => '0941096372',
                        'ip_creacion'     =>  'MASIVO_CREA_COD_PROD',
                        'ip_modificacion' =>    $producto->id,
                    ]);*/
                } else {

                    $producto->update([
                        'descripcion'     => $desc,
                        'precio1'          => $valor_pvp,
                        'valor_total_paq'  => $valor_pvp,

                        'id_usuariomod'   => '0941096372',
                        'ip_modificacion' => 'AC6623-' . $producto->id,
                    ]);

                    $id_prod_new = $producto->id;
                }



                if ($asisken != 0 or $asisken != null) {
                    Ct_Productos_Tarifario::create([
                        'id_seguro'        => 16,
                        'nivel'            => 9,
                        'id_producto'      => $id_prod_new,
                        'precio_producto'  => $asisken,
                        'estado'           => 1,
                        'id_usuariocrea'  => '0941096372',
                        'id_usuariomod'   => '0941096372',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $idusuario,
                    ]);
                }

                if ($salud3 != 0 or $salud3 != null) {
                    Ct_Productos_Tarifario::create([
                        'id_seguro'        => 7,
                        'nivel'            => 5,
                        'id_producto'      => $id_prod_new,
                        'precio_producto'  => $salud3,
                        'estado'           => 1,
                        'id_usuariocrea'  => '0941096372',
                        'id_usuariomod'   => '0941096372',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $idusuario,
                    ]);
                }

                if ($salud4 != 0 or $salud4 != null) {
                    Ct_Productos_Tarifario::create([
                        'id_seguro'        => 7,
                        'nivel'            => 6,
                        'id_producto'      => $id_prod_new,
                        'precio_producto'  => $salud4,
                        'estado'           => 1,
                        'id_usuariocrea'  => '0941096372',
                        'id_usuariomod'   => '0941096372',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $idusuario,
                    ]);
                }

                if ($salud5 != 0 or $salud5 != null) {
                    Ct_Productos_Tarifario::create([
                        'id_seguro'        => 7,
                        'nivel'            => 7,
                        'id_producto'      => $id_prod_new,
                        'precio_producto'  => $salud5,
                        'estado'           => 1,
                        'id_usuariocrea'  => '0941096372',
                        'id_usuariomod'   => '0941096372',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $idusuario,
                    ]);
                }
            }
        });

        return "elt";
    }

    public function masivo_actualizar_productos($nombre)
    {

        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
            foreach ($reader as $book) {
                $cod_ant    = $book->cod_act;
                $cod_new    = $book->cod_nuevo;
                $desc       = $book->descripcion;
                $valor_pvp  = $book->pvp;
                $asisken    = $book->asisken;
                $salud3     = $book->salud3;
                $salud4     = $book->salud4;
                $salud5     = $book->salud5;
                $id_empresa = Session::get('id_empresa');
                $ip_cliente  = $_SERVER["REMOTE_ADDR"];
                $idusuario   = Auth::user()->id;

                $productos = Ct_productos::where('codigo', $cod_ant)->get();
                if (!is_null($productos)) {
                    foreach ($productos as $producto) {
                        //dd($producto);
                        $producto->update([
                            'precio1'          => $valor_pvp,
                            'valor_total_paq'  => $valor_pvp,
                            'nombre'           => $desc,
                            'descripcion'      => $desc,
                            'ip_creacion'      =>  'MASIVO_ACT_VAL_PROD_1707',
                        ]);

                        if ($asisken != 0 or $asisken != null) {
                            $tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('id_seguro', 16)->where('nivel', 9)->first();
                            if (!is_null($tarifario)) {
                                $tarifario->update([
                                    'precio_producto'  => $asisken,
                                    'ip_creacion'      =>  'MASIVO_ACT_VAL_PROD_1707',
                                ]);
                            } else {
                                Ct_Productos_Tarifario::create([
                                    'id_seguro'        => 16,
                                    'nivel'            => 9,
                                    'id_producto'      => $producto->id,
                                    'precio_producto'  => $asisken,
                                    'estado'           => 1,
                                    'id_usuariocrea'  => '0941096372',
                                    'id_usuariomod'   => '0941096372',
                                    'ip_creacion'      =>  'MASIVO_CRE_VAL_PROD_1707',
                                    'ip_modificacion' => $idusuario,
                                ]);
                            }
                        }
                        if ($salud3 != 0 or $salud3 != null) {
                            $tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('id_seguro', 7)->where('nivel', 5)->first();
                            if (!is_null($tarifario)) {
                                $tarifario->update([
                                    'precio_producto'  => $salud3,
                                    'ip_creacion'      =>  'MASIVO_ACT_VAL_PROD_1707',
                                ]);
                            } else {
                                Ct_Productos_Tarifario::create([
                                    'id_seguro'        => 7,
                                    'nivel'            => 5,
                                    'id_producto'      => $producto->id,
                                    'precio_producto'  => $salud3,
                                    'estado'           => 1,
                                    'id_usuariocrea'  => '0941096372',
                                    'id_usuariomod'   => '0941096372',
                                    'ip_creacion'      =>  'MASIVO_CRE_VAL_PROD_1707',
                                    'ip_modificacion' => $idusuario,
                                ]);
                            }
                        }
                        if ($salud4 != 0 or $salud4 != null) {
                            $tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('id_seguro', 7)->where('nivel', 6)->first();
                            if (!is_null($tarifario)) {
                                $tarifario->update([
                                    'precio_producto'  => $salud4,
                                    'ip_creacion'      =>  'MASIVO_ACT_VAL_PROD_1707',
                                ]);
                            } else {
                                Ct_Productos_Tarifario::create([
                                    'id_seguro'        => 7,
                                    'nivel'            => 6,
                                    'id_producto'      => $producto->id,
                                    'precio_producto'  => $salud4,
                                    'estado'           => 1,
                                    'id_usuariocrea'  => '0941096372',
                                    'id_usuariomod'   => '0941096372',
                                    'ip_creacion'      =>  'MASIVO_CRE_VAL_PROD_1707',
                                    'ip_modificacion' => $idusuario,
                                ]);
                            }
                        }
                        if ($salud5 != 0 or $salud5 != null) {
                            $tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('id_seguro', 7)->where('nivel', 7)->first();
                            if (!is_null($tarifario)) {
                                $tarifario->update([
                                    'precio_producto'  => $salud5,
                                    'ip_creacion'      =>  'MASIVO_ACT_VAL_PROD_1707',
                                ]);
                            } else {
                                Ct_Productos_Tarifario::create([
                                    'id_seguro'        => 7,
                                    'nivel'            => 7,
                                    'id_producto'      => $producto->id,
                                    'precio_producto'  => $salud5,
                                    'estado'           => 1,
                                    'id_usuariocrea'  => '0941096372',
                                    'id_usuariomod'   => '0941096372',
                                    'ip_creacion'      =>  'MASIVO_CRE_VAL_PROD_1707',
                                    'ip_modificacion' => $idusuario,
                                ]);
                            }
                        }
                    }
                }
            }
        });
        return "oki";
    }
}
