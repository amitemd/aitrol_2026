<?php

namespace Sis_medico\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Procedimiento;
use Sis_medico\Ct_Importaciones_Det;
use Sis_medico\Ct_Importaciones_Cab;
use Sis_medico\Ct_Imp_Gastos;
use Sis_medico\Ct_Importaciones_Gasto_Cab;
use Sis_medico\Ct_Importaciones_Detalle_Compra;
use Sis_medico\Ct_Importaciones_Compras;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\contable\ImportacionesController as recalcula;
use Sis_medico\LogImportaciones;
use Response;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_master_tipos;
class ImportacionesController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //hol main principal
        $proveedor = Proveedor::all();
        $otros_gastos = recalcula::recalcularImportaciones($id);
        $termino       = Ct_Termino::where('estado', '1')->get();
        $c_tributario  = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = Ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        //dd($otros_gastos);

        //dd($otros_gastos);

        $logImportaciones = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first();

        if (is_null($logImportaciones) or $otros_gastos["status"]== "error") {
            return response()->view('errors.404');
        }


        $compras = Ct_Importaciones_Compras::find($logImportaciones->id_compra);
      //  dd($compras->detalles);

        $detalle_gasto = Ct_Imp_Gastos::where('estado', 1)->get();

        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }

        $gastos = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->orderBy('tipo', 'ASC')->get();

        $sum_egre_info = 0;
        $sum_egre_costo = 0;
        $ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->get();

        foreach ($ct_comprobante_egreso_varios as $value) {
            foreach ($value->detalles as $det) {

                if ($det->tipo_liq == 1) {
                    $sum_egre_info += $det->debe;
                } elseif ($det->tipo_liq == 2) {
                    $sum_egre_costo += $det->debe;
                }
            }
        }

        $log_asiento = LogImportaciones::where('id_import_cab', $id)->where('principal', 1)->first();
        $log_compra = null;
        if(!is_null($log_asiento)){
            $log_compra = Ct_compras::find($log_asiento->id_compra);
        }
        
       // dd($log_compra);

        return view('importaciones.index', ['otros_gastos' => $otros_gastos, 'compra_cabecera' => $compras, 'sum_egre_info' => $sum_egre_info, 'sum_egre_costo' => $sum_egre_costo, 'ct_comprobante_egreso_varios' => $ct_comprobante_egreso_varios, 'imp' => $ct_importaciones_cab, 'gastos' => $gastos, 'proveedor' => $proveedor, 'detalle_gasto' => $detalle_gasto, 'log_compra'=> $log_compra, 'termino' => $termino, 'c_tributario' => $c_tributario, 't_comprobante' => $t_comprobante]);
    }

    public function _excel($id)
    {
        $proveedor = Proveedor::all();
        $detalle_gasto = Ct_Imp_Gastos::where('estado', 1)->get();

        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }
        $gastos = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->orderBy('tipo', 'ASC')->get();
        //$ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->first(); 

        $sum_egre_info = 0;
        $sum_egre_costo = 0;
        $ct_comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->get();


        foreach ($ct_comprobante_egreso_varios as $value) {
            foreach ($value->detalles as $det) {
                if ($det->tipo_liq == 1) {
                    $sum_egre_info += $det->debe;
                } elseif ($det->tipo_liq == 2) {
                    $sum_egre_costo += $det->debe;
                }
            }
        }


        $subtitulos = array("TOTAL COMPRA", "DESCUENTO", "TOTAL", "GASTOS", "TOTAL", "FACTOR");
        $otros = array("Fecha", "Proveedor", "Cuenta", "Valor");
        $titulos = array("CODIGO", "NOMBRE", "DESCRIPCION", "CANTIDAD", "PESO KGs", "PRECIO SIN DESCNTO", "PRECIO CON DESCUENTO", "SUB TOTAL", "%", "COSTO ASIGNADO AL TOTAL", "COSTO ASIGNADO UNIDAD", "COSTO UNITARIO", "COSTO TOTAL");
        $tabli = array('Iva Liquidación Aduanera', 'Iva Facturas de Gastos', 'Total Iva');
        $tabliFinal = array('PROVEEDOR', 'FACTURA', 'DETALLE', 'SUBTOTAL', 'IVA', 'TOTAL');
        $otraTabla = array("ISD", "Gastos Financieros", "Flete Maritimo", "Seguro Transporte Internacional", "Trasporte Pais Origen", "Impuestos aduaneros locales", "Bodega/manejo documentos", "Otros Gastos", "Transporte Interno", "Gastos portuarios locales", "DHL Gastroclinica", "DHL", "TOTAL");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
        $posicion1 = array("H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
        $posicion3 = array("C", "D", "E");
        Excel::create('Modulo de Importaciones', function ($excel) use ($sum_egre_info, $ct_comprobante_egreso_varios, $gastos, $ct_importaciones_cab, $titulos, $posicion, $subtitulos, $otraTabla, $tabli, $tabliFinal, $posicion1, $otros, $posicion3) {
            $excel->sheet('Importaciones', function ($sheet) use ($sum_egre_info, $ct_comprobante_egreso_varios, $gastos, $ct_importaciones_cab, $titulos, $posicion, $subtitulos, $otraTabla, $tabli, $tabliFinal, $posicion1, $otros, $posicion3) {
                $comienzo = 1;
                $cabeceraDatos = $ct_importaciones_cab[0];
                $cabeceraTexto = ['CLIENTE', 'PROVEEDOR', 'FACTURA'];
                $cont_cabecera = 1;
                for ($i = 0; $i < count($cabeceraTexto); $i++) {
                    $sheet->cell('A' . $cont_cabecera, function ($cell) use ($cabeceraTexto, $i) {
                        $cell->setValue($cabeceraTexto[$i]);
                        $cell->setFontColor('#000000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('8');
                        $cell->setAlignment('center');
                    });
                    $cont_cabecera++;
                }
                $array = [$cabeceraDatos->cliente->nombrecomercial, $cabeceraDatos->proveedor_da->razonsocial, $cabeceraDatos->secuencia_factura];
                for ($i = 0; $i < count($array); $i++) {
                    $sheet->cell('B' . $comienzo, function ($cell) use ($array, $i) {
                        $cell->setValue($array[$i]);
                        $cell->setFontColor('#000000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('8');
                        $cell->setAlignment('center');
                    });
                    $comienzo++;
                }

                for ($i = 0; $i < count($titulos); $i++) {
                    //dd('' . $posicion[$i] . '' . $comienzo);
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setFontSize('9');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }


                $subTotalTotal = 0;
                foreach ($ct_importaciones_cab as $cab) {
                    foreach ($cab->detalles as $details) {
                    }
                    $subTotalTotal += $cab->subtotal;
                }
                $gastosTotales = 0;
                $contadorIva = 0;


                foreach ($gastos as $val) {
                    $gastosTotales += $val->compra->subtotal;
                    $contadorIva += $val->compra->iva_total;
                }
                //dd($sum_egre_info);
                $egresosVariosValor = $sum_egre_info;

                // if(!is_null($ct_comprobante_egreso_varios)){
                //     $egresosVariosValo = $ct_comprobante_egreso_varios->valor;
                // }

                $arrayIvaLiqui = [
                    $egresosVariosValor,
                    $contadorIva,
                    $contadorIva + $egresosVariosValor
                ];


                $comienzo++;
                //Calculos Generales
                //SUBTOTAL TOTAL
                $descuentoTotal = 0;
                //END
                $arrayTabla = array();
                $arrayUnic = array();
                $porcentaje = 0;
                foreach ($ct_importaciones_cab as $cab) {
                    $descuentoTotal += $cab->descuento;
                    $sumaTotalUni = 0;
                    $subTotalUni = 0;
                    $costoasignadoSuma = 0;
                    $porcentajeUnitario = 0;
                    $cantidad = 0;
                    $costototal = 0;
                    $descuento = 0;
                    foreach ($cab->detalles as $value) {
                        $subTotalUni = $value->cantidad * $value->precio_desc;
                        $sumaTotalUni += $subTotalUni;
                        $descuento += $value->porcentaje;
                        $cantidad += $value->cantidad;
                        $porcentajeNumerico = $value->subtotal / $subTotalTotal;
                        $porcentajeUnitario = round(((float)$value->subtotal / $subTotalTotal) * 100, 1);
                        $costoasignado = $gastosTotales * $porcentajeNumerico;
                        $porcentaje += $porcentajeUnitario;
                        $costoasignadoSuma += $costoasignado;
                        $costoUnitario = $costoasignado / $value->cantidad;
                        $costoUn = $costoUnitario + $value->precio_desc;
                        $costTotal = $costoUn * $value->cantidad;
                        $costototal += $value->cantidad * $costoUn;
                        $arrayTabla[] = [
                            $value->productos->codigo,
                            $value->productos->nombre,
                            $value->productos->descripcion,
                            $value->cantidad,
                            $value->peso,
                            $value->precio,
                            number_format($value->precio_desc, 2, ",", ""),
                            number_format($subTotalUni, 2, ",", ""),
                            $porcentajeUnitario . '%',
                            number_format($costoasignado, 2, ",", ""),
                            number_format($costoUnitario, 2, ",", ""),
                            number_format($costoUn, 2, ",", ""),
                            number_format($costTotal, 2, ",", "")
                        ];
                    }
                    $arrayUnic[] = [
                        '',
                        '',
                        'Total',
                        $cantidad,
                        '',
                        '',
                        '',
                        $sumaTotalUni,
                        $porcentaje,
                        $costoasignadoSuma,
                        '',
                        '',
                        $costototal,

                    ];
                }

                $sumaTotalUniSub = 0;
                $cantidadSub = 0;
                $costoasignadoSumaSub = 0;
                $costototalSub = 0;
                foreach ($arrayUnic as $unic) {
                    $cantidadSub += $unic[3];
                    $sumaTotalUniSub += $unic[7];
                    $costoasignadoSumaSub += $unic[9];
                    $costototalSub += $unic[12];
                }

                $arr[] = [
                    'Total',
                    '',
                    '',
                    $cantidadSub,
                    '',
                    '',
                    '',
                    number_format($sumaTotalUniSub, 2, ",", ""),
                    $porcentaje . '%',
                    number_format($costoasignadoSumaSub, 2, ",", ""),
                    '',
                    '',
                    number_format($costototalSub, 2, ",", ""),
                ];

                for ($i = 0; $i < count($arrayTabla); $i++) {
                    for ($j = 0; $j < count($arrayTabla[$i]); $j++) {
                        $sheet->cell('' . $posicion[$j] . '' . $comienzo, function ($cell) use ($arrayTabla, $i, $j) {
                            $cell->setValue($arrayTabla[$i][$j]);
                            $cell->setAlignment('left');
                            $cell->setFontSize('9');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }

                for ($i = 0; $i < count($arr); $i++) {
                    for ($j = 0; $j < count($arr[$i]); $j++) {
                        //dd($arr[$i][$j]);
                        $sheet->cell('' . $posicion[$j] . '' . $comienzo, function ($cell) use ($arr, $i, $j) {
                            $cell->setValue($arr[$i][$j]);
                            $cell->setAlignment('left');
                            $cell->setFontSize('9');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }

                $comienzo += 2;
                $total  = $subTotalTotal + $descuentoTotal;
                $totT = $total + $gastosTotales;
                $valoreTabla = [
                    number_format($subTotalTotal, 2, ",", ""),
                    number_format($descuentoTotal, 2, ",", ""),
                    number_format($total, 2, ",", ""),
                    number_format($gastosTotales, 2, ",", ""),
                    number_format($totT, 2, ",", ""),
                    ''
                ];

                $arrayAduanera = array('IVA LIQUIDACION ADUANERA', 'IVA FACTURA DE GASTOS', 'TOTAL IVA');
                for ($j = 0; $j < count($arrayAduanera); $j++) {
                    $sheet->cell('A' . $comienzo, function ($cell) use ($arrayAduanera, $j) {
                        $cell->setValue($arrayAduanera[$j]);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $comienzo, function ($cell) use ($arrayIvaLiqui, $j) {
                        $cell->setValue($arrayIvaLiqui[$j]);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $comienzo++;
                }



                $comienzo -= count($arrayAduanera);

                for ($i = 0; $i < count($subtitulos); $i++) {
                    $sheet->cell('D' . $comienzo, function ($cell) use ($subtitulos, $i) {
                        $cell->setValue($subtitulos[$i]);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $comienzo, function ($cell) use ($valoreTabla, $i) {
                        $cell->setValue($valoreTabla[$i]);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;
                }

                $comienzo += 2;
                $sheet->mergeCells('C' . $comienzo . ':' . 'E' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) {
                    $cell->setValue('CARGA CONSOLIDADA IMPORTACIÓN');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $arrayLiqui = array('FECHA', 'PRODUCTO', 'PRECIO');
                for ($i = 0; $i < count($arrayLiqui); $i++) {
                    $sheet->cell('' . $posicion3[$i] . '' . $comienzo, function ($cell) use ($arrayLiqui, $i) {
                        $cell->setValue($arrayLiqui[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setFontSize('9');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $sumaTabla = 0;
                $arrp = array();





                foreach ($ct_importaciones_cab as $val) {
                    foreach ($val->detalles as $value) {
                        $arrp[] = [
                            $val->fecha,
                            $value->productos->nombre,
                            $value->subtotal,
                        ];
                    }
                }

                foreach ($ct_comprobante_egreso_varios as $egre_vario) {
                    foreach ($egre_vario->detalles as $det_varios) {
                        if ($det_varios->tipo_liq == 2) {
                            $arrp[] = [
                                $egre_vario->fecha_comprobante,
                                $egre_vario->descripcion,
                                $det_varios->debe,
                            ];
                        }
                    }
                }
                foreach ($gastos as $val) {
                    $arrp[] = [
                        $val->compra->fecha,
                        $val->compra->proveedor_da->nombrecomercial,
                        $val->compra->subtotal
                    ];
                }
                for ($i = 0; $i < count($arrp); $i++) {
                    $sumaTabla += $arrp[$i][2];
                    for ($j = 0; $j < count($arrp[$i]); $j++) {
                        $sheet->cell('' . $posicion3[$j] . '' . $comienzo, function ($cell) use ($arrp, $i, $j) {
                            $cell->setValue($arrp[$i][$j]);
                            $cell->setAlignment('center');
                            $cell->setFontSize('9');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $comienzo++;
                }

                $sheet->mergeCells('C' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) {
                    $cell->setValue('Total');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('9');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('E' . $comienzo . ':' . 'E' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) use ($sumaTabla) {
                    $cell->setValue('$' . $sumaTabla);
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontSize('9');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public function excel($id)
    {
        $ct_importaciones_cab = Ct_Importaciones_Cab::find($id);
        $aux_ct_importaciones_cab = $ct_importaciones_cab;

        if ($ct_importaciones_cab->agrupada == 1) {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id_cab_imp', $id)->get();
        } else {
            $ct_importaciones_cab = Ct_Importaciones_Cab::where('id', $id)->get();
        }
        $subtitulos = array("TOTAL COMPRA", "DESCUENTO", "TOTAL", "GASTOS", "TOTAL", "FACTOR");
        $otros = array("Fecha", "Proveedor", "Cuenta", "Valor");
        $titulos = array("CODIGO", "NOMBRE", "DESCRIPCION", "CANTIDAD", "PESO KGs", "PRECIO", "SUB TOTAL", "%", "COSTO ASIGNADO AL TOTAL", "COSTO ASIGNADO UNIDAD", "COSTO UNITARIO", "COSTO TOTAL");
        $tabli = array('Iva Liquidación Aduanera', 'Iva Facturas de Gastos', 'Total Iva');
        $tabliFinal = array('PROVEEDOR', 'FACTURA', 'DETALLE', 'SUBTOTAL', 'IVA', 'TOTAL');
        $otraTabla = array("ISD", "Gastos Financieros", "Flete Maritimo", "Seguro Transporte Internacional", "Trasporte Pais Origen", "Impuestos aduaneros locales", "Bodega/manejo documentos", "Otros Gastos", "Transporte Interno", "Gastos portuarios locales", "DHL Gastroclinica", "DHL", "TOTAL");


        Excel::create('Modulo de Importaciones', function ($excel) use ($ct_importaciones_cab, $titulos, $subtitulos, $otraTabla, $tabli, $tabliFinal, $otros, $id, $aux_ct_importaciones_cab) {
            $excel->sheet('Importaciones', function ($sheet) use ($ct_importaciones_cab, $titulos, $subtitulos, $otraTabla, $tabli, $tabliFinal, $otros, $id, $aux_ct_importaciones_cab) {
                $otros_gastos = recalcula::recalcularImportaciones($id);

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
                $posicion1 = array("H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");


                $comienzo = 1;
                $cabeceraDatos = $ct_importaciones_cab[0];
                $cabeceraTexto = ['CLIENTE', 'PROVEEDOR', 'FACTURA'];
                $cont_cabecera = 1;
                for ($i = 0; $i < count($cabeceraTexto); $i++) {
                    $sheet->cell('A' . $cont_cabecera, function ($cell) use ($cabeceraTexto, $i) {
                        $cell->setValue($cabeceraTexto[$i]);
                        $cell->setFontColor('#000000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('8');
                        $cell->setAlignment('center');
                    });
                    $cont_cabecera++;
                }

                $proveedor_importa = "IMP. AGRUPADA";
                $secuencia_importacion = $cabeceraDatos->secuencia_importacion;
                
                if($aux_ct_importaciones_cab->agrupada == 0){
                    $proveedor_importa = $cabeceraDatos->proveedor_da->razonsocial;
                    $secuencia_importacion = $cabeceraDatos->secuencia_factura;
                }

                $array = [$cabeceraDatos->cliente->nombrecomercial, $proveedor_importa, $secuencia_importacion];
                for ($i = 0; $i < count($array); $i++) {
                    $sheet->cell('B' . $comienzo, function ($cell) use ($array, $i) {
                        $cell->setValue($array[$i]);
                        $cell->setFontColor('#000000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('8');
                        $cell->setAlignment('center');
                    });
                    $comienzo++;
                }


                ImportacionesController::excelDetalles($sheet, $comienzo, $posicion, $titulos);
                $comienzo++;

                $totalCantidad = 0;
                $totalSubtotal = 0;
                $totalPorct = 0;
                $totalCostoAsignadoTotal = 0;
                $totalCostoTotal = 0;


                $logImportaciones = LogImportaciones::where('id_import_cab', $id)->where('principal', 2)->first();

                $compras = Ct_Importaciones_Compras::find($logImportaciones->id_compra);
                foreach ($compras->detalles as $details) {
                    $data = [];
                    $data = [$details->codigo, isset($details->producto->nombre) ? $details->producto->nombre : '', isset($details->producto->nombre) ? $details->producto->nombre : '', $details->cantidad, $details->peso_kg, $details->precio, $details->total, $details->prct_item, $details->costo_asignado_total, $details->costo_asignado_unidad, $details->costo_unitario, $details->costo_total];
                    $totalCantidad += $details->cantidad;
                    $totalSubtotal += $details->total;
                    $totalPorct += $details->prct_item;
                    $totalCostoAsignadoTotal += $details->costo_asignado_total;
                    $totalCostoTotal += $details->costo_total;

                    ImportacionesController::excelDetalles($sheet, $comienzo, $posicion, $data);
                    $comienzo++;
                }

                $arrLetras = ["D", "G", "H", "I", "L"];
                $mostrarTotales = [$totalCantidad, $totalSubtotal, intval($totalPorct), $totalCostoAsignadoTotal, $totalCostoTotal];

                ImportacionesController::excelDetalles($sheet, $comienzo, $arrLetras,  $mostrarTotales, 1);
                $comienzo++;

                /************************* Mostrar Gastos ************************/
                $gastos = Ct_Importaciones_Gasto_Cab::where('id_import_cabl', $id)->where('tipo', "<>", 3)->get();
                $arrNombreGasto = [];
                $totalIvaLiquidacionAduanera = 0;
                $totalIvaFacturaGasto = 0;
                $totalTotalIva = 0;

                $arrLiquidacion = [];

                $arrGastosAcumulado = [];


                $comienzo += 2;
                $auxComienzo = $comienzo;

                //if (count($gastos) > 0) {
                    foreach ($gastos as $gasto) {
                        if (isset($gasto->compra)) {
                            $totalIvaFacturaGasto += $gasto->compra->iva_total;
                            array_push($arrGastosAcumulado, [$gasto->compra->observacion, $gasto->compra->subtotal, $gasto->compra->fecha]);
                        }
                    }

                    $egre_varios = Ct_Comprobante_Egreso_Varios::where('id_importacion', $id)->where("estado", 1)->get();

                    if (count($egre_varios) > 0) {
                        foreach ($egre_varios as $egre_vario) {
                            foreach ($egre_vario->detalles as $detallesVarios) {
                                if ($detallesVarios->tipo_liq == 1) {
                                    $totalIvaLiquidacionAduanera += $detallesVarios->debe;
                                } else if ($detallesVarios->tipo_liq == 2) {
                                    array_push($arrLiquidacion, [$egre_vario->descripcion, $detallesVarios->debe, $egre_vario->fecha_comprobante]);
                                }
                            }
                        }
                    }
                    $totalTotalIva = $totalIvaLiquidacionAduanera + $totalIvaFacturaGasto;

                    $arrTotalIvas = [$totalIvaLiquidacionAduanera, $totalIvaFacturaGasto, $totalTotalIva];

                    for ($l = 0; $l < 3; $l++) {
                        ImportacionesController::excelDetalles($sheet, $auxComienzo, ["A"], [$tabli[$l]]);
                        ImportacionesController::excelDetalles($sheet, $auxComienzo, ["B"], [$arrTotalIvas[$l]]);
                        $auxComienzo++;
                    }

                    $mostrarTotales = [number_format($totalSubtotal, 2, ".", ""), "0.00", number_format($totalSubtotal, 2, ".", ""), number_format($otros_gastos["total_gastos"], 2, ".", ""), number_format(($totalSubtotal + $otros_gastos["total_gastos"]), 2, ".", ""), "0.00"];

                    for ($x = 0; $x < count($subtitulos); $x++) {
                        ImportacionesController::excelDetalles($sheet, $comienzo, ["D"], [$subtitulos[$x]]);
                        ImportacionesController::excelDetalles($sheet, $comienzo, ["E"], [$mostrarTotales[$x]]);
                        $comienzo++;
                    }
                //}



                /************************* FIN MOSTRAR GASTOS ************************/

                /***************************INICIO CARGA CONSOLIDADA*****************************/
                $comienzo += 2;
                $sheet->mergeCells('C' . $comienzo . ':' . 'E' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) {
                    $cell->setValue('CARGA CONSOLIDADA IMPORTACIÓN');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $arrayLiqui = array('PRODUCTO', 'PRECIO', 'FECHA');
                ImportacionesController::excelDetalles($sheet, $comienzo, ["C", "D", "E"], $arrayLiqui);
                $comienzo++;
                //dd($arrLiquidacion[0]);
                foreach ($ct_importaciones_cab as $cabecera) {
                    foreach ($cabecera->detalles as $detalle) {
                        ImportacionesController::excelDetalles($sheet, $comienzo, ["C", "D", "E"], [$detalle->productos->nombre, $detalle->subtotal, $cabecera->fecha]);
                        $comienzo++;
                    }
                }


                for ($k = 0; $k < count($arrLiquidacion); $k++) {
                    ImportacionesController::excelDetalles($sheet, $comienzo, ["C", "D", "E"], $arrLiquidacion[$k]);
                    $comienzo++;
                }

                for ($k = 0; $k < count($arrGastosAcumulado); $k++) {
                    ImportacionesController::excelDetalles($sheet, $comienzo, ["C", "D", "E"], $arrGastosAcumulado[$k]);
                    $comienzo++;
                }
                //dd($otros_gastos);
                ImportacionesController::excelDetalles($sheet, $comienzo, ["C", "D", "E"], ["TOTAL", ($totalSubtotal + $otros_gastos["total_gastos"]), ""]);
                /***************************FIN CARGA CONSOLIDADA*****************************/
            });
        })->export('xlsx');
    }

    public static function excelDetalles($sheet, $comienzo, $arrLetras, $data, $ver = "")
    {
        for ($i = 0; $i < count($data); $i++) {
            $sheet->cell('' . $arrLetras[$i] . '' . $comienzo, function ($cell) use ($data, $i) {
                $cell->setValue("" . $data[$i]);
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');  
                if ($data[$i] == 'Saldo Según Bancos'|| $data[$i] == 'Saldo Según Libros')  {
                    $cell->setBackground('#CDE4F1'); 
                } else {
                    $cell->setBackground('#FFFF'); 
                }
                
                $cell->setFontSize('9');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
        }
        return $comienzo++;
    }

    public function insumos_excel(Request $request)
    {

        $nombre_original = $request['file']->getClientOriginalName();
        $extension       = $request['file']->getClientOriginalExtension();
        $r1              = Storage::disk('public')->put($nombre_original, \File::get($request['file']));
        $arrFinal = array();
        if ($r1) {
            try {

                Excel::load(('storage\\app\\avatars\\' . $nombre_original), function ($reader) use (&$arrFinal) {
                    foreach ($reader->toArray() as $val) {


                        $array = array(
                            'cantidad'  => $val['cantidad'],
                            'codigo' => $val['codigo'],
                        );


                        array_push($arrFinal, $array);
                    }
                });

                return ['array' => $arrFinal];
            } catch (\Exception $e) {

                return ['msj' => $e->getMessage()];
            }
        }
    }

    public function masivoActivoFijo($id_empresa){
        $facFijos = AfFacturaActivoCabecera::all();
        foreach ($facFijos as $facFijo){
            $subtotal0 = 0;
            $subtotal12 = 0;
            foreach ($facFijo->detalles as $details){
                if($details->valor_iva > 0){
                    $subtotal12 += $details->total;
                    $details->iva = 1;
                }else{
                    $subtotal0 += $details->total;
                    $details->iva = 0;
                }
                $total = ($details->cantidad * $details->costo) - $details->descuento;
                $details->total = $total;
                $details->save();
            }
            $facFijo->subtotal0   = $subtotal0;
            $facFijo->subtotal12  = $subtotal12;
            $facFijo->save();

            $compra = Ct_compras::where('id_asiento_cabecera', $facFijo->id_asiento)->first();

            if(!is_null($compra)){

                $compra->subtotal_0     = $subtotal0;
                $compra->subtotal_12    = $subtotal12;
                $compra->save();
            }
            
        }

        // $compras = Ct_compras::where('id_empresa', $id_empresa)->get();

        // foreach($compras as $compra){
        //     $detalle = $compra->detalles;
        //     $subtotal0  = 0;
        //     $subtotal12 = 0;
        //     foreach ($detalle as $value){
        //         if($value->iva == 1){
        //             $subtotal12 += ($value->cantidad * $value->precio) - $value->descuento;
        //         }else{
        //             $subtotal0 += ($value->cantidad * $value->precio) - $value->descuento;
        //         }
        //     }


        //     if($compra->subtotal_0 != $subtotal0 or $compra->subtotal_12 != $subtotal12){
        //         $compra->ip_modificacion      = "{$compra->subtotal_0} - {$compra->subtotal_12}";
        //         $compra->subtotal_0     = $subtotal0;
        //         $compra->subtotal_12    = $subtotal12;
        //         $compra->save();
        //     }
        //  }
        

        dd("ok gracias amigo");
    }
}
