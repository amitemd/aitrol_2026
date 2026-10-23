<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

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

<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>

	<script type="text/javascript">
		function buscar_nombre_medicina(div){
			///alert($("#nombre_generico"+div).val());
			//console.log($("#nombre_generico"+div).val());
	      	$.ajax({
		        type: 'post',
		        url:"{{route('buscar_nombre2.receta')}}",
		        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		        datatype: 'json',
		        data: $("#nombre_generico"+div),
	          
	            success: function(data){
	            	console.log(data);
	            	//alert('entra');
	            	
	            	if(data!='0'){
		              	if(data.dieta == 1 ){
		              		var dosis = data.dosis;
			                if(null == data.dosis){
			                  dosis = '';
			                }
			                anterior = tinyMCE.get("tprescripcion"+div).getContent();
			                tinyMCE.get("tprescripcion"+div).setContent(anterior+ data.value +': \n' +dosis);
			                $("#prescripcion"+div).val(tinyMCE.get("tprescripcion"+div).getContent());
			                  cambiar_receta_2();
			                  console.log("dieta"); 
		                }
		                if(data.dieta == 0){
		                  Crear_detalle(data, div);
		                  console.log("medicina"); 
		                }
	                  	$("#nombre_generico"+div).val(''); 
	                }               
	            },
	            error: function(data){
	            }
	      	});
		} 
		function cambiar_receta_2(div){
		    /*$.ajax({
		      type: 'post',
		      url:"{{route('update_receta_2.receta')}}",
		      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		      datatype: 'json',
		      data: $("#final_receta").serialize(),
		      success: function(data){
		        //alert("guardado")
		        //console.log(data);
		      },
		      error: function(data){
		        //console.log(data);
		      }
		    })*/
		}
		function Crear_detalle(med, div){
		    var js_cedula = document.getElementById("id_paciente").value;
		    var id = div.slice(0, -6);
		    $.ajax({
		        type: 'get',
		        url:"{{url('detalle_receta/detalle_crear')}}"+"/"+id+"/"+med.id+"/"+js_cedula, 
		        datatype: 'json',
		        success: function(data){
	          
		          if(data == 1){
		            if(med.genericos == null){
		              anterior2 = tinyMCE.get('trp'+div).getContent();
		                var keywords = ['cie10-receta'];
		                var resultado = "";
		                var pos = -1;
		                keywords.forEach(function(element){
		                pos = anterior2.search(element.toString());
		                  if(pos!=-1){
		                    resultado += " Palabra "+element+ "encontrada en la posición "+pos;
		                  }
		                });

		                //En caso de que no exista.
		                if(pos === -1 && resultado === ""){
		                    tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
		                    $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
		                }else{
		                    pos = pos-12;
		                    tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +':  ' +med.cantidad +anterior2.substr(pos));
		                    $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
		                }
		                //fin de receta
		                //anterior = $('#prescripcion').val();
		                anterior = tinyMCE.get('tprescripcion'+div).getContent();
		                //console.log(anterior);
		                //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
		                var dosis = med.dosis;
		                if(null == med.dosis){
		                  dosis = '';
		                }
		                tinyMCE.get('tprescripcion'+div).setContent(anterior +'\n'+ med.value +':  ' +dosis);

		                $('#prescripcion'+div).val(tinyMCE.get('tprescripcion'+div).getContent());
		                cambiar_receta_2(div); 
		            }else{
		                //anterior2 = $('#rp').val();
		                anterior2 = tinyMCE.get('trp'+div).getContent();
		                //codigo cie10 de posicion de receta
		                var keywords = ['cie10-receta'];
		                var resultado = "";
		                var pos = -1;

		                keywords.forEach(function(element) {
		                    //En caso de existir se asigna la posición en pos
		                    pos = anterior2.search(element.toString());
		                    //Si existe
		                    if(pos!=-1){
		                        resultado += " Palabra "+element+ "encontrada en la posición "+pos;
		                    }

		                });

		                //En caso de que no exista.
		                if(pos === -1 && resultado === ""){

		                  	tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);

		                    $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
		                }else{
		                    pos = pos-12;
		                    tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
		                    $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
		                }
		                //fin de receta cie10
		                //anterior = $('#prescripcion').val();
		                anterior = tinyMCE.get('tprescripcion'+div).getContent();
		                //$('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
		                var dosis = med.dosis;
		                if(null == med.dosis){
		                  dosis = '';
		                }
		                tinyMCE.get('tprescripcion'+div).setContent(anterior +'\n'+ med.value +':  ' +dosis);
		                $('#prescripcion'+div).val(tinyMCE.get('tprescripcion'+div).getContent());
		                cambiar_receta_2(div); 
		            }
		            }else{
		                $('#index'+div).empty().html(data);
		                //var contenido = data;
		                //var texto = contenido.replace(/<[^>]*>?/g,'');
		                //alert(texto);
		                //var texto = $(contenido).text();
		                //alert(texto);
		            }
		            //console.log(data);
		        },
		        error: function(data){
		             //console.log(data);
		        }
	        });
		}
	</script>
<input type="hidden" id="id_paciente" name="" value="{{$paciente->id}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />

<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
	<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
	  	<div class="row">
		  	<div class="col-8">
			    <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
	            	<img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png"> 
	                <b>CONSULTA</b>
				</h1> 
			</div>
		
			<div class="col-4" style="padding-left: 0px">
			    <div style="margin-bottom: 5px;text-align: left;">
					<a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white;" onclick="guardar_consulta('{{$paciente->id}}', 'no');"  >
		                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
			            	<div class="col-12" style="padding-left: 0px;padding-right: 0px;">
			            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
			            		<label style="font-size: 10px">AGREGAR CONSULTA</label>
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
    <div class="box-body" style="background-color: #56ABE3;">
  		<div class="col-12">
		  	<div class="row parent" >
		  		<span id="msn1" style="color: white; "></span>
		  		@foreach($procedimientos2 as $value)
			    	<div  class="col-12" id="consulta{{$value->hc_id_procedimiento}}">
			    		<div class="box @if(substr($value->fechaini,0,10) != date('Y-m-d'))  collapsed-box @endif " style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						  	<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                            <div class="row">
                               <div class="col-3">   

                                @php
									$evolucion = null;
									$agenda = null;
		                         	$evolucion = DB::table('hc_evolucion as e')->where('e.hcid',$value->hcid)->first();
		                         	//dd($evolucion);
		                         	$agenda = DB::table('agenda as a')->where('a.id', $value->id_agenda)->first();  
		                         	//dd($agenda);
		                         	//dd($value->id_doctor_examinador);
									if($value->id_doctor_examinador != ""){
		                         		$xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor_examinador)->first();
									}else{
										$xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor1)->first();
									}


		                        @endphp
		    					@if(!is_null($value->fecha_atencion))
			                        @php 
			                        $dia =  Date('N',strtotime($value->fecha_atencion)); 
			                        $mes =  Date('n',strtotime($value->fecha_atencion)); 
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
		                             	{{substr($value->fecha_atencion,8,2)}} de 
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
		                             	del {{substr($value->fecha_atencion,0,4)}}</b>
		                        @else 
		                         	@php 
			                        $dia =  Date('N',strtotime($agenda->fechaini)); 
			                        $mes =  Date('n',strtotime($agenda->fechaini)); 
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
		                             	{{substr($agenda->fechaini,8,2)}} de 
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
		                             	del {{substr($agenda->fechaini,0,4)}}</b>
		                        @endif

		                        </div>
		                        <div class="col-4">
		                        	<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Especialidad: </span> 
										<span style="font-size: 12px">   {{DB::table('especialidad')->find($agenda->espid)->nombre}} 
										</span>
							        </div>
		                        </div>
		                        <div class="col-4">
		                           <div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a):</span>
										<span style="font-size: 12px">
											{{$xdoctor->nombre1}} {{$xdoctor->apellido1}}
										</span>
							       </div>	
		                        </div>
		                        <div class="pull-right box-tools" style="padding-top: 4px;">
		                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            <i class="fa fa-plus"></i></button>
		                    	</div>
                              </div>
						  	</div>
						  	<div class="box-body" style="background: white;">
						  		<form id="frm_evol{{$value->hc_id_procedimiento}}">  
						  			<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
						  			<input type="hidden" name="id_hc_procedimiento" value="{{$value->hc_id_procedimiento}}">
								  	<div class="col-12" style="padding: 1px;">
				                        <div class="row">
					                        <div class="col-7">
						                        @php 
											  		$dia =  Date('N',strtotime($agenda->fechaini));
							                 		$mes =  Date('n',strtotime($agenda->fechaini)); 
							                 		//dd($agenda->fechaini);
							            		@endphp
					                        	<b>Fecha Visita: </b>
					                        	@if($agenda->proc_consul ==0 )
						                            @if($dia == '1') Lunes 
							                            @elseif($dia == '2') Martes 
							                            @elseif($dia == '3') Miércoles 
							                            @elseif($dia == '4') Jueves 
							                            @elseif($dia == '5') Viernes 
							                            @elseif($dia == '6') Sábado 
							                            @elseif($dia == '7') Domingo 
						                            @endif 
						                            	{{substr($agenda->fechaini,8,2)}} de 
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
					                            	del {{substr($agenda->fechaini,0,4)}} 
						                            <b><br>Hora: <input type="hidden" value="{{$agenda->fechaini}}" name="fecha_doctor"></b>{{substr($agenda->fechaini,10,10)}}@else
						                            <div style="border: 2px solid #004AC1; padding-top: 1px" class="input-group date datetimepicker2<?php echo e(date('his')); ?>" id="datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" data-target-input="nearest" >
									                    <input  class="form-control datetimepicker-input" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" value="@if(!is_null($evolucion->fecha_doctor)){{date('Y/m/d h:i', strtotime($evolucion->fecha_doctor))}}@else{{date('Y/m/d h:i', strtotime($agenda->fechaini))}}@endif"  name="fecha_doctor"/>
									                    <div class="input-group-append" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" data-toggle="datetimepicker" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" type="text" @endif>
									                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
									                    </div>
									                </div>
					                            @endif	
					                        </div>
					                        <div class="col-5" style="font-size: 12px">
				                                <b>
				                                @if($agenda->proc_consul=='1')
				                                    Tipo:PROCEDIMIENTO 
				                                    @if(!is_null($evolucion))
				                                        @php
				                                        $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);
					                                        if($procedimiento_evolucion != null){
					                                           if($procedimiento_evolucion->id_procedimiento_completo != null){
					                                            echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
					                                           }
					                                        }
				                                        @endphp
				                                    @endif
				                                @endif
				                                </b>
				                            </div>
				                        </div>
				                    </div>
		                    		<div class="col-12" style="padding: 1px;">
		                    		<div class="row">
			                               <div class="col-8"><h6><b>Datos Generales</b></h6></div>
			                               
			                               </div> 
			                               <div class="col-12">
				                               <div class="row">
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_doctor_examinador" class="control-label" style="font-size: 12px">Medico Examinador @if(substr($value->fechaini,0,10) != date('Y-m-d')) @endif</label>
					                                    <select class="form-control input-sm" style="width: 100%; font-size: 12px; border: 2px solid #004AC1;" name="id_doctor_examinador" id="id_doctor_examinador{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif  > 
					                                        @foreach($doctores as $doc)
				                                                <option @if($value->id_doctor_examinador == $doc->id) selected @elseif($value->id_doctor_examinador == "" && $doc->id == $value->id_doctor1) selected @endif value="{{$doc->id}}" >{{$doc->apellido1}} @if($doc->apellido2 != "(N/A)"){{ $doc->apellido2}}@endif {{ $doc->nombre1}} @if($doc->nombre2 != "(N/A)"){{ $doc->nombre2}}@endif</option>
				                                            @endforeach
					                                    </select>
					                                </div>
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_seguro" class="control-label" style="font-size: 12px">Seguro</label>
					                                    <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="id_seguro" id="id_seguro{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif> 
					                                        @foreach($seguros as $seg)
				                                                <option @if($value->id_seguro == $seg->id) selected @endif value="{{$seg->id}}" >{{$seg->nombre}}</option>
				                                            @endforeach
					                                    </select>
					                                </div>
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_seguro" class="control-label" style="font-size: 12px">Cortesia</label>
					                                    <select id="consulta_cortesia_paciente{{$value->hc_id_procedimiento}}" name="consulta_cortesia_paciente" class="form-control input-sm" required style="background-color: #ccffcc; font-size: 11px; border: 2px solid #004AC1;">
					                                    @if(!is_null($value->cortesia))
									                      	<option @if($value->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
									                      	<option @if($value->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
									                    @else
									                    	<option value="NO" selected >NO</option>
									                    	<option value="SI" >SI</option>
									                    @endif
									                    </select>
					                                </div>
					                                <div class="col-md-3 col-6 has-error" style="padding: 1px;">
					                                    <label for="observaciones" class="control-label" style="font-size: 12px">Observaciones</label>
					                                    <textarea class="form-control input-sm" id="observaciones{{$value->hc_id_procedimiento}}" name="observaciones" style="width: 100%;background-color: #ffffb3; border: 2px solid #004AC1;" rows="1" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >{{strip_tags($value->observaciones)}}</textarea>
					                                </div> 
				                                </div>
			                                </div>
			                		</div>
				                    <div class="col-12">
				                    	<input type="hidden" name="id_agenda" value="{{$value->id_agenda}}">
				                    	<input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
		                            	<input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
					                    <div class="row">
						                	<div class="col-12">
						                        <h6><b>Preparación</b></h6>
						                        <div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="presion" class="control-label" style="font-size: 12px">P. Arterial</label>
						                                <input class="form-control input-sm" name="presion" id="pre{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->presion}}" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="pulso" class="control-label" style="font-size: 12px">Pulso</label>
						                                <input class="form-control input-sm" name="pulso" id="pul{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->pulso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="temperatura" class="control-label" style="font-size: 12px">Temperatura (ºC)</label>
						                                <input class="form-control input-sm" name="temperatura" id="tem{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->temperatura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="o2" class="control-label" style="font-size: 12px">SaO2:</label>
						                                <input class="form-control input-sm" name="o2" id="sao{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->o2}}" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                        </div>
						                        <div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="estatura" class="control-label" style="font-size: 12px">Estatura (cm)</label>
						                                <input class="form-control input-sm" id="estatura{{$value->hc_id_procedimiento}}" name="estatura" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->altura}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($agenda->estado_cita!='4') readonly="yes" @endif  @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="peso" class="control-label" style="font-size: 12px">Peso (kg)</label>
						                                <input class="form-control input-sm" id="peso{{$value->hc_id_procedimiento}}" name="peso" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->peso}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="perimetro" class="control-label" style="font-size: 12px">Perimetro Abdominal</label>
						                                <input class="form-control input-sm" id="perimetro{{$value->hc_id_procedimiento}}" name="perimetro" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->perimetro}}" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;" >
						                                <label for="peso_ideal" class="control-label" style="font-size: 12px">Peso Ideal (kg)</label>
						                                <input class="form-control input-sm" id="peso_ideal{{$value->hc_id_procedimiento}}" name="peso_ideal" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                        </div>
						                        <div class="row">
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="gct" class="control-label" style="font-size: 12px">% GCT RECOMENDADO</label>
						                                <input class="form-control input-sm" id="gct{{$value->hc_id_procedimiento}}" name="gct" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="imc" class="control-label" style="font-size: 12px">IMC</label>
						                                <input class="form-control input-sm" id="imc{{$value->hc_id_procedimiento}}" name="imc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="cimc" class="control-label" style="font-size: 12px">Categoria IMC</label>
						                                <input class="form-control input-sm" id="cimc{{$value->hc_id_procedimiento}}" name="cimc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >
						                            </div>
						                        </div>
						                        <h6><b>Clasificación Child Pugh</b></h6>
						                        <?php
						                        	$idusuario = Auth::user()->id;
				        							$ip_cliente= $_SERVER["REMOTE_ADDR"];

						                        	$child_pugh = null;
						                        	$child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

						                        	//dd($child_pugh);
						                        	//dd($evolucion->id);
						                        	//dd($agenda->estado_cita);
						                        	if(is_null($child_pugh)){

				                    					if($agenda->estado_cita=='4'){

									                        $input_child_pugh = [
									                            'id_hc_evolucion' => $evolucion->id,
									                            'ip_modificacion' => $ip_cliente,
									                            'id_usuariomod' => $idusuario,                    
									                            'id_usuariocrea' => $idusuario,
									                            'examen_fisico' => 'ESTADO CABEZA Y CUELLO:
ESTADO TORAX: 
ESTADO ABDOMEN: 
ESTADO MIEMBROS SUPERIORES: 
ESTADO MIEMBROS INFERIORES: 
OTROS: ',
									                            'ip_creacion' => $ip_cliente,
									                            'created_at' => date('Y-m-d H:i:s'),
									                            'updated_at' => date('Y-m-d H:i:s'),
									                        ]; 
									                        \Sis_medico\hc_child_pugh::insert($input_child_pugh);
									                        
									                        $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
									                    }
									                }
									                //dd($child_pugh);
									                ?>
									            <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id}}">
						                        <div class="row">
						                            <!--<input type="hidden" name="id_child_pugh" value="">-->
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="ascitis" class="control-label" style="font-size: 12px">Ascitis</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="ascitis" id="ascitis{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});" >
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 1) selected @endif @endif value="1" >Ausente</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 2) selected @endif @endif value="2" >Leve</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 3) selected @endif @endif value="3" >Moderada</option>
				                                        	
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="encefalopatia" class="control-label" style="font-size: 12px">Encefalopatia</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="encefalopatia" id="encefalopatia{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 1) selected @endif @endif value="1" >No</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 2) selected @endif @endif value="2" >Grado 1 a 2</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 3) selected @endif @endif value="3" >Grado 3 a 4</option>
				                                        	
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="albumina" class="control-label" style="font-size: 12px">Albúmina(g/l)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="albumina" id="albumina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 1) selected @endif @endif value="1" >&gt; 3.5</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 2) selected @endif @endif value="2" >2.8 - 3.5</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 3) selected @endif @endif value="3" >&lt; 2.8</option>
					                                        
						                                </select>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="bilirrubina" class="control-label" style="font-size: 12px">Bilirrubina(mg/dl)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="bilirrubina" id="bilirrubina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 1) selected @endif @endif value="1" >&lt; 2</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 2) selected @endif @endif value="2" >2 - 3</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 3) selected @endif @endif value="3" >&gt; 3</option>
					                                        
						                                </select>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="inr" class="control-label" style="font-size: 12px">Protrombina% (INR)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="inr" id="inr{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 1) selected @endif @endif value="1" >&gt; 50 (&lt; 1.7)</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->inr == 2) selected @endif @endif value="2" >30 - 50 (1.8 - 2.3)</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->inr == 3) selected @endif @endif value="3" >&lt; 30 (&gt; 2.3)</option>
				                                        
						                                </select>
						                            </div>
						                    	</div>
						                    	<div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="puntaje" class="control-label" style="font-size: 12px">Puntaje</label>
						                                <input class="form-control input-sm" id="puntaje{{$value->hc_id_procedimiento}}" name="puntaje" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes" >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="clase" class="control-label" style="font-size: 12px">Clase</label>
						                                <input class="form-control input-sm" id="clase{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="sv1" class="control-label" style="font-size: 12px">SV1 Año:</label>
						                                <input class="form-control input-sm" id="sv1{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="sv2" class="control-label" style="font-size: 12px">SV2 años:</label>
						                                <input class="form-control input-sm" id="sv2{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes">
						                            </div>
						                        </div>
						                    </div>  
										</div>
									</div>
		                            <div class="col-12" style="padding: 1px;">
		                                <label for="motivo" class="control-label" style="font-size: 14px"><b>Motivo</b></label>
		                                <textarea name="motivo" id="motivo{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif > @if(!is_null($value)){{$value->motivo}}@endif </textarea>
		                            </div>

		                          	<div class="col-12" style="padding: 1px;">
		                                <label for="thistoria_clinica" class="control-label" style="font-size: 14px"><b>Evolución</b></label>
		                                <div id="thistoria_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->cuadro_clinico ?>@endif</div>
		                                <input type="hidden" name="historia_clinica" id="historia_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>"  >
		                            </div>

		                            <div class="col-12" style="padding: 1px;">
		                                <label for="tresultado_exam" class="control-label" style="font-size: 14px"><b>Resultados de Exámenes y Procedimientos Diagnósticos</b></label>
		                                <div id="tresultado_exam<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->resultado ?>@endif</div>
		                                <input type="hidden" name="resultado_exam" id="resultado_exam<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" >
		                            </div>
								

		                            <div class="col-12" style="padding: 1px;">
		                                <label for="examen_fisico" class="control-label" style="font-size: 14px"><b>Examen Fisico</b></label>
		                                <textarea id="examen_fisico{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" name="examen_fisico" style="width: 100%; border: 2px solid #004AC1;" rows="7"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif > @if(!is_null($child_pugh)){{strip_tags($child_pugh->examen_fisico)}}@endif </textarea>
		                            </div>                    
		                           	@if($agenda->espid=='8')
			                            @php
			                                $cardiologia = DB::table('hc_cardio')->where('hcid',$value->hcid)->first(); 
			                            @endphp  
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="resumen" class="control-label"><b>Resumen</b></label>
			                                <textarea id="resumen{{$value->hc_id_procedimiento}}" name="resumen" style="width: 100%; border: 2px solid #004AC1;" rows="1"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
			                            </div>
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="plan_diagnostico" class="control-label"><b>Plan Diagnóstico</b></label>
			                                <textarea id="plan_diagnostico{{$value->hc_id_procedimiento}}" name="plan_diagnostico" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
			                            </div>
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="plan_tratamiento" class="control-label"><b>Plan Tratamiento</b></label>
			                                <textarea id="plan_tratamiento{{$value->hc_id_procedimiento}}" name="plan_tratamiento" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
			                            </div>
		                           	@endif
		                            <input type="hidden" name="codigo" id="codigo{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>">
		                      		<label for="cie10" class="col-12 control-label" style="padding-left: 0px; @if($agenda->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b>
			                        </label>
		                      		<div class="row">
			                            <div class="form-group col-md-6 col-sm-6 col-12" style="padding: 15px; @if($agenda->proc_consul=='1') display: none; @endif">
			                                <input id="cie10{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase; border: 2px solid #004AC1;" onkeyup="javascript:this.value=this.value.toUpperCase(); " required placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
			                            </div>

			                             <div class="form-group col-md-3 col-sm-6 col-6" style=" padding: 15px; @if($agenda->proc_consul=='1') display: none; @endif">
						                    <select id="pre_def{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" name="pre_def" class="form-control input-sm" >
						                        <option value="">Seleccione ...</option>
						                        <option value="PRESUNTIVO">PRESUNTIVO</option>
						                        <option value="DEFINITIVO">DEFINITIVO</option>   
						                    </select> 
						                </div>
			                            <div class="col-md-3 col-sm-12 col-6" >
			                                <center>
			                            		<div class="col-md-12 col-sm-6 col-12" style="padding: 15px; ">
					                            	@if($agenda->estado_cita=='4') 
					                            	<button id="bagregar{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" class="btn btn_agregar_diag btn-sm col-10" style=" color: white; @if($agenda->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span>
					                            	</button>
					                            	@endif
					                            </div>
			                            	</center>
			                            </div>
		                            </div>
		                            <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
		                                <table id="tdiagnostico{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" class="table table-striped" style="font-size: 12px;">
		                                    
		                                </table>
		                            </div>
		                            <div class="col-12" style="padding: 1px;">
		                                <label for="examenes_realizar" class="control-label" style="font-size: 14px"><b>Examenes a Realizar</b></label>
		                                <textarea id="examenes_realizar{{$value->hc_id_procedimiento}}" name="examenes_realizar" style="width: 100%; border: 2px solid #004AC1;" rows="2"  @if($agenda->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})" @endif >@if(!is_null($value)){{$value->examenes_realizar}}@endif</textarea>
		                            </div>
<!-- RECETA-->
									@php

										$rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
										if(is_null($rec)){
											$ip_cliente= $_SERVER["REMOTE_ADDR"];
											$input_hc_receta = [
								                'id_hc' => $value->hcid,
								                'ip_creacion' => $ip_cliente,
								                'id_usuariocrea' => '9666666666',
								                'ip_modificacion' => $ip_cliente,
								                'id_usuariomod' => '9666666666',
								                'created_at' => date('Y-m-d H:i:s'),
								                'updated_at' => date('Y-m-d H:i:s'),
								            ]; 
								           $id_receta = \Sis_medico\hc_receta::insertGetId($input_hc_receta);
										}else{
											$id_receta = $rec->id;
										}
										

									@endphp
									<input type="hidden" name="id_receta" value="{{$id_receta}}">
									
									<div class="col-md-12" style="padding-left: 90px" >
				                        <div class="row">
				                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">                      
				                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '2']) }}"> 
				                              <div class="col-md-12" style="text-align: center; ">
				                                <div class="row" style="padding-left: 0px; padding-right: 0px;">
				                                  <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
				                                    <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
				                                  </div>
				                                  <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
				                                    <label style="font-size: 14px; ">Imprimir Membretada</label>
				                                  </div>  
				                                </div>
				                              </div>
				                              </a>
				                            </div>
				                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
				                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '1']) }}"> 
				                                <div class="col-md-12" style="text-align: center">
				                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
				                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
				                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
				                                    </div>
				                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
				                                      <label style="font-size: 14px">Imprimir</label>
				                                    </div>  
				                                  </div>
				                                </div>
				                              </a>
				                            </div>
				                        </div>
		                        	</div>
 					


			                        <div class="col-md-11 col-sm-11 col-11" style="margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;">
			                          <!--Contenedor Historial de Recetas-->
				                        <div  style=" color: white; font-family: 'Helvetica general'; font-size: 16px; ">
				                            <div class="box-title" style=" margin-left: 10px">
				                          	<div class="row">
					                            <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">
					                        
					                            </div> 
					                            <div class="col-12">
									                <div class="row">
									                  <div class="col-12">
									                  
									                    <div class="form-group">
									                      <label style="font-family: 'Helvetica general';" for="inputid" class="control-label">Medicina</label>
									                      <div class="row"> 
									                        <div class=" col-md-9 col-sm-9 col-12">
									                          <input style="margin-bottom: 10px" value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico{{$id_receta}}{{date('his')}}" placeholder="Nombre">
									                        </div>
									                          
									                           <button id="limpiar_medicina" class="btn btn-primary col-md-2 col-sm-2 col-12" style="background-color: #004AC1; width: 100%; height: 100%"
									                            onClick="buscar_nombre_medicina('{{$id_receta}}{{date('his')}}')">
									                            <span class="fa fa-plus"></span> Agregar
									                           </button>
									                        
									                      </div>
									                    </div>
									                  
									                  </div> 
									                </div>
					          					</div>
					          					<div id="index{{$id_receta}}{{date('his')}}"> 

					          					</div>
					          					<div style="font-family: 'Helvetica general'; color: black" class="col-md-2">Alergias:</div>
									            <div class="col-md-10">
									              @if($alergiasxpac->count()==0) 
									               <b>NO TIENE </b>
									              @else 
									                @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
									                @endforeach 
									              @endif
									            </div>
					                        </div>
				                            </div>
				                        </div>

				                        <div class="contenedor2" id="receta{{$id_receta}}{{date('his')}}" style="padding-bottom: 20px; padding-right: 15px">
				                            <div class="col-md-12">
						                        <div class="row">
						                            <div class="col-md-6">
						                              	<span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
						                                <div id="trp{{$id_receta}}{{date('his')}}" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
							                              <?php if(!is_null($rec)): ?>
							                                <?php echo $rec->rp ?>
							                              <?php endif; ?>
							                            </div>

                                  						<input type="hidden" name="rp" id="rp{{$id_receta}}{{date('his')}}">
						                            </div>
						                            <div class="col-md-6" >
						                              	<span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
						                                <div id="tprescripcion<?php echo e($id_receta); ?>{{date('his')}}"  style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
						                                	<?php if(!is_null($rec)): ?>        
						                                  	<?php echo $rec->prescripcion ?>
						                                	<?php endif; ?>                              
						                              	</div> 

                                    					<input type="hidden" name="prescripcion" id="prescripcion{{$id_receta}}{{date('his')}}">
						                            </div>
						                        </div>
				                            </div>
				                        </div>
			                        </div>

		                        

			                        <script type="text/javascript">
			                        	tinymce.init({
									    selector: '#tprescripcion{{$id_receta}}{{date('his')}}',
									    inline: true,
									    menubar: false,
									    content_style: ".mce-content-body {font-size:14px;}",
									    //readonly: 1,
									      
									      setup: function (editor){
									            editor.on('init', function (e){
									               var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{date('his')}}');
									                $("#prescripcion<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
									            });
									      },
									       
									      init_instance_callback: function (editor){
									            editor.on('Change', function (e) {
									                var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{date('his')}}');
									                $("#prescripcion<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
									                cambiar_receta_2("{{$id_receta}}{{date('his')}}"); 
									                @if(substr($value->fechaini,0,10) == date('Y-m-d'))
										                guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}}) 
										            @endif
									              
									            });
									      }
									  });


			                        	tinymce.init({
									    selector: '#trp<?php echo e($id_receta); ?>{{date('his')}}',
									    inline: true,
									    menubar: false,
									    content_style: ".mce-content-body {font-size:14px;}",
									    //readonly: 1,
									        
									      setup: function (editor){
									          editor.on('init', function (e) {
									             var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{date('his')}}');
									              $("#rp<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
									          });
									      },
									      
									      init_instance_callback: function (editor){
									          editor.on('Change', function (e) {
									              var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{date('his')}}');
									              $("#rp<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
									              cambiar_receta_2("{{$id_receta}}{{date('his')}}"); 
										            @if(substr($value->fechaini,0,10) == date('Y-m-d'))
										                guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}}) 
										            @endif
									          }); 
									      }
									  });

			                        	  $("#nombre_generico<?php echo e($id_receta); ?>{{date('his')}}").autocomplete({
										    source: function( request, response ) {
										      $.ajax({
										        url:"{{route('buscar_nombre.receta')}}",
										        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
										        data: {
										            term: request.term,
										            seguro: {{$value->id_seguro}}
										              },
										              dataType: "json",
										              type: 'post',
										              success: function(data){
										                response(data);
										              }
										            })
										        },
										    minLength:2,
										  });

			                        


								    

										$("#prescripcion<?php echo e($id_receta); ?>{{date('his')}}").change( function(){
										    cambiar_receta_2("{{$id_receta}}{{date('his')}}");
										});
										  
										$("#rp<?php echo e($id_receta); ?>{{date('his')}}").change( function(){
										    cambiar_receta_2("{{$id_receta}}{{date('his')}}");
										});
			                        </script>
		                            <div class="col-12">
			                            <center>
				                            <div class="col-4">
			                               		<button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})"  ><span class="fa fa-floppy-o"></span>&nbsp;Guardar
		    									</button>
			                               </div>
			                            </center>
	                                </div>
                        		</form>
						  	</div>
						</div>
			    	</div>

			   	@endforeach


			   	@foreach($procedimientos1 as $value)


			    	<div  class="col-12" id="consulta{{$value->hc_id_procedimiento}}">
			    		<div class="box @if(substr($value->fechaini,0,10) != date('Y-m-d'))  collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						  	<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
						  	    <div class="row">
						  		    <div class="col-3">  
										@php
											$evolucion = null;
											$agenda = null;
				                         	$evolucion = DB::table('hc_evolucion as e')->where('e.hcid',$value->hcid)->first();
				                         	//dd($evolucion);
				                         	$agenda = DB::table('agenda as a')->where('a.id', $value->id_agenda)->first();  
				                         	//dd($agenda); 

				                         	$xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor1)->first();  
				                         	//dd($xdoctor); 

		                                @endphp
				    					@if(!is_null($value->fecha_atencion))
					                        @php 
					                        $dia =  Date('N',strtotime($value->fecha_atencion)); 
					                        $mes =  Date('n',strtotime($value->fecha_atencion)); 
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
				                             	{{substr($value->fecha_atencion,8,2)}} de 
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
				                             	del {{substr($value->fecha_atencion,0,4)}}</b>
				                        @else 
				                         	@php 
					                        $dia =  Date('N',strtotime($agenda->fechaini)); 
					                        $mes =  Date('n',strtotime($agenda->fechaini)); 
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
				                             	{{substr($agenda->fechaini,8,2)}} de 
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
				                             	del {{substr($agenda->fechaini,0,4)}}</b>
				                        @endif
		                            </div>
		                            <div class="col-4">
			                        	<div>
											<span style="font-family: 'Helvetica general'; font-size: 12px">Especialidad: </span> 
											<span style="font-size: 12px">   {{DB::table('especialidad')->find($agenda->espid)->nombre}} 
											</span>
								        </div>
			                        </div>
		                            <div class="col-4">
			                           <div>
											<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a):</span>
											<span style="font-size: 12px">
												{{$xdoctor->nombre1}} {{$xdoctor->apellido1}}
											</span>
								       </div>	
		                            </div>
			                        <div class="pull-right box-tools">
			                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
			                            <i class="fa fa-plus"></i></button>
			                    	</div>
		                    	</div>
						  	</div>
						  	
						  	<div class="box-body" style="background: white;">
						  		<form id="frm_evol{{$value->hc_id_procedimiento}}">  
						  	 		<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
						  			<input type="hidden" name="id_hc_procedimiento" value="{{$value->hc_id_procedimiento}}">
								  	<div class="col-12" style="padding: 1px;">
				                        <div class="row">
					                        <div class="col-7">
					                        @php 
										  		$dia =  Date('N',strtotime($agenda->fechaini));
						                 		$mes =  Date('n',strtotime($agenda->fechaini)); 
						                 		//dd($agenda->fechaini);
						            		@endphp
					                        	<b>Fecha Visita: </b>
					                        	@if($agenda->proc_consul ==0 )
						                            @if($dia == '1') Lunes 
							                            @elseif($dia == '2') Martes 
							                            @elseif($dia == '3') Miércoles 
							                            @elseif($dia == '4') Jueves 
							                            @elseif($dia == '5') Viernes 
							                            @elseif($dia == '6') Sábado 
							                            @elseif($dia == '7') Domingo 
						                            @endif 
						                            	{{substr($agenda->fechaini,8,2)}} de 
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
					                            	del {{substr($agenda->fechaini,0,4)}} 
						                            <b><br>Hora: <input type="hidden" value="{{$agenda->fechaini}}" name="fecha_doctor"></b>{{substr($agenda->fechaini,10,10)}}@else
						                            <div style="border: 2px solid #004AC1; padding-top: 1px" class="input-group date datetimepicker1<?php echo e(date('his')); ?>" id="datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" data-target-input="nearest" >
									                    <input  type="text" class="form-control datetimepicker-input" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" value="@if(!is_null($evolucion->fecha_doctor)){{date('Y/m/d h:i', strtotime($evolucion->fecha_doctor))}}@else{{date('Y/m/d h:i', strtotime($agenda->fechaini))}}@endif"  name="fecha_doctor"/>
									                    <div class="input-group-append" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" data-toggle="datetimepicker">
									                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
									                    </div>
									                </div>
					                            @endif	
					                        </div>
					                        <div class="col-5" style="font-size: 12px">
				                                <b>
				                                    @if($agenda->proc_consul=='1')
				                                	     Tipo: PROCEDIMIENTO 
				                                	    @if(!is_null($evolucion))
						                                    @php
						                                    $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);
						                                    if($procedimiento_evolucion != null){
						                                        if($procedimiento_evolucion->id_procedimiento_completo != null){
						                                            echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
						                                        }
						                                    }
						                                    @endphp
				                                        @endif
				                                    @endif
				                                </b>
				                            </div>
				                        </div>
				                    </div>
		                    		<div class="col-12" style="padding: 1px;">
		                    		<div class="row">
			                               <div class="col-8"><h6><b>Datos Generales</b></h6></div>
			                               
			                               </div> 
			                               <div class="col-12">
				                               <div class="row">
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_doctor_examinador" class="control-label" style="font-size: 12px">Medico Examinador</label>
					                                    <select   class="form-control input-sm" style="width: 100%; font-size: 12px; border: 2px solid #004AC1;" name="id_doctor_examinador" id="id_doctor_examinador{{$value->hc_id_procedimiento}}">
					                                        @foreach($doctores as $doc)
				                                                <option @if($value->id_doctor_examinador == $doc->id) selected @endif value="{{$doc->id}}" >{{$doc->apellido1}} @if($doc->apellido2 != "(N/A)"){{ $doc->apellido2}}@endif {{ $doc->nombre1}} @if($doc->nombre2 != "(N/A)"){{ $doc->nombre2}}@endif</option>
				                                            @endforeach
					                                    </select>
					                                </div>
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_seguro" class="control-label" style="font-size: 12px">Seguro</label>
					                                    <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="id_seguro" id="id_seguro{{$value->hc_id_procedimiento}}"> 
					                                        @foreach($seguros as $seg)
				                                                <option @if($value->id_seguro == $seg->id) selected @endif value="{{$seg->id}}" >{{$seg->nombre}}</option>
				                                            @endforeach
					                                    </select>
					                                </div>
					                                <div class="col-md-3 col-6" style="padding: 1px;">
					                                    <label for="id_seguro" class="control-label" style="font-size: 12px">Cortesia</label>
					                                    <select id="consulta_cortesia_paciente{{$value->hc_id_procedimiento}}" name="consulta_cortesia_paciente" class="form-control input-sm" required style="background-color: #ccffcc; font-size: 11px; border: 2px solid #004AC1;">
					                                    @if(!is_null($value->cortesia))
									                      	<option @if($value->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
									                      	<option @if($value->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
									                    @else
									                    	<option value="NO" selected >NO</option>
									                    	<option value="SI" >SI</option>
									                    @endif
									                    </select>
					                                </div>
					                                <div class="col-md-3 col-6 has-error" style="padding: 1px;">
					                                    <label for="observaciones" class="control-label" style="font-size: 12px">Observaciones</label>
					                                    <textarea class="form-control input-sm" id="observaciones{{$value->hc_id_procedimiento}}" name="observaciones" style="width: 100%;background-color: #ffffb3; border: 2px solid #004AC1;" rows="1" >{{strip_tags($value->observaciones)}}</textarea>
					                                </div> 
				                                </div>
			                                </div>
			                		</div>

				                    <div class="col-12">
				                    	<input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
		                            	<input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
					                    <div class="row">
						                	<div class="col-12">
						                        <h6><b>Preparación</b></h6>
						                        <div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="presion" class="control-label" style="font-size: 12px">P. Arterial</label>
						                                <input class="form-control input-sm" name="presion" id="pre{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->presion}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="pulso" class="control-label" style="font-size: 12px">Pulso</label>
						                                <input class="form-control input-sm" name="pulso" id="pul{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->pulso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="temperatura" class="control-label" style="font-size: 12px">Temperatura (ºC)</label>
						                                <input class="form-control input-sm" name="temperatura" id="tem{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->temperatura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="o2" class="control-label" style="font-size: 12px">SaO2:</label>
						                                <input class="form-control input-sm" name="o2" id="sao{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->o2}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                        </div>
						                        <div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="estatura" class="control-label" style="font-size: 12px">Estatura (cm)</label>
						                                <input class="form-control input-sm" id="estatura{{$value->hc_id_procedimiento}}" name="estatura" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->altura}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($agenda->estado_cita!='4') readonly="yes" @endif >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="peso" class="control-label" style="font-size: 12px">Peso (kg)</label>
						                                <input class="form-control input-sm" id="peso{{$value->hc_id_procedimiento}}" name="peso" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->peso}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="perimetro" class="control-label" style="font-size: 12px">Perimetro Abdominal</label>
						                                <input class="form-control input-sm" id="perimetro{{$value->hc_id_procedimiento}}" name="perimetro" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->perimetro}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;" >
						                                <label for="peso_ideal" class="control-label" style="font-size: 12px">Peso Ideal (kg)</label>
						                                <input class="form-control input-sm" id="peso_ideal{{$value->hc_id_procedimiento}}" name="peso_ideal" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                        </div>
						                        <div class="row">
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="gct" class="control-label" style="font-size: 12px">% GCT RECOMENDADO</label>
						                                <input class="form-control input-sm" id="gct{{$value->hc_id_procedimiento}}" name="gct" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="imc" class="control-label" style="font-size: 12px">IMC</label>
						                                <input class="form-control input-sm" id="imc{{$value->hc_id_procedimiento}}" name="imc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                            <div class="col-md-4 col-6" style="padding: 1px;">
						                                <label for="cimc" class="control-label" style="font-size: 12px">Categoria IMC</label>
						                                <input class="form-control input-sm" id="cimc{{$value->hc_id_procedimiento}}" name="cimc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($agenda->estado_cita!='4') readonly="yes" @endif>
						                            </div>
						                        </div>
						                        <h6><b>Clasificación Child Pugh</b></h6>
						                        <?php
						                        	$idusuario = Auth::user()->id;
				        							$ip_cliente= $_SERVER["REMOTE_ADDR"];

						                        	$child_pugh = null;
						                        	$child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

						                        	//dd($child_pugh);
						                        	//dd($evolucion->id);
						                        	//dd($agenda->estado_cita);
						                        	if(is_null($child_pugh)){

				                    					if($agenda->estado_cita=='4'){

									                        $input_child_pugh = [
									                            'id_hc_evolucion' => $evolucion->id,
									                            'ip_modificacion' => $ip_cliente,
									                            'id_usuariomod' => $idusuario,                    
									                            'id_usuariocrea' => $idusuario,
									                            'examen_fisico' => 'ESTADO CABEZA Y CUELLO:
ESTADO TORAX: 
ESTADO ABDOMEN: 
ESTADO MIEMBROS SUPERIORES: 
ESTADO MIEMBROS INFERIORES: 
OTROS: ',
									                            'ip_creacion' => $ip_cliente,
									                            'created_at' => date('Y-m-d H:i:s'),
									                            'updated_at' => date('Y-m-d H:i:s'),
									                        ]; 
									                        \Sis_medico\hc_child_pugh::insert($input_child_pugh);
									                        
									                        $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
									                    }
									                }
									                //dd($child_pugh);
									                ?>
									            <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id}}">
						                        <div class="row">
						                            <!--<input type="hidden" name="id_child_pugh" value="">-->
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="ascitis" class="control-label" style="font-size: 12px">Ascitis</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="ascitis" id="ascitis{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});">
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 1) selected @endif @endif value="1" >Ausente</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 2) selected @endif @endif value="2" >Leve</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 3) selected @endif @endif value="3" >Moderada</option>
				                                        	
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="encefalopatia" class="control-label" style="font-size: 12px">Encefalopatia</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="encefalopatia" id="encefalopatia{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 1) selected @endif @endif value="1" >No</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 2) selected @endif @endif value="2" >Grado 1 a 2</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 3) selected @endif @endif value="3" >Grado 3 a 4</option>
				                                        	
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="albumina" class="control-label" style="font-size: 12px">Albúmina(g/l)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="albumina" id="albumina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 1) selected @endif @endif value="1" >&gt; 3.5</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 2) selected @endif @endif value="2" >2.8 - 3.5</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 3) selected @endif @endif value="3" >&lt; 2.8</option>
					                                        
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="bilirrubina" class="control-label" style="font-size: 12px">Bilirrubina(mg/dl)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="bilirrubina" id="bilirrubina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                	
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 1) selected @endif @endif value="1" >&lt; 2</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 2) selected @endif @endif value="2" >2 - 3</option>
					                                        <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 3) selected @endif @endif value="3" >&gt; 3</option>
					                                        
						                                </select>
						                            </div>
						                            <div class="col-md-2 col-6" style="padding: 1px;">
						                                <label for="inr" class="control-label" style="font-size: 12px">Protrombina% (INR)</label>
						                                <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="inr" id="inr{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"> 
						                                
						                                    <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 1) selected @endif @endif value="1" >&gt; 50 (&lt; 1.7)</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->inr == 2) selected @endif @endif value="2" >30 - 50 (1.8 - 2.3)</option>
				                                        	<option @if(!is_null($child_pugh)) @if($child_pugh->inr == 3) selected @endif @endif value="3" >&lt; 30 (&gt; 2.3)</option>
				                                        
						                                </select>
						                            </div>
						                    	</div>
						                    	<div class="row">
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="puntaje" class="control-label" style="font-size: 12px">Puntaje</label>
						                                <input class="form-control input-sm" id="puntaje{{$value->hc_id_procedimiento}}" name="puntaje" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes" >
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="clase" class="control-label" style="font-size: 12px">Clase</label>
						                                <input class="form-control input-sm" id="clase{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="sv1" class="control-label" style="font-size: 12px">SV1 Año:</label>
						                                <input class="form-control input-sm" id="sv1{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
						                            </div>
						                            <div class="col-md-3 col-6" style="padding: 1px;">
						                                <label for="sv2" class="control-label" style="font-size: 12px">SV2 años:</label>
						                                <input class="form-control input-sm" id="sv2{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes">
						                            </div>
						                        </div>
						                    </div>  
										</div>
									</div>


		                            <div class="col-12" style="padding: 1px;">
		                                <label for="motivo" class="control-label" style="font-size: 14px"><b>Motivo</b></label>
		                                <textarea name="motivo" id="motivo{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"  @if($agenda->estado_cita!='4') readonly="yes" @endif> @if(!is_null($value)){{$value->motivo}}@endif </textarea>
		                            </div>

		                          	 <div class="col-12" style="padding: 1px;">
		                                <label for="thistoria_clinica" class="control-label" style="font-size: 14px"><b>Evolución</b></label>
		                                <div id="thistoria_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->cuadro_clinico ?>@endif</div>
		                                <input type="hidden" name="historia_clinica" id="historia_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" >
		                            </div>

		                             <div class="col-12" style="padding: 1px;">
		                                <label for="tresultado_exam" class="control-label" style="font-size: 14px"><b>Resultados de Exámenes y Procedimientos Diagnósticos</b></label>
		                                <div id="tresultado_exam<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->resultado ?>@endif</div>
		                                <input type="hidden" name="resultado_exam" id="resultado_exam<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>" >
		                            </div>
		                            
		                            <div class="col-12" style="padding: 1px;">
		                                <label for="examen_fisico" class="control-label" style="font-size: 14px"><b>Examen Fisico</b></label>
		                                <textarea id="examen_fisico{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" name="examen_fisico" style="width: 100%; border: 2px solid #004AC1;" rows="7"  @if($agenda->estado_cita!='4') readonly="yes" @endif> @if(!is_null($child_pugh)){{strip_tags($child_pugh->examen_fisico)}}@endif </textarea>
		                            </div>                    
		                           	@if($agenda->espid=='8')
			                            @php
			                                $cardiologia = DB::table('hc_cardio')->where('hcid',$value->hcid)->first(); 
			                            @endphp  
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="resumen" class="control-label"><b>Resumen</b></label>
			                                <textarea id="resumen{{$value->hc_id_procedimiento}}" name="resumen" style="width: 100%; border: 2px solid #004AC1;" rows="1"  @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
			                            </div>
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="plan_diagnostico" class="control-label"><b>Plan Diagnóstico</b></label>
			                                <textarea id="plan_diagnostico{{$value->hc_id_procedimiento}}" name="plan_diagnostico" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
			                            </div>
			                            <div class="col-12" style="padding: 1px;">
			                                <label for="plan_tratamiento" class="control-label"><b>Plan Tratamiento</b></label>
			                                <textarea id="plan_tratamiento{{$value->hc_id_procedimiento}}" name="plan_tratamiento" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
			                            </div>
		                           	@endif
		                            <input type="hidden" name="codigo" id="codigo{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>">
		                      		<label for="cie10" class="col-12 control-label" style="padding-left: 0px; @if($agenda->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b>
			                        </label>
		                      		<div class="row">
			                            <div class="form-group col-md-6 col-sm-6 col-12" style="padding: 15px; @if($agenda->proc_consul=='1') display: none; @endif">
			                                <input id="cie102{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase; border: 2px solid #004AC1;" onkeyup="javascript:this.value=this.value.toUpperCase(); " required placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
			                            </div>

			                             <div class="form-group col-md-3 col-sm-6 col-6" style=" padding-right: 5px; padding-left: 5px; @if($agenda->proc_consul=='1') display: none; @endif">
						                    	<select id="pre_def{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" name="pre_def" class="form-control input-sm" >
						                        <option value="">Seleccione ...</option>
						                        <option value="PRESUNTIVO">PRESUNTIVO</option>
						                        <option value="DEFINITIVO">DEFINITIVO</option>   
						                    </select> 
						                </div>
			                             
			                            <div class="col-md-3 col-sm-12 col-6" style="padding-top: 18px">
			                            	<center>
			                            		<div class="col-md-12 col-sm-6 col-12" style="padding: 15px; ">
					                            	@if($agenda->estado_cita=='4') 
					                            	<button id="bagregar{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" class="btn btn_agregar_diag btn-sm col-md-10" style="@if($agenda->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span>
					                            	</button>
				                            		@endif
			                            		</div>
			                            	</center>
			                            </div>
		                            </div>
		                            <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
		                                <table id="tdiagnostico{{$value->hc_id_procedimiento}}<?php echo e(date('his')); ?>" class="table table-striped" style="font-size: 12px;">
		                                    
		                                </table>
		                            </div>
		                            <div class="col-12" style="padding: 1px;">
		                                <label for="examenes_realizar" class="control-label" style="font-size: 14px"><b>Examenes a Realizar</b></label>
		                                <textarea id="examenes_realizar{{$value->hc_id_procedimiento}}" name="examenes_realizar" style="width: 100%; border: 2px solid #004AC1;" rows="2"  @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($value)){{$value->examenes_realizar}}@endif</textarea>
		                            </div>


		                            <!-- LA RECETA
		                            	<div class="col-9">
                        <div class="row">
                          <div class="col-6" style="text-align: right;" >
                              <label style="font-family: 'Helvetica general';" >Seguro:</label>
                          </div>
                          <div class="col-6">
                            @if(!is_null($re_hist->nombre))
                              {{$re_hist->nombre}} 
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <!--Contenedor Historial de Recetas Rp y Prescripcion-->
                        <div class="col-md-11"  >
                          <div class="row">
                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">                      
                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '2']) }}"> 
                              <div class="col-md-12" style="text-align: center; ">
                                <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                  <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                    <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                  </div>
                                  <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                    <label style="font-size: 14px; ">Imprimir Membretada</label>
                                  </div>  
                                </div>
                              </div>
                              </a>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '1']) }}"> 
                                <div class="col-md-12" style="text-align: center">
                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                    </div>
                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                      <label style="font-size: 14px">Imprimir</label>
                                    </div>  
                                  </div>
                                </div>
                              </a>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-11 col-sm-11 col-11" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;background-color:#004AC1;">
                          <!--Contenedor Historial de Recetas-->
                          <div  style="background-color: #004AC1; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                            <div class="box-title" style="background-color: #004AC1; margin-left: 10px">
                              <div class="row">
                                <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">
                                  <div class="btn" style="color: white">
                                    <a class="fa fa-pencil-square-o " onclick="editarreceta({{$re_hist->id}},'{{$paciente->id}}');"><span style="font-size: 13px">&nbsp;Editar</span>
                                    </a>
                                  </div>
                                </div> 
                                <div class="col-md-8 col-sm-8 col-8" style="padding-top: 4px">
                                  <span>Historial de Recetas</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="contenedor2" id="receta{{$re_hist->id}}" style="padding-bottom: 20px; padding-right: 15px">
                            <div class="col-md-12">
                              <div class="row">
                                <div class="col-md-6">
                                  <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                  <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #004AC1; ">
                                      @if(!is_null($re_hist->rp))
                                      <p><?php echo $re_hist->rp?>
                                      </p>
                                      @endif
                                  </div>
                                </div>
                                <div class="col-md-6" >
                                  <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                  <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                    @if(!is_null($re_hist->prescripcion))
                                      <p><?php echo $re_hist->prescripcion?></p>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                  
                        <div class="col-12">
                        	<center>
                            	<div class="col-4">
                     				<button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}})"  ><span class="fa fa-floppy-o"></span>&nbsp;Guardar
									</button>
                           		</div>
                           	</center>
                       </div>
                    </form>
		                        
						  	</div>
						</div>
			    	</div>
			   	@endforeach
		    </div>		
  	    </div>    
  </div>
</div>



<script type="text/javascript">
 
    function guardar_consulta(id_paciente, id_agenda){
        $.ajax({
			//async: true,
			type: "GET",
			url: "{{route('consulta.crear_nueva_consulta')}}/"+id_paciente+'/'+id_agenda, 
			data: "",
			datatype: "html",
			success: function(datahtml){
				//alert("!! CONSULTA CREADA !!");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }




 	function calcular_indice(id){
        var peso  =  document.getElementById('peso'+id).value;
        var estatura = document.getElementById('estatura'+id).value;
        var sexo = @if($paciente->sexo == 1){{$paciente->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$paciente->fecha_nacimiento}}');
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        } 
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc'+id).val(texto);
        $('#gct'+id).val(gct.toFixed(2));
        $('#imc'+id).val(imc.toFixed(2));
        $('#peso_ideal'+id).val(peso_ideal.toFixed(2));
    }

	function datos_child_pugh(id){  
        dato1 = parseInt($('#ascitis'+id).val());
        dato2 = parseInt($('#albumina'+id).val());
        dato3 = parseInt($('#encefalopatia'+id).val());
        dato4 = parseInt($('#bilirrubina'+id).val());
        dato5 = parseInt($('#inr'+id).val());
        cantidad = dato1+ dato2+dato3+dato4+dato5;
        $('#puntaje'+id).val(cantidad);
        if(cantidad >= 5 && cantidad<=6){
            $('#clase'+id).val('A');
            $('#sv1'+id).val('100%');
            $('#sv2'+id).val('85%');
        }else if(cantidad >= 7 && cantidad<=9){
            $('#clase'+id).val('B');
            $('#sv1'+id).val('80%');
            $('#sv2'+id).val('60%');
        }else if(cantidad >= 10 && cantidad<=15){
            $('#clase'+id).val('C');
            $('#sv1'+id).val('45%');
            $('#sv2'+id).val('35%');
        }
    }


    function cargar_tabla(id){
	    $.ajax({
            url:"{{route('epicrisis.cargar2')}}/"+id,
            dataType: "json",
            type: 'get',
            success: function(data){
               // console.log(data);
                var table = document.getElementById("tdiagnostico"+id+'{{date("his")}}');

                $.each(data, function (index, value) {
                    
                    var row = table.insertRow(index);
                    row.id = 'tdiag'+value.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>'+value.cie10+'</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = value.descripcion;

                    var vpre_def = '';
                    if(value.pre_def!=null){
                        vpre_def = value.pre_def;
                    }
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                    //alert(index);                       
                });
            }
        })    
    }


    function eliminar(id_h, id){
        var i = document.getElementById('tdiag'+id_h).rowIndex; 
        document.getElementById("tdiagnostico"+id+'{{date("his")}}').deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',
          
          success: function(data){
            //console.log(data);
            //cargar_tabla();
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function guardar_cie10_consulta(hcid, hc_id_procedimiento, div_receta){
        $.ajax({
            type: 'post',
            url:"{{route('hc4/epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+hc_id_procedimiento+'{{date("his")}}').val(), 'pre_def': $("#pre_def"+hc_id_procedimiento+'{{date("his")}}').val(), 'hcid': hcid, 'hc_id_procedimiento': hc_id_procedimiento, 'in_eg': null, 'id_paciente': '{{$paciente->id}}' },
            success: function(data){
                

                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico"+hc_id_procedimiento+'{{date("his")}}');
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;

                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';

                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;

	            var vpre_def = '';
                if(data.pre_def!=null){
                    vpre_def = data.pre_def;
                }
                var cell3 = row.insertCell(2);
                cell3.innerHTML = vpre_def;

                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+', '+hc_id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                //aqui va para la receta
                anterior = tinyMCE.get('trp'+div_receta).getContent();
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                tinyMCE.get('trp'+div_receta).setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                $('#rp'+div_receta).val(tinyMCE.get('trp'+div_receta).getContent());
                //cambiar_receta_2(); 
               
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
   		

    var edad;
    edad = calcularEdad('');

    function guardar_protocolo(id_hc_procedimiento, espid){
    	if (espid=='8') {
    		guardar_cardio(id_hc_procedimiento);
    	}
        calcular_indice(id_hc_procedimiento);
        datos_child_pugh(id_hc_procedimiento);
        $.ajax({
          type: 'post',
          url:"{{route('consulta.modificacion')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol"+id_hc_procedimiento).serialize(),
          success: function(data){
          	//alert("!! CONSULTA ACTUALIZADA !!");
            //console.log(data);
            $("#alerta_datos").fadeIn(1000);
    		$("#alerta_datos").fadeOut(3000);
            //cargar_consulta();
          },
          error: function(data){
             console.log(data);
          }
        });
    }


    function guardar_cardio(id){
        calcular_indice(id);
        $.ajax({
          type: 'post',
          url:"{{route('cardiologia.crea_actualiza')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_evol"+id).serialize(),
          success: function(data){
            //console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento"+id ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad'+id).val( edad );
          },
          error: function(data){
          }
        });
    }

	@foreach($procedimientos2 as $value) 
		cargar_tabla({{$value->hc_id_procedimiento}});
		calcular_indice({{$value->hc_id_procedimiento}});
	    datos_child_pugh({{$value->hc_id_procedimiento}});

    	$('#edad{{$value->hc_id_procedimiento}}').val( edad );

	    tinymce.init({
	    selector: '#thistoria_clinica{{$value->hc_id_procedimiento}}{{date("his")}}',
	    inline: true,
	    menubar: false,
	    content_style: ".mce-content-body {font-size:14px;}",

	    @if($agenda->estado_cita!='4') 
	    readonly: 1,
	    @else
	    
	    setup: function (editor) {
	        editor.on('init', function (e) {
	           var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{date("his")}}');
	           //alert(ed.getContent());
	            $('#historia_clinica{{$value->hc_id_procedimiento}}{{date("his")}}').val(ed.getContent());
	    
	        });
	    },
	    @endif
	   
	    init_instance_callback: function (editor) {
	        editor.on('Change', function (e) {
	            var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{date("his")}}');
	            $('#historia_clinica{{$value->hc_id_procedimiento}}{{date("his")}}').val(ed.getContent());
	            //guardar_protocolo({{$value->hc_id_procedimiento}}); 
	            @if(substr($value->fechaini,0,10) == date('Y-m-d'))
	                guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}}) 
	            @endif
	        });
	      }
	    });


	    tinymce.init({
	    selector: '#tresultado_exam{{$value->hc_id_procedimiento}}{{date("his")}}',
	    inline: true,
	    menubar: false,
	    content_style: ".mce-content-body {font-size:14px;}",

	    @if($agenda->estado_cita!='4') 
	    readonly: 1,
	    @else
	    
	    setup: function (editor) {
	        editor.on('init', function (e) {
	           var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{date("his")}}');
	           //alert(ed.getContent());
	            $('#resultado_exam{{$value->hc_id_procedimiento}}{{date("his")}}').val(ed.getContent());
	    
	        });
	    },
	    @endif
	   
	    init_instance_callback: function (editor) {
	        editor.on('Change', function (e) {
	            var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{date("his")}}');
	            $('#resultado_exam{{$value->hc_id_procedimiento}}{{date("his")}}').val(ed.getContent());
	            //guardar_protocolo({{$value->hc_id_procedimiento}}); 
	            @if(substr($value->fechaini,0,10) == date('Y-m-d'))
	                guardar_protocolo({{$value->hc_id_procedimiento}}, {{$agenda->espid}}) 
	            @endif
	        });
	      }
	    });



		$('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').autocomplete({
	    source: function( request, response ) 
	    {
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
	    });	


		$('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').change( function()
		{
	    $.ajax({
	        type: 'post',
	        url:"{{route('epicrisis.cie10_nombre2')}}",
	        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	        datatype: 'json',
	        data: $('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}'),
	        success: function(data){
	            if(data!='0'){

	                $('#codigo{{$value->hc_id_procedimiento}}{{date("his")}}').val(data.id);
	                // guardar_cie10({{$value->hc_id_procedimiento}}, {{$value->hcid}}, {{$value->hc_id_procedimiento}});
	            }
	        },
	        error: function(data){    
	        }
	    })
		});


		$('#bagregar{{$value->hc_id_procedimiento}}{{date("his")}}').click( function(){
    
        if($('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').val()!='' ){
            if($('#pre_def{{$value->hc_id_procedimiento}}{{date("his")}}').val()!='' ){
                //alert("guardar");+
                @php
						$rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
						//dd($rec->id); 
				@endphp
                guardar_cie10_consulta({{$value->hcid}}, {{$value->hc_id_procedimiento}}, '{{$rec->id}}{{date("his")}}');   
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo{{$value->hc_id_procedimiento}}{{date("his")}}').val('');
        $('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').val('');
        $('#pre_def{{$value->hc_id_procedimiento}}{{date("his")}}').val(''); 
  		});
	@endforeach

	@foreach($procedimientos1 as $value) 
		cargar_tabla({{$value->hc_id_procedimiento}});
		calcular_indice({{$value->hc_id_procedimiento}});
	    datos_child_pugh({{$value->hc_id_procedimiento}});

    	$('#edad{{$value->hc_id_procedimiento}}').val( edad );


	    tinymce.init({
	    selector: '#thistoria_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>',
	    inline: true,
	    menubar: false,
	    content_style: ".mce-content-body {font-size:14px;}",

	    @if($agenda->estado_cita!='4') 
	    readonly: 1,
	    @else
	    
	    setup: function (editor) {
	        editor.on('init', function (e) {
	           var ed = tinyMCE.get('thistoria_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>');
	           //alert(ed.getContent());
	            $("#historia_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>").val(ed.getContent());
	    
	        });
	    },
	    @endif
	   
	    init_instance_callback: function (editor) {
	        editor.on('Change', function (e) {
	            var ed = tinyMCE.get('thistoria_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>');
	            $("#historia_clinica<?php echo e($value->hc_id_procedimiento); ?><?php echo e(date('his')); ?>").val(ed.getContent());
	            
	        });
	      }
	    });


		$('#cie1022{{$value->hc_id_procedimiento}}{{date("his")}}').autocomplete({
	    source: function( request, response ) 
	    {
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
	    });	


		$('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').change( function()
		{
	    $.ajax({
	        type: 'post',
	        url:"{{route('epicrisis.cie10_nombre2')}}",
	        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	        datatype: 'json',
	        data: $('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}'),
	        success: function(data){
	            if(data!='0'){
	                $('#codigo{{$value->hc_id_procedimiento}}{{date("his")}}').val(data.id);
	                 //guardar_cie10( {{$value->hcid}}, {{$value->hc_id_procedimiento}});
	            }
	        },
	        error: function(data){    
	        }
	    })
		});

		$('#bagregar{{$value->hc_id_procedimiento}}{{date("his")}}').click( function(){
    
        if($('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').val()!='' ){
            if($('#pre_def{{$value->hc_id_procedimiento}}{{date("his")}}').val()!='' ){
                //alert("guardar");
                @php
						$rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
						//dd($rec->id); 
				@endphp
                guardar_cie10_consulta({{$value->hcid}}, {{$value->hc_id_procedimiento}}, '{{$rec->id}}{{date("his")}}');    
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo{{$value->hc_id_procedimiento}}{{date("his")}}').val('');
        $('#cie10{{$value->hc_id_procedimiento}}{{date("his")}}').val('');
        $('#pre_def{{$value->hc_id_procedimiento}}{{date("his")}}').val(''); 
  		});
	@endforeach
</script>

<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<script type="text/javascript">

	$('.datetimepicker2<?php echo e(date('his')); ?>').datetimepicker({
		format: 'YYYY/MM/DD hh:mm',
	});

	$('.datetimepicker1<?php echo e(date('his')); ?>').datetimepicker({
		format: 'YYYY/MM/DD hh:mm',
	});

</script>

 