@extends('hc_admision.medicina.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><div class="col-md-9"><h3 class="box-title">Crear Medicina</h3></div><div class="col-md-3"><a type="button" href="{{URL::previous()}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a></div></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('medicina2.store2') }}">
                        <input type="hidden" name="agenda" value="{{$agenda}}">
                        {{ csrf_field() }}

                        <input type="hidden" name="ruta" value="{{$ruta}}">
                        <div class="form-group col-md-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Nombre</label>
                            
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" maxlength="50" value="{{old('nombre')}}" required autofocus >
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('cantidad') ? ' has-error' : '' }}">
                            <label for="cantidad" class="control-label">Cantidad a Prescribir</label>
                            
                                <input id="cantidad" class="form-control input-sm" type="text" name="cantidad" maxlength="50" value="{{old('cantidad')}}" required autofocus>
                                @if ($errors->has('cantidad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cantidad') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('dosis') ? ' has-error' : '' }}">
                            <label for="dosis" class="control-label">Dosis</label>
                            
                                <textarea id="dosis" class="form-control input-sm" name="dosis" required>{{old('dosis')}}</textarea>
                                @if ($errors->has('dosis'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('dosis') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        
                        <div class="form-group col-md-6 {{ $errors->has('genericos') ? ' has-error' : '' }}">
                            <label for="genericos" class="control-label">Genéricos </label>
                                    <select id="genericos" class="form-control input-sm select2" name="genericos[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off" @if(old('dieta') != '1') required @endif >
                                        @foreach($genericos as $generico) 
                                            <option value="{{$generico->id}}">{{$generico->nombre}}</option>
                                        @endforeach
                                    </select>

                                @if ($errors->has('genericos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('genericos') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        <!--ESTADO-->
                        <div class="form-group col-md-3{{ $errors->has('dieta') ? ' has-error' : '' }}">
                            <label for="dieta" class="control-label">Es una Recomendacion</label>
                            
                                <select id="dieta" name="dieta" class="form-control" required>
                                    <option @if(old('dieta')== '0') selected @endif value="0">No</option>
                                    <option @if(old('dieta')== '1') selected @endif value="1">Si</option> 
                                      
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
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Crear Medicina
                                </button>
                            </div>
                        </div>
                    </form>

                    <form id="frm_gen">
                        <!--div class="col-md-4" >
                            <label for="presion" class="control-label">Agregar Nuevo Genérico</label>
                            <div class="col-md-10">  
                                <input class="form-control input-sm" name="generico" id="generioc_muestra">
                            </div>
                            <div class="col-md-2">
                                <a class="btn btn-success btn-sm" onclick="guardar_generico()">
                                    Agregar
                                </a>
                            </div>
                        </div--> 
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

    function guardar_generico(){

        $.ajax({
          type: 'post',
          url:"{{route('generico2.store2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_gen").serialize(),
          success: function(data){
            $("#genericos").append('<option selected value="'+data.id+'">'+data.nombre+'</option>');
            $("#generioc_muestra").val('');
            console.log(data);
          },
          error: function(data){
            
          }
        });
    }

    

</script>
@endsection
