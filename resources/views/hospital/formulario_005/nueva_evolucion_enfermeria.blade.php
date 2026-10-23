<div class="card">
	<div class="card-body">
		<br>
		<form id="form_evol_enfermeria" method="POST">
			{{ csrf_field() }}
			<div class="row" style="padding-top: 10px;">
				<input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">
				<div class="col-md-12">
					<h4>{{trans('transquirofano.1.SignosVitales')}}</h4>
					<div class="row">
						<div class="col-md-2">
							<label for="">{{trans('transquirofano.Color')}}</label>
						</div>
						<div class="col-md-4">
							<select name="colorPaleta" class="form-control" id="colorPaleta" onchange="color(this)">
								<option value="">{{trans('transquirofano.Seleccione')}}</option>
								<option value="1">{{trans('transquirofano.Rojo')}}</option>
								<option value="2">{{trans('transquirofano.Azul')}}</option>
							</select>
						</div>
						<div class="col-md-2">
							<label for="">{{trans('transquirofano.Eliminar')}}</label>
						</div>
						<div class="col-md-4">
							<button type="button" id="delete" class="btn btn-warning">{{trans('transquirofano.Eliminar')}}</button>
						</div>
					</div>
					<div class="row" style="padding-top: 10px; text-align:center;">
						<div class="col-md-7">
							<canvas id="canvas" width="700" height="450"></canvas>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-md-2">
							<label for="fRespiratori">{{trans('transquirofano.FrecuenciaRespiratoria')}}</label>
						</div>
						<div class="col-md-4">
							<!--Only number-->
							<input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" class="form-control" name="fRespiratori" id="fRespiratori">
						</div>

						<div class="col-md-2">
							<label for="pArterial">{{trans('transquirofano.PresionArterial')}}</label>
						</div>
						<div class="col-md-4">
							<!--Only number-->
							<input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" class="form-control" name="pArterial" id="pArterial">
						</div>
					</div>
				</div>

			</div>
			<br>
			<div class="row">
				<div class="col-md-12">
					<h4>{{trans('transquirofano.2.BalanceHidrico')}}</h4>
				</div>
				<div class="col-md-3">
					<span><b>{{trans('transquirofano.IngresosCC')}}</b></span>
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Parenteral')}}</b></label>
					<input type="text" name="parenteral" class="form-control input-sm" value="{{$evolucion->parenteral}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.ViaOral')}}</b></label>
					<input type="text" name="via_oral" class="form-control input-sm" value="{{$evolucion->via_oral}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Total')}}</b></label>
					<input type="text" name="total_ingresos" class="form-control input-sm" value="{{$evolucion->total_ingresos}}">
				</div>

				<div class="col-md-3">
					<span><b>{{trans('transquirofano.EliminacionesCC')}}</b></span>
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Orina')}}</b></label>
					<input type="text" name="orina" class="form-control input-sm" value="{{$evolucion->orina}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Drenaje')}}</b></label>
					<input type="text" name="drenaje" class="form-control input-sm" value="{{$evolucion->drenaje}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Otros')}}</b></label>
					<input type="text" name="otros_elimina" class="form-control input-sm" value="{{$evolucion->otros_elimina}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Total')}}</b></label>
					<input type="text" name="total_elimina" class="form-control input-sm" value="{{$evolucion->total_elimina}}">
				</div>

			</div>
			<br>
			<div class="row">
				<div class="col-md-12">
					<h4>{{trans('transquirofano.3.MedicionesyActividades')}}</h4>
				</div>

				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Aseo/Baño')}}</b></label>
					<input type="text" name="aseo_bano" class="form-control input-sm" value="{{$evolucion->aseo_bano}}">
				</div>

				<div class="col-md-3">
					<label><b>{{trans('transquirofano.PesoKg')}}</b></label>
					<input type="text" name="peso" class="form-control input-sm" value="{{$evolucion->peso}}">
				</div>

				<div class="col-md-3">
					<label><b>{{trans('transquirofano.PesoNeonato')}}</b></label>
					<input type="text" name="peso_neonato" class="form-control input-sm" value="{{$evolucion->peso_neonato}}">
				</div>

				<div class="col-md-3">
					<label><b>{{trans('transquirofano.PesoPanal')}}</b></label>
					<input type="text" name="peso_panal" class="form-control input-sm" value="{{$evolucion->peso_panal}}">
				</div>

				<div class="col-md-3">
					<label><b>{{trans('transquirofano.DietaAdministrada')}}</b></label>
					<input type="text" name="dieta" class="form-control input-sm" value="{{$evolucion->dieta}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.NumerodeComidas')}}</b></label>
					<input type="text" name="num_comidas" class="form-control input-sm" value="{{$evolucion->num_comidas}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.NumerodeMicciones')}}</b></label>
					<input type="text" name="num_micciones" class="form-control input-sm" value="{{$evolucion->num_micciones}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.NumerodeDeposiciones')}}</b></label>
					<input type="text" name="num_deposiciones" class="form-control input-sm" value="{{$evolucion->num_deposiciones}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.ActividadFisica')}}</b></label>
					<input type="text" name="actividad_fisica" class="form-control input-sm" value="{{$evolucion->actividad_fisica}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.CambiodeSonda')}}</b></label>
					<input type="text" name="cambio_sonda" class="form-control input-sm" value="{{$evolucion->cambio_sonda}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Recanalizacion')}}</b></label>
					<input type="text" name="recanalizacion" class="form-control input-sm" value="{{$evolucion->recanalizacion}}">
				</div>
				<div class="col-md-3">
					<label><b>{{trans('transquirofano.Responsable')}}</b></label>
					<input type="text" name="responsable" class="form-control input-sm" value="{{$evolucion->responsable}}">
				</div>
			</div>



	</div>
	<div class="row" style="padding-top: 10px;">
		<div class="col-md-6">
			<button class="btn btn-primary" type="button" id="guardar_diagnostico" onclick="guardar_evolucion();"> <span class="fa fa-save"> {{trans('hospitalizacion.Guardar')}}</span> </button>
		</div>
	</div>
	</form>
</div>
</div>

<script type="text/javascript">
	//CANVAS
	var GlobalColor;

	function color(color) {

		color.value == 1 ? GlobalColor = "hsl(0, 100%, 50%)" : GlobalColor = "hsl(240, 100%, 50%)";

	}
	var img = new Image();
	img.onload = setup;
	img.setAttribute('style', 'background-size:fill');
	img.src = "{{asset('/imagenes/expedienteunico/grafico.png')}}";

	function setup() {
		var canvas = document.querySelector("canvas"),
			ctx = canvas.getContext("2d"),
			lastPos, isDown = false;

		ctx.drawImage(this, 0, 0, canvas.width, canvas.height); // draw duck        
		ctx.lineCap = "butt"; // make lines prettier
		ctx.lineWidth = 1.5; //tamano del pincel
		ctx.lineJoin = "miter";
		ctx.globalCompositeOperation = "multiply"; // KEY MODE HERE

		canvas.onmousedown = function(e) {
			isDown = true;
			lastPos = getPos(e);
			ctx.strokeStyle = GlobalColor;
		};
		window.onmousemove = function(e) {
			if (!isDown) return;
			var pos = getPos(e);
			ctx.beginPath();
			ctx.moveTo(lastPos.x, lastPos.y);
			ctx.lineTo(pos.x, pos.y);
			ctx.stroke();
			lastPos = pos;
		};
		window.onmouseup = function(e) {
			isDown = false
		};

		function getPos(e) {
			var rect = canvas.getBoundingClientRect();
			return {
				x: e.clientX / 0.80 - rect.left,
				y: e.clientY / 0.80 - rect.top
			}
		}
	}

	//delete image

	document.getElementById("delete").addEventListener('click', function() {
		var canvas = document.querySelector('canvas');
		var context = canvas.getContext('2d');
		context.clearRect(0, 0, canvas.width, canvas.height);
		var img = new Image;
		img.onload = setup;
		img.src = "{{asset('/imagenes/expedienteunico/grafico.png')}}";
	});




	function guardar_evolucion() {


		var canvas = document.getElementById('canvas');
		var blob = canvas.toDataURL();

		canvas.toBlob(function(blob) {
			const formData = new FormData();
			formData.append('my-file', blob, 'filename.png');
			$.ajax({
				type: 'post',
				url: "{{route('tipoemergencia.guardarimagen',['id_evol' => $evolucion->id])}}",
				headers: {
					'X-CSRF-TOKEN': $('input[name=_token]').val()
				},
				processData: false,
				contentType: false,
				data: formData,
				success: function(data) {
					$.ajax({
						type: 'post',
						url: "{{route('formulario005.guardar_evolucion_enfermeria',['id_evol' => $evolucion->id])}}",
						headers: {
							'X-CSRF-TOKEN': $('input[name=_token]').val()
						},
						datatype: 'json',
						data: $("#form_evol_enfermeria").serialize(),
						success: function(data) {
							return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
						},
						error: function(data) {}
					})
				},
				error: function(data) {}
			})
		});
	}
</script>