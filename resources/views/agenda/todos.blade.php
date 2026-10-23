
@extends('agenda.base')

@section('action-content')
<style type="text/css">
    @foreach($agenda2 as $value)
        .a{{$value->id}}
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif

        }
    @endforeach
        .reuniones          
        {
            color: #023f84;
                                     
        }
    @foreach($agenda as $value)
        .a{{$value->id}} 
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif
        }
    @endforeach
    .fc-title{
    font-size: 1em !important;
    font-weight: bold;

    }
     .fc-event{
        width: 33% !important;
    }
    .fc-event:hover{
    width: 99% !important;
    z-index: 100 !important;
}
</style>


<link rel='stylesheet' href="{{ asset ('/js/calendario/fullcalendar.min.css') }}"/>
<section class="content" >
  <div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">@lang('agenda.agendaTodosDoctores-')</h3>
        <div class="form-group">
            <label for="inputtipodeprocedimiento" style="text-align: right;" class="col-sm-3 control-label">Tipos de Agenda</label>
            <div class="col-sm-4">
              <select id="proc_consul" name="proc_consul" class="form-control" >
                <option value="0" >@lang('agenda.procedimientos')/@lang('agenda.consultas')</option>
                <option value="1" >@lang('agenda.consulta')</option>                
                <option value="2" >@lang('agenda.procedimiento')</option>                               
                <option value="3" >@lang('agenda.reunion')</option>     
              </select>
            </div>
          </div>
    </div>
    <div id='calendar'>  
    </div>
  </div>
</section>    

   
   

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/jquery.min.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script>
    $(document).ready(function() {

        $('#calendar').fullCalendar('removeEvents', 1);
        $('#calendar').fullCalendar('removeEvents', 2);
        $('#calendar').fullCalendar('removeEvents', 3);
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            views:{
                agenda:{
                    slotDuration: "00:15:00",
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00"   
                }
            },

            events : [
                @foreach($agenda as $value)
                  {
                  id    : '1',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Consulta |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '1',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 
                    textColor: 'black',

                  @endif

                },
                @endforeach                
                @foreach($agenda2 as $value)
                {
                  id    : '2',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Procedimiento: {{ $value->prnombre}} |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}' ,  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '2',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}',
                    textColor: 'black', 

                  @endif

                },
                @endforeach

                /*@foreach($agenda3 as $value)
                {
                  className: 'reuniones',
                  id    : '3',
                  school: '3',
                  title : 'Reunion: {{ $value->observaciones}} | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84', 
                                     
                },
                @endforeach*/



            ],
            defaultView: 'agendaDay',
			      editable: false,
            selectable: true,
            header: {
				      left: 'prev,next today',
				      center: 'title',
				      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
			      },
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
        });

                            
    var pro_consul = document.getElementById('proc_consul').value;

    if(pro_consul==1){
        $('#calendar').fullCalendar('removeEvents', 2);
        $('#calendar').fullCalendar('removeEvents', 3);
    }
    if(pro_consul==2){
        $('#calendar').fullCalendar('removeEvents', 1);
        $('#calendar').fullCalendar('removeEvents', 3);
    }
    if(pro_consul==3){
        $('#calendar').fullCalendar('removeEvents', 1);
        $('#calendar').fullCalendar('removeEvents', 2);
    }
    if(pro_consul==0){
        $('#calendar').fullCalendar('removeEvents', 3);
    }
        


    });




    $('#proc_consul').on('change',function(){
        var pro_consul = document.getElementById('proc_consul').value;
          $('#calendar').fullCalendar('removeEvents', 1);
          $('#calendar').fullCalendar('removeEvents', 2);
          $('#calendar').fullCalendar('removeEvents', 3);
          if(pro_consul == 1){
              @foreach($agenda as $value)
                eventData = {
                  id    : '1',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Consulta |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '1',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}',
                    textColor: 'black', 

                  @endif

                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach
          }
          if(pro_consul == 2){
              @foreach($agenda2 as $value)
                eventData = {
                  id    : '2',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Procedimiento: {{ $value->prnombre}} |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}' ,  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '2',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}',
                    textColor: 'black', 

                  @endif

                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach
          }
          if(pro_consul == 3){
              @foreach($agenda3 as $value)
                eventData = {
                  className: 'reuniones',
                  id    : '3',
                  school: '3',
                  title : 'Reunion: {{ $value->observaciones}} | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach
          }
          if(pro_consul == 0){
              @foreach($agenda as $value)
                eventData = {
                  id    : '1',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Consulta |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '1',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 
                    textColor: 'black',

                  @endif

                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach
              @foreach($agenda2 as $value)
                eventData = {
                  id    : '2',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} {{ $value->papellido2}} {{ $value->pnombre1}}  {{ $value->pnombre2}} | Procedimiento: {{ $value->prnombre}} |@if($value->est_amb_hos == 0) Ambulatorio @else Hospitalizado @endif | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_seguro}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}}| @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}}' ,  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  school: '2',
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 
                    textColor: 'black',

                  @endif

                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach
              /*@foreach($agenda3 as $value)
                eventData = {
                  className: 'reuniones',
                  id    : '3',
                  school: '3',
                  title : 'Reunion: {{ $value->observaciones}} | Dr. {{ $value->d1nombre1}} {{ $value->d1apellido1}} | {{ $value->nombre_sala}} /  {{ $value->nombre_hospital}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}} ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                }
                $('#calendar').fullCalendar('renderEvent', eventData, true);
              @endforeach*/
          }


    });
</script>
@endsection
