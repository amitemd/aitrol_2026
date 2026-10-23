<style type="text/css">

  .btn-detalle{
      font-size: 10px ;
      width: 100%;
      height: 100%;
      background-color:#124574;
      color: white;
      border-radius: 5px;
  }
  #reuniones .row{
    width: 100%;
  }
  .row_completo .row{
    width: 100%;
  }
  .boton_doctor{
    width: 95%;
  }
  .barra{
    padding: 5px;
  }
</style>


<div class="box box" style="border-radius: 8px;" id="area_busqueda_doctor">
  <div class="box-header with-border" style="background-color: #124574;color: white; font-size: 14px; padding: 8px;">
    <div class="row">
      <div class="barra col-lg-2 col-4">
        <span>@lang('hc4.resultadoBusqueda')</span>
      </div>
      <div class="barra col-lg-2 col-4">
        <button type="button" id="agenda_semana" class="btn btn-danger boton_doctor" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.agendaSemana')\@lang('hc4.mes')</button>
      </div>
      <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="cargar_nuevopaciente();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;">  @lang('hc4.agregarNuevoPaciente')</a>
      </div>

      @if(Auth::user()->id == '1307189140' || Auth::user()->id == '9666666666' || Auth::user()->id == '1316262193')
      <div class="barra col-lg-2 col-4 estadisticos" style="text-align: center;" >
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_master_hc();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticos')</a>
      </div>
      <div class="barra col-lg-2 col-4 estadisticos" style="text-align: center;" >
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_reales();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticassegurospriv')</a>
      </div>
      <div class="barra col-lg-2 col-4 estadisticos" style="text-align: center;" >
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_factura();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticasdefac')</a>
      </div>

      <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_estimado();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.gananciaEstimada')</a>
      </div>

      <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_efectivo();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.gananciaEfectiva')</a>
      </div>

      <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
        <a class="btn btn-danger boton_doctor" onclick="estadisticos_produccion();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.produccionMedicos')</a>
      </div>
      @endif

      <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" style="text-align: center;">
        <a class="btn btn-danger boton_doctor" onclick="revisar_procedimientos();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;">@lang('hc4.revisarProcedimientos')</a>
      </div>
      <div class="barra col-lg-2 col-4" style="text-align: center;" id="ex_excel">
            <a class="btn btn-danger boton_doctor" onclick="descargar_hc_reporte();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.exportarExcel')</a>
      </div>
      <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" id="ex_revision">
            <a class="btn btn-danger boton_doctor" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;" onclick="exportar_revision();"> <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.exportarRevision')</a>
      </div>
    </div>
  </div>
  <div class="box-body" style="border: 2px solid white;padding-left: 0px;padding-right: 0px;" id="modificar">
    <div class=" col-md-12"   id="div_grafico">
      @if($agendas_reuniones->count() > 0)
      <div class="box box-primary" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#reu').click());"><b>@lang('hc4.reuniones')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>@lang('hc4.tiene') {{$agendas_reuniones->count()}} @lang('hc4.agendadasenelDia')</b>
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
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.hora')</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.tipo')</th>
                      <th width="55%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.nombre')</th>
                      <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >@lang('hc4.lugar')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_reuniones as $pac)
                  <tr role="row" class="odd">
                    <td class="color">{{substr($pac->fechaini,11,8)}} - {{substr($pac->fechafin,11,8)}}</td>
                    <td class="color">{{$pac->procedencia}}</td>
                    <td class="color">{{$pac->observaciones}}</td>
                    @php
                      $sala = \Sis_medico\Sala::find($pac->id_sala);
                    @endphp
                    <td class="color"> {{$sala->nombre_sala}} / {{$sala->hospital->nombre_hospital}}</td>

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

      @php
        $array_alerta = array();
      @endphp
      @if($agendas_pac->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#consult').click());"><b>@lang('hc4.consultas')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>@lang('hc4.tiene') {{$agendas_pac->count()}} @lang('hc4.agendadasenelDia')</b>
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
                      <th scope="col" class="color titulo" >@lang('hc4.cedula')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.apellidos')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.nombres')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.fechaNacimiento')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.seguro')/@lang('hc4.convenio')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.hora')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.tipo')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.cortesia')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.estado')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.accion')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac as $pac)
                    @php $agenda_last = DB::table('agenda as a')
                        ->where('a.id_paciente', $pac->id)
                        ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                        ->where('a.espid', '<>', '10')
                        ->orderBy('a.fechaini', 'asc')
                        ->join('seguros as s', 's.id', 'h.id_seguro')
                        ->join('empresa as em', 'em.id', 'a.id_empresa')
                        ->select('h.*', 's.nombre', 'a.fechaini', 'a.proc_consul', 'a.cortesia', 'em.nombre_corto')
                        ->first();

                      if (!is_null($agenda_last)) {
                          $dia = Date('N', strtotime($agenda_last->fechaini));
                          $mes = Date('n', strtotime($agenda_last->fechaini));
                      } else {
                          $dia = 0;
                          $mes = 0;
                      }
                      $dt = new DateTime($pac->fechaini);
                      $hora_inicio = $dt->modify("-90 minutes")->format("Y-m-d H:i:s");
                      $hora_fin = $dt->modify("+120 minutes")->format("Y-m-d H:i:s");

                      $turno = DB::table('reservaciones_turno')
                              ->where('cedula', $pac->id)
                              ->whereBetween('created_at', [$hora_inicio, $hora_fin])
                              ->first();
                    @endphp

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
                        @if($pac->tc)
                        <span style="padding: 2px;">@lang('hc4.teleconsulta'):</span><br>
                        <span style="padding: 2px;">{{$pac->teleconsulta}}</span>
                        @else
                        @lang('hc4.consulta-')
                        @endif
                      @elseif($pac->proc_consul=='1')
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}}
                        @else
                        @lang('hc4.procedimiento-')
                        @endif
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red">
                         @lang('hc4.si')
                         </span>
                        @else
                         <span class="color">
                         @lang('hc4.no')
                         </span>
                        @endif
                      </b>
                    </td>
                    @php

                      $contador_cie10 = 0;


                      $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $pac->id_agenda)->first();
                      if($pac->proc_consul=='0'){
                        if(!is_null($verificar)){
                          $consulta = \Sis_medico\Hc_Evolucion::where('hcid', $verificar->hcid)->first();
                        }else{
                          $consulta = null;
                        }
                      }else{
                        $consulta = null;
                      }
                      $nueva_agenda = \Sis_medico\Agenda::find($pac->id_agenda)->first();

                      $contador_cie10 = 0;$receta_ok = 0;
                      if($pac->hcid!=null){
                        $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();
                        $receta = Sis_medico\hc_receta::where('id_hc',$pac->hcid)->first();
                        if(!is_null($receta)){
                            if($receta->rp!=null && $receta->prescripcion!=null){
                                $receta_ok = 1;
                            }
                        }
                      }
                    @endphp
                    <td class="color">
                      @if($pac->omni=='OM')
                      @if($pac->estado_cita==4)
                      @lang('hc4.ingresado')
                      @elseif($pac->estado_cita==5)
                      @lang('hc4.alta')
                      @elseif($pac->estado_cita==6) 
                      @lang('hc4.emergencia')
                      @endif
                    @elseif($contador_cie10 >'1')
                    @lang('hc4.atendido-')
                    @elseif(!is_null($verificar))
                    @lang('hc4.admisionado-')
                    @else
                      @if($nueva_agenda->estado_cita == 0)
                      @lang('hc4.porConfirmar')
                      @elseif($nueva_agenda->estado_cita == 1)
                      @lang('hc4.confirmada')
                      @elseif($nueva_agenda->estado_cita == 2)
                      @lang('hc4.reagendado')
                      @endif
                    @endif
                    </td>

                    <td>@if(!is_null($verificar))
                      <a class="btn btn-info btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                      @lang('hc4.verDetalleCompleto')</a><br>
                      @php
                        $hcproc = \Sis_medico\hc_procedimientos::where('id_hc', $verificar->hcid)->first();
                        $datos = $pac->apellido1.' '.$pac->apellido2.' '.$pac->nombre1;
                        array_push($array_alerta, $datos);
                      @endphp
                        @if(is_null($hcproc->hora_fin) && !is_null($hcproc->hora_inicio))
                          <a class="btn btn-danger btn-cita btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px">@lang('hc4.consultasinfinalizar') </a>
                        @endif
                      @endif
                    </td>

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
                <h3 class="box-title"><a href="javascript:void($('#proced').click());"><b>@lang('hc4.procedimiento')</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>@lang('hc4.tiene') {{$agendas_pac_procedimientos->count()}} @lang('hc4.agendadasenelDia')</b>
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
                      <th scope="col" class="color titulo" >@lang('hc4.cedula')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.apellidos')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.nombres')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.fechaNacimiento')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.seguro')/@lang('hc4.convenio')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.hora')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.tipo')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.cortesia')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.estado')</th>
                      <th scope="col" class="color titulo" >@lang('hc4.accion')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac_procedimientos as $pac)

                    <?php $agenda_last = DB::table('agenda as a')
                        ->where('a.id_paciente', $pac->id)
                        ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                        ->where('a.espid', '<>', '10')
                        ->where('a.proc_consul', '<>', '4')
                        ->orderBy('a.fechaini', 'asc')
                        ->join('seguros as s', 's.id', 'h.id_seguro')
                        ->join('empresa as em', 'em.id', 'a.id_empresa')
                        ->select('h.*', 's.nombre', 'a.fechaini', 'a.proc_consul', 'a.cortesia', 'em.nombre_corto')
                        ->first();
                    
                    if (!is_null($agenda_last)) {
                        $dia = Date('N', strtotime($agenda_last->fechaini));
                        $mes = Date('n', strtotime($agenda_last->fechaini));
                    } else {
                        $dia = 0;
                        $mes = 0;
                    }
                    ?>
                    <tr>
                    @php

                      $contador_cie10 = 0;

                      $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $pac->id_agenda)->first();
                      $nueva_agenda = \Sis_medico\Agenda::find($pac->id_agenda);

                      $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();


                    @endphp
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
                      @lang('hc4.consulta-')
                      @elseif($pac->proc_consul=='1')
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}}
                        @else

                          @php
                            $procedimiento_agenda = \Sis_medico\Procedimiento::find($nueva_agenda->id_procedimiento);
                            $resto_procedimientos = \Sis_medico\AgendaProcedimiento::where('id_agenda',$pac->id_agenda)->get();
                            $text_pro = '';
                            foreach($resto_procedimientos as $px){
                              $procedimiento_r = \Sis_medico\Procedimiento::find($px->id_procedimiento);
                              $text_pro = $text_pro.' + '.$procedimiento_r->nombre;
                            }
                          @endphp
                          @if(!is_null($procedimiento_agenda))
                            {{$procedimiento_agenda->nombre.$text_pro}}

                          @else
                          @lang('hc4.procedimiento-')
                          @endif
                        @endif
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red">
                         @lang('hc4.si')
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
                        @lang('hc4.ingresado')
                        @elseif($pac->estado_cita==5)
                        @lang('hc4.alta')
                        @elseif($pac->estado_cita==6)
                        @lang('hc4.emergencia')
                        @endif
                      @elseif($contador_cie10 >'1')
                      @lang('hc4.atendido-')
                      @elseif(!is_null($verificar))
                      @lang('hc4.admisionado-')
                      @else
                        @if($nueva_agenda->estado_cita == 0)
                        @lang('hc4.porConfirmar')
                        @elseif($nueva_agenda->estado_cita == 1)
                        @lang('hc4.confirmada')
                        @elseif($nueva_agenda->estado_cita == 2)
                        @lang('hc4.reagendado')
                        @endif
                      @endif
                    </td>

                    <td>@if(!is_null($verificar))
                        <a class="btn btn-info btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                        @lang('hc4.verDetalleCompleto')</a>
                      @endif
                    </td>

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
    function estadisticos_reales(){
      //estadisticos_hc4.privados
      $.ajax({
          type: 'post',
          url:"{{route('estadisticos_hc4_s.privados')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form").serialize(),
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
    function estadisticos_factura(){
      //estadisticos_hc4.privados
      $.ajax({
          type: 'post',
          url:"{{route('venta.estadisticoshc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form").serialize(),
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
    // ganancia efectiva
    function estadisticos_efectivo(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4.ganancia_efectiva')}}",
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
    // Producción de medicos
    function estadisticos_produccion(){
        $.ajax({
          type: 'get',
          url:"{{route('produccion.estad_index')}}",
          datatype: 'html',
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

    function exportar_revision(){
      $('#exp_rev').click();
    }

</script>

<script type="text/javascript">
  $(document).ready(function() {
  if ( $("#ex_revision").length > 0 ) {
    $('#ex_revision').hide();
  }
  });
</script>




