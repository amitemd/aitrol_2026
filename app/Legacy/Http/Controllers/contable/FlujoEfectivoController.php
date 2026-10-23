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
use Sis_medico\EstructuraFlujoEfectivo;  

use Excel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;

class FlujoEfectivoController extends Controller
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
            $fecha_desde    = date('Y-m-d');
            $fecha_hasta    = date('Y-m-d');
        }

        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $data           = array();  

        return view('contable/flujo_efectivo/index', ['empresa' => $empresa, 'data' => $data, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);

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
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }
            $data           = array(); 
            $data = FlujoEfectivo::flujoEfectivo($fecha_desde, $fecha_hasta);
            return view('contable/flujo_efectivo/index', ['empresa' => $empresa, 'data' => $data, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
            //dd($data);
        }else{
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }

            $data = array(); 
            $data = FlujoEfectivo::flujoEfectivo($fecha_desde, $fecha_hasta);

            if($request['exportar']==""){ 
                $vistaurl = "contable/flujo_efectivo/print";
                $view     = \View::make($vistaurl, compact('empresa', 'data', 'fecha_desde', 'fecha_hasta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('FlujoEfectivo-' . $fecha_desde .'-'. $fecha_hasta . '.pdf');
            }else{
                Excel::create('FlujoEfectivo-'.$fecha_desde.'-al-'.$fecha_hasta, function($excel) use($empresa, $fecha_desde, $fecha_hasta, $data) {
                    $excel->sheet('FlujoEfectivo', function($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $data) {
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
                        $sheet->cell('A2', function($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function($cell) {
                            // manipulate the cel
                            $cell->setValue("FLUJO DE EFECTIVO");
                            $cell->setFontWeight('bold'); 
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A4:F4');
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
                        $sheet->cell('F5', function($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        }); 
                         
                        
                        $sheet->setColumnFormat(array(
                            'F' => '0.00', 
                            // 'G' => '0.00',  
                        ));
                       
                        $i = $this->setDetalles($data, $sheet, 6);

                        //  CONFIGURACION FINAL 
                        $sheet->cells('A3:F3', function($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A5:F5', function($cells) {
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
                })->export('xlsx');
            }

        }   
    }


    
    public function setDetalles($balance, $sheet, $i){ 
        $x = 0;     $acum_mas = 0;      $acum_menos = 0;     $band = 0;    
        foreach($balance as $value)
        {   
            if($value['signo']=='1'){
                $acum_mas += $value['saldo'];
            }else{
                $acum_menos += $value['saldo'];
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

            $i++; 
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

        $i++; 

        $saldo = number_format($acum_mas-$acum_menos,2);
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

        $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F'.$i, function($cells) { 
            $cells->setAlignment('right');
        });
        return $i;
    }
  
 
 
}
