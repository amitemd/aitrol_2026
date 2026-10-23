@extends('laboratorio.agenda.base')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">
.fc-title{
    font-size: 1.1em !important;
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

   
</style>
<style type="text/css">
  .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }
       
        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222; 
        }

        .table-hover>tbody>tr:hover{
          background-color: #ccffff !important;
        }
        
</style>




@section('action-content')

<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />


<!-- Ventana modal editar -->
<div class="modal fade" id="magendar_dr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade" id="magendar_reunion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<link rel='stylesheet' href="{{ asset ('/js/calendario/fullcalendar.min.css') }}"/>
<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />



<section class="content" >

      
      
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <div class="row col-md-12"><h4><b>Agenda Laboratorio</b></h4></div>

  
            <div class="row col-md-12">
              
              <div class="col-md-1">&nbsp;</div>
               
            </div>  
             
        </div>
        <div class="box-body">
          <div class="col-md-12">
          <form method="POST" action="{{ route('agendalabs.agenda') }}" >
          {{ csrf_field() }}
          
          <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="col-md-3 control-label">Fecha</label>
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
          

          
       

          <div class="form-group col-md-1 col-xs-2" >
            <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
          </div>
          
            
             
        </form>
        </div>
          
        
          
        <div id="calendar" ></div>   
        </div>      
	     
  </div>

  

  <script type="text/javascript">





</script>
</section>

  
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ('/plugins/fullcalendar/jquery.min.js') }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>



<script type="text/javascript">
  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hoy}}',

            });
        
        
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

        $(".clickable-row").click(function() {
            window.location = $(this).data("href");
        });
    });

  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  $('#magendar_dr').on('hidden.bs.modal', function(){
                location.reload();
                //alert("hola");
                $(this).removeData('bs.modal');
            });

  $('#magendar_reunion').on('hidden.bs.modal', function(){
                location.reload();
                //alert("hola");
                $(this).removeData('bs.modal');
            });            




    $(document).ready(function() { 

        $(".breadcrumb").append('<li class="active">Agenda</li>');
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            allDaySlot: false,
            height: 1280,
            defaultDate: '{{$fecha_hoy}}',
            views:{
                agenda:{
                    slotDuration: "00:15:00" ,
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
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}}  @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif ({{ $value->nombre_seguro}}), TOMA DE MUESTRA | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}} @if(!is_null($value->observaciones)) | Observaciones:{{$value->observaciones}} @endif',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  //url: '{{ route("agendalabs.laboratorio", ['id' => $value->id_paciente])}}',
                  //url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '4444444444'])}}',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

            ],
            defaultView: 'listDay',
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
        })
    });

var vartiempo = setInterval(function(){ location.reload(); }, 300000);

</script>

@endsection