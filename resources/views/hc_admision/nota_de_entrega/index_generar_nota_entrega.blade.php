@extends('hc_admision.fullcontrol.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>

@php $tipo_usuario = Auth::user()->id_tipo_usuario; @endphp
<script type="text/javascript">
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}'
    });
    $("#fecha").on("dp.change", function(e) {
      fechacalendario();
      $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
      minDate: '{{$fecha_hasta}}',
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      fechacalendario();
    });

    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });

  $(function() { // document ready
  });

  function fechacalendario() {
    var dato = document.getElementById('fecha').value;
    $('#enviar_fecha').click();
  }

  var vartiempo = setInterval(function() {
    location.reload();
  }, 300000);
</script>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
    font-size: 14px;
  }
  #calendar {
    /*max-width: 900px;*/
    margin: 50px auto;
  }

  .table-hover>tbody>tr:hover {
    background-color: #ccffff !important;
  }
</style>
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="form-group col-md-12">
        <!--label class="col-md-1 control-label">Fecha</label-->
        <div class="col-md-12">
          <form method="POST" action="{{ route('receta.index_generar_nota_entrega') }}">
            {{ csrf_field() }}
            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha" class="col-md-3 control-label" style="padding:0px;">Desde:</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha" id="fecha">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha_hasta" class="col-md-3 control-label" style="padding-left: 0;">Hasta:</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">Paciente:</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-1 col-xs-2">
              <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="box-body">
      <div class="table-responsive col-md-12 col-xs-12">
        <table id="example2" class="table table-striped table-hover" style="font-size: 12px;">
          @if($agendas_pac!=[])
            <thead>
              <th width="5%"> {{trans('laboratorio.cedula')}} </th>
              <th width="15%"> {{trans('laboratorio.apellidos')}} </th>
              <th width="15%"> {{trans('laboratorio.nombres')}} </th>
              <th width="10%"> {{trans('laboratorio.fechaNacimiento')}} </th>
              <th width="5%"> {{trans('laboratorio.seguro')}}/{{trans('laboratorio.convenio')}} </th>
              <th width="15%"> {{trans('laboratorio.doctor')}} </th>
              <th width="15%"> {{trans('laboratorio.hora')}} </th>
              <th width="15%"> {{trans('laboratorio.cedula')}} </th>
              <th width="20%"> {{trans('laboratorio.cortesia')}} </th>
              <th width="20%"> {{trans('laboratorio.estado')}} </th>
            </thead>
            <tbody>
              @php $nomb_proc = null; $agprocedimientos = null; $apellidos = null; @endphp
              @foreach ($agendas_pac as $pac)
                @php
                  $contador_cie10 = 0;
                  $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();
                  $tipo_usuario = Auth::user()->id_tipo_usuario;
                  $hc_proc_p = Sis_medico\Aud_Hc_Procedimientos::where('id_hc',$pac->hcid)->first();
                  $agenda_last = DB::table('agenda as a')
                    ->where('a.id_paciente',$pac->id)
                    ->join('historiaclinica as h','h.id_agenda','a.id')
                    ->where('a.espid','<>','10')
                    ->orderBy('a.fechaini','desc')
                    ->join('seguros as s','s.id','h.id_seguro')
                    ->join('empresa as em','em.id','a.id_empresa')
                    ->select('h.*','s.nombre','a.fechaini','a.proc_consul','a.cortesia','em.nombre_corto')
                    ->first();
                @endphp
                <tr @if($tipo_usuario=='11' ) @if($pac->hcid!=null)
                  @else
                    onclick="javascript:alert('Agenda no Admisionada');"
                  @endif
                  @else class='clickable-row'
                    data-href='{{ route("receta.detalle_receta_nota_entrega", ['id' => $pac->id_agenda])}}'
                  @endif @if($pac->cortesia=='SI') style="background-color: #ccffcc;"
                  @endif>
                  <td>{{$pac->id}}</td>
                  <td>{{$pac->apellido1}} {{$pac->apellido2}}</td>
                  <td>{{$pac->nombre1}} {{$pac->nombre2}}</td>
                  <td>{{$pac->fecha_nacimiento}} </td>
                  @php
                  $hc_seguro = '';
                  $nombre_seg = Sis_medico\Seguro::find($pac->seguro_nom)->nombre;
                  $hc_proc = Sis_medico\hc_procedimientos::where('id_hc',$pac->hcid)->first();
                  $hc_proc_p = Sis_medico\Aud_Hc_Procedimientos::where('id_hc',$pac->hcid)->first();
                  if(!is_null($hc_proc)){
                    if($hc_proc->id_seguro!=null){
                      $hc_seguro = Sis_medico\Seguro::find($hc_proc->id_seguro)->nombre;
                    }
                  }
                  @endphp
                  <td >@if($pac->omni=='OM') {{$hc_seguro}}/{{$pac->nombre_corto}} @else {{$nombre_seg}}/{{$pac->nombre_corto}} @endif</td>
                  @php $doctor = $doctores->find($pac->doctor); $procedimiento_nombre = null; @endphp
                  <td>@if(!is_null($doctor)) {{$doctor->apellido1}} {{$doctor->nombre1}} @endif</td>
                  <td @if($fecha!=$fecha_hasta) style="font-size: 11px;" @endif>@if($fecha!=$fecha_hasta) {{substr($pac->fechaini,0,10)}}: @endif {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}</td>
                  <td style="font-size: 11px;">
                    <b>
                      @if($pac->proc_consul=='0')
                        @lang('laboratorio.consulta-')
                      @elseif($pac->proc_consul=='1')
                        @if(isset($agendas_proc[$pac->id_agenda]))
                          {{$agendas_proc[$pac->id_agenda]['0']}}
                        @else
                          @php
                            if($nombres!=null) {  
                              $nomb_proc = \Sis_medico\Procedimiento::where('id',$pac->id_procedimiento)->first();
                              $agprocedimientos = \Sis_medico\AgendaProcedimiento::where('id_agenda',$pac->id_agenda)->get();
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
                        @if($pac->observaciones == 'PROCEDIMIENTO CREADO POR EL DOCTOR')
                          @php
                            if(!(($nombres!=null)||($apellidos!=null))) {
                              if($pac->hcid != ''){
                                $hc_proce = \Sis_medico\hc_procedimientos::where('id_hc', $pac->hcid)->first();
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
                            {{$procedimiento_nombre->nombre}}
                          @else
                            @lang('laboratorio.procedimiento-')
                          @endif
                        @elseif($pac->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                          @if($pac->omni=='OM')
                            @lang('laboratorio.visita-') OMNI
                          @else
                            @lang('laboratorio.visita-')
                          @endif
                        @endif
                      @endif
                    </b>
                  </td>
                  <td style="font-size: 11px;"><b>@if($pac->cortesia=='SI')<span style="color: red"> @lang('laboratorio.si')</span> @else @lang('laboratorio.no') @endif</b></td>
                  <td style="font-size: 11px;">
                    <b>
                      @if($pac->omni=='OM')
                        @if($pac->estado_cita=='4')
                          @lang('laboratorio.ingreso-')
                        @elseif($pac->estado_cita=='5')
                          @lang('laboratorio.alta-')
                        @elseif($pac->estado_cita=='6')
                          @lang('laboratorio.emergencia-')
                        @endif
                      @else
                        @if($contador_cie10 >='1')
                          @lang('laboratorio.atendido-')
                        @else
                          @lang('laboratorio.noatendido-')
                        @endif
                      @endif
                    </b>
                  </td>
                </tr>
              @endforeach
            </tbody>
          @endif
        </table>
        {{$agendas_pac->count()}} @lang('laboratorio.registros')
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false,
    });
  });
</script>
@endsection