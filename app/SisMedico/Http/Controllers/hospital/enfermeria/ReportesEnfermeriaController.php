<?php

namespace Sis_medico\Http\Controllers\hospital\enfermeria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Tipo_Aporte;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Agenda;
use Sis_medico\Producto;

class ReportesEnfermeriaController extends Controller
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
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 8,  19, 20, 21, 22)) == false) {
            return true;
        }
    }
    public function pdf_medicamentos($id_planilla){
        $planilla_cabecera = Planilla::find($id_planilla);
        $cab_fecha = [];
        /*$detalle_planilla = Planilla_Detalle::where('id_planilla_cabecera',$id_planilla)->orderby('created_at','desc')->get();
        
        $agr_insumos = Planilla_Detalle::where('id_planilla_cabecera',$id_planilla)->groupBy("codigo")->select("codigo")->get();
        foreach($detalle_planilla as $det){
            $fecha = explode(" ", $det->created_at);
            if(!isset($cab_fecha[$fecha[0]])){
                $cab_fecha[$fecha[0]]="";
            }
        }*/
        $detalles_planilla = $planilla_cabecera->detalles;
        $cont = 0;
        $vfecha ="";
        $cont2 = 0;
        
        foreach ($detalles_planilla as $det){
            $fecha = $det->created_at;
            $hora = date('H:i', strtotime($fecha));
            $fecha = date('Y-m-d', strtotime($fecha));
            
            $usuario_cre = $det->usuario;
            $iniciales = substr($usuario_cre->nombre1,0,1).substr($usuario_cre->apellido1,0,1);

            if($vfecha != $fecha){
               $prod = [];
               $cont = 0;
               $vfecha = $fecha;
            }
            
            $productos = $det->codigo;
            //dd($productos);
            $prod[$cont] = [$det->codigo, $hora ,$iniciales, "ENFERMERO" ];

            $cont ++;
           
            $cab_fecha[$fecha]= $prod;

           
        }
        
        foreach($detalles_planilla as $deta){
            $p = $deta->codigo;
            $producto = Producto::where('codigo', $p)->first();
            $arr_ins[$producto->codigo] = $producto->nombre;
            
        }
        //($arr_ins);

        //dd($cab_fecha);
        foreach($cab_fecha as $key => $valor){
            $fechas_prod[$cont2] = $key;
            $cont2++;  
        }        

        //dd($fechas_prod);
        //dd($cab_fecha,$prod);
        $agenda = Agenda::where('id', $planilla_cabecera->id_agenda)->first();
        $vistaurl = "hospital/reportes/enfermeria/pdf_medicamentos";
        $view     = \View::make($vistaurl, compact('planilla_cabecera', 'detalles_planilla', 'agenda','cab_fecha','prod','fechas_prod', 'arr_ins'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Kardes de Enfermeria');
        
    }
    

}