
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

<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link
      type="text/css"
      href="https://uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.css"
      rel="stylesheet"
    />
    <style>
      .tui-image-editor-header-logo{
        display: none;
      }
	  .tui-image-editor-load-btn{
		display: none;
	  }
    </style>

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



	@foreach($procedimientos2 as $value)
	    .image-checkbox{{$value->id_protocolo}} {
	        cursor: pointer;
	        box-sizing: border-box;
	        -moz-box-sizing: border-box;
	        -webkit-box-sizing: border-box;
	        border: 4px solid transparent;
	        margin-bottom: 0;
	        outline: 0;
	    }
	    .image-checkbox{{$value->id_protocolo}} input[type="checkbox"] {
	        display: none;
	    }
	    .image-checkbox{{$value->id_protocolo}} .fa {
	      position: absolute;
	      color: #004AC1;
	      background-color: white;
	      padding: 10px;
	      top: 0;
	      right: 0;
	    }
	@endforeach



	@foreach($procedimientos1 as $value)

		.image-checkbox2{{$value->id_protocolo}} {
	        cursor: pointer;
	        box-sizing: border-box;
	        -moz-box-sizing: border-box;
	        -webkit-box-sizing: border-box;
	        border: 4px solid transparent;
	        margin-bottom: 0;
	        outline: 0;
	    }
	    .image-checkbox2{{$value->id_protocolo}} input[type="checkbox"] {
	        display: none;
	    }
	    .image-checkbox2{{$value->id_protocolo}} .fa {
	      position: absolute;
	      color: #004AC1;
	      background-color: white;
	      padding: 10px;
	      top: 0;
	      right: 0;
	    }


	@endforeach

    .image-checkbox-checked {
        border-color: red;
    }

    .image-checkbox-checked2 {
        border-color: red;
    }

    .image-checkbox-checked2 .fa {
      display: block !important;
    }

	.image-checkbox-checked .fa {
      display: block !important;
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>
</div>
<div class="modal fade bd-example-modal-xl" id="editimage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document" style="width: 80%;">
      <div class="modal-content" id="imagecontent" >

      </div>
    </div>
</div>
<div class="box" style="border: 2px solid#004AC1; background-color: #56ABE3; ">
	<div class="box-header " style="background-color:#004AC1; color: white; font-family: 'Helvetica general3'; padding-top: 0px; padding-bottom: 0px; ">
	  	<div class="row">
		  	<div class="col-md-12">
			    <h1 style="font-size: 15px; background-color:#004AC1; color: white; margin-top: 0px; margin-bottom: 0px" >
	            	<img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	             	<b>ARMAR ESTUDIOS</b>
				</h1>
			</div>
		</div>
        @if(!is_null($paciente))
			<center>
			    <div class="col-12" style="padding-bottom: 20px;">
					<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
			            <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
			            	{{$paciente->nombre1}} {{$paciente->nombre2}}
		                </b>
					</h1>
				</div>
			</center>
		@endif
	    <!-- /.box-tools -->
	</div>
  <!-- /.box-header -->
  <div class="box-body" style="padding-left: 0px">
  	<div class="col-12">
		  	<div class="row parent" >
		  		<span id="msn1" style="color: white;"></span>
		  		@foreach($procedimientos2 as $value)


			    	<div class="col-12 {{$value->id_protocolo}}">
			    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; ">
						  	<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
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
							    <div class="pull-right box-tools">
			                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
			                            <i class="fa fa-minus"></i>
			                        </button>
			                    </div>
		                        <div class="col-12" style="margin-top: 20px; background-color: #004AC1; color: white; width: 100%; height: 100%">
			                    	@if(!is_null($value))
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
						  			@endif
						  			{{$texto}}
			                    </div>
						  	</div>
						  	<div class="box-body" style="background: white;">
							  	<div class="row">

							  		<div class="col-md-10 col-sm-12 col-12" style="padding-left: 15px;">
							  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
							  				<div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">

							  					<span>Imagenes de Procedimientos</span>
							  				</div>
							  				@php
						  						$id_protocolo = $value->id_protocolo;
						  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get();
						  						//echo $imagenes->last()->id;
						  					@endphp
							  				@if(!is_null($imagenes) && $imagenes != '[]')
							  				<div class="col-12" style="">Imagenes Seleccionas:
							  					<span id="contador_muestra{{$value->id_protocolo}}">0</span>
							  				</div>
					                        <form id="modificar_fecha{{$value->id_protocolo}}" >
						                        <div class="row"  style="margin-top: 10px">

						                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
						                              <input type="hidden" name="id" value="{{$value->id_protocolo}}">
						                              <input type="hidden" name="id_procedimiento" value="{{$value->id_procedimiento}}">
						                              <span style="font-family: 'Helvetica general">Fecha imprimible:</span>
						                              <input style="border: 2px solid #004AC1;" type="text" name="fecha" id="fecha1{{$value->id_protocolo}}" value="@if($value->fecha != null){{$value->fecha}}@else{{substr($value->created_at_proto, 0, -9)}}@endif" class="form-control pull-right input-sm" required onchange="cambio_fecha({{$value->id_protocolo}})">
						                              <br><br>
						                            </div>

						                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
						                                <span style="font-family: 'Helvetica general">Medico que firma</span>
						                                <select style="border: 2px solid #004AC1;" onchange="cambio_fecha({{$value->id_protocolo}});" class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador2" id="id_doctor_examinador2">
						                                    @foreach($doctores as $dr)
						                                        <option @if($value->id_doctor_examinador2 == $dr->id) selected @endif value="{{$dr->id}}" >{{$dr->apellido1}} @if($dr->apellido2 != "(N/A)"){{ $dr->apellido2}}@endif {{ $dr->nombre1}} @if($dr->nombre2 != "(N/A)"){{ $dr->nombre2}}@endif</option>
						                                    @endforeach
						                                </select>
						                            </div>
						                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
						                                <span style="font-family: 'Helvetica general">Medico responsable</span>
						                                <select style="border: 2px solid #004AC1;" onchange="cambio_fecha({{$value->id_protocolo}});" class="form-control input-sm" style="width: 100%;" name="id_doctor_responsable" id="id_doctor_responsable">
						                                	<option value="">No</option>
						                                    @foreach($doctores as $dr)
						                                        <option @if($value->id_doctor_responsable == $dr->id) selected @endif value="{{$dr->id}}" >{{$dr->apellido1}} @if($dr->apellido2 != "(N/A)"){{ $dr->apellido2}}@endif {{ $dr->nombre1}} @if($dr->nombre2 != "(N/A)"){{ $dr->nombre2}}@endif</option>
						                                    @endforeach
						                                </select>
						                            </div>
						                            @php
						                            	$referido_agenda = \Sis_medico\Paciente::where('id', $paciente->id)->first();
						                            	//dd($referido_agenda->referido);
						                            @endphp

						                            @php
						                            	$referido_doctor = \Sis_medico\hc_protocolo::where('id', $value->id_protocolo)->first();
						                            	//dd($referido_doctor->referido_por);
						                            @endphp


						                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
						                                @php
						                                	$referido = "";
							                            	$referido_estudio = "";
							                            	if($referido_doctor->referido_por != null){
								                            	$referido = $referido_doctor->referido_por;
								                            }
									                        elseif(!is_null($referido_agenda->referido)){

									                        	 $input =  [
														            'referido_por' => $referido_agenda->referido
														        ];

										                        \Sis_medico\hc_protocolo::where('id', $value->id_protocolo)->update($input);

										                        $referido = $referido_agenda->referido;
										                    }
						                            	@endphp
						                            	<span style="font-family: 'Helvetica general">Referido por:</span>
						                                <input class="form-control" onchange="cambio_fecha({{$value->id_protocolo}});" type="text" name="referido" id="referido1{{$value->id_protocolo}}" value="@if(!is_null($referido)){{$referido}} @endif" style="border: 2px solid #004AC1; ">
						                            </div>
						                        </div>
					                        </form>
					                        @endif
							  				<div id="{{$value->id_protocolo}}" class="box-body">
								  				<div class="row">
								  					@php
								  						$id_protocolo = $value->id_protocolo;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->get();
								  					@endphp
        											<input type="hidden" id="contador1{{$value->id_protocolo}}" value="0">
					                                @if(!is_null($imagenes) && $imagenes != '[]')
						                                @foreach($imagenes as $imagen)
						                                	@php
				                                                $explotar = explode( '.', $imagen->nombre);
				                                                $extension = end($explotar);
				                                            @endphp
							                                @if($extension == "jpg" ||
				                                            	$extension == "jpeg" ||
				                                            	$extension == "png" ||
				                                            	$extension == "bmp" ||
				                                            	$extension == "jpe" ||
				                                            	$extension == "jfif" ||
				                                            	$extension == "tiff" ||
				                                            	$extension == "tif" ||
				                                            	$extension == "JPG" ||
				                                            	$extension == "JPEG" ||
				                                            	$extension == "PNG" ||
				                                            	$extension == "BMP" ||
				                                            	$extension == "JPE" ||
				                                            	$extension == "JFIF" ||
				                                            	$extension == "TIFF" ||
				                                            	$extension == "TIF" )
							                                <div  class="col-sm-4 col-12" >
								                                    <label class="image-checkbox{{$value->id_protocolo}}" onclick="seleccion_imagen({{$imagen->id}})" style=" min-width: 110px; min-height: 70px">
								                                        <img src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}" width="100%" >
								                                        <input   type="checkbox" name="image[]" value="{{$imagen->id}}" @if($imagen->seleccionado == 1){{"checked"}} @endif/>
								                                    </label>
																	 
								                                     <input class="imagen_recortada" type="checkbox" value="{{$imagen->id}}" @if($imagen->seleccionado_recortada == 1){{"checked"}} @endif  ><span style="font-size: 12px">Recortar Imagen</span>

																	 <div>
																	 <button type="button" class="btn btn-info btn_accion" style="color: white;"  onclick="modal_image({{$imagen->id}})"> Editar &nbsp; &nbsp;  <i class="fa fa-edit"></i> </button>
																	 </div>

							                                </div>
							                                @endif
							  							@endforeach
							  						@else
							  							<div class="col-12" style="padding-top: 50px; padding-bottom: 50px">
								  							<div style=" text-align: center">
								  								<span style="font-family: 'Helvetica general';">No posee imagenes de procedimientos</span>
								  							</div>
							  							</div>
							  						@endif
								  				</div>
								  			</div>
							  			</div>
							  		</div>
							  		<div class="col-md-2 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

								  			<div class="col-12">
								  				<div class="row">
								  					<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
									  					<a type="button" class="btn btn-info" id="" style="width: 100%; height: 100%; font-size: 12px; background-color: green " onclick="regresar_proc_endoscopico({{$value->id_protocolo}}, '{{$paciente->id}}');">
									  						<i style="color: white" class="fa fa-reply" aria-hidden="true"></i>
				                        					<span style=" color: white" >Revisar Procedimiento</span>
				                    					</a>
				                    				</div>
				                    				@if(!is_null($imagenes->last()))
									  				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
									  					<a type="button" class="btn btn-info btn_accion" id="recortar_todas_1{{$value->id_protocolo}}" style="width: 100%; height: 100%; font-size: 12px; ">
				                        					<span style=" color: white" >Recortar Todas</span>
				                    					</a>
				                    				</div>
				                    				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
										    			<a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%; height: 100%; font-size: 12px;" >
						                        			<div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
								                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
								                        		<label style="color: white; " >Descargar</label>
						                        			</div>
								 						</a>
								 					</div>
								 					@endif
							 					</div>
							 				</div>

									</div>
							  	</div>
						  </div>
						</div>
			    	</div>
		    	@endforeach
		    	@foreach($procedimientos1 as $value)

			    	<div class="col-12 {{$value->id_protocolo}}">
			    		<div class="box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						  <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
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
						    <div class="pull-right box-tools">
		                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            <i class="fa fa-minus"></i></button>
		                    </div>
		                        <div class="col-12" style="margin-top: 20px; background-color: #004AC1; color: white">
			                    	@php
			                    		$procedimiento =  \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
			                    		$procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
									@endphp
									<span>{{$procedimiento_completo->nombre_general}}</span>
			                    </div>

						  </div>
						  <div class="box-body" style="background: white;">
						  	<div class="row">

						  		<div class="col-md-10 col-sm-12 col-12" style="padding-left: 15px;">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
						  				<div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">

						  					<span>Imagenes Procedimientos</span>
						  				</div>
						  				@php
						  						$id_protocolo = $value->id_protocolo;
						  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get();
						  						//echo $imagenes->last()->id;
						  					@endphp
							  				@if(!is_null($imagenes) && $imagenes != '[]')
							  				<div class="col-12" >Imagenes Seleccionas:
							  					<span  id="contador_muestra2{{$value->id_protocolo}}">0</span>
							  				</div>
						                        <form id="modificar_fecha{{$value->id_protocolo}}" >
							                        <div class="row"  style="margin-top: 10px">

							                            <div class="col-md-4 col-sm-12 col-12" style="padding-right: 25px; padding-left: 25px">
							                              <input type="hidden" name="id" value="{{$value->id_protocolo}}">
							                              <input type="hidden" name="id_procedimiento" value="{{$value->id_procedimiento}}">
							                              <span style="font-family: 'Helvetica general">Fecha imprimible:</span>
							                              <input style="border: 2px solid #004AC1;" type="text" name="fecha" id="fecha2_{{$value->id_protocolo}}" value="@if($value->fecha != null){{$value->fecha}}@else{{substr($value->created_at_proto, 0, -9)}}@endif" class="form-control pull-right input-sm" required onchange="cambio_fecha({{$value->id_protocolo}})">
							                              <br><br>
							                            </div>

							                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
							                                <span style="font-family: 'Helvetica general">Medico que firma</span>
							                                <select style="border: 2px solid #004AC1;" onchange="cambio_fecha({{$value->id_protocolo}});" class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador2" id="id_doctor_examinador2">
							                                    @foreach($doctores as $dr)
							                                        <option @if($value->id_doctor_examinador2 == $dr->id) selected @endif value="{{$dr->id}}" >{{$dr->apellido1}} @if($dr->apellido2 != "(N/A)"){{ $dr->apellido2}}@endif {{ $dr->nombre1}} @if($dr->nombre2 != "(N/A)"){{ $dr->nombre2}}@endif</option>
							                                    @endforeach
							                                </select>
							                            </div>

							                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
							                                <span style="font-family: 'Helvetica general">Medico Responsable</span>
							                                <select style="border: 2px solid #004AC1;" onchange="cambio_fecha({{$value->id_protocolo}});" class="form-control input-sm" style="width: 100%;" name="id_doctor_responsable" id="id_doctor_responsable">
							                                	<option value="">No</option>
							                                    @foreach($doctores as $dr)
							                                        <option @if($value->id_doctor_responsable == $dr->id) selected @endif value="{{$dr->id}}" >{{$dr->apellido1}} @if($dr->apellido2 != "(N/A)"){{ $dr->apellido2}}@endif {{ $dr->nombre1}} @if($dr->nombre2 != "(N/A)"){{ $dr->nombre2}}@endif</option>
							                                    @endforeach
							                                </select>
							                            </div>

							                             @php
							                            	$referido_agenda = \Sis_medico\Paciente::where('id', $paciente->id)->first();
							                            @endphp

							                             @php
						                            		$referido_doctor = \Sis_medico\hc_protocolo::where('id', $value->id_protocolo)->first();
						                            	@endphp

							                            <div class="col-md-4 col-sm-12 col-12" style="padding-left: 25px; padding-right: 25px">
							                            	 @php
							                            	 	$referido = "";
								                            	$referido_estudio = "";
								                            	if($referido_doctor->referido_por != null){
									                            	$referido = $referido_doctor->referido_por;
									                            }
										                        elseif(!is_null($referido_agenda->referido)){

										                        	 $input =  [
															            'referido_por' => $referido_agenda->referido
															        ];

											                        \Sis_medico\hc_protocolo::where('id', $value->id_protocolo)->update($input);

											                        $referido = $referido_agenda->referido;
											                    }
							                            	@endphp
							                                <span style="font-family: 'Helvetica general">Referido por:</span>
							                                <input class="form-control" onchange="cambio_fecha({{$value->id_protocolo}});" type="text" name="referido" id="referido2{{$value->id_protocolo}}" value="@if(!is_null($referido)){{$referido}} @endif" style="border: 2px solid #004AC1;">
						                           		 </div>
							                        </div>
						                        </form>
					                        @endif
						  				<div id="{{$value->id_protocolo}}" class="box-body">
							  				<div class="row">
							  					@php
							  						$id_protocolo = $value->id_protocolo;
							  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->get();
							  					@endphp
        										<input type="hidden" id="contador2{{$value->id_protocolo}}" value="0">
				                                @if(!is_null($imagenes) && $imagenes != '[]')
					                                @foreach($imagenes as $imagen)
					                                	@php
			                                                $explotar = explode( '.', $imagen->nombre);
			                                                $extension = end($explotar);
			                                            @endphp
			                                            @if($extension == "jpg" ||
			                                            		$extension == "jpeg" ||
			                                            		$extension == "png" ||
			                                            		$extension == "bmp" ||
			                                            		$extension == "jpe" ||
			                                            		$extension == "jfif" ||
			                                            		$extension == "tiff" ||
			                                            		$extension == "tif" )
						                                	<div   class="col-sm-4 col-12"  >
							                                    <label  class="image-checkbox2{{$value->id_protocolo}}"  style=" min-width: 110px; min-height: 70px">
							                                        <img  src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}" width="100%" >
							                                        <input  type="checkbox" name="image[]" value="{{$imagen->id}}" @if($imagen->seleccionado == 1){{"checked"}} @endif/>

							                                    </label>
																 <button class="btn btn-info btn-xs" type="button" onclick="seleccion_imagen({{$imagen->id}})"> <i class="fa fa-edit"></i></button>
							                                     <input class="imagen_recortada" type="checkbox" value="{{$imagen->id}}" @if($imagen->seleccionado_recortada == 1){{"checked"}} @endif  ><span style="font-size: 10px">Recortar Imagen</span>
						                                	</div>
						                                @endif
						  							@endforeach
						  						@else
						  							<div class="col-12" style="padding-top: 50px; padding-bottom: 50px">
							  							<div style=" text-align: center">
							  								<span style="font-family: 'Helvetica general';">No posee imagenes de procedimientos</span>
							  							</div>
							  						</div>
						  						@endif
							  				</div>
							  			</div>
						  			</div>
						  		</div>
						  		<div class="col-md-2 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

							  			<div class="col-12">
								  			<div class="row">
								  				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
								  					<a type="button" class="btn btn-info  " id="" style="width: 100%; height: 100%; font-size: 12px; background-color: green" onclick="regresar_proc_endoscopico({{$value->id_protocolo}}, '{{$paciente->id}}');" >
								  						<i style="color: white" class="fa fa-reply" aria-hidden="true"></i>
			                        					<span style=" color: white" >Revisar Procedimiento</span>
			                    					</a>
			                    				</div>
			                    				@if(!is_null($imagenes->last()))
								  				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
										  			<a type="button" class="btn btn-info btn_accion" id="recortar_todas_2{{$value->id_protocolo}}" style="width: 100%; margin-bottom: 15px;  font-size: 12px; height: 23px">
					                        			<span style="width: 100%; color: white" >Recortar Todas</span>
					                    			</a>
				                    			</div>
				                    			<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
										    		<a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%;  font-size: 12px; height: 23px; margin-bottom: 15px" >
								 						<div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
							                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
							                        		<label style="color: white; " >Descargar</label>
						                        		</div>
								 					</a>
						 						</div>
						 						@endif
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
</script>

<!--<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>-->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
function modal_image(seq){
	$.ajax({
      type: 'get',
      url:"{{url('modalimage/edit/')}}/"+seq,
      datatype: 'json',
      success: function(data){
        $('#imagecontent').empty().html(data);
        $('#editimage').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });  
}
$('#editimage').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
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



	 $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
        //$(this).find('#imagen_solita').empty().html('');
    });

	<?php foreach ($procedimientos2 as $value): ?>
		$('#recortar_todas_1{{$value->id_protocolo}}').on('click', function(event, value){
	        $.ajax({
	            url:"{{route('seleccionar_todas.recortar', ['id_protocolo' => $value->id_protocolo])}}",
	            dataType: "json",
	            type: 'get',
	            success: function(data){
	              //  location.reload();
	              cargar_imagenes_paciente();
	            },
	            error: function(data){
	                console.log(data);
	            }
	        });
	    });
	<?php endforeach?>



	<?php foreach ($procedimientos1 as $value): ?>
		    $('#recortar_todas_2{{$value->id_protocolo}}').on('click', function(event, value){
	        $.ajax({
	            url:"{{route('seleccionar_todas.recortar', ['id_protocolo' => $value->id_protocolo])}}",
	            dataType: "json",
	            type: 'get',
	            success: function(data){
	               // location.reload();
	               cargar_imagenes_paciente();
	            },
	            error: function(data){
	                console.log(data);
	            }
	        });
	    });
	<?php endforeach?>


    function seleccion_imagen(id){
        $.ajax({
            url:"{{route('hc_reporte.cambio_seleccion')}}/"+id,
            dataType: "json",
            type: 'get',
            success: function(data){
                alert(data);

            },
            error: function(data){
                console.log(data);
            }
        })
    }



     //   $(".breadcrumb").append('<li class="active">Historia Clinica</li>');
<?php foreach ($procedimientos2 as $value): ?>
        $('#fecha1{{$value->id_protocolo}}').datetimepicker({
            format: 'YYYY/MM/DD',
            });
        $("#fecha1{{$value->id_protocolo}}").on("dp.change", function (e) {
            cambio_fecha({{$value->id_protocolo}});
        });
<?php endforeach?>

<?php foreach ($procedimientos1 as $value): ?>
        $('#fecha2_{{$value->id_protocolo}}').datetimepicker({
            format: 'YYYY/MM/DD',
            });
        $("#fecha2_{{$value->id_protocolo}}").on("dp.change", function (e) {
            cambio_fecha({{$value->id_protocolo}});
        });
<?php endforeach?>

  function cambio_fecha(id){

  	//console.log($("#modificar_fecha"+id).serialize());
      $.ajax({
          type: 'post',
          url:'{{route("hc_foto.fecha_convenios")}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#modificar_fecha"+id).serialize(),
          success: function(data){
              //alert('valio');

          },
          error: function(data){
            console.log(data);
          }
      })
    }

        $('.imagen_recortada').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

        $('.imagen_recortada').on('ifChecked', function(event, value){
            var val = this.checked ? this.value : '';
            $.ajax({
                url:"{{route('hc_reporte.cambio_seleccion2')}}/"+val,
                dataType: "json",
                type: 'get',
                success: function(data){
                    alert(data);
                },
                error: function(data){
                    console.log(data);
                }
            });
        });
        $('.imagen_recortada').on('ifClicked', function(event, value){
            var val = this.checked ? this.value : '';
            $.ajax({
                url:"{{route('hc_reporte.cambio_seleccion2')}}/"+val,
                dataType: "json",
                type: 'get',
                success: function(data){
                    alert(data);
                },
                error: function(data){
                    console.log(data);
                }
            });
        });


<?php foreach ($procedimientos2 as $value): ?>
    $(".image-checkbox{{$value->id_protocolo}}").each(function () {
      if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
        $(this).addClass('image-checkbox-checked');

        contador = parseInt($('#contador1{{$value->id_protocolo}}').val());
        contador = contador+1;
        $('#contador1{{$value->id_protocolo}}').val(contador);
        $(this).find("i").text(contador);
        $('#contador_muestra{{$value->id_protocolo}}').text(contador);

      }
      else {
        $(this).removeClass('image-checkbox-checked');

      }
    });
    // sync the state to the input
    $(".image-checkbox{{$value->id_protocolo}}").on("click", function (e) {
        $(this).toggleClass('image-checkbox-checked');
        var $checkbox = $(this).find('input[type="checkbox"]');
      $checkbox.prop("checked",!$checkbox.prop("checked"))

      e.preventDefault();
        if ($(this).hasClass('image-checkbox-checked')){
            contador = parseInt($('#contador1{{$value->id_protocolo}}').val());
            contador = contador+1;
            $('#contador_muestra{{$value->id_protocolo}}').text(contador);
            $('#contador1{{$value->id_protocolo}}').val(contador);
            //$(this).find("i").text(contador);
            var cuenta = 1;
            $(".image-checkbox{{$value->id_protocolo}}").each(function () {
              if ($(this).hasClass('image-checkbox-checked')) {
                $(this).find("i").text(cuenta);
                cuenta++;
              }

            });
           //alert(contador);
        }else{


           contador = parseInt($('#contador1{{$value->id_protocolo}}').val());
            contador = contador-1;
            $('#contador_muestra{{$value->id_protocolo}}').text(contador);
            $('#contador1{{$value->id_protocolo}}').val(contador);
            var cuenta2 = 1;
            $(".image-checkbox{{$value->id_protocolo}}").each(function () {
              if ($(this).hasClass('image-checkbox-checked')) {
                $(this).find("i").text(cuenta2);
                cuenta2++;
              }
            });
            //alert(contador);
        }
    });
<?php endforeach?>

<?php foreach ($procedimientos1 as $value): ?>
    $(".image-checkbox2{{$value->id_protocolo}}").each(function () {
      if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
        $(this).addClass('image-checkbox-checked2');

        contador = parseInt($('#contador2{{$value->id_protocolo}}').val());
        contador = contador+1;
        $('#contador2{{$value->id_protocolo}}').val(contador);
        $(this).find("i").text(contador);
        $('#contador_muestra2{{$value->id_protocolo}}').text(contador);

      }
      else {
        $(this).removeClass('image-checkbox-checked2');
      }
    });
    // sync the state to the input
    $(".image-checkbox2{{$value->id_protocolo}}").on("click", function (e) {
        $(this).toggleClass('image-checkbox-checked2');
        var $checkbox = $(this).find('input[type="checkbox"]');
      $checkbox.prop("checked",!$checkbox.prop("checked"))

      e.preventDefault();
        if ($(this).hasClass('image-checkbox-checked2')){
            contador = parseInt($('#contador2{{$value->id_protocolo}}').val());
            contador = contador+1;
            $('#contador_muestra2{{$value->id_protocolo}}').text(contador);
            $('#contador2{{$value->id_protocolo}}').val(contador);
            //$(this).find("i").text(contador);
            var cuenta = 1;
            $(".image-checkbox2{{$value->id_protocolo}}").each(function () {
              if ($(this).hasClass('image-checkbox-checked2')) {
                $(this).find("i").text(cuenta);
                cuenta++;
              }

            });
           //alert(contador);
        }else{


            contador = parseInt($('#contador2{{$value->id_protocolo}}').val());
            contador = contador-1;
            $('#contador_muestra2{{$value->id_protocolo}}').text(contador);
            $('#contador2{{$value->id_protocolo}}').val(contador);
            var cuenta2 = 1;
            $(".image-checkbox2{{$value->id_protocolo}}").each(function () {
              if ($(this).hasClass('image-checkbox-checked2')) {
                $(this).find("i").text(cuenta2);
                cuenta2++;
              }
            });
            //alert(contador);
        }
    });
<?php endforeach?>

		 function regresar_proc_endoscopico(id, id_paciente){
			$.ajax({
				type: "GET",
	            url: "{{route('hc4/regresar_proc_endo')}}/"+id+'/'+id_paciente,
	            datatype: 'json',
				success: function(data){
				$("#area_trabajo").html(data);
				},
				error:  function(){
					alert('error al cargar');
				}
			});

		}


</script>
