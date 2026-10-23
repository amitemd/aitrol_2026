@extends('insumos.producto.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-4">
              <h3 class="box-title">{{trans('winsumos.lista_productos')}}</h3>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('producto_dar_baja')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>{{trans('winsumos.dar_baja_producto')}}
              </button>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('producto.create')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>{{trans('winsumos.aggnuevoprod')}}
              </button>
            </div>
            <div class="col-md-2">
              <a type="button" target="_blank" href="{{route('importar.masivoProductoRe')}}" class="btn btn-success">
                   <i aria-hidden="true"></i>{{trans('winsumos.reportproductos')}}
              </a>
            </div>
	          <div class="col-md-2">
              <a type="button" target="_blank" href="{{route('producto.reporte_listado_productos')}}" class="btn btn-warning">
                   <i aria-hidden="true"></i>{{trans('winsumos.listado_producto')}}
              </a>
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
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="inputcodigo" class="col-sm-3 control-label">{{trans('winsumos.codigo')}}</label>
              <input type="text" name="codigo" id="inputcodigo" class="form-control" placeholder="Codigo" value="@if(isset($searchingVals['codigo'])){{$searchingVals['codigo']}}@endif">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="inputcodigo_referencial" class="col-sm-4 control-label">{{trans('winsumos.codigo_referencial')}}</label>
              <input type="text" name="codigo_referencial" id="inputcodigo_referencial" class="form-control" placeholder="{{trans('winsumos.codigo')}} Referencial" value="@if(isset($searchingVals['codigo_iess'])){{$searchingVals['codigo_iess']}}@endif">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="inputnombre" class="col-sm-3 control-label">{{trans('winsumos.nombre')}}</label>
              <input type="text" name="nombre" id="inputnombre" class="form-control" placeholder="{{trans('winsumos.nombre')}}" value="@if(isset($searchingVals['nombre'])){{$searchingVals['nombre']}}@endif">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="inputnombre" class="col-sm-3 control-label">{{trans('winsumos.estado')}}</label>
              <select class="form-control" name="estado" id="estado">
                <option @if(isset($searchingVals)) @if($searchingVals['estado']==1) selected @endif @endif value="1">{{trans('winsumos.activo')}}</option>
                <option @if(isset($searchingVals)) @if($searchingVals['estado']==0) selected @endif @endif value="0">{{trans('winsumos.inactivo')}}</option>
              </select>
            </div>
          </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="inputtipo" class="col-sm-9 control-label">{{trans('winsumos.tipo_producto')}}</label>
                <select name="tipo_producto" id="inputtipo" class="form-control">
                  <option value="">{{trans('winsumos.todos')}}...</option>
                  @foreach($tipos as $tipo)
                  <option value="{{$tipo->id}}" @if(isset($searchingVals)) @if($tipo->id==$searchingVals['tipo_producto']) selected @endif @endif>{{$tipo->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                 {{trans('winsumos.Buscar')}}
              </button>
            </div>
          </div>
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th>{{trans('winsumos.codigo')}}</th>
                      <th>{{trans('winsumos.codigo_referencial')}}</th>
                      <th>{{trans('winsumos.nombre')}}</th>
                      <th>{{trans('winsumos.tipo_producto')}}</th>
                      <th>{{trans('winsumos.marca')}}</th>
                      <!--proveedor-->
                      <!--th >Cantidad</th-->
                      <th>{{trans('winsumos.stock_minimo')}}</th>
                      <th>{{trans('winsumos.estado')}}</th>
                      <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->
                      @if($empresa->imagen_producto == 1)
                      <th> {{trans('winsumos.Imagen')}} </th>
                      @endif
                      <th>{{trans('winsumos.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($productos as $value)

                      <tr>
                        <td >{{ $value->codigo }}</td>
                        <td >{{ $value->codigo_iess }}</td>
                        <td >{{ $value->nombre }}<br>@if( $value->kardex_uso)<b>Uso kardex:</b> {{$value->cantidad_usos_kardex }}@endif</td>
                        <td>@if(isset($value->tipo_produc))

                            {{  $value->tipo_produc->nombre  }}
                              @endif
                            </td>
                        <td >@if(isset($value->marca )) {{ $value->marca->nombre }} @endif</td>
                       <!--proveedor-->
                        <!--td >{{ $value->cantidad }}</td-->
                        <td >{{ $value->minimo }}</td>
                        <td>{{$value->estado == 0 ? 'INACTIVO' : 'ACTIVO'}}</td>
                        <!--<td>{{ $value->precio_compra }}</td>-->
                        @if($empresa->imagen_producto == 1)
                        <td>
                            @if(!is_null($value->imagen_producto))
                            <input type="hidden" name="imagen_producto" value="@if($value->imagen_producto=='') {{$value->imagen_producto='producto_.jpg'}} @endif">
                            <img src="{{asset('/avatars').'/'.$value->imagen_producto}}"  style="width:150px;height:120px;" id="imagen_producto">
                            @endif
                        </td>
                        @endif
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('producto.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                          {{trans('winsumos.actualizar')}}
                          </a>
                          <!--<a href="{{ route('producto.seguimiento', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                          Movimientos
                          </a>-->
                          @if($value->codigo_siempre == 1)
                            <a href="{{ route('imprimir.barras_unico', ['id' => $value->id]) }}" target="_blank" class="btn btn-success col-md-6 col-xs-6 btn-margin">
                            {{trans('winsumos.imprimir_codigo')}}
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} {{1 + (($productos->currentPage() - 1) * $productos->perPage())}} / {{count($productos) + (($productos->currentPage() - 1) * $productos->perPage())}} de {{$productos->total()}} {{trans('winsumos.registros')}}</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $productos->appends(Request::only(['codigo', 'nombre', 'tipo_producto', 'estado']))->links() }}
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
