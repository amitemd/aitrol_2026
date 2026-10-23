@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

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
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.ComprobantedeEgreso')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroComprobantedeEgreso')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">{{trans('contableM.ComprobantedeEgreso')}}</h3>
      </div>
      <div class="col-md-3 text-right">
        <button onclick="location.href='<?= route('acreedores_ccreate_solicitud'); ?>'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i> &nbsp; {{trans('contableM.nuevo')}}
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.BUSCADORDECOMPDEEGRESO')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <input type="hidden" id="armHtml" value="@if(isset($armHtml)) {{$armHtml}} @endif">
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('comp_egreso.buscar_solicitud') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2  container-4">
          <select class="form-control select2_find_proveedor" style="width: 100%;" name="proveedor" id="proveedor"></select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia">{{trans('contableM.secuencia')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">Nº: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="cheque" name="cheque" value="@if(isset($searchingVals)){{$searchingVals['no_cheque']}}@endif" placeholder="Ingrese número de cheque..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="descripcion">{{trans('contableM.Descripcion')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <input class="form-control" type="text" name="descripcion" id="descripcion" value="@if(isset($searchingVals)){{$searchingVals['descripcion']}}@endif">
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fechacheque')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_cheque']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="asiento_id">{{trans('contableM.asiento')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <input class="form-control" type="text" name="asiento_id" id="asiento_id" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif">
        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscarEmpleado" onclick="document.getElementById('excel').value=0" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.LISTADODECOMPROBANTES')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr class='well-dark'>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># {{trans('contableM.asiento')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fechacheque')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.NroCheque')}}</th>
                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Descripcion')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.anuladopor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($comp_egreso as $value)
              @php

              if(Auth::user()->id == "0957258056"){
              // if($value->id_pago == 2){
              // dd($value);
              // }
              }
              @endphp
              <tr class="well">
                <td>{{$value->id}}</td>
                <td>{{$value->id_asiento_cabecera}}</td>
                <td>{{$value->secuencia}}</td>
                <td>{{$value->fecha_cheque}}</td>
                <td>{{$value->no_cheque}}</td>
                <td>@if(isset($value->proveedor)){{$value->proveedor->razonsocial}}@endif</td>
                <td>{{$value->descripcion}}</td>
                <td>@if(($value->valor_pago)>0)
                  {{$value->valor_pago}}
                  @else
                  @if(isset($value->asiento_cabecera))
                  {{$value->asiento_cabecera->valor}} @endif @endif
                </td>
                <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                <td>@if(($value->estado)==0)@if(isset($value->usuariomod)) {{$value->usuariomod->nombre1}} {{$value->usuariomod->nombre2}} @endif @endif</td>
                <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                <td>
                  @if($value->anulado_tipo!=1)
                  <a class="btn btn-danger btn-gray" target="_blank" href="{{route('pdf_comprobante_egreso_solicitud',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>
                  @endif
                  @if(($value->estado)==1)
                  <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                  @endif
                  <a class="btn btn-success btn-gray" href="{{route('egresoa_edit_solicitud',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                  <!-- <a class="btn btn-success btn-gray" href="{{route('reporte_datos.compegreso',['id'=>$value->id, 'tipo'=>1])}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a> -->
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} / {{count($comp_egreso) + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} {{trans('laboratorio.de')}} {{$comp_egreso->total()}} {{trans('contableM.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $comp_egreso->appends(Request::only(['proveedor', 'id', 'secuencia','no_cheque','fecha_cheque','descripcion','id_asiento_cabecera','cheque']))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  $(document).ready(function() {
    let armHtml = $("#armHtml").val();
    if (armHtml !== "") {
      let op = armHtml.split("-");
      let option = new Option(op[1], op[0], true, true);
      $('#proveedor').append(option).trigger('change')
    }

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
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $(".nombre_proveedor").autocomplete({
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

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#proveedor').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#proveedor').val("");
          $('#direccion_proveedor').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }
  $('.select2').select2({
    tags: false
  });

  function anular(id) {
    //alert("aqui");
    Swal.fire({
      title: `{{trans('contableM.deseaanulestacompro')}}`,
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
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
        inputPlaceholder: `{{trans('contableM.ingremotanula')}}`,
        inputAttributes: {
          'aria-label': `{{trans('contableM.ingremotanula')}}`
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/acreedores/documentos/nota/comprobante/egreso/anular/solicitud/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            console.log(data.status);
            if ((data.status == 'success')) {
              Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
              setTimeout(function() {
                location.href = "{{route('acreedores_cegreso_solicitud')}}";
              }, 3000); 

            } else {

              alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
              console.log(data);
            }
          },
          error: function(data) {
            alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
            console.log(data);
          }
        });

      }

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

  $('.select2_find_proveedor').select2({
    placeholder: "trans('contableM.escrinomprovee')",
    allowClear: true,
    ajax: {
      url: '{{route("compras.proveedorsearch")}}',
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
</script>

@endsection