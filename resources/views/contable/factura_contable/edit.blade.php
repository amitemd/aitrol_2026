@extends('contable.compra.base')
@section('action-content')
@php
use Sis_medico\De_Empresa;
$date = date('Y-m-d');
$h = date('Y-m', strtotime($date));
$deEmpresa = De_Empresa::getDatos($empresa->id);
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
@endphp
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
        min-width: 460px;
        _width: 460px !important;
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

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
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
</style>

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        location.href = "{{ route('fact_contable_index') }}";
    }
</script>

<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">
        </div>
    </div>
</div>
<div class="modal fade" id="modalpedido" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="modalpdf" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.COMPRA')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">{{trans('contableM.RegistroFacturadeCompra')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.NuevaFacturadeCompra')}}</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-6">
                            <div class="box-title"><b>{{trans('contableM.FACTURADECOMPRA')}}</b></div>
                        </div>
                        <div class="col-6" style="text-align: center;">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$compras->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$compras->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.editarasientodiario')}}
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                                <a class="btn btn-default btn-gray" data-remote="{{ route('compras.subirpdf',['id'=> $compras->id,'parametro'=>'2'])}}" data-toggle="modal" data-target="#modalpdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{trans('contableM.subir')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12">
                        <div class="header row">
                            <div class="col-md-2 col-xs-2 col-1 px-0">
                                <label class=" label_header">{{trans('contableM.tipocomprobante')}}</label>
                                <select name="tipo_comprobante" id="tipo_comprobante" class="form-control" disabled style="width: 100%;heigth: 22px">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($t_comprobante as $value)
                                    <option {{ $value->codigo == $compras->tipo_comprobante ? 'selected' : ''}} value="{{$value->codigo}}">{{$value->codigo}} - {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <input type="text" class="form-control" name="sucursal" id="sucursal" value="{{ $compras->sucursal }}" readonly>
                                    @if ($errors->has('sucursal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sucursal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}} ({{trans('contableM.empresa')}})</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="punto_emision" id="punto_emision" value="{{ $compras->punto_emision }}" readonly>
                                    @if ($errors->has('punto_emision'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('punto_emision') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header" style="padding-left: 0px">{{trans('contableM.estado')}}</label>
                                <input class="form-control col-md-12 col-xs-12" style="@if($compras->estado==0) background-color: red; @else background-color: green; @endif">
                            </div>

                            <div class="col-md-1 col-xs-1 px-1">
                                <label class=" label_header">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly @if(($compras->tipo==1)) value="COM-FA" @elseif($compras->tipo==2) value="COM-FACT" @else value="COM-FAS" @endif>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fecha')}}</label>
                                <div class="input-group col-md-12">
                                    <input class="form-control col-md-12 col-xs-12" id="fecha" type="date" name="fecha" value="{{$compras->fecha}}" readonly>
                                </div>
                            </div>
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header">{{trans('contableM.id')}}:</label>
                                <input class="col-md-12 col-xs-12 form-control" id="id_fc" value="{{$compras->id}}" name="id_fc" readonly>
                            </div>
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header">{{trans('contableM.asiento')}}: </label>
                                <input class="col-md-12 col-xs-12 form-control" id="ct_c.id_asiento_cabecera" name="id_asiento_cabecera" value="{{$compras->id_asiento_cabecera}}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-1 col-xs-2 px-1">
                                <label for="empresa" class="label_header">Electrónica</label>
                                <div class="input-group">
                                    <label class="switch">
                                        <input name="electros" onchange="changeStatus(this)" readonly class="electros" <?php $estado = '';
                                                                                                                        if ($empresa->electronica == 0) {
                                                                                                                            $estado = 'uncheked';
                                                                                                                        }
                                                                                                                        if ($deEmpresa != '') {
                                                                                                                            $estado = 'checked';
                                                                                                                        }
                                                                                                                        echo $estado; ?> id="toggleswitch" type="checkbox">
                                        <span class="slider round" readonly></span>
                                    </label>
                                </div>
                                <input type="hidden" name="tipo_electronica" readonly id="tipo_electronica" value="<?php $elec = $empresa->electronica;
                                                                                                                    if ($elec == 0) {
                                                                                                                        if ($deEmpresa != '') {
                                                                                                                            $elec = 1;
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        $elec = 1;
                                                                                                                    }
                                                                                                                    echo $elec; ?>">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.id')}}</label>
                                <input id="proveedor" type="text" class="form-control  " name="proveedor" value="{{$compras->proveedor}}" onchange="cambiar_proveedor()" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.proveedor')}}</label>
                                <select class="form-control" disabled style="width: 100%;" name="nombre_proveedor" id="nombre_proveedor">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($proveedor as $value)
                                    <option {{ $value->id == $compras->proveedor ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                <input id="direccion_proveedor" type="text" class="form-control  " name="direccion_proveedor" value="{{$compras->proveedorf->direccion}}" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="termino" class="label_header">{{trans('contableM.termino')}}</label>
                                </div>
                                <div class="col-md-12 px-1">
                                    <select id="termino" name="termino" class="form-control" disabled style="width:100%">
                                        @foreach($termino as $t)
                                        <option @if($compras->termino==$t->id) selected="selected" @endif value="{{$t->id}}">{{$t->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fechacaducidad')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_caducidad" type="date" class="form-control   col-md-12" name="f_caducidad" value="@if(!is_null($compras)){{$compras->f_caducidad}}@endif" readonly>
                                </div>
                            </div>
                            <!--
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">{{trans('contableM.OCompra')}}</label>
                                        <div class="input-group">
                                            <input id="o_compra" maxlength="30" type="text" class="form-control  " name="o_compra"
                                            value="" >
                                            <div class="input-group-addon " >
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('o_compra').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>-->
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.autorizacion')}}</label>
                                <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="@if(!is_null($compras)){{$compras->autorizacion}}@endif" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fechafacturacion')}}</label>
                                <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@if(!is_null($compras)){{$compras->f_autorizacion}}@endif" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.serie')}}</label>
                                <input id="serie" maxlength="25" type="text" class="form-control  " name="serie" onkeyup="agregar_serie()" value="@if(!is_null($compras)){{$compras->serie}}@endif" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.secuenciafactura')}}</label>
                                <input id="secuencia_factura" maxlength="30" type="text" class="form-control  " name="secuencia_factura" value="@if(!is_null($compras)){{$compras->secuencia_factura}}@endif" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 col-1 px-1">
                                <label class=" label_header">{{trans('contableM.creditotributario')}}</label>
                                <select name="credito_tributario" id="cred_tributario" class="form-control" disabled style="width: 100%; heigth: 22px">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($c_tributario as $value)
                                    <option {{ $value->codigo == $compras->credito_tributario ? 'selected' : ''}} value="$value->codigo"> {{$value->codigo}} - {{$value->nombre}}</option>
                                    <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-3 px-1">
                                <label class=" label_header">{{trans('contableM.aparecesri')}} &nbsp;
                                </label>
                                <div class="input-group col-md-12" style="text-align: center;">
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;" name="archivo_sri" @if($compras->archivo_sri==1) checked @endif disabled>
                                </div>

                            </div>

                            <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                            <input type="hidden" name="sucursal_final" id="sucursal_final">

                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <label class=" label_header">{{trans('contableM.concepto')}}</label>
                        <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion" value="{{$compras->observacion}}" readonly>
                    </div>
                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                                <th width="35%" class="" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <!-- cambio en select x imput x Af no salia los nombres -->
                            @foreach($compras->detalles as $x)
                            <tr class="wello">
                                <td style="max-width:100px;">
                                    <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <input type="text" name="nombre[]" class="form-control" style="width: -webkit-fill-available;" readonly value="{{$x->nombre}}">
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" readonly placeholder="Detalle del producto">{{$x->detalle}}</textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->cantidad}}" name="cantidad[]" required readonly>
                                </td>

                                <td id="tprecio" style="max-width:100px;">
                                    <input type="text" class="form-control pneto" style="width: 80%;height:20px;" name="precio[]" value="{{$x->precio}}" readonly>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->descuento_porcentaje}}" name="descpor[]" readonly>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{$x->descuento}}" name="desc[]" readonly>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="{{$x->extendido}}" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" readonly>
                                </td>
                                <td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" @if($x->iva==1) checked @endif disabled>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class=''>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}} {{$porcentajeIva}}%</td>
                                <td id="subtotal_12" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal_12}}@endif</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                <td id="subtotal_0" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal_0}}@endif</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">@if(!is_null($compras)){{$compras->descuento}}@endif</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal}}@endif</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{trans('contableM.tarifa')}} {{$porcentajeIva}}%</td>
                                <td id="tarifa_iva" class="text-right px-1">@if(!is_null($compras)){{$compras->iva_total}}@endif</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <!--<tr>
                            <td></td><td></td><td></td><td></td><td></td>
                            <td colspan="2" class="text-right">Transporte</td>
                            <td id="transporte" class="text-right px-1">0.00</td>
                            <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                        </tr>-->
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">@if(!is_null($compras)){{number_format($compras->total_final,2)}}@endif</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script>
    $(document).ready(function() {
        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });

        $('#archivo_sri').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
        $('#poseexml').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional

        });

        $('.iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

        $('.ice').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });

    function changeStatus(v) {
        if (!v.checked) {
            document.getElementById("tipo_electronica").value = 0;
            return
        }
        document.getElementById("tipo_electronica").value = 1;
    }
</script>
@endsection