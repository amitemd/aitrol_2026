@extends('hospital_admin.base')
@section('action-content')
<!-- AGREGAR MEDICIANA -->
<div class="modal fade" id="modalmarcas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<a type="button" href="{{ route('hospital_admin.modalproducto')}}" class="btn btn-sm btn-info my-2" data-toggle="modal" data-target="#modalmarcas"><i class="fas fa-plus"></i> Agregar medicina</a>
<!--fin AGREGAR MEDICIANA -->

<div class="modal fade" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<a type="button" href="{{ route('hospital_admin.darbaja')}}" class="btn btn-sm btn-danger my-2"><i class="far fa-arrow-alt-circle-down"></i> Dar de baja la medicina </a>
<a type="button"  href="{{ route('hospital_admin.farmacia')}}" class="btn btn-primary btn-sm"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<!-- CONTENT ROW -->
<div class="row">
  <div class="col-md-12">
    
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Producto</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <tbody>
                <tr class="text-dark">
                  <th>Codigo</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Indicaciones Medicina</th>
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
                @foreach ($producto as $value)
                  <tr role="row" class="odd">
                    <td>{{$value->codigo}}</td>
                    <td> {{$value->nombre}}</td>
                    <td>{{$value->descripcion}}</td>
                    <td>{{$value->indicaciones_medicina}}</td>
                    <td>{{$value->medida}}</td>
                    <td>{{$value->minimo}}</td>
                    <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Medicina @endif</td>
                    <td>{{$value->registro_sanitario}}</td>
                    <td>{{$value->marcas->nombre}}</td>
                    <td>{{$value->tipo->nombre}}</td>
                    <td>{{$value->usos}}</td>                  
                    <td>@if(($value->iva)==1) NO @elseif(($value->iva)==0) SI  @endif</td> 
                    <td>
                      <a href="{{ route('hospital_admin.modaleditarp', ['id' => $value->id]) }}" data-toggle="modal" data-target="#modaleditar" class="btn btn-warning">Actualizar</a>
                      <a href="{{ route('hospital_admin.movientop',['id' =>$value->id])}}" class="btn btn-warning">Movimiento</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!--aqui va el paginate-->
            {{ $producto->links()}}
          <!--Fin de la paginacion-->
        </div>
      </div>
    </div>

  </div>
</div>
<!--FINAL / CONTENT ROW-->

<!-- CONTENT ROW -->
<div class="row">
  <div class="col-md-12">
    
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Movimientos máster productos</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <tbody>
                <tr class="text-dark">
                  <th>Serie</th>
                  <th>Nombre</th>
                  <th>Cantidad</th>
                  <th>Bodega</th>
                  <th>Transaccion</th>
                  <th>Fecha</th>
                  <th>Modificado por</th>
                </tr>
                @foreach ($movimiento as $values)
                  <tr>
                    <td>{{ $values->movimiento->serie}}</td>
                    <td>{{$values->producto->nombre}}</td>
                    <td>{{ $values->movimiento->cantidad }}</td>
                    <td>{{$values->movimiento->bodega->nombre}}</td>
                    @if($values->observacion == 'Ingreso del producto') 
                    <td style="background-color: #59E100; color: black">{{ $values->observacion }}</td> 
                    @elseif($values->observacion == 'Producto en Transito') 
                    <td style="background-color: yellow; color: black">{{ $value->observacion }}</td> 
                    @elseif($value->observacion == 'Producto dado de baja') 
                    <td style="background-color: black; color: white">{{ $values->observacion }}</td> 
                    @elseif($values->observacion == 'Producto entregado a paciente') 
                    <td style="background-color: #F05D36; color: black">{{ $values->observacion }}</td> 
                    @elseif($values->observacion == 'Eliminacion de Producto de pedido') 
                    <td style="background-color: orange; color: black">{{ $values->observacion }}</td> 
                    @endif
                    <td>{{ $values->created_at}}</td>
                    <td>{{$values->updated_at}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!--aqui va el paginate-->
            {{ $producto->links()}}
          <!--Fin de la paginacion-->
        </div>
      </div>
    </div>

  </div>
</div>
<!--FINAL / CONTENT ROW-->

<script type="text/javascript">
  $('#modalmarcas').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
</script>
<script type="text/javascript">
  $('#modaleditar').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
</script>
@endsection