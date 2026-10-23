@extends('contable.facturacion.base')

@section('action-content')

<section class="content" >
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                	<h3 class="box-title"><b>FACTURACIÓN POR EMPRESAS</b></h3>
                </div>
                
                <div class="box-body">
                	<div class="col-md-12" id="work">
                		
                	</div>	
                	<div class="col-md-12" id="data">
                	@foreach($empresas as $empresa)
						<div class="col-md-6" >
							<div class="box box-success box-solid">
	                			<div class="header box-header with-border" >
	                				<div class="box-title col-md-12" ><b style="font-size: 16px;">{{$empresa->razonsocial}}</b></div>
	                				<div class="box-title col-md-12"><b>RUC: {{$empresa->id}}</b></div>
	                			</div>
	                			<div class="box-body">
	            				
	        						<div class="col-md-12">
		        						<label>Dirección</label>
		        						<p>{{$empresa->direccion}}</p>
		        					</div>	
	        					
	        						<div class="col-md-6">
		        						<label>Teléfono</label>
		        						<p>{{$empresa->telefono1}}</p>
		        					</div>		
		        					
		        					<div class="col-md-6">
		        						<label>Ciudad</label>
		        						<p>{{$empresa->ciudad}}</p>
		        					</div>		
		        					
		        					<div class="col-md-6" align="center">
		        						<button class="btn btn-warning" onclick="editar('{{$empresa->id}}')">Editar</button>
		        					</div>		
		        					
		        					<div class="col-md-6" align="center">
		        						<a href="{{route('factura.index',['id' => $empresa->id])}}" class="btn btn-success">Facturación</a>
		        					</div>		
	        					</div>	
	        				</div>	
	            				
                		</div>
                	@endforeach
                	</div>
                </div>
               
            
            </div>
        </div>
    </div>
    
</section>
<script type="text/javascript">
	
	function editar(id){
		alert(id);
		$.ajax({
        type: 'get',
        url: "{{url('contable/empresas/editar')}}/"+id,           
        success: function(data){

            $('#work').empty().html(data);
            $('#work').removeClass( "col-md-12" );
            $('#work').addClass( "col-md-6" );
            $('#data').removeClass( "col-md-12" );
            $('#data').addClass( "col-md-6" );


        }
    })  
	}
</script>



@endsection