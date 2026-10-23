@extends('rrhh.grupopregunta.base')

@section('action-content')
<div class="container">
    <div class="row">
        <!--left-->
            <div class="box box-primary col-xs-24"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Grupo de Preguntas</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('grupopreguntas.update', ['id' => $grupopregunta->id]) }}">
                    <div class="box-body col-xs-24">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $grupopregunta->id }}"> 
                        <!--RUC-->
                        <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-2 control-label">Pregunta Central</label>
                            <div class="col-md-9">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $grupopregunta->nombre }}" required autofocus>
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                                            
                        
                        
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-2 control-label">Descripcion</label>
                            <div class="col-md-9">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $grupopregunta->descripcion }}" required autofocus>
                                @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Ciudad-->
                        <div class="form-group col-xs-12{{ $errors->has('tipo_calificacion') ? ' has-error' : '' }}">
                            <label for="tipo_calificacion" class="col-md-2 control-label">Tipo Calificacion:</label>
                            <div class="col-md-9">
                                <select id="tipo_calificacion" name="tipo_calificacion" class="form-control" required="required">
                                    <option {{ $grupopregunta->tipo_calificacion == '1' ? 'selected' : ''}} value="1">Texto</option>
                                    <option {{ $grupopregunta->tipo_calificacion == '2' ? 'selected' : ''}} value="2">Tiempo</option>
                                    <option {{ $grupopregunta->tipo_calificacion == '3' ? 'selected' : ''}} value="3">Reaccion</option>
                                </select> 
                                @if ($errors->has('tipo_calificacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo_calificacion') }}</strong>
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
