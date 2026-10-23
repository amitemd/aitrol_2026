

@extends('bo.base_agenda')
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
  <div class="box box-primary">
    <div class="box-header">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-6" style="padding: 5px;">
            <h3 class="box-title">Doctores</h3>
          </div>
          <div class="col-md-3" style="padding: 5px;">
            <a class="btn btn-block btn-success" href="{{ route('solicitud.consulta') }}" > <i class="glyphicon glyphicon-th-list" >  Consulta/Procedimiento  </i></a>

          </div>
        </div>

      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      @foreach ($users as $user)
      @php $espe = Sis_medico\User_espe::where('usuid',$user->id)->join('especialidad as e','e.id','user_espe.espid')->select('e.*')->get(); @endphp
      <div class="col-md-6" style="padding: 5px;">
        <div class="box box-success">
          <div class="col-md-4">
            <a href="{{ route('solicitud.calendario', ['id' => $user->id, 'fecha' => 0]) }}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
            <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:100%;height:160px;" id="fotografia_usuario" ></a>
          </div>
          <div class="col-md-8">
            <h4>Dr(a). {{$user->apellido1}} {{ $user->apellido2 }}</h4>
            <h4>{{$user->nombre1}} {{ $user->nombre2 }}</h4>
            @foreach($espe as $e)
              <li>{{$e->nombre}}</li>
            @endforeach
            <a href="{{ route('solicitud.calendario', ['id' => $user->id, 'fecha' => 0]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                          Agendar
                          </a>
          </div>


        </div>
      </div>
      @endforeach

    </div>
    <!-- /.box-body -->
  </div>
</section>






<script type="text/javascript">


  $(document).ready(function()
    {

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
