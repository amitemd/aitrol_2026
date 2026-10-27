@extends('rrhh.empleados.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de empleados</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('rrhh.empleados.create')}}"><span class="glyphicon glyphicon-user"></span>  Agregar Empleado</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('rrhh.empleados.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Cédula', 'Apellidos', 'Nombres'], 
          'oldVals' => [$searchingVals['employee_id'] ?? '', $searchingVals['last_names'] ?? '', $searchingVals['first_names'] ?? '' ]])

          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%">Cédula</th>
                <th width="30%">Nombres</th>
                <th width="20%">Teléfonos</th>
                <th width="30%">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($empleados as $value)
                <tr role="row">
                  <td>{{ $value->employeeId }}</td>
                  <td>{{ $value->fullName() }}</td>
                  <td>{{ $value->phones() }}</td>
                  <td>  
                      <div class="form-group col-md-6">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('rrhh.empleados.edit', ['empleado' => $value->employeeId]) }}" class="btn btn-block btn-warning btn-xs">
                        <span class="fa fa-fw fa-pencil"></span> Actualizar
                        </a>  
                      </div>  
                      <div class="form-group col-md-6">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('empleados.documentos', ['id' => $value->employeeId]) }}" class="btn btn-block btn-warning btn-xs"><span class="fa fa-fw fa-paperclip"></span> Documentos</a>
                      </div>
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($empleados)}} de {{$empleados->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $empleados->links()}}
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

<script type="text/javascript">

  $(document).ready(function($){

    $(".breadcrumb").append('<li class="active">Empleados</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  });

</script>  

@endsection
