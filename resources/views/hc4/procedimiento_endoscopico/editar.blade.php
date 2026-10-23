<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
</style>

@php
    $var_tiempo_endos  = rand(0,99999);
@endphp
<input type="hidden" name="contador{{$var_tiempo_endos}}" id="contador{{$var_tiempo_endos}}" value="0">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
<div class="modal fade" id="cpre_eco{{$var_tiempo_endos}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
<div class="row">
    @php
        $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $procedimiento->historia->id_agenda)->first()->fechaini));
    @endphp
    <input type="hidden" id="tipo_guardado{{$var_tiempo_endos}}" value="3">
    <form id="hc_protocolo{{$procedimiento->id}}" style="width: 100%;">
        <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
         <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
        <input type="hidden" name="id_procedimiento" value="{{$procedimiento->id}}">
        <input type="hidden" name="id_protocolo" value="<?php if (!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif;?>">
        <input type="hidden" name="tipo" value="{{$tipo}}">
        <div class="col-12">&nbsp;</div>
        <div class="row">
            <div class="col-6">

                <div class="col-12">
                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                </div>
                <?php
$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
?>
                @if(!is_null($adicionales->last()))
                    @php
                        $mas = true;
                        $texto = "";
                        foreach($adicionales as $value2)
                        {
                            if($mas == true){
                             $texto = $texto.$value2->procedimiento->nombre  ;
                             $mas = false;
                             }
                            else{
                             $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                             }
                        }
                    @endphp
                    <div class="col-12">
                        <span>{{$texto}}
                        </span>
                    </div>
                @else
                    <div class="col-12">
                        @php  $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                        @endphp
                        <span>@if(!is_null($procedimiento_completo)) {{$procedimiento_completo->nombre_general}} @endif</span>
                    </div>
                @endif
                <div class="col-12">

                    <select id="id_procedimiento{{$var_tiempo_endos}}" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 95.5%; padding-left: 15px " autocomplete="off" onchange="validar_proc();">
                        @php
                            $aceptacion = 0;


                        @endphp
                          @foreach($px as $key => $value)
                              @php
                                  $validacion = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->where('id_hc_procedimientos', $procedimiento->id)->first();
                                  if($key == 0){
                                    $cantidad = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->count();
                                    if($cantidad >= 1){
                                        $valida_d = 1;
                                    }else{
                                        $valida_d = 0;
                                    }
                                  }


                                  $clase = 'c';
                                  if(!is_null($value->grupo_procedimiento)){
                                    $clase = $clase.$value->grupo_procedimiento->tipo_procedimiento;
                                  }
                                  if(!is_null($validacion)){
                                    $aceptacion++;
                                  }
                              @endphp
                              <option data-clase="{{$clase}}" class="{{$clase}}" @if(!is_null($validacion)) selected @else @if($valida_d == 1 && strlen($clase) > 1 ) disabled="disabled"  @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                          @endforeach
                    </select>
                    <br>
                    @if($fecha_r == date('Y-m-d') )
                    <button type="button" class="btn-primary btn" onclick="autoguardado({{$procedimiento->id}}, 1)"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp; Guardar</button>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="col-12" >
                    <!--<center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_proc_endos_plantilla', ['id' => $procedimiento->id.date('his') ])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar Plantilla
                            </a>
                        </div>
                    </center>-->
                    <center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_cpre.eco_modal', ['hcid' => $protocolo->hcid])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar CPRE+ECO
                            </a>
                        </div>
                    </center>
                </div>
            </div>
        </div>
        <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
          Seleccione un Procedimiento Endoscopico
        </div>
        <br>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Plantilla de Procedimientos</span>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7" style="width: 100%">
                    <select class="form-control select2_plantilla"  name="proc_com" style="width: 100%;">
                            <option value="">Todos</option>
                        @foreach($proc_completo as $value)
                            <option @if($value->id == $procedimiento_completo_plantilla) selected @endif value="{{$value->id}}">{{$value->nombre_general}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5" >
                    <a class="btn btn-info btn_ordenes" style="color: white; height: 100%; " onClick="cargar_plantilla('1',{{$procedimiento->id}}{{$var_tiempo_endos}}, {{$procedimiento->id}});">
                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                            <label style="font-size: 16px">Insertar Plantilla</label>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <br>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">Seguro</span>
            </div>
        <!-- DOCTOR TRAINING -->
        <!--VH. PREGUNTAR POR EL ESTADO ADMISIONADO -->
            @php
                $dr_training = Auth::user();
                $proc_training = Sis_medico\Hc_protocolo_training::where('id_hc_protocolo',$protocolo->id)->where('id_training',$dr_training->id)->first();
            @endphp
            @if($dr_training->training)
                <div class="col-6" >
                    <span style="font-family: 'Helvetica general';">Training </span>
                </div>
            @endif
        <!-- DOCTOR TRAINING -->
        </div>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">{{$hc_seguro->nombre}}</span>
            </div>
            <!-- DOCTOR TRAINING -->
            @if($dr_training->training)
            <div class="col-6">
                <input id="ch{{$protocolo->id}}" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> Participe de este Procedimiento</span>
            </div>
            @endif
            <!-- DOCTOR TRAINING -->
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Hallazgos</span>
        </div>
        <div class="col-12">
            <div id="thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif;?></div>
            <input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>
        <div class="col-12">&nbsp;</div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Conclusiones</span>
        </div>
        <div class="col-12">
            <div id="tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif;?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">&nbsp;</span>
        </div>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
        </div>
        <div class="col-4">
            <select name="id_doctor_examinador" onchange="autoguardado({{$procedimiento->id}}, 1)">
                @foreach($doctores as $value)
                    @if($value->training != 1)
                    <option
                    @if(!is_null($procedimiento->id_doctor_examinador))
                        @if($procedimiento->id_doctor_examinador == $value->id)
                            selected
                        @endif
                    @endif
                    value="{{$value->id}}"> {{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}
                    </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">&nbsp;</span>
        </div>
        <div id="escala_boston<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" class="col-12" style="display: none;">
            <div class="col-12">
                <span style="font-family: 'Helvetica general';">Escala Boston </span>
            </div>
            <div class="col-12" style="padding-left: 25px; "  >
                <table width="100%">
                    <tr style="padding: 10px">
                        <td>Colon Derecho:</td>
                        <td>
                            <select name="colon_derecho" id="colon_derecho<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" onchange="calcular_puntaje({{$procedimiento->id}}, {{$var_tiempo_endos}})">
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_derecho == 0) selected @endif value="0" >0 = Inadecuada</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_derecho == 1) selected @endif value="1" >1 = Mala</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_derecho == 2) selected @endif value="2" >2 = Buena</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_derecho == 3) selected @endif value="3" >3 = Excelente</option>
                            </select>
                        </td>
                        <td style="text-align: center;"><span style="font-family: 'Helvetica general';">Puntaje Final:</span></td>
                    </tr>
                    <tr style="padding: 10px">
                        <td>Colon Transverso:</td>
                        <td>
                            <select name="colon_transverso" id="colon_transverso<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" onchange="calcular_puntaje({{$procedimiento->id}}, {{$var_tiempo_endos}})">
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_transverso == 0) selected @endif value="0" >0 = Inadecuada</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_transverso == 1) selected @endif value="1" >1 = Mala</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_transverso == 2) selected @endif value="2" >2 = Buena</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_transverso == 3) selected @endif value="3" >3 = Excelente</option>
                            </select>
                        </td>
                        <td rowspan="2" style="text-align: center;"><input type="text" name="puntaje_final" id="puntaje_final<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="width: 30px; font-weight: bold; font-size: 18px" @if(!is_null($escala_boston)) value = '{{$escala_boston->puntaje_final}}' @endif placeholder="0" readonly></td>
                    </tr>
                    <tr style="padding: 10px">
                        <td>Colon Izquierdo:</td>
                        <td>
                            <select name="colon_izquierdo" id="colon_izquierdo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" onchange="calcular_puntaje({{$procedimiento->id}}, {{$var_tiempo_endos}})">
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_izquierdo == 0) selected @endif value="0" >0 = Inadecuada</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_izquierdo == 1) selected @endif value="1" >1 = Mala</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_izquierdo == 2) selected @endif value="2" >2 = Buena</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->colon_izquierdo == 3) selected @endif value="3" >3 = Excelente</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <span style="font-family: 'Helvetica general';">&nbsp;</span>
            </div>
            <div class="col-12">
                <span style="font-family: 'Helvetica general';">Polipos</span>
            </div>
            <div class="col-12" style="padding-left: 25px">
                <table style="width: 100%">
                    <tr style="padding: 10px">
                        <td>Polipo Adenomatosos</td>
                        <td>
                            <select name="resp_polipo_adenomatosos" id="resp_polipo_adenomatosos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" onchange="polipo('ubicacion_polipo_adenomatosos{{$procedimiento->id}}{{$var_tiempo_endos}}', '1', 'tamano_adenomatosos{{$procedimiento->id}}{{$var_tiempo_endos}}', '{{$procedimiento->id}}')">
                                <option @if(!is_null($escala_boston) && $escala_boston->polipo_adenomatosos == 0) selected @endif value="0" >NO</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->polipo_adenomatosos == 1) selected @endif value="1" >SI</option>
                            </select>
                        </td>
                        <td>
                            <select name="ubicacion_polipo_adenomatosos" id="ubicacion_polipo_adenomatosos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
                                <option value="0" >Seleccione...</option>
                                @foreach($polipos_ubicacion as $polipo_ub)
                                <option @if(!is_null($escala_boston) && $escala_boston->ubicacion_polipo_adenomatosos == $polipo_ub->id) selected @endif value="{{$polipo_ub->id}}" >{{$polipo_ub->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>Tama&ntilde;o: <input  type="number" name="tamano_adenomatosos" id="tamano_adenomatosos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" placeholder="0 mm" @if(!is_null($escala_boston)) value ='{{$escala_boston->tamano_polipo_adenomatosos}}' @endif style="width: 60px;">mm</td>
                    </tr>
                    <tr style="padding: 10px">
                        <td>Polipo Hiperplasico</td>
                        <td>
                            <select name="resp_polipo_hiperplasico" id="resp_polipo_hiperplasico<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" onchange="polipo('ubicacion_polipo_hiperplasico{{$procedimiento->id}}{{$var_tiempo_endos}}', '2', 'tamano_hiperplasico{{$procedimiento->id}}{{$var_tiempo_endos}}', '{{$procedimiento->id}}')">
                                <option @if(!is_null($escala_boston) && $escala_boston->polipo_hiperplasico == 0) selected @endif value="0" >NO</option>
                                <option @if(!is_null($escala_boston) && $escala_boston->polipo_hiperplasico == 1) selected @endif value="1" >SI</option>
                            </select>
                        </td>
                        <td>
                            <select name="ubicacion_polipo_hiperplasico" id="ubicacion_polipo_hiperplasico<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
                                <option value="0">Seleccione...</option>
                                @foreach($polipos_ubicacion as $polipo_ub)
                                <option @if(!is_null($escala_boston) && $escala_boston->ubicacion_polipo_hiperplasico == $polipo_ub->id) selected @endif value="{{$polipo_ub->id}}" >{{$polipo_ub->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>Tama&ntilde;o: <input type="number" name="tamano_hiperplasico" id="tamano_hiperplasico<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" placeholder="0 mm" @if(!is_null($escala_boston)) value ='{{$escala_boston->tamano_polipo_hiperplasico}}' @endif style="width: 60px;">mm </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <span style="font-family: 'Helvetica general';">&nbsp;</span>
            </div>
            <div class="col-12">
                <span style="font-family: 'Helvetica general';">Intubacion: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NO <input id="no<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" value="0" type="radio" checked  name="intubacion" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SI <input id="si<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" value="1" type="radio" name="intubacion" @if(!is_null($escala_boston) && $escala_boston->intubacion == 1) checked @endif> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
            </div>
        </div>
        <div class="col-12" >
        <center>
        <div class="col-5" style="padding-top: 15px;text-align: center;">
            <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento({{$procedimiento->id}})" ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
        </center>
        </div>
    </form>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">


    $(document).ready(function(){

        validar_proc();
      $('.select2_plantilla').select2({
        tags: false
      });
      
      $(".breadcrumb").append('<li class="active">Procedimientos</li>');
      calcular_puntaje(<?php echo e($procedimiento->id); ?>, {{$var_tiempo_endos}});
    });

    function calcular_puntaje(id_proc, tiempo){
        total = 0;
        colon_derecho = parseInt($('#colon_derecho'+id_proc+tiempo).val());
        colon_transverso = parseInt($('#colon_transverso'+id_proc+tiempo).val());
        colon_izquierdo = parseInt($('#colon_izquierdo'+id_proc+tiempo).val());
        total = colon_derecho + colon_transverso + colon_izquierdo;
        $('#puntaje_final'+id_proc+tiempo).val(total);
    }

    function polipo(ubicacion, tipo, tamano, id_proc){
        polipo1 = null;
        if(tipo == '1'){
            polipo1 = document.querySelector("#resp_polipo_adenomatosos"+id_proc+{{$var_tiempo_endos}});
        }else if(tipo == '2'){
            polipo1 = document.querySelector("#resp_polipo_hiperplasico"+id_proc+{{$var_tiempo_endos}});
        }
        polipo1.addEventListener("change", () => {
            if (polipo1.value == "0") {
                document.getElementById(ubicacion).value = "0"; 
                document.getElementById(tamano).value = ""; 
            }
        });
    }
    function validar_proc(){
        colono = document.querySelector("#id_procedimiento"+{{$var_tiempo_endos}}).value;
        if(colono == 9){
            div = document.getElementById('escala_boston'+<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}});
            div.style.display = '';
        }else{
            div = document.getElementById('escala_boston'+<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}});
            div.style.display = 'none';
        }
    }


     tinymce.init({
        selector: '#thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());


            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            });

            @if($fecha_r == date('Y-m-d') )
                editor.on('keyup', function (e) {
                    contador = parseInt($('#contador{{$var_tiempo_endos}}').val());
                    if(contador <= 19){
                        contador++
                        $('#contador{{$var_tiempo_endos}}').val(contador);
                    }else{
                        var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                        $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                        autoguardado({{$procedimiento->id}}), 1;
                        $('#contador{{$var_tiempo_endos}}').val(0);
                    }
                });
            @endif
          }
    });

    tinymce.init({
        selector: '#tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());

            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            });
            @if($fecha_r == date('Y-m-d') )
            editor.on('keyup', function (e) {
                contador = parseInt($('#contador{{$var_tiempo_endos}}').val());
                if(contador <= 19){
                    contador++
                    $('#contador{{$var_tiempo_endos}}').val(contador);
                }else{
                    var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                    $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                    autoguardado({{$procedimiento->id}}, 1);
                    $('#contador{{$var_tiempo_endos}}').val(0);
                }
            });
            @endif
          }
    });
    function guardar_procedimiento(id){
        var jprocedimientos = $('#id_procedimiento{{$var_tiempo_endos}}').val();
        //console.log (jprocedimientos);

        if (jprocedimientos != ""){
            var entra = id;
                $.ajax({
                type: "POST",
                url: "<?php echo e(route('guardar.procedimiento_endoscopico')); ?>",
                data: $("#hc_protocolo"+id).serialize(),
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                success: function(datahtml, entra){

                    if(datahtml == 'No puede Guardar sin Procedimiento'){
                        alert(datahtml);

                    }else if(datahtml == 'Seleccione un procedimiento principal.'){
                        alert(datahtml);

                    }else{
                        $("#carga_insumo"+id).html("");
                        $("#procedimiento"+id).html(datahtml);
                    }
                },
                error:  function(){
                    alert('error al cargar');
                }
                });
        }else {
            $("#select_proc").fadeIn(1000);
            $("#select_proc").fadeOut(3000);
            //alert("Seleccione un Procedimiento");
        }
    };

</script>
<script type="text/javascript">
    setInterval(carga_insumos({{$procedimiento->id}}), 30000);
    @php $dr_training = Auth::user(); @endphp

    @if($dr_training->training)

        $('#ch{{$protocolo->id}}').on('ifChecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '1']) }}",
              datatype: 'json',
              success: function(data_cpre){
                $('#train').removeClass('text-muted');
                $('#train').addClass('text-red');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

        $('#ch{{$protocolo->id}}').on('ifUnchecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '0']) }}",


              datatype: 'json',

              success: function(data_cpre){
                $('#train').removeClass('text-red');
                $('#train').addClass('text-muted');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

    @endif

    $('.c{{$tipo}}').removeAttr('disabled');

    $('#id_procedimiento{{$var_tiempo_endos}}').select2({
        tags: false,
    });


    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    });

    function actualiza_select{{$var_tiempo_endos}}(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
        var longitud =  seleccionados.length;

        console.log(seleccionados);


      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');
      }

      seleccionados.each(function( index ) {
          $(this).removeAttr('disabled');
      });

          $('#id_procedimiento{{$var_tiempo_endos}}').select2({
                tags: false,
            });
    }

    function quita_select{{$var_tiempo_endos}}(){
        var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
        var longitud =  seleccionados.length;
      if(longitud>1){
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');
      }else{
        //alert("salio");
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

        $('#id_procedimiento{{$var_tiempo_endos}}').select2({
            tags: false,
        });


    }

    function cargar_plantilla(actualiza, id, id_proc){
        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica_plantilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_protocolo"+id_proc).serialize(),
          success: function(data){
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                alert("No Existe Plantilla Precargada !!");
                var tecnica = "";
            }

            if(actualiza=='1'){
                anterior1=  $('#hallazgos'+id).val();
                anterior2=  tinyMCE.get('thallazgos'+id).getContent();
                $('#hallazgos'+id).val(anterior1+tecnica);
                tinyMCE.get('thallazgos'+id).setContent(anterior2+tecnica);
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            }
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    $("#id_procedimiento{{$var_tiempo_endos}}").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

        actualiza_select{{$var_tiempo_endos}}();
    });

    $("#id_procedimiento{{$var_tiempo_endos}}").on("select2:unselecting", function (evt) {
        quita_select{{$var_tiempo_endos}}();
    });


    @if($aceptacion > 0 )
        $('#id_procedimiento{{$var_tiempo_endos}}').select2({
                tags: false,
            });
    @endif

    @if($fecha_r == date('Y-m-d') )

        function autoguardado(id, tipo){
            var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
            var longitud =  parseInt(seleccionados.length);
            var tipo_2 = parseInt($('#tipo_guardado{{$var_tiempo_endos}}').val());
            if(tipo == 1){
                if(longitud>0){
                    var entra = id;
                        $.ajax({
                        type: "POST",
                        url: "{{route('guardar.procedimiento_endoscopico_autoguardado')}}",
                        data: $("#hc_protocolo"+id).serialize(),
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: "html",
                        success: function(datahtml){
                            console.log(datahtml);
                            if(datahtml == "ok"){
                                $("#alerta_datos").fadeIn(1000);
                                $("#alerta_datos").fadeOut(3000);
                            }else{
                                alert(datahtml);
                            }

                        },
                        error:  function(){
                            alert('error al cargar');
                        }
                        });
                }else {
                    //$("#alerta_datos").fadeIn(1000);
                    //$("#alerta_datos").fadeOut(3000);
                    //console.log('parsea');
                    alert("Debe Seleccionar un procedimiento Principal.");
                }

            }else{
                if(tipo != parseInt(tipo_2)){
                    if(longitud>0){
                        var entra = id;
                            $.ajax({
                            type: "POST",
                            url: "{{route('guardar.procedimiento_endoscopico_autoguardado')}}",
                            data: $("#hc_protocolo"+id).serialize(),
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: "html",
                            success: function(datahtml){
                                console.log(datahtml);
                                if(datahtml == "ok"){
                                    $("#alerta_datos").fadeIn(1000);
                                    $("#alerta_datos").fadeOut(3000);
                                }else{
                                    alert(datahtml);
                                }

                            },
                            error:  function(){
                                alert('error al cargar');
                            }
                            });
                    }else {
                        //$("#alerta_datos").fadeIn(1000);
                        //$("#alerta_datos").fadeOut(3000);
                        alert("Debe Seleccionar un procedimiento Principal");
                    }
                }
            }
            $('#tipo_guardado{{$var_tiempo_endos}}').val(tipo);

        }

    @else
        function autoguardado(id){

        }
    @endif
</script>
