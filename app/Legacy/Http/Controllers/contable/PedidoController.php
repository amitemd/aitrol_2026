<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Sucursal_Banco;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_cuentas;
use Sis_medico\Proveedor;

class PedidoController extends Controller
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

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd('ENTRA');

        $caj_ban = Ct_caja_banco::paginate(10);

        return view('contable.caja_banco.index', ['caj_ban' => $caj_ban]); //*Aqui va la ruta de la carpeta*
    }
    public function crear()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $divisas  = Ct_Divisas::where('estado', 1)->get();

        $id_empresa = $request->session()->get('id_empresa');
        //1.01.01
        $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PEDIDOS_EFECTIVO');
        $plan   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();

        //$plan     = Plan_Cuentas::where('id_padre', '1.01.01')->get();
        $sucursal = Ct_Sucursal_Banco::all();

        return view('contable.caja_banco.create', ['divisas' => $divisas, 'plan' => $plan, 'sucursal' => $sucursal]); //*Aqui va la ruta de la carpeta*
    }

    public function guardar_caja_banco(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Ct_caja_banco::create([
            'codigo'           => $request['codigo'],
            'nombre'           => $request['nombre'],
            'numero_de_cuenta' => $request['numero'],
            'tipo'             => $request['tipos'],
            'clase'            => $request['clases'],
            'grupo'            => $request['grupos'],
            'cta_mayor'        => $request['ctamayor'],
            'sucursal'         => $request['sucursales'],
            'divisa'           => $request['divisas'],
            'www'              => $request['web'],
            'formas_de_pago'   => $request['formasdepago'],
            'comentarios'      => $request['comen'],
            'estado'           => $request['estados'],
            'id_usuariomod'    => $idusuario,
            'id_usuariocrea'   => $idusuario,
            'ip_modificacion'  => $ip_cliente,
            'ip_creacion'      => $ip_cliente,

        ]);

        return redirect()->intended('contable/caja_banco');
        //return Redirect::to('formulario.registro')->withInput();

    }

    public function contable_index()
    {
        $contable = Ct_caja_banco::all();
        return view('contable.caja_banco.index', ['contable' => $contable]);
    }

    public function query_cuentas(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];

        if (!is_null($codigo)) {
            $id_padre = Plan_Cuentas::where('id_padre', $codigo)->get();

            return $id_padre;
        }
        return 'no';
    }
    public function edit($id)
    {
        $caja     = Ct_caja_banco::find($id);
        $divisas  = Ct_Divisas::where('estado', 1)->get();
        
        $id_empresa = $request->session()->get('id_empresa');
        //$plan     = Plan_Cuentas::where('id_padre', '1.01.01')->get();
        $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PEDIDOS_EFECTIVO');
        $plan   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();
        
        $sucursal = Ct_Sucursal_Banco::all();

        //dd($plan);

        return view('contable.caja_banco.edit', ['caja' => $caja, 'divisas' => $divisas, 'plan' => $plan, 'sucursal' => $sucursal]);
    }
    public function update(Request $request, $id)
    {
        //return $request->all();

        $caja = Ct_caja_banco::findOrFail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $input = [
            'codigo'           => $request['codigo'],
            'nombre'           => $request['nombre'],
            'numero_de_cuenta' => $request['numero'],
            'tipo'             => $request['tipos'],
            'clase'            => $request['clases'],
            'grupo'            => $request['grupos'],
            'cta_mayor'        => $request['ctamayor'],
            'sucursal'         => $request['sucursales'],
            'divisa'           => $request['divisas'],
            'www'              => $request['web'],
            'formas_de_pago'   => $request['formasdepago'],
            'comentarios'      => $request['comen'],
            'estado'           => $request['estados'],
            'id_usuariomod'    => $idusuario,
            'id_usuariocrea'   => $idusuario,
            'ip_modificacion'  => $ip_cliente,
            'ip_creacion'      => $ip_cliente,

        ];

        $caja->update($input);

        return redirect()->intended('contable/caja_banco');
    }





    /*public function pedidos_insumos(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        if(isset($request['id_proveedor'])){
            $resultados = DB::table('pedido as p')
            ->join('inv_cab_movimientos as icm', 'icm.id_pedido', '=', 'p.id')
            ->join('inv_det_movimientos as idm', 'idm.id_inv_cab_movimientos', '=', 'icm.id')
            ->join('producto as p2', 'p2.id', '=', 'idm.id_producto')
            ->join('proveedor as p3', 'p3.id', '=', 'p.id_proveedor')
            ->select(
                'p.id',
                'icm.numero_documento',
                'p3.nombrecomercial',
                'icm.fecha',
                'p2.nombre',
                'idm.valor_unitario',
                'idm.cantidad',
                'idm.subtotal',
                'idm.iva',
                'idm.descuento',
                'idm.total',
                'p.factura'
            )
            ->when($request->id_proveedor, function ($query) use ($request) {
                $query->whereIn('p.id_proveedor', $request->id_proveedor);
            })
            ->whereBetween('p.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->get();

        }else{
            $resultados = array();
        }  
        
        return view('contable.pedidos.pedidos', ['resultados' => $resultados, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }*/
    public function pedidos_insumos(Request $request)
    {
        // 🗓 Si no se envían fechas, por defecto: desde hace 1 mes hasta hoy
        $fecha_hasta = $request->input('fecha_hasta') ?? date('Y-m-d');
        $fecha_desde = $request->input('fecha_desde') ?? date('Y-m-d', strtotime('-1 month', strtotime($fecha_hasta)));

        // 🔍 Construimos la consulta base
        $query = DB::table('pedido as p')
            ->join('inv_cab_movimientos as icm', 'icm.id_pedido', '=', 'p.id')
            ->join('inv_det_movimientos as idm', 'idm.id_inv_cab_movimientos', '=', 'icm.id')
            ->join('producto as p2', 'p2.id', '=', 'idm.id_producto')
            ->join('proveedor as p3', 'p3.id', '=', 'p.id_proveedor')
            ->select(
                'p.id',
                'icm.numero_documento',
                'p3.nombrecomercial',
                'icm.fecha',
                'p2.nombre',
                'idm.valor_unitario',
                'idm.cantidad',
                'idm.subtotal',
                'idm.iva',
                'idm.descuento',
                'idm.total',
                'p.factura'
            )
            ->whereBetween('p.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        // 📦 Si se seleccionan proveedores, filtramos por ellos
        if (!empty($request->id_proveedor)) {
            $query->whereIn('p.id_proveedor', $request->id_proveedor);
        }

        // 🔄 Obtenemos los resultados
        $resultados = $query->get();

        // 📤 Devolvemos la vista con los valores calculados
        return view('contable.pedidos.pedidos', [
            'resultados'   => $resultados,
            'fecha_desde'  => $fecha_desde,
            'fecha_hasta'  => $fecha_hasta
        ]);
    }

    public function buscar_proveedor(Request $request)
    {
        $proveedor = [];
        if ($request['term'] != null) {
            $proveedor     = Proveedor::where('estado', 1)
                ->where('nombrecomercial', 'like', '%' . $request['term'] . '%')
                ->select('nombrecomercial as text', 'id as id')
                ->get();
        }

        return response()->json($proveedor);
    }
}
