   <style type="text/css">
    .boton_doctor{
      width: 95%;
    }
    .barra{
      padding: 5px;
    }
   </style>
   <div class="modal fade" id="mlog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document" style="width: 110% !important">
        <div class="modal-content">
          
        </div>
      </div>
    </div>
   <div class="box-header with-border" style="background-color: #124574;color: white; font-size: 14px; padding: 8px;">
        <div class="row">
          <div class="col-2 boton_doctor" style="padding-top: 8px">
            <span>@lang('hc4.resultadoBusqueda')</span>
          </div>
          <div class="barra col-lg-2 col-4">
            <button type="button" id="agenda_semana" class="btn btn-danger boton_doctor" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.agendaSemana')\@lang('hc4.mes')</button>
          </div>
          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="cargar_nuevopaciente();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;">  @lang('hc4.agregarNuevoPaciente')</a>
          </div>
          <div class="barra col-lg-2 col-4" style="text-align: center;" id="ex_excel">
            <a class="btn btn-danger boton_doctor" onclick="descargar_hc_reporte();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.exportarExcel')</a>
          </div>
          <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" id="ex_revision">
            <a class="btn btn-danger boton_doctor" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;" onclick="exportar_revision();"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.exportarRevision')</a>
          </div>

          @if(Auth::user()->id == '1307189140' || Auth::user()->id == '9666666666')
          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_master_hc();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticos')</a>
          </div>
          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_reales();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticassegurospriv')</a>
          </div>
          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_factura();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticasdefac')</a>
          </div>
          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_master_hc();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.estadisticos')</a>
          </div>


          <div class="barra col-lg-2 col-4" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_estimado();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> @lang('hc4.gananciaEstimada')</a>
          </div>


          <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_efectivo();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.gananciaEfectiva')</a>
          </div>

          <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_produccion();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> @lang('hc4.produccionMedicos')</a>
          </div>
          @endif

          <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="revisar_procedimientos();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> @lang('hc4.revisarProcedimientos')</a>
          </div>

          @if(Auth::user()->id != '1307189140')
          @endif
        </div>
      </div>
      <div class="box-body" style="border: 2px solid white;padding-left: 0px;padding-right: 0px;" id="modificar">
        <div class="content col-md-12"  style="padding-left: 5px;padding-right: 5px;">
          <div class="table-responsive" id="div_grafico">
            <h4> </h4>
            @php
              $aleatorio = rand();
            @endphp
            <table  id="example2{{$aleatorio}}" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
              @if($pacientes!=[])
              <thead style="">
                <tr style=" ">
                  <th scope="col" class="color titulo" >@lang('hc4.cedula')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.apellidos')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.nombres')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.fechaNacimiento')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.seguro')/@lang('hc4.convenio')</th>
                  @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                    <th scope="col" class="color titulo" >@lang('hc4.ultimaVisita')</th>
                  @else
                    <th scope="col" class="color titulo" >@lang('hc4.doctor')</th>
                    <th scope="col" class="color titulo" >@lang('hc4.hora')</th>
                  @endif
                  <th scope="col" class="color titulo" >@lang('hc4.tipo')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.cortesia')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.estado')</th>
                  <th scope="col" class="color titulo" >@lang('hc4.accion')</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pacientes as $paci)

                  @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                    @php

                    $agenda_last = DB::table('agenda as a')
                      ->where('a.id_paciente', $paci->id)
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


                      @php
                        if(!(($nombres!=null)||($apellidos!=null)||($cedula!=null))) {
                          $seguro_nombre = \Sis_medico\Seguro::where('id', $paci->seguro_nom)->first();
                        }
                      @endphp


                      @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                        @if(!is_null($agenda_last))
                            {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                        @endif
                      @else
                        @if($seguro_nombre) {{$seguro_nombre->nombre}} @endif/{{$paci->nombre_corto}}
                      @endif
                    </td>
                    <!--Última visita-->
                    @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                      <td class="color">
                        @if(!is_null($agenda_last))
                          @if($dia == '1')
                          @lang('hc4.lunes')
                          @elseif($dia == '2')
                          @lang('hc4.martes')
                          @elseif($dia == '3')
                          @lang('hc4.miercoles')
                          @elseif($dia == '4')
                          @lang('hc4.jueves')
                          @elseif($dia == '5')
                          @lang('hc4.viernes')
                          @elseif($dia == '6')
                          @lang('hc4.sabado')
                          @elseif($dia == '7')
                          @lang('hc4.domingo')
                          @endif
                            {{substr($agenda_last->fechaini,8,2)}} @lang('hc4.de')
                              @if($mes == '1')
                              @lang('hc4.enero')
                              @elseif($mes == '2')
                              @lang('hc4.febrero')
                              @elseif($mes == '3')
                              @lang('hc4.marzo')
                              @elseif($mes == '4')
                              @lang('hc4.abril')
                              @elseif($mes == '5')
                              @lang('hc4.mayo')
                              @elseif($mes == '6')
                              @lang('hc4.junio')
                              @elseif($mes == '7')
                              @lang('hc4.julio')
                              @elseif($mes == '8')
                              @lang('hc4.agosto')
                              @elseif($mes == '9')
                              @lang('hc4.septiembre')
                              @elseif($mes == '10')
                              @lang('hc4.octubre')
                              @elseif($mes == '11')
                              @lang('hc4.noviembre')
                              @elseif($mes == '12')
                              @lang('hc4.diciembre')
                              @endif
                              @lang('hc4.del') {{substr($agenda_last->fechaini,0,4)}}
                        @endif
                      </td>
                    @else
                      @php
                        $doc = \Sis_medico\User::where('id', $paci->doctor)->first();
                        $procedimiento_nombre = null;
                      @endphp
                      <td class="color">@if(!is_null($doc)) {{$doc->nombre1}} {{$doc->apellido1}} @endif</td>
                      <td class="color" @if($fecha1!=$fecha2) style="font-size: 11px;" @endif>@if($fecha1!=$fecha2) {{substr($paci->fechaini,0,10)}}: @endif {{substr($paci->fechaini,10,10)}} - {{substr($paci->fechafin,10,10)}}
                      </td>
                    @endif

                    @if(($nombres!=null)||($apellidos!=null)||($cedula!=null))
                        <td class="color">
                          @if(!is_null($agenda_last))
                            @if($agenda_last->proc_consul=='0')
                            @lang('hc4.consulta-')
                            @elseif($agenda_last->proc_consul=='1')
                            @lang('hc4.procedimiento-')
                            @elseif($agenda_last->proc_consul=='4')
                            @lang('hc4.visita-')
                            @endif
                          @endif
                        </td>
                        <!--Hora-->
                        <td class="color">
                          @if(!is_null($agenda_last))
                             @if($agenda_last->cortesia=='SI')
                               <span style="color: red"> @lang('hc4.si')</span>
                             @else
                             @lang('hc4.no')
                             @endif
                          @endif
                        </td>
                        <!--Tipo-->
                        <td class="color">
                          @if($contador_cie10 >'1')
                          @lang('hc4.atendido-')
                          @else
                          @lang('hc4.noatendido-')
                          @endif
                        </td>
                    @else
                        <!--Cortesia-->
                        <td class="color">
                          @if($paci->proc_consul=='0')
                            @if($paci->tc)
                            <span style="padding: 2px;">@lang('hc4.teleconsulta'):</span><br>
                            <span style="padding: 2px;">{{$paci->teleconsulta}}</span>
                            @else
                            @lang('hc4.consulta-')
                            @endif
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
                                PROCEDIMIENTO
                              @endif
                            @elseif($paci->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                              @if($paci->omni=='OM')
                                VISITA OMNI
                              @else
                                VISITA
                              @endif
                            @endif
                          @endif
                        </td>
                        <!--Estado-->
                        <td class="color">
                          @if($paci->cortesia=='SI')
                            <span style="color: red">@lang('hc4.si')</span>
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
                              @lang('hc4.ingresado')
                              @elseif($paci->estado_cita==5)
                              @lang('hc4.alta')
                              @elseif($paci->estado_cita==6)
                              @lang('hc4.emergencia')
                              @endif
                            @else
                              @if(!is_null($estado))
                              @lang('hc4.atendido')
                              @else
                              @lang('hc4.noAtendido')
                              @endif
                            @endif
                          @elseif($paci->estado_cita == 0)
                          @lang('hc4.porConfirmar')
                          @elseif($paci->estado_cita == 1)
                          @lang('hc4.confirmada')
                          @elseif($paci->estado_cita == 2)
                          @lang('hc4.reagendado')
                          @elseif($paci->estado_cita == 3)
                          @lang('hc4.suspendido')
                          @endif
                        </td>
                    @endif
                    <td>
                    @if($nombres!=null || $apellidos != null || $cedula != null)
                    <a class="btn btn-info boton-2" style="color: white; width: 100%; height: 100%; padding-left: 0px; padding-right: 0px" href="{{route('nd.buscador', ['id_paciente' => $paci->id])}}">
                    @lang('hc4.verDetalleCompleto')</a></td>
                    @else
                    @if($paci->estado_cita >= 4)<a class="btn btn-info boton-2" style="color: white; width: 100%; height: 100%; padding-left: 0px; padding-right: 0px" href="{{route('nd.buscador', ['id_paciente' => $paci->id])}}">
                    @lang('hc4.verDetalleCompleto')</a>@endif
                    <a class="btn btn-info boton-2" style="color: white; width: 100%; height: 100%; padding-left: 0px; padding-right: 0px" data-toggle="modal" data-target="#mlog" data-remote="{{route('hc4controller.busca_log_agenda',['id' => $paci->id_agenda])}}">
                    @lang('hc4.logAgenda')</a>
                    @endif
                    </td>
                  </tr>
              @endforeach
              </tbody>
              @endif
             </table>
              <label class="color" style="padding-left: 15px;font-size: 15px">@lang('hc4.totalRegistros'): {{$pacientes->count()}}</label>
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

  $(function () {
    $('#example2{{$aleatorio}}').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
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
