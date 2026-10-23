<?php

namespace Sis_medico\Http\Controllers\financiero;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\ProyeccionFinanciera2;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas; 
use Sis_medico\Empresa;  
use Sis_medico\ProyeccionFinanciera;  
use Sis_medico\EstructuraFlujoEfectivo;  
use Sis_medico\EstructuraReporte;  
use Sis_medico\EstadoResultado;  
use Sis_medico\Financery; 
use Excel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;

class Proyeccion_Financiera2Controller extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth');
    }

    public function index(Request $request){

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $balance    = array();
        
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
        }
        return view('financiero/estado_esi', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa'=>$empresa, 'balance'=>$balance]);
    }

   
    public function proyeccionfinanciera2_index(Request $request){
        //$estadoresultado = new EstadoResultado();
        //dd('hola');
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        
        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1; 
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle']; 
            }
            $date=date_create(str_replace("/","-",$request['fecha_hasta'].'-01'));
            $date=date_format($date,"Y");
            //dd($gastos);

            $fechagrupo=array(); 
            for ($i=$fecha_desde; $i <= $date ; $i++) { 
                array_push($fechagrupo,(int)$i);
            } 
            
             $ingresos   = array();   $gresos = array();   $gastos = array();

            $ingresos         = ProyeccionFinanciera2::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $liquidez         = ProyeccionFinanciera2::detalle_principal($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $solvencia        = ProyeccionFinanciera2::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $gestion          = ProyeccionFinanciera2::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $rentabilidad     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $cuentas_detalle = null;
            $fech=$fecha_desde."-01-01";
            $fech2= $fecha_hasta."-31";
            
            $costos     = EstadoResultado::detalle_estadopg($fech, $fech2,'C',$cuentas_detalle);
            $totcos     = EstadoResultado::detalle_total_cuenta($fech, $fech2,'C'); 

            $gastos     = EstadoResultado::detalle_estadopg($fech, $fech2,'G',$cuentas_detalle);
            $totgas     = EstadoResultado::detalle_total_cuenta($fech, $fech2,'G');
 
            $totpyg     = EstadoResultado::detalle_total_pyg($fech, $fech2); 
            $trabajadores = EstadoResultado::trabajadores($fech, $fech2); 
            
          
          //dd($liquidez);
        return view('financiero/proyeccion_financiera2/proyeccionfinanciera2_index',['fechagrupo' => $fechagrupo,'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa'=>$empresa,'ingresos'=>$ingresos,'totpyg'=>$totpyg, 'trabajadores'=>$trabajadores, 'costos'=>$costos, 'gastos'=>$gastos, 'cuentas_detalle'=>$cuentas_detalle, 'mostrar_detalle'=>$mostrar_detalle,'liquidez'=> $liquidez,'solvencia'=> $solvencia,'gestion'=> $gestion,'rentabilidad'=> $rentabilidad]);
        }else{ 
            if (!is_null($request['fecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle']; 
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1; 
            }
            
            // $estadopyg = array();
            // $estadopyg = $this->detalle_estadopg($fecha_desde, $fecha_hasta, $tipo);

            $ingresos   = array();   $gresos = array();   $gastos = array();
            
            $ingresos   = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $toting     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'I');
            
            $costos     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'C',$cuentas_detalle);
            $totcos     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'C');
            
            $gastos     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'G',$cuentas_detalle);
            $totgas     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'G');

            $totpyg           = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta,null); 
            $trabajadores     = ProyeccionFinanciera2::trabajadores($fecha_desde, $fecha_hasta,null);
            $utilidad_gravable= ProyeccionFinanciera2::utilidad_gravable($fecha_desde, $fecha_hasta,null);
        
            if($request['exportar']==0){
                $vistaurl = "estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('EstadoResultado-' . $fecha_desde .'-'. $fecha_hasta . '.pdf');
            }else{
               /*
                Excel::create('EstadoSituacionFinanciera-'.$fecha_desde.'-al-'.$fecha_hasta, function($excel) use($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                    $excel->sheet('EstadoSituacionFinanciera', function($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function($cell) {
                            // manipulate the cel
                            $cell->setValue("ESTADO DE RESULTADO INTEGRAL");
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function($cell) use($periodo_hasta)  {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        $sheet->mergeCells('B4:E4');
                        $sheet->cell('B4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            
                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->cell('F4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                        // DETALLES
                        
                        $sheet->setColumnFormat(array(
                            'F' => '0.00', 
                        ));
                       
                         $i = $this->setDetalles($ingresos, $sheet, 5);
                         $i = $this->setDetalles($costos, $sheet, $i);
                         $i = $this->setDetalles($gastos, $sheet, $i);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                        $sheet->cell('A'.$i, function($cell) {
                            // manipulate the cel
                            $cell->setValue(" Utilidad / Perdida del Periodo "); 
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                        $sheet->mergeCells('A'.$i.':E'.$i);
                        // $sheet->cell('B'.$i, function($cell) use($value, $cont) {
                        //     // manipulate the cel
                        //     $cell->setValue($value['nombre']);
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //     $this->setSangria($cont, $cell,1);
                        // });
                        
                        $sheet->cell('F'.$i, function($cell) use($totpyg) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue(number_format($totpyg,2));
                            if($totpyg<0){
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        

                        //  CONFIGURACION FINAL 
                        $sheet->cells('A2:F2', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A4:F4', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd'); 
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                        });


                        $sheet->setWidth(array(
                            'A'     =>  12,
                            'B'     =>  12,
                            'C'     =>  12,
                            'D'     =>  12,
                            'E'     =>  12,
                            'F'     =>  12,
                        ));

                    });
                })->export('xlsx');*/
            }
        }
    } 

    public function resultado(Request $request){
        //$estadoresultado = new EstadoResultado();
        //dd('hola');
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        
        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1; 
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle']; 
            }
            $date=date_create(str_replace("/","-",$request['fecha_hasta'].'-01'));
            $date=date_format($date,"Y");
            //dd($gastos);

            $fechagrupo=array(); 
            for ($i=$fecha_desde; $i <= $date ; $i++) { 
                array_push($fechagrupo,(int)$i);
            } 
            
            $ingresos   = array();   $gresos = array();   $gastos = array();

            $ingresos   = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $liquidez= ProyeccionFinanciera2::total($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $solvencia     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $gestion     =ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $rentabilidad     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            
            $costos     = EstadoResultado::detalle_estadopg($fech, $fech2,'C',$cuentas_detalle);
            $totcos     = EstadoResultado::detalle_total_cuenta($fech, $fech2,'C'); 

            $gastos     = EstadoResultado::detalle_estadopg($fech, $fech2,'G',$cuentas_detalle);
            $totgas     = EstadoResultado::detalle_total_cuenta($fech, $fech2,'G');
 
            $totpyg     = EstadoResultado::detalle_total_pyg($fech, $fech2); 
            $trabajadores = EstadoResultado::trabajadores($fech, $fech2); 
            
        
            //(dd($liquidez);

            return view('financiero/proyeccion_financiera2/proyeccionfinanciera2_index' ,['fechagrupo' => $fechagrupo,'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa'=>$empresa, 'ingresos'=>$ingresos
            , 'totpyg'=>$totpyg, 'trabajadores'=>$trabajadores, 'costos'=>$costos, 'gastos'=>$gastos, 'cuentas_detalle'=>$cuentas_detalle, 'mostrar_detalle'=>$mostrar_detalle,
            'liquidez'=> $liquidez,'solvencia'=> $solvencia,'gestion'=> $gestion,'rentabilidad'=> $rentabilidad]);
        }else{ 
            if (!is_null($request['fecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle']; 
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1; 
            }
            
            // $estadopyg = array();
            // $estadopyg = $this->detalle_estadopg($fecha_desde, $fecha_hasta, $tipo);

            $ingresos   = array();   $gresos = array();   $gastos = array();
            
            $ingresos   = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'I',$cuentas_detalle);
            $toting     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'I');
            
            $costos     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'C',$cuentas_detalle);
            $totcos     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'C');
            
            $gastos     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta,'G',$cuentas_detalle);
            $totgas     = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta,'G');

            $totpyg           = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta,null); 
            $trabajadores     = ProyeccionFinanciera2::trabajadores($fecha_desde, $fecha_hasta,null);
            $utilidad_gravable= ProyeccionFinanciera2::utilidad_gravable($fecha_desde, $fecha_hasta,null);
         
            if($request['exportar']==0){
                $vistaurl = "estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('EstadoResultado-' . $fecha_desde .'-'. $fecha_hasta . '.pdf');
            }else{
               /*
                Excel::create('EstadoSituacionFinanciera-'.$fecha_desde.'-al-'.$fecha_hasta, function($excel) use($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                    $excel->sheet('EstadoSituacionFinanciera', function($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function($cell) {
                            // manipulate the cel
                            $cell->setValue("ESTADO DE RESULTADO INTEGRAL");
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function($cell) use($periodo_hasta)  {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        $sheet->mergeCells('B4:E4');
                        $sheet->cell('B4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            
                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->cell('F4', function($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                        // DETALLES
                        
                        $sheet->setColumnFormat(array(
                            'F' => '0.00', 
                        ));
                       
                         $i = $this->setDetalles($ingresos, $sheet, 5);
                         $i = $this->setDetalles($costos, $sheet, $i);
                         $i = $this->setDetalles($gastos, $sheet, $i);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                        $sheet->cell('A'.$i, function($cell) {
                            // manipulate the cel
                            $cell->setValue(" Utilidad / Perdida del Periodo "); 
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                        $sheet->mergeCells('A'.$i.':E'.$i);
                        // $sheet->cell('B'.$i, function($cell) use($value, $cont) {
                        //     // manipulate the cel
                        //     $cell->setValue($value['nombre']);
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //     $this->setSangria($cont, $cell,1);
                        // });
                        
                        $sheet->cell('F'.$i, function($cell) use($totpyg) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue(number_format($totpyg,2));
                            if($totpyg<0){
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        

                        //  CONFIGURACION FINAL 
                        $sheet->cells('A2:F2', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A4:F4', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd'); 
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                        });


                        $sheet->setWidth(array(
                            'A'     =>  12,
                            'B'     =>  12,
                            'C'     =>  12,
                            'D'     =>  12,
                            'E'     =>  12,
                            'F'     =>  12,
                        ));

                    });
                })->export('xlsx');*/
            }
        }
    } 
 
}
