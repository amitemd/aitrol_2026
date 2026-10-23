@extends('contable.divisas.base')
@section('action-content')
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item active">Divisas</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>DIVISAS</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" onclick="location.href='{{route('divisas.crear')}}'" class="btn btn-success btn-gray">
                    <i aria-hidden="true"></i>Agregar Divisa
                </button>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR DIVISAS</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="buscad_tip_ambient" action="{{ route('divisas.buscar') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="buscar_codigo">Código: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="buscar_codigo" name="buscar_codigo" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" autocomplete="off" placeholder="Ingrese codigo..." />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="buscar_tipo_ambiente">Descripci&oacute;n:</label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" value="@if(isset($searchingVals)){{$searchingVals['descripcion']}}@endif" autocomplete="off" placeholder="Ingrese la descripcion..." />
                </div>
                <div class="col-xs-2">
                    <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                </div>
            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO DIVISAS</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-row">
                <div id="contenedor">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr class="well-dark">
                                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Código</th>
                                            <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Descripción</th>
                                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($divisas as $value)
                                        <tr role="row" class="odd" style="background:#F5F5F5">
                                            <td class="sorting_1">{{ $value->id}}</td>
                                            <td>{{$value->descripcion}}</td>
                                            <td>@if (( $value->estado)==1) Activo @elseif(( $value->estado)==0) Inactivo @endif </td>
                                            <td>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a href="{{route('divisas.editar',$value->id)}}" class="btn btn-success btn-gray">
                                                    <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($divisas->currentPage() - 1) * $divisas->perPage())}} / {{count($divisas) + (($divisas->currentPage() - 1) * $divisas->perPage())}} de {{$divisas->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                   {{ $divisas->appends(Request::only(['id','descripcion']))->links() }}
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

<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false
    })
</script>
@endsection