@extends('contable.rubros_clientes.base')
@section('action-content')

<script type="text/javascript">  
  $(function () {    
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
  });    
</script>

<section class="content">
    <nav aria-label="breadcrumb"> 
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rubros Clientes</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header">
          <div class="col-md-9">
            <h5><b>RUBROS CLIENTES</b></h5>
          </div>
          <div class="col-md-1 text-right">
            <button onclick="location.href='{{route('rubros_cliente.create')}}'" class="btn btn-success btn-gray" >
              <i aria-hidden="true"></i>Agregar Nuevo Rubro
              </button>
          </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">BUSCADOR DE RUBROS CLIENTES</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('rubros_cliente.search') }}" >
          {{ csrf_field() }}
              <div class="form-group col-md-2 col-xs-2">
                  <label class="texto" for="identificacion">Código</label>
              </div>
              <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
                    <input class="form-control" type="text" id="codigo" name="codigo" value="@if(isset($searchingVals)){{$searchingVals['codigo']}}@endif" autocomplete="off" placeholder="Ingrese Código.."  />
              </div>
              <div class="form-group col-md-2 col-xs-2">
                  <label class="texto" for="identificacion">Nombre</label>
              </div>
              <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
                    <input class="form-control" type="text" id="nombre" name="nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" autocomplete="off" placeholder="Ingrese Nombre.."  />
              </div>
              <div class="col-xs-1">
                <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                </button>
              </div>
        </form>
      </div>
      <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" >LISTADO DE RUBROS CLIENTES<label>
          </div>
      </div> 
      <div class="box-body dobra">
        <div class="form-row">
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th >Código</th>
                        <th >Nombre</th>
                        <th >Cuenta Debe</th>
                        <th >Cuenta Haber</th>
                        <th >Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($rubros as $value)
                        <tr class="well">                          
                          <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                          <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                          @php
                            $debe = \Sis_medico\Plan_Cuentas::find($value->debe);
                            $haber = \Sis_medico\Plan_Cuentas::find($value->haber);
                          @endphp
                          <td>@if(!is_null($debe->nombre)){{$debe->nombre}}@endif</td>
                          <td>@if(!is_null($haber->nombre)){{$haber->nombre}}@endif</td>
                          <td>
                            @php
                              $codigo = $value->codigo;
                              $codigo = str_replace("/", "_", $codigo);
                              //dd($codigo);

                            @endphp
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <a href="{{route('rubros_cliente.editar', ['id' => $codigo])}}" class="btn btn-warning col-md-6 col-xs-6 btn-gray">
                              Actualizar
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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($rubros->currentPage() - 1) * $rubros->perPage())}} / {{count($rubros) + (($rubros->currentPage() - 1) * $rubros->perPage())}} de {{$rubros->total()}} registros
                    </div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $rubros->appends(Request::only(['codigo','nombre']))->links() }}
                  </div>
                </div>
              </div>
            </div>  
          </div>
        </div>
      </div>
    </div>
</div>

</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    
    $(document).ready(function(){
      
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });
    
    });

</script>

@endsection
