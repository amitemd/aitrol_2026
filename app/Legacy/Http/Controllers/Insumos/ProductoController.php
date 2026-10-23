<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Bodega;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_movimiento;
use Sis_medico\Marca;
use Sis_medico\Movimiento;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Tipo;
use Sis_medico\Insumo_Plantilla_Tipo;
use Sis_medico\Empresa;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\InvKardex;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Asientos_Cabecera;
use Session;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Http\Controllers\ImportacionesController;


class ProductoController extends Controller
{
    //
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7, 20)) == false) {
            return true;
        }

        $id_empresa = session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            $n_empresa = Empresa::where('prioridad', 1)->first();
            session()->put('id_empresa', $n_empresa->id);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = Producto::where('estado', 1)->paginate(20);
        $tipos     = Tipo::where('estado', '1')->get();
        // $tipos     = Tipo::all();
        $id_empresa = session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        return view('insumos/producto/index', ['productos' => $productos, 'tipos' => $tipos, 'empresa' => $empresa]);
    }

    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = session()->get('id_empresa');

        $tipos          = Tipo::where('estado', '1')->get();
        $proveedor      = proveedor::all();
        $marcas         = Marca::where('estado', '1')->get();
        $ct_productos   = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        $tipo_plantilla = Insumo_Plantilla_Tipo::get();
        $id_empresa = session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        return view('insumos/producto/create', ['proveedores' => $proveedor, 'marcas' => $marcas, 'tipos' => $tipos, 'ct_productos' => $ct_productos, 'tipo_plantilla' => $tipo_plantilla, 'empresa' => $empresa]);
    }

    public function bajar_producto()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('insumos/producto/bajar_producto');
    }

    public function revisar_pedido($id_pedido)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $this->recalcular_pedido($id_pedido);

        $pedido = Pedido::find($id_pedido);
        $detalle  = InvDetMovimientos::where('id_pedido', $id_pedido)->whereNotNull('id_detalle_pedido')->get();
        if (count($detalle) > 0) {
            $pedido   = Pedido::find($id_pedido);
            $detalles = array();
            $mov      = array();
            foreach ($detalle as $value) {

                # datos
                $mov['codigo']             = $value->producto->codigo;
                $mov['nombre']             = $value->producto->nombre;
                $mov['cantidad']           = $value->cantidad;
                $mov['cant_uso']           = $value->cant_uso;
                $mov['serie']              = $value->serie;
                $mov['bodega']             = (isset($pedido->bodega)) ? $pedido->bodega->nombre : "";
                $mov['lote']               = $value->lote;
                $mov['registro_sanitario'] = $value->producto->registro_sanitario;
                $mov['vencimiento']        = $value->fecha_vence; // fecha de vencimiento de InvDetMovimientos
                $mov['valor_unitario']     = $value->valor_unitario;
                $mov['total']              = round(($value->valor_unitario * $value->cantidad), 2);
                # traslado
                $mov['traslado'] = Pedido::cant_traslado($value->serie);
                # existencia
                $mov['existencia'] = Pedido::existencia($value->serie, $pedido->id_bodega);
                # facturado
                $mov['facturado'] = Pedido::facturado($value->serie);
                $mov['subtotal_0']         = ($pedido->subtotal_0);
                $mov['subtotal_12']        = ($pedido->subtotal_12);
                $mov['descuento']          = ($pedido->descuento);
                $mov['iva']                = ($pedido->iva);
                $mov['total']              = ($pedido->total);



                $detalles[] = $mov;
            }
        } else {
            $detalle = Movimiento::where('id_pedido', $id_pedido)->get();
            $pedido   = Pedido::find($id_pedido);
            $detalles = array();
            $mov      = array();
            foreach ($detalle as $value) {
                # datos
                $bodega = Bodega::find($value->id_bodega);
                $mov['codigo']             = $value->producto->codigo;
                $mov['nombre']             = $value->producto->nombre;
                $mov['cantidad']           = $value->cantidad;
                $mov['cant_uso']           = $value->usos;
                $mov['serie']              = $value->serie;
                $mov['bodega']             = (isset($bodega)) ? $bodega->nombre : "";
                $mov['lote']               = $value->lote;
                $mov['registro_sanitario'] = $value->producto->registro_sanitario;
                $mov['vencimiento']        = $value->fecha_vencimiento; // fecha de vencimiento de InvDetMovimientos
                $mov['valor_unitario']     = $value->precio;
                $mov['total']              = round(($value->precio * $value->cantidad), 2);
                # traslado
                $mov['traslado'] = Pedido::cant_traslado($value->serie);
                # existencia
                $mov['existencia'] = Pedido::existencia($value->serie, $pedido->id_bodega);
                # facturado
                $mov['facturado'] = Pedido::facturado($value->serie);
                $mov['subtotal_0']         = ($pedido->subtotal_0);
                $mov['subtotal_12']        = ($pedido->subtotal_12);
                $mov['descuento']          = ($pedido->descuento);
                $mov['iva']                = ($pedido->iva);
                $mov['total']              = ($pedido->total);

                $detalles[] = $mov;
            }
        }

        //
        //->select('log_movimiento.id_producto', DB::raw('SUM(log_movimiento.cantidad) as cantidad_total'))
        //->groupBy('id_producto')

        return view('insumos/ingreso/visualizar', ['pedido' => $pedido, 'detalle' => $detalle, 'detalles' => $detalles]);
    }

    public function editar_pedido_new($id_pedido)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido = Pedido::find($id_pedido);

        $detalle  = InvDetMovimientos::where('id_pedido', $id_pedido)->get();
        $pedido   = Pedido::find($id_pedido);
        $detalles = array();
        $mov      = array();
        foreach ($detalle as $value) {
            # datos
            $mov['id']                 = $value->id;
            $mov['codigo']             = $value->producto->codigo;
            $mov['nombre']             = $value->producto->nombre;
            $mov['cantidad']           = $value->cantidad;
            $mov['cant_uso']           = $value->cant_uso;
            $mov['serie']              = $value->serie;
            $mov['bodega']             = (isset($pedido->bodega)) ? $pedido->bodega->nombre : "";
            $mov['lote']               = $value->lote;
            $mov['registro_sanitario'] = $value->producto->registro_sanitario;
            $mov['vencimiento']        = $value->fecha_vence; // fecha de vencimiento de InvDetMovimientos
            $mov['valor_unitario']             = $value->valor_unitario;
            $mov['total']              = round(($value->precio * $value->cantidad), 2);
            # traslado
            $mov['traslado'] = Pedido::cant_traslado($value->serie);
            # existencia
            $mov['existencia'] = Pedido::existencia($value->serie, $pedido->id_bodega);
            # facturado
            $mov['facturado'] = Pedido::facturado($value->serie);
            $mov['subtotal_0']         = ($pedido->subtotal_0);
            $mov['subtotal_12']        = ($pedido->subtotal_12);
            $mov['descuento']          = ($pedido->descuento);
            $mov['iva']                = ($pedido->iva);
            $mov['total']              = ($pedido->total);



            $detalles[] = $mov;
        }
        //
        //->select('log_movimiento.id_producto', DB::raw('SUM(log_movimiento.cantidad) as cantidad_total'))
        //->groupBy('id_producto')

        return view('insumos/ingreso/edit_pedidos_new', ['pedido' => $pedido, 'detalle' => $detalle, 'detalles' => $detalles]);
    }

    public function show(Request $request)
    {
        //return array('value' => 'hi', 'nombre' => 'hi');
        $codigo = $request['term'];

        $data      = array();
        $productos = DB::table('producto')->where('codigo', 'like', '%' . $codigo . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo, 'nombre' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function listar(Request $request)
    {
        //return array('value' => 'hi', 'nombre' => 'hi');
        $codigo = $request['term'];

        $data      = array();
        $productos = DB::table('producto')->where('codigo', 'like', '%' . $codigo . '%')->where('estado', '1')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo, 'nombre' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function codigo(Request $request)
    {
        //dd("hola");
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        $pedido     = $request['numerodepedido'];
        $proveedor  = $request['razonsocial'];
        $id_empresa = $request->session()->get('id_empresa');



        //$constraints = ['numerodepedido' => $request['numerodepedido']];

        /*$pedidos = DB::table('pedido as p')
            ->whereNull('p.estado_compras')
            //->where('p.pedido', 'like', '%' . $pedido . '%')
            ->join('proveedor as pro', 'pro.id', 'p.id_proveedor')
            ->join('users as u', 'u.id', 'p.id_usuariocrea')
            ->where('p.id_empresa', $id_empresa)
            ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial', 'pro.razonsocial');

        if (!is_null($pedido)) {
            $pedidos = $pedidos->where('p.pedido', 'like', '%' . $pedido . '%');
        }

        if (!is_null($proveedor)) {
            $pedidos = $pedidos->where('pro.id', 'like', '%' . $proveedor . '%');
        }*/
        $pedidos = ProductoController::buscadorPedidos($request);
        $pedidos         = $pedidos->OrderBy('pedido.created_at', 'desc')->paginate(10);
        $i               = 0;
        $cantidades      = array();
        $pedidosdetalles = array();
        foreach ($pedidos as $value) {

            $busqueda = Pedido::where('pedido.id', $value->id)

                ->join('inv_det_movimientos as m', 'm.id_pedido', 'pedido.id')
                //->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->groupBy('m.serie')
                ->get();
            //dd($busqueda);

            $busqueda2 = Pedido::where('pedido.id', $value->id)
                ->join('inv_det_movimientos as m', 'm.id_pedido', 'pedido.id')
                //->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                //->where('m.tipo', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->select(DB::raw('count(*) as cantidad_total'))
                ->get();
            //dd($busqueda2);

            $busqueda3 = Detalle_Pedido::where('id_pedido', $value->id)
                ->where('estado', 1)
                ->first();

            //dd($busqueda3);
            if ($busqueda->count() > 0) {
                $cantidades[$i][0] = $busqueda->count();
                $cantidades[$i][1] = $busqueda2[0]->cantidad_total;
            } else {
                $sql = Movimiento::where('id_pedido', $value->id)
                    ->where('estado', '1');
                $busqueda4 = $sql->get();
                $busqueda2 = $sql->select(DB::raw('count(*) as cantidad'))->get();
                $cantidades[$i][0] = $busqueda4->count();
                $cantidades[$i][1] = $busqueda2[0]->cantidad_total;
            }
            if (isset($busqueda3->id)) {
                $pedidosdetalles[$i][0] = $busqueda3->id;
            } else {
                $pedidosdetalles[$i][0] = null;
            }
            /*if (isset($busqueda4->id)) {
                $pedidosdetalles[$i][0] = $busqueda4->id;
            }*/
            $i = $i + 1;
        }
        // if(Auth::user()->id == "0957258056"){
        //     dd($request->holsa);
        // }
        return view('insumos/producto/codigobarra', ['pedidos' => $pedidos, 'cantidades' => $cantidades, 'pedidosdetalles' => $pedidosdetalles, 'id_empresa' => $id_empresa, 'request' => $request->all()]);
    }

    public function reportePedido($id_pedido)
    {
        //dd($id_pedido);
        $pedido = Pedido::find($id_pedido);


        Excel::create("PEDIDO {$pedido->id}", function ($excel) use ($pedido) {
            $excel->sheet("PEDIDO {$pedido->id}", function ($sheet) use ($pedido) {
                $comienzo = 1;
                $inv_det = InvDetMovimientos::where('id_pedido', $pedido->id)->get();
                // $detalle = $pedido->detalle;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["FECHA PEDIDO", strtoupper($pedido->fecha)]);
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["# FACTURA", strtoupper($pedido->factura)]);
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["PROVEEDOR", isset($pedido->proveedor) ? strtoupper($pedido->proveedor->nombrecomercial) : $pedido->id_proveedor]);
                $comienzo++;
                $doc_bodega = $pedido->tipo;
                if (isset($pedido->movimiento_inv)) {
                    if (isset($pedido->movimiento_inv->documento_bodega)) {
                        $doc_bodega = strtoupper($pedido->movimiento_inv->documento_bodega->documento);
                    }
                }

                ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["DOC. BODEGA", $doc_bodega]);
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, 1, ["D", "E"], ["# PEDIDO", $pedido->pedido]);

                ImportacionesController::excelDetalles($sheet, 2, ["D", "E"], ["FECHA VENCE", $pedido->vencimiento]);

                ImportacionesController::excelDetalles($sheet, 3, ["D", "E"], ["BODEGA RECIBE", isset($pedido->bodega) ? $pedido->bodega->nombre : $pedido->id_bodega]);
                $comienzo++;


                $titles['comienzo'] = $comienzo;
                $titles["data"] = ['Codigo', 'Nombre', 'Cantidad', 'Usos',    'Serie', 'Bodegas', 'Lote', 'Registro Sanitario', 'Fecha de Vencimiento', 'Precio unitario', 'Traslado', 'Existencia', 'Precio final'];
                $titles["color"] = "#ffff";
                $titles["background-color"] = "#000000";
                excelCreate::details($sheet, $titles);
                $comienzo++;
                if (count($inv_det) > 0) {
                    foreach ($inv_det as $value) {
                        if (!is_null($value->id_detalle_pedido)) {

                            $details["data"] = [
                                isset($value->producto) ? $value->producto->codigo : $value->id_producto,
                                isset($value->producto) ? $value->producto->nombre : '',
                                $value->cantidad,
                                $value->cant_uso,
                                $value->serie . "-",
                                isset($value->bodega) ? $value->bodega->nombre : 'N/A',
                                $value->lote,
                                isset($value->producto) ? $value->producto->registro_sanitario : 'N/A',
                                $value->fecha_vence,
                                $value->valor_unitario,
                                Pedido::cant_traslado($value->serie),
                                Pedido::existencia($value->serie, $pedido->id_bodega),
                                $value->valor_unitario * $value->cantidad,
                            ];
                            $details['comienzo'] = $comienzo;
                            excelCreate::details($sheet, $details);
                            $comienzo++;
                        }
                    }
                } else {
                    foreach ($pedido->detalle as $value) {
                        //dd($value);
                        $details["data"] = [
                            isset($value->producto) ? $value->producto->codigo : $value->id_producto,
                            isset($value->producto) ? $value->producto->nombre : '',
                            $value->cantidad,
                            $value->usos,
                            $value->serie . "-",
                            isset($value->bodega) ? $value->bodega->nombre : 'N/A',
                            $value->lote,
                            isset($value->producto) ? $value->producto->registro_sanitario : 'N/A',
                            $value->fecha_vencimiento,
                            $value->precio,
                            Pedido::cant_traslado($value->serie),
                            Pedido::existencia($value->serie, $pedido->id_bodega),
                            $value->precio * $value->cantidad,
                        ];
                        $details['comienzo'] = $comienzo;
                        excelCreate::details($sheet, $details);
                        $comienzo++;
                    }
                }
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["SUBTOTAL 12%", $pedido->subtotal_12]);
                $comienzo++;


                ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["SUBTOTAL 0%", $pedido->subtotal_0]);
                $comienzo++;


                ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["DESCUENTO", $pedido->descuento]);
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["IVA", $pedido->iva]);
                $comienzo++;

                ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["TOTAL", $pedido->total]);
                $comienzo++;
            });
        })->export('xlsx');
    }

    public function codigo2(Request $request)
    {

        $codigo    = $request['codigo'];
        $data      = null;
        $productos = Producto::where('codigo', 'like', '%' . $codigo . '%')
            ->where('estado', '1')
            ->first();

        if (!is_null($productos)) {
            //dd($productos);
            $data = $productos->nombre;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }


    public function verificarCodSiempre(Request $request)
    {

        $producto = Producto::where('codigo', trim($request['codigo']))->first();
        // dd($producto);
        if (!is_null($producto)) {
            return ['status' => 'success', 'valor' => $producto->codigo_siempre, 'kardex_uso' => $producto->kardex_uso];
        } else {
            return ['status' => 'error', 'valor' => '0'];
        }
    }

    public function descontar($cant, $tipo, $id_pro, $serie, $bodega, $pedido, $f_vencimiento, $lote)
    {
        // dd($cant);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('insumos/producto/descontar_producto', ['cantidad' => $cant, 'tipo' => $tipo, 'id_producto' => $id_pro, 'serie' => $serie, 'bodega' => $bodega, 'pedido' => $pedido, 'f_venci' => $f_vencimiento, 'lote' => $lote]);
    }

    public function nuevo_dar_baja_serie($serie, $id_bodega, $id_producto)
    {
        //dd($serie);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $producto = Producto::find($id_producto);
        $bodega   = Bodega::find($id_bodega);

        $id_empresa = session()->get('id_empresa');
        if ($id_empresa == null) {
            $empresa    = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        }
        $empresa    = Empresa::find($id_empresa);

        return view('insumos/producto/nuevo_dar_baja', ['serie' => $serie, 'id_bodega' => $id_bodega, 'producto' => $producto, 'bodega' => $bodega, 'empresa' => $empresa]);
    }

    public function codigo_baja(Request $request)
    {

        $nombre = $request['serie'];
        //dd($nombre);
        //return $nombre;
        $data = null;

        $productos = InvInventarioSerie::where('inv_inventario_serie.serie', $nombre)
            ->join('producto as pro', 'pro.id', 'inv_inventario_serie.id_producto')
            ->join('bodega as b', 'b.id', 'inv_inventario_serie.id_bodega')
            ->where('inv_inventario_serie.id_empresa', Session::get('id_empresa'))
            ->where('inv_inventario_serie.estado', 1)->where('pro.estado', 1)
            ->where('inv_inventario_serie.existencia', '>', 0)
            ->select('inv_inventario_serie.existencia as cantidad_total', 'inv_inventario_serie.*', 'pro.codigo', 'pro.descripcion', 'pro.nombre as nombre_producto', 'b.nombre as bodega')
            ->get();

        return view('insumos/producto/bajar_producto_tabla', ['productos' => $productos]);
    }

    public function imprimirbarras($id)
    {
        //return $id;
        $pedidos = DB::Select("SELECT  m.*, p.nombre AS nombreproducto, p.id AS id_pedido
        FROM pedido pe
        INNER JOIN inv_cab_movimientos icm ON pe.id = icm.id_pedido
        INNER JOIN inv_det_movimientos m ON icm.id = m.id_inv_cab_movimientos
        INNER JOIN producto p ON m.id_producto = p.id
        WHERE pe.id  = '" . $id . "' GROUP BY m.serie;");

        $pedido = Pedido::find($id);
        $id_p   = $id;
        $numero = $pedido->pedido;
        $data   = $pedidos;
        $date   = date('Y-m-d');
        $view   = \View::make('insumos.producto.pdf', compact('data', 'date', 'numero', 'id_p'))->render();
        $pdf    = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper(array(0, 0, 300, 140));

        return $pdf->stream('Codigo-de-Barra-pedido-n-' . $numero . '.pdf');
    }

    public function pedido($id)
    {

        $pedidos = DB::table('pedido as p')
            ->where('p.id', $id)
            ->join('inv_det_movimientos as m', 'm.id_pedido', 'p.id')
            ->where('m.estado', '1')
            ->join('producto as pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->OrderBy('m.created_at')
            ->groupBy('m.serie')
            //->groupBy('m.tipo')
            ->select(DB::raw('count(*) as cantidad_total'), 'm.serie', 'pro.nombre as nombre_producto', 'b.nombre as nombre_bodega', 'm.*')
            ->get();
        //dd($pedidos);

        return view('insumos/producto/tabla_movimiento', ['productos' => $pedidos]);
    }

    public function nombre(Request $request)
    {

        $nombre = $request['term'];

        $data      = array();
        $productos = DB::table('producto')->where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->id, 'codigo' => $product->codigo);
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
        $productos = DB::table('producto')->where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->first();
        if (!is_null($productos)) {
            $data = $productos->codigo;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        //dd("hola");
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'codigo'              => $request['codigo'],
            'codigo_iess'         => $request['codigo_referencial'],
            'nombre'              => $request['nombre'],
            'tipo_producto'       => $request['tipo_producto'],
            'estado'              => $request['estado'],
        ];
        $productos = $this->doSearchingQuery($constraints);
        $tipos     = Tipo::where('estado', '1')->get();
        $id_empresa = session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        return view('insumos/producto/index', ['request' => $request, 'productos' => $productos, 'searchingVals' => $constraints, 'tipos' => $tipos, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Producto::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
      
        $reglas = [
            'codigo'             => 'required|max:50|unique:producto',
            'codigo_iess'        => 'max:50',
            'nombre'             => 'required|max:255',
            'descripcion'        => 'required|max:255',
            'registro_sanitario' => 'required|max:255',
            'medida'             => 'required',
            'minimo'             => 'required|numeric',
            'cantidad_unidad'    => 'required|numeric',
            'precio_venta'       => 'numeric',
            'precio_compra'      => 'numeric',
            'despacho'           => 'required',
            'id_marca'           => 'required',
            'iva'                => 'required',
            'id_tipo'            => 'required',
            'usos'               => 'required|numeric',
            'cod_general'        => 'required',
        ];

        $mensajes = [
            'codigo.unique'               => 'El Código ya se encuentra registrado.',
            'codigo.required'             => 'Agregue un Código.',
            //'codigo_iess.required'        => 'Agregue un Código IESS.',
            'id_marca.required'           => 'Seleccione marca.',
            'id_tipo.required'            => 'Seleccione un tipo de producto.',
            'codigo.max'                  => 'El código no puede tener mayor a :max caracteres.',
            'codigo_iess.max'             => 'El código IESS no puede tener mayor a :max caracteres.',
            'nombre.required'             => 'Agrega el nombre del Producto.',
            'nombre.max'                  => 'El nombre no puede ser mayor a :max caracteres.',
            'registro_sanitario.required' => 'Agrega el Registro Sanitario del producto.',
            'registro_sanitario.max'      => 'El Registro Sanitario no puede ser mayor a :max caracteres.',
            'descripcion.required'        => 'Agrega la descripcion al producto.',
            'descripcion.max'             => 'La descripcion no puede ser mayor a :max caracteres.',
            'medida.required'             => 'Selecione en que unidad viene el producto.',
            'minimo.required'             => 'Agrega el Stock minimo que debe haber del producto.',
            'minimo.numeric'              => 'La cantidad debe ser en numero',
            'cantidad_unidad.required'    => 'Agrega la cantidad que viene en el envase.',
            'cantidad_unidad.numeric'     => 'La cantidad debe ser en numero',
            'usos.numeric'                => 'La cantidad de usos debe ser en numero',
            'usos.required'               => 'Agregue la cantidad de usos',
            //'precio_venta.required' => 'Agrega el precio de Venta.',
            'precio_venta.numeric'        => 'El precio de Venta debe ser numerico',
            //'precio_compra.required' => 'Agrega el precio de compra.',
            'precio_compra.numeric'       => 'El precio de compra debe ser numerico',
            'despacho.required'           => 'Seleccione una forma de despacho',
            'cod_general.required'        => 'Seleccione un producto contable',
        ];


        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $this->validate($request, $reglas, $mensajes);
            if ($request->kardex_uso == '1') {
                $maximo_procedimientos = $request->cantidad_usos_kardex;
                $cantidad = 1;
                $this->validate($request, ['cantidad_usos_kardex' => 'required|max_procedimiento:' . $cantidad . ',' . $maximo_procedimientos . ',',], ['cantidad_usos_kardex.required' => 'Ingrese la cantidad de usos', 'cantidad_usos_kardex.max_procedimiento' => 'Debe ser mayor a 1',]);
            }
            date_default_timezone_set('America/Guayaquil');
            $arr_total = [];
            if (!is_null($request->input("precio")) or $request->input("precio")) {
                for ($i = 0; $i < count($request->input("precio")); $i++) {
                    if ($request->input("precio")[$i] != "" && $request->input("nivel")[$i] != "") {
                        $arr = [
                            'nivel'  => $request->input("nivel")[$i],
                            'precio' => $request->input("precio")[$i],
                        ];
                        array_push($arr_total, $arr);
                    }
                }
            }
            $nuevo_nombre = null;
            if (!is_null($request->imagen_producto)) {
                $imagen = $request->file('imagen_producto');
                // $nombre_original     = $request['imagen_producto']->getClientOriginalName();
                $nombre_original     = $imagen->getClientOriginalName();
                $extension       = $imagen->getClientOriginalExtension();
                $tiempo = time();
                $nuevo_nombre = "producto_{$tiempo}{$nombre_original}.{$extension}";
                //$nuevo_nombre    = "preparaciones".$fecha.'_'.".".$extension;
                //dd($nuevo_nombre);
                $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['imagen_producto']));
                $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
            }

            $nuevo_nombre2 = null;
            if (!is_null($request->ficha_tecnica)) {
                $imagen = $request->file('ficha_tecnica');
                // $nombre_original     = $request['imagen_producto']->getClientOriginalName();
                $nombre_original     = $imagen->getClientOriginalName();
                $extension       = $imagen->getClientOriginalExtension();
                $tiempo = time();
                $nuevo_nombre2 = "ficha_{$tiempo}{$nombre_original}.{$extension}";
                //$nuevo_nombre2    = "preparaciones".$fecha.'_'.".".$extension;
                //dd($nuevo_nombre2);
                $r1 = Storage::disk('public')->put($nuevo_nombre2, \File::get($request['ficha_tecnica']));
                $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre2;
            }

            $p_id = Producto::insertGetId([
                'codigo'             => strtoupper($request['codigo']),
                'codigo_iess'        => strtoupper($request['codigo_iess']),
                'nombre'             => strtoupper($request['nombre']),
                'id_marca'           => $request['id_marca'],
                'tipo_producto'      => $request['id_tipo'],
                'descripcion'        => strtoupper($request['descripcion']),
                'medida'             => $request['medida'],
                'minimo'             => $request['minimo'],
                'cantidad_unidad'    => $request['cantidad_unidad'],
                'registro_sanitario' => $request['registro_sanitario'],
                'precio_venta'       => $request['precio_venta'],
                'precio_compra'      => $request['precio_compra'],
                'iva'                => $request['iva'],
                'despacho'           => $request['despacho'],
                'usos'               => $request['usos'],
                'codigo_siempre'     => $request['codigo_siempre'],
                'descuento'          => isset($request['descuento']),
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'farmacia'           => $request['farmacia'],
                'tipo'               => $request['tipo'],
                'imagen_producto'    => $nuevo_nombre,
                'ficha_tecnica'    => $nuevo_nombre2,
                'kardex_uso'         => $request->kardex_uso,
                'cantidad_usos_kardex' => $request->cantidad_usos_kardex,
            ]);
            if (!is_null($request['cod_general'])) {
                $empresa = Empresa::find(session('id_empresa'));
                if (isset($empresa->union_producto) && $empresa->union_producto == 1) {
                    $data = [
                        'codigo' => strtoupper($request['codigo']),
                        'nombre' => strtoupper($request['nombre']),
                        'tipo' => $request['id_tipo'],
                        'descripcion' => strtoupper($request['descripcion']),
                        'grupo' => $request['grupo'],
                        'precio1' => $request['precio_venta'],
                        'valor_total_paq' => $request['precio_venta'],
                        'estado_tabla' => 1,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => 'UnionProducto' . date('Y-m-d'),
                        'ip_modificacion' => 'UnionProducto' . date('Y-m-d'),
                        'iva' => $request['iva'],
                        'tipo_producto' => $request['tipo_producto'],
                        'id_empresa' => session('id_empresa'),
                    ];
                    $id_producto_ = Ct_productos::insertGetId($data);
                    /*$codigo = date("ymdHis") . rand(100, 999);
                    $arr_medicamento = [
                        'descripcion' => strtoupper($request['nombre']),
                        'tipo' => 'M',
                        'valor' =>  $request['precio_venta'],
                        'estado' => 1,
                        'tipo_i' => 'F',
                        'codigo' => $codigo,
                        'ip_creacion' => '::1',
                        'ip_modificacion' =>  'UnionProducto' . date('Y-m-d'),
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'id_producto' => $id_producto_,
                    ];
                    ApProcedimiento::create($arr_medicamento);*/
                    $data2 = array(
                        'id_usuariocrea'  => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'codigo_producto' => strtoupper($request['codigo']),
                        'id_insumo'       => $p_id,
                        'id_producto'     =>  $id_producto_,

                    );
                    Ct_productos_insumos::insert($data2);
                } else {
                    $data2 = array(
                        'id_usuariocrea'  => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'codigo_producto' => strtoupper($request['codigo']),
                        'id_insumo'       => $p_id,
                        'id_producto'     => $request["cod_general"],

                    );
                }
            }

            /*
            foreach($arr_total as $valor){
            //for ($i = 1; $i <= $request['contador']; $i++) {
            $precio = [
            'id_producto'     => $p_id,
            'nivel'          => $valor['nivel'],
            'precio'          => $valor['precio'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            ];
            // print_r($precio);
            PrecioProducto::create($precio);
            }*/

            DB::commit();
            return redirect('producto/create')->with('success', 'Producto creado correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('producto/create')->with('error', $e->getMessage()); 
        }
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        // /dd("hola");
        $producto = Producto::find($id);

        // Redirect to user list if updating user wasn't existed
        if ($producto == null) {
            return redirect()->intended('/producto');
        }
        $proveedor    = Proveedor::all();
        $tipos        = Tipo::where('estado', '1')->get();
        $marcas       = Marca::where('estado', '1')->get();
        $ct_productos = Ct_productos::where('estado_tabla', '1')->get();
        $tipo_plantilla = Insumo_Plantilla_Tipo::get();
        $id_empresa = session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        // $precios = PrecioProducto::where('id_producto', $id)->get();
        return view('insumos/producto/edit', ['producto' => $producto, 'proveedores' => $proveedor, 'ct_productos' => $ct_productos, 'marcas' => $marcas, 'tipos' => $tipos, 'tipo_plantilla' => $tipo_plantilla, 'empresa' => $empresa]);
    }

    public function update(Request $request, $id)
    {
        //return $request->all();

        $producto   = Producto::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
            //'codigo.unique'               => 'El Codigo ya se encuentra registrado.',
            'codigo.required'             => 'Agregue un Codigo.',
            'id_marca.required'           => 'Seleccione marca.',
            'id_tipo.required'            => 'Seleccione un tipo de Producto.',
            'nombre.required'             => 'Agrega el nombre del Producto.',
            'nombre.max'                  => 'El nombre no puede ser mayor a :max caracteres.',
            'descripcion.required'        => 'Agrega la descripcion al producto.',
            'registro_sanitario.required' => 'Agrega el Registro Sanitario del producto.',
            'registro_sanitario.max'      => 'El Registro Sanitario no puede ser mayor a :max caracteres.',
            'descripcion.max'             => 'La descripcion no puede ser mayor a :max caracteres.',
            'medida.required'             => 'Selecione en que unidad viene el producto.',
            'minimo.required'             => 'Agrega el Stock minimo que debe haber del producto.',
            'minimo.numeric'              => 'La cantidad debe ser en numero',
            'cantidad_unidad.required'    => 'Agrega la cantidad que viene en el envase.',
            'cantidad_unidad.numeric'     => 'La cantidad debe ser en numero',
            'precio_venta.required'       => 'Agrega el precio de Venta.',
            'precio_venta.numeric'        => 'El precio de Venta debe ser numerico',
            'precio_compra.required'      => 'Agrega el precio de compra.',
            'precio_compra.numeric'       => 'El precio de compra debe ser numerico',
            'despacho.required'           => 'Seleccione una forma de despacho',
            'uso.required'                => 'Agrega el uso que debe tener el producto.',
            'uso.numeric'                 => 'La cantidad debe ser en numero',
            'cod_general.required'        => 'Seleccione un producto contable',
        ];

        $constraints = [
            //'codigo'             => 'required|max:50|unique:producto,id,' . $id,
            //'codigo_iess'        => 'required',
            'nombre'             => 'required|max:255',
            'descripcion'        => 'required|max:255',
            'registro_sanitario' => 'required|max:255',
            'medida'             => 'required',
            'minimo'             => 'required|numeric',
            'cantidad_unidad'    => 'required|numeric',
            //'precio_venta' => 'required|numeric',
            //'precio_compra' => 'required|numeric',
            'id_tipo'            => 'required',
            'despacho'           => 'required',
            'id_marca'           => 'required',
            'uso'                => 'required',
            'cod_general'        => 'required',
        ];


        $nuevo_nombre = null;
        if (!is_null($request->file('imagen_producto'))) {
            $imagen = $request->file('imagen_producto');
            // $nombre_original     = $request['imagen_producto']->getClientOriginalName();
            $nombre_original     = $imagen->getClientOriginalName();

            $extension       = $imagen->getClientOriginalExtension();
            $tiempo = time();
            $nuevo_nombre = "producto_{$tiempo}{$nombre_original}.{$extension}";
            //$nuevo_nombre    = "preparaciones".$fecha.'_'.".".$extension;
            //dd($nuevo_nombre);
            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['imagen_producto']));
            $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
        }


        $prod = Producto::where('codigo', $request['codigo'])->first();
        if (!is_null($prod)) {

            $input = [
                //'codigo' => $request['codigo'],
                'nombre'             => strtoupper($request['nombre']),
                'codigo_iess'        => strtoupper($request['codigo_iess']),
                'descripcion'        => strtoupper($request['descripcion']),
                'id_marca'           => $request['id_marca'],
                'estado'             => $request['estado'],
                'medida'             => $request['medida'],
                'minimo'             => $request['minimo'],
                'registro_sanitario' => $request['registro_sanitario'],
                'precio_venta'       => $request['precio_venta'],
                'precio_compra'      => $request['precio_compra'],
                'usos'               => $request['uso'],
                'codigo_siempre'     => $request['codigo_siempre'],
                'tipo_producto'      => $request['id_tipo'],
                'despacho'           => $request['despacho'],
                'cantidad_unidad'    => $request['cantidad_unidad'],
                'iva'                => $request['iva'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'tipo'               => $request['tipo'],
                'imagen_producto'    => $nuevo_nombre,
                ////// VICTOR KARDEX POR USOS
                'kardex_uso'         => $request->kardex_uso,
                'cantidad_usos_kardex' => $request->cantidad_usos_kardex,

            ];
        } else {

            $input = [
                //'codigo'             => strtoupper($request['codigo']),
                'codigo_iess'        => strtoupper($request['codigo_iess']),
                'nombre'             => strtoupper($request['nombre']),
                'descripcion'        => strtoupper($request['descripcion']),
                'id_marca'           => $request['id_marca'],
                'estado'             => $request['estado'],
                'medida'             => $request['medida'],
                'minimo'             => $request['minimo'],
                'registro_sanitario' => $request['registro_sanitario'],
                'precio_venta'       => $request['precio_venta'],
                'precio_compra'      => $request['precio_compra'],
                'usos'               => $request['uso'],
                'codigo_siempre'     => $request['codigo_siempre'],
                'tipo_producto'      => $request['id_tipo'],
                'despacho'           => $request['despacho'],
                'iva'                => $request['iva'],
                'cantidad_unidad'    => $request['cantidad_unidad'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'tipo'               => $request['tipo'],
                'imagen_producto'    => $nuevo_nombre,
                ////////// VICTOR KARDEX POR USOS
                'kardex_uso'         => $request->kardex_uso,
                'cantidad_usos_kardex' => $request->cantidad_usos_kardex,
            ];
        }
        ////////// VICTOR KARDEX POR USOS
        if ($request->despacho == '1' and $request->kardex_uso == '1') {
            $rules = [
                'cantidad_usos_kardex' => 'numeric|min: 1',
            ];
            $msn = [
                'cantidad_usos_kardex.numeric' => 'Ingrese un número',
                'cantidad_usos_kardex.min'     => 'Ingrese un número mayor a 0',
            ];
            $this->validate($request, $rules, $msn);
        }
        ////////// VICTOR KARDEX POR USOS

        $this->validate($request, $constraints, $mensajes);

        Producto::where('id', $id)
            ->update($input);

        /*           $arr_total = [];
        for($i=0;$i<count($request->input("precio"));$i++){
        if($request->input("precio")[$i] != "" && $request->input("nivel")[$i] !=""){
        $arr = ['nivel'=>$request->input("nivel")[$i],
        'precio' =>$request->input("precio")[$i]
        ];
        //    print_r($arr);
        array_push($arr_total, $arr);
        }

        }
        foreach($arr_total as $valor){
        //for ($i = 1; $i <= $request['contador']; $i++) {
        $precio = [
        'id_producto'     => $id,
        'nivel'          => $valor['nivel'],
        'precio'          => $valor['precio'],
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ];
        // print_r($precio);
        PrecioProducto::create($precio);
        }
         */
        $s      = Ct_productos_insumos::where('id_insumo', $id)->first();
        $inputf = [
            'id_producto'     => $request['cod_general'],
            'id_usuariocrea'  => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        if (!is_null($request['cod_general'])) {
            if (!is_null($s) && $s != '[]') {
                $s->update($inputf);
            } else {
                $data2 = array(
                    'id_usuariocrea'  => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'codigo_producto' => strtoupper($request['codigo']),
                    'id_insumo'       => $id,
                    'id_producto'     => $request['cod_general'],

                );

                Ct_productos_insumos::insert($data2);
            }
        }

        return redirect()->intended('/producto');
    }

    public function seguimiento($id)
    {
        //dd($id);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $productos = DB::table('log_movimiento as lm')
            ->where('lm.id_producto', $id)
            ->join('producto as p', 'p.id', 'lm.id_producto')
            ->join('movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('lm.tipo', 'm.serie')
            ->select(DB::raw('count(*) as cantidad_total, lm.tipo'), 'lm.*', 'p.nombre', 'p.cantidad as ptotal', 'm.serie', 'm.fecha_vencimiento', 'b.nombre as nombre_bodega')
            ->OrderBy('lm.created_at', 'desc')->paginate(15);
        //dd($productos);

        $producto = Producto::find($id);

        return view('insumos/producto/seguimiento', ['producto' => $producto, 'productos' => $productos]);
    }

    public function vt_dar_baja_producto(Request $request)
    {


        $serie         = $request->serie;
        $id_bodega     = $request->id_bodega;
        $cantidad_baja = $request->cantidad_baja;
        $observacion   = $request->observacion;

        $fecha_actual = date('Y-m-d H:i:s');

        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        DB::beginTransaction();
        try {

            $id_empresa = Session::get('id_empresa');
            $empresa    = Empresa::find($id_empresa);

            if (is_null($empresa)) {
                return ['estado' => 'Error', 'mensaje' => 'No existe empresa'];
            }

            //$movimiento = Movimiento::where( 'serie', $serie )->where( 'id_bodega', $id_bodega )->first();
            $movimiento = Movimiento::where('serie', $serie)->first();
            //EDUARDO INDICA QUE NO DEBE TENER BODEGA


            if (!is_null($movimiento)) {

                $producto = $movimiento->producto;
                $cantidad = $movimiento->cantidad;

                if ($cantidad_baja > $cantidad) {
                    //return [ 'estado' => 'Error', 'mensaje' => 'Cantidad supera a la que existe' ];     
                }

                $cantidad -= $cantidad_baja;


                $tipo_documento = '6';
                $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EGR')->first();
                if (is_null($inv_documento_bod)) {
                    return ['estado' => 'Error', 'mensaje' => 'No existe Documento'];
                }
                $tipo_documento = $inv_documento_bod->id; //dd($tipo_documento);

                $secuencia = InvDocumentosBodegas::getSecuecia($tipo_documento, $id_bodega);

                if ($secuencia != 0) {

                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $tipo_documento)
                        ->where('id_bodega', $id_bodega)
                        ->first();

                    $inventario = InvInventario::where('id_producto', $producto->id)
                        ->where('id_bodega', $id_bodega)
                        //->where('tipo', $tipo)
                        ->where('estado', 1)
                        ->where('id_empresa', $id_empresa)
                        ->first();

                    $iserie = InvInventarioSerie::where('serie', $serie)
                        ->where('id_bodega', $id_bodega)
                        //->where('tipo', $tipo)
                        ->where('estado', 1)
                        ->where('id_empresa', $id_empresa)
                        ->first();

                    if (is_null($inventario)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe Inventario'];
                    }

                    $existencia = $inventario->existencia;
                    if ($cantidad_baja > $existencia) {
                        return ['estado' => 'Error', 'mensaje' => 'Cantidad supera a la existencia en Inventario'];
                    }

                    if (is_null($iserie)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe Inventario Serie'];
                    }

                    $existencia2 = $iserie->existencia;
                    if ($cantidad_baja > $existencia2) {
                        return ['estado' => 'Error', 'mensaje' => 'Cantidad supera a la existencia en Inventario por Serie'];
                    }

                    $primer_movimiento = InvDetMovimientos::where('serie', $serie)->where('estado', '1')->OrderBy('created_at', 'asc')->first();
                    if (is_null($primer_movimiento)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe primer movimiento'];
                    }

                    $cabecera_1mov = $primer_movimiento->cabecera;

                    if ($cabecera_1mov->id_pedido == null) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe pedido'];
                    }

                    if ($cabecera_1mov->id_documento_bodega == null) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe documento del pedido'];
                    }

                    $pedido_1mov = $cabecera_1mov->pedido;

                    $documento_1mov = $cabecera_1mov->documento_bodega;

                    $tipo_1mov = $documento_1mov->tipo;

                    $estado_contable = $pedido_1mov->estado_contable;

                    $genera_asiento = false;
                    if ($tipo_1mov == 'C') {
                        $genera_asiento = true;
                    }
                    if ($tipo_1mov == 'F') {
                        if ($estado_contable == '1') {
                            $genera_asiento = true;
                        }
                    }

                    if ($genera_asiento) {
                        $plan_cuentas_debe  = null;
                        $plan_cuentas_haber = null;
                        if ($tipo_1mov == 'C') { //CONCESION
                            $plan_cuentas_debe  = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_PROVISIONES_CUENTAS_POR_PAGAR');
                            // 2.01.01.01.02 
                            $plan_cuentas_haber = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_MERCADERIA_EN_CONSIGNACION');
                            //1.01.03.01.04 

                        }
                        if ($tipo_1mov == 'F') { //FACTURA
                            $plan_cuentas_debe  = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_PERDIDA_POR_FALTANTE_INVENTARIO');
                            // 5.2.01.06.02 
                            $plan_cuentas_haber = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_INVENTARIOS_PROD_ALMACEN_COMPRADO_TERCEROS');
                            // 1.01.03.01.01 
                        }

                        if (is_null($plan_cuentas_debe)) {
                            return ['estado' => 'Error', 'mensaje' => 'No esta configurada la cuenta para el Debe'];
                        }
                        if (is_null($plan_cuentas_haber)) {
                            return ['estado' => 'Error', 'mensaje' => 'No esta configurada la cuenta para el Haber'];
                        }
                    }


                    if (!is_null($transaccion)) {

                        $descuento      = 0;
                        $mov_unitario   = $inventario->costo_promedio;
                        $mov_subtotal   = $cantidad_baja * $mov_unitario;
                        $mov_subtotal_0 = $mov_subtotal;

                        $movimiento->update([
                            'cantidad' => $cantidad,
                        ]);

                        $id_asiento_cabecera = null;

                        if ($mov_subtotal > 0) {

                            if ($genera_asiento) {

                                $input_cabecera = [
                                    'fecha_asiento'   => $fecha_actual,
                                    'id_empresa'      => $id_empresa,
                                    'observacion'     => 'DAR DE BAJA PRODUCTO: ' . $observacion,
                                    'valor'           => $mov_subtotal,
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                    'ip_creacion'     => $ip_cliente,
                                    'ip_modificacion' => $ip_cliente,
                                ];

                                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera' => $id_asiento_cabecera,
                                    'id_plan_cuenta'      => $plan_cuentas_debe->cuenta_guardar,
                                    'descripcion'         => $plan_cuentas_debe->nombre_mostrar,
                                    'fecha'               => $fecha_actual,
                                    'debe'                => $mov_subtotal,
                                    'haber'               => 0,
                                    'id_usuariocrea'      => $idusuario,
                                    'id_usuariomod'       => $idusuario,
                                    'ip_creacion'         => $ip_cliente,
                                    'ip_modificacion'     => $ip_cliente,

                                ]);

                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera' => $id_asiento_cabecera,
                                    'id_plan_cuenta'      => $plan_cuentas_haber->cuenta_guardar,
                                    'descripcion'         => $plan_cuentas_haber->nombre_mostrar,
                                    'fecha'               => $fecha_actual,
                                    'debe'                => 0,
                                    'haber'               => $mov_subtotal,
                                    'id_usuariocrea'      => $idusuario,
                                    'id_usuariomod'       => $idusuario,
                                    'ip_creacion'         => $ip_cliente,
                                    'ip_modificacion'     => $ip_cliente,

                                ]);
                            }
                        }
                        //CABECERA MOVIMIENTO
                        $a_cab_mov = [
                            'id_documento_bodega'       => $tipo_documento,
                            'id_transaccion_bodega'     => $transaccion->id,
                            'id_bodega_origen'          => $id_bodega, //'en caso de traslado',
                            'id_bodega_destino'         => $id_bodega, //'en caso de traslado',
                            'numero_documento'          => str_pad($secuencia, 9, "0", STR_PAD_LEFT), //'Numero del documento interno',
                            //'num_doc_ext'  //'Numero de la guia, Numero proforma',
                            //'num_doc_cont'  //'Numero de la factura',
                            'observacion'               => $observacion,
                            'fecha'                     => date('Y-m-d'),
                            'descuento'                 => $descuento,
                            'subtotal'                  => $mov_subtotal,
                            'subtotal_0'                => $mov_subtotal_0,
                            'iva'                       => 0,
                            'total'                     => $mov_subtotal_0,
                            'estado'                    => 1,
                            //'id_movimiento_estado' ,
                            //'id_pedido'  //'hace referencia a la tabla de pedido',
                            //'id_agenda'  //'hace referencia a la agenda',
                            //'id_docum_origen'  //'hace referencia a la misma tabla',
                            //'id_hc_procedimientos' ,
                            'id_asiento'                => $id_asiento_cabecera,
                            'id_empresa'                => $id_empresa,

                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,

                        ];

                        $id_cabecera = InvCabMovimientos::insertGetId($a_cab_mov);

                        //DETALLE MOVIMIENTO
                        $arr_detalle = [
                            'id_inv_cab_movimientos'      => $id_cabecera,
                            'id_producto'                 => $producto->id,
                            'serie'                       => $serie,
                            //'fecha_vence'                 =>,
                            //'lote'                        =>,
                            'id_inv_inventario'           => $inventario->id,
                            'cantidad'                    => $cantidad_baja,
                            'cant_uso'                    => $cantidad_baja,
                            'valor_unitario'              => $mov_unitario,
                            'subtotal'                    => $mov_subtotal,
                            'descuento'                   => $descuento,
                            'iva'                         => 0,
                            'total'                       => $mov_subtotal,
                            'estado'                      => 1,
                            'kardex'                      => 1, //'1: si ya realizo el movimiento en inventario, inv serie y kardex; 0: No',
                            'motivo'                      => $observacion,
                            //'id_detalle_origen'           => ,
                            //.'id_pedido'                   => ,
                            'id_detalle_pedido'           => $movimiento->id,
                            //'id_procedimiento'            => ,
                            //'id_movimiento_paciente'      => ,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $inv_det_mov = InvDetMovimientos::insertGetId($arr_detalle);

                        $existencia = $inventario->existencia;

                        $existencia -= $cantidad_baja;
                        //$existencia --;
                        $existencia_uso = $inventario->existencia_uso;
                        //$existencia_uso --;
                        $existencia_uso -= $cantidad_baja;

                        //INVENTARIO
                        $inventario->update([
                            'existencia'      => $existencia,
                            'existencia_uso'  => $existencia_uso,
                        ]);

                        $existencia2 = $iserie->existencia;
                        //$existencia2 --;
                        $existencia2 -= $cantidad_baja;
                        $existencia_uso2 = $iserie->existencia_uso;
                        //$existencia_uso2 --;
                        $existencia_uso2 -= $cantidad_baja;

                        //INVENTARIO SERIE
                        $iserie->update([
                            'existencia'      => $existencia2,
                            'existencia_uso'  => $existencia_uso2,
                        ]);

                        $arr_kardex = [
                            'id_inv_inventario'         => $inventario->id,
                            'id_bodega'                 => $id_bodega,
                            'id_producto'               => $producto->id,
                            'tipo'                      => 'E',
                            'descripcion'               => $observacion,
                            'referencia'                => 'DAR DE BAJA PRODUCTO ' . $producto->id . '-' . $producto->nombre, //'referencia contable',
                            'fecha'                     => date('Y-m-d'),
                            'cantidad'                  => $cantidad_baja,
                            'cant_uso'                  => $cantidad_baja,
                            'valor_unitario'            => $mov_unitario,
                            'iva'                       => 0,
                            'total'                     => $mov_subtotal,
                            'exist_cant'                => $existencia,
                            'exist_uso'                 => $existencia_uso,
                            //'exist_valor_unitario' ,
                            //'exist_total' ,
                            'estado'                    => 1,
                            'id_documento_bodega'       => $tipo_documento,
                            'id_inv_det_movimientos'    => $inv_det_mov,
                            'id_empresa'                => $id_empresa,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'dar_baja'        => 1,

                        ];

                        InvKardex::create($arr_kardex);
                        DB::commit();
                        return ['estado' => 'Ok', 'mensaje' => 'Dado de Baja'];
                    }
                }

                return ['estado' => 'Error', 'mensaje' => 'No existe documento bodega'];
            } else {

                return ['estado' => 'Error', 'mensaje' => 'No existe Movimiento'];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }

        return ['estado' => 'Error', 'mensaje' => 'No existe producto por bodega'];
    }

    public function borrar(Request $request)
    {
        //return $request->all();
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cantidad          = $request['cantidad'];
        $tipo              = $request['tipo'];
        $id_producto       = $request['id_producto'];
        $serie             = $request['serie'];
        $id_bodega         = $request['id_bodega'];
        $id_pedido         = $request['id_pedido'];
        $fecha_vencimiento = $request['f_venci'];
        $lote              = $request['lote'];
        if ($idusuario == "1316262193") {
            //dd($request->all());
        }
        if (!is_null($cantidad)) {
            for ($x = 0; $x < $cantidad; $x++) {
                if ($tipo == '1') {
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto - 1;
                    $input3            = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Producto::where('id', $id_producto)->update($input3);

                    $input = [
                        'cantidad'        => '1',
                        'usos'            => '1',
                        'id_encargado'    => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => '4',
                    ];

                    $movimiento_cambio = Movimiento::where('serie', $serie)->where('tipo', '1')->where('usos', '>=', '1')->first();
                    $movimiento_cambio->update($input);
                    $id_movimiento = $movimiento_cambio->id;

                    Log_movimiento::create([
                        'id_producto'     => $id_producto,
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Producto dado de baja",
                        'motivo'          => $request['motivo'],
                        'tipo'            => '4',
                        'cantidad'        => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                } elseif ($tipo == '2') {

                    $input = [
                        'cantidad'        => '1',
                        'usos'            => '1',
                        'id_encargado'    => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => '4',
                    ];
                    $movimiento_cambio = Movimiento::where('serie', $serie)->where('tipo', '2')->where('usos', '>=', '1')->first();
                    $movimiento_cambio->update($input);
                    $id_movimiento = $movimiento_cambio->id;

                    Log_movimiento::create([
                        'id_producto'     => $id_producto,
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Producto dado de baja",
                        'motivo'          => $request['motivo'],
                        'tipo'            => '4',
                        'cantidad'        => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
            }
            return redirect()->intended('/producto');
        } else {
            return redirect()->intended('/producto');
        }
    }

    public function reporte()
    {
        $productos = DB::select("SELECT p.id as num_pedido, pro.id as codigo_prod, mar.nombre as marca,
                                p.vencimiento as f_vencimiento, pro.registro_sanitario, pro.descripcion,
                                m.serie, m.estado, m.lote, b.nombre as bodega
                                from pedido p, movimiento m, producto pro, marca mar, log_movimiento lm, bodega b
                                where m.id_pedido = p.id AND
                                pro.id = m.id_producto AND
                                pro.id_marca = mar.id AND
                                m.id_bodega = b.id AND
                                pro.id = lm.id_producto GROUP BY p.id ;");
        //dd($productos);

        Excel::create('Reporte de Insumos en Bodega ', function ($excel) use ($productos) {

            $excel->sheet(date('Y-m-d'), function ($sheet) use ($productos) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# de Pedido');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MARCA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO DE PRODUCTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# DE LOTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('FECHA DE VENCIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REG. SANITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION DEL PRODUCTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO SERIE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BODEGA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = 2;
                foreach ($productos as $value) {
                    //dd($value);

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->num_pedido);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->marca);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo_prod);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->lote);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->f_vencimiento);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->registro_sanitario);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->serie);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->bodega);
                    });
                    $i++;
                }
            });
        })->export('xlsx');
    }

    public function guardar_bajar_producto(Request $request)
    {

        //return $request->all();
        //dd("sdfsdf");
        $serie      = $request['serie'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $motivo     = $request['motivo'];
        $cant_baja  = $request['cant_baja'];

        $query = "SELECT m.*, p.*, m.usos as usos_producto, p.cantidad as cantidad1, p.id as producto_id
              FROM movimiento m, producto p
              WHERE m.serie LIKE '" . $serie . "' AND
             m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";

        $productos = DB::select($query);

        if ($productos != array()) {
            $anterior           = movimiento::where('serie', $serie)->first();
            $cantidad_productos = $anterior->cantidad - $cant_baja;
            $calculo            = $productos[0]->usos;
            $id                 = $productos[0]->id;
            $producto_id        = $productos[0]->producto_id;
            $movimiento         = Movimiento::where('serie', $serie)->get();
            // dd($cantidad);
            $id_movimiento = $movimiento[0]->id;

            $input = [
                'cantidad'        => $cantidad_productos,
                'usos'            => $calculo,
                'id_encargado'    => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Movimiento::where('serie', $serie)->update($input);

            $input2 = [
                'cantidad'        => $cantidad_productos,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            Producto::where('id', $producto_id)->update($input2);

            Log_movimiento::create([
                'id_producto'     => $id,
                'id_encargado'    => $idusuario,
                'id_movimiento'   => $id_movimiento,
                'observacion'     => $motivo,
                'tipo'            => '4',
                'cantidad'        => $cant_baja,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }
        return redirect()->intended('/producto');
    }

    public function imprimir_barra($id)
    {
        $producto = Producto::findOrFail($id);
        if ($producto->codigo_siempre == 1) {
            $data = $producto;
            $date = date('Y-m-d');
            $view = \View::make('insumos.producto.unico', compact('data', 'date'))->render();
            $pdf  = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper(array(0, 0, 300, 120));

            return $pdf->stream('Codigo-de-Barra-pedido-n-' . $id . '.pdf');
        } else {
            return redirect()->route('producto.index');
        }
    }
    public function buscarProductos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $producto = [];
        if (!is_null($request["search"])) {
            $producto1 = Producto::where('codigo', "LIKE", "%{$request['search']}%")->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));
            $producto2 = Producto::where('nombre', 'LIKE', "%{$request['search']}%")->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));
            $producto = $producto1->union($producto2)->take(20)->get();
        }
        return response()->json($producto);
    }

    public static function buscadorPedidos($request)
    {
        $pedido     = $request['numerodepedido'];
        $proveedor  = $request['razonsocial'];
        $id_empresa = $request->session()->get('id_empresa');
        $pedidos = Pedido::whereNull('pedido.estado_compras')
            //->where('p.pedido', 'like', '%' . $pedido . '%')
            ->join('proveedor as pro', 'pro.id', 'pedido.id_proveedor')
            ->join('users as u', 'u.id', 'pedido.id_usuariocrea')
            ->where('pedido.id_empresa', $id_empresa)
            ->select('pedido.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial', 'pro.razonsocial');

        if (!is_null($pedido)) {
            $pedidos = $pedidos->where('pedido.pedido', 'like', '%' . $pedido . '%');
        }

        if (!is_null($proveedor)) {
            $pedidos = $pedidos->where('pro.id', 'like', '%' . $proveedor . '%');
        }

        if (!is_null($request->producto) or !is_null($request->serie)) {
            $pedidos = $pedidos->join('movimiento as m', 'm.id_pedido', 'pedido.id');
        }

        if (!is_null($request->producto)) {
            $pedidos = $pedidos->where('m.id_producto', $request->producto)->groupBy('pedido.id');
        }

        if (!is_null($request->serie)) {
            $pedidos = $pedidos->where('m.serie', 'like', "%{$request->serie}%")->groupBy('pedido.id');
        }


        if (!is_null($request->fecha_desde)) {
            $pedidos = $pedidos->where('pedido.fecha', '>=', $request->fecha_desde)->groupBy('pedido.id');
        }

        if (!is_null($request->fecha_hasta)) {
            $pedidos = $pedidos->where('pedido.fecha', '<=', $request->fecha_hasta)->groupBy('pedido.id');
        }


        return $pedidos;
    }

    public function reportePedidoMasivo(Request $request)
    {
        //dd($id_pedido);
        // $pedidos = Pedido::where('id_empresa', Session::get('id_empresa'))->get();
        $pedidos = ProductoController::buscadorPedidos($request);

        $pedidos         = $pedidos->OrderBy('pedido.created_at', 'desc')->get();
        // dd($request->all());
        if (count($pedidos) > 0) {
            Excel::create("PEDIDO", function ($excel) use ($pedidos) {
                foreach ($pedidos as $pedido) {
                    // $id_pedido = $pedido->id;
                    $excel->sheet("PEDIDO", function ($sheet) use ($pedido) {

                        $comienzo = 1;
                        $inv_det = InvDetMovimientos::where('id_pedido', $pedido->id)->get();
                        // $detalle = $pedido->detalle;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["FECHA PEDIDO", strtoupper($pedido->fecha)]);
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["# FACTURA", strtoupper($pedido->factura)]);
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["PROVEEDOR", isset($pedido->proveedor) ? strtoupper($pedido->proveedor->nombrecomercial) : $pedido->id_proveedor]);
                        $comienzo++;
                        $doc_bodega = $pedido->tipo;
                        if (isset($pedido->movimiento_inv)) {
                            if (isset($pedido->movimiento_inv->documento_bodega)) {
                                $doc_bodega = strtoupper($pedido->movimiento_inv->documento_bodega->documento);
                            }
                        }

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["A", "B"], ["DOC. BODEGA", $doc_bodega]);
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, 1, ["D", "E"], ["# PEDIDO", $pedido->pedido]);

                        ImportacionesController::excelDetalles($sheet, 2, ["D", "E"], ["FECHA VENCE", $pedido->vencimiento]);

                        ImportacionesController::excelDetalles($sheet, 3, ["D", "E"], ["BODEGA RECIBE", isset($pedido->bodega) ? $pedido->bodega->nombre : $pedido->id_bodega]);
                        $comienzo++;


                        $titles['comienzo'] = $comienzo;
                        $titles["data"] = ['Codigo', 'Nombre', 'Cantidad', 'Usos',    'Serie', 'Bodegas', 'Lote', 'Registro Sanitario', 'Fecha de Vencimiento', 'Precio unitario', 'Traslado', 'Existencia', 'Precio final'];
                        $titles["color"] = "#ffff";
                        $titles["background-color"] = "#000000";
                        excelCreate::details($sheet, $titles);
                        $comienzo++;
                        if (count($inv_det) > 0) {
                            foreach ($inv_det as $value) {
                                if (!is_null($value->id_detalle_pedido)) {
                                    $details["data"] = [
                                        isset($value->producto) ? $value->producto->codigo : $value->id_producto,
                                        isset($value->producto) ? $value->producto->nombre : '',
                                        $value->cantidad,
                                        $value->cant_uso,
                                        $value->serie . "-",
                                        isset($value->bodega) ? $value->bodega->nombre : 'N/A',
                                        $value->lote,
                                        isset($value->producto) ? $value->producto->registro_sanitario : 'N/A',
                                        $value->fecha_vence,
                                        $value->valor_unitario,
                                        Pedido::cant_traslado($value->serie),
                                        Pedido::existencia($value->serie, $pedido->id_bodega),
                                        $value->valor_unitario * $value->cantidad,
                                    ];
                                    $details['comienzo'] = $comienzo;
                                    excelCreate::details($sheet, $details);
                                    $comienzo++;
                                }
                            }
                        } else {
                            foreach ($pedido->detalle as $value) {
                                //dd($value);
                                $details["data"] = [
                                    isset($value->producto) ? $value->producto->codigo : $value->id_producto,
                                    isset($value->producto) ? $value->producto->nombre : '',
                                    $value->cantidad,
                                    $value->usos,
                                    $value->serie . "-",
                                    isset($value->bodega) ? $value->bodega->nombre : 'N/A',
                                    $value->lote,
                                    isset($value->producto) ? $value->producto->registro_sanitario : 'N/A',
                                    $value->fecha_vencimiento,
                                    $value->precio,
                                    Pedido::cant_traslado($value->serie),
                                    Pedido::existencia($value->serie, $pedido->id_bodega),
                                    $value->precio * $value->cantidad,
                                ];
                                $details['comienzo'] = $comienzo;
                                excelCreate::details($sheet, $details);
                                $comienzo++;
                            }
                        }
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["SUBTOTAL 12%", $pedido->subtotal_12]);
                        $comienzo++;


                        ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["SUBTOTAL 0%", $pedido->subtotal_0]);
                        $comienzo++;


                        ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["DESCUENTO", $pedido->descuento]);
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["IVA", $pedido->iva]);
                        $comienzo++;

                        ImportacionesController::excelDetalles($sheet, $comienzo, ["L", "M"], ["TOTAL", $pedido->total]);
                        $comienzo++;
                    });
                }
            })->export('xlsx');
        } else {
            return redirect()->intended('/codigo/barras');
        }
    }
    public function reporte_listado_productos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        $productos = Producto::where('producto.estado', "1")->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Listado_Productos' . $fecha_d, function ($excel) use ($productos) {

            $excel->sheet('Reporte Listado de Productos', function ($sheet) use ($productos) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:R2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte de Listado de Productos');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = 3;

                $arrTitulos = ["Id", "Codigo", "Codigo Referencial", "Nombre", "Descripción", "Cantidad de Medida", "Medida", "Forma de despacho", "Stock Minimo", "Registro Sanitario", "Marca", "Tipo de Producto", "Cantidad de usos", "Posee Iva", "Codigo Siempre", "Kardex Por Usos", "Cantidad de Kardex Por usos", "Estado"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                //dd($biopsias->get());
                $fecha_hoy = date('Y/m/d');
                $forma_despacho = "";
                $marca = "";
                $tipo_producto = "";
                $iva = "";
                $codigo_siempre = "";
                $kxu = "";

                foreach ($productos as $value) {


                    if ($value->despacho == '1') {
                        $forma_despacho = 'Codigo de Serie';
                    }
                    if ($value->despacho == '0') {
                        $forma_despacho = 'Codigo de Producto';
                    }
                    if (!is_null($value->marca)) {
                        $marca = $value->marca->nombre;
                    }
                    if (!is_null($value->fx_tipo)) {
                        $tipo_producto = $value->fx_tipo->nombre;
                    }

                    if ($value->iva == '1') {
                        $iva = 'SI';
                    } else {
                        $iva = 'NO';
                    }
                    if ($value->codigo_siempre == '1') {
                        $codigo_siempre = "SI";
                    } else {
                        $codigo_siempre = "NO";
                    }
                    if ($value->kardex_uso == '1') {
                        $kxu = "SI";
                    } else {
                        $kxu = "NO";
                    }
                    $arrValue = [];

                    $arrValue = [$value->id, $value->codigo, $value->codigo_iess, $value->nombre, $value->descripcion, $value->cantidad_unidad, $value->medida, $forma_despacho, $value->minimo, $value->registro_sanitario,  $marca, $tipo_producto, $value->usos, $iva, $codigo_siempre, $kxu, $value->cantidad_usos_kardex, "ACTIVO"];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    /*public function recalcular_pedido($id_pedido)
    {

        $pedido   = Pedido::find($id_pedido);
        $detalles = $pedido->det_movi;

        $cabecera = $pedido->movimiento_inv;

        if(is_null($cabecera)){
            return "ok";
        }

        $total       = 0;
        $subtotal_0  = 0;
        $subtotal_12 = 0;
        $iva         = 0;
        $descuento   = 0;

        foreach ($detalles as $detalle) {

            if ($detalle->iva > 0) {
                $subtotal_12 += $detalle->subtotal;
            } else {
                $subtotal_0 += $detalle->subtotal;
            }
            $iva += $detalle->iva;
            $descuento += $detalle->descuento;

            $total = $subtotal_0 + $subtotal_12 + $iva - $descuento;
        }

        $subtotal_final = $subtotal_0 + $subtotal_12;
        $id_asiento = $cabecera->id_asiento;
        if ($id_asiento != null) {
            $asiento    = Ct_Asientos_Cabecera::find($id_asiento);
            if (!is_null($asiento)) {
                $asiento->update(['valor' => $subtotal_final]);
                //$adetalles = $asiento->detalles;
                //foreach($adetalles as $adetalle){

                //}
            }
        }

        $input = [
            'total'       => $total,
            'subtotal_0'  => $subtotal_0,
            'subtotal_12' => $subtotal_12,
            'iva'         => $iva,
            'descuento'   => $descuento
        ];


        $input2 = [
            'total'       => $total,
            'subtotal_0'  => $subtotal_0,
            'subtotal'    => $subtotal_12,
            'iva'         => $iva,
            'descuento'   => $descuento
        ];

        $pedido->update($input);
        $cabecera->update($input2);

        return "ok";
    }*/
    public function recalcular_pedido($id_pedido)
    {

        $pedido   = Pedido::find($id_pedido);
        $detalles = $pedido->det_movi;

        $cabecera = $pedido->movimiento_inv;

        $total       = 0;
        $subtotal_0  = 0;
        $subtotal_12 = 0;
        $iva         = 0;
        $descuento   = 0;
        $total_final = 0;
        $valor_iva   = 0;
        

        foreach ($detalles as $detalle) {
            $cantidad = $detalle->cantidad;
            $total_u = $cantidad *  $detalle->valor_unitario;
            
            $conf = Ct_Configuraciones::find(3);
            if ($detalle->iva > 0) {
                $subtotal_12 += ($detalle->subtotal - ($total_u*$detalle->descuento/100));
                $subtotal_12 = round($subtotal_12, 2);
                 $valor_iva = ($detalle->subtotal - ($total_u*$detalle->descuento/100)) * ( $conf->iva);
                //$subtotal_12 += ($detalle->subtotal);

            } else {
                $subtotal_0 += ($detalle->subtotal - ($total_u*$detalle->descuento/100));
                $subtotal_0 = round($subtotal_0,2);
                //$subtotal_0 += ($detalle->subtotal);
            }           
            //$iva += $detalle->iva;
            $iva += $valor_iva;
            //$descuento += $detalle->descuento;
            $descuento += ($total_u*$detalle->descuento/100);
            $descuento = round($descuento,2);
            $total_final += $cantidad *  $detalle->valor_unitario;
            

            //$total = $subtotal_0 + $subtotal_12 + $iva - $descuento;
            $total = $total_final + $iva - $descuento;
        }

        $subtotal_final = $subtotal_0 + $subtotal_12;
        $id_asiento = $cabecera->id_asiento;
        if ($id_asiento != null) {
            $asiento    = Ct_Asientos_Cabecera::find($id_asiento);
            if (!is_null($asiento)) {
                $asiento->update(['valor' => $subtotal_final]);
                /*$adetalles = $asiento->detalles;
                foreach($adetalles as $adetalle){

                }*/
            }
        }

        $input = [
            'total'       => $total,
            'subtotal_0'  => $subtotal_0,
            'subtotal_12' => $subtotal_12,
            'iva'         => $iva,
            'descuento'   => $descuento
        ];


        $input2 = [
            'total'       => $total,
            'subtotal_0'  => $subtotal_0,
            'subtotal'    => $subtotal_12,
            'iva'         => $iva,
            'descuento'   => $descuento
        ];

        $pedido->update($input);
        $cabecera->update($input2);

        return "ok";
    }
}
