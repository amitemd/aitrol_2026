<style type="text/css">
    .parent{
	  	overflow-y:scroll;
     	height: 600px;
	}
	.parent::-webkit-scrollbar {
	    width: 8px;
	} /* this targets the default scrollbar (compulsory) */
	.parent::-webkit-scrollbar-thumb {
	    background: #004AC1;
	    border-radius: 10px;
	}
	.parent::-webkit-scrollbar-track {
		width: 10px;
	    background-color: #004AC1;
	    box-shadow: inset 0px 0px 0px 3px #56ABE3;
	} /* the new scrollbar will have a flat appearance with the set background color */
	.parent::-webkit-scrollbar-track-piece{
		width: 2px;
	    background-color: none;
	}

	.parent::-webkit-scrollbar-button {
	      background-color: none;
	} /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

	.parent::-webkit-scrollbar-corner {
	      background-color: none;
	}

	.thead-dark>tr>th{
		background-color: #005580 !important;
		padding: 5px;
	}

	.table-hover tbody tr:hover{
		background-color: #80ffff !important;

	}

	#area_trabajo .hc4-lab-orders > .hc4-lab-orders__header {
		display: grid !important;
		grid-template-columns: minmax(190px, .8fr) minmax(220px, 1fr) auto;
		align-items: center;
		gap: 10px;
		width: 100%;
		min-height: 74px;
		padding: 12px 14px !important;
		box-sizing: border-box;
		overflow: hidden;
		background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
	}

	.hc4-lab-orders__title {
		display: flex;
		align-items: center;
		gap: 12px;
		min-width: 0;
		font-size: 16px;
		font-weight: 700;
		line-height: 1.25;
	}

	.hc4-lab-orders__title-icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		flex: 0 0 40px;
		width: 40px;
		height: 40px;
		border: 1px solid rgba(255,255,255,.35);
		border-radius: 10px;
		background: rgba(255,255,255,.12);
	}

	.hc4-lab-orders__actions,
	.hc4-lab-orders__patient {
		display: flex;
		align-items: center;
		gap: 6px;
	}

	#area_trabajo .hc4-lab-orders__action {
		width: auto !important;
		height: auto !important;
		min-height: 40px;
		margin: 0 !important;
		padding: 7px 9px !important;
		border: 1px solid rgba(255,255,255,.35) !important;
		border-radius: 9px !important;
		background: transparent !important;
		box-shadow: none;
		font-size: 11px;
		font-weight: 700;
		white-space: nowrap;
	}

	#area_trabajo .hc4-lab-orders__action:hover {
		background: rgba(255,255,255,.22) !important;
	}

	#area_trabajo .hc4-lab-orders__action img {
		width: 19px !important;
		height: 19px !important;
		margin: 0 !important;
	}

	.hc4-lab-orders__patient {
		justify-content: center;
		min-width: 0;
		grid-column: 2;
		grid-row: 1;
		text-align: center;
	}

	.hc4-lab-orders__actions {
		grid-column: 3;
		grid-row: 1;
		justify-content: flex-end;
		min-width: 0;
	}

	.hc4-lab-orders__patient-name,
	.hc4-lab-orders__age,
	.hc4-lab-orders__title {
		font-weight: 700;
	}

	.hc4-lab-orders__patient-name,
	.hc4-lab-orders__age {
		padding: 7px 8px;
		border-radius: 9px;
		background: transparent;
		font-size: 12px;
		line-height: 1.3;
	}

	.hc4-lab-orders__patient-name {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.hc4-lab-orders__age { flex: 0 0 auto; white-space: nowrap; }

	@media (max-width: 900px) {
		#area_trabajo .hc4-lab-orders > .hc4-lab-orders__header {
			grid-template-columns: 1fr auto;
		}
		.hc4-lab-orders__title {
			grid-column: 1;
			grid-row: 1;
		}
		.hc4-lab-orders__actions {
			grid-column: 2;
			grid-row: 1;
			flex-wrap: wrap;
		}
		.hc4-lab-orders__patient {
			grid-column: 1 / -1;
			grid-row: 2;
			justify-content: flex-start;
		}
	}

	@media (max-width: 700px) {
		#area_trabajo .hc4-lab-orders > .hc4-lab-orders__header { display: flex !important; flex-direction: column; align-items: stretch; }
		.hc4-lab-orders__actions { flex-wrap: wrap; }
		.hc4-lab-orders__patient { align-items: stretch; flex-direction: column; }
		.hc4-lab-orders__patient-name { white-space: normal; }
	}

</style>
@php
  $tipo_usuario  = Auth::user()->id;
	$xedad = '0';
	if($paciente->fecha_nacimiento!=null){
		$xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
	}
@endphp
<div class="box hc4-lab-orders" style="background-color: white;">
	<input type="hidden" name="paciente_id" id="paciente_id" value="{{$paciente->id}}">
	<div class="box-header with-border hc4-lab-orders__header">
		<div class="hc4-lab-orders__title">
			<span class="hc4-lab-orders__title-icon"><i class="fa fa-flask" aria-hidden="true"></i></span>
			<span>@lang('hc4.historialOrdenesLaboratorio')</span>
		</div>
		<div class="hc4-lab-orders__actions">
			<a class="btn btn-info hc4-lab-orders__action" onClick="agregar_orden_laboratorio();">
				<img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
				<span>@lang('hc4.agregarOrdenParticular')</span>
			</a>
		</div>
		<div class="hc4-lab-orders__patient">
			<div class="hc4-lab-orders__patient-name">
				<b>@lang('hc4.paciente'):</b>
				{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
				{{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
			</div>
			<div class="hc4-lab-orders__age"><b>@lang('hc4.edad'):</b> {{$xedad}} @lang('hc4.anios')</div>
		</div>
	</div>
	<div class="box-body" style="background-color: #56ABE3;">
		<div class="col-md-12">
            <div class="row parent">

            	<div class="col-md-12" id="listado" style="padding: 0">
            	</div>


			</div>
        </div>
    </div>
</div>

<script type="text/javascript">

    agregar_orden_listado();
    //Funcion para crear una Orden de Procedimiento Endoscopico
    function agregar_orden_listado(){
        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('hc4_orden_lab.index2',['paciente' => $paciente->id ])}}",
            data: {},
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
    }

    function agregar_orden_laboratorio(){
    	$.ajax({
			async: true,
			type: "POST",
			url: "{{route('hc4_orden_lab.crear',['paciente' => $paciente->id ])}}",
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(xhr){
				mostrar_error_crear_orden(xhr);
			}
		});
    }

	function mostrar_error_crear_orden(xhr){
		console.error('Error al crear la orden de laboratorio', xhr);
		var respuesta = xhr && xhr.responseJSON ? xhr.responseJSON : null;
		var mensaje = respuesta && (respuesta.message || respuesta.error)
			? (respuesta.message || respuesta.error)
			: (xhr && xhr.responseText ? xhr.responseText : '');
		if (!mensaje || mensaje.indexOf('<') !== -1) {
			mensaje = {!! json_encode(trans('hc4.errorCargar')) !!};
		}
		alert(mensaje);
	}

    


    {{--function agregar_orden_publica(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('laboratorio.orden.publica',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(xhr){
				mostrar_error_crear_orden(xhr);
			}
		});
    }--}}



    //Descarga Orden de Procedimiento Endoscopico
	function descargar(id_or){//
       window.open('{{url('cotizador_p/orden/imprimir')}}/'+id_or,'_blank');
    }

	function agregar_lab_publico(){
		var id_paciente = $('#paciente_id').val();
		$.ajax({
			async: true,
			type: "POST",
			url: "{{route('laboratorio.ordenIess.crear',['id' => $paciente->id ])}}",
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
			success: function(datahtml){
			    $("#listado").html(datahtml);
			},
			error:  function(xhr){
				mostrar_error_crear_orden(xhr);
			}
		});
	}

	function editar_labs_publico(){
		var id_paciente = $('#paciente_id').val();
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/paciente/laboratorio/detalle/solic/editar/pub/')}}/"+id_paciente,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);
				
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
	}
	function listar_labs_publico(id){
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/paciente/laboratorio/listar/examenes/publicos/')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado"+id).html(datahtml);
				
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
	}
</script>
