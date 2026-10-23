@extends('contable.compra.base')
@section('action-content')
@php
use Sis_medico\De_Empresa;
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
$deEmpresa = De_Empresa::getDatos($empresa->id);
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

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
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

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* .form-control{
            background-color: #f5f5f5;
        }*/

    /* On mouse-over, add a grey background color */
    .container:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .text {
        color: white;
        padding: 10px;
        background-color: green;
        font-size: 15px;
        font-family: helvetica;
        font-weight: bold;
        text-transform: uppercase;
    }

    .parpadea {

        animation-name: parpadeo;
        animation-duration: 0.5s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;

        -webkit-animation-name: parpadeo;
        -webkit-animation-duration: 4s;
        -webkit-animation-timing-function: linear;
        -webkit-animation-iteration-count: infinite;
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

    @-webkit-keyframes parpadeo {
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

    @keyframes parpadeo {
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

    .select2 {
        width: 100% !important;
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
        location.href = "{{ route('compras_index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
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
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.COMPRA')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">Registro Factura de Compra</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Factura de Compra</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-9 col-6">

                            <div class="box-title"><b>FACTURA DE COMPRA</b></div>
                        </div>
                        <div class="col-md-2 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-3" style="text-align: center;">
                            <div class="row">

                                <button type="button" class="btn btn-success  btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                                <a class="btn btn-success btn-gray" href="javascript:crear_compra()" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">

                <div class="header row">
                    <div class="col-md-12">
                        <div class="header row">




                            <div class="col-md-2 col-xs-2  px-1">
                                <label class=" label_header">Buscar por Pedido</label>
                                <div class="input-group">
                                    <input id="pedido_nombre" type="text" class="form-control" name="pedido_nombre" data-remote="{{ route('pedido.modal')}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalpedido">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('pedido_nombre').value = ''; buscar_pedido();"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2  px-1">
                                <label class=" label_header">Buscar por Factura</label>
                                <div class="input-group">
                                    <input id="factura_nombre" type="text" class="form-control " name="factura_nombre" onchange="buscar_factura();">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('factura_nombre').value = ''; buscar_factura();"></i>
                                    </div>
                                </div>
                            </div>
                            @if(!is_null($iva_param))
                            <input type="text" name="ivareal" id="ivareal" class="hidden" value="{{$iva_param->iva}}">
                            @endif

                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header" style="padding-left: 0px">{{trans('contableM.estado')}}</label>
                                <input class="form-control col-md-12 col-xs-12" style="background-color: green;">
                            </div>

                            <div class="col-md-1 col-xs-1 px-1">
                                <label class=" label_header">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="FACT-COMPRA">
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Fecha</label>
                                <div class="input-group col-md-12">
                                    <input class="form-control col-md-12 col-xs-12" id="fecha" type="date" name="fecha" onchange="swetAlertDate()" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header">{{trans('contableM.id')}}:</label>
                                <input class="col-md-12 col-xs-12 form-control" id="id_fc" name="id_fc" readonly>
                            </div>
                            <div class="col-md-1 col-xs-1 px-1">
                                <label class="label_header">{{trans('contableM.asiento')}}:</label>
                                <input class="col-md-12 col-xs-12 form-control" id="id_asiento" name="id_asiento" readonly>
                            </div>
                            <div class="col-md-2 col-3 px-1">
                                <label class=" label_header">Aparece Archivo SRI &nbsp;
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
                                <label for="empresa" class="label_header">Electrónica</label>
                                <div class="input-group">
                                    <label class="switch">
                                        <input class="electros" <?php $estado = '';
                                                                if ($empresa->electronica == 0) {
                                                                    $estado = 'disabled';
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
                                                                                                                if ($deEmpresa != '') $elec = 1;
                                                                                                            } else {
                                                                                                                $elec = 1;
                                                                                                            }
                                                                                                            echo $elec; ?>">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="label_header">Id</label>
                                <div class="input-group">
                                    <input id="proveedor" type="text" class="form-control  " name="proveedor" value="" onchange="cambiar_proveedor()">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('proveedor').value = ''; cambiar_proveedor()"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class=" label_header">{{trans('contableM.proveedor')}}</label>

                                <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor(); llenarCampo()" name="nombre_proveedor" id="nombre_proveedor">
                                    <option value="">Seleccione...</option>
                                    @foreach($proveedor as $value)
                                    <option value="{{$value->id}}">{{$value->razonsocial}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                <div class="input-group">
                                    <input id="direccion_proveedor" type="text" class="form-control" name="direccion_proveedor">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = ''; cambiar_nombre_proveedor()"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">





                                <label class=" label_header">Término</label>
                                <select onchange="sumarFecha(this)" class="form-control select2_cuentas" name="termino" id="termino" class="form-control ">
                                    <option value="">Seleccione...</option>
                                    @foreach($termino as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">O. Compra</label>
                                        <div class="input-group">
                                            <input id="o_compra" maxlength="30" type="text" class="form-control  " name="o_compra"
                                            value="" >
                                            <div class="input-group-addon " >
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('o_compra').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>-->
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Fecha Caducidad</label>
                                <div class="input-group col-md-12">
                                    <input id="f_caducidad" type="date" class="form-control   col-md-12" name="f_caducidad" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Autorización</label>
                                <div class="input-group">
                                    <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Fecha Facturación</label>
                                <div class="input-group col-md-12">
                                    <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" onchange="swetAlertDate()" value="@php echo date('Y-m-d');@endphp">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Serie</label>
                                <div class="input-group">
                                    <input id="serie" maxlength="25" type="text" class="form-control" onchange="llenarCampo()" name="serie" onkeyup="agregar_serie()">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">Secuencia Factura</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero(); llenarCampo()">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 col-1 px-1">
                                <label class=" label_header">Credito Tributario</label>
                                <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " style="width: 100%; heigth: 22px">
                                    <option value="">Seleccione...</option>
                                    @foreach($c_tributario as $value)
                                    <option value="{{$value->codigo}}">{{$value->codigo}} - {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-xs-2 col-1 px-0">
                                <label class=" label_header">Tipo de Comprobante</label>
                                <select onchange="cambiarProve(this)" name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas " style="width: 100%;heigth: 22px">
                                    <option value="">Seleccione...</option>
                                    @foreach($t_comprobante as $value)
                                    <option value="{{$value->codigo}}">{{$value->codigo}} - {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                            <!--
                            <div class="col-md-12 col-xs-12 col-12 px-0">
                                <label for="id_empresa" class=" label_header">{{trans('contableM.empresa')}}</label>

                            </div>
                            <div class="col-md-4 col-xs-4 col-4 px-0">
                                <select class="form-control " name="id_empresa" id="id_empresa" onchange="obtener_sucursal()" required>
                                    @foreach($empresa_general as $value)
                                    <option {{$empresa->id == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->razonsocial}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 px-1">

                                <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($sucursales as $value)
                                    <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 px-1">

                                <select class="form-control" name="punto_emision" id="punto_emision" required>
                                    <option value="">Seleccione...</option>
                                </select>

                            </div>-->
                            <!--Punto de Emision-->
                            <input type="hidden" name="sucursal_final" id="sucursal_final">


                        </div>
                    </div>
                    <div class="col-md-4 px-1">
                        <label class=" label_header">Concepto</label>
                        <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion">
                    </div>
                    <div class="col-md-4 px-1">
                        <label class="label_header">Sucursal (Empresa)</label>
                        <select name="sucursal" id="sucursal" class="form-control" onchange="cargarSucursal(this)">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($sucursales as $value) {
                                $select = '';
                                if ($value->codigo_sucursal == '100')
                                    $select = 'selected';
                                echo '<option value="' . $value->id . '" ' . $select . '>' . $value->codigo_sucursal . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 px-1">
                        <label class=" label_header">P. Emision (Empresa)</label>
                        <select name="p_emision" id="p_emision" class="form-control">
                            <option value="">Seleccione</option>
                        </select>
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
                                <th width="35%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
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
                                    <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                        <option> </option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                        @endforeach

                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                    <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                        <option> </option>

                                        @foreach($bodega as $value)
                                        @if(!is_null($value))
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endif
                                        @endforeach



                                    </select>
                                </td>
                                <td id="tprecio" style="max-width:100px;">

                                    <input type="text" class="form-control pneto" style="width: 80%;height:20px;" name="precio[]" placeholder="0.00">
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                                </td>
                                <td>
                                    <button id="eliminar1" type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr style="display:none" id="mifila">






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
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
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
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
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
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
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
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
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
                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
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
                                <td></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
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
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    $(document).ready(function() {
        document.getElementById("tipo_electronica").value = 1
        $('#modalpedido').on('hidden.bs.modal', function() {
            $(this).removeData('bs.modal');
        });

        limpiar();

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

        /*
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");

            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {

            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
        if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        }
         $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
        */

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().children().removeAttr("checked");
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;

        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function agregarElement(data) {
        var id = "";
        var contador = 0;

        for (i = 0; i < data[3].length; i++) {

            var midiv = document.createElement("tr");
            var extendido = parseFloat(data[3][i].precio) * parseFloat(data[3][i].cantidad_total);
            {{--midiv.innerHTML = '<tr style="display:none" id="mifila"><td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" /><select  id="f' + contador + '" name="nombre[]" class="form-control select2s" style="width:100%" required onchange="verificar(this)"><option> </option>@foreach($productos as $value)<option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}"data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}" >{{$value->codigo}} | {{$value->nombre}}</option>@endforeach</select><textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea><input type="hidden" name="iva[]" class="iva" /></td><td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="' + data[3][i].cantidad_total + '" name="cantidad[]"  required><select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required ><option> </option>@foreach($bodega as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" value="' + data[3][i].precio + '" placeholder="0.00"></td><td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td><td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td><td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" value="' + extendido + '" required></td><td><input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled></td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>';--}}


            midiv.innerHTM = `<tr style="display:none" id="mifila">
                            <td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select id="f${contador}" name="nombre[]" class="form-control select2s" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>@foreach($productos as $value)<option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>@endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${data[3][i].cantidad_total}" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                    <option> </option>@foreach($bodega as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach
                                </select>
                            </td>
                            <td><input type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" value="${data[3][i].precio}" placeholder="0.00"></td>
                            <td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td>
                            <td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td>
                            <td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" value="${extendido}" required></td>
                            <td><input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled></td>
                            <td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                        </tr>`
            document.getElementById('agregar_cuentas').appendChild(midiv);
            //$(".select2s").val(data[3][i].codigo);
            //$(".select2s").select2().trigger('change');
            $("#f" + contador).val(data[3][i].codigo);
            $("#f" + contador).select2().trigger('change');
            contador++;
        }


        totales(0);

    }



    $('body').on('click', '.delete', function() {

        $(this).parent().parent().remove();
        totales(0);
    });
    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto
    $('body').on('blur', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('active', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        // verificar(this);
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

        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;

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
    $('body').on('change', '.desc', function() {
        var m = verificar(this);
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        /*if(pdesc> m){
            swal("El descuento no puede ser mayor a "+m+"%");
            $(this).val(m);
        }*/
        var descuento = $(this).val();
        verificar(this);

        var pdesc = 0;
        if (cant == 0 || precio == 0) {
            pdesc = 0;
        } else {
            pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
        }
        //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
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
                //aqui falta

                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                var descuento_total = descuentosub12 + descuentosub0;
                var sum = sb12 + sb0 - descuento_total;
                $("#base1").val(sum);
                $("#base").html(sum.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;

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
    //new change to round be
    //eduardo me dijo que quemara esas bodegas en totbodega
    function nuevo() {
        var nuevafila = ` <td style="max-width:100px;">
                                    <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                        <option> </option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <textarea required rows="3" name="descrip_prod[]"  class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                    <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                        <option> </option>
                                        @foreach($bodega as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>`;
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = nuevafila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }

    function crea_td(contador) {

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td><input   style=" width: 100%; height: 80%;" name="codigo' + id + '" class="codigo form-control" id="codigo' + id + '" onchange="cambiar_codigo(' + id + ')"/></td><td><input   style=" width: 100%; height: 80%;" name="codigoref' + id + '" class="form-control" id="codigoref' + id + '"/></td> <td><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input name="nombre' + id + '"  class="nombre form-control" id="nombre' + id + '" onchange="cambiar_nombre(' + id + ')" style="width: 100%; height: 80%;"></td><td><select class="form-control select2" id="bodega' + id + '" required name="bodega' + id + '" style="width: 100%; height: 78%;"> @foreach($bodega as $value) <option value="{{$value->id}}">{{$value->nombre}}</option>  @endforeach  </select> </td><td> <input class="form-control" style=" width: 100%; height: 80%;" type="text" id="cantidad' + id + '" required value="0.00" onchange="total_calculo(' + id + '); redondea_cantidad(this, ' + id + ',2);" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="cantidad' + id + '" > </td> <td> <select class="form-control" id="empaque' + id + '" name="empaque' + id + '" style="width: 100%; height: 80%;"> <option value="unidad">Unidad</option> </select></td><td><input class="form-control" type="text" id="precio' + id + '" name="precio' + id + '" value="0.00" onchange="total_calculo(' + id + '); validarprecio(this,' + id + '); " onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;"  ></td><td> <input class="form-control" type="text" name="desc_porcentaje' + id + '" id="desc_porcentaje' + id + '" onchange="total_calculo(' + id + ')" value="0.00" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="redondea_descuento(this,' + id + ',2);" style="width: 100%; height: 80%;"><td> <input class="form-control" type="text" name="desc' + id + '" id="desc' + id + '" value="0" style="width: 100%; height: 80%;" disabled> <input type="text" class="hidden" name="desc_' + id + '" id="desc_' + id + '" ></td> <td> <input class="form-control" name="extendido' + id + '" id="extendido' + id + '" value="0" style="width: 100%; height: 80%;" disabled > <input type="text" class="hidden" name="extendido_' + id + '" id="extendido_' + id + '"> <input type="hidden" name="ivaver' + id + '" id="ivaver' + id + '" > </td> <td> <input type="checkbox" id="iva' + id + '" name="iva' + id + '" onchange="suma_totales(' + id + '); ivas(' + id + ');" value="1"> </td> <td> <input type="checkbox" id="ice' + id + '" name="ice' + id + '" value="1"> </td> <td><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
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

    function crear_compra() {
        //$("#boton_guardar").attr("disabled", "disabled");
        let boton = document.getElementById('boton_guardar');
        boton.style.display = "none"
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var observacion = $("#observacion").val();
        var proveedor = $("#proveedor").val();
        var op_compra = $("#o_compra").val();
        var fecha_caducidad = $("#f_caducidad").val();
        var credito_tributario = $("#credito_tributario").val();
        var tipo_comprobante = $("#tipo_comprobante").val();
        var secuencia_factura = $("#secuencia_factura").val();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        //var credito_tributario = formulario.credito_tributario.value;
        //var proveedor = formulario.proveedor.value;
        var termino = formulario.termino.value;
        //var secuencia = formulario.secuencia_factura.value;
        var secuencia = $("#secuencia_factura").val();
        var nro_autorizacion = formulario.autorizacion.value;
        //var tipo_comprobante = formulario.tipo_comprobante.value;
        var fecha = formulario.fecha.value;
        var f_caducidad = formulario.f_caducidad.value;
        //var secuencia_factura = formulario.secuencia_factura.value;
        var serie = formulario.serie.value;
        var totaliva = $("#tarifa_iva1").val();
        var msj = "";
        if (observacion == "") {
            msj += "Por favor, Llene el campo concepto<br/>";
        }
        if (termino == "") {
            msj += "Por favor, Llene el término <br/>";
        }
        if (credito_tributario == "") {
            msj += "Ingrese credito tributario<br/>";
        }
        if (secuencia == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene el campo de secuencia<br/>";
        }
        if (tipo_comprobante == "") {
            msj += "Por favor, Llene el campo de tipo de comprobante<br/>";
        }
        if (fecha == "") {
            msj += "Por favor, Llene la fecha de la factura<br/>";
        }
        if (f_caducidad == "") {
            msj += "Por favor, Llene la fecha de caducidad de la factura<br/>";
        }
        if (secuencia_factura == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene la secuencia de la factura<br/>";
        }
        if (serie == "") {
            msj += "Por favor, Llene la serie de la factura<br/>";
        }
        if (nro_autorizacion == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene campo de número de autorización<br/>";
        }
        /*if (nro_autorizacion == "") {
            msj += "Por favor, Llene campo de número de autorización<br/>";
        }*/
        let obsDetalle = document.getElementsByName("descrip_prod[]");
        let obsBodega = document.getElementsByName("bodega[]");
        let array = [];
        let contador = obsDetalle.length;
        for (let index = 0; index < contador; index++) {
            if (obsDetalle[index].value == '') {
                array.push(' Ingrese el detalle del producto ' + Number(index + 1));
            }
            if (tipo_comprobante != '03') {
                if (obsBodega[index].value == '') {
                    array.push(' Ingrese la bodega ' + Number(index + 1));
                }
            }
        }
        if (array.length > 0) {
            msj += array.join(' ');
        }
        if (msj != "") {
            boton.style.display = "initial"
        }
        if (msj == "") {
            if ($("#crear_factura").valid()) {
                $.ajax({
                    type: 'post',
                    url: "{{route('compra_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {
                        if (data != '!Error!, Coincidencia en las Facturas, ingrese otra factura con otros valores') {
                            secuencia_f(data);
                            //$("#boton_guardar").attr("disabled", "disabled");
                            if (confirm('¿Desea agregar retenciones?')) {
                                $.ajax({
                                    type: "get",
                                    url: "{{route('retenciones_modal_retenciones')}}",
                                    data: {
                                        'id_proveedor': proveedor,
                                        'secuencia': secuencia_factura,
                                        'id_compra': data[0],
                                        'total_iva': totaliva
                                    },
                                    datatype: "html",
                                    success: function(datahtml, data) {

                                        $("#content").html(datahtml);
                                        $("#calendarModal").modal("show");
                                        $('#crear_factura input').attr('readonly', 'readonly');
                                        // $("#boton_guardar").attr("disabled", "disabled");
                                    },
                                    error: function() {
                                        alert('error al cargar');
                                    }
                                });
                            } else {
                                swal(`{{trans('contableM.correcto')}}!`, "Factura guardada correctamente", "success");
                                $("#id_fc").val(data.id);
                                $("#id_asiento").val(data.id_asiento);
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                                // $('#crear_factura input').attr('readonly', 'readonly');
                                //$("#boton_guardar").attr("disabled", true);
                                //$('select').prop('disabled', true);
                            }
                        } else {
                            swal("Informe", data, "error");
                        }
                    },
                    error: function(data) {
                        boton.style.display = "block"
                        swal("Error!", data, "error");
                    }
                });
            } else {
                boton.style.display = "initial";
            }
        } else {
            //$('#boton_guardar').removeAttr("disabled");
            boton.style.display = "initial";
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
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

    function ivas(id) {
        $("#ivaver" + id).val(1);
    }

    function validarcantidad(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Debe ingresar el código", "Nombre del producto", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0) {
            swal("Cantidad no Permitida", "Por favor ingrese de nuevo", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        if (numero < -1 || numero > 999999999) {
            swal("Cantidad no Permitida", "Por favor ingrese de nuevo", "error");
            $('#cantidad' + id).val("0");
            return false;
        }
        return true;
    }

    function validartotal(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("Total no Permitido");
            $('#total' + id).val("0");
            return false;
        }
        return true;
    }

    function validarprecio(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val();
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "Debe ingresar el código, nombre, cantidad del producto", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var prec = elemento.value;
        if (prec.length == 0) {
            swal("!Error!", "Ingrese de nuevo", "error");
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
            swal("Precio no Permitido");
            $('#precio' + id).val("0");
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 100) {
            swal("Rango de descuento permitido (0% - 100%)", "Por favor ingrese de nuevo", "error");
            $('#desc_porcentaje' + id).val("0");
            return false;
        }
        return true;
    }

    function validartransporte(elemento) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("Transporte no Permitido", "Por favor ingrese de nuevo", "error");
            $('#transporte').val("0");
            return false;
        }
        return true;
    }
    $('input[name=numero_debito]').change(function() {
        swal(this);
    });

    function redondea_precio(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        //alert(decimal);
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

    function buscar_pedido() {
        var msj = "";
        var concepto = $("#pedido_nombre").val();

        var contador = parseInt($("#contador").val());

        if (isNaN(contador)) {
            contador = 0;
        }
        $('#eliminar1').trigger("click");
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_pedido')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_pedido': $("#pedido_nombre").val()
            },
            success: function(data) {


                if (data.value != 'no resultados') {

                    if ($("#proveedor").val() != "") {
                        if ($("#proveedor").val() != "") {
                            if (data[0] != $("#proveedor").val()) {
                                swal("Error", "Ingreso un pedido de otro", "error");
                            } else {
                                $('#proveedor').val(data[0]);
                                if ($('#proveedor').val(data[0]) == "") {
                                    msj += "No existe el proveedor. <br>"
                                }
                                if ($('#cuenta_proovedor').val(data[5]) == "") {
                                    msj += "El proveedor no tiene cuenta. <br>";
                                }
                                $('#direccion_proveedor').val(data[4]);
                                $('#fecha').val(data[1]);
                                $('#nombre_proveedor').val(data[0]);
                                $('#f_caducidad').val(data[2]);
                                $('#cuenta_proovedor').val(data[5]);
                                $('#id_empresa').val(data[6]);
                                var trar = data[3].length;
                                if (contador > 0) {
                                    trar += contador;
                                }

                                for (i = 0; i < data[3].length; i++) {

                                    crea_td(contador);
                                    $('#codigo' + contador).val((data[3][i].codigo));
                                    $('#nombre' + contador).val(data[3][i].nombre);
                                    $('#cantidad' + contador).val((data[3][i].cantidad).toFixed(2, 2));
                                    $('#total' + contador).val((data[3][i].cantidad_total).toFixed(2, 2));
                                    $('#precio' + contador).val((data[3][i].precio).toFixed(2, 2));

                                    if (data[3][i].iva == '1') {
                                        document.getElementById('iva' + i).checked = true;
                                        $('#ivaver' + i).val(1);
                                        document.getElementById('iva' + i).disabled = true;
                                    }
                                    total_calculo(contador);
                                    suma_totales();
                                    cambiar_proveedor();
                                    contador++;
                                }
                                var anterior = $("#observacion").val();
                                $('#nombre_proveedor').val(data[0]).trigger("change");
                                $("#observacion").val(anterior + " PEDIDO : " + concepto);
                                $("#contador").val(trar);
                            }
                        } else {

                        }


                    } else {
                        if ($('#proveedor').val(data[0]) == "") {
                            msj += "No existe el proveedor. <br>"
                        }
                        if ($('#cuenta_proovedor').val(data[5]) == "") {
                            msj += "El proveedor no tiene cuenta. <br>";
                        }

                        $('#direccion_proveedor').val(data[4]);
                        $('#fecha').val(data[1]);
                        $('#f_caducidad').val(data[2]);
                        $('#cuenta_proovedor').val(data[5]);
                        $('#id_empresa').val(data[6]);
                        var trar = data[3].length;
                        if (contador > 0) {
                            trar += contador;
                        }

                        /*
                        for (i = 0; i < data[3].length; i++) {

                            crea_td(contador);
                            $('#codigo' + contador).val((data[3][i].codigo));
                            $('#nombre' + contador).val(data[3][i].nombre);
                            /*$('#bodega'+i).val(data[3][i].id_bodega);
                            $('#cantidad' + contador).val((data[3][i].cantidad_total).toFixed(2, 2));
                            $('#total' + contador).val((data[3][i].cantidad_total).toFixed(2, 2));
                            $('#precio' + contador).val((data[3][i].precio).toFixed(2, 2));

                            if (data[3][i].iva == '1') {
                                document.getElementById('iva' + i).checked = true;
                                $('#ivaver' + i).val(1);
                                document.getElementById('iva' + i).disabled = true;
                            }
                            total_calculo(contador);
                            suma_totales();
                            //document.getElementById('eliminar'+i).disabled=true;
                            cambiar_proveedor();
                            contador++;
                        }*/
                        agregarElement(data);
                        var anterior = $("#observacion").val();
                        $('#nombre_proveedor').val(data[0]).trigger("change");

                        $("#observacion").val(anterior + " PEDIDO : " + concepto);
                        $("#contador").val(trar);

                    }




                } else {
                    $('#proveedor').val('');
                    $('#cuenta_proovedor').val('');
                    $("#pedido_nombre").val('')
                    if (msj != "") {
                        swal("Error!", msj, "warning");
                    }
                    swal("Error!", "No coinciden los productos del pedido con los productos en stock. <br>", "error");
                }
            },
            error: function(data) {

                swal("Error!", "No coinciden los productos del pedido con los productos en stock. <br>", "error");
            }
        })


    }


    function validar_retenciones(elemento) {
        var numero = parseInt(elemento.value, 10);
        var total_final = parseFloat($('#total_final').val());
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 999999999) {
            swal("Valor de Retención no Permitido", "Por favor ingrese de nuevo", "error");
            $('#retenciones').val("0");
            return false;
        }
        if (numero > total_final) {
            swal("No puedes ingresar el monto superior al valor de la factura", "Por favor ingrese de nuevo", "error");
            $('#retenciones').val("0");
            return false;
        }
        return true;
    }

    function buscar_factura() {
        var contador = parseInt($("#contador").val());
        if (isNaN(contador)) {
            contador = 0;
        }
        if (!isNaN($("#factura_nombre").val())) {
            $.ajax({
                type: 'post',
                url: "{{route('compra_buscar_factura')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_factura': $("#factura_nombre").val()
                },
                success: function(data) {

                    if (data.value != 'no resultados') {
                        if ($("#proveedor").val() != "") {
                            if (data[0] != $("#proveedor").val()) {
                                swal("Error", "Ingreso un pedido de otro");
                            } else {
                                $('#proveedor').val(data[0]);
                            }
                        }
                        //$('#proveedor').val(data[0]);
                        $('#direccion_proveedor').val(data[4]);
                        $('#fecha').val(data[1]);
                        $('#f_caducidad').val(data[2]);
                        $('#id_empresa').val(data[6]);
                        for (var i = 0; i < data[3].length; i++) {
                            crea_td(i);
                            $('#codigo' + contador).val(data[3][i].codigo);
                            $('#nombre' + contador).val(data[3][i].nombre);
                            /* $('#bodega'+i).val(data[3][i].id_bodega);*/
                            $('#cantidad' + contador).val(data[3][i].cantidad_total);
                            $('#total' + contador).val(data[3][i].cantidad_total);
                            $('#precio' + contador).val(data[3][i].precio);
                            if (data[3][i].iva == '1') {
                                document.getElementById('iva' + i).checked = true;
                            }
                            total_calculo(contador);
                            cambiar_proveedor();
                            contador++;
                        }
                        suma_totales()
                        $("#contador").val(data[3].length);
                        $('#nombre_proveedor').val(data[0]).trigger("change");
                    } else {
                        $('#proveedor').val('');
                    }
                },
                error: function(data) {

                    swal("Error!", "No coinciden los productos del pedido con los productos en stock. <br>", "error");
                }
            })
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
            total = pre_neto;


            if (visibilidad == 1) {
                if ($('#iva' + contador).prop('checked')) {
                    var iva_par = 0.12;
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "Debe ingresar el código, nombre del Producto o Servicio", "error");
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

    function suma_totales2() {

        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;

        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                valor = parseFloat($(this).find('#precio' + contador).val());
                descu = parseFloat($(this).find('#desc' + contador).val());
                pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                total = cantidad * valor;
                if ($('#iva' + contador).prop('checked')) {
                    $('#ivaver' + contador).val(1);
                    var iva_par = $('#iva_par').val();
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
                        iva1 = (pre_neto - descu) * (0.12);

                        iva = iva + iva1;
                    } else {
                        iva1 = pre_neto * 0.12;
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
                //aask to change discount to values
                // discount values with level headers
                // why i dont use values the function laravl view
                //



            }
            //contador = contador + 1;
        });
        var ivaf = parseFloat($("#iva_par").val());

        var dsiva = sub;
        if (iva > 0) {
            dsiva = sub * ivaf;
        }
        if (descu1 > 0) {
            if (iva > 0) {
                dsiva = (sub - descu1) * ivaf;

            } else {
                dsiva = (sub - descu1);

            }

        }
        subt = sub;

        trans = parseFloat($('#transporte').val());
        total_fin = parseFloat((sub - descu1) + dsiva);
        if (descu1 > 0) {
            subt = sub - descu1;
        }
        if (!isNaN(sub)) {
            $('#subtotal_final').val(sub.toFixed(2));
        }
        if (!isNaN(descu1)) {
            $('#descuento').val(descu1.toFixed(2));
        }
        if (!isNaN(dsiva)) {
            $('#iva_final').val(dsiva.toFixed(2));
        }
        if (!isNaN(total_fin)) {
            $('#total_final').val(total_fin.toFixed(2));
        }
        $('#subtotal_final1').val(sub.toFixed(2));
        if (descu1 == NaN) {
            decu1 = 0;
        }
        $('#descuento1').val(descu1.toFixed(2));
        $('#iva_final1').val(dsiva.toFixed(2));
        $('#total_final1').val(total_fin.toFixed(2));


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



    function validar_cheque() {
        var valor_efectivo = parseInt($("#valor_cheque").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var valor_efec = $("#valor_cheque").val();
        var validacion = calculartotales();


        if (valor_efectivo > valor_totalfinal) {

            swal("El valor ingresado no puede ser mayor al valor Total", "Por favor ingrese de nuevo", "error");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
        if (isNaN(valor_efectivo)) {
            $("#datos_cheque").hide();
        }

        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_cheque").show();
                //alert(validacion);
            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                //alert(validacion);
                $('#valor_cheque').val('');
            }

        }
        if ((valor_efectivo < 0)) {
            swal("El valor ingresado es incorrecto");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
    }

    function validar_efectivo() {
        var valor_efectivo = parseInt($("#valor_efectivo").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if (validacion == 'ok') {
            if ((valor_efectivo > 0) && (valor_totalfinal > 0)) {

                if (valor_efectivo > valor_totalfinal) {

                    swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

                    $('#valor_efectivo').val('');

                }
            }
        } else {
            swal("Error!", "No cumple la suma con el total de la factura", "error");
            $('#valor_efectivo').val('');
        }
    }

    function validar_tarj_credito() {
        var valor_efectivo = parseInt($("#valor_tarjetacredito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0)) {
            swal("Error!", "El valor ingresado es incorrecto", "error");
            $('#valor_tarjetacredito').val('');
            $("#datos_tarjeta_credito").hide();

        }
        if (isNaN(valor_efectivo)) {
            $("#datos_tarjeta_credito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {

            swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

            $('#valor_tarjetacredito').val('');

        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_credito").show();

            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                $('#valor_tarjetacredito').val('');
            }
        }
    }

    function validar_tarj_debito() {
        var valor_efectivo = parseInt($("#valor_tarjetadebito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0)) {
            swal("Error!", "El valor ingresado es incorrecto", "error");
            $('#valor_tarjetadebito').val('');
            $("#datos_tarjeta_debito").hide();
        }
        if (isNaN(valor_efectivo)) {
            $("#datos_tarjeta_debito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {

            swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

            $('#valor_tarjetadebito').val('');

        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_debito").show();

            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                $('#valor_tarjetadebito').val('');
                //swal(validacion);
            }
        }
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
            if (longitud >= 10) {
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


    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function _ingresar_cero() {
        let secuencia = $('#secuencia_factura').val(); //obtener el valor ingresado
        let numero = ""; //se encargara agregar los 0
        let final_secuencia = ""; //la concatenacion de ambas variabes

        if (parseInt(secuencia) > 0) {
            if (secuencia.length > 10) {
                alertas("error", "Error!", "Valor no permitido");
                $('#secuencia_factura').val('');
            } else {
                if (secuencia.length < 10) {
                    while (final_secuencia.length != 10) {
                        numero = "0" + numero
                        final_secuencia = numero + secuencia
                    }
                    $('#secuencia_factura').val(final_secuencia);
                } else {
                    $('#secuencia_factura').val(secuencia);
                }
            }
        } else {
            alertas("error", "Error!", "Valor no permitido", );
            $('#secuencia_factura').val('');
        }
    }

    function _ingresar_cero() {
        let secuencia = document.getElementById("secuencia_factura").value;
        let cero = "";
        let suma = "";

        //let secuencia = "";
        //secuencia = $('#secuencia_factura').val();
        //alert(parseInt(secuencia))
        if (parseInt(secuencia) > 0) {
            if (secuencia.length > 10) {
                //alert("valor no permitido")
                document.getElementById("secuencia_factura").value = "";
            } else {
                while (suma.length != 10) {
                    cero = "0" + cero;
                    suma = cero + secuencia;

                }
                document.getElementById("secuencia_factura").value = suma;
            }
        } else {
            //alert("no es un numero")
            document.getElementById("secuencia_factura").value = "";
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

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            swal("Error!", `{{trans('proforma.seriecorrectamente')}}`, "error");
        }
    }

    function mostrar_rubros() {

        var valor = $("#rubros").val();
        if (valor != 0) {
            $.ajax({
                type: 'post',
                url: "{{route('compra.buscar_rubros')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'opcion': valor
                },
                success: function(data) {
                    //alert(data[0].nombre);

                    $("#tabla_rubros").show('slow');
                    $("#debe").val(data[0].value);
                    $("#haber").val(data[0].otro);
                },
                error: function(data) {

                }
            })
        } else {
            swal("El asiento se llenará las cuentas por defecto");
            $("#tabla_rubros").hide('slow');
            $("#debe").val('');
            $("#haber").val('');

        }
        //alert(valor);

    }

    function nuevo_comprobante() {
        location.href = "{{route('compra_crear')}}";
    }

    /*   function _obtener_caja() {
           var id_sucursal = $("#sucursal").val();

           $.ajax({
               type: 'post',
               url: "{{route('caja.sucursal')}}",
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
                               $("#punto_emision").append('<option value=' + registro.codigo_sucursal + '-' + registro.codigo_caja + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                           });
                       } else {
                           $("#punto_emision").empty();

                       }

                   }
               },
               error: function(data) {

               }
           })

       }*/

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


    function obtener_sucursal() {

        var id_seleccionado = $("#id_empresa").val();

        $.ajax({
            type: 'post',
            url: "{{route('sucursal.empresa')}}",
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

            }
        })

    }


    function cambiarProve(val) {
        $.ajax({
            type: 'post',
            url: "{{route('compras.buscador_master_tipos')}}",
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
    document.getElementById("fecha").addEventListener('blur', function() {
        let fechaAc = new Date();
        let mes = fechaAc.getUTCMonth() + 1;
        let d = new Date(this.value);
        let month = d.getUTCMonth() + 1;
        if (mes != month) {
            swal("Recuerde!", "La fecha que ingresa está fuera del periodo ", "error")
            //document.getElementById("fecha_hoy").value = ty;
            //location.reload();
        }
    });
    document.getElementById("f_autorizacion").addEventListener('blur', function() {
        let fechaAc = new Date();
        let mes = fechaAc.getUTCMonth() + 1;
        let d = new Date(this.value);
        let month = d.getUTCMonth() + 1;
        if (mes != month) {
            swal("Recuerde!", "La fecha que ingresa está fuera del periodo ", "error")
            //document.getElementById("fecha_hoy").value = ty;
            //location.reload();
        }
    });

    document.getElementById("f_caducidad").addEventListener('blur', function() {
        let fechaAc = new Date();
        let mes = fechaAc.getUTCMonth() + 1;
        let d = new Date(this.value);
        let month = d.getUTCMonth() + 1;
        if (mes != month) {
            swal("Recuerde!", "La fecha que ingresa está fuera del periodo ", "error")
            //document.getElementById("fecha_hoy").value = ty;
            //location.reload();
        }
    });


    const llenarCampo = () => {

        let proveedor = document.getElementById("nombre_proveedor");

        let serie = document.getElementById("serie").value;
        //serie = serie.options[serie.selectedIndex].text;
        let secuencia = document.getElementById("secuencia_factura").value;
        let value = proveedor.options[proveedor.selectedIndex].text;
        if (value != '') {
            document.getElementById("observacion").value = `FACT-COMPRA ${value} ${serie}-${secuencia}`
            //document.getElementById("observacion").value = 'FACT-COMPRA' + ' ' + value + ' ' + serie + ' ' + secuencia;
        }


    }

    $('.select2_find_proveedor_create').select2({
        placeholder: "Escriba el nombre del proveedor",
        allowClear: true,
        ajax: {
            url: '{{route("compras.proveedorsearch")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });

    document.querySelector("#toggleswitch").onchange = (e) => {
        let checked = e.target.checked;
        if (checked) {
            document.getElementById("tipo_electronica").value = 1;
        } else {
            document.getElementById("tipo_electronica").value = 0;
        }
    }

    const cargarSucursal = (e) => {
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
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
                        $("#p_emision").empty();

                        $.each(data, function(key, registro) {
                            var select = '';
                            if (registro.codigo_caja == '100')
                                select = 'selected';
                            $("#p_emision").append('<option value=' + registro.codigo_caja + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                        });
                    } else {
                        $("#p_emision").empty();

                    }
                }



            },
            error: function(data) {
                console.log(data);
            }


        });

    }

    const sumarFecha = (e) => {

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
</script>

@endsection