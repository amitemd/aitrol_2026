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

  #modal_log_agenda .modal-dialog{width:min(900px,calc(100% - 30px));margin:32px auto}
  #modal_log_agenda .modal-content{overflow:hidden;border:0;border-radius:18px;background:#f5f9fb;box-shadow:0 28px 80px rgba(5,43,68,.34)}
  #modal_log_agenda .modal-header{display:flex;align-items:center;justify-content:space-between;min-height:82px;padding:16px 20px;border:0;color:#fff;background:linear-gradient(120deg,#075f8f,#078bb6 58%,#10a39f)}
  #modal_log_agenda .modal-header__identity{display:flex;align-items:center;gap:12px;min-width:0}
  #modal_log_agenda .modal-header__icon{display:grid;width:44px;height:44px;flex:0 0 44px;place-items:center;border:1px solid rgba(255,255,255,.3);border-radius:13px;background:rgba(255,255,255,.14);font-size:18px}
  #modal_log_agenda .modal-title{margin:0;color:#fff;font-size:18px;font-weight:800;line-height:1.25}
  #modal_log_agenda .modal-header__identity small{display:block;margin-top:3px;color:rgba(255,255,255,.8);font-size:10px;letter-spacing:.02em}
  #modal_log_agenda .modal-header .close{position:static!important;display:inline-flex!important;width:36px;height:36px;min-width:36px;flex:0 0 36px;align-items:center;justify-content:center;float:none!important;margin:0!important;padding:0!important;border:1px solid rgba(255,255,255,.28);border-radius:10px;color:#fff;background:rgba(0,0,0,.08);font-size:15px;line-height:1!important;opacity:1;text-shadow:none;transition:background .18s,transform .18s}
  #modal_log_agenda .modal-header .close i{display:block;width:auto;height:auto;margin:0;padding:0;font-size:15px;line-height:1}
  #modal_log_agenda .modal-header .close:hover{background:rgba(0,0,0,.18);transform:rotate(4deg)}
  #modal_log_agenda .modal-body{max-height:72vh;padding:18px 20px;overflow-y:auto;background:#f5f9fb}
  #modal_log_agenda .modal-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-top:1px solid #dce8ed;background:#fff;text-align:initial}
  #modal_log_agenda .modal-footer__note{display:flex;align-items:center;gap:6px;color:#6b8290;font-size:10px}
  #modal_log_agenda .modal-footer .btn{min-width:105px;padding:8px 16px;border:0;border-radius:9px;color:#fff;background:#087da8;font-weight:750}
  .hc4-log-loading{display:flex;min-height:220px;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:#5b7889;text-align:center}
  .hc4-log-loading i{color:#0789b3;font-size:27px}.hc4-log-loading strong{color:#274e65;font-size:13px}.hc4-log-loading span{font-size:10px}
  .hc4-log-error{display:flex;min-height:180px;align-items:center;justify-content:center;flex-direction:column;gap:8px;padding:24px;color:#8d3434;text-align:center}.hc4-log-error i{font-size:28px}.hc4-log-error strong{font-size:14px}.hc4-log-error span{color:#816363;font-size:11px}
  .hc4-agenda-log__summary{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:14px;padding:12px 14px;border:1px solid #d8e7ed;border-radius:12px;background:#fff}.hc4-agenda-log__summary strong,.hc4-agenda-log__summary span{display:block}.hc4-agenda-log__summary strong{color:#183f58;font-size:12px}.hc4-agenda-log__summary>div>span{margin-top:3px;color:#718896;font-size:10px}.hc4-agenda-log__secure{display:inline-flex!important;align-items:center;gap:6px;padding:6px 9px;border-radius:12px;color:#137463;background:#e8f7f3;font-size:9px;font-weight:800;text-transform:uppercase}
  .hc4-agenda-log__list{display:grid}.hc4-agenda-log__item{display:grid;grid-template-columns:28px minmax(0,1fr)}.hc4-agenda-log__rail{position:relative}.hc4-agenda-log__rail:before{position:absolute;left:13px;top:0;bottom:0;width:2px;background:#cfe1e8;content:""}.hc4-agenda-log__item:first-child .hc4-agenda-log__rail:before{top:25px}.hc4-agenda-log__item:last-child .hc4-agenda-log__rail:before{bottom:calc(100% - 26px)}.hc4-agenda-log__rail span{position:absolute;z-index:1;left:8px;top:20px;width:12px;height:12px;border:3px solid #e9f7fa;border-radius:50%;background:#0789b3;box-shadow:0 0 0 2px #8acbd9}
  .hc4-agenda-log__card{margin-bottom:12px;padding:14px 15px;border:1px solid #d8e6ec;border-radius:13px;background:#fff;box-shadow:0 5px 15px rgba(12,72,99,.06)}.hc4-agenda-log__card header{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding-bottom:11px;border-bottom:1px solid #e6eef2}.hc4-agenda-log__event{display:flex;align-items:center;gap:9px;min-width:0}.hc4-agenda-log__icon{display:grid;width:33px;height:33px;flex:0 0 33px;place-items:center;border-radius:9px;color:#087da8;background:#e8f6fa}.hc4-agenda-log__event strong,.hc4-agenda-log__event small{display:block}.hc4-agenda-log__event strong{color:#173f58;font-size:12px;line-height:1.35}.hc4-agenda-log__event small{margin-top:2px;color:#8ba0ac;font-size:9px}.hc4-agenda-log__card time{flex:0 0 auto;text-align:right}.hc4-agenda-log__card time strong,.hc4-agenda-log__card time span{display:block}.hc4-agenda-log__card time strong{color:#087da8;font-size:13px}.hc4-agenda-log__card time span{margin-top:2px;color:#738b99;font-size:9px}
  .hc4-agenda-log__user{display:flex;align-items:center;gap:8px;padding:11px 0 0}.hc4-agenda-log__avatar{display:grid;width:29px;height:29px;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#087fab,#11a29f);font-size:9px;font-weight:900}.hc4-agenda-log__user small,.hc4-agenda-log__user strong{display:block}.hc4-agenda-log__user small{color:#879aa5;font-size:8px;text-transform:uppercase}.hc4-agenda-log__user strong{margin-top:1px;color:#395c70;font-size:10px}
  .hc4-agenda-log__changes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:11px}.hc4-agenda-log__changes>div{display:flex;align-items:flex-start;gap:8px;padding:9px 10px;border-radius:9px;color:#4e6d7e;background:#f2f7f9}.hc4-agenda-log__changes>div>i{width:15px;padding-top:2px;color:#148ca2;text-align:center}.hc4-agenda-log__changes span,.hc4-agenda-log__changes small,.hc4-agenda-log__changes strong{display:block}.hc4-agenda-log__changes small{color:#8599a5;font-size:8px;text-transform:uppercase}.hc4-agenda-log__changes strong{margin-top:2px;color:#385b6e;font-size:9px;font-weight:750;line-height:1.45}.hc4-agenda-log__changes em{color:#8a6c6c;font-style:normal;text-decoration:line-through}.hc4-agenda-log__change--wide{grid-column:1/-1}.hc4-agenda-log__no-detail{margin:10px 0 0;color:#7d929e;font-size:9px}.hc4-agenda-log__no-detail i{margin-right:5px;color:#55a4b5}.hc4-agenda-log__empty{display:flex;min-height:230px;align-items:center;justify-content:center;flex-direction:column;color:#7a909d;text-align:center}.hc4-agenda-log__empty>i{display:grid;width:58px;height:58px;margin-bottom:10px;place-items:center;border-radius:17px;color:#168da8;background:#e8f6f9;font-size:23px}.hc4-agenda-log__empty strong{color:#31566c;font-size:13px}.hc4-agenda-log__empty span{margin-top:4px;font-size:10px}
  @media(max-width:640px){#modal_log_agenda .modal-dialog{width:calc(100% - 16px);margin:8px auto}#modal_log_agenda .modal-content{border-radius:13px}#modal_log_agenda .modal-header{padding:13px 14px}#modal_log_agenda .modal-body{max-height:calc(100vh - 150px);padding:12px}#modal_log_agenda .modal-footer{padding:10px 13px}.hc4-agenda-log__summary{align-items:flex-start;flex-direction:column}.hc4-agenda-log__changes{grid-template-columns:1fr}.hc4-agenda-log__change--wide{grid-column:auto}.hc4-agenda-log__card header{align-items:flex-start}.hc4-agenda-log__card{padding:12px}.hc4-agenda-log__item{grid-template-columns:22px minmax(0,1fr)}.hc4-agenda-log__rail:before{left:10px}.hc4-agenda-log__rail span{left:5px}}
</style>

<div class="modal fade" id="modal_log_agenda" tabindex="-1" role="dialog" aria-labelledby="modal_log_agenda_titulo" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-header__identity"><span class="modal-header__icon"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span><div><h4 class="modal-title" id="modal_log_agenda_titulo">Historial de la agenda</h4><small>Movimientos, responsables y cambios registrados</small></div></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><i class="fas fa-times" aria-hidden="true"></i></button>
      </div>
      <div class="modal-body" id="modal_log_agenda_contenido">
        <div class="hc4-log-loading"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i><strong>Cargando historial</strong><span>Estamos consultando los movimientos de la agenda.</span></div>
      </div>
      <div class="modal-footer">
        <span class="modal-footer__note"><i class="fas fa-lock" aria-hidden="true"></i> Registro de auditoría de solo lectura</span>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
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

      $contenido.html('<div class="hc4-log-loading"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i><strong>Cargando historial</strong><span>Estamos consultando los movimientos de la agenda.</span></div>');
      $('#modal_log_agenda').modal('show');

      $.ajax({
        type: 'GET',
        url: url,
        datatype: 'html',
        success: function(datahtml){
          $contenido.html(datahtml);
        },
        error: function(xhr){
          $contenido.html('<div class="hc4-log-error"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><strong>No se pudo cargar el historial</strong><span>Intenta nuevamente o actualiza la página.</span></div>');
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
