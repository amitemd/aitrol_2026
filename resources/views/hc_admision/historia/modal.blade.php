<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">FORMATO DE OXIGENO</h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
		<form class="form-vertical" role="form" method="POST" action="{{ route('oxigeno.oxigenExcel') }}" target="_blank">
			{{ csrf_field() }}
			<input type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}">
			<input type="hidden" class="form-control" name="agenda" value="{{$agenda->id}}">
			<input type="hidden" id="isCpre" value="{{$isCpre}}">
                <div class="box-body col-md-12">
	                <!--id_procedimiento_completo-->
	              
					<div class="form-group col-md-6{{ $errors->has('fecha_oxigeno') ? ' has-error' : '' }}">
	                    <label for="fecha_operacion" class="col-md-6 control-label">Fecha</label>

	                    <div class="col-md-6">
	                        <input class="form-control" type="text" name="fecha_oxigeno" id="fecha_oxigeno" value="{{$fecha}}" required="required"> 
	                        @if ($errors->has('fecha_operacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_operacion') }}</strong>
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
                    <div class="form-group col-md-6{{ $errors->has('hora_fin') ? ' has-error' : '' }}">
	                    <label for="hora_ini" class="col-md-6 control-label">Hora Fin</label>

	                    <div class="col-md-6">
	                        <input class="form-control" type="time" name="hora_fin" id="hora_fin" value="{{$hora_fin}}" required="required"> 
	                        @if ($errors->has('hora_fin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hora_fin') }}</strong>
                            </span>
	                        @endif
	                    </div>
					</div>
					<!--
					<div class="form-group col-md-6 {{$errors->has('')}}">
						<label class="col-md-6 control-label" for="codigo">Código</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="codigo" id="codigo" placeholder="Ingrese código">
						</div>
					</div>-->
					<div class="form-group col-md-6 {{$errors->has('litros')}}">
						<label class="col-md-6 control-label" for="codigo">Litros</label>
						<div class="col-md-6">
						<input type="number" class="form-control" value="{{ $agenda->id_seguro == 5 ? '5' : '' }}"
							name="litros" id="litros" required placeholder="Ingrese litros">
					</div>
				</div>

				<div class="form-group col-md-6">
					<label class="col-md-6 control-label" for="anestesiologo">@lang('admision.anestesiologo')</label>
					<div class="col-md-6">
						<select name="anestesiologo" id="anestesiologo" class="form-control select2" required>
							<option value="">--Seleccione--</option>
							@foreach($anestesiologos as $value)

							<option value="{{$value->id}}">{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}}</option>

							@endforeach
						</select>
					</div>
				</div>



				<div class="row" style="margin-left:0px !important">
					<div class="form-group col-md-6">
						<label class="col-md-6 control-label" for="sevofluorano">
							@lang('admision.sevofluorano')
						</label>
						<div class="col-md-6">
							<input type="checkbox" name="sevofluorano" id="sevofluorano">
						</div>
					</div>
				</div>

				<div id="show" style="display: none;">
					<div class="form-group col-md-6">
						<label class="col-md-6 control-label" for="porcentaje">@lang('admision.sevofluorano') %</label>
						<div class="col-md-6">
							<input type="text" name="porcentaje" id="porcentaje" class="form-control" min="0" placeholder="12%">
						</div>
					</div>
				</div>

	                <div class="form-group col-md-12" style="text-align: center;">
	                  
					<!-- <button type="button" class="btn btn-primary" formtarget="_blank">
						<i class="fa fa-download"></i> @lang('laboratorio.descargar')
					</button> -->
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-download"></i> @lang('laboratorio.descargar')
					</button>
					<button type="submit" class="btn btn-danger" name="pdf" value="pdf">
						<i class="fa fa-download"></i> Pdf
					</button>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		let val = $("#isCpre").val();
		if (val) {
			$("#sevofluorano").prop("checked", true).trigger("change");

		}
		$('.select2').select2({
			width: "100%"
		});


		$('#porcentaje').on('input', function() {
			let valor = $(this).val();

			valor = valor.replace(/[^0-9.]/g, '');

			const primerPunto = valor.indexOf('.');
			if (primerPunto !== -1) {
				valor = valor.substring(0, primerPunto + 1) + valor.substring(primerPunto + 1).replace(/\./g, '');
			}

			if (valor.startsWith('.')) {
				valor = '0' + valor;
			}

			$(this).val(valor);
		});


		$('#fecha_oxigeno').datetimepicker({
			format: 'YYYY/MM/DD'
		});
	});


	$('#porcentaje').on('keydown', function(e) {
		if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E') {
			e.preventDefault();
		}
	});

	$('#porcentaje').on('blur', function() {
		let valor = $(this).val();

		if (valor === '') {
			return;
		}

		if (parseFloat(valor) < 0) {
			$(this).val('0');
		}
	});

	$('input[type="checkbox"].flat-orange').iCheck({
		checkboxClass: 'icheckbox_flat-orange',
		radioClass: 'iradio_flat-orange'
	})

	$('#sevofluorano').on('change', function() {
		if ($(this).is(':checked')) {
			$("#show").show();
		} else {
			$("#show").hide();
		}
	});

	

</script>