<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Empresa;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_compras;
use PHPExcel_Style_Color;
use laravel\laravel;
use Carbon\Carbon;
use DateTime;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Contable;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Termino;
use Sis_medico\Http\Controllers\Apis\BdReportesFinancieros;
use Sis_medico\Http\Controllers\excelCreate;

class InformesAcreedorController extends Controller
{
    private $controlador = 'informes';
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function index_cartera(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $deudas = [];
        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        //$deudas = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
        $deudas = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        //dd($deudas);
        $groupDeudas = Contable::groupBy($deudas, "proveedor");
        //dd($groupDeudas);
        $proveedores = Proveedor::all();
        //dd($groupDeudas);
        return view('contable/carteras_pagar/index', ['cartera' => $groupDeudas, 'empresa' => $empresa, 'proveedores' => $proveedores, 'id_proveedor' => $proveedor, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde]);
    }
    public function cartera(Request $request)
    {
        $draw = $request->get('draw');
        $today = date('Y-m-d');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page 
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $proveedor = $request->id_proveedor;
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];
        $rec = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        $totalRecords = count($rec);
        $totalRecordswithFilter = count($rec);
        $records = Contable::groupBy($rec, "proveedor");;
        $data_arr = array();
        $sno = $start + 1;
        $vt = 0;
        $pt = 0;
        $pv = 0;
        foreach ($records as $key => $record) {
            //dd($record);
            $proveedor = Proveedor::find($key);
            $id = $proveedor->id;
            $name = $proveedor->razonsocial;
            $vencido = 0;
            $periodo = 0;
            $porvencer = 0;
            foreach ($record as $c) {
                // dd($c);
                $fec = new DateTime($today);
                $fec2 = new DateTime($c['fecha_termino']);
                $datetime1 = date_create($today);
                $datetime2 = date_create($c['fecha_termino']);
                $interval = date_diff($datetime1, $datetime2);
                $p = $interval->format('%R%a');
                $diff = $fec->diff($fec2);
                $days = $p;
                //$fecha_entrada = strtotime($c['fecha_termino']."00:00:00");
                if ($days < 0) {
                    $vencido += $c['valor_contable'];
                    $vt += $vencido;
                }
                if ($days > 10 && $days <= 31) {
                    $periodo += $c['valor_contable'];
                    $pt += $periodo;
                }
                if ($days > 0 && $days <= 10) {
                    $porvencer += $c['valor_contable'];
                    $pv += $porvencer;
                }
            }
            $data_arr[] = array(
                "id" => $id,
                "proveedor" => $name,
                "details" => $record,
                "vencido" => $vencido,
                "periodo" => $periodo,
                "porvencer" => $porvencer
            );
        }


        $finalData['vencido_total'] = $vt;
        $finalData['periodo_total'] = $pt;
        $finalData['porvencer_total'] = $pv;
        $response = array(

            "draw" => intval($draw),

            "iTotalRecords" => $totalRecords,

            "iTotalDisplayRecords" => $totalRecordswithFilter,

            "aaData" => $data_arr

        );


        echo json_encode($response);

        exit;
    }
    public function index_saldos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $deudas = null;
        $fecha_desde = $request['fecha_desde'];
        $proveedor = $request['id_proveedor'];
        $fecha_hasta = $request['fecha_hasta'];
        $deudas = [];
        if (!is_null($fecha_hasta)) {

            $deudas = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
            if ($fecha_desde == null) {
                $deudas = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
            }
            if ($fecha_desde == null && $fecha_hasta == null) {
                $deudas = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
            }
        }
        $proveedores = Proveedor::all();
        return view('contable/informe_saldos/index', ['informe' => $deudas, 'id_proveedor' => $proveedor, 'proveedores' => $proveedores, 'empresa' => $empresa, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde]);
    }
    public function informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $var)
    {
        $tipo = 2;
        $deudas = null;
        if ($var == 0) {
            if ($proveedor != null) {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('proveedor', $proveedor)->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            } else {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            }
        } else if ($var == 1) {
            if ($proveedor != null) {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->where('fecha', '<=', $fecha_hasta)->where('proveedor', $proveedor)->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            } else {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->where('fecha', '<=', $fecha_hasta)->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            }
        } else if ($var == 2) {
            if ($proveedor != null) {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->where('proveedor', $proveedor)->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            } else {
                $deudas = Ct_compras::where('estado', '>', '0')->where('id_empresa', $id_empresa)->select(DB::raw('sum(total_final) as total'), 'proveedor')->groupBy('proveedor')->get();
            }
        }
        return $deudas;
    }
    public function informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $var)
    {
        $deudas = null;
        /*         if ($var == 0) {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->with(['compras' => function ($query) use ($proveedor) {
                    $query->where('proveedor', $proveedor)->groupBy('proveedor');
                }])
                    ->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->get();
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->with(['compras' => function ($query) {
                    $query->groupBy('proveedor');
                }])
                    ->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->get();
            }
        } else if ($var == 1) {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('fecha_asiento', '<', $fecha_hasta)->with(['compras' => function ($query) use ($proveedor) {
                    $query->where('proveedor', $proveedor)->groupBy('proveedor');
                }])->where('id_empresa', $id_empresa)->get();
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('fecha_asiento', '<', $fecha_hasta)->with(['compras' => function ($query) {
                    $query->groupBy('proveedor');
                }])->where('id_empresa', $id_empresa)->get();
            }
        } else if ($var == 2) {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->with(['compras' => function ($query) use ($proveedor) {
                    $query->where('proveedor', $proveedor)->groupBy('proveedor');
                }])->where('id_empresa', $id_empresa)->get();
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->with(['compras' => function ($query) {
                    $query->groupBy('proveedor');
                }])->where('id_empresa', $id_empresa)->get();
            }
        } */

        $deudas = Ct_compras::where('estado', '<>', '0')->where('id_empresa', $id_empresa)->where('valor_contable', '>', '0');
        if ($fecha_desde != null && $fecha_hasta != null) {
            $deudas = $deudas->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_hasta != null) {
            $deudas = $deudas->where('fecha', '<=', $fecha_hasta);
        }
        if ($proveedor != null) {
            $deudas = $deudas->where('proveedor', $proveedor);
        }
        $deudas = $deudas->get();
        if ($var == 1) {
            $deudas = $deudas->toArray();
        }
        return $deudas;
    }
    public function excel_saldos2(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $proveedor = $request['id_proveedor2'];
        $fecha_hasta = $request['filfecha_hasta'];
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
        if ($fecha_desde == null) {
            $consulta = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        }
        if ($fecha_hasta == null && $fecha_desde == null) {
            $consulta = $this->informe_saldos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 2);
        }
        Excel::create('Informe Cartera Por Pagar ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:H2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:H3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME CARTERA POR PAGAR");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:H4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . $fecha_desde . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue("Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('CUENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('VENCIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('PERIODO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('POR VENCER');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                $i = $this->setDetalles5($consulta, $sheet, 6, $fecha_hasta);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:H3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:H5', function ($cells) {
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
                    'H'     =>  12,
                ));
            });
        })->export('xlsx');
    }
    public function setDetalles5($consulta, $sheet, $i, $fecha_hasta)
    {
        $acumulador = 0;
        $acumulador2 = 0;
        $acumulador3 = 0;
        $acumulador4 = 0;
        $result = array();
        $valor_por_vencer = 0;
        $valor_vencido = 0;
        $valor_periodo = 0;
        foreach ($consulta as $value) {
            if (isset($value) && $value != null) {
                $fecha_actual = strtotime($value["fecha"] . "00:00:00");
                $fecha_entrada = strtotime($value["fecha_termino"] . "00:00:00");

                if ($value->termino == 6) {
                    if ($fecha_actual <= $fecha_hasta) {
                    } else {
                        $acumulador += $value["valor_contable"];
                        $acumulador2 += $value["valor_contable"];
                        $valor_vencido = $value["valor_contable"];
                    }
                } else {
                    if ($fecha_actual < $fecha_entrada) {
                        $acumulador += $value["valor_contable"];
                        $acumulador3 += $value["valor_contable"];
                        $valor_periodo = $value["valor_contable"];
                    }
                    if ($fecha_actual == $fecha_entrada) {
                        $acumulador += $value["valor_contable"];
                        $acumulador4 += $value["valor_contable"];
                        $valor_por_vencer = $value["valor_contable"];
                    }
                }
            }
            if (isset($value) && $value != null) {
                if ($valor_por_vencer > 0 || $valor_vencido > 0 || $valor_periodo > 0) {
                    $sheet->cell('A' . $i, function ($cell) use ($value) {

                        $cell->setValue(" " . $value->proveedorf->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->mergeCells('B' . $i . ':E' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->proveedorf->nombrecomercial);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($valor_vencido) {

                        // $this->setSangria($cont, $cell);
                        $cell->setValue($valor_vencido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($valor_periodo) {

                        // $this->setSangria($cont, $cell);
                        $cell->setValue($valor_periodo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($valor_por_vencer) {

                        // $this->setSangria($cont, $cell);
                        $cell->setValue($valor_por_vencer);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                }
            }
        }
        $sheet->cell('B' . $i, function ($cell) use ($valor_periodo) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue("TOTAL:");
            $cell->setFontWeight('bold');
        });
        $sheet->cell('F' . $i, function ($cell) use ($acumulador2) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($acumulador2, 2, '.', ''));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('G' . $i, function ($cell) use ($acumulador3) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($acumulador3, 2, '.', ''));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('H' . $i, function ($cell) use ($acumulador4) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($acumulador4, 2, '.', ''));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }
    public function excel_saldos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $proveedor = $request['id_proveedor2'];
        $fecha_hasta = $request['filfecha_hasta'];
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
        if ($fecha_desde == null && $fecha_hasta == null) {
            $consulta = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 2);
        }
        if ($fecha_desde == null) {
            $consulta = $this->informe_saldos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        }
        Excel::create('Informe Saldo ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:H2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:H3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME SALDO");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:H4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue(" Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('CUENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('DEUDAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('ANTICIPOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('SALDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                $i = $this->setDetalles($consulta, $sheet, 6);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:H3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:H5', function ($cells) {
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
                    'H'     =>  12,
                ));
            });
        })->export('xlsx');
    }
    function fechaTexto($fecha)
    {
        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
    public function setDetalles($consulta, $sheet, $i)
    {
        $x = 0;
        $valor_base = 0;
        $anticipo = 0;
        $retenciones = 0;
        $totalrete = 0;
        $totales = 0;
        $totales2 = 0;
        $totales3 = 0;
        foreach ($consulta as $value) {

            /* $consulta= DB::table('ct_comprobante_egreso')->where('id_proveedor',$value->proveedor)->get();  if($consulta!=null){ foreach($consulta as $val){ $anticipo+= $val->valor_pago; } } 
            $proveedor= DB::table('proveedor')->where('id',$value->proveedor)->first(); $totales+=$value->total; $totales2+=$anticipo; 
            $valor_base= $value->total-$anticipo; $totales3+=$valor_base;  */
            $anticipo = 0;
            $retenciones = 0;
            $consulta = DB::table('ct_comprobante_egreso')->where('id_proveedor', $value->proveedor)->get();
            if ($consulta != null) {
                foreach ($consulta as $val) {
                    $anticipo += $val->valor_pago;
                    $detalles = DB::table('ct_detalle_comprobante_egreso')->where('id_comprobante', $val->id)->get();
                    if ($detalles != null) {
                        foreach ($detalles as $x) {
                            $consulta_retenciones = DB::table('ct_retenciones')->where('id_compra', $x->id_compra)->first();
                            if ($consulta_retenciones != null) {
                                $retenciones += $consulta_retenciones->valor_fuente + $consulta_retenciones->valor_iva;
                            }
                        }
                    }
                }
            }
            $proveedor = DB::table('proveedor')->where('id', $value->proveedor)->first();
            $totalrete = $value->total - $retenciones;
            $totales += $totalrete;
            $totales2 += $anticipo;
            $valor_base = $totalrete - $anticipo;
            $totales3 += $valor_base;
            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(" " . $value->proveedor);

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });

            $sheet->mergeCells('B' . $i . ':E' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($proveedor) {
                // manipulate the cel 
                $cell->setValue($proveedor->nombrecomercial);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell,1);
            });

            $sheet->cell('F' . $i, function ($cell) use ($totalrete) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($totalrete, 2));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('G' . $i, function ($cell) use ($anticipo) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($anticipo, 2));

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('H' . $i, function ($cell) use ($valor_base) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(number_format($valor_base, 2));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $i++;
            $x++;
        }
        $sheet->cell('B' . $i, function ($cell) use ($anticipo) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue("TOTAL :");
            $cell->setFontWeight('bold');
        });
        $sheet->cell('F' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($totales, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('G' . $i, function ($cell) use ($totales2) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($totales2, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('H' . $i, function ($cell) use ($totales3) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue(number_format($totales3, 2));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }
    public function index_cheques(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $cheques = [];
        if (!is_null($fecha_hasta)) {
            $cheques = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 0);
            if ($fecha_desde == null) {
                $cheques = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 1);
            }
            if ($fecha_desde == null && $fecha_hasta == null) {
                $cheques = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 2);
            }
        }
        $proveedores = Proveedor::all();
        return view('contable/cheques_girados/index', ['cheques' => $cheques, 'empresa' => $empresa, 'proveedores' => $proveedores, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor]);
    }
    public function cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, $r)
    {
        $cheques = null;
        /* $cheques = null;
        if ($r == 0) {
            if ($proveedor != null) {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.id_proveedor', $proveedor)
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->wherebetween('co.fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'co.id_asiento_cabecera', 'coe.fecha_comprobante as f2', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            } else {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->wherebetween('co.fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'coe.fecha_comprobante as f2', 'co.id_asiento_cabecera', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            }
        } else if ($r == 1) {
            if ($proveedor != null) {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.id_proveedor', $proveedor)
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->where('co.fecha_comprobante', '<', $fecha_hasta)
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'co.id_asiento_cabecera', 'coe.fecha_comprobante as f2', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            } else {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.fecha_comprobante', '<', $fecha_hasta)
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'co.id_asiento_cabecera', 'coe.fecha_comprobante as f2', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            }
        } else if ($r == 2) {
            if ($proveedor != null) {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.id_proveedor', $proveedor)
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'coe.fecha_comprobante as f2', 'co.id_asiento_cabecera', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            } else {
                $cheques = DB::table('proveedor as p')
                    ->rightjoin('ct_comprobante_egreso as co', 'co.id_proveedor', 'p.id')
                    ->leftjoin('ct_comprobante_egreso_varios as coe', 'coe.id_proveedor', 'p.id')
                    ->leftjoin('ct_caja_banco as banco', 'banco.id', 'co.id_caja_banco')
                    ->leftjoin('ct_caja_banco as banco1', 'banco1.id', 'coe.id_caja_banco')
                    ->where('co.id_empresa', $id_empresa)
                    ->orwhere('coe.id_empresa', $id_empresa)
                    ->select('p.nombrecomercial', 'p.id', 'banco.numero_cuenta', 'co.fecha_comprobante as f1', 'coe.fecha_comprobante as f2', 'co.id_asiento_cabecera', 'banco.nombre', 'co.secuencia as secuencia1', 'coe.secuencia as secuencia2', 'co.fecha_cheque as fecha1', 'coe.fecha_cheque as fecha2', 'coe.nro_cheque as cheque1', 'co.no_cheque as cheque2', 'co.valor_pago as valor_cabecera', 'coe.valor')
                    ->get();
            }
        }
        */

        //$egreso= Ct_Comprobante_Egreso::where('id_empresa',$id_empresa)->where('estado','<>',0);
        //$egresov= Ct_Comprobante_Egreso_Varios::where('id_empresa',$id_empresa)->where('estado','<>',0);
        /******************Nueva Consulta**********************/
        $egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id_pago', 2);
        $egresov = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id_pago', 2);
        if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {
            $egreso = $egreso->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            $egresov = $egresov->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($fecha_hasta)) {
            $egreso = $egreso->where('fecha_comprobante', '<=', $fecha_hasta);
            $egresov = $egresov->where('fecha_comprobante', '<=', $fecha_hasta);
        }
        if (!is_null($proveedor)) {
            $egreso = $egreso->where('id_proveedor', $proveedor);
            $egresov = $egresov->where('id_proveedor', $proveedor);
        }
        //dd($egreso->get());
        $egresov = $egresov->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'nro_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'beneficiario as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago');
        $egreso = $egreso->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'no_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'id_proveedor as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago')->union($egresov)->orderBy('fecha_comprobante', 'DESC')->get();
        $cheques = $egreso;
        //dd($cheques[0]);
        return $cheques;
    }
    public function excel_cheque(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $proveedor = $request['id_proveedor2'];
        $fecha_hasta = $request['filfecha_hasta'];
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 0);
        if ($fecha_desde == null) {
            $consulta = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 1);
        }
        if ($fecha_desde == null && $fecha_hasta == null) {
            $consulta = $this->cheques($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, 2);
        }
        Excel::create('Informe Cheques Girados ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:L2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:L3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME CHEQUES GIRADOS");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:L4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B5:C5');
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('NÚMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E5:G5');
                $sheet->cell('E5', function ($cell) {
                    $cell->setValue('BENEFICIARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H5:I5');
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('BANCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('FECHA CHQ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('# Chq.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    $cell->setValue('VALOR BASE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'L' => '0.00',
                    'M' => '0.00',
                ));

                $i = $this->setDetalles2($consulta, $sheet, 6);
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

                $sheet->cells('A5:L5', function ($cells) {
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
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  12,
                    'M'     =>  12,

                ));
            });
        })->export('xlsx');
    }
    public function setDetalles2($consulta, $sheet, $i)
    {
        $x = 0;
        $acumulador = 0;
        $resta = 0;
        $anticipo = 0;
        foreach ($consulta as $value) {
            if ($value->valor_cabecera != null) {
                $acumulador += round(($value->valor_cabecera), 2);
            } else {
            }

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->f1) != null) {
                    $cell->setValue($value->f1);
                } else {
                    $cell->setValue($value->f2);
                }

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });

            $sheet->mergeCells('B' . $i . ':C' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel 
                if (($value->secuencia1) != null) {
                    $cell->setValue($value->secuencia1 . " Asiento # " . $value->id_asiento_cabecera);
                } else {
                    $cell->setValue($value->secuencia2 . " Asiento # " . $value->id_asiento_cabecera);
                }

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell,1);
            });

            $sheet->cell('D' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->id);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('E' . $i . ':G' . $i);
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->nombrecomercial);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('H' . $i . ':I' . $i);
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->nombre);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('J' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->fecha1) != null) {
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha1)));
                } else {
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha2)));
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('K' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->cheque1) != null) {
                    $cell->setValue($value->cheque1);
                } else {
                    $cell->setValue($value->cheque2);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('L' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->valor_cabecera) != null) {
                    $cell->setValue($value->valor_cabecera);
                } else {
                    $cell->setValue($value->valor);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
            $x++;
        }
        $sheet->cell('K' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL: ');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($acumulador) {
            // manipulate the cel
            $cell->setValue($acumulador);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        return $i;
    }
    public function index_retenciones(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $secuencia = $request['secuencia'];
        $variable = 1;
        if (!is_null($request['id_proveedor'])) {
            $proveedor = $request['id_proveedor'];
        } else {
            $proveedor = $request['id_proveedor2'];
        }
        $retenciones = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 0);
        $variable2 = 0;
        $retenciones2 = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable2, $secuencia, 0);
        $total1 = 0;
        $total2 = 0;
        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        /*   if ($fecha_desde == null) {
            $retenciones = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 1);
            $retenciones2 = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable2, $secuencia, 1);
        }
        if ($fecha_desde == null && $fecha_hasta == null) {
            $retenciones = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 2);
            $retenciones2 = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable2, $secuencia, 2);
        } */
        $retenciones = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 1);
        /*         foreach ($retenciones2 as $value) {
            if ($value->estado != 0) {
                $total1 += $value->valor_fuente;
                $total2 += $value->valor_iva;
            }
        } */
        $r = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 0);
        if (count($r) > 0) {
            foreach ($r as $fuente) {
                if ($fuente->estado == 1)
                    $total1 += $fuente->valor_fuente;
            }
        }
        if (count($r) > 0) {
            foreach ($r as $fuente) {
                if ($fuente->estado == 1)
                    $total2 += $fuente->valor_iva;
            }
        }
        //dd($r->where('estado', 1)->sum('valor_fuente'));
        // $total1 = $r->where('ct_retenciones.estado', 1)->sum('ct_retenciones.valor_fuente');
        // $total2 = $r->where('ct_retenciones.estado', 1)->sum('ct_retenciones.valor_iva');
        $proveedores = Proveedor::all();
        $renta = [];
        $iva = [];

        return view('contable/retenciones/informes', ['retenciones' => $retenciones, 'proveedores' => $proveedores, 'total1' => $total1, 'total2' => $total2, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'id_proveedor' => $proveedor]);
    }
    public function retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, $r)
    {

        $retenciones = null;
        /*if ($r == 0) {
            if ($secuencia != null) {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->get();
                    }
                }
            } else {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('id_empresa', $id_empresa)->paginate(10);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::wherebetween('fecha', array($fecha_desde, $fecha_hasta))->where('id_empresa', $id_empresa)->get();
                    }
                }
            }
        } else if ($r == 1) {
            if ($secuencia != null) {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('fecha', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('fecha', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('fecha', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('fecha', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->get();
                    }
                }
            } else {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('fecha', '<', $fecha_hasta)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('fecha', '<', $fecha_hasta)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('fecha', '<', $fecha_hasta)->paginate(10);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('fecha', '<', $fecha_hasta)->get();
                    }
                }
            }
        } elseif ($r == 2) {
            if ($secuencia != null) {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('secuencia', $secuencia)->where('id_empresa', $id_empresa)->get();
                    }
                }
            } else {
                if ($proveedor != null) {
                    $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->paginate(20);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->where('id_proveedor', $proveedor)->get();
                    }
                } else {
                    $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->paginate(10);
                    if ($variable == 0) {
                        $retenciones = Ct_Retenciones::where('id_empresa', $id_empresa)->get();
                    }
                }
            }
        } */
        //$retenciones= Ct_Retenciones::where('estado','<>','0')->where('id_emppresa',$id_empresa);
        $retenciones = Ct_Retenciones::where('ct_retenciones.id_empresa', $id_empresa)->join('ct_compras as ct_com', 'ct_com.id', 'ct_retenciones.id_compra')
            ->select('ct_retenciones.fecha', 'ct_retenciones.estado', 'ct_retenciones.id_proveedor', 'ct_retenciones.nro_comprobante', 'ct_retenciones.id_compra', 'ct_com.subtotal', 'ct_com.subtotal_0', 'ct_com.subtotal_12', 'ct_retenciones.*');


        if (!isset($request['fecha_desde'])) {
            $request['fecha_desde'] = "01-01-" . date('Y');
        }
        if (!isset($request['fecha_hasta'])) {
            $request['fecha_hasta'] = "31-12-" . date('Y');
        }


        if ($fecha_desde != null && $fecha_hasta != null) {
            $retenciones = $retenciones->whereBetween('ct_retenciones.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_desde != null) {
            $retenciones = $retenciones->where('ct_retenciones.fecha', '>=', $fecha_desde);
        }
        if ($fecha_hasta != null) {
            $retenciones = $retenciones->where('ct_retenciones.fecha', '<=', $fecha_hasta);
        }
        if ($proveedor != null) {
            $retenciones = $retenciones->where('ct_retenciones.id_proveedor', $proveedor);
        }
        if (!is_null($secuencia)) {
            $retenciones = $retenciones->where('ct_retenciones.nro_comprobante', 'like', "%{$secuencia}%");
        }
        if ($r == 1) {
            $retenciones = $retenciones->orderBy('ct_retenciones.fecha', 'DESC');
        }
        $retenciones = $retenciones->get();

        return $retenciones;
    }
    public function excel_retenciones(Request $request)
    {

        $request->request->add(["tipo" => "RA", "estado" => "T"]);

        // dd($request->all());
        return DeclaracionTributacionController::descargarExcel($request);

        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $proveedor = $request['id_proveedor'];
        $secuencia = $request['secuencia'];
        if (!is_null($request['id_proveedor'])) {
            $proveedor = $request['id_proveedor'];
        } else {
            $proveedor = $request['id_proveedor2'];
        }
        $fecha_hasta = $request['fecha_hasta'];
        $variable = 0;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 0);
        if ($fecha_desde == null) {
            $consulta = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 1);
        }
        if ($fecha_desde == null && $fecha_hasta == null) {
            $consulta = $this->retenciones($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $variable, $secuencia, 2);
        }
        Excel::create('Informe Retenciones ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Retenciones', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta) {
                $sheet->mergeCells('A1:S1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:S2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:S3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME RETENCIONES");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:S4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue("Al  " . date("d-m-Y", strtotime($fecha_hasta)));
                    }

                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('NUMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    $cell->setValue('PREIMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D5:F5');
                $sheet->cell('D5', function ($cell) {
                    $cell->setValue('ACREEDOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('RUC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H5:I5');
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('BASE 0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    $cell->setValue('BASE 12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    $cell->setValue('TOTAL RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    $cell->setValue('PORCENTAJE RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    $cell->setValue('TOTAL RFIVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    $cell->setValue('PORCENTAJE RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    $cell->setValue('CREADO POR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S5', function ($cell) {
                    $cell->setValue('ANULADO POR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'G' => '@',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'O' => '0.00',
                ));

                $i = $this->setDetalles3($consulta, $sheet, 6);
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

                $sheet->cells('A5:S5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalles3($consulta, $sheet, $i)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $totales2 = 0;
        $total_subtotal = 0;
        $total_subtotal0 = 0;
        $total_subtotal12 = 0;
        foreach ($consulta as $value) {
            if ($value->estado != 0) {
                $totales += $value->valor_fuente;
                $totales2 += $value->valor_iva;
                $total_subtotal  += $value->subtotal;
                $total_subtotal0 += $value->subtotal_0;
                $total_subtotal12 += $value->subtotal_12;
            }

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(date("d-m-Y", strtotime($value->fecha)));

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });

            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $f = '';
                if (($value->compras->tipo) == 1) {
                    $f = 'COM-FA';
                } else {
                    $f = 'COM-FACT';
                }
                $cell->setValue($f . ' : ' . $value->compras->numero);

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell,1);
            });

            $sheet->cell('C' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->nro_comprobante);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('D' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue('ACR-RT');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('E' . $i . ':F' . $i);
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->proveedor->nombrecomercial);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('G' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue(" " . $value->proveedor->id);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('H' . $i . ':I' . $i);
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->descripcion);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('J' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->subtotal);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('K' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->subtotal_0);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('L' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->subtotal_12);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('M' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->valor_fuente);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('N' . $i, function ($cell) use ($value) {
                // manipulate the cel

                if (($value->detalle) != null) {
                    $string = "";
                    foreach ($value->detalle as $val) {
                        if (($val->porcentajer->tipo) == 2) {
                            $string = strval($val->porcentajer->valor) . " % " . $string;
                        }
                    }
                    $cell->setValue($string);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('O' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->valor_iva);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('P' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $string = "";
                if (($value->detalle) != null) {
                    foreach ($value->detalle as $val) {
                        if (($val->porcentajer->tipo) == 1) {
                            $string = strval($val->porcentajer->valor) . " % " . $string;
                        }
                    }
                    $cell->setValue($string);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('Q' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->estado) != 0) {
                    $cell->setValue('ACTIVO');
                } else {
                    $cell->setValue('ANULADO');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('R' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->usuario) != null) {
                    $cell->setValue($value->usuario->nombre1 . $value->usuario->apellido1);
                } else {
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('S' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->usuario) != null && ($value->estado) == 0) {
                    $cell->setValue($value->usuario->nombre1 . $value->usuario->apellido1);
                } else {
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
            $x++;
        }
        $sheet->cell('I' . $i, function ($cell) {
            // manipulate the cel

            $cell->setValue("TOTAL: ");
            $cell->setFontWeight('bold');
        });
        $sheet->cell('J' . $i, function ($cell) use ($total_subtotal) {
            // manipulate the cel
            $cell->setValue($total_subtotal);
            $cell->setFontWeight('bold');
        });
        $sheet->cell('K' . $i, function ($cell) use ($total_subtotal0) {
            // manipulate the cel
            $cell->setValue($total_subtotal0);
            $cell->setFontWeight('bold');
        });
        $sheet->cell('L' . $i, function ($cell) use ($total_subtotal12) {
            // manipulate the cel
            $cell->setValue($total_subtotal12);
            $cell->setFontWeight('bold');
        });
        $sheet->cell('M' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            $cell->setValue($totales);
            $cell->setFontWeight('bold');
        });
        $sheet->cell('O' . $i, function ($cell) use ($totales2) {
            // manipulate the cel
            $cell->setValue($totales2);
            $cell->setFontWeight('bold');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        return $i;
    }
    public function index_deudas_pagos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $deudas = [];
        $total = 0;
        $debe = 0;
        $haber = 0;
        $deudas_val = [];
        $variable = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        if ($proveedor == null) {
            $proveedor = $request['id_proveedor2'];
        }
        //dd($proveedor);
        if (!is_null($fecha_hasta)) {
            $deudas = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
            /*            
            $deudas_val = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);

            foreach ($deudas_val as $value) {
                if ($value != null) {


                    if (($value->egresos) != null) {
                     
                            $total += $value->valor_contable;
                            $debe += $value->total_final;
                            foreach ($value->egresos as $v) {
                                $haber += $v->abono;
                            }
                        
                        
                    }
                    if (($value->cruce) != null) {
                      
                            $total += $value->valor_contable;
                            $debe += $value->total_final;
                            foreach ($value->cruce as $v) {
                                $haber += $v->total;
                            }
                        
                        
                    }
                    if (($value->retenciones) != null) {
                        
                            $total += $value->valor_contable;
                            $debe += $value->total_final;
                            foreach ($value->retenciones as $rete) {
                                if ($rete->estado == 1) {
                                    $totals = ($rete->valor_fuente) + ($rete->valor_iva);
                                    $haber += $totals;
                                }
                            }
                        
                        
                    }
                }
            } */
        }

        //dd($deudas);
        $proveedores = Proveedor::all();
        //dd($proveedores);
        //dd($deudas[0]);
        return view('contable/deudasvspagos/index', ['deudas' => $deudas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'totales' => $total, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'debe' => $debe, 'haber' => $haber]);
    }
    public function deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $paginate)
    {
        $deudas = null;
        /* if (!is_null($fecha_desde)) {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::whereHas('compras', function ($q) use ($proveedor, $id_empresa) {
                    $q->where('proveedor', $proveedor)->where('id_empresa', $id_empresa)->orderBy('f_autorizacion', 'asc');
                })->where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->orderBy('fecha_asiento', 'asc')->get();
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::whereHas('compras', function ($q) use ($proveedor) {
                        $q->where('proveedor', $proveedor)->orderBy('f_autorizacion', 'asc');
                    })->where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->orderBy('fecha_asiento', 'asc')->where('id_empresa', $id_empresa)->get();
                }
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->orderBy('fecha_asiento', 'asc')->where('id_empresa', $id_empresa)->get();
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->orderBy('fecha_asiento', 'asc')->where('id_empresa', $id_empresa)->get();
                }
            }
        } else {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::whereHas('compras', function ($q) use ($proveedor, $id_empresa) {
                    $q->where('proveedor', $proveedor)->where('id_empresa', $id_empresa)->orderBy('f_autorizacion', 'asc');
                })->where('estado', '!=', 'null')->where('id_empresa', $id_empresa)->orderBy('fecha_asiento', 'asc')->get();
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::whereHas('compras', function ($q) use ($proveedor) {
                        $q->where('proveedor', $proveedor)->orderBy('f_autorizacion', 'asc');
                    })->where('estado', '!=', 'null')->orderBy('fecha_asiento', 'asc')->where('id_empresa', $id_empresa)->get();
                }
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('id_empresa', $id_empresa)->orderBy('fecha_asiento', 'asc')->get();
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('id_empresa', $id_empresa)->orderBy('fecha_asiento', 'asc')->get();
                }
            }
        } */
        $deudas = Ct_compras::leftjoin('ct_asientos_detalle as ad', function ($q) {
            $q->on('ct_compras.id_asiento_cabecera', 'ad.id_asiento_cabecera')
                ->where('ad.descripcion', 'like', '%PROVEEDORE%');
            //->orWhere('ad.descripcion', 'like', '%CUENTAS Y DOCUMENTOS POR PAGAR%');
        })
            ->where('ct_compras.estado', '<>', '0')->where('ct_compras.id_empresa', $id_empresa);
        if ($fecha_desde != null && $fecha_hasta != null) {
            $deudas = $deudas->wherebetween('ct_compras.f_autorizacion', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_hasta != null) {
            $deudas = $deudas->where('ct_compras.f_autorizacion', '<=', $fecha_hasta);
        }
        if ($proveedor != null) {
            $deudas = $deudas->where('ct_compras.proveedor', $proveedor);
        }

        $deudas = $deudas->orderBy('ct_compras.fecha', 'DESC')->get([
            'ct_compras.id',
            'ct_compras.numero',
            'ct_compras.secuencia_f',
            'ct_compras.punto_emision',
            'ct_compras.sucursal',
            'ct_compras.id_asiento_cabecera',
            'ct_compras.tipo',
            'ct_compras.fecha_termino',
            'ct_compras.fecha',
            'ct_compras.estado',
            'ct_compras.archivo_sri',
            'ct_compras.proveedor',
            'ct_compras.direccion_proveedor',
            'ct_compras.termino',
            'ct_compras.orden_compra',
            'ct_compras.f_caducidad',
            'ct_compras.tipo_gasto',
            'ct_compras.autorizacion',
            'ct_compras.f_autorizacion',
            'ct_compras.serie',
            'ct_compras.secuencia_factura',
            'ct_compras.credito_tributario',
            'ct_compras.tipo_comprobante',
            'ct_compras.observacion',
            'ct_compras.subtotal',
            'ct_compras.descuento',
            'ct_compras.subtotal_0',
            'ct_compras.subtotal_12',
            'ct_compras.iva_total',
            'ct_compras.ice_total',
            'ct_compras.transporte',
            'ct_compras.valor_contable',
            'ct_compras.total_final',
            'ct_compras.id_empresa',
            'ct_compras.id_usuariocrea',
            'ct_compras.id_usuariomod',
            'ct_compras.ip_creacion',
            'ct_compras.ip_modificacion',
            'ct_compras.created_at',
            'ct_compras.updated_at',
            'ct_compras.pedido',
            'ct_compras.rutapdf',
            'ct_compras.doc_electronico',
            'ct_compras.electronica',
            'ct_compras.nro_comprobante',
            'ct_compras.nro_autorizacion',
            'ct_compras.forma_pago',
            'ct_compras.emisor',
            'ct_compras.clave_acceso',
            'ad.id_plan_cuenta',
            'ad.descripcion'
        ]);
        return $deudas;
    }
    public function excel_deudas(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $proveedor = $request['id_proveedor2'];
        $gastos = $request['es_fact_dos'];
        if ($gastos == null) {
            $gastos = 0;
        }
        $fecha_hasta = $request['filfecha_hasta'];
        //$variable = 1;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        Excel::create('Informe Deudas Vs Pagos ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Deudas vs Pagos', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:L2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:L3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME DEUDAS VS PAGOS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:L4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->mergeCells('A5:D5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G5:H5');
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('REF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('DEBE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('HABER');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    $cell->setValue('SALDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                ));
                $i = $this->setDetalles4($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:L3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:L5', function ($cells) {
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
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  12,
                    'M'     =>  12,

                ));
            });
        })->export('xlsx');
    }
    public function setDetalles4($consulta, $sheet, $i, $variable)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $debe = 0;
        $haber = 0;
        foreach ($consulta as $value) {
            if ($value != null) {
                if ($value->estado > 0) {
                    $totales += $value->valor_contable;
                    $debe += $value->total_final;

                    $sheet->mergeCells('A' . $i . ':D' . $i);
                    $sheet->cell('A' . $i, function ($cell) use ($value) {

                        if (($value->secuencia_f) != null && ($value->numero) != null) {
                            $cell->setValue(isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '' . " Fact : #" . $value->secuencia_f . " Ref: " . $value->numero);
                        }

                        $cell->setFontWeight('bold');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {

                        if ($value->f_autorizacion != null) {
                            $cell->setValue(date("d-m-Y", strtotime($value->f_autorizacion)));
                        }
                        $cell->setFontWeight('bold');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($variable) {

                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('COM-FA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G' . $i . ':H' . $i);
                    $sheet->cell('G' . $i, function ($cell) use ($value) {

                        // $this->setSangria($cont, $cell);
                        if ($value->numero != null) {
                            $cell->setValue($value->numero);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {

                        // $this->setSangria($cont, $cell);
                        if ($value->total_final != null) {
                            $cell->setValue($value->total_final);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {

                        // $this->setSangria($cont, $cell);
                        if ($value->total_final != null) {
                            $cell->setValue($value->total_final);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {

                        // $this->setSangria($cont, $cell);
                        $cell->setValue('0.00');
                        $cell->setFontWeight('bold');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {

                        // $this->setSangria($cont, $cell);

                        $cell->setValue(number_format($value->valor_contable, 2, '.', ''));
                        $cell->setValignment('center');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    if ($value->egresos != null & $value->egresos != '[]') {

                        foreach ($value->egresos as $v) {
                            if ($v->comp_egreso->estado == 1) {
                                $haber += $v->abono;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                    if ($v->comp_egreso->secuencia != null) {
                                        $cell->setValue("    " . isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '' . " # " . $v->comp_egreso->secuencia . " Ref: " . $value->numero);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {

                                    if ($v->comp_egreso->fecha_comprobante != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('ACR-EG');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->comp_egreso->secuencia != null) {
                                        $cell->setValue($v->comp_egreso->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->saldo_base != null) {
                                        $cell->setValue($v->saldo_base);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->abono != null) {
                                        $cell->setValue($v->abono);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    if ($value->bndebito != null & $value->bndebito != '[]') {

                        foreach ($value->bndebito as $v) {
                            if (isset($v->cabecera)) {
                                if ($v->cabecera->estado == 1) {
                                    $haber += $v->abono;
                                    $sheet->mergeCells('A' . $i . ':D' . $i);
                                    $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                        if ($v->cabecera->concepto != null) {
                                            $varNombreComercial = isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '';
                                            $cell->setValue("    " . $varNombreComercial . " # " . $v->cabecera->concepto . " Ref: " . $value->numero);
                                        } else {
                                            $cell->setValue("");
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('E' . $i, function ($cell) use ($v) {

                                        if ($v->cabecera->fecha != null) {
                                            $cell->setValue(date("d-m-Y", strtotime($v->cabecera->fecha)));
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell,1);
                                    });
                                    $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                        // $this->setSangria($cont, $cell);

                                        $cell->setValue('BAN-ND');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->mergeCells('G' . $i . ':H' . $i);
                                    $sheet->cell('G' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->cabecera->secuencia != null) {
                                            $cell->setValue($v->cabecera->secuencia);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('I' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->saldo != null) {
                                            $cell->setValue($v->saldo);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('K' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->abono != null) {
                                            $cell->setValue($v->abono);
                                        }
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('L' . $i, function ($cell) use ($value) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $i++;
                                }
                            }
                        }
                    }
                    if ($value->credito_acreedor != null & $value->credito_acreedor != '[]') {

                        foreach ($value->credito_acreedor as $v) {
                            if (isset($v->estado)) {
                                if ($v->estado == 1) {
                                    $haber += $v->abono;
                                    $sheet->mergeCells('A' . $i . ':D' . $i);
                                    $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                        if ($v->concepto != null) {
                                            $varNombreComercial = isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '';
                                            $cell->setValue("    " . $varNombreComercial . " # " . $v->concepto . " Ref: " . $value->numero);
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('E' . $i, function ($cell) use ($v) {

                                        if ($v->fecha != null) {
                                            $cell->setValue(date("d-m-Y", strtotime($v->fecha)));
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell,1);
                                    });
                                    $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                        // $this->setSangria($cont, $cell);

                                        $cell->setValue('ACR-NC');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->mergeCells('G' . $i . ':H' . $i);
                                    $sheet->cell('G' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->secuencia != null) {
                                            $cell->setValue($v->secuencia);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('I' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->subtotal != null) {
                                            $cell->setValue($v->subtotal);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('K' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->subtotal != null) {
                                            $cell->setValue($v->subtotal);
                                        }
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('L' . $i, function ($cell) use ($value) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $i++;
                                }
                            }
                        }
                    }
                    if ($value->cruce != null & $value->cruce != '[]') {
                        foreach ($value->cruce as $v) {
                            if (isset($v->cabecera)) {
                                if ($v->cabecera->estado == 1) {
                                    $haber += $v->total;
                                    $sheet->mergeCells('A' . $i . ':D' . $i);
                                    $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                        if ($v->cabecera->secuencia != null) {
                                            $varNombreComercial = isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '';
                                            $cell->setValue("    " . $varNombreComercial . " # " . $v->cabecera->secuencia . " Ref: " . $value->numero);
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('E' . $i, function ($cell) use ($v) {

                                        if ($v->cabecera->fecha_pago != null) {
                                            $cell->setValue(date("d-m-Y", strtotime($v->cabecera->fecha_pago)));
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell,1);
                                    });
                                    $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                        // $this->setSangria($cont, $cell);

                                        $cell->setValue('ACR-CR-AF');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->mergeCells('G' . $i . ':H' . $i);
                                    $sheet->cell('G' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->cabecera->secuencia != null) {
                                            $cell->setValue($v->cabecera->secuencia);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('I' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->total_factura != null) {
                                            $cell->setValue($v->total_factura);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('K' . $i, function ($cell) use ($v) {

                                        // $this->setSangria($cont, $cell);
                                        if ($v->total != null) {
                                            $cell->setValue($v->total);
                                        }
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('L' . $i, function ($cell) use ($value) {

                                        // $this->setSangria($cont, $cell);
                                        $cell->setValue('0.00');
                                        $cell->setValignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $i++;
                                }
                            }
                        }
                    }
                    if ($value->cruce_cuentas != null & $value->cruce_cuentas != '[]') {

                        foreach ($value->cruce_cuentas as $v) {
                            if ($v->estado == 1) {
                                $haber += $v->total;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                    if ($v->secuencia != null) {
                                        $varNombreComercial = isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '';
                                        $cell->setValue("    " . $varNombreComercial . " # " . $v->secuencia . " Ref: " . $value->numero);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {

                                    if ($v->fecha != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->fecha)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CRUCE-CUENTAS');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->secuencia != null) {
                                        $cell->setValue($v->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    if ($value->retenciones != null & $value->retenciones != '[]') {
                        foreach ($value->retenciones as $x) {
                            if ($x->estado == 1) {
                                $suma = $x->valor_fuente + $x->valor_iva;
                                $totals = ($x->valor_fuente) + ($x->valor_iva);
                                $haber += $totals;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $x) {

                                    if ($x->secuencia != null) {
                                        $varNombreComercial = isset($value->proveedorf->nombrecomercial) ? $value->proveedorf->nombrecomercial : '';
                                        $cell->setValue("    " . $varNombreComercial . " # " . $x->secuencia . " Ref: " . $value->numero);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($x) {

                                    if ($x->fecha != null) {
                                        $cell->setValue(date("d-m-Y", strtotime(substr($x->fecha, 0, 10))));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('ACR-RE');

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($x) {

                                    // $this->setSangria($cont, $cell);
                                    if ($x->secuencia != null) {
                                        $cell->setValue($x->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($suma) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue($suma);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($x) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($suma) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue($suma);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($x) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                                $x++;
                            }
                        }
                    }
                    if ($value->masivos != null & $value->masivos != '[]') {

                        foreach ($value->masivos as $v) {
                            if ($v->comp_egreso->estado == 1) {
                                $haber += $v->abono;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {

                                    if ($v->comp_egreso->descripcion != null) {
                                        $varRazonSocial = isset($value->proveedorf->razonsocial) ? $value->proveedorf->razonsocial : '';
                                        $cell->setValue("    " . $varRazonSocial . " # " . $v->comp_egreso->descripcion . " Ref: " . $value->numero);
                                    } else {
                                        $cell->setValue("");
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {

                                    if ($v->comp_egreso->fecha_comprobante != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($variable) {

                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('ACR-MASIVO');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->comp_egreso->secuencia != null) {
                                        $cell->setValue($v->comp_egreso->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->saldo_base != null) {
                                        $cell->setValue($v->saldo_base);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {

                                    // $this->setSangria($cont, $cell);
                                    if ($v->abono != null) {
                                        $cell->setValue($v->abono);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {

                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        $sheet->mergeCells('A' . $i . ':D' . $i);
        $sheet->cell('A' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue("TOTAL :");
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($debe) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($debe);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($haber) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($haber);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        return $i;
    }
    public function informe_compras(Request $request)
    {
        config(['data' => []]);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $variable = 0;
        $variable2 = 0;
        $totales = 0;
        $subtotal12 = 0;
        $subtotal0 = 0;
        $subtotal = 0;
        $descuento = 0;
        $impuesto = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $secuencia = $request['secuencia_f'];
        $deudas = [];
        $deudas2 = [];
        $proveedores = Proveedor::all();
        $estado = $request->estado;
        //dd($request->estado);
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $secuencia, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $estado);

            $deudas2 = $this->informe_final($proveedor, $secuencia, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $estado);
            $totales = $deudas2->where('estado', '<>', '0')->sum('total_final');
            $subtotal12 = $deudas2->where('estado', '<>', '0')->sum('subtotal_12');
            $subtotal = $deudas2->where('estado', '<>', '0')->sum('subtotal');
            $subtotal0 = $deudas2->where('estado', '<>', '0')->sum('subtotal_0');
            $descuento = $deudas2->where('estado', '<>', '0')->sum('descuento');
            $impuesto = $deudas2->where('estado', '<>', '0')->sum('iva_total');
        }

        $busq = [
            'estado'    => $request->estado,
            'tipo'      => $tipo
        ];
        //  dd($deudas[0]);
        //dd($busq['estado']);
        $data['controlador'] = $this->controlador;
        config(['data' => $data]);
        return view('contable/compra/informe', ['informe' => $deudas, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo, 'busq' => $busq]);
    }
    public function informe_final($proveedor, $secuencia, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $referencia, $r, $estado)
    {
        $deudas = Ct_compras::where('id_empresa', $id_empresa);
        if (!is_null($secuencia)) {
            $deudas = $deudas->where('numero', 'like', '%' . $secuencia . '%');
        }

        if (!is_null($estado) || $estado != "") {
            if ($estado == 0) {
                $deudas = $deudas->where('estado', $estado);
            } elseif ($estado == 1) {
                $deudas = $deudas->where('estado', '>', '0');
            }
        }

        if (!is_null($proveedor)) {
            $deudas = $deudas->where('proveedor', $proveedor);
        }

        if (($referencia) != 0) {
            if ($referencia == 1) {
                $deudas = $deudas->where('tipo', $referencia);
            } elseif ($referencia == 2) {
                $deudas = $deudas->whereNull('tipo_gasto')
                    ->where('tipo', $referencia);
            } else {
                $deudas->whereNotNull('tipo_gasto')
                    ->where('tipo', '2');
            }
        }
        if (!is_null($fecha_desde)) {
            $deudas = $deudas->wherebetween('f_autorizacion', [str_replace('/', '-', $fecha_desde), str_replace('/', '-', $fecha_hasta)])->orderBy('f_autorizacion', 'ASC');
        } else {
            $deudas = $deudas->where('f_autorizacion', '<=', str_replace('/', '-', $fecha_hasta))->orderBy('f_autorizacion', 'ASC');
        }
        if ($variable == 0) {
            $deudas = $deudas->get();
        } else {
            $deudas = $deudas->paginate(20);
            //dd($deudas);
        }
        //dd($deudas);
        return $deudas;
    }
    public function excel_informe_compras(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $proveedor = $request['id_proveedor2'];
        $fecha_hasta = $request['filfecha_hasta'];
        $gastos = $request['tipo2'];
        $variable = 0;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, 0);
        Excel::create('Informe Factura de Compras ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Factura de Compras', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:C1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:Q2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:R3');
                $sheet->cell('C3', function ($cell) {
                    $cell->setValue("INFORME FACTURA DE COMPRAS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:Q4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('13');
                    $cell->setFontColor('#FFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    $cell->setValue('PROVEEDOR');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    $cell->setValue('TIPO');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('TIPO COMPROBANTE');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('DIVISA');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    $cell->setValue('ICE ');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    $cell->setValue('TOTAL');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    $cell->setValue('ANULADO POR');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',

                ));
                $i = $this->setDetalleInforme($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('C2:R3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('left');
                });
                $sheet->cells('C3:R3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('left');
                });

                $sheet->cells('A5:R5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalleInforme($consulta, $sheet, $i, $variable)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $subtotal12 = 0;
        $subtotal0 = 0;
        $subtotal = 0;
        $descuento = 0;
        $impuesto = 0;
        $totales = $consulta->where('estado', '<>', '0')->sum('total_final');
        $subtotal12 = $consulta->where('estado', '<>', '0')->sum('subtotal_12');
        $subtotal = $consulta->where('estado', '<>', '0')->sum('subtotal');
        $subtotal0 = $consulta->where('estado', '<>', '0')->sum('subtotal_0');
        $descuento = $consulta->where('estado', '<>', '0')->sum('descuento');
        $impuesto = $consulta->where('estado', '<>', '0')->sum('iva_total');
        foreach ($consulta as $value) {
            if ($value != null) {
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    $cell->setValue(date("d-m-Y", strtotime($value->f_autorizacion)));
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setFontWeight('bold');

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    $cell->setValue($value->numero);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    $cell->setValue(' ' . $value->proveedor);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->proveedorf != null) {
                        $cell->setValue($value->proveedorf->nombrecomercial);
                    } else {
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->tipo == 1) {
                        $cell->setValue('COM-FA');
                    } else {
                        $cell->setValue('COM-FACT');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->master_tipos->nombre);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValue("# Asiento " . $value->id_asiento_cabecera);

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('$');
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue(number_format($value->subtotal_12, 2, '.', ''));
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    $cell->setValue(number_format($value->subtotal_0, 2, '.', ''));
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->subtotal);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->descuento > 0) {
                        $cell->setValue($value->descuento);
                    } else {
                        $cell->setValue('0.00');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->iva_total > 0) {
                        $cell->setValue($value->iva_total);
                    } else {
                        $cell->setValue('0.00');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->ice > 0) {
                        $cell->setValue($value->ice);
                    } else {
                        $cell->setValue('0.00');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->total_final);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue('ANULADA');
                    } else {
                        $cell->setValue('ACTIVO');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R' . $i, function ($cell) use ($value) {
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            }
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($descuento) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue('0.00');
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('O' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function index_deudas_pendientes(Request $request)
    {
        //dd ("EPA");
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $proveedores = Proveedor::all();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $observacion = $request['concepto'];
        $variable2 = 0;
        $totales = 1;
        $totales2 = 0;
        $variable = 1;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        if ($observacion == null) {
            $observacion = $request['observacion2'];
        }
        $deudas = [];
        if (!is_null($fecha_hasta)) {
            $deudas = $this->deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, $observacion, 0);

            if ($fecha_desde == null) {

                $deudas = $this->deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, $observacion, 1);
            }
        }
        /*if (Auth::user()->id == '0921605895') {
            echo '<pre>';print_r($deudas);exit;
        }*/

        return view('contable/deudas_pendientes/index', ['informe' => $deudas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'observacion' => $observacion, 'totales' => $totales, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo, 'totales2' => $totales2]);
    }
    public function deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, $observacion, $var)
    {
        $deudas = null;
        $variable = 0;

        $var = 0;
        /*  if ($fecha_desde != null) {
            if ($tipo == 0) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($proveedor, $id_empresa, $observacion, $fecha_desde, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('estado', '<>', '0')->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->where('valor_contable', '>', '0')->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $id_empresa, $fecha_hasta, $observacion) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($proveedor, $id_empresa, $fecha_desde, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $fecha_hasta, $id_empresa) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            } else if ($tipo == 1) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query)  use ($proveedor, $id_empresa, $fecha_desde, $fecha_hasta, $observacion) {
                            $query->where('proveedor', $proveedor)->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('tipo', 1)->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $fecha_hasta, $id_empresa, $observacion) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('tipo', 1)->where('valor_contable', '>', '0')->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query)  use ($proveedor, $fecha_desde, $id_empresa, $fecha_hasta, $observacion) {
                            $query->where('proveedor', $proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('tipo', 1)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $fecha_hasta, $id_empresa) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('tipo', 1)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            } else if ($tipo == 2) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query)  use ($proveedor, $id_empresa, $fecha_desde, $fecha_hasta, $observacion) {
                            $query->where('proveedor', $proveedor)->where('estado', '<>', '0')->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('tipo', 2)->where('observacion', 'like', '%' . $observacion . '%')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $id_empresa, $fecha_hasta, $observacion) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('tipo', 2)->where('observacion', 'like', '%' . $observacion . '%')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query)  use ($proveedor, $fecha_desde, $fecha_hasta, $observacion, $id_empresa) {
                            $query->where('proveedor', $proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('tipo', 2)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($fecha_desde, $fecha_hasta, $observacion, $id_empresa) {
                            $query->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('estado', '<>', '0')->where('tipo', 2)->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            }
        } else {
            if ($tipo == 0) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query) use ($proveedor, $observacion, $fecha_hasta, $id_empresa) {
                            $query->where('proveedor', $proveedor)->where('fecha', '<=', $fecha_hasta)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($observacion, $fecha_hasta, $id_empresa) {
                            $query->where('fecha', '<', $fecha_hasta)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query) use ($proveedor, $id_empresa, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('fecha', '<=', $fecha_hasta)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($id_empresa, $fecha_hasta) {
                            $query->where('fecha', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            } else if ($tipo == 1) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query)  use ($proveedor, $id_empresa, $observacion, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('fecha', '<=', $fecha_hasta)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('tipo', 1)->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($id_empresa, $fecha_hasta, $observacion) {
                            $query->where('fecha', '<', $fecha_hasta)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('tipo', 1)->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query)  use ($proveedor, $fecha_desde, $id_empresa, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('fecha', '<=', $fecha_hasta)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->where('tipo', 1)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($id_empresa, $fecha_hasta, $observacion) {
                            $query->where('fecha', '<', $fecha_hasta)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('tipo', 1)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            } else if ($tipo == 2) {
                if ($observacion != null) {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query)  use ($proveedor, $observacion, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('fecha', '<=', $fecha_hasta)->where('tipo', 2)->where('observacion', 'like', '%' . $observacion . '%')->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($observacion, $fecha_hasta) {
                            $query->where('fecha', '<', $fecha_hasta)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('tipo', 2)->where('observacion', 'like', '%' . $observacion . '%')->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                } else {
                    if ($proveedor != null) {
                        $deudas = Proveedor::where('estado', '>', '0')->where('id', $proveedor)->with(['compras' => function ($query)  use ($proveedor, $id_empresa, $observacion, $fecha_hasta) {
                            $query->where('proveedor', $proveedor)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('fecha', '<=', $fecha_hasta)->where('tipo', 2)->where('observacion', 'like', '%' . $observacion . '%')->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    } else {
                        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($id_empresa, $fecha_hasta) {
                            $query->where('fecha', '<', $fecha_hasta)->where('valor_contable', '>', '0')->where('estado', '<>', '0')->where('tipo', 2)->where('id_empresa', $id_empresa)->orderBy('fecha_termino', 'asc');
                        }])->get();
                    }
                }
            }
        } */
        $deudas = Proveedor::where('estado', '>', '0')->with(['compras' => function ($query) use ($proveedor, $id_empresa, $observacion, $fecha_desde, $fecha_hasta) {
            $query = $query->where('estado', '<>', 0)->where('id_empresa', $id_empresa);
            if ($proveedor != null) {
                $query = $query->where('proveedor', $proveedor);
            }
            if ($fecha_desde == null) {
                $query = $query->where('fecha', '<=', $fecha_hasta)->where('fecha', '>', '2010-01-01');
            }
            if ($fecha_desde != null && $fecha_hasta != null) {
                $query = $query->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            }
            if ($observacion != null) {
                $query = $query->where('observacion', 'like', '%' . $observacion . '%');
            }
            $query = $query->where('estado', '>', 0);
            $query = $query->where('valor_contable', '>', '0')->orderBy('fecha_termino', 'asc');
        }])->get();
        //  dd($proveedor);
        return $deudas;
    }
    public function excel_deudas_pendientes(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['filfecha_desde'];
        $observacion = $request['observacion2'];
        $proveedor = $request['id_proveedor2'];
        $gastos = $request['tipo2'];
        if ($gastos == null) {
            $gastos = 0;
        }
        $fecha_hasta = $request['filfecha_hasta'];
        $variable = 0;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, $observacion, 0);
        if ($fecha_desde == null) {
            $consulta = $this->deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, $observacion, 1);
        }
        if ($fecha_desde == null && $fecha_hasta == null) {
            $consulta = $this->deudas_pendientes($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, $observacion, 2);
        }
        Excel::create('Informe Deudas Pendientes ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Deudas Pendientes', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:C1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                /*   $sheet->mergeCells('C1:M1');
                $sheet->cell('C1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                }); */
                $sheet->mergeCells('C2:M2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:M3');
                $sheet->cell('C3', function ($cell) {
                    $cell->setValue("Informe Deudas Pendientes ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                /*                 $sheet->mergeCells('A1:M1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:M2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:M3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("Informe Deudas Pendientes ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                }); */
                $sheet->mergeCells('A4:M4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');

                $i = $this->setDeudasPendientes($consulta, $sheet, 6, $gastos, $fecha_hasta);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('C3:P3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:P2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('C4:P4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
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
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  12,
                    'M'     =>  12,

                ));
            });
        })->export('xlsx');
    }
    public function setDeudasPendientes($consulta, $sheet, $i, $variable, $fecha_hasta)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $totales2 = 0;
        foreach ($consulta as $value) {
            if (count($value->compras) > 0) {
                $sheet->mergeCells('A' . $i . ':' . 'P' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    $cell->setValue($value->compras[0]->proveedorf->id . '   |  ' . $value->compras[0]->proveedorf->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
                $sheet->cell('A' . $i, function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('DIAS');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    $cell->setValue('VENCIMIENTO');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setValue('DIAS VENCIDOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    $cell->setValue('TIPO');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    $cell->setValue('NÚMERO');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    $cell->setValue('PROVEEDOR');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H' . $i . ':' . 'M' . $i);
                $sheet->cell('H' . $i, function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    $cell->setValue('DIV');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    $cell->setValue('SALDO');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFF');
                    $cell->setBackground('BFBFBF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'L' => '0.00',
                    'M' => '0.00',
                ));
                $i++;
                foreach ($value->compras as $val) {
                    if ($val->estado != 0) {

                        //dd($fecha_hasta);
                        $totales += $val->valor_contable;
                        $totales2 += $val->total_final;
                        $valor += $val->total_final;
                        $resta += $val->valor_contable;
                        //changes for Paola date : 17 de Nov
                        $days = "0  dias";
                        $daysf = "0";
                        if (!is_null($val->fecha_termino)) {
                            $fec = new DateTime($val->f_autorizacion);
                            $fec2 = new DateTime($val->fecha_termino);
                            $diff = $fec->diff($fec2);
                            $days = $diff->days . ' dias ';
                            $fech = new DateTime($val->fecha_termino);
                            $fech2 = new DateTime($fecha_hasta);
                            $diff2 = $fech2->diff($fech);
                            $days2 = $diff2->days . ' dias ';
                            $daysf = $diff2->format("%r%a");
                        }
                        $sheet->cell('A' . $i, function ($cell) use ($val) {

                            $cell->setValue(date("d-m-Y", strtotime($val->fecha)));
                            $cell->setFontWeight('bold');

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell);
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($val, $days, $daysf) {

                            $cell->setValue($days);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell,1);
                        });

                        if (($val->fecha_termino) != null) {
                            $sheet->cell('C' . $i, function ($cell) use ($val) {

                                $cell->setValue(date("d-m-Y", strtotime($val->fecha_termino)));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                // $this->setSangria($cont, $cell,1);
                            });
                        } else {
                            $sheet->cell('C' . $i, function ($cell) {

                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                // $this->setSangria($cont, $cell,1);
                            });
                        }


                        $sheet->cell('D' . $i, function ($cell) use ($daysf) {


                            if ($daysf < 0) {
                                $cell->setFontColor("#FF5733");
                                $cell->setValue($daysf . " dias");
                            } else {
                                $cell->setValue($daysf . " dias");
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell,1);
                        });
                        //$sheet->mergeCells('G'.$i.':H'.$i);  
                        $sheet->cell('E' . $i, function ($cell) use ($val) {

                            // $this->setSangria($cont, $cell);
                            if ($val->tipo == 1) {
                                $cell->setValue('COM-FA');
                            } else {
                                $cell->setValue('COM-FACT');
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($val) {

                            $cell->setValue($val->numero);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell,1);
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($val) {

                            if ($val->proveedorf != null) {
                                $cell->setValue($val->proveedorf->nombrecomercial);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell,1);
                        });
                        $sheet->mergeCells('H' . $i . ':' . 'M' . $i);
                        $sheet->cell('H' . $i, function ($cell) use ($val) {

                            // $this->setSangria($cont, $cell);
                            $cell->setValue("Fact #: " . $val->secuencia_f . " Ref:" . $val->numero . " " . $val->observacion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('N' . $i, function ($cell) use ($val) {

                            // $this->setSangria($cont, $cell);
                            $cell->setValue('$');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('O' . $i, function ($cell) use ($val) {

                            // $this->setSangria($cont, $cell);
                            if (($val->total_final) != null) {
                                $cell->setValue($val->total_final);
                            } else {
                                $cell->setValue('0.00');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('P' . $i, function ($cell) use ($val) {

                            // $this->setSangria($cont, $cell);
                            if (($val->valor_contable) != null) {
                                $cell->setValue($val->valor_contable);
                            } else {
                                $cell->setValue('0.00');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }

                $sheet->cell('M' . $i, function ($cell) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue("TOTAL :");
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($totales2) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($totales2);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totales) {
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($totales);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

                $sheet->cells('F5:F' . $i, function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells('G5:G' . $i, function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells('H5:H' . $i, function ($cells) {
                    $cells->setAlignment('center');
                });
                $totales = 0;
                $totales2 = 0;
                $i++;
            }
        }
        $i++;
        $sheet->cell('M' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('O' . $i, function ($cell) use ($valor) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($valor);
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('P' . $i, function ($cell) use ($resta) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($resta);
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $sheet->cells('H5:H' . $i, function ($cells) {
            $cells->setAlignment('center');
        });
        $totales = 0;
        $totales2 = 0;
        return $i;
    }
    public function reporte_tiempo(Request $request)
    {
        $termino = Ct_Termino::all();
        //dd($termino);
        if ($request->fecha_hasta == null) {
            $request->fecha_hasta = date('Y-m-d');
        }
        $compras = $this->dataFactura($request);
        $proveedores = Proveedor::all();
        $empresa = Empresa::find($request->session()->get('id_empresa'));
        return view('contable.tiempos.index', ['compras' => $compras, 'proveedores' => $proveedores, 'request' => $request, 'empresa' => $empresa]);
    }
    public function dataFactura($request)
    {
        $compras = Ct_compras::where('id_empresa', $request->session()->get('id_empresa'))
            ->where('estado', '>', 0)->where('valor_contable', '>', 0)->where('tipo', '<>', 3);
        if ($request->fecha_desde != null && $request->fecha_hasta != null) {
            $compras = $compras->whereBetween('fecha', [$request->fecha_desde . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
        }
        if ($request->fecha_hasta != null && $request->fecha_desde == null) {
            $compras = $compras->where('fecha', '<=', $request->fecha_hasta);
        }
        if ($request->id_proveedor != null) {
            $compras = $compras->where('proveedor', $request->id_proveedor);
        }
        $seccion = [];
        //$termino= Ct_Termino::all();
        $compras = $compras->get()->toArray();
        $seccion = Contable::groupBy($compras, "termino");
        return $seccion;
    }

    public function deudasvspagos_pdf(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request->filfecha_desde;
        $proveedor = $request->id_proveedor2;
        $gastos = $request->gastos;
        if ($gastos == null) {
            $gastos = 0;
        }
        $fecha_hasta = $request->filfecha_hasta;
        //$variable = 1;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $deudas = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);

        $vistaurl = "contable.deudasvspagos.deudasvspagos_pdf";
        $view     = \View::make($vistaurl, compact('deudas', 'empresa', 'id_empresa', 'fecha_hasta', 'fecha_desde', 'proveedor', 'gastos'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Informe Deudas vs Pagos' . '.pdf');
    }


    public function index_cheques_emitidos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        // dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $tipo = is_null($request['tipo']) ? -1 : $request['tipo'];
        $n_cheque = $request['n_cheque'];

        $cheques["cheques"] = [];
        if (!is_null($fecha_hasta)) {
            $cheques = InformesAcreedorController::chequesEmitidos($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, $tipo, $n_cheque);
        }
        $proveedores = Proveedor::all();
        // dd($tipo);
        return view('contable/cheques_girados/index_emitidos', [
            'cheques' => $cheques["cheques"],
            'empresa' => $empresa,
            'proveedores' => $proveedores,
            'fecha_hasta' => $fecha_hasta,
            'fecha_desde' => $fecha_desde,
            'id_proveedor' => $proveedor,
            "tipo" => $tipo,
            "n_cheque" => $n_cheque
        ]);
    }

    public static function chequesEmitidos($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, $tipo, $n_cheque = "")
    {
        $cheques = null;

        /******************Nueva Consulta**********************/
        $egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id_pago', 2);
        $egresov = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id_pago', 2);
        $egresom = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->where('id_pago', 2);

        if (!is_null($tipo)) {
            if ($tipo == 1 or $tipo == 0) {
                $egreso = $egreso->where("estado", $tipo);
                $egresov = $egresov->where("estado", $tipo);
                $egresom = $egresom->where("estado", $tipo);
            }
            if ($tipo == 2) {
                $egreso = $egreso->where("check", 1);
                $egresov = $egresov->where("check", 1);
                $egresom = $egresom->where("check", 1);
            }

            if ($tipo == 3) {
                $egreso = $egreso->where("check", 0)->where("estado", 1);
                $egresov = $egresov->where("check", 0)->where("estado", 1);
                $egresom = $egresom->where("check", 0)->where("estado", 1);
            }
        }

        if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {
            $egreso = $egreso->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            $egresov = $egresov->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            $egresom = $egresom->whereBetween('fecha_comprobante', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($fecha_hasta)) {
            $egreso = $egreso->where('fecha_comprobante', '<=', $fecha_hasta);
            $egresov = $egresov->where('fecha_comprobante', '<=', $fecha_hasta);
            $egresom = $egresom->where('fecha_comprobante', '<=', $fecha_hasta);
        }
        if (!is_null($proveedor)) {
            $egreso = $egreso->where('id_proveedor', $proveedor);
            $egresov = $egresov->where('id_proveedor', $proveedor);
            $egresom = $egresom->where('id_proveedor', $proveedor);
        }

        if (!is_null($n_cheque) or $n_cheque != "") {
            $egreso = $egreso->where('no_cheque', "like", "%{$n_cheque}%");
            $egresov = $egresov->where('nro_cheque', "like", "%{$n_cheque}%");
            $egresom = $egresom->where('no_cheque', "like", "%{$n_cheque}%");
        }


        $egresoConsult          = $egreso->whereNotNull("id_caja_banco")->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'no_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'id_proveedor as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado")->orderBy('secuencia', 'DESC')->get();
        $egresoVariosConsult    = $egresov->whereNotNull("id_caja_banco")->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'nro_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'beneficiario as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado")->orderBy('secuencia', 'DESC')->get();;
        $egresoMasivoConsult    = $egresom->whereNotNull("id_caja_banco")->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'no_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'beneficiario as id_proveedor', 'id_caja_banco as banco', 'valor_pago as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado")->orderBy('secuencia', 'DESC')->get();;

        //dd($egreso->get());
        $egresov = $egresov->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'nro_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'beneficiario as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado");
        $egresom = $egresom->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'no_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'beneficiario as id_proveedor', 'id_caja_banco as banco', 'valor_pago as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado");
        $egreso = $egreso->select('fecha_comprobante as fecha_comprobante', 'fecha_cheque as fecha_cheque', 'no_cheque as cheque', 'id_asiento_cabecera as id_asiento_cabecera', 'id_proveedor as id_proveedor', 'id_caja_banco as banco', 'valor as valor', 'estado as estado_egreso', 'secuencia as secuencia_egreso', 'check as check', 'id_pago as id_pago', "fecha_entregado as fecha_entregado")->union($egresov)->union($egresom)->orderBy('fecha_comprobante', 'DESC')->get();
        $cheques = $egreso;
        //dd($cheques[0]);

        return ["cheques" => $cheques, "egresoConsult" => $egresoConsult, "egresoVariosConsult" => $egresoVariosConsult, "egresoMasivoConsult" => $egresoMasivoConsult];
    }

    public function excel_cheques_emitidos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        $tipo = $request['tipo'];
        $n_cheque = $request['n_cheque'];

        $cheques = InformesAcreedorController::chequesEmitidos($fecha_desde, $fecha_hasta, $id_empresa, $proveedor, $tipo, $n_cheque);

        Excel::create('Reporte de cheques emitidos', function ($excel) use ($cheques, $empresa, $fecha_desde, $fecha_hasta) {
            $excel->sheet('Cheque Emitidos', function ($sheet) use ($cheques, $empresa, $fecha_desde, $fecha_hasta) {


                $comienzo = 1;
                $sheet->mergeCells('A' . $comienzo . ':' . 'J' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($fecha_desde, $fecha_hasta) {
                    $cell->setValue("REPORTE DE CHEQUES EMITIDOS DESDE ({$fecha_desde} - {$fecha_hasta})");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#2A79C6');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo++;
                // dd($cheques);
                $cabecera = ["Fecha Comprobante", "Fecha Cheque", "Secuencia", "Nro. Asiento", "Nro_Cheque", "Nombre Proveedor", "Banco", "Fecha Entregado",  "Valor", "Acción"];

                if (count($cheques["egresoConsult"]) > 0) {
                    $sheet->mergeCells('A' . $comienzo . ':' . 'J' . $comienzo);
                    $sheet->cell('A' . $comienzo, function ($cell) use ($fecha_desde, $fecha_hasta) {
                        $cell->setValue("COMPROBANTE DE EGRESOS");
                        $cell->setFontColor('#FCFCFC');
                        $cell->setBackground('#2A79C6');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('13');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;
                    $cabecera = $cabecera;
                    $titulo['background-color'] = "#000000";
                    $titulo['color'] = "#FCFCFC";
                    $titulo["data"] = $cabecera;
                    $titulo["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $titulo);
                    $comienzo++;

                    foreach ($cheques["egresoConsult"] as $value) {
                        $banco  = Ct_Caja_Banco::find($value->banco);
                        if ($banco->clase == '1') {
                            $pro    = Proveedor::find($value->id_proveedor);

                            $egreso["background-color"] = "#FFFFFF";

                            if ($value->estado_egreso != 1) {
                                $egreso["background-color"] = "#FEB2B2";
                            }
                            $egreso["comienzo"] = $comienzo;
                            $egreso["data"] = [
                                date('d/m/Y', strtotime($value->fecha_comprobante)),
                                date('d/m/Y', strtotime($value->fecha_cheque)),
                                $value->secuencia_egreso,
                                $value->id_asiento_cabecera,
                                $value->cheque,
                                !is_null($pro) ? $pro->razonsocial : $value->id_proveedor,
                                !is_null($banco) ? $banco->nombre : "",
                                $value->fecha_entregado,
                                number_format($value->valor, 2, '.', ''),
                                $value->estado_egreso == 1 ? "Activo" : "Anulado"
                            ];
                            excelCreate::details($sheet, $egreso);

                            $comienzo++;
                        }
                    }
                }

                $comienzo++;

                if (count($cheques["egresoMasivoConsult"]) > 0) {
                    $sheet->mergeCells('A' . $comienzo . ':' . 'J' . $comienzo);
                    $sheet->cell('A' . $comienzo, function ($cell) use ($fecha_desde, $fecha_hasta) {
                        $cell->setValue("COMPROBANTE DE EGRESOS MASIVOS");
                        $cell->setFontColor('#FCFCFC');
                        $cell->setBackground('#2A79C6');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('13');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;
                    $cabecera = $cabecera;
                    $titulo['background-color'] = "#000000";
                    $titulo['color'] = "#FCFCFC";
                    $titulo["data"] = $cabecera;
                    $titulo["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $titulo);
                    $comienzo++;

                    foreach ($cheques["egresoMasivoConsult"] as $value) {
                        $banco  = Ct_Caja_Banco::find($value->banco);
                        if ($banco->clase == '1') {
                            $pro    = Proveedor::find($value->id_proveedor);

                            $egreso["background-color"] = "#FFFFFF";

                            if ($value->estado_egreso != 1) {
                                $egreso["background-color"] = "#FEB2B2";
                            }
                            $egreso["comienzo"] = $comienzo;
                            $egreso["data"] = [
                                date('d/m/Y', strtotime($value->fecha_comprobante)),
                                date('d/m/Y', strtotime($value->fecha_cheque)),
                                $value->secuencia_egreso,
                                $value->id_asiento_cabecera,
                                $value->cheque,
                                !is_null($pro) ? $pro->razonsocial : $value->id_proveedor,
                                !is_null($banco) ? $banco->nombre : "",
                                $value->fecha_entregado,
                                number_format($value->valor, 2, '.', ''),
                                $value->estado_egreso == 1 ? "Activo" : "Anulado"
                            ];
                            excelCreate::details($sheet, $egreso);

                            $comienzo++;
                        }
                    }
                }

                $comienzo++;

                if (count($cheques["egresoVariosConsult"]) > 0) {
                    $sheet->mergeCells('A' . $comienzo . ':' . 'J' . $comienzo);
                    $sheet->cell('A' . $comienzo, function ($cell) use ($fecha_desde, $fecha_hasta) {
                        $cell->setValue("COMPROBANTE DE EGRESOS VARIOS");
                        $cell->setFontColor('#FCFCFC');
                        $cell->setBackground('#2A79C6');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('13');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;
                    $cabecera = $cabecera;
                    $titulo['background-color'] = "#000000";
                    $titulo['color'] = "#FCFCFC";
                    $titulo["data"] = $cabecera;
                    $titulo["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $titulo);
                    $comienzo++;

                    foreach ($cheques["egresoVariosConsult"] as $value) {
                        $banco  = Ct_Caja_Banco::find($value->banco);
                        if ($banco->clase == '1') {
                            $pro    = Proveedor::find($value->id_proveedor);

                            $egreso["background-color"] = "#FFFFFF";

                            if ($value->estado_egreso != 1) {
                                $egreso["background-color"] = "#FEB2B2";
                            }
                            $egreso["comienzo"] = $comienzo;
                            $egreso["data"] = [
                                date('d/m/Y', strtotime($value->fecha_comprobante)),
                                date('d/m/Y', strtotime($value->fecha_cheque)),
                                $value->secuencia_egreso,
                                $value->id_asiento_cabecera,
                                $value->cheque,
                                !is_null($pro) ? $pro->razonsocial : $value->id_proveedor,
                                !is_null($banco) ? $banco->nombre : "",
                                $value->fecha_entregado,
                                number_format($value->valor, 2, '.', ''),
                                $value->estado_egreso == 1 ? "Activo" : "Anulado"
                            ];
                            excelCreate::details($sheet, $egreso);

                            $comienzo++;
                        }
                    }
                }
            });
        })->export('xlsx');
    }

    public function updateCheck(Request $request)
    {
        // dd($request->all());
        $id_usuario = Auth::user()->id;
        $id_asiento = $request["id_asiento"];
        $check = $request["check"];
        $egreso = Ct_Comprobante_Egreso::where('id_asiento_cabecera', $id_asiento)->first();
        $egresov = Ct_Comprobante_Egreso_Varios::where('id_asiento_cabecera', $id_asiento)->first();
        $egresom = Ct_Comprobante_Egreso_Masivo::where('id_asiento_cabecera', $id_asiento)->first();


        $fecha = date('Y-m-d');
        DB::beginTransaction();
        try {
            if (!is_null($egreso)) {
                $egreso->check = $check;
                $egreso->fecha_entregado = $fecha;
                $egreso->id_usuariomod = $id_usuario;
                $egreso->ip_modificacion = $_SERVER["REMOTE_ADDR"];
                $egreso->save();
            }


            if (!is_null($egresov)) {
                $egresov->check = $check;
                $egresov->fecha_entregado = $fecha;
                $egresov->id_usuariomod = $id_usuario;
                $egresov->ip_modificacion = $_SERVER["REMOTE_ADDR"];
                $egresov->save();
            }


            if (!is_null($egresom)) {
                $egresom->check = $check;
                $egresom->fecha_entregado = $fecha;
                $egresom->id_usuariomod = $id_usuario;
                $egresom->ip_modificacion = $_SERVER["REMOTE_ADDR"];
                $egresom->save();
            }
            DB::commit();
            return ["status" => "success", "msj" => "Guardado cone exito", "fecha" => date("d/m/Y", strtotime($fecha))];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }

    public function reporte_edad_cartera(Request $request)
    {  
        $fecha_desde = $request->d;
        $fecha_hasta = $request->h; 
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $informe = BdReportesFinancieros::search_edad_cartera($request);
        return view('contable/edad_cartera/index',["empresa" => $empresa, "informe" => $informe, "fecha_desde" => $fecha_desde, "fecha_hasta" => $fecha_hasta]); 
    }
}
