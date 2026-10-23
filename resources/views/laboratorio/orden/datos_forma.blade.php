	<div class="box-header">
	    <h4>Crear Forma de pago</h4>
	</div>

	<div class="box-body">
		<form id="form_datos" class="form-horizontal" name="form_datos">
			{{ csrf_field() }}
			<div class="form-group col-md-4 "  id="div_id_pago">
				<div class="col-md-12">
					<label> Metodo</label>
				</div>
		      	<div class="col-md-12">
			        <input type="hidden" name="id_orden" value="{{$id_orden}}">
			        <select id="id_pago" name="id_pago" class="form-control input-sm" onchange="validar();valor_neto();">
			        	
			          @foreach($pagos as $value)
			            <option value="{{$value->id}}">{{$value->nombre}}</option>
			          @endforeach
			        </select>
		      	</div> 
	    	</div>

	    	<div class="form-group col-md-4 " style="display:none;" id="div_id_tarjeta">
	    		<div class="col-md-12">
	    			<label> Tipo tarjeta</label>
	    		</div>
		      	<div class="col-md-12">
			        <select id="id_tipo_tarjeta" name="id_tipo_tarjeta" class="form-control input-sm">
			        	<option value="">Seleccione</option>
			          @foreach($tarjetas as $tarjeta)
			            <option value="{{$tarjeta->id}}">{{$tarjeta->nombre}}</option>
			          @endforeach
			        </select>			      	
		      	</div> 
	    	</div>

	    	<div class="form-group col-md-4" style="display:none;" id="div_transaccion">
				<div class="col-md-12">
					<label> Número Transaccion</label>
				</div>
		      	<div class="col-md-12">
			        <input type="text" id="transaccion" name="transaccion" class="form-control input-sm">			       
		      	</div> 
	    	</div>

	    	<div class="form-group col-md-4 " style="display:none;" id="div_banco">
	    		<div class="col-md-12">
	    			<label> Banco</label>
	    		</div>
		      	<div class="col-md-12">			       	
			        <select id="id_banco" name="id_banco" class="form-control input-sm">
			        	<option value="">Seleccione</option>
			          @foreach($bancos as $banco)
			            <option value="{{$banco->id}}">{{$banco->nombre}}</option>
			          @endforeach
			        </select>
			      	
		      	</div> 
	    	</div>


	    	<div class="form-group col-md-4 "  id="div_valor">
				<div class="col-md-12">
					<label> Valor Base</label>
				</div>
		      	<div class="col-md-12">
			        <input type="number" id="valor" name="valor" class="form-control input-sm" onchange="valor_neto();">
			       
		      	</div> 
	    	</div>

	    	<div class="form-group col-md-4 "  id="div_valor">
				<div class="col-md-12">
					<label> &nbsp;</label>
				</div>
		      	<div class="col-md-12">
		      		<button type="button" class="btn btn-info btn-xs"><i class="fa fa-calculator"></i></button>
		      	</div> 
	    	</div>


	    	<div class="form-group col-md-4 "  id="div_valor">
				<div class="col-md-12">
					<label> Valor Neto</label>
				</div>
		      	<div class="col-md-12">
			        <span id="valor_neto" name="valor_neto"></span>
			       	
		      	</div> 
	    	</div>

	    	<!--div class="form-group col-md-4" style="display:none;" id="div_fi">
				<label> Fi <input type="checkbox" name="fi" id="fi"></label> 
	    	</div-->

	    	<div class="form-group col-md-6 ">
				<div class="col-md-6">
	          		<button id="crear" type="button" class="btn btn-info" onclick="guardar();">Guardar</button>
	      		</div>
	    	</div>
    	</form>
	</div>

	<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
	<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
	<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
	<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
	<script type="text/javascript">
		function validar(){
			$('#div_transaccion').hide();
			$('#div_banco').hide();
			$('#div_id_tarjeta').hide();
			$('#div_fi').hide();

			var forma_pago= $('#id_pago').val();
			if(forma_pago == '2' || forma_pago == '3' || forma_pago == '5'){
				$('#div_transaccion').show();
				$('#div_banco').show();
			}

			if (forma_pago == '4' || forma_pago == '6') {
				$('#div_transaccion').show();
				$('#div_banco').show();
				$('#div_id_tarjeta').show();
				$('#div_fi').show();

			}
		}

		function valor_neto(){
			var valor_base= $('#valor').val();
			valor_base= parseFloat(valor_base);
			var forma_pago = $('#id_pago').val();

			var fi = 0;
			var valor_neto =0;
			if (forma_pago == '4') {
				//fi= 0.07;

			}

			if (forma_pago == '6') {
				//fi= 0.045;
			}
			//console.log(forma_pago, fi, valor_base);
			valor_neto= valor_base+(valor_base*fi);
			valor_neto =  Math.round(valor_neto*100)/100;
			
			//console.log(forma_pago, fi, valor_base, valor_neto);

			$('#valor_neto').text(valor_neto);
			

		}
		function guardar(){
			var forma_pago= $('#id_pago').val();
			var transaccion= $('#transaccion').val();
			var banco =$('#id_banco').val();
			var tarjeta =$('#id_tipo_tarjeta').val();
			var error='';
			if(forma_pago == '2' || forma_pago == '3' || forma_pago == '5'){
				if (transaccion == '') {
					error='Debe ingresar transaccion <br>';
				}
				if(banco == ''){
					error='Debe seleccionar el banco <br>';
				}

			}

			if (forma_pago == '4' || forma_pago == '6') {
				if(banco == ''){
					error='Debe seleccionar el banco <br>';
				}
				if (tarjeta== '') {
					error='Debe seleccionar el tarjeta <br>';
				}
			}

			if (error=='') {
				$.ajax({
			      type: 'post',
			      url:"{{ route('facturalabs.guardar_forma') }}",
			      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
			      datatype: 'json',
			      data: $("#form_datos").serialize(),
			      success: function(data){
			          //console.log(data);
			          if(data.estado=='ok'){
			              cargar_forma_pago_tabla(data.id_orden);
			              $("#crear_registro").hide();
			          };
			      },
			      error: function(data){
			          console.log(data);
			      }
			    });
			}
		    
		}

	</script>
