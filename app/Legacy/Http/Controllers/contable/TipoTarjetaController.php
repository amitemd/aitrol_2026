<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Tipo_Tarjeta;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_Tipo_Pago;

class TipoTarjetaController extends Controller
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

    /************************************************
     **********LISTADO TIPO DE TARJETA****************
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tip_tarjeta = Ct_Tipo_Tarjeta::where('estado', '=', 1)->orderby('id', 'asc')->paginate(5);

        return view('contable.tipo_tarjeta.index', ['tip_tarjeta' => $tip_tarjeta]);
    }

    /*************************************************
     ****************CREAR TIPO TARJETA***************
    /*************************************************/
    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('contable.tipo_tarjeta.create');
    }
    /*************************************************
     *************GUARDA TIPO TARJETA*****************
    /*************************************************/

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Ct_Tipo_Tarjeta::create([

            'nombre'          => $request['nombre_tarjeta'],
            'estado'          => $request['estado_tip_tarjeta'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ]);

        return redirect()->route('tipo_tarjeta.index');
    }

    /*************************************************
     **************EDITAR TIPO TARJETA****************
    /*************************************************/

    public function editar($id)
    {
        $tip_pago = Ct_Tipo_Tarjeta::findorfail($id);

        return view('contable.tipo_tarjeta.edit', ['tip_pago' => $tip_pago]);
    }

    /*************************************************
     ************ACTUALIZAR TIPO TARJETA**************
    /*************************************************/

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['id_tipo_pago'];
        //dd($id);
        $tipo_ambiente = Ct_Tipo_Tarjeta::findOrFail($id);

        $input = [

            'nombre'          => $request['tipo_tarj'],
            'estado'          => $request['estado_tip_pago'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        $tipo_ambiente->update($input);

        return redirect()->route('tipo_tarjeta.index');
    }

    /*************************************************
     ****************BUSCAR TIPO TARJETA**************
    /*************************************************/
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'id'     => $request['buscar_codigo'],
            'nombre' => $request['buscar_tipo_tarjeta'],
            'estado' => 1,
        ];

        $tip_tarjeta = $this->doSearchingQuery($constraints);
        return view('contable.tipo_tarjeta.index', ['request' => $request, 'tip_tarjeta' => $tip_tarjeta, 'searchingVals' => $constraints]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Tipo_Tarjeta::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }

    public function informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $referencia, $r, $secuencia)
    {
        $deudas = null;
        $deudas = Ct_ventas::where('estado', '<>', '0')
            ->where('tipo', 'VEN-FA')
            ->where('id_empresa', $id_empresa);

        if (!is_null($fecha_desde)) {
            $deudas = $deudas->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $deudas = $deudas->where('fecha', '<=', $fecha_hasta);
        }
        if (!is_null($secuencia)) {
            $deudas = $deudas->where('nro_comprobante', 'like', '%' . $secuencia . '%');
        }
        if (!is_null($proveedor)) {
            $deudas = $deudas->where('id_cliente', $proveedor);
        }
        if (($variable) == 1) {
            $deudas = $deudas->paginate(20);
        } else {
            $deudas = $deudas->get();
        }

        //dd($deudas);

        return $deudas;
    }
    public function infome_labs(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        $variable2   = 0;
        $totales     = 0;
        $subtotal12  = 0;
        $subtotal0   = 0;
        $subtotal    = 0;
        $descuento   = 0;
        $impuesto    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas($request);
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);
            //dd($deudas);
            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);
            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->total_final;
                        $subtotal12 += $value->subtotal_12;
                        $subtotal0 += $value->subtotal_0;
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                    }
                }
            }
        }
        return view('contable/ventas/informe_labs', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo]);
    }

    public function excel_labs(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $fechaDesde = $request['filfecha_desde'];
        $fechaHasta = $request['filfecha_hasta'];
        $secuencia  = $request['id_proveedor2'];
        $proveedor  = $request['id_cliente'];
        $concepto   = $request['secuencia'];
        $gastos     = $request['tipo'];
        $variable   = 0;
        $fech       = 0;

        $empresa  = Empresa::where('id', $id_empresa)->first();
        $consulta = $this->informe_final($proveedor, $fechaDesde, $fechaHasta, $id_empresa, $variable, $gastos, 0, $concepto);
        //dd($consulta);
        Excel::create('Informe Factura de Ventas ' . $fechaDesde . '-al-' . $fechaHasta, function ($excel) use ($empresa, $consulta, $gastos, $fechaHasta, $fechaDesde) {
            $excel->sheet('Informe Factura de Ventas', function ($sheet) use ($empresa, $fechaDesde, $fechaHasta, $consulta, $gastos) {
                if ($empresa->logo != null) {
                    $fech = 1;
                    $sheet->mergeCells('A1:B1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C1:N1');
                $sheet->cell('C1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:N2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME DE VENTAS LABS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:N3');
                $sheet->cell('C3', function ($cell) use ($fechaDesde, $fechaHasta) {
                    // manipulate the cel
                    if ($fechaDesde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fechaDesde)) . " Hasta " . date("d-m-Y", strtotime($fechaHasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fechaHasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#c3c3c3');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA DE CREDITO');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSF / DEP');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PEND FC SEG');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL VTA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
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

                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL
                $sheet->cells('C3:N3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:N2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C1:N1', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A4:N4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $suma_uno = 0;
                $suma_dos = 0;
                $suma_tres = 0;
                $suma_cuatro = 0;
                $suma_cinco = 0;
                $suma_sei = 0;
                $suma_sette = 0;
                $i = 5;
                foreach ($consulta as $consu) {
                    $subtotalf = 0;
                    $subtotalf = $consu->subtotal_12 + $consu->subtotal_0 + $consu->descuento;
                    $detCompIngreso = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $consu->id)->first();
                    $pago = null;
                    $porcTarjeta = null;
                    $total_vta = null;
                    if (!is_null($detCompIngreso)) {
                        $pago = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $detCompIngreso->id_comprobante)->first();
                        //dd($pago->total);
                        $porcTarjeta = Ct_Tipo_Pago::where('id', $pago->id_tipo)->first();
                        $total_vta = $pago->total + $porcTarjeta->porcentaje;
                        $suma_tres += $porcTarjeta->porcentaje;
                        $suma_uno += $pago->total;
                        $suma_cuatro += $total_vta;
                        if (empty($pago)) {
                        } elseif (($pago->id_tipo) == 1) {
                            $suma_uno += $pago->total;
                        } elseif (($pago->id_tipo) == 4) {
                            $suma_dos += $pago->total;
                        } elseif (($pago->id_tipo) == 5 && 3) {
                            $suma_cinco += $pago->total;
                        } elseif (($pago->id_tipo) == 2) {
                            $suma_sei += $pago->total;
                        } elseif (($pago->id_tipo) == 7) {
                            $suma_sette += $pago->total;
                        }
                    }
                    $sheet->cells('A' . $i, function ($cells) use ($consu) {
                        $cells->setValue($consu->fecha);
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('B' . $i, function ($cells) use ($consu) {
                        $cells->setValue($consu->cliente->nombre);
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('C' . $i, function ($cells) use ($consu) {
                        $cells->setValue($consu->id_cliente);
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('D' . $i, function ($cells) use ($consu,$subtotalf) {
                        $cells->setValue(number_format($subtotalf,2,'.',','));
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('E' . $i, function ($cells) use ($consu) {
                        $cells->setValue($consu->descuento);
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('F' . $i, function ($cells) use ($consu, $subtotalf) {
                        $cells->setValue(number_format($consu->total_final, 2, '.', ','));
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('G' . $i, function ($cells) use ($consu) {
                        $cells->setValue($consu->nro_comprobante);
                        $cells->setAlignment('left');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('H' . $i, function ($cells) use ($consu,$pago) {
                        if (empty($pago)){
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } elseif ($pago->id_tipo == 1) {
                            $cells->setValue($pago->total);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('I' . $i, function ($cells) use ($consu, $pago) {
                        if (empty($pago))  {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } elseif ( ($pago->id_tipo == 4)) {
                            $cells->setValue($pago->total);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('J' . $i, function ($cells) use ($consu, $porcTarjeta) {
                            if(empty($porcTarjeta)){
                                $cells->setValue('-');
                                $cells->setAlignment('left');
                                $cells->setFontWeight('bold');
                                $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            }else{
                            $cells->setValue($porcTarjeta->porcentaje);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('K' . $i, function ($cells) use ($consu, $pago) {
                        if (empty($pago)) {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } elseif ($pago->id_tipo == 5) {
                            $cells->setValue($pago->total);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('L' . $i, function ($cells) use ($consu, $pago) {
                        if (empty($pago)) {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } elseif ($pago->id_tipo == 2) {
                            $cells->setValue($pago->total);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('M' . $i, function ($cells) use ($consu, $pago) {
                        if (empty($pago)) {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } elseif ($pago->id_tipo == 7) {
                            $cells->setValue($pago->total);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cells->setValue('-');
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cells('N' . $i, function ($cells) use ($consu, $pago,$total_vta) {
                            $cells->setValue($total_vta);
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                
                    });
                    $i++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
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

    function descarga_excel(Request $request){

        $productos= DB::table('producto as p')->join('movimiento as m','m.id_producto','p.id')->where('p.estado','1')->groupBy('m.id_producto')->select('m.id_producto','m.tipo as tipo','m.cantidad','p.nombre','m.id_producto','p.estado','p.codigo','m.created_at as created_at',DB::raw('COUNT(CASE WHEN m.tipo=2 THEN 0 ELSE null END) as transito'),DB::raw('COUNT(CASE WHEN m.tipo=1 THEN 0 ELSE null END) as ingreso'))->get();
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        Excel::create('Administración de Productos ', function ($excel) use ($empresa, $id_empresa, $productos) {
            $excel->sheet('Administración de Productos', function ($sheet)  use ($empresa, $id_empresa, $productos) {
                $sheet->mergeCells('A1:E2');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("Administración de Productos");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Codigo');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cantidad');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Bodega');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Bodega en Transito');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cells('A1:E1', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('A2:E2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('A3:E3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $i = 4;
                foreach ($productos as $value) {


                    $sheet->cells('A' . $i, function ($cells) use ($value) {
                        $cells->setValue($value->codigo);
                        $cells->setAlignment('left');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('B' . $i, function ($cells) use ($value) {
                        $cells->setValue($value->nombre);
                        $cells->setAlignment('left');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('C' . $i, function ($cells) use ($value) {
                        $cells->setValue($value->cantidad);
                        $cells->setAlignment('left');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('D' . $i, function ($cells) use ($value) {
                        $cells->setValue($value->ingreso);
                        $cells->setAlignment('left');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('E' . $i, function ($cells) use ($value) {
                        if(($value->transito)==0){
                            $cells->setValue($value->transito);
                            $cells->setAlignment('left');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setBackground('#EC7063');
                        }else{
                        $cells->setValue($value->transito);
                        $cells->setAlignment('left');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');

                        }
                    });
                    $i ++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
        })->export('xlsx');

    

    }
}
