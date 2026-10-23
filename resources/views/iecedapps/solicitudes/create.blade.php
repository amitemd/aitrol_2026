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
            Charlas Crear
        </div>
        <div class="card-body">

            <form method="POST" action="{{route('charlasapps.store')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion">
                </div>
                <div class="form-group">
                    <label for="descripcion">Dr(a)</label>
                    <select name="user" id="user" class="form-control">
                        @foreach($list as $list)
                         <option value="{{$list->id}}"> {{$list->apellido2}} {{$list->nombre1}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="url">Fecha y Hora</label>
                    <input class="form-control" type="datetime-local" id="meeting-time" name="fecha" >
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" name="url" id="url">
                </div>
                <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
