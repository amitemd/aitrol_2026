<table id="example2" class="table table-striped col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px; margin-top: 20px;">
  <thead>
    <tr role="row">
    <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Código</th>
      <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
      <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Fecha de Caducidad</th>
      <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Cantidad</th>
      <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Bodega/Transito</th>
      <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Acción</th>
    </tr>
  </thead>
  <tbody>
      @foreach ($producto as $value)
          <tr role="row" class="odd">
          <td>{{$value->serie}}</td>
            <td>{{$value->descripcion}}</td>
            <td>{{$value->fecha_vencimiento}}</td>
              <td>{{$value->cantidad}}</td>
            <td>{{$value->usos}}</td>
            <td> <a href="{{ route('hospital_admin.modalbaja', ['cant' => $value->cantidad_total, 'tipo' => $value->tipo, 'id_producto' => $value->id_producto, 'serie' => $value->serie, 'id_bodega' => $value->id_bodega, 'id_pedido' => $value->id_pedido, 'f_vencimiento' => $value->fecha_vencimiento, 'lote' => $value->lote])}}" class="btn btn-warning col-md-6 col-xs-6 btn-margin" data-toggle="modal" data-target="#modals">
              Descontar
              </a></td>                             
        </tr>
      @endforeach
      </tbody>
</table>