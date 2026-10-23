<style type="text/css">
	.form-group {
		margin-bottom: 0;
	}

	.alerta_correcto {
		position: fixed;
		z-index: 9999;
		bottom: 58%;
		left: 41%;

	}

	.alerta_ok {
		position: fixed;
		z-index: 9999;
		bottom: 58%;
		left: 41%;
	}
</style>


<div class="alert alert-success alerta_ok alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
	<b>{{trans('laboratorio.guardado')}}...<span id="actualiza"></span></b>
</div>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
	<h3 style="margin:0;">{{trans('laboratorio.factura_agrupada')}} {{trans('laboratorio.total')}}</h3>
</div>
<div class="modal-body">
	<div class="box-body">
		<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
			<div class="row">
				<div class="table-responsive col-md-6" style="min-height: 210px;">
					<center>
						<h4><b>{{trans('laboratorio.segurosPrivados')}}</b></h4>
					</center>
					<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;" width="50%">
						<thead>
							<tr>
								<th>{{trans('laboratorio.cantidad')}}</th>
								<th>{{trans('laboratorio.total')}}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{{$cantidad_priv}}</td>
								<td>${{$total_priv}}</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="table-responsive col-md-6" style="min-height: 210px;">
					<center>
						<h4><b>{{trans('laboratorio.segurosPúblicos')}}</b></h4>
					</center>
					<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;" width="50%">
						<thead>
							<tr>
								<th>{{trans('laboratorio.cantidad')}}</th>
								<th>{{trans('laboratorio.total')}}</th>
							</tr>
						</thead>
						<tbody>

							<tr>
								<td>{{$cantidad_pub}}</td>
								<td>${{$total_pub - $cabecera->valor_descuento}}</td>
							</tr>
						</tbody>
					</table>
				</div>


				<form id="form_det">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-12">
							@php
								$meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
								$mes =  $cabecera->mes; 
								$ms  = intval($mes)-1;

							$nombre_det = "";
							@endphp

							@foreach($cabecera->detalles as $detalle)
							@php
							if($detalle->pub_priv == '1'){
							$nombre_det = "Detalle Privado";
							}else{
							$nombre_det = "Detalle Publico";
							}
							@endphp
							<div class="form-group col-md-12">
								<label class="form-group col-md-2">{{$nombre_det}}</label>
								<input class="form-control" type="text" name="det{{$detalle->id}}" id="det{{$detalle->id}}" value="{{$detalle->descripcion}}" style="width: 70%;" onchange="guardar_det('{{$detalle->id}}')">
								<a class="btn btn-info"> <i class="fa fa-save"></i></a>
							</div>
							@endforeach
						</div>
					</div>

				</form>

			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<button id="btn_recalcular" onclick="envio_sri_agrupada('{{$id_cab}}');" class="btn btn-success"> {{trans('laboratorio.enviar')}}</button>
			</div>
		</div>
	</div>
</div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
	function envio_sri_agrupada(id_cab) {
		Swal.fire({
			title: '<?=trans('laboratorio.estasegurodeenviar');?>',
			//text: "",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si'
		}).then((result) => {
			if (result.isConfirmed) {
				$('#btn_recalcular').attr('disabled', 'disabled');
				$.ajax({
					type: 'get',
					url: "{{url('humanlabs/factura_agrupada/guardar_agrup_sri')}}/" + id_cab,
					datatype: 'json',
					success: function(data) {
						if (data == 'ok') {
							$('#modal_agrupada').modal('hide');
							location.reload();
						}
					},
					error: function(data) {
					}
				});
			}
		})
	}
	function guardar_det(id_det) {
		//alert("entra")
		$.ajax({
			type: 'post',
			url: "{{url('agrupada/editar_detalle')}}/" + id_det,
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: 'json',
			data: $("#form_det").serialize(),
			success: function(datahtml) {
				if (datahtml == 'ok') {
					$(".alerta_ok").fadeIn(1000);
					$(".alerta_ok").fadeOut(2000);
				}

			},
			error: function() {
				alert('error al cargar');
			}
		});


	}
</script>
