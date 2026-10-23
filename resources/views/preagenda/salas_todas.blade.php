
@extends('agenda.base')

@section('action-content')
@php



    $maximo_procedimientos = Sis_medico\Max_Procedimiento::find('1')->cantidad;

    $alerta_procedimientos = Sis_medico\Max_Procedimiento::find('2')->cantidad;

    $user_agenda = Auth::user()->id;
    $permiso = Sis_medico\Agenda_Permiso::where('id_usuario',$user_agenda)->where('proc_consul','1')->where('estado','2')->first();
@endphp
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




<style>

  body {
    margin: 0;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    /*max-width: 900px;*/
    margin: 50px auto;
  }

  .fc-toolbar{
    margin-bottom: 0px !important;
    padding-bottom: 2px !important;
    padding-top: 2px !important;
  }

  .fc-unthemed{
    margin-top: 0px !important;
  }

  .blink {
      animation: blinker 0.9s linear infinite;

      font-size: 15px;

      font-family: sans-serif;
    }
    @keyframes blinker {
      50% { opacity: 0; }
    }



</style>

<section class="content" >

    <div class="box">
        <div class="box-header" style="padding-bottom: 2px;">


            <div class="col-md-12">
                <form method="POST" id="f_sala" >
                    {{ csrf_field() }}
                    <label class="col-md-1 control-label">Fecha</label>
                    <div class="col-md-2">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="fecha" class="form-control input-sm" id="fecha" onchange="carga_sala();" autocomplete="off">
                        </div>
                    </div>


                    <div class="form-group col-md-2 col-xs-4" >
                        <button type="submit" class="btn btn-primary" formaction="{{ route('preagenda.to_excel') }}"><span class="glyphicon glyphicon-download" aria-hidden="true"> Descargar</button>
                    </div>

                </form>
                <div class="form-group col-md-2 col-xs-4" id="div_cantidad"></div>

            </div>
            <div class="form-group col-md-2 col-xs-4" id="div_alerta"></div>


        </div>
        <div class="box-body" id="div_sala" style="padding: 0;">
            <script>

                $(function () {
                    $('#fecha').datetimepicker({
                        format: 'YYYY/MM/DD',
                        defaultDate: '{{$fecha}}'
                        });
                    $("#fecha").on("dp.change", function (e) {
                        carga_sala();
                    });
                });

                $(function() { // document ready

                    @php $fecha_valida = date("Y-m-d"); @endphp

                    $('#calendar').fullCalendar({
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        defaultView: 'agendaDay',
                        defaultDate: '{{$fecha}}',
                        editable: false,
                        allDaySlot: false,
                        minTime: "05:00:00",
                        views:{
                            agenda:{
                                slotDuration: "00:15:00",
                                slotLabelFormat: 'HH:mm',
                                scrollTime: "07:00:00",
                            }
                        },
                        selectable: true,
                        eventLimit: true, // allow "more" link when too many events
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'agendaDay,agendaTwoDay,agendaWeek,month'
                        },

                        //// uncomment this line to hide the all-day slot
                        //allDaySlot: false,

                        resources: [
                            @foreach($salas as $sala)
                                @if($sala->proc_consul_sala!='2')
                                    { id: '{{$sala->id}}', title: '{{$sala->nombre_sala}}' },
                                @endif
                            @endforeach
                        ],
                         resources: [
                            @foreach($salas as $sala)
                                {
                                  id: '{{$sala->id}}',
                                  title: '{{$sala->nombre_sala}}'
                                },
                            @endforeach
                        ],
                        events: [
                            @foreach($agendas as $value)
                            {
                                id: '{{$value->id}}',
                                resourceId: '{{$value->id_sala}}',
                                start: '{{$value->fechaini}}',
                                end: '{{$value->fechafin}}',
                                @if($value->fechaini <= $fecha_valida)
                                    editable: false,
                                @else
                                    editable: true,
                                @endif
                                @php  $agendaprocedimientos=DB::table('agenda_procedimiento')->where('id_agenda',$value->id)->get();
                                      $hc = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                                      $hc_seguro_nombre = '';
                                      if(!is_null($hc)){
                                          if(!is_null($hc->seguro)){
                                              $hc_seguro_nombre = $hc->seguro->nombre;
                                          }

                                      }
                                @endphp
                                @if($value->estado == '-1')

                                    title: '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  |{{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR**' ,
                                    url: '{{ route('preagenda.edit', ['id' => $value->id])}}',
                                @endif
                                @if($value->estado == '1')
                                    title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif | Dr(a). {{$value->dnombre1}} {{$value->dapellido1}} | {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif |  Agdo por: {{$value->uapellido1}} | @if($value->estado_cita!='4'){{ $value->nombre_seguro}}@else {{$hc_seguro_nombre}}@endif | Cortesia: {{ $value->cortesia}}',
                                    url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                                @endif
                                @if($value->paciente_dr == 0)
                                    @if($value->estado_cita == 0)
                                        color: 'black',
                                    @else
                                        color: '{{ $value->color}}',
                                        textColor: 'black',

                                    @endif
                                @endif
                                @if($value->paciente_dr == 1)
                                    @if($value->estado_cita == 0)
                                        color: 'black',
                                    @else
                                        color: '#56070C',
                                        textColor: 'white',
                                    @endif
                                @endif
                                @if($value->supervisa_robles == 1)
                                  color: 'red',
                                @endif
                                @if($value->solo_robles == 1)
                                  color: 'purple',
                                @endif
                                description: '{{$value->observacion_proc}}',
                            },

                            @endforeach
                            @foreach($salas as $x)
                              @php
                                $horario = DB::table('horario_sala')
                                ->where('id_sala', '=', $x->id)->orderBy('ndia')
                                ->orderBy('hora_ini')
                                ->get(); //dd($value->id_sala);
                              @endphp
                       @foreach($horario as $key=>$xx)
                            {
                              resourceId: '{{$x->id}}',
                              start: '{{$xx->hora_ini}}',
                              end: '{{$xx->hora_fin}}',
                              color: '@if($xx->tipo == 0) #61c9ff @endif @if($xx->tipo == 1) #6666FF @endif @if($xx->tipo == 2) #FF00BF @endif @if($xx->tipo == 3) #00e600 @endif',
                              rendering: 'background',
                              @if($xx->ndia != 7)
                              dow: [ {{$xx->ndia}}],
                              @endif
                              @if($xx->ndia == 7)
                                  dow: [0],
                              @endif
                            },
                  @endforeach
                @endforeach
                        ],

                        eventDrop: function(event, delta, start, end){ // event drag and drop

                               var start = event.start;
                               var id = event.id;
                               var end = event.end;
                               var resourceId = event.resourceId;

                               var url = "{{ url('salas_todas/actualiza') }}/"+id+"/"+start+"/"+end+"/"+resourceId;//salas_todas.desplazamiento
                               $.get(url, function(result){
                                  alert(result);
                                  console.log(result);
                                  if(result!='Proceso completado correctamente'){
                                    carga_sala();
                                  }
                                  //location.reload(true);
                                  //$('#enviar_fecha').click();
                               });
                            },

                        eventResize: function(event, start, end) {
                            var start = event.start;
                            var id = event.id;
                            var end = event.end;

                            var url = "{{ url('salas_todas/intervalo') }}/"+id+"/"+start+"/"+end;//salas_todas.intervalo
                            $.get(url, function(result){
                              alert(result);
                              console.log(result);
                              if(result!='Proceso completado correctamente'){
                                carga_sala();
                              }
                              //location.reload(true);
                              //$('#enviar_fecha').click();
                            });
                        },

                        eventRender:function(event, element){

                          var texto=event.description;
                            $(".tooltip").hide();
                            $(element).tooltip({
                              title: texto,
                              container: "body"
                          });

                        },

                        select: function(start, end, event, view, resource) {
                          @php
                            date_default_timezone_set('America/Guayaquil');
                          @endphp
                          var js_sala = resource.id;
                          var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');
                          var today = '{{date("Ymd H:m")}}';
                           console.log(view.type);
                          if(check < today)
                          {
                             alert("Fecha pasada, no es posible seleccionar");
                          }
                          else
                           {
                              var dia =  $.fullCalendar.formatDate(start,'ddd');
                              var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                              var dato = 0;

                              @foreach($salas as $x)
                                    @php
                                        $horario = DB::table('horario_sala')
                                        ->where('id_sala', '=', $x->id)->orderBy('ndia')
                                        ->orderBy('hora_ini')
                                        ->get();
                                    @endphp

                                 @foreach($horario as $value)
                                      if(dia == '<?php echo $value->dia; ?>'){
                                        if(hora >= '<?php echo $value->hora_ini; ?>' && hora < '<?php echo $value->hora_fin; ?>'){
                                        dato = 1;
                                      }
                                    }

                                  @endforeach
                              @endforeach

                             if(dato == 0)
                                {
                                    alert('¡AGENDA FUERA DEL HORARIO DE LA SALA DISPONIBLE PARA EL DOCTOR!');
                                    return 0;

                                }

                                valida_maximo(view.start._i,start,js_sala);


                                //location.href = "{{url('preagenda/pentax/agendar_pnombre') }}/"+start+"/"+js_sala;
                            }
                        },


                        dayClick: function(date, jsEvent, view, resource) {
                            console.log(
                              'dayClick',
                              date.format(),
                              resource ? resource.id : '(no resource)'
                            );
                        },

                        viewRender: function (view, element) {
                          console.log('viewrender',view.start._i);
                          obtener_fecha(view.start._i);
                        },
                    });

                });


                function fechacalendario() {
                    var dato = document.getElementById('fecha').value;
                    $('#enviar_fecha').click();
                }


            </script>
            <div id="calendar" style="margin: 0;"></div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function carga_sala(){
        $.ajax({
          type: 'post',
          url:'{{ route('salas_todas.buscar_ajax')}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#f_sala").serialize(),
          success: function(data){
            $('#div_sala').empty().html(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }
    function obtener_fecha(tiempo){
      console.log(tiempo);
      if(tiempo=='1604275200000' || tiempo=='1604361600000'){
        alert("¡Feriado del 2 y 3 de Noviembre, no Agendar!");
        $('#div_alerta').empty().html('<span class="label label-danger blink" >¡Feriado del 2 y 3 de Noviembre, no Agendar!</span>');
      }

      $.ajax({
        type: 'get',
        url:'{{ url('preagenda/pentax/maximo_dia')}}/'+tiempo,
        datatype: 'json',
        success: function(data){
          if(data>={{$maximo_procedimientos}}){
            $('#div_cantidad').empty().html('<span class="label label-danger blink" >Tiene '+data+' procedimientos para la fecha</span>');
          }else if(data>={{$alerta_procedimientos}}){
            $('#div_cantidad').empty().html('<span class="label label-warning blink" >Tiene '+data+' procedimientos para la fecha</span>');
          }else{
            $('#div_cantidad').empty().html('<span class="label label-success blink" >Tiene '+data+' procedimientos para la fecha</span>');
          }

        },
        error: function(data){
          //console.log(data);
        }
      });
    }
      function valida_maximo(tiempo,start,js_sala){
      $.ajax({
          type: 'get',
          url:'{{ url('preagenda/pentax/maximo_dia')}}/'+tiempo,
          datatype: 'json',
          success: function(data){
            if(data>={{$maximo_procedimientos}}){
              //alert("No puede ingresar mas de {{$maximo_procedimientos}} procedimientos");
              @if(!is_null($permiso))
                  var xconfirmar = confirm("Recuerde que ya excede los {{$maximo_procedimientos}} procedimientos, Desea Continuar");
                  if(xconfirmar){
                   // console.log("jfsdfo");
                   location.href = "{{url('preagenda/pentax/agendar_pnombre') }}/"+start+"/"+js_sala;
                  }
              @else
                  alert("No puede ingresar mas de {{$maximo_procedimientos}} procedimientos");
              @endif
            }else{
              //console.log("fodofo");
              //location.href = "{{url('preagenda/pentax/agendar_pnombre') }}/"+start+"/"+js_sala;
              location.href = "{{route('preagenda.nuevo') }}/"+start+"/0/"+js_sala;
            }

          },
          error: function(data){
            //console.log(data);
          }
        });

    }

</script>


@endsection
