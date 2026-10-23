@extends('contable.retenciones.base')
@section('action-content')
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
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Proveedores</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">Retenciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visualizar Retención</li>
        </ol>
    </nav>

    <form class="form-vertical" method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-5">
                            <div class="box-title "><b>VISUALIZADOR ACREEDORES-COMP. DE RETENCIONES</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$retenciones->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$retenciones->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>    
                                <button type="button" onclick="goBack()" class="btn btn-success btn-xs btn-gray" style="padding: 7px 27px">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body  dobra">


                <form class="form" action="" id="form_guardado" method="post">

                    <div class="header row">
                        <input type="text" name="cont" id="cont" value="1" class="hidden">


                        <div class="form-group col-md-12 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label ">DATOS DE LA FACTURA</label>
                                </div>
                                <div class="col-md-3 col-xs-2 px-1">
                                    <label class="label_header" style="padding-left: 0px">Estado</label>
                                    <input class="col-md-12 col-xs-12 form-control" style="@if(!is_null($retenciones))@if(($retenciones->estado!=0)) background-color: green; @else background-color: red;  @endif @endif">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="control-label label_header">SECUENCIAL</label>
                                    <input type="text" class="form-control " name="secuencial" value="@if(!is_null($retenciones)) {{$retenciones->secuencia}} @endif" id="secuencial" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="control-label label_header">ID_ASIENTO</label>
                                    <input type="text" class="form-control " name="id_asiento" value="@if(!is_null($retenciones)) {{$retenciones->id_asiento_cabecera}} @endif" id="id_asiento" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="control-label label_header">N° VALOR DE LA FACTURA </label>
                                    <input type="text" class="form-control " value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->total_final}} @endif" name="valor_factura" id="valor_factura" readonly>
                                </div>
                                <div class="col-md-3 px-1">
                                    <label class="control-label label_header">Asiento </label>
                                    <input type="text" class="form-control " value="@if(isset($retenciones)) {{$retenciones->id_asiento_cabecera}} @endif" name="valor_factura" id="valor_factura" readonly>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">CLIENTE</label>
                                    <input type="text" class="form-control " name="proveedor_modal" value="@if(!is_null($retenciones)) {{$retenciones->id_cliente}} @endif" id="proveedor_modal" readonly>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">FECHA AUTORIZACION</label>
                                    <input type="date" class="form-control " name="fecha_aut" id="fecha_aut" value="@if(!is_null($retenciones)){{$retenciones->fecha}}@endif">
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">CONCEPTO</label>
                                    <input type="text" class="form-control " name="concepto" value="@if(isset($retenciones)) {{$retenciones->descripcion}} @endif" id="concepto">
                                </div>
                                <div class="col-md-6 px-1">
                                    <label class="control-label label_header">N° AUTORIZACIÓN DE RETENCIÓN</label>
                                    <input type="text" class="form-control " name="nro_autorizacion" id="nro_autorizacion" value="@if(!is_null($retenciones)) {{$retenciones->autorizacion}} @endif">
                                </div>
                                <div class="col-md-3 px-1">
                                    <label class="control-label label_header">FECHA</label>
                                    <input type="date" class="form-control " name="fecha_cambio" id="fecha_cambio" value="{{$retenciones->fecha}}" onchange="cambio()">
                                </div>



                            </div>
                        </div>
                        <div class="table-responsive col-md-12 px-1">
                            <table id="example2" style="width: 100%;" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                <thead class="well-dark">
                                    <tr style="position: relative;">
                                        <th>NÚMERO DE REF</th>
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
                                    @if(isset($detalles))
                                    @foreach($detalles as $value )

                                    <tr>
                                        <td>@if(!is_null($value->numerorefs)){{$value->numerorefs}} @endif</td>
                                        <td>@if(!is_null($value->numerorefs)){{$value->numerorefs}} @endif</td>
                                        <td>@if(!is_null($value->fechaauto)) {{$value->fechaauto}} @else &nbsp; @endif</td>
                                        <td>{{$value->base_imponible}}</td>
                                        <td>@if(!is_null($value->tipo)){{$value->tipo}} @endif</td>
                                        <td>@if(isset($value->porcentajer)){{$value->porcentajer->codigo}} @endif</td>
                                        <td>@if(isset($value->porcentajer)){{$value->porcentajer->valor}} % @endif</td>
                                        <td>{{$value->totales}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <input type="hidden" name="id_rentecion" id="id_retencion" value = {{$id}}>
                        <input type="hidden" name="cuenta_renta" id="cuenta_renta">
                        <input type="hidden" name="cuenta_iva" id="cuenta_iva">
                        <input type="hidden" name="eliminados" id="eliminados" value="0">
                        <input type="hidden" name="id_proveedor" id="id_proveedor">
                        <input type="hidden" name="id_compra" id="id_compra" value="0">
                        <input type="hidden" name="id_fact_contable" id="id_fact_contable" value="0">
                </form>
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
                            <input class="form-control " value="@if(!is_null($retenciones)) {{$retenciones->valor_fuente}} @endif" type="text" name="total_rfirt" id="total_rfirt">

                        </div>
                        <div class="form-group col-md-3 px-0">

                            <label for="total_abonos" class="label_header">Total RFIVA</label>
                            <input class="form-control " value="@if(!is_null($retenciones)) {{$retenciones->valor_iva}} @endif" type="text" name="total_rfivat" id="total_rfivat">

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



                                @php $cont=$retenciones->valor_fuente+$retenciones->valor_iva; $tot=0; if(isset($retenciones->ventas)){ $tot= $retenciones->ventas->total_final - $cont; } @endphp


                                <tr class="well">
                                    <!--AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                    <td> <input class="form-control input-sm" type="text" name="vence{{$tot}}" value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->fecha}} @endif" id="vence{{$tot}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="tipo{{$tot}}" value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->tipo}}  @endif" id="tipo{{$tot}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="numero{{$tot}}" value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->nro_comprobante}} @endif" id="numero{{$tot}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="concepto{{$tot}}" value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->concepto}} @endif" id="concepto{{$tot}}" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5; " type="text" name="div{{$tot}}" id="div{{$tot}}" value="$" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;" type="text" name="saldo{{$tot}}" value="@if(isset($retenciones->ventas)) {{$retenciones->ventas->total_final}} @endif" id="saldo{{$tot}}" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;  text-align: center;" value="{{number_format($cont,2)}}" type="text" name="abono{{$tot}}" id="abono{{$tot}}" readonly></td>
                                    <td> <input class="form-control input-sm" style="text-align: center; width: 85%;" type="text" value="{{number_format($tot,2)}}" name="abono_base{{$tot}}" id="abono_base{{$tot}}" readonly> </td>
                                </tr>



                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="form-group col-md-2 px-0">

                                <label for="total_ingresos" class="label_header">Total Egreso</label>
                                <input class="form-control input-sm" type="text" name="total_egreso" value="{{number_format($cont,2)}}" id="total_egreso" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="credito_aplicado" class="label_header">Debito Aplicado</label>
                                <input class="form-control input-sm" olor: red; text-align: right;" value="{{number_format($cont,2)}}" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_deudas" class="label_header">Total Deudas</label>
                                <input class="form-control input-sm" type="text" name="total_deudas" value="{{number_format($tot,2)}}" id="total_deudas" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_abonos" class="label_header">Total Abonos</label>
                                <input class="form-control input-sm" type="text" name="total_abonos" value="{{number_format($tot,2)}}" id="total_abonos" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2 px-0">

                            <label for="nuevo_saldo" class="label_header">Nuevo Saldo</label>
                            <input class="form-control input-sm" type="text" name="nuevo_saldo" value="{{number_format($cont,2)}}" id="nuevo_saldo" readonly>

                        </div>
                        <div class="form-group col-md-2">
                            <input type="hidden" name="retencion_fuente" id="retencion_fuente">
                            <input type="hidden" name="retencion_ivas" id="retencion_ivas">
                            <input type="hidden" name="retencion_totales" id="retencion_totales">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12" style="text-align: center;">
                <button class="btn btn-success btn-gray" type="submit" formaction="{{route('clienter.update',['id'=>$retenciones->id])}}"> <i class="fa fa-pencil-square"></i> Editar </button>
            </div>
        </div>






        </div>

    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    function cambio() {
        var fecha = $("#fecha_cambio").val();
        var id_asiento = $("#id_asiento").val();
        var id_retencion = $("#id_retencion").val();
        var opcion = confirm("Desea cambiale la fecha ?");
        if (opcion == true) {
            $.ajax({
                url: "{{route('actualizar_fecha')}}",
                data: {
                    'fecha': fecha,
                    'id_asiento':id_asiento,
                    'id_rentecion':id_retencion,
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                   if(data =='ok'){
                    swal("Correcto!", "Editado", "success");
                    location.reload();
                   }
                },
                error: function(xhr) {
                    //alert('Existió un problema');
                    console.log(xhr);
                },
            });
        } else {}
    }
</script>


@endsection