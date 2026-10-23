<?php

namespace Sis_medico\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Svg\Tag\Rect;
use Session;
use Sis_medico\Bodega;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvCosto;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Producto;
use Sis_medico\InvKardex;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Proveedor;
use Sis_medico\Empresa;
use Sis_medico\InvContableCab;
use Sis_medico\InvTipo_Ing_Egr;
use Sis_medico\invTipoMovimiento;

use Sis_medico\Ct_Cierre_Cabecera;

class InvIngresoEgresoVarioController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 6, 7, 20, 10)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        $mes = date('m');
        $fecha_desde = "";
        $fecha_hasta = "";
        $busq = array();

        if (isset($request->fecha_desde) || !is_null($request->fecha_desde)) {
            $fecha_desde = date("Y-m-d", strtotime($request->fecha_desde));
        }

        if (isset($request->fecha_hasta) || !is_null($request->fecha_hasta)) {
            $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta));
        }

        $busq = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ];

        $movimientos      = InvCabMovimientos::ingresosEgresosVarios($fecha_desde, $fecha_hasta);

        // return view('insumos/transito/index_transito',compact('transito','traslados', 'busq'));
        return view('inventario/ing_egr_vario/index', compact('movimientos', 'busq'));
    }

    public function crear()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        $bodegas     = Bodega::where('estado', '1')->whereNull('deleted_at')->where('id_empresa', $id_empresa)->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::all();
        $bod_princ   = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        $documentos  = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->where('id_inv_tipo_movimiento', 1)->whereIn('id', [1, 2, 3, 4])->get();
        $tipos_ing_egr = InvTipo_Ing_Egr::where('estado', '1')->get();
        //dd($tipos_ing_egr);
        return view('inventario/ing_egr_vario/crear', [
            'bodegas' => $bodegas, 'proveedores' => $proveedores,
            'empresa' => $empresa, 'doc_bodega'  => $documentos, 'bodega_principal' => $bod_princ, 'tipos_ing_egr' => $tipos_ing_egr
        ]);
    }

    public function guardar(Request $request)
    { 
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        $id_empresa2 = $request->session()->get('id_empresa');
        //dd(Empresa::all(), $id_empresa,$id_empresa2);
        date_default_timezone_set('America/Guayaquil');
        $iva      = 0;
        $variable = $request['contador'];
        $bodega   = Bodega::find($request['bodega_recibe']);
        if (isset($bodega->id)) {
            $request['id_empresa'] = $bodega->id_empresa;
        }
        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i              => 'required',
                    'fecha_vencimiento' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required'              => 'Ingrese el numero de Lote.',
                    'fecha_vencimiento' . $i . '.required' => 'Ingrese la fecha de vencimiento.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }
        if ($request['id_proveedor'] == "0") {
            $request['id_proveedor'] = '9999999999999';
        }
        $tipo = $request['tipo'];
        if ($tipo == 'I') {
            $leyenda = 'INV';
        }
        if ($tipo == 'E') {
            $leyenda = 'EGV';
        }
        if ($tipo == 'R') {
            $leyenda = 'IVR';
        }
        if ($tipo == 'C') {
            $leyenda = 'EVC';
        }
        if ($tipo == 'N') {
            $leyenda = 'ENR';
        }
        if ($tipo == 'G'){
            $leyenda = 'IER';
        }
        if($tipo == 'U'){
            $leyenda = 'EER';
        }
        if($tipo == 'P'){
            $leyenda = 'EPP';
        }
        if($tipo == 'M'){
            $leyenda = 'EMC';
        }
        if($tipo == 'B'){
            $leyenda = 'EPR';
        }
        if($tipo == 'S'){
            $leyenda = 'EMS';
        }
        if($tipo == 'F'){
            $leyenda = 'EGF';
        }
        if($tipo == 'V'){
            $leyenda = 'EPI';
        }
        if($tipo == 'O'){
            $leyenda = 'ECC';
        }
        $leyenda .= date('YmdHis');
        // dd($tipo);
        $tipo_movimiento = InvTipoMovimiento::where('tipo', $tipo)->first();
       //dd($tipo_movimiento);

        $input = [
            'id_proveedor'    => $request['id_proveedor'],
            //'tipo'            => $request['tipo_'], 
            'pedido'          => $leyenda,
            'factura'         => $leyenda,
            'fecha'           => date('Y-m-d'),
            'vencimiento'     => date('Y-m-d'),
            'id_bodega'       => $request['bodega_recibe'],
            'observaciones'   => $request['observaciones'],
            'tipo'            => $tipo_movimiento->id,
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'descuento'       => 0,
            'iva'             => $request['iva'],
            'total'           => $request['total'],
            'id_empresa'      => Session::get('id_empresa'),
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        //dd($input);
        /*$verifica_cierre = LogAsiento::verificar_cierre_anio(date('Y-m-d'));
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }*/
        $fecha = date('Y-m-d');
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fecha));
        
        if ($verifica_cierre->status == 'error') {
            $data['msj']   =  $verifica_cierre->status;
            $data['error'] =  $verifica_cierre->message;
            return $data;
        }
        //dd($fecha, $verifica_cierre);
        DB::beginTransaction();
        try {
 
            $id_pedido = Pedido::insertGetId($input);
            // GUARDO EN LA TABLA DE MOVIMIENTOS DE INVENTARIO //
            //      INGRESO LA CABECERA DEL MOVIMIENTO       //
            if ($tipo == 'I') {
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'INV')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'INGRESO';
            }
            if ($tipo == 'E') {
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EGV')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGRESO';
            }
            if ($tipo == 'R') {
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'IVR')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'ING REGALO';
            }
            if ($tipo == 'C') {
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EVC')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGR CONSUMIBLE';
            }
            if ($tipo == 'N') {
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'ENR')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGR NO REUSO';
            }
            if($tipo == 'G'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'IER')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'IER REUTILIZABLE';
            }
            if($tipo == 'U'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EER')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EER REUTILIZABLE';
            }
            if($tipo == 'P'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EPP')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGRESO POR PLANILLA';
            }
            if($tipo == 'M'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EMC')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGRESO DEVOLUCION CONSIGNA';
            }
            if($tipo == 'B'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EPR')->first();
                $leyenda = 'EGRESO POR REGALO';
            }
            if($tipo == 'F'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EGF')->first();
                $leyenda = 'EGRESO POR FACTURA';
            }
            if($tipo == 'V'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EPI')->first();
                $leyenda = 'EGRESO DE INVENTARIO';
            }
            if($tipo == 'S'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'EMS')->where('id_empresa', Session::get('id_empresa'))->first();
                $leyenda = 'EGRESO DEVOLUCION CONSIGNA SOLCA';
            }
            if($tipo == 'O'){
                $docu = InvDocumentosBodegas::where('abreviatura_documento', 'ECC')->first();
                $leyenda = 'EGRESO CONSUMIBLES EN CONSIGNA';
            }
            if (!isset($docu->id)) {
                $data['msj']   = 'error';
                $data['error'] = 'No existe el documento de bodega. ';
                return response()->json($data);
            }
            $secuencia = InvDocumentosBodegas::getSecuecia($docu->id, $request['bodega_recibe']);
            
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $docu->id)
                    ->where('id_bodega', $request['bodega_recibe'])
                    ->first();
                $doc_bodega                         = InvDocumentosBodegas::find($docu->id);
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $docu->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $request['bodega_recibe'];
                $cab_mov_inv->id_bodega_destino     = $request['bodega_recibe'];
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $request['observaciones'];
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->descuento             = $request['descuentx'];
                $cab_mov_inv->subtotal              = $request['subtotal_12'];
                $cab_mov_inv->subtotal_0            = $request['subtotal_0'];
                $cab_mov_inv->iva                   = $request['iva'];
                $cab_mov_inv->total                 = $request['total'];
                $cab_mov_inv->id_pedido             = $id_pedido;
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();
                //dd($cab_mov_inv);
            }
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {

                    $cant_uso = 0;
                    $iva      = 0;
                    $producto = Producto::find($request['id' . $i]);
                    if (isset($producto->id)) {
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($request['cantidad' . $i] * $request['precio' . $i]) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }
                    }
                    $cantidad          = $request['cantidad' . $i];
                    $consecion_detalle = 0;
                    if ($request['consecion_det' . $i] == 1) {
                        $consecion_detalle = 1;
                    }

                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => $request['cantidad' . $i],
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'usos'              => $request['cantidad' . $i] * $cant_uso,
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
                    $id_movimiento      = DB::table('movimiento')->insertGetId($input2);
                    $id_producto        = $request['id' . $i];
                    $producto           = Producto::find($id_producto);
                    $cantidad_producto  = $producto->cantidad;
                    $nueva_cantidad     = $cantidad_producto + 1;

                    //  DETALLE DE INGRESO   //
                    if (isset($cab_mov_inv->id) and $request['cantidad' . $i] != 0 and $visibilidad == 1) {
                        if (isset($documentos->id) and $documentos->tipo != '') {
                            $tipo = $documentos->tipo;
                        } else {
                            $tipo = 'C';
                        }
                        $inventario = InvInventario::getInventario($request['id' . $i], $request['bodega_recibe'], $tipo);
                        if ($inventario == '[]') {
                            $inventario = InvInventario::setNeoInventario($request['id' . $i], $request['bodega_recibe'], $tipo, 0, 0);
                        }

                        $det_mov_inv                         = new InvDetMovimientos;
                        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                        $det_mov_inv->id_producto            = $request['id' . $i];
                        $det_mov_inv->id_inv_inventario      = $inventario->id;
                        $det_mov_inv->cantidad               = $request['cantidad' . $i];
                        $det_mov_inv->cant_uso               = $request['cantidad' . $i] * $cant_uso;
                        $det_mov_inv->serie                  = $request['serie' . $i];
                        $det_mov_inv->lote                   = $request['lote' . $i];
                        $det_mov_inv->fecha_vence            = $request['fecha_vencimiento' . $i];
                        $det_mov_inv->valor_unitario         = $request['precio' . $i];
                        $det_mov_inv->subtotal               = $request['cantidad' . $i] * $request['precio' . $i];
                        $det_mov_inv->descuento              = $request['descuento' . $i];
                        $det_mov_inv->iva                    = $iva;
                        $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                        $det_mov_inv->motivo                 = $leyenda . ' VARIO';
                        $det_mov_inv->id_detalle_pedido      = $id_movimiento;
                        $det_mov_inv->ip_creacion            = $ip_cliente;
                        $det_mov_inv->ip_modificacion        = $ip_cliente;
                        $det_mov_inv->id_pedido              = $id_pedido;
                        $det_mov_inv->id_usuariocrea         = $idusuario;
                        $det_mov_inv->id_usuariomod          = $idusuario;
                        $det_mov_inv->save();
                    }
                }
            }

            // MOVIMIENTO EN KARDEX
            $kardex = InvKardex::setKardex($cab_mov_inv->id);
            // CONTABILIDAD
            $id_asiento              = InvContableCab::setAsientoContablePedido($cab_mov_inv->id);
            $cab_mov_inv->id_asiento = $id_asiento;
            $cab_mov_inv->save();

           
            DB::commit();
            // if (Auth::user()->id == '0957258056') {
            //     dd($id_asiento);
            // }

            // DB::rollBack();
            $data['msj'] = 'ok';
            return response()->json($data);
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function editar($id)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $cab_movimiento = InvCabMovimientos::find($id);
        $pedido         = Pedido::find($cab_movimiento->id_pedido);
        if(Auth::user()->id == '0951561075'){
            //dd($pedido);
        }
        $bodegas        = Bodega::where('estado', '1')->whereNull('deleted_at')->get();
        $proveedores    = Proveedor::where('estado', '1')->get();
        $empresa        = Empresa::all();
        $bod_princ      = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        $documentos     = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->where('id_inv_tipo_movimiento', 1)->whereIn('id', [1, 2, 3, 4, 5, 6])->get();
        return view('inventario/ing_egr_vario/editar', [
            'bodegas' => $bodegas, 'proveedores' => $proveedores,
            'empresa' => $empresa, 'doc_bodega'  => $documentos, 'bodega_principal' => $bod_princ, 'pedido' => $pedido, 'cab_movimiento' => $cab_movimiento
        ]);
    }

    public function actualizar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $pedido = Pedido::find($request->id_pedido);
        $variable = $request['contador'];
        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i              => 'required',
                    'fecha_vencimiento' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required'              => 'Ingrese el numero de Lote.',
                    'fecha_vencimiento' . $i . '.required' => 'Ingrese la fecha de vencimiento.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }
        if ($request['id_proveedor'] == "0") {
            $request['id_proveedor'] = '9999999999999';
        }
        $tipo = $request['tipo'];
        if ($tipo == 'I') {
            $leyenda = 'INV';
        }
        if ($tipo == 'E') {
            $leyenda = 'EGV';
        }
        if ($tipo == 'R') {
            $leyenda = 'IVR';
        }
        if ($tipo == 'C') {
            $leyenda = 'EVC';
        }
        if ($tipo == 'N') {
            $leyenda = 'ENR';
        }
        if ($tipo == 'G') {
            $leyenda = 'IER';
        }
        if ($tipo == 'U') {
            $leyenda = 'EER';
        }
        if ($tipo == 'P') {
            $leyenda = 'EPP';
        }
        if ($tipo == 'M') {
            $leyenda = 'EMC';
        }
        if($tipo == 'B'){
            $leyenda = 'EPR';
        }
        if($tipo == 'F'){
            $leyenda = 'EGF';
        }
        if($tipo == 'V'){
            $leyenda = 'EPI';
        }
        if($tipo == 'O'){
            $leyenda = 'ECC';
        }
        DB::beginTransaction();
        try {
            DB::commit();
            $pedido->id_proveedor       = $request['id_proveedor'];
            $pedido->observaciones      = $request['observaciones'];
            $pedido->save();
            // movimiento de inventario cabecera //
            $cab_mov_inv                = InvCabMovimientos::where('id_pedido', $pedido->id)->first();
            $cab_mov_inv->observacion   = $request['observaciones'];
            $cab_mov_inv->save();
            // dd($request);
            // detalles //
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {
                    // movimientos
                    $movimiento         = Movimiento::find($request['id_movimiento' . $i]);
                    $movimiento->lote   = $request['lote' . $i];
                    $movimiento->fecha_vencimiento   = $request['fecha_vencimiento' . $i];
                    $movimiento->save();
                    // actualizar lote y fecha de vencimiento 
                    $dets_mov            = InvDetMovimientos::where('serie', $movimiento->serie)->get();
                    foreach ($dets_mov as $det) {
                        $det->lote            = $request['lote' . $i];
                        $det->fecha_vence     = $request['fecha_vencimiento' . $i];
                        $det->save();
                    }
                    $inv_series          = InvInventarioSerie::where('serie', $movimiento->serie)->get();
                    foreach ($inv_series as $det) {
                        $det->lote            = $request['lote' . $i];
                        $det->fecha_vence     = $request['fecha_vencimiento' . $i];
                        $det->save();
                    }
                }
            }
            $data['msj'] = 'ok';
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function anularIngEgrVario($id_pedido)
    {
        # busco el documento #
        $cabcera = InvCabMovimientos::where('id_pedido', $id_pedido)->where('estado', 1)->first();
        if ($cabcera->id) {
            # busco los traslados #
            InvCabMovimientos::anularTransaccionBodega($row->id_inv_cab_movimientos);
            // InvContableCab::anularAsiento($cabcera->id_asiento);
            $cabcera->id_pedido = null;
            $cabcera->save();
            $this->eliminar_pedido($id_pedido);
        }
    }

    public function eliminar_pedido($id)
    {
        DB::beginTransaction();
        try {
            $pedido_v       = Pedido::find($id);
            $productos = Movimiento::where('id_pedido', $id)->get();
            // movimientos inventario 
            $inv_movimientos = InvCabMovimientos::where('id_pedido', $id)->get();
            foreach ($inv_movimientos as $mov) {
                $movimiento = InvCabMovimientos::find($mov->id);
                $movimiento->id_pedido = null;
                $movimiento->save();
            }

            $detalles = Detalle_Pedido::where('id_pedido', $id)->get();
            foreach ($detalles as $row) {
                $detalle            = Detalle_Pedido::find($row->id);
                $detalle->delete();
            }
            foreach ($productos as $value) {
                Log_movimiento::where('id_movimiento', $value->id)->delete();
                $producto           = Producto::find($value->id_producto);
                $cantidad           = $producto->cantidad - 1;
                $producto->cantidad = $cantidad;
                $producto->save();
                $movimiento = Movimiento::find($value->id);
                $movimiento->delete();
            }
            $pedido = Pedido::find($id);
            $pedido->delete();
            DB::commit();
            return "okay";
            // DB::rollBack();
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function pdf_ingreso_egreso($id, Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $cab_movimiento = InvCabMovimientos::find($id);
        $pedido         = Pedido::find($cab_movimiento->id_pedido);
        $bodegas        = Bodega::where('estado', '1')->whereNull('deleted_at')->get();
        $proveedores    = Proveedor::where('estado', '1')->get();
        $empresa = Empresa::where('id', $cab_movimiento->id_empresa)->first();
        $bod_princ      = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        $documentos     = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->where('id_inv_tipo_movimiento', 1)->whereIn('id', [1, 2, 3, 4, 5, 6])->get();
        $documento_bodega = $cab_movimiento->documento_bodega->documento;

        $vistaurl = "inventario/ing_egr_vario/pdf_ing_egr";
        $view     = \View::make($vistaurl, compact('documento_bodega','bodegas','proveedores','documentos','bod_princ','pedido','cab_movimiento','empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Ingreso_Egreso_Vario.pdf');

    }


    public function listar_series(Request $request){
        
        $codigo     = $request->codigo;
        $bodega     = $request->bodega_recibe;
        $serie      = $request->serie;
        //dd($request->all());
        $id_empresa = Session::get('id_empresa');
        $series     = [];
        if($codigo != null){
            if($bodega != null){
                if($id_empresa != null){
                    $producto = Producto::where('codigo',$codigo)->first();
                    if(!is_null($producto)){
                        //dd($bodega,$id_empresa,$producto);
                        $series = InvInventarioSerie::where('id_empresa',$id_empresa)->where('id_bodega',$bodega)->where('id_producto',$producto->id)->where('existencia','>','0')->get();
                    }
                }    
            }    
        }else{
            if($serie != null){
                if($bodega != null){
                    if($id_empresa != null){
                        $series = InvInventarioSerie::where('id_empresa',$id_empresa)->where('id_bodega',$bodega)->where('serie',$serie)->where('existencia','>','0')->get();
                    }    
                }    
            }
                    
        }

        return view('inventario/ing_egr_vario/listado_serie', [
            'series' => $series
        ]);
 
    } 
}
