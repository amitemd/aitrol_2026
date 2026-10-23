
      <table id="example2" class="table table-striped col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px; margin-top: 20px;">
        <thead>
          <tr role="row">
          <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Código</th>
            <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
            <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripción</th>
            <th width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Estado</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Medida</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Stock Minimo</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Forma de Despacho</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Registro Sanitario</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Marca</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo de Producto</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Cantidad de Usos</th>
            <th  width="7.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >IVA</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($codigo as $value)
                <tr role="row" class="odd">
                <td>{{$value->codigo}}</td>
                  <td> {{$value->nombre}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td @if($value->estado==1) bgcolor='#69f0ae'  @elseif($value->estado==2) bgcolor='#d32f2f' @endif> @if($value->estado==1)  ACTIVO   @elseif($value->estado==2)  INACTIVO @endif</td> 
                  <td>{{$value->medida}}</td>
                  <td>{{$value->minimo}}</td>
                  <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>
                  <td>{{$value->registro_sanitario}}</td>
                  <td>{{$value->marcas->nombre}}</td>
                  <td>{{$value->tipo->nombre}}</td>
                  <td>{{$value->usos}}</td>                  
                  <td>@if(($value->iva)==1) NO @elseif(($value->iva)==0) SI  @endif</td>                 
              </tr>
            @endforeach
        </tbody>
      </table>
