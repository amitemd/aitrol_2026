<?php

namespace Sis_medico\Http\Controllers\plantilla_labs;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Derivado;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\ApiHumanlabs;
use Sis_medico\Http\Controllers\Insumos\PlantillaControlController;
use Sis_medico\Http\Controllers\Insumos\PlantillaController;
use Sis_medico\Http\Controllers\plantilla_labs\PlantillaControlLabsController as Plantilla_labsPlantillaControlLabsController;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Planilla_Cabecera_Labs;
use Sis_medico\Planilla_Detalle_Labs;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\ModuloBodega;
use Sis_medico\Movimiento;
use Sis_medico\Planilla_Control_Labs;
use Sis_medico\Plantilla_Control_Labs;
use Sis_medico\Plantilla_Item_Control_Labs;
use Sis_medico\Plantilla_Tipo_Labs;
use Sis_medico\Producto;

use Storage;

class PlantillaControlLabsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    // private function rol()
    // {
    //     $rolUsuario = Auth::user()->id_tipo_usuario;
    //     if (in_array($rolUsuario, array(1, 4, 5, 20, 11)) == false) {
    //         return true;
    //     }
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orden_plantilla($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        //dd($orden);
        $empresa = Empresa::find(Session::get('id_empresa'));
        // dd($empresa, $orden);
        $planilla = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', '1')->first();
        $plantilla_control = Plantilla_Control_Labs::where('estado', 1)->where('id_empresa', $empresa->id)->get();
        $examenes = Examen::where('estado', 1)->get();
        $examen_det = $orden->detalles;
        $planilla_derivado = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', '3')->first();
        $derivados =  Examen_Orden::where('examen_orden.estado', 1)
            ->join('examen_detalle as ed', 'ed.id_examen_orden', 'examen_orden.id')
            ->join('examen as e', 'e.id', 'ed.id_examen')
            ->join('examen_derivado as der', 'der.id_examen', 'ed.id_examen')
            ->join('examen_tipo_derivado as etd', 'etd.id', 'der.id_tipo')
            ->where('examen_orden.id', $id_orden)
            ->select('der.*', 'e.nombre as nombre_examen', 'etd.nombre as nombre_tipo')
            ->get();
        //    dd($planilla);
        $modulo_bodega = ModuloBodega::where('estado', '1')->where('id_usuario', Auth::user()->id)->where('id_empresa', $empresa->id)->where('id_modulo', 1)->first();
        // $bodegas = Bodega::where('estado', 1)->where("id_empresa", $empresa->id)->get();
        $bodegas = PlantillaControlLabsController::apiGetBodegasGye();

        $filas = PlantillaControlLabsController::filaGuardada($planilla);

        return view('plantilla_labs/edit', [
            "orden" => $orden, 'empresa' => $empresa, 'planilla' => $planilla, 'plantillas' => $plantilla_control, 'filas' => $filas,
            'examenes' => $examenes, 'planilla_derivado' => $planilla_derivado, 'examen_det' => $examen_det, 'derivados' => $derivados, "bodegas" => $bodegas['detalles'], "modulo_bodega" => $modulo_bodega
        ]);
    }

    public static function filaGuardada($data)
    {
        $fila = '';
        if (isset($data->detalles)) {
            foreach ($data->detalles as $value) {

                $apiProd = PlantillaControlLabsController::apiGetProductoUnico($value->codigo);
                $apiBodega = PlantillaControlLabsController::apiGetBodegasGye();

                $nombre_bodega = '';
                foreach ($apiBodega['detalles'] as $bodega) {
                    if ($bodega['id'] == $value->id_bodega) {
                        $nombre_bodega = $bodega['nombre'];
                    }
                }

                $fila .= "<tr id='tr_{$value->id}'>
                            <td>{$value->serie}</td>
                            <td>{$apiProd['nombre']}</td>
                            <td>{$value->created_at}</td>
                            <td>{$value->cantidad}</td>
                            <td>{$nombre_bodega}</td>
                            <td>{$value->usuario->nombre1} {$value->usuario->apellido1}</td>
                            <td><button class='btn btn-danger' onclick='eliminar_det({$value->id})'>Delete</button></td>
                        </tr>";
            }
        }
        return $fila;
    }

    public static function apiGetBodegasGye()
    {
        $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
        $elemento = json_encode([]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        // $url_emp = Grupo_Empresas::find(7);

        $url     = 'https://humanlabs.aitrol.com/api/bodegas/getBodegas';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public static function apiGetProducto($buscar)
    {
        $appId = "TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=";
        $elemento = json_encode(['nombreProducto' => $buscar]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        // $url_emp = Grupo_Empresas::find(7);

        $url     = 'https://humanlabs.aitrol.com/api/producto/getProductos';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public function buscarProductoSelect(Request $request)
    {
        $nombreProducto = strtoupper($request['term']);
        $arrayProductos = [];
        // $productos = Producto::where('nombre', 'like', "%$nombreProducto%")
        //     ->orWhere('codigo', 'like', "%$nombreProducto%")
        //     ->orWhere('descripcion', 'like', "%$nombreProducto%")
        //     ->get();

        $productos = PlantillaControlLabsController::apiGetProducto($nombreProducto);

        foreach ($productos['detalles'] as $val) {
            $arrayProductos[] = ['id' => $val['id'], 'nombre' => $val['nombre']];
        }

        return response()->json($arrayProductos);
    }

    public static function apiGetProductoUnico($buscar, $id_bodega = null)
    {
        $appId = "TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=";
        $elemento = json_encode(['nombreProducto' => $buscar, "id_bodega" => $id_bodega]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        // $url_emp = Grupo_Empresas::find(7);

        $url     = 'https://humanlabs.aitrol.com/api/producto/getProductosUnico';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public static function getCostoPromedio($data)
    {
        $appId = "TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=";
        $elemento = json_encode(['id_producto' => $data['id_producto'], 'id_bodega' => $data['id_bodega']]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        // $url_emp = Grupo_Empresas::find(7);

        $url     = 'https://humanlabs.aitrol.com/api/producto/getCostoPromedio';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public function getConsultarStock($data)
    {
        $appId = "TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=";
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $url     = 'https://humanlabs.aitrol.com/apihumanlabs/verificarStock';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public function buscarProducto(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $id_orden = $request->id_orden;
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        // $bodega = Bodega::find($request->id_bodega);
        $id_productoB = $request['id_producto'];
        // $nombre_producto = $request['nombre_producto'];
        $id_bodega = $request['id_bodega'];
        $apiProd = PlantillaControlLabsController::apiGetProductoUnico($id_productoB, $id_bodega);

        $arrayDatos = [
            'id_producto' => $id_productoB,
            'cantidad' => 1,
            'id_bodega' => $id_bodega,
        ];
        // $respuesta = $this->getConsultarStock($arrayDatos);
        if(Auth::user()->id == "0957258056"){
           // dd($apiProd);
        }
        if ($apiProd['status'] == 'error') {
            return ['status' => 'error', 'msj' => $apiProd['msj']];
        }
        //$inv_serie = Producto::where('id', $id_productoB)
        //->where('existencia', '>', 0)
        //->where('existencia_uso', '>', 0)
        //->where('id_empresa', $id_empresa)
        //->where('id_bodega', $id_bodega)
        //->first();
        //echo '<pre>';print_r(DB::getQueryLog());exit;
        /*if (is_null($inv_serie)) {
            return ['status' => 'error', 'msj' => 'No se encontro en el inventario o no tiene existencias'];
        }*/
        DB::beginTransaction();
        try {
            $id_detalle_planilla = "";
            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 1)->first();
            if (is_null($planilla)) {
                $examen_orden = Examen_Orden::find($id_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];
                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);
                //dd($inv_serie);
                // $precio = InvCosto::where('id_producto', $inv_serie->id)->first();
                $id_productoB = $apiProd["id"];
                $precio = PlantillaControlLabsController::getCostoPromedio(["id_producto" => $id_productoB, "id_bodega" => $id_bodega]);
                if(Auth::user()->id == "0957258056"){
                    // dd($precio);
                }
                if ($precio['status'] == "error") {
                    return $precio;
                }

                $precio =
                    $detalle = [
                        'id_producto'               => $id_productoB,
                        'codigo'                    => $apiProd["codigo"],
                        'id_planilla_cabecera'      => $id_planilla,
                        // 'procedimiento'             => ,
                        'precio'                    => $precio["costo"],
                        'check'                     => 1,
                        'estado'                    => 1,
                        'id_usuariocrea'            => $id_usuario,
                        'id_usuariomod'             => $id_usuario,
                        'ip_creacion'               => $ip_cliente,
                        'ip_modificacion'           => $ip_cliente,
                        'cantidad'                  => 1,
                        'serie'                     => $apiProd["serie"],
                        // 'lote'                      => $inv_serie->lote,
                        // 'fecha_vencimiento'         => $inv_serie->fecha_vence,
                        'observacion'               => "",
                        'tipo_plantilla'            => "",
                        'id_bodega'                 => $id_bodega,
                    ];
                $id_detalle_planilla = Planilla_Detalle_Labs::insertGetId($detalle);
            } else {
                // $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                // $precio = InvInventario::where('id_producto', $inv_serie->id)->first();

                $precio = PlantillaControlLabsController::getCostoPromedio(["id_producto" => $apiProd["id"], "id_bodega" => $id_bodega]);
                if(Auth::user()->id == "0957258056"){
                    // dd($precio);
                }
                if ($precio['status'] == "error") {
                    return $precio;
                }

                $detalle = [
                    'id_producto'               => $apiProd["id"],
                    'codigo'                    => $apiProd["codigo"],
                    'id_planilla_cabecera'      => $planilla->id,
                    'precio'                    => $precio["costo"],
                    'check'                     => 1,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    'cantidad'                  => 1,
                    'serie'                     => $apiProd["serie"],
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_bodega'                 => $id_bodega,
                ];
                $id_detalle_planilla = Planilla_Detalle_Labs::insertGetId($detalle);
            }
            $tr = PlantillaControlLabsController::crearFila($apiProd, $id_detalle_planilla, $id_bodega);
            // $comprometer = InvInventarioSerie::comprometer($inv_serie, 1);
            // $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            // $inventario = InvInventario::getInventario($inv_serie->id_producto, $inv_serie->id_bodega);
            // $inventario = InvInventario::getInventario($inv_serie->id, $id_bodega);
            DB::commit();
            return ['status' => 'success', 'fila' => $tr];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'status' => 'error',
                "msj" => "Error al cargar el insumo",
                "exp" => $e->getMessage(),
                'linea' => $e->getLine()
            ];
        }
    }
    public static function crearFila($data, $id_det, $id_bodega = null)
    {
        $tr = '';
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->nombre1 . " " . Auth::user()->apellido1;
        $nombre_bodega = "";
        // $bodega = Bodega::find($id_bodega);
        $api = PlantillaControlLabsController::apiGetBodegasGye();
        foreach ($api["detalles"] as $value) {
            if ($value["id"] == $id_bodega) {
                $nombre_bodega = $value["nombre"];
            }
        }
        // if (isset($data->bodega)) {
        //     $nombre_bodega = $data->nombre;
        // }
        // "id_producto"=> $apiProd["detalles"][0]["id"]
        // if (!is_null($bodega)) {
        //     $nombre_bodega = $bodega->nombre;
        // }
        $tr .= "
        <tr id='tr_{$id_det}'>
            <td>{$data["codigo"]}</td>
            <td>{$data["nombre"]}</td>
            <td>{$fecha}</td>
            <td>1</td>
            <td>{$nombre_bodega}</td>
            <td>{$usuario}</td>
            <td><button class='btn btn-danger' onclick='eliminar_det({$id_det})'>Delete</button></td>
        </tr>";
        return $tr;
    }
    public function index()
    {
        $plantillas = Plantilla_Control_Labs::orderby('id', 'desc')->paginate(10);
        return view('plantillas_labs.index', ['plantillas' => $plantillas, 'nombre' => '']);
    }
    public function create()
    {
        $producto = Producto::where('estado', 1)->get();
        //dd($procedimiento);
        $tipo_plantilla = Plantilla_Tipo_Labs::get();
        //dd($tipo_plantilla);

        return view('plantillas_labs.create', ['producto' => $producto, 'tipo_plantilla' => $tipo_plantilla]);
    }
    public function save(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        DB::beginTransaction();
        try {

            $planilla_id = Plantilla_Control_Labs::insertGetId([
                'codigo'                     => $request->codigo,
                'nombre'                     => $request->nombre,
                'estado'                     => $request->estado,
                'id_empresa'                 => $id_empresa,
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
            ]);
            //dd($request->all());
            if (count($request->producto) > 0) {
                for ($i = 0; $i < count($request->producto); $i++) {
                    $data2 = array(
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_producto'      => $request->producto[$i],
                        'id_plantilla'     => $planilla_id, //si va
                        'cantidad'         => $request->cantidad[$i],
                        'total'            => $request->total[$i],
                        'valor_uni'        => $request->valor_unitario[$i],
                        'id_tipo_labs'     => $request->tipo_plantilla[$i],

                    );
                    Plantilla_Item_Control_Labs::create($data2);
                }
            }

            DB::commit();
            $mensaje = "exito";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            $mensaje = "error";
            DB::rollBack();
            return $e->getMessage();
        }


        return ['respuesta' => $mensaje];
    }
    public function edit($id)
    {
        $producto = Producto::where('estado', 1)->get();
        //$procedimiento = Procedimiento::get();
        $plantilla = Plantilla_Control_Labs::find($id);
        //dd($plantilla);
        //$planPro = Planilla_Procedimiento::where('id_planilla',$plantilla->id)->get();
        $plantillas_items = Plantilla_Item_Control_Labs::where('id_plantilla', $id)->get();
        $tipo_plantilla = Plantilla_Tipo_Labs::get();
        //->select('prod.precio_compra', 'insumo_plantilla_item_control.id_plantilla', 'insumo_plantilla_item_control.id_producto', 'insumo_plantilla_item_control.orden', 'insumo_plantilla_item_control.cantidad', 'prod.nombre as nom_prod', 'insumo_plantilla_item_control.total')->get();
        //dd($plantillas_items);
        return view('plantillas_labs.editar', ['producto' => $producto, 'plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'tipo_plantilla' => $tipo_plantilla]);
    }
    public function update(Request $request)
    {

        $mensaje = "";
        //dd($request);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$plantilla= Insumo_Plantilla::where('codigo',$request['codigo']);
        DB::beginTransaction();
        try {
            $plantilla = Plantilla_Control_Labs::find($request->id_plantilla);

            $plantilla->codigo          = $request->codigo;
            $plantilla->nombre          = $request->nombre;
            $plantilla->estado          = $request->estado;
            $plantilla->ip_modificacion = $ip_cliente;
            $plantilla->save();

            //$plantilla_detalle = Insumo_Plantilla_Item_Control::where('id_plantilla', $request->id_plantilla)->get();
            $items = DB::table('plantilla_item_control_labs')->where('id_plantilla',  $request->id_plantilla)->first();
            if (!is_null($items)) {
                $items = DB::table('plantilla_item_control_labs')->where('id_plantilla',  $request->id_plantilla)->delete();
            }


            if (count($request->producto) > 0) {
                for ($i = 0; $i < count($request->producto); $i++) {
                    $datos = array(
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'id_producto'      => $request->producto[$i],
                        'id_plantilla'     => $request->id_plantilla, //si va
                        'cantidad'         => $request->cantidad[$i],
                        'total'            => $request->total[$i],
                        'valor_uni'        => $request->valor_unitario[$i],
                        'id_tipo_labs'   => $request->tipo_plantilla[$i],
                        //'iva'              => $request->iva[$i],
                        //'orden'            => $request->orden[$item],

                    );
                    Plantilla_Item_Control_Labs::create($datos);
                }
            }
            DB::commit();
            $mensaje = "exito";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            $mensaje = "error";
            DB::rollBack();
            return $e->getMessage();
        }

        //dd($request->all());

        return ['respuesta' => $mensaje];
    }
    public function comprobar(Request $request)
    {
        $verificar = Plantilla_Control_Labs::where('estado', 1)->first();
        if (!is_null($verificar)) {
            return ['validar' => 'si', 'mensaje' => 'Ya existe una plantilla con los mismos procedimientos'];
        } else {
            return ['validar' => 'no', 'mensaje' => 'No existe'];
        }
    }
    public function item_lista($id)
    {
        // se elimino el el ->where('estado', '1') en plantilla 

        $plantilla = Plantilla_Control_Labs::where('id', $id)->first();

        $plantillas_items = Plantilla_Item_Control_Labs::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'plantilla_item_control_labs.id_producto')
            ->select('plantilla_item_control_labs.id_tipo_labs', 'plantilla_item_control_labs.updated_at as fecha', 'plantilla_item_control_labs.total', 'plantilla_item_control_labs.id_plantilla', 'plantilla_item_control_labs.valor_uni', 'plantilla_item_control_labs.id_producto', 'plantilla_item_control_labs.cantidad', 'prod.nombre as nom_prod')->get();
        //dd($plantillas_items);

        /*$planilla_procedimiento = Planilla_Procedimiento::where('id_planilla', $id)
            ->join('procedimiento as p', 'p.id', 'planilla_procedimiento.id_procedimiento')
            ->select('p.id as id', 'p.observacion as nombre')->get();*/

        //dd($planilla_procedimiento);

        $tipo_plantilla = Plantilla_Tipo_Labs::get();

        return view('plantillas_labs.lista', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'tipo_plantilla' => $tipo_plantilla]);
    }
    public function buscar(Request $request)
    {
        $nombre = $request['nombre'];
        $plantillas = Plantilla_Control_Labs::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->paginate(10);

        return view('plantillas_labs.index', ['nombre' => $nombre, 'plantillas' => $plantillas]);
    }
    public function cargarPanltilla(Request $request)
    {
        //dd($request->all());
        $id_empresa = Session::get('id_empresa');
        $id_plantilla = $request->id_plantilla;
        $items = Plantilla_Item_Control_Labs::where('id_plantilla', $id_plantilla)->where('estado', 1)->get();
        $falta = "";
        $tr = "";
        // $id_bodega = 1;
        $bodega = Bodega::where("principal_labs", 1)->first();
        //$id_bodega = $bodega->id;
        DB::beginTransaction();
        try {
            foreach ($items as $value) {
                //$inv_serie = InvInventarioSerie::where('id_producto', $value->id_producto)
                $inv_serie = Producto::where('id',  $value->id_producto)
                    //->where('existencia', '>', 0)
                    //->where('existencia_uso', '>', 0)
                    //->where('id_empresa', $id_empresa)
                    //->where('id_bodega', $id_bodega)
                    ->first();
                for ($i = 0; $i < $value->cantidad; $i++) {
                    $guardar = PlantillaControlLabsController::guardarPlanilla($request->id_orden, $inv_serie, $id_plantilla, $request);
                    if ($guardar['status'] == 'success') {
                        $tr .= $guardar["fila"];
                    } else {
                        DB::rollback();
                        return $guardar;
                    }
                }
            }



            /* foreach ($items as $value) {
                $inv_serie = InvInventarioSerie::where('id_producto', $value->id_producto)
                ->where('existencia', '>', 0)
                ->where('existencia_uso', '>', 0)
                ->where('id_empresa', $id_empresa)
                //->where('id_bodega', $id_bodega)
                ->first();
                if (is_null($inv_serie)) {
                    // return ['status' => 'error', 'msj' => 'No se encontro en el inventario o no tiene existencias'];
                    $falta .= isset($value->producto) ? "<span class='badge bg-warning'>{$value->producto->nombre}</span><br>" : '';
                } else {
                    for ($i = 0; $i < $value->cantidad; $i++) {
                        $guardar = PlantillaControlLabsController::guardarPlanilla($request->id_orden, $inv_serie, $id_plantilla);
                        if ($guardar['status'] == 'success') {
                            $tr .= $guardar["fila"];
                        } else {
                            DB::rollback();
                            return $guardar;
                        }
                    }
                }
            }*/
            DB::commit();
            return ['status' => 'success', 'fila' => $tr, "falta" => 'No'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", 'fila' => $tr, 'linea' => $e->getLine()];
        }
    }
    public static function guardarPlanilla($id_orden, $inv_serie, $id_plantilla = "", $r = null)
    {
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            //$inv_serie = InvInventarioSerie::where('id_producto', $value->id_producto)->where('existencia', '>', 0)->where('existencia_uso', '>', 0)->where('id_empresa', $id_empresa)->first();
            // $id_bodega = 1;
            /*$bodega = Bodega::where("principal_labs", 1)->first();
            $id_bodega = $bodega->id;*/
            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 1)->first();
            $id_det = null;
            if ($planilla == '') {
                $examen_orden = Examen_Orden::find($id_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];

                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id,
                    'codigo'                    => $inv_serie->codigo,
                    'id_planilla_cabecera'      => $id_planilla,
                    // 'procedimiento'             => ,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    // 'movimiento',
                    // 'id_movimiento_paciente',
                    'cantidad'                  => 1,
                    'serie'                     => $inv_serie->serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_planilla'               => $id_plantilla,
                    'id_bodega'                 => $r->id_bodega,
                ];
                $id_det = Planilla_Detalle_Labs::insertGetId($detalle);
            } else {
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id,
                    'codigo'                    => $inv_serie->codigo,
                    'id_planilla_cabecera'      => $planilla->id,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    'cantidad'                  => 1,
                    'serie'                     => $inv_serie->serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_planilla'              => $id_plantilla,
                    'id_bodega'                 => $r->id_bodega,
                ];
                $id_det = Planilla_Detalle_Labs::insertGetId($detalle);
                // Planilla_Detalle_Labs::create($detalle);
            }
            $tr = PlantillaControlLabsController::crearFila($inv_serie, $id_det);
            $comprometer = InvInventarioSerie::comprometer($inv_serie, 1);
            // $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            $inventario = InvInventario::getInventario($inv_serie->id, $r->id_bodega);
            DB::commit();
            return ['status' => 'success', 'fila' => $tr];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public static function comparativo($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        $empresa = Empresa::find(Session::get('id_empresa'));
        $planilla = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', 1)->first();
        $planilla_derivados = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', 3)->first();
        if ($planilla == '' && $planilla_derivados == '') {
            return ['status' => false, 'msj' => 'Debe crear una planilla para poder aprobarla.'];
        }
        $plantilla_control = Plantilla_Control_Labs::where('estado', 1)->where('id_empresa', $empresa->id)->get();
        $id_usuario = Auth::user()->id;
        $certificar = 0;
        if (!is_null($planilla)) {
            if ($planilla->aprobado == 1) {
                $certificar = 0;
            } else {
                $certificar = Agenda_Permiso::where('id_usuario', $id_usuario)->where('ip_creacion', 'CERTIFICACION LABS')->first();
                if (!is_null($certificar) or Auth::user()->id_tipo_usuario == 1) {
                    $certificar = 1;
                } else {
                    $certificar = 0;
                }
            }
        }
        $filas = PlantillaControlLabsController::filaGuardadaComparativo($planilla, $certificar);

        return view('plantilla_labs/comparativo', [
            "orden" => $orden, 'empresa' => $empresa, 'planilla' => $planilla, 'plantillas' => $plantilla_control,
            'planilla_derivado' => $planilla_derivados, 'certificar' => $certificar, "filas" => $filas
        ]);
    }

    public static function filaGuardadaComparativo($data, $certificar)
    {
        $fila2 = "";
        if (isset($data->detalles)) {
            foreach ($data->detalles as $value) {

                $apiProd = PlantillaControlLabsController::apiGetProductoUnico($value->codigo);
                $apiBodega = PlantillaControlLabsController::apiGetBodegasGye();
                $nombre_bodega = '';
                foreach ($apiBodega['detalles'] as $bodega) {
                    if ($bodega['id'] == $value->id_bodega) {
                        $nombre_bodega = $bodega['nombre'];
                    }
                }



                $fila2 .= "
                    <input type='hidden' name='detalles[]' value='{$value->id}'>
                    <input type='hidden' name='codigos[]' value='{$value->codigo}'>
                    <input type='hidden' name='bodega[]' value='{$value->id_bodega}'>
                    <input type='hidden' name='nombrebodega[]' value='{$nombre_bodega}'>
                    <tr>";

                if (isset($value->serie)) {
                    $fila2 .= "<td>{$value->serie}</td>";
                } else {
                    $fila2 .= "<td>{$value->codigo}</td>";
                }
                $fila2 .= "
                        <td>{$apiProd['nombre']}</td>
                        <td>{$value->created_at}</td>
                        <td>
                            <input type='hidden' name='cantidad[]' value='{$value->cantidad}'>
                            {$value->cantidad}
                        </td>
                        <td>{$nombre_bodega}</td>";

                $checked = "";
                if ($value->check == 1) {
                    $checked = "checked";
                }

                $permisos = "";

                if ($certificar == 0) {
                    $permisos = "disabled";
                }

                $fila2 .= "<td>
                            <input {$permisos}  id='checkbox{$value->id}' onchange='cambiarEstado({$value->id})' style='width: 17px;height: 17px;' type='checkbox' {$checked}>
                            <input type='hidden' value='{$value->check}' id='check_aprobado{$value->id}' name='check_aprobado[]'>
                        </td>
                    </tr>";
            }
        }
        return $fila2;
    }

    public static function enviarAPiHuman($request)
    {
        $data['sitio'] = $_SERVER['HTTP_HOST'];
        $data['datos'] = $request->input();
        $id_empresa = Session::get('id_empresa');
        $data['id_empresa'] = $id_empresa;
        $examen_orden = Examen_Orden::find($request->id_orden);
        $data['examen_orden'] = $examen_orden;
        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
        $data['cab_movimiento'] = $cab_movimiento;
        $planilla_cabecera_labs = Planilla_Cabecera_Labs::find($request->id_planilla);
        dd($planilla_cabecera_labs);
        $data['planilla_cabecera_labs'] = $planilla_cabecera_labs;
        $data['planilla_cabecera_labs']->observacion .= ' | Aprobado por: ' . Auth::user()->nombre1 . ' ' . Auth::user()->apellido1 . '|' . Auth::user()->id;
        $pl_cab_derivado = Planilla_Cabecera_Labs::where('id_orden', $examen_orden->id)->where('estado', 3)->first();
        $data['pl_cab_derivado'] = $pl_cab_derivado;

        $arrayCodigos = [];
        $arrayBodegas = [];
        for ($i = 0; $i < count($data['datos']['codigos']); $i++) {
            $arrayCodigos[$i] = $data['datos']['codigos'][$i];
            $arrayBodegas[$i] = Bodega::where('id', $data['datos']['bodega'][$i])->first();
            $sitio = explode('.', $_SERVER['HTTP_HOST']);
            $arrayBodegas[$i]->nombre .= ' ' . strtoupper($sitio[0]);
        }
        $data['bodegas'] = $arrayBodegas;
        $productos = Producto::whereIn('codigo', $arrayCodigos)->get();
        $data['productos'] = $productos;
        $total_asiento = 0;
        // $id_bodega = 1;
        //Envio a Api
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        $nonce = $randomString;
        $appId     = date('YmdHis') . '-Humanlabs';
        $appSecret = 'jdjdjKJOS(/68759856498465';
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ),
            "ssl"  => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        $url = 'http://humanlabs.aitrol.com/apihumanlabs/bajarinsumos';
        $context = stream_context_create($options);
        try {
            $respuesta = file_get_contents($url, false, $context);
            if ($respuesta != '') {
            }
        } catch (Exception $e) {
            $respuesta = '';
        }
        $respuesta = json_decode($respuesta);
        if (isset($respuesta->status) && $respuesta->status == false) {
            return ['status' => 'error', "msj" => $respuesta->msj];
        } elseif (isset($respuesta->status) && $respuesta->status == true) {
            //$this->storePlanilla($request);
            return ['status' => 'success', "msj" => $respuesta->msj];
        } else {
            return ['status' => 'error', "msj" => 'Error de comunicacion con Api Humanlabs'];
        }
        exit;
    }
    public static function storePlanilla(Request $request)
    {
        $empresa = Empresa::find(Session::get('id_empresa'));
        $data['sitio'] = $_SERVER['HTTP_HOST'];
        $data['datos'] = $request->input();
        $id_empresa = Session::get('id_empresa');
        $data['id_empresa'] = $id_empresa;
        $examen_orden = Examen_Orden::find($request->id_orden);
        $data['examen_orden'] = $examen_orden;
        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
        $data['cab_movimiento'] = $cab_movimiento;
        $planilla_cabecera_labs = Planilla_Cabecera_Labs::find($request->id_planilla);
        $data['planilla_cabecera_labs'] = $planilla_cabecera_labs;
        $data['planilla_cabecera_labs']->observacion .= ' | Aprobado por: ' . Auth::user()->nombre1 . ' ' . Auth::user()->apellido1 . '|' . Auth::user()->id;
        $pl_cab_derivado = Planilla_Cabecera_Labs::where('id_orden', $examen_orden->id)
            ->where('estado', 3)
            ->first();
        $data['pl_cab_derivado'] = $pl_cab_derivado;
        $data['planilla_detalle'] = $planilla_cabecera_labs->detalles;
        $arrayCodigos = [];
        $arrayBodegas = [];
        $sitio = explode('.', $_SERVER['HTTP_HOST']);
        if (count($data['datos']['codigos']) > 0 && $data['datos']['codigos'][0] != '') {
            for ($i = 0; $i < count($data['datos']['codigos']); $i++) {
                $arrayCodigos[$i] = $data['datos']['codigos'][$i];
                // dd($data['datos']);
                $arrayBodegas[$i] = Bodega::where('id', $data['datos']['bodega'][$i])->first();
                /*if ($arrayBodegas[$i]->id == '') {
                    return ['status' => 'error', "msj" => 'Debe ligar la bodega ' . $arrayBodegas[$i]->nombre . ' de la sucursal con la bodega de Guayaquil'];
                }*/
                //$arrayBodegas[$i]->nombre = ' ' . strtoupper($sitio[0]);
            }
        }
        $data['bodegas'] = $arrayBodegas;
        $productos = Producto::whereIn('codigo', $arrayCodigos)->get();
        $data['productos'] = $productos;
        $total_asiento = 0;
        // $id_bodega = 1;
        //Envio a Api
        if (isset($pl_cab_derivado->detalles)) {
            $detalle = $pl_cab_derivado->detalles->sum('precio');
            $total_asiento += $detalle;
        }

        $empresa_envio = Empresa::where('prioridad_labs', 1)->first();
        $data['info_adicional'] = [
            "ciudad" => $empresa->ciudad,
            "nombre_paciente" => "{$examen_orden->paciente->apellido1} {$examen_orden->paciente->apellido2} {$examen_orden->paciente->nombre1} {$examen_orden->paciente->nombre2}",
            "id_paciente" => $examen_orden->id_paciente,
            "id_orden"    => $examen_orden->id,
            "empresa_info"   => "{$empresa_envio->establecimiento}-{$empresa_envio->punto_emision}",
        ];


        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        $nonce = $randomString;
        $appId     = date('YmdHis') . '-Humanlabs';
        $appSecret = 'jdjdjKJOS(/68759856498465';
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ),
            "ssl"  => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );

        $url = 'http://humanlabs.aitrol.com/apihumanlabs/bajarinsumos';
        $context = stream_context_create($options);
        try {
            $respuesta = file_get_contents($url, false, $context);
            if ($respuesta != '') {
            }
        } catch (Exception $e) {
            $respuesta = '';
        }

        //  dd($respuesta);
        //$respuesta = json_decode($respuesta);
        $respuesta = json_decode($respuesta);
        if (isset($respuesta->status) && $respuesta->status != true) {
            return $respuesta;
            return ['status' => 'error', "msj" => $respuesta->msj, 'data' => $respuesta->data, 'linea' => isset($respuesta->linea) ? $respuesta->linea : ''];
        } elseif (isset($respuesta->status) && $respuesta->status == true) {
            $planilla_cabecera_labs->aprobado = 1;
            $planilla_cabecera_labs->id_usuariomod = Auth::user()->id;
            $planilla_cabecera_labs->save();
            return ['status' => 'success', "msj" => $respuesta->msj, 'asiento_costo' => $respuesta->asiento];
        } else {
            return ['status' => 'error', "msj" => 'Error de comunicacion con Api Humanlabs'];
        }
        /*
            1) Inv_Inventario
            2) Inv_cab_Movimiento
            3) Inv_Detalle_Movimiento
            4) Inv_inventario Serie
            5) Inv_Kardex
        */
        exit;
        $id_empresa = Session::get('id_empresa');
        $examen_orden = Examen_Orden::find($request->id_orden);
        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
        $planilla_cabecera_labs = Planilla_Cabecera_Labs::find($request->id_planilla);
        $pl_cab_derivado = Planilla_Cabecera_Labs::where('id_orden', $examen_orden->id)->where('estado', 3)->first();

        $total_asiento = 0;
        // $id_bodega = 1;
        $bodega = Bodega::where("principal_labs", 1)->first();
        $id_bodega = $bodega->id;

        if ($planilla_cabecera_labs->aprobado == 1) {
            return ["status" => "error", "msj" => "Ya se encuentra aprobada la plantilla"];
        }

        $verificarStock = PlantillaControlLabsController::verificarStock($planilla_cabecera_labs->id);
        if ($verificarStock['status'] == "error") {
            return ['status' => 'error', "msj" => $verificarStock['problem']];
        }
        if (isset($pl_cab_derivado->detalles)) {
            $detalle = $pl_cab_derivado->detalles->sum('precio');
            $total_asiento += $detalle;
        }
        $id_usuario = Auth::user()->id;
        $numero = "000000001";
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        if (!is_null($cab_movimiento)) {
            $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
        }
        $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EPL')->where('id_empresa', $id_empresa)->first();

        if (is_null($inv_documento_bod)) {
            return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas"];
        }
        DB::beginTransaction();
        try {
            $inv_transacciones_bodegas = InvTransaccionesBodegas::insertGetId([
                'id_documento_bodega'   => $inv_documento_bod->id,
                // 'id_bodega'             => $id_bodega,//cambiar por bodefa de empresa prioridad_labs
                'secuencia'             => 100,
                'id_empresa'            => $id_empresa,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente
            ]);
            // $inv_trans_bodega = InvTransaccionesBodegas::where('id_documento_bodega', 18)->first();
            $data_cab_movimiento = [
                'id_documento_bodega'       => $inv_documento_bod->id,
                'id_transaccion_bodega'     => $inv_transacciones_bodegas,
                'id_bodega_origen'          => 1,
                // 'id_bodega_destino'=> null, 
                'numero_documento'          => $numero,
                // 'num_doc_ext'=> '', 
                // 'num_doc_cont'=> '', 
                'observacion'               => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente}, Orden: {$examen_orden->id}",
                'fecha'                     => date("Y-m-d", strtotime($examen_orden->fecha_orden)),
                'descuento'                 => 0,
                'subtotal'                  => 0,
                'subtotal_0'                => 0,
                'iva'                       => 0,
                'total'                     => 0,
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
            if (isset($request->detalles)) {
                for ($i = 0; $i < count($request->detalles); $i++) {
                    if ($request['check_aprobado'][$i] == 1) {
                        $planilla_detalle_labs = Planilla_Detalle_Labs::find($request['detalles'][$i]);
                        $inv_inventario = InvInventario::where('id_producto', $planilla_detalle_labs->id_producto)->where('id_bodega', $planilla_detalle_labs->id_bodega)->latest()->first();
                        // dd($inv_inventario);
                        $costo_promedio = $planilla_detalle_labs->precio;
                        $exist_nueva = $planilla_detalle_labs->cantidad;
                        if (!is_null($inv_inventario)) {
                            $exist_nueva = $inv_inventario->existencia + $planilla_detalle_labs->cantidad;
                            if (!is_null($inv_inventario->costo_promedio)) {
                                $cantidad_ant       = $exist_nueva;
                                $precio_ant         = $inv_inventario->costo_promedio;
                                $suma_ant           = $inv_inventario->existencia * $precio_ant;
                                $costo_promedio     = ($suma_ant + $planilla_detalle_labs->costo_total) / $exist_nueva;
                            }
                        }
                        $total_asiento +=  $planilla_detalle_labs->precio;
                        $data_inv_inventario = [
                            'id_producto' => $planilla_detalle_labs->id_producto,
                            'id_bodega' => $planilla_detalle_labs->id_bodega,
                            'tipo' => 'C',
                            'existencia' => intval($inv_inventario->existencia) - 1,
                            'existencia_uso' => intval($inv_inventario->existencia_uso) - 1,
                            'existencia_min' => $inv_inventario->existencia_min,
                            'existencia_max' => $inv_inventario->existencia_max,
                            'comprometido'  => intval($inv_inventario->comprometido) + 1,
                            'comprometido_uso'  => intval($inv_inventario->comprometido_uso) + 1,
                            'costo_promedio'    => $costo_promedio,
                            // 'ubicacion'=> ,
                            'estado' => 1,
                            'id_empresa' => $id_empresa,
                            'id_usuariocrea' => $id_usuario,
                            'id_usuariomod' => $id_usuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                        $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);
                        $movimiento = Movimiento::where('serie', "%{$planilla_detalle_labs->serie}%")->where('estado', 1)->first();
                        $data_inv_det_movimiento = [
                            'id_inv_cab_movimientos'        => $id_cab_movimiento,
                            'id_producto'                   => $planilla_detalle_labs->id_producto,
                            'serie'                         => $planilla_detalle_labs->serie,
                            'fecha_vence'                   => date('Y-m-d', strtotime($planilla_detalle_labs->fecha_vencimiento)),
                            'lote'                          => $planilla_detalle_labs->lote,
                            'id_inv_inventario'             => $id_inv_inventario,
                            'cantidad'                      => 1,
                            'cant_uso'                      => 1,
                            'valor_unitario'                => $planilla_detalle_labs->precio,
                            'subtotal'                      => $planilla_detalle_labs->precio,
                            'descuento'                     => !is_null($movimiento) ? $movimiento->descuento : 0,
                            'iva'                           => 0,
                            'total'                         => $planilla_detalle_labs->precio,
                            'estado'                        => 1,
                            'kardex'                        => 1,
                            'motivo'                        => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente} ",
                            // 'id_detalle_origen'=> '',
                            'id_pedido'                     => !is_null($movimiento) ? $movimiento->id_pedido : null,
                            // 'id_detalle_pedido'=> '',
                            // 'id_procedimiento'=> '',
                            // 'id_movimiento_paciente'=> '',
                            'id_usuariocrea'                => $id_usuario,
                            'id_usuariomod'                 => $id_usuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ];
                        $inv_det_movimiento = InvDetMovimientos::insertGetId($data_inv_det_movimiento);
                        $inv_serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('serie', $planilla_detalle_labs->serie)
                            ->where('id_bodega', $planilla_detalle_labs->id_bodega)->first();
                        if (!is_null($inv_serie)) {
                            if ($inv_serie->existencia > 0) {
                                $inv_serie->existencia = $inv_serie->existencia - 1;
                                $inv_serie->existencia_uso = $inv_serie->existencia_uso - 1;
                                $inv_serie->id_usuariomod = $id_usuario;
                                $inv_serie->ip_modificacion = $ip_cliente;
                                $inv_serie->save();
                            }
                        }
                        $data_inv_kardex = [
                            'id_inv_inventario' => $id_inv_inventario,
                            'id_bodega' => $planilla_detalle_labs->id_bodega,
                            'id_producto' => $planilla_detalle_labs->id_producto,
                            'tipo' => 'E',
                            'descripcion' => 'EGR-',
                            'referencia' => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente}",
                            'fecha' => date('Y-m-d'),
                            'cantidad' => 1,
                            'cant_uso' => $inv_serie->existencia,
                            'valor_unitario' => $planilla_detalle_labs->precio,
                            // 'iva' => '', 
                            'total' => $planilla_detalle_labs->precio,
                            'exist_cant' => $inv_inventario->existencia,
                            'exist_uso' => $inv_inventario->existencia_uso,
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
                    }
                }
            }
            $id_asiento_cab = null;
            $inv_contable_cab = InvContableCab::where('id_documento_bodega', $inv_documento_bod->id)->where('id_empresa', $id_empresa)->first();
            if (!is_null($inv_contable_cab)) {
                $input_cabecera = [

                    'fecha_asiento'   => $examen_orden->fecha_orden,
                    'fact_numero'     => $numero,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => "EXAMEN LAB PACIENTE: {$examen_orden->id_paciente} ",
                    'valor'           => $total_asiento,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                foreach ($inv_contable_cab->detalles as $det_labs) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $det_labs->cuenta,
                        'descripcion'         => '',
                        'fecha'               => $examen_orden->fecha_orden,
                        'debe'                => $det_labs->tipo == 'D' ? $total_asiento : 0,
                        'haber'               => $det_labs->tipo == 'H' ? $total_asiento : 0,
                        'estado'              => '1',
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            }
            if (!is_null($planilla_cabecera_labs)) {
                $planilla_cabecera_labs->aprobado = 1;
                $planilla_cabecera_labs->observacion = $request->observacion_planilla;
                $planilla_cabecera_labs->id_usuariomod = $id_usuario;
                $planilla_cabecera_labs->id_asiento_cabecera = $id_asiento_cab;
                $planilla_cabecera_labs->save();
            }
            if (!is_null($pl_cab_derivado)) {
                $pl_cab_derivado->aprobado = 1;
                $pl_cab_derivado->observacion = $request->observacion_planilla;
                $pl_cab_derivado->id_usuariomod = $id_usuario;
                $pl_cab_derivado->id_asiento_cabecera = $id_asiento_cab;
                $pl_cab_derivado->save();
            }

            $examen_orden->total_planilla = $total_asiento;
            $examen_orden->id_usuariomod = $id_usuario;
            $examen_orden->ip_modificacion      = $ip_cliente;
            $examen_orden->save();
            DB::commit();
            return ['status' => 'success', "msj" => 'Guardado con exito', 'asiento' => $id_asiento_cab];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage()];
        }
    }
    public static function _verificarStock($id_planilla)
    {
        $planilla_detalle = Planilla_Detalle_Labs::where('id_planilla_cabecera', $id_planilla)
            ->where('check', 1)
            ->groupBy('serie', 'id_bodega')
            ->select(DB::raw('COUNT(serie) as cantidad'), 'serie', 'id_bodega')
            ->get();
        // $problem = [];
        // dd($problem);
        //echo '<pre>';print_r($planilla_detalle);exit;
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {
            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->first();
            if (is_null($inv_serie)) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
            } else if ($inv_serie->existencia < $det->cantidad) {
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente | Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }
    public static function verificarStock($id_planilla)
    {
        $id_empresa = Session::get('id_empresa');
        $planilla_detalle = Planilla_Detalle_Labs::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)
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
    public function busca_examen(Request $request)
    {

        $id_examen = $request->id_examen;

        $derivado = Examen_Derivado::where('examen_derivado.id_examen', $id_examen)->where('examen_derivado.estado', '1')
            ->join('examen_tipo_derivado as etd', 'etd.id', 'examen_derivado.id_tipo')->select('examen_derivado.*', 'etd.nombre as nombre_tipo')->get();

        return $derivado;
    }
    public function guardar_derivado(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $id_orden = $request->id_orden;
            $id_derivado = $request->id_derivado;
            $id_examen = $request->id_examen;
            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 3)->where('id_empresa', $id_empresa)->first();
            if (is_null($planilla)) {
                $examen_orden = Examen_Orden::find($id_orden);
                //dd($examen_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 3,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];
                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);
                $examen_derivado = Examen_Derivado::find($id_derivado);
                $detalle = [
                    'id_planilla_cabecera'      => $id_planilla,
                    'precio'                    => $examen_derivado->valor,
                    'check'                     => 1,
                    'estado'                    => 1,
                    'cantidad'                  => 1,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_examen_derivado'        => $id_derivado,
                    'id_examen'                 => $id_examen,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                ];
                Planilla_Detalle_Labs::create($detalle);
            } else {
                $pdetalle = Planilla_Detalle_Labs::where('id_planilla_cabecera', $planilla->id)->where('id_examen', $id_examen)->where('estado', 1)->first();
                if (is_null($pdetalle)) {
                    # code...
                    $examen_derivado = Examen_Derivado::find($id_derivado);
                    $detalle = [
                        'id_planilla_cabecera'      => $planilla->id,
                        'precio'                    => $examen_derivado->valor,
                        'check'                     => 1,
                        'estado'                    => 1,
                        'cantidad'                  => 1,
                        'observacion'               => "",
                        'tipo_plantilla'            => "",
                        'id_examen_derivado'        => $id_derivado,
                        'id_examen'                 => $id_examen,
                        'id_usuariocrea'            => $id_usuario,
                        'id_usuariomod'             => $id_usuario,
                        'ip_creacion'               => $ip_cliente,
                        'ip_modificacion'           => $ip_cliente,
                    ];
                    Planilla_Detalle_Labs::create($detalle);
                } else {
                    return ['status' => 'error', 'msj' => 'Examen cargado', 'title' => 'Error'];
                }
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado', 'title' => 'Success'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage()];
        }
    }
    public function eliminar_det($id)
    {
        $det_planilla = Planilla_Detalle_Labs::find($id);
        $det_planilla->delete();
        return ['status' => 'success', 'msj' => 'Eliminado'];
    }
    public function checksUpdate(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $detalle = Planilla_Detalle_Labs::find($request->id);
            // dd($detalle);
            $detalle->check = $request->estado;
            $detalle->id_usuariomod = $id_usuario;
            $detalle->ip_modificacion = $ip_cliente;
            $detalle->save();
            DB::commit();
            return ['status' => 'success', 'msj' => 'Editado con exito'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
        }
    }
    public function reprocesar_serie($id)
    {
        $planilla = Planilla_Cabecera_Labs::findOrFail($id);
        if ($planilla->aprobado == 1) {
            return response()->json([
                'estado'    => 'error',
                'respuesta' => 'planilla ya aprobada',
                'valores'   => array()
            ]);
        }
        $controlador = false;
        $faltantes = array();
        $detalles = Planilla_Detalle_Labs::where('id_planilla_cabecera', $id)->where('check', 1)->where('estado', 1)->get();
        // dd($planilla, $detalles,$id);

        $usados = [];
        foreach ($detalles as $value) {
            $serie = InvInventarioSerie::where('serie', $value->serie)
                ->where('id_empresa', Session::get('id_empresa'))
                ->where('id_bodega', $value->id_bodega)
                ->where('estado', 1)
                ->first();

            if (!is_null($serie)) {
                if ($serie->producto->codigo_siempre == 1) {

                    $nueva_serie = null;
                    /*********************CAMBIOS PARA LAS CANTIDADES EN PRODUCTOS******************************/
                    if (isset($usados[$serie->producto->id][$value->serie])) {
                        $restante = $serie->existencia - ($usados[$value->producto->id][$value->serie] + $value->cantidad);
                        if ($restante < 0) {
                            $inv_series = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', Session::get('id_empresa'))
                                // ->where('id', '<>', $serie->id)
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->get();

                            foreach ($inv_series as $inv_serie) {
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
                            $usados[$value->producto->id][$value->serie] = $value->cantidad;
                        } else {
                            $nueva_serie = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', Session::get('id_empresa'))
                                // ->where('id', '<>', $serie->id)  
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->first();
                            if (!is_null($nueva_serie)) {
                                $usados[$serie->producto->id][$nueva_serie->serie] = $value->cantidad;
                            }
                            $controlador = true;
                        }
                    }

                    if ($controlador) {


                        if (!is_null($nueva_serie)) {

                            DB::beginTransaction();
                            // $nuevo_pedido = Movimiento::where('movimiento.serie', $nueva_serie->serie)
                            //     ->join('pedido', 'movimiento.id_pedido', '=', 'pedido.id')
                            //     ->where('pedido.id_empresa', Session::get('id_empresa'))
                            //     ->select('movimiento.*')
                            //     ->first();
                            try {
                                Planilla_Detalle_Labs::where('id', $value->id)
                                    ->update(['serie' => $nueva_serie->serie, 'ip_modificacion' => 'REPROCESO APROBADO', 'observacion' => 'REPROCESO APROBADO']);
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
}
