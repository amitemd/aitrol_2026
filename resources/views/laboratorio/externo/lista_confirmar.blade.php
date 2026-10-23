<style type="text/css">
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
		color: black;padding-top: 5px !important;padding-bottom: 5px !important;
	}
</style>
<div class="modal-header" style="background-color: #16a39a;padding: 2px;">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Confirmar Pago Cotización Nro. {{$orden->id}}</h4>
</div>

<div class="modal-body" >
	<table class="table table-hover" >
		<thead>
			<th>N.</th>
			<th>Examen</th>
			<th>Valor</th>
			<th>Descuento</th>
		</thead>
		<tbody>
			@php $cont = 0;@endphp
			@foreach($detalles as $detalle)
				@php $cont++;@endphp
				<tr>
					<td>{{$cont}}</td>
					<td>{{$detalle->examen->nombre}}</td>
					<td><span class="pull-right">{{$detalle->valor}}</span></td>
					<td><span class="pull-right">{{$detalle->valor_descuento}}</span></td>
				</tr>
			@endforeach
			<tr>
				<td></td>
				<td></td>
				<td><b>SubTotal</b></td>
				<td><b><span class="pull-right">{{$orden->valor}}</span></b></td>	
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><b>Descuento</b></td>
				<td><b><span class="pull-right">{{$orden->descuento_valor}}</span></b></td>	
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><b>Total</b></td>
				<td><b><span class="pull-right">{{$orden->total_valor}}</span></b></td>	
			</tr>
		</tbody>
	</table>
</div>
<div class="modal-footer">
	<div id="imagen_espera_conf" style="display: none;">
        <img src="images/espera.gif" style="width: 10%;"> Procesando ...
    </div>
	<button class="btn btn-info" id="btn_confirmar" onclick="confirmar_pago()">
		Confirmar
	</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div> 
	



	
<script type="text/javascript">
	function confirmar_pago(){
		$("#imagen_espera_conf").show();
        $("#btn_confirmar").hide();
		
		$.ajax({
            type: 'get',
            url: "webservice/confirma_pago.php?id="+"{{$orden->id}}",
            //url: "{{ route('sinlogin.carrito_pago',['id' => $orden->id]) }}", 
                       
            success: function(data){
            	console.log(data);

            	var object = JSON.parse(data);

                //console.log("-----data raw: "+object);
                console.log("-----data del bridge de confirmar_pago: "+object.url_vpos);
                //vpos invocation here -----------------------------------------                    
                $('#sanbox_pago').attr('src',object.url_vpos);
                openModal('modal_pago_1');
                //---------------------------------------------------------------


                
            },
            error: function(data){
            	console.log(data);
                alert('Error No se pudo realizar el pago, comuniquese con la administración');
                
            }    
        });  

	}
</script>
<script type="text/javascript">
    /*
        funciones para mostrar o esconder el modal del iframe
    */
	function openModal(modalId){
		$('#modal_pago_1').show();
	}
	function closeModal(modalId){
		$('#modal_pago_1').hide();
		location.reload();
	}
	$("#modal_pago_1").on('hide.bs.modal', function(){
        //alert("HOla se cerro");
    });
</script>

