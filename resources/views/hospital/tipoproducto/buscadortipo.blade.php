<table id="example2" class="table table-bordered table-hover dataTable col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
        <thead>
          <tr role="row">
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripcion</th>
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Estado</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($tipopro as $value)
                <tr role="row" class="odd">
                  <td> {{ $value->nombre}}</td>
                  <td>{{ $value->descripcion }}</td>
                  <td @if($value->estado==1) bgcolor='#00c853'  @elseif($value->estado==2) bgcolor='#d50000' @endif> @if($value->estado==1) ACTIVO @elseif($value->estado==2) INACTIVO @endif</td> 
              </tr>
            @endforeach
        </tbody>
</table>