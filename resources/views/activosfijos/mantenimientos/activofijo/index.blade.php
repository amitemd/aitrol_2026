@extends('activosfijos.mantenimientos.activofijo.base')
@section('action-content')
<!-- Ventana modal editar -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<style type="text/css">
  .table-bordered>tbody>tr>td {
    font-size: 12px;
  }

  th {
    font-size: 14px !important;
  }
</style>
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.activosfijos')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.mantenimientos')}}</a></li>
      <li class="breadcrumb-item active">{{trans('contableM.afijo')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">{{trans('contableM.listacfijo')}}</h3>
      </div>
      <div class="col-md-2 text-right">
        <a type="button" href="{{route('afActivo.create')}}" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>{{trans('contableM.saldosini')}}
        </a>
      </div>
    </div>
    <!--div class="col-md-1 text-right">
              <a type="button" href="{{route('activofjo.index_listado_tipo')}}" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>Listado Tipo
              </a>
            </div-->

    <!--div class="col-md-1 text-right">
              <a type="button" href="{{route('activofjo.excel_listado_general')}}" class="btn btn-success btn-gray">
                   <i aria-hidden="true" class="fa fa-download"></i> Listado
              </a>
            </div-->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.buscactfijos-')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('activofjo.buscar_activo') }}">
        {{ csrf_field() }}
        <div class="col-md-3">
          <label class="col-md-12 control-label" for="buscar_nombre">{{trans('contableM.nombre')}}: </label>
          <div class="col-md-12">
            <input class="form-control" type="text" id="nombre_activo" name="nombre_activo" value="{{$activo}}" placeholder="Ingrese nombre del activo..." />
          </div>
        </div>

        <div class="col-md-3">
          <label class="col-md-12 control-label" for="buscar_nombre">{{trans('contableM.tipo')}}: </label>
          <div class="col-md-12">
            <select id="id_tipo" name="id_tipo" class="form-control">
              <option value="">{{trans('contableM.seleccione')}}...</option>
              @foreach($tipos as $value)
              <option value="{{$value->id}}" @if((string) $tipo === (string) $value->id) selected @endif>{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <label class="col-md-12 control-label" for="fecha">{{trans('contableM.FechaDesde')}}: </label>
          <div class="col-md-12">
            <input style="text-align: center;line-height:10px;" value="{{$desde}}" type="date" name="desde" id="desde" class="form-control">
          </div>

        </div>

        <div class="col-md-3">
          <label class="col-md-12 control-label" for="fecha">{{trans('contableM.Fechahasta')}}: </label>
          <div class="col-md-12">
            <input style="text-align: center;line-height:10px;" value="{{$hasta}}" type="date" name="hasta" id="hasta" class="form-control">
          </div>
        </div>
        <div>&nbsp;</div>

        <div class="col-md-12" style=" text-align: right;">
          <button type="submit" class="btn btn-success" formaction="{{ route('activofjo.excel_activos') }}">
            <span class="fa fa-file-excel-o" aria-hidden="true"></span> Descargar Excel
          </button>
          <button type="submit" id="buscarTipo" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.listacfijo-')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="row">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="resultados">
            </div>
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr class="well-dark">
                          <th width="5%">{{trans('contableM.id')}}</th>
                          <th width="5%">{{trans('contableM.fechacompra')}}</th>
                          <th width="5%">{{trans('contableM.codigo')}}</th>
                          <th width="25%">{{trans('contableM.nombre')}}</th>
                          <th width="15%">{{trans('contableM.Descripcion')}}</th>
                          <th width="15%">{{trans('contableM.tipo')}}</th>
                          <th width="15%">{{trans('contableM.categoria')}}</th>
                          <th width="5%">{{trans('contableM.marca')}}</th>
                          <th width="5%">{{trans('contableM.modelo')}}</th>
                          <th width="5%">{{trans('contableM.serie')}}</th>
                          <th width="5%">{{trans('contableM.responsable')}}</th>
                          <th width="5%">{{trans('contableM.ubicacion')}}</th>
                          <th width="5%">{{trans('contableM.vidautil')}}</th>
                          <th width="5%">{{trans('contableM.usuariocrea')}}</th>
                          <th width="5%">{{trans('contableM.estado')}}</th>
                          <th width="5%">{{trans('contableM.acciones')}}</th>
                        </tr>
                      </thead>
                      <tbody id="tbl_detalles" name="tbl_detalles">
                        @foreach ($activos as $value)
                        @php
                        $fcompra= substr($value->fecha_compra, 0, 10);
                        @endphp
                        <tr class="well">
                          <td>{{ $value->id}}</td>
                          <td>{{$fcompra}}</td>
                          <td>{{ $value->codigo}}</td>
                          <td>{{ $value->nombre}}</td>
                          <td>{{ $value->descripcion}}</td>
                          <td>@if(isset($value->tipo)) {{ $value->tipo->nombre }} @endif</td>
                          <td>@if(isset($value->sub_tipo)) {{ $value->sub_tipo->nombre }} @endif</td>
                          <td> @if(!is_null($value->marca)) {{ $value->marca }} @endif </td>
                          <td>@if(!is_null($value->modelo)) {{ $value->modelo }} @endif</td>
                          <td> @if(!is_null($value->serie)) {{ $value->serie }} @endif </td>
                          <td> @if(!is_null($value->responsable)) {{ $value->responsable}} @endif</td>
                          <td> @if(!is_null($value->ubicacion)) {{$value->ubicacion}} @endif</td>
                          <td> @if(!is_null($value->vida_util)) {{ $value->vida_util}} @endif</td>
                          <td> @if(!is_null($value->id_usuariocrea)) {{$value->user_crea->apellido1}} {{$value->user_crea->nombre1}} @endif</td>
                          <td>@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') {{trans('contableM.anulada')}} @else {{trans('contableM.activo')}} @endif</td>
                          <td>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{ route('afActivo.edit', ['id_activo' => $value->id ]) }}" class="btn btn-success btn-gray btn-sm">
                              <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                              <!--&nbsp;&nbsp; Revisar Nota-->
                            </a>
                            <a href="{{route('activofjo.excel_depreciacion_acumulada',['id_activo' => $value->id])}}" class="btn btn-info btn-gray btn-sm"><i class="fa fa-download "></i></a>

                            <a href="javascript:anular_activos({{$value->id}})" class="btn btn-danger btn-gray btn-sm">
                              <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-danger btn-gray btn-sm" target="_blank" href="{{route('activofjo.pdf_activo',['id_activo' => $value->id])}}"><i class="fa fa-file-pdf-o"></i></a>
                            <a class="btn btn-warning btn-gray btn-lg" target="_blank" href="{{route('activofjo.codigo_activo',['id' => $value->id])}}" style="width: 45%">
                              <i class="fa fa-barcode"></i>
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
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($activos->currentPage() - 1) * $activos->perPage())}} / {{count($activos) + (($activos->currentPage() - 1) * $activos->perPage())}} {{trans('laboratorio.de')}} {{$activos->total()}} {{trans('laboratorio.registros')}}</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $activos->appends(Request::only(['nombre_activo', 'id_tipo', 'desde', 'hasta']))->links() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#estado').val('{{@$estado }}');
    $('#tipo').val('{{ @$tipo }}');

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false
    });
    $('#fecha_desde').datetimepicker({
      format: 'YYYY-MM-DD',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY-MM-DD',
    });

  });

  function actualizar(obj) {
    $.post("{{route('conciliacionbancaria.actualizar')}}", {
      id: obj.value,
      _token: "{{ csrf_token() }}"
    });
  }

  function actualizarmasivo(obj, accion = "") {
    $.post("{{route('conciliacionbancaria.actualizarmasivo')}}", {
      id: obj.value,
      accion: accion,
      _token: "{{ csrf_token() }}"
    });
  }

  function seleccionar_todo(checked) {
    var miTabla = document.getElementById('tbl_detalles');
    for (i = 0; i < miTabla.rows.length; i++) {
      miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
      actualizarmasivo(miTabla.rows[i].getElementsByTagName("input")[0], checked);
    }
  }

  function anular_activos(id_activo) {
    Swal.fire({
      title: `{{trans('contableM.anularfactura')}}`,
      text: "{{trans('contableM.norevertiraccion')}}",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
    }).then((result) => {

      if (result.isConfirmed) {
        test(id_activo);
      }
    })

  }
  async function test(id_activo) {
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
          url: `{{ url('activosfijo/eliminar_activo/${id_activo}')}}`,
          datatype: 'json',
          data: {
            'codigo': text
          },
          success: function(data) {
            //Swal.fire("Correcto!", "{{trans('contableM.anucorrec')}}", "success");
            //location.href = "{{route('afDocumentoFactura.index')}}";
            alertas(data.respuesta, data.respuesta.toUpperCase(), data.msj)
            if (data.respuesta == "success") {
              setTimeout(() => {
                location.reload();
              }, 1500);
            }
          },
          error: function(data) {
            console.log(data);
            alertas(data.status, data.status.toUpperCase(), data.msj)
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>
@endsection
