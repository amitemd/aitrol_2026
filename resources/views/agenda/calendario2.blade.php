@extends('agenda.base')
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

        .fc-toolbar,.fc-widget-header{
          display: none;
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

      
      
      
  <div class="box box-primary">
    <div class="box-header with-border">
      <div class="row col-md-12"><h4><b>Agenda del Dr(a). {{$doctor->nombre1}} {{$doctor->apellido1}}</b></h4></div>


      <div class="row col-md-12">
        <div class="btn-group col-md-5">
          <a href="{{route('agendar_dr.agendar_doctor',['id_doctor' => $doctor->id, 'i' => '0' ])}}" data-toggle="modal" data-target="#magendar_dr" class="btn btn-primary">Agendar: Consultas</a>
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{route('agendar_dr.agendar_doctor',['id_doctor' => $doctor->id, 'i' => '1' ])}}" data-toggle="modal" data-target="#magendar_dr">Procedimientos</a></li>
          </ul>
        </div>
        <div class="col-md-1">&nbsp;</div>
        <div class="btn-group col-md-5">
          <a href="{{route('agendar_dr.reunion',['id_doctor' => $doctor->id, 'i' => 'Reuniones' ])}}" data-toggle="modal" data-target="#magendar_reunion" class="btn btn-primary">Agendar: Reuniones</a>
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{route('agendar_dr.reunion',['id_doctor' => $doctor->id, 'i' => 'Vacaciones' ])}}" data-toggle="modal" data-target="#magendar_reunion">Vacaciones</a></li>
            <li><a href="{{route('agendar_dr.reunion',['id_doctor' => $doctor->id, 'i' => 'Eventos' ])}}" data-toggle="modal" data-target="#magendar_reunion">Eventos</a></li>
            <li><a href="{{route('agendar_dr.reunion',['id_doctor' => $doctor->id, 'i' => 'Cursos' ])}}" data-toggle="modal" data-target="#magendar_reunion">Cursos</a></li>
            <li><a href="{{route('agendar_dr.reunion',['id_doctor' => $doctor->id, 'i' => 'Otros' ])}}" data-toggle="modal" data-target="#magendar_reunion">Otros</a></li>
          </ul>
        </div>  
      </div>  
       
    </div>
    <div class="box-body">
      <div class="col-md-12">

        <form method="POST" action="{{ route('agenda.agenda2') }}" >
          {{ csrf_field() }}
    
          <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="col-md-3 control-label">Fecha</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>
    

          <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="nombres" class="col-md-3 control-label">Paciente</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>  
            </div>
          </div>
 

          <div class="form-group col-md-1 col-xs-2" >
            <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
          </div>
          <div class="form-group col-md-1 col-xs-2" >
            <input type="submit" formaction="{{route('agenda_doctor.visualizarcompleta')}}" class="btn btn-primary" id="boton_buscar" value="Agenda por semana\mes">
          </div>
        </form>
      </div>
      
    
      <div class="table-responsive col-md-12 col-xs-12">
        <table id="example2" class="table table-striped table-hover" style="font-size: 12px;">
          @if($agendas_pac!=[])
          <thead>
            <th>Cédula</th>
            <th>Apellidos</th>
            <th>Nombres</th>
            <th>Fecha Nacimiento</th>
            <th>Seguro</th>
            <th>Última visita</th>
            <th>Tipo</th>
            <th>Cortesia</th>
          </thead>
          <tbody>
          @foreach ($agendas_pac as $pac)
            <?php $agenda_last= DB::table('agenda as a')->where('a.id_paciente',$pac->id)->join('historiaclinica as h','h.id_agenda','a.id')->where('a.espid','<>','10')->orderBy('a.fechaini','desc')->join('seguros as s','s.id','h.id_seguro')->select('h.*','s.nombre','a.fechaini','a.proc_consul','a.cortesia')->first();
                if(!is_null($agenda_last)){
                  $dia =  Date('N',strtotime($agenda_last->fechaini)); $mes =  Date('n',strtotime($agenda_last->fechaini));    
                }else{
                  $dia = 0; $mes= 0;  
                }
            ?>
            <tr class='clickable-row' @if(!is_null($agenda_last)) data-href='{{ route("agenda.detalle", ['id' => $agenda_last->id_agenda])}}' @if($agenda_last->cortesia=='SI') style="background-color: #ccffcc;" @endif @endif >
              <td>{{$pac->id}}</td>
              <td>{{$pac->apellido1}} {{$pac->apellido2}} </td>
              <td>{{$pac->nombre1}} {{$pac->nombre2}}</td>
              <td>{{$pac->fecha_nacimiento}} </td>
              <td>@if(!is_null($agenda_last)){{$agenda_last->nombre}} @endif</td> 
              <td>@if(!is_null($agenda_last)) @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda_last->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda_last->fechaini,0,4)}} @endif</td>
              <td><b>@if(!is_null($agenda_last)) @if($agenda_last->proc_consul=='0')CONSULTA @elseif($agenda_last->proc_consul=='1')PROCEDIMIENTO @endif @endif</b></td>
              <td><b>@if(!is_null($agenda_last)) @if($agenda_last->cortesia=='SI')<span style="color: red"> SI </span> @else NO @endif @endif</b></td>    
            </tr>
          @endforeach
          </tbody>
          @endif
        </table>
      </div> 
      <div class="col-md-12" style="text-align: center;">
        @php $dia =  Date('N',strtotime($fecha_hoy)); $mes =  Date('n',strtotime($fecha_hoy)); @endphp
         <h4><b>@if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($fecha_hoy,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($fecha_hoy,0,4)}}</b></h4>  
      </div> 

    </div>      
 
    
  </div>

  @if($agenda2->count()>0)
  <div class="box box-primary">
    <div class="box-header">
      <div class="col-md-4">
          <h3 class="box-title"><a href="javascript:void($('#reu').click());"><b>Reuniones</b></a></h3>
      </div>
      <div class="col-md-4">
          <b>tiene {{$agenda2->count()}} agendadas en el día</b>
      </div>
      <div class="pull-right box-tools">
          <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="reu">
              <i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">
      <div id='calendar_r' ></div>
    </div>  
  </div> 
  @endif

  @if($agenda3->count()>0)
  <div class="box box-primary collapsed-box">
    <div class="box-header">
      <div class="col-md-4">
          <h3 class="box-title"><a href="javascript:void($('#con').click());"><b>Consultas</b></a></h3>
      </div>
      <div class="col-md-4">
          <b>tiene {{$agenda3->count()}} agendadas en el día</b>
      </div>
      <div class="col-md-4">
          <b style="background-color: #ccffcc;">{{$agenda3->where('cortesia','SI')->count()}} CORTESIA(S) en el día</b>
      </div>
      <div class="pull-right box-tools">
          <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="con">
              <i class="fa fa-plus"></i></button>
      </div>
    </div>
    <div class="box-body">
      <div id='calendar_c' ></div>
    </div>  
  </div>
  @endif

  @if($agenda_px->count() + $agenda->count() > 0) 
  <div class="box box-primary collapsed-box">
    <div class="box-header">
      <div class="col-md-4">
          <h3 class="box-title"><a href="javascript:void($('#proc').click());"><b>Procedimientos</b></a></h3>
      </div>
      <div class="col-md-4">
          <b>tiene {{$agenda_px->count() + $agenda->count()}} agendadas en el día</b>
      </div>
      <div class="col-md-4">
          <b style="background-color: #ccffcc;">{{$agenda_px->where('cortesia','SI')->count() + $agenda->where('cortesia','SI')->count()}} CORTESIA(S) en el día</b>
      </div>
      <div class="pull-right box-tools">
          <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="proc">
              <i class="fa fa-plus"></i></button>
      </div>
    </div>
    <div class="box-body">
      <div id='calendar_p' ></div>
    </div>  
  </div>
  @endif 
       

  

</section>

  
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ('/plugins/fullcalendar/jquery.min.js') }}"></script>
<script src="{{ asset ('/plugins/fullcalendar/fullcalendar.js') }}"></script>
<script src="{{ asset ('/plugins/fullcalendar/es.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>





<script>
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
        $('#calendar_r').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            //height: 'auto',
            defaultDate: '{{$fecha_hoy}}',
            //allDaySlot: false,
            views:{
                agenda:{
                    @if($doctor->id == '1314490929')
                        slotDuration: "00:10:00" ,
                    @else
                        slotDuration: "00:15:00" ,
                    @endif
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00",

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
                  url: '{{route('reunion.edit2', ['id' => $value->id]) }}',
                                     
                }, 
                @endforeach


            ],
            defaultView: 'listDay',
            editable: false,
            selectable: true,
            header: 'none',
            
                 
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
        });
        $('#calendar_c').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            height: 1280,
            defaultDate: '{{$fecha_hoy}}',
            views:{
                agenda:{
                    @if($doctor->id == '1314490929')
                        slotDuration: "00:10:00" ,
                    @else
                        slotDuration: "00:15:00" ,
                    @endif
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "08:00:00"

                }

            }, 
            events : [
               
                @foreach($agenda3 as $value)
                {
                  @php $historia=Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first(); @endphp
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}} @endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  ({{ $value->nombre_seguro}})| CONSULTA | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif @if($historia!=null)  @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                  /*url: '@if($value->estado_cita >= 4){{ route("agenda.detalle2", ['id' => $value->id ])}}@else{{ route("agenda.detalle", ['id' => $value->id])}}@endif',*/ /* en el title if($historia->estado==0) | Pendiente de Atención else | ATENDIDO endif */
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
              left: 'none',
              center: 'title',
              right: 'month,agendaWeek,agendaDay,listMonth,listDay',
            },
            
                 
            /*eventClick: function(event, jsEvent, view){
              alert(event.id);
            }*/
        });
        $('#calendar_p').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            height: 1280,
            defaultDate: '{{$fecha_hoy}}',
            views:{
                agenda:{
                    @if($doctor->id == '1314490929')
                        slotDuration: "00:10:00" ,
                    @else
                        slotDuration: "00:15:00" ,
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
                  title : '{{ $value->papellido1}}  @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif  ({{ $value->nombre_seguro}}), PROCEDIMIENTOS:{{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @elseif($value->estado_cita == 2) Confirmar Datos @endif | Cortesia: {{ $value->cortesia}} | {{$value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  //url: '{{ route("procedimiento.ruta", ['id' => $value->id])}}',
                  url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                  
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
                  url: '{{ route("agenda.detalle", ['id' => $value->id])}}',
                  
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
        });  
    });

var vartiempo = setInterval(function(){ location.reload(); }, 300000);



</script>

@endsection