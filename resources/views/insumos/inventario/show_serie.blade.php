@extends('contable.balance_general.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 14px;
    font-weight: bold;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 60px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .td_center {
    text-align: center;
  }

  .td_der {
    text-align: left;
  }

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {

    padding: 0;
    font-size: 14px !important;
    line-height: 1;
  }

  .tabla_footer th {
    color: transparent !important;
  }
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css" />

<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('winsumos_reportes.insumos')}}</a></li>
      <li class="breadcrumb-item"><a href="../">{{trans('winsumos_reportes.ExistenciasSerie')}}</a></li>
    </ol>
  </nav>
  <div class="box">

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('winsumos_reportes.BUSCADOR')}}</label>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('insumos.inventario_serie.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos_reportes.Producto')}}:</label>
          <div class="col-md-9">
            <select id="id_producto" name="id_producto" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>

              @foreach($productos as $value)
              <option value="{{$value->id}}"> {{$value->codigo}} {{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos_reportes.Bodega')}}:</label>
          <div class="col-md-9">
            <select id="id_bodega" name="id_bodega" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($bodegas as $value)
              <option value="{{$value->id}}"> {{$value->nombre}} @if(isset($value->empresa)) - {{$value->empresa->nombrecomercial}} @endif</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos_reportes.Descripción')}}:</label>
          <div class="col-md-9">
            <input type="text" class="form-control input-sm" name="descripcion" id="descripcion" value="{{@$descripcion}}">
          </div>
        </div>
        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.marca')}}:</label>
          <div class="col-md-9">
            <select id="id_marca" name="id_marca" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($marca as $value)
              <option value="{{$value->id}}"> {{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Serie</label>
          <div class="col-md-9">
            <input type="text" class="form-control" id="serie" name="serie" value="">
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('winsumos.Buscar')}}
          </button>
          <!--<button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>-->
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    {{-- <b>Compras del período:</b> --}}
    <div class="content" id="contenedor">
      <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="tbl_compras" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_compras_info">
              <thead>
                <tr class="well-dark">
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.ID')}}*</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Código')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Marca')}}</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Producto')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Pedido')}}</th>
                  <th width="15%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Bodega')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Proveedor</th>
                  <th width="15%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Lote')}}</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.serie')}}</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Fechadevencimiento')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}}</th>
                  <!--th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Usos')}}</th-->
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}} Min</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}} Max</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> Tipo de Documento </th>
                  @if(Auth::user()->id_tipo_usuario == 1)
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Costo')}}</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @php
                $contUsos = 0;
                $contExist = 0;
                @endphp

                @if(!is_null($inventario) || count($inventario)>0)
                @foreach ($inventario as $value)
                @php
                $det_movi = $value->inventario->det_mov;
                $nombre_proveedor = "N/A";

                @endphp
                @php $marca = ""; $pedido = null; @endphp
                @if(isset($value->producto->marca))
                @if(!is_null($value->producto->marca->nombre))
                @php $marca = $value->producto->marca->nombre; @endphp
                @endif
                @endif
                @php
                $movimiento = \Sis_medico\Movimiento::where('serie',$value->serie)->orderBy('id', 'asc')->first();
                if (isset($movimiento->pedido)) {
                $pedido = $movimiento->pedido;
                }
                $consul = \Sis_medico\InvDetMovimientos::proveedorDetalle($value->serie);
                if(count($consul) > 0) {
                $nombre_proveedor = $consul[0]->razonsocial;
                }
                @endphp
                <tr>
                  <td class="td_der">{{ $value->id }}</td>
                  <td> {{ $value->producto->codigo }} </td>
                  <td> {{ $marca }} </td>
                  <td> {{ $value->producto->nombre }} </td>
                  <td> @if(isset($pedido->id)) {{ $pedido->pedido }} @endif</td>
                  <td> @if(isset($value->bodega)) {{ $value->bodega->nombre }} @endif</td>
                  <td> {{$nombre_proveedor}} </td>
                  <td> {{ $value->lote }}</td>
                  <td> {{ $value->serie }}-</td>
                  <td> {{ $value->fecha_vence }}</td>
                  @php
                  $contExist += $value->existencia;
                  $contUsos +=$value->existencia_uso;
                  @endphp
                  <td class="td_der">{{ $value->existencia }}</td>
                  <!--td class="td_der">{{ $value->existencia_uso }}</td-->
                  <td class="td_der">{{ $value->existencia_min }}</td>
                  <td class="td_der">{{ $value->existencia_max }}</td>
                  <td class="td_der">@if(isset($value->id_cab_movimientos))
                    @php
                    $id_empresa = session('id_empresa');
                    $serie = \Sis_medico\InvInventarioSerie::where('serie',$value->serie)->where('id_empresa',$id_empresa)->first();
                    $id_cabecera = $serie->id_cab_movimientos;
                    $cabecera = \Sis_medico\InvCabMovimientos::find($id_cabecera);
                    $id_doc_bodega = $cabecera->id_documento_bodega;
                    $nombre_documento = '';
                    if(!is_null($id_doc_bodega)){
                      $doc_bodega = \Sis_medico\InvDocumentosBodegas::find($id_doc_bodega);
                      
                      if(!is_null($doc_bodega)){
                        $nombre_documento = $doc_bodega->documento;
                      }
                    }
                    @endphp
                    {{$nombre_documento}} @endif
                  </td>
                  @if(Auth::user()->id_tipo_usuario == 1)
                  <td class="td_der">@if(isset($value->inventario)){{str_replace(',','',number_format($value->inventario->costo_promedio,4)) }}@endif</td>
                  @endif
                </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <table>
      <thead>
        <tr class="tabla_footer">
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.ID')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Código')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Marca')}}</th>
          <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Producto')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Pedido')}}</th>
          <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Bodega')}}</th>
          <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Lote')}}</th>
          <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.serie')}}</th>
          <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Fechadevencimiento')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Usos')}}</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}} min</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Existencia')}} max</th>
          <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos_reportes.Costo')}}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th colspan="7" style="text-align:right;">Total: </th>
          <th>{{$contExist}}</th>
          <!--th> {{$contUsos}}</th-->
        </tr>
      </tbody>
    </table>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>


    <script type="text/javascript">
      $(document).ready(function() {

        $('#tbl_compras').DataTable({
          'paging': false,
          'lengthChange': false,
          'searching': false,
          'ordering': false,
          'info': false,
          'autoWidth': false,
          "scrollY": 300,
          "scrollX": true,
          'scrollCollapse': true,
          responsive: false,
          dom: 'Bfrtilp',
          buttons: [{
              extend: 'excelHtml5',
              text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
              titleAttr: 'Exportar a Excel',
              className: 'btn btn-success'
            },
            {
              extend: 'pdfHtml5',
              text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
              titleAttr: 'Exportar a PDF',
              className: 'btn btn-danger'
            },
            {
              extend: 'print',
              text: '<i class="fa fa-print"></i> ',
              titleAttr: 'Imprimir',
              className: 'btn btn-info'
            },
          ]
        });

        tinymce.init({
          selector: '#hc'
        });


      });
    </script>
  </div>
</section>

<form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
  <input type="hidden" name="filid_producto" id="filid_producto" value="{{@$id_producto}}">
  <input type="hidden" name="filid_bodega" id="filid_bodega" value="{{@$id_bodega}}">
  <input type="hidden" name="exportar" id="exportar" value="0">
</form>
<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });


  $('#cuenta').on('select2:select', function(e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filcuentas_detalle").val($("#cuentas_detalle").val());
    $("#filmostrar_detalles").val($("#mostrar_detalles").val());
    $("#print_reporte_master").submit();
  });

  $("#btn_exportar").click(function() {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filid_producto").val($("#id_producto").val());
    $("#filid_bodega").val($("#id_bodega").val());
    //alert($("#cuentas_detalle").prop("checked"));
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function() {
    $('#id_producto').val(<?= $id_producto; ?>);
    $('#id_producto').select2().trigger('change');
    $('#id_bodega').val(<?= $id_bodega; ?>);
    $('#id_bodega').select2().trigger('change');

    tinymce.init({
      selector: '#hc'
    });
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });
  });

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection