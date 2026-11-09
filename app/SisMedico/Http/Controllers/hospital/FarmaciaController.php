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
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\InvInventario;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Seguro;
use Sis_medico\Utilidades;
use Sis_medico\User;
use Sis_medico\Services\VademecumCatalogService;

class FarmaciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
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
        $medicamento = $this->pharmacyCatalog($fecha_desde, $fecha_hasta);
        return view('hospital/farmacia/master_farmacia', ['bodegas' => $bodegas, 'medicamento' => $medicamento, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function buscar_medicina(Request $request)
    {
        //dd($request->all());
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $bodega = $request['bodega'];
        $producto = $request['producto'];
        $producto = trim((string) $producto);
        $medicamento = $this->pharmacyCatalog($fecha_desde, $fecha_hasta, $bodega, $producto);

        $bodegas = Bodega::where('estado', '1')->get();

        return view('hospital/farmacia/master_farmacia', ['bodegas' => $bodegas, 'medicamento' => $medicamento, 'bodega' => $bodega, 'producto' => $producto, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function invoice(Request $request){
        $iva = Utilidades::porcentajeIva();
        if (is_null($iva)) {
            return redirect()
                ->route('hospital.master_farmacia')
                ->with('error', 'No se puede crear la factura porque falta configurar el porcentaje del IVA.');
        }

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

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = $id_empresa
            ? Empresa::where('id', $id_empresa)->where('estado', 1)->first()
            : null;

        if (is_null($empresa)) {
            $empresa = Empresa::where('prioridad', '1')->where('estado', 1)->first();
            if (!is_null($empresa)) {
                $id_empresa = $empresa->id;
                $request->session()->put('id_empresa', $id_empresa);
            }
        }

        if (is_null($empresa)) {
            return redirect()
                ->route('hospital.master_farmacia')
                ->with('error', 'No se puede crear la factura porque no existe una empresa emisora activa.');
        }

        app(VademecumCatalogService::class)->syncOperationalCatalogs(Auth::user()->id, $id_empresa);

        $bodega = Bodega::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->where('ct_s.id_empresa', $id_empresa)
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //dd($request->all());
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)
            ->where('estado_tabla', 1)
            ->where('grupo', '2')
            ->orderBy('nombre')
            ->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        //dd($empresa);
        return view('hospital/farmacia/invoice',['divisas' => $divisas,'t_factura'=>'1', 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas,'tipo_tarjeta'=>$tipo_tarjeta]);
    }

    /**
     * El vademecum es el catalogo; inv_inventario solo agrega existencias.
     * De esta forma un medicamento nuevo puede buscarse aun antes de su
     * primer ingreso a bodega y nunca se presenta una existencia ficticia.
     */
    private function pharmacyCatalog($fechaDesde, $fechaHasta, $bodega = null, $term = null)
    {
        $query = DB::table('producto as p')
            ->leftJoin('inv_inventario as i', function ($join) use ($fechaDesde, $fechaHasta) {
                $join->on('i.id_producto', '=', 'p.id')
                    ->where('i.estado', '=', '1')
                    ->whereNull('i.deleted_at')
                    ->where('i.created_at', '>=', $fechaDesde . ' 00:00:00')
                    ->where('i.created_at', '<=', $fechaHasta . ' 23:59:59');
            })
            ->leftJoin('bodega as b', 'b.id', '=', 'i.id_bodega')
            ->where('p.estado', 1)
            ->where('p.farmacia', 1);

        if (!is_null($bodega) && $bodega !== '') {
            $query->where('i.id_bodega', $bodega);
        }

        if (!is_null($term) && $term !== '') {
            $query->where(function ($filter) use ($term) {
                $filter->where('p.nombre', 'like', '%' . $term . '%')
                    ->orWhere('p.codigo', 'like', '%' . $term . '%')
                    ->orWhere('p.descripcion', 'like', '%' . $term . '%');
            });
        }

        return $query->select(
            'p.id as id_producto',
            'p.codigo',
            'p.nombre as producto_nombre',
            'p.descripcion as producto_descripcion',
            'p.registro_sanitario',
            'i.id as id_inventario',
            'i.id_bodega',
            DB::raw('COALESCE(i.existencia, 0) as existencia'),
            DB::raw("COALESCE(i.estado, '1') as estado"),
            'i.created_at',
            'b.nombre as bodega_nombre'
        )->orderBy('p.nombre')->get();
    }
}
