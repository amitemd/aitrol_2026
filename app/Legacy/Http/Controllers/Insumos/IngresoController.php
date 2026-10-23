<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
//use Session;
use Illuminate\Support\Facades\Session;
use Mail;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\Inventario;
use Sis_medico\InvInventario;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\Log_Detalle_Pedido;
use Sis_medico\Log_movimiento;
use Sis_medico\LogAsiento;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardexNuevo;
use Sis_medico\PreMovimiento;
use Sis_medico\PrePedido;
use Sis_medico\Movimiento_Paciente;


class IngresoController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7, 20, 10)) == false) {
            return true;
        }
    }

    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = session()->get('id_empresa');
        $bodegas     = Bodega::where('estado', '1')->whereNull('deleted_at')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::all();
        //$bod_princ   = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        $bod_princ   = Bodega::where('estado', '1')->where('id_empresa', $id_empresa)->where('prioridad', 1)->first();
        $documentos  = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->where('id_inv_tipo_movimiento', 1)->whereIn('id', [1, 2, 3, 4])->get();
        return view('insumos/ingreso/index', [
            'bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'doc_bodega'  => $documentos, 'bodega_principal' => $bod_princ
        ]);
    }

    public function editar_pedido($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return response()->view('errors.404');

        $pedido = pedido::findOrFail($id);

        // $cantidad_pedido = Movimiento::where('id_pedido', $pedido->id)->get();
        //dd($pedido);

        /* $cantidad_pedido = DB::table('movimiento as m')
        ->where('m.id_pedido', $id)
        ->join('pedido as p', 'p.id', 'm.id_pedido')
        ->join('producto as  pro', 'pro.id', 'm.id_producto')
        ->join('bodega as b', 'b.id', 'm.id_bodega')
        ->where('m.tipo', '1')
        ->where('m.estado', '1')
        ->groupBy('m.serie')
        ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', DB::raw('count(*) as cantidad, m.serie'), 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'pro.usos', 'm.precio', 'm.descuentop', 'm.descuento', 'pro.iva', 'pro.id as id_producto', 'pro.registro_sanitario', 'm.consecion_det')
        ->get();*/
        //dd($cantidad_pedido);

        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();

        $empresa = Empresa::all();

        /************************** */

        $cantidad_pedido = DB::table('movimiento as m')
            ->where('m.id_pedido', $id)
            ->join('pedido as p', 'p.id', 'm.id_pedido')
            ->join('producto as  pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('m.serie')
            ->select('m.id', 'pro.codigo as codigo', 'pro.nombre as nombre_producto', DB::raw('m.cantidad, m.serie'), 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'pro.usos', 'm.precio', 'm.descuentop', 'm.descuento', 'pro.iva', 'pro.id as id_producto', 'pro.registro_sanitario', 'm.consecion_det')
            ->get();
        //$detalle = Movimiento::where('id_pedido', $id_pedido)->get();

        /*********************1 */

        //dd($detalle);
        $documentos = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->get();
        $bod_princ  = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        return view('insumos/ingreso/editar_pedido', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'pedido' => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ]);
    }

    public function formulario(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['codigo'];
        $nombre = $request['nombre'];

        $producto = Producto::where('codigo', 'LIKE', '%' . $codigo . '%')
            ->where('nombre', 'like', '%' . $nombre . '%')
            ->where('estado', '1')
            ->with('inv_costo')
            ->get();

        return $producto;
    }

    public function formulario_serie(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $serie  = $request['serie'];
        $bodega = $request['bodega_recibe'];
        $cantidad_egr = $request['cantidad_egr'];
        $idusuario  = Auth::user()->id;
        



        $invserie = InvInventarioSerie::where('id_bodega', $bodega)->where('serie', $serie)->where('id_empresa', Session::get('id_empresa'))->where('existencia', '>', '0')->first();
        $inv      = $invserie->inventario;
        $mbodega   = $invserie->bodega;
        $vt_valor = 0;
        //$cab_mov = InvCabMovimientos::find($invserie->id_cab_movimientos);
        //$pedido = $cab_mov->pedido;
        $cab_movimientos = InvDetMovimientos::where('serie',$serie)->where('id_inv_inventario',$inv->id)->where('estado','1')->first();
        $cab_mov  = InvCabMovimientos::find($cab_movimientos->id_inv_cab_movimientos);
       
        if($idusuario == '0941096372'){
            //dd($cab_mov,$cab_mov->detalles->where('serie', $serie)->first());
        }
        
        if (!is_null($cab_mov)) {

            $detalle = $cab_mov->detalles->where('serie', $serie)->first();
            if (!is_null($detalle)) {
                $vt_valor = $detalle->valor_unitario;
            }
        } else {
            return [[], [], [], 'err', 'Inconsistencia, No asociado con Cabecera'];
        }

        $producto = [];
        if (!is_null($invserie)) {
            if ($cantidad_egr < 1) {
                return [[], [], [], 'err', 'La cantidad solicitada es menor a 0'];
            }
            if ($cantidad_egr > $invserie->existencia) {
                return [[], [], [], 'err', 'La cantidad solicitada: ' . $cantidad_egr . ' es superior a la cantidad de existencias: ' . $invserie->existencia];
            }
            $producto = $invserie->producto;
        }



        return [$producto, $invserie, $inv, 'ok', 'none', $mbodega, $vt_valor];
    }
    private function ValidatePedido(Request $request)
    {

        $prules = [
            'pedido'       => 'required',
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);
    }

    public function guardar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $iva      = 0;
        $variable = $request['contador'];
        $bodega   = Bodega::find($request['bodega_recibe']);
        if (isset($bodega->id)) {
            $request['id_empresa'] = $bodega->id_empresa;
        }

        $validaCodigo = Pedido::where('id_empresa', Session::get('id_empresa'))->where('pedido', $request['pedido'])->whereNull('deleted_at')->first();

        if (!is_null($validaCodigo)) {
            $data['msj']   = 'error';
            $data['error'] = "error: Ya existe un pedido activo con el mismo codigo";
            return response()->json($data);
        }

        $input = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'tipo'            => $request['tipo_'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'id_bodega'       => $request['bodega_recibe'],
            'observaciones'   => $request['observaciones'],
            'consecion'       => $request['consecion'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'descuento'       => $request['descuentx'],
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

        $this->validatePedido($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i              => 'required',
                    //'fecha_vencimiento' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required'              => 'Ingrese el numero de Lote.',
                    //'fecha_vencimiento' . $i . '.required' => 'Ingrese la fecha de vencimiento.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }
        //



        DB::beginTransaction();
        try {


            $id_pedido = Pedido::insertGetId($input);
            // GUARDO EN LA TABLA DE MOVIMIENTOS DE INVENTARIO //
            //      INGRESO LA CABECERA DEL MOVIMIENTO       //
            $secuencia = InvDocumentosBodegas::getSecuecia($request['tipo_'], $request['bodega_recibe']);
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $request['tipo_'])
                    ->where('id_bodega', $request['bodega_recibe'])
                    ->first();
                $doc_bodega                         = InvDocumentosBodegas::find($request['tipo_']);
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $request['tipo_'];
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $request['bodega_recibe'];
                $cab_mov_inv->id_bodega_destino     = $request['bodega_recibe'];
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $request['observaciones'];
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->num_doc_ext           = $request['pedido'];
                $cab_mov_inv->num_doc_cont          = $request['num_factura'];
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
                    // for ($x = 0; $x < $cantidad; $x++) {
                    if ($request['fecha_vencimiento' . $i] == NULL or !isset($request['fecha_vencimiento' . $i])) {
                        $request['fecha_vencimiento' . $i] = date('Y-m-d');
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
                    $id_movimiento = DB::table('movimiento')->insertGetId($input2);
                    $id_producto   = $request['id' . $i];
                    $producto      = Producto::find($id_producto);
                    //$id_ct_productos   = Ct_productos_insumos::where('id_insumo',$producto->id)->get();
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;
                    // }
                }



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

                    /// KARDEX POR USOS VICTOR
                    $cantidad_usos_kardex = 0;
                    if ($producto->kardex_uso == 1) {
                        $cantidad_usos_kardex = $producto->cantidad_usos_kardex;
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
                    $det_mov_inv->motivo                 = 'INGRESO PEDIDO';
                    $det_mov_inv->id_pedido              = $id_pedido;
                    // $det_mov_inv->id_detalle_origen         = $id_movimiento;
                    $det_mov_inv->id_detalle_pedido = $id_movimiento;
                    /// KARDEX POR USOS VICTOR
                    $det_mov_inv->cantidad_usos_kardex = $cantidad_usos_kardex;
                    ////////
                    $det_mov_inv->ip_creacion       = $ip_cliente;
                    $det_mov_inv->ip_modificacion   = $ip_cliente;
                    $det_mov_inv->id_usuariocrea    = $idusuario;
                    $det_mov_inv->id_usuariomod     = $idusuario;
                    $det_mov_inv->save();
                }
            }
            // MOVIMIENTO EN KARDEX
            $this->recalcular_pedido($id_pedido);


            $kardex = InvKardex::setKardex($cab_mov_inv->id);



            // CONTABILIDAD
            $id_asiento              = InvContableCab::setAsientoContablePedido($cab_mov_inv->id);

            if (!is_null($id_asiento)) {
                $cab_mov_inv->id_asiento = $id_asiento;
                $cab_mov_inv->save();
            }



            //ENVIO DE CORREO A LA ING SHEYLA
            if (Session::get('id_empresa') == "0992704152001") {
                $correo = 'fortiz@mdconsgroup.com';
                $array = ['id_pedido' => $id_pedido];
                Mail::send('insumos/ingreso/mail', $array, function ($msj) use ($correo) {
                    $msj->subject('Numero de Pedido');
                    $msj->to($correo);
                    $msj->from('soporte@mdconsgroup.com');
                    $msj->bcc('torbi10@hotmail.com');
                });
            }



            $url = route('producto.index');
            DB::commit();
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

    public function guardarDocumentoBodega($id_pedido)
    {
        # code...
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $pedido = Pedido::find($id_pedido);
    }

    private function ValidatePedido_actualizar(Request $request)
    {

        $id     = $request['id_pedido'];
        $prules = [
            'pedido'       => 'required|unique:pedido,id,' . $id,
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);
    }

    public function actualizar_pedido(Request $request)
    {
        //return $request['total'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $pedido = Pedido::findOrFail($request['id_pedido']);

        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'tipo'            => $request['tipo_'],
            'id_empresa'      => Session::get('id_empresa'),
            'consecion'       => $request['consecion'],
            'observaciones'   => $request['observaciones'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'iva'             => $request['iva'],
            'descuento'       => $request['descuentx'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        $pedido->update($input);

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

        $id        = $request['id_pedido'];
        $id_pedido = $id;

        DB::beginTransaction();
        try {

            for ($i = 0; $i < $variable; $i++) {
                $serie = $request['serie' . $i];

                $cantidad_pedido = DB::table('movimiento as m')
                    ->where('m.id_pedido', $id)
                    ->join('pedido as p', 'p.id', 'm.id_pedido')
                    ->join('producto as  pro', 'pro.id', 'm.id_producto')
                    ->join('bodega as b', 'b.id', 'm.id_bodega')
                    ->where('m.tipo', '1')
                    ->where('m.estado', '1')
                    ->where('m.serie', $serie)
                    ->groupBy('m.serie')
                    ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', 'm.cantidad', 'm.serie', 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'm.precio', 'pro.iva')
                    ->first();
                if (!is_null($cantidad_pedido)) {
                    $visibilidad = $request['visibilidad' . $i];
                    if ($visibilidad == 1) {
                        // if ($cantidad_pedido->cantidad == $request['cantidad' . $i]) {
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                        foreach ($detalles_actualizar as $value) {
                            $movimiento_detalle_ac = Movimiento::find($value->id);
                            $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                            $cantidad_producto     = $producto->cantidad;
                            $input_ac              = [
                                'precio'            => $request['precio' . $i],
                                'cantidad'          => $request['cantidad' . $i],
                                'lote'              => $request['lote' . $i],
                                'id_bodega'         => $request['id_bodega' . $i],
                                'descuentop'        => $request['descuento' . $i],
                                'descuento'         => $request['descuentof' . $i],
                                'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariomod'     => $idusuario,
                                'consecion_det'     => $request['consecion_det' . $i],
                            ];
                            Log_movimiento::create([
                                'id_producto'     => $movimiento_detalle_ac->id_producto,
                                'id_movimiento'   => $value->id,
                                'id_encargado'    => $idusuario,
                                'observacion'     => "Actualizacion de precio/lote",
                                'tipo'            => '1',
                                'ip_creacion'     => $ip_cliente,
                                'cantidad'        => $request['cantidad' . $i],
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                            $movimiento_detalle_ac->update($input_ac);
                        }
                        // }
                    }
                } else {
                    # agrego el nuevo detalle
                    $visibilidad = $request['visibilidad' . $i];
                    if ($visibilidad == 1) {
                        $producto = Producto::find($request['id' . $i]);
                        $input2   = [
                            'id_producto'       => $request['id' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'id_encargado'      => $idusuario,
                            'serie'             => $request['serie' . $i],
                            'id_bodega'         => $request['id_bodega' . $i],
                            'tipo'              => '1',
                            'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                            'lote'              => $request['lote' . $i],
                            'descuentop'        => $request['descuento' . $i],
                            'descuento'         => $request['descuentof' . $i],
                            'usos'              => $producto->usos,
                            'precio'            => $request['precio' . $i],
                            'id_pedido'         => $id_pedido,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                        ];

                        $id_movimiento = Movimiento::insertGetId($input2);
                    }
                }
            }
            if ($id_pedido != null) {
                # actualizo el movimiento de inventario o el ingreso pedido
                InvCabMovimientos::actualizarIngresoPedido($id_pedido);
            }
            DB::commit();
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

    public function ___actualizar_pedido(Request $request)
    {
        //return $request['total'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $pedido = Pedido::findOrFail($request['id_pedido']);

        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'tipo'            => $request['tipo_'],
            'id_empresa'      => $request['id_empresa'],
            'consecion'       => $request['consecion'],
            'observaciones'   => $request['observaciones'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'iva'             => $request['iva'],
            'descuento'       => $request['descuentx'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $pedido->update($input);
        //$this->ValidatePedido_actualizar($request);

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
        $id        = $request['id_pedido'];
        $id_pedido = $id;
        for ($i = 0; $i < $variable; $i++) {
            $serie = $request['serie' . $i];

            $cantidad_pedido = DB::table('movimiento as m')
                ->where('m.id_pedido', $id)
                ->join('pedido as p', 'p.id', 'm.id_pedido')
                ->join('producto as  pro', 'pro.id', 'm.id_producto')
                ->join('bodega as b', 'b.id', 'm.id_bodega')
                ->where('m.tipo', '1')
                ->where('m.estado', '1')
                ->where('m.serie', $serie)
                ->groupBy('m.serie')
                ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', 'm.cantidad', 'm.serie', 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'm.precio', 'pro.iva')
                ->first();
            if (!is_null($cantidad_pedido)) {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {
                    if ($cantidad_pedido->cantidad == $request['cantidad' . $i]) {
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                        foreach ($detalles_actualizar as $value) {
                            $movimiento_detalle_ac = Movimiento::find($value->id);
                            $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                            $cantidad_producto     = $producto->cantidad;
                            $input_ac              = [
                                'precio'            => $request['precio' . $i],
                                'lote'              => $request['lote' . $i],
                                'id_bodega'         => $request['id_bodega' . $i],
                                'descuentop'        => $request['descuento' . $i],
                                'descuento'         => $request['descuentof' . $i],
                                'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariomod'     => $idusuario,
                                'consecion_det'     => $request['consecion_det' . $i],
                            ];
                            Log_movimiento::create([
                                'id_producto'     => $movimiento_detalle_ac->id_producto,
                                'id_movimiento'   => $value->id,
                                'id_encargado'    => $idusuario,
                                'observacion'     => "Actualizacion de precio/lote",
                                'tipo'            => '1',
                                'ip_creacion'     => $ip_cliente,
                                'cantidad'        => $request['cantidad' . $i],
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                            $movimiento_detalle_ac->update($input_ac);
                        }
                    } elseif ($cantidad_pedido->cantidad < $request['cantidad' . $i]) {
                        $cantidad   = $request['cantidad' . $i] - $cantidad_pedido->cantidad;
                        $pedido_act = Movimiento::where('serie', $serie)->where('estado', 1)->first();
                        // for ($x = 0; $x < $cantidad; $x++) {
                        $input2 = [
                            'id_producto'       => $pedido_act->id_producto,
                            'cantidad'          => $request['cantidad' . $i],
                            'id_encargado'      => $idusuario,
                            'serie'             => $pedido_act->serie,
                            'id_bodega'         => $pedido_act->id_bodega,
                            'tipo'              => '1',
                            'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                            'lote'              => $request['lote' . $i],
                            'descuentop'        => $request['descuento' . $i],
                            'descuento'         => $request['descuentof' . $i],
                            'usos'              => $pedido_act->usos,
                            'precio'            => $request['precio' . $i],
                            'id_pedido'         => $id_pedido,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'consecion_det'     => $request['consecion_det' . $i],
                        ];

                        $id_movimiento = Movimiento::insertGetId($input2);

                        $id_producto       = $pedido_act->id_producto;
                        $producto          = Producto::find($id_producto);
                        $cantidad_producto = $producto->cantidad;
                        $nueva_cantidad    = $cantidad_producto + 1;

                        $input3 = [
                            'cantidad'        => $nueva_cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        Log_movimiento::create([
                            'id_producto'     => $pedido_act->id_producto,
                            'id_movimiento'   => $id_movimiento,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Ingreso del producto",
                            'tipo'            => '1',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => $request['cantidad' . $i],
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        Producto::where('id', $id_producto)->update($input3);
                        // }
                    } elseif ($cantidad_pedido->cantidad > $request['cantidad' . $i]) {
                        $cantidad            = $cantidad_pedido->cantidad - $request['cantidad' . $i];
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->first();
                        // for ($x = 0; $x < $cantidad; $x++) {
                        $movimiento_detalle_ac = Movimiento::find($detalles_actualizar->id);
                        $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                        $cantidad_producto     = $producto->cantidad;
                        $nueva_cantidad        = $cantidad_producto - 1;
                        $input3                = [
                            'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        $input_ac = [
                            'tipo'            => 4,
                            'estado'          => 0,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'consecion_det'   => $request['consecion_det'],
                        ];
                        Log_movimiento::create([
                            'id_producto'     => $movimiento_detalle_ac->id_producto,
                            'id_movimiento'   => $detalles_actualizar->id,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Eliminacion de Producto de pedido",
                            'tipo'            => '4',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => $request['cantidad' . $i],
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        $movimiento_detalle_ac->update($input_ac);
                        $producto->update($input3);
                        // }
                    }
                } else {
                    $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                    foreach ($detalles_actualizar as $value) {
                        $movimiento_detalle_ac = Movimiento::find($value->id);
                        $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                        $cantidad_producto     = $producto->cantidad;
                        $nueva_cantidad        = $cantidad_producto - 1;
                        $input3                = [
                            'cantidad'        => $nueva_cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        $input_ac = [
                            'tipo'            => 4,
                            'estado'          => 0,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        Log_movimiento::create([
                            'id_producto'     => $movimiento_detalle_ac->id_producto,
                            'id_movimiento'   => $value->id,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Eliminacion de Producto de pedido",
                            'tipo'            => '4',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => '1',
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        $movimiento_detalle_ac->update($input_ac);
                        $producto->update($input3);
                    }
                }
            } else {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {
                    $cantidad = $request['cantidad' . $i];

                    // for ($x = 0; $x < $cantidad; $x++) {
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => $request['cantidad' . $i],
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'descuentop'        => $request['descuento' . $i],
                        'descuento'         => $request['descuentof' . $i],
                        'usos'              => $request['usos' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => $request['consecion_det' . $i],
                    ];

                    $id_movimiento = DB::table('movimiento')->insertGetId($input2);

                    $id_producto       = $request['id' . $i];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $cantidad,
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
                        'cantidad'        => $request['cantidad' . $i],
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Producto::where('id', $id_producto)->update($input3);
                    // }
                }
            }
        }
        $url = route('producto.index');
        return $url;
    }

    public function eliminar_pedido($id)
    {
        $seq = InvDocumentosBodegas::getSecueciaTipo(env('BODEGA_EGR_PACI1', 14), 'T');
        //dd($seq);
        // $this->anularIngPedido($id);
        // $cabcera        = InvCabMovimientos::where('id_pedido',1)->where('estado',1)->first();
        // $traslados      = InvTrasladosBodegas::where('id_inv_cab_mov_origen', $cabcera->id)->orderBy('id','asc')->get();
        // dd($traslados);
        $productos = Movimiento::where('id_pedido', $id)
            /*->where(function ($query) {
                $query->where('tipo', 0)
                    ->orWhere('tipo', 2)->orWhere('tipo', 4);
            }) POR CONSTRAINT NO IMPORTA EL TIPO QUE SEA NO SE PERMITE ELIMINAR*/->get();
        //dd($productos);
        $productos = Movimiento::join('movimiento_paciente as mp','movimiento.id','mp.id_movimiento')->where('movimiento.id_pedido', $id)->get();
        //dd($productos);
        //$movimientos = Movimiento_Paciente::where('id_movimiento',$productos->id)->first();

        return view('insumos/ingreso/eliminar', ['productos' => $productos, 'id' => $id]);
    }

    public function eliminar_clave(Request $request)
    {
        DB::beginTransaction();
        try {
            $ingresada      = $request['password'];
            $id             = $request['id'];
            $hashedPassword = Auth::user()->password;
            $pedido_v       = Pedido::find($id);
            if (Hash::check($ingresada, $hashedPassword) && $pedido_v->estado_contable == 0) {
                $productos = Movimiento::where('id_pedido', $id)->get();
                foreach ($productos as $value) {
                    Log_movimiento::where('id_movimiento', $value->id)->delete();
                    $producto           = Producto::find($value->id_producto);
                    $cantidad           = $producto->cantidad - 1;
                    $producto->cantidad = $cantidad;
                    $producto->save();
                    $movimiento = Movimiento::find($value->id);
                    $movimiento->delete();
                }
                $this->anularIngPedido($id);

                $pedido = Pedido::find($id);
                $pedido->delete();
                DB::commit();
                return "okay";
            }
            // DB::rollBack();
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function anularIngPedido($id_pedido)
    {
        # busco el documento
        $cabcera = InvCabMovimientos::where('id_pedido', $id_pedido)->where('estado', 1)->first();
        if (!is_null($cabcera)) {
            if ($cabcera->id) {
                # busco los traslados
                $traslados = InvTrasladosBodegas::where('id_inv_cab_mov_origen', $cabcera->id)->get();
                foreach ($traslados as $row) {
                    InvCabMovimientos::anularTransaccionBodega($row->id_inv_cab_movimientos);
                    // break;
                }
                # se anulan primero los traslados y se generan los egresos
                InvCabMovimientos::anularTransaccionBodega($cabcera->id);
                if (!is_null($cabcera->id_asiento)) {
                    InvContableCab::anularAsiento($cabcera->id_asiento);
                }


                //dd('entra');
                $cabcera->id_pedido = null;
                $cabcera->save();
            }
        }
    }

    public function consecion_detalle()
    {
        $consecion = Pedido::where('consecion', '1')->get();
        //dd($consecion);
        foreach ($consecion as $value) {
            foreach ($value->detalle as $detalle) {
                //dd($detalle);
                $arr = [
                    'consecion_det'   => '1',
                    'ip_modificacion' => 'cambio consecion',
                ];
                $detalle->update($arr);
            }
        }

        return "ok";
    }
    public function generar_factura(Request $request)
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
    public function conglomerada(Request $request)
    {
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->get();
        return view('insumos/ingreso/master', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }
    public function details(Request $request)
    {
        $serie       = $request['serie'];
        $series      = $request['series'];
        $pedido      = $request['pedido'];
        $codigo      = $request['codigo'];
        $lote        = $request['lote'];
        $tipo        = $request['tipo'];
        $idproveedor = $request['proveedor'];
        $fdesde      = $request['desde'];
        $fhasta      = $request['hasta'];
        $ds          = DB::table('pedido')->where('pedido', $pedido)->first();
        $proveedor   = null;
        $objpedido   = null;
        if (isset($ds->id)) {
            $proveedor = Proveedor::find($ds->id_proveedor);
        }
        $val_series = "";
        $separador  = "";
        if (!empty($series)) {
            foreach ($series as $row) {
                $val_series .= "$separador '$row'";
                $separador = ",";
            }
        }

        $productos = array();
        if ($tipo == 1) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vence as fecha_vencimiento,det.lote,
            det.descuento as descuentop, p.id, p.id_proveedor, prov.razonsocial, p.fecha,
            p.vencimiento, prov.id_cuentas, px.nombre,cab.id_documento_bodega as id_bodega,det.subtotal as precio,
            px.iva, prov.direccion, p.id_proveedor, p.id_empresa, p.pedido, b.nombre as bodega, prov.nombrecomercial
            FROM inv_det_movimientos det
            JOIN inv_cab_movimientos cab ON det.id_inv_cab_movimientos =cab.id
            JOIN inv_documentos_bodegas doc ON cab.id_documento_bodega = doc.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN pedido AS p ON p.id = cab.id_pedido
            INNER JOIN proveedor AS prov ON prov.id = p.id_proveedor
            JOIN bodega b ON b.id =cab.id_bodega_origen
            WHERE 1 = 1 ";
            if ($serie != "") {
                $sql .= " AND det.serie = '" . $serie . "' ";
            }
            if ($codigo != "") {
                $sql .= " AND px.codigo = '" . $codigo . "' ";
            }
            if ($lote != "") {
                $sql .= " AND det.lote = '" . $lote . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 2) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vence as fecha_vencimiento,det.lote,
            det.descuento as descuentop, p.id, p.id_proveedor, prov.razonsocial, p.fecha,
            p.vencimiento, prov.id_cuentas, px.nombre,cab.id_documento_bodega as id_bodega,det.subtotal as precio,
            px.iva, prov.direccion, p.id_proveedor, p.id_empresa, p.pedido, b.nombre as bodega, prov.nombrecomercial
            FROM inv_det_movimientos det
            JOIN inv_cab_movimientos cab ON det.id_inv_cab_movimientos =cab.id
            JOIN inv_documentos_bodegas doc ON cab.id_documento_bodega = doc.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN pedido AS p ON p.id = cab.id_pedido
            INNER JOIN proveedor AS prov ON prov.id = p.id_proveedor
            JOIN bodega b ON b.id =cab.id_bodega_origen
            WHERE   det.serie NOT IN
            (
                SELECT  d.serie
                FROM    detalle_pedido d
                INNER JOIN pedido AS p ON p.id = d.id_pedido
                WHERE  p.pedido='" . $pedido . "'
            )";
            if ($pedido != "") {
                $sql .= " AND p.pedido = '" . $pedido . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 3) {
            $sql = " SELECT px.codigo, d.cantidad, d.id_producto, d.serie, d.fecha_vence as fecha_vencimiento, d.lote, d.descuento as descuentop, p.id
                        , p.id_proveedor, prov.nombrecomercial, p.fecha, p.vencimiento, prov.id_cuentas, px.nombre, c.id_bodega_origen as id_bodega
                        , d.valor_unitario as precio, px.iva, prov.direccion, p.id_empresa, p.pedido, b.nombre as bodega
                        FROM inv_cab_movimientos c
                        JOIN inv_det_movimientos d ON c.id = d.id_inv_cab_movimientos
                        JOIN pedido p ON p.id = d.id_pedido
                        JOIN inv_documentos_bodegas doc ON c.id_documento_bodega = doc.id
                        JOIN proveedor AS prov ON prov.id = p.id_proveedor
                        JOIN producto px ON px.id = d.id_producto
                        JOIN bodega b ON b.id = c.id_bodega_origen
                        LEFT JOIN detalle_pedido xz ON px.id=xz.id_producto and p.id = xz.id_pedido
                        WHERE c.estado = 1
                        AND xz.id IS NULL
                        AND p.id_proveedor = '$idproveedor'
                        AND doc.abreviatura_documento = 'EGP'
                        AND p.fecha BETWEEN '$fdesde' AND '$fhasta' ";
            if ($val_series != "") {
                $sql .= " AND d.serie NOT IN ( $val_series ) ";
            }
        }
        $productos = DB::select(DB::raw($sql));

        if (isset($productos[0]->id) and $proveedor == null) {
            $proveedor = Proveedor::find($productos[0]->id_proveedor);
        }
        if ($objpedido == null && isset($productos[0]->pedido)) {
            $objpedido = Pedido::find($productos[0]->id);
        }
        // dd($pedido);
        $bodegas = Bodega::where('estado', '1')->get();
        return view('insumos/ingreso/details', [
            'numero_pedido' => $pedido, 'bodegas'      => $bodegas,
            'productos'                                             => $productos, 'proveedor' => $proveedor, 'objpedido' => $objpedido
        ]);
    }
    public function conglomeradaAnterior(Request $request)
    {
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::all();
        return view('insumos/ingreso/master_anterior', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }
    public function detailsConglomeradaAnterior(Request $request)
    {
        $serie       = $request['serie'];
        $series      = $request['series'];
        $pedido      = $request['pedido'];
        $codigo      = $request['codigo'];
        $lote        = $request['lote'];
        $tipo        = $request['tipo'];
        $idproveedor = $request['proveedor'];
        $fdesde      = $request['desde'];
        $fhasta      = $request['hasta'];
        $ds          = DB::table('pedido')->where('pedido', $pedido)->first();
        $proveedor   = null;
        $objpedido   = null;
        if (isset($ds->id)) {
            $proveedor = Proveedor::find($ds->id_proveedor);
        }
        $val_series = "";
        $separador  = "";
        if (!empty($series)) {
            foreach ($series as $row) {
                $val_series .= "$separador '$row'";
                $separador = ",";
            }
        }

        $productos = array();
        if ($tipo == 1) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vencimiento,det.lote,
            det.descuento as descuentop, cab.id, cab.id_proveedor, prov.razonsocial, cab.fecha,
            cab.vencimiento, prov.id_cuentas, px.nombre,det.precio,
            px.iva, prov.direccion, cab.id_proveedor, cab.id_empresa, cab.pedido, b.nombre as bodega, prov.nombrecomercial,
            cab.id_bodega, prov.nombrecomercial, mp.id as id_movimiento_paciente
            FROM movimiento det
            JOIN pedido cab ON det.id_pedido =cab.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN proveedor AS prov ON prov.id = cab.id_proveedor
            LEFT JOIN bodega b ON b.id =cab.id_bodega
            LEFT JOIN movimiento_paciente mp ON mp.id_movimiento = det.id
            WHERE cab.tipo = 1 AND det.cantidad > 0 ";
            if ($serie != "") {
                $sql .= " AND det.serie = '" . $serie . "' ";
            }
            if ($codigo != "") {
                $sql .= " AND px.codigo = '" . $codigo . "' ";
            }
            if ($lote != "") {
                $sql .= " AND det.lote = '" . $lote . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 2) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vencimiento ,det.lote,
            det.descuento as descuentop, cab.id, cab.id_proveedor, prov.razonsocial, cab.fecha,
            cab.vencimiento, prov.id_cuentas, px.nombre, det.precio,
            px.iva, prov.direccion,cab.id_proveedor, cab.id_empresa, cab.pedido, b.nombre as bodega, prov.nombrecomercial,
            cab.id_bodega
            FROM movimiento det
            JOIN pedido cab ON det.id_pedido =cab.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN proveedor AS prov ON prov.id = cab.id_proveedor
            LEFT JOIN bodega b ON b.id =cab.id_bodega
            WHERE det.serie NOT IN
            (
                SELECT  d.serie
                FROM    detalle_pedido d
                INNER JOIN pedido AS p ON p.id = d.id_pedido
                WHERE  p.pedido='$pedido'
            ) AND det.cantidad > 0";

            if ($pedido != "") {
                $sql .= " AND cab.pedido = '" . $pedido . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 3) {
            $sql = " SELECT px.codigo, d.cantidad, d.id_producto, d.serie, d.fecha_vencimiento, d.lote, d.descuento as descuentop, c.id
                , c.id_proveedor, prov.nombrecomercial, c.fecha, c.vencimiento, prov.id_cuentas, px.nombre, c.id_bodega
                , d.precio, px.iva, prov.direccion, c.id_empresa, c.pedido, b.nombre as bodega, mp.id, prov.razonsocial,
                c.id_bodega
                FROM movimiento_paciente mp
                JOIN movimiento d ON mp.id_movimiento = d.id
                JOIN pedido c ON d.id_pedido =c.id
                JOIN proveedor AS prov ON prov.id = c.id_proveedor
                JOIN producto px ON px.id = d.id_producto
                JOIN bodega b ON b.id = c.id_bodega
                LEFT JOIN detalle_pedido xz ON px.id=xz.id_producto and c.id = xz.id_pedido
                WHERE xz.id IS NULL
                AND c.fecha BETWEEN '$fdesde' AND '$fhasta' AND d.cantidad > 0";
            if ($val_series != "") {
                $sql .= " AND d.serie NOT IN ( $val_series ) ";
            }
        }
        $productos = DB::select(DB::raw($sql));
        // if (Auth::user()->id == '0957258056'){
        //     dd($sql);
        // }


        if (isset($productos[0]->id) and $proveedor == null) {
            $proveedor = Proveedor::find($productos[0]->id_proveedor);
        }
        if ($objpedido == null && isset($productos[0]->id)) {
            $objpedido = Pedido::find($productos[0]->id);
        }
        $bodegas = Bodega::where('estado', '1')->get();
        return view('insumos/ingreso/details', [
            'numero_pedido' => $pedido, 'bodegas'      => $bodegas,
            'productos'                                             => $productos, 'proveedor' => $proveedor, 'objpedido' => $objpedido, 'conglomerada' => 1, 'anterior' => 1
        ]);
    }
    public function store_new(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        //dd($request->all());
        DB::beginTransaction();

        try {
            $input = [
                'id_proveedor'    => $request['id_proveedor'],
                'pedido'          => $request['num_factura'],
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
                'id_empresa'      => Session::get('id_empresa'),
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            //$this->validatePedido($request);

            $id_pedido = Pedido::insertGetId($input);

            for ($x = 0; $x < count($request['cantidad']); $x++) {
                $codeP       = Producto::find($request['id'][$x]);
                $pedido_find = Pedido::find($id_pedido);
                if (isset($pedido_find->id)) {
                    Detalle_Pedido::create([
                        'id_pedido'       => $pedido_find->id,
                        'cantidad'        => trim($request['cantidad'][$x]),
                        'total'           => trim($request['precio'][$x]),
                        'id_producto'     => trim($request['id'][$x]),
                        'serie'           => trim($request['serie'][$x]),
                        'estado'          => '1',
                        'observacion'     => 'Ingreso de pedido por factura',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
                    $input2 = [
                        'id_producto'       => $request['id'][$x],
                        'cantidad'          => trim($request['cantidad'][$x]),
                        'id_encargado'      => $idusuario,
                        'serie'             => trim($request['serie'][$x]),
                        'id_bodega'         => trim($request['bodega'][$x]),
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento'][$x],
                        'lote'              => $request['lote'][$x],
                        'usos'              => $codeP->usos,
                        'descuentop'        => $request['pDescuento'][$x],
                        'descuento'         => $request['descuento'][$x],
                        'precio'            => $request['precio'][$x],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => '0',
                    ];
                    $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                    $id_producto       = $request['id'][$x];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $precio_uni   = str_replace(",", "", $request['precio'][$x]);
                    $precio_total = $request['cantidad'][$x] * $precio_uni;
                    /*Log_Detalle_Pedido::create([
                    'id_pedido'=>$pedido_find->id,
                    'id_pedidon'=>$id_pedido,
                    'concepto'=>'LOG DE ENTRADA '.date('Y-m-d'),
                    'valor'=>$precio_total,
                    'valor_ant'=>$pedido_find->total,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    ]); */
                    Log_movimiento::create([
                        'id_producto'     => $request['id'][$x],
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

            Inventario::consigna($id_pedido, Session::get('id_empresa'));

            DB::commit();
            return response()->json('Guardado correctamente');
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function update_conglomerada(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        //dd($request->all());
        DB::beginTransaction();

        try {
            $input = [
                'id_proveedor'    => $request['id_proveedor'],
                'pedido'          => $request['num_factura'],
                'factura'         => $request['num_factura'],
                'fecha'           => $request['fecha'],
                'vencimiento'     => $request['vencimiento'],
                'id_empresa'      => $request['id_empresa'],
                'observaciones'   => $request['observaciones'],
                'subtotal_12'     => $request['subtotal_12'],
                'subtotal_0'      => $request['subtotal_0'],
                'descuento'       => $request['descuentx'],
                'iva'             => $request['iva'],
                'total'           => $request['total'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            //$this->validatePedido($request);
            $id_pedido   = $request['id_pedido'];
            $pedido_find = Pedido::find($id_pedido);
            Pedido::where('id', $request['id_pedido'])->update($input);

            /* BORRO LOS DETALLE Y SE VUELVEN A CREAR */
            $log_detalle_pedido = Log_Detalle_Pedido::where('id_pedidon', $request['id_pedido'])->delete();
            $detalle_pedido     = Detalle_Pedido::where('id_pedido', $request['id_pedido'])->delete();
            $movimientos        = Movimiento::where('id_pedido', $request['id_pedido'])->get();
            foreach ($movimientos as $value) {
                Log_movimiento::where('id_movimiento', $value->id)->delete();
                $movimiento = Movimiento::find($value->id);
                $movimiento->delete();
            }
            for ($x = 0; $x < count($request['cantidad']); $x++) {
                $codeP = Producto::find($request['id'][$x]);
                Detalle_Pedido::create([
                    'id_pedido'       => $pedido_find->id,
                    'cantidad'        => $request['cantidad'][$x],
                    'total'           => '0',
                    'id_producto'     => $request['id'][$x],
                    'serie'           => $request['serie'][$x],
                    'estado'          => '1',
                    'observacion'     => 'Ingreso de pedido por factura',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $input2 = [
                    'id_producto'       => $request['id'][$x],
                    'cantidad'          => $request['cantidad'][$x],
                    'id_encargado'      => $idusuario,
                    'serie'             => $request['serie'][$x],
                    'id_bodega'         => $request['bodega'][$x],
                    'tipo'              => '1',
                    'fecha_vencimiento' => $request['fecha_vencimiento'][$x],
                    'lote'              => $request['lote'][$x],
                    'usos'              => $codeP->usos,
                    'descuentop'        => $request['pDescuento'][$x],
                    'descuento'         => $request['descuento'][$x],
                    'precio'            => $request['precio'][$x],
                    'id_pedido'         => $id_pedido,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'consecion_det'     => '0',

                ];
                $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                $id_producto       = $request['id'][$x];
                $producto          = Producto::find($id_producto);
                $cantidad_producto = $producto->cantidad;
                $nueva_cantidad    = $cantidad_producto + 1;

                $input3 = [
                    'cantidad'        => $nueva_cantidad,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $precio_total = $request['cantidad'][$x] * $request['precio'][$x];
                Log_Detalle_Pedido::create([
                    'id_pedido'      => $pedido_find->id,
                    'id_pedidon'     => $id_pedido,
                    'concepto'       => 'LOG DE ENTRADA ' . date('Y-m-d'),
                    'valor'          => $precio_total,
                    'valor_ant'      => $pedido_find->total,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                ]);
                Log_movimiento::create([
                    'id_producto'     => $request['id'][$x],
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

            DB::commit();
            return response()->json('Guardado correctamente');
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function editar_conglomerada($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido = pedido::findOrFail($id);

        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();

        $empresa = Empresa::all();

        /***************************/

        $cantidad_pedido = DB::table('movimiento as m')
            ->where('m.id_pedido', $id)
            ->join('pedido as p', 'p.id', 'm.id_pedido')
            ->join('producto as  pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('m.serie')
            ->select(
                'm.id',
                'pro.codigo as codigo',
                'pro.nombre as nombre_producto',
                DB::raw('m.cantidad, m.serie'),
                'm.serie',
                'b.nombre as nombre_bodega',
                'm.lote',
                'm.fecha_vencimiento',
                'pro.usos',
                'm.precio',
                'm.descuentop',
                'm.descuento',
                'pro.iva',
                'pro.id as id_producto',
                'pro.registro_sanitario',
                'm.consecion_det',
                'p.id_proveedor'
            )
            ->get();

        /***************************/
        $vence      = $pedido->vencimiento;
        $documentos = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->get();
        $bod_princ  = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        return view('insumos/ingreso/master', [
            'bodegas' => $bodegas, 'proveedores'    => $proveedores, 'empresa'        => $empresa,
            'pedido'                                         => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ
        ]);
    }

    public function crear_orden_conglomerada(Request $request)
    {
        // dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido                     = pedido::findOrFail($request->id_pedido);
        $pedido->orden_conglomerada = 1;
        $pedido->save();
    }

    public function anular_envio_orden(Request $request)
    {
        // dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido                     = pedido::findOrFail($request->id_pedido);
        $pedido->orden_conglomerada = 0;
        $pedido->save();
    }

    public function eliminar_conglomerada($id_pedido)
    {
        $pedido   = Pedido::find($id_pedido);
        $eliminar = null;
        if (!is_null($pedido)) {
            if ($pedido->estado_contable == 1) {
                $eliminar = 1;
            }
        }
        return view('insumos/ingreso/eliminar_conglomerada', ['eliminar' => $eliminar, 'id' => $id_pedido]);
    }

    public function eliminar_conglomerada_clave(Request $request)
    {
        DB::beginTransaction();
        try {
            $idusuario  = Auth::user()->id;
            $ingresada      = $request['password'];
            $id             = $request['id'];
            $hashedPassword = Auth::user()->password;
            $pedido_v       = Pedido::find($id);
            if (!is_null($pedido_v)) {
                if (Hash::check($ingresada, $hashedPassword) && $pedido_v->estado_contable == 0) {
                    $productos = Movimiento::where('id_pedido', $id)->get();
                    $detalles = Detalle_Pedido::where('id_pedido', $id)->get();
                    foreach ($detalles as $row) {
                        $detalle = Detalle_Pedido::find($row->id);
                        $detalle->delete();
                    }
                    foreach ($productos as $value) {
                        Log_movimiento::where('id_movimiento', $value->id)->delete();
                        $movimiento = Movimiento::find($value->id);
                        $movimiento->delete();
                    }
                    // $this->anularIngPedido($id);
                    $pedido = Pedido::find($id);
                    if ($pedido->orden_conglomerada == 1) {
                        $pedido->forceDelete();
                    } else {
                        $pedido->delete();
                    }
                    DB::commit();
                    return "okay";
                }
            }
            // DB::rollBack();
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function validarNumeroPedido(Request $request)
    {
        $pedido = Pedido::where("id_proveedor", $request->id_proveedor)->where('pedido', trim($request->pedido))->where("deleted_at", null)->first();
        if (!is_null($pedido)) {
            return ["status" => "error", "msj" => "Existe un pedido con el mismo numero y el mismo proveedor activo"];
        }

        return ["status" => "success", "msj" => "Puede guardar"];

        // dd($pedido);
    }
    public function conglomerada_nueva(Request $request)
    {
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->get();
        $doc_bodega = InvDocumentosBodegas::all();

        return view('insumos/ingreso/master_nuevo', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'doc_bodega' => $doc_bodega,]);
    }
    public function guardar_conglomerada_nueva(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_user = $_SERVER["REMOTE_ADDR"];
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['findfechadesde'] . ' 00:00:00';
        $fecha_hasta = $request['findfechahasta'] . ' 23:59:59';

        try {
            $documentoBodega = InvDocumentosBodegas::where('abreviatura_documento', 'ICG')->first();
            if ($documentoBodega == '') {
                return ['error' => 'No existe el documento INGRESO FACTURA CONGLOMERADA, por favor contactese con sistema'];
            }
            if ($fecha_desde < $fecha_hasta) {
                $pedidoNuevo = $request['pedido'];
                $productos = $this->busqueda($request);
                //dd($productos);
                $cant = count($productos);
                $pedidoExiste = Pedido::where('pedido', $pedidoNuevo)->first();
                $id = '';
                if ($pedidoExiste == '') {
                    if ($cant > 0) {
                        DB::beginTransaction();
                        $pedido_cabecera = [
                            'id_proveedor' => $request['id_proveedor'],
                            'pedido' => $pedidoNuevo,
                            'tipo' => $documentoBodega->id,
                            'factura' => $request['num_factura'],
                            'fecha' => $request['fecha'],
                            'vencimiento' => $request['vencimiento'],
                            'observaciones' => $request['observaciones'],
                            'id_empresa' => $id_empresa,
                            //'id_bodega' => $request['id_bodega'],
                            //'total' => $request['total'],
                            // 'subtotal_0' => null,
                            // 'subtotal_12' => null,
                            // 'iva' => null,
                            //'descuento' => $request['descuento'],
                            // 'consecion' => null,
                            // 'estado_compra' => null,
                            'orden_conglomerada' => 1,
                            'estado_contable' => 0,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_user,
                            'ip_modificacion' => $ip_user,
                        ];
                        $id = PrePedido::insertGetId($pedido_cabecera);
                        for ($i = 0; $i < $cant; $i++) {
                            //$bodega  = Bodega::where('estado', '1')->where('nombre', $productos[$i]->nombre_bodega)->first();
                            $iva = 0;
                            $subtotal_12 = 0;
                            $subtotal_0 = 0;
                            $precio = 0;
                            //$idm = InvDetMovimientos::where('id_pedido', $productos[$i]->id_pedido)->where('serie', $productos[$i]->serie)->orderBy('id', 'asc')->first();
                            $idm = InvDetMovimientos::where('id_pedido', $productos[$i]->id_pedido)->where('serie', $productos[$i]->serie)->orderBy('id', 'asc')->first();
                            if (!is_null($idm)) {
                                $precio = $idm->valor_unitario;
                                if (!is_null($idm->cantidad_usos_Kardex)) {
                                    $kardex = InvKardexNuevo::where('serie', $idm->serie)->where('tipo', 'E')->first();
                                    if (!is_null($kardex)) {
                                        $precio = $kardex->valor_unitario;
                                    }
                                }
                            } else {
                                $precio = $productos[$i]->valor_unitario;
                            }

                            if ($productos[$i]->iva2 == 1) {
                                $subtotal_12 = ($productos[$i]->cant * $precio) - $productos[$i]->descuento;
                                $p_iva = 0.12;
                                //$ct_conf = Ct_Configuraciones::where('tipo', 'FACTCONTABLE_IVA -4.1.01.02-')->where('id_empresa', $id_empresa)->first();
                                $ct_conf = Ct_Configuraciones::find(3);

                                if (!is_null($ct_conf)) {
                                    $p_iva = $ct_conf->iva;
                                }

                                $iva = $subtotal_12 * $p_iva;
                            } else {
                                $subtotal_0 = ($productos[$i]->cant * $precio) - $productos[$i]->descuento;
                            }

                            $movimiento = [
                                'id_producto' => $productos[$i]->id_producto,
                                'cantidad' => $productos[$i]->cant,
                                'serie' => $productos[$i]->serie,
                                'id_bodega' => $productos[$i]->id_bodega,
                                'id_pre_pedido' => $id,
                                'id_pedido_original' => $productos[$i]->id_pedido,
                                'estado' => 1,
                                'tipo' => 1,
                                'fecha_vencimiento' => $request['vencimiento'],
                                'id_encargado' => $idusuario,
                                'usos' => $productos[$i]->cant_uso,
                                'lote' => $productos[$i]->lote,
                                'precio' => round($precio, 4),
                                'descuento' => round($productos[$i]->descuento, 4),
                                'subtotal_0' => round($subtotal_0, 4),
                                'subtotal_12' => round($subtotal_12, 4),
                                'iva' => round($iva, 4),
                                // 'check_iva' => $productos[$i]->iva2,
                                // 'descuentop' => $productos[$i],
                                // 'consecion_det' => $productos[$i],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_user,
                                'ip_modificacion' => $ip_user,
                            ];
                            //dd($movimiento);
                            PreMovimiento::create($movimiento);
                        }
                        DB::commit();
                        $this->recalcular($id);
                        return ['id' => $id];
                    } else {
                        return ['error' => "No hay productos en esta consulta"];
                    }
                } else {
                    return ['error' => "Pedido ya registrado"];
                }
            } else {
                return ['error' => "La fecha hasta no puede ser menor a la fecha desde"];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function guardar_conglomerada_nueva_consumibles(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_user = $_SERVER["REMOTE_ADDR"];
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['findfechadesde'] . ' 00:00:00';
        $fecha_hasta = $request['findfechahasta'] . ' 23:59:59';

        try {
            $documentoBodega = InvDocumentosBodegas::where('abreviatura_documento', 'ICG')->first();
            if ($documentoBodega == '') {
                return ['error' => 'No existe el documento INGRESO FACTURA CONGLOMERADA, por favor contactese con sistema'];
            }
            if ($fecha_desde < $fecha_hasta) {
                $pedidoNuevo = $request['pedido'];
                $productos = $this->busqueda_consumibles_vt($request);
                //dd($productos);
                $cant = count($productos);
                $pedidoExiste = Pedido::where('pedido', $pedidoNuevo)->first();
                $id = '';
                if ($pedidoExiste == '') {
                    if ($cant > 0) {
                        DB::beginTransaction();
                        $pedido_cabecera = [
                            'id_proveedor' => $request['id_proveedor'],
                            'pedido' => $pedidoNuevo,
                            'tipo' => $documentoBodega->id,
                            'factura' => $request['num_factura'],
                            'fecha' => $request['fecha'],
                            'vencimiento' => $request['vencimiento'],
                            'observaciones' => $request['observaciones'],
                            'id_empresa' => $id_empresa,
                            //'id_bodega' => $request['id_bodega'],
                            //'total' => $request['total'],
                            // 'subtotal_0' => null,
                            // 'subtotal_12' => null,
                            // 'iva' => null,
                            //'descuento' => $request['descuento'],
                            // 'consecion' => null,
                            // 'estado_compra' => null,
                            'orden_conglomerada' => 1,
                            'estado_contable' => 0,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_user,
                            'ip_modificacion' => $ip_user,
                        ];
                        $id = PrePedido::insertGetId($pedido_cabecera);
                        for ($i = 0; $i < $cant; $i++) {
                            //$bodega  = Bodega::where('estado', '1')->where('nombre', $productos[$i]->nombre_bodega)->first();
                            $iva = 0;
                            $subtotal_12 = 0;
                            $subtotal_0 = 0;
                            $precio = 0;
                            $idm = InvDetMovimientos::where('id_pedido', $productos[$i]->id_pedido)->where('serie', $productos[$i]->serie)->orderBy('id', 'asc')->first();
                            if (!is_null($idm)) {
                                $precio = $idm->valor_unitario;
                                if (!is_null($idm->cantidad_usos_Kardex)) {
                                    $kardex = InvKardexNuevo::where('serie', $idm->serie)->where('tipo', 'E')->first();
                                    if (!is_null($kardex)) {
                                        $precio = $kardex->valor_unitario;
                                    }
                                }
                            } else {
                                $precio = $productos[$i]->valor_unitario;
                            }

                            if ($productos[$i]->iva2 == 1) {
                                $subtotal_12 = ($productos[$i]->cant * $precio) - $productos[$i]->descuento;
                                $p_iva = 0.12;
                                //$ct_conf = Ct_Configuraciones::where('tipo', 'FACTCONTABLE_IVA -4.1.01.02-')->where('id_empresa', $id_empresa)->first();
                                $ct_conf = Ct_Configuraciones::find(3);

                                if (!is_null($ct_conf)) {
                                    $p_iva = $ct_conf->iva;
                                }

                                $iva = $subtotal_12 * $p_iva;
                            } else {
                                $subtotal_0 = ($productos[$i]->cant * $precio) - $productos[$i]->descuento;
                            }

                            $movimiento = [
                                'id_producto' => $productos[$i]->id_producto,
                                'cantidad' => $productos[$i]->cant,
                                'serie' => $productos[$i]->serie,
                                'id_bodega' => $productos[$i]->id_bodega,
                                'id_pre_pedido' => $id,
                                'id_pedido_original' => $productos[$i]->id_pedido,
                                'estado' => 1,
                                'tipo' => 1,
                                'fecha_vencimiento' => $request['vencimiento'],
                                'id_encargado' => $idusuario,
                                'usos' => $productos[$i]->cant_uso,
                                'lote' => $productos[$i]->lote,
                                'precio' => round($precio, 4),
                                'descuento' => round($productos[$i]->descuento, 4),
                                'subtotal_0' => round($subtotal_0, 4),
                                'subtotal_12' => round($subtotal_12, 4),
                                'iva' => round($iva, 4),
                                // 'check_iva' => $productos[$i]->iva2,
                                // 'descuentop' => $productos[$i],
                                // 'consecion_det' => $productos[$i],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_user,
                                'ip_modificacion' => $ip_user,
                            ];
                            //dd($movimiento);
                            PreMovimiento::create($movimiento);
                        }
                        DB::commit();
                        $this->recalcular($id);
                        return ['id' => $id];
                    } else {
                        return ['error' => "No hay productos en esta consulta"];
                    }
                } else {
                    return ['error' => "Pedido ya registrado"];
                }
            } else {
                return ['error' => "La fecha hasta no puede ser menor a la fecha desde"];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }


    public function editar_conglomerada_nueva($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido = PrePedido::findOrFail($id);

        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();

        $empresa = Empresa::all();

        /***************************/

        $cantidad_pedido = DB::table('pre_movimiento as m')
            ->where('m.id_pre_pedido', $id)
            ->join('pre_pedido as p', 'p.id', 'm.id_pre_pedido')
            ->join('producto as  pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('m.serie')
            ->select(
                'm.id',
                'pro.codigo as codigo',
                'pro.nombre as nombre_producto',
                'm.id_pedido_original',
                DB::raw('m.cantidad, m.serie'),
                'm.serie',
                'b.nombre as nombre_bodega',
                'm.lote',
                'm.fecha_vencimiento',
                'pro.usos',
                'm.precio',
                'm.descuentop',
                'm.descuento',
                'pro.iva',
                'pro.id as id_producto',
                'pro.registro_sanitario',
                'm.consecion_det',
                'p.id_proveedor'
            )
            ->get();

        /***************************/
        $documentos = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->get();
        $bod_princ  = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        // return redirect()->route('ingreso.editar_conglomerada_nueva',['id' => $id,'bodegas' => $bodegas, 'proveedores'    => $proveedores, 'empresa'        => $empresa,
        // 'pedido' => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ]); 
        return view('insumos/ingreso/master_nuevo', [
            'bodegas' => $bodegas, 'proveedores'    => $proveedores, 'empresa'        => $empresa,
            'pedido' => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ
        ]);
    }

    public static function busqueda($request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('hc_procedimientos as hp', 'hp.id', 'idm.id_procedimiento')
            ->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('planilla_detalle as pd', 'pd.id_movimiento_paciente', 'mp.id')
            ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
            ->whereNull('p1.cantidad_usos_kardex')
            ->where('p.id_empresa', $id_empresa)
            ->where('ik.id_empresa', $id_empresa)
            ->where('pd.check', '1')
            ->where('pd.estado', '1')
            ->groupby('pd.id_movimiento_paciente')
            ->select(
                'pd.id_movimiento_paciente as id_movimiento_paciente',
                'p1.cantidad_usos_kardex',
                'ik.valor_unitario as precio_k',
                'p1.codigo',
                'p.id as id_pedido',
                'b.nombre as nombre_bodega',
                'p1.nombre as nombre_producto',
                'prov.nombrecomercial',
                'a.fechaini',
                'u.nombre1',
                'u.nombre2',
                'u.apellido1',
                'u.apellido2',
                'm.nombre as nombre_m',
                'ik.descripcion',
                'ik.referencia',
                'idm.cantidad as cant',
                'idm.lote',
                'p.vencimiento',
                'p.pedido',
                'idm.serie',
                'idm.total',
                'idb.documento',
                'hp.id as hc_id_procedimiento',
                'hp.id_doctor_examinador',
                'idm.valor_unitario',
                'idm.descuento',
                'p.tipo as tipo_pedido',
                'idm.cant_uso',
                'idm.iva',
                'p1.iva as iva2',
                'p1.codigo',
                'b.id as id_bodega',
                'p1.id as id_producto',
                'prov.id as id_proveedor'
            );
        if (count($request->all()) > 0) {
            if (!is_null($request->tipo)) {
                $productos = $productos->where("p.tipo", $request->tipo);
            }
            if (!is_null($request->id_proveedor)) {
                $productos = $productos->where("p.id_proveedor", $request->id_proveedor);
            }
            if (!is_null($request->findfechadesde)) {
                $productos = $productos->where('a.fechaini', ">=", "{$request->findfechadesde} 00:00:00");
            }

            if (!is_null($request->findfechahasta)) {
                $productos = $productos->where('a.fechaini', "<=", "{$request->findfechahasta} 23:59:59");
            }
        }
        // dd($productos->get());
        return $productos->get();
    }

    public static function busqueda_agrupada($request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('hc_procedimientos as hp', 'hp.id', 'idm.id_procedimiento')
            ->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('planilla_detalle as pd', 'pd.id_movimiento_paciente', 'mp.id')
            ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
            ->where('p.id_empresa', $id_empresa)
            ->where('ik.id_empresa', $id_empresa)
            ->where('m.nombre', '!=', 'ER')
            ->selectRaw('MAX(idm.id) as idm_id')
            ->whereIn('pd.id_movimiento_paciente', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('planilla_detalle')
                    ->groupBy('id_movimiento_paciente');
            });
        if (!is_null($request->tipo)) {
            $productos = $productos->where("p.tipo", $request->tipo);
        }
        if (!is_null($request->id_proveedor)) {
            $productos = $productos->where("p.id_proveedor", $request->id_proveedor);
        }
        if (!is_null($request->findfechadesde)) {
            $productos = $productos->where('a.fechaini', ">=", "{$request->findfechadesde} 00:00:00");
        }

        if (!is_null($request->findfechahasta)) {
            $productos = $productos->where('a.fechaini', "<=", "{$request->findfechahasta} 23:59:59");
        }

        $productos = $productos->select(DB::raw('SUM(idm.cantidad) as cant'), 'idm.cantidad_usos_kardex', 'pd.id_movimiento_paciente', 'idm.serie', 'idm.id_pedido', 'idm.valor_unitario', 'ik.total as precio_k', 'a.fechaini', 'ik.descripcion', 'idm.cantidad',  'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.total', 'idb.documento', 'p.id as id_pedido', 'ik.referencia', 'prov.id as id_proveedor', 'p.tipo as tipo_pedido', 'idm.descuento', 'idm.cant_uso', 'idm.iva', 'p1.iva as iva2', 'p1.codigo', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'b.id as id_bodega', 'p1.id as id_producto', 'u.apellido2', 'm.nombre as nombre_m')
            ->groupBy('idm.serie', 'idm.id_pedido', 'ik.total')
            ->orderBy('idm.serie', 'idm.id_pedido', 'ik.total');
        return $productos->get();
    }
    //VT CONSUMIBLES PRE FACTURA CONGLOMERADA
    public function busqueda_consumibles_vt(Request $request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'ik.id_documento_bodega')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->where('idb.abreviatura_documento', 'EVC')
            ->where('m.nombre', '!=', 'ER');

        if (!is_null($request->tipo)) {
            //$productos = $productos->where("p.tipo", $request->tipo);
        }
        if (!is_null($request->id_proveedor)) {
            $productos = $productos->where("p.id_proveedor", $request->id_proveedor);
        }
        if (!is_null($request->findfechadesde)) {
            $productos = $productos->whereBetween('ik.fecha', [$request->findfechadesde . ' 00:00:00', $request->findfechahasta . ' 23:59:59']);
        } else {
            $productos = $productos->where('ik.fecha', '<=', $request->findfechahasta);
        }

        $productos = $productos->select(DB::raw('SUM(idm.cantidad) as cant'), 'idm.cantidad_usos_kardex', 'idm.serie', 'idm.id_pedido', 'idm.valor_unitario', 'ik.total as precio_k', 'ik.fecha', 'ik.descripcion', 'idm.cantidad',  'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.total', 'idb.documento', 'p.id as id_pedido', 'ik.referencia', 'prov.id as id_proveedor', 'p.tipo as tipo_pedido', 'idm.descuento', 'idm.cant_uso', 'idm.iva', 'p1.iva as iva2', 'p1.codigo', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'b.id as id_bodega', 'p1.id as id_producto', 'm.nombre as nombre_m')
            ->groupBy('idm.serie', 'idm.id_pedido', 'ik.total')
            ->orderBy('idm.serie', 'idm.id_pedido', 'ik.total');

        //dd($request->all(),$productos->toSql());    
        return $productos->get();
    }

    public function recalcular($id)
    {
        // dd($id);
        $pedido = PrePedido::find($id);
        $detalles = $pedido->detalle;
        $total = 0;
        $subtotal_0 = 0;
        $subtotal_12 = 0;
        $iva = 0;
        $descuento = 0;
        //dd($detalles);
        foreach ($detalles as $detalle) {
            // $idm = InvDetMovimientos::where('id_pedido',$detalle->id_pedido)->where('serie',$detalle->serie)->orderBy('id','asc')->first();

            // if(!is_null($idm)){
            //     $precio = $idm->valor_unitario;
            //     if(!is_null($idm->cantidad_usos_Kardex)){
            //         $kardex = InvKardexNuevo::where('serie',$idm->serie)->where('tipo','E')->first();
            //         if(!is_null($kardex)){
            //             $precio = $kardex->valor_unitario;
            //         }
            //     }
            // }
            // else{
            //     $precio = $detalle->valor_unitario;
            // }
            if ($detalle->iva > 0) {
                $subtotal_12 += ($detalle->cantidad * $detalle->precio) - $detalle->descuento;
            } else {
                $subtotal_0 += ($detalle->cantidad * $detalle->precio) - $detalle->descuento;
            }
            $iva += $detalle->iva;
            $total += ($detalle->cantidad * $detalle->precio) - $detalle->descuento + $detalle->iva;
            $descuento += $detalle->descuento;
        }
        $pedido->update([
            'total' => round($total, 2),
            'subtotal_0' => round($subtotal_0, 2),
            'subtotal_12' => round($subtotal_12, 2),
            'iva' => round($iva, 2),
            'descuento' => round($descuento, 2),
        ]);
    }
    public function nvo_cgl_actualizar_item(Request $request)
    {
        $id_movimiento = $request->id_mov;
        $cantidad = $request->cantidad;
        $movimiento = PreMovimiento::find($id_movimiento);
        if (!is_null($movimiento)) {
            $movimiento->update([
                'cantidad' => $cantidad,
            ]);
        }
        $this->recalcular($movimiento->pre_pedido->id);
    }
    public function nvo_cgl_eliminar_item(Request $request)
    {
        $id_movimiento = $request->id_mov;
        $movimiento = PreMovimiento::find($id_movimiento);
        $id_pedido = $movimiento->pre_pedido->id;
        if (!is_null($movimiento)) {
            $movimiento->delete();
        }
        $this->recalcular($id_pedido);
    }
    public function guardar_pre_pedido(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_user = $_SERVER["REMOTE_ADDR"];
        try {
            DB::beginTransaction();
            $idPrePedido = $request['idPedido'];
            $prepedido = PrePedido::find($idPrePedido);
            //dd($prepedido);
            $pedido = Pedido::where('id_pre_pedido', $idPrePedido)->first();
            if (is_null($pedido)) {
                if (!is_null($prepedido)) {
                    $id = '';
                    $pedido_cabecera = [
                        'id_proveedor' => $prepedido->id_proveedor,
                        'id_pre_pedido' => $idPrePedido,
                        'pedido' => $prepedido->pedido,
                        'tipo' => $prepedido->tipo,
                        'factura' => $prepedido->factura,
                        'fecha' => $prepedido->fecha,
                        'vencimiento' => $prepedido->vencimiento,
                        'observaciones' => $prepedido->observaciones,
                        'id_empresa' => $prepedido->id_empresa,
                        'id_bodega' => $prepedido->id_bodega,
                        'total' => $prepedido->total,
                        'subtotal_0' => $prepedido->subtotal_0,
                        'subtotal_12' => $prepedido->subtotal_12,
                        'iva' => $prepedido->iva,
                        'descuento' => $prepedido->descuento,
                        'consecion' => $prepedido->consecion,
                        'estado_compras' => $prepedido->estado_compras,
                        'orden_conglomerada' => 1,
                        'estado_contable' => 0,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_user,
                        'ip_modificacion' => $ip_user,
                    ];
                    $id = Pedido::insertGetId($pedido_cabecera);
                    $premovimientos = PreMovimiento::where('id_pre_pedido', $idPrePedido)->get();
                    foreach ($premovimientos as $premovimiento) {
                        $movimiento = [
                            'id_producto' => $premovimiento->id_producto,
                            'cantidad' => $premovimiento->cantidad,
                            'serie' => $premovimiento->serie,
                            'id_bodega' => $premovimiento->id_bodega,
                            'id_pedido' => $id,
                            'estado' => 1,
                            'tipo' => 1,
                            'fecha_vencimiento' => $premovimiento->fecha_vencimiento,
                            'id_encargado' => $idusuario,
                            'usos' => $premovimiento->usos,
                            'lote' => $premovimiento->lote,
                            'precio' => $premovimiento->precio,
                            'iva' => $premovimiento->iva,
                            'descuento' => $premovimiento->descuento,
                            'subtotal_0' => $premovimiento->subtotal_0,
                            'subtotal_12' => $premovimiento->subtotal_12,
                            // 'descuentop' => $premovimiento,
                            // 'consecion_det' => $premovimiento,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_user,
                            'ip_modificacion' => $ip_user,
                        ];
                        Movimiento::create($movimiento);
                    }
                    DB::commit();
                    return ['id' => $id];
                } else {
                    return ['error' => 'Error al crear el pedido'];
                }
            } else {
                return ['error' => 'El pedido ya existe'];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    public function eliminar_clave_force(Request $request)
    {
        try {
            $ingresada      = $request['password'];
            $id             = $request['id'];
            $hashedPassword = Auth::user()->password;
            $pedido_v       = Pedido::find($id);
            if (!is_null($pedido_v)) {
                if (Hash::check($ingresada, $hashedPassword) && $pedido_v->estado_contable == 0) {
                    DB::beginTransaction();
                    $productos = Movimiento::where('id_pedido', $id)->get();
                    foreach ($productos as $value) {
                        Log_movimiento::where('id_movimiento', $value->id)->delete();
                        $producto           = Producto::find($value->id_producto);
                        $cantidad           = $producto->cantidad - 1;
                        $producto->cantidad = $cantidad;
                        $producto->save();
                        if ($pedido_v->orden_conglomerada == 1) {
                            $movimiento = Movimiento::find($value->id);
                            $movimiento->forceDelete();
                        } else {
                            $movimiento = Movimiento::find($value->id);
                            $movimiento->delete();
                        }
                    }
                    $this->anularIngPedido($id);
                    if ($pedido_v->orden_conglomerada == 1) {
                        $pedido = Pedido::find($id);
                        $pedido->forceDelete();
                    } else {
                        $pedido = Pedido::find($id);
                        $pedido->delete();
                    }
                    DB::commit();
                    return "okay";
                }
            } else {
                return "Pedido no existe";
            }
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function index_reverso()
    {
        return view('insumos/reverso_planilla/index_reverso');
    }
    public function reverso_planilla(Request $request)
    {

        $id_cab_mov = $request['id_cab_mov'];
        //dd($id_cab_mov);
        DB::beginTransaction();
        try {
            if (!is_null($id_cab_mov)) {
                DB::commit();
                InvCabMovimientos::anularTransaccionBodega($id_cab_mov);
                return  'Cabecera Reversada Correctamente';
            } else {
                return 'Error al Reversar la cabecera';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(),'linea' => $e->getLine()];
        }
    }

    public function recalcular_prepedido($id_prepedido){
        $prepedido = PrePedido::find($id_prepedido);
        $id_empresa = $prepedido->id_empresa;
        $idusuario = Auth::user()->id;
        $ip_user = $_SERVER["REMOTE_ADDR"];
        try {
                $productos = PreMovimiento::where('id_pre_pedido',$id_prepedido)->get();
                $cant = count($productos);
                $id = '';
                if ($cant > 0) {
                    DB::beginTransaction();
                    
                    $id = $prepedido->id;
                    foreach ($productos as $value) {
                        $iva = 0;
                        $subtotal_iva = 0;
                        $subtotal_0 = 0;
                        $precio = $value->precio;
                        $insumo = Producto::find($value->id_producto);
                        
                        if(!is_null($insumo)){
                            if($insumo->iva==1){
                                $p_iva = 0.12;
                                $subtotal_iva = ($value->cantidad * $precio) - $value->descuento;
                                $p_iva = $this->porIva->iva;
                                $iva = $subtotal_iva * $p_iva;
                            } else {
                                $subtotal_0 = ($value->cantidad * $precio) - $value->descuento;
                            }

                            $movimiento = [
                                'cantidad' => $value->cantidad,
                                'precio' => round($precio, 4),
                                'descuento' => round($value->descuento, 4),
                                'subtotal_0' => round($subtotal_0, 4),
                                'subtotal_12' => round($subtotal_iva, 4),
                                'iva' => round($iva, 4),
                                'id_usuariomod' => $idusuario,
                                'ip_modificacion' => $ip_user,
                            ];
                            $value->update($movimiento);
                        }
                        
                    }
                    DB::commit();
                    $this->recalcular($id);
                    return ['status' => 'success', 'msj' => 'Recalculado correctamente','id' => $id];
                } else {
                    return ['status' => 'error','msj' => "No hay productos en esta consulta"];
                }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function reverso_planilla_por_procedimiento($id_hc_proc)
    {

        $movimientos = InvCabMovimientos::where('id_hc_procedimientos',$id_hc_proc)->where('observacion','like','%EGP EGRESO DE PROCEDIMIENTOS PACIENTES%')->get();
        DB::beginTransaction();
        try {
            DB::commit();
            foreach($movimientos as $movimiento){
                InvCabMovimientos::anularTransaccionBodega($movimiento->id);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return 'Error al Reversar la cabecera';
        }  
        
        return "Listo Calixto";

    }
}
