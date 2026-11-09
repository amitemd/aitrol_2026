@extends('hc_admision.visita.base')
@section('action-content')

<section class="content" >
    
    <div class="box">
        <div class="box-header">
          <h4>Estadistico Ordenes de Procedimiento</h4>  
          <div class="form-group col-md-12" >
           
          </div>
        </div>
        <div class="box-body" id="carga_js">
        </div>
    </div>
</section> 
<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
	        type: 'get',
	        url:"{{ url('produccion/estadistico/index_js') }}", 
	        
	        success: function(data){
	          $('#carga_js').html(data);

	        },


	        error: function(data){
	          
	           
	        }
	    });
	});    
	
</script>     	

@endsection