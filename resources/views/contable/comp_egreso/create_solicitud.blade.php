@extends('contable.comp_egreso.base')
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

    #mostrar_anticipos tr td {
        color: #F3785A;
        border: 1px solid #d2d6de;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('acreedores_cegreso_solicitud') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('acreedores_cegreso')}}">{{trans('contableM.ComprobantedeEgreso')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.NuevoComprobantedeEgreso')}}</li>
        </ol>
    </nav>
    <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{trans('contableM.GuardadoCorrectamente')}}
    </div>
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-9 col-6">
                            <div class="box-title "><b>{{trans('contableM.COMPDEEGRESOSACREEDORES')}}</b></div>
                        </div>
                        <div class="col-md-2 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <input type="hidden" id="minDate" name="minDate" value="@if(isset($min_date)){{$min_date}}@endif">
                                <button type="button" id="botong" class="btn btn-success btn-gray" onclick="guardar()">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                </button>
                                <button type="button" class="btn btn-success btn-gray" onclick="nuevo_comprobante()">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <button type="button" class="btn btn-success btn-gray" onclick="goBack()">
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
                            <div class=" col-md-2 px-1">
                                <label class="label_header">{{trans('contableM.estado')}}</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}:</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="ACRE-SOL" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control" type="date" min="@if(isset($min_date)){{$min_date}}@endif" name="fecha_hoy" id="fecha_hoy" value="{{ date('Y-m-d') }}">
                                <input type="hidden" name="total_favor" id="total_favor">
                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-10 px-0">
                                <label class="label_header" for="acreedor">{{trans('contableM.concepto')}}:</label>
                                <input class="form-control  col-md-12" type="text" name="aaa" autocomplete="off" id="aaa" required>
                            </div>
                            <div class=" col-md-2 px-0">
                                <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                <!-- <label style="margin: 0px;" class="container col-md-12">{{trans('contableM.Chequeentregado')}}
                                    <input class="spropety" type="checkbox" id="cheque_entregado" name="cheque_entregado">
                                </label>
                                <div style="text-align: center;">
                                    <label style="font-size:19px;" class="col-md-12">{{trans('contableM.rtasumidas')}}
                                        <input class="spropety" type="checkbox" id="ch_rt_asumidas" name="ch_rt_asumidas" onchange="retencion_asumidas()">
                                    </label>
                                </div> -->

                                <div style="text-align: center;">
                                    <label style="font-size:15px;" class="col-md-12">Aprobación De Directorio
                                        <input style="margin-left: 25px;" class="spropety" type="checkbox" id="aprobacion_directorio_checkbox" onchange="aprobacion()">
                                    </label>
                                    <!-- Input oculto que se enviará en el request -->
                                    <input type="hidden" id="aprobacion_directorio" name="aprobacion_directorio" value="0">
                                </div>

                            </div>
                            <div class=" col-md-4  px-0">
                                <label class="col-md-12 label_header" for="ruc">{{trans('contableM.banco')}}:</label>
                                <select class="form-control " name="banco" id="banco" onchange="bloquear_cheque();">
                                    <option value="0">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-0">
                                <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.divisass')}}:</label>
                                <select class="form-control col-md-12 " name="divisas" id="divisas">
                                    <option value="0">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.cambio')}}:</label>
                                <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header control-label" for="numero_cheque">{{trans('contableM.NroCheque')}}</label>
                                <input class="form-control " type="number" name="numero_cheque" id="numero_cheque">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="fecha_cheque">{{trans('contableM.fechacheque')}}: </label>
                                <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="valor" class="label_header">{{trans('contableM.valor')}}</label>
                                <input class="form-control " type="text" autocomplete="off" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            <div class=" col-md-4 px-0">
                                {{ csrf_field() }}
                                <input type="hidden" name="superavit" id="superavit" value="0">
                                <input type="hidden" name="comprobarx" id="comprobarx" value="0">
                                <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.acreedor')}}</label>
                                <!--<input type="text" id="nombre_proveedor" name="nombre_proveedor" class="form-control form-control-sm nombre_proveedor " onchange="cambiar_nombre_proveedor()">-->
                                <!-- <select name="nombre_proveedor" id="nombre_proveedor" class="form-control form-control-sm select2_cuentas" onchange="cambiar_nombre_proveedor(this);">
                                    <option value="">{{trans('agenda.seleccione')}}</option>
                                    @foreach($proveedor as $value)     
                                    <option value="{{$value->id}}" > {{$value->id}} || {{$value->nombrecomercial}}</option>
                                    @endforeach 
                                </select> -->
                                <select class="form-control select2_proveedor" style="width: 100%;" onchange="cambiar_nombre_proveedor(this);anterioresAnticipos();" name="nombre_proveedor" id="nombre_proveedor">
                                </select>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="direccion">{{trans('contableM.direccion')}}: </label>
                                <input class="form-control " type="text" name="direccion" id="direccion" autocomplete="off">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="ruc">{{trans('contableM.ruc')}}:</label>
                                <input class="form-control " type="text" name="id_proveedor" id="id_proveedor" readonly>
                            </div>
                            <div class=" col-md-1">
                                <div class="col-md-12">&nbsp;</div>
                                <button type="button" class="btn btn-success btn-xs btn-gray" id="aplicar_deuda" onclick="boton_deuda();">{{trans('contableM.AplicarDeuda')}}</button>
                            </div>
                            <div class=" col-md-4  px-1">
                                <label class="col-md-12 label_header" for="girado"> {{trans('contableM.giradoa')}}:</label>
                                <input class="form-control " type="text" name="giradoa" id="giradoa">
                            </div>
                            <div class="col-md-4 px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.formasdepago')}}</label>
                                <select name="formas_pago" id="formas_pago" class="form-control">
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($formas_pago as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                                <select name="sucursal" id="sucursal" class="form-control" onchange="buscar_proveedor();">
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_revision">FECHA REVISION DE FLUJO: </label>
                                <input class="form-control" type="date" min="@if(isset($min_date)){{$min_date}}@endif" name="fecha_revision" id="fecha_revision" value="{{ date('Y-m-d') }}">
                            </div>
                            <div style="display:none;" id="contenedor_asumidas" class=" col-md-2 px-1">
                                <label for="valor" class="label_header">{{trans('contableM.RetencionesAsumidas')}}</label>
                                <input onchange="verificar_valor();" class="form-control " type="text" autocomplete="off" name="rt_asumidas" id="rt_asumidas" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            <!-- <input type="hidden" name="total_suma" id="total_suma"> -->
                        </div>
                    </div>
                    <!-- <div class="col-md-12">
                        <label class="label_header" for="detalle_deuda">{{trans('contableM.ANTICIPOSREALIZADOSDEPROVEEDORES')}}</label>
                        <div class="table-responsive col-md-12 px-1">
                            <table id="example5" class="px-1" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.fecha')}}</th>
                                        <th style="width: 5%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.numero')}}</th>
                                        <th style="width: 55%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                        <th style="width: 5%; text-align: center;">{{trans('contableM.div')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.saldo')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.saldobase')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="mostrar_anticipos">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <label class="label_header" for="detalle_deuda">{{trans('contableM.DetallededeudasdelProveedor')}}</label>
                        <input type="hidden" name="id_compra" id="id_compra">
                        <input type="hidden" name="contador" id="contador" value="0">
                        <div class="table-responsive col-md-12 px-1" style="min-height: 250px; max-height: 250px;">
                            <table id="example2" class="px-1" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.vence')}}</th>
                                        <th style="width: 5%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                        <th style="width: 5%; text-align: center;">{{trans('contableM.sucursal')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.numero')}}</th>
                                        <th style="width: 35%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                        <th style="width: 6%; text-align: center;">{{trans('contableM.retencion')}}</th>
                                        <th style="width: 5%; text-align: center;">{{trans('contableM.div')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.saldo')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.abono')}}</th>
                                        <th style="width: 10%; text-align: center;">{{trans('contableM.saldobase')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 6) as $i)
                                    <tr>
                                        <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="sucursal{{$cont}}" id="sucursal{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="retencion{{$cont}}" id="retencion{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5; width: 100% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                        <td> <input class="form-control" style="width: 100%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                    </tr>
                                    @php $cont = $cont +1; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <input type="hidden" name="saldo_hidden0" id="saldo_hidden0">
                                    <input type="hidden" name="verificar_cheque" id="verificar_cheque" value="0">
                                    <input type="hidden" name="total_egreso" id="total_egreso" value="0">
                                </div>
                                <div class="form-group col-md-3" style="text-align: right;">
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <input type="hidden" name="proveedor" id="proveedor">
                                        <input type="hidden" name="sobrante" id="sobrante">
                                        <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">&nbsp;</div>
                        <div class="col-md-2" style="text-align:right;">
                            <label class="label_header">{{trans('contableM.SaldoaFavor')}}</label>
                            <input class="form-control" type="hidden" name="total_suma" id="total_suma" readonly>
                            <input type="text" id="restante" value="0.00" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="hidden" name="saldo_final" id="saldo_final" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">&nbsp;</div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12 label_header" style="color: white;" for="nota">{{trans('contableM.nota')}}:</label>
                            <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#cheque_entregado').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
    });

    function bloquear_cheque() {
        var bco = $("#banco").val();
        $.ajax({
            type: 'get',
            url: "{{url('contable/acreedores/documentos/cuentas/egreso/varios/buscar/banco')}}/" + bco,
            datatype: 'json',
            success: function(data) {
                if (data.clase_banco != '1') {
                    document.getElementById("numero_cheque").readOnly = true;
                    $("#numero_cheque").val(' ');
                } else {
                    document.getElementById("numero_cheque").readOnly = false;
                }
            },
            error: function(data) {}
        })
    }
    $('.spropety').on('ifChecked', function(event) {
        $("#verificar_cheque").val(1);
    });
    $('.spropety').on('ifUnchecked', function(event) {
        $("#verificar_cheque").val(0);
    });
    $('.select2_proveedor').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        cache: true,
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

    function retencion_asumidas() {
        let ch_rt_asumidas = document.getElementById("ch_rt_asumidas").checked;
        let contenedor_asumidas = document.getElementById("contenedor_asumidas");
        let rt_asumidas = document.getElementById("rt_asumidas");
        if (ch_rt_asumidas) {
            contenedor_asumidas.style.display = "block";

        } else {
            contenedor_asumidas.style.display = "none";
            rt_asumidas.value = "0.00";
        }
    }


    function aprobacion() {
        var checkbox = document.getElementById("aprobacion_directorio_checkbox");
        var hiddenField = document.getElementById("aprobacion_directorio");

        // Si el checkbox está marcado, cambia el valor a 1, de lo contrario 0.
        hiddenField.value = checkbox.checked ? 1 : 0;
    }



    function verificar_valor() {
        let rt_asumidas = document.getElementById("rt_asumidas");
        let valor_cheque = document.getElementById("valor_cheque");

        if (valor_cheque.value == "") {
            swal("Incorrecto!", "<?= trans('contableM.ingresecampovalorcheque'); ?>", "error");
            rt_asumidas.value = "0.00";
        } else if (parseFloat(rt_asumidas.value) > parseFloat(valor_cheque.value)) {
            swal("Incorrecto!", "<?= trans('contableM.elvalretennodebesermayorcheque'); ?>", "error");
            rt_asumidas.value = "0.00";
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
            error: function(data) {}
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
        if ($(this).prop("checked")) {
            $("#esfac_contable").val(1);
        } else {
            $("#esfac_contable").val(0);
        }
    });


    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });

    });

    function funciones_pago() {
        var formas_pago = parseInt($("#formas_pago").val());
        switch (formas_pago) {
            case 1:
                [].forEach.call(document.querySelectorAll('.'), function(el) {
                    el.style.display = 'none';
                });
                break;
            case 2:
                [].forEach.call(document.querySelectorAll('.'), function(el) {
                    el.style.display = 'block';
                });
                break;
        }
    }
    //cambia esta funcion que en vez que reciba el nombre la funcion que traiga con el id
    function cambiar_nombre_proveedor(eq) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedornombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': eq.value,
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#id_proveedor").val(data.value);
                    $("#direccion").val(data.direccion);
                    buscar_proveedor()
                } else {
                    $('#id_proveedor').prop('readonly', false);
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }
    $(".nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_buscar_nombreproveedor')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    //console.log(data)
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

    function suma_totales() {
        var total = 0;
        $('.abonos').each(function(i, obj) {
            console.log($(this).val());
            total += parseFloat($(this).val());

        });
        $("#total_suma").val(total.toFixed(2, 2));
        var cheque = $("#valor_cheque").val();
        var restante = cheque - total;
        $("#restante").val(restante.toFixed(2, 2));
    }

    function guardar() {
        var formulario = document.forms["form_guardado"];
        var valor_cheque = document.getElementById("valor_cheque");
        var numero_cheque = formulario.numero_cheque.value;
        var acreedores = formulario.id_proveedor.value;
        var fecha = formulario.fecha_hoy.value;
        var formas_pago = $("#formas_pago").val();
        var sucursal = $("#sucursal").val();
        var asiento = $("#asiento").val();
        var banco = formulario.banco.value;
        var superavit = parseInt($("#verificar_superavit").val());
        var msj = "";
        // let banco = document.getElementById("banco").value;
        if (banco == 0 || banco == "") {
            alertas("error", "Error...", "Seleccione el banco")
            return;
        }
        if (formas_pago == 2 && numero_cheque == "") {
            msj += "{{trans('contableM.porfallenenumcheque')}}<br/>";
        }
        if (valor_cheque == "") {
            msj += "{{trans('contableM.porfallenevalor')}}<br/>";
        }
        if (acreedores == "") {
            msj += "{{trans('contableM.porfaacreedor')}}<br/>";
        }
        if (formas_pago == "") {
            msj += "{{trans('contableM.porfapago')}} <br/>";
        }
        if (banco == "") {
            msj += "{{trans('contableM.porfallenebanco')}} <br/>";
        }
        if (fecha == "") {
            msj += "{{trans('contableM.porfalenefecha')}} <br/>";
        }
        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = parseFloat($("#valor_cheque").val());
        var total_suma = parseFloat($("#total_suma").val());
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        if (msj == "") {
            // if(formas_pago == 2){
            //     let no_cheque = document.getElementById("numero_cheque").value;
            //     if(no_cheque.trim() == ""){
            //         alertas("error", "Error...", "Selecciono forma de pago cheque, debe colorcar el campo de <b>Nro_Cheque</b>");
            //         return ;
            //     }
            // }
            //$('#botong').attr('disabled', 'disabled');
            //console.log(superavit);
            if (superavit == 1) {
                if (total_suma < final_valor_cheque) {
                    var resta_superavit = parseFloat(final_valor_cheque - total_suma);
                    var resta_fixed = resta_superavit.toFixed(2, 2);
                    $("#total_favor").val(resta_superavit);

                    $("#total_favor").val(resta_superavit);
                    $("#superavit").val(0);
                    $("#comprobarx").val(1);
                    //console.log("entro nuevo");
                    if ($("#form_guardado").valid()) {
                        $.ajax({
                            type: 'post',
                            url: "{{route('acreedores_cstore_solicitud')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),

                            success: function(data) {

                                console.log(data.id);
                                if ((data) != 'false') {
                                    $("#id_factura").val(data.id_comprobante);
                                    $("#asiento").val();
                                    swal("Correcto!", "Los comprobantes se generaron con exito", "success");
                                    document.getElementById("aplicar_deuda").disabled = true;
                                    $('#form_guardado input').attr('readonly', 'readonly');
                                    $('#botong').attr('disabled', 'disabled');
                                    url = "{{ url('contable/compra/comprobante/egreso/solicitud/pdf/')}}/" + data.id_comprobante;
                                    window.open(url, '_blank');
                                } else {
                                    if (data.status == "success") {
                                        console.log(data)
                                        $("#id_factura").val(data.id_comprobante);
                                        swal("Correcto!", "Los comprobantes se generaron con exito", "success");
                                        document.getElementById("aplicar_deuda").disabled = true;
                                        $('#form_guardado input').attr('readonly', 'readonly');
                                        $('#botong').attr('disabled', 'disabled');
                                        url = "{{ url('contable/compra/comprobante/egreso/solicitud/pdf/')}}/" + data.id_comprobante;
                                        window.open(url, '_blank');
                                    } else {
                                        alertas(data.status, "Error...", data.msj)
                                    }
                                }
                            }
                        })
                    }
                } else {                    
                    $('#botong').attr('disabled', 'disabled');
                    if ($("#form_guardado").valid()) {
                        $.ajax({
                            type: 'post',
                            url: "{{route('acreedores_cstore_solicitud')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),
                            success: function(data) {
                                console.log(data)
                                if ((data) != 'false') {
                                    $("#id_factura").val(data);
                                    if (data.status == "success") {
                                        $("#id_factura").val(data.id_comprobante);
                                        swal(`{{trans('contableM.correcto')}}!`, "trans('contableM.loscomprosegenerexito')", "success");
                                        document.getElementById("aplicar_deuda").disabled = true;
                                        $('#form_guardado input').attr('readonly', 'readonly');
                                        $('#botong').attr('disabled', 'disabled');
                                        url = "{{ url('contable/compra/comprobante/egreso/solicitud/pdf/')}}/" + data.id_comprobante;
                                        window.open(url, '_blank');
                                    } else {
                                        url = "{{ url('contable/compra/comprobante/egreso/solicitud/pdf/')}}/" + data.id_comprobante;
                                        window.open(url, '_blank');
                                    } // else {
                                    //     alertas(data.status, `${data.status.toUpperCase()}...`, data.msj);
                                    // }
                                }
                            },
                            error: function(data) {
                                console.log(data);
                            }
                        })
                    }
                }
            } else {                
                $("#superavit").val(1);
                $("#total_favor").val(final_valor_cheque);
                if ($("#form_guardado").valid()) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('acreedores_cstore_solicitud')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#form_guardado').serialize(),
                        success: function(data) {
                            if ((data) != 'false') {

                                $("#id_factura").val(data);
                                if (data.status == "success") {

                                    $("#id_factura").val(data.id_comprobante);
                                    $('#form_guardado input').attr('readonly', 'readonly');
                                    $('#botong').attr('disabled', 'disabled');
                                    url = "{{ url('contable/compra/comprobante/egreso/solicitud/pdf/')}}/" + data.id_comprobante;
                                    window.open(url, '_blank');

                                    //document.getElementById("botong").disabled = true;
                                    swal(`{{trans('contableM.correcto')}}!`, "trans('contableM.anticipoproveegeneroexito')", "success");
                                    document.getElementById("aplicar_deuda").disabled = true;
                                }
                            }
                        },
                        error: function(data) {
                            console.log(data.msj);
                        }
                    })
                }
            }
        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }
    }
    function boton_deuda() {
        var valor = parseFloat($("#valor_cheque").val());
        var valor2 = parseFloat($("#valor_cheque").val());
        var contador = parseInt($("#contador").val());
        var cero = 0;

        // Reiniciar los campos antes de recalcular
        for (var i = 0; i <= contador; i++) {
            $("#abono" + i).val(cero.toFixed(2)); // Reinicia los campos de abono
            $("#nuevo_saldo" + i).val(cero.toFixed(2)); // Reinicia los campos de nuevo saldo
        }

        var saldo = 0;
        var abono = 0;
        var valor_saldo = 0;
        var total = 0;

        for (var i = 0; i <= contador; i++) {
            valor_saldo = parseFloat($("#saldo" + i).val());
            saldo += valor_saldo;

            if (valor >= valor_saldo) {
                $("#abono" + i).val(valor_saldo.toFixed(2));
                $("#nuevo_saldo" + i).val(cero.toFixed(2));
                valor -= valor_saldo;
            } else {
                if (valor > 0) {
                    $("#abono" + i).val(valor.toFixed(2));
                    var nuevo_saldo = valor_saldo - valor;
                    $("#nuevo_saldo" + i).val(nuevo_saldo.toFixed(2));
                    valor = 0; // Se consume todo el valor disponible
                } else {
                    $("#abono" + i).val(cero.toFixed(2));
                    $("#nuevo_saldo" + i).val(valor_saldo.toFixed(2));
                }
            }
        }

        if (!isNaN(valor2) && !isNaN(valor_saldo)) {
            $("#verificar_superavit").val(1);
            suma_totales(); // Asumiendo que esta función suma los totales de los abonos o saldos.
        } else {
            swal("Error!", "trans('contableM.ingrevalorchequeprim')", "error");
        }
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
        location.href = '<?= route('acreedores_ccreate_solicitud'); ?>';
    }
    //aqui ando
    function buscar_proveedor() {
        var proveedor = $("#id_proveedor").val();
        var tipo = parseInt($("#esfac_contable").val());
        var provedores = $("#nombre_proveedor").val();
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
                'tipo': tipo,
            },
            success: function(data) {
                if (data.value != "no resultados") {
                    console.log(data);
                    $("#crear").empty();
                    var fila = 0;
                    for (i = 0; i < data[5].length; i++) {
                        if (data[5][i].tipo == 1) {
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FA', data[5][i].sucursal, data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[8][i], data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else if (data[5][i].tipo == 2) {
                            console.log("aqui 2")
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FACT', data[5][i].sucursal, data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[8][i], data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else {
                            var row = addNewRowf(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'SALDO INICIAL', data[5][i].sucursal, data[5][i].numero, data[5][i].observacion, "Hola", data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        }
                    }
                    $("#contador").val(fila);
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    const anterioresAnticipos = () => {
        let id_proveedor = document.getElementById("nombre_proveedor").value;
        $.ajax({
            type: `get`,
            url: `{{route('compra.egreso.buscarAntAnticipos')}}`,
            data: {
                id_proveedor: id_proveedor,
            },
            datatype: 'json',
            success: function(data) {
                // console.log(data);
                $("#mostrar_anticipos").empty();
                $("#mostrar_anticipos").append(data.html)
            },
            error: function(data) {}
        })
        //   $.ajax({
        //     type: 'post',
        //     headers: {
        //         'X-CSRF-TOKEN': $('input[name=_token]').val()
        //     },
        //     url: "{{route('compra.egreso.buscarAntAnticipos')}}",
        //     data: {
        //         'id_proveedor': id_proveedor
        //     },
        //     datatype: 'json',
        //     success: function(data) {
        //         //alert(data)
        //     },
        //     error: function(data) {
        //         //alert(data)
        //     }
        // });
    }

    function validar_td(id) {
        if ((id) != null) {
            var valor = parseFloat($("#valor_cheque").val());
            var abono = parseFloat($("#abono" + id).val());
            var saldo = parseFloat($("#saldo" + id).val());

            var cantidad = parseFloat($("#total_suma").val());
            if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                if (cantidad <= valor) {
                    var uno = 1;
                    $("#verificar_superavit").val(uno);
                    if (abono > saldo) {
                        abono = saldo;
                    }
                    $("#abono" + id).val(abono.toFixed(2, 2));
                } else {
                    valor = 0;
                    $("#abono" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "<?= trans('ContableM.errornosuperarvalorcheque'); ?>", "error")

                }
            } else {
                abono = 0;
                valor = 0;
                $("#abono" + id).val(valor.toFixed(2, 2));
            }
        } else {
            alert("error");
        }
        suma_totales()
    }

    function suma_totaless() {
        /* 
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
        $("#total_suma").val(total.toFixed(2, 2)); */
        //alert(total_fin);
    }

    function addNewRow(pos, fecha, valor, factura, sucursal, fact_numero, observacion, retencion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text'  id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "<input class='form-control' type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id + "'>" +
            "<td> <input class='form-control' type='text'  id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text'  id='sucursal" + pos + "' name='sucursal" + pos + "' value='" + sucursal + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='Fact #:" + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text'  id='retencion" + pos + "' readonly='' value='" + retencion + "'> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control abonos' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")' value='0.00'></td>" +
            "<td> <input class='form-control' type='text' style=' text-align: left;' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
            "</tr>";
        return markup;
    }

    function addNewRowf(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text'  id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            " <input class='form-control' type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text'  id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control'  type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control abonos' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")' value='0.00'></td>" +
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
        console.log(ty);
        if ($mes != month) {
            swal("Recuerde!", '<?= trans('contableM.lafechaingreestafueradelperio'); ?>', "error")
            //document.getElementById("fecha_hoy").value = ty;
            //location.reload();
        }
    });

    function bigImg(x) {
        x.style.height = "100%";
        x.style.background = "#1E88E5";
        x.style.color = "#fff";
        x.style.zIndex = "9999";
        x.style.position = "relative";
        x.style.width = "250%";
    }

    function normalImg(x) {
        x.style.height = "100%";
        x.style.width = "100%";
        x.style.zIndex = "";
        x.style.color = "#000";
        x.style.background = "#DDEBEE";
    }
</script>
@endsection