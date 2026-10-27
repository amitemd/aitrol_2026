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

  .td_der{
    text-align: center;
  }
  .td_der1
  {
    text-align: center;
    font-weight: bold;
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
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css"/>
 
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('winsumos_reportes.insumos')}}</a></li>
      <li class="breadcrumb-item"><a href="../">{{trans('winsumos.existencia')}}</a></li>
    </ol>
  </nav>
  <div class="box">

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('winsumos_reportes.buscador')}}</label>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('insumos.inventario.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.productos')}}:</label>
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
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.bodegas')}}:</label>
          <div class="col-md-9">
              <select id="id_bodega" name="id_bodega"  class="form-control select2_cuentas" style="width: 100%;">
                  <option> </option>
                  @foreach($bodegas as $value)
                      <option value="{{$value->id}}"> {{$value->nombre}} @if(isset($value->empresa))- {{$value->empresa->nombrecomercial}} @endif</option>
                  @endforeach
              </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.ingrese_nombre_producto')}}:</label>
          <div class="col-md-9">
              <input type="text" name="producto" class="form-control" @if(isset($busq)) @if(!is_null($busq['producto'])) value="{{$busq['producto']}}" @endif @endif >
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="tipo_pro" class="texto col-md-3 control-label">{{trans('winsumos.TipodeProductos')}}:</label>
          <div class="col-md-9">
            <select id="tipo_pro" name="tipo_pro" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($tipo_prod as $value)
              <option @if ($tipo_pro == $value->id) selected @endif value="{{$value->id}} "> {{$value->nombre}} </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.fecha_desde')}}:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if(isset($fecha_desde)) {{date('d/m/Y',strtotime($fecha_desde))}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div> --}}

        {{-- <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('winsumos.fecha_hasta')}}:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{date('d/m/Y',strtotime($fecha_hasta))}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div> --}}


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
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.codigo')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.marca')}}</th>
                 
                  <th width="15%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.productos')}}</th>
                  <th width="15%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.bodegas')}}</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.existencia')}}</th>
                  <!--th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('winsumos.usos')}}</th-->
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> {{trans('winsumos.StockMinimo')}}  </th>
                  @if(Auth::user()->id_tipo_usuario == 1)
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> {{trans('winsumos.ValorUnitario')}}  </th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> {{trans('winsumos.ValorTotal')}}</th>
                  @endif
                </tr>
              </thead>
              
              <tbody>
                @if(!is_null($inventario) || count($inventario)>0)
                @php $acum_costo = 0;
                $acum_existencia =0;
                $acum_total =0;
                $acum_existencia_min = 0;

                @endphp
                  @foreach ($inventario as $value)
                      @php $total = $value->quantity * $value->averageCost; @endphp
                      <tr>
                        <td class="td_der">{{ $value->id}}</td>
                        <td>{{ $value->productCode }}</td>
                        <td>{{ $value->brandName }}</td>
                        <td>{{ $value->productName }}</td>
                        <td>{{ $value->warehouseName }}</td>
                                         
                        <td class="td_der">{{ $value->quantity }}</td>
                        <!--td class="td_der">{{ $value->existencia_uso }}</td-->
                        <td class="td_der">{{ $value->minimumQuantity }}</td>
                        @if(Auth::user()->id_tipo_usuario == 1)
                        <td class="td_der"> {{ number_format($value->averageCost,4) }} </td>
                        <td class="td_der"> {{ number_format($total,4) }} </td>
                        @endif
                      </tr>       
                 
                  @php 
                   $acum_existencia += $value->quantity;
                   $acum_existencia_min += $value->minimumQuantity;
                   $acum_total += $total;
                  
                  @endphp
                  
                  @endforeach
                @endif
                </tbody>
                @if(Auth::user()->id_tipo_usuario == 1)
                <tfoot>
                  <tr>
                    <td class="td_der1"> Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td class="td_der1"> {{$acum_existencia}} </td>
                    <!--td class="td_der1">{{$acum_existencia_uso}}</td-->
                    <td class="td_der1"> - </td>
                    @php  $acum_costo = $acum_total == 0 ? 0 : ($acum_total / $acum_existencia); 
                    
                    @endphp
                    <td class="td_der1">  {{ number_format($acum_costo,4)}} </td>
                    <td class="td_der1"> {{number_format($acum_total,4)}} </td>
                  </tr>
                <tfoot>
                @endif
             
            </table>
          </div>
        </div>
      </div>
    </div>
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
          responsive:'true',
          dom: 'Bfrtilp',
          buttons:[ 
              {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                titleAttr: "{{trans('winsumos_reportes.exportar_excel')}}",
                title: 'Reporte de Inventario en Existencias',
                className: 'btn btn-success'
              },
              {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
                titleAttr: "{{trans('winsumos_reportes.exportar_pdf')}}",
                orientation: 'landscape',
                title: 'Reporte de Inventario en Existencias',
                className: 'btn btn-danger'
              },
              {
                extend:    'print',
                text:      '<i class="fa fa-print"></i> ',
                titleAttr: "{{trans('winsumos.imprimir')}}",
                title: 'Reporte de Inventario en Existencias',
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
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
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
    $('#id_producto').val({{$id_producto}});
    $('#id_producto').select2().trigger('change');
    $('#id_bodega').val({{$id_bodega}});
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

  $(function() {
    $('#fecha_desde').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

    //alert('{{$fecha_hasta}}');

  });

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: "{{trans('winsumos.error')}}",
        text: "{{trans('winsumos.verifique_rango_fechas')}}"
      });
    }
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection
