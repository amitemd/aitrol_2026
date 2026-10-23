<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div id="area_index"  class="container-fluid" style="padding-left: 0px; padding-right: 0px;">
  <div class="modal-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;text-align: center">
    <h4 align="center">Horario Laborable del Dr(a). {{$nombre_doctor->nombre1}} {{$nombre_doctor->nombre2}}
    {{$nombre_doctor->apellido1}} {{$nombre_doctor->apellido2}}
    </h4>  
    
   
  </div>
  <div class="modal-body" style="border: 2px solid #004AC1;border-radius: 3px;"> 
    <div class="col-md-12">
        <div class="box-body with-border ">
            <div id='calendar'>    
            </div>
        </div>  
    </div>
  </div>
<div>

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
                color: '@if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
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
                color: '@if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                extra: '1',  // an end time (2pm in this example) 
                 // Repeat monday and thursday 
                editable: true,
                id: '{{$value->id}}',
                overlap:false,
              },
              @endforeach 
            ],
            defaultView: 'agendaWeek',
            //editable: false,
            selectable: false,
            selectHelper:true,
            selectable: false,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            header: {
              left:   '',
              center: 'title',
              right: '',
            },
             //startEditable: true,

           });
        }); 
</script>