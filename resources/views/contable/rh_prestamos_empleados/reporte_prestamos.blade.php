@extends('contable.rh_prestamos_empleados.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Reportes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reportes Prestamos</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <!--<h8 class="box-title size_text">Empleados</h8>-->
                <!--<label class="size_text" for="title">EMPLEADOS</label>-->
                <h3 class="box-title">Reportes Prestamos</h3>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR DE PRESTAMOS</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('reportes_prestamos.buscar') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="cedula">Cedula</label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" maxlength="10" type="text" id="cedula" name="cedula" value="@if(isset($searchingVals)){{$searchingVals['id_empl']}}@endif" placeholder="Ingrese cedula..." />

                </div>

                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre_empresa">Empresa</label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <select class="form-control select2" name="nombre_empresa" id="nombre_empresa">
                        <option value="">Seleccione...</option>
                        @foreach($empresa_buscar as $val)
                        <option value="{{$val->id}}">{{$val->nombrecomercial}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="concepto">Concepto: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese Concepto..." />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="tipo">Banco: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <select class="form-control select2" style="width: 100%;" type="text" id="banco" name="banco">
                        <option value="">Seleccione...</option>
                        @foreach($bancos as $val)
                        <option value="{{$val->id}}">{{$val->nombre}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="fechaini">Fecha Inicio: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="fechaini" class="form-control fecha" id="fechaini">
                    </div>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="fecha">Fecha Fin: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="fecha" class="form-control fecha" id="fecha">
                    </div>
                </div>

                <div class="col-md-offset-9 col-xs-2">
                    <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                </div>
            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO DE PRESTAMOS</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="resultados">
                    </div>
                    <div id="contenedor">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well-dark'>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">#</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Rol</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Numero Cheque</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reporte_prestamo as $value)
                                            <tr class="well">
                                                <td>{{$value->id}}</td>
                                                <td>@if(isset($value->nombres)){{$value->nombres}}@endif</td>
                                                <td>@if(isset($value->nombrecomercial)){{$value->nombrecomercial}}@endif</td>
                                                <td>@if(isset($value->tipo_rol)){{$value->tipo_rol}}@endif</td>
                                                <td>@if(isset($value->concepto)){{$value->concepto}}@endif</td>
                                                <td>@if(isset($value->num_cheque)){{$value->num_cheque}} @endif</td>
                                                <td>
                                                    @if($value->estado == 1)
                                                    Activo
                                                    @elseif($value->estado == 0)
                                                    Inactivo
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($reporte_prestamo->currentPage() - 1) * $reporte_prestamo->perPage())}} / {{count($reporte_prestamo) + (($reporte_prestamo->currentPage() - 1) * $reporte_prestamo->perPage())}} de {{$reporte_prestamo->total()}} registros
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{$reporte_prestamo->appends(Request::only(['codigo','reporte_prestamo']))->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $('#fecha').datetimepicker({
        format: 'YYYY-MM-DD',
    });
    $('#fechaini').datetimepicker({
        format: 'YYYY-MM-DD',
    });
    window.addEventListener("load", function() {
        reporte_master.cedula.addEventListener("keypress", soloNumeros, false);
    });

    //Solo permite introducir numeros.
    function soloNumeros(e) {
        var key = window.event ? e.which : e.keyCode;
        if (key < 48 || key > 57) {
            e.preventDefault();
        }
    }
</script>
@endsection