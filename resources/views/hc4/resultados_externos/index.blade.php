<style type="text/css">
    .parent{
        overflow-y:scroll;
        overflow-x: hidden;
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
    #area_index .external-result-header {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 48px;
        align-items: center !important;
        gap: 12px;
        min-height: 76px;
        margin: 0 !important;
        padding: 12px 16px !important;
        color: #fff;
    }
    #area_index .external-result-header::before,
    #area_index .external-result-header::after {
        content: none !important;
        display: none !important;
    }
    #area_index .external-result-date {
        justify-self: start;
        min-width: 0;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #fff;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        text-align: left !important;
    }
    #area_index .external-result-tools {
        position: static !important;
        inset: auto !important;
        float: none !important;
        justify-self: end;
        margin: 0 !important;
        padding: 0 !important;
    }
    #area_index .external-result-tools .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        margin: 0 !important;
    }
</style>


<div id="area_index"  class="container-fluid" style="">
	<div class="row">
		<div class="col-12" style="padding-left: 0px;padding-right: 0px;margin-left: 0px;margin-right: 0px; ">
			<div class="col-12 box hc4-standard-module-container" style="margin-right: 0px; margin-left: 0px; padding-right: 0px; padding-left: 0px;">
				@include('hc4.partials.module_header', [
					'moduleTitle' => __('hc4.resultadosExternos-'),
					'moduleIcon' => asset('/').'hc4/img/iconos/exa_externos.png',
					'paciente' => $paciente
				])
			    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
					<div class="parent"   style="margin-left: 0px;   margin-bottom: 10px ; height: 450px " >
					    <div style=" margin-right: 30px;" >
							@foreach($biopsias_1 as $imagen)
			    			@php
			    				$explotar = explode( '.', $imagen->nombre);
									$extension = end($explotar);
									$fechaResultado = null;
									if (!is_null($imagen->created_at)) {
										$timestampResultado = strtotime($imagen->created_at);
										$diasResultado = [1 => 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
										$mesesResultado = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
										$fechaResultado = $diasResultado[(int) date('N', $timestampResultado)].' '.date('d', $timestampResultado).' de '.$mesesResultado[(int) date('n', $timestampResultado)].' del '.date('Y', $timestampResultado);
									}
								@endphp
								@if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
								    <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 20px; padding-left: 0px; padding-right: 0px" >
								    	<div class="box-header external-result-header">
								    		<div class="external-result-date">{{$fechaResultado}}</div>
								    		<div class="box-tools external-result-tools">
												<button type="button" class="btn btn-info btn-sm" data-widget="collapse" aria-label="Contraer resultado">
													<i class="fa fa-minus"></i>
												</button>
											</div>
								    	</div>

								    	<div class="box-body">
									    	<div class="row">
										    	<div class="col-md-9 col-12" >
											    	<div style="border: 2px solid #004AC1;  margin-bottom: 10px; margin-left: 15px">
													    <div class="row" id="imagen_solita">
													        <div class="col-12">
													            <img id="imafoto{{$imagen->id}}" src="{{asset('hc_ima/'.$imagen->nombre)}}" alt="Imagen Ingresada" style="max-width: 900px; height: 300px">
													            <!--<div class="col-md-3 col-md-offset-8" style="margin:20px 0;">
										<a class="btn btn-info btn_accion" style="height: 100%; font-size: 16px; color: white;" onclick="eliminar('{{$imagen->id}}');">@lang('hc4.eliminarFoto')</a>
													            </div>-->
													        </div>
													    </div>
												    </div>
											    </div>
											    <div class="col-md-3 col-12" style="padding-left: 0px">
											    	<center>
	<div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
											    			<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
		       						 							<div class="row" style="margin-left: 0px; margin-right: 0px">
										                        	<div class="col-12" style="padding-left: 0px; padding-right: 0px">
										                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
											<label style="color: white; padding-left: 5px;  padding-top: 1px" >@lang('hc4.descargar')</label>
										                        	</div>
						                        				</div>
	       						 							</a>
											    		</div>
											    	</center>
										    	</div>
											</div>
	</div>
	</div>
								    <script src="{{asset('/js/lupa.js')}}" > </script>
								    <script>
						            	$(document).ready(function() {
						                $("#imafoto{{$imagen->id}}").mlens(
						                {
						                    imgSrc: $("#imafoto{{$imagen->id}}").attr("data-big"),   // path of the hi-res version of the image
						                    lensShape: "circle",                // shape of the lens (circle/square)
						                    lensSize: 200,                  // size of the lens (in px)
						                    borderSize: 1,                  // size of the lens border (in px)
						                    borderColor: "#000000",                // color of the lens border (#hex)
						                    zoomLevel: 2,
						                    borderRadius: 0,                // border radius (optional, only if the shape is square)
						                    imgOverlay: $("#imafoto{{$imagen->id}}").attr("data-overlay"), // path of the overlay image (optional)
						                    overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
						                    responsive: true
						                });
						            	});
								    </script>

								@elseif(($extension == 'pdf') || ($extension == 'PDF'))
								    <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 20px; padding-left: 0px; padding-right: 0px" class="col-md-12 " >
			                            <div class="box-header external-result-header">
			                            	<div class="external-result-date">{{$fechaResultado}}</div>
			                            	<div class="box-tools external-result-tools">
													<button type="button" class="btn btn-info btn-sm" data-widget="collapse" aria-label="Contraer resultado">
														<i class="fa fa-minus"></i>
													</button>
												</div>
			                            </div>
								    	<div class="box-body">
										    <div class="row " >
										        <div class="col-md-9 col-12">
											        <div style="border: 2px solid #004AC1; margin-bottom: 10px; margin-left: 15px ">
											            <embed style="max-width: 900px; height: 300px" id="miIFrame" src="{{URL::to('/')}}/../storage/app/hc_ima/{{$imagen->nombre}}" width="100%" type='application/pdf'>
											        </div>
										        </div>
										        <div class="col-md-3 col-12">
										        	<center>
	<div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
										        			<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%;  font-size: 14px">
		       						 							<div class="row" style="margin-left: 0px; margin-right: 0px">
										                        	<div class="col-12" style="padding-left: 0px; padding-right: 0px">
										                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
										                        		<label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
										                        	</div>
						                        				</div>
	       						 							</a>
										        		</div>
										        	</center>
										    	</div>
									    	</div>
								    	</div>
								    </div>
						            <script type="text/javascript">
							            $(document).ready(function () {
							              var myWidth = 0, myHeight = 0;
							              if( typeof( window.innerWidth ) == 'number' ) {
							                //No-IE
							                myWidth = window.innerWidth;
							                myHeight = window.innerHeight;
							              } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
							                //IE 6+
							                myWidth = document.documentElement.clientWidth;
							                myHeight = document.documentElement.clientHeight;
							              } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
							                //IE 4 compatible
							                myWidth = document.body.clientWidth;
							                myHeight = document.body.clientHeight;
							              }
							              var nuevo_alto = myHeight*0.80;
							              document.getElementById("miIFrame").height = nuevo_alto;
							            });
						       		</script>
								@else
								    @php
								        $ruta = rawurlencode(asset('hc_ima/'.ltrim($imagen->nombre, '/')));
								    @endphp
								    <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 20px;" class="col-md-12" >
												<div class="box-header external-result-header">
													<div class="external-result-date">{{$fechaResultado}}</div>
													<div class="box-tools external-result-tools">
														<button type="button" class="btn btn-info btn-sm" data-widget="collapse" aria-label="Contraer resultado">
															<i class="fa fa-minus"></i>
														</button>
													</div>
												</div>
	 	<div class="box-body">
									        <div class="row">
									            <div class="col-md-9 col-12">
										            <div style="border: 2px solid #004AC1; margin-bottom: 30px; margin-left: 15px ">
	<embed style="max-width: 900px; height: 300px" id="miIFrame" src="https://docs.google.com/viewer?hl=en&embedded=true&url={{$ruta}}" width="100%" style="border: none;">
	</div>
									            </div>
									            <div class="col-md-3 col-12">
									            	<center>
	<div class="col-md-12 col-5" style="padding-left: 0px; padding-right: 0px">
										        			<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" style="height: 100%; width: 100%; font-size: 14px">
	       						 								<div class="row" style="margin-left: 0px; margin-right: 0px">
								                        			<div class="col-12" style="padding-left: 0px; padding-right: 0px">
										                        		<img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
										                        		<label style="color: white; padding-left: 5px;  padding-top: 1px" >Descargar</label>
									                        		</div>
					                        					</div>
	       						 							</a>
									        			</div>
									        		</center>
								   				</div>
			   				 				</div>
			   				 			</div>
			   				 		</div>
								    <script type="text/javascript">
								        $(document).ready(function () {
								          var myWidth = 0, myHeight = 0;
								          if( typeof( window.innerWidth ) == 'number' ) {
								            //No-IE
								            myWidth = window.innerWidth;
								            myHeight = window.innerHeight;
								          } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
								            //IE 6+
								            myWidth = document.documentElement.clientWidth;
								            myHeight = document.documentElement.clientHeight;
								          } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
								            //IE 4 compatible
								            myWidth = document.body.clientWidth;
								            myHeight = document.body.clientHeight;
								          }
								          var nuevo_alto = myHeight*0.80;
								          document.getElementById("miIFrame").height = nuevo_alto;
								        });
								    </script>
								@endif
							@endforeach
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
