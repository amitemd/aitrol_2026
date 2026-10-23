@extends('contable.cruce_valores.base')
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

        .alerta_correcto {
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
        }
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
        location.href = "{{route('cruce.index')}}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
            <li class="breadcrumb-item"><a href="../cruce_valores">Cruce de Valores a Favor</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Cruce de Valores a Favor</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 col-sm-9 col-6">
                            <div class="box-title"><b>CRUCE DE VALORES A FAVOR </b></div>
                        </div>
                        <div class="col-md-4 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <a class="btn btn-success btn-gray btn-xs" href="javascript:guardar_cruce()" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </a>
                                <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="label_header" style="padding-left: 0px">Estado</label>
                                <input class="col-md-12 form-control input-sm col-xs-12" style="background-color: green;">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Id</label>
                                <input id="id_proveedor" type="text" class="form-control  input-sm" name="id_proveedor" value="" onchange="cambiar_proveedor()">

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Número</label>
                                <input class="form-control input-sm" type="text" name="numero" id="numero" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo</label>
                                <input class="form-control input-sm" type="text" name="tipo" id="tipo" readonly value="ACR-CR-AF">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha</label>

                                <input class="form-control input-sm" onblur="validarPeriodo();validarCierre();" id="fecha" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" type="date" name="fecha" value="{{date('Y-m-d') }}">

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Asiento</label>
                                <input class="form-control input-sm" type="text" name="asiento" id="asiento" readonly>
                            </div>
                        </div>
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
                    <div class="col-md-10">
                        <div class="row  ">
                            <div class="col-md-8 col-xs-4 px-1">
                                <label class="control-label label_header">Concepto</label>
                                <input id="concepto" type="text" class="form-control  input-sm col-md-12" name="concepto">
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                <!--<input type="text" id = "nombre_proveedor" name="nombre_proveedor" class= "form-control form-control-sm nombre_proveedor input-sm col-md-12" onchange="cambiar_nombre_proveedor()" >-->
                                <select name="nombre_proveedor" id="nombre_proveedor" class="form-control form-control-sm select2_cuentas" style="width:100%" onchange="cambiar_nombre_proveedor(this);">
                                    <option value="">Seleccione</option>
                                    @foreach($proveedores as $value)
                                    <option value="{{$value->id}}"> {{$value->id}} || {{$value->nombrecomercial}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE VALORES A FAVOR</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1">
                        <input type="hidden" name="contador_a" id="contador_a" value="0">
                        <table id="example3" role="grid" aria-describedby="example2_info">
                            <thead class='well-dark'>
                                <tr style="position: relative;">
                                    <th style="width: 5%; text-align: center;">Emisión</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 5%; text-align: center;">Número</th>
                                    <th style="width: 22%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 4%; text-align: center;">Abono</th>
                                    <th style="width: 2%; text-align: center;">Nuevo </th>

                                </tr>
                            </thead>
                            <tbody id="crear_a">
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12 px-1">
                        <div class="row">
                            <div class="col-md-9">
                            </div>
                            <div class="col-md-3">
                                <label class="label_header col-md-12">Total</label>
                                <input class="form-control col-md-3" type="text" name="total_anticipos" id="total_anticipos" class="col-md-12" readonly>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="contador" id="contador" value="0" class="hidden">
                    <input type="hidden" name="total_suma" id="total_suma">

                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL PROVEEDOR</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1 " style="max-height: 250px;">
                        <table id="example2" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Vence</th>
                                    <th style="width: 8%; text-align: center;">Tipo</th>
                                    <th style="width: 10%; text-align: center;">Número</th>
                                    <th style="width: 20%; text-align: center;">Concepto</th>
                                    <th style="width: 2%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 5%; text-align: center;">Abono</th>
                                    <th style="width: 5%; text-align: center;">Saldo Base</th>
                                    <th style="width: 5%; text-align: center;">Abono Base</th>

                                </tr>
                            </thead>
                            <tbody id="crear">

                                @php $cont=0; @endphp
                                @foreach (range(1, 6) as $i)
                                <tr>
                                    <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5; width: 150% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                    <td> <input class="form-control" style="width: 150%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                    <td> <input class="form-control" style="text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" readonly> </td>
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
                            </div>
                            <div class="form-group col-md-2">
                                <!--  <input type="text" name="retencion_impuesto" id="retencion_impuesto" disabled >-->
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
                </div>
            </div>
    </form>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script src="{{ asset ("/js/icheck.js") }}"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });
            $('#fact_contable_check').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });

        });

        function crea_td(contador) {
            id = document.getElementById('contador').value;
            var midiv = document.createElement("tr")
            midiv.setAttribute("id", "dato" + id);
            midiv.innerHTML = '<td><input class="form-control" type="date" name="emision' + id + '" class="emision" id="emision' + id + '"/></td> <td><input class="visibilidad" type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input name="tipo' + id + '" class="tipo form-control" id="tipo' + id + '" value="ACR-EG" readonly></td><td> <input name="numero' + id + '" id="numero' + id + '" readonly> </td><td> <input class="cantidad form-control" type="text" id="concepto' + id + '" name="concepto' + id + '"></td><td><input class="form-control"  value="$" readonly></td><td><input class="form-control"  type="text" id="saldo' + id + '" name="saldo' + id + '" onchange="agregar_secuencia(' + id + ')" value="0.00" ></td><td> <input type="text" class="form-control" name="abono' + id + '" id="abono' + id + '" ><td> <input class="form-control" type="text" name="nuevo_saldo' + id + '" id="nuevo_saldo' + id + '" value="0.00" disabled> </td><td><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
            document.getElementById('det_recibido').appendChild(midiv);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;
        }

        function eliminar_registro(valor) {
            var dato1 = "dato" + valor;
            var nombre2 = 'visibilidad' + valor;
            document.getElementById(nombre2).value = 0;
            document.getElementById(dato1).style.display = 'none';
            suma_totales();
        }

        function guardar_cruce() {
            //$('#boton_guardar').prop('disabled', true);
            let boton = document.getElementById('boton_guardar');
            boton.style.display = "none"
            var formulario = document.forms["crear_factura"];
            var proveedor = formulario.id_proveedor.value;
            var nombre_proveedor = formulario.nombre_proveedor.value;
            var fecha = formulario.fecha.value;
            var concepto = formulario.concepto.value;
            var msj = "";
            var contador_a = formulario.contador_a.value;
            var contador = formulario.contador.value;
            var sucursal = $("#sucursal").val();
            if (proveedor == "") {
                msj += "Por favor, Llene el campo proveedor<br/>";
            }
            if (nombre_proveedor == "") {
                msj += "Por favor, Llene el campo de secuencia<br/>";
            }
            if (fecha == "") {
                msj += "Por favor, Llene la fecha del cruce<br/>";
            }
            if (concepto == "") {
                msj += "Por favor, Llene el concepto <br/>";
            }
            if (contador_a == "") {
                msj += "Por favor, Llene los campos faltantes de la tabla <br/>";
            }
            if (contador == "") {
                msj += "Por favor, Llene los campos faltantes de la tabla deuda <br/>";
            }
            if (sucursal == 0 || sucursal == "") {
                alertas("error", "Error...", "Seleccione la sucursal")
                return;
            }

            let cantidadElementoSF = $("#crear_a tr");
            let cantidadElementoSoF = $("#crear_a tr");
            console.log(cantidadElementoSF, cantidadElementoSoF)

            if (cantidadElementoSF.length == 0) {
                msj += "Necesita tener un valor a favor <br/>";
            }

            let cantidadEnc = 0;
            for (let index = 0; index < cantidadElementoSoF.length; index++) {

                if ($("#abono_a" + index).val() != 0 || $("#abono_a" + index).val() != "") {
                    console.log($("#abono_a" + index).val());
                    cantidadEnc++;
                }
            }
            if (cantidadEnc == 0) {
                msj += "Debe ingresar un valor al abono <br/>";
            }

            if (cantidadElementoSoF == 0) {
                msj += "Necesita tener una deuda a favor <br/>";
            }
            console.log(cantidadEnc);
            if (msj == "") {
                //alert("entras");
                $("#boton_guardar").attr("disabled", "disabled");
                $.ajax({
                    type: 'post',
                    url: "{{route('cruce.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {
                        if (data.status == 'success') {
                            swal(`{{trans('contableM.correcto')}}!`, "trans('contableM.secreocrucedevalocorrect')", "success");
                            buscarAsiento(data.id);
                            $('#crear_factura input').attr('readonly', 'readonly');
                            $("#boton_guardar").attr("disabled", "disabled");
                            $("#id_proveedor").val(data.id_proveedor);
                        } else {
                            $("#boton_guardar").attr("disabled", false);
                            swal('Error', data.msj, 'error')
                        }
                    },
                    error: function(data) {
                        $("#boton_guardar").attr("disabled", false);
                        console.log(data);
                    }
                })
            } else {
                swal({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
            }
        }

        function buscar_factura() {
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
                        crea_td(i);
                        $('#proveedor').val(data[0]);
                        $('#direccion_proveedor').val(data[4]);
                        $('#fecha').val(data[1]);
                        $('#f_caducidad').val(data[2]);
                        $('#id_empresa').val(data[6]);
                        for (var i = 0; i < data[3].length; i++) {
                            $('#codigo' + i).val(data[3][i].codigo);
                            $('#nombre' + i).val(data[3][i].nombre);
                            $('#bodega' + i).val(data[3][i].id_bodega);
                            $('#cantidad' + i).val(data[3][i].cantidad_total);
                            $('#total' + i).val(data[3][i].cantidad_total);
                            $('#precio' + i).val(data[3][i].precio);
                            if (data[3][i].iva == '1') {
                                document.getElementById('iva' + i).checked = true;
                            }
                            total_calculo(i);
                            cambiar_proveedor();
                        }
                        $("#contador").val(data[3].length);
                    } else {}
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
                    //console.log(data.value);
                    if (data.value != "no") {
                        $('#nombre_proveedor').val(data.value);
                        $('#direccion_proveedor').val(data.direccion);
                    } else {
                        $('#nombre_proveedor').val(" ");
                        $('#direccion_proveedor').val("");
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

        function cambiar_nombre_proveedor(eq) {
            $.ajax({
                type: 'post',
                url: "{{route('compra_buscar_proveedornombre')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': eq.value
                },
                success: function(data) {
                    if (data.value != "no") {
                        $('#id_proveedor').val(data.value);
                        $('#direccion').val(data.direccion);
                        buscar_proveedor()
                        buscador_anticipos()
                    } else {
                        $('#id_proveedor').val("");
                        $('#direccion').val("");
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
                    if (data.value != "no") {
                        $("#crear").empty();
                        var fila = 0;
                        console.log(data);
                        for (i = 0; i < data[5].length; i++) {
                            if (data[5][i].tipo == 1) {
                                var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FA', data[5][i].numero, data[5][i].proveedor + "" + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                                $('#example2').append(row);
                                fila = i;
                            } else {
                                var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FACT', data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
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

        function validar_td(id) {
            if ((id) != null) {
                var valor = parseFloat($("#saldo" + id).val());
                var abono = parseFloat($("#abono" + id).val());
                suma_totales();
                if (!isNaN(abono) && !isNaN(valor)) {
                    if (valor >= abono) {
                        let operacion = parseFloat(valor) - parseFloat(abono);
                        $("#nuevo_saldo" + id).val(operacion.toFixed(2, 2));
                        // sumar y adebitar
                        // $("#total_anticipos").val(parseFloat($("#total_anticipos").val() || 0) + parseFloat(abono));
                    } else {
                        valor = 0;
                        $("#abono" + id).val(valor.toFixed(2, 2));
                        swal("¡Error!", "Error no puede superar al valor del cheque", "error")
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

        function buscarAsiento(id_asiento) {
            $.ajax({
                type: 'get',
                url: "{{route('buscar_asiento.diario')}}",
                datatype: 'json',
                data: {
                    'id_asiento': id_asiento,
                    'validacion': '2'
                },
                success: function(data) {
                    if (data.value != 'No se encontraron resultados') {
                        $('#asiento').val(data[0]);
                        $('#numero').val(data[1]);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function validar_saldos(id) {
            if ((id) != null) {
                var valor = parseFloat($("#saldo_a" + id).val());
                var abono = parseFloat($("#abono_a" + id).val());
                suma_totales2();
                suma_totales3();
                var sumax = parseFloat($("#saldoax").val());
                let zero = 0;
                var total = parseFloat($("#total_anticipos").val());
                if (isNaN(total)) {
                    total = 0;
                }
                var cantidad = parseFloat($("#total_suma_a").val());
                if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                    if (abono >= 0) {
                        if(abono == 0){
                            $("#nuevo_saldo_a" + id).val(zero.toFixed(2, 2));  
                        }else{
                            $("#nuevo_saldo_a" + id).val(valor - abono);  
                        }   
                        if (cantidad <= valor && cantidad <= sumax) {
                            var abo = total + abono;
                            var cantid = parseFloat($("#saldo_a" + id));
                            $("#total_anticipos").val(cantidad.toFixed(2, 2));
                            $("#abono_a" + id).val(abono.toFixed(2, 2));
                            var totalx = valor - abono;

                        } else {
                            $("#abono_a" + id).val(abono.toFixed(2, 2));
                            suma_totales2();
                            var cantidad = parseFloat($("#total_suma_a").val());
                            $("#total_anticipos").val(cantidad.toFixed(2, 2));
                        }
                    } else {
                        var abo = total - abono;
                        suma_totales2();
                        $("#abono_a" + id).val(abono.toFixed(2, 2));
                        $("#total_anticipos").val(cantidad.toFixed(2, 2));
                    }

                    
                } else {
                    abono = 0;
                    valor = 0;
                    $("abono_a" + id).val(valor.toFixed(2, 2));
                }
            }
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

            $("#crear_a tr").each(function() {
                $(this).find('td')[0];
                cantidad = parseFloat($("#abono_a" + contador).val());
                if (!isNaN(cantidad)) {
                    total += cantidad;
                }
                contador = contador + 1;
            });
            if (isNaN(total)) {
                total = 0;
            }
            $("#total_suma_a").val(total.toFixed(2, 2));

            //alert(total_fin);

        }

        function suma_totales3() {
            contador = 0;
            iva = 0;
            total = 0;
            sub = 0;
            descu1 = 0;
            total_fin = 0;
            descu = 0;
            cantidad = 0;

            $("#crear_a tr").each(function() {
                $(this).find('td')[0];
                cantidad = parseFloat($("#saldo_a" + contador).val());
                if (!isNaN(cantidad)) {
                    total += cantidad;
                }
                contador = contador + 1;
            });
            if (isNaN(total)) {
                total = 0;
            }
            $("#saldoax").val(total.toFixed(2, 2));

            //alert(total_fin);

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


        function boton_deuda(id) {
            if ($("#id_cliente").val() == "") {
                return mensajes("warning", "Seleccione un cliente para continuar");
            } else if ($("#valor_total").val() <= 0) {

                $('#crear tr').each(function(i, obj) {
                    $("#abono" + i).val("0.00");
                    $("#nuevo_saldo" + i).val("0.00");
                });
                return mensajes("warning", "Ingrese un valor mayor a 0");
            }

            let totalAbono = 0;
            $(".abonoA").each((i, value) => {
                if (value.value !== "") {
                    totalAbono += parseFloat(value.value);
                }
            });

            var valor_total = parseFloat(totalAbono);
            var valor_restante = valor_total;
            if (valor_restante === 0) {
                $('#crear tr').each(function(i, obj) {
                    $("#abono" + i).val("0.00");
                    $("#nuevo_saldo" + i).val("0.00");
                });
                return;
            }

            $('#crear tr').each(function(i, obj) {
                $("#abono" + i).val("0.00");
                $("#nuevo_saldo" + i).val("0.00");
            });

            $('#crear tr').each(function(i, obj) {
                var saldo_actual = parseFloat($("#saldo" + i).val()) || 0;
                if (saldo_actual < 0) {
                    $("#abono" + i).val("0.00");
                    $("#nuevo_saldo" + i).val("0.00");
                    return;
                } else {
                    var abono = Math.min(saldo_actual, valor_restante);
                    $("#abono" + i).val(abono.toFixed(2));
                    var nuevo_saldo = saldo_actual - abono;
                    $("#nuevo_saldo" + i).val(nuevo_saldo.toFixed(2));
                    valor_restante -= abono;
                    console.log("valor restante", valor_restante)
                    if (valor_restante <= 0) {

                        return false;
                    }
                }
            });
            suma_totales2();
        }




        function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
            var markup = "";
            var num = parseInt(pos) + 1;
            markup = "<tr>" +
                "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
                " <input class='form-control' type='hidden'   name='id_actualiza" + pos + "' id='id_actualiza" + pos + "'  value='" + id + "'>" +
                "<td> <input class='form-control' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
                "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto_a" + pos + "' id='concepto_a" + pos + "' value='Fact # " + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")'></td>" +
                "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
                "<td> <input class='form-control' type='text' style='text-align: center; name='abono_base" + pos + "' id='abono_base" + pos + "' readonly=''> </td>" +
                "</tr>";
            return markup;

        }

        function addNewRow2(pos, fecha, tipo, fact_numero, observacion, valor, id) {
            var markup = "";
            var num = parseInt(pos) + 1;
            markup = "<tr>" +
                "<td> <input type='text' class='form-control input-sm' name='emision" + pos + "' id='emision" + pos + "' readonly='' value='" + fecha + "'> <input type='hidden' name='id_act" + pos + "' value='" + id + "'>  </td>" +
                "<td> <input type='text' class='form-control input-sm' name='tipo" + pos + "' id='tipo" + pos + "' value='" + tipo + "' readonly=''> </td>" +
                "<td> <input type='text' class='form-control input-sm' name='numero_a" + pos + "' id='numero_a" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
                "<td> <input type='text' onmouseover='bigImg(this)' onmouseout='normalImg(this)' class='form-control input-sm'  name='concepto" + pos + "' id='concepto" + pos + "' value='" + observacion + "' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='saldo_a" + pos + "' value='" + valor + "' id='saldo_a" + pos + "' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' class='form-control input-sm abonoA' name='abono_a" + pos + "' id='abono_a" + pos + "' onchange='validar_saldos(" + pos + ")' onfocusout='boton_deuda(" + pos + ")'></td>" +
                "<td> <input type='text'  class='form-control input-sm' name='nuevo_saldo" + pos + "' value='0.00'  id='nuevo_saldo_a" + pos + "' readonly=''><input type='hidden' name='visibilidad" + pos + "' id='visibilidad" + pos + "' value='0'></td>" +
                "</tr>";
            return markup;
        }



        function validarCierre() {

            <?php
            if (isset($min_date) && $min_date != null) {
            ?>
                let fechaAc = new Date();
                var fecha1 = '<?= $min_date; ?>';
                console.log(fecha1);
                var fecha2 = document.getElementById("fecha").value;
                const fechaInicial = new Date(fecha1);
                const fechaFinal = new Date(fecha2);
                if (fechaFinal < fechaInicial) {
                    swal("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error")
                    const fechaFormateada = fechaAc.toISOString().split('T')[0];
                    document.getElementById("fecha").value = fechaFormateada;
                }
            <?php
            }
            ?>
        }

        function validarPeriodo() {
            let fechaAc = new Date();
            let mes = fechaAc.getUTCMonth() + 1;
            var fechaInput = document.getElementById("fecha").value;
            var partesFecha = fechaInput.split('/');
            var fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
            var d = new Date(fechaFormateada);
            var month = d.getMonth() + 1;
            var ty = document.getElementById("fechita").value;
            if (mes != month) {
                return swal("Recuerde!", '<?= trans('contableM.lafechaingreestafueradelperio'); ?>', "error")
            }
        }

        function bigImg(x) {
            x.style.height = "100%";
            x.style.background = "#1E88E5";
            x.style.color = "#fff";
            x.style.zIndex = "9999";
            x.style.position = "relative";
            x.style.width = "450%";
        }

        function normalImg(x) {
            x.style.height = "100%";
            x.style.width = "100%";
            x.style.color = "#000";
            x.style.background = "#DDEBEE";
        }

        function buscador_anticipos() {
            var proveedor = $("#id_proveedor").val();
            var tipo = parseInt($("#esfac_contable").val());
            $.ajax({
                type: "post",
                url: "{{route('cruce.anticipos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "json",
                data: {
                    'proveedor': proveedor,
                    'tipo': tipo
                },
                success: function(data) {
                    console.log("aqui ac");
                    console.log(data);
                    if (data.value != "no") {
                        $("#crear_a").empty();
                        var fila = 0;
                        for (i = 0; i < data.length; i++) {
                            var row = addNewRow2(i, data[i].fecha_asiento, 'BAN-ND', data[i].secuencia, data[i].observacion, data[i].valor_abono, data[i].id);
                            if (data[i].id_referencia == null) {
                                row = addNewRow2(i, data[i].fecha_asiento, 'ACR-EG', data[i].secuencia, data[i].observacion, data[i].valor_abono, data[i].id);
                            }
                            $('#example3').append(row);
                            fila = i;
                        }
                        $("#contador_a").val(fila);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function nuevo_comprobante() {
            location.href = "{{route('cruce.create')}}";
        }
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });

        });
    </script>
</section>
@endsection