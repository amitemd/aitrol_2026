@extends('contable.comp_egreso.base')
@section('action-content')
@php
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
@endphp
<style type="text/css">
    .ui-autocomplete
        {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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
         .alerta_correcto{
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
        .container:hover input ~ .checkmark {
        background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .container input:checked ~ .checkmark {
        background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        }

        /* Show the checkmark when checked */
        .container input:checked ~ .checkmark:after {
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
          color:  white;
          padding: 10px;
          background-color: green;
          font-size:15px;
          font-family:helvetica;
          font-weight:bold;
          text-transform:uppercase;
        }
        .parpadea {
          
          animation-name: parpadeo;
          animation-duration: 0.5s;
          animation-timing-function: linear;
          animation-iteration-count: infinite;

          -webkit-animation-name:parpadeo;
          -webkit-animation-duration: 4s;
          -webkit-animation-timing-function: linear;
          -webkit-animation-iteration-count: infinite;
        }

        @-moz-keyframes parpadeo{  
          0% { opacity: 1.0; }
          50% { opacity: 0.5; }
          100% { opacity: 1.0; }
        }

        @-webkit-keyframes parpadeo {  
          0% { opacity: 1.0; }
          50% { opacity: 0.5; }
           100% { opacity: 1.0; }
        }

        @keyframes parpadeo {  
          0% { opacity: 1.0; }
           50% { opacity: 0.5; }
          100% { opacity: 1.0; }
        }
  

</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('acreedores_cegreso') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('acreedores_cegreso')}}">Comprobante de Egreso</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Comprobante de Egreso Anulado</li>
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
                        <div class="col-md-4 col-sm-9 col-6">
                            <div class="box-title "><b>COMP. DE EGRESOS ACREEDORES</b></div>
                        </div>
                          <div class="col-md-4 text-left">
                                    <span class="parpadea text" id="boton" >{{$h}}</span>
                                </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" id="botong" class="btn btn-success btn-gray" onclick="guardar()" >
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </button>
                                <button type="button" class="btn btn-success btn-gray" onclick="nuevo_comprobante()" >
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <button type="button" class="btn btn-success btn-gray" onclick="goBack()" >
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
                            <div class=" col-md-2 px-1">
                                <label class="label_header">Estado</label>
                                <div id="estado_back" style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" readonly>


                            </div>

                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" required value="{{date('Y-m-d')}}">
                                <input type="hidden" name="total_favor" id="total_favor">

                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-10 px-0">
                                <label class="label_header" for="acreedor">Concepto:</label>
                                <input class="form-control  col-md-12" type="text" name="aaa" autocomplete="off" id="aaa" required>
                            </div>
                            <div class=" col-md-2 px-0">
                                <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                <label style="margin: 0px; font-size: 17px;" class="container col-md-12">Cheque entregado
                                    <input class="spropety" type="checkbox" id="cheque_entregado" name="cheque_entregado">
                                </label>
                                @if($empresa->id == "09927041520010")
                                <div style="text-align: center;">
                                    <label style="font-size:19px;" class="col-md-12">Rt. Asumidas
                                        <input onchange="retencion_asumidas()" class="spropety" type="checkbox" id="ch_rt_asumidas" name="ch_rt_asumidas">
                                    </label>
                                </div>
                                @endif
                                
                            </div>
                            <div class=" col-md-4  px-0">
                                <label class="col-md-12 label_header" for="ruc">Banco:</label>
                                <select class="form-control " name="banco" id="banco">
                                    <option value="0">Seleccione..</option>
                                    @foreach($banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-0">
                                <label class="col-md-12 label_header" for="secuencia">Divisas:</label>
                                <select class="form-control col-md-12 " name="divisas" id="divisas">
                                    <option value="0">Seleccione...</option>
                                    @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="secuencia">Cambio:</label>
                                <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header control-label" for="numero_cheque">Cheque No:</label>
                                <input class="form-control " type="number" name="numero_cheque" id="numero_cheque">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="fecha_cheque">Fecha Cheque: </label>
                                <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="valor" class="label_header">Valor</label>
                                <input class="form-control " type="text" autocomplete="off" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">

                            </div>
                            <div class=" col-md-3 px-0">
                                {{ csrf_field() }}
                                <input type="hidden" name="superavit" id="superavit" value="0">
                                <input type="hidden" name="comprobarx" id="comprobarx" value="0">
                                <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                <!--<input type="text" id="nombre_proveedor" name="nombre_proveedor" class="form-control form-control-sm nombre_proveedor " onchange="cambiar_nombre_proveedor()">-->
                                <select name="nombre_proveedor" id="nombre_proveedor" class="form-control form-control-sm select2_cuentas" onchange="cambiar_nombre_proveedor(this);">
                                    <option value="">Seleccione</option>
                                    @foreach($proveedor as $value)     
                                    <option value="{{$value->id}}" > {{$value->id}} || {{$value->nombrecomercial}}</option>
                                    @endforeach 
                                </select>
                            </div>  
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="direccion">Dirección: </label>
                                <input class="form-control " type="text" name="direccion" id="direccion" autocomplete="off">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="ruc">RUC:</label>
                                <input class="form-control " type="text" name="id_proveedor" id="id_proveedor" readonly>
                            </div>
                            <div class=" col-md-2">
                                <div class="col-md-12">&nbsp;</div>
                                <button type="button" class="btn btn-success btn-xs btn-gray" id="aplicar_deuda" onclick="boton_deuda();">Aplicar Deuda</button>
                            </div>
                            <div class=" col-md-4  px-1">
                                <label class="col-md-12 label_header" for="girado"> Girado a:</label>
                                <input class="form-control " type="text" name="giradoa" id="giradoa">
                            </div>
                            <div class="col-md-4 px-1">
                                <label for="" class="control-label label_header">Formas de Pago</label>
                                <select name="formas_pago" id="formas_pago" class="form-control">
                                    <option value="">Seleccione..</option>
                                    @foreach($formas_pago as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                            <div style="display:none;" id="contenedor_asumidas" class=" col-md-2 px-1">
                                <label for="valor" class="label_header">Retenciones Asumidas</label>
                                <input onchange="verificar_valor();" class="form-control " type="text" autocomplete="off" name="rt_asumidas" id="rt_asumidas"  onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            
                            <!-- <input type="hidden" name="total_suma" id="total_suma"> -->
                        </div>
                    </div>
                   
                    
                </div>

            </div>
        
    </form>

</section>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#cheque_entregado').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });

    $('.spropety').on('ifChecked', function(event) {
        $("#verificar_cheque").val(1);
    });
    $('.spropety').on('ifUnchecked', function(event) {
        $("#verificar_cheque").val(0);
    });


    function retencion_asumidas(){
        let ch_rt_asumidas = document.getElementById("ch_rt_asumidas").checked;

        let contenedor_asumidas = document.getElementById("contenedor_asumidas");

        let rt_asumidas = document.getElementById("rt_asumidas");


        if(ch_rt_asumidas){
            contenedor_asumidas.style.display= "block";
            
        }else{
            contenedor_asumidas.style.display= "none";
            rt_asumidas.value= "0.00";
        }
    }


    function verificar_valor(){
        let rt_asumidas = document.getElementById("rt_asumidas");
        let valor_cheque = document.getElementById("valor_cheque");

        if(valor_cheque.value == ""){
            swal("Incorrecto!", "Ingrese  el campo valor cheque", "error");
            rt_asumidas.value="0.00";
        }else if(parseFloat(rt_asumidas.value) > parseFloat(valor_cheque.value)) {
            swal("Incorrecto!", "El valor de la retencion no debe ser mayor que el Valor del Cheque", "error");
            rt_asumidas.value="0.00";
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
                //buscar_proveedor();


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

    function buscarAsiento(id_asiento) {
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '7'
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data[0]);
                    $('#numero_factura').val(data[1]);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }

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
                'nombre': eq.value
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#id_proveedor").val(data.value);
                    $("#direccion").val(data.direccion);
                   // buscar_proveedor()
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
    function suma_totales(){
        var total=0;
        $('.abonos').each(function(i,obj){
            console.log($(this).val());
            total+= parseFloat($(this).val());
           
        });
        $("#total_suma").val(total.toFixed(2,2));
        var cheque= $("#valor_cheque").val();
        var restante= cheque-total;
        $("#restante").val(restante.toFixed(2,2));
    }
    function _guardar() {
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque = formulario.valor_cheque.value;
        var acreedores = formulario.id_proveedor.value;
        var fecha= formulario.fecha_hoy.value;
        var formas_pago = formulario.formas_pago.value;
        var banco = formulario.banco.value;
        var superavit = parseInt($("#verificar_superavit").val());
        var msj = "";
        if (valor_cheque == "") {
            msj += "Por favor, Llene el valor del cheque<br/>";
        }
        if (acreedores == "") {
            msj += "Por favor, Llene el campo de acreedor<br/>";
        }
        if (formas_pago == "") {
            msj += "Por favor, Llene las formas de pago <br/>";
        }
        if (banco == "") {
            msj += "Por favor, Llene el banco <br/>";
        }
        if (fecha == "") {
            msj += "Por favor, Llene la fecha <br/>";
        }
        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = parseFloat($("#valor_cheque").val());
        var total_suma = parseFloat($("#total_suma").val());
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        if (msj == "") {
            $('#botong').attr('disabled', 'disabled');
            //console.log(superavit);
            if (superavit == 1) {
                if (total_suma < final_valor_cheque) {
                    var resta_superavit = parseFloat(final_valor_cheque - total_suma);
                    var resta_fixed = resta_superavit.toFixed(2, 2);
                    $("#total_favor").val(resta_superavit);
                    if (confirm('Existe un superávit de ' + resta_fixed + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?')) {
                        $("#total_favor").val(resta_superavit);
                        $("#superavit").val(0);
                        $("#comprobarx").val(1);
                        //console.log("entro nuevo");
                        if ($("#form_guardado").valid()) {
                            $.ajax({
                                type: 'post',
                                url: "{{route('acreedores_cstore')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                datatype: 'json',
                                data: $('#form_guardado').serialize(),
                                success: function(data) {
                                    console.log(data)
                                    if ((data) != 'false') {
                                        $("#id_factura").val(data);
                                        buscarAsiento(data);
                                        swal("Correcto!", "Los comprobantes se generaron con exito", "success");
                                        document.getElementById("aplicar_deuda").disabled = true;
                                        $('#form_guardado input').attr('readonly', 'readonly');
                                        $('#botong').attr('disabled', 'disabled');
                                        //url = "{{ url('contable/compra/comprobante/egreso/pdf/')}}/" + data;
                                        //window.open(url, '_blank');
                                    } else {

                                    }
                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            })
                        }

                    } else {

                    }
                   
                } else {
                    $('#botong').attr('disabled', 'disabled');
                    console.log("entro store");
                    if ($("#form_guardado").valid()) {
                        $.ajax({
                            type: 'post',
                            url: "{{route('acreedores_cstore')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),
                            success: function(data) {
                                console.log(data);
                                if ((data) != 'false') {
                                    $("#id_factura").val(data);
                                    buscarAsiento(data);
                                    swal("Correcto!", "Los comprobantes se generaron con exito", "success");
                                    document.getElementById("aplicar_deuda").disabled = true;
                                    $('#form_guardado input').attr('readonly', 'readonly');
                                    $('#botong').attr('disabled', 'disabled');
                                   // url = "{{ url('contable/compra/comprobante/egreso/pdf/')}}/" + data;
                                   // window.open(url, '_blank');
                                } else {

                                }
                            },
                            error: function(data) {
                                console.log(data);
                            }
                        })
                    }

                }

            } else {
                if (confirm('Existe un superávit de ' + final_valor_cheque + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?')) {
                    $("#superavit").val(1);
                    $("#total_favor").val(final_valor_cheque);
                    if ($("#form_guardado").valid()) {
                        $.ajax({
                            type: 'post',
                            url: "{{route('acreedores_cstore')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: $('#form_guardado').serialize(),
                            success: function(data) {

                                if ((data) != 'false') {
                                    buscarAsiento(data);
                                    $("#id_factura").val(data);
                                    $('#form_guardado input').attr('readonly', 'readonly');
                                    $('#botong').attr('disabled', 'disabled');
                                    //document.getElementById("botong").disabled = true;
                                    swal("Correcto!", "El anticipo a proveedor se generó con exito", "success");
                                    document.getElementById("aplicar_deuda").disabled = true;
                                } else {

                                }
                            },
                            error: function(data) {
                                console.log(data);

                            }
                        })
                    }

                } else {}
            }
        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }



    function guardar() {
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque = formulario.valor_cheque.value;
        var acreedores = formulario.id_proveedor.value;
        var fecha= formulario.fecha_hoy.value;
        var formas_pago = formulario.formas_pago.value;
        var banco = formulario.banco.value;
        var superavit = parseInt($("#verificar_superavit").val());
        var msj = "";
        if (valor_cheque == "") {
            msj += "Por favor, Llene el valor del cheque<br/>";
        }
        if (acreedores == "") {
            msj += "Por favor, Llene el campo de acreedor<br/>";
        }
        if (formas_pago == "") {
            msj += "Por favor, Llene las formas de pago <br/>";
        }
        if (banco == "") {
            msj += "Por favor, Llene el banco <br/>";
        }
        if (fecha == "") {
            msj += "Por favor, Llene la fecha <br/>";
        }
        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = parseFloat($("#valor_cheque").val());
        var total_suma = parseFloat($("#total_suma").val());
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        if (msj == "") {
            $('#botong').attr('disabled', 'disabled');
            //console.log(superavit);
            //if (superavit == 1) {//
               // if (total_suma < final_valor_cheque) {
                    var resta_superavit = parseFloat(final_valor_cheque - total_suma);
                    var resta_fixed = resta_superavit.toFixed(2, 2);
                    $("#total_favor").val(resta_superavit);
                   // if (confirm('Existe un superávit de ' + resta_fixed + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?')) {
                        // $("#total_favor").val(resta_superavit);
                        // $("#superavit").val(0);
                        // $("#comprobarx").val(1);
                        //console.log("entro nuevo");
                        //if ($("#form_guardado").valid()) {
                            $.ajax({
                                type: 'post',
                                url: "{{route('compra.egreso_anulado_store')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                datatype: 'json',
                                data: $('#form_guardado').serialize(),
                                success: function(data) {
                                    console.log(data)
                                        
                                    
                                    if(data.id_egreso !="" || data.id_egreso !=null){
                                          swal("Correcto!", "Los comprobantes se generaron con exito", "success");
                                          document.getElementById('id_factura').value= data.id_egreso;      
                                          document.getElementById('estado_back').style.background = 'yellow'
                                          document.getElementById('numero_factura').value=data.secuencia;
                                    }else{
                                          swal("Error...", "Ocurrio un error", "error");
                                    }
                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            })
                       // }

                    //} 
                   
                //} 

            //} 
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
        var valor_saldo = parseFloat($("#saldo0").val());
        var contador = parseInt($("#contador").val());
        var saldo = 0
        var abono = 0;
        var total = 0;
        var cero = 0;
        var nuevo_s = 0;
        for (i = 0; i <= contador; i++) {
            saldo += parseFloat($("#saldo" + i).val());
            valor_saldo = parseFloat($("#saldo" + i).val());
            valor -= valor_saldo;
            var cont = parseFloat($("#abono" + i).val());
            if (isNaN(cont)) {
                cont = 0;
            }
            abono += cont;
            console.log(valor);
            if (valor > valor_saldo) {

                $("#abono" + i).val(valor_saldo.toFixed(2, 2));
                $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
            } else {
                total = valor + valor_saldo;
                //console.log(total + " anthonby");
                if (total <= valor2) {
                    if (total > 0) {
                        //console.log("entra");
                        if (total > valor_saldo) {
                            total = valor_saldo;
                            $("#abono" + i).val(total.toFixed(2, 2));
                            $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
                        } else {
                            $("#abono" + i).val(total.toFixed(2, 2));
                            var saldo = parseFloat($('#saldo' + i).val()) - total;
                            $("#nuevo_saldo" + i).val(saldo.toFixed(2, 2));
                        }

                    } else {

                    }
                }



            }
            console.log("veces");
        }

        var total = 0;
        if (!isNaN(valor) && !isNaN(valor_saldo)) {
            /*
            if(valor_saldo<=valor){
                total= valor-valor_saldo;
                $("#verificar_superavit").val(1);
                $("#abono0").val(valor_saldo.toFixed(2,2));
            }else{
                total= valor_saldo-valor;
                $("#valor_cheque").val(total.toFixed(2,2));
                $("#verificar_superavit").val(1);
                $("#abono0").val(valor.toFixed(2,2));
            }*/
            $("#verificar_superavit").val(1);
            suma_totales();

        } else {
            swal("Error!", "Ingrese valor de cheque primero", "error");
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
            location.reload(true);
    }

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
                'tipo': tipo
            },
            success: function(data) {
                if (data.value != "no resultados") {
                    $("#crear").empty();
                    var fila = 0;
                    for (i = 0; i < data[5].length; i++) {

                        if (data[5][i].tipo == 1) {
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FA', data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else if (data[5][i].tipo == 2) {
                            var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FACT', data[5][i].numero, data[5][i].proveedor + " " + data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else {
                            var row = addNewRowf(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'SALDO INICIAL', data[5][i].numero, data[5][i].observacion, data[5][i].valor_nuevo, data[5][i].id);
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
            var valor = parseFloat($("#valor_cheque").val());
            var abono = parseFloat($("#abono" + id).val());
            var saldo = parseFloat($("#saldo" + id).val());
            suma_totales();
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
                    swal("¡Error!", "Error no puede superar al valor del cheque", "error")
                    suma_totales();
                }
            } else {
                abono = 0;
                valor = 0;
                $("#abono" + id).val(valor.toFixed(2, 2));
            }
        } else {
            alert("error");
        }
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

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text'  id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "> <input class='form-control' type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text'  id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='Fact #:" + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
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
      document.getElementById("fecha_hoy").addEventListener('change',function(){
        @if($empresa->id != "0992704152001") //id_empresa de gastroclinica
        $fechaAc = new Date();
        $mes = $fechaAc.getUTCMonth() + 1;
        var d = new Date( this.value );
        var month = d.getMonth()+1;

        var ty = document.getElementById("fechita").value;
        console.log(ty);
       if($mes != month){


         swal("¡Error!","Fecha fuera del periodo ","error")
         document.getElementById("fecha_hoy").value = ty;
         //location.reload();
       }
       @endif
        
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