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
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroCredito')}} {{trans('contableM.acreedor')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</h3>
      </div>

      <!--div class="col-md-1 text-right">
        <button onclick="location.href='{{route('creditoacreedores.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div-->

      <div class="col-md-1 text-right">
        <a class="btn btn-success" href="{{route('compra.notacredito.newNotaCredito')}}">{{trans('contableM.nuevo')}} {{trans('contableM.notacredito')}}</a>
      </div>

    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.buscar')}} {{trans('contableM.notacredito')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('creditoacreedores.search') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Identificación..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">{{trans('contableM.proveedor')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <select class="form-control select2" name="nombre_proveedor" id="nombre_proveedor" style="width: 100%;">
            <option value="">{{trans('proforma.seleccion')}}...</option>
            @foreach($proveedor as $value)
            <option @if(isset($searchingVals)) {{ $value->id == $searchingVals['id_proveedor'] ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">{{trans('contableM.concepto')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <input type="text" class="form-control" id="nombre_concepto" name="nombre_concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Nombre proveedor...">
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">{{trans('contableM.secuencia')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <input type="text" class="form-control" id="secuencia_nombre" name="secuencia_nombre" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Nombre proveedor...">
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fecha')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha_credito" class="form-control fecha" id="fecha_credito" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="identificacion">{{trans('contableM.autorizacion')}} </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <input type="text" class="form-control" id="autorizacion_credito" name="autorizacion_credito" value="@if(isset($searchingVals)){{$searchingVals['autorizacion']}}@endif" placeholder="{{trans('contableM.autorizacion')}} ...">
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id_asiento_cabecera">{{trans('contableM.asiento')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
          <input type="text" class="form-control" id="id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Id Asiento...">
        </div>



        <div class="col-xs-1">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.listadocredito')}}</label>
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
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.secuencia')}}</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.asiento')}}</th>
                        <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.concepto')}}</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <th>{{trans('proforma.estado')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('proforma.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($nota_credito as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->id)) {{$value->id}} @endif </td>
                        <td> @if(!is_null($value->nro_comprobante)) {{$value->nro_comprobante}} @endif </td>
                        <td>@if(!is_null($value->id_asiento_cabecera)) {{$value->id_asiento_cabecera}} @endif</td>
                        <td> @if(!is_null($value->concepto)) {{$value->concepto}} @endif</td>
                        <td> @if(!is_null($value->fecha)) {{$value->fecha}} @endif</td>
                        <td>@if(!is_null($value->proveedor)){{$value->proveedor->nombrecomercial}}@endif</td>
                        <td>{{$value->valor_contable}}</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if($value->estado==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                        <td>
                          @if($value->estado==1)
                          <button onclick="verificarAnulacion({{$value->id}})" class="btn btn-warning btn-gray"><i class="fa fa-trash"></i></button>
                          @endif
                          <a class="btn btn-danger btn-gray" href="{{route('creditoacreedores.edit2',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                        </td>

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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} / {{count($nota_credito) + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} de {{$nota_credito->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $nota_credito->appends(Request::only(['id_proveedor', 'id', 'secuencia','no_cheque','fecha_cheque','secuencia_nombre','id_asiento_cabecera','nombre_concepto','autorizacion_credito']))->links() }}
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
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
  const verificarAnulacion = id => {

    Swal.fire({
      title: `¿{{trans('contableM.anularfactura')}}?`,
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        anular(id)
      }
    })
  }


  async function anular(id) {
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

      //if (text) {
        $.ajax({
          type: 'get',
          url: `{{route('creditoacreedores.anular')}}`,
          datatype: 'json',
          data: {
            'observacion': text,
            'id': id,
          },
          success: function(data) {
            console.log(data);
            if(data.status == 'success'){
              let enlace = `</b><a target="_blank" href="{{ url('contable/contabilidad/libro/edit/${data.asiento}')}}"><b>Asiento Creado</b></a>`;
              alertas(data.status, `${data.status}..`, `${data.msj} ${enlace}`);
            }else{
              alertas(data.status, `${data.status}..`, `${data.msj}`);
            }
           
          },
          error: function(data) {

          }
        });
      // /}

    } catch (err) {
      console.log(err);
    }
  }

  function alertas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`
    })
  }
</script>


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
  /*
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
  */
  $('.fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $('.select2').select2({
    tags: false
  });
</script>

@endsection