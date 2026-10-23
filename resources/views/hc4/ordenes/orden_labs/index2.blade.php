
 @foreach($ordenes as $orden)
	<div class="col-md-12">
   	    @php
			if(!is_null($orden->created_at)){
    		  	$fecha_r =  Date('Y-m-d',strtotime($orden->created_at)); 
    	    }else{
				$fecha_r =  Date('Y-m-d',strtotime($orden->created_at));
    		}
				if($orden->id_usuariocrea != ""){ 
               		$xdoctor = DB::table('users as us')->where('us.id',$orden->id_usuariocrea)->first();
               		if($xdoctor->id_tipo_usuario != '3'){
               			$xdoctor = DB::table('users as us')->where('us.id',$orden->id_doctor_ieced)->first();
               		}
            	}
   	  	@endphp 
   	  	@php
            $fecha = substr($orden->created_at,0,10);
            $invert = explode( '-',$fecha);
            $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];
		@endphp

        <div class="box @if($fecha_r != date('Y-m-d')) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px;">
   	  		<div class="box-header with-border" style="background-color: white; color: black;font-family: 'Helvetica general3';border-bottom: #004AC1;">
   	  			<div class="row">
   	  				<div class="col-md-5">
                            @if(!is_null($orden->created_at))
                                @php
		                         $dia =  Date('N',strtotime($orden->created_at));
		                         $mes =  Date('n',strtotime($orden->created_at));
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
                                 {{substr($orden->created_at,8,2)}} de
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
                                del {{substr($orden->created_at,0,4)}}</b>
                            @endif
                    </div>
                    <div class="col-md-5">
                    	<div>
							<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a): </span>
							<span style="font-size: 12px">@if(!is_null($xdoctor->nombre1))
							{{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif</span>
					    </div>
                    </div>
                    <div class="col-md-1" style="color: white">
		                  @if(!is_null($orden->id))
		                    {{$orden->id}}
		                  @endif
                    </div>

                    <div class="pull-right box-tools" style="padding-top: 4px;">
                    	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-plus"></i></button>
            	    </div>
                </div>

   	  		</div>
   	  		<div class="box-body" style="background: white;">
   	  			<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
	                <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

	                    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
		  					<div class="row">
			  					<div class="col-3" style="margin-right: 10px">
			  						
			  						@if($orden->seguro->tipo!='0')
			    					<div class="btn" style="color: white">
					    				<a class="fa fa-pencil-square-o " onclick="editar_orden_lab({{$orden->id}},{{$orden->estado}});" ><span style="font-size: 13px">&nbsp;Editar</span>
					    				</a>
				    				</div>
				    				@endif
			    				</div>
			    				<i style="color: #004AC1;">{{$orden->id}}</i>
			    				<div class="col-4" style="text-align: center;padding-top: 6px">
			  						<span >Detalle de Orden {{$orden->seguro->nombre}}</span>
			  					</div>
			  					<div class="col-4" style="text-align: right;padding-top: 6px">
						           <a class="btn btn-danger" @if($orden->seguro->tipo=='0') href="{{url('orden/descargar')}}/{{$orden->id}}" @else href="{{url('cotizador_p/orden/imprimir')}}/{{$orden->id}}"  @endif target="_blank" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden</a>
						        </div>
		  					</div>
		  			    </div>
		                <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden{{$orden->id}}">
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
			                        	@if(!is_null($orden->seguro))
							                <span style="font-family: 'Helvetica general';font-size: 12px">SEGURO:</span>
						                    <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
						                      <b>
						                        {{$orden->seguro->nombre}}
						                      </b>
						                    </label>
					                    @endif
			                        </div>
			                    </div>
			                </div>
			                <div class="col-12" style="padding-top: 5px"></div>
			                <div class="col-12" style="padding-top: 5px">
			                	@php
				                	$examenes_labs= array();
			                		if($orden->seguro->tipo=='0'){
										$detalles = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->get();
			                		}else{


										$detalles = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','examen_detalle.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();

										$examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
			                		}
			                	@endphp

								@if($orden->seguro->tipo=='0')
									
								@else
									<div class="row">
									    <div class="table-responsive col-md-12">
										    <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
										        <tbody>
										        	<tr>
										        		<td style="text-align: right;"><b>Cantidad:</b></td>
								              			<td style="text-align: right;"><b>{{$orden->cantidad}}</b></td>
										        	</tr>
										       		@php  $cambia = 0; $contador = 0; @endphp
										       		@foreach($examenes_labs as $examen)
										       			@if($cambia != $examen->id_examen_agrupador_labs)
										       				@php $contador = 0; @endphp
										       				<tr>
												            	<td colspan="2" style="background-color: #ff6600;color: white;margin: 0px;padding: 0;">{{$examen->lnombre}}</td>
												            </tr>
												            @php $cambia = $examen->id_examen_agrupador_labs; @endphp
										       			@endif
										       			@if($contador == 0)
										       			<tr >
										       			@endif
										                  <td style="padding: 5px;" >{{$examen->descripcion}}</td>

										                  @php $contador ++; @endphp
										                  @if($contador == 2) @php $contador = 0; @endphp @endif
										                @if($contador == 0)
										                </tr>
										                @endif

										       		@endforeach
										        </tbody>
									      	</table>
								    	</div>
								  </div>
								@endif

			                </div>

			   	  		    <div class="col-md_12" >
						        <center>
						            <div class="col-md-5" style="padding-top: 15px;text-align: center;">
						                <a style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  class="btn btn-info btn_ordenes" @if($orden->seguro->tipo=='0') href="{{url('orden/descargar')}}/{{$orden->id}}" @else href="{{url('cotizador_p/orden/imprimir')}}/{{$orden->id}}"  @endif target="_blank" ><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Descargar Orden
						                </a>
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

<script type="text/javascript">

	//Funcion Editar Orden de Procedimiento Endoscopico
	@if($editar!='0')
		editar_orden_lab({{$editar}},'-1');
	@endif

    function editar_orden_lab(id_orden,estado){

    	if(estado=='-1'){
    		$.ajax({
				type: "GET",
				url: "{{url('hc4/orden/laboratorio/doctor/editar')}}/"+id_orden,
				data: "",
				datatype: "html",
				success: function(datahtml){
				   $("#xorden"+id_orden).html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});
    	}else{

    		alert("orden en Proceso no se puede modificar");

		}
    }

</script>
