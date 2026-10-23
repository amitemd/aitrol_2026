@extends('archivo_plano.medicinapublica.base')
@section('action-content')
<!-- Main content -->
<section class="content">
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-sm-10">
                <h3 class="box-title">{{trans('contableM.listado_medicina')}}</h3>
            </div>
            <div class="col-sm-2">
                <a class="btn btn-primary" href="{{ route('procedimientos.crear') }}">{{trans('contableM.agregar_medicina')}}</a>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6"></div>
            <div class="col-sm-6"></div>
        </div>
        <!--AQUI VA EL BUSCADOR-->
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <form method="POST" id="reporte_master" action="{{ route('procedimientos.buscar') }}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-1 col-xs-2">
                            <label class="texto" for="descripcion">{{trans('contableM.nombre_medicina')}}</label>
                        </div>
                        <div class="form-group col-md-3 col-xs-10 container-4">
                            <input class="form-control" type="text" id="descripcion" name="descripcion" value="@if (isset($searchingVals)) {{ $searchingVals['nro_comprobante'] }} @endif" placeholder="{{trans('contableM.ingrese_nombre')}}" />
                        </div>
                        <div class="form-group col-md-1 col-xs-2">
                            <label class="texto" for="codigo">{{trans('contableM.codigo_medicina')}}</label>
                        </div>
                        <div class="form-group col-md-3 col-xs-10 container-4">
                            <input class="form-control" type="text" id="codigo" name="codigo" value="@if (isset($searchingVals)) {{ $searchingVals['id_asiento'] }} @endif" placeholder="{{trans('contableM.ingrese_codigo')}}" />
                        </div>
                        <div class="col-md-2 col-xs-2 col-xs-10 container-4">
                            <button type="submit" id="" class="btn btn-primary ">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                {{ trans('contableM.buscar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-sm-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                        aria-describedby="example2_info">
                        <thead>
                            <tr>


                                <th width=60% tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                    aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('contableM.nombre_medicina')}}
                                </th>
                                <th width=10% tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                    aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('contableM.codigo_medicina')}}
                                </th>
                                <th width=10% tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                    aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('contableM.codigo_medicina_tipo')}}
                                </th>    
                                <th width=10% tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                    aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('contableM.valor_medicina')}}
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicinas as $medicina)
                                <tr>

                                    <td>{{ $medicina->descripcion }}</td>
                                    <td>{{ $medicina->codigo }}</td>
                                    <td>{{ $medicina->codigo_medicina }}</td>
                                    <td>{{ $medicina->valor }}</td>
                                    <td>
                                        <a href="{{ route('procedimientos.editar', ['id' => $medicina->id]) }}"
                                            class="btn btn-warning col-sm-7 col-xs-8 btn-margin">{{trans('contableM.valor')}}</a>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-warning col-sm-8 col-xs-8 btn-margin"
                                            onclick="eliminarMedicamento('{{ $medicina->id }}')">{{trans('contableM.eliminar_medicina')}}</a>
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando_registro')}}
                        {{ count($medicinas) }} {{trans('contableM.registros_valores')}}</div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $medicinas->appends(Request::only(['id', 'descripcion', 'valor']))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.box-body -->
</div>
<div class="modal fade" id="mensaje_eliminacion" data-keyboard="false" tabindex=null role="dialog"
    aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>
</section>
<!-- /.content -->
</div>
@endsection
<script>
function eliminarMedicamento(id) {
Swal.fire({
    title: 'Desea eliminar este medicamento?',
    //icon:'question',
    showDenyButton: false,
    showCancelButton: true,
    confirmButtonText: 'Si',
    cancelButtonText: 'No',
}).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        $.ajax({
            url: "{{ asset('medicina_publica/delete/') }}" + '/' + id,
            type: 'get',
            dataType: 'json',
            success: function(json) {
                if (json.respuesta == 'ok')
                    Swal.fire('Registro eliminado correctamente').then((result) => {
                        window.open('index', '_self');
                    });

                else
                    Swal.fire('Error al actualizar el registro', 'error');
            }
        });
    }
});
}
</script>
