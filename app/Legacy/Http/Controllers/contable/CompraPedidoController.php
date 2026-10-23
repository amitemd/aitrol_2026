<?php

namespace Sis_medico\Http\Controllers\contable;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Contable;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Detalle_Pedido;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_pedidos_Compra;
use Sis_medico\Ct_Proceso;
use Sis_medico\Ct_Proceso_Detalle;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Termino;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Usuario_Proceso;
use Sis_medico\Producto;
use Sis_medico\Inventario;
use Sis_medico\Ct_Inv_Interno;
use Sis_medico\Ct_Inv_Movimiento;
use Sis_medico\Ct_Inv_Kardex;
use Sis_medico\User;
use Sis_medico\Ct_Inv_Costos;
use Sis_medico\Ct_Inventario_Bodega;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Caja;


class CompraPedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $pedidos= Ct_pedidos_Compra::where('id_empresa',$id_empresa);
        if($request->id!=null){
            $pedidos= $pedidos->where('id',$request->id);
        }else{
            if($request->fecha!=null){
                $pedidos= $pedidos->where('fecha',$request->fecha);
            }
            if($request->id_proveedor!=null){
                $pedidos= $pedidos->where('id_proveedor',$request->id_proveedor);
            }
            if($request->concepto!=null){
                $pedidos= $pedidos->where('observacion','like','%'.$request->concepto.'%');
            }
        }
        $pedidos= $pedidos->orderBy('id','desc')->paginate(10);
        $permisos = Inventario::permitidos();
        //dd($permisos);
        return view('contable.compras_pedidos.index', ['permisos'=>$permisos, 'pedidos' => $pedidos, 'proveedor' => Proveedor::all(), 'tipo_comprobante' => ct_master_tipos::where('estado', '1')->where('tipo', '1')->get(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(),'request'=>$request
        ]);
    }

    public function proveedorsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('razonsocial', 'LIKE', '%' . $request['search'] . '%')->where('estado', '1')->select('proveedor.id as id', 'proveedor.razonsocial as text')->get();
        }

        return response()->json($proveedor);
    }

    public function create(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $cuenta_vent_ins_med = Ct_Configuraciones::obtener_cuenta('COMPRAPEDIDO_VENTA_INSUMOS_MED');
        return view('contable.compras_pedidos.create', ['empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(), 'proveedor' => Proveedor::all(), 'bodega' => Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get(), 'productos' => $productos = Ct_productos::where('id_empresa', $id_empresa)->get(), 'iva_param' => Ct_Configuraciones::where('id_plan',  $cuenta_vent_ins_med->cuenta_guardar)->first()]);
    }

    public function store(Request $request)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $objeto_validar = new Validate_Decimals();
        $errores        = "";
        $punto_emision  = $request['serie'];
        $sucursal       = substr($punto_emision, 0, -4);
        $punto_emision  = substr($punto_emision, 4);
        $empresa        = Empresa::find($id_empresa);
        $total_final    = $objeto_validar->set_round($request['total1']);
        $numero_factura = 0;
        $contador_ctv   = DB::table('ct_pedidos_compra')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $numero_factura      = 0;
        $fechahoy            = date("Y-m-d");
        $numeroconcadenado   = $request['serie'] . '-' . $request['secuencia_factura'];
        $comprobacion_compra = Ct_pedidos_Compra::where('numero', $numeroconcadenado)->where('tipo', '1')->where('proveedor', $request['proveedor'])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
        if (is_null($comprobacion_compra) || $comprobacion_compra == '[]') {

            if ($empresa->id != '0992704152001') {
                $id_proveedor = $request['proveedor'];
                //dd($id_proveedor);
                //$proveedor_find = Proveedor::find($id_proveedor);
                $proveedor_find = Ct_Acreedores::where('id_empresa', $id_empresa)->where('id_proveedor', $id_proveedor)->first();
                if(is_null($proveedor_find)){
                    $proveedor_find = Proveedor::find($id_proveedor);
                }
                $cabeceraa      = [
                    'observacion'     => $proveedor_find->razonsocial . ' # ' . $numeroconcadenado,
                    'fecha_asiento'   => $fechahoy,
                    'fact_numero'     => $request['secuencia_factura'],
                    'valor'           => $total_final,
                    'id_empresa'      => $id_empresa,
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
            }
            $nueva_fecha = null;

            $subtotalf = $request['base1'];
            $input     = [
                'tipo'                => '1',
                'fecha'               => $fechahoy,
                'proveedor'           => $request['proveedor'],
                'direccion_proveedor' => $request['direccion_proveedor'],
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'valor_contable'      => $total_final,
                'secuencia_f'         => $numero_factura,
                'estado'              => '1',
                'autorizacion'        => $request['autorizacion'],
                'f_autorizacion'      => $request['f_autorizacion'],
                'serie'               => $request['serie'],
                'id_empresa'          => $id_empresa,
                'numero'              => $numeroconcadenado,
                'secuencia_factura'   => $request['secuencia_factura'],
                'observacion'         => $request['observacion'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'subtotal'            => $subtotalf,
                'descuento'           => $request['descuento1'],
                'iva_total'           => $request['tarifa_iva1'],
                'ice_total'           => $request['ice_final1'],
                'total_final'         => $total_final,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_compra = Ct_pedidos_Compra::insertGetId($input);
            $id_proceso=Ct_Proceso::insertGetId([
                'id_pedido'=>$id_compra,
                'fecha'=>$fechahoy,
                'secuencia'=>Contable::secuence($id_empresa),
                'id_empresa'=>$id_empresa,
                'estado'=>'1'
            ]);
            Ct_Proceso_Detalle::create([
                'id_tipo_proceso'          =>'1',
                'id_referencia'       => $id_proceso,
                'id_find'             => $id_compra,
                'tipo'                =>'P',
                'id_usuariocrea'      => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'secuencia'           => Contable::secuence_process('P',$id_empresa),
            ]);
            $arr_total = [];
            for ($i = 0; $i < count($request->input("nombre")); $i++) {
                if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                    $arr = [
                        'nombre'     => $request->input("nombre")[$i],
                        'cantidad'   => $request->input("cantidad")[$i],
                        'bodega'     => $request->input("bodega")[$i],
                        'codigo'     => $request->input("codigo")[$i],
                        'precio'     => $request->input("precio")[$i],
                        'descpor'    => $request->input("descpor")[$i],
                        'copago'     => $request->input("copago")[$i],
                        'descuento'  => $request->input("desc")[$i],
                        'precioneto' => $request->input("precioneto")[$i],
                        'detalle'    => $request->input("descrip_prod")[$i],
                        'iva'        => $request->input("iva")[$i],

                    ];
                    array_push($arr_total, $arr);
                }
            }
            $cuentas_iva = [];
            foreach ($arr_total as $valor) {
                $consulta_product = Ct_productos::where('codigo', $valor['codigo'])->first();
                if (count($consulta_product) > 0) {
                    $cuentas_iva = $consulta_product->impuesto_iva_compras;
                }
                $detalle = [
                    'id_ct_compras_pedido' => $id_compra,
                    'codigo'               => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'bodega'               => $valor['bodega'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'estado'               => '1',
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['precioneto'],
                    'detalle'              => $valor['detalle'],
                    'iva'                  => $valor['iva'],
                    'porcentaje'           => $request['ivareal'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];

                Ct_Detalle_Pedido::create($detalle);
                $id_bod = Ct_Inventario_Bodega::insertGetId($detalle);
                $details_bode = Ct_Inventario_Bodega::find($id_bod);
                $details_bode->tipo = "IN";
                $details_bode->save();

            }
        }

        return json_encode('Guardado');
    }
    public function generar_factura($id, Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $pedido=Ct_pedidos_Compra::find($id);
        $c_tributario   = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante  = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $cuenta_vent_ins_med = Ct_Configuraciones::obtener_cuenta('COMPRAPEDIDO_VENTA_INSUMOS_MED');
        return view('contable.compras_pedidos.show',['pedido'=>$pedido, 'proveedor' => Proveedor::all(), 'iva_param' => Ct_Configuraciones::where('id_plan', $cuenta_vent_ins_med->cuenta_guardar)->first(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(), 'productos' => Ct_productos::where('id_empresa', $id_empresa)->get(), 'bodega' => Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get(),'termino'=>Ct_Termino::all(),'c_tributario'=>$c_tributario,'t_comprobante'=>$t_comprobante]);
    }
    public function edit(Request $request, $id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cuenta_vent_ins_med = Ct_Configuraciones::obtener_cuenta('COMPRAPEDIDO_VENTA_INSUMOS_MED');

        return view('contable.compras_pedidos.edit', ['compraPedido' => Ct_pedidos_Compra::findOrFail($id), 'proveedor' => Proveedor::all(), 'iva_param' => Ct_Configuraciones::where('id_plan', $cuenta_vent_ins_med->cuenta_guardar)->first(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(), 'productos' => Ct_productos::where('id_empresa', $id_empresa)->get(), 'bodega' => Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get()]);
    }
    public function update(Request $request, $id)
    {
        $pedidos    = Ct_pedidos_Compra::where('id', $id)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario  = Auth::user()->id;

        $pedidos->proveedor           = $request['nombre_proveedor'];
        $pedidos->direccion_proveedor = $request['direccion_proveedor'];
        $pedidos->autorizacion        = $request['autorizacion'];
        $pedidos->f_autorizacion      = $request['f_autorizacion'];
        $pedidos->observacion         = $request['observacion'];
        $pedidos->secuencia_factura   = $request['secuencia_factura'];
        $pedidos->serie               = $request['serie'];
        $pedidos->save();

        $editarPedido = Ct_pedidos_Compra::findOrFail($id)->update();
        $id_empresa   = $request->session()->get('id_empresa');
        $permisos = Inventario::permitidos();
        return view('contable.compras_pedidos.index', ['permisos'=>$permisos, 'pedidos' => Ct_pedidos_Compra::where('estado', '1')->paginate(10), 'proveedor' => Proveedor::all(), 'tipo_comprobante' => ct_master_tipos::where('estado', '1')->where('tipo', '1')->get(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(),
        ]);
    }

   

    public function confirmar_Check(Request $request,$id){
        $id_empresa = $request->session()->get('id_empresa');
        $cuenta_vent_ins_med = Ct_Configuraciones::obtener_cuenta('COMPRAPEDIDO_VENTA_INSUMOS_MED');
        return view('contable.compras_pedidos.confirmar', ['compraPedido' => Ct_pedidos_Compra::findOrFail($id), 'proveedor' => Proveedor::all(), 'iva_param' => Ct_Configuraciones::where('id_plan', $cuenta_vent_ins_med->cuenta_guardar)->first(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(), 'productos' => Ct_productos::where('id_empresa', $id_empresa)->get(), 'bodega' => Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get()]);
    }

    public function update_Check(Request $request){
        if($request->tipo == 'det'){

            $detalles = Ct_Detalle_Pedido::find($request->id);
            $detalles->check = $request->estado;
            $detalles->bodega = $request->bodega;
            $detalles->save();

        }else{
            $cab = Ct_pedidos_Compra::find($request->id);
            $cab->aprobar_pedido = 1;
            $cab->save();
        }
        
        return ['respuesta'=>'si', 'tipo'=>$request->tipo];

    }
    
    public function storeInvBodega(Request $request){
       // dd($request->all());
       DB::beginTransaction();

       try{
        for($i=0 ; $i < count($request->id_detalle) ; $i++){
            if($request['check'. $request->id_detalle[$i]] == 1){
                $details = Ct_Detalle_Pedido::find($request->id_detalle[$i]);
                $arr = $details['original'];
                unset($arr['id']);
                $cab = Ct_Inventario_Bodega::insertGetId($arr);
                $upd = Ct_Inventario_Bodega::find($cab);
                $upd->tipo = "EGRE";
                $upd->save();
            }
        }
        DB::commit();
        return ['respuesta'=>'si', 'msj'=>'Guardado Correctamente'];
       }catch (\Exception $e) {
        DB::rollBack();
        return ['respuesta'=>'no', 'msj' => $e->getMessage()];
    }
      
        
    }
    
    public function buscar(Request $request)
    {
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $constraints      = [
            'ct_c.id'                  => $request['id'],
            'proveedor'                => $request['proveedor'],
            'observacion'              => $request['detalle'],
            'ct_c.id_asiento_cabecera' => $request['id_asiento_cabecera'],
            'fecha'                    => $request['fecha'],
            'ct_c.tipo_comprobante'    => $request['tipo'],
            'secuencia_factura'        => $request['secuencia_f'],
        ];
        $pedido = $this->doSearchingQuery($constraints, $request);
        return view('contable.compras_pedidos.index', [
            'pedidos' => $pedido, 'proveedor' => Proveedor::all(), 'tipo_comprobante' => ct_master_tipos::where('estado', '1')->where('tipo', '1')->get(), 'empresa' => Empresa::where('id', $id_empresa)->where('estado', '1')->first(),
        ]);
    }
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query      = DB::table('ct_pedidos_compra as ct_c')
            ->join('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->select('ct_c.*');
        $fields = array_keys($constraints);
        $index  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == "ct_c.id_asiento_cabecera") {
                    $query = $query->where($fields[$index], $constraint);
                } elseif ($fields[$index] == "ct_c.id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function cambio_estado(Request $request)
    {

        $pedidos         = Ct_pedidos_Compra::where('id', $request['id'])->first();
        $pedidos->estado = 0;
        $pedidos->save();
        return json_encode('ok');
    }
    public function timeline($id){
        $pedidos = Ct_pedidos_Compra::find($id);
        $proceso= Ct_Proceso::where('id_pedido', $id)->first();
        return view('contable.timeline.index',['proceso'=>$proceso]);
    }
    public function aprobar_pedido(Request $request){
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $id=$request['id'];
      
        $pedido= Ct_pedidos_Compra::find($id);

        //dd($pedido);

        $pedido->aprobado=1;
        $pedido->save();
        //$compra= Ct_compras::where('orden_compra','P'.$id)->first();
        $proceso= Ct_Proceso::where('id_pedido',$id)->first();
        //dd($proceso);
        Ct_Proceso_Detalle::create([
            'id_tipo_proceso'     =>'3',
            'id_referencia'       => $proceso->id,
            'id_find'             => $id,
            'tipo'                =>'APD',
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'secuencia'           => Contable::secuence_process('APD',$id_empresa),
        ]);
        return response()->json('ok');
    }
    public function paso_factura(Request $request){
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $id= str_replace('P','',$request['id']);
        // dd($request->all());
        // dd("Replace: {$id}, Request: {$request->id}");

        $pedido= Ct_pedidos_Compra::find($id);
        $pedido->aprobado=1;
        $pedido->save();
        $compra= Ct_compras::where('orden_compra',$request['id'])->first();
        $proceso= Ct_Proceso::where('id_pedido',$id)->first();
        Ct_Proceso_Detalle::create([
            'id_tipo_proceso'     =>'4',
            'id_referencia'       => $proceso->id,
            'id_find'             => $compra->id,
            'tipo'                =>'C',
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'secuencia'           => Contable::secuence_process('C',$id_empresa),
        ]);
        return response()->json('ok');
    }
    public function retencion(Request $request){
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $compras=Ct_compras::find($request['id']);
        $retencion= Ct_Retenciones::where('id_compra',$compras->id)->where('estado','<>',0)->first();
        if(!is_null($compras)){
            if(!is_null($retencion)){
                $orden= $compras->orden_compra;
                $id= str_replace('P','',$orden);
                $pedido= Ct_pedidos_Compra::find($id);
                if(!is_null($pedido)){
                    $pedido->aprobado=2;
                    $pedido->save(); 
                    $proceso= Ct_Proceso::where('id_pedido',$id)->first();
                    Ct_Proceso_Detalle::create([
                        'id_tipo_proceso'     =>'5',
                        'id_referencia'       => $proceso->id,
                        'id_find'             => $retencion->id,
                        'tipo'                =>'R',
                        'id_usuariocrea'      => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'secuencia'           => Contable::secuence_process('R',$id_empresa),
                    ]);
                }
            }
        }
        return response()->json('ok');
    }

    public function aprobar_factura(Request $request){
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $id=$request['id'];
        $pedido= Ct_pedidos_Compra::find($id);
        $pedido->aprobado=3;
        $pedido->save();
        //$compra= Ct_compras::where('orden_compra','P'.$id)->first();
        $proceso= Ct_Proceso::where('id_pedido',$id)->first();
        Ct_Proceso_Detalle::create([
            'id_tipo_proceso'     =>'6',
            'id_referencia'       => $proceso->id,
            'id_find'             => $id,
            'tipo'                =>'APP',
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'secuencia'           => Contable::secuence_process('APP',$id_empresa),
        ]);
        return response()->json('ok');
    }

  

    public function indexInicales(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $id_producto = "";
        $datos = array();
        $busq = array();
        if(isset($request->id_producto)){
            $id_producto = $request->id_producto;  
            $datos = Ct_Inv_Interno::join('ct_inv_kardex as k', 'ct_inv_interno.id', 'k.id_inv')
            ->where('id_transaccion',1)->where('ct_inv_interno.id_empresa', $id_empresa)
            ->where('ct_inv_interno.id_producto', $id_producto)
            ->join('ct_bodegas as b', 'b.id', 'k.id_bodega')
            ->select('ct_inv_interno.*', 'b.nombre as b_nombre','k.estado as k_estado')
            ->orderBy('ct_inv_interno.id', 'desc')
            //->get();
            ->paginate(10);
            array_push($busq,$id_producto);
        }else{
            $datos = Ct_Inv_Interno::join('ct_inv_kardex as k', 'ct_inv_interno.id', 'k.id_inv')
            ->where('id_transaccion',1)->where('ct_inv_interno.id_empresa', $id_empresa)
            ->join('ct_bodegas as b', 'b.id', 'k.id_bodega')
            ->select('ct_inv_interno.*', 'b.nombre as b_nombre','k.estado as k_estado')
            ->orderBy('ct_inv_interno.id', 'desc')
            //->get();
            ->paginate(10);
        }
      
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();

        $empresa = Empresa::find($id_empresa);
        return view('contable/compra/inicial/index', ['datos'=> $datos, 'empresa'=> $empresa,'productos'=> $productos, 'busq'=>$busq]);
    }
    
    public function createInicial(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $bodegas = Ct_Bodegas::where('id_empresa', $empresa->id)->where('estado', 1)->get();
        return view('contable/compra/inicial/create', ['empresa'=> $empresa, 'productos'=> $productos, 'bodegas'=>$bodegas]);
    }
 
    public function storeInicial(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $id_usuario       = Auth::user()->id;
        $id_producto = $request->id_producto;
        $fecha = $request->fecha;

        $iniciales= Ct_Inv_Kardex::join('ct_inv_interno as interno','interno.id','ct_inv_kardex.id_inv')
                        ->where('interno.id_empresa',$id_empresa)->whereYear('ct_inv_kardex.fecha',$fecha)
                        ->where('ct_inv_kardex.estado',1)->where('ct_inv_kardex.id_transaccion',1)
                        ->where('ct_inv_kardex.id_producto',$id_producto)
                        ->where('ct_inv_kardex.id_bodega', $request->bodega)
                        ->select('ct_inv_kardex.*')->first();
            if(!is_null($iniciales)){
                return ['respuesta'=>'existe', 'msj' => 'Ya existe un producto Inicial en el año'];
            }else{
                
                $arreglo = [
                    'id_producto'       => $id_producto,
                    'costo'             => $request->costo,
                    'costo_venta'       => $request->costo_venta,
                    'fecha'             => $request->fecha,
                    'id_empresa'        => $id_empresa,
                    'stock'             => $request->stock,
                    'id_usuario'        => $id_usuario,
                    'bodega'            => $request->bodega,
                ];

                $retorno = $this->crearProducto($arreglo);

            return $retorno;
                
                //$fecha = "";
                // $fecha = Date('Y-m-d H:i:s', strtotime("1/1/{$fecha} 00:00:00"));
                // //dd($fecha);
                // $comprobar= Ct_Inv_Interno::where('id_producto',$id_producto)->where('id_empresa',$id_empresa)->first();
                // $id_inv_interno=null;
                // $valx=false;
                // if(is_null($comprobar)){
                //     $id_inv_interno = Ct_Inv_Interno::insertGetId([
                //         'id_producto'       => $id_producto,
                //         'stock'             => $request->stock,
                //         'cantidad'          => $request->stock,  
                //         'estado'            => '1',
                //         'costo'             => $request->costo,
                //         'id_empresa'        => $id_empresa,
                //         'costo_venta'       => $request->costo_venta,
                //     ]);
                // }else{
                //     $id_inv_interno= $comprobar->id;
                //     $valx=true;
                // }
                // $inv_moviento = Ct_Inv_Movimiento::insertGetId([
                //     'id_transaccion'       => 1,
                //     'tipo'                 => 'INI',
                // ]);

                // $inv_costo = Ct_Inv_Costos::insertGetId([
                //     'id_producto'          => $id_producto,
                //     'costo_promedio'       => $request->costo,
                //     'costo_anterior'       => $request->costo,
                //     'id_empresa'           => $id_empresa,
                //     'estado'               => '1' 
                // ]);


                // $inv_kardex = Ct_Inv_Kardex::insertGetId([
                //     'id_bodega'            => $request->bodega,
                //     'cantidad'             => $request->stock,
                //     'id_producto'          => $request->id_producto,
                //     'id_transaccion'       => '1',
                //     'id_movimiento'        => $inv_moviento,
                //     'fecha'                => $fecha,
                //     'precio'               => $request->costo,
                //     'id_inv'               => $id_inv_interno,
                //     'id_usuariocrea'       => $id_usuario,
                //     'id_usuariomod'        => $id_usuario,
                // ]);
            }

        //    DB::commit();
            //dd($valx);
            // if($valx){
            //     $recalcula= Inventario::recalcular_compras($id_inv_interno);
            // }
            
        //     return ['respuesta'=>'si', 'msj'=>'Guardado Exitosamente'];
        // }catch (\Exception $e) {
        //     DB::rollBack();
        //     return ['respuesta'=>'no', 'msj' => $e->getMessage()];
        // }
    }   
        
    public function editarInicial(Request $request, $id){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
    
        $inv_interno = Ct_Inv_Interno::where('ct_inv_interno.id', $id)
                        ->join('ct_inv_kardex as k', 'k.id_inv', 'ct_inv_interno.id')
                        ->where('id_transaccion',1)
                        ->select('ct_inv_interno.*', 'k.fecha as fecha', 'k.id_bodega as bodega')
                        ->first();

        $productos = Ct_productos::where('id_empresa', $id_empresa)->where('id', $inv_interno->id_producto)->first();

        $year = explode("-", $inv_interno->fecha);
        
        $bodegas = Ct_Bodegas::where('id_empresa', $empresa->id)->where('estado', 1)->where('id', $inv_interno->bodega)->first();

        return view('contable/compra/inicial/editar', ['empresa'=> $empresa, 'productos'=> $productos, 'interno'=>$inv_interno, 'years'=> $year[0], 'bodegas'=>$bodegas]);
    }
    public function  deleteInicial(Request $request){
        //dd($request->all());
        $interno = Ct_Inv_Interno::find($request->id);
        $kardex = Ct_Inv_Kardex::where('id_inv', $interno->id)->where('estado',1)->where('id_transaccion', 1)->first();
        $kardex->estado  = 0;
        $kardex->save();
        $recalcula= Inventario::recalcular_compras($request->id);
        return['respuesta'=>'si', 'msj'=>'Eliminado Exitosamente'];
    }

    public function excelInicial(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $bodegas = Ct_Bodegas::where('id_empresa', $empresa->id)->where('estado', 1)->get();
        return view('contable/compra/inicial/excel_view',['empresa'=> $empresa, 'bodegas'=>$bodegas]);
    }
    public function descargarExcel(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        Excel::create('Carga Inicial ', function ($excel) use ($empresa) {
            $excel->sheet('Carga Inicial de Producto', function ($sheet) use($empresa) {
                $sheet->mergeCells('A1:A1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Producto');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B1:B1');
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('Codigo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('Año');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('Stock');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('Bodega');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('Costo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('Costo Venta');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

            });
        })->export('xlsx');
    }

    public static function crearProducto($arreglo){
        $id_usuario       = Auth::user()->id;
        $id_producto = $arreglo['id_producto'];
        $fecha = intval($arreglo['fecha']);
        $id_empresa = $arreglo['id_empresa'];
        $costo = intval($arreglo['costo']);
        $costo_venta = $arreglo['costo_venta'];
        $stock = intval($arreglo['stock']);
        $bodega = intval($arreglo['bodega']);

         DB::beginTransaction();
         try{
            $fecha = Date('Y-m-d H:i:s', strtotime("1/1/{$fecha} 00:00:00"));
            
            $comprobar= Ct_Inv_Interno::where('id_producto',$id_producto)->where('id_empresa',$id_empresa)->first();
            $id_inv_interno=null;
            $valx=false;
            if(is_null($comprobar)){
                $id_inv_interno = Ct_Inv_Interno::insertGetId([
                    'id_producto'       => $id_producto,
                    'stock'             => intval($stock),
                    'cantidad'          => intval($stock),  
                    'estado'            => '1',
                    'costo'             => $costo,
                    'id_empresa'        => $id_empresa,
                    'costo_venta'       => $costo_venta,
                ]);
            }else{
                $id_inv_interno= $comprobar->id;
                $valx=true;
            }

            $inv_moviento = Ct_Inv_Movimiento::insertGetId([
                'id_transaccion'       => 1,
                'tipo'                 => 'INI',
            ]);


            $inv_costo = Ct_Inv_Costos::insertGetId([
                'id_producto'          => $id_producto,
                'costo_promedio'       => $costo,
                'costo_anterior'       => $costo,
                'id_empresa'           => $id_empresa,
                'estado'               => '1' 
            ]);


            $inv_kardex = Ct_Inv_Kardex::insertGetId([
                'id_bodega'            => intval($bodega),
                'cantidad'             => intval($stock),
                'id_producto'          => $id_producto,
                'id_transaccion'       => '1',
                'id_movimiento'        => $inv_moviento,
                'fecha'                => $fecha,
                'precio'               => $costo,
                'id_inv'               => $id_inv_interno,
                'id_usuariocrea'       => $id_usuario,
                'id_usuariomod'        => $id_usuario,
            ]);
            DB::commit();
            if($valx){
                $recalcula= Inventario::recalcular_compras($id_inv_interno);
            }
            return ['respuesta'=>'si', 'msj'=>'Guardado Exitosamente'];
         }catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta'=>'no', 'msj' => $e->getMessage()];
        }

    }


    public static function creareExcelInicial(Request $request){
        $id_empresa       = $request->session()->get('id_empresa');
        $id_usuario       = Auth::user()->id;
        $nombre_original = $request['excel']->getClientOriginalName();
		$extension       = $request['excel']->getClientOriginalExtension();
		$nuevo_nombre    = "carga_inicial" . rand(0, 999999) . "." . $extension;
        
		$r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['excel']));
		$rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
        if($r1){
            Excel::filter('chunk')->load($rutadelaimagen)->chunk(600, function ($reader) use($id_empresa, $id_usuario) {
                foreach ($reader as $book) {
                    $producto = Ct_Productos::where('codigo', $book->codigo)->where('id_empresa', $id_empresa)->select('id')->first();

                    $iniciales= Ct_Inv_Kardex::join('ct_inv_interno as interno','interno.id','ct_inv_kardex.id_inv')
                        ->where('interno.id_empresa',$id_empresa)->whereYear('ct_inv_kardex.fecha',intval($book->ano))
                        ->where('ct_inv_kardex.estado',1)->where('ct_inv_kardex.id_transaccion',1)
                        ->where('ct_inv_kardex.id_producto',$producto->id)
                        ->where('ct_inv_kardex.id_bodega',intval($book->bodega))
                        ->select('ct_inv_kardex.*')->first();
                    //dd("empresa: {$id_empresa}, fecha: {$book->ano}, producto: {$producto->id}, bodega: {$book->bodega}");
                    //dd($iniciales);
                    if(is_null($iniciales)){
                       
                        $arreglo = [
                            'id_producto'       => $producto->id,
                            'costo'             => $book->costo,
                            'costo_venta'       => $book->costo_venta,
                            'fecha'             => $book->ano,
                            'id_empresa'        => $id_empresa,
                            'stock'             => $book->stock,
                            'id_usuario'        => $id_usuario,
                            'bodega'            => $book->bodega,
                        ];
                        CompraPedidoController::crearProducto($arreglo);
                    }
                }
            });
        }
        return redirect()->route('contable.excelProdInicial');
    }
}
