@extends('sensor.sensor')
@section('action-content')


<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">EDITAR</h3>
        </div>
        <div class="box-body">
            <form id="editar_sensor" method="post" action="{{route('sensor.dispositivo_update')}}">
                {{ csrf_field() }}
                 <input type="hidden" name="id" id="id" value="{{$id}}">
                 <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('serie') ? ' has-error' : '' }}">
                        <label for="serie" class="col-md-20 control-label">Serie</label>
                        <input type="text" name="serie" id="serie" size="20" class="form-control" value="{{$sensor->serie}}">
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="estado" class="col-md-20 control-label">Estado</label>
                        <select name="estado" id="estado" class="form-control " type="text">
                           
                            <option {{$sensor->estado == '1'  ? 'selected' : ''}} value="1">AUTORIZADA</option>
                            <option {{$sensor->estado == '0'  ? 'selected' : ''}} value="0">NO AUTORIZADA</option>
                           
                        </select>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="dispositivo" class="col-md-20 control-label">Establecimiento</label>
                        <select name="dispositivo" id="dispositivo" class="form-control " type="text">
                            @foreach ($dispositivo as $es)
                            <option {{$sensor->id_establecimiento == $es->id  ? 'selected' : ''}}   value="{{$es->id}}">{{$es->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('Actualizar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection