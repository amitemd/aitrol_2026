<?php

namespace Sis_medico\Http\Controllers\financiero;

use Excel;
use Illuminate\Http\Request;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultadofecha;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\ValoresCuentas;

class FinancieroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

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
        return view('financiero/estado_esi', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'balance' => $balance]);
    }
    public function fechaTexto($fecha)
    {
        $fecha     = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia       = date('l', strtotime($fecha));
        $mes       = date('F', strtotime($fecha));
        $anio      = date('Y', strtotime($fecha));
        $dias_EN   = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $dias_ES   = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        // return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
        return $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
    public function estado_esi(Request $request)
    {

        $id_empresa             = $request->session()->get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();
        $balance                = array();
        $request['fecha_desde'] = '2020/01/01';
        $request['fecha_hasta'] = '2020/04/16';
        $id_empresa             = $request->session()->get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }
            $data    = array();
            $data    = ValoresCuentas::activoCorriente($fecha_desde, $fecha_hasta);
            $dataanc = ValoresCuentas::activonoCorriente($fecha_desde, $fecha_hasta);
            $datap   = ValoresCuentas::pasivoCorriente($fecha_desde, $fecha_hasta);
            $datapnc = ValoresCuentas::pasivonoCorriente($fecha_desde, $fecha_hasta);
            $datapat = ValoresCuentas::Patrimonio($fecha_desde, $fecha_hasta);
            return view('financiero/estado_esi', ['empresa' => $empresa, 'datapat' => $datapat, 'data' => $data, 'datap' => $datap, 'dataanc' => $dataanc, 'datapnc' => $datapnc, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
            //dd($data);
        } else {
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }

            $data = array();
            $data = ValoresCuentas::activoCorriente($fecha_desde, $fecha_hasta);

            if ($request['exportar'] == "") {
                $vistaurl = "contable/flujo_efectivo/print";
                $view     = \View::make($vistaurl, compact('empresa', 'data', 'fecha_desde', 'fecha_hasta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('FlujoEfectivo-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {
                Excel::create('FlujoEfectivo-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $data) {
                    $excel->sheet('FlujoEfectivo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $data) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("FLUJO DE EFECTIVO");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A4:F4');
                        $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                            // manipulate the cel
                            $cell->setValue("$fecha_desde al $fecha_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A5', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B5:E5');
                        $sheet->cell('B5', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F5', function ($cell) {
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
                        $sheet->cells('A3:F3', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A5:F5', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                        });

                        $sheet->setWidth(array(
                            'A' => 12,
                            'B' => 12,
                            'C' => 12,
                            'D' => 12,
                            'E' => 12,
                            'F' => 12,
                        ));

                    });
                })->export('xlsx');
            }

        }
    }

    public function setDetalles($balance, $sheet, $i)
    {
        $x          = 0;
        $acum_mas   = 0;
        $acum_menos = 0;
        $band       = 0;
        foreach ($balance as $value) {
            if (isset($value['signo'])) {
                if ($value['signo'] == '1') {
                    $acum_mas += $value['saldo'];
                }

            } else {
                $acum_menos += $value['saldo'];
            }

            if ($value['signo'] == 2 and $band == 0) {
                $sheet->mergeCells("A$i:E$i");
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL INGRESOS ');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->mergeCells('A'.$i.':E');
                $sheet->cell('F' . $i, function ($cell) use ($value, $acum_mas) {
                    // manipulate the cel
                    $cell->setValue(number_format($acum_mas, 2));
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
                $band = 1;
            }

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(" " . $value['id_plan'] . " ");
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->mergeCells('B' . $i . ':E' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(strtoupper($value['nombre']));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('F' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(number_format($value['saldo'], 2));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $i++;
        }

        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL EGRESOS ');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        // $sheet->mergeCells('A'.$i.':E');
        $sheet->cell('F' . $i, function ($cell) use ($value, $acum_menos) {
            // manipulate the cel
            $cell->setValue(number_format($acum_menos, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $i++;

        $saldo = number_format($acum_mas - $acum_menos, 2);
        $sheet->mergeCells("A$i:E$i");
        $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('SALDO FINAL DEL EFECTIVO ');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        // $sheet->mergeCells('A'.$i.':E');
        $sheet->cell('F' . $i, function ($cell) use ($value, $saldo) {
            // manipulate the cel
            $cell->setValue(number_format($saldo, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }

    public function estadoresultados(Request $request)
    {
        //if ($this->rol()) {
        //    return response()->view('errors.404');
        //}
        $request['fecha_desde'] = date('Y') . '-01-01';
        $request['fecha_hasta'] = date('Y-m-d');
        $estadopyg              = array();
        $ingresos               = array();
        $costos                 = array();
        $gastos                 = array();
        $id_empresa             = $request->session()->get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y');
            $fecha_hasta = date('Y-m');
        }

        // $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);

        return view('financiero/estado_resultado', ['empresa' => $empresa, 'estadopyg' => $estadopyg, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'ingresos' => $ingresos
            , 'costos' => $costos, 'gastos' => $gastos]);
    }

    public function proyeccionfinancera(Request $request)
    {
        //if ($this->rol()) {
        //    return response()->view('errors.404');
        //}
        $request['fecha_desde'] = '2020-01-01';
        $request['fecha_hasta'] = '2020-04-20';
        $estadopyg              = array();
        $ingresos               = array();
        $costos                 = array();
        $gastos                 = array();
        $id_empresa             = $request->session()->get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y');
            $fecha_hasta = date('Y-m');
        }

        // $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);

        return view('financiero/estado_resultado', ['empresa' => $empresa, 'estadopyg' => $estadopyg, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'ingresos' => $ingresos
            , 'costos' => $costos, 'gastos' => $gastos]);
    }

    public function resultado(Request $request)
    {
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

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            //$toting     = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta,'I');

            $costos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            //$totcos     = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta,'C');

            $gastos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            //$totgas     = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta,'G');

            $totpyg = EstadoResultadofecha::detalle_total_pyg($fecha_desde, $fecha_hasta);
            $date   = date_create(str_replace("/", "-", $request['fecha_hasta'] . '-01'));
            $date   = date_format($date, "Y");
            //dd($gastos);

            $fechagrupo = array();
            for ($i = $request['fecha_desde']; $i <= $date; $i++) {
                array_push($fechagrupo, (int) $i);
            }
            //dd($fechagrupo);

            return view('financiero/estado_resultado', ['fechagrupo' => $fechagrupo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'ingresos' => $ingresos
                , 'totpyg' => $totpyg, 'costos' => $costos, 'gastos' => $gastos, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalle' => $mostrar_detalle]);
        } else {
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

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $toting   = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I');

            $costos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            $totcos = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C');

            $gastos = EstadoResultadofecha::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            $totgas = EstadoResultadofecha::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G');

            $totpyg = EstadoResultadofecha::detalle_total_pyg($fecha_desde, $fecha_hasta);

            $periodo_desde = "";
            $periodo_hasta = "";
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            if ($request['exportar'] == 0) {
                $vistaurl = "contable/estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
 
                return $pdf->stream('EstadoResultado-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {

                Excel::create('EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                    $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("ESTADO DE RESULTADO INTEGRAL");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function ($cell) use ($periodo_hasta) {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B4:E4');
                        $sheet->cell('B4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->cell('F4', function ($cell) {
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

                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" Utilidad / Perdida del Periodo ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $i . ':E' . $i);
                        // $sheet->cell('B'.$i, function($cell) use($value, $cont) {
                        //     // manipulate the cel
                        //     $cell->setValue($value['nombre']);
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //     $this->setSangria($cont, $cell,1);
                        // });

                        $sheet->cell('F' . $i, function ($cell) use ($totpyg) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue(number_format($totpyg, 2));
                            if ($totpyg < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        //  CONFIGURACION FINAL
                        $sheet->cells('A2:F2', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A4:F4', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                        });

                        $sheet->setWidth(array(
                            'A' => 12,
                            'B' => 12,
                            'C' => 12,
                            'D' => 12,
                            'E' => 12,
                            'F' => 12,
                        ));

                    });
                })->export('xlsx');
            }
        }
    }

}
