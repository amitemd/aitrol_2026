

@extends('rrhh.area.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista Areas para encuestas y sugerencias</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('area.create') }}">Agregar Nueva Area</a>
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
            
                <th width="40%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripción</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Estado </th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Accion</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($area as $value)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $value->nombre }}</td>
                  <td> {{ $value->descripcion }}</td>
                  <td> @if($value->estado == 1 )<div class="bg-green " style="text-align: center;"><span>Activo</span></div>@else<div class="bg-gray" style="text-align: center;"><span>Inactivo</span></div>@endif</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('area.edit', ['id' => $value->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($area)}} de {{count($area)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $area->links() }}
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