  @extends('contable.nota_debito_cliente.base')
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
  <div class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nota de Débito Cliente</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Débito Clientes</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header header_new">
        <div class="col-md-9">
          <!--<h8 class="box-title size_text">Empleados</h8>-->
          <!--<label class="size_text" for="title">EMPLEADOS</label>-->
          <h3 class="box-title">Nota de Débito Cliente</h3>
        </div>

        <div class="col-md-1 text-right">
          <button onclick="location.href='{{route('nota_cliente_debito.create')}}'" class="btn btn-success btn-gray">
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
        <form method="POST" id="reporte_master" action="{{ route('nota_cliente_debito.search') }}">
          {{ csrf_field() }}
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion">Id</label>
          </div>
          <div class="form-group col-md-1 col-xs-1 container-4">
            <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
          </div>
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion control-label">Cliente: </label>
          </div>
          <div class="form-group col-md-3 col-xs-3" style="text-align: left;">
            <select class="form-control select2_cuentas" style="width: 100%;" name="id_cliente" id="id_cliente">
              <option value="">Seleccione...</option>
             
            </select>
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="identificacion control-label">Concepto: </label>
          </div>
          <div class="form-group col-md-4 col-xs-4">
            <input class="form-control" type="text" id="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" name="concepto" placeholder="Ingrese concepto..." />
          </div>
          <div class="col-xs-12" style="text-align: right;">
            <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto">LISTADO DE NOTAS DE DÉBITO</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">

            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">

                  <table id="example2" class="table-bordered table-hover dataTable table-striped col-md-12" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cliente</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Concepto</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($comp_egreso as $value)
                      <tr class="well">
                        <td>{{$value->secuencia}}</td>
                        <td>@if(isset($value->cliente)){{$value->cliente->nombre}} @endif</td>
                        <td>{{$value->concepto}}</td>
                        <td>{{$value->valor_contable}}</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if($value->estado==1) Activo @else Inactivo @endif</td>
                        <td>
                          @if($value->estado==1)
                          <a class="btn btn-danger btn-gray" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                          @endif
                          <a class="btn btn-success btn-gray" href="{{route('nota_cliente_debito.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                          <a href="{{route('nota_cliente_debito.pdf',['id'=>$value->id])}}" class="btn btn-warning btn-gray" target="_blank" rel="noopener noreferrer"><i class="fa fa-file-pdf-o "></i></a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} / {{count($comp_egreso) + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} de {{$comp_egreso->total()}} registros</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $comp_egreso->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha','concepto']))->links() }}
                    </div>
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
  <script src="{{ asset ('/hospital/cleave/dist/cleave.min.js')}}"></script>
  <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#example2').DataTable({
        'paging': false,
        'lengthChange': true,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
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

    function anular(id) {
      Swal.fire({
        title: '¿Desea eliminar esta Nota de débito?',
        text: "No puedes revertir esta acccion!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Eliminar!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: 'get',
            url: `{{ url('contable/cliente/nota_debito/anular/${id}')}}`,
            datatype: 'json',
            data: {

            },
            success: function(data) {
              alertas("success", "Exito..", "Se eliminó con exito");
              location.href = "{{route('nota_cliente_debito.index')}}";
            },
            error: function(data) {
              console.log(data);
            }
          });
        }
      })
    }

    function alertas(icon, title, text) {
      Swal.fire({
        icon: `${icon}`,
        title: `${title}`,
        html: `${text}`
      })
    }

    $('.select2_cuentas').select2({
        placeholder: "Escriba el nombre del cliente",
          allowClear: true,
        ajax: {
            url: '{{route("venta.clientesearch")}}',
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