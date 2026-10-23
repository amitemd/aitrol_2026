<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <span class="sradio">10</span>
                </div>
                <div class="col-md-8">
                	@if($tipo=='0')
                    <label style="color: white;" class="control_label">{{trans('boxesh.ORDENESDEPROCEDIMIENTOSENDOSCOPICOS')}}</label>
                    @elseif($tipo=='1')
                    <label style="color: white;" class="control_label">{{trans('boxesh.ORDENESDEPROCEDIMIENTOSFUNCIONALES')}}</label>
                    @elseif($tipo=='2')
                    <label style="color: white;" class="control_label">{{trans('boxesh.ORDENESDEIMAGENES')}}</label>
                    @else
                    <label style="color: white;" class="control_label">{{trans('boxesh.ORDENESDEPROCEDIMIENTOSQUIRURGICOS')}}</label>
                    @endif
                </div>

                <div class="col-md-3">
                    <button class="btn btn-xs btn-info" onclick="agregar_procedimiento();"><i class="fa fa-plus"></i> {{trans('boxesh.Agregar')}}</button>
                </div>
            </div>


        </div>
    </div>
    <div class="card-body" style="padding: 0px;">
        <div class="col-md-12" style="margin-top: 10px;padding: 0;">
        	<div class="card">
		        
        	@foreach($ordenes as $orden)
				
		   	    @php
					if(!is_null($orden->fecha_orden)){
		    		  	$fecha_r =  Date('Y-m-d',strtotime($orden->fecha_orden));
		    	    }else{
						$fecha_r =  Date('Y-m-d',strtotime($orden->created_at));
		    		}

					if($orden->id_doctor != ""){
		               $xdoctor = $orden->doctor;
		            }

		            $fecha = substr($orden->fecha_orden,0,10);
		            
				@endphp
					
				<div class="card h-80">
					<div class="card-header bg bg-primary colorbasic">
						<div class="col-md-1">
		        			<button id="edit{{$orden->id}}" type="button" class="btn btn-warning btn-xs" onclick="editar_orden('{{$orden->id}}')">
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
		        		<div class="col-md-5">
		        			Dr (a):	@if(!is_null($xdoctor->nombre1)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif
		        		</div>	
		        		<div class="col-md-1">
		        			<button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden('{{$orden->id}}')">
	                        	<i class="fa fa-plus"></i>
	                        </button>
	                        <button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$orden->id}}')" style="display: none">
	                        	<i class="fa fa-minus"></i>
	                        </button>	
		        		</div>
			        </div>	
			        <div class="card-body" id="proc_detalle{{$orden->id}}" style="padding: 0px;">
			        			          
			        </div>
			    </div>
			        
			@endforeach    
        </div>

    </div>

</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">

	@if($id_editar != null)
		editar_orden('{{$id_editar}}');
	@endif
	
	function agregar_procedimiento(){

		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('decimopaso.procedimiento_crear',[ 'id' => $solicitud->id, 'tipo' => $tipo ])}}",
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

	function ver_orden(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/procedimiento/detalle/solic')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#proc_detalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();	
	}

	function editar_orden(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/procedimiento/editar/solic')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#proc_detalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();	
	}
	
	function ocultar_orden(id){

		$("#proc_detalle"+id).html("<br>");
		$("#min"+id).hide();
		$("#plus"+id).show();


	}	
</script>	

