@extends('contable.chequespost.base')
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
        location.href = "{{route('chequespost.index')}}";
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
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
            <li class="breadcrumb-item"><a href="{{route('chequespost.index')}}">Recibo de Cheques PostGirados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Recibo de Cheques PostGirados</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-3 col-6">
                            <div class="box-title"><b>VISUALIZADOR RECIBO DE CHEQUES POSTFECHADOS CLIENTES</b></div>
                        </div>
                        <div class="col-6">
                            
                            <div class="row">
                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$cheques->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                            </a>
                            <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$cheques->id])}}" target="_blank">
                                <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                            </a>
                                <a class="btn btn-success btn-gray" style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="padding: 3px 1px"></i>&nbsp;&nbsp;Regresar
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
                                <input class="col-md-12 col-xs-12" style="background-color: green;" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Id</label>
                                <input id="idx" type="text" class="form-control" value="@if(!is_null($cheques)) {{$cheques->id}} @endif" name="idx" readonly>

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Número</label>
                                <input class="form-control " type="text" name="numero" value="@if(!is_null($cheques)) {{$cheques->secuencia}} @endif" id="numero" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="CLI-CH">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha</label>
                                <div class="input-group col-md-12">
                                    <input class="col-md-12 col-xs-12 form-control " id="fecha" type="text" name="fecha" value="@if(!is_null($cheques)) {{$cheques->fecha}} @endif" readonly>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Asiento</label>
                                <input class="form-control " type="text" name="asiento" value="@if(!is_null($cheques)) {{$cheques->id_asiento_cabecera}} @endif" id="asiento" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12   ">
                        <div class="row  ">

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="acreedor">Cliente: </label>
                                <input type="text" id="id_cliente" name="id_cliente" value="@if(!is_null($cheques)) {{$cheques->id_cliente}} @endif" placeholder="Cédula" class="form-control form-control-sm id_cliente col-md-12" readonly>
                            </div>
                            <div class="col-md-4 col-xs-4 px-1">
                                <label class="col-md-12 label_header" for="valor">&nbsp;</label>
                                <input type="text" id="nombre_proveedor" name="nombre_proveedor" value="@if(!is_null($cheques->cliente)) {{$cheques->cliente->nombre}} @endif" placeholder="Nombre Cliente" class="form-control form-control-sm nombre_proveedor  col-md-12">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="valor">Valor:</label>
                                <input class="form-control" type="text" id="valor_total" placeholder="$ 0.00" autocomplete="off" name="valor_total" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($cheques)) {{$cheques->total_ingreso}} @endif">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Caja</label>
                                <select class="form-control select2_cuentas" style="width: 100%;" name="id_caja" id="id_caja" disabled>
                                    <option value="">Seleccione...</option>
                                    @foreach($caja as $value)
                                    <option {{$value->id == $cheques->id_caja ? 'selected' : ''}} value="0">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 px-1">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE CHEQUES RECIBIDOS</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1">
                        <input type="hidden" name="contador_a" id="contador_a" value="0">
                        <table id="example3" class="table-responsive" role="grid" aria-describedby="example2_info">
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

                                </tr>
                            </thead>
                            <tbody id="det_recibido">
                                @foreach($cheques->pago_ingresos as $value)
                                <tr>
                                    <td> <select class="form-control" id="tipo" style="width: 85%;" class="form-control" disabled>
                                            @foreach($tipo_pago as $v)
                                            <option {{$value->id_tipo == $v->id ? 'selected' : ''}} value="0">{{$v->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <b>{{$value->fecha}}</b>

                                    </td>
                                    <td>
                                        <b>{{$value->numero}}</b>
                                    </td>
                                    <td>
                                        @if($value->id_tipo==3 || $value->id_tipo==5)
                                        <select class="form-control" id="tipo" style="width: 85%; " class="form-control" disabled>

                                            <option value="">Seleccione...</option>
                                            @foreach($tipos as $vs)
                                            <option {{$value->id_tipo == $vs->id ? 'selected' : ''}} value="0">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <select class="form-control" id="tipo" style="width: 85%; " class="form-control" disabled>
                                            <option value="">Seleccione...</option>
                                            @foreach($lista_banco as $vs)
                                            <option {{$value->id_banco == $vs->id ? 'selected' : ''}} value="0">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>

                                        @endif
                                    </td>
                                    <td>
                                        <b>{{$value->cuenta}}</b>
                                    </td>
                                    <td>
                                        <b>{{$value->girador}}</b>
                                    </td>
                                    <td>
                                        <b>@if(!is_null($value->total)) {{$value->total}} @endif</b>
                                    </td>
                                    <td>
                                        <b>@if(!is_null($value->total)) {{$value->total}} @endif</b>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

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
                        <input class="form-control col-md-3" type="text" name="total_ingresos" value="@if(!is_null($cheques)) {{$cheques->total_ingreso}} @endif" id="total_ingresos" class="col-md-12" readonly>
                    </div>



                    <input type="text" name="contador" id="contador" value="0" class="hidden">
                    <input type="hidden" name="total_suma" id="total_suma">

                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1 " style="min-height: 250px; max-height: 250px;">
                        <table id="example2" class="table-responsive" role="grid" aria-describedby="example2_info">
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
                                @foreach ($detalle_cheques as $values)
                                <tr>
                                    <td>{{$values->id_factura}}</td>
                                    <td>{{$values->fecha}}</td>
                                    <td>VEN-FA</td>
                                    <td>{{$values->secuencia_factura}}</td>
                                    <td>@if(!is_null($values->observaciones)){{$values->observaciones}} @endif</td>
                                    <td>$</td>
                                    <td>{{$values->total_factura}}</td>
                                    <td>{{$values->total}}</td>
                                    @php $tot= $values->total_factura-$values->total; @endphp
                                    <td>{{number_format($tot,2)}}</td>
                                </tr>
                                @php $cont = $cont +1; @endphp
                                @endforeach

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
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
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="form-row">

                            <div class="form-group col-md-12">

                                <input class="form-control" type="text" name="autollenar" id="autollenar" autocomplete="off">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">Observaciones</label>
                                <textarea class="form-control" name="observaciones2" id="observaciones2" cols="30" rows="5"></textarea>
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

    </script>
</section>
@endsection