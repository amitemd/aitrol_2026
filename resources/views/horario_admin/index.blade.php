

@extends('horario_admin.base')
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
    </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
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
                <th>Acción</th>                    
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                  <td width="10%"><a href="{{ route('agenda.agenda', ['id' => $user->id])}}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                  <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:80px;height:80px;" id="fotografia_usuario" ></a></td>  
                  <td width="15%">{{$user->id}}</td>
                  <td width="20%">{{$user->apellido1}} {{ $user->apellido2 }}</td>
                  <td width="20%">{{$user->nombre1}} {{ $user->nombre2 }}</td>
                  <td width="15%"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('horario.doctor', ['id' => $user->id]) }}" class="btn btn-success col-md-9 col-sm-9 col-xs-9 btn-margin">
                      Horario
                      </a> 
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


  $(document).ready(function() 
    {
        $(".breadcrumb").append('<li class="active">Agenda</li>');
    });

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