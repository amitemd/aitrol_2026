<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_Arqueo_Caja;
use Sis_medico\Ct_Arqueo_Caja_Detalle;
use Session;
use Sis_medico\Ct_Denominacion;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Arqueo_Orden;

class Ct_Arqueo_CajaController extends Controller
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
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22]) == false) {
            return true;
        }
    }

    public function crear_arqueo(Request $request){
      
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $id_usuario   = Auth::user()->id;
        $id_empresa   = session()->get('id_empresa');
        $idusuario    = Auth::user()->id;
        $fecha_arqueo = $request->fecha;
        $caja         = $request->caja;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        //$ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision',$fecha_arqueo)->where('id_usuariocrea',$id_usuario)->get();
        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision', $fecha_arqueo)->where('caja',$caja)->get();
        
        $tefectivo = 0;
        $acum_efectivo = 0;
        $total = 0;
        foreach($ordenes as $value){
            $pagos = $value->pagos;
            $efectivo = 0;
            foreach($pagos as $pago){
                $total += $pago->valor;
                if($pago->tipo == '1'){
                $efectivo += $pago->valor;
                }
            }
            $acum_efectivo = $acum_efectivo + $efectivo;
        }

        $arqueo_caja = Ct_Arqueo_Caja::where('id_empresa', $id_empresa)->whereNull('id_usuario')->where('caja',$caja)->where('fecha_proceso', $fecha_arqueo)->first();
        if(is_null($arqueo_caja)){
            $arr_arqueo_caja = [ 
                'fecha_proceso'   => $fecha_arqueo,
                'id_empresa'      => $id_empresa,
                'id_usuario'      => null,
                'valor_efectivo'  => $acum_efectivo,  
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'vales'           => 0,
                'base'            => 30, 
                'caja'            => $caja, 
            ];
            //dd($arr_arqueo_caja);
            $id_arqueo = Ct_Arqueo_Caja::insertGetId($arr_arqueo_caja);  
            $arqueo_caja = Ct_Arqueo_Caja::find($id_arqueo); 

            $ctdenominaciones = Ct_Denominacion::where('estado',1)->get();
            foreach($ctdenominaciones as $ctdenominacion){
                $array_det = [
                    'id_arqueo_caja'   => $id_arqueo,
                    'id_denominacion'  => $ctdenominacion->id,
                    'denominacion'     => $ctdenominacion->nombre,
                    'cantidad'         => 0,
                    'valor'            => $ctdenominacion->valor, 
                    'estado'          => 1,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,  
                ];
                Ct_Arqueo_Caja_Detalle::create($array_det);
            }

            foreach($ordenes as $value){
                $pagos = $value->pagos;
                foreach($pagos as $pago){    
                    if($pago->tipo == '1'){
                        $arr_or_ef = [
                            'id_empresa'        => $id_empresa,
                            'fecha_proceso'     => $fecha_arqueo,
                            'id_orden'          => $value->id,
                            'id_arqueo'         => $id_arqueo,
                            'valor_efectivo'    => $pago->valor,
                            'estado'            => '1',
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomodifica'=> $id_usuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ]; 
                        
                        Ct_Arqueo_Orden::create($arr_or_ef);
                    }
                }
            }
            
        }
  
        else{

            /*$hoy = date('Y-m-d');
            if($arqueo_caja->fecha_proceso == $hoy){
                $arr_arqueo_caja = [ 
                    'valor_efectivo'  => $acum_efectivo, 
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $arqueo_caja->update($arr_arqueo_caja);
            }*/


        }
        //dd("hola");
        $detalles = $arqueo_caja->detalles;

        $total = 0;  $saldo = 0;  
        foreach ($detalles as $value) {
            $item = $value->cantidad * $value->valor;
            $total += $item;
        }  

        $saldo = $arqueo_caja->valor_efectivo + $arqueo_caja->base - ( $total + $arqueo_caja->vales );
        
        return $arqueo_caja->id;
        //return view('contable/arqueo_caja/create',['arqueo_caja' => $arqueo_caja,  'ordenes'=> $ordenes, 'detalles'=>$detalles, 'saldo' => $saldo, 'total' => $total ] );
    }

    public function editar_arqueo($id){
       
         $arqueo_caja = Ct_Arqueo_Caja::find($id);
         //dd($arqueo);
         $detalles = $arqueo_caja->detalles;

         $total = 0;  $saldo = 0;  
        
         foreach ($detalles as $value) {
             $item = $value->cantidad * $value->valor;
             $total += $item;
         }  
 
         $saldo = $arqueo_caja->valor_efectivo + $arqueo_caja->base - ( $total + $arqueo_caja->vales );
         //dd("vic vale popo");
         return view('contable/arqueo_caja/create',['arqueo_caja' => $arqueo_caja, 'detalles'=>$detalles, 'saldo' => $saldo, 'total' => $total ] );
    }
    public function update_arqueo(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $detalle= Ct_Arqueo_Caja_Detalle::find( $request['id_detalle']) ;
        $detalle->cantidad          = $request['valor'];
        $detalle->estado               = 1;
        $detalle->ip_modificacion      = $ip_cliente;
        $detalle->id_usuariomod        = $idusuario;
        $detalle->save();

        $arqueo = $detalle->arqueo;

        $detalles = $arqueo->detalles;

        $total = 0;    
        foreach ($detalles as $value) {
            $item = $value->cantidad * $value->valor;
            $total += $item;
        }   

        $saldo = $arqueo->valor_efectivo + $arqueo->base - ( $total + $arqueo->vales ); 

        return ['total' => number_format($total, 2, ',', ' '), 'saldo' => number_format($saldo, 2, ',', ' ')];

    } 

    public function update_cabecera(Request $request){

        $id_usuario   = Auth::user()->id;
        $id_empresa   = session()->get('id_empresa');
        $idusuario    = Auth::user()->id;
        $fecha_arqueo = $request->fecha_arqueo;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id    = $request->id;
        $vales = $request->vales;
        $base  = $request->base;

        $arqueo = Ct_Arqueo_Caja::find($id);

        $arr = [
            'vales' => $vales,
            'base'  => $base,
        ];
        
        $arqueo->update($arr);

        $detalles = $arqueo->detalles;

        $total = 0;  $saldo = 0;  
        foreach ($detalles as $value) {
            $item = $value->cantidad * $value->valor;
            $total += $item;
        }  

        $saldo = $arqueo->valor_efectivo + $base - ( $total + $vales );

        return ['total' => number_format($total, 2, ',', ' '), 'saldo' => number_format($saldo, 2, ',', ' ')];
    }     


    public function index_arqueo(Request $request){
      
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $id_usuario   = Auth::user()->id;
        $id_empresa   = session()->get('id_empresa');
        $fecha_arqueo = $request->fecha;

        //dd($acum_efectivo, $ordenes, $fecha_arqueo, $id_usuario);
        $arqueos = Ct_Arqueo_Caja::where('id_empresa', $id_empresa)->where('id_usuario', $id_usuario)->where('fecha_proceso', $fecha_arqueo)->get();
    
        //dd($detalles);
        //dd($ordenes);
        return view('contable/arqueo_caja/index_arqueo',['arqueos' => $arqueos] );
    } 

    
    public function crear_arqueo_user (Request $request){
        //POR USUARIO
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        //$id_usuario   = Auth::user()->id;
        $id_empresa   = session()->get('id_empresa');
        $idusuario    = Auth::user()->id;
        $fecha_arqueo = $request->fecha;
        $caja         = $request->caja;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision',$fecha_arqueo)->where('caja',$caja)->where('id_usuariocrea',$idusuario)->get();
        //$ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision', $fecha_arqueo)->where('caja',$caja)->get();
        $tefectivo = 0;
        $acum_efectivo = 0;
        $total = 0;
        foreach($ordenes as $value){  
            $pagos = $value->pagos;
            $efectivo = 0;
            foreach($pagos as $pago){
                $total += $pago->valor;
                if($pago->tipo == '1'){
                $efectivo += $pago->valor;
                }
            }
            $acum_efectivo = $acum_efectivo + $efectivo;
        }

        $arqueo_caja = Ct_Arqueo_Caja::where('id_empresa', $id_empresa)->where('id_usuario',$idusuario)->where('caja',$caja)->where('fecha_proceso', $fecha_arqueo)->first();
        //dd($arqueo_caja);
        if(is_null($arqueo_caja)){
            $arr_arqueo_caja = [ 
                'fecha_proceso'   => $fecha_arqueo,
                'id_empresa'      => $id_empresa,
                'id_usuario'      => $idusuario,
                'valor_efectivo'  => $acum_efectivo,  
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'vales'           => 0,
                'base'            => 30, 
                'caja'            => $caja, 
            ];
            //dd($arr_arqueo_caja);
            $id_arqueo = Ct_Arqueo_Caja::insertGetId($arr_arqueo_caja);  
            $arqueo_caja = Ct_Arqueo_Caja::find($id_arqueo); 

            $ctdenominaciones = Ct_Denominacion::where('estado',1)->get();
            foreach($ctdenominaciones as $ctdenominacion){
                $array_det = [
                    'id_arqueo_caja'   => $id_arqueo,
                    'id_denominacion'  => $ctdenominacion->id,
                    'denominacion'     => $ctdenominacion->nombre,
                    'cantidad'         => 0,
                    'valor'            => $ctdenominacion->valor, 
                    'estado'          => 1,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,  
                ];
                Ct_Arqueo_Caja_Detalle::create($array_det);
            }

            foreach($ordenes as $value){
                $pagos = $value->pagos;
                foreach($pagos as $pago){    
                    if($pago->tipo == '1'){
                        $arr_or_ef = [
                            'id_empresa'        => $id_empresa,
                            'fecha_proceso'     => $fecha_arqueo,
                            'id_orden'          => $value->id,
                            'id_arqueo'         => $id_arqueo,
                            'valor_efectivo'    => $pago->valor,
                            'estado'            => '1',
                            'id_usuario'    => $idusuario,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomodifica'=> $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ]; 
                        
                        Ct_Arqueo_Orden::create($arr_or_ef);
                    }
                }
            }
            
        }
  
        else{

            /*$hoy = date('Y-m-d');
            if($arqueo_caja->fecha_proceso == $hoy){
                $arr_arqueo_caja = [ 
                    'valor_efectivo'  => $acum_efectivo, 
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $arqueo_caja->update($arr_arqueo_caja);
            }*/


        }
        //dd("hola");
        $detalles = $arqueo_caja->detalles;

        $total = 0;  $saldo = 0;  
        foreach ($detalles as $value) {
            $item = $value->cantidad * $value->valor;
            $total += $item;
        }  

        $saldo = $arqueo_caja->valor_efectivo + $arqueo_caja->base - ( $total + $arqueo_caja->vales );
        
        return $arqueo_caja->id;
        //return view('contable/arqueo_caja/create',['arqueo_caja' => $arqueo_caja,  'ordenes'=> $ordenes, 'detalles'=>$detalles, 'saldo' => $saldo, 'total' => $total ]);
    }

    public function recalcular_valor_efectivo_arqueo($id){
        //dd("hola");
        $arqueo = Ct_Arqueo_Caja::find($id);

        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision',$arqueo->fecha_proceso)->where('caja',$arqueo->caja);
        if(!is_null($arqueo->id_usuario)){
            $ordenes = $ordenes->where('id_usuariocrea',$arqueo->id_usuario);
        }
        $ordenes = $ordenes->get();
        $idusuario    = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        //dd($ordenes);
        //$ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereDate('fecha_emision', $fecha_arqueo)->where('caja',$caja)->get();
        $tefectivo = 0;
        $acum_efectivo = 0;
        $total = 0;
        foreach($ordenes as $value){  
            $pagos = $value->pagos;
            $efectivo = 0;
            foreach($pagos as $pago){
                $total += $pago->valor;
                if($pago->tipo == '1'){
                $efectivo += $pago->valor;
                $arqueo_orden = Ct_Arqueo_Orden::where('id_arqueo',$arqueo->id)->where('id_orden',$value->id)->first();
                if(!is_null($arqueo_orden)){
                  $arr_arqueo_orden=[
                    'id_orden'          => $value->id,
                    'id_arqueo'         => $arqueo->id,
                    'valor_efectivo'    => $pago->valor,
                    'estado'            => '1',
                    'id_usuario'        => $idusuario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomodifica'=> $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => 'VALOR RECALCULADO',
                  ];
                  $arqueo_orden->update($arr_arqueo_orden);  
                }else{
                    $arr_arqueo_orden=[
                        'id_empresa'        => $arqueo->id_empresa,
                        'fecha_proceso'     => date('Y-m-d'),
                        'id_orden'          => $value->id,
                        'id_arqueo'         => $arqueo->id,
                        'valor_efectivo'    => $pago->valor,
                        'estado'            => '1',
                        'id_usuario'        => $idusuario,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomodifica'=> $idusuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => 'VALOR RECALCULADO',
                      ];  
                      Ct_Arqueo_Orden::create($arr_arqueo_orden);
                }
                
                }
            }
            $acum_efectivo = $acum_efectivo + $efectivo;
        }
        //$arqueo = Ct_Arqueo_Caja::find($id);
        //$arqueo_caja = $arqueo->id;   
        //dd($acum_efectivo);
       
        $arr_arqueo_caja = [ 
            'valor_efectivo'  => $acum_efectivo, 
            'ip_modificacion' => 'VALOR RECALCULADO',
            'id_usuariomod'   => $idusuario,
        ];
                //dd($arr_arqueo_caja);
        $arqueo->update($arr_arqueo_caja);


        return redirect(route('ct_arqueo_caja.editar_arqueo',['arqueo'=> $arqueo->id]));
    }

 

}