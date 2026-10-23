<div class="card">
	<div class="card-header bg bg-primary">
		<div class="col-md-1">
			<i style="color: #004AC1;">{{$orden->id}}</i>
		</div>	
		<div class="col-md-9">
      	   <span style="margin-right: 5px;border-radius: 2px;" class="badge badge-primary">{{trans('boxesh.DetalledeOrden')}}  @if(!is_null($txtprocedimientos)) {{$txtprocedimientos}} @endif</span>
        </div>
        <div class="col-md-2" style="text-align: right;padding-top: 6px">
        <a style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-success btn_ordenes" href="{{route('decimopaso.imprimir_orden_funcional_hospital', ['id' => $orden->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt" ></span>{{trans('boxesh.Descargar')}} </a>
        </div>
	</div>

	<div class="card-body" style="margin: 10px 10px;padding: 0;">
           
    	<div class="row">
    	    <div class="col-md-8">	
				<span><b>{{trans('boxesh.DOCTORSOLICITANTE')}}</b></span>
				<label for="doctor" class="control-label" ><b>{{$orden->doctor->apellido1}} {{$orden->doctor->apellido2}}
				      {{$orden->doctor->nombre1}} {{$orden->doctor->nombre2}}  
                  </b>
                </label>
            </div>
            <div class="col-md-4">
            	@if(!is_null($seguro)) 
	                <span >{{trans('boxesh.SEGURO')}}</span>
                    <label for="convenio" class="control-label" >
                      <b> 
                        {{$seguro->nombre}}
                      </b>
                    </label>
                @endif
            </div>
        </div>
                  
                   
        <div class="row">
            <div class="col-md-12">
                <b> {{trans('boxesh.AntecedentesPersonalesyFamiliares')}}</b>
            </div>
            <div class="col-md-4">
                <b style="">1. {{trans('boxesh.Alergias')}}</b>
            </div>
            @php 
                $alergias = $orden->paciente->a_alergias; $txt_al = '';$cont = 0;
                foreach($alergias as $alergia){ 
                    if($cont < 2)  {  
                    if($cont==0){ $txt_al = $alergia->principio_activo->nombre; }
                    else{ $txt_al = $txt_al.' + '.$alergia->principio_activo->nombre; }
                    $cont++;
                    }
                }  
                $txt_al = $txt_al.' ...'  
            @endphp
            <div class="col-md-8">
                <span class="badge badge-danger" style="font-size: 11px"> {{$txt_al}} </span>
            </div>
            
            <div class="col-md-6">
                <b style="">2. {{trans('boxesh.Clinicos')}}</b>
            </div>
            @php $datos_paciente = $orden->paciente->ho_datos_paciente; @endphp
            <div class="col-md-6">
                <span style=""> {{ substr($datos_paciente->clinico,0,30) }} ... </span>
            </div>
             <div class="col-md-6">
                <b style="">3. {{trans('boxesh.Ginecologico') }}</b>
            </div>

            <div class="col-md-6">
                <span style=""> {{ substr($datos_paciente->ginecologico,0,30) }} ... </span>
            </div>
            <div class="col-md-6">
                <b style="">4. {{trans('boxesh.Traumatologicos')}} </b>
            </div>
            <div class="col-md-6">
                <span style=""> {{ substr($datos_paciente->traumatologico,0,30) }} ... </span>
            </div>
           
        </div> 
                       
                  
		<div class="row">
			<div class="col-md-12">
	          <span >{{trans('boxesh.MOTIVO')}}</span>
	        </div>
	        <div class="col-12">
					<span><?php echo $orden->motivo_consulta?></span>
				</div>
	    </div>
  		               
  		               
        <div class="row">
    		<div class="col-md-12">
              <span >{{trans('boxesh.RESUMENDELAHISTORIACLINICA')}}</span>
            </div>
            <div class="col-12">
  				<span><?php echo $orden->resumen_clinico?></span>
  			</div>
        </div>
  		          
        @if(!is_null($orden->diagnosticos))
        <div class="col-md-12" style="padding: 1px;">
        	<div class="row">
        		<div class="col-md-12">
                  <span >{{trans('boxesh.DIAGNOSTICO')}}</span>
                </div>
                <div class="col-12">
	  				<span><?php echo $orden->diagnosticos?></span>
	  			</div>
            </div>
        </div>
        @endif
  		                <div class="col-md-12" style="padding: 1px;">
                        @if($txtprocedimientos != "")
                            <div class="row">
                              	<div class="col-md-12">
                                   <div style="background-color: #004AC1; color: white">
					                   <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">{{trans('boxesh.PROCEDIMIENTOSIMAGENES')}} 
					                    </label>
					                </div>
                                </div>
                                <div class="col-12">
			  						<span>
			  					      {{$txtprocedimientos}}
			  						</span>
						  	    </div>
                            </div>
                        @endif
                    </div>
                    @if(!is_null($orden->observacion_medica))
  		                <div class="col-md-12" style="padding: 1px;">
                    	<div class="row">
                    		<div class="col-md-12">
			                  <span >{{trans('boxesh.OBSERVACIONMEDICA')}}</span>
			                </div>
			                <div class="col-12">
			                  <span><?php echo $orden->observacion_medica?></span>
			                </div>
	   	  			    </div>
  		                </div>
  		                @endif
  		                @if(!is_null($orden->observacion_recepcion))
  		                <div class="col-md-12" style="padding: 1px;">
                    	<div class="row">
                    		<div class="col-md-12">
			                  <span >{{trans('boxesh.OBSERVACIONRECEPCION')}}</span>
			                </div>
			                <div class="col-12">
			                  <span><?php echo $orden->observacion_recepcion?></span>
			                </div>
	   	  			    </div>
  		                </div>
  		                @endif
  		                <div class="col-md_12" >
				        <center>
				            <div class="col-md-5" style="padding-top: 15px;text-align: center;">
				                <a style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" href="{{route('decimopaso.imprimir_orden_funcional_hospital', ['id' => $orden->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt" ></span>&nbsp;&nbsp; {{trans('boxesh.DescargarOrden')}}
                                </a>
				            </div>
				        </center>
                    </div>
                </div>
            </div>
        </div>

	</div>	
	
</div>	


  

