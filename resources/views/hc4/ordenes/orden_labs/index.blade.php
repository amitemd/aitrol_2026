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
	}

	.thead-dark>tr>th{
		background-color: #005580 !important;
		padding: 5px;
	}

	.table-hover tbody tr:hover{
		background-color: #80ffff !important;

	}

</style>
<div class="box " style="border: 2px solid #004AC1; background-color: white;">
	<div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;">
		<div class="row">
			<div class="col-md-8 col-sm-8 col-12">
			    <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;padding-top: 10px">
                    <b>HISTORIAL DE ORDENES DE LABORATORIO</b>
				</h1>
		    </div>
		    <div class="col-md-2 col-sm-2 col-12 oculto" style="padding-left: 0px">
			    <div style="margin-bottom: 5px;text-align: left; margin-left: 5px; margin-right: 5px">
					<a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white; margin-left: 10px" onClick="agregar_orden_as400();" >
		                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
			            	<div class="col-12" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
			            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
			            		<label style="font-size: 10px">AGREGAR ORDEN IESS - AS400</label>
			            	</div>
						</div>
					</a>
				</div>
	        </div>
		    <div class="col-md-2 col-sm-2 col-12" style="padding-left: 0px">
			    <div style="margin-bottom: 5px;text-align: left; margin-left: 5px; margin-right: 5px">
					<a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white; margin-left: 10px" onClick="agregar_orden_laboratorio();" >
		                <div class="row" style="margin-left: 0px; margin-right: 0px; ">
			            	<div class="col-12" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
			            		<img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
			            		<label style="font-size: 10px">AGREGAR ORDEN PARTICULAR</label>
			            	</div>
						</div>
					</a>
				</div>
	        </div>
		</div>

		@php
			$xedad = '0';
			if($paciente->fecha_nacimiento!=null){
				$xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
			}

		@endphp
		<div class="row">
			<div class="col-md-9" style="padding-top: 15px">
				<center>
				<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
			        <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
			        	{{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
	                </b>
				</h1>
				</center>
		    </div>
		    <div class="col-md-3" style="padding-top: 15px">
				<h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
			        <b>
			        	EDAD: {{$xedad}} AÑOS
	                </b>
				</h1>
		    </div>
		</div>

	</div>
	<div class="box-body" style="background-color: #56ABE3;">
		<div class="col-md-12">
            <div class="row parent">

            	<div class="col-md-12" id="listado" style="padding: 0">
            	</div>


			</div>
        </div>
    </div>
</div>

<script type="text/javascript">

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

</script>
