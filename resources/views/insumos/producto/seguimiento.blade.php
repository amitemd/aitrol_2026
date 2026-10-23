@extends('insumos.producto.base')
@section('action-content')
<div class="header" style="background-color: white">
  <div class="col-md-12" style="text-align: right; padding-right: 60px;">
  <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
      <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
  </button>

  </div>
  <h4  style="text-align: center;"><b>MOVIMIENTOS POR PRODUCTO</b></h4>
  <h4  style="text-align: center;"><b>{{$producto->codigo}} - {{$producto->nombre}}</b></h4>
  <h4  style="text-align: center;"><b>Total de existencias: {{$producto->cantidad}}</b></h4>
</div>

<div class="body" style="background-color: white">
  <div class="row" style="padding: 10px;">
      <center>
        <div class="row">
          <div class="col-md-2">
            <label style="background-color: #59E100; color: black">&nbsp;Ingreso del producto&nbsp;</label>
          </div>
          <div class="col-md-2">
            <label style="background-color: yellow; color: black">&nbsp;Producto en transito&nbsp;</label>
          </div>
          <div class="col-md-3">
            <label style="background-color: #F05D36; color: black">&nbsp;Producto entregado a paciente&nbsp;</label>
          </div>
          <div class="col-md-2">
            <label style="background-color: black; color: white">&nbsp;Producto dado de baja&nbsp;</label>
          </div>
          <div class="col-md-3">
            <label style="background-color: orange; color: black">&nbsp;Eliminacion de Producto del pedido&nbsp;</label>
          </div>
        </div>
      </center>
         <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12" style="padding-left: 50px">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
               <tr >
                <th >Serie</th>
                <th >Nombre</th>
                <th >Cantidad</th>
                <th >Bodega</th>
                <th >Transaccion</th>
                <th >Fecha</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($productos as $value)
                <tr>
                  <td >{{ $value->serie }}</td>
                  <td >{{$value->nombre}}</td>
                  <td >{{ $value->cantidad_total }}</td>
                  <td >{{ $value->nombre_bodega }}</td>
                  @if($value->observacion == 'Ingreso del producto') <td style="background-color: #59E100; color: black">{{ $value->observacion }}</td> @elseif($value->observacion == 'Producto en Transito') <td style="background-color: yellow; color: black">{{ $value->observacion }}</td> @elseif($value->observacion == 'Producto dado de baja') <td style="background-color: black; color: white">{{ $value->observacion }}</td> @elseif($value->observacion == 'Producto entregado a paciente') <td style="background-color: #F05D36; color: black">{{ $value->observacion }}</td> @elseif($value->observacion == 'Eliminacion de Producto de pedido') <td style="background-color: orange; color: black">{{ $value->observacion }}</td> @endif
                  <td >{{ $value->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5" style="padding-left: 30px">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($productos)}} de {{$productos->total()}} Registros</div>
        </div>
        <div class="col-sm-7" style="padding-right: 30px">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{$productos->links()}}
          </div>
        </div>
      </div>
    </div>

    </div>
</div>

<script type="text/javascript">

    $(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });

    function goBack() {
      window.history.back();
    }

</script>
@endsection