


            <div class="panel panel-default">
                <div class="panel-heading">@lang('agenda.agregarNuevaCita')  <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('agenda.store') }}">
                        <input id="id" type="hidden" name="id_doctor1" value="{{$id}}" >
                        {{ csrf_field() }}
                        <!--cedula-->
                        <a style="display: none;" id="mienlace"></a>
                        <div class="form-group {{ $errors->has('id') ? ' has-error' : '' }}">

                            <label for="id" class="col-md-4 control-label">@lang('agenda.cedulaPaciente')</label>
                            <div class="col-md-7">
                                <input id="idpaciente" maxlength="10" type="text" class="form-control" name="idpaciente" value="@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" required onchange="teclaEnter(event);" autofocus >
                                @if ($errors->has('id_paciente'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_paciente') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group  {{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">@lang('agenda.nombrePaciente')</label>
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
                        <div class="form-group {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">@lang('agenda.doctor')</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  disabled="disabled">
                            </div>
                        </div>

                        <!--pais-->
                        <div class="form-group {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">@lang('agenda.ubicacion')</label>
                            <div class="col-md-6">
                            <select id="id_sala" name="id_sala" class="form-control" required>
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
                        <div class="form-group {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-4 control-label">@lang('agenda.tipoAgendamiento')</label>
                            <div class="col-md-6">
                                <select id="proc_consul" name="proc_consul" class="form-control" onchange="edad();">

                                    <option value="1">@lang('agenda.procedimiento')</option> 
                                    <option value="0" >@lang('agenda.consulta')</option>
                                </select>  
                                @if ($errors->has('proc_consul'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proc_consul') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="cambio3" class="form-group {{ $errors->has('id_procedimiento') ? ' has-error' : '' }} oculto">
                            <label for="id_procedimiento" class="col-md-4 control-label">@lang('agenda.procedimientoRealizar')</label>
                            <div class="col-md-6">
                            <select id="id_procedimiento" name="id_procedimiento" class="form-control" >
                                    <option value="" selected>@lang('agenda.seleccione')..</option>
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

                        <div id="cambio1" class="form-group {{ $errors->has('id_doctor2') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor2" class="col-md-4 control-label">@lang('agenda.medicoAsistente') 1</label>
                            <div class="col-md-6">
                            <select id="id_doctor2" name="id_doctor2" class="form-control" >
                                    <option value="" selected>@lang('agenda.seleccione')..</option>
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
                        
                        <div id="cambio2" class="form-group {{ $errors->has('id_doctor3') ? ' has-error' : '' }} oculto">
                            <label for="id_doctor3" class="col-md-4 control-label">@lang('agenda.medicoAsistente') 2</label>
                            <div class="col-md-6">
                            <select id="id_doctor3" name="id_doctor3" class="form-control">
                                    <option value="" selected>@lang('agenda.seleccione')..</option>
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
                        
                        <div class="form-group {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-4 control-label">@lang('agenda.tipodeingreso')</label>
                            <div class="col-md-6">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control" onchange="edad();">
                                    <option value="" selected>@lang('agenda.seleccione')..</option>
                                    <option value="0">@lang('agenda.ambulatorio')</option> 
                                    <option value="1" >@lang('agenda.hospitalizado')</option>
                                </select>  
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('agenda.inicio')</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('inicio') }}" name="inicio" class="form-control pull-right" id="inicio" >
                                    @if ($errors->has('inicio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('inicio') }}</strong>
                                </span>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('agenda.fin')</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fin') }}" name="fin" class="form-control pull-right" id="fin">
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-4 control-label">@lang('agenda.observaciones')</label>
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
                                    @lang('agenda.agregar')
                                </button>
                                <button type="button" 
                        class="btn btn-default" 
                            data-dismiss="modal">@lang('agenda.cerrar')</button>
                            </div>
                        </div>  
                    </form>
                </div>
            </div>


