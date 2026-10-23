
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">

    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }

    .boton-proce{
      font-size: 15px ;
      width: 20%;
      background-color: #004AC1;
      color: white;
      text-align: center;
      height: 35px;
      padding-left: 5px;
      padding-right: 5px;
      padding-bottom: 0px;
      padding-top: 7px;
      margin-bottom: 5px;
    }

    .parent{
     height: 462px;
    }

    .alerta_correcto{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;
      
    }


    .alerta_guardado{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;
      
    }

</style>

@php $aceptacion = 0;@endphp
<div role="alert" aria-live="polite" aria-atomic="true" class="alert alert-success alerta_guardado" id="alerta_guardado{{$orden->id}}" style="display: none;">
  <h4 class="alert-heading">{{trans('boxesh.GuardadoCorrectamente')}}  </h4>
</div>

<div role="alert" aria-live="polite" aria-atomic="true" class="alert alert-danger alerta_correcto" id="select_proc{{$orden->id}}" style="display: none;">
  <h4 class="alert-heading"> {{trans('boxesh.SeleccioneunProcedimiento')}} </h4>
</div>

<div id="req_val" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        {{trans('boxesh.Indique')}}
</div>
<div class="col-md-12" style="padding-left: 15px;padding-right: 30px;">

    @php

      $fecha = substr($orden->fecha_orden,0,10);
      $invert = explode( '-',$fecha);
      $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];

    @endphp
    <form method="POST" id="edit_orden{{$orden->id}}">
      {{ csrf_field() }}
      <input type="hidden" name="id_orden" value="{{$orden->id}}">
      <input type="hidden" name="id_ordenfun" value="{{$orden->id}}">
      <input type="hidden" name="id_ordenendos" value="{{$orden->id}}">
      
      <input type="hidden" name="id_pacient" value="{{$paciente->id}}">
      <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
      <input type="hidden" name="necesita_valoracion" value="1">

      <div class="col-md-12">&nbsp;</div>
      @php $co_aceptacion  = 0;$en_aceptacion = 0; $ec_aceptacion = 0; $cp_aceptacion  = 0;$br_aceptacion  = 0; @endphp
      <div class="row">
        @if($orden->tipo_procedimiento == 0)
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.MOTIVO')}}</span>
          <textarea name="xmotivo_orden" id="motivo" class="form-control input-sm" rows="3">@if(!is_null($orden)){{$orden->motivo_consulta}}@endif
          </textarea>
        </div>
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.RESUMENDELAHISTORIACLÍNICA')}}</span>
          <textarea name="endos_historia_clinica" id="resumen" class="form-control input-sm" rows="3">@if(!is_null($orden))<?php echo $orden->resumen_clinico ?>@endif
          </textarea>
        </div>
        <div class="col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.DIAGNOSTICO')}}</span>
          <textarea name="endos_desc_diagnostico" id="diagnostico" class="form-control input-sm" rows="3">  
            @if(!is_null($orden))<?php echo $orden->diagnosticos ?>@endif
          </textarea>
        </div>
        @else
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.MOTIVO')}}</span>
          <textarea name="xmotivo_orden" id="motivo" class="form-control input-sm" rows="3">@if(!is_null($orden)){{$orden->motivo_consulta}}@endif
          </textarea>
        </div>
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.RESUMENDELAHISTORIACLÍNICA')}}</span>
          <textarea name="func_historia_clinica" id="resumen" class="form-control input-sm" rows="3">@if(!is_null($orden))<?php echo $orden->resumen_clinico ?>@endif
          </textarea>
        </div>
        <div class="col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">{{trans('boxesh.DIAGNOSTICO')}}</span>
          <textarea name="func_des_diagnostico" id="diagnostico" class="form-control input-sm" rows="3">  
            @if(!is_null($orden))<?php echo $orden->diagnosticos ?>@endif
          </textarea>
        </div>
        @endif
        <div class="col-12">&nbsp;</div>
        @php
          $x_orden_tipo_eda = null;
          $x_orden_tipo_broncoscopia = null;
          $x_orden_tipo_colono = null;
          $x_orden_tipo_enteroscopia = null;
          $x_orden_tipo_ecoendoscopia = null;
          $x_orden_tipo_cpre = null;
          $x_orden_tipo_funcional= null;
          $x_orden_tipo_quirurgico= null;
          $x_orden_tipo_imagen= null;

          if(!is_null($orden->id)){
            $x_orden_tipo_eda = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','1')
              ->first();
            $x_orden_tipo_broncoscopia = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','14')
              ->first();
            $x_orden_tipo_colono = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','2')
              ->first();
            $x_orden_tipo_enteroscopia = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','3')
              ->first();
            $x_orden_tipo_ecoendoscopia = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','9')
              ->first();
            $x_orden_tipo_cpre = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
              ->where('id_grupo_procedimiento','10')
              ->first();
            $x_orden_tipo_funcional = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
                                                              ->where('id_grupo_procedimiento','18')
                                                              ->first();  
            $x_orden_tipo_quirurgico = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
                                                              ->where('id_grupo_procedimiento','21')
                                                              ->first();
            $x_orden_tipo_imagen = Sis_medico\Orden_Tipo::where('id_orden', $orden->id)
                                                              ->where('id_grupo_procedimiento','20')
                                                              ->first();                                                  
          }
     
        @endphp
        @if($orden->tipo_procedimiento == 0)
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.ENDOSCOPIASDIGESTIVAS')}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_endosco" class="form-control input-sm select2_proc" name="x_procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $aceptacion = 0;
               $validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_eda))
                @php
                  $proc_endo = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_eda->id)->get();
                @endphp
                  @foreach($proc_endo as $value)
                    @php
                      $clase = 'c';
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      $aceptacion++;
                      if($value->procedimiento->id == '71'){
                        $clase = 'c';
                      }
                    @endphp
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'c';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_eda)){
                    $validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_eda->id)->first();
                    if(!is_null($validacion)){
                      $aceptacion++;
                    }
                  }
                  if($value->id == '71'){
                    $clase = 'c';
                  }
                @endphp
                @if(is_null($validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.COLONOSCOPIAPROCTOLOGIA')}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_colono" class="form-control input-sm select2_proc" name="x_procedimiento_colono[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $co_aceptacion = 0;
               $co_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_colono))
                @php
                  $proc_colono = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_colono->id)->get();
                @endphp
                  @foreach($proc_colono as $value)
                    @php
                      $clase = 'co';
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      $co_aceptacion++;
                    @endphp
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'co';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_colono)){
                    $co_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_colono->id)->first();
                    if(!is_null($co_validacion)){
                      $co_aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($co_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.INTESTINODELGADO')}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_entero" class="form-control input-sm select2_proc" name="x_procedimiento_entero[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $en_aceptacion = 0;
               $en_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_enteroscopia))
                @php
                  $proc_enter = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_enteroscopia->id)->get();
                @endphp
                  @foreach($proc_enter as $value)
                    @php
                      $clase = 'en';
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      $en_aceptacion++;
                    @endphp
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'en';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_enteroscopia)){
                    $en_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_enteroscopia->id)->first();
                    if(!is_null($en_validacion)){
                      $en_aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($en_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.ECOENDOSCOPIAS')}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_ecoend" class="form-control input-sm select2_proc" name="x_procedimiento_ecoend[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $ec_aceptacion = 0;
               $ec_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_ecoendoscopia))
                @php
                  $proc_ecoend = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_ecoendoscopia->id)->get();
                @endphp
                @foreach($proc_ecoend as $value)
                  @php
                    $clase = 'ec';
                    if(!is_null($value->procedimiento->grupo_procedimiento)){
                      $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                    }
                    $ec_aceptacion++;
                  @endphp
                  <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'ec';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_ecoendoscopia)){
                    $ec_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_ecoendoscopia->id)->first();
                    if(!is_null($ec_validacion)){
                      $ec_aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($ec_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-12">&nbsp;</div> 
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">CPRE
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_cpre" class="form-control input-sm select2_proc" name="x_procedimiento_cpre[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $cp_aceptacion = 0;
               $cp_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_cpre))
                @php
                  $proc_cpre = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_cpre->id)->get();
                @endphp
                  @foreach($proc_cpre as $value)
                    @php
                      $clase = 'cp';
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      $cp_aceptacion++;
                    @endphp
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'cp';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_cpre)){
                    $cp_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_cpre->id)->first();
                    if(!is_null($cp_validacion)){
                      $cp_aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($cp_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.BRONCOSCOPIA')}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_bronc" class="form-control input-sm select2_proc" name="x_procedimiento_bronc[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $br_aceptacion = 0;
               $br_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_broncoscopia))
                @php
                  $proc_broncoscopia = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_broncoscopia->id)->get();
                @endphp
                  @foreach($proc_broncoscopia as $value)
                    @php
                      $clase = 'br';
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      $br_aceptacion++;
                    @endphp
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'br';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_broncoscopia)){
                    $br_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_broncoscopia->id)->first();
                    if(!is_null($br_validacion)){
                      $br_aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($br_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
        @elseif($orden->tipo_procedimiento=='3')
          @php $co_aceptacion  = 0;@endphp
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.PROCEDIMIENTOSQUIRURGICOS')}}  
              </label>
            </div>
          </div> 
          <div class="col-md-12">
            <select id="id_procedimiento_x" class="form-control input-sm select2_proc" name="x_procedimiento_func[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $aceptacion = 0;
               $validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_quirurgico))
                @php
                  $proc_quir = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_quirurgico->id)->get();
                @endphp
                @foreach($proc_quir as $value)
                  @php
                    $clase = 'c';
                    if(!is_null($value->procedimiento->grupo_procedimiento)){
                      $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                    }
                    $aceptacion++;
                  @endphp
                  <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                @endforeach
              @endif
              @foreach($qx as $value)
                @php
                  $clase = 'c';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_quirurgico)){
                    $validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_quirurgico->id)->first();
                    if(!is_null($validacion)){
                      $aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>
        
        @elseif($orden->tipo_procedimiento=='1')
          @php $co_aceptacion  = 0; $aceptacion = 0;@endphp
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label"> Procedimiento Funcional 
              </label>
            </div>
          </div> 
          <div class="col-md-12">
            <select id="id_procedimiento_x" class="form-control input-sm select2_proc" name="x_procedimiento_func[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $aceptacion = 0;
               $validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_funcional))
                @php
                  $proc_quir = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_funcional->id)->get();
                @endphp
                @foreach($proc_quir as $value)
                  @php
                    $clase = 'c';
                    if(!is_null($value->procedimiento->grupo_procedimiento)){
                      $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                    }
                    $aceptacion++;
                  @endphp
                  <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                @endforeach
              @endif
              @foreach($px as $value)
                @php
                  $clase = 'c';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_funcional)){
                    $validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_funcional->id)->first();
                    if(!is_null($validacion)){
                      $aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>

        @elseif($orden->tipo_procedimiento=='2')
          @php $co_aceptacion  = 0;$aceptacion = 0;@endphp
          <div class="col-12">&nbsp;</div>
          <div class="col-12">&nbsp;</div>
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.IMAGENES')}} 
              </label>
            </div>
          </div> 
          <div class="col-md-12">
            <select id="id_procedimiento_x" class="form-control input-sm select2_proc" name="x_procedimiento_func[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $aceptacion = 0;
               $validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_imagen))
                @php
                  $proc_quir = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_imagen->id)->get();
                @endphp
                @foreach($proc_quir as $value)
                  @php
                    $clase = 'c';
                    if(!is_null($value->procedimiento->grupo_procedimiento)){
                      $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                    }
                    $aceptacion++;
                  @endphp
                  <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                @endforeach
              @endif
              @foreach($ix as $value)
                @php
                  $clase = 'c';
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }
                  if(!is_null($x_orden_tipo_imagen)){
                    $validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_imagen->id)->first();
                    if(!is_null($validacion)){
                      $aceptacion++;
                    }
                  }
                @endphp
                @if(is_null($validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif
              @endforeach
            </select>
          </div>   
        @endif
        <div class="col-md-12"><br></div>
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION MEDICA:</span>
          <textarea name="observacion_medica" id="observacion_medica" class="form-control input-sm" rows="3" onchange="actualiza_orden_endoscopica({{$orden->id}});">  
            @if(!is_null($orden))<?php echo $orden->observacion_medica ?>@endif
          </textarea>
        </div>
        <div class="col-md-6">
          <center>
            <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="actualiza_orden_endoscopica({{$orden->id}})"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp; {{trans('boxesh.Guardar')}}
            </button>
          </center>
        </div>
        <div class="col-md-6">
          <center>
            <a type="button" class="btn btn-info btn_ordenes" href="{{route('decimopaso.imprimir_orden_funcional_hospital', ['id' => $orden->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp; {{trans('boxesh.DescargarOrden')}}
            </a>
          </center>
        </div>
        @if($orden->id_evolucion!=null)
        <!--div class="col-4">
          <center>
            <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="formato_012({{$orden->id_evolucion}},{{$orden->id}},'area_trabajo_formato012{{$orden->id}}')"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Formato 012
            </button>
          </center>
        </div-->
        @endif
      </div>
        
    </form>
    <div class="row" style="border-radius: 8px;" id="area_trabajo_formato012{{$orden->id}}"></div>

</div>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">


    /*tinymce.init({
      selector: '#endos_resumen_orden{{$orden->id}}',
      inline: true,
      menubar: false,
        invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('endos_resumen_orden{{$orden->id}}');
            $('#endos_historia_clinica{{$orden->id}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('endos_resumen_orden{{$orden->id}}');
              $('#endos_historia_clinica{{$orden->id}}').val(ed.getContent());

          });
      }
    });


    tinymce.init({
      selector: '#endos_diagnostico{{$orden->id}}',
      inline: true,
      menubar: false,
        invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('endos_diagnostico{{$orden->id}}');
            $('#endos_desc_diagnostico{{$orden->id}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('endos_diagnostico{{$orden->id}}');
              $('#endos_desc_diagnostico{{$orden->id}}').val(ed.getContent());

          });
      }
    });*/

</script>

<script type="text/javascript">

    //Funcion para actualizar Orden de Procedimiento Endoscopico
    function actualiza_orden_endoscopica(id_orden){

      @if($orden->tipo_procedimiento == 0)
        var jprocedimiento_endos = $('#id_procedimiento_endosco').val();
        var jprocedimiento_colono = $('#id_procedimiento_colono').val();
        var jprocedimiento_int = $('#id_procedimiento_entero').val();
        var jprocedimiento_ecoend = $('#id_procedimiento_ecoend').val();
        var jprocedimiento_cpre = $('#id_procedimiento_cpre').val();
        var jprocedimiento_bron = $('#id_procedimiento_bronc').val();

        if ((jprocedimiento_endos != "")||(jprocedimiento_colono != "")||(jprocedimiento_int != "")||(jprocedimiento_ecoend != "")||(jprocedimiento_cpre != "")||(jprocedimiento_bron != "")){
            $.ajax({
              type: "POST",
              url: "{{route('actualiza.ordenhc4_proendoscopica')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: "html",
              data: $("#edit_orden{{$orden->id}}").serialize(),
              success: function(datahtml){
                if(datahtml==1){
                  $("#req_val").fadeIn(1000);
                  $("#req_val").fadeOut(3000);
                  //return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
                }else{
                  $("#alerta_guardado{{$orden->id}}").fadeIn(1000);
                  $("#alerta_guardado{{$orden->id}}").fadeOut(3000);  
                 // return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
                }
              },
              error: function(){
                //console.log(data);
              }
            });
          }else {
            $("#select_proc{{$orden->id}}").fadeIn(1000);
            $("#select_proc{{$orden->id}}").fadeOut(3000);
          }
      @else
        var jprocedimientos = $('#id_procedimiento_x').val();

        if (jprocedimientos != ""){
          $.ajax({
              type: "POST",
              url: "{{route('decimopaso.procedimiento_actualizar_pxs')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: "html",
              data: $("#edit_orden{{$orden->id}}").serialize(),
              success: function(datahtml){
                $("#alerta_guardado{{$orden->id}}").fadeIn(1000);
                $("#alerta_guardado{{$orden->id}}").fadeOut(3000);
                //return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);

              },
              error:  function(){

                 //console.log();
              }
          });

        }else {
          $("#select_proc{{$orden->id}}").fadeIn(1000);
          $("#select_proc{{$orden->id}}").fadeOut(3000);
          //return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
        }
      @endif 
    }   

</script>

<script type="text/javascript">

    $('.c1').removeAttr('disabled');
    $('.c18').removeAttr('disabled');
    $('.c21').removeAttr('disabled');
    $('.c20').removeAttr('disabled');

    $('.select2_proc').select2({
            tags: false,
    });

    function actualiza_select(){

      var seleccionados =  $('#id_procedimiento_endosco').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){

        //$('.c1').attr('disabled','disabled');
        $('.c').removeAttr('disabled');
        //$('.c18').removeAttr('disabled');

      }else{
        $('.c1').removeAttr('disabled');
        $('.c').attr('disabled','disabled');

        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_endosco').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_endosco').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.c1').attr('disabled','disabled');
        $('.c').removeAttr('disabled');
        //$('.c18').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c1').removeAttr('disabled');
        $('.c').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_endosco').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_endosco").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

        actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});

    });

    $("#id_procedimiento_endosco").on("select2:unselecting", function (evt) {
       quita_select();
       actualiza_orden_endoscopica({{$orden->id}});
    });

    @if($aceptacion > 0 )
      actualiza_select();
    @endif

</script>

<script type="text/javascript">

  $('input[type="checkbox"].flat-green').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  });
  $('input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  }); 

    $('.co2').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function co_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_colono').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.co2').attr('disabled','disabled');
        $('.co').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.co2').removeAttr('disabled');
        $('.co').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_colono').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function co_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_colono').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.co2').attr('disabled','disabled');
        $('.co').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.co2').removeAttr('disabled');
        $('.co').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_colono').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_colono").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        co_actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});
    });

    $("#id_procedimiento_colono").on("select2:unselecting", function (evt) {
       co_quita_select();
       actualiza_orden_endoscopica({{$orden->id}});
    });

    @if($co_aceptacion > 0 )
      //alert("base");
      co_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.en3').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function en_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_entero').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.en3').attr('disabled','disabled');
        $('.en').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.en3').removeAttr('disabled');
        $('.en').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_entero').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function en_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_entero').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.en3').attr('disabled','disabled');
        $('.en').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.en3').removeAttr('disabled');
        $('.en').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_entero').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_entero").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        en_actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});
    });

    $("#id_procedimiento_entero").on("select2:unselecting", function (evt) {
       en_quita_select();
       actualiza_orden_endoscopica({{$orden->id}});
    });

    @if($en_aceptacion > 0 )
      //alert("base");
      en_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.ec9').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function ec_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.ec9').attr('disabled','disabled');
        $('.ec').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.ec9').removeAttr('disabled');
        $('.ec').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_ecoend').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function ec_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.ec9').attr('disabled','disabled');
        $('.ec').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.ec9').removeAttr('disabled');
        $('.ec').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_ecoend').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_ecoend").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        ec_actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});
    });

    $("#id_procedimiento_ecoend").on("select2:unselecting", function (evt) {
       ec_quita_select();
       actualiza_orden_endoscopica({{$orden->id}});
    });

    @if($ec_aceptacion > 0 )
      //alert("base");
      ec_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.cp10').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function cp_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_cpre').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.cp10').attr('disabled','disabled');
        $('.cp').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.cp10').removeAttr('disabled');
        $('.cp').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_cpre').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function cp_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_cpre').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.cp10').attr('disabled','disabled');
        $('.cp').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.cp10').removeAttr('disabled');
        $('.cp').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_cpre').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_cpre").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        cp_actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});
    });

    $("#id_procedimiento_cpre").on("select2:unselecting", function (evt) {
       cp_quita_select();
       actualiza_orden_endoscopica({{$orden->id}});
    });

    @if($cp_aceptacion > 0 )
      //alert("base");
      cp_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.br14').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function br_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_bronc').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.br14').attr('disabled','disabled');
        $('.br').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.br14').removeAttr('disabled');
        $('.br').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_bronc').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$orden->id}});


    }

    function br_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_bronc').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.br14').attr('disabled','disabled');
        $('.br').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.br14').removeAttr('disabled');
        $('.br').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_bronc').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_bronc").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        br_actualiza_select();
        actualiza_orden_endoscopica({{$orden->id}});
    });

    $("#id_procedimiento_bronc").on("select2:unselecting", function (evt) {
       br_quita_select();
    });

    @if($br_aceptacion > 0 )
      //alert("base");
      br_actualiza_select();
      actualiza_orden_endoscopica({{$orden->id}});
    @endif

    $('.flat-red').on('ifChecked', function(event){
      actualiza_orden_endoscopica({{$orden->id}});
    });

    function descargar(id_or){
       window.open('{{url('imprimir/orden_hc4/endoscopico')}}/'+id_or,'_blank');  
    }

</script>
















