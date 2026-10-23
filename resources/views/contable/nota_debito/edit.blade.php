@extends('contable.nota_debito.base')
@section('action-content')

<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Banco</a></li>
            <li class="breadcrumb-item"><a href="../../notadebito">Nota de Debito</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-4">
                <h3 class="box-title">Ver Nota de D&eacute;bito</h3>
            </div>
            <div class="col-md-1 ">
                <a target="_blank" href="{{ route('notadebito.imprimir_nuevo', ['id' => $registro->id]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i>
                    <!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-7 text-right">
                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$registro->id_asiento])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                </a>
                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$registro->id_asiento])}}" target="_blank">
                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                </a>
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body dobra">
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
                        <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-ND" readonly>
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
                        <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha" type="text" class="form-control" name="fecha_asiento" value="@if(!is_null($registro)){{$registro->fecha}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-6 px-1">
                    <div class="col-md-12 px-0">
                        <label for="observacion" class="label_header">Concepto</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="observacion" type="text" class="form-control" name="observacion" value="@if(!is_null($registro)){{$registro->concepto}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-6 px-1">
                    <div class="col-md-12 px-0">
                        <label for="beneficiario" class="label_header">Beneficiario</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="beneficiario" type="text" class="form-control" name="beneficiario" value="@if(!is_null($registro)){{$registro->beneficiario}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-4 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id_banco" class="label_header">Banco</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="banco" type="text" class="form-control" name="banco" value="@if(!is_null($banco)){{$banco->nombre}} #{{$banco->numero_de_cuenta}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id_divisa" class="label_header">Divisa</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="divisa" id="divisa" disabled>
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
                        <input id="cambio" type="number" class="form-control" value="1.00" name="cambio" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="valor" class="label_header">Valor</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="valor" type="text" class="form-control" name="valor" value={{$registro->valor}} readonly autofocus>
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

            </div>

            <div class="col-md-12 table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class='well-dark'>
                            <th width="10%" class="" tabindex="0"># de Cuenta</th>
                            <th width="30%" class="" tabindex="0">Nombre</th>
                            <th width="20%" class="" tabindex="0">Debe</th>
                            <th width="20%" class="" tabindex="0">Haber</th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        @foreach($detalle as $value)
                        <tr class="well" style="position: relative;">
                            <td style="width: 10%;text-align: center;">@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                            <td style="width: 10%; text-align: center;">@if(!is_null($value->cuenta)){{$value->cuenta}}@endif</td>
                            <td class="valor" style="width: 5%; text-align: center;">@if(!is_null($value->debe)){{number_format($value->debe, 2)}}@endif</td>
                            <td style="width: 5%; text-align: center;">@if(!is_null($value->haber)){{number_format($value->haber, 2)}}@endif</td>
                        </tr>
                        @endforeach
                    <tfoot class='well text-center'>
                        <td></td>
                        <td class="text-right">Totales</td>
                        <td>
                            {{$registro->valor}}
                        </td>
                        <td>
                            {{number_format(0, 2)}}
                        </td>
                    </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });
        });
        $(function() {
            $('#fecha').datetimepicker({
                format: 'YYYY/MM/DD',
                defaultDate: '{{date("Y-m-d")}}',
            });
        });




        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }
    </script>

</section>
@endsection