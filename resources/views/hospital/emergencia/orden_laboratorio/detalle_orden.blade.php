<div class="card">
	<div class="card-header bg bg-primary">
		
		<div class="col-md-3">
			@if($orden->seguro->tipo!='0')
				<div class="btn" style="color: white">
    				<a class="fa fa-pencil-square-o " onclick="editar_orden_lab({{$orden->id}},{{$orden->estado}});" ><span style="font-size: 13px">&nbsp;{{trans('emergencia.Editar')}}</span>
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


				$detalles = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','examen_detalle.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();

				$examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
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


  

