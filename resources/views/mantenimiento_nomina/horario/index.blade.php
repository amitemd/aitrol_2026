@extends('mantenimiento_nomina.horario.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> Mantenimiento Horarios Laboral</h3>
        </div>
        <div class="col-sm-4">
           <a  href="{{route('mantenimiento.horario.create')}}"  class="btn btn-primary btn-gray"><i aria-hidden="true"></i> Agregar Nuevos Horarios</a
        </div>
    </div>
  </div>
  
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <form method="POST" action="">
         {{ csrf_field() }}

      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
            <tr class="well-dark" >
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Horario Laboral</th>
                <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Estado</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>
                
              </tr>
            </thead>
            <tbody>
                @foreach ($mantenimientos_horarios as $mh)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $mh->horario }}</td>
                  <td class="sorting_2">
                  @if($mh->estado == 1)
                                  Activo
                                @elseif($mh->estado == 0)
                                  Inactivo
                                @endif
                  <td align="center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('mantenimiento.horario.edit', ['id' => $mh->id])}}" class="btn btn-success btn-gray">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
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
    <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($mantenimientos_horarios->currentPage() - 1) * $mantenimientos_horarios->perPage())}} / {{count($mantenimientos_horarios) + (($mantenimientos_horarios->currentPage() - 1) * $mantenimientos_horarios->perPage())}} de {{$mantenimientos_horarios->total()}} registros
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{$mantenimientos_horarios->appends(Request::only(['descripcion','estado']))->links() }}
                    </div>
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