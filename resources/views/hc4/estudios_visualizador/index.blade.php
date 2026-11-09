
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

    .study-preview-card {
        overflow: hidden;
        border: 1px solid #78c8d4 !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: 0 10px 24px rgba(8, 72, 108, .08);
    }

    .study-preview-card > .study-preview-card__header {
        display: grid !important;
        grid-template-columns: minmax(170px, 220px) minmax(0, 1fr) 48px;
        grid-template-rows: auto;
        align-items: center;
        gap: 16px;
        min-height: 96px;
        padding: 14px 16px !important;
        color: #fff !important;
        border: 0 !important;
        background: linear-gradient(110deg, #08658f 0%, #0784ad 52%, #17aaa8 100%) !important;
        font-family: 'Helvetica general3', Arial, sans-serif;
    }

    .study-preview-card__header > b {
        grid-column: 1;
        grid-row: 1;
        display: block;
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.45;
        text-align: center;
    }

    .study-preview-card__header > .col-12 {
        grid-column: 2;
        grid-row: 1;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 12px !important;
        color: #fff !important;
        background: transparent !important;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.35;
        text-align: center;
        overflow-wrap: anywhere;
    }

    .study-preview-card__header > .box-tools {
        position: static !important;
        top: auto !important;
        right: auto !important;
        float: none !important;
        grid-column: 3;
        grid-row: 1;
        margin: 0 !important;
        text-align: right;
    }

    .study-preview-card__header > .box-tools .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        padding: 0;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 10px;
        background: rgba(255, 255, 255, .12);
        box-shadow: none;
    }

    @media (max-width: 767px) {
        .study-preview-card > .study-preview-card__header {
            grid-template-columns: minmax(110px, 135px) minmax(0, 1fr) 42px;
            gap: 8px;
            min-height: 82px;
            padding: 10px !important;
        }

        .study-preview-card__header > b { font-size: 12px; }
        .study-preview-card__header > .col-12 { padding: 0 4px !important; font-size: 13px; }
        .study-preview-card__header > .box-tools .btn { width: 38px; height: 38px; }
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>
</div>
<div class="box" style="border: 2px solid#004AC1; background-color: #56ABE3; ">
	@include('hc4.partials.module_header', [
		'moduleTitle' => __('hc4.visualizarestudios_'),
		'moduleIcon' => asset('/').'hc4/img/iconos/imagenes.png',
		'paciente' => $paciente
	])
  <!-- /.box-header -->
  <div class="box-body" style="padding-left: 0px">
  	<div class="col-12">
		  	<div class="row parent" >
		  		<span id="msn1" style="color: white;"></span>
		  		@foreach($procedimientos2 as $value)

			    	<div class="col-12 {{$value->id_protocolo}}">
<div class="box study-preview-card">
<div class="box-header with-border study-preview-card__header">
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
		                        <div class="col-12" style="margin-top: 20px; color: white; width: 100%; height: 100%">
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

							  		<div class="col-md-9 col-sm-12 col-12" style="padding-left: 15px;">
						  				@php
					  						$id_protocolo = $value->id_protocolo;
					  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->where('seleccionado','1')->count();

					  						//dd($imagenes);
					  						//echo $imagenes->last()->id;
					  					@endphp
							 			<embed style="max-width: 900px; height: 500px" id="miIFrame" src="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '7' ])}}" width="100%" type='application/pdf'>
							  		</div>
							  		<div class="col-md-3 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

							  			<div class="col-12">
		                    				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
								    			<a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%; height: 100%; font-size: 12px;" >
				                        			<div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
						                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
						                        		<label style="color: white; " >Descargar en otro Formato</label>
				                        			</div>
						 						</a>
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
<div class="box study-preview-card">
						  <div class="box-header with-border study-preview-card__header">
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
		                        <div class="col-12" style="margin-top: 20px; color: white">
			                    	@php
			                    		$procedimiento =  \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
			                    		$procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
									@endphp
									<span>{{$procedimiento_completo->nombre_general}}</span>
			                    </div>

						  </div>
						  	<div class="box-body" style="background: white;">
							  	<div class="row">

							  		<div class="col-md-9 col-sm-12 col-12" style="padding-left: 15px;">
						  				@php
					  						$id_protocolo = $value->id_protocolo;
					  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->where('seleccionado','1')->count();

					  						//dd($imagenes);
					  						//echo $imagenes->last()->id;
					  					@endphp
							 			<embed style="max-width: 900px; height: 500px" id="miIFrame" src="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '7' ])}}" width="100%" type='application/pdf'>
							  		</div>
							  		<div class="col-md-3 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

							  			<div class="col-12">
		                    				<div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
								    			<a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%; height: 100%; font-size: 12px;" >
				                        			<div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
						                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
						                        		<label style="color: white; " >Descargar en otro Formato</label>
				                        			</div>
						 						</a>
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
