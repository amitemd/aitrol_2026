<style type="text/css">
	.boton-buscar{
		font-size: 14px ;
		width: 70%; 
		height: 35px;
		background-color: #004AC1; 
		color: white;
		text-align: center;
		
	} 

  #example2_fav_wrapper .row{
    width: 100%;
  }

  tbody>tr:hover{
    background-color: #8CAFFF;
  }  

</style>
<div class="row">
  <div class="col-12" id="xarea_compartida" style="padding: 5px;">
    <div class="box-header">
      
        <div class="col-12" style="background-color: #004AC1; padding: 10px">
           <label class="box-title" style="color: white; font-size: 20px">Lista de Exámenes Favoritos</label>
        </div>
        <div class="row" style="padding: 10px;">
          <div class="col-6">
          	<form method="POST" id="proc_plantilla" >
              {{ csrf_field() }}
              <div class="row">
              	<div class="col-10">
                  <div class="row">
                		<label for="proc_com" class="control-label col-4" style="font-size: 14px"><span style="font-family: 'Helvetica general';">Buscar</span>
                		</label>
                    <div class="col-8">
                      <input type="text" name="nombre" style="width: 100%;" value="{{$nombre}}"> 
                    </div>
                  </div>  
              	</div>
  	            <div class="col-2" >
  	              <button style="background-color: #004AC1" type="button"  onclick="buscar_favorito()" class="btn btn-info " id="boton_buscar">
  	                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
  	            </div>
  	          </div>   
            </form>  
          </div>
          <div class="col-6">
              <button  type="button" class="btn btn-success " onclick="crear_favorito()" >
              	<span class="glyphicon glyphicon-plus"> 
              		Crear
              	</span>
              </button>
          </div>  
        </div>  
      
    </div> 
  	<div class="box-body">
    	<div class="dataTables_wrapper form-inline dt-bootstrap">
      
          
  	      	<div class="table-responsive" >
  	        	<table id="example2_fav" class="table table-hover dataTable" cellspacing="0" width="100%" >
  	        		<thead> 
                    <th  width="50%"><span style="font-family: 'Helvetica general';">Nombre</span></th>
                    <th  width="30%"><span style="font-family: 'Helvetica general';">Cantidad</span></th>
                    <th  width="20%"><span style="font-family: 'Helvetica general';">Acción</span></th>
                </thead>
                <tbody >
                  @foreach ($protocolos as $value)
                    <tr>
                      <td style="font-size: 12px;">{{ $value->nombre}}</td>
                      <td style="font-size: 12px;">{{$value->examenes->count()}}</td>                        
                      <td style="font-size: 12px;">
                        <button type="button" class="btn btn-sm btn-warning" style="color: white" onclick="editar_favoritos({{$value->id}})">Editar</button>
                        <button type="button" class="btn btn-sm btn-success" style="color: white" onclick="ver_favoritos({{$value->id}})">Ver</button>
                      </td>
                    </tr>
                  @endforeach 
                </tbody>
  	        	</table>
              <label style="padding-left: 15px;font-size: 16px">Total de Registros: {{$protocolos->count()}}</label> 
          	</div>
          
        
      </div>
    </div>
  </div>
  <div class="col-6" id="xarea_trabajo2" style="padding: 5px;">
    
  </div>
</div>  

 <script type="text/javascript">
    
    $(document).ready(function(){

      $('.select2').select2({
        tags: false
      });

      $('#example2_fav').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : true
      });

  
      $(".breadcrumb").append('<li class="active">Procedimientos</li>');
 
    });

  function buscar_favorito(){
   	//alert("entro");
      $.ajax({
        type: 'post',
        url:"{{route('hc4_examenes.buscar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#proc_plantilla").serialize(),
        success: function(data){
        	//console.log(data);
          $("#area_trabajo").html(data);
          //console.log(data);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
  }

	function crear_favorito(){
   	//alert("entro");
    $.ajax({
      type: 'get',
      url:"{{route('hc4_examenes.crear')}}",
      datatype: 'json',
      success: function(data){
        $("#area_trabajo").html(data);
        //console.log(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function editar_favoritos(id){
    $.ajax({
      type: 'get',
      url:"{{url('hc4/laboratorio/examenes/favoritos/editar')}}/" + id,
      datatype: 'json',
      success: function(data){
        $("#area_trabajo").html(data);
        //console.log(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function ver_favoritos(id){
    $.ajax({
      type: 'get',
      url:"{{url('hc4/laboratorio/examenes/favoritos/ver')}}/"+id,
      datatype: 'json',
      success: function(data){
        $('#xarea_compartida').removeClass('col-12');
        $('#xarea_compartida').addClass('col-6');
        $("#xarea_trabajo2").html(data);
        //console.log(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });  
  }

  </script>