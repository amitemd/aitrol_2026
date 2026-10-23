

@extends('sala-mgmt.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Salas de Atención de la Unidad: {{$hospital->nombre_hospital}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('sala-management.crear',['id' => $hospital->id]) }}">Agregar Nueva Sala</a>
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


    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Sala</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($salas as $sala)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $sala->nombre_sala }}</td>
                  
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('sala-management.editar', ['id_hospital' => $hospital->id, 'id_sala' => $sala->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Actualizar
                        </a>
                         <a href="{{route('horario.sala',['id' =>$sala->id])}}" class="btn btn-warning col-md-3 col-sm-3 col-xs-3 btn-margin"> Horario</a>

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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($salas)}} de {{count($salas)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $salas->links() }}
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