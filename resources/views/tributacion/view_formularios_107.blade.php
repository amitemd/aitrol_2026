@extends('contable.compra.base')
@section('action-content')
@php
use Sis_medico\De_Empresa;
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
$deEmpresa = De_Empresa::getDatos($empresa->id);

@endphp
<style type="text/css">
    .dropdown-menu a {
        display: block;
        border-radius: 2;
    }

    .button {
        transition-duration: 0.4s;
    }

    .button:hover {
        background-color: red;
        color: white !important;
    }

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
        min-width: 460px;
        _width: 460px !important;
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

    .input-number {
        width: 80%;
        height: 20px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
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
        location.href = "{{ route('compras_index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<div class="modal fade" id="modalpedido" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Tributación</a></li>
            <li class="breadcrumb-item"><a href="#">Lista de formularios 107</a></li>
            <li class="breadcrumb-item"><a href="#">Impuesto a la renta/personas naturales</a></li>
        </ol>
    </nav>
    <div class="box box-solid">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-sm-9 col-6">
                        <div class="box-title"><b>Lista de formularios</b></div>
                    </div>
                    <div class="col-12" style="text-align: right;">
                        <div class="col-md-3">
                            <select class="form-control" id="periodo" name="periodo">
                                <?php
                                if (isset($periodos) && count($periodos) > 0) {
                                    echo '<option value="">--Seleccionar periodo a exportar--</option>';
                                    foreach ($periodos as $row) {
                                        echo '<option value="' . $row->periodo . '">' . $row->periodo . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <!--a href="javascript:;" class="btn btn-info" id="exportarXml"><i class="fa fa-file-code-o" aria-hidden="true"></i> Exportar XML</a>-->
                        <a href="{{route('tributacion.descargar_xml',['periodo'=>'2022'])}}" class="btn btn-info" ><i class="fa fa-file-code-o" aria-hidden="true" ></i> Exportar XML</a>
                        <a href="agregar" class="btn btn-success"><i class="glyphicon glyphicon-save-file" aria-hidden="true"></i> Agregar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="header row">
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Identificación</th>
                                <th>Empleado</th>
                                <th>Fecha entrega</th>
                                <th>Periodo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo">
                            <?php
                            if (isset($datos) && count($datos) > 0) {
                                $count = 0;
                                foreach ($datos as $row) {
                                    $datosFormulario = json_decode($row->datos);
                                    $count++;
                                    $estado = '';
                                    if ($row->estado == 1)
                                        $estado = 'Activo';
                                    else
                                        $estado = 'Inactivo';
                                    echo '<tr>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $count . '</a></td>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $row->identificacion . '</a></td>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $datosFormulario->nombre . '</a></td>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $row->fecha_presentacion . '</a></td>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $row->periodo . '</a></td>
                                    <td><a href="formulario_107?id=' . $row->id . '">' . $estado . '</a></td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</section>
<div class="modal fade bd-example-modal-lg" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header" style="background:#f6b33d -moz-linear-gradient(center top , #f6b33d 5%, #d29105 100%) repeat scroll 0 0;">
                <a class="close" data-dismiss="modal">×</a>
                <h3 id="titulo"></h3>
            </div>
            <div class="modal-body" id="body">
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
    $(document).ready(function() {
        $('#docSri').change(function() {
            $('#cuerpo').html('');
            var inputFileImage = document.getElementById('docSri');
            var file = inputFileImage.files[0];
            var fileSize = $('#docSri')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                $('#estado_firma').html('');
                $('#estado_firma').css({
                    'display': 'none'
                });
                $('#docSri').val('');
                alert('Error: el archivo es muy pesado para ser procesado');
            } else {
                var fd = new FormData();
                fd.append('docSri', file);
                fd.append('tipo_archivo', 'docSri');
                fd.append('_token', $('input[name=_token]').val());
                $.ajax({
                    url: '{{route("docelectronicos.guardarArchivo")}}',
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function(data) {
                        $('#cuerpo').html(data);
                        $('#docSri').val('');
                        calcular();
                    }
                });
            }
        });
        $('#btncerrarModal').click(function() {
            $('#myModal').modal('toggle');
        });
        $('#exportarXml').click(function() {
            if ($('#periodo').val() == '') {
                alert('Seleccione el periodo a exportar');
            } else {
                $.ajax({
                    url: 'exportarXML',
                    type: 'post',
                    data: {
                        _token: $('input[name=_token]').val(),
                        periodo: $('#periodo').val()
                    },
                    datatype: 'json',
                    success: function(json) {
                        console.log(json);
                    }
                });
            }
        });
    });

    function asignarCodigo(num) {
        if ($('#nombre_' + num).val() != '')
            $('#codigo_' + num).val($('#nombre_' + num).val());
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

    function validarFormulario() {
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
        var termino = $("#termino").val();
        var secuencia = $("#secuencia_factura").val();
        var nro_autorizacion = $("#autorizacion").val();
        var fecha = $("#fecha").val();
        var f_caducidad = $("#f_caducidad").val();
        var serie = $("#serie").val();
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
                console.log($('#crear_factura').serialize());
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

    function abrirModal(clave, tipo, comprobante) {
        $('#titulo').html('');
        $('#titulo').html(tipo + ' | ' + comprobante);
        $.ajax({
            url: '{{route("docelectronicos.traedocumento")}}',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#myModal').modal('toggle');
            }
        });

    }
</script>
@endsection