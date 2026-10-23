         <table id="example2" class="table table-hover dataTable">
            <thead>
              <tr role="row">
                <th>Marca</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Medida</th>
                <th>Stock Minimo</th>
                <th >Forma de Despacho</th>
                <th>Registro Sanitario</th>
                <th>Tipo de Producto</th>
                <th>Cantidad de Usos</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($farmacia as $value)
                <tr role="row" class="odd">
                  <td>{{$value->marcas->nombre}}</td>
                  <td>{{$value->codigo}}</td>
                  <td>{{$value->nombre}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td>{{$value->medida}}</td>
                  <td>{{$value->minimo}}</td>
                  <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>  
                  <td>{{$value->registro_sanitario}}</td>
                  <td>{{$value->tipo->nombre}}</td>
                  <td>{{$value->usos}}</td>                          
                </tr>
              @endforeach
            </tbody>
          </table>