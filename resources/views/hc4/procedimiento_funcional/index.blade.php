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


	#style-1::-webkit-scrollbar
	{
		width: 8px;
	}

	#style-1::-webkit-scrollbar-thumb
	{
		border-radius: 10px;

		background-color: #004AC1;
	}

	#style-1::-webkit-scrollbar-track
	{
	   background-color: #004AC1;
	   width: 10px;
	   box-shadow: inset 0px 0px 0px 3px #56ABE3;
	}
	.parent::-webkit-scrollbar-button {
	      background-color: none;
	} /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

	.parent::-webkit-scrollbar-corner {
	      background-color: none;
	} /* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */

	.btn-block{
      background-color: #004AC1;
    }
     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }



    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
        background-color: white !important;
    }

    .btn_agregar_diag{
    	color: white;
    	background-color: green;
    }
</style>
@php
	$ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp

<script src="{{ asset ("/js/dropzone.js") }}"></script>


<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>


<div id="area_index_pf" class="container-fluid" style="padding-left: 5px">
	<div class="row">
		<div class="col-12" style="padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;">
			<div class="col-12" style="border: 2px solid #004AC1; margin-left: 0px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px; background-color: #56ABE3">
				<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1; ">
				  	<div class="row">
					  	<div class="col-md-9 col-sm-8 col-12">
						     <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
				            	<img style="width: 35px; margin-left: 5px; margin-bottom: 5px; margin-right: 5px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
				             	<b>PROCEDIMIENTOS FUNCIONALES</b>
				            </h1>
						</div>
						<div class="col-md-3 col-sm-4 col-12" style="padding-left: 0px">
						    <div style="margin-bottom: 5px;text-align: left; margin-left: 10px">
								<a class="btn btn-primary btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white;" onClick="agregar_procedimiento_funcional();" >
					                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
										<div class="col-2" style="margin-right: 0px; text-align: center; height: 20px; font-size: 10px;padding-left: 0px;padding-right: 0px;" >
											<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
										</div>
						            	<div class="col-9" style="padding-left: 0px;padding-right: 0px;">
						            		<label style="font-size: 10px">AGREGAR PROCEDIMIENTO</label>
						            	</div>
									</div>
								</a>
							</div>
					    </div>
					</div>
					@if(!is_null($paciente))
						<center>
						    <div class="col-12" style="padding-top: 15px">
								<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
						            <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
						            	{{$paciente->nombre1}} {{$paciente->nombre2}}
					                </b>
								</h1>
							</div>
			            </center>
		            @endif
  				</div>

			    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >

				    <div class="parent" id="style-1"  style="margin-left: 0px;   margin-bottom: 10px ; height: 450px " >
				    	<div style=" margin-right: 30px;" >
				    		 <!-- DIV DE LOS PROCEDIMIENTOS -->
				    		 <span id="msn1" style="color: white; "></span>
					   		@foreach ($procedimientos2 as $value)
						   		@php
					  				if($paciente->sexo == 1){
					                    $sexo = "MASCULINO";
					                }else{
					                    $sexo = "FEMENINO";
					                }
					                $seguro =  \Sis_medico\Seguro::find($value->hc_p_id_seguro);
					                $nombre_procedimiento = "";
					                $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
					                $alergias = \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
									if($alergias == "[]"){
					                    $alergia = "No";
					                }else{
					                    $alergia  = "";
					                    foreach ($alergias as $value_alergia) {
					                        if($alergia == ""){
					                            $alergia = $value_alergia->principio_activo->nombre;
					                        }else{
					                            $alergia = $alergia.", ".$value_alergia->principio_activo->nombre;
					                        }

					                    }
					                }

					                $procedimientos = \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
					                //dd($value);
					                if($procedimientos->id_procedimiento != null){
					                    $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
					                }
					                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

				  					$mas = true;
				  					$nombre_procedimiento = "";

					          		foreach($adicionales as $value2)
					          		{
										if($mas == true){
										 	$nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre  ;
										 	$mas = false;
										 }
										else{
									  	 	$nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre  ;
									  	 }
									}
					                $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
					  				$contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
					  				if($contador == 0){
					  					$cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD <br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
						                $input = [
						                    'hc_id_procedimiento' => $value->id_procedimiento,
						                    'hcid' => $value->id_hc,
						                    'secuencia' => 0,
						                    'cuadro_clinico' => $cuadro_clinico,
						                    'motivo' => 'Evolucion Pre-Operatoria',
						                    'fecha_ingreso' => ' ',
						                    'ip_creacion' => $ip_cliente,
						                    'ip_modificacion' => $ip_cliente,
						                    'id_usuariocrea' => $idusuario,
						                    'id_usuariomod' => $idusuario,
						                ];
						                \Sis_medico\Hc_Evolucion::insert($input);
					  				}
					  				$contador2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 1)->orderBy('secuencia', 'DESC')->count();
					  				if($contador2 == 0){
										$cuadro_clinico ="PACIENTE EGRESA ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS FUNCIONALES. PACIENTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. SE DA DE ALTA, SE EXPLICAN LOS RESULTADOS A PACIENTE Y ACOMPAÑANTE.";
						                $input = [
						                    'hc_id_procedimiento' => $value->id_procedimiento,
						                    'hcid' => $value->id_hc,
						                    'motivo' => 'Evolucion Post-Operatoria',
						                    'secuencia' => 1,
						                    'cuadro_clinico' => $cuadro_clinico,
						                    'fecha_ingreso' => ' ',
						                    'ip_creacion' => $ip_cliente,
						                    'ip_modificacion' => $ip_cliente,
						                    'id_usuariocrea' => $idusuario,
						                    'id_usuariomod' => $idusuario,
						                ];
						                \Sis_medico\Hc_Evolucion::insert($input);
					  				}
					  			@endphp
						    	<div class="col-12" style="padding-left: 0px; padding-right: 0px">
						    		<div class="box collapse-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
										<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
											@php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

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
						  					@endphp

						  					@php
	  						                   $hc_seguro = \Sis_medico\Seguro::where('id', $value->hc_p_id_seguro)->OrderBy('created_at', 'desc')->first();

	  						                   //dd($hc_seguro);
	  					                    @endphp
										    <div class="row">
										    	<div class="col-4">
												    @if(!is_null($value->f_operacion))
								                        @php
								                        $dia =  Date('N',strtotime($value->f_operacion));
								                        $mes =  Date('n',strtotime($value->f_operacion)); @endphp
				                                   		<b>
				                                        @if($dia == '1') Lunes
					                                         @elseif($dia == '2') Martes
					                                         @elseif($dia == '3') Miércoles
					                                         @elseif($dia == '4') Jueves
					                                         @elseif($dia == '5') Viernes
					                                         @elseif($dia == '6') Sábado
					                                         @elseif($dia == '7') Domingo
				                                        @endif
				                                         	{{substr($value->f_operacion,8,2)}} de
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
				                                         	del {{substr($value->f_operacion,0,4)}}</b>
				                                    @else
				                                     	@php
								                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
								                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
				                                   		<b>
				                                        @if($dia == '1') Lunes
					                                         @elseif($dia == '2') Martes
					                                         @elseif($dia == '3') Miércoles
					                                         @elseif($dia == '4') Jueves
					                                         @elseif($dia == '5') Viernes
					                                         @elseif($dia == '6') Sábado
					                                         @elseif($dia == '7') Domingo
				                                        @endif
				                                         	{{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
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
				                                         	del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
							                        @endif
						                        </div>
						                        <div class="col-4">
													<div>
														<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
														<span style="font-size: 12px">@if(!is_null($texto)) {{$texto}} @endif</span>
													</div>
												</div>
												<div class="col-3">
													<div>
														<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
														<span style="font-size: 12px">@if(!is_null($value->nombre1)) {{$value->nombre1}} {{$value->apellido1}} @endif</span>
													</div>
												</div>
					                        </div>
										    <div class="pull-right box-tools">
						                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
						                            <i class="fa fa-plus"></i></button>
						                	</div>
										    <!-- /.box-tools -->
										</div>
									  	<div class="box-body" style="background: white;">
									  		<div class="row">
										  		<div class="col-12" style="padding-right: 15px;">
										  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

										  				<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
										  					<div class="row">
											  					<div class="col-3" >
											    					<div class="btn" style="color: white">
													    				<a class="fa fa-pencil-square-o "  onclick="editar_procedimiento_funcional({{$value->id_procedimiento}}, '{{$paciente->id}}');"><span style="font-size: 13px">&nbsp;Editar</span>
													    				</a>
												    				</div>
											    				</div>
											  					<div class="col-6" style="text-align: center">
											  						<span >Detalles del Procedimientos</span>
											  					</div>
											  					
											  					<div class="col-3" >
											    					<div class="btn" style="color: white">
													    				<a class="glyphicon glyphicon-file" onclick="crear_receta_fun('{{$paciente->id}}', {{$value->id_hc}});"><span style="font-size: 13px">&nbsp;Crear Receta</span>
													    				</a>
												    				</div>
											    				</div>
											    				
										  					</div>
										  				</div>
										  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="procedimiento{{$value->id_procedimiento}}">
											  				<div class="row">
											  					<div class="col-12">&nbsp;</div>
											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Procedimiento</span>
											  					</div>
											  					<div class="col-12">
											  						<span>
											  							{{$texto}}
											  						</span>
											  					</div>

	                                                            <div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Seguro</span>
											  					</div>
											  					<div class="col-12">
											  						<span>
											  						 @if(!is_null($hc_seguro))
											  						  {{$hc_seguro->nombre}}
											  						 @endif
	                                                                </span>
											  					</div>
											  					<div class="col-12">&nbsp;</div>
											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Hallazgos</span>
											  					</div>
											  					<div class="col-12">
											  						<span>@if(!is_null($value)) <?php echo strip_tags($value->hallazgos); ?> @endif</span>
											  					</div>
											  					<div class="col-12">&nbsp;</div>
											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Conclusiones</span>
											  					</div>
											  					<div class="col-12">
											  						<span>@if(!is_null($value))  <?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?> @endif</span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">&nbsp;</span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">&nbsp;</span>
											  					</div>
											  					<div class="col-4">
											  						<span>@if(!is_null($value))  Dr. {{$value->nombre1}} {{$value->apellido1}}  @endif</span>
											  					</div>
											  				</div>
											  			</div>
										  			</div>
										  		</div>
										  		<div class="col-12" style="padding-top: 15px">
										  			<div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
										  				<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
										  					<div class="row">
										  						<div class="col-md-3 col-sm-3 col-4">
											  						<a class="btn btn-info btn-block" style="color: white; width: 100%; height: 100%; padding-left: 0px;padding-right: 0px; border: 2px solid white;" onClick="agregar_evolucion({{$value->id_procedimiento}});" >
														                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
															            	<div class="col-12" style="padding-left: 5px;padding-right: 5px;">
															            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
															            		<label style="font-size: 10px; ">AGREGAR EVOLUCION</label>
															            	</div>
																		</div>
																	</a>
											  					</div>
											  					<div class="col-md-6 col-sm-6 col-7" style="padding-left: 5px">
											  						<span> <b>Historial de Evoluciones del Procedimiento</b></span>
											  					</div>
										  					</div>
														    <div class="pull-right box-tools">
										                        <button style="margin-top: 8px"   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
										                            <i class="fa fa-plus"></i></button>
										                    </div>
														    <!-- /.box-tools -->
														</div>
														<div class="box-body" style="background: white;" id="evolucion_agregar{{$value->id_procedimiento}}">
									  						@php
										  						$evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
										  					@endphp
										  					@foreach($evoluciones as $evolucion)
										  					<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
												  				<div class="box-header with-border" style="background-color: #004AC1; color: white;  font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
												  					<div class="row">
												  						<div class="col-2" style="margin-right: 10px" >
													    					<div class="btn" style="color: white">
															    				<a class="fa fa-pencil-square-o "  onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');" ><span style="font-size: 13px">&nbsp;Editar</span>
															    				</a>
														    				</div>
													    				</div>
													    				<div class="col-9" style="text-align: center;">
													  						<span style="padding-top: 5px;">Detalles de la Evolucion</span>
													  					</div>
												  					</div>
												  				</div>
												  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
													  				<div class="row">
													  					<div class="col-12">
													  						<span style="font-family: 'Helvetica general';">Motivo</span>
													  					</div>
													  					<div class="col-12">
													  						<span>{{$evolucion->motivo}}</span>
													  					</div>
													  					<div class="col-12">&nbsp;</div>
													  					<div class="col-12">
													  						<span style="font-family: 'Helvetica general';">Detalle</span>
													  					</div>
													  					<div class="col-12">
													  						<span><?php echo $evolucion->cuadro_clinico; ?></span>
													  					</div>
													  				</div>
													  			</div>
									  						</div>
									  						<div class="col-md-12" style="height: 5px;"></div>
										  					@endforeach
										  				</div>
													</div>
										  		</div>

	        									<div class="col-12" style="height: 5px;"></div>
										  		<div class="col-12">
										  			<div class="box box-primary collapse-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
										  				<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
										  					<span> <b>Diagnostico CIE10</b></span>
														    <div class="pull-right box-tools">
										                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
										                            <i class="fa fa-plus"></i></button>
										                    </div>
														    <!-- /.box-tools -->
														</div>
														<div class="box-body" style="background: white;" >
									  						<form id="frm_cie{{$value->id_procedimiento}}">
										  						<div class="col-12">
										  							<div class="row">

											                            <input type="hidden" name="codigo" id="codigo{{$value->id_procedimiento}}">
											                            <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px" ><b>Diagnóstico</b></label>

											                            <div class="form-group col-md-6 col-sm-12 " style="padding-left: 0px">
											                                <input id="cie10{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" >
											                            </div>
											                            <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
											                                <select id="pre_def{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
											                                    <option value="">Seleccione ...</option>
											                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
											                                    <option value="DEFINITIVO">DEFINITIVO</option>
											                                </select>
											                            </div>
											                            <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
											                                <select id="ing_egr{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
											                                    <option value="">Seleccione ...</option>
											                                    <option value="INGRESO">INGRESO</option>
											                                    <option value="EGRESO">EGRESO</option>
											                                </select>
											                            </div>
											                            <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
											                                <button id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
											                            </div>


											                            <div class="form-group col-md-12" >
											                                <table id="tdiagnostico{{$value->id_procedimiento}}" class="table table-striped" style="font-size: 12px;">

											                                </table>
											                            </div>
																	</div>
																</div>
									                        </form>
										  				</div>
													</div>
										  		</div>
											  	<div class="col-12" >
										            <div class="box box-primary collapse-box" style="border: 2px solid #004AC1; border-radius: 3px;">
										            	<center>
										                <div class="box-header " style="margin-top: 5px; margin-bottom: 5px">
										                    <div class="col-12">
										                        <span>GUARDADO DE DOCUMENTOS &amp; ANEXOS</span>
										                    </div>
										                    <div class="pull-right box-tools">
							                        			<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
								                            		<i class="fa fa-plus"></i>
							                            		</button>
							                				</div>
										                </div>
										                </center>
										                <div class="box-body">
										                <div class=" col-12">
	                                        				<table class="table table-bordered  dataTable" >
					                                            <tbody style="font-size: 12px; ">
						                                            @if(!is_null($value))
						                                                @php
						                                                $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '2')->get();
						                                                @endphp
						                                            @endif
					                                                <div class="row" id="fila{{$value->id}}">
						                                                @if(!is_null($documentos))
						                                                @foreach($documentos as $imagen)
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
						                                                @endforeach
						                                                @endif
					                                                </div>
					                                            </tbody>
	                                        				</table>
	                                    				</div>
										                    @if(!is_null($value))
												                @php
										                            $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
										                            $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
										                            $cant_total = $cant_anexo + $cant_estud;
										                        @endphp
									                        @endif
										                    <div class="row">
										                        <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
										                            <div class="row" style="text-align: center;">
											                            <div class="col-sm-6 col-12"><b>Ingreso de Documentos del Procedimiento</b></div>
											                            <div class="col-sm-6 col-12" style="">
											                            	@if($cant_total > 0)
				                                                    		<span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: @if(!is_null($cant_anexo)) {{$cant_anexo}} @endif ANEXO(S) </b></span>
				                                                			@else
				                                               		 		@endif
				                                                		</div>
			                                                		</div>
										                            <div class="col-12">
										                                <form method="POST" action="{{route('hc4.documento_guardar')}}" enctype="multipart/form-data" class="dropzone" id="upload_{{$value->id_protocolo}}" >
										                                    <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
										                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
										                                    <div class="fallback" >

										                                    </div>
										                                </form>
										                            </div>
										                        </div>
										                    </div>
										                </div>
										                <script type="text/javascript">
															$("#upload_{{$value->id_protocolo}}").dropzone({
																url: "{{route('hc4.documento_guardar')}}",
																successmultiple: function(file, response){
																	agregar_imagen(response, 'fila{{$value->id}}');
															        this.removeAllFiles();
															    },
															    error: function(file, response) {
														                  alert(response);
														       }
															});
														</script>
										            </div>
		        								</div>
		        								<!--estudios-->
		        								<div class="col-12" >
										            <div class="box box-primary collapse-box" style="border: 2px solid #004AC1; border-radius: 3px;">
										            	<center>
										                <div class="box-header " style="margin-top: 5px; margin-bottom: 5px" >
										                    <div class="col-12">
										                        <span>GUARDADO DE ESTUDIOS</span>
										                    </div>
										                    <div class="pull-right box-tools">
							                        			<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
								                            		<i class="fa fa-plus"></i>
							                            		</button>
							                				</div>
										                </div>
										                </center>
										                <div class="box-body">
											                <div class=" col-12">
		                                        				<table class="table table-bordered  dataTable" >
						                                            <tbody style="font-size: 12px; ">
						                                            	@if(!is_null($value))
						                                                @php
						                                                $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '3')->get();
						                                                @endphp
						                                                @endif
						                                                <div class="row" id="fila2{{$value->id}}">
						                                                @if(!is_null($documentos))
						                                                @foreach($documentos as $imagen)
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
								                                                        		<div class="color-12">
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
								                                                    		<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
								                                                        		<div class="col-12">
								                                                        			<span class="glyphicon glyphicon-download-alt"> Descargar</span>
								                                                        		</div>
								                                                    		</a>
								                                                    	</div>
								                                                    </div>

								                                                @endif
							                                                </div>
						                                                @endforeach
						                                                @endif
						                                                </div>
						                                            </tbody>
		                                        				</table>
		                                    				</div>
											                @php
									                            $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
									                            $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
									                            $cant_total = $cant_anexo + $cant_estud;
									                        @endphp
										                    <div class="row">
										                        <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
										                            <div class="row" style="text-align: center;">
											                            <div class="col-sm-6 col-12"><b>Ingreso de Estudios del Procedimiento</b></div>
											                            <div class="col-sm-6 col-12" >
											                            	@if($cant_total > 0)
				                                                    		<span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_estud}} ESTUDIO(S)</b></span>
				                                                			@else
				                                               		 		@endif
				                                                		</div>
			                                                		</div>
										                            <div class="col-12">
										                                <form method="POST" action="{{route('hc4.estudio_guardar')}}" enctype="multipart/form-data" class="dropzone" id="upload2_{{$value->id_protocolo}}" >
										                                    <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
										                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
										                                    <div class="fallback" >

										                                    </div>
										                                </form>
										                            </div>
										                        </div>
										                    </div>
										                </div>
										                <script type="text/javascript">
															$("#upload2_{{$value->id_protocolo}}").dropzone({
																url: "{{route('hc4.estudio_guardar')}}",
																successmultiple: function(file, response){
																	agregar_imagen(response, 'fila2{{$value->id}}');
																	//console.log(response);
															        this.removeAllFiles();
															    },
															    error: function(file, response) {
														                  alert(response);
														       }
															});
														</script>
										            </div>
	        									</div>
	        								</div>
									  	</div>
									</div>
						    	</div>
						    @endforeach

				  			@foreach ($procedimientos1 as $value)
				  				@php
					  				if($paciente->sexo == 1){
					                    $sexo = "MASCULINO";
					                }else{
					                    $sexo = "FEMENINO";
					                }
					                $seguro =  \Sis_medico\Seguro::find($value->hc_p_id_seguro);
					                $nombre_procedimiento = "";
					                $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
					                $alergias = \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
									if($alergias == "[]"){
					                    $alergia = "No";
					                }else{
					                    $alergia  = "";
					                    foreach ($alergias as $value_alergia) {
					                        if($alergia == ""){
					                            $alergia = $value_alergia->principio_activo->nombre;
					                        }else{
					                            $alergia = $alergia.", ".$value_alergia->principio_activo->nombre;
					                        }

					                    }
					                }

					                $procedimientos = \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
					                //dd($value);
					                if($procedimientos->id_procedimiento != null){
					                    $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
					                }
					                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

				  					$mas = true;
				  					$nombre_procedimiento = "";

					          		foreach($adicionales as $value2)
					          		{
										if($mas == true){
										 	$nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre  ;
										 	$mas = false;
										 }
										else{
									  	 	$nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre  ;
									  	 }
									}
					                $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
					  				$contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
					  				if($contador == 0){
					  					$cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD <br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
						                $input = [
						                    'hc_id_procedimiento' => $value->id_procedimiento,
						                    'hcid' => $value->id_hc,
						                    'secuencia' => 0,
						                    'cuadro_clinico' => $cuadro_clinico,
						                    'motivo' => 'Evolucion Pre-Operatoria',
						                    'fecha_ingreso' => ' ',
						                    'ip_creacion' => $ip_cliente,
						                    'ip_modificacion' => $ip_cliente,
						                    'id_usuariocrea' => $idusuario,
						                    'id_usuariomod' => $idusuario,
						                ];
						                \Sis_medico\Hc_Evolucion::insert($input);
					  				}
					  				$contador2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 1)->orderBy('secuencia', 'DESC')->count();
					  				if($contador2 == 0){
										$cuadro_clinico ="PACIENTE EGRESA ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS FUNCIONALES. PACIENTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. SE DA DE ALTA, SE EXPLICAN LOS RESULTADOS A PACIENTE Y ACOMPAÑANTE.";
						                $input = [
						                    'hc_id_procedimiento' => $value->id_procedimiento,
						                    'hcid' => $value->id_hc,
						                    'motivo' => 'Evolucion Post-Operatoria',
						                    'secuencia' => 1,
						                    'cuadro_clinico' => $cuadro_clinico,
						                    'fecha_ingreso' => ' ',
						                    'ip_creacion' => $ip_cliente,
						                    'ip_modificacion' => $ip_cliente,
						                    'id_usuariocrea' => $idusuario,
						                    'id_usuariomod' => $idusuario,
						                ];
						                \Sis_medico\Hc_Evolucion::insert($input);
					  				}
					  			@endphp
						    	<div class="col-12" style="padding-left: 0px; padding-right: 0px">
						    		<div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
										<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
											<div class="row">
												<div class="col-4">
												    @if(!is_null($value->f_operacion))
								                        @php
								                        $dia =  Date('N',strtotime($value->f_operacion));
								                        $mes =  Date('n',strtotime($value->f_operacion)); @endphp
				                                   		<b>
				                                    	@if($dia == '1') Lunes
					                                         @elseif($dia == '2') Martes
					                                         @elseif($dia == '3') Miércoles
					                                         @elseif($dia == '4') Jueves
					                                         @elseif($dia == '5') Viernes
					                                         @elseif($dia == '6') Sábado
					                                         @elseif($dia == '7') Domingo
				                                        @endif
				                                         	{{substr($value->f_operacion,8,2)}} de
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
				                                         	del {{substr($value->f_operacion,0,4)}}</b>
				                                    @else
				                                     	@php
								                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
								                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
				                                   		<b>
				                                        @if($dia == '1') Lunes
					                                         @elseif($dia == '2') Martes
					                                         @elseif($dia == '3') Miércoles
					                                         @elseif($dia == '4') Jueves
					                                         @elseif($dia == '5') Viernes
					                                         @elseif($dia == '6') Sábado
					                                         @elseif($dia == '7') Domingo
				                                        @endif
				                                         	{{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
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
				                                         	del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
							                        @endif
						                        </div>
						                        <div class="col-4">
													<div>
														<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
														<span style="font-size: 12px"> {{$value->nombre}} </span>
													</div>
												</div>
												<div class="col-3">
													<div>
														<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
														<span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
													</div>
												</div>
					                        </div>
										    <div class="pull-right box-tools">
						                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
						                            <i class="fa fa-plus"></i></button>
						                	</div>
										    <!-- /.box-tools -->
										</div>
									  	<div class="box-body" style="background: white;">
									  		<div class="row">
										  		<div class="col-12" style="padding-right: 15px;">
										  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

										  				<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
										  					<div class="row">
											  					<div class="col-3" >
											    					<div class="btn" style="color: white">
													    				<a class="fa fa-pencil-square-o "  onclick="editar_procedimiento_funcional({{$value->id_procedimiento}}, '{{$paciente->id}}');" ><span style="font-size: 13px">&nbsp;Editar</span>
													    				</a>
												    				</div>
											    				</div>
											    				<div class="col-8" style="text-align: center">
											  					  <span style="padding-top: 8px">Detalles del Procedimientos</span>
											  				    </div>
										  					</div>
										  				</div>
										  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="procedimiento{{$value->id_procedimiento}}">
											  				<div class="row">
											  					<div class="col-12">&nbsp;</div>
											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Procedimiento</span>
											  					</div>

											  					<div class="col-12">
											  						<span>
											  							{{$value->nombre}}
											  						</span>
											  					</div>
											  					<div class="col-12">&nbsp;</div>
											  					@php
						  						                   $hc_seguro = \Sis_medico\Seguro::where('id', $value->hc_p_id_seguro)->first();
						  					                    @endphp
						  					                    <div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Seguro</span>
											  					</div>
						  					                    <div class="col-12">
						  					                    	<span>
						  					                    		@if(!is_null($hc_seguro))
						  					                    		  {{$hc_seguro->nombre}}
						  					                    		@endif
						  					                    	</span>
						  						                </div>

											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Hallazgos</span>
											  					</div>
											  					<div class="col-12">
											  						<span><?php echo strip_tags($value->hallazgos); ?></span>
											  					</div>
											  					<div class="col-12">&nbsp;</div>
											  					<div class="col-12">
											  						<span style="font-family: 'Helvetica general';">Conclusiones</span>
											  					</div>
											  					<div class="col-12">
											  						<span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">&nbsp;</span>
											  					</div>
											  					<div class="col-4">
											  						<span style="font-family: 'Helvetica general';">&nbsp;</span>
											  					</div>
											  					<div class="col-4">
											  						<span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
											  					</div>
											  				</div>
											  			</div>
										  			</div>
										  		</div>

										  		<div class="col-12" style="padding-top: 15px">
										  			<div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
										  				<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
										  					<div class="row">
										  						<div class="col-md-3 col-sm-3 col-4">
											  						<a class="btn btn-info btn-block" style="color: white; width: 100%; height: 100%; padding-left: 0px;padding-right: 0px; border: 2px solid white;" onClick="agregar_evolucion({{$value->id_procedimiento}});" >
														                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
															            	<div class="col-12" style="padding-left: 5px;padding-right: 5px;">
															            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
															            		<label style="font-size: 10px; ">AGREGAR EVOLUCION</label>
															            	</div>
																		</div>
																	</a>
											  					</div>
											  					<div class="col-md-6 col-sm-6 col-7" style="padding-left: 5px">
											  						<span> <b>Historial de Evoluciones del Procedimiento</b></span>
											  					</div>
										  					</div>
														    <div class="pull-right box-tools">
										                        <button style="margin-top: 8px"   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
										                            <i class="fa fa-plus"></i></button>
										                    </div>
														    <!-- /.box-tools -->
														</div>
														<div class="box-body" style="background: white;" id="evolucion_agregar{{$value->id_procedimiento}}">
									  						@php
										  						$evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
										  					@endphp
										  					@foreach($evoluciones as $evolucion)
										  					<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
												  				<div class="box-header with-border" style="background-color: #004AC1; color: white;  font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
												  					<div class="row">
												  						<div class="col-2" style="margin-right: 10px" >
													    					<div class="btn" style="color: white">
															    				<a class="fa fa-pencil-square-o "  onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');" ><span style="font-size: 13px">&nbsp;Editar</span>
															    				</a>
														    				</div>
													    				</div>
													    				<div class="col-9" style="text-align: center;">
													  						<span style="padding-top: 5px;">Detalles de la Evolucion</span>
													  					</div>
												  					</div>
												  				</div>
												  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
													  				<div class="row">
													  					<div class="col-12">
													  						<span style="font-family: 'Helvetica general';">Motivo</span>
													  					</div>
													  					<div class="col-12">
													  						<span>{{$evolucion->motivo}}</span>
													  					</div>
													  					<div class="col-12">&nbsp;</div>
													  					<div class="col-12">
													  						<span style="font-family: 'Helvetica general';">Detalle</span>
													  					</div>
													  					<div class="col-12">
													  						<span><?php echo $evolucion->cuadro_clinico; ?></span>
													  					</div>
													  				</div>
													  			</div>
									  						</div>
									  						<div class="col-md-12" style="height: 5px;"></div>
										  					@endforeach
										  				</div>
													</div>
										  		</div>

											  	<div class="col-12" style="padding-right: 0px; padding-left: 0px; margin-top: 10px; border: 2px solid #004AC1; padding-left: 5px; padding-right: 5px">
										            <div class="box" style="border-top-width: 0px; margin-bottom: 0px;">
										            	<center>
										                <div class="box-header " style="margin-top: 5px; margin-bottom: 5px">
										                    <div class="col-12">
										                        <span>GUARDADO DE DOCUMENTOS &amp; ANEXOS</span>
										                    </div>
										                    <div class="pull-right box-tools">
							                        			<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
								                            		<i class="fa fa-plus"></i>
							                            		</button>
							                				</div>
										                </div>
										                </center>
										                <div class="box-body">
										                <div class=" col-12">
	                                        				<table class="table table-bordered  dataTable" >
					                                            <tbody style="font-size: 12px; ">
					                                                @php

					                                                $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '2')->get();

					                                                @endphp
					                                                <div class="row">
					                                                @foreach($documentos as $imagen)
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
								                                                    	<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
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
					                                                @endforeach
					                                                </div>
					                                            </tbody>
	                                        				</table>
	                                    				</div>
										                @php
								                            $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
								                            $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
								                            $cant_total = $cant_anexo + $cant_estud;
								                        @endphp

									                        <div class="row">
								                        <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
								                        	<div class="row" style="text-align: center;">
									                            <div class="col-sm-6 col-12"><b>Ingreso de Imagenes del Procedimiento</b></div>
									                            <div class="col-sm-6 col-12" >
									                            	@if($cant_total > 0)
		                                                    		<span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_anexo}} ANEXO(S) </b></span>
		                                                			@else
		                                               		 		@endif
		                                                		</div>
	                                                		</div>
								                            <div class="col-12">
								                                <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="upload_{{$value->id_protocolo}}">
							                                        <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
							                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
							                                        <div class="fallback">
							                                        </div>
								                                </form>
								                            </div>


										                <script type="text/javascript">
															$("#upload_{{$value->id_protocolo}}").dropzone({url: "{{route('hc_video.guardado_foto2_documento')}}"});

														</script>
								                        </div>
								                    </div>
										                </div>
										            </div>
			        							</div>
		        							<!--estudios-->
		        								<div class="col-12" style="padding-right: 0px; padding-left: 0px; margin-top: 10px; border: 2px solid #004AC1; padding-left: 5px; padding-right: 5px">
										            <div class="box" style="border-top-width: 0px; margin-bottom: 0px;">
										            	<center>
										                <div class="box-header " style="margin-top: 5px; margin-bottom: 5px" >
										                    <div class="col-12">
										                        <span>GUARDADO DE ESTUDIOS</span>
										                    </div>
										                    <div class="pull-right box-tools">
							                        			<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
								                            		<i class="fa fa-plus"></i>
							                            		</button>
							                				</div>
										                </div>
										                </center>
										                <div class="box-body">
										                <div class=" col-12">
	                                        				<table class="table table-bordered  dataTable" >
					                                            <tbody style="font-size: 12px; ">
					                                                @php

					                                                $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '3')->get();

					                                                @endphp
					                                                <div class="row">
					                                                @foreach($documentos as $imagen)
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
								                                                    	<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
								                                                        <div>
								                                                        	<span class="glyphicon glyphicon-download-alt"> Descargar</span>
								                                                        </div>
								                                                    	</a>
								                                                    </div>
								                                                </div>
							                                                @elseif(($extension == 'pdf'))
								                                                <div class="row">
								                                                	<div class="col-12">
								                                                    	<a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
								                                                        	<div>
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
							                                                    	<div class="col-md-12">
							                                                    		<a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
							                                                        		<div class="col-12">
							                                                        			<span class="glyphicon glyphicon-download-alt"> Descargar</span>
							                                                        		</div>
							                                                    		</a>
							                                                    	</div>
							                                                    </div>
							                                                @endif
						                                                </div>
					                                                @endforeach
					                                                </div>
					                                            </tbody>
	                                        				</table>
	                                    				</div>

											                @php
									                            $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
									                            $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
									                            $cant_total = $cant_anexo + $cant_estud;
									                        @endphp
										                    <div class="row">
										                        <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
										                            <div class="row" style="text-align: center;">
											                            <div class="col-sm-6 col-12"><b>Ingreso de Estudios del Procedimiento</b></div>
											                            <div class="col-sm-6 col-12" >
											                            	@if($cant_total > 0)
				                                                    		<span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_estud}} ESTUDIO(S)</b></span>
				                                                			@else
				                                               		 		@endif
				                                                		</div>
			                                                		</div>
										                            <div class="col-12">
										                                <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="upload2_{{$value->id_protocolo}}" >
										                                    <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
										                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
										                                    <div class="fallback" >

										                                    </div>
										                                </form>
										                            </div>
										                        </div>
										                    </div>
										                </div>
										                <script type="text/javascript">
															$("#upload2_{{$value->id_protocolo}}").dropzone({
																url: "{{route('hc_video.guardado_foto2_estudios')}}",
																success: function(file, response){
															        alert("entra, 22");
															        console.log('entra');
															    },
															    error: function(file, response) {
														                  alert(response);
														       }
															});

														</script>
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
		</div>
	</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
	function editar_evolucion(id, id_paciente){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('editar.procedimiento_evolucion')}}/"+id+'/'+id_paciente,
			data: "",
			datatype: "html",
			success: function(datahtml, entra){
				$("#evolucion"+id).html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}
	function agregar_evolucion(id, id_evolucion){
    	var entra = id;
		$.ajax({
			type: "GET",
			url: "{{route('agregar.procedimiento_evolucion')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml, entra){
				anterior  = $("#evolucion_agregar"+id).html();
				$("#evolucion_agregar"+id).html(datahtml+anterior);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}
	Dropzone.options.addimage = {
      acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif, .rtf",
      init: function() {
        this.on("error", function(file, response) {
            alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
            console.log(response);
        });
        this.on("success", function(file, response) {
            console.log(response);
        });
      }
    };

	function editar_procedimiento_funcional(id, id_paciente){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('editar.procedimiento_funcional')}}/"+id+'/'+id_paciente,
			data: "",
			datatype: "html",
			success: function(datahtml, entra){
				$("#procedimiento"+id).html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}

	function agregar_procedimiento_funcional(){
    	//alert("agregar");
    	contador = parseInt($('#contador_funcionales').val());
    	hc_endoscopico = parseInt($('#id_endoscopico').val());
    	hc_funcional = parseInt($('#id_funcional').val());
    	if(contador>0){
	    	agregar_procedimiento_hc('funcional_id');
        }else if (hc_funcional >0) {
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/1/{{$paciente->id}}/"+hc_funcional;
        	agregar_procedimiento_tipo(url_datos);
        }else if (hc_endoscopico >0) {
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/1/{{$paciente->id}}/"+hc_endoscopico;
        	agregar_procedimiento_tipo(url_datos);
        	//console.log(url_datos);
        }else{
	    	$.ajax({
				async: true,
				type: "GET",
				url: "{{route('proc_fun.selecciona_procedimiento',['tipo' => '1', 'paciente' => $paciente->id ])}}",
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
    }

    function agregar_imagen(array, div){
		var div_anterior =$("#"+div).html();
		for (i = 0; i < array.length; i++) {
		  console.log(array[i]);
		  $.ajax({
				async: true,
				type: "GET",
				url: "{{route('hc_4.mostar_div')}}/"+array[i],
				data: "",
				datatype: "html",
				success: function(datahtml){
					//alert(datahtml);
					//console.log(datahtml);
					$("#"+div).html(div_anterior+datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
		}
    }
    function cargar_tabla(id){
        $.ajax({
                url:"{{route('epicrisis.cargar22')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){

                    var table = document.getElementById("tdiagnostico"+id);

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.descripcion;
                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = value.ingreso_egreso;
                        var cell5 = row.insertCell(4);
                        cell5.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });

                }
            })
    }

    function eliminar(id_h, id){
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico"+id).deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });
    }
    @foreach ($procedimientos2 as $value)
		cargar_tabla({{$value->id_procedimiento}});
    	$("#cie10{{$value->id_procedimiento}}").autocomplete({
	        source: function( request, response ) {

	            $.ajax({
	                url:"{{route('epicrisis.cie10_nombre')}}",
	                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	                data: {
	                    term: request.term
	                      },
	                dataType: "json",
	                type: 'post',
	                success: function(data){
	                    response(data);

	                }
	            })
	        },
	        minLength: 2,
	    } );

	    $("#cie10{{$value->id_procedimiento}}").change( function(){
	        $.ajax({
	            type: 'post',
	            url:"{{route('epicrisis.cie10_nombre2')}}",
	            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	            datatype: 'json',
	            data: $("#cie10{{$value->id_procedimiento}}"),
	            success: function(data){
	                if(data!='0'){
	                    $('#codigo{{$value->id_procedimiento}}').val(data.id);
	                }

	            },
	            error: function(data){

	                }
	        })
	    });
	    $('#bagregar{{$value->id_procedimiento}}').click( function(){
	        if($('#pre_def{{$value->id_procedimiento}}').val()!='' ){
	            if($('#ing_egr{{$value->id_procedimiento}}').val()!='' ){
	                guardar_cie10_PRO({{$value->id_procedimiento}}, {{$value->id_hc}});
	                $('#ing_egr{{$value->id_procedimiento}}').val('');
	            }else{
	                alert("Seleccione Ingreso o Egreso");
	            }
	        }else{
	            alert("Seleccione Presuntivo o Definitivo");
	        }
	        $('#codigo{{$value->id_procedimiento}}').val('');
	        $('#cie10{{$value->id_procedimiento}}').val('');
	    });
	@endforeach
	function guardar_cie10_PRO(id_procedimiento, id_hc){
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_procedimiento).val(), 'pre_def': $("#pre_def"+id_procedimiento).val(),'hcid': id_hc, 'hc_id_procedimiento': id_procedimiento, 'in_eg': $("#ing_egr"+id_procedimiento).val(), 'id_paciente': '{{$paciente->id}}'  },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+id_procedimiento);
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = data.in_eg;
                var cell5 = row.insertCell(4);
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

            },
            error: function(data){

                }
        })
    } 
    function crear_receta_fun(id_paciente, id_hc){
		$.ajax({
			type: "GET",
			url:"{{url('inicio/nueva/recetas/funcional')}}/"+id_paciente+"/"+id_hc,
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



