<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">
	.input-sm {
		padding: 2px;
	}
	

	.bootstrap-datetimepicker-widget {
		background-color: white ;
		color: #333 ;
	}
</style>
<div class="box box-primary box-solid" >
	<div class="box-header with-border" >
        <div><b style="font-size: 16px;">LISTADO DE SOLICITUDES</b></div>
        <div>
        	<form id="form_listado">
	            {{ csrf_field() }}
	            
	            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
	              <label for="desde" class="control-label">Desde</label>
	              
	                <div class="input-group date" id="desde_g">
	                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    <i class="fa fa-calendar"></i>
	                  </div>
	                  <input type="text" class="form-control input-sm" name="desde" id="desde" autocomplete="off" onclick="fecha_desde();">
	                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('desde').value = ''; buscar();"></i>
	                  </div>   
	                </div>
	                
	            </div>
	            
	            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
	              <label for="hasta" class="control-label">Hasta</label>
	              
	                <div class="input-group date" id="hasta_g">
	                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    <i class="fa fa-calendar"></i>
	                  </div>
	                  <input type="text" class="form-control input-sm" name="hasta" id="hasta" autocomplete="off" onclick="fecha_hasta();">
	                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('hasta').value = ''; buscar();"></i>
	                  </div>   
	                </div>
	               
	            </div>
	            <?php /*

	            <!--div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
	              <label for="cedula" class="control-label">Cédula</label>
	             
	                <div class="input-group">
	                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" onchange="buscar();">
	                	<div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    	<i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
	                  	</div>  
	                </div>
	              
	            </div-->
	            */ ?>
				
	            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
	              <label for="nombres" class="control-label">Paciente</label>
	              
	                <div class="input-group">
	                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();">
	                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
	                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';buscar();"></i>
	                  </div>
	                </div>  
	              
	            </div>

	            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
	              <label for="id_seguro" class="control-label">Estado</label>
	              
	                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
	                  <option value="">Seleccione ...</option>
	                @foreach($estados as $estado)
	                  <option @if($estado->id==$id_estado) selected @endif value="{{$estado->id}}">{{$estado->descripcion}}</option>
	                @endforeach  
	                </select>
	              
	            </div>
	            
	            
	            
	            <!--div class="form-group col-md-2 col-xs-6" >
	              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ url('consultam/pastelpentax ') }}"><span class="glyphicon glyphicon-stats" aria-hidden="true"> Estadísticas</button>
	            </div-->
	               
	        </form>
	        <div class="form-group col-md-1 col-xs-4" >
            	<br>
              	<button class="btn btn-primary btn-sm" id="boton_buscar" style="border: solid 1px white;">
                	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                </button>  
            </div>
	        <div class="form-group col-md-1 col-xs-4" >
	            <br>
		        <button class="btn btn-primary btn-sm" id="boton_plus" style="border: solid 1px white;">
	            	<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar
	            </button> 
	        </div>    
        	
        </div>
    </div>
    <div class="box-body">
		<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th>Contacto</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Teléfono</th>
                    <th>Mail</th>
                    <th>Respuesta</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($solicitudes as $solicitud)
                	<tr>
                		<td>{{substr($solicitud->fecha_contacto,0,10)}}</td>
                		<td>{{$solicitud->apellido1}}{{$solicitud->apellido2}}</td>
                		<td>{{$solicitud->nombre1}}{{$solicitud->nombre2}}</td>
                		<td>{{$solicitud->telefono1}}</td>
                		<td>{{$solicitud->mail}}</td>
                		<td>{{substr($solicitud->fecha_respuesta,0,10)}}</td>
                		<td>@if(!is_null($solicitud->bo_estado)){{$solicitud->bo_estado->descripcion}}@endif</td>
                	</tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
          
            <div class="col-md-5 col-xs-12">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($solicitudes->currentPage()-1)*$solicitudes->perPage()}}  / @if(($solicitudes->currentPage()*$solicitudes->perPage())<$solicitudes->total()){{($solicitudes->currentPage()*$solicitudes->perPage())}} @else {{$solicitudes->total()}} @endif de {{$solicitudes->total()}} registros</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $solicitudes->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
              </div>
            </div>	
    </div>    	
</div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

	$(function () {

		
        
		$('#desde_g').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$desde}}',
            
        });

		$('#hasta_g').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$hasta}}',
            
        });
        
        
        $("#desde_g").on("dp.change", function (e) {
            buscar();
        });

         $("#hasta_g").on("dp.change", function (e) {
            buscar();
        });
  	});	

  	$('#desde').on('click', function(){
	        $('#desde_g').datetimepicker('show');
	});

	$('#hasta').on('click', function(){
	        $('#hasta_g').datetimepicker('show');
	});

	$('#boton_plus').on('click', function(){
        alert("agregar");
        $.ajax({
          type: 'post',
          url:"{{route('solicitud.agregar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form_listado").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
	});

	

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