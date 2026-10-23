
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
        		<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
		            <div class="table-responsive col-md-12 col-xs-12">
		              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
		                <thead>
		                  <tr>
		                    <th>Fecha</th>
		                    <th>Cedula</th>
		                    <th>Paciente</th>
		                    <th>Factura</th>
		                    <th>Total</th>
		                  </tr>
		                </thead>
		                <tbody>
		                @foreach ($facturas as $factura)
		                	<tr>
		                		<td>{{$factura->fecha_emision}}</td>
		                		<td>{{$factura->id_paciente}}</td>
		                		<td>{{$factura->paciente->apellido1}} {{$factura->paciente->nombre1}}</td>
		                		<td>{{$factura->sucursal}}-{{$factura->caja}}-{{$factura->numero}}</td>
		                		<td align="right">$ {{$factura->total}}</td>
		                	</tr>
		                @endforeach
		                </tbody>
		              </table>
		            </div>
		        </div>
			    <div class="col-md-5 col-xs-12">
	              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($facturas->currentPage()-1)*$facturas->perPage()}}  / @if(($facturas->currentPage()*$facturas->perPage())<$facturas->total()){{($facturas->currentPage()*$facturas->perPage())}} @else {{$facturas->total()}} @endif de {{$facturas->total()}} registros</div>
	            </div>
	            <div class="col-md-7 col-xs-12">
	              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
	                {{ $facturas->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
	              </div>
	            </div>	
			</div>    	
		</div>    
    </div>	


<script type="text/javascript">

	$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });

</script>






