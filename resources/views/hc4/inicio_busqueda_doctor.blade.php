@include('hc4.partials.catalog_list_styles')
<style type="text/css">
#reuniones .row{
  width: 100%;
}
.row_completo .row{
  width: 100%;
}
</style>

<section class="content v3-clinical-list">
<div class="v3-clinical-list__card" id="area_busqueda_doctor">
  <header class="v3-clinical-list__head">
    <div class="v3-clinical-list__title"><div class="v3-clinical-list__icon"><i class="fas fa-user-md" aria-hidden="true"></i></div><div><div class="v3-clinical-list__eyebrow">Gestión ambulatoria · V3</div><h3>@lang('hc4.search_results')</h3><p>Consulta las citas asignadas y accede a la historia clínica de cada paciente.</p></div></div>
  </header>
  <div class="box-body" style="padding:0" id="modificar">
    <div class="content col-md-12" id="div_grafico" style="padding:0">
      @if($agendas_reuniones->count() === 0 && $agendas_pac->count() === 0 && $agendas_pac_procedimientos->count() === 0)
        <div class="v3-clinical-list__empty"><i class="fas fa-search" aria-hidden="true"></i>Selecciona una opción o utiliza los filtros para consultar pacientes.</div>
      @endif
      @if($agendas_reuniones->count() > 0)
      <div class="box box-primary" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#reu').click());"><b>@lang('hc4.meetings')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>{{$agendas_reuniones->count()}} @lang('hc4.has_scheduled_today')</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="reu">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">
            <div class="table-responsive col-md-12">
              <table  id="reuniones" class="table table-hover dataTable" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_reuniones!=[])
                <thead >
                  <tr >
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.time')</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.type')</th>
                      <th width="55%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.name')</th>
                      <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.place')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_reuniones as $pac)
                  <tr role="row" class="odd">
                    <td class="color">{{substr($pac->fechaini,11,8)}} - {{substr($pac->fechafin,11,8)}}</td>
                    <td class="color">{{$pac->procedencia}}</td>
                    <td class="color">{{$pac->observaciones}}</td>
                    @php
                      $sala = $salas_reuniones->get($pac->id_sala);
                    @endphp
                    <td class="color"> {{optional($sala)->nombre_sala}} / {{optional(optional($sala)->hospital)->nombre_hospital}}</td>

                  </tr>
                  @endforeach
                </tbody>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
      @endif
      @if($agendas_pac->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#consult').click());"><b>@lang('hc4.consultations')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>{{$agendas_pac->count()}} @lang('hc4.has_scheduled_today')</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="consult">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">
            @php
              $aleatorio1 = rand();
            @endphp
            <div class="table-responsive col-md-12">
              <table  id="example2{{$aleatorio1}} row_completo" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_pac!=[])
                <thead style="">
                  <tr style=" ">
                      <th scope="col" class="color titulo" >@lang('hc4.identification')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.last_names')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.first_names')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.birth_date')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.insurance_agreement')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.time')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.type')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.courtesy')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.status')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.action')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac as $pac)
                    @php $agenda_last = $ultimos_seguros->get($pac->id); @endphp

                    <tr>
                    <td class="color">{{$pac->id}}</td>
                    <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
                    <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
                    <td class="color">{{$pac->fecha_nacimiento}}</td>
                    <td class="color">
                      @if(!is_null($agenda_last))
                        {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                      @endif
                    </td>
                    <td class="color">{{$pac->dnombre1}} {{$pac->dapellido1}}</td>
                    <td class="color">
                      {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}
                    </td>
                    <td class="color">
                      @if($pac->proc_consul=='0')
                        @lang('hc4.consultation')
                      @elseif($pac->proc_consul=='1')
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}}
                        @else
                          @lang('hc4.procedure')
                        @endif
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red">
                          @lang('hc4.yes')
                         </span>
                        @else
                         <span class="color">
                          @lang('hc4.no')
                         </span>
                        @endif
                      </b>
                    </td>
                    @php $contador_cie10 = $diagnosticos_por_hcid->get($pac->hcid, 0); @endphp
                    <td class="color">
                      @if($pac->omni=='OM')
                        @if($pac->estado_cita==4)
                          @lang('hc4.admitted')
                        @elseif($pac->estado_cita==5)
                          @lang('hc4.discharge')
                        @elseif($pac->estado_cita==6)
                          @lang('hc4.emergency')
                        @endif
                      @elseif($contador_cie10 >'1')
                          @lang('hc4.attended')
                      @elseif(!is_null($pac->hcid))
                          @lang('hc4.admitted_status')
                      @else
                        @if($pac->estado_cita == 0)
                          @lang('hc4.pending_confirmation')
                        @elseif($pac->estado_cita == 1)
                          @lang('hc4.confirmed')
                        @elseif($pac->estado_cita == 2)
                          @lang('hc4.rescheduled')
                        @endif
                      @endif
                    </td>

                    <td><div class="v3-clinical-list__action-stack">
                      @if(!is_null($pac->hcid) && in_array((int) $pac->estado_cita, [4, 5, 6], true))
                        <a class="v3-clinical-list__action v3-clinical-list__action--view" href="{{route('nd.buscador', ['id_paciente' => $pac->id, 'id_agenda' => $pac->id_agenda])}}">
                          <i class="fas fa-eye" aria-hidden="true"></i>@lang('hc4.view_full_details')
                        </a>
                      @else
                        <span class="v3-clinical-list__action v3-clinical-list__action--disabled" aria-disabled="true" title="Disponible después de admisionar la cita"><i class="fas fa-eye-slash" aria-hidden="true"></i>@lang('hc4.view_full_details')</span>
                      @endif
                      <a class="v3-clinical-list__action v3-clinical-list__action--log" href="{{route('consultam.log_agenda', ['id' => $pac->id_agenda])}}" target="_blank" rel="noopener noreferrer"><i class="fas fa-clipboard-list" aria-hidden="true"></i>Log de Agenda</a>
                    </div></td>

                  </tr>
                  @endforeach
                </tbody>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
      @endif

      @if($agendas_pac_procedimientos->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#proced').click());"><b>@lang('hc4.procedures')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>{{$agendas_pac_procedimientos->count()}} @lang('hc4.has_scheduled_today')</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="proced">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">

            <div class="table-responsive col-md-12">
              @php
                $aleatorio2 = rand();
              @endphp
              <table  id="example2{{$aleatorio2}} row_completo" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_pac_procedimientos!=[])
                <thead style="">
                  <tr style=" ">
                      <th scope="col" class="color titulo" >@lang('hc4.identification')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.last_names')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.first_names')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.birth_date')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.insurance_agreement')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.time')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.type')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.courtesy')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.status')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.action')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac_procedimientos as $pac)

                    @php $agenda_last = $ultimos_seguros->get($pac->id); @endphp

                    <tr>
                    @php $contador_cie10 = $diagnosticos_por_hcid->get($pac->hcid, 0); @endphp
                    <td class="color">{{$pac->id}}</td>
                    <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
                    <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
                    <td class="color">{{$pac->fecha_nacimiento}}</td>
                    <td class="color">
                      @if(!is_null($agenda_last))
                        {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                      @endif
                    </td>
                    <td class="color">{{$pac->dnombre1}} {{$pac->dapellido1}}</td>
                    <td class="color">
                      {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}
                    </td>
                    <td class="color">
                      @if($pac->proc_consul=='0')
                        @lang('hc4.consultation')
                      @elseif($pac->proc_consul=='1')
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}}
                        @else

                          @lang('hc4.procedure')
                        @endif
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red">
                          @lang('hc4.yes')
                         </span>
                        @else
                         <span class="color">
                          @lang('hc4.no')
                         </span>
                        @endif
                      </b>
                    </td>

                    <td class="color">
                      @if($pac->omni=='OM')
                        @if($pac->estado_cita==4)
                          @lang('hc4.admitted')
                        @elseif($pac->estado_cita==5)
                          @lang('hc4.discharge')
                        @elseif($pac->estado_cita==6)
                          @lang('hc4.emergency')
                        @endif
                      @elseif($contador_cie10 >'1')
                          @lang('hc4.attended')
                      @elseif(!is_null($pac->hcid))
                          @lang('hc4.admitted_status')
                      @else
                        @if($pac->estado_cita == 0)
                          @lang('hc4.pending_confirmation')
                        @elseif($pac->estado_cita == 1)
                          @lang('hc4.confirmed')
                        @elseif($pac->estado_cita == 2)
                          @lang('hc4.rescheduled')
                        @endif
                      @endif
                    </td>

                    <td><div class="v3-clinical-list__action-stack">
                      @if(!is_null($pac->hcid) && in_array((int) $pac->estado_cita, [4, 5, 6], true))
                        <a class="v3-clinical-list__action v3-clinical-list__action--view" href="{{route('nd.buscador', ['id_paciente' => $pac->id, 'id_agenda' => $pac->id_agenda])}}">
                          <i class="fas fa-eye" aria-hidden="true"></i>@lang('hc4.view_full_details')
                        </a>
                      @else
                        <span class="v3-clinical-list__action v3-clinical-list__action--disabled" aria-disabled="true" title="Disponible después de admisionar la cita"><i class="fas fa-eye-slash" aria-hidden="true"></i>@lang('hc4.view_full_details')</span>
                      @endif
                      <a class="v3-clinical-list__action v3-clinical-list__action--log" href="{{route('consultam.log_agenda', ['id' => $pac->id_agenda])}}" target="_blank" rel="noopener noreferrer"><i class="fas fa-clipboard-list" aria-hidden="true"></i>Log de Agenda</a>
                    </div></td>

                  </tr>
                  @endforeach
                </tbody>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
</section>

<script type="text/javascript">
    $('#agenda_semana').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('hc4.calendario_fullcalendar')}}",
          success: function(data){
            //console.log(data);
            //alert("ok");
            $("#modificar").html(data);
            //$("#agenda_semana").addClass('oculto');

          },
          error: function(data){
            console.log(data);
          }
      });

    });

    $('#example2{{rand()}}').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

//Busqueda de estadisticos
    function estadisticos_master_hc(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_consulta.pasteles_hc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    //Busqueda de estadisticos
    function estadisticos_estimado(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_consulta.ganancia_hc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function revisar_procedimientos(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_revisar.procedimientos')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }



</script>




