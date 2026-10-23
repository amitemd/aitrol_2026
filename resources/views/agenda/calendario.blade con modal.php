
@extends('agenda.base')

@section('action-content')

<div class="modal fade" id="favoritesModal" 
     tabindex="-1" role="dialog" 
     aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-footer">
        <button type="button" 
           class="btn btn-default" 
           data-dismiss="modal">Close</button>
        <span class="pull-right">
          <button type="button" class="btn btn-primary">
            Add to Favorites
          </button>
        </span>
      </div>
    </div>
  </div>
</div>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
 <div class="box">
 <div class="box-header">
    <div class="row">
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('agenda.paciente', ['id' => $id, 'i' => '0'])}}" >Agregar nuevo Paciente</a>
        </div>
    </div>
  </div>
	<h3>Agenda de pacientes del Dr. {{ Sis_medico\user::find($id)->apellido1 }} {{ Sis_medico\user::find($id)->nombre1 }}</h3>
  <div class="panel panel-default">
                <div class="panel-heading">Agregar nueva cita <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('agenda.store') }}">
                        <input id="id" type="hidden" name="id_doctor1" value="{{$id}}" >
                        {{ csrf_field() }}
                        <!--cedula-->
                        <a style="display: none;" id="mienlace"></a>
                        <div class="form-group col-md-6 {{ $errors->has('id') ? ' has-error' : '' }}">

                            <label for="id" class="col-md-4 control-label">Cédula del paciente</label>
                            <div class="col-md-7">
                                <input id="idpaciente" maxlength="10" type="text" class="form-control" name="idpaciente" value="@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" required onchange="teclaEnter(event);" autofocus >
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">Nombre del Paciente</label>
                            <div class="col-md-7">
                                <input id="nombre1" type="text" class="form-control" disabled="disabled" name="nombre1" value="@if($paciente != Array()){{$paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2}}@else {{old('nombre1')}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required >
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Doctor</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  disabled="disabled">
                            </div>
                        </div>

                        <!--pais-->
                        <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">Ubicacion</label>
                            <div class="col-md-6">
                            <select id="id_sala" name="id_sala" class="form-control" required>
                                    <option value="">Seleccione..</option>
                                    @foreach ($salas as $sala)
                                        <option value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--proc_consul-->
                        <div class="form-group col-md-6 {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-4 control-label">Tipo de Agendamiento</label>
                            <div class="col-md-6">
                                <select id="proc_consul" name="proc_consul" class="form-control" onchange="edad();">

                                    <option value="1">Procedimiento</option> 
                                    <option value="0" selected>Consulta</option>
                                </select>  
                                @if ($errors->has('proc_consul'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proc_consul') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio3" class="form-group col-md-6 {{ $errors->has('id_procedimiento') ? ' has-error' : '' }} oculto">
                            <label for="id_procedimiento" class="col-md-4 control-label">Procedimiento a realizar</label>
                            <div class="col-md-6">
                            <select id="id_procedimiento" name="id_procedimiento" class="form-control" >
                                    <option value="" selected>Seleccione..</option>
                                    @foreach ($procedimiento as $value)         
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_procedimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_procedimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio1" class="form-group col-md-6 {{ $errors->has('id_doctor2') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor2" class="col-md-4 control-label">Medico Asistente 1</label>
                            <div class="col-md-6">
                            <select id="id_doctor2" name="id_doctor2" class="form-control" >
                                    <option value="" selected>Seleccione..</option>
                                    @foreach ($users as $user)
                                        @if($doctor->id != $user->id)
                                        <option value="{{$user->id}}">{{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                        @endif
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_doctor2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div id="cambio2" class="form-group col-md-6 {{ $errors->has('id_doctor3') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor3" class="col-md-4 control-label">Medico Asistente 2</label>
                            <div class="col-md-6">
                            <select id="id_doctor3" name="id_doctor3" class="form-control">
                                    <option value="" selected>Seleccione..</option>
                                    @foreach ($users as $user)         
                                    @if($doctor->id != $user->id)
                                        <option value="{{$user->id}}">{{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</option>
                                    @endif
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_doctor3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-4 control-label">Tipo de Ingreso</label>
                            <div class="col-md-6">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control" onchange="edad();">
                                    <option value="" selected>Seleccione..</option>
                                    <option value="0">Ambulatorio</option> 
                                    <option value="1" >Hospitalizado</option>
                                </select>  
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">Inicio</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('inicio') }}" name="inicio" class="form-control pull-right" id="inicio" >
                                </div>
                                @if ($errors->has('inicio'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('inicio') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">Fin</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fin') }}" name="fin" class="form-control pull-right" id="fin">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-4 control-label">Observaciones</label>
                            <div class="col-md-7">
                                <input id="observaciones" maxlength="10" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required>
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>    
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                        </div>  
                    </form>
                </div>
            </div>


	<div id='calendar' style="height: 1220px;"></div>
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
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',
            events : [
                @foreach($users as $user)
                {
                    title : '{{ $user->nombre1 }}',	
                    start : '{{ $user->created_at }}',
                    end : '{{ $user->updated_at }}',
                    url : '{{ route('agenda.edit', $user->id) }}'
                },
                @endforeach
            ],
            defaultView: 'agendaWeek',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},

        })
    });
</script>
<script type="text/javascript">

$('#inicio').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    format : 'YYYY-MM-DD HH:mm',
    lang : 'es',

});
$('#fin').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    format : 'YYYY-MM-DD HH:mm',
    lang : 'es',

});


function teclaEnter(e)

{

    vcedula = document.getElementById("idpaciente").value;
        //pulsar boton
        //location.href ="{{ route('agenda.paciente2', ['id' => $id])}}/"+document.getElementById("id").value;
        vcedula =  vcedula.trim();

        if (vcedula != ""){
              location.href ="{{ route('agenda.agenda3', ['id' => $id])}}/"+vcedula;

        }

}

function edad()
{
    var valor = document.getElementById("proc_consul").value;
    var elemento1 = document.getElementById("cambio1");
    var elemento2 = document.getElementById("cambio2");
    var elemento3 = document.getElementById("cambio3");
    if(valor == 0){
        $(elemento1).addClass('oculto');
        $(elemento2).addClass('oculto');
        $(elemento3).addClass('oculto');
    }
    if(valor == 1){
        $(elemento1).removeClass('oculto');
        $(elemento2).removeClass('oculto');
        $(elemento3).removeClass('oculto');
    }
}

</script>
@include('sweet::alert')
@endsection
