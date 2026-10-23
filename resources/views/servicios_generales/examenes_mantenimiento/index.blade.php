@extends('servicios_generales.examenes_mantenimiento.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="box-title"> Examenes</h3>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form method="POST" action="{{ route('mantenimientoexcel.buscador') }}">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-2">
                            <label for="nombre" class="col-md-4 texto">Nombre</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="nombre" id="nombre" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr role="row">

                                        <th tabindex="0" aria-controls="example2">Nombre</th>
                                        <th tabindex="0" aria-controls="example2">Nombre Tubo</th>
                                        <th tabindex="0" aria-controls="example2">Cantidad de Tubos</th>
                                        <th tabindex="0" aria-controls="example2">Indice de Tubos</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($examenes as $val)
                                    @php
                                    $nombreTubo = Sis_medico\Labs_Tipo_Tubo::where('id',$val->id_labs_tubo)->first();
                                    @endphp
                                    <tr role="row" class="odd">

                                        <td>{{$val->nombre}}</td>
                                        <td>@if($nombreTubo != null) {{$nombreTubo->nombre}} @else @endif</td>
                                        <td>{{$val->cantidad_tubos}}</td>
                                        <td>{{$val->indice_tubos}}</td>
                                        <td>
                                            <a href="{{ route('mantenimientoexcel.actualizar', ['id' => $val->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                                                Actualizar
                                            </a>
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
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($examenes->currentPage() - 1) * $examenes->perPage())}} / {{count($examenes) + (($examenes->currentPage() - 1) * $examenes->perPage())}} de {{$examenes->total()}} registros
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                {{$examenes->appends(Request::only(['id','nombre']))->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
