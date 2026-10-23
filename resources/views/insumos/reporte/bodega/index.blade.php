@extends('insumos.reporte.bodega.base')
@section('action-content')
    <!-- Main content -->
<section class="content">
  <div class="box">
    <form method="POST" id="form_reporte" action="{{route('reporte.reporte_producto_bodega')}}" >
      {{ csrf_field() }}
      <div class="box-header">
        <div class="row">
            <div class="col-sm-10"></div>
            <div class="col-sm-2">
              <button type="button" onclick="enviar_datos()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos_reportes.descargar')}}&nbsp;</span>
              </button>
            </div>
        </div>
      </div>
      <div class="box-body">
        <h4 class="col-md-12" style="text-align: center;"><b>{{trans('winsumos_reportes.reporte_productos_existente_bodega')}}</b></h4>
          <div class="col-md-12">
            <div class="col-md-3"></div>
            <div class="col-md-9" style="padding-top: 5px;">
              <div class="row">
                <div class="col-md-3" >
                  <div class="">
                    <input value="@if($codigo!=''){{$codigo}}@endif" type="text" class="form-control form-control-sm " name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo_producto')}}" style="text-transform:uppercase;"  >
                  </div>
                </div>
                <div class="col-md-3" >
                  <div class="">
                    <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control form-control-sm " name="nombres" id="nombres" placeholder="{{trans('winsumos.ingrese_nombre_producto')}}" style="text-transform:uppercase;"  >
                  </div>
                </div>
                <div class="col-md-3">
                    <select name="bodega" id="bodega" class="form-control">
                        <option value="">{{trans('winsumos.seleccione')}}</option>
                        @foreach($bodegas as $v)
                          <option  @if($v->id==$bodega) selected @endif value="{{$v->id}}">{{$v->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3" >
                  <button type="submit" class="btn btn-primary" style="color:white;  border-radius: 5px; border: 2px solid white;" formaction="{{ route('reporte.reporte_bodega') }}"> <i class="fa fa-search" aria-hidden="true">
                  </i> &nbsp;{{trans('winsumos.Buscar')}}&nbsp;</button>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr >
                  <th >{{trans('winsumos.codigo')}}</th>
                  <th >{{trans('winsumos.nombre')}}</th>
                  <th >{{trans('winsumos.descripcion')}}</th>
                  <th >{{trans('winsumos.serie')}}</th>
                  <th >{{trans('winsumos.pedidos')}}</th>
                  <th >{{trans('winsumos.bodegas')}}</th>
                  <th >{{trans('winsumos.proveedores')}}</th>
                  <th >{{trans('winsumos.marca')}}</th>
                  <th >{{trans('winsumos.tipo_producto')}}</th>
                  <th >{{trans('winsumos_reportes.cantidad_en_bodega')}}</th>
                  <th >{{trans('winsumos.lote')}}</th>
                  <th >{{trans('winsumos.Fecha')}}</th>
                  <th >{{trans('winsumos.fecha_vencimiento')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($productos as $value)
                  <tr>
                    <td >{{$value->codigo}}</td>
                    <td >{{$value->nombre_producto}}</td>
                    <td >{{$value->descripcion}}</td>
                    <td >{{$value->serie}}</td>
                    <td >{{$value->pedido}}</td>
                    <td >{{$value->nombre_bodega}}</td>
                    <td >{{$value->nombrecomercial}}</td>
                    <td >{{$value->nombre_marca}}</td>
                    <td >{{$value->nombre_tipo}}</td>
                    <td >{{$value->cantidad}}</td>
                    <td>{{$value->lote}}</td>
                    <td >{{$value->fecha}}</td>
                    <td>{{$value->fecha_vence}}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
              </tfoot>
            </table>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 {{trans('winsumos.a')}} {{count($productos)}} {{trans('winsumos.de')}} {{$productos->total()}} {{trans('winsumos.registros')}}</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $productos->appends(Request::only(['nombre','codigo','nombres']))->links() }}
              </div>
            </div>
          </div>
      </div>
    </form>
  </div>
</section>

<script type="text/javascript">
  $(function () {
    $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        'order'       : [[ 6, "asc" ]]
      });
  });
  function enviar_datos(){
    $('#form_reporte').submit();
  }  
</script>

@endsection