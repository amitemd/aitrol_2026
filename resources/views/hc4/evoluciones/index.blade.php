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
	}
</style>


<div id="area_index"  class="container-fluid" style="padding-left: 8px" >
	<div class="row">
		<div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px; ">
			<div class="col-md-12" style="border: 2px solid #004AC1; margin-left: 0px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px; background-color: #56ABE3">
				<h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
	            	<img style="width: 35px; margin-left: 15px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/evoluciones.png"> 
	             	<b>EVOLUCIONES</b>
			    </h1>   
			    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
			    	
					<div class="parent"   style="margin-left: 0px;   margin-bottom: 10px ; height: 450px " >
					    <div style=" margin-right: 30px;" >
					    <!-- DIV DE LAS EVOLUCIONES -->
					   		@foreach ($evoluciones as $evoluciones) 
						    	<div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px" >
					    			<div class="box-header ">
						    			<div class="row" style="margin-top: 6px;background-color: #004AC1;margin-right: 0px; color: white; margin-left: 0px">
						    				<div class="col-md-5 col-sm-4 col-4" style="margin-left: 0px; ">
						    					<div class="btn" style="color: white">
								    				<a class="fa fa-pencil-square-o "  onclick="editar_evoluciones({{$evoluciones->id}}, {{$id_agenda}});" ><span style="font-size: 13px">&nbsp;Editar</span>
								    				</a>
							    				</div>
						    				</div> 
						    				<div class="col-md-6 col-sm-6 col-6" style="margin-left: 6px; padding-top: 10px">
						    					<!--@if(!is_null($evoluciones))
						                            @php
						                              $fecha = substr($evoluciones->fechaini,0,10);
						                              $invert = explode( '-',$fecha);
						                              $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
						                            @endphp
						                            {{$fecha_invert}}
						                            <br>
						                        @endif-->
						                        @if(!is_null($evoluciones))
							                        @php 
							                        $dia =  Date('N',strtotime($evoluciones->fechaini)); 
							                        $mes =  Date('n',strtotime($evoluciones->fechaini)); @endphp
			                                   		<span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
			                                        @if($dia == '1') Lunes 
				                                         @elseif($dia == '2') Martes
				                                         @elseif($dia == '3') Miércoles 
				                                         @elseif($dia == '4') Jueves 
				                                         @elseif($dia == '5') Viernes 
				                                         @elseif($dia == '6') Sábado 
				                                         @elseif($dia == '7') Domingo 
			                                        @endif 
			                                         	{{substr($evoluciones->fechaini,8,2)}} de 
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
			                                         	del {{substr($evoluciones->fechaini,0,4)}}</span>
		                                        @endif
						    				</div>
						    				<div class="pull-right box-tools " style="padding-top: 4px;">
						                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
						                        <i class="fa fa-minus"></i>
						                        </button>
						                    </div>
						   				</div>
						   				
					   				</div>
					   				<div class="box-body" id="evolucion{{$evoluciones->id}}" >  
							    		<table style="margin-left: 20px; margin-top: 10px; margin-bottom: 10px; margin-right: 30px;" >
							    			<tr style="height: 40px" >
							    				<td style="width: 300px;" ><span style="font-family: 'Helvetica general';">Fecha</span></td>
							    				<td style="width: 200px"><span style="font-family: 'Helvetica general';">Hora</span></td>
							    			</tr>
							    			<tr>
							    				<td>
							    					@if(!is_null($evoluciones))
						                         @php $dia =  Date('N',strtotime($evoluciones->fechaini)); $mes =  Date('n',strtotime($evoluciones->fechaini)); @endphp
				                                <b>
				                                    @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($evoluciones->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($evoluciones->fechaini,0,4)}}</b> 
				                                 @endif
							    				</td>
							    				<td> @if(!is_null($evoluciones)) {{substr($evoluciones->fechaini,10,10)}} @endif</td>
							    			</tr>
							    			<!--
							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Seguro</span></td>
							    			</tr>
							    			@php
							    				$hc_seguro = \Sis_medico\Seguro::find($evoluciones->id_seguro)->first();
							    			@endphp
							    			@if(!is_null($hc_seguro))
							    			<tr style="">
							    				<td style="" colspan="2">{{$hc_seguro->nombre}}</td>
							    			</tr>
						    				@endif-->


							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Procedimiento</span></td>
							    			</tr>
							    			@php
						    					$procedimiento = \Sis_medico\hc_procedimientos::find($evoluciones->hc_id_procedimiento);
						    					$nprocedimiento = null;
						    					$texto = null;
						    					if(!is_null($procedimiento->id_procedimiento_completo)){
						    						$nprocedimiento = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
						    						$texto =  $nprocedimiento->nombre_general;
						    					}else{
						    						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
													$mas = true; 

													foreach($adicionales as $value2)
													{
														if($mas == true){
														 $texto = $texto.$value2->procedimiento->nombre  ;
														 $mas = false; 
														 }
														else{
													  	 $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
													  	 }					  						
													}
						    					}
							    			@endphp
							    			<tr style="">
							    				<td style="" colspan="2">{{$texto}}</td>
							    			</tr>


							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Motivo</span></td>
							    			</tr>
							    			<tr>
							    				<td style="" colspan="2">@if(!is_null($evoluciones)){{strip_tags($evoluciones->motivo)}}@endif</td>
							    			</tr>
							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span></td>
							    				<td><span style="font-family: 'Helvetica general';">Seguro</span></td>
							    			</tr>
							    			<tr>
							    				@php $procedimiento_3333 = null; @endphp
							    				@if(!is_null($evoluciones))
							    				@php
				                                    $procedimiento_3333 = Sis_medico\hc_procedimientos::find($evoluciones->hc_id_procedimiento);
				                                    //dd($procedimiento_3333->doctor);
				                                @endphp
				                                @endif
							    				<td>
							    					<b>@if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_doctor_examinador))Dr. {{$procedimiento_3333->doctor->nombre1}} {{$procedimiento_3333->doctor->apellido1}}@else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif @else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif</b>
							    				</td>
							    				<td>
							    					@if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_seguro)){{$procedimiento_3333->seguro->nombre}}@else{{$agenda->snombre}}@endif @else{{$agenda->snombre}}@endif
							    				</td>
							    				
							    			</tr>
							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Observaci&oacute;n</span></td>
							    			</tr>
							    			<tr>
							    				<td colspan="2"> 

							    				@if(!is_null($procedimiento_3333)){{strip_tags($procedimiento_3333->observaciones)}}
						                        
						                        @endif
						                        </td>
							    			</tr>
							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Evoluci&oacute;n</span></td>
							    			</tr>
							    			<tr>
							    				<td colspan="2">
						                         @if(!is_null($evoluciones))
						                               
						                        <p> <?php echo  strip_tags($evoluciones->cuadro_clinico); ?></p>
						                        @endif 
						                        </td>
							    			</tr>

							    			<tr style="height: 40px">
							    				<td colspan="2"><span style="font-family: 'Helvetica general';">Resultados de Exámenes y Procedimientos Diagnósticos </span></td>
							    			</tr>
							    			<tr>
							    				<td colspan="2">
						                         @if(!is_null($evoluciones))
						                               
						                        <p> <?php echo  strip_tags($evoluciones->resultado); ?></p>
						                        @endif 
						                        </td>
							    			</tr>

							    			<tr style="height: 40px">
							    				<td><span style="font-family: 'Helvetica general';">Diagn&oacute;stico</span></td>
							    			</tr>
							    			<tr>
							    				<td colspan="2">

							    				@php $hc_cie10 = null ;  @endphp
							    				@if(!is_null($evoluciones))
							    				@php 
							    					$hc_cie10 = DB::table('hc_cie10')->where('hc_id_procedimiento',$evoluciones->hc_id_procedimiento)->get();
							    				@endphp
							    				@endif
							    				
							    					@if(!is_null($hc_cie10))
				                                    @foreach($hc_cie10 as $cie10)
				                                    @php $c10 = DB::table('cie_10_3')->where('id',$cie10->cie10)->first(); @endphp
				                                    @if(!is_null($c10))
				                                    <tr><td colspan="4">
				                                    {{$c10->descripcion}}
				                                    </td></tr>
				                                    @endif 
				                                    @php $c10 = DB::table('cie_10_4')->where('id',$cie10->cie10)->first(); @endphp
				                                    @if(!is_null($c10))
				                                    <tr><td colspan="4">
				                                    {{$c10->descripcion}}
				                                    </td></tr>
				                                    @endif 
				                                    @endforeach 
				                                 	@endif
							    				</td>
							    			</tr>
							    		</table>
						    		</div>
						    	</div>
						    @endforeach
					    	<!--fin de foreach-->
					    </div>  
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function editar_evoluciones(id, id_agenda){
			$.ajax({
			type: "GET",
			url: "{{route('paciente.editar_evol')}}/"+id+'/'+id_agenda, 
			data: "",
			datatype: "html",
			success: function(datahtml){
				$("#evolucion"+id).html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});	
	}



	
</script> 