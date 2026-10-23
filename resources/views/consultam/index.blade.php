

@extends('consultam.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
/* unvisited link */
a:link {
    color: black;
}

/* visited link */
a:visited {
    color: lightgreen;
}

/* mouse over link */
a:hover {
    color: blue;
}
#consultam_d button{
  width: 100%;
}


</style>
@php 
  $rolUsuario = Auth::user()->id_tipo_usuario;
  $id_fellow = Auth::user()->id; 
@endphp
<div class="container-fluid">
  <div class="row">

    <div class="col-md-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">

        </div>
        <div class="box-body" id="consultam_d">
          <form method="POST" action="{{ route('consultam.search') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="proc_consul" class="col-md-3 control-label">{{trans('econsultam.Tipo')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="proc_consul" id="proc_consul" onchange="buscar();">
                  <option @if($proc_consul=='2') selected @endif value="2" >{{trans('econsultam.Todos')}}</option>
                  <option @if($proc_consul=='0') selected @endif value="0" >{{trans('econsultam.Consultas')}}</option>
                  <option @if($proc_consul=='1') selected @endif value="1" >{{trans('econsultam.Procedimientos')}}</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha" class="col-md-3 control-label">{{trans('econsultam.Desde')}}</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha_hasta" class="col-md-3 control-label">{{trans('econsultam.Hasta')}}</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            @if($proc_consul=='1')
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="pentax" class="col-md-3 control-label">{{trans('econsultam.Unidad')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="pentax" id="pentax" onchange="buscar();"  >
                  <option @if($pentax=='x') selected @endif value="x" >{{trans('econsultam.Todos')}}</option>
                  <option @if($pentax=='0') selected @endif value="0" >{{trans('econsultam.Otros')}}</option>
                  <option @if($pentax=='2') selected @endif value="2" >{{trans('econsultam.Pentax')}}</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_procedimiento" class="col-md-3 control-label" style="font-size: 12px;">{{trans('econsultam.Procedimiento')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_procedimiento" id="id_procedimiento" onchange="buscar();">
                  <option value="">{{trans('econsultam.Todos')}} ...</option>
                @foreach($procedimientos as $procedimiento)
                  <option @if($procedimiento->id==$id_procedimiento) selected @endif value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                @endforeach
                </select>
              </div>
            </div>
            @endif

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">{{trans('econsultam.Cédula')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="{{trans('econsultam.Cédula')}}" >
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">{{trans('econsultam.Paciente')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="{{trans('econtrolsintomas.NombresyApellidos')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>


            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_doctor1" class="col-md-3 control-label">{{trans('econsultam.Doctor')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_doctor1" id="id_doctor1" onchange="buscar();">
                  <option value="">{{trans('econsultam.Seleccione')}} ...</option>
                @foreach($doctores as $doctor)
                  <option @if($doctor->id=='1307189140') style="color:red;" @endif @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}} </option>
                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_seguro" class="col-md-3 control-label">{{trans('econsultam.Seguro')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
                  <option value="">{{trans('econsultam.Seleccione')}}...</option>
                @foreach($seguros as $seguro)
                  <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="espid" class="col-md-3 control-label">{{trans('econsultam.Especialidad')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="espid" id="espid" onchange="buscar();">
                  <option value="">{{trans('econsultam.Todos')}} ...</option>
                @foreach($especialidades as $especialidad)
                  <option @if($especialidad->id==$id_especialidad) selected @endif value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-1 col-xs-4" >
                <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </div>

            @if($proc_consul=='1' && $pentax=='2')
            <div class="form-group col-md-2 col-xs-3" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.pentax')}}"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> {{trans('econsultam.Pentax')}}</button>
            </div>
            @endif

            <!--div class="form-group col-md-2 col-xs-6">
              <a style="color: white;" href="{{ url('consultam/pastelpentax ') }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Estadísticas</a>
            </div-->

            <div class="form-group col-md-2 col-xs-6" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ url('consultam/pastelpentax ') }}"><span class="glyphicon glyphicon-stats" aria-hidden="true"> {{trans('econsultam.Estadísticas')}}</button>
            </div>

            <div class="form-group col-md-1 col-xs-6" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('consultam.vista_reporte_tiempo_sucursal') }}"><span class="fa fa-clock-o" aria-hidden="true"> Tiempos</button>
            </div>

            <!--div class="form-group col-md-1 col-xs-6" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('consultam.reporte_excel') }}"><span class="fa fa-download" aria-hidden="true"> Excel</button>
            </div-->

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.Descargar')}}</button>
            </div>

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.reporte2')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.Info.Paciente')}}</button>
            </div>
          @if(!is_null($id_seguro))
            @if($seguros->find($id_seguro)->tipo=='0')
              <div class="form-group col-md-2 col-xs-6">
                <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('controldoc.reporte_doc')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.Documentos')}}</button>
              </div>
            @endif
          @endif
            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('controldoc.reporte_doc_seguros')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.ProcedimientosSegurosPublicos')}}</button>
            </div>

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('controldoc.reporte_documentos_seguros2')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.ConsultasSegurosPublicos')}}</button>
            </div>

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-success btn-sm" formaction="{{route('masterhc.masterhc')}}">{{trans('econsultam.HistoriaClínica')}}</button>
            </div>

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-success btn-sm" formaction="{{route('consultam.reporte_paquetes')}}"> {{trans('econsultam.Promos')}}</button>
            </div>

            <!--Button Reporte de Biopsias-->
            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.reporte_biopsias')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>{{trans('econsultam.ReportedeBiopsias')}}</button>
            </div>
            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.control_consultas')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>{{trans('econsultam.ControlConsultas')}}</button>
            </div>
            <div class="form-group col-md-2 col-xs-6">
              <a style="color: white;" class="btn btn-primary btn-sm" href="{{route('consulta_ordenes.index')}}"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> {{trans('econsultam.OrdenesdeProcedimientos')}}</a>
            </div>

             
            <div class="form-group col-sm-3 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('consultam.reporte_estados')}}" ><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('econsultam.ReporteProcedimientosSuspendidos')}} </button>
            </div>
             
            @if($rolUsuario==1 or $rolUsuario==20)
            <div class="form-group col-sm-3 col-xs-6">
              <button type="submit" class="btn btn-warning btn-sm" formaction="{{route('reporte.index_costos_planillas')}}" ><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Costos por Procedimientos </button>
            </div>
            @endif
            @if($rolUsuario==1 or $rolUsuario==20)
            <div class="form-group col-sm-3 col-xs-6">
              <button type="submit" class="btn btn-warning btn-sm" formaction="{{route('reportecostosventas.index_costos_ventas')}}" ><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Costos vs Ventas por Procedimientos </button>
            </div>
            @endif
            
            @if($rolUsuario==3)
            <div class="form-group col-md-2 col-xs-6">
              <a style="color: white;" class="btn btn-primary btn-sm" href="{{route('consultam.reporte_fellows',['id_fellow' => $id_fellow])}}"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> {{trans('econsultam.Fellows')}}</a>
            </div>
            @endif
          </form>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th>{{trans('econsultam.Fecha')}}</th>
                    <th>{{trans('econsultam.Hora')}}</th>
                    <th>{{trans('econsultam.Paciente')}}</th>
                    <th>{{trans('econsultam.Cédula')}}</th>
                    <th>{{trans('econsultam.Doctor')}}</th>
                    <th>{{trans('econsultam.Sala')}}</th>
                    @if($proc_consul=='0' ||$proc_consul=='1' || $proc_consul=='2')<th>Procedimientos</th>@endif
                    <th>{{trans('econsultam.Seguro/Convenio')}}</th>
                    <th>{{trans('econsultam.Modifica')}}</th>
                    <th>{{trans('econsultam.Estado')}}</th>
                    <th><span data-toggle="tooltip" title="Ambulatorio/Hospitalizado">{{trans('econsultam.Amb/Hosp')}}</span></th>
                    <th><span data-toggle="tooltip" title="Documentos Pendientes">{{trans('econsultam.P(%)')}}</span></th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($agendas as $agenda)
                @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
                      $p_color1="black"; if($agenda->estado_cita != 0){ if($agenda->paciente_dr == 1) { $p_color1=$agenda->d1color; } else{ $p_color1=$agenda->scolor;} };
                      $p_color2="black"; if($agenda->d1color!=''){ $p_color2=$agenda->d1color;}
                      $historia_clinica = Sis_medico\Historiaclinica::where('id_agenda',$agenda->id)->first();
                      $empresa=null;
                      if($agenda->id_empresa!=null){
                        $empresa = Sis_medico\Empresa::find($agenda->id_empresa);
                      }


                 @endphp
                  <tr >
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->fechaini,0,10) }}</a></td>
                     <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->fechaini,11,5) }}</a></td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->pnombre1 }} @if($agenda->pnombre2=='N/A'||$agenda->pnombre2=='(N/A)') @else{{ $agenda->pnombre2 }} @endif {{ $agenda->papellido1 }} @if($agenda->papellido2=='N/A'||$agenda->papellido2=='(N/A)') @else{{ $agenda->papellido2 }} @endif</a>@if($agenda->vip=='1')<span class="alert-danger" style="padding: 1px;">VIP</span>@endif <span style="padding: 1px;"></span> @if($agenda->paciente_dr=='1')<span class="alert-success" style="padding: 1px;">PART</span>@endif @if($agenda->cortesia=='SI')<span class="alert-warning" style="padding: 1px;">{{trans('econsultam.Cortesía')}}</span>@endif</td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->id_paciente }}</a></td>
                    <td >
                    @if($agenda->proc_consul == '0')
                      @php
                      $hist_clinica = Sis_medico\Historiaclinica::where('id_agenda',$agenda->id)->first();
                      @endphp
                      @if(!is_null($hist_clinica))
                        @if(!is_null($hist_clinica->hc_procedimientos))
                          @if(!is_null($hist_clinica->hc_procedimientos->id_doctor_examinador))
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $hist_clinica->hc_procedimientos->doctor->nombre1 }} {{ $hist_clinica->hc_procedimientos->doctor->apellido1 }}</a>
                          @elseif(!is_null($hist_clinica->id_doctor1))
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $hist_clinica->doctor_1->nombre1 }} {{ $hist_clinica->doctor_1->apellido1 }}</a>
                          @else
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                          @endif
                        @else
                          <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                        @endif  
                      @else
                         <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                      @endif
                    @else
                      <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                    @endif
                    </td>

                    @if($agenda->omni =='OM')
                      @if($agenda->proc_consul =='4')
                        <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->sala_hospital}}</a></td>
                      @endif
                    @else
                      <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->snombre }}</a></td>
                    @endif

                    <!--@if($proc_consul=='1' || $proc_consul=='2')<td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">@if(!is_null($agenda->probservacion)){{$agenda->probservacion}}@else Consulta @endif @if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} @endforeach @endif</a></td>@endif-->
                    
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">
                      @if($agenda->proc_consul=='0')
                        @if($agenda->espid=='8') 
                          INTERCONSULTA CARDIOLOGICA 
                        @else
                          CONSULTA
                        @endif
                      @elseif($agenda->proc_consul=='1')
                          @if(!is_null($agenda->probservacion))
                            {{$agenda->probservacion}}
                          @endif
                          @if(!$agprocedimientos->isEmpty()) 
                            @foreach($agprocedimientos as $agendaproc) 
                            + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} 
                            @endforeach 
                          @endif
                      @else
                        @if($agenda->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                          @if($agenda->omni=='OM')
                            VISITA OMNI
                          @else
                            VISITA
                          @endif
                        @endif
                      @endif
                    </a></td>
                      

                    <!--<td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if(is_null($historia_clinica)){{ $agenda->senombre }}@else {{$historia_clinica->seguro->nombre}} @endif @if($empresa!=null)/ {{$empresa->nombre_corto}}@endif </a></td>-->
                    
                    @php
                        $hc_seguro = null; 
                        $hc_proc = null; 
                        $hc = null;

                        $hc = Sis_medico\historiaclinica::where('id_agenda',$agenda->id)->first();
                        
                        if(!is_null($hc)){
                           $hc_proc = Sis_medico\hc_procedimientos::where('id_hc',$hc->hcid)->first();

                        }
                          
                        if(!is_null($hc_proc)){

                          if($hc_proc->id_seguro!=null){

                            $hc_seguro = Sis_medico\Seguro::find($hc_proc->id_seguro)->nombre;
                          }
                        }
                  
                    @endphp

                    <!--<td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">
                      @if(is_null($historia_clinica))
                        {{ $agenda->senombre }}
                      @else 
                        {{$historia_clinica->seguro->nombre}} 
                      @endif 

                      @if($empresa!=null)
                        /{{$empresa->nombre_corto}}
                      @endif </a></td>-->

                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">
                      
                      @if(is_null($historia_clinica))
                        {{$agenda->senombre }}
                        @if($empresa!=null)
                          /{{$empresa->nombre_corto}} 
                        @endif
                        @if($agenda->consultorio==1 && $agenda->senombre=='IESS')
                        <br/> IESS CONSULTORIO
                        @endif
                        
                      @else 
                        @if($agenda->omni=='OM') 
                          {{$hc_seguro}}
                        @else 
                          {{$agenda->senombre}}
                        @endif 
                        @if($empresa!=null)
                          /{{$empresa->nombre_corto}}
                        @endif 
                        @if($agenda->consultorio==1 && $agenda->senombre=='IESS')
                        <br/> IESS CONSULTORIO
                        @endif
                      @endif
                    </a></td>

                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">{{ substr($agenda->aunombre1,0,1) }}{{ $agenda->auapellido1 }}</a></td>

                    <td @if($agenda->estado_cita=='3' || $agenda->estado_cita=='-1') 
                        style="background-color: #ffcccc;" 
                      @endif>
                      <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">
                      @if($agenda->omni=='OM')
                        @if($agenda->estado_cita=='4')
                          INGRESO
                        @elseif($agenda->estado_cita=='5')
                          ALTA
                        @elseif($agenda->estado_cita=='6')
                          EMERGENCIA  
                        @endif
                      @elseif($agenda->estado_cita=='0')
                        {{'PorConfirmar'}}
                      @elseif($agenda->estado_cita=='1')
                        {{'Confirmado'}}
                      @elseif($agenda->estado_cita=='-1')
                        {{'No Asiste'}}
                      @elseif($agenda->estado_cita=='3')
                        {{'Suspendido'}}
                      @elseif($agenda->estado_cita=='4')
                        @php
                         $pentax = DB::table('pentax')->where('id_agenda', $agenda->id)->first();
                        @endphp
                        @if($pentax != "")
                          @if($pentax->estado_pentax == -1)
                          {{'PRE - ADMISION'}}
                          @elseif($pentax->estado_pentax == 0)
                          {{'EN ESPERA'}}
                          @elseif($pentax->estado_pentax == 1)
                          {{'PREPARACION'}}
                          @elseif($pentax->estado_pentax == 2)
                          {{'EN PROCEDIMIENTO'}}
                          @elseif($pentax->estado_pentax == 3)
                          {{'RECUPERACION'}}
                          @elseif($pentax->estado_pentax == 4)
                          {{'ALTA'}}
                          @elseif($pentax->estado_pentax == 5)
                          {{'SUSPENDIDO'}}
                          @endif
                        @else
                          {{'Asistió'}}
                        @endif
                      @elseif($agenda->estado_cita=='2')
                        @if($agenda->estado=='1')
                          {{'Completar Datos'}}
                        @else
                          {{'Reagendar'}}
                        @endif 
                      @endif</a></td>

                     <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};"> @if($agenda->est_amb_hos=='0') 
                      AMBU 
                     @elseif($agenda->est_amb_hos=='1') 
                      @if($agenda->omni=='SI') 
                        OMNI 
                      @else
                        @if($agenda->omni=='OM') 
                          OMNI 
                        @else
                          HOSP
                        @endif 
                      @endif
                     @endif</a></td>

                    <!--<td @if($agenda->estado_cita=='3' || $agenda->estado_cita=='-1') style="background-color: #ffcccc;" @endif><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if($agenda->estado_cita=='0'){{'Por Confirmar'}}@elseif($agenda->estado_cita=='1'){{'Confirmado'}}@elseif($agenda->estado_cita=='-1'){{'No Asiste'}}@elseif($agenda->estado_cita=='3'){{'Suspendido'}}@elseif($agenda->estado_cita=='4')
                      @php
                       $pentax = DB::table('pentax')->where('id_agenda', $agenda->id)->first();
                      @endphp
                        @if($pentax != "")
                          @if($pentax->estado_pentax == -1)
                          {{'PRE - ADMISION'}}
                          @elseif($pentax->estado_pentax == 0)
                          {{'EN ESPERA'}}
                          @elseif($pentax->estado_pentax == 1)
                          {{'PREPARACION'}}
                          @elseif($pentax->estado_pentax == 2)
                          {{'EN PROCEDIMIENTO'}}
                          @elseif($pentax->estado_pentax == 3)
                          {{'RECUPERACION'}}
                          @elseif($pentax->estado_pentax == 4)
                          {{'ALTA'}}
                          @elseif($pentax->estado_pentax == 5)
                          {{'SUSPENDIDO'}}
                          @endif
                        @else
                          {{'Asistió'}}
                        @endif
                      @elseif($agenda->estado_cita=='2')@if($agenda->estado=='1'){{'Completar Datos'}}@else{{'Reagendar'}}@endif @endif</a></td>-->
                      <!--<td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if($agenda->est_amb_hos=='0') AMBU @elseif($agenda->est_amb_hos=='1') @if($agenda->omni=='SI') OMNI @else HOSP @endif @endif</a></td>-->
                    @php
                    $pct=0;
                    if(array_has($dp_proc, $agenda->id)){
                    $pct=$dp_proc[$agenda->id];
                    }
                    else{
                    $pct=0;
                    }
                    @endphp
                    
                      @if($pct <=25)
                      <td><span class="label pull-right bg-red">{{round($pct,2)}}</span></td>
                      @endif
                      @if($pct >25 && $pct <50)
                      <td><span class="label pull-right bg-yellow">{{round($pct,2)}}</span></td>
                      @endif
                      @if($pct >=50)
                      <td><span class="label pull-right bg-green">{{round($pct,2)}}</span></td>
                      @endif
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>

            <div class="col-md-5 col-xs-12">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('ecamilla.Mostrando')}} {{1+($agendas->currentPage()-1)*$agendas->perPage()}}  / @if(($agendas->currentPage()*$agendas->perPage())<$agendas->total()){{($agendas->currentPage()*$agendas->perPage())}} @else {{$agendas->total()}} @endif {{trans('ecamilla.de')}} {{$agendas->total()}} {{trans('ecamilla.registros')}}</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $agendas->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
              </div>
            </div>

        </div>
      </div>
    </div>
  </div>
</div>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">

  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha}}',

            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });
  });


  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });


function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}


 </script>

@endsection
