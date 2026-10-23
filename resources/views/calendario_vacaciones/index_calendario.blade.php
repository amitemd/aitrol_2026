@extends('calendario_vacaciones.base')
@section('action-content')
<style>
    #calendar {
    max-width: 100%;
    margin: 40px auto;
    padding: 0 10px;
  }

  .fc-title{
    font-size: 1.5em !important;
    font-weight: bold;

  }

  .fc-day-grid-event{
    padding: 13px 0;    
  }
</style>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />

<div id="calendar"></div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script>

function random_bg_color() {
		var x = Math.floor(Math.random() * 256);
		var y = Math.floor(Math.random() * 256);
		var z = Math.floor(Math.random() * 256);
		var bgColor = "rgb(" + x + "," + y + "," + z + ")";
		return bgColor;
	}

    $(document).ready(function() {

$('#calendar').fullCalendar({
  //locale: 'zh-cn',
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,basicWeek,basicDay'
  },
  defaultDate: '{{date("Y-m-d")}}',
  navLinks: true, // can click day/week names to navigate views
  editable: true,
  eventLimit: true, // allow "more" link when too many events
  events: [
    @foreach($listado_vacaciones as $value)
    {
      title: '{{$value->nombre_usuario->apellido1}} {{$value->nombre_usuario->apellido2}}  {{$value->nombre_usuario->nombre1}}  {{$value->nombre_usuario->nombre2}} Periodo: {{$value->inicio}} - {{$value->fin}}',
      start: '{{$value->inicio}}'+' 00:00:00',
      end:   '{{$value->fin}}'+' 23:59:59',
      color: random_bg_color(),
      //textColor: 'black',
    },

    @endforeach

    
  ]
});

});
</script>

@endsection