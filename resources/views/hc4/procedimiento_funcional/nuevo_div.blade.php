<div class="col-md-4 col-sm-6 col-12" style='margin: 10px 0;text-align: center;' >			                                                
	@php
	    $explotar = explode( '.', $imagen->nombre);
	    $extension = end($explotar);
	@endphp
	@if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
	    <div class="row">
	        <div class="col-12">
	            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
	                <div class="col-12">
	                	<img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
	                </div>
	                <div class="col-12">
	                	<p style="font-size: 12px">
	                    	@if(strlen($imagen->nombre_anterior) >= '20')
	  							{{substr($imagen->nombre_anterior,0,20)}}...
	  							@else
	  								{{$imagen->nombre_anterior}}
	  						@endif
							</p>
	                </div>
	            </a>
	        </div>
	        <div class="col-12">
	            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
	                <div class="col-12">
	                	<span class="glyphicon glyphicon-download-alt"> Descargar</span>
	                </div>
	            </a> 
	        </div>
	    </div>
	@elseif(($extension == 'pdf'))
	    <div class="row">
	        <div class="col-12">
	       		<a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
	            	<div class="col-12">
	                	<img  src="{{asset('imagenes/pdf.png')}}" width="90%">
	                </div>
	                <div class="col-12">
	                	<p style="font-size: 12px">
	                		@if(strlen($imagen->nombre_anterior) >= '20')
	  							{{substr($imagen->nombre_anterior,0,20)}}...
	  							@else
	  								{{$imagen->nombre_anterior}}
	  						@endif
	                	</p>  
	                </div>  
	        	</a>
	        </div> 
	        <div class="col-12">
	        	<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
	        		<div class="col-12">
	            		<span class="glyphicon glyphicon-download-alt"> Descargar</span>
	            	</div>
	        	</a>
	        </div>
	    </div>
	@else
	    @php
	        $variable = explode('/' , asset('/hc_ima/'));
	        $d1 = $variable[3];
	        $d2 = $variable[4];
	        $d3 = $variable[5];
	        
	    @endphp 
	    <div class="row">
	    	<div class="col-12">
	    		<a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
	        		<div class="col-12">
	        			<img  src="{{asset('imagenes/office.png')}}" width="90%">
	        		</div>
	        		<div class="col-12">
	        			<p style="font-size: 12px">
	        				@if(strlen($imagen->nombre_anterior) >= '20')
	  							{{substr($imagen->nombre_anterior,0,20)}}...
	  							@else
	  								{{$imagen->nombre_anterior}}
	  						@endif
	        			</p>
	        		</div>  
	    		</a> 
	    	</div>
	    	<div class="col-12">
	    		<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
	            	<div class="col-12">
	            		<span class="glyphicon glyphicon-download-alt"> Descargar</span>
	            	</div>
	    		</a> 
	    	</div>
	    </div>
	@endif      
	</div> 