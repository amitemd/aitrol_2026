<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Bodega;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvKardex;
use Sis_medico\Producto;

class KardexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7)) == false) {
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
        // dd($fecha_desde);
        $productos = Producto::orderby('id', 'desc')->get();
        $bodegas   = Bodega::where('estado', 1)->get();
        return view('insumos/kardex/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto,
            'bodegas'   => $bodegas, 'id_bodega' => $id_bodega,
        ]);
    }

    public function show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
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
        }
        $codigo_producto = 0;
        if (isset($request['codigo_producto'])) {
            $codigo_producto = $request['codigo_producto'];
        }
        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }

        #   KARDEX ANTERIOR    suma #
        $getAnterior = InvKardex::where('inv_kardex.estado', '1');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $getAnterior = $getAnterior->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $getAnterior = $getAnterior->where('inv_kardex.id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {

            $getAnterior = $getAnterior->where('inv_kardex.id_bodega', $request['id_bodega']);
        }
        $getAnterior = $getAnterior->where('inv_kardex.fecha', '<', $fecha_desde)
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.tipo', 'I')
            ->where('inv_kardex.estado', '1')
            ->leftJoin('inv_det_movimientos', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
            ->orderBy('inv_kardex.fecha', 'ASC')
            ->orderBy('inv_kardex.id', 'ASC')
            ->orderBy('inv_kardex.tipo', 'I')
            ->select(DB::raw('SUM(inv_kardex.cantidad) as cantidad'), DB::raw('inv_kardex.valor_unitario'), DB::raw('SUM(inv_kardex.total) as total'), 'fecha', DB::raw('SUM(inv_det_movimientos.iva) as iva'))->first();
        #   KARDEX ANTERIOR    resta #
        $getAnterior_2 = InvKardex::where('inv_kardex.estado', '1');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $getAnterior_2 = $getAnterior_2->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $getAnterior_2 = $getAnterior_2->where('inv_kardex.id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {

            $getAnterior_2 = $getAnterior_2->where('inv_kardex.id_bodega', $request['id_bodega']);
        }
        $getAnterior_2 = $getAnterior_2->where('inv_kardex.fecha', '<', $fecha_desde)
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.tipo', '<>', 'I')
            ->leftJoin('inv_det_movimientos', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
            ->orderBy('inv_kardex.fecha', 'ASC')
            ->orderBy('inv_kardex.id', 'ASC')
            ->select(DB::raw('SUM(inv_kardex.cantidad) as cantidad'), DB::raw('inv_kardex.valor_unitario'), DB::raw('SUM(inv_kardex.total) as total'), 'fecha', DB::raw('SUM(inv_det_movimientos.iva) as iva'))->first();
        //dd($getAnterior);

        $kardex = array();
        $kardex = InvKardex::where('estado', '!=', '2')
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.estado', '1')
            ->where('fecha', '>=', $fecha_desde . " 00:00:00")
            ->where('fecha', '<=', $fecha_hasta . " 23:59:59");
        //->where('tipo', '!=', 'T');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $kardex = $kardex->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $kardex = $kardex->where('id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
            $kardex = $kardex->where('id_bodega', $request['id_bodega']);
        }
        $kardex = $kardex->orderBy('fecha', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        //dd($kardex);

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos  = Producto::orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        return view('insumos/kardex/table', ['productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => $getAnterior, 'fecha_hasta' => $fecha_hasta, 'kardex' => $kardex, 'id_producto' => $id_producto, 'detalles' => "", 'bodegas' => $bodegas, 'id_bodega' => $id_bodega, 'getAnterior_2' => $getAnterior_2]);

    }

}
