<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
	<div class="row">
		<div class="table-responsive col-md-12">
			<table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
				<tbody>
					@php $cambia = 0; $contador = 0; @endphp
					@foreach($examenes_labs as $examen)
					@if($examen->estado=='0' && !in_array($examen->ex_id,$detalles_ch))
					@else
					@if($cambia != $examen->id_examen_agrupador_labs)
					@php $contador = 0; @endphp
					<tr>
						<td colspan="4" style="background-color: #ff6600;color: white;margin: 0px;padding: 0;">{{$agrupador_labs->where('id',$examen->id_examen_agrupador_labs)->first()->nombre}}</td>
					</tr>
					@php $cambia = $examen->id_examen_agrupador_labs; @endphp
					@endif
					@if($contador == 0)
					<tr>
						@endif
						<td style="padding: 5px;@if(in_array($examen->ex_id,$detalles_ch)) background-color: #b3e0ff; @endif"><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif ></td>
						<td style="padding: 5px;@if(in_array($examen->ex_id,$detalles_ch)) background-color: #b3e0ff; @endif">{{$examen->descripcion}}</td>

						@php $contador ++; @endphp
						@if($contador == 2) @php $contador = 0; @endphp @endif
						@if($contador == 0)
					</tr>
					@endif
					@endif

					@endforeach

				</tbody>
			</table>
		</div>
	</div>

</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
	$('input[type="checkbox"].flat-orange').iCheck({
		checkboxClass: 'icheckbox_flat-orange',
		radioClass: 'iradio_flat-orange'
	});

	$('input[type="checkbox"].flat-orange').on('ifChecked', function(event) {

		//console.log(this.name.substring(2));
		cotizador_crear_id(this.name.substring(2));

	});

	$('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event) {

		//cotizador_crear();
		cotizador_delete_id(this.name.substring(2));

	});

	$('input[type="checkbox"].flat-orange').on('ifChecked', function(event) {
		var esto = $(this).attr("id");
		console.log(esto);
		var d = document.getElementsByClassName("flat-orange");
		if (esto == 'ch892') {
			for (var i = 0; i < d.length; i++) {
				$(d[i]).parent().addClass("checked");
			}
		}
		if (esto == 'ch409') {
			for (var i = 0; i < d.length; i++) {
				$(d[i]).parent().removeClass("checked");
			}
		}
	});
</script>