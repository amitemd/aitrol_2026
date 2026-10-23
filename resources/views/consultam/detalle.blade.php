
@extends('consultam.base_detalle')

@section('action-content')
<style type="text/css">

.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}    

</style>

<br>
 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
        
    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="col-md-6">{{trans('econsultam.DETALLEDELPACIENTE')}}</h4>
                    <div class="col-md-3">
                    <a href="{{route('consultam.log_agenda',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> {{trans('econsultam.LogAgenda')}}</button>
                    </a>
                    </div>
                    @if(!is_null($historiaclinica) && $cantidad_doc>0)
                    <div class="col-md-3">
                    <a href="{{route('consultam.consulta_documentos',['hcid' => $historiaclinica->hcid])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span>{{trans('econsultam.Documentos')}}</button>
                    </a>
                    </div>
                    <?php /*<div class="col-md-12">
                        <h4>Descarga de Documento</h4>
                        <div class="col-md-1" style="color: #f2f2f2;">
                        <a type="button" href="{{route('hc_reporte.descargar', ['id_protocolo' => $protocolo->id, 'tipo' => 1])}}"  class="btn btn-primary btn-sm" target="_blank">
                            <span class="glyphicon glyphicon-download-alt">  Imprimir Estudio</span>
                        </a>
                    </div> 
                    </div> */ ?>

                    @endif  
                </div>    
                <div class="box-body">                   
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><b>{{trans('econsultam.Paciente')}}:</b></td>
                                    <td colspan="4">{{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td colspan="2"><b>{{trans('econsultam.CédulaPaciente')}}:</b></td>
                                    <td colspan="2">{{$agenda->id_paciente}}</td>
                                    <td><b>{{trans('econsultam.Edad')}}:</b></td>
                                    <td><span id="edad"></span></td>
                                </tr>
                                <tr>
                                    <td ><b>{{trans('econsultam.Ocupación')}}:</b></td>
                                    <td colspan="4">@if($agenda->ocupacion!=''){{$agenda->ocupacion}}@else{{"No ingresado"}}@endif</td>
                                    <td colspan="2"><b>{{trans('econsultam.EstadoCivil')}}:</b></td>
                                    <td colspan="2">@if($agenda->estadocivil==1) {{"SOLTERO(A)"}} @elseif($agenda->estadocivil==2) 
                                    {{"CASADO(A)"}} @elseif($agenda->estadocivil==3) {{"VIUDO(A)"}} @elseif($agenda->estadocivil==4) {{"DIVORCIADO(A)"}} @elseif($agenda->estadocivil==5) 
                                    {{"UNION LIBRE"}} @elseif($agenda->estadocivil==6) {{"UNION DE HECHO"}} @else {{"No ingresado"}} @endif</td>
                                    <td><b>{{trans('ehistorialexam.Parentesco')}}:</b></td>
                                    <td colspan="2">{{$agenda->parentesco}}</td>
                                </tr>
                                <tr>
                                    <td><b>{{trans('econsultam.Ciudad')}}:</b></td>
                                    <td colspan="4">@if($agenda->ciudad!=''){{$agenda->ciudad}}@else{{"No ingresado"}}@endif</td>
                                    <td colspan="2"><b>{{trans('econsultam.Nacimiento')}}:</b></td>
                                    <td colspan="4">@if($agenda->lugar_nacimiento!=''){{$agenda->lugar_nacimiento}}@else{{"No ingresado"}}@endif</td> 
                                </tr>
                                <tr>
                                    <td><b>{{trans('econsultam.Dirección')}}:</b></td>
                                    <td colspan="8">@if($agenda->direccion!=''){{$agenda->direccion}}@else{{"No ingresado"}}@endif</td>     
                                </tr>
                                <tr>
                                    <td><b>{{trans('econsultam.Teléfono')}}:</b></td>
                                    <td colspan="4">@if($agenda->telefono1!=''){{$agenda->telefono1}}@else{{"No ingresado"}}@endif</td>
                                    <td colspan="2"><b>Celular:</b></td>
                                    <td colspan="4">@if($agenda->telefono2!=''){{$agenda->telefono2}}@else{{"No ingresado"}}@endif</td>     
                                </tr>
                                @if(!is_null($agenda->palergias)&&$agenda->palergias!='')
                                <tr>
                                    <td><b>{{trans('econsultam.Alergias')}}:</b></td>   
                                    <td colspan="10"> {{$agenda->palergias}}</td>     
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_pat))
                                <tr>
                                    <td><b>{{trans('econsultam.AntecedentesPatológicos')}}:</b></td>
                                    <td colspan="10"> {{$agenda->pantecedentes_pat}}</td>      
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_fam))
                                <tr>
                                    <td><b>{{trans('econsultam.AntecedentesFamiliares')}}:</b></td>
                                    <td colspan="10"> {{$agenda->pantecedentes_fam}}</td>      
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_quir))
                                <tr>
                                    <td><b>{{trans('econsultam.AntecedentesQuirúrgicos')}}:</b></td>
                                    <td colspan="10"> {{$agenda->pantecedentes_quir}}</td>     
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>{{trans('econsultam.DETALLEAGENDA')}}</h4> 
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr> 
                                    <td><b>{{trans('ehistorialexam.Estado:')}}</b></td>
                                    <td @if($agenda->estado_cita=='3' || $agenda->estado_cita=='-1') style="color: red; font-weight: bold;" @endif>@if($agenda->estado_cita=='0'){{'Por Confirmar'}}@elseif($agenda->estado_cita=='1'){{'Confirmado'}}@elseif($agenda->estado_cita=='3'){{'Suspendido'}}@elseif($agenda->estado_cita=='-1'){{'No Asiste'}}@elseif($agenda->estado_cita=='4'){{'Asistió'}}@elseif($agenda->estado_cita=='2')@if($agenda->estado==1){{'Completar Datos'}}@else{{'Reagendar'}}@endif @endif</td>    
                                    <td><b>{{trans('econsultam.Tipo:')}}</b></td>
                                    <td>@if($agenda->proc_consul=='0'){{'Consulta'}}@else{{'Procedimiento'}}@endif</td>
                                    <td><b>{{trans('econsultam.Seguro')}}:</b></td>
                                    <td><b>{{$agenda->sanombre}}</b></td>
                                </tr>
                                <tr>
                                    <td><b>{{trans('econsultam.FechaCita:')}}</b></td>
                                    <td>{{ substr($agenda->fechaini, 0, 10)}}</td>
                                    <td><b>{{trans('ehistorialexam.Hora:')}}</b></td>
                                    <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                    <td><b>{{trans('econsultam.Cortesía')}}</b></td>
                                    <td>@if($agenda->cortesia=='SI') SI @else NO @endif</td>
                                    
                                </tr>
                                
                                <tr>
                                    <td><b>{{trans('econsultam.Ingreso:')}}</b></td>
                                    <td>@if($agenda->est_amb_hos=='0') Ambulatorio @else Hospitalizado @if($agenda->omni=='SI') OMNI @endif @endif</td>
                                     <td><b>{{trans('econsultam.Sala:')}}</b></td>
                                    <td colspan="2">@if(!is_null($agenda->slnombre)){{$agenda->slnombre}}/{{$agenda->hsnombre}}@else No Ingresada @endif</td>
                                    <td><b>@if($agenda->tipo_cita=='0')PRIMERA VEZ @else CONSECUTIVO @endif</b></td>  
                                    
                                </tr>
                                <tr>
                                    <td><b>{{trans('ehistorialexam.Doctor:')}}</b></td>
                                    <td colspan="3">@if(!is_null($agenda->id_doctor1)){{$agenda->udnombre}} {{$agenda->udapellido}} @else No asignado @endif</td> 
                                    <td><b>{{trans('econsultam.Especialidad:')}}</b></td>
                                    <td>@if(!is_null($agenda->id_doctor1)){{$agenda->esnombre}}@else No asignado @endif</td> 
                                </tr>
                                @if($agenda->proc_consul=='1')
                                <tr>
                                    <td><b>{{trans('ehistorialexam.Procedimientos:')}}</b></td>
                                    <td colspan="5">{{$agenda->pnombre}} @if(!is_null($agendaprocs)) @foreach($agendaprocs as $agendaproc) + {{$agendaproc->nombre}} @endforeach @endif</td>  
                                </tr>
                                @endif
                                <tr>
                                    <td><b>{{trans('econsultam.Observaciones:')}}</b></td>
                                    <td colspan="5">{{$agenda->observaciones}}</td>  
                                </tr>
                                 <tr>
                                    <td><b>Creado:</b></td> 
                                    <td>{{$agenda->created_at}}</td> 
                                    <td>{{substr($agenda->ucnombre,0,1)}}{{$agenda->ucapellido}}</td> 
                                    <td><b>Modificado:</b></td>
                                    <td>{{$agenda->updated_at}}</td> 
                                    <td>{{substr($agenda->umnombre,0,1)}}{{$agenda->umapellido}}</td> 
                                </tr>
                                
                                


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 

        @if(!is_null($historiaclinica))
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>{{trans('econsultam.DETALLEADMISIÓN')}}</h4> 
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr> 
                                    @if(!is_null($agenda->procedencia))    
                                    <td><b>{{trans('ehistorialexam.Seguro:')}}</b></td>
                                    <td><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>{{trans('econsultam.Procedencia:')}}</b></td>
                                    <td>{{$agenda->procedencia}}</td>
                                    <td><b>{{trans('ehistorialexam.Parentesco')}}:</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @else
                                    <td><b>{{trans('econsultam.Seguro')}}:</b></td>
                                    <td colspan="3"><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>{{trans('ehistorialexam.Parentesco')}}:</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @endif
                                </tr>
                                @if($historiaclinica->parentesco!="Principal")
                                <tr> 
                                    <td><b>{{trans('econsultam.Principal:')}}</b></td>
                                    <td colspan="5">{{$historiaclinica->id_usuario}} - {{$historiaclinica->upapellido1}} @if($historiaclinica->upapellido2!='(N/A)'){{$historiaclinica->upapellido2}}@endif {{$historiaclinica->upnombre1}} @if($historiaclinica->upnombre2!='(N/A)'){{$historiaclinica->upnombre2}}@endif</td> 
                                       
                                </tr>
                                @endif
                                <tr>  
                                    <td><b>{{trans('ehistorialexam.Doctor:')}}</b></td>
                                    <td colspan="2">{{$historiaclinica->d1nombre1}} {{$historiaclinica->d1apellido1}}</td> 
                                    <td><b>{{trans('eplanilla.Empresa:')}}</b></td>
                                    <td colspan="2">{{$agenda->id_empresa}} - {{$agenda->nombrecomercial}}</td>    
                                </tr>
                                
                                 @if(!is_null($pentaxprocs))
                                <tr>
                                    
                                    <td><b>{{trans('ehistorialexam.Procedimientos:')}}</b></td>
                                    @php $flag=false; @endphp
                                    <td colspan="5"> @foreach($pentaxprocs as $pentaxproc) @if($flag) + @endif @php $flag=true; @endphp {{$pentaxproc->nombre}} @endforeach</td>  
                                </tr>
                                @endif  
                                
                                @if(!is_null($historiaclinica->d2apellido1)||!is_null($historiaclinica->d3apellido1))
                                <tr> 
                                    <td><b>{{trans('econsultam.Asistentes:')}}</b></td>
                                    <td>{{$historiaclinica->d2nombre1}} {{$historiaclinica->d2apellido1}} @if(!is_null($historiaclinica->d2apellido1) && !is_null($historiaclinica->d3apellido1) ) + @endif {{$historiaclinica->d3nombre1}} {{$historiaclinica->d3apellido1}}</td>   
                                </tr>
                                @endif
                                 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if(!is_null($historiaclinica->id_pentax))
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>{{trans('econsultam.DETALLEPENTAX')}}</h4> 
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr  >
                                <th >{{trans('econsultam.Fecha-hora')}}</th>
                                <th >{{trans('econsultam.Cambio')}}</th>
                                <th >{{trans('econsultam.Descripción')}}</th>
                                <!--th >Doctor</th>
                                <th >Asistentes</th>
                                <th >Procedimientos</th-->
                                <th >{{trans('econsultam.Seguro')}}</th>
                                <th >{{trans('econsultam.Sala')}}</th>
                                <th >{{trans('econsultam.Modifica')}}</th>
                                <th >{{trans('econsultam.Observación')}}</th>
                            </tr>
                            </thead>
                            <tbody >
                                @foreach ($pentax_logs as $value)
                                <tr >
                                    <td >{{ $value->created_at}}</td>
                                    <td >{{ $value->tipo_cambio}}</td>
                                    <td >{{ $value->descripcion}}</td>
                  
                                    <td >{{ $value->snombre}}</td>
                                    <td >{{ $value->nbrsala}}</td>
                                    <td >{{substr($value->umnombre1,0,1)}}{{ $value->umapellido1}}</td>
                                    <td >{{ $value->observacion}}</td>
                                </tr>
                                @endforeach  
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
        @endif 
        @endif                          
                                  
         
    </div>
</div> 


<script type="text/javascript">
    $(document).ready(function() {
         var edad;
                edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>')+ " años";
                $('#edad').text(edad);
    });            


</script>         

       

    




@include('sweet::alert')
@endsection
