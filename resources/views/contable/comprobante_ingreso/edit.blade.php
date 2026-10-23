@extends('contable.comprobante_ingreso.base')
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
        history.back();
    }
</script>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
            <li class="breadcrumb-item"><a href="{{route('comprobante_ingreso.index')}}">Comprobante de ingreso Clientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visualizador Comprobante de ingreso Clientes</li>
        </ol>
    </nav>
    <form class="form-vertical" id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-56">
                            <div class="box-title"><b> VISUALIZADOR COMPROBANTE DE INGRESO CLIENTES</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                 <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante_ingreso->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante_ingreso->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>
                                <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;padding: 7px 30px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">

                <div class="row header">
                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-12">
                                &nbsp;
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="label_header" style="padding-left: 0px">Estado</label>
                                <input class="col-md-12 col-xs-12" style=" @if(($comprobante_ingreso->estado)==1) background-color: green; @else background-color: red; @endif" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Id</label>
                                <input id="idx" type="text" class="form-control" name="idx" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->id}} @endif" readonly>

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Número</label>
                                <input class="form-control " type="text" name="numero" id="numero" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->secuencia}} @endif" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="CLI-IN">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha</label>
                                <div class="input-group col-md-12">
                                    <input class="col-md-12 col-xs-12 form-control " id="fecha" name="fecha" type="date" value="@if(!is_null($comprobante_ingreso)){{date('Y-m-d',strtotime($comprobante_ingreso->fecha))}}@endif">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Asiento</label>
                                <input class="form-control " type="text" name="asiento" id="asiento" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->id_asiento_cabecera}} @endif" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">
                            <div class="col-md-2 col-xs-2 px-0">
                                <label class="col-md-12 label_header" for="acreedor">Cliente: </label>
                                <input type="text" id="id_cliente" name="id_cliente" placeholder="Cédula" class="form-control form-control-sm id_cliente  col-md-12" value="@if(!is_null($comprobante_ingreso->cliente)) {{$comprobante_ingreso->cliente->identificacion}} @endif">
                            </div>
                            <div class="col-md-4 col-xs-4 px-0">
                                <label class="col-md-12 label_header" for="valor">&nbsp;</label>
                                <input type="text" id="nombre_proveedor" name="nombre_proveedor" value="@if(!is_null($comprobante_ingreso->cliente)) {{$comprobante_ingreso->cliente->nombre}} @endif" placeholder="Nombre Cliente" class="form-control form-control-sm nombre_proveedor  col-md-12">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="valor">Valor:</label>
                                <input class="form-control" type="text" id="valor_total" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->total_ingreso}} @endif" autocomplete="off" placeholder="$ 0.00" name="valor_total" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="secuencia">Sec: </label>
                                <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(!is_null($comprobante_ingreso->sc)) {{$comprobante_ingreso->sc}} @endif">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="acreedor">O/S: </label>
                                <input class="form-control" type="text" id="os" name="os" value="@if(!is_null($comprobante_ingreso->os)) {{$comprobante_ingreso->os}} @endif">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="vendedor">Vendedor: </label>
                                <input type="text" id="id_vendedor1" name="id_vendedor1" disabled placeholder="Cédula" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->id_vendedor}} @endif" class="form-control form-control-sm id_vendedor  col-md-12">
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class="col-md-12 label_header" for="valor">&nbsp;</label>
                                <select class="form-control select2_cuentas" onchange="uservendedor()" name="id_vendedor" id="id_vendedor" style="width: 100%;">
                                    <option value="">Seleccione...</option>
                                    @foreach($user_vendedor as $v)
                                    <option {{$v->id == $comprobante_ingreso->id_vendedor ? 'selected' : ''}} value="0">{{$v->nombre1}} {{$v->apellido1}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE VALORES RECIBIDOS</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1">
                        <input type="hidden" name="contador_a" id="contador_a" value="0">
                        <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead class='well-dark'>
                                <tr style="position: relative;">
                                    <th text-align: center;">Tipo</th>
                                    <th style="text-align: center;">Fecha</th>
                                    <th style="text-align: center;">Número</th>
                                    <th style="text-align: center;">Banco</th>
                                    <th style="text-align: center;">Cuenta</th>
                                    <th style="text-align: center;">Girador</th>
                                    <th style="text-align: center;">Valor</th>
                                    <th style="text-align: center;">Valor B</th>
                                    <th>
                                        <button onclick="crea_td()" type="button" class="btn btn-success btn-gray btn-xs">
                                            <i style="height: 10px;" class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            @php
                            $contador=0;
                            @endphp
                            <tbody id="det_recibido">
                                @php 
                                    $pagos_ingreso = Sis_medico\Ct_Detalle_Pago_Ingreso::where("id_comprobante", $comprobante_ingreso->id)->whereNull("tipo_c")->get();
                                @endphp
                                {{--@foreach($comprobante_ingreso->pago_ingresos as $value)--}}
                                @foreach($pagos_ingreso as $value)
                                <tr>
                                    <td> <select class="form-control select2_cuentas" id="tipo{{$contador}}" name="tipo{{$contador}}" onchange="cambio_banco({{$contador}})" class="form-control">
                                            @foreach($tipo_pago as $v)
                                            <option {{$value->id_tipo == $v->id ? 'selected' : ''}} value="{{$v->id}}">{{$v->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>

                                        <input type="date" class="form-control" name="fecha{{$contador}}" id="fecha{{$contador}}" value="{{$value->fecha}}"> <input class="visibilidad" type="hidden" id="visibilidad{{$contador}}" name="visibilidad{{$contador}}" value="1" </td>
                                    <td>
                                        <input type="text" class="form-control" name="numero_a{{$contador}}" id="numero_a{{$contador}}" value="{{$value->numero}}">

                                    </td>
                                    <td>
                                        @if($value->id_tipo==3 || $value->id_tipo==5 || $value->id_tipo==2)
                                        @php //dd($lista_banco); @endphp


                                        <select class="form-control " name="banco{{$contador}}" id="banco{{$contador}}" class="form-control">
                                            <option value="">Seleccione...</option>
                                            
                                            @foreach($lista_banco as $vs)
                                                <option @if($vs->id==$value->id_banco) selected="selected" @endif value="{{$vs->id}}">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <select class="form-control " name="banco{{$contador}}" id="banco{{$contador}}" class="form-control">

                                            <option value="">Seleccione...</option>
                                            @foreach($tipos as $vs)
                                            <option {{$value->id_tipo_tarjeta == $vs->id ? 'selected' : ''}} value="{{$vs->id}}">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>

                                        @endif
                                    </td>
                                    <td>
                                        <input name="cuenta{{$contador}}" id="cuenta{{$contador}}" type="text" class="form-control" value="{{$value->cuenta}}">
                                    </td>
                                    <td>
                                        <input name="girador{{$contador}}" id="girador{{$contador}}" type="text" class="form-control" value="{{$value->girador}}">
                                    </td>
                                    <td>
                                        <input type="text" name="valor{{$contador}}" id="valor{{$contador}}" class="form-control" value="@if(!is_null($value->total)) {{$value->total}} @endif">
                                    </td>
                                    <td>
                                        <b>@if(!is_null($value->total)) {{$value->total}} @endif</b>
                                    </td>
                                    <td>
                                        <button style="text-align:center;" id="eliminar{{$contador}}" type="button" onclick="javascript:eliminar_registro({{$contador}})" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                                    </td>
                                    </td>

                                </tr>
                                @php
                                $contador++;
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" name="contador" id="contador" value="{{$contador}}">
                    <div class="col-md-12">
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
                                <input class="form-control col-md-3" type="text" name="total_ingresos" id="total_ingresos" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->total_ingreso}} @endif" class="col-md-12" readonly>

                            </div>
                        </div>
                    </div>

                 
                    <input type="hidden" name="total_suma" id="total_suma">
                    <input type="hidden" name="total_favor" id="total_favor">

                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1" style="min-height: 250px; max-height: 250px; ">
                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">DI</th>
                                    <th style="width: 8%; text-align: center;">Emisión</th>
                                    <th style="width: 5%; text-align: center;">Tipo</th>
                                    <th style="width: 2%; text-align: center;">Número</th>
                                    <th style="width: 20%; text-align: center;">Concepto</th>
                                    <th style="width: 5%; text-align: center;">Div</th>
                                    <th style="width: 5%; text-align: center;">Saldo</th>
                                    <th style="width: 5%; text-align: center;">Abono</th>
                                    <th style="width: 5%; text-align: center;">Nuevo Saldo</th>

                                </tr>
                            </thead>
                            <tbody id="crear">

                                @php $cont=0; @endphp
                                @foreach($detalle_ingreso as $values)
                                <tr>
                                    <td>{{$values->id_factura}}</td>
                                    <td>{{$values->fecha}}</td>
                                    @php 
                                        $vent= Sis_medico\Ct_ventas::find($values->id_factura);
                                    @endphp
                                    <td>@if(!is_null($vent)){{$vent->tipo}} @else VEN-FA @endif</td>
                                    <td>{{$values->secuencia_factura}}</td>
                                    <td>@if(!is_null($values->observaciones)){{$values->observaciones}} @endif</td>
                                    <td>$</td>
                                    <td>{{$values->total_factura}}</td>
                                    <td>{{$values->total}}</td>
                                    @php $tot= $values->total_factura-$values->total; @endphp
                                    <td>{{number_format($tot,2)}}</td>
                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12 px-1" style="text-align: center;">
                        <textarea name="autollenar" id="" cols="3" rows="3" class="form-control">{{$comprobante_ingreso->observaciones}}</textarea>
                        <!--<button style="margin-top: 4px;" formaction="{{route('comprobante_ingreso.update',['id'=>$comprobante_ingreso->id])}}" class="btn btn-success btn-gray"><i class="fa fa-save"></i> Actualizar</button>-->
                    </div>

                </div>


            </div>
    </form>

</section>
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
            increaseArea: '20%'
        });
        //$('.select2_cuentas').trigger("change");

    });

    function setNumber(e) {
        if (e == "") {
            e = 0;
        }
        $("#valor_total").val(parseFloat(e).toFixed(2))
        $("#total_ingresos").val(parseFloat(e).toFixed(2))
    }

    function uservendedor() {
        var vendedor = $("#id_vendedor").val();
        if (vendedor != "") {
            $("#id_vendedor1").val(vendedor);
        } else {
            $("#id_vendedor1").val("");
        }
    }

    function crea_td(contador) {
        var validar = parseFloat($("#valor_total").val());
        if (!isNaN(validar) && validar > 0) {
            id = document.getElementById('contador').value;
            var midiv = document.createElement("tr")
            midiv.setAttribute("id", "dato" + id);
            midiv.innerHTML = '<td><select class="cien form-control tipopago select2_cuentas2" onchange="cambio_banco(' + id + ')" name="tipo' + id + '" style="width: 100%;" id="tipo' + id + '" required> <option value="">Seleccione...</option> @foreach($tipo_pago as $value) <option value="{{$value->id}}">{{$value->nombre}}</option>  @endforeach </select></td> <td><input class="visibilidad" type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input name="fecha' + id + '" class="cien2 " style="" value="{{date("Y-m-d")}}" type="date" id="fecha' + id + '" ></td><td> <input style="width: 100%;  " class="cien3 form-control" name="numero_a' + id + '" id="numero_a' + id + '" autocomplete="off"> </td><td> <select style=" width: 100%; " class="cien form-control select2_cuentas2" required name="banco' + id + '" id="banco' + id + '"> </select></td><td><input style="width: 100%; " autocomplete="off" class="cien3 form-control" name="cuenta' + id + '" id="cuenta' + id + '" ></td><td><input class="cien3 form-control" style=" width: 100%; "  type="text" id="girador' + id + '" name="girador' + id + '"></td><td> <input style=" width:100%; " class="cien3 form-control " type="text" name="valor' + id + '" onchange="validar_td(' + id + ')"  id="valor' + id + '" autocomplete="off" ><td> <input style=" width: 100%; " class="cien3 form-control" type="text" required name="valor_base' + id + '" id="valor_base' + id + '" value="0.00" disabled> </td><td style="text-align: center;"><button style="text-align:center;" id="eliminar' + id + '" type="button" onclick="javascript:eliminar_registro(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
            document.getElementById('det_recibido').appendChild(midiv);
            llenar_girador(id);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;
            $('.select2_cuentas2').select2({
                tags: false
            });
        } else {
            swal("Error!", "Ingrese valor total ingreso", "error");
        }


    }

    function autollenarf() {
        var contador = parseInt($('#contador_a').val());
        var acumulador = "";
        for (i = 0; i <= contador; i++) {
            var concepto = $('#numero' + i).val();
            var abono = parseFloat($('#abono_a' + i).val());
            if (abono > 0) {
                acumulador += concepto + "  ";
            } else {

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

    function buscarAsiento(id_asiento) {
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '8'
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

    function cambio_banco(id) {
        if (id != null) {
            //alert(id);
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
                                console.log(data);
                                //remove it
                                eleman.disabled = false;
                                num.disabled = false;
                                cuenta.disabled = false;
                                $.each(data, function(key, registro) {
                                    console.log("entra");
                                    $("#banco" + id).append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
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
                num.disabled = true;
                cuenta.disabled = true;
            }

        } else {
            console.log("Error id null");
        }
    }

    function boton_deuda2() {
        var valor = parseFloat($("#valor_total").val());
        var valor_saldo = parseFloat($("#saldo_a0").val());
        var total = 0;
        if (!isNaN(valor) && !isNaN(valor_saldo)) {

            if (valor_saldo <= valor) {
                total = valor - valor_saldo;
                $("#verificar_superavit").val(1);
                $("#abono_a0").val(valor_saldo.toFixed(2, 2));
                suma_totales2();
            } else {
                total = valor_saldo - valor;
                $("#verificar_superavit").val(1);
                $("#abono_a0").val(valor.toFixed(2, 2));
                suma_totales2();
            }

        } else {
            swal("Error!", "Ingrese valor", "error");
        }
    }

    function boton_deuda() {
        var valor = parseFloat($("#valor_total").val());
        var valor2 = parseFloat($("#valor_total").val());
        var valor_saldo = parseFloat($("#saldo0").val());
        var contador = parseInt($("#contador_a").val());
        var saldo = 0
        var abono = 0;
        var total = 0;
        var nuevo_s = 0;
        var cero = 0;
        for (i = 0; i <= contador; i++) {
            saldo += parseFloat($("#saldo_a" + i).val());
            valor_saldo = parseFloat($("#saldo_a" + i).val());
            valor -= valor_saldo;
            var cont = parseFloat($("#abono_a" + i).val());
            if (isNaN(cont)) {
                cont = 0;
            }
            abono += cont;
            console.log(valor);
            if (valor > valor_saldo) {
                $("#abono_a" + i).val(valor_saldo.toFixed(2, 2));
                $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
                suma_totales2();
            } else {
                total = valor + valor_saldo;
                console.log(abono + " anthony");
                if (total <= valor2) {
                    if (total > 0) {
                        console.log("entra");
                        if (total > valor_saldo) {
                            total = valor_saldo;
                            $("#abono_a" + i).val(total.toFixed(2, 2));
                            $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
                            suma_totales2();
                        } else {
                            $("#abono_a" + i).val(total.toFixed(2, 2));
                            var saldo = parseFloat($('#saldo_a' + i).val()) - total;
                            $("#nuevo_saldo" + i).val(saldo.toFixed(2, 2));
                            suma_totales2();
                        }

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


        } else {
            swal("Error!", "Ingrese valor de cheque primero", "error");
        }
    }

    function eliminar_registro(valor) {
        var dato1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display = 'none';
        suma_totales();
    }

    function validar_tipo_pago() {
        var total = 0;
        $(".tipopago").each(function() {
            if ($(this).val() == "") {
                total++;
            }
        });

        return total;
    }

    function validate2() {
        var contador = $("#contador").val();

        if (contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function guardar() {

        var formulario = document.forms["crear_factura"];
        var proveedor = formulario.id_cliente.value;
        //var vendedor= formulario.id_vendedor.value;

        var fecha = formulario.fecha.value;
        var msj = "";
        var contador_a = formulario.contador_a.value;
        var contador = formulario.contador.value;
        var valor_total = formulario.valor_total.value;
        var validacion = validar_tabla1();
        var sumas = parseFloat($("#total_suma_a").val());
        var total_ingreso = parseFloat($("#total_ingresos").val());
        var superavit = parseInt($("#verificar_superavit").val());
        var validar = validar_tipo_pago();
        var invoices = disabled();
        console.log(invoices);
        var vad = parseFloat($("#total_suma").val());
        if (isNaN(vad)) {
            vad = 0;
        }
        var validacionf = validacion_campos();
        //console.log(vad);
        if (proveedor == "") {
            msj += "Por favor, Llene el campo id cliente <br/>";
        }

        if (fecha == "") {
            msj += "Por favor, Llene la fecha <br/>";
        }
        if (vad < total_ingreso) {
            msj += "Por favor, Complete el valor de los valores recibidos. <br/>";
        }
        if (contador_a == "") {
            msj += "Por favor, Llene los campos faltantes de la tabla <br/>";
        }
        if (valor_total == "") {
            msj += "Por favor, Llene el campo valor <br/>";
        }

        if (msj == "") {
            var data = $('form#crear_factura').serializeArray();
            data.push({
                name: 'listInvoice',
                value: invoices
            });
            if (superavit == "1") {

                if (sumas < total_ingreso) {
                    var resta_superavit = parseFloat(total_ingreso - sumas);
                    var resta_fixed = resta_superavit.toFixed(2, 2);
                    $("#total_favor").val(resta_superavit);
                    if (confirm('Existe un superávit de ' + resta_fixed + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?')) {
                        $("#total_favor").val(resta_superavit);
                        if ($("#crear_factura").valid()) {
                            $("#boton_guardar").attr("disabled", "disabled");
                            $.ajax({
                                type: 'post',
                                url: "{{route('comprobante_ingreso.store')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                datatype: 'json',
                                data: data,
                                success: function(data) {
                                    swal("Correcto!", "Se creo el comprobante correctamente", "success");
                                    $('#crear_factura input').attr('readonly', 'readonly');
                                    $("#boton_guardar").attr("disabled", true);
                                    $("#idx").val(data);
                                    buscarAsiento(data);
                                    $("#asiento").val(data);
                                    url = "{{ url('contable/cliente/comprobante/ingreso/pdf/')}}/" + data;
                                    window.open(url, '_blank');
                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            })
                        }

                    } else {
                        Swal("Por favor reingrese el guardado");
                    }
                    //swal("Error!","El saldo del total no cumple con el pago de las factura.","error");
                } else {
                    console.log("store");

                    if ($("#crear_factura").valid()) {
                        $("#boton_guardar").attr("disabled", "disabled");
                        $.ajax({
                            type: 'post',
                            url: "{{route('comprobante_ingreso.store')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: data,
                            success: function(data) {
                                swal("Correcto!", "Se creo el comprobante correctamente", "success");
                                $('#crear_factura input').attr('readonly', 'readonly');
                                $("#boton_guardar").attr("disabled", true);
                                $("#idx").val(data);
                                buscarAsiento(data);
                                $("#asiento").val(data);
                                url = "{{ url('contable/cliente/comprobante/ingreso/pdf/')}}/" + data;
                                window.open(url, '_blank');
                            },
                            error: function(data) {
                                console.log(data);
                            }
                        })
                    }

                }


            } else if (superavit == "0") {

                //console.log("superavit");
                $("#total_favor").val(total_ingreso);
                if ($("#crear_factura").valid()) {
                    $("#boton_guardar").attr("disabled", "disabled");
                    $.ajax({
                        type: 'post',
                        url: "{{route('comprobante_ingreso.superavit')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: data,
                        success: function(data) {
                            //console.log(data);
                            swal("Correcto!", "Se creo el comprobante correctamente", "success");
                            $('#crear_factura input').attr('readonly', 'readonly');
                            $("#boton_guardar").attr("disabled", true);
                            $("#idx").val(data);
                            buscarAsiento(data);
                            $("#asiento").val(data);
                            url = "{{ url('contable/cliente/comprobante/ingreso/pdf/')}}/" + data;
                            window.open(url, '_blank');
                        },
                        error: function(data) {
                            //console.log(data);
                        }
                    });
                }

            }

        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }

    function disabled() {
        //tocreate array
        contador = parseInt($("#contador_a").val());
        var complex = [];
        var a = ['abono', 'id'];
        for (i = 0; i <= contador; i++) {
            //console.log("hi");
            if (parseFloat($("#abono_a" + i).val()) > 0) {
                //console.log("holaaaaa");
                //complex=[{abono:$("#abono_a"+i).val(),id:$("#vence"+i).val()},];
                //complex[a[$("#abono_a"+i).val()]] = $("#vence"+i).val();
                //complex.push(complex);
                complex.push({
                    abono: $("#abono_a" + i).val(),
                    id: $("#vence" + i).val(),
                    numero: $("#numero" + i).val(),
                    saldo: $("#saldo_a" + i).val()
                });
            }
        }
        console.log(complex);
        //$("#invoces").val(complex);
        return JSON.stringify(complex);
    }

    function validar_tabla1() {
        var contador = parseInt($('#contador').val());
        var validacion = 0;
        if (!isNaN(contador)) {
            for (i = 0; i <= contador; i++) {
                var tipo = $("#tipo" + i).val();
                if (tipo == undefined) {
                    validacion++;
                }
            }

            if (validacion > 0) {
                return 'error';
            } else {
                return 'ok';
            }

        } else {
            console.log("Error contador de la tabla 1");
        }
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
                    if (data[4] != null) {
                        for (i = 0; i < data[4].length; i++) {

                            var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, data[4][i].tipo, data[4][i].nro_comprobante, data[4][i].nro_comprobante + " " + data[4][i].concepto, data[4][i].valor_contable, data[4][i].id);
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
            console.log("entra aqui");
            var cantidad = parseFloat($("#total_suma").val());
            if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
                if (cantidad <= valor) {
                    $("#valor" + id).val(abono.toFixed(2, 2));
                    suma_totales2();
                } else {
                    valor = 0;
                    $("#valor" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "Error no puede superar al valor", "error")
                    suma_totales2();
                }
            } else {
                abono = 0;
                valor = 0;
                $("valor" + id).val(valor.toFixed(2, 2));
            }
        } else {
            alert("error");
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
                    var tot = saldo - abono;
                    $("#abono_a" + id).val(abono.toFixed(2, 2));
                    $("#nuevo_saldo" + id).val(tot.toFixed(2, 2));
                } else {
                    valor = 0;

                    $("#abono_a" + id).val(valor.toFixed(2, 2));
                    $("#nuevo_saldo" + id).val(valor.toFixed(2, 2));
                    swal("¡Error!", "Error no puede superar al valor del cheque", "error")
                }
            } else {
                abono = 0;
                valor = 0;
                $("#abono_a" + id).val(valor.toFixed(2, 2));
                $("#nuevo_saldo" + id).val(valor.toFixed(2, 2));
            }
        } else {
            alert("error");
        }
    }

    function validacion_campos() {
        var contador = 0;
        var iva = parseFloat($("#iva_par").val());
        var validar1 = 0;
        var validar2 = 0;
        var validar3 = 0;
        var validar4 = 0;
        var ivan = 0;
        var total = 0;
        var totaal = 0;
        var sub = 0;
        var valor_d = 0;
        var ivaf = 0;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            var fecha1 = $(this).find("#fecha" + contador).val();
            var valor = parseFloat($(this).find("#valor" + contador).val());
            var tipo = $(this).find("#tipo" + contador).val();
            var girador = $(this).find("#girador" + contador).val();
            if (visibilidad == 1) {
                if (fecha1 == "") {
                    validar1++;
                }
                if (tipo == "") {
                    validar4++;
                }
                if (valor == 0) {
                    validar3++;
                }
            }
            contador = contador + 1;
        });
        var totales = validar1 + validar2 + validar3 + validar4;
        console.log(totales);
        if (totales > 0) {
            return false;
        }
        return true;
    }

    function suma_totales2() {
        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;
        //console.log("entra aqui");
        $("#crear tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#abono_a" + contador).val());
            visibilidad = $(this).find('#visibilidad' + contador).val();

            if (!isNaN(cantidad)) {
                total += cantidad;
            }


            contador = contador + 1;
        });
        if (isNaN(total)) {
            total = 0;
        }
        $("#total_suma_a").val(total.toFixed(2, 2));
    }

    function suma_totales3() {
        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;

        $("#crear_a tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#saldo_a" + contador).val());
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                if (!isNaN(cantidad)) {
                    total += cantidad;
                }
            }

            contador = contador + 1;
        });
        if (isNaN(total)) {
            total = 0;
        }
        $("#saldoax").val(total.toFixed(2, 2));
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
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                if (!isNaN(cantidad)) {
                    total += cantidad;
                }
            }

            contador = contador + 1;
        });
        if (isNaN(total)) {
            total = 0;
        }
        $("#total_suma").val(total.toFixed(2, 2));
    }

    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, ids) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input class='form-control' type='text' name='vence[]' id='vence" + pos + "' disabled value='" + ids + "'> </td>" +
            "<td> <input class='form-control' type='text' name='emision[]' id='emision" + pos + "' value='" + fecha + "' disabled> </td>" + "<td> <input class='form-control' type='text' name='tipo_a[]' id='tipo_a" + pos + "' value='" + factura + "' disabled> </td>" +
            "<input class='form-control' type='hidden' name='id_actualiza[]' value='" + ids + "' disabled> " +
            "<td> <input class='form-control' type='text' name='numero[]' id='numero" + pos + "' value='" + fact_numero + "' disabled> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;'  onmouseover='bigImg(this)' onmouseout='normalImg(this)' name='observacion[]' id='observacion" + pos + "' value='Fact:" + fact_numero + " Ref: " + observacion + "' disabled> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' value='$' disabled> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo_a[]' value='" + valor + "' id='saldo_a" + pos + "' disabled> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' id='abono_a" + pos + "' onchange='validar_td2(" + pos + ")'></td>" +
            "<td> <input  class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo[]' value='" + valor + "' id='nuevo_saldo" + pos + "' disabled></td>" +
            "</tr>";
        return markup;
    }

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
        x.style.background = "#DDEBEE";
    }

    function addNewRow2(pos, fecha, tipo, fact_numero, observacion, valor) {
        var markup = "";
        var num = parseInt(pos) + 1;
        markup = "<tr>" +
            "<td> <input type='text' name='emision" + pos + "' id='emision" + pos + "' readonly='' value='" + fecha + "'> </td>" +
            "<td> <input type='text'  style='width: 100%;' name='tipos" + pos + "' id='tipos" + pos + "' value='" + tipo + "' readonly=''> </td>" +
            "<td> <input type='text' name='numero_a" + pos + "' id='numero_a" + pos + "' value='" + fact_numero + "' readonly=''> </td>" +
            "<td> <input type='text'  onmouseover='bigImg(this)' onmouseout='normalImg(this)' style='width: 100%;' name='concepto_a" + pos + "' id='concepto_a" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; ' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; ' name='saldo_a" + pos + "' value='" + valor + "' id='saldo_a" + pos + "' readonly=''> </td>" +
            "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' name='abono_a" + pos + "' id='abono_a" + pos + "' onchange='validar_saldos(" + pos + ")'></td>" +
            "<td> <input type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='0.00'  id='nuevo_saldo_a" + pos + "' readonly=''><input type='hidden' name='visibilidad" + pos + "' id='visibilidad" + pos + "' value='0'></td>" +
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
                //console.log(data);
                if (data.value != "no") {
                    $("#crear_a").empty();
                    var fila = 0;
                    for (i = 0; i < data.length; i++) {
                        var row = addNewRow2(i, data[i].fecha_asiento, 'ACR-EG', data[i].secuencia, data[i].observacion, data[i].valor_abono);
                        $('#example3').append(row);
                        fila = i;
                    }
                    $("#contador").val(fila);
                }
            },
            error: function(data) {
                console.log(data);

            }
        });
    }

    function nuevo_comprobante() {
        location.href = "{{route('comprobante_ingreso.create')}}";
    }
</script>
@endsection