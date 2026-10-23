@extends('agenda.base')
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
        }
    
    @endforeach
</style>

@section('action-content')
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
 <div class="box">
	<h3>Agenda de pacientes</h3>

    <div class="col-sm-12">
      <form class="form-horizontal" role="form" method="POST" action="{{ route('agenda.reuniondoctor') }}">
        <h4>Agregar Reunion</h4>
                    <input id="id_doctor1" type="hidden" name="id_doctor1" value="{{$id}}" >
                    {{ csrf_field() }}
                         
                        <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">Inicio</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('inicio') }}" name="inicio" class="form-control" id="inicio" required>
                                </div>
                                   @if ($errors->has('inicio'))
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

                        <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Fin</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fin') }}" name="fin" class="form-control" id="fin" required>
                                </div>
                                @if ($errors->has('fin'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fin') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <div class="form-group col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-4 control-label">Titulo</label>
                            <div class="col-md-7">
                                <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required="required">
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--pais-->
                        <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">Ubicación</label>
                            <div class="col-md-7">
                            <select id="id_sala" name="id_sala" class="form-control" required>
                                    <option value="">Seleccione..</option>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">@if($sala->nombre_sala == "..") {{$sala->nombre_hospital}}{{$sala->nombre_sala}}@else {{$sala->nombre_sala}} / {{$sala->nombre_hospital}} @endif</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar Nueva Reunion
                                </button>
                            </div>
                        </div>  
                    </form>   
           
    </div>
	  <div id='calendar' style="height: 1220px;"></div>
  </div>
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
</script>
</section>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/jquery.min.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>


<script>

    $(document).ready(function() {
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',

            views:{
                agenda:{
                
                slotDuration: "00:15:00"
                }
             },  
            events : [
                @foreach($agenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp
                  @php $historia=Sis_medico\historiaclinica::where('id_agenda',$value->id)->first(); @endphp
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}}  @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif ({{ $value->nombre_seguro}}), PROCEDIMIENTOS:{{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @endif @if($historia!=null) @if($historia->estado==0) | Pendiente de Atención @else | ATENDIDO @endif @endif | Cortesia: {{ $value->cortesia}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  /*url: '{{ route("agenda.detalle", ['id' => $value->id])}}',*/
                  url: '@if($value->estado_cita >= 4){{ route("agenda.detalle2", ['id' => $value->id])}} @else{{ route("agenda.detalle", ['id' => $value->id])}}@endif',
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
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}} @endif ({{ $value->nombre_seguro}})| CONSULTA | @if($value->estado_cita == 4) ASISTIÓ @elseif($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1) Confirmado @endif @if($historia!=null) @if($historia->estado==0) | Pendiente de Atención @else | ATENDIDO @endif @endif | Cortesia: {{ $value->cortesia}} ',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  /*url: '{{ route("agenda.detalle", ['id' => $value->id])}}',*/
                  url: '@if($value->estado_cita >= 4){{ route("agenda.detalle2", ['id' => $value->id ])}}@else{{ route("agenda.detalle", ['id' => $value->id])}}@endif',
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

                @foreach($agenda2 as $value)
                {
                  @php $sala=Sis_medico\sala::find($value->id_sala) @endphp  
                  className: 'a{{$value->id}}',
                  id    : '{{$value->id}}',
                  title : 'Reunión Programada - @if($sala->nombre_sala == "..") {{Sis_medico\hospital::find($sala->id_hospital)->nombre_hospital}}{{$sala->nombre_sala}}@else{{$sala->nombre_sala}}/{{Sis_medico\hospital::find($sala->id_hospital)->nombre_hospital}}@endif {{ $value->observaciones}}',  
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
</script>

@endsection