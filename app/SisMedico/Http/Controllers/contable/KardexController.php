<?php

namespace Sis_medico\Http\Controllers\contable;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_productos;
use Sis_medico\Empresa;
use Session;
use Excel;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_ventas;

class KardexController extends Controller
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

    public function kardex()
    {
        // $data['id'] = 61;
        // $data['tipo'] = '1';
        // $msj = Ct_Kardex::generar_kardex($data);
        // print_r($msj);

        // $data['id'] = 18;
        // $data['tipo'] = 'VEN-FA';
        // $msj = Ct_Kardex::generar_kardex($data);
        // dd($msj);

        $data['id'] = 50;
        $data['tipo'] = 'ING-INV';
        $msj = Ct_Kardex::generar_kardex($data);
        print_r($msj);
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $producto_id = 0;
        if (isset($request['producto_id'])) {
            $producto_id = $request['producto_id'];
        }
        // dd($fecha_desde);
        $productos = Ct_productos::orderby('id', 'desc')->where('id_empresa', $id_empresa)->get();
        return view('contable/kardex/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'producto_id' => $producto_id
        ]);
    }

    public function show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = Session::get('id_empresa');
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $kardex = '[]';
        $dateFin = "2020/12/31";
        $dateIni = "2020/12/31";
        $getAnterior = Ct_Kardex::where('producto_id', $request['producto_id'])
            ->where('id_empresa', $id_empresa)
            ->where('tipo', 'INVENTARIO')
            ->where('producto_id', $request['producto_id'])
            ->whereBetween('fecha', [$dateIni . " 00:00:00", $dateFin . " 23:59:59"])
            ->orderBy('fecha', 'ASC')
            ->orderBy('id', 'ASC')
            ->select(DB::raw('SUM(cantidad) as cantidad'), DB::raw('SUM(valor_unitario) as valor_unitario'), DB::raw('SUM(total) as total'), 'fecha')->first();
        $producto_id = "";
        if (isset($request['producto_id']) and $request['producto_id'] != "") {
            $detalles=DB::table('movimiento as m')
                        ->join('producto as p','m.id_producto','p.id')
                        ->join('pedido as pd','m.id_pedido','pd.id')
                        ->join('ct_productos_insumos as ctpi','m.id_producto','ctpi.id_insumo')
                        ->where('ctpi.id_producto',$request['producto_id'])
                        ->groupBy('m.id_producto')
                        ->select(DB::raw('count(*) as cantidad'),'p.nombre as descripcion','pd.fecha as fecha')->get();
            $kardex = Ct_Kardex::where('producto_id', $request['producto_id'])
                ->where('id_empresa', $id_empresa)
                ->where('tipo', 'NOT LIKE', '%ANU%')
                ->where('tipo', '<>', 'ING-INV')
                ->whereBetween('fecha', [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
            $producto_id = $request['producto_id'];
            // dd($fecha_hasta);
        }
        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $productos = Ct_productos::orderby('id', 'desc')->where('id_empresa',$id_empresa)->get();
        return view('contable/kardex/show', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => $getAnterior, 'fecha_hasta' => $fecha_hasta, 'kardex' => $kardex, 'producto_id' => $producto_id, 'detalles' => $detalles
        ]);
    }

    public function exportar(Request $request)
    {
        // dd("Exportar");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
            $rfecha_desde           = $request['filfecha_desde'];
            $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['filfecha_hasta'];
            $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $kardex = '[]';
        $producto = "[]";

        if (isset($request['filproducto_id']) and $request['filproducto_id'] != "") {
            $kardex = Ct_Kardex::where('producto_id', $request['filproducto_id'])
                ->where('tipo', 'NOT LIKE', '%ANU%')
                ->whereBetween('fecha', [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
            $producto = Ct_productos::where('id', $request['filproducto_id'])->first();

            // dd($fecha_desde);
            $id_empresa     = Session::get('id_empresa');
            $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $dateFin = "2020/12/31";
            $dateIni = "2020/12/31";
            $getAnterior = Ct_Kardex::where('producto_id', $request['filproducto_id'])
                ->where('id_empresa', $id_empresa)
                ->where('tipo', 'INVENTARIO')
                ->whereBetween('fecha', [$dateIni . " 00:00:00", $dateFin . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->select(DB::raw('SUM(cantidad) as cantidad'), DB::raw('SUM(valor_unitario) as valor_unitario'), DB::raw('SUM(total) as total'), 'fecha')->first();
            //dd($getAnterior);
            $periodo_desde  = $this->fechaTexto($fecha_desde);
            $periodo_hasta  = $this->fechaTexto($fecha_hasta);

            //  DOCUMENTACION
            // https://docs.laravel-excel.com/2.1/export/cells.html
            Excel::create('Kardex-' . $producto->nombre . ' ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $getAnterior, $periodo_desde, $periodo_hasta, $producto, $kardex) {
                $excel->sheet('Kardex', function ($sheet) use ($empresa, $periodo_desde, $periodo_hasta, $producto, $kardex, $getAnterior) {
                    // dd($participacion);
                    $sheet->mergeCells('A1:L1');
                    $sheet->cell('A1', function ($cell) use ($empresa) {
                        $cell->setValue($empresa->nombrecomercial);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });
                    $sheet->mergeCells('A2:L2');
                    $sheet->cell('A2', function ($cell) {
                        $cell->setValue("KARDEX");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:L3');
                    $sheet->cell('A3', function ($cell) use ($periodo_desde, $periodo_hasta) {
                        $cell->setValue("$periodo_desde al $periodo_hasta");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A4:F4');
                    $sheet->cell('A4', function ($cell) {
                        $cell->setValue("PRODUCTO");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G4:L4');
                    $sheet->cell('G4', function ($cell) {
                        $cell->setValue("MÉTODO");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A5:F5');
                    $sheet->cell('A5', function ($cell) use ($producto) {
                        $cell->setValue($producto->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G5:L5');
                    $sheet->cell('G5', function ($cell) {
                        $cell->setValue('');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A6:A7');
                    $sheet->cell('A6', function ($cell) {
                        $cell->setValue('FECHA');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('B6:C6');
                    $sheet->cell('B6', function ($cell) {
                        $cell->setValue('DETALLE');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D6:F6');
                    $sheet->cell('D6', function ($cell) {
                        $cell->setValue('ENTRADA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G6:I6');
                    $sheet->cell('G6', function ($cell) {
                        $cell->setValue('SALIDA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('J6:L6');
                    $sheet->cell('J6', function ($cell) {
                        $cell->setValue('SALDOS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('B7:C7');
                    $sheet->cell('B7', function ($cell) {
                        $cell->setValue('CONCEPTO');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //ENTRADA
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('D7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //SALDIDA
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('G7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //SALDO
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('J7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M7', function ($cell) {
                        $cell->setValue('OBSERVACION');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // DETALLES

                    $sheet->setColumnFormat(array(
                        'D' => '0.00',
                        'E' => '0.00',
                        'F' => '0.00',
                        'G' => '0.00',
                        'H' => '0.00',
                        'I' => '0.00',
                        'J' => '0.00',
                        'K' => '0.00',
                        'L' => '0.00',
                    ));

                    $i = $this->setDetalles($kardex, $sheet, 8, $getAnterior);
                    // $i = $this->setDetalles($pasivos, $sheet, $i, '',$participacion);
                    // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);


                    //  CONFIGURACION FINAL
                    $sheet->cells('A2:M2', function ($cells) {
                        // manipulate the range of cells
                        $cells->setBackground('#0070C0');
                        // $cells->setFontSize('10');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        $cells->setValignment('center');
                    });

                    $sheet->cells('A4:M4', function ($cells) {
                        // manipulate the range of cells
                        $cells->setBackground('#cdcdcd');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize('12');
                    });

                    $sheet->setWidth(array(
                        'A' => 12,
                        'B' => 20,
                        'C' => 12,
                        'D' => 12,
                        'E' => 12,
                        'F' => 12,
                        'G' => 12,
                        'H' => 12,
                        'I' => 12,
                        'J' => 12,
                        'K' => 12,
                        'L' => 12,
                    ));
                });
                $excel->getActiveSheet()->getColumnDimension("M")->setWidth(45)->setAutosize(false);
            })->export('xlsx');
        }
    }

    public function setDetalles($data, $sheet, $i, $getAnterior)
    {
        $sheet->cell('F' . $i, function ($cell) {
            $cell->setValue('SALDOS ANTERIORES:');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('G' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue(date('d/m/Y', strtotime($getAnterior->fecha)));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->cantidad);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->valor_unitario);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->total);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $getPrice = 0;
        $getPriceant = 0;
        $getCount = 0;
        $getTotal = 0;
        $cantidadant = 0;
        $anterior = $getAnterior->cantidad;
        if (is_null($anterior)) {
            $anterior = 0;
        }
        $cantidad = $anterior;
        $anteriorprecio = $getAnterior->valor_unitario;
        if (is_null($anteriorprecio)) {
            $anteriorprecio = 0;
        }
        $anteriortotal = $getAnterior->total;
        if (is_null($anteriortotal)) {
            $anteriortotal = 0;
        }
        //dd($anteriorprecio);
        $totalCosto = $anteriortotal;
        $precioCosto = $anteriorprecio;
        $contador = 0;
        $i++;
        foreach ($data as $value) {
            if ($value->movimiento == 1) {

                $cantidad += $value->cantidad;
            } else {

                $cantidad = $cantidad - $value->cantidad;
            }
            $getPrice += $value->valor_unitario;
            $getTotal += $value->total;
            $sheet->cell('A' . $i, function ($cell) use ($value) {
                $cell->setValue(date('d/m/Y', strtotime($value['fecha'])));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('B' . $i . ':C' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                $cell->setValue($value['tipo'] . " " . $value['numero']);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            if ($value->movimiento == 1) {
                $observ = DB::table('ct_compras')->find($value->id_movimiento);
                $totalCosto += $value->total;
                if ($cantidad > 0) {
                    $precioCosto = $totalCosto / $cantidad;
                } else {
                    $precioCosto = 0;
                }
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    $cell->setValue($value['cantidad']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    $cell->setValue($value['valor_unitario']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    $cell->setValue($value['total']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            } else {
                $observ=Ct_ventas::find($value->id_movimiento);
                $totalCosto = $precioCosto * $cantidad;
                $tots = $precioCosto * $value->cantidad;
                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($cantidad) {
                    $cell->setValue($cantidad);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H' . $i, function ($cell) use ($precioCosto) {
                    $cell->setValue($precioCosto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I' . $i, function ($cell) use ($tots) {
                    $cell->setValue($tots);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            }

            $sheet->cell('J' . $i, function ($cell) use ($cantidad) {
                $cell->setValue($cantidad);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('K' . $i, function ($cell) use ($precioCosto) {
                $cell->setValue($precioCosto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('L' . $i, function ($cell) use ($totalCosto) {
                $cell->setValue($totalCosto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('M' . $i, function ($cell) use ($observ, $value) {
                if ($observ != null) {
                    if ($value->movimiento == 1) {
                        $cell->setValue($observ->observacion);
                    } else {
                        $observs = Ct_ventas::find($value->id_movimiento);
                        $cell->setValue($observs->nombres_paciente . ' P: ' . $observs->procedimientos);
                    }
                }

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
            $observ = "";
            //$cantidad=$value->cantidad;
            if ($value->movimiento == 1) {

                $cantidadant += $value->cantidad;
            } else {

                $cantidadant = $cantidad - $value->cantidad;
            }
            $getTotalant = 0;
            $getPriceant += $value->valor_unitario;
            $getTotalant += $value->total;
            $contador++;
            //dd($getTotal);
        }
        // $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        // $sheet->cells('F5:F' . $i, function ($cells) {
        //     $cells->setAlignment('right');
        // });
        // $sheet->cells('G5:G' . $i, function ($cells) {
        //     $cells->setAlignment('right');
        // });
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
        // return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
        return $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
}
