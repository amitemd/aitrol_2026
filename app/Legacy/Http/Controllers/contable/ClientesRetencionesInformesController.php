<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Compras;
use Sis_medico\Ct_factura_contable;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Empresa;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\Retenciones;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Cliente_Retencion;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Clientes;
use PHPExcel_Worksheet_Drawing;
use laravel\laravel;
use Sis_medico\User;

class ClientesRetencionesInformesController extends Controller
{
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

    public function deudasPendientes(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $observacion = $request['concepto'];
        $variable = 1;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $id_cliente = $request['id_cliente'];
        if ($observacion == null) {
            $observacion = $request['observacion2'];
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $deudas = '[]';
        // $deudas= $this->deudas_pendientes($proveedor,$fecha_desde,$fecha_hasta,$id_empresa,$variable,$tipo,$observacion,0); 
        $deudas = $this->deudas_pendientes($request);
        //dd($deudas->take(10));

        $variable2 = 0;
        $totales = 1;
        $totales2 = 0;

        // if($fecha_desde==null){
        //     $deudas= $this->deudas_pendientes($proveedor,$fecha_desde,$fecha_hasta,$id_empresa,$variable2,$tipo,$observacion,1); 
        // }

        return view('contable/clientes_retenciones/informes/deudas_pendientes/index', [
            'informe' => $deudas, 'empresa' => $empresa, 'observacion' => $observacion,
            'totales' => $totales, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'tipo' => $tipo, 'totales2' => $totales2
        ]);
    }


    public function clienteCarteraVencida(Request $request)
    {
        ini_set('max_execution_time', 300);
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $observacion = $request['concepto'];
        $variable = 1;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $id_cliente = $request['id_cliente'];
        if ($observacion == null) {
            $observacion = $request['observacion2'];
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $deudas = '[]';
        // $deudas= $this->deudas_pendientes($proveedor,$fecha_desde,$fecha_hasta,$id_empresa,$variable,$tipo,$observacion,0); 
        $deudas = $this->deudas_pendientes($request);
        //dd($deudas->take(10));

        $variable2 = 0;
        $totales = 1;
        $totales2 = 0;
        return view('contable/clientes_retenciones/informes/cartera_vencida/index', [
            'informe' => $deudas, 'empresa' => $empresa, 'observacion' => $observacion,
            'totales' => $totales, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'tipo' => $tipo, 'totales2' => $totales2
        ]);
    }

    public function getcliente(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos = [];
        if ($request['search'] != null) {
            $productos = Ct_Clientes::where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.identificacion as id', 'ct_clientes.nombre as text')->get();
        }

        return response()->json($productos);
    }

    public function deudas_pendientes($data)
    {
        $data['id_empresa'] = Session::get('id_empresa');
        $deudas = array();
        /*         if (!isset($data['fecha_desde']) and !isset($data['fecha_desde'])) {
            $data['fecha_desde'] = "01-01-" . date('Y');
            $data['fecha_hasta'] = "31-12-" . date('Y');
        } else {
            $data['fecha_desde'] = date('Y-m-d', strtotime($data['fecha_desde']));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($data['fecha_hasta']));
        } */
        if ($data['fecha_desde'] == null && $data['fecha_hasta'] != null) {
            $data['fecha_desde'] = date('Y-m-d', strtotime($data['fecha_desde']));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($data['fecha_hasta']));

            $deudas = Ct_Clientes::where('estado', '!=', '0')->with(['facturas' => function ($query) use ($data) {
                $query->where('estado', '!=', 0)->where('fecha', '<=', $data['fecha_hasta'] . ' 23:59:59');
                if ($data['id_cliente'] != null) {
                    $query =  $query->where('id_cliente', $data['id_cliente']);
                }

                if ($data['observacion'] != null) {
                    $query =  $query->where('observacion', 'like', '%' . $data['observacion'] . '%');
                }
                $query = $query->where('id_empresa', Session::get('id_empresa'));
                $query = $query->where('valor_contable', '!=', 0);
            }])->get();
        } else {
            $data['fecha_desde'] = date('Y-m-d', strtotime($data['fecha_desde']));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($data['fecha_hasta']));

            $deudas = Ct_Clientes::where('estado', '!=', '0')->with(['facturas' => function ($query) use ($data) {
                $query->where('estado', '!=', 0)->wherebetween('fecha', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59']);
                if ($data['id_cliente'] != null) {
                    $query =  $query->where('id_cliente', $data['id_cliente']);
                }
                if ($data['observacion'] != null) {
                    $query =  $query->where('observacion', 'like', '%' . $data['observacion'] . '%');
                }
                $query = $query->where('id_empresa', Session::get('id_empresa'));
                $query = $query->where('valor_contable', '!=', 0);
            }])->get();
        }


        return $deudas;
    }

    public function deudasPendientesExcel(Request $request)
    {
        //dd($request->all());
        $id_empresa             = Session::get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();
        $data['fecha_desde']    = $request['filfecha_desde'];
        $data['fecha_hasta']    = $request['filfecha_hasta'];
        $data['id_cliente']     = $request['id_cliente2'];
        $data['observacion']    = $request['observacion'];
        $consulta               = $this->deudas_pendientes($data);

        Excel::create('InformeDeudasPendiente-' . $data['fecha_desde'] . '-al-' . $data['fecha_hasta'], function ($excel) use ($empresa, $consulta, $data) {
            $excel->sheet('Informe Deudas Pendiente', function ($sheet) use ($empresa, $consulta, $data) {
                $sheet->mergeCells('C1:N1');
                $sheet->cell('C1', function ($cell) use ($empresa) {
                    if (!is_null($empresa)) {
                        $cell->setValue($empresa->nombrecomercial . ':' . $empresa->id);
                    }
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:B1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(220);
                    $objDrawing->setWidth(120);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('C1:N1');
                $sheet->cell('C1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:N2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:N3');
                $sheet->cell('C3', function ($cell) use ($empresa) {
                    if (!is_null($empresa)) {
                        $cell->setValue('INFORME DEUDAS PENDIENTES');
                    }
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($data) {
                    // manipulate the cel
                    if ($data['fecha_desde'] != null) {
                        $cell->setValue("Desde " . date("d/m/Y", strtotime($data['fecha_desde'])) . " Hasta " . date("d/m/Y", strtotime($data['fecha_hasta'])));
                    } else {
                        $cell->setValue(" Al " . date("d/m/Y", strtotime($data['fecha_hasta'])));
                    }
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');

                $i = $this->setDeudasPendientes($consulta, $sheet, 6);

                //  CONFIGURACION FINAL 

                //$sheet->cells('A3:M3', function ($cells) {
                // manipulate the range of cells
                //$cells->setBackground('#D1D1D1');
                // $cells->setFontSize('10');
                // $cells->setFontWeight('bold');
                // $cells->setBorder('thin', 'thin', 'thin', 'thin');
                // $cells->setValignment('center');
                // });

                $sheet->cells('A5:N5', function ($cells) {
                    // manipulate the range of cells
                    // $cells->setBackground('#cdcdcd'); 
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


    public function setDeudasPendientes($consulta, $sheet, $i)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $totales2 = 0;
        $abonos = 0;
        $totalabonos = 0;
        foreach ($consulta as $value) {
            if ($value->facturas != "[]") {
                $sheet->mergeCells('A' . $i . ':' . 'N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->identificacion . '  |  ' . $value->nombre);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#e7e7e7');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENCIMIENTO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NÚMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F' . $i . ':' . 'J' . $i);
                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIV');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ABONO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'L' => '0.00',
                    'M' => '0.00',
                ));
                $i++;
                foreach ($value->facturas as $val) {
                    // if($val->saldo>0){
                    $totales += $val->valor_contable;
                    $totales2 += $val->total_final;
                    $valor += $val->total_final;
                    $resta += $val->valor_contable;
                    $abonos += ($val->total_final - $val->valor_contable);
                    $totalabonos += ($val->total_final - $val->valor_contable);
                    $sheet->cell('A' . $i, function ($cell) use ($val) {
                        // manipulate the cel
                        $cell->setValue(date("d/m/Y", strtotime($val->fecha)));
                        $cell->setFontWeight('bold');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $fechaActual = date('Y-m-d');
                    $currentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $fechaActual);
                    $shippingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $val->fecha);
                    $diferencia_en_dias = $currentDate->diffInDays($shippingDate);

                    $sheet->cell('B' . $i, function ($cell) use ($diferencia_en_dias) {
                        // manipulate the cel 
                        $cell->setValue($diferencia_en_dias);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });
                    //$sheet->mergeCells('G'.$i.':H'.$i);
                    $sheet->cell('C' . $i, function ($cell) use ($val) {
                        // manipulate the cel
                        $cell->setValue($val->tipo);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($val) {
                        // manipulate the cel 
                        $cell->setValue($val->nro_comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($val) {
                        // manipulate the cel 
                        $cell->setValue($val->cliente->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });
                    $sheet->mergeCells('F' . $i . ':' . 'J' . $i);
                    $sheet->cell('F' . $i, function ($cell) use ($val) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setValue("Fact #: " . $val->nro_comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($val) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setValue('$');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($val) {
                        // manipulate the cel
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
                    $sheet->cell('M' . $i, function ($cell) use ($val) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        if (($val->valor_contable) != null) {
                            $cell->setValue($val->total_final - $val->valor_contable);
                        } else {
                            $cell->setValue('0.00');
                        }
                        $cell->setFontWeight('bold');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($val) {
                        // manipulate the cel
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


                    // }                   
                }

                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue("TOTAL :");
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($totales2) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($totales2);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($abonos) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($abonos);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($totales) {
                    // manipulate the cel
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
                $abonos = 0;
                $i++;
            }
        }
        $i++;
        $sheet->cell('K' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setFontColor('#FFFFFF');
            $cell->setBackground('#D1D1D1');
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($valor) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($valor);
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($totalabonos) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totalabonos);
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($resta) {
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
        $totalabonos = 0;
        return $i;
    }

    public function saldocxc(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];

        $observacion = $request['concepto'];
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $nombre_cliente = "";
        $id_cliente = $request['id_cliente'];
        if ($id_cliente != null) {
            $id_cliente = $request['id_cliente'];
            $cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();
            $nombre_cliente = $cliente->nombre;
        }

        $saldos = '[]';
        if (isset($request['boton_buscar'])) {
            $saldos = $this->getSaldoscxc($request);
        }

        // $saldos = Ct_Clientes::join("ct_comprobante_ingreso as ci", "ct_clientes.identificacion", "ci.id_cliente")
        //         ->wherebetween('ci.fecha', [$request['fecha_desde'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])->where('ci.id_empresa',$request['id_empresa'])
        //         ->join("ct_ventas as v", "v.id_cliente", "ct_clientes.identificacion")
        //         ->where("ci.tipo", 2)
        //         ->groupBy('ct_clientes.identificacion')
        //         //->sum('ci.total_ingreso')
        //         ->get();

        // dd($saldos);

        // dd($saldos);
        // dd($saldos[18]->anticipos->sum('total_ingreso'));
        // dd($saldos[18]->facturas->sum('total_final'));
        return view('contable/clientes_retenciones/informes/saldos/index', [
            'informe' => $saldos, 'empresa' => $empresa,
            'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'nombre_cliente' => $nombre_cliente
        ]);
    }

    public function __getSaldoscxc($data)
    {
        $data['id_empresa'] = Session::get('id_empresa');
        //dd($data);
        $saldos = array();
        if (!is_null($data['fecha_desde'])) {
            $saldos = Ct_ventas::where('estado', '!=', 0)
                // ->with(['anticipos' => function($query) use ($data){
                //     $query->wherebetween('fecha', [$data['fecha_desde'].' 00:00:00', $data['fecha_hasta'].' 23:59:59'])
                //     ->select(DB::raw("SUM(total_ingreso) as anticipo"))
                //     ->where('estado',2);
                // }])
                ->where('saldo', '!=', 0)
                ->select('id_cliente', DB::raw("SUM(total_final) as total"), DB::raw("SUM(saldo) as saldo"))
                ->groupBy('id_cliente')
                ->wherebetween('fecha', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59']);
        } else {

            $saldos = Ct_ventas::where('estado', '!=', 0)
                // ->with(['anticipos' => function($query) use ($data){
                //     $query->wherebetween('fecha', [$data['fecha_desde'].' 00:00:00', $data['fecha_hasta'].' 23:59:59'])
                //     ->select(DB::raw("SUM(total_ingreso) as anticipo"))
                //     ->where('estado',2);
                // }])
                ->where('saldo', '!=', 0)
                ->select('id_cliente', DB::raw("SUM(total_final) as total"), DB::raw("SUM(saldo) as saldo"))
                ->groupBy('id_cliente')
                ->where('fecha', '<=', $data['fecha_hasta']);
        }

        if ($data['id_cliente'] != null) {
            $saldos =  $saldos->where('id_cliente', $data['id_cliente']);
        }
        $saldos = $saldos->get();

        return $saldos;
    }

    public function getSaldoscxc($data)
    {
        $data['id_empresa'] = Session::get('id_empresa');
        $saldos = array();
        if (!is_null($data['fecha_desde'])) {
            $data['fecha_desde']    = str_replace('/', '-', $data['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($data['fecha_desde'])->timestamp;
            $data['fecha_desde']    = date('Y-m-d', $timestamp);
        }
        $data['fecha_hasta']    = str_replace('/', '-', $data['fecha_hasta']);
        $timestamp              = \Carbon\Carbon::parse($data['fecha_hasta'])->timestamp;
        $data['fecha_hasta']    = date('Y-m-d', $timestamp);
        if (!is_null($data['fecha_desde'])) {
            //dd($data['fecha_hasta']);
            $saldos = Ct_Clientes::where('estado', '!=', 0)
                ->with(['anticipos' => function ($query) use ($data) {
                    $query->wherebetween('fecha', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59'])->where('id_empresa', $data['id_empresa']);
                    // $query = $query->select(DB::raw("SUM(total_ingreso) as anticipo"));
                    if ($data['id_cliente'] != null) {
                        $query =  $query->where('id_cliente', $data['id_cliente']);
                    }
                    $query = $query->where('tipo', 2);
                }])
                ->with(['facturas' => function ($query) use ($data) {
                    $query->wherebetween('fecha', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59'])->where('id_empresa', $data['id_empresa']);
                    if ($data['id_cliente'] != null) {
                        $query =  $query->where('id_cliente', $data['id_cliente']);
                    }
                    // $query = $query->select(DB::raw("SUM(total_final) as total"))
                    $query = $query->where('saldo', '!=', 0)
                        ->where('estado', '!=', 0);
                }])
                ->get();
        } else {
            $saldos = Ct_Clientes::where('estado', '!=', 0)
                ->with(['anticipos' => function ($query) use ($data) {
                    $query->where('fecha', '<=', $data['fecha_hasta'])->where('id_empresa', $data['id_empresa']);
                    // $query = $query->select(DB::raw("SUM(total_ingreso) as anticipo"));
                    if ($data['id_cliente'] != null) {
                        $query =  $query->where('id_cliente', $data['id_cliente']);
                    }
                    $query = $query->where('tipo', 2);
                }])
                ->with(['facturas' => function ($query) use ($data) {
                    $query->where('fecha', '<=', $data['fecha_hasta'])->where('id_empresa', $data['id_empresa']);
                    if ($data['id_cliente'] != null) {
                        $query =  $query->where('id_cliente', $data['id_cliente']);
                    }
                    // $query = $query->select(DB::raw("SUM(total_final) as total"))
                    $query = $query->where('saldo', '!=', 0)
                        ->where('estado', '!=', 0);
                }])
                ->get();
        }


        return $saldos;
    }


    public function saldosExcel(Request $request)
    {
        $id_empresa             = Session::get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();
        $data['fecha_desde']    = $request['filfecha_desde'];
        $data['fecha_hasta']    = $request['filfecha_hasta'];
        $data['id_cliente']     = $request['id_cliente2'];
        //dd($data);
        $consulta               = $this->getSaldoscxc($data);

        Excel::create('SaldosCuentasPorCobrarClientes-' . $data['fecha_desde'] . '-al-' . $data['fecha_hasta'], function ($excel) use ($empresa, $consulta, $data) {
            $excel->sheet('Saldos Clientes', function ($sheet) use ($empresa, $consulta, $data) {
                $sheet->mergeCells('A1:E1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:E2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:E3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("SALDOS CUENTAS POR COBRAR CLIENTES ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A1:E1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:E2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:E3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("SALDOS CUENTAS POR COBRAR CLIENTES");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:E4');
                $sheet->cell('A4', function ($cell) use ($data) {
                    // manipulate the cel
                    if ($data['fecha_desde'] != null) {
                        $cell->setValue("Desde " . date("d/m/Y", strtotime($data['fecha_desde'])) . " Hasta " . date("d/m/Y", strtotime($data['fecha_hasta'])));
                    } else {
                        $cell->setValue(" Al " . date("d/m/Y", strtotime($data['fecha_hasta'])));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');

                $i = $this->setDetalleSaldos($consulta, $sheet, 6);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:E3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:E5', function ($cells) {
                    // manipulate the range of cells
                    // $cells->setBackground('#cdcdcd'); 
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

    public function setDetalleSaldos($consulta, $sheet, $i)
    {
        $x = 0;
        $valor = 0;
        $resta = 0;
        $totales = 0;
        $totales2 = 0;


        // DETALLES
        $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('IDENTIFICACION');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('B' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('CLIENTE');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('C' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('DEUDAS');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('D' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('ANTICIPOS');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('E' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('SALDO');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->setColumnFormat(array(
            'C' => '0.00',
            'D' => '0.00',
            'E' => '0.00',
        ));
        $i++;
        $acumdeudas = 0;
        $acumantic = 0;
        $acumsaldo = 0;
        foreach ($consulta as $val) {
            $acumdeudas += $val->facturas->sum('total_final');
            $acumantic += $val->facturas->sum('total_ingreso');
            $saldo = ($val->facturas->sum('total_final') - $val->anticipos->sum('total_ingreso'));
            $acumsaldo += $saldo;
            if ($val->facturas->sum('total_final') > 0 || $val->facturas->sum('total_ingreso') > 0) {
                // if($val->saldo>0){
                $sheet->cell('A' . $i, function ($cell) use ($val) {
                    // manipulate the cel
                    $cell->setValue($val->identificacion);
                    $cell->setFontWeight('bold');

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($val) {
                    // manipulate the cel 
                    $cell->setValue($val->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);

                $sheet->cell('C' . $i, function ($cell) use ($val) {
                    // manipulate the cel
                    $cell->setValue($val->facturas->sum('total_final'));
                    // $cell->setValignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) use ($val) {
                    // manipulate the cel 
                    $cell->setValue($val->anticipos->sum('total_ingreso'));
                    // $cell->setValignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) use ($saldo) {
                    // manipulate the cel 
                    $cell->setValue($saldo);
                    // $cell->setValignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            }



            // }                   
        }

        $sheet->mergeCells('A' . $i . ':B' . $i);
        $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue("TOTAL :");
            $cell->setFontColor('#FFFFFF');
            $cell->setBackground('#D1D1D1');
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('C' . $i, function ($cell) use ($acumdeudas) {
            // manipulate the cel
            $cell->setValue($acumdeudas);
            $cell->setFontWeight('bold');
            $cell->setValignment('right');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('D' . $i, function ($cell) use ($acumantic) {
            // manipulate the cel
            $cell->setValue($acumantic);
            $cell->setFontWeight('bold');
            $cell->setValignment('right');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('E' . $i, function ($cell) use ($acumsaldo) {
            // manipulate the cel
            $cell->setValue($acumsaldo);
            $cell->setFontWeight('bold');
            $cell->setValignment('right');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $totales = 0;
        $totales2 = 0;
        $i++;


        return $i;
    }


    public function informeRetenciones(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $acumfuente  = 0;
        $acumiva  = 0;
        $acumsaldo  = 0;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        $observacion = $request['concepto'];
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $nombre_cliente = "";
        $id_cliente = $request['id_cliente'] != null ? $request['id_cliente'] : null;
        if ($id_cliente != null) {
            $id_cliente = $request['id_cliente'];
            $cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();
            $nombre_cliente = $cliente->nombre;
        }


        $saldos = '[]';
        if (isset($request['boton_buscar'])) {

            $saldos = $this->factura_retenciones($request);
        }
        $clientes = Ct_Clientes::where('estado', '<>', '0')->get();

        return view('contable/clientes_retenciones/informes/retenciones/index', [
            'informe' => $saldos, 'empresa' => $empresa,
            'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'clientes' => $clientes, 'nombre_cliente' => $nombre_cliente,
            'acumfuente' => $acumfuente, 'acumiva' => $acumiva, 'acumsaldo' => $acumsaldo
        ]);
    }


    public function factura_retenciones($data)
    {


        $fecha_desde = $data['fecha_desde'];
        $fecha_hasta = $data['fecha_hasta'];
        $id_cliente = $data['id_cliente'];

        $empresa = Session::get('id_empresa');
        $buscador = Ct_Cliente_Retencion::join('ct_ventas', 'ct_cliente_retencion.id_factura', 'ct_ventas.id')
            ->join("ct_clientes", "ct_cliente_retencion.id_cliente", "ct_clientes.identificacion")
            ->where('ct_cliente_retencion.id_empresa', $empresa)
            ->where('ct_cliente_retencion.estado','!=',0);



        if (!empty($fecha_desde)) {
            $buscador = $buscador->where('ct_cliente_retencion.fecha', '>=', $fecha_desde);
        }

        if (!empty($fecha_hasta)) {
            $buscador = $buscador->where('ct_cliente_retencion.fecha', '<=', $fecha_hasta);
        }


        if (!empty($id_cliente)) {
            $buscador = $buscador->where('ct_clientes.identificacion', $id_cliente);
        }

        $deudas = $buscador->orderBy('ct_cliente_retencion.fecha', 'ASC')->get([
            'ct_cliente_retencion.id',
            'ct_cliente_retencion.valor_fuente',
            'ct_cliente_retencion.valor_iva',
            'ct_cliente_retencion.nro_comprobante',
            'ct_clientes.nombre as nombre_cliente',
            'ct_cliente_retencion.descripcion',
            'ct_cliente_retencion.estado',
            'ct_cliente_retencion.id_usuariocrea',
            'ct_cliente_retencion.id_cliente',
            'ct_cliente_retencion.created_at',
            'ct_cliente_retencion.id_empresa',
            'ct_cliente_retencion.secuencia',
            'ct_cliente_retencion.id_factura',
        ]);
        return $deudas;
    }



    /*    public function factura_retenciones($data)
    {
        $data['id_empresa'] = Session::get('id_empresa');
        $deudas = array();
        if (!isset($data['fecha_desde'])) {
            $data['fecha_desde'] = "01-01-" . date('Y');
        }
        if (!isset($data['fecha_hasta'])) {
            $data['fecha_hasta'] = "31-12-" . date('Y');
        }
        // return $deudas = Ct_ventas::where('estado','!=','0')->get();

        $deudas = Ct_ventas::where('estado', '!=', '0');
        if ($data['id_cliente'] != null) {
            $deudas =  $deudas->where('id_cliente', $data['id_cliente']);
        }
        // if($data['observacion'] != null){ $deudas =  $deudas->where('observacion','like','%'.$data['observacion'].'%'); }
        $deudas = $deudas->with(['retenciones' => function ($query) use ($data) {
            $query->where('estado', '!=', 0);
            if ($data['fecha_desde'] != null) {
                $query =  $query->where('fecha', '>=', $data['fecha_desde']);
            }
            if ($data['fecha_hasta'] != null) {
                $query =  $query->where('fecha', '<=', $data['fecha_hasta']);
            }
            $query= $query->where('id_empresa',$data['id_empresa'])->orderBy('fecha','ASC');
        }])->get();

        return $deudas;
    } */


    public function retencionesExcel(Request $request)
    {

        $id_empresa             = Session::get('id_empresa');
        $empresa                = Empresa::where('id', $id_empresa)->first();
        $data['fecha_desde']    = $request['fecha_desde'];
        $data['fecha_hasta']    = $request['fecha_hasta'];
        $data['id_cliente']     = $request['id_cliente2'];
        $consulta = $this->factura_retenciones($data);

        Excel::create('RetencionesClientes-' . $data['fecha_desde'] . '-al-' . $data['fecha_hasta'], function ($excel) use ($empresa, $consulta, $data) {
            $excel->sheet('Retenciones Clientes', function ($sheet) use ($empresa, $consulta, $data) {
                $sheet->mergeCells('A1:O1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:O2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:O3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME RETENCIONES");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:O4');
                $sheet->cell('A4', function ($cell) use ($data) {
                    // manipulate the cel
                    if ($data['fecha_desde'] != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($data['fecha_desde'])) . " Hasta " . date("d-m-Y", strtotime($data['fecha_hasta'])));
                    } else {
                        $cell->setValue("Al  " . date("d-m-Y", strtotime($data['fecha_hasta'])));
                    }

                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SECUENCIA DE FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO COMPROBANTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D5:F5');
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PORCENTAJE RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PORCENTAJE RFIR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL RFIVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'J' => '0.00',
                    'L' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',
                    'G' => '',
                ));

                $i = $this->setDetallesRetenciones($consulta, $sheet, 6);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:O3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:O5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });


                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  25,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  25,
                    'I'     =>  25,
                    'J'     =>  25,
                    'K'     =>  25,
                    'L'     =>  15,
                    'M'     =>  25,
                    'N'     =>  25,
                    'O'     =>  25,
                    'P'     =>  25,
                ));
            });
        })->export('xlsx');
    }




    public function setDetallesRetenciones($consulta, $sheet, $i)
    {
        $x = 0;
        $valor1 = 0;
        $valor2 = 0;


        foreach ($consulta as $value) {


            if ((isset($value->valor_fuente)) or (isset($value->valor_iva))) {
                $valor1 += $value->valor_fuente;
                $valor2 += $value->valor_iva;
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(date("Y-m-d", strtotime($value->created_at)));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(!is_null($value->retencion) ? $value->retencion->nro_comprobante : '');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // manipulate the cel 
                    $cell->setValue($value->nro_comprobante);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D' . $i . ':F' . $i);
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nombre_cliente);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->id_cliente);

                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // $sheet->mergeCells('F' . $i . ':I' . $i);
                $sheet->cell('H' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->descripcion);
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $valorPorcentaje = '';
                foreach ($value->detalle_retencion as $detalle) {
                    if (isset($detalle->porcentajer)) {
                        if ($detalle->tipo == 'RENTA')
                            $valorPorcentaje .= $detalle->porcentajer->valor;
                    }
                }
            }
            $sheet->cell('I' . $i, function ($cell) use ($valorPorcentaje) {
                // manipulate the cel
                $cell->setValue($valorPorcentaje == '' ? '0%' : $valorPorcentaje . ' %');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('J' . $i, function ($cell) use ($value) {
                $cell->setValue($value->valor_fuente);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $porciva = "";
            foreach ($value->detalle_retencion as $item) {
                if (isset($item->porcentajer)) {
                    if ($item->tipo == 'IVA') {
                        $porciva .= $item->porcentajer->valor . '% ';
                    }
                }
            }
            $sheet->cell('K' . $i, function ($cell) use ($porciva) {
                // manipulate the cel
                $cell->setValue($porciva == '' ? '0%' : $porciva);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('L' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->valor_iva);
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('M' . $i, function ($cell) use ($value) {
                // manipulate the cel
                if (($value->estado) != 0) {
                    $cell->setValue('ACTIVO');
                } else {
                    $cell->setValue('ANULADO');
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('N' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(isset($value->id_usuariocrea) ?  $value->usuariocrea->nombre1 . '  ' .  $value->usuariocrea->apellido1 : '');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('O' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->estado == 0 ?  $value->usuariomodif->nombre1  . '   ' .  $value->usuariomodif->apellido1 : '');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
            $x++;
        }


        $sheet->mergeCells('A' . $i . ':' . 'H' . $i);
        $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue("TOTAL");
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('', 'thin', 'thin', 'thin');
        });

        $sheet->cell('J' . $i, function ($cell) use ($valor1) {
            // manipulate the cel
            $cell->setValue($valor1);
            $cell->setValignment('center');
        });

        $sheet->cell('L' . $i, function ($cell) use ($valor2) {
            // manipulate the cel
            $cell->setValue($valor2);
            $cell->setValignment('center');
        });
    }


    public function autocompletar_usuario(Request $request)
    {

        $campo      = strtoupper($request['term']);
        $valid_tags = [];
        $clientes = Ct_Clientes::where('estado', '<>', '0')->where('nombre', 'like', '%' . $campo . '%')->get();
        foreach ($clientes as $id => $val) {
            $valid_tags[] = ['id' => $val->identificacion, 'nombre' => $val->nombre];
        }
        return response()->json($valid_tags);
    }

    public function clienteDeudasVencidas(Request $request)
    {
        ini_set('max_execution_time', 300);
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $observacion = $request['concepto'];
        $variable = 1;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $id_cliente = $request['id_cliente'];
        if ($observacion == null) {
            $observacion = $request['observacion2'];
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $deudas = '[]';
        // $deudas= $this->deudas_pendientes($proveedor,$fecha_desde,$fecha_hasta,$id_empresa,$variable,$tipo,$observacion,0); 
        $deudas = $this->deudas_vencidas($request);
        //dd($deudas->take(10));

        $variable2 = 0;
        $totales = 1;
        $totales2 = 0;
        return view('contable/clientes_retenciones/informes/deudas_vencidas/index', [
            'informe' => $deudas, 'empresa' => $empresa, 'observacion' => $observacion,
            'totales' => $totales, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'tipo' => $tipo, 'totales2' => $totales2
        ]);
    }

    public function deudas_vencidas($data)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        $idUser = Auth::user()->id;
        $data['id_empresa'] = Session::get('id_empresa');
        $deudas = array();
        if ($data['fecha_desde'] == null && $data['fecha_hasta'] != null) {
            $data['fecha_desde'] = date('Y-m-d', strtotime($data['fecha_desde']));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($data['fecha_hasta']));

            $deudas = Ct_Clientes::where('estado', '!=', '0')->with(['facturas' => function ($query) use ($data, $rolUsuario, $idUser) {
                $query->where('estado', '!=', 0)->where('fecha', '<=', $data['fecha_hasta'] . ' 23:59:59');
                if ($data['id_cliente'] != null) {
                    $query =  $query->where('id_cliente', $data['id_cliente']);
                }
                if ($data['observacion'] != null) {
                    $query =  $query->where('observacion', 'like', '%' . $data['observacion'] . '%');
                }



                if ($rolUsuario == 17) {
                    $query =  $query->where('ci_vendedor', $idUser);
                } else {
                    if ($data["vendedor"] != null) {
                        $query =  $query->where('ci_vendedor', $data['vendedor']);
                    }
                }

                $query = $query->where('id_empresa', Session::get('id_empresa'));
                $query = $query->where('valor_contable', '!=', 0);
            }])->get();
        } else {
            $data['fecha_desde'] = date('Y-m-d', strtotime($data['fecha_desde']));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($data['fecha_hasta']));

            $deudas = Ct_Clientes::where('estado', '!=', '0')->with(['facturas' => function ($query) use ($data, $rolUsuario, $idUser) {
                $query->where('estado', '!=', 0)->wherebetween('fecha', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59']);
                if ($data['id_cliente'] != null) {
                    $query =  $query->where('id_cliente', $data['id_cliente']);
                }
                if ($data['observacion'] != null) {
                    $query =  $query->where('observacion', 'like', '%' . $data['observacion'] . '%');
                }

                if ($rolUsuario == 17) {
                    $query =  $query->where('ci_vendedor', $idUser);
                } else {
                    if ($data["vendedor"] != null) {
                        $query =  $query->where('ci_vendedor', $data['vendedor']);
                    }
                }

                $query = $query->where('id_empresa', Session::get('id_empresa'));
                $query = $query->where('valor_contable', '!=', 0);
            }])->get();
        }
        return $deudas;
    }


    public function clienteDeudasVencidas_vendedor(Request $request)
    {
        ini_set('max_execution_time', 300);
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $observacion = $request['concepto'];
        $variable = 1;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $id_cliente = $request['id_cliente'];
        if ($observacion == null) {
            $observacion = $request['observacion2'];
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $deudas = '[]';
        // $deudas= $this->deudas_pendientes($proveedor,$fecha_desde,$fecha_hasta,$id_empresa,$variable,$tipo,$observacion,0); 
        $deudas = $this->deudas_vencidas($request);
        //dd($deudas->take(10));

        $variable2 = 0;
        $totales = 1;
        $totales2 = 0;
        return view('contable/clientes_retenciones/informes/deudas_vencidas/index_vendedor', [
            'informe' => $deudas, 'empresa' => $empresa, 'observacion' => $observacion,
            'totales' => $totales, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_cliente' => $id_cliente, 'tipo' => $tipo, 'totales2' => $totales2
        ]);
    }
    public function pagar_vendedor(Request $request)
    {


        $id_empresa = $request->session()->get('id_empresa');
        $vendedor = [];
        if ($request['search'] != null) {
            $vendedor = User::whereRaw("CONCAT(users.nombre1, ' ', users.apellido1) LIKE ?", ["%" . $request['search'] . "%"])
                ->join("ct_ventas", "ct_ventas.ci_vendedor", "users.id")
                ->join('tipousuario', 'users.id_tipo_usuario', 'tipousuario.id')
                ->groupBy('users.id')
                ->select("users.*")
                ->get();
        }
        $arreglo = [];
        foreach ($vendedor as $arr) {
            array_push($arreglo, ["id" => $arr["id"], "text" => $arr["nombre1"] . ' ' . $arr["nombre2"] . ' ' . $arr["apellido1"] . ' ' . $arr["apellido2"]]);
        }

        return response()->json($arreglo);
    }
}
