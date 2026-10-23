@extends('contable.factura_contable.base')
@section('action-content')
@php
use Sis_medico\De_Empresa;
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
$date = date('Y-m-d');
$h = date('Y-m', strtotime($date));
$deEmpresa = De_Empresa::getDatos($empresa->id);
$mes = date('m');
$anio = date('Y');
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

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .cabecera {
        background-color: #9E9E9E;
        border-radius: 2px;
        top: 2px;
        color: white;
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

    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    .container:hover input~.checkmark {
        background-color: #ccc;
    }

    .container input:checked~.checkmark {
        background-color: #2196F3;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .container input:checked~.checkmark:after {
        display: block;
    }

    @-moz-keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1.0;
        }
    }

    .input-number {
        width: 80%;
        height: 20px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
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

    .legend {
        color: black;
        font-size: 15px;
        font-weight: bold;
        margin: 0px;
    }
</style>

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8) {
            return true;
        }
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        location.href = "{{ route('fact_contable_index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{ $date }}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content"></div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{ trans('contableM.contable') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('fact_contable_index') }}">Factura Contable</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Factura Contable</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="box-title col-md-6 "><b style="font-size: 16px;">CREAR FACTURA CONTABLE</b></div>
                <div class="col-md-12" style="display:flex; justify-content: space-between;">
                    <div class="col-md-2 text-left">
                        <fieldset>
                            <legend class="legend">Periodo</legend>
                            <div style="display: flex; justify-content: space-around;">
                                <input style="width: 50%;" id="periodo_mes" name="periodo_mes" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'mes');revisarIvaPeriodo();" type="text" class="form-control" value="{{$mes}}">
                                <input style="width: 50%;" id="periodo_anio" name="periodo_anio" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'anio');revisarIvaPeriodo();" type="text" class="form-control" value="{{$anio}}">
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-12" style="text-align: end; ">
                        <div style="bottom: 0px; position: absolute;">
                            <button type="button" class="btn btn-warning" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                            </button>
                            <a class="btn btn-danger" style="margin-left: 3px;" href="javascript:goBack()">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                            </a>
                            <a class="btn btn-success" href="javascript:crear_compra()" id="boton_guardar">
                                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body  dobra">
                <div class="header row">
                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="ivareal" id="ivareal" value="" >
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header" style="padding-left: 0px">{{ trans('contableM.estado') }}</label>
                                <input class=" form-control col-md-12 col-xs-12" style="background-color: green;">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.tipocomprobante') }}</label>
                                <select onchange="cambiarProve(this)" name="tipo_comprobante" id="tipo_comprobante" class="form-control select2_cuentas" style="width: 100%">
                                    <option value="">Seleccione...</option>
                                    @foreach ($t_comprobante as $value)
                                    <option value="{{ $value->codigo }}">{{ $value->codigo }} - {{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{ trans('contableM.sucursal') }}
                                        ({{ trans('contableM.empresa') }})</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select name="sucursal" id="sucursal" class="form-control" onchange="cargarSucursal(this)">
                                        <option value="">Seleccione</option>
                                        <?php
                                        foreach ($sucursales as $value) {
                                            $select = '';
                                            if ($value->codigo_sucursal == '100') {
                                                $select = 'selected';
                                            }
                                            echo '<option value="' . $value->id . '" ' . $select . '>' . $value->codigo_sucursal . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class=" label_header">P. Emision (Empresa)</label>
                                <select name="punto_emision" id="punto_emision" class="form-control">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="col-xs-1 px-1">
                                <label class="control-label label_header">{{ trans('contableM.tipo') }}</label>
                                <input id="" type="text" class="form-control" value="COM-FACT" disabled>
                                <input id="tipo" type="hidden" name="tipo" value="COM-FACT">
                            </div>
                            <div class="col-xs-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.fecha') }}</label>
                                <div class="input-group col-md-12">

                                    <input id="fecha" onchange="cambiarFecha(this, 'fecha')" type="date" class="form-control col-md-12" name="fecha" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-xs-1 px-1">
                                <label class="label_header" style="padding-left: 0px">{{ trans('contableM.id') }}</label>
                                <input class="col-md-12 col-xs-12 form-control" name="id_fc" id="id_fc" readonly>
                            </div>
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header" style="padding-left: 0px">{{ trans('contableM.asiento') }}</label>
                                <input class="col-md-12 col-xs-12 form-control" name="idasi" id="idasi" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <input name="electros" onchange="changeStatus(this)" class="electros" <?php $estado = '';
                                                                                                    if ($empresa->electronica == 0) {
                                                                                                        $estado = 'uncheked';
                                                                                                    }
                                                                                                    if ($deEmpresa != '') {
                                                                                                        $estado = 'checked';
                                                                                                    }
                                                                                                    echo $estado; ?> id="toggleswitch" type="hidden">
                            <input type="hidden" name="tipo_electronica" id="tipo_electronica" value="<?php $elec = $empresa->electronica;
                                                                                                        if ($elec == 0) {
                                                                                                            if ($deEmpresa != '') {
                                                                                                                $elec = 1;
                                                                                                            }
                                                                                                        } else {
                                                                                                            $elec = 1;
                                                                                                        }
                                                                                                        echo $elec; ?>">
                            <!--<div class="col-md-1 col-xs-2 px-1">
                                <label for="empresa" class="label_header">Electrónica</label>
                                <div class="input-group">
                                    <label class="switch">
                                        <input name="electros" onchange="changeStatus(this)" class="electros" <?php $estado = '';
                                                                                                                if ($empresa->electronica == 0) {
                                                                                                                    $estado = 'uncheked';
                                                                                                                }
                                                                                                                if ($deEmpresa != '') {
                                                                                                                    $estado = 'checked';
                                                                                                                }
                                                                                                                echo $estado; ?> id="toggleswitch" type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <input type="hidden" name="tipo_electronica" id="tipo_electronica" value="<?php $elec = $empresa->electronica;
                                                                                                            if ($elec == 0) {
                                                                                                                if ($deEmpresa != '') {
                                                                                                                    $elec = 1;
                                                                                                                }
                                                                                                            } else {
                                                                                                                $elec = 1;
                                                                                                            }
                                                                                                            echo $elec; ?>">
                            </div>-->
                            <div class="col-xs-2 px-0">
                                <label class="control-label label_header">{{ trans('contableM.id') }}</label>
                                <div class="input-group">
                                    <input id="proveedor" type="text" class="form-control" name="proveedor" value="" onchange="cambiar_proveedor()">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('proveedor').value = ''; cambiar_proveedor()"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-0">
                                <label class="control-label label_header">{{ trans('contableM.proveedor') }}</label>
                                <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor(); automatizar();" name="nombre_proveedor" id="nombre_proveedor">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-1 px-1">
                                <label class="control-label label_header">{{ trans('contableM.OCompra') }}</label>
                                <div class="input-group">
                                    <input id="o_compra" maxlength="30" type="text" class="form-control" name="o_compra" value="">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('o_compra').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.fechafacturacion') }}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_autorizacion" onchange="cambiarFecha(this, 'fecha_auto')" type="date" class="form-control col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d'); @endphp">
                                </div>
                            </div>
                            <div class="col-md-1 col-xs-1 px-0">
                                <label class="control-label label_header">{{ trans('contableM.termino') }}</label>
                                <select onchange="fecha_vencimiento(this)" class="form-control select2_cuentas" name="termino" id="termino" class="form-control ">
                                    <option value="">Seleccione...</option>
                                    @foreach ($termino as $value)
                                    <option value="{{ $value->id }}" data-dias="{{ $value->dias }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha Vencimiento</label>
                                <input id="f_caducidad" type="date" class="form-control" name="f_caducidad" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xs-1 px-1">
                                <label class="control-label label_header">{{ trans('contableM.divisass') }}</label>
                                <select name="divisas" id="divisas" class="form-control">
                                    @foreach ($divisas as $value)
                                    <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 20px">
                        <div class="row">
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Pedido No.</label>
                                <input id="num_pedido" type="text" class="form-control col-md-12" name="num_pedido" value="">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.autorizacion') }}</label>
                                <div class="input-group">
                                    <input id="autorizacion" type="text" class="form-control" name="autorizacion" value="" maxlength="49">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-0">
                                <label class="control-label label_header">{{ trans('contableM.serie') }}</label>
                                <div class="input-group">
                                    <input id="serie" maxlength="25" type="text" class="form-control" name="serie" onkeyup="agregar_serie()" onblur="agregar_serie()">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-0">
                                <label class="control-label label_header">{{ trans('contableM.secuenciafactura') }}</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" class="form-control" name="secuencia_factura" onchange="ingresar_cero(); automatizar();">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.creditotributario') }}</label>
                                <select name="credito_tributario" id="credito_tributario" class="form-control select2_cuentas" style="width: 100%">
                                    <option value="">Seleccione...</option>
                                    @foreach ($c_tributario as $value)
                                    <option value="{{ $value->codigo }}">{{ $value->codigo }}-{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-2 px-0" style="display: none;">
                                <label for="id_empresa" class="control-label label_header">{{ trans('contableM.empresa') }}</label>
                                <select class="form-control " name="id_empresa" id="id_empresa" onchange="obtener_sucursal()" required>
                                    @foreach ($empresa_general as $value)
                                    <option {{ $empresa->id == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->razonsocial }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-2 px-1">
                                <label class="control-label label_header">{{ trans('contableM.aparecesri') }} &nbsp;
                                </label>
                                <div class="input-group col-md-12" style="text-align: center;">
                                    <input type="hidden" name="archivosri" id="archivosri" value="1">
                                    <input id="archivo_sri" name="archivo_sri" type="checkbox" value="1" checked class="flat-blue smr">
                                </div>
                            </div>
                            <div class="col-xs-9 px-1">
                                <label class="control-label label_header">{{ trans('contableM.concepto') }}: </label>
                                <input type="text" class="form-control col-md-12 col-xs-6" name="observacion" id="observacion" required>
                            </div>
                            <div class="col-xs-3 px-1">
                                <label class="control-label label_header">{{ trans('contableM.formasdepago') }}: </label>
                                <select class="form-control select2_cuentas" style="width: 100%;" id="forma_pago" name="forma_pago">
                                    <option value="">Seleccione...</option>
                                    <option value="01">01 - SIN UTILIZACION DEL SISTEMA FINANCIERO</option>
                                    <option value="20">20 - OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO</option>
                                </select>
                            </div>
                            <input type="hidden" name="sucursal_final" id="sucursal_final">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="35%" class="" tabindex="0">
                                    {{ trans('contableM.DescripciondelProducto') }}
                                </th>
                                <th width="10%" class="" tabindex="0">{{ trans('contableM.cantidad') }}
                                </th>
                                <th width="10%" class="" tabindex="0">{{ trans('contableM.precio') }}
                                </th>
                                <th width="10%" class="" tabindex="0">% {{ trans('contableM.prctdesc') }}
                                </th>
                                <th width="10%" class="" tabindex="0">{{ trans('contableM.descuento') }}
                                </th>
                                <th width="10%" class="" tabindex="0">{{ trans('contableM.precioneto') }}
                                </th>
                                <th width="5%" class="" tabindex="0">{{ trans('contableM.iva') }}</th>
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <tr class="wello">
                                <td style="max-width:100px;">
                                    <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select name="nombre[]" id="control1" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                        <option> </option>
                                        @foreach ($cuentas as $value)
                                        <option value="{{ $value->nombre }}" data-name="{{ $value->nombre }}" data-codigo="{{ $value->id }}" data-descuento="{{ $value->mod_desc }}" data-precio="{{ $value->mod_precio }}" data-maxdesc="{{ $value->descuento }}" data-iva="{{ $value->iva }}">{{ $value->id }} | {{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle de la cuenta"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="0" name="cantidad[]" id="cantidad_0" required>
                                </td>
                                <td id="tprecio" style="max-width:100px;">
                                    <input type="text" class="form-control pneto" id="precio_0" style="width: 80%;height:20px;" name="precio[]" placeholder="0.00" onblur="calcularCompra(0)" value="0.00">
                                </td>
                                <td>
                                    <input id="porc_0" class="form-control text-right" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="0" name="descpor[]" required>
                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right" onchange="descuentoCal(this)" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" id="descuento_0" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" id="total_0" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form checkiv" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class=''>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right" id="textSubtotal">{{ trans('contableM.subtotal') }} </td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{ trans('contableM.subtotal0') }}%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{ trans('contableM.descuento') }}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">{{ trans('contableM.subtotal') }}</td>
                                <td id="base" class="text-right px-1">0.00</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right" id="textIva">{{ trans('contableM.tarifa') }} </td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>{{ trans('contableM.total') }}</strong>
                                </td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset('/js/icheck.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    var filaCompra = 0;
    var valorIva = '{{$valorIva}}';
    var porcentajeIva = '{{$porcentajeIva}}';
    $(document).ready(function() {
        var textSubtotal = $('#textSubtotal').html() + porcentajeIva + '%';
        var textIva = $('#textIva').html() + porcentajeIva + '%';
        $('#ivareal').val(valorIva);
        $('#textSubtotal').html(textSubtotal);
        $('#textIva').html(textIva);
        document.getElementById("tipo_electronica").value = 1
        limpiar();
        $('.select2_cuentas').select2({
            tags: false
        });
        $("#nombre_proveedor").select2({
            width: "element",
            noResults: function() {
                return "No hay resultado";
            },
            searching: function() {
                return "Buscando..";
            },
            placeholder: "--Seleccione--",
            minimumInputLength: 3,
            ajax: {
                url: "../../compras/buscarProveedor",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "post",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },
                error(error) {
                    console.log(error);
                },
                cache: true,
            },
        });
        $('#archivo_sri').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%'
        });
        $('.iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%'
        });
        $('.ice').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%'
        });
        $('#busqueda').click(function(event) {
            id = document.getElementById('contador').value;
            var midiv = document.createElement("tr");
            midiv.setAttribute("id", "dato" + id);
            midiv.innerHTML = '<td><input   style=" width: 100%; height: 80%;" name="codigo' + id + '" class="codigo form-control" id="codigo' + id + '" onchange="cambiar_codigo(' + id + ')"/></td> <td><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input name="nombre' + id + '" class="nombre form-control" id="nombre' + id + '" onchange="cambiar_nombre(' + id + ')" style="width: 100%; height: 80%;"></td> <td> <input name="detalle' + id + '" class="form-control" id="detalle' + id + '"  style="width: 100%; height: 80%;"></td><td><input class="form-control" type="text" id="precio' + id + '" name="precio' + id + '" value="0.00" onkeyup="total_calculo(' + id + ');" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;"  onchange="validarprecio(this,' + id + '); redondea_precio(this,' + id + ',2)"></td><td> <input class="form-control" type="text" name="desc_porcentaje' + id + '" id="desc_porcentaje' + id + '" onkeyup="total_calculo(' + id + ')" value="0.00" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="redondea_descuento(this,' + id + ',2);" style="width: 100%; height: 80%;"><td> <input class="form-control" type="text" name="desc' + id + '" id="desc' + id + '" value="0" style="width: 100%; height: 80%;" disabled> <input type="text" class="hidden" name="desc1' + id + '" id="desc1' + id + '" ></td> <td> <input class="form-control" name="extendido' + id + '" id="extendido' + id + '" value="0" style="width: 100%; height: 80%;" readonly > <input type="text" class="hidden" name="extendido_' + id + '" id="extendido_' + id + '"> </td> <td> <input type="checkbox" id="iva' + id + '" name="iva' + id + '" onchange="suma_totales(' + id + ')" value="1"> </td> <td> <input type="checkbox" id="ice' + id + '" name="ice' + id + '" value="1"> </td> <td><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
            document.getElementById('det_recibido').appendChild(midiv);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;
            $("#visibilidad" + id).val("1");
            $(".nombre").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('fact_contable_nombre') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 3,
            });
            $(".codigo").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('fact_contable_codigo') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 1,
            });
        });
    });

    function revisarIvaPeriodo() {
        var textSubtotal = $('#textSubtotal').html().replace(porcentajeIva + '%', '');
        var textIva = $('#textIva').html().replace(porcentajeIva + '%', '');
        var hoy = new Date();
        var mesPeriodo = parseFloat($('#periodo_mes').val());
        var fechaFormulario = new Date($('#periodo_anio').val() + '-' + mesPeriodo + '-01');
        var fechaMenos = new Date('2024-04-01');
        hoy.setHours(0, 0, 0, 0);
        fechaFormulario.setHours(0, 0, 0, 0);
        //if (hoy> fechaMenos) {
        if (fechaFormulario < fechaMenos) {
            valorIva = '0.12';
            porcentajeIva = '12';
            console.log(1);
        } else {
            valorIva = '{{$valorIva}}';
            porcentajeIva = '{{$porcentajeIva}}';
            console.log(2);
        }
        $('#textSubtotal').html('');
        $('#textIva').html('');
        $('#textSubtotal').html(textSubtotal + porcentajeIva + '%');
        $('#textIva').html(textIva + porcentajeIva + '%');
        $('#ivareal').val(valorIva);
        totales(0);
    }

    function automatizar() {
        let nombre_proveedor = document.getElementById("nombre_proveedor");
        let nombre = nombre_proveedor.options[nombre_proveedor.selectedIndex].text;
        let id_serie = document.getElementById('serie').value;
        let secuencia_factura = document.getElementById("secuencia_factura").value;
        observacion.value = `${nombre} - ${id_serie} - ${secuencia_factura}`;
    }
    const fecha_vencimiento = (e) => {
        const d = new Date(document.getElementById("f_autorizacion").value);
        let f_caducidad = document.getElementById("f_caducidad");
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
    const validarFechasPeriodo = () => {
        let periodo_mes = document.getElementById("periodo_mes").value;
        let periodo_anio = document.getElementById("periodo_anio").value;
        let f_autorizacion = document.getElementById("f_autorizacion").value;
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
        if (type == "fecha") {
            document.getElementById("f_autorizacion").value = e.value;
        } else if (type == "fecha_auto") {
            document.getElementById("fecha").value = e.value;
        }
        return;
    }

    function __________fecha_vencimiento(e) {
        let day = e.options[e.selectedIndex].text;
        let date = new Date(document.getElementById("fecha").value);
        let subday = day.substring(0, 2).trim();
        if (Number.isInteger(parseInt(subday))) {
            date.setDate(date.getDate() + parseInt(subday));
            let fechaImp = date.toISOString().slice(0, 10);
            console.log(fechaImp);
            document.getElementById("f_caducidad").value = fechaImp;
            document.getElementById("f_caducidad").readOnly = true;
        } else {
            document.getElementById("f_caducidad").value = date.toISOString().slice(0, 10);
            document.getElementById("f_caducidad").readOnly = false;
        }
    }
    var fila = $(".wello").html();

    $('.smr').on('ifChecked', function(event) {
        $("#archivosri").val(1);
    });
    $('.smr').on('ifUnchecked', function(event) {
        $("#archivosri").val(0);
    });

    function nuevo_comprobante() {
        location.href = "{{ route('fact_contable_crear') }}";
    }
    var contadorGeneral = 0;

    function crear_compra() {
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var observacion = $("#observacion").val();
        var autorizacion = $("#autorizacion").val();
        var credito_tributario = $("#credito_tributario").val();
        var tipo_comprobante = $("#tipo_comprobante").val();
        var proveedor = $("#proveedor").val();
        var secuencia_factura = $("#crear_factura").val();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        var proveedor = formulario.proveedor.value;
        var credito_tributario = formulario.credito_tributario.value;
        var secuencia = formulario.secuencia_factura.value;
        var termino = formulario.termino.value;
        var op_compra = formulario.o_compra.value;
        var fecha = formulario.fecha.value;
        var f_autorizacion = formulario.f_autorizacion.value;
        var f_caducidad = formulario.f_caducidad.value;
        var secuencia_factura = formulario.secuencia_factura.value;
        var sucursal = $("#sucursal").val();
        var pemision = $("#punto_emision").val();
        var serie = formulario.serie.value;
        var msj = "";
        if (tipo_comprobante == '03')
            document.getElementById("tipo_electronica").value = 1;
        if (observacion == "") {
            msj += "Por favor, Llene el campo observación<br/>";
        }
        if (termino == "") {
            msj += "Por favor, Llene el campo término<br/>";
        }
        if (credito_tributario == "") {
            msj += "Ingrese credito tributario<br/>";
        }
        if (secuencia == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene el campo de secuencia<br/>";
        }
        if (forma_pago == "" && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione una forma de pago<br/>";
        }
        if (op_compra == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene el campo de opción de compra<br/>";
        }
        if (tipo_comprobante != '03') {
            document.getElementById("tipo_electronica").value = 0;
        }
        if (tipo_comprobante == "") {
            msj += "Por favor, Llene el campo de tipo de comprobante<br/>";
        }
        if (fecha == "") {
            msj += "Por favor, Llene la fecha de la factura<br/>";
        }
        if (proveedor == "") {
            msj += "Por favor, Llene el proveedor<br/>";
        }
        if ((autorizacion.length != 10 && autorizacion.length != 49) && (tipo_comprobante != '03' && tipo_comprobante != '19')) {
            msj += "Por favor, El número de autorización debe contener 10 o 49 dígitos<br/>";
        }
        if (f_caducidad == "") {
            msj += "Por favor, Llene la fecha de caducidad de la factura<br/>";
        }
        if (sucursal == "" && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione la Sucursal<br/>";
        }
        if (pemision == "" && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione la Punto de emision<br/>";
        }
        if (fecha != f_autorizacion) {
            msj += " Las fechas no coinciden. <br/>";
        }
        if ($("#tipo_comprobante").val() != '03') {
            if (autorizacion == "") {
                msj += "Por favor, Llene el campo autorizacion<br/>";
            }
            if (serie == "") {
                msj += "Por favor, Llene la serie de la factura<br/>";
            }
            if (secuencia_factura == "") {
                msj += "Por favor, Llene la secuencia de la factura<br/>";
            }
        }
        let obsDetalle = document.getElementsByName("descrip_prod[]");
        let obsNombre = document.getElementsByName("nombre[]");
        let array = [];
        for (let index = 0; index < obsDetalle.length; index++) {
            if (obsNombre[index].value == '') {
                array.push(' Ingrese el nombre del producto ' + Number(index + 1));
            }
        }
        msj += array.join(" ");
        if (msj == "") {
            if (!validarFechasPeriodo()) {
                alertas("error", "Error en fechas", "La fecha del periodo con la fecha de facturacion no coinciden")
                return;
            }
            document.getElementById("boton_guardar").style.display = "none";
            if ($("#crear_factura").valid()) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('fact_contable_store') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#crear_factura").serialize(),
                    success: function(data) {
                        console.log(data);
                        if (data != '¡Error!, Coincidencia en las Facturas, ingrese otra factura con otros valores') {
                            secuencia_f(data[0]);
                            $("#idasi").val(data[1]);
                            if (confirm('¿Desea agregar retenciones?')) {
                                $.ajax({
                                    type: "get",
                                    url: "{{ route('retenciones_modal_retenciones') }}",
                                    data: {
                                        'id_proveedor': proveedor,
                                        'secuencia': secuencia_factura,
                                        'id_compra': data[0]
                                    },
                                    datatype: "html",
                                    success: function(datahtml, data) {
                                        console.log(data);
                                        document.getElementById("boton_guardar").style.display = "none";
                                        $("#content").html(datahtml);
                                        $("#calendarModal").modal("show");
                                        $('#crear_factura input').attr('readonly', 'readonly');
                                        $("#boton_guardar").attr("disabled", "disabled");
                                    },
                                    error: function() {
                                        document.getElementById("boton_guardar").style.display = "inline";
                                        swal('error al cargar');
                                    }
                                });
                            } else if (data == '¡Error!, Tiene que crear la cuenta factura contable desc comp') {
                                swal(`Incorrecto`,
                                    data, "error");
                            } else {
                                document.getElementById("boton_guardar").style.display = "none";
                                swal(`{{ trans('contableM.correcto') }}!`, "Factura guardada correctamente", "success");
                                $('#crear_factura input').attr('readonly', 'readonly');
                                $("#tipo_comprobante").attr('disabled', true);
                                $("#credito_tributario").attr('disabled', true);
                                $('select').prop('disabled', true);
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }
                        } else {
                            document.getElementById("boton_guardar").style.display = "inline";
                            swal("Error!", data, "error");
                        }
                    },
                    error: function(data) {
                        document.getElementById("boton_guardar").style.display = "inline";
                        console.log(data);
                    }
                });
            }
        } else {
            document.getElementById("boton_guardar").style.display = "inline";
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }
    }

    function secuencia_f(secuencia_factura) {
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#id_fc').val('');
            } else {
                var concadenate = parseInt(digitos - longitud);
                switch (longitud) {
                    case 1:
                        secuencia = '000000000';
                        break;
                    case 2:
                        secuencia = '00000000';
                        break;
                    case 3:
                        secuencia = '0000000';
                        break;
                    case 4:
                        secuencia = '000000';
                        break;
                    case 5:
                        secuencia = '00000';
                        break;
                    case 6:
                        secuencia = '0000';
                        break;
                    case 7:
                        secuencia = '000';
                        break;
                    case 8:
                        secuencia = '00';
                        break;
                    case 9:
                        secuencia = '0';
                }
                $('#id_fc').val(secuencia + secuencia_factura);
            }
        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#id_fc').val('');
        }
    }

    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
    }

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
        suma_totales();
    }

    function validarprecio(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val();
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "Debe ingresar el código, nombre del producto", "error");
            $('#precio' + id).val("0");
            $('#desc_porcentaje' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }
        var prec = elemento.value;
        if (prec.length == 0) {
            swal("Error!", "Precio no Permitido", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        if (numero < -1 || numero > 999999999) {
            swal("Error!", "Precio no Permitido", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "Debe ingresar el código, nombre del producto", "error");
            $('#precio' + id).val("0");
            $('#desc_porcentaje' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        if (numero < 0 || numero > 100) {
            swal("Error!", "Rango de descuento permitido (0% - 100%)", "error");
            $('#desc_porcentaje' + id).val('0');
            return false;
        }
        return true;
    }

    function total_calculo(id) {
        total = 0;
        descuento_total = 0;
        cantidad = 1;
        precio = parseFloat($("#precio" + id).val());
        descuento = parseFloat($("#desc_porcentaje" + id).val());
        total = cantidad * precio;
        descuento_total = (total * descuento) / 100;
        if (isNaN(descuento_total)) {
            descuento_total = 0;
        }
        $('#desc' + id).val(descuento_total.toFixed(2));
        $('#extendido' + id).val(total.toFixed(2, 2));
        console.log(id + 'chilan');
        $('#desc1' + id).val(descuento_total.toFixed(2, 2));
        $('#extendido_' + id).val(total.toFixed(2, 2));
        suma_totales();
    }

    function suma_totales() {
        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 1;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                valor = parseFloat($(this).find('#precio' + contador).val());
                descu = parseFloat($(this).find('#desc' + contador).val());
                pre_neto = parseFloat($(this).find('#extendido_' + contador).val());
                total = pre_neto;
                console.log(total);
                if ($('#iva' + contador).prop('checked')) {
                    var iva_par = valorIva;
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "Debe ingresar el código, nombre del Producto o Servicio", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }
                    if (descu > 0) {
                        iva1 = (pre_neto - descu) * (iva_par);
                        iva = iva + iva1;
                    } else {
                        iva1 = pre_neto * iva_par;
                        iva = iva + iva1;
                    }
                } else {
                    if (total > 0) {
                        sub = sub + total;
                    }
                }
                if (descu > 0) {
                    descu1 = descu1 + descu;
                }
            }
            contador++;
        });
        total_fin = parseFloat((sub - descu1) + iva);
        if (!isNaN(sub)) {
            $('#subtotal_final').val(sub.toFixed(2));
        }
        if (!isNaN(descu1)) {
            $('#descuento').val(descu1.toFixed(2));
        }
        if (!isNaN(iva)) {
            $('#iva_final').val(iva.toFixed(2));
        }
        if (!isNaN(total_fin)) {
            $('#total_final').val(total_fin.toFixed(2));
        }
        if (!isNaN(sub)) {
            $('#subtotal_b').html(sub.toFixed(2));
        }
        if (!isNaN(descu1)) {
            $('#descuento_b').html(descu1.toFixed(2));
        }
        if (!isNaN(iva)) {
            $('#iva_b').html(iva.toFixed(2));
        }
        if (!isNaN(total_fin)) {
            $('#total_b').html(total_fin.toFixed(2));
        }
        $('#subtotal_final1').val(sub.toFixed(2));
        if (descu1 == NaN) {
            decu1 = 0;
        }
        $('#descuento1').val(descu1.toFixed(2));
        $('#iva_final1').val(iva.toFixed(2));
        $('#total_final1').val(total_fin.toFixed(2));
    }

    function cambiar_nombre(id) {
        $.ajax({
            type: 'get',
            url: "{{ route('fact_contable_nombre2') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre" + id).val()
            },
            success: function(data) {
                $('#codigo' + id).val(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{ route('fact_contable_codigo2') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val()
            },
            success: function(data) {
                $('#nombre' + id).val(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    $("#proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_identificacion')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 1,
    });

    function validar_cheque() {
        var valor_efectivo = parseInt($("#valor_cheque").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var valor_efec = $("#valor_cheque").val();
        var validacion = calculartotales();
        if (valor_efectivo > valor_totalfinal) {
            swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_cheque").show();
            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                $('#valor_cheque').val('');
            }
        }
        if ((valor_efectivo < 0) || (valor_efectivo == 0)) {
            swal("Error!", "El valor ingresado es incorrecto", "error");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
        if (valor_efec > 0) {
            alert("error");
        }
    }

    function verificatotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales == valor_totalfinal) {
                return 'ok';
            } else {
                return false;
            }
        }
    }

    function validar_efectivo() {
        var valor_efectivo = parseInt($("#valor_efectivo").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if (validacion == 'ok') {
            if ((valor_efectivo > 0) && (valor_totalfinal > 0)) {
                if (valor_efectivo > valor_totalfinal) {
                    swal("El valor ingresado no puede ser mayor al valor Total");
                    $('#valor_efectivo').val('');
                }
            } else {
                swal("Valores no permitidos");
                $('#valor_efectivo').val('');
            }
        } else {
            swal("No cumple la suma con el total de la factura");
            $('#valor_efectivo').val('');
        }
    }

    function validar_tarj_credito() {
        var valor_efectivo = parseInt($("#valor_tarjetacredito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0) || (valor_efectivo == 0)) {
            swal("El valor ingresado es incorrecto");
            $('#valor_tarjetacredito').val('');
            $("#datos_tarjeta_credito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {
            swal("El valor ingresado no puede ser mayor al valor Total");
            $('#valor_tarjetacredito').val('');
        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_credito").show();
            } else {
                swal("No cumple la suma con el total de la factura");
                $('#valor_tarjetacredito').val('');
            }
        }
    }

    function validar_tarj_debito() {
        var valor_efectivo = parseInt($("#valor_tarjetadebito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0) || (valor_efectivo == 0)) {
            swal("El valor ingresado es incorrecto");
            $('#valor_tarjetadebito').val('');
            $("#datos_tarjeta_debito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {
            alert("El valor ingresado no puede ser mayor al valor Total");
            $('#valor_tarjetadebito').val('');
        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_debito").show();
            } else {
                alert("No cumple la suma con el total de la factura");
                $('#valor_tarjetadebito').val('');
            }
        }
    }

    function cambiar_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{ route('compra_buscar_proveedornombre') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#f_caducidad').val("");
                    $('#autorizacion').val("");
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function calculartotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales > valor_totalfinal) {
                return false;
            } else {
                return 'ok';
            }
        }
    }

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{ route('compra_buscar_proveedornombre') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#proveedor').val("");
                    $('#f_caducidad').val("");
                    $('#autorizacion').val("");
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function ingresar_cero() {
        var secuencia_factura = $('#secuencia_factura').val();
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#secuencia_factura').val('');
            } else {
                var concadenate = parseInt(digitos - longitud);
                switch (longitud) {
                    case 1:
                        secuencia = '00000000';
                        break;
                    case 2:
                        secuencia = '0000000';
                        break;
                    case 3:
                        secuencia = '000000';
                        break;
                    case 4:
                        secuencia = '00000';
                        break;
                    case 5:
                        secuencia = '0000';
                        break;
                    case 6:
                        secuencia = '000';
                        break;
                    case 7:
                        secuencia = '00';
                        break;
                    case 8:
                        secuencia = '0';
                        break;
                    case 9:
                        secuencia = '';
                }
                $('#secuencia_factura').val(secuencia + secuencia_factura);
            }
        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#secuencia_factura').val('');
        }
    }

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            swal("Error!", `Ingrese la serie correctamente: 000-000`, "error");
        }
    }

    function redondea_precio(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split(".");
        entero = arr[0];
        decimal = arr[1];
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#precio' + id).val(s);
            }
        } else {
            $('#precio' + id).val(n.toFixed(2, 2));
        }
    }

    function redondea_cantidad(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var en = String(d);
        arr = en.split(".");
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) >= 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#cantidad' + id).val(s);
            } else {
                $('#cantidad' + id).val(n.toFixed(2, 2));
            }
        }
        $('#cantidad' + id).val(s);
    }

    function redondea_descuento(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var en = String(n);
        arr = en.split(".");
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#desc_porcentaje' + id).val(s);
            } else {
                $('#desc_porcentaje' + id).val(n.toFixed(2, 2));
            }
        } else {
            $('#desc_porcentaje' + id).val(s);
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
                            $("#punto_emision").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#punto_emision").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function obtener_sucursal() {
        var id_seleccionado = $("#id_empresa").val();
        $.ajax({
            type: 'post',
            url: "{{ route('sucursal.empresa') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_emp': id_seleccionado
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (id_seleccionado != 0) {
                        $("#sucursal").empty();
                        $.each(data, function(key, registro) {
                            $("#sucursal").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '</option>');
                        });
                    } else {
                        $("#sucursal").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    $("#descargar_xml").change(function() {
        var file = document.getElementById("descargar_xml").files[0];
        console.log(file);
        var reader = new FileReader();
        reader.readAsText(file);
        reader.onloadend = function() {
            var xmlData = $(reader.result);
            var xmlDoc = $.parseXML(xmlData);
            var customers = $(xmlDoc).find("ambiente");
            $.each(xmlData, function() {
                var direccion = ($(this).find('comprobante dirEstablecimiento').text());
                $("#direccion_proveedor").val(direccion);
                var numero_autorizacion = ($(this).find('numeroAutorizacion').text());
                $("#autorizacion").val(numero_autorizacion);
                var secuencia_factura = ($(this).find('comprobante secuencial').text());
                $("#secuencia_factura").val(secuencia_factura);
                var nombreComercial = ($(this).find('comprobante nombreComercial').text());
                $("#nombre_proveedor").append('<option value="">' + nombreComercial + '</option>');
                var id_proveedor = ($(this).find('comprobante ruc').text());
                $("#proveedor").val(id_proveedor);
                var codigo = ($(this).find('comprobante totalImpuesto codigo').text()).substring(0,
                    1);
                console.log(codigo);
            });
        };
    });


    function calcularCompra(e) {
        pDescuento(e);
        var cant = $('#cantidad_' + e).val();
        var copago = 0;
        var descuento = $("#descuento_" + e).val();
        var total = (parseInt(cant) * parseFloat($('#precio_' + e).val())) - descuento - copago;
        $("#total_" + e).val(parseFloat(total).toFixed(2));
        totales(0);
    }

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");
        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().children().removeAttr("checked");
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) return signo * Math.round(num);
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        return true;
    }
    $('body').on('click', '.delete', function() {
        console.log($(this));
        $(this).parent().parent().remove();
        totales(0);
        filaCompra++;
    });
    $('body').on('blur', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $(document).on('click', 'agregar_cuentas *', function() {
        console.log(this);
    });
    $('body').on('click', '.checkiv', function() {
        if ($(this).is(":checked")) {
            console.log("aa");
            $(this).parent().prev().prev().prev().prev().prev().prev().find(".iva").val("1");
            console.log("chequeado");
        } else {
            console.log("deschequeado");
            $(this).parent().prev().prev().prev().prev().prev().prev().find(".iva").val("0");
        }
        totales(0);
    });
    $('body').on('active', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.copago', function() {
        verificar(this);
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();
        var copago = $(this).val();
        console.log("copago", copago);
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        console.log(total);
        $(this).parent().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.pdesc', function() {
        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();
        var pdesc = $(this).val();
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = 0;
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().children().val(total);
        totales(0);
    });

    function pDescuento(e) {
        var cant = $("#cantidad_" + e).val();
        var precio = $("#precio_" + e).val();
        var pdesc = $("#porc_" + e).val();
        console.log(cant, precio, pdesc);
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100;
        $("#descuento_" + e).val(descuento.toFixed(2));
        var copago = 0;
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $("total_" + e).val(total);
        totales(0);
    }
    $('body').on('change', '.desc', function() {
        var m = verificar(this);
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var descuento = $(this).val();
        verificar(this);
        var pdesc = 0;
        if (cant == 0 || precio == 0) {
            pdesc = 0;
        } else {
            pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
        }
        $(this).parent().prev().children().val(pdesc);
        var copago = 0;
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.fpago', function() {
        var total_pagos = 0;
        $('.fpago').each(function(i, obj) {
            total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        });
        $("#valor_totalPagos").val(total_pagos);
    });

    function totales(e) {
        var subt12 = [];
        var subt0 = [];
        var descuento = [];
        var descuentosub0 = 0;
        var descuentosub12 = 0;
        var sb12 = 0;
        var sb0 = 0;
        var d = 0;
        var copagoTotal = 0;
        if (e == 0) {
            $('.cneto').each(function(i, obj) {
                var cant = $(this).val();
                var e = $(this).parent().prev().children().closest(".select2_cuentas");
                var precio1 = 0;
                var precio2 = 0;
                var precio3 = 0;
                var precio4 = 0;
                var precio5 = 0;
                var precioAut = 0;
                var tipo = $("#tipo_cliente").val();
                var precio = $(this).parent().next().children().val();
                var copago = 0;
                var descuento = $(this).parent().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().children().prop('checked');
                precio = precio != null ? precio : 0;
                var total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
                if (iva) {
                    subt12.push(total);
                    sb12 = sb12 + total;
                    descuentosub12 += parseFloat(descuento);
                } else {
                    subt0.push(total);
                    sb0 = sb0 + total;
                    descuentosub0 += parseFloat(descuento);
                }
                copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                var descuento_total = descuentosub12 + descuentosub0;
                var sum = sb12 + sb0 - descuento_total;
                $("#base1").val(sum);
                $("#base").html(sum.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;
                console.log(ti);
                if (d > 0) {
                    if (sb12 > 0) {
                        ti = iva * (sb12 - descuentosub12);
                    }
                }
                ti = redondeafinal(ti);
                $("#tarifa_iva").html(ti.toFixed(2));
                var t = sb12 + sb0 + ti - d;
                var totax = sum + ti;
                totax = redondeafinal(totax);
                $("#total").html(totax.toFixed(2, 2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                sb12 = redondeafinal(sb12);
                $("#subtotal_121").val(sb12);
                sb0 = redondeafinal(sb0);
                $("#subtotal_01").val(sb0);
                d = redondeafinal(d);
                $("#descuento1").val(d);
                $("#tarifa_iva1").val(ti);
                $("#total1").val(totax);
                $("#totalc").val(copagoTotal.toFixed(2));
            });
        }
    }

    function nuevo() {
        filaCompra++;
        let nuevafila = `<td style="max-width:100px;"><Input type="hidden" name="codigo[]" class="codigo_producto" /><select name="nombre[]" id="control1" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)"><option> </option>@foreach ($cuentas as $value)<option value="{{ $value->nombre }}" data-name="{{ $value->nombre }}"data-codigo="{{ $value->id }}"data-descuento="{{ $value->mod_desc }}"data-precio="{{ $value->mod_precio }}"data-maxdesc="{{ $value->descuento }}"data-iva="{{ $value->iva }}">{{ $value->id }} | {{ $value->nombre }}</option>@endforeach</select><textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto"placeholder="Detalle de la cuenta"></textarea><input type="hidden" name="iva[]" class="iva" /></td><td><input class="form-control text-right cneto" type="text"style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(${filaCompra});"value="0" name="cantidad[]" id="cantidad_${filaCompra}" required></td><td id="tprecio" style="max-width:100px;"><input type="text" class="form-control pneto" id="precio_${filaCompra}"style="width: 80%;height:20px;" name="precio[]" placeholder="0.00"onblur="calcularCompra(${filaCompra})" value="0.00"></td><td><input id="porc_${filaCompra}" class="form-control text-right"type="text" style="width: 80%;height:20px;"onkeypress="return isNumberKey(event)"onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(${filaCompra});"value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden"style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"onblur="this.value=parseFloat(this.value).toFixed(0);calcularCompra(${filaCompra});" value="0"name="maxdesc[]" required></td><td><input class="form-control text-right"type="text" style="width: 80%;height:20px;"onkeypress="return isNumberKey(event)"onblur="this.value=parseFloat(this.value).toFixed(2);alcularCompra(${filaCompra});" " value="0"name="desc[]" id="descuento_${filaCompra}" required></td><td><input class="form-control px-1 text-right" type="text" style="height:20px;"onkeypress="return isNumberKey(event)" value="0.00"onblur="this.value=parseFloat(this.value).toFixed(2);calcularCompra(${filaCompra});" id="total_${filaCompra}" name="precioneto[]"required></td><td><input class="form checkiv" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]"></td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>`;
        let arr = document.getElementById("agregar_cuentas").insertRow(-1);
        arr.innerHTML = nuevafila;
        $('.select2_cuentas').select2({
            tags: false
        });

    }

    const swetAlertDate = () => {
        $("#termino").val('');
        $("#termino").trigger('change');
        let dateAsiento = document.getElementById("fecha").value;
        let date = document.getElementById("f_autorizacion").value;
        console.log(date, dateAsiento);
        if (dateAsiento != '' && date != '') {
            if (dateAsiento != date) {
                $("#fecha_alertas").empty();
                $("#fecha_alertas").append(` <label class="label label-danger">Fechas no coinciden</label>`);
            }
        }
    }
    const cargarSucursal = (e) => {
        console.log(e.value);
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{ route('caja.sucursal') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': e.value
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#punto_emision").empty();
                        $.each(data, function(key, registro) {
                            var select = '';
                            if (registro.codigo_caja == 100)
                                select = 'selected';
                            $("#punto_emision").append('<option value=' + registro.id + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#punto_emision").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function changeStatus(v) {
        if (!v.checked) {
            document.getElementById("tipo_electronica").value = 0;
            return
        }
        document.getElementById("tipo_electronica").value = 1;
    }

    function cambiarProve(val) {
        $.ajax({
            type: 'post',
            url: "{{ route('compras.buscador_master_tipos') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': val.value
            },
            success: function(data) {
                if (data) {
                    $('#nombre_proveedor').empty();
                    let newOption = [];
                    for (let index = 0; index < data.length; index++) {
                        newOption.push(new Option(data[index].razonsocial, data[index].id, false, false));
                    }
                    newOption.unshift(new Option('Seleccione', '', false, false));
                    $('#nombre_proveedor').append(newOption).trigger('change');
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
</script>
@endsection