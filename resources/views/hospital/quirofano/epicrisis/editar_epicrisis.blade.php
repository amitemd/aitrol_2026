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


<div class="card">
    <div class="card-header bg bg-primary colorbasic">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px"><b> {{trans('Epicrisis')}}</b> </label> <br>
                    <label style="font-size: 16px;"><b>Paciente: {{$solicitud->paciente->apellido1}} {{$solicitud->paciente->apellido2}} {{$solicitud->paciente->nombre1}} {{$solicitud->paciente->nombre2}}</b></label>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
        

     
    </div>
    <div class="card-body">
    <div>&nbsp;</div>

    <div class="box-header with-border">
                    <a target="_blank" class="form-group col-md-3 col-sm-3 col-xs-3" href=""><button type="button" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-download-alt"></span> Descargar</button>
                    </a>   
                </div>

        <form id="frm">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="hcid" value="">
            <input type="hidden" name="epicrisis" value="">
            <input type="hidden" name="protocolo_id" value="">
            <input type="hidden" name="hc_id_procedimiento" value="">

            <br>
            <div class="card-header bg bg-primary colorbasic">
                <div class="col-md-7">
                    <label class="colorbasic" for="tcuadro" class="control-label"><b>RESUMEN DEL CUADRO CLÍNICO</b></label>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="favorable_des" class="col-md-12 control-label">EVOLUCIÓN PRE</label>
                        <textarea name="evolucion" id="evolucion" class="form-control input-sm" rows="3"></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="favorable_des" class="col-md-12 control-label">EVOLUCIÓN POST</label>
                        <textarea name="evolucion" id="evolucion" class="form-control input-sm" rows="3"></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="conclusion" class="col-md-12 control-label">CONCLUSION</label>
                        <textarea name="conclusion" id="conclusion" class="form-control input-sm" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            
            <div class="card-header bg bg-primary colorbasic">

                <div class="col-md-7">
                    <label class="colorbasic" for="tcuadro" class="control-label">RESUMEN DE EVOLUCION Y COMPLICACIONES</label>
                </div>

            </div>
            <div>&nbsp;</div>
            <div class="col-md-12">

                <div class="row">

                    <div class=" col-md-6 {{ $errors->has('ep_resumen_evolucion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                        <label for="ep_resumen_evolucion" class="col-md-12 control-label">EVOLUCION</label> 
                            <input type="text" class="form-control input-sm" name="ep_resumen_evolucion" id="ep_resumen_evolucion" value="" onchange="guardar();">
                   
                    </div>
                    <div class=" col-md-6 {{ $errors->has('complicacion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                        <label for="complicacion" class="col-md-12 control-label">COMPLICACION</label>
                            <input type="text" class="form-control input-sm" name="complicacion" id="complicacion" value="" onchange="guardar();">
            
                    </div>

                </div>
                <div>&nbsp;</div>
                <div class="card-header bg bg-primary colorbasic">
                <div class="col-md-12">
                    <label class="colorbasic" for="tcuadro" class="control-label"><b>CONDICIONES DE EGRESO Y PRONOSTICO</b></label>
                </div>
            </div>
        
            <div>&nbsp;</div>
              
                <div class="col-md-12">

                    <div class="row">

                        <div class="form-group col-md-6 {{ $errors->has('condicion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="condicion" class="col-md-12 control-label">CONDICION</label>
                            <input type="text" class="form-control input-sm" name="condicion" id="condicion" value="" onchange="guardar();">
                     
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('pronostico') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="pronostico" class="col-md-12 control-label">PRONOSTICO</label>
                            <input type="text" class="form-control input-sm" name="pronostico" id="pronostico" value="" onchange="guardar();">

                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('alta') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="alta" class="col-md-12 control-label">ALTA</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="alta" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option value="DEFINITIVA">DEFINITIVA</option>
                                    <option value="TRANSITORIA">TRANSITORIA</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('discapacidad') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="discapacidad" class="col-md-12 control-label">DISCAPACIDAD</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="discapacidad" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option value="ASINTOMÁTICA">ASINTOMÁTICA</option>
                                    <option value="LEVE">LEVE</option>
                                    <option value="MODERADA">MODERADA</option>
                                    <option value="GRAVE">GRAVE</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('retiro') ? ' has-error' : '' }}">
                            <label for="retiro" class="col-md-12 control-label">RETIRO</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="retiro" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option>AUTORIZADO</option>
                                    <option value="NO AUTORIZADO">NO AUTORIZADO</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('defuncion') ? ' has-error' : '' }}">
                            <label for="defuncion" class="col-md-12 control-label">DEFUNCIÓN</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="defuncion" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option>MENOS DE 48H</option>
                                    <option>MAS DE 48H</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="dias_estadia" class="col-md-12 control-label" style="padding-left: 0px;">DIAS DE ESTADIA</label>
                            <input id="dias_estadia" type="number" class="form-control input-sm" name="dias_estadia" value="" onchange="guardar();">

                        </div>

                        <div class="form-group col-md-4">
                            <label for="dias_incapacidad" class="col-md-12 control-label">DIAS INCAPACIDAD</label>
                            <input id="dias_incapacidad" type="number" class="form-control input-sm" name="dias_incapacidad" value="" onchange="guardar();">

                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('receta') ? ' has-error' : '' }}">
                            <label for="receta" class="col-md-12 control-label">RECETA</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="receta" required onchange="guardar();">
                                    <option value="">Seleccione ...</option>
                                    <option>SI</option>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
        </form>

        <div class="card-body">
            <form id="frm_cie">
                <input type="hidden" name="codigo" id="codigo">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="col-md-7">
                        <label class="colorbasic" for="tcuadro" class="control-label"><b>DIAGNÓSTICO PRESUNTIVO/DEFINITIVO</b></label>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="col-md-12">

                    <div class="row">
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
                    </div>
                </div>
            </form>
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


        $('#edad').text(edad);

        $('#fecha_imprime').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
        }).on('dp.change', function(e) {
            guardar()
        });




    });
   


    /*function guardar(){

        //alert("ok");
        $.ajax({
          type: 'post',
          url:"{{route('epicrisis.actualiza')}}", 
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
            //alert(data);
            
          },
          error: function(data){

           
             
          }
        });
    }*/



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