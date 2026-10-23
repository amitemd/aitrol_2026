@extends('contable.comprobante_ingreso_varios.base')
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

.cien {
    width: 98%;

}

.cien2 {
    width: 95%;
}

.cien3 {
    width: 95%;
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
    location.href = "{{route('comprobante_ingreso_v.index')}}";
}
</script>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
            <li class="breadcrumb-item"><a href="{{route('comprobante_ingreso_v.index')}}">Comprobante de Ingreso Varios Clientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Comprobante de ingreso Clientes</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <div class="box-title"><b>COMPROBANTE DE INGRESO VARIOS CLIENTES</b></div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <a class="btn btn-success bloquearicon btn-gray btn-xs" href="javascript:guardar()"
                                    id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk"
                                        aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </a>
                                <button type="button" class="btn btn-success btn-xs btn-gray"
                                    onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;"
                                    href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left"
                                        aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                
                    <div class="header row">
                        <div class="col-md-12">
                            <div class="row  ">
                                <div class="col-md-12">
                                    &nbsp;
                                </div>

                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="label_header" style="padding-left: 0px">Estado</label>
                                    <input class="form-control col-md-12 col-xs-12" style="background-color: green;" readonly>
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="control-label label_header">Id</label>
                                    <input id="idx" type="text" class="form-control" name="idx" readonly>

                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="control-label label_header">Número</label>
                                    <input class="form-control " type="text" name="numero" id="numero" readonly>
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="control-label label_header">Tipo</label>
                                    <input class="form-control " type="text" name="tipo" id="tipo" readonly
                                        value="BAN-IN">
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="control-label label_header">Fecha</label>
                                    <div class="input-group col-md-12">
                                        <input class="col-md-12 col-xs-12 form-control " id="fecha" type="date"
                                            name="fecha" value="{{date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="control-label label_header">Asiento</label>
                                    <input class="form-control " type="text" name="asiento" id="asiento" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row  ">
                                <div class="col-md-12 col-xs-12 px-1">
                                    <label class="col-md-12 label_header" for="valor">Concepto</label>
                                    <input type="text" id="concepto" name="concepto" placeholder="concepto" autocomplete="off"
                                        class="form-control concepto  col-md-12">
                                </div>
                                <div class="col-md-4 col-xs-2 px-0">
                                    <label class="col-md-12 label_header" for="banco">Banco: </label>
                                    <select class="form-control" onchange="banco_informacion()" name="id_banco" id="id_banco">
                                        <option value="">Seleccione...</option> @foreach($banco as $value) <option
                                             @if($value->id==1) selected="selected" @endif value="{{$value->id}}">{{$value->nombre}}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-xs-2 px-0">
                                    <label class="col-md-12 label_header" for="acreedor">&nbsp;</label>
                                    <input class="form-control" type="text" id="datos_banco" autocomplete="off" name="datos_banco">
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="col-md-12 label_header" for="vendedor">Divisas: </label>
                                    <select class="form-control " name="divisas" id="divisas">
                                        <option value="">Seleccione...</option>
                                        @foreach($divisas as $value)
                                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class="col-md-12 label_header" for="cambio">Cambio :</label>
                                    <input class="form-control" type="text" name="cambio" id="cambio" value="1.00" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12 px-1">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE VALORES RECIBIDOS</label>
                    </div>
                   
                        <table id="example3" class="table-responsive px-0 col-md-12" role="grid"
                            aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Tipo</th>
                                    <th style="width: 5%; text-align: center;">Fecha</th>
                                    <th style="width: 5%; text-align: center;">Número</th>
                                    <th style="width: 12%; text-align: center;">Banco</th>
                                    <th style="width: 5%; text-align: center;">Cuenta</th>
                                    <th style="width: 18%; text-align: center;">Girador</th>
                                    <th style="width: 8%; text-align: center;">Valor</th>
                                    <th style="width: 3%; text-align: center;">Valor B</th>
                                    <th style="width: 3%; text-align: center;">
                                        <button onclick="crea_td()" type="button"
                                            class="btn btn-success bloquearicon btn-gray btn-xs">
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
                   
                    <div class="col-md-12" style="top: 20px;">
                        <div class="row">
                            <div class="col-md-1 px-1">
                                &nbsp;
                            </div>
                            <div class="col-md-1 px-1">
                                &nbsp;
                            </div>
                            <div class="col-md-2 px-1">
                                &nbsp;
                            </div>
                            <div class="col-md-2 px-1">
                                &nbsp;
                            </div>
                            <div class="col-md-2 px-1">
                                &nbsp;

                            </div>
                            <div class="col-md-2 px-1">
                                &nbsp;

                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header col-md-12"> TOTAL INGRESOS:</label>
                                <input class="form-control col-md-3" type="text" name="total_ingresos"
                                    id="total_ingresos" class="col-md-12" readonly>

                            </div>
                        </div>
                    </div>

                    <input type="text" name="contador" id="contador" value="0" class="hidden">
                    <input type="hidden" name="total_suma" id="total_suma">
                
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12 px-0">
                    <label for="detalle_deuda" class="control-label label_header">DETALLE DEL COMPLEMENTO
                        CONTABLE</label>
                    <input type="hidden" name="contador_a" id="contador_a" value="0">
                   
                        <table id="example2" class="table-responsive px-0 col-md-12" role="grid"
                            aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="text-align: center;">Código</th>
                                    <th style=" text-align: center;">Divisa</th>
                                    <th style=" text-align: center;">Debe</th>
                                    <th style="text-align: center;">Haber</th>
                                    <th style="text-align: center;">Valor Base</th>
                                    <th style=" text-align: center;">
                                        <button id="busqueda" type="button" class="btn btn-success bloquearicon btn-gray btn-xs">
                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="dt_recibido">
                                @php $cont=0; @endphp
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
         
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="form-row">
                            <div class="form-group col-md-2">

                                <input type="hidden" name="saldo_hidden0" id="saldo_hidden0">
                                <input type="hidden" name="total_egreso" id="total_egreso" value="0">
                            </div>
                            <div class="form-group col-md-2">

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

                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: white;" for="nota">Nota: </label>
                            <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                            <input type="hidden" name="saldo_final" id="saldo_final">
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
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

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
    function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '9'
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
    
    function setNumber(e) {
        if (e == "") {
            e = 0;
        }
        $("#valor_total").val(parseFloat(e).toFixed(2))
        $("#total_ingresos").val(parseFloat(e).toFixed(2))
    }

    function crea_td(contador) {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td><select class="cien form-control tipopago" onchange="cambio_banco(' + id + ')" name="tipo' + id +
            '" style=" width: 100%; " id="tipo' + id +
            '"> <option value="">Seleccione...</option> @foreach($tipos_pagos as $value)  <option value="{{$value->id}}">{{$value->nombre}}</option>  @endforeach </select></td> <td><input class="visibilidad" type="hidden" id="visibilidad' +
            id + '" name="visibilidad' + id + '" value="1"><input name="fecha' + id +
            '" class="cien2 " style="width: 100%; " value="{{date("Y-m-d")}}" type="date" id="fecha' + id +
            '" ></td><td> <input autocomplete="off" style=" width: 100%; " class="cien3 form-control" name="numero_a' + id +
            '" id="numero_a' + id +
            '"> </td><td> <select style=" width: 100%; " class="cien form-control select2" name="banco' + id +
            '" id="banco' + id +
            '"> </select></td><td><input style=" width: 100%; " autocomplete="off" class="cien3 form-control" name="cuenta' +
            id + '" id="cuenta' + id +
            '" ></td><td><input autocomplete="off" class="cien3 form-control" style=" width: 100%; "  type="text" id="girador' + id +
            '" name="girador' + id +
            '"></td><td> <input style=" width: 100%; " class="cien3 form-control" type="text" name="valor' + id +
            '" onchange="validar_td(' + id + ')"  id="valor' + id +
            '" autocomplete="off" ><td> <input style="width: 100%; " class="cien3 form-control" type="text" name="valor_base1' +
            id + '" id="valor_base1' + id + '" value="0.00" disabled> </td><td><button id="eliminar2' + id +
            '" type="button" onclick="javascript:eliminar_registro(' + id +
            ')" class="btn btn-danger bloquearicon btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        llenar_girador(id); 
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;
        
        $('.select2').select2({
            tags: false
        });
    }

    function valor_nuevo(id) {

    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    $('#busqueda').click(function(event) {

        id = document.getElementById('contador_a').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id", "dato_" + id);

        midiv.innerHTML =
            '<td><select class="form-control select2" name="codigo'+id+'" id="codigo'+id+'" required> <option value="">Seleccione</option> @foreach($plan_cuentas as $val) <option value="{{$val->id}}">{{$val->plan}} | {{$val->nombre}} </option> @endforeach </select> <input  type="hidden" id="visibilidads' + id + '" name="visibilidads' + id +
            '" value="1"</td><td><div> <select class="form-control" style=" width: 100%; height: 80%;" name="divisas' +
            id + '" id="divisas' + id +
            '" > <option value="">Seleccione...</option> @foreach($divisas as $value) <option value="{{$value->id}}">{{$value->descripcion}}</option> @endforeach  </select></div></td><td><div><input class="form-control debe" style="width: 100%; height: 80%;"   type="text" name="debe' +
            id + '" id="debe' + id +
            '" value="0.00" readonly  ></div></td><td><div><input class="form-control haber" style="width: 100%; height: 80%"  id="haber' +
            id + '" name="haber' + id +
            '" value="0.00"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" onchange="envalor_base(' +            id +
            ');" ></div></td> <td><div> <input style=" width: 100%; height: 80%" class="form-control" id="valor_base' +
            id + '" name="valor_base' + id +
            '" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" ></div></td>  <td><button id="eliminar' +
            id + '" style="margin-left: 20px;" type="button" onclick="javascript:eliminar_registro2(' + id +
            ')" class="btn btn-danger bloquearicon btn-gray btn-xs delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('dt_recibido').appendChild(midiv);
        id = parseInt(id);
        envalor_base(id);
        id = id + 1;

        document.getElementById('contador_a').value = id;
        $("#visibilidad" + id).val("1");
        $('.nombres').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('fact_contable_nombre')}}",
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
            minLength: 3,
        });
        $('.codigo').autocomplete({
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
        $('.select2').select2({
            tags: false
        });
    });

    function agregar_valor(id) {
        var valor_cheque = parseFloat($("#total_ingresos").val());
        var validacion = addvalue();
        if (validacion > valor_cheque) {
            swal("¡Error!", "El valor supera al monto total", "error");
            $("#haber" + id).val("0.00");
            return 'error';
        } else {

        }


    }

    function addvalue() {

        var total = 0;
        var contador=0;
        $(".haber").each(function() {
            if ($(this).val().length > 0) {
                
                total = parseFloat(total) + parseFloat($(this).val());
            }
        });
        total = parseFloat(total).toFixed(2)
        $("#valor_base").val(total);
        return total;
    }
    function validar_tipo_pago(){
        var total = 0;
        $(".tipopago").each(function() {
            if ($(this).val()=="") {
                total++;
            }
        });
        
        return total;
    }
    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('fact_contable_codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val()
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#nombre' + id).val(data);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function eliminar_registro2(valor) {
        var dato1 = 'dato_' + valor;
        var nombre2 = 'visibilidads' + valor;
        var cero = 0;
        $("#haber" + valor).val(cero);
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
    }

    function envalor_base(id) {
        var debe = parseFloat($("#haber" + id).val());
        var validacion = agregar_valor(id);
        if (!isNaN(debe)) {
            if (validacion != 'error') {
                $("#valor_base" + id).val(debe.toFixed(2, 2));
            } else {
                $("#valor_base" + id).val('0.00');
            }

        } else {
            $("#valor_base" + id).val('0.00');
        }

    }

    function banco_informacion() {
        var validacion = $("#id_banco").val();
        var eleman = document.getElementById("datos_banco");
    }

    function autollenarf() {
        var contador = parseInt($('#contador').val());
        var acumulador = "";
        for (i = 0; i <= contador; i++) {
            var concepto = $('#numero' + i).val();
            var abono = parseFloat($('#abono_a' + i).val());
            if (abono > 0 && !isNaN(abono)) {
                acumulador += concepto + " - ";
            } else {
                console.log('error abono');
            }
            
        }
        $('#autollenar').val("Cancela FV: " + acumulador);

    }

    function llenar_girador(id) {
        var girador = $('#nombre_proveedor').val();
        
        if ((girador) != null) {
            $('#girador' + id).val(girador);
        } else {
           
        }
    }

    function cambio_banco(id) {
        if (id != null) {
            $("#banco" + id).empty();
            var valor = $("#tipo" + id).val();
            var eleman = document.getElementById("banco" + id);
            var num = document.getElementById("numero_a" + id);
            var cuenta = document.getElementById("cuenta" + id);
            var validacion = 0;
           
            switch (valor) {
                case '1':
                    validacion = 3;
                    break;
                case '4':
                    validacion = 1;
                    break;
                case '6':
                    validacion = 2;
                    break;
            }
            if (validacion != 3) {
                $.ajax({
                    type: 'post',
                    url: "{{route('comp_ingreso.tarjeta')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'opcion': validacion
                    },
                    success: function(data) {
                        
                        if (data.value != 'no') {
                            if (valor != 0) {
                                $("#banco" + id).empty();
                               
                                eleman.disabled = false;
                                num.disabled=false;
                                cuenta.disabled=false;
                                $.each(data, function(key, registro) {
                                    $("#banco" + id).append('<option value=' + registro.id + '>' +
                                        registro.nombre + '</option>');
                                });
                            } else {
                                $("#banco" + id).empty();
                            }

                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            } else {
                eleman.disabled = true;
                num.disabled=true;
                cuenta.disabled=true;
            }

        } else {
            console.log("Error id null");
        }
    }

    function boton_deuda() {
        var valor = parseFloat($("#valor_total").val());
        var valor_saldo = parseFloat($("#saldo_a0").val());
        var total = 0;
        if (!isNaN(valor) && !isNaN(valor_saldo)) {

            if (valor_saldo <= valor) {
                total = valor - valor_saldo;

                $("#abono_a0").val(valor_saldo.toFixed(2, 2));
            } else {
                total = valor_saldo - valor;
                $("#valor_total").val(total.toFixed(2, 2));
                $("#abono_a0").val(valor.toFixed(2, 2));
            }

        } else {
            swal("Error!", "Ingrese valor", "error");
        }
    }

    function guardar() {
        var formulario = document.forms["crear_factura"];
        var fecha = formulario.fecha.value;
        var banco = formulario.id_banco.value;
        var concepto= formulario.concepto.value;
        var contadora= formulario.contador_a.value;
        var contador= formulario.contador.value;
        var divisas= formulario.divisas.value;
        var validacion= addvalue();
        
        var final= parseFloat($("#total_ingresos").val());
        var msj = "";
        if (fecha == "") {
            msj += "Por favor, Llene la fecha <br/>";
        }
        if (banco == "") {
            msj += "Por favor, Llene el Banco <br/>";
        }
        if (concepto == "") {
            msj += "Por favor, Llene el concepto <br/>";
        }
        if(contador==""){
            msj += "Por favor, Llene la tabla de valores recibidos <br/>";
        }
        if(contadora==""){
            msj += "Por favor, Llene la tabla de complemento contable <br/>";
        }
        if(divisas==0){
            msj += "Por favor, Llene las divisas <br/>";
        }
        if (msj == "") {
                if(validacion<final){
                 swal("Error!","El valor de ingresos es mayor que en el haber del complemento contable.","error");
                }else{
                    $.ajax({
                        type: 'post',
                        url: "{{route('comprobante_ingreso_v.store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#crear_factura').serialize(),
                        success: function(data) {

                            if(data.status == "error"){
                                alertas('error','Error...',data.msj);

                                return ;
                            }

                            //bloquearcampos();
                            swal("Correcto!", "Se creo el comprobante de ingreso correctamente", "success");
                            url="{{ url('contable/cliente/comprobante/ingreso/varios/pdf/')}}/"+data.id_comprobante;
                            buscarAsiento(data.id_comprobante);
                            window.open(url,'_blank');
                            $("#idx").val(data.id_comprobante);
                        },
                        error: function(data) {
                            $('#boton_guardar').removeAttr("disabled");
                            console.log(data);
                        }
                    })
                }
            //alert("entras");
            
            
            
        } else {
            $('#boton_guardar').removeAttr("disabled");
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }
    function validar_tabla1(){
        var contador = parseInt($('#contador').val());
        var validacion=0;
        if(!isNaN(contador)){
            for(i=0; i<=contador; i++){
                var tipo= $("#tipo"+i).val();
                if(tipo == null){
                    validacion++;
                }
            }  
           
            if(validacion>0){
                return 'error';
            }else{
                return 'ok';
            }
             
        }else{
            console.log("Error contador");
        }
    }
    function validar_tabla2(){
        var contador = parseInt($('#contador_a').val());
        var validacion=0;
        if(!isNaN(contador)){
            for(i=0; i<=contador; i++){
                var tipo= $("#codigo"+i).val();
                if(tipo == null){
                    validacion++;
                }
            }  
           
            if(validacion>0){
                return 'error';
            }else{
                return 'ok';
            }
             
        }else{
            console.log("Error contador");
        }
    }
    $("#id_cliente").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        type: 'GET',
        url: "{{route('ventas.buscarclientexid')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        
            console.log("identificacion_cliente", data.id);
            if(data.id==""){
                //swal("el cliente no existe");
                existeCliente= false; 
            }else{
                existeCliente = true;
            }
        }
        } );
    },
    change:function(event, ui){
        $("#crear").empty();
        $("#nombre_proveedor").val(ui.item.nombre);
        buscar_vendedor();
    },
        selectFirst: true,
        minLength: 1,
    } );

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
        suma_totales();

    }

    function envalor_base(id) {
        var debe = parseFloat($("#haber" + id).val());
        var validacion = agregar_valor(id);
        if (!isNaN(debe)) {
            if (validacion != 'error') {
                $("#valor_base" + id).val(debe.toFixed(2, 2));
            } else {
                $("#valor_base" + id).val('0.00');
            }

        } else {
            $("#valor_base" + id).val('0.00');
        }

    }

    function cambiar_id_cliente() {
        $.ajax({
            type: 'post',
            url: "{{route('clientes.datos_cliente')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#id_cliente").val()
            },
            success: function(data) {
                //console.log(data.value);
                if (data.value != "no") {
                    $('#nombre_proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    buscar_vendedor();
                } else {
                    $('#nombre_proveedor').val(" ");
                    $('#direccion_proveedor').val("");
                    buscar_vendedor();
                }
            },
            error: function(data) {
                // console.log(data);
            }
        })
    }

    $("#nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('clientes.nombre_clientes')}}",
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

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('clientes.datos_cliente2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#id_cliente').val(data.value);
                    $('#direccion').val(data.direccion);
                    buscar_vendedor();
                } else {
                    $('#id_cliente').val("");
                    $('#direccion').val("");
                    buscar_vendedor();
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    }

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
                if (data.value != "no") {
                    $("#crear").empty();
                    var fila = 0;
                   // console.log(data);
                    for (i = 0; i < data[4].length; i++) {
                        if (tipo != 1) {
                            var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA',
                                data[4][i].numero, data[4][i].nro_comprobante, data[4][i]
                                .valor_contable, data[4][i].id);
                            $('#example2').append(row);
                            fila = i;
                        } else {
                            var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA',
                                data[4][i].numero, data[4][i].nro_comprobante, data[4][i]
                                .valor_contable, data[4][i].id);
                            $('#example2').append(row);
                            fila = i;
                        }
                    }
                    
                    $("#contador_a").val(fila);
                    
                }
                             
            },
            error: function(data) {
                console.log(data);

            }
        });



    }

    function validar_td(id) {
        if ((id) != null) {
            var valor = parseFloat($("#valor_total").val());
            var abono = parseFloat($("#valor" + id).val());
            suma_totales();
            var cantidad = parseFloat($("#total_suma").val());
            $("#valor" + id).val(abono.toFixed(2, 2));
        }
    }

    function validar_td2(id) {
        if ((id) != null) {
            var valor = parseFloat($("#valor_total").val());
            var abono = parseFloat($("#abono_a" + id).val());
            var saldo = parseFloat($("#saldo_a" + id).val());
            suma_totales2();
            var cantidad = parseFloat($("#total_suma_a").val());
            if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                if (cantidad <= valor) {
                    var uno = 1;
                    $("#verificar_superavit").val(uno);
                    if (abono > saldo) {
                        abono = saldo;
                    }
                    $("#abono_a" + id).val(abono.toFixed(2, 2));
                } else {
                    valor = 0;
                    $("#abono_a" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "Error no puede superar al valor del cheque", "error")
                }
            } else {
                abono = 0;
                valor = 0;
                $("#abono_a" + id).val(valor.toFixed(2, 2));
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
        
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#valor" + contador).val());
            visibilidad = parseFloat($("#visibilidad" + contador).val());
            if (!isNaN(cantidad)) {
                if (visibilidad > 0) {
                    total += cantidad;
                }
            }
            contador = contador + 1;
        });
        if (isNaN(total)) {
            total = 0;
        }
        $("#total_suma").val(total.toFixed(2, 2));
        $("#total_ingresos").val(total.toFixed(2, 2));

    }

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, ids) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos +"' readonly='' value='" + ids + "'> </td>" +
            "<td> <input class='form-control' type='text' name='emision" + pos + "' id='emision" + pos + "' value='" +
            fecha + "' readonly=''> </td>" + "<td> <input class='form-control' type='text' name='tipo_a" + pos +
            "' id='tipo_a" + pos + "' value='VEN-FA' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" +
            fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='observacion" + pos +
            "' id='observacion" + pos + "' value='Fact:" + fact_numero + " Ref: " + observacion +
            "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div" + pos +
            "' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo_a" + pos +
            "' value='" + valor + "' id='saldo_a" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono_a" +
            pos + "' id='abono_a" + pos + "' onchange='validar_td2(" + pos + ")'></td>" +
            "<td> <input style='width: 77%;' class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" +
            pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
            "</tr>";
        return markup;

    }

    function addNewRow2(pos, fecha, tipo, fact_numero, observacion, valor) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input type='text' name='emision" + pos + "' id='emision" + pos + "' readonly='' value='" + fecha +
            "'> </td>" +
            "<td> <input type='text'  style='width: 100%;' name='tipo" + pos + "' id='tipo" + pos + "' value='" + tipo +
            "' readonly=''> </td>" +
            "<td> <input type='text' name='numero_a" + pos + "' id='numero_a" + pos + "' value='" + fact_numero +
            "' readonly=''> </td>" +
            "<td> <input type='text' style='width: 100%;' name='concepto" + pos + "' id='concepto" + pos + "' value='" +
            observacion + "' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; ' name='div" + pos + "' id='div" + pos +
            "' value='$' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; ' name='saldo_a" + pos + "' value='" + valor +
            "' id='saldo_a" + pos + "' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' name='abono_a" + pos +
            "' id='abono_a" + pos + "' onchange='validar_saldos(" + pos + ")'></td>" +
            "<td> <input type='text' style=' text-align: left;' name='nuevo_saldo" + pos +
            "' value='0.00'  id='nuevo_saldo_a" + pos + "' readonly=''><input type='hidden' name='visibilidad" + pos +
            "' id='visibilidad" + pos + "' value='0'></td>" +
            "</tr>";
        return markup;

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
                
                if (data.value != "no") {
                    $("#crear_a").empty();
                    var fila = 0;
                    for (i = 0; i < data.length; i++) {
                        var row = addNewRow2(i, data[i].fecha_asiento, 'ACR-EG', data[i].secuencia, data[i]
                            .observacion, data[i].valor_abono);
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
        location.href = "{{route('comprobante_ingreso_v.create')}}";
    }
    function bloquearcampos(){
            $('#crear_factura input').attr('readonly', 'readonly');
            $('#crear_factura textarea').attr('readonly', 'readonly');
        $('#crear_factura select').attr("disabled", true);
        $("#boton_guardar").attr("disabled", true);
        $('.bloquearicon').attr("disabled", true);
    }
    </script>
</section>
@endsection