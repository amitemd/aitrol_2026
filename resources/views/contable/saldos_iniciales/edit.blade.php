@extends('contable.credito_acreedores.base')
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
</style>
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
</script>
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
            <li class="breadcrumb-item"><a href="{{ route('saldosinicialesp.index2') }}">Saldos Iniciales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Saldo Inicial</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-3">
                            <div class="box-title "><b>Saldo Iniciales Acreedores</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$compras->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
        <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
</a>
<a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$compras->id])}}" target="_blank">
    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
</a>
                              
                                <button type="button" onclick="goBack()" class="btn btn-success btn-xs btn-gray " style="padding: 7px 27px">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
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

                            <div class=" col-md-1 px-1">
                                <label class="label_header">Estado</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" value="{{$compras->id}}" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" value="{{$compras->numero}}" readonly>

                            </div>
                            <div class=" col-md-1 px-1">

                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="ACR-DB" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" value="{{$compras->id_asiento_cabecera}}" readonly>

                            </div>

                            <div class=" col-md-4 px-1">

                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="{{$compras->fecha}}">

                            </div>
                        </div>

                        <div class=" col-md-6 px-1">
                            {{ csrf_field() }}
                            <input type="hidden" name="superavit" id="superavit" value="0">
                            <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                            <select class="form-control select2_cuentas" style="width: 100%;" name="id_proveedor" req id="id_proveedor">
                                <option value="">Seleccione...</option>
                                @foreach($proveedor as $value)
                                <option @if($value->id== $compras->proveedor) selected="selected" @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 px-1">
                            <label class="col-md-12 label_header" for="acreedor">Concepto: </label>
                            <input type="text" class="form-control" name="concepto" id="concepto" value="@if($compras->observacion!=null) {{$compras->observacion}} @endif">
                        </div>


                    </div>
                    <div class="col-md-12">
                        <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS</label>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <input type="hidden" name="contador" id="contador" value="0">
                        <div class="table-responsive col-md-12 px-1">
                            <table id="example3" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 8%; text-align: center;">Rubro</th>
                                        <th style="width: 20%; text-align: center;">Detalle</th>
                                        <th style="width: 10%; text-align: center;">Divisa</th>
                                        <th style="width: 10%; text-align: center;">Valor</th>
                                        <th style="width: 6%; text-align: center;">Total Base</th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="det_recibido">
                                    <tr>
                                        <td>SALDOS INICIALES</td>
                                        <td>SALDOS INICIALES</td>
                                        <td>USD</td>
                                        <td>{{$compras->total_final}}</td>
                                        <td>{{$compras->total_final}}</td>
                                    </tr>
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
                                    &nbsp;
                                    <!--
                                            <label class="label_header" for="subtotal">Subtotal</label>
                                            <input class="form-control  col-md-12" type="text" name="subtotal" id="subtotal" >-->
                                </div>
                                <div class="col-md-2 px-1">
                                    &nbsp;
                                    <!--
                                            <label class="label_header" for="impuesto">Impuesto</label>
                                            <input class="form-control  col-md-12" type="text" onchange="sumar_impuesto()" name="impuesto" id="impuesto" >-->
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="total">Total</label>
                                    <input class="form-control  col-md-12" type="text" name="total" id="total" value="{{$compras->total_final}}" readonly>
                                </div>

                            </div>
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
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    function buscar_factura() {
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_buscar_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_factura': $("#buscar").val()
            },
            success: function(data) {
                console.log(data);
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[1]);
                $("#concepto").val(data[4] + '.' + ' ' + 'REF :' + data[0]);
                $("#asiento").val(data[1]);
                $("#acreedor").val(data[0] + ' ' + data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#nuevo_saldo0").val(data[10]);
                $("#total_deudas").val(data[10]);
                $("#id_compra").val(data[14]);
                for (i = 0; i < data[6].length; i++) {
                    $("#vence" + i).val(data[6][i].fecha);
                    $("#tipo" + i).val(data[6][i].id_tipoproveedor);
                    $("#numero_referencia" + i).val((data[6][i].serie) + '-' + data[1]);
                    $("#base_fuente" + i).val(data[6][i].valor);
                    $("#divisas" + i).val(data[6][i].divisas);
                    $("#numero" + i).val(data[6][i].id);
                    $("#concepto" + i).val((data[6][i].id) + '-' + data[0]);
                    $("#saldo" + i).val((data[6][i].valor));
                    $("#tipo_rfiva" + i).val((data[6][i].id_porcentaje_iva));
                    $("#tipo_rfir" + i).val((data[6][i].id_porcentaje_ft));
                    var iva_base = parseFloat(data[6][i].valor);
                    var total_iva = iva_base * 12 / 100;
                    $("#base_iva" + i).val(total_iva);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
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
        } else {
            total = subtotal;
            impuesto = 0;
            $("#impuesto").val(impuesto.toFixed(2, 2));
            $("#total").val(total.toFixed(2, 2));

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
                    $('#asiento').val(data[0]);
                    $('#numero').val(data[1]);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
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
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                //console.log(data);
                $("#id_proveedor").val(data.value);
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
                    console.log(data);
                }
            });
        },
        minLength: 1,
    });

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

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            alert("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
    }

    function lista_valores(id) {

        var variable_select = $("#tipo_rfir" + id).val();
        var variable = parseFloat($("#total_factura").val());
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_query')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': variable_select
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'no') {

                    $("#total_rfir" + id).val(data[0].valor);
                    $("#retencion_impuesto").val(data[0].valor);
                    var totales = parseFloat($("#numero_factura").val());
                    var final = totales * 0.12;
                    total_abono()
                    $("#base_iva" + id).val();
                    /*if(final!= NaN){
                        $("#base_iva"+id).val(final.toFixed(2));
                    }*/

                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function lista_valores2(id) {

        var variable_select = $("#tipo_rfiva" + id).val();
        var variable = parseFloat($("#total_factura").val());
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_query2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': variable_select
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'no') {

                    $("#total_rfiva" + id).val(data[0].valor);
                    $("#retencion_iva").val(data[0].valor);
                    total_abono()
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function total_abono() {
        var variable1 = parseFloat($("#retencion_impuesto").val())
        var variable2 = parseFloat($("#retencion_iva").val())
        var totales = variable1 + variable2;
        var vad = parseFloat($("#total_factura").val());
        var total_da = vad - totales;
        if (totales != NaN) {
            $("#abono0").val(totales);
            $("#abono_base0").val(totales);
            $("#total_egreso").val(totales);
            $("#nuevo_saldo").val(total_da);
        }


    }

    function nuevo_comprobante() {
        location.href = "{{route('saldosinicialesp.index')}}";
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
        /*var totalsx= ivan*iva;*/
        var totalsx = 0;
        var total_final = totalsx + ivan;
        if (!isNaN(ivan)) {
            $('#impuesto').val(totalsx.toFixed(2));
        }
        if (!isNaN(sub)) {
            $('#subtotal').val(sub.toFixed(2));
        }
        if (!isNaN(total)) {
            $('#total').val(total_final.toFixed(2));
        }
    }

    function crea_td(contador) {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td> <input name="rubro' + id + '" id="rubro' + id + '" onchange="set_rubros(' + id +
            ')" class="rubrosa" style="width: 98%;"> <input type="hidden" name="id_codigo' + id + '" id="id_codigo' + id +
            '"></td> <td> <input style="width: 98%;" name="detalle_rubro' + id + '" id="detalle_rubro' + id +
            '" ></td> <td><input style="width: 98%;" name="divisas" id="divisas" value="USD" readonly ></td> <td> <input class="valortotal" name="valor' +
            id + '" style="width: 98%;" id="valor' + id + '" onchange="valor_rubro(' + id +
            ')" value="0.00" ></td><input class="visibilidad" type="hidden" name="visibilidad' + id + '" id="visibilidad' +
            id + '" value="1"><td><input style="width: 90%;" name="total_base' + id + '" id="total_base' + id +
            '" value="0.00" readonly></td><td><button id="eliminar' + id +
            '" type="button" onclick="javascript:eliminar_registro(' + id +
            ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;
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
                        console.log(data);
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

    function boton_deuda() {
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var concepto = formulario.concepto.value;
        var fecha = formulario.fecha_hoy.value;
        var total = formulario.total.value;
        var msj = "";
        if (fecha == "") {
            msj += "Por favor, Llene el campo fecha <br/>";
        }
        if (concepto == "") {
            msj += "Por favor, Llene el campo concepto <br/>";
        }
        if (total == "") {
            msj += "Por favor, Llene el campo de rubros <br/>";
        }
        if (msj == "") {
            if ($("#form_guardado").valid()) {
                $.ajax({
                    type: 'post',
                    url: "{{route('saldosinicialesp.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#form_guardado').serialize(),
                    success: function(data) {
                        console.log(data);

                        $("#id_factura").val(data[1]);
                        $("#asiento").val(data[0]);
                        $("#secuencia").val(data[2]);
                        //buscarAsiento(data);
                        $('#form_guardado input').attr('readonly', 'readonly');
                        $("#boton_guardar").attr("disabled", true);
                        swal("Correcto!", "Saldos iniciales se generaron correctamete", "success");

                    },
                    error: function(data) {
                        swal("Error!", data, "error");
                    }
                })
            }


        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }

    function crear_retenciones() {
        var buscar = $("#buscar").val();

        if (buscar != 0) {

            $.ajax({
                type: 'post',
                url: "{{route('retenciones_store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    console.log(data);
                    alert("Retencion guardada correctamente");
                    location.href = "{{route('retenciones_index')}}";
                },
                error: function(data) {
                    console.log(data);
                }
            })
        } else {
            $("#buscar").next().remove();
            $("#buscar").after('<span class="validationMessage" style="color:red;">Inserte la serie en la factura</span>');
            alert("Existen campos vacios en la retención");
        }

    }
</script>


@endsection