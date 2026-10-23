@extends('laboratorio.examen.base')

@section('action-content')



<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">@lang('laboratorio.parametrosanteriores') {{$parametro->examen->nombre}}</h3></div>
                <div class="box-body">
                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('fecha') ? ' has-error' : '' }}">
                        <label class="control-label">@lang('laboratorio.fecha')</label>
                        <input type="date" name="fecha_anterior" class="form-control pull-right input-sm validar" id="fecha_anterior" required>
                    </div>
                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('fecha') ? ' has-error' : '' }}">
                        <br>
                        <button id="btn_agregar" type="button" class="btn btn-primary" onclick="agregar_referencia_anterior()">@lang('laboratorio.agregar')</button>
                    </div>
                    <div style="margin-bottom: 0px;" id="index_ref_anterior">
                        
                    </div>
                    
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">@lang('laboratorio.agregarparametropara') {{$parametro->examen->nombre}}</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('examen.update_parametro') }}">
                        <input id="id" type="hidden" name="id" value="{{ $parametro->id}}">
                        <input id="id_examen" type="hidden" name="id_examen" value="{{ $parametro->id_examen}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-3 control-label">@lang('laboratorio.nombre')</label>
                            <div class="col-md-7    ">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{ $parametro->nombre }}"  required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-3 control-label">@lang('laboratorio.descripcion')</label>
                            <div class="col-md-7    ">
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value="{{ $parametro->descripcion}}" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('valor1') ? ' has-error' : '' }}">
                            <label for="valor1" class="col-md-3 control-label">@lang('laboratorio.valorreferenciamenor')</label>
                            <div class="col-md-7    ">
                                <input id="valor1" class="form-control input-sm" type="text" name="valor1" value="{{ $parametro->valor1 }}" required autofocus>
                                @if ($errors->has('valor1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('valor1g') ? ' has-error' : '' }}">
                            <label for="valor1g" class="col-md-3 control-label">@lang('laboratorio.valorreferenciamaximo')</label>
                            <div class="col-md-7    ">
                                <input id="valor1g" class="form-control input-sm" type="text" name="valor1g" value="{{ $parametro->valor1g }}" required autofocus>
                                @if ($errors->has('valor1g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1g') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('unidad') ? ' has-error' : '' }}">
                            <label for="unidad" class="col-md-3 control-label">@lang('laboratorio.unidadParametro')</label>
                            <div class="col-md-7    ">
                                <input id="unidad" class="form-control input-sm" type="text" name="unidad" value="{{ $parametro->unidad1 }}"  required autofocus>
                                @if ($errors->has('unidad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-3 control-label">@lang('laboratorio.sexoreferencia')</label>
                            <div class="col-md-7">
                                <select class="form-control input-sm" name="sexo">
                                    <option @if($parametro->sexo == 1){{"selected"}}@endif value="1">@lang('laboratorio.hombre')</option>
                                    <option @if($parametro->sexo == 2){{"selected"}}@endif value="2">@lang('laboratorio.mujer')</option>
                                    <option @if($parametro->sexo == 3){{"selected"}}@endif value="3">@lang('laboratorio.ambos')</option>
                                </select>
                                @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('edad_ini') ? ' has-error' : '' }}">
                            <label for="edad_ini" class="col-md-3 control-label">@lang('laboratorio.edadinicialdelrango')</label>
                            <div class="col-md-7    ">
                                <input id="edad_ini" class="form-control input-sm" type="number" name="edad_ini" value="{{ $parametro->edad_ini}}"  required autofocus>
                                @if ($errors->has('edad_ini'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('edad_ini') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('edad_fin') ? ' has-error' : '' }}">
                            <label for="edad_fin" class="col-md-3 control-label">@lang('laboratorio.edadfinaldelrango')</label>
                            <div class="col-md-7    ">
                                <input id="edad_fin" class="form-control input-sm" type="number" name="edad_fin" value="{{ $parametro->edad_fin}}"  required autofocus>
                                @if ($errors->has('edad_fin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('edad_fin') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('orden') ? ' has-error' : '' }}">
                            <label for="orden" class="col-md-3 control-label">@lang('laboratorio.orden')</label>
                            <div class="col-md-7    ">
                                <input id="orden" class="form-control input-sm" type="number" name="orden" value="{{ $parametro->orden}}"  required autofocus>
                                @if ($errors->has('orden'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('orden') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--div class="form-group col-md-offset-1  col-md-10{{ $errors->has('texto_referencia') ? ' has-error' : '' }}">
                            <label for="texto_referencia" class="col-md-3 control-label">@lang('laboratorio.textoreferencia')</label>
                            <div class="col-md-7">
                                <textarea id="texto_referencia" class="form-control input-sm" type="text" name="texto_referencia" value="" >{{ $parametro->texto_referencia}}</textarea> 
                                @if ($errors->has('texto_referencia'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto_referencia') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div-->

                        <div class="form-group col-md-offset-1  col-md-10{{ $errors->has('texto_referencia') ? ' has-error' : '' }}">
                            <label for="texto_referencia" class="col-md-3 control-label">@lang('laboratorio.textoreferencia')</label>
                            <div class="col-md-7" style="1px solid #ccc;">
                                <div id="tx_textoreferencia" style="background-color: white;">
                                    <?php echo $parametro->texto_referencia; ?>
                                </div>
                            </div>    
                            <input type="hidden" name="texto_referencia" id="texto_referencia">
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-3 control-label">@lang('laboratorio.estado')</label>
                            <div class="col-md-7    ">
                                <select class="form-control input-sm" name="estado">
                                    <option @if($parametro->estado == 0){{"selected"}}@endif value="0">Inactivo</option>
                                    <option @if($parametro->estado == 1){{"selected"}}@endif value="1">Activo</option>
                                </select>
                                @if ($errors->has('estado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-6">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> @lang('laboratorio.agregar')
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
       
        cargar_listado();
        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
        $(".breadcrumb").append('<li><a href="{{route('examen.parametro',['id_examen' => $parametro->id_examen])}}"></i> Parametro</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    tinymce.init({
        selector: '#tx_textoreferencia',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        //readonly: 1,

        setup: function (editor){
            editor.on('init', function (e) {
                var ed = tinyMCE.get('tx_textoreferencia');
                $("#texto_referencia").val(ed.getContent());
            });
        },

        init_instance_callback: function (editor){
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tx_textoreferencia');
                $("#texto_referencia").val(ed.getContent());

            });
        }
    });

    function agregar_referencia_anterior(){

        var fecha = $('#fecha_anterior').val();
        var mensaje = "";
        if( fecha == ''){
            var mensaje = "Ingrese la fecha de validez del parametro \n";
        }

        if(mensaje != ""){
            alert(mensaje);
        }else{
            var confirmar = confirm("¿ Esta seguro de crear un parametro anterior ?");
            if(confirmar){
                
                $.ajax({
                    type: 'post',
                    url:"{{route('examen.crear_referencia_anterior',['id' => $parametro->id ])}}", 
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: {'fecha_valida':fecha },
                    success: function(data){
                        cargar_listado();
                        //console.log(data);
                    },
                    error: function(data){
                        //console.log(data);
                    }
                })
            }
        }

    }

    function cargar_listado(){

        $.ajax({
            type: 'get',
            url:"{{route('examen.listar_referencia_anterior',['id' => $parametro->id ])}}", //receta.editar_detalle
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "html",
            success: function(datahtml){
                $('#index_ref_anterior').empty().html(datahtml);    
            },
            error: function(data){
            }
        });    

    }

    

</script>
@endsection
