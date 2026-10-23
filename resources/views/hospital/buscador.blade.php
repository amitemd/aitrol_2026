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
</style>
<script type="text/javascript">
    function cargar_consulta(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.consulta', ['id_paciente' => $paciente->id])}}", 
				data: "",
				datatype: "html",
				success: function(datahtml){
					$("#area_trabajo").html(datahtml);
					
				},
				error:  function(){
					alert('error al cargar');
				}
			});
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
		    <div class="col-md-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #004AC1,#0C8BEC,#004AC1); margin-bottom: 10px">   
		     	<div class="row">  
			        <div class="col-md-7" style=""> 
			            <h1 style="font-size: 15px; margin:0;">
			            	<img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png"> 
			             	<b>HISTORIA CL&Iacute;NICA POR PACIENTE</b>
			            </h1>    
			        </div>
			        <div class="col-md-5" style="padding-right: 0px;right: 0px; top: 5px"> 
				        <!--div class="row">
				            <div class="col-8">
				                <div class="input-group">
				                  	<input value="" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
				                </div>
				            </div>

				            <div class="col-4">
				              	<a class="btn btn-info boton-buscar" style="color: white ; background-color: #da291c;  border-radius: 5px; border: 2px solid white; "  > <i class="fa fa-search" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;</a>
				            </div> 
				        </div -->
			        </div>
		        </div>
            </div>
		</div>
	</div>

	<div class="container-fluid" >      
		<div class="row">  
			<div class="col-md-7"  style="padding-left: 5px; padding-right: 10px; " >
				<div class="row">
					<div class="col-md-12">
						<div style="border: 2px solid #004AC1; border-radius: 8px; margin-left: 8px; margin-bottom: 5px" >
							<div class="col-md-12" style="" >
								<div class="row">
									<div class="col-md-9 col-12" style="padding-left: 0px; padding-right: 15px;" >
										<label style="margin-top: 10px; color: #004AC1; font-family: arial ; margin-left: 20px" >
											<span style="font-family: 'Helvetica general';"><i class="fa fa-exclamation-circle"></i> Datos Principales del Paciente </span>
										</label>
										<table style="margin-left: 20px; margin-bottom: 5px; text-align: left; margin-top: 5px"   >
											<tr>	
												<td style="font-size: 11px; width: 300px"> <span style="font-family: 'Helvetica general';"> Paciente </span></td>
												<td style="font-size: 11px; width: 100px"> <span style="font-family: 'Helvetica general';">Identificacion </span></td>
												<td style="font-size: 11px; width: 60px"> <span style="font-family: 'Helvetica general';">Edad </span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">Seguro </span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">Cortesia </span></td>
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
							                      	<option @if($paciente_cort->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
							                      	<option @if($paciente_cort->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
							                    @else
							                    	<option value="NO" selected >NO</option>
							                    	<option value="SI" >SI</option>
							                    @endif
							                  </select>  
												</td>								
											</tr>
										</table>
										<div class="col-md-11 has-error" style="padding: 1px; margin-left: 20px; margin-bottom: 10px">
											<form  id="frm">
												<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
				                                <div class="col-md-12" style="padding: 0px;">
				                                    <label for="ale_list" class="control-label" style="color: orange; font-size: 11px"><span style="font-family: 'Helvetica general';">Alergias</span></label>
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
											<span style="font-family: 'Helvetica general';"> Observaciones </span>
										</label>
										<form id="obs_id" >
											<input type="hidden" name="id_paciente"  value="{{$paciente->id}}">
											<input style="font-size: 12px; width: 90%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->observacion}}" autocomplete="off" type="text" name="observacion" placeholder="OBSERVACIONES GENERALES DEL PACIENTE">
											<div class="col-md-12" style="padding: 10px">
												<div class="col-md-12" style="padding-left: 0px; padding-right: 0px; text-align: center; ">
													<div class="row">
														<div class="col-md-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">Antecedentes Patologicos</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_patologicos" onchange="guardar_ob();">{{$paciente->antecedentes_pat}}
																	</textarea>
																	<!--<input style="width: 100%" type="text" name="an_patologicos" value="{{$paciente->antecedentes_pat}}" onchange="guardar_ob();">-->
																</div>
															</div> 
														</div>
														<div class="col-md-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">Antecedentes Familiares</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12" >
																	<textarea rows="2" name="an_familiares" onchange="guardar_ob();">{{$paciente->antecedentes_fam}}
																	</textarea>
																	<!--<input style="width: 100%" type="text" name="an_familiares" value="{{$paciente->antecedentes_fam}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-md-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">Antecedentes Quirurgicos</span></div>
																<div class="col-md-12 col-sm-6 col-12">
																<textarea rows="2" name="an_quirurgicos" onchange="guardar_ob();">{{$paciente->antecedentes_quir}}
																</textarea>
																<!--<input style="width: 100%" type="text" name="an_quirurgicos" value="{{$paciente->antecedentes_quir}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
									    </form>
									</div>
									<div class="col-md-3 col-12" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
										<div class="row">
											<div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_detalle_filiacion()"> 
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 10px; padding-right: 10px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/escudo.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
								                        		<label style="font-size: 10px">VER DETALLES DE FILIACI&Oacute;N</label>
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
								                        		<label style="font-size: 10px">NUEVA RECETA</label>
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
						    			<span id="eendoscopico">El paciente tiene {{$fendoscopicos}} @if ($fendoscopicos > 1) procedimientos endoscopicos @else procedimiento endoscopico @endif por crear el dia de hoy, presione aqui para atender</span>
						    		</a>
						    		<input type="hidden" id="endoscopico_id" value="{{route('hc4_procedimiento.selecciona_procedimiento2',['tipo' => '0', 'paciente' => $paciente->id, 'hcid' => $hc->hcid ])}}">
						    		<br/>&nbsp;<br/>
						    	
						    	@endif
						    	@if($ffuncional > 0)
						    		<a onclick="agregar_procedimiento_hc('funcional_id');"  class="btn-danger boton_burbuja">
						    			<span id="efuncional">El paciente tiene {{$ffuncional}} @if ($ffuncional > 1) procedimientos funcionales @else procedimiento funcional @endif por crear el dia de hoy , presione aqui para atender</span>
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
						    			<span id="pconsulta">El paciente tiene {{$xagenda}} @if ($xagenda > 1) consultas @else consulta @endif el dia de hoy, presione aqui para atender</span>
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
						<div class="row" style="margin-left: 10px;">				
							<!--div class="col-md-4" style="padding: 3px;  "> 
								<div class="box box-solid box-primary recuadro "  >	
								<div class="fila1" >
	        						<div class="box-header"  >
				                        		<div class="row" style="margin-left: 0px">
	                								<div style="margin-right: 5px">
	                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/evoluciones.png">
	                								</div>
						                        	<div>
						                        		<h3 class="box-title" style="font-size: 12px; color: white	"> EVOLUCIONES </h3>
						                        	</div>
							                    </div>
						                        <div class="box-tools">
							                          <button type="button" class="btn btn-box-tool" 
							                          onclick="cargar_evoluciones();" 
							                            style="color: white"><i class="fa fa-plus"></i> 
							                          </button>
						                        </div>
	       						    </div>
								</div>
       						 	<div class="box-body cuerpo">
			                      	<div class="row">
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($evoluciones)) {{$evoluciones->id}} @endif</div>
           						 	</div>

			                        @if(!is_null($evoluciones))
			                         @php $dia =  Date('N',strtotime($evoluciones->fechaini)); $mes =  Date('n',strtotime($evoluciones->fechaini)); @endphp
                                    <b>
                                        @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($evoluciones->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($evoluciones->fechaini,0,4)}}</b> 
                                     @endif
			                        <br>
			                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
			                        <br>

			                       	@if(!is_null($evoluciones))
			                         @if(!is_null($procedimientos_observaciones))
			                          	@if(!is_null($procedimientos_observaciones->id_doctor_examinador))
			                          		Dr.{{$procedimientos_observaciones->doctor->nombre1}} {{$procedimientos_observaciones->doctor->apellido1}}
			                       		@else
			                         		@if(!is_null($paciente->agenda->last()))
			                         			
			                         			@if(!is_null($paciente->agenda->last()->historia_clinica))
			                          			Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
			                          			@endif
			                          		@endif
			                         	@endif 
			                        @endif
			                        @endif
			                        <br>
			                        <span style="font-family: 'Helvetica general';">Observaci&oacute;n</span>
			                        <br>

			                        @if(!is_null($procedimientos_observaciones))
			                        @if(strlen($procedimientos_observaciones->observaciones) >= '30')
			                        	{{substr(strip_tags($procedimientos_observaciones->observaciones),0,30).'...'}}
			                        	@else
			                        		{{strip_tags($procedimientos_observaciones->observaciones)}}
			                        @endif
			                        @endif

			                        <br>
			                        <span style="font-family: 'Helvetica general';">Evoluci&oacute;n</span>
			                        <br>
			                        @if(!is_null($evoluciones))
			                        @if(strlen($evoluciones->cuadro_clinico)>= '30')      
			                        <?php echo  substr(strip_tags($evoluciones->cuadro_clinico),0,30); ?>
			                        ...
			                        @else
			                        	 <?php echo  substr(strip_tags($evoluciones->cuadro_clinico),0,30); ?>
			                        @endif
			                        @endif

		                   	    </div>
	   							</div>
							</div-->

							<div class="col-md-4" style="padding: 3px;"> 
								<div class="box box-solid box-primary recuadro" >
									<div class="fila1">
		        						<div class="box-header">
					                        <div class="row" style="margin-left: 0px">
		        								<div style="margin-right: 5px">
		        									<img width="20px" src="{{asset('/')}}hc4/img/iconos/estudios.png">
		        								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;CONSULTA</h3>
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
		   						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
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
				                            @if($dia == '1') Lunes 
				                                 @elseif($dia == '2') Martes
				                                 @elseif($dia == '3') Miércoles 
				                                 @elseif($dia == '4') Jueves 
				                                 @elseif($dia == '5') Viernes 
				                                 @elseif($dia == '6') Sábado 
				                                 @elseif($dia == '7') Domingo 
				                            @endif 
				                             	{{substr($consulta_nueva->fecha_atencion,8,2)}} de 
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
				                             	del {{substr($consulta_nueva->fecha_atencion,0,4)}}</b>
				                        @else 
				                         	@php 
					                        $dia =  Date('N',strtotime($agenda_fecha_consulta->fechaini)); 
					                        $mes =  Date('n',strtotime($agenda_fecha_consulta->fechaini)); 
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
				                             	{{substr($agenda_fecha_consulta->fechaini,8,2)}} de 
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
				                             	del {{substr($agenda_fecha_consulta->fechaini,0,4)}}</b>
				                        @endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
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
				                        <span style="font-family: 'Helvetica general';">Evolucion </span>
				                        <br>  
				                      	@if(!is_null($consulta_nueva))
				                        @if(!is_null($consulta_nueva->cuadro_clinico))
					                        @if(strlen($consulta_nueva->cuadro_clinico)>= '30')      
					                        <?php echo  substr(strip_tags($consulta_nueva->cuadro_clinico),0,30).'...'; ?>
					                        @else
					                        	 <?php echo  substr(strip_tags($consulta_nueva->cuadro_clinico),0,30); ?>
					                        @endif
					                    @endif	
					                    @endif
					                		                        
			                   	    </div>
								</div>
							</div>


							<div class="col-md-4" style="padding: 3px;left: 0px;"> 
								<div class="box box-solid box-primary recuadro" >
									<div class="fila1">	
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									 <img width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">RECETAS</h3>
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
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($hc_rec)) {{$hc_rec->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($hc_rec))
				                       
					                        
						                        @php 
						                        	$dia =  Date('N',strtotime($hc_rec->fechaini)); 
						                        	$mes =  Date('n',strtotime($hc_rec->fechaini)); 
						                        @endphp
                                   		
		                                        @if($dia == '1') Lunes 
			                                        @elseif($dia == '2') Martes 
			                                        @elseif($dia == '3') Miércoles 
			                                        @elseif($dia == '4') Jueves 
			                                        @elseif($dia == '5') Viernes 
			                                        @elseif($dia == '6') Sábado 
			                                        @elseif($dia == '7') Domingo 
	                                        	@endif 

	                                        	{{substr($hc_rec->fechaini,8,2)}} de 
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
	                                        	del {{substr($hc_rec->fechaini,0,4)}}
				                        	
				                       
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">Rp</span>
				                         <br>
				                         
				                        @if(!is_null($hc_rec))
					                        <?php
					                        	if(strlen($hc_rec->rp)>=150){
					                         		$texto = substr(strip_tags($hc_rec->rp),0,150 ) ;
					                         		echo $texto.'...';
					                     		}else
					                     			echo strip_tags($hc_rec->rp);
					                          ?>
				                        @endif
				                        
				                       <br>
				                       <!-- <span style="font-family: 'Helvetica general';">Prescripci&oacute;n</span>
				                        <br>
				                        @if(!is_null($hc_rec))
					                        <?php
					                        //	if(strlen($hc_rec->prescripcion)>=60){
					                        // 		$texto = substr(strip_tags($hc_rec->prescripcion),0,50 ) ;
					                        // 		echo $texto.'...';
					                     	//	}else
					                     	//		echo strip_tags($hc_rec->prescripcion);
					                          ?>
				                        @endif-->
			                   	    </div>
       							</div>
							</div>
						
							<div class="col-md-4" style="padding: 3px;left: 0px; z-index: 998;" > 
								<div class="box box-solid box-primary recuadro"	>
									<div class="fila1">	
                						<div class="box-header" >
            								<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> PROCEDIMIENTOS <br> ENDOSC&Oacute;PICOS</h3>
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
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_endoscopico)) {{$doctor_procedimiento_endoscopico->hc_proto_id}} @endif</div>
           						 		</div>	
           						 		@if(!is_null($doctor_procedimiento_endoscopico))
					                        @if(!is_null($doctor_procedimiento_endoscopico->f_operacion))
						                        @php 
						                        $dia =  Date('N',strtotime($doctor_procedimiento_endoscopico->f_operacion)); $mes =  Date('n',strtotime($doctor_procedimiento_endoscopico->f_operacion)); 
						                        @endphp
	                                   		
	                                        	@if($dia == '1') Lunes 
		                                        	@elseif($dia == '2') Martes 
		                                        	@elseif($dia == '3') Miércoles 
		                                        	@elseif($dia == '4') Jueves 
		                                        	@elseif($dia == '5') Viernes 
		                                        	@elseif($dia == '6') Sábado 
		                                        	@elseif($dia == '7') Domingo 
	                                        	@endif 
	                                        		{{substr($doctor_procedimiento_endoscopico->f_operacion,8,2)}} de 
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
	                                        		del {{substr($doctor_procedimiento_endoscopico->f_operacion,0,4)}}
	                                        @else

	                                        	@php 
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini)); 
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini)); 
						                        @endphp
	                                   		
	                                        	@if($dia == '1') Lunes 
		                                        	@elseif($dia == '2') Martes 
		                                        	@elseif($dia == '3') Miércoles 
		                                        	@elseif($dia == '4') Jueves 
		                                        	@elseif($dia == '5') Viernes 
		                                        	@elseif($dia == '6') Sábado 
		                                        	@elseif($dia == '7') Domingo 
	                                        	@endif 
	                                        		{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini,8,2)}} de 
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
	                                        		del {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_endoscopico))
				                        <b>@if(!is_null($doctor_procedimiento_endoscopico)) @if(!is_null($doctor_procedimiento_endoscopico->id))Dr. {{$doctor_procedimiento_endoscopico->nombre1}} {{$doctor_procedimiento_endoscopico->apellido1}}@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}@endif @else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}@endif</b>
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">Procedimiento </span>
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
				                        <!--center>
				                        <img src="https://www.merckmanuals.com/-/media/manual/professional/images/c0199665-endoscopy-large-intestine-science-photo-library-high_es.jpg?la=es&thn=0&mw=350" style="width: 20px; height: 20px">

				                        <img src="https://www.merckmanuals.com/-/media/manual/professional/images/c0199665-endoscopy-large-intestine-science-photo-library-high_es.jpg?la=es&thn=0&mw=350" style="width: 20px; height: 20px">

				                        <img src="https://www.merckmanuals.com/-/media/manual/professional/images/c0199665-endoscopy-large-intestine-science-photo-library-high_es.jpg?la=es&thn=0&mw=350" style="width: 20px; height: 20px">

				                        <img src="https://www.merckmanuals.com/-/media/manual/professional/images/c0199665-endoscopy-large-intestine-science-photo-library-high_es.jpg?la=es&thn=0&mw=350" style="width: 20px; height: 20px">
				                        </center-->    
			                   	    </div>
       							</div>
							</div>	
						</div>
					</div>
					<!--parte 2-->
											<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
											<div class="row" style="margin-left: 10px;">				
												<div class="col-md-4" style="padding: 3px;  "> 
														<div class="box box-solid box-primary recuadro" >
															<div class="fila2">	
						                    						<div class="box-header" >
												                        	<div class="row" style="margin-left: 0px">
								                								<div style="margin-right: 5px">
								                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/ordenes.png">
								                								</div>

													                        	<div>
													                        		<h3 class="box-title" style="font-size: 12px; color: white">&Oacute;RDENES</h3>
													                        	</div>
											                        		</div>
													                        <div class="box-tools">
														                        <button type="button" class="btn btn-box-tool" 
														                           style="color: white" onclick="alert('¡COMPRAR LICENCIA COMPLETA!')"><i class="fa fa-plus"></i> 
														                        </button>
													                        </div>	
						                   						    </div>
						                   					</div>
					                   						<div class="box-body" style="margin-top:5px;padding-left:10px;padding-top:0px; padding-bottom:0px;">
								                                <a class="btn btn-info btn_ordenes" style="color: white" onClick="alert('¡COMPRAR LICENCIA COMPLETA!')">
										                        	<div class="col-12" style="padding: 0px;  margin-right: 10px">
										                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/lab.png">
										                        		<label style="font-size: 10px">LABORATORIO</label>
										                        	</div>	
								                                </a>

							                               		<a class="btn btn-info btn_ordenes" style="color: white" onClick="alert('¡COMPRAR LICENCIA COMPLETA!')"> 
										                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
										                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
										                        		<label style="font-size: 10px">IM&Aacute;GENES</label>
										                        	</div>	
							                               		</a>

							                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="alert('¡COMPRAR LICENCIA COMPLETA!');" > 
										                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
										                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
										                        		<label style="font-size: 10px">PROCEDIMIENTOS <br>ENDOSC&Oacute;PICOS</label>
										                        	</div>	
							                               		</a>

							                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="alert('¡COMPRAR LICENCIA COMPLETA!')"> 
										                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
										                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
										                        		<label style="font-size: 10px">PROCEDIMIENTOS <br> FUNCIONALES</label>
										                        	</div>	
							                               		</a>

							                               		<a class="btn btn-info btn_ordenes" style="color: white; " onClick="alert('¡COMPRAR LICENCIA COMPLETA!')" > 
										                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
										                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
										                        		<label style="font-size: 10px">BIOPSIAS</label>
										                        	</div>	
							                               		</a>
										                   	</div>
					           							</div>
												</div>

												<div class="col-md-4" style="padding: 3px;left: 0px;"> 
													<div class="box box-solid box-primary recuadro" >
														<div class="fila2">	
					                						<div class="box-header" >
								                        		<div class="row" style="margin-left: 0px">
					                								<div style="margin-right: 5px">
					                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
					                								</div>
										                        	<div>
										                        		<h3 class="box-title" style="font-size: 12px; color: white"> PROCEDIMIENTOS <br> FUNCIONALES</h3>
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
						           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
						           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_funcional)) {{$doctor_procedimiento_funcional->id_procedimiento}} @endif</div>
					           						 		</div>
					           						 			@if(!is_null($doctor_procedimiento_funcional))
											                        @if(!is_null($doctor_procedimiento_funcional->f_operacion))
												                        @php 
												                        $dia =  Date('N',strtotime($doctor_procedimiento_funcional->f_operacion)); 
												                        $mes =  Date('n',strtotime($doctor_procedimiento_funcional->f_operacion)); @endphp
								                                   		<b>
								                                        @if($dia == '1') Lunes 
									                                         @elseif($dia == '2') Martes
									                                         @elseif($dia == '3') Miércoles 
									                                         @elseif($dia == '4') Jueves 
									                                         @elseif($dia == '5') Viernes 
									                                         @elseif($dia == '6') Sábado 
									                                         @elseif($dia == '7') Domingo 
								                                        @endif 
								                                         	{{substr($doctor_procedimiento_funcional->f_operacion,8,2)}} de 
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
								                                         	del {{substr($doctor_procedimiento_funcional->f_operacion,0,4)}}</b>
								                                    @else 
								                                     	@php 
												                        $dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini)); 
												                        $mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini)); @endphp
								                                   		<b>
								                                        @if($dia == '1') Lunes 
									                                         @elseif($dia == '2') Martes
									                                         @elseif($dia == '3') Miércoles 
									                                         @elseif($dia == '4') Jueves 
									                                         @elseif($dia == '5') Viernes 
									                                         @elseif($dia == '6') Sábado 
									                                         @elseif($dia == '7') Domingo 
								                                        @endif 
								                                         	{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini,8,2)}} de 
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
								                                         	del {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_funcional->id_agenda)->first()->fechaini,0,4)}}</b>
											                        @endif
										                        @endif
										                        <br>
									                          <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
									                          <br>
										                       	@if(!is_null($doctor_procedimiento_funcional)) 			
											                        @if(!is_null($doctor_procedimiento_funcional)) 						@if(!is_null($doctor_procedimiento_funcional->id))Dr. 				{{$doctor_procedimiento_funcional->nombre1}} 						{{$doctor_procedimiento_funcional->apellido1}}
											                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
											                        	@endif 
										                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
										                        	@endif
										                        @endif

										                       <br>
									                          <span style="font-family: 'Helvetica general';">Procedimiento</span>
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
												<div class="col-md-4" style="padding: 3px;left: 0px; z-index: 998;"> 
													<div class="box box-solid box-primary recuadro" >	
														<div class="fila2">
					                						<div class="box-header" >
								                        		<div class="row" style="margin-left: 0px">
					                								<div style="margin-right: 5px">
					                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
					                								</div>
										                        	<div>
										                        		<h3 class="box-title" style="font-size: 12px; color: white"> BIOPSIAS</h3>
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

					           						 	<div class="box-body cuerpo" >
					           						 		<center>
					               						 		<table style="text-align: center; margin-top: 25px"   >
					               						 			<tr>
					               						 				<td style="width: 80px; font-family: 'Helvetica general';"> Fecha </td>
					               						 				<td style="width: 80px; font-family: 'Helvetica general';"> Acci&oacute;n</td>
					               						 			</tr>
					               						 			
					           						 				<tr>
					           						 					@if(!is_null($biopsias_1))
					           						 					<td>
					           						 					@if(!is_null($biopsias_1)) {{substr($biopsias_1->created_at,0,10)}} @endif
					           						 					</td>
					           						 					<td>
						           						 					<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$biopsias_1->id}}" >
						           						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
									                								<div style="margin-right: 0px">
									                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
									                								</div>

														                        	<div>
														                        		<label style="color: white" >Ver Resultados</label>
														                        	</div>
											                        			</div>
						           						 					</a>
					           						 					</td>
					           						 					@endif
					           						 				</tr>
					           						 				<tr>
					           						 					<td><br></td>
					           						 				</tr>
					           						 				<tr>
					           						 					@if(!is_null($biopsias_2))
					           						 					<td>
					           						 						@if(!is_null($biopsias_2)) {{substr($biopsias_2->created_at,0,10)}} @endif
					           						 					</td>
					           						 					<td>
					               						 					<a class="btn btn-info btn_accion" href="{{asset('hc_ima_nombre')}}/{{$biopsias_2->id}}" >
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
					               						 				@endif
					           						 				</tr>
					               						 		</table>
					           						 		</center>
					       								</div>
													</div>
												</div>
											</div>
											</div>
					<!--parte 3-->
											<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
												<div class="row" style="margin-left: 10px;">				
													<div class="col-md-4" style="padding: 3px;  "> 
														<div class="box box-solid box-primary recuadro" >	
															<div class="fila3">
						                						<div class="box-header" >
									                        		<div class="row" style="margin-left: 0px">
						                								<div style="margin-right: 5px">
						                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
						                								</div>

											                        	<div>
											                        		<h3 class="box-title" style="font-size: 12px; color: white">ESTUDIOS</h3>
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
						           						 		@if(!is_null($doctor_procedimiento_endoscopico)) 
										                        	@if(isset($doctor_procedimiento_endoscopico->nombre))
										                        	<div style="color: black, font-size: 10px; ">
												  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
												  						<div style="font-family: 'Helvetica general'; text-align: center;">
												  						@if(strlen($doctor_procedimiento_endoscopico->nombre) >= '25')
												  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}...
												  						@else
												  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}
												  						@endif
												  						</div>
											  						</div>
										                        	
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

												  						if(strlen($texto) >= '25')
												  							$texto = substr($texto,0,25).'...';
												  					@endphp
											  						<div style="color: black, font-size: 10px; ">
												  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
												  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
											  						</div>					  						
										                       		@endif
										                        @endif 
						           						 		<center>
						           						 		<table style="text-align: center; margin-top: 25px" >
						           						 			<tr>
						           						 				<td style="width: 80px; font-family: 'Helvetica general';"> Fecha</td>
						           						 				<td style="width: 80px; font-family: 'Helvetica general';"> Acci&oacute;n</td>
						           						 			</tr>
						       						 				<tr>
						       						 					<td>
						       						 						@if(!is_null($doctor_procedimiento_endoscopico))
						       						 							@php
							       						 							$fecha_proc = \Sis_medico\agenda::where('id', $doctor_procedimiento_endoscopico->id_agenda)->first()->fechaini;

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
						       						 						@if(!is_null($doctor_procedimiento_endoscopico))
						       						 							@php
															  						$id_protocolo = $doctor_procedimiento_endoscopico->hc_proto_id;
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
														                        		<label style="color: white" >Ver Resultados</label>
														                        	</div>
											                        			</div>
						           						 					</a>
						           						 					@else
						           						 					<div>No posee imagenes de procedimientos</div>
						           						 					@endif
						       						 					</td>
						       						 				</tr>
						           						 		</table>
						           						 		</center>
						       								</div>
														</div>
													</div>
													
													<div class="col-md-4" style="padding: 3px;left: 0px;"> 
														<div class="box box-solid box-primary recuadro" >
															<div class="fila3">
						                						<div class="box-header" >
									                        		<div class="row" style="margin-left: 0px">
						                								<div style="margin-right: 5px">
						                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/exa_externos.png">
						                								</div>
											                        	<div>
											                        		<h3 class="box-title" style="font-size: 12px; color: white"> RESULTADOS <br> EXTERNOS</h3>
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
						           						 				<td style="width: 80px; font-family: 'Helvetica general';">Fecha </td>
						           						 				<td style="width: 80px; font-family: 'Helvetica general';" >Acci&oacute;n</td>
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
														                        		<label style="color: white">Ver Resultados</label>
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
													<div class="col-md-4" style="padding: 3px; z-index: 998; "> 
														<div class="box box-solid box-primary recuadro" >	
															<div class="fila3">
								                				<div class="box-header" >


						                                            <div class="row" style="margin-left: 0px">
						                								<div style="margin-right: 5px">
						                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/lab.png">
						                								</div>
											                        	<div>
											                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;LABORATORIO</h3>
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
								               						 		<td style="width: 80px; font-family: 'Helvetica general';"> Fecha </td>
								               						 		<td style="width: 80px; font-family: 'Helvetica general';"> Acci&oacute;n</td>
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
																                        	<label style="color: white">Ver Resultados</label>
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
												</div>
											</div>
					<!--parte 4-->
											<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
												<div class="row" style="margin-left: 10px;">				
													<div class="col-md-4" style="padding: 3px;  "> 
														<div class="box box-solid box-primary recuadro" >	
															<div class="fila3">
						                						<div class="box-header" >
									                        		<div class="row" style="margin-left: 0px">
						                								<div style="margin-right: 5px">
						                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
						                								</div>

											                        	<div>
											                        		<h3 class="box-title" style="font-size: 12px; color: white">IMAGENES</h3>
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
						           						 		@if(!is_null($doctor_procedimiento_endoscopico)) 
										                        	@if(isset($doctor_procedimiento_endoscopico->nombre))
										                        	<div style="color: black, font-size: 10px; ">
												  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
												  						<div style="font-family: 'Helvetica general'; text-align: center;">
												  						@if(strlen($doctor_procedimiento_endoscopico->nombre) >= '25')
												  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}...
												  						@else
												  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}
												  						@endif
												  						</div>
											  						</div>
										                        	
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

												  						if(strlen($texto) >= '25')
												  							$texto = substr($texto,0,25).'...';
												  					@endphp
											  						<div style="color: black, font-size: 10px; ">
												  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
												  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
											  						</div>					  						
										                       		@endif
										                        @endif 
						           						 		<center>
						           						 			@if(!is_null($doctor_procedimiento_endoscopico))
							           						 			@php
														  						$id_protocolo = $doctor_procedimiento_endoscopico->hc_proto_id;
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
							           						 					NO POSEE IMAGENES DEL PROCEDIMIENTO
							           						 				</div>
						           						 				@endif
					           						 				@endif
						           						 		</center>
						       								</div>
														</div>
													</div>
												</div>
											</div>
					<!--	<div class="col-md-4" style="padding: 3px;left: 0px;"> 
									<div class="box box-solid box-primary recuadro" >
										<div class="fila4" >
	                						<div class="box-header"">
						                        		<h3 class="box-title" style="font-size: 13px; color: white"> <i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp; RESULTADOS EXTERNOS </h3>
								                        <div class="box-tools">
									                          <button type="button" class="btn btn-box-tool" 
									                           data-widget="collapse" style="color: white"><i class="fa fa-plus"></i> 
									                          </button>
								                        </div>
	               						    </div>
	               						</div>

               						 	<div class="box-body cuerpo">
               						 		<center>
               						 		<table style="text-align: center; margin-top: 25px" >
               						 			
               						 			<tr>
               						 				<td style="width: 40px; font-family: 'Helvetica general';"> Fecha </td>
               						 				<td style="font-family: 'Helvetica general';"> Acci&oacute;n</td>
               						 			</tr>
           						 				<tr>
           						 					<td>2019-02-13</td>
           						 					<td><button style="color: white; background-color: #004AC1; border-radius: 4px" class="btn btn-info boton-3">Ver Resultados</button></td>
           						 				</tr>
           						 				<tr>
           						 					<td>2019-02-11</td>
           						 					<td><button style="color: white; background-color: #004AC1; border-radius: 4px" class="btn btn-info boton-3">Ver Resultados</button></td>
           						 				</tr>
               						 		</table>
               						 		</center>
           							    </div>
						            </div>
						</div>-->

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
		      	console.log(remoto_href);
		    	console.log($(this).data('target'));

		    	console.log(jQuery(this).data('target') + ' .modal-content');
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
	            var edad;
	            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
	            edad = calcularEdad(fecha_nacimiento);
	            
	            $('#edad').val( edad );
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


	    


	  	function cargar_procedimiento_endoscopico(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_endoscopico', ['id_paciente' => $paciente->id])}}", 
				data: "",
				datatype: "html",
				success: function(datahtml){
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
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});	
		}


		function cargar_imagenes_paciente(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.imagenes_paciente', ['id_paciente' => $paciente->id])}}", 
				data: "",
				datatype: "html",
				success: function(datahtml){
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
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}

		function cargar_biopsias(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.biopsias', ['id_paciente' => $paciente->id])}}", 
				data: "",
				datatype: "html",
				success: function(datahtml){
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


	</script>
</section>


@endsection




