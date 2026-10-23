@extends('seguro.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Actualizar Seguro</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('form_enviar_seguro.update', ['id' => $seguro->id]) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" class="form-control" name="horacrea" value="{{ $seguro->created_at }}">
                        <input id="nombre1" type="hidden" class="form-control" name="nombre1" value="{{ $seguro->nombre }}" >
                        <input id="color1" type="hidden" type="text" class="form-control" name="color1" value="{{ $seguro->color }}" >
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $seguro->nombre }}" required autofocus style="text-transform:uppercase;">

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Descripcion</label>

                            <div class="col-md-6">
                                <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $seguro->descripcion }}" required>

                                @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($seguro->tipo != 2)
                            <div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
                                <label for="tipo"    class="col-md-4 control-label">Tipo de Seguro</label>

                                <div class="col-md-6">
                                    <select id="tipo" class="form-control" name="tipo" onchange="campos();"> 
                                        <option value="0" <?php if($seguro->tipo == '0'){?> selected="selected"<?php }?> >Publico</option>
                                        <option value="1" <?php if($seguro->tipo == '1'){?> selected="selected"<?php }?> >Privado</option>
                                    </select>
                                    
                                </div>
                            </div>
                        @else
                            <input id="tipo" type="hidden" type="text" class="form-control" name="tipo" value="2" required>
                        @endif
                        
                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">Color de la Etiqueta</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $seguro->color }}" required>
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($seguro->tipo != 2)
                            <div class="form-group{{ $errors->has('codigo_validacion') ? ' has-error' : '' }}">
                                <label for="codigo_validacion" class="col-md-4 control-label">Código de Validación</label>

                                <div class="col-md-6">
                                    <input @if($seguro->codigo_validacion=="SI") checked @endif type="checkbox" name="codigo_validacion" value="SI"> SI <br>
                                    
                                    
                                </div>
                            </div>
                            @if($seguro->tipo == 2)
                            <div class="form-group oculto" id="cambio1" >
                                <label for="tipo" class="col-md-4 control-label">Posee Url de Verificacion</label>
                                <div class="col-md-6">
                                    <input type="checkbox" name="posee_url" id="posee_url"> SI <br>                                    
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('url_validacion') ? ' has-error' : '' }} oculto" id="cambio2">
                                <label for="url_validacion" class="col-md-4 control-label">URL de Validacion</label>

                                <div class="col-md-6">
                                    <input id="url_validacion" type="text" class="form-control" name="url_validacion" >

                                    @if ($errors->has('url_validacion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('url_validacion') }}</strong>
                                        </span>
                                    @endif 
                                </div>
                            </div>
                            @endif
                            @if($seguro->tipo == 1)
                            <div class="form-group " id="cambio1" >
                                <label for="tipo" class="col-md-4 control-label">Posee Url de Verificacion</label>
                                <div class="col-md-6">
                                    <input type="checkbox" name="posee_url" id="posee_url" @if($seguro->url_validacion !="") checked @endif> SI <br>                                    
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('url_validacion') ? ' has-error' : '' }} @if($seguro->url_validacion == "") oculto @endif" id="cambio2">
                                <label for="url_validacion" class="col-md-4 control-label">URL de Validacion</label>

                                <div class="col-md-6">
                                    <input id="url_validacion" type="text" class="form-control" name="url_validacion" value="{{ $seguro->url_validacion }}" >

                                    @if ($errors->has('url_validacion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('url_validacion') }}</strong>
                                        </span>
                                    @endif 
                                </div>
                            </div>
                            @endif
                        @endif
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

  function campos()
  {
      var valor = document.getElementById("tipo").value;
      var elemento1 = document.getElementById("cambio1");
      if(valor == 0){
          $(elemento1).addClass('oculto');
      }
      if(valor == 1){
          $(elemento1).removeClass('oculto');
      }
  }

  $( '#posee_url' ).on( 'click', function() {
    var elemento2 = document.getElementById("cambio2");
    if( $(this).is(':checked') ){
        // Hacer algo si el checkbox ha sido seleccionado
        $(elemento2).removeClass('oculto');
        $('#url_validacion').prop("required", true);

    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $(elemento2).addClass('oculto');
        $('#url_validacion').removeAttr("required");
    }
    });

</script>
@endsection
<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>