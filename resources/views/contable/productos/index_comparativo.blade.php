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
        <h5><b> CONTROL DE PRODUCTOS</b></h5>
      </div>
      <!--       <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('productos_servicios_crear')}}'" class="btn btn-primary btn-gray" >
          <i aria-hidden="true"></i>Agregar Producto o Servicio
        </button>
      </div> -->
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" action="{{route('productos.comparar.index')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_codigo">Código: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="codigo" name="codigo" value="@if(isset($request)){{$request['codigo']}}@endif" autocomplete="off" placeholder="Ingrese codigo..." />
        </div>
        <div class="form-group col-md-5 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombre" name="nombre" value="@if(isset($request)){{$request['nombre']}}@endif" autocomplete="off" placeholder="Ingrese el nombre..." />
        </div>
        <div class="form-group col-md-2">
        <label class="texto"> Fecha Desde </label>
        </div>
        <div class="form-group col-md-2">
          <input class="form-control" type="date" id="fecha_desde" name="fecha_desde" value="@if(isset($fecha_desde)){{$fecha_desde}}@endif"/>
        </div>
        <div class="form-group col-md-2">
        <label class="texto"> Fecha Hasta </label>
        </div>
        <div class="form-group col-md-2">
          <input class="form-control" type="date" id="fecha_hasta" name="fecha_hasta" value="@if(isset($fecha_hasta)){{$fecha_hasta}}@endif"/>
        </div>
        <div class="col-md-12">  
          &nbsp;
        </div>
        <div class="col-xs-3">
          <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
    </div>
    </form>
  </div>
  <div class="row head-title">
    <div class="col-md-12 cabecera">
      <label class="color_texto">CONTROL PLANILLA</label>
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
                    <th>Id</th>
                    <th>Codigo</th>
                    <th>Observacion</th>
                    <th>Planilla</th>
                    <th>Procedimiento</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Usuario Aprueba</th>
                    <th>Fecha Aprueba</th>
                    <th>Accion</th>
                  </tr>
                </thead>
                <tbody style="background: #F9F9F9;">
                  @foreach ($planilla as $value)
                  <tr>
                    <td>{{$value->id}}</td>
                    <td>{{ $value->codigo }}</td>
                    <td>{{ $value->observacion }}</td>
                    <td>@if(isset($value->planilla)){{ $value->planilla->nombre }}@endif</td>
                    <td>@if(isset($value->procedimiento)) @if(isset($value->procedimiento->hc_procedimiento_final)) @if(isset($value->procedimiento->hc_procedimiento_final->procedimiento)) {{$value->procedimiento->hc_procedimiento_final->procedimiento->nombre}} @endif @endif @endif</td>
                    <td>{{ date('d/m/Y H:i:s',strtotime($value->fecha)) }}</td>
                    <td>@if(isset($value->usuario)) {{ $value->usuario->nombre1 }} {{ $value->usuario->apellido1 }} @endif</td>
                    <td> @if($value->aprobado==1) @if(isset($value->usuariomod)) {{$value->usuariomod->nombre1}} {{$value->usuariomod->apellido1}} @endif @endif</td>
                    <td>@if($value->aprobado==1) {{date('d/m/Y H:i:s',strtotime($value->updated_at))}} @endif</td>
                    <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('productos.edit_comparar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                        <i class="fa fa-eye"></i>
                      </a>
                      @if($value->aprobado == 0)
                      <button type="button" class="btn btn-danger btn-gray" onclick="accept('{{$value->id}}')"> <i class="fa fa-trash"></i> </button>
                      <a style="display: none;" href="{{route('productos.anular_comparativo',['id'=>$value->id])}}" id="a{{$value->id}}" class="btn btn-danger btn-gray"> <i class="fa fa-trash"></i> </a>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($planilla)}} de {{$planilla->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $planilla->appends(Request::only(['verificar','codigo','nombre','fecha_desde','fecha_hasta']))->links() }}
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
    'ordering': false,
    'info': false,
    'autoWidth': false
  })

  function accept(a) {
    var r = confirm("Desea eliminar el comparativo");
    if (r == true) {
      console.log('aceptado');
    /*   $("#a"+a).trigger("click"); */
      document.getElementById('a'+a).click();
    } else {
      
    }
  }
</script>
@endsection