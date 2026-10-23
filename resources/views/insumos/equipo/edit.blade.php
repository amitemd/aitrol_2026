@extends('insumos.equipo.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class="box-title">Actualizar Equipos Medicos</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" onclick="goBack()" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('equipo.update', ['id' => $equipo->id]) }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="_method" value="PATCH">
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $equipo->nombre }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
                            <label for="tipo" class="col-md-4 control-label">Tipo</label>
                            <div class="col-md-6">
                                <input id="tipo" type="text" class="form-control" name="tipo" value="{{ $equipo->tipo }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('tipo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('marca') ? ' has-error' : '' }}">
                            <label for="marca" class="col-md-4 control-label">Marca</label>
                            <div class="col-md-6">
                                <input id="marca" type="text" class="form-control" name="marca" value="{{ $equipo->marca }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('marca'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('marca') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('modelo') ? ' has-error' : '' }}">
                            <label for="modelo" class="col-md-4 control-label">Modelo</label>
                            <div class="col-md-6">
                                <input id="modelo" type="text" class="form-control" name="modelo" value="{{ $equipo->modelo }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('modelo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('modelo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('serie') ? ' has-error' : '' }}">
                            <label for="serie" class="col-md-4 control-label">Serie</label>
                            <div class="col-md-6">
                                <input id="serie" type="text" class="form-control" name="serie" value="{{ $equipo->serie }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('serie'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('serie') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('fecha') ? ' has-error' : '' }}">
                            <label for="fecha" class="col-md-4 control-label">Fecha de Ingreso</label>
                            <div class="col-md-6">
                              <div class="input-group date">
                                <div class="input-group-addon">
                                  <i class="fa fa-calendar"></i>
                                </div>
                                <input type="date" name="fecha" class="form-control" id="fecha" value="{{$equipo->fecha_ingreso}}" >
                              </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('fecha_mantenimiento') ? ' has-error' : '' }}">
                            <label for="fecha_mantenimiento" class="col-md-4 control-label">Fecha de Mantenimiento</label>
                            <div class="col-md-6">
                              <div class="input-group date">
                                <div class="input-group-addon">
                                  <i class="fa fa-calendar"></i>
                                </div>
                                <input type="date" name="fecha_mantenimiento" class="form-control" id="fecha_mantenimiento" value="{{$equipo->fecha_mantenimiento}}" >
                              </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('fecha') ? ' has-error' : '' }}">
                            <label for="fecha" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-6">
                              <div class="input-group date">
                                <select class="form-control" required="" name="estado">
                                    <option @if($equipo->estado == 0) selected @endif value="0">Inactivo</option>
                                    <option @if($equipo->estado == 1) selected @endif value="1">Activo</option>
                                </select>
                              </div>
                            </div>
                        </div>
                        <!-- MULTIPLE EMPRESA-->
                        <div class="form-group {{ $errors->has('prestamo') ? ' has-error' : '' }}">
                            <label for="prestamo" class="col-md-4 control-label">En Calidad de Prestamo</label>
                            <div class="col-md-8">
                              <input id="prestamo" name="prestamo" type="checkbox" @if($equipo->prestamo == 1 ) checked @endif value="1" class="flat-blue"  >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>
    $(document).ready(function(){
      $('#consecion').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>

<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>
<script>
    function goBack() {
      window.history.back();
    }
</script>
