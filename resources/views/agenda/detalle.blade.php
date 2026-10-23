
@extends('agenda.base')

@section('action-content')

<style type="text/css">
    
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}  


</style>

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
                <div class="box-header with-border">
                    <h4 class="form-group col-md-3 col-sm-3 col-xs-3">@lang('agenda.detallePaciente-')</h4>
                     @if(!is_null($historiaclinica))
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> @lang('agenda.historiaClinica')</button>
                    </a>
                    <!--a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('agenda.detalle2', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('agenda.detalle3', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento vt</button>
                    </a-->
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span>@lang('agenda.atencionProcedimiento')</button>
                    </a>
                    @endif
                    <h4 class="form-group col-md-3 col-sm-3 col-xs-3" style="text-align:right; @if($cant_cortesias>1) color:red; @endif">  @lang('agenda.cortesiaseneldia'): {{$cant_cortesias}}</h4>
                </div>
                <div class="box-body">
                    <div class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }}" >
                        <label for="cortesia" class="col-md-3 control-label">@lang('agenda.editar') @lang('agenda.cortesia')</label>
                        <div class="col-md-3">
                            <select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">
                                <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">@lang('agenda.no')</option>
                                <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">@lang('agenda.si')</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><b>@lang('agenda.paciente'):</b></td>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td><b>@lang('agenda.cedula'): </b></td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td><b>@lang('agenda.edad'):</b></td>
                                    <td><span id="edad"></span></td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.ocupacion'):</b></td>
                                    <td>@if($agenda->ocupacion!=''){{$agenda->ocupacion}}@else{{"No ingresado"}}@endif</td>
                                    <td><b>@lang('agenda.estadoCivil'): </b></td>
                                    <td>@if($agenda->estadocivil==1) {{"SOLTERO(A)"}} @elseif($agenda->estadocivil==2)
                                    {{"CASADO(A)"}} @elseif($agenda->estadocivil==3) {{"VIUDO(A)"}} @elseif($agenda->estadocivil==4) {{"DIVORCIADO(A)"}} @elseif($agenda->estadocivil==5)
                                    {{"UNION LIBRE"}} @elseif($agenda->estadocivil==6) {{"UNION DE HECHO"}} @else {{"No ingresado"}} @endif</td>
                                    <td><b>@lang('agenda.parentesco'):</b></td>
                                    <td>@if(!is_null($agenda->hparentesco)){{$agenda->hparentesco}}@else{{$agenda->pparentesco}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.ciudad'):</b></td>
                                    <td >@if($agenda->ciudad!=''){{$agenda->ciudad}}@else{{"No ingresado"}}@endif</td>
                                    <td ><b>@lang('agenda.ciudadNcaimiento'):</b></td>
                                    <td colspan="3">@if($agenda->lugar_nacimiento!=''){{$agenda->lugar_nacimiento}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.direccion'):</b></td>
                                    <td colspan="5">@if($agenda->direccion!=''){{$agenda->direccion}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.telefono'):</b></td>
                                    <td colspan="2">@if($agenda->telefono1!=''){{$agenda->telefono1}}@else{{"No ingresado"}}@endif</td>
                                    <td ><b>@lang('agenda.celular'):</b></td>
                                    <td colspan="2">@if($agenda->telefono2!=''){{$agenda->telefono2}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                @if(!is_null($agenda->palergias)&&$agenda->palergias!='')
                                <tr>
                                    <td><b>@lang('agenda.alergias'):</b></td>
                                    <td colspan="5"> {{$agenda->palergias}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_pat))
                                <tr>
                                    <td><b>@lang('agenda.antecedentesPatologicos'):</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_pat}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_fam))
                                <tr>
                                    <td><b>@lang('agenda.antecedentesFamiliares'):</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_fam}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_quir))
                                <tr>
                                    <td><b>@lang('agenda.antecedentesQuirurgicos'):</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_quir}}</td>
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
                <div class="box-header with-border"><h4 class="col-md-6">@lang('agenda.detalleAgenda-')</h4></div>
                <div class="box-body">
                    @if($agenda->nro_reagenda>'0')
                    <div class="form-group col-md-12">
                    <p><b style="color: red;">** @lang('agenda.citaReagendada') : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{@lang('agenda.vez')}}@else{{@lang('agenda.veces')}}@endif</b></p>
                    </div>
                     @endif
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>

                                <tr>
                                    <td><b>@lang('agenda.estado'):  </b></td>
                                    <td>@if ($agenda->estado_cita=='0')
                                    @lang('agenda.porConfirmar')
                                    @endif
                                    @if ($agenda->estado_cita=='1')
                                    @lang('agenda.confirmada')
                                    @endif
                                    @if ($agenda->estado_cita=='2')
                                            @lang('agenda.reagendada')
                                    @endif
                                    @if ($agenda->estado_cita=='3')
                                    @lang('agenda.suspendida')
                                    @endif
                                    @if ($agenda->estado_cita=='4')
                                         @lang('agenda.asistio-')
                                    @endif</td>
                                    <td><b>@lang('agenda.tipo'):</b></td>
                                    <td>@if($agenda->proc_consul=='0'){{@lang('agenda.consulta')}}
                                        @elseif($agenda->proc_consul=='1'){{@lang('agenda.procedimiento')}}
                                        @else{{@lang('agenda.reuniones')}}
                                        @endif</td>
                                    <td><b>@lang('agenda.seguro'):  </b></td>
                                    <td><b>{{$agenda->snombre}}</b></td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.fechaCita'):</b></td>
                                    <td>{{ substr($agenda->fechaini, 0, 10)}}</td>
                                    <td><b>@lang('agenda.hora'):</b></td>
                                    <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                    <td><b>@lang('agenda.cortesia'):</b></td>
                                    <td>@if($agenda->cortesia=='SI') @lang('agenda.si') @else @lang('agenda.no') @endif</td>

                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.ingreso'):</b></td>
                                    <td>@if($agenda->est_amb_hos=='0'){{@lang('agenda.ambulatorio')}}@else{{@lang('agenda.hospitalizado')}}@endif</td>
                                     <td><b>@lang('agenda.sala'):</b></td>
                                    <td colspan="2">@if(!is_null($agenda->slnombre)){{$agenda->slnombre}}/{{$agenda->hsnombre}}@else No Ingresada @endif</td>
                                    <td><b>@if($agenda->tipo_cita=='0')@lang('agenda.primeraVez') @else @lang('agenda.consecutivo') @endif</b></td>

                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.doctor'):</b></td>
                                    <td colspan="3">@if(!is_null($agenda->id_doctor1)){{$agenda->udnombre}} {{$agenda->udapellido}} @else No asignado @endif</td>
                                    <td><b>@lang('agenda.especialidad'):</b></td>
                                    <td>@if(!is_null($agenda->id_doctor1)){{$agenda->esnombre}}@else No asignado @endif</td>
                                </tr>
                                @if($agenda->proc_consul=='1')
                                <tr>
                                    <td><b>@lang('agenda.procedimientos'):</b></td>
                                    <td colspan="5">{{$agenda->pnombre}} @if(!is_null($agendaprocs)) @foreach($agendaprocs as $agendaproc) + {{$agendaproc->nombre}} @endforeach @endif</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><b>@lang('agenda.observaciones'): </b></td>
                                    <td colspan="5">{{$agenda->observaciones}}</td>
                                </tr>
                                <tr>
                                    <td><b>@lang('agenda.creado'):</b></td>
                                    <td>{{$agenda->created_at}}</td>
                                    <td>{{substr($agenda->ucnombre,0,1)}}{{$agenda->ucapellido}}</td>
                                    <td><b>@lang('agenda.modificado'):</b></td>
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
                    <h4>@lang('agenda.detalleAdmision')</h4>
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    @if(!is_null($agenda->procedencia))
                                    <td><b>@lang('agenda.seguro'):</b></td>
                                    <td><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>@lang('agenda.procedencia'):</b></td>
                                    <td>{{$agenda->procedencia}}</td>
                                    <td><b>@lang('agenda.parentesco'):</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @else
                                    <td><b>@lang('agenda.seguro'):</b></td>
                                    <td colspan="3"><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>@lang('agenda.parentesco'):</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @endif
                                </tr>
                                @if($historiaclinica->parentesco!="Principal")
                                <tr>
                                    <td><b>@lang('agenda.principal'):</b></td>
                                    <td colspan="5">{{$historiaclinica->id_usuario}} - {{$historiaclinica->upapellido1}} @if($historiaclinica->upapellido2!='(N/A)'){{$historiaclinica->upapellido2}}@endif {{$historiaclinica->upnombre1}} @if($historiaclinica->upnombre2!='(N/A)'){{$historiaclinica->upnombre2}}@endif</td>

                                </tr>
                                @endif
                                <tr>
                                    <td><b>@lang('agenda.doctor'):</b></td>
                                    <td colspan="2">{{$historiaclinica->d1nombre1}} {{$historiaclinica->d1apellido1}}</td>
                                    <td><b>@lang('agenda.empresa'):</b></td>
                                    <td colspan="2">{{$agenda->id_empresa}} - {{$agenda->nombrecomercial}}</td>
                                </tr>
                                
                                 @if(!is_null($pentaxprocs))
                                <tr>

                                    <td><b>@lang('agenda.procedimientos'):</b></td>
                                    @php $flag=false; @endphp
                                    <td colspan="5"> @foreach($pentaxprocs as $pentaxproc) @if($flag) + @endif @php $flag=true; @endphp {{$pentaxproc->nombre}} @endforeach</td>  
                                </tr>
                                @endif  
                                
                                @if(!is_null($historiaclinica->d2apellido1)||!is_null($historiaclinica->d3apellido1))
                                <tr>
                                    <td><b>@lang('agenda.asistente'):</b></td>
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
                    <h4>@lang('agenda.detallePentax-')</h4>
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
            <thead>
              <tr  >
                <th >@lang('agenda.fecha') - @lang('agenda.hora')</th>
                <th >@lang('agenda.cambio')</th>
                <th >@lang('agenda.descripcion')</th>
                <!--th >Doctor</th>
                <th >Asistentes</th>
                <th >Procedimientos</th-->
                <th >@lang('agenda.seguro')</th>
                <th >@lang('agenda.sala')</th>
                <th >@lang('agenda.modifica')</th>
                <th >@lang('agenda.observacion')</th>
              </tr>
            </thead>
            <tbody >
            @foreach ($pentax_logs as $value)
              <tr >
                  <td >{{ $value->created_at}}</td>
                  <td >{{ $value->tipo_cambio}}</td>
                  <td >{{ $value->descripcion}}</td>
                  <!--td >{{ $value->d1nombre1}} {{ $value->d1apellido1}}</td>
                  <td >{{ $value->d2nombre1}} {{ $value->d2apellido1}} @if(!is_null($value->d2nombre1)) + @endif {{ $value->d3nombre1}} {{ $value->d3apellido1}}</td>
                  <td >@php 
                        $id_procs = explode('+',$value->procedimientos);
                        $list_procs="";
                        $flag=0;
                        foreach($id_procs as $id_proc){
                          if($flag==0){
                            $list_procs=Sis_medico\Procedimiento::find($id_proc)->observacion;
                            $flag=1;
                          }
                          else{
                            $list_procs=$list_procs."+".Sis_medico\Procedimiento::find($id_proc)->observacion;
                          }
                        }
                      @endphp
                      {{$list_procs}}
                  </td-->
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

   
$(document).ready(function(){

    $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> @lang('agenda.agenda')</a></li>');
        $(".breadcrumb").append('<li class="active">@lang('agenda.detalle')</li>');

    $('#example').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
       
    });

    var edad;
                edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>')+ " años";
                $('#edad').text(edad);

    

});
 /*
    $(document).ready(function(){

$("#example").DataTable({
    'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      

    });

});*/


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
