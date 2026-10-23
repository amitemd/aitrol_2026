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
        <h5><b>PRODUCTOS SALDOS</b></h5>
        @if($errors->any())
        <span class="badge badge-danger">{{$errors->first()}}</span>
         @endif
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('productos.create_saldos')}}'" class="btn btn-primary btn-gray">
          <i aria-hidden="true"></i>Agregar Producto o Servicio
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR SALDOS PRODUCTOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" action="{{route('productos.saldos_iniciales')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_codigo">Código: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control select2" name="id_producto" id="id_producto">
            <option value=""> Seleccione ...</option>
            @foreach($productos as $p)
            <option @if($id_producto==$p->id) selected="selected" @endif value="{{$p->id}}"> {{$p->codigo}} {{$p->nombre}} </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_nombre">Descripcion: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input type="text" name="descripcion" placeholder="Ingrese concepto" value="{{$descripcion}}" class="form-control">
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
                    <th>Producto</th>
                    <th>Descripcion</th>
                    <th>Fecha</th>
                    <th>Observacion</th>
                    <th>Estado</th>
                    <th>Accion</th>
                  </tr>
                </thead>
                <tbody style="background: #F9F9F9;">
                  @foreach ($saldos as $value)
                    <td>@if(isset($value->product)) {{$value->product->codigo}} {{$value->product->nombre}} @endif</td>
                    <td>{{$value->descripcion}}</td>
                    <td>{{$value->fecha}}</td>
                    <td>{{$value->nota}}</td>
                    <td>@if($value->estado==1) ACTIVO @else INACTIVO @endif</td>
                    <td> <a href="{{ route('productos.edit_saldos', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                       <i class="glyphicon glyphicon-edit"></i>
                      </a></td>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($saldos)}} de {{$saldos->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $saldos->links() }}
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
  $(document).ready(function() {
    $('.select2').select2({
      tags: false
    });
  });
</script>
@endsection