@extends('contable.cruce_valores_cliente.base')
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

    .disableds {
        display: none;
    }

    .disableds2 {
        display: none;
    }

    .disableds3 {
        display: none;
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

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #9E9E9E;
        border-radius: 2px;
        color: white;
    }

    .borde {
        border: 2px solid #9E9E9E;
    }

    .hde {
        background-color: #888;
        width: 100%;
        height: 25px;
        margin: 0 auto;
        line-height: 25px;
        color: #FFF;
        text-align: center;
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
        location.href = "{{route('cruce_clientes.index')}}";
    }
</script>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
            <li class="breadcrumb-item"><a href="{{route('cruce_clientes.index')}}">Cruce de Valores a Favor</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Cruce de Valores a Favor</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <div class="box-title"><b>CLIENTES- CRUCE DE VALORES A FAVOR</b></div>
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
                                <input class="col-md-12 form-control  col-xs-12" style="background-color: green;">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Id</label>
                                <input id="idx" type="text" class="form-control  " name="idx">

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Número</label>
                                <input class="form-control " type="text" name="numero" id="numero" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="CLI-CR-AF">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha</label>
                                <input class="form-control  " id="fecha" type="date" onblur="validarCierre();" name="fecha" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Asiento</label>
                                <input class="form-control " type="text" name="asiento" id="asiento" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-12 col-xs-4 px-1">
                                <label class="control-label label_header">Concepto</label>
                                <input id="concepto" type="text" class="form-control   col-md-12" autocomplete="off" placeholder="Concepto" name="concepto">
                            </div>
                            <div class="col-md-12 col-xs-12 px-0">
                                <label class="control-label label_header">Cliente</label>
                            </div>
                            <div class="col-md-6 col-xs-6 px-0">
                                <input type="text" id="id_cliente" name="id_cliente" placeholder="Cédula" class="form-control  form-control-sm id_cliente  col-md-12">
                            </div>
                            <div class="col-md-6 col-xs-6 px-0">
                                <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre Cliente" class="form-control form-control-sm nombre_cliente  col-md-12">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE VALORES A FAVOR</label>
                    </div>
                    <input type="hidden" name="contador_a" id="contador_a" value="0">
                    <div class="table-responsive col-md-12 px-0" style="max-height: 250px;">
                        <table id="example3" class="table-responsive col-md-12 px-0" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 5%; text-align: center;">Emisión</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 5%; text-align: center;">Número</th>
                                    <th style="width: 18%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 4%; text-align: center;">Abono</th>
                                    <th style="width: 6%; text-align: center;">Nuevo Saldo</th>

                                </tr>
                            </thead>
                            <tbody id="crear_a">
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>


                    <div class="col-md-12">
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
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                    </div>

                    <div class="table-responsive  col-md-12 px-0" style="max-height: 250px;">
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
                                    <td> <input class="form-control" type="text" readonly> </td>
                                    <td> <input class="form-control" type="text" readonly> </td>
                                    <td> <input class="form-control" type="text" readonly> </td>
                                    <td> <input class="form-control" type="text" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" value="$" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5; width: 100% " type="text" readonly> </td>
                                    <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{$cont}}" readonly></td>
                                    <td> <input class="form-control" style="width: 80%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" readonly></td>
                                    <td> <input class="form-control" style="text-align: center; width: 75%;" type="text" name="abono_base{{$cont}}" readonly> </td>

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
                                <div class="input-group">
                                    <input type="hidden" name="retencion_iva" id="retencion_iva" disabled>
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
            midiv.innerHTML = '<td><input class="form-control" type="date" name="emision' + id + '" class="emision" id="emision' + id + '"/></td> <td><input class="visibilidad" type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input name="tipo' + id + '" class="tipo form-control" id="tipo' + id + '" value="ACR-EG" readonly></td><td> <input name="numero' + id + '" id="numero' + id + '" readonly> </td><td> <input class="cantidad form-control" type="text" id="concepto' + id + '" name="concepto' + id + '"></td><td><input class="form-control"  value="$" readonly></td><td><input class="form-control"  type="text" id="saldo' + id + '" name="saldo' + id + '" onchange="agregar_secuencia(' + id + ')" value="0.00" ></td><td> <input type="text" class="form-control" name="abono' + id + '" id="abono' + id + '" ><td> <input class="form-control" style="width: 100%;" type="text" name="nuevo_saldo' + id + '" id="nuevo_saldo' + id + '" value="0.00" disabled> </td><td><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
            document.getElementById('det_recibido').appendChild(midiv);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;
        }
        $("#id_cliente").autocomplete({
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
                    }
                });
            },
            change: function(event, ui) {
                $("#crear").empty();
                $("#crear_a").empty();
                $("#nombre_cliente").val(ui.item.nombre);
                buscar_vendedor();
                buscador_anticipos();
            },
            selectFirst: true,
            minLength: 1,
        });
        function eliminar_registro(valor) {
            var dato1 = "dato" + valor;
            var nombre2 = 'visibilidad' + valor;
            document.getElementById(nombre2).value = 0;
            document.getElementById(dato1).style.display = 'none';
            suma_totales();
        }
        function buscarAsiento(id_asiento) {
            $.ajax({
                type: 'get',
                url: "{{route('buscar_asiento.diario')}}",
                datatype: 'json',
                data: {
                    'id_asiento': id_asiento,
                    'validacion': '3'
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

        function validarCierre() {
            <?php
            if (isset($min_date) && $min_date != null) {
            ?>
                let fechaAc = new Date();
                var fecha1 = '<?= $min_date; ?>';
                var fecha2 = document.getElementById("fecha").value;
                const fechaInicial = new Date(fecha1);
                const fechaFinal = new Date(fecha2);
                if (fechaFinal <= fechaInicial) {
                    alertas("error", "Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.')
                    const fechaFormateada = fechaAc.toISOString().split('T')[0];
                    document.getElementById("fecha").value = fechaFormateada;
                }
            <?php
            }
            ?>
        }

        function guardar_cruce() {

            let boton = document.getElementById('boton_guardar');

            boton.style.display = "none"

            var formulario = document.forms["crear_factura"];
            var proveedor = formulario.id_cliente.value;
            var nombre_proveedor = formulario.nombre_cliente.value;
            var fecha = formulario.fecha.value;
            var concepto = formulario.concepto.value;
            var msj = "";
            var contador_a = formulario.contador_a.value;
            var contador = formulario.contador.value;
            var total_anticipos = formulario.total_anticipos.value;
            var total_suma = formulario.total_suma.value;
            if (proveedor == "") {
                msj += "Por favor, Llene el campo de cliente<br/>";
            }
            if (nombre_proveedor == "") {
                msj += "Por favor, Llene el campo de cliente<br/>";
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
            if (total_anticipos == "") {
                msj += "Por favor, use los anticipos existentes <br/>";
            }
            if (total_suma == 0 || total_suma == "") {
                msj += "Por favor, usa los valores de ingreso para pagar las facturas <br/>";
            }
            if (msj == "") {
                $("#boton_guardar").hide();
                $.ajax({
                    type: 'post',
                    url: "{{route('cruce_clientes.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {
                        if (data.status == 'success') {
                            swal('Success', data.msj, 'success')
                            //swal(`{{trans('contableM.correcto')}}!`,"trans('contableM.secreocrucedevalocorrect')","success");
                            // $('#crear_factura input').attr('readonly', 'readonly');
                            $("#idx").val(data.id);
                            buscarAsiento(data);
                            $("#asiento").val(data.id_asiento);
                        } else {
                            swal('Error', data.msj, 'error')
                        }
                    },
                    error: function(data) {
                        boton.style.display = "block"
                        console.log(data);
                    }
                })
            } else {
                $("#boton_guardar").show();
                swal({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
            }
        }

        $("#nombre_cliente").autocomplete({

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
                $("#crear").empty();
                $("#crear_a").empty();
                $("#id_cliente").val(ui.item.id);
                buscar_vendedor();
                buscador_anticipos();

            },
            selectFirst: true,
            minLength: 1,
        });

        function buscar_vendedor() {
            var proveedor = $("#id_cliente").val();
            var tipo = parseInt($("#esfac_contable").val());
            $.ajax({
                type: "post",
                url: "{{route('clientes.deudas')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "json",
                data: {
                    'id_cliente': proveedor,
                    'tipo': tipo
                },
                success: function(data) {
                    $("#crear").empty();
                    if (data.value != "no resultados") {
                        $("#crear").empty();
                        var fila = 0;
                        //console.log(data);
                        for (i = 0; i < data[4].length; i++) {
                            if (tipo != 1) {
                                var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].nro_comprobante, data[4][i].nro_comprobante, data[4][i].valor_contable, data[4][i].id);
                                $('#example2').append(row);
                                fila = i;
                            } else {
                                var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].nro_comprobante, data[4][i].nro_comprobante, data[4][i].valor_contable, data[4][i].id);
                                $('#example2').append(row);
                                fila = i;
                            }
                        }
                        //console.log("total es:",fila);

                        $("#contador").val(fila);
                        //console.log(data[5]);
                    }
                    //swal("Correcto!", "Superavit creado correctamente", "success");                  
                },
                error: function(data) {
                    console.log(data);


                }
            });



        }

        function validar_td(id) {
            if ((id) != null) {
                var valor = parseFloat($("#total_anticipos").val());
                var abono = parseFloat($("#abono" + id).val());
                var saldo = parseFloat($("#saldo" + id).val());
                suma_totales();
                var cantidad = parseFloat($("#total_suma").val());
                if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                    if (cantidad <= valor) {

                        if (abono <= saldo) {
                            var tot = saldo - abono;
                            $("#abono" + id).val(abono.toFixed(2, 2));
                            $("#nuevo_saldo" + id).val(tot.toFixed(2, 2));
                        } else {
                            var tot = saldo - abono;
                            $("#abono" + id).val(saldo.toFixed(2, 2));
                            $("#nuevo_saldo" + id).val(tot.toFixed(2, 2));
                        }

                    } else {
                        valor = 0;
                        $("#abono" + id).val(valor.toFixed(2, 2));
                        $("#nuevo_saldo" + id).val(valor.toFixed(2, 2));
                        suma_totales();
                        swal("¡Error!", "Error no puede superar al valor", "error")
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

        function validar_saldos(id) {
            if ((id) != null) {
                var valor = parseFloat($("#saldo_a" + id).val());
                var abono = parseFloat($("#abono_a" + id).val());
                suma_totales2();
                suma_totales3();
                var sumax = parseFloat($("#saldoax").val());
                var total = parseFloat($("#total_anticipos").val());
                if (isNaN(total)) {
                    total = 0;
                }
                var cantidad = parseFloat($("#total_suma_a").val());
                if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                    if (abono > 0) {
                        if (abono <= valor && cantidad <= sumax) {
                            var abo = total + abono;
                            console.log("aqui");
                            var cantid = parseFloat($("#saldo_a" + id));
                            $("#total_anticipos").val(cantidad.toFixed(2, 2));
                            suma_totales2();
                            $("#abono_a" + id).val(abono.toFixed(2, 2));
                            var totalx = valor - abono;
                            // console.log("daz");
                            $("#nuevo_saldo_a" + id).val(totalx.toFixed(2, 2));
                        } else {

                            valor = 0;
                            $("#abono_a" + id).val(valor.toFixed(2, 2));
                            $('#nuevo_saldo_a' + id).val(valor.toFixed(2, 2));
                            suma_totales2();
                            suma_totales();
                            var cantidad = parseFloat($("#total_suma_a").val());
                            $("#total_anticipos").val(cantidad.toFixed(2, 2));
                            swal("¡Error!", "Error no puede superar al valor del anticipo", "error")
                        }
                    } else {
                        var abo = total - abono;
                        suma_totales();
                        valor = 0;
                        //console.log("dada");
                        $("#nuevo_saldo_a" + id).val(abono.toFixed(2, 2));
                        $("#abono_a" + id).val(abono.toFixed(2, 2));
                        $("#total_anticipos").val(cantidad.toFixed(2, 2));
                    }
                } else {
                    abono = 0;

                    valor = 0;
                    $("#nuevo_saldo_a" + id).val(valor.toFixed(2, 2));
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
            console.log(total);
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

        function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
            var markup = "";
            var num = parseInt(pos) + 1;
            markup = "<tr>" +
                "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
                "<td> <input class='form-control' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='Fact # " + fact_numero + " Cliente: " + observacion + "' readonly=''> <input type='hidden' name='id_fact" + pos + "' value='" + id + "'> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' autocomplete='off' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")'></td>" +
                "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
                "<td> <input class='form-control' type='text' style='text-align: center; width: 70%;' name='abono_base" + pos + "' id='abono_base" + pos + "' readonly=''> </td>" +
                "</tr>";
            return markup;

        }

        function addNewRow2(pos, fecha, tipo, fact_numero, observacion, valor, id) {
            var markup = "";
            var num = parseInt(pos) + 1;
            markup = "<tr>" +
                "<td> <input type='text' class='form-control input-sm' name='emision" + pos + "' id='emision" + pos + "' readonly='' value='" + fecha + "'> <input type='hidden' name='idac" + pos + "' value='" + id + "'> </td> " +
                "<td> <input type='text' class='form-control input-sm' name='tipo" + pos + "' id='tipo" + pos + "' value='" + tipo + "' readonly=''> </td>" +
                "<td> <input type='text' class='form-control input-sm' name='numero_a" + pos + "' id='numero_a" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
                "<td> <input type='text'class='form-control input-sm' name='concepto_a" + pos + "' id='concepto_a" + pos + "' value='" + observacion + "' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='saldo_a" + pos + "' value='" + valor + "' id='saldo_a" + pos + "' readonly=''> </td>" +
                "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' class='form-control input-sm' name='abono_a" + pos + "' autocomplete='off' id='abono_a" + pos + "' onchange='validar_saldos(" + pos + ")'></td>" +
                "<td> <input type='text'  class='form-control input-sm' name='nuevo_saldo" + pos + "' value='0.00'  id='nuevo_saldo_a" + pos + "' readonly=''><input type='hidden' name='visibilidad" + pos + "' id='visibilidad" + pos + "' value='0'></td>" +
                "</tr>";
            return markup;

        }

        function buscador_anticipos() {
            var proveedor = $("#id_cliente").val();
            //var tipo= parseInt($("#esfac_contable").val());
            $("#crear_a").empty();
            $.ajax({
                type: "post",
                url: "{{route('cruce_clientes.obtener_anticipos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "json",
                data: {
                    'proveedor': proveedor
                },
                success: function(data) {
                    //console.log(data);
                    $("#crear_a").empty();
                    if (data.value != "no resultados") {
                        $("#crear_a").empty();
                        var fila = 0;
                        for (i = 0; i < data.length; i++) {
                            var row = addNewRow2(i, data[i].fecha, 'CLI-IN', data[i].secuencia, data[i].observaciones, data[i].deficit_ingreso, data[i].id);
                            $('#example3').append(row);
                            fila = i;
                        }

                        $("#contador_a").val(fila);

                    }

                },
                error: function(data) {
                    console.log(data);
                    $("#crear_a").empty();

                }
            });



        }

        function nuevo_comprobante() {
            location.href = "{{route('cruce_clientes.create')}}";
        }


    </script>
</section>
@endsection