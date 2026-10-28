
@extends('agenda.base')

@section('action-content')

  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
        <style>
            .glyphicon-refresh-animate {
                -animation: spin .7s infinite linear;
                -webkit-animation: spin2 .7s infinite linear;
            }

            @-webkit-keyframes spin2 {
                from { -webkit-transform: rotate(0deg);}
                to { -webkit-transform: rotate(360deg);}
            }

            @keyframes spin {
                from { transform: scale(1) rotate(0deg);}
                to { transform: scale(1) rotate(360deg);}
            }
            input.error { border: 1px solid red !important; }
            select.error { border: 1px solid red !important; }
            label.error{

                color: red !important;

            }



     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
        font-size: 12px;
     }



        </style>
        <style type="text/css">
          .icheckbox_flat-green.checked.disabled {
                background-position: -22px 0 !important;
                cursor: default;
            }
        </style>
 <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
 <!--link rel="stylesheet" href="{{ asset("/css/screen.css")}}"-->

 <!--div class="modal fade" id="verificacion" tabindex="0" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
         <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" ></iframe>
    </div>
  </div>
 </div-->
<div class="modal fade" id="confirmar" tabindex="0" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_confirmar">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="myModalLabel">@lang('agenda.estasegurodeagendar')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group col-md-12">
                <label for="max_consulta" class="col-md-12 control-label">@lang('agenda.estaagendado') @if($tipo_horario == 1) @lang('agenda.unprocedimiento_reunionhorarioconsulta') @endif @if($tipo_horario == 2) @lang('agenda.unaconsulta_reunionhorarioprocedimiento') @endif @lang('agenda.estasegurodehacerlo')
                </label>
            </div>
            <button type="button"  id="dato_confirmacion"  class="btn btn-primary">@lang('agenda.agendar')</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">@lang('agenda.cerrar')</button>
        </div>
    </div>
  </div>
</div>



<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />

<section class="content" >
    <div class="row">
            
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border" style="padding: 5px;">
                    <div class="col-md-12">
                        <h4> @lang('agenda.agregarNuevaAgendaDoctor-') {{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}} </h4>

                    </div>
                    <div class="form-group col-md-3">
                        <a class="btn btn-primary" href="{{ route('agenda.paciente', ['id' => $id, 'i' => '0', 'fecha' => $unix, 'sala' => '0'])}}" ><span class="glyphicon glyphicon-user"></span> @lang('agenda.agregarNuevoPaciente')</a>
                    </div>
                    <div class="form-group col-md-3">
                        <a class="btn btn-primary" href="{{ route('paciente.buscaxnombre', ['id_doc' => $id, 'fecha' => $unix, 'sala' => '0'])}}" ><span class="glyphicon glyphicon-search"></span>  @lang('agenda.pacienteporNombre')</a>
                    </div>
                    {{--
                    <div class="form-group col-md-3">
                        <a class="btn btn-primary" href="{{ route('iess_consultorio.crear_consulta', ['id' => $id, 'unix' => $unix]) }}"><span class="glyphicon glyphicon-plus-sign"></span> IESS @lang('agenda.consultorio')</a>
                    </div>
                    --}}
                    @if($paciente != Array())
                    <!--div class="form-group col-md-3">
                        <a  data-toggle="modal" data-target="#verificacion">
                                    <button type="button" class="btn btn-primary" >
                                        <span class="glyphicon glyphicon-globe"></span> @lang('agenda.csPublica')
                                    </button>
                                </a>
                    </div-->
                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn btn-warning btn-xs agbtn" href="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/{{$paciente->id}}" target="_blank"><span class="glyphicon glyphicon-globe"> </span> @lang('agenda.cPublica')</a>
                    </div>
                    @endif



                </div>
                <div class="box-body">

                    @if($citas == '[]' )
                        @php $citas = array();
                        @endphp
                    @endif
                    @if($citas != array())
                    <div  class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <!--th>
                                    <div class="callout callout-danger">
                                        El Paciente tiene {{$citas->count()}} agenda(s) para el día de hoy !!
                                    </div>
                                </th-->
                                <th>@lang('agenda.fecha')</th>
                                <th>@lang('agenda.hora')</th>
                                <th>@lang('agenda.tipo')</th>
                                <th>@lang('agenda.doctor')</th>
                                <th>@lang('agenda.estado')</th>
                                <th>@lang('agenda.agendadoPor')</th>
                                <th>@lang('agenda.modificadoPor')</th>
                                <th>@lang('agenda.accion')</th>

                            </thead>
                            <tbody style="background-color: #ffebe6;">
                                @foreach($citas as $cita)
                                    @if($cita->proc_consul<2)
                                    <tr >
                                        <!--td>@if($cita->proc_consul==0) CONSULTA @elseif($cita->proc_consul==1) PROCEDIMIENTO @endif Desde: {{substr($cita->fechaini,10)}} hasta: {{substr($cita->fechafin,10)}}  con el Dr(a).   agendado por {{Sis_medico\User::find($cita->id_usuariomod)->nombre1}} {{Sis_medico\User::find($cita->id_usuariomod)->apellido1}}</td-->
                                        @php
                                            $ya_agendado = false;
                                            if(Date('Y-m-d',strtotime($hora))==substr($cita->fechaini,0,10)){
                                                $ya_agendado = true;
                                            }
                                        @endphp
                                        <td ><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>{{substr($cita->fechaini,0,10)}} </span>@if(Date('Y-m-d',strtotime($hora))==substr($cita->fechaini,0,10)) <span style="background-color: red ; color: white;font-size: 14px;padding-left: 2px;padding-right: 2px;"><b> @lang('agenda.yaagendadoparalafecha') </b></span> @endif</a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>{{substr($cita->fechaini,10)}}</span></a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>@if($cita->proc_consul==0) @lang('agenda.consulta-') @elseif($cita->proc_consul==1) @lang('agenda.procedimiento-') @endif</span></a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>{{$cita->nombre1}}{{$cita->apellido1}}</span></a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>@if($cita->estado_cita=='0')@lang('agenda.porConfirmar')@elseif($cita->estado_cita=='1')@lang('agenda.confirmado')@elseif($cita->estado_cita=='-1')@lang('agenda.noAsiste')@elseif($cita->estado_cita=='3')@lang('agenda.suspendido')@elseif($cita->estado_cita=='4')@lang('agenda.asistio')@elseif($cita->estado_cita=='2')@if($cita->estado=='1')@lang('agenda.completarDatos')@else@lang('agenda.reagendar')@endif @endif</span></a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>{{substr($cita->ucnombre1,0,1)}}{{$cita->ucapellido1}}</span></a></td>
                                        <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif>{{substr($cita->umnombre1,0,1)}}{{$cita->umapellido1}}</span></a></td>
                                        <td>@if($cita->estado_cita=='2' || $cita->estado_cita=='-1'|| $cita->estado_cita=='3')@if($cita->id_doctor1!=null)<a href="{{ route('agenda.edit2', ['id' => $cita->id, 'doctor' => $cita->id_doctor1])}}" class="btn btn-warning btn-xs">@lang('agenda.reagendar')</a>@else<a href="{{ route('preagenda.edit', ['id' => $cita->id])}}" class="btn btn-warning btn-xs">@lang('agenda.reagendar')</a>@endif @endif</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    

                    <form class="form-vertical" id="target" role="form" method="POST" action="{{ route('agenda.store') }}" enctype="multipart/form-data">
             
                        <input type="hidden" class="form-control input-sm" name="especialidad_nombre" id="espenombre" value="{{$especialidad[0]->enombre}}">


                        <input type="hidden" class="form-control input-sm" name="tipo_horario" id="tipo_horario" value="{{$tipo_horario}}">
                        <input type="hidden" class="form-control input-sm" name="hospital_nombre" id="hosnombre" value="{{old('hospital_nombre')}}">
                        <input type="hidden" class="form-control input-sm" name="hospital_direccion" id="hosdireccion" value="{{old('hospital_direccion')}}">
                        <input type="hidden" class="form-control input-sm" name="consultorio_nombre" id="connombre" value="{{old('consultorio_nombre')}}">
                        <input type="hidden" class="form-control input-sm" name="nombre_doctor" id="nombre_doctor" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}">
                        <input type="hidden" class="form-control input-sm" name="procedimiento_nombre" id="pronombre" value="{{old('procedimiento_nombre')}}">
                        <input type="hidden" class="form-control input-sm" name="unix" id="unix" value="{{$unix}}">
                        {{ csrf_field() }}
                        <!--cedula-->
                        <a style="display: none;" id="mienlace"></a>
                        <div id="cambio4" class="form-group col-md-6 {{ $errors->has('id_paciente') ? ' has-error' : '' }} {{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="id" class="col-md-4 control-label">@lang('agenda.cedula')</label>

                        <div class="col-md-7">
                            <input id="idpaciente" maxlength="10" type="text" class="form-control input-sm" name="id_paciente" value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" onchange="teclaEnter2(event);" autofocus required>
                            @if ($errors->has('id_paciente'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_paciente') }}</strong>
                            </span>
                            @endif
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                        </div>
                                            
                            <!--nombre1-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre1') ? ' has-error' : '' }}has-success" id="cambio5">
                            <label for="nombre1" class="col-md-2 control-label" style="padding-left: 0;">@lang('agenda.nombre')</label>
                            <div class="col-md-9" style="padding-left: 0;">
                                <input type="hidden" class="form-control input-sm" name="nombre_paciente" value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" >
                                <input id="nombre1" type="text" class="form-control input-sm"  readonly value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div id="cambio17" class="form-group col-md-6 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">@lang('agenda.fechaNacimiento')</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" onchange="edad2();" value="@if( old('fecha_nacimiento')!=''){{ old('fecha_nacimiento') }}@elseif($paciente != Array()){{$paciente->fecha_nacimiento}}@endif" name="fecha_nacimiento" class="form-control input-sm" id="fecha_nacimiento"  placeholder="AAAA/MM/DD" >
                                </div>
                                   @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        @php

                            /*
                            $xseguro = null;
                            if($tipo_horario=='3'){
                                $xseguro = Sis_medico\Seguro::find('2');
                            }else{
                                if($paciente != Array()){
                                    $xseguro = Sis_medico\Seguro::find($paciente->id_seguro);
                                }
                            }
                            */


                        @endphp
                       

                        <!--seguro-->
                        
                        <div id="cambio15b" class="form-group col-md-3  " >
                            <label for="id_seguro" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.seguro')</label>
                            <div class="col-md-7" style="padding-left: 5px;padding-right: 0;">
                                <select id="id_seguro" name="id_seguro" onchange="valida_seguro(event);" class="form-control input-sm" required>
                                    <option value="" >@lang('agenda.seleccione')...</option>
                                    @foreach ($seguro as $seguro)
                                        @if($tipo_horario=='3')
                                            @if($seguro->id=='2')
                                                <option  @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                            @endif
                                        @else
                                        <option  @if($paciente != Array()) @if($paciente->id_seguro == $seguro->id)  @endif @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio15" class="form-group col-md-3  ">
                            <label for="Xedad" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.edad')</label>
                            <div class="col-md-6" style="padding-left: 0;">
                                <input id="Xedad" type="text" class="form-control input-sm" name="Xedad"  required readonly >
                                @if ($errors->has('Xedad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Xedad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--div id="cambio15a" class="form-group col-md-6  ">
                            <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
                            <div class="col-md-7">
                                <input id="cortesia" type="text" class="form-control input-sm" name="cortesia"  value="@if(!is_null($cortesia_paciente)) {{$cortesia_paciente->cortesia}} @else NO @endif" readonly>
                            </div>
                        </div-->

                        <div id="cambio15a" class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }} has-warning" >
                            <label for="cortesia" class="col-md-4 control-label">@lang('agenda.cortesia')</label>
                            <div class="col-md-7">
                                <select id="cortesia" name="cortesia" class="form-control input-sm" required>
                                    <option @if(!is_null($cortesia_paciente)) @if($cortesia_paciente->cortesia=='NO') selected @endif @endif value="NO">@lang('agenda.no')</option>
                                    <option @if(!is_null($cortesia_paciente)) @if($cortesia_paciente->cortesia=='SI') selected @endif @endif value="SI">@lang('agenda.si')</option>
                                </select>
                            </div>
                        </div>



                        <input type="hidden" class="form-control input-sm" name="id_doctor1" value="{{ $doctor->id}}" id="id_doctor1">
                            <!--primer apellido
                            <div class="form-group col-md-6 {{ $errors->has('id') ? ' has-error' : '' }}" >
                            <label for="nombre_doctor1" class="col-md-4 control-label">Doctor</label>
                            <div class="col-md-7">
                                <input type="hidden" class="form-control input-sm" name="id_doctor1" value="{{ $doctor->id}}"  >
                                <input id="nombre_doctor" maxlength="10" type="text" class="form-control input-sm" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  disabled="disabled">
                            </div>
                            </div>-->

                            <!--proc_consul-->


                        <div id="cambio7" class="form-group col-md-3 {{ $errors->has('espid') ? ' has-error' : '' }}" >
                            <label for="espid" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.especialidad')</label>
                            <div class="col-md-7" style="padding-left: 5px;padding-right: 0;">
                            <select id="espid" name="espid" class="form-control input-sm" onchange="especialidad();" >
                                    @foreach ($especialidad as $value)
                                        <option  @if(old('espid')==$value->espid){{"selected"}}@endif value="{{$value->espid}}">{{$value->enombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('espid'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('espid') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.tipo')</label>
                            <div class="col-md-6" style="padding-left: 0;">
                                <select id="proc_consul" name="proc_consul" class="form-control input-sm" onchange="campos();" required>
                                    @if($tipo_horario != '3')
                                    <option  value="">@lang('agenda.seleccione')..</option>
                                    @endif
                                    <option value="0" @if(old('proc_consul')=="0"){{"selected"}}@endif @if($tipo_horario == '1' || $tipo_horario == '3') selected @endif>@lang('agenda.consulta')</option>
                                    @if($tipo_horario != '-1' )
                                        @if($tipo_horario != '3')
                                        <option value="1" @if(old('proc_consul')=="1"){{"selected"}}@endif @if($tipo_horario == '2') selected @endif>@lang('agenda.procedimiento')</option>
                                        @endif
                                    @endif
                                    <option value="2" @if(old('proc_consul')=="2"){{"selected"}}@endif>@lang('agenda.reuniones')</option>
                                </select>
                                @if ($errors->has('proc_consul'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proc_consul') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                            <!--pais-->
                        <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">@lang('agenda.ubicacion')</label>
                            <div class="col-md-7">
                            <select id="id_sala" name="id_sala" class="form-control input-sm"  onchange="salas();" required>
                                    <option value="">@lang('agenda.seleccione')..</option>
                                    <?php /* @foreach ($salas as $sala)
<option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
@endforeach */?>
                                </select>
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="iempresa" class="form-group col-md-3 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                            <label for="id_empresa" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.empresa')</label>
                            <div class="col-md-7" style="padding-left: 5px;padding-right: 0;">
                                <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                    <option value=""  >@lang('agenda.seleccione')..</option>
                                    @foreach($empresa as $empresa)
                                        @if($empresa->id!='9999999999')
                                        <option @if(old('id_empresa')==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001") {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_empresa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_empresa') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--vip -->
                        <div id="ivip" class="form-group col-md-3 {{ $errors->has('vip') ? ' has-error' : '' }} oculto">
                            <label for="vip" class="col-md-5 control-label" style="color: red;padding-left: 0;">VIP</label>
                            <input type="checkbox" id="vip" class="flat-red" name="vip" value="1"
                            @if(old('vip')=="1")
                              checked
                            @endif>
                            @if ($errors->has('vip'))
                            <span class="help-block">
                                <strong>{{ $errors->first('vip') }}</strong>
                            </span>
                            @endif
                        </div>


                            <!--Teleconsulta-->


                            <!--seguro-->
                        <!--input id="id_seguro" name="id_seguro" value="@if($paciente != Array()){{$paciente->id_seguro}}@endif" type="hidden" -->


                        <div id="cambio1" class="form-group col-md-6 {{ $errors->has('id_doctor2') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor2" class="col-md-4 control-label">@lang('agenda.asistente') 1</label>
                            <div class="col-md-7">
                            <select id="id_doctor2" name="id_doctor2" class="form-control input-sm" >
                                    <option value="" selected>@lang('agenda.seleccione')..</option>
                                    @foreach ($users as $user)
                                        @if($doctor->id != $user->id)
                                        <option @if(old('id_doctor2')==$user->id){{"selected"}}@endif value="{{$user->id}}">Dr(a). {{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($enfermero as $user)
                                    @if($doctor->id != $user->id)
                                        <option @if(old('id_doctor2')==$user->id){{"selected"}}@endif value="{{$user->id}}">{{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_doctor2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio2" class="form-group col-md-6 {{ $errors->has('id_doctor3') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor3" class="col-md-2 control-label" style="padding-left: 0;">@lang('agenda.asistente') 2</label>
                            <div class="col-md-9" style="padding-left: 0;">
                            <select id="id_doctor3" name="id_doctor3" class="form-control input-sm">
                                    <option value="" >@lang('agenda.seleccione')..</option>
                                    @foreach ($users as $user)
                                    @if($doctor->id != $user->id)
                                        <option @if(old('id_doctor3')==$user->id){{"selected"}}@endif value="{{$user->id}}">@lang('agenda.dr_(a)') {{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                    @endif
                                    @endforeach
                                    @foreach ($enfermero as $user)
                                    @if($doctor->id != $user->id)
                                        <option @if(old('id_doctor3')==$user->id){{"selected"}}@endif value="{{$user->id}}">{{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_doctor3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" id="cambio6">
                            <label for="est_amb_hos" class="col-md-4 control-label" >@lang('agenda.tipodeingreso')</label>
                            <div class="col-md-7">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" onchange="ingreso();">
                                    <option @if(old('est_amb_hos')=="0"){{"selected"}}@endif value="0">@lang('agenda.ambulatorio')</option>
                                    <option style="color: red;" @if(old('est_amb_hos')=="1"){{"selected"}}@endif value="1" >@lang('agenda.hospitalizado')</option>
                                </select>
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('omni') ? ' has-error' : '' }} has-warning oculto" id="div_omni">
                            <label for="omni" class="col-md-4 control-label">OMNI</label>
                            <div class="col-md-7">
                                <select id="omni" name="omni" class="form-control input-sm" >
                                    <option value="">Seleccione ...</option>
                                    <option @if(old('omni')=="SI"){{"selected"}}@endif value="SI">@lang('agenda.si')</option>
                                    <option @if(old('omni')=="NO"){{"selected"}}@endif value="NO" >@lang('agenda.no')</option>
                                </select>
                                @if ($errors->has('omni'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('omni') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-12" style="margin-bottom: 1px;">
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">@lang('agenda.inicio')</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$hora}}" name="inicio" class="form-control input-sm" id="inicio" required onchange="incremento(event)">
                                </div>
                                   @if ($errors->has('inicio'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('inicio') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-2 control-label" style="padding-left: 0;">@lang('agenda.fin')</label>
                            <div class="col-md-9" style="padding-left: 0;">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fin') }}" name="fin" class="form-control input-sm" id="fin" required>
                                </div>
                                @if ($errors->has('fin'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fin') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php 
                        if(!is_null(old('procedimiento'))){
                            $cont=count(old('procedimiento'));
                        }else{
                            $cont=0;
                        }
                        @endphp
                        <div id="cambio3" class="form-group col-md-12 {{ $errors->has('procedimiento') ? ' has-error' : '' }} oculto">
                            <label for="id_procedimiento" class="col-md-2 control-label">@lang('agenda.procedimientos') </label>
                            <div class="col-md-10" style="margin-left: -5px;">
                                    <select id="id_procedimiento" class="form-control input-sm select2" name="procedimiento[]" onchange="especialidad();" multiple="multiple" data-placeholder="@lang('agenda.seleccione')"
                                    style="width: 95.5%;" autocomplete="off">
                                        @foreach($procedimiento as $procedimiento)
                                            <option  @for($x=0; $x<$cont; $x++) @if(old('procedimiento.'.$x)==$procedimiento->id) selected @endif @endfor value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                                        @endforeach
                                    </select>

                                @if ($errors->has('procedimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio11" class="form-group col-md-6 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                            <label for="tipo_cita" class="col-md-4 control-label">@lang('agenda.consecutivo')/@lang('agenda.primeravez')</label>
                            <div class="col-md-7">
                                <select id="cortesia" name="tipo_cita" class="form-control input-sm" >
                                    <option @if(old('tipo_cita')=="1"){{"selected"}}@endif value="1">@lang('agenda.consecutivo')</option>
                                    <option @if(old('tipo_cita')=="0"){{"selected"}}@endif value="0" >@lang('agenda.primeravez')</option>
                                </select>
                                @if ($errors->has('tipo_cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo_cita') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="div_tc" class="form-group col-md-6 has-warning">
                            <label for="teleconsulta" class="col-md-2 control-label" style="padding-left: 0;">@lang('agenda.teleconsulta')</label>
                            <div class="col-md-1" style="padding-left: 0;">
                                <input type="checkbox" id="tc" class="flat-yellow" name="tc" value="1"  @if(old('tc')=="1") checked @endif>
                            </div>
                            <div class="col-md-8" style="padding-left: 0;">

                                <input class="form-control input-sm" type="text" name="teleconsulta" id="teleconsulta" value="" placeholder="@lang('agenda.telefonoteleconsulta')" maxlength="45">
                                @if ($errors->has('teleconsulta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('teleconsulta') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $paciente_flag = false; $doctor_otro = null;

                            if($paciente != Array()){
                                if(!is_null($paciente->paciente_doctor)){
                                    if($paciente->paciente_doctor->id_usuario == $doctor->id){

                                        $paciente_flag = true;
                                    }else{
                                        $doctor_otro = Sis_medico\User::find($paciente->paciente_doctor->id_usuario);
                                    }
                                }
                            }


                        @endphp
                        <!--div id="cambio16" class="form-group col-md-6 {{ $errors->has('paciente_dr') ? ' has-error' : '' }}">
                            <label for="paciente_dr" class="col-md-5 control-label" style="color: green;padding-left: 0;">PACIENTE PARTICULAR DEL Dr.</label>
                            <input type="checkbox" id="paciente_dr" class="flat-green" name="paciente_dr" value="1"  @if(old('paciente_dr')=="1") checked @endif
                            @if($paciente_flag) checked @endif  @if($paciente != Array()) @if(!is_null($paciente->paciente_doctor)) disabled @endif @endif>
                            @if(!is_null($doctor_otro)) <span style="color: red;">Paciente del Dr(a). {{$doctor_otro->apellido1}} {{$doctor_otro->nombre1}}</span> @endif
                            @if ($errors->has('paciente_dr'))
                            <span class="help-block">
                                <strong>{{ $errors->first('paciente_dr') }}</strong>
                            </span>
                            @endif
                        </div-->


                        <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                            <label for="observaciones" id="titulo" class="col-md-2 control-label" >@lang('agenda.observaciones')</label>
                            <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                                <input id="observaciones" type="text" class="form-control input-sm" name="observaciones" value="{{old('observaciones')}}" >
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-12" id="obs_admin1">
                            <label for="observaciones_admin" id="titulo" class="col-md-2 control-label" >@lang('agenda.observacionesadministrativas')</label>
                            <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                                <input id="observaciones_admin" type="text" class="form-control input-sm" name="observaciones_admin" value="{{$observaciones_admin}}" >
                            </div>
                        </div>
                        <div class="form-group col-md-12" id="obs_admin1">
                            <label for="post_procedimiento" id="titulo" class="col-md-2 control-label" >Post Procedimiento</label>
                            <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                                <input type="checkbox" id="post_procedimiento" class="flat-green" name="post_procedimiento">
                            </div>
                        </div>
                        <!--Adelantado-->
                        <div id="idad" class="form-group col-md-3 {{ $errors->has('adelantado') ? ' has-error' : '' }} oculto">
                            <label for="adelantado" class="col-md-9 control-label" style="color: red;padding-left: 2;">@lang('agenda.adelantada')</label>
                            <input type="checkbox" id="adelantado" class="checkVal" name="adelantado" style="width: 20px;height: 20px" onchange="valida_check(event);"  value="1"
                            @if(old('adelantado')=="1")
                              checked
                            @endif>
                            @if ($errors->has('adelantado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('adelantado') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div  class="form-group col-md-10">
                            <label for="ocul" class="col-md-5 control-label"></label>
                            <div class="col-md-7">
                            </div>
                        </div>


                     <!--Fecha de Validación-->
                     <div id="fecha_validacion1" class="form-group col-md-6{{ $errors->has('fecha_val') ? ' has-error' : '' }} oculto">
                            <label for="fecha_val" class="col-md-4 control-label">@lang('agenda.fechavalidacion')</label>
                            <div class="input-group date col-md-7">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_val" type="text" class="form-control input-sm" name="fecha_val" value="@if($paciente != Array()) @if(!is_null($paciente->fecha_val)){{$paciente->fecha_val}}@endif @endif">
                            </div>
                            @if ($errors->has('fecha_val'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_val') }}</strong>
                            </span>
                            @endif
                        </div>

                            <!--Código de validación-->
                            <div id="cod_validacion1" class="form-group col-md-3{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                                <label for="cod_val" class="col-md-4 control-label" style="padding-left: 0;">@lang('agenda.codigovalidacion')</label>

                                <div class="col-md-7" style="padding-left: 5px;padding-right: 0;">
                                    <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="@if($paciente != Array()) @if(!is_null($paciente->cod_val)){{$paciente->cod_val}}@endif @endif">

                                    @if ($errors->has('cod_val'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cod_val') }}</strong>
                                        </span>
                                    @endif
                                </div>


                            </div>
                            <div id="cod_validacion2" class="form-group col-md-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                                <label for="validacion_cv_msp" class="col-md-3 control-label" style="padding-left: 0;">@lang('agenda.codigovalidacion')</label>

                                <div class="col-md-2">
                                    <input id="validacion_cv_msp" type="validacion_cv_msp" class="form-control" name="validacion_cv_msp" value="@if($paciente != Array()) @if(!is_null($paciente->validacion_cv_msp)){{$paciente->validacion_cv_msp}}@endif @endif" >

                                    @if ($errors->has('validacion_cv_msp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('validacion_cv_msp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <input id="validacion_nc_msp" type="validacion_nc_msp" class="form-control" name="validacion_nc_msp" value="@if($paciente != Array()) @if(!is_null($paciente->validacion_nc_msp)){{$paciente->validacion_nc_msp}}@endif @endif" >

                                    @if ($errors->has('validacion_nc_msp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('validacion_nc_msp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <input id="validacion_sec_msp" type="validacion_sec_msp" class="form-control" name="validacion_sec_msp" value="@if($paciente != Array()) @if(!is_null($paciente->validacion_sec_msp)){{$paciente->validacion_sec_msp}}@endif @endif"  >

                                    @if ($errors->has('validacion_sec_msp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('validacion_sec_msp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>



                        <div id="tipo_clase" class="form-group col-md-6 {{ $errors->has('clase') ? ' has-error' : '' }} oculto">

                            <label for="clase" class="col-md-4 control-label">@lang('agenda.tipodereunion')</label>
                            <div class="col-md-7">
                                <select id="clase" name="clase" class="form-control input-sm" >
                                    <option  value="">@lang('agenda.seleccione')..</option>
                                    <option @if(old('clase')=="Reuniones"){{"selected"}}@endif value="Reuniones">@lang('agenda.reuniones')</option>
                                    <option @if(old('clase')=="Vacaciones"){{"selected"}}@endif value="Vacaciones" >@lang('agenda.vacaciones')</option>
                                    <option @if(old('clase')=="Eventos"){{"selected"}}@endif value="Eventos" >@lang('agenda.eventos')</option>
                                    <option @if(old('clase')=="Cursos"){{"selected"}}@endif value="Cursos" >@lang('agenda.cursos')</option>
                                    <option @if(old('clase')=="Otros"){{"selected"}}@endif value="Otros" >@lang('agenda.otros')</option>
                                </select>
                                @if ($errors->has('clase'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('clase') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($i != 0)
                        <div id="div_hc1" class="form-group col-md-12 {{ $errors->has('archivo') ? ' has-error' : '' }}">

                                <label for="archivo" class="col-md-2 control-label" >@lang('agenda.seleccionehistorialmedico')</label>
                                <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                                <input class="form-control input-sm" id="archivo" type="file"  name="archivo" value="{{old('archivo')}}" >
                                @if ($errors->has('archivo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('archivo') }}</strong>
                                </span>
                                @endif
                                </div>
                        </div>

                        <div id="div_hc2" class="form-group col-md-12 {{ $errors->has('hc') ? ' has-error' : '' }}">
                            <label for="hc" class="col-md-2 control-label">@lang('agenda.copieHistorialMedico')</label>
                            <div class="col-md-10" style="padding-left: 10px;">

                                <textarea name="hc" id="hc" rows="10" cols="50" style="width: 92%;"></textarea>
                                @if ($errors->has('hc'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('hc') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button type="button" id="enviar_dato" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> @lang('agenda.agendar')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@php

$doctor_todo = Sis_medico\Doctor_Tiempo::where('id_doctor',$doctor->id)->first();
if(!is_null($doctor_todo)){
    $minutos = $doctor_todo->tiempo * 10;
}
//echo $minutos;
@endphp

</section>




<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>


    $(document).ready(function()
    {

        $('#dato_confirmacion').click(function(){
            $('#confirmar').modal('hide');
            $('#target').submit();
        });
        @if($errors->any())
            $('#enviar_dato').removeAttr('disabled');
        @endif

            //Muestra Fecha de Validacion  y Codigo de Validacion dependiendo del Seguro
            //Seleccionado

            var id_seguro = document.getElementById("id_seguro").value;

            if (id_seguro == '3') {//issfa
                $("#fecha_validacion1").removeClass("oculto");
                $('#cod_validacion1').removeClass("oculto");
                $("#fecha_val").prop("required",true);
                $('#cod_validacion1').prop("required", true);
                $("#cod_validacion2").addClass("oculto");//codigo val msp
                $('#cod_validacion2').removeAttr("required");
                $("#idad").removeClass('oculto');
                $('#idad').prop("required", true);
                //$('#fecha_val').val(" ");
                //$('#cod_val').val(" ");
                $('#validacion_cv_msp').val(" ");
                $('#validacion_nc_msp').val(" ");
                $('#validacion_sec_msp').val(" ");
                $('#adelantado').prop('checked', false);
            }else if(id_seguro == '5'){//Msp
                $("#fecha_validacion1").removeClass("oculto");
                $("#cod_validacion2").removeClass("oculto");//Cod val Msp
                $('#fecha_validacion1').prop("required", true);
                $('#cod_validacion2').prop("required", true);
                $("#idad").removeClass('oculto');
                //$('#adelantado').prop("required", true);
                $('#cod_validacion1').addClass("oculto");//Cod val issfa y isspol
                $('#cod_validacion1').removeAttr("required");
                //$('#fecha_val').val(" ");
                //$('#cod_val').val(" ");
                //$('#validacion_cv_msp').val(" ");
                //$('#validacion_nc_msp').val(" ");
                //$('#validacion_sec_msp').val(" ");
                $('#adelantado').prop('checked', false);
            }else if(id_seguro == '6'){//isspol
                $("#fecha_validacion1").removeClass("oculto");
                $('#cod_validacion1').removeClass("oculto");
                $('#fecha_validacion1').prop("required", true);
                $('#cod_validacion1').prop("required", true);
                $("#cod_validacion2").addClass("oculto");
                $('#cod_validacion2').removeAttr("required");
                $("#idad").removeClass('oculto');
                //$('#adelantado').prop("required", true);
                //$('#fecha_val').val(" ");
                //$('#cod_val').val(" ");
                $('#validacion_cv_msp').val(" ");
                $('#validacion_nc_msp').val(" ");
                $('#validacion_sec_msp').val(" ");
                $('#adelantado').prop('checked', false);
            }else{//Otro tipo de Seguro menos isspol-issfa-msp
                $("#fecha_validacion1").addClass("oculto");
                $("#cod_validacion1").addClass("oculto");
                $("#cod_validacion2").addClass("oculto");//Cod val Msp
                $("#idad").addClass("oculto");
                $('#fecha_validacion1').removeAttr("required");
                $('#cod_validacion1').removeAttr("required");
                $('#cod_validacion2').removeAttr("required");
                $('#idad').removeAttr("required");
                $('#fecha_val').val(" ");
                $('#cod_val').val(" ");
                $('#validacion_cv_msp').val(" ");
                $('#validacion_nc_msp').val(" ");
                $('#validacion_sec_msp').val(" ");
                $('#adelantado').prop('checked', false);
            }


        $('#enviar_dato').click(function(){
            $(this).attr('disabled', 'disabled');


            var tipo_horario = document.getElementById('tipo_horario').value - 1;
            var proc_consul = $( "#proc_consul" ).val();
            $("#target").validate({
                rules: {
                    id_paciente : "required",
                    id_sala : "required",
                    id_seguro : "required",
                    proc_consul : "required",
                    fecha_nacimiento : "required",


                },
                messages : {
                   id_paciente :`{{trans('agenda.debeIngresarNumeroCedula')}}`,
                   id_sala : `{{trans('agenda.debeIngresarUbicacion')}}`,
                   id_seguro : `{{trans('agenda.debesingresarelseguro')}}`,
                   proc_consul : `{{trans('agenda.debeIngresarTipoAgendamiento')}}`,
                   fecha_nacimiento : `{{trans('agenda.ingreseFechaNacimiento')}}`,

                },
                onfocusout: false,
                onkeyup: false,
                onclick: false,
                highlight: function(element) {
                    var datos_3 = $(element).parent();
                    datos_3.parent().addClass('has-error');
                    $('#enviar_dato').removeAttr('disabled');

                },
            });


            if(tipo_horario == '-1' || tipo_horario == '-2')
            {
                $('#target').submit();
            }
            else if(tipo_horario == proc_consul)
            {
                $('#target').submit();
            }
            else if(tipo_horario == '2')
            {

                $('#target').submit();
            }
            else{

                $('#confirmar').modal('show');
            }



        });


        var especialidad = document.getElementById("espid").value;

        @foreach ($especialidad as $value)
            if(especialidad == "{{$value->espid}}"){
                $("#especialidad_nombre").val("{{$value->enombre}}");
            }
        @endforeach

        $('.select2').select2({
            tags: false
        });
        var valor = document.getElementById("proc_consul").value;
        var valor_proc = new Array();
            $(".select2").on("change", function(e) {
            valor_proc.push(document.getElementById("id_procedimiento").value);



            var nombre_especialidad = 0;
            <?php foreach ($procedimiento2 as $value) {?>
                if(valor == {{ $value->id }}){
                    nombre_especialidad = "{{$value->nombre}} +"+nombre_especialidad;
                }
            <?php }?>

                $("#pronombre").val('entra');
            });
            $("select").on("select2:select", function (evt) {
                var element = evt.params.data.element;

                var $element = $(element);

                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });

            @if($paciente != Array())
        var js_cedula = document.getElementById("idpaciente").value;
        if(js_cedula!=''){

            $("#proc_consul").focus();
        }
            @endif





        $('#favoritesModal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
        });

        $('#BuscaPacxNombre').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

        edad2();

        campos();
    });

    $('#paciente_dr').on('ifChecked', function(event){

        @if($paciente != Array())

            alert(`{{trans('agenda.confirmePacienteParticular')}}`);

        @endif

    });




</script>
<script>
  tinymce.init({
    selector: '#hc'
  });

  $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

  $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass   : 'iradio_flat-red'
    })

  $('input[type="checkbox"].flat-yellow').iCheck({
      checkboxClass: 'icheckbox_flat-yellow',
      radioClass   : 'iradio_flat-yellow'
    })
  </script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">



    $(function () {
        ingreso();
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',
            minDate: '{{$hora}}',
             //Important! See issue #1075

        });
        $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075

        });
        $('#fecha_val').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
        });
        $("#inicio").on("dp.change", function (e) {
            $('#fin').data("DateTimePicker").minDate(e.date);
            incremento(e);
        });
        $("#fin").on("dp.change", function (e) {
            //$('#inicio').data("DateTimePicker").maxDate(e.date);
        });
        $("#fecha_val").on("dp.change", function(e) {
        valida_fval(e);
        });

    });
</script>
<script type="text/javascript">

    function veractivas(e, suspendidas)
    {

        var boton1 =document.getElementsByClassName("suspendidas");
        if(suspendidas == 0){
            $(boton1).text('Ver Activas');
            var suspendidas=1;
            //alert(suspendidas);
        }
        if(suspendidas == 1){
            $(boton1).text('Ver Suspendidas');
            var suspendidas=0;
            //alert(suspendidas);
        }

    }

    function valida_fval(e)
    {
       //console.log("safds");
        var fecha_val = document.getElementById("fecha_val").value;
        var anio = fecha_val.substr(0,4);
        var mes = fecha_val.substr(5,2);
        var dia = fecha_val.substr(8,2);

        var mes_2 = parseInt(mes)-1;

        var fecha_nueva = new Date(anio,mes_2, dia);
        var fecha = new Date();

        var isChecked = document. getElementById('adelantado'). checked;

        if(isChecked == false){

           if(fecha_nueva <= fecha){

            alert(`{{trans('agenda.lafechavalidacionnodebesermenorfechaactual')}}`)
            $('#fecha_val').val("");

           }

        }

    }


        function valida_check(e)
        {

            var isChecked = document. getElementById('adelantado'). checked;

            if(isChecked == false){

                $('#fecha_val').val("");


            }


    }


    //Valida Cambio de Seguro Seleccionado
    function valida_seguro(e){

        var id_seguro = document.getElementById("id_seguro").value;

        if(id_seguro == '3'){//issfa
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $("#fecha_val").prop("required",true);
            $('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");//codigo val msp
            $('#cod_validacion2').removeAttr("required");
            $("#idad").removeClass('oculto');
            $('#idad').prop("required", true);
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }else if(id_seguro == '5'){//Msp
            $("#fecha_validacion1").removeClass("oculto");
            $("#cod_validacion2").removeClass("oculto");//Cod val Msp
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion2').prop("required", true);
            $("#idad").removeClass('oculto');
           // $('#adelantado').prop("required", true);
            $('#cod_validacion1').addClass("oculto");//Cod val issfa y isspol
            $('#cod_validacion1').removeAttr("required");
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }else if(id_seguro == '6'){//isspol
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
            $('#cod_validacion2').removeAttr("required");
            $("#idad").removeClass('oculto');
           // $('#adelantado').prop("required", true);
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }else{//Otro tipo de Seguro menos isspol-issfa-msp
            $("#fecha_validacion1").addClass("oculto");
            $("#cod_validacion1").addClass("oculto");
            $("#cod_validacion2").addClass("oculto");//Cod val Msp
            $("#idad").addClass("oculto");
            $('#fecha_validacion1').removeAttr("required");
            $('#cod_validacion1').removeAttr("required");
            $('#cod_validacion2').removeAttr("required");
            $('#idad').removeAttr("required");
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }

    }


    function teclaEnter2(e)
    {
        vcedula = document.getElementById("idpaciente").value;
            //pulsar boton
            //location.href ="{{ route('agenda.paciente2', ['id' => $id])}}/"+document.getElementById("id").value;
            vcedula =  vcedula.trim();
            if (vcedula != ""){
                location.href ="{{ route('agenda.nuevo', ['id' => $id])}}/{{$unix}}/"+vcedula;

            }

    }

    function campos()
    {
        var valor = document.getElementById("proc_consul").value;
        var elemento1 = document.getElementById("cambio1");
        var elemento2 = document.getElementById("cambio2");
        var elemento3 = document.getElementById("cambio3");
        var elemento4 = document.getElementById("cambio4");
        var elemento5 = document.getElementById("cambio5");
        var elemento6 = document.getElementById("cambio6");
        var elemento7 = document.getElementById("cambio7");
        var elemento8 = document.getElementById("cambio8");
        var elemento11 = document.getElementById("cambio11");
        var iempresa = document.getElementById("iempresa");
        var ivip = document.getElementById("ivip");
        var elemento17 = document.getElementById("cambio17");
        var elemento13 = document.getElementById("cambio13");
        var elemento14 = document.getElementById("cambio14");
        var elemento15 = document.getElementById("cambio15");
        var elemento15a = document.getElementById("cambio15a");
        var elemento15b = document.getElementById("cambio15b");
        var elemento16 = document.getElementById("cambio16");
        var elemento18 = document.getElementById("obs_admin1");
        var teleconsulta = document.getElementById("div_tc");
        var inicio = document.getElementById("inicio").value;
        var div_hc1 = document.getElementById("div_hc1");
        var div_hc2 = document.getElementById("div_hc2");


        if(valor == 0){
            $(div_hc1).removeClass('oculto');
            $(div_hc2).removeClass('oculto');
            $(elemento1).addClass('oculto');
            $(elemento2).addClass('oculto');
            $(elemento3).addClass('oculto');
            $(elemento4).removeClass('oculto');
            $(teleconsulta).removeClass('oculto');
            $(elemento5).removeClass('oculto');
            $(elemento6).removeClass('oculto');
            $(elemento7).removeClass('oculto');
            $(elemento8).removeClass('oculto');
            $(elemento11).removeClass('oculto');
            $(iempresa).removeClass('oculto');
            @if($doctor->id == '1307189140')
                $(ivip).removeClass('oculto');
            @endif

            $(elemento13).removeClass('oculto');
            $(elemento14).removeClass('oculto');
            $("#tipo_clase").addClass('oculto');
            $("#clase").removeAttr("required");
            $(elemento15).removeClass('oculto');
            $(elemento15a).removeClass('oculto');
            $(elemento15b).removeClass('oculto');
            $(elemento16).removeClass('oculto');
            $(elemento17).removeClass('oculto');
            $(elemento18).removeClass('oculto');
            document.getElementById('titulo').innerHTML = 'Observaciones';
            $("#fecha_nacimiento").attr("required","yes");

            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            @if(!is_null($doctor_todo))
                @if($doctor->id== $doctor_todo->id_doctor)
                var fin = moment(inicio).add({{$minutos}}, 'm').format('YYYY/MM/DD HH:mm');
                @endif
            @endif

            $("#id_procedimiento").removeAttr("required");
            $("#fin").val(fin);
        }

        if(valor == 1){
            $(div_hc1).removeClass('oculto');
            $(div_hc2).removeClass('oculto');
            $(elemento1).removeClass('oculto');
            $(teleconsulta).addClass('oculto');
            $(elemento2).removeClass('oculto');
            $(elemento3).removeClass('oculto');
            //$("#id_procedimiento").attr("required","yes");
            $(elemento4).removeClass('oculto');
            $(elemento5).removeClass('oculto');
            $(elemento6).removeClass('oculto');
            $(elemento7).removeClass('oculto');
            $(elemento8).removeClass('oculto');
            $(elemento11).removeClass('oculto');
            $(iempresa).removeClass('oculto');
            @if($doctor->id == '1307189140')
                $(ivip).removeClass('oculto');
            @endif

            $("#tipo_clase").addClass('oculto');
            $("#clase").removeAttr("required");
            var fin = moment(inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
            $("#fin").val(fin);
            $(elemento13).removeClass('oculto');
            $(elemento14).removeClass('oculto');
            $(elemento15).removeClass('oculto');
            $(elemento15a).removeClass('oculto');
            $(elemento15b).removeClass('oculto');
            $(elemento16).removeClass('oculto');
            $(elemento17).removeClass('oculto');
            $(elemento18).removeClass('oculto');
            document.getElementById('titulo').innerHTML = 'Observaciones';
            $("#fecha_nacimiento").attr("required","yes");
        }
        if(valor == 2){
            console.log(div_hc2);
            $(div_hc1).addClass('oculto');
            $(div_hc2).addClass('oculto');
            $(teleconsulta).addClass('oculto');
            $(elemento1).addClass('oculto');
            $(elemento2).addClass('oculto');
            $(elemento3).addClass('oculto');
            $(elemento4).addClass('oculto');
            $(elemento5).addClass('oculto');
            $(elemento6).addClass('oculto');
            $(elemento7).addClass('oculto');
            $(elemento8).addClass('oculto');
            $(elemento11).addClass('oculto');
            $(iempresa).addClass('oculto');
            $(ivip).addClass('oculto');
            $("#id_procedimiento").removeAttr("required");

            $(elemento13).addClass('oculto');
            $(elemento14).addClass('oculto');
            $(elemento15).addClass('oculto');
            $(elemento15a).addClass('oculto');
            $(elemento15b).addClass('oculto');
            $(elemento16).addClass('oculto');
            $(elemento17).addClass('oculto');

            $(elemento18).addClass('oculto');
            $("#tipo_clase").removeClass('oculto');
            $("#clase").attr("required","yes");
            document.getElementById('titulo').innerHTML = 'Titulo';
            $("#fecha_nacimiento").removeAttr("required");
        }
        crear_select();


    }

function ingreso(){

    var js_hospital = document.getElementById('est_amb_hos').value;
    //alert(js_hospital);
    if(js_hospital=='1'){
        $("#div_omni").removeClass('oculto');
    }else{
        $('#div_omni').addClass('oculto');
    }


}


function especialidad()
{

    var valor = document.getElementById("espid").value;

    @foreach ($especialidad as $value)
        if(valor == {{ $value->espid }}){
            $("#espenombre").val('{{ $value->enombre}}')
        }
    @endforeach

}

function salas()
{
    var valor = document.getElementById("id_sala").value;
    @foreach ($salas as $value)
        if(valor == {{ $value->id }}){
            $("#connombre").val('{{ $value->nombre_sala}}')
            $("#hosnombre").val('{{$value->nombre_hospital}}')
            $("#hosdireccion").val('{{$value->direccion_hospital}}')
        }

    @endforeach
}

function incremento (e){
        var fjs_inicio = document.getElementById("inicio").value;
        var fjs_valor = document.getElementById("proc_consul").value;



        if(fjs_valor == 0 || fjs_valor == ''){

        var fjs_fin = moment(fjs_inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
        @if(!is_null($doctor_todo))
            @if($doctor->id== $doctor_todo->id_doctor)
                var fjs_fin = moment(fjs_inicio).add({{$minutos}}, 'm').format('YYYY/MM/DD HH:mm');
            @endif
        @endif

            $("#fin").val(fjs_fin);
         }
        if(fjs_valor == 1){

            var fjs_fin = moment(fjs_inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');

            $("#fin").val(fjs_fin);
         }
    }

$(document).ready(function($){

    $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> @lang('agenda.agenda')</a></li>');
    $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda', ['id' => $doctor->id, 'i' => $doctor->id]) }}"></i> @lang('agenda.doctor')</a></li>');
    $(".breadcrumb").append('<li class="active">@lang('agenda.agregar')</li>');


    var ventana_ancho = $(window).width();


    if(ventana_ancho > "962" ){
        var nuevovalor = ventana_ancho * 0.8;
    }
    else
    {
        var nuevovalor = ventana_ancho * 0.9;
    }
    $("#frame_ventana").width(nuevovalor);

});

    function crear_select () {
        console.log({{old('id_sala')}});
        var proc = document.getElementById("proc_consul").value;
        $('option[class^="cl_ub"]').remove();
        //alert(proc);
        if(proc!=""){
            var sel_ub = document.getElementById("id_sala");
            @foreach($salas as $sala)
            if(proc=='0'){
                @if($sala->proc_consul_sala=='0' || $sala->proc_consul_sala=='-1')
                var option = document.createElement("option");
                option.value = "{{$sala->id}}";
                option.text = "{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}";
                option.setAttribute("class", "cl_ub");
                sel_ub.add(option);
                @endif
                @if($sala->id == old('id_sala'))
                    option.setAttribute("selected","true");
                @endif
            }else if(proc=='1'){
                @if($sala->proc_consul_sala=='1' || $sala->proc_consul_sala=='-1')
                var option = document.createElement("option");
                option.value = "{{$sala->id}}";
                option.text = "{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}";
                option.setAttribute("class", "cl_ub");
                sel_ub.add(option);
                @endif
                @if($sala->id == old('id_sala'))
                    option.setAttribute("selected","true");
                @endif
            }else{
                var option = document.createElement("option");
                option.value = "{{$sala->id}}";
                option.text = "{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}";
                option.setAttribute("class", "cl_ub");
                sel_ub.add(option);
                @if($sala->id == old('id_sala'))
                    option.setAttribute("selected","true");
                @endif
            }
            @endforeach

        }

    }

</script>

@endsection
