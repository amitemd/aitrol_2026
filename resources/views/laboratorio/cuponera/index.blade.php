@extends('laboratorio.cuponera.base')
@section('action-content')
<section class="content">
  	<div class="box box-success">
	    <div class="box-header">
	      <div class="row">
	          <div class="col-md-9">
	            
	            <h3 class="box-title">Control de Cuponeras -- Total de Cupones Usados: {{$total}}</h3>

	          </div>
	      </div>
	    </div>

	    <!-- /.box-header -->
	    <div class="box-body">
	      	<div style="text-align: center">
		        <div id="example1_wrapper" >
		          	<div class="row">
			            <div class="col-md-12 col-sm-12">
			              	<div class="table-responsive">
				                <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 11px;">
				                	<thead>
					                    <tr role="row">
					                      <th >Cedula</th>
					                      <th >Apellidos</th>
					                      <th >Nombres</th>
					                      <th >F. Entrega</th>
					                      <th >R. Inferior</th>
					                      <th >R. Superior</th>
					                      <th >Cantidad</th>
					                    </tr>
				                  	</thead>
				                  	<tbody>
				                  	
					                @foreach($cupones as $cupon)

					                    <tr role="row" >
					                     	<td>{{$cupon->cedula}}</td>
					                     	<td>{{$cupon->apellidos}}</td>
					                    	<td>{{$cupon->nombres}}</td>  
					                      	<td>{{$cupon->fecha_entrega}}</td>
					                      	<td>{{$cupon->inferior}}</td>
					                      	<td>{{$cupon->superior}}</td>
					                      	<td>@if(isset($cupon->id)){{$arr[$cupon->id]}}  @endif</td>
					                    </tr>
					                @endforeach
				                  	</tbody>
				                </table>
			              	</div>
			            </div>  
		          	</div>
		        </div>
	      	</div>
	      	
	    </div>
    <!-- /.box-body -->
  	</div>
</section>
<script type="text/javascript">
	$('#example1').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
</script>
@endsection

