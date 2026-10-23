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
            Charlas Editar
        </div>
        <div class="card-body">
            <form method="POST" action="{{route('charlasapps.update')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="hidden" name="id" value="{{$charlas->id}}">
                    <input type="text" class="form-control" name="descripcion" value="{{$charlas->descripcion}}" id="descripcion">
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" name="url" value="{{$charlas->url}}" id="url">
                </div>
                <div class="form-group">
                    <label for="descripcion">Dr(a)</label>
                    <select name="user" id="user" class="form-control">
                        @foreach($list as $list)
                         <option @if($charlas->id_doctor==$list->id) selected="selected" @endif value="{{$list->id}}"> {{$list->apellido2}} {{$list->nombre1}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="url">Fecha y Hora</label>
                    <input class="form-control" type="datetime-local" value="{{$charlas->fecha}}" id="meeting-time" name="fecha" >
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="">Seleccione... </option>
                        <option value="1" @if($charlas->estado==1) selected="selected" @endif>Activo</option>
                        <option value="0" @if($charlas->estado==0) selected="selected" @endif>Inactivo</option>
                    </select>
                </div>
                <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
