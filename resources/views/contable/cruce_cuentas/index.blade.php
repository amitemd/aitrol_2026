@extends('contable.nota_debito_cliente.base')
  @section('action-content')
  <style type="text/css">
    tr > th, tr > td {
      text-align: center;
      font-family: 'Nunito', sans-serif;
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;400&display=swap" rel="stylesheet">
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
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.CrucedeCuentas')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro Cruce Cuentas</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header header_new">
        <div class="col-md-9">
          <!--<h8 class="box-title size_text">Empleados</h8>-->
          <!--<label class="size_text" for="title">EMPLEADOS</label>-->
          <h3 class="box-title">{{trans('contableM.CrucedeCuentas')}}</h3>
        </div>

        <div class="col-md-1 text-right">
          <button onclick="location.href='{{route('pr.cruce_cuentas_create')}}'" class="btn btn-success btn-gray">
            <i class="fa fa-file"></i>
          </button>
        </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">{{trans('contableM.BUSCADORCRUCEDEVALORES')}}</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('pr.cruce_cuentas_search') }}">
          {{ csrf_field() }}
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="identificacion">{{trans('contableM.id')}}</label>
          </div>
          <div class="form-group col-md-2 col-xs-2 container-4">
            <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
          </div>
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion"># {{trans('contableM.asiento')}}</label>
          </div>
          <div class="form-group col-md-2 col-xs-2 container-4">
            <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese Asiento..." />
          </div>
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="identificacion control-label">{{trans('contableM.proveedor')}}: </label>
          </div>
          <div class="form-group col-md-3 col-xs-3" style="text-align: left;">
            <select class="form-control select2_find_proveedor" style="text-align: left; width: 100%;" name="id_proveedor" id="id_proveedor">
              
            </select>
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="identificacion control-label">{{trans('contableM.concepto')}}: </label>
          </div>
          <div class="form-group col-md-4 col-xs-4">
            <input class="form-control" type="text" id="detalle" value="@if(isset($searchingVals)){{$searchingVals['detalle']}}@endif" name="detalle" placeholder="Ingrese concepto..." />
          </div>
          <div class="col-xs-12" style="text-align: right;">
            <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
          </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto">{{trans('contableM.LISTADODECRUCES')}}</label>
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
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># {{trans('contableM.asiento')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.concepto')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($anticipo as $value)
                      <tr class="well">
                        @php 
                          $proveedor= DB::table('proveedor')->where('id',$value->id_proveedor)->first();
                           @endphp
                        <td>{{$value->secuencia}}</td>
                        <td>{{$value->id_asiento_cabecera}}</td>
                        <td>{{$value->fecha}} </td>

                        <td> @if(!is_null($proveedor)) {{$proveedor->razonsocial}} @endif</td>
                        <td>{{$value->detalle}}</td>
                        <td>{{$value->total}}</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                        <td>
                          @if(($value->estado)==1)
                          <a class="btn btn-danger" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                          @endif
                          <a class="btn btn-success" href="{{route('pr.cruce_cuentas_edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                           <a class="btn btn-danger" target="_blank" href="{{route('pdf_cruce_cuentas.pdf',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} / {{count($anticipo) + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} de {{$anticipo->total()}} {{trans('contableM.registros')}}</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $anticipo->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha','id_asiento_cabecera']))->links() }}
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
        text: `{{trans('contableM.norevertiraccion')}}!`,
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
            url: "{{ url('contable/cruce_cuentas/anular/comprobante/')}}/" + id,
            datatype: 'json',
            data: {
              'observacion': text
            },
            success: function(data) {
              
              Swal.fire(data.status.toUpperCase(), data.msj, data.status);
              if(data.status == 'success'){
                setTimeout(() => {
                  location.reload(true);                  
                }, 2000);
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

    $('.select2_find_proveedor').select2({
        placeholder: "Escriba el nombre del proveedor",
         allowClear: true,
        ajax: {
            url: '{{route("comprapedido.proveedorsearch")}}',
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