<?php

namespace Sis_medico\Http\Controllers\financiero;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;

class Indice_FinancieroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indicefinanciero_index(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $balance    = array();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];

        } else {
            $fecha_desde = date('Y');
            $fecha_hasta = date('Y');
        }
        $fecha_desde = '2010';

        $impuesto_causado = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, 2);
        $participacion    = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, 2);

        $activo_corriente  = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01', 2), 2);
        $activo_ncorriente = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.02', 2), 2);
        $activo_total      = round($activo_corriente + $activo_ncorriente, 2);

        $pasivo_corriente = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '2.01', 2), 2);
        //dd($pasivo_corriente . ' -- ' . $impuesto_causado . ' -- ' . $participacion);
        $pasivo_corriente = $pasivo_corriente + $impuesto_causado + $participacion;

        $pasivo_ncorriente = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '2.02', 2), 2);
        $pasivo_total      = round($pasivo_corriente + $pasivo_ncorriente, 2);
        $patrimonio_neto   = round($activo_total - $pasivo_total, 2);
        $gastos            = round($this->gastos($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);

        //documentos por cobrar
        $dc1 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.05', 2);
        $dc2 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.06', 2);
        $dc3 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.07', 2);
        $dc4 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.08', 2);
        $dc5 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.09', 2);
        $dct = $dc1 + $dc2 + $dc3 + $dc4 + $dc5;

        $documentos_cobrar = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.02.05.01', 2);
        //documentos por cobrar clientes

        //restante
        $ventas  = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I', 2);
        $compras = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '5.1', 2), 2);

        $dp1 = EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '2.01.03', 2);
        $dpt = $dp1;

        $documentos_pagar       = round($dpt, 2);
        $gastos_operacionales   = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '5.2.01', 2), 2);
        $gastos_administrativos = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '5.2.02', 2), 2);
        $intereses              = round($this->intereses($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $gastos_financieros     = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '5.2.03', 2), 2);
        $costos_venta           = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '5.1', 2), 2);
        $uaii                   = $ventas - $costos_venta - $gastos_operacionales;
        //dd($ventas);
        //dd($uaii);
        $uai              = $uaii - $intereses;
        $inventario_total = round(EstadoResultado::detalle_total_cuenta_general($fecha_desde, $fecha_hasta, '1.01.03', 2), 2);

        $total                = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, 2);
        $renta_acumulada      = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, 2);
        $utilidad_operacional = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, 2);
        //dd($utilidad_operacional);
        $utilidad_neta = $total - $renta_acumulada;
        //dd($utilidad_neta . ' -- ' . $total . ' -- ' . $renta_acumulada);
        //dd($inventario_total);
        $periodo_desde = $this->fechaTexto($fecha_desde);
        $periodo_hasta = $this->fechaTexto($fecha_hasta);
        return view('financiero/indice_financiero/indicefinanciero_index', ['periodo_desde' => $periodo_desde, 'periodo_hasta' => $periodo_hasta, 'activo_corriente' => $activo_corriente, 'activo_ncorriente' => $activo_ncorriente, 'activo_total' => $activo_total, 'pasivo_total' => $pasivo_total, 'inventarios_total' => $inventario_total, 'gastos_administracion' => $gastos_administrativos, 'gastos_financieros' => $gastos_financieros, 'gastos' => $gastos, 'costos_ventas' => $costos_venta, 'pasivo_corriente' => $pasivo_corriente, 'patrimonio_neto' => $patrimonio_neto, 'compras' => $compras, 'ventas' => $ventas, 'gastos_operacionales' => $gastos_operacionales, 'documentos_cobrar' => $documentos_cobrar, 'uaii' => $uaii, 'uai' => $uai, 'documentos_pagar' => $documentos_pagar, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'balance' => $balance, 'utilidad_neta' => $utilidad_neta, 'utilidad_operacional' => $utilidad_operacional]);
    }

    public function buscar(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_proveedor'];
        if ($fecha_desde == null) {
            $fecha_desde = date('Y');
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m');
        }
        $deudas = $this->deudasvspagos($fecha_desde, $fecha_hasta, $proveedor, $id_empresa, $gastos, $variable);

        return view('financiero/indice_financiero/indicefinanciero_index', ['periodo_desde' => $periodo_desde, 'periodo_hasta' => $periodo_hasta, 'deudas' => $deudas, 'empresa' => $empresa, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor]);
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
    public function excel_indicefinanciero_index(Request $request)
    {
        $id_empresa             = $request->session()->get('id_empresa');
        $fecha_desde            = $request['filfecha_desde'];
        $proveedor              = $request['id_proveedor'];
        $fecha_hasta            = $request['filfecha_hasta'];
        $empresa                = Empresa::where('id', $id_empresa)->first();
        $activo_corriente       = round($this->activos($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $activo_ncorriente      = round($this->activos($fecha_desde, $fecha_hasta, $id_empresa, 2), 2);
        $activo_total           = round($activo_corriente + $activo_ncorriente, 2);
        $pasivo_corriente       = round($this->pasivos($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $pasivo_ncorriente      = round($this->pasivos($fecha_desde, $fecha_hasta, $id_empresa, 2), 2);
        $pasivo_total           = round($pasivo_corriente + $pasivo_ncorriente, 2);
        $patrimonio_neto        = round($activo_total - $pasivo_total, 2);
        $gastos                 = round($this->gastos($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $documentos_cobrar      = round($this->documentos_cobrar($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $ventas                 = round($this->documentos_cobrar($fecha_desde, $fecha_hasta, $id_empresa, 2), 2);
        $compras                = round($this->documentos_pagar($fecha_desde, $fecha_hasta, $id_empresa, 2), 2);
        $documentos_pagar       = round($this->documentos_pagar($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $gastos_operacionales   = round($this->gastos_operacionales($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $gastos_administrativos = round($this->gastos_administracion($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $intereses              = round($this->intereses($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $gastos_financieros     = round($this->gastos_financieros($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $costos_ventas          = round($this->costos_ventas($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $uaii                   = $ventas - $costos_ventas - $gastos_operacionales;
        $uai                    = $uaii - $intereses;
        $inventarios_total      = round($this->inventario_total($fecha_desde, $fecha_hasta, $id_empresa, 1), 2);
        $periodo_desde          = $this->fechaTexto($fecha_desde);
        $periodo_hasta          = $this->fechaTexto($fecha_hasta);
        //liquidez

        if ($pasivo_corriente != 0) {$liquidez_corriente = round($activo_corriente / $pasivo_corriente, 2);} else { $liquidez_corriente = 0;}
        //prueba acida
        $activo_corriente_inventario = round($activo_corriente + $inventarios_total, 2);
        if ($pasivo_corriente != 0) {$prueba_acida = round($activo_corriente_inventario / $pasivo_corriente, 2);} else { $prueba_acida = 0;}
        //capital trabajo neto
        $capital_trabajo_neto = round($activo_corriente - $pasivo_corriente, 2);
        //endeudamiento activo
        if ($activo_total != 0) {$endeudamiento_activo = round($pasivo_total / $activo_total, 2);} else { $endeudamiento_activo = 0;}
        //Endeudamiento Patrimordial
        if ($patrimonio_neto != 0) {$endeudamiento_patrimordial = round($pasivo_total / $patrimonio_neto, 2);} else { $endeudamiento_patrimordial = 0;}
        //endeudamiento activo_fijo
        if ($activo_ncorriente != 0) {$endeudamiento_activo_fijo = round($patrimonio_neto / $activo_ncorriente, 2);} else { $endeudamiento_activo_fijo = 0;}
        // Apalancamiento
        if ($activo_ncorriente != 0) {$apalancamiento = round($patrimonio_neto / $activo_total, 2);} else { $apalancamiento = 0;}
        //Apalancamiento Financiero
        if ($activo_total != 0) {$uaii_activostotales = $uaii / $activo_total;} else { $uaii_activostotales = 0;}
        if ($patrimonio_neto != 0) {$uai_patrimonio = $uai / $patrimonio_neto;} else { $uai_patrimonio = 0;}
        if ($uai_patrimonio != 0) {$apalancamiento_financiero = round($uaii_activostotales / $uai_patrimonio, 2);} else { $apalancamiento_financiero = 0;}
        // Rotacion de Cartera
        if ($documentos_cobrar != 0) {$rotacion_cartera = round($ventas / $documentos_cobrar, 2);} else { $rotacion_cartera = 0;}
        // Rotacion Activo
        if ($activo_ncorriente != 0) {$rotacion_activo = round($ventas / $activo_ncorriente, 2);} else { $rotacion_activo = 0;}
        // Rotacion Ventas
        if ($activo_total != 0) {$rotacion_ventas = round($ventas / $activo_total, 2);} else { $rotacion_ventas = 0;}
        //Periodo por cobranza
        $documentos_cobrar365 = $documentos_cobrar;
        if ($ventas != 0) {$periodo_de_cobranza = round(($documentos_cobrar) / ($ventas), 2);} else { $periodo_de_cobranza = 0;}
        // Periodo Medio Pago
        $documentos_pagar365 = $documentos_pagar;
        if ($compras != 0) {$periodo_medio_pago = round(($documentos_cobrar) / ($compras), 2);} else { $periodo_medio_pago = 0;}
        // Impacto Gasto Admministracion y Ventas
        if ($ventas != 0) {$impacto_gastoadmin = round($gastos_administrativos / $ventas, 2);} else { $impacto_gastoadmin = 0;}
        //Impacto de la carga financiera
        if ($ventas != 0) {$impacto_carga_financiera = round($gastos_financieros / $ventas, 2);} else { $impacto_carga_financiera = 0;}
        //Rotacion inventario
        if ($inventarios_total != 0) {$rotacion_inventario = round($costos_ventas / $inventarios_total, 2);} else { $rotacion_inventario = 0;}
        //Periodo Inventario
        if ($rotacion_inventario != 0) {$periodo_inventario = round(360 / $rotacion_inventario);} else { $periodo_inventario = 0;}
        //Rentabilidad del Activo (Dupont)
        $utilidad_neta = $gastos;
        if ($ventas != 0 && $activo_total != 0) {$rentabilidad_neta = round((($utilidad_neta / $ventas) * ($ventas / $activo_total) * 100), 2);} else { $rentabilidad_neta = 0;}
        //Margen Bruto
        $costosmenosventa = round($ventas - $costos_ventas, 2);
        if ($ventas != 0) {$margen_bruto = round(($costosmenosventa / $ventas) * 100, 2);} else { $margen_bruto = 0;}
        // Margen Operacional
        $utilidad_bruta       = round($ventas - $costos_ventas, 2);
        $utilidad_operacional = round($utilidad_bruta - $gastos_operacionales, 2);
        if ($ventas != 0) {$margen_operacional = round(($utilidad_operacional / $ventas) * 100, 2);} else { $margen_operacional = 0;}
        //Rentabilidad Neta de Ventas
        if ($ventas != 0) {$rentabilidad_netav = round(($utilidad_neta / $ventas) * 100, 2);} else { $rentabilidad_netav = 0;}
        //rentabilidad operacional del patrimonio
        if ($patrimonio_neto != 0) {$rentabilidad_op = round(($utilidad_operacional / $patrimonio_neto) * 100, 2);} else { $rentabilidad_op = 0;}
        //rentabilidad financiera
        //$rentabilidad_fin_total= round((($ventas/$activo_total)* ($uaii/$ventas)* ($activo_total/$patrimonio_neto) * (($uai)/($uaii))*($utilidad_neta/$uai))*100,2);
        if ($activo_total != 0 && $ventas != 0 && $patrimonio_neto != 0 && $uaii != 0 && $uai != 0) {$rentabilidad_fin_total = round((($ventas / $activo_total) * ($uaii / $ventas) * ($activo_total / $patrimonio_neto) * (($uai) / ($uaii)) * ($utilidad_neta / $uai)) * 100, 2);} else { $rentabilidad_fin_total = 0;}

        Excel::create('Indice_Financiero-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $periodo_desde, $periodo_hasta, $fecha_hasta, $fecha_desde, $activo_corriente, $activo_ncorriente, $activo_corriente_inventario, $activo_total, $pasivo_corriente, $pasivo_total, $patrimonio_neto, $gastos, $documentos_cobrar, $ventas, $compras, $documentos_pagar, $gastos_operacionales, $gastos_administrativos, $intereses, $gastos_financieros, $costos_ventas, $uaii, $uai, $inventarios_total, $liquidez_corriente, $prueba_acida, $capital_trabajo_neto, $endeudamiento_activo, $endeudamiento_patrimordial, $endeudamiento_activo_fijo, $apalancamiento, $uaii_activostotales, $uai_patrimonio, $apalancamiento_financiero, $rotacion_cartera, $rotacion_activo, $rotacion_ventas, $documentos_cobrar365, $periodo_medio_pago, $impacto_gastoadmin, $impacto_carga_financiera, $rotacion_inventario, $periodo_inventario, $rentabilidad_neta, $costosmenosventa, $margen_bruto, $utilidad_bruta, $utilidad_operacional, $margen_operacional, $rentabilidad_netav, $rentabilidad_op, $rentabilidad_fin_total, $periodo_de_cobranza, $utilidad_neta) {
            $excel->sheet('Indice_Financiero', function ($sheet) use ($empresa, $periodo_desde, $periodo_hasta, $fecha_desde, $fecha_hasta, $activo_corriente, $pasivo_corriente, $pasivo_total, $patrimonio_neto, $gastos, $documentos_cobrar, $compras, $documentos_pagar, $gastos_operacionales, $gastos_administrativos, $intereses, $gastos_financieros, $costos_ventas, $uaii, $uai, $inventarios_total, $liquidez_corriente, $prueba_acida, $capital_trabajo_neto, $endeudamiento_activo, $endeudamiento_activo_fijo, $endeudamiento_patrimordial, $apalancamiento, $uaii_activostotales, $uai_patrimonio, $apalancamiento_financiero, $rotacion_cartera, $rotacion_activo, $rotacion_ventas, $documentos_cobrar365, $periodo_medio_pago, $impacto_gastoadmin, $impacto_carga_financiera, $rotacion_inventario, $periodo_inventario, $rentabilidad_neta, $costosmenosventa, $margen_bruto, $utilidad_bruta, $utilidad_operacional, $margen_operacional, $rentabilidad_netav, $rentabilidad_op, $rentabilidad_fin_total, $activo_corriente_inventario, $activo_total, $activo_ncorriente, $ventas, $periodo_de_cobranza, $utilidad_neta) {
                $sheet->mergeCells('B1:H1');
                $sheet->cell('B1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $sheet->mergeCells('B3:H3');
                $sheet->cell('B3', function ($cell) use ($periodo_desde, $periodo_hasta) {
                    // manipulate the cel
                    $cell->setValue(date("m-Y", strtotime($periodo_hasta)) . " a " . date("m-Y", strtotime($periodo_hasta)));
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B4:H5');
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INDICADORES FINANCIEROS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('22');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B7:H7');
                $sheet->cell('B7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIQUIDEZ');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C9:C10');
                $sheet->cell('C9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Liquidez corriente');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('C13:C14');
                $sheet->cell('C13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Prueba acida');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('C16:C18');
                $sheet->cell('C16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Capital trabajo neto');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo corriente');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Pasivo Corriente');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo corriente - Inventario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D14', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Pasivo Corriente');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D17', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo corriente  - Pasivo Corriente');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E9:E10');
                $sheet->cell('E9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E13:E14');
                $sheet->cell('E13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E17', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->cell('F9', function ($cell) use ($activo_corriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_corriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F10', function ($cell) use ($pasivo_corriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $pasivo_corriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F13', function ($cell) use ($activo_corriente_inventario) {
                    // manipulate the cel
                    $cell->setValue('$ ' . $activo_corriente_inventario); //concadenar es poner un string y un punto
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F14', function ($cell) use ($pasivo_corriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $pasivo_corriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F17', function ($cell) use ($capital_trabajo_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $capital_trabajo_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G9', function ($cell) use ($liquidez_corriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $liquidez_corriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G13', function ($cell) use ($prueba_acida) {
                    // manipulate the cel
                    $cell->setValue('$' . $prueba_acida);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //SOLVENCIA
                $sheet->mergeCells('B21:H21');
                $sheet->cell('B21', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SOLVENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->mergeCells('C23:C24');
                $sheet->cell('C23', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Endeudamiento del Activo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C27:C28');
                $sheet->cell('C27', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Endeudamiento Patrimonial');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C31:C32');
                $sheet->cell('C31', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Endeudamiento del Activo Fijo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C35:C36');
                $sheet->cell('C35', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Apalancamiento');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C39:C40');
                $sheet->cell('C39', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Apalancamiento Financiero');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });

                $sheet->cell('D23', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Pasivo Total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D24', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D27', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Pasivo Total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D28', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Patrimonio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D31', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Patrimonio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D32', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo fijo Neto');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D35', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Patrimonio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('D36', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activo Total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D39', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('(UAII/Activos Totales)');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D40', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('(UAI/Patrimonio)');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->mergeCells('E23:E24');
                $sheet->cell('E23', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E27:E28');
                $sheet->cell('E27', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E31:E32');
                $sheet->cell('E31', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E35:E36');
                $sheet->cell('E35', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E39:E40');
                $sheet->cell('E39', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F23', function ($cell) use ($pasivo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $pasivo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F24', function ($cell) use ($activo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F27', function ($cell) use ($pasivo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $pasivo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F28', function ($cell) use ($patrimonio_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $patrimonio_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F31', function ($cell) use ($patrimonio_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $patrimonio_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F32', function ($cell) use ($activo_ncorriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_ncorriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F35', function ($cell) use ($patrimonio_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $patrimonio_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F36', function ($cell) use ($activo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F39', function ($cell) use ($uaii_activostotales) {
                    // manipulate the cel
                    $cell->setValue('$' . $uaii_activostotales);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F40', function ($cell) use ($uai_patrimonio) {
                    // manipulate the cel
                    $cell->setValue('$' . $uai_patrimonio);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('G23', function ($cell) use ($endeudamiento_activo) {
                    // manipulate the cel
                    $cell->setValue('$' . $endeudamiento_activo);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G27', function ($cell) use ($endeudamiento_patrimordial) {
                    // manipulate the cel
                    $cell->setValue('$' . $endeudamiento_patrimordial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G31', function ($cell) use ($endeudamiento_activo_fijo) {
                    // manipulate the cel
                    $cell->setValue('$' . $endeudamiento_activo_fijo);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G35', function ($cell) use ($apalancamiento) {
                    // manipulate the cel
                    $cell->setValue('$' . $apalancamiento);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G39', function ($cell) use ($apalancamiento_financiero) {
                    // manipulate the cel
                    $cell->setValue('$' . $apalancamiento_financiero);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //GESTION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->mergeCells('B44:H45');
                $sheet->cell('B44', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GESTION');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->mergeCells('C48:C49');
                $sheet->cell('C48', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rotacion de Cartera');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C52:C53');
                $sheet->cell('C52', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rotacion de Activo Fijo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C56:C57');
                $sheet->cell('C56', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rotacion de Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C60:C61');
                $sheet->cell('C60', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Periodo medio cobranza');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C64:C65');
                $sheet->cell('C64', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Periodo medio pago');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C69:C71');
                $sheet->cell('C69', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Impacto gasto administracion y ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C74:C76');
                $sheet->cell('C74', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Impacto de la carga Financiera');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C79:C80');
                $sheet->cell('C79', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rotación de inventario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C83:C84');
                $sheet->cell('C83', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Periodo de inventario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('D48:D48');
                $sheet->cell('D48', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D49', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('cuentas por cobrar');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D52', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D53', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activos fijo neto');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D56', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D57', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Activos Total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D60', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('documentos por cobrar   * 360 dias');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D61', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D64', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cuentas y Documentos por pagar  * 360 dias');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D65', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Compras');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D70', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Gastos administracion');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D71', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D75', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Gastos financieros');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D76', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D79', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Costo de ventas o produccion');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D80', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('inventarios total promedio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D83', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('360');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D84', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rotacion de inventarios');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('E48', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E52', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E56', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E60', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E64', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E70', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E75', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E79', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E83:E84');
                $sheet->cell('E83', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F48', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F49', function ($cell) use ($documentos_cobrar) {
                    // manipulate the cel
                    $cell->setValue('$' . $documentos_cobrar);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F52', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F53', function ($cell) use ($activo_ncorriente) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_ncorriente);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F56', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F57', function ($cell) use ($activo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F60', function ($cell) use ($documentos_cobrar) {
                    // manipulate the cel
                    $cell->setValue('$' . $documentos_cobrar);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F61', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F64', function ($cell) use ($documentos_cobrar) {
                    // manipulate the cel
                    $cell->setValue('$' . $documentos_cobrar);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F65', function ($cell) use ($compras) {
                    // manipulate the cel
                    $cell->setValue('$' . $compras);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F70', function ($cell) use ($gastos_administrativos) {
                    // manipulate the cel
                    $cell->setValue('$' . $gastos_administrativos);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F71', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F75', function ($cell) use ($gastos_financieros) {
                    // manipulate the cel
                    $cell->setValue('$' . $gastos_financieros);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F76', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F79', function ($cell) use ($costos_ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $costos_ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F80', function ($cell) use ($inventarios_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $inventarios_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('F83', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('360');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F84', function ($cell) use ($rotacion_inventario) {
                    // manipulate the cel
                    $cell->setValue('$' . $rotacion_inventario);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('G48', function ($cell) use ($rotacion_cartera) {
                    // manipulate the cel
                    $cell->setValue($rotacion_cartera);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G52', function ($cell) use ($rotacion_activo) {
                    // manipulate the cel
                    $cell->setValue($rotacion_activo);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G56', function ($cell) use ($rotacion_ventas) {
                    // manipulate the cel
                    $cell->setValue($rotacion_ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G60', function ($cell) use ($periodo_de_cobranza) {
                    // manipulate the cel
                    $cell->setValue($periodo_de_cobranza);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G64', function ($cell) use ($periodo_medio_pago) {
                    // manipulate the cel
                    $cell->setValue($periodo_medio_pago);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G70', function ($cell) use ($impacto_gastoadmin) {
                    // manipulate the cel
                    $cell->setValue($impacto_gastoadmin . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G75', function ($cell) use ($impacto_carga_financiera) {
                    // manipulate the cel
                    $cell->setValue($impacto_carga_financiera . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G79', function ($cell) use ($rotacion_inventario) {
                    // manipulate the cel
                    $cell->setValue($rotacion_inventario);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G83', function ($cell) use ($periodo_inventario) {
                    // manipulate the cel
                    $cell->setValue($periodo_inventario);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B88:J89');
                $sheet->cell('B88', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RENTABILIDAD');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->mergeCells('C93:C94');
                $sheet->cell('C93', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rentabilidad Neta de Activo (Dupont)');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C97:C98');
                $sheet->cell('C97', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Margen Bruto');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C101:C102');
                $sheet->cell('C101', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Margen operacional ');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C105:C106');
                $sheet->cell('C105', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rentabilidad Neta de Ventas ( margen neto)');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C109:C111');
                $sheet->cell('C109', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rentabilidad Operacional del Patrimonio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });

                $sheet->cell('C114', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Rentabilidad Financiera');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////       $sheet->mergeCells('D48:D48');
                $sheet->mergeCells('D93:D94');
                $sheet->cell('D93', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Utilidad Neta/Ventas  *  Ventas/Activo total');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D97', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas- Costos de ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D98', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D101', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Utilidad  operacional');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D102', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D105', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Utilidad  Neta');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D106', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('D109', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Utilidad  operacional');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('D110', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Patrimonio');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('D113:D114');
                $sheet->cell('D113', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ventas/Activos * UAII/ Ventas  * Activo/Patrimonio * UAI/AUII  * Utilidad neta/UAI');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->mergeCells('E93:E94');
                $sheet->cell('E93', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('E97:E98');
                $sheet->cell('E97', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E101', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E105', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E109', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('E114', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('G93:G94');
                $sheet->cell('G93', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('*');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('F93', function ($cell) use ($utilidad_neta) {
                    // manipulate the cel
                    $cell->setValue('$' . $utilidad_neta);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F94', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('H93', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('H94', function ($cell) use ($activo_total) {
                    // manipulate the cel
                    $cell->setValue('$' . $activo_total);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('I93', function ($cell) use ($rentabilidad_neta) {
                    // manipulate the cel
                    $cell->setValue($rentabilidad_neta . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F97', function ($cell) use ($costosmenosventa) {
                    // manipulate the cel
                    $cell->setValue('$' . $costosmenosventa);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F98', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->mergeCells('G97:G98');
                $sheet->cell('G97', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('=');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });
                $sheet->cell('H97', function ($cell) use ($margen_bruto) {
                    // manipulate the cel
                    $cell->setValue($margen_bruto . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('F101', function ($cell) use ($utilidad_operacional) {
                    // manipulate the cel
                    $cell->setValue('$' . $utilidad_operacional);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F102', function ($cell) use ($ventas) {
                    // manipulate the cel
                    $cell->setValue('$' . $ventas);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('G101', function ($cell) use ($margen_operacional) {
                    // manipulate the cel
                    $cell->setValue($margen_operacional . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('F105', function ($cell) use ($utilidad_operacional) {
                    // manipulate the cel
                    $cell->setValue('$' . $utilidad_operacional);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F106', function ($cell) use ($patrimonio_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $patrimonio_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('G105', function ($cell) use ($rentabilidad_netav) {
                    // manipulate the cel
                    $cell->setValue($rentabilidad_netav . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $sheet->cell('F109', function ($cell) use ($utilidad_operacional) {
                    // manipulate the cel
                    $cell->setValue('$' . $utilidad_operacional);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F110', function ($cell) use ($patrimonio_neto) {
                    // manipulate the cel
                    $cell->setValue('$' . $patrimonio_neto);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');

                });

                $sheet->cell('G109', function ($cell) use ($rentabilidad_op) {
                    // manipulate the cel
                    $cell->setValue($rentabilidad_op . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F114', function ($cell) use ($rentabilidad_fin_total) {
                    // manipulate the cel
                    $cell->setValue($rentabilidad_fin_total . '%');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // DETALLES

                $sheet->setColumnFormat(array(
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                //  CONFIGURACION FINAL
                $sheet->cells('B1:H1', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('B4:H5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('22');
                });
                $sheet->cells('B7:H7', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('B21:H21', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('B88:H89', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('B44:H45', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#E16605');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C9:C10', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C16:C18', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C13:C14', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C16:C18', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C23:C24', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C27:C28', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C31:C32', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C35:C36', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C39:C40', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C48:C49', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C52:C53', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C56:C57', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C60:C61', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C64:C65', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C69:C71', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C74:C76', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C79:C80', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C83:C84', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C93:C94', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C97:C98', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C101:C102', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C105:C106', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C109:C110', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('C114:C114', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#F8FFB2');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cells('G9:G9', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G13:G13', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('F17:F17', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G23:G23', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G9:G9', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G27:G27', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G31:G31', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G35:G35', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G39:G39', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G48:G48', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G52:G52', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G56:G56', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G60:G60', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G64:G64', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G70:G70', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G75:G75', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G79:G79', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G83:G83', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G101:G101', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G105:G105', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('G109:G109', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('I93:I93', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('H97:H97', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cells('F114:F114', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#9DCD8F');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('B8:B18', function ($cell) {
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->cell('B19', function ($cell) {
                    $cell->setBorder('', '', 'thin', 'thin');
                });

                $sheet->cell('C19:G19', function ($cell) {
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('H8:H18', function ($cell) {
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->cell('H19', function ($cell) {
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                //SOLVENCIA
                $sheet->cell('B22:B41', function ($cell) {
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->cell('B42', function ($cell) {
                    $cell->setBorder('', '', 'thin', 'thin');
                });

                $sheet->cell('C42:G42', function ($cell) {

                    $cell->setBorder('', '', 'thin', '');

                });

                $sheet->cell('H21:H41', function ($cell) {
                    $cell->setBorder('', 'thin', '', '');
                });
                $sheet->cell('H42', function ($cell) {
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                //GESTION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sheet->cell('B46:B85', function ($cell) {
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->cell('B86', function ($cell) {
                    $cell->setBorder('', '', 'thin', 'thin');
                });

                $sheet->cell('C86:G86', function ($cell) {
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('H46:H85', function ($cell) {
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->cell('H86', function ($cell) {
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $sheet->cell('B90:B115', function ($cell) {
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->cell('B116', function ($cell) {
                    $cell->setBorder('', '', 'thin', 'thin');
                });

                $sheet->cell('C116:I116', function ($cell) {
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('J90:J115', function ($cell) {
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->cell('J116', function ($cell) {
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 5,
                    'C' => 28,
                    'D' => 50,
                    'E' => 10,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                    'I' => 20,
                    'J' => 20,
                ));

            });
            $excel->getActiveSheet()->getStyle('C10:C11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C13:C14')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C16:C18')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C27:C28')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C31:C32')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C39:C40')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C69:C71')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C74:C76')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C93:C94')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C105:C106')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C109:C111')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D1:D114')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }

    public function activos($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $saldo2              = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '1.01';
            //activo corriente
        } else if ($referencia == 2) {
            $condicion = '1.02'; //activo no corriente
            //plan de cuentas desde donde hasta donde
        } elseif ($referencia == 3) {
            $condicion = '1'; //activo no corriente
            //plan de cuentas desde donde hasta donde
        }

        $saldo   = 0;
        $saldo2  = 0;
        $plan    = Plan_Cuentas::find($condicion);
        $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $condicion . '%')
            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->where('c.id_empresa', $id_empresa)
            ->where('ct_asientos_detalle.estado', '<>', 0);
        if (strpos($plan->nombre, '-)') == false) {
            $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
            $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $condicion . '%')
                ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                ->where('c.id_empresa', $id_empresa)
                ->where('p.nombre', 'like', '%-)%')
                ->where('ct_asientos_detalle.estado', '<>', 0)
                ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                ->get();
            foreach ($asiento2 as $row) {
                $saldo2 = $row->saldo;
            }
        }
        $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
            ->get();
        foreach ($asiento as $row) {
            $saldo += $row->saldo - $saldo2;
        }

        return $saldo;
        //return 'no';
    }
    public function pasivos($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '2.01.';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();

            $saldo = 0;

            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('c.fecha_asiento', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"]);
                        if ($plan->naturaleza_2 == 1) {
                            $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                                ->get();
                        } else {
                            $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                                ->get();
                        };
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('c.fecha_asiento', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"]);
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

                }
            }
            return $saldo;
        } elseif ($referencia == 2) {
            $condicion = '2.02.'; //pasivo no corriente
            //plan de cuentas desde donde hasta donde
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {

                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo;
                    }

                }
            }
            return $saldo;
        } elseif ($referencia == 3) {
            $condicion = '2.'; //activo no corriente
            //plan de cuentas desde donde hasta donde
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {

                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function patrimonio_neto($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '3.';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }

            return $saldo;
        }
        return 'no';
    }
    public function gastos($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '5.2.01.';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)

                ->where('id_padre', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }

    public function documentos_cobrar($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '1.01.02.05.0';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)

                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        } elseif ($referencia == 2) {
            $condicion = '4.1.01';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {

                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }

    public function documentos_pagar($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '2.01.03';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        } elseif ($referencia == 2) {
            $condicion = '5.1.0';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }
                }
            }
            return $saldo;
        }
        return 'no';
    }

    public function gastos_operacionales($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '5.2.01';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function intereses($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '4.1.03';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }
                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function gastos_financieros($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '5.2.03.';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function gastos_administracion($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '5.2.02';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function costos_ventas($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activo              = 0;
        $activos[]           = null;
        $saldo               = 0;
        $activo_no_corriente = 0;
        if ($referencia == 1) {
            $condicion = '5.1.01';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        } elseif ($referencia == 2) {
            $condicion = '4.1.07.01';
            //costos venta
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {

                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo;
                    }

                }
            }
            return $saldo;
        } elseif ($referencia == 3) {
            $condicion = '4.1.01';
            //ventas
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {

                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }
    public function inventario_total($fecha_desde, $fecha_hasta, $id_empresa, $referencia)
    {
        $activos[] = null;
        $saldo     = 0;
        if ($referencia == 1) {
            $condicion = '1.01.03.01';
            //pasivo corriente
            $plans = Plan_Cuentas::where('estado', '<>', 0)

                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre')
                ->orderBy('id', 'asc')
                ->get();
            $saldo = 0;
            foreach ($plans as $plan) {
                $data = array();
                if ($plan->id != "") {
                    $saldo2  = 0;
                    $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', '<>', 0);
                    if (strpos($plan->nombre, '-)') == false) {
                        $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                        $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                            ->where('c.id_empresa', $id_empresa)
                            ->where('p.nombre', 'like', '%-)%')
                            ->where('ct_asientos_detalle.estado', '<>', 0)
                            ->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                        foreach ($asiento2 as $row) {
                            $saldo2 = $row->saldo;
                        }
                    }
                    $asiento = $asiento->whereBetween('fecha', [$fecha_desde . "-01-01 " . " 00:00:00", $fecha_hasta . "-12-31 " . " 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();

                    foreach ($asiento as $row) {
                        $saldo += $row->saldo - $saldo2;
                    }

                }
            }
            return $saldo;
        }
        return 'no';
    }

}
