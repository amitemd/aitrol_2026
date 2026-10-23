@extends('insumos.producto.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Lista de Productos</h3>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('producto_dar_baja')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Dar de Baja Producto
              </button>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('producto.create')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar Producto
              </button>
            </div>
            <!--<div class="col-md-2">
              <button type="button" onclick="location.href='{{route('producto.reporte')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Descargar Reporte
              </button>
            </div>-->
        </div>

      <!-- /.box-header -->
      <div class="box-body">
        <form method="POST" action="{{route('producto.search')}}">
          {{ csrf_field() }}
          @component('layouts.search', ['title' => 'Buscar'])
            @component('layouts.two-cols-search-row', ['items' => ['Codigo', 'Nombre'],
            'oldVals' => [isset($searchingVals) ? $searchingVals['codigo'] : '', isset($searchingVals) ? $searchingVals['nombre'] : '']])
            @endcomponent
            </br>
          @endcomponent
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Codigo</th>
                      <th >Codigo IESS</th>
                      <th >Nombre</th>
                      <th>Tipo de Producto</th>
                      <th >Marca</th>
                      <!--proveedor-->
                      <th >Cantidad</th>
                      <th >Stock Minimo</th>
                      <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($productos as $value)
                   
                      <tr>
                        <td >{{ $value->codigo }}</td>
                        <td >{{ $value->codigo_iess }}</td>
                        <td >{{ $value->nombre }}</td>
                        <td>{{ $value->tipo_produc->nombre }}</td>
                        <td >{{ $value->marca->nombre }}</td>
                       <!--proveedor-->
                        <td >{{ $value->cantidad }}</td>
                        <td >{{ $value->minimo }}</td>
                        <!--<td>{{ $value->precio_compra }}</td>-->
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('producto.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                          Actualizar
                          </a>
                          <a href="{{ route('producto.seguimiento', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                          Movimientos
                          </a>
                          @if($value->codigo_siempre == 1)
                            <a href="{{ route('imprimir.barras_unico', ['id' => $value->id]) }}" target="_blank" class="btn btn-success col-md-6 col-xs-6 btn-margin">
                            Imprimir Codigo
                            </a>
                          @endif
                        </td>
                      </tr>
                    @endforeach

                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($productos)}} de {{$productos->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $productos->links() }}
                </div>
              </div>
            </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </section>
  <!-- /.content -->

<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

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
</script>
@endsection
