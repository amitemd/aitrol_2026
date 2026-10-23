<div class="box box-primary box-solid" >
	<div class="box-header with-border" >
        <div><b style="font-size: 16px;">AGREGAR SOLICITUD</b></div>
        
    </div>
    <div class="box-body">
		<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div>
        	<form method="POST" action="{{ route('consultam.search') }}" >
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
	            
	            <div class="form-group col-md-1 col-xs-4" >
	            	<br>
	              	<button type="submit" class="btn btn-primary btn-sm" id="boton_buscar" style="border: solid 1px white;">
	                	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
	                </button>  
	            </div>
	            
	            <!--div class="form-group col-md-2 col-xs-6" >
	              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ url('consultam/pastelpentax ') }}"><span class="glyphicon glyphicon-stats" aria-hidden="true"> Estadísticas</button>
	            </div-->
	               
	        </form>
	            
        	
        </div>
         </div>
          
            	
    </div>    	
</div>