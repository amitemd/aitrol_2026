<?php

namespace Sis_medico\Http\Controllers\Insumos;

use App\Modules\InventoryOperations\Application\Data\InventoryStockFilters;
use App\Modules\InventoryOperations\Application\Queries\SearchInventoryStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Session;
use Illuminate\Support\Facades\Session;
use Sis_medico\Bodega;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Producto;
use Sis_medico\Tipo;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22, 7, 10]) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (! is_null($request['fecha_desde']) && ! is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        $tipo_pro = 0;
        if (isset($request['tipo_pro'])) {
            $tipo_pro = $request['tipo_pro'];
        }
        // dd($fecha_desde);
        $productos = Producto::orderby('id', 'desc')->where('estado', '1')->get();
        $bodegas = Bodega::where('estado', 1)->where('id_empresa', Session::get('id_empresa'))->get();
        $tipo_prod = Tipo::where('estado', 1)->get();

        return view('insumos/inventario/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto,
            'bodegas' => $bodegas, 'id_bodega' => $id_bodega, 'tipo_pro' => $tipo_pro, 'tipo_prod' => $tipo_prod,
        ]);
    }

    public function show(Request $request, SearchInventoryStock $searchInventoryStock)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        if (! is_null($request['fecha_desde']) && ! is_null($request['fecha_hasta'])) {
            $rfecha_desde = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp = Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde = date('Y-m-d', $timestamp);

            $rfecha_hasta = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp = Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        $tipo_pro = 0;
        if (isset($request['tipo_pro'])) {
            $tipo_pro = $request['tipo_pro'];
        }
        $inventario = $searchInventoryStock->handle(
            (string) $id_empresa,
            new InventoryStockFilters(
                productId: $request->filled('id_producto') ? $request->integer('id_producto') : null,
                warehouseId: $request->filled('id_bodega') ? $request->integer('id_bodega') : null,
                productTypeId: $request->filled('tipo_pro') ? $request->integer('tipo_pro') : null,
                productName: $request->filled('producto') ? $request->string('producto')->trim()->toString() : null,
            ),
        );
        $busq = [
            'producto' => $request->producto,
        ];

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos = Producto::orderby('id', 'desc')->where('estado', '1')->get();
        $bodegas = Bodega::where('estado', 1)->where('id_empresa', Session::get('id_empresa'))->get();
        $tipo_prod = Tipo::where('estado', 1)->get();

        return view('insumos/inventario/show', [
            'productos' => $productos, 'tipo_prod' => $tipo_prod, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => '[]', 'fecha_hasta' => $fecha_hasta, 'inventario' => $inventario, 'id_producto' => $id_producto,
            'detalles' => '', 'bodegas' => $bodegas, 'id_bodega' => $id_bodega, 'busq' => $busq, 'tipo_pro' => $tipo_pro,
        ]);

    }

    public function busqueda(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $serie = 0;
        if (isset($serie['serie'])) {
            $serie = $request['serie'];
        }

        return view('insumos/inventario/busqueda', [
            'empresa' => $empresa, 'serie' => $serie,

        ]);
    }

    public function busquedaserie(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        $serie = 0;
        if (isset($request['serie'])) {
            $serie = $request['serie'];
        }
        $descripcion = '';
        if (isset($request['descripcion'])) {
            $descripcion = $request['descripcion'];
        }
        $pedido = '';
        if (isset($request['pedido'])) {
            $pedido = $request['pedido'];
        }
        $id_bodega = 0;
        $inventario = [];

        // if (isset($request['serie']) and $request['serie'] != "") {
        $inventario = InvDetMovimientos::where('inv_det_movimientos.estado', '!=', '0')->leftJoin('inv_cab_movimientos as icm', 'icm.id', 'inv_det_movimientos.id_inv_cab_movimientos')->where('icm.id_empresa', $id_empresa);
        if (isset($request['serie']) and $request['serie'] != '') {
            $inventario = $inventario->where('serie', '=', $request['serie']);
        }
        if ($request['descripcion'] != '') {
            $inventario = $inventario->join('producto as p', 'p.id', 'inv_det_movimientos.id_producto')
                ->where('p.nombre', 'like', '%'.$request['descripcion'].'%');
        }
        if ($request['pedido'] != '') {
            $inventario = $inventario->join('inv_cab_movimientos as c', 'c.id', 'inv_det_movimientos.id_inv_cab_movimientos')
                ->leftJoin('pedido as p', 'p.id', 'c.id_pedido')
                ->where('p.pedido', 'like', '%'.$request['pedido'].'%');
        }
        $inventario = $inventario->select('inv_det_movimientos.*')
            ->orderBy('id', 'ASC')
            ->get();
        // dd($inventario);
        // }
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        return view('insumos/inventario/busqueda_show', [
            'empresa' => $empresa, 'inventario' => $inventario, 'serie' => $serie, 'descripcion' => $descripcion, 'pedido' => $pedido,
        ]);

    }
}
