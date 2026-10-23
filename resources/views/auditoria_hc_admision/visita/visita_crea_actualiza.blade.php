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
 <div class="modal fade" id="foto_auditoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$agenda->id_paciente}}</td>
                                    <td style="text-align:right;"><b>Cortesias en el día</b></td><td style="text-align:right; @if($cant_cortesias>1) color:red; @endif">{{$cant_cortesias}}</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <!--a type="button" href="{{route('agenda.detalle', ['id' => $id_agenda ])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Historia Clínica</span>
            </a-->
            <a type="button" href="{{ URL::previous() }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
        
        </div>
        <div class="col-md-12" style="padding-right: 6px;">
            
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#examenes_externos').click());"><b>Examenes Externos</b></a></h3>
                    </div>

                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="examenes_externos">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered  dataTable" >
                            <tbody style="font-size: 12px;">
                                @php $count=0; @endphp    
                                @foreach($laboratorio_externo as $imagen)
                                <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                        <span>{{$imagen->nombre_anterior}}</span>    
                                    </a> 
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        $d2 = $variable[4];
                                        $d3 = $variable[5];
                                        
                                    @endphp 
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>  
                                @endif      
                                </div>          
                                @endforeach  
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-right: 6px;">
            
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#biopsias_h').click());"><b>Historial de Biopsias</b></a></h3>
                    </div>

                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="biopsias_h">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered  dataTable" >
                            <tbody style="font-size: 12px;">
                                @php $count=0; @endphp    
                                @foreach($biopsias_1 as $imagen)
                                <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>    
                                    </a> 
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        $d2 = $variable[4];
                                        $d3 = $variable[5];
                                        
                                    @endphp 
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>  
                                @endif      
                                </div>          
                                @endforeach 

                                @php $count=0; @endphp    
                                @foreach($biopsias_2 as $imagen)
                                <div class="col-md-3" style='margin: 10px 0;text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>    
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        $d2 = $variable[4];
                                        $d3 = $variable[5];
                                        
                                    @endphp 
                                    <a data-toggle="modal" data-target="#foto_auditoria" href="{{ route('auditoria_hc_video_biopsias.mostrar_biopsias', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>  
                                    </a> 
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a> 
                                @endif      
                                </div>          
                                @endforeach   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        @if($agenda->estado_cita!='4')
        <div class="col-md-12" style="padding-right: 6px;">
            @php $dia =  Date('N',strtotime($agenda->fechaini)); $mes =  Date('n',strtotime($agenda->fechaini)); @endphp
            <div class="callout callout-warning col-md-12" style="margin-bottom: 5px;padding: 5px;">
             Paciente Aun No Admisionado Para 
             @if($agenda->proc_consul=='0') La Consulta 
             @elseif($agenda->proc_consul=='1') El Procedimiento 
             @endif 
             Del 
             @if($dia == '1') Lunes 
             @elseif($dia == '2') Martes 
             @elseif($dia == '3') Miércoles 
             @elseif($dia == '4') Jueves 
             @elseif($dia == '5') Viernes 
             @elseif($dia == '6') Sábado 
             @elseif($dia == '7') Domingo 
             @endif {{substr($agenda->fechaini,8,2)}} de 
             @if($mes == '1') Enero 
             @elseif($mes == '2') Febrero 
             @elseif($mes == '3') Marzo 
             @elseif($mes == '4') Abril 
             @elseif($mes == '5') Mayo 
             @elseif($mes == '6') Junio 
             @elseif($mes == '7') Julio 
             @elseif($mes == '8') Agosto 
             @elseif($mes == '9') Septiembre 
             @elseif($mes == '10') Octubre 
             @elseif($mes == '11') Noviembre 
             @elseif($mes == '12') Diciembre 
             @endif del {{substr($agenda->fechaini,0,4)}}, se encuentra en estado @if($agenda->estado_cita=='0') POR CONFIRMAR @elseif($agenda->estado_cita=='1') CONFIRMADO @elseif($agenda->estado_cita=='2') REAGENDAR @elseif($agenda->estado_cita=='3') SUSPENDIDA: {{$agenda->observaciones}} @endif      
            </div>

        </div>
        @endif
        <div class="col-md-12" style="padding-right: 6px;">
            @php $dia =  Date('N',strtotime($agenda->fechaini));
                 $mes =  Date('n',strtotime($agenda->fechaini)); 

                 $aud_evoluciones = DB::table('aud_hc_evolucion')->where('id_evolucion_org',$evolucion->id)->first();
                 $aud_procedimientos = DB::table('aud_hc_procedimientos')->where('id_procedimientos_org',$hc_procedimiento->id)->first();
            @endphp 

            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-body" style="padding: 5px;">  
                    <div class="col-md-12" style="padding: 1px;">
                        <form id="frm_evol">  
                            <input type="hidden" name="id_hc_procedimiento" value="{{$hc_procedimiento->id}}">
                            <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                                <div class="col-md-7">
                                    <b>Fecha Visita: </b>

                                    @if($agenda->proc_consul ==0 )

                                    @if($dia == '1') 
                                    Lunes @elseif($dia == '2') 
                                    Martes @elseif($dia == '3') 
                                    Miércoles @elseif($dia == '4') 
                                    Jueves @elseif($dia == '5') 
                                    Viernes @elseif($dia == '6') 
                                    Sábado @elseif($dia == '7') 
                                    Domingo 

                                    @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') 
                                    Enero @elseif($mes == '2') 
                                    Febrero @elseif($mes == '3') 
                                    Marzo @elseif($mes == '4') 
                                    Abril @elseif($mes == '5') 
                                    Mayo @elseif($mes == '6') 
                                    Junio @elseif($mes == '7') 
                                    Julio @elseif($mes == '8') 
                                    Agosto @elseif($mes == '9') 
                                    Septiembre @elseif($mes == '10') 
                                    Octubre @elseif($mes == '11') 
                                    Noviembre @elseif($mes == '12') 
                                    Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: <input type="hidden" value="{{$agenda->fechaini}}" name="fecha_doctor"></b>{{substr($agenda->fechaini,10,10)}}@else
                                    <div class="input-group date" style="">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(!is_null($evolucion->fecha_doctor)){{$aud_evoluciones->fecha_doctor}}@else{{$agenda->fechaini}}@endif" name="fecha_doctor" class="form-control input-sm" id="fecha_doctor" onchange="guardar_protocolo()">
                                    </div>
                                    
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <b>@if($agenda->proc_consul=='0')
                                        CONSULTA {{DB::table('especialidad')->find($agenda->espid)->nombre}} @elseif($agenda->proc_consul=='1')
                                        PROCEDIMIENTO @if(!is_null($evolucion))
                                        @php

                                        $procedimiento_evolucion_p = DB::table('aud_hc_procedimientos')->where('id_procedimientos_org',$evolucion->hc_id_procedimiento)->first();
                                        

                                        $procedimiento_evolucion  =  Sis_medico\aud_hc_procedimientos::find($procedimiento_evolucion_p->id);
                                        
                                        if($procedimiento_evolucion != null){
                                            if($procedimiento_evolucion->id_procedimiento_completo != null){
                                                echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
                                            }
                                        }

                                        @endphp
                                        @endif
                                    @endif</b>
                                </div>
                            </div>
                            <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                            <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">

                            @if($agenda->espid=='8')
                                @php
                                    $cardiologia = DB::table('hc_cardio')->where('hcid',$evolucion->hcid)->first(); 
                                @endphp  
                                @if(!is_null($cardiologia))
                                    <a href="{{route('auditoria_cardio.formato',['id' => $evolucion->id])}}">
                                        <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                            <span class="glyphicon glyphicon-file"> Cardiología</span>
                                        </button>    
                                    </a>
                                @endif    
                            @endif
                
                            <div class="col-md-6">
                                <h4><b>Preparación</b></h4>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="presion" class="control-label">P. Arterial</label>
                                    <input class="form-control input-sm" name="presion" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->presion}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="pulso" class="control-label">Pulso</label>
                                    <input class="form-control input-sm" name="pulso" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->pulso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="temperatura" class="control-label">Temperatura (ºC)</label>
                                    <input class="form-control input-sm" name="temperatura" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->temperatura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="o2" class="control-label">SaO2:</label>
                                    <input class="form-control input-sm" name="o2" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->o2}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="estatura" class="control-label">Estatura (cm)</label>
                                    <input class="form-control input-sm" id="estatura" name="estatura" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->altura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="peso" class="control-label">Peso (kg)</label>
                                    <input class="form-control input-sm" id="peso" name="peso" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->peso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="perimetro" class="control-label" id="peri">Perimetro Abdominal</label>
                                    <input class="form-control input-sm" id="perimetro" name="perimetro" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->perimetro}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;" >
                                    <label for="peso_ideal" class="control-label">Peso Ideal (kg)</label>
                                    <input class="form-control input-sm" id="peso_ideal" name="peso_ideal" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="gct" class="control-label">% GCT RECOMENDADO</label>
                                    <input class="form-control input-sm" id="gct" name="gct" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="imc" class="control-label">IMC</label>
                                    <input class="form-control input-sm" id="imc" name="imc" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="cimc" class="control-label">Categoria IMC</label>
                                    <input class="form-control input-sm" id="cimc" name="cimc" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <h4><b>Clasificación Child Pugh</b></h4>
                                <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id_child_pugh_org}}">
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="ascitis" class="control-label">Ascitis</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="ascitis" id="ascitis">
                                        <option @if($child_pugh->ascitis == 1) selected @endif value="1" >Ausente</option>
                                        <option @if($child_pugh->ascitis == 2) selected @endif value="2" >Leve</option>
                                        <option @if($child_pugh->ascitis == 3) selected @endif value="3" >Moderada</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="encefalopatia" class="control-label">Encefalopatia</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="encefalopatia" id="encefalopatia"> 
                                        <option @if($child_pugh->encefalopatia == 1) selected @endif value="1" >No</option>
                                        <option @if($child_pugh->encefalopatia == 2) selected @endif value="2" >Grado 1 a 2</option>
                                        <option @if($child_pugh->encefalopatia == 3) selected @endif value="3" >Grado 3 a 4</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="albumina" class="control-label">Albúmina(g/l)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="albumina" id="albumina"> 
                                        <option @if($child_pugh->albumina == 1) selected @endif value="1" >&gt; 3.5</option>
                                        <option @if($child_pugh->albumina == 2) selected @endif value="2" >2.8 - 3.5</option>
                                        <option @if($child_pugh->albumina == 3) selected @endif value="3" >&lt; 2.8</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="bilirrubina" class="control-label">Bilirrubina(mg/dl)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="bilirrubina" id="bilirrubina"> 
                                        <option @if($child_pugh->bilirrubina == 1) selected @endif value="1" >&lt; 2</option>
                                        <option @if($child_pugh->bilirrubina == 2) selected @endif value="2" >2 - 3</option>
                                        <option @if($child_pugh->bilirrubina == 3) selected @endif value="3" >&gt; 3</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="inr" class="control-label" >Protrombina% (INR)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="inr" id="inr"> 
                                        <option @if($child_pugh->inr == 1) selected @endif value="1" >&gt; 50 (&lt; 1.7)</option>
                                        <option @if($child_pugh->inr == 2) selected @endif value="2" >30 - 50 (1.8 - 2.3)</option>
                                        <option @if($child_pugh->inr == 3) selected @endif value="3" >&lt; 30 (&gt; 2.3)</option>
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
                                <!--DESDE AQUI--->
                                <input type="hidden" name="id_hc_procedimiento_org" value="{{$hc_procedimiento->id}}">
                               <input type="hidden" name="id" value="{{$aud_procedimientos->id}}">
                                <div class="col-md-12" style="padding: 1px;">
                                   <h4><b>Datos Generales</b></h4>
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="id_doctor_examinador" class="control-label">Medico Examinador</label>
                                        <select onchange="guardar_protocolo();"  class="form-control input-sm select2" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                            @foreach($doctores as $value)
                                                <option @if($aud_procedimientos->id_doctor_examinador == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6" style="padding: 1px;">
                                        <label for="id_seguro" class="control-label">Seguro</label>
                                        <select onchange="guardar_protocolo();cargar_empresa2();"  class="form-control input-sm" style="width: 100%;" name="id_seguro" id="id_seguro"> 
                                            @foreach($seguros as $value)
                                                <option @if($aud_procedimientos->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="div_empresa"></div>
                                    <div class="col-md-12 has-error" style="padding: 1px;">
                                        <label for="observaciones" class="control-label">Observaciones </label>
                                        <textarea class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;background-color: #ffffb3;" rows="7" onchange="guardar_protocolo();">{{$aud_procedimientos->observaciones}}</textarea>
                                    </div>
                                </div>
                            </div>
                                     
                            <!--div>{{$evolucion->motivo}}</div-->
                            <div class="col-md-12">    
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="motivo" class="control-label">Motivo </label>
                                    <textarea name="motivo" style="width: 100%;" rows="1" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($evolucion)){{$aud_evoluciones->motivo}}@endif</textarea>

                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="thistoria_clinica" class="control-label">Evolución</label>
                                    <div id="thistoria_clinica" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $aud_evoluciones->cuadro_clinico ?>@endif</div>
                                    <input type="hidden" name="historia_clinica" id="historia_clinica">
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="tresultado_ev" class="control-label">Resultados de Exámenes y Procedimientos Diagnósticos</label>
                                    <div id="tresultado_ev" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $aud_evoluciones->resultado ?>@endif</div>
                                    <input type="hidden" name="resultado_ev" id="resultado_ev">
                                </div>

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examen_fisico" class="control-label">Examen Fisico</label>
                                    <textarea name="examen_fisico" style="width: 100%;" rows="7" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                                </div>                                
                                
                            @if($agenda->espid=='8')
                                 
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="resumen" class="control-label">Resumen</label>
                                    <textarea name="resumen" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_diagnostico" class="control-label">Plan Diagnóstico</label>
                                    <textarea name="plan_diagnostico" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_tratamiento" class="control-label">Plan Tratamiento</label>
                                    <textarea name="plan_tratamiento" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
                                </div>
                            @endif  

                                <input type="hidden" name="codigo" id="codigo">

                                <label for="cie10" class="col-md-8 control-label" style="padding-left: 0px; @if($agenda->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b></label>
                                <div class="form-group col-md-8" style="padding: 1px; @if($agenda->proc_consul=='1') display: none; @endif">
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>

                                <div class="form-group col-md-2" style="padding: 1px; @if($agenda->proc_consul=='1') display: none; @endif">
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>   
                                    </select> 
                                </div>

                                @if($agenda->estado_cita=='4') 
                                <button id="bagregar" class="btn btn-success btn-sm col-md-2" style="@if($agenda->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                @endif

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

                                    <!--textarea name="rp" style="width: 100%;" readonly="readonly" rows="2" onchange="guardar_protocolo();">{{$evolucion->rp}}</textarea-->

                                @endif
                                */ ?>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examenes_realizar" class="control-label">Examenes a Realizar</label>
                                    <textarea name="examenes_realizar" style="width: 100%;" rows="2" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($evolucion)){{$agenda->examenes_realizar}}@endif</textarea>
                                </div>
                            </div> 
                        </form> 
                        @if($agenda->proc_consul=='0')
                        <div class="col-md-offset-5 col-md-12">
                            <a href="{{route('auditoria_orden_proc.crear_editar',['hcid' => $evolucion->hcid])}}">
                                <button class="btn btn-success">Orden De Procedimiento</button>    
                            </a>
                        </div> 
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
            }).on('dp.change', function (e) { 
                guardar_protocolo()});
    });
</script>
<script type="text/javascript">

    $('#foto_auditoria').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
        //$(this).find('#imagen_solita').empty().html('');
    });

    cargar_empresa2();
    function cargar_empresa2(){

        $.ajax({
          type: 'get',
          url:"{{ url('auditoria_empresa2/auditoria_historiaclinica/auditoria_cargar') }}/{{$hc_procedimiento->id}}/{{$agenda->id}}/"+$('#id_seguro').val(), 
          
          success: function(data){
                
            $('#div_empresa').empty().html(data);

          },

          error: function(data){
            
             
          }
        });  
    }
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
          url:"{{route('consulta.actualizar_auditoria')}}",
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
        var sexo = @if($agenda->sexo == 1){{$agenda->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$agenda->fecha_nacimiento}}');
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
                    url:"{{route('auditoria_epicrisis.cargar',['id' => $evolucion->hc_id_procedimiento])}}",
                    dataType: "json",
                    type: 'get',
                    success: function(data){
                        
                        var table = document.getElementById("tdiagnostico");

                        $.each(data, function (index, value) {
                            
                            var row = table.insertRow(index);
                            row.id = 'tdiag'+value.id;
                            //alert(value.cie10);
                            var cell1 = row.insertCell(0);
                            cell1.innerHTML = '<b>'+value.cie10+'</b>';

                            var cell2 = row.insertCell(1);
                            cell2.innerHTML = value.descripcion;

                            var vpre_def = '';
                            if(value.pre_def!=null){
                                vpre_def = value.pre_def;
                            }
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = vpre_def;

                            var cell4 = row.insertCell(3);
                            cell4.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
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
          url:"{{url('auditoria_cie10/auditoria_eliminar')}}/"+id_h,  //epicrisis.eliminar
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
                url:"{{route('auditoria_epicrisis.cie10_nombre')}}",
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
            url:"{{route('auditoria_epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);
                    //guardar_cie10();
                    
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

    $('#bagregar').click( function(e){
         e.preventDefault();
        if($('#cie10').val()!='' ){
            if($('#pre_def').val()!='' ){
                //alert("guardar");
                guardar_cie10();    
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo').val('');
        $('#cie10').val('');
        $('#pre_def').val(''); 

    });

    @if(!is_null($evolucion))
    //alert($evolucion)
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('auditoria_epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$evolucion->hcid}}, 'hc_id_procedimiento': {{$evolucion->hc_id_procedimiento}}, 'in_eg': null },
            success: function(data){
                
                if(data.count>0){

                    var indexr = data.count-1 
                    var table = document.getElementById("tdiagnostico");
                    var row = table.insertRow(indexr);
                    row.id = 'tdiag'+data.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>'+data.cie10+'</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = data.descripcion;

                    var vpre_def = '';
                    if(data.pre_def!=null){
                        vpre_def = data.pre_def;
                    }
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                    //aqui va para la receta
                    anterior = tinyMCE.get('trp').getContent();
                    //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                    tinyMCE.get('trp').setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                    $('#rp').val(tinyMCE.get('trp').getContent());
                    cambiar_receta_2(); 
                }    
               
                
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

        @if($agenda->estado_cita!='4') 
        readonly: 1,
        @else
        
        
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica');
               //alert(ed.getContent());
                $("#historia_clinica").val(ed.getContent());

            });
        },

        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_protocolo(); 
              
            });
          }
    });

    tinymce.init({
        selector: '#tresultado_ev',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if($agenda->estado_cita!='4') 
        readonly: 1,
        @else
        
        
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tresultado_ev');
               //alert(ed.getContent());
                $("#resultado_ev").val(ed.getContent());

            });
        },

        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tresultado_ev');
                $("#resultado_ev").val(ed.getContent());
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

            @if($agenda->estado_cita!='4') 
            readonly: 1,
            @else
            
            
            setup: function (editor) {
                editor.on('init', function (e) {
                   var ed = tinyMCE.get('tindicaciones');
                   //alert(ed.getContent());
                    $("#indicaciones").val(ed.getContent());

                });
            },

            @endif

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

        
        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
            });
        },
        @endif
        

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

        
        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
            });
        },
        @endif
        

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
                    //para receta
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;

                    keywords.forEach(function(element) {
                        //En caso de existir se asigna la posición en pos
                        pos = anterior2.search(element.toString());
                        //Si existe
                        if(pos!=-1){
                            resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                        }

                    });

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp').setContent(anterior2.substr(0, pos) +'\n'+ med.value +':  ' +med.cantidad +anterior2.substr(pos));
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }
                    //fin de receta
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion').getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                    tinyMCE.get('tprescripcion').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                    $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                    cambiar_receta_2(); 
                }else{
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp').getContent();
                    //codigo cie10 de posicion de receta
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;

                    keywords.forEach(function(element) {
                        //En caso de existir se asigna la posición en pos
                        pos = anterior2.search(element.toString());
                        //Si existe
                        if(pos!=-1){
                            resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                        }

                    });

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp').setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }
                    //fin de receta cie10
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
