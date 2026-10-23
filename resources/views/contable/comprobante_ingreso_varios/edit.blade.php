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
<div class="modal fade bd-example-modal-lg" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
            <li class="breadcrumb-item active" aria-current="page">Visualizador Comprobante de ingreso Clientes</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-3">
                            <div class="box-title"><b>VISUALIZADOR - COMPROBANTE DE INGRESO VARIOS CLIENTES</b></div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante_ingreso->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante_ingreso->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="padding:2px 7px"></i>&nbsp;&nbsp;Regresar
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
                                <input class="col-md-12 col-xs-12" style="@if(($comprobante_ingreso->estado)==1) background-color: green; @else background-color: red; @endif" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Id</label>
                                <input id="idx" type="text" class="form-control" name="idx" readonly>

                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Número</label>
                                <input class="form-control " type="text" name="numero" id="numero" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->secuencia}} @endif" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Tipo</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" readonly value="ACR-CR-AF">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="control-label label_header">Fecha</label>
                                <div class="input-group col-md-12">
                                    <input class="col-md-12 col-xs-12 form-control " id="fecha" type="text" name="fecha" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->fecha}} @endif" readonly>
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
                            <div class="col-md-12 col-xs-12 px-1">
                                <label class="col-md-12 label_header" for="valor">Concepto</label>
                                <input type="text" id="concepto" name="concepto" placeholder="concepto" autocomplete="off" class="form-control concepto  col-md-12" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->concepto}} @endif" readonly>
                            </div>
                            <div class="col-md-4 col-xs-2 px-0">
                                <label class="col-md-12 label_header" for="banco">Banco: </label>
                                <select class="form-control select2_cuentas" onchange="banco_informacion()" name="id_banco" id="id_banco" disabled>
                                    <option value="">Seleccione...</option> @foreach($bancos as $v) <option {{$v->id == $comprobante_ingreso->id_banco ? 'selected' : ''}} value="0">{{$v->nombre}} </option>
                                    </option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-xs-2 px-0">
                                <label class="col-md-12 label_header" for="acreedor">&nbsp;</label>
                                <input class="form-control" type="text" id="datos_banco" autocomplete="off" name="datos_banco">
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="vendedor">Divisas: </label>
                                <input class="form-control" value="DOLARES" readonly>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class="col-md-12 label_header" for="cambio">Cambio :</label>
                                <input class="form-control" type="text" name="cambio" id="cambio" value="1.00" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="total_suma_a" id="total_suma_a">
                        <input type="hidden" name="saldoax" id="saldoax">
                        <label class="control-label label_header" for="">DETALLE DE VALORES RECIBIDOS</label>
                    </div>
                    <div class="table-responsive col-md-12 px-1">
                        <table id="example3" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead class='well-dark'>
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
                                @foreach($comprobante_ingreso->pago_ingresos as $value)
                                <tr>
                                    <td> <select class="form-control" style="width: 85%; height: 80%;" class="form-control" disabled>
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

                                        <select class="form-control select2_cuentas" style="width: 85%; height: 80%;" name="banco" id="banco" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach($lista_banco as $vs)

                                            <option {{$value->id_banco == $vs->id ? 'selected' : ''}} value="{{$vs->id}}">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <select class="form-control select2_cuentas" style="width: 85%; height: 80%;" name="banco" id="banco" class="form-control">

                                            <option value="">Seleccione...</option>
                                            @foreach($tipos as $vs)
                                            <option {{$value->id_tipo_tarjeta == $vs->id ? 'selected' : ''}} value="{{$vs->id}}">{{$vs->nombre}}</option>
                                            @endforeach
                                        </select>

                                        @endif
                                    </td>
                                    <td>
                                        <b>@if(!is_null($value->cuenta)) {{$value->cuenta}} @endif</b>
                                    </td>
                                    <td>
                                        <b>@if(!is_null($value->girador)) {{$value->girador}} @endif</b>
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
                    <div class="col-md-12" style="">
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
                                <input class="form-control col-md-3" type="text" name="total_ingresos" id="total_ingresos" class="col-md-12" value="@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->total_ingreso}} @endif" readonly>

                            </div>
                        </div>
                    </div>
                    <label for="detalle_deuda" class="control-label label_header">DETALLE DEL COMPLEMENTO
                        CONTABLE</label>
                    <input type="hidden" name="contador_a" id="contador_a" value="0">
                    <div class="table-responsive col-md-12 px-1">
                        <table id="example2" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr>
                                    <th style="width: 8%; text-align: center;">Código</th>
                                    <th style="width: 10%; text-align: center;">Cuenta</th>
                                    <th style="width: 10%; text-align: center;">Divisa</th>
                                    <th style="width: 10%; text-align: center;">Debe</th>
                                    <th style="width: 6%; text-align: center;">Haber</th>
                                    <th style="width: 6%; text-align: center;">Valor Base</th>

                                </tr>
                            </thead>
                            <tbody id="dt_recibido">
                                @php $cont=0; @endphp
                                @foreach($detalle_ingreso as $values)
                                <tr>
                                    <td>{{$values->codigo}}</td>
                                    <td>{{$values->cuenta}}</td>
                                    <td>DOLARES</td>
                                    <td>0.00</td>
                                    <td>{{$values->haber}}</td>
                                    <td>{{$values->haber}}</td>
                                </tr>

                                @endforeach
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
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: white;" for="nota">Nota: </label>
                            <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5">@if(!is_null($comprobante_ingreso)) {{$comprobante_ingreso->comentarios}} @endif</textarea>
                            <input type="hidden" name="saldo_final" id="saldo_final">
                        </div>
                    </div>

                </div>


                <input type="text" name="contador" id="contador" value="0" class="hidden">
                <input type="hidden" name="total_suma" id="total_suma">

                <div class="col-md-12">
                    &nbsp;
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