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
@include('hc4.ordenes.partials.procedure_history_header_styles')
@php $xedad = isset($edad) ? (int) $edad : 0; @endphp
<div class="box hc4-procedure-orders" id="hc4-functional-orders">
	<div class="box-header with-border hc4-procedure-orders__header">
		<div class="hc4-procedure-orders__title">
			<span class="hc4-procedure-orders__icon"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
			<span>@lang('hc4.historialOrdenesProcedimientosFuncionales')</span>
		</div>
		<a class="btn btn-info hc4-procedure-orders__action" onClick="agregar_orden_profuncional();">
			<img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
			<span>@lang('hc4.agregarOrdenProcedimiento')</span>
		</a>
		@if(!is_null($paciente))
			<div class="hc4-procedure-orders__patient">
				<div class="hc4-procedure-orders__patient-name">
					<strong>@lang('hc4.paciente'):</strong>
					{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
					{{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
				</div>
				<div class="hc4-procedure-orders__age"><strong>@lang('hc4.edad'):</strong> {{$xedad}} @lang('hc4.anios')</div>
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
								$fecha_r = null;
								$xdoctor = null;
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
								$procedimiento_orden_tipo = null;
								if (!is_null($ordfun->id)) {
									$procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $ordfun->id)
										->where('id_grupo_procedimiento', '18')
										->first();
								}

								$nombresProcedimientos = [];
								if (!is_null($procedimiento_orden_tipo)) {
									$procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $procedimiento_orden_tipo->id)->get();
									foreach ($procedimiento_orden_proced as $value2) {
										$nombre_procedimiento = \Sis_medico\Procedimiento::find($value2->id_procedimiento);
										if (!is_null($nombre_procedimiento)) {
											$nombresProcedimientos[] = $nombre_procedimiento->nombre;
										}
									}
								}
								$texto = implode(' + ', $nombresProcedimientos);
							@endphp
							<div class="box hc4-procedure-order-card @if($fecha_r != date('Y-m-d')) collapsed-box @endif">
								@php
									$diasOrden = [1 => 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
									$mesesOrden = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
									$fechaOrdenTexto = '';
									if (!is_null($ordfun->fecha_orden)) {
										$fechaOrdenTimestamp = strtotime($ordfun->fecha_orden);
										$fechaOrdenTexto = $diasOrden[(int) date('N', $fechaOrdenTimestamp)].' '.date('d', $fechaOrdenTimestamp).' de '.$mesesOrden[(int) date('n', $fechaOrdenTimestamp)].' del '.date('Y', $fechaOrdenTimestamp);
									}
								@endphp
								<div class="box-header with-border hc4-procedure-order-summary hc4-procedure-order-summary--grid">
									<div class="hc4-procedure-order-summary__date">{{$fechaOrdenTexto}}</div>
									<div class="hc4-procedure-order-summary__doctor">
										<strong>Dr (a):</strong>
										@if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @else No asignado @endif
									</div>
									<div class="hc4-procedure-order-summary__right">
										<div class="hc4-procedure-order-summary__procedures">
											<span>{{trim($texto) !== '' ? $texto : 'Sin procedimientos seleccionados'}}</span>
										</div>
										<div class="hc4-procedure-order-summary__tools box-tools">
											<span class="hc4-procedure-order-summary__id">Orden #{{$ordfun->id}}</span>
											<button type="button" class="btn btn-sm hc4-procedure-order-summary__toggle" data-widget="collapse" title="Mostrar u ocultar orden">
												<i class="fa fa-plus"></i>
											</button>
										</div>
									</div>
								</div>
                            <div class="box-body" style="background: white;">
                                <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
								<div class="box hc4-procedure-order-detail">
									<div class="box-header with-border hc4-procedure-order-detail__header">
										<div class="row hc4-procedure-order-detail__header-row">
							  					<div class="col-3" style="margin-right: 10px">
							    					<div class="btn" style="color: white">
											<a class="fa fa-pencil-square-o " onclick="editar_orden_profuncional({{$ordfun->id}},'{{$paciente->id}}', this);" ><span style="font-size: 13px">&nbsp;@lang('hc4.editar')</span>
									    				</a>
								    				</div>
							    				</div>
							    				<div class="col-4" style="text-align: center"> 
											<span class="sr-only">@lang('hc4.detalleOrden')</span>
							  					</div>
							  					<div class="col-4" style="text-align: right;padding-top: 6px">
											<a class="btn btn-danger" onclick="descargar_orden_funcional({{$ordfun->id}});" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.descargarOrden')</a>
									            </div>
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
                                            <div class="col-md-12" style="padding: 1px;">
				                                @if($texto != "")
					                                <div class="row">
					                                  	<div class="col-md-12">
													<div class="hc4-procedure-section-title">
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


	    //Funcion para crear una Orden de Procedimiento Funcional
	    function agregar_orden_profuncional(){
			var $contenedor = $("#nueva_orden");
			if ($contenedor.children().length) {
				$contenedor.stop(true, true).slideToggle(160);
				return;
			}

			var $accion = $("#hc4-functional-orders .hc4-procedure-orders__action").first();
			var contenidoOriginal = $accion.html();
			$accion.addClass('disabled').css('pointer-events', 'none').html('<i class="fa fa-spinner fa-spin"></i><span>Cargando...</span>');
			$.ajax({
			async: true,
			type: "POST",
			url: "{{route('hc4_orden_proc_funcional',['tipo' => '1', 'paciente' => $paciente->id ])}}", 
			data: {'_token': '{{ csrf_token() }}'},
			datatype: "html",
				success: function(datahtml){
					$contenedor.hide().html(datahtml).slideDown(160);

				},
				error:  function(){
					$contenedor.html('<div class="alert alert-danger">No se pudo abrir el selector de procedimientos.</div>');
				},
				complete: function(){
					$accion.removeClass('disabled').css('pointer-events', '').html(contenidoOriginal);
				}
			});
	    }

    //Funcion para editar Orden de Porcedimiento Funcional
	function editar_orden_profuncional(id_orden,id_paciente,boton){
		var $boton = $(boton);
		var contenidoOriginal = $boton.html();
		if (window.tinymce) {
			$("#xorden"+id_orden).find('[id]').each(function() {
				var editor = tinymce.get(this.id);
				if (editor) { editor.remove(); }
			});
		}
		$boton.addClass('disabled').css('pointer-events', 'none').html('<i class="fa fa-spinner fa-spin"></i> Cargando');
		$("#xorden"+id_orden).html('<div style="padding:25px;text-align:center;color:#087eae"><i class="fa fa-spinner fa-spin"></i> Cargando edición...</div>');
		$.ajax({
			type: "GET",
			url: "{{route('editar.orden_procedimiento_funcional')}}/"+id_orden+'/'+id_paciente, 
			data: "",
			datatype: "html",
			success: function(datahtml){
				$("#xorden"+id_orden).html(datahtml);
			},
			error:  function(){
				$("#xorden"+id_orden).html('<div class="alert alert-danger">No se pudo cargar la edición.</div>');
			},
			complete: function(){
				$boton.removeClass('disabled').css('pointer-events', '').html(contenidoOriginal);
			}
		});	
	}


	//Descarga Orden de Procedimiento Funcional
	function descargar_orden_funcional(id_or){
       window.open('{{url('imprimir/orden_hc4/funcional')}}/'+id_or,'_blank');  
    }



</script>
