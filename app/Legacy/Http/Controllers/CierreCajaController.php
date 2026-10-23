<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\Http\Controllers\Controller;
use Excel;

class CierreCajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5,20)) == false) {
            return true;
        }
    }

    public function index_cierre(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = '0992704152001';
        }
        $caja = $request['caja'];
        
        $doctor = $request['doctor'];
        
        $tipo = $request['tipo'];
        if (is_null($tipo)) {
            $tipo = '0';
        }
        $ordenes  = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('a.fechaini', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:00'])
            ->where('a.proc_consul',$tipo)
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');
            

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);    
        } 

        if (!is_null($doctor)) {
            $ordenes = $ordenes->where('a.id_doctor1',$doctor);
        }

        $ordenes = $ordenes->get();   

        $doctores = User::where('id_tipo_usuario','3')->where('training','0')->where('uso_sistema','0')->orderby('apellido1')->get();

        $empresas = Empresa::where('id', '0992704152001')->orWhere('id', '1314490929001')->get();

        return view('contable/reporte_cierre_caja/index_cierre', ['empresas' => $empresas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ordenes' => $ordenes, 'request' => $request, 'doctores' => $doctores]);
    }

    public function reporte(Request $request)
    {
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = '0992704152001';
        }
        $caja = $request['caja'];
        
        $doctor = $request['doctor'];
        
        $tipo = $request['tipo'];
        if (is_null($tipo)) {
            $tipo = '0';
        }

        
        $ordenes  = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('a.fechaini', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:00'])
            ->where('a.proc_consul',$tipo)
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');
            

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);    
        } 

        if (!is_null($doctor)) {
            $ordenes = $ordenes->where('a.id_doctor1',$doctor);
        }

        $ordenes = $ordenes->get();  
        $empresa = Empresa::findorfail($id_empresa);

        $vistaurl = "contable.reporte_cierre_caja.pdf";
        $view     = \View::make($vistaurl, compact('ordenes', 'empresa', 'fecha', 'fecha_hasta', 'caja'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Cierre de Caja-' . $fecha . '.pdf');
    }
    public function imprimir_excel(Request $request)
    {
       $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = '0992704152001';
        }
        $caja = $request['caja'];
        
        $doctor = $request['doctor'];
        
        $tipo = $request['tipo'];
        if (is_null($tipo)) {
            $tipo = '0';
        }

        
        $ordenes  = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('a.fechaini', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:00'])
            ->where('a.proc_consul',$tipo)
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');
            

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);    
        } 

        if (!is_null($doctor)) {
            $ordenes = $ordenes->where('a.id_doctor1',$doctor);
        }

        $ordenes = $ordenes->get(); 
        //dd($request->all(),$ordenes);

        $empresa = Empresa::findorfail($id_empresa);     
        

         Excel::create('Cierre de Caja-' . $fecha, function($excel) use($ordenes, $empresa) {

            $excel->sheet('Cierre de Caja', function($sheet) use($ordenes, $empresa) {
              $sheet->mergeCells('B2:M2');
                $sheet->cell('B2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSULTAS MEDICAS ESPECIALIZADAS DR CARLOS ROBLES');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#1A28D7');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('D3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO 1');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO 2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE 1');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE 2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CTS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('I3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ADMISION');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('J3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('K3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('L3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('N3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('T.CREDITO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('2% T/D');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSF/DEP');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PEND FC SEG');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL VTA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HONOR. MEDICOS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $tefectivo = 0;
                $ttarjeta = 0;
                $acum_efectivo = 0;
                $acum_tcredito = 0;
                $acum_p7= 0;
                $acum_p2= 0;
                $acum_tran= 0;
                $acum_cheque= 0;
                $acum_oda= 0;
                $acum_total= 0;
                $acum_honorario= 0;
                $i = 4;
                foreach ($ordenes as $value){
                    $pagos = $value->pagos;
                    $efectivo  = 0;
                    $tcredito = 0;
                    $p7 = 0;
                    $p2 = 0;
                    $tran = 0;
                    $cheque = 0;
                    $total = 0;
                    $referencia = "";
                    foreach($pagos as $pago){
                      if($pago->tipo == '1'){
                        $efectivo += $pago->valor;
                        $total += $pago->valor;
                      }

                      if($pago->tipo == 4){
                        $va = $pago->valor/(1 +$pago->p_fi);
                        $po = $va * $pago->p_fi;
                        $tcredito += $va;
                        $p7 += $po;
                        $total += $va;
                      }

                      if($pago->tipo == 6){
                        $va = $pago->valor/(1+$pago->p_fi);
                        $po = $va * $pago->p_fi;
                        $tcredito += $va;
                        $p2 += $po;
                        $total += $va;
                      }

                      if($pago->tipo == 3 || $pago->tipo == 5){
                        $tran += $pago->valor;
                        $total += $pago->valor;
                      }

                      if($pago->tipo == 2 ){
                        $cheque += $pago->valor;
                        $total += $pago->valor;
                      }

                    }
                    if($efectivo > 0){
                      $referencia = "CASH";
                    }
                    if($tcredito > 0){
                      if(!is_null($referencia)){
                        $referencia = $referencia."+Tarjeta ";
                      }else{
                        $referencia = "Tarjeta ";
                      }
                    }
                    if($tran > 0){
                      if(!is_null($referencia)){
                        $referencia = $referencia."+TRAN/DEP";
                      }else{
                        $referencia = "TRAN/DEP";
                      }
                    }
                    if($cheque > 0){
                      if(!is_null($referencia)){
                        $referencia = $referencia."+CH";
                      }else{
                        $referencia = "CH";
                      }
                    }
                    $honorario = $total - $p2 - $p7;
                    $acum_efectivo = $acum_efectivo + $efectivo;
                    $acum_tcredito = $acum_tcredito + $tcredito;
                    $acum_p7 = $acum_p7 + $p7;
                    $acum_p2 = $acum_p2 + $p2 ;
                    $acum_tran = $acum_tran + $tran;
                    $acum_cheque = $acum_cheque + $cheque;
                    $acum_oda = $acum_oda + $value->valor_oda;
                    $acum_total = $acum_total + $total;
                    $acum_honorario = $acum_honorario + $honorario;

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->agenda->fechaini, 0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->agenda->fechaini, 11,5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if($value->agenda->paciente->apellido2 != 'N/A'){
                        $cell->setValue($value->agenda->paciente->apellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if($value->agenda->paciente->nombre2 != 'N/A'){
                        $cell->setValue($value->agenda->paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->cortesia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->usercrea->apellido1." ".$value->usercrea->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->doctor1->apellido1." ".$value->agenda->doctor1->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if($value->agenda->proc_consul == 0){
                        $cell->setValue("CONSULTA");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                        elseif($value->agenda->proc_consul == 1){
                        $cell->setValue( "PROCEDIMIENTO");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->seguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if($value->agenda->tipo_cita == 0){
                        $cell->setValue("PRIMERA VEZ");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                        else{
                            $cell->setValue("CONSECUTIVO");
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value, $referencia) {
                        // manipulate the cel
                        $cell->setValue($referencia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $efectivo) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$efectivo));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value, $tcredito) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$tcredito));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $p7) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$p7));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($value, $p2) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$p2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value, $tran) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$tran));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value, $cheque) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$cheque));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$value->valor_oda));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value, $total) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$total));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$honorario));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                } 
                    $x=$i;
                    $sheet->cell('O' . $x, function ($cell) use ($acum_efectivo) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_efectivo));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('P' . $x, function ($cell) use ($acum_tcredito) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_tcredito));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Q' . $x, function ($cell) use ($acum_p7) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_p7));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('R' . $x, function ($cell) use ($acum_p2) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_p2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S' . $x, function ($cell) use ($acum_tran) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_tran));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('T' . $x, function ($cell) use ($acum_cheque) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_cheque));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('U' . $x, function ($cell) use ($acum_oda) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_oda));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $x, function ($cell) use ($acum_total) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_total));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $x, function ($cell) use ($acum_honorario) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f",$acum_honorario));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });





                    
            });
        })->export('xlsx');
    }
}
