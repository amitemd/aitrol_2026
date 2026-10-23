<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Agenda;
use Sis_medico\Producto;
use Sis_medico\Empresa;
use Sis_medico\Archivo_Plano_Cabecera;

class ReporteCostosVentasController extends Controller
{
    protected $redirectTo = '/dashboard';
    /**
    * Create a new controller instance.
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index_costos_ventas(Request $request){

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        if (!isset($request['fecha_desde']) or is_null($request['fecha_desde'])) {
            $request['fecha_desde'] = "01-01-" . date('Y');
        }
        if (!isset($request['fecha_hasta']) or is_null($request['fecha_hasta'])) {
            $request['fecha_hasta'] = "31-12-" . date('Y');
        }
        // dd($request->all());
        $fecha_desde = date("Y-m-d", strtotime($request['fecha_desde'])); 
        $fecha_hasta = date("Y-m-d", strtotime($request['fecha_hasta']));
        
        
        
        $planillas = DB::table('planilla_detalle as pd')
        ->join('planilla_cabecera as pc', 'pd.id_planilla_cabecera', 'pc.id')
        ->where('pc.aprobado', 1)
        ->where('pd.check', 1)
        ->where('pd.estado', 1)
        ->whereNotNull('pc.id_asiento_cabecera')
        ->groupby('pd.id_planilla_cabecera')
        //->sum('pd.precio')
        ->select('pc.id','pc.fecha','pc.observacion','pc.id_hc_procedimiento','pc.id_agenda', 'pd.precio', 'pc.aprobado', 'pd.estado','pd.check','pc.id_asiento_cabecera','pc.nota_entrega as nota_entrega');

        
        if($fecha_desde!=null && $fecha_hasta!=null){
            $planillas= $planillas->whereBetween('pc.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if($fecha_desde!=null){
            $planillas= $planillas->where('pc.fecha','>=',$fecha_desde);
        }
        if($fecha_hasta!=null){
            $planillas= $planillas->where('pc.fecha','<=',$fecha_hasta);
        }
        $planillas = $planillas->get();
        // dd($fecha_desde, $fecha_hasta);
       // return $planillas;
       // dd($planillas);


        return view('contable/costos_ventas/index',['planillas'=>$planillas, 'empresa'=>$empresa,'fecha_desde'=>$fecha_desde ,'fecha_hasta'=>$fecha_hasta]);
    }

    public function recalcular_valor_ap_cab(){

        $cabeceras_ap = Archivo_Plano_Cabecera::where('estado',1)->where('leido',0)->get();
        foreach($cabeceras_ap as $archivo_plano){
            //dd($archivo_plano);
            $honor_medicos = Db::table('archivo_plano_detalle as apd')
            ->where('id_ap_cabecera', $archivo_plano->id)
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->orderby('apd.porcentaje_honorario', 'desc')
            ->orderby('apt.secuencia', 'asc')
            ->where('apt.tipo_ex', 'HME')
            //->orWhere('apt.tipo_ex','P')
            ->where('apd.estado', '1')
            ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
            ->get();

            $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

            $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

            $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

            $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

            $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

            $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
            $liquidacion_total=0;
            $total_hm = 0;
			foreach ($honor_medicos as $value1){
                $total_hm += $value1->total;
            }
            $tota_med = 0;
            foreach ($medicinas as $value2){
                $tota_med += $value2->total;
            }
            $totalins = 0; 
			foreach ($insumos as $value3){
                $total_item = round($value3->subtotal, 2) + round($value3->porcentaje10, 2) + ($value3->subtotal * $value3->porcentaje_iva);
                $totalins += $total_item;        
            }
            $total_lab = 0; 
            foreach ($laboratorio as $value4){
                $total_lab += $value4->total;
            }
            $total_imagen = 0; 
            foreach ($imagen as $value5){
                $total_imagen += $value5->total; 
            }       
            $total_serv = 0; 
            foreach ($servicios_ins as $value6){
                $total_serv += $value6->total; 
            }
            $total_equi = 0;
            foreach ($equipos as $value7){
                $total_equi += $value7->total; 
            }              

            $liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $tota_med + $total_hm;	
            
            if(!is_null($archivo_plano)){
                $archivo_plano->update([
                    'valor'  => $liquidacion_total,
                    'total_hm' => $total_hm,
                    'total_ins' => $totalins,
                    'total_lab' => $total_lab,
                    'total_imagen' => $total_imagen,
                    'total_serv' => $total_serv,
                    'total_equi' => $total_equi,
                    'leido'      => 1,
                ]);
            }
        }
        return "elt";
    }

}    