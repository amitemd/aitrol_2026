<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\ApProcedimiento;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Equipo_Historia;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examenes;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_movimiento;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\Tipo;
use Sis_medico\User;

class EstadisticosPlanoController extends Controller
{
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 6, 7, 9, 11, 20,3,14)) == false) {
            return true;
        }
    }


    public function index(Request $request)
    {
        //dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //seguro IESS Y MSP IEES: 2 Y MSP: 5
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        //dd($fechaini);
        if(is_null($fechaini)){
            $fechaini= '2021-01-01';
        }
        if(is_null($fechafin)){
            $fechafin= date('Y-m-d');
        }
        //$archivoplano = Archivo_Plano_Cabecera::where('estado', '1');
        $procedimiento = Procedimiento::where('estado',1)->whereNotNull('id_grupo_procedimiento')->get();
        $procedimiento2 = Procedimiento::where('estado',1)->whereNotNull('id_grupo_procedimiento')->get();
        $id_procedimiento = $request['id_procedimiento'];

        $archivoplano =  DB::table('archivo_plano_cabecera as ap_cabecera')->where('ap_cabecera.estado','1');
        $archivoplano2 =  DB::table('archivo_plano_cabecera as ap_cabecera')->where('ap_cabecera.estado','1');
        //dd($archivoplano);
        if ($fechaini == null) {
            $archivoplano = $archivoplano->whereDate('ap_cabecera.fecha_ing', '<=', $fechafin);
            $archivoplano2 = $archivoplano2->whereDate('ap_cabecera.fecha_ing', '<=', $fechafin);
        } else {
            $archivoplano = $archivoplano->wherebetween('ap_cabecera.fecha_ing', [$fechaini . ' 00:00:00', $fechafin . ' 23:59:59']);
            $archivoplano2 = $archivoplano2->wherebetween('ap_cabecera.fecha_ing', [$fechaini . ' 00:00:00', $fechafin . ' 23:59:59']);
        }
        if ($id_procedimiento != null) {
            //$proc = DB::table('procedimiento')->where('estado', '<>', '0')->where('id', $id_procedimiento)->first();
            //$archivoplano = $archivoplano->where('ap_cabecera.procedimiento','like' ,'%'.$proc->nombre.'%');

            //$proc2 = DB::table('procedimiento')->where('estado', '<>', '0')->where('tipo_procedimiento', $id_procedimiento)->first();
            $archivoplano2 = $archivoplano2->where('ap_cabecera.tipo_procedimiento',$id_procedimiento);//dd($archivoplano2->get());
        }
       
        $archivoplano = $archivoplano->groupBy('ap_cabecera.tipo_procedimiento')->select(DB::raw("SUM(ap_cabecera.valor) as total, count(*) as cantidad"), 'ap_cabecera.tipo_procedimiento as nombre');
        $archivoplano = $archivoplano->get();

        $archivoplano2 = $archivoplano2->groupBy('ap_cabecera.tipo_procedimiento')->groupBy('ap_cabecera.procedimiento')->select(DB::raw("SUM(ap_cabecera.valor) as total, count(*) as cantidad"), 'ap_cabecera.tipo_procedimiento', 'ap_cabecera.procedimiento as nombre');
        $archivoplano2 = $archivoplano2->get();
        //dd($archivoplano2);

        //dd($fechaini, $fechafin);
        return view('archivo_plano/estadisticos/index', ['archivo_plano' => $archivoplano, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'procedimiento' => $procedimiento, 'id_procedimiento' => $id_procedimiento,
            'archivo_plano2' => $archivoplano2, 'procedimiento' => $procedimiento]);
    }

    public function apestadisticos()
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $archivoplano_iess_gastro = DB::table('archivo_plano_cabecera as ap_cabecera')
                        ->join('archivo_plano_detalle as ap_detalle', 'ap_detalle.id_ap_cabecera', 'ap_cabecera.id')
                        ->join('empresa as  e','e.id','ap_cabecera.id_empresa')
                        ->where('ap_cabecera.estado', '1')
                        ->where('ap_detalle.estado', '1')
                        ->where('ap_cabecera.id_seguro',2)
                        ->where('ap_cabecera.id_empresa','0992704152001')
                        ->groupBy('e.nombre_corto','ap_cabecera.mes_plano')->select('e.nombre_corto as empresa','ap_cabecera.mes_plano as mes',DB::raw("SUM(ap_detalle.total) as total, count(*) as cantidad"))
                        ->get();
        $arr_iess_gastro = [];
        foreach ($archivoplano_iess_gastro as $value) {
            $arr_iess_gastro[$value->mes] = $value->total;
        } 
        $archivoplano_iess_robles = DB::table('archivo_plano_cabecera as ap_cabecera')
                        ->join('archivo_plano_detalle as ap_detalle', 'ap_detalle.id_ap_cabecera', 'ap_cabecera.id')
                        ->join('empresa as  e','e.id','ap_cabecera.id_empresa')
                        ->where('ap_cabecera.estado', '1')
                        ->where('ap_detalle.estado', '1')
                        ->where('ap_cabecera.id_seguro',2)
                        ->where('ap_cabecera.id_empresa','1307189140001')
                        ->groupBy('e.nombre_corto','ap_cabecera.mes_plano')->select('e.nombre_corto as empresa','ap_cabecera.mes_plano as mes',DB::raw("SUM(ap_detalle.total) as total, count(*) as cantidad"))
                        ->get();
        $arr_iess_robles = [];
        foreach ($archivoplano_iess_robles as $value) {
            $arr_iess_robles[$value->mes] = $value->total;
        }
        $archivoplano_msp_gastro = DB::table('archivo_plano_cabecera as ap_cabecera')
                        ->join('archivo_plano_detalle as ap_detalle', 'ap_detalle.id_ap_cabecera', 'ap_cabecera.id')
                        ->join('empresa as  e','e.id','ap_cabecera.id_empresa')
                        ->where('ap_cabecera.estado', '1')
                        ->where('ap_detalle.estado', '1')
                        ->where('ap_cabecera.id_seguro',5)
                        ->where('ap_cabecera.id_empresa','0992704152001')
                        ->groupBy('e.nombre_corto','ap_cabecera.mes_plano')->select('e.nombre_corto as empresa','ap_cabecera.mes_plano as mes',DB::raw("SUM(ap_detalle.total) as total, count(*) as cantidad"))
                        ->get();
        $arr_msp_gastro = [];
        foreach ($archivoplano_msp_gastro as $value) {
            $arr_msp_gastro[$value->mes] = $value->total;
        } 
        $archivoplano_msp_crm = DB::table('archivo_plano_cabecera as ap_cabecera')
                        ->join('archivo_plano_detalle as ap_detalle', 'ap_detalle.id_ap_cabecera', 'ap_cabecera.id')
                        ->join('empresa as  e','e.id','ap_cabecera.id_empresa')
                        ->where('ap_cabecera.estado', '1')
                        ->where('ap_detalle.estado', '1')
                        ->where('ap_cabecera.id_seguro',5)
                        ->where('ap_cabecera.id_empresa','1307189140001')
                        ->groupBy('e.nombre_corto','ap_cabecera.mes_plano')->select('e.nombre_corto as empresa','ap_cabecera.mes_plano as mes',DB::raw("SUM(ap_detalle.total) as total, count(*) as cantidad"))
                        ->get();
        $arr_msp_crm = [];
        foreach ($archivoplano_msp_crm as $value) {
            $arr_msp_crm[$value->mes] = $value->total;
        } 

        dd($archivoplano_iess_gastro,$archivoplano_iess_robles, $archivoplano_msp_gastro);

                   
        
        //return $archivoplano_iess;

        return view('archivo_plano/estadisticos/index_total', ['arr_iess_gastro' => $arr_iess_gastro, 'arr_iess_robles' => $arr_iess_robles, 'arr_msp_gastro' => $arr_msp_gastro ]);
    }

    public function orden(Request $request){
        if ($this->rol()) {
            return response()->view('errors.404');
        } 

        //dd("dasda");
        //dd($consulta);
        //seguro IESS Y MSP IEES: 2 Y MSP: 5
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        $seguro= $request['seguro'];
        $validate_anio= $request['anio_validate'];
        if(is_null($validate_anio)){
            $validate_anio=0;
        }
        if($validate_anio==1){
            $tipo_pagos=DB::table('ct_orden_venta as ct_orden')
                                ->where('ct_orden.estado', 1)
                                ->join('ct_orden_venta_pago as pago','pago.id_orden','ct_orden.id')
                                ->join('ct_tipo_pago as forma','forma.id','pago.tipo')
                                ->groupBy('pago.tipo');
            //dd($fechaini);
            $anio= $request['anio'];
            if(is_null($anio)){
                $anio=date('Y');
                //dd($anio);
            }
            $venorden = DB::table('ct_orden_venta as ct_orden')
                            ->where('ct_orden.estado', 1)
                            ->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')
                            ->join('seguros as s','s.id','ct_orden.id_seguro');
            if(!is_null($request->seguro)){
                $venorden= $venorden->whereIn('ct_orden.id_seguro',$seguro);
                $tipo_pagos= $tipo_pagos->whereIn('ct_orden.id_seguro',$seguro);
            }
            $id_seguro= $request->seguro;
            $venorden= $venorden->whereYear('ct_orden.fecha_emision',$anio);
            $arraymes=['0'=>'01','1'=>'02','2'=>'03','3'=>'04','4'=>'05','5'=>'06','6'=>'07','7'=>'08','8'=>'09','9'=>'10','10'=>'11','11'=>'12'];
            $venfinal=[];
            $querymeses=[];
            $s=0;
            for ($i=1; $i < 13; $i++) { 
                 
                if($i < 10){
                    $n = '0'.$i;
                }else{
                    $n = $i;                    
                }
                
                //dd($venorden2->toSql());
                $venordenes_final=  DB::table('ct_orden_venta as ct_orden')
                                    ->where('ct_orden.estado', 1)
                                    ->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')
                                    ->join('seguros as s','s.id','ct_orden.id_seguro')
                                    ->where('s.tipo','<>','0');
                if(!is_null($request->seguro)){
                    $venordenes_final= $venordenes_final->whereIn('ct_orden.id_seguro',$seguro);
                }                
                $venordenes_final= $venordenes_final->whereYear('ct_orden.fecha_emision',$anio)->whereMonth('ct_orden.fecha_emision', $n)->groupBy('ct_orden.id_seguro')->select(DB::raw('SUM(detalles.total) as total'),'s.nombre as nombre_seguro')->get();       
            
                array_push($querymeses,$venordenes_final);
               
            }
            //dd($venfinal);
            //dd($querymeses);
            $tipo_pagos= $tipo_pagos->whereYear('ct_orden.fecha_emision',$anio)
                        ->groupBy('pago.tipo')
                        ->select(DB::raw('SUM(pago.valor) as valor'),'forma.nombre as nombre_pagos')->get();
            $seguros= Seguro::all();
            //dd($venordenes);
            //dd($venfinal);
            //dd($venorden,$tipo_pagos);
            return view('contable/facturacion/estadisticos', ['venorden' => $venorden,'query_meses'=>$querymeses,'anio'=>$anio,'validate'=>$validate_anio,'tipo_pagos'=>$tipo_pagos, 'fechaini' => $fechaini, 'fechafin' => $fechafin,'id_seguro'=>$id_seguro, 'seguros' => $seguros]);
        }else{
            $tipo_pagos=DB::table('ct_orden_venta as ct_orden')
                            ->where('ct_orden.estado', 1)
                            ->join('ct_orden_venta_pago as pago','pago.id_orden','ct_orden.id')
                            ->join('ct_tipo_pago as forma','forma.id','pago.tipo')
                            ->groupBy('pago.tipo');
            //dd($fechaini);
            if(is_null($fechaini)){
                $fechaini= date('Y-m-d',strtotime($fechaini."- 1 month"));
            }
            if(is_null($fechafin)){
                $fechafin= date('Y-m-d');
            }
            $venorden = DB::table('ct_orden_venta as ct_orden')
                            ->where('ct_orden.estado', 1)
                            ->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')
                            ->join('seguros as s','s.id','ct_orden.id_seguro');
            if(!is_null($request->seguro)){
                $venorden= $venorden->whereIn('ct_orden.id_seguro',$seguro);
                $tipo_pagos= $tipo_pagos->whereIn('ct_orden.id_seguro',$seguro);
            }
            $id_seguro= $request->seguro;
            $venorden= $venorden->where('s.tipo','<>','0')->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('ct_orden.id_seguro')->select(DB::raw('SUM(detalles.total) as total'),'s.nombre')->get();
            $tipo_pagos= $tipo_pagos->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('pago.tipo')->select(DB::raw('SUM(pago.valor) as valor'),'forma.nombre as nombre_pagos')->get();
            $seguros= Seguro::all();
            
            //dd($venorden,$tipo_pagos);
            return view('contable/facturacion/estadisticos', ['venorden' => $venorden,'validate'=>$validate_anio,'tipo_pagos'=>$tipo_pagos, 'fechaini' => $fechaini, 'fechafin' => $fechafin,'id_seguro'=>$id_seguro, 'seguros' => $seguros]);
        }
       
    }
    public function hc4(Request $request){
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("dasda");
        //dd($consulta);
        //seguro IESS Y MSP IEES: 2 Y MSP: 5
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        $seguro= $request['seguro'];
        $validate_anio= $request['anio_validate'];
        if(is_null($validate_anio)){
            $validate_anio=0;
        }
        if($validate_anio==1){
            $tipo_pagos=DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_pago as pago','pago.id_orden','ct_orden.id')->join('ct_tipo_pago as forma','forma.id','pago.tipo')->groupBy('pago.tipo');
            //dd($fechaini);
            $anio= $request['anio'];
            if(is_null($anio)){
                $anio=date('Y');
                //dd($anio);
            }
            $venorden = DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')->join('seguros as s','s.id','ct_orden.id_seguro');
            if(!is_null($request->seguro)){
                $venorden= $venorden->whereIn('ct_orden.id_seguro',$seguro);
                $tipo_pagos= $tipo_pagos->whereIn('ct_orden.id_seguro',$seguro);
            }
            $id_seguro= $request->seguro;
            $venorden= $venorden->whereYear('ct_orden.fecha_emision',$anio);
            $arraymes=['0'=>'01','1'=>'02','2'=>'03','3'=>'04','4'=>'05','5'=>'06','6'=>'07','7'=>'08','8'=>'09','9'=>'10','10'=>'11','11'=>'12'];
            $venfinal=[];
            $querymeses=[];
            $s=0;
            for ($i=1; $i < 13; $i++) { 
                 
                if($i < 10){
                    $n = '0'.$i;
                }else{
                    $n = $i;                    
                }
                
                //dd($venorden2->toSql());
                $venordenes_final=  DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')->join('seguros as s','s.id','ct_orden.id_seguro')->where('s.tipo','<>','0');
                if(!is_null($request->seguro)){
                    $venordenes_final= $venordenes_final->whereIn('ct_orden.id_seguro',$seguro);
                }                
                $venordenes_final= $venordenes_final->whereYear('ct_orden.fecha_emision',$anio)->whereMonth('ct_orden.fecha_emision', $n)->groupBy('ct_orden.id_seguro')->select(DB::raw('SUM(detalles.total) as total'),'s.nombre as nombre_seguro')->get();       
            
                array_push($querymeses,$venordenes_final);
               
            }
            //dd($venfinal);
            //dd($querymeses);
            $tipo_pagos= $tipo_pagos->whereYear('ct_orden.fecha_emision',$anio)->groupBy('pago.tipo')->select(DB::raw('SUM(pago.valor) as valor'),'forma.nombre as nombre_pagos')->get();
            $seguros= Seguro::all();
            //dd($venordenes);
            //dd($venfinal);
            //dd($venorden,$tipo_pagos);
            return view('contable/facturacion/estadisticoshc4', ['venorden' => $venorden,'query_meses'=>$querymeses,'anio'=>$anio,'validate'=>$validate_anio,'tipo_pagos'=>$tipo_pagos, 'fechaini' => $fechaini, 'fechafin' => $fechafin,'id_seguro'=>$id_seguro, 'seguros' => $seguros]);
        }else{
            $tipo_pagos=DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_pago as pago','pago.id_orden','ct_orden.id')->join('ct_tipo_pago as forma','forma.id','pago.tipo')->groupBy('pago.tipo');
            //dd($fechaini);
            if(is_null($fechaini)){
                $fechaini= date('Y-m-d',strtotime($fechaini."- 1 month"));
            }
            if(is_null($fechafin)){
                $fechafin= date('Y-m-d');
            }
            $venorden = DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')->join('seguros as s','s.id','ct_orden.id_seguro');
            if(!is_null($request->seguro)){
                $venorden= $venorden->whereIn('ct_orden.id_seguro',$seguro);
                $tipo_pagos= $tipo_pagos->whereIn('ct_orden.id_seguro',$seguro);
            }
            $id_seguro= $request->seguro;
            $venorden= $venorden->where('s.tipo','<>','0')->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('ct_orden.id_seguro')->select(DB::raw('SUM(detalles.total) as total'),'s.nombre')->get();
            $tipo_pagos= $tipo_pagos->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('pago.tipo')->select(DB::raw('SUM(pago.valor) as valor'),'forma.nombre as nombre_pagos')->get();
            $seguros= Seguro::all();
            
            //dd($venorden,$tipo_pagos);
            return view('contable/facturacion/estadisticoshc4', ['venorden' => $venorden,'validate'=>$validate_anio,'tipo_pagos'=>$tipo_pagos, 'fechaini' => $fechaini, 'fechafin' => $fechafin,'id_seguro'=>$id_seguro, 'seguros' => $seguros]);
        }
       
    }

    public function masivo_carga_archivo_plano(){

        $cabeceras = Archivo_Plano_Cabecera::where('estado','1')->whereNull('tipo_procedimiento')->get();
        foreach ($cabeceras as $cabecera) {$cont=0;
            $detalles = Archivo_Plano_Detalle::where('id_ap_cabecera',$cabecera->id)->where('estado','1')->orderBy('codigo','desc')->get();
            $txt_procedimiento = null;$tipo_proc = null;$proc_anterior='';$total = 0;
            
            foreach ($detalles as $detalle) {
                $total += $detalle->total_solicitado_usd;
                if($detalle->tipo=='P' || $detalle->tipo=='PA' || $detalle->tipo=='IM'){
                    $procedimiento = ApProcedimiento::where('tipo',$detalle->tipo)->where('codigo',$detalle->codigo)->first();
                    if($procedimiento->tipo_procedimiento!=null){
                        //$tipo_proc = $procedimiento->tipo_procedimiento;
                        if($txt_procedimiento==''){
                            $txt_procedimiento = $procedimiento->procedimiento;
                            $proc_anterior = $procedimiento->procedimiento; 
                        }else{
                            if ($proc_anterior === $procedimiento->procedimiento) {
                                
                            }else{
                                if($procedimiento->procedimiento!=null){
                                    $txt_procedimiento = $txt_procedimiento.'+'.$procedimiento->procedimiento; 
                                    $proc_anterior = $procedimiento->procedimiento;
                                }         
                            }
                        }

                        if($cont==0){
                            $tipo_proc = $procedimiento->tipo_procedimiento;    
                        }$cont++;
                        if($tipo_proc=='ENDOSCOPIA DIGESTIVA ALTA'){
                            
                            if($tipo_proc!=$procedimiento->tipo_procedimiento){
                                $tipo_proc = $procedimiento->tipo_procedimiento;

                            }
                               
                        } 


                    }
                }
                        
                
            }

                

            $cabecera->update(['tipo_procedimiento' => $tipo_proc, 'procedimiento' => $txt_procedimiento, 'valor' => $total]);
            
        }

    }
    public function labs_estadisticos(Request $request){
         $examenes= [];
         $examenesanio=[];
         $fecha=$request['fecha'];
         if(is_null($fecha)){
             $fecha=date('Y-m-d');
         }
         $fechafin= $request['fechafin'];
         if(is_null($fechafin)){
             $fechafin=date('Y-m-d');
         }
         if(!is_null($request['exam'])){
            $examenes= Examen_Detalle::join('examen as e','e.id','examen_detalle.id_examen')
            ->join('examen_orden as orden','orden.id','examen_detalle.id_examen_orden')
            ->where('orden.estado','>','0')
            ->whereIn('e.id',$request['exam'])
            ->where('e.estado','1');
            $examenesanio= Examen_Detalle::join('examen as e','e.id','examen_detalle.id_examen')
            ->join('examen_orden as orden','orden.id','examen_detalle.id_examen_orden')
            ->where('orden.estado','>','0')
            ->whereIn('e.id',$request['exam'])
            ->where('e.estado','1');
            if(in_array('-10',$request['exam'])){
                $examenes= Examen_Detalle::join('examen as e','e.id','examen_detalle.id_examen')
                ->join('examen_orden as orden','orden.id','examen_detalle.id_examen_orden')
                ->where('orden.estado','>','0')
                ->where('e.estado','1');
                $examenesanio= Examen_Detalle::join('examen as e','e.id','examen_detalle.id_examen')
                ->join('examen_orden as orden','orden.id','examen_detalle.id_examen_orden')
                ->where('orden.estado','>','0')
                ->where('e.estado','1');
            }
           
            if(!is_null($fecha)){
               $examenes= $examenes->whereBetween('orden.fecha_orden',[$fecha.' 00:00:00',$fechafin.' 23:59:59']);
               $examenesanio= $examenesanio->whereYear('orden.fecha_orden',date('Y',strtotime($fecha)));
            }
            $examenes= $examenes->groupBy('examen_detalle.id_examen')
            ->select(DB::raw('SUM(examen_detalle.valor) as total'),DB::raw('SUM(examen_detalle.cantidad) as cantidad'),'e.descripcion')->get();
            $examenesanio= $examenesanio->groupBy('examen_detalle.id_examen')
            ->select(DB::raw('SUM(examen_detalle.valor) as total'),DB::raw('SUM(examen_detalle.cantidad) as cantidad'),'e.descripcion')->get();
         }
         $id_exam= $request['exam'];

         //dd($examenes);
         $listadoex= Examen::where('estado','1')->get();
        
         return view('laboratorio.estadistico.new',['examenes'=>$examenes,'examenesanio'=>$examenesanio,'fechafin'=>$fechafin,'listadoex'=>$listadoex,'id_exam'=>$id_exam,'fecha'=>$fecha]);
    }

   
}
