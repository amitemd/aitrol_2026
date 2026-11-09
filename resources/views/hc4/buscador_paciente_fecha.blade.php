@include('hc4.partials.catalog_list_styles')
<section class="content v3-clinical-list">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title">
        <div class="v3-clinical-list__icon"><i class="fas fa-user-injured" aria-hidden="true"></i></div>
        <div><div class="v3-clinical-list__eyebrow">Gestión ambulatoria · V3</div><h3>@lang('hc4.search_results')</h3><p>Consulta las citas y accede a la historia clínica de cada paciente.</p></div>
      </div>
    </header>
      <div class="box-body" style="padding:0" id="modificar">
        <div class="content col-md-12"  style="padding-left: 5px;padding-right: 5px;">
          <div class="table-responsive" id="div_grafico">
            <h4> </h4>
            @php
              $aleatorio = rand();
            @endphp
            <table id="example2{{$aleatorio}}" class="v3-clinical-list__table hc4-results-table" cellspacing="0" width="100%">
              @if($pacientes!=[])
              <thead style="">
                <tr style=" ">
                  <th scope="col" class="color titulo" >@lang('hc4.identification')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.last_names')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.first_names')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.birth_date')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.insurance_agreement')</th>
                  @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                    <th scope="col" class="color titulo" >@lang('hc4.last_visit')</th>
                  @else
                    <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                    <th scope="col" class="color titulo" >@lang('hc4.time')</th>
                  @endif
                  <th scope="col" class="color titulo" >@lang('hc4.type')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.courtesy')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.status')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.action')</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pacientes as $paci)

                  @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                    @php

                    $agenda_last = DB::table('agenda as a')
                      ->where('a.id_paciente', $paci->id)
                      ->where('a.id_empresa', session('id_empresa'))
                      ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                      ->where('a.espid', '<>', '10')
                      ->orderBy('a.fechaini', 'desc')
                      ->join('seguros as s', 's.id', 'h.id_seguro')
                      ->join('empresa as em', 'em.id', 'a.id_empresa')
                      ->select('h.*', 's.nombre', 'a.fechaini', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'em.nombre_corto')
                      ->first();
                      //dd($agenda_last);

                      if (!is_null($agenda_last)) {
                          $dia = Date('N', strtotime($agenda_last->fechaini));
                          $mes = Date('n', strtotime($agenda_last->fechaini));
                      } else {
                          $dia = 0;
                          $mes = 0;
                      }

                    @endphp
                  @endif

                  @php
                    $contador_cie10 = 0;
                    if(($nombres!=null)||($apellidos!=null)||($cedula!=null)){

                      if(!is_null($agenda_last)){
                        $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$agenda_last->hcid)->get()->count();
                      }

                    }
                  @endphp

                  <tr>
                    <!--Cedula-->
                    <td class="color">{{$paci->id}}</td>
                    <!--Apellidos-->
                    <td class="color">{{$paci->apellido1}} {{$paci->apellido2}}</td>
                    <!--Nombres-->
                    <td class="color">{{$paci->nombre1}} {{$paci->nombre2}}</td>
                    <!--Fecha Nacimiento-->
                    <td class="color">{{$paci->fecha_nacimiento}}</td>
                    <!--Seguro/Convenio-->
                    <td class="color">


                      @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                        @if(!is_null($agenda_last))
                            {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                        @endif
                      @else
                        {{$paci->nombre}}/{{$paci->nombre_corto}}
                      @endif
                    </td>
                    <!--Última visita-->
                    @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                      <td class="color">
                        @if(!is_null($agenda_last))
                          @if($dia == '1')
                            @lang('hc4.monday')
                          @elseif($dia == '2')
                            @lang('hc4.tuesday')
                          @elseif($dia == '3')
                            @lang('hc4.wednesday')
                          @elseif($dia == '4')
                             @lang('hc4.thursday')
                          @elseif($dia == '5')
                             @lang('hc4.friday')
                          @elseif($dia == '6')
                             @lang('hc4.saturday')
                          @elseif($dia == '7')
                             @lang('hc4.sunday')
                          @endif
                            {{substr($agenda_last->fechaini,8,2)}} @lang('hc4.of')
                              @if($mes == '1')
                                @lang('hc4.january')
                              @elseif($mes == '2')
                                @lang('hc4.february')
                              @elseif($mes == '3')
                                @lang('hc4.march')
                              @elseif($mes == '4')
                                @lang('hc4.april')
                              @elseif($mes == '5')
                                @lang('hc4.may')
                              @elseif($mes == '6')
                                @lang('hc4.june')
                              @elseif($mes == '7')
                                @lang('hc4.july')
                              @elseif($mes == '8')
                                @lang('hc4.august')
                              @elseif($mes == '9')
                                @lang('hc4.september')
                              @elseif($mes == '10')
                                @lang('hc4.october')
                              @elseif($mes == '11')
                                @lang('hc4.november')
                              @elseif($mes == '12')
                                @lang('hc4.december')
                              @endif
                                @lang('hc4.of') {{substr($agenda_last->fechaini,0,4)}}
                        @endif
                      </td>
                    @else
                      @php
                        $doc = \Sis_medico\User::where('id', $paci->doctor)->first();
                      @endphp
                      <td class="color">@if(!is_null($doc)) {{$doc->nombre1}} {{$doc->apellido1}} @endif</td>
                      <td class="color" @if($fecha1!=$fecha2) style="font-size: 11px;" @endif>@if($fecha1!=$fecha2) {{substr($paci->fechaini,0,10)}}: @endif {{substr($paci->fechaini,10,10)}} - {{substr($paci->fechafin,10,10)}}
                      </td>
                    @endif

                    @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                        <td class="color">
                          @if(!is_null($agenda_last))
                            @if($agenda_last->proc_consul=='0')
                              @lang('hc4.consultation')
                            @elseif($agenda_last->proc_consul=='1')
                              @lang('hc4.procedure')
                            @elseif($agenda_last->proc_consul=='4')
                              @lang('hc4.visit')
                            @endif
                          @endif
                        </td>
                        <!--Hora-->
                        <td class="color">
                          @if(!is_null($agenda_last))
                             @if($agenda_last->cortesia=='SI')
                               <span style="color: red">@lang('hc4.yes')</span>
                             @else
                               @lang('hc4.no')
                             @endif
                          @endif
                        </td>
                        <!--Tipo-->
                        <td class="color">
                          @if($contador_cie10 >'1')
                            @lang('hc4.attended')
                          @else
                            @lang('hc4.not_attended')
                          @endif
                        </td>
                    @else
                        <!--Cortesia-->
                        <td class="color">
                          @if($paci->proc_consul=='0')
                            @lang('hc4.consultation')
                          @elseif($paci->proc_consul=='1')
                            @if(isset($agendas_proc[$paci->id_agenda]))
                              {{$agendas_proc[$paci->id_agenda]['0']}}
                            @else
                              @php
                                  if(!(($nombres!=null)||($apellidos!=null)||($cedula!=null))) {
                                      $nomb_proc = \Sis_medico\Procedimiento::where('id',$paci->id_procedimiento)->first();
                                      $agprocedimientos = \Sis_medico\AgendaProcedimiento::where('id_agenda',$paci->id_agenda)->get();
                                  }
                                @endphp
                                  @if(!is_null($nomb_proc))
                                    {{$nomb_proc->nombre}}
                                  @endif
                                  @if(!is_null($agprocedimientos))
                                    @foreach($agprocedimientos as $agendaproc)
                                      + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}}
                                    @endforeach
                                  @endif
                            @endif
                          @else

                            @if($paci->observaciones == 'PROCEDIMIENTO CREADO POR EL DOCTOR')
                              @php
                                if(!(($nombres!=null)||($apellidos!=null)||($cedula!=null))) {
                                  if($paci->hcid != ''){

                                    $hc_proce = \Sis_medico\hc_procedimientos::where('id_hc', $paci->hcid)->first();

                                    if(!is_null($hc_proce)){
                                      $proc_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_proce->id)->first();
                                    }

                                    if(!is_null($proc_final)){
                                      $procedimiento_nombre = \Sis_medico\Procedimiento::where('id', $proc_final->id_procedimiento)->first();
                                    }
                                  }
                                }
                              @endphp
                              @if(!is_null($procedimiento_nombre))

                                {{$procedimiento_nombre->nombre}}
                              @else
                                @lang('hc4.procedure')
                              @endif
                            @elseif($paci->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                              @if($paci->omni=='OM')
                                @lang('hc4.omni_visit')
                              @else
                                @lang('hc4.visit')
                              @endif
                            @endif
                          @endif
                        </td>
                        <!--Estado-->
                        <td class="color">
                          @if($paci->cortesia=='SI')
                            <span style="color: red">@lang('hc4.yes')</span>
                          @else
                            @lang('hc4.no')
                          @endif
                        </td>
                        @php
                          $estado = Sis_medico\Hc_Cie10::where('hcid',$paci->hcid)->first();
                        @endphp
                        <td class="color" style="font-size: 11px;">
                          @if($paci->estado_cita >= 4)
                            @if($paci->omni=='OM')
                              @if($paci->estado_cita==4)
                                @lang('hc4.admitted')
                              @elseif($paci->estado_cita==5)
                                @lang('hc4.discharge')
                              @elseif($paci->estado_cita==6)
                                @lang('hc4.emergency')
                              @endif
                            @else
                              @if(!is_null($estado))
                                @lang('hc4.attended')
                              @else
                                @lang('hc4.not_attended')
                              @endif
                            @endif
                          @elseif($paci->estado_cita == 0)
                            @lang('hc4.pending_confirmation')
                          @elseif($paci->estado_cita == 1)
                            @lang('hc4.confirmed')
                          @elseif($paci->estado_cita == 2)
                            @lang('hc4.rescheduled')
                          @elseif($paci->estado_cita == 3)
                            @lang('hc4.suspended')
                          @endif
                        </td>
                    @endif
                    @php
                      $id_agenda_accion = isset($paci->id_agenda) ? $paci->id_agenda : null;
                      if (is_null($id_agenda_accion) && isset($agenda_last) && !is_null($agenda_last)) {
                        $id_agenda_accion = $agenda_last->id_agenda;
                      }

                      $ruta_detalle = route('nd.buscador', array_filter([
                        'id_paciente' => $paci->id,
                        'id_agenda' => $id_agenda_accion,
                      ]));
                    @endphp
                    <td><div class="v3-clinical-list__action-stack">
                      <a class="v3-clinical-list__action v3-clinical-list__action--view" href="{{$ruta_detalle}}">
                        <i class="fas fa-eye" aria-hidden="true"></i>@lang('hc4.view_full_details')
                      </a>
                      @if(!is_null($id_agenda_accion))
                        <button type="button" class="v3-clinical-list__action v3-clinical-list__action--log" data-log-url="{{route('hc4controller.busca_log_agenda', ['id_agenda' => $id_agenda_accion])}}" onclick="abrirLogAgenda(this)">
                          <i class="fas fa-clipboard-list" aria-hidden="true"></i>Log de Agenda
                        </button>
                      @endif
                    </div></td>
                  </tr>
              @endforeach
              </tbody>
              @endif
             </table>
          </div>
        </div>
      </div>
      <footer class="v3-clinical-list__footer">
        <span>@if($nombres!=null || $apellidos != null || $cedula != null) @lang('hc4.total_patients') @else @lang('hc4.total_records') @endif</span>
        <strong>{{$pacientes->count()}}</strong>
      </footer>
  </div>
</section>

<style>
  /* Los accesos superiores de HC4 usan z-index: 99999. El modal se monta
     directamente en body y debe permanecer por encima de toda la cabecera. */
  #modal_log_agenda {
    position: fixed !important;
    z-index: 2147483647 !important;
  }

  #modal_log_agenda .modal-dialog,
  #modal_log_agenda .modal-content {
    position: relative !important;
    z-index: 2147483647 !important;
  }

  body.modal-open > .modal-backdrop.hc4-log-agenda-backdrop {
    z-index: 2147483600 !important;
  }
</style>

<div class="modal fade" id="modal_log_agenda" tabindex="-1" role="dialog" aria-labelledby="modal_log_agenda_titulo" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="margin-top: 35px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal_log_agenda_titulo">Log de Agenda</h4>
      </div>
      <div class="modal-body" id="modal_log_agenda_contenido" style="max-height: 72vh; overflow-y: auto;">
        <div class="text-center" style="padding: 25px;">Cargando...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
    // El resultado se inyecta dentro de un contenedor con su propio contexto
    // de apilamiento. Mover el modal al body evita que las tarjetas superiores
    // lo cubran y reemplaza cualquier instancia de una búsqueda anterior.
    $('body > #modal_log_agenda').remove();
    $('#modal_log_agenda').appendTo('body');

    $('#modal_log_agenda').on('show.bs.modal', function () {
      $(this).css({
        'position': 'fixed',
        'z-index': 2147483647
      });
      window.setTimeout(function () {
        $('.modal-backdrop').last()
          .addClass('hc4-log-agenda-backdrop')
          .css('z-index', 2147483600);
      }, 0);
    });

    function abrirLogAgenda(boton){
      var url = $(boton).data('log-url');
      var $contenido = $('#modal_log_agenda_contenido');

      $contenido.html('<div class="text-center" style="padding: 25px;">Cargando...</div>');
      $('#modal_log_agenda').modal('show');

      $.ajax({
        type: 'GET',
        url: url,
        datatype: 'html',
        success: function(datahtml){
          $contenido.html(datahtml);
        },
        error: function(xhr){
          $contenido.html('<div class="alert alert-danger">No se pudo cargar el log de agenda.</div>');
          if (typeof mostrarErrorAmbulatorio === 'function') {
            mostrarErrorAmbulatorio(xhr, 'cargar el log de agenda');
          }
        }
      });
    }

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

  $(function () {
    $('#example2{{$aleatorio}}').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        'language'    : {'emptyTable': 'No se encontraron pacientes con los criterios seleccionados.'},
        'order'       : [[ 6, "asc" ]]
      });
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
