@extends('contable.importaciones.mantenimiento_gastos.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.importaciones')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.mangastos')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('contableM.mangastos')}}</h3>
            </div>

            <div class="col-md-1 text-right">
                <a href="{{route('gastosimportacion.create')}}" class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('laboratorio.crear')}}
                </a>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">{{trans('contableM.listgastos-')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="contenedor">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well-dark'>
                                                <th>Id</th>
                                                <th>{{trans('contableM.codigo')}}</th>
	                                        	<th>{{trans('contableM.nombre')}}</th>
	                                        	<th>{{trans('contableM.accion')}}</th>
                                        	</tr>
                                        </thead>
                                        <tbody>
                                            @foreach($gastos as $gs)
                                            <tr class="well">
                                                <td>{{$gs->id}}</td>
                                                <td>{{$gs->codigo}}</td>
                                                <td>{{$gs->nombre}}</td>
                                                <td>
                                                    <a href="{{route('gastosimportacion.edit',['id' => $gs->id])}}" class="btn btn-warning"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                                                    <a href="{{route('gastosimportacion.eliminar',['id' => $gs->id])}}" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($gastos->currentPage() - 1) * $gastos->perPage())}} / {{count($gastos) + (($gastos->currentPage() - 1) * $gastos->perPage())}} {{trans('laboratorio.de')}} {{$gastos->total()}} {{trans('contableM.registros')}}</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $gastos->appends(request()->query())->links()  }}
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
    $(document).ready(function() {
        $('#tabla').DataTable({
            'paging': false,
            'lengthChange': true,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
</script>

@endsection