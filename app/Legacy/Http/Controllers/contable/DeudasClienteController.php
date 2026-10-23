<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Asientos_Cabecera;
use Excel;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Detalle_Anticipo_Proveedores;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Cruce_Clientes;
use Sis_medico\Ct_Detalle_Pago_Cruce;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Nota_Credito_Clientes;

class DeudasClienteController extends Controller
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
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos = $request['esfac_contable'];
        $variable = 0;
        $debe = 0;
        $total = 0;
        $haber = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $deudas = [];
        $cliente = Ct_Clientes::where('estado', '1')->get();
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor = $request['id_proveedor'];
        if ($proveedor == null) {
            $proveedor = $request['id_proveedor2'];
        }
        if (!is_null($fecha_hasta)) {
            $deudas = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 0);
            $deudas_val = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
            foreach ($deudas_val as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $total += $value->valor_contable;
                        $debe += $value->total_final;
                        if (!is_null($value->comp_ingreso) && $value->comp_ingreso != '[]') {
                            foreach ($value->comp_ingreso as $v) {
                                if (isset($v->ingreso)) {
                                    if ($v->ingreso->estado == 1) {
                                        $haber += $v->total;
                                    }
                                }
                            }
                        }
                        if (!is_null($value->cruce) && $value->cruce != '[]') {
                            foreach ($value->cruce as $vs) {
                                if ($vs->cabecera->estado == 1) {
                                    $haber += $vs->total;
                                }
                            }
                        }
                        if (!is_null($value->chequepost) && $value->chequepost != '[]') {
                            foreach ($value->chequepost as $vs) {
                                if ($vs->cabecera->estado == 1) {
                                    $haber += $vs->total;
                                }
                            }
                        }
                        if (!is_null($value->credito) && $value->credito != '[]') {
                            foreach ($value->credito as $vs) {
                                if ($vs->cabecera->estado == 1) {
                                    $haber += $vs->cabecera->total_credito;
                                }
                            }
                        }
                        if (!is_null($value->retenciones)) {
                            //dd($value->retenciones);
                            $totals = ($value->retenciones->valor_fuente) + ($value->retenciones->valor_iva);
                            $haber += $totals;
                        }
                    }
                }
            }
        }
        return view('contable/deudas_cliente/index', ['deudas' => $deudas, 'cliente' => $cliente, 'empresa' => $empresa, 'totales' => $total, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'debe' => $debe, 'haber' => $haber]);
    }
    public function corteafecha(Request $r)
    {
        $data['empresa'] = Empresa::where('id', session('id_empresa'))->first();
        $data['fecha_desde'] = $r->fecha_desde;
        $data['fecha_hasta'] = $r->fecha_hasta;
        if (count($r->input()) > 0) {
            $datos = Ct_ventas::getCorteaFecha($r);
            $data['dato'] = $datos;
        } else {
            return view('contable/deudas_cliente/view_deuda_corte', $data);
        }
    }
    //Miyako Sushi 
    public function deudasvspagos2($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $paginate)
    {
        $deudas = null;
        if (!is_null($fecha_desde)) {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::whereHas('ventas', function ($q) use ($proveedor) {
                    $q->where('id_cliente', $proveedor)->where('estado', '<>', '0')->orderBy('fecha', 'DESC')->orderBy('numero', 'DESC');
                })->where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->paginate(20);
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::whereHas('ventas', function ($q) use ($proveedor) {
                        $q->where('id_cliente', $proveedor)->where('estado', '<>', '0')->orderBy('fecha', 'DESC')->orderBy('numero', 'DESC');
                    })->where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->get();
                }
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->paginate(20);
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->wherebetween('fecha_asiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_empresa', $id_empresa)->get();
                }
            }
        } else {
            if ($proveedor != null) {
                $deudas = Ct_Asientos_Cabecera::whereHas('ventas', function ($q) use ($proveedor) {
                    $q->where('id_cliente', $proveedor)->where('estado', '<>', '0')->orderBy('fecha', 'DESC')->orderBy('numero', 'DESC');
                })->where('fecha_asiento', '<', $fecha_hasta)->where('estado', '!=', 'null')->where('id_empresa', $id_empresa)->paginate(20);
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::whereHas('ventas', function ($q) use ($proveedor) {
                        $q->where('id_cliente', $proveedor)->where('estado', '<>', '0')->orderBy('fecha', 'DESC')->orderBy('numero', 'DESC');
                    })->where('fecha_asiento', '<', $fecha_hasta)->where('estado', '!=', 'null')->where('id_empresa', $id_empresa)->get();
                }
            } else {
                $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('fecha_asiento', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->paginate(20);
                if ($paginate == 1) {
                    $deudas = Ct_Asientos_Cabecera::where('estado', '!=', 'null')->where('fecha_asiento', '<', $fecha_hasta)->where('id_empresa', $id_empresa)->get();
                }
            }
        }

        return $deudas;
    }
    public function deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $paginate)
    {
        $ventas = Ct_ventas::where('estado', '<>', '0')
        //->where('tipo', '<>', 'N-D')
        ->where('id_empresa', $id_empresa);
        if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {
            $ventas = $ventas->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($fecha_hasta)) {
            $ventas = $ventas->where('fecha', '<', $fecha_hasta);
        }
        if (!is_null($proveedor)) {
            $ventas = $ventas->where('id_cliente', $proveedor);
        }
        $ventas = $ventas->orderBy('fecha', 'DESC')->orderBy('numero', 'DESC')->get();
        return $ventas;
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
        $variable = 1;
        $empresa = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, 1);
        Excel::create('Informe Deudas Vs Cobros-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Deudas vs Cobros', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:L2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:L3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME DEUDAS VS COBROS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:L4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
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
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G5:H5');
                $sheet->cell('G5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEBE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HABER');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
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
                    'A'     =>  20,
                    'B'     =>  20,
                    'C'     =>  21,
                    'D'     =>  22,
                    'E'     =>  20,
                    'F'     =>  20,
                    'G'     =>  10,
                    'H'     =>  10,
                    'I'     =>  20,
                    'J'     =>  20,
                    'K'     =>  20,
                    'L'     =>  20,
                    'M'     =>  20,

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
                if ($value->estado != 0) {
                    $totales += $value->valor_contable;
                    $debe += $value->total_final;

                    $sheet->mergeCells('A' . $i . ':D' . $i);
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (($value->numero) != null && ($value->nro_comprobante) != null) {
                            $cell->setValue(" Fact : #" . $value->numero . " Ref: " . $value->nro_comprobante);
                        }

                        $cell->setFontWeight('bold');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel 
                        if ($value->fecha != null) {
                            $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                        }
                        $cell->setFontWeight('bold');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell,1);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($variable) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('VEN-FA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G' . $i . ':H' . $i);
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        if ($value->numero != null) {
                            $cell->setValue($value->numero);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        if ($value->total_final != null) {
                            $cell->setValue($value->total_final);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        if ($value->total_final != null) {
                            $cell->setValue($value->total_final);
                        }
                        $cell->setFontWeight('bold');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setValue('0.00');
                        $cell->setFontWeight('bold');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);

                        $cell->setValue(number_format($value->valor_contable, 2, '.', ''));
                        $cell->setValignment('center');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    if ($value->comp_ingreso != null & $value->comp_ingreso != '[]') {

                        foreach ($value->comp_ingreso as $v) {
                            if ($v->ingreso->estado == 1) {
                                $haber += $v->total;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {
                                    // manipulate the cel
                                    if ($v->ingreso->secuencia != null) {
                                        $cell->setValue("    " . $value->cliente->nombre . " # " . $v->ingreso->secuencia . " Ref: " . $v->ingreso->id);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {
                                    // manipulate the cel 
                                    if ($v->ingreso->fecha != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->ingreso->fecha)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($variable) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CLI-IN');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->ingreso->secuencia != null) {
                                        $cell->setValue($v->ingreso->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total_factura != null) {
                                        $cell->setValue($v->total_factura);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    $parcial = Ct_Nota_Credito_Clientes::join('ct_clientes as c', 'ct_nota_credito_clientes.id_cliente', 'c.identificacion')
                        ->where('ct_nota_credito_clientes.id_factura', $value->id)->where('ct_nota_credito_clientes.estado', 1)->get();
                    if (count($parcial) > 0) {
                        foreach ($parcial as $par) {
                            if ($par->estado == 1) {
                                $haber += $par->total_credito;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($par) {
                                    // manipulate the cel
                                    if ($par->secuencia != null) {
                                        $cell->setValue("    " . $par->nombre . " # " . $par->concepto . " Ref: " . $par->nro_comprobante);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($par) {
                                    // manipulate the cel 
                                    if (date("d-m-Y", strtotime($par->fecha)) != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($par->fecha)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CLI-NC');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($par) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($par->secuencia != null) {
                                        $cell->setValue(str_pad($par->secuencia, 9, 0, STR_PAD_LEFT));
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($par) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($par->total_credito != null) {
                                        $cell->setValue($par->total_credito);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($par) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($par->total_credito != null) {
                                        $cell->setValue($par->total_credito);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }

                    if ($value->cruce != null & $value->cruce != '[]') {

                        foreach ($value->cruce as $v) {
                            if ($v->cabecera->estado == 1) {
                                $haber += $v->total;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {
                                    // manipulate the cel
                                    if ($v->cabecera->secuencia != null) {
                                        $cell->setValue("    " . $value->cliente->nombre . " # " . $v->cabecera->secuencia . " Ref: " . $v->cabecera->id);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {
                                    // manipulate the cel 
                                    if ($v->cabecera->fecha_pago != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->cabecera->fecha_pago)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CLI-BAN');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->cabecera->secuencia != null) {
                                        $cell->setValue($v->cabecera->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total_factura != null) {
                                        $cell->setValue($v->total_factura);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    if ($value->credito != null & $value->credito != '[]') {

                        foreach ($value->credito as $v) {
                            if ($v->cabecera->estado == 1) {
                                $haber += $v->cabecera->total_credito;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {
                                    // manipulate the cel
                                    if ($v->cabecera->secuencia != null) {
                                        $cell->setValue("    " . $value->cliente->nombre . " # " . $v->cabecera->secuencia . " Ref: " . $v->cabecera->id);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {
                                    // manipulate the cel 
                                    if ($v->cabecera->fecha_pago != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->cabecera->fecha_pago)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CLI-CREDITO');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->secuencia_factura != null) {
                                        $cell->setValue($v->secuencia_factura);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->cabecera->total_credito != null) {
                                        $cell->setValue($v->cabecera->total_credito);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->cabecera->total_credito != null) {
                                        $cell->setValue($v->cabecera->total_credito);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    if ($value->chequepost != null & $value->chequepost != '[]') {

                        foreach ($value->chequepost as $v) {
                            if ($v->cabecera->estado == 1) {
                                $haber += $v->total;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {
                                    // manipulate the cel
                                    if ($v->cabecera->secuencia != null) {
                                        $cell->setValue("    " . $value->cliente->nombre . " # " . $v->cabecera->secuencia . " Ref: " . $v->cabecera->id);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {
                                    // manipulate the cel 
                                    if ($v->cabecera->fecha_pago != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->cabecera->fecha_pago)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CLI-CH');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->cabecera->secuencia != null) {
                                        $cell->setValue($v->cabecera->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total_factura != null) {
                                        $cell->setValue($v->total_factura);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $i++;
                            }
                        }
                    }
                    if ($value->cruce_cuentas != null & $value->cruce_cuentas != '[]') {

                        foreach ($value->cruce_cuentas as $v) {
                            if ($v->estado == 1) {
                                $haber += $v->total;
                                $sheet->mergeCells('A' . $i . ':D' . $i);
                                $sheet->cell('A' . $i, function ($cell) use ($value, $v) {
                                    // manipulate the cel
                                    if ($v->secuencia != null) {
                                        $cell->setValue("    " . $value->cliente->nombre . " # " . $v->secuencia . " Ref: " . $v->id);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($v) {
                                    // manipulate the cel 
                                    if ($v->fecha != null) {
                                        $cell->setValue(date("d-m-Y", strtotime($v->fecha)));
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell,1);
                                });
                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setValue('CRUCE-CUENTAS');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->mergeCells('G' . $i . ':H' . $i);
                                $sheet->cell('G' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->secuencia != null) {
                                        $cell->setValue($v->secuencia);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('I' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total_factura != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    $cell->setValue('0.00');
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('K' . $i, function ($cell) use ($v) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);
                                    if ($v->total != null) {
                                        $cell->setValue($v->total);
                                    }
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('L' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
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
                        $suma = $value->retenciones->valor_fuente + $value->retenciones->valor_iva;
                        $totals = ($value->retenciones->valor_fuente) + ($value->retenciones->valor_iva);
                        $haber += $totals;
                        $sheet->mergeCells('A' . $i . ':D' . $i);
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->retenciones->secuencia != null) {
                                $cell->setValue("    " . $value->cliente->nombre . " # " . $value->retenciones->secuencia . " Ref: " . $value->retenciones->id);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell);
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel 
                            if ($value->retenciones->created_at != null) {
                                $cell->setValue(date("d-m-Y", strtotime(substr($value->retenciones->fecha, 0, 10))));
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            // $this->setSangria($cont, $cell,1);
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($variable) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);

                            $cell->setValue('CLI-RE');

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('G' . $i . ':H' . $i);
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            if ($value->retenciones->id != null) {
                                $cell->setValue($value->retenciones->id);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I' . $i, function ($cell) use ($suma) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue($suma);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue('0.00');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K' . $i, function ($cell) use ($suma) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            $cell->setValue($suma);
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
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
        }
        $sheet->cell('A' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue("TOTAL :");
            $cell->setFontWeight('bold');
            $cell->setValignment('center');
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
        $sheet->setAutoSize(array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
        ));
        return $i;
    }
}
