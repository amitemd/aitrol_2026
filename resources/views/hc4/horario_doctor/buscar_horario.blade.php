<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style>
  .hc4-schedule-view {
    overflow: hidden;
    border: 1px solid #cfe2ec;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(18, 78, 111, .12);
  }

  .hc4-schedule-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 16px 20px;
    color: #fff;
    background: linear-gradient(118deg, #086c98 0%, #118caf 58%, #18aaa7 100%);
  }

  .hc4-schedule-doctor {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 12px;
  }

  .hc4-schedule-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    width: 44px;
    height: 44px;
    border: 1px solid rgba(255,255,255,.32);
    border-radius: 12px;
    background: rgba(255,255,255,.14);
    font-size: 21px;
  }

  .hc4-schedule-heading h4 {
    margin: 0 0 3px;
    font-size: 17px;
    font-weight: 700;
  }

  .hc4-schedule-heading p {
    margin: 0;
    color: rgba(255,255,255,.82);
    font-size: 12px;
  }

  .hc4-schedule-legend {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 7px 14px;
    font-size: 11px;
  }

  .hc4-schedule-legend span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
  }

  .hc4-schedule-legend i {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(255,255,255,.14);
  }

  .hc4-schedule-calendar {
    padding: 18px 20px 20px;
    background: linear-gradient(180deg, #f4fbfc 0%, #ffffff 34%);
  }

  .hc4-schedule-calendar .fc-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 15px;
  }

  .hc4-schedule-calendar .fc-toolbar .fc-left,
  .hc4-schedule-calendar .fc-toolbar .fc-center,
  .hc4-schedule-calendar .fc-toolbar .fc-right {
    float: none;
  }

  .hc4-schedule-calendar .fc-toolbar h2 {
    color: #164f69;
    font-size: 19px;
    font-weight: 700;
    text-transform: capitalize;
  }

  .hc4-schedule-calendar .fc-button {
    height: 34px;
    padding: 0 12px;
    border: 1px solid #c7dde6;
    border-radius: 8px;
    color: #087ca7;
    background: #fff;
    box-shadow: none;
    text-shadow: none;
  }

  .hc4-schedule-calendar .fc-button:hover,
  .hc4-schedule-calendar .fc-button:focus,
  .hc4-schedule-calendar .fc-state-active {
    border-color: #118eae;
    color: #fff;
    background: #118eae;
  }

  .hc4-schedule-calendar .fc-widget-header {
    padding: 9px 4px;
    border-color: #d7e8ed;
    color: #315b73;
    background: #eaf6f8;
  }

  .hc4-schedule-calendar .fc-widget-content {
    border-color: #dfecef;
  }

  .hc4-schedule-calendar .fc-axis {
    color: #607f90;
    background: #f8fbfc;
  }

  .hc4-schedule-calendar .fc-today {
    background: #edfafa !important;
  }

  .hc4-schedule-calendar .fc-event {
    padding: 3px 5px;
    border: 0;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(14, 103, 132, .18);
    font-size: 11px;
  }

  .hc4-schedule-calendar .hc4-patient-event {
    z-index: 5;
    border-left: 4px solid rgba(255,255,255,.72);
    font-weight: 700;
  }

  @media (max-width: 767px) {
    .hc4-schedule-heading,
    .hc4-schedule-calendar .fc-toolbar {
      align-items: flex-start;
      flex-direction: column;
    }

    .hc4-schedule-legend { justify-content: flex-start; }
    .hc4-schedule-calendar { padding: 14px 10px; }
    .hc4-schedule-calendar .fc-toolbar > * { width: 100%; text-align: center; }
  }
</style>

<div id="area_index" class="container-fluid hc4-schedule-view" style="padding-left: 0px; padding-right: 0px;">
  <div class="hc4-schedule-heading">
    <div class="hc4-schedule-doctor">
      <span class="hc4-schedule-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
      <div>
        <h4>Horario laboral</h4>
        <p>Dr(a). {{$nombre_doctor->nombre1}} {{$nombre_doctor->nombre2}} {{$nombre_doctor->apellido1}} {{$nombre_doctor->apellido2}}</p>
      </div>
    </div>
    <div class="hc4-schedule-legend" aria-label="Tipos de horario">
      <span><i style="background:#087ca7"></i>Consultas</span>
      <span><i style="background:#17a090"></i>Procedimientos</span>
      <span><i style="background:#0b637f"></i>Todos</span>
    </div>
  </div>
  <div class="hc4-schedule-calendar">
    <div id='calendar'></div>
  </div>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>

<script type="text/javascript">

	$(document).ready(function() 
    {
    	// page is now ready, initialize the calendar...
    	 
        $fecha_datos= null;
        $strFecha = null;
        $('#calendar').fullCalendar({
    	 	    lang: 'es',
            locate: 'es',
            views:{
                agenda:{
                    slotDuration: "00:15:00",
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00"
                }
            },
            eventSources: [{
              events: [
              @php
                $fecha_datos = date('Y-m-d');
                $dia = date('N');
                $strFecha = strtotime($fecha_datos);
                if($dia == 1){
                  $l = date('Y-m-d');
                  $m = date('Y-m-d',strtotime('next Tuesday',$strFecha));
                  $mi = date('Y-m-d',strtotime('next Wednesday',$strFecha));
                  $j = date('Y-m-d',strtotime('next Thursday',$strFecha));
                  $v = date('Y-m-d',strtotime('next Friday',$strFecha));
                  $s = date('Y-m-d',strtotime('next Saturday',$strFecha));
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 2){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d');
                  $mi = date('Y-m-d',strtotime('next Wednesday',$strFecha));
                  $j = date('Y-m-d',strtotime('next Thursday',$strFecha));
                  $v = date('Y-m-d',strtotime('next Friday',$strFecha));
                  $s = date('Y-m-d',strtotime('next Saturday',$strFecha));
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 3){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d',strtotime('last Tuesday',$strFecha));
                  $mi = date('Y-m-d');
                  $j = date('Y-m-d',strtotime('next Thursday',$strFecha));
                  $v = date('Y-m-d',strtotime('next Friday',$strFecha));
                  $s = date('Y-m-d',strtotime('next Saturday',$strFecha));
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 4){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d',strtotime('last Tuesday',$strFecha));
                  $mi = date('Y-m-d',strtotime('last Wednesday',$strFecha));
                  $j = date('Y-m-d');
                  $v = date('Y-m-d',strtotime('next Friday',$strFecha));
                  $s = date('Y-m-d',strtotime('next Saturday',$strFecha));
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 5){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d',strtotime('last Tuesday',$strFecha));
                  $mi = date('Y-m-d',strtotime('last Wednesday',$strFecha));
                  $j = date('Y-m-d',strtotime('last Thursday',$strFecha));
                  $v = date('Y-m-d');
                  $s = date('Y-m-d',strtotime('next Saturday',$strFecha));
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 6){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d',strtotime('last Tuesday',$strFecha));
                  $mi = date('Y-m-d',strtotime('last Wednesday',$strFecha));
                  $j = date('Y-m-d',strtotime('last Thursday',$strFecha));
                  $v = date('Y-m-d',strtotime('last Friday',$strFecha));
                  $s = date('Y-m-d');
                  $d = date('Y-m-d',strtotime('next Sunday',$strFecha));
                }
                if($dia == 7){
                  $l = date('Y-m-d',strtotime('last Monday',$strFecha));
                  $m = date('Y-m-d',strtotime('last Tuesday',$strFecha));
                  $mi = date('Y-m-d',strtotime('last Wednesday',$strFecha));
                  $j = date('Y-m-d',strtotime('last Thursday',$strFecha));
                  $v = date('Y-m-d',strtotime('last Friday',$strFecha));
                  $s = date('Y-m-d',strtotime('last Saturday',$strFecha));
                  $d = date('Y-m-d');
                }
              @endphp
              @foreach($horarios as  $value)
              { 
                title:"<?php if($value->ndia == 0){echo "Horario Domingo";}if($value->ndia == 1){echo "Horario Lunes";}if($value->ndia == 2){echo "Horario Martes";}if($value->ndia == 3){echo "Horario Miercoles";}if($value->ndia == 4){echo "Horario Jueves";}if($value->ndia == 5){echo "Horario Viernes";}if($value->ndia == 6){echo "Horario Sabado";}?> \n @if($value->tipo == 0) Tipo de  Horario: Todos @endif @if($value->tipo == 1) Tipo de  Horario: Consultas @endif @if($value->tipo == 2) Tipo de  Horario: Procedimiento @endif", 
                start: '<?php if($value->ndia == 7){echo $d;} if($value->ndia == 1){echo $l;} if($value->ndia == 2){echo $m;}if($value->ndia == 3){echo $mi;} if($value->ndia == 4){echo $j;}if($value->ndia == 5){echo $v;}if($value->ndia == 6){echo $s;}?> {{$value->hora_ini}}', // a start time (10am in this example) 
                end: '<?php if($value->ndia == 7){echo $d;} if($value->ndia == 1){echo $l;} if($value->ndia == 2){echo $m;}if($value->ndia == 3){echo $mi;} if($value->ndia == 4){echo $j;}if($value->ndia == 5){echo $v;}if($value->ndia == 6){echo $s;}?> {{$value->hora_fin}}', // an end time (2pm in this example) 
                 // Repeat monday and thursday 
                editable: false,
                id: '{{$value->id}}',
                color: '@if($value->tipo == 0) #0b637f @endif @if($value->tipo == 1) #087ca7 @endif @if($value->tipo == 2) #17a090 @endif',
                overlap:false,
                extra: '0',
              },
              @endforeach
              @foreach($extra as  $value)
              { 
                title:"Horario Extra Añadido \n @if($value->tipo == 0) Tipo de  Horario: Todos @endif @if($value->tipo == 1) Tipo de  Horario: Consultas @endif @if($value->tipo == 2) Tipo de  Horario: Procedimiento @endif", 
                start: '{{$value->inicio}}', // a start time (10am in this example) 
                end: '{{$value->fin}}',
                textColor: 'black', 
                color: '@if($value->tipo == 0) #0b637f @endif @if($value->tipo == 1) #087ca7 @endif @if($value->tipo == 2) #17a090 @endif',
                extra: '1',  // an end time (2pm in this example) 
                 // Repeat monday and thursday 
                editable: true,
                id: '{{$value->id}}',
                overlap:false,
              },
              @endforeach 
              ]
            }, {
              url: "{{route('obtener.horario_doctor_eventos')}}",
              type: 'GET',
              cache: false,
              error: function() {
                if (window.console) console.error('No se pudieron cargar los pacientes agendados del médico.');
              }
            }],
            defaultView: 'agendaWeek',
            //editable: false,
            selectable: false,
            selectHelper:true,
            selectable: false,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            header: {
              left:   'prev,next today',
              center: 'title',
              right: 'agendaWeek,agendaDay',
            },
             //startEditable: true,

           });
        }); 
</script>
