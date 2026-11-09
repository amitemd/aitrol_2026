
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

    .image-bank-record-header {
        position: relative !important;
        display: grid !important;
        grid-template-columns: minmax(300px, .95fr) minmax(420px, 1.45fr) minmax(260px, .8fr);
        align-items: center;
        gap: 18px;
        min-height: 92px;
        padding: 14px 88px 14px 24px !important;
        text-align: left !important;
    }

    .image-bank-record {
        flex: 0 0 calc(100% - 28px) !important;
        width: calc(100% - 28px) !important;
        max-width: calc(100% - 28px) !important;
        margin-right: 28px !important;
        padding-right: 0 !important;
    }

    .image-bank-record > .box {
        width: 100% !important;
        max-width: 100% !important;
    }

    .image-bank-record-header::before,
    .image-bank-record-header::after,
    .image-bank-record-header > .row::before,
    .image-bank-record-header > .row::after {
        display: none !important;
        content: none !important;
    }

    .image-bank-record-header > .row {
        display: contents !important;
    }

    .image-bank-record-header > .row > [class*="col-"] {
        float: none !important;
        width: auto !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .image-bank-record-header .image-bank-record-date {
        text-align: left !important;
        white-space: nowrap;
    }

    .image-bank-record-header > .row > :nth-child(2) {
        min-width: 0;
        line-height: 1.45;
        white-space: normal;
        overflow-wrap: break-word;
    }

    .image-bank-record-header > .row > :nth-child(3) {
        min-width: 0;
        white-space: nowrap;
    }

    .image-bank-record-header > .box-tools,
    #area_trabajo .image-bank-record-header > .box-tools {
        position: absolute !important;
        float: none !important;
        top: 50% !important;
        right: 18px !important;
        left: auto !important;
        bottom: auto !important;
        z-index: 20;
        display: flex !important;
        width: 44px !important;
        max-width: 44px !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: translateY(-50%) !important;
    }

    .image-bank-record-header > .box-tools .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 9px !important;
    }

    @media (max-width: 1199px) {
        .image-bank-record-header {
            grid-template-columns: minmax(220px, .9fr) minmax(260px, 1.35fr) minmax(190px, .75fr);
            gap: 12px;
        }
    }

    @media (max-width: 767px) {
        .image-bank-record-header {
            grid-template-columns: minmax(0, 1fr);
            gap: 8px 12px;
            padding: 14px 72px 14px 14px !important;
        }

        .image-bank-record-header .image-bank-record-date,
        .image-bank-record-header > .row > :nth-child(2),
        .image-bank-record-header > .row > :nth-child(3) {
            grid-column: 1 / -1;
            white-space: normal;
        }

        .image-bank-record-header > .box-tools {
            width: 42px !important;
            max-width: 42px !important;
            right: 14px !important;
        }
    }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

@php
	$ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp

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
<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
  @include('hc4.partials.module_header', [
      'moduleTitle' => __('hc4.imagenes-'),
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente
  ])
  <!-- /.box-header -->
  <div class="box-body" style="background-color: #56ABE3;">
  	<div class="col-12">
	  	<div class="row parent" >
	  		<span id="msn1" style="color: white; "></span>
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
	  					$cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD ACUDE CON ORDEN DEL ".$seguro->nombre." PARA LA REALIZACION DE ".$nombre_procedimiento."<br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
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
			<div class="col-12 image-bank-record">
		    		<div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 0px; ">
						<div class="box-header with-border image-bank-record-header" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">
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
							<div class="row">
								<div class="col-4 image-bank-record-date">
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
										<span style="font-size: 12px"> {{$texto}} </span>
									</div>
								</div>
								<div class="col-3">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
										<span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
									</div>
								</div>
			               	</div>
			               	<div class="pull-right box-tools ">
		                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            <i class="fa fa-plus"></i>
		                        </button>
		               		</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
						  	<div class="row">

						  		<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 10px;">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
						  				<div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
						  					<div class="row">

			                                 	<div class="col-12" style="text-align: center">
						  							<span>Imagenes Procedimientos</span>
						  						</div>
						  					</div>
						  				</div>
						  				<div class="box-body">
						  					<form role="form" method="POST" action="{{route('hc_video.descargar_zip')}}">
							  					{{ csrf_field() }}
							  					<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
								  				<div class="row">
								  					@php
								  						$id_protocolo = $value->id_protocolo;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->get();
								  					@endphp


								  					@if($imagenes != '[]')
								  						@foreach($imagenes as $imagen)
										  					<div class="col-md-3 col-sm-4 ">
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


					                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
					                                                </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />



					                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
					                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
					                                                </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />

					                                            @elseif(($extension == 'mp4'))
					                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
					                                                    </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
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
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
					                                            @endif
										  					</div>
								  						@endforeach
								  						<br>
								                            <div class="col-12" style="padding-top: 10px">
								                            	<center>
									                                <button type="submit" class="btn btn-primary" formaction="{{route('hc_video.descargar_zip')}}">Descargar Seleccionadas
									                                </button>
								                                </center>
								                            </div>
								  					@else
								  						<div class="col-md-12 col-sm-12" style="text-align: center">
								  							<center><span style="font-family: 'Helvetica general'; font-size: 12px"> NO POSEE IMAGENES DEL PROCEDIMIENTO </span></center>
								  						</div>
								  					@endif

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

	    	@foreach($procedimientos1 as $value)
			<div class="col-12 image-bank-record">
		    		<div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						<div class="box-header with-border image-bank-record-header" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
					  		<div class="row">
<div class="col-4 image-bank-record-date">
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
	                            	<i class="fa fa-plus"></i>
	                            </button>
	                    	</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
						  	<div class="row">

						  		<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 10px;">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
						  				<div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
						  					<div class="row">

			                                 	<div class="col-12" style="text-align: center">
						  							<span>Imagenes Procedimientos</span>
						  						</div>
						  					</div>
						  				</div>
						  				<div class="box-body">
						  					<form role="form" method="POST" action="{{route('hc_video.descargar_zip')}}">
							  					{{ csrf_field() }}
							  					<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
								  				<div class="row">
								  					@php
								  						$id_protocolo = $value->id_protocolo;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->get()
								  					@endphp

								  					@if($imagenes != '[]')
								  						@foreach($imagenes as $imagen)
										  					<div class="col-md-3 col-sm-4">
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
					                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
					                                                </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
					                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
					                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
					                                                </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
					                                            @elseif(($extension == 'mp4'))
					                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
					                                                </a>
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
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
					                                                <input class="flat-green" type="checkbox" name="image[]" value="{{$imagen->id}}" />
					                                            @endif

										  					</div>
								  						@endforeach
								  						<br>
								                            <div class="col-12" style="padding-top: 15px">
								                            	<center>
									                                <button type="submit" class="btn btn-primary" formaction="{{route('hc_video.descargar_zip')}}">Descargar Seleccionadas
									                                </button>
								                                </center>
								                            </div>
								  					@else
								  						<div class="col-md-12 col-sm-12" style="text-align: center;">
								  							<center><span style="font-family: 'Helvetica general'; font-size: 12px"> NO POSEE IMAGENES DEL PROCEDIMIENTO </span></center>
								  						</div>
								  					@endif

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
  </div>
  <!-- box-footer -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">



 $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
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




</script>
