        @extends('tecnicas.base')

        @section('action-content')
        <section class="content">
            <div class="box box-primary">
                <div class="box-header"><h3 class="box-title">Agregar Nuevo Procedimiento</h3>
                </div>
                <div class="box-body"> 
                    <form class="form-vertical" role="form" method="POST" action="{{ route('tecnicas.store') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="agenda" value="{{$agenda}}">
                        <div class="form-group col-xs-12{{ $errors->has('nombre_general') ? ' has-error' : '' }}">
                            <label for="nombre_general" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre_general" type="text" class="form-control" name="nombre_general" value="" required autofocus>
                                @if ($errors->has('nombre_general'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre_general') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('id_grupo_procedimiento') ? ' has-error' : '' }}">
                            <label for="id_grupo_procedimiento" class="col-xs-4 control-label">Grupo al que pertenece</label>
                            <div class="col-md-7">
                                <select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control" required>
                                    <option value="">Seleccione..</option>
                                    @foreach($grupo_procedimiento as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach            
                                </select>  
                                @if ($errors->has('id_grupo_procedimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_grupo_procedimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('estado_anestesia') ? ' has-error' : '' }}">
                            <label for="estado_anestesia" class="col-xs-4 control-label">Posee Record Anestesico</label>
                            <div class="col-md-7">
                                <select id="estado_anestesia" name="estado_anestesia" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>            
                                </select>  
                                @if ($errors->has('estado_anestesia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado_anestesia') }}</strong>
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
                    </form>
                </div>
            </div>
        </section>


        <script type="text/javascript">
            $(document).ready(function() 
            {
                $(".breadcrumb").append('<li><a href="{{ url('tecnicas') }}" style="color: blue;"></i> Procedimientos</a></li>');
                $(".breadcrumb").append('<li class="active">Agregar</li>');
            });        
        </script>
        @endsection
