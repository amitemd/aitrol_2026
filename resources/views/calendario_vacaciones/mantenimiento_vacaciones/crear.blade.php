@extends('calendario_vacaciones.mantenimiento_vacaciones.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('nuevos.crear')}}</h3>
            <div class="col-md-12" style="text-align: right">
                <a href="{{ url('calendario/nuevo/index/mostrar_mantenimiento') }}" class="btn btn-danger btn-sm">regresar</a>
            </div>
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('recursos_humanos.Calendario.guardar')}}">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.empresa')}}</label>
                        <input type="text" name="descripcion_tipo" size="20" class="form-control" value="{{$empresa->nombrecomercial}}" disabled>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.usuario')}} </label>
                        <select class="form-control" name="id_empleado" id="id_empleado">
                            <option value="">{{trans('nuevos.seleccione')}}</option>
                            @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id_user}}">{{$usuario->nombres}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>




                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="descripcion_contingente" class="col-md-20 control-label">{{ trans('Contingente')}}</label>
                        <select name="id_usuario" id="id_usuario" class="form-control " type="text">
                            <option value="">{{trans('nuevos.seleccione')}}</option>
                            @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id_user}}">{{$usuario->nombres}} </option>
                            @endforeach

                        </select>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="observacion" class="col-md-20 control-label">{{ trans('obsevacion') }}</label>
                        <input type="text" id="observacion" name="observacion" size="20" class="form-control" value="">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="anio" class="col-md-20 control-label">Año</label>
                        <input type="text" id="anio" name="anio" size="20" class="form-control" value="">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.fecha_inicio')}} </label>
                        <input type="date" name="fecha_inicio" size="20" class="form-control" value="">
                    </div>

                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">{{trans('nuevos.fecha_fin')}}</label>
                        <input type="date" name="fecha_fin" size="20" class="form-control" value="">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('nuevos.agregar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $("#id_usuario").select2();
        $("#id_empleado").select2();
    });

    $(document).ready(function() {

        $('form').submit(function(event) {


            var selectedValue = $('#id_empleado').val();
            var selectedValue2 = $('#id_usuario').val();
            var selectedValue3 = $('#anio').val();

            if (!selectedValue) {
                event.preventDefault();
                alert('Ingrese un usuario, por favor.');

            } else if (!selectedValue2) {
                event.preventDefault();
                alert('Ingrese un contingente, por favor.');
            } else if (!selectedValue3) {
                event.preventDefault();
                alert('Ingrese el año actual, por favor.');
            }
        });
    });
</script>

@endsection