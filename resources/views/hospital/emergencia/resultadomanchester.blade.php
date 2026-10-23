@extends('layouts.app-template-h')
@section('content')


<?php
date_default_timezone_set('America/Guayaquil');
$fecha_actual = date("Y-m-d H:i:s");
?>


<div class="content">

	<div class="row">
		<!-- 1.- Registro de Primera Admisión -->
		<div class="col-md-12">
			<div class="card card-primary">
				<div class="card-header with-border">
					<label>{{trans('emergencia.Registrodeemergencia')}}</label>
					<div class="card-tools pull-right">
						<button type="button" onclick="location.href='{{route('hospital.emergencia')}}'" class="btn btn-danger btn-sm btn-block">{{trans('emergencia.Regresar')}}</button>
					</div>
				</div>
				<!-- /.card-header -->
				<form id="form_manchester">
					{{ csrf_field() }}
					<div class="card-body">

						<div class="row">

							<div class="col-md-12">
								<div class="form-row">
									<input type="hidden" name="id_manchester" value="{{$id}}">
									<div class="form-group col-md-4">
										<label class="col-form-label-sm">{{trans('emergencia.ApellidosNombres')}}</label>
										<input type="text" class="form-control form-control-sm nombre" name="nombre" id="id_paciente" value="{{$datos->apellido1}} {{$datos->apellido2}} {{$datos->nombre1}} {{$datos->nombre2}}" readonly>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.CI')}}</label>
										<input type="text" class="form-control form-control-sm" id="paciente" name="paciente" value="{{ $datos->id_paciente}}" readonly>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-9">
										<label class="col-form-label-sm">{{trans('emergencia.MotivodeConsulta')}}</label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm validar" name="mot_consulta" id="mot_consulta" value="{{ $datos->motivo_consulta}} ">
										</div>
									</div>
									<!--div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('emergencia.AdmisionID')}}</label>
											<input type="text" class="form-control form-control-sm" id="admision" readonly name="admision"value="{{ $datos->id_ho_solicitud}}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('emergencia.Nrevision')}}</label>
											<input type="text" class="form-control form-control-sm" id="num_rev" name="num_rev" value="{{ $datos->num_revision}}"> 
										</div-->


								</div>
								<div class="form-row">

									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.TipodeEmergencia')}}</label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm validar" name="tipos_emergencia" id="tipos_emergencia" required>
												<option value="0">{{trans('emergencia.Seleccione')}}</option>
												@foreach($tipos_emergencia as $value)
												<option @if($value->id == $datos->tipo_emergencia) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.EmbarazoPuerperio:')}}</label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="embarazo_p" id="embarazo_p">
												<option>{{trans('emergencia.Seleccione')}}</option>
												<option @if($datos->embarazo_puerperio == '1') selected @endif value="1">Si</option>
												<option @if($datos->embarazo_puerperio == '0') selected @endif value="0">No</option>
											</select>
										</div>

									</div>

									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.PresionArterialSistolica')}}</label>
										<input type="text" class="form-control form-control-sm" id="presion_art_sis" name="presion_art_sis" value="{{ $datos->presion_sistolica}}">
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.PresionArterialDiastolica')}}</label>
										<input type="text" class="form-control form-control-sm" id="presion_art_dias" name="presion_art_dias" value="{{ $datos->presion_diastolica}}">
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.FrecuenciaCardiaca')}}</label>
										<input type="text" class="form-control form-control-sm" name="frec_cardiaca" id="frec_cardiaca" value="{{ $datos->frec_cardiaca}}">

									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.FrecuenciaRespiratoria')}}</label>
										<input type="text" class="form-control form-control-sm" id="frec_resp" name="frec_resp" value="{{ $datos->frec_resp}}">
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.TemperaturaC')}}</label>
										<input type="text" class="form-control form-control-sm" id="temperatura" name="temperatura" value="{{ $datos->temp}}">

									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.Tallacm')}}</label>
										<input type="text" class="form-control form-control-sm" id="talla" name="talla" value="{{ $datos->talla}}">

									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.PesoKg')}}</label>
										<input type="text" class="form-control form-control-sm" id="peso" name="peso" value="{{ $datos->peso}}">

									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.RespuestaOcular')}}</label>
										<select class="form-control form-control-sm validar" id="resp_ocular" name="resp_ocular">
											<option value="">{{trans('emergencia.Seleccione')}}</option>
											@foreach($ocular as $oc)
											<option @if($datos->resp_ocular == $oc->prioridad) selected @endif value="{{$oc->prioridad}}">{{$oc->nombre}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.RespuestaVerbal')}}</label>
										<select class="form-control form-control-sm validar" id="resp_verbal" name="resp_verbal">
											<option value="">{{trans('emergencia.Seleccione')}}</option>
											@foreach($verbal as $verb)
											<option @if($datos->resp_verbal == $verb->prioridad) selected @endif value="{{$verb->prioridad}}">{{$verb->nombre}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.RespuestaMotora')}}</label>
										<select class="form-control form-control-sm validar" id="resp_motora" name="resp_motora">
											<option value="">{{trans('emergencia.Seleccione')}}</option>
											@foreach($motora as $mot)
											<option @if($mot->prioridad == $datos->resp_motora) selected @endif value="{{$mot->prioridad}}">{{$mot->nombre}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-md-4">
										<label class="col-form-label-sm">{{trans('emergencia.ReaccionPupilar')}}</label>
										<div class="col-sm-12">
											<!-- <input type="text" class="form-control form-control-sm" id="reac_pupilar" name="reac_pupilar" value="{{ $datos->reaccion_pupilar}}"> -->
											<select class="form-control form-control-sm" id="reac_pupilar" name="reac_pupilar">
												<option @if($datos->reaccion_pupilar == 'Si') selected @endif value="Si">Si</option>
												<option @if($datos->reaccion_pupilar == 'No') selected @endif value="No">No</option>
											</select>
										</div>
									</div>

									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.TotalLlenadoCapilar')}}</label>
										<input type="text" class="form-control form-control-sm" id="total_capilar" name="total_capilar" value="{{ $datos->llenado_capilar}}">
									</div>

									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.SaturaciondeOxigeno')}}</label>
										<input type="text" class="form-control form-control-sm" id="satura_oxigeno" name="satura_oxigeno" value="{{ $datos->sat_oxigeno}}">
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm">{{trans('emergencia.EstadodeConciencia')}}</label>
										<div class="col-sm-10">
											<select class="form-control form-control-sm validar" name="est_conciencia" id="est_conciencia">
												<option value="">{{trans('emergencia.Seleccione')}}</option>
												<option @if($datos->estado_conciencia == '1') selected @endif value="1">Consiente</option>
												<option @if($datos->estado_conciencia == '0') selected @endif value="0">Inconsiente</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-3">
										<label class="col-form-label-sm">{{trans('emergencia.Prioridad')}}</label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm validar" name="prioridad" id="prioridad" required>
												<option value="0">{{trans('emergencia.Seleccione')}}</option>
												@foreach($prioridad as $value)
												<option style="background-color: {{$value->color}}" @if($value->id == $datos->prioridad) selected @endif value="{{$value->prioridad}}">{{$value->nombre}}</option>
												@endforeach
											</select>
										</div>
									</div>

									<!-- /.col -->

								</div>
								<!-- /.row -->
							</div>
							<!-- ./card-body -->
							<div class="card-footer" style="text-align: center">
								<div class="row">
									<!--label  class="col-sm-10 col-form-label">ACTUALIZAR LOS SIGNOS VITALES DEL PACIENTE</label-->
									<button type="button" onclick="update_manchester();" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i>{{trans('emergencia.Actualizar')}}</button>
								</div>
								<!-- /.row -->
							</div>
							<!-- /.row -->
						</div>
						<!-- /.card-footer -->
				</form>
			</div>
			<!-- /.card -->
		</div>
		<!-- /.col -->
	</div>

</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
	function update_manchester() {
		
		if (!validarDatos()) {
			$.ajax({
				type: 'post',
				url: "{{ route('manchester.update') }}",
				headers: {
					'X-CSRF-TOKEN': $('input[name=_token]').val()
				},
				datatype: 'json',
				data: $("#form_manchester").serialize(),
				success: function(data) {
					alertas(data.respuesta, `${data.respuesta.toUpperCase()}...`, data.msj)
					if (data.respuesta == 'success') {
						setTimeout(function() {
							window.location.href = "{{url('hospital/modulos/index')}}/"+data.paso;
						}, 1500)
					}
				},
				error: function(data) {
					console.log(data);
				}
			});
		}
	}
</script>




@endsection