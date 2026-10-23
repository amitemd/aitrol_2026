@extends('laboratorio.examen.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Editar Examen</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('examen.update',['id' => $examen->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        
                    
                        
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre Corto</label>
                            <div class="col-md-3">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value=@if(old('nombre')!='')"{{old('nombre')}}"@else"{{$examen->nombre}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Nombre Largo</label>
                            <div class="col-md-7">
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value=@if(old('descripcion')!='')"{{old('descripcion')}}" @else "{{$examen->descripcion}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('tarifario') ? ' has-error' : '' }}">
                            <label for="tarifario" class="col-md-4 control-label">Tarifario</label>
                            <div class="col-md-3">
                                <input id="tarifario" class="form-control input-sm" type="text" name="tarifario" value=@if(old('tarifario')!='')"{{old('tarifario')}}" @else "{{$examen->tarifario}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('tarifario'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tarifario') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--id_agrupador-->
                        <div class="form-group col-md-12{{ $errors->has('id_agrupador') ? ' has-error' : '' }}">
                            <label for="id_agrupador" class="col-md-4 control-label">Agrupador</label>
                            <div class="col-md-3">
                                <select id="id_agrupador" name="id_agrupador" class="form-control" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($agrupadores as $agrupador) 
                                        <option @if(old('id_agrupador')!='') @if(old('id_agrupador')== $agrupador->id) selected @endif @else @if($examen->id_agrupador == $agrupador->id) selected @endif @endif value="{{$agrupador->id}}">{{$agrupador->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                @if ($errors->has('id_agrupador'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_agrupador') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--ESTADO-->
                        <div class="form-group col-md-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-3">
                                <select id="estado" name="estado" class="form-control" required>
                                    <option @if(old('estado')!='') @if(old('estado')== '0') selected @endif @else @if($examen->estado == '0') selected @endif @endif value="0">INACTIVO</option>
                                    <option @if(old('estado')!='') @if(old('estado')== '1') selected @endif @else @if($examen->estado == '1') selected @endif @endif value="1">ACTIVO</option>   
                                </select>  
                                
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('tiempo') ? ' has-error' : '' }}">
                            <label for="tiempo" class="col-md-4 control-label">@lang('laboratorio.tiempo')</label>
                            <div class="col-md-3">
                                <input id="tiempo" class="form-control input-sm" type="text" name="tiempo" maxlength="50" value=@if(old('tiempo')!='')"{{old('tiempo')}}"@else"{{$examen->tiempos}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                                @if ($errors->has('tiempo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tiempo') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-12{{ $errors->has('valor') ? ' has-error' : '' }}">
                            <label for="valor" class="col-md-4 control-label">Valor</label>
                            <div class="col-md-3">
                                <input id="valor" class="form-control input-sm" type="number" step="any" name="valor" value=@if(old('valor')!='')"{{ old('valor') }}" @else"{{$examen->valor}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('valor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                    @foreach($niveles as $nivel)
                        <div class="form-group col-md-12{{ $errors->has('valor'.$nivel->id) ? ' has-error' : '' }}">
                            <label for="valor{{$nivel->id}}" class="col-md-4 control-label">{{$nivel->nombre}}</label>
                            <div class="col-md-3">
                                <input id="valor{{$nivel->id}}" class="form-control input-sm" type="number" step="any" name="valor{{$nivel->id}}" 
                                value=@if(old('valor'.$nivel->id)!='')"{{ old('valor'.$nivel->id) }}" @else"{{$nivel_seg[$nivel->id]}}" @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('valor'.$nivel->id))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor'.$nivel->id) }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    @endforeach                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Editar
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
        $(".breadcrumb").append('<li class="active">Editar</li>');
           

    });

    

</script>
@endsection
