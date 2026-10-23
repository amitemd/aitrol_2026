<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <span class="sradio">10</span>
                </div>
                <div class="col-md-7">
                    <label style="color: white;" class="control_label">{{trans('emergencia.HISTORIALDEORDENESDELABORATORIO')}}</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_lab_publico();"><i class="fa fa-plus"></i> {{trans('emergencia.Publico')}}</button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-info" onclick="agregar_lab_privado();"><i class="fa fa-plus"></i> {{trans('emergencia.Privado')}}</button>
                </div>

                <!--div class="col-md-2">
                    <button class="btn btn-primary btn-xs" type="button" > <i class="fa fa-remove"></i> </button>
                </div-->
            </div>


        </div>
    </div>
    <div class="card-body" style="padding: 0;">
    	<br>
    	@foreach($ordenes as $orden)

	   	    @php
				if(!is_null($orden->fecha_orden)){
	    		  	$fecha_r =  Date('Y-m-d',strtotime($orden->fecha_orden));
	    	    }else{
					$fecha_r =  Date('Y-m-d',strtotime($orden->created_at));
	    		}

				if($orden->id_doctor_ieced != ""){
	               $xdoctor = $orden->doctor;
	            }

	            $fecha = substr($orden->fecha_orden,0,10);

			@endphp

			<div id="orden_lab_div{{$orden->id}}" class="col-md-12" style="padding: 0;" >
				<div class="card-header bg bg-primary colorbasic" >
					<div class="col-md-1">
	        			<button id="edit{{$orden->id}}" type="button" class="btn btn-warning btn-xs" onclick="editar_labs('{{$orden->id}}')">
                        	<i class="fa fa-edit"></i>
                        </button>
	        		</div>
	        		<div class="col-md-5">
	        			@if(!is_null($orden->fecha_orden))
                            @php
	                         $dia =  Date('N',strtotime($orden->fecha_orden));
	                         $mes =  Date('n',strtotime($orden->fecha_orden));
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
                             {{substr($orden->fecha_orden,8,2)}} de
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
                            del {{substr($orden->fecha_orden,0,4)}}</b>
                        @endif
	        		</div>
	        		<div class="col-md-4">
	        			Dr (a):	@if(!is_null($xdoctor->nombre1)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif
	        		</div>
	        		<div class="col-md-2" >
					<div class="row">
					<div class="col-md-5">
						<button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$orden->id}}')" style="display: none">
                        	<i class="fa fa-minus"></i>
                        </button>
						<button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden('{{$orden->id}}')">
                        	<i class="fa fa-plus"></i>
                        </button>
					</div>
					@if(!is_null($orden->estado))
						@if($orden->estado == -1)
						<div class="col-md-5">
							<button id="delete{{$orden->id}}" type="button" class="btn btn-danger btn-xs" onclick="eliminar_labs('{{$orden->id}}')">
								<i class="fa fa-trash"></i>
							</button>
						</div>
						@endif
					@endif
					</div>
                        <!-- <button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$orden->id}}')" style="display: none">
                        	<i class="fa fa-minus"></i>
                        </button>
                        <button id="delete{{$orden->id}}" type="button" class="btn btn-danger btn-xs" onclick="eliminar_labs('{{$orden->id}}')">
                        	<i class="fa fa-trash"></i>
                        </button>
                        <button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden('{{$orden->id}}')">
                        	<i class="fa fa-plus"></i>
                        </button> -->
	        		</div>
		        </div>
		        <div class="card-body" id="labs_detalle{{$orden->id}}" style="padding: 0px;">
		        </div>
		    </div>
		    <br>

		@endforeach
	</div>
</div>


<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
	@if($id_editar != null)
		editar_labs('{{$id_editar}}');
	@endif
	function ver_orden(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#labs_detalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();
	}

	function ocultar_orden(id){

		$("#labs_detalle"+id).html("<br>");
		$("#min"+id).hide();
		$("#plus"+id).show();


	}

	function agregar_lab_publico(){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hospital.decimo_laboratorio_crear_pb',[ 'id' => $solicitud->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});

	}

	function agregar_lab_privado(){

		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('decimopaso.laboratorio_orden_crear_part',[ 'id' => $solicitud->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});


	}

	function editar_labs(id){
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/editar/pub')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#labs_detalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();
	}

	function eliminar_labs(id) {
		var respuesta = confirm("¿ Deseas eliminar la orden ?");
		if (respuesta == true) {
			$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/eliminar')}}/"+id,
			data: "",
			datatype: "json",
			success: function(data){
				document.getElementById( 'orden_lab_div'+id).style.display = 'none';
				alert('orden eliminada');

			},
			error:  function(){
				alert('error al cargar');
			}
		});

		
		}
	}

</script>
<!--script type="text/javascript">

    agregar_orden_listado();
    //Funcion para crear una Orden de Procedimiento Endoscopico
    function agregar_orden_listado(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hc4_orden_lab.index2',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    function agregar_orden_laboratorio(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hc4_orden_lab.crear',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    function agregar_orden_as400(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('as400.index_hc4')}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }




    //Descarga Orden de Procedimiento Endoscopico
	function descargar(id_or){//
       window.open('{{url('cotizador_p/orden/imprimir')}}/'+id_or,'_blank');
    }

</script-->
