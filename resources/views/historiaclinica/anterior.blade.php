
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
                    <h4>Detalle de la cita</h4>
                </div>
                    <div class="box-body">
                                <div class="col-md-12"> 
                                        @php 

                                        $doctor=Sis_medico\User::find($agenda->id_doctor1);
                                        $sala=Sis_medico\Sala::find($agenda->id_sala); 
                                        $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                                        $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                                        $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                                        $especialidad=Sis_medico\Especialidad::find($agenda->espid);

                                        @endphp
                                    <div class="table-responsive col-md-12">    
                                    <table class="table table-striped">    
                                        <tbody>
                                            <tr>
                                                <td><b>Doctor: </b></td>
                                                <td>Dr(a). {{$agenda->dnombre1}} {{$agenda->dapellido1}} {{$agenda->dapellido2}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Tipo Agendamiento:</b></td>
                                                <td>@if($agenda->proc_consul=='0'){{'Consulta'}}
                                                    @elseif($agenda->proc_consul=='1'){{'Procedimiento'}}
                                                    @else{{'Reuniones'}}
                                                    @endif</td>
                                                @if($agenda->proc_consul=='1')    
                                                <td><b>Procedimiento: </b></td>
                                                <td>{{Sis_medico\Procedimiento::find($agenda->id_procedimiento)->nombre}}</td>
                                                @endif    
                                            </tr>
                                            <tr>
                                                <td><b>Tipo de Ingreso:</b></td>
                                                <td>@if($agenda->est_amb_hos=='0'){{'Ambulatorio'}}@else{{'Hospitalizado'}}@endif</td>
                                                <td><b>Especialidad:</b></td>
                                                <td>{{$especialidad->nombre}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Fecha:</b></td>
                                                <td>{{substr($agenda->fechaini, 0, 10)}}</td>
                                                <td><b>Hora: </b></td>
                                                <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Cédula del paciente:</b></td>
                                                <td>{{$agenda->id_paciente}}</td>
                                                <td><b>Nombre del Paciente:  </b></td>
                                                <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                            </tr>
                                            <tr>
                                                <td><b>Seguro:</b></td>
                                                <td>{{$seguro->nombre}}</td>
                                                <td><b>Cortesia:</b></td>
                                                <td>{{$agenda->cortesia}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>

                                    <h4>Datos de la Consulta</h4>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive col-md-12">
                                    <table class="table table-striped">    
                                        <tbody>
                                            <tr>
                                                <td><b>Peso:</b></td>
                                                <td>{{$hca[0]->peso}} (Kg)</td>
                                                <td><b>Altura :</b></td>
                                                <td>{{$hca[0]->altura}} (Cm)</td>
                                            </tr>
                                            <tr>
                                                <td><b>Temperatura :</b></td>
                                                <td>{{$hca[0]->temperatura}} (°C)</td>
                                                <td><b>Presión :</b></td>
                                                <td>{{$hca[0]->presion}} (mm Hg)</td>
                                            </tr>
                                            <tr>
                                                <td><b>Evolución: </b></td>
                                                <td colspan="3">{{$hca[0]->evolucion}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Observaciones: </b></td>
                                                <td colspan="3">{{$hca[0]->observaciones}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Receta </b></td>
                                                <td colspan="3">{{$hca[0]->receta}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                                    
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                                <input type="hidden" name="id" value="{{ $hca[0]->hcid }}"> 
                                
                                
                                
                                
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
 
        $(document).ready(function() {

                $('#favoritesModal2').on('hidden.bs.modal', function(){
                    $(this).removeData('bs.modal');
                });


            })
 
        </script>

