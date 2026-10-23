@extends('hc4.base2')
@section('action-content')

<style type="text/css">
	.boton-2{
		font-size: 9px ;
		width: 100%;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 35px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 7px;
		margin-bottom: 5px;
	}

	.boton-buscar{
		font-size: 14px ;
		width: 70%;
		height: 35px;
		background-color: #004AC1;
		color: white;
		text-align: center;

	}

	.btn_ordenes{
		font-size: 10px ;
		width: 100%;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 22px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 5px;


	}


	.btn_accion{
		font-size: 9px ;
		width: 95px;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 20px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 0px;

	}

	.recuadro{
			height: 200px;
			margin-bottom: 0px;


	}

	.cuerpo{
		font-size: 10px;
		font-weight: bold;

	}

	.fila1{
		background-color: #004AC1;
		height: 40px;
		color: white;
	}

	.fila2{
		background-color: #0081D5;
		height: 40px;
	}

	.fila3{
		background-color: #56ABE3;
		height: 40px;
	}

	.fila4{
		background-color: #004AC1;
		height: 40px;
	}

	.contenido_btn_ordenes{
		color: white;
		height: 20px;
		width: 20px;
		font-size: 12px;

	}
	.select2-selection__choice{
		background-color: red !important;
		border-color: red !important;
	}

	.btn-block{
      background-color: #004AC1;
    }
    .boton_burbuja{
    	color: white;
    	border-radius: 15px;
    	padding: 5px;
    	margin: 2px;
    	-moz-animation: 2s bote 1;
    	animation: 2s bote 1;
    	-webkit-transform: 2s bote 1;
    }
    .boton_burbuja span{
    	color: white;
    	margin: 20px;
    }
    @keyframes bote {
	  20%, 50%, 80% {
	  	transform: translateY(0);
	    -moz-transform: translateY(0);
	    -webkit-transform: translateY(0);
	  }

	  40% {
	  	transform: translateY(-30px);
	    -moz-transform: translateY(-30px);
	    -webkit-transform: translateY(-30px);
	  }

	  65% {
	  	transform: translateY(-15px);
	    -moz-transform: translateY(-15px);
	    -webkit-transform:  translateY(-15px);
	  }
	}
	.mostrar{
        position: fixed;
        bottom: 5px;
        right: 0;
        z-index: 999;
        width: 50%;
    }
</style>
<script type="text/javascript">
	
	
    function cargar_consulta(){
		$.ajax({
			type: "GET",
			url: "{{route('paciente.consulta', ['id_paciente' => $paciente->id])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){
				scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}

	function activar_frame(url_d){
        $('#div_frame').removeClass('oculto');
        $('#div_frame').addClass('mostrar');
		var divFrame= document.getElementById('div_frame');
		var mousePosition;
		var offset = [0,0];
		var isDown = false;
		divFrame.addEventListener('mousedown', function(e) {
			isDown = true;
			offset = [
				divFrame.offsetLeft - e.clientX,
				divFrame.offsetTop - e.clientY
			];
		}, true);
	
		document.addEventListener('mouseup', function() {
			isDown = false;
		}, true);
	
		document.addEventListener('mousemove', function(event) {
			event.preventDefault();
			if (isDown) {
				mousePosition = {
			
					x : event.clientX,
					y : event.clientY
			
				};
				divFrame.style.left = (mousePosition.x + offset[0]) + 'px';
				divFrame.style.top  = (mousePosition.y + offset[1]) + 'px';
			}
		}, true);

        document.getElementById('frame').src = url_d;
    }

    function cerrar_frame(){
        $('#div_frame').addClass('oculto');
        $('#div_frame').removeClass('mostrar');
        document.getElementById('frame').src = "https://google.com";
    }
</script>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>
</div>

<div class="modal fade" id="foto2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>

<div id="div_frame" class="col-md-4 oculto" style="background-color:white; height: 500px;">
    <button onclick="cerrar_frame();" style="background-color: white; color:red; width:100; text-align:right; font-size:30px;" class="btn"><b>X</b></button>
    <iframe id="frame" src="" style="height: 400px;width: 100%;" allow="camera *;microphone *"></iframe>
</div>
<form id="formulario1" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable" id="variable">
</form>


<form id="formulario2" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable2" id="variable2">
</form>


<form id="formulario3" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable3" id="variable3">
</form>

<form id="formulario4" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable4" id="variable4">
</form>

<section class="content" >
	<div class="container-fluid" >
		<div class="row">
		    <div class="col-md-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #124574,#0C8BEC,#124574); margin-bottom: 10px">
		     	<div class="row">
			        <div class="col-md-7" style="">
			            <h1 style="font-size: 15px; margin:0;">
			            	<img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png">
			             	<b>@lang('hc4.historiaClinicaPaciente')</b>
			            </h1>
			        </div>
			        <!-- <div class="col-md-5" style="padding-right: 0px;right: 0px; top: 5px">
				       <div class="row">
				            <div class="col-8">
				                <div class="input-group">
				                  	<input value="" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
				                </div>
				            </div>

				            <div class="col-4">
				              	<a class="btn btn-info boton-buscar" style="color: white ; background-color: #da291c;  border-radius: 5px; border: 2px solid white; "  > <i class="fa fa-search" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;</a>
				            </div>
				        </div
			        </div> -->
		        </div>
            </div>
		</div>
	</div>

	<div class="container-fluid" >
		<div class="row">
			<div class="col-md-7 area_1_dato"  style="padding-left: 5px; padding-right: 10px; " >
				<div class="row">
					<div class="col-md-12">
						<div style="border: 2px solid white; border-radius: 8px; margin-left: 8px; margin-bottom: 5px" >
							<div class="col-md-12 responsive_filiacion" style="" >
								<div class="row">
									<div class="col-lg-9 col-12" style="padding-left: 0px; padding-right: 15px;" >
										<label style="margin-top: 10px; color: #124574; font-family: arial ; margin-left: 20px" >
											<span style="font-family: 'Helvetica general';"><i class="fa fa-exclamation-circle"></i> @lang('hc4.datosPrincipalesPaciente_') </span>
										</label>
										<table class="detalle_tabla" style="margin-left: 20px; margin-bottom: 5px; text-align: left; margin-top: 5px"   >
											<tr>
												<td style="font-size: 11px; width: 300px"> <span style="font-family: 'Helvetica general';"> @lang('hc4.paciente') </span></td>
												<td style="font-size: 11px; width: 100px"> <span style="font-family: 'Helvetica general';">@lang('hc4.identificacion') </span></td>
												<td style="font-size: 11px; width: 60px"> <span style="font-family: 'Helvetica general';">@lang('hc4.edad') </span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">@lang('hc4.seguro') </span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">@lang('hc4.cortesia') </span></td>
											</tr>
											<tr>
												<td style="font-size: 16px; color: #da291c"> <span style="font-family: 'Helvetica general';">{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif </span></td>
												<td style="font-size: 11px"> {{$paciente->id}} </td>
												<td style="font-size: 11px"> {{$edad}} </td>
												<td style="font-size: 11px"> {{$paciente->seguro->nombre}} </td>
												<td style="font-size: 11px">

												<select id="cortesia_paciente" name="cortesia_paciente" class="form-control input-sm" required onchange="actualiza_cortesia();" style="background-color: #ccffcc; font-size: 11px">

												@php
													$paciente_cort = Sis_medico\cortesia_paciente::where('id', $paciente->id)->get()->first();
												@endphp

												@if(!is_null($paciente_cort))
							                      	<option @if($paciente_cort->cortesia=='NO'){{'selected '}}@endif value="NO">@lang('hc4.no')</option>
							                      	<option @if($paciente_cort->cortesia=='SI'){{'selected '}}@endif value="SI">@lang('hc4.si')</option>
							                    @else
							                    	<option value="NO" selected >@lang('hc4.no')</option>
							                    	<option value="SI" >@lang('hc4.si')</option>
							                    @endif
							                  </select>
												</td>
											</tr>
										</table>
										@php
											$paciente_d_doctor = \Sis_medico\Paciente_Doctor::where('id_paciente', $paciente->id)->first();
										@endphp
										@if(!is_null($paciente_d_doctor))
										<div class="col-md-11 has-error" style="padding: 1px; margin-left: 20px; margin-bottom: 10px">
			                                <div class="col-md-12" style="padding: 0px;">
			                                    <label for="ale_list" class="control-label" style="font-size: 14px; color: #da291c"><span style="font-family: 'Helvetica general';">@lang('hc4.pacienteparticulardr-') @if(isset($paciente_d_doctor->doctor)) {{$paciente_d_doctor->doctor->nombre1}} {{$paciente_d_doctor->doctor->apellido1}} @endif</span></label>
			                                </div>
                            			</div>
                            			@endif
										<div class="col-md-11 has-error" style="padding: 1px; margin-left: 20px; margin-bottom: 10px">
											<form  id="frm">
												<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
				                                <div class="col-md-12" style="padding: 0px;">
				                                    <label for="ale_list" class="control-label" style="color: orange; font-size: 11px"><span style="font-family: 'Helvetica general';">@lang('hc4.alergias')</span></label>
				                                </div>
				                                <div class="col-md-12 has-error" style="padding: 15px; padding-left: 0px; font-size: 11px">
				                                    <select id="ale_list" name="ale_list[]" class="form-control" multiple style="width: 100%;" >
				                                        @foreach($alergiasxpac as $ale_pac)
				                                        <option selected value="{{$ale_pac->id_principio_activo}}" >{{$ale_pac->principio_activo->nombre}}</option>
				                                        @endforeach
				                                    </select>
				                                </div>
			                                </form>
                            			</div>
										<label style=" color: black; font-size: 11px; margin-left: 20px">
											<span style="font-family: 'Helvetica general';"> @lang('hc4.observaciones') </span>
										</label>
										<form id="obs_id" >
											<input type="hidden" name="id_paciente"  value="{{$paciente->id}}">
											<input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->observacion}}" autocomplete="off" type="text" name="observacion" placeholder="@lang('hc4.observacionesGeneralesPaciente')">
											<label style=" color: black; font-size: 11px; margin-left: 20px">
											<span style="font-family: 'Helvetica general';"> @lang('hc4.habitos') </span>
										    </label>
										    <input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->alcohol}}" autocomplete="off" type="text" name="habitos" placeholder="@lang('hc4.habitosPaciente')">
											<div class="col-md-12" style="padding: 10px">
												<div class="col-md-12" style="padding-left: 0px; padding-right: 0px; text-align: center; ">
													<div class="row antecedentes_responsive">
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesPatologicos')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_patologicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_pat}}</textarea>
																	<!--<input style="width: 100%" type="text" name="an_patologicos" value="{{$paciente->antecedentes_pat}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesFamiliares')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12" >
																	<textarea rows="2" name="an_familiares" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_fam}}</textarea>
																	<!--<input style="width: 100%" type="text" name="an_familiares" value="{{$paciente->antecedentes_fam}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesQuirurgicos')</span></div>
																<div class="col-md-12 col-sm-6 col-12">
																<textarea rows="2" name="an_quirurgicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_quir}}</textarea>
																<!--<input style="width: 100%" type="text" name="an_quirurgicos" value="{{$paciente->antecedentes_quir}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>


											@php
												$observaciones_admin ="";
												if(!is_null($paciente_observaciones)>0){
													$observaciones_admin = $paciente_observaciones->observacion;
												}else{
													$observaciones_admin ="";
												}
											@endphp
											<label style=" color: black; font-size: 11px; margin-left: 20px">
												<span style="font-family: 'Helvetica general';">@lang('hc4.observacionesadministrativas')</span>
											</label>
										   	<input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();"value="{{$observaciones_admin}} " autocomplete="off" type="text" name="observacion_admin" placeholder="@lang('hc4.observacionesadministrativas')">




									    </form>
									</div>
									<div class="col-lg-3 col-12" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
										<div class="row">
											<div class="col-md-12 col-xs-6 col-6" style="margin-bottom: 15px">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_detalle_filiacion()">
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 10px; padding-right: 10px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/escudo.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
								                        		<label style="font-size: 10px">@lang('hc4.verDetallesFiliacion')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>

											<div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px;">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_nueva_receta()">
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 15px; padding-right: 15px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/receta.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
								                        		<label style="font-size: 10px">@lang('hc4.nuevaReceta')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>
											@if(Auth::user()->id == '9666666666')
											<!-- <div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px;">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_nueva_receta_publica()">
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 15px; padding-right: 15px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/receta.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
								                        		<label style="font-size: 10px">NUEVA RECETA PUBLICA</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div> -->
											@endif
											<div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px;">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" target="_blank" href="{{ route('paciente.historia', ['id' => $paciente->id]) }}" >
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 15px; padding-right: 15px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<i class="glyphicon glyphicon-download-alt"></i>
			                								</div>
								                        	<div class="col-9" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
								                        		<label style="font-size: 10px">@lang('hc4.historiaClinica')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							@php
						    	$pendoscopicos = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 0)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->count();

						    	$hc = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->join('historiaclinica', 'agenda.id', '=', 'historiaclinica.id_agenda')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 0)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->select('historiaclinica.hcid')
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->first();

						    	$rendoscopicos = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
						            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
						            ->where('gp.tipo_procedimiento', '0')
            						->where('hc_p.estado', '1')
						            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc')
						            ->count();

								$rendoscopicos2 = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->where('hc_proto.tipo_procedimiento', '0')

            						->where('hc_p.estado', '1')
						            ->select( 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion' , 'h.id_agenda as id_agenda')
						            ->count();
						    	$fendoscopicos = $pendoscopicos - $rendoscopicos - $rendoscopicos2;
						    @endphp
						    @php
						    	$pfuncional = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 1)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->count();

						    	$hc_funcional = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->join('historiaclinica', 'agenda.id', '=', 'historiaclinica.id_agenda')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 1)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->select('historiaclinica.hcid')
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->first();

						    	$rfuncional = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
						            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
						            ->where('gp.tipo_procedimiento', '1')
						            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc')
						            ->count();

								$rfuncional2 = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->where('hc_proto.tipo_procedimiento', '1')
						            ->select( 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion' , 'h.id_agenda as id_agenda')
						            ->count();
						    	$ffuncional  = $pfuncional  - $rfuncional - $rfuncional2;
						    @endphp
						    <div class="col-md-12">
						    	<input type="hidden" id="contador_endoscopicos" value="{{$fendoscopicos}}">
						    	<input type="hidden" id="contador_funcionales" value="{{$ffuncional}}">
						    	@if($fendoscopicos > 0)
						    		<a onclick="agregar_procedimiento_hc('endoscopico_id');"  class="btn-danger boton_burbuja">
						    			<span id="eendoscopico">@lang('hc4.elPacienteTiene') {{$fendoscopicos}} @if ($fendoscopicos > 1) @lang('hc4.procedimientosEndoscopicos') @else @lang('hc4.procedimientoEndoscopico') @endif @lang('hc4.porCrearEldiaHoyPresioneAtender')</span>
						    		</a>
						    		<input type="hidden" id="endoscopico_id" value="{{route('hc4_procedimiento.selecciona_procedimiento2',['tipo' => '0', 'paciente' => $paciente->id, 'hcid' => $hc->hcid ])}}">
						    		<br/>&nbsp;<br/>

						    	@endif
						    	@if($ffuncional > 0)
						    		<a onclick="agregar_procedimiento_hc('funcional_id');"  class="btn-danger boton_burbuja">
						    			<span id="efuncional">@lang('hc4.elPacienteTiene') {{$ffuncional}} @if ($ffuncional > 1) @lang('hc4.procedimientosFuncionales') @else @lang('hc4.procedimientoFuncional') @endif @lang('hc4.porCrearEldiaHoyPresioneAtender')</span>
						    		</a>
						    		<input type="hidden" id="funcional_id" value="{{route('hc4_procedimiento.selecciona_procedimiento2',['tipo' => '1', 'paciente' => $paciente->id, 'hcid' => $hc_funcional->hcid ])}}">
						    		<br>
						    	@endif

						    	@if(!is_null($hc))
						    		<input type="hidden" id="id_endoscopico" value="{{$hc->hcid}}">
								@else
									<input type="hidden" id="id_endoscopico" value="0">
								@endif
						    	@if(!is_null($hc_funcional))
						    		<input type="hidden" id="id_funcional" value="{{$hc_funcional->hcid}}">
						    	@else
									<input type="hidden" id="id_funcional" value="0">
								@endif
						    </div>
						    <br>
                            <!--Valida si tiene una consulta-->
                            @php
                               $xagenda = DB::table('agenda as a')
                                ->where('a.id_paciente',$paciente->id)
                                ->join('historiaclinica as h','h.id_agenda','a.id')
                                ->where('a.espid','<>','10')
                                ->where('a.proc_consul', 0)
                                ->orderBy('a.fechaini','desc')
                                ->where('a.estado_cita', 4)
                                ->whereBetween('a.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
                                ->select('h.*')
                                ->count();
                            @endphp
                            <div class="col-md-12">
                                @if($xagenda > 0)
                                <a onclick="cargar_consulta();" class="btn-danger boton_burbuja">
						    			<span id="pconsulta">@lang('hc4.elPacienteTiene') {{$xagenda}} @if ($xagenda > 1) @lang('hc4.consultas') @else @lang('hc4.consulta') @endif @lang('hc4.eldiaHoyPresioneAtender')</span>
						    	</a>
						    	<script type="text/javascript">
						    		$( document ).ready(function() {
									    cargar_consulta();
									});
						    	</script>
                                @endif
                            </div>
						    <br>
						</div>
					</div>
					<br>
					<div class="col-md-12 container-fluid" id="area_trabajo" style="padding-left: 23px">

					</div>
				</div>
			</div>
			<div id="area_trabajo_2" class="col-md-5" style="padding-left: 0px; padding-right: 0px; ">
				<div class="row" style="margin-right: 25px;">
					<!--parte 1-->
					<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
						<div class="row " style="margin-left: 10px;">
							<div class="col-md-6 col-lg-4" style="padding: 3px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila1">
		        						<div class="box-header">
					                        <div class="row" style="margin-left: 0px">
		        								<div style="margin-right: 5px">
		        									<img width="20px" src="{{asset('/')}}hc4/img/iconos/estudios.png">
		        								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;@lang('hc4.consulta-')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
						                          <button type="button" class="btn btn-box-tool"
						                          onclick="cargar_consulta();"
						                            style="color: white"><i class="fa fa-plus"></i>
						                          </button>
					                        </div>
		       						    </div>
		       						</div>

		   						 	<div class="box-body cuerpo" >

		   						 		<div class="row">
		   						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
		   						 		<div class="col-md-6" style="color: white"> @if(!is_null($consulta_nueva)) {{$consulta_nueva->hcid}} @endif</div>
		   						 		</div>
		   						 		@if(!is_null($consulta_nueva))
		   						 		@php
		   						 			$agenda_fecha_consulta = DB::table('agenda as a')->where('a.id', $consulta_nueva->id_agenda)->first();
		   						 			//dd($agenda_fecha_consulta);
		   						 		@endphp
		   						 		@if(!is_null($consulta_nueva->fecha_atencion))
					                        @php
					                        $dia =  Date('N',strtotime($consulta_nueva->fecha_atencion));
					                        $mes =  Date('n',strtotime($consulta_nueva->fecha_atencion));
					                        @endphp
				                       		<b>
				                            @if($dia == '1') @lang('hc4.lunes')
				                                 @elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
				                            @endif
				                             	{{substr($consulta_nueva->fecha_atencion,8,2)}} @lang('hc4.de')
				                            @if($mes == '1') @lang('hc4.enero')
				                                 @elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
				                            @endif
											@lang('hc4.del') {{substr($consulta_nueva->fecha_atencion,0,4)}}</b>
				                        @else
				                         	@php
					                        $dia =  Date('N',strtotime($agenda_fecha_consulta->fechaini));
					                        $mes =  Date('n',strtotime($agenda_fecha_consulta->fechaini));
					                        @endphp
				                       		<b>
				                            @if($dia == '1') @lang('hc4.lunes')
											@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
				                            @endif
				                             	{{substr($agenda_fecha_consulta->fechaini,8,2)}} @lang('hc4.de')
				                            @if($mes == '1') @lang('hc4.enero')
											@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
				                            @endif
											@lang('hc4.del') {{substr($agenda_fecha_consulta->fechaini,0,4)}}</b>
				                        @endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @php $doctor_nombre = null; @endphp
				                        @if(!is_null($consulta_nueva))
				                        	@if(!is_null($consulta_nueva->id_doctor_examinador))
						                        @php
						                        	//dd($consulta_nueva);
						                        	$doctor_nombre = DB::table('users as u')->where('u.id', $consulta_nueva->id_doctor_examinador)->first();
						                        	 //dd($doctor_nombre);
						                        @endphp
					                        @endif
				                            <b>@if(!is_null($consulta_nueva))

				                        			@if(!is_null($doctor_nombre))
				                        				Dr.	{{$doctor_nombre->nombre1}} {{$doctor_nombre->apellido1}}
				                        			@else
				                        				@if(!is_null($paciente->agenda->last()->historia_clinica))
				                        				Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
				                        				@endif
				                        			@endif
				                        		@else
				                        			Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} 	{{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
				                        		@endif</b>
				                       	 @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.evolucion') </span>
				                        <br>
				                      	@if(!is_null($consulta_nueva))
					                        @if(!is_null($consulta_nueva->cuadro_clinico))
						                        @if(strlen($consulta_nueva->cuadro_clinico)>= '30')
						                        	<?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30) . '...'; ?>
						                        @else
						                        	 <?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30); ?>
						                        @endif
						                    @endif
					                    @endif

			                   	    </div>
								</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila1">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									 <img width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.recetas-')</h3>
					                        	</div>
						                    </div>
					                        <div class="box-tools">
						                          <button type="button" class="btn btn-box-tool"
						                           onclick="cargar_recetas()"
						                           style="color: white"><i class="fa fa-plus"></i>
						                          </button>
					                        </div>
	           						    </div>
	           						</div>
           						 	<div class="box-body cuerpo " >

				                      <div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($hc_rec)) {{$hc_rec->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($hc_rec))


						                        @php
						                        	$dia =  Date('N',strtotime($hc_rec->fechaini));
						                        	$mes =  Date('n',strtotime($hc_rec->fechaini));
						                        @endphp

		                                        @if($dia == '1') @lang('hc4.lunes')
												@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif

	                                        	{{substr($hc_rec->fechaini,8,2)}} @lang('hc4.de')
	                                        	@if($mes == '1') @lang('hc4.enero')
	                                        	@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
	                                        	@lang('hc4.del') {{substr($hc_rec->fechaini,0,4)}}


				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.rp')</span>
				                         <br>

				                        @if(!is_null($hc_rec))
					                        @php
												if (strlen($hc_rec->rp) >= 150) {
												    $texto = substr(strip_tags($hc_rec->rp), 0, 150);
												    echo $texto . '...';
												} else {
												    echo strip_tags($hc_rec->rp);
												}
											@endphp
				                        @endif

				                       <br>
			                   	    </div>
       							</div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px; z-index: 998;" >
								<div class="box box-solid box-primary recuadro"	>
									<div class="fila1">
                						<div class="box-header" >
            								<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> @lang('hc4.procedimientos-') <br>  @lang('hc4.endoscopicos-')</h3>
					                        	</div>
				                        	</div>

					                        <div class="box-tools">
						                        <button type="button" class="btn btn-box-tool"  onclick="cargar_procedimiento_endoscopico();"
						                               style="color: white"><i class="fa fa-plus"></i>
						                        </button>
					                        </div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_endoscopico)) {{$doctor_procedimiento_endoscopico->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($doctor_procedimiento_endoscopico))
					                        @if(!is_null($doctor_procedimiento_endoscopico->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($doctor_procedimiento_endoscopico->f_operacion));
						                        $mes =  Date('n',strtotime($doctor_procedimiento_endoscopico->f_operacion));
						                        @endphp

	                                        	@if($dia == '1') @lang('hc4.lunes')
												@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr($doctor_procedimiento_endoscopico->f_operacion,8,2)}} @lang('hc4.de')
	                                        	@if($mes == '1') @lang('hc4.enero')
												@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($doctor_procedimiento_endoscopico->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini));
						                        @endphp

	                                        	@if($dia == '1') @lang('hc4.lunes')
												@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini,8,2)}} @lang('hc4.de')
	                                        	@if($mes == '1') @lang('hc4.enero')
												@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_endoscopico))
				                        <b>@if(!is_null($doctor_procedimiento_endoscopico))
					                        	@if(!is_null($doctor_procedimiento_endoscopico->id))
					                        		Dr. {{$doctor_procedimiento_endoscopico->nombre1}} {{$doctor_procedimiento_endoscopico->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $doctor_procedimiento_endoscopico->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_endoscopico))
					                        @if(isset($doctor_procedimiento_endoscopico->nombre))
				                        		@if(strlen($doctor_procedimiento_endoscopico->nombre) >= '25')
						  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}...
						  						@else
						  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_endoscopico->id_procedimiento)->get();
							  					 $mas = true;
							  					 $texto = "";

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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
       							</div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
									<div class="box box-solid box-primary recuadro" >
										<div class="fila2">
	                    						<div class="box-header" >
							                        	<div class="row" style="margin-left: 0px">
			                								<div style="margin-right: 5px">
			                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/ordenes.png">
			                								</div>

								                        	<div>
								                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.ordenes')</h3>
								                        	</div>
						                        		</div>
								                        <!--<div class="box-tools">
									                        <button type="button" class="btn btn-box-tool"
									                           style="color: white" onclick="alert('¡COMPRAR LICENCIA COMPLETA!')"><i class="fa fa-plus"></i>
									                        </button>
								                        </div>-->
	                   						    </div>
	                   					</div>
	               						<div class="box-body" style="margin-top:5px;padding-left:10px;padding-top:0px; padding-bottom:0px;">
			                                <a class="btn btn-info btn_ordenes" style="color: white" onClick="carga_ordenes_laboratorio()">
					                        	<div class="col-12" style="padding: 0px;  margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/lab.png">
					                        		<label style="font-size: 10px">@lang('hc4.laboratorio-')</label>
					                        	</div>
			                                </a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white" onClick="carga_ordenes_imagenes();">
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
					                        		<label style="font-size: 10px">@lang('hc4.imagenes-')</label>
					                        	</div>
		                               		</a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_proendospico();" >
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
					                        		<label style="font-size: 10px">@lang('hc4.procedimientos-') <br>@lang('hc4.endoscopicos-')</label>
					                        	</div>
		                               		</a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_profuncional();">
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
					                        		<label style="font-size: 10px">@lang('hc4.procedimientos-') <br> @lang('hc4.funcionales-')</label>
					                        	</div>
		                               		</a>

		                               		<!-- <a class="btn btn-info btn_ordenes" style="color: white; " onClick="alert('¡COMPRAR LICENCIA COMPLETA!')" >
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
					                        		<label style="font-size: 10px">BIOPSIAS</label>
					                        	</div>
		                               		</a> -->
					                   	</div>
	       							</div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila2">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
	            								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> @lang('hc4.procedimientos-') <br> @lang('hc4.funcionales-')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                           	<button type="button" class="btn btn-box-tool"
					                           	onclick="cargar_procedimiento_funcional();"
					                           style="color: white"><i class="fa fa-plus"></i>
					                            </button>
					                        </div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo" style="font-size: 10px">
					                    <div class="row">
	           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
	           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_funcional)) {{$doctor_procedimiento_funcional->id_procedimiento}} @endif</div>
	       						 		</div>
	       						 			@if(!is_null($doctor_procedimiento_funcional))
						                        @if(!is_null($doctor_procedimiento_funcional->f_operacion))
							                        @php
							                        $dia =  Date('N',strtotime($doctor_procedimiento_funcional->f_operacion));
							                        $mes =  Date('n',strtotime($doctor_procedimiento_funcional->f_operacion)); @endphp
			                                   		<b>
			                                        @if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
			                                        @endif
			                                         	{{substr($doctor_procedimiento_funcional->f_operacion,8,2)}}  @lang('hc4.de')
			                                        @if($mes == '1')  @lang('hc4.enero')
													@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
			                                        @endif
													@lang('hc4.del') {{substr($doctor_procedimiento_funcional->f_operacion,0,4)}}</b>
			                                    @else
			                                     	@php
							                        $dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini));
							                        $mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini)); @endphp
			                                   		<b>
			                                        @if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
			                                        @endif
			                                         	{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini,8,2)}}  @lang('hc4.de')
			                                        @if($mes == '1')  @lang('hc4.enero')
													@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
			                                        @endif
													@lang('hc4.del') {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini,0,4)}}</b>
						                        @endif
					                        @endif
					                        <br>
				                          <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                          <br>
					                       	@if(!is_null($doctor_procedimiento_funcional))
						                        @if(!is_null($doctor_procedimiento_funcional)) 						@if(!is_null($doctor_procedimiento_funcional->id))Dr. 				{{$doctor_procedimiento_funcional->nombre1}} 						{{$doctor_procedimiento_funcional->apellido1}}
						                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
						                        	@endif
					                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
					                        	@endif
					                        @endif

					                       <br>
				                          <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento')</span>
				                          <br>
					                        @if(!is_null($doctor_procedimiento_funcional))
				                        @if(isset($doctor_procedimiento_funcional->nombre))
				                        {{substr($doctor_procedimiento_funcional->nombre,0,50)}}
				                        ...
				                        @else

				                        @php
					  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_funcional->id_procedimiento)->get();
					  					 $mas = true;
					  					 $texto = "";

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

					  						if(strlen($texto) >= '50')
					  							$texto = substr($texto,0,50).'...';
					  					@endphp
					  						{{$texto}}
				                        @endif
				                        @endif
					                        <br>
			                  	    </div>
	   							</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px; z-index: 998;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila2">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
	            								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.resultadosBiopsias')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                            <button type="button" class="btn btn-box-tool"
					                            onclick="cargar_biopsias()"
					                            style="color: white"><i class="fa fa-plus"></i>
					                            </button>
					                        </div>
	           						    </div>
	           						</div>

	       						 	<div class="box-body cuerpo" style="font-size: 10px">
	       						 		<center>
	                   						<table style="text-align: center; margin-top: 25px" >
	                   						    <tr>
		               						 		<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha') </td>
		               						 		<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	           						 		    </tr>
												@if(!is_null($biopsias_interno))<tr>
													<td rowspan="2">
													@if(!is_null($biopsias_interno)) {{substr($biopsias_interno->updated_at,0,10)}}
													@endif
													</td>
												</tr>
												<tr>
													<td>
														<a target="_blank" class="btn btn-info btn_accion"
															 href="{{route('formulario.013B', ['id'=> $biopsias_interno->id_hc_procedimiento])}}">
															<div class="row" style="margin-left: 0px; margin-right: 0px">
															<div style="margin-right: 0px">
																<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
															</div>
															<div>
																<label style="color: white">Ver Resultados</label>
															</div>
														</div>
														</a>
													</td>
												</tr>
	           						 		    @elseif(!is_null($biopsias_1))
	           						 		    <tr>
	           						 		        <td rowspan="2">
	                                                    @if(!is_null($biopsias_1)) {{substr($biopsias_1->created_at,0,10)}}
	           						 				    @endif
	           						 				</td>
	           						 			</tr>
	           						 			<tr>
	           						 			    <td>
	           						 				    <a target="_blank" class="btn btn-info btn_accion"
	           						 					     href="{{asset('laboratorio_externo_descarga')}}/{{$biopsias_1->id}}">
	               						 					<div class="row" style="margin-left: 0px; margin-right: 0px">
					                							<div style="margin-right: 0px">
					                								<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
					                						    </div>
	                                                            <div>
										                        	<label style="color: white">@lang('hc4.verResultados')</label>
										                        </div>
							                        		</div>
	           						 			        </a>
	           						 			    </td>
	           						 			</tr>
	           						 		    @endif
	                                        </table>
	               						</center>
	   								</div>
								</div>
							</div>


							@if(Auth::user()->id == '1307189140')
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.bancoImagenes')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_estudios_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo"  >
           						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
						  					 $mas = true;
						  					 $texto = "";

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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
           						 		<center>
           						 			@if(!is_null($nuevo_armado))
	           						 			@php
								  						$id_protocolo = $nuevo_armado->hc_proto_id;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->limit(4)->get();
							  					@endphp


							  					@if($imagenes != '[]')
	           						 				<div class="col-md-12">
	           						 					<div class="row">
	           						 					@foreach($imagenes as $imagen)

					                                            @php
					                                                $explotar = explode( '.', $imagen->nombre);
					                                                $extension = end($explotar);
					                                            @endphp
					                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
					                                            <div class="col-6">
					                                            	<a style="" data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('hc_ima')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
														@lang('hc4.noPoseeImagenesProcedimientos')
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
														@lang('hc4.noPoseeImagenesProcedimientos')
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@else
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> @lang('hc4.armarEstudios')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_imagenes_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo"  >
	       						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
						  					 $mas = true;
						  					 $texto = "";

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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
	       						 		<center>
	       						 		<table style="text-align: center; margin-top: 25px" >
	       						 			<tr>
	       						 				<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha')</td>
	       						 				<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	       						 			</tr>
	   						 				<tr>
	   						 					<td>
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
	       						 							$fecha_proc = \Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini;

	       						 							$fecha = substr($fecha_proc,0,10);
	       						 							$invert = explode( '-',$fecha);
								                            $fecha_invert = $invert[0]."-".$invert[1]."-".$invert[2];
							                            @endphp
							                            {{$fecha_invert}}
						                        	@endif
	   						 					</td>
	   						 					<td>
	   						 						@php
	   						 							$imagenes = null;
	   						 						@endphp
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
									  						$id_protocolo = $nuevo_armado->hc_proto_id;
									  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get()->last();
									  					@endphp
								  					@endif

								  					@if(!is_null($imagenes))
	       						 					<a type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $id_protocolo])}}" data-toggle="modal" data-target="#foto">
	       						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
			                								<div style="margin-right: 0px">
			                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
			                								</div>
								                        	<div>
								                        		<label style="color: white" >@lang('hc4.verResultados')</label>
								                        	</div>
					                        			</div>
	       						 					</a>
	       						 					@else
	       						 					<div>@lang('hc4.noPoseeImagenesdeProcedimientos')</div>
	       						 					@endif
	   						 					</td>
	   						 				</tr>
	       						 		</table>
	       						 		</center>
	   								</div>
								</div>
							</div>
							@endif
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 10px; color: white">@lang('hc4.preVisualizarEstudios')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_visualizador_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo"  >
	       						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
						  					 $mas = true;
						  					 $texto = "";

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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
	       						 		<center>
	       						 		<table style="text-align: center; margin-top: 25px" >
	       						 			<tr>
	       						 				<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha')</td>
	       						 				<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	       						 			</tr>
	   						 				<tr>
	   						 					<td>
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
	       						 							$fecha_proc = \Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini;

	       						 							$fecha = substr($fecha_proc,0,10);
	       						 							$invert = explode( '-',$fecha);
								                            $fecha_invert = $invert[0]."-".$invert[1]."-".$invert[2];
							                            @endphp
							                            {{$fecha_invert}}
						                        	@endif
	   						 					</td>
	   						 					<td>
	   						 						@php
	   						 							$imagenes = null;
	   						 						@endphp
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
									  						$id_protocolo = $nuevo_armado->hc_proto_id;
									  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get()->last();
									  					@endphp
								  					@endif

								  					@if(!is_null($imagenes))
	       						 					<a type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $id_protocolo])}}" data-toggle="modal" data-target="#foto">
	       						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
			                								<div style="margin-right: 0px">
			                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
			                								</div>
								                        	<div>
								                        		<label style="color: white" >@lang('hc4.verResultados')</label>
								                        	</div>
					                        			</div>
	       						 					</a>
	       						 					@else
	       						 					<div>@lang('hc4.noPoseeImagenesdeProcedimientos')</div>
	       						 					@endif
	   						 					</td>
	   						 				</tr>
	       						 		</table>
	       						 		</center>
	   								</div>
								</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px; z-index: 998; ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
		                				<div class="box-header" >


	                                        <div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/lab.png">
	            								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;@lang('hc4.laboratorio-')</h3>
					                        	</div>
			                        		</div>


								            <div class="box-tools">
									            <button type="button" class="btn btn-box-tool"
									                onclick="cargar_laboratorio();"
									                style="color: white"><i class="fa fa-plus"></i>
									            </button>
								            </div>
		               				    </div>
		               			    </div>
	                                <div class="box-body cuerpo" >
	               						<center>
	                   						<table style="text-align: center; margin-top: 25px" >
	                   						    <tr>
		               						 		<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha') </td>
		               						 		<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	           						 		    </tr>
	           						 		    @if(!is_null($orden_lab))
	           						 		    <tr>
	           						 		        <td rowspan="2">
	                                                    @if(!is_null($orden_lab)) {{substr($orden_lab->fecha_orden,0,10)}}
	           						 				    @endif
	           						 				</td>
	           						 			</tr>
	           						 			<tr>
	           						 			    <td>
	           						 				    <a target="_blank" class="btn btn-info btn_accion"
	           						 					     onclick="carga_resultado_laboratorio({{$orden_lab->id}});">
	               						 					<div class="row" style="margin-left: 0px; margin-right: 0px">
					                							<div style="margin-right: 0px">
					                								<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
					                						    </div>
	                                                            <div>
										                        	<label style="color: white">@lang('hc4.verResultados')</label>
										                        </div>
							                        		</div>
	           						 			        </a>
	           						 			    </td>
	           						 			</tr>
	           						 		    @endif
	                                        </table>
	               						</center>
	           						</div>
							    </div>
							</div>
							@if(Auth::user()->id == '1307189140')
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.epicrisis-')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_epicrisis();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

												@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr($nuevo_armado->f_operacion,8,2)}}  @lang('hc4.de')
	                                        	@if($mes == '1')  @lang('hc4.enero')
												@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
						                        @endphp

												@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,8,2)}}  @lang('hc4.de')
	                                        	@if($mes == '1')  @lang('hc4.enero')
		                                        @elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
				                        <b>@if(!is_null($nuevo_armado))
					                        	@if(!is_null($nuevo_armado->id))
					                        		Dr. {{$nuevo_armado->nombre1}} {{$nuevo_armado->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $nuevo_armado->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
					                        @if(isset($nuevo_armado->nombre))
				                        		@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
							  					 $mas = true;
							  					 $texto = "";

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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
								</div>
							</div>
							@else
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.bancoImagenes')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_estudios_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo"  >
           						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
						  					 $mas = true;
						  					 $texto = "";

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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
						  						<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
           						 		<center>
           						 			@if(!is_null($nuevo_armado))
	           						 			@php
								  						$id_protocolo = $nuevo_armado->hc_proto_id;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->limit(4)->get();
							  					@endphp


							  					@if($imagenes != '[]')
	           						 				<div class="col-md-12">
	           						 					<div class="row">
	           						 					@foreach($imagenes as $imagen)

					                                            @php
					                                                $explotar = explode( '.', $imagen->nombre);
					                                                $extension = end($explotar);
					                                            @endphp
					                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
					                                            <div class="col-6">
					                                            	<a style="" data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('hc_ima')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
														@lang('hc4.noPoseeImagenesProcedimientos')
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
														@lang('hc4.noPoseeImagenesProcedimientos')
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@endif

							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/exa_externos.png">
                								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> @lang('hc4.resultados-') <br> @lang('hc4.externos-')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                          onclick="cargar_resultados_externos();"
					                            style="color: white"><i class="fa fa-plus"></i>
					                          </button>
					                        </div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<center>
           						 		<table style="text-align: center; margin-top: 25px" >
           						 			<tr>
           						 				<td style="width: 80px; font-family: 'Helvetica general';">@lang('hc4.fecha') </td>
           						 				<td style="width: 80px; font-family: 'Helvetica general';" >@lang('hc4.accion')</td>
           						 			</tr>
       						 				<tr>
       						 					@if(!is_null($laboratorio_externo))
       						 					<td>@if(!is_null($laboratorio_externo)) {{substr($laboratorio_externo->created_at,0,10)}} @endif
       						 					</td>
       						 					<td>
           						 					<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$laboratorio_externo->id}}" >
           						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
			                								<div style="margin-right: 0px">
			                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
			                								</div>
								                        	<div>
								                        		<label style="color: white">@lang('hc4.verResultados')</label>
								                        	</div>
						                        		</div>
           						 					</a>
       						 					</td>
       						 					@endif
       						 				</tr>
           						 		</table>
           						 		</center>
       							    </div>
					            </div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px; z-index: 998; ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
		                				<div class="box-header" >
	                                        <div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;@lang('hc4.ecografias')</h3>
					                        	</div>
			                        		</div>
								            <div class="box-tools">
									            <button type="button" class="btn btn-box-tool"
									                onclick="cargar_procedimiento_ecografia();"
									                style="color: white"><i class="fa fa-plus"></i>
									            </button>
								            </div>
		               				    </div>
		               			    </div>
           						 	<div class="box-body cuerpo" >

           						 		<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_ecografia)) {{$doctor_procedimiento_ecografia->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($doctor_procedimiento_ecografia))
					                        @if(!is_null($doctor_procedimiento_ecografia->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($doctor_procedimiento_ecografia->f_operacion)); $mes =  Date('n',strtotime($doctor_procedimiento_ecografia->f_operacion));
						                        @endphp

	                                        	@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
													@elseif($dia == '3') @lang('hc4.miercoles')
													@elseif($dia == '4') @lang('hc4.jueves')
													@elseif($dia == '5') @lang('hc4.viernes')
													@elseif($dia == '6') @lang('hc4.sabado')
													@elseif($dia == '7') @lang('hc4.domingo')
													@endif
	                                        		{{substr($doctor_procedimiento_ecografia->f_operacion,8,2)}} @lang('hc4.de')
													@if($mes == '1')  @lang('hc4.enero')
														@elseif($mes == '2') @lang('hc4.febrero')
														@elseif($mes == '3') @lang('hc4.marzo')
														@elseif($mes == '4') @lang('hc4.abril')
														@elseif($mes == '5') @lang('hc4.mayo')
														@elseif($mes == '6') @lang('hc4.junio')
														@elseif($mes == '7') @lang('hc4.julio')
														@elseif($mes == '8') @lang('hc4.agosto')
														@elseif($mes == '9') @lang('hc4.septiembre')
														@elseif($mes == '10') @lang('hc4.octubre')
														@elseif($mes == '11') @lang('hc4.noviembre')
														@elseif($mes == '12') @lang('hc4.diciembre')
													@endif
												@lang('hc4.del') {{substr($doctor_procedimiento_ecografia->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini));
						                        @endphp

	                                        	@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
													@elseif($dia == '3') @lang('hc4.miercoles')
													@elseif($dia == '4') @lang('hc4.jueves')
													@elseif($dia == '5') @lang('hc4.viernes')
													@elseif($dia == '6') @lang('hc4.sabado')
													@elseif($dia == '7') @lang('hc4.domingo')
												@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini,8,2)}} @lang('hc4.de')
													@if($mes == '1')  @lang('hc4.enero')
														@elseif($mes == '2') @lang('hc4.febrero')
														@elseif($mes == '3') @lang('hc4.marzo')
														@elseif($mes == '4') @lang('hc4.abril')
														@elseif($mes == '5') @lang('hc4.mayo')
														@elseif($mes == '6') @lang('hc4.junio')
														@elseif($mes == '7') @lang('hc4.julio')
														@elseif($mes == '8') @lang('hc4.agosto')
														@elseif($mes == '9') @lang('hc4.septiembre')
														@elseif($mes == '10') @lang('hc4.octubre')
														@elseif($mes == '11') @lang('hc4.noviembre')
														@elseif($mes == '12') @lang('hc4.diciembre')
													@endif
												@lang('hc4.del') {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_ecografia))
				                        <b>@if(!is_null($doctor_procedimiento_ecografia))
					                        	@if(!is_null($doctor_procedimiento_ecografia->id))
					                        		Dr. {{$doctor_procedimiento_ecografia->nombre1}} {{$doctor_procedimiento_ecografia->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $doctor_procedimiento_ecografia->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_ecografia))
				                        @if(isset($doctor_procedimiento_ecografia->nombre))
			                        		@if(strlen($doctor_procedimiento_ecografia->nombre) >= '25')
					  							{{substr($doctor_procedimiento_ecografia->nombre,0,25)}}...
					  						@else
					  							{{substr($doctor_procedimiento_ecografia->nombre,0,25)}}
					  						@endif
				                        @else

				                        @php
					  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_ecografia->id_procedimiento)->get();
					  					 $mas = true;
					  					 $texto = "";

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

					  						if(strlen($texto) >= '50')
					  							$texto = substr($texto,0,50).'...';
					  					@endphp
					  						{{$texto}}
				                        @endif
				                        @endif

				                        <br>
			                   	    </div>
							    </div>
							</div>
							@if(Auth::user()->id != '1307189140')
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.epicrisis-')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_epicrisis();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

												@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr($nuevo_armado->f_operacion,8,2)}} @lang('hc4.de')
													@if($mes == '1')  @lang('hc4.enero')
		                                        @elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
						                        @endphp

												@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,8,2)}} @lang('hc4.de')
													@if($mes == '1')  @lang('hc4.enero')
		                                        @elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
				                        <b>@if(!is_null($nuevo_armado))
					                        	@if(!is_null($nuevo_armado->id))
					                        		Dr. {{$nuevo_armado->nombre1}} {{$nuevo_armado->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $nuevo_armado->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
					                        @if(isset($nuevo_armado->nombre))
				                        		@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
							  					 $mas = true;
							  					 $texto = "";

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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
								</div>
							</div>
							@endif
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.proximosprocedimientos-')<N/h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_prox_proc();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($prox_procedimientos)) {{$prox_procedimientos->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($prox_procedimientos))
					                        @if(!is_null($prox_procedimientos->fechaini))
						                        @php
						                        $dia =  Date('N',strtotime($prox_procedimientos->fechaini)); $mes =  Date('n',strtotime($prox_procedimientos->fechaini));
						                        @endphp
												@if($dia == '1') @lang('hc4.lunes')
													@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
	                                        		{{substr($prox_procedimientos->fechaini,8,2)}} @lang('hc4.de')
	                                        		@if($mes == '1')  @lang('hc4.enero')
		                                        @elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($prox_procedimientos->fechaini,0,4)}}
					                        @endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($prox_procedimientos))
				                        <b>@if(!is_null($prox_procedimientos))
					                        	@if(!is_null($prox_procedimientos->id_doctor1))
					                        		Dr. {{$prox_procedimientos->doctor1->nombre1}} {{$prox_procedimientos->doctor1->apellido1}}
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        	@if(!is_null($prox_procedimientos))
	                                            @php 
	                                            	$texto = ""; 
		                                            if(!is_null($prox_procedimientos->id_procedimiento)){
		                                                $texto = $procedimientos->find($prox_procedimientos->id_procedimiento)->nombre;
		                                            }
		                                            if(!is_null($prox_procedimientos->id)){ 
		                                                $proc_agenda = \Sis_medico\AgendaProcedimiento::where('id_agenda', $prox_procedimientos->id)->get();
		                                                foreach($proc_agenda as $value2){
		                                                    $texto = $texto.' + '. $value2->procedimiento->nombre  ;
		                                                }
		                                            }
		                                            if(strlen($texto) >= '40'){
									  					$texto = substr($texto,0,40).'...';
		                                            }
	                                            @endphp
	                                            {{$texto}}
	                                        @endif
				                        <br>
			                   	    </div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
						<div class="row" style="margin-left: 10px;">


						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">

		$('#foto').on('hidden.bs.modal', function(){
	        $(this).removeData('bs.modal');
	        //$(this).find('#imagen_solita').empty().html('');
	    });

		var remoto_href = '';
		jQuery('body').on('click', '[data-toggle="modal"]', function() {
		    if(remoto_href != jQuery(this).data('remote')) {
		        remoto_href = jQuery(this).data('remote');
		        jQuery(jQuery(this).data('target')).removeData('bs.modal');

		        jQuery(jQuery(this).data('target')).find('.modal-body').empty();
		      	//console.log(remoto_href);
		    	//console.log($(this).data('target'));

		    	//console.log(jQuery(this).data('target') + ' .modal-content');
		        jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
		    }
		});



		$('#ale_list').select2({
	        placeholder: "Seleccione Medicamento...",
	        minimumInputLength: 2,
	        ajax: {
	            url: '{{route('generico.find')}}',
	            dataType: 'json',
	            data: function (params) {
	                //console.log(params);
	                return {
	                    q: $.trim(params.term)
	                };
	            },
	            processResults: function (data) {
	                //console.log(data);
	                return {
	                    results: data
	                };
	            },
	            cache: true
	        },
	        tags: true,
	        createTag: function (params) {
	            var term = $.trim(params.term);
	            return {
	                id: term.toUpperCase()+'xnose',
	                text: term.toUpperCase(),
	                newTag: true, // add additional parameters
	            }
	        }
	    });

	    $('#ale_list').on('change', function (e) {
	      //alert("hola");
	      guardar_alergia();
	    });

	    function guardar_alergia(){

	        //alert("ok");
	        $.ajax({
	          type: 'post',
	          url:"{{route('n_filiacion')}}", // hc4/HistoriaPacienteController->ingreso
	          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

	          datatype: 'json',
	          data: $("#frm").serialize(),
	          success: function(data){
	            console.log(data);
	            //alert(data);
	            /*var edad;
	            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
	            edad = calcularEdad(fecha_nacimiento);

	            $('#edad').val( edad );*/
	          },
	          error: function(data){

	            console.log(data.responseJSON);

	          }
	        });
	    }


	    function guardar_ob(){

			$.ajax({

				type: 'post',
	            url:"{{route('paciente_observacion_act')}}",
	            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	            datatype: 'json',
	            data: $("#obs_id").serialize(),
				success: function(data){

				},
				error:  function(){
					alert('error al cargar');
				}
			});

		}



		function scroll_datos(id){
			$('html, body').animate({
		      scrollTop: $(id).offset().top
		    }, 1000)
		}


	  	function cargar_procedimiento_endoscopico(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_endoscopico', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_procedimiento_ecografia(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_ecografia', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_procedimiento_funcional(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_funcional', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_evoluciones(){

			$.ajax({
				type: "GET",
				url: "{{route('paciente.evoluciones', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}


		function cargar_imagenes_paciente(){
			//alert('entra');
			$.ajax({
				type: "GET",
				url: "{{route('paciente.imagenes_paciente', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_visualizador_paciente(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.visualizador_estudio', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}


		function cargar_estudios_paciente(){
			$.ajax({
				type: "GET",
				url: "{{route('hc4.estudios_paciente', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_resultados_externos(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.hc4resultados_externos', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

	    function cargar_recetas(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.recetas', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

	    function cargar_nueva_receta(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente_nueva.receta', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
	    }

	    function cargar_nueva_receta_publica(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente_nueva.receta_publica', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
	    }

		function cargar_laboratorio(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.laboratorio', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		//Reemplazada por resultados de Biopsias
		function cargar_biopsias(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.biopsias', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_resultado_biopsias(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente_resultados.biopsias', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}






		function agregar_procedimiento_hc(div){
    		//alert("agregar");
	    	$.ajax({
				async: true,
				type: "GET",
				url: $('#'+div).val(),
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
	    }

		function cargar_detalle_filiacion(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.detalle_filiacion', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function carga_resultado_laboratorio(id_or){

	      window.open('{{url('inicio/laboratorio/hc4resultados/imprimir')}}/'+id_or,'_blank');

	    }

	    function agregar_procedimiento_tipo(url_datos){
    		//alert("agregar");
    	$.ajax({
			async: true,
			type: "GET",
			url: url_datos,
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
			});
	    }

	      function actualiza_cortesia(e){
          cortesia = document.getElementById("cortesia_paciente").value;
          if (cortesia == "SI"){
              act_cortesia_si();
          }
          else if(cortesia == "NO"){
              act_cortesia_no();
          }

      }


      function act_cortesia_si(){

	        $.ajax({
	          type: 'get',
	          url:"{{ route('hc4_filiacion.cortesia', ['id' => $paciente->id, 'c' => 1])}}",
	          datatype: 'json',
	          success: function(data){
	          },
	          error: function(data){
	             //console.log(data);
	          }
	        });

    }	

    function act_cortesia_no(){

	        $.ajax({
	          type: 'get',
	          url:"{{ route('hc4_filiacion.cortesia', ['id' => $paciente->id, 'c' => 0])}}",
	          datatype: 'json',
	          success: function(data){
	          },
	          error: function(data){
	             //console.log(data);
	          }
	        });
    }

    function cargar_epicrisis(){

			$.ajax({
				type: "GET",
				url: "{{route('hc4_paciente.epicrisis', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

    function cargar_prox_proc(){
			$.ajax({
				type: "GET",
				url: "{{route('hc4.index_proximos_proc', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

    //Funcion Permite crear Orden Procedimiento Endoscopico
    //Solo para Doctores
    function carga_ordenes_proendospico(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_endoscopico',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    //Funcion Permite crear Orden Procedimiento Funcional
    //Solo para Doctores
    function carga_ordenes_profuncional(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_funcional',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }


    //Funcion Permite crear Orden de Imagenes
    //Solo para Doctores
    function carga_ordenes_imagenes(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_imagenes',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }


    function carga_ordenes_laboratorio(){
    	$.ajax({
		    type: "GET",
			url: "{{route('hc4_orden_lab.index',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }


   </script>
</section>


@endsection




