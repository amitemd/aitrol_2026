<div class="card">
	<div class="card-header bg bg-primary">
		
		<div class="col-md-3">
			@if($orden->seguro->tipo!='0')
				<div class="btn" style="color: white">
                    <a class="fa fa-pencil-square-o " onclick="editar_labs('{{ $orden->id }}');" ><span style="font-size: 13px">&nbsp;{{trans('emergencia.Editar')}}</span>
    				</a>
				</div>
			@endif
		</div>
		<div class="col-md-1">
			<i style="color: #004AC1;">{{$orden->id}}</i>
		</div>	
		<div class="col-md-5" style="text-align: center;color: white;">
			{{trans('emergencia.DetalledeOrden')}} {{$orden->seguro->nombre}}
		</div>
		<div class="col-md-3" style="text-align: right;padding-top: 6px">
           <a class="btn btn-success" @if($orden->seguro->tipo=='0') href="{{url('orden/descargar')}}/{{$orden->id}}" @else href="{{url('cotizador_p/orden/imprimir')}}/{{$orden->id}}"  @endif target="_blank" style="background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('emergencia.DescargarOrden')}}</a>
        </div>
	
	</div>

	<div class="card-body" style="margin-bottom: 1px;padding: 0;">
		@php
	    	$examenes_labs= array();
			if($orden->seguro->tipo=='0'){

				$detalles = $orden->detalles;
			}else{


                $examenes_labs = app(\Sis_medico\Services\HospitalLaboratoryOrderCatalog::class)->data($orden, ['seleccionados' => 1])['examenes_labs'];
			}
		@endphp

		@if($orden->seguro->tipo=='0')
			<iframe frameBorder="0" src="{{route('as400.visualizar', ['id' => $orden->id])}}" style="width: 100%; height: 400px;"></iframe>
		@else			
		    <div class="table-responsive">
			    <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
			        <tbody>
			        	<tr>
			        		<td style="text-align: right;"><b>{{trans('emergencia.Cantidad')}}</b></td>
	              			<td style="text-align: right;"><b>{{$orden->cantidad}}</b></td>
			        	</tr>
			       		@php  $cambia = 0; $contador = 0; @endphp
			       		@foreach($examenes_labs as $examen)
                            @if($loop->first || $cambia != $examen->id_agrupador)
			       				@php $contador = 0; @endphp
			       				<tr>
                                    <td colspan="2" style="background-color: #edf6fa;color: #174763;margin: 0px;padding: 6px;">{{$examen->lnombre ?: 'Sin agrupador'}}</td>
					            </tr>
                                @php $cambia = $examen->id_agrupador; @endphp
			       			@endif
			       			@if($contador == 0)
			       			<tr >
			       			@endif
                              <td style="padding: 5px;" >{{$examen->descripcion ?: $examen->nombre}}</td>

			                  @php $contador ++; @endphp
			                  @if($contador == 2) @php $contador = 0; @endphp @endif
			                @if($contador == 0)
			                </tr>
			                @endif

			       		@endforeach
			        </tbody>
		      	</table>
	    	</div>
		@endif
		<div class="col-md_12" >
			<center>
	            <div class="col-md-5" style="padding-top: 15px;text-align: center;">
	                <a style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  class="btn btn-info btn_ordenes" @if($orden->seguro->tipo=='0') href="{{url('orden/descargar')}}/{{$orden->id}}" @else href="{{url('cotizador_p/orden/imprimir')}}/{{$orden->id}}"  @endif target="_blank" ><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;{{trans('emergencia.DescargarOrden')}}
	                </a>
	            </div>
	        </center>
        </div>
			
		  
	</div>    
	
</div>	


  

