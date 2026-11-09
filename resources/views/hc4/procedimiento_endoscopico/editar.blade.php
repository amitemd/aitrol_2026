<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }

    .select2-container--open .select2-results__options {
        max-height: 360px !important;
        overflow-y: auto !important;
    }

    .select2-container--open .select2-results__option {
        box-sizing: border-box;
        min-height: 36px;
    }

    #area_trabajo .hc4-endoscopy-edit-form .select2-container {
        width: 100% !important;
    }

    #area_trabajo .hc4-endoscopy-edit-form .hc4-safety-check-label {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        margin: 0;
        cursor: pointer;
    }

    #area_trabajo .hc4-endoscopy-edit-form input[type="checkbox"].hc4-safety-checkbox {
        display: inline-block !important;
        flex: 0 0 18px;
        width: 18px !important;
        min-width: 18px !important;
        height: 18px !important;
        min-height: 18px !important;
        margin: 0 !important;
        padding: 0 !important;
        appearance: auto !important;
        -webkit-appearance: checkbox !important;
        accent-color: #087eae;
    }

    #area_trabajo .hc4-procedure-link-editor {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    #area_trabajo .hc4-procedure-link-editor__field {
        flex: 1 1 auto;
        position: relative;
        min-width: 0;
    }

    #area_trabajo .hc4-procedure-link-editor .form-control {
        width: 100%;
        height: 38px;
        border: 1px solid #cbdde6;
        border-radius: 7px;
        color: #087fa7;
        box-shadow: none;
        text-decoration: underline;
    }

    #area_trabajo .hc4-procedure-link-editor__action {
        display: inline-flex;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border: 1px solid #0b8eaf;
        border-radius: 5px;
        color: #16778e;
        background: #fff;
        font-size: 14px;
    }

    #area_trabajo .hc4-procedure-editor-shell {
        width: 100%;
        padding: 20px;
        background: #f6fbfd;
        color: #334155;
    }

    #area_trabajo .hc4-procedure-editor-shell > .col-12,
    #area_trabajo .hc4-procedure-editor-shell > .col-md-12 {
        float: none;
        width: 100%;
        padding-right: 0;
        padding-left: 0;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-card {
        height: auto;
        padding: 14px;
        border: 1px solid #d5e6ed;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(10, 91, 128, .08);
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-top {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 0 0 12px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-top > [class*="col-"] {
        padding: 0;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-procedure {
        flex: 1 1 58%;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-quick-action {
        display: flex;
        flex: 1 1 32%;
        align-items: flex-start;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-quick-action .col-12 {
        width: 100%;
        padding: 0;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-label {
        display: block;
        margin: 0 0 6px;
        color: #185b76;
        font-family: 'Helvetica general';
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-current-procedure {
        min-height: 22px;
        margin-bottom: 10px;
        color: #334155;
        font-size: 14px;
        line-height: 1.45;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-inline-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    #area_trabajo .hc4-procedure-editor-shell .select2_proc + .select2-container,
    #area_trabajo .hc4-procedure-editor-shell .select2-selection--multiple {
        width: 100% !important;
        min-height: 42px;
        border-color: #cbdde6;
        border-radius: 8px;
    }

    #area_trabajo .hc4-procedure-editor-shell .select2-selection--multiple .select2-selection__rendered {
        max-height: 94px;
        padding: 4px 7px;
        overflow-y: auto;
    }

    #area_trabajo .hc4-procedure-editor-shell .select2-selection--multiple .select2-selection__choice {
        max-width: calc(100% - 8px);
        margin: 3px 5px 3px 0;
        padding: 6px 9px;
        overflow: hidden;
        border: 1px solid #9ed7df !important;
        border-radius: 6px;
        background: #e6f7f8 !important;
        color: #175e73 !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-editor-shell .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 6px;
        color: #b63849 !important;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-inline-actions .btn,
    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-quick-action .btn {
        width: auto !important;
        min-height: 38px;
        margin: 0 !important;
        padding: 9px 17px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(110deg, #06658f, #12a8c1) !important;
        box-shadow: 0 5px 12px rgba(5, 110, 151, .18);
        color: #fff !important;
        font-size: 13px !important;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-quick-action .btn {
        width: 100% !important;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-template-card {
        margin-bottom: 14px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-template-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 280px);
        gap: 10px;
        align-items: stretch;
        margin: 0;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-template-row > [class*="col-"] {
        width: auto !important;
        padding: 0;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-template-row .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 38px;
        margin: 0;
        border-radius: 8px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-meta {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 14px;
        padding: 12px 16px;
        border: 1px solid #d9e8ee;
        border-radius: 10px;
        background: #edf7fa;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-meta strong {
        display: block;
        margin-top: 3px;
        color: #243746;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-clinical-field {
        margin: 3px 0 6px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-clinical-field + .col-12 {
        margin-bottom: 12px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-clinical-editor {
        min-height: 84px;
        padding: 10px 12px;
        border: 1px solid #cbdde6 !important;
        border-radius: 8px;
        background: #fff;
        line-height: 1.45;
    }

    #area_trabajo .hc4-procedure-editor-shell .note-editor {
        overflow: hidden;
        border: 1px solid #cbdde6 !important;
        border-radius: 8px;
        box-shadow: none;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-doctor {
        margin-top: 14px;
        padding: 16px;
        border: 1px solid #d9e8ee;
        border-radius: 10px;
        background: #fff;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-footer {
        display: flex;
        justify-content: flex-end;
        padding-top: 14px;
    }

    #area_trabajo .hc4-procedure-editor-shell .hc4-editor-footer .btn {
        width: auto !important;
        min-width: 170px;
        margin: 0 !important;
        padding: 10px 22px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(110deg, #06658f, #12a8c1);
        box-shadow: 0 5px 12px rgba(5, 110, 151, .18);
    }

    @media (max-width: 900px) {
        #area_trabajo .hc4-procedure-editor-shell { padding: 12px; }
        #area_trabajo .hc4-procedure-editor-shell .hc4-editor-top { display: block; }
        #area_trabajo .hc4-procedure-editor-shell .hc4-editor-top > div + div { margin-top: 12px; }
        #area_trabajo .hc4-procedure-editor-shell .hc4-editor-template-row { grid-template-columns: 1fr; }
        #area_trabajo .hc4-procedure-editor-shell .hc4-editor-meta { flex-direction: column; }
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
<div class="row hc4-endoscopy-edit-form">
    @php
        if(!is_null($procedimiento->created_at)){
            $fecha_r =  Date('Y-m-d',strtotime($procedimiento->created_at));
        }else{
            $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\agenda::where('id', $procedimiento->historia->id_agenda)->first()->fechaini));
        }
    @endphp
    <input type="hidden" id="tipo_guardado{{$var_tiempo_endos}}" value="3">
    <form id="hc_protocolo{{$procedimiento->id}}" class="hc4-procedure-editor-shell">
        <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
         <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
        <input type="hidden" name="id_procedimiento" value="{{$procedimiento->id}}">
        <input type="hidden" name="id_protocolo" value="<?php if (!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif;?>">
        <input type="hidden" name="tipo" value="{{$tipo}}">
        <div class="row hc4-editor-top">
            <div class="col-6 hc4-editor-procedure">

                <div class="hc4-editor-card">

                <div class="col-12">
                    <span class="hc4-editor-label">Procedimiento</span>
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
                    <div class="col-12 hc4-editor-current-procedure">
                        <span>{{$texto}}
                        </span>
                    </div>
                @else
                    <div class="col-12 hc4-editor-current-procedure">
                        @php  $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                        @endphp
                        <span>@if(!is_null($procedimiento_completo)) {{$procedimiento_completo->nombre_general}} @endif</span>
                    </div>
                @endif
                <div class="col-12">

                    <select id="id_procedimiento{{$var_tiempo_endos}}" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Buscar procedimientos" style="width: 95.5%; padding-left: 15px " autocomplete="off" >
                        @php
                            $aceptacion = 0
                        @endphp
                          @foreach($px as $value)
                              @php
                                  $validacion = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->where('id_hc_procedimientos', $procedimiento->id)->first();

                                  $clase = 'c';
                                  if(!is_null($value->grupo_procedimiento)){
                                    $clase = $clase.$value->grupo_procedimiento->tipo_procedimiento;
                                  }
                                  if(!is_null($validacion)){
                                    $aceptacion++;
                                  }
                              @endphp
                              <option data-clase="{{$clase}}"  @if(is_null($validacion)) disabled="disabled" @endif class="{{$clase}}" @if(!is_null($validacion)) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                          @endforeach
                    </select>
                    <div class="hc4-editor-inline-actions">
                    @if($fecha_r == date('Y-m-d') )
                    <button type="button" class="btn-primary btn" onclick="autoguardado({{$procedimiento->id}}, 1)"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp; Guardar</button>
                    @endif
                    </div>
                </div>
                </div>
            </div>
            <div class="col-6 hc4-editor-quick-action">
                <div class="hc4-editor-card" style="width:100%;">
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
        </div>
        <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
          Seleccione un Procedimiento Endoscopico
        </div>
        <br>
        <div class="col-md-12 hc4-editor-card hc4-editor-template-card">
            <span class="hc4-editor-label">Plantilla de procedimientos</span>
            <div class="row hc4-editor-template-row">
                <div class="col-md-7" style="width: 100%">
                    <select class="form-control select2_plantilla hc4-endoscopy-single-select" name="proc_com" data-placeholder="Buscar plantilla" style="width: 100%;">
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

        <div class="hc4-editor-meta">
            <div>
                <span class="hc4-editor-label">Seguro</span>
                <strong>{{$hc_seguro->nombre}}</strong>
            </div>
        <!-- DOCTOR TRAINING -->
        <!--VH. PREGUNTAR POR EL ESTADO ADMISIONADO -->
            @php
                $dr_training = Auth::user();
                $proc_training = Sis_medico\Hc_protocolo_training::where('id_hc_protocolo',$protocolo->id)->where('id_training',$dr_training->id)->first();
            @endphp
            @if($dr_training->training)
                <div>
                    <span class="hc4-editor-label">Training</span>
                    <div>
                        <input id="ch{{$protocolo->id}}" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> Participe de este Procedimiento</span>
                    </div>
                </div>
            @endif
        <!-- DOCTOR TRAINING -->
        </div>

        <div class="col-12 hc4-clinical-field">
            <span class="hc4-editor-label">Motivo</span>
        </div>
        <div class="col-12">
            <div class="hc4-clinical-editor" id="mprotocolo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->motivo ?><?php endif;?></div>
            <input type="hidden" name="mprotocolo" id="mprotocolos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>

        <div class="col-12 hc4-clinical-field">
            <span class="hc4-editor-label">Hallazgos</span>
        </div>
        <div class="col-12">
            <div class="hc4-clinical-editor" id="thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif;?></div>
            <input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>
        <div class="col-12 hc4-clinical-field">
            <span class="hc4-editor-label">Conclusiones</span>
        </div>
        <div class="col-12">
            <div class="hc4-clinical-editor" id="tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif;?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>

        @include('hc4.partials.procedure_image_links_editor', [
            'uniqueId' => $var_tiempo_endos,
            'onChange' => $fecha_r == date('Y-m-d') ? 'autoguardado('.$procedimiento->id.', 1)' : '',
        ])

        <div class="col-12 hc4-editor-doctor">
            <span class="hc4-editor-label">M&eacute;dico examinador</span>
            <select class="form-control hc4-endoscopy-single-select" name="id_doctor_examinador" data-placeholder="Buscar médico" onchange="autoguardado({{$procedimiento->id}}, 1)">
                @foreach($doctores as $value)
                    <option
                    @if(!is_null($procedimiento->id_doctor_examinador))
                        @if($procedimiento->id_doctor_examinador == $value->id)
                            selected
                        @endif
                    @endif
                    value="{{$value->id}}"> {{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}
                    </option>
                @endforeach
            </select>
        </div>

        @include('hc4.partials.procedure_safety')

        <div id="procedure_save_error{{$procedimiento->id}}" class="col-12 alert alert-danger" style="display:none;margin-top:10px;"></div>

        <div class="col-12 hc4-editor-footer">
            <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento({{$procedimiento->id}})" ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
    </form>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">

    var hc4EndoscopySelect2Options = {
        tags: false,
        width: '100%',
        minimumResultsForSearch: 0,
        language: {
            noResults: function(){ return 'Sin resultados'; },
            searching: function(){ return 'Buscando...'; }
        }
    };

    function inicializarSelect2Endoscopico(){
      if(!$.fn.select2){
        return;
      }

      $('.hc4-endoscopy-single-select').each(function(){
        var select = $(this);
        if(!select.hasClass('select2-hidden-accessible')){
          select.select2(hc4EndoscopySelect2Options);
        }
      });
    }

    inicializarSelect2Endoscopico();

    $(document).ready(function(){




      $(".breadcrumb").append('<li class="active">Procedimientos</li>');

    });




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
    tinymce.init({
        selector: '#mprotocolo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('mprotocolo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#mprotocolos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());

            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('mprotocolo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#mprotocolos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
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
                    var ed = tinyMCE.get('mprotocolo<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                    $("#mprotocolos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                    autoguardado({{$procedimiento->id}}, 1);
                    $('#contador{{$var_tiempo_endos}}').val(0);
                }
            });
            @endif
          }
    });
    function mostrar_error_guardado_procedimiento(id, xhr){
        var errorBox = $('#procedure_save_error' + id);
        var mensaje = $.trim(xhr.responseText || 'No se pudo guardar el procedimiento.');
        errorBox.text(mensaje).show();
        if (errorBox.length && errorBox[0].scrollIntoView) {
            errorBox[0].scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    }

    function abrir_link_procedimiento(id){
        var input = document.getElementById(id);
        var url = input ? $.trim(input.value) : '';
        if(!url){ return; }
        if(!/^https?:\/\//i.test(url)){
            alert('Ingrese un enlace válido que comience con http:// o https://');
            return;
        }

        var nuevaPestana = window.open(url, '_blank');
        if(nuevaPestana){
            nuevaPestana.opener = null;
        }
    }

    function pegar_link_procedimiento(id){
        var input = document.getElementById(id);
        if(!input){ return; }
        if(navigator.clipboard && navigator.clipboard.readText){
            navigator.clipboard.readText().then(function(texto){
                input.value = $.trim(texto);
                $(input).trigger('input').trigger('change').trigger('focus');
            }).catch(function(){
                var texto = window.prompt('Pegue el link de imágenes:');
                if(texto !== null){ input.value = $.trim(texto); $(input).trigger('input').trigger('change').trigger('focus'); }
            });
            return;
        }
        var texto = window.prompt('Pegue el link de imágenes:');
        if(texto !== null){ input.value = $.trim(texto); $(input).trigger('input').trigger('change').trigger('focus'); }
    }

    function copiar_link_procedimiento(id){
        var input = document.getElementById(id);
        if(!input || !$.trim(input.value)){ return; }
        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText($.trim(input.value)).catch(function(){ input.select(); document.execCommand('copy'); });
        }else{
            input.select();
            document.execCommand('copy');
        }
    }

    function copiar_link_visible_procedimiento(control){
        var url = $.trim($(control).closest('.hc4-procedure-link-controls').find('.hc4-procedure-link-view').attr('href') || '');
        if(!url){ return; }

        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(url).catch(function(){
                var temporal = $('<textarea readonly></textarea>').val(url).appendTo('body');
                temporal[0].select();
                document.execCommand('copy');
                temporal.remove();
            });
        }else{
            var temporal = $('<textarea readonly></textarea>').val(url).appendTo('body');
            temporal[0].select();
            document.execCommand('copy');
            temporal.remove();
        }
    }

    function sincronizar_link_imagenes_procedimiento(){
        var urls = [];
        $('#hc_protocolo{{$procedimiento->id}}').find('input[name="link_imagenes[]"]').each(function(){
            var url = $.trim($(this).val());
            if(url && urls.indexOf(url) === -1){ urls.push(url); }
        });
        var controles = $('#hc4_procedure_media_{{$protocolo->id}}, #hc4_images_procedure_media_{{$protocolo->id}}').find('.hc4-procedure-link-controls');
        if(!controles.length){ return; }

        controles.each(function(){
            var contenedor = $(this).empty();
            if(!urls.length){
                contenedor.append($('<span class="hc4-procedure-link-empty"></span>').text('No hay links de imágenes registrados.'));
                return;
            }
            $.each(urls, function(indice, url){
                var fila = $('<div class="hc4-procedure-link-row"></div>');
                fila.append(
                    $('<a></a>', {
                        class: 'hc4-procedure-link-view',
                        href: url,
                        target: '_blank',
                        rel: 'noopener noreferrer',
                        title: 'Abrir enlace de imágenes en una nueva pestaña',
                        text: url
                    }),
                    $('<button type="button" class="hc4-procedure-link-action" title="Copiar enlace" aria-label="Copiar enlace"><i class="fa fa-copy" aria-hidden="true"></i></button>').on('click', function(){
                        copiar_link_visible_procedimiento(this);
                    })
                );
                contenedor.append(fila);
            });
        });
    }

    function guardar_procedimiento(id){
        var jprocedimientos = $('#id_procedimiento{{$var_tiempo_endos}}').val();
        var errorBox = $('#procedure_save_error' + id);
        errorBox.hide().text('');
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
                    sincronizar_link_imagenes_procedimiento();
                    $("#procedimiento"+id).html(datahtml);
                    //console.log(datahtml);
                },
                error:  function(xhr){
                    mostrar_error_guardado_procedimiento(id, xhr);
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

    @php $dr_training = Auth::user(); @endphp

    @if($dr_training->training)

        $('#ch{{$protocolo->id}}').on('ifChecked', function(event){

            $.ajax({
              type: 'post',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '1']) }}",
              data: {'_token': '{{ csrf_token() }}'},
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
              type: 'post',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '0']) }}",
              data: {'_token': '{{ csrf_token() }}'},


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

    $('#id_procedimiento{{$var_tiempo_endos}}').select2(hc4EndoscopySelect2Options);


    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    });

    function actualiza_select{{$var_tiempo_endos}}(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
        var longitud =  seleccionados.length;


      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

          $('#id_procedimiento{{$var_tiempo_endos}}').select2(hc4EndoscopySelect2Options);
    }

    function quita_select{{$var_tiempo_endos}}(){
        //alert("quita");
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

        $('#id_procedimiento{{$var_tiempo_endos}}').select2(hc4EndoscopySelect2Options);


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

    //guardar();
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
     actualiza_select{{$var_tiempo_endos}}();
    @endif

    @if($fecha_r == date('Y-m-d') )

        function autoguardado(id, tipo){
            var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
            var longitud =  parseInt(seleccionados.length);
            //console.log('entra');
            //console.log(tipo);
            //console.log($("#hc_protocolo"+id).serialize());return 0;
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
                                sincronizar_link_imagenes_procedimiento();
                                $("#alerta_datos").fadeIn(1000);
                                $("#alerta_datos").fadeOut(3000);
                            }else{
                                alert(datahtml);
                            }

                        },
                        error:  function(xhr){
                            mostrar_error_guardado_procedimiento(id, xhr);
                        }
                        });
                }else {
                    //$("#alerta_datos").fadeIn(1000);
                    //$("#alerta_datos").fadeOut(3000);
                    //alert("Debe Seleccionar un procedimiento Principal.");
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
                                    sincronizar_link_imagenes_procedimiento();
                                    $("#alerta_datos").fadeIn(1000);
                                    $("#alerta_datos").fadeOut(3000);
                                }else{
                                    alert(datahtml);
                                }

                            },
                            error:  function(xhr){
                                mostrar_error_guardado_procedimiento(id, xhr);
                            }
                            });
                    }else {
                        //$("#alerta_datos").fadeIn(1000);
                        //$("#alerta_datos").fadeOut(3000);
                        //alert("Debe Seleccionar un procedimiento Principal");
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
