<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<style>
	.ui-autocomplete {
		overflow-x: hidden;
		max-height: 200px;
		width: 1px;
		position: absolute;
		top: 100%;
		left: 0;
		z-index: 1000;
		float: left;
		display: none;
		min-width: 160px;
		_width: 160px;
		padding: 4px 0;
		margin: 2px 0 0 0;
		list-style: none;
		background-color: #fff;
		border-color: #ccc;
		border-color: rgba(0, 0, 0, 0.2);
		border-style: solid;
		border-width: 1px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		-webkit-background-clip: padding-box;
		-moz-background-clip: padding;
		background-clip: padding-box;
		*border-right-width: 2px;
		*border-bottom-width: 2px;
	}

	.nursing-discharge__header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 14px;
		width: 100%;
		min-height: 68px;
		padding: 12px 18px;
	}

	.nursing-discharge__title {
		flex: 0 0 auto;
		margin: 0;
		color: #fff !important;
		font-size: 16px;
		font-weight: 700;
		line-height: 1.25;
	}

	.nursing-discharge__actions {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		flex: 1 1 auto;
		flex-wrap: wrap;
		gap: 7px;
	}

	.nursing-discharge__action {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-height: 36px;
		margin: 0 !important;
		padding: 8px 12px;
		border-radius: 8px;
		font-size: 12px;
		font-weight: 700;
		line-height: 1;
		white-space: nowrap;
	}

	.nursing-discharge__action i {
		margin-right: 5px;
	}

	.nursing-discharge__action.btn-default {
		border-color: rgba(255, 255, 255, .35);
		background: rgba(255, 255, 255, .15);
		color: #fff;
	}

	.nursing-discharge__item-header {
		display: grid;
		align-items: center;
		grid-template-columns: minmax(240px, 1fr) minmax(210px, 1fr) 42px;
		gap: 14px;
		width: 100%;
		min-height: 58px;
		padding: 9px 18px;
	}

	.nursing-discharge__item-date,
	.nursing-discharge__doctor {
		min-width: 0;
		color: #fff;
		font-size: 13px;
		line-height: 1.3;
	}

	.nursing-discharge__item-date {
		font-weight: 700;
	}

	.nursing-discharge__doctor {
		text-align: center;
	}

	.nursing-discharge__doctor-name {
		font-weight: 700;
	}

	.nursing-discharge__toggle {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 40px;
		height: 40px;
		margin: 0;
		padding: 0;
		border: 1px solid rgba(255, 255, 255, .18) !important;
		border-radius: 10px;
		background: rgba(0, 74, 145, .58) !important;
	}

	@media (max-width: 991px) {
		.nursing-discharge__header {
			align-items: flex-start;
			flex-direction: column;
		}

		.nursing-discharge__actions {
			justify-content: flex-start;
			width: 100%;
		}

		.nursing-discharge__item-header {
			grid-template-columns: minmax(210px, 1fr) minmax(180px, 1fr) 42px;
		}
	}

	@media (max-width: 575px) {
		.nursing-discharge__header {
			padding: 12px;
		}

		.nursing-discharge__action {
			flex: 1 1 calc(50% - 7px);
		}

		.nursing-discharge__item-header {
			grid-template-columns: 1fr 42px;
			gap: 7px 12px;
			padding: 10px 12px;
		}

		.nursing-discharge__doctor {
			grid-column: 1 / -1;
			grid-row: 2;
			text-align: left;
		}

		.nursing-discharge__toggle-wrap {
			grid-column: 2;
			grid-row: 1;
		}
	}
</style>
<div class="card">
	<div id="alerta_datos_vh" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<span id="msn_guardado_vh"></span> Guardada Correctamente
	</div>
	<div class="card-header bg bg-primary nursing-discharge__header">
		<h4 class="nursing-discharge__title">{{trans('paso2.DESCARGODEENFERMERIA')}}</h4>
		<nav class="nursing-discharge__actions" aria-label="Acciones clínicas del episodio">
			<a target="_blank" href="{{route('clinical_safety.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-warning btn-sm nursing-discharge__action"><i class="fa fa-shield"></i> Seguridad clínica</a>
			<a target="_blank" href="{{route('clinical_access.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-default btn-sm nursing-discharge__action"><i class="fa fa-unlock"></i> Acceso excepcional</a>
			<a target="_blank" href="{{route('clinical_consults.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-info btn-sm nursing-discharge__action"><i class="fa fa-user-md"></i> Interconsultas</a>
			<a target="_blank" href="{{route('clinical_orders.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-primary btn-sm nursing-discharge__action"><i class="fa fa-list-alt"></i> Órdenes y resultados</a>
		</nav>
	</div>
	<div class="card-body" style="padding: 0;">
		<br>
		@forelse($recetas as $receta)
		@php
		$xdoctor = $receta->doctor;
		if (is_null($xdoctor) && !is_null($receta->historia)) {
			$xdoctor = $receta->historia->doctor_1;
		}
		if (is_null($xdoctor)) {
			$xdoctor = $receta->creador;
		}
		$fecha = substr($receta->created_at,0,10);
		@endphp

		<div class="col-md-12" style="padding: 0;">
			<div class="card-header bg bg-primary colorbasic nursing-discharge__item-header">
				<div class="nursing-discharge__item-date">
					@if(!is_null($fecha))
					@php
					$dia = Date('N',strtotime($fecha));
					$mes = Date('n',strtotime($fecha));
					@endphp

					{{Sis_medico\Utilidades::getDia($dia)}}
					{{substr($fecha,8,2)}} de
					{{Sis_medico\Utilidades::getMes($mes)}}
					del {{substr($fecha,0,4)}}
					@endif
				</div>
				<div class="nursing-discharge__doctor">
					Dr(a):
					@if(!is_null($xdoctor))
					<span class="nursing-discharge__doctor-name">{{$xdoctor->nombre1}} {{$xdoctor->nombre2}} {{$xdoctor->apellido1}} {{$xdoctor->apellido2}}</span>
					@else
					<span class="nursing-discharge__doctor-name">No registrado</span>
					@endif
				</div>
				<div class="nursing-discharge__toggle-wrap">

					<button id="min{{$receta->id}}" type="button" class="btn btn-primary nursing-discharge__toggle" onclick="ocultar_orden('{{$receta->id}}')" style="display: none">
						<i class="fa fa-minus"></i>
					</button>

					<button id="plus{{$receta->id}}" type="button" class="btn btn-primary nursing-discharge__toggle" onclick="ver_receta('{{$receta->id}}','{{$solicitud->id}}')">
						<i class="fa fa-plus"></i>
					</button>
				</div>
			</div>
			<div class="card-body" id="receta{{$receta->id}}" style="padding: 0px;">

			</div>
		</div>
		<br>
		@empty
		<div class="alert alert-warning mx-3">
			<i class="fa fa-exclamation-triangle"></i>
			No se encontraron recetas asociadas a una historia clínica de esta hospitalización.
		</div>
		@endforelse

	</div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
	
	function ver_receta(id, id_solicitud) {
		
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/receta/descargo/enfermeria')}}/" + id +'/'+id_solicitud,
			datatype: "html",
			success: function(datahtml) {

				$("#receta" + id).html(datahtml);

			},
			error: function() {
				alert('error al cargar');
			}
		});
		$("#plus" + id).hide();
		$("#min" + id).show();
	}

	function ocultar_orden(id) {

		$("#receta" + id).empty();
		$("#min" + id).hide();
		$("#plus" + id).show();


	}

	function agregar_lab_publico() {
		$.ajax({
			async: true,
			type: "POST",
			url: "{{route('hospital.decimo_laboratorio_crear_pb',[ 'id' => $solicitud->id ])}}",
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml) {

				$("#content").html(datahtml);

			},
			error: function() {
				alert('error al cargar');
			}
		});

	}

	function agregar_lab_privado() {

		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('decimopaso.laboratorio_orden_crear_part',[ 'id' => $solicitud->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml) {

				$("#content").html(datahtml);

			},
			error: function() {
				alert('error al cargar');
			}
		});


	}

	function editar_labs(id) {
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/editar/pub')}}/" + id,
			data: "",
			datatype: "html",
			success: function(datahtml) {

				$("#labs_detalle" + id).html(datahtml);

			},
			error: function() {
				alert('error al cargar');
			}
		});
		$("#plus" + id).hide();
		$("#min" + id).show();
	}

	function enviar(recetaId) {
		var nombre = $.trim($('#codigo' + recetaId).val());
		var id_bodega = $('#id_bodega' + recetaId).val();
		var procedimiento = $('#procedimientoDescargo' + recetaId).val();
		var button = $('#agregarDescargo' + recetaId);
		var message = $('#mensajeDescargo' + recetaId);
		message.empty();
		if (!nombre) {
			Swal.fire({icon: 'warning', title: 'Seleccione un medicamento', text: 'Elija un medicamento prescrito o búsquelo por nombre o código.'});
			$('#nombre' + recetaId).focus();
			return;
		}
		if (!id_bodega) {
			Swal.fire({icon: 'warning', title: 'Bodega principal no configurada', text: 'Configure la bodega principal de la empresa y su vínculo con inventario.'});
			return;
		}
		if (!procedimiento) {
			Swal.fire({icon: 'error', title: 'Sin procedimiento', text: 'No se encontró un procedimiento asociado a esta receta.'});
			return;
		}
		button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registrando...');
		$.ajax({
			type: 'POST',
			url: "{{url('enfermeria/serie_enfermeroget')}}/" + encodeURIComponent(nombre) + "/" + encodeURIComponent(procedimiento),
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: 'json',
			data: {
				'nombre': nombre,
				'id_bodega': id_bodega,
			},
			success: function(data) {
				if (data !== 'ok') {
					var detail = data === 'caducado'
						? 'No se puede descargar el medicamento porque el lote disponible está caducado.'
						: String(data || 'No fue posible registrar el descargo.');
					Swal.fire({title: 'No se registró el descargo', text: detail, icon: 'warning'});
				} else {
					var descripcion = $('#nombre' + recetaId).val();
					$("#msn_guardado_vh").text(descripcion);
					$("#alerta_datos_vh").fadeIn(1000);
					$("#alerta_datos_vh").fadeOut(4000);
					message.html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Descargo registrado correctamente.</div>');
					$('#nombre' + recetaId).val('');
					$('#codigo' + recetaId).val('');
					listado_prod(recetaId);
				}
			},
			error: function(xhr) {
				var response = xhr.responseJSON || {};
				Swal.fire({icon: 'error', title: 'No fue posible registrar el descargo', text: response.message || response.msn || 'Inténtelo nuevamente.'});
			},
			complete: function() {
				button.prop('disabled', false).html('<span class="glyphicon glyphicon-plus"> Registrar descargo</span>');
			}
		});
	}

	function listado_prod(recetaId) {
		var procedimiento = $('#procedimientoDescargo' + recetaId).val();
		if (!procedimiento) {
			return;
		}

		$.ajax({
			type: "GET",
			url: "{{url('enfermeria/listado_prod')}}/" + procedimiento,
			datatype: "html",
			success: function(datahtml) {
				$("#listado" + recetaId).html(datahtml);

			},
			error: function() {
				$('#mensajeDescargo' + recetaId).html('<div class="alert alert-warning">No se pudo actualizar el listado de descargos.</div>');
			}
		});
	}

	
</script>
