@extends('contable.debito_bancario.base')
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
        padding: 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    li.ui-menu-item {
        border-bottom: 1px solid #ccc;
        height: 30px;
        padding: 0 10px;
        line-height: 30px;
    }

    .t8 {
        font-size: 0.7rem;
    }

    .pv-10 {
        padding-bottom: 10px;
        padding-top: 10px;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{route('debitobancario.index')}}";
    }
</script>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Banco</a></li>
            <li class="breadcrumb-item"><a href="../debitobancario">D&eacute;bito Bancario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Crear D&eacute;bito Bancario (Acreedores)</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body dobra">
            <form class="form-vertical" id="form">
                {{ csrf_field() }}
                <div class="header row">
                    <div class="form-group col-xs-6 col-md-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">Id</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="">
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6 col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">N&uacute;mero</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="">
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">Tipo</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-ND-AC" readonly>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Asiento</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" value="">
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Proyecto</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="">
                            @if ($errors->has('proyecto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('proyecto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">Fecha</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha"  type="date" class="form-control" name="fecha_asiento" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" required />
                            @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0">
                            <label for="observacion" class="label_header">Concepto</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <!-- <input class="form-control" style="width: 210%;" type="text" name="aaa" id="aaa" > -->
                            <input id="observacion" type="text" class="form-control" name="observacion" value="{{ old('observacion') }}" required autofocus>
                            @if ($errors->has('observacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-8  col-md-4 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_banco" class="label_header">Banco</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <!-- banco -->
                            <select class="form-control" name="id_banco" id="id_banco" required>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa" class="label_header">Divisa</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="divisa" id="divisa" required>
                                @foreach($divisas as $value)
                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="cambio" class="label_header">Cambio</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="cambio" type="number" class="form-control" value="1.00" name="cambio" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor" class="label_header">Valor</label>
                        </div>
                        <div class="col-md-12 px-0">

                            <input class="form-control" type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onchange="abono_totales()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                        </div>
                    </div>
                    <div class=" col-md-2" style="text-align: center;">
                        <div class="col-md-12">&nbsp;</div>
                        <button type="button" class="btn btn-success btn-xs btn-gray" id="aplicar_deuda" onclick="boton_deuda();">Aplicar Deuda</button>
                    </div>


                    <div class="form-group col-xs-8  col-md-8  px-1">
                        <div class="col-md-12 px-0">
                            <label for="nombre_proveedor" class="label_header">Acreedor</label>
                        </div>
                        <div class="col-md-4 px-0">
                            <input type="text" id="id_proveedor" name="id_proveedor" class="form-control form-control-sm id_proveedor" autocomplete="off" onblur="cambiar_id_proveedor()">
                        </div>
                        <div class="col-md-8 px-0">
                            <select name="nombre_proveedor" id="nombre_proveedor" class="form-control form-control-sm select2_cuentas" style="width:100%" onchange="cambiar_nombre_proveedor(this);">
                                <option value="">Seleccione</option>
                                @foreach($proveedor as $value)
                                <option value="{{$value->id}}"> {{$value->id}} || {{$value->nombrecomercial}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 px-1">
                        <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                        <select name="sucursal" id="sucursal" class="form-control" required>
                            <option value="">{{trans('agenda.seleccione')}}..</option>
                            @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xs-12  col-md-12  px-1 pv-10">
                        <div class="col-md-12 px-0">
                            <label for="detalle" class="label_header text-left">Detalle de deudas con el proveedor</label>
                        </div>
                        <div class="table-responsive col-md-12 px-0" style="max-height: 250px; ">
                            <input type="hidden" name="contador" id="contador" value="0" />
                            <table id="example2" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead>
                                    <tr style="position: relative;">
                                        <th style="text-align: center;">Vence</th>
                                        <th style="text-align: center;">Tipo</th>
                                        <th style="text-align: center;">Número</th>
                                        <th style="text-align: center;">Concepto</th>
                                        <th style="text-align: center;">Div</th>
                                        <th style="text-align: center;">Saldo</th>
                                        <th style="text-align: center;">Abono</th>
                                        <th style="text-align: center;">Saldo Base</th>

                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 2) as $i)
                                    <tr>

                                        <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5;text-align: right  ;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                        <td> <input class="form-control" style="width: 150%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                        <!-- <td> <input class="form-control" type="text" name="check{{$cont}}" id="check{{$cont}}" readonly> </td>-->

                                    </tr>
                                    @php $cont = $cont +1; @endphp
                                    @endforeach

                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="form-group col-xs-2  col-md-2  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="total_debito" class="label_header">Total D&eacute;bito</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" id="total_debito" name="total_debito" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-2  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="debito_aplicado" class="label_header">D&eacute;bito Aplicado</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" id="debito_aplicado" name="debito_aplicado" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-2  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="total_deudas" class="label_header">Total Deudas</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="hidden" name="comprobarx" id="comprobarx" value="2">
                            <input type="text" id="total_deudas" name="total_deudas" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-2  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="total_abono" class="label_header">Total Abonos</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" id="total_abono" name="total_abono" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-2  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="nuevo_saldo" class="label_header">Nuevo Saldo</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" id="nuevo_saldo" name="nuevo_saldo" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-1  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="deficit" class="label_header">D&eacute;ficit</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" id="deficit" name="deficit" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-2  col-md-1  px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="debito_favor" class="label_header">D&eacute;bito a favor</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" name="debito_favor" id="debito_favor" class="form-control form-control-sm text-right" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0 t8">
                            <label for="nota" class="label_header">Nota</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="5"></textarea>
                            <input type="hidden" name="saldo_final" id="saldo_final">
                            <input type="hidden" name="proveedor" id="proveedor">
                            <input type="hidden" name="sobrante" id="sobrante">
                            <input type="hidden" name="total_favor" id="total_favor">
                            <input type="hidden" name="superavit" id="superavit">
                            <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                            <!--<input type="text" id = "nota" name="nota" class= "form-control form-control-sm">        -->
                        </div>
                        <input type="hidden" name="total_suma" id="total_suma">
                    </div>
                </div>
                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <!--<button type="submit"  class="btn btn-success btn-gray">
                            Guardar
                        </button>-->
                        <button onclick="guardar()" id="botong" type="button" class="btn btn-default btn-gray btn_add">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>







</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>


<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: "YYYY-MM-DD",
            defaultDate: '{{date("Y-m-d")}}',
            });
            
      });
    function bigImg(x) {
        x.style.height = "100%";
        x.style.background = "#1E88E5";
        x.style.color = "#fff";
        x.style.zIndex = "9999";
        x.style.position = "relative";
        x.style.width = "450%";
    }

    function normalImg(x) {
        x.style.height = "100%";
        x.style.width = "100%";
        x.style.color = "#000";
        x.style.zIndex = "";

        x.style.background = "#DDEBEE";
    }

    function buscar_factura() {
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url: "{{route('debitobancario.buscarcodigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_factura': $("#buscar").val()
            },
            success: function(data) {
                console.log(data);
                var iva = (data[10] * 0.12);
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[12]);
                $("#concepto").val(data[4] + '.' + ' ' + 'REF :' + data[0]);
                $("#asiento").val(data[11]);
                $("#acreedor").val(data[0] + ' ' + data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#total_deudas").val(data[10]);
                //$("#id_compra").val(data[1]);
                $("#vence").val(data[6]);
                $("#tipo").val(data[7]);
                $("#base_fuente").val(data[10]);
                $("#id_proveedor").val(data[0]);
                $("#nombre_proveedor").val(data[2]);

                for (i = 0; i < data[16].length; i++) {
                    $("#vence" + i).val(data[16][i].fecha_asiento);
                    $("#tipo" + i).val('FACT-COMPRA')
                    $("#numero_referencia" + i).val('Id:' + (data[16][i].id) + 'Sec:' + (data[16][i].fact_numero));
                    $("#base_fuente" + i).val(data[16][i].valor);
                    $("#nuevo_saldo" + i).val(data[16][i].valor_nuevo);
                    $("#divisas" + i).val(data[16][i].divisas);
                    $("#numero" + i).val(data[16][i].fact_numero);
                    $("#concepto" + i).val(data[16][i].observacion);
                    $("#saldo" + i).val((data[16][i].valor_nuevo));
                    $("#saldo_hidden" + i).val((data[16][i].valor_nuevo));
                    $("#tipo_rfiva" + i).val((data[16][i].id_porcentaje_iva));
                    $("#tipo_rfir" + i).val((data[16][i].id_porcentaje_ft));
                    var iva_base = parseFloat(data[16][i].valor);
                    var total_iva = iva_base * 12 / 100;
                    $("#base_iva" + i).val(total_iva.toFixed(2));
                }


            },
            error: function(data) {
                //console.log(data);
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

    function cambiar_nombre_proveedor(e) {
        $.ajax({
            type: 'post',
            url: "{{route('debitobancario.comprasproveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                console.log("data", data);
                $("#id_proveedor").val(data.value);

                buscar_proveedor()
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    $(".nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('debitobancario.buscarproveedor')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        minLength: 1,
    });

    function abono_totales() {

        /* var valor= parseFloat($("#valor_cheque").val());
         var saldo= parseFloat($("#saldo0").val());
         if(!isNaN(valor)){
             $("#abono0").val(valor);
             $("#abono_base0").val(valor);
             var totales= saldo-valor;
             //alert(totales);
             if(totales>0){
                 $("#saldo0").val(totales.toFixed(2));
                 var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                 var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                 $("#saldo_final").val(total_sinresta);
             }else{
                 var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                 var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                 $("#saldo_final").val(total_sinresta);
                 $("#saldo0").val('0');
             }
             
         }else{
             var valor =parseFloat($("#saldo_hidden0").val());
             //alert(valor);
             $("#saldo0").val(valor);
             $("#abono_base0").val(valor);
         }
         */
        var valor = parseFloat($("#valor_cheque").val());
        console.log(valor);
        $("#total_egreso").val(0);
        $("#debito_favor").val(valor);
        // debito_favor
        buscar_proveedor();

        /*if(!isNaN(valor)){
            if(valor>0){
            }else{
                swal("Error!","Por favor ingrese correctamente el valor","error");
            }
        }*/
    }

    function boton_deuda() {
        var valor = parseFloat($("#valor_cheque").val());
        var valor2 = parseFloat($("#valor_cheque").val());
        var valor_saldo = parseFloat($("#saldo0").val());
        var contador = parseInt($("#contador").val());
        var saldo = 0
        var abono = 0;
        var total = 0;
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
            //console.log(valor);
            if (valor > valor_saldo) {
                $("#abono" + i).val(valor_saldo.toFixed(2, 2));
            } else {
                total = valor + valor_saldo;
                // console.log(total+" anthonby");
                if (total <= valor2) {
                    if (total > 0) {
                        console.log("entra");
                        if (total > valor_saldo) {
                            total = valor_saldo;
                            $("#abono" + i).val(total.toFixed(2, 2));
                        } else {
                            $("#abono" + i).val(total.toFixed(2, 2));
                        }

                    } else {

                    }
                }



            }
            console.log("veces");
        }
        suma_totales();
        var total = 0;
        if (!isNaN(valor) && !isNaN(valor_saldo)) {

            $("#verificar_superavit").val(1);


        } else {
            swal("Error!", "Ingrese valor de cheque primero", "error");
        }
        $("#total_abono").val(valor2);

    }

    function guardar() {
        //swal("hassta aqui");
        var formulario = document.forms["form"];
        var valor_cheque = formulario.valor_cheque.value;
        var acreedores = formulario.id_proveedor.value;
        var id_banco = formulario.id_banco.value;
        var total_abono = formulario.total_abono.value;
        var total_suma = parseFloat($("#total_suma").val());
        var final_valor_cheque = parseFloat($("#valor_cheque").val());
        var superavit = parseInt($("#verificar_superavit").val());
        var sucursal = $("#sucursal").val();
        var msj = "";
        if (valor_cheque == "") {
            msj += "Por favor, Llene el valor del cheque<br/>";
        }
        if (acreedores == "") {
            msj += "Por favor, Llene el campo de acreedor<br/>";
        }
        if (id_banco == "") {
            msj += "Por favor, Llene el campo banco <br/>";
        }
        if (total_abono == "") {
            msj += "Por favor, Llene la tabla deudas acreedores <br/>";
        }
        if (sucursal == 0 || sucursal == "") {
            alertas("error", "Error...", "Seleccione la sucursal")
            return;
        }
        if (msj == "") {
            $('#botong').attr('disabled', 'disabled');
            if (total_suma < final_valor_cheque) {
                //  swal("¡Error!","El pago de las factura no cumple con el total del valor.","error");
                var resta_superavit = parseFloat(final_valor_cheque - total_suma);
                var resta_fixed = resta_superavit.toFixed(2, 2);
                $("#total_favor").val(resta_superavit);
                if (confirm('Existe un superávit de ' + resta_fixed + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?')) {

                    $("#total_favor").val(resta_superavit);
                    $("#superavit").val(0);
                    $("#comprobarx").val(1);
                    $.ajax({
                        type: 'post',
                        url: "{{route('debitobancario.generar')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#form').serialize(),
                        success: function(data) {
                            
                            if (data.status == "error") {

                                Swal.fire({
                                    title: 'Error',
                                    icon: 'error',
                                    html: data.error,

                                });
                                $('#botong').attr("disabled", false);
                            }
                            if(data.status == 'success') {
                                $("#id").val(data.iddebito);
                                $("#numero").val(data.idasiento);
                                swal("Correcto!", "El comprobante se generó con exito", "success");
                                document.getElementById("aplicar_deuda").disabled = true;
                                $('#form input').attr('readonly', 'readonly');
                                $('#botong').attr("disabled", false);
                            } 
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    })
                }
            } else {
                console.log("entro store");
                $.ajax({
                    type: 'post',
                    url: "{{route('debitobancario.generar')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#form').serialize(),
                    success: function(data) {

                        if (data.status == "error") {

                            Swal.fire({
                                title: 'Error',
                                icon: 'error',
                                html: data.error,

                            });
                            $('#botong').attr("disabled", false);
                        }

                        if(data.status == 'success') {
                            $("#id").val(data.iddebito);
                            $("#numero").val(data.idasiento);
                            swal("Correcto!", "El comprobante se generó con exito", "success");
                            document.getElementById("aplicar_deuda").disabled = true;
                            $('#form input').attr('readonly', 'readonly');
                            $('#botong').attr("disabled", false);
                        } 
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            }


        } else {
            Swal.fire({
                icon: 'error',
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }

    function generar() {
        //swal("hassta aqui");
        var vence = $("#vence0").val();
        var tipo = $("#tipo0").val();
        var numero = $("#numero0").val();
        var final_valor_cheque = $("#valor_cheque").val();
        var concepto = $("#concepto0").val();
        var saldo_final = $("#saldo_base0").val();
        // console.log($('#form_guardado').serialize());
        if (final_valor_cheque > 0) {
            $.ajax({
                type: 'post',
                url: "{{route('debitobancario.generar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_guardado').serialize(),
                success: function(data) {


                },
                error: function(data) {
                    // console.log(data);
                }
            })
        } else {
            swal("Correcto!", "Por favor ingrese correctamente los valores..", "error");
        }

    }

    function superavit() {
        var valor_final = $("#saldo0").val();
        var bono = $("#abono0").val();
        var proveedor = $("#id_proveedor").val();
        var pago = $("#formas_pago").val();
        var nuevo_saldo = $("#nuevo_saldo0").val();
        var saldo_final = $("#saldo_final").val();
        var secuencia_factura = $("#asiento").val();
        if (valor_final == 0) {
            if (confirm('Existe un superávit de' + saldo_final + 'en la cobertura de las deudas. \n Desea que éste valor sea considerado como un Débito a favor de la Empresa')) {
                $.ajax({
                    type: "post",
                    url: "{{route('debitobancario.superavit')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: "json",
                    data: {
                        'asiento': secuencia_factura,
                        'id_pago': pago,
                        'proveedor': proveedor,
                        'nuevo_saldo0': nuevo_saldo,
                        'saldo_final': saldo_final
                    },
                    success: function(data) {
                        $("#alerta_datos").fadeIn(1000);
                        $("#alerta_datos").fadeOut(3000);
                        swal("¡Correcto!", "Superavit creado correctamente", "success");
                    },
                    error: function() {
                        alert('error al cargar');

                    }
                });

            } else {
                swal("¡Correcto!", "Comprobante Guardado Correctamente", "success");
                //location.href ="{{route('acreedores_cegreso')}}";
            }
        } else {
            swal("¡Correcto!", "Guardado Correctamente", "success");
        }


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

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "> <input class='form-control' type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='Fact #:" + fact_numero + " Prov: " + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;'  id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control abonos' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")' value='0.00'></td>" +
            "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +

            "</tr>";
        return markup;

    }

    function addNewRowf(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            " <input class='form-control' type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id + "'> " +
            "<td> <input class='form-control' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;'  id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" + pos + "' value='" + valor + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control abonos' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' onchange='validar_td(" + pos + ")' value='0.00'></td>" +
            "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +

            "</tr>";
        return markup;

    }

    function goNew() {
        location.href = "{{route('debitobancario.crear')}}";
    }

    function validar_td(id) {
        console.log("aqui " + id);
        if ((id) != null) {
            $("#comprobarx").val(0);
            var valor = parseFloat($("#valor_cheque").val());
            var abono = parseFloat($("#abono" + id).val());
            var saldo = parseFloat($("#saldo" + id).val());
            suma_totales();
            var cantidad = parseFloat($("#total_suma").val());
            if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                if (cantidad <= valor) {
                    var uno = 1;
                    if (abono > saldo) {
                        abono = saldo;
                    }
                    $("#abono" + id).val(abono.toFixed(2, 2));
                } else {
                    valor = 0;
                    $("#abono" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "Error no puede superar al valor del cheque", "error")
                    suma_totales()
                }
            } else {
                abono = 0;
                valor = 0;
                $("#abono" + id).val(valor.toFixed(2, 2));
            }
            var uno = parseFloat($("#abono" + id).val());
            var tot = saldo - uno;
            $("#nuevo_saldo" + id).val(tot.toFixed(2, 2));
        } else {
            alert("error");
        }
    }

    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
    });

    function suma_totales() {
        var total=0;
        $('.abonos').each(function(i,obj){
            console.log($(this).val());
            total+= parseFloat($(this).val());
           
        });
        //$("#total_suma").val(total.toFixed(2,2));
        var cheque= $("#valor_cheque").val();
        var restante= cheque-total;
        $("#debito_favor").val(restante.toFixed(2,2));
        $("#total_suma").val(total.toFixed(2, 2));
        $("#total_abono").val(total.toFixed(2, 2));


        //alert(total_fin);

    }
    function validarCierre() {
        <?php
            if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha").value;
            //console.log(fecha2);
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                //console.log(fechaFinal,fechaInicial);
                swal("Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.', "error");
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha").value = fechaFormateada;
            }
        <?php
            }
        ?>
    }
</script>

@endsection