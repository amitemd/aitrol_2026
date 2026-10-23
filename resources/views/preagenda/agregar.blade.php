
@extends('agenda.base')

@section('action-content')

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

    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
        font-size: 12px;
    } 
    .box-header, .panel-body, .box-body{
        padding: 1px;
    }
    .box{
        margin-bottom: 1px;
    }
    div.form-group{
        margin-bottom: 1px;
        padding-left: 1px;
        padding-right: 1px;
    }
    .select2-search__field{
        margin-top: 0px !important;
    }

</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<!--div class="modal fade" id="verificacion" tabindex="0" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" ></iframe> 
    </div>
  </div>
</div-->

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
    @if($citas == '[]' )
        @php $citas = array(); @endphp
    @endif
    @if($citas != array())
    <div class="box box-success">
        <div class="box-header with-border">
            <h4 class="box-title">Agendas para el Paciente</h4> 
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>   
        </div>
        <div class="box-body no-padding"> 
            <div  class="table-responsive col-md-12">
                <table class="table table-striped">
                    <thead>
                        <!--th>
                            <div class="callout callout-danger">
                                El Paciente tiene {{$citas->count()}} agenda(s) para el día de hoy !!
                            </div>
                        </th-->
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Doctor</th>
                        <th>Estado</th>
                        <th>Agdo.por</th>
                        <th>Modf.por</th>
                        <th>Acción</th>

                    </thead>
                    <tbody style="background-color: #ffebe6;">
                        @foreach($citas as $cita)
                            @if($cita->proc_consul<2)            
                            <tr >  
                                @php 
                                    $ya_agendado = false;
                                    $futura_fecha = 0;
                                    if(Date('Y-m-d',strtotime($hora))==substr($cita->fechaini,0,10)){
                                        $ya_agendado = true;
                                    }
                                    if(Date('Y-m-d',strtotime($hora))< substr($cita->fechaini,0,10)){
                                        $futura_fecha = 1;
                                    }
                                @endphp
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>{{substr($cita->fechaini,0,10)}} </span>@if(Date('Y-m-d',strtotime($hora))==substr($cita->fechaini,0,10)) <span style="background-color: red ; color: white;font-size: 14px;padding-left: 2px;padding-right: 2px;"><b> YA AGENDADO PARA HOY!! </b></span> @endif</a></td>
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>{{substr($cita->fechaini,10)}}</span></a></td>
                                @php $procs = Sis_medico\AgendaProcedimiento::where('id_agenda',$cita->id)->get(); @endphp
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>@if($cita->proc_consul==0) CONSULTA @elseif($cita->proc_consul==1) @if($cita->id_procedimiento!=null){{Sis_medico\Procedimiento::find($cita->id_procedimiento)->observacion}} @endif @foreach($procs as $px) + {{Sis_medico\Procedimiento::find($px->id_procedimiento)->observacion}} @endforeach @endif</span></a></td>
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>{{$cita->nombre1}}{{$cita->apellido1}}</span></a></td>
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>@if($cita->estado_cita=='0'){{'Por Confirmar'}}@elseif($cita->estado_cita=='1'){{'Confirmado'}}@elseif($cita->estado_cita=='-1'){{'No Asiste'}}@elseif($cita->estado_cita=='3'){{'Suspendido'}}@elseif($cita->estado_cita=='4'){{'Asistió'}}@elseif($cita->estado_cita=='2')@if($cita->estado=='1'){{'Completar Datos'}}@else{{'Reagendar'}}@endif @endif</span></a></td>
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>{{substr($cita->ucnombre1,0,1)}}{{$cita->ucapellido1}}</span></a></td>
                                <td><a href="{{ route('consultam.detalle_ag',['id' => $cita->id, 'unix' => $unix]) }}"><span @if($ya_agendado) style="color: red;font-size: 15px;font-weight: bold;" @endif @if($futura_fecha) style="color: green;font-size: 15px;" @endif>{{substr($cita->umnombre1,0,1)}}{{$cita->umapellido1}}</span></a></td>
                                <td>@if($cita->estado_cita=='2' || $cita->estado_cita=='-1'|| $cita->estado_cita=='3')@if($cita->id_doctor1!=null)<a href="{{ route('agenda.edit2', ['id' => $cita->id, 'doctor' => $cita->id_doctor1])}}" class="btn btn-warning btn-xs">Reagendar</a>@else<a href="{{ route('preagenda.edit', ['id' => $cita->id])}}" class="btn btn-warning btn-xs">Reagendar</a>@endif @endif</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody> 
                </table>
            </div> 
        </div>
    </div>            
    @endif
    @if($ordenes == '[]' )
        @php $ordenes = array(); @endphp
    @endif

    @if($ordenes != array())
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4 class="box-title">Ordenes de Procedimiento</h4> 
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>   
        </div>
        <div class="box-body no-padding">            
            
            
            <div  class="table-responsive col-md-12">
                <table class="table table-striped">
                    <thead>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Procedimientos</th>
                        <th>Doctor</th>
                        <th>Acción</th>
                    </thead>
                    <tbody >
                        @foreach($ordenes as $orden)          
                            <tr >      
                                <td >{{substr($orden->fecha_orden,0,10)}}</td>
                                <td>@if($orden->tipo_procedimiento=='0') ENDOSCÓPICO @elseif($orden->tipo_procedimiento=='1') FUNCIONAL @else IMÁGENES @endif</td>
                                <td>
                                    @if(!is_null($orden->orden_tipo)) 
                                        @foreach($orden->orden_tipo as $tipo) 
                                            @php 
                                              $pflag = 0; 
                                            @endphp 
                                            @foreach($tipo->orden_procedimiento as $proc) 
                                                @if(!$pflag) 
                                                  @php 
                                                    $pflag=1; 
                                                  @endphp 
                                                  <span style="font-size: 14px;margin-right: 5px;" class="label pull-left @if($proc->id_agenda==null)bg-red @else bg-green @endif">
                                                @else 
                                                    @if($proc->procedimiento->id_grupo_procedimiento != null)
                                                      </span> <span style="font-size: 14px;margin-right: 5px;" class="label pull-left @if($proc->id_agenda==null)bg-red @else bg-green @endif">
                                                    @else
                                                       +
                                                    @endif  
                                                @endif 
                                                {{$proc->procedimiento->observacion}} 
                                            @endforeach 
                                            </span> 
                                        @endforeach 
                                    @endif
                                </td>
                                <td>{{$orden->doctor->apellido1}} {{$orden->doctor->nombre1}}</td>
                                <td>@if($orden->tipo_procedimiento=='0')<a href="{{ route('imprimir.ordenhc4_endoscopica', ['id' => $orden->id])}}" class="btn btn-primary btn-xs" target="_blank">Descargar</a>@elseif($orden->tipo_procedimiento=='1')<a href="{{ route('imprimir.ordenhc4_funcional', ['id' => $orden->id])}}" class="btn btn-primary btn-xs" target="_blank">Descargar</a>@else <a href="{{ route('imprimir.ordenhc4_imagenes', ['id' => $orden->id])}}" class="btn btn-primary btn-xs" target="_blank">Descargar</a> @endif</td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
            </div>    
            
        </div> 
    </div>
    @endif 

    @if(!is_null($paciente_obser))
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4 class="box-title">Observaciones Administrativas del paciente</h4> 
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>   
        </div>
        <div class="box-body no-padding">       
            <div  class="table-responsive col-md-12">
                <table class="table table-striped">
                    <thead>
                        <th>Observaciones</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                               {{$paciente_obser->observacion}} 
                            </td>
                        </tr>
                    </tbody>
                   
                </table>
            </div>    
            
        </div> 
    </div>
    @endif

    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-4"><h4> AGREGAR NUEVO PROCEDIMIENTO </h4></div>
                <div class="form-group col-md-4">
                    <a class="btn btn-primary btn-sm" href="{{ route('agenda.paciente', ['id' => '1', 'i' => '0', 'fecha' => $unix, 'sala' => $sala2])}}" ><span class="glyphicon glyphicon-user"> </span> Agregar nuevo Paciente</a>
                </div>
                 <div class="form-group col-md-4">
                    <a class="btn btn-primary btn-sm" href="{{ route('paciente.buscaxnombre', ['id_doc' => '0', 'fecha' => $unix, 'sala' => $sala2])}}" ><span class="glyphicon glyphicon-search"></span> Paciente Por Nombre</a>
                </div>
            </div>
        </div>
    
        <div class="box-body">
            
                <div class="panel-body">
                
                    <form class="form-vertical" role="form" method="POST" action="{{ route('preagenda.store') }}" enctype="multipart/form-data" id="formulario_agenda">
                       
                        <input type="hidden" class="form-control input-sm" name="hospital_nombre" id="hosnombre" value="{{old('hospital_nombre')}}">
                        <input type="hidden" class="form-control input-sm" name="hospital_direccion" id="hosdireccion" value="{{old('hospital_direccion')}}">
                        <input type="hidden" class="form-control input-sm" name="consultorio_nombre" id="connombre" value="{{old('consultorio_nombre')}}">
                        <input type="hidden" class="form-control input-sm" name="unix" id="unix" value="{{$unix}}">
                       
                        <input type="hidden" class="form-control input-sm" name="procedimiento_nombre" id="pronombre" value="{{old('procedimiento_nombre')}}">
                        {{ csrf_field() }}
                        <!--cedula-->
                        <a style="display: none;" id="mienlace"></a>
                        <div id="cambio4" class="form-group col-md-2 {{ $errors->has('id_paciente') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-12 control-label">Cédula</label>
                            <div class="col-md-12">
                                <input id="idpaciente" maxlength="10" type="text" class="form-control input-sm" name="id_paciente" value="@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" onchange="teclaEnter2(event);" autofocus >
                                @if ($errors->has('id_paciente'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_paciente') }}</strong>
                                </span>
                                    @endif
                            </div>
                        </div>
                        <!--nombre1-->
                        <div class="form-group col-md-4 {{ $errors->has('nombre1') ? ' has-error' : '' }} has-success" id="cambio5">
                            <label for="nombre1" class="col-md-12 control-label">Nombre</label>
                            <div class="col-md-12" style="padding-left: 0;">
                                <input type="hidden" class="form-control input-sm" name="nombre_paciente" value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" >
                                <input id="nombre1" type="text" class="form-control input-sm" disabled="disabled"  value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required >
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio17" class="form-group col-md-2 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}" >
                            <label class="col-md-12 control-label">Fecha Nacimiento</label>
                            <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" onchange="edad2();" value="@if( old('fecha_nacimiento')!=''){{ old('fecha_nacimiento') }}@elseif($paciente != Array()){{$paciente->fecha_nacimiento}}@endif" name="fecha_nacimiento" class="form-control input-sm" id="fecha_nacimiento" required>
                                </div>
                                   @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                       
                        

                        <!--div id="cambio15b" class="form-group col-md-3  ">  
                            <label for="nombreseguro" class="col-md-12 control-label">Seguro</label>
                            <div class="col-md-12" style="padding-left: 5px;padding-right: 0;">
                                <input id="nombreseguro" type="text" class="form-control input-sm" name="nombreseguro"  value="@if($paciente != Array()){{Sis_medico\Seguro::find($paciente->id_seguro)->nombre}}@endif" readonly>
                            </div>
                        </div-->

                        <div id="cambio15b" class="form-group col-md-2 " >
                            <label for="id_seguro" class="col-md-12 control-label" >Seguro</label>
                            <div class="col-md-12" >
                                <select id="id_seguro" name="id_seguro"  onchange="valida_seguro(event);" class="form-control input-sm" required>
                                    @foreach ($seguro as $seguro)
                                        
                                        <option  @if(old('id_seguro')!='') @if(old('id_seguro')==$seguro->id) selected  @endif @else @if($paciente != Array())  @if($paciente->id_seguro == $seguro->id) selected  @endif @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>    
                                        
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div> 

                        <div id="cambio15" class="form-group col-md-2 {{ $errors->has('Xedad') ? ' has-error' : '' }}">
                            <label for="Xedad" class="col-md-12 control-label">Edad</label>
                            <div class="col-md-6">
                                <input id="Xedad" type="text" class="form-control input-sm" name="Xedad"  required readonly>
                                @if ($errors->has('Xedad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Xedad') }}</strong>
                                </span>
                                @endif
                            </div>    
                        </div>

                        <!--div id="cambio15a" class="form-group col-md-6  ">
                            <label for="cortesia" class="col-md-12 control-label">Cortesia</label>
                            <div class="col-md-12">
                                <input id="cortesia" type="text" class="form-control input-sm" name="cortesia"  value="@if(!is_null($cortesia_paciente)) {{$cortesia_paciente->cortesia}} @else NO @endif" readonly>
                            </div>
                        </div-->  
                            
                        <div id="cambio15a" class="form-group col-md-2 {{ $errors->has('cortesia') ? ' has-error' : '' }} has-warning" >
                            <label for="cortesia" class="col-md-12 control-label">Cortesia</label>
                            <div class="col-md-12">
                                <select id="cortesia" name="cortesia" class="form-control input-sm" required>
                                    <option @if(!is_null($cortesia_paciente)) @if($cortesia_paciente->cortesia=='NO') selected @endif @endif value="NO">NO</option>
                                    <option @if(!is_null($cortesia_paciente)) @if($cortesia_paciente->cortesia=='SI') selected @endif @endif value="SI">SI</option>
                                </select>    
                            </div>
                        </div> 

                        <div id="iempresa" class="form-group col-md-2 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                            <label for="id_empresa" class="col-md-12 control-label" >Empresa</label>
                            <div class="col-md-12" style="padding-left: 0;">
                                <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
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
                         
                        <!--proc_consul-->
                        <div class="form-group col-md-2 {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-12 control-label">Tipo</label>
                            <div class="col-md-12">
                                <select id="proc_consul" name="proc_consul" class="form-control input-sm" onchange="campos();">
                                    <option value="1" selected >Procedimiento</option> 
                                </select>  
                                @if ($errors->has('proc_consul'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proc_consul') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                        <!--pais-->
                        <div class="form-group col-md-2 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                            <div class="col-md-12">
                            <select id="id_sala" name="id_sala" class="form-control input-sm" required onchange="salas();" >
                                    <!--option value="">Seleccione..</option-->
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}}@endif @if($sala2 ==$sala->id){{"selected"}}@endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        

                           
                        <!--seguro-->
                        <!--input id="id_seguro" name="id_seguro" value="@if($paciente != Array()){{$paciente->id_seguro}}@endif" type="hidden" -->
                        
                        <div id="cambio11" class="form-group col-md-2 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                            <label for="tipo_cita" class="col-md-12 control-label">Consec./Prim.vez</label>
                            <div class="col-md-12">
                                <select id="cortesia" name="tipo_cita" class="form-control input-sm" >
                                    <option @if(old('tipo_cita')=="1"){{"selected"}}@endif value="1">Consecutivo</option> 
                                    <option @if(old('tipo_cita')=="0"){{"selected"}}@endif value="0" >Primera vez</option>
                                </select>  
                                @if ($errors->has('tipo_cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo_cita') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group col-md-2 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" id="cambio6">
                            <label for="est_amb_hos" class="col-md-12 control-label">Tipo de Ingreso</label>
                            <div class="col-md-12">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" onchange="ingreso();">
                                    <option @if(old('est_amb_hos')=="0"){{"selected"}}@endif value="0">Ambulatorio</option> 
                                    <option style="color: red;" @if(old('est_amb_hos')=="1"){{"selected"}}@endif value="1" >Hospitalizado</option>
                                </select>  
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-2 {{ $errors->has('omni') ? ' has-error' : '' }} has-warning oculto" id="div_omni">
                            <label for="omni" class="col-md-12 control-label">OMNI</label>
                            <div class="col-md-12" >
                                <select id="omni" name="omni" class="form-control input-sm" >
                                    <option value="">Seleccione ...</option> 
                                    <option @if(old('omni')=="SI"){{"selected"}}@endif value="SI">SI</option> 
                                    <option @if(old('omni')=="NO"){{"selected"}}@endif value="NO" >NO</option>
                                </select>  
                                @if ($errors->has('omni'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('omni') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group col-md-2 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-12 control-label">Inicio</label>
                            <div class="col-md-12" >
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

                        <div class="form-group col-md-2 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-12 control-label">Fin</label>
                            <div class="col-md-12">
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
                        

                        @php $cont=count(old('procedimiento')) @endphp 
                        <div id="cambio3" class="form-group col-md-6 {{ $errors->has('procedimiento') ? ' has-error' : '' }} oculto">
                            <label for="id_procedimiento" class="col-md-12 control-label">Procedimientos </label>
                            <div class="col-md-12" >
                                    <select id="id_procedimiento" class="form-control input-sm select2" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione"
                                    style="width: 100%;" required="required" >
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

                        
                        <div class="form-group col-md-4 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-12 control-label">Observaciones</label>
                            <div class="col-md-12">
                                <input id="observaciones" type="text" class="form-control input-sm" name="observaciones" value="{{old('observaciones')}}" >
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>  
                        </div>
                         
                        
                      <!--Fecha de Validación-->
                        <div id="fecha_validacion1" class="form-group col-md-2{{ $errors->has('fecha_val') ? ' has-error' : '' }} oculto">
                            <label for="fecha_val" class="col-md-12 control-label">Fecha de Validación</label>
                              <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                     </div>
                                  <input id="fecha_val" type="text" class="form-control input-sm" name="fecha_val" value="@if($paciente != Array()) @if(!is_null($paciente->fecha_val)){{$paciente->fecha_val}}@endif @endif" required autofocus>
                              </div>    
                               @if ($errors->has('fecha_val'))
                                  <span class="help-block">
                                    <strong>{{ $errors->first('fecha_val') }}</strong>
                                 </span>
                               @endif
                            </div>
                        </div>

                            <!--Código de validación-->
                            <div id="cod_validacion1" class="form-group col-md-2{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                                <label for="cod_val" class="col-md-12 control-label" style="padding-left: 0;">Código de Validación</label>

                                <div class="col-md-12 " style="padding-left: 5px;padding-right: 0;">
                                    <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="@if($paciente != Array()) @if(!is_null($paciente->cod_val)){{$paciente->cod_val}}@endif @endif">

                                    @if ($errors->has('cod_val'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cod_val') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            
                            </div>
                            <div id="cod_validacion2" class="form-group col-md-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                                <label for="validacion_cv_msp" class="col-md-12 control-label" style="padding-left: 0;">Código de Validación</label>

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
                            
                           
                         <!--Adelantado-->
                            <div id="idad" class="form-group col-md-3 {{ $errors->has('adelantado') ? ' has-error' : '' }} oculto">
                                <label for="adelantado" class="col-md-12 control-label" style="color: red;padding-left: 0;">Adelantado</label>
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
                   
                          
                        

                        @if($i != 0)
                            <div  class="form-group col-md-12 {{ $errors->has('archivo') ? ' has-error' : '' }}">

                                <label for="archivo" class="col-md-12 control-label">Seleccione Historial Médico</label>
                                <div class="col-md-6">
                                    <input class="form-control input-sm" id="archivo" type="file"  name="archivo" value="{{old('archivo')}}" >
                                    @if ($errors->has('archivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </span>
                                    @endif
                                </div>  
                            </div>
                        
                        <div class="form-group col-md-12 {{ $errors->has('hc') ? ' has-error' : '' }}">
                            <label for="hc" class="col-md-12 control-label">Copie el Historial Médico</label>
                            <div class="col-md-12" >
                                <textarea  name="hc" id="hc" rows="5" cols="50" ></textarea>
                                @if ($errors->has('hc'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('hc') }}</strong>
                                </span>
                                @endif
                            </div>  
                        </div>
                        @endif  
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" id="enviar_formulario" class="btn btn-primary">
                                    Agendar
                                </button>
                                @if($paciente != Array())
                                <!--a  data-toggle="modal" data-target="#verificacion">
                                    <button type="button" class="btn btn-primary" >
                                        Cobertura Salud Pública 
                                    </button>
                                </a-->
                                <div class="col-md-2 col-sm-4 col-xs-4" style="padding: 1px;">    
                                    <a class="btn btn-warning btn-xs agbtn" href="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/{{$paciente->id}}" target="_blank"><span class="glyphicon glyphicon-globe"> </span> C. Pública</a>
                                </div>
                                @endif
                            </div>
                        </div>  
                    </form>   
                </div>
                

        </div>
    </div>

</section>
<script>
  tinymce.init({
    selector: '#hc'
  });
  </script>
<script>    
    
    
    $(document).ready(function() 
    {
        ingreso();
        $('#enviar_formulario').click(function(){
            $('#formulario_agenda').submit();            
            $(this).attr('disabled', 'disabled');
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
        $('.select2').select2({
            tags: false
        });

        var valor = document.getElementById("proc_consul").value;
        

        $("select").on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);

                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });    
        
        

        $('#favoritesModal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
        });

        edad2();

        campos();
    });
</script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
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
    
 $(document).ready(function() {

    $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{asset('/agenda_procedimiento/pentax_procedimiento')}}">Pentax</li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');


    $('#alternar-respuesta-ej5').toggle( 
       // Primer click
        function(e){ 
            $('#respuesta-ej5').slideDown();
            $(this).text('Ocultar respuesta');
            e.preventDefault();
        }, // Separamos las dos funciones con una coma
      
        // Segundo click
        function(e){ 
            $('#respuesta-ej5').slideUp();
            $(this).text('Ver respuesta');
            e.preventDefault();
        }
  
    );
 });

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
        }else if(id_seguro == '2'){
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


function veractivas(e, suspendidas)
{
    alert(suspendidas);
    var boton1 =document.getElementsByClassName("suspendidas");
    if(suspendidas == 0){
        $(boton1).text('Ver Activas');
        var suspendidas=1;
        alert(suspendidas);   
    }
    if(suspendidas == 1){
        $(boton1).text('Ver Suspendidas');
        var suspendidas=0; 
        alert(suspendidas);  
    }
    
}



function teclaEnter2(e)
{
    vcedula = document.getElementById("idpaciente").value;
       
        vcedula =  vcedula.trim();
        if (vcedula != ""){
              location.href ="{{ route('preagenda.nuevo')}}/{{$unix}}/"+vcedula+"/{{$sala2}}";

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
    var elemento17 = document.getElementById("cambio17");
    var elemento13 = document.getElementById("cambio13");
    var elemento14 = document.getElementById("cambio14");
    var elemento15 = document.getElementById("cambio15");
     var elemento15a = document.getElementById("cambio15a");
    var elemento15b = document.getElementById("cambio15b");
    var elemento16 = document.getElementById("cambio16");
    var inicio = document.getElementById("inicio").value;
    



    if(valor == 0){
        $(elemento1).addClass('oculto');
        $(elemento2).addClass('oculto');
        $(elemento3).addClass('oculto');
        $(elemento4).removeClass('oculto');
        $(elemento5).removeClass('oculto');
        $(elemento6).removeClass('oculto');
        $(elemento7).removeClass('oculto');
        $(elemento8).removeClass('oculto');
        $(elemento11).removeClass('oculto');
        $(elemento13).removeClass('oculto');
        $(elemento14).removeClass('oculto');
        $(elemento15).removeClass('oculto');
         $(elemento15a).removeClass('oculto');
        $(elemento15b).removeClass('oculto');
        $(elemento16).removeClass('oculto');
        $(elemento17).removeClass('oculto');
        var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
        $("#id_procedimiento").removeAttr("required");
        $("#fin").val(fin);


    }
    if(valor == 1){
        $(elemento1).removeClass('oculto');
        $(elemento2).removeClass('oculto');
        $(elemento3).removeClass('oculto');
        $("#id_procedimiento").attr("required","yes");
        $(elemento4).removeClass('oculto');
        $(elemento5).removeClass('oculto');
        $(elemento6).removeClass('oculto');
        $(elemento7).removeClass('oculto');
        $(elemento8).removeClass('oculto');
        $(elemento11).removeClass('oculto');
        var fin = moment(inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
        $("#fin").val(fin);
        $(elemento13).removeClass('oculto');
        $(elemento14).removeClass('oculto');
        $(elemento15).removeClass('oculto');
         $(elemento15a).removeClass('oculto');
        $(elemento15b).removeClass('oculto');
        $(elemento16).removeClass('oculto');
        $(elemento17).removeClass('oculto');
    }
    if(valor == 2){
        $(elemento1).addClass('oculto');
        $(elemento2).addClass('oculto');
        $(elemento3).addClass('oculto');
        $(elemento4).addClass('oculto');
        $(elemento5).addClass('oculto');
        $(elemento6).addClass('oculto');       
        $(elemento7).addClass('oculto');       
        $(elemento8).addClass('oculto');
        $(elemento11).addClass('oculto');
        $("#id_procedimiento").removeAttr("required");

        $(elemento13).addClass('oculto');
        $(elemento14).addClass('oculto');
        $(elemento15).addClass('oculto');
        $(elemento15a).addClass('oculto');
        $(elemento15b).addClass('oculto');
        $(elemento16).addClass('oculto');
        $(elemento17).addClass('oculto');
    }
    
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
            
            $("#fin").val(fjs_fin);
         }
         if(fjs_valor == 1){

            var fjs_fin = moment(fjs_inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
            
            $("#fin").val(fjs_fin);
         }
    }

$(document).ready(function($){
    var ventana_ancho = $(window).width();

    
    if(ventana_ancho > "962" ){
        var nuevovalor = ventana_ancho * 0.8;
    }
    else
    {
        var nuevovalor = ventana_ancho * 0.9;
    }
    $("#frame_ventana").width(nuevovalor);


    @if($paciente != Array())
        var js_cedula = document.getElementById("idpaciente").value;
        
        if(js_cedula!=''){
            
            $("#proc_consul").focus();
        }  
            @endif  

});



</script>
@include('sweet::alert')
@endsection
