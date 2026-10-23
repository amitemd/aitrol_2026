
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

    

    .btn_agregar_diag{
    	color: white;
    	background-color: green;
    }
    .alerta_correcto{
		position: absolute;
		z-index: 9999;
		top: 100px;
		right: 10px;
	}
</style>
@php
	$ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp
<script type="text/javascript">
    function editarprocedimiento(id, id_paciente){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('quirofano.editar_ecografia')}}/"+id+'/'+id_paciente,
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
</script>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>
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
<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>


<div class="card">
	<div class="card-header bg bg-primary colorbasic">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <!--label class="colorbasic sradio" > 3</label--> 
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px" ><b>Imagenes</b>  </label> <br>
                    <label style="font-size: 16px;"><b>Paciente: {{$solicitud->paciente->apellido1}} {{$solicitud->paciente->apellido2}} {{$solicitud->paciente->nombre1}} {{$solicitud->paciente->nombre2}}</b></label>
                </div>
                <div class="col-md-2">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">
    	
		  		@php
	                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

	                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
	            @endphp

		  		@foreach($procedimientos2 as $value)
		  			@php
		  				if($paciente->sexo == 1){
		                    $sexo = "MASCULINO";
		                }else{
		                    $sexo = "FEMENINO";
		                }
		                $seguro =  \Sis_medico\Seguro::find($value->seguro_final);
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
			                $input = [
			                    'hc_id_procedimiento' => $value->id_procedimiento,
			                    'hcid' => $value->id_hc,
			                    'motivo' => 'Evolucion Post-Operatoria',
			                    'secuencia' => 1,
			                    'cuadro_clinico' => ' ',
			                    'fecha_ingreso' => ' ',
			                    'ip_creacion' => $ip_cliente,
			                    'ip_modificacion' => $ip_cliente,
			                    'id_usuariocrea' => $idusuario,
			                    'id_usuariomod' => $idusuario,
			                ];
			                \Sis_medico\Hc_Evolucion::insert($input);
		  				}
		  				//dd($value);
		  			@endphp
			    	<div class="col-md-12" style="padding: 0;">
			    		@php
				    		if(!is_null($value->f_operacion)){
					    		$fecha_r =  Date('Y-m-d',strtotime($value->f_operacion));
					    	}else{
						    	$fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
						    }
			    		@endphp
			    		
			    			<br>
							<div class="card-header bg bg-primary colorbasic">

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
								
									<div class="col-4">
										@if(!is_null($value->f_operacion))
					                        @php
						                        $dia =  Date('N',strtotime($value->f_operacion));
						                        $mes =  Date('n',strtotime($value->f_operacion)); 
						                        $aux = intval($dia)-1;
		                                        $ms  = intval($mes)-1;
					                        @endphp

					                   		<b>{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</b>

					                    @else
					                     	@php
						                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
						                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
						                        $aux = intval($dia)-1;
		                                        $ms  = intval($mes)-1; 
					                        @endphp

					                   		<b> {{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>

					                    @endif
				                    </div>
									<div class="col-4">
										<div>
											<span style="font-size: 12px">Procedimiento:</span>
											<span style="font-size: 12px">@if(!is_null($texto)) {{$texto}} @endif </span>
										</div>
									</div>
									<div class="col-3">
										<div>
											<span style=" font-size: 12px">Dr (a) </span>
											<span style="font-size: 12px">@if(!is_null($value->nombre1)) {{$value->nombre1}} {{$value->apellido1}} @endif</span>
										</div>
									</div>
				               					               				               	
							    <!-- /.card-tools -->
							</div>
							<br>
							<div class="card-body" style="padding: 0px;">
							  	<div class="row">
							  		<div class="col-md-8 col-sm-12 col-12">
							  			<div class="card" >

							  				<div class="card-header bg bg-primary colorbasic">
							  					<div class="row">
								  					<div class="col-3" style="margin-right: 10px">
									    				<a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');" ><span class="fa fa-pencil-square-o"></span>
									    				</a>
								    				</div>
								    				<div class="col-8" style="text-align: center">
								  						<span >Detalles del Procedimientos</span>
								  					</div>
							  					</div>
							  				</div>
							  				<div class="card-body" style="font-size: 11px;" id="procedimiento{{$value->id_procedimiento}}" >
								  				<div class="row">
								  					<div class="col-12">&nbsp;</div>
								  					<div class="col-12">
								  						<span style="">Procedimiento</span>
								  					</div>

								  					<div class="col-12">
								  						<span>
								  							{{$texto}}
								  						</span>
								  					</div>

								  					<div class="col-12" style="padding-top: 10px">
								  						<span style="">Seguro</span>
								  					</div>
								  					@php
								  						$hc_seguro = \Sis_medico\Seguro::where('id', $value->seguro_final)->first();
								  						//dd($hc_seguro);
								  					@endphp
								  					<div class="col-12" style="padding-bottom: 10px"><span> @if(!is_null($hc_seguro)) {{$hc_seguro->nombre}} @endif</span>

								  					</div>

								  					<div class="col-12">&nbsp;</div>
								  					<div class="col-12">
								  						<span style="">Hallazgos</span>
								  					</div>
								  					<div class="col-12">
								  						<span><?php echo strip_tags($value->hallazgos); ?></span>
								  					</div>
								  					<div class="col-12">&nbsp;</div>
								  					<div class="col-12">
								  						<span style="">Conclusiones</span>
								  					</div>
								  					<div class="col-12">
								  						<span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
								  					</div>
													  <div class="col-12">&nbsp;</div>
								  					<div class="col-4">
								  						<span style="">M&eacute;dico Examinador</span>
								  					</div>
								  					<div class="col-4">
								  						<span style="">&nbsp;</span>
								  					</div>
								  					<div class="col-4">
								  						<span style="">&nbsp;</span>
								  					</div>
								  					<div class="col-4">
								  						<span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
								  					</div>
								  				</div>
								  			</div>
							  			</div>
							  		</div>
							  		<div class="col-md-4 col-sm-12 col-12" style="padding-left: 5px; padding-right: 10px;">
							  			<div class="card" >
							  				<div class="card-header bg bg-primary colorbasic">
							  					<div class="row">
							  						<div class="col-3" style="margin-right: 10px">
							  							
							    						<a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button" onclick="editar_imagenes({{$value->id_protocolo}})" ><span class="fa fa-pencil-square-o"></span>
							    						</a>
							    					
				                                 	</div>
				                                 	<div class="col-8" style="text-align: center">
							  							<span>Imagenes Procedimientos</span>
							  						</div>
							  					</div>
							  				</div>
							  				<div class="card-body">
								  				<div class="row">
								  					@php
								  						$id_protocolo = $value->id_protocolo;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->limit(8)->OrderBy('created_at', 'desc')->get()
								  					@endphp
								  					@foreach($imagenes as $imagen)
								  					<div class="col-md-6 col-sm-4 ">
			                                            @php
			                                                $explotar = explode( '.', $imagen->nombre);
			                                                $extension = end($explotar);
			                                            @endphp
			                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
			                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
			                                                </a><br>
			                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
			                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
			                                                </a>
			                                            @elseif(($extension == 'mp4'))
			                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
			                                                </a>
			                                            @else
			                                                @php
			                                                    $variable = explode('/' , asset('/hc_ima/'));
			                                                    $d1 = $variable[3];
			                                                    $d2 = $variable[4];
			                                                    $d3 = $variable[5];
			                                                 @endphp
			                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/office.png')}}" width="98%">
			                                                </a>
			                                            @endif
								  					</div>
								  					@endforeach
								  				</div>
								  			</div>
							  			</div>
							  		</div>
							  		<div class="col-12" style="height: 5px;"></div>
							  		<div class="col-12" >
							  			<div class="card" >
							  				<div class="card-header bg bg-primary colorbasic">
							  					
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
							  					
											    
											    <!-- /.card-tools -->
											</div>
											<div class="card-body" id="evolucion_agregar{{$value->id_procedimiento}}" style="padding: 0px;">
												<br>
						  						@php
							  						$evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
							  					@endphp
							  					@foreach($evoluciones as $evolucion)
							  					<div class="card" >
									  				<div class="card-header bg bg-primary colorbasic">
									  					
									  						<div class="col-2" style="margin-right: 10px" >
										    					
											    				<a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editar_evolucion({{$evolucion->id}}, '{{$solicitud->paciente->id}}');"><span class="fa fa-pencil-square-o"></span>
                                                                </a>
										    				</div>
										    				<div class="col-9" style="text-align: center;">
										  						<span style="padding-top: 5px;">Detalles de la Evolucion</span>
										  					</div>
									  					
									  				</div>
									  				<div class="card-body" style="font-size: 11px;" id="evolucion{{$evolucion->id}}">
									  					<br>
										  				<div class="row">
										  					<div class="col-12">
										  						<span style="">Motivo</span>
										  					</div>
										  					<div class="col-12">
										  						<span>{{$evolucion->motivo}}</span>
										  					</div>
										  					<div class="col-12">&nbsp;</div>
										  					<div class="col-12">
										  						<span style="">Detalle</span>
										  					</div>
										  					<div class="col-12">
										  						<span><?php echo $evolucion->cuadro_clinico; ?></span>
										  					</div>
										  				</div>
										  			</div>
						  						</div>
							  					@endforeach
							  				</div>
										</div>
							  		</div>
							  		<div class="col-12" style="height: 5px;"></div>
							  		<div class="col-12">
							  			<div class="card card-primary @if($fecha_r != date('Y-m-d') ) collapsed-card @endif">
							  				<div class="card-header bg bg-primary colorbasic">
							  					<span> <b>Diagnostico CIE10</b></span>
											    
											    <!-- /.card-tools -->
											</div>
											<div class="card-body" style="" >
												<br>
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
								                                <button id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="fa fa-plus"> Agregar</span></button>
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
							  	</div>
							</div>
						
			    	</div>
			    	@if($fecha_r == date('Y-m-d') )
					<script type="text/javascript">
						editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');
					</script>
					@endif
		    	@endforeach

		    	@php
	                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

	                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
	            @endphp

		    	@foreach($procedimientos1 as $value)
			    	<div class="col-12">
			    		<div class="card collapsed-card" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
							<div class="card-header with-border" style= color: black; text-align: center; border-bottom: #004AC1;">
						  		<div class="row">
						  			<div class="col-4">
			    						@if(!is_null($value->f_operacion))
					                        @php
						                        $dia =  Date('N',strtotime($value->f_operacion));
						                        $mes =  Date('n',strtotime($value->f_operacion)); 
						                        $aux = intval($dia)-1;
	                                            $ms  = intval($mes)-1;
						                    @endphp

		                               		<b>{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</b>
		                                @else
		                                 	@php
						                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
						                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); 
						                        $aux = intval($dia)-1;
		                                        $ms = intval($mes)-1;
					                        @endphp

		                               		<b> {{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>

				                        @endif
				                    </div>
				                    <div class="col-4">
										<div>
											<span style=" font-size: 12px">Procedimiento:</span>
											<span style="font-size: 12px"> {{$value->nombre}} </span>
										</div>
									</div>
									<div class="col-3">
										<div>
											<span style=" font-size: 12px">Dr (a) </span>
											<span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
										</div>
									</div>
							    </div>
							    <div style="color: white">
			                       {{$value->id_protocolo}}
			                    </div>
						    	<div class="pull-right card-tools">
		                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            	<i class="fa fa-plus"></i>
		                            </button>
		                    	</div>
							    <!-- /.card-tools -->
							</div>
							<div class="card-body" style="">
							  	<div class="row">
							  		<div class="col-md-8 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
							  			<div class="card" style="border: 2px solid #004AC1; border-radius: 3px; margin-bottom: 0;">
							  				<div class="card-header with-border" style="background-color: #004AC1; color: white; border-bottom: #004AC1;padding: 2px;">
							  					<div class="row">
								  					<div class="col-3" style="margin-right: 10px">
								    					
									    				<a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');" ><span class="fa fa-pencil-square-o"></span>
									    				</a>
								    				</div>
								    				<div class="col-8" style="text-align: center">
								  						<span >Detalles del Procedimientos</span>
								  					</div>
							  					</div>
							  				</div>
							  				<div class="card-body" style="font-size: 11px;" id="procedimiento{{$value->id_procedimiento}}">
								  				<div class="row">
								  					<div class="col-12">&nbsp;</div>
								  					<div class="col-12">
								  						<span style="">Procedimiento</span>
								  					</div>
								  					<div class="col-12">
								  						<span>
								  							{{$value->nombre}}
								  						</span>
								  					</div>
								  					<div class="col-12">&nbsp;</div>

								  					<div class="col-12" style="padding-top: 10px">
								  						<span style="">Seguro</span>
								  					</div>
								  					@php
								  						$hc_seguro = \Sis_medico\Seguro::where('id', $value->seguro_final)->first();
								  					@endphp
								  					<div class="col-12" style="padding-bottom: 10px"><span> @if(!is_null($hc_seguro)) {{$hc_seguro->nombre}} @endif</span>

								  					</div>

								  					<div class="col-12">
								  						<span style="">Hallazgos</span>
								  					</div>
								  					<div class="col-12">
								  						<span><?php echo strip_tags($value->hallazgos); ?></span>
								  					</div>
								  					<div class="col-12">&nbsp;</div>
								  					<div class="col-12">
								  						<span style="">Conclusiones</span>
								  					</div>
								  					<div class="col-12">
								  						<span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
								  					</div>
								  					<div class="col-4">
								  						<span style="">M&eacute;dico Examinador</span>
								  					</div>
								  					<div class="col-4">
								  						<span style="">&nbsp;</span>
								  					</div>
								  					<div class="col-4">
								  						<span style="">&nbsp;</span>
								  					</div>
								  					<div class="col-4">
								  						<span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
								  					</div>
								  				</div>
								  			</div>
							  			</div>
							  		</div>
							  		<div class="col-md-4 col-sm-12 col-12" style="padding-left: 5px; padding-right: 10px;">
							  			<div class="card" style="border: 2px solid #004AC1; border-radius: 3px; margin-bottom: 0;">
							  				<div class="card-header with-border" style="background-color: #004AC1; color: white; text-align: center; border-bottom: #004AC1;padding: 2px;">
							  					<div class="row">
							  						<div class="col-3" style="margin-right: 10px">
							  							<div class="btn" style="color: white">
								    						<a class="fa fa-pencil-square-o "  onclick="editar_imagenes({{$value->id_protocolo}})" ><span style="font-size: 13px">&nbsp;Editar</span>
								    						</a>
							    						</div>
				                                 	</div>
				                                 	<div class="col-8" style="text-align: center">
							  							<span>Imagenes Procedimientos</span>
							  						</div>
							  					</div>
							  				</div>
							  				<div class="card-body">
								  				<div class="row">
								  					@php
								  						$id_protocolo = $value->id_protocolo;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->limit(8)->OrderBy('created_at', 'desc')->get()
								  					@endphp
								  					@foreach($imagenes as $imagen)
								  					<div class="col-md-6 col-sm-4">
			                                            @php
			                                                $explotar = explode( '.', $imagen->nombre);
			                                                $extension = end($explotar);
			                                            @endphp
			                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
			                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
			                                                </a><br>
			                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
			                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
			                                                </a>
			                                            @elseif(($extension == 'mp4'))
			                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
			                                                </a>
			                                            @else
			                                                @php
			                                                    $variable = explode('/' , asset('/hc_ima/'));
			                                                    $d1 = $variable[3];
			                                                    $d2 = $variable[4];
			                                                    $d3 = $variable[5];
			                                                 @endphp
			                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
			                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/office.png')}}" width="98%">
			                                                </a>
			                                            @endif
								  					</div>
								  					@endforeach
								  				</div>
								  			</div>
							  			</div>
							  		</div>
							  		<div class="col-12" style="padding-top: 15px">
							  			<div class="card card-primary collapsed-card" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
							  				<div class="card-header with-border" style= color: black; text-align: center; border-bottom: #004AC1;">
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
											    <div class="pull-right card-tools">
							                        <button style="margin-top: 8px"  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
							                            <i class="fa fa-plus"></i></button>
							                    </div>
											    <!-- /.card-tools -->
											</div>
											<div class="card-body" style="" id="evolucion_agregar{{$value->id_procedimiento}}">
						  						@php
							  						$evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
							  					@endphp
							  					@foreach($evoluciones as $evolucion)
							  					<div class="card" style="border: 2px solid #004AC1; border-radius: 3px; margin-bottom: 0;">
									  				<div class="card-header with-border" style="background-color: #004AC1; color: white; border-bottom: #004AC1;padding: 2px;">
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
									  				<div class="card-body" style="font-size: 11px;" id="evolucion{{$evolucion->id}}">
										  				<div class="row">
										  					<div class="col-12">
										  						<span style="">Motivo</span>
										  					</div>
										  					<div class="col-12">
										  						<span>{{$evolucion->motivo}}</span>
										  					</div>
										  					<div class="col-12">&nbsp;</div>
										  					<div class="col-12">
										  						<span style="">Detalle</span>
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
							  			<div class="card card-primary collapsed-card" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
							  				<div class="card-header with-border" style= color: black; text-align: center; border-bottom: #004AC1;">
							  					<span> <b>Diagnostico CIE10</b></span>
											    <div class="pull-right card-tools">
							                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
							                            <i class="fa fa-plus"></i></button>
							                    </div>
											    <!-- /.card-tools -->
											</div>
											<div class="card-body" style="" >
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
							                                <button id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="fa fa-plus"> Agregar</span></button>
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
							  	</div>
							</div>
						</div>
			    	</div>
		    	@endforeach
	    
    </div>
	
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
	var remoto_href = '';
	jQuery('body').on('click', '[data-toggle="modal"]', function() {
	    if(remoto_href != jQuery(this).data('remote')) {
	        remoto_href = jQuery(this).data('remote');
	        jQuery(jQuery(this).data('target')).removeData('bs.modal');

	        jQuery(jQuery(this).data('target')).find('.modal-body').empty();
	        jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
	    }
	});
	function editar_imagenes22(id_protocolo){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('paciente.imagenes_protocolo')}}"+'/'+id_protocolo,
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
	function editar_imagenes(id_protocolo) {
	  window.location.assign("{{route('paciente.imagenes_protocolo')}}"+'/'+id_protocolo);

    }
    function agregar_procedimiento_ecografia(){
    	contador = parseInt($('#contador_endoscopicos').val());
    	hc_endoscopico = parseInt($('#id_endoscopico').val());
    	hc_funcional = parseInt($('#id_funcional').val());
    	var url_datos = "";
    	if (hc_endoscopico >0) {
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/2/{{$paciente->id}}/"+hc_endoscopico;
        	agregar_procedimiento_tipo(url_datos);
        	console.log(url_datos);
        }else if (hc_funcional >0) {
        	//alert(hc_funcional);
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/2/{{$paciente->id}}/"+hc_funcional;
        	agregar_procedimiento_tipo(url_datos);
        }else{
          $.ajax({
				async: true,
				type: "GET",
				url: "{{route('hc4_procedimiento.selecciona_procedimiento',['tipo' => '2', 'paciente' => $paciente->id ])}}",
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
    	//alert("agregar");
    }


    function editarprocedimiento2(id){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('editar.procedimiento_ecografia2')}}/"+id,
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

	function editar_evolucion(id, id_paciente){
    	var entra = id;
		$.ajax({
			type: "GET",
			url: "{{route('quirofano.editar_evolucion')}}/"+id+'/'+id_paciente,
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
			url: "{{route('quirofano.agregar_evolucion')}}/"+id,
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
                        cell5.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="fa fa-trash" ></span></a>';

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


	@foreach($procedimientos2 as $value)

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

    @foreach($procedimientos1 as $value)

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
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="fa fa-trash"></span></a>';

            },
            error: function(data){

                }
        })
    }
</script>