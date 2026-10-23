
@extends('agenda.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">
    @foreach($cagenda2 as $value)
        .a{{$value->id}}
    {
        color: #023f84;

    }
    @endforeach

    @foreach($cagenda as $value)
        .a{{$value->id}}
    {
    @if($value->estado_cita == 0)
        color: black;
    @else
        color: {{ $value->color}};
    @endif
    }
    @endforeach

    @foreach($cagenda3 as $value)
    .a{{$value->id}}
    {
    @if($value->estado_cita == 0)
        color: black;
    @else
        color: {{ $value->color}};
    @endif
    }

    @endforeach

    td{
        padding: 5px !important;
    }
    .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .agbtn{
        width: 100%;
    }
    .desabilitar{
        pointer-events: none;
        cursor:no-drop;
    }
    .fincita{
        background-color: #dc3545;
        /*background-color: #ef3838;*/
        padding: 10px 20px;
        margin-top: 15px;
        margin-left: 180px;
        text-align: center;
        font-weight: bold;
        font-family: 'Helvetica general3';
        font-size: 15px;
        border-radius: 10px;
        color: white;
        display: block;
    }
</style>

<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        </div>
    </div>
</div>

<div class="modal fade" id="modalconsentimiento" data-backdrop="static"  role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="modalpreparacion" data-backdrop="static"  role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!--div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coberturasalud.msp.gob.ec/" ></iframe>
    </div>
  </div>
</div-->

<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>
<div class="modal fade" id="cert_med" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>
<!--Modal Recibo de Cobro-->
<div class="modal fade" id="modal_recibo_cobro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="documentos_pdf" data-backdrop="static" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<div class="modal fade" id="preparaciones_procedimientos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >


    <div class="row">
        <div class="col-md-12" style="padding-right: 4px;">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding-bottom: 0px;padding-top: 0;">
                    <div class="col-md-6">
                        <h4>@lang('agenda.actualiza') @if($agenda->proc_consul=='0') @lang('agenda.consulta-') @elseif($agenda->proc_consul=='1') @lang('agenda.procedimiento-') @else{{'Reuniones'}}@endif @lang('agenda.delpaciente'): </h4>
                    </div>
                    <div class="col-md-6"><h4 style="color: red;"> {{$agenda->id_paciente}} - {{  $agenda->pnombre1}} @if($agenda->pnombre2 != '(N/A)' ){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != '(N/A)'){{ $agenda->papellido2}}@endif</h4>
                    </div>
                    <!--cambiar a produ 7/11/2017-->
                </div>

            </div>
        </div>
        <div class="col-md-7" style="padding-right: 4px;">
            <div class="box box-primary">
                <div class="box-header with-border" >
                    <!--cambiar a produ 7/11/2017-->
                    @php
                        $doctor=Sis_medico\User::find($agenda->id_doctor1);
                        $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                        $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                        $paciente=Sis_medico\Paciente::find($agenda->id_paciente);
                        $user_aso=Sis_medico\User::find($paciente->id_usuario);
                        $alergia = $paciente->alergias;
                        $tipo_usuario  = Auth::user()->id_tipo_usuario;
                        
                        if($alergia == null || $alergia == "No" || $alergia == "" || $alergia == "NO" || $alergia == "no" || $alergia == "nO" || $alergia == "NO REFIERE"){
                            $dato_alergia =  1;
                        }
                        else
                        {
                            $dato_alergia =  2;
                        }

                        $doctor_otro = null;

                        if(!is_null($paciente->paciente_doctor)){
                            if($paciente->paciente_doctor->id_usuario == $agenda->id_doctor1){


                            }else{
                                $doctor_otro = Sis_medico\User::find($paciente->paciente_doctor->id_usuario);
                            }
                        }

                    @endphp

                    <!--div class="col-md-3 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a  data-toggle="modal" data-target="#favoritesModal2" >
                            <button type="button" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-globe"></span> @lang('agenda.cPublica')</button>
                        </a>
                    </div-->
                    @if($agenda->id_doctor1!='4444444444')
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn btn-warning btn-xs agbtn" href="https://coberturasalud.msp.gob.ec/" target="_blank"><span class="glyphicon glyphicon-globe"> </span> @lang('agenda.cPublica')</a>
                        </div>
                        @if(!$historia->isEmpty())
                            @if($agenda->proc_consul == '1')
                            <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                                <a class="btn btn-primary btn-xs agbtn"  id="imprimir_etiquetas" target="_blank" href="{{ route('admision.etiqueta2', ['id' => $agenda->id, 'seguro' => $historia[0]->id_seguro, 'alergia' => $dato_alergia]) }}" ><span class="glyphicon glyphicon-print"></span> @lang('agenda.etiqueta')</a>
                            </div>
                            @endif
                            @if($cantidad_doc)
                                <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                                <!--form class="col-md-6 col-sm-6 col-xs-6" role="form" method="POST" action="{{url('controldoc/admision')}}/{{$historia[0]->hcid}}"-->
                                    <form role="form" method="POST" action="{{route('controldoc.control_doc')}}" style="padding-left: 0;">
                                        <input type="hidden" name="hcid" value="{{$historia[0]->hcid}}">
                                        <input type="hidden" name="url_doctor" value="{{$url_doctor}}">
                                        <input type="hidden" id="unix2" name="unix" value="">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <button type="submit" id="enviar2" class="btn btn-info btn-xs agbtn" ><span class="glyphicon glyphicon-level-up"></span> @lang('agenda.documentos')</button>
                                    </form>
                                </div>
                            @endif
                        @endif
                        @php
                            if($agenda->proc_consul=='0')
                            {
                                $pendiente_prepa=true;
                                if(!$historia->isEmpty()){

                                    if($historia['0']->presion <> 0 && $historia['0']->pulso <> 0 && $historia['0']->temperatura <> 0 && $historia['0']->altura <> 0 && $historia['0']->peso <> 0)
                                    {
                                        $pendiente_prepa=false;
                                    }
                                }
                            }

                        @endphp
                    @if($agenda->id_doctor1!='4444444444')
                        @if($agenda->proc_consul=='0')
                            @if(!$historia->isEmpty())
                            <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                                <a class="btn btn-primary btn-xs agbtn" href="{{route('preparacion.mostrar',['id' => $agenda->id, 'url_doctor' => $url_doctor])}}" @if(!$pendiente_prepa)class="btn btn-primary" @else class="btn btn-danger btn-xs agbtn" @endif ></span> @lang('agenda.preparacion_')</a>
                            </div>
                            @endif
                        @endif
                        @endif
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn btn-primary btn-xs agbtn"  id="examenes_externos"  href="{{ route('laboratorio.externo', ['id_paciente' => $agenda->id_paciente]) }}" ><span class="ionicons ion-ios-flask"></span> @lang('agenda.examenesexternos')</a>

                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">

                            <a class="btn btn-success btn-xs agbtn"  id="examenes_externos"  href="{{ route('ingreso.biopsias2', ['id_paciente' => $agenda->id_paciente]) }}" > @lang('agenda.biopsias')</a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            @php
                                $orden = Sis_medico\Ct_Orden_Venta::where('id_agenda', $agenda->id)->where('estado', '<>', '0')->first();
                            @endphp
                            @if(is_null($orden))
                                
                                
                                <a class="btn bg-purple btn-xs agbtn"  id="examenes_externos"  href="{{ route('nuevorecibocobro.crear', ['id' => $agenda->id]) }}" > @lang('agenda.recibocobro') N</a>
                               
                            @else
                                <a target="_blank" class="btn bg-purple btn-xs agbtn"  id="examenes_externos"  href="{{ route('facturacion.modal_recibo', ['id_orden' => $orden->id]) }}" data-toggle="modal" data-target="#modal_recibo_cobro"> @lang('agenda.recibocobro')</a>
                                <!--La que estaba -->
                                <!--<a target="_blank" class="btn btn-primary btn-sm"  id="examenes_externos"  href="{{ route('facturacion.imprimir_ride', ['id_orden' => $orden->id]) }}" > @lang('agenda.recibocobro')</a>-->
                            @endif
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn bg-orange btn-xs agbtn" href="{{ route('paciente.historial_ordenes', ['id' => $agenda->id_paciente])}}" target="_blank"> @lang('agenda.ordenProcedimiento')</a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn btn-info btn-xs agbtn" href="{{ route('paciente.historial_orden_biopsias', ['id' => $agenda->id_paciente])}}"> @lang('agenda.ordenesdebiopsias')</a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn bg-red btn-xs agbtn" data-remote="{{ route('preparaciones.index')}}"
                        class="btn btn-info btn-sm" data-toggle="modal" data-target="#preparaciones_procedimientos"> @lang('agenda.preparaciones')</a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            @if(is_null($agenda->id_empresa))
                            @php $id_empresa = '0992704152001';  @endphp
                            <a class="btn bg-navy btn-xs agbtn" data-remote="{{ route('documentos_pdf',['id'=>$agenda->id_paciente,'empresa'=>$id_empresa, 'id_agenda'=>$agenda->id])}}"
                      class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalconsentimiento"> @lang('agenda.consentimiento')</a>
                            @elseif(!is_null($agenda->id_empresa))
                                <a class="btn bg-navy btn-xs agbtn" data-remote="{{ route('documentos_pdf',['id'=>$agenda->id_paciente,'empresa'=>$agenda->id_empresa, 'id_agenda'=>$agenda->id])}}"
                                class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalconsentimiento"> @lang('agenda.consentimiento')</a>
                            @endif
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                            <a class="btn bg-maroon btn-xs agbtn" href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"> @lang('agenda.laboratorio') Labs </a>
                        </div>
                    @endif
                    @php 
                        $proforma = Sis_medico\Proforma_Cabecera::where('id_paciente',$paciente->id)->where('estado', 1)->get();
                    @endphp
                    @if(count($proforma) > 0)
                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn bg-maroon btn-xs agbtn" data-remote="{{route('comercial.proforma.proformaModal',['id_paciente' => $agenda->id_paciente])}}" data-toggle="modal" data-target="#tarifario_nivel">@lang('agenda.seleccionarproforma')</a>
                    </div>
                    @endif


                </div>

                <div class="box-body">

                    <form class="form-vertical" role="form" method="POST" action="{{ route('agenda.update2', ['id' => $agenda->id, 'url_doctor' => $url_doctor]) }}" enctype="multipart/form-data" id="form_agenda">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="fecha" value="{{date('Y-m-d H:i:s')}}">
                        <input type="hidden" id="id_paciente" name="id_paciente" value="{{$agenda->id_paciente}}">
                        <input type="hidden" id="unix" name="unix" value="">
                        <input type="hidden" id="ruta" name="ruta" value="{{$ruta}}">
                        <textarea name="hc" id="hc2" rows="10"  cols="50" style="width: 100%; display:none;" @if($agenda->estado_cita>='4')readonly @endif>@if(!is_null($ar_historiatxt)){{$ar_historiatxt->texto}}@endif</textarea>
                        @if(!is_null($ar_historiatxt))
                            <input type="hidden" name="id_ag_artxt" id="id_ag_artxt" value="{{$ar_historiatxt->id}}">
                        @endif

                        <!--proc_consul-->
                        <input id="proc_consul" type="hidden" class="form-control" name="proc_consul" value="{{$agenda->proc_consul}}" >

                        @if($agenda->estado_cita!='4')
                            @if($agenda->proc_consul=='1')
                                <!--id_procedimiento-->
                                <div class="form-group col-md-12 {{ $errors->has('id_procedimiento') ? ' has-error' : '' }}" style="padding: 0;margin-bottom: 2px;">
                                    <label for="id_procedimiento" class="col-md-12 control-label">@lang('agenda.procedimientos')</label>
                                    <div class="col-md-12">
                                        <select class="form-control select2 input-sm" multiple="multiple" name="proc[]" data-placeholder="@lang('agenda.seleccioneProcedimientos')" required style="width: 100%;">
                                            @if($agenda->id_procedimiento!=null)
                                            <option selected value="{{$agenda->id_procedimiento}}">{{$procedimientos->find($agenda->id_procedimiento)->nombre}}</option>
                                            @endif
                                            @foreach($agendaprocedimientos as $agendaproc)
                                            <option selected value="{{$agendaproc->id_procedimiento}}">{{$procedimientos->find($agendaproc->id_procedimiento)->nombre}}</option>
                                            @endforeach
                                            @foreach($procedimientos as $procedimiento)
                                            @if($agenda->id_procedimiento!=$procedimiento->id)
                                            @if(is_null($agendaprocedimientos->where('id_procedimiento',$procedimiento->id)->first()))
                                            <option @if($agenda->id_procedimiento==$procedimiento->id) selected @endif @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) @if($agendaproc->id_procedimiento==$procedimiento->id) selected @endif @endforeach @endif value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                                            @endif
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <!--especialidad-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-4 {{ $errors->has('espid') ? ' has-error' : '' }}" >
                                <label for="espid" class="col-md-12 control-label">@lang('agenda.especialidad')</label>
                                <div class="col-md-12">
                                    <select id="espid" name="espid" class="form-control input-sm" required>
                                        @foreach ($especialidades as $especialidad)
                                            <option  @if(old('espid')==$especialidad->espid){{"selected"}} @elseif($agenda->espid==$especialidad->espid){{"selected"}} @endif value="{{$especialidad->espid}}">{{$especialidad->enombre}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('espid'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('espid') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!--seguro-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}" >
                                <label for="id_seguro" class="col-md-12 control-label">@lang('agenda.seguro')</label>
                                <div class="col-md-12">
                                    <select id="id_seguro" name="id_seguro" class="form-control input-sm" required onchange="valida_seguro(this.value), valida_seguros(event);">
                                        @foreach ($seguros as $seguro)
                                            <option  @if(old('id_seguro')!='') @if(old('id_seguro')==$seguro->id) selected @endif @else @if($agenda->id_seguro==$seguro->id) selected @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!--otros seguro-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}" >
                                <label for="id_seguro" class="col-md-12 control-label">Otros Seguro</label>
                                <div class="col-md-12">
                                    <select class="form-control form-control-sm input-sm select2_seguros" style="width: 100%" data-placeholder="Seleccione seguro" name="id_seguro_multiple[]" id="id_seguro_multiple" multiple onchange="guardarOtroSeguro('{{$agenda->id}}');" >
                                        @foreach($seguros as $seguro)
                                            <option @if(in_array($seguro->id, $seguros_pacientes)) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <!--fecha_nacimiento-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-4 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                                <label class="col-md-12 control-label" style="padding-right: 0;">@lang('agenda.fechaNacimiento')</label>
                                <div class="col-md-12" style="padding-right: 0;">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" onchange="edad2();" value="@if(old('fecha_nacimiento')!=''){{old('fecha_nacimiento')}}@else{{$paciente->fecha_nacimiento}}@endif" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required >

                                    </div>
                                    @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif

                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-2 {{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-12 control-label">@lang('agenda.edad')</label>
                                <div class="col-md-12">
                                    <input id="Xedad" type="text" class="form-control input-sm" name="Xedad"  required readonly>
                                    @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;" class="form-group col-md-4 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                                <label style="padding-right: 0;padding-left: 0;" for="tipo_cita" class="col-md-12 control-label">@lang('agenda.consecutivo')/@lang('agenda.primeravez')</label>
                                <div class="col-md-12" style="padding-left: 0;">
                                    <select id="tipo_cita" name="tipo_cita" class="form-control input-sm" >
                                        <option  @if(old('tipo_cita')=="1"){{"selected"}}@elseif($agenda->tipo_cita=="1"){{"selected"}}@endif value="1">@lang('agenda.consecutivo')</option>
                                        <option  @if(old('tipo_cita')=="0"){{"selected"}}@elseif($agenda->tipo_cita=="0"){{"selected"}}@endif value="0" >@lang('agenda.primeravez')</option>
                                    </select>
                                    @if ($errors->has('tipo_cita'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tipo_cita') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div id="ivip" style="margin-bottom: 1px;" class="form-group col-md-2 {{ $errors->has('vip') ? ' has-error' : '' }}">
                                <label for="vip" class="col-md-12 control-label" style="color: red;">VIP</label>
                                <div class="col-md-12">
                                    <input type="checkbox" id="vip" class="flat-red" name="vip" value="1"  @if(old('vip')=="1") checked @elseif($agenda->vip=='1') checked @endif>
                                    @if ($errors->has('vip'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('vip') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding-left: 0;" class="form-group col-md-6 has-warning" >
                                <label for="teleconsulta" class="col-md-12 control-label">@lang('agenda.teleconsulta')</label>
                                <div class="col-md-12" style="padding-right: 0;">
                                    <div class="col-md-1" style="padding-left: 0;">
                                        <input type="checkbox" id="tc" class="flat-yellow" name="tc" value="1"  @if($agenda->tc=="1") checked @endif>
                                    </div>
                                    <div class="col-md-11" style="padding: 0;">
                                        <input class="form-control input-sm" type="text" name="teleconsulta" id="teleconsulta" value="{{$agenda->teleconsulta}}" placeholder="Teléfono Teleconsulta" maxlength="45">
                                    </div>
                                    @if ($errors->has('teleconsulta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('teleconsulta') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!--tipo Ingreso-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-4 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" >
                                <label for="est_amb_hos" class="col-md-12 control-label">@lang('agenda.tipodeingreso')</label>
                                <div class="col-md-12" style="padding-right: 0;">
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" onchange="ingreso();">
                                        <option @if($agenda->est_amb_hos=="0") selected @endif value="0">Ambulatorio</option>
                                        <option style="color: red;" @if($agenda->est_amb_hos=="1") selected @endif value="1" >Hospitalizado</option>
                                    </select>
                                    @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-2 {{ $errors->has('omni') ? ' has-error' : '' }} has-warning oculto" id="div_omni">
                                <label for="omni" class="col-md-12 control-label">Hospital</label>
                                <div class="col-md-12">
                                    <select id="omni" name="omni" class="form-control input-sm" >
                                        <option value="">@lang('agenda.seleccione') ...</option>
                                        <option @if($agenda->omni=="SI") selected @endif value="SI">@lang('agenda.si')</option>
                                        <option @if($agenda->omni=="NO") selected @endif value="NO" >@lang('agenda.no')</option>
                                    </select>
                                    @if ($errors->has('omni'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('omni') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            @if($agenda->proc_consul=='1')

                                <div class="form-group col-md-6 {{ $errors->has('solo_robles') ? ' has-error' : '' }}">

                                    <div class="col-md-2">
                                        <input type="checkbox" class="flat-purple" id="solo_robles" name="solo_robles" value="1"  @if(old('solo_robles')=="1") checked @elseif($agenda->solo_robles=='1') checked @endif>
                                        @if ($errors->has('solo_robles'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('solo_robles') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <label style="padding: 0; color: purple;" for="supervisa_robles" class="col-md-10 control-label"> @lang('agenda.solodr') ROBLES</label>
                                    <div class="col-md-2">
                                        <input type="checkbox" class="flat-red" id="supervisa_robles" name="supervisa_robles" value="1"  @if(old('supervisa_robles')=="1") checked @elseif($agenda->supervisa_robles=='1') checked @endif>
                                        @if ($errors->has('supervisa_robles'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('supervisa_robles') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <label style="padding: 0;color: red;" for="supervisa_robles" class="col-md-10 control-label">SUPERVISADO Dr. ROBLES</label>

                                </div>

                            @endif



                        @else


                            @if($agenda->proc_consul=='1')
                                <!--id_procedimiento-->
                                <div class="form-group col-md-12 " style="margin-bottom: 0px;">
                                    <label for="id_procedimiento" class="col-md-12 control-label"> @lang('agenda.procedimiento')</label>
                                    <div class="col-md-12" style="padding-right: 0px;padding-left: 0px; margin-bottom: 0px;">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                    @if($agenda->id_procedimiento!=null)
                                                        {{$procedimientos->find($agenda->id_procedimiento)->nombre}}
                                                    @endif
                                                    @foreach($agendaprocedimientos as $agendaproc)
                                                        + {{$procedimientos->find($agendaproc->id_procedimiento)->nombre}}
                                                    @endforeach
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            <input type="hidden" id="espid" name="espid" value="{{$agenda->espid}}">
                            <input type="hidden" id="id_seguro" name="id_seguro" value="{{$agenda->id_seguro}}">
                            <!--input type="hidden" id="est_amb_hos" name="est_amb_hos" value="{{$agenda->est_amb_hos}}"-->
                            <input type="hidden" id="id_empresa" name="id_empresa" value="{{$agenda->id_empresa}}">
                            <input type="hidden" value="@if(old('fecha_nacimiento')!=''){{old('fecha_nacimiento')}}@else{{$paciente->fecha_nacimiento}}@endif" name="fecha_nacimiento" id="fecha_nacimiento">
                            <input type='hidden' value="{{$agenda->fecha_val}}" name='fecha_val' id='fecha_val'>
                            <!--cambiar a produ 7/11/2017-->
                            <!--especialidad-->
                            <div class="form-group col-md-12 {{ $errors->has('espid') ? ' has-error' : '' }}" style="margin-bottom: 0px;">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr >
                                            <td ><b> @lang('agenda.especialidad')</b></td>
                                            <td >{{Sis_medico\Especialidad::find($agenda->espid)->nombre}}</td>
                                            <td ><b> @lang('agenda.seguro')</b></td>
                                            <td >
					    
                                            @if(!(isset($historia)))
					    	@if(!is_null($historia->first()))
							{{$seguros->where('id',$historia->first()->id_seguro)->first()->nombre}} 
						@endif 
					    @else{{$seguros->where('id',$agenda->id_seguro)->first()->nombre}}
					    @endif</td>
                                        </tr>

                                        <tr>
                                            <!--td><b>Tipo Ingreso</b></td-->
                                            <td ><b> @lang('agenda.empresa')</b></td>
                                            <td >{{Sis_medico\Empresa::find($agenda->id_empresa)->nombrecomercial}}</td>
                                            <td ><b>Otros seguros:</b></td>
                                            <td >   
                                                <select class="form-control form-control-sm input-sm select2_seguros" style="width: 100%" data-placeholder="Seleccione seguro" name="id_seguro_multiple[]" id="id_seguro_multiple" multiple onchange="guardarOtroSeguro('{{$agenda->id}}');" >
                                                    @foreach($seguros as $seguro)
                                                        <option @if(in_array($seguro->id, $seguros_pacientes)) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <!--tr>
                                            <td>@if($agenda->est_amb_hos=="0") @lang('agenda.ambulatorio') @else @lang('agenda.hospitalizado') @endif</td>
                                            <td>{{Sis_medico\Empresa::find($agenda->id_empresa)->nombrecomercial}}</td>
                                        </tr-->
                                    </tbody>
                                </table>
                            </div>

                            <!--tipo Ingreso-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" >
                                <label for="est_amb_hos" class="col-md-12 control-label">@lang('agenda.tipodeingreso')</label>
                                <div class="col-md-12" >
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" onchange="ingreso();">
                                        <option @if($agenda->est_amb_hos=="0") selected @endif value="0">@lang('agenda.ambulatorio')</option>
                                        <option style="color: red;" @if($agenda->est_amb_hos=="1") selected @endif value="1" >@lang('agenda.hospitalizado')</option>
                                    </select>
                                    @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding-left: 0;" class="form-group col-md-6 has-warning" >
                                <label for="teleconsulta" class="col-md-12 control-label">@lang('agenda.teleconsulta')</label>
                                <div class="col-md-12" style="padding-right: 0;">
                                    <div class="col-md-1" style="padding-left: 0;">
                                        <input type="checkbox" id="tc" class="flat-yellow" name="tc" value="1"  @if($agenda->tc=="1") checked @endif>
                                    </div>
                                    <div class="col-md-11" style="padding: 0;">
                                        <input class="form-control input-sm" type="text" name="teleconsulta" id="teleconsulta" value="{{$agenda->teleconsulta}}" placeholder="Teléfono Teleconsulta" maxlength="45">
                                    </div>
                                    @if ($errors->has('teleconsulta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('teleconsulta') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('omni') ? ' has-error' : '' }} has-warning oculto" id="div_omni">
                                <label for="omni" class="col-md-12 control-label">Hospital</label>
                                <div class="col-md-12">
                                    <select id="omni" name="omni" class="form-control input-sm" >
                                        <option value="">@lang('agenda.seleccione') ...</option>
                                        <option @if($agenda->omni=="SI") selected @endif value="SI">@lang('agenda.si')</option>
                                        <option @if($agenda->omni=="NO") selected @endif value="NO" >@lang('agenda.no')</option>
                                    </select>
                                    @if ($errors->has('omni'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('omni') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            @endif

                            <div class="col-md-12"> </div>
                            @php
                                $fecha_db=$agenda->fechaini;
                                $fecha_db=substr($fecha_db,0,10);
                                $fecha_db=strtotime($fecha_db);
                                $fecha_db=date('Y-m-d ',$fecha_db);
                                $fecha_hoy=date('Y-m-d ');

                            @endphp


                            @php

                                $doctor_todo = Sis_medico\Doctor_Tiempo::where('id_doctor',$doctor->id)->first();
                                    if(!is_null($doctor_todo)){
                                    $minutos = $doctor_todo->tiempo * 10;
                                }

                                $fpermiso='0';
                                if($agenda->espid=='8'){
                                    $fpermiso='1';
                                }else{
                                    if($agenda->proc_consul=='1'){
                                    $fpermiso='1';
                                    }else{
                                        $id_user_ses = Auth::user()->id;
                                        $permiso = Sis_medico\Agenda_Permiso::where('id_usuario',$id_user_ses)->where('estado','1')->first();
                                        if(!is_null($permiso)){
                                            $fpermiso = '1';
                                        }
                                    }
                                }


                            @endphp

                            <!--estado cita-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('estado_cita') ? ' has-error' : '' }} has-success">
                                <label for="est_amb_hos" class="col-md-12 control-label"> @lang('agenda.accionarealizar')</label>
                                <div class="col-md-12">
                                <select id="estado_cita" name="estado_cita" class="form-control input-sm" required>
                                    @php
                                        $bandera='0';
                                    @endphp

                                    @if($agenda->estado_cita=='0')
                                        @if($agenda->fechaini<=date('Y-m-d H:i'))
                                                {{$bandera='1'}}
                                        @endif
                                    @endif
                                    @if($agenda->estado_cita=='0')
                                        <option @if($agenda->estado_cita=='0'){{'selected '}}@endif value="0">@lang('agenda.porConfirmar')</option>
                                    @endif
                                        <!--if ($bandera=='0' && $agenda->estado_cita!='4')-->

                                    @if($agenda->espid=='10'  && $agenda->id_doctor1=='4444444444') <!-- DIFERENTE DE LABS -->
                                    <option style="color: #003326 !important;" @if(old('estado_cita')=='4'){{'selected '}}@elseif($agenda->estado_cita=='4'){{'selected '}}@endif value="4"> @lang('agenda.asisteallaboratorio-') </option>
                                    @endif


                                    @if($agenda->estado_cita>='0' && $agenda->estado_cita<'4' && $fpermiso)
                                        <option @if(old('estado_cita')=='1'){{'selected '}}@elseif($agenda->estado_cita=='1'){{'selected '}}@endif value="1"> @lang('agenda.confirmar')</option>
                                    @endif

                                    @if($agenda->estado_cita!='4' && $fpermiso)
                                    <option @if(old('estado_cita')=='2'){{'selected '}}@elseif($agenda->estado_cita=='2'){{'selected '}}@endif value="2">@lang('agenda.reagendar')</option>
                                    @endif

                                    @if($agenda->estado_cita!='-1' && $agenda->estado_cita != '4' && $fpermiso)
                                    <option @if(old('estado_cita')=='3'){{'selected '}}@elseif($agenda->estado_cita=='3'){{'selected '}}@endif value="3">@lang('agenda.suspender')</option>
                                    @endif

                                    @if($agenda->estado_cita!='4'  && $agenda->id_doctor1=='4444444444') 
                                        <option @if(old('estado_cita')=='-1'){{'selected '}}@elseif($agenda->estado_cita=='-1'){{'selected '}}@endif value="-1">@lang('agenda.noasiste-')</option>
                                    @endif

                                    <!--if($fecha_hoy==$fecha_db || $agenda->estado_cita=='4')-->
                                    @if($agenda->espid!='10' || $agenda->id_doctor1!= '4444444444') <!-- DIFERENTE DE LABS -->
                                    @if($agenda->estado_cita != '2' && $agenda->estado_cita != '3' && $agenda->estado_cita != '-1')
                                    <option @if(old('estado_cita')=='4'){{'selected '}}@elseif($agenda->estado_cita=='4'){{'selected '}}@endif value="4">@lang('agenda.admision-')</option>
                                    @endif
                                    @endif

                                    @if($agenda->estado_cita != '3' && $agenda->estado_cita != '4')
                                    <option @if(old('estado_cita')=='-1'){{'selected '}}@elseif($agenda->estado_cita=='-1'){{'selected '}}@endif value="-1">@lang('agenda.noasiste-')</option>
                                    @endif
                                    <!--endif-->

                                </select>
                                @if ($errors->has('estado_cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado_cita') }}</strong>
                                    </span>
                                @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                                <label for="id_empresa" class="col-md-12 control-label">@lang('agenda.empresa')</label>
                                <div class="col-md-12">
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                        <option value=""  >@lang('agenda.seleccione')..</option>
                                        @foreach($empresas as $empresa)
                                            @if($empresa->admision  == '1')
                                            <option @if(old('id_empresa')!='') @if(old('id_empresa')==$empresa->id){{"selected"}} @endif @elseif($agenda->id_empresa==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $agenda->proc_consul == "1") {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
                                            @endif
                                            @if($agenda->espid=='10' && $empresa->id=='0993075000001')
                                            <option @if(old('id_empresa')==$empresa->id){{"selected"}}@elseif($agenda->id_empresa==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $agenda->proc_consul == "1") {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
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



                            @if($agenda->proc_consul=='1')
                                <!--procedencia  -->
                                <div id="iprocedencia" style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('procedencia') ? ' has-error' : '' }} ">
                                    <label for="procedencia" class="col-md-12 control-label">Procedencia</label>
                                    <div class="col-md-12">
                                        <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="@if(old('procedencia')!=''){{old('procedencia')}}@else{{$agenda->procedencia}}@endif" @if($agenda->estado_cita==4) readonly="readonly" @endif>
                                        @if ($errors->has('procedencia'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('procedencia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            @endif



                            <!--Doctor-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label for="id_doctor1" class="col-md-12 control-label">@lang('agenda.doctor')</label>
                                <div class="col-md-12">
                                    <input id="nombre_doctor" type="text" class="form-control input-sm" name="nombre_doctor" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  readonly="readonly">

                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" onchange="mensaje_alerta();">
                                    @foreach ($usuarios as $usuario)
                                        @if($usuario->id!='9666666666')
                                        @if(!($usuario->training == '1' && $agenda->proc_consul == '1'))
                                        <option @if($usuario->id!='1307189140') class="sel" @endif @if(old('id_doctor1') != '') @if(old('id_doctor1') == $usuario->id) {{"selected"}} @endif @elseif($usuario->id == $agenda->id_doctor1) {{"selected"}} @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @elseif($usuario->id=='1314490929') style="color: blue;" @endif>{{$usuario->apellido1}} @if($usuario->apellido2!='(N/A)'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} @if($usuario->nombre2!='(N/A)'){{$usuario->nombre2}}@endif</option>
                                        @endif
                                        @endif
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>



                            <!--salas-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                <label for="id_sala" class="col-md-12 control-label">@lang('agenda.ubicacion')</label>
                                <div class="col-md-12">
                                    <input id="tid_sala" type="text" class="form-control input-sm" name="tid_sala" value="@if(!is_null($sala)) {{$sala->nombre_sala}} / {{$hospital->nombre_hospital}} @endif" readonly="readonly">
                                    <select id="id_sala" name="id_sala" class="form-control input-sm" required>
                                        <option value="">Seleccionar...</option>
                                    @foreach ($salas as $sala)
                                        @if($sala->proc_consul_sala==$agenda->proc_consul)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                        @endif
                                        @if($sala->proc_consul_sala=='-1')
                                        <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @if($agenda->proc_consul=='1')
                                <!--Doctor Asistente 1-->
                                <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_doctor2') ? ' has-error' : '' }}">
                                    <label for="nombre_doctor2" class="col-md-12 control-label">@lang('agenda.asistente') 1</label>
                                    <div class="col-md-12">
                                    @php
                                        $asis1="";
                                        $asis2=""
                                    @endphp
                                    @if($agenda->id_doctor2!='')
                                        @if($doctor2->id_tipo_usuario=='3')
                                            @php $asis1="Dr(a). " @endphp
                                        @else
                                            @php $asis1="Enf. " @endphp
                                        @endif
                                        @php $asis1=$asis1.$doctor2->nombre1." ".$doctor2->nombre2." ".$doctor2->apellido1." ".$doctor2->apellido2 @endphp
                                    @else
                                        @php $asis1="Sin Asistente" @endphp
                                    @endif
                                    @if($agenda->id_doctor3!='')
                                        @if($doctor3->id_tipo_usuario=='3')
                                            @php $asis2="Dr(a). " @endphp
                                        @else
                                            @php $asis2="Enf. " @endphp
                                        @endif
                                        @php $asis2=$asis2.$doctor3->nombre1." ".$doctor3->nombre2." ".$doctor3->apellido1." ".$doctor3->apellido2 @endphp
                                    @else
                                        @php $asis2="Sin Asistente" @endphp
                                    @endif
                                    <input id="nombre_doctor2" type="text" class="form-control input-sm" name="nombre_doctor2" value="{{$asis1}}" readonly="readonly">
                                    <select id="id_doctor2" name="id_doctor2" class="form-control input-sm" >
                                        <option value="" >@lang('agenda.seleccione')..</option>
                                    @foreach ($usuarios as $usuario)
                                        
                                        <option @if(old('id_doctor2')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor2){{"selected"}}@endif value="{{$usuario->id}}">Dr(a). {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
                                        
                                    @endforeach
                                    @foreach ($enfermeros as $usuario)
                                        @if($doctor->id != $usuario->id)
                                            <option @if(old('id_doctor2')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor2){{"selected"}}@endif value="{{$usuario->id}}">Enf. {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
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
                                <!--Doctor Asistente 2-->
                                <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_doctor3') ? ' has-error' : '' }}">
                                    <label for="nombre_doctor3" class="col-md-12 control-label">@lang('agenda.asistente') 2</label>
                                    <div class="col-md-12">

                                        <input id="nombre_doctor3" type="text" class="form-control input-sm" name="nombre_doctor3" value="{{$asis2}}" readonly="readonly">
                                        <select id="id_doctor3" name="id_doctor3" class="form-control input-sm" >
                                            <option value="" >@lang('agenda.seleccione')..</option>
                                            @foreach ($usuarios as $usuario)
                                            @if($doctor->id != $usuario->id)
                                            <option @if(old('id_doctor3')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor3){{"selected"}}@endif value="{{$usuario->id}}">Dr(a). {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
                                            @endif
                                            @endforeach
                                            @foreach ($enfermeros as $usuario)
                                            @if($doctor->id != $usuario->id)
                                            <option @if(old('id_doctor3')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor3){{"selected"}}@endif value="{{$usuario->id}}">Enf. {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
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
                            @endif

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                                <label class="col-md-12 control-label">@lang('agenda.inicio')</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('inicio')!=''){{old('inicio')}}@else{{$agenda->fechaini}}@endif" name="inicio" class="form-control pull-right input-sm" id="inicio" required onchange="incremento(event)">
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
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label class="col-md-12 control-label">@lang('agenda.fin')</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('fin')!=''){{old('fin')}}@else{{$agenda->fechafin}}@endif" name="fin" class="form-control pull-right input-sm" id="fin" required >

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

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }} has-warning" >
                                <label for="cortesia" class="col-md-12 control-label">@lang('agenda.cortesia')</label>
                                <div class="col-md-12">
                                    <select id="cortesia" name="cortesia" class="form-control input-sm" required>
                                        <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">@lang('agenda.no')</option>
                                        <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">@lang('agenda.si')</option>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;margin-top: 4px" class="form-group col-md-6" >
                                <label for="triaje" class="col-md-6 control-label" ><span style="font-weight:bold;color:red">Triaje Telefonico</span></label>
                                <input @if(!is_null($triaje_Check)) checked @endif type="checkbox" id="triaje" name="triaje" style="transform: scale(1.5);" onchange="cambio_estado()">
                            </div>
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('ocupacion') ? ' has-error' : '' }} ">
                                <label for="ocupacion" class="col-md-12 control-label">@lang('agenda.ocupacion'):</label>
                                <div class="col-md-12">
                                    <input id="ocupacion" type="text" class="form-control input-sm" name="ocupacion" value="@if(old('ocupacion')!=''){{old('ocupacion')}}@else{{$agenda->ocupacion}}@endif" >
                                    @if ($errors->has('ocupacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ocupacion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;margin-top: 4px" class="form-group col-md-6" >
                                <label for="post_procedimiento" class="col-md-6 control-label" ><span>Post Procedimiento</span></label>
                                <input @if($agenda->post_proc == '1') checked @endif type="checkbox" id="post_procedimiento" name="post_procedimiento" style="transform: scale(1.5);">
                            </div>
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('referido') ? ' has-error' : '' }} ">
                                <label for="referido" class="col-md-12 control-label">@lang('agenda.referidoPor')</label>
                                <div class="col-md-12">
                                    <input id="referido" type="text" class="form-control input-sm" name="referido" value="@if(old('referido')!=''){{old('referido')}}@else{{$agenda->referido}}@endif" >
                                    @if ($errors->has('referido'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('referido') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @if($paciente != Array())
                                @if(is_null($paciente->paciente_doctor))
                                    <div id="ipac_dr" style="margin-bottom: 1px;" class="form-group col-md-6 {{ $errors->has('paciente_dr') ? ' has-error' : '' }}">
                                        <label for="paciente_dr" class="col-md-12 control-label" style="color: green;font-size: 12px;padding-left: 0;">@lang('agenda.pacienteparticulardr-')</label>
                                        <div class="col-md-12">
                                            <input type="checkbox" id="paciente_dr" name="paciente_dr" value="1" class="flat-green" @if(old('paciente_dr')=="1") checked @elseif($agenda->paciente_dr=='1') checked @endif >

                                            @if ($errors->has('paciente_dr'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('paciente_dr') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                @else
                                    <div id="ipac_dr" style="margin-bottom: 1px;" class="form-group col-md-6 {{ $errors->has('paciente_dr') ? ' has-error' : '' }}">
                                        <label for="paciente_dr" class="col-md-12 control-label" style="color: green;font-size: 12px;padding-left: 0;"></label>
                                        @if(!is_null($doctor_otro)) <span style="color: red;">@lang('agenda.pacienteparticulardr') {{$doctor_otro->apellido1}} {{$doctor_otro->nombre1}}</span>
                                        @else
                                            <span style="color: red;">@lang('agenda.pacienteparticulardr')</span>
                                        @endif
                                    </div>

                                @endif
                            @endif

                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}" style="padding: 0;">

                                <label for="observaciones" class="col-md-12 control-label">@lang('agenda.observaciones')</label>
                                <div class="col-md-12">
                                    <textarea id="observaciones" class="form-control input-sm" name="observaciones">@if(!is_null($agenda->observaciones)) {{$agenda->observaciones}} @endif</textarea>
                                    @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                             <div class="form-group col-md-12" style="padding: 0;">
                                <label for="observaciones_admin" class="col-md-12 control-label">@lang('agenda.observacionesadministrativas')</label>
                                <div class="col-md-12">
                                    <textarea id="observaciones_admin" class="form-control input-sm" name="observaciones_admin">{{$observaciones_admin}}</textarea>
                                </div>
                            </div>

                            <div class="form-group col-md-12" style="padding: 0;">
                                <label for="cie10" class="col-md-12 control-label">Cie10</label>
                                <div class="col-md-12">
                                    <textarea id="cie10" class="form-control input-sm" name="cie10" onblur="guardarCie10('{{$agenda->id}}')">{{$agenda->cie10}}</textarea>
                                </div>
                            </div>


                            <!--Adelantado-->
                            @if($agenda->id_doctor1!='4444444444')
                        <div id="idad" class="form-group col-md-3 {{ $errors->has('adelantado') ? ' has-error' : '' }} oculto">
                            <label for="adelantado" class="col-md-9 control-label" style="color: red;padding-left: 2;">@lang('agenda.porgestionar')</label>
                            <input type="checkbox" name="adelantado" style="width: 20px;height: 20px" onchange="valida_check(event);"  value="1" @if($agenda->adelantado=='1') checked @else @if(old('adelantado')=='1') checked @endif @endif >
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

                         <div id="fecha_validacion1" class="form-group col-xs-6{{ $errors->has('fecha_val') ? ' has-error' : '' }} oculto">
                            <label for="fecha_val" class="col-xs-8 control-label">@lang('agenda.fechavalidacion')</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_val" type="text" class="form-control input-sm" name="fecha_val" value="{{$agenda->fecha_val}}">
                            </div>
                            @if ($errors->has('fecha_val'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_val') }}</strong>
                                </span>
                            @endif
                        </div>


                        <!--Código de validación-->
                            <div  id="cod_validacion1" class="form-group col-xs-6{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                                <label for="cod_val" class="col-xs-8 control-label">@lang('agenda.codigovalidacion')</label>
                                    <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="{{$agenda->cod_val}}" >
                                    @if ($errors->has('cod_val'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cod_val') }}</strong>
                                        </span>
                                    @endif

                            </div>
                          @endif
                           @if($agenda->id_doctor1!='4444444444')
                        <div id="cod_validacion2" class="form-group col-xs-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                            <label for="validacion_cv_msp" class="col-md-12 control-label">@lang('agenda.codigovalidacion')</label>

                            <div class="col-md-4">
                                <input id="validacion_cv_msp" type="validacion_cv_msp" class="form-control" name="validacion_cv_msp" value="{{$agenda->validacion_cv_msp}}" >

                                @if ($errors->has('validacion_cv_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_cv_msp') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <input id="validacion_nc_msp" type="validacion_nc_msp" class="form-control" name="validacion_nc_msp" value="{{$agenda->validacion_nc_msp}}" >

                                @if ($errors->has('validacion_nc_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_nc_msp') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <input id="validacion_sec_msp" type="validacion_sec_msp" class="form-control" name="validacion_sec_msp" value="{{$agenda->validacion_sec_msp}}" >

                                @if ($errors->has('validacion_sec_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_sec_msp') }}</strong>
                                    </span>
                                @endif
                            </div>

                          </div>
                         @endif

                            <input type="hidden"  value="{{$id_doc}}" name="id_consultadr">
                            <div class="form-group">
                                <div class="col-md-2">
                                    <button type="submit" id="enviar" class="btn btn-primary  agbtn">
                                        <span class="glyphicon glyphicon-floppy-disk"></span>@lang('agenda.guardar')</button>
                                </div>

                                @if($agenda->estado_cita=='4' && !$historia->isEmpty())
                                    <input type="hidden" name="hcid" value="{{$historia['0']->hcid}}">
                                    @if($agenda->proc_consul=='1')
                                        @php    $protocolo = DB::table('hc_protocolo')->where('hcid',$historia['0']->hcid)->first();

                                        @endphp
                                        @if(!is_null($protocolo))
                                        <!-- HALLAZGO / MOTIVO / CONCLUSION : SI NO TIENE-->
                                        @if($protocolo->hallazgos==null || $protocolo->motivo==null || $protocolo->conclusion==null)

                                        <div class="col-md-2" >
                                            <button type="submit" class="btn btn-danger" formaction="{{route('admision.suspension',['url_doctor' => $url_doctor])}}"> @lang('agenda.suspender')</button>
                                        </div>
                                        @endif
                                        @endif
                                    @endif
                                    @if($agenda->proc_consul=='0')
                                        @php    $evolucion = DB::table('hc_evolucion')->where('hcid',$historia['0']->hcid)->first();
                                                $receta = DB::table('hc_receta')->where('id_hc',$historia['0']->hcid)->first();
                                        @endphp
                                        @if(!is_null($evolucion))
                                            <!-- MOTIVO / CC / RECETA : SI NO TIENE-->
                                            @if($agenda->espid!='8')
                                                @if($evolucion->motivo==null || $evolucion->cuadro_clinico==null || $receta->prescripcion==null)
                                                <div class="col-md-2" >
                                                    <button type="submit" class="btn btn-danger" formaction="{{route('admision.suspension',['url_doctor' => $url_doctor])}}"> @lang('agenda.suspender')</button>
                                                </div>
                                                @endif
                                            @else
                                                @if($evolucion->motivo==null || $evolucion->cuadro_clinico==null)
                                                <div class="col-md-2" >
                                                    <button type="submit" class="btn btn-danger" formaction="{{route('admision.suspension',['url_doctor' => $url_doctor])}}"> @lang('agenda.suspender')</button>
                                                </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endif

                                @endif



                                @if(!is_null($orden_a))

                                    <!--div class="col-md-2" >
                                        <a href="{{ route('orden.codigo_barras', ['id' => $orden_a->id_orden]) }}" target="_blank" class="btn bg-olive agbtn" >
                                        <span class="fa fa-barcode"> @lang('agenda.codigo')</span></a>
                                    </div-->
                                    <div class="col-md-2" >
                                        @if($orden_a->orden->seguro->tipo=='0')
                                        <a href="{{ route('orden.descargar', ['id' => $orden_a->id_orden]) }}" target="_blank" class="btn btn-success agbtn">
                                            <span > @lang('agenda.verOrden')</span>
                                        </a>
                                        @else
                                        <a href="{{ route('cotizador.imprimir', ['id' => $orden_a->id_orden]) }}" target="_blank" class="btn btn-success agbtn">
                                            <span >@lang('agenda.cotiz')</span>
                                        </a>
                                        @endif
                                    </div>
                                    @if($orden_a->orden->estado=='-1')
                                    <div class="col-md-2" >
                                        @if($orden_a->orden->seguro->tipo=='0')
                                        <a href="{{ route('orden.edit', ['id' => $orden_a->id_orden]) }}" target="_blank" class="btn btn-warning agbtn" >
                                        <span>@lang('agenda.editarOrden')</span></a>
                                        @else
                                        <a href="{{ route('cotizador.editar', ['id' => $orden_a->id_orden]) }}" target="_blank" class="btn btn-warning agbtn" >
                                        <span>@lang('agenda.editar')</span></a>
                                        @endif
                                    </div>
                                    @endif
                                    @if($orden_a->orden->estado=='-1')
                                    @if($orden_a->orden->seguro->tipo=='0')
                                    <div class="col-md-3" >
                                        <button type="button" class="btn btn-danger agbtn" onclick="confirmar('{{$orden_a->id_orden}}')">
                                        <span>@lang('agenda.generarOrden')</span></button>
                                    </div>
                                    @endif
                                    @else
                                    <div class="col-md-3" >
                                        <button type="button" class="btn btn-success agbtn">
                                        <span>@lang('agenda.ordenyagenerada')</span></button>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </form>
                </div>
            </div>


            <div class="col-md-12">
                <div class="box box collapsed-box">
                                <div class="box-header with-border">
                                    <h4 class="col-md-12">@if($agenda->estado_cita!='4')@lang('agenda.actualizar') @endif @lang('agenda.ordenesexternasodasyotrosarchivos')</h4>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>


                                <div class="box-body">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">@lang('agenda.listadodearchivos')</div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                              <tr>
                                                                <th>@lang('agenda.tipodearchivos')</th>
                                                                <th>@lang('agenda.mostrar')</th>
                                                                <th>@lang('agenda.subidopor'):</th>
                                                                <th>@lang('agenda.fecha')</th>
                                                                <th>@lang('agenda.eliminar')</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              @foreach($ar_historia as $thumbnail)
                                                                  @php
                                                                    $explotado = explode("." ,$thumbnail->archivo);
                                                                    $extension = end($explotado);
                                                                  @endphp
                                                                  <tr>
                                                                    <td>
                                                                        @if($extension == "jpg" || $extension == "jpeg" || $extension == "png")
                                                                        <a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            <img src="{{asset('imagenes/img.png')}}" style="width: 80px; height: 50px;" alt="{{$thumbnail->tipo_documento}}">
                                                                        </a>
                                                                        @elseif($extension == "pdf")
                                                                        <a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            <img src="{{asset('imagenes/pdf.png')}}" style="width: 80px; height: 50px;" alt="pdf">
                                                                        </a>
                                                                        @else
                                                                        <a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            <img src="{{asset('imagenes/word.png')}}" style="width: 80px; height: 50px;" alt="word">
                                                                        </a>
                                                                        @endif
                                                                    </td>
                                                                    @php 
                                                                        $usuario_archivo = Sis_medico\User::where('id', $thumbnail->id_usuariocrea)->first(); 
                                                                        $inicial_nombre = substr($usuario_archivo->nombre1,0,1);
                                                                    @endphp
                                                                    <td><a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            {{$thumbnail->archivo}}
                                                                        </a></td>
                                                                    <td>{{$inicial_nombre}}. {{$usuario_archivo->apellido1}}</td>
                                                                    <td> {{$thumbnail->created_at}} </td>
                                                                    <td><a href="{{ route('agenda.eliminarfoto', ['id' => $thumbnail->id])}}">
                                                                            <img src="{{asset('imagenes/tacho.png')}}" style="width: 30px; height: 30px;" alt="eliminar">
                                                                        </a></td>
                                                                  </tr>
                                                              @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="archivo" class="col-md-8 control-label"> @lang('agenda.ingresararchivos')</label>
                                        <div class="col-md-12">
                                            <form method="POST" action="{{route('agenda.archivo5')}}" class="dropzone" id="addimage" >
                                                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                                                <input type="hidden" name="id" value="{{ $agenda->id }}">
                                            </form>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 {{ $errors->has('hc') ? ' has-error' : '' }}">
                                        <label for="hc" class="col-md-4 control-label">@lang('agenda.ingresehistorialentexto')</label>
                                        <div class="col-md-12">
                                            <textarea name="hc" id="hc" rows="10" cols="50"  style="width: 100%;" @if($agenda->estado_cita>='4')readonly @endif>@if(!is_null($ar_historiatxt)){{$ar_historiatxt->texto}}@endif</textarea>
                                            @if ($errors->has('hc'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('hc') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <button onclick="guardar();">@lang('agenda.guardar')</button>
                                </div>
            </div>
        </div>
        </div>



        @php
            $mensaje_pre = "";
            $class_pre = "";
            if($pre_post>0){
                if(is_null($ex_pre)){
                    $class_pre="callout callout-danger";
                    $mensaje_pre=`{{trans('agenda.generarordenexamenpreoperatorio-')}}`;
                }else{
                    if($ex_pre->realizado=='0'){
                        $class_pre="callout callout-warning";
                        $mensaje_pre=`{{trans('agenda.examenpreoperatorioenproceso-')}}`;
                    }else{
                        $class_pre="callout callout-success";
                        $mensaje_pre=`{{trans('agenda.examenpreoperatoriorealizado-')}}`;
                    }
                }
                if(is_null($ex_post)){
                    $class_post="callout callout-danger";
                    $mensaje_post=`{{trans('agenda.generarordenexamenpostoperatorio-')}}`;
                }else{
                    if($ex_post->realizado=='0'){
                        $class_post="callout callout-warning";
                        $mensaje_post=`{{trans('agenda.examenpostoperatorioenproceso-')}}`;
                    }else{
                        $class_post="callout callout-success";
                        $mensaje_post=`{{trans('agenda.examenpostoperatoriorealizado-')}}`;
                    }
                }
            }
        @endphp
        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            @if($pre_post>0)
                @if($pre_post=='2')
                <div class="{{$class_pre}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    <a href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"><p> {{$mensaje_pre}}</p></a>
                </div>
                <div class="{{$class_post}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    <a href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"><p> {{$mensaje_post}}</p></a>
                </div>
                @endif
                @if($pre_post=='1')
                <div class="{{$class_pre}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    <a href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"><p> {{$mensaje_pre}}</p></a>
                </div>
                @endif

            @endif




            @if($agenda->nro_reagenda>'0' || (!$historia->isEmpty() && $agenda->estado_cita=='4'))
                <div class="callout callout-success   col-md-12" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    @if($agenda->nro_reagenda>'0')
                        <p>** @lang('agenda.citaReagendada') : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif</p>
                    @endif
                    @if(!$historia->isEmpty() && $agenda->estado_cita=='4')
                        <p>** @lang('agenda.pacienteyafueadmisionado')</p>

                    @endif

                </div>



                @php
                    $pendiente_prepa=false;
                    if($agenda->proc_consul=='0' && $agenda->estado_cita=='4')
                    {
                        $pendiente_prepa=true;

                        if($historia['0']->presion <> 0 && $historia['0']->pulso <> 0 && $historia['0']->temperatura <> 0 && $historia['0']->altura <> 0 && $historia['0']->peso <> 0)
                        {

                            $pendiente_prepa=false;
                        }
                    }

                @endphp


                @if($agenda->proc_consul=='0')
                @if($pendiente_prepa)
                <div class="callout callout-danger   col-md-12" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    <p>** @lang('agenda.pacientependientedeingresarpreparacion')</p>
                </div>
                @endif
                @endif
            @else


            @endif
            <div class="callout callout-success   col-md-12" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                @if($ordenes>0)
                    <p>**<a style="color: blue;" href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"> @lang('agenda.ordendeexamengenerada') @if($ordenes>1)({{$ordenes}})@endif</a></p>
                @endif
            </div>
            @if($ordenes==0)
                <div class="callout callout-success   col-md-12" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                    <p>**<a style="color: blue;" href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"> @lang('agenda.ordeneslaboratorio') </a></p>
                </div>
            @endif

        </div>



        @php

        $hcid=null;

        if(!$historia->isEmpty()){
            $hcid = $historia[0]->hcid;
        }

        @endphp

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary" style="margin-bottom: 2px;">
                <div class="box-body">
                    @if($agenda->id_doctor1 == '4444444444')
                        <a data-toggle="modal" data-target="#cert_med" class="btn btn-primary btn-sm"  href="{{ route('controldoc.form_cert', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> @lang('agenda.certificado_medico')</a>
                    @endif
                    @if(!$historia->isEmpty())

                        <a data-toggle="modal" data-target="#cert_med" class="btn btn-primary btn-sm"  href="{{ route('controldoc.form_cert', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> @lang('agenda.certificado_medico')</a>


                    @endif

                        <a  class="btn btn-success btn-sm"  href="{{ route('paciente.historial_recetas', ['id' => $agenda->id_paciente])}}" ><span class="glyphicon glyphicon-print"></span> @lang('agenda.receta')</a>

                        <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirpdf_resumen', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> @lang('agenda.resumen')</a>




                        <a href="{{ route('orden.admision', ['id_agenda' => $agenda->id, 'url' => $url_doctor]) }}" data-toggle="tooltip" title="Orden de Laboratorio" >
                            <button type="button" class="btn btn-warning btn-sm"><span class="ionicons ion-ios-flask"></span> @lang('agenda.publica')</button>
                        </a>

                        <!--a href="{{ route('orden_particular.crear_particular2',['id' => $agenda->id_paciente])}}" data-toggle="tooltip" title="Orden de Laboratorio">
                            <button type="button" class="btn btn-success btn-sm"><span class="ionicons ion-ios-flask"></span> @lang('agenda.privada')</button>
                        </a-->


                    @if($agenda->proc_consul=='1')

                        <a href="{{ route('cardiologia.agenda', ['id_agenda' => $agenda->id, 'url' => $url_doctor]) }}" >
                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-heart-empty"></span> @lang('agenda.cardiologia')</button>
                        </a>

                    @endif


                    @if($agenda->proc_consul=='0')

                        <!--a href="{{ route('cardiologia.agenda', ['id_agenda' => $agenda->id, 'url' => $url_doctor]) }}" >
                            <button type="button" class="btn btn-primary btn-sm"><span></span> @lang('agenda.ordenasociado')</button>
                        </a-->

                    @endif


                        <button type="button" onclick="regresar();" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-chevron-left" data-toggle="tooltip" title="Regresar"></span></button>


                </div>
            </div>
        </div>

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary" style="margin-bottom: 2px;">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span>  @lang('agenda.observacionmedicadelpaciente')</h4>

                </div>
                <div class="box-body">

                  {{$paciente->observacion}}

                </div>
            </div>
        </div>

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 2px;">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span> @lang('agenda.informacioncontacto')</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div  class="table-responsive col-md-12">
                    <table class="table table-striped">
                        <tbody>
                        @if($paciente->ciudad!="")
                        <tr>
                            <td><b>@lang('agenda.ciudad'):</b></td>
                            <td>{{ $paciente->ciudad }}</td>
                        </tr>
                        @endif
                        @if($paciente->direccion!="")
                        <tr>
                            <td><b>@lang('agenda.direccion'):</b></td>
                            <td>{{ $paciente->direccion }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><b>@lang('agenda.email'):</b></td>
                            <td>{{ $user_aso->email }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('agenda.telefonoDomicilio'):</b></td>
                            <td>{{ $paciente->telefono1 }}</td>
                        </tr>
                        <tr>
                            @php 
                                $telefono_sin_espacio = str_replace(" ", "", $paciente->telefono2); 
                                $telefono_10_digitos = substr($telefono_sin_espacio, 0, 10); 
                            @endphp 
                            <td><b>@lang('agenda.telefonoCelular'):</b></td>  
                            <td>{{$telefono_sin_espacio}}</td>
                            <td>
                                <a href="https://wa.me/@if(!is_null($telefono_10_digitos))593{{$telefono_10_digitos}}@endif/?text=*Estimada%20(o)%20Sr.%20(a)%20{{$agenda->pnombre1}}@if($agenda->pnombre2 != '(N/A)') {{$agenda->pnombre2}}@endif {{$agenda->papellido1}}@if($agenda->papellido2 != '(N/A)') {{$agenda->papellido2}}@endif*%0ALe%20estamos%20contactando%20de%20Endoscopynet%20para%20recordarle%20su%20asistencia%20el%20dia%20{{substr($agenda->fechaini,0,10)}}%0APara%20realizar:%0A*1.-%20%20el%20procedimiento%20de_____%20a%20las%20_____*%20%0A*%20Venir%20en%20ayunas%20(en%20el%20caso%20que%20tome%20pastilla%20para%20la%20presión%20se%20puede%20tomar%202%20horas%20antes%20de%20venir)%0A*Acompañado%20por%201%20familiar%20(persona%20mayor%20de%20edad)*%0ANo%20puede%20conducir%0A*LUGAR:*%20_____%0A*HORA:*%20_____%0A*Por%20favor%20confirmar%20si%20recibe%20la%20información.*%0A" target="_blank">
                                    <img src="{{asset('imagenes/whatsapp_logo.png')}}" width="50" height="40">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><b>@lang('agenda.familiar')</b></td>
                            <td>{{  $paciente->nombre1familiar}} @if($paciente->nombre2familiar != '(N/A)' ){{ $paciente->nombre2familiar}}@endif {{ $paciente->apellido1familiar}} @if($paciente->apellido2familiar != '(N/A)'){{ $paciente->apellido2familiar}}@endif</td>
                        </tr>
                        <tr>
                            <td><b>@lang('agenda.telefonoFamiliar'):</b></td>
                            <td>{{ $paciente->telefono3 }}</td>
                        </tr>

                        </tbody>
                    </table>
                    </div>



                </div>
            </div>

        </div>
        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">

            <div class="box box-primary collapsed-box" style="margin-bottom: 2px;">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span> LOG</h4>
                    @if($agenda->observaciones!=null)<div>@lang('agenda.ultimaObservacion'):<b> {{$agenda->observaciones}}</b></div>@endif
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">

                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="overflow:scroll; height:240px;">
                        <div class="table-responsive col-md-12" style="padding: 0;">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                <tbody>
                                    @foreach ($logs as $log)
                                    <tr style="background-color: cyan;">
                                        <td><b>@lang('agenda.fecha')</b></td>
                                        <td>{{ substr($log->created_at,0,10) }} {{ substr($log->created_at,11,5) }}</td>
                                        <td><b>{{ substr($log->nombre1,0,1) }}{{ $log->apellido1 }}</b></td>
                                    </tr>

                                    <tr>
                                        <td><b>@lang('agenda.descripcion')</b></td>
                                        <td colspan="2">@if($log->descripcion!=null){{ $log->descripcion }} / @endif @if($log->descripcion2!=null){{ $log->descripcion2 }} / @endif @if($log->descripcion3!='CAMBIO: '){{ $log->descripcion3 }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td><b>@lang('agenda.observacion')</b></td>
                                        <td colspan="2">{{ $log->observaciones_ant }} / {{ $log->observaciones }}</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary" id="consulta_calendario">

            </div>
        </div>

    </div>
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script>
    $(document).ready(function() {

        obtener_dias();


    });
    function guardarCie10(id){
        $.ajax({
            type: 'post',
            url: "{{route('agenda.guardarCie10_isspol')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'idAgenda': id,
                'cie10': $('#cie10').val(),
            },
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                
            }
        });
    }



    function obtener_dias(){

        var fech_val = $("#fecha_val").val();

        var fech_format = String(fech_val).split('-');
        var nuev_fech_val = fech_format[1] + '/' + fech_format[2] + '/' + fech_format[0];

        var nuev_fech_val_pars = new Date(Date.parse(nuev_fech_val));

        var f = new Date();
        fech_actual = (f.getMonth() +1) + "/" +  f.getDate() + "/" + f.getFullYear();

        var fech_actual_pars = new Date(Date.parse(fech_actual));

        var milisegundosDia = 24*60*60*1000;

        var milisegundosTranscurridos = Math.abs(nuev_fech_val_pars.getTime() - fech_actual_pars.getTime());

        var diasTranscurridos = Math.round(milisegundosTranscurridos/milisegundosDia);


        if(diasTranscurridos > 0){

            swal({
                title: "Su fecha de validación expira en"+" "+diasTranscurridos+" "+"días",
                icon: "success",
                type: 'success',
                buttons: true,
                closeOnConfirm: false
            })

        }
   }



    @if($agenda->id_doctor1 != '1307189140')
        $('#ivip').hide();

    @endif

    var doctor_vip = document.getElementById('id_doctor1').value;
    if(doctor_vip == '1307189140'){
        $('#ivip').show();
    }

    $('#paciente_dr').on('ifChecked', function(event){

        @if($paciente != Array())

            alert("Confirme que es paciente particular del Doctor");

        @endif

    });

    function mensaje_alerta(){
        var doctor_vip = document.getElementById('id_doctor1').value;
        if(doctor_vip == '1307189140'){
            $('#ivip').show();
        }

        var pac_doc = document.getElementById('paciente_dr').value
        if(pac_doc == '1'){
            alert("Confirme es paciente particular del Doctor");
        }

    }

  tinymce.init({

    selector: '#hc',
    @if($agenda->estado_cita>='4')
        readonly : 1,
    @endif
    setup:function(ed) {
       ed.on('change', function(e) {
            tinyMCE.triggerSave();
            var mensaje  = document.getElementById('hc').value;
            document.getElementById('hc2').value = mensaje;
            datos = document.getElementById('hc2').value;
       });
   }
  });
  </script>

<script type="text/javascript">
    $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
    });
    $('input[type="checkbox"].flat-purple').iCheck({
        checkboxClass: 'icheckbox_flat-purple',
        radioClass   : 'iradio_flat-purple'
    });
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
    $('input[type="checkbox"].flat-yellow').iCheck({
      checkboxClass: 'icheckbox_flat-yellow',
      radioClass   : 'iradio_flat-yellow'
    });

    function guardar(){
        $('#form_agenda').submit();

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

    
    function generarRC(id_proforma){
        console.log("hola");

        $.ajax({
            type: 'post',
            url: "{{route('comercial.proforma.pasarNuevoRecibo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                
                'id': id_proforma,
                'id_agenda': {{$agenda->id}},
            },
            success: function(data) {
                console.log(data);
                window.location.href = `{{url('nuevo_recibo_de_cobro/editar')}}/${data.id_orden}`;
            },
            error: function(data) {
              
            }
        });

    }


    function incremento (e){
        var inicio = document.getElementById("inicio").value;
        var valor = document.getElementById("proc_consul").value;

         if(valor == 0){

            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
        @if(!is_null($doctor_todo))
            @if($agenda->id_doctor1== $doctor_todo->id_doctor)
            var fin = moment(inicio).add({{$minutos}}, 'm').format('YYYY/MM/DD HH:mm');
            @endif
        @endif


            $("#fin").val(fin);
         }
         if(valor == 1){

            var fin = moment(inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');

            $("#fin").val(fin);
         }
    }

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
}else if(id_seguro == '2'){//IESS
    $("#fecha_validacion1").removeClass("oculto");
    $('#cod_validacion1').removeClass("oculto");
    //$('#fecha_validacion1').prop("required", true);
    //$('#cod_validacion1').prop("required", true);
    $("#cod_validacion2").addClass("oculto");
    $('#cod_validacion2').removeAttr("required");
    $("#idad").removeClass('oculto');
 // $('#adelantado').prop("required", true);
  //$('#fecha_val').val(" ");
   // $('#cod_val').val(" ");
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
    //$('#fecha_val').val(" ");
   // $('#cod_val').val(" ");
    $('#validacion_cv_msp').val(" ");
    $('#validacion_nc_msp').val(" ");
    $('#validacion_sec_msp').val(" ");
    $('#adelantado').prop('checked', false);
}


    $(function () {
        ingreso();
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'


            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',

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
            incremento(event);
        });
        $("#fin").on("dp.change", function (e) {
            $('#inicio').data("DateTimePicker").maxDate(e.date);
        });
        $("#fecha_val").on("dp.change", function(e) {
        valida_fval(e);
        });

    });

    $('#cert_med').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

    $(document).ready(function()
    {
        inicializar();

        valida_seguro(document.getElementById('id_seguro').value);

        @if($url_doctor=='0')
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{asset('/agenda_procedimiento/pentax_procedimiento')}}">Pentax</li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
        @else
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => $url_doctor]) }}"></i> Doctor</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
        @endif


        var fecha_url = document.getElementById('inicio').value;
        var unix =  Math.round(new Date(fecha_url).getTime()/1000)-18000;
        $("#unix").val(unix);
        $("#unix2").val(unix);

                $('#favoritesModal').on('hidden.bs.modal', function(){
                    $(this).removeData('bs.modal');
                });
        //Initialize Select2 Elements
        $('.select2').select2({
            tags: false
        });

        $("select").on("select2:select", function (evt) {
            var element = evt.params.data.element;
            //console.log(element);
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });

        var estado = document.getElementById("estado_cita").value;
        /* //$("#observaciones").attr("readonly","readonly"); ///14/08 */
        $("#observaciones").removeAttr("required");
        $("#inicio").attr("disabled","disabled");
        $("#fin").attr("disabled","disabled");
        $("#id_doctor1").hide();
        $("#nombre_doctor").show();
        $("#id_doctor2").hide();
        $("#nombre_doctor2").show();
        $("#id_doctor3").hide();
        $("#nombre_doctor3").show();
        $("#id_sala").hide();
        $("#tid_sala").show();
        $("#estado_cita").focus();

        //$("#iprocedencia").hide();
        $("#iempresa").hide();
        //$("#procedencia").removeAttr("required");
        $("#id_empresa").removeAttr("required");




        if (estado==2){
            $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("disabled");
                $("#fin").removeAttr("disabled");
                $("#observaciones").attr("required","required");
                $("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
                $("#iprocedencia").hide();
                $("#iempresa").hide();
                $("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");

        }
        @if($agenda->proc_consul=='1')
        if (estado==4){
               //$("#iprocedencia").show();
               //$("#procedencia").attr("required","required");
               $("#observaciones").removeAttr("required");


        }
        @endif
        if (estado==4){
            @if($agenda->estado_cita!='4')
               $("#iempresa").show();
               $("#id_empresa").attr("required","required");
            @endif
        }
        @if(is_null($agenda->id_sala))
        $("#id_sala").show();
        $("#tid_sala").hide();
        @endif



        $("#estado_cita").change(function () {
            // 13/01/2018   Solo cuenta reagenda para procedimientos si:
            //cambia de hospital, fecha de inicio y fin a otro día
            $("#inicio").attr("disabled","disabled");
            $("#fin").attr("disabled","disabled");
            //------

            //$("#iprocedencia").hide();
                $("#iempresa").hide();
                //$("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");

            var estado = document.getElementById("estado_cita").value;
             //$("#iprocedencia").hide();
                $("#iempresa").hide();
                //$("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");
            $("#observaciones").removeAttr("required");
            if(estado==1 ){
                $("#observaciones").removeAttr("readonly");
                $("#observaciones").removeAttr("required");
                $("#observaciones").val("");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();
                //$("#iprocedencia").hide();
                $("#iempresa").hide();
                //$("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");
            } else if(estado==3 || estado==-1){
                $("#observaciones").removeAttr("readonly");
                $("#observaciones").val("");
                $("#observaciones").prop("required", true);
                $("#inicio").attr("disabled","disabled");
                $("#fin").attr("disabled","disabled");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();
                //$("#iprocedencia").hide();
                $("#iempresa").hide();
                //$("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");
            } else if(estado==2){
                $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("disabled");
                $("#fin").removeAttr("disabled");
                $("#observaciones").prop("required", true);
                $("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
                //$("#iprocedencia").hide();
                $("#iempresa").hide();
                //$("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");
            }
            else if(estado==4){
                @if($agenda->proc_consul=='1')
                //$("#iprocedencia").show();
                //$("#procedencia").attr("required","required");
                @endif
                $("#iempresa").show();
                $("#id_empresa").attr("required","required");

                $("#observaciones").removeAttr("readonly");
                $("#observaciones").val("");
             }
            else{
                //$("#observaciones").attr("readonly","readonly");//1408
                $("#inicio").attr("disabled","disabled");
                $("#fin").attr("disabled","disabled");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();
            }
            @if(is_null($agenda->id_sala))
            $("#id_sala").show();
            $("#tid_sala").hide();
            @endif

        });
        var ventana_ancho = $(window).width();


        if(ventana_ancho > "962" ){
            var nuevovalor = ventana_ancho * 0.8;
        }
        else
        {
            var nuevovalor = ventana_ancho * 0.9;
        }
        $("#frame_ventana").width(nuevovalor);


        edad2();

        $('.select2_seguros').select2({
            tags: false,
        });
    });


/*function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}*/

    function confirmar(id){
        var confirmar = confirm("Se procederá a generar la orden y enviar a Laboratorio");

        <?php /* location.href = "{{url('orden_iess/confirmar')}}"+"/{{$orden->id}}" */?>
        if(confirmar){
            $.ajax({
                type: "get",
                url:  "{{ url('orden_iess/confirmar')}}/"+id,   //agenda.consulta_ag
                success: function(data){
                    if(data.estado=='ok'){
                        alert("orden fue generada correctamente");
                        location.reload();
                    }
                    if(data.estado=='error'){
                        alert(data.mensaje);
                    }
                }
            })
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
        /*resta de fechas*/
        /*var x = new Date(fecha.getDate() - fecha_nueva.getDate());
        /*restando dias*/
               /* x.setDate(x.getDate() - 3 );*/

        var isChecked = document. getElementById('adelantado'). checked;

        if(isChecked == false){

           if(fecha_nueva <= fecha){

            alert("La Fecha de Validacion no debe ser menor a la Fecha Actual")
            $('#fecha_val').val("");

           }

        }


    }




    //Valida Cambio de Seguro Seleccionado
    function valida_seguros(e){

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
           // $('#adelantado').prop("required", true);
            $('#cod_validacion1').addClass("oculto");//Cod val issfa y isspol
            $('#cod_validacion1').removeAttr("required");
            //$('#fecha_val').val(" ");
            //$('#cod_val').val(" ");
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
            //$('#fecha_val').val(" ");
            //$('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }else if(id_seguro == '2'){//IESS
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            //$('#fecha_validacion1').prop("required", true);
            //$('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
            $('#cod_validacion2').removeAttr("required");
            $("#idad").removeClass('oculto');
           // $('#adelantado').prop("required", true);
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
            //S$('#fecha_val').val(" ");
           // $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
            $('#adelantado').prop('checked', false);
        }

    }



    function valida_seguro(seguro){

        //alert(seguro);
        if(seguro =='2' || seguro =='3' || seguro == '5' || seguro == '6'){
            $('#ipac_dr').hide();
        }
        else{
            $('#ipac_dr').show();
        }

    }

    var inicializar = function ()
    {


        var jsfecha = document.getElementById('fecha').value;
        var jsunix =  Math.round(new Date(jsfecha).getTime()/1000)-18000;

        $.ajax({
            type: 'get',
            url:'{{ url('agenda/consulta_agenda')}}/{{$id_doc}}/'+jsunix,   //agenda.consulta_ag
            success: function(data){
                $('#consulta_calendario').empty().html(data);
            }
        })

    }

    function regresar(){

        var fecha_url = document.getElementById('inicio').value;
        var unix =  Math.round(new Date(fecha_url).getTime()/1000);

        location.href = "{{url('pre_agenda/regresar')}}/"+{{$agenda->id}}+"/"+unix+"/"+{{$url_doctor}};

    }

    $('input[type="checkbox"].flat-purple').on('ifChecked', function(event){

        $("#estado_cita option[value=2]").attr("selected",true);
        $("#observaciones").removeAttr("readonly");
        $("#inicio").removeAttr("disabled");
        $("#fin").removeAttr("disabled");
        $("#observaciones").prop("required", true);
        $("#observaciones").val("");
        $("#id_doctor1").show();
        $("#iempresa").hide();
        $("#id_empresa").removeAttr("required");


        $("#nombre_doctor").hide();
        $("#id_doctor2").show();
        $("#nombre_doctor2").hide();
        $("#id_doctor3").show();
        $("#nombre_doctor3").hide();
        $("#id_sala").show();
        $("#tid_sala").hide();
        //$("#id_doctor1 option[value=1307189140]").attr("selected",true);
        //$("#id_doctor1 option[class='sel']").hide();




    });

    $('input[type="checkbox"].flat-purple').on('ifUnchecked', function(event){

        $("#id_doctor1 option[class='sel']").show();

    });

    function inicio_cita (hcid){
       // $('#btn_inicio').css("display", "none");
        //$('#btn_fin').css("display", "block");

        $.ajax({
          type: 'get',
          url:"{{url('consulta/inicio')}}/"+hcid,
          datatype: 'json',
          success: function(data){
            location.reload();
          },
          error: function(data){

          }
        });
    }

    function fin_cita (hcid){

        $.ajax({
          type: 'get',
          url:"{{url('consulta/fin')}}/"+hcid,
          datatype: 'json',
          success: function(data){
            if (data == "ok") {
                location.reload();
            }
          },
          error: function(data){

          }
        });
    }


</script>
<script>

    $('#modal_recibo_cobro').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    function cambio_estado(){

        document.getElementById("observaciones").style.height ='160px';
        let triaje = document.getElementById("triaje").checked;
        let variable = {{$agenda->id}};
        let msj = `
        •¿Fiebre mayor a 38°, tomada con termómetro?
        •¿Dolor de garganta y/o cabeza?
        •¿Congestión nasal?
        •¿Malestar en general?`;

        if(triaje){
            document.getElementById("observaciones").value = msj;
        $.ajax({
            url:"{{route('agenda.triaje_telefonico')}}",
            datatype: 'json',
            data:{
                'id_agenda':variable,
            },
            type: 'get',
            success: function(data){
            },
            error: function(data){
                console.log(data);
            }
          });
        }else{
            document.getElementById("observaciones").value = ''; 
            document.getElementById("observaciones").style.height='20px';

        }
    }

    function guardarOtroSeguro(id_agenda){
        $.ajax({
        type: 'post',
        url: "{{route('agenda.guardar_otro_seguro')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
            'id_seguro_multiple': $("#id_seguro_multiple").val(),
            'id_agenda': id_agenda,
           
        },
        success: function(datahtml) {
        },
        error: function(data) {
            console.log(data);
        }
        });
    }

</script>





@include('sweet::alert')
@endsection
