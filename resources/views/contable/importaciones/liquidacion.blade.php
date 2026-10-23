@extends('contable.compras_pedidos.base')
@section('action-content')
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.importaciones')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">{{trans('contableM.regisliquida')}}</a></li>
        </ol>
    </nav>
    <form class="form-vertical " id="form_guardado" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b></b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <button type="button" id="guard" class="btn btn-success btn-gray" onclick="boton_deuda()">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                </button>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('importaciones.index')}}">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>


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
                                <label class="label_header">{{trans('contableM.estado')}}</label>
                                <div style="background-color: green;" class="form-control "></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}:</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>

                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" readonly>


                            </div>

                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{ date('Y-m-d') }}">

                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-10 px-1">
                                <label class="label_header" for="acreedor">{{trans('contableM.concepto')}}:</label>
                                <input class="form-control  col-md-12" type="text" name="concepto" autocomplete="off" id="concepto">
                            </div>
                            <div class=" col-md-2 px-1 visibilidad">
                                <label class="container ">{{trans('contableM.Chequeentregado')}}
                                    <input type="checkbox" id="cheque_entregado" class="spropety" name="cheque_entregado">
                                    <span class="checkmark"></span>

                                </label>
                            </div>
                            <div class=" col-md-6 visibilidad px-0">
                                <label class="col-md-12 label_header" for="ruc">{{trans('contableM.banco')}}:</label>
                                <select class="form-control " name="banco" id="banco">
                                    <option value="0">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2 visibilidad px-0">
                                <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.divisass')}}:</label>
                                <select class="form-control col-md-12 " name="divisasa" id="divisasa">
                                    <option value="0">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class=" col-md-2 visibilidad px-1">
                                <label class="col-md-12 label_header control-label" for="numero_cheque">{{trans('contableM.NroCheque')}}</label>
                                <input class="form-control " type="number" name="numero_cheque" id="numero_cheque">
                            </div>
                            <div class=" col-md-2 visibilidad px-1">
                                <label class="col-md-12 label_header" for="fecha_cheque">{{trans('contableM.fechacheque')}}: </label>
                                <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="valor" class="label_header">{{trans('contableM.valor')}}</label>
                                <input class="form-control " autocomplete="off" type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">

                            </div>
                            <div class=" col-md-8 px-1">
                                {{ csrf_field() }}
                                <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.beneficiario')}}</label>
                                <input type="text" id="beneficiario" name="beneficiario" class="form-control form-control-sm " autocomplete="off">
                            </div>

                            <div class=" col-md-2 visibilidad px-1">
                                <label class="col-md-12 label_header" for="num_liq">{{trans('contableM.numliquida')}}: </label>
                                <input class="form-control " type="number" name="num_liq" id="num_liq">
                                <input type="hidden" name="id_importacion" id="id_importacion" value="{{$id}}">
                            </div>

                            <div class="col-md-12">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="detalle_deuda" class="control-label label_header">{{trans('contableM.DETALLEDELCOMPLEMENTOCONTABLE')}}</label>

                        <input type="hidden" name="id_compra" id="id_compra">

                        <div class="table-responsive" style="width: 100%;">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="text-align: center;"></th>
                                        <th style="text-align: center;">{{trans('contableM.Cuenta')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.divisas')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.Debe')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.Haber')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.ValorBase')}}</th>
                                        <th style=" text-align: center;">
                                            <button id="busqueda" type="button" onclick="crearFila();" class="btn btn-success btn-gray btn-xs">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="dt_recibido">
                                    @php $cont=0; @endphp
                                    @if(count($cuentas_liq)>0)
                                    @foreach ($cuentas_liq as $cliq)
                                    <tr>
                                        <td>
                                            <select class="form-control" name="tipo[]" id="tipo{{$cont}}" style=" width: 85%; height: 80%;" required>
                                                <option value="">{{trans('agenda.seleccione')}}...</option>
                                                <option @if($cliq->id_tipo == 1) selected @endif value="1">{{trans('contableM.informativo-')}}</option>
                                                <option @if($cliq->id_tipo == 2) selected @endif value="2">{{trans('contableM.Costo')}}</option>
                                            </select>
                                        </td>
                                        <td>

                                            <select class="form-control select2_cuentas" name="codigo[]" id="codigo{{$cont}}" style="width: 100%;" required>
                                                <option value="">{{trans('agenda.seleccione')}}...</option>

                                                @foreach($cuentas as $value)

                                                <option @if($cliq->id_plan_cuenta == $value->id) selected @endif value="{{$value->id}}"> {{$value->plan_id}} - {{$value->nombre}} </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="visibilidad{{$cont}}" name="visibilidad[]" value="1">
                                        </td>
                                        <td>
                                            <div>
                                                <select class="form-control" style=" width: 85%; height: 80%;" name="divisas[]" id="divisas{{$cont}}">
                                                    <option value="">{{trans('agenda.seleccione')}}... </option>
                                                    @foreach($divisas as $value)
                                                    <option @if($value->id == 1) selected @endif value="{{$value->id}}">{{$value->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <input class="form-control debe" style=" width: 88%; height: 80%;" type="text" name="debe[]" id="debe{{$cont}}" onkeypress="return isNumberKey(event)" onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00';addvalue();" value="0.00">
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <input class="form-control haber" style=" width: 88%; height: 80%" id="haber{{$cont}}" name="haber[]" onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00' ;addvalue2();" value="0.00">
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <input style=" width: 79%; height: 80%" class="form-control" id="valor_base{{$cont}}" name="valor_base[]" value="0.00" readonly onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();">
                                            </div>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-danger btn-gray delete">
                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @endif

                                    <input type="hidden" name="contador" id="contador" value="{{$cont}}">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
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
                            <div class="row">
                                <div class=" col-md-6 form-group">
                                    <label class="control_label" for="debe_final">{{trans('contableM.Debe')}}</label>
                                    <input type="text" class="form-control" id="debe_final" readonly value="0.00">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control_label" for="haber_final">{{trans('contableM.Haber')}}</label>
                                    <input type="text" class="form-control" id="haber_final" readonly value="0.00">
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12">
                            &nbsp;
                        </div>
                        <div class="col-md-12">
                            <div class="input-group">
                                <label class="col-md-12 cabecera" style="color: white;" for="nota">{{trans('contableM.nota')}}:</label>
                                <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                                <input type="hidden" name="saldo_final" id="saldo_final">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>



<script type="text/javascript">
    $(document).ready(function() {
        // crearFila(0);
        // crearFila(1);
        $('.select2_cuentas').select2({
            tags: false
        });
    });

    function setNumber(e) {
        // return parseFloat(e).toFixed(2);
        //if(e.length)
        if (e == "") {
            e = 0;
        }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function addvalue() {
        //need to change
        var contador = 0;
        var total = 0;
        $(".debe").each(function() {
            if ($(this).val().length > 0) {

                total = parseFloat(total) + parseFloat($(this).val());

                contador++;
            }
        });
        total = parseFloat(total).toFixed(2, 2)
        $("#debe_final").val(total);
        return total;
    }

    function addvalue2() {

        var total = 0;
        var contador = 0;
        $(".haber").each(function() {
            if ($(this).val().length > 0) {

                total = parseFloat(total) + parseFloat($(this).val());


                contador++;
            }
        });
        var cheque = parseFloat($('#valor_cheque').val());
        if (isNaN(cheque)) {
            cheque = 0;
        }
        var tos = parseFloat(total) + cheque;
        $("#haber_final").val(tos.toFixed(2, 2));
        return total;
    }

    function buscarAsiento(id_asiento) {
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '6'
            },
            success: function(data) {

                if (data.value != `{{trans('contableM.noencontrresul')}}`) {
                    $('#asiento').val(data[0]);
                    $('#numero_factura').val(data[1]);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function agregar_valor(id) {
        var valor_cheque = parseFloat($("#valor_cheque").val());
        var validacion = addvalue();
        var validacion2 = addvalue2();
        if (validacion > valor_cheque) {
            if (validacion2 > 0) {

            } else {
                swal("¡Error!", "{{trans('contableM.elvalorsuperamontocheque')}}", "error");
                $("#debe" + id).val("0.00");
                return 'error';
            }

        } else {

        }


    }

    function agregar_valor2(id) {
        var valor_cheque = parseFloat($("#valor_cheque").val());
        var validacion = addvalue2();
        if (validacion > valor_cheque) {
            swal("¡Error!", "{{trans('contableM.elvalorsuperamontocheque')}}", "error");
            $("#debe" + id).val("0.00");
            return 'error';
        } else {

        }


    }

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
        $(this).parent().parent().remove(); //posibility with mode protocal
        console.log("eliminando ...");
        console.log("eliminando ..");
    }

    function envalor_base(id) {
        var debe = parseFloat($("#debe" + id).val());
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

    function envalor_base2(id) {
        var debe = parseFloat($("#haber" + id).val());
        var validacion = agregar_valor2(id);
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

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(dato1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
        $(this).parent().parent().remove(); //posibility with mode protocal
        console.log("eliminando ...");
        console.log("eliminando ..");
    }

    const crearFila = (seleccion = 0) => {
        let id = document.getElementById('contador').value;
        console.log(id)
        let cuenta_id = 0;
        if (parseInt(id) == 1) {
            cuenta_id = 1;
        }
        console.log(cuenta_id)
        let fila = `
            <tr >
                <td>
                    <select class="form-control" name="tipo[]" id="tipo${id}" style=" width: 85%; height: 80%;" required>
                        <option value="">Seleccione...</option>
                        <option value="1">INFORMATIVO</option>
                        <option value="2">{{trans('contableM.Costo')}}</option>
                    </select>
                </td>
                <td>
             
                    <select class="form-control select2_cuentas" name="codigo[]" id="codigo${id}" style="width: 100%;" required> 
                    <option value="">Seleccione...</option> 

                        @foreach($cuentas as $value)  
                            <option value="{{$value->id}}"> {{$value->plan_id}} - {{$value->nombre}} </option>
                        @endforeach 
                    </select> 
                    <input  type="hidden" id="visibilidad${id}" name="visibilidad[]" value="1">
                </td>
                <td>
                    <div> 
                        <select class="form-control" style=" width: 85%; height: 80%;" name="divisas[]" id="divisas${id}" > 
                            <option value="">Seleccione... </option> 
                            @foreach($divisas as $value) 
                                <option @if($value->id == 1) selected @endif value="{{$value->id}}">{{$value->descripcion}}</option> 
                            @endforeach  
                        </select>
                    </div>
                </td>

                <td>
                    <div>
                        <input class="form-control debe" style=" width: 88%; height: 80%;"   type="text" name="debe[]" id="debe${id}" onkeypress="return isNumberKey(event)" onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00';addvalue();" value="0.00"   >
                    </div>
                </td>

                <td>
                    <div>
                        <input class="form-control haber" style=" width: 88%; height: 80%" id="haber${id}" name="haber[]"  onblur="this.value= parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : '0.00';addvalue2();"  value="0.00"  >
                    </div>
                </td> 

                <td>
                    <div> 
                        <input style=" width: 79%; height: 80%" class="form-control" id="valor_base${id}" name="valor_base[]" value="0.00" readonly onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" >
                    </div>
                </td> 

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#dt_recibido').append(fila);

        $('.select2_cuentas').select2({
            tags: false
        });


        envalor_base(id);
        envalor_base2(id);
        document.getElementById('dt_recibido').value = id;

        id++;
        document.getElementById('contador').value = id;

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



    }


    function boton_deuda() {
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque = formulario.valor_cheque.value;
        var numero_cheque = formulario.numero_cheque.value;
        var contador = formulario.contador.value;
        var beneficiario = formulario.beneficiario.value;

        var banco = formulario.banco.value;
        var divisas = formulario.divisasa.value;
        var concepto = formulario.concepto.value;
        var msj = "";
        if (valor_cheque == "") {
            msj += "{{trans('contableM.porfacheque')}} <br/>";
        }
        if (contador == "") {
            msj += "{{trans('contableM.porfallenacampotablaantesguard')}} <br/>";
        }
        if (beneficiario == "") {
            msj += "{{trans('contableM.porfallenecampobenefi')}} <br/>";
        }

        if (banco == "") {
            msj += "{{trans('contableM.porfallenacambanco')}} <br/>";
        }
        if (divisas == "") {
            msj += "{{trans('contableM.porfallenecampodivisas')}} <br/>";
        }
        if (concepto == "") {
            msj += "{{trans('contableM.porfallenecamconcepto')}} <br/>";
        }
        if (fecha == "") {
            msj += "{{trans('contableM.porfallenalafecha')}} <br/>";
        }

        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = $("#valor_cheque").val();
        var validacion = addvalue();
        var validacion2 = addvalue2();
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        if (msj == "") {

            var debe = parseFloat($('#debe_final').val());
            var haber = parseFloat($('#haber_final').val());
            var diferencia = (debe - haber) * (-1);
            if (debe == haber) {
                if ($("#form_guardado").valid()) {
                    $("#guard").prop("disabled", "disabled");
                    $.ajax({
                        type: 'post',
                        url: "{{route('importaciones.store_liquidacion')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#form_guardado').serialize(),
                        success: function(data) {
                            //console.log(data);
                            
                            if ((data.respuesta) == 'success') {
                                $("#vence0").val(vence);
                                $("#tipo0").val(tipo);
                                $("#numero0").val(numero);
                                $("#saldo0").val(saldo_final);
                                $("#nuevo_saldo0").val(saldo_final);
                                $("#abono_base0").val(saldo_final);
                                // swal("Guardado correcto");
                                alertas(data.respuesta, data.respuesta, data.msj)
                                $('#form_guardado input').attr('readonly', 'readonly');
                                $("#guard").prop("disabled", "disabled");
                                buscarAsiento(data.id_asiento);
                                url = "{{ url('contable/compra/comprobante/egresovarios/pdf/')}}/" + data.id_comprobante;
                                window.open(url, '_blank');
                                setTimeout(function() {
                                    window.location.href = "{{route('importaciones.index')}}";
                                }, 1500)
                                //swal(`{{trans('contableM.correcto')}}!`,"Se creo el comprobante de egresos varios","success");             
                            } else {
                                $('#guard').prop("disabled", false);
                                alertas(data.respuesta, data.respuesta, data.msj)

                            }
                        },
                        error: function(data) {
                            $('#guard').prop("disabled", false);
                            console.log(data);
                        }
                    })
                }
            } else {
                $('#guard').prop("disabled", false);
                swal("{{trans('winsumos.mensaje')}}", "{{trans('contableM.tienediferenvalores')}} $" + diferencia, "error");
            }


        } else {
            $('#guard').prop("disabled", false);
            //$("#guard").prop("disabled", "");
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }


    function validarCierre() {
                
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>

            var fecha1 = '<?= $min_date; ?>';
            let fechaAc = new Date();
            var fecha2 = document.getElementById("fecha_hoy").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            const fechaFormateada = fechaAc.toISOString().split('T')[0];
            if (fechaFinal < fechaInicial) {
                document.getElementById("fecha_hoy").value = fechaFormateada;
                return new swal("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error")
        }
        <?php
        }
        ?>
    }


</script>
@endsection