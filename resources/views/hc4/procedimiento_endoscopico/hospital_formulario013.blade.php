<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .mce-content-body{

        background-color: inherit !important;

    }

   

    .parcial_border {
        border-top-color: #2276d2;
        border-bottom-color: #2276d2;
        border-top-width: 3px;
        border-bottom-width: 3px;
        border-top-style: solid;
        border-bottom-style: solid;
        width: 90%;
        margin-left: 5%;
    }

    .td-border-bottom {
        border-bottom: 1px solid;
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="row">
            <div class="d-flex align-items-center col-md-12">


                <h4 class="card-title ml-25 colorbasic">
                    Formato 013
                </h4>

            </div>
        </div>
    </div>
    <div class="card-body">
        
        <form class="form-vertical" id="form13" name="form13" role="form" method="POST">
            
            {{ csrf_field() }}

            <input id="id" type="hidden" name="id" value="{{ $datos->id }}">
            <input id="id_empresa" type="hidden" name="id_empresa" value="{{ $datos->id_empresa }}">
            <input id="id_hc_procedimiento" type="hidden" name="id" value="{{ $datos->id_hc_procedimiento }}">
            
            <div class="row">
    
                <div class="col-md-12">
                    <div class="{{ $errors->has('resumen_clinico') ? ' has-error' : '' }}">
                        <label for="tresumen_clinico" class="control-label">{{trans('bitacora.resumen_clinico')}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="tresumen_clinico" style="border: solid 1px; border-color: #CED9DA; overflow:auto !important;max-height: 150px;resize:none;background-color: white;">@if(!is_null($datos))<?php echo ($datos->resumen_clinico) ?> @endif</div>
                    <input type="hidden" name="resumen_clinico" id="resumen_clinico">
                </div>
           
                <div class="col-md-12">
                    <div class="row{{ $errors->has('estudio_solicitado') ? ' has-error' : '' }}" style="margin-top:10px">
                        <label for="estudio_solicitado" class="control-label">{{trans('bitacora.estudio_solicitado')}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select onchange="actualizar()" name="estudio_solicitado" id="estudio_solicitado" class="form-control">
                        <option value="0" @if($datos->estudio_solicitado == 0) selected @endif>{{ trans('bitacora.hispatologia') }}</option>
                        <option value="1" @if($datos->estudio_solicitado == 1) selected @endif>{{ trans('bitacora.citogia') }}</option>
                    </select>
                    @if ($errors->has('estudio_solicitado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('estudio_solicitado') }}</strong>
                    </span>
                    @endif
                </div>
            
                <div class="col-md-12">
                    <div class="{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="control-label">{{trans('bitacora.descripcion_estudio')}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <textarea id="descripcion" onchange="actualizar()" class="form-control input-sm" type="text" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase; resize:none;" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>{{ $datos->descripcion_estudio }}</textarea>
                    @if ($errors->has('descripcion'))
                    <span class="help-block">
                        <strong>{{ $errors->first('descripcion') }}</strong>
                    </span>
                    @endif
                </div>
           
                <div class="col-md-12">
                    <div class="{{ $errors->has('prioridad') ? ' has-error' : '' }}">
                        <label for="prioridad" class="control-label">{{trans('bitacora.prioridad')}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select name="prioridad" onchange="actualizar()" id="prioridad" class="form-control input-sm">
                        <option value="0" @if($datos->prioridad == 0) selected @endif>{{trans('bitacora.urgente')}}</option>
                        <option value="1" @if($datos->prioridad == 1) selected @endif>{{trans('bitacora.rutina')}}</option>
                        <option value="2" @if($datos->prioridad == 2) selected @endif>{{trans('bitacora.control')}}</option>
                    </select>
                    @if ($errors->has('prioridad'))
                    <span class="help-block">
                        <strong>{{ $errors->first('prioridad') }}</strong>
                    </span>
                    @endif
                </div>
          
                <div class="col-md-12">
                    <div class="{{ $errors->has('tratamiento') ? ' has-error' : '' }}">
                        <label for="tratamiento" class="control-label">{{trans('bitacora.tratamiento')}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <textarea id="tratamiento" onchange="actualizar()" class="form-control input-sm" type="text" name="tratamiento" value="{{ old('tratamiento') }}" style="text-transform:uppercase; resize:none;" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>{{ $datos->tratamiento }}</textarea>
                    @if ($errors->has('tratamiento'))
                    <span class="help-block">
                        <strong>{{ $errors->first('tratamiento') }}</strong>
                    </span>
                    @endif
                </div>
            
                <div class="col-md-12">
                    <div class="{{ $errors->has('fecha') ? ' has-error' : '' }}">
                        <label for="fecha" class="control-label">{{trans('bitacora.fecha_toma')}}</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <input id="fecha" type="date" onchange="actualizar()" class="form-control input-sm" name="fecha" value="{{ $fecha_toma }}">
                    @if ($errors->has('fecha'))
                    <span class="help-block">
                        <strong>{{ $errors->first('fecha') }}</strong>
                    </span>
                    @endif
                </div>
            
                <!--div class="col-md-12" style="text-align: center;">
                    <a target="_blank" href="{{route('nuevoArchivoPlano.formulario_013', ['id'=>$datos->id_hc_procedimiento])}} " class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('laboratorio.descargar') }} EXCEL</a>
                </div-->
                <div class="col-md-3" style="text-align: center;">
                    <button  type="button" class="btn btn-success"> Guardar</button>
                </div>
                
                <div class="col-md-3" style="text-align: center;">
                    <a target="_blank" href="{{route('nuevoArchivoPlano.formulario_013_pdf', ['id'=>$datos->id_hc_procedimiento])}} " class="btn btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ trans('laboratorio.descargar') }} PDF</a>
                </div>
                
            </div>
        </form>
          
    </div>
</div>    

<!--script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script-->
<script src="{{ asset ("/js/tinymce/tinymce.min.js") }}"></script>
<script text="text/javascript">
    $(document).ready(function() {
        return tinymce.init({
            selector: '#tresumen_clinico',
            height: 100,
            width: 550,
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:12px;}",
            plugins: ['image', 'charmap', 'uploadimage'],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link uploadimage | charmap ',
            theme: 'modern',
            relative_urls: false,
            remove_script_host: false,
            document_base_url: (!window.location.origin ? window.location.protocol + '//' + window.location.host : window.location.origin) + '/',
            setup: function(editor) {
                editor.on('init', function(e) {
                    var ed = tinyMCE.get('tresumen_clinico');
                    //alert(ed.getContent());
                    $("#resumen_clinico").val(ed.getContent());

                });
            },
            init_instance_callback: function(editor) {
                editor.on('Change', function(e) {
                    var ed = tinyMCE.get('tresumen_clinico');
                    $("#resumen_clinico").val(ed.getContent());
                    actualizar();
                });
            }
        });
    });

    function actualizar() {
        $.ajax({
            //async: true,
            type: "POST",
            url: "{{route('hc4.formulario013.update')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: $("#form13").serialize(),
            success: function(data) {
                
            },
            error: function(data) {
                swal({
                    title: "Error!",
                    type: "error",
                    html: data.msj
                });
            }
        });
    }
</script>