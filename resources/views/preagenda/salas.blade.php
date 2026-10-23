
@extends('agenda.base')

@section('action-content')


@php
    $maximo_procedimientos = Sis_medico\Max_Procedimiento::find('1')->cantidad;


    $alerta_procedimientos = Sis_medico\Max_Procedimiento::find('2')->cantidad;

@endphp
<style type="text/css">
    @foreach($calendarios as $valores)
        @foreach($valores as $value)
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

                    color: '#56070C',
                    textColor: 'white',
                @endif
            @endif
        }
        @endforeach
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
        .table>tbody>tr>td{
            padding: 4px;
        }
        .table>thead>tr>th{
            padding: 4px;
        }
        .blink {
            animation: blinker 0.9s linear infinite;

            font-size: 15px;

            font-family: sans-serif;
        }
        @keyframes blinker {
            50% { opacity: 0; }
        }
    </style>
<div class="modal fade" id="ocupar_sala" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<!--link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' /-->
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

    <div class="box">
        <div class="box-header">
            @if($reuniones != '[]')
            <div  class="table-responsive col-md-12"><h4>REUNIONES</h4>
                <table class="table table-striped" style="font-size: 12px;">
                    <thead>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Tipo</th>
                        <th>Doctor</th>

                        <th>Agdo.por</th>
                        <th>Modf.por</th>

                    </thead>
                    <tbody >
                        @foreach($reuniones as $reunion)
                        <tr style="background-color: #ffccf2;">
                            <td>{{$reunion->fechaini}}</td>
                            <td>{{$reunion->fechafin}}</td>
                            <td>{{$reunion->procedencia}} {{$reunion->observaciones}}</td>
                            <td>{{$reunion->dnombre}} {{$reunion->dapellido}}</td>
                            <td>{{$reunion->ucnombre1}} {{$reunion->ucapellido1}}</td>
                            <td>{{$reunion->umnombre1}} {{$reunion->umapellido1}}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @if($pentax_pend != [])
            <div  class="table-responsive col-md-12"><h4>PENDIENTES DE EXAMEN</h4>
                <table class="table table-striped" style="font-size: 12px;">
                    <thead>
                      <tr role="row">
                        <th >Fecha/Hora</th>
                        <th >Cédula</th>
                        <th >Nombres</th>
                        <th >Seguro</th>
                        <th >Procedimiento</th>
                        <th >Pendiente</th>
                        <th >Observación</th>
                      </tr>
                    </thead>
                    <tbody >
                        @foreach($pentax_pend as $value)
                        <tr >
                            <td >{{$value['0']->fechaini}}</td>
                            <td >{{$value['0']->id_paciente}}</td>
                            <td >{{$value['0']->apellido1}} {{$value['0']->apellido2}} {{$value['0']->nombre1}} {{$value['0']->nombre2}}</td>
                            <td >{{$value['0']->snombre}}</td>
                            <td >{{$value['0']->procedimiento}}</td>
                            <td ><?php echo $value['1'] ?>&nbsp<?php echo $value['2'] ?></td>
                            <td >{{$value['0']->epobservacion}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="col-md-6">
                <h4>AGENDA DE PROCEDIMIENTOS PENTAX </h4>
            </div>

            @php
                $cantidad_procs=0;
                foreach($calendarios as $calendario){
                    $cantidad = $calendario->where('fechaini','>', $fecha.'  0:00:00')->where('fechaini','<', $fecha.' 23:59:59')->count();
                    $cantidad_procs = $cantidad_procs + $cantidad;

                    //dd($cantidad);
                    //dd($calendario);
                }
                $user_agenda = Auth::user()->id;
                $permiso = Sis_medico\Agenda_Permiso::where('id_usuario',$user_agenda)->where('proc_consul','1')->where('estado','2')->first();


            @endphp


            <div class="form-group col-md-12 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                <label class="col-md-1 control-label">Fecha</label>
                <div class="col-md-12">
                    <form method="POST" action="{{ route('preagenda.pentax')}}" >
                        {{ csrf_field() }}
                        <div class="col-md-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="" name="fecha" class="form-control input-sm" id="fecha" onchange="fechacalendario();">
                            </div>
                        </div>
                        <input type="hidden" id="sel_sala" name="sel_sala" value=@if($sel_sala!=null)"{{$sel_sala}}" @else "{{old('sel_sala')}}" @endif>
                        <input type="submit" id="enviar_fecha" style="display: none;">
                        <div class="form-group col-md-3 col-xs-5">
                            <button type="submit" class="btn btn-primary" formaction="{{route('preagenda.salas_todas')}}"> SALAS TODAS</button>
                        </div>
                        <div class="form-group col-md-3 col-xs-5">
                            <span @if($cantidad_procs>=$maximo_procedimientos) class="label label-danger blink" @elseif($cantidad_procs>=$alerta_procedimientos) class="label label-warning blink" @else class="label label-success blink" @endif>Tiene {{$cantidad_procs}} procedimientos para la fecha</span>

                        </div>
                        <div class="form-group col-md-3 col-xs-5">
                        <a style="color: white;padding:9px;margin-left:15%;margin-top:-5px;" class="btn btn-primary btn-xs agbtn" data-remote="{{ route('ocupar_sala')}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ocupar_sala">OCUPAR SALA</a>
                        </div>
                    </form>
                    @if($fecha=='2020-11-02' || $fecha=='2020-11-03')
                    <div class="form-group col-md-12 col-xs-5">
                        <span  class="label label-danger blink" class="label label-warning blink">¡Feriado del 2 y 3 de Noviembre, no Agendar!</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="box-body">

            <div class="w3-bar w3-blue">
                @foreach($salas_encontradas as $value)
                    @if($value->proc_consul_sala!='2')
                        <button id="btn{{$value->id}}" class="w3-bar-item w3-button tablink w3-orange" onclick="AbrirSala(event,'{{$value->id}}')">{{$value->nombre_sala}}</button>
                    @endif
                @endforeach
            </div>

            @php $sala=0; @endphp
            @foreach($salas_encontradas as $value)
                @if($value->proc_consul_sala!='2')
                    <div id="{{$value->id}}" class="w3-container sala" >
                        <!--h4>{{$value->nombre_sala}}</h4-->
                        <div id='calendar{{$sala}}'></div>
                    </div>
                @endif
                @php $sala=$sala+1; @endphp
            @endforeach

       </div>
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
            defaultDate: '{{$fecha}}'
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
        });
    });
</script>
<script>


    $(document).ready(function()
    {
        <?php $j = 0; ?>
        
        @foreach($calendarios as $clave => $valores)
            @if($salas_encontradas[$j]->proc_consul_sala!='2')
              // page is now ready, initialize the calendar...

            $('#calendar{{$j}}').fullCalendar({
                // put your options and callbacks here
                minTime: "05:00:00",
                lang: 'es',
                locate: 'es',
                @if($fecha != '0')
                    defaultDate: '{{$fecha}}',
                @endif
                //eventLimit: true, // for all non-TimeGrid views
                views:{
                    agenda:{
                        slotDuration: "00:15:00",
                        slotLabelFormat: 'HH:mm',
                        scrollTime: "07:00:00",
                    },
                    //timeGrid: {
                      //eventLimit: 6 // adjust to 6 only for timeGridWeek/timeGridDay
                    //}
                },
                @php $fecha_valida = date("Y-m-d H:i");
                @endphp
                resources: [
                            @foreach($salas_unica as $sala)
                                {
                                  id: '{{$sala->id}}',
                                  title: '{{$sala->nombre_sala}}'
                                },
                            @endforeach
                        ],
                events : [
                    @php
                    $reuniones_sin_id = array();
                    //dd($value);
                    $reuniones_sin_id = DB::table('agenda as a')
                                        ->where('a.estado', 1)
                                        ->where('a.proc_consul', 2)
                                        ->where('id_sala', $salas_unica[$clave]->id)
                                        ->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])
                                        ->join('users as uc', 'uc.id', 'a.id_usuariocrea')
                                        ->join('users as um', 'um.id', 'a.id_usuariomod')
                                        ->select('a.*', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')
                                        ->orderBy('a.fechaini')->get();
                    @endphp
                    @foreach($reuniones_sin_id as $reunion)
                    {
                        id    : '{{$reunion->id}}',
                        start: '{{$reunion->fechaini}}',
                        end: '{{$reunion->fechafin}}',
                        color: '#FF00BF',
                        rendering: 'background',
                        @if(!empty($reunion->procedencia))
                        @php 
                        $nombre_usuarios=Sis_medico\User::where('id',$reunion->id_usuariomod)->first();
                        $sala = Sis_medico\Sala::where('id',$reunion->id_sala)->first();
                        @endphp
                        color: '#023f84',
                        title: '{{$reunion->procedencia}} | {{$sala->nombre_sala}}  | Observaciones : {{$reunion->observaciones}} | Modificado por :  {{$nombre_usuarios->nombre1}} {{ $nombre_usuarios->nombre2}} {{ $nombre_usuarios->apellido1}} {{ $nombre_usuarios->apellido2}}',
                        rendering: 'none',
                        url:'{{ route('agenda.calendario', ['id' => $reunion->id])}}',
                        @endif
                    },
                    @endforeach
                    @foreach($valores as $value)
                    {
                      @if($value->estado == '-1')
                          @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp
                          id    : '{{$value->id}}',
                          className: 'a{{$value->id}}',
                          title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR** @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) |Ciudad Proc: {{$value->ciudad}} @endif',
                          start : '{{ $value->fechaini }}',
                          end : '{{ $value->fechafin }}',
                          @if($value->fechaini <= $fecha_valida)
                            editable: false,
                          @else
                            editable: true,
                          @endif
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
                      @endif
                      @if($value->estado == '1')
                          @php  $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();
                                $doctor=Sis_medico\User::find($value->id_doctor1);
                                $hc = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                                $hc_seguro_nombre = '';
                                if(!is_null($hc)){
                                    if(!is_null($hc->seguro)){
                                        $hc_seguro_nombre = $hc->seguro->nombre;
                                    }

                                }
                          @endphp

                          id    : '{{$value->id}}',
                          className: 'a{{$value->id}}',
                          title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | Doctor: {{$doctor->nombre1}} {{$doctor->apellido1}} | PROCEDIMIENTOS: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) Estado:  Reagendada  @elseif($value->estado_cita == 4) Estado:  ADMISIONADO @endif |  Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: @if($value->estado_cita!='4'){{ $value->nombre_seguro}}@else {{$hc_seguro_nombre}} @endif | Cortesia: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif ',
                          start : '{{ $value->fechaini }}',
                          end : '{{ $value->fechafin }}',
                          @if($value->fechaini <= $fecha_valida )
                            editable: false,
                          @else
                            editable: true,
                          @endif
                          url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
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
                    @endif

                    },
                    @endforeach
                    @php
                        $horario = DB::table('horario_sala')
                        ->where('id_sala', '=', $salas_encontradas[$j]->id)->orderBy('ndia')
                        ->orderBy('hora_ini')
                        ->get();
                    @endphp
                    @foreach($horario as $key=>$xx)
                    {
                        resourceId: '{{$salas_encontradas[$j]->id}}',
                        start: '{{$xx->hora_ini}}',
                        end: '{{$xx->hora_fin}}',
                        color: '@if($xx->tipo == 0) #61c9ff @endif @if($xx->tipo == 1) #6666FF @endif @if($xx->tipo == 2) #FF00BF @endif @if($xx->tipo == 3) #00e600 @endif',
                        rendering: 'background',
                        @if($xx->ndia != 7)
                        dow: [ {{$xx->ndia}}],
                        @endif
                        @if($xx->ndia == 7)
                            dow: [0],
                        @endif
                    },
                    @endforeach
                ],

                height: 850,

                defaultView: 'agendaDay',
                duration: '00:15:00',
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
                        var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');
                        var today = '{{date("Ymd H:m")}}';
                        console.log(view.type);
                    if(check < today)
                    {
                       alert("Fecha pasada, no es posible seleccionar");
                    }
                    else
                    {
                        var dia =  $.fullCalendar.formatDate(start,'ddd');
                        var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                        var dato = 0;

                        @foreach($salas_unica as $x)
                            @php
                                $horario = DB::table('horario_sala')
                                ->where('id_sala', '=', $x->id)->orderBy('ndia')
                                ->orderBy('hora_ini')
                                ->get();
                            @endphp

                            @foreach($horario as $value)
                              if(dia == '<?php echo $value->dia; ?>'){
                                if(hora >= '<?php echo $value->hora_ini; ?>' && hora < '<?php echo $value->hora_fin; ?>'){
                                 dato = 1;
                               }
                              }
                            @endforeach
                        @endforeach

                        if(dato == 0)
                        {
                            alert('¡AGENDA FUERA DEL HORARIO DE LA SALA DISPONIBLE PARA EL DOCTOR!');
                            return 0;
                        }

                        @if($cantidad_procs>=$maximo_procedimientos )
                            @if(!is_null($permiso))
                                var xconfirmar = confirm("Recuerde que ya excede los {{$maximo_procedimientos}} procedimientos, Desea Continuar");
                                if(xconfirmar){
                                    location.href = "{{route('preagenda.nuevo') }}/"+start+"/0/{{$salas_encontradas[$j]->id}}";
                                }
                            @else
                                alert("No puede ingresar mas de {{$maximo_procedimientos}} procedimientos");
                            @endif
                        @else
                            location.href = "{{route('preagenda.nuevo') }}/"+start+"/0/{{$salas_encontradas[$j]->id}}";
                        @endif


                    }

                },
                eventDrop: function(event, delta, start, end){ // event drag and drop
                   var start = event.start;
                   var id = event.id;
                   var end = event.end;
                   var url = "{{ route('preagenda.actualizarhorario') }}/"+id+"/"+start+"/"+end;
                   $.get(url, function(result){
                      alert(result);
                      //location.reload(true);
                      $('#enviar_fecha').click();
                   });
                },


                header: {
                      left: '',
                      center: 'title',
                      right: 'agendaWeek,agendaDay,listDay',
                  },
            });

            @endif
        <?php $j = $j + 1;?>
        @endforeach
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
    //AbrirSala('10');
    //$('#btn10').click();

    $(".sala").css("display", "none");
    $('.tablink').removeClass('w3-orange');

    n_sala = document.getElementById('sel_sala').value;
    //alert('#btn'+n_sala);
    if(n_sala!=''){
        $('#btn'+n_sala).addClass('w3-orange');
        $('#'+n_sala).css("display", "block");
    }else{

        $('#btn10').addClass('w3-orange');
        $("#10").css("display", "block");
    }






    $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li class="active">Pentax</li>');

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
     var dato = document.getElementById('fecha').value;
    $('#enviar_fecha').click();
 }



function AbrirSala(evt, SalaNombre) {

    //alert(SalaNombre);
    //console.log(evt);
    var i, x, tablinks;
    var x = document.getElementsByClassName("sala");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" w3-orange", "");
    }

    document.getElementById(SalaNombre).style.display = "block";
    document.getElementById('sel_sala').value= SalaNombre;
    evt.currentTarget.className += " w3-orange";
}


</script>
@include('sweet::alert')
@endsection
