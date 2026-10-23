@extends('hc_admision.visita.base')
@section('action-content')

<section class="content" >
    
    <div class="box">
        <div class="box-header">
        	<div class="form-group col-md-6" >
          		<h4>Estadistico Ordenes de Procedimiento</h4>  
          	</div>	
      		<div class="form-group col-md-6" id="xboton">
       
      		</div>
        </div>
        <div class="box-body" id="div_grafico">
        </div>
    </div>
</section> 
<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
	        type: 'get',
	        url:"{{ url('produccion/estadistico/index_js') }}", 
	        
	        success: function(data){
	          $('#div_grafico').html(data);

	        },


	        error: function(data){
	          
	           
	        }
	    });
	});    
	
</script>     	

@endsection