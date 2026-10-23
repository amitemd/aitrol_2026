@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
    p.s1 {
        margin-left: 10px;
        font-size: 12px;
        font-weight: bold;
    }

    p.s2 {
        margin-left: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    p.s3 {
        margin-left: 30px;
        font-size: 10px;
        font-weight: bold;
    }

    p.s4 {
        margin-left: 40px;
        font-size: 10px;
    }

    p.t1 {
        font-size: 14px;
        font-weight: bold;
    }

    p.t2 {
        font-size: 12px;
        font-weight: bold;
    }

    p.t3 {
        font-size: 10px;
    }

    .table-striped>thead>tr>th>td,
    .table-striped>tbody>tr>th>td,
    .table-striped>tfoot>tr>th>td,
    .table-striped>thead>tr>td,
    .table-striped>tbody>tr>td,
    .table-striped>tfoot>tr>td {
        padding: 0.5px;
        line-height: 1;
    }

    .secundario {
        left: 10px;
    }

    .table {
        margin-bottom: -10px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .hidden-paginator {

        display: none;

    }

    .removethe {
        display: none;
    }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.ventas')}}</a></li>
        </ol>
    </nav>
    <div class="box dobra" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 header">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>
        <div class="box-body ">
            <form method="POST" id="reporte_master" action="{{ route('venta.informe_ventas_mensual') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" required>
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="nombre_proveedor">{{trans('contableM.cliente')}}: </label>
                </div>
                <div class="form-group col-md-4 col-xs-4 container-4">
                    <select name="id_cliente" id="id_cliente" class="form-control select2_cuentas" style="width: 100%;">
                        <option value="">Seleccione...</option>
                        <?php

                        use Illuminate\Support\Facades\DB;

                        if (isset($clientes)) {
                            foreach ($clientes as $value) {
                        ?>
                                <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="nombre_proveedor">{{trans('contableM.secuencia')}}: </label>
                </div>
                <div class="form-group col-md-4 col-xs-4 container-4">
                    <input type="hidden" name="excelF" id="excelF" value="0">
                    <input type="text" class="form-control" name="secuencia" id="secuencia" value="{{isset($secuencia)?$secuencia:''}}" placeholder="Ingrese secuencia de factura ---">
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="button" class="btn btn-primary" onclick="buscarf();" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printDiv()">
                        <span class="glyphicon glyphicon-print" aria-hidden="true"></span> {{trans('contableM.Imprimir')}}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('contableM.Exportar')}}
                    </button>
                </div>
                <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
                </div>
            </form>
        </div>
        <form method="POST" id="print_reporte_master" action="{{ route('venta.excel_ventas') }}" target="_blank">
            {{ csrf_field() }}
            <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{isset($desde)?$desde:''}}">
            <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{isset($hasta)?$hasta:''}}">
            <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{isset($id_cliente)?$id_cliente:''}}">
            <input type="hidden" name="secuencia2" id="secuencia2" value="{{isset($secuencia)?$secuencia:''}}">
            <input type="hidden" name="tipo2" id="tipo2" value="{{isset($tipo)?$tipo:''}}">
        </form>

        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                        </div>
                        <div class="box-body">
                            <div class="col-md-1">
                                <dl>
                                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                                </dl>
                            </div>
                            <div id="imprimir">
                                <div class="col-md-3">
                                    <dl>
                                        <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                                        <dd>&nbsp; {{$empresa->id}}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <h4 style="text-align: center;">INFORME DE VENTAS MENSUAL</h4>
                                    <h5 style="text-align: center;"> @if(($desde!=null)) Desde {{date("d-m-Y", strtotime($desde))}} - Hasta {{date("d-m-Y", strtotime($hasta))}} @else AL - {{date("d-m-Y", strtotime($hasta))}} @endif</h5>
                                </div>
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-12">

                                </div>
                                <div class="col-md-12 table-responsive" style="margin-top: 10px;">
                                    <?php
                                    $total_anio = 0;
                                    $tablaMes = '';
                                    $tablaCabecera = '<table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;margin-top: 10px;">
                                    <thead>
                                    <tr class="well-dark">
                                    <th width="6.25%" tabindex="0" aria-controls="example2" colspan="1">Mes</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Subtotal 0</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Subtotal 12</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.descuento') . '</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.subtotal') . '</th>
                                    <!--<th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.impuesto') . '</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas Brutas</th>-->
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total NCA</th>
                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas Netas </th>
                                    </tr>
                                    </thead>';
                                    if (isset($periodo) && count($periodo) > 0) {
                                        foreach ($periodo as $peri) {
                                            $fecha_periodo = explode('-', $peri->fecha);
                                            $anio = $fecha_periodo[0] . '-' . $fecha_periodo[1];
                                            $tablaMes .= '<table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;margin-top: 10px;width:100%">
                                            <thead>
                                                <tr class="well-dark">
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">' . trans('contableM.fecha') . '</th>
                                                    <th width="7.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">' . trans('contableM.numero') . '</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">CI</th>
                                                    <th width="8.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.cliente') . '</th>
                                                    <th width="5.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.tipo') . '</th>
                                                    <th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.detalle') . '</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Subtotal 0</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SUbtotal 12</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.descuento') . '</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.subtotal') . '</th>
                                                    <!--<th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.impuesto') . '</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas Brutas</th>-->
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total NCA</th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas Netas </th>
                                                    <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">' . trans('contableM.estado') . '</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $final_credito = 0;
                                            $periodo = '';
                                            $total_mes = 0;
                                            $totales = 0;
                                            $subtotal = 0;
                                            $subtotal0 = 0;
                                            $subtotal12 = 0;
                                            $subtotalTotal = 0;
                                            $descuento = 0;
                                            $impuesto = 0;
                                            $total_ventas_netas = 0;
                                            foreach ($informe as $key => $info) {
                                                //echo '<pre>';print_r($info);exit;
                                                $fecha_venta = explode('-', $info->fecha);
                                                $anio_venta = $fecha_venta[0] . '-' . $fecha_venta[1];
                                                $subtotalIndividual = 0.00;
                                                $n_credito = 0.00;
                                                if (isset($informe) && count($informe) > 0) {
                                                    if ($anio == $anio_venta) {
                                                        $notasCredito = Sis_medico\Ct_Nota_Credito_Clientes::where('id_factura', $info->id)->get();
                                                        if ($info->estado == 1) {
                                                            $subtotal = $subtotal + $info->subtotal_total;
                                                            $subtotal0 = $subtotal0 + $info->subtotal_0;
                                                            $subtotal12 = $subtotal12 + $info->subtotal_12;
                                                            $totales = $totales + $info->total_final;
                                                            $total_mes = $total_mes + $info->total_final;
                                                            $descuento = $descuento + $info->descuento;
                                                            $impuesto = $impuesto + $info->impuesto;
                                                            $subtotalIndividual = $info->subtotal_0 + $info->subtotal_12 - $info->descuento;
                                                        }
                                                        $subtotalTotal = $subtotalTotal + $subtotalIndividual;

                                                        foreach ($notasCredito as $notaCredito) {
                                                            if (!is_null($notaCredito)) {
                                                                $n_credito = $n_credito + $notaCredito->total_credito;
                                                            }
                                                        }

                                                        $final_credito = $final_credito + $n_credito;
                                                        $ventas_netas = $subtotalIndividual - $n_credito ;

                                                        $total_ventas_netas = $total_ventas_netas + $ventas_netas;
                                                        $tablaMes .= '<tr style="text-align: center;">
                                                        <td width="6.25%" colspan="1">' . $info->fecha . '</td>
                                                        <td width="7.25%" colspan="1">' . $info->nro_comprobante . '</td>
                                                        <td width="6.25%" colspan="1">' . $info->ruc_id_cliente . '</td>
                                                        <td width="8.25%" colspan="1">' . $info->nombre_cliente . '</td>
                                                        <td width="5.25%" colspan="1">' . $info->tipo . '</td>
                                                        <td width="10%" colspan="1">' . $info->concepto . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($info->subtotal_0, 2, '.', ',') . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($info->subtotal_12, 2, '.', ',') . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($info->descuento, 2, '.', ',') . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($subtotalIndividual, 2, '.', ',') . '</td>
                                                        <!--<td align="right" width="6.25%" colspan="1"> $' . number_format($info->impuesto, 2, '.', ',') . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($info->total_final, 2, '.', ',') . '</td>-->
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($n_credito, 2, '.', ',') . '</td>
                                                        <td align="right" width="6.25%" colspan="1"> $' . number_format($ventas_netas, 2, '.', ',') . ' </td>';

                                                        if ($info->estado == 1)
                                                            $tablaMes .= '<td align="center" width="6.25%" colspan="1">Activo</td>';
                                                        else
                                                            $tablaMes .= '<td align="center" width="6.25%" colspan="1">Inactivo</td>';
                                                        $tablaMes .= '</tr>';
                                                    }
                                                }
                                            }
                                            $total_anio = $total_anio + $total_mes;
                                            $tablaMes .= '</tbody><tfoot>';
                                            //dd($tablaMes);
                                            $tablaMes .= '<tr>
                                            <td width="6.25%" style="font-weight:bold;text-align:center;" colspan="3">
                                                <label>' . trans('contableM.total') . '</label>';
                                            $mesTabla = '';
                                            switch ($fecha_periodo[1]) {
                                                case '01':
                                                    $tablaMes .= ' Enero: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Enero - ' . $fecha_periodo[0];
                                                    break;
                                                case '02':
                                                    $tablaMes .= ' Febrero: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Febrero - ' . $fecha_periodo[0];
                                                    break;
                                                case '03':
                                                    $tablaMes .= ' Marzo: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Marzo - ' . $fecha_periodo[0];
                                                    break;
                                                case '04':
                                                    $tablaMes .= ' Abril: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Abril - ' . $fecha_periodo[0];
                                                    break;
                                                case '05':
                                                    $tablaMes .= ' Mayo: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Mayo - ' . $fecha_periodo[0];
                                                    break;
                                                case '06':
                                                    $tablaMes .= ' Junio: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Junio - ' . $fecha_periodo[0];
                                                    break;
                                                case '07':
                                                    $tablaMes .= ' Julio: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Julio - ' . $fecha_periodo[0];
                                                    break;
                                                case '08':
                                                    $tablaMes .= ' Agosto: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Agosto - ' . $fecha_periodo[0];
                                                    break;
                                                case '09':
                                                    $tablaMes .= ' Septiembre: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Septiembre - ' . $fecha_periodo[0];
                                                    break;
                                                case '10':
                                                    $tablaMes .= ' Octubre: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Octubre - ' . $fecha_periodo[0];
                                                    break;
                                                case '11':
                                                    $tablaMes .= ' Noviembre: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Noviembre - ' . $fecha_periodo[0];
                                                    break;
                                                default:
                                                    $tablaMes .= ' Diciembre: $ ' . number_format($total_mes, 2, '.', ',');
                                                    $mesTabla = 'Diciembre - ' . $fecha_periodo[0];
                                                    break;
                                            }
                                            $tablaCabecera .= '<tbody>
                                            <tr>
                                                <td width="10%" style="font-weight: bold;text-align:left; ">
                                                   ' . $mesTabla . '
                                                </td>
                                                <td width="10%" style="text-align:right; ">
                                                    <p> $' . number_format($subtotal0, 2, '.', ',') . '</p>
                                                </td>
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($subtotal12, 2, '.', ',') . '</p>
                                                </td>
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($descuento, 2, '.', ',') . '</p>
                                                </td>
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($subtotalTotal, 2, '.', ',') . '</p>
                                                </td>
                                               <!-- <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($impuesto, 2, '.', ',') . '</p>
                                                </td>
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($totales, 2, '.', ',') . '</p>
                                                </td>-->
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($final_credito, 2, '.', ',') . '</p>
                                                </td>
                                                <td width="6.25%" style="text-align:right;">
                                                    <p> $' . number_format($total_ventas_netas, 2, '.', ',') . '</p>
                                                </td>
                                            </tr>
                                        </tbody>';
                                            $tablaMes .= '</td>
                                            <td width="7.25%"></td>
                                            <td width="8.25%"></td>
                                            <td width="5.25%" style="font-weight:bold;text-align:center;"></td>
                                            <td width="10%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($subtotal0, 2, '.', ',') . '</p>
                                            </td>
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($subtotal12, 2, '.', ',') . '</p>
                                            </td>
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($descuento, 2, '.', ',') . '</p>
                                            </td>
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($subtotalTotal, 2, '.', ',') . '</p>
                                            </td>
                                            <!--<td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($impuesto, 2, '.', ',') . '</p>
                                            </td>
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($totales, 2, '.', ',') . '</p>
                                            </td>-->
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($final_credito, 2, '.', ',') . '</p>
                                            </td>
                                            <td width="6.25%" style="text-align:right; font-weight: bold; ">
                                                <p> $' . number_format($total_ventas_netas, 2, '.', ',') . '</p>
                                            </td>
                                            
                                            </tr>';
                                            $tablaMes .= '</tfoot>
                                            </table>';
                                        }
                                        $tablaMes .= '<label style="font-size: 16px;"> ' . trans('contableM.total') . ': ' . number_format($total_anio, 2, '.', ',') . '</label>';
                                    }
                                    echo $tablaCabecera . '</table><br/>' . $tablaMes;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
        $('#fact_contable_check').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
    });
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('div.paginationLinks').remove();
            }
        });
    });
    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    $('#cuenta').on('select2:select', function(e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
    });
    $('#nombre').on('select2:select', function(e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
    });
    $("#btn_imprimir").click(function() {
        $("#print_reporte_master").submit();
        // document.getElementById("print_reporte_master").submit();
    });
    $(document).ready(function() {
        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
    });

    function excel() {
        $("#excelF").val(1);
        $("#reporte_master").submit();
    }

    function buscarf() {
        $("#excelF").val(0);
        $("#reporte_master").submit();
    }
    $("#nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_buscar_nombreproveedor')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
    });
    $('#fact_contable_check').on('ifChanged', function(event) {
        //aqui funciona si cambio el input time
        if ($(this).prop("checked")) {
            $("#esfac_contable").val(1);
            $("#es_fact_dos").val(1);
        } else {
            $("#esfac_contable").val(0);
        }
    });

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedornombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#id_proveedor').val(data.value);
                    $('#id_proveedor2').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                } else {
                    $('#id_proveedor').val("");
                    $('#id_proveedor2').val("");
                    $('#direccion_proveedor').val("");
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function printDiv(nombreDiv) {
        var contenido = document.getElementById("imprimir").innerHTML;
        var contenidoOriginal = document.body.innerHTML;
        document.body.innerHTML = contenido;
        window.print();
        document.body.innerHTML = contenidoOriginal;
    }
    $(function() {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{isset($desde)?$desde:""}}',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{isset($hasta)?$hasta:""}}',
        });
        $("#fecha_desde").on("dp.change", function(e) {
            verifica_fechas();
        });
        $("#fecha_hasta").on("dp.change", function(e) {
            verifica_fechas();
        });
    });

    function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }

    function tipo_factura() {
        var tipo = $("#tipo").val();
        if (isNaN(tipo)) {
            tipo = 0;
        }
        $("#tipo2").val(tipo);
    }

    function verifica_fechas() {
        if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Verifique el rango de fechas y vuelva consultar'
            });
        }
    }
</script>
@endsection