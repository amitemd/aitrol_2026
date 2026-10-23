<table class="table table-bordered table-hover dataTable">
	<tbody>
		@foreach($sub_resultados as $sub)
		<tr id="sr{{$sub->id}}">
			<td style="width: 30% !important;">{{$sub->campo1}}</td>
			<td style="width: 30% !important;">{{$sub->campo2}}</td>
			<td style="width: 30% !important;">{{$sub->campo3}}</td>
			<td style="width: 10% !important;padding: 0px;"><button class="btn btn-sm btn-danger" onclick="elimina_sub('{{$sub->id}}')" href=""><i class="glyphicon glyphicon-trash"></i></button></td>
		</tr>
		@endforeach
	</tbody>
</table>