@extends('rrhh.grupopregunta.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nuevo Grupo de Preguntas</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('grupopreguntas.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    
                    <!--Nombre-->
                        <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-2 control-label">Pregunta Central </label>
                            <div class="col-md-9">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}"  required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
               
                    <!--Descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Descripcion</label>
                        <div class="col-md-9">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}"  required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                        </div>  
                    </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-12{{ $errors->has('crespuesta') ? ' has-error' : '' }}">
                            <label for="crespuesta" class="col-md-2 control-label">Tipo de Calificacion</label>
                            <div class="col-md-9">
                                <select id="crespuesta" name="crespuesta" class="form-control" required="required">
                                    <option value="">Seleccione..</option>
                                    <option value="1">Texto</option>
                                    <option value="2">Tiempo</option>
                                    <option value="3">Reaccion</option>
                                </select> 
                                @if ($errors->has('crespuesta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('crespuesta') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            
        </div>
    </div>
</div>
@endsection
