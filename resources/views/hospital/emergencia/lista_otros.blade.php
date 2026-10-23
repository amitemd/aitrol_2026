<div class="mb-0 table-responsive">
	<table class="table b-table" role="table" style="font-size: 12px;">
          <tbody>
          	<tr role="row" class="">
               	<th colspan="4" width="100%"><b><center style="font-size: 16px;">{{trans('Otros Examenes')}}</center></b></th>
            	</tr>
			@foreach($detalles as $detalle)
				@if($detalle->examen->id_agrupador == '7')
				<tr role="row">
					<td><b>{{$detalle->examen->nombre}}</b></td>
     				<td><input onclick="agregar_quitar_examen( this );ver_otros();" id="ch-{{$orden->id}}-{{$detalle->id_examen}}" checked name="ch-{{$orden->id}}-{{$detalle->id_examen}}" type="checkbox"></td>
     			</tr>			
				@endif		        
			@endforeach 
		</tbody>
	</table>
</div>			