@extends('tecnicas.base')

@section('action-content') 
<section class="content">
    <div class="box box-primary"> 
        <div class="box-header with-border"><h3 class="box-title">Editar Procedimiento</h3>
    </div>
    <div class="box-body">
        <form class="form-vertical" role="form" method="POST" action="{{ route('tecnicas.update', ['id' => $procedimiento_completo->id]) }}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{ $procedimiento_completo->id }}">
            <input type="hidden" name="agenda" value="{{$agenda}}">

            <div class="form-group col-xs-12{{ $errors->has('nombre_general') ? ' has-error' : '' }}">
                <label for="nombre_general" class="col-md-4 control-label">Nombre a Mostrar</label>
                <div class="col-md-7">
                    <input id="nombre_general" type="text" class="form-control" name="nombre_general" value="{{ $procedimiento_completo->nombre_general }}" required autofocus>
                    @if ($errors->has('nombre_general'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_general') }}</strong>
                        </span>
                    @endif 
                </div>
            </div>

            <div class="form-group col-xs-12{{ $errors->has('nombre_completo') ? ' has-error' : '' }}">
                <label for="nombre_completo" class="col-md-4 control-label">Nombre Completo</label>
                <div class="col-md-7">
                    <input id="nombre_completo" type="text" class="form-control" name="nombre_completo" value="{{ $procedimiento_completo->nombre_completo }}" required autofocus>
                    @if ($errors->has('nombre_completo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_completo') }}</strong>
                        </span>
                    @endif 
                </div>
            </div>

            <div class="form-group col-xs-12{{ $errors->has('estado_anestesia') ? ' has-error' : '' }}">
                <label for="estado_anestesia" class="col-xs-4 control-label">Posee Record Anestesico</label>
                <div class="col-md-7">
                    <select id="estado_anestesia" name="estado_anestesia" class="form-control">
                        <option {{$procedimiento_completo->estado_anestesia == 0 ? 'selected' : ''}} value="0">No</option>
                        <option {{$procedimiento_completo->estado_anestesia == 1 ? 'selected' : ''}} value="1">Si</option>            
                    </select>  
                    @if ($errors->has('estado_anestesia'))
                        <span class="help-block">
                            <strong>{{ $errors->first('estado_anestesia') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                <label for="estado" class="col-xs-4 control-label">Estado</label>
                <div class="col-md-7">
                    <select id="estado" name="estado" class="form-control">
                        <option {{$procedimiento_completo->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                        <option {{$procedimiento_completo->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                    </select>  
                    @if ($errors->has('estado'))
                        <span class="help-block">
                            <strong>{{ $errors->first('estado') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group col-xs-12{{ $errors->has('id_grupo_procedimiento') ? ' has-error' : '' }}">
                <label for="id_grupo_procedimiento" class="col-xs-4 control-label">Grupo al que pertenece</label>
                <div class="col-md-7">
                    <select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control" required>
                        @foreach($grupo_procedimiento as $value)
                        <option @if($procedimiento_completo->id_grupo_procedimiento == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach            
                    </select>  
                    @if ($errors->has('id_grupo_procedimiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id_grupo_procedimiento') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
                                
            <!--Tecnicas-->
            <div class="form-group col-xs-12{{ $errors->has('tecnica_quirurgica') ? ' has-error' : '' }}">
                <label for="tecnica_quirurgica" class="col-md-4 control-label">Tecnicas Quirugicas</label>
                <div class="col-md-7">
                    <textarea id="tecnica_quirurgica" name="tecnica_quirurgica" style="width: 100%; height: 200px;">{{ $procedimiento_completo->tecnica_quirurgica }}
                    </textarea>
                
                    @if ($errors->has('tecnica_quirurgica'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tecnica_quirurgica') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group col-xs-12">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                    Actualizar
                    </button>
                </div>
            </div>    
        </form>
    </div>  
</section>

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
    tinymce.init({
        selector: '#tecnica_quirurgica'
    }); 
    $(document).ready(function() 
    {
        $(".breadcrumb").append('<li><a href="{{ url('tecnicas') }}" style="color: blue;"></i> Procedimientos</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
    });
</script>    

@endsection
