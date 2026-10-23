<?php

use Sis_medico\Ct_ventas;

$xml = $comprobante->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante;
$comp = simplexml_load_string($xml);
$venta = Ct_ventas::where('nro_autorizacion', $comp->docsSustento->docSustento->numAutDocSustento)->first();
?>
<style type="text/css">
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

    #datos_a {
        text-align: center;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('retencion.cliente') }}";
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Clientes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">Retenciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visualizar Retención</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-8 col-sm-8 col-5">
                        <div class="box-title "><b>VISUALIZADOR CLIENTES-COMP. DE RETENCIONES</b></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body  dobra">
            <form class="form" action="" id="form_guardado" method="post">
                {{ csrf_field() }}
                <div class="header row">
                    <input type="text" name="cont" id="cont" value="1" class="hidden">
                    <div class="form-group col-md-12 ">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label ">DATOS DE LA RETENCIÓN</label>
                            </div>
                            <div class="col-md-1 px-1">
                                <label class="label_header" style="padding-left: 0px">Establecimiento</label>
                                <input class="col-md-12 col-xs-12 form-control" id="establecimiento" name="establecimiento" value="{{$comp->infoTributaria->estab}}" readonly>
                            </div>
                            <div class="col-md-1 px-1">
                                <label class="control-label label_header">Emision</label>
                                <input type="text" class="form-control text-right" name="emision" value="{{$comp->infoTributaria->ptoEmi}}" id="emision" readonly>
                            </div>
                            <div class="col-md-1 px-1">
                                <label class="control-label label_header">Secuencial</label>
                                <input type="text" class="form-control  text-right" name="secuencial" value="{{$comp->infoTributaria->secuencial}}" id="secuencial" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header">Fecha autorización</label>
                                <input type="text" class="form-control " value="{{date('d/m/Y H:i:s',strtotime($comprobante->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion))}}" name="fecha_autorizacion" id="fecha_autorizacion" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header">Ambiente </label>
                                <input type="text" class="form-control " value="{{$comprobante->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente}}" name="valor_factura" id="valor_factura" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header">Ruc cliente</label>
                                <input type="text" class="form-control " name="id_cliente" value="{{$venta->id_cliente}}" id="id_cliente" readonly>
                            </div>
                            <div class="col-md-3 px-1">
                                <label class="control-label label_header">Cliente</label>
                                <input type="text" class="form-control " name="cliente" id="cliente" value="{{$comp->infoTributaria->razonSocial}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12 ">
                        <div class="row">
                            <div class="col-md-5 px-1">
                                <label class="control-label label_header">N° autorización Retención</label>
                                <input type="text" class="form-control " name="nro_autorizacion" id="nro_autorizacion" value="{{$comprobante->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion}}" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header">Fecha emision</label>
                                <input type="text" class="form-control " name="fecha_cambio" id="fecha_cambio" value="{{$comp->infoCompRetencion->fechaEmision}}" readonly>
                            </div>
                            <div class="col-md-5 px-1">
                                <label class="control-label label_header">Concepto</label>
                                <input type="text" class="form-control " name="concepto" value="" id="concepto">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12 px-1">
                        <table id="example2" style="width: 100%;" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead class="well-dark">
                                <tr style="position: relative;">
                                    <th>NÚMERO FACTURA</th>
                                    <th>FECHA</th>
                                    <th>BASE IMP RET</th>
                                    <th>TIPO</th>
                                    <th>COD</th>
                                    <th>% DE RET</th>
                                    <th>VALOR RETENIDO</th>
                                </tr>
                            </thead>
                            <tbody id="datos_a">
                                <?php
                                $totalRenta = 0;
                                $totalIva = 0;
                                $totalISD = 0;
                                if (isset($comp->docsSustento->docSustento->retenciones->retencion) && count($comp->docsSustento->docSustento->retenciones->retencion) > 0) {
                                    foreach ($comp->docsSustento->docSustento->retenciones->retencion as $row) {
                                        $tipo = '';
                                        if ($row->codigo == 1) {
                                            $tipo = 'RENTA';
                                            $totalRenta += (float)$row->valorRetenido;
                                        } elseif ($row->codigo == 2) {
                                            $tipo = 'IVA';
                                            $totalIva += (float)$row->valorRetenido;
                                        } else {
                                            $tipo = 'ISD';
                                            $totalISD += (float)$row->valorRetenido;
                                        }
                                        echo '<tr>
                                        <td>' . $comp->docsSustento->docSustento->numDocSustento . '</td>
                                        <td>' . $comp->docsSustento->docSustento->fechaEmisionDocSustento . '</td>
                                        <td style="text-align:right">' . number_format((float)$row->baseImponible, 2) . '</td>
                                        <td>' . $tipo . '</td>
                                        <td>' . $row->codigoRetencion . '</td>
                                        <td>' . $row->porcentajeRetener . '</td>
                                        <td style="text-align:right">' . number_format((float)$row->valorRetenido, 2) . '</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" name="id_rentecion" id="id_retencion" value="">
                    <input type="hidden" name="cuenta_renta" id="cuenta_renta">
                    <input type="hidden" name="cuenta_iva" id="cuenta_iva">
                    <input type="hidden" name="eliminados" id="eliminados" value="0">
                    <input type="hidden" name="id_proveedor" id="id_proveedor">
                    <input type="hidden" name="id_compra" id="id_compra" value="0">
                    <input type="hidden" name="id_fact_contable" id="id_fact_contable" value="0">
                    <div class="col-md-12" style="margin-top: 10px;">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-3">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-3 px-0">
                                <label class="label_header">Total RFIR</label>
                                <input class="form-control text-right" value="{{number_format($totalRenta,2)}}" type="text" name="total_rfirt" id="total_rfirt" readonly>
                            </div>
                            <div class="form-group col-md-3 px-0">
                                <label for="total_abonos" class="label_header">Total RFIVA</label>
                                <input class="form-control text-right" value="{{number_format($totalIva,2)}}" type="text" name="total_rfivat" id="total_rfivat" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:20px">
                        <label class="control-label">DETALLE DE DEUDAS DEL PROVEEDOR</label>
                        <input type="hidden" name="total_factura" id="total_factura">
                    </div>
                    <div class="col-12 ">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                <thead class='well-dark'>
                                    <tr style="position: relative;">
                                        <th style="width: 8%; text-align: center;">Vence</th>
                                        <th style="width: 10%; text-align: center;">Tipo</th>
                                        <th style="width: 10%; text-align: center;">Número</th>
                                        <th style="width: 10%; text-align: center;">Concepto</th>
                                        <th style="width: 6%; text-align: center;">Div</th>
                                        <th style="width: 6%; text-align: center;">Saldo</th>
                                        <th style="width: 6%; text-align: center;">Abono</th>
                                        <th style="width: 6%; text-align: center;">Saldo Base</th>
                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    {{--@php $cont=$retenciones->valor_fuente+$retenciones->valor_iva; $tot=0; if(isset($retenciones->ventas)){ $tot= $retenciones->ventas->total_final - $cont; } @endphp--}}
                                    <?php
                                    $tot = 0;
                                    $cont = 0;
                                    $fechaVence = date('Y-m-d', strtotime($venta->fecha . ' + ' . (int)$venta->dias_plazo . ' days'));
                                    $totalAbono = $totalRenta + $totalIva + $totalISD;
                                    $saldo = $venta->total_final - $totalAbono;
                                    ?>
                                    <tr class="well">
                                        <!--AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                        <td> <input class="form-control input-sm" type="text" name="vence{{$tot}}" value="{{$fechaVence}}" id="vence{{$tot}}" readonly> </td>
                                        <td> <input class="form-control input-sm" type="text" name="tipo{{$tot}}" value="{{$venta->tipo}}" id="tipo{{$tot}}" readonly> </td>
                                        <td> <input class="form-control input-sm" style="width: 100%; text-align: center" type="text" name="numero{{$tot}}" value="{{$venta->nro_comprobante}}" id="numero{{$tot}}" readonly> </td>
                                        <td> <input class="form-control input-sm" type="text" name="concepto{{$tot}}" value="{{$venta->concepto}}" id="concepto{{$tot}}" readonly> </td>
                                        <td> <input class="form-control input-sm" style="background-color: #c9ffe5; " type="text" name="div{{$tot}}" id="div{{$tot}}" value="$" readonly> </td>
                                        <td> <input class="form-control input-sm" style="background-color: #c9ffe5;width: 100%; text-align: center" type="text" name="saldo{{$tot}}" value="{{$venta->total_final}}" id="saldo{{$tot}}" readonly> </td>
                                        <td> <input class="form-control input-sm" style="background-color: #c9ffe5;  text-align: center;" value="{{number_format($totalAbono,2)}}" type="text" name="abono{{$tot}}" id="abono{{$tot}}" readonly></td>
                                        <td> <input class="form-control input-sm" style="text-align: center; width: 85%;" type="text" value="{{number_format($saldo,2)}}" name="abono_base{{$tot}}" id="abono_base{{$tot}}" readonly> </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-row" align="center">
                            <div class="form-group col-md-12 px-4">
                                <a href="javascript:;" id="boton_guardar" onclick="guardar_retenciones()" class="btn btn-success">Guardar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    function guardar_retenciones() {
        if ($('#form_guardado').valid()) {
            $("#boton_guardar").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url: "{{route('retenciones.clientes.newstoreSRI')}}",
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    //console.log(data);
                    if (data.error == 'no') {
                        $("#boton_guardar").attr("disabled", "disabled");
                        swal("Correcto!", "Retencion guardada correctamente", "success");
                        //url = "{{ url('contable/compra/comprobante/retenciones/')}}/" + data.id;
                        //window.open(url, '_blank');
                        $('#form_guardado input').attr('readonly', 'readonly');
                    } else {
                        swal("Advertencia", data.error, "warning");
                    }
                },
                error: function(data) {
                    //console.log(data);
                }
            })
        }
    }
</script>