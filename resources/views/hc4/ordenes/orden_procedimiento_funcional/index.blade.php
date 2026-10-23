<style type="text/css">
    .parent{
	  	overflow-y:scroll;
     	height: 600px;
	}
	.parent::-webkit-scrollbar {
	    width: 8px;
	} /* this targets the default scrollbar (compulsory) */
	.parent::-webkit-scrollbar-thumb {
	    background: #004AC1;
	    border-radius: 10px;
	}
	.parent::-webkit-scrollbar-track {
		width: 10px;
	    background-color: #004AC1;
	    box-shadow: inset 0px 0px 0px 3px #56ABE3;
	} /* the new scrollbar will have a flat appearance with the set background color */
	.parent::-webkit-scrollbar-track-piece{
		width: 2px;
	    background-color: none;
	}
	 
	.parent::-webkit-scrollbar-button {
	      background-color: none;
	} /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */
	 
	.parent::-webkit-scrollbar-corner {
	      background-color: none;
	}

</style>
@php
$doctor = auth()->user();
$idTipoDoctor = Sis_medico\User_espe::where('usuid',$doctor->id)->first();
@endphp
<div class="box " style="border: 2px solid #004AC1; background-color: white;">
	<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;">
		<div class="row">
			<div class="col-md84 col-sm-8 col-12">
			    <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;padding-top: 10px">
                    <b>HISTORIAL ORDENES DE PROCEDIMIENTOS FUNCIONALES</b>
				</h1>   
		    </div>
		    <div class="col-md-4 col-sm-4 col-12" style="padding-left: 0px">
			    <div style="margin-bottom: 5px;text-align: left; margin-left: 5px; margin-right: 5px">
					<a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white; margin-left: 10px" onClick="agregar_orden_profuncional();">
		                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
			            	<div class="col-12" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
			            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
			            		<label style="font-size: 10px">AGREGAR ORDEN PROCEDIMIENTO</label>
			            	</div>
						</div>
					</a>
				</div>
	        </div>
		</div>
	    @if(!is_null($paciente))
			@php
	   			$xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age; 
			@endphp 
		<div class="row">	
			<div class="col-md-9" style="padding-top: 15px">
				<center>
				<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
			        <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
			        	{{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
	                </b>
				</h1>
				</center>
		    </div>
		    <div class="col-md-3" style="padding-top: 15px">
				<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
			        <b>
			        	EDAD: {{$xedad}} AÑOS
	                </b>
				</h1>
		    </div> 
		</div>	
	    @endif      
	</div>
	<div class="box-body" style="background-color: #56ABE3;">
        <div class="col-md-12">
	      	<div class="row parent">
                <div class="col-md-12" id="nueva_orden" style="padding: 0">
            	</div>
                @foreach($listado_ordenes as $ordfun)

	                @php  $seguro = Sis_medico\Seguro::find($paciente->id_seguro); @endphp
		            
		            <div class="col-md-12">
		            	@php
		   	  			    
                            if(!is_null($ordfun->fecha_orden)){
				    		   $fecha_r =  Date('Y-m-d',strtotime($ordfun->fecha_orden));
				    	    }

		   	  			    if($ordfun->id_doctor != ""){
		                        $xdoctor = DB::table('users as us')->where('us.id',$ordfun->id_doctor)->first();
		                    }

                        @endphp
						@php
			                $fecha = substr($ordfun->fecha_orden,0,10);
			                $invert = explode( '-',$fecha);
			                $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
						@endphp
						@php
	  		                
	  		                if(!is_null($ordfun->id)){
   	  			  				$procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $ordfun->id) ->where('id_grupo_procedimiento','18')
                                ->first();
   	  			            }

                            $texto = ""; 
   	  			            if(!is_null($procedimiento_orden_tipo)){ 

	   	  			            $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $procedimiento_orden_tipo->id)->get();

	 	                        $mas = true;
	 	              
                                foreach($procedimiento_orden_proced as $value2)
          		                {
          		                  	$nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                                    if($mas == true){
		  							 $texto = $nombre_procedimiento->nombre;
		  							 $mas = false; 
                                    }
  							        else{
  						  	         $texto = $texto.' + '.$nombre_procedimiento->nombre;
  						  	        }

  						  	    }

                            }
                        @endphp
						<div class="box @if($fecha_r != date('Y-m-d')) collapsed-box  @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px;">
							<div class="box-header with-border" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">
								<div class="row">
									<div class="col-md-5">
                                        @if(!is_null($ordfun->fecha_orden))
                                            @php 
					                         $dia =  Date('N',strtotime($ordfun->fecha_orden)); 
					                         $mes =  Date('n',strtotime($ordfun->fecha_orden)); 
					                        @endphp
					                        <b>
					                        @if($dia == '1') Lunes 
			                                 @elseif($dia == '2') Martes
			                                 @elseif($dia == '3') Miércoles 
			                                 @elseif($dia == '4') Jueves 
			                                 @elseif($dia == '5') Viernes 
			                                 @elseif($dia == '6') Sábado 
			                                 @elseif($dia == '7') Domingo 
	                                        @endif
	                                         {{substr($ordfun->fecha_orden,8,2)}} de
	                                        @if($mes == '1') Enero 
				                                 @elseif($mes == '2') Febrero 
				                                 @elseif($mes == '3') Marzo 
				                                 @elseif($mes == '4') Abril 
				                                 @elseif($mes == '5') Mayo 
				                                 @elseif($mes == '6') Junio 
				                                 @elseif($mes == '7') Julio 
				                                 @elseif($mes == '8') Agosto 
				                                 @elseif($mes == '9') Septiembre 
				                                 @elseif($mes == '10') Octubre 
				                                 @elseif($mes == '11') Noviembre 
				                                 @elseif($mes == '12') Diciembre 
	                                        @endif
	                                        del {{substr($ordfun->fecha_orden,0,4)}}</b>  
                                        @endif
		                            </div>
		                            <div class="col-md-5">
		                            	<div>
											<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a): </span> 
											<span style="font-size: 12px">@if(!is_null($xdoctor->nombre1)){{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @endif</span>
									    </div>
                                    </div>
                                    <div class="col-md-1" style="color: white"> 
						                  @if(!is_null($ordfun->id)) 
						                    {{$ordfun->id}}
						                  @endif
                                    </div>
                                    <div class="pull-right box-tools" style="padding-top: 4px;">
			                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
			                            <i class="fa fa-plus"></i></button>
		                    	    </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-12">
                                  	<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimientos:</span>
								  </div>
                                  <div class="col-md-12">
                                    <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;" class="badge badge-primary">@if(!is_null($texto)) {{$texto}} @endif</span>
                                  </div>
                                </div>
                            </div>
                            <div class="box-body" style="background: white;">
                                <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
	                            	<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
						  					<div class="row">
							  					<div class="col-sm" style="margin-right: 10px">
							    					<div class="btn" style="color: white">
									    				<a class="fa fa-pencil-square-o " onclick="editar_orden_profuncional({{$ordfun->id}},'{{$paciente->id}}');" ><span style="font-size: 13px">&nbsp;Editar</span>
									    				</a>
								    				</div>
							    				</div>
							    				<div class="col-sm" style="text-align: center"> 
							  						<span >Detalle de Orden</span>
							  					</div>
							  					<div class="col-sm" style="text-align: right;padding-top: 6px">
									               <a class="btn btn-danger" onclick="descargar_orden_funcional({{$ordfun->id}});" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden</a>
									            </div>
									            <?php //dd($idTipoDoctor); ?>
												@if(!is_null($idTipoDoctor))
													@if($idTipoDoctor->espid == 5)
														<div class="col-sm" style="text-align: center;padding-top: 6px">
															<a class="btn btn-danger" id="{{$ordfun->id}}" onclick="imprimirCir({{$ordfun->id}});" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> CIR</a>
														</div>
													@endif
												@endif
                                            </div>
						  		        </div>
						  		        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden{{$ordfun->id}}">
                                            <div class="col-md-12" style="padding: 1px;">
							   	  			    <div class="row">
							   	  			    	<div class="col-md-8">
									                    @if(!is_null($fecha_invert))
									                      <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
									                      <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
									                      </label>
									                    @endif 
			                                        </div>
			                                    </div>
                                            </div>
                                            <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                	    <div class="col-md-8">
								   	  					<div>
								   	  						@if(!is_null($xdoctor)) 
																<span style="font-family: 'Helvetica general'; font-size: 12px">DOCTOR SOLICITANTE:</span>
																<label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$xdoctor->apellido1}} {{$xdoctor->apellido2}}
																      {{$xdoctor->nombre1}} {{$xdoctor->nombre2}}  
				                                                  </b>
				                                                </label>
			                                                @endif 
												        </div>	
					                                </div>
					                                <div class="col-md-4">
					                                	@if(!is_null($seguro)) 
											                <span style="font-family: 'Helvetica general';font-size: 12px">SEGURO:</span>
										                    <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
										                      <b> 
										                        {{$seguro->nombre}}
										                      </b>
										                    </label>
									                    @endif
			                                        </div>
			                                    </div>
                                            </div>
                                            <div class="col-12" style="padding-top: 5px"></div>
                                            <div class="col-md-12" style="padding: 1px;">
                                            	<div class="row">
                                            		<div class="col-md-12">
					                                	<div class="row">
					                                		<div class="col-md-12">
											                  <span style="font-family: 'Helvetica general';font-size: 12px">ANTECEDENTES PATOLOGICOS</span>
											                </div>
											                <div class="col-12">
											                  <span><?php echo $paciente->antecedentes_pat?></span>
											                </div>
									   	  			    </div>
				   	  		                        </div>
				   	  		                        <div class="col-12" style="padding-top: 5px"></div>
													<div class="col-md-12">
					                                	<div class="row">
					                                		<div class="col-md-12">
											                  <span style="font-family: 'Helvetica general';font-size: 12px">ANTECEDENTES FAMILIARES</span>
											                </div>
											                <div class="col-12">
											                  <span><?php echo $paciente->antecedentes_fam?></span>
											                </div>
									   	  			    </div>
				   	  		                        </div>
				   	  		                        <div class="col-12" style="padding-top: 5px"></div>
													<div class="col-md-12">
					                                	<div class="row">
					                                		<div class="col-md-12">
											                  <span style="font-family: 'Helvetica general';font-size: 12px">ANTECEDENTES QUIRURGICOS</span>
											                </div>
											                <div class="col-12">
											                  <span><?php echo $paciente->antecedentes_quir?></span>
											                </div>
									   	  			    </div>
				   	  		                        </div>
                                                </div>
                                            </div>
                                            <div class="col-12" style="padding-top: 5px"></div>
                                            <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">MOTIVO:</span>
									                </div>
									                <div class="col-12">
										  				<span><?php echo $ordfun->motivo_consulta?></span>
										  			</div>
									            </div>
				   	  		                </div>
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
									                </div>
									                <div class="col-12">
										  				<span><?php echo $ordfun->resumen_clinico?></span>
										  			</div>
									            </div>
				   	  		                </div>
				   	  		                @if(!is_null($ordfun->diagnosticos))
                                            <div class="col-md-12" style="padding: 1px;">
                                                <div class="row">
				                            		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
									                </div>
									                <div class="col-12">
										  				<span><?php echo $ordfun->diagnosticos?></span>
										  			</div>
									            </div>
									        </div>
									        @endif
                                            <div class="col-md-12" style="padding: 1px;">
				                                @if($texto != "")
					                                <div class="row">
					                                  	<div class="col-md-12">
					                                       <div style="background-color: #004AC1; color: white">
											                   <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS FUNCIONALES 
											                    </label>
											                </div>
					                                    </div>
					                                    <div class="col-12">
									  						<span>
									  					      {{$texto}}
									  						</span>
												  	    </div>
					                                </div>
				                                @endif
                                            </div>
                                            @if(!is_null($ordfun->observacion_medica))
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
									                </div>
									                <div class="col-12">
									                  <span><?php echo $ordfun->observacion_medica?></span>
									                </div>
							   	  			    </div>
				   	  		                </div>
				   	  		                @endif
				   	  		                @if(!is_null($ordfun->observacion_recepcion))
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span>
									                </div>
									                <div class="col-12">
									                  <span><?php echo $ordfun->observacion_recepcion?></span>
									                </div>
							   	  			    </div>
				   	  		                </div>
				   	  		                @endif
				   	  		                <div class="col-md_12" >
										        <center>
										            <div class="col-md-5" style="padding-top: 15px;text-align: center;">
										                <button style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="descargar_orden_funcional({{$ordfun->id}});"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Descargar Orden
										                </button>
										            </div>
										        </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
	            @endforeach
	        </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function imprimirCir(cedula) {
		window.open('{{url('imprimir/orden_hc4/funcional/cir')}}/'+cedula,'_blank');  
	}

function descargar_orden_funcional(id_or){
       window.open('{{url('imprimir/orden_hc4/funcional')}}/'+id_or,'_blank');  
    }
    //Funcion para crear una Orden de Procedimiento Funcional
    function agregar_orden_profuncional(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hc4_orden_proc_funcional',['tipo' => '1', 'paciente' => $paciente->id ])}}", 
			data: "",
			datatype: "html",
			success: function(datahtml){
				
                $("#nueva_orden").html(datahtml);
			
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    //Funcion para editar Orden de Porcedimiento Funcional
    function editar_orden_profuncional(id_orden,id_paciente){
    	$.ajax({
			type: "GET",
			url: "{{route('editar.orden_procedimiento_funcional')}}/"+id_orden+'/'+id_paciente, 
			data: "",
			datatype: "html",
			success: function(datahtml){
				$("#xorden"+id_orden).html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});	
	}
	


</script>

<script type="text/javascript">

function formato_012(id, id_orden, div) {

$.ajax({
  type: 'GET',
  url:"{{url('hc4/formato012')}}/"+id+"/"+id_orden,
  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
  datatype: 'json',
  data: $("#edit_orden").serialize(),
  success: function(data){
    $("#"+div).empty().html(data);
    //console.log(data);
  },
  error: function(data){
    console.log(data);
  }
});

}
  
</script>