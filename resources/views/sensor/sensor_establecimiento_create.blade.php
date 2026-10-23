@extends('sensor.encabezado')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('Crear')}}</h3>
        </div>
        <div class="box-body">
            <form id="sensor_establecimiento" method="post" action="{{route('sensor.establecimiento_store')}}">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" size="20" class="form-control">
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