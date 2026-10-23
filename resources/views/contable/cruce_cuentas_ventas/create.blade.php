@extends('contable.nota_debito_cliente.base')
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
</style>
<script type="text/javascript">
    function goBack() {
        window.history.back();

    }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('cr.cruce_cuentas')}}">Cruce de Cuentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Cruce de Cuentas</li>
        </ol>
    </nav>
    <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Guardado Correctamente
    </div>
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <div class="box-title"><b>Cruce Cuentas</b></div>
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
                            <div class=" col-md-2 px-1">
                                <label class="label_header">Estado</label>
                                <input style="background-color: green;" readonly class="form-control col-md-1">
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control" type="text" name="id_factura" id="id_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control" type="text" id="numero_factura" name="numero_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control" type="text" name="tipo" id="tipo" value="CR-NEW" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control" type="text" id="asiento" name="asiento" readonly>


                            </div>

                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control" id="fecha_hoy" type="date" name="fecha_hoy" onblur="validarCierre();" min="{{isset($min_date)?date('Y-m-d',strtotime($min_date.'+1 day')):''}}" value="{{date('Y-m-d')}}">

                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class="col-md-4 px-1">
                                <input type="hidden" name="total_suma" id="total_suma">
                                <label class="label_header" for="concepto">Concepto:</label>
                                <input class="form-control col-md-12" autocomplete="off" type="text" name="concepto" id="concepto" required>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header">{{trans('contableM.sucursal')}}</label>
                                <select name="sucursal" id="sucursal" class="form-control" required>
                                    <option value="">{{trans('agenda.seleccione')}}</option>
                                    @foreach($sucursales as $s)
                                    <option value="{{$s->codigo_sucursal}}">{{$s->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class="control-label label_header">Factura ID # </label>
                                <select class="select2 form-control" style="width: 100%;" onchange="traer_valor(this)" name="id_facturas" id="id_facturas" required>
                                    <option value="">Seleccione..</option>
                                    @foreach($facturas as $value)
                                    <option value="{{$value->id}}" data-observacion="{{$value->observacion}}" data-codigo="{{$value->id}}"> C: {{$value->nro_comprobante}} - #: {{$value->id}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="control-label label_header"> Info: </label>
                                <input class="form-control" type="text" name="infox" id="infox" readonly>
                            </div>
                            {{--
                            <div class=" col-md-6 px-0">
                                <label class="col-md-12 label_header" for="nombre_cliente">Proveedor : </label>
                                <select class="form-control select2" name="id_proveedor" style="width: 100%;" id="id_proveedor">
                                    <option value="">Seleccione...</option>
                                    @foreach($clientes as $value)
                                    <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                            @endforeach
                            </select>
                        </div>
                        --}}
                        <div class="col-md-4 col-xs-4 px-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
                <div class="col-md-12 px-1">
                    <label class="label_header" for="detalle_deuda">DETALLE DE CUENTAS EN EL HABER</label>
                </div>
                <div class="col-md-12 px-1">
                    <input type="hidden" name="id_compra" id="id_compra">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <div class="table-responsive col-md-12 px-1" style="width: 100%;">
                        <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr>
                                    <th style="width: 26.66%; text-align: center;">Código</th>
                                    <th style="width: 16.66%; text-align: center;">Detalle</th>
                                    <th style="width: 16.66%; text-align: center;">Divisa</th>
                                    <th style="width: 16.66%; text-align: center;">Valor</th>
                                    <th style="width: 16.66%; text-align: center;">Total Base</th>
                                    <th style="width: 6.66%; text-align: center;">
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
                                <input class="form-control  col-md-12" value="0.00" type="text" name="subtotal" readonly id="subtotal">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="impuesto">Impuesto</label>
                                <input class="form-control  col-md-12" value="0.00" type="text" autocomplete="off" onchange="sumar_impuesto()" name="impuesto" id="impuesto">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header" for="total">Total</label>
                                <input class="form-control  col-md-12" value="0.00" type="text" name="total" id="total" readonly>
                            </div>
                        </div>
                    </div>

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
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            tags: false
        });
        $("#form_guardado").validate({
            lang: 'en' // or whatever language option you have.
        });
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido.",
            remote: "Please fix this field.",
            email: "Please enter a valid email address.",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date (ISO).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "Please enter the same value again.",
            accept: "Please enter a value with a valid extension.",
            maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
            minlength: jQuery.validator.format("Please enter at least {0} characters."),
            rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
            range: jQuery.validator.format("Please enter a value between {0} and {1}."),
            max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
            min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
        });

    });

    function boton_deuda() {
        let boton = document.getElementById('boton_guardar');

        boton.style.display = "none"
        //swal("hassta aqui");
        var msj = "";


        if (msj == "") {
            if ($("#form_guardado").valid()) {
                $("#boton_guardar").attr("disabled", "disabled");
                $.ajax({
                    type: 'post',
                    url: "{{route('cr.cruce_cuentas_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#form_guardado').serialize(),
                    success: function(data) {
                        if (data.status == 'success') {
                            $("#id_factura").val(data.id);
                            $("#asiento").val(data.id_asiento);
                            $("#numero_factura").val(data.numero_factura);
                            swal("Correcto!", "Creado con éxito", "success");
                            $('#form_guardado input').attr('readonly', 'readonly');
                            //$("#boton_guardar").attr("disabled", "disabled");
                            buscarAsiento(data.id);
                        } else {
                            $('#boton_guardar').css('display', 'inline');
                            $("#boton_guardar").removeAttr("disabled");
                            swal("Error!", data.msj, "error");
                        }
                    },
                    error: function(data) {
                        boton.style.display = "block";
                        $("#boton_guardar").removeAttr("disabled");
                        swal("Error!", data.msj, "error");
                    }
                })
            } else {
                $('#boton_guardar').css('display', 'inline');
                $("#boton_guardar").removeAttr("disabled");
            }
        } else {
            $('#boton_guardar').css('display', 'inline');
            $('#boton_guardar').removeAttr("disabled");
            swal("Error!", "error", "error");
        }
    }

    function set_rubros(id) {
        $.ajax({
            type: 'post',
            url: "{{route('rubros_cliente.nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#rubro" + id).val()
            },
            success: function(data) {
                console.log(data);
                if (data.value != 'no') {
                    $("#id_codigo" + id).val(data[0]);
                    $("#codigo" + id).val(data[0]);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function traer_valor(datas) {
        //alert(data.value);
        $.ajax({
            type: 'get',
            url: "{{route('cr.traervalores')}}",
            datatype: 'json',
            data: {
                'codigo': datas.value,
            },
            success: function(data) {
                console.log(data);
                $("#infox").val(data[2]);
            },
            error: function(data) {
                console.log(data);
            },
        })
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
            $("#id_cliente").val(ui.item.id);
            //buscar_vendedor();

        },
        selectFirst: true,
        minLength: 1,
    });

    function set_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('rubros_cliente.codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val()
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#rubro" + id).val(data[0]);
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
        location.href = "{{route('cr.cruce_cuentas_create')}}";
    }

    function traer_cliente() {

        $.ajax({
            type: 'get',
            url: "{{route('nota_cliente_debito.buscar_cliente')}}",
            datatype: 'json',
            data: {
                'secuencia': $("#facturano").val(),
                'validacion': '1'
            },
            success: function(data) {
                if (data != 'error') {
                    $("#id_cliente").val(data[0]);
                    $("#nombre_cliente").val(data[1]);
                    console.log(data[2]);
                    if (data[2] == 1) {
                        $("#facturano").val(data[3]);
                    } else {
                        $("#facturan").val(data[3]);
                    }
                } else {
                    $("#facturano").val('');
                    $("#facturan").val('');
                    $("#id_cliente").val('');
                    $("#nombre_cliente").val('');
                }

            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function traer_cliente2() {
        $.ajax({
            type: 'get',
            url: "{{route('nota_cliente_debito.buscar_cliente')}}",
            datatype: 'json',
            data: {
                'validacion': '0',
                'id_factura': $("#facturan").val()
            },
            success: function(data) {
                if (data != 'error') {
                    $("#id_cliente").val(data[0]);
                    $("#nombre_cliente").val(data[1]);
                    console.log(data[2]);
                    if (data[2] == 1) {
                        $("#facturano").val(data[3]);
                    } else {
                        $("#facturan").val(data[3]);
                    }
                } else {
                    $("#facturano").val('');
                    $("#facturan").val('');
                    $("#id_cliente").val('');
                    $("#nombre_cliente").val('');
                }

            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function crea_td(contador) {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td> <select class="form-control select2" style="width: 100%;" name="codigo' + id + '" required> <option value=""></option> @foreach($cuentas as $x) <option value="{{$x->id}}" data-id="{{$x->id}}" data-nombre="{{$x->id}}"> #: {{$x->id_plan}}  {{$x->nombre}} </option> @endforeach </select> </td><td> <input class="form-control" name="detalle' + id + '" placeholder="Ingresar detalle" required> </td> <td><input style="width: 100%;" name="divisas" id="divisas" class="form-control" value="USD" readonly ></td> <td> <input class="valortotal form-control" name="valor' + id + '" style="width: 100%;" id="valor' + id + '" onchange="valor_rubro(' + id + ')" value="0.00" ></td><input class="visibilidad" type="hidden" name="visibilidad' + id + '" id="visibilidad' + id + '" value="1"><td><input class="form-control" style="width: 100%;" name="total_base' + id + '" id="total_base' + id + '" value="0.00" readonly></td><td style="text-align: center;"><button id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;

        $(".rubrosa").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('rubros_cliente.nombre')}}",
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
        $('.select2').select2({
            tags: false
        });

        $(".codigo").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('fact_contable_codigo')}}",
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
        /*if(!isNaN(ivan)){ $('#impuesto').val(totalsx.toFixed(2));   }*/
        if (!isNaN(sub)) {
            $('#subtotal').val(sub.toFixed(2));
        }
        if (!isNaN(total)) {
            $('#total').val(total_final.toFixed(2));
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

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input style='width: 100%;' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "<td> <input style='width: 100%;' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input style='width: 100%;' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input style='width: 100%;' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='Fact #:" + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
            "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5;' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input style='width: 100%;' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")'></td>" +
            "<td> <input style='width: 100%;' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
            "<td> <input style='width: 100%;' type='text' style='text-align: center;' name='abono_base" + pos + "' id='abono_base" + pos + "' readonly=''> </td>" +
            "</tr>";
        return markup;

    }

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_hoy").value;
            var fechaInicial = new Date(fecha1);
            let fechaAc = new Date();
            const fechaFinal = new Date(fecha2);
            if (fechaFinal < fechaInicial) {
                fechaInicial.setDate(fechaInicial.getDate() + 1);
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_hoy").value = fechaFormateada;
                return swal("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error")
            }
        <?php
        }
        ?>
    }
</script>

@endsection