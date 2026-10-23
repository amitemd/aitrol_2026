<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
//use Session;
use Sis_medico\Bodega;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Producto;
use Sis_medico\Marca;
use Sis_medico\InvDocumentosBodegas;

class InventarioSerieController extends Controller
{
    private $controlador = 'inventarioSerie';
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7, 10)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        $id_marca = 0;
        if (isset($request['id_marca'])) {
            $id_marca = $request['id_marca'];
        }
        $serie = "";
        if (isset($request['serie'])) {
            $serie = $request['serie'];
        }
        // dd($fecha_desde);
        $productos = Producto::orderby('id', 'desc')->where('estado', '1')->get();
        $bodegas   = Bodega::whereNull('deleted_at')->where('id_empresa', Session::get('id_empresa'))->where('estado','1')->get();
        $marca     = Marca::where('estado', '1')->get();
        return view('insumos/inventario/index_serie', [
            'productos' => $productos, 'id_marca' => $id_marca, 'marca' => $marca, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto,
            'bodegas'   => $bodegas, 'serie' => $serie, 'id_bodega' => $id_bodega, "data" => $request->all()
        ]);
    }

    public function show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        /*if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }*/
        $serie      = 0;
        if (isset($request['serie'])) {
            $serie = $request['serie'];
        }
        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        $descripcion = "";
        if (isset($request['descripcion'])) {
            $descripcion = $request['descripcion'];
        }
        $id_marca = 0;
        if (isset($request['id_marca'])) {
            $id_marca = $request['id_marca'];
        }
        $inventario = array();
        $id_empresa = Session::get('id_empresa');
        // if (isset($request['id_producto']) and $request['id_producto'] != "") {
        $inventario = InvInventarioSerie::where('inv_inventario_serie.estado', '!=', '0')
            ->where('inv_inventario_serie.id_empresa', $id_empresa)
            ->where('existencia', '>', 0);

        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $inventario = $inventario->where('id_producto', $request['id_producto']);
        }
        if (isset($request['descripcion']) and $request['descripcion'] != "") {
            $inventario = $inventario->join('producto', 'inv_inventario_serie.id_producto', '=', 'producto.id');
            $inventario = $inventario->where('producto.nombre', 'like', '%' . $request['descripcion'] . '%');
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
            $inventario = $inventario->where('inv_inventario_serie.id_bodega', $request['id_bodega']);
        }
        if (Auth::user()->id == '0922729587') {
            //dd($inventario->get());
        }
        if (isset($request['id_marca']) and $request['id_marca'] != "") {
            $inventario = $inventario->join('producto', 'inv_inventario_serie.id_producto', '=', 'producto.id');
            $inventario = $inventario->join('marca', 'producto.id_marca', '=', 'marca.id');
            $inventario = $inventario->where('marca.id',  $request['id_marca']);
        }
        if (isset($request['serie']) and $request['serie'] != "") {
            $inventario = $inventario->where('serie', '=', $request['serie']);
        }
        $inventario = $inventario->where('estado',1)->orderBy('inv_inventario_serie.id', 'ASC')
            ->get();
        // }
        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos  = Producto::orderby('id', 'desc')->where('estado', '1')->get();
        //$doc_bodega =InvDocumentosBodegas::all();
        $bodegas    = Bodega::where('estado', 1)->where('id_empresa', Session::get('id_empresa'))->get();
        $marca    = Marca::where('estado', 1)->get();
        /*$data['controlador'] = $this->controlador;
        $data['tabla'] = null;
        config(['data' => $data]);*/
        return view('insumos/inventario/show_serie', [
            'productos' => $productos, 'serie' => $serie, 'empresa' => $empresa, 'marca' => $marca, 'id_marca' => $id_marca, /*'fecha_desde' => $fecha_desde, */ 'getAnterior' => '[]', 'inventario' => $inventario, 'id_producto' => $id_producto,
            'detalles'  => "", 'bodegas'         => $bodegas, 'id_bodega'   => $id_bodega, 'descripcion'   => $descripcion,
        ]);
    }

    public function busqueda(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

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
        $serie      = 0;
        if (isset($request['serie'])) {
            $serie = $request['serie'];
        }
        $id_bodega  = 0;
        $inventario = array();

        if (isset($request['serie']) and $request['serie'] != "") {
            $inventario = InvDetMovimientos::where('serie', $request['serie'])
                ->where('estado', '!=', '0')
                ->orderBy('id', 'ASC')
                ->get();
        }
        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        return view('insumos/inventario/busqueda_show', [
            'empresa' => $empresa, 'inventario' => $inventario, 'serie' => $serie,
        ]);
    }
}
