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

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    .container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .ocultos {
        display: none;
        width: 90%;
    }

    .ocultosp {
        width: 90%;
    }

    .datos td {
        text-align: center;
    }

    .valores {
        text-align: end;
        font-weight: bold;
    }

    .valores input {
        background: none;
        border: 0px;
        text-align: center;
    }
</style>
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
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
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">Nota Crédito Cliente</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_nota_credito_cl">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 col-sm-7 col-6">
                            <!--<div class="box-title "><b>Crear Nota de Crédito Clientes</b></div>-->
                            <h5><b>CREAR NOTA CRÉDITO CLIENTE</b></h5>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$nota_cred_client->id_asiento_cabecera])}}" class="btn btn-info" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento
                                </a>
                                <a class="btn btn-success btn-gray " href="{{route('librodiario.edit',['id'=>$nota_cred_client->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>
                                <button type="button" class="btn btn-info" onclick="nueva_nota_credito()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <button type="button" class="btn  btn-danger" onclick="goBack()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12">
                        <div class="form-row ">
                            <div class=" col-md-1 px-1">
                                <label class="label_header">{{trans('contableM.estado')}}</label>
                                <input style=" @if(($nota_cred_client->estado)==1) background-color: green; @else background-color: red; @endif" class="form-control col-md-1">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_nota_credito">{{trans('contableM.id')}}:</label>
                                <input class="form-control " type="text" id="id_nota_credito" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id}} @endif" disabled>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_secuencia">Número:</label>
                                <input class="form-control " type="text" id="numero_secuencia" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->secuencia}} @endif" disabled>
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="CLI-CR" disabled>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}:</label>
                                <input class="form-control " type="text" id="num_asiento" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id_asiento_cabecera}} @endif" disabled>

                            </div>
                            @php
                            $fech = substr($nota_cred_client->fecha, 0, 10);
                            $fech_inver = date("d/m/Y",strtotime($fech));
                            @endphp

                            <div class="col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control " type="text" id="fecha_hoy" value="@if(!is_null($fech_inver)) {{$fech_inver}} @endif" disabled>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.NoFactura')}} </label>
                                <input class="form-control " type="text" id="nro_factura" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->numero_factura}} @endif">
                            </div>
                        </div>

                        <div class="form-group col-xs-6  col-md-1 px-1">
                            <div class="col-md-12 px-0">
                                <label for="empresa" class="label_header">Electrónica</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <label class="switch">
                                    <input class="electros" @if($empresa->electronica==1) @else disabled @endif id="toggleswitch" type="checkbox">
                                    <span class="slider round"></span>
                                    <input type="hidden" id="electronica" name="electronica" value="0">
                                </label>
                            </div>
                        </div>

                        <div class="col-md-7 px-1">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                            <input class="form-control  col-md-12" type="text" maxlength="50" id="concepto" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->concepto}}@endif" disabled>
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="label_header" for="sucursal">{{trans('contableM.sucursal')}}:</label>
                            <div class="col-md-12 px-0">
                                <input type="text" id="sucursal" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->sucursal}} @endif" disabled>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="label_header" for="punto_emision">Punto de Emision:</label>
                            <div class="col-md-12 px-0">
                                <input type="text" id="punto_emision" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->punto_emision}} @endif" disabled>

                            </div>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2 px-0" style="padding-top: 13px">
                            <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">Archivo del SRI</span>
                            <input style="width:17px;height:17px;" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" disabled value="1" @if($nota_cred_client->check_sri=='1')
                            checked
                            @endif>
                        </div>


                        <div class="col-md-6 px-0">
                            <label class="label_header"> Factura </label>
                            <input type="text" id="id_factura" class="form-control" value="@if(isset($nota_cred_client->valorf)) {{$nota_cred_client->valorf->nombre_cliente}}  | {{$nota_cred_client->valorf->nro_comprobante}} @endif " disabled>
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="label_header">{{trans('contableM.TotalFactura')}}</label>
                            <input type="text" class="form-control" id="total_final" value="@if(isset($nota_cred_client->valorf)) {{$nota_cred_client->valorf->total_final}} @endif" disabled>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">

                </div>
                <div class="col-md-12" style="top: 20px;">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="detalle_deuda">DETALLE DE PRODUCTOS</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">

                    <table id="example2" class="table table-hover dataTable" role="grid" style="width: 100%;" aria-describedby="example2_info">
                        <thead style="background-color: #9E9E9E; color: white;">
                            <tr style="position: relative;">
                                <!--<th style="width: 10%; text-align: center;">{{trans('contableM.id')}}</th>-->
                                {{-- <th style="text-align: center;">#</th> --}}
                                <th style="text-align: center;">{{trans('contableM.codigo')}}</th>
                                <th style="text-align: center;">{{trans('contableM.cantidad')}}</th>
                                <th style="text-align: center;">{{trans('contableM.nombre')}}</th>
                                <th style="text-align: center;">{{trans('contableM.valor')}}</th>
                                <th style="text-align: center;">{{trans('contableM.accion')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosdev as $value)
                            <tr class="datos">
                                {{-- <td>#</td> --}}
                                <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                                <td>@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                                <td>@if(!is_null($value->precio)){{$value->precio}}@endif</td>
                                <td>
                                    <input class='verificar' type='checkbox' name='verificarx[]' checked value='0' disabled>
                                    <input class='vercheckbox' type='hidden' name='verificar[]' value='1' disabled>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div> 

                <div class="col-md-12">
                    <div class="valores">
                        <div>Subtotal {{$porcentajeIva}}% <input name="subtotal121" id="subtotal12" type="text" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->subtotal12,2)}} @endif" disabled> </div>
                    </div>
                    <div class="valores">
                        <div>Subtotal 0% <input name="subtotal01" id="subtotal0" type="text" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->subtotal0,2)}} @endif" disabled> </div>
                    </div>
                    <div class="valores">
                        <div>Descuento <input name="descuento1" id="descuento" type="text" value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->descuento,2)}} @endif"> </div>
                    </div>
                    <div class="valores">
                        <div>Subtotal <input name="subtotal1" id="subtotal" type="text" disabled value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->subtotal,2)}} @endif"> </div>
                    </div>
                    <div class="valores">
                        <div>Impuesto <input name="impuesto1" id="impuesto" type="text" disabled value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->impuesto,2)}} @endif"> </div>
                    </div>
                    <div class="valores">
                        <div>Total <input name="total1" id="total" type="text" disabled value="@if(!is_null($nota_cred_client)) {{number_format($nota_cred_client->total_credito,2)}} @endif"> </div>
                    </div>
                </div>


                <div class="col-md-12" style="padding-left: 30px; padding-top: 10px">
                    <label for="observaciones">{{trans('contableM.observaciones')}}</label>
                    <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3" readonly>@if(!is_null($nota_cred_client)){{$nota_cred_client->observacion}}@endif</textarea>
                </div>
            </div>
        </div>
    </form>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection