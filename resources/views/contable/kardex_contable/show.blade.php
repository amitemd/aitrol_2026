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
    text-align: right;
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

  .t_det {
    font-size: 10px; 
  }
  .tt_det {
    font-size: 10px; 
    font-weight: bold;
  }
  .t_det_r {
    text-align: right;
  }
  
  .t_det_c {
    text-align: center;
  }
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="../">Kardex</a></li>
    </ol>
  </nav>
  <div class="box">

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('contable.compras.kardex.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Producto:</label>
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
          <label for="fecha" class="texto col-md-3 control-label">Bodega:</label>
          <div class="col-md-9">
            <select id="id_bodega" name="id_bodega" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($bodegas as $value)
              <option value="{{$value->id}}"> {{$value->nombre}} - {{$value->empresa->nombrecomercial}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde"
                value="@if(isset($fecha_desde)) {{date('d/m/Y',strtotime($fecha_desde))}} @else {{ date('d/m/Y') }} @endif"
                required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle"
                  onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta"
                value="@if(isset($fecha_hasta)) {{date('d/m/Y',strtotime($fecha_hasta))}} @else {{ date('d/m/Y') }} @endif"
                required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle"
                  onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          {{-- <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button> --}}
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    {{-- <b>Compras del período:</b> --}} 

    {{-- <div class="content" id="contenedor">
      <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="tbl_compras" class="table-bordered table-hover dataTable table-striped" style="max-width:800px;" role="grid"
              aria-describedby="tbl_compras_info">
              <thead>
                <tr class="well-dark">
                  <th width="25%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                    aria-label="Codigo: activate to sort column ascending">Codigo</th>
                  <th width="75%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                    aria-label="Codigo: activate to sort column ascending">Nombre</th>
                </tr>
              </thead>
              <tbody>
                <tr class="well">
                  <td>@if (isset($r['codigo'])) {{ $r['codigo'] }} @endif</td>
                  <td>@if (isset($r['nombre'])) {{ $r['nombre'] }} @endif</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div> --}}
    @foreach ($skardex as $r) 
    <div class="box-body">
      <div class="box">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                  <div class="table-responsive col-md-12">
                      <table id="tbl_detalles" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                          <thead>
                              <tr> 
                                  <th width="20%">Codigo</th>
                                  <th width="40%">Nombre</th>
                                  <th width="40%">Descripcion</th>
                              </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td bgcolor="00cefc"> {{$r['codigo']}}</td>
                              <td bgcolor="00cefc">{{trim($r['nombre'])}}</td> 
                              <td bgcolor="00cefc">{{trim($r['nombre'])}}</td> 
                            </tr> 
                            <tr>
                              <table id="tbl_detalles" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                  <tr>
                                    <th class="tt_det" colspan="5">Detalles</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Entradas</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Salidas</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Saldos</th> 
                                  </tr>
                                    <tr> 
                                        <th class="tt_det" width="10%">Fecha</th>
                                        <th class="tt_det" width="15%">Descripción</th> 
                                        <th class="tt_det" width="10%">Referencia</th>
                                        <th class="tt_det" width="10%">Tipo</th>
                                        <th class="tt_det" width="10%">Bodega</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                    </tr>
                                </thead> 
                                <tbody>
                                  <tr>
                                    <th colspan="4"></th>
                                    <th class="tt_det" width="5%">Saldo Anterior</th>
                                    <th class="tt_det t_det_r" width="5%">{{$r['tcantidad']}}</th>
                                    <th class="tt_det t_det_r" width="5%">{{number_format($r['tvaluni'],2,'.','')}}</th>
                                    <th class="tt_det t_det_r" width="5%">{{number_format($r['ttotal'],2,'.','')}}</th>
                                    <th colspan="6"></th>
                                  </tr>
                                  @php 
                                  $acum_cant = $r['tcantidad'];
                                  $acum_tvaluni = $r['tvaluni'];
                                  $acum_ttotal = $r['ttotal'];
                                  @endphp
                                  @foreach ($r['detales'] as $it)
                                  @php
                                      if($it['tipo']=='I'){
                                        $acum_cant += $it['cantidad'];
                                        $acum_tvaluni = $it['valor_unitario'];
                                        $acum_ttotal = $acum_cant*$acum_tvaluni;
                                      } else {
                                        $acum_cant -= $it['cantidad'];
                                        $acum_tvaluni = $it['valor_unitario'];
                                        $acum_ttotal += $acum_cant*$acum_tvaluni;
                                      }
                                  @endphp
                                      <tr>
                                        <td class="t_det">{{date('d/m/Y', strtotime($it['fecha']))}}</td>
                                        <td class="t_det">{{$it['descripcion']}}</td>
                                        <td class="t_det">{!!$it['referencia']!!}</td>
                                        <td class="t_det">@if($it['tipo']=='I') INGRESO @else EGRESO @endif</td>
                                        <td class="t_det">{{$it['bodega']}}</td>
                                        @if($it['tipo']=='I')
                                          <td class="t_det t_det_r">{{$it['cantidad']}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['valor_unitario'],2,'.','')}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['total'],2,'.','')}}</td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                        @else 
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r">{{$it['cantidad']}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['valor_unitario'],2,'.','')}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['total'],2,'.','')}}</td>
                                        @endif
                                        <td class="t_det t_det_r">{{$acum_cant}}</td>
                                        <td class="t_det t_det_r">{{number_format($acum_tvaluni,2,'.','')}}</td>
                                        <td class="t_det t_det_r">{{number_format($acum_ttotal,2,'.','')}}</td>
                                      </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </div>
  @endforeach

    {{-- <div class="general form-group ">
      <div class="col-md-12">
        @foreach ($skardex as $r) 
          <table id="tbl_detalles" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
            <thead>
              <tr role="row" style="text-align: center;">
                 <th>Código</th>
                <th>Nombre</th>
              </tr>
            </thead>
            <tbody>  
              <tr>
                <td>{{$r['codigo']}}</td>
                <td>{{$r['nombre']}}</td> 
              </tr> 
            </tbody>
          </table>
          @endforeach
      </div> 
    </div> --}}







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
      $(document).ready(function () {

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
          responsive: 'true',
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
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $('#seguimiento').on('hidden.bs.modal', function () {
    $(this).removeData('bs.modal');
  });


  $('#cuenta').on('select2:select', function (e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function (e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function () {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filcuentas_detalle").val($("#cuentas_detalle").val());
    $("#filmostrar_detalles").val($("#mostrar_detalles").val());
    $("#print_reporte_master").submit();
  });

  $("#btn_exportar").click(function () {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filid_producto").val($("#id_producto").val());
    $("#filid_bodega").val($("#id_bodega").val());
    //alert($("#cuentas_detalle").prop("checked"));
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function () {
    $('#id_producto').val({{ $id_producto }});
    $('#id_producto').select2().trigger('change');
    $('#id_bodega').val(
      {{
        $id_bodega
      }}
    );
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

  $(function () {
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
    $("#fecha_desde").on("dp.change", function (e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function (e) {
      verifica_fechas();
    });

    //alert('{{$fecha_hasta}}');

  });

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
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