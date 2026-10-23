@extends('hospital_admin.base')
@section('action-content')

<div class="modal fade" id="modalmarcas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<a type="button" href="{{ route('htransito.transitoag')}}" class="btn btn-sm btn-info my-2" data-toggle="modal" data-target="#modalmarcas"><i class="fas fa-plus"></i> AGREGAR PRODUCTO A TRANSITO</a>

<div class="modal fade" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<a type="button" href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="card shadow mb-4">
  <!-- Card Header - Accordion -->
  <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
    <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
  </a>
  <!-- Card Content - Collapse -->
  <div class="collapse show" id="collapseCardExample" style="">
    <div class="card-body">
      
    <div class="table-responsive col-md-12">
      <table class="table table-bordered">
        <thead>
          <tr role="row">
            <th>Fecha</th>
            <th>Código</th>
            <th>Nombre Encargado</th>
            <th>Nombre del Producto</th>
            <th>Cantidad</th>
            <th>Acción</th> 
          </tr>
        </thead>
        <tbody>
          @foreach ($productos as $value)
            <tr>
              <td>{{ $value->fecha }}</td>
              <td>{{ $value->serie }}</td>
              <td>@if(!is_null($value->nombre)) {{$value->nombre}} {{$value->apellido1}} {{$value->apellido2}} @endif </td>
              <td>{{ $value->producto_nombre }}</td>
              <td>@if($value->tipo == 2) En Transito @endif</td>
              <td><a class="btn btn-sm btn-warning" href="{{ route('hospital_admin.producto') }}">Editar</a></td>
            <tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>
  </div>
</div>

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