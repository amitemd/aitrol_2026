<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;

class BalanceComprobacionController extends Controller
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
        return view('contable/balance_comprobacion/index', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'balance' => $balance]);
    }

    public function show(Request $request)
    {

        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $cuentas_detalle = "";
        if (isset($request['cuentas_detalle'])) {
            $cuentas_detalle = $request['cuentas_detalle'];
        }

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];

                $rfecha_desde           = $request['fecha_desde'];
                $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
                $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
                $fecha_desde            = date('Y-m-d', $timestamp);

                $rfecha_hasta           = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta            = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }
            $fecha_mes_ant    = date("Y-m-d", strtotime($fecha_desde . "- 1 months"));
            $participacionant = EstadoResultado::trabajadores($fecha_mes_ant, $fecha_desde, $id_empresa);
            $participacion    = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);
            $balance          = array();
            $balance_ant      = $this->balance($fecha_mes_ant, $fecha_desde, $id_empresa, $cuentas_detalle);
            $balance          = $this->balance($fecha_desde, $fecha_hasta, $id_empresa, $cuentas_detalle);
            return view('contable/balance_comprobacion/index', [
                'fecha_desde' => $rfecha_desde,
                'fecha_hasta' => $rfecha_hasta,
                'empresa' => $empresa,
                'balance' => $balance,
                'balance_ant' => $balance_ant,
                'cuentas_detalle' => $cuentas_detalle,
                'participacion' => $participacion,
                'participacionant' => $participacionant
                /*,'mostrar_detalles'=>$mostrar_detalles*/
            ]);
        } else {
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];

                $rfecha_desde              = $request['filfecha_desde'];
                $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
                $fecha_desde               = date('Y-m-d', $timestamp);

                $rfecha_hasta              = $request['filfecha_hasta'];
                $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
                $fecha_hasta               = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
            }

            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }

            $balance       = array();
            $fecha_mes_ant = date("Y-m-d", strtotime($fecha_desde . "- 1 months"));
            $balance       = array();
            $balance_ant   = $this->balance($fecha_mes_ant, $fecha_desde, $id_empresa, $cuentas_detalle);
            $balance       = $this->balance($fecha_desde, $fecha_hasta, $id_empresa, $cuentas_detalle);

            $participacionant = EstadoResultado::trabajadores($fecha_mes_ant, $fecha_desde, $id_empresa);
            $participacion    = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa);

            $fecha_d = $this->fechaTexto($fecha_desde);
            $fecha_h = $this->fechaTexto($fecha_hasta);

            if ($request['exportar'] == "") {

                $vistaurl = "contable/balance_comprobacion/print";
                $view     = \View::make($vistaurl, compact('fecha_d', 'fecha_h', 'empresa', 'balance', 'balance_ant', 'participacionant', 'participacion'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('BalaneComprobacion-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {
                Excel::create('BalanceComprobacion-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $balance_ant, $balance, $participacionant, $participacion) {
                    $excel->sheet('BalanceComprobacion', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $balance_ant, $balance, $participacionant, $participacion) {
                        $sheet->mergeCells('A1:K1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:K2');
                        $sheet->cell('A2', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:K3');
                        $sheet->cell('A3', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("BALANCE COMPROBACION");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A4:K4');
                        $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                            // manipulate the cel
                            $cell->setValue("$fecha_desde al $fecha_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A5:E5');
                        $sheet->mergeCells('F5:G5');
                        $sheet->cell('F5', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Saldos iniciales');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValignment('center');
                            $cell->setBackground('#FFF81B');
                        });
                        $sheet->mergeCells('H5:I5');
                        $sheet->cell('H5', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Período');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValignment('center');
                            $cell->setBackground('#4DB2FF');
                        });
                        $sheet->mergeCells('J5:K5');
                        $sheet->cell('J5', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Saldos finales');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValignment('center');
                            $cell->setBackground('#FFABF2');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B6:E6');
                        $sheet->cell('B6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DEUDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ACREEDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('H6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DEUDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ACREEDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DEUDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ACREEDOR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        // DETALLES

                        $sheet->setColumnFormat(array(
                            'F' => '0.00',
                            'G' => '0.00',
                            'H' => '0.00',
                            'I' => '0.00',
                            'J' => '0.00',
                            'K' => '0.00',
                        ));

                        $i = $this->setDetalles($balance_ant, $balance, $sheet, 7, $participacionant, $participacion);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                        //  CONFIGURACION FINAL
                        $sheet->cells('A3:G3', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A6:K6', function ($cells) {
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
                            'H' => 12,
                            'I' => 12,
                            'J' => 12,
                            'K' => 12,
                        ));
                    });
                })->export('xlsx');
            }
        }
    }

    public function balance($desde, $hasta, $id_empresa, $cuentas_detalle = "")
    {
        $balance = array();
        if ($cuentas_detalle == "") {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                // ->whereRaw('character_length(id) <= 7')
                ->where('estado', '=', 1)
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->where('estado', '=', 2)
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
        }

        $i = 0;
        foreach ($plans as $plan) {
            $data = array();
            if ($plan->id != "") {

                $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '.%')
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    ->where('c.id_empresa', $id_empresa)
                    ->where('ct_asientos_detalle.estado', '<>', 0)
                    ->where('p.estado', '<>', 0)
                    ->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                    ->select(DB::raw('ifnull(SUM(debe),0) as debe'), DB::raw('ifnull(SUM(haber),0) as haber'))
                    ->get();
                $debe  = 0;
                $haber = 0;
                foreach ($asiento as $row) {
                    $debe  = $row->debe;
                    $haber = $row->haber;
                }
                $data['cuenta'] = $plan->id;
                $data['nombre'] = strtoupper($plan->nombre);
                $data['debe']   = $debe;
                $data['haber']  = $haber;
                $balance[]      = $data;
            }
        }
        return $balance;
    }

    public function setDetalles($balance1, $balance2, $sheet, $i, $participacionant = "", $participacion = "")
    {
        $x           = 0;
        $acum_debe1  = 0;
        $acum_haber1 = 0;
        $acum_debe2  = 0;
        $acum_haber2 = 0;
        $acum_debe3  = 0;
        $acum_haber3 = 0;
        foreach ($balance1 as $value) {

            $cuenta_participacion = \Sis_medico\Ct_Configuraciones::obtener_cuenta('BALANCECOMPROBACION_PARTICIP');

            if (trim($value['cuenta']) == $cuenta_participacion->cuenta_guardar) {
                $value['haber'] += $participacionant;
                $balance2[$x]['haber'] += $participacion;
            }
            // if(trim($value['cuenta'])=='2.01.07.05.01' ){}

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(" " . $value['cuenta'] . " ");
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
            });

            $sheet->mergeCells('B' . $i . ':E' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value['nombre']);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                // $this->setSangria($cont, $cell,1);
            });
            $acum_debe1 += $value['debe'];
            $acum_haber1 += $value['haber'];
            $sheet->cell('F' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($value['debe'], 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('G' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($value['haber'], 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $acum_debe2 += $balance2[$x]['debe'];
            $acum_haber2 += $balance2[$x]['haber'];
            $sheet->cell('H' . $i, function ($cell) use ($balance2, $x, $value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($balance2[$x]['debe'], 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('I' . $i, function ($cell) use ($balance2, $x, $value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($balance2[$x]['haber'], 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $acum_debe   = 0;
            $acum_haber  = 0;
            $saldo_debe  = 0;
            $saldo_haber = 0;
            $saldo       = 0;
            $acum_debe   = $balance2[$x]['debe'] + $value['debe'];
            $acum_haber  = $balance2[$x]['haber'] + $value['haber'];
            $saldo       = $acum_debe - $acum_haber;
            if ($saldo > 0) {
                $saldo_debe  = $saldo;
                $saldo_haber = 0;
            } else {
                $saldo_debe  = 0;
                $saldo_haber = (-1) * $saldo;
            }

            $acum_debe3 += $saldo_debe;
            $acum_haber3 += $saldo_haber;

            $sheet->cell('J' . $i, function ($cell) use ($saldo_debe, $value) {
                // manipulate the cel
                $cell->setValue(number_format($saldo_debe, 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('K' . $i, function ($cell) use ($saldo_haber, $value) {
                // manipulate the cel
                $cell->setValue(number_format($saldo_haber, 2));
                if (strlen($value['cuenta']) <= 9) {
                    $cell->setFontWeight('bold');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $i++;
            $x++;
        }

        $sheet->cell('A' . $i, function ($cell) use ($value) {
            // manipulate the cel
            $cell->setValue("");
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->mergeCells('B' . $i . ':E' . $i);
        $sheet->cell('B' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue("TOTALES");
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('F' . $i, function ($cell) use ($acum_debe1) {
            $cell->setValue(number_format($acum_debe1, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('G' . $i, function ($cell) use ($acum_haber1) {
            $cell->setValue(number_format($acum_haber1, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('H' . $i, function ($cell) use ($acum_debe2) {
            $cell->setValue(number_format($acum_debe2, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('I' . $i, function ($cell) use ($acum_haber2) {
            $cell->setValue(number_format($acum_haber2, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('J' . $i, function ($cell) use ($acum_debe3) {
            $cell->setValue(number_format($acum_debe3, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('K' . $i, function ($cell) use ($acum_haber3) {
            $cell->setValue(number_format($acum_haber3, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $i++;

        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F7:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G7:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('H7:H' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('I7:I' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('J7:J' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('K7:K' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
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
        return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
}
