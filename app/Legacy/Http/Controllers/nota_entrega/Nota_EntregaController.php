<?php

namespace Sis_medico\Http\Controllers\nota_entrega;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\HcEgreso;
use Sis_medico\Agenda;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\CtCajaCobro;
use Sis_medico\Seguro;
use Sis_medico\MembresiasLabsController;
use Sis_medico\SeguroTipos;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\hc_receta;
use Sis_medico\Medicina;
use Sis_medico\Nota_entrega;
use Sis_medico\Nota_entrega_detalle;
use Sis_medico\Medicina_tarifario;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\Log_nota_entrega;
use Sis_medico\Bodega;
use Sis_medico\ApProcedimiento;
use Sis_medico\Ct_productos;
use Sis_medico\Producto;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvCabMovimientos;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Planilla;
use Sis_medico\Empresa;
use Sis_medico\InvInventario;
use Sis_medico\ControlInventario;
use Sis_medico\Http\Controllers\contable\ProductosController;
use Sis_medico\Utilidades;



class Nota_EntregaController extends Controller
{
  private function rol()
  {
      $rolUsuario = Auth::user()->id_tipo_usuario;
      if (in_array($rolUsuario, array(1, 3, 4, 5,6, 11, 20, 22, 30)) == false) {
          return true;
      }
  }

    protected $redirectTo = '/'; 
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('auth');
    }
 
    public function editar($id)
    {
      //dd($id);
      if ($this->rol()) {
            return response()->view('errors.404');
        }
        $nota_entrega      = Nota_Entrega::where('id_hc_receta', $id)->where('estado','<>', 0)->first();
        $empresa    = $nota_entrega->empresa;
        $paciente   = $nota_entrega->historia->paciente;
        $bodegas = Bodega::where('prioridad_farmacia', '1')->where('estado', 1)->first();

        return view('hc_admision/nota_de_entrega/editar_nota_entrega',['nota_entrega' => $nota_entrega, 'paciente' => $paciente, 'id_receta' => $id, 'empresa' => $empresa, 'bodegas' => $bodegas]);
    }

    public function detalles($id_receta)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $nota_entrega      = Nota_Entrega::where('id_hc_receta', $id_receta)->where('estado','<>', 0)->first();
        $detalles   = $nota_entrega->detalles;
        $porIva = Utilidades::porcentajeIva();
        $bodegas = Bodega::where('prioridad_farmacia', '1')->where('estado', 1)->first();
        return view('hc_admision/nota_de_entrega/detalles', ['nota_entrega' => $nota_entrega, 'detalles' => $detalles, 'bodegas' => $bodegas, 'porIva' => $porIva]);
    }

    public function buscarMedicinas(Request $request)
    {
        //dd($request->all());
        $producto = [];
        if (!is_null($request["search"])) {
            $producto = Medicina::where('estado', 1)
                ->where('nombre', "LIKE", "%{$request['search']}%")
                ->where('iess', 2)
                ->select('id as id', 'nombre as text')->get();
        }
        return response()->json($producto);
    }

    public function guardar_medicina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_receta = $request->id;
        $id_producto = $request->producto_nuevo;
        $nota_entrega = Nota_Entrega::where('id_hc_receta', $id_receta)->first();
        $medicina_tarifario    = Medicina_tarifario::where('id_medicina', $id_producto)->where('estado', '1')->first();
        $ap_procedimiento = $medicina_tarifario->ap_procedimiento;
        $precio = 0;
        if($ap_procedimiento->valor != null || $ap_procedimiento->valor != 0){
          $precio = $ap_procedimiento->valor;
        }
        $arr_producto = [
            'id_nota_entrega' => $nota_entrega->id,
            'id_medicina' => $id_producto,
            'nombre_medicina' => $ap_procedimiento->descripcion,
            'cantidad'      => 1,
            'precio'        => $precio,
            'subtotal_0'    => $precio,
            'total'         => $precio,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente
        ];
        Nota_entrega_detalle::create($arr_producto);
        $this->recalcular($nota_entrega->id);
        $log_nota_entrega = [
            'id_hc_receta' => $id_receta,
            'id_nota_entrega' => $nota_entrega->id,
            'descripcion' => "Agrego nueva medicina",
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Log_nota_entrega::create($log_nota_entrega);

        return "ok";
    }

    public function actualizar_medicina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_detalle = $request->id;
        $id_producto = $request->id_producto;
        $id_nota = $request->id_nota;
        $cantidad = $request->cantidad;
        $precio   = $request->precio;
        $detalle  = Nota_entrega_detalle::find($id_detalle);
        $subtotal  = $cantidad * $precio;
        $total     = $subtotal;
        $arr_producto = [
            'precio'            => $precio,
            'cantidad'          => $cantidad,
            'subtotal_0'        => $subtotal,
            'total'             => $total,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente
        ];
        $detalle->update($arr_producto);
        $this->recalcular($detalle->id_nota_entrega);
        $nota_entrega = Nota_entrega::find($id_nota);
        $log_nota_entrega = [
            'id_hc_receta' => $nota_entrega->id_hc_receta,
            'id_nota_entrega' => $nota_entrega->id,
            'descripcion' => "Actualizo la medicina",
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Log_nota_entrega::create($log_nota_entrega);
        return "ok";
    }

    public static function recalcular($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nota_entrega    = Nota_entrega::find($id);
        $total = $nota_entrega->detalles->sum('total');
        $subtotal_0          = $nota_entrega->detalles->sum('subtotal_0');
        $subtotal_12         = $nota_entrega->detalles->sum('subtotal_12');
        $arr_cabecera = [
            'total'                 => $total,
            'subtotal_0'            => $subtotal_0,
            'subtotal_12'           => $subtotal_12,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente
        ];

        $nota_entrega->update($arr_cabecera);
    }

    public function _eliminar_medicina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $detalle  = Nota_entrega_detalle::find($request->id);
        $detalle->delete();
        $this->recalcular($request->id_nota);
        $nota_entrega = Nota_entrega::find($request->id_nota);
        $log_nota_entrega = [
            'id_hc_receta' => $nota_entrega->id_hc_receta,
            'id_nota_entrega' => $nota_entrega->id,
            'descripcion' => "Elimino la medicina",
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Log_nota_entrega::create($log_nota_entrega);
        return "ok";
    }

    public function eliminar_medicina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $detalle  = Nota_entrega_detalle::find($request->id);
        $detalle->delete();
        $this->recalcular($request->id_nota);
        $nota_entrega = Nota_entrega::find($request->id_nota);
        $log_nota_entrega = [
            'id_hc_receta' => $nota_entrega->id_hc_receta,
            'id_nota_entrega' => $nota_entrega->id,
            'descripcion' => "Elimino la medicina",
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Log_nota_entrega::create($log_nota_entrega);

        $id_ap_procedimiento = Medicina_tarifario::where('id_medicina', $detalle->id_medicina)->first()->id_ap_procedimiento;

        if(!is_null($id_ap_procedimiento)){
            $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $nota_entrega->id_planilla_cabecera)->where('id_ap_procedimiento', $id_ap_procedimiento)->get();
            if(!is_null($planilla_detalle)){
                foreach ($planilla_detalle as $val) {
                    $val->delete();
                }
            }
        }
        return "ok";
    }

    public function emitir_recibo($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nota_entrega    = Nota_entrega::find($id);
        $detalles   = $nota_entrega->detalles;
        $arr_log_nota = [
            'id_hc_receta'      => $nota_entrega->id_hc_receta,
            'id_nota_entrega'   => $nota_entrega->id,
            'descripcion'       => 'GENERADA EL: ' . date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        Log_nota_entrega::create($arr_log_nota);
        $arr_nota_entrega = [
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'estado'            => 1,
        ];
        $nota_entrega->update($arr_nota_entrega);
        return "ok";
    }

    public function imprimir_pdf_nota_entrega($id_nota)
    {
        $porIva = Utilidades::porcentajeIva();
      $nota_entrega    = Nota_entrega::find($id_nota);
      $nota_entrega_detalle   = $nota_entrega->detalles;
      $hc = Historiaclinica::findorfail($nota_entrega->id_hc);
      $agenda = Agenda::findOrFail($hc->id_agenda);
      $vistaurl = "hc_admision.receta.pdf_nota_entrega";
        $view     = \View::make($vistaurl, compact('nota_entrega', 'nota_entrega_detalle', 'hc', 'agenda', 'porIva'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
      $pdf->setPaper('A4', 'portraint');
      return $pdf->stream('Nota de Entrega - ' . $nota_entrega->id . '.pdf');
    }

    public function guardar_planilla_detalle(Request $request){
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cantidad = $request['cantidad_inv'];
        $check = 1;
        $estado = 1;
        $id_bodega = Bodega::where('prioridad_farmacia', '1')->where('estado', 1)->first()->id;
        $nota_entrega = Nota_entrega::findorfail($request->id_nota_entrega);
        $receta = hc_receta::findorfail($nota_entrega->id_hc_receta);
        $hc = Historiaclinica::findorfail($receta->id_hc);
        $agenda = Agenda::findOrFail($hc->id_agenda);
        $id_hc_procedimiento = hc_procedimientos::where('id_hc', $hc->hcid)->first()->id;
        $producto = Producto::where('codigo', $request->codigo)->where('estado', 1)->first();
        $ct_productos = Ct_productos::where('codigo', $request->codigo)->where('estado_tabla', 1)->first();
        $ap_procedimiento = ApProcedimiento::where('id_producto', $ct_productos->id)->first();
        $inv_inventario = InvInventario::where('id_producto', $producto->id)->where('id_bodega', $id_bodega)->where('existencia', '!=', 0)->where('estado', 1)->first();

        $planilla_cabecera = Planilla_Cabecera::where('id_agenda', $agenda->id)->where('id_hc_procedimiento', $id_hc_procedimiento)->where('estado', 1)->where('nota_entrega', 1)->first();

        if(!is_null($planilla_cabecera)){
            $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $planilla_cabecera->id)->where('codigo', $request->codigo)->where('serie', $request->serie)->where('lote', $request->lote)->where('check', 1)->where('estado', 1)->first();
        }
       
        if($request['cantidad_inv'] == 0){
            $check = 0;
            $estado = 0;
        }

        if(!is_null($planilla_cabecera) && is_null($planilla_detalle))
        {
            $detalle = [
                'codigo'      => $request['codigo'],
                'id_planilla_cabecera'  => $planilla_cabecera->id,
                'precio' => $inv_inventario->costo_promedio,
                'check'     => $check,
                'estado'  => $estado,
                'cantidad'  => $cantidad,
                'serie'  => $request['serie'],
                'lote'  => $request['lote'],
                'fecha_vencimiento'  =>  $request['fecha_vence'],
                'observacion'  =>  'Nota de entrega',
                'id_bodega'  => $request['id_bodega'],
                'id_ct_producto'  => $ct_productos->id,
                'id_producto'  =>  $producto->id,
                'id_ap_procedimiento'  =>  $ap_procedimiento->id,
                'valor_ap_procedimiento'  =>  $ap_procedimiento->valor,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Planilla_Detalle::create($detalle);
        }elseif(!is_null($planilla_cabecera) && !is_null($planilla_detalle)){
                $planilla_detalle->precio = $inv_inventario->costo_promedio;
                $planilla_detalle->cantidad = $cantidad;
                $planilla_detalle->check = $check;
                $planilla_detalle->estado = $estado;
                $planilla_detalle->id_usuariomod = $idusuario;
                $planilla_detalle->ip_modificacion = $ip_cliente;
                $planilla_detalle->save();   
        }
    }

    public function validar_detalle(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_bodega = $request->id_bodega;
        $nota_entrega    = Nota_entrega::find($request->id);
        $detalles   = $nota_entrega->detalles;
        $msj = "";
        foreach ($detalles as $row) {
            $medicina_tarifario    = Medicina_tarifario::where('id_medicina', $row->id_medicina)->where('estado', '1')->first();
            $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $nota_entrega->id_planilla_cabecera)
            ->where('id_ap_procedimiento', $medicina_tarifario->id_ap_procedimiento)
            ->where('estado', 1)
            ->groupBy('codigo')
            ->select(DB::raw('SUM(cantidad) as cantidad'))->first();

           // dd($planilla_detalle->cantidad, $row->cantidad);
            if(!is_null($planilla_detalle))
            {
                if($planilla_detalle->cantidad != $row->cantidad){
                    $msj .= "Agregue la cantidad a descontar de la medicina | ".'('. $row->nombre_medicina .')'."<br/> <br/>";
                }
            }else{
                $msj .= "Ingrese la cantidad a descontar de la medicina: ".'('. $row->nombre_medicina .')'."<br/> <br/>";
            }
        }
        if($msj != ''){
            return $msj; 
        } else{
            return "ok";
        } 
    }

    public function validar_stock(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_bodega = $request->id_bodega;
        $nota_entrega    = Nota_entrega::find($request->id);
        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $nota_entrega->id_planilla_cabecera)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega', 'id_producto')->get();

        $msj = "";
        foreach ($planilla_detalle as $det) {
            $producto    = Producto::find($det->id_producto);
            $inv_serie = InvInventarioSerie::where('serie', $det->serie)
                ->where('id_bodega', $id_bodega)
                ->where('id_empresa', $nota_entrega->id_empresa)
                ->where('estado', 1)
                ->where('existencia', '!=', 0)
                ->first();
            if(!is_null($inv_serie)){
                if($inv_serie->existencia < $det->cantidad){
                    $msj .= "Stock Insuficiente " .'('. $producto->nombre .')' . " | Cantidad en Stock: " . $inv_serie->existencia ."<br/> <br/>" ;
                }
            }else{
                $msj .= "No se encontro en inventario la medicina: ".'('. $producto->nombre .')'."<br/> <br/>";
            }
        }
        if($msj != ''){
            return $msj; 
        } else{
            //$kardex = Nota_EntregaController::descontarKardex($request->id, $id_bodega);
            return "ok";
        } 
    }

    // public static function descontarKardex($id, $id_bodega)
    // {
    //     try{ 
    //         $nota_entrega    = Nota_entrega::find($id);
    //         $id_hc_procedimientos = hc_procedimientos::where('id_hc', $nota_entrega->id_hc)->first()->id;
    //         $id_agenda = $nota_entrega->historia->agenda->id;
            
    //         $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $nota_entrega->id_planilla_cabecera)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega', 'id_producto', 'id')->get();

    //         foreach ($planilla_detalle as $det) {
    //             $producto = null;
    //             $inv_serie = null;
    //             $cantidad = $det->cantidad;
    //             $cantidad_uso = 0;
    //             $id_movimiento_paciente = null;

    //             $producto    = Producto::find($det->id_producto);
    //             if (!is_null($producto)) {
    //                 $cantidad_uso       = $cantidad * $producto->usos;
    //             } else {
    //                 $cantidad_uso       = 0;
    //             }
                
    //             $inv_serie = InvInventarioSerie::where('serie', $det->serie)
    //             ->where('id_bodega', $id_bodega)
    //             ->where('id_empresa', $nota_entrega->id_empresa)
    //             ->where('estado', 1)
    //             ->where('existencia', '!=', 0)
    //             ->first();

                
    //             if (isset($inv_serie->id) and ($id_agenda != null and $id_agenda != "") and ($id_hc_procedimientos != null and $id_hc_procedimientos != "") ) {
    //                 $id_egre_pac = InvCabMovimientos::documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente, $id_bodega, $det->id);
    //             }
    //         }
    //         return "ok";
    //     }catch(\Exception $e){
    //         return [$e->getMessage()] ;
    //     }
    // }

    public function agregar_stock_medicina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cantidad_planilla = Planilla_Detalle::where('id_planilla_cabecera', $request->id_planilla)
            ->where('codigo', $request->codigo)
            ->where('estado', 1)
            ->groupBy('codigo')
            ->select(DB::raw('SUM(cantidad) as cantidad'))->first();

        $nota_entrega_detalle = Nota_entrega_detalle::find($request->id);


        if(!is_null($cantidad_planilla))
        {
            if($cantidad_planilla->cantidad > $nota_entrega_detalle->cantidad_receta){
                $resp['status']  = 'error';
                $resp['message'] = "Cantidad a entregar no puede ser mayor a la cantidad recetada";
                return response()->json($resp);
            }

            $nota_entrega_detalle->cantidad = $cantidad_planilla->cantidad;
            $nota_entrega_detalle->id_usuariomod = $idusuario;
            $nota_entrega_detalle->ip_modificacion = $ip_cliente;
            $nota_entrega_detalle->save();   

            return $cantidad_planilla->cantidad;
        }else{
            $resp['status']  = 'error';
            $resp['message'] = "Cantidad no agregada";
            return response()->json($resp);
        }
    }

    public function planilla_index(Request $request)
    {
        $plantilla = Planilla::where('estado', 1)->where('nota_entrega', 1);
        if($request->codigo!=null){
            $plantilla= $plantilla->where('codigo',$request->codigo);
        }
        if($request->nombre!=null){
            $plantilla= $plantilla->where('observacion','LIKE','%'.$request->nombre.'%');
        }
        $fecha_desde= $request->fecha_desde;
        if($fecha_desde==null){
            $fecha_desde= date('Y-m-d');
        }
        $fecha_hasta= $request->fecha_hasta;
        if($fecha_hasta==null){
            $fecha_hasta= date('Y-m-d');
        }
        if($fecha_desde==null){
            $plantilla= $plantilla->where('fecha','<=',date('Y-m-d'));
        }else{
            $plantilla= $plantilla->whereBetween('fecha',[$fecha_desde.' 00:00:00',$fecha_hasta.' 23:59:59']);
        }
        $plantilla= $plantilla->orderBy('fecha', 'DESC')->paginate(10);
        $procedimientos = array();
        return view('hc_admision.nota_de_entrega.comparativo', ['planilla' => $plantilla, 'request'=>$request,
        'fecha_hasta'=>$fecha_hasta,'fecha_desde'=>$fecha_desde, 'procedimientos'=>$procedimientos]);
    }

    public function edit_comparar($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        $plantilla = Planilla::find($id);
        if ($plantilla->id_planilla == null || $plantilla->id_planilla == 0) {
            $plantilla->id_planilla = 1;
            $plantilla->save();
        }
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $agenda = Agenda::find($plantilla->id_agenda);

        if(!is_null($agenda->id_empresa)){
            $empresa = Empresa::find($agenda->id_empresa);
        }

        return view('hc_admision/nota_de_entrega/edit_comparativo', ['planilla' => $plantilla, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function storeAprobado(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $planilla   = Planilla::find($request['id_planilla']);
        $nota_entrega = Nota_entrega::where('id_planilla_cabecera', $request['id_planilla'])->first();
        $resp       = array();
        $id_empresa = Session::get('id_empresa');

        if($nota_entrega->estado != 1){
            $resp['status']  = 'error';
            $resp['message'] = "La Nota de Entrega no se encuentra emitida";
            return response()->json($resp);
        }

        $control_inventario = ControlInventario::where('id_empresa', $id_empresa)->where('estado', '0')->first();
        if (!is_null($control_inventario)) {
            $resp['status']  = 'error';
            $resp['message'] = "Aprobacion se encuentra en proceso, por favor espere a que termine y vuelva a intentar";
            return response()->json($resp);
        }

        if ($planilla->aprobado == 1) {
            $resp['status']  = 'error';
            $resp['message'] = "Ya se encuentra aprobado esta planilla";
            return response()->json($resp);
        }

        //Verificacion de stock de productos
        $verificarStockFun = $this->verificarStock($planilla->id);
        if ($verificarStockFun["status"] == "error") {
            $resp['status']  = 'error';
            $resp['message'] = $verificarStockFun['problem'];
            return response()->json($resp);
        }
        $array_control = [
            'id_empresa'            => $id_empresa,
            'id_planilla_cabecera'  => $planilla->id,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
        ];
        $id_control_inventario = ControlInventario::insertGetId($array_control);

        $resp       = array();
        if ($planilla != null and !is_null($request)) {
            if (!is_null($planilla->detalles)) {
                DB::beginTransaction();
                try {
                    $planilla->codigo          = $request->codigo;
                    $planilla->observacion     = $request->observacion;
                    $planilla->id_usuariomod   = $idusuario;
                    $planilla->ip_modificacion = $ip_cliente;
                    $planilla->aprobado        = 1;
                    $planilla->save();
                    foreach ($planilla->detalles_validos as $row) {

                        $this->corrige_existencias($row->serie, $row->id_bodega, $planilla->id_agenda);
                        // $id_bodega              = env('BODEGA_EGR_PACI1', 2);
                        $id_bodega              = $row->id_bodega;
                        $id_agenda              = $planilla->id_agenda;
                        $id_hc_procedimientos   = $planilla->id_hc_procedimiento;
                        $cantidad               = $row->cantidad;
                        // $movimiento             = $row->tmovimiento;
                        $producto = Producto::where('codigo', $row->codigo)->first();
                        if (isset($producto->id)) {
                            $cantidad_uso = $cantidad * $producto->usos;
                        } else {
                            $cantidad_uso = 0;
                        }
                        $id_movimiento_paciente = null;
                        
                        $inv_serie = InvInventarioSerie::where('serie', $row->serie)
                            ->where('id_bodega', $id_bodega)
                            ->where('id_empresa', $id_empresa)
                            ->where('estado', 1)
                            ->first();

                        if (isset($inv_serie->id) and ($id_agenda != null and $id_agenda != "") and ($id_hc_procedimientos != null and $id_hc_procedimientos != "") ) {
                            //$id_egre_pac = InvCabMovimientos::documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente, $id_bodega);
                            /* VICTOR KARDEX NUEVO */
                            $id_egre_pac = InvCabMovimientos::documentoEgresoPaciente($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso, $id_movimiento_paciente, $id_bodega, $row->id);
                        }
                        /*else {
                    $resp['status'] = 'error';
                    $resp['message'] = 'Faltan datos! ';
                    return response()->json($resp);
                    }*/
                    }
                    // InvContableCab::setAsientoContableComprarativo($id_egre_pac);

                    DB::commit();
                    $resp['status']  = 'ok';
                    $resp['message'] = 'Plantilla validada con &eacute;xito';
                    $control_inventario = ControlInventario::find($id_control_inventario);
                    if (!is_null($control_inventario)) {
                        $control_inventario->update(['estado' => '1']);
                    }

                    $nota_entrega    = Nota_entrega::where('id_planilla_cabecera', $request['id_planilla'])->first();
                    $arr_log_nota = [
                        'id_hc_receta'      => $nota_entrega->id_hc_receta,
                        'id_nota_entrega'   => $nota_entrega->id,
                        'descripcion'       => 'PLANILLA APROBADA EL: ' . date('Y-m-d H:i:s'),
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    Log_nota_entrega::create($arr_log_nota);
                    $arr_nota_entrega = [
                        'id_usuariomod'     => $idusuario,
                        'ip_modificacion'   => $ip_cliente,
                        'estado_generado'   => 1,
                    ];
                    $nota_entrega->update($arr_nota_entrega);

                } catch (\Exception $e) {
                    DB::rollBack();
                    $resp['status'] = 'error';
                    $resp['message'] = 'error: ' . $e->getMessage() . ' line:' . $e->getLine();

                    //return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'mod' => 'store', 'linea' => $e->getLine()];
                    return response()->json($resp);
                }
            } else {
                $resp['status']  = 'error';
                $resp['message'] = 'No se encontraron detalle de la plantilla';
                $control_inventario = ControlInventario::find($id_control_inventario);
                if (!is_null($control_inventario)) {
                    $control_inventario->update(['estado' => '1']);
                }
                return response()->json($resp);
            }
        }
        return response()->json($resp);
    }

    public function verificarStock($id_planilla)
    {
        $id_empresa = Session::get('id_empresa');
        
        $planilla_detalle = Planilla_Detalle::where('id_planilla_cabecera', $id_planilla)->where('check', 1)->groupBy('serie', 'id_bodega')->where('estado', 1)
            ->select(DB::raw('SUM(cantidad) as cantidad'), 'serie', 'id_bodega')->get();
        // dd($planilla_detalle);
        $status = 'success';
        $problemas = '';
        foreach ($planilla_detalle as $det) {

            $inv_serie = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->where('existencia', '>=', $det->cantidad)->first();
            if (is_null($inv_serie)) {

                $inv_serie2 = InvInventarioSerie::where('serie', $det->serie)->where('id_bodega', $det->id_bodega)->where('id_empresa', $id_empresa)->first();
                if(!is_null($inv_serie2)) {
                    if($inv_serie2->cantidad_usos_kardex != null && $inv_serie2->cantidad_usos_kardex > 0){
                       
                    }else{
                        $status = "error";
                        $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
                    }
                }else{
                    $status = "error";
                    $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente o <br>No se encontro en el inventario esta serie | Serie: {$det->serie}</span> <br> <br>";
                }
                
                
               
            } else if ($inv_serie->existencia < $det->cantidad) {
                
                $status = "error";
                $problemas .= "<span class='label label-danger'>Error: Cantidad insuficiente | Serie: {$det->serie}</span> <br> <br>";
            }
        }
        return ['status' => $status, "problem" => $problemas];
    }

    public function corrige_existencias($serie, $id_bodega, $id_agenda){
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');

        $inv_serie = InvInventarioSerie::where('serie', $serie)->where('id_bodega', $id_bodega)->where('id_empresa', $id_empresa)->first();
        if( $inv_serie->cantidad_usos_kardex > 0){
            if( $inv_serie->existencia == 0){
                $inv_serie->update(['existencia' => 1]);
                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CORRIGE EXISTENCIAS DESDE APROBACION",
                    'dato_ant1'   => $serie,
                    'dato1'       => $id_bodega,
                    'dato_ant4'   => " ",
                    'dato2'       => ' ',
                ];
                Log_usuario::create($input_log);
            }
        }
    }

    public function devolver_nota_entrega(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_orden = $request['id'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nota_entrega = Nota_entrega::find($id_orden);

        if($nota_entrega->estado_generado == 1){
            return 'error';
        }

        $arr_log_nota = [
            'id_hc_receta'      => $nota_entrega->id_hc_receta,
            'id_nota_entrega'   => $nota_entrega->id,
            'descripcion'       => 'DEVUELTA EL: ' . date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        Log_nota_entrega::create($arr_log_nota);

        $arr_nota_entrega = [
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'estado'            => -1,
        ];
        $nota_entrega->update($arr_nota_entrega);
        return 'ok';
    }
}
 