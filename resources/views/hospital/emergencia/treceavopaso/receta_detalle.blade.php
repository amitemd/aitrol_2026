<table role="table" aria-busy="false" aria-colcount="4" class="table b-table">
	<thead role="rowgroup" class="">
		<thead role="rowgroup" class="">
      <tr role="row" class="">
        <th width="20%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
        <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
        <th width="55%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
        <th width="15%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
      </tr>
    </thead>
	</thead>
	<tbody role="rowgroup">
		@foreach($detalles as $detalle)
          <tr role="row" class="">
            <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">{{$detalle->nombre}}</span></td>
            <td aria-colindex="2" role="cell" class=""><input class="form-control" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="editar_medicina('{{$detalle->id}}')"></td>
            <td aria-colindex="3" role="cell" class="">
              <textarea wrap="soft" class="mb-1 mb-xl-0 form-control" style="resize: none; overflow-y: scroll; height: 92px;" name="dosis{{$detalle->id}}" id="cantidad{{$detalle->id}}" onchange="editar_medicina('{{$detalle->id}}')">{{$detalle->dosis}}</textarea>
            </td>
            <td aria-colindex="4" role="cell" class="">
              <button id="edit{{$detalle->id}}" type="button" class="btn btn-warning btn-sm" onclick="">
                <i class="fa fa-edit"></i>
              </button>
              <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm" onclick="eliminar_medicina('{{$detalle->id}}');">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>
    @endforeach      
	</tbody>
</table>
