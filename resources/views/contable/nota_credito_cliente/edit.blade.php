@extends('contable.nota_credito_cliente.base')
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
</style>

<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('nota_credito_cliente.index') }}";
    }
</script>
<!--<script type="text/javascript">
    function goBack() {
        location.href="{{ route('nota_cliente_debito.index') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">-->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">Nota Crédito Cliente</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_nota_credito_actual">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <!--<div class="box-title "><b>Ver Nota de Crédito Cliente</b></div>-->
                            <h5><b>DETALLE NOTA CRÉDITO CLIENTE</b></h5>
                        </div>
                        <div class="col-md-6 col-sm-6 col-3">
                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$nota_cred_client->id_asiento_cabecera])}}" class="btn btn-info" data-toggle="modal" data-target="#visualizar_estado">
                                <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                            </a>
                
                                <a class="btn btn-success btn-gray " href="{{route('librodiario.edit',['id'=>$nota_cred_client->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>
                                <button type="button" class="btn btn-success btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-12">&nbsp;</div>
                            <div class=" col-md-1 px-1">
                                <label class="label_header">Estado</label>
                                <input style=" @if(($nota_cred_client->estado)==1) background-color: green; @else background-color: red; @endif" class="form-control col-md-1">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_nota_credito">ID:</label>
                                <input class="form-control" type="text" name="id_nota_credito" id="id_nota_credito" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id}} @endif" id="id_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_secuencia">Número</label>
                                <input class="form-control" type="text" id="numero_secuencia" name="numero_secuencia" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->secuencia}} @endif" readonly>
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control" type="text" name="tipo" id="tipo" value="CLI-CR" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control" type="text" id="asiento" readonly name="asiento" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id_asiento_cabecera}} @endif" readonly>
                            </div>
                            @php
                            $fech = substr($nota_cred_client->fecha, 0, 10);
                            $fech_inver = date("d/m/Y",strtotime($fech));
                            @endphp
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control" type="text" name="fecha_hoy" readonly id="fecha_hoy" value="@if(!is_null($fech_inver)) {{$fech_inver}} @endif">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">No. Factura: </label>
                                <input class="form-control" type="text" name="fecha_hoy" readonly id="fecha_hoy" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->numero_factura}} @endif">
                            </div>
                        </div>
                        <div class="col-md-8 px-1">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <label class="label_header" for="concepto">Concepto:</label>
                            <input class="form-control" type="text" name="concepto" id="concepto" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->concepto}}@endif" readonly>
                        </div>
                        <div class=" col-md-2 px-1">
                            <label class="label_header" for="cliente">Sucursal: </label>
                            <input type="text" id="sucursal" name="sucursal" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->sucursal}} @endif" readonly>
                        </div>
                        <div class=" col-md-2 px-1">
                            <label class="label_header" for="cliente">Punto de Emision: </label>
                            <input type="text" id="punto_emision" name="punto_emision" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->punto_emision}} @endif" readonly>
                        </div>
                        <div class=" col-md-4 px-0">
                            <label class="col-md-12 label_header" for="cliente">Cliente: </label>
                            <input type="text" id="id_cliente" name="id_cliente" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id_cliente}} @endif" readonly>
                        </div>
                        <div class="col-md-4 px-0">
                            <label class="col-md-12 label_header" for="nombre_cliente">&nbsp;</label>
                            <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" value="@if(!is_null($nota_cred_client->cliente)){{$nota_cred_client->cliente->nombre}}@endif" readonly>
                        </div>
                        <div class="col-md-2 px-0" style="padding-top: 13px">
                            <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">Archivo del SRI</span>
                            <input style="width:17px;height:17px;" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" disabled value="1" @if($nota_cred_client->check_sri=='1')
                            checked
                            @endif>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="detalle_deuda">DETALLE DE RUBROS DE CRÉDITO</label>
                </div>
                <div class="col-md-12">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <div class="table-responsive col-md-12">
                        <table id="example3" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Código</th>
                                    <th style="width: 12%; text-align: center;">Rubro</th>
                                    <th style="width: 20%; text-align: center;">Detalle</th>
                                    <th style="width: 8%; text-align: center;">Divisa</th>
                                    <th style="width: 8%; text-align: center;">Valor</th>
                                    <th style="width: 8%; text-align: center;">Total Base</th>
                                </tr>
                            </thead>
                            <tbody id="det_recibido">
                                @foreach($det_rub_cred as $value)
                                <tr>
                                    <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                                    <td>@if(!is_null($value->nombre_rubro)){{$value->nombre_rubro}}@endif</td>
                                    <td>@if(!is_null($value->detalle)){{$value->detalle}}@endif</td>
                                    <td>Dolares</td>
                                    <td>@if(!is_null($value->valor)){{number_format($value->valor,2)}}@endif</td>
                                    <td>@if(!is_null($value->total_base)){{number_format($value->total_base,2)}}@endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                &nbsp;
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="subtotal">Subtotal:</label>
                                <input class="form-control  col-md-12" type="text" name="subtotal" id="subtotal" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->subtotal,2)}} @endif" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="impuesto">Impuesto:</label>
                                <input class="form-control  col-md-12" type="text" name="impuesto" id="impuesto" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->impuesto,2)}} @endif" readonly>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="total">Total Crédito:</label>
                                <input class="form-control  col-md-12" type="text" name="total" id="total" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_credito,2)}} @endif" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="top: 20px;">
                    <label for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive col-md-12 " style="min-height: 250px; max-height: 250px; top: 20px;">
                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Emisión</th>
                                    <th style="width: 10%; text-align: center;">Vence</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 10%; text-align: center;">Numero</th>
                                    <th style="width: 30%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 10%; text-align: center;">Saldo</th>
                                    <th style="width: 10%; text-align: center;">Abono</th>
                                    <th style="width: 10%; text-align: center;">Nuevo Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="crear">
                                @foreach($det_cred_client as $values)
                                @php
                                $fech_emis_inver = date("d/m/Y",strtotime($values->fecha_emision));
                                $fech_venc_inver = date("d/m/Y",strtotime($values->fecha_vence));
                                @endphp
                                <tr>
                                    <td>@if(!is_null($fech_emis_inver)){{$fech_emis_inver}}@endif</td>
                                    <td>@if(!is_null($fech_venc_inver)){{$fech_venc_inver}}@endif</td>
                                    <td>@if(!is_null($values->tipo)){{$values->tipo}}@endif</td>
                                    <td>@if(!is_null($values->secuencia_factura)){{$values->secuencia_factura}}@endif</td>
                                    <td>@if(!is_null($values->concepto)){{$values->concepto}}@endif</td>
                                    <td>$</td>
                                    <td>@if(!is_null($values->saldo)){{number_format($values->saldo,2)}}@endif</td>
                                    <td>@if(!is_null($values->abono)){{number_format($values->abono,2)}}@endif</td>
                                    <td>@if(!is_null($values->nuevo_saldo)){{number_format($values->nuevo_saldo,2)}}@endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 30px;padding-left: 30px">
                    <div class=" col-md-2 px-1">
                        <label for="total_deudas">Total Deudas:</label>
                        <input class="form-control " style="align:left;" type="text" name="total_deudas" id="total_deudas" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_deudas,2)}} @endif" readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                        <label for="total_credito">Total Crédito:</label>
                        <input class="form-control " type="text" name="total_credito" id="total_credito" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_credito,2)}} @endif" readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                        <label for="total_abonos">Total Abonos:</label>
                        <input class="form-control " type="text" name="total_abonos" id="total_abonos" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_abonos,2)}} @endif" readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                        <label for="total_nuevo_saldo">Nuevo Saldo:</label>
                        <input class="form-control " type="text" name="total_nuevo_saldo" id="total_nuevo_saldo" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_nuevo_saldo,2)}} @endif" readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                        <label for="deficit_ingreso">Deficit de Ingresos:</label>
                        <input class="form-control " type="text" name="deficit_ingreso" id="deficit_ingreso" value='0.00' readonly>
                    </div>
                    <div class=" col-md-2 px-1">
                        <label for="superavit_favor">Superavit a favor:</label>
                        <input class="form-control " type="text" name="superavit_favor" id="superavit_favor" value='0.00' readonly>
                    </div>
                </div>
                <div class="col-md-12" style="padding-left: 30px">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3" readonly>@if(!is_null($nota_cred_client)){{$nota_cred_client->observacion}}@endif</textarea>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection