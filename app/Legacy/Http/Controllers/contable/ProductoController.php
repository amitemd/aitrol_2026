<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Excel;
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
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Tipo;
use Sis_medico\Insumo_Plantilla_Tipo;

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
        $productos = Producto::paginate(20);
        $tipos     = Tipo::all();
        return view('insumos/producto/index', ['productos' => $productos, 'tipos' => $tipos]);
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
        return view('insumos/producto/create', ['proveedores' => $proveedor, 'marcas' => $marcas, 'tipos' => $tipos, 'ct_productos' => $ct_productos, 'tipo_plantilla' => $tipo_plantilla]);
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

        $pedido = Pedido::find($id_pedido);

        $detalle  = Movimiento::where('id_pedido', $id_pedido)->get();
        $pedido   = Pedido::find($id_pedido);
        $detalles = array();
        $mov      = array();
        foreach ($detalle as $value) {
            # datos
            $mov['codigo']             = $value->producto->codigo;
            $mov['nombre']             = $value->producto->nombre;
            $mov['cantidad']           = $value->cantidad;
            $mov['usos']               = $value->usos;
            $mov['serie']              = $value->serie;
            $mov['bodega']             = (isset($value->bodega)) ? $value->bodega->nombre : "";
            $mov['lote']               = $value->lote;
            $mov['registro_sanitario'] = $value->producto->registro_sanitario;
            $mov['fecha_vencimiento']  = $value->fecha_vencimiento;
            $mov['precio']             = $value->precio;
            $mov['total']              = round(($value->precio * $value->cantidad), 2);
            # traslado
            $mov['traslado'] = Pedido::cant_traslado($value->serie);
            # existencia
            $mov['existencia'] = Pedido::existencia($value->serie, $pedido->id_bodega);
            # facturado
            $mov['facturado'] = Pedido::facturado($value->serie);

            $detalles[] = $mov;
        }
        //
        //->select('log_movimiento.id_producto', DB::raw('SUM(log_movimiento.cantidad) as cantidad_total'))
        //->groupBy('id_producto')

        return view('insumos/ingreso/visualizar', ['pedido' => $pedido, 'detalle' => $detalle, 'detalles' => $detalles]);
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

        // $pedidos = DB::table('pedido as p')
        //     ->whereNull('p.estado_compras')
        //     //->where('p.pedido', 'like', '%' . $pedido . '%')
        //     ->join('proveedor as pro', 'pro.id', 'p.id_proveedor')
        //     ->join('users as u', 'u.id', 'p.id_usuariocrea')
        //     ->where('p.id_empresa', $id_empresa)
        //     ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial', 'pro.razonsocial');

        // if (!is_null($pedido)) {
        //     $pedidos = $pedidos->where('p.pedido', 'like', '%' . $pedido . '%');
        // }

        // if (!is_null($proveedor)) {
        //     $pedidos = $pedidos->where('pro.id', 'like', '%' . $proveedor . '%');
        // }

        $pedidos = ProductoController::buscadorPedidos($request);
        
        $pedidos         = $pedidos->OrderBy('p.created_at', 'desc')->paginate(10);
        $i               = 0;
        $cantidades      = array();
        $pedidosdetalles = array();
        foreach ($pedidos as $value) {

            $busqueda = DB::table('pedido as p')
                ->where('p.id', $value->id)
                ->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->groupBy('m.serie')
                ->get();
            //dd($busqueda);

            $busqueda2 = DB::table('pedido as p')
                ->where('p.id', $value->id)
                ->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->where('m.tipo', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->select(DB::raw('count(*) as cantidad_total, m.tipo'))
                ->get();
            //dd($busqueda2);

            $busqueda3 = Detalle_Pedido::where('id_pedido', $value->id)
                ->where('estado', 1)
                ->first();

            //dd($busqueda3);
            $cantidades[$i][0] = $busqueda->count();
            $cantidades[$i][1] = $busqueda2[0]->cantidad_total;
            if (isset($busqueda3->id)) {
                $pedidosdetalles[$i][0] = $busqueda3->id;
            } else {
                $pedidosdetalles[$i][0] = null;
            }
            $i = $i + 1;
        }
        // dd($pedidosdetalles);
        return view('insumos/producto/codigobarra', ['pedidos' => $pedidos, 'cantidades' => $cantidades, 'pedidosdetalles' => $pedidosdetalles, 'id_empresa' => $id_empresa]);
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
        $pedidos = DB::table('pedido as p')
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
        }

        if (!is_null($request->producto)) {
            $pedidos = $pedidos->join('movimiento as m', 'm.id_pedido', 'p.id')->where('m.id_producto', $request->producto)->groupBy('p.id');
        }
        return $pedidos;
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
            return "error";
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

    public function codigo_baja(Request $request)
    {

        $nombre = $request['serie'];
        //dd($nombre);
        //return $nombre;
        $data = null;

        $productos = DB::table('movimiento as m')
            ->where('m.serie', $nombre)
            ->join('pedido as p', 'p.id', 'm.id_pedido')
            ->where('m.estado', '1')
            ->join('producto as pro', 'pro.id', 'm.id_producto')
            ->groupBy('m.tipo')
            ->select(DB::raw('count(*) as cantidad_total, m.tipo'), 'm.serie', 'pro.nombre as nombre_producto', 'm.*', 'pro.codigo', 'pro.descripcion')
            ->where(function ($query) {
                $query->where('m.tipo', '2')
                    ->orWhere('m.tipo', '1');
            })->get();

        return view('insumos/producto/bajar_producto_tabla', ['productos' => $productos]);
    }

    public function imprimirbarras($id)
    {
        //return $id;
        $pedidos = DB::Select("SELECT m.*, p.nombre as nombreproducto, p.id as id_pedido
                                FROM movimiento m, producto p
                                WHERE m.id_pedido = '" . $id . "' AND
                                m.estado = '1' AND
                                m.cantidad > 0 AND
                                p.id = m.id_producto
                                GROUP BY m.serie;");

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
            ->join('movimiento as m', 'm.id_pedido', 'p.id')
            ->where('m.estado', '1')
            ->join('producto as pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->OrderBy('m.created_at')
            ->groupBy('m.serie')
            ->groupBy('m.tipo')
            ->select(DB::raw('count(*) as cantidad_total, m.tipo '), 'm.serie', 'pro.nombre as nombre_producto', 'b.nombre as nombre_bodega', 'm.*')
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
        $productos = DB::table('producto')->where('nombre', 'like', '%' . $nombre . '%')->first();
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
            'codigo'        => $request['codigo'],
            'nombre'        => $request['nombre'],
            'tipo_producto' => $request['tipo_producto'],
        ];
        $productos = $this->doSearchingQuery($constraints);
        $tipos     = Tipo::all();
        return view('insumos/producto/index', ['request' => $request, 'productos' => $productos, 'searchingVals' => $constraints, 'tipos' => $tipos]);
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
        //dd("aqui");

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
        ];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $this->validate($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        $arr_total = [];
        for ($i = 0; $i < count($request->input("precio")); $i++) {
            if ($request->input("precio")[$i] != "" && $request->input("nivel")[$i] != "") {
                $arr = [
                    'nivel'  => $request->input("nivel")[$i],
                    'precio' => $request->input("precio")[$i],
                ];
                //    print_r($arr);
                array_push($arr_total, $arr);
            }
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
            'tipo'               => $request['tipo'],
        ]);
        if (!is_null($request['cod_general'])) {
            $data2 = array(
                'id_usuariocrea'  => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'codigo_producto' => strtoupper($request['codigo']),
                'id_insumo'       => $p_id,
                'id_producto'     => $request['cod_general'],

            );

            Ct_productos_insumos::insert($data2);
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
        PrecioProducto::create($precio);*/
        // }

        return redirect()->intended('/producto');
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $producto = Producto::find($id);

        // Redirect to user list if updating user wasn't existed
        if ($producto == null) {
            return redirect()->intended('/producto');
        }
        $proveedor      = Proveedor::all();
        $tipos          = Tipo::where('estado', '1')->get();
        $marcas         = Marca::where('estado', '1')->get();
        $ct_productos   = Ct_productos::where('estado_tabla', '1')->get();
        $tipo_plantilla = Insumo_Plantilla_Tipo::get();
        // $precios = PrecioProducto::where('id_producto', $id)->get();
        return view('insumos/producto/edit', ['producto' => $producto, 'proveedores' => $proveedor, 'ct_productos' => $ct_productos, 'marcas' => $marcas, 'tipos' => $tipos, 'tipo_plantilla' => $tipo_plantilla]);
    }

    public function update(Request $request, $id)
    {
        //return $request->all();

        $producto   = Producto::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
           // 'codigo.unique'               => 'El Codigo ya se encuentra registrado.',
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
        ];

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

            ];
        } else {

            $input = [
                'codigo'             => strtoupper($request['codigo']),
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
            ];
        }

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
}
