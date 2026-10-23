@extends('contable.debito_acreedores.base')
@section('action-content')
<style type="text/css">

</style>
<script type="text/javascript">
  $(function() {
    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Debito Acreedores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Registro Debito Acreedores</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">Nota de Débito Acreedores</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('debitoacreedores.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE NOTA DE DÉBITO</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('debitoacreedores.search') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Id</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Identificación..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Proveedor: </label>
        </div>
        <div class="form-group col-md-3 col-xs-4 container-4">
          <select class="form-control select2_find_proveedor" style="width: 100%;" name="id_proveedor" id="id_proveedor">
            
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Serie</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="serie" name="serie" value="@if(isset($searchingVals)){{$searchingVals['serie']}}@endif" placeholder="Ingrese Identificación..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Secuencia</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Identificación..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Concepto</label>
        </div>
        <div class="form-group col-md-3 col-xs-3 container-4">
          <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese concepto..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">Asiento</label>
        </div>
        <div class="form-group col-md-3 col-xs-3 container-4">
          <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese asiento..." />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha Factura </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha_debito" class="form-control fecha" id="fecha_debito" value="@if(isset($searchingVals)){{$searchingVals['fecha_factura']}}@endif">
          </div>
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
        <label class="color_texto">LISTADO DE DÉBITOS</label>
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
                  <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Concepto</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Factura</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Proveedor</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($nota_debito as $value)
                      @php 
                      $tot = $value->id;
                      $conc = str_pad($tot, 10, "0", STR_PAD_LEFT);
                      @endphp
                      <tr class="well">
                        <td> @if(!is_null($value->secuencia)) {{$value->secuencia}}  @elseif (empty($value->secuncia)) {{$conc}}  @endif</td>
                        <td>@if(!is_null($value->id_asiento_cabecera)) {{$value->id_asiento_cabecera}} @endif</td>
                        <td> @if(!is_null($value->concepto)) {{$value->concepto}} @endif</td>
                        <td> @if(!is_null($value->fecha_factura)) {{$value->fecha_factura}} @endif</td>
                        <td>@if(!is_null($value->proveedor)){{$value->proveedor->nombrecomercial}}@endif</td>
                        <td>{{$value->valor_contable}}</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td> <a href="{{route('debitoacreedores.anular',['id'=>$value->id])}}" class="btn btn-warning btn-gray"><i class="fa fa-trash"></i></a> <a class="btn btn-success btn-gray" href="{{route('debitoacreedores.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a> 
                        <a class="btn btn-danger btn-gray" target="_blank" href="{{route('pdf_debito_acreedores.pdf',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>

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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($nota_debito->currentPage() - 1) * $nota_debito->perPage())}} / {{count($nota_debito) + (($nota_debito->currentPage() - 1) * $nota_debito->perPage())}} de {{$nota_debito->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $nota_debito->appends(Request::only(['id_proveedor', 'id', 'secuencia','no_cheque','fecha_cheque','concepto','serie','id_asiento_cabecera']))->links() }}
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
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#example2').DataTable({
        'paging': false,
        'lengthChange': true,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': true,
        'sInfoEmpty': true,
        'sInfoFiltered': true,
        'language': {
          "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

    });
    $('.select2').select2({
      tags: false
    });
    $('.fecha').datetimepicker({
      format: 'YYYY-MM-DD',
    });
    $("#nombre_proveedor").autocomplete({
      source: function(request, response) {
        $.ajax({
          url: "{{route('compra_buscar_nombreproveedor')}}",
          dataType: "json",
          data: {
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      },
      minLength: 2,
    });

    $('.select2_find_proveedor').select2({
        placeholder: "Escriba el nombre del proveedor",
         allowClear: true,
        ajax: {
            url: '{{route("anticipoproveedor.proveedorsearch")}}',
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });

  </script>

  @endsection