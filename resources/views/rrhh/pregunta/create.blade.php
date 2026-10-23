@extends('rrhh.pregunta.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Pregunta</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('preguntas.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    

                    <!--Nombre-->
                        <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-2 control-label">Escriba la pregunta </label>
                            <div class="col-md-9">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}"  required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div> 
               
                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-12{{ $errors->has('id_grupopregunta') ? ' has-error' : '' }}">
                            <label for="id_grupopregunta" class="col-md-2 control-label">Grupo de Pregunta</label>

                            <div class="col-md-9">
                                <select id="id_grupopregunta" name="id_grupopregunta" class="form-control" required="required">
                                    <option value="">Seleccione..</option>
                                    @foreach($grupopregunta as $value)
                                                <option value="{{$value->id}}"> {{$value->nombre}}</option>
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_grupopregunta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_grupopregunta') }}</strong>
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
