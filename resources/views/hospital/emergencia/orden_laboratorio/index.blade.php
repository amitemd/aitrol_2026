<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
@include('hospital.emergencia.partials.clinical-step-styles')

<style>
	.laboratory-order-card .laboratory-header-actions {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-left: auto;
	}
	.laboratory-order-card .laboratory-action {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 7px;
		min-height: 38px;
		padding: 8px 13px;
		border-radius: 9px;
		font-size: 12px;
		font-weight: 750;
		box-shadow: none;
	}
	.laboratory-order-card .laboratory-action--public {
		border: 0;
		background: #087fab;
		color: #fff;
	}
	.laboratory-order-card .laboratory-action--private {
		border: 1px solid #9fc9d8;
		background: #fff;
		color: #087fab;
	}
	.laboratory-order-card .laboratory-empty-state {
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		min-height: 280px;
		padding: 36px 22px;
		text-align: center;
	}
	.laboratory-order-card .laboratory-feedback {
		display: none;
		margin: 14px 20px 0;
		padding: 12px 14px;
		border: 1px solid #f0c5c5;
		border-radius: 9px;
		background: #fff3f3;
		color: #9f2f2f;
		font-size: 13px;
		line-height: 1.45;
	}
	.laboratory-order-card .laboratory-empty-icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 62px;
		height: 62px;
		margin-bottom: 14px;
		border-radius: 18px;
		background: #e2f5f6;
		color: #087fab;
		font-size: 25px;
	}
	.laboratory-order-card .laboratory-empty-state h5 {
		margin: 0 0 6px;
		color: #0b3a5b;
		font-size: 17px;
		font-weight: 800;
	}
	.laboratory-order-card .laboratory-empty-state p {
		max-width: 460px;
		margin: 0;
		color: #688093;
		font-size: 13px;
		line-height: 1.55;
	}
	.laboratory-order-card .laboratory-order-item {
		margin: 0 20px 14px;
		overflow: hidden;
		border: 1px solid #d7e6ee;
		border-radius: 11px;
		background: #fff;
	}
	@media (max-width: 760px) {
		.laboratory-order-card .clinical-step-header { align-items: flex-start; flex-wrap: wrap; }
		.laboratory-order-card .laboratory-header-actions { width: 100%; margin-left: 0; }
		.laboratory-order-card .laboratory-action { flex: 1 1 150px; }
	}
</style>

<div class="card clinical-step-card laboratory-order-card">
	<div class="clinical-step-header">
		<span class="clinical-step-icon"><i class="fa fa-flask"></i></span>
		<div>
			<h4 class="clinical-step-title">{{trans('emergencia.HISTORIALDEORDENESDELABORATORIO')}}</h4>
			<p class="clinical-step-subtitle">Consulta las solicitudes del episodio o crea una nueva orden para el paciente.</p>
		</div>
		<div class="laboratory-header-actions">
			<button id="create-public-lab-order" type="button" class="btn laboratory-action laboratory-action--public" onclick="agregar_lab_publico(this);">
				<i class="fa fa-plus"></i> Nueva orden {{trans('emergencia.Publico')}}
			</button>
			<button id="create-private-lab-order" type="button" class="btn laboratory-action laboratory-action--private" onclick="agregar_lab_privado(this);">
				<i class="fa fa-plus"></i> Nueva orden {{trans('emergencia.Privado')}}
			</button>
		</div>
	</div>
	<div class="card-body clinical-step-body" style="padding-right: 0; padding-left: 0;">
		<div id="laboratory-order-feedback" class="laboratory-feedback" role="alert"></div>
		@if($ordenes->isEmpty())
			<div class="laboratory-empty-state" role="status">
				<span class="laboratory-empty-icon"><i class="fa fa-flask"></i></span>
				<h5>No existen órdenes de laboratorio</h5>
				<p>Este episodio todavía no tiene solicitudes. Usa una de las acciones superiores para crear una orden pública o privada.</p>
			</div>
		@endif
		@foreach($ordenes as $orden)

	   	    @php
				if(!is_null($orden->fecha_orden)){
	    		  	$fecha_r =  Date('Y-m-d',strtotime($orden->fecha_orden));
	    	    }else{
					$fecha_r =  Date('Y-m-d',strtotime($orden->created_at));
	    		}

				$xdoctor = null;
				if($orden->id_doctor_ieced != ""){
	               $xdoctor = $orden->doctor;
	            }

	            $fecha = substr($orden->fecha_orden,0,10);

			@endphp

			<div id="orden_lab_div{{$orden->id}}" class="laboratory-order-item" >
				<div class="card-header bg bg-primary colorbasic" >
					<div class="col-md-1">
	        			<button id="edit{{$orden->id}}" type="button" class="btn btn-warning btn-xs" onclick="editar_labs('{{$orden->id}}')">
                        	<i class="fa fa-edit"></i>
                        </button>
	        		</div>
	        		<div class="col-md-5">
	        			@if(!is_null($orden->fecha_orden))
                            @php
	                         $dia =  Date('N',strtotime($orden->fecha_orden));
	                         $mes =  Date('n',strtotime($orden->fecha_orden));
	                        @endphp

	                        <b>
	                        @if($dia == '1') Lunes
                             @elseif($dia == '2') Martes
                             @elseif($dia == '3') Miércoles
                             @elseif($dia == '4') Jueves
                             @elseif($dia == '5') Viernes
                             @elseif($dia == '6') Sábado
                             @elseif($dia == '7') Domingo
                            @endif
                             {{substr($orden->fecha_orden,8,2)}} de
                            @if($mes == '1') Enero
                                 @elseif($mes == '2') Febrero
                                 @elseif($mes == '3') Marzo
                                 @elseif($mes == '4') Abril
                                 @elseif($mes == '5') Mayo
                                 @elseif($mes == '6') Junio
                                 @elseif($mes == '7') Julio
                                 @elseif($mes == '8') Agosto
                                 @elseif($mes == '9') Septiembre
                                 @elseif($mes == '10') Octubre
                                 @elseif($mes == '11') Noviembre
                                 @elseif($mes == '12') Diciembre
                            @endif
                            del {{substr($orden->fecha_orden,0,4)}}</b>
                        @endif
	        		</div>
	        		<div class="col-md-4">
					Dr. (a): @if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @else Sin médico asignado @endif
	        		</div>
	        		<div class="col-md-2" >
					<div class="row">
					<div class="col-md-5">
						<button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$orden->id}}')" style="display: none">
                        	<i class="fa fa-minus"></i>
                        </button>
						<button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden('{{$orden->id}}')">
                        	<i class="fa fa-plus"></i>
                        </button>
					</div>
					@if(!is_null($orden->estado))
						@if($orden->estado == -1)
						<div class="col-md-5">
							<button id="delete{{$orden->id}}" type="button" class="btn btn-danger btn-xs" onclick="eliminar_labs('{{$orden->id}}')">
								<i class="fa fa-trash"></i>
							</button>
						</div>
						@endif
					@endif
					</div>
                        <!-- <button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$orden->id}}')" style="display: none">
                        	<i class="fa fa-minus"></i>
                        </button>
                        <button id="delete{{$orden->id}}" type="button" class="btn btn-danger btn-xs" onclick="eliminar_labs('{{$orden->id}}')">
                        	<i class="fa fa-trash"></i>
                        </button>
                        <button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden('{{$orden->id}}')">
                        	<i class="fa fa-plus"></i>
                        </button> -->
	        		</div>
		        </div>
		        <div class="card-body" id="labs_detalle{{$orden->id}}" style="padding: 0px;">
		        </div>
                @if((int) $orden->estado === -1)
                    <div style="padding:12px;display:flex;gap:10px;flex-wrap:wrap">
                        <button type="button" class="btn btn-primary" onclick="editar_labs('{{ $orden->id }}')">Editar exámenes</button>
                        <form method="post" action="{{ route('hospital.laboratory.confirm', ['id_solicitud' => $solicitud->id, 'order' => $orden->id]) }}" style="margin:0">
                            @csrf<button type="submit" class="btn btn-success">Confirmar solicitud</button>
                        </form>
                    </div>
                @endif
		    </div>
		    <br>

		@endforeach
	</div>
</div>


<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
	@if($id_editar != null)
		editar_labs('{{$id_editar}}');
	@endif
	function ver_orden(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle')}}/"+id,
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml){

			    $("#labs_detalle"+id).html(datahtml);

			},
			error:  function(xhr){
				laboratory_order_error(xhr);
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();
	}

	function ocultar_orden(id){

		$("#labs_detalle"+id).html("<br>");
		$("#min"+id).hide();
		$("#plus"+id).show();


	}

	function laboratory_order_error(xhr){
		var message = xhr && xhr.responseText ? xhr.responseText : 'No se pudo crear la orden de laboratorio.';
		if (message.indexOf('<!DOCTYPE') !== -1 || message.indexOf('<html') !== -1) {
			message = 'No se pudo crear la orden. Revisa la configuración de seguro, convenio y nivel tarifario.';
		}
		$('#laboratory-order-feedback').text(message).show();
	}

	function laboratory_order_clear_error(){
		$('#laboratory-order-feedback').text('').hide();
	}

	function laboratory_order_loading(button, loading){
		if (!button) return;
		button.disabled = loading;
		if (loading) {
			button.setAttribute('data-label', button.innerHTML);
			button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Creando...';
		} else if (button.getAttribute('data-label')) {
			button.innerHTML = button.getAttribute('data-label');
		}
	}

	function agregar_lab_publico(button){
		laboratory_order_clear_error();
		laboratory_order_loading(button, true);
		$.ajax({
			async: true,
			type: "POST",
			url: "{{route('hospital.decimo_laboratorio_crear_pb',[ 'id' => $solicitud->id ])}}",
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(xhr){
				laboratory_order_error(xhr);
			},
			complete: function(){
				laboratory_order_loading(button, false);
			}
		});

	}

	function agregar_lab_privado(button){
		laboratory_order_clear_error();
		laboratory_order_loading(button, true);

		$.ajax({
			async: true,
			type: "POST",
			url: "{{route('decimopaso.laboratorio_orden_crear_part',[ 'id' => $solicitud->id ])}}",
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(xhr){
				laboratory_order_error(xhr);
			},
			complete: function(){
				laboratory_order_loading(button, false);
			}
		});


	}

	function editar_labs(id){
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/editar/pub')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#labs_detalle"+id).html(datahtml);

			},
			error:  function(xhr){
				laboratory_order_error(xhr);
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();
	}

	function eliminar_labs(id) {
		var respuesta = confirm("¿ Deseas eliminar la orden ?");
		if (respuesta == true) {
			$.ajax({
			async: true,
			type: "DELETE",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/eliminar')}}/"+id,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data: "",
			datatype: "json",
			success: function(data){
				document.getElementById( 'orden_lab_div'+id).style.display = 'none';
				alert('orden eliminada');

			},
			error:  function(xhr){
				laboratory_order_error(xhr);
			}
		});

		
		}
	}

</script>
