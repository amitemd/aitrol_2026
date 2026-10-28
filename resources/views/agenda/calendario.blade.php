
@extends('agenda.base')

@section('action-content')
<style type="text/css">

   .fc-title{
    font-size: 1em !important;
    font-weight: bold;

    }
    .fc-title:before{

    }


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

<link rel="stylesheet" href="{{ asset('/js/calendario/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

<div class="box">
    <div class="box-header">
        <div class="col-md-6">
            <h4>@lang('agenda.agendaPacientesDr') {{ Sis_medico\user::find($id)->nombre1 }} {{ Sis_medico\user::find($id)->apellido1 }}</h4>
            <div class="form-group col-md-12 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                <label class="col-md-1 control-label">@lang('agenda.fecha')</label>
                <div class="col-md-6">
                    @php 
                        if($fecha == '0'){
                            $fecha_in = date("Y/m/d");
                        }else{
                            date_default_timezone_set('Europe/London');
                            $fecha_d  = substr($fecha, 0, 10);
                            $fecha_in = date('Y/m/d', $fecha_d);
                        }
                    @endphp
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="{{$fecha_in}}" name="fecha" class="form-control" id="fecha" onchange="fechacalendario();"  required>
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
                <h4>@lang('agenda.coloresreferencia')</h4>
            </div>

            <table style="margin-left: 30px">
                <tr>
                    <td style="background-color: #6666FF; color: white"> <label style="margin-bottom: 0px"  class="col-md-12 control-label">@lang('agenda.consulta')</label></td><td>&nbsp;</td>
                    <td style="background-color: #FF00BF; color: white"><label style="margin-bottom: 0px"  class="col-md-12 control-label">@lang('agenda.procedimiento')</label></td><td>&nbsp;</td>
                    <td style="background-color: #61c9ff; color: white"> <label style="margin-bottom: 0px" class="col-md-12 control-label">@lang('agenda.todoTipo')</label></td><td>&nbsp;</td>
                    <td style="background-color: #00e600; color: white"> <label style="margin-bottom: 0px" class="col-md-12 control-label">IEES</label></td>
                </tr>

            </table>

            <div>&nbsp;</div>
            <div class="col-md-12">
                <button onclick="nueva_consulta()" class="btn btn-primary">@lang('agenda.agendaconsulta')</button>
            </div>

        </div>
        <!--<div class="row">
            <div class="col-sm-4">
                <a class="btn btn-primary" id="suspendidas" >@lang('agenda.verSuspendidas')</a>
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
<input type="hidden" name="semana" id="semana" value="9">

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset('/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('/plugins/fullcalendar/es.js') }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () { 
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            @if($fecha == '0')
                defaultDate: '{{date("Y/m/d")}}'
            @else
                <?php
                        date_default_timezone_set('Europe/London');
                        $fecha  = substr($fecha, 0, 10);
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
    function nueva_consulta(){
        var tiempo = Date.now() - 5000 * 60 * 60;
        window.location.href = "{{route('agenda.nuevo', ['id'=> $id])}}/"+tiempo+"/0";
    }



    $(document).ready(function()
    {
        //console.log({{$id}});
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> @lang('agenda.agenda')</a></li>');
        $(".breadcrumb").append('<li class="active">Doctor</li>');
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            locale: 'es',
            allDaySlot: false,
            @if($fecha != '0')
                <?php
                    date_default_timezone_set('Europe/London');
                    $fecha  = substr($fecha, 0, 10);
                    $fecha2 = date('Y-m-d', $fecha);
                ?>
                defaultDate: '{{$fecha2}}',
            @endif
            views:{
                agenda:{
                    slotDuration: "00:15:00" ,
                    @if(!is_null($doctor_todo))
                        @if($id == $doctor_todo->id_doctor)
                            slotDuration: "00:05:00" ,
                        @endif
                    @endif
                    @if($id == '4444444444' || $id == '444444444A' || $id == '444444444B')
                        slotDuration: "00:10:00" ,
                    @endif
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "07:00:00"
                }
            },
            @php 
                $fecha_valida = date("Y-m-d H:i");
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
                       $hc = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                       $hc_seguro_nombre = '';
                       if(!is_null($hc)){
                            if(!is_null($hc->seguro)){
                                $hc_seguro_nombre = $hc->seguro->nombre;
                            }

                       }
                       if($hc_seguro_nombre==''){
                            $hc_seguro_nombre = $value->seguro->nombre;
                       }
                  @endphp
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->paciente->apellido1}} @if($value->paciente->apellido2 != "(N/A)"){{ $value->paciente->apellido2}}@endif {{ $value->paciente->nombre1}} @if($value->paciente->nombre2 != "(N/A)"){{ $value->paciente->nombre2}}@endif @if($value->paciente_dr=="1") /PART @endif | PROC @if($value->vip=="1") /VIP @endif: {{$value->procedimiento->nombre}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($versuspendidas==0) @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) @if($value->estado==1) Estado:  Completar Datos @else Estado:  Por Reagendar @endif  @elseif($value->estado_cita == 4) Estado:  ASISTIÓ @endif | @endif Agendado: {{substr($value->user_crea->nombre1, 0, 1)}}. {{$value->user_crea->apellido1}} | Modificado: {{substr($value->user_mod->nombre1, 0, 1)}}. {{$value->user_mod->apellido1}} | Seguro: @if($value->estado_cita!=4) {{ $value->seguro->nombre}} @else {{ $hc_seguro_nombre }} @endif| Cortesia: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif @if($value->paciente->ciudad != null) |Ciudad Proc: {{$value->paciente->ciudad}} @endif|Teleconsulta: @if($value->tc=='1' )SI @else NO @endif',
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
                        color: '{{ $value->seguro->color}}',
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
                    @php
                        $tedad = Carbon\Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;                

                        $atendido = 0;$receta_ok = 0;$hc_seguro_nombre = '';$hc_color = '';$hc_procedimientos = array();
                        $hc = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                        if(!is_null($hc)){
                            $hc_procedimientos = Sis_medico\hc_procedimientos::where('id_hc',$hc->hcid)->first();
                        }
                        if(!is_null($hc)){
                            $cie10 = Sis_medico\Hc_Cie10::where('hcid',$hc->hcid)->first();
                            if(!is_null($cie10)){
                                $atendido = 1;
                            }
                            $receta = Sis_medico\hc_receta::where('id_hc',$hc->hcid)->first();
                            if(!is_null($receta)){
                                if($receta->rp!=null && $receta->prescripcion!=null){
                                    $receta_ok = 1;
                                }
                            }
                            if(!is_null($hc->seguro)){
                                $hc_seguro_nombre = $hc->seguro->nombre;
                                $hc_color = $hc->seguro->color;
                            }
                        }
                        if($hc_seguro_nombre==''){
                            $hc_seguro_nombre = $value->seguro->nombre;
                       }
                    @endphp
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  @if($id == '4444444444' || $id == '444444444A' || $id == '444444444B')
                  title : '{{ $value->paciente->apellido1}} @if($value->paciente->apellido2 != "(N/A)"){{ $value->paciente->apellido2}}@endif {{ $value->paciente->nombre1}} @if($value->paciente->nombre2 != "(N/A)"){{ $value->paciente->nombre2}}@endif | TOMA DE MUESTRA @if($value->estado_cita == 4) REALIZADO @endif | Agenda: {{substr($value->user_crea->nombre1, 0, 1)}}. {{$value->user_crea->apellido1}} | Modif: {{substr($value->user_mod->nombre1, 0, 1)}}. {{$value->user_mod->apellido1}} | Seguro: {{ $hc_seguro_nombre}} | Edad: @if(!is_null($tedad)) {{$tedad}} @endif',
                  @else
                  title : '{{ $value->paciente->apellido1}} @if($value->paciente->apellido2 != "(N/A)"){{ $value->paciente->apellido2}}@endif {{ $value->paciente->nombre1}} @if($value->paciente->nombre2 != "(N/A)"){{ $value->paciente->nombre2}}@endif @if($value->paciente_dr=="1") /PART @endif | CONSULTA @if($value->vip=="1") /VIP @endif | @if($versuspendidas==0) @if($value->estado_cita == 0) Por Confirmar @endif @if($value->estado_cita == 1)  Confirmada @endif @if($value->estado_cita == 2) @if($value->estado==1)  Completar Datos @else  Por Reagendar @endif @endif @if($value->estado_cita == 4)  ASISTIÓ @endif| @endif @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agenda: {{substr($value->user_crea->nombre1, 0, 1)}}. {{$value->user_crea->apellido1}} | Modif: {{substr($value->user_mod->nombre1, 0, 1)}}. {{$value->user_mod->apellido1}} | Seguro: @if($value->estado_cita!='4'){{ $value->seguro->nombre}} @else {{$hc_seguro_nombre}}@endif | Cort.: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) | Ciu.Proc: {{$value->paciente->ciudad}} @endif @if(!empty($hc_procedimientos->hora_fin)) |**ATENDIDO** | @endif @if($receta_ok)|RECETA OK @endif |Teleconsulta: @if($value->tc=='1' )SI @else NO @endif',
                  @endif
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
                        @if($hc_color!='')
                            color: '{{ $hc_color}}',
                        @else
                            color: '{{ $value->seguro->color}}',
                        @endif

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
                    title : '{{$value->procedencia}} - {{ str_replace(array("\r\n", "\r"), " ", $value->observaciones)}} | {{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}} | Agendado: {{substr($value->user_crea->nombre1, 0, 1)}}{{$value->user_crea->apellido1}} | Modificado: {{substr($value->user_mod->nombre1, 0, 1)}}{{$value->user_mod->apellido1}} ',
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
            duration: '00:10:00',
            selectHelper: true,
            selectable: true,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            startEditable: false,

            select: function(start, end, allDay, view) {

                @php
                    date_default_timezone_set('America/Guayaquil');
                @endphp
                /*window.location.replace("{{route

                    ('agenda.nuevo', ['id' => $id]) }}/"+start+"/0");*/
                var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');
                var today = '{{date("Ymd H:m")}}';
                console.log(view.type);
                if(check <= today)
                {
                   alert(`{{trans('agenda.fechaPasadaImposibleSeleccionar')}}`); 
                }
                else
                {
                    var dia =  $.fullCalendar.formatDate(start,'ddd');
                    var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                    var dato = 0;
                    @foreach($horario as $value)
                        if(dia == '<?php echo $value->dia; ?>'){
                            if(hora >= '<?php echo $value->hora_ini; ?>' && hora < '<?php echo $value->hora_fin; ?>'){
                                location.href = "{{route('agenda.nuevo', ['id' => $id]) }}/"+start+"/0";
                                dato = 1;
                            }
                        }

                    @endforeach

                    var inicial2=  $.fullCalendar.formatDate(start,'YYYY-MM-DD HH:mm:ss');

                    @foreach($extra as $key=>$value)
                        if(inicial2 >= '<?php echo $value->inicio; ?>' && inicial2 < '<?php echo $value->fin; ?>'){
                                location.href = "{{route('agenda.nuevo', ['id' => $id]) }}/"+start+"/0";
                                dato = 1;
                            }
                    @endforeach
                    if(dato == 0)
                    {
                        if(view.type=='month'){
                            window.AitrolAlerts.confirm({
                                text: `{{ trans('agenda.soloAgendarReuniones') }}`,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    location.href = "{{ url('agenda/reunion/nuevo') }}/{{ $id }}/"+start+"/0";
                                }
                            });

                        }else{
                            alert(`{{trans('agenda.agendaFueraHorarioDisponibleDoctor')}}`);
                        }

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
                   });
                },
            selectable: true,
            header: {
                left: 'today',
                center: 'title',
                right: 'agendaWeek,agendaDay,listDay',
            },

        });

        $('#favoritesModal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
        });
        function modal1(){
            $('.classreunion').find("a").attr("data-toggle","modal");
            $('.classreunion').find("a").attr("data-target","#favoritesModal");
        };
        $('.fc-agendaWeek-button').click(function() {
            var semana = $('#semana').val();
            var fecha = $('#fecha').val();
            $.ajax({
                url: "{{ route('agenda.buscador_json')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'post',
                data: {
                    'tipo':     1,
                    'semana':   semana,
                    'mes':      0,
                    'fecha':    fecha,
                    'id_doctor':    '{{$id}}'
                },
                beforeSend: function() {
                    Swal.fire({
                        //title: "winsumos.procesando",
                        text: "Cargando...",
                        imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                        imageWidth: 200,
                        imageHeight: 200,
                        imageAlt: 'Custom image',
                    })
                },
                success: function(data) {

                        var calendar = $('#calendar');

                        calendar.fullCalendar('removeEvents');
                        var eventos = JSON.parse(data);
                        
                        $.each(eventos, function(index, evento) {
                            calendar.fullCalendar('renderEvent', evento, true);
                        }); 
                        @foreach($horario as $key=>$value)
                            evento3 = {
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
                            }
                            calendar.fullCalendar('renderEvent', evento3, true);
                        @endforeach
                        @foreach($extra as $key=>$value)
                            evento2 = {
                                start: '{{$value->inicio}}',
                                end: '{{$value->fin}}',
                                color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                                rendering: 'background',
                            }
                            calendar.fullCalendar('renderEvent', evento2, true);
                        @endforeach

                    $('#semana').val(1);                           
                    Swal.close();    
                    console.log("amonos");
                },
                error: function(data) {
                    console.log('error');
                    console.log(data);
                    Swal.close(); 
                }
            });
        });
            
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
            $(this).text(`{{trans('agenda.ocultarRespuesta')}}`);
            e.preventDefault();
        }, // Separamos las dos funciones con una coma

        // Segundo click
        function(e){
            $('#respuesta-ej5').slideUp();
            $(this).text(`{{trans('agenda.verRespuesta')}}`);
            e.preventDefault();
        }

    );




 });


function veractivas(e, suspendidas)
{
    alert(suspendidas);
    var boton1 =document.getElementsByClassName("suspendidas");
    if(suspendidas == 0){
        $(boton1).text(`{{trans('agenda.verActivas')}}`);
        var suspendidas=1;
        alert(suspendidas);
    }
    if(suspendidas == 1){
        $(boton1).text(`{{trans('agenda.verSuspendidas')}}`);
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

@endsection
