
@extends('agenda.base')

@section('action-content')
<style type="text/css">

   .fc-title{
    font-size: 1em !important;
    font-weight: bold;
    
    } 
    .fc-title:before{
        
    }
    @foreach($agenda2 as $value)
        .a{{$value->id}}          
        {
            color: #023f84;

                                      
        } 
    @endforeach

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
 
    @foreach($agenda3 as $value)
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

    .fc-time-grid .fc-slats td{
        height: 1.2em !important;
    }
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
        /* Style the tab */
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }

            /* Style the buttons inside the tab */
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 17px;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
                background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
                background-color: #ccc;
            }

            /* Style the tab content */
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
            }
</style>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

<div class="box">
    <div class="box-header">
        <div class="col-md-6">
            <h4>Agenda de Pacientes del Dr(a). {{ Sis_medico\user::find($id)->nombre1 }} {{ Sis_medico\user::find($id)->apellido1 }}</h4>
            <div class="form-group col-md-12 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                <label class="col-md-1 control-label">Fecha</label>
                <div class="col-md-6">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="" name="fecha" class="form-control" id="fecha" onchange="fechacalendario();"  required>
                    </div>
                    @if ($errors->has('fecha'))
                    <span class="help-block">
                        <strong>{{ $errors->first('fecha') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5" >
            <div class="col-md-12" style="text-align: center;">
                <h4>Colores de referencia</h4>
            </div>

            <table style="margin-left: 30px">
                <tr>
                    <td style="background-color: #6666FF; color: white"> <label style="margin-bottom: 0px"  class="col-md-12 control-label">Consulta</label></td><td>&nbsp;</td>
                    <td style="background-color: #FF00BF; color: white"><label style="margin-bottom: 0px"  class="col-md-12 control-label">Procedimiento</label></td><td>&nbsp;</td>
                    <td style="background-color: #61c9ff; color: white"> <label style="margin-bottom: 0px" class="col-md-12 control-label">Todo Tipo</label></td><td>&nbsp;</td>
                    <td style="background-color: #00e600; color: white"> <label style="margin-bottom: 0px" class="col-md-12 control-label">IESS</label></td>
                </tr>
                
            </table>
        </div>
        <!--<div class="row">
            <div class="col-sm-4">           
                <a class="btn btn-primary" id="suspendidas" >Ver Suspendidas</a>
            </div>
        </div>-->  
    </div>    
        
	    <div id='calendar'>    
        </div>
</div>
</div></div>

 @php
    $doctor_todo = Sis_medico\Doctor_Tiempo::where('id_doctor',$id)->first();
    if(!is_null($doctor_todo)){
        $minutos = $doctor_todo->tiempo * 10;
    }
    $fpermiso = '0';
    $id_user_ses = Auth::user()->id;
    $permiso = Sis_medico\Agenda_Permiso::where('id_usuario',$id_user_ses)->where('estado','1')->first();
    if(!is_null($permiso)){
        $fpermiso = '1';
    }

@endphp

</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>

<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            //minDate: '{{date("Y/m/d")}}',
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

        
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li class="active">Doctor</li>');
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here

            lang: 'es',
            allDaySlot: false,


            
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
                    slotDuration: "00:15:00" ,
                    @if(!is_null($doctor_todo))
                        @if($id == $doctor_todo->id_doctor)
                            slotDuration: "00:10:00" ,
                        @endif
                    @endif

                    slotLabelFormat: 'HH:mm',
                    scrollTime: "07:00:00"

                }

            },
            @php $fecha_valida = date("Y-m-d H:i");
            @endphp
            events : [

                @foreach($horario as $key=>$value)
                    {
                    
                        start: '{{$value->hora_ini}}', 
                        end: '{{$value->hora_fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif @if($value->tipo == 3) #00e600 @endif',
                        rendering: 'background',
                        @if($value->ndia != 7)
                        dow: [ {{$value->ndia}}],
                        @endif
                        @if($value->ndia == 7)
                            dow: [0],
                        @endif
                    },
                @endforeach
                @foreach($extra as $key=>$value)
                    {
                    
                        start: '{{$value->inicio}}', 
                        end: '{{$value->fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                        rendering: 'background',
                    },
                @endforeach

                @foreach($agenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();  
                       $varhospital = Sis_medico\Sala::find($value->id_sala)->hospital->id;
                  @endphp  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                   
                  title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif @if($value->paciente_dr=="1") /PART @endif | PROC @if($value->vip=="1") /VIP @endif: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($versuspendidas==0) @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) @if($value->estado==1) Estado:  Completar Datos @else Estado:  Por Reagendar @endif  @elseif($value->estado_cita == 4) Estado:  ASISTIÓ @endif | @endif Agendado: {{$value->uapellido1}} | Modificado: {{$value->umapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) |Ciudad Proc: {{$value->ciudad}} @endif',	
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  @if($varhospital==2)
                  editable: false,// 10/10/2018 BLOQUEAR AGENDA
                  @else
                  editable: true,
                  @endif
                   
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $id])}}',
                  
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
                  @if($value->vip == 1)
                    color: '#ff3300',
                  @endif

                },
                @endforeach
                @foreach($agenda3 as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif @if($value->paciente_dr=="1") /PART @endif | CONSULTA @if($value->vip=="1") /VIP @endif | @if($versuspendidas==0) @if($value->estado_cita == 0) Estado:  Por Confirmar @endif @if($value->estado_cita == 1) Estado:  Confirmada @endif @if($value->estado_cita == 2) @if($value->estado==1) Estado:  Completar Datos @else Estado:  Por Reagendar @endif @endif @if($value->estado_cita == 4) Estado:  ASISTIÓ @endif| @endif @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado: {{$value->uapellido1}} | Modificado: {{$value->umapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) | Ciudad Proc: {{$value->ciudad}} @endif',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  @if($fpermiso)
                    editable: true,
                  @else 
                    @if($value->espid=='8')
                        editable: true,
                    @else
                        editable: false,
                    @endif
                    
                  @endif 
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $id])}}',
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
                        textColor: 'black', 
                    @endif  
                  @endif
                  @if($value->vip == 1)
                    color: '#ff3300',
                  @endif

                },
                @endforeach
                
                @foreach($agenda2 as $value)
                {

                  
                  @php $sala=Sis_medico\Sala::find($value->id_sala) @endphp  
                  
                  id    : '{{$value->id}} idreunion',
                  className: 'classreunion',
                  title : '{{$value->procedencia}} - {{ str_replace(array("\r\n", "\r", "'"), " ",$value->observaciones)}} | {{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}} | Agendado: {{substr($value->unombre1, 0, 1)}}{{$value->uapellido1}} | Modificado: {{substr($value->umnombre1, 0, 1)}}{{$value->umapellido1}} ',  
                  start : '{{ $value->fechaini }}', 
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                  url: '{{route('reunion.edit2', ['id' => $value->id]) }}',
                  @if($value->fechaini >= $fecha_valida)
                  editable: false,
                  @endif

                                     
                },


                @endforeach
            ],
            defaultView: 'agendaDay',
            duration: '00:15:00',
            selectHelper: true,
            selectable: true,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            startEditable: false,

            
            select: function(start, end, allDay) {
                
                @php 
                    date_default_timezone_set('America/Guayaquil');
                @endphp
                /*window.location.replace("{{route

                    ('agenda.nuevo', ['id' => $id]) }}/"+start+"/0");*/
                var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');
                var today = '{{date("Ymd H:m")}}';
                if(check <= today)
                {
                   alert("Fecha pasada, no es posible seleccionar");
                }
                else
                {   
                    var dia =  $.fullCalendar.formatDate(start,'ddd');
                    var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                    var dato = 0;
                    @foreach($horario as $value)
                        if(dia == '<?php echo $value->dia;?>'){
                            if(hora >= '<?php echo $value->hora_ini;?>' && hora < '<?php echo $value->hora_fin;?>'){
                                location.href = "{{route('agenda.nuevo', ['id' => $id]) }}/"+start+"/0";
                                dato = 1;
                            }
                        }

                    @endforeach

                    var inicial2=  $.fullCalendar.formatDate(start,'YYYY-MM-DD HH:mm:ss');

                    @foreach($extra as $key=>$value)
                        if(inicial2 >= '<?php echo $value->inicio;?>' && inicial2 < '<?php echo $value->fin;?>'){
                                location.href = "{{route('agenda.nuevo', ['id' => $id]) }}/"+start+"/0";
                                dato = 1;
                            }
                    @endforeach
                    if(dato == 0)
                    {
                        alert('¡AGENDA FUERA DEL HORARIO DISPONIBLE PARA EL DOCTOR!');
                        /* 9/10/2018 SE VUELVE A BLOQUEAR LA AGENDA FUERA DEL HORARIO DEL DOCTOR
                        var mensaje = confirm("¿Desea Agendar la Consulta?");
                        if(mensaje){
                            location.href = "{{route('agenda.nuevo', ['id' => $id]) }}/"+start+"/0";
                        }*/
                    }
                }

            },
            eventDrop: function(event, delta, start, end){ // event drag and drop
                   var start = event.start;
                   var id = event.id;
                   var end = event.end;
                   var url = "{{ route('agenda.actualizarhorario') }}/"+id+"/"+start+"/"+end;
                   $.get(url, function(result){
                      alert(result);
                      if(result=='Proceso completado correctamente'){

                      }else{
                         fechacalendario(); // 10/10/2018 ARREGLO DESPLAZAR  
                      }
                      //alert("ok");
                      //console.log(result);
                      //location.reload(true);
                      
                   });
                },
            selectable: true,
            header: {
				      left: 'prev,next today',
				      center: 'title',
				      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
			      },
                  
            /*eventRender: function(event, element) {
                var elemento = $('#calendar').fullCalendar( 'getView' );
                if(event.className == "classreunion"){
                    if( elemento.name == "listMonth"){
                        element.find("a").attr("data-toggle","modal");
                        element.find("a").attr("data-target","#favoritesModal");
                    }
                    if( elemento.name == "agendaDay"){
                        element.attr("data-toggle","modal");
                        element.attr("data-target","#favoritesModal");
                    }
                    if( elemento.name == "listDay"){
                        element.find("a").attr("data-toggle","modal");
                        element.find("a").attr("data-target","#favoritesModal");
                    }
                    if( elemento.name == "agendaWeek"){
                        element.attr("data-toggle","modal");
                        element.attr("data-target","#favoritesModal");
                    }
                    if( elemento.name == "month"){
                        element.attr("data-toggle","modal");
                        element.attr("data-target","#favoritesModal");
                    }
                }
                
            },*/     
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
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


function veractivas(e, suspendidas)
{
    alert(suspendidas);
    var boton1 =document.getElementsByClassName("suspendidas");
    if(suspendidas == 0){
        $(boton1).text('Ver Activas');
        var suspendidas=1;
        alert(suspendidas);   
    }
    if(suspendidas == 1){
        $(boton1).text('Ver Suspendidas');
        var suspendidas=0; 
        alert(suspendidas);  
    }
    
}
 function fechacalendario() {
     var fecha = document.getElementById('fecha').value;    
     var unix =  Math.round(new Date(fecha).getTime()/1000);
     
     if(fecha=='' ||fecha==' '){
     }else{
        
        location.href ="{{ route('agenda.agenda', ['id' => $id])}}/"+unix;
     }
     
 }

</script>
@include('sweet::alert')
@endsection
