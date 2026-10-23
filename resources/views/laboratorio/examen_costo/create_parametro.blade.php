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

                        <div class="form-group col-md-12"><h3 style="text-align: center;">Referencias</h3></div>
                        <div class="form-group col-md-12">
                            <h4 class="col-md-6" style="text-align: center;">Nombre</h4>
                            <h4 class="col-md-2" style="text-align: center;">Mínimo</h4>
                            <h4 class="col-md-2" style="text-align: center;">Máximo</h4>
                            <h4 class="col-md-2" style="text-align: center;">Unidad</h4>
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('texto1') ? ' has-error' : '' }}">
                            <label for="texto1" class="col-md-4 control-label">1.</label>
                            <div class="col-md-7">
                                <input id="texto1" class="form-control input-sm" type="text" name="texto1" value="{{ old('texto1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('texto1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor1') ? ' has-error' : '' }}">
                            
                                <input id="valor1" class="form-control input-sm" type="text" name="valor1" value="{{ old('valor1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor1g') ? ' has-error' : '' }}">
                            
                                <input id="valor1g" class="form-control input-sm" type="text" name="valor1g" value="{{ old('valor1g') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor1g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1g') }}</strong>
                                </span>
                                @endif
                        
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('unidad1') ? ' has-error' : '' }}">
                            
                                <input id="unidad1" class="form-control input-sm" type="text" name="unidad1" value="{{ old('unidad1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('unidad1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad1') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('texto2') ? ' has-error' : '' }}">
                            <label for="texto2" class="col-md-4 control-label">2.</label>
                            <div class="col-md-7">
                                <input id="texto2" class="form-control input-sm" type="text" name="texto2" value="{{ old('texto2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('texto2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor2') ? ' has-error' : '' }}">
                            
                            
                                <input id="valor2" class="form-control input-sm" type="text" name="valor2" value="{{ old('valor2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor2') }}</strong>
                                </span>
                                @endif
                           
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor2g') ? ' has-error' : '' }}">
                            
                                <input id="valor2g" class="form-control input-sm" type="text" name="valor2g" value="{{ old('valor2g') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor2g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor2g') }}</strong>
                                </span>
                                @endif
                        
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('unidad2') ? ' has-error' : '' }}">
                            
                            
                                <input id="unidad2" class="form-control input-sm" type="text" name="unidad2" value="{{ old('unidad2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('unidad2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad2') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('texto3') ? ' has-error' : '' }}">
                            <label for="texto3" class="col-md-4 control-label">3.</label>
                            <div class="col-md-7">
                                <input id="texto3" class="form-control input-sm" type="text" name="texto3" value="{{ old('texto3') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('texto3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto3') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor3') ? ' has-error' : '' }}">
                            
                            
                                <input id="valor3" class="form-control input-sm" type="text" name="valor3" value="{{ old('valor3') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor3') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor3g') ? ' has-error' : '' }}">
                            
                                <input id="valor3g" class="form-control input-sm" type="text" name="valor3g" value="{{ old('valor3g') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor3g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor3g') }}</strong>
                                </span>
                                @endif
                        
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('unidad3') ? ' has-error' : '' }}">
                            
                            
                                <input id="unidad3" class="form-control input-sm" type="text" name="unidad3" value="{{ old('unidad3') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('unidad3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad3') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('texto4') ? ' has-error' : '' }}">
                            <label for="texto4" class="col-md-4 control-label">4.</label>
                            <div class="col-md-7">
                                <input id="texto4" class="form-control input-sm" type="text" name="texto4" value="{{ old('texto4') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('texto4'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto4') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor4') ? ' has-error' : '' }}">
                            
                            
                                <input id="valor4" class="form-control input-sm" type="text" name="valor4" value="{{ old('valor4') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor4'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor4') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('valor4g') ? ' has-error' : '' }}">
                            
                                <input id="valor4g" class="form-control input-sm" type="text" name="valor4g" value="{{ old('valor4g') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('valor4g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor4g') }}</strong>
                                </span>
                                @endif
                        
                        </div>

                        <div class="form-group col-md-2{{ $errors->has('unidad4') ? ' has-error' : '' }}">
                            
                            
                                <input id="unidad4" class="form-control input-sm" type="text" name="unidad4" value="{{ old('unidad4') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autofocus>
                                @if ($errors->has('unidad4'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad4') }}</strong>
                                </span>
                                @endif
                            
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
