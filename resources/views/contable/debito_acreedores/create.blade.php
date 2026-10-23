@extends('contable.debito_acreedores.base')
@section('action-content')

@php
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
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
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{route('debitoacreedores.index')}}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('debitoacreedores.index')}}">Nota de Débito Acreedores</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva nota de Debito</li>
        </ol>
    </nav>
    <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Guardado Correctamente
    </div>
    <form class="form-vertical " method="post" id="form_guardado" name="form_guardado">
            {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-9 col-6">
                            <div class="box-title "><b>Nota de Débito Acreedores</b></div>
                        </div>
                        <div class="col-md-6 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <a type="button" id="boton_guardar" href="javascript:boton_deuda()" class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </a>
                                <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12 px-1">
                        <div class="form-row ">

                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="label_header">Estado</label>
                                <div style="background-color: green;" class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control" type="text" name="id_factura" id="id_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control" type="text" id="numero_factura" name="numero_factura" readonly>

                            </div>
                            <div class=" col-md-1 px-1">

                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control" type="text" name="tipo" id="tipo" value="ACR-DB" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control" type="text" id="asiento" name="asiento" readonly>
                                @if(!is_null($iva_param))

                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$iva_param->iva}}">
                                @endif

                            </div>

                            <div class=" col-md-2 px-1">

                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control" type="date" name="fecha_hoy" id="fecha_hoy" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{date('Y-m-d')}}" >

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_caducidad">Fecha Caducidad: </label>
                                <input class="form-control" type="date" name="fecha_caducidad" id="fecha_caducidad" value="{{date('Y-m-d')}}">

                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-2 px-1">
                                {{ csrf_field() }}
                                <input type="hidden" name="superavit" id="superavit" value="0">
                                <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                <select class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor" onchange="buscar_proveedor()">
                                    <option value="">Seleccione...</option>
                                    @foreach($proveedores as $value)
                                    <option value="{{$value->id}}"> {{$value->id}} {{$value->nombrecomercial}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header" for="autorizacion">Autorización</label>
                                <input type="text" name="autorizacion" id="autorizacion" class="form-control">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="col-md-12 label_header control-label" for="serie">Serie</label>
                                <input type="text" class="form-control" id="serie" name="serie" onkeyup="agregar_serie()">
                            </div>
                            <div class="col-md-2 px-1">
                                <label for="secuencia" class="label_header col-md-12 control-label">Secuencia</label>
                                <input type="text" class="form-control" id="secuencia" name="secuencia" onchange="ingresar_cero();">
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fechand">Fecha N/D: </label>
                                <input class="form-control" type="date" name="fechand" id="fechand" value="{{date('Y-m-d')}}">

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Credito Tributario</label>
                                <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas" style="width: 100%; heigth: 22px">
                                    <option value="">Seleccione...</option>
                                    @foreach($c_tributario as $value)
                                    <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 px-1">
                                <input type="hidden" name="total_suma" id="total_suma">
                                <label class="label_header" for="concepto">Concepto:</label>
                                <input class="form-control col-md-12" type="text" name="concepto" id="concepto">
                            </div>
                            <div class="col-md-2 px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                                <select name="sucursal" id="sucursal" class="form-control" required>
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo de Comprobante</label>
                                <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas" style="width: 100%;heigth: 22px">
                                    <option value="">Seleccione...</option>
                                    @foreach($t_comprobante as $value)
                                    <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_factura">Fecha factura: </label>
                                <input class="form-control" type="date" name="fecha_factura" id="fecha_factura" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="serie_factura">Serie Fact:</label>
                                <input class="form-control col-md-12" type="text" name="serie_factura" id="serie_factura" onkeyup="agregar_serie2()">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="secuencia_fact">Secuncia . Fact:</label>
                                <input class="form-control col-md-12" type="text" name="secuencia_fact" id="secuencia_fact" onchange="ingresar_cero2();">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="autorizacion_factura">Autori. Fact:</label>
                                <input class="form-control col-md-12" type="text" name="autorizacion_factura" id="autorizacion_factura">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header">Número de Factura</label>
                                <input class="form-control" type="text" name="nro_factura" id="nro_factura" autocomplete="off">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo de Comprobante</label>
                                <select name="tipo_comprobante2" id="tipo_comprobante2" class="form-control  select2_cuentas" style="width: 100%;heigth: 22px">
                                    <option value="">Seleccione...</option>
                                    @foreach($t_comprobante as $value)
                                    <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS</label>
                    </div>
                    <div class="col-md-12 px-1">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <input type="hidden" name="contador" id="contador" value="0">
                        <div class="table-responsive col-md-12 px-1">
                            <table id="example3" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 16.66%; text-align: center;">Rubro</th>
                                        <th style="width: 16.66%; text-align: center;">Detalle</th>
                                        <th style="width: 16.66%; text-align: center;">Divisa</th>
                                        <th style="width: 16.66%; text-align: center;">Valor</th>
                                        <th style="width: 16.66%; text-align: center;">Total Base</th>
                                        <th style="width: 16.66%; text-align: right;">
                                            <button onclick="crea_td()" type="button" class="btn btn-success btn-gray btn-xs">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="det_recibido">
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
                                    <label class="label_header" for="subtotal">Subtotal</label>
                                    <input class="form-control  col-md-12" type="text" name="subtotal" id="subtotal" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="impuesto">Impuesto</label>
                                    <input class="form-control  col-md-12" onchange="sumar_impuesto()" type="text" name="impuesto" id="impuesto">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="total">Total</label>
                                    <input class="form-control  col-md-12" type="text" name="total" id="total" readonly>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS CON EL PROVEEDOR</label>
                        <input type="hidden" name="contadore" id="contadore" value="0">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px;">

                            <table id="example2" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr style="position: relative;">

                                        <th style="width: 8%; text-align: center;">Vence</th>
                                        <th style="width: 10%; text-align: center;">Tipo</th>
                                        <th style="width: 10%; text-align: center;">Número</th>
                                        <th style="width: 10%; text-align: center;">Concepto</th>
                                        <th style="width: 6%; text-align: center;">Div</th>
                                        <th style="width: 6%; text-align: center;">Saldo</th>
                                        <th style="width: 6%; text-align: center;">Abono</th>
                                        <th style="width: 6%; text-align: center;">Saldo Base</th>
                                        <th style="width: 6%; text-align: center;">Abono Base</th>

                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 5) as $i)

                                    <tr>
                                        <td> <input style="width: 100%;" type="text" name="vence{{$cont}}" id="vence{{$cont}}" disabled="disabled"> </td>
                                        <td> <input style="width: 100%;" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" disabled="disabled"> </td>
                                        <td> <input style="width: 100%;" type="text" name="numero{{$cont}}" id="numero{{$cont}}" disabled="disabled"> </td>
                                        <td> <input style="width: 100%;" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" disabled="disabled"> </td>
                                        <td> <input style="background-color: #c9ffe5; width: 100%;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" disabled="disabled"> </td>
                                        <td> <input style="background-color: #c9ffe5; width: 100% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" disabled="disabled"> </td>
                                        <td> <input style="background-color: #c9ffe5; width: 100%; text-align: right;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" disabled="disabled"></td>
                                        <td> <input style="width: 100%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" disabled="disabled"></td>
                                        <td> <input style="width: 100%; text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" disabled="disabled"> </td>
                                    </tr>
                                    @php $cont = $cont +1; @endphp
                                    @endforeach

                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: black;" for="nota">Nota: </label>
                            <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                            <input type="hidden" name="saldo_final" id="saldo_final">
                        </div>
                    </div>

                </div>

            </div>
    </form>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#fact_contable_check').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
        });
        $('.select2_cuentas').select2({
            tags: false
        });
    });

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            alert("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
    }

    function agregar_serie2() {
        var serie = $('#serie_factura').val();
        if ((serie.length) == 3) {
            $('#serie_factura').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie_factura').val('');
            alert("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
    }

    function ingresar_cero2() {
        var secuencia_factura = $('#secuencia_fact').val();
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#secuencia_fact').val('');

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
                $('#secuencia_fact').val(secuencia + secuencia_factura);
            }


        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#secuencia_fact').val('');
        }
    }

    function ingresar_cero() {
        var secuencia_factura = $('#secuencia').val();
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#secuencia').val('');

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
                $('#secuencia').val(secuencia + secuencia_factura);
            }


        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#secuencia').val('');
        }
    }

    function buscar_factura() {
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url: "{{route('acreedores_buscar_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_factura': $("#buscar").val()
            },
            success: function(data) {
                var esfact = $("#esfac_contable").val();
                var iva = (data[10] * 0.12);
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[12]);
                $("#concepto").val(data[4] + '.' + ' ' + 'REF :' + data[0]);
                $("#asiento").val(data[11]);
                $("#id_proveedor").val(data[0]);
                $('#nombre_proveedor').val(data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#total_deudas").val(data[10]);
                //$("#id_compra").val(data[1]);
                $("#vence").val(data[6]);
                buscar_proveedor();
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    $(".buscar").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('retenciones_codigo')}}",
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
    $('#fact_contable_check').on('ifChanged', function(event) {
        //aqui funciona si cambio el input time
        if ($(this).prop("checked")) {
            $("#esfac_contable").val(1);
        } else {
            $("#esfac_contable").val(0);
        }

    });

    function funciones_pago() {
        var formas_pago = parseInt($("#formas_pago").val());
        switch (formas_pago) {
            case 1:
                [].forEach.call(document.querySelectorAll('.visibilidad'), function(el) {
                    el.style.display = 'none';
                });
                break;
            case 2:
                [].forEach.call(document.querySelectorAll('.visibilidad'), function(el) {
                    el.style.display = 'block';
                });
                break;
        }
    }
    /*
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
        
                $("#id_proveedor").val(data.value);
                
            },
            error: function(data){
            }
        })
    }*/
    $(".nombre_proveedor").autocomplete({
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
        minLength: 1,
    });

    function abono_totales() {
        var valor = parseFloat($("#valor_cheque").val());
        var saldo = parseFloat($("#saldo0").val());
        if (!isNaN(valor)) {
            $("#abono0").val(valor);
            $("#abono_base0").val(valor);
            var totales = saldo - valor;
            //alert(totales);
            if (totales > 0) {
                $("#saldo0").val(totales.toFixed(2));
                var saldo_hidden = parseFloat($("#saldo_hidden0").val());
                var total_sinresta = saldo_hidden.toFixed(2) - totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
            } else {
                var saldo_hidden = parseFloat($("#saldo_hidden0").val());
                var total_sinresta = saldo_hidden.toFixed(2) - totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
                $("#saldo0").val('0');
            }

        } else {
            var valor = parseFloat($("#saldo_hidden0").val());
            //alert(valor);
            $("#saldo0").val(valor);
            $("#abono_base0").val(valor);
        }
    }
    $(document).on("focus", "#nro_factura", function() {

        $("#nro_factura").autocomplete({

            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('notacreditocreedores.obtener_num_fact')}}",
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
                $("#id_comp").val(ui.item.id_compra);
                $("#serie_factura").val(ui.item.serie);
                $("#autorizacion_factura").val(ui.item.autorizacion);
                $("#secuencia_fact").val(ui.item.secuencia);
                $("#nombre_proveedor").val(ui.item.nomb_proveedor);
                $("#id_proveedor").val(ui.item.id_proveedor);
                $("#val_contable").val(ui.item.val_contable);

                //obtener_detalle_deudas();
                //obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,
        });

    });

    function disabled() {
        //tocreate array
        contador = parseInt($("#contadore").val());
        var complex = [];
        var a = ['abono', 'id'];
        for (i = 0; i <= contador; i++) {
            if (parseFloat($("#abono" + i).val()) > 0) {
                //complex=[{abono:$("#abono_a"+i).val(),id:$("#vence"+i).val()},];
                //complex[a[$("#abono_a"+i).val()]] = $("#vence"+i).val();
                //complex.push(complex);
                complex.push({
                    abono: $("#abono" + i).val(),
                    id: $("#id_actualiza" + i).val(),
                    numero: $("#numero" + i).val(),
                    saldo: $("#nuevo_saldo" + i).val()
                });
            }
        }
        //$("#invoces").val(complex);
        return JSON.stringify(complex);
    }

    function boton_deuda() {
        //swal("hassta aqui");
        let boton = document.getElementById('boton_guardar');

        boton.style.display = "none"

        var formulario = document.forms["form_guardado"];
        var superavit = parseInt($("#verificar_superavit").val());
        var msj = "";
        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = parseFloat($("#valor_cheque").val());
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        var fecha_factura = formulario.fecha_factura.value;
        var fecha = formulario.fecha_hoy.value;
        var concepto = formulario.concepto.value;
        var total = formulario.total.value;
        var total_suma = formulario.total_suma.value;
        var contador = formulario.contador.value;
        var sucursal = $("#sucursal").val();
        var invoices = disabled();
        var data = $('form#form_guardado').serializeArray();
        data.push({
            name: 'listInvoice',
            value: invoices
        });
        if (fecha_factura == "") {
            msj += "Ingrese fecha factura <br>";
        }
        if (serie == "") {
            msj += "Ingrese serie factura <br>";
        }
        if (concepto == "") {
            msj += "Ingrese concepto <br>";
        }
        if (total == "") {
            msj += "Ingrese valores en el detalle de rubros <br>";
        }
        if (contador == 0) {
            msj += "Complete campos de la tabla <br>";
        }
        if (total_suma == 0) {
            msj += "Complete el pago al proveedor <br>";
        }
        if (sucursal == 0 || sucursal == "") {
            alertas("error", "Error...", "Seleccione la sucursal")
            return;
        }
        if (msj == "") {
            if ($("#form_guardado").valid()) {
                $.ajax({
                    type: 'post',
                    url: "{{route('debitoacreedores.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: data,
                    success: function(data) {
                        if (data.status == 'success') {
                        $("#id_factura").val(data.id);
                        buscarAsiento(data.id);
                            $('#form_guardado input').attr('readonly', 'readonly');
                            swal("Correcto!", "Nota de débito generada con exito", "success");
                        } else {
                            swal("Error!", data.message, data.status);
                        }
                        /*if ((data) != 'false') {
                            $("#id_factura").val(data);
                            buscarAsiento(data);
                            $('#form_guardado input').attr('readonly', 'readonly');
                            //$("#boton_guardar").attr("disabled", "readonly");
                            swal("Correcto!", "Nota de débito generada con exito", "success");
                        } else {

                        }*/
                    },
                    error: function(data) {
                        boton.style.display = "block"
                        swal("Error!", data.message, "error");
                    }
                })
            }


        } else {
            $('#boton_guardar').removeAttr("disabled");
            swal("Error!", msj, "error");
        }

    }

    function buscarAsiento(id_asiento) {
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '5'
            },
            success: function(data) {
                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function set_rubros(id) {
        $.ajax({
            type: 'post',
            url: "{{route('rubrosa.nombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#rubro" + id).val()
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#id_codigo" + id).val(data[0]);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function setNumber(e) {
        // return parseFloat(e).toFixed(2);
        //if(e.length)
        if (e == "") {
            e = 0;
        }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }

    function nuevo_comprobante() {
        location.href = "{{route('debitoacreedores.create')}}";
    }

    function crea_td(contador) {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);

        midiv.innerHTML = '<td> <select style="width: 100%;" class="form-control select2_cuentas" name="id_codigo' + id + '" id="id_codigo' + id + '" required> <option value="" >Seleccione...</option> @foreach($rubros as $x) <option value="{{$x->codigo}}">{{$x->nombre}}</option> @endforeach </select> </td> <td> <input style="width: 98%;" name="detalle_rubro' + id + '" id="detalle_rubro" required ></td> <td><input style="width: 98%;" name="divisas" id="divisas" value="USD" readonly ></td> <td> <input class="valortotal" name="valor' + id + '" style="width: 98%;" id="valor' + id + '" onchange="valor_rubro(' + id + ')" value="0.00" ></td><input class="visibilidad" type="hidden" name="visibilidad' + id + '" id="visibilidad' + id + '" value="1"><td><input style="width: 90%;" name="total_base' + id + '" id="total_base' + id + '" value="0.00" readonly></td><td><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;
        $('.select2_cuentas').select2({
            tags: false
        });
        $(".nombre").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('compra_nombre')}}",
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
        $(".rubrosa").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('rubrosa.searchcode')}}",
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
        $(".codigo").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('compra_codigo')}}",
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

    }

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = "visibilidad" + valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display = 'none';
        sumar();
    }

    function valor_rubro(id) {
        var e = parseFloat($("#valor" + id).val());
        var coniva = 0;
        var total = 0;
        if (e == "") {
            e = 0;
        }
        if (isNaN(e)) {
            e = 0;
        }
        $("#valor" + id).val(parseFloat(e).toFixed(2, 2));
        $("#total_base" + id).val(parseFloat(e).toFixed(2, 2));
        $("#subtotal").val(parseFloat(e).toFixed(2, 2));
        sumar();
        sumar2();
    }

    function sumar() {
        var contador = 0;
        var iva = parseFloat($("#iva_par").val());
        var ivan = 0;
        var total = 0;
        var totaal = 0;
        var sub = 0;
        var valor_d = 0;
        var ivaf = 0;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                valor = parseFloat($(this).find('#valor' + contador).val());
                ivan = ivan + valor;
                sub = sub + valor;
                totaal = valor + ivan;
                total = total + totaal;
            }
            contador = contador + 1;
        });
        var totalsx = ivan * iva;
        var total_final = ivan;
        var impuesta = 0;
        if (!isNaN(ivan)) {
            $('#impuesto').val(impuesta.toFixed(2));
        }
        if (!isNaN(sub)) {
            $('#subtotal').val(sub.toFixed(2));
        }
        if (!isNaN(total)) {
            $('#total').val(total_final.toFixed(2));
        }
    }

    function sumar_impuesto() {

        var subtotal = parseFloat($("#subtotal").val());
        if (isNaN(subtotal)) {
            subtotal = 0;
        }
        var impuesto = parseFloat($("#impuesto").val());
        if (isNaN(impuesto)) {
            impuesto = 0;
        }
        $("#impuesto").val(impuesto.toFixed(2, 2));
        var s = subtotal * 0.12;
        var t = s + subtotal;
        var total = subtotal + impuesto;
        if (total == t) {
            $("#total").val(total.toFixed(2, 2));
            //if(!isNaN(s)){$('#tar_iva_12').val(s.toFixed(2));}
            //$("#total_credito").val(total.toFixed(2,2));
        } else {
            total = subtotal;
            impuesto = 0;
            $("#impuesto").val(impuesto.toFixed(2, 2));
            $("#total").val(total.toFixed(2, 2));
            //if(!isNaN(s)){$('#tar_iva_12').val(s.toFixed(2));}
            //$("#total_credito").val(total.toFixed(2,2));

        }
    }

    function sumar2() {
        var contador = 0;
        var iva = parseFloat($("#iva_par").val());
        var ivan = 0;
        var total = 0;
        var totaal = 0;
        var sub = 0;
        var valor_d = 0;
        var ivaf = 0;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                valor = parseFloat($(this).find('#total_base' + contador).val());
                ivan = ivan + valor;
                sub = sub + valor;
                totaal = valor + ivan;
                total = total + totaal;
            }
            contador = contador + 1;
        });
        var totalsx = ivan * iva;
        var total_final = totalsx + ivan;
        if (!isNaN(total)) {
            $('#total_suma').val(total_final.toFixed(2));
        }
    }

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedornombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#id_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                    //buscar_proveedor()

                } else {
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#autorizacion').val("");
                    //buscar_proveedor()
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function buscar_proveedor() {
        var proveedor = $("#id_proveedor").val();
        var tipo = parseInt($("#esfac_contable").val());
        var provedores = $("#nombre_proveedor").val();
        cambiar_nombre_proveedor()
        $("#giradoa").val(provedores);
        $.ajax({
            type: "post",
            url: "{{route('acreedores_buscarproveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                'proveedor': proveedor,
                'tipo': tipo
            },
            success: function(data) {
                if (data.value != "no resultados") {
                    $("#crear").empty();
                    var fila = 0;
                    for (i = 0; i < data[5].length; i++) {

                        if (data[5][i].tipo == 1) {
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FA', data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else if (data[5][i].tipo == 2) {
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FACT', data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else {
                            var row = addNewRowf(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'SALDO INICIAL', data[5][i].numero, data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        }

                    }

                    $("#contadore").val(fila);

                }

            },
            error: function(data) {
                console.log(data);

            }
        });



    }

    function validar_td(id) {
        if ((id) != null) {
            var valor = parseFloat($("#total").val());
            var abono = parseFloat($("#abono" + id).val());
            var saldo = parseFloat($("#saldo" + id).val());
            suma_totales();
            var cantidad = parseFloat($("#total_suma").val());
            if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                if (cantidad <= valor) {
                    if (abono > saldo) {
                        abono = saldo;
                    }
                    $("#abono" + id).val(abono.toFixed(2, 2));
                } else {
                    valor = 0;
                    $("#abono" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "Error no puede superar al valor total", "error")
                }
            } else {
                abono = 0;
                valor = 0;
                $("abono" + id).val(valor.toFixed(2, 2));
            }
        } else {
            alert("error");
        }
    }

    function suma_totales() {
        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;

        $("#crear tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#abono" + contador).val());
            if (!isNaN(cantidad)) {
                total += cantidad;
            }
            contador = contador + 1;
        });
        if (isNaN(total)) {
            total = 0;
        }
        $("#total_suma").val(total.toFixed(2, 2));

        //alert(total_fin);

    }

    function rellena_ceros() {
        contador = 0;
        cero = 0;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                $(this).find('#valor' + contador).val(cero.toFixed(2, 2));
            }
            contador = contador + 1;
        });
    }

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text'  id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "> <input class='form-control' type='hidden'  id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text'  id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' id='concepto" + pos + "' value='Fact #:" + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;'  id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; '  value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' id='abono" + pos + "' onchange='validar_td(" + pos + ")'></td>" +
            "<td> <input class='form-control' type='text' style=' text-align: left;'  value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +

            "</tr>";
        return markup;

    }

    function addNewRowf(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text'  id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            " <input class='form-control' type='hidden'  id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text'  id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' id='concepto" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;'  id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;'  id='abono" + pos + "' onchange='validar_td(" + pos + ")'></td>" +
            "<td> <input class='form-control' type='text' style=' text-align: left;' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +

            "</tr>";
        return markup;

    }


    document.getElementById("fecha_hoy").addEventListener('blur', function() {

        $fechaAc = new Date();
        $mes = $fechaAc.getUTCMonth() + 1;
        var d = new Date(this.value);
        var month = d.getMonth() + 1;

        var ty = document.getElementById("fechita").value;
        if ($mes != month) {


            swal("Recuerde!", "La fecha que ingresa está fuera del periodo ", "error")
            //document.getElementById("fecha_hoy").value = ty;
            //location.reload();
        }


    });
    function validarCierre() {
        <?php
            if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_hoy").value;
            //console.log(fecha2);
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                //console.log(fechaFinal,fechaInicial);
                swal("Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.', "error");
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_hoy").value = fechaFormateada;
            }
        <?php
            }
        ?>
    }




</script>

@endsection