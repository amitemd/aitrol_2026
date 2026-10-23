@extends('contable.retenciones.base')
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

    input {
        width: 96%;
        padding: 0 2%;
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
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('retenciones_index') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Proveedores</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">Retenciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Retención</li>
        </ol>
    </nav>
    <form class="form-vertical" method="post" id="form_guardado">
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <div class="box-title "><b>ACREEDORES-COMP. DE RETENCIONES</b></div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" onclick="guardar_retenciones()" id="boton_guardar" class="btn btn-success btn-gray "><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </button>
                                <button type="button" class="btn btn-success  btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </button>
                                <button type="button" onclick="goBack()" class="btn btn-success  btn-gray">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </button>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body  dobra">


                <form class="form" action="" id="form_guardado" method="post">

                    <div class="header row">
                        <input type="text" name="cont" id="cont" value="1" class="hidden">
                        <div class="form-group col-md-9 px-0">
                            <label class="control-label">BUSCADOR POR FACTURA </label>
                        </div>
                        <label for="buscar" class="control-label label_header">BUSCADORES</label>
                        <div class="form-group col-md-4 px-0">

                            <input type="text" class="form-control buscar" placeholder="Ingrese Secuencia..." id="buscar" name="buscar" onchange="buscar_factura()">

                        </div>
                        <div class="form-group col-md-4 px-0">
                            <select class="select2 form-control" onchange="grupos_acreedores()" style="width: 100%;" name="id_acreedor" id="id_acreedor">
                                <option value="">Seleccione Proveedor...</option>
                                @foreach($acreedores as $value)
                                <option value="{{$value->id}}">{{$value->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 px-0">
                            <select class="select2 form-control" style="width: 100%;" name="id_facturaf" onchange="buscar_factura2()" id="id_facturaf">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label ">DATOS DE LA FACTURA</label>
                                </div>
                                <div class="col-xs-3  col-md-2 px-1">
                                  
                                        <label for="empresa" class="label_header">Electrónica</label>

                                        <label class="switch">
                                            <input class="electros" @if($empresa->electronica==1)  @else disabled @endif id="toggleswitch" type="checkbox">
                                            <span class="slider round"></span>
                                            <input type="hidden" name="electronica" id="electronica" value="0">
                                        </label>

                                </div>
                                <div class="col-md-5 px-1">
                                    <label class="control-label label_header">SECUENCIAL</label>
                                    <input type="text" class="form-control " name="secuencial" id="secuencial" readonly>
                                </div>

                                <div class="col-md-5 px-1">
                                    <label class="control-label label_header">N° VALOR DE LA FACTURA </label>
                                    <input type="text" class="form-control " name="valor_factura" id="valor_factura" readonly>
                                </div>
                                <div class="col-md-12 px-1">
                                    <label class="control-label label_header">PROVEEDOR</label>
                                    <input type="text" class="form-control " name="proveedor_modal" id="proveedor_modal" readonly>
                                </div>
                                <div class="col-md-12 px-1">
                                    <label class="control-label label_header">CONCEPTO</label>
                                    <input type="text" class="form-control " name="concepto" id="conceptos" readonly>
                                </div>
                                <div class="col-md-6 px-1">
                                    <label class="control-label label_header">N° AUTORIZACIÓN DE RETENCIÓN</label>
                                    <input type="text" class="form-control " name="nro_autorizacion" id="nro_autorizacion">
                                </div>

                                <div class="col-md-6 px-1">
                                    <label class="control-label label_header">SUCURSAL</label>
                                    <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($sucursales as $value)
                                        <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="row ">
                                <div class="col-md-12">
                                    <label class="control-label">DATOS DE LA RETENCIÓN</label>
                                </div>
                                <div class="col-md-6 px-1">
                                    <label class="control-label label_header">Tipo Retención</label>
                                    <select class="form-control " onchange="traer_retenciones()" name="tipo_retencion" id="tipo_retencion">
                                        <option value="">Seleccione...</option>
                                        <option value="2">FUENTE</option>
                                        <option value="1">IVA</option>
                                    </select>
                                </div>
                                <input type="hidden" name="subtotal_final" id="subtotal_final">
                                <input type="hidden" name="iva_final" id="iva_final">
                                <div class="col-md-6 px-1">
                                    <label class="control-label label_header">PORCENTAJE RETENCIÓN</label>
                                    <select class="form-control " name="porcentaje_retencionf" onchange="lista_valores(this)" id="porcentaje_retencionf">
                                    </select>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">CÓDIGO</label>
                                    <input type="text" class="form-control " name="codigo" id="codigo" readonly>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">BASE RETENCIÓN</label>
                                    <input class="form-control " type="text" name="base_retencion" onchange="cambiar_decimales()" id="base_retencion" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">MONTO RETENIDO</label>
                                    <input type="text" id="monto_retenido" class="form-control " onchange="cambiar_decimales2()" name="monto_retenido" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                    <input type="hidden" name="id_proveedor_modal" id="id_proveedor_modal">
                                    <input type="hidden" name="retencion_total" id="retencion_total">
                                </div>
                                <div class="col-m-12 px-1">
                                    <label class="control-label col-md-12 label_header">Concepto:</label>
                                    <input type="text" class="form-control" name="concepto" id="concepto">
                                    <input type="hidden" name="valor_fuente" id="valor_fuente">
                                    <input type="hidden" name="valor_iva" id="valor_iva">
                                    <input type="hidden" name="tipo_rfir" id="tipo_rfir">
                                    <input type="hidden" name="tipo_rfiva" id="tipo_rfiva">
                                </div>
                                <div class="col-md-4 px-1">
                                    <label for="punto_emision" class="control-label label_header">PUNTO DE EMISIÓN</label>
                                    <select class="form-control" name="punto_emision" id="punto_emision" required>
                                        <option value="">Seleccione...</option>
                                    </select>

                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header"> SECUENCIAL FISICO: </label>
                                    <input type="number" class="form-control" name="punto_final" onchange="verificar_secuencia(this)" id="punto_final" placeholder="INGRESE SECUENCIAL" required>

                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">Fecha Retencion</label>
                                    <input type="date" name="fecha_retencion" id="fecha_retencion" value="{{date('Y-m-d')}}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12">
                            <table id="example2" style="width: 100%;" aria-describedby="example2_info">
                                <thead class="well-dark">
                                    <tr>
                                        <th style="width: 16.6%; text-align: center;">BASE IMP RET</th>
                                        <th style="width: 16.6%; text-align: center;">TIPO</th>
                                        <th style="width: 16.6%; text-align: center;">COD</th>
                                        <th style="width: 16.6%; text-align: center;">% DE RET</th>
                                        <th style="width: 16.6%; text-align: center;">VALOR RETENIDO</th>
                                        <th style="width: 16.6%; text-align: center;">
                                            <button onclick="crea_tds()" type="button" class="btn btn-success btn-xs btn-gray">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="datos_a" style="text-align: right!important;">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="cuenta_renta" id="cuenta_renta">
                        <input type="hidden" name="cuenta_iva" id="cuenta_iva">
                        <input type="hidden" name="eliminados" id="eliminados" value="0">
                        <input type="hidden" name="id_proveedor" id="id_proveedor">
                        <input type="hidden" name="id_compra" id="id_compra" value="0">
                        <input type="hidden" name="id_fact_contable" id="id_fact_contable" value="0">
                </form>
                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-3">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-3 px-0">

                            <label class="label_header">Total RFIR</label>
                            <input class="form-control " type="text" name="total_rfirt" id="total_rfirt">

                        </div>
                        <div class="form-group col-md-3 px-0">

                            <label for="total_abonos" class="label_header">Total RFIVA</label>
                            <input class="form-control " type="text" name="total_rfivat" id="total_rfivat">

                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top:20px">
                    <label class="control-label">DETALLE DE DEUDAS DEL PROVEEDOR</label>
                    <input type="hidden" name="total_factura" id="total_factura">

                </div>
                <div class="col-12 ">
                    <div class="table-responsive col-md-12">
                        <table id="example2" role="grid" aria-describedby="example2_info">
                            <thead class='well-dark'>
                                <tr style="position: relative;">

                                    <th style="width: 8%; text-align: center;">Vence</th>
                                    <th style="width: 10%; text-align: center;">Tipo</th>
                                    <th style="width: 10%; text-align: center;">Número</th>
                                    <th style="width: 10%; text-align: center;">Concepto</th>
                                    <th style="width: 6%; text-align: center;">Div</th>
                                    <th style="width: 6%; text-align: center;">Saldo</th>
                                    <th style="width: 6%; text-align: center;">Abono</th>
                                    <th style="width: 6%; text-align: center;">Saldo Base</th>
                                </tr>
                            </thead>
                            <tbody id="crear">
                                @php $cont=0; @endphp
                                @foreach (range(1, 2) as $i)
                                <tr class="well">
                                    <!--AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                    <td> <input class="form-control input-sm" nowrap="nowrap" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                    <td> <input class="form-control input-sm" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5; " type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;" type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;  text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                    <td> <input class="form-control input-sm" style="text-align: center; width: 85%;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" readonly> </td>
                                </tr>
                                @php $cont = $cont +1; @endphp
                                @endforeach

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="form-group col-md-2 px-0">

                                <label for="total_ingresos" class="label_header">Total Egreso</label>
                                <input class="form-control input-sm" type="text" name="total_egreso" id="total_egreso" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="credito_aplicado" class="label_header">Debito Aplicado</label>
                                <input class="form-control input-sm" olor: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_deudas" class="label_header">Total Deudas</label>
                                <input class="form-control input-sm" type="text" name="total_deudas" id="total_deudas" readonly>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_abonos" class="label_header">Total Abonos</label>
                                <input class="form-control input-sm" type="text" name="total_abonos" id="total_abonos" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2 px-0">

                            <label for="nuevo_saldo" class="label_header">Nuevo Saldo</label>
                            <input class="form-control input-sm" type="text" name="nuevo_saldo" id="nuevo_saldo" readonly>

                        </div>
                        <div class="form-group col-md-2">
                            <input type="hidden" name="retencion_fuente" id="retencion_fuente">
                            <input type="hidden" name="retencion_ivas" id="retencion_ivas">
                            <input type="hidden" name="retencion_totales" id="retencion_totales">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>






        </div>

    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#fact_contable_check').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
    });
    $('.select2').select2({
        tags: false
    });

    function nuevo_comprobante() {
        location.href = "{{route('retenciones_crear')}}";
    }

    function grupos_acreedores() {

        var valor = $("#id_acreedor").val();
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('retencionesa.buscarpro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': valor
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'No se encontraron resultados') {
                    if (valor != 0) {
                        $("#id_facturaf").empty();
                        $("#id_facturaf").append('<option value=' + "" + '>' + "Seleccione # Factura..." + '</option>');
                        $.each(data, function(key, registro) {
                            $("#id_facturaf").append('<option value=' + registro.id + '>' + registro.value + '</option>');
                        });
                    } else {
                        $("#id_facturaf").empty();
                        buscar_factura2();
                    }

                } else {

                    $("#id_facturaf").empty();
                    buscar_factura2()
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    var input = document.getElementById('toggleswitch');
    //var outputtext = document.getElementById('status');

    input.addEventListener('change', function() {
        if (this.checked) {
            $("#electronica").val(1);
            $('#punto_final').attr('readonly', true);
            //$('#numero').attr('readonly', true);
        } else {
            $("#electronica").val(0);
            $('#punto_final').attr('readonly', false);
            //$('#numero').attr('readonly', false);
        }
    });

    function buscar_factura2() {
        var validacion = $("#esfac_contable").val();
        var formulario = document.forms["form_guardado"];
        var id_facturaf = formulario.id_facturaf.value;
        if (id_facturaf != "") {
            $.ajax({
                type: 'post',
                url: "{{route('retenciones_buscar_codigo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_factura': $("#id_facturaf").val(),
                    'tipo': $("#esfac_contable").val(),
                    'secuencia': $("#secuencial").val()
                },
                success: function(data) {
                    $("#secuencial").val(data[11]);
                    $("#valor_factura").val(data[10]);
                    $("#id_compra").val(data[14]);
                    $("#proveedor_modal").val(data[0]);
                    $("#id_proveedor").val(data[0]);
                    $("#id_proveedor_modal").val(data[0]);
                    // $("#nro_autorizacion").val(data[17]);
                    $("#concepto").val(data[8]);
                    $("#total_factura").val(data[10]);
                    $("#subtotal_final").val(data[18]);
                    $("#iva_final").val(data[19]);
                    if (data[20] == "1") {
                        $("#tipo0").val("COM-FA");
                    } else {
                        $("#tipo0").val("COM-FACT");
                    }

                    for (i = 0; i < data[16].length; i++) {
                        $("#vence" + i).val(data[16][i].fecha_asiento);
                        $("#numero_referencia" + i).val((data[16][i].fact_numero) + '-' + data[1]);
                        $("#base_fuente" + i).val(data[16][i].valor);
                        $("#divisas" + i).val(data[16][i].divisas);
                        $("#numero" + i).val(data[16][i].secuencia_f);
                        $("#concepto" + i).val('Fact #: ' + data[16][i].secuencia_f + ' Ref: ' + data[16][i].fact_numero);
                        $("#saldo" + i).val((data[16][i].valor));
                        $("#nuevo_saldo" + i).val((data[16][i].valor));
                        $("#tipo_rfiva" + i).val((data[16][i].id_porcentaje_iva));
                        $("#tipo_rfir" + i).val((data[16][i].id_porcentaje_ft));
                        var iva_base = parseFloat(data[16][i].valor);
                        var total_iva = iva_base * 12 / 100;
                        $("#base_iva" + i).val(total_iva.toFixed(2));
                    }




                },
                error: function(data) {
                    console.log(data);
                }
            })
        } else {
            $("#secuencial").val('');
            $("#valor_factura").val('');
            $("#proveedor_modal").val('');
            $("#id_proveedor").val('');
            $("#id_compra").val('');
            $("#concepto").val('');
            $("#total_factura").val('');
            $("#subtotal_final").val('');
            $("#iva_final").val('');
            $("#tipo0").val('');

            for (i = 0; i < 5; i++) {
                $("#vence" + i).val('');
                $("#numero_referencia" + i).val('');
                $("#base_fuente" + i).val('');
                $("#divisas" + i).val('');
                $("#numero" + i).val('');
                $("#concepto" + i).val('');
                $("#saldo" + i).val((''));
                $("#nuevo_saldo" + i).val((''));
                $("#tipo_rfiva" + i).val((''));
                $("#tipo_rfir" + i).val('');
                $("#base_iva" + i).val('');
            }
        }


    }

    function buscar_factura() {
        var validacion = $("#esfac_contable").val();
        valor = "";
        $('#id_acreedor').val(valor);
        $('#id_acreedor').select2().trigger('change');
        var formulario = document.forms["form_guardado"];
        var id_facturaf = formulario.buscar.value;
        if (id_facturaf != "") {
            $.ajax({
                type: 'post',
                url: "{{route('retenciones_buscar_codigo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_factura': $("#buscar").val(),
                    'tipo': $("#esfac_contable").val(),
                    'secuencia': $("#secuencial").val()
                },
                success: function(data) {
                    $("#secuencial").val(data[11]);
                    $("#valor_factura").val(data[10]);
                    $("#id_compra").val(data[14]);
                    $("#proveedor_modal").val(data[0]);
                    $("#id_proveedor").val(data[0]);
                    $("#id_proveedor_modal").val(data[0]);
                    $("#concepto").val(data[8]);
                    $("#total_factura").val(data[10]);
                    //$("#nro_autorizacion").val(data[17]);
                    $("#subtotal_final").val(data[18]);
                    $("#iva_final").val(data[19]);
                    if (data[20] == "1") {
                        $("#tipo0").val("COM-FA");
                    } else {
                        $("#tipo0").val("COM-FACT");
                    }
                    for (i = 0; i < data[16].length; i++) {
                        $("#vence" + i).val(data[16][i].fecha_asiento);
                        $("#numero_referencia" + i).val((data[16][i].fact_numero) + '-' + data[1]);
                        $("#base_fuente" + i).val(data[16][i].valor);
                        $("#divisas" + i).val(data[16][i].divisas);
                        $("#numero" + i).val(data[16][i].secuencia_f);
                        $("#concepto" + i).val('Fact #: ' + data[16][i].secuencia_f + ' Ref: ' + data[16][i].fact_numero);
                        $("#saldo" + i).val((data[16][i].valor));
                        $("#nuevo_saldo" + i).val((data[16][i].valor));
                        $("#tipo_rfiva" + i).val((data[16][i].id_porcentaje_iva));
                        $("#tipo_rfir" + i).val((data[16][i].id_porcentaje_ft));
                        var iva_base = parseFloat(data[16][i].valor);
                        var total_iva = iva_base * 12 / 100;
                        $("#base_iva" + i).val(total_iva.toFixed(2));
                    }




                },
                error: function(data) {
                    console.log(data);
                }
            })
        } else {
            $("#secuencial").val('');
            $("#valor_factura").val('');
            $("#id_compra").val('');
            $("#proveedor_modal").val('');
            $("#id_proveedor").val('');
            $("#concepto").val('');
            $("#total_factura").val('');
            $("#subtotal_final").val('');
            $("#iva_final").val('');
            $("#tipo0").val('');
            for (i = 0; i < 5; i++) {
                $("#vence" + i).val('');
                $("#numero_referencia" + i).val('');
                $("#base_fuente" + i).val('');
                $("#divisas" + i).val('');
                $("#numero" + i).val('');
                $("#concepto" + i).val('');
                $("#saldo" + i).val((''));
                $("#nuevo_saldo" + i).val((''));
                $("#tipo_rfiva" + i).val((''));
                $("#tipo_rfir" + i).val('');
                $("#base_iva" + i).val('');
            }
        }
    }

    $("#buscar").autocomplete({
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
        minLength: 2,
    });




    function validar_vacios() {
        var retencion_fuente = $("#retencion_fuente").val();
        var retencion_ivas = $("#retencion_ivas").val();
        var retencion_totales = $("#retencion_totales").val();
        var retencion_iva = $("#retencion_impuesto").val();
        var nuevo_saldo0 = $("#nuevo_saldo0").val();
        var id_compra = $("#id_compra").val();
        var id_proveedor = $("#id_proveedor").val();
        if (retencion_fuente != "" && retencion_ivas != "" && retencion_totales != "" && retencion_iva != "" && nuevo_saldo0 != "" && id_compra != "" && id_proveedor != "") {
            return 'ok';
        }
        return 'no';
    }

    function traer_retenciones() {
        //retenciones.buscartipo
        var id = $("#tipo_retencion").val();
        //alert(id);
        $.ajax({
            type: 'get',
            url: "{{route('retenciones.buscartipo')}}",
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                if (data != null) {
                    //alert("dasda");

                    $("#porcentaje_retencionf").empty();
                    $("#porcentaje_retencionf").append('<option value="0">Seleccione...</option>');
                    $.each(data, function(key, registro) {
                        $("#porcentaje_retencionf").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                    });
                } else {
                    $("#porcentaje_retencionf").empty();
                }
                // console.log(data);  
                //swal("Correcto!","Retencion guardada correctamente","success");

            },
            error: function(data) {
                //console.log(data);
            }


        });
    }

    function guardar_retenciones() {
        var tipo_rfir = $("#tipo_rfir").val();
        var tipo_rfiva = $("#tipo_rfiva").val();
        var validacion = validar_vacios();
        var formulario = document.forms["form_guardado"];
        var porcentaje_retencion = formulario.porcentaje_retencionf.value;
        var proveedor = formulario.proveedor_modal.value;
        var secuencia = formulario.secuencial.value;
        var no_autorizacion = formulario.nro_autorizacion.value;
        var punto_emision = formulario.punto_emision.value;
        var sucursal = formulario.sucursal.value;
        var no_factura = formulario.valor_factura.value;
        var valor_fuente = formulario.valor_fuente.value;
        var valor_iva = formulario.valor_iva.value;
        var cuenta_renta = formulario.cuenta_renta.value;
        var cuenta_iva = formulario.cuenta_iva.value;
        var electronica= $("#electronica").val();
        
        var msj = "";
        if(electronica=="1"){
            no_autorizacion="fe";
           
        }
        var compra = formulario.id_compra.value;
        if (porcentaje_retencion == "") {
            msj += "Por favor, Porcentaje de retención \n";
        }
        if (proveedor == "") {
            msj += "No existe proveedor\n";
        }
        if (secuencia == "") {
            msj += "Por favor, Llene el campo de secuencia\n";
        }
        
        if (no_autorizacion == "") {
            msj += "Por favor, Falta la autorización\n";
        }
        if (no_factura == "") {
            msj += "Por favor, Llene el numero de la factura\n";
        }
        if (valor_fuente == "") {
            msj += "Por favor, Llene el valor de la fuente\n";
        }
        if (valor_iva == "") {
            msj += "Por favor, Llene el valor del IVA\n";
        }
        if (punto_emision == "") {
            msj += "Por favor, Llene el punto de emision\n";
        }
        if (sucursal == "") {
            msj += "Por favor, Llene el campo sucursal\n";
        }
        if (compra == "") {
            msj += "No se seleccionó la factura de compra\n";
        }

        if (msj == "") {
            $("#boton_guardar").attr("disabled", "disabled");
            $.ajax({
                type: 'post',
                url: "{{route('retenciones_store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_guardado').serialize(),
                success: function(data) {
                    //console.log(data);
                    if (data.error== 'no') {
                        $.ajax({
                            type: 'post',
                            url: "{{route('contable.pedido.paso_retencion')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: {
                                'id': $('#id_compra').val()
                            },
                            success: function(data) {
                                console.log(data);
                                /* if(data.errores!=''){
                                    swal()
                                    location.href="{{route('contable.compraspedidos.index')}}";
                                }
                                swal('Mensaje',data,'info'); */
                                //location.href="{{route('contable.compraspedidos.index')}}";
                        $("#boton_guardar").attr("disabled", "disabled");
                                swal(`{{trans('contableM.correcto')}}!`, "Retencion guardada correctamente", "success");
                        //url = "{{ url('contable/compra/comprobante/retenciones/')}}/" + data.id;
                        //window.open(url, '_blank');
                        $('#form_guardado input').attr('readonly', 'readonly');
                            },
                            error: function(data) {
                                console.log(data);
                                // swal("Error!", data, "error");
                            }
                        });
                    } else {
                        swal("Advertencia", data.error, "warning");
                    }




                },
                error: function(data) {
                    //console.log(data);
                }
            })
        } else {
            alert(msj);
            $("#boton_guardar").attr("disabled", false);
        }


    }

    function redondeafinal(value, decimales = 2) {

        value = +value;
        if (isNaN(value)) return NaN; // Shift 
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
        value = value.toString().split('e');
        return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);

    }

    function lista_valores(id) {

        var variable_select = $("#porcentaje_retencionf").val();
        var tipo = $("#tipo_retencion").val();
        var total_factura = $("#subtotal_final").val();
        var total_ivav = parseFloat($("#iva_final").val());
        if (isNaN(total_ivav)) {
            total_ivav = 0;
        }
        //alert(valor);
        console.log(variable_select);
        $.ajax({
            type: 'post',
            url: "{{route('retenciones_query')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': variable_select,
                'tipo': tipo
            },
            success: function(data) {
                //alert(data[0].nombre);
                console.log(data);
                if (data.value != 'no') {
                    $("#codigo").val(data[0].codigo);
                    var codigo = parseFloat(data[0].valor);
                    //1 es iva 2 fuente
                    //console.log(codigo+"el codigo es ");

                    if (tipo == '1') {
                        var factura_total = parseFloat($("#subtotal_final").val());

                        var totales = total_ivav * (codigo / 100);
                        totales = redondeafinal(totales);
                        $("#monto_retenido").val(totales);
                        total_ivav = redondeafinal(total_ivav);
                        console.log(totales + " total");
                        $("#base_retencion").val(total_ivav);
                    } else {
                        var totales = total_factura * (codigo / 100);
                        console.log(totales + " total");
                        totales = redondeafinal(totales);
                        total_factura = redondeafinal(total_factura);
                        $("#monto_retenido").val(totales);
                        $("#base_retencion").val(total_factura);
                    }

                    /* total_abono()  */
                }
            },
            error: function(data) {
                //console.log(data);
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
                //console.log(data);
                if (data.value != 'no') {
                    $("#total_rfiva" + id).val(data[0].valor + '%');
                    $("#retencion_iva").val(data[0].valor);
                    var total_enrfiva = parseFloat(data[0].valor) / 100;
                    var retencion_iva = parseFloat($("#base_iva0").val());
                    var asiento_retencion_rfiva = total_enrfiva * retencion_iva;
                    asiento_retencion_rfiva = redondeafinal(asiento_retencion_rfiva);
                    $("#retencion_ivas").val(asiento_retencion_rfiva);
                    total_abono()
                }
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    function cambiar_decimales() {
        var retenci = parseFloat($("#base_retencion").val());
        $("#base_retencion").val("");
        //swal("Advertencia!","Este metodo es de manera manual por lo cual debe ingresar correctamente los valores","warning");
        if (isNaN(retenci)) {
            retenci = 0;
            $("#base_retencion").val(retenci.toFixed(2, 2));
        } else {
            $("#base_retencion").val(retenci.toFixed(2, 2));
        }

    }

    function cambiar_decimales2() {
        var retenci = parseFloat($("#monto_retenido").val());
        $("#monto_retenido").val("");
        //swal("Advertencia!","Este metodo es de manera manual por lo cual debe ingresar el monto retenido de manera manual según el porcentaje seleccionado","warning");
        if (isNaN(retenci)) {
            retenci = 0;
            $("#monto_retenido").val(retenci.toFixed(2, 2));
        } else {
            $("#monto_retenido").val(retenci.toFixed(2, 2));
        }
    }

    function verificar_secuencia(gordo) {
        var empresa = "{{$empresa->id}}";
        console.log(empresa + "aqui va la empresa");
        var punto_emision = $("#punto_emision").val();
        if (empresa != "" && punto_emision != "") {
            $.ajax({
                type: 'get',
                url: "{{route('verificar_secuencia.contable')}}",
                datatype: 'json',
                data: {
                    'secuencia': gordo.value,
                    'id_empresa': empresa,
                    'punto_emision': punto_emision
                },
                success: function(data) {
                    if (data == 'ok') {
                        $("#punto_final").val(gordo.value);
                    } else {
                        swal("Ya existe registro con ese comprobante");
                        $("#punto_final").val('');
                    }
                },
                error: function(data) {
                    //console.log(data);
                }


            });
        } else {
            swal("ingrese punto de emision primero");
        }

    }

    function obtener_caja() {

        var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': id_sucursal
            },
            success: function(data) {
                //console.log(data);

                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#punto_emision").empty();

                        $.each(data, function(key, registro) {
                            $("#punto_emision").append('<option value=' + registro.codigo_sucursal + '-' + registro.codigo_caja + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                        });
                    } else {
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function total_abono() {
        var retencion_fuente = parseFloat($("#retencion_fuente").val());
        var retencion_iva = parseFloat($("#retencion_ivas").val());
        var total_retenciones = retencion_fuente + retencion_iva;
        if (total_retenciones != NaN) {
            $("#retencion_totales").val(total_retenciones.toFixed(2));
            $("#nuevo_saldo0").val(total_retenciones.toFixed(2));
        }
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

    function crea_tds() {
        id = document.getElementById('cont').value;
        var tipo = $("#tipo_retencion").val();
        var total_factura = $("#base_retencion").val();
        var codigo = $("#codigo").val();
        var valor_retenido = $("#monto_retenido").val();
        var porcentaje = $("#porcentaje_retencionf").val();
        var total = $("#total_final").val();
        var eliminados = parseInt($("#eliminados").val());
        var total_final = 0;
        var conter = 0;
        var contaiva = 0;
        var cuenta_retenta = parseInt($("#cuenta_renta").val());
        if (isNaN(cuenta_retenta)) {
            cuenta_retenta = 0;
        }
        //alert(cuenta_retenta);
        var formulario = document.forms["form_guardado"];
        var tipo_retencion = formulario.tipo_retencion.value;
        var valor_factura = formulario.valor_factura.value;
        var cuenta_iva = parseInt($("#cuenta_iva").val());
        if (isNaN(cuenta_iva)) {
            cuenta_iva = 0;
        }
        if (tipo_retencion != "" && valor_factura != "") {
            var midiv = document.createElement("tr");
            if (tipo == '2') {
                tipo = 'RENTA';
                conter = cuenta_retenta + 1;
                if (conter <= 2 && conter > 0) {
                    $("#cuenta_renta").val(conter);
                } else {
                    $("#cuenta_renta").val(2);
                }


            } else {
                tipo = 'IVA';
                contaiva = cuenta_iva + 1;
                if (contaiva <= 1 && contaiva > 0) {
                    $("#cuenta_iva").val(contaiva);
                } else {
                    $("#cuenta_iva").val(1);
                }

            }
            //alert(contaiva);
            var validate1 = true;
            if (conter > 2) {
                validate1 = false;
            }
            var validate2 = true;
            if (contaiva > 2) {
                validate2 = false;
            }
            var finx = 1;
            @if($empresa - > id == ' 0992704152001')
            if (!validate1 && !validate2) {
                finx = 0;
            }
            @endif
            if (finx == 1) {
            midiv.setAttribute("id", "dato" + id);
            midiv.innerHTML = '<td><input class="form-control input-sm"  name="base_imp' + id + '" id="base_imp' + id + '"readonly></td> <td><input class="form-control input-sm"   name="tipor' + id + '" id="tipor' + id + '" value="' + tipo + '" readonly></td> <td> <input class="form-control input-sm" name="codigor' + id + '" id="codigor' + id + '"  readonly ></td> <td> <input class="form-control input-sm"  name="porcentaje_retencion' + id + '" id="porcentaje_retencion' + id + '" readonly></td><td> <input style="width: 89%;" class="form-control input-sm " name="valor_retenido' + id + '" id="valor_retenido' + id + '" readonly></td> <input type="hidden" name="id_porcentaje' + id + '" id="id_porcentaje' + id + '"> <input type="hidden" name="porcentaje' + id + '" id="porcentaje' + id + '"> <td style="text-align:center;"><button style="text-align: center;" id="eliminar' + id + '" type="button" onclick="eliminar_registros(' + id + ')" class="btn btn-danger btn-xs btn-gray delete"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td> ';
            document.getElementById('datos_a').appendChild(midiv);
            id = parseInt(id);
            //alert(codigo);
            $("#codigor" + id).val(codigo);
            $("#tipo_p" + id).val(tipo);
            $("#valor_retenido" + id).val(valor_retenido);
            $("#porcentaje_retencion" + id).val(porcentaje);
            $("#base_imp" + id).val(total_factura);
            $("#id_porcentaje" + id).val(porcentaje);
            $("#porcentaje" + id).val(codigo)
            id = id + 1;
            document.getElementById('cont').value = id;
            suma_seccion(id);

            $("#agregar_item").attr("disabled", true);
            //alert("si funciona");
            } else {
                swal('Cantidad maxima permitida');
            }

        } else {
            swal("¡Error!", "Ingresa primero los datos", "error");
        }

    }

    function eliminar_registros(valor) {
        var dato1 = "dato" + valor;
        var total = 0;
        var contador_verdadero = document.getElementById('cont').value;
        var contador = parseInt(contador_verdadero);
        var contfinal = contador - 1;
        var cuenta_retenta = parseInt($("#cuenta_renta").val());
        var cuenta_referencia = $("#tipor" + valor).val();
        var total_renta = 0;
        var total_iva = 0;
        /*
        if(isNaN(cuenta_retenta)){ cuenta_retenta=0; }
        //alert(cuenta_retenta);
        var cuenta_iva=parseInt($("#cuenta_iva").val());
        if(isNaN(cuenta_iva)){ cuenta_iva=0; }
        if(contador_verdadero>1){
            total=valor;
        }else{
            total=1;
            //alert("aqui llega");
        }
        if(cuenta_referencia!='RENTA'){
            if(cuenta_iva>0&& cuenta_iva<=2){
            total_iva= cuenta_iva-1;
            $("#cuenta_iva").val(total_iva);
            }
        }else{
            if(cuenta_retenta>0 && cuenta_retenta <=2){
            total_renta=cuenta_retenta-1;
            $("#cuenta_renta").val(total_renta);
             }

        }*/
        document.getElementById('cont').value = contfinal;
        $("#dato" + valor).remove();
        var valor_en = parseInt(valor);
        $("#eliminados").val(1);

        suma_seccion();
    }

    function suma_seccion(cont) {
        var tipo = parseFloat($("#tipo_retencion").val());
        //alert(tipo);
        var contador = parseInt($("#cont").val());
        //alert(contador);
        var sumador = 0;
        var sumador2 = 0;
        for (i = 1; i < contador; i++) {
            var totales = parseFloat($("#valor_retenido" + i).val());
            var tipo = $("#tipor" + i).val();
            if (tipo == 'RENTA') {
                if ((totales) != NaN) {
                    sumador += totales;
                    //alert(totales)
                } else {
                    sumador = 0;
                }
            } else if (tipo == 'IVA') {
                if ((totales) != NaN) {
                    sumador2 += totales;
                    //alert(totales)
                } else {
                    sumador2 = 0;
                }
            }
            //alert(sumador2);
        }
        $("#valor_iva").val(sumador2.toFixed(2));
        $("#total_rfirt").val(sumador.toFixed(2));
        $("#total_rfivat").val(sumador2.toFixed(2));
        var totalx = sumador + sumador2;
        var find = parseFloat($("#saldo0").val());
        if (find == NaN) {
            find = 0;
        }
        var total_f = find - totalx;
        $("#abono0").val(totalx.toFixed(2, 2));
        $("#abono_base0").val(total_f.toFixed(2, 2));
        $("#valor_fuente").val(sumador.toFixed(2));
        var total = parseFloat(sumador2) + parseFloat(sumador);
        $("#total_egreso").val(total.toFixed(2, 2));
        $("#nuevo_saldo").val(total_f.toFixed(2, 2));
        $("#total_deudas").val(find.toFixed(2, 2));
        $("#total_abonos").val(total.toFixed(2, 2));
        $("#retencion_total").val(total);
    }
</script>


@endsection