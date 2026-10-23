
@extends('hc_admision.visita.base')

@section('action-content')

<style type="text/css">
    
    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    } 
    .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }
       
        .ui-widget
        { 
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222; 
        }

        .mce-edit-focus,
        .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
        }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $evolucion->historiaclinica->paciente->apellido1}} @if($evolucion->historiaclinica->paciente->apellido2 != "(N/A)"){{ $evolucion->historiaclinica->paciente->apellido2}}@endif {{ $evolucion->historiaclinica->paciente->nombre1}} @if($evolucion->historiaclinica->paciente->nombre2 != "(N/A)"){{ $evolucion->historiaclinica->paciente->nombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$evolucion->historiaclinica->id_paciente}}</td>
                                    
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        
       
        <div class="col-md-12" style="padding-right: 6px;">
            @php 
                $agenda = DB::table('agenda')->where('id',$evolucion->historiaclinica->id_agenda)->first();    
                $dia =  Date('N',strtotime($agenda->fechaini));
                $mes =  Date('n',strtotime($agenda->fechaini)); 
            @endphp 

            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-body" style="padding: 5px;">  
                    <div class="col-md-12" style="padding: 1px;">
                        <form id="frm_evol">  
                            <input type="hidden" name="id_hc_procedimiento" value="{{$hc_procedimiento->id}}">
                            <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                                <div class="col-md-5">
                                    <b>Fecha Visita: </b>@if($agenda->proc_consul ==0 )
                                    @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: <input type="hidden" value="{{$agenda->fechaini}}" name="fecha_doctor"></b>{{substr($agenda->fechaini,10,10)}}@else
                                    <div class="input-group date" style="">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(!is_null($evolucion->fecha_doctor)){{$evolucion->fecha_doctor}}@else{{$agenda->fechaini}}@endif" name="fecha_doctor" class="form-control input-sm" id="fecha_doctor" onchange="guardar_protocolo()">
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <b>@if($agenda->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($agenda->espid)->nombre}} @elseif($agenda->proc_consul=='1')PROCEDIMIENTO @if(!is_null($evolucion))
                                        @php
                                        $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);
                                        if($procedimiento_evolucion != null){
                                            if($procedimiento_evolucion->id_procedimiento_completo != null){
                                                echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
                                            }
                                        }
                                        @endphp
                                        @endif
                                    @endif</b>
                                    
                                </div>
                                <div class="col-md-2">
                                    <a target="_blank" href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-success btn-sm">
                                        <span class="glyphicon glyphicon-download-alt"> Receta</span>
                                    </a>
                                </div>
                            </div>
                            <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                            <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
                            <div class="col-md-6">
                                <h4><b>Preparación</b></h4>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="presion" class="control-label">P. Arterial</label>
                                    <input class="form-control input-sm" name="presion" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->presion}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="pulso" class="control-label">Pulso</label>
                                    <input class="form-control input-sm" name="pulso" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->pulso}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="temperatura" class="control-label">Temperatura (ºC)</label>
                                    <input class="form-control input-sm" name="temperatura" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->temperatura}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="o2" class="control-label">SaO2:</label>
                                    <input class="form-control input-sm" name="o2" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->o2}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="estatura" class="control-label">Estatura (cm)</label>
                                    <input class="form-control input-sm" id="estatura" name="estatura" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->altura}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="peso" class="control-label">Peso (kg)</label>
                                    <input class="form-control input-sm" id="peso" name="peso" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->peso}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="perimetro" class="control-label" id="peri">Perimetro Abdominal</label>
                                    <input class="form-control input-sm" id="perimetro" name="perimetro" style="width: 100%;" rows="4"  value="{{$evolucion->historiaclinica->perimetro}}"  readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;" >
                                    <label for="peso_ideal" class="control-label">Peso Ideal (kg)</label>
                                    <input class="form-control input-sm" id="peso_ideal" name="peso_ideal" disabled style="width: 100%;" rows="4"   readonly="yes" >
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="gct" class="control-label">% GCT RECOMENDADO</label>
                                    <input class="form-control input-sm" id="gct" name="gct" disabled style="width: 100%;" rows="4"   readonly="yes" >
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="imc" class="control-label">IMC</label>
                                    <input class="form-control input-sm" id="imc" name="imc" disabled style="width: 100%;" rows="4"   readonly="yes" >
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="cimc" class="control-label">Categoria IMC</label>
                                    <input class="form-control input-sm" id="cimc" name="cimc" disabled style="width: 100%;" rows="4"   readonly="yes" >
                                </div>
                                <h4><b>Clasificación Child Pugh</b></h4>
                                <input type="hidden" name="id_child_pugh" value="@if(isset($child_pugh)){{ $child_pugh->id }}@endif">
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="ascitis" class="control-label">Ascitis</label>
                                    <select   class="form-control input-sm" style="width: 100%;" name="ascitis" id="ascitis" disabled>
                                        <option @if(isset($child_pugh)) @if($child_pugh->ascitis == 1) selected @endif @endif value="1" >Ausente</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->ascitis == 2) selected @endif @endif value="2" >Leve</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->ascitis == 3) selected @endif @endif value="3" >Moderada</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="encefalopatia" class="control-label">Encefalopatia</label>
                                    <select   class="form-control input-sm" style="width: 100%;" name="encefalopatia" id="encefalopatia" disabled> 
                                        <option @if(isset($child_pugh)) @if($child_pugh->encefalopatia == 1) selected @endif @endif value="1" >No</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->encefalopatia == 2) selected @endif @endif value="2" >Grado 1 a 2</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->encefalopatia == 3) selected @endif @endif value="3" >Grado 3 a 4</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="albumina" class="control-label">Albúmina(g/l)</label>
                                    <select   class="form-control input-sm" style="width: 100%;" name="albumina" id="albumina" disabled> 
                                        <option @if(isset($child_pugh)) @if($child_pugh->albumina == 1) selected @endif @endif value="1" >&gt; 3.5</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->albumina == 2) selected @endif @endif value="2" >2.8 - 3.5</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->albumina == 3) selected @endif @endif value="3" >&lt; 2.8</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="bilirrubina" class="control-label">Bilirrubina(mg/dl)</label>
                                    <select   class="form-control input-sm" style="width: 100%;" name="bilirrubina" id="bilirrubina" disabled> 
                                        <option @if(isset($child_pugh)) @if($child_pugh->bilirrubina == 1) selected @endif @endif value="1" >&lt; 2</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->bilirrubina == 2) selected @endif @endif value="2" >2 - 3</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->bilirrubina == 3) selected @endif @endif value="3" >&gt; 3</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="inr" class="control-label" >Protrombina% (INR)</label>
                                    <select   class="form-control input-sm" style="width: 100%;" name="inr" id="inr" disabled> 
                                        <option @if(isset($child_pugh)) @if($child_pugh->inr == 1) selected @endif @endif value="1" >&gt; 50 (&lt; 1.7)</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->inr == 2) selected @endif @endif value="2" >30 - 50 (1.8 - 2.3)</option>
                                        <option @if(isset($child_pugh)) @if($child_pugh->inr == 3) selected @endif @endif value="3" >&lt; 30 (&gt; 2.3)</option>
                                    </select>
                                </div>

                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="puntaje" class="control-label">Puntaje</label>
                                    <input class="form-control input-sm" id="puntaje" name="puntaje" disabled style="width: 100%;" readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="clase" class="control-label">Clase</label>
                                    <input class="form-control input-sm" id="clase" disabled style="width: 100%;"  readonly="yes">
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="sv1" class="control-label">SV1 Año:</label>
                                    <input class="form-control input-sm" id="sv1" disabled style="width: 100%;"  readonly="yes">
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="sv2" class="control-label">SV2 años:</label>
                                    <input class="form-control input-sm" id="sv2" disabled style="width: 100%;" readonly="yes">
                                </div>
                            </div>    
                            <div class="col-md-6">
                                
                                <div class="col-md-12" style="padding: 1px;">
                                   <h4><b>Datos Generales</b></h4>
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="id_doctor_examinador" class="control-label col-md-4">Medico Examinador</label>
                                        <input class="col-md-8" type="text" name="examinador" readonly value="@if(isset($hc_procedimiento->doctor)){{$hc_procedimiento->doctor->apellido1}} {{$hc_procedimiento->doctor->nombre1}}@else{{$hc_procedimiento->historia->doctor_1->apellido1}} {{$hc_procedimiento->historia->doctor_1->nombre1}}@endif"> 
                                    </div>
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="id_seguro" class="control-label col-md-4">Seguro</label>
                                        <input class="col-md-8" type="text" name="seguro" readonly value="@if(isset($hc_procedimiento->seguro)){{$hc_procedimiento->seguro->nombre}}@else{{$hc_procedimiento->historia->seguro->nombre}}@endif">
                                    </div>
                                    <div class="col-md-12 has-error" style="padding: 1px;">
                                        <label for="observaciones" class="control-label">Observaciones</label>
                                        <textarea class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;background-color: #ffffb3;" rows="7" readonly>{{$hc_procedimiento->observaciones}}</textarea>
                                    </div> 
                                </div>
                                
                            </div>
                            <div class="col-md-12">    
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="motivo" class="control-label">Motivo</label>
                                    <textarea name="motivo" style="width: 100%;" rows="1"   readonly="yes" >@if(!is_null($evolucion)){{$evolucion->motivo}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="thistoria_clinica" class="control-label">Evolución</label>
                                    <div id="thistoria_clinica" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $evolucion->cuadro_clinico ?>@endif</div>
                                    <input type="hidden" name="historia_clinica" id="historia_clinica">
                                </div>

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examen_fisico" class="control-label">Examen Fisico</label>
                                    <textarea name="examen_fisico" rows="8" style="width: 100%;" readonly="yes" >@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                                </div>                                
                                
                                @if($agenda->espid=='8')  
                                @php
                                    $cardiologia = DB::table('hc_cardio')->where('hcid',$evolucion->hcid)->first(); 
                                @endphp  
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="resumen" class="control-label">Resumen</label>
                                    <textarea name="resumen" style="width: 100%;" rows="1"  readonly="yes" >@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_diagnostico" class="control-label">Plan Diagnóstico</label>
                                    <textarea name="plan_diagnostico" style="width: 100%;" rows="1"  readonly="yes" >@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_tratamiento" class="control-label">Plan Tratamiento</label>
                                    <textarea name="plan_tratamiento" style="width: 100%;" rows="1"  readonly="yes" >@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
                                </div>
                                @endif

                                <input type="hidden" name="codigo" id="codigo">

                                <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px; @if($agenda->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b></label>
                               

                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                    <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
                                        
                                    </table>
                                </div>
                                <?php /*
                                @if(!is_null($evolucion))
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="rp" class="control-label col-md-6">RP</label>
                                    @if($agenda->estado_cita=='4') 
                                    <a href="{{route('receta.receta', ['hcid' => $evolucion->hcid])}}" class="col-md-offset-4 btn btn-success btn-sm col-md-2"><span> Receta</span></a>
                                    @endif
                                </div>

                                @php
                                    $rec_ev = Sis_medico\hc_receta::where('id_hc',$evolucion->hcid)->first();
                                    $medicinas = Sis_medico\Medicina::where('estado',1)->get();
                                    if(!is_null($rec_ev)){
                                        $det_ev = Sis_medico\hc_receta_detalle::where('id_hc_receta',$rec_ev->id)->get();
                                    }
                                @endphp
                                @if(!is_null($rec_ev))    
                                <div class="col-md-12" style="padding: 1px;border: 1px solid black;">
                                    @foreach($det_ev as $dvalue)
                                    @php $genericos = $medicinas->where('id',$dvalue->id_medicina)->first()->genericos; @endphp
                                    {{$dvalue->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$dvalue->cantidad}}<br>

                                    @endforeach
                                </div> 
                                @endif   

                                    <!--textarea name="rp" style="width: 100%;" readonly="readonly" rows="2" >{{$evolucion->rp}}</textarea-->

                                @endif
                                */ ?>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examenes_realizar" class="control-label">Examenes a Realizar</label>
                                    <textarea name="examenes_realizar" style="width: 100%;" readonly="yes" >@if(!is_null($evolucion)){{$evolucion->historiaclinica->examenes_realizar}}@endif</textarea>
                                </div>
                            </div> 
                        </form> 
                        @if($agenda->proc_consul=='0')
                        <!--div class="col-md-offset-5 col-md-12">
                            <a href="{{route('orden_proc.crear_editar',['hcid' => $evolucion->hcid])}}">
                                <button class="btn btn-success">Orden De Procedimiento</button>    
                            </a>
                        </div--> 
                        @endif       

                    </div>
                </div>    
            </div>    
        </div>
            
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha_doctor').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
            }).on('dp.change', function (e) { guardar_protocolo()});
    });
</script>
<script type="text/javascript">
    
    function datos_child_pugh(){
        dato1 = parseInt($('#ascitis').val());
        dato2 = parseInt($('#albumina').val());
        dato3 = parseInt($('#encefalopatia').val());
        dato4 = parseInt($('#bilirrubina').val());
        dato5 = parseInt($('#inr').val());
        cantidad = dato1+ dato2+dato3+dato4+dato5;
        $('#puntaje').val(cantidad);
        if(cantidad >= 5 && cantidad<=6){
            $('#clase').val('A');
            $('#sv1').val('100%');
            $('#sv2').val('85%');
        }else if(cantidad >= 7 && cantidad<=9){
            $('#clase').val('B');
            $('#sv1').val('80%');
            $('#sv2').val('60%');
        }else if(cantidad >= 10 && cantidad<=15){
            $('#clase').val('C');
            $('#sv1').val('45%');
            $('#sv2').val('35%');
        }
    }
    function guardar_protocolo(){
        calcular_indice();
        datos_child_pugh();
        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualizar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }
    function guardar_obs(){
        $.ajax({
          type: 'post',
          url:"{{route('visita.actualiza2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }
    tinymce.init({
        selector: '#hallazgos',
        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
              ajaxSave();  
            });
          }
      });
    function ajaxSave() {
        var ed = tinyMCE.get('hallazgos');
        $("#hallazgos").val(ed.getContent());
        guardar();
    }
    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('visita.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }
    function calcular_indice(){
        var peso =  document.getElementById('peso').value;
        var estatura = document.getElementById('estatura').value;
        var sexo = @if($evolucion->historiaclinica->paciente->sexo == 1){{$evolucion->historiaclinica->paciente->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$evolucion->historiaclinica->paciente->fecha_nacimiento}}');
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        } 
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc').val(texto);
        $('#gct').val(gct.toFixed(2));
        $('#imc').val(imc.toFixed(2));
        $('#peso_ideal').val(peso_ideal.toFixed(2));
    }

    

    $(document).ready(function() {
        var edad;
        index();
        datos_child_pugh();
        edad = calcularEdad('');
        $('#edad').val( edad );
        //cargar_historia();
        calcular_indice();
        @if(!is_null($evolucion))
        cargar_tabla();
            
        @endif 

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');
    });

     @if(!is_null($evolucion))
        function cargar_tabla(){
            $.ajax({
                    url:"{{route('epicrisis.cargar',['id' => $evolucion->hc_id_procedimiento])}}",
                    dataType: "json",
                    type: 'get',
                    success: function(data){
                        //console.log(data);
                        var table = document.getElementById("tdiagnostico");

                        $.each(data, function (index, value) {
                            
                            var row = table.insertRow(index);
                            row.id = 'tdiag'+value.id;
                            //alert(value.cie10);
                            //console.log(row);
                            var cell1 = row.insertCell(0);
                            cell1.innerHTML = value.cie10;
                            var cell2 = row.insertCell(1);
                            cell2.innerHTML = value.descripcion;
                            
                            //alert(index);                       
                        });

                    }
                })    
        }
    @endif

    function eliminar(id_h){

        
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        
        document.getElementById("tdiagnostico").deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',
          
          success: function(data){
            //console.log(data);
            //cargar_tabla();
          },
          error: function(data){
             //console.log(data);
          }
        });
    }


    

    $('.select2').select2({
            tags: false
        });

    $("#cie10").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);
                }
            })
        },
        minLength: 2,
    } );



    $("#cie10").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);
                    guardar_cie10();
                    
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

    $('#bagregar').click( function(){
        $('#codigo').val('');
        $('#cie10').val('');     
    });

    @if(!is_null($evolucion))
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': null, 'hcid': {{$evolucion->hcid}}, 'hc_id_procedimiento': {{$evolucion->hc_id_procedimiento}}, 'in_eg': null },
            success: function(data){
                
                //console.log(data);
                
                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                var cell1 = row.insertCell(0);
                cell1.innerHTML = data.cie10;
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;
                

                    /*$.each(data, function (index, value) {
                        
                        
                        alert(value.cie10);
                        console.log(row);
                       

                        
                        //alert(index);
                       
 
                    });*/
               
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
    @endif

    tinymce.init({
        selector: '#thistoria_clinica',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

         
        readonly: 1,
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_protocolo(); 
              
            });
          }
    });
    @if($agenda->proc_consul == 1)
        tinymce.init({
            selector: '#tindicaciones',
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:14px;}",

             
            readonly: 1,
            

            init_instance_callback: function (editor) {
                editor.on('Change', function (e) {
                    var ed = tinyMCE.get('tindicaciones');
                    $("#indicaciones").val(ed.getContent());
                    guardar_protocolo(); 
                  
                });
              }
        });
    @endif

    function guardar_cardio(){

        calcular_indice();

        $.ajax({
          type: 'post',
          url:"{{route('cardiologia.crea_actualiza')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){
            
          }
        });
    }
</script>

<script>
    /*$("#limpiar").click( function(){
        $('#nombre_generico').val(''); 
    });*/
    $("#nombre_generico").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('receta.buscar_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);
                }
            })
        },
        minLength: 2,
    } );
    
    $("#nombre_generico").change( function(){
        var variable1; 
        var variable2;
        $.ajax({
            type: 'post',
            url:"{{route('receta.buscar_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre_generico"),
            success: function(data){
                if(data!='0'){
                    //console.log(data);
                    if(data.dieta == 1 ){
                        //anterior = $('#prescripcion').val();
                        anterior = tinyMCE.get('tprescripcion').getContent();
                        //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                        tinyMCE.get('tprescripcion').setContent(anterior+ data.value +': \n' +data.dosis);
                        $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                        cambiar_receta_2();
                    }
                    if(data.dieta == 0){
                        Crear_detalle(data);
                    }
                    $('#nombre_generico').val('');
                }
                    
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });
    $("#prescripcion").change( function(){
       cambiar_receta_2();
    });
    $("#rp").change( function(){
       cambiar_receta_2();
    });


    function cambiar_receta_2(){

        $.ajax({
            type: 'post',
            url:"{{route('receta.update_receta_2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#final_receta").serialize(),
            success: function(data){
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
    tinymce.init({
        selector: '#trp',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        
        
        readonly: 1,
        
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
          }
    });

    tinymce.init({
        selector: '#tprescripcion',
        inline: true,
        menubar: false,

        content_style: ".mce-content-body {font-size:14px;}",

        
        
        readonly: 1,
        
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
          }
    });

    @if(!is_null($hc_receta))

    function Crear_detalle(med){
        var js_cedula = document.getElementById("id_paciente").value;
        //alert(js_cedula);
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/crear_detalle')}}"+"/"+{{$hc_receta->id}}+"/"+med.id+"/"+js_cedula, //receta.crear_detalle
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
            if(data == 1){
                console.log(med);
                if(med.genericos == null){
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp').getContent();
                    //$('#rp').empty().html(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                    tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                    $('#rp').val(tinyMCE.get('trp').getContent());
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion').getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                    tinyMCE.get('tprescripcion').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                    $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                    cambiar_receta_2(); 
                }else{
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp').getContent();
                    //$('#rp').empty().html(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                    tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                    $('#rp').val(tinyMCE.get('trp').getContent());
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion').getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
                    tinyMCE.get('tprescripcion').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                    $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                    cambiar_receta_2();
                }
                
            }else{
                $('#index').empty().html(data); 
            }
            console.log(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function index(){
        
        $.ajax({
          type: 'get',
          url:"{{route('receta.index_detalle',['receta' => $hc_receta->id])}}",
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function det_delete(id) {
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/eliminar_detalle')}}/"+{{$hc_receta->id}}+"/"+id,
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    @endif


    function guardar3(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.guardar2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form2").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }
    var vartiempo = setInterval(function(){ location.reload(); }, 7201000)
</script>                     
</section>

@include('sweet::alert')
@endsection
