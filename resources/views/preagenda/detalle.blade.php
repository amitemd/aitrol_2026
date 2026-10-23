
@extends('agenda.base')

@section('action-content')


<div class="modal fade fullscreen-modal" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"  >
    <div class="modal-content"  id="imprimir3">
        
    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h4 class="col-md-6">Detalle de la cita</h4><h4 class="col-md-6" style="text-align:right; @if($cant_cortesias>1) color:red; @endif">Cortesias en el día: {{$cant_cortesias}}</h4></div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                    <p><b style="color: red;">@if($agenda->nro_reagenda>'0')** La cita ya ha sido reagendada : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif @endif</b></p>
                    </div>
                        @php


                            $doctor=Sis_medico\User::find($agenda->id_doctor1);
                            $sala=Sis_medico\Sala::find($agenda->id_sala); 
                            $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                            $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                            $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                            $especialidad=Sis_medico\Especialidad::find($agenda->espid);
                        @endphp

                        <div class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }}" >
                                <label for="cortesia" class="col-md-3 control-label">Editar Cortesia</label>
                                <div class="col-md-3">
                                    <select id="cortesia" name="cortesia" class="form-control" required onchange="actualiza(event);">
                                        <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                        <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                    </select>    
                                </div>
                            </div>
                    <div class="table-responsive col-md-12">
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
                            </tbody>
                    </table>
                    </div>
                            
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Historial Clínico del Paciente</h4>   
                </div>
                <div class="box-body" >
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                      <tr>
                                        <th>Fecha</th>
                                        <th>Especialidad</th>
                                        <th>Seguro</th>
                                        <th>Tipo</th>
                                        <th>Doctor </th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($hcp as $value)
                                        @if($value->tipo_cita == 0)
                                        <tr>
                                            <td><a  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->snombre}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}"> Consulta</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @elseif($value->tipo_cita == 1)
                                        <tr>
                                            <td><a  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->snombre}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}"> Procedimientos</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @endif 
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>



<script type="text/javascript">

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

function actualiza(e){
    cortesia = document.getElementById("cortesia").value;
    
    if (cortesia == "SI"){

        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 1])}}";

    }
    else if(cortesia == "NO"){
        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 0])}}";
    }

}  




</script>                     
                           

                        
                           

                       

                                           



	
</section>

@include('sweet::alert')
@endsection
