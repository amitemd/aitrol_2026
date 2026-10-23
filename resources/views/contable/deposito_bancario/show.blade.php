@extends('contable.transferencia_bancaria.base')
@section('action-content')
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
        å
        return patron.test(tecla_final);
    }

    function goBack() {
        window.history.back();
        //window.location.reload(history.back());
    }

    function goNew() {
        $(".btn_add").attr("disabled", false);

        $("#asiento").val("");
        $("#id").val("");
        $("#numero").val("");
        $(".print").css('visibility', 'hidden');
    }
</script>
<style>
    .text_der {
        text-align: right;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>


<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Banco</a></li>
            <li class="breadcrumb-item"><a href="../notacredito">Déposito Bancario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-5">
                <h3 class="box-title">Crear Déposito Bancario</h3>
            </div>

            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('depositobancario.imprimir', ['id' => $registro->id]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i>
                    <!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$registro->id_asiento])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
            </a>
            <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$registro->id_asiento])}}" target="_blank">
                <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
            </a>

            <div class="col-md-1 text-right">
                <button onclick="location.href='{{ route('depositobancario.create') }}'" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="location.href='{{ route('depositobancario.index') }}'" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>

        <form id="form" class="form-vertical" role="form">
            <div class="box-body dobra">
                {{ csrf_field() }}
                <input type="hidden" id="minDate" name="minDate"
                value="@if (!is_null($mindate)) {{ $mindate }} @endif">
                <!--header row-->
                <div class="col-md-1">
                </div>
                <div class="header row col-md-11">
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">Estado</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="estado" type="text" class="form-control" name="estado" value=" {{ $registro->estado ? "ACTIVA" : "ANULADA" }}" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">Id</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="{{ str_pad( $registro->id, 10, "0", STR_PAD_LEFT) }}" readonly autofocus>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">N&uacute;mero</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="{{ str_pad( $registro->numero, 10, "0", STR_PAD_LEFT) }}" readonly autofocus>
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Asiento</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" value="{{ $registro->id_asiento }}" readonly autofocus>
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">Tipo</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-DP" readonly autofocus>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">Fecha</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_asiento" type="text" class="form-control" name="fecha_asiento" value="{{ date('d/m/Y', strtotime($registro->fecha_asiento)) }}" required>
                            @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Proyecto</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="" readonly autofocus>
                            @if ($errors->has('proyecto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('proyecto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0">
                            <label for="concepto" class="label_header">Concepto</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="concepto" type="text" class="form-control" name="concepto" value="{{$registro->concepto}}" required a utofocus>
                            @if ($errors->has('concepto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('concepto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-4 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_origen" class="label_header">Cuenta Origen</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" disabled="disabled" required>
                                <option value="">Seleccione...</option>
                                @foreach($cuentas as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-8 px-1">
                        <div class="col-md-12 px-0">
                            <label for="nota" class="label_header">Nota</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="nota" type="text" class="form-control" name="nota" value="{{$registro->nota}}" autofocus>
                            @if ($errors->has('nota'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nota') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino" class="label_header">Cuenta Destino</label>
                        </div>
                        {{--<span>{{$registro->id_cuenta_destino}}</span>--}}
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_destino" id="id_cuenta_destino" required>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option {{$value->cuenta_mayor == $registro->id_cuenta_destino ? 'selected' : ''}} value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor1" class="label_header">Valor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor1" type="number" class="form-control" onchange="cambiar_float('valor1');" name="valor1" value="@if(is_null($registro->valor_destino)){{$registro->total_deposito}}@else{{$registro->valor_destino}}@endif" step="0.01" autofocus readonly>
                            @if ($errors->has('valor1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('valor1') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino2" class="label_header">Cuenta Destino 2</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_destino2" id="id_cuenta_destino2" @if(is_null($registro->id_cuenta_destino2)) disabled @endif >
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option {{$value->cuenta_mayor == $registro->id_cuenta_destino2 ? 'selected' : ''}} value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor2" class="label_header">Valor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor2" onchange="cambiar_float('valor2');" type="number" class="form-control" name="valor2" value="{{$registro->valor_destino2}}" readonly step="0.01" autofocus @if(is_null($registro->id_cuenta_destino2)) readonly @endif>
                            @if ($errors->has('valor2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('valor2') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!--<div class="col-md-12" style="text-align: center;">
                    <button formaction="{{route('depositobancario.update',['id'=>$registro->id])}}" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> Actualizar </button>
                </div>-->
            </div>


            <div class="box-body dobra">
                <div class="header row col-md-11">
                    @include('contable.deposito_bancario.detalles')
                </div>
            </div>

            <div class="box-body dobra">

                <div class="header row col-md-11">
                    {{-- <div class="container">
                        <div class="form-group col-xs-2 px-1">
                            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true, 'tbl_detalles')"
                                class="btn btn-default">
                                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                            </button>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false, 'tbl_detalles')"
                                class="btn btn-default">
                                <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                            </button>
                        </div>
                    </div> --}}
                    <div class="form-group col-xs-2 px-1">
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total_efectivo" class="label_header">Total Efectivo</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total_efectivo" type="text" class="form-control" style="text-align: right;" name="total_efectivo" value="{{ number_format($registro->total_efectivo, 2,'.','') }}" readonly="true">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total_pap_deposito" class="label_header">Total Pap. Déposito</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total_pap_deposito" type="text" class="form-control" style="text-align: right;" name="total_pap_deposito" value="{{ number_format($registro->total_pap_deposito, 2,'.','') }}" readonly="true">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total_cheques" class="label_header">Total Cheques</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total_cheques" type="text" class="form-control" style="text-align: right;" name="total_cheques" value="{{ number_format($registro->total_cheque, 2,'.','') }}" readonly="true">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total_tarjetas" class="label_header">Total Tarjetas</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total_tarjetas" type="text" class="form-control" style="text-align: right;" name="total_tarjetas" value="{{ number_format($registro->total_tarjeta, 2,'.','') }}" readonly="true">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="total_deposito" class="label_header">TOTAL DEPOSITO</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="total_deposito" type="text" class="form-control" style="text-align: right;" name="total_deposito" value="{{ number_format($registro->total_deposito, 2,'.','') }}" readonly="true">
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>
                </div>
            </div> --}}

        </form>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    // $(".print").css('visibility', 'hidden');
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });

        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
    });

    $(function() {
        $('#fecha_asiento').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: '{{date("Y-m-d")}}',
            minDate: $('#minDate').val(),
        });
        $('#fecha_cheque').datetimepicker({
            format: 'DD/MM/YYYY',
        });

        $('.number').keypress(function(event) {

            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault(); //stop character from entering input
            }

        });

    });

    $("#id_cuenta_origen").val('{{ $registro->id_cuenta_origen }}');
    $("#id_cuenta_destino").val('{{ $registro->id_cuenta_destino }}');

    function buscar_forma_pago() {
        var id_forma_pago = $("#id_forma_pago").val();

        if (id_forma_pago != '') {
            $.ajax({
                type: 'get',
                url: "{{route('depositobancario.buscarformapago')}}",
                datatype: 'html',
                data: {
                    'id_forma_pago': id_forma_pago,
                    'fecha_desde': $("#fecha_desde").val(),
                    'fecha_hasta': $("#fecha_hasta").val(),


                },
                success: function(datahtml) {
                    $("#tbl_detalles").empty();
                    $("#tbl_detalles").html(datahtml);
                    // $("#resultados").show();
                    // $("#contenedor").hide();

                },
                error: function(data) {
                    console.log(data);

                }
            });
        }
        // else{
        // alert("campos vacios")
        // $("#resultados").hide();
        // $("#contenedor").show();
        // }

    }

    function seleccionar_todo(checked, tablename) {
        var miTabla = document.getElementById(tablename);
        for (i = 0; i < miTabla.rows.length; i++) {
            miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
        }
        calcular_todo();
    }

    function calcular_todo() {
        var totefectivo = 0;
        var totetransfe = 0;
        var totcheques = 0;
        var totdeposito = 0;
        var tottarjetas = 0;
        var total = 0;
        var x = 0;
        var miTabla = document.getElementById('tbl_detalles');
        $('#tbl_detalles tr').each(function() {
            var tipo = $(this).find("td").eq(2).html();
            var total = $(this).find("td").eq(9).html();
            // var nombre = $(this).find("td").eq(1).html();
            // var apellidos = $(this).find("td").eq(3).html();
            if (miTabla.rows[x].getElementsByTagName("input")[0].checked && tipo == 'EFECTIVO') {
                totefectivo = parseFloat(totefectivo) + parseFloat(total);
            }
            if (miTabla.rows[x].getElementsByTagName("input")[0].checked && tipo == 'CHEQUE') {
                totcheques = parseFloat(totcheques) + parseFloat(total);
            }
            if (miTabla.rows[x].getElementsByTagName("input")[0].checked && tipo == 'TARJETA CREDITO') {
                tottarjetas = parseFloat(tottarjetas) + parseFloat(total);
            }
            if (miTabla.rows[x].getElementsByTagName("input")[0].checked && tipo == 'TARJETA DEBITO') {
                tottarjetas = parseFloat(tottarjetas) + parseFloat(total);
            }
            if (miTabla.rows[x].getElementsByTagName("input")[0].checked && tipo == 'DEPOSITO') {
                totdeposito = parseFloat(totdeposito) + parseFloat(total);
            }
            x++;
        });
        $("#total_efectivo").val(devuelvefloat(totefectivo, 2));
        $("#total_pap_deposito").val(devuelvefloat(totetransfe, 2));
        $("#total_cheques").val(devuelvefloat(totetransfe, 2));
        $("#total_tarjetas").val(devuelvefloat(totetransfe, 2));
        total = parseFloat(totefectivo) + parseFloat(totetransfe) + parseFloat(totdeposito);
        $("#total_deposito").val(devuelvefloat(total, 2));

    }



    function nuevo() {
        // var nuevafila = $("#mifila").html();
        // var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        $("#form")[0].reset();

        $('.select2_cuentas').select2({
            tags: false
        });

    }

    function calcular() {
        var valor_origen = $("#valor_origen").val();
        var valor_destino = $("#valor_destino").val();
        var comision_tarjeta = $("#comision_tarjeta").val();
        var iva_retenido = $("#iva_retenido").val();
        var retencion_fuente = $("#retencion_fuente").val();
        var valores_adicionales = $("#valores_adicionales").val();
        var valores_destino = 0;

        var total = 0;
        var subtotal = 0;

        var base_ret_fuente = $("#base_ret_fuente").val();
        var id_base_ret_fuente = $("#id_base_ret_fuente").val();
        var retencion_fuente = 0;
        if (base_ret_fuente != null && id_base_ret_fuente != null) {
            retencion_fuente = (parseFloat(base_ret_fuente) * parseFloat(id_base_ret_fuente)) / 100;
            retencion_fuente = devuelvefloat(retencion_fuente, 2); //alert(retencion_fuente);
            $("#retencion_fuente").val(retencion_fuente);
        }

        subtotal = devuelvefloat(parseFloat(comision_tarjeta) + parseFloat(iva_retenido) + parseFloat(
            retencion_fuente) + parseFloat(valores_adicionales), 2);
        total = devuelvefloat(parseFloat(valor_origen) - parseFloat(subtotal), 2);
        if (total == 'NaN') {
            total = 0;
        }
        // alert((retencion_fuente));
        $("#valor_destino").val(devuelvefloat(total, 2));
        valores_destino = parseFloat(subtotal) + parseFloat(total);
        valores_destino = devuelvefloat(valores_destino, 2);
        $("#total").val(devuelvefloat(valores_destino, 2));
    }

    function devuelvefloat(cant, decimales) {
        var tmp = null;
        $.ajax({
            url: "{{route('transferenciabancaria.devuelvefloat')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            async: false,
            data: {
                cantidad: cant,
                decimales: decimales
            },
            success: function(data) {
                tmp = data.valor;
            },
            error: function(data) {
                console.error(data.responseText);
            }
        });
        return tmp;
    }


    $(".btn_add").click(function() {
        if ($("#form").valid()) {
            // $(".print").css('visibility', 'visible');
            $(".btn_add").attr("disabled", true);
            $.ajax({
                url: "{{route('depositobancario.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    console.log(data);
                    $("#asiento").val(data.id_asiento);
                    $("#id").val(data.id);
                    $("#numero").val(data.numero);

                    Swal.fire(
                        'Mensaje!',
                        'Datos guardados con éxito!',
                        'success'
                    )

                },
                error: function(data) {
                    console.error(data.responseText);
                }
            });
        }
    });
</script>

</section>
@endsection