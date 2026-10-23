@extends('contable.compra.base')
@section('action-content')
<style>
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

    .ui-autocomplete {
        z-index: 5000;
    }

    .ui-autocomplete {
        z-index: 999999;
        list-style: none;
        background-color: #FFFFFF;
        width: 300px;
        border: solid 1px #EEE;
        border-radius: 5px;
        padding-left: 10px;
        line-height: 2em;
    }

    .swal-wide {
        width: 250px !important;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Ventana modal editar -->
<div class="modal fade" id="modal_devoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Compras')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.comprassri')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('contableM.COMPRA')}}</h3>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.ingresarclave')}}</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <div class="col-md-12">
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="id">{{trans('contableM.claveacceso')}}: </label>
                </div>
                <div class="form-group col-md-6 col-xs-6">
                    <input class="form-control" type="text" id="claveAcceso" name="claveAcceso" value="" placeholder="Clave de acceso de 49 dígitos" />
                </div>
                <div class="col-md-4">
                    <button type="button" id="btnbuscar" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                </div>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">{{trans('contableM.compra')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="contenedor"></div>
                </div>
                <div class="col-3" style="text-align: center;">
                    <div class="row">
                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                        </a>
                        <a class="btn btn-success btn-gray" href="javascript:reprocesar()" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Reprocesar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#btnbuscar').click(function() {
            $('#contenedor').html('');
            if ($('#claveAcceso').val() != '') {
                if ($('#claveAcceso').val().length < 49 || $('#claveAcceso').val().length > 49) {
                    alertas("warning", "Atención!", "La calve de acceso debe contener 49 dígitos");
                } else {
                    $.ajax({
                        url: '{{route("traercomprobante_sri")}}',
                        type: 'post',
                        data: {
                            _token: $('input[name=_token]').val(),
                            claveacceso: $('#claveAcceso').val()
                        },
                        dataType: 'html',
                        success: function(html) {
                            if (html == 'No existe comprobante o aun no ha sido autorizado...' || html == '')
                                if (html == '')
                                    alertas('info', '', 'No existe comprobante o aun no ha sido autorizado...');
                                else
                                    alertas('info', '', html);
                            else
                                $('#contenedor').html(html);
                        }
                    });
                }

            } else {
                alertas("warning", "Atención!", "Ingrese la clave de acceso...");
            }
        });
    });

    function reprocesar() {
        $.ajax({
            url: '../api_doc_electronico?opcion=leerxml&clave=' + $('#claveAcceso').val(),
            type: 'get',
            dataType: 'json',
            success: function(json) {
                console.log(json);
            }
        });
    }
    const cargarSucursal = (e) => {
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': e.value
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#p_emision").empty();
                        $.each(data, function(key, registro) {
                            var select = '';
                            if (registro.codigo_caja == '100')
                                select = 'selected';
                            $("#p_emision").append('<option value=' + registro.codigo_caja + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#p_emision").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function asignarCodigo(num) {
        if ($('#nombre_' + num).val() != '')
            $('#codigo_' + num).val($('#nombre_' + num).val());
    }

    function calcular() {
        var cantidad = 0;
        var precio = 0;
        var total_fila = 0;
        var descuento = 0;
        var subtotal0 = 0;
        var subtotal12 = 0;
        var pordescuento = 0;
        var totalsindescuento = 0;
        var descuentoTotal = 0;
        var iva = 0;
        var totalIva = 0;
        try {
            $("#crear_factura").find(':input').each(function() {
                var elemento = this;
                if (/cantidad_/.test(elemento.id) || /precio_/.test(elemento.id) || /total_/.test(elemento.id) || /descuento_/.test(elemento.id)) {
                    if (/precio_/.test(elemento.id)) {
                        if ($(elemento).val() != '') {
                            var to = elemento.id.replace('precio_', '');
                            cantidad = $('#cantidad_' + to).val();
                            descuento = parseFloat($('#descuento_' + to).val());
                            precio = $('#precio_' + to).val();
                            iva = $('#iva_' + to).val();
                            totalIva += parseFloat(iva);
                            total_fila = (parseFloat(cantidad) * parseFloat(precio)) - parseFloat(descuento);
                            totalsindescuento = (parseFloat(cantidad) * parseFloat(precio));
                            if (descuento > 0) {
                                pordescuento = (descuento * 100) / totalsindescuento;
                            }
                            descuentoTotal += descuento;
                            $('#pordescuento_' + to).val(pordescuento);
                            $('#total_' + to).val(total_fila);
                            if ($('#iva_' + to).val() > 0) {
                                subtotal12 += total_fila;
                            } else {
                                subtotal0 += total_fila;
                            }
                        }
                    }
                }
            });
            $('#subtotal_12').val(subtotal12);
            $('#subtotal_0').val(subtotal0);
            $('#subtotal_12_').html(parseFloat(subtotal12).toFixed(2));
            $('#subtotal_0_').html(parseFloat(subtotal0).toFixed(2));
            $('#descuentoTotal').val(descuentoTotal);
            $('#descuentoTotal_').html(parseFloat(descuentoTotal).toFixed(2));
            var base = parseFloat(subtotal0) + parseFloat(subtotal12);
            $('#base').html(parseFloat(base).toFixed(2));
            $('#base_').val(base);
            $('#tarifa_iva').html(parseFloat(totalIva).toFixed(2));
            $('#tarifa_iva_').val(totalIva);
            var totalPagar = parseFloat(base) + parseFloat(totalIva);
            $('#totalPagar').html(parseFloat(totalPagar).toFixed(2));
            $('#totalPagar_').val(totalPagar);
        } catch (error) {
            console.log(error);
        }
    }

    function cambioTipo(e) {

        if (e.value == '03') {
            $("#nombre_proveedor").val("").trigger("change");
            $("#pedido_nombre").val("").trigger("change");

            $.ajax({
                type: 'post',
                url: "{{ route('compras.buscador_master_tipos') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': e.value
                },
                success: function(data) {
                    if (data) {
                        $('#nombre_proveedor').empty();
                        let newOption = [];
                        for (let index = 0; index < data.length; index++) {
                            newOption.push(new Option(data[index].razonsocial, data[index].id, false, false));
                        }
                        newOption.unshift(new Option('Seleccione', '', false, false));
                        $('#nombre_proveedor').append(newOption).trigger('change');
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }
    }
    const sumarFecha = (e) => {

        let day = e.options[e.selectedIndex].text;
        let date = new Date(document.getElementById("fecha").value);
        let subday = day.substring(0, 2).trim();
        if (Number.isInteger(parseInt(subday))) {
            date.setDate(date.getDate() + parseInt(subday));
            let fechaImp = date.toISOString().slice(0, 10);
            console.log(fechaImp);
            document.getElementById("f_caducidad").value = fechaImp;
            document.getElementById("f_caducidad").readOnly = true;
        } else {
            document.getElementById("f_caducidad").value = date.toISOString().slice(0, 10);
            document.getElementById("f_caducidad").readOnly = false;
        }
    }

    function validarFormulario() {
        return true;
    }

    function crear_compra() {
        let boton = document.getElementById('boton_guardar');
        boton.style.display = "none"
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var observacion = $("#observacion").val();
        var proveedor = $("#proveedor").val();
        var op_compra = $("#o_compra").val();
        var fecha_caducidad = $("#f_caducidad").val();
        var credito_tributario = $("#credito_tributario").val();
        var tipo_comprobante = $("#tipo_comprobante").val();
        var secuencia_factura = $("#secuencia_factura").val();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        //var credito_tributario = formulario.credito_tributario.value;
        //var proveedor = formulario.proveedor.value;
        var termino = formulario.termino.value;
        //var secuencia = formulario.secuencia_factura.value;
        var secuencia = $("#secuencia_factura").val();
        var nro_autorizacion = formulario.autorizacion.value;
        //var tipo_comprobante = formulario.tipo_comprobante.value;
        var fecha = formulario.fecha.value;
        var f_caducidad = formulario.f_caducidad.value;
        //var secuencia_factura = formulario.secuencia_factura.value;
        var serie = formulario.serie.value;
        var totaliva = $("#tarifa_iva1").val();
        var msj = "";
        if (observacion == "") {
            msj += "Por favor, Llene el campo concepto<br/>";
        }
        if (termino == "") {
            msj += "Por favor, Llene el término <br/>";
        }
        if (credito_tributario == "") {
            msj += "Ingrese credito tributario<br/>";
        }
        if (secuencia == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene el campo de secuencia<br/>";
        }
        if (tipo_comprobante == "") {
            msj += "Por favor, Llene el campo de tipo de comprobante<br/>";
        }
        if (fecha == "") {
            msj += "Por favor, Llene la fecha de la factura<br/>";
        }
        if (f_caducidad == "") {
            msj += "Por favor, Llene la fecha de caducidad de la factura<br/>";
        }
        if (secuencia_factura == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene la secuencia de la factura<br/>";
        }
        if (serie == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene la serie de la factura<br/>";
        }
        if (nro_autorizacion == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene campo de número de autorización<br/>";
        }
        if ((nro_autorizacion.length != 10 && nro_autorizacion.length != 49) && tipo_comprobante != '03') {
            msj += "Por favor, El n{umero de autorización debe contener 10 o 49 dígitos<br/>";
        }
        if ($('#sucursal').val() == '' && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione el la sucursal del la empresa<br/>";
        }
        if ($('#p_emision').val() == '' && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione el punto de emision de la empresa<br/>";
        }
        let obsDetalle = document.getElementsByName("descrip_prod[]");
        let obsBodega = document.getElementsByName("bodega[]");
        let array = [];
        let contador = obsDetalle.length;
        for (let index = 0; index < contador; index++) {
            /*if (tipo_comprobante == '03') {
                if(obsDetalle[index].value == ''){
                    array.push(' Ingrese el detalle del producto '+Number(index+1));
                }
            }*/
            if (tipo_comprobante == '03') {
                if (obsBodega[index].value == '') {
                    array.push(' Ingrese la bodega ' + Number(index + 1));
                }
            }
        }
        if (array.length > 0) {
            msj += array.join(' ');
        }
        if (msj != "") {
            boton.style.display = "initial"
        }
        if (msj == "") {
            if (validarFormulario() == true) {
                $.ajax({
                    type: 'post',
                    url: "{{route('comprasri_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {
                        console.log(data.errores);
                        if (data != '¡Error!, Coincidencia en las Facturas, ingrese otra factura con otros valores') {
                            if (data.errores == 'si') {
                                boton.style.display = "initial";
                                swal({
                                    title: "Error!",
                                    type: "error",
                                    html: data.msj
                                });
                                return;
                            }
                            //$("#boton_guardar").attr("disabled", "disabled");
                            if (confirm('¿Desea agregar retenciones?')) {
                                $.ajax({
                                    type: "get",
                                    url: "{{route('retenciones_modal_retenciones')}}",
                                    data: {
                                        'id_proveedor': proveedor,
                                        'secuencia': secuencia_factura,
                                        'id_compra': data[0],
                                        'total_iva': totaliva
                                    },
                                    datatype: "html",
                                    success: function(datahtml, data) {
                                        //console.log(data);
                                        $("#content").html(datahtml);
                                        $("#calendarModal").modal("show");
                                        $('#crear_factura input').attr('readonly', 'readonly');
                                        // $("#boton_guardar").attr("disabled", "disabled");
                                    },
                                    error: function() {
                                        alert('error al cargar');
                                    }
                                });
                            } else {
                                swal("Correcto!", "Factura guardada correctamente", "success");
                                $("#id_fc").val(data.id);
                                $("#id_asiento").val(data.id_asiento);
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }
                        } else {
                            swal("Informe", data, "error");
                        }
                    },
                    error: function(data) {
                        boton.style.display = "block"
                        console.log(data);
                        swal("Error!", data, "error");
                    }
                });
            } else {
                boton.style.display = "initial";
            }

        } else {
            //$('#boton_guardar').removeAttr("disabled");
            boton.style.display = "initial";
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function verificatotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales == valor_totalfinal) {
                return 'ok';
            } else {
                return false;
            }
        }
    }
</script>
@endsection