@extends('contable.debito_bancario.base')
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

    function Imprimir() {
        window.open("{{route('imprimir_pdf',[$registro->id])}}", '_blank');

    }

    function enviar_correo() {
        //Swal.fire("Enviando correo...","----","info");
        /*$.ajax({
            type: "GET",
            url: "{{route('debitoBancario.envioCorreo',['id'=>$registro->id])}}",
            data: $("#form").serialize(),
            datatype: "json",
            success: function(data) {
                console.log(data);
                if (data == "ok") {
                    Swal.fire("Envio correcto");
                }

            },
            error: function() {
                alert('error al cargar');
            }
        });*/
        Swal.fire({
            title: '¿Desea enviar correo a {{$registro->proveedor->razonsocial}} ?',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return fetch(`{{route('debitoBancario.envioCorreo',['id'=>$registro->id])}}`)
                    .then(response => {
                        //console.log(response);
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire("Bien!","Envio correcto","success");
            }
            
        })

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
            <li class="breadcrumb-item"><a href="#">Banco</a></li>
            <li class="breadcrumb-item"><a href="../../debitobancario">D&eacute;bito Bancario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-3">
                <h3 class="box-title">Ver D&eacute;bito Bancario</h3>
            </div>
            <div class="col-md-6 text-right">
            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$registro->id_asiento])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
        <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
</a>
<a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$registro->id_asiento])}}" target="_blank">
    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
</a>
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="Imprimir()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-download" aria-hidden="true"></i>&nbsp;&nbsp;Imprimir
                </button>
            </div>
            <!--formaction="{{route('debitoBancario.envioCorreo',['id'=>$registro->id])}}" -->
            @if($registro->estado==1)
            <div class="col-md-1 text-right">
                <button type="button" onclick="enviar_correo()" class="btn btn-default btn-gray"><i class="fa fa-envelope"></i> &nbsp; &nbsp; Enviar </button>
            </div>
            @endif
        </div>
    </div>
    <div class="box-body dobra">
        <form class="form-vertical" method="post">
        {{ csrf_field() }}
        <div class="header row">
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="id" class=" label_header">Id</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" class="form-control" name="id" id="id" value="@if(!is_null($registro)){{$registro->id}}@endif" disabled>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="numero" class=" label_header">N&uacute;mero</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" class="form-control" name="numero" id="numero" value="@if(!is_null($registro)){{$registro->id}}@endif" disabled>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="tipo" class="label_header">Tipo</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" class="form-control" name="tipo" id="tipo" value="@if(!is_null($registro)){{$registro->tipo}}@endif" readonly>
                    @if ($errors->has('tipo'))
                    <span class="help-block">
                        <strong>{{ $errors->first('tipo') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="asiento" class="label_header">Asiento</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" class="form-control" name="asiento" id="asiento" value="@if(!is_null($registro)){{$registro->id_asiento}}@endif" disabled>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="asiento" class="label_header">Proyecto</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="">
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="fecha_asiento" class="label_header">Fecha</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="fecha" type="date" class="form-control" name="fecha_asiento" value="@if(!is_null($registro)){{$registro->fecha}}@endif" >
                </div>
            </div>
            <div class="form-group col-xs-12 px-1">
                <div class="col-md-12 px-0">
                    <label for="observacion" class="label_header">Concepto</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="observacion" type="text" class="form-control" name="observacion" value="@if(!is_null($registro)){{$registro->concepto}}@endif" >
                </div>
            </div>
            <div class="form-group col-xs-4 px-1">
                <div c  lass="col-md-12 px-0">
                    <label for="id_banco" class="label_header">Banco</label>
                </div>
                <div class="col-md-12 px-0">
                   
                    <select class="form-control " name="id_banco" id="id_banco" required>
                                            <option value="">Seleccione..</option>
                                            @foreach($banco as $value)
                                                <option {{$value->id == $registro->id_banco ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                   </select>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="id_divisa" class="label_header">Divisa</label>
                </div>
                <div class="col-md-12 px-0">
                    <select class="form-control" name="divisa" id="divisa">
                        @foreach($divisas as $value)
                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="cambio" class="label_header">Cambio</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="cambio" type="number" class="form-control" value="1.00" name="cambio" >
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="valor" class="label_header">Valor</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="valor" type="text" class="form-control" name="valor" value="{{number_format($registro->valor,2)}}" readonly autofocus>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="estado" class="label_header">Estado</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="estado" type="text" class="form-control" name="estado" value="@if($registro->estado==1)Activa @else Anulada @endif" readonly autofocus>
                </div>
            </div>
            <div class="form-group col-xs-8  col-md-8  px-1">
                <div class="col-md-12 px-0">
                    <label for="nombre_proveedor" class="label_header">Acreedor</label>
                </div>
                <div class="col-md-4 px-0">
                    <input type="text" id="id_proveedor" name="id_proveedor" class="form-control form-control-sm id_proveedor" value="{{$registro->proveedor->id}}" readonly>
                </div>
                <div class="col-md-8 px-0">
                    <input type="text" id="nombre_proveedor" name="nombre_proveedor" class="form-control form-control-sm nombre_proveedor" value="{{$registro->proveedor->razonsocial}}" readonly>
                </div>
            </div>
            
            <div class="form-group col-xs-12  col-md-12  px-1 pv-10">
                <div class="col-md-12 px-0">
                    <label for="detalle" class="label_header text-left">Detalle de deudas con el proveedor</label>
                </div>
                <div class="table-responsive col-md-12 px-0" @if(count($detalle)>0) style="min-height: 250px; max-height: 250px;" @endif>
                    <input type="hidden" name="contador" id="contador" value="0" />
                    <table id="example2" role="grid" aria-describedby="example2_info">

                        <tr style="position: relative;">
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 10%; text-align: center;">Vence</th>
                            <th style="width: 12%; text-align: center;">Tipo</th>
                            <th style="width: 10%; text-align: center;">Número</th>
                            <th style="width: 18%; text-align: center;">Concepto</th>
                            <th style="width: 5%; text-align: center;">Div</th>
                            <th style="width: 8%; text-align: center;">Saldo</th>
                            <th style="width: 8%; text-align: center;">Abono</th>
                            <th style="width: 8%; text-align: center;">Saldo Base</th>
                            <th style="width: 8%; text-align: center;">Abono Base</th>

                        </tr>
                        </thead>
                        <tbody id="crear">
                            @foreach($detalle as $value)
                            @php $cont=1; @endphp
                            <tr class="well" style="position: relative;">
                                <td> <input class="form-control" type="text" name="id{{$cont}}" id="id{{$cont}}" value="{{$cont}}" readonly>
                                <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" value="{{$value->fecha_vencimiento}}" readonly> </td>
                                <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" value="{{$value->tipo}}" readonly> </td>
                                <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" value="{{$value->numero}}" readonly> </td>
                                <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" value="{{$value->concepto}}" readonly> </td>
                                <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                @php 
                                    $tot= $value->saldo-$value->abono;
                                @endphp
                                <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" value="{{$value->saldo}}" readonly> </td>
                                <td> <input class="form-control" style="background-color: #c9ffe5;text-align: right  ;" type="text" name="abono{{$cont}}" value="{{$value->abono}}" id="abono{{$cont}}" readonly></td>
                                <td> <input class="form-control" style="text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" value="{{number_format($tot,2)}}" readonly></td>
                                <td> <input class="form-control" style="text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" value="{{number_format($tot,2)}}" readonly> </td>

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
                    <input type="text" id="total_debito" name="total_debito" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->total_debito,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-2  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="debito_aplicado" class="label_header">D&eacute;bito Aplicado</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" id="debito_aplicado" name="debito_aplicado" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->debito_aplicado,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-2  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="total_deudas" class="label_header">Total Deudas</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" id="total_deudas" name="total_deudas" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->debito_aplicado,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-2  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="total_abono" class="label_header">Total Abonos</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" id="total_abono" name="total_abono" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->total_abono,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-2  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="nuevo_saldo" class="label_header">Nuevo Saldo</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" id="nuevo_saldo" name="nuevo_saldo" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->nuevo_saldo,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-1  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="deficit" class="label_header">D&eacute;ficit</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" id="deficit" name="deficit" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->deficit,2)}}@endif" readonly>
                </div>
            </div>
            <div class="form-group col-xs-2  col-md-1  px-1">
                <div class="col-md-12 px-0 t8">
                    <label for="debito_favor" class="label_header">D&eacute;bito a favor</label>
                </div>
                <div class="col-md-12 px-0">
                    <input type="text" name="debito_favor" id="debito_favor" class="form-control form-control-sm text-right" value="@if(!is_null($registro)){{number_format($registro->debito_favor,2)}}@endif" readonly>
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
                    <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                    <!--<input type="text" id = "nota" name="nota" class= "form-control form-control-sm">        -->
                </div>
            </div>
            <div class="form-group col-md-12" style="text-align: center;">
                <button type="submit" class="btn btn-success btn-gray" formaction="{{route('debitobancario.update',['id'=>$registro->id])}}" > <i class="fa fa-save"></i> &nbsp; Actualizar </button>            
            </div>
        </div>
        
        </form>




</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
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




    /* $(".buscar").autocomplete({
         source: function( request, response ) {
             $.ajax( {
             url: "{{route('retenciones_codigo')}}",
             dataType: "json",
             data: {
                 term: request.term
             },
             success: function( data ) {
                 response(data);
                 //console.log(data);
             }
             } );
         },
         minLength: 1,
     } );*/

    function setNumber(e) {
        // return parseFloat(e).toFixed(2);
        //if(e.length)
        if (e == "") {
            e = 0;
        }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('debitobancario.comprasproveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
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
        debito_favor
        buscar_proveedor();

        /*if(!isNaN(valor)){
            if(valor>0){
            }else{
                swal("Error!","Por favor ingrese correctamente el valor","error");
            }
        }*/
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
                    //console.log(data);
                    /*if((data)!='false'){
                        $("#alerta_datos").fadeIn(1000);
                        $("#alerta_datos").fadeOut(3000);
                        $("#vence0").val(vence);
                        $("#tipo0").val(tipo);
                        $("#numero0").val(numero);
                        $("#saldo0").val(saldo_final);
                        $("#nuevo_saldo0").val(saldo_final);
                        $("#abono_base0").val(saldo_final);      
                        //swal("Guardado correcto");
                        superavit()                  
                    }else{
                        
                    }   */
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
        $.ajax({
            type: "post",
            url: "{{route('debitobancario.buscardatosproveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                'proveedor': proveedor
            },
            success: function(data) {
                if (data.value != "no") {
                    $("#crear").empty();
                    var fila = 0;
                    for (i = 0; i < data[5].length; i++) {
                        var row = addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor, 'FCT-COMPRA', data[5][i].fact_numero, data[5][i].observacion, data[5][i].valor_nuevo);
                        $('#example2').append(row);
                        fila = i;
                    }
                    console.log("total es:", fila);
                    $("#contador").val(fila);
                }
                $(".checkVal").click(function() {
                    if ($(this).is(':checked')) {
                        var valor = parseFloat($("#valor_cheque").val()).toFixed(2);
                        var total_egreso = parseFloat($("#total_egreso").val()).toFixed(2);
                        valor = valor - total_egreso;
                        var item = $(this).attr('name');
                        item = item.substring(5);
                        var saldo = $("#saldo" + item).val();
                        var resta = parseFloat(saldo).toFixed(2) - valor;
                        var debo = 0;
                        if (resta <= 0) {
                            debo = resta * -1;
                            resta = valor;
                            total_egreso = parseFloat(saldo) + parseFloat(total_egreso);
                            $("#total_egreso").val(total_egreso.toFixed(2));
                            $("#debito_favor").val(debo.toFixed(2));
                            $("#abono" + item).val(saldo);
                        } else {

                            $("#total_egreso").val(parseFloat($("#valor_cheque").val()).toFixed(2));
                            $("#debito_favor").val(debo.toFixed(2));
                            $("#abono" + item).val(valor.toFixed(2));
                        }
                    } else {
                        var item = $(this).attr('name');
                        item = item.substring(5);
                        var valor_inicial = $("#abono" + item).val();
                        var total_egreso = parseFloat($("#total_egreso").val()).toFixed(2);
                        var debito = parseFloat($("#debito_favor").val()).toFixed(2);
                        $("#abono" + item).val(0);
                        $("#total_egreso").val(total_egreso - valor_inicial);
                        $("#debito_favor").val(parseFloat(debito) + parseFloat(valor_inicial));
                    }
                });
                //swal("Correcto!", "Superavit creado correctamente", "success");                  
            },
            error: function(data) {
                console.log(data);

            }
        });



    }

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +

            "<td> <input class='form-control' type='text' name='pos" + pos + "' id='pos" + pos + "' readonly='' value='" + num + "'> </td>" +
            "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "<td> <input class='form-control' type='text' name='tipo" + pos + "' id='tipo" + pos + "' value='" + factura + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='concepto" + pos + "' id='concepto" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; width: 150%;' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; width: 150% ' name='saldo" + pos + "' value='" + valor_nuevo + "' id='saldo" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; width: 150%; text-align: center;' name='abono" + pos + "' id='abono" + pos + "' readonly=''></td>" +
            "<td> <input class='form-control' type='text' style='width: 150%; text-align: left;' name='nuevo_saldo" + pos + "' value='" + valor + "' id='nuevo_saldo" + pos + "' readonly=''></td>" +
            "<td> <input class='form-control' type='text' style='text-align: center;' name='abono_base" + pos + "' id='abono_base" + pos + "' readonly=''> </td>" +
            "<td> <div class='form-control'><input class='checkVal' type='checkbox' name='check" + pos + "' id='check" + pos + "' > </div></td>" +
            "</tr>";
        return markup;

    }
</script>

@endsection