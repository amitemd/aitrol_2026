<div class="box-body">
  <table id="example2" class="table table-striped col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
    <thead>
      <tr role="row">
        <th width="33.3%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
        <th width="33.3%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripción</th>
        <th width="33.3%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($marcas as $value)
          <tr role="row" class="odd">
            <td> {{ $value->nombre}}</td>
            <td>{{ $value->descripcion }}</td>
            <td @if($value->estado==1) bgcolor='#69f0ae'  @elseif($value->estado==2) bgcolor='#d32f2f' @endif> @if($value->estado==1)  ACTIVO   @elseif($value->estado==2)  INACTIVO @endif</td> 
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

