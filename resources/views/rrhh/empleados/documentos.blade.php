@extends('rrhh.empleados.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Documentos de: {{$empleado->nombre1}} {{$empleado->apellido1}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('empleados.create')}}"><span class="glyphicon glyphicon-user"></span>  Agregar Documento</a>
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
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Cédula', 'Apellidos', 'Nombres'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['apellido1'] : '', isset($searchingVals) ? $searchingVals['nombre1'] : '' ]])

          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="40%">Documento</th>
                <th width="30%">Archivo</th>
                <th width="30%">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($documentos as $value)
                <tr role="row">
                  <td >{{ $value->descripcion}}</td>
                  <td></td>
                  <td></td>
                  <td>  
                      <div class="form-group col-md-6">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('empleados.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs">
                        <span class="fa fa-fw fa-pencil"></span> Actualizar
                        </a>  
                      </div>  
                      <div class="form-group col-md-6">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('empleados.documentos', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs"><span class="fa fa-fw fa-paperclip"></span> Documentos</a>  
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al  Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            
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

    $(".breadcrumb").append('<li><a href="{{asset('/empleados')}}"></i> Empleados</a></li>');
    $(".breadcrumb").append('<li class="active">Documentos</li>');

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