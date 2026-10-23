
<table border="1">
	<tbody>
		@foreach($historia_clinica as $h)
		<tr @if(in_array($h->hcid, $arr)) style= "background-color: blue;" @endif>
			<td>{{$h->hcid}}</td>
			<td>{{$h->examenes_realizar}}</td>
			<td>{{$h->doctor_1->apellido1}}</td>
			<td>{{$h->created_at}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
