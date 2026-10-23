@extends('sensor.encabezado')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('Crear')}}</h3>
        </div>
        <div class="box-body">
            <form id="sensor_establecimiento" method="post" action="{{route('sensor.dispositivo_store')}}">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('serie') ? ' has-error' : '' }}">
                        <label for="serie" class="col-md-20 control-label">Serie</label>
                        <input type="text" name="serie" id="serie" size="20" class="form-control">
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="estado" class="col-md-20 control-label">Estado</label>
                        <select name="estado" id="estado" class="form-control " type="text">
                           
                            <option  value="1">AUTORIZADO</option>
                            <option  value="0">NO AUTORIZADO</option>
                           
                        </select>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="establecimiento" class="col-md-20 control-label">Establecimiento</label>
                        <select name="establecimiento" id="establecimiento" class="form-control " type="text">
                            @foreach ($establecimiento as $es)
                            <option  value="{{$es->id}}">{{$es->nombre}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
        
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('Agregar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection