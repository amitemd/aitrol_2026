@php

    $maximo_procedimientos = 22;
    $alerta_procedimientos = 15;
    $user_agenda = Auth::user()->id;
    $permiso = Sis_medico\Agenda_Permiso::where('id_usuario',$user_agenda)->where('proc_consul','1')->where('estado','2')->first();
@endphp
<script>

    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
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
                    @php $agendaprocedimientos=DB::table('agenda_procedimiento')->where('id_agenda',$value->id)->get(); @endphp
                    @if($value->estado == '-1')

                       title: '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  |{{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR**' ,
                        url: '{{ route('preagenda.edit', ['id' => $value->id])}}',
                    @endif
                    @if($value->estado == '1')
                         title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif | Dr(a). {{$value->dnombre1}} {{$value->dapellido1}} | {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif |  Agdo por: {{$value->uapellido1}} | {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',
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

            select: function(start, end, jsEvent, view, resource) {
              var js_sala = resource.id;
              var check = $.fullCalendar.formatDate(start,'YYYYMMDD');
              var today = '{{date('Ymd')}}';
              if(check < today)
              {
                 alert("Fecha pasada, no es posible seleccionar");
              }
              else
              {
                var dia =  $.fullCalendar.formatDate(start,'ddd');
                var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                var dato = 0;

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
              console.log(view.start._i);
              obtener_fecha(view.start._i);
            },
        });

    });



</script>




<div id="calendar" ></div>
