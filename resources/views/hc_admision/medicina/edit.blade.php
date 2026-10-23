@extends('hc_admision.medicina.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><div class="col-md-9"><h3 class="box-title">Editar Medicina</h3></div><div class="col-md-3"></div><a type="button" href="{{route('medicina.index',['agenda' => $agenda])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('medicina.update',['id' => $medicina->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        
                        <input type="hidden" name="agenda" value="{{$agenda}}">
                        <div class="form-group col-md-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Nombre</label>
                            
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" maxlength="50" value=@if(old('nombre')!='')"{{old('nombre')}}" @else"{{$medicina->nombre}}" @endif"  required autofocus >
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('cantidad') ? ' has-error' : '' }}">
                            <label for="cantidad" class="control-label">Cantidad a Prescribir</label>
                            
                                <input id="cantidad" class="form-control input-sm" type="text" name="cantidad" maxlength="50" value=@if(old('cantidad')!='')"{{old('cantidad')}}"@else"{{$medicina->cantidad}}"@endif"  required autofocus>
                                @if ($errors->has('cantidad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cantidad') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('dosis') ? ' has-error' : '' }}">
                            <label for="dosis" class="control-label">Dosis</label>
                            
                                <textarea id="dosis" class="form-control input-sm" name="dosis" required>@if(old('dosis')!=''){{old('dosis')}} @else {{$medicina->dosis}} @endif </textarea>
                                @if ($errors->has('dosis'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('dosis') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <!--div class="form-group col-md-6{{ $errors->has('concentracion') ? ' has-error' : '' }}">
                            <label for="concentracion" class="control-label">Concentración</label>
                            
                                <input id="concentracion" class="form-control input-sm" type="text" name="concentracion" maxlength="100" value=@if(old('concentracion')!='')"{{old('concentracion')}}"@else"{{$medicina->concentracion}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('concentracion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('concentracion') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('presentacion') ? ' has-error' : '' }}">
                            <label for="presentacion" class="control-label">Presentación</label>
                            
                                <input id="presentacion" class="form-control input-sm" type="text" name="presentacion" maxlength="50" value=@if(old('presentacion')!='')"{{old('presentacion')}}"@else"{{$medicina->presentacion}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('presentacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('presentacion') }}</strong>
                                </span>
                                @endif
                            
                        </div-->
                        

                        <!--ESTADO-->
                        <div class="form-group col-md-3{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="control-label">Estado</label>
                            
                                <select id="estado" name="estado" class="form-control" required>
                                    <option @if(old('estado')!='') @if(old('estado')== '0') selected @endif @else @if($medicina->estado == '0') selected @endif @endif value="0">INACTIVO</option>
                                    <option @if(old('estado')!='') @if(old('estado')== '1') selected @endif @else @if($medicina->estado == '1') selected @endif @endif value="1">ACTIVO</option>   
                                </select>  
                                
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <!--PUBLICO - PRIVADO-->
                        <!--div class="form-group col-md-3{{ $errors->has('publico_privado') ? ' has-error' : '' }}">
                            <label for="publico_privado" class="control-label">Tipo</label>
                            
                                <select id="publico_privado" name="publico_privado" class="form-control" required>
                                    <option @if(old('publico_privado')!='') @if(old('publico_privado')== '0') selected @endif @else @if($medicina->publico_privado == '0') selected @endif @endif value="0">PUBLICO</option>
                                    <option @if(old('publico_privado')!='') @if(old('publico_privado')== '1') selected @endif @else @if($medicina->publico_privado == '1') selected @endif @endif value="1">PRIVADO</option>   
                                </select>  
                                
                                @if ($errors->has('publico_privado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('publico_privado') }}</strong>
                                    </span>
                                @endif
                            
                        </div-->

                        
                        <div class="form-group col-md-6 {{ $errors->has('genericos') ? ' has-error' : '' }}">
                            <label for="genericos" class="control-label">Genéricos </label>
                            
                                    <select id="genericos" class="form-control input-sm select2" name="genericos[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off" @if(old('dieta')!='') @if(old('dieta')== '0') required @endif @else @if($medicina->dieta == '0') required @endif @endif>
                                        @foreach($genericos as $generico)
                                            @php $gid = $medicina_principio->where('id_principio_activo',$generico->id)->first(); @endphp 
                                            <option @if(!is_null($gid)) selected @endif value="{{$generico->id}}">{{$generico->nombre}}</option>
                                        @endforeach
                                    </select>

                                @if ($errors->has('genericos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('genericos') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-3{{ $errors->has('dieta') ? ' has-error' : '' }}">
                            <label for="dieta" class="control-label">Es una Recomendacion</label>
                            
                                <select id="dieta" name="dieta" class="form-control" required>
                                    <option @if(old('dieta')!='') @if(old('dieta')== '0') selected @endif @else @if($medicina->dieta == '0') selected @endif @endif value="0">No</option>
                                    <option @if(old('dieta')!='') @if(old('dieta')== '1') selected @endif @else @if($medicina->dieta == '1') selected @endif @endif value="1">Si</option> 
                                      
                                </select>  
                                
                                @if ($errors->has('dieta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dieta') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                 

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

    });

    $('.select2').select2({
        tags: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            return {
                id: term.toUpperCase()+'xnose',
                text: term.toUpperCase(),
                newTag: true, // add additional parameters
            }
        }
    });

    $('#dieta').on('change', function(){
        dieta =  $('#dieta').val();
        if(dieta == 0){
            $('#genericos').prop("required", true);
        }
        if(dieta == 1){
            $('#genericos').removeAttr("required");
        }
    })
</script>
@endsection
