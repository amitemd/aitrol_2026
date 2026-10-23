@extends('contable.deposito_bancario.base')
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

    .colorselect {
        background-color: #6099ca !important;
        color: white;
    }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
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
            <div class="col-md-6">
                <h3 class="box-title">Crear Déposito Bancario</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('notadebito.imprimir', ['id' => 3]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i>
                    <!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" class="btn btn-default btn-gray btn_add">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="location.href='<?= route('depositobancario.create'); ?>'" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="location.href='<?= route('depositobancario.index'); ?>'" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <form id="form" class="form-vertical" role="form">
            <div class="box-body dobra">
                {{ csrf_field() }}
                <!--header row-->
                <div class="col-md-1">
                </div>
                <div class="header row col-md-11">
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">Estado</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="estado" type="text" class="form-control" name="estado" value="Activa" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">Id</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="" readonly autofocus>
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
                            <input type="text" class="form-control" name="numero" id="numero" value="" readonly autofocus>
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
                            <input type="text" class="form-control" name="asiento" id="asiento" value="" readonly autofocus>
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

                            <input id="fecha_asiento" type="date" class="form-control" name="fecha_asiento" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{ date('Y-m-d') }}" required>
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
                            <input id="concepto" type="text" class="form-control" name="concepto" value="{{ old('concepto') }}" required autofocus>
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
                            <label for="referencia_deposito" class="label_header">Referencia deposito</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="referencia_deposito" type="text" class="form-control" name="referencia_deposito" required>
                        </div>
                    </div>
                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino" class="label_header">Cuenta Destino</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_destino" id="id_cuenta_destino" required>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor1" class="label_header">Valor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor1" type="number" class="form-control" onchange="cambiar_float('valor1');" name="valor1" value="{{ old('valor1') }}" step="0.01" autofocus required>
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
                            <select class="form-control" name="id_cuenta_destino2" id="id_cuenta_destino2">
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-3 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor2" class="label_header">Valor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor2" onchange="cambiar_float('valor2');" type="number" class="form-control" name="valor2" value="{{ old('valor2') }}" step="0.01" autofocus>
                            @if ($errors->has('valor2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('valor2') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row col-md-11">
                    <div class="container">
                        <div class="form-group col-xs-2 px-1">
                            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true, 'tbl_detalles')" class="btn btn-default">
                                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                            </button>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <button type="button" id="buscarAsiento2" onclick="seleccionar_todo(false, 'tbl_detalles')" class="btn btn-default">
                                <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                            </button>
                        </div>
                    </div>
                    <!-- <div class="form-group col-xs-2 px-1">
                    </div> -->
                    <div class="container">
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_efectivo" class="label_header">Total Efectivo</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_efectivo" type="text" class="form-control" style="text-align: right;" name="total_efectivo" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_pap_deposito" class="label_header">Total Pap. Déposito</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_pap_deposito" type="text" class="form-control" style="text-align: right;" name="total_pap_deposito" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_cheques" class="label_header">Total Cheques</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_cheques" type="text" class="form-control" style="text-align: right;" name="total_cheques" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_tarjetas" class="label_header">Total Tarjetas</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_tarjetas" type="text" class="form-control" style="text-align: right;" name="total_tarjetas" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_datalink" class="label_header">Total Datalink</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_datalink" type="text" class="form-control" style="text-align: right;" name="total_datalink" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_crucecuentas" class="label_header">Total Cruce Cuentas</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_crucecuentas" type="text" class="form-control" style="text-align: right;" name="total_crucecuentas" value="" readonly="true">
                            </div>
                        </div>
                        <div class="form-group col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="total_deposito" class="label_header">TOTAL DEPOSITO</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="total_deposito" type="text" class="form-control" style="text-align: right;" name="total_deposito" value="" readonly="true">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row col-md-11">
                    @include('contable.deposito_bancario.detalles')
                </div>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        var fila = $("#mifila").html();
        $(".print").css('visibility', 'hidden');
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

        function validarCierre() {
            <?php

            use Sis_medico\v3\contable\PlanCuentas;

            if (isset($min_date) && $min_date != null) {
            ?>
                let fechaAc = new Date();
                var fecha1 = '<?= $min_date; ?>';
                var fecha2 = document.getElementById("fecha_asiento").value;
                const fechaInicial = new Date(fecha1);
                const fechaFinal = new Date(fecha2);
                if (fechaFinal <= fechaInicial) {
                    alertas("error", "Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.')
                    const fechaFormateada = fechaAc.toISOString().split('T')[0];
                    document.getElementById("fecha_asiento").value = fechaFormateada;
                }
            <?php
            }
            ?>
        }

        $(function() {
            $('#fecha_cheque').datetimepicker({
                format: 'YYYY/MM/DD',
            });
            $('.number').keypress(function(event) {

                if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault(); //stop character from entering input
                }
            });
        });
        <?php
        $cuentaCaja = PlanCuentas::where('nombre','caja general')->where('id_empresa',session('id_empresa'))->first();
        ?>
        $("#id_cuenta_origen").val('<?=$cuentaCaja->plan;?>');
        //$("#id_cuenta_origen").val('1.01.01.1.01');
        function buscar_forma_pago() {
            var id_forma_pago = $("#id_forma_pago").val();
            // console.log(id_forma_pago);
            var fechaini = $("#fecha_desde").val();
            var fechafin = $("#fecha_hasta").val();
            //console.log(id_forma_pago);
            if (id_forma_pago != '') {
                $.ajax({
                    type: 'get',
                    url: "{{route('depositobancario.buscarformapago')}}",
                    datatype: 'html',
                    data: {
                        'id_forma_pago': id_forma_pago,
                        'fecha_desde': fechaini,
                        'fecha_hasta': fechafin,
                    },
                    success: function(datahtml) {
                        //  console.log(datahtml);
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
        const verificarChecks = (id_check, id_ing, id) => {
            console.log(id)
            let hiden = document.getElementById(id_check);
            console.log(hiden)
            if (id.checked) {
                hiden.value = id_ing;
            } else {
                hiden.value = ''
            }
        }

        function cambiar_float(div) {
            valor = parseFloat($('#' + div).val());
            $('#' + div).val(devuelvefloat(valor, 2));
        }

        function seleccionar_todo(checked, tablename) {
            $('#' + tablename).find('.veractivo').val(1);
            $('#' + tablename).find('tr').addClass('colorselect');
            $('#' + tablename).find('.form-check-input').prop("checked", true);
            if (checked == false) {
                $('#' + tablename).find('.form-check-input').prop("checked", false);
                $('#' + tablename).find('.veractivo').val(0);
                $('#' + tablename).find('tr').removeClass('colorselect');
            }
            if (checked) {
                calcular_todo(1);
            } else {
                calcular_todo(0);
            }
        }

        $('body').on('click', '.relactivo', function() {
            if ($(this).prop("checked") == true) {
                $(this).parent().find('.veractivo').val(1);
                console.log($(this).parent().parent().html())
                $(this).parent().parent().addClass('colorselect');

                calcular_todo();
            } else if ($(this).prop("checked") == false) {
                $(this).parent().find('.veractivo').val(0);
                $(this).parent().parent().removeClass('colorselect');

                calcular_todo();
            }
        });

        function calcular_todo(r) {
            var totefectivo = 0;
            var totetransfe = 0;
            var totcheques = 0;
            var totdeposito = 0;
            var tottarjetas = 0;
            var totdatalink = 0;
            var totcrucecuentas = 0;
            var total = 0;
            var x = 0;
            var miTabla = document.getElementById('tbl_detalles');
            $('.veractivo').each(function(i, obj) {

                var activo = parseInt($(this).val());
                //console.log("ver activo "+activo);
                if (r == 1) {
                    var tipo = $(this).parent().find('.tipox').val();
                    var total = $(this).parent().find('.totax').val();
                    //console.log("ver total"+total);
                    if (tipo == 'TARJETA CREDITO') {
                        tottarjetas += parseFloat(total);
                    }
                    if (tipo == 'TARJETA DEBITO') {
                        tottarjetas += parseFloat(total);
                    }
                    if (tipo == 'CHEQUE') {
                        totcheques += parseFloat(total);
                    }
                    if (tipo == 'EFECTIVO') {
                        totefectivo += parseFloat(total);
                        //console.log("aqui efectivo");
                    }
                    if (tipo == 'DEPOSITO' || tipo == 'PAYPAL' || tipo == 'PAGO POR APP') {
                        totdeposito += parseFloat(total);
                    }
                    if (tipo == 'TRANSFERENCIA BANCARIA') {
                        totetransfe += parseFloat(total);
                    }
                    if (tipo == 'DATALINK') {
                        totdatalink += parseFloat(total);
                    }
                    if (tipo == 'CRUCE CUENTAS') {
                        totcrucecuentas += parseFloat(total);
                    }

                } else {
                    if (activo == 1) {
                        var tipo = $(this).parent().find('.tipox').val();
                        var total = $(this).parent().find('.totax').val();
                        //console.log("ver total"+total);
                        if (tipo == 'TARJETA CREDITO') {
                            tottarjetas += parseFloat(total);
                        }
                        if (tipo == 'TARJETA DEBITO') {
                            tottarjetas += parseFloat(total);
                        }
                        if (tipo == 'CHEQUE') {
                            totcheques += parseFloat(total);
                        }
                        if (tipo == 'EFECTIVO') {
                            totefectivo += parseFloat(total);
                        }
                        if (tipo == 'DEPOSITO' || tipo == 'PAYPAL' || tipo == 'PAGO POR APP') {
                            totdeposito += parseFloat(total);
                        }
                        if (tipo == 'TRANSFERENCIA BANCARIA') {
                            totetransfe += parseFloat(total);
                        }
                        if (tipo == 'DATALINK') {
                            totdatalink += parseFloat(total);
                        }
                        if (tipo == 'CRUCE CUENTAS') {
                            totcrucecuentas += parseFloat(total);
                        }
                    }
                }

            });
            //console.log(totefectivo);
            $("#total_efectivo").val(devuelvefloat(totefectivo, 2));
            $("#total_pap_deposito").val(devuelvefloat(totetransfe, 2));
            $("#total_cheques").val(devuelvefloat(totcheques, 2));
            $("#total_tarjetas").val(devuelvefloat(tottarjetas, 2));
            $("#total_datalink").val(devuelvefloat(totdatalink, 2));
            $("#total_crucecuentas").val(devuelvefloat(totcrucecuentas, 2));
            total = parseFloat(totefectivo) + parseFloat(totetransfe) + parseFloat(totdeposito) + parseFloat(totcheques) + parseFloat(tottarjetas) + parseFloat(totdatalink) + parseFloat(totcrucecuentas);
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

        function devuelvefloat(value, decimales) {
            value = +value;
            if (isNaN(value)) return NaN; // Shift
            value = value.toString().split('e');
            value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back
            value = value.toString().split('e');
            return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
        }
        $(".btn_add").click(function() {
            var hoy = new Date();
            var fechaFormulario = new Date($('#fecha_asiento').val());
            hoy.setHours(0, 0, 0, 0);
            if (hoy < fechaFormulario) {
                Swal.fire('Error', 'La fecha debe ser menor o igual a la de hoy <?= date('Y-m-d') ?>', 'error');
                return;
            }
            document.querySelector(".btn_add").style.display = 'none';
            if ($("#form").valid()) {
                valor1 = parseFloat($('#valor1').val());
                valor2 = parseFloat($('#valor2').val());
                if (isNaN(valor2)) {
                    valor2 = 0;
                }
                texto_2 = $('#id_cuenta_destino2').val();
                tgeneral = (Math.round((valor1 + valor2) * 100) / 100);
                valida = 0;
                total_deposito = parseFloat($('#total_deposito').val());
                console.log(`deposito ${total_deposito} tgeneral ${tgeneral}`)
                if (total_deposito != tgeneral) {
                    console.log('valor1');
                    console.log(valor1);
                    console.log('valor2');
                    console.log(valor2);
                    console.log('tgeneral');
                    console.log(tgeneral);
                    valida = 1;
                    Swal.fire('Error', 'Valores Descuadrados en los depositos.', 'error');
                    document.querySelector(".btn_add").style.display = 'initial';
                }
                <?php
                if ($empresa->id != '0992704152001') {
                ?>
                    if (valor2 > 0 && (texto_2 == null || texto_2 == "")) {
                        Swal.fire('Error', 'Debe Ingresar la segunda cuenta destino', 'error');
                        valida = 1;
                        document.querySelector(".btn_add").style.display = 'initial';

                    }
                <?php
                }
                ?>
                if (valida == 0) {
                    // $(".print").css('visibility', 'visible');
                    //$(".btn_add").attr("disabled", true);
                    $.ajax({
                        url: "{{route('depositobancario.store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        type: 'POST',
                        datatype: 'json',
                        data: $("#form").serialize(),
                        success: function(data) {
                            //console.log(data);
                            $("#asiento").val(data.id_asiento);
                            $("#id").val(data.id);
                            $("#numero").val(data.numero);
                            Swal.fire(
                                data.status,
                                data.msj,
                                data.status
                            )
                            if (data.status == "error") {
                                //$(".btn_add").attr("disabled", true);
                                document.querySelector(".btn_add").style.display = 'initial';
                            }
                        },
                        error: function(data) {
                            console.error(data.responseText);
                            document.querySelector(".btn_add").style.display = 'initial';
                        }
                    });
                }

            } else {
                document.querySelector(".btn_add").style.display = 'initial';
            }
        });
    </script>
</section>
@endsection