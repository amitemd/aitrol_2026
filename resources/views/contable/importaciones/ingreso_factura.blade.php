@extends('contable.compras_pedidos.base')
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

    /* CSS CARGANDO */
    .loader-wrapper {
        width: 220px;
        height: 220px;
    }

    .swal2-container {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-direction: row;
        flex-direction: row;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 10px;
        background-color: transparent;
        z-index: 1060;
    }

    .loader {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        border: 34px solid #162534;
        border-top-color: #4bc8eb;
        border-bottom-color: #f13a8f;
        border-radius: 50%;
        animation: rotate 5s linear infinite;
    }

    .loader-inner {
        border-top-color: #36f372;
        border-bottom-color: #fff;
        animation-duration: 2.5s;
    }

    @keyframes rotate {
        0% {
            transform: scale(1) rotate(360deg);
        }

        50% {
            transform: scale(.8) rotate(-360deg);
        }

        100% {
            transform: scale(1) rotate(360deg);
        }
    }

    .cargando {
        text-align: center;
        color: white;
        font-size: 38px;
        text-transform: uppercase;
        font-weight: 700;
        font-family: inherit;
    }

    /* FIN CSS CARGANDO */
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<style>
    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }
</style>

<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.importaciones')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">{{trans('contableM.regisgastoadi')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.newgastoadi')}}</li>
        </ol>
    </nav>
    <!-- ANIMACION DE CARGANDO-->
    <div id="cargando" style="overflow-y: auto;">

    </div>
    <!-- FIN DE ANIMACION DE CARGANDO-->
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b></b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('importaciones.index')}}">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <input type="hidden" id="id_estado" name="id_estado">
                    <div class="col-md-12 col-xs-12 px-1">
                        <label class="">{{trans('contableM.buscadordeordenimporta')}}</label>
                        <br>
                        <select id="selecOrden" style="width:50%!important" class="form-control select2_cuentas select2-hidden-accessible" class="form-control" onchange="selecFactura()">
                            <option value="">{{trans('agenda.seleccione')}}...</option>
                            @foreach($cab->cruce as $value)

                            @foreach($value->compra->detalles as $det)
                            <option data-id_estado="{{$value->id}}" data-proveedor="{{$value->compra->proveedor}}" data-gasto="{{$det->id_gasto}}" data-precio="{{$det->precio}}" data-fecha="{{$value->compra->fecha}}" data-descuento="{{$det->descuento}}" data-total="{{$det->total}}" value="{{$value->compra->id}}">{{$value->compra->proveedor_da->nombrecomercial}} | {{$cab->observacion}}
                            </option>
                            @endforeach
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="header row">
                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="id_cab_imp" id="id_cab_imp" value="{{$id}}">
                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select class="form-control validar" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                        <option value="">{{trans('agenda.seleccione')}}...</option>
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
                                        <option value="">{{trans('agenda.seleccione')}}...</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="label_header" style="padding-left: 0px">{{trans('contableM.estado')}}</label>
                                <input class="form-control col-md-12 col-xs-12" style="background-color: green;">
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="FACT-IMP">
                            </div>


                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fecha')}}</label>
                                <div class="input-group col-md-12">
                                    <input class="form-control col-md-12 col-xs-12" id="fecha" type="date" name="fecha" onchange="cambiarFecha(this, 'fecha')" onblur="validarCierre(this);" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{date('Y-m-d')}}">
                                </div>
                            </div>

                            <div class="col-md-2 col-2 px-1">
                                <label class=" label_header">{{trans('contableM.aparecesri')}} &nbsp;
                                </label>
                                <div class="input-group col-md-12" style="text-align: center;">
                                    <input type="hidden" name="archivosri" id="archivosri" value="1">
                                    <input id="archivo_sri" name="archivo_sri" type="checkbox" value="1" checked class="flat-blue smr" style="position: absolute; opacity: 0;">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="label_header">{{trans('contableM.asiento')}}:</label>
                                <input class="col-md-12 col-xs-12 form-control" id="id_asiento" name="id_asiento" readonly>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.id')}}</label>
                                <div class="input-group">
                                    <input id="proveedor" type="text" class="form-control" data-id="23" name="proveedor" value="" readonly>
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="cambiar_proveedor()"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.proveedor')}}</label>

                                <select class="form-control select2_proveedor" style="width: 100%;" onchange="buscarDireccion(); llenarCampo();" name="nombre_proveedor" id="nombre_proveedor">

                                </select>
                            </div>
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                <div class="input-group">
                                    <input id="direccion_proveedor" type="text" class="form-control validar" name="direccion_proveedor">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = ''; cambiar_nombre_proveedor()"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.termino')}}</label>
                                <select class="form-control select2_cuentas validar" name="termino" id="termino" class="form-control" onchange="sumarFecha(this)">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($termino as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fechacaducidad')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_caducidad" type="date" class="form-control validar  col-md-12" name="f_caducidad" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class=" label_header">{{trans('contableM.autorizacion')}}</label>
                                <div class="input-group">
                                    <input id="autorizacion" type="text" class="form-control validar " name="autorizacion" value="">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fechafacturacion')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_autorizacion" type="date" class="form-control validar  col-md-12" name="f_autorizacion" onchange="revisarIvaPeriodo(), cambiarFecha(this, 'f_autorizacion')" onblur="validarCierre(this);" value="@php echo date('Y-m-d');@endphp" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.serie')}}</label>
                                <div class="input-group">
                                    <input id="serie" maxlength="25" type="text" class="form-control validar " name="serie" onblur="agregar_serie()" onkeyup="agregar_serie(); llenarCampo();">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.secuenciafactura')}}</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" class="form-control validar " name="secuencia_factura" onchange="ingresar_cero(); llenarCampo();">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>




                            <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">

                            <input type="hidden" name="sucursal_final" id="sucursal_final">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-2 col-xs-2 col-1 px-1">
                                <label class=" label_header">{{trans('contableM.creditotributario')}}</label>
                                <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas validar" style="width: 100%; height: 22px">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($c_tributario as $value)
                                    <option value="{{$value->codigo}}">{{$value->codigo}} - {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-xs-2 col-1 px-0">
                                <label class=" label_header">{{trans('contableM.tipocomprobante')}}</label>
                                <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas validar " style="width: 100%;height: 22px">
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($t_comprobante as $value)
                                    <option value="{{$value->codigo}}">{{$value->codigo}} - {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8 px-1">
                                <label class=" label_header">{{trans('contableM.concepto')}}</label>
                                <input autocomplete="off" type="text" class="form-control col-md-12 validar" name="observacion" id="observacion">
                            </div>
                        </div>
                    </div>

                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input name='contador_items' id='contador_items' type='hidden' value="1">
                    <input type="hidden" id="iva_global" value="">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="40%" class="" tabindex="0">{{trans('contableM.descripcionproducto')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="crearFila()" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">

                        </tbody>
                        <tfoot class=''>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right" id="textSubtotal">{{trans('contableM.subtotal')}}</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right" id="textIva">{{trans('contableM.TarifaPorcIva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardarImportacion(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
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
    });

    $('.select2_proveedor').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        ajax: {
            url: '{{route("importaciones.proveedores")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    function revisarIvaPeriodo() {
        var textSubtotal = $('#textSubtotal').html().replace(porcentajeIva + '%', '');
        var textIva = $('#textIva').html().replace(porcentajeIva + '%', '');
        var hoy = new Date();
        var fecha = new Date($('#f_autorizacion').val());
        var fechaMenos = new Date('2024-04-01')
        var dias = 1; // Número de días a agregar
        fecha.setDate(fecha.getDate() + dias);
        var fechaFormulario = new Date(fecha);
       
        hoy.setHours(0, 0, 0, 0);
        fechaFormulario.setHours(0, 0, 0, 0);
        // if (hoy > fechaFormulario) {
        // if (fecha2 > fecha1) {
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

        $('#iva_global').val(valorIva);

        let ids = document.querySelectorAll('.ids');
        for (let i = 0; i < ids.length; i++) {
            totalProducto(ids[i].value);
            
        }
        totalGlobal();
    }

    const buscarDireccion = () => {
        console.log("Buscar Direccion")
        let proveedor = document.getElementById('nombre_proveedor').value;
        $.ajax({
            type: 'get',
            url: "{{route('importaciones.direccion')}}",
            datatype: 'json',
            data: {
                'proveedor': proveedor
            },
            success: function(data) {
                if (data.status == "success") {
                    document.getElementById('direccion_proveedor').value = data.direccion;
                    document.getElementById('proveedor').value = data.id;
                }
            },
            error: function(data) {

            }
        })
    }

    const sumarFecha = (e) => {

        let day = e.options[e.selectedIndex].text;
        let date = new Date();
        let subday = day.substring(0, 2).trim();
        if (Number.isInteger(parseInt(subday))) {
            date.setDate(date.getDate() + parseInt(subday));
            var fechaImp = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            console.log(fechaImp);
            document.getElementById("f_caducidad").value = fechaImp;
            document.getElementById("f_caducidad").readOnly = true;
        } else {
            document.getElementById("f_caducidad").value = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            document.getElementById("f_caducidad").readOnly = false;
        }

    }

    const cargando = accion => {
        let divCargando = document.getElementById("cargando");
        if (accion == 1) {
            let content = `<div id="cargando_hijo" class="loader-wrapper">
                                <div class="loader">
                                    <div class="loader loader-inner"></div>
                                </div>
                                <div class="cargando">Cargando...</div>
                            </div>`;
            divCargando.classList.add("swal2-container", "swal2-center", "swal2-backdrop-show")

            $('#cargando').append(content);
        } else {
            let hijo = document.getElementById("cargando_hijo");
            divCargando.removeChild(hijo)
            divCargando.classList.remove("swal2-container", "swal2-center", "swal2-backdrop-show")
        }


    }


    const llenarCampo = () => {
        let proveedor = document.getElementById("nombre_proveedor");

        let serie = document.getElementById("serie").value;

        let serie_option = serie;

        //serie.value != '' ? serie = serie.options[serie.selectedIndex].text : '';

        let secuencia = document.getElementById("secuencia_factura").value;
        let value = '';

        proveedor.value != '' ? value = proveedor.options[proveedor.selectedIndex].text : '';

        if (value != '') {
            document.getElementById("observacion").value = `IMPORTACION # ${proveedor.value != ''? value : ''} ${serie_option.value != ''? serie : ''}-${secuencia}`
            //document.getElementById("observacion").value = 'FACT-COMPRA' + ' ' + value + ' ' + serie + ' ' + secuencia;
        }
        //llenarSerie();
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    const crearFila = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr class="prueba" >
                <td style="max-width:100px;">
                    <input  type="hidden" class="ids" value="${id}" />
                    <Input id="codigo${id}" type="hidden" name="codigo[]" class="codigo_producto" />
                    <select id="producto${id}" name="producto[]" class="form-control select2_productos" style="width:100%" required >
                        <option> </option>
                        @foreach($gastos as $value)
                        <option value="{{$value->id}}">{{$value->id}} | {{$value->nombre}}</option>
                        @endforeach

                    </select>
                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                    <input type="hidden" name="iva[]" class="iva" />
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto validar cant" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required>

                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                </td>
                <td>
                    <input onchange="valorDescuento(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" value="0.00" required>
                </td>
                <td>
                    <input onchange="totalProducto(${id}); cambia_check(${id})" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva${id}" value="1">
                    <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">
                    <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                    <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor">

                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_productos').select2({
            tags: false
        });
        id++;
        document.getElementById('contador_items').value = id;
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

    function cambia_check(id) {

        var check = document.getElementById("valoriva" + id);
        var check_af = document.getElementById("check_iva" + id);

        if (check.checked) {

            check_af.value = 1;
        } else {

            check_af.value = 0;
        }
        totalProducto(id)
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


        let prec_desc = precio - ((descpor / 100) * precio);
        let preXcantidad = (precio * cantidad)
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        let valoriva = 0;


        if ($('#valoriva' + id).prop('checked')) {
            valoriva = precioTotal * parseFloat(iva_global);
        }

        let total = precioTotal + valoriva;

        val_iva.value = valoriva.toFixed(2);
        desc.value = porcDescuento.toFixed(2);
        precioneto.value = precioTotal.toFixed(2);
        total_valor.value = total.toFixed(2);

        totalGlobal();

    }

    function valorDescuento(id) {
        let precio = document.getElementById("precio" + id).value;
        let cantidad = document.getElementById("cantidad" + id).value
        let porcDescuento = document.getElementById("descpor" + id)
        let desc = document.getElementById("desc" + id).value

        let precioXcantidad = precio * cantidad;

        let descuento = (desc * 100) / precioXcantidad;

        porcDescuento.value = descuento.toFixed(4);

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
            // 
            descuento += parseFloat(desc[i].value);

            iva += parseFloat(vliva[i].value);

            totv += parseFloat(tot_valor[i].value);

            if (parseFloat(check_iva[i].value) == 1) {
                subtotal12 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            } else {
                subtotal0 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            }
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

    const validarProducto = () => {
        let msj = "";
        let validate = false;
        let selectProductos = document.querySelectorAll(".select2_productos");
        let btn_guardar = document.getElementById('boton_guardar');
        if (selectProductos.length <= 0) {
            // cargando(0);
            alertas('error', 'Error!..', "{{trans('contableM.debetenerproduc')}}")
            $('#boton_guardar').prop("disabled", false);
            btn_guardar.style.display = 'initial'
            validate = true;
        } else {
            for (let i = 0; i < selectProductos.length; i++) {
                let td = selectProductos[i].parentElement;
                if (selectProductos[i].value == "") {
                    // cargando(0);
                    td.children[2].style.border = '1px solid red';
                    msj = "{{trans('contableM.nohaselecprod')}}";
                    alertas('error', 'Error!..', msj)
                    $('#boton_guardar').prop("disabled", false);
                    btn_guardar.style.display = 'initial'
                    validate = true;
                }
            }
        }

        return validate;
    }

    function guardarImportacion(e) {
        e.preventDefault();
        if (!validar_campos()) {
            var formulario = document.forms["crear_factura"];
            var observacion = formulario.observacion.value;
            var proveedor = formulario.proveedor.value;
            var secuencia = formulario.secuencia_factura.value;
            $('#boton_guardar').prop("disabled", true);
            // var serie = formulario.serie.value;
            var msj = "";
            if (observacion == "") {
                msj += "{{trans('contableM.porfallenecamobserva')}}<br/>";
            }
            if (secuencia == "") {
                msj += "{{trans('contableM.porfallenecampsecuencia')}}<br/>";
            }
            /* if (serie == "") {
                msj += "Por favor, Llene la serie de la factura<br/>";
            }*/
            if (msj != "") {
                alertas('error', 'Error!..', msj)
                $('#boton_guardar').prop("disabled", false);
            } else {
                if (!validarProducto()) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('gastosimportacion.store_factura')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#crear_factura').serialize(),
                        success: function(data) {

                            alertas(data.respuesta, data.titulos, data.msj)
                            if (data.respuesta == "success") {
                                document.getElementById('id_asiento').value = data.id_asiento;
                                $('#boton_guardar').prop("disabled", true);
                                setTimeout(function() {
                                    window.location.href = "{{route('importaciones.index')}}";
                                }, 1500)
                            } else {
                                $('#boton_guardar').prop("disabled", false);
                            }

                        },
                        error: function(data) {
                            $('#boton_guardar').prop("disabled", false);
                        }
                    });
                }
            }


        } else {
            //btn_guardar.style.display = 'initial'
            $('#boton_guardar').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }





    var atr = document.getElementById("selecOrden");

    atr.onchange = function(event) {
        var datas = event.target.options[event.target.selectedIndex].dataset;
        var proveedor = datas.proveedor;
        var precio = datas.precio;
        var id_gasto = datas.gasto;
        var id_estado = datas.id_estado;

        document.getElementById("id_estado").value = id_estado;

        $("#proveedor").val(proveedor);
        $("#proveedor").change();

        $("#producto1").val(id_gasto);

        $(`#nombre_proveedor option[value='${proveedor}']`).prop('selected', true).trigger('change');
        $(`#producto1 option[value='${id_gasto}']`).prop('selected', true).trigger('change');

        //$(".select2_cuentas").trigger("change");

        $("#precio1").val(datas.precio)
        document.getElementById("total").innerHTML = datas.total;


    };
</script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    $(document).ready(function() {

        limpiar();

        crearFila();

        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });

        $('#archivo_sri').iCheck({
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

    $('.smr').on('ifChecked', function(event) {
        $("#archivosri").val(1);
    });
    $('.smr').on('ifUnchecked', function(event) {
        $("#archivosri").val(0);
    });

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

    const cambiarFecha = (e, type) => {
        if (type == "f_autorizacion") {
            document.getElementById("fecha").value = e.value;
        } else if (type == "fecha") {
            document.getElementById("f_autorizacion").value = e.value;
        }
        return;
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
                document.getElementById("f_autorizacion").value = fechaFormateada;
                document.getElementById("fecha").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }



    $('body').on('click', '.delete', function() {
        //

        $(this).parent().parent().remove();
        //totales(0);
    });
    $('body').on('click', '.form', function() {
        //

        //$(this).parent().parent().remove();
        //totales(0);
    });

    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }

    function crea_td(contador) {

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = `
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigo${id}" class="codigo form-control" id="codigo${id}" onchange="cambiar_codigo(${id})"/>
                        </td>
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigoref${id}" class="form-control" id="codigoref${id}"/>
                        </td>
                        <td>
                            <input type="hidden" id="visibilidad${id}" name="visibilidad${id}" value="1">
                            <input name="nombre${id}"  class="nombre form-control" id="nombre${id}" onchange="cambiar_nombre(${id})" style="width: 100%; height: 80%;">
                        </td>
                        <td>
                            <select class="form-control select2" id="bodega${id}" required name="bodega${id}" style="width: 100%; height: 78%;">
                            @foreach($bodega as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control cant" style=" width: 100%; height: 80%;" type="text" id="cantidad${id}" required value="0.00" onchange="total_calculo(${id}); redondea_cantidad(this, ${id},2);" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="cantidad${id}">
                        </td>
                        <td>
                            <select class="form-control" id="empaque${id}" name="empaque${id}" style="width: 100%; height: 80%;">
                                <option value="unidad">Unidad</option>
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" id="precio${id}" name="precio${id}" value="0.00" onchange="total_calculo(${id}); validarprecio(this,${id}); " onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="desc_porcentaje${id}" id="desc_porcentaje${id}" onchange="total_calculo(${id})" value="0.00" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="redondea_descuento(this,${id},2);" style="width: 100%; height: 80%;">
                        <td>
                            <input class="form-control" type="text" name="desc${id}" id="desc${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="desc_${id}" id="desc_${id}" >
                        </td>
                        <td>
                            <input class="form-control" name="extendido${id}" id="extendido${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="extendido_${id}" id="extendido_${id}">
                            <input type="hidden" name="ivaver${id}" id="ivaver${id}">
                        </td>
                        <td>
                            <input type="checkbox" id="iva${id}" name="iva${id}" onchange="suma_totales(${id}); ivas(${id});" value="1">
                        </td>
                        <td>
                            <input type="checkbox" id="ice${id}" name="ice${id}" value="1">
                        </td>
                        <td>
                            <button id="eliminar${id}" type="button" onclick="javascript:eliminar_registro(${id})" class="btn btn-danger btn-gray delete btn-xs">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>`;
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;

        $(".nombre").autocomplete({
            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('compra_nombre')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

        $(".codigo").autocomplete({

            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    url: "{{route('compra_codigo')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
        });

    }

    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#retenciones").val('0.00');
        $("#tabla_rubros").hide();
    }

    function eliminar_registro(valor) {
        var nombre2 = "visibilidad" + valor;
        var dato1 = "dato" + valor;
        $("#visibilidad" + valor).val(0);
        //document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display = 'none';
        suma_totales();
    }


    function goBack() {
        window.history.back();
    }


    function ivas(id) {
        $("#ivaver" + id).val(1);
        //
    }


    function validarprecio(elemento, id) {

        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()

        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "{{trans('contableM.debeingrecodnomcantprodu')}}", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }

        var prec = elemento.value;
        if (prec.length == 0) {

            swal("¡Error!", "{{trans('contableM.ingresedenuevo')}}", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            total_calculo(id);
            //suma_totales();
            return false;
        }

        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("{{trans('contableM.precionopermi')}}");
            $('#precio' + id).val("0");
            return false;
        }
        return true;
    }



    $('input[name=numero_debito]').change(function() {
        swal(this);
    });




    function redondea_cantidad(elemento, id, nDec) {

        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
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
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
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



    function total_calculo(id) {

        total = 0;
        descuento_total = 0;
        dec = 0;
        cantidad = parseInt($("#cantidad" + id).val());
        precio = parseFloat($("#precio" + id).val());
        descuento = parseFloat($("#desc_porcentaje" + id).val());

        //alert(descuento);
        total = cantidad * precio;
        descuento_total = (total * descuento) / 100;
        $('#desc_' + id).val(descuento_total.toFixed(2));
        dec = parseFloat($("#desc_" + id).val());
        //alert(descuento_total);
        $('#desc' + id).val(descuento_total.toFixed(2));
        $('#extendido' + id).val(total.toFixed(2));
        $('#extendido_' + id).val(total.toFixed(2));
        //
        suma_totales();
    }

    function suma_totales() {

        contador = 0;
        iva1 = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        trans = 0;
        subtotal_0 = 0;
        subtotal_12 = 0;
        base_imponible = 0;
        var iva_par = $('#iva_par').val();
        //var iva_par = $('#iva_par').val();
        var prop = "";
        $("#det_recibido tr").each(function() {
            //$(this).find('td')[0];

            cantidad = parseInt($(this).find('#cantidad' + contador).val());
            valor = parseFloat($(this).find('#precio' + contador).val());
            descu = parseFloat($(this).find('#desc_' + contador).val());
            pre_neto = parseFloat($(this).find('#extendido_' + contador).val());
            visibilidad = parseInt($('#visibilidad' + contador).val());
            let iva_global = document.getElementById("iva_global").value

            total = pre_neto;

            //
            if (visibilidad == 1) {
                if ($('#iva' + contador).prop('checked')) {
                    // var iva_par = 0.12;
                    var iva_par = parseFloat(iva_global);
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "{{trans('contableM.ingresacodnomproser')}}", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }
                    //iva = sub * 0.12;

                    iva1 = pre_neto * iva_par;
                    iva = iva + iva1;

                } else {

                    prop = +"no entra en el iva";

                    if (total > 0) {
                        sub = sub + total;
                    }
                }

                /*if(iva == 1){
                    sub = sub + total;
                    iva = sub * 0.12;
                }*/
                //sub = sub + iva;
                if (descu > 0) {
                    descu1 = descu1 + descu;
                }




            }
            contador = contador + 1;

        });






        //iva = (sub- descu) * iva_par;

        base_imponible = sub;

        //iva = sub * iva_par;
        //iva = subtotal_12 * iva_par;

        trans = parseInt($('#transporte').val());
        //total_fin = (sub - descu1)+trans+iva;


        //iva = (sub - descu1) * iva_par;




        //total_fin = (sub - descu1)+trans+iva;


        //total_fin = (sub - descu1);
        //iva = sub * 0.12;
        //total = sub + iva;

        /*if(iva == 1){
            subtotal_12 = subtotal_12 + total;
        }else{
            subtotal_0 = subtotal_0 + total;
        }*/

        if (trans > 0) {
            total_fin = sub + trans + iva;
        } else {
            total_fin = sub + iva;
        }
        if (descu1 > 0) {
            total_fin = total_fin - descu1;
        }

        $('#iva_final').val(iva.toFixed(2, 2));

        //
        $('#subtotal_final').val(sub.toFixed(2, 2));


        $('#descuento').val(descu1.toFixed(2, 2));


        $('#total_final').val(total_fin.toFixed(2, 2));


        $('#base_imponible').val(base_imponible);


        //Campos Oculto
        $('#subtotal1').val(sub);
        $("#total_b").html(total_fin.toFixed(2, 2));
        $("#subtotal_b").html(sub.toFixed(2, 2));
        $("#iva_b").html(iva.toFixed(2, 2));
        $("#descuento_b").html(descu1.toFixed(2, 2));
        $('#descuento1').val(descu1);
        $('#total1').val(total_fin);
        $('#base_imponible1').val(base_imponible);
        $('#transporte1').val(trans);
        //$('#total1').val(total_fin);

        //evaluar(); 401


    }


    function cambiar_nombre(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre" + id).val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#codigo' + id).val(data.value);
                    if (data.iva == '1') {
                        $('#ivaver' + id).val(1);
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                    }
                } else {
                    $('#codigo' + id).val(" ");
                }
            },
            error: function(data) {

            }
        })
    }


    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val(),
                'id_empresa': $("#id_empresa").val(),
            },
            success: function(data) {

                if (data.value != "no") {
                    $('#nombre' + id).val(data.value);
                    if (data.iva == '1') {
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                        $('#ivaver' + id).val(1);
                    }
                } else {
                    $('#nombre' + id).val(" ");
                }
            },
            error: function(data) {

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

    function cambiar_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#proveedor").val()
            },
            success: function(data) {

                if (data.value != "no") {
                    $('#nombre_proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#nombre_proveedor').val(" ");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#autorizacion').val("");
                }
            },
            error: function(data) {
                // 
            }
        })
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
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#f_caducidad').val(data.caducidad);
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

            }
        });
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
                swal("Error!", "{{trans('contableM.valornoper')}}", "error");
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
            swal("Error!", "{{trans('contableM.valornoper')}}", "error");
            $('#secuencia_factura').val('');
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
                swal("Error!", "{{trans('contableM.valornoper')}}", "error");
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
            swal("Error!", "{{trans('contableM.valornoper')}}", "error");
            $('#id_fc').val('');
        }
    }

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            swal("Error!", "Ingrese la serie correctamente: 000-000", "error");
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