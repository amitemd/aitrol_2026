@extends('procedimiento.base')
@section('action-content')
<!-- Ventana modal editar -->
                      <div class="modal fade" id="seguimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">

                          </div>
                        </div>  
                      </div>

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista Tipo de Procedimientos</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('procedimiento.create') }}">Agregar Nuevo Procedimiento</a>
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
    <form method="POST" action="{{ route('procedimiento.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Nombre'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
          @endcomponent
          </br>
        @endcomponent 
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="60%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Observación</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Acción</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($procedimientos as $procedimiento)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $procedimiento->nombre }}</td>
                  <td> {{ $procedimiento->observacion }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('procedimiento.edit', ['id' => $procedimiento->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                        </a>
                        <a data-toggle="modal" data-target="#seguimiento" href="{{ route('procedimiento.sugerido', ['id' => $procedimiento->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Procedimientos Sugeridos
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($procedimientos)}} de {{count($procedimientos)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $procedimientos->appends(Request::only(['nombre']))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection