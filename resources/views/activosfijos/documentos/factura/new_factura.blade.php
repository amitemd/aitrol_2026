@extends('activosfijos.documentos.factura.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">

@php
$mes = date('m');
$anio = date('Y');
@endphp
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<style>
    .input-number {
        width: 80%;
        height: 20px;
    }

    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }

    .block {
        display: block;
    }

    .none {
        display: none;
    }

    .my_scroll_div {
        overflow-y: auto;
        max-height: 600px;
    }

    .legend {
        color: black;
        font-size: 15px;
        font-weight: bold;
        margin: 0px;
    }
</style>




<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.activosfijos')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.documentos')}}</a></li>
            <li class="breadcrumb-item"><a href="javascript:goBack();">{{trans('contableM.facturaaf')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.nuevo')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('contableM.nuevafacturaaf')}}</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="return location.reload(true);" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
    </div>

    <form class="form-vertical" enctype="multipart/form-data" id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">

            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12 col-xs-12 px-1">
                        <label class="">{{trans('contableM.buscadorordenacfi')}}</label>
                        <br>
                        <select id="select_orden" style="width:50%!important" class="form-control select2_cuentas select2-hidden-accessible" class="form-control" onchange="buscar_orden();" name="select_orden">
                            <option value="">{{trans('contableM.seleccione')}}...</option>

                            @foreach($ordenes as $orden)
                            <option value="{{$orden->id}}">{{$orden->fecha_compra}} | {{$orden->razonsocial}} | {{$orden->observacion}}</option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="header row">
                    <div class="col-md-12" style="display: flex;justify-content: space-between;">
                        <div class="col-md-2 text-left">
                            <fieldset>
                                <legend class="legend">Periodo</legend>
                                <div style="display: flex; justify-content: space-around;">
                                    <input style="width: 50%;" id="periodo_mes" name="periodo_mes" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'mes');revisarIvaPeriodo();" type="text" class="form-control" value="{{$mes}}">
                                    <input style="width: 50%;" id="periodo_anio" name="periodo_anio" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'anio');revisarIvaPeriodo();" type="text" class="form-control" value="{{$anio}}">
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="header row">
                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="details_details" id="details_count" value="0">
                            <div id="form_acc"></div>
                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select class="form-control validar" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach ($sucursales as $value)
                                        <option value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}} ({{trans('contableM.empresa')}})</label>
                                    <input type="hidden" id="electronica" name="electronica" value="0">
                                </div>
                                <div class="col-md-12 px-0">
                                    <select class="form-control validar" name="punto_emision" id="punto_emision" required>
                                        <option value="">{{trans('contableM.seleccione')}}...</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="id" id="id" value="" readonly>
                                    @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="numero" class=" label_header">{{trans('contableM.numero')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="numero" id="numero" value="" readonly>
                                    @if ($errors->has('numero'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numero') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="tipo" class="label_header">{{trans('contableM.tipo')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="tipo_transaccion" id="tipo_transaccion" value="ACT-FA" readonly>
                                    @if ($errors->has('tipo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="asiento_id" class="label_header">{{trans('contableM.asiento')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="asiento_id" id="asiento_id" value="" readonly>
                                    @if ($errors->has('asiento_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('asiento_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="fecha_asiento" class="label_header">{{trans('contableM.FechadeAsiento')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input id="fecha_asiento" onchange="cambiarFecha(this, 'fecha_asiento')" onblur="validarCierre(this);" type="date" class="form-control validar" name="fecha_asiento" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="@php echo date('Y-m-d');@endphp">
                                    @if ($errors->has('fecha_asiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_asiento') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="fecha_caduca" class="label_header">{{trans('contableM.fechacaducidad')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input id="fecha_caduca" type="date" class="form-control validar" name="fecha_caduca" value="{{date('Y-m-d')}}" readonly>
                                    @if ($errors->has('fecha_caduca'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_caduca') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-4  col-md-4  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="proveedor" class="label_header">{{trans('contableM.proveedor')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas" style="width:100%" id="proveedor" onchange="llenarCampo(); cargar_autorizacion();" name="proveedor">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach ($proveedor as $value)
                                        <option value="{{ $value->id }}">{{ $value->razonsocial }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('orden_venta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proveedor') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="divisas" class="label_header">{{trans('contableM.divisass')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select id="divisas" name="divisas" class="form-control select2_cuentas validar" style="width:100%">
                                        @foreach($divisas as $value)
                                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="serie_factura" class=" label_header">{{trans('contableM.serie')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control validar" onkeyup="agregar_serie(); llenarCampo();" onblur="agregar_serie()" name="serie_factura" id="serie_factura" value="" >
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie_factura').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">


                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="ord_compra" class=" label_header">{{trans('contableM.ocompra')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ord_compra" id="ord_compra" value="">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('ord_compra').value = '';"></i>
                                        </div>
                                    </div>
                                    @if ($errors->has('ord_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ord_compra') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-6 col-md-4 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="nro_autorizacion" class=" label_header">{{trans('contableM.autorizacion')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control validar" name="nro_autorizacion" id="nro_autorizacion" value="">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('nro_autorizacion').value = '';"></i>
                                        </div>
                                    </div>
                                    @if ($errors->has('nro_autorizacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nro_autorizacion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="fecha_compra" class=" label_header">{{trans('contableM.fecha')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="date" class="form-control col-md-12 validar" onchange="cambiarFecha(this, 'fecha_compra')" onblur="validarCierre(this);" name="fecha_compra" id="fecha_compra" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="@php echo date('Y-m-d');@endphp">

                                    @if ($errors->has('fecha_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_compra') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="termino" class="label_header">{{trans('contableM.termino')}}</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select id="termino" name="termino" class="form-control select2_cuentas validar" style="width:100%" onchange="fecha_vencimiento(this)">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($term as $t)
                                        <option value="{{$t->id}}">{{$t->nombre}}</option>
                                        @endforeach
                                        <!--  <option value="4">30 Dias</option>
                                        <option value="9">60 Dias</option>
                                        <option value="9">Credito</option> -->
                                    </select>

                                </div>
                            </div>


                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="secuencia" class=" label_header">{{trans('contableM.secuenciafactura')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control validar" name="secuencia" id="secuencia" value="" onchange="ingresar_cero('secuencia', 9); llenarCampo()" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia').value = '';"></i>
                                        </div>
                                    </div>
                                    @if ($errors->has('secuencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('secuencia') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label class="label_header">{{trans('contableM.creditotributario')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas validar " style="width:100%">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($c_tributario as $value)
                                        <option value="{{$value->codigo}}">{{$value->codigo}}-{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="tipo_comprobante" class="label_header">{{trans('contableM.tipocomprobante')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas validar" id="tipo_comprobante" name="tipo_comprobante" style="width:100%">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach ($tipos_comp as $value)
                                        <option value="{{ $value->codigo }}">{{ $value->codigo }} - {{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('tipo_comprobante'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo_comprobante') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-8 col-md-8 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="concepto" class=" label_header">{{trans('contableM.concepto')}}</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <input type="text" class="form-control validar" name="concepto" id="concepto" value="" width="100%">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">


                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>
                                <th width="30%" class="" tabindex="0">{{trans('contableM.descripcionactivo')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.Costo')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.total')}}</th>
                                <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="crearFila(0, {})" type="button" class="btn btn-success btn-gray btn-sm">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" onclick="crearTransporte()" class="btn btn-primary btn-sm">
                                        <i class="fa fa-truck" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" onclick="crearGasto()" class="btn btn-primary btn-sm">
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">

                        </tbody>
                        <input type="hidden" id="iva_global" name="iva_global" value="">
                        <tfoot class=''>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right" id="textSubtotal">{{trans('contableM.subtotal')}} </td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}} 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>



                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right" id="textIva">{{trans('contableM.TarifaPorcIva')}} </td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardar(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>

            </div>
        </div>


        <div class="modal fade bs-example-modal-lg " id="modal_datos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content my_scroll_div" id="datos_activo">

                </div>
            </div>
        </div>
        <input name='contador_items' id='contador_items' type='hidden' value="1">
    </form>
</section>
@include('activosfijos.documentos.factura.mdactivo2')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var valorIva = '{{$valorIva}}';
    var porcentajeIva = '{{$porcentajeIva}}';
    $(document).ready(function() {
        var textSubtotal = $('#textSubtotal').html() + porcentajeIva + '%';
        var textIva = $('#textIva').html() + porcentajeIva + '%';
        $('#iva_global').val(valorIva);
        $('#textSubtotal').html(textSubtotal);
        $('#textIva').html(textIva);
        $('.select2_cuentas').select2({
            tags: false
        });
        $('.select2_cuentas2').select2({
            tags: false
        });
        $('.select2_color').select2({
            tags: true
        });
        crearFila(0, {});
    });

    function revisarIvaPeriodo() {
        var textSubtotal = $('#textSubtotal').html().replace(porcentajeIva + '%', '');
        var textIva = $('#textIva').html().replace(porcentajeIva + '%', '');
        var hoy = new Date();
        var mesPeriodo = parseFloat($('#periodo_mes').val())+1;
        var fechaFormulario = new Date($('#periodo_anio').val() + '-' + mesPeriodo + '-01');
        var mesf = fechaFormulario.getMonth();
        var aniof = fechaFormulario.getFullYear();
        var mesh = hoy.getMonth();
        var anioh = hoy.getFullYear();
        var fecha1 = mesf + '-' + aniof;
        var fecha2 = mesh + '-' + anioh;
        hoy.setHours(0, 0, 0, 0);
        fechaFormulario.setHours(0, 0, 0, 0);
        if (fecha2 > fecha1) {
            valorIva = '0.12';
            porcentajeIva = '12';
        } else {
            valorIva = '{{$valorIva}}';
            porcentajeIva = '{{$porcentajeIva}}';
        }
        $('#textSubtotal').html('');
        $('#textIva').html('');

        $('#textSubtotal').html(textSubtotal + porcentajeIva + '%');
        $('#textIva').html(textIva + porcentajeIva + '%');

        $('#iva_global').val(valorIva);
        let ids = document.querySelectorAll('.ids');
        for (let i = 0; i < ids.length; i++) {
            totalProducto(ids[i].value);

        }
        totalGlobal();
    }

    function buscar_orden() {
        var orden = $("#select_orden").val();
        $.ajax({
            type: 'get',
            url: "{{route('documentofactura.buscar_orden')}}",
            datatype: 'json',
            data: {
                'orden': orden
            },
            success: function(data) {

                llenarInputs(data.cab);
            },
            error: function(data) {

            }
        })
    }

    const fecha_vencimiento = (e) => {
        const d = new Date(document.getElementById("fecha_compra").value);
        let f_caducidad = document.getElementById("fecha_caduca");
        let day = e.options[e.selectedIndex].text;
        const dayArray = day.split(" ");
        let fechaNew = "";
        if (parseInt(dayArray[0]) >= 0) {
            fechaNew = sumarDias(d, +dayArray[0])
        } else if (dayArray[0].toUpperCase() == "CONTADO") {
            fechaNew = sumarDias(d, +0)
        } else {
            f_caducidad.readOnly = false;
            return;
        }
        var fechaImp = fechaNew.getFullYear() + '-' + ('0' + (fechaNew.getMonth() + 1)).slice(-2) + '-' + ('0' + fechaNew.getDate()).slice(-2);
        f_caducidad.value = fechaImp;
        f_caducidad.readOnly = true;
        return;
    }

    function sumarDias(fecha, dias) {
        fecha.setDate(fecha.getDate() + dias + 1);
        return fecha;
    }

    const validarPeriodo = (e, type) => {
        const fecha = new Date();
        const anioActual = fecha.getFullYear();
        const mesActual = fecha.getMonth() + 1;
        if (type == "mes") {
            if (parseInt(e.value) > 12) {
                e.value = mesActual;
                if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                    e.value = "0" + parseInt(e.value);
                }
                alertas("error", "Error en mes", "El mes del periodo no existe")
                return;
            } else if (parseFloat(e.value) <= 0) {
                e.value = mesActual;
                if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                    e.value = "0" + parseInt(e.value);
                }
                alertas("error", "Error en mes", "El mes del periodo no puede ser menor o igual a 0")
                return;
            } else if (parseInt(e.value) >= 1 && parseInt(e.value) <= 9) {
                e.value = "0" + parseInt(e.value);
                return;
            }
        }

        if (type == "anio") {
            if (parseInt(e.value) > 2200) {
                e.value = anioActual;
                alertas("error", "Error en año", "El año del periodo no esta permitido")
                return;
            } else if (parseInt(e.value) < 2015) {
                e.value = anioActual;
                alertas("error", "Error en año", "El año del periodo no esta permitido")
                return;
            }
        }
    }

    function validarCierre(e) {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById(e.id).value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                alertas("error", "Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.')
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_compra").value = fechaFormateada;
                document.getElementById("fecha_asiento").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    const validarFechasPeriodo = () => {
        let periodo_mes = document.getElementById("periodo_mes").value;
        let periodo_anio = document.getElementById("periodo_anio").value;
        let f_autorizacion = document.getElementById("fecha_compra").value;
        const fechArray = f_autorizacion.split("-");
        let fechAuto = `${fechArray[0]}-${fechArray[1]}`;
        let fechPeriodo = `${periodo_anio}-${periodo_mes}`;

        if (fechAuto == fechPeriodo) {
            return true;
        } else {
            return false;
        }
    }

    const cambiarFecha = (e, type) => {
        if (type == "fecha_compra") {
            document.getElementById("fecha_asiento").value = e.value;
        } else if (type == "fecha_asiento") {
            document.getElementById("fecha_compra").value = e.value;
        }
        return;
    }


    const llenarInputs = (data) => {
        console.log(data);
        document.getElementById("concepto").value = data.observacion;
        document.getElementById("fecha_compra").value = data.fecha_compra;
        document.getElementById("fecha_asiento").value = data.fecha_compra;
        document.getElementById("serie_factura").value = data.serie;
        document.getElementById("secuencia").value = data.secuencia;
        document.getElementById("divisas").value = data.divisas;
        document.getElementById("subtotal_121").value = data.subtotal12;
        document.getElementById("subtotal_01").value = data.subtotal0;
        document.getElementById("descuento1").value = data.descuento;
        document.getElementById("base1").value = data.subtotal;
        document.getElementById("tarifa_iva1").value = data.impuesto;
        document.getElementById("total1").value = data.total;
        document.getElementById("subtotal_12").innerHTML = parseFloat(data.subtotal12).toFixed(2);
        document.getElementById("subtotal_0").innerHTML = parseFloat(data.subtotal0).toFixed(2);
        document.getElementById('base').innerHTML = parseFloat(data.subtotal).toFixed(2);
        document.getElementById('descuento').innerHTML = parseFloat(data.descuento).toFixed(2);
        document.getElementById('tarifa_iva').innerHTML = parseFloat(data.impuesto).toFixed(2);
        document.getElementById('total').innerHTML = parseFloat(data.total).toFixed(2);

        $('#proveedor').empty();
        $('#proveedor').append(`<option selected value="${data.id_proveedor}" >${data.nombre_proveedor}</option>`);
        $("#proveedor").change();

        $('#agregar_cuentas').empty();
        document.getElementById('contador_items').value = 1;

        const details = data.detalles.map(detalle => {
            crearFila(1, detalle);
        });
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;
        //  console.log(campo)
        for (let i = 0; i < campo.length; i++) {
            //console.log(`${campo[i].name}: ${campo[i].value}`);
            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    function agregar_serie() {
        var serie = $('#serie_factura').val();
        if ((serie.length) == 3) {
            $('#serie_factura').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie_factura').val('');
            alerta("Error!", "error" ,"Ingrese la serie correctamente: 000-000");
        }
    }

    

    const sumarFecha = (e) => {

        let day = e.options[e.selectedIndex].text;
        let date = new Date(document.getElementById("fecha_compra").value);
        let subday = day.substring(0, 2).trim();
        if (Number.isInteger(parseInt(subday))) {
            date.setDate(date.getDate() + parseInt(subday));
            var fechaImp = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + (date.getDate() + 1)).slice(-2);
            console.log(fechaImp);
            document.getElementById("fecha_caduca").value = fechaImp;
            document.getElementById("fecha_caduca").readOnly = true;
        } else {
            document.getElementById("fecha_caduca").value = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            document.getElementById("fecha_caduca").readOnly = false;
        }

    }

    function ingresar_cero(ids, longitud) {

        let id = document.getElementById(ids);
        let cero = "";
        let concat = "";
        if (parseInt(id.value) > 0) {
            if (id.value.length < longitud) {
                while (concat.length != longitud) {
                    cero = "0" + cero;
                    concat = cero + id.value;
                }
                id.value = concat;
            } else {
                alerta("Error!", "error", "{{trans('contableM.valorincorrecto')}}");
                id.value = "";
            }
        } else {
            alerta("Error!", "error", "{{trans('contableM.valorincorrecto')}}");
            id.value = "";
        }
    }


    function guardar_color(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_color')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {},
            error: function(data) {
                //alert(data)
            }
        });

    }

    function guardar_marca(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_marca')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_serie(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_serie')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_responsable(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_responsable')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function ingresar_cero2() {
        var secuencia_factura = $('#mdcodigo_num').val();
        var digitos = 6;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 7) {
                swal("Error!", "{{trans('contableM.valornoper')}}", "error");
                $('#mdcodigo_num').val('');

            } else {

                var concadenate = parseInt(digitos - longitud);
                switch (longitud) {
                    case 1:
                        secuencia = '00000';
                        break;
                    case 2:
                        secuencia = '0000';
                        break;
                    case 3:
                        secuencia = '000';
                        break;
                    case 4:
                        secuencia = '00';
                        break;
                    case 5:
                        secuencia = '0';
                        break;
                    case 6:
                        secuencia = '';
                }
                $('#mdcodigo_num').val(secuencia + secuencia_factura);
            }


        } else {
            swal("Error!", "{{trans('contableM.valornoper')}}", "error");
            $('#mdcodigo_num').val('');
        }
    }

    function alerta(title, icon, text) {
        Swal.fire({
            icon: '' + icon,
            title: '' + title,
            html: '' + text
        })
    }
    $('#modal_datosactivo').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    const crearFila = (req = 0, data = {}) => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                    <input  type="hidden" class="ids" value="${id}" />
                    <button type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id}, event)"> <i class="glyphicon glyphicon-edit"></i></button>  
                </td>
                <td>
                    <input id="codigo${id}" class="form-control validar" type="text" style="width: 80%;height:20px;" name="codigo[]" readonly value="${req === 1 ? data.codigo : '' }" >
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control validar" type="text" style="width: 93%;height:20px;" name="descrip_prod[]" readonly value="${req === 1 ? data.nombre : '' }">
                    <textarea  rows="3" name="observacion[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cant validar" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.cantidad : 0}" name="cantidad[]" >

                </td>
                <td>
                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id})" id="precio${id}" value="${req === 1 ? data.precio : 0.00}"  type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.porct_descuento : '0.00'}" name="descpor[]" >
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" >
                </td>
                <td>
                    <input onchange="descuento(${id});"  id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="${req === 1 ? data.descuento : '0.00'}" name="desc[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="${req === 1 ? data.precio_neto : '0.00'}"  name="precioneto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva${id}" ${req === 1 ? data.iva === 1 ? 'checked' : '' : ''}>
                    <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" value="${req === 1 ? data.val_iva : '0.00'}">
                    <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="${req === 1 ? data.total_valor : '0.00'}">
                    <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="${req === 1 ? data.iva : '0'}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarModalUni(${id})">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        if (req === 1) {

            $("#datos_activo").append(modal_crear(id, req, data.activo_fijo));
            let modal_datos_a = document.getElementById("modal_datos_a" + id);
            modal_datos_a.classList.remove("block");
            modal_datos_a.classList.add("none");
            console.log("Activo fijo", data.activo_fijo);
            $(`#mdtipo${id} option[value="${data.activo_fijo.tipo_id}"]`).attr("selected", true);
            $(`#mdtipo${id}`).change();
            $(`#mdmarca${id} option[value="${data.activo_fijo.marca}"]`).attr("selected", true);
            $(`#mdcolor${id} option[value="${data.activo_fijo.color}"]`).attr("selected", true);
            $(`#mdserie${id} option[value="${data.activo_fijo.serie}"]`).attr("selected", true);
            $(`#mdresponsable${id} option[value="${data.activo_fijo.responsable}"]`).attr("selected", true);
            $(`#mdubicacion${id} option[value="${data.activo_fijo.ubicacion}"]`).attr("selected", true);
            setTimeout(() => {
                $(`#mdgrupo${id} option[value="${data.activo_fijo.subtipo_id}"]`).attr("selected", true);
            }, 1000);
            if (data.activo_fijo.accesorios.length > 0) {
                const accesorio = data.activo_fijo.accesorios.map(access => {
                    agregar_items(id, req, access);

                });
            }
        }
        id++;
        document.getElementById('contador_items').value = id;
    }

    function select_iva(id) {
        var check = document.getElementById("valoriva" + id);
        var check_af = document.getElementById("check_iva" + id);

        if (check.checked) {
            check_af.value = 1;
        } else {
            check_af.value = 0;
        }
        totalProducto(id);
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)
        let val_iva = document.getElementById("val_iva" + id)
        let total_valor = document.getElementById("total_valor" + id)
        let subtotal0 = document.getElementById("subtotal_12")
        let subtotal12 = document.getElementById("subtotal_0")

        let iva_global = document.getElementById("iva_global").value


        let preXcantidad = (precio * cantidad);
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        let valoriva = 0;


        if ($('#valoriva' + id).prop('checked')) {

            valoriva = Math.round(precioTotal * (1000 * parseFloat(iva_global)));
            valoriva = valoriva / 1000;

        }

        let total = precioTotal + valoriva;

        val_iva.value = valoriva;
        desc.value = porcDescuento.toFixed(2);
        precioneto.value = precioTotal.toFixed(2);
        total_valor.value = total;

        totalGlobal();

    }


    function descuento(id) {
        let descpor = document.getElementById("descpor" + id);
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let desc = document.getElementById("desc" + id).value;
        let preXcantidad = (precio * cantidad);
        let preXcantidadV = 0;
        if (preXcantidad == 0) {
            preXcantidadV = 1;
        } else {
            preXcantidadV = preXcantidad;
        }
        let descuento = (desc * 100) / preXcantidadV;
        descpor.value = descuento.toFixed(4);
        totalProducto(id);
    }



    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let vliva = document.querySelectorAll(".val_iva");
        let tot_valor = document.querySelectorAll(".total_valor");
        let cantidad = document.querySelectorAll(".cant");
        let precio = document.querySelectorAll(".pneto");
        let check_iva = document.querySelectorAll(".check_iva");

        let total = 0;
        let descuento = 0;
        let iva = 0;
        let totv = 0;
        let subtotal0 = 0;
        let subtotal12 = 0;

        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value);
            descuento += parseFloat(desc[i].value);

            iva += parseFloat(vliva[i].value);
            totv += parseFloat(tot_valor[i].value);

            // if(parseFloat(vliva[i].value) > 0){
            //     subtotal12 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            // }else{
            //     subtotal0 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            // }
            if (parseFloat(check_iva[i].value) == 1) {
                subtotal12 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
                //subtotal12 = parseFloat(precioneto[i].value);
            } else {
                //subtotal0 = parseFloat(precioneto[i].value);
                subtotal0 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            }

            //totv += parseFloat(tot_valor[i].value);


        }

        document.getElementById("subtotal_12").innerHTML = parseFloat(subtotal12).toFixed(2);
        document.getElementById("subtotal_121").value = parseFloat(subtotal12).toFixed(2);

        document.getElementById("subtotal_0").innerHTML = parseFloat(subtotal0).toFixed(2);
        document.getElementById("subtotal_01").value = parseFloat(subtotal0).toFixed(2);

        document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('base1').value = parseFloat(total).toFixed(2);

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

        document.getElementById('tarifa_iva').innerHTML = parseFloat(iva).toFixed(2);
        document.getElementById('tarifa_iva1').value = parseFloat(iva).toFixed(2);

        document.getElementById('total').innerHTML = parseFloat(totv).toFixed(2);
        document.getElementById('total1').value = parseFloat(totv).toFixed(2);

    }

    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
        totalGlobal();
    });


    function goBack() {
        window.history.back();
    }

    const crearTransporte = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                    <!--<button type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id}, event)"> <i class="glyphicon glyphicon-edit"></i></button>-->
                </td>
                <td>
                    <input  type="hidden" class="ids" value="${id}" />
                    <input type="hidden" name="transporte" value="TRANS"/>
                    <input id="codigo${id}" class="form-control validar" type="text" style="width: 80%;height:20px;" name="codigo_trans[]" value="TRANS" readonly>
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control validar" type="text" style="width: 93%;height:20px;" name="descrip_prod_trans[]" value="TRANS | TRANSPORTE" readonly>
                    <textarea  rows="3" name="observacion_trans[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_trans[]" >

                </td>
                <td>
                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control" name="precio_trans[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor_trans[]" >
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc_trans[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc_trans[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_trans[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva_trans[]" id="valoriva${id}" value="1">
                    <input type="hidden" name="val_iva_trans[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                    <input type="hidden" name="total_valor_trans[]" id="total_valor${id}" class="form-control px-1 text-right total_valor">
                    <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        id++;
        document.getElementById('contador_items').value = id;
    }

    const crearGasto = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                   
                </td>
                <td>
                    <input type="hidden" name="gasto" value="GASTO"/>
                    <input  type="hidden" class="ids" value="${id}" />
                    <input id="codigo${id}" class="form-control" type="text" style="width: 80%;height:20px;" name="codigo_gasto[]" value="" readonly>
                </td>
                <td>
                    <select id="id_plan${id}" name="id_plan[]" class="form-control select2_cuentas validar" style="width: 100%;">
                        <option value=""> Seleccione...</option>
                        @foreach($plan_cuentas as $value)
                            <option value="{{$value->id_plan}}"> {{$value->nombre}} </option>
                        @endforeach 
                    </select>
                    <textarea  rows="3" name="observacion_gasto[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_gasto[]" >

                </td>
                <td>
                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control" name="precio_gasto[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor_gasto[]" >
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc_gasto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc_gasto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_gasto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva_gasto[]" id="valoriva${id}" value="1">
                    <input type="hidden" name="val_iva_gasto[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                    <input type="hidden" name="total_valor_gasto[]" id="total_valor${id}" class="form-control px-1 text-right total_valor">
                    <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        id++;
        document.getElementById('contador_items').value = id;
    }
</script>

<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }



    const validarTodo = () => {
        let camposVacios = [];

        if (document.getElementById("proveedor").value == '') {
            camposVacios.push(`{{trans('contableM.proveedor')}}`)
        }
        if (document.getElementById("divisas").value == '') {
            camposVacios.push(`{{trans('contableM.divisass')}}`)
        }
        if (document.getElementById("termino").value == '') {
            camposVacios.push(`{{trans('contableM.termino')}}`)
        }
        if (document.getElementById("cred_tributario").value == '') {
            camposVacios.push(`{{trans('contableM.creditotributario')}}`)
        }
        if (document.getElementById("nro_autorizacion").value == '') {
            camposVacios.push(`{{trans('contableM.numauto')}}`)
        }

        if (document.getElementById("secuencia").value == '') {
            camposVacios.push(`{{trans('contableM.secuencia')}}`)
        }
        if (document.getElementById("tipo_comprobante").value == '') {
            camposVacios.push(`{{trans('contableM.tipocomprobante')}}`)
        }
        if (document.getElementById("concepto").value == '') {
            camposVacios.push(`{{trans('contableM.concepto')}}`)
        }
        if (camposVacios.length > 0) {
            $('#boton_guardar').prop("disabled", false);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `{{trans('contableM.sigcamvacios')}}: ${camposVacios.map(i=>i)}`
            })

            return true;
        }

        return false;
    }

    const guardar = (e) => {
        $('#boton_guardar').prop("disabled", true);
        e.preventDefault();
        let proveedor = $('#proveedor').val();
        let serie = $('#serie').val();
        let secuencia = $('#secuencia_factura').val();
        let sec_imp = $('#secuencia_importacion').val();

        let resp = validarTodo();

        if (!validar_campos()) {
            if (!resp) {
                if ($('#nro_autorizacion').val() != '') {
                    if ($('#nro_autorizacion').val().length != 10 && $('#nro_autorizacion').val().length != 49) {
                        $('#boton_guardar').prop("disabled", false);
                        alerta('Error', 'error', 'Por favor, El número de autorización debe contener 10 o 49 dígitos');
                        return;
                    }
                }
                //console.log(validarFechasPeriodo())
                if (!validarFechasPeriodo()) {

                    $('#boton_guardar').prop("disabled", false);
                    alertas("error", "Error en fechas", "La fecha del periodo con la fecha de facturacion no coinciden")
                    return;
                }

                $.ajax({
                    url: "{{route('afDocumentoFactura.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: $("#crear_factura").serialize(),

                    success: function(data) {
                        if (data.respuesta == 'success') {
                            alerta(`{{trans('contableM.exito')}}`, data.respuesta, data.msj)
                            $('#asiento_id').val(data.id_asiento)
                        } else {
                            alerta('Error', data.respuesta, data.msj)
                            $('#boton_guardar').prop("disabled", false);

                        }
                    },
                    error: function(data) {

                    }
                });
            }
        } else {
            //btn_guardar.style.display = 'initial'
            $('#boton_guardar').prop("disabled", false);
            alerta('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }


    function cargar_autorizacion() {
        $.ajax({
            type: 'post',
            url: "{{ route('documentofactura.buscar_proveedor') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_proveedor': proveedor.value
            },
            success: function(data) {
                $('#nro_autorizacion').val(data.autorizacion);
                $('#serie_factura').val(data.serie);
                $('#fecha_caduca').val(data.f_caduca);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }



    const llenarCampo = () => {
        let proveedor = document.getElementById("proveedor");
        let serie = document.getElementById("serie_factura").value;
        let secuencia = document.getElementById("secuencia").value;
        let value = proveedor.options[proveedor.selectedIndex].text;
        if (value != '') {
            document.getElementById("concepto").value = `ACT-FA # ${value} ${serie}-${secuencia}`
        }
        //alert(proveedor.value);
    }

    const swetAlertDate = () => {

        let dateAsiento = document.getElementById("fecha_asiento").value;
        let date = document.getElementById("fecha_compra").value;
        console.log(date, dateAsiento);

        if (dateAsiento != '' && date != '') {

            if (dateAsiento != date) {

                alerta(`{{trans('contableM.fechanocoin')}}`, "error", "{{trans('turnerof.incorrecto')}}");

            }

        }



    }

    function obtener_caja() {

        var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url: "{{ route('caja.sucursal') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': id_sucursal
            },
            success: function(data) {


                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#punto_emision").empty();

                        $.each(data, function(key, registro) {
                            $("#punto_emision").append('<option value=' + registro.id + '>' +
                                registro.codigo_sucursal + '-' + registro.codigo_caja +
                                '</option>');

                        });
                    } else {
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data) {

            }
        })

    }
</script>
@endsection