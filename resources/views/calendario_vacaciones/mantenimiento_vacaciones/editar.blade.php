@extends('calendario_vacaciones.mantenimiento_vacaciones.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('nuevos.editar_vacaciones')}} </h3>
            <div class="col-md-12" style="text-align: right">
                <a href="{{ url('calendario/nuevo/index/mostrar_mantenimiento') }}" class="btn btn-danger btn-sm">regresar</a>
            </div>
        </div>
        <div class="box-body">
            <form id="editar_plantilla" method="post" action="{{route('recursos_humanos.Calendario.actualizar')}}">
                {{ csrf_field() }}
                <input type="hidden" name="id" id="id" value="{{$id}}">

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.usuario')}}</label>
                        <input type="text" name="descripcion_tipo" size="20" class="form-control" value="{{$vacacion->nombre_usuario->nombre1}} {{$vacacion->nombre_usuario->apellido1}}" required maxlength="100" disabled>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{ trans('nuevos.empresa') }}</label>

                        <input type="text" name="descripcion_tipo" size="20" class="form-control" value="{{ $vacacion->empresa->nombrecomercial }}" disabled>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="descripcion_contingente" class="col-md-20 control-label">{{ trans('Contingente') }}</label>
                        <select name="contingente" id="contingente" class="form-control " type="text">
                            @foreach ($contingente as $con)
                            <option @if($vacacion->id_contingente == $con->id_user) selected @endif value="{{$con->id_user}}">{{$con->nombres}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="observacion" class="col-md-20 control-label">{{ trans('obsevacion') }}</label>
                        <input type="text" name="observacion" id="observacion" size="20" class="form-control" value="{{$vacacion->observacion}}">
                    </div>
                </div>



                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="anio" class="col-md-20 control-label">Año</label>
                        <input type="text" id="anio" name="anio" size="20" class="form-control" value="{{$vacacion->anio}}">
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{ trans('nuevos.fecha_inicio') }}</label>
                        <input type="date" name="fecha_inicio" size="20" class="form-control" value="{{ $vacacion->inicio }}">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.fecha_fin')}}</label>
                        <input type="date" name="fecha_fin" size="20" class="form-control" value="{{$vacacion->fin}}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('nuevos.actualizar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $("#contingente").select2();
    });
    
    $(document).ready(function() {
        $('form').submit(function(event) {
            var selectedValue = $('#anio').val();
            if (!selectedValue) {
                event.preventDefault();
                alert('Ingrese el año actual, por favor.');

            }
        });
    });
</script>
@endsection