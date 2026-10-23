	<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">FORMATO DE EVOLUCIÓN AUDITORIA</h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
		<form class="form-vertical" role="form" method="POST" action="{{ route('auditoria_evolucion.guardar_op') }}">
                {{ csrf_field() }}
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >

                <div class="box-body col-md-12">
	                <!--id_procedimiento_completo-->
	                <div class="form-group col-md-6{{ $errors->has('tipo_anestesia') ? ' has-error' : '' }}">
	                    <label for="tipo_anestesia" class="col-md-6 control-label">Tipo Anestesia</label>

	                    <div class="col-md-6">
	                        <select id="tipo_anestesia" name="tipo_anestesia" class="form-control" style="width: 100%;" required="required">
	                            <option @if($protocolo->tipo_anestesia=='GENERAL') selected @endif value="GENERAL"> GENERAL</option>
	                            <option @if($protocolo->tipo_anestesia=='SEDACION') selected @endif value="SEDACION"> SEDACION</option>
	                            <option @if($protocolo->tipo_anestesia=='GENERAL90') selected @endif value="GENERAL90"> GENERAL(90 MIN)</option>
	                            <option @if($protocolo->tipo_anestesia=='GENERAL120') selected @endif value="GENERAL120"> GENERAL(120 MIN)</option>
	                            <option @if($protocolo->tipo_anestesia=='GENERAL150') selected @endif value="GENERAL150"> GENERAL(150 MIN)</option>
	                            <option @if($protocolo->tipo_anestesia=='GENERAL180') selected @endif value="GENERAL180"> GENERAL(180 MIN)</option>
								<option @if($protocolo->tipo_anestesia=='GENERAL210') selected @endif value="GENERAL210"> GENERAL(210 MIN)</option>
								<option @if($protocolo->tipo_anestesia=='8') selected @endif value="8"> PH </option>
								<option @if($protocolo->tipo_anestesia=='9') selected @endif value="9"> MANOMETRIA ESOFAGICA </option>
								<option @if($protocolo->tipo_anestesia=='10') selected @endif value="10"> MANOMETRIA ANORECTAL </option>
								<option @if($protocolo->tipo_anestesia=='10') selected @endif value="11"> CAPSULA ENDOSCOPICA </option>
								<option @if($protocolo->tipo_anestesia=='11') selected @endif value="12"> EDA + PH METRIA </option>
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

					@if(!is_null($cardiologia))

					<div class="form-group col-md-6{{ $errors->has('hora_ini') ? ' has-error' : '' }}">
	                    <label for="hora_ini" class="col-md-6 control-label">Aplicar Cardiología</label>

	                    <div class="col-md-6">
	                        <input type="checkbox" class="flat-orange" name="cardio" id="cardio" value="1" >

	                    </div>
	                </div>
	                <input type="hidden" name="id_cardio" id="id_cardio" value="{{$cardiologia->id_cardio}}">

					<div class="form-group col-md-12{{ $errors->has('hora_ini') ? ' has-error' : '' }}">

						<div class="col-md-2" style="background-color: #00b3b3;">
	                    	<b>Fecha</b>
	                    </div>
	                    <div class="col-md-10" style="background-color: #00b3b3;">
	                    	<b>Resumen de la evaluación cardiológica</b>
	                    </div>
	                    <div class="col-md-2">
	                    	{{substr($cardiologia->fechaini,0,10)}}
	                    </div>
	                    <div class="col-md-10">
	                    	{{$cardiologia->resumen}}
	                    </div>
	                </div>
	                @endif


	                <div class="form-group col-md-12">
	                    <div class="col-md-6 col-md-offset-4">
	                        @if($protocolo->id != 2960)
	                        <button type="submit" class="btn btn-primary" formtarget="_blank">
	                            Descargar Formato Evolucion
	                        </button>
	                        @else
	                        <a href="{{asset('mts-14-02-2022.pdf')}}" class="btn btn-primary" target="_blank">
	                        	Descargar Formato Evolucion
	                        </a>
	                        @endif
	                    </div>
	                </div>
                </div>
				<div class="form-group col-md-4 ">                     
                        <div class="col-md-7">
                            <button type="submit" formtarget="_blank" formaction="{{ route('auditoria_evolucion.nuevo_guardar_op')}}" class="btn btn-primary" id="new_guardar_op"><span class="glyphicon glyphicon-download" > Nueva Evolucion </span></button>
                        </div>
                </div>
        </form>
	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
	$(function () {
        $('#fecha_operacion').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });
    $('input[type="checkbox"].flat-orange').iCheck({
		checkboxClass: 'icheckbox_flat-orange',
		radioClass   : 'iradio_flat-orange'
	})


</script>
