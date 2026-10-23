@extends('laboratorio.examen.base')

@section('action-content')



<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agregar Parámetro para {{$examen->nombre}}</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('examen.store_parametro') }}">
                        {{ csrf_field() }}
                    
                        <input type="hidden" name="id_examen" value="{{$examen->id}}">
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-3 control-label">Nombre</label>
                            <div class="col-md-8">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        

                        <div class="form-group col-md-12{{ $errors->has('valor1') ? ' has-error' : '' }}">
                            <label for="valor1" class="col-md-3 control-label">Mínimo</label>
                            <div class="col-md-3">
                                <input id="valor1" class="form-control input-sm" type="text" name="valor1" value="{{ old('valor1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('valor1g') ? ' has-error' : '' }}">
                            <label for="valor1" class="col-md-3 control-label">Máximo</label>
                            <div class="col-md-3">
                                <input id="valor1g" class="form-control input-sm" type="text" name="valor1g" value="{{ old('valor1g') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor1g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1g') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('unidad1') ? ' has-error' : '' }}">
                            <label for="valor1" class="col-md-3 control-label">Unidad</label>
                            <div class="col-md-3">
                                <input id="unidad1" class="form-control input-sm" type="text" name="unidad1" value="{{ old('unidad1') }}" autofocus>
                                @if ($errors->has('unidad1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('texto_referencia') ? ' has-error' : '' }}">
                            <label for="texto_referencia" class="col-md-3 control-label">Texto de Referencia</label>
                            <div class="col-md-8">
                                <textarea id="texto_referencia" class="form-control input-sm" type="text" name="texto_referencia" value="" >{{ old('texto_referencia') }}</textarea> 
                                @if ($errors->has('texto_referencia'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto_referencia') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12 {{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-3 control-label">Sexo de Referencia</label>
                            <div class="col-md-3">
                                <select class="form-control input-sm" name="sexo">
                                    <option  value="3">Ambos</option>
                                    <option  value="1">Hombre</option>
                                    <option  value="2">Mujer</option>
                                </select>
                                @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        

                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-6">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Agregar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>



<script type="text/javascript">

    $(document).ready(function() {
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
        $(".breadcrumb").append('<li><a href="{{route('examen.parametro',['id_examen' => $examen->id])}}"></i> Parametro</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
