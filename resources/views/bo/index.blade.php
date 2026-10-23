@extends('bo.base')

@section('action-content')




<section class="content" >
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                
                <div class="box-body">
                	<div class="col-md-7" id="work" style="padding: 0px;">
                		    
                	</div>	
                	<div class="col-md-5" id="data" style="padding: 0px;">
                		
						
							
                	
                	</div>
                </div>
               
            
            </div>
        </div>
    </div>
    
</section>


<script type="text/javascript">
	
	$(function () {

		cargar_listado();
        cargar_data();
        
		
        
        
       
  	});

  	

    function cargar_listado(){

    	$.ajax({
        type: 'get',
        url: "{{route('solicitud.listado')}}",           
        success: function(data){

	            $('#work').empty().html(data);
	            /*$('#work').removeClass( "col-md-12" );
	            $('#work').addClass( "col-md-6" );
	            $('#data').removeClass( "col-md-12" );
	            $('#data').addClass( "col-md-6" );*/


        	}
    	})  
    }
    
    function cargar_data(){

        $.ajax({
        type: 'get',
        url: "{{route('solicitud.data')}}",           
        success: function(data){

            $('#data').empty().html(data);


            }
        })  
    }


</script>



@endsection