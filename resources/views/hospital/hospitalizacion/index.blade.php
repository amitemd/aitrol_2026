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
</style>
<div class="card">
	<div id="alerta_datos_vh" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<span id="msn_guardado_vh"></span> Guardada Correctamente
	</div>
	<div class="card-header bg bg-primary">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-1">
				</div>
				<div class="col-md-7">
					<label style="color: white;" class="control_label">{{trans('paso2.DESCARGODEENFERMERIA')}}</label>
				</div>
			</div>


		</div>
	</div>
	<div class="card-body" style="padding: 0;">
		<br>
		@foreach($agenda as $value)
		@php
		
		$recetas = $value->historia_clinica->recetas;
		
		$hcid = $value->historia_clinica->hcid;
		
		$hc_proc = Sis_medico\hc_procedimientos::where('id_hc', $hcid)->first();
		
		@endphp
		@foreach($recetas as $receta)
		@php
		$xdoctor = null;
		if($receta->id_doctor_examinador != ""){
		$xdoctor = $receta->doctor;
		}
		$fecha = substr($receta->created_at,0,10);
		@endphp

		<div class="col-md-12" style="padding: 0;">
			<div class="card-header bg bg-primary colorbasic">
				
				<div class="col-md-5">
					@if(!is_null($fecha))
					@php
					$dia = Date('N',strtotime($fecha));
					$mes = Date('n',strtotime($fecha));
					@endphp

					<b>
						{{Sis_medico\Utilidades::getDia($dia)}}
						{{substr($fecha,8,2)}} de
						{{Sis_medico\Utilidades::getMes($mes)}}
						del {{substr($fecha,0,4)}}</b>
					@endif
				</div>
				<div class="col-md-4">
					Dr (a): @if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif
				</div>
				<div class="col-md-2">

					<button id="min{{$receta->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$receta->id}}')" style="display: none">
						<i class="fa fa-minus"></i>
					</button>

					<button id="plus{{$receta->id}}" type="button" class="btn btn-primary" onclick="ver_receta('{{$receta->id}}','{{$solicitud->id}}')">
						<i class="fa fa-plus"></i>
					</button>
				</div>
			</div>
			<div class="card-body" id="receta{{$receta->id}}" style="padding: 0px;">

			</div>
		</div>
		<br>
		@endforeach

		@endforeach

	</div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
	
	function ver_receta(id, id_solicitud) {
		
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/receta/descargo/enfermeria')}}/" + id +'/'+id_solicitud,
			data: "",
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

		$("#labs_detalle" + id).html("<br>");
		$("#min" + id).hide();
		$("#plus" + id).show();


	}

	function agregar_lab_publico() {
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hospital.decimo_laboratorio_crear_pb',[ 'id' => $solicitud->id ])}}",
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

	function enviar() {
		var nombre = $('#codigo').val();
		var id_bodega = document.getElementById('id_bodega').value;
		//console.log(nombre);
		$.ajax({
			type: 'GET',
			url: "{{url('enfermeria/serie_enfermeroget')}}/" + nombre + "/{{$hc_proc->id}}",
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: 'json',
			data: {
				'nombre': nombre,
				'id_bodega': id_bodega,
			},
			success: function(data) {
				console.log(data);
				if (data == 'No se encontraron resultados') {
					$('#agregar_respuesta' + nombre).html('No se encontraron resultados');
					swal({
						title: "No se encontraron resultados",
						icon: "warning",
						type: 'error',
						buttons: true,
					});
					$("#recibir" + nombre).focus();
				} else if (data == 'caducado') {
					swal({
						title: "No se puede usar el medicamento, se encuentra caducado",
						icon: "warning",
						type: 'error',
						buttons: true,
					});
				} else if (data != 'ok') {

					swal.fire({
						title: data,
						icon: "warning",
						type: 'error',

					});
				} else {

					$("#msn_guardado_vh").text($("#nombre").val());
					$("#alerta_datos_vh").fadeIn(1000);
					$("#alerta_datos_vh").fadeOut(4000);
					listado_prod();
				}
			},
			error: function(data) {
				console.log(data);
			}
		});
	}

	function listado_prod() {

		$.ajax({
			type: "GET",
			url: "{{route('enfermeria.listado_prod',['id_proc'=>$hc_proc->id])}}",
			datatype: "html",
			success: function(datahtml) {
				//console.log(datahtml);
				$("#listado").html(datahtml);

			},
			error: function() {
				alert('error al cargar');
			}
		});
	}

	
</script>