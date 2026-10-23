
@extends('agenda.base')

@section('action-content')

 
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--link rel="stylesheet" href="{{ asset("/css/screen.css")}}"-->

 
<section class="content" >
    <div class="row">

        <div class="col-md-12">
              
            <div class="box box-primary">
                <div class="box-header with-border" style="padding: 5px;">
                    <div class="col-md-12">
                        <h4> @lang('agenda.agregarNuevaAgendaDoctor') {{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}} </h4>

                    </div>             
                </div>
                <div class="box-body">
                          
                    <form class="form-vertical" id="target" role="form" method="POST" action="{{ route('agenda.nuevo_reunion_guardar') }}">
                        <input type="hidden" name="clase" value="{{$i}}">
                        <input id="id_doctor1" type="hidden" name="id_doctor1" value="{{$doctor->id}}" >
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control input-sm" name="unix" id="unix" value="{{$unix}}">
                                     
                        <div class="form-group col-md-6 cl_inicio {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">@lang('agenda.inicio')</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$hora}}" name="inicio" class="form-control" id="inicio" required>
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

                        <div class="form-group col-md-6 cl_fin {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">@lang('agenda.fin')</label>
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

                        <div class="form-group col-md-6 cl_observaciones {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-4 control-label">@lang('agenda.titulo')</label>
                            <div class="col-md-7">
                                <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required="required">
                                <span class="help-block">
                                        <strong id="str_observaciones"></strong>
                                    </span>
                            </div>
                        </div>

                        <div id="tipo_clase" class="form-group col-md-6 {{ $errors->has('clase') ? ' has-error' : '' }} ">

                            <label for="clase" class="col-md-4 control-label">@lang('agenda.tipodereunion')</label>
                            <div class="col-md-7">
                                <select id="clase" name="clase" class="form-control input-sm" >
                                    <option  value="">@lang('agenda.seleccione')..</option> 
                                    <option @if(old('clase')=="Reuniones"){{"selected"}}@endif value="Reuniones">@lang('agenda.reuniones')</option> 
                                    <option @if(old('clase')=="Vacaciones"){{"selected"}}@endif value="Vacaciones" >@lang('agenda.vacaiones')</option>
                                    <option @if(old('clase')=="Eventos"){{"selected"}}@endif value="Eventos" >@lang('agenda.eventos')</option>
                                    <option @if(old('clase')=="Cursos"){{"selected"}}@endif value="Cursos" >@lang('agenda.cursos')</option>
                                    <option @if(old('clase')=="Otros"){{"selected"}}@endif value="Otros" >@lang('agenda.otros')</option>
                                </select>  
                                @if ($errors->has('clase'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('clase') }}</strong>
                                </span>
                                @endif
                            </div>  
                        </div>

                                     
                        <div class="form-group col-md-6 cl_id_sala {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">@lang('agenda.ubicacion')</label>
                            <div class="col-md-7">
                            <select id="id_sala" name="id_sala" class="form-control" required>
                                    <option value="">@lang('agenda.seleccione')..</option>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">@if($sala->nombre_sala == "..") {{$sala->nombre_hospital}}{{$sala->nombre_sala}}@else {{$sala->nombre_sala}} / {{$sala->nombre_hospital}} @endif</option>
                                    @endforeach
                                </select>      
                                <span class="help-block">
                                        <strong id="str_id_sala"></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" >@lang('agenda.guardar')</button>
                                
                            </div>
                        </div>  
                       
                    </form>   
                </div>
            </div>
        </div> 
    </div>




</section>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
    
    $('#inicio').datetimepicker({
        format: 'YYYY/MM/DD HH:mm'
    });

    $('#fin').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD HH:mm',
        minDate: '{{$hora}}',
         //Important! See issue #1075
    });

    $("#inicio").on("dp.change", function (e) {
        $('#fin').data("DateTimePicker").minDate(e.date);
        incremento(e);
    });

    var inicio = document.getElementById("inicio").value;

    var fin = moment(inicio).add(120, 'm').format('YYYY/MM/DD HH:mm');
    $("#fin").val(fin);

    function incremento (e){
        var fjs_inicio = document.getElementById("inicio").value;
        
        var fjs_fin = moment(fjs_inicio).add(120, 'm').format('YYYY/MM/DD HH:mm');    
        

        $("#fin").val(fjs_fin);
        
    }

</script>

 


@endsection
