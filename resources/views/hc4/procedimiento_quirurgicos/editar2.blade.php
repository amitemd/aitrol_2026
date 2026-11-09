<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }


    .vista-previa-img {
        max-width: 100%;
        max-height: 200px;
    }

    #area_trabajo .hc4-surgical-editor-shell {
        width: 100%;
        margin: 0;
        padding: 18px;
        background: #f6fbfd;
        color: #334155;
    }
    #area_trabajo .hc4-surgical-editor-form {
        width: 100%;
    }
    #area_trabajo .hc4-surgical-editor-form > .col-12,
    #area_trabajo .hc4-surgical-editor-form > .col-md-12 {
        float: none;
        width: 100%;
        padding-right: 0;
        padding-left: 0;
    }
    #area_trabajo .hc4-surgical-editor-card {
        height: auto;
        padding: 14px;
        border: 1px solid #d5e6ed;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(10, 91, 128, .08);
        box-sizing: border-box;
    }
    #area_trabajo .hc4-surgical-editor-top {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 0 0 12px;
    }
    #area_trabajo .hc4-surgical-editor-top > [class*="col-"] {
        float: none;
        width: auto;
        padding: 0;
    }
    #area_trabajo .hc4-surgical-editor-procedure { flex: 1 1 58%; }
    #area_trabajo .hc4-surgical-editor-quick-action {
        display: flex;
        flex: 1 1 32%;
        align-items: flex-start;
    }
    #area_trabajo .hc4-surgical-editor-quick-action > .col-12,
    #area_trabajo .hc4-surgical-editor-quick-action center,
    #area_trabajo .hc4-surgical-editor-quick-action center > .col-12 {
        width: 100%;
        padding: 0 !important;
    }
    #area_trabajo .hc4-surgical-editor-quick-action .btn,
    #area_trabajo .hc4-surgical-template-card .btn_ordenes,
    #area_trabajo .hc4-surgical-editor-footer .btn_ordenes {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 46px !important;
        min-height: 46px;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 9px !important;
        background: linear-gradient(100deg, #076a91 0%, #0794bd 60%, #17b2ad 100%) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(8, 117, 153, .16);
    }
    #area_trabajo .hc4-surgical-editor-procedure > .col-12 {
        float: none;
        width: 100%;
        padding: 0;
    }
    #area_trabajo .hc4-surgical-editor-procedure > .col-12 + .col-12 { margin-top: 6px; }
    #area_trabajo .hc4-surgical-editor-procedure > .col-12:first-child,
    #area_trabajo .hc4-surgical-template-label {
        color: #17627e;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
    }
    #area_trabajo .hc4-surgical-editor-procedure .btn {
        min-width: 120px;
        margin-top: 8px;
        border: 0;
        border-radius: 8px;
        background: #087ca7;
    }
    #area_trabajo .hc4-surgical-template-card { margin-bottom: 12px; }
    #area_trabajo .hc4-surgical-template-label {
        margin-bottom: 8px;
        color: #17627e;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }
    #area_trabajo .hc4-surgical-template-row {
        margin: 0;
        align-items: center;
    }
    #area_trabajo .hc4-surgical-template-row > [class*="col-"] { padding: 0 6px; }
    #area_trabajo .hc4-surgical-clinical-card {
        display: flow-root;
        margin-bottom: 12px;
    }
    #area_trabajo .hc4-surgical-editor-form label,
    #area_trabajo .hc4-surgical-editor-form span[style*="Helvetica general"] {
        color: #334155;
        font-weight: 600;
    }
    #area_trabajo .hc4-surgical-editor-form input[type="text"],
    #area_trabajo .hc4-surgical-editor-form input[type="number"],
    #area_trabajo .hc4-surgical-editor-form input[type="datetime-local"],
    #area_trabajo .hc4-surgical-editor-form select,
    #area_trabajo .hc4-surgical-editor-form .select2-container .select2-selection {
        min-height: 42px;
        border: 1px solid #cbdde6 !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #334155;
        box-sizing: border-box;
    }
    #area_trabajo .hc4-surgical-editor-form input[type="text"],
    #area_trabajo .hc4-surgical-editor-form input[type="number"] {
        width: 100%;
        padding: 8px 11px;
    }
    #area_trabajo .hc4-surgical-editor-form .select2-container { width: 100% !important; }
    #area_trabajo .hc4-surgical-editor-form .select2-container--default .select2-selection--multiple {
        padding: 5px 8px;
    }
    #area_trabajo .hc4-surgical-editor-form .select2-selection__choice {
        border: 1px solid #8dd6df !important;
        border-radius: 6px !important;
        background: #eaf9fb !important;
        color: #226479 !important;
    }
    #area_trabajo .hc4-surgical-clinical-card > .col-12,
    #area_trabajo .hc4-surgical-clinical-card > .form-group,
    #area_trabajo .hc4-surgical-clinical-card > .row {
        float: none;
        width: 100%;
        margin-bottom: 13px;
        padding-right: 0;
        padding-left: 0;
    }
    #area_trabajo .hc4-surgical-clinical-card > br { display: none; }
    #area_trabajo .hc4-surgical-clinical-card [contenteditable="true"],
    #area_trabajo .hc4-surgical-clinical-card div[id^="thallazgos"],
    #area_trabajo .hc4-surgical-clinical-card div[id^="tconclusion"] {
        min-height: 72px;
        padding: 10px;
        border: 1px solid #cbdde6 !important;
        border-radius: 8px;
        background: #fff;
    }
    #area_trabajo .hc4-surgical-editor-footer {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 4px 0 2px;
    }
    #area_trabajo .hc4-surgical-editor-footer > center,
    #area_trabajo .hc4-surgical-editor-footer .col-5 { width: auto; padding: 0 !important; }
    #area_trabajo .hc4-surgical-editor-footer .btn_ordenes { width: auto; min-width: 145px; }
    #area_trabajo .hc4-surgical-editor-footer .btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 46px;
        margin-left: 8px;
        padding: 0 16px;
        border-radius: 9px;
    }

    .hc4-safety-panel { border: 1px solid #b9d8e8; background: #f4fbff; padding: 20px; margin-top: 15px; border-radius: 8px; }
    .hc4-safety-panel h4 { margin: 0 0 8px; }
    .hc4-safety-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px 18px; }
    .hc4-safety-field { min-width: 0; margin: 0; }
    .hc4-safety-field--wide { grid-column: span 2; }
    .hc4-safety-field--full { grid-column: 1 / -1; }
    .hc4-safety-field > label { display: block; margin-bottom: 7px; font-weight: 600; }
    .hc4-safety-field .form-control { width: 100%; min-height: 42px; }
    .hc4-safety-field textarea.form-control { resize: vertical; }
    .hc4-safety-checks { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .hc4-safety-check { display: flex !important; align-items: center; gap: 10px; min-height: 46px; margin: 0 !important; padding: 11px 13px; border: 1px solid #c8dde7; border-radius: 7px; background: #fff; cursor: pointer; font-weight: 600; }
    .hc4-safety-check input[type="checkbox"] { appearance: auto !important; -webkit-appearance: checkbox !important; flex: 0 0 20px !important; width: 20px !important; min-width: 20px !important; max-width: 20px !important; height: 20px !important; margin: 0 !important; padding: 0 !important; accent-color: #087e9c; }
    .hc4-safety-field .form-control:disabled, .hc4-safety-check.is-disabled { opacity: .58; cursor: not-allowed; background: #eef3f5; }
    @media (max-width: 991px) {
        #area_trabajo .hc4-surgical-editor-top { display: block; }
        #area_trabajo .hc4-surgical-editor-top > [class*="col-"] { width: 100%; margin-bottom: 12px; }
        .hc4-safety-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .hc4-safety-checks { grid-template-columns: 1fr; }
    }
    @media (max-width: 575px) {
        .hc4-safety-grid { grid-template-columns: 1fr; }
        .hc4-safety-field--wide, .hc4-safety-field--full { grid-column: 1; }
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
<div class="row hc4-surgical-editor-shell">
    @php
        $agenda_editor = $procedimiento->historia ? $procedimiento->historia->agenda : null;
        $fecha_r = $agenda_editor ? date('Y-m-d', strtotime($agenda_editor->fechaini)) : date('Y-m-d');
    @endphp
    <input type="hidden" id="tipo_guardado{{$var_tiempo_endos}}" value="3">
    <form id="hc_protocolo{{$procedimiento->id}}" class="hc4-surgical-editor-form" style="width: 100%;" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="id_protocolo_cirugia" value="{{$id_protocolo_cirugia['id']}}">
        <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
         <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
        <input type="hidden" name="id_procedimiento" value="{{$procedimiento->id}}">
        <input type="hidden" name="id_protocolo" value="<?php if (!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif;?>">
        <input type="hidden" name="tipo" value="{{$tipo}}">
        <div class="row hc4-surgical-editor-top">
            <div class="col-6 hc4-surgical-editor-card hc4-surgical-editor-procedure">

                <div class="col-12">
                    <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento')</span>
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

                
                    <select id="id_procedimiento{{$var_tiempo_endos}}" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="@lang('hc4.seleccione')" style="width: 95.5%; padding-left: 15px " autocomplete="off" onchange="validar_proc();">
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
                    <button type="button" class="btn-primary btn" onclick="autoguardado({{$procedimiento->id}}, 1)"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp; @lang('hc4.guardar')</button>
                    @endif
                </div>
            </div>
            <div class="col-6 hc4-surgical-editor-card hc4-surgical-editor-quick-action">
                <div class="col-12" >
                    <!--<center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_proc_endos_plantilla', ['id' => $procedimiento->id.date('his') ])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar Plantilla
                            </a>
                        </div>
                    </center>-->
                    <center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_cpre.eco_modal', ['hcid' => $protocolo->hcid])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">@lang('hc4.agregarCPREECO')
                            </a>
                        </div>
                    </center>
                </div>
            </div>
        </div>
        <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
            @lang('hc4.seleccioneProcedimientoEndoscopico')
        </div>
        <br>
        <div class="col-12 hc4-surgical-editor-card hc4-surgical-template-card">
        <div class="col-12 hc4-surgical-template-label">
            <span style="font-family: 'Helvetica general';">@lang('hc4.plantillasProcedimientos')</span>
        </div>

        <div class="col-md-12">
            <div class="row hc4-surgical-template-row">
                <div class="col-md-7" style="width: 100%">
                    <select class="form-control select2_plantilla"  name="proc_com" style="width: 100%;">
                            <option value="">@lang('hc4.todos')</option>
                        @foreach($proc_completo as $value)
                            <option @if($value->id == $procedimiento_completo_plantilla) selected @endif value="{{$value->id}}">{{$value->nombre_general}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5" >
                    <a class="btn btn-info btn_ordenes" style="color: white; height: 100%; " onClick="cargar_plantilla('1',{{$procedimiento->id}}{{$var_tiempo_endos}}, {{$procedimiento->id}});">
                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                            <label style="font-size: 16px">@lang('hc4.insertarPlantilla')</label>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        </div>

        <br>

        <div class="col-12 hc4-surgical-editor-card hc4-surgical-clinical-card">
                <div class="form-group col-md-6">
                                 <label>Tipo de anestesia</label>
                                 <select  class="form-control input-sm" style="width: 100%;" name="servicio" id="servicio" required="required">
                                     <option value="">Seleccione un servicio</option>
                                     <option @if($id_protocolo_cirugia->servicio === 'GENERAL') selected @endif value="GENERAL">GENERAL</option>
                                     <option @if($id_protocolo_cirugia->servicio === 'REGIONAL') selected @endif value="REGIONAL">REGIONAL</option>
                                     <option @if($id_protocolo_cirugia->servicio === 'SEDACION') selected @endif value="SEDACION">SEDACION</option>
                                     <option @if($id_protocolo_cirugia->servicio === 'OTROS') selected @endif value="OTROS">OTROS</option>
                                 </select>
                             </div>

                             <br>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">@lang('hc4.seguro')</span>
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
                <input id="ch{{$protocolo->id}}" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> @lang('hc4.participeEsteProcedimiento')</span>
            </div>
            @endif
            <!-- DOCTOR TRAINING -->
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Dieresis</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="dieresis" id="dieresis" value="{{$id_protocolo_cirugia['dieresis']}}">
        </div>

        <br>

        
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Exposicion y Exploracion</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="exploracion" id="exploracion" value="{{$id_protocolo_cirugia['exploracion']}}">
        </div>

        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">@lang('hc4.hallazgos')</span>
        </div>
        <div class="col-12">
            <div id="thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif;?></div>
            <input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>
        <div class="col-12">&nbsp;</div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">@lang('hc4.conclusiones')</span>
        </div>
        <div class="col-12">
            <div id="tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif;?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>

        <br>

        

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">COMPLICACIONES DEL PROCEDIMIENTO QUIRURGICO</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="complicaciones" id="complicaciones" value="{{$id_protocolo_cirugia['complicaciones']}}">
        </div>

        
        <br>


        <div class="col-12">
            <span style="font-family: 'Helvetica general';">PERDIDA SANGUINEA TOTAL</span>
            <input type="text" name="perdida_sangre" id="perdida_sangre" placeholder="Ingrese la cantidad"> 
            <span style="font-family: 'Helvetica general';">MUESTRA</span>
            <input type="text" name="muestra1" id="muestra1" placeholder="muestra">
        </div>

        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Descripcion</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="descripcion" id="descripcion" value="{{$id_protocolo_cirugia['descripcion']}}">
        </div>
        <br>
        
    
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Transquirurgico</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="transquirurgico" id="transquirurgico123" value="{{$id_protocolo_cirugia['transquirurgico']}}">
        </div>



        <br>

        <div class="form-group col-md-6">
                                 <label>PROCEDIMIENTO</label>
                                 <select  class="form-control input-sm" style="width: 100%;" name="procedimiento123" id="procedimiento123" required="required">
                                     <option value="">Seleccione un procedimiento</option>
                                     <option @if($id_protocolo_cirugia->procedimiento === 'ELECTIVA') selected @endif value="ELECTIVA">ELECTIVA</option>
                                     <option @if($id_protocolo_cirugia->procedimiento === 'EMERGENCIA') selected @endif value="EMERGENCIA">EMERGENCIA</option>
                                     <option @if($id_protocolo_cirugia->procedimiento === 'URGENCIA') selected @endif value="URGENCIA">URGENCIA</option>
                                    
                                 </select>
                             </div>


        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Proyectado</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="proyectado" id="proyectado" value="{{$id_protocolo_cirugia['proyectado']}}">
        </div>

        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Realizado</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="realizado" id="realizado" value="{{$id_protocolo_cirugia['realizado']}}">
        </div>
        <br>



        <div class="col-12">
            <span class="col-4" style="font-family: 'Helvetica general';">BIOPSIA POR CONGELACION </span>
                <label class="radio-inline ">
                        <input type="checkbox" class="flat-orange" name="bioxsia" value="1" {{ $id_protocolo_cirugia->bioxsia == 1 ? 'checked' : '' }} required="required"> Sí
                </label>
                    <label class="radio-inline ">
                        <input type="checkbox" class="flat-orange" name="bioxsia" value="0" {{ $id_protocolo_cirugia->bioxsia == 0 ? 'checked' : '' }} required="required"> No
                </label>
                        <span class="col-4" style="font-family: 'Helvetica general';">Resultado</span>

                        <input type="text" name="resultado" id="resultado" placeholder="resultado">
        </div>

        <br>

        <div class="col-12">
            <span class="col-4" style="font-family: 'Helvetica general';">HISTOPATOLOGICO</span>
                <label class="radio-inline">
                    <input type="checkbox" class="flat-orange" name="histopatologico" value="1" {{ $id_protocolo_cirugia->histopatologico == 1 ? 'checked' : '' }} required="required"> Sí
                </label>
                <label class="radio-inline">
                    <input type="checkbox" class="flat-orange" name="histopatologico" value="0" {{ $id_protocolo_cirugia->histopatologico == 0 ? 'checked' : '' }} required="required"> No
                </label>
            <span class="col-4" style="font-family: 'Helvetica general';">Muestra</span>
                    <input type="text" name="muestra123" id="muestra123" placeholder="muestra">
        </div>


        <br>


        <div class="col-12">
            <span style="font-family: 'Helvetica general';">PRIMER AYUDANTE</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="ayudante1" id="ayudante1"  value="{{$id_protocolo_cirugia['ayudante1']}}">
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">SEGUNDO AYUDANTE</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="ayudante2" id="ayudante2"  value="{{$id_protocolo_cirugia['ayudante2']}}">
        </div>
        <br>

        
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">INTRUMENTISTA</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="intrumentista" id="intrumentista"  value="{{$id_protocolo_cirugia['intrumentista']}}">
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">CIRCULANTE</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="circulante" id="circulante"  value="{{$id_protocolo_cirugia['circulante']}}">
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">ANESTESIOLOGO/A</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="anestesiologo" id="anestesiologo"   value="{{$id_protocolo_cirugia['anestesiologo']}}">
        </div>
        <br>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">AYUDANTE ANESTECIA</span>
                <input  style=" font-size: 16px;"  class="col-12"  type="text" name="ayudante_anestesia" id="ayudante_anestesia"  value="{{$id_protocolo_cirugia['ayudante_anestesia']}}">
        </div>
        <br>







        
        <div class="col-12">
    <span style="font-family: 'Helvetica general';">Subir Archivo:</span>
    <input type="file" name="foto_quirurgico" id="foto_quirurgico" accept=".jpg, .jpeg, .png, .pdf, .xls, .xlsx, .doc, .docx" onchange="mostrarVistaPrevia()" />
    <input type="hidden" id="archi_vali" value="{{$id_protocolo_cirugia['archivo']}}}">
    @php 
    $emplace = asset('/');
    $remplace = str_replace('public', 'storage', $emplace);
    @endphp
    
    
    @if(isset($id_protocolo_cirugia['archivo']))
    <a href="{{$remplace . 'app/avatars/' . $id_protocolo_cirugia['archivo']}}" target="_blank"><h4>Descargar</h4></a>
        <img src="{{asset($remplace . 'app/avatars/'.$id_protocolo_cirugia['archivo'])}}" style="width: 25%; height: 100px" />
       <button type="button" class="btn btn-danger"  onclick="eliminar_quirurgico({{$id_protocolo_cirugia['id']}})"><i class="fa fa-trash"></i></button>
    @endif
</div>
<div id="vista-previa"></div>

                        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">&nbsp;</span>
        </div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
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
        
        </div>

        <div id="seguridad_procedimiento{{$procedimiento->id}}" class="col-12 hc4-safety-panel hc4-surgical-editor-card">
            <h4><i class="fa fa-shield"></i> Seguridad antes de iniciar el procedimiento</h4>
            <p class="text-muted">En el primer guardado se validan identidad, indicación, consentimiento vigente y checklist. La excepción de emergencia exige justificación y queda auditada.</p>
            <div id="error_seguridad_procedimiento{{$procedimiento->id}}" class="alert alert-warning" role="alert" style="display:none; white-space:normal;"></div>
            <div class="hc4-safety-grid">
                <div class="hc4-safety-field hc4-safety-field--wide"><label>Indicación clínica</label><textarea class="form-control" name="procedure_indication" rows="2"></textarea></div>
                <div class="hc4-safety-field"><label>Consentimiento</label><select class="form-control hc4-consent-field" name="consent_template_id"><option value="">Seleccione...</option>@foreach($consentFormats as $format)<option value="{{$format->id}}">{{$format->nombre}}</option>@endforeach</select></div>
                <div class="hc4-safety-field"><label>Versión</label><input class="form-control hc4-consent-field" name="consent_version" value="1.0" placeholder="Ej. 1.0"></div>
                <div class="hc4-safety-field"><label>Fecha y hora de firma</label><input type="datetime-local" class="form-control hc4-consent-field" name="consent_signed_at" value="{{date('Y-m-d\\TH:i')}}" max="{{date('Y-m-d\\TH:i')}}"></div>
                <div class="hc4-safety-field"><label>Firmado por</label><input class="form-control hc4-consent-field" name="consent_signed_by"></div>
                <div class="hc4-safety-field"><label>Calidad del firmante</label><select id="consent_signer_role{{$procedimiento->id}}" class="form-control hc4-consent-field" name="consent_signer_role"><option value="PATIENT">Paciente</option><option value="REPRESENTATIVE">Representante</option></select></div>
                <div class="hc4-safety-field"><label>Relación del representante</label><input id="consent_relationship{{$procedimiento->id}}" class="form-control hc4-consent-field" name="consent_representative_relationship" disabled></div>
                <div class="hc4-safety-checks">
                    <label class="hc4-safety-check"><input type="checkbox" name="procedure_identity_verified" value="1"><span>Identidad verificada</span></label>
                    <label id="checklist_label{{$procedimiento->id}}" class="hc4-safety-check"><input id="procedure_checklist{{$procedimiento->id}}" type="checkbox" name="procedure_checklist_complete" value="1"><span>Checklist completo</span></label>
                    <label class="hc4-safety-check"><input id="procedure_emergency{{$procedimiento->id}}" type="checkbox" name="procedure_emergency_exception" value="1"><span>Excepción por emergencia</span></label>
                </div>
                <div class="hc4-safety-field hc4-safety-field--full"><label>Justificación de emergencia</label><textarea id="procedure_emergency_reason{{$procedimiento->id}}" class="form-control" name="procedure_emergency_reason" rows="2" disabled></textarea></div>
            </div>
        </div>

        <div class="col-12 hc4-surgical-editor-footer" >
        <center>
        <div class="col-5" style="padding-top: 15px;text-align: center;">
            <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento_quirurgico({{$procedimiento->id}})" ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;@lang('hc4.guardar')
            </button>
            <a href="{{route('formulario.017',['id' =>$id_protocolo_cirugia->id_hc_protocolo])}}" target="_blank" class="btn btn-danger">DescargarPDF017<i class="fa fa-file-pdf-o" aria-hidder="true"></i></a>
        </div>
        </center>
        </div>
    </form>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">

    function mostrar_error_seguridad_quirurgica(mensaje) {
        var alerta = document.getElementById('error_seguridad_procedimiento{{$procedimiento->id}}');
        var seccion = document.getElementById('seguridad_procedimiento{{$procedimiento->id}}');
        if (alerta) {
            alerta.textContent = mensaje;
            alerta.style.display = 'block';
        }
        if (seccion && typeof seccion.scrollIntoView === 'function') {
            seccion.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    }

    function limpiar_error_seguridad_quirurgica() {
        var alerta = document.getElementById('error_seguridad_procedimiento{{$procedimiento->id}}');
        if (alerta) {
            alerta.textContent = '';
            alerta.style.display = 'none';
        }
    }

    function actualizar_campos_seguridad_quirurgica() {
        var emergencia = $('#procedure_emergency{{$procedimiento->id}}').is(':checked');
        var representante = $('#consent_signer_role{{$procedimiento->id}}').val() === 'REPRESENTATIVE';
        var panel = $('#seguridad_procedimiento{{$procedimiento->id}}');
        var checklist = $('#procedure_checklist{{$procedimiento->id}}');

        panel.find('.hc4-consent-field').prop('disabled', emergencia);
        $('#consent_relationship{{$procedimiento->id}}').prop('disabled', emergencia || !representante);
        checklist.prop('disabled', emergencia);
        $('#checklist_label{{$procedimiento->id}}').toggleClass('is-disabled', emergencia);
        $('#procedure_emergency_reason{{$procedimiento->id}}').prop('disabled', !emergencia);

        if (emergencia) {
            checklist.prop('checked', false);
        } else {
            $('#procedure_emergency_reason{{$procedimiento->id}}').val('');
        }
    }

    $('#consent_signer_role{{$procedimiento->id}}, #procedure_emergency{{$procedimiento->id}}')
        .off('change.hc4Safety')
        .on('change.hc4Safety', actualizar_campos_seguridad_quirurgica);
    actualizar_campos_seguridad_quirurgica();


    $(document).ready(function(){

    validar_proc();

      $('.select2_plantilla').select2({
        tags: false
      });

      $(".breadcrumb").append('<li class="active">@lang('hc4.procedimientos')</li>');

      calcular_puntaje(<?php echo e($procedimiento->id); ?>, {{$var_tiempo_endos}});

    });

    function calcular_puntaje(id_proc, tiempo){
        //alert(id_proc);
        total = 0;
        colon_derecho = parseInt($('#colon_derecho'+id_proc+tiempo).val());
        //alert(colon_derecho);
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
                        autoguardado({{$procedimiento->id}}, 1);
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

    function guardar_procedimiento_quirurgico(id){
       
        var jprocedimientos = $('#id_procedimiento{{$var_tiempo_endos}}').val();
        
        console.log (jprocedimientos);

        if (Array.isArray(jprocedimientos) && jprocedimientos.length > 0){
            var entra = id;
                $.ajax({
                type: "POST",
                url: "<?php echo e(route('guardar.procedimiento_quirurgico')); ?>",
                data: $("#hc_protocolo"+id).serialize(),
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                success: function(datahtml, entra){
                    if(datahtml == `{{trans('hc4.nopuedeguardarsinprocedimiento')}}`){
                        alert(datahtml);
                    }else{
                        limpiar_error_seguridad_quirurgica();
                        guardar_quirurgico();
                        $("#procedimiento"+id).html(datahtml);
                    }
                    //console.log(datahtml);
                },
                error:  function(xhr){
                    if (xhr.status === 422) {
                        mostrar_error_seguridad_quirurgica(xhr.responseText);
                    } else {
                        alert(xhr.responseText || 'Error al guardar el procedimiento quirúrgico');
                    }
                }
                });
        }else {
            $("#select_proc").fadeIn(1000);
            $("#select_proc").fadeOut(3000);
            //alert("Seleccione un Procedimiento");
        }
    };


    function guardar_quirurgico(mensaje) {

        var formData = new FormData();
        var archivoInput = document.getElementById('foto_quirurgico');
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append('archivo', archivoInput.files[0]);
        }
        formData.append('formulario', $("#hc_protocolo{{$procedimiento->id}}").serialize()); 
        //alert(formData);
        $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData: false, 
        contentType: false,
        url: "{{route('guardar.procedimiento_quirurgico2')}}",
        data: formData,
        datatype: "json",
        success: function(datahtml) {
            console.log(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
        });
  }

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
                alert(`{{trans('main.noExistePlantillaPrecargada')}}`);
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
        $('#id_procedimiento{{$var_tiempo_endos}}').select2({
                tags: false,
            });
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
                        url: "{{route('guardar.procedimiento_quirurgico_autoguardado')}}",
                        data: $("#hc_protocolo"+id).serialize(),
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: "html",
                        success: function(datahtml){
                            console.log(datahtml);
                            if(datahtml == "ok"){
                                limpiar_error_seguridad_quirurgica();
                                $("#alerta_datos").fadeIn(1000);
                                $("#alerta_datos").fadeOut(3000);
                            }else{
                                alert(datahtml);
                            }

                        },
                        error:  function(xhr){
                            if (xhr.status === 422) {
                                mostrar_error_seguridad_quirurgica(xhr.responseText);
                            } else {
                                alert(xhr.responseText || 'Error al guardar el procedimiento quirúrgico');
                            }
                        }
                        });
                }else {
                    //$("#alerta_datos").fadeIn(1000);
                    //$("#alerta_datos").fadeOut(3000);
                    //console.log('parsea');
                    alert(`{{trans('main.debeSeleccionarProcedimientoPrincipal')}}`);
                }

            }else{
                if(tipo != parseInt(tipo_2)){
                    if(longitud>0){
                        var entra = id;
                            $.ajax({
                            type: "POST",
                            url: "{{route('guardar.procedimiento_quirurgico_autoguardado')}}",
                            data: $("#hc_protocolo"+id).serialize(),
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: "html",
                            success: function(datahtml){
                                console.log(datahtml);
                                if(datahtml == "ok"){
                                    limpiar_error_seguridad_quirurgica();
                                    $("#alerta_datos").fadeIn(1000);
                                    $("#alerta_datos").fadeOut(3000);
                                }else{
                                    alert(datahtml);
                                }

                            },
                            error:  function(xhr){
                                if (xhr.status === 422) {
                                    mostrar_error_seguridad_quirurgica(xhr.responseText);
                                } else {
                                    alert(xhr.responseText || 'Error al guardar el procedimiento quirúrgico');
                                }
                            }
                            });
                    }else {
                        //$("#alerta_datos").fadeIn(1000);
                        //$("#alerta_datos").fadeOut(3000);
                        alert(`{{trans('main.debeSeleccionarProcedimientoPrincipal')}}`);
                    }
                }
            }
            $('#tipo_guardado{{$var_tiempo_endos}}').val(tipo);

        }

    @else
        function autoguardado(id){

        }
    @endif





    function mostrarVistaPrevia() {

        let imagenvali = $("#archi_vali").val();
      
        if(imagenvali != ""){
        
            const archivoInput = document.getElementById("foto_quirurgico");
        const vistaPrevia = document.getElementById("vista-previa");

        if (archivoInput.files && archivoInput.files[0]) {
            const archivo = archivoInput.files[0];
            const tipoArchivo = archivo.type;
            
            // Verifica si el tipo de archivo es una imagen
            if (tipoArchivo.startsWith("image/")) {
                const img = document.createElement("img");
                img.classList.add("vista-previa-img");
                img.file = archivo;
                vistaPrevia.innerHTML = ""; // Limpia cualquier vista previa anterior
                vistaPrevia.appendChild(img);
                
                const lector = new FileReader();
                lector.onload = (function (unaImagen) {
                    return function (e) {
                        unaImagen.src = e.target.result;
                    };
                })(img);
                
                lector.readAsDataURL(archivo);
            } else {
                vistaPrevia.innerHTML = `Nombre del archivo: ${archivo.name}`;
            }
        } else {
            vistaPrevia.innerHTML = "";
        }
        
        }
        
    }

     function eliminar_quirurgico(id_eliminar) {
            $.ajax({
                type: "POST",
                url: "{{route('eliminar.procedimiento_quirurgico')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    id : id_eliminar
                },
                datatype: "json",
                success: function(data) {
                  console.log(data);
                    if (data.status) {
                        alert(data.msj);
                    } else {
                        alert(data.msj);
                    }
                },
                error: function() {
                    alert('error al cargar');
                }
            });
        }
    
</script>
