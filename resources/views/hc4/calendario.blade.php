
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">
.fc-title{
    font-size: 1.1em !important;
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
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif
            @if($value->cortesia=='SI')
                background-color: #ccffcc;
            @endif
             
        }
    @endforeach
    @foreach($agenda_px as $value)
        .a{{$value->id}}
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif
            @if($value->cortesia=='SI')
                background-color: #ccffcc;
            @endif
             
        }
    @endforeach

    @foreach($agenda3 as $value)
        .a{{$value->id}}
        {
            @if($value->estado_cita == 0)
                color: black;
            @else
                color: {{ $value->color}}; 
            @endif
            @if($value->cortesia=='SI')
                background-color: #ccffcc;
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

        .fc-scroller{
          height: auto !important;
        }       
</style>
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

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css'/>

<form id="formulario4" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
  {{ csrf_field() }}
  <input type="hidden" name="variable4" id="variable4">
</form>

<section class="content" >
  <div class="box box-primary" style="border-top: none;">
    <div class="box-header">
      <div class="row col-md-12 col-12 color">
        <h4><b>@lang('hc4.agendaDr') {{$doctor->nombre1}} {{$doctor->apellido1}}</b></h4>
      </div>
      <div class="row ">
        <div class="btn-group col-5" role="group" aria-label="Button group with nested dropdown">
          <a data-remote="{{route('hc4/agendar_dr.hc4_agendar_doctor',['id_doctor' => $doctor->id, 'i' => '0' ])}}" data-toggle="modal" data-target="#magendar_dr"  class="btn btn-info color2">@lang('hc4.agendar'): @lang('hc4.consultas')</a>
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle color2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" data-remote="{{route('hc4/agendar_dr.hc4_agendar_doctor',['id_doctor' => $doctor->id, 'i' => '1' ])}}" data-toggle="modal" data-target="#magendar_dr">@lang('hc4.procedimientos')</a>
            </div>
          </div>
        </div>
        <div class="col-md-1 col-1">&nbsp;</div>
        <div class="btn-group col-5" role="group" aria-label="Button group with nested dropdown">
          <a data-remote="{{route('hc4/agendar_dr.hc4_reunion',['id_doctor' => $doctor->id, 'i' => 'Reuniones' ])}}" data-toggle="modal" data-target="#magendar_dr"  class="btn btn-info color2">@lang('hc4.agendar'): @lang('hc4.reuniones')</a>
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle color2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" data-remote="{{route('hc4/agendar_dr.hc4_reunion',['id_doctor' => $doctor->id, 'i' => 'Vacaciones' ])}}" data-toggle="modal" data-target="#magendar_reunion">@lang('hc4.vacaciones')</a>
              <a class="dropdown-item" data-remote="{{route('hc4/agendar_dr.hc4_reunion',['id_doctor' => $doctor->id, 'i' => 'Eventos' ])}}" data-toggle="modal" data-target="#magendar_reunion">@lang('hc4.eventos')</a>
              <a class="dropdown-item" data-remote="{{route('hc4/agendar_dr.hc4_reunion',['id_doctor' => $doctor->id, 'i' => 'Cursos' ])}}" data-toggle="modal" data-target="#magendar_reunion">@lang('hc4.cursos')</a>
              <a class="dropdown-item" data-remote="{{route('hc4/agendar_dr.hc4_reunion',['id_doctor' => $doctor->id, 'i' => 'Otros' ])}}" data-toggle="modal" data-target="#magendar_reunion">@lang('hc4.otros')</a>
            </div>
          </div>
        </div>
      </div>  
    </div>
    <div class="box-body">
      <div class="col-md-12">
        <form method="POST" id="formulario_fecha" >
        <div class="row">
          {{ csrf_field() }}
          <div class="col-md-5 col-10 cl_fecha_nacimiento">
              <label for="fecha" class="col-md-4 control-label">@lang('hc4.fecha')</label>
              <div class="input-group col-12">
                  <div class="input-group-prepend" id="dt1">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                  </div>
                  <input type="text" class="form-control pull-right"  name="fecha" id="fecha" value="{{old('fecha')}}" required autocomplete="off">
              </div>
              <span class="help-block">
                  <strong id="str_fecha_nacimiento" style="padding-left: 15px;"></strong>
              </span>
          </div>  
          <div class="col-md-1 col-2" style="padding-top: 30px" >
            <button type="button" class="btn btn-primary color2" id="buscar_datos">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
          </div>
          <div class="col-md-3 col-12" style="padding-top: 30px">
            <center>
              <div class="col-md-1 col-5" >
                <input type="button"  class="btn btn-primary color2" id="buscador_dia" value="Agenda del Dia">
              </div>
            </center>
          </div>
        </div>
        </form>
      </div>
    </div>      
  </div>
  <div class="box box-primary" style="border-top: none;">
    <div class="box-body">
      <div id='calendar_r'></div>
    </div>  
  </div> 
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
  $('#buscar_datos').click(function(){
    //alert("entra");
    $.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',          
        data: $("#formulario_fecha").serialize(),
        url:"{{route('hc4.calendario_fullcalendar')}}",
        success: function(data){
          //console.log(data);
          //alert("ok");
          $("#modificar").html(data);
          //$("#agenda_semana").addClass('oculto');
          
        },
        error: function(data){
          console.log(data);
        }
    }); 
  });
  
  $('#buscador_dia').click(function(){
    $('#variable4').val('4');
    $('#formulario4').submit();
    //alert("entra");
    /*$.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',          
        data: $("#formulario_fecha").serialize(),
        url:"{{route('busqueda_pacientes_doctor')}}",
        success: function(data){
          //console.log(data);
          //alert("ok");
          $("#info").html(data);
          //$("#agenda_semana").addClass('oculto');
          
        },
        error: function(data){
          console.log(data);
        }
    });*/ 
  });

  
  var remoto_href = '';
    jQuery('body').on('click', '[data-toggle="modal"]', function() {
      remoto_href = jQuery(this).data('remote');
      jQuery(jQuery(this).data('target')).removeData('bs.modal');
      jQuery(jQuery(this).data('target')).find('.modal-body').empty();
      jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
  });
  
  $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hoy}}',
  });

  $('#dt1').on('click', function(){
            $('#fecha').datetimepicker('show');
  });
  
  $(function () {
    @if($agendas_pac!=[])
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });
    @endif

        
        


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
      $(this).removeData('bs.modal');
  });

  $('#magendar_reunion').on('hidden.bs.modal', function(){
    $(this).removeData('bs.modal');
  });            


    $(document).ready(function() { 

        $(".breadcrumb").append('<li class="active">Agenda</li>');
        // page is now ready, initialize the calendar...
        $('#calendar_r').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            defaultDate: '{{$fecha_hoy}}',
            views:{
                agenda:{
                    slotDuration: "00:15:00" ,
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "07:00:00"
                }

            }, 
            events : [
                

                @foreach($agenda2 as $value)
                {
                  @php $sala=Sis_medico\Sala::find($value->id_sala) @endphp  
                  className: 'a{{$value->id}}',
                  id    : '{{$value->id}}',
                  title : '{{$value->procedencia}} - @if($sala->nombre_sala == "..") {{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}}{{$sala->nombre_sala}}@else{{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}}@endif {{ $value->observaciones}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84', 
                                     
                }, 
                @endforeach


                @foreach($agenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp

                  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}}  @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  ({{ $value->nombre_seguro}}), PROCEDIMIENTOS:{{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  //url: '{{ route("procedimiento.ruta", ['id' => $value->id])}}',

                  url: '{{route('nd.buscador', ['id_paciente' => $value->id_paciente])}}',
                  
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

                @foreach($agenda_px as $value)
                {
                  @php  $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); 
                        $tx_prx="";
                        if($value->pentax!=null){
                          
                          $pentax_proc = DB::table('pentax_procedimiento as p')->join('procedimiento as px','px.id','p.id_procedimiento')->where('p.id_pentax',$value->pentax)->get();
                          
                          $xc=0;
                          $tx_prx=""; 
                          
                          foreach ($pentax_proc as $prx){

                            if($xc==0){
                              $tx_prx = $prx->nombre;                              
                            }else{
                              $tx_prx = $tx_prx.'+'.$prx->nombre; 
                            }
                            $xc = $xc + 1;  
                            

                          }
                          //dd($tx_prx);   
                            
                        }
                        $historia=Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                        $cant=0;
                        if(!is_null($historia)){

                          $protocolo = Sis_medico\hc_protocolo::where('hcid',$historia->hcid)->get();
                          
                          foreach($protocolo as $pval){
                            $archivos = Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$pval->id)->where('estado','>','1')->where('estado','<','4')->get()->count();
                            $cant = $cant + $archivos;
                          }
                        }
                        
                  @endphp
                  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}}  @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  ({{ $value->nombre_seguro}}), PROCEDIMIENTOS: @if($tx_prx!=""){{$tx_prx}}@else{{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif @endif| @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}}@if($cant>0)|| {{$cant}} archivo(s) cargado(s) ||@endif ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  url: '{{route('nd.buscador', ['id_paciente' => $value->id_paciente])}}',
                  
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

                @foreach($agenda3 as $value)
                {
                  @php $historia=Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first(); @endphp
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}} @endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  ({{ $value->nombre_seguro}})| CONSULTA | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif @if($historia!=null)  @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  url: '{{route('nd.buscador', ['id_paciente' => $value->id_paciente])}}',
                  /*url: '@if($value->estado_cita >= 4){{ route("agenda.detalle2", ['id' => $value->id ])}}@else{{ route("agenda.detalle", ['id' => $value->id])}}@endif',*/ /* en el title if($historia->estado==0) | Pendiente de Atención else | ATENDIDO endif */
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach
            ],
            defaultView: 'agendaWeek',
            duration: '00:15:00',
            selectHelper: true,
            selectable: true,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            startEditable: false,

            header: {
              left: 'prev,next today',
              center: 'title',
              right: 'month,agendaWeek,agendaDay,listMonth,listDay',
            },
            
            
                 
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
        });
    });

var vartiempo = setInterval(function(){ location.reload(); }, 300000);



</script>

