@include('hc4.partials.catalog_list_styles')
<section class="content v3-clinical-list">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title">
        <div class="v3-clinical-list__icon"><i class="fas fa-users" aria-hidden="true"></i></div>
        <div><div class="v3-clinical-list__eyebrow">Agenda ambulatoria · V3</div><h3>Listado de pacientes del día</h3><p>Consulta las citas de hoy y accede al detalle de cada paciente.</p></div>
      </div>
    </header>
  <div class="box-body" style="padding:0" id="modificar">
    <div class="content col-md-12" style="padding: 0">
      <div class="table-responsive" id="div_grafico">
        @php
          $aleatorio = rand();
        @endphp
        <table id="example2{{$aleatorio}}" class="v3-clinical-list__table" cellspacing="0" width="100%">
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

              <tr>
              <td class="color">{{$pac->id}}</td>
              <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
              <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
              <td class="color">{{$pac->fecha_nacimiento}}</td>
              <td class="color">
                @if(!is_null($pac->seguro_nombre))
                  {{$pac->seguro_nombre}}/{{$pac->empresa_nombre}}
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
              @php
                $contador_cie10 = !is_null($pac->hcid) ? (int) $diagnosticos_por_hcid->get($pac->hcid, 0) : 0;
                $tiene_historia = !is_null($pac->hcid);
              @endphp
              <td class="color">
                  @if($pac->omni=='OM') 
                    @if($pac->estado_cita==4)
                      @lang('hc4.admitted')
                    @elseif($pac->estado_cita==5)
                      @lang('hc4.discharge')
                    @elseif($pac->estado_cita==6)
                      @lang('hc4.emergency')
                    @endif
                  @elseif($contador_cie10 >'0')
                    @lang('hc4.attended')
                  @elseif($tiene_historia)
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

              <td>@if($tiene_historia)
                <a class="v3-clinical-list__action v3-clinical-list__action--view" href="{{route('nd.buscador', ['id_paciente' => $pac->id, 'id_agenda' => $pac->id_agenda])}}"><i class="fas fa-eye" aria-hidden="true"></i>@lang('hc4.view_full_details')</a>@endif
              </td>

            </tr>
            @endforeach
          </tbody>
          @endif
        </table>

      </div>
    </div>
  </div>
  <footer class="v3-clinical-list__footer"><span>@lang('hc4.total_records')</span><strong>{{$agendas_pac->count()}}</strong></footer>
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

      $('#example2{{$aleatorio}}').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'language'    : {'emptyTable': 'No se encontraron pacientes para el día seleccionado.'}
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
    function exportar_revisionxx(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4controller.exportar_revision')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            console.log("hola");
            //$("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function exportar_revision(){
      $('#exp_rev').click();
    }

</script>
