<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas; 
use Sis_medico\Empresa;  
use Sis_medico\FlujoEfectivo;  
use Sis_medico\FlujoEfectivoComparativo;
use Sis_medico\GrupoEstructuraFlujoEfectivo;   

use Excel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;

class FlujoEfectivoComparativoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22,26)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde    = $request['fecha_desde'];
            $fecha_hasta    = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y-m-d');
            $fecha_hasta = date('Y-m-d'); 
            $fecha_hasta = date('Y-m-d',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
        }

        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $data1          = array();     $data2           = array(); 
        return view('contable/flujo_efectivo_comparativo/index', ['empresa' => $empresa, 'data1' => $data1, 'data2' => $data2, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);

    } 
 
    public function show(Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();

        

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y-m');
                $fecha_hasta = date('Y-m'); 
                $fecha_hasta = date('Y-m',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
            }
            $lastDay = date('t',strtotime("$fecha_desde-01"));
            $desde1 = $fecha_desde."-01";
            $hasta1 = $fecha_desde."-$lastDay";

            $lastDay = date('t',strtotime("$fecha_hasta-01"));
            $desde2 = $fecha_hasta."-01";
            // dd($desde1);  
            // $desde2 = strtotime('Y-m-d', $desde2);
            $hasta2 = $fecha_hasta."-$lastDay";
           
            $data1 = array();       $data2 = array(); 
            // dd($desde1);    
            $data1 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde1, $hasta1); 
            $data2 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde2, $hasta2); 
            return view('contable/flujo_efectivo_comparativo/index', ['empresa' => $empresa, 'data1' => $data1, 'data2' => $data2, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]); 
        }else{
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_desde = date('Y-m');
                $fecha_hasta = date('Y-m'); 
                $fecha_hasta = date('Y-m',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
            }

            $lastDay = date('t',strtotime("$fecha_desde-01"));
            $desde1 = $fecha_desde."-01";
            $hasta1 = $fecha_desde."-$lastDay";

            $lastDay = date('t',strtotime("$fecha_hasta-01"));
            $desde2 = $fecha_hasta."-01";
            $hasta2 = $fecha_hasta."-$lastDay";
           
            $data1 = array();       $data2 = array(); 
            $data1 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde1, $hasta1); 
            $data2 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde2, $hasta2);

            if($request['exportar']==""){
                $vistaurl = "contable/flujo_efectivo/print";
                $view     = \View::make($vistaurl, compact('empresa', 'data', 'fecha_desde', 'fecha_hasta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('FlujoEfectivo-' . $fecha_desde .'-'. $fecha_hasta . '.pdf');
            }else{
                Excel::create('FlujoEfectivoComparativo-'.$fecha_desde.'-al-'.$fecha_hasta, function($excel) use($empresa, $fecha_desde, $fecha_hasta, $data1, $data2) {
                    $excel->sheet('FlujoEfectivoComparativo', function($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $data1, $data2) {
                        $sheet->mergeCells('A1:G1');
                        $sheet->cell('A1', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:G2');
                        $sheet->cell('A2', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:G3');
                        $sheet->cell('A3', function($cell) {
                            // manipulate the cel
                            $cell->setValue("FLUJO DE EFECTIVO COMPARATIVO");
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A4:G4');
                        $sheet->cell('A4', function($cell) use($fecha_desde, $fecha_hasta)  {
                            // manipulate the cel
                            $cell->setValue("$fecha_desde al $fecha_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        $sheet->mergeCells('B5:E5');
                        $sheet->cell('B5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            
                        });
                        $sheet->cell('F5', function($cell) use ($fecha_desde) {
                            // manipulate the cel
                            $cell->setValue($fecha_desde);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                         
                        $sheet->cell('G5', function($cell) use ($fecha_hasta)  {
                            // manipulate the cel
                            $cell->setValue($fecha_hasta);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                        
                        $sheet->setColumnFormat(array(
                            'F' => '0.00', 
                            'G' => '0.00',  
                        ));
                       
                        $i = $this->setDetalles($data1, $data2, $sheet, 6); 

                        //  CONFIGURACION FINAL 
                        $sheet->cells('A3:G3', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A5:G5', function($cells) {
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
                            'G'     =>  12, 
                        ));

                    });
                })->export('xlsx');
            }

        }   
    }


    public function setDetalles($data1, $data2, $sheet, $i)
    { 
        $x = 0;     $acum_mas = 0;      $acum_menos = 0;     $band = 0;    $acum_mas1 = 0;     $acum_menos1 = 0;    
        foreach($data1 as $value)
        {   
            if($value['signo']=='1'){
                $acum_mas += $value['saldo'];
                $acum_mas1 += $data2[$x]['saldo'];
            }else{
                $acum_menos += $value['saldo'];
                $acum_menos1 += $data2[$x]['saldo'];
            }

            if($value['signo']==2 and $band == 0){
                $sheet->mergeCells("A$i:E$i");
                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL INGRESOS '); 
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                // $sheet->mergeCells('A'.$i.':E'); 
                $sheet->cell('F'.$i, function($cell) use($value, $acum_mas) {
                    // manipulate the cel
                    $cell->setValue(number_format($acum_mas,2)); 
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                
                $sheet->cell('G'.$i, function($cell) use($value, $acum_mas1) {
                    // manipulate the cel
                    $cell->setValue(number_format($acum_mas1,2)); 
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                $i++;
                $band = 1;
            }

            $sheet->cell('A'.$i, function($cell) use($value) {
                // manipulate the cel
                $cell->setValue(" ".$value['id_plan']." "); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            
            $sheet->mergeCells('B'.$i.':E'.$i);
            $sheet->cell('B'.$i, function($cell) use($value) {
                // manipulate the cel 
                $cell->setValue(strtoupper($value['nombre']));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            
            $sheet->cell('F'.$i, function($cell) use($value) {
                // manipulate the cel
                $cell->setValue(number_format($value['saldo'],2)); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');  
            }); 
 
            $sheet->cell('G'.$i, function($cell) use($data2, $x) {
                // manipulate the cel
                $cell->setValue(number_format($data2[$x]['saldo'],2)); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');  
            }); 

            $i++;   $x++;
        }

        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL EGRESOS '); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('F'.$i, function($cell) use($acum_menos) {
            // manipulate the cel
            $cell->setValue(number_format($acum_menos,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('G'.$i, function($cell) use($acum_menos1) {
            // manipulate the cel
            $cell->setValue(number_format($acum_menos1,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });

        $i++; 

        $saldo = number_format($acum_mas-$acum_menos,2);
        $saldo1 = number_format($acum_mas1-$acum_menos1,2);
        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('SALDO FINAL DEL EFECTIVO '); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('F'.$i, function($cell) use($saldo) {
            // manipulate the cel
            $cell->setValue(number_format($saldo,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('G'.$i, function($cell) use($saldo1) {
            // manipulate the cel
            $cell->setValue(number_format($saldo1,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });

        $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F'.$i, function($cells) { 
            $cells->setAlignment('right');
        });
        return $i;
    }



    
    public function index2(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde    = $request['fecha_desde'];
            $fecha_hasta    = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y-m-d');
            $fecha_hasta = date('Y-m-d'); 
            $fecha_hasta = date('Y-m-d',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
        }

        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first(); 
        $grupos         = GrupoEstructuraFlujoEfectivo::all();
        $data1          = array();     $data2           = array(); 
        return view('contable/flujo_efectivo_comparativo/index2', ['empresa' => $empresa, 'data1' => $data1, 'data2' => $data2, 
        'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'grupos' => $grupos]);

    } 
  
    public function show2(Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $grupos         = GrupoEstructuraFlujoEfectivo::all();
        

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y-m');
                $fecha_hasta = date('Y-m'); 
                $fecha_hasta = date('Y-m',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
            }

            if (isset($request['grupo'])) {
                $filgrupo = $request['grupo'];
            } else {
                $filgrupo = '[]';
            }
            
            if($filgrupo != '[]'){
                foreach ($filgrupo as $field) { 
                    // $conditions[] = ['id', 'like', '%' . $field . '%']; 
                    $conditions[] = $field; 
                }
            }else{
                $conditions = "[]";
            } 
            $lastDay = date('t',strtotime("$fecha_desde-01"));
            $desde1 = $fecha_desde."-01";
            $hasta1 = $fecha_desde."-$lastDay";

            $lastDay = date('t',strtotime("$fecha_hasta-01"));
            $desde2 = $fecha_hasta."-01";
            // dd($desde1);  
            // $desde2 = strtotime('Y-m-d', $desde2);
            $hasta2 = $fecha_hasta."-$lastDay";
           
            $data1 = array();       $data2 = array(); 
            // dd($conditions);    
            $data1 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde1, $hasta1, $conditions); 
            $data2 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde2, $hasta2, $conditions); 
            return view('contable/flujo_efectivo_comparativo/index2', ['empresa' => $empresa, 'data1' => $data1, 'data2' => $data2, 
            'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'grupos' => $grupos]); 
        }else{
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_desde = date('Y-m');
                $fecha_hasta = date('Y-m'); 
                $fecha_hasta = date('Y-m',strtotime($fecha_hasta."+ 1 month")); //sumo 1 mes
            }

            if (isset($request['filgrupo'])) {
                $filgrupo = $request['filgrupo'];
            } else {
                $filgrupo = '[]';
            } 
            if($filgrupo != '[]'){
                $filgrupo = explode(",", $filgrupo); 
                foreach ($filgrupo as $field) { 
                    // $conditions[] = ['id', 'like', '%' . $field . '%']; 
                    $conditions[] = $field; 
                }
            }else{
                $conditions = "[]";
            } 

            $lastDay = date('t',strtotime("$fecha_desde-01"));
            $desde1 = $fecha_desde."-01";
            $hasta1 = $fecha_desde."-$lastDay";

            $lastDay = date('t',strtotime("$fecha_hasta-01"));
            $desde2 = $fecha_hasta."-01";
            $hasta2 = $fecha_hasta."-$lastDay";
           
            $data1 = array();       $data2 = array(); 
            $data1 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde1, $hasta1, $conditions); 
            $data2 = FlujoEfectivoComparativo::flujoEfectivoComparativo($desde2, $hasta2, $conditions);

            if($request['exportar']==""){
                $vistaurl = "contable/flujo_efectivo/print";
                $view     = \View::make($vistaurl, compact('empresa', 'data', 'fecha_desde', 'fecha_hasta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('FlujoEfectivo-' . $fecha_desde .'-'. $fecha_hasta . '.pdf');
            }else{
                Excel::create('FlujoEfectivoComparativo-'.$fecha_desde.'-al-'.$fecha_hasta, function($excel) use($empresa, $fecha_desde, $fecha_hasta, $data1, $data2) {
                    $excel->sheet('FlujoEfectivoComparativo', function($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $data1, $data2) {
                        $sheet->mergeCells('A1:G1');
                        $sheet->cell('A1', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:G2');
                        $sheet->cell('A2', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:G3');
                        $sheet->cell('A3', function($cell) {
                            // manipulate the cel
                            $cell->setValue("FLUJO DE EFECTIVO COMPARATIVO");
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A4:G4');
                        $sheet->cell('A4', function($cell) use($fecha_desde, $fecha_hasta)  {
                            // manipulate the cel
                            $cell->setValue("$fecha_desde al $fecha_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        $sheet->cell('B5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('GRUPO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        });
                        $sheet->mergeCells('C5:E5');
                        $sheet->cell('C5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            
                        });
                        $sheet->cell('F5', function($cell) use ($fecha_desde) {
                            // manipulate the cel
                            $cell->setValue($fecha_desde);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                         
                        $sheet->cell('G5', function($cell) use ($fecha_hasta)  {
                            // manipulate the cel
                            $cell->setValue($fecha_hasta);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                        
                        $sheet->setColumnFormat(array(
                            'F' => '0.00', 
                            'G' => '0.00',  
                        ));
                       
                        $i = $this->setDetallesGrupo($data1, $data2, $sheet, 6); 

                        //  CONFIGURACION FINAL 
                        $sheet->cells('A3:G3', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A5:G5', function($cells) {
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
                            'G'     =>  12, 
                        ));

                    });
                })->export('xlsx');
            }

        }   
    }

    public function setDetallesGrupo($data1, $data2, $sheet, $i)
    { 
        $x = 0;     $acum_mas = 0;      $acum_menos = 0;     $band = 0;    $acum_mas1 = 0;     $acum_menos1 = 0;    
        foreach($data1 as $value)
        {   
            if($value['signo']=='1'){
                $acum_mas += $value['saldo'];
                $acum_mas1 += $data2[$x]['saldo'];
            }else{
                $acum_menos += $value['saldo'];
                $acum_menos1 += $data2[$x]['saldo'];
            }

            if($value['signo']==2 and $band == 0){
                $sheet->mergeCells("A$i:E$i");
                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL INGRESOS '); 
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                // $sheet->mergeCells('A'.$i.':E'); 
                $sheet->cell('F'.$i, function($cell) use($value, $acum_mas) {
                    // manipulate the cel
                    $cell->setValue(number_format($acum_mas,2)); 
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                
                $sheet->cell('G'.$i, function($cell) use($value, $acum_mas1) {
                    // manipulate the cel
                    $cell->setValue(number_format($acum_mas1,2)); 
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');  
                });
                $i++;
                $band = 1;
            }

            $sheet->cell('A'.$i, function($cell) use($value) {
                // manipulate the cel
                $cell->setValue(" ".$value['id_plan']." "); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            
            $sheet->cell('B'.$i, function($cell) use($value) {
                // manipulate the cel
                $cell->setValue(" ".$value['grupo']." "); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->mergeCells('C'.$i.':E'.$i);
            $sheet->cell('C'.$i, function($cell) use($value) {
                // manipulate the cel 
                $cell->setValue(strtoupper($value['nombre']));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            
            $sheet->cell('F'.$i, function($cell) use($value) {
                // manipulate the cel
                $cell->setValue(number_format($value['saldo'],2)); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');  
            }); 
 
            $sheet->cell('G'.$i, function($cell) use($data2, $x) {
                // manipulate the cel
                $cell->setValue(number_format($data2[$x]['saldo'],2)); 
                $cell->setBorder('thin', 'thin', 'thin', 'thin');  
            }); 

            $i++;   $x++;
        }

        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL EGRESOS '); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('F'.$i, function($cell) use($acum_menos) {
            // manipulate the cel
            $cell->setValue(number_format($acum_menos,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('G'.$i, function($cell) use($acum_menos1) {
            // manipulate the cel
            $cell->setValue(number_format($acum_menos1,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });

        $i++; 

        $saldo = number_format($acum_mas-$acum_menos,2);
        $saldo1 = number_format($acum_mas1-$acum_menos1,2);
        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('SALDO FINAL DEL EFECTIVO '); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('F'.$i, function($cell) use($saldo) {
            // manipulate the cel
            $cell->setValue(number_format($saldo,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });
        // $sheet->mergeCells('A'.$i.':E'); 
        $sheet->cell('G'.$i, function($cell) use($saldo1) {
            // manipulate the cel
            $cell->setValue(number_format($saldo1,2)); 
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');  
        });

        $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F'.$i, function($cells) { 
            $cells->setAlignment('right');
        });
        return $i;
    }
 
 
}
