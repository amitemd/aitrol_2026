

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
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Doctores</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('agenda.agenda4') }}">Agenda completa de doctores</a>
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
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Imágen</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Cédula</th>
                <th width="18%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Apellidos</th>
                <th width="18%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Nombres</th>
                <th width="17%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Máximo Consultas</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Máximo Procedimientos</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>                    
              </tr>
            </thead>
            <tbody>

            

            @foreach ($users as $user)
                <tr role="row" class="odd">
                  <td><a href="{{ route('agenda.agenda', ['id' => $user->id, 'i' => 0]) }}"> <input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                  <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:80px;height:80px;" id="fotografia_usuario" ></a>  
                  </td>
                  <td class="sorting_1" >{{ $user->id }}</td>
                  <td> {{ $user->apellido1 }} {{ $user->apellido2 }}</td>
                  <td> {{ $user->nombre1 }} {{ $user->nombre2 }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{$user->max_consulta}}</td>
                  <td>{{$user->max_procedimiento}}</td>
                  <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('agenda.agenda', ['id' => $user->id, 'i' => 0]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Agendar
                                                              
                        
                      <!-- Botón para abrir la ventana modal de editar -->
                      <a href="{{ route('doctor.max', ['id' => $user->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Máximos
                        </a>
  
                  
                  </td>
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
  

 </script> 




@endsection