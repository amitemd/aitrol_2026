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

    .select2-container {
        width: 100% !important;
    }

    .label_header tr th {
        text-align: center;
        border: none !important;
        margin: 0 !important;
    }

    .abonos {
        background-color: #c9ffe5;
        width: 93%;
        border: 1px solid gray;
        font-weight: bold;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('creditoacreedores.index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('creditoacreedores.index') }}">{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.nuevo')}} {{trans('contableM.notacredito')}}</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}

        <input name="id_comp" id="id_comp" type="text" class="hidden" value="">

        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-9 col-6">
                            <div class="box-title "><b>{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</b></div>
                        </div>
                        <div class="col-md-6 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" id="boton_guardar" onclick="boton_deuda(event)" class="btn btn-success"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}</button>
                                <button type="button" class="btn btn-info" onclick="window.location.reload();" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <button type="button" class="btn btn-danger" onclick="goBack()" style="margin-left: 10px;">
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

                            <div class=" col-md-1 px-1">
                                <label class="label_header">{{trans('proforma.estado')}}</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>

                            </div>
                            <div class=" col-md-1 px-1">

                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="ACR-NC" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" readonly>
                                

                            </div>

                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_caducidad">{{trans('contableM.fechacaducidad')}}: </label>
                                <input class="form-control " type="date" name="fecha_caducidad" id="fecha_caducidad" value="{{date('Y-m-d')}}">

                            </div>
                        </div>

                        <div class=" col-md-4 px-1">
                            {{ csrf_field() }}
                            <input type="hidden" name="superavit" id="superavit" value="0">
                            <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.acreedor')}} </label>
                            <select class="form-control select2_proveedor validar" onchange="buscarFacturas();" style="width: 100%;" name="proveedor" id="proveedor">
                                <option value="">{{trans('proforma.seleccion')}}...</option>
                            </select>
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="control-label label_header" for="autorizacion">{{trans('contableM.autorizacion')}} </label>
                            <input type="text" name="autorizacion" id="autorizacion" class="form-control validar">
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="col-md-12 label_header control-label" for="serie">{{trans('contableM.serie')}} NC</label>
                            <input type="text" class="form-control validar" id="serie" name="serie" onkeyup="agregar_serie()">
                        </div>
                        <div class="col-md-2 px-1">
                            <label for="secuencia" class="label_header col-md-12 control-label">{{trans('contableM.secuencia')}} NC</label>
                            <input type="text" class="form-control validar" id="secuencia" name="secuencia" onchange="ingresar_cero();">
                        </div>
                        <div class=" col-md-2 px-1">
                            <label class="col-md-12 label_header" for="fechand">{{trans('contableM.FechaNC')}} </label>
                            <input class="form-control " type="date" name="fechand" id="fechand" value="{{date('Y-m-d')}}">
                        </div>

                        <div class=" col-md-6 px-1">
                            <label class="col-md-12 label_header" for="nro_factura"># {{trans('contableM.factura')}} </label>
                            <select class="form-control select2 validar" name="nro_factura" id="nro_factura" onchange="buscarDetalleFactura()">

                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="control-label label_header">{{trans('contableM.tipocomprobante')}}</label>
                            <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas " style="width: 100%;">
                                <option value="">{{trans('proforma.seleccion')}}...</option>
                               
                            </select>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="control-label label_header">{{trans('contableM.creditotributario')}}</label>
                            <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas" style="width: 100%; ">
                                <option value="">{{trans('proforma.seleccion')}}...</option>

                            </select>
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="col-md-12 label_header" for="nro_factura"> {{trans('contableM.total')}}{{trans('contableM.factura')}} </label>
                            <input class="form-control " type="text" name="total_factura" id="total_factura" autocomplete="off" value="0.00">
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.valorcontable')}}: </label>
                            <input class="form-control " type="text" name="val_contable" id="val_contable" autocomplete="off" value="0.00">
                        </div>

                        <!-- <div class="col-md-2 px-1">
                            <label class="col-md-12 label_header" for="nro_factura"{{trans('contableM.total')}}Abono: </label>
                            <input class="form-control " type="text" name="val_contable" id="val_contable" autocomplete="off" placeholder="0.00">
                        </div> -->

                        <div class="col-md-12 px-1">
                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                            <input class="form-control  col-md-12" type="text" name="concepto" id="concepto">
                        </div>

                    </div>

                    <div class="col-md-12">
                        <label class="label_header" for="detalle_deuda">{{trans('contableM.detallefactura')}}</label>
                    </div>

                    <div class="col-md-12">
                        <table style="width:100%" class="table-bordered table-hover dataTable table-striped">
                            <thead class="label_header">
                                <tr>
                                    <th style="width: 40%;">{{trans('contableM.producto')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.cantidad')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.precio')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.descuento')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.impuesto')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.precioneto')}}</th>
                                    <th style="width: 10%;">{{trans('contableM.abono')}}</th>
                                    <th style="width: 10%;">Check Iva</th>
                                </tr>
                            </thead>
                            <tbody id="details">

                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <input type="hidden" name="contador" id="contador" value="0">

                        <div class="col-md-12">
                            <div class="row sumas">
                                <div class="col-md-2">
                                    &nbsp;
                                </div>
                                <!--aqui ando-->
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="subtotal">{{trans('contableM.subtotal0')}}</label>
                                    <input class="form-control  col-md-12" type="text" name="subtotal0" id="subtotal0">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="subtotal">{{trans('contableM.subtotal12')}}</label>
                                    <input class="form-control col-md-12" type="text" name="subtotal12" id="subtotal12">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="subtotal">{{trans('contableM.subtotal')}}</label>
                                    <input class="form-control  col-md-12" type="text" name="subtotal" id="subtotal">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="impuesto">{{trans('contableM.impuesto')}}</label>
                                    <input class="form-control  col-md-12" type="text" onchange="sumar_impuesto()" name="impuesto" id="impuesto">
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header" for="total">{{trans('contableM.total')}}</label>
                                    <input class="form-control  col-md-12" type="text" name="total" id="total">
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: black;" for="nota">{{trans('contableM.nota')}}: </label>
                            <textarea class="col-md-12 validar" name="nota" id="nota" cols="200" rows="5"></textarea>
                            <input type="hidden" name="saldo_final" id="saldo_final">
                        </div>
                    </div>

                </div>

            </div>
    </form>



</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>



<script type="text/javascript">
    const buscarFacturas = () => {
        let id_proveedor = document.getElementById('proveedor').value;
        let nro_factura = document.getElementById('nro_factura');

        $.ajax({
            type: 'get',
            url: `{{route('compra.notacredito.buscarFacturas')}}`,
            data: {
                'id_proveedor': id_proveedor
            },
            datatype: 'json',
            success: function(data) {
                $("#nro_factura").empty();
                $('#nro_factura').append(data.option);
                $("#details").empty();
            }
        })
    }

    const buscarDetalleFactura = () => {
        let id_compra = document.getElementById('nro_factura').value;
        $.ajax({
            type: 'get',
            url: `{{route('compra.notacredito.buscarDetalleFacturas')}}`,
            data: {
                'id_compra': id_compra
            },
            datatype: 'json',
            success: function(data) {
                $("#tipo_comprobante option[value=" + data.compra.tipo_comprobante + "]").attr("selected", true);
                $("#cred_tributario option[value=" + data.compra.credito_tributario + "]").attr("selected", true);

                document.getElementById('total_factura').value = data.compra.total_final;
                document.getElementById('val_contable').value = data.compra.valor_contable;

                $("#details").empty();
                $('#details').append(data.data.table);
                sumaGlobal();
            }
        })
    }

    const checkProducto = id => {
        let verificar = document.getElementById('verificar' + id);
        let textArea = document.getElementById('textArea' + id);
        let check = document.getElementById('check' + id);
        if (check.checked) {
            verificar.value = 1;
            textArea.style.display = 'initial';
        } else {
            verificar.value = 0;
            textArea.style.display = 'none';
        }
        sumaGlobal()
    }


    const sumaGlobal = () => {
        let iva = document.querySelectorAll('.iva')
        let valor_iva = document.querySelectorAll('.iva_producto')
        let descuentos = document.querySelectorAll('.descuento')
        let totalProducto = document.querySelectorAll('.totalProducto')
        let checks = document.querySelectorAll('.checkProducto');
        let abonos = document.querySelectorAll('.abonos');
        let porcentaje = document.querySelectorAll('.porcentaje')


        let subtotal0 = 0;
        let subtotal12 = 0;
        let impuesto = 0;
        let descuento = 0;


        let mostrarSubtotal0 = document.getElementById('subtotal0')
        let mostrarSubtotal12 = document.getElementById('subtotal12')
        let mostrarSubtotal = document.getElementById('subtotal')
        let mostrarImpuesto = document.getElementById('impuesto')
        let mostrarDescuento = document.getElementById('descuento')
        let mostrarTotal = document.getElementById('total')

        for (let i = 0; i < checks.length; i++) {
            if (parseFloat(abonos[i].value) > 0) {
                if (iva[i].value == 1) {
                    // impuesto += parseFloat(valor_iva[i].value);
                    // subtotal12 += parseFloat(totalProducto[i].value);
                    subtotal12 += parseFloat(abonos[i].value);

                    impuesto += (parseFloat(abonos[i].value) * parseFloat(porcentaje[i].value));

                } else {
                    subtotal0 += parseFloat(abonos[i].value);
                }
                //descuento += parseFloat(descuentos[i].value);
            }
        }


        mostrarSubtotal0.value = subtotal0.toFixed(2);
        mostrarSubtotal12.value = subtotal12.toFixed(2);
        mostrarImpuesto.value = impuesto.toFixed(2);
        mostrarSubtotal.value = (parseFloat(subtotal0) + parseFloat(subtotal12)).toFixed(2);
        mostrarTotal.value = (parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(impuesto)).toFixed(2);

    }

    const boton_deuda = (e) => {
        let boton = document.getElementById('boton_guardar');
        boton.style.display = 'none';
        e.preventDefault();
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{route('compra.notaCreditoAcreedores.newStore')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    alertas(data.status, (data.status).toUpperCase(), data.msj);
                    if (data.status == 'success') {
                        document.getElementById('id_factura').value = data.id;
                        document.getElementById('asiento').value = data.id_asiento
                        document.getElementById('numero_factura').value = data.secuencia
                    } else {
                        boton.style.display = 'initial';
                    }
                },
                error: function(data) {
                    swal("Error!", data, "error");
                    boton.style.display = 'initial';
                }
            })
        } else {
            boton.style.display = 'initial';
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }

    }

    const cambiosEstadoIva = id => {
        let check = document.getElementById('checkIva' + id).checked;

        if (check) {
            document.querySelector('.ivaProducto' + id).value = '1';
        } else {
            document.querySelector('.ivaProducto' + id).value = '0';
        }

        sumaGlobal();

    }
</script>

<script>
    //funciones Secundarias

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            swal("Error!", `{{trans('proforma.seriecorrectamente')}}`, "error");
        }
    }

    function ingresar_cero(longitud = 10) {
        let id = document.getElementById('secuencia');
        let cero = "";
        let concat = "";
        if (parseInt(id.value) > 0) {
            if (id.value.length < longitud) {
                while (concat.length != longitud) {
                    cero = "0" + cero;
                    concat = cero + id.value;

                }
                id.value = concat;
            } else {
                alertas("Error!", "error", `{{trans('contableM.valorincorrecto')}}`);
                id.value = "";
            }
        } else {
            alertas("Error!", "error", `{{trans('contableM.valorincorrecto')}}`);
            id.value = "";
        }
    }

    const decimal = e => {
        e.value = parseFloat(e.value).toFixed(2);
        if (isNaN(e.value)) {
            e.value = (0).toFixed(2)
        }
    }
    $('.select2').select2({
        tags: false
    });

    $('.select2_proveedor').select2({
        placeholder: "{{trans('proforma.seleccion')}}...",
        allowClear: true,
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

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }
</script>