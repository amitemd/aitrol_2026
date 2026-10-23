@extends('contable.productos.base')
@section('action-content')

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item active">Producto y Servicios</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <h5><b>PRODUCTOS Y SERVICIOS</b></h5>
      </div>
      <div class="col-md-3 text-right">
        <button type="button" onclick="location.href='{{route('productos_servicios_crear')}}'" class="btn btn-primary btn-gray" >
          <i aria-hidden="true"></i>Agregar Producto o Servicio
        </button>
        <a class="btn btn-success" target="_blank" href="{{ route('productoNoLigados')}}"> REPORTE DE PRODUCTOS NO LIGADOS</a>

      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR PRODUCTOS Y SERVICIOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" action="{{route('productos_servicios_search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_codigo">Código: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="codigo" name="codigo" value="@if(isset($searchingVals)){{$searchingVals['codigo']}}@endif" autocomplete="off" placeholder="Ingrese codigo..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_nombre">Nombre: </label>
        </div>

        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombre" name="nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" autocomplete="off" placeholder="Ingrese el nombre..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_nombre">Opciones: </label>
        </div>
        <div class="form-group col-md-3">
            <select class="form-control" name="verificar" id="verificar">
              <option value="">Seleccione ...</option>
              <option value="1" @if(isset($verificar)) @if($verificar==1) selected @endif @endif >Ligados</option>
              <option value="2" @if(isset($verificar)) @if($verificar==2) selected @endif @endif >Sin ligar</option>
            </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_nombre">Tipo: </label>
        </div>
        <div class="form-group col-md-3">
          <select class="form-control" name="tipo" id="tipo">
            <option value="">{{trans('agenda.seleccione')}} ...</option>
            <option value="1" @if(isset($tipo)) @if($tipo==1) selected @endif @endif>Producto</option>
            <option value="0"@if(isset($tipo)) @if($tipo==0) selected @endif @endif>Servicios</option>
          </select>
        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
    </div>
    </form>
  </div>
  <div class="row head-title">
    <div class="col-md-12 cabecera">
      <label class="color_texto">PRODUCTOS Y SERVICIOS</label>
    </div>
  </div>
  <div class="box-body dobra">
    <div class="form-row">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr class="well-dark">
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Codigo de Barras</th>
                    <th>Descripcion</th>
                    <th>Grupo</th>
                    <th>Observacion</th>
                    <th>Estado</th>
                    <th>Accion</th>
                  </tr>
                </thead>
                <tbody style="background: #F9F9F9;">
                  @foreach ($productos as $value)
                    @php $verificar= DB::table('ct_productos_insumos')->where('id_producto',$value->id)->first(); @endphp
                  <tr>
                    <td>{{ $value->codigo }}</td>
                    <td>{{ $value->nombre }}</td>
                    <td>{{ $value->codigo_barra }}</td>
                    <td>{{ $value->descripcion }}</td>
                    <td>{{ $value->grupo }}</td>
                    <td>@if(is_null($verificar) || $verificar=='[]') Falta Ligar Producto. @else Sin observaciones. @endif</td>
                    <td>@if($value->estado_tabla == '1') Activo @else Inactivo @endif</td>
                    <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('productos_servicios_editar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                       <i class="glyphicon glyphicon-edit"></i>
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($productos)}} de {{$productos->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $productos->appends(Request::only(['verificar','codigo','nombre']))->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</section>

<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })
</script>
@endsection