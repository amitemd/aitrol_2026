@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$idusuario  = Auth::user()->id;
@endphp
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="Pentax_log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover" style="font-size: 11px;">
            <thead>
              <tr  >
                <th width="10%" >Apellidos</th>
                <th width="10%" >Nombres</th>
                <th width="10%" >Procedimientos</th>
                <th width="5%" >Sala del Procedimiento</th>
                <th width="5%" >Hora del procedimiento</th>
                <th width="10%" >Amb/Hos</th>
                <th width="10%" >Medico Asignado</th>
                <th width="10%" >Medico 1</th>
                <th width="5%" >Medico 2</th>
                <th width="5%" >Enfermero</th>
                <th width="5%" >Anestesiologo</th>
                <th width="15%" >Convenio</th>

                <!--th >Cort</th-->
                <th width="10%" >Estado</th>
                @if($rolUsuario == 1 or $rolUsuario == 7) <th> Modo </th>  @endif
                <!--th >Log</th-->
              </tr>
            </thead>
            <tbody >
            @foreach ($pentax as $value)
              @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();
                 $pantallapentax =  Sis_medico\Pentax::where('id_agenda',$value->id)->first();
                 /* $p_color1="black"; if($value->estado_cita != 0){ if($value->paciente_dr == 1) { $p_color1=$value->dcolor; } else{ $p_color1=$value->scolor;} }; */
                 $p_color1="black"; if($value->supervisa_robles == '1'){ $p_color1='red'; }; if($value->solo_robles == '1'){ $p_color1='purple'; } ;

                 //CONVENIO
                $empresa = '';
                if($value->id_empresa!=null){
                  $empresa = '/'.DB::table('empresa')->where('id',$value->id_empresa)->first()->nombre_corto;
                }

                //Planilla
                $planillaExiste = Sis_medico\Planilla::where('id_agenda',$value->id)->where('aprobado',1)->first();
              @endphp
                @if(is_null($pantallapentax))
                <tr style="color: {{$p_color1}};">
                  <td >{{ $value->papellido1}} @if($value->papellido2!='(N/A)'){{ $value->papellido2}}@endif</td>
                  <td >{{ $value->pnombre1}} @if($value->pnombre2!='(N/A)'){{ $value->pnombre2}}@endif</td>

                  <td >{{ $value->pobservacion}}@if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc)+ {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} @endforeach @endif</td>
                  <td >{{ $value->nombre_sala }} </td>
                  <td >{{substr($value->fechaini, 11, 5)}}</td>
                  <!-- <td>@if($value->est_amb_hos == 0)AMBULATORIO @else HOSPITALIZADO @endif</td> -->
                  <td>@if($value->est_amb_hos == 0)AMBULATORIO @else @if($value->omni=='SI') HOSP/OMNI @else HOSPITALIZADO @endif @endif</td>
                  <!--td> Dr. PENTAX</td-->
                  <td >@if($value->id_doctor1!='9666666666') {{ $value->dapellido1 }} {{ $value->dnombre1 }} @endif</td>
                  <td >{{ $value->d2nombre1 }} {{ $value->d2apellido1 }}</td>
                  <td >{{ $value->d3nombre1 }} {{ $value->d3apellido1 }}</td>
                  <td> </td>
                  <td> </td>
                  <td >{{ $value->snombre }}{{$empresa}} </td>

                  <!--td >{{ $value->cortesia }} </td-->
                  <td > No Admisionado </td>
                  <!--td > </td-->
                @if(!is_null($planillaExiste))  @if($rolUsuario == 7)<td><i style="color:red" class="fa fa-heart" aria-hidden="true"></i> </td>   @elseif($rolUsuario == 1 or $rolUsuario == 7 ) <td> <i style="color:green" class="fa fa-check-circle-o" aria-hidden="true"></i></td>   @endif @endif

              </tr>
              @else
              @php
              $ptx_seg=$seguros->find($pantallapentax->id_seguro);
                $pentaxproc =  Sis_medico\PentaxProc::where('id_pentax',$pantallapentax->id)->get();
                $flag=0;
                /* $p_color2="black"; if($value->estado_cita != 0){ if($value->paciente_dr == 1) { $p_color2=$value->dcolor; } else{ $p_color2=$ptx_seg->color;} }; */
                $p_color2="black"; if($value->supervisa_robles == '1'){ $p_color2='red'; }; if($value->solo_robles == '1'){ $p_color2='purple'; } ;
              @endphp
              <tr
                @if($pantallapentax->estado_pentax == '-1') @elseif($pantallapentax->estado_pentax == '0') style="background-color: #ffffe6; color: {{$p_color2}}; font-weight: bold;" @elseif($pantallapentax->estado_pentax < '3') style="background-color: #ffe6e6; color: {{$p_color2}}; font-weight: bold;"
                @elseif($pantallapentax->estado_pentax == '5')
                  @if($value->estado_cita!= 4) style="background-color: #ffffff; color: {{$p_color2}}; font-weight: bold;"
                  @else style="background-color: #ccf5ff; color: {{$p_color2}}; font-weight: bold;"
                  @endif
                @else  style="background-color: #ccf5ff; color: {{$p_color2}}; font-weight: bold;"
                @endif
                @if($value->estado_cita != 0)
                  @if($value->paciente_dr == 1) style="color: {{$p_color2}};"
                  @else style="color: {{$p_color2}};"
                  @endif
                @endif>
                  <td >{{ $value->papellido1}} @if($value->papellido2!='(N/A)'){{ $value->papellido2}}@endif</td>
                  <td >{{ $value->pnombre1}} @if($value->pnombre2!='(N/A)'){{ $value->pnombre2}}@endif</td>
                  <td >@if(!is_null($pentaxproc))
                        @foreach($pentaxproc as $proc) @if($flag!='0') + @endif @php $flag=1; @endphp {{$procedimientos->where('id',$proc->id_procedimiento)->first()->observacion}}
                        @endforeach
                    @endif
                  </td>
                   <td>
                        @if($salas->find($pantallapentax->id_sala)!=null){{ $salas->find($pantallapentax->id_sala)->nombre_sala }}@endif

                  </td>
                  <td >{{substr($value->fechaini, 11, 5)}}</td>
                  <!-- <td>@if($value->est_amb_hos == 0)AMBULATORIO @else HOSPITALIZADO @endif</td> -->
                  <td>@if($value->est_amb_hos == 0)AMBULATORIO @else @if($value->omni=='SI') HOSP/OMNI @else HOSPITALIZADO @endif @endif</td>
                  <!--td>DR PENTAX</td-->
                  <td>
                        @if($pantallapentax->id_doctor1!="")
                        @php $xdoctor = $doctores->find($pantallapentax->id_doctor1); @endphp
                        @if($pantallapentax->id_doctor1!='9666666666') {{$xdoctor->apellido1}} @if($xdoctor->apellido2!='(N/A)'){{$xdoctor->apellido2}}@endif {{$xdoctor->nombre1}} @endif

                        @endif
                  </td>
                  <td>
                        @if($pantallapentax->id_doctor2!="")
                        @php $doctor2=$doctores->find($pantallapentax->id_doctor2); @endphp

                        @if(!is_null($doctor2) && $doctor2->id_tipo_usuario=='3')Dr(a). @else Enf. @endif @if(!is_null($doctor2)) {{$doctor2->apellido1}} {{$doctor2->nombre1}} @endif

                        @endif
                  </td>
                  <td>  @if($pantallapentax->id_doctor3!="")
                    @php $doctor3=$doctores->find($pantallapentax->id_doctor3) @endphp
                        @if(!is_null($doctor3) && $doctor3->id_tipo_usuario=='3')Dr(a). @else Enf. @endif @if(!is_null($doctor3)) {{$doctor3->apellido1}} {{$doctor3->nombre1}} @endif </a>



                        @endif
                  </td>

                  <td>  @if($pantallapentax->id_doctor4!="")
                    @php $doctor4=$doctores->find($pantallapentax->id_doctor4) @endphp
                        @if(!is_null($doctor4) && $doctor4->id_tipo_usuario=='3')Dr(a). @else Enf. @endif @if(!is_null($doctor4)) {{$doctor4->apellido1}} {{$doctor4->nombre1}} @endif</a>



                        @endif
                  </td>

                  <td>  @if($pantallapentax->id_anestesiologo!="")
                          @php $anestesiologo=$anestesiologos->find($pantallapentax->id_anestesiologo) @endphp
                          @if(!is_null($anestesiologo)) 
                            Dr(a). {{$anestesiologo->apellido1}} {{$anestesiologo->nombre1}} 
                          @endif
                        @endif
                  </td>

                  <td >

                        {{$seguros->find($pantallapentax->id_seguro)->nombre}}{{$empresa}}

                  </td>

                  <!--td > {{ $value->cortesia }} </td-->

                  <td>  @if($pantallapentax->estado_pentax=='-1') PRE - ADMISION @endif
                        @if($pantallapentax->estado_pentax=='0') EN ESPERA @endif
                        @if($pantallapentax->estado_pentax=='1') PREPARACIÓN @endif
                        @if($pantallapentax->estado_pentax=='2') EN PROCEDIMIENTO @endif
                        @if($pantallapentax->estado_pentax=='3') RECUPERACION @endif
                        @if($pantallapentax->estado_pentax=='4') ALTA @endif
                        @if($pantallapentax->estado_pentax=='5') @if($value->estado_cita!= 4) No Admisionado @else SUSPENDER @endif @endif

                  </td>
                  <!--td ><a href="{{route('pentax.log',['id' => $pantallapentax->id])}}" data-toggle="modal" data-target="#Pentax_log" class="btn btn-warning col-md-7 col-sm-7 col-xs-7 btn-margin">Log</a></td-->
                  @if(!is_null($planillaExiste))  @if($rolUsuario == 7 )<td><i style="color:red" class="fa fa-heart" aria-hidden="true"></i> </td>   @elseif($rolUsuario == 1) <td> <i style="color:green" class="fa fa-check-circle-o" aria-hidden="true"></i></td>   @endif @endif
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
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

          </div>
        </div>
      </div>
  </div>


<script type="text/javascript">
  $('#Pentax_log').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            });

  $('#Pentax_log').on('show.bs.modal', function (e) {
  clearInterval(vartiempo);
  console.log(vartiempo);
})

</script>
