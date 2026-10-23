
@extends('agenda.base')

@section('action-content')

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
 <div class="box">
  <div class="panel panel-default">
                <div class="panel-heading">@lang('agenda.detallecita')</div>
                <div class="panel-body">
                    <div class="form-group col-md-12">
                    <p>
                        <b style="color: red;">@if($agenda->nro_reagenda>'0')** @lang('agenda.citaReagendada') : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{@lang('agenda.vez')}}@else{{@lang('agenda.veces')}}@endif @endif</b>
                        @php 

                            $doctor=Sis_medico\User::find($agenda->id_doctor1);
                            $sala=Sis_medico\Sala::find($agenda->id_sala); 
                            $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                            $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                            $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                            $especialidad=Sis_medico\Especialidad::find($agenda->espid);

                        @endphp
                    
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><b>@lang('agenda.cedulaPaciente'):</b></td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td><b>@lang('agenda.nombrePaciente'): </b></td>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.tipoAgendamiento'):</b></td>
                                    <td>@if($agenda->proc_consul=='0'){{@lang('agenda.consulta')}}
                                        @elseif($agenda->proc_consul=='1'){{'Procedimiento'}}
                                        @else{{@lang('agenda.reuniones')}}
                                        @endif</td>
                                    @if($agenda->proc_consul=='1')    
                                    <td><b>@lang('agenda.procedimientos'): </b></td>
                                    <td>{{Sis_medico\Procedimiento::find($agenda->id_procedimiento)->nombre}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) - {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.tipodeingreso'): </b></td>
                                    <td>@if($agenda->est_amb_hos=='0'){{@lang('agenda.ambulatorio')}}@else{{@lang('agenda.hospitalizado')}}@endif</td>
                                    <td><b>@lang('agenda.seguro'):  </b></td>
                                    <td>{{Sis_medico\Seguro::find($agenda->id_seguro)->nombre}}</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.especialidad'): </b></td>
                                    <td>{{$especialidad->nombre}}</td> 
                                    <td><b>@lang('agenda.estado'):  </b></td>
                                    <td>@if ($agenda->estado_cita=='0')
                                    @lang('agenda.porConfirmar')
                                    @endif
                                    @if ($agenda->estado_cita=='1')
                                    @lang('agenda.confirmada')
                                    @endif   
                                    @if ($agenda->estado_cita=='2')
                                    @lang('agenda.reagendado')
                                    @endif  
                                    @if ($agenda->estado_cita=='3')
                                    @lang('agenda.suspendido')
                                    @endif   
                                    @if ($agenda->estado_cita=='4')
                                    @lang('agenda.asistio-')    
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.fecha'): </b></td>
                                    <td>{{substr($agenda->fechaini, 0, 10)}}</td> 
                                    <td><b>@lang('agenda.hora'):  </b></td>
                                    <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.cortesia'):  </b></td>
                                    <td>{{$agenda->cortesia}}</td>
                                    
                                </tr>
                            </tbody>
                        </table>            

                        
                           

                        
                           

                       

                                           



	
</section>

@include('sweet::alert')
@endsection
