<div class="table-responsive">

  <table  id="example2" class="table table-bordered"  aria-describedby="example2_info">
    <thead>
      <tr role="row" class="text-dark">
        <th>Codigo</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Medida</th>
        <th>Stock Minimo</th>
        <th>Forma de Despacho</th>
        <th>Registro Sanitario</th>
        <th>Marca</th>
        <th>Tipo de Producto</th>
        <th>Cantidad de Usos</th>
        <th>IVA</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($farmacia as $value)
        <tr role="row" class="odd">
          <td>{{$value->codigo}}</td>
          <td>{{$value->nombre}}</td>
          <!--<td>{{$value->marcas->nombre}}</td>-->
          <td>{{$value->descripcion}}</td>
          <td>{{$value->medida}}</td>
          <td>{{$value->minimo}}</td>
          <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>       
          <td>{{$value->registro_sanitario}}</td>
          <td>{{$value->marcas->nombre}}</td>
          <td>{{$value->tipo->nombre}}</td>
          <td>{{$value->usos}}</td>                  
          <td>@if(($value->iva)==1) NO @elseif(($value->iva)==0) SI  @endif</td> 
          <td> <a href="{{ route('hospital_admin.modaleditarp', ['id' => $value->id]) }}" data-toggle="modal" data-target="#modaleditar" class="btn btn-sm btn-warning">Ver</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>

</div>

