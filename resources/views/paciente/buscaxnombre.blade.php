@extends('agenda.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('pacientes.buscarpornombre')}}</h3>
        </div>
        <div class="col-sm-4">
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('paciente.search2')}}">
         {{ csrf_field() }}
         <input type="hidden" value="{{$id_doc}}" name="id_doc" id="id_doc">
         <input type="hidden" value="{{$fecha}}" name="fecha" id="fecha">
         <input type="hidden" value="{{$sala}}" name="sala" id="sala">

         @component('paciente.buscar', ['title' => 'Buscar'])
          @component('paciente.nombre', ['items' => ['Nombres'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre1'] : '' ]])

          @endcomponent
        @endcomponent
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">

                  <th>{{trans('pacientes.nombres')}}</th>
                  <th>{{trans('pacientes.cedula')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($paciente as $value)
                <tr>
                   
                  <td><a  href="@if($id_doc=='0'){{ route('preagenda.nuevo')}}/{{$fecha}}/{{$value->id}}/{{$sala}}@else{{ route('agenda.nuevo', ['id' => $id_doc])}}/{{$fecha}}/{{$value->id}}@endif">{{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif {{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif</a></td>
                  <td><a href="@if($id_doc=='0'){{ route('preagenda.nuevo')}}/{{$fecha}}/{{$value->id}}/{{$sala}}@else{{ route('agenda.nuevo', ['id' => $id_doc])}}/{{$fecha}}/{{$value->id}}@endif">{{ $value->id}}</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('pacientes.mostrando')}} 1 al {{count($paciente)}} de {{$paciente->total()}} {{trans('pacientes.registros')}}</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $paciente->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
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

  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  
  



 </script> 

@endsection