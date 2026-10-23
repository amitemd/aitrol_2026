@extends('hospital_admin.base')
@section('action-content')

<a type="button" href="{{ route('hospital_admin.producto')}}"  class="btn btn-sm btn-primary my-3"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="row">
  <div class="col-md-12">

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de producto</h6>
        <h4  style="text-align: center;"><b>MOVIMIENTOS POR PRODUCTO</b></h4>
        <h4  style="text-align: center;"><b>{{$producto->codigo}} - {{$producto->nombre}}</b></h4>
        <h4  style="text-align: center;"><b>Total de existencias: {{$producto->cantidad}}</b></h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">

          <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <div class="row">
              <div class="col-sm-12 col-md-6">
                <span class="badge badge-success">Ingreso del producto</span>
                <span class="badge badge-warning">Producto en transito</span>
              </div>
              <div class="col-sm-12 col-md-6">
                <span class="badge badge-danger">Producto entregado a paciente</span>
                <span class="badge badge-dark">Producto dado de baja</span>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                  <thead>
                    <tr role="row">
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Serie: activate to sort column ascending" style="width: 156px;">Serie</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Nombre: activate to sort column ascending" style="width: 240px;">Nombre</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Cantidad: activate to sort column ascending" style="width: 112px;">Cantidad</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Bodega: activate to sort column ascending" style="width: 49px;">Bodega</th>
                      <th class="sorting_desc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Transaccion: activate to sort column ascending" style="width: 104px;" aria-sort="descending">Transaccion</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Fecha: activate to sort column ascending" style="width: 94px;">Fecha</th>
                    </tr>
                  </thead>
                  <tbody>              
                    @foreach ($productos as $value)
                      <tr role="row" class="odd">
                        <td >{{ $value->serie }}</td>
                        <td >{{$value->nombre}}</td>
                        <td >{{ $value->cantidad_total }}</td>
                        <td >{{ $value->nombre_bodega }}</td>
                        @if($value->observacion == 'Ingreso del producto')
                        <td style="background-color: #59E100; color: black">{{ $value->observacion }}</td> 
                        @elseif($value->observacion == 'Producto en Transito') 
                        <td style="background-color: yellow; color: black">{{ $value->observacion }}</td> 
                        @elseif($value->observacion == 'Producto dado de baja') 
                        <td style="background-color: black; color: white">{{ $value->observacion }}</td> 
                        @elseif($value->observacion == 'Producto entregado a paciente') 
                        <td style="background-color: #F05D36; color: black">{{ $value->observacion }}</td> 
                        @elseif($value->observacion == 'Eliminacion de Producto de pedido') 
                        <td style="background-color: orange; color: black">{{ $value->observacion }}</td> 
                        @endif
                        <td >{{ $value->created_at }}</td>
                      </tr>
                    @endforeach
                    </tbody>
                </table>
              </div>
            </div>

          </div>
        
        </div>
      </div>
    </div>

  </div>
</div>

@endsection