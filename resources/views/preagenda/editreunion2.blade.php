
@extends('agenda.base')

@section('action-content')

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Editar Reunión: </h4>
                    @php 
                        $doctor=Sis_medico\User::find($agenda->id_doctor1);
                        $sala=Sis_medico\Sala::find($agenda->id_sala); 
                        $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                        $observaciones=$agenda->observaciones;
                    @endphp       
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form class="form-vertical" role="form" method="POST" action="{{ route('agenda.updatereunion2', ['id' => $agenda->id]) }}">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                            <input type="hidden" id="fecha" value="{{date('Y-m-d H:i')}}"> 
                            @if($agenda->fechaini > date('Y-m-d H:i'))                                
                            <!--estado cita-->
                            <div class="form-group col-md-6 {{ $errors->has('estado_cita') ? ' has-error' : '' }}">
                                <label for="est_amb_hos" class="col-md-12 control-label">SELECCIONE ACCIÓN A REALIZAR</label>
                                <div class="col-md-12">
                                <select id="estado_cita" name="estado_cita" class="form-control" >
                                    <option value="">Seleccione ..</option>
                                    <option value="2">Reagendar</option>
                                    <option value="3">Suspender</option>
                                </select>  
                                @if ($errors->has('estado_cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado_cita') }}</strong>
                                    </span>
                                @endif
                                </div>
                            </div> 
                            @endif

                            <input type="hidden" name="id_doctor1" value="{{$agenda->id_doctor1}}">
                        
                            <!--salas-->
                            <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                                <div class="col-md-12">
                                    <input id="tid_sala" type="text" class="form-control" name="tid_sala" value="{{$sala->nombre_sala}} / {{$hospital->nombre_hospital}}" readonly="readonly">
                                    <select id="id_sala" name="id_sala" class="form-control" required>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endforeach
                                    </select> 
                                    @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                    @endif    
                                </div>
                            </div> 

                            <!--inicio-->
                            <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                                <label class="col-md-12 control-label">Inicio</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('inicio')!=''){{old('inicio')}}@else{{$agenda->fechaini}}@endif" name="inicio" class="form-control pull-right" id="inicio" required  >
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

                            <!--fin-->
                            <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label class="col-md-12 control-label">Fin</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="@if(old('fin')!=''){{old('fin')}}@else{{$agenda->fechafin}}@endif" name="fin" class="form-control pull-right" id="fin" required >

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

                            <!--observaciones-->
                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-12 control-label">Observaciones</label>
                                <div class="col-md-12">
                                    <textarea maxlength="200" id="observaciones" class="form-control" name="observaciones" >@if(old('observaciones')!=''){{old('observaciones')}}@else{{$observaciones}}@endif</textarea>
                                    @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>    
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" id="enviar" class="btn btn-primary">
                                        Aceptar
                                    </button>
                                </div>    
                            </div>  
                        </form>
                    </div>    
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="box box-primary">
                <style type="text/css">
                @foreach($cagenda2 as $value)
                    .a{{$value->id}}          
                {
                    color: #023f84;
                                      
                }
                @endforeach

                @foreach($cagenda as $value)
                    .a{{$value->id}}
                {
                @if($value->estado_cita == 0)
                    color: black;
                @else
                    color: {{ $value->color}}; 
                @endif
                }
                @endforeach

                @foreach($cagenda3 as $value)
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
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
                <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
                <div class="box-header with-border">
                    <h4>Agenda del Dr(a). {{Sis_medico\user::find($agenda->id_doctor1)->nombre1}} {{Sis_medico\user::find($agenda->id_doctor1)->apellido1}}</h4>
                </div> 
                <div class="box-body">
                    <div id='calendar'>
        
        
                    </div>

    

                </div>        
            </div>
        </div>        
    </div>        
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    
    
    $(function () {
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'


            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',
            
             //Important! See issue #1075
            
        });
        $("#inicio").on("dp.change", function (e) {
            $('#fin').data("DateTimePicker").minDate(e.date);
        });
        $("#fin").on("dp.change", function (e) {
            $('#inicio').data("DateTimePicker").maxDate(e.date);
        });
    });

    $(document).ready(function() {
        $("#id_sala").hide();
        $("#tid_sala").show();
        $("#inicio").attr("disabled","disabled");
        $("#fin").attr("disabled","disabled");
        $("#observaciones").attr("readonly","readonly");
        $("#observaciones").removeAttr("required","required");

        @if($agenda->fechaini > date('Y-m-d H:i'))

        var estado = document.getElementById("estado_cita").value;
        if (estado==2)
        {
            $("#id_sala").show();
            $("#tid_sala").hide();
            $("#inicio").removeAttr("disabled","disabled");
            $("#fin").removeAttr("disabled","disabled");
            $("#observaciones").attr("required","required");
            $("#observaciones").removeAttr("readonly","readonly");
            $("#observaciones").value("");
        }
        if (estado==3)
            {
                $("#observaciones").attr("required","required");
                $("#observaciones").removeAttr("readonly","readonly");
                $("#observaciones").value("");
            }

        $("#estado_cita").change(function () {
            
            $("#id_sala").hide();
            $("#tid_sala").show();
            $("#inicio").attr("disabled","disabled");
            $("#fin").attr("disabled","disabled");
            $("#observaciones").attr("readonly","readonly");
            $("#observaciones").removeAttr("required","required");
            var estado = document.getElementById("estado_cita").value;
            if (estado==2)
            {
                $("#id_sala").show();
                $("#tid_sala").hide();
                $("#inicio").removeAttr("disabled","disabled");
                $("#fin").removeAttr("disabled","disabled");
                $("#observaciones").attr("required","required");
                $("#observaciones").removeAttr("readonly","readonly");
                
            }
            if (estado==3)
            {
                $("#observaciones").attr("required","required");
                $("#observaciones").removeAttr("readonly","readonly");
                
            }

         });

        @endif

        $('#calendar').fullCalendar({
            // put your options and callbacks here
            lang: 'es',
            locate: 'es',

            events : [
                @foreach($cagenda as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} {{ $value->papellido1}} | Proc: {{$value->nombre_procedimiento}} @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) - {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif  | {{ $value->nombre_seguro}}',    
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                   
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach
                @foreach($cagenda3 as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} {{ $value->papellido1}} | Consulta | {{ $value->nombre_seguro}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                   
                  @if($value->estado_cita == 0)
                    color: 'black',
                  @else
                    color: '{{ $value->color}}', 

                  @endif

                },
                @endforeach

                @foreach($cagenda2 as $value)
                {
                  @php $sala=Sis_medico\sala::find($value->id_sala) @endphp  
                  
                  id    : '{{$value->id}} idreunion',
                  className: 'a{{$value->id}} classreunion',
                  title : 'Reunión Programada - {{$sala->nombre_sala}}/{{Sis_medico\hospital::find($sala->id_hospital)->nombre_hospital}} {{ $value->observaciones}} | Agendado por: {{$value->unombre1}} {{$value->uapellido1}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                   


                                     
                },


                @endforeach


            ],
            defaultView: 'list',
                  editable: false,
            selectable: true,
            header: {
                      left: 'prev,next today',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay,listMonth,listDay',
                  },
                 
            
        })

        
        
    });
           

    




</script>
@include('sweet::alert')
@endsection
