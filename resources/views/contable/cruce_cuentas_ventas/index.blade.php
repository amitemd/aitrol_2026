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
        <li class="breadcrumb-item"><a href="#">Cruce Cuentas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Cruce Cuentas</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header header_new">
        <div class="col-md-9">
          <!--<h8 class="box-title size_text">Empleados</h8>-->
          <!--<label class="size_text" for="title">EMPLEADOS</label>-->
          <h3 class="box-title">Cruce Cuentas Clientes</h3>
        </div>

        <div class="col-md-1 text-right">
          <button onclick="location.href='{{route('cr.cruce_cuentas_create')}}'" class="btn btn-success btn-gray">
            <i class="fa fa-file"></i>
          </button>
        </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR DE CRUCES</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('cr.cruce_cuentas_search') }}">
          {{ csrf_field() }}
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion">Id</label>
          </div>
          <div class="form-group col-md-1 col-xs-1 container-4">
            <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
          </div>
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion control-label">Clientes: </label>
          </div>
          <div class="form-group col-md-3 col-xs-3" style="text-align: left;">
            <select class="form-control select2" style="text-align: left; width: 100%;" name="id_cliente" id="id_cliente">
              <option value="">Seleccione...</option>
              @foreach($clientes as $value)
              <option @if(isset($searchingVals)) {{ $value->identificacion == $searchingVals['id_cliente'] ? 'selected' : ''}} @endif value="{{$value->identificacion}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="identificacion control-label">Concepto: </label>
          </div>
          <div class="form-group col-md-4 col-xs-4">
            <input class="form-control" type="text" id="detalle" value="@if(isset($searchingVals)){{$searchingVals['detalle']}}@endif" name="detalle" placeholder="Ingrese concepto..." />
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
          <label class="color_texto">LISTADO DE CRUCES</label>
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
                      @foreach ($anticipo as $value)
                      <tr class="well">
                        <td>{{$value->id}}</td>
                        <td>{{$value->id_cliente}}</td>
                        <td>{{$value->detalle}}</td>
                        <td>{{$value->total}}</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if(($value->estado)==1) Activo @else Inactivo @endif</td>
                        <td>
                          @if(($value->estado)==1)
                          <a class="btn btn-danger btn-gray" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                          @endif
                          <a class="btn btn-success btn-gray" href="{{route('cr.cruce_cuentas_edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} / {{count($anticipo) + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} de {{$anticipo->total()}} registros</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $anticipo->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha']))->links() }}
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
  <script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
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
        title: '¿Desea Anular este comprobante?',
        text: "No puedes revertir esta acccion!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
      }).then((result) => {
        if (result.isConfirmed) {
          var acumulate = "";
          test(id);
        }
      })
    }
    async function test(id) {
      try {
        const {
          value: text
        } = await Swal.fire({
          input: 'textarea',
          inputPlaceholder: 'Ingrese motivo de anulación...',
          inputAttributes: {
            'aria-label': 'Ingrese motivo de anulación'
          },
          showCancelButton: true
        })

        if (text) {
          $.ajax({
            type: 'get',
            url: "{{ url('contable/cruce_cuentas_clientes/anular/comprobante/')}}/" + id,
            datatype: 'json',
            data: {
              'observacion': text
            },
            success: function(data) {
              Swal.fire("Correcto!", "Anulación Correcta", "success");
              location.reload(true);
            },
            error: function(data) {
              console.log(data);
            }
          });
        }

      } catch (err) {
        console.log(err);
      }
    }
  </script>

  @endsection