
@extends('agenda.base')

@section('action-content')

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
                </style>

<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/{{$agenda->id_paciente}}" ></iframe> 
    </div>
  </div>
</div>

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

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Actualiza @if($agenda->proc_consul=='0') CONSULTA @elseif($agenda->proc_consul=='1') PROCEDIMIENTO @else{{'Reuniones'}}@endif del paciente: {{  $agenda->pnombre1}} @if($agenda->pnombre2 != '(N/A)' ){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != '(N/A)'){{ $agenda->papellido2}}@endif</h4>
                    <!--cambiar a produ 7/11/2017-->
                    @php 
                        $doctor=Sis_medico\User::find($agenda->id_doctor1);
                        
                        $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                        $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                        $paciente=Sis_medico\paciente::find($agenda->id_paciente);
                        $user_aso=Sis_medico\user::find($paciente->id_usuario);
                        $alergia = $paciente->alergias;
                        if(($alergia == null )||($alergia == "No" )||($alergia == "" )||($alergia == "NO")||($alergia == "no" )||($alergia == "nO" )){
                            $dato_alergia =  1;
                        }
                        else
                        {
                            $dato_alergia =  2;
                        }
                    @endphp
                    <div class="form-group">
                        <div class="form-group col-md-4 col-sm-4 col-xs-4" >    
                            <a  data-toggle="modal" data-target="#favoritesModal2" >
                                <button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-globe"></span> Cobertura S.Pública</button>
                            </a>
                        </div>
                        @if(!$historia->isEmpty())
                        <div class="form-group col-md-4 col-sm-4 col-xs-4">
                            <a class="btn btn-primary"  id="imprimir_etiquetas" target="_blank" href="{{ route('admision.etiqueta2', ['id' => $agenda->id_paciente, 'seguro' => $historia[0]->id_seguro, 'alergia' => $dato_alergia]) }}" ><span class="glyphicon glyphicon-print"></span> Generar Etiqueta</a>
                        </div>

                        
                        
                        
                        @endif 
                        @if(!$historia->isEmpty())
                        @if($cantidad_doc)
                        <div class="form-group col-md-4 col-sm-4 col-xs-4" >
                        <!--form class="col-md-6 col-sm-6 col-xs-6" role="form" method="POST" action="{{url('controldoc/admision')}}/{{$historia[0]->hcid}}"-->
                            <form role="form" method="POST" action="{{route('controldoc.control_doc')}}" style="padding-left: 0;">    
                                <input type="hidden" name="hcid" value="{{$historia[0]->hcid}}">
                                <input type="hidden" name="url_doctor" value="{{$url_doctor}}">
                                <input type="hidden" id="unix2" name="unix" value=""> 
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                                <button type="submit" id="enviar2" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Documentos</button>
                            </form>
                        </div>
                        @endif
                        
                        @endif  
                    </div>
                </div>    

                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form class="form-vertical" role="form" method="POST" action="{{ route('agenda.update2', ['id' => $agenda->id, 'url_doctor' => $url_doctor]) }}" enctype="multipart/form-data">
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                            <input type="hidden" id="fecha" value="{{date('Y-m-d H:i:s')}}">
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
                            <div class="form-group col-md-12 {{ $errors->has('id_procedimiento') ? ' has-error' : '' }}">
                                <label for="id_procedimiento" class="col-md-12 control-label">Procedimientos</label>
                                <div class="col-md-12">
                                    <select class="form-control select2" multiple="multiple" name="proc[]" data-placeholder="Seleccione los Procedimientos" required style="width: 100%;">
                                        <option selected value="{{$agenda->id_procedimiento}}">{{$procedimientos->find($agenda->id_procedimiento)->nombre}}</option>
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
                            <div class="form-group col-md-6 {{ $errors->has('espid') ? ' has-error' : '' }}" >
                                <label for="espid" class="col-md-12 control-label">Especialidad</label>
                                <div class="col-md-12">
                                    <select id="espid" name="espid" class="form-control" required>
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
                            <div class="form-group col-md-6 {{ $errors->has('id_seguro') ? ' has-error' : '' }}" >
                                <label for="id_seguro" class="col-md-12 control-label">Seguro</label>
                                <div class="col-md-12">
                                    <select id="id_seguro" name="id_seguro" class="form-control" required>
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
                             

                            <!--tipo Ingreso-->
                            <div class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" >
                                <label for="est_amb_hos" class="col-md-12 control-label">Tipo de Ingreso</label>
                                <div class="col-md-12" >
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control" >    
                                        <option @if(old('est_amb_hos')=="0"){{"selected"}}@elseif($agenda->est_amb_hos=="0"){{"selected"}}@endif value="0">Ambulatorio</option> 
                                        <option @if(old('est_amb_hos')=="1"){{"selected"}}@elseif($agenda->est_amb_hos=="1"){{"selected"}}@endif value="1" >Hospitalizado</option>
                                    </select>
                                    @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                    @endif  
                                </div>
                            </div>                        

                            
                            <!--fecha_nacimiento-->
                            <div class="form-group col-md-6 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                                <label class="col-md-12 control-label">Fecha Nacimiento</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" onchange="edad2();" value="@if(old('fecha_nacimiento')!=''){{old('fecha_nacimiento')}}@else{{$paciente->fecha_nacimiento}}@endif" name="fecha_nacimiento" class="form-control pull-right" id="fecha_nacimiento" required >

                                    </div>
                                    @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                    
                                </div>
                            </div>

                            <div class="form-group col-md-6 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                                <label for="tipo_cita" class="col-md-12 control-label">Consecutivo/Primera vez</label>
                                <div class="col-md-12">
                                    <select id="cortesia" name="tipo_cita" class="form-control" >
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

                            <div class="form-group col-md-3 {{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-12 control-label">Edad</label>
                                <div class="col-md-12">
                                    <input id="Xedad" type="text" class="form-control" name="Xedad"  required readonly>
                                    @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-md-3 {{ $errors->has('paciente_dr') ? ' has-error' : '' }}">
                                <label for="paciente_dr" class="col-md-12 control-label">Paciente del Dr(a).</label>
                                <div class="col-md-12">
                                    <input type="checkbox" id="paciente_dr" name="paciente_dr" value="1"  @if(old('paciente_dr')=="1") checked @elseif($agenda->paciente_dr=='1') checked @endif> 
                                    @if ($errors->has('paciente_dr'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('paciente_dr') }}</strong>
                                    </span>
                                    @endif
                                </div>  

                            </div>



                            @else
                            @if($agenda->proc_consul=='1')
                            <!--id_procedimiento-->
                            <div class="form-group col-md-12 " style="margin-bottom: 0px;">
                                <label for="id_procedimiento" class="col-md-12 control-label">Procedimientos</label>
                                <div class="col-md-12" style="padding-right: 0px;padding-left: 0px; margin-bottom: 0px;">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>
                                                {{$procedimientos->find($agenda->id_procedimiento)->nombre}}
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
                            <input type="hidden" id="est_amb_hos" name="est_amb_hos" value="{{$agenda->est_amb_hos}}">
                            <input type="hidden" id="id_empresa" name="id_empresa" value="{{$agenda->id_empresa}}"> 
                            <!--cambiar a produ 7/11/2017-->
                            <!--especialidad-->
                            <div class="form-group col-md-12 {{ $errors->has('espid') ? ' has-error' : '' }}" style="margin-bottom: 0px;">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><b>Especialidad</b></td>
                                            <td><b>Seguro</b></td>
                                        </tr>
                                        <tr>
                                            <td>{{Sis_medico\Especialidad::find($agenda->espid)->nombre}}</td>
                                            <td>@if(!is_null($historia->first())){{$seguros->where('id',$historia->first()->id_seguro)->first()->nombre}}@else{{$seguros->where('id',$agenda->id_seguro)->first()->nombre}}@endif</td>
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
                            <div class="form-group col-md-6 {{ $errors->has('estado_cita') ? ' has-error' : '' }} has-success">
                                <label for="est_amb_hos" class="col-md-12 control-label">ACCIÓN A REALIZAR</label>
                                <div class="col-md-12">
                                <select id="estado_cita" name="estado_cita" class="form-control" required>
                                    @php $bandera='0'; @endphp
                                    
                                    @if($agenda->estado_cita=='0') 
                                        @if($agenda->fechaini<=date('Y-m-d H:i')) 
                                                {{$bandera='1'}}  
                                        @endif 
                                    @endif
                                    @if($agenda->estado_cita=='0')                                 
                                        <option @if($agenda->estado_cita=='0'){{'selected '}}@endif value="0">Por Confirmar</option>
                                    @endif
                                        <!--if ($bandera=='0' && $agenda->estado_cita!='4')-->
                                    @if($agenda->estado_cita>='0' && $agenda->estado_cita<'4')
                                        <option @if(old('estado_cita')=='1'){{'selected '}}@elseif($agenda->estado_cita=='1'){{'selected '}}@endif value="1">Confirmar</option>
                                    @endif
                                    
                                    @if($agenda->estado_cita!='4')    
                                    <option @if(old('estado_cita')=='2'){{'selected '}}@elseif($agenda->estado_cita=='2'){{'selected '}}@endif value="2">Reagendar</option>
                                    @endif

                                    @if($agenda->estado_cita!='-1' && $agenda->estado_cita != '4')
                                    <option @if(old('estado_cita')=='3'){{'selected '}}@elseif($agenda->estado_cita=='3'){{'selected '}}@endif value="3">Suspender</option>
                                    @endif                                    

                                    <!--if($fecha_hoy==$fecha_db || $agenda->estado_cita=='4')-->
                                    @if($agenda->estado_cita != '2' && $agenda->estado_cita != '3' && $agenda->estado_cita != '-1')
                                    <option @if(old('estado_cita')=='4'){{'selected '}}@elseif($agenda->estado_cita=='4'){{'selected '}}@endif value="4">ADMISION</option>
                                    @endif
                                    
                                    @if($agenda->estado_cita != '3' && $agenda->estado_cita != '4')
                                    <option @if(old('estado_cita')=='-1'){{'selected '}}@elseif($agenda->estado_cita=='-1'){{'selected '}}@endif value="-1">NO ASISTE</option>
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
                            
                            @if($agenda->proc_consul=='1')
                            <!--procedencia  -->
                            <div id="iprocedencia" class="form-group col-md-6 {{ $errors->has('procedencia') ? ' has-error' : '' }} ">
                                <label for="procedencia" class="col-md-12 control-label">Procedencia</label>
                                <div class="col-md-12">
                                    <input id="procedencia" type="text" class="form-control" name="procedencia" value="@if(old('procedencia')!=''){{old('procedencia')}}@else{{$agenda->procedencia}}@endif" @if($agenda->estado_cita==4) readonly="readonly" @endif>
                                    @if ($errors->has('procedencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedencia') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                            </div>
                            @endif

                            <!--div id="iempresa" class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                                <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                                <div class="col-md-12">
                                    <select id="id_empresa" name="id_empresa" class="form-control" >
                                        <option value=""  >Seleccione..</option> 
                                        @foreach($empresas as $empresa)    
                                        <option @if(old('id_empresa')==$empresa->id){{"selected"}}@elseif($agenda->id_empresa==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $agenda->proc_consul == "1") {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
                                        @endforeach 
                                    </select>
                                    @if ($errors->has('id_empresa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_empresa') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                            </div>-->
                        
                            <!--Doctor-->
                            <div class="form-group col-md-6 {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label for="id_doctor1" class="col-md-12 control-label">Doctor</label>
                                <div class="col-md-12">
                                    <input id="nombre_doctor" type="text" class="form-control" name="nombre_doctor" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  readonly="readonly">
                                     
                                    <select id="id_doctor1" name="id_doctor1" class="form-control" >
                                    @foreach ($usuarios as $usuario)
                                        <option @if(old('id_doctor1') != '') @if(old('id_doctor1') == $usuario->id) {{"selected"}} @endif @elseif($usuario->id == $agenda->id_doctor1) {{"selected"}} @endif value="{{$usuario->id}}">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
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
                            <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                                <div class="col-md-12">
                                    <input id="tid_sala" type="text" class="form-control" name="tid_sala" value="@if(!is_null($sala)) {{$sala->nombre_sala}} / {{$hospital->nombre_hospital}} @endif" readonly="readonly">
                                    <select id="id_sala" name="id_sala" class="form-control" required>
                                        <option value="">Seleccionar...</option>
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
                            <div class="form-group col-md-6 {{ $errors->has('id_doctor2') ? ' has-error' : '' }}">
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
                                <input id="nombre_doctor2" type="text" class="form-control" name="nombre_doctor2" value="{{$asis1}}" readonly="readonly">
                                <select id="id_doctor2" name="id_doctor2" class="form-control" >
                                    <option value="" >Seleccione..</option>
                                @foreach ($usuarios as $usuario)
                                    @if($doctor->id != $usuario->id)
                                    <option @if(old('id_doctor2')==$usuario->id){{"selected"}}@elseif($usuario->id==$agenda->id_doctor2){{"selected"}}@endif value="{{$usuario->id}}">Dr(a). {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
                                    @endif
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
                            <div class="form-group col-md-6 {{ $errors->has('id_doctor3') ? ' has-error' : '' }}">
                                <label for="nombre_doctor3" class="col-md-12 control-label">Asistente 2</label>
                                <div class="col-md-12">

                                    <input id="nombre_doctor3" type="text" class="form-control" name="nombre_doctor3" value="{{$asis2}}" readonly="readonly">
                                    <select id="id_doctor3" name="id_doctor3" class="form-control" >
                                        <option value="" >Seleccione..</option>    
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

                            <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
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
                            <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
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

                             


                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                                <label for="observaciones" class="col-md-12 control-label">Observaciones</label>
                                <div class="col-md-12">
                                    <textarea maxlength="200" id="observaciones" class="form-control" name="observaciones">{{old('observaciones')}}</textarea>
                                    @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <input type="hidden"  value="{{$id_doc}}" name="id_consultadr"> 
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
            <div class="col-md-12">
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
                                                                    <td><a href="{{ route('agenda.imagen567', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                                            {{$thumbnail->archivo}}
                                                                        </a></td>
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
                                        <label for="archivo" class="col-md-8 control-label">Agregar Historial</label>
                                        <div class="col-md-12">
                                            <form method="POST" action="{{route('agenda.archivo5')}}" class="dropzone" id="addimage" > 
                                                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                                                <input type="hidden" name="id" value="{{ $agenda->id }}">
                                            </form>
                                        </div>
                                    </div>    

                                    <div class="form-group col-md-12 {{ $errors->has('hc') ? ' has-error' : '' }}">
                                        <label for="hc" class="col-md-4 control-label">Ingrese Historial en Texto</label>
                                        <div class="col-md-12">
                                            <textarea name="hc" id="hc" rows="10" cols="50"  style="width: 100%;" @if($agenda->estado_cita>='4')readonly @endif>@if(!is_null($ar_historiatxt)){{$ar_historiatxt->texto}}@endif</textarea>
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
        <div class="col-md-5">
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
                @if(!$historia->isEmpty())
                    <p>** Paciente ya fue Admisionado</p>
                    @if($ordenes>0)
                        <p>**<a style="color: blue;" href="{{route('orden.index_admin',['cedula' => $historia[0]->id_paciente])}}"> Orden de Examen ya generada @if($ordenes>1)({{$ordenes}})@endif</a></p>
                    @endif
                @endif

            </div>
            @endif 
        </div>

        @php

        $hcid=null;

        if(!$historia->isEmpty()){
            $hcid = $historia[0]->hcid;
        }
               
        @endphp
        
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-body">
                    @if(!$historia->isEmpty())    
                    <div class="col-md-4 col-sm-6 col-xs-6" style="text-align: center;">
                        <a data-toggle="modal" data-target="#cert_med" class="btn btn-primary btn-sm"  href="{{ route('controldoc.form_cert', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> Certificado Médico</a>
                    </div>
                    
                    @endif

                    <div class="col-md-4 col-sm-6 col-xs-6" style="text-align: center;">
                        <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirpdf_resumen', ['id' => $agenda->id]) }}" ><span class="glyphicon glyphicon-print"></span> Resumen</a>
                    </div>  
                    

                    <div class="col-md-4 col-sm-6 col-xs-6" style="text-align: center;">    
                        <a href="{{ route('orden.admision', ['id_agenda' => $agenda->id, 'url' => $url_doctor]) }}" >
                            <button type="button" class="btn btn-primary btn-sm"><span class="ionicons ion-ios-flask"></span> Laboratorio</button>
                        </a>
                    </div>
                    
                </div>    
            </div>    
        </div>
       
        <div class="col-md-5">
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span> Información de Contacto</h4>
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
        <div class="col-md-5">
            
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="glyphicon glyphicon-user"></span> LOG</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div> 
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                      <th>Fecha</th>
                                      <th>Hora</th>
                                      <th>Descripción</th>
                                      <th>Usuario</th>
                                      <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($logs as $log)
                                    <tr >
                                      <td>{{ substr($log->created_at,0,10) }}</a></td>
                                       <td>{{ substr($log->created_at,11,5) }}</a></td>
                                      <td>@if($log->descripcion!=null){{ $log->descripcion }} / @endif @if($log->descripcion2!=null){{ $log->descripcion2 }} / @endif @if($log->descripcion3!='CAMBIO: '){{ $log->descripcion3 }} @endif</a></td>
                                      <td>{{ substr($log->nombre1,0,1) }}{{ $log->apellido1 }}</a></td>
                                      <td>{{ $log->observaciones }}</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>        
            </div>
        </div>    
         
        <div class="col-md-5">
            <div class="box box-primary" id="consulta_calendario">
                       
            </div>
        </div>

    </div>        
</div>





<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
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
    
    function incremento (e){
        var inicio = document.getElementById("inicio").value;
        var valor = document.getElementById("proc_consul").value;

         if(valor == 0){
            @if($agenda->id_doctor1=='1314490929')
            var fin = moment(inicio).add(20, 'm').format('YYYY/MM/DD HH:mm');
            @else
            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            @endif
            
            $("#fin").val(fin);
         }
         if(valor == 1){

            var fin = moment(inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
            
            $("#fin").val(fin);
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
            incremento(event);
        });
        $("#fin").on("dp.change", function (e) {
            $('#inicio').data("DateTimePicker").maxDate(e.date);
        });
       
    });
    
    $('#cert_med').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

    $(document).ready(function() 
    {
        inicializar();

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
        //$("#observaciones").attr("readonly","readonly"); ///14/08
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
                //$("#observaciones").val("");
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



        
    });


/*function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}*/

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
           

</script>
@include('sweet::alert')
@endsection
