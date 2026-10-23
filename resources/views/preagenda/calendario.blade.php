
@extends('agenda.base')

@section('action-content')
<style type="text/css">
   .fc-title{
    font-size: 1em !important;
    font-weight: bold;
}
 .fc-event{
    width: 33% !important;
}
.fc-event:hover{
    width: 66% !important;
    z-index: 100 !important;
}
     

    @foreach($agenda as $value)
        .a{{$value->id}}
        {
            @if($value->paciente_dr == 0)
                @if($value->estado_cita == 0)
                    color: black;
                @else
                    color: {{ $value->color}};
                     
                @endif
            @endif
            @if($value->paciente_dr == 1) 
                @if($value->estado_cita == 0)
                    color: black;
                @else
                    color: red; 
                @endif 
            @endif
        }
    @endforeach

      
</style>
        <style>
            .glyphicon-refresh-animate {
                -animation: spin .7s infinite linear;
                -webkit-animation: spin2 .7s infinite linear;
            }

            @-webkit-keyframes spin2 {
                from { -webkit-transform: rotate(0deg);}
                to { -webkit-transform: rotate(360deg);}
            }

            @keyframes spin {
                from { transform: scale(1) rotate(0deg);}
                to { transform: scale(1) rotate(360deg);}
            }
        </style>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

    <div class="box">
        <div class="box-header">
            <div class="col-md-6">
                <h4>AGENDA DE PROCEDIMIENTOS </h4>
            </div>
            <div class="form-group col-md-12 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                <label class="col-md-1 control-label">Fecha</label>
                <div class="col-md-3">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="" name="fecha" class="form-control" id="fecha" onchange="fechacalendario();">
                    </div>
                    @if ($errors->has('fecha'))
                    <span class="help-block">
                        <strong>{{ $errors->first('fecha') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="col-md-4">
                    <a class="btn btn-primary col-md-8" href="{{ route('agenda.agenda4') }}">Agenda completa de doctores</a>
                </div>
            </div>
        </div>    
	    <div id='calendar'>    
        </div>
	</div>
</section>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            minDate: '{{date("Y/m/d")}}',
            @if($fecha == '0')
                defaultDate: '{{date("Y/m/d")}}'
            @else
            <?php 
            date_default_timezone_set('Europe/London');
            $fecha  = substr($fecha, 0,10);
            $fecha2 = date('Y/m/d', $fecha);
            ?>
                defaultDate: '{{$fecha2}}'
            @endif
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
        });
    });
</script>
<script>    
    

    $(document).ready(function() 
    {

        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here

            lang: 'es',
            locate: 'es',
            @if($fecha != '0')
            <?php 
            date_default_timezone_set('Europe/London');
            $fecha  = substr($fecha, 0,10);
            $fecha2 = date('Y-m-d', $fecha);
            ?>
                defaultDate: '{{$fecha2}}',
            @endif
            views:{
                agenda:{
                    @if($id == '1314490929')
                        slotDuration: "00:10:00" ,
                    @else
                        slotDuration: "00:10:00" ,
                    @endif  
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00"   
                }
            },
            events : [
                @foreach($agenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ASISTIÓ @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} ',	
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  url: '{{ route('preagenda.edit', ['id' => $value->id])}}',
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
                        color: 'red', 
                    @endif  
                  @endif

                },
                @endforeach
                
            ],
            defaultView: 'agendaDay',
			editable: false,
            duration: '00:15:00',
            selectable: true,
            selectHelper: true,

            
            select: function(start, end, allDay) {
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
                    location.href = "{{route('preagenda.nuevo') }}/"+start+"/0";
                }

            },
            selectable: true,
            header: {
				      left: 'prev,next today',
				      center: 'title',
				      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
			      },
                  
            
        });

        $('#favoritesModal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
        });
        function modal1(){
            $('.classreunion').find("a").attr("data-toggle","modal");
            $('.classreunion').find("a").attr("data-target","#favoritesModal");
        };
        
         
    });
</script>
<script type="text/javascript">

$('#inicio').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD HH:mm',
    lang : 'es',

});
$('#fin').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD  HH:mm',
    lang : 'es',

});
$('#fecha_nacimiento').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD',
    lang : 'es',
    time: false,

});

 $(document).ready(function() {

    

    $('#alternar-respuesta-ej5').toggle( 
  
        // Primer click
        function(e){ 
            $('#respuesta-ej5').slideDown();
            $(this).text('Ocultar respuesta');
            e.preventDefault();
        }, // Separamos las dos funciones con una coma
      
        // Segundo click
        function(e){ 
            $('#respuesta-ej5').slideUp();
            $(this).text('Ver respuesta');
            e.preventDefault();
        }
  
    );
     
    


 });

 function fechacalendario() {
     var fecha = document.getElementById('fecha').value;    
     var unix =  Math.round(new Date(fecha).getTime()/1000);
     location.href ="{{ route('preagenda.procedimiento')}}/"+unix;
 }




</script>
@include('sweet::alert')
@endsection
