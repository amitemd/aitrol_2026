
@extends('agenda.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!--Modal Recibo de Cobro-->
<div class="modal fade" id="modal_recibo_cobro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>

    </div>
  </div>
</div>

<div class="modal fade" id="preparaciones_procedimientos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="modalconsentimiento" data-backdrop="static"  role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>
<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
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
                    <div class="col-md-6"><h4>Actualiza @if($agenda->proc_consul=='0') CONSULTA @elseif($agenda->proc_consul=='1') PROCEDIMIENTO @else{{'Reuniones'}}@endif del paciente: </h4></div><div class="col-md-6"><h4 style="color: red;">{{$agenda->id_paciente}} - {{  $agenda->pnombre1}} @if($agenda->pnombre2 != '(N/A)' ){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != '(N/A)'){{ $agenda->papellido2}}@endif</h4></div>
                    <!--cambiar a produ 7/11/2017-->
                </div>

            </div>
        </div>
        <div class="col-md-7" style="padding-right: 4px;">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <!--h4>CONFIRMA-ASIGNA DOCTOR/HORARIO-SUSPENDE PROCEDIMIENTO </h4-->
                    <!--cambiar a produ 7/11/2017-->
                    @php
                        $doctor=Sis_medico\User::find($agenda->id_doctor1);
                        $sala=Sis_medico\Sala::find($agenda->id_sala);
                        $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                        $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                        $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                        $paciente=Sis_medico\Paciente::find($agenda->id_paciente);
                        $user_aso=Sis_medico\User::find($paciente->id_usuario);
                    @endphp
                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn btn-warning btn-xs agbtn" href="https://coberturasalud.msp.gob.ec/" target="_blank" style="width: 100%"><span class="glyphicon glyphicon-globe"> </span> C. Pública</a>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn btn-primary btn-xs"  id="examenes_externos"  href="{{ route('laboratorio.externo', ['id_paciente' => $agenda->id_paciente]) }}" style="width: 100%"><span class="ionicons ion-ios-flask" ></span> Examenes Externos</a>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn btn-primary btn-xs"  id="examenes_externos"  href="{{ route('ingreso.biopsias2', ['id_paciente' => $agenda->id_paciente]) }}" style="width: 100%"> Biopsias</a>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn bg-red btn-xs agbtn" data-remote="{{ route('preparaciones.index')}}"
                    class="btn btn-info btn-sm" data-toggle="modal" data-target="#preparaciones_procedimientos" style="width: 100%"> Preparaciones</a>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        @if(is_null($agenda->id_empresa))
                        @php $id_empresa = '0992704152001';  @endphp
                            <a class="btn bg-navy btn-xs agbtn" data-remote="{{ route('documentos_pdf',['id'=>$agenda->id_paciente,'empresa'=>$id_empresa, 'id_agenda'=>$agenda->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalconsentimiento"> Consentimiento</a>
                        @elseif(!is_null($agenda->id_empresa))
                                <a class="btn bg-navy btn-xs agbtn" data-remote="{{ route('documentos_pdf',['id'=>$agenda->id_paciente,'empresa'=>$agenda->id_empresa, 'id_agenda'=>$agenda->id])}}"
                            class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalconsentimiento"> Consentimiento</a>
                        @endif
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-4" style="padding: 1px;">
                        
                        @php
                            $orden = Sis_medico\Ct_Orden_Venta::where('id_agenda', $agenda->id)->where('estado', '<>', '0')->first();
                        @endphp
                        @if(is_null($orden))
                            <!--a class="btn btn-primary btn-sm"  id="examenes_externos"  href="{{ route('factura.agenda', ['id' => $agenda->id]) }}" > Recibo de Cobro</a-->
                            <a class="btn bg-purple btn-xs agbtn"  id="examenes_externos"  href="{{ route('nuevorecibocobro.crear', ['id' => $agenda->id]) }}" style="width: 100%" > Recibo de Cobro N</a>
                        @else
                            
                            <a target="_blank" class="btn bg-purple btn-xs agbtn"  id="examenes_externos"  href="{{ route('facturacion.modal_recibo', ['id_orden' => $orden->id]) }}" data-toggle="modal" data-target="#modal_recibo_cobro" style="width: 100%"> Recibo de Cobro</a>

                            <!--La que estaba -->
                            <!--<a target="_blank" class="btn btn-primary btn-sm"  id="examenes_externos"  href="{{ route('facturacion.imprimir_ride', ['id_orden' => $orden->id]) }}" > Recibo de Cobro</a>-->
                        @endif


                    </div>
                    @php 
                        $proforma = Sis_medico\Proforma_Cabecera::where('id_paciente',$agenda->id_paciente)->where('estado', 1)->get();
                    @endphp
                    @if(count($proforma) > 0)
                    <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">
                        <a class="btn bg-maroon btn-xs agbtn" data-remote="{{route('comercial.proforma.proformaModal',['id_paciente' => $agenda->id_paciente])}}" data-toggle="modal" data-target="#tarifario_nivel" style="width: 100%">Seleccionar Proforma</a>
                    </div>
                    @endif


                    <!--div  class="table-responsive col-md-12">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><b>Cédula:</b></td>
                                <td>{{$agenda->id_paciente}}</td>
                                <td><b>Nombres:</b></td>
                                <td colspan="4">{{  $agenda->pnombre1}} @if($agenda->pnombre2 != '(N/A)' ){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != '(N/A)'){{ $agenda->papellido2}}@endif</td>
                            </tr>
                            <tr>
                                <td><b>Tipo Agendamiento:</b></td>
                                <td>@if($agenda->proc_consul=='0'){{'Consulta'}}@elseif($agenda->proc_consul=='1'){{'Procedimiento'}}@else{{'Reuniones'}}@endif</td>
                                <td><b>Estado:</b></td>
                                <td>@if($agenda->estado_cita=='0')Por Confirmar @elseif($agenda->estado_cita=='1')Confirmada @elseif($agenda->estado_cita=='4')ASISTIÓ @endif</td>
                                <td><b>Cortesia:</b></td>
                                <td> @if(!is_null($cortesia_paciente)){{$cortesia_paciente->cortesia}}@else NO @endif </td>
                            </tr>
                            @if(!$historia->isEmpty())
                            <tr>
                                <td style="color: red;">** Ya fue generada la Historia Clínica para esta Cita </td>
                            </tr>
                            @endif
                            @if($agenda->nro_reagenda>'0')
                            <tr>
                                <td style="color: red;">** La cita ya ha sido reagendada : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    </div-->

                </div>
                <div class="box-body">
                     @if($citas == '[]' )
                @php
                    $citas = array();
                @endphp
            @endif
            @if($citas != array())
            <div  class="table-responsive col-md-12">
                <table class="table table-striped">
                    <thead>
                    <th style="color:red;">El Paciente tiene {{$citas->count()}} agenda(s) para el día de hoy</th>

                    </thead>
                    <tbody>
                @foreach($citas as $cita)
                       @php $adoctor=Sis_medico\User::find($cita->id_doctor1) @endphp
                      <tr>
                        <td>@if($cita->proc_consul==0) CONSULTA @elseif($cita->proc_consul==1) PROCEDIMIENTO @endif Desde: {{substr($cita->fechaini,10)}} hasta: {{substr($cita->fechafin,10)}} @if($adoctor!=null) con el Dr(a). {{$adoctor->nombre1}}{{$adoctor->apellido1}} @endif agendado por {{Sis_medico\User::find($cita->id_usuariomod)->nombre1}}{{Sis_medico\User::find($cita->id_usuariomod)->apellido1}}</td>
                       </tr>
                @endforeach
                    </tbody>
                </table>
            </div>
            @endif
                    <div class="form-group col-md-12" style="padding: 0;">
                        <form class="form-vertical" role="form" method="POST" action="{{ route('preagenda.update', ['id' => $agenda->id]) }}" enctype="multipart/form-data">

                            <input type="hidden" name="_method" value="PATCH">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="fecha" value="{{date('Y-m-d H:i')}}">
                            <input type="hidden" id="ruta" name="ruta" value="{{$ruta}}">
                            <input type="hidden" id="id_paciente" name="id_paciente" value="{{$agenda->id_paciente}}">
                            <input type="hidden" id="unix" name="unix" value="">
                            <textarea name="hc" id="hc2" rows="10"  cols="50" style="width: 100%; display:none;" @if($agenda->estado_cita>='4')readonly @endif>@if(!is_null($ar_historiatxt)){{$ar_historiatxt->texto}}@endif</textarea>
                            @if(!is_null($ar_historiatxt))
                                <input type="hidden" name="id_ag_artxt" id="id_ag_artxt" value="{{$ar_historiatxt->id}}">
                            @endif

                            <!--proc_consul-->
                            <input id="proc_consul" type="hidden" class="form-control" name="proc_consul" value="{{$agenda->proc_consul}}" >

                            @if($agenda->estado_cita!='4')
                            @if($agenda->proc_consul=='1')
                            <!--id_procedimiento-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-12 {{ $errors->has('id_procedimiento') ? ' has-error' : '' }}">
                                <label for="id_procedimiento" class="col-md-12 control-label">Procedimientos</label>
                                <div class="col-md-12">
                                    <select class="form-control select2" multiple="multiple" name="proc[]" data-placeholder="Seleccione los Procedimientos" required style="width: 100%;">
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

                            <!--seguro-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_seguro') ? ' has-error' : '' }}" >
                                <label for="id_seguro" class="col-md-12 control-label">Seguro</label>
                                <div class="col-md-12">
                                    <select id="id_seguro" name="id_seguro" class="form-control input-sm"  required onchange="valida_seguro(this.value), valida_seguros(event);">
                                        @foreach ($seguros as $seguro)
                                            <option  @if(old('id_seguro')==$seguro->id){{"selected"}}@elseif($agenda->id_seguro==$seguro->id){{"selected"}} @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
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
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                                <label class="col-md-12 control-label">Fecha Nacimiento</label>
                                <div class="col-md-12">
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

                            <!--tipo Ingreso-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" >
                                <label for="est_amb_hos" class="col-md-12 control-label">Tipo de Ingreso</label>
                                <div class="col-md-12" >
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" onchange="ingreso();">
                                        <option @if(old('est_amb_hos')=="0"){{"selected"}}@elseif($agenda->est_amb_hos=="0"){{"selected"}}@endif value="0">Ambulatorio</option>
                                        <option style="color: red;" @if(old('est_amb_hos')=="1"){{"selected"}}@elseif($agenda->est_amb_hos=="1"){{"selected"}}@endif value="1" >Hospitalizado</option>
                                    </select>
                                    @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('omni') ? ' has-error' : '' }} has-warning oculto" id="div_omni">
                                <label for="omni" class="col-md-12 control-label">Hospital</label>
                                <div class="col-md-12">
                                    <select id="omni" name="omni" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                        <option @if($agenda->omni=="SI") selected @endif value="SI">SI</option>
                                        <option @if($agenda->omni=="NO") selected @endif value="NO" >NO</option>
                                    </select>
                                    @if ($errors->has('omni'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('omni') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                                <label for="tipo_cita" class="col-md-8 control-label">Consecutivo/Primera vez</label>
                                <div class="col-md-12">
                                    <select id="cortesia1" name="tipo_cita" class="form-control input-sm" >
                                        <option  @if(old('tipo_cita')=="1"){{"selected"}}@elseif($agenda->tipo_cita=="1"){{"selected"}}@endif value="1">Consecutivo</option>
                                        <option  @if(old('tipo_cita')=="0"){{"selected"}}@elseif($agenda->tipo_cita=="0"){{"selected"}}@endif value="0" >Primera vez</option>
                                    </select>
                                    @if ($errors->has('tipo_cita'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tipo_cita') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-3 {{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-12 control-label">Edad</label>
                                <div class="col-md-12">
                                    <input id="Xedad" type="text" class="form-control input-sm" name="Xedad"  required readonly>
                                    @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div style="padding: 0;" class="form-group col-md-3 {{ $errors->has('paciente_dr') ? ' has-error' : '' }}">
                                <label style="padding: 0;" for="paciente_dr" class="col-md-12 control-label">Paciente del Dr(a).</label>
                                <div class="col-md-12">
                                    <input type="checkbox" id="paciente_dr" name="paciente_dr" value="1" class="flat-green" @if(old('paciente_dr')=="1") checked @elseif($agenda->paciente_dr=='1') checked @endif>
                                    @if ($errors->has('paciente_dr'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('paciente_dr') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group col-md-6 {{ $errors->has('solo_robles') ? ' has-error' : '' }}">

                                <div class="col-md-2">
                                    <input type="checkbox" class="flat-purple" id="solo_robles" name="solo_robles" value="1"  @if(old('solo_robles')=="1") checked @elseif($agenda->solo_robles=='1') checked @endif>
                                    @if ($errors->has('solo_robles'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('solo_robles') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label style="padding: 0; color: purple;" for="supervisa_robles" class="col-md-10 control-label">SOLO Dr. ROBLES</label>
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





                            @else
                            @if($agenda->proc_consul=='1')
                            <!--id_procedimiento-->
                            <div class="form-group col-md-12 ">
                                <label for="id_procedimiento" class="col-md-12 control-label">Procedimientos</label>
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                @foreach($procedimientos as $procedimiento)
                                                    @if($agenda->id_procedimiento==$procedimiento->id) <td>{{$procedimiento->nombre}}</td> @endif @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) @if($agendaproc->id_procedimiento==$procedimiento->id) <td>{{$procedimiento->nombre}}</td> @endif @endforeach @endif
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            <input type="hidden" id="espid" name="espid" value="{{$agenda->espid}}">
                            <input type="hidden" id="id_seguro" name="id_seguro" value="{{$agenda->id_seguro}}">
                            <input type="hidden" id="est_amb_hos" name="est_amb_hos" value="{{$agenda->est_amb_hos}}">
                            <input type="hidden" id="id_empresa" name="id_empresa" value="{{$agenda->id_empresa}}">



                            <!--cambiar a produ 7/11/2017-->
                            <!--especialidad-->
                            <div class="form-group col-md-12 {{ $errors->has('espid') ? ' has-error' : '' }}" >
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><b>Especialidad</b></td>
                                            <td><b>Seguro</b></td>
                                        </tr>
                                        <tr>
                                            <td>{{Sis_medico\Especialidad::find($agenda->espid)->nombre}}</td>
                                            <td>{{Sis_medico\Seguro::find($agenda->id_seguro)->nombre}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Tipo Ingreso</b></td>
                                            <td><b>Empresa</b></td>
                                        </tr>
                                        <tr>
                                            <td>@if($agenda->est_amb_hos=="0") Ambulatorio @else Hospitalizado @endif</td>
                                            <td>{{Sis_medico\Empresa::find($agenda->id_empresa)->nombrecomercial}}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                            <!--estado cita-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('estado_cita') ? ' has-error' : '' }} has-success">
                                <label for="est_amb_hos" class="col-md-12 control-label">SELECCIONE ACCIÓN A REALIZAR</label>
                                <div class="col-md-12">
                                <select id="estado_cita" name="estado_cita" class="form-control input-sm" required>
                                    @php $bandera='0'; @endphp
                                    @if($agenda->estado_cita=='0') @if($agenda->fechaini<=date('Y-m-d H:i')) {{$bandera='1'}}  @endif @endif
                                    @if($agenda->estado_cita!='1')
                                        @if ($bandera=='1')
                                        <option value="">Fecha expirada, seleccionar ...</option>
                                        @elseif($agenda->estado_cita!='4' && $agenda->estado_cita!='3')
                                        <option @if($agenda->estado_cita=='0'){{'selected '}}@endif value="0">Por Confirmar</option>
                                        @endif
                                    @endif
                                    @if ($bandera=='0' && $agenda->estado_cita!='4' && $agenda->estado_cita!='3')
                                        <option @if(old('estado_cita')=='1'){{'selected '}}@elseif($agenda->estado_cita=='1'){{'selected '}}@endif value="1">Confirmar</option>
                                    @endif
                                    @if($agenda->estado_cita!='4')
                                    <option @if(old('estado_cita')=='2'){{'selected '}}@elseif($agenda->estado_cita=='2'){{'selected '}}@endif value="2">Asignar Doctor/Horario</option>
                                    <option @if(old('estado_cita')=='3'){{'selected '}}@elseif($agenda->estado_cita=='3'){{'selected '}}@endif value="3">Suspender</option>
                                    @endif




                                </select>
                                @if ($errors->has('estado_cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado_cita') }}</strong>
                                    </span>
                                @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" id="iempresa" class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                                <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                                <div class="col-md-12">
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                        <option value="" >Seleccione..</option>
                                        @foreach($empresas as $empresa)
                                        <option @if(old('id_empresa')==$empresa->id){{"selected"}}@elseif($agenda->id_empresa==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
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
                            <!--procedencia-->
                            <div style="margin-bottom: 1px;padding: 0;" id="iprocedencia" class="form-group col-md-6 {{ $errors->has('procedencia') ? ' has-error' : '' }} ">
                                <label for="procedencia" class="col-md-12 control-label">Procedencia</label>
                                <div class="col-md-12">
                                    <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="@if(old('procedencia')!=''){{old('procedencia')}}@else{{$agenda->procedencia}}@endif">
                                    @if ($errors->has('procedencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedencia') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endif



                            <!--Doctor-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_doctor1') ? ' has-error' : '' }} {{ $errors->has('id') ? ' has-error' : '' }}" id="div_doctor">
                                <label for="id_doctor1" class="col-md-12 control-label">Doctor</label>
                                <div class="col-md-12">

                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" onchange="crear_select()" >
                                        <option class="sel" value="">Seleccione...</option>
                                    @foreach ($usuarios as $usuario)
                                        @if($usuario->id!='9666666666')
                                        @if(!($usuario->training == '1' && $agenda->proc_consul == '1'))
                                        <option @if($usuario->id!='1307189140') class="sel" @endif @if(old('id_doctor1')!='') @if(old('id_doctor1') == $usuario->id) {{"selected"}} @endif @endif @if($usuario->id == $agenda->id_doctor1) {{"selected"}} @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @elseif($usuario->id=='1314490929') style="color: blue;" @endif>
                                            @if($usuario->id=='9666666666') DR. AUXILIAR @else {{$usuario->apellido1}} @if($usuario->apellido2!='(N/A)'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} @if($usuario->nombre2!='(N/A)'){{$usuario->nombre2}}@endif @endif
                                        </option>
                                        @endif
                                        @endif
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor1')||$errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}{{ $errors->first('id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                             <!--especialidad-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('espid') ? ' has-error' : '' }}" id="div_espe">
                                <label for="espid" class="col-md-12 control-label">Especialidad</label>
                                <div class="col-md-12">
                                    <select id="espid" name="espid" class="form-control input-sm" >

                                    </select>
                                    @if ($errors->has('espid'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('espid') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!--salas-->
                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                                <div class="col-md-12">
                                    <input id="tid_sala" type="text" class="form-control input-sm" name="tid_sala" value="{{$sala->nombre_sala}} / {{$hospital->nombre_hospital}}" readonly="readonly">
                                    <select id="id_sala" name="id_sala" class="form-control input-sm" required>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
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
                                <label for="nombre_doctor2" class="col-md-12 control-label">Asistente 1</label>
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
                                    <option value="" >Seleccione..</option>
                                @foreach ($usuarios as $usuario)

                                    <option @if($usuario->id==$agenda->id_doctor2){{"selected"}}@endif value="{{$usuario->id}}">Dr(a). {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>

                                @endforeach
                                @foreach ($enfermeros as $usuario)

                                        <option @if($usuario->id==$agenda->id_doctor2){{"selected"}}@endif value="{{$usuario->id}}">Enf. {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>

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
                                <label for="nombre_doctor3" class="col-md-12 control-label">Asistente 2</label>
                                <div class="col-md-12">

                                    <input id="nombre_doctor3" type="text" class="form-control input-sm" name="nombre_doctor3" value="{{$asis2}}" readonly="readonly">
                                    <select id="id_doctor3" name="id_doctor3" class="form-control input-sm" >
                                        <option value="" >Seleccione..</option>
                                        @foreach ($usuarios as $usuario)

                                        <option @if($usuario->id==$agenda->id_doctor3){{"selected"}}@endif value="{{$usuario->id}}">Dr(a). {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>

                                        @endforeach
                                        @foreach ($enfermeros as $usuario)

                                        <option @if(old('id_doctor3')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor3){{"selected"}}@endif value="{{$usuario->id}}">Enf. {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
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

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }} has-warning" >
                                <label for="cortesia" class="col-md-12 control-label">Cortesia</label>
                                <div class="col-md-12">
                                    <select id="cortesia" name="cortesia" class="form-control input-sm" required>
                                        <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                        <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;margin-top: 4px" class="form-group col-md-6" >
                                <label for="triaje" class="col-md-6 control-label" ><span style="font-weight:bold;color:red">Triaje Telefonico</span></label>
                                <input type="checkbox" @if(!is_null($triaje_Check)) checked @endif id="triaje" name="triaje" style="transform: scale(1.5);" onchange="cambio_estado()">
                            </div>
                            <div class="form-group col-md-12" style="margin-bottom: 1px;">
                            </div>

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                                <label class="col-md-12 control-label">Inicio</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('inicio')!=''){{old('inicio')}}@else{{$agenda->fechaini}}@endif" name="inicio" class="form-control pull-right" id="inicio" required onchange="incremento(event)">
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

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-5 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label class="col-md-12 control-label">Fin</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('fin')!=''){{old('fin')}}@else{{$agenda->fechafin}}@endif" name="fin" class="form-control pull-right" id="fin" required >

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

                            <!--
                            <div class="form-group col-md-3 {{ $errors->has('cortesia') ? ' has-error' : '' }}" >
                                <label for="cortesia" class="col-md-12 control-label">Cortesia</label>
                                <div class="col-md-12">
                                    <select id="cortesia" name="cortesia" class="form-control" required>
                                        <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                        <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                    </select>
                                </div>
                            </div>-->




                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                                <label for="observaciones" class="col-md-12 control-label">Observaciones</label>
                                <div class="col-md-12">
                                    <textarea id="observaciones" class="form-control" name="observaciones">@if(!is_null($agenda->observaciones)) {{$agenda->observaciones}} @endif</textarea>
                                    @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $admin = Sis_medico\Paciente_Observaciones::where('id_paciente', $agenda->id_paciente)->first();
                                $obser_admin ="";
                               // dd($admin);
                               if (!is_null($admin)){
                                    $obser_admin = $admin->observacion;
                                }else{
                                    $obser_admin ="";
                                }


                            @endphp

                            <div style="margin-bottom: 1px;padding: 0;" class="form-group col-md-12">
                                <label for="observaciones_admin" class="col-md-12 control-label">Observaciones Administrativa</label>
                                <div class="col-md-12">
                                    <textarea id="observaciones_admin" class="form-control" name="observaciones_admin">{{$obser_admin}}</textarea>
                                </div>
                            </div>
                              <!--Adelantado-->
                        <div id="idad" class="form-group col-md-3 {{ $errors->has('adelantado') ? ' has-error' : '' }} oculto">
                            <label for="adelantado" class="col-md-9 control-label" style="color: red;padding-left: 2;">Por gestionar</label>
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
                            <label for="fecha_val" class="col-xs-8 control-label">Fecha de Validación</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_val" type="text" class="form-control input-sm" name="fecha_val" value="{{$agenda->fecha_val}}"  autofocus>
                            </div>
                            @if ($errors->has('fecha_val'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_val') }}</strong>
                            </span>
                            @endif
                        </div>


                        <!--Código de validación-->
                        <div  id="cod_validacion1" class="form-group col-xs-6{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                            <label for="cod_val" class="col-xs-8 control-label">Código de Validación</label>
                                <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="{{$agenda->cod_val}}" >

                                @if ($errors->has('cod_val'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cod_val') }}</strong>
                                    </span>
                                @endif


                        </div>
                        <div id="cod_validacion2" class="form-group col-xs-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                            <label for="validacion_cv_msp" class="col-md-12 control-label">Código de Validación</label>

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


                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" id="enviar" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-floppy-disk"></span> Aceptar
                                    </button>

                                </div>
                            </div>


                        </form>
                    </div>
                </div>

            </div>

            <div class="col-md-13">
                <div class="box box collapsed-box">
                                <div class="box-header with-border">
                                    <h4 class="col-md-6">@if($agenda->estado_cita!='4')Actualizar @endif Historial Médico</h4>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <br><br>

                                <div class="box-body">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">Listado de Archivos</div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                              <tr>
                                                                <th>Tipo de archivo</th>
                                                                <th>Mostrar</th>
                                                                <th>Subido por:</th>
                                                                <th>Fecha</th>
                                                                <th>Eliminar</th>
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
                                                                            <img src="{{asset('imagenes/img.png')}}" style="width: 50px; height: 50px;" alt="{{$thumbnail->tipo_documento}}">
                                                                        </a>
                                                                        @elseif($extension == "pdf")
                                                                        <a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            <img src="{{asset('imagenes/pdf.png')}}" style="width: 50px; height: 50px;" alt="pdf">
                                                                        </a>
                                                                        @else
                                                                        <a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            <img src="{{asset('imagenes/word.png')}}" style="width: 50px; height: 50px;" alt="word">
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
                                                                    <td><a href="{{ route('preagenda.eliminarfoto', ['id' => $thumbnail->id])}}">
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
                                    <div class="form-group col-md-12">
                                        <label for="archivo" class="col-md-8 control-label">Agregar Historial</label>
                                        <div class="col-md-12" style="width: 100%;">

                                            <form method="POST" action="{{route('agenda.archivo5')}}" class="dropzone" id="addimage" >
                                                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                                                <input type="hidden" name="id" value="{{ $agenda->id }}">
                                            </form>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 {{ $errors->has('hc') ? ' has-error' : '' }}">
                                        <label for="hc" class="col-md-4 control-label">Ingrese Historial en Texto</label>
                                        <div class="col-md-12">
                                            <textarea name="hc" id="hc" rows="10" cols="50" style="width: 100%;" @if($agenda->estado_cita>='4')readonly @endif>@if(!is_null($ar_historiatxt)){{$ar_historiatxt->texto}}@endif</textarea>
                                            @if ($errors->has('hc'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('hc') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
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
                    $mensaje_pre="GENERAR ORDEN EXAMEN PRE-OPERATORIO";
                }else{
                    if($ex_pre->realizado=='0'){
                        $class_pre="callout callout-warning";
                        $mensaje_pre="EXAMEN PRE-OPERATORIO EN PROCESO";
                    }else{
                        $class_pre="callout callout-success";
                        $mensaje_pre="EXAMEN PRE-OPERATORIO REALIZADO";
                    }
                }
                if(is_null($ex_post)){
                    $class_post="callout callout-danger";
                    $mensaje_post="GENERAR ORDEN EXAMEN POST-OPERATORIO";
                }else{
                    if($ex_post->realizado=='0'){
                        $class_post="callout callout-warning";
                        $mensaje_post="EXAMEN POST-OPERATORIO EN PROCESO";
                    }else{
                        $class_post="callout callout-success";
                        $mensaje_post="EXAMEN POST-OPERATORIO REALIZADO";
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



            @if($agenda->nro_reagenda>'0' || !$historia->isEmpty())
            <div class="callout callout-success   col-md-12">
                @if($agenda->nro_reagenda>'0')
                    <p>** La cita ya ha sido reagendada : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif</p>
                @endif


            </div>
            @endif

            <div class="callout callout-success   col-md-12" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
                <p>**<a style="color: blue;" href="{{route('orden.index_admin',['cedula' => $agenda->id_paciente])}}"> Ordenes de Laboratorio </a></p>
            </div>

        </div>
        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary" style="margin-bottom: 2px;">
                <div class="box-body">


                        <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirpdf_resumen', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> Resumen</a>



                        <a href="{{ route('orden.admision', ['id_agenda' => $agenda->id, 'url' => '0']) }}" data-toggle="tooltip" title="Orden de Laboratorio">
                            <button type="button" class="btn btn-warning btn-sm"><span class="ionicons ion-ios-flask"></span> Pública</button>
                        </a>

                        <!--a href="{{ route('orden_particular.crear_particular2',['id' => $agenda->id_paciente])}}" data-toggle="tooltip" title="Orden de Laboratorio">
                            <button type="button" class="btn btn-success btn-sm"><span class="ionicons ion-ios-flask"></span> Privada</button>
                        </a-->


                    @if($agenda->proc_consul=='1')

                        <a href="{{ route('cardiologia.agenda', ['id_agenda' => $agenda->id, 'url' => '0']) }}" >
                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-heart-empty"></span> Cardiología</button>
                        </a>

                    @endif


                        <button type="button" onclick="regresar();" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-chevron-left" data-toggle="tooltip" title="Regresar"></span></button>


                </div>
            </div>
        </div>

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary" style="margin-bottom: 2px;">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span> Observación Médica del Paciente</h4>

                </div>
                <div class="box-body">

                  {{$paciente->observacion}}

                </div>
            </div>
        </div>

        <div class="col-md-5" style="padding-left: 4px;padding-right: 4px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 2px;">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span>Información de Contacto</h4>
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
                            <td><b>Ciudad:</b></td>
                            <td>{{ $paciente->ciudad }}</td>
                        </tr>
                        @endif
                        @if($paciente->direccion!="")
                        <tr>
                            <td><b>Dirección:</b></td>
                            <td>{{ $paciente->direccion }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><b>Email:</b></td>
                            <td>{{ $user_aso->email }}</td>
                        </tr>
                        <tr>
                            <td><b>Teléfono Domicilio:</b></td>
                            <td>{{ $paciente->telefono1 }}</td>
                        </tr>
                        <tr>
                            <td><b>Teléfono Celular:</b></td>
                            <td>{{ $paciente->telefono2 }}</td>
                        </tr>
                        <tr>
                            <td><b>Familiar</b></td>
                            <td>{{  $paciente->nombre1familiar}} @if($paciente->nombre2familiar != '(N/A)' ){{ $paciente->nombre2familiar}}@endif {{ $paciente->apellido1familiar}} @if($paciente->apellido2familiar != '(N/A)'){{ $paciente->apellido2familiar}}@endif</td>
                        </tr>
                        <tr>
                            <td><b>Teléfono Familiar:</b></td>
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
                    @if($agenda->observaciones!=null)<div>Última Observación:<b> {{$agenda->observaciones}}</b></div>@endif
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
                                        <td><b>Fecha</b></td>
                                        <td>{{ substr($log->created_at,0,10) }} {{ substr($log->created_at,11,5) }}</td>
                                        <td><b>{{ substr($log->nombre1,0,1) }}{{ $log->apellido1 }}</b></td>
                                    </tr>

                                    <tr>
                                        <td><b>Descripción</b></td>
                                        <td colspan="2">@if($log->descripcion!=null){{ $log->descripcion }} / @endif @if($log->descripcion2!=null){{ $log->descripcion2 }} / @endif @if($log->descripcion3!='CAMBIO: '){{ $log->descripcion3 }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td><b>Observación</b></td>
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





<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
  tinymce.init({

    selector: '#hc',

    setup:function(ed) {
       ed.on('change', function(e) {
            tinyMCE.triggerSave();
            var mensaje  = document.getElementById('hc').value;
            console.log(document.getElementById('hc2'));
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


    function incremento (e){
        var inicio = document.getElementById("inicio").value;
        var valor = document.getElementById("proc_consul").value;

         if(valor == 0){
            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');

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

    function crear_select () {

        var id_doctor = document.getElementById("id_doctor1").value;
        if(id_doctor!=""){

            var sel_espid = document.getElementById("espid");
            $('option[class^="cl_esp"]').remove();
            @foreach($especialidades as $especialidad)
            if(id_doctor== '{{$especialidad->usuid}}'){

                var option = document.createElement("option");
                option.value = "{{$especialidad->espid}}";
                option.text = "{{$especialidad->enombre}}";
                option.setAttribute("class", "cl_esp");
                sel_espid.add(option);
            }
            @endforeach


            var indice_doc = document.getElementById("id_doctor1").selectedIndex;


            /*var sel_doctor = document.getElementById("sp_doctor");
            sel_doctor.innerHTML = document.getElementById("id_doctor1").options[indice_doc].text;*/

            campos(id_doctor);

        }



    }

    $(function () {
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


    $(document).ready(function()
    {

        ingreso();

        inicializar();

        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{asset('/agenda_procedimiento/pentax_procedimiento')}}">Pentax</li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');

       crear_select();

        var fecha_url = document.getElementById('inicio').value;
        var unix =  Math.round(new Date(fecha_url).getTime()/1000);
        $("#unix").val(unix);
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#modal_recibo_cobro').on('hidden.bs.modal', function(){
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
        //$("#observaciones").attr("readonly","readonly");
        //$("#observaciones").val("");
        $("#observaciones").removeAttr("required");
        $("#inicio").attr("readonly","readonly");
        $("#fin").attr("readonly","readonly");
        $("#id_doctor1").hide();
        $("#div_doctor").hide();
        $("#div_espe").hide();
        $("#nombre_doctor").show();
        $("#id_doctor2").hide();
        $("#nombre_doctor2").show();
        $("#id_doctor3").hide();
        $("#nombre_doctor3").show();
        $("#id_sala").hide();
        $("#tid_sala").show();
        $("#estado_cita").focus();
        //$("#iprocedencia").hide();
        //$("#iempresa").hide();
        $("#procedencia").removeAttr("required");
        $("#id_empresa").removeAttr("required");







        if (estado==2){
            $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("readonly");
                $("#fin").removeAttr("readonly");
                //$("#observaciones").prop("required", true);
                //$("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();
                $("#div_doctor").show();
                $("#div_espe").show();
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
                //$("#iprocedencia").hide();
                //$("#iempresa").hide();
                $("#procedencia").removeAttr("required");
                $("#id_empresa").removeAttr("required");

        }
        @if($agenda->proc_consul=='1')
        if (estado==4){

               //$("#iprocedencia").show();
               //$("#procedencia").attr("required","required");


        }
        @endif
        if (estado==4){
               $("#iempresa").show();
               $("#id_empresa").attr("required","required");
        }



        $("#estado_cita").change(function () {


            var estado = document.getElementById("estado_cita").value;
            //$("#iempresa").hide();
            $("#id_empresa").removeAttr("required");

            if(estado==1 ){

                $("#observaciones").removeAttr("readonly");
                $("#observaciones").removeAttr("required");
                $("#observaciones").val("");
                $("#id_doctor1").hide();
                $("#div_doctor").hide();
                $("#div_espe").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();
                //$("#iprocedencia").hide();
            } else if(estado==3){
                $("#observaciones").removeAttr("readonly");
                //$("#observaciones").val("");
                $("#observaciones").prop("required", true);
                $("#inicio").attr("readonly","readonly");
                $("#fin").attr("readonly","readonly");
                $("#id_doctor1").hide();
                $("#div_doctor").hide();
                $("#div_espe").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();
                //$("#iprocedencia").hide();
                //$("#procedencia").removeAttr("required");
            } else if(estado==2){
                $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("readonly");
                $("#fin").removeAttr("readonly");
                //$("#observaciones").prop("required", true);
                //$("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();
                $("#div_doctor").show();
                $("#div_espe").show();
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
                //$("#iprocedencia").hide();
                //$("#procedencia").removeAttr("required");
            }
             else if(estado==4){
                @if($agenda->proc_consul=='1')
                //$("#iprocedencia").show();
                //$("#procedencia").attr("required","required");
                @endif
                $("#iempresa").show();
                $("#id_empresa").attr("required","required");
             }
            else{
                $("#observaciones").attr("readonly","readonly");
                $("#inicio").attr("readonly","readonly");
                $("#fin").attr("readonly","readonly");
                $("#id_doctor1").hide();
                $("#div_doctor").hide();
                $("#div_espe").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show();

            }

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

        $('#calendar').fullCalendar({

            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            views:{
                agenda:{
                    slotDuration: "00:15:00",
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00"
                }
            },
            events : [
                @foreach($cagenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : 'Dr(a).{{Sis_medico\User::find($value->id_doctor1)->nombre1}} {{Sis_medico\User::find($value->id_doctor1)->apellido1}} | {{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif | {{$value->pnombre1}} {{$value->papellido1}}',
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',

                    @if($value->paciente_dr == 0)
                      @if($value->estado_cita == 0)
                        color: 'black',
                      @else
                        color: '{{ $value->color}}',
                        textColor: 'black',

                      @endif
                  @endif
                  @if($value->paciente_dr == 1)
                      @if($value->estado_cita == 0)
                        color: 'black',
                      @else
                        color: 'red',
                    @endif
                  @endif





                },
                @endforeach

                @foreach($cagenda3 as $value)
                {

                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : 'Dr(a).{{Sis_medico\User::find($value->id_doctor1)->nombre1}} {{Sis_medico\User::find($value->id_doctor1)->apellido1}} | CONSULTA | {{$value->pnombre1}} {{$value->papellido1}}',
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',

                  @if($value->paciente_dr == 0)
                      @if($value->estado_cita == 0)
                        color: 'black',
                      @else
                        color: '{{ $value->color}}',
                        textColor: 'black',

                      @endif
                  @endif

                  @if($value->paciente_dr == 1)
                      @if($value->estado_cita == 0)
                        color: 'black',
                      @else
                        color: 'red',
                    @endif
                  @endif



                },
                @endforeach
                @php $cont =0;@endphp
                @foreach($cagenda2 as $value)
                @if($value->id_doctor1 != null)
                {


                  @php $sala=Sis_medico\sala::find($value->id_sala) @endphp


                    id    : '{{$value->id}} idreunion',
                    className: 'a{{$value->id}} classreunion',
                    title : 'Dr(a).{{Sis_medico\User::find($value->id_doctor1)->nombre1}} {{Sis_medico\User::find($value->id_doctor1)->apellido1}} | REUNIÓN',
                    start : '{{ $value->fechaini }}',
                    end : '{{ $value->fechafin }}',
                    color: '#023f84',



                },
                @endif

                @php $cont++; @endphp
                @endforeach



            ],
            defaultView: 'agendaDay',
                  editable: false,
            selectable: true,
            header: {
                      left: 'prev,next today',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
                  },


        });

        edad2();


        @if($agenda->proc_consul=='1')
            if( $('#solo_robles').prop('checked') ) {
                $("#estado_cita option[value=2]").attr("selected",true);
                $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("readonly");
                $("#fin").removeAttr("readonly");
                //$("#observaciones").val("");
                $("#id_doctor1").show();
                $("#div_doctor").show();
                $("#div_espe").show();
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
                //$("#id_doctor1 option[value=1307189140]").attr("selected",true);
                //$("#id_doctor1 option[class='sel']").hide();
                crear_select();
            }else{
                $("#id_doctor1 option[class='sel']").show();
            }

        @endif


    });

 function campos(id_doctor)
{

    var elemento_to = document.getElementsByClassName("cl_ag");
    $(elemento_to).addClass('oculto');

    var elemento_doc = document.getElementsByClassName("d"+id_doctor);
        $(elemento_doc).removeClass('oculto');

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

            alert("La Fecha de Validacion no debe ser menor a la Fecha Actual")
            $('#fecha_val').val("");

           }

        }

    }


        function valida_check(e)
        {

            var isChecked = document. getElementById('adelantado'). checked;

            if(isChecked == false){

                $('#fecha_val').val(" ");


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
            //$('#fecha_val').val(" ");
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
            //$('#fecha_val').val(" ");
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


var inicializar = function ()
        {


            var jsfecha = document.getElementById('fecha').value;
            var jsunix =  Math.round(new Date(jsfecha).getTime()/1000);

            $.ajax({
                type: 'get',
                url:'{{ url('agenda/consulta_agenda')}}/1307189140/'+jsunix,   //agenda.consulta_ag
                success: function(data){
                    $('#consulta_calendario').empty().html(data);
                }
            })

        }

    function regresar(){

        var fecha_url = document.getElementById('inicio').value;
        var unix =  Math.round(new Date(fecha_url).getTime()/1000);

        location.href = "{{url('pre_agenda/regresar')}}/"+{{$agenda->id}}+"/"+unix+"/0";

    }

    $('input[type="checkbox"].flat-purple').on('ifChecked', function(event){

        $("#estado_cita option[value=2]").attr("selected",true);
        $("#observaciones").removeAttr("readonly");
        $("#inicio").removeAttr("readonly");
        $("#fin").removeAttr("readonly");
        //$("#observaciones").val("");
        $("#id_doctor1").show();
        $("#div_doctor").show();
        $("#div_espe").show();
        $("#nombre_doctor").hide();
        $("#id_doctor2").show();
        $("#nombre_doctor2").hide();
        $("#id_doctor3").show();
        $("#nombre_doctor3").hide();
        $("#id_sala").show();
        $("#tid_sala").hide();
        //$("#id_doctor1 option[value=1307189140]").attr("selected",true);
        //$("#id_doctor1 option[class='sel']").hide();
        crear_select();



    });

    $('input[type="checkbox"].flat-purple').on('ifUnchecked', function(event){

        $("#id_doctor1 option[class='sel']").show();

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
</script>
@include('sweet::alert')
@endsection
