@extends('contable.ventas.base')
@section('action-content')
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<style type="text/css">
    .input-number {
        width: 80%;
        height: 20px;
    }
</style>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Ventas</a></li>
            <li class="breadcrumb-item"><a href="{{route('factura_convenios.index')}}">Registro de Factura de Convenios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Nueva Factura de Convenios</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
    </div>
    <!-- DO CHANGES BEACUSE THE VALUES NOT SAME WITH "AP_AGRUPADO" AND NOT SURE ITS POSIBLE ADD NEW HEADERS OF "FACTURA DE VENTA" DATE: 25 NOV 2020 -->
    <div class="box-body dobra">
        <form class="form-vertical" id="form">
            {{ csrf_field() }}
            <div class="header row">
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="sucursal" class="label_header">Sucursal (Empresa)</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $value)
                            <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="punto_emision" class="label_header">P. Emision (Empresa)</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="punto_emision" id="punto_emision" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-1 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">Id</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="id" id="id" value="">
                        @if ($errors->has('id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero" class=" label_header">N&uacute;mero Factura</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="numero" id="numero" value="" onchange="ingresar_cero()">
                        @if ($errors->has('numero'))
                        <span class="help-block">
                            <strong>{{ $errors->first('numero') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo" class="label_header">Tipo</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="tipo" id="tipo" value="VEN-CONVENIO" readonly>
                        @if ($errors->has('tipo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento" class="label_header">Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="asiento" id="asiento" value="">
                        @if ($errors->has('asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-1  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha" type="text" class="form-control" name="fecha_asiento" value="{{ old('fecha_asiento') }}" required>
                        @if ($errors->has('fecha_asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="orden_venta" class="label_header">Orden de Venta</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="orden_venta" type="text" class="form-control" name="orden_venta" value="{{ old('orden_venta') }}">
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header">Empresa</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="empresa" type="text" class="form-control" name="empresa" value="{{ $empresa->nombrecomercial }}" readonly>
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero_autorizacion" class="label_header"># Autorización</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="numero_autorizacion" type="text" class="form-control" name="numero_autorizacion" value="{{ old('numero_autorizacion') }}">
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="divisas" class="label_header">Divisas</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="divisas" name="divisas" class="form-control">
                            @foreach($divisas as $value)
                            <option value="{{$value->id}}">{{$value->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="recaudador" class="label_header">Recaudador</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="recaudador" name="recaudador" class="form-control">
                            <option value="">Seleccione...</option>
                            @foreach($user_vendedor as $value)
                            <option value="{{$value->nombre1}} {{$value->apellido1}}" data-id="{{$value->id}}" data-name="{{$value->nombre1}} {{$value->apellido1}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_recaudador" id="cedula_recaudador" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="vendedor" class="label_header">Vendedor</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="vendedor" name="vendedor" class="form-control">
                            <option value="">Seleccione...</option>
                            @foreach($user_vendedor as $value)
                            <option value="{{$value->nombre1}} {{$value->apellido1}}" data-id="{{$value->id}}" data-name="{{$value->nombre1}} {{$value->apellido1}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_vendedor" id="cedula_vendedor" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <div class="col-md-12 px-0">
                        <label for="cliente" class="label_header text-left">Cliente</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" value="{{ old('identificacion_cliente') }}" onchange="reloadCliente()" required placeholder="Identificacion">
                        @if ($errors->has('identificacion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" value="{{ old('nombre_cliente') }}" onchange="reloadCliente()" required placeholder="Nombre">
                        @if ($errors->has('nombre_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente" value="{{ old('direccion_cliente') }}" required placeholder="Dirección">
                        @if ($errors->has('direccion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente" value="{{ old('ciudad_cliente') }}" required placeholder="Ciudad">
                        @if ($errors->has('ciudad_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="mail_cliente" type="text" class="form-control" name="mail_cliente" value="{{ old('mail_cliente') }}" required placeholder="Mail">
                        @if ($errors->has('mail_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente') }}" required placeholder="Teléfono">
                        <input id="tipo_cliente" type="hidden" class="form-control" name="tipo_cliente" value="{{ old('tipo_cliente') }}" required placeholder="tipo">
                        @if ($errors->has('telefono_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>


                <div class="form-group col-xs-12  px-1">
                    <label for="concepto" class="label_header">Concepto</label>
                    <input type="hidden" name="ivareal" id="ivareal" value="{{$valorIva}}">
                    <input class="form-control" name="concepto" id="concepto" placeholder="Ingrese concepto...">
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-row">
                        <label class="col-md-12"> Buscar codigo </label>
                        <input type="text" class="form-control col-md-6" id="buscar_final" placeholder="Ingrese número de reclamo">
                        <button type="button" class="btn btn-default btn-xs btn-gray buscar col-md-6" onclick="getLoader(this)"> <i class="fa fa-search"></i> </button>
                    </div>
                </div>

            </div>
            <div class="col-md-12 table-responsive" id="recycler" style=" height: 400px; overflow-y: scroll;">
                <input type="hidden" name="contador" id="contador" value="0">
            </div>
            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr></tr>
                </thead>
                <tbody id="agregar_cuentas">
                    <tr style="display:none" id="mifila">
                        <td style="max-width:100px;">
                            <Input type="hidden" name="codigo[]" class="codigo_producto" />
                            <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                <option> </option>
                                @foreach($ap_agrupado as $value)
                                <option value="{{$value->cod_proceso}}" @if(($value->base_iva)>0) data-iva="1" @else data-iva="0" @endif data-codigo="{{$value->cod_proceso}}" data-mes="{{$value->mes_plano}}" data-precio="{{$value->valor_cobrado}}" > Codigo Proceso: {{$value->cod_proceso}} Mes Plano: {{$value->mes_plano}}</option>
                                @endforeach
                            </select>
                            <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                            <input type="hidden" name="iva[]" class="iva" />
                        </td>
                        <td>
                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" onchange="verificar_stock(this)" required>

                        </td>
                        <td>
                            <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                <option value="0"> </option>
                            </select>
                            <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td>
                            <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" readonly required>
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
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" class="text-right">Subtotal {{$porcentajeIva}}%</td>
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
                        <td colspan="2" class="text-right">Subtotal 0%</td>
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
                        <td colspan="2" class="text-right">Descuento</td>
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
                        <td colspan="2" class="text-right">Subtotal</td>
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
                        <td colspan="2" class="text-right">Tarifa Iva {{$porcentajeIva}}%</td>
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
                        <td colspan="2" class="text-right"><strong>Total</strong></td>
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
                        <td colspan="2" class="text-right"><strong>Por Cobrar Seguro</strong></td>
                        <td id="copagoTotal" class="text-right px-1">0.00</td>
                        <input type="hidden" name="totalc" id="totalc" class="hidden">
                    </tr>
                </tfoot>
            </table>
            <div class="col-md-12" style="height:30px;">
                <div class="row head-title">
                    <div class="col-md-12 cabecera">
                        <label class="color_texto">Forma de Pago</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 table-responsive ">
                <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                <table id="example1" role="grid" class="table table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                    <thead>
                        <tr>
                            <th width="20%" style="text-align: center;">Metodo</th>
                            <th width="10%" style="text-align: center;">Fecha</th>
                            <th width="15%" style="text-align: center;">Tipo Tarjeta</th>
                            <th width="10%" style="text-align: center;">Número</th>
                            <th width="15%" style="text-align: center;">Banco</th>
                            <th width="10%" style="text-align: center;">Cuenta</th>
                            <th width="10%" style="text-align: center;">Girado a</th>
                            <th width="10%" style="text-align: center;">Valor</th>
                            <th width="10%" style="text-align: center;">Valor Base</th>
                            <th width="5%" style="text-align: center;">
                                <button id="btn_pago" type="button" class="btn btn-success btn-gray">
                                    <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" />
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_pago">
                    </tbody>
                </table>
            </div>
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    var fila = $("#mifila").html();
    var existeCliente = false;
    $('.select2_cuentas').select2({
        tags: false
    });
    $('body').on('click', '.des', function() {
        console.log($(this));
        //alert('eliminando')
        $(this).parent().parent().parent().parent().hide('slow').remove()
        totales(0);
    });
    $(document).on("focus", "#nombre_cliente", function() {
        $("#nombre_cliente").autocomplete({
            classes: {
                "ui-autocomplete": "dob_autocomplete",
            },
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('ventas.buscarcliente')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                $("#identificacion_cliente").val(ui.item.id);

                $("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);
                $("#tipo_cliente").val(ui.item.tipo);
                totales(0);
            },
            selectFirst: true,
            minLength: 1,
        });
    });
    $(document).on("focus", "#identificacion_cliente", function() {
        $("#identificacion_cliente").autocomplete({
            classes: {
                "ui-autocomplete": "dob_autocomplete",
            },
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('ventas.buscarclientexid')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                        //console.log("identificacion_cliente", data.id);
                        if (data.id == "") {
                            //swal("el cliente no existe");
                            existeCliente = false;
                        } else {
                            existeCliente = true;
                        }
                    }
                });
            },
            change: function(event, ui) {
                $("#nombre_cliente").val(ui.item.nombre);
                $("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);
                $("#tipo_cliente").val(ui.item.tipo);
                totales(0);
            },
            selectFirst: true,
            minLength: 1,
        });
    });
    $(document).on("focus", "#identificacion_paciente", function() {
        $("#identificacion_paciente").autocomplete({
            classes: {
                "ui-autocomplete": "dob_autocomplete",
            },
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('ventas.buscarpaciente')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                        if (data.id == "") {
                            //swal("el cliente no existe");
                            existeCliente = false;
                        }
                    }
                });
            },
            change: function(event, ui) {
                if (ui.item != null) {
                    //console.log(ui.item);
                    $("#nombre_paciente").val(ui.item.nombre);
                    $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                } else {
                    swal("No Existe el paciente");
                }
                /*$("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);*/
            },
            selectFirst: true,
            minLength: 3,
        });
    });

    function getLoader(pedido) {
        $.ajax({
            type: "get",
            url: "{{route('f_convenios.tabla')}}",
            data: {
                'pedido': $('#findpedido').val(),
            },
            datatype: "html",
            success: function(datahtml, data) {
                ///console.log(data);
                //$('#findpedido').val('');
                $("#recycler").append(datahtml);
                $('.select2_cuentas').select2({
                    tags: false
                });
                totales(0)
            },
            error: function() {
                alert('Error, ingrese otro pedido');
            }
        });
    }
    $(document).on("focus", "#nombre_paciente", function() {
        $("#nombre_paciente").autocomplete({
            classes: {
                "ui-autocomplete": "dob_autocomplete",
            },
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('ventas.buscarpaciente_nombre')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                        if (data.id == "") {
                            //swal("el cliente no existe");
                            existeCliente = false;
                        }
                    }
                });
            },
            change: function(event, ui) {
                if (ui.item != null) {
                    //console.log(ui.item);
                    $("#identificacion_paciente").val(ui.item.nombre);
                    $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                } else {
                    swal("No Existe el paciente");
                }
                /*$("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);*/
            },
            selectFirst: true,
            minLength: 3,
        });
    });

    $('body').on('click', '.delete', function() {
        //console.log($(this));
        $(this).parent().parent().remove();
        totales(0);
    });
    $(document).ready(function() {
        limpiar();
        $('.select2_cliente').select2({
            tags: false
        });
        $('#iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%'
        });
    });
    $('body').on('click', '.cp', function() {
        //console.log($(this));
        //console.log($(this).prev().attr('class'));
        var clase = $(this).prev().attr('class');
        var html = '<input type="text" class="form-control pneto"  name="precio[]" style="width:40%;display:inline;height:20px;">' +
            '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
            '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
            '</button>';
        //console.log($(this).parent());
        if (clase.includes('select2_precio')) {
            $(this).parent().append(html);
            $(this).prev().remove();
            $(this).remove();
        } else {
            html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" autofocus active required>' +
                '<option value="0"> </option></select>' +
                '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
                '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
            // $(this).parent().empty();
            // $(this).parent().append(html);
            $(this).parent().append(html);
            verificar($(this).parent().prev().prev().children().closest('.select2_cuentas'));
            $(this).prev().remove();
            $(this).remove();
        }
    });
    /*function changePrecio(i, e){
        var html = '<input type="text" class="form-control pneto" style="width:40%;display:inline;height:20px;">'+
                    '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,2)">'+
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>'+
                    '</button>';
                    console.log(i.parent());
        if(e==1){

            $("#tprecio").empty();
            $("#tprecio").append(html);
        }else{
            html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" required>'+
                    '<option value="0"> </option></select>'+
                    '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,1)">'+
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
            $("#tprecio").empty();
            $("#tprecio").append(html);
            console.log($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
            verificar($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
        }
    }*/
    function goNew() {
        $(".btn_add").attr("disabled", false);
        location.href = '{{route('factura_convenios.create')}}';
        $("#asiento").val("");
        $("#id").val("");
        $("#numero").val("");
    }
    //Desabilita Componentes
    function desabilita_componente(elemento, id) {
        var id_tipo = parseInt(elemento.value);
        if ((id_tipo == 1) || (id_tipo == 4) || (id_tipo == 5) || (id_tipo == 6)) {
            $('#fecha' + id).val("");
            document.getElementById('fecha' + id).disabled = true;
            document.getElementById('numero' + id).disabled = true;
            document.getElementById('id_banco' + id).disabled = true;
            document.getElementById('id_cuenta_pago' + id).disabled = true;
            document.getElementById('giradoa' + id).disabled = true;
        } else {
            if ((id_tipo == 2) || (id_tipo == 3)) {
                document.getElementById('fecha' + id).disabled = false;
                document.getElementById('numero' + id).disabled = false;
                document.getElementById('id_banco' + id).disabled = false;
                document.getElementById('id_cuenta_pago' + id).disabled = false;
                document.getElementById('giradoa' + id).disabled = false;
            }
        }
    }

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");
        //console.log(modPrecio);
        $(e).parent().next().children().closest(".cneto").val('1');
        $(e).parent().next().next().children().closest(".pneto").empty();
        $(e).parent().next().next().children().closest(".pneto").append('<option value=' + modPrecio + '>' + modPrecio + '</option>');
        $(e).parent().next().next().children().closest(".pneto").trigger('change');
        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        //console.log($(e).parent().next().next().children().closest(".cp"));
        /*
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            //console.log("modifica precio");
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            //console.log("no modifca el precio");
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
        if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr
            ("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        }
        $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }*/
        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }
        //cargarPrecios
        var tipo = $("#tipo_cliente").val();
        var selected = "";
        //console.log(codigo);
        /*
        $.ajax({
            type: 'get',
            url: "{{route('factura_convenios.obtener_ap')}}",
            datatype: 'json',
            data: {
                id: codigo
            },
            success: function(data) {
                /*
                $(e).parent().next().next().children().find('option').remove();
                $.each(data, function(key, value) {
                    if (tipo == value.nivel) {
                        selected = "selected";
                    } else {
                        selected = "";
                    }
                    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
                });
                //console.log(data);
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });*/
    }
    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto
    $('body').on('blur', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('active', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        // verificar(this);
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        // console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        $(this).parent().next().next().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.copago', function() {
        verificar(this);
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();
        var copago = $(this).val();
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        //console.log(total);
        $(this).parent().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.pdesc', function() {
        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var pdesc = $(this).val();
        //console.log("el descuento maximo debe de ser", m, pdesc);
        if ('parseFloat'(pdesc) > parseFloat(m)) {
            swal("El descuento no puede ser mayor a " + m + "%");
            $(this).val(m).focus();
        }
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = $(this).parent().prev().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        $(this).parent().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.desc', function() {
        var m = verificar(this);
        var cant = $(this).parent().prev().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().prev().children().val();
        /*if(pdesc> m){
            swal("El descuento no puede ser mayor a "+m+"%");
            $(this).val(m);
        }*/
        var descuento = $(this).val();
        verificar(this);
        //console.log(cant, precio);
        var pdesc = 0;
        if (cant == 0 || precio == 0) {
            pdesc = 0;
        } else {
            pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
        }
        //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
        $(this).parent().prev().children().val(pdesc);
        var copago = $(this).parent().prev().prev().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        $(this).parent().next().children().val(total.toFixed(2));
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
                //console.log("el e es: ", e.val());
                var precio = $(this).parent().next().children().val();
                var copago = $(this).parent().next().next().children().val();
                var descuento = $(this).parent().next().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                //console.log(iva);
                precio = precio != null ? precio : 0;
                var total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
                //console.log("precio y cantidad"+total);
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
                //console.log("subtotal12"+sb12);
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                var descuento_total = descuentosub12 + descuentosub0;
                var sum = sb12 + sb0 - descuento_total;
                $("#base").html(sum.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;
                console.log(ti);
                if (d > 0) {
                    if (sb12 > 0) {
                        ti = iva * (sb12 - descuentosub12);
                    }
                }
                $("#tarifa_iva").html(ti.toFixed(2));
                var t = sb12 + sb0 + ti - d;
                //console.log(t);
                var totax = sum + ti;
                $("#total").html(totax.toFixed(2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                sb12 = redondeafinal(sb12);
                $("#subtotal_121").val(sb12);
                sb0 = redondeafinal(sb0);
                $("#subtotal_01").val(sb0);
                $("#descuento1").val(d.toFixed(2));
                $("#tarifa_iva1").val(ti.toFixed(2));
                totax = redondeafinal(totax);
                $("#total1").val(totax);
                $("#totalc").val(copagoTotal.toFixed(2));
            });
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        console.log("entra aqui");
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

    function ingresar_cero() {
        var secuencia_factura = $('#numero').val();
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#numero').val('');
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
                $('#numero').val(secuencia + secuencia_factura);
            }
        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#numero').val('');
        }
    }
    $(".btn_add").click(function() {
        if ($("#form").valid()) {
            $(".print").css('visibility', 'visible');
            $(".btn_add").attr("disabled", true);
            $("#mifila").html("");
            $.ajax({
                url: "{{route('factura_convenios.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    //console.log(data);
                    if (data != "error") {
                        $("#asiento").val(data.idasiento);
                        $("#id").val(data.idventa);
                        $("#numero").val(data.num_vent);
                        swal("Guardado con Exito!");
                    } else {
                        swal("Error! No guardo correctamente, intente de nuevo");
                    }
                },
                error: function(data) {
                    console.log(data.responseText);
                }
            });
        } else {
            swal("Tiene campos vacios");
            console.log($("#form").serialize());
        }
    });
    $('#btn_pago').click(function(event) {
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.innerHTML = '<td><select class="form-control" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');"><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="form-control input-number" value="<?= date('Y-m-d'); ?>" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '" name="tipo_tarjeta' + id + '" style="width: 175px;height:25px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="form-control" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="width: 80%;height:20px;" autocomplete="off" class="form-control" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="form-control" style="width: 80%;height:20px;"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="form-control text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);"></td><td><input class="form-control input-number" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,' + id + ',2);"></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;
    });
    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor) {
        var dato_pago1 = "dato_pago" + valor;
        var nombre_pago2 = 'visibilidad_pago' + valor;
        document.getElementById(dato_pago1).style.display = 'none';
        document.getElementById(nombre_pago2).value = 0;
    }

    function revisar_componentes(e, id) {
        metodo = $('#id_tip_pago' + id).val();
        if (metodo == 1) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero_pago" + id).prop('disabled', true);
            $("#id_banco_pago" + id).prop('disabled', true);
            $("#id_cuenta_pago" + id).prop('disabled', true);
            //$("#fi"+id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 2) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //$("#fi"+id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 3) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //$("#fi"+id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 4) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //$("#fi"+id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 5) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //$("#fi"+id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 6) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //$("#fi"+id).prop('disabled', false);
            revision_total(id);
        }
    }

    function soloNumeros(e) {
        // capturamos la tecla pulsada
        var teclaPulsada = window.event ? window.event.keyCode : e.which;
        // capturamos el contenido del input
        var valor = e.value;
        // 45 = tecla simbolo menos (-)
        // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
        // Modificamos el contenido del mismo añadiendo el simbolo menos al
        // inicio
        //console.log("indexof", valor);
        if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
            document.getElementById("inputNumero").value = "-" + valor;
        }
        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
            return true;
        }
        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    function revision_total(id) {
        var valor = $('#valor' + id).val();
        ntotal = valor * 1;
        $('#valor_base' + id).val(ntotal.toFixed(2));
    }

    function redondea_valor_base(e, id, val) {
        return parseFloat(e.value).toFixed(val);
    }

    function reloadCliente() {
        if (!existeCliente) {
            //swal("epa");
        }
    }

    function goBack() {
        location.href = "{{ route('factura_convenios.index') }}";
    }

    function crear_factura_venta() {
        //$('#crear_factura').submit();
        var valor_total = $('#total1').val();
        var formulario = document.forms["crear_factura"];
        //Datos Cabecera Factura
        var divisas = formulario.divisas.value;
        //Datos Generales
        var id_emp = formulario.id_empresa.value;
        var sucurs = formulario.sucursal.value;
        var punt_emision = formulario.punto_emision.value;
        //Datos Paciente
        var cedula = formulario.ced_paciente.value;
        var nombre_paciente = formulario.nomb_paciente.value;
        var seguro_paciente = formulario.id_seguro.value;
        var proced = formulario.procedimiento.value;
        var fech_proced = formulario.fecha_proced.value;
        //Datos Clientes
        var cliente = formulario.cliente.value;
        var ruc_ced_cliente = formulario.ruc_cedula.value;
        var direccion = formulario.direccion.value;
        var ciud_cliente = formulario.ciudad.value;
        var email_cliente = formulario.email.value;
        var telf_cliente = formulario.telefono.value;
        //Datos Recaudador
        var recaud = formulario.recaudador.value;
        var ced_recaudador = formulario.cedula_recaudador.value;
        //Detalle de Asiento
        var det_asiento = formulario.nota.value;
        var msj = "";
        if (divisas == "") {
            msj += "Por favor,Selecione la divisa\n";
        }
        //Datos Generales
        if (id_emp == "") {
            msj += "Por favor, Seleccione la Empresa\n";
        }
        if (sucurs == "") {
            msj += "Por favor, Seleccione la Sucursal\n";
        }
        if (punt_emision == "") {
            msj += "Por favor, Seleccione el Punto de Emision\n";
        }
        //Paciente
        if (cedula == "") {
            msj += "Por favor,Ingrese la cedula del paciente\n";
        }
        if (nombre_paciente == "") {
            msj += "Por favor,Ingrese el nombre del paciente\n";
        }
        if (seguro_paciente == "") {
            msj += "Por favor,Seleccione el seguro paciente\n";
        }
        if (proced == "") {
            msj += "Por favor,Ingrese los Procedimientos del paciente\n";
        }
        if (fech_proced == "") {
            msj += "Por favor,Seleccione la fecha de procedimientos\n";
        }
        //Cliente
        if (cliente == "") {
            msj += "Por favor,Selecione el cliente\n";
        }
        if (ruc_ced_cliente == "") {
            msj += "Por favor,Ingrese el ruc del cliente\n";
        }
        if (direccion == "") {
            msj += "Por favor,Ingrese la direccion del cliente\n";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente\n";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente\n";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente\n";
        }
        //Vendedor/Recaudador
        if (recaud == "") {
            msj += "Por favor,Selecione el recaudador\n";
        }
        if (ced_recaudador == "") {
            msj += "Por favor,Ingrese la cedula del recaudador\n";
        }
        //Detalle Asiento
        if (det_asiento == "") {
            msj += "Por favor,Ingrese el detalle del asiento\n";
        }
        if (msj == "") {
            if (valor_total > 0) {
                $.ajax({
                    type: 'post',
                    url: "{{route('ventas_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#crear_factura").serialize(),
                    success: function(data) {
                        //console.log(data);
                        swal({
                                title: "Guardado correctamente",
                                buttons: true,
                            })
                            .then((value) => {
                                location.href = "{{route('venta_index')}}";
                            });
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            } else {
                swal({
                    title: "Calcular el total a pagar",
                    buttons: true,
                });
            }
        } else {
            alert(msj);
        }
    }
    //Completa 2 Decimales a la izquierda
    function completa_decimales(elemento, id, nDec) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val();
        var num = elemento.value;
        var n = parseFloat(elemento.value);
        var s;
        //n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        //s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if ((cod.length == 0) && (nomb.length == 0)) {
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad' + id).val("0.00");
            $('#desc' + id).val("0.00");
            $('#extendido' + id).val("0.00");
            return false;
        }
        if (num.length == 0) {
            alert("Cantidad no permitida");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        $('#cantidad' + id).val(s);
    }
    //Completa 2 decimales a la izquierda y redondea el valor a dos decimales
    function redondea_precio(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        $('#precio' + id).val(s);
    }
    //Redondea descuento a 2 decimales
    function redondea_descuento(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        $('#desc_porcentaje' + id).val(s);
    }
    //Valida el valor Ingresado en el Campo Cantidad
    function validarcantidad(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0) {
            alert("Cantidad no permitida");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var st = $('#stock' + id).val();
        var nu = parseInt(elemento.value, 10);
        if (nu > st) {
            alert("Cantidad no debe ser mayor al stock");
            $('#cantidad' + id).val("0");
            $('#total_acum' + id).val('');
        }
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 1 || numero > 999999999) {
            alert("Cantidad no permitida");
            $('#cantidad' + id).val("0");
            return false;
        }
        $('#total_acum' + id).val(numero);
        return true;
    }
    //No usada por el momento
    function validartotal(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 1 || numero > 999999999) {
            alert("Total no permitido");
            $('#total' + id).val("");
            return false;
        }
        return true;
    }
    //Calcula el Total
    function total_calculo(id) {
        total = 0;
        descuento_total = 0;
        cantidad = parseFloat($('#cantidad' + id).val());
        precio = parseFloat($('#precio' + id).val());
        descuento = parseFloat($('#desc_porcentaje' + id).val());
        total = cantidad * precio;
        descuento_total = (total * descuento) / 100;
        $('#desc' + id).val(descuento_total.toFixed(2));
        $('#extendido' + id).val(total.toFixed(2));
        $('#desc1' + id).val(descuento_total.toFixed(2));
        $('#extendido1' + id).val(total.toFixed(2));
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
        val_cost_prod = 0;
        cost_prod = 0;
        iva_p = 0;
        vent_tar_12 = 0;
        vent_tar_0 = 0;
        sum1 = 0;
        sum2 = 0;
        $("#crear tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                //iva_p = parseInt($(this).find('#iva_obt'+contador).val());
                cost_prod = parseFloat($(this).find('#cost_prod' + contador).val());
                cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                valor = parseFloat($(this).find('#precio' + contador).val());
                descu = parseFloat($(this).find('#desc1' + contador).val());
                pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                total = cantidad * valor;
                if ($('#iva' + contador).prop('checked')) {
                    subtotal_12 = subtotal_12 + total;
                    if (total > 0) {
                        val_cost_prod = val_cost_prod + cost_prod;
                    }
                    if (descu > 0) {
                        descu1 = descu1 + descu;
                    }
                } else {
                    subtotal_0 = subtotal_0 + total;
                    if (total > 0) {
                        val_cost_prod = val_cost_prod + cost_prod;
                    }
                    if (descu > 0) {
                        descu1 = descu1 + descu;
                    }
                }
            }
            contador = contador + 1;
        });
        sum_subt = subtotal_12 + subtotal_0;
        iva = subtotal_12 * '{{$valorIva}}';
        base_imponible = subtotal_12;
        trans = parseFloat($('#transporte').val());
        if (trans > 0) {
            total_fin = (sum_subt - descu1) + trans + iva;
        } else {
            total_fin = (sum_subt - descu1) + iva;
        }
        $('#subtotal').val(sum_subt);
        $('#impuesto').val(iva.toFixed(2));
        $('#descuento').val(descu1.toFixed(2));
        $('#total').val(total_fin.toFixed(2));
        $('#base_imponible').val(base_imponible.toFixed(2));
        $('#subtotal').val(subtotal_12.toFixed(2));
        $('#subtotal2').val(subtotal_0.toFixed(2));
        //Campos Oculto
        $('#subtotal1').val(subtotal_12.toFixed(2));
        $('#subtotal_2').val(subtotal_0.toFixed(2));
        $('#impuesto1').val(iva.toFixed(2));
        $('#descuento1').val(descu1.toFixed(2));
        $('#total1').val(total_fin.toFixed(2));
        $('#base_imponible1').val(base_imponible.toFixed(2));
        $('#transporte1').val(trans);
        $('#cost_vent_merc').val(val_cost_prod);
    }
    //Valida Precio
    function validarprecio(elemento, id) {
        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            alert("Debe ingresar el código, nombre, cantidad del producto");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var prec = elemento.value;
        if (prec.length == 0) {
            alert("Precio no permitido");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 1 || numero > 999999999) {
            alert("Precio no permitido");
            $('#precio' + id).val("0");
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id) {
        var desc = elemento.value;
        if (desc.length == 0) {
            alert("Rango de descuento permitido (0% - 100%)");
            $('#desc_porcentaje' + id).val("0");
            $('#desc' + id).val("0");
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 100) {
            alert("Rango de descuento permitido (0% - 100%)");
            $('#desc_porcentaje' + id).val("0");
            $('#desc' + id).val("0");
            return false;
        }
        return true;
    }
    //Obtengo el Ruc o Cedula del Cliente Seleccionado
    $("#cliente").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('ventas_buscar_identificacion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#cliente"),
            success: function(data) {
                $('#ruc_cedula').val(data.client_identificacion);
                $('#direccion').removeAttr('disabled');
                $('#direccion').val(data.client_direccion);
                $('#identif_cliente').val(data.client_identificacion);
                $('#telefono').val(data.client_telefono);
                $('#email').val(data.client_email);
                $('#ciudad').val(data.client_ciudad);
            },
            error: function(data) {
                console.log(data);
            }
        })
    });
    //Obtengo datos del vendedor de la Tabla Usuarios
    /*$("#vendedor").change(function(){
        $.ajax({
            type: 'post',
            url:"{{route('vendedor.identificacion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#vendedor"),
            success: function(data){
                $('#cedula_vendedor').val(data.vendedor_cedula);
            },
            error: function(data){
                console.log(data);
            }
        })
    });*/

    $("#vendedor").change(function() {
        $('#cedula_vendedor').val($('option:selected', $(this)).data("id"));
    });
    //Obtengo datos del Recaudador de la Tabla Usuarios
    $("#recaudador").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('recaudador.identificacion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#recaudador"),
            success: function(data) {
                $('#cedula_recaudador').val(data.recaudador_cedula);
            },
            error: function(data) {
                console.log(data);
            }
        })
    });
    //Actualiza la direccion del Cliente
    $("#direccion").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('update_direccion_client')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            //data: $("#identif_cliente"),
            data: {
                'ident_cliente': $("#identif_cliente").val(),
                'direc_cliente': $("#direccion").val()
            },
            success: function(data) {
                //console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    });

    function verificar_stock(e) {
        //console.log($(e).parent().parent().find('.cneto').val());
        var cantidad = $(e).parent().parent().find('.cneto').val();
        var bodega = $(e).parent().parent().find('.bodega').val();
        var producto = $(e).parent().parent().find('.codigo_producto').val();
        $.ajax({
            type: 'post',
            url: "{{route('ventas.inventariable')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                producto: producto,
            },
            success: function(data) {
                if (data >= 1 && bodega != "" && cantidad > 0) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('ventas.stock')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: {
                            producto: producto,
                            bodega: bodega,
                        },
                        success: function(data) {
                            //console.log('data stock',data.length);
                            if (data.length > 0) {
                                //console.log(parseInt(data[0].existencia), parseInt(cantidad));
                                if (parseInt(data[0].existencia) < parseInt(cantidad)) {
                                    swal("No tiene existencia en dicha bodega");
                                    //$(".btn_add").attr("disabled", true);
                                } else {
                                    // $(".btn_add").attr("disabled", false);
                                }
                            } else {
                                swal("El producto no existe en dicha bodega");
                                // $(".btn_add").attr("disabled", true);
                            }
                        },
                        error: function(data) {
                            console.log(data.responseText);
                        }
                    })
                }
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
    }
    //Obtengo el Codigo del Producto por el nombre del producto Ingresado
    function cambiar_nombre(id) {
        $.ajax({
            type: 'post',
            url: "{{route('ventas_buscar_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre" + id).val()
            },
            success: function(data) {
                //console.log(data);
                $('#codigo' + id).val(data.cod_product);
                $('#cost_prod' + id).val(data.cost_vent);
                //$('#iva_obt'+id).val(data.iva_prod);
                if (data.iva_prod == '1') {
                    $('#iva' + id).prop("checked", true);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    //Obtengo el Nombre del Producto por el codigo del producto Ingresado
    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('ventas_buscar_nombre2')}}",
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
    /* function obtener_num_factura(){
         $.ajax({
             url:"{{route('numero_factura')}}",
             type: 'get',
             datatype: 'json',
             success: function(data){
                //console.log(data);
                $('#nfactura').val(data);
             },
             error: function(data){
                 console.log(data);
             }
         })
     }*/
    function limpiar() {
        //obtenemos la fecha actual
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        var fe_proced = new Date('Y-m-d H:i:s');
        $("#fecha").val(today);
        $("#fecha_proced").val(fe_proced);
    }
    //Completa la cedula del Paciente por minimo 3 digitos
    $(".ced_paciente").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('autocomple_paciente_cedula')}}",
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
    //Obtengo datos del paciente por la cedula
    function obtener_datos_paciente() {
        $.ajax({
            type: 'post',
            url: "{{route('obtener_info_paciente')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                'ced_paciente': $("#ced_paciente").val()
            },
            success: function(data) {
                //console.log(data);
                if (data.texto != '') {
                    $('#nomb_paciente').val(data.texto);
                }
                if (data.id_seg != '') {
                    $('#id_seguro').val(data.id_seg);
                }
                /*if(data.trim() == 'error'){
                  alert("No existe el Paciente");
                  $('#ced_paciente').val('');
                  $('#nomb_paciente').val('');
                  $('#id_seguro').val('');
                }*/
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
            url: "{{route('sucursal.empresa')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_emp': id_seleccionado
            },
            success: function(data) {
                //console.log(data);
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

    function obtener_caja() {
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
                //console.log(data);
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
                console.log(data);
            }
        })
    }
    $('#busqueda').click(function(event) {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td><input name="codigo' + id + '" class="codigo" id="codigo' + id + '" style="width: 110px;" onchange="cambiar_codigo(' + id + ')"/><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input type="text" class="hidden" id="id_prod' + id + '" name="id_prod' + id + '"><input type="text" class="hidden" name="cost_prod' + id + '" id="cost_prod' + id + '"></td><td><input name="nombre' + id + '" class="nombre" id="nombre' + id + '"  onchange="cambiar_nombre(' + id + ')"></td><td> <input type="text" style="width: 110px;" id="cantidad' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="return completa_decimales(this,' + id + ',2);" name="cantidad' + id + '"></td><td><input type="text" style="width: 110px;" id="precio' + id + '" name="precio' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onchange="return redondea_precio(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc_porcentaje' + id + '" name="desc_porcentaje' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  onchange="return redondea_descuento(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc' + id + '" name="desc' + id + '"  value="0.00" disabled><input type="text" class="hidden" name="desc1' + id + '" id="desc1' + id + '"></td><td><input type="text" name="extendido' + id + '" id="extendido' + id + '" value="0.00"  value="" disabled><input type="text" class="hidden" name="extendido1' + id + '" id="extendido1' + id + '"></td><td><input type="checkbox" id="iva' + id + '" name="iva' + id + '" disabled></td><td><button type="button" onclick="eliminar_registro(' + id + ')" class="btn btn-warning btn-margin">Eliminar</button></td>';
        document.getElementById('crear').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;
        //Completa el codigo del Producto al Ingresar
        $(".codigo").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('ventas_completa_codigo')}}",
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
        //Completa el nombre  del Producto al Ingresar
        $(".nombre").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('ventas_buscar_nombre')}}",
                    //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    dataType: "json",
                    //type: 'post',
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
    });
    //Elimina Registro de la Tabla Productos
    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
        suma_totales();
    }

    function Cambio() {
        var seguro = document.getElementById("id_seguro");
        var selected = seguro.options[seguro.selectedIndex].text;
        if (selected != "") {
            //alert("esta cambiando el seguro");
        } else {
            console.log(selected);
        }
    }
    $(function() {
        $('#fecha_proced').datetimepicker({
            format: 'YYYY/MM/DD'
        });
        $('.fecha_pago').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });
</script>
@endsection