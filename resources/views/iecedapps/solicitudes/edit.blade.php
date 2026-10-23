@extends('layouts.app-template-apps')
@section('content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            Solicitudes Visualizador
        </div>
        <div class="card-body">
            <form method="POST" action="{{route('solicitudes_apps.update')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Observacion</label>
                    <input type="hidden" name="id" value="{{$solicitudes->id}}">
                    <input type="text" class="form-control" name="descripcion" value="{{$solicitudes->observaciones}}" readonly id="descripcion">
                </div>
                <div class="form-group">
                    <label for="url">Telefono 1</label>
                    <input type="text" class="form-control" readonly name="url" value="{{$solicitudes->telefono1}}" id="url">
                </div>
                <div class="form-group">
                    <label for="url">Telefono 2</label>
                    <input type="text" class="form-control" readonly name="url" value="{{$solicitudes->telefono2}}" id="url">
                </div>
                <div class="form-group">
                    <label for="descripcion">Usuario</label>
                    @php 
                     $user= Sis_medico\User::find($solicitudes->id_usuariocrea);
                    @endphp
                    <input class="form-control" readonly type="text" name="usuario" value="{{$user->nombre1}} {{$user->apellido1}}">
                </div>
                <div class="form-group" style="text-align: center;">
                <label for="descripcion">Archivo Adjunto</label> 
                </div>
                <div class="form-group" style="text-align: center;">
               
                <img src="{{asset('/avatars').'/'.$solicitudes->url}}" style="width: 300px; height: 400px;
                " class="img-circle" alt="User Image">
                </div>
              <!--   <div class="form-group">
                    <label for="url">Fecha y Hora</label>
                    <input class="form-control" type="datetime-local" value="{{$solicitudes->fecha}}" id="meeting-time" name="fecha" >
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="">Seleccione... </option>
                        <option value="1" @if($solicitudes->estado==1) selected="selected" @endif>Activo</option>
                        <option value="0" @if($solicitudes->estado==0) selected="selected" @endif>Inactivo</option>
                    </select>
                </div> -->
               <!--  <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">GESTIONAR</button>
                </div> -->
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
