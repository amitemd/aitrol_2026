@extends('contable.productos_tarifario.base')
@section('action-content')
<section class="content">
  <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="form-group col-md-6 ">
              <div class="row" >
                <div class="form-group col-md-10 ">

                </div>
              </div>
              </div>
          </div>
        <form  method="POST" action="{{route('productos.buscar')}}">
          {{ csrf_field() }}
          <div class="form-group col-md-6 ">
            <div class="row" >
              <div class="form-group col-md-10 ">
                <label for="nombre" class="col-md-4 control-label">Nombre:</label>
                <div class="col-md-8">
                  <input id="nombre" maxlength="100" type="text" class="form-control input-sm" name="nombre" value="{{$nombre}}" >
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-2 ">
            <div class="col-md-7">
                <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
            </div>
          </div>
        </form>
        <div class="row">
          <div class="col-md-2">
            <div class="col-md-7">
              <a id="crear" class="btn btn-success" href="{{route('productos.crear')}}">Crear Registro </span></a>
            </div>
          </div>
        </div>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <td><b>Productos</b></td>
                      @foreach($seg as $value)
                        <td><b>{{substr($value->seguro->nombre, 0,2)}}.{{$value->nivel}}</b></td>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($productos as $producto)
                    <tr>
                      <td>{{$producto->nombre}}</td>
                        @foreach($seg as $value)
                          @php
                          $validacion =Sis_medico\Ct_Productos_Tarifario::where('id_producto',$producto->id)->where('id_seguro',$value->id_seguro)->where('nivel',$value->nivel)->first();
                          @endphp
                          @if(is_null($validacion))
                          <td>0.00 <a href="{{ route('productos.edit2', ['id_producto' => $producto->id, 'id_seguro' => $value->id_seguro])}}" class="btn btn-warning btn-xs"  style="float: right;"> <span class="glyphicon glyphicon-edit"></span></a></td>
                          @else
                          <td>{{$validacion->precio_producto}} <a href="{{ route('productos.edit', ['id' => $producto->id])}}" class="btn btn-warning btn-xs"  style="float: right;"> <span class="glyphicon glyphicon-edit"></span></a></td>
                          @endif
                        @endforeach
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($productos->currentPage() - 1) * $productos->perPage())}} / {{count($productos) + (($productos->currentPage() - 1) * $productos->perPage())}} de {{$productos->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $productos->appends(Request::only(['id', 'nombre']))->links() }}
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

</script>
@endsection
