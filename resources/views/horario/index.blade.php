

@extends('horario.base')
@section('action-content')
<div class="modal fade" id="agregarHorario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="agregarHorario2">
    </div>
  </div>
</div>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
  <div class="container-fluid" >
    <div class="row">
      
      
      
        <div class="col-md-12">
          <div class="box box-default @if(session()->get('error')  ) @else collapsed-box @endif  " >
            <div class="box-header with-border">
              <h4>Agregar Dia Laborable por Fecha</h4>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool " data-widget="collapse"><i class="fa @if(count($errors) == '0') fa-plus @else fa-plus @endif "></i></button>
              </div>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" method="POST" action="{{ route('horario.unico') }}">
                <input id="id_doctor1" type="hidden" name="id_doctor1" value="{{$id}}" >
                {{ csrf_field() }}
                         
                        <div class="form-group col-md-6 {{ session()->get('error') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }} "  >
                            <label class="col-md-4 control-label">Inicio</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(session()->get('inicio')) {{ session()->get('inicio')}} @endif" name="inicio" class="form-control" id="inicio" required>
                                </div>
                                   @if (session()->get('error'))
                                    <span class="help-block">
                                      <strong>{{ session()->get('error') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('acceso'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('inicio') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ session()->get('error') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Fin</label>
                            <div class="col-md-7">

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(session()->get('inicio')) {{ session()->get('fin')}} @endif" name="fin" class="form-control" id="fin" required>
                                </div>
                                @if (session()->get('error'))
                                    <span class="help-block">
                                      <strong>{{ session()->get('error') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('tipo') ? ' has-error' : '' }}">
                            <label for="tipo" class="col-md-4 control-label">Tipo de Horario</label>
                            <div class="col-md-6">
                                <select id="tipo" name="tipo" class="form-control" onchange="edad();">
                                    <option value="0">Todos</option> 
                                    <option value="1" >Consulta</option>
                                    <option value="2" >Procedimiento</option>
                                </select>  
                                @if ($errors->has('tipo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--pais-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>  
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary ">
                <div class="box-body with-border ">
                  <div id='calendar'>    
                  </div>
                  {!! Form::open(['route' => ['horario.actualizar'], 'method'=>'POST' ])!!} 
                  {!! Form::close() !!}    
                </div>  
            </div>
        </div>
        
    </div>
  </div>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
  <script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
  <script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
  <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>

<script type="text/javascript">

  $(function () {
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            minDate : '@php echo date("Y/m/d H:m") @endphp',

            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',
            
        });
        
        $("#inicio").on("dp.change", function (e) {
            $('#fin').data("DateTimePicker").minDate(e.date);
        });
    });

    $(document).ready(function() 
    {

        $('#agregarHorario').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

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
                editable: true,
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
            eventResize: function(event, start, end) {  
               var start = event.start;
               var id = event.id;
               var end = event.end;
               var extra = event.extra;
               var url = "{{ route('horario.actualizar') }}/"+id+"/"+start+"/"+end+'/'+extra;
               $.get(url, function(result){
                  alert(result);
               });
            },
            select: function(start, end, jsEvent) {  // click on empty time slot
               var titulo = $.fullCalendar.formatDate(start, "dddd");
               $("#agregarHorario2").load('{{route("horario.agregar_modal")}}/'+start+'/'+end);
               $('#agregarHorario').modal('show');
               /*$.get(url, function(result){

                  $("#calendar").fullCalendar('renderEvent',
                   {
                       id: result,
                       title: "Horario "+titulo,
                       start: start,
                       end: end,
                   },
                   true);
                  alert('Se a creado el evento Exitosamente');
               });*/
            },
            eventDrop: function(event, delta, start, end){ // event drag and drop
               var start = event.start;
               var id = event.id;
               var end = event.end;
               var extra = event.extra;
               var url = "{{ route('horario.actualizar2') }}/"+id+"/"+start+"/"+end+"/"+extra;
               $.get(url, function(result){
                  alert(result);
               });
            },
            eventClick: function(event) {
              var mensaje = confirm("¿Desea eliminar el Horario?");
              //Detectamos si el usuario acepto el mensaje
              if (mensaje) {
                if(event.extra == "0"){
                  var start = event.start;
                  var id = event.id;
                  var end = event.end;
                  var url = "{{ route('horario.eliminar') }}/"+id;
                  $.get(url, function(result){
                     alert(result);
                  });
                  $('#calendar').fullCalendar('removeEvents', event.id);
                }
                if(event.extra == "1"){
                  var start = event.start;
                  var id = event.id;
                  var end = event.end;
                  var url = "{{ route('horario.eliminar2') }}/"+id;
                  $.get(url, function(result){

                     alert(result);
                  });
                  $('#calendar').fullCalendar('removeEvents', event.id);
                }            
              }
            },
            defaultView: 'agendaWeek',
            selectable: true,
            selectHelper: true,
            selectable: true,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            header: {
              left:   '',
              center: 'title',
              right: '',
            },
            startEditable: true,
        });
    });
$('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false
    });
     
</script>
@endsection        