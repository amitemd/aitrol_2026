

@extends('paciente_biopsia.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Biopsias sin Asignar</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('biopsias_paciente.create') }}">Agregar Biopsias</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('user-management.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Nombre'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '', ]])
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
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Paciente</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Fecha de Subida</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($paciente_biopsia as $value)
              <tr role="row" class="odd">
                <td>{{ $value->paciente->apellido1}} @if($value->paciente->apellido2 != "(N/A)"){{ $value->paciente->apellido2}}@endif {{ $value->paciente->nombre1}} @if($value->paciente->nombre2 != "(N/A)"){{ $value->paciente->nombre2}}@endif</td>
                
                <td>{{$value->created_at}}</td>     
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <!--<tr>
                <th width="10%" rowspan="1" colspan="1">Nombre de Usuario</th>
                <th width="20%" rowspan="1" colspan="1">Email</th>
                <th class="hidden-xs" width="20%" rowspan="1" colspan="1">Nombres</th>
                <th class="hidden-xs" width="20%" rowspan="1" colspan="1">Apellidos</th>
                <th rowspan="1" colspan="2">Acción</th>
              </tr>-->
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection