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
		background-color: #7FC6F5;
		height: 40px;
	}

	.contenido_btn_ordenes{
		color: white;
		height: 20px;
		width: 20px;
		font-size: 12px;

	}
	.select2-selection__choice{
		background-color: #da291c !important;
		border-color: #da291c !important;
	}
	.btn-success{
		background-color: white;
		color: red !important;
		border-color: red !important;
	}

	.btn-success:hover{
		background-color: #004AC1 !important;
	}
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
		background-color: #da291c !important;
		border-color: #da291c !important;
	}

	.btn-block{
      background-color: #004AC1;
    }
    .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .image-checkbox {
        cursor: pointer;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border: 4px solid transparent;
        margin-bottom: 0;
        outline: 0;
    }
    .image-checkbox input[type="checkbox"] {
        display: none;
    }

    .image-checkbox-checked {
        border-color: #4783B0;
    }
    .image-checkbox .fa {
      position: absolute;
      color: #4A79A3;
      background-color: #fff;
      padding: 10px;
      top: 0;
      right: 0;
    }
    .image-checkbox-checked .fa {
      display: block !important;
    }

    .hidden{
    	display: none; !important;
    }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">


<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

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
			        </div>
		        </div>
            </div>
		</div>
	</div>

	<div class="container-fluid" >
		<div class="row">
			<div class="col-md-7 area_1_dato"  style="padding-left: 5px; padding-right: 10px; " >
				<div class="row">
					<div class="col-md-12">
						<div style="border: 2px solid #004AC1; border-radius: 8px; margin-left: 8px; margin-bottom: 5px" >
							<div class="col-md-12 responsive_filiacion " style="" >
								<div class="row">
									<div class="col-lg-9" style="padding-left: 0px; padding-right: 15px;" >
										<label style="margin-top: 10px; color: #004AC1; font-family: arial ; margin-left: 20px" >
												<span style="font-family: 'Helvetica general';">
													<i class="fa fa-exclamation-circle"></i> Datos Principales del Paciente
												</span>
										</label>
										<table class="detalle_tabla" style="margin-left: 20px; margin-bottom: 5px; text-align: left; margin-top: 5px"   >
											<tr >
												<td style="font-size: 11px; width: 300px"> <span style="font-family: 'Helvetica general';"> Paciente </span> </td>
												<td style="font-size: 11px; width: 100px"> <span style="font-family: 'Helvetica general';">Identificacion</span></td>
												<td style="font-size: 11px; width: 60px"> <span style="font-family: 'Helvetica general';">Edad</span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">Seguro</span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">Cortesia</span></td>
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
											<input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->observacion}}" autocomplete="off" type="text" name="observacion" placeholder="OBSERVACIONES GENERALES DEL PACIENTE">
											<label style=" color: black; font-size: 11px; margin-left: 20px">
											<span style="font-family: 'Helvetica general';"> H&aacutebitos </span>
										    </label>
										    <input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->alcohol}}" autocomplete="off" type="text" name="habitos" placeholder="H&Aacute;BITOS DEL PACIENTE">
											<div class="col-md-12" style="padding: 10px">
												<div class="col-md-12" style="padding-left: 0px; padding-right: 0px; text-align: center;">
													<div class="row antecedentes_responsive">
														<div class="col-lg-4 col-sm-12">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">Antecedentes Patologicos</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_patologicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_pat}}</textarea>
																	<!--<input style="width: 100%" type="text" name="an_patologicos" value="{{$paciente->antecedentes_pat}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">Antecedentes Familiares</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_familiares" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_fam}}</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">Antecedentes Quirurgicos</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_quirurgicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_quir}}</textarea>
															<!--<div class="col-md-12 col-sm-6 col-12"><input style="width: 100%" type="text" name="an_quirurgicos" value="{{$paciente->antecedentes_quir}}" onchange="guardar_ob();"></div>-->
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
									<div class="col-lg-3 col-12" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
										<div class="row">
											<div class="col-md-12 col-sm-6 col-12" style="margin-bottom: 15px">
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

											<div class="col-md-12 col-sm-6 col-12" style="margin-bottom: 15px;text-align: left;">
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
						    	@if($fendoscopicos > 0)
						    		<a onclick="agregar_procedimiento_hc('endoscopico_id');"  class="btn-danger boton_burbuja">
						    			<span >El paciente tiene {{$fendoscopicos}} @if ($fendoscopicos > 1) procedimientos endoscopicos @else procedimiento endoscopico @endif por crear el dia de hoy, presiones aqui para atender</span>
						    		</a>
						    		<input type="hidden" id="endoscopico_id" value="{{route('hc4_procedimiento.selecciona_procedimiento2',['tipo' => '0', 'paciente' => $paciente->id, 'hcid' => $hc->hcid ])}}">
						    		<br>
						    	@endif
						    	@if($ffuncional > 0)
						    		<a onclick="agregar_procedimiento_hc('funcional_id');"  class="btn-danger boton_burbuja">
						    			<span>El paciente tiene {{$ffuncional}} @if ($ffuncional > 1) procedimientos funcionales @else procedimiento funcional @endif por crear el dia de hoy , presiones aqui para atender</span>
						    		</a>
						    		<input type="hidden" id="funcional_id" value="{{route('hc4_procedimiento.selecciona_procedimiento2',['tipo' => '0', 'paciente' => $paciente->id, 'hcid' => $hc_funcional->hcid ])}}">
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
					    			<span id="pconsulta">El paciente tiene {{$xagenda}} @if ($xagenda > 1) consultas @else consulta @endif el dia de hoy, presiones aqui para atender</span>
					    		</a>
                                @endif
                            </div>
						    <br>
						</div>
					</div>
					<br>
					<div class="col-md-12" id="area_trabajo" style="padding-left: 23px">
						<style type="text/css">
							.table>tbody>tr>td, .table>tbody>tr>th {
							    padding: 0.4% ;
							}
						</style>
						<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
						<style type="text/css">
						  .icheckbox_flat-green.checked.disabled {
						        background-position: -22px 0 !important;
						        cursor: default;
						    }
						    #mceu_61{
						        display: none;
						    }
						    .nopad {
						        padding-left: 0 !important;
						        padding-right: 0 !important;
						    }
						    /*image gallery*/
						    .image-checkbox {
						        cursor: pointer;
						        box-sizing: border-box;
						        -moz-box-sizing: border-box;
						        -webkit-box-sizing: border-box;
						        border: 4px solid transparent;
						        margin-bottom: 0;
						        outline: 0;
						    }
						    .image-checkbox input[type="checkbox"] {
						        display: none;
						    }

						    .image-checkbox-checked {
						        border-color: #4783B0;
						    }
						    .image-checkbox .fa {
						      position: absolute;
						      color: #4A79A3;
						      background-color: #fff;
						      padding: 10px;
						      top: 0;
						      right: 0;
						    }
						    .image-checkbox-checked .fa {
						      display: block !important;
						    }
						    .parpadea {

						      animation-name: parpadeo;
						      animation-duration: 1s;
						      animation-timing-function: linear;
						      animation-iteration-count: infinite;

						      -webkit-animation-name:parpadeo;
						      -webkit-animation-duration: 1s;
						      -webkit-animation-timing-function: linear;
						      -webkit-animation-iteration-count: infinite;
						    }

						    @-moz-keyframes parpadeo{
						      0% { opacity: 1.0; }
						      50% { opacity: 0.0; }
						      100% { opacity: 1.0; }
						    }

						    @-webkit-keyframes parpadeo {
						      0% { opacity: 1.0; }
						      50% { opacity: 0.0; }
						       100% { opacity: 1.0; }
						    }

						    @keyframes parpadeo {
						          0% { opacity: 1.0; }
						           50% { opacity: 0.0; }
						          100% { opacity: 1.0; }
						    }
						</style>


						<div class="box" style="border: 2px solid #004AC1; background-color: #ececed; border-radius: 3px; ">
							<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;">
							    <h3 class="box-title"><img style="width: 41px; margin-left: 15px" src="{{asset('/')}}hc4/img/iconos/imagenes.png"> <b>IMAGENES</b></h3>

							    <!-- /.box-tools -->
							</div>
						  <!-- /.box-header -->
							  <div class="box-body" style="background-color: #56ABE3;">
							  	<div class="col-md-12">
								  	<div class="row parent" >
								    	<div class="col-md-12" >
								    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
											  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
											    <span><b>Guardado de Im&aacute;genes y Video</b><span>
											    <!-- /.box-tools -->
											  </div>
											  <div class="box-body" style="background: white;">
											  	<div class="row">
											  		<div class="col-md-12" >
											  			<input id="id_hc_procedimientos" type="hidden" name="id_hc_procedimientos" value="{{ $id }}">
									                    <div class="row">
									                        <div class="col-md-9" style="padding-right: 2px;">
									                            <video id="video" style="width: 100%" autoplay="true"/>
									                        </div>
									                        <div class="col-md-3" style="padding-right: 2px; padding-left: 10px">
											                    <div class="col-md-12" >
											                    	<span style="font-family: 'Helvetica general3';">Imagenes Capturadas: </span>
											                    	<span class="parpadea" id="cimagenes" style="color:white; background-color: red; padding: 5px; font-weight: 800px;"><b>{{$cimagenes}}</b></span>
											                    </div>
											                    <div class="col-md-12" >
											                    	<span style="font-family: 'Helvetica general3';">Videos Capturados: </span>
											                    	<span class="parpadea" id="cvideo" style="color: white; background-color: red; padding: 5px; font-weight: 800px; "><b>{{$cvideo}}</b></span>
											                    </div>
									                        	<div class="col-md-12">
									                        		<span class="btn btn-success texto" id="color_grabacion" style="width: 100%;">En Espera</span>
									                        	</div>
									                        	<div class="col-sm-12"> &nbsp;</div>
										                        <div class="col-md-12">
										                            <button id="tomar" class="btn btn-danger btn_ordenes" style="height: 100%; font-size: 16px;">Tomar Foto</button>
										                        </div>
										                        <div class="col-md-12">
										                            <section class="experiment recordrtc">
										                                <h2 class="header">
										                                    <input type="hidden" class="recording-media" value="record-video" />
										                                    <input type="hidden" class="media-container-format" value="Mp4" />
										                                    <button style="height: 100%; font-size: 16px;" id="grabacion" class="btn btn-danger btn_ordenes">Start Recording</button>
										                                </h2>

										                                <div style="text-align: center; display: none;" class="oculto">
										                                    <button class="btn btn-primary" id="save-to-disk">Save To Disk</button>
										                                    <button class="btn btn-primary" id="open-new-tab">Open New Tab</button>
										                                    <button class="btn btn-primary" id="upload-to-server">Upload To Server</button>
										                                </div>

										                                <br>

										                                <video style="display: none;" controls muted></video>
										                            </section>
										                        </div>
									                        </div>
									                        <!--  inputs para el grabado de imagenes -->
													        <input type="hidden" class="recording-media" value="record-audio-plus-video">
													        <input type="hidden" class="media-container-format" value="mp4">
													        <input type="hidden" class="media-resolutions" value="default">
													        <input type="hidden" class="media-framerates" value="default">
													        <input type="hidden" class="media-bitrates" value="default">
									                        <div class="col-md-6">
									                            <div></div>
									                            <form  id="frm2"  method="POST"   enctype="multipart/form-data">
									                                <input type="hidden" name="id_hc_protocolo" value="{{$id}}">
									                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
									                                <input style="display: none;" type="file" id="pic" name="pic" />
									                                <input type="hidden" id="imageName" name="imageName" />
									                                <input type="hidden" id="contentType" name="contentType" />
									                                <input type="hidden" id="imageData" name="imageData" />
									                                <canvas id="canvas" width="1280" height="720" style="display: none;"></canvas>
									                            </form>
									                        </div>
									                    </div>
											  		</div>

											  	</div>
											  </div>
											</div>
								    	</div>

								    	<div class="col-md-12" >
								    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
											  <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004ac1;">
											    <span><b>Imagenes del Procedimiento</b><span>
											    <!-- /.box-tools -->
											  </div>
											  <div class="box-body" style="background: white;">
											  	<div class="row">
								                    <div class="col-md-12">
									                    <div class="row" style="padding: 0px 5px; " id="fotos_agregar">
								                            @foreach($imagenes as $imagen)

								                            <div class="col-md-3" style='margin: 10px 0;padding: 2px;' >
								                                @php
								                                    $explotar = explode( '.', $imagen->nombre);
								                                    $extension = end($explotar);
								                                @endphp
								                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG')|| ($extension == 'bmp') || ($extension == 'BMP'))
								                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
								                                        <img  src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}" style='width:100%;'>
								                                    </a>
								                                @elseif(($extension == 'pdf') || ($extension == 'PDF'))
								                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
								                                        <img  src="{{asset('imagenes/pdf.png')}}" style='width:100%;'>
								                                    </a>
								                                @elseif(($extension == 'mp4'))
								                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
								                                        <img  src="{{asset('imagenes/video.png')}}" style='width:100%;'>
								                                    </a>
								                                @else
								                                    
								                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
								                                        <img  src="{{asset('imagenes/office.png')}}" width="70%">
								                                    </a>
								                                @endif
								                            </div>
								                            @endforeach
									                    </div>
							                    	</div>
											  	</div>
											  </div>
											</div>
								    	</div>
								    	<div class="col-md-12" >
								    		<div class="box box-primary">
								                <div class="box-body">
								                    <div class="row">
								                        <div class="form-group col-md-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
								                            <label for="conclusiones" class="col-md-12 control-label"><b>Ingreso de Imagenes del Procedimiento</b></label>
								                            <div class="col-md-12">
								                                <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="addimage">
							                                        <input type="hidden" name="id_hc_protocolo" value="{{$id}}">
							                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
							                                        <div class="fallback">
							                                        </div>
								                                </form>
								                            </div>
								                        </div>
								                    </div>
								                </div>
							            	</div>
								    	</div>

								        <div class="col-md-12">
								            <div class="box box-primary">
								                <div class="box-header with-border">
								                    <div class="col-md-9">
								                        <h4> Historial de Imagenes del Paciente </h4>
								                    </div>
								                </div>
								                <div class="box-body">
								                    <form role="form" method="POST" action="{{ route('hc_video.nuevas_ima')}}">
								                        {{ csrf_field() }}
								                        <input type="hidden" name="id_hc_protocolo" value="{{$id}}">
								                        <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
								                        <div class="col-md-12">
								                            <div class="table-responsive col-md-12" id="fotos_agregar">
								                            	<div class="row">
								                                @foreach($imagenes2 as $imagen)
								                                <div class="col-md-2  text-center" style='' >
								                                    <label class="image-checkbox">
								                                        @php
								                                            $explotar = explode( '.', $imagen->nombre);
								                                            $extension = end($explotar);
								                                        @endphp
								                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPEG') || ($extension == 'PNG')|| ($extension == 'JPG') || ($extension == 'bmp') || ($extension == 'BMP'))
								                                            <img  src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}" width="90%" style="max-height: 84px;">
								                                        @elseif(($extension == 'pdf'))
								                                            <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 84px;">
								                                        @elseif(($extension == 'mp4'))
								                                            <img  src="{{asset('imagenes/video.png')}}" width="90%" style="max-height: 84px;">
								                                        @else
								                                            <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 84px;">
								                                        @endif
								                                        <input type="checkbox" name="image[]" value="{{$imagen->id}}" />
								                                        <br>
								                                        <span style="text-align: center;">{{substr($imagen->created_at, 0, -9)}}</span>
								                                        <i class="fa fa-check hidden"></i>
								                                    </label>
								                                </div>
								                                @endforeach
								                                </div>
								                            </div>
								                        </div>
								                        <div class="form-group">
								                            <div class="col-md-6 col-md-offset-6">
								                                <input type="submit" value="Agregar" class="btn btn-primary">
								                                <button type="submit" class="btn btn-primary" formaction="{{route('hc_video.descargar_zip')}}">Descargar Seleccionadas</button>
								                            </div>
								                        </div>

								                    </form>
								                </div>
								            </div>
								        </div>
								    </div>
							  	</div>
							</div>
						  <!-- box-footer -->
						</div>
					</div>
				</div>
			</div>

				<div id="area_trabajo_2" class="col-md-5" style="padding-left: 0px; padding-right: 0px; ">
					<div class="row" style="margin-right: 25px;">
<!--parte 1-->
					<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
						<div class="row" style="margin-left: 10px;">
							<div class="col-lg-4 col-md-6" style="padding: 3px;">
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

		   						 	<div class="box-body cuerpo" ">

		   						 		<div class="row">
			   						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
			   						 		<div class="col-md-6" style="color: white"> @if(!is_null($consulta_nueva)) {{$consulta_nueva->hcid}} @endif</div>
		   						 		</div>
		   						 		@if(!is_null($consulta_nueva))
		   						 		@php
		   						 			$agenda_fecha_consulta = DB::table('agenda as a')->where('a.id', $consulta_nueva->id_agenda)->first();
		   						 		@endphp
		   						 		@if(!is_null($consulta_nueva))
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
					                        <?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30) . '...'; ?>

					                        @else
					                        	 <?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30); ?>
					                        @endif
					                        @endif
					                    @endif
			                   	    </div>
								</div>
							</div>

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px;">
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

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px; z-index: 998;">
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
           						 	<div class="box-body cuerpo" ">

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
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
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
					                        		<label style="font-size: 10px">LABORATORIO</label>
					                        	</div>
			                                </a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white" onClick="carga_ordenes_imagenes();">
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
					                        		<label style="font-size: 10px">IM&Aacute;GENES</label>
					                        	</div>
		                               		</a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_proendospico();" >
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
					                        		<label style="font-size: 10px">PROCEDIMIENTOS <br>ENDOSC&Oacute;PICOS</label>
					                        	</div>
		                               		</a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_profuncional();">
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

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px;">
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
			           						 		<div class="col-md-6" style="color: white"> 										@if(!is_null($doctor_procedimiento_funcional)) 										{{$doctor_procedimiento_funcional->id_procedimiento}}
			           						 			@endif
			           						 		</div>
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
							                        <b>@if(!is_null($doctor_procedimiento_funcional)) @if(!is_null($doctor_procedimiento_funcional->id))Dr. {{$doctor_procedimiento_funcional->nombre1}} {{$doctor_procedimiento_funcional->apellido1}}@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}@endif @else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}@endif</b>
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

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px;z-index: 998;">
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
           						 					<td>@if(!is_null($biopsias_1)) {{substr($biopsias_1->created_at,0,10)}} @endif</td>
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
           						 					<td>@if(!is_null($biopsias_2)) {{substr($biopsias_2->created_at,0,10)}} @endif</td>
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
							@if(Auth::user()->id == '1307189140')
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">BANCO DE IMAGENES</h3>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@else
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> ARMAR ESTUDIOS</h3>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
							@endif

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 10px; color: white">PRE-VISUALIZAR ESTUDIOS</h3>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
							<div class="col-lg-4 col-md-6" style="padding: 3px;z-index: 998;">
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
							@if(Auth::user()->id == '1307189140')
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">EPICRISIS</h3>
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
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr($nuevo_armado->f_operacion,8,2)}} de
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
	                                        		del {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
						                        @endphp

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,8,2)}} de
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
	                                        		del {{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
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
				                        <span style="font-family: 'Helvetica general';">Procedimiento </span>
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
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">BANCO DE IMAGENES</h3>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
						  						<div style="font-family: 'Helvetica general'; text-align: center">Procedimiento</div>
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
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@endif

							<div class="col-lg-4 col-md-6" style="padding: 3px;left: 0px; ">
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

							<div class="col-lg-4 col-md-6" style="padding: 3px; z-index: 998; ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
		                				<div class="box-header" >
	                                        <div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>
					                        	<div>
					                        		<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;ECOGRAFIAS</h3>
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

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr($doctor_procedimiento_ecografia->f_operacion,8,2)}} de
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
	                                        		del {{substr($doctor_procedimiento_ecografia->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini));
						                        @endphp

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini,8,2)}} de
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
	                                        		del {{substr(\Sis_medico\agenda::where('id', $doctor_procedimiento_ecografia->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
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
				                        <span style="font-family: 'Helvetica general';">Procedimiento </span>
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
							@if(Auth::user()->id == '1307189140')
							@else
							<div class="col-lg-4 col-md-6" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white">EPICRISIS</h3>
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
           						 		<div class="col-md-6"> <span style="font-family: 'Helvetica general';">Fecha</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr($nuevo_armado->f_operacion,8,2)}} de
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
	                                        		del {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$dia =  Date('N',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
	                                        		$mes =  Date('n',strtotime(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini));
						                        @endphp

	                                        	@if($dia == '1') Lunes
		                                        	@elseif($dia == '2') Martes
		                                        	@elseif($dia == '3') Miércoles
		                                        	@elseif($dia == '4') Jueves
		                                        	@elseif($dia == '5') Viernes
		                                        	@elseif($dia == '6') Sábado
		                                        	@elseif($dia == '7') Domingo
	                                        	@endif
	                                        		{{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,8,2)}} de
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
	                                        		del {{substr(\Sis_medico\agenda::where('id', $nuevo_armado->id_agenda)->first()->fechaini,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
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
				                        <span style="font-family: 'Helvetica general';">Procedimiento </span>
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
						</div>
					</div>

		</div>
	</div>

<script type="text/javascript">
	$(".image-checkbox").each(function () {
      if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
        $(this).addClass('image-checkbox-checked');
      }
      else {
        $(this).removeClass('image-checkbox-checked');
      }
    });

    // sync the state to the input
    $(".image-checkbox").on("click", function (e) {
      $(this).toggleClass('image-checkbox-checked');
      var $checkbox = $(this).find('input[type="checkbox"]');
      $checkbox.prop("checked",!$checkbox.prop("checked"))

      e.preventDefault();
    });
	var remoto_href = '';
	jQuery('body').on('click', '[data-toggle="modal"]', function() {
	    if(remoto_href != jQuery(this).data('remote')) {
	        remoto_href = jQuery(this).data('remote');
	        jQuery(jQuery(this).data('target')).removeData('bs.modal');

	        jQuery(jQuery(this).data('target')).find('.modal-body').empty();
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

    function scroll_datos(id){
		$('html, body').animate({
	      scrollTop: $(id).offset().top
	    }, 1000)
	}

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

				scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
				$('#msn1').text(mensaje);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}

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



	function cargar_procedimiento_funcional(){
		$.ajax({
			type: "GET",
			url: "{{route('paciente.procedimiento_funcional', ['id_paciente' => $paciente->id])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){
				$("#area_trabajo").html(datahtml);
				scroll_datos("#area_trabajo");
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

	function cargar_imagenes_paciente(){
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

	function carga_resultado_laboratorio(id_or){

      window.open('{{url('inicio/laboratorio/hc4resultados/imprimir')}}/'+id_or,'_blank');

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

</script>

<script src="{{ asset ("/js/dropzone.js") }}"></script>
<script src="{{asset('hc4/js/grabador/RecordRTC.js')}}"></script>
<script src="{{asset('hc4/js/grabador/gif-recorder.js')}}"></script>
<script src="{{asset('hc4/js/grabador/getScreenId.js')}}"></script>
<!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
<script src="{{asset('plugins/video/gumadapter.js')}}"></script>
<script>

    Dropzone.options.addimage = {
      acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif, .dcm",
      maxFiles: 50,
    };


    //constants
    var MAX_WIDHT = 1280,
        MAX_HEIGHT = 720;

    var URL = window.URL;

    var inputFile = document.getElementById('pic');

    var video_foto = document.getElementById('video');
    var localStream = null;
    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var errBack = function(e) {
        console.log('Opps.. no se puede utilizar la camara', e);
    };
    navigator.getUserMedia = navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia;

    window.URL = window.URL ||
                 window.webkitURL ||
                 window.mozURL ||
                 window.msURL;

    window.addEventListener('load', function() {

      navigator.getUserMedia({
                  video: true,
                  audio:false
                },
                function(stream) {
                  var
                      video = document.querySelector('video');
                      video.srcObject  = stream;
                },
                function(e) {
                  console.log(e);
                });

        }, false);
    //captura de video
    document.getElementById('tomar').addEventListener('click', function(event) {
        //elements
        var canvas = document.getElementById('canvas'),
            ctx = canvas.getContext('2d');

        context.drawImage(video_foto, 0, 0, 1280, 720   );
        //envio de datos por ajax

        var canvas = document.getElementById('canvas');
        var dataURL = canvas.toDataURL();

        //console.log("empieza guardado");


        $.ajax({
        	async: false,
            type: 'post',
            url: '{{route('hc_video.guardado_foto', ['id_protocolo' => $id])}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: {
                imgBase64: dataURL
            },
            success: function (result) {

				//nuevo
				// Agrega la imagen al principio del contenido existente
				$("#fotos_agregar").prepend("<div class='col-md-3' style='margin: 10px 0;padding: 2px;'><a data-toggle='modal' data-target='#foto' data-remote='{{route('hc_video.mostrar_foto2')}}/"+result.id+"'><img src='{{asset('imagenes/descarga/thumb')}}/"+result.archivo+"' style='width:100%;'></div>");

				// Incrementa el contador de imágenes
				var canterior = parseInt($('#cimagenes').text());
				canterior = canterior + 1;
				$("#cimagenes").text(canterior);

				console.log(result);

				/* Anterior
                anterior = $("#fotos_agregar").html();
                canterior = parseInt($('#cimagenes').text());
                canterior = canterior+1;
                $("#cimagenes").html(canterior);
                 $("#fotos_agregar").html("<div class='col-md-3' style='margin: 10px 0;padding: 2px;'><a data-toggle='modal' data-target='#foto' data-remote='{{route('hc_video.mostrar_foto2')}}/"+result.id+"'><img src='{{asset('hc_ima')}}/"+result.archivo+"' style='width:100%;'></div>"+anterior);
                console.log(result);*/
            }
        });
    });
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });


    $(document).keypress(function(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==13){
            $("#tomar").click();
        }
        if (tecla == 112 || tecla == 80){
            $("#grabacion").click();
        }
    });
</script>
<!-- grabacion del video-->
<script>
    (function() {
        var params = {},
            r = /([^&=]+)=?([^&]*)/g;

        function d(s) {
            return decodeURIComponent(s.replace(/\+/g, ' '));
        }

        var match, search = window.location.search;
        while (match = r.exec(search.substring(1))) {
            params[d(match[1])] = d(match[2]);

            if(d(match[2]) === 'true' || d(match[2]) === 'false') {
                params[d(match[1])] = d(match[2]) === 'true' ? true : false;
            }
        }

        window.params = params;
    })();
</script>
<script>
    var recordingDIV = document.querySelector('.recordrtc');
    var recordingMedia = recordingDIV.querySelector('.recording-media');
    var recordingPlayer = recordingDIV.querySelector('video');
    var mediaContainerFormat = recordingDIV.querySelector('.media-container-format');

    recordingDIV.querySelector('button').onclick = function() {
        var button = this;

        if(button.innerHTML === 'Stop Recording') {
            button.disabled = true;
            button.disableStateWaiting = true;
            setTimeout(function() {
                button.disabled = false;
                button.disableStateWaiting = false;
            }, 2 * 1000);

            button.innerHTML = 'Start Recording';
            $('#color_grabacion').html('En Espera');
            $('#color_grabacion').addClass('btn-success');
            $('#color_grabacion').removeClass('btn-danger');
            $('#color_grabacion').removeClass('parpadea');

            function stopStream() {
                if(button.stream && button.stream.stop) {
                    button.stream.stop();
                    button.stream = null;

                }
            }

            if(button.recordRTC) {
                if(button.recordRTC.length) {
                    button.recordRTC[0].stopRecording(function(url) {
                        if(!button.recordRTC[1]) {
                            button.recordingEndedCallback(url);
                            stopStream();

                            saveToDiskOrOpenNewTab(button.recordRTC[0]);
                            $('#upload-to-server').click();
                            return;
                        }

                        button.recordRTC[1].stopRecording(function(url) {
                            button.recordingEndedCallback(url);
                            stopStream();
                            $('#upload-to-server').click();
                        });
                    });
                }
                else {
                    button.recordRTC.stopRecording(function(url) {
                        button.recordingEndedCallback(url);
                        stopStream();

                        saveToDiskOrOpenNewTab(button.recordRTC);
                        $('#upload-to-server').click();
                    });
                }
            }

            return;
        }

        button.disabled = true;

        var commonConfig = {
            onMediaCaptured: function(stream) {
                button.stream = stream;
                if(button.mediaCapturedCallback) {
                    button.mediaCapturedCallback();
                }

                button.innerHTML = 'Stop Recording';

                $('#color_grabacion').html('Grabando');
                $('#color_grabacion').removeClass('btn-success');
                $('#color_grabacion').addClass('btn-danger');
                $('#color_grabacion').addClass('parpadea');
                button.disabled = false;
            },
            onMediaStopped: function() {
                button.innerHTML = 'Start Recording';
                $('#color_grabacion').html('En Espera');
                $('#color_grabacion').addClass('btn-success');
                $('#color_grabacion').removeClass('btn-danger');
                $('#color_grabacion').removeClass('parpadea');
                if(!button.disableStateWaiting) {
                    button.disabled = false;
                }
            },
            onMediaCapturingFailed: function(error) {
                if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                    InstallTrigger.install({
                        'Foo': {
                            // https://addons.mozilla.org/firefox/downloads/latest/655146/addon-655146-latest.xpi?src=dp-btn-primary
                            URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                            toString: function () {
                                return this.URL;
                            }
                        }
                    });
                }

                commonConfig.onMediaStopped();
            }
        };

        if(recordingMedia.value === 'record-video') {
            captureVideo(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                    disableLogs: params.disableLogs || false,
                    canvas: {
                        width: params.canvas_width || 1280,
                        height: params.canvas_height || 720
                    },
                    frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.src = null;
                    recordingPlayer.srcObject = null;

                    if(mediaContainerFormat.value === 'Gif') {
                        recordingPlayer.pause();
                        recordingPlayer.poster = url;

                        recordingPlayer.onended = function() {
                            recordingPlayer.pause();
                            recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                        };
                        return;
                    }

                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio') {
            captureAudio(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: 'audio',
                    bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                    sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                    leftChannel: params.leftChannel || false,
                    disableLogs: params.disableLogs || false,
                    recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                });

                button.recordingEndedCallback = function(url) {
                    var audio = new Audio();
                    audio.src = url;
                    audio.controls = true;
                    recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                    recordingPlayer.parentNode.appendChild(audio);

                    if(audio.paused) audio.play();

                    audio.onended = function() {
                        audio.pause();
                        audio.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio-plus-video') {
            captureAudioPlusVideo(commonConfig);

            button.mediaCapturedCallback = function() {

                if(webrtcDetectedBrowser !== 'firefox') { // opera or chrome etc.
                    button.recordRTC = [];

                    if(!params.bufferSize) {
                        // it fixes audio issues whilst recording 720p
                        params.bufferSize = 16384;
                    }

                    var audioRecorder = RecordRTC(button.stream, {
                        type: 'audio',
                        bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                        sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                        leftChannel: params.leftChannel || false,
                        disableLogs: params.disableLogs || false,
                        recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                    });

                    var videoRecorder = RecordRTC(button.stream, {
                        type: 'video',
                        disableLogs: params.disableLogs || false,
                        canvas: {
                            width: params.canvas_width || 1280,
                            height: params.canvas_height || 720
                        },
                        frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                    });

                    // to sync audio/video playbacks in browser!
                    videoRecorder.initRecorder(function() {
                        audioRecorder.initRecorder(function() {
                            audioRecorder.startRecording();
                            videoRecorder.startRecording();
                        });
                    });

                    button.recordRTC.push(audioRecorder, videoRecorder);

                    button.recordingEndedCallback = function() {
                        var audio = new Audio();
                        audio.src = audioRecorder.toURL();
                        audio.controls = true;
                        audio.autoplay = true;

                        audio.onloadedmetadata = function() {
                            recordingPlayer.src = videoRecorder.toURL();
                            recordingPlayer.play();
                        };

                        recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                        recordingPlayer.parentNode.appendChild(audio);

                        if(audio.paused) audio.play();
                    };
                    return;
                }

                button.recordRTC = RecordRTC(button.stream, {
                    type: 'video',
                    disableLogs: params.disableLogs || false,
                    // we can't pass bitrates or framerates here
                    // Firefox MediaRecorder API lakes these features
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.srcObject = null;
                    recordingPlayer.muted = false;
                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-screen') {
            captureScreen(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
                    disableLogs: params.disableLogs || false,
                    canvas: {
                        width: params.canvas_width || 1280,
                        height: params.canvas_height || 720
                    }
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.src = null;
                    recordingPlayer.srcObject = null;

                    if(mediaContainerFormat.value === 'Gif') {
                        recordingPlayer.pause();
                        recordingPlayer.poster = url;
                        recordingPlayer.onended = function() {
                            recordingPlayer.pause();
                            recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
                        };
                        return;
                    }

                    recordingPlayer.src = url;
                    recordingPlayer.play();
                };

                button.recordRTC.startRecording();
            };
        }

        if(recordingMedia.value === 'record-audio-plus-screen') {
            captureAudioPlusScreen(commonConfig);

            button.mediaCapturedCallback = function() {
                button.recordRTC = RecordRTC(button.stream, {
                    type: 'video',
                    disableLogs: params.disableLogs || false,
                    // we can't pass bitrates or framerates here
                    // Firefox MediaRecorder API lakes these features
                });

                button.recordingEndedCallback = function(url) {
                    recordingPlayer.srcObject = null;
                    recordingPlayer.muted = false;
                    recordingPlayer.src = url;
                    recordingPlayer.play();

                    recordingPlayer.onended = function() {
                        recordingPlayer.pause();
                        recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                    };
                };

                button.recordRTC.startRecording();
            };
        }
    };

    function captureVideo(config) {
        captureUserMedia({video: true}, function(videoStream) {
            recordingPlayer.srcObject = videoStream;
            recordingPlayer.play();

            config.onMediaCaptured(videoStream);

            videoStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudio(config) {
        captureUserMedia({audio: true}, function(audioStream) {
            recordingPlayer.srcObject = audioStream;
            recordingPlayer.play();

            config.onMediaCaptured(audioStream);

            audioStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureAudioPlusVideo(config) {
        captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
            recordingPlayer.srcObject = audioVideoStream;
            recordingPlayer.play();

            config.onMediaCaptured(audioVideoStream);

            audioVideoStream.onended = function() {
                config.onMediaStopped();
            };
        }, function(error) {
            config.onMediaCapturingFailed(error);
        });
    }

    function captureScreen(config) {
        getScreenId(function(error, sourceId, screenConstraints) {
            if (error === 'not-installed') {
                document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
            }

            if (error === 'permission-denied') {
                alert('Screen capturing permission is denied.');
            }

            if (error === 'installed-disabled') {
                alert('Please enable chrome screen capturing extension.');
            }

            if(error) {
                config.onMediaCapturingFailed(error);
                return;
            }

            captureUserMedia(screenConstraints, function(screenStream) {
                recordingPlayer.srcObject = screenStream;
                recordingPlayer.play();

                config.onMediaCaptured(screenStream);

                screenStream.onended = function() {
                    config.onMediaStopped();
                };
            }, function(error) {
                config.onMediaCapturingFailed(error);
            });
        });
    }

    function captureAudioPlusScreen(config) {
        getScreenId(function(error, sourceId, screenConstraints) {
            if (error === 'not-installed') {
                document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
            }

            if (error === 'permission-denied') {
                alert('Screen capturing permission is denied.');
            }

            if (error === 'installed-disabled') {
                alert('Please enable chrome screen capturing extension.');
            }

            if(error) {
                config.onMediaCapturingFailed(error);
                return;
            }

            screenConstraints.audio = true;

            captureUserMedia(screenConstraints, function(screenStream) {
                recordingPlayer.srcObject = screenStream;
                recordingPlayer.play();

                config.onMediaCaptured(screenStream);

                screenStream.onended = function() {
                    config.onMediaStopped();
                };
            }, function(error) {
                config.onMediaCapturingFailed(error);
            });
        });
    }

    function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
        navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
    }

    function setMediaContainerFormat(arrayOfOptionsSupported) {
        var options = Array.prototype.slice.call(
            mediaContainerFormat.querySelectorAll('option')
        );

        var selectedItem;
        options.forEach(function(option) {
            option.disabled = true;

            if(arrayOfOptionsSupported.indexOf(option.value) !== -1) {
                option.disabled = false;

                if(!selectedItem) {
                    option.selected = true;
                    selectedItem = option;
                }
            }
        });
    }

    recordingMedia.onchange = function() {
        if(this.value === 'record-audio') {
            setMediaContainerFormat(['WAV', 'Ogg']);
            return;
        }
        setMediaContainerFormat(['WebM', 'Mp4','Gif']);
    };

    if(webrtcDetectedBrowser === 'edge') {
        // webp isn't supported in Microsoft Edge
        // neither MediaRecorder API
        // so lets disable both video/screen recording options

        console.warn('Neither MediaRecorder API nor webp is supported in Microsoft Edge. You cam merely record audio.');

        recordingMedia.innerHTML = '<option value="record-audio">Audio</option>';
        setMediaContainerFormat(['WAV']);
    }

    if(webrtcDetectedBrowser === 'firefox') {
        // Firefox implemented both MediaRecorder API as well as WebAudio API
        // Their MediaRecorder implementation supports both audio/video recording in single container format
        // Remember, we can't currently pass bit-rates or frame-rates values over MediaRecorder API (their implementation lakes these features)

        recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                    + '<option value="record-audio-plus-screen">Audio+Screen</option>'
                                    + recordingMedia.innerHTML;
    }

    // disabling this option because currently this demo
    // doesn't supports publishing two blobs.
    // todo: add support of uploading both WAV/WebM to server.
    if(false && webrtcDetectedBrowser === 'chrome') {
        recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
                                    + recordingMedia.innerHTML;
        console.info('This RecordRTC demo merely tries to playback recorded audio/video sync inside the browser. It still generates two separate files (WAV/WebM).');
    }

    function saveToDiskOrOpenNewTab(recordRTC) {
        recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'none';
        recordingDIV.querySelector('#save-to-disk').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            recordRTC.save();
        };

        recordingDIV.querySelector('#open-new-tab').onclick = function() {
            if(!recordRTC) return alert('No recording found.');

            window.open(recordRTC.toURL());
        };

        recordingDIV.querySelector('#upload-to-server').disabled = false;
        recordingDIV.querySelector('#upload-to-server').onclick = function() {
            if(!recordRTC) return alert('No recording found.');
            this.disabled = true;

            var button = this;
            uploadToServer(recordRTC, function(progress, fileURL) {
                if(progress === 'ended') {
                    button.disabled = false;
                    button.innerHTML = 'Descargar del servidor';
                    button.onclick = function() {
                        window.open(fileURL);
                    };
                    return;
                }
                button.innerHTML = progress;
            });

        };
    }

    var listOfFilesUploaded = [];

    function uploadToServer(recordRTC, callback) {
        var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
        var fileType = blob.type.split('/')[0] || 'audio';
        var fileName = (Math.random() * 1000).toString().replace('.', '');

        if (fileType === 'audio') {
            fileName += '.' + (!!navigator.mozGetUserMedia ? 'ogg' : 'wav');
        } else {
            fileName += '.mp4';
        }

        // create FormData
        var formData = new FormData();
        formData.append('id_usuario', '{{Auth::user()->id}}');
        formData.append('id_hc_protocolo', '{{$id}}');
        formData.append('video-filename', fileName);
        formData.append('video-blob', blob);

        callback('Uploading ' + fileType + ' recording to server.');

        makeXMLHttpRequest('{{asset("/")}}save.php', formData, function(progress) {
            if (progress !== 'upload-ended') {
                callback(progress);
                return;
            }

            var initialURL = '{{asset("/")}}uploads/';

            callback('ended', initialURL + fileName);
            // to make sure we can delete as soon as visitor leaves
            listOfFilesUploaded.push(initialURL + fileName);
        });
    }

    function makeXMLHttpRequest(url, data, callback) {
        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                callback('upload-ended');
                anterior = $("#fotos_agregar").html();
                canterior = parseInt($('#cvideo').text());
                canterior = canterior+1;
                $("#cvideo").html(canterior);
                $("#fotos_agregar").html("<div class='col-md-3' style='margin: 10px 0; padding:2px;'><a data-toggle='modal' data-target='#foto' data-remote='{{route('hc_video.mostrar_foto2')}}/"+request.responseText+"'><img src='{{asset("/")}}imagenes/video.png' width='95%'></div>"+anterior);
                console.log(request.responseText);
            }
        };

        request.upload.onloadstart = function() {
            callback('Upload started...');
        };

        request.upload.onprogress = function(event) {
            callback('Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%");
        };

        request.upload.onload = function() {
            callback('progress-about-to-end');
        };

        request.upload.onload = function() {
            callback('progress-ended');
        };

        request.upload.onerror = function(error) {
            callback('Failed to upload to server');
            console.error('XMLHttpRequest failed', error);
        };

        request.upload.onabort = function(error) {
            callback('Upload aborted.');
            console.error('XMLHttpRequest aborted', error);
        };

        request.open('POST', url);
        var metas = document.getElementsByTagName('meta');
        for (i=0; i<metas.length; i++) {
            if (metas[i].getAttribute("name") == "csrf-token") {
                request.setRequestHeader("X-CSRF-Token", metas[i].getAttribute("content"));
            }
        }
        request.send(data);
    }

    window.onbeforeunload = function() {
        recordingDIV.querySelector('button').disabled = false;
        recordingMedia.disabled = false;
        mediaContainerFormat.disabled = false;

        if(!listOfFilesUploaded.length) return;

        listOfFilesUploaded.forEach(function(fileURL) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    if(this.responseText === ' problem deleting files.') {
                        alert('Failed to delete ' + fileURL + ' from the server.');
                        return;
                    }

                    listOfFilesUploaded = [];
                    alert('You can leave now. Your files are removed from the server.');
                }
            };
            request.open('POST', 'delete.php');

            var formData = new FormData();
            formData.append('delete-file', fileURL.split('/').pop());
            request.send(formData);
        });

        return 'Please wait few seconds before your recordings are deleted from the server.';
    };
    var vartiempo = setInterval(function(){ location.reload(); }, 7201000);

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
</section>


@endsection




