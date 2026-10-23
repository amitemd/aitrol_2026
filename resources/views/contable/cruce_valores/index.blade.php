@extends('contable.cruce_valores.base')
@section('action-content')
<!-- Ventana modal editar -->
<div class="modal fade" id="modal_devoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Registro de Cruce de Valores</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">CRUCE DE VALORES A FAVOR</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('cruce.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR CRUCE DE VALORES</label>
      </div>
    </div>
    
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('cruce.index') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="identificacion">Id</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($request)) {{$request->id}} @endif" placeholder="Ingrese Identificación..." />
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="identificacion">Proveedor: </label>

        </div>

        <div class="form-group col-md-3 col-xs-10 container-4">
          <select style="width: 100%;" class="form-control select2_find_proveedor" name="id_proveedor" id="id_proveedor">

          </select>
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="identificacion">Detalle: </label>

        </div>
        <div class="form-group col-md-4 col-xs-4 container-5">
          <input value="@if(isset($request)) @if($request->detalle!=null) {{$request->detalle}} @endif @endif" class="form-control" type="text" id="detalle" name="detalle" placeholder="Ingrese Detalle..." />
        </div>


        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id_asiento_cabecera">Asiento: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <input class="form-control" type="text" id="id_asiento_cabecera" value="@if(isset($request)){{$request['id_asiento_cabecera']}}@endif" name="id_asiento_cabecera" placeholder="Ingrese Id Asiento..." />
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
        <label class="color_texto">LISTADO DE CRUCE DE VALORES</label>
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
                  <table id="example2" class="table table-hover dataTable">
                    <thead class="well-dark">
                      <tr>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Proveedor</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($anticipo as $values)



                      <tr class="well">
                        <td>{{$values->secuencia}}</td>
                        <td>{{$values->fecha_pago}}</td>
                        <td>{{$values->id_asiento_cabecera}}</td>
                        <td>@if(isset($values->proveedor)) {{$values->proveedor->nombrecomercial}} @elseif(isset($values->proveedor_cruce_valores)) {{$values->proveedor_cruce_valores->nombrecomercial}} @endif</td>
                        <td>{{$values->detalle}}</td>
                        <td>@if(!is_null($values->total_disponible)) {{$values->total_disponible}} @endif</td>
                        <td>@if(isset($values->usuario)) {{$values->usuario->nombre1}} {{$values->usuario->nombre2}} @endif</td>
                        <td>@if($values->estado == '1') Activo @elseif($values->estado =='0') Anulada @else Activo @endif</td>
                        <td>
                          <a href="{{route('pdf_cruces.contable',[$values->id])}}" target="_blank" class="btn btn-danger btn-gray"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                          <a class="btn btn-success btn-gray" href="{{route('cruce.edit',['id'=>$values->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                          @if(($values->estado)==1)
                          <a href="javascript:anular({{$values->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>

                    </tfoot>
                  </table>
                  <div class="row">
                    <div class="col-sm-5">
                      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} / {{count($anticipo) + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} de {{$anticipo->total()}} registros</div>
                    </div>
                    <div class="col-sm-7">
                      <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $anticipo->appends(Request::only(['id', 'id_proveedor', 'secuencia','detalle','fecha']))->links() }}
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
  </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
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
  $('#modal_devoluciones').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');

  });

  $('.select2').select2({
    tags: false
  });

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta comprobante?',
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

  $('.select2_find_proveedor').select2({
      placeholder: "Escriba el nombre del proveedor",
    allowClear: true,
    ajax: {
      url: '{{route("anticipoproveedor.proveedorsearch")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        console.log(data);
        return {
          results: data
        };
      }
    }
  });



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
          url: "{{ url('contable/anticipo/anular_anticipo/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            if ((data.status == 'success')) {
              Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success").then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });

            } else {
              alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
            }
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