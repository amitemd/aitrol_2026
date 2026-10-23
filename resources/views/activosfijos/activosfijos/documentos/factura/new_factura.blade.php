@extends('activosfijos.documentos.factura.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
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
</style>




<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
            <li class="breadcrumb-item"><a href="#">Documentos</a></li>
            <li class="breadcrumb-item"><a href="javascript:goBack();">Factura Activo Fijo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Nueva Factura de Activo Fijos</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="return location.reload(true);" class="btn btn-primary btn-gray">
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

    <form class="form-vertical" enctype="multipart/form-data" id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">

            <div class="box-body dobra">
                <div class="header row">

                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="details_details" id="details_count" value="0">
                            <div id="form_acc">

                            </div>

                            <div class="form-group col-xs-7  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">Sucursal (Empresa)</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($sucursales as $value)
                                        <option value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="punto_emision" class="label_header">P. Emision (Empresa)</label>
                                    <input type="hidden" id="electronica" name="electronica" value="0">
                                </div>
                                <div class="col-md-12 px-0">
                                    <select class="form-control" name="punto_emision" id="punto_emision" required>
                                        <option value="">Seleccione...</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="id" class=" label_header">Id</label>
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
                                    <label for="numero" class=" label_header">Número</label>
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
                                    <label for="tipo" class="label_header">Tipo</label>
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
                                    <label for="asiento_id" class="label_header">Asiento</label>
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
                                    <label for="fecha_asiento" class="label_header">Fecha Asiento</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input id="fecha_asiento" type="date" class="form-control" name="fecha_asiento" value="@php echo date('Y-m-d');@endphp">
                                    @if ($errors->has('fecha_asiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_asiento') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="fecha_caduca" class="label_header">Fecha Caduca</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input id="fecha_caduca" type="date" class="form-control" name="fecha_caduca" value="@php echo date('Y-m-d');@endphp">
                                    @if ($errors->has('fecha_caduca'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_caduca') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-4  col-md-4  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="proveedor" class="label_header">Proveedor</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas" id="proveedor" onchange="llenarCampo()" name="proveedor" width="100%">
                                        <option value="">Seleccione...</option>
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
                                    <label for="divisas" class="label_header">Divisas</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select id="divisas" name="divisas" class="form-control select2_cuentas">
                                        @foreach($divisas as $value)
                                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="termino" class="label_header">Término</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select id="termino" name="termino" class="form-control select2_cuentas">
                                        <option value="">Seleccione...</option>
                                        @foreach($term as $t)
                                        <option value="{{$t->id}}">{{$t->nombre}}</option>
                                        @endforeach
                                        <!--  <option value="4">30 Dias</option>
                                        <option value="9">60 Dias</option>
                                        <option value="9">Credito</option> -->
                                    </select>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">


                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="ord_compra" class=" label_header">O. Compra</label>
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
                                    <label for="nro_autorizacion" class=" label_header">Autorización</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nro_autorizacion" id="nro_autorizacion" value="">
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
                                    <label for="fecha_compra" class=" label_header">Fecha</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="date" class="form-control col-md-12" name="fecha_compra" id="fecha_compra" value="@php echo date('Y-m-d');@endphp">

                                    @if ($errors->has('fecha_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_compra') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="serie_factura" class=" label_header">Serie</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" onkeyup="agregar_serie(); llenarCampo();" name="serie_factura" id="serie_factura" value="" maxlength="7">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie_factura').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="secuencia" class=" label_header">Secuencia Factura</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="secuencia" id="secuencia" value="" onchange="ingresar_cero('secuencia', 10); llenarCampo()" autocomplete="off">
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
                                    <label class="label_header">Credito Tributario</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas ">
                                        <option value="">Seleccione...</option>
                                        @foreach($c_tributario as $value)
                                        <option value="{{$value->codigo}}">{{$value->codigo}}-{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="tipo_comprobante" class="label_header">Tipo comprobante</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas" id="tipo_comprobante" name="tipo_comprobante">
                                        <option value="">Seleccione...</option>
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
                                    <label for="concepto" class=" label_header">Concepto</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <input type="text" class="form-control" name="concepto" id="concepto" value="" width="100%">

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
                                <th width="10%" class="" tabindex="0">Codigo</th>
                                <th width="30%" class="" tabindex="0">Descripción del Activo</th>
                                <th width="10%" class="" tabindex="0">Cantidad</th>
                                <th width="10%" class="" tabindex="0">Costo</th>
                                <th width="10%" class="" tabindex="0">% Desc</th>
                                <th width="10%" class="" tabindex="0">Descuento</th>
                                <th width="10%" class="" tabindex="0">Total</th>
                                <th width="5%" class="" tabindex="0">IVA</th>
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="crearFila();" type="button" class="btn btn-success btn-gray btn-sm">
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
                        <tfoot class=''>
                            <!--tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">Subtotal 12%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr-->

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Subtotal</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
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
                    <button class="btn btn-success btn-gray" onclick="guardar(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
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



        <!--MODAL-->

        <!--Modal fin-->

        <input name='contador_items' id='contador_items' type='hidden' value="1">
    </form>
</section>

@include('activosfijos.documentos.factura.mdactivo')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function agregar_serie() {
        var serie = $('#serie_factura').val();
        if ((serie.length) == 3) {
            $('#serie_factura').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie_factura').val('');
            swal("Error!", "Ingrese la serie de la factura correctamente", "error");
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
                    console.log(concat);
                }
                id.value = concat;
            } else {
                alerta("Error!", "error", "Valor incorrecto");
                id.value = "";
            }
        } else {
            alerta("Error!", "error", "Valor incorrecto");
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
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                //console.log(data);
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
                console.log(data);
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
                console.log(data);
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
                swal("Error!", "Valor no permitido", "error");
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
            swal("Error!", "Valor no permitido", "error");
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

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
</script>



<script type="text/javascript">
    $('#modal_datosactivo').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });

        $('.select2_cuentas2').select2({
            tags: false
        });

        $('.select2_color').select2({
            tags: true
        });


        crearFila();
    });

    const crearFila = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                    <button type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id}, event)"> <i class="glyphicon glyphicon-edit"></i></button>  
                </td>
                <td>
                    <input id="codigo${id}" class="form-control" type="text" style="width: 80%;height:20px;" name="codigo[]" readonly >
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control" type="text" style="width: 93%;height:20px;" name="descrip_prod[]" readonly>
                    <textarea  rows="3" name="observacion[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" >

                </td>
                <td>
                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" >
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" >
                </td>
                <td>
                    <input onchange="descuento(${id});"  id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva${id}" >
                    <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                    <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor">
                    <input id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

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

    function select_iva(id) {
        var check = document.getElementById("valoriva" + id);
        var check_af = document.getElementById("check_iva" + id);

        if (check.checked) {
            check_af.value = 1;
        } else {
            check_af.value = 0;
        }
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)
        let val_iva = document.getElementById("val_iva" + id)
        let total_valor = document.getElementById("total_valor" + id)

        let preXcantidad = (precio * cantidad);
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        let valoriva = 0;


        if ($('#valoriva' + id).prop('checked')) {
            valoriva = precioTotal * 0.12;
        }

        let total = precioTotal + valoriva;

        val_iva.value = valoriva.toFixed(2);
        desc.value = porcDescuento.toFixed(2);
        precioneto.value = precioTotal.toFixed(2);
        total_valor.value = total.toFixed(2);

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
        console.log(preXcantidad);
        descpor.value = descuento.toFixed(2);
        totalProducto(id);
    }



    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let vliva = document.querySelectorAll(".val_iva");
        let tot_valor = document.querySelectorAll(".total_valor");

        let total = 0;
        let descuento = 0;
        let iva = 0;
        let totv = 0;

        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value);
            // console.log(total);
            descuento += parseFloat(desc[i].value);

            iva += parseFloat(vliva[i].value);

            totv += parseFloat(tot_valor[i].value);
        }

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
        totales(0);
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
                    <input type="hidden" name="transporte" value="TRANS"/>
                    <input id="codigo${id}" class="form-control" type="text" style="width: 80%;height:20px;" name="codigo_trans[]" value="TRANS" readonly>
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control" type="text" style="width: 93%;height:20px;" name="descrip_prod_trans[]" value="TRANS | TRANSPORTE" readonly>
                    <textarea  rows="3" name="observacion_trans[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_trans[]" >

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
                    <input id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

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
                    <input id="codigo${id}" class="form-control" type="text" style="width: 80%;height:20px;" name="codigo_gasto[]" value="" readonly>
                </td>
                <td>
                    <select id="id_plan${id}" name="id_plan[]" class="form-control select2_cuentas" style="width: 100%;">
                        <option value=""> Seleccione...</option>
                        @foreach($plan_cuentas as $value)
                            <option value="{{$value->id_plan}}"> {{$value->nombre}} </option>
                        @endforeach 
                    </select>
                    <textarea  rows="3" name="observacion_gasto[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_gasto[]" >

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
                    <input id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

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
            camposVacios.push('proveedor')
        }
        if (document.getElementById("divisas").value == '') {
            camposVacios.push('divisas')
        }
        if (document.getElementById("termino").value == '') {
            camposVacios.push('termino')
        }
        if (document.getElementById("cred_tributario").value == '') {
            camposVacios.push('credito tributario')
        }
        if (document.getElementById("nro_autorizacion").value == '') {
            camposVacios.push('numero autorización')
        }

        if (document.getElementById("secuencia").value == '') {
            camposVacios.push('secuencia')
        }
        if (document.getElementById("tipo_comprobante").value == '') {
            camposVacios.push('tipo comprobante')
        }
        if (document.getElementById("concepto").value == '') {
            camposVacios.push('concepto')
        }
        if (camposVacios.length > 0) {
            $('#boton_guardar').prop("disabled", false);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `Los siguientes campos estan vacios: 
                    ${
                       camposVacios.map(i=>i)    
                    }
                    `
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

        if (!resp) {
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
                        alerta('Exito', data.respuesta, data.msj)
                        $('#asiento_id').val(data.id_asiento)
                    } else {
                        alerta('Error', data.respuesta, data.msj)
                    }

                },
                error: function(data) {
                    console.log(data.responseText);


                }
            });
        }


    }



    const llenarCampo = () => {
        let proveedor = document.getElementById("proveedor");

        let serie = document.getElementById("serie_factura").value;
        let secuencia = document.getElementById("secuencia").value;
        let value = proveedor.options[proveedor.selectedIndex].text;
        if (value != '') {
            document.getElementById("concepto").value = `ACT-FA # ${value} ${serie}-${secuencia}`

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