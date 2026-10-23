    
@extends('agenda.base')

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

        $('#calendar').fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultView: 'agendaDay',
            defaultDate: '{{$fecha}}',
            editable: false,
            allDaySlot: false,
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
                    @if($sala->nombre_sala!='RECUPERACION')
                        { id: '{{$sala->id}}', title: '{{$sala->nombre_sala}}' },
                    @endif
                @endforeach
            ],
            events: [
                @foreach($agendas as $value)
                { 
                    @if($value->proc_consul == 0)
                        id: '{{$value->id}}', 
                        resourceId: '{{$value->id_sala}}', 
                        start: '{{$value->fechaini}}', 
                        end: '{{$value->fechafin}}', 
                        @php $agendaprocedimientos=DB::table('agenda_procedimiento')->where('id_agenda',$value->id)->get(); @endphp
                        @if($value->estado == '-1')
                    
                            title: '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR**' ,
                        @endif
                        @if($value->estado == '1')
                            title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | Doctor: {{$value->dnombre1}} {{$value->dapellido1}} | PROCEDIMIENTOS: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) Estado:  Reagendada  @elseif($value->estado_cita == 4) Estado:  ADMISIONADO @endif |  Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',
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
                                color: 'red', 
                            @endif  
                        @endif
                        url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                    @elseif($value->proc_consul == 1)
                        id: '{{$value->id}}', 
                        resourceId: '{{$value->id_sala}}', 
                        start: '{{$value->fechaini}}', 
                        end: '{{$value->fechafin}}', 
                        @php $agendaprocedimientos=DB::table('agenda_procedimiento')->where('id_agenda',$value->id)->get(); @endphp
                        @if($value->estado == '-1')
                    
                            title: '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR**' ,
                        @endif
                        @if($value->estado == '1')
                            title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | Doctor: {{$value->dnombre1}} {{$value->dapellido1}} | PROCEDIMIENTOS: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{DB::table('procedimiento')->find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) Estado:  Reagendada  @elseif($value->estado_cita == 4) Estado:  ADMISIONADO @endif |  Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',
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
                                color: 'red', 
                            @endif  
                        @endif
                        url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                    @endif
                },
                @endforeach
                @foreach($consultas as $value)
                { 
                    @if($value->proc_consul == 0)
                        id: '{{$value->id}}', 
                        resourceId: '{{$value->id_sala}}', 
                        start: '{{$value->fechaini}}', 
                        end: '{{$value->fechafin}}', 
                        @if($value->estado == '-1')
                    
                            title: '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |CONSULTA | @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR**' ,
                        @endif
                        @if($value->estado == '1')
                            title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | Doctor: {{$value->dnombre1}} {{$value->dapellido1}} | CONSULTA| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) Estado:  Reagendada  @elseif($value->estado_cita == 4) Estado:  ADMISIONADO @endif |  Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',
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
                                color: 'red', 
                            @endif  
                        @endif
                        url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                    @endif
                },
                @endforeach

            ],
            

            select: function(start, end, jsEvent, view, resource) {
                console.log(
                  'select',
                  start.format(),
                  end.format(),
                  resource ? resource.id : '(no resource)'
                );
            },
            
            dayClick: function(date, jsEvent, view, resource) {
                console.log(
                  'dayClick',
                  date.format(),
                  resource ? resource.id : '(no resource)'
                );
            }
        });
          
    });


    function fechacalendario() {
        var dato = document.getElementById('fecha').value;
        $('#enviar_fecha').click();
    }

  
</script>

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

</style>

<section class="content" >
    
    <div class="box">
        <div class="box-header">
            <div class="form-group col-md-12" >
                <label class="col-md-1 control-label">Fecha</label>
                <div class="col-md-12">
                    <form method="POST" action="{{ route('historia_clinica.fullcontrol')}}" > 
                        {{ csrf_field() }}
                        <div class="col-md-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="" name="fecha" class="form-control input-sm" id="fecha" onchange="fechacalendario();" autocomplete="off">
                            </div>    
                        </div>
                        
                        <input type="submit" id="enviar_fecha" style="display: none;">
                        
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">

            <div id="calendar" ></div>
        </div>
    </div>
</section>


@endsection
