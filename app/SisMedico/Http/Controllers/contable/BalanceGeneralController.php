<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Balances;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogCierreAnio;
use Sis_medico\Plan_Cuentas;

class BalanceGeneralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 26)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } elseif (is_null($request['fecha_desde'])) {
            $fecha_desde = '01/01/2010';
            $fecha_hasta = date('d/m/Y');
        } else {
            // $fecha_desde = date('Y-m-d');
            // $fecha_hasta = date('Y-m-d');
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $activos    = array();
        $pasivos    = array();
        $patrimonio = array();
        $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);
        return view('contable/balance_general/index', ['empresa' => $empresa, 'activos' => $activos, 'pasivos' => $pasivos, 'patrimonio' => $patrimonio, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function show(Request $request)
    {

        $nuevo = 0;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        try {
            $confi = Ct_Configuraciones::where('nombre', 'PRESENTABALANCE')->where('estado', 1)->first();
            if ($confi != '') {
                $nuevo = 1;
            }
        } catch (Exception $e) {
            $nuevo = 0;
        }
        $cuentas_detalle = "";
        ini_set('max_execution_time', 300);
        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $rfecha_desde = $request['fecha_desde'];
                $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
                $fecha_desde = date('Y-m-d', $timestamp);
                $rfecha_hasta = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta = date('Y-m-d', $timestamp);
            } else if (!is_null($request['fecha_hasta'])) {
                $fecha_desde = '2010-01-01';
                $rfecha_desde = '01/01/2010';
                $rfecha_hasta = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde  = '2010-01-01';
                $rfecha_desde = '01/01/2010';
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
            }
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalles = "";
            if (isset($request['mostrar_detalles'])) {
                $mostrar_detalles = $request['mostrar_detalles'];
            }
            $balance = array();
            $activos = $this->detalle($fecha_desde, $fecha_hasta, 'A', $id_empresa, $cuentas_detalle);
            $pasivos = $this->detalle($fecha_desde, $fecha_hasta, 'P', $id_empresa, $cuentas_detalle);
            $patrimonio = $this->detalle($fecha_desde, $fecha_hasta, '', $id_empresa, $cuentas_detalle);
            $ingresos = array();
            $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, 0, 0);
            if ($id_empresa == "0992704152001" || $id_empresa == "1391707460001") {
                $participacion = EstadoResultado::trabajadores('2010-01-01', $fecha_hasta, $id_empresa);
                $totpyg = EstadoResultado::utilidad_gravable('2010-01-01', $fecha_hasta, $id_empresa, '1');
                $impuesto_causado = EstadoResultado::impuesto_causado('2010-01-01', $fecha_hasta, $id_empresa, '1');
            } else {
                $participacion = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
                $totpyg = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, '1', null, 0, $ingresos);
                $impuesto_causado = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, '1', null, 0, $ingresos);
            }
            $cierre = LogCierreAnio::where('anio', date('Y', strtotime($request['fecha_hasta'])))->where('id_empresa', $id_empresa)->first();
            if ($cierre != '') {
                $totpyg = $totpyg;
                $impuesto_causado = 0;
            } else {
                $totpyg = $totpyg - $impuesto_causado;
            }
            $totpyg = round($totpyg, 2);
            /*if (Auth::user()->id == '0921605895') {
                echo '<pre>';
                print_r($totpyg);
                exit;
            }*/
            $periodo_desde = $this->fechaTexto($fecha_desde);
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            return view('contable/balance_general/index', ['fecha_desde' => $rfecha_desde, 'fecha_hasta' => $rfecha_hasta, 'empresa' => $empresa, 'activos' => $activos, 'pasivos' => $pasivos, 'patrimonio' => $patrimonio, 'totpyg' => $totpyg, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalles' => $mostrar_detalles, 'periodo_desde' => $periodo_desde, 'periodo_hasta' => $periodo_hasta, 'participacion' => $participacion, 'impuesto_causado' => $impuesto_causado]);
        } else {
            if (!is_null($request['fecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $rfecha_desde              = $request['filfecha_desde'];
                $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
                $fecha_desde               = date('Y-m-d', $timestamp);

                $rfecha_hasta              = $request['filfecha_hasta'];
                $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
                $fecha_hasta               = date('Y-m-d', $timestamp);
            } else if (!is_null($request['filfecha_hasta'])) {
                $fecha_desde               = '2010-01-01';
                $rfecha_desde              = '01/01/2010';
                $rfecha_hasta              = $request['filfecha_hasta'];
                $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
                $fecha_hasta               = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde  = '2010-01-01';
                $rfecha_desde = '01/01/2010';
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
            }

            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }
            $mostrar_detalles = "";
            if (isset($request['filmostrar_detalles'])) {
                $mostrar_detalles = $request['filmostrar_detalles'];
            }

            //$activos    = $this->detalle($fecha_desde, $fecha_hasta, 'A', $id_empresa, $cuentas_detalle);
            //$pasivos    = $this->detalle($fecha_desde, $fecha_hasta, 'P', $id_empresa, $cuentas_detalle);
            $activos    = json_decode($request->datosBalanceA);
            $pasivos    = json_decode($request->datosBalanceP);
            $patrimonio = json_decode($request->datosBalancePat);
            if ($request->funcionN == 1) {
                $activos    = json_decode($request->datosBalanceA);
                $pasivos    = json_decode($request->datosBalanceP);
                $patrimonio = json_decode($request->datosBalancePat);
                // if ($id_empresa == "0992704152001" || $id_empresa == "1391707460001") {
                //     $totpyg = EstadoResultado::utilidad_gravable("2010-01-01", $fecha_hasta, '1');
                //     $participacion = EstadoResultado::trabajadores("2010-01-01", $fecha_hasta, $id_empresa);
                // } else {
                //     $totpyg = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, '1');
                //     $participacion = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
                // }
                $periodo_desde = $this->fechaTexto($fecha_desde);
                $periodo_hasta = $this->fechaTexto($fecha_hasta);

                if ($request['exportar'] == 0) {
                    $vistaurl = "contable/balance_general/print";
                    $view     = \View::make($vistaurl, compact(
                        'fecha_desde',
                        'fecha_hasta',
                        'empresa',
                        'activos',
                        'pasivos',
                        'patrimonio',
                        'periodo_desde',
                        'periodo_hasta'
                    ))->render();
                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                    $pdf->setPaper('A4', 'portrait');

                    return $pdf->stream('BalaneGeneral-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
                } else {
                    $periodo_desde = $this->fechaTexto($fecha_desde);
                    $periodo_hasta = $this->fechaTexto($fecha_hasta);
                    // $participacion = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
                    // $ingresos = array();
                    // $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, 0, 0);
                    // //dd($patrimonio);
                    // $totpyg           = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, '1');
                    // $impuesto_causado = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, '1', null, 0, $ingresos);
                    // $impuesto_causado = round($impuesto_causado, 2);
                    // $totpyg = $totpyg - $impuesto_causado;
                    Excel::create('EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $periodo_desde, $periodo_hasta, $activos, $pasivos, $patrimonio) {
                        $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $periodo_desde, $periodo_hasta, $activos, $pasivos, $patrimonio) {
                            // dd($participacion);
                            $sheet->mergeCells('A1:G1');
                            $sheet->cell('A1', function ($cell) use ($empresa) {
                                // manipulate the cel
                                $cell->setValue($empresa->nombrecomercial);
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', '', 'thin');
                            });
                            $sheet->mergeCells('A2:G2');
                            $sheet->cell('A2', function ($cell) {
                                // manipulate the cel
                                $cell->setValue("ESTADO DE SITUACION FINANCIERA");
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->mergeCells('A3:G3');
                            $sheet->cell('A3', function ($cell) use ($periodo_desde, $periodo_hasta) {
                                // manipulate the ce
                                if ($periodo_desde == '01 de Enero de 2010') {

                                    $cell->setValue("al $periodo_hasta");
                                } else {
                                    $cell->setValue("$periodo_desde al $periodo_hasta");
                                }

                                $cell->setFontWeight('bold');
                                $cell->setFontSize('12');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
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
                            $sheet->cell('F4', function ($cell) {
                                // manipulate the cel
                                $cell->setValue('SALDO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            // $sheet->mergeCells('A7:A8');
                            $sheet->cell('G4', function ($cell) {
                                // manipulate the cel
                                $cell->setValue('%');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            // DETALLES

                            $sheet->setColumnFormat(array(
                                'F' => '0.00',
                                'G' => '0.00',
                            ));
                            $i = $this->setDetallesP($activos, $sheet, 5);
                            $i = $this->setDetallesP($pasivos, $sheet, $i, '');
                            $i = $this->setDetallesP($patrimonio, $sheet, $i);
                            $sheet->mergeCells("A$i:E$i");
                            $sheet->cell("A$i", function ($cell) use ($empresa) {
                                // manipulate the cel
                                $cell->setValue("TOTAL PASIVO + PATRIMONIO");
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $tpatrimonio = 0;
                            $tpasivos = 0;
                            foreach ($pasivos as $value) {

                                $cont = substr_count($value->cuenta, ".");
                                if ($cont == 0) {
                                    $tpasivos = floatval(str_replace(',', '', $value->saldo));
                                    break;
                                }
                            }
                            foreach ($patrimonio as $value) {
                                $cont = substr_count($value->cuenta, ".");
                                if ($cont == 0) {
                                    $tpatrimonio = floatval(str_replace(',', '', $value->saldo));
                                    break;
                                }
                            }
                            $sheet->cell("F$i", function ($cell) use ($tpasivos, $tpatrimonio) {
                                // manipulate the cel
                                $cell->setValue(($tpasivos + $tpatrimonio));
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('right');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $sheet->cell("G$i", function ($cell) use ($tpasivos, $tpatrimonio) {
                                // manipulate the cel
                                $cell->setValue('100,00');
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('right');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            //  CONFIGURACION FINAL
                            $sheet->cells('A2:G2', function ($cells) {
                                // manipulate the range of cells
                                $cells->setBackground('#0070C0');
                                // $cells->setFontSize('10');
                                $cells->setFontWeight('bold');
                                $cells->setBorder('thin', 'thin', 'thin', 'thin');
                                $cells->setValignment('center');
                            });

                            $sheet->cells('A4:G4', function ($cells) {
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
                                'G' => 12,
                            ));
                        });
                    })->export('xlsx');
                }
            } else {
                $activos    = json_decode($request->datosBalanceA);
                $pasivos    = json_decode($request->datosBalanceP);
                $patrimonio = json_decode($request->datosBalancePat);
                if ($id_empresa == "0992704152001" || $id_empresa == "1391707460001") {
                    $totpyg = EstadoResultado::utilidad_gravable("2010-01-01", $fecha_hasta, '1');
                    $participacion = EstadoResultado::trabajadores("2010-01-01", $fecha_hasta, $id_empresa);
                } else {
                    $totpyg = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, '1');
                    $participacion = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
                }
                $periodo_desde = $this->fechaTexto($fecha_desde);
                $periodo_hasta = $this->fechaTexto($fecha_hasta);

                $vistaurl = "contable/balance_general/print";
                $view     = \View::make($vistaurl, compact(
                    'fecha_desde',
                    'fecha_hasta',
                    'empresa',
                    'activos',
                    'pasivos',
                    'patrimonio',
                    'totpyg',
                    'periodo_desde',
                    'periodo_hasta',
                    'participacion'
                ))->render();
                if ($request['exportar'] == 0) {
                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                    $pdf->setPaper('A4', 'portrait');

                    return $pdf->stream('BalaneGeneral-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
                } else {
                    $periodo_desde = $this->fechaTexto($fecha_desde);
                    $periodo_hasta = $this->fechaTexto($fecha_hasta);
                    $participacion = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
                    $ingresos = array();
                    $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, 0, 0);
                    //dd($patrimonio);
                    $totpyg           = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, '1');
                    $impuesto_causado = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, '1', null, 0, $ingresos);
                    $impuesto_causado = round($impuesto_causado, 2);
                    $totpyg = $totpyg - $impuesto_causado;
                    Excel::create('EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $periodo_desde, $periodo_hasta, $activos, $pasivos, $patrimonio, $totpyg, $participacion, $impuesto_causado) {
                        $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $periodo_desde, $periodo_hasta, $activos, $pasivos, $patrimonio, $totpyg, $participacion, $impuesto_causado) {
                            // dd($participacion);
                            $sheet->mergeCells('A1:G1');
                            $sheet->cell('A1', function ($cell) use ($empresa) {
                                // manipulate the cel
                                $cell->setValue($empresa->nombrecomercial);
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', '', 'thin');
                            });
                            $sheet->mergeCells('A2:G2');
                            $sheet->cell('A2', function ($cell) {
                                // manipulate the cel
                                $cell->setValue("ESTADO DE SITUACION FINANCIERA");
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->mergeCells('A3:G3');
                            $sheet->cell('A3', function ($cell) use ($periodo_desde, $periodo_hasta) {
                                // manipulate the ce
                                if ($periodo_desde == '01 de Enero de 2010') {

                                    $cell->setValue("al $periodo_hasta");
                                } else {
                                    $cell->setValue("$periodo_desde al $periodo_hasta");
                                }

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
                            // $sheet->mergeCells('A7:A8');
                            $sheet->cell('G4', function ($cell) {
                                // manipulate the cel
                                $cell->setValue('%');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            // DETALLES

                            $sheet->setColumnFormat(array(
                                'F' => '0.00',
                                'G' => '0.00',
                            ));
                            $i = $this->setDetalles($activos, $sheet, 5);
                            $i = $this->setDetalles($pasivos, $sheet, $i, '', $participacion, $impuesto_causado);
                            $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);
                            $sheet->mergeCells("A$i:E$i");
                            $sheet->cell("A$i", function ($cell) use ($empresa) {
                                // manipulate the cel
                                $cell->setValue("TOTAL PASIVO + PATRIMONIO");
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $tpatrimonio = 0;
                            $tpasivos = 0;
                            foreach ($pasivos as $value) {

                                $cont = substr_count($value['cuenta'], ".");
                                if ($cont == 0) {
                                    $tpasivos = $value['saldo'];
                                    break;
                                }
                            }
                            $tpasivos = $tpasivos + $impuesto_causado + $participacion;

                            foreach ($patrimonio as $value) {
                                if ($totpyg != "") {
                                    if (trim($value['cuenta']) == '3') {
                                        $value['saldo'] += $totpyg;
                                    }
                                }
                                $cont = substr_count($value['cuenta'], ".");
                                if ($cont == 0) {
                                    $tpatrimonio = $value['saldo'];
                                    break;
                                }
                            }
                            $sheet->cell("F$i", function ($cell) use ($tpasivos, $tpatrimonio) {
                                // manipulate the cel
                                $cell->setValue(($tpasivos + $tpatrimonio));
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('right');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $sheet->cell("G$i", function ($cell) use ($tpasivos, $tpatrimonio) {
                                // manipulate the cel
                                $cell->setValue('100,00');
                                $cell->setFontWeight('bold');
                                $cell->setFontSize('15');
                                $cell->setAlignment('right');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            //  CONFIGURACION FINAL
                            $sheet->cells('A2:G2', function ($cells) {
                                // manipulate the range of cells
                                $cells->setBackground('#0070C0');
                                // $cells->setFontSize('10');
                                $cells->setFontWeight('bold');
                                $cells->setBorder('thin', 'thin', 'thin', 'thin');
                                $cells->setValignment('center');
                            });

                            $sheet->cells('A4:G4', function ($cells) {
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
                                'G' => 12,
                            ));
                        });
                    })->export('xlsx');
                }
            }
        }
    }

    public function setSangria($cont, $cell, $indent = "")
    {
        switch ($cont) {
            case 0:
                $cell->setFontSize('12');
                $cell->setFontWeight('bold');
                break;
            case 1:
                $cell->setFontSize('11');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(1);
                }
                break;
            case 2:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(2);
                }
                break;
            case 3:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(3);
                }
                break;
            case 4:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(4);
                }
                break;
            default:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(5);
                }
                break;
        }
    }

    public function setDetallesP($data, $sheet, $i, $totpyg = "", $participacion = "", $impuesto_causado = "")
    {
        $exp_p = "";
        $exp_r = "";
        $cuenta_p = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PARTICIPACION_15');
        if (!is_null($cuenta_p)) {
            $exp_p = explode('.', $cuenta_p->secuencial_mostrar);
        }
        $cuenta_r = \Sis_medico\Ct_Configuraciones::obtener_cuenta('IMPUESTO_RENTA_PAGAR_BALANCE');
        if (!is_null($cuenta_r)) {
            $exp_r = explode('.', $cuenta_r->secuencial_mostrar);
        }
        foreach ($data as $value) {
            if ($value->saldo != 0) {
                $cont     = substr_count($value->cuenta, ".");


                $sheet->cell('A' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue(" " . $value->cuenta . " ");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $sheet->mergeCells('B' . $i . ':E' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel

                    $cell->setValue($value->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell, 1);
                });

                $sheet->cell('F' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $this->setSangria($cont, $cell);
                    $cell->setValue(str_replace(',', '', $value->saldo));
                    if ($value->saldo < 0) {
                        $cell->setFontColor('#ff0000');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) use ($value, $cont, $data) {
                    // manipulate the cel
                    $saldo = floatval(str_replace(',', '', $data[0]->saldo));
                    if ($saldo != 0) {
                        $porcent = ((floatval(str_replace(',', '', $value->saldo)) * 100) / $saldo);
                    } else {
                        $porcent = $saldo;
                    }
                    $cell->setValue("$porcent");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $i++;
            }
        }
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }


    public function setDetalles($data, $sheet, $i, $totpyg = "", $participacion = "", $impuesto_causado = "")
    {
        $exp_p = "";
        $exp_r = "";
        $cuenta_p = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PARTICIPACION_15');
        if (!is_null($cuenta_p)) {
            $exp_p = explode('.', $cuenta_p->secuencial_mostrar);
        }
        $cuenta_r = \Sis_medico\Ct_Configuraciones::obtener_cuenta('IMPUESTO_RENTA_PAGAR_BALANCE');
        if (!is_null($cuenta_r)) {
            $exp_r = explode('.', $cuenta_r->secuencial_mostrar);
        }
        foreach ($data as $value) {

            if ($participacion != "") {
                $entorno = "";
                if ($exp_p != "") {
                    foreach ($exp_p as $key => $value_parti) {
                        if ($key == 0) {
                            $entorno = $value_parti;
                        } else {
                            $entorno = $entorno . '.' . $value_parti;
                        }
                        if (trim($value['cuenta']) == $entorno) {
                            $value['saldo'] += $participacion;
                            break;
                        }
                    }
                } else {
                    if (trim($value['cuenta']) == '2') {
                        $value['saldo'] += $participacion;
                    }
                    if (trim($value['cuenta']) == '2.01') {
                        $value['saldo'] += $participacion;
                    }
                    if (trim($value['cuenta']) == '2.01.07') {
                        $value['saldo'] += $participacion;
                    }
                    if (trim($value['cuenta']) == '2.01.07.05') {
                        $value['saldo'] += $participacion;
                    }
                    if (trim($value['cuenta']) == '2.01.07.05.01') {
                        $value['saldo'] += $participacion;
                    }
                }
            }


            $entorno = "";
            if ($impuesto_causado != "") {
                if ($exp_r != "") {
                    foreach ($exp_r as $key => $value_parti) {
                        if ($key == 0) {
                            $entorno = $value_parti;
                        } else {
                            $entorno = $entorno . '.' . $value_parti;
                        }
                        if (trim($value['cuenta']) == $entorno) {
                            $value['saldo'] += $impuesto_causado;
                            break;
                        }
                    }
                } else {
                    if (trim($value['cuenta']) == '2') {
                        $value['saldo'] += $impuesto_causado;
                    }
                    if (trim($value['cuenta']) == '2.01') {
                        $value['saldo'] += $impuesto_causado;
                    }
                    if (trim($value['cuenta']) == '2.01.07') {
                        $value['saldo'] += $impuesto_causado;
                    }
                    if (trim($value['cuenta']) == '2.01.07.01') {
                        $value['saldo'] += $impuesto_causado;
                    }
                    if (trim($value['cuenta']) == '2.01.07.01.11') {
                        $value['saldo'] += $impuesto_causado;
                    }
                }
            }

            if (trim($value['cuenta']) == '3') {
                if ($value['cuenta'] == '3' and Auth::user()->id == '0922729587') {
                    //dd($value['saldo'] .' -- ' . $totpyg);
                }
                $value['saldo'] += $totpyg;
            }
            if ($totpyg != "") {
                //dd($totpyg);
            }
            if ($value['saldo'] != 0) {
                $nsaldo = 0;
                if (trim($value['cuenta']) == '3.07') {
                    $value['saldo'] += $totpyg;
                }
                if (trim($value['cuenta']) == '3.07.01') {
                    if ($totpyg > 0) {
                        $value['saldo'] += $totpyg;
                    } elseif ($value['saldo'] < 0) {
                        $nsaldo         = $value['saldo'];
                        $value['saldo'] = 0;
                    }
                }
                if (trim($value['cuenta']) == '3.07.02') {
                    if ($totpyg < 0) {
                        if ($value['cuenta'] == '3.07.02' and Auth::user()->id == '0922729587') {
                            //dd($value['saldo'] .' -- ' . $totpyg);
                        }
                        $value['saldo'] += ($nsaldo * (-1)) + ($totpyg * (-1));
                    }
                }
                $valor100 = 0;
                $cont     = substr_count($value['cuenta'], ".");
                if ($cont == 0) {
                    $valor100 = $value['saldo'];
                }

                $sheet->cell('A' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue(" " . $value['cuenta'] . " ");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $sheet->mergeCells('B' . $i . ':E' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel

                    $cell->setValue($value['nombre']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell, 1);
                });

                $sheet->cell('F' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $this->setSangria($cont, $cell);
                    $cell->setValue($value['saldo']);
                    if ($value['saldo'] < 0) {
                        $cell->setFontColor('#ff0000');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) use ($value, $cont, $valor100) {
                    // manipulate the cel
                    if ($valor100 != 0) {
                        $porcent = (($value['saldo'] * 100) / $valor100);
                    } else {
                        $porcent = $valor100;
                    }
                    $cell->setValue("$porcent");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $i++;
            }
        }
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }

    public function detalle($desde, $hasta, $tipo, $id_empresa, $cuentas_detalle = "")
    {

        $balance = array();
        if ($tipo == 'A') {
            $condicion = '1';
        } elseif ($tipo == 'P') {
            $condicion = '2';
        } else {
            $condicion = '3';
        }
        //DB::enableQueryLog();
        $desde = '1999-01-01 00:00:00';
        if ($cuentas_detalle == "") {
            $plans = Plan_Cuentas::where('p.estado', '<>', 0)
                ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
                ->where('p.id_empresa', session()->get('id_empresa'))
                ->whereRaw('character_length(p.plan) <= 8')
                ->where('p.plan', 'like', "$condicion%")
                //->orwhere('id', 'like', "$condicion")
                ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2')
                ->orderBy('p.plan', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('p.estado', '<>', 0)
                ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
                ->where('p.id_empresa', session()->get('id_empresa'))
                ->where('p.plan', 'like', "$condicion%")
                //->orwhere('id', 'like', "$condicion")
                ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2')
                ->orderBy('p.plan', 'asc')
                ->get();
        }
        if ($id_empresa == "0992704152001" || $id_empresa == "1391707460001") {
            $desde = "2010-01-01";
        }
        $i       = 0;
        $lastDay = date('t', strtotime("$hasta-01"));
        foreach ($plans as $plan) {
            $data = array();
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
                    ->where('pe.plan', 'like', $plan->id . '%')
                    ->where('pe.id_empresa', $id_empresa)
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->where('c.id_empresa', $id_empresa);
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento = $asiento->where('p.nombre', 'not like', '%-)%')
                        ->where('p.naturaleza', 1);
                    $asiento2 = Ct_Asientos_Detalle::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
                        ->where('pe.id_empresa', $id_empresa)
                        ->where('pe.plan', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->where('c.id_empresa', $id_empresa)
                        ->whereBetween('c.fecha_asiento', [$desde . " 00:00:00", $hasta . " 23:59:59"]);
                    $asiento2 = $asiento2->where(function ($asiento2) {
                        $asiento2->where([['p.nombre', 'like', '%-)%']])
                            ->orWhere([['p.naturaleza', 0]]);
                    });
                    if ($plan->naturaleza_2 == 1) {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                            ->get();
                    } else {
                        if (Auth::user()->id == "0922729587") {
                            //dd($asiento2->toSql());
                        }
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                    }
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta 23:59:59"]);
                if ($plan->naturaleza_2 == 1) {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                        ->get();
                } else {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                }
                $saldo = 0;
                foreach ($asiento as $row) {
                    if ($row->saldo == 0) {
                        $saldo = $saldo2 * (-1);
                    } elseif ($saldo2 == 0) {
                        $saldo = $row->saldo;
                    } elseif ($saldo2 < 0) {
                        $saldo = $row->saldo + $saldo2;
                    } else {
                        $saldo = $row->saldo - $saldo2;
                    }
                }
                if (session('id_empresa') != '1793178049001' || session('id_empresa') == '1391927177001') {
                    if (strtoupper($plan->nombre) == '(-) PERDIDA NETA DEL PERIODO') {
                        $data['cuenta'] = '3.06.02';
                    } else {
                        $data['cuenta'] = $plan->id;
                    }
                } else {
                    $data['cuenta'] = $plan->id;
                }
                $data['cuenta'] = $plan->id;
                $data['nombre'] = strtoupper($plan->nombre);
                $cadena = $data['cuenta'];
                $buscar = "3.01.01";
                $buscarReservas = '3.04.01';
                $resultado = strpos($cadena, $buscar);
                $resultado_ = strpos($cadena, $buscarReservas);
                if ($resultado !== FALSE) {
                    /*if ($saldo < 0)
                        $data['saldo']  = $saldo * -1;
                    else
                        $data['saldo']  = $saldo;*/
                    $data['saldo']  = $saldo;
                } elseif ($resultado_ !== FALSE) {
                    $data['saldo']  = $saldo;
                    /*if ($saldo < 0)
                        $data['saldo']  = $saldo * -1;
                    else
                        $data['saldo']  = $saldo;*/
                } else {
                    $data['saldo']  = $saldo;
                }
                // if ($data['cuenta'] == '3.01.02') {
                //     if ($saldo < 0)
                //         $data['saldo']  = $saldo * -1;
                //     else
                //         $data['saldo']  = $saldo;
                // } elseif ($data['cuenta'] == '3.01.02.01') {
                //     if ($saldo < 0)
                //         $data['saldo']  = $saldo * -1;
                //     else
                //         $data['saldo']  = $saldo;
                // } else {
                //     $data['saldo']  = $saldo;
                // }
                $balance[] = $data;
            }
        }
        return $balance;
    }
    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        return view('contable/diario/asiento', ['registro' => $registro]);
    }
    public function crear()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cuentas = plan_cuentas::where('estado', '2')->get();
        return view('contable/diario/create', ['cuentas' => $cuentas]);
    }
    public function buscar(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return view('contable/diario/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => $request['observacion'],
            'fecha_asiento'   => $request['fecha_asiento'] . ' ' . date('H:i:s'),
            'valor'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        for ($i = 1; $i <= $request['contador']; $i++) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $request['id_plan' . $i],
                'debe'                => $request['debe' . $i],
                'haber'               => $request['haber' . $i],
                'fecha'               => date('Y-m-d H:i:s'),
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        return redirect()->route('librodiario.index');
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
    public function redireccionar(Request $request)
    {
        $var = app(LibroDiarioController::class)->libro_mayor($request);
    }
}
