<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;"><b>MOVIMIENTOS POR PEDIDO</b></h4>
  
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
	    	<div class="table-responsive col-md-12">
	          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
	            <thead>
	              <tr >
	                <th >Serie</th>
	                <th >Nombre</th>
	                <th >Cantidad</th>
	                <th >Bodega</th>
	                <th >Tipo de Movimiento</th>
	                <th >Fecha</th>  
	              </tr>
	            </thead>
	            <tbody>
	            @foreach ($productos as $value)
	                <tr>
	                  <td >{{ $value->serie }}</td>
	                  <td >{{ $value->nombre_producto }}</td>
	                  <td >{{ $value->cantidad_total }}</td>
	                  <td >{{ $value->nombre_bodega }}</td>
	                  <td >@if($value->tipo == 1 ) Ingreso @elseif($value->tipo == 2 ) Transito @elseif($value->tipo == 4 ) Dado de Baja @elseif($value->tipo == 0 ) Entregado a Paciente @endif</td>
	                  <td >{{ $value->updated_at }}</td>
	              	</tr>
	            @endforeach 
	            </tbody>
	            <tfoot>
	              
	            </tfoot>
	          </table>
	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>	 
<script type="text/javascript">
    

    $(document).ready(function(){


    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });


    
    
});


  </script>
