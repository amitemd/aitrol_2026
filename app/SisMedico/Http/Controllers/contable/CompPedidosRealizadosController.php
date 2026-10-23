<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Bodega;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\Log_movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Proveedor;


class CompPedidosRealizadosController extends Controller
{

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
        //return "hola";
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $pedido     = $request['numerodepedido'];
        $proveedor  = $request['proveedor'];
        $id_empresa = $request->session()->get('id_empresa');

        $constraints = ['numerodepedido' => $request['numerodepedido'], 'id_proveedor' => $request['proveedor']];

        $pedidos = DB::table('pedido as p')
            ->where('p.pedido', 'like', '%' . $pedido . '%')
            ->join('proveedor as pro', 'pro.id', 'p.id_proveedor')
            ->join('users as u', 'u.id', 'p.id_usuariocrea')
            ->where('p.id_empresa', $id_empresa)
            ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial')
            ->OrderBy('p.created_at', 'desc')->paginate(10);
        //dd($pedidos);

        if (!is_null($proveedor)) {
            if (!is_numeric($proveedor)) {
                $proveedf = DB::table('proveedor')->where('nombrecomercial', 'like', '%' . $proveedor . '%')->first();
                if (!is_null($proveedf)) {
                    $pedidos = DB::table('pedido as p')
                        ->join('proveedor as pro', 'pro.id', 'p.id_proveedor')
                        ->where('pro.nombrecomercial', 'like', '%' . $proveedf->nombrecomercial . '%')
                        ->join('users as u', 'u.id', 'p.id_usuariocrea')
                        ->where('p.id_empresa', $id_empresa)
                        ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial')
                        ->OrderBy('p.created_at', 'desc')->paginate(10);
                } else {
                }
                //dd($proveedf);

            } else {
                $pedidos = DB::table('pedido as p')
                    ->where('p.id_proveedor', 'like', '%' . $proveedor . '%')
                    ->join('proveedor as pro', 'pro.id', 'p.id_proveedor')
                    ->join('users as u', 'u.id', 'p.id_usuariocrea')
                    ->where('p.id_empresa', $id_empresa)
                    ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial')
                    ->OrderBy('p.created_at', 'desc')->paginate(10);
            }
        }

        $i          = 0;
        $cantidades = array();
        foreach ($pedidos as $value) {

            $busqueda = DB::table('pedido as p')
                ->where('p.id', $value->id)
                ->where('p.id_empresa', $id_empresa)
                ->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->groupBy('m.serie')
                ->get();
            //dd($busqueda);

            $busqueda2 = DB::table('pedido as p')
                ->where('p.id', $value->id)
                ->where('p.id_empresa', $id_empresa)
                ->join('movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->where('m.tipo', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->select(DB::raw('count(*) as cantidad_total, m.tipo'))
                ->get();

            //dd($busqueda);
            $cantidades[$i][0] = $busqueda->count();
            $cantidades[$i][1] = $busqueda2[0]->cantidad_total;

            $i = $i + 1;
        }
        //dd($cantidades);
        return view('contable/comp_pedido_realizado/index', ['pedidos' => $pedidos, 'cantidades' => $cantidades, 'searchingVals' => $constraints]);
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

        return view('contable/comp_pedido_realizado/tabla_movimiento', ['productos' => $pedidos]);
    }

    public function modalcompras(Request $request)
    {
        $documentoBodega = InvDocumentosBodegas::where('abreviatura_documento', 'ICG')->first();
        if ($documentoBodega == '') {
            return ['error' => 'No existe el documento INGRESO FACTURA CONGLOMERADA, por favor contactese con sistema'];
        }
        $id_empresa = $request->session()->get('id_empresa');
        $pedidos = DB::table('pedido')
            ->join('proveedor', 'proveedor.id', '=', 'pedido.id_proveedor')
            ->join('users', 'users.id', '=', 'pedido.id_usuariocrea')
            ->join('inv_documentos_bodegas', 'inv_documentos_bodegas.id', '=', 'pedido.tipo')
           //->where('inv_documentos_bodegas.tipo', 'F')
            ->whereIn('pedido.tipo', [3, $documentoBodega->id])
            ->where('pedido.id_empresa', $id_empresa)
            ->whereNull('pedido.deleted_at')
            ->where('pedido.estado_contable', 0)
            //->where('pedido.tipo', '!=', 3)
            ->select('pedido.*', 'users.nombre1', 'users.apellido1', 'proveedor.nombrecomercial');
        // ->get();
        $ordenes = DB::table('pedido')
            ->join('proveedor', 'proveedor.id', '=', 'pedido.id_proveedor')
            ->join('users', 'users.id', '=', 'pedido.id_usuariocrea')
            ->where('pedido.id_empresa', $id_empresa)
            ->where('pedido.tipo', '=', 3)
            ->where('pedido.estado_contable', 0)
            ->whereNull('pedido.deleted_at')
            ->union($pedidos)
            ->select('pedido.*', 'users.nombre1', 'users.apellido1', 'proveedor.nombrecomercial')
            ->get();
        return view('contable/compra/modalcompra', ['pedidos' => $ordenes]);
    }

    // public function index(Request $request){

    //     if($this->rol()){
    //         return response()->view('errors.404');
    //     }

    //     return view('contable/comp_pedido_realizado/index');
    // }

    public function crear_bodega_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::find($id_empresa);
        return view('contable/comp_pedido_realizado/crear_pedido', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa]);

        // return view('contable/comp_pedido_realizado/crear_pedido');
    }

    public function guardar_pedido_compra(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'tipo'            => '3',
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'id_empresa'      => $request['id_empresa'],
            'observaciones'   => $request['observaciones'],
            'consecion'       => $request['consecion'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'descuento'       => $request['descuentx'],
            'iva'             => $request['iva'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'estado_compras'  => 1,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->validatePedido($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required' => 'Ingrese el numero de Lote.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }

        $id_pedido = Pedido::insertGetId($input);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $cantidad          = $request['cantidad' . $i];
                $consecion_detalle = 0;
                if ($request['consecion_det' . $i] == 1) {
                    $consecion_detalle = 1;
                }
                for ($x = 0; $x < $cantidad; $x++) {
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => '1',
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'usos'              => $request['usos' . $i],
                        'descuentop'        => $request['descuento' . $i],
                        'descuento'         => $request['descuentof' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => $consecion_detalle,

                    ];
                    $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                    $id_producto       = $request['id' . $i];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Log_movimiento::create([
                        'id_producto'     => $request['id' . $i],
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Ingreso del producto",
                        'tipo'            => '1',
                        'ip_creacion'     => $ip_cliente,
                        'cantidad'        => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Producto::where('id', $id_producto)->update($input3);
                }
            }
        }

        return redirect()->route('codigo.barra');
    }

    private function ValidatePedido(Request $request)
    {

        $prules = [
            'pedido'       => 'required|unique:pedido',
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);
    }
}
