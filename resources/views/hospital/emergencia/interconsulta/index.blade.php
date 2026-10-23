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
                    <label style="color: white;" class="control_label">{{trans('emergencia.INTERCONSULTA')}}</label>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-xs btn-info" onclick="agregar_interconsulta();"><i class="fa fa-plus"></i> {{trans('emergencia.Agregar')}}</button>
                </div>
            </div>


        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12" style="margin-top: 10px;">
        	<div class="card">
		        
        	@foreach($interconsultas as $interconsulta)
				
		   	    @php
					if(!is_null($interconsulta->fecha)){
		    		  	$fecha_r =  Date('Y-m-d',strtotime($interconsulta->fecha));
		    	    }else{
						$fecha_r =  Date('Y-m-d',strtotime($interconsulta->created_at));
		    		}

					if($interconsulta->id_doctor != ""){
		               $doctor = $interconsulta->doctor;
		            }

		            $fecha = substr($interconsulta->fecha,0,10);
		            
				@endphp
					
				<div class="card h-80">
					<div class="card-header bg bg-primary colorbasic">
						<div class="col-md-1">
		        			<button id="edit{{$interconsulta->id}}" type="button" class="btn btn-warning btn-xs" onclick="editar_interconsulta('{{$interconsulta->id}}')">
	                        	<i class="fa fa-edit"></i>
	                        </button>
	        			</div>
		        		<div class="col-md-4">
		        			@if( !is_null($interconsulta->fecha ))
                                @php
		                        	$dia =  Date('N',strtotime($interconsulta->fecha));
		                        	$mes =  Date('n',strtotime($interconsulta->fecha));
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
                                 {{substr($interconsulta->fecha,8,2)}} de
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
                                del {{substr($interconsulta->fecha,0,4)}}</b>
                            @endif	
		        		</div>
		        		<div class="col-md-4">
		        			Dr (a):	@if(!is_null($doctor->nombre1)) {{$doctor->nombre1}} {{$doctor->apellido1}}@endif
		        		</div>	
						<div class="col-md-2">
							<a id="imprimir{{$interconsulta->id}}" type="button" class="btn btn-success btn-xs" href="{{route('formulario.007',['id' => $interconsulta->id])}}" target="_blank">
								Formulario 007 - Solicitud
							</a>
	        			</div>
		        		<div class="col-md-1">
		        			<button id="plus{{$interconsulta->id}}" type="button" class="btn btn-primary" onclick="ver_interconsulta('{{$interconsulta->id}}')">
	                        	<i class="fa fa-plus"></i>
	                        </button>
	                        <button id="min{{$interconsulta->id}}" type="button" class="btn btn-primary" onclick="ocultar_interconsulta('{{$interconsulta->id}}')" style="display: none">
	                        	<i class="fa fa-minus"></i>
	                        </button>	
		        		</div>
			        </div>	
			        <div class="card-body" id="idetalle{{$interconsulta->id}}" style="padding: 0px;">
			        			          
			        </div>
			    </div>
			        
			@endforeach    
        </div>

    </div>

</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">

	@if($id_editar != null)
		editar_interconsulta('{{$id_editar}}');
	@endif

	function agregar_interconsulta(){

		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('decimo.crear_interconsulta',[ 'id' => $solicitud->id ])}}",
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

	function editar_interconsulta(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/interconsulta/editar')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#idetalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();
	}

	function ver_interconsulta(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/interconsulta/detalle')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#idetalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();	
	}
	function ocultar_interconsulta(id){

		$("#idetalle"+id).html("<br>");
		$("#min"+id).hide();
		$("#plus"+id).show();


	}	
</script>	

