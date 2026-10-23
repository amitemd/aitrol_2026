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
      <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
      <li class="breadcrumb-item"><a href="#">Mantenimientos</a></li>
      <li class="breadcrumb-item active">Activo Fijo</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Listado de Activos</h3>
      </div>
      <div class="col-md-2 text-right">
        <a type="button" href="{{route('afActivo.create')}}" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Saldos Iniciales
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
        <label class="color_texto" for="title">BUSCADOR DE ACTIVOS FIJOS</label>
      </div>
    </div>


    <!-- /.box-header -->
    <div class="box-body dobra">

      <form method="POST" id="reporte_master" action="{{ route('activofjo.buscar_activo') }}">
        {{ csrf_field() }}

        <div class="col-md-3">
          <label class="col-md-12 control-label" for="buscar_nombre">Nombre: </label>
          <div class="col-md-12">
            <input class="form-control" type="text" id="nombre_activo" name="nombre_activo" value="{{$activo}}" placeholder="Ingrese nombre del activo..." />
          </div>
        </div>

        <div class="col-md-3">
          <label class="col-md-12 control-label" for="buscar_nombre">Tipo: </label>
          <div class="col-md-12">
            <select id="id_tipo" name="id_tipo" class="form-control">
              <option value="">Seleccione...</option>
              @foreach($tipos as $value)
              <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <label class="col-md-12 control-label" for="fecha">Fecha desde: </label>
          <div class="col-md-12">
            <input style="text-align: center;line-height:10px;" value="{{$desde}}" type="date" name="desde" id="desde" class="form-control">
          </div>

        </div>

        <div class="col-md-3">
          <label class="col-md-12 control-label" for="fecha">Fecha hasta: </label>
          <div class="col-md-12">
            <input style="text-align: center;line-height:10px;" value="{{$hasta}}" type="date" name="hasta" id="hasta" class="form-control">
          </div>
        </div>
        <div>&nbsp;</div>

        <div class="col-md-12" style=" text-align: right;">
          <button type="submit" id="buscarTipo" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO DE ACTIVOS</label>
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
                          <th width="5%">ID</th>
                          <th width="5%">Fecha Compra</th>
                          <th width="5%">Codigo</th>
                          <th width="25%">Nombre</th>
                          <th width="15%">Descripción</th>
                          <th width="15%">Tipo</th>
                          <th width="15%">Categoria</th>
                          <th width="5%">Marca</th>
                          <th width="5%">Modelo</th>
                          <th width="5%">Serie</th>
                          <th width="5%">Resp.</th>
                          <th width="5%">Ubicación</th>
                          <th width="5%">Vida util</th>
                          <th width="5%">Usuario Crea</th>
                          <th width="5%">Estado</th>
                          <th width="5%">Acciones</th>
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
                          <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
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
                            <a class="btn btn-danger btn-gray btn-sm" target="_blank" href="{{route('activofjo.pdf_activo',['id_activo' => $value->id])}}"><i class="fa fa-file-pdf-o" ></i></a>
                            <a class="btn btn-warning btn-gray btn-lg" target="_blank"href="{{route('activofjo.codigo_activo',['id' => $value->id])}}" style="width: 45%"> 
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($activos->currentPage() - 1) * $activos->perPage())}} / {{count($activos) + (($activos->currentPage() - 1) * $activos->perPage())}} de {{$activos->total()}} registros</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $activos->appends(Request::only(['codigo', 'nombre','nombre_activo']))->links() }}
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
    //alert();
    Swal.fire({
      title: '¿Desea Anular esta factura?',
      text: "No puedes revertir esta acccion!",
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
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
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
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            //location.href = "{{route('afDocumentoFactura.index')}}";
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