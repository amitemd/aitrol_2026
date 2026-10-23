<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Empresa;
use Sis_medico\Sala;
use Sis_medico\Producto;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\hc_receta;
use Sis_medico\Movimiento;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Producto_Medicina;
use Sis_medico\Hospitalizacion;
use Sis_medico\Farmacia;
use Sis_medico\Bodega;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\InvInventario;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Seguro;
use Sis_medico\User;

class FarmaciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    public function index()
    {
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $bodegas = Bodega::where('estado', '1')->get();
        $medicamento = InvInventario::where('inv_inventario.estado', '1')
            ->whereBetween('inv_inventario.created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->join('producto as p', 'p.id', 'inv_inventario.id_producto')
            ->select('inv_inventario.*', 'p.nombre')
            ->get();
        return view('hospital/farmacia/master_farmacia', ['bodegas' => $bodegas, 'medicamento' => $medicamento, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function buscar_medicina(Request $request)
    {
        //dd($request->all());
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $bodega = $request['bodega'];
        $producto = $request['producto'];
        $medicamento = InvInventario::where('inv_inventario.estado', '1')
            ->whereBetween('inv_inventario.created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->join('producto as p', 'p.id', 'inv_inventario.id_producto')
            ->join('bodega as b', 'b.id', 'inv_inventario.id_bodega')
            ->select('inv_inventario.*', 'p.nombre', 'b.nombre');
        if (count($medicamento) > 0) {
            if ($bodega != null) {

                $medicamento = $medicamento->where('b.id', $bodega);
                //dd($medicamento);
            }
            if ($producto != null) {
                $nombresp = explode(" ", $producto);
                $cantidad = count($nombresp);
    
                if ($cantidad == '2' || $cantidad == '3') {
                    $medicamento = $medicamento->where(function ($jq1) use ($nombresp) {
                        $jq1->orwhereraw('CONCAT(p.nombre) LIKE ?', ['%' . $nombresp . '%'])
                            ->orwhereraw('CONCAT(p.nombre) LIKE ?', ['%' . $nombresp . '%']);
                    });
    
                } else {
    
                    $medicamento = $medicamento->whereraw('CONCAT(p.nombre) LIKE ?', ['%' . $producto . '%']);
                }
            }
           
            if (count($medicamento) > 0) {
                $medicamento = $medicamento->get();
            }
        }

        $bodegas = Bodega::where('estado', '1')->get();

        return view('hospital/farmacia/master_farmacia', ['bodegas' => $bodegas, 'medicamento' => $medicamento, 'bodega' => $bodega, 'producto' => $producto, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function invoice(Request $request){
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $validate = $request['validate'];

        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();

        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = "RUC_HOSPITAL";
        $empresa    = Empresa::where('prioridad', '1')->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
           
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //dd($request->all());
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::all();
        $iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        //dd($empresa);
        return view('hospital/farmacia/invoice',['divisas' => $divisas,'t_factura'=>'1', 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas,'tipo_tarjeta'=>$tipo_tarjeta]);
    }
}
