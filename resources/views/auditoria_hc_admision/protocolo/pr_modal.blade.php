<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">PROTOCOLO OPERATORIO AUDITORIA No. {{ $protocolo->id }}</h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
		<form class="form-vertical" role="form" method="POST" action="{{ route('auditoria_protocolo.guardar_op') }}">
            {{ csrf_field() }}
            <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
            <input  type="hidden" class="form-control" name="xtipo" value="auditoria" >
           
            <div class="box-body col-md-12">
                <!--id_procedimiento_completo-->
                <div class="form-group col-md-6{{ $errors->has('tipo_anestesia') ? ' has-error' : '' }}">
                    <label for="tipo_anestesia" class="col-md-6 control-label">Tipo Anestesia</label>

                    <div class="col-md-6">
                        <select id="tipo_anestesia" name="tipo_anestesia" class="form-control" required="required">
                            <option @if($protocolo->tipo_anestesia=='GENERAL') selected @endif value="GENERAL"> GENERAL</option>
                            <option @if($protocolo->tipo_anestesia=='SEDACION') selected @endif value="SEDACION"> SEDACION</option>
                            <option @if($protocolo->tipo_anestesia=='GENERAL90') selected @endif value="GENERAL90"> GENERAL(90 MIN)</option>
                            <option @if($protocolo->tipo_anestesia=='GENERAL120') selected @endif value="GENERAL120"> GENERAL(120 MIN)</option>
                            <option @if($protocolo->tipo_anestesia=='GENERAL150') selected @endif value="GENERAL150"> GENERAL(150 MIN)</option>
                            <option @if($protocolo->tipo_anestesia=='GENERAL180') selected @endif value="GENERAL180"> GENERAL(180 MIN)</option>
                            <option @if($protocolo->tipo_anestesia=='GENERAL210') selected @endif value="GENERAL210"> GENERAL(210 MIN)</option>
                        </select>  
                        @if ($errors->has('tipo_anestesia'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo_anestesia') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6{{ $errors->has('id_doctor_examinador2') ? ' has-error' : '' }}">
                    <label for="id_doctor_examinador2" class="col-md-4 control-label">Cirujano</label>

                    <div class="col-md-8">
                        <select id="id_doctor_examinador2" name="id_doctor_examinador2" class="form-control" required="required">
                        	@foreach($doctores as $doctor)
	                            <option @if($doctor->id==$id_doctor_firma) selected @endif value="{{$doctor->id}}"> {{$doctor->apellido1}} @if($doctor->apellido2!='N/A') {{$doctor->apellido2}} @endif {{$doctor->nombre1}} </option>
                        	@endforeach
                        </select>  
                        @if ($errors->has('id_doctor_examinador2'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id_doctor_examinador2') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

				<div class="form-group col-md-6{{ $errors->has('fecha_operacion') ? ' has-error' : '' }}">
                    <label for="fecha_operacion" class="col-md-6 control-label">Fecha Operacion</label>

                    <div class="col-md-6">
                        <input class="form-control" type="text" name="fecha_operacion" id="fecha_operacion" value="{{$fecha_operacion}}" required="required"> 
                        @if ($errors->has('fecha_operacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_operacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6{{ $errors->has('id_doctor_ayudante_con') ? ' has-error' : '' }}">
                    <label for="id_doctor_ayudante_con" class="col-md-4 control-label">Ayudante</label>

                    <div class="col-md-8">
                        <select id="id_doctor_ayudante_con" name="id_doctor_ayudante_con" class="form-control" >
                            <option value=""> </option>
                            @foreach($doctores as $doctor)
	                            <option @if($doctor->id==$id_doctor_ayudante_con) selected @endif value="{{$doctor->id}}"> {{$doctor->apellido1}} @if($doctor->apellido2!='N/A') {{$doctor->apellido2}} @endif {{$doctor->nombre1}} </option>
                        	@endforeach
                        </select>  
                        @if ($errors->has('id_doctor_ayudante_con'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id_doctor_ayudante_con') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6{{ $errors->has('hora_ini') ? ' has-error' : '' }}">
                    <label for="hora_ini" class="col-md-6 control-label">Hora Inicio</label>

                    <div class="col-md-6">
                        <input class="form-control" type="time" name="hora_ini" id="hora_ini" value="{{$hora_inicio}}" required="required"> 
                        @if ($errors->has('hora_ini'))
                        <span class="help-block">
                            <strong>{{ $errors->first('hora_ini') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6{{ $errors->has('id_anestesiologo') ? ' has-error' : '' }}">
                    <label for="id_anestesiologo" class="col-md-4 control-label">Anestesiólogo</label>
                    <div class="col-md-8">
                        <select id="id_anestesiologo" name="id_anestesiologo" class="form-control" required="required">
                        	
                            @foreach($anestesiologos as $anest)
	                            <option  value="{{$anest->id}}">{{$anest->apellido1}} @if($anest->apellido2!='N/A') {{$anest->apellido2}} @endif {{$anest->nombre1}}</option>
                        	@endforeach
                        </select>  
                        @if ($errors->has('id_anestesiologo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id_anestesiologo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @php $protocolo_original = Sis_medico\hc_protocolo::find($protocolo->id_protocolo_org); @endphp
                <div class="form-group col-md-6">
                    <label for="id_doctor_ayudante_con" class="col-md-4 control-label">Descripcion</label>
                    <div class="col-md-8">
                    <input class="form-control" type="text" name="descripcion" id="descripcion" value="@if(!is_null($protocolo_original)) {{ $protocolo_original->descripcion }} @endif">
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label for="id_doctor_ayudante_con" class="col-md-4 control-label">Muestra:</label>
                    <div class="col-md-8">
                    <input class="form-control" type="text" name="muestra" id="muestra" value="@if(!is_null($protocolo_original)) {{ $protocolo_original->muestra }} @endif">
                    </div>
                </div>
					
               
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm" formtarget="_blank">
                        Protocolo Operatorio
                    </button>
                </div>

                <div class="col-md-4" >
                    <button type="submit" class="btn btn-warning btn-sm" formaction="{{ route('auditoria_protocolo_cpre_eco.guardar_op_cpre_eco') }}" formtarget="_blank"> Protocolo Operatorio CPRE+ECO</button>
                </div>

                <div class="col-md-4">
                    
                    <button type="submit" formtarget="_blank" formaction="{{route('formulario.017')}} " class="btn btn-primary btn-sm">Protocolo Quirurgico</a>
                    
                </div>
               

                

            </div>
        </form>   


	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
	$(function () {
        $('#fecha_operacion').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });    


	

</script>