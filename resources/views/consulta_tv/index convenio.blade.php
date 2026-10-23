<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="Pentax_log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Ventana modal editar -->
<div class="modal fade" id="Estados_pentax" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<style type="text/css">
  td>a:hover {
    font-size: 90%;
    color: blue !important;
}
</style>

@if($reuniones != '[]')
            <div  class="table-responsive col-md-12"><h4>REUNIONES</h4>
                        <table class="table table-striped">
                            <thead>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Tipo</th>
                                <th>Doctor</th>
                                
                                <th>Agdo.por</th>
                                <th>Modf.por</th>

                            </thead>
                            <tbody style="background-color: #ffebe6;">
                                @foreach($reuniones as $reunion)           
                                <tr >  
                                    
                                    <td>{{$reunion->fechaini}}</td>
                                    <td>{{$reunion->fechafin}}</td>
                                    <td>{{$reunion->procedencia}} {{$reunion->observaciones}}</td>
                                    <td>{{$reunion->dnombre}}{{$reunion->dapellido}}</td>
                                    <td>{{$reunion->ucnombre1}}{{$reunion->ucapellido1}}</td>
                                    <td>{{$reunion->umnombre1}}{{$reunion->umapellido1}}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>     
            @endif
  
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
        <thead>
          <tr>
            <th >Apellidos</th>
            <th >Nombres</th>
            <th >Procedimientos</th>
            <th >Sala del Procedimiento</th>
            <th >Hora del Procedimiento</th>
            <th >Amb/Hos</th>
            <th >Doctor</th>
            <th >Asistente 1</th>
            <th >Asistente 2</th>
            <!--th >Seguro</th-->
            <th>Convenio</th>
            <th >Cort</th>
            <th >Estado</th>
            <th >Log</th>
          </tr>
        </thead>
        <tbody >
          @foreach ($pentax as $value)
          
            @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); 
              $pantallapentax =  Sis_medico\Pentax::where('id_agenda',$value->id)->first();
              /* YA APROBADO ...$p_color1="black"; if($value->estado_cita != 0){ if($value->paciente_dr == 1) { $p_color1=$value->dcolor; } else{ $p_color1=$value->scolor;} }; */
              $p_color1="black"; if($value->supervisa_robles == '1'){ $p_color1='red'; }; if($value->solo_robles == '1'){ $p_color1='purple'; } ; 
            @endphp
            @if(is_null($pantallapentax))
              <tr style="color: {{$p_color1}};" >
                <td >{{ $value->papellido1}} @if($value->papellido2!='(N/A)'){{ $value->papellido2}}@endif</td>
                <td >{{ $value->pnombre1}} @if($value->pnombre2!='(N/A)'){{ $value->pnombre2}}@endif</td>
                <td >{{ $value->pobservacion}}@if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc)+ {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} @endforeach @endif</td>
                <td >{{ $value->nombre_sala }} </td>
                <td >{{substr($value->fechaini, 11, 5)}}</td>
                <td>@if($value->est_amb_hos == 0)AMBULATORIO @else HOSPITALIZADO @endif</td>
                <td >{{ $value->dnombre1 }} {{ $value->dapellido1 }}</td>
                <td >{{ $value->d2nombre1 }} {{ $value->d2apellido1 }}</td>
                <td >{{ $value->d3nombre1 }} {{ $value->d3apellido1 }}</td>
                <td >{{ $value->snombre }} / {{Sis_Medico\Empresa::find($value->id_empresa)->nombre_corto}}ññññppp </td>
                
                <td >{{ $value->cortesia }} </td>
                <td > No Admisionado </td>
                <td >&nbsp</td>
              </tr>
            @else
              @php
                $ptx_seg=$seguros->find($pantallapentax->id_seguro);
                $pentaxproc =  Sis_medico\PentaxProc::where('id_pentax',$pantallapentax->id)->get();
                $flag=0;
                /* YA APROBADO ... $p_color2="black"; if($value->estado_cita != 0){ if($value->paciente_dr == 1) { $p_color2=$value->dcolor; } else{ $p_color2=$ptx_seg->color;} }; */
                /* pendiente de aprobar */
                $p_color2="black"; if($value->supervisa_robles == '1'){ $p_color2='red'; }; if($value->solo_robles == '1'){ $p_color2='purple'; } ; 
              @endphp  
              <tr 
                @if($pantallapentax->estado_pentax == '0') style="background-color: #ffffe6; color: {{$p_color2}}; font-weight: bold;" @elseif($pantallapentax->estado_pentax < '3') style="background-color: #ffe6e6; color: {{$p_color2}}; font-weight: bold;" 
                @elseif($pantallapentax->estado_pentax == '5') 
                  @if($value->estado_cita!= 4) style="background-color: #ffffff; color: {{$p_color2}}; font-weight: bold;"
                  @else style="background-color: #ccf5ff; color: {{$ptx_seg->color}}; font-weight: bold;"
                  @endif 
                @else style="background-color: #ccf5ff; color: {{$ptx_seg->color}}; font-weight: bold;" 
                @endif  
                @if($value->estado_cita != 0) 
                  @if($value->paciente_dr == 1) style="color: {{$value->dcolor}};" 
                  @else style="color: {{$value->scolor}};" 
                  @endif 
                @endif>
                <td >{{ $value->papellido1}} @if($value->papellido2!='(N/A)'){{ $value->papellido2}}@endif</td>
                <td >{{ $value->pnombre1}} @if($value->pnombre2!='(N/A)'){{ $value->pnombre2}}@endif</td>
                <td >@if(!is_null($pentaxproc))  
                  <?php /*@if($pantallapentax->estado_pentax!='5')*/?>
                  @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                    <a style="text-decoration:underline; color:{{$p_color2}};" href="javascript:cambia_estado({{$pantallapentax->id}}, document.getElementById('estado_pentax{{$value->id}}').value)">
                    @foreach($pentaxproc as $proc) @if($flag!='0') + @endif @php $flag=1; @endphp {{$procedimientos->where('id',$proc->id_procedimiento)->first()->observacion}}    
                    @endforeach 
                    </a>
                  @else
                    @foreach($pentaxproc as $proc) @if($flag!='0') + @endif @php $flag=1; @endphp {{$procedimientos->where('id',$proc->id_procedimiento)->first()->observacion}}    
                    @endforeach 
                  @endif
                @endif                  
                </td>
                <td>@if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                        <select id="id_sala{{$value->id}}" name="id_sala" onchange="cambia_sala({{$pantallapentax->id}}, this.value)">
                          @foreach($salas as $sala)
                          <option @if($pantallapentax->id_sala==$sala->id) selected @endif value="{{$sala->id}}">{{$sala->nombre_sala}}</option>
                          @endforeach
                        </select> 
                      @else
                        {{$salas->find($pantallapentax->id_sala)->nombre_sala}}
                      @endif  
                  </td>
                <td >{{substr($value->fechaini, 11, 5)}}</td>
                <td>@if($value->est_amb_hos == 0)AMBULATORIO @else HOSPITALIZADO @endif</td>
                <td>
                  @if($pantallapentax->id_doctor1!="")
                  @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                    <a style="text-decoration:underline; color:{{$p_color2}};" href="javascript:cambia_estado({{$pantallapentax->id}}, document.getElementById('estado_pentax{{$value->id}}').value)">{{$doctores->find($pantallapentax->id_doctor1)->nombre1}} {{$doctores->find($pantallapentax->id_doctor1)->apellido1}}</a>
                  @else
                    {{$doctores->find($pantallapentax->id_doctor1)->nombre1}} {{$doctores->find($pantallapentax->id_doctor1)->apellido1}}
                  @endif
                  @endif
                  </td>
                  <td>
                    @if($pantallapentax->id_doctor2!="")
                      @php $doctor2=$doctores->find($pantallapentax->id_doctor2) @endphp
                        @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                          <a style="text-decoration:underline; color:{{$p_color2}};" href="javascript:cambia_estado({{$pantallapentax->id}}, document.getElementById('estado_pentax{{$value->id}}').value)">@if($doctor2->id_tipo_usuario=='3')Dr(a). @else Enf. @endif {{$doctor2->nombre1}} {{$doctor2->apellido1}}</a>
                    @else
                      @if($doctor2->id_tipo_usuario=='3')Dr(a). @else Enf. @endif {{$doctor2->nombre1}} {{$doctor2->apellido1}}
                      @endif
                    @endif
                  </td>
                  <td>  @if($pantallapentax->id_doctor3!="")
                    @php $doctor3=$doctores->find($pantallapentax->id_doctor3) @endphp
                        @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                        <a style="text-decoration:underline; color:{{$p_color2}};" href="javascript:cambia_estado({{$pantallapentax->id}}, document.getElementById('estado_pentax{{$value->id}}').value)">@if($doctor3->id_tipo_usuario=='3')Dr(a). @else Enf. @endif {{$doctor3->nombre1}} {{$doctor3->apellido1}}</a>
                        @else
                        @if($doctor3->id_tipo_usuario=='3')Dr(a). @else Enf. @endif {{$doctor3->nombre1}} {{$doctor3->apellido1}}
                        @endif
                        @endif
                  </td>    
                  <td > @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                        <a style="text-decoration:underline; color:{{$p_color2}};" href="javascript:cambia_estado({{$pantallapentax->id}}, document.getElementById('estado_pentax{{$value->id}}').value)">{{$ptx_seg->nombre}} / {{DB::table('empresa')->where('id',$value->id_empresa)->first()->nombre_corto}}</a> 
                        @else
                        {{$ptx_seg->nombre}}/ {{Sis_Medico\Empresa::find($value->id_empresa)->nombre_corto}}ññññ
                        @endif
                  </td>
                  
                  <td > {{ $value->cortesia }} </td>
                  <td>@if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5')
                        <select id="estado_pentax{{$value->id}}" name="estado_pentax"  onchange="cambia_estado({{$pantallapentax->id}}, this.value)">
                          @if($pantallapentax->estado_pentax=='0' || $pantallapentax->estado_pentax=='1' || $pantallapentax->estado_pentax=='2')
                          <option @if($pantallapentax->estado_pentax=='0') selected @endif value='0'>EN ESPERA</option>
                          @endif
                          @if($pantallapentax->estado_pentax=='0' || $pantallapentax->estado_pentax=='1' || $pantallapentax->estado_pentax=='2')
                          <option @if($pantallapentax->estado_pentax=='1') selected @endif value='1'>PREPARACIÓN</option>
                          @endif
                          @if($pantallapentax->estado_pentax=='0' || $pantallapentax->estado_pentax=='1' || $pantallapentax->estado_pentax=='2')
                          <option @if($pantallapentax->estado_pentax=='2') selected @endif value='2'>EN PROCEDIMIENTO</option>
                          @endif
                          @if($pantallapentax->estado_pentax!='0' && $pantallapentax->estado_pentax!='1')
                          <option @if($pantallapentax->estado_pentax=='3') selected @endif value='3'>RECUPERACION</option>
                          @endif
                          @if($pantallapentax->estado_pentax!='0' && $pantallapentax->estado_pentax!='1' && $pantallapentax->estado_pentax!='2')
                          <option @if($pantallapentax->estado_pentax=='4') selected @endif value='4'>ALTA</option>
                          @endif
                          <option @if($pantallapentax->estado_pentax=='5') selected @endif value='5'>SUSPENDER</option>
                        </select>
                      @else
                        <input type="hidden" name="estado_pentax" id="estado_pentax{{$value->id}}" value="{{$pantallapentax->estado_pentax}}">
                        @if($pantallapentax->estado_pentax=='0') EN ESPERA @endif
                        @if($pantallapentax->estado_pentax=='1') PREPARACIÓN @endif
                        @if($pantallapentax->estado_pentax=='2') EN PROCEDIMIENTO @endif
                        @if($pantallapentax->estado_pentax=='3') RECUPERACION @endif
                        @if($pantallapentax->estado_pentax=='4') ALTA @endif
                        @if($pantallapentax->estado_pentax=='5') @if($value->estado_cita!= 4) No Admisionado @else SUSPENDER @endif @endif
                      @endif   
                  </td>
                  <td ><a href="{{route('pentax.log',['id' => $pantallapentax->id])}}" data-toggle="modal" data-target="#Pentax_log" class="btn btn-warning col-md-7 col-sm-7 col-xs-7 btn-margin">Log</a></td>
                </tr>
              @endif
            @endforeach
            <a id="cambios_pentax" style="display: none;" data-toggle="modal" data-target="#Estados_pentax"></a>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-5">
        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($pentax)}} Registros</div>
    </div>
    <div class="col-sm-7">
      <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate"> </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $('#Pentax_log').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
      vartiempo = setInterval(function(){ findex_pentax(); console.log(vartiempo);}, 5000);
  }); 

  $('#Estados_pentax').on('hidden.bs.modal', function(){
    //location.reload();
    $(this).removeData('bs.modal');
    vartiempo = setInterval(function(){ findex_pentax(); console.log(vartiempo);}, 5000);
}); 

  $('#Pentax_log').on('show.bs.modal', function (e) {
  clearInterval(vartiempo);
  //console.log(vartiempo);
})

</script>
  