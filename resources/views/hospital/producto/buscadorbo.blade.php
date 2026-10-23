<table id="example2" class="table table-bordered table-hover dataTable col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
          <thead>
            <tr role="row" class="odd">
              <th  >Nombre</th>
              <th >Ubicacion</th>
              <th >Piso</th>
              <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Color</th>
              
            </tr>
          </thead>
          <tbody>
          	 @foreach ($bodega as $value)
                <tr role="row" class="odd">
                  <td> {{$value->nombre}}</td>
                  <td>{{$value->ubicacion }}</td>
                  <td>@if(($value->id_piso)==1)piso 1 @elseif (($value->id_piso)==2) piso 2 @elseif (($value->id_piso)==3) piso 3  @endif</td>
                  <td  bgcolor={{$value->color}}></td>
              </tr>
            @endforeach
        </tbody>
</table>