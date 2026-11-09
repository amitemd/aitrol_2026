<style type="text/css">
    .parent{
		overflow-y:auto;
		overflow-x:hidden;
		max-height:600px;
		min-height:0;
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

	#area_trabajo .hc4-image-orders > .hc4-image-orders__header {
		display: grid !important;
		grid-template-areas: "title patient action";
		grid-template-columns: minmax(260px, 1fr) minmax(380px, 1.4fr) auto;
		align-items: center;
		gap: 18px;
		min-height: 86px;
		padding: 14px 20px !important;
		background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
	}

	#area_trabajo .hc4-image-orders__header::before,
	#area_trabajo .hc4-image-orders__header::after,
	#area_trabajo .hc4-image-order-summary::before,
	#area_trabajo .hc4-image-order-summary::after,
	#area_trabajo .hc4-image-order-detail__header::before,
	#area_trabajo .hc4-image-order-detail__header::after {
		display: none !important;
		content: none !important;
	}

	.hc4-image-orders__title {
		grid-area: title;
		display: flex;
		align-items: center;
		gap: 12px;
		min-width: 0;
		font-size: 16px;
		font-weight: 700;
		line-height: 1.25;
	}

	.hc4-image-orders__title-icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		flex: 0 0 40px;
		width: 40px;
		height: 40px;
		border: 1px solid rgba(255,255,255,.35);
		border-radius: 10px;
		background: rgba(255,255,255,.12);
		font-size: 18px;
	}

	#area_trabajo .hc4-image-orders__action {
		grid-area: action;
		justify-self: end;
		display: inline-flex !important;
		align-items: center;
		justify-content: center;
		gap: 8px;
		width: max-content !important;
		max-width: 290px;
		height: auto !important;
		min-height: 40px;
		margin: 0 !important;
		padding: 8px 13px !important;
		border: 1px solid rgba(255,255,255,.38) !important;
		border-radius: 9px !important;
		background: rgba(255,255,255,.13) !important;
		color: #fff !important;
		box-shadow: none !important;
		font-size: 13px;
		font-weight: 600;
		line-height: 1.2;
		white-space: nowrap;
		cursor: pointer;
		appearance: none;
	}

	#area_trabajo .hc4-image-orders__action:hover,
	#area_trabajo .hc4-image-orders__action:focus {
		background: rgba(255,255,255,.23) !important;
		color: #fff !important;
		text-decoration: none;
	}

	#area_trabajo .hc4-image-orders__action img {
		position: static !important;
		width: 19px !important;
		height: 19px !important;
		margin: 0 !important;
	}

	.hc4-image-orders__patient {
		grid-area: patient;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 18px;
		min-width: 0;
		font-size: 14px;
		line-height: 1.35;
	}

	.hc4-image-orders__patient-name {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.hc4-image-orders__age {
		flex: 0 0 auto;
		white-space: nowrap;
	}

	.hc4-image-orders__empty {
		padding: 32px 18px;
		border: 1px dashed #b9d8e3;
		border-radius: 11px;
		background: #fff;
		color: #587080;
		font-size: 14px;
		text-align: center;
	}

	.hc4-image-order-card {
		overflow: hidden;
		margin-bottom: 18px !important;
		border: 1px solid #1aa4b0 !important;
		border-radius: 15px !important;
		background: #fff !important;
		box-shadow: 0 8px 22px rgba(5,84,126,.09);
	}

	#area_trabajo .hc4-image-order-card > .hc4-image-order-summary {
		display: grid !important;
		grid-template-columns: minmax(220px, 1fr) minmax(220px, 1fr) minmax(360px, 1.25fr);
		align-items: center;
		gap: 18px;
		min-height: 72px;
		padding: 12px 18px !important;
		background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
		color: #fff !important;
	}

	.hc4-image-order-summary__date,
	.hc4-image-order-summary__doctor,
	.hc4-image-order-summary__procedures {
		min-width: 0;
		font-size: 13px;
		line-height: 1.4;
	}

	.hc4-image-order-summary__doctor { text-align: center; }

	.hc4-image-order-summary__procedures span {
		display: block;
		overflow: hidden;
		margin-top: 3px;
		color: #fff;
		font-size: 11px;
		font-weight: 400;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.hc4-image-order-summary__tools {
		display: flex;
		align-items: center;
		gap: 9px;
	}

	.hc4-image-order-summary__right {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		gap: 14px;
		min-width: 0;
	}

	.hc4-image-order-summary__right .hc4-image-order-summary__procedures {
		min-width: 0;
		text-align: right;
	}

	.hc4-image-order-summary__id {
		padding: 7px 10px;
		border-radius: 8px;
		background: rgba(0,61,105,.25);
		font-size: 12px;
		font-weight: 700;
		white-space: nowrap;
	}

	#area_trabajo .hc4-image-order-summary__toggle {
		width: 38px !important;
		height: 38px !important;
		min-height: 38px !important;
		margin: 0 !important;
		padding: 0 !important;
		border: 1px solid rgba(255,255,255,.4) !important;
		border-radius: 9px !important;
		background: rgba(255,255,255,.13) !important;
		color: #fff !important;
	}

	.hc4-image-order-card > .box-body { padding: 18px !important; }

	.hc4-image-order-detail {
		overflow: hidden;
		margin: 0 !important;
		border: 1px solid #25a7b4 !important;
		border-radius: 13px !important;
		background: #fff !important;
	}

	#area_trabajo .hc4-image-order-detail > .hc4-image-order-detail__header {
		display: grid !important;
		grid-template-columns: minmax(150px, 1fr) minmax(220px, 2fr) minmax(180px, 1fr);
		align-items: center;
		gap: 14px;
		min-height: 64px;
		padding: 10px 16px !important;
		background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
		color: #fff !important;
	}

	.hc4-image-order-detail__title {
		text-align: center;
		font-size: 16px;
		font-weight: 700;
	}

	.hc4-image-order-detail__action--right { text-align: right; }

	#area_trabajo .hc4-image-order-detail__button {
		display: inline-flex !important;
		align-items: center;
		justify-content: center;
		gap: 7px;
		width: auto !important;
		height: auto !important;
		min-height: 40px;
		margin: 0 !important;
		padding: 8px 13px !important;
		border: 1px solid rgba(255,255,255,.42) !important;
		border-radius: 9px !important;
		background: rgba(255,255,255,.13) !important;
		color: #fff !important;
		box-shadow: none !important;
		font-size: 13px;
		line-height: 1.2 !important;
		white-space: nowrap;
		cursor: pointer;
	}

	#area_trabajo .hc4-image-order-detail__button:hover,
	#area_trabajo .hc4-image-order-detail__button:focus {
		background: rgba(255,255,255,.24) !important;
		color: #fff !important;
		text-decoration: none;
	}

	#area_trabajo .hc4-image-order-detail__button > i {
		position: static !important;
		display: inline-block !important;
		width: auto !important;
		height: auto !important;
		margin: 0 !important;
		padding: 0 !important;
		background: transparent !important;
		font-size: 15px !important;
	}

	@media (max-width: 1100px) {
		#area_trabajo .hc4-image-orders > .hc4-image-orders__header {
			grid-template-areas: "title action" "patient patient";
			grid-template-columns: 1fr auto;
		}
		.hc4-image-orders__patient {
			grid-column: 1 / -1;
			justify-content: flex-start;
		}
		#area_trabajo .hc4-image-order-card > .hc4-image-order-summary {
			grid-template-columns: 1fr 1fr;
		}
		.hc4-image-order-summary__right { grid-column: 1 / -1; }
	}

	@media (max-width: 700px) {
		#area_trabajo .hc4-image-orders > .hc4-image-orders__header {
			display: flex !important;
			flex-direction: column;
			align-items: stretch;
		}
		#area_trabajo .hc4-image-orders__action { width: 100% !important; }
		.hc4-image-orders__patient {
			align-items: flex-start;
			flex-direction: column;
			gap: 5px;
		}
		.hc4-image-orders__patient-name { white-space: normal; }
		#area_trabajo .hc4-image-order-card > .hc4-image-order-summary,
		#area_trabajo .hc4-image-order-detail > .hc4-image-order-detail__header {
			display: flex !important;
			flex-direction: column;
			align-items: stretch;
		}
		.hc4-image-order-summary__procedures span { white-space: normal; }
		.hc4-image-order-summary__right {
			align-items: flex-start;
			flex-direction: column;
		}
		.hc4-image-order-summary__right .hc4-image-order-summary__procedures { text-align: left; }
		.hc4-image-order-detail__title { text-align: left; }
		.hc4-image-order-detail__action--right { text-align: left; }
		#area_trabajo .hc4-image-order-detail__button { width: 100% !important; }
	}

</style>

@php
	$xedad = isset($edad) ? (int) $edad : 0;
@endphp
<div class="box hc4-image-orders" style="background-color: white;">
	<div class="box-header with-border hc4-image-orders__header">
		<div class="hc4-image-orders__title">
			<span class="hc4-image-orders__title-icon"><i class="fa fa-picture-o" aria-hidden="true"></i></span>
			<span>@lang('hc4.historialOrdenesImagenes')</span>
		</div>
		<button type="button" class="hc4-image-orders__action" onClick="agregar_orden_proimagenes('{{$paciente->id}}');">
			<img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
			<span>@lang('hc4.agregarOrdenProcedimiento')</span>
		</button>
		@if(!is_null($paciente))
			<div class="hc4-image-orders__patient">
				<div class="hc4-image-orders__patient-name">
					<strong>@lang('hc4.paciente'):</strong>
					{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
					{{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
				</div>
				<div class="hc4-image-orders__age"><strong>@lang('hc4.edad'):</strong> {{$xedad}} @lang('hc4.anios')</div>
			</div>
		@endif
	</div>
	<div class="box-body" style="background-color: #56ABE3;">
        <div class="col-md-12">
	      	<div class="row parent">
				<div class="col-md-12" id="nueva_orden" style="padding: 0">
				</div>
				@if(count($listado_ordImagenes) === 0)
					<div class="col-md-12">
						<div class="hc4-image-orders__empty">No existen órdenes de imágenes registradas para este paciente.</div>
					</div>
				@endif
		            @foreach($listado_ordImagenes as $ordimag)

	                @php  $seguro = Sis_medico\Seguro::find($paciente->id_seguro); @endphp

		            <div class="col-md-12">
					@php
						$fecha_r = null;
						$xdoctor = null;
						if(!is_null($ordimag->fecha_orden)){
				    		   $fecha_r =  Date('Y-m-d',strtotime($ordimag->fecha_orden));
				    	    }

                            if($ordimag->id_doctor != ""){
		                        $xdoctor = DB::table('users as us')->where('us.id',$ordimag->id_doctor)->first();
		                    }
						@endphp
						@php
			                $fecha = substr($ordimag->fecha_orden,0,10);
			                $invert = explode( '-',$fecha);
			                $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
						@endphp
						@php
							$procedimiento_orden_tipo = null;
							if (!is_null($ordimag->id)) {
								$procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $ordimag->id)
									->where('id_grupo_procedimiento', '20')
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
						<div class="box hc4-image-order-card @if($fecha_r != date('Y-m-d')) collapsed-box @endif">
							<div class="box-header with-border hc4-image-order-summary">
								<div class="hc4-image-order-summary__date">
                                        @if(!is_null($ordimag->fecha_orden))
                                            @php 
					                         $dia =  Date('N',strtotime($ordimag->fecha_orden)); 
					                         $mes =  Date('n',strtotime($ordimag->fecha_orden)); 
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
	                                         {{substr($ordimag->fecha_orden,8,2)}} de
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
	                                        del {{substr($ordimag->fecha_orden,0,4)}}</b>  
                                        @endif
		                            </div>
		                            <div class="hc4-image-order-summary__doctor">
									<strong>Dr (a):</strong>
									@if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @else No asignado @endif
                                    </div>
                                    <div class="hc4-image-order-summary__right">
									<div class="hc4-image-order-summary__procedures">
										<strong>Procedimientos:</strong>
										<span>{{trim($texto) !== '' ? $texto : 'Sin procedimientos seleccionados'}}</span>
									</div>
									<div class="hc4-image-order-summary__tools box-tools">
										<span class="hc4-image-order-summary__id">Orden #{{$ordimag->id}}</span>
										<button type="button" class="btn btn-sm hc4-image-order-summary__toggle" data-widget="collapse" title="Mostrar u ocultar orden">
											<i class="fa fa-plus"></i>
										</button>
									</div>
								</div>
                            </div>
                            <div class="box-body" style="background: white;">
                                <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
								<div class="box hc4-image-order-detail">
                                        <div class="box-header with-border hc4-image-order-detail__header">
										<div>
											<button type="button" class="hc4-image-order-detail__button" onclick="editar_orden_proimagenes({{$ordimag->id}},'{{$paciente->id}}');">
												<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												<span>@lang('hc4.editar')</span>
											</button>
										</div>
										<div class="hc4-image-order-detail__title">@lang('hc4.detalleOrden')</div>
										<div class="hc4-image-order-detail__action--right">
											<button type="button" class="hc4-image-order-detail__button" onclick="descargar_orden_imagenes({{$ordimag->id}});">
												<i class="glyphicon glyphicon-download-alt" aria-hidden="true"></i>
												<span>@lang('hc4.descargarOrden')</span>
											</button>
										</div>
										</div>
						  		        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden{{$ordimag->id}}">
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
										  				<span><?php echo $ordimag->motivo_consulta?></span>
										  			</div>
									            </div>
				   	  		                </div>
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                    <div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
									                </div>
									                <div class="col-12">
										  				<span><?php echo $ordimag->resumen_clinico?></span>
										  			</div>
									            </div>
				   	  		                </div>
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
									                </div>
									                <div class="col-12">
										  				<span><?php echo $ordimag->diagnosticos?></span>
										  			</div>
									            </div>
									        </div>
				   	  		                <div class="col-md-12" style="padding: 1px;">
				                                @if($texto != "")
					                                <div class="row">
					                                  	<div class="col-md-12">
					                                       <div style="background-color: #004AC1; color: white">
											                   <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS IMAGENES 
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
                                            @if(!is_null($ordimag->observacion_medica))
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
									                </div>
									                <div class="col-12">
									                  <span><?php echo $ordimag->observacion_medica?></span>
									                </div>
							   	  			    </div>
				   	  		                </div>
				   	  		                @endif
				   	  		                @if(!is_null($ordimag->observacion_recepcion))
				   	  		                <div class="col-md-12" style="padding: 1px;">
			                                	<div class="row">
			                                		<div class="col-md-12">
									                  <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span>
									                </div>
									                <div class="col-12">
									                  <span><?php echo $ordimag->observacion_recepcion?></span>
									                </div>
							   	  			    </div>
				   	  		                </div>
				   	  		                @endif
				   	  		                <div class="col-md_12" >
										        <center>
										            <div class="col-md-5" style="padding-top: 15px;text-align: center;">
										                <button style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="descargar_orden_imagenes({{$ordimag->id}});"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Descargar Orden
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


    //Funcion para crear una Orden de Procedimiento Imagenes
    function agregar_orden_proimagenes(id_paciente){
    	
        $.ajax({
			//async: true,
				@if(!empty($contextoOdontologia))
				type: "GET",
				url: "{{route('odontologia.ordenes.imagenes.crear',['id_paciente' => $paciente->id])}}",
				data: "",
				@else
				type: "POST",
				url: "{{route('hc4_orden_proc_imagenes',['tipo' => '2', 'paciente' => $paciente->id])}}",
				data: {'_token': '{{ csrf_token() }}'},
				@endif
			datatype: "html",
			success: function(datahtml){
               
                $("#nueva_orden").html(datahtml);
               
            },
			error:  function(){
				alert('error al cargar');
			}
	    });

    }


    //Funcion para editar Orden de Imagenes
    function editar_orden_proimagenes(id_orden,id_paciente){
    	$.ajax({
			type: "GET",
				@if(!empty($contextoOdontologia))
				url: "{{url('odontologia/ordenes/imagenes/editar')}}/"+id_orden+'/'+id_paciente,
				@else
				url: "{{route('editar.orden_procedimiento_imagenes')}}/"+id_orden+'/'+id_paciente,
				@endif
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

	//Descarga Orden de Procedimiento Imagenes
	function descargar_orden_imagenes(id_or){
       window.open('{{url('imprimir/orden_hc4/imagenes')}}/'+id_or,'_blank');  
    }



</script>
