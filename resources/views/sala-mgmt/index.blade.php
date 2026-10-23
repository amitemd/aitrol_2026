

@extends('tipo_usuario-mgmt.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista Tipo de Usuarios</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('tipo_usuario-management.create') }}">Agregar Nuevo Tipo de Usuario</a>
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
            
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="40%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripción</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($tipo_usuarios as $tipo_usuario)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $tipo_usuario->nombre }}</td>
                  <td> {{ $tipo_usuario->descripcion }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('tipo_usuario-management.edit', ['id' => $tipo_usuario->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($tipo_usuarios)}} de {{count($tipo_usuarios)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $tipo_usuarios->links() }}
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