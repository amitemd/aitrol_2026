@extends('contable.rubros_acreedores.base')
@section('action-content')
<style type="text/css">

</style>
<script type="text/javascript">  

$(function () {    
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});    
</script>
<div class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Rubro</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro de Rubros Acreedores</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">Rubros Acreedores</h3>
            </div>

            <div class="col-md-1 text-right">
              <button onclick="location.href='{{route('rubrosa.create')}}'" class="btn btn-success" >
                <i aria-hidden="true"></i>Agregar Nuevo Rubro
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE RUBROS</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('rubrosa.search') }}" >
            {{ csrf_field() }}
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="identificacion">Código</label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="codigo" name="codigo"  placeholder="Ingrese Identificación..."  />
                </div>
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="identificacion">Proveedor: </label>
                   
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
                  <input type="text" class="form-control" id="nombre_proveedor" name="nombre_proveedor" placeholder="Nombre proveedor...">
                </div>
                <div class="col-xs-1">
                  <button type="submit" id="buscarEmpleado" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE RUBROS (NOTA DE DEBITO)</label>
            </div>
        </div> 
        <div class="box-body dobra">
          <div class="form-group col-md-12">
            <div class="form-row">
                <div id="resultados">
                </div>  
                <div id="contenedor">
                  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                          <thead>
                            <tr >
                              <th >Código</th>
                              <th >Nombre</th>
                              <th >Cuenta Debe</th>
                              <th >Cuenta Haber</th>
                              <th >Acción</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($rubros as $value)
                              <tr>
                                <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                                <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                                @php
                                  $debe = \Sis_medico\Plan_Cuentas::find($value->debe);
                                  $haber = \Sis_medico\Plan_Cuentas::find($value->haber);
                                @endphp
                                <td>@if(!is_null($debe->nombre)){{$debe->nombre}}@endif</td>
                                <td>@if(!is_null($haber->nombre)){{$haber->nombre}}@endif</td>
                                <td>
                                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <a href="{{route('rubrosa.editar', ['id' => $value->codigo])}}" class="btn btn-warning col-md-6 btn-gray col-xs-6 btn-margin">
                                    Actualizar
                                    </a>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                          <tfoot>
                          </tfoot>
                      </table>
                        </div>
                      </div>

                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
   $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': true,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });

  });


</script>

@endsection
