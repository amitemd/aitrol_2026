<div class="modal-header">
    <div class="col-md-10"><h3>Actualiza Valor</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>

<div class="modal-body">
	<div class="box-body">
		 <form class="form-horizontal" id="form_liq">
		 	{{ csrf_field() }}
		 	<div class="row">
		        <div class="form-group col-md-6">
		            <label for="val_liq" class="col-md-4 control-label">Valor:</label>
		            <div class="col-md-8">
		              <input id="val_liq" type="text" class="form-control" name="val_liq" value="" required> 
		              <input type="hidden" name="id_prestamo" value="{{$id}}" class="form-control">
		            </div>
		        </div>
      		</div>
		 </form>
		 <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
	      <center>
					<div class="col-md-6 col-md-offset-2">
						<div class="col-md-7">
							<button type="button" class="btn btn-primary" onclick="guardar_utili('{{$id}}')"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
						</div>
					</div>
	      </center>
		</div>
	</div>

	<div class="modal-footer">
    	<button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
  	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
	
</script>
