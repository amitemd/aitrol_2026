
@extends('agenda.base')

@section('action-content')

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
 <div class="box">
  <div class="panel panel-default">
                <div class="panel-heading">Detalle de la cita</div>
                <div class="panel-body">
                    <div class="form-group col-md-12">
                    <p>
                        <b style="color: red;">@if($agenda->nro_reagenda>'0')** La cita ya ha sido reagendada : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif @endif</b>
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
                                    <td><b>Cédula del paciente:</b></td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td><b>Nombre del Paciente: </b></td>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>Tipo Agendamiento:</b></td>
                                    <td>@if($agenda->proc_consul=='0'){{'Consulta'}}
                                        @elseif($agenda->proc_consul=='1'){{'Procedimiento'}}
                                        @else{{'Reuniones'}}
                                        @endif</td>
                                    @if($agenda->proc_consul=='1')    
                                    <td><b>Procedimientos: </b></td>
                                    <td>{{Sis_medico\Procedimiento::find($agenda->id_procedimiento)->nombre}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) - {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Tipo de Ingreso: </b></td>
                                    <td>@if($agenda->est_amb_hos=='0'){{'Ambulatorio'}}@else{{'Hospitalizado'}}@endif</td>
                                    <td><b>Seguro:  </b></td>
                                    <td>{{Sis_medico\Seguro::find($agenda->id_seguro)->nombre}}</td>
                                </tr>
                                <tr>
                                    <td><b>Especialidad: </b></td>
                                    <td>{{$especialidad->nombre}}</td> 
                                    <td><b>Estado:  </b></td>
                                    <td>@if ($agenda->estado_cita=='0')
                                        Por Confirmar
                                    @endif
                                    @if ($agenda->estado_cita=='1')
                                        Confirmada
                                    @endif   
                                    @if ($agenda->estado_cita=='2')
                                        Reagendada
                                    @endif  
                                    @if ($agenda->estado_cita=='3')
                                        Suspendida
                                    @endif   
                                    @if ($agenda->estado_cita=='4')
                                        ASISTIÓ    
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td><b>Fecha: </b></td>
                                    <td>{{substr($agenda->fechaini, 0, 10)}}</td> 
                                    <td><b>Hora:  </b></td>
                                    <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                </tr>
                                <tr>
                                    <td><b>Cortesia:  </b></td>
                                    <td>{{$agenda->cortesia}}</td>
                                    
                                </tr>
                            </tbody>
                        </table>            

                        
                           

                        
                           

                       

                                           



	
</section>

@include('sweet::alert')
@endsection
