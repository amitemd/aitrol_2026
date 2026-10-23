@extends('rrhh.pregunta.base')

@section('action-content')
<div class="container">
    <div class="row">
        <!--left-->
            <div class="box box-primary col-xs-24"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Pregunta</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('preguntas.update', ['id' => $pregunta->id]) }}">
                    <div class="box-body col-xs-24">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

     
                        <!--RUC-->
                        <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-2 control-label">Escriba la Pregunta</label>
                            <div class="col-md-9">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $pregunta->nombre }}" required autofocus>
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                                   

                        <div class="form-group col-xs-12{{ $errors->has('id_grupopregunta') ? ' has-error' : '' }}">
                            <label for="id_grupopregunta" class="col-md-2 control-label">Grupo de Pregunta</label>
                            <div class="col-md-9">
                            <select id="id_grupopregunta" name="id_grupopregunta" class="form-control">
                                @foreach($grupopregunta as $value)

                                                <option {{ $pregunta->id_grupopregunta == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option> 
                                    @endforeach
                                        
                                </select>  
                                @if ($errors->has('id_grupopregunta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_grupopregunta') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-9">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$pregunta->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$pregunta->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                            </select>  
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>  
    </div>
</div>    

@endsection
