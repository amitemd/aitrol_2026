    <div class="col-md-12">
				   	    @php
	   					    $fecha_r = null;
	   					    $xdoctor = null;
	   					    if(!is_null($ordend->fecha_orden)){
				    		   $fecha_r =  Date('Y-m-d',strtotime($ordend->fecha_orden));
				    	    }

   	  					    if($ordend->id_doctor != ""){
                               $xdoctor = DB::table('users as us')->where('us.id',$ordend->id_doctor)->first();
                            }
				   	  	
				   	  	@endphp
				   	  	@php
	                        $fecha = substr($ordend->fecha_orden,0,10);
	                        $invert = explode( '-',$fecha);
	                        $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
						@endphp
						@php
							    $x_orden = Sis_medico\Orden::find($ordend->id);
							    $diasOrden = [1 => 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
							    $mesesOrden = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
							    $fechaOrdenTexto = '';
							    if (!is_null($ordend->fecha_orden)) {
							        $fechaOrdenTimestamp = strtotime($ordend->fecha_orden);
							        $fechaOrdenTexto = $diasOrden[(int) date('N', $fechaOrdenTimestamp)].' '.date('d', $fechaOrdenTimestamp).' de '.$mesesOrden[(int) date('n', $fechaOrdenTimestamp)].' del '.date('Y', $fechaOrdenTimestamp);
							    }
							    $nombresResumen = [];
							    if (!is_null($x_orden)) {
							        foreach ($x_orden->orden_tipo as $tipo) {
							            foreach ($tipo->orden_procedimiento as $proc) {
							                if (!is_null($proc->procedimiento)) {
							                    $nombresResumen[] = $proc->procedimiento->observacion ?: $proc->procedimiento->nombre;
							                }
							            }
							        }
							    }
							    $procedimientosResumen = implode(' + ', array_unique($nombresResumen));
                        @endphp
                       
					   	<div id="procedimiento_orden_eliminado{{$ordend->id}}" class="box hc4-procedure-order-card @if($fecha_r != date('Y-m-d')) collapsed-box @endif">
					   		<div class="box-header with-border hc4-procedure-order-summary hc4-procedure-order-summary--grid">
					   			<div class="hc4-procedure-order-summary__date">{{$fechaOrdenTexto}}</div>
					   			<div class="hc4-procedure-order-summary__doctor">
					   				<strong>Dr (a):</strong>
					   				@if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @else No asignado @endif
					   			</div>
					   			<div class="hc4-procedure-order-summary__right">
						<div class="hc4-procedure-order-summary__procedures">
							<span>{{$procedimientosResumen !== '' ? $procedimientosResumen : 'Sin procedimientos seleccionados'}}</span>
					   				</div>
					   				<div class="hc4-procedure-order-summary__tools box-tools">
					   					<span class="hc4-procedure-order-summary__id">Orden #{{$ordend->id}}</span>
							<button type="button" class="hc4-procedure-order-summary__delete" onclick="eliminar_orden_procedimiento({{$ordend->id}});" title="Eliminar orden">
								<i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">@lang('hc4.eliminar')</span>
					   					</button>
					   					<button type="button" class="btn btn-sm hc4-procedure-order-summary__toggle" data-widget="collapse" title="Mostrar u ocultar orden">
					   						<i class="fa fa-plus"></i>
					   					</button>
					   				</div>
					   			</div>
					   		</div>
				   	  		<div class="box-body" style="background: white;">
					   			<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
					   				<div class="box hc4-procedure-order-detail">
					   					<div class="box-header with-border hc4-procedure-order-detail__header">
					  						<div class="row hc4-procedure-order-detail__header-row">
						  					<div class="col-3" style="margin-right: 10px">
						    						<div class="btn" style="color: white">
								    				<a class="fa fa-pencil-square-o " onclick="editar_orden_proendospico({{$ordend->id}},'{{$ordend->id_paciente}}', this);" ><span style="font-size: 13px">&nbsp;@lang('hc4.editar')</span>
								    				</a>
							    				</div>
						    				</div>
						    				<div class="col-4" style="text-align: center;padding-top: 6px">
									<span class="sr-only">@lang('hc4.detalleOrden')</span>
						  					</div>
						  					<div class="col-4" style="text-align: right;padding-top: 6px">
									           <a class="btn btn-danger" onclick="descargar({{$ordend->id}});"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('hc4.descargarOrden')</a>
									        </div>
				  						</div>
				  					</div>
					   				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden{{$ordend->id}}">

					   				</div>
					   				</div>
					   			</div>
				   	    </div>
                   </div> 
    </div> 

 <script type="text/javascript">

 	    editar_orden_proendospico_new('{{$ordend->id}}','{{$ordend->id_paciente}}');
	
		 function eliminar_orden_procedimiento(id){
        var opcion = confirm("Estas Seguro que deseas borrar el procedimiento?");
        if (opcion == true) {
            $.ajax({
                type: "DELETE",
                url: "{{asset('/')}}/orden/procedimientos/eliminar/"+id,
                data: {'_token': '{{ csrf_token() }}'},
                datatype: "html",
                success: function(datahtml, entra){
                    document.getElementById( 'procedimiento_orden_eliminado'+id ).style.display = 'none';
                },
                error:  function(){
                    alert('error al cargar');
                }
            });

        }
    }




	    function editar_orden_proendospico_new(id_orden,id_paciente){

	    	
	    	$.ajax({
				type: "GET",
				url: "{{route('editar.orden_procedimiento_endoscopico')}}/"+id_orden+'/'+id_paciente, 
				data: "",
				datatype: "html",
				success: function(datahtml){
			      
				      $("#xorden"+id_orden).html(datahtml);
				},
				error:  function(){
					alert('error al cargar');
				}
			});	
		}

 </script>
