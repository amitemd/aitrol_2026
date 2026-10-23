@extends('hc_admision.epicrisis.base')

@section('action-content')


<style type="text/css">
    .mce-branding {
        display: none;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 15px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">



<div class="modal fade fullscreen-modal" id="Crea_Actualiza" tabindex="-1" role="dialog" aria-labelledby="Crea_ActualizaLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row ">
        <div class="col-md-10" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Paciente: </b></td>
                                    <td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td>
                                    <td>{{$agenda->id_paciente}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a type="button" href="{{ URL::previous() }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>

        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a target="_blank" class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('epicrisis.imprimir',['id' => $epicrisis->id])}}"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Descargar</button>
                    </a>

                    <a href="{{route('formulario.006',['id' => $epicrisis->id])}}" target="_blank" class="btn btn-danger">Descargar Nuevo Formato<i class="fa fa-file-pdf-o" aria-hidder="true"></i></a>
                </div>
                <div class="box-body">

                    <form id="frm">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="hcid" value="{{$hca->hcid}}">
                        <input type="hidden" name="epicrisis" value="@if(!is_null($epicrisis)){{$epicrisis->id}}@endif">
                        <input type="hidden" name="protocolo_id" value="{{$protocolo->id}}">
                        <input type="hidden" name="hc_id_procedimiento" value="{{$id}}">
                        <div class="input-group date col-md-4">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="@if(!is_null($epicrisis->fecha_imprime)){{$epicrisis->fecha_imprime}}@else{{$protocolo->created_at}}@endif" name="fecha_imprime" class="form-control input-sm" id="fecha_imprime" onchange="guardar()">
                        </div>
                        <br>

                        <div class="form-group col-md-12" style="background-color: #ccffff; ">
                            <label for="tcuadro" class="control-label">RESUMEN DEL CUADRO CLÍNICO</label>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="favorable_des" class="col-md-12 control-label">EVOLUCIÓN PRE</label>
                        </div>
                        <div class="form-group col-md-12">
                            <div id="tcuadro" style="border: solid 1px;"><?php echo $epicrisis->cuadro_clinico ?></div>
                            <input type="hidden" name="cuadro" id="cuadro">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="favorable_des" class="col-md-12 control-label">EVOLUCIÓN POST</label>
                        </div>
                        <div class="form-group col-md-12">

                            <div id="tfavorable_des" style="border: solid 1px;"><?php echo $epicrisis->favorable_des ?></div>
                            <input type="hidden" name="favorable_des" id="favorable_des">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="conclusion" class="col-md-12 control-label">CONCLUSION</label>
                        </div>
                        <div class="form-group col-md-12">

                            <div id="tconclusion" style="border: solid 1px;"><?php echo $protocolo->conclusion ?></div>
                            <input type="hidden" name="conclusion" id="conclusion">
                        </div>


                        <div class="form-group col-md-12">
                            <label for="indicaciones_alta" class="col-md-12 control-label">INDICACIONES DE ALTA / EGRESO </label>
                        </div>
                        <div class="form-group col-md-12">

                            <div id="tindicaciones_alta" style="border: solid 1px;"><?php echo $epicrisis->indicaciones_alta ?></div>
                            <input type="hidden" name="indicaciones_alta" id="indicaciones_alta">
                        </div>




                        <div>&nbsp;</div>


                        <div class="form-group col-md-12" style="background-color: #ccffff;"><b>RESUMEN DE EVOLUCION Y COMPLICACIONES</b></div>

                        <div class="form-group col-md-6 {{ $errors->has('ep_resumen_evolucion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="ep_resumen_evolucion" class="col-md-12 control-label">EVOLUCION</label>
                            <div class="col-md-12">
                                <input type="text" name="ep_resumen_evolucion" id="ep_resumen_evolucion" value="@if(!is_null($epicrisis)) @if($epicrisis->ep_resumen_evolucion==null) FAVORABLE @else {{$epicrisis->ep_resumen_evolucion}} @endif @else FAVORABLE @endif" onchange="guardar();">
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('complicacion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="complicacion" class="col-md-12 control-label">COMPLICACION</label>
                            <div class="col-md-12">
                                <input type="text" name="complicacion" id="complicacion" value="@if(!is_null($epicrisis)) @if($epicrisis->complicacion==null) NINGUNA @else {{$epicrisis->complicacion}} @endif @else NINGUNA @endif" onchange="guardar();">
                            </div>
                        </div>

                        <div class="form-group col-md-12" style="background-color: #ccffff;"><b>CONDICIONES DE EGRESO Y PRONOSTICO</b></div>

                        <div class="form-group col-md-6 {{ $errors->has('condicion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="condicion" class="col-md-12 control-label">CONDICION</label>
                            <div class="col-md-12">
                                <input type="text" name="condicion" id="condicion" value="@if(!is_null($epicrisis)) @if($epicrisis->condicion==null) MEJORADA @else {{$epicrisis->condicion}} @endif @else MEJORADA @endif" onchange="guardar();">
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('pronostico') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="pronostico" class="col-md-12 control-label">PRONOSTICO</label>
                            <div class="col-md-12">
                                <input type="text" name="pronostico" id="pronostico" value="@if(!is_null($epicrisis)) @if($epicrisis->pronostico==null) BUENA @else {{$epicrisis->pronostico}} @endif @else BUENA @endif" onchange="guardar();">
                            </div>
                        </div>



                        <div class="form-group col-md-3 {{ $errors->has('alta') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="alta" class="col-md-12 control-label">ALTA</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="alta" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('alta')!='' )@if(old('alta')=='DEFINITIVA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->alta=='DEFINITIVA') selected @endif @endif value="DEFINITIVA">DEFINITIVA</option>
                                    <option @if(old('alta')!='' )@if(old('alta')=='TRANSITORIA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->alta=='TRANSITORIA') selected @endif @endif value="TRANSITORIA">TRANSITORIA</option>
                                    <option @if(old('alta')!='' )@if(old('alta')=='MEDICA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->alta=='MEDICA') selected @endif @endif value="MEDICA">MEDICA</option>
                                    <option @if(old('alta')!='' )@if(old('alta')=='VOLUNTARIA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->alta=='VOLUNTARIA') selected @endif @endif value="VOLUNTARIA">VOLUNTARIA</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('discapacidad') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="discapacidad" class="col-md-12 control-label">DISCAPACIDAD</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="discapacidad" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('discapacidad')!='' )@if(old('discapacidad')=='ASINTOMÁTICA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->discapacidad=='ASINTOMÁTICA') selected @endif @endif value="ASINTOMÁTICA">ASINTOMÁTICA</option>
                                    <option @if(old('discapacidad')!='' )@if(old('discapacidad')=='LEVE' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->discapacidad=='LEVE') selected @endif @endif value="LEVE">LEVE</option>
                                    <option @if(old('discapacidad')!='' )@if(old('discapacidad')=='MODERADA' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->discapacidad=='MODERADA') selected @endif @endif value="MODERADA">MODERADA</option>
                                    <option @if(old('discapacidad')!='' )@if(old('discapacidad')=='GRAVE' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->discapacidad=='GRAVE') selected @endif @endif value="GRAVE">GRAVE</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('retiro') ? ' has-error' : '' }}">
                            <label for="retiro" class="col-md-12 control-label">RETIRO</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="retiro" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('retiro')!='' )@if(old('retiro')=='AUTORIZADO' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->retiro=='AUTORIZADO') selected @endif @endif value="AUTORIZADO">AUTORIZADO</option>
                                    <option @if(old('retiro')!='' )@if(old('retiro')=='NO AUTORIZADO' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->retiro=='NO AUTORIZADO') selected @endif @endif value="NO AUTORIZADO">NO AUTORIZADO</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('defuncion') ? ' has-error' : '' }}">
                            <label for="defuncion" class="col-md-12 control-label">DEFUNCIÓN</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="defuncion" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('defuncion')!='' )@if(old('defuncion')=='MENOS DE 48H' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->defuncion=='MENOS DE 48H') selected @endif @endif value="MENOS DE 48H">MENOS DE 48H</option>
                                    <option @if(old('defuncion')!='' )@if(old('defuncion')=='MAS DE 48H' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->defuncion=='MAS DE 48H') selected @endif @endif value="MAS DE 48H">MAS DE 48H</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="dias_estadia" class="col-md-12 control-label" style="padding-left: 0px;">DIAS DE ESTADIA</label>
                            <input id="dias_estadia" type="number" class="form-control input-sm" name="dias_estadia" value="@if(old('dias_estadia')!=''){{old('dias_estadia')}}@elseif(!is_null($epicrisis)){{$epicrisis->dias_estadia}}@endif" onchange="guardar();">

                        </div>

                        <div class="form-group col-md-3">
                            <label for="dias_incapacidad" class="col-md-12 control-label">DIAS INCAPACIDAD</label>
                            <input id="dias_incapacidad" type="number" class="form-control input-sm" name="dias_incapacidad" value="@if(old('dias_incapacidad')!=''){{old('dias_incapacidad')}}@elseif(!is_null($epicrisis)){{$epicrisis->dias_incapacidad}}@endif" onchange="guardar();">

                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('receta') ? ' has-error' : '' }}">
                            <label for="receta" class="col-md-12 control-label">RECETA</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="receta" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('receta')!='' )@if(old('receta')=='SI' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->receta=='SI') selected @endif @endif value="SI">SI</option>
                                    <option @if(old('receta')!='' )@if(old('receta')=='NO' ) selected @endif @elseif(!is_null($epicrisis))@if($epicrisis->receta=='NO') selected @endif @endif value="NO">NO</option>
                                </select>
                            </div>
                        </div>

                    </form>

                    <div class="col-md-12">
                        <form id="frm_cie">
                            <input type="hidden" name="codigo" id="codigo">


                            <label for="cie10" class="col-md-6 control-label" style="padding-left: 0px;background-color: #ccffff;"><b>DIAGNÓSTICO PRESUNTIVO/DEFINITIVO</b></label>
                            <div class="form-group col-md-8" style="padding: 1px;">
                                <input id="cie10" type="text" class="form-control input-sm" name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico">
                            </div>
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <select id="ing_egr" name="pre_def" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                    <option value="INGRESO">INGRESO</option>
                                    <option value="EGRESO">EGRESO</option>
                                </select>
                            </div>

                            <div class="form-group col-md-1" style="padding: 1px;">
                                <button id="bagregar" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                            </div>



                            <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                                </table>
                            </div>
                        </form>
                    </div>

                </div>





            </div>
        </div>
    </div>




</div>



<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
    $(document).ready(function() {

        //Initialize Select2 Elements
        $('.select2').select2();

        cargar_tabla();



        /*$(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');
        $(".breadcrumb").append('<li class="active">Atención</li>');*/



        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>') + "años";

        $('#edad').text(edad);

        $('#fecha_imprime').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
        }).on('dp.change', function(e) {
            guardar()
        });




    });


    function guardar() {

        //alert("ok");
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.actualiza')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },

            datatype: 'json',
            data: $("#frm").serialize(),
            success: function(data) {
                //console.log(data);
                //alert(data);

            },
            error: function(data) {



            }
        });
    }



    function formatRepoSelection(repo) {
        return repo.full_name || repo.text;
    }

    tinymce.init({
        selector: '#tcuadro',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tcuadro');
                $("#cuadro").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tcuadro');
                $("#cuadro").val(ed.getContent());
                guardar();

            });
        }
    });

    tinymce.init({
        selector: '#tfavorable_des',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tfavorable_des');
                $("#favorable_des").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tfavorable_des');
                $("#favorable_des").val(ed.getContent());
                guardar();

            });
        }
    });


    tinymce.init({
        selector: '#thallazgos',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('thallazgos');
                $("#hallazgos").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('thallazgos');
                $("#hallazgos").val(ed.getContent());
                guardar();

            });
        }
    });

    tinymce.init({
        selector: '#tconclusion',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tconclusion');
                $("#conclusion").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tconclusion');
                $("#conclusion").val(ed.getContent());
                guardar();

            });
        }
    });

    ///// pilas

    tinymce.init({
        selector: '#tindicaciones_alta',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tindicaciones_alta');
                $("#indicaciones_alta").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tindicaciones_alta');
                $("#indicaciones_alta").val(ed.getContent());
                guardar();

            });
        }
    });

    tinymce.init({
        selector: '#tresumen',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tresumen');
                $("#resumen").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tresumen');
                $("#resumen").val(ed.getContent());
                guardar();

            });
        }
    });

    tinymce.init({
        selector: '#tcondicion',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tcondicion');
                $("#condicion").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tcondicion');
                $("#condicion").val(ed.getContent());
                guardar();

            });
        }
    });

    tinymce.init({
        selector: '#tpronostico',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        setup: function(editor) {
            editor.on('init', function(e) {
                var ed = tinyMCE.get('tpronostico');
                $("#pronostico").val(ed.getContent());
            });
        },



        init_instance_callback: function(editor) {
            editor.on('Change', function(e) {
                var ed = tinyMCE.get('tpronostico');
                $("#pronostico").val(ed.getContent());
                guardar();

            });
        }
    });

    $("#cie10").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('epicrisis.cie10_nombre')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);

                }
            })
        },
        minLength: 2,
    });

    $("#cie10").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cie10_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#cie10"),
            success: function(data) {
                if (data != '0') {
                    $('#codigo').val(data.id);

                }

            },
            error: function(data) {

            }
        })
    });


    $('#bagregar').click(function() {

        if ($('#pre_def').val() != '') {
            if ($('#ing_egr').val() != '') {
                guardar_cie10_PRO();
                $('#pre_def').val('');
                $('#ing_egr').val('');
            } else {
                alert("Seleccione Ingreso o Egreso");
            }
        } else {
            alert("Seleccione Presuntivo o Definitivo");
        }

        $('#codigo').val('');
        $('#cie10').val('');
    });

    function guardar_cie10_PRO() {
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.agregar_cie10')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo").val(),
                'pre_def': $("#pre_def").val(),
                'hcid': {
                    {
                        $epicrisis - > hcid
                    }
                },
                'hc_id_procedimiento': {
                    {
                        $epicrisis - > hc_id_procedimiento
                    }
                },
                'in_eg': $("#ing_egr").val()
            },
            success: function(data) {
                //console.log(data);


                var indexr = data.count - 1
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag' + data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>' + data.cie10 + '</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell3 = row.insertCell(3);
                cell3.innerHTML = data.in_eg;
                var cell4 = row.insertCell(4);
                cell4.innerHTML = '<a href="javascript:eliminar(' + data.id + ');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';




            },
            error: function(data) {

            }
        })
    }

    function cargar_tabla() {
        $.ajax({
            url: "{{route('epicrisis.cargar',['id' => $epicrisis->hc_id_procedimiento])}}",
            dataType: "json",
            type: 'get',
            success: function(data) {

                var table = document.getElementById("tdiagnostico");

                $.each(data, function(index, value) {

                    var row = table.insertRow(index);
                    row.id = 'tdiag' + value.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>' + value.cie10 + '</b>';
                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = value.pre_def;
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = value.descripcion;
                    var cell5 = row.insertCell(3);
                    cell5.innerHTML = value.ingreso_egreso;
                    var cell4 = row.insertCell(4);
                    cell4.innerHTML = '<a href="javascript:eliminar(' + value.id + ');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                });

            }
        })
    }

    function eliminar(id_h) {


        var i = document.getElementById('tdiag' + id_h).rowIndex;

        document.getElementById("tdiagnostico").deleteRow(i);

        $.ajax({
            type: 'get',
            url: "{{url('cie10/eliminar')}}/" + id_h, //epicrisis.eliminar
            datatype: 'json',

            success: function(data) {

            },
            error: function(data) {

            }
        });
    }
</script>

@include('sweet::alert')
@endsection