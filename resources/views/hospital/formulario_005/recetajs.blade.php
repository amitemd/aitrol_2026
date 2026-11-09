@if(count($detalles) > 0)
  <div class="recipe-table__autosave">
    <i class="fa fa-cloud-upload" aria-hidden="true"></i> Guardado automático
  </div>
  <div class="table-responsive recipe-table-wrap">
    <table role="table" aria-busy="false" aria-colcount="4" class="table recipe-table">
      <thead role="rowgroup">
        <tr role="row">
          <th width="25%" role="columnheader" scope="col">{{trans('paso2.MEDICAMENTO')}}</th>
          <th width="13%" role="columnheader" scope="col">{{trans('paso2.Cantidad')}}</th>
          <th width="50%" role="columnheader" scope="col">{{trans('paso2.POSOLOGIA')}}</th>
          <th width="12%" role="columnheader" scope="col" class="recipe-table__action">{{trans('paso2.ACCION')}}</th>
        </tr>
      </thead>
      <tbody role="rowgroup">
        @foreach($detalles as $detalle)
          @php
            $desabilitar = $detalle->descargo == 1 ? 'disabled' : '';
          @endphp
          <tr role="row">
            <td data-label="Medicamento" role="cell">
              <span class="recipe-medication">
                <span class="recipe-medication__icon"><i class="fa fa-medkit" aria-hidden="true"></i></span>
                <span>{{$detalle->nombre}}</span>
              </span>
            </td>
            <td data-label="Cantidad" role="cell">
              <input class="form-control recipe-autosave-field recipe-quantity" min="1" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}', this)">
            </td>
            <td data-label="Posología" role="cell">
              <textarea wrap="soft" class="form-control recipe-autosave-field recipe-posology" rows="2" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}', this)">{{$detalle->dosis}}</textarea>
            </td>
            <td data-label="Acción" role="cell" class="recipe-table__action">
              @if($detalle->descargo == 0)
                <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm recipe-delete-button" title="Eliminar medicina" onclick="eliminar_medicina('{{$detalle->id}}', this);">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
              @else
                <span class="badge badge-success">{{trans('paso2.Entregado')}}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@else
  <div class="recipe-empty-state">
    <i class="fa fa-medkit" aria-hidden="true"></i>
    <span>No hay medicinas agregadas.</span>
  </div>
@endif
