

@extends('agenda.base')
@section('action-content')

<!-- Ventana modal editar -->
                      <div class="modal fade" id="editMaxPacientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">

                          </div>
                        </div>
                      </div>







    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title">Lista de Doctores</h3>
        </div>
        <div class="col-md-4">
          <a class="btn btn-primary col-md-6" href="{{ route('agenda.agenda4') }}">Agenda completa de doctores</a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('preagenda.preagenda') }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Procedimientos
                        </a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('agenda.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Cédula', 'Apellido'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['apellido1'] : '']])
          @endcomponent
          </br>
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Imágen</th>
                <th>Cédula</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Email</th>
                <th>Máximo Consultas</th>
                <th>Máximo Procedimientos</th>
                <th>Acción</th>                    
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                  <td><a href="{{ route('agenda.agenda', ['id' => $user->id])}}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                  <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:80px;height:80px;" id="fotografia_usuario" ></a></td>  
                  <td>{{$user->id}}</td>
                  <td>{{$user->apellido1}} {{ $user->apellido2 }}</td>
                  <td>{{$user->nombre1}} {{ $user->nombre2 }}</td>
                  <td>{{$user->email}}</td>
                  <td>{{$user->max_consulta}}</td>
                  <td>{{$user->max_procedimiento}}</td>
                  <td><input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('agenda.agenda', ['id' => $user->id]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Agendar
                        </a>                                        
                        
                       <!--Botón para abrir la ventana modal de editar -->
                      <a href="{{ route('doctor.max', ['id' => $user->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Máximos
                        </a>

                        <!--Botón para abrir la ventana modal de editar 
                      <a href="{{ route('doctor.max', ['id' => $user->id]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Máximos/Horario
                        </a>-->

                  </td>
                </tr>
            @endforeach
            </tbody>
          </table> 

        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($users)}} de {{$users->total()}}registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $users->links() }}
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