@extends('contable.ats.base')
@section('action-content')
<style>
  div.dataTables_wrapper {
    width: 95%;
    margin: 0 auto;
  }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="../">ATS</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('ats.index') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Periodo:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="periodo" id="periodo" value="{{$periodo}}" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('periodo').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="btn_generar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Generar en pantalla
          </button>
          <button type="button" class="btn btn-primary" id="btn_guardar">
            <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Guardar
          </button>
          {{-- <button type="button" class="btn btn-primary" id="btn_exportar_xls">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar excel
            </button>
            <button type="button" class="btn btn-primary" id="btn_exportar_xml" name="btn_exportar_xml">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar xml
            </button>  --}}
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">CONCILIACI&Oacute;NES BANCARIAS</label>
      </div>
    </div>
    <form method="POST" id="store_reporte_master" action="{{ route('ats.store') }}">
      {{ csrf_field() }}
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="row">
                    <div class="col-md-12">
                      <!-- Custom Tabs -->
                      <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#tab_1" data-toggle="tab">Ventas</a></li>
                          <li><a href="#tab_2" data-toggle="tab">Compras</a></li>
                          <li><a href="#tab_3" data-toggle="tab">Comprobantes Anulados</a></li>
                          <li><a href="#tab_4" data-toggle="tab">ATS Generados</a></li>
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                            @include('contable.ats.ventas')
                            {{-- <b>Ventas del período:</b>
                                <div id="contenedor">
                                  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                      <div class="row">
                                        <div class="table-responsive col-md-12">
                                          <table id="tbl_ventas" class="table table-sm table-bordered table-condensed dataTable table-striped" role="grid" aria-describedby="example2_info">
                                            <thead>
                                              <tr class="well-dark">
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                                                <th width="10%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Clie</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RUC/Cédula</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Com</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">SubTotal.</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">B.No Iva</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base 0%</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base Iva</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto Iva</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Iva</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Fue</th>
                                              </tr>
                                            </thead>
                                            <tbody id="tbl_detalles" name="tbl_detalles">
                                              @foreach ($ventas as $value)
                                              <tr class="well">
                                                <td ><input type="checkbox" id="id_factura_{{ $value['id'] }}" class="form-check-input" name="id_factura_{{ $value['id'] }}" onchange="actualizar(this);" value="{{ $value['id'] }}" @if(@$value['validado']=="1") checked @endif> &nbsp; {{ $value['id'] }}</td>
                            <td>VEN-FA</td>
                            <td>{{ $value['sucursal'] }}-{{ $value['caja'] }}-{{ $value['numero'] }}</td>
                            <td>{{ $value->paciente->nombre1 }} &nbsp; {{ $value->paciente->apellido1 }}</td>
                            <td></td>
                            <td>{{ $value->id_paciente }}</td>
                            <td>18</td>
                            <td>{{ number_format($value['subtotal'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['base_imponible'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['tarifa_0'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['tarifa_12'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['iva'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['iva'], 2, '.', '')  }}</td>
                            <td>{{ number_format($value['iva'], 2, '.', '')  }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                            </table>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-xs-2">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros {{count($ventas)}} </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-2">
                            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true, 'tbl_detalles')" class="btn btn-default">
                              <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                            </button>
                          </div>
                          <div class="col-xs-2">
                            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false, 'tbl_detalles')" class="btn btn-default">
                              <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                            </button>
                          </div>
                        </div>
                      </div>
                    </div> --}}

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_2">
                    @include('contable.ats.compras')
                    {{-- <b>Compras del período:</b>
                                                    <div id="contenedor">
                                                      <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                                          <div class="row">
                                                            <div class="table-responsive col-md-12">
                                                              <table id="tbl_compras" class="table table-sm table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_compras_info">
                                                                <thead>
                                                                  <tr class="well-dark">
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Linea</th>
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                                                                    <th width="10%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sust.</th>
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Prov</th>
                                                                    <th width="10%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RUC/Cédula</th>
                                                                    <th width="5%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Com</th>
                                                                    <th width="10%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estab.</th>
                                                                    <th width="10%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">P.Emi</th>
                                                                    <th width="10%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                                                                    <th width="35%" class="" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                                                                  </tr>
                                                                </thead>
                                                                <tbody id="tbl_detalle_compras" name="tbl_detalle_compras">
                                                                  @foreach ($compras as $value)
                                                                  <tr class="well">
                                                                    <td ><input type="checkbox" id="id_compra_{{ $value['id'] }}" class="form-check-input" name="id_compra_{{ $value['id'] }}" onchange="actualizar(this);" value="{{ $value['id'] }}" @if(@$value['validado']=="1") checked @endif> &nbsp; {{ $value['id'] }}</td>
                    <td>{{ $value['secuencia_f'] }}</td>
                    <td>{{ $value['tipo'] }}</td>
                    <td>{{ $value->proveedorf->razonsocial }}</td>
                    <td>{{ $value['credito_tributario'] }}</td>
                    <td>{{ $value['id_tipoproveedor'] }}</td>
                    <td>{{ $value['proveedor'] }}</td>
                    <td>{{ $value['tipo_comprobante'] }}</td>
                    @php list($est, $pto, $sec) = explode("-", $value['numero']); @endphp
                    <td>{{ $est }}</td>
                    <td>{{ $pto }}</td>
                    <td>{{ $sec }}</td>
                    <td>{{ $value['autorizacion'] }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-2">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros {{count($compras)}} </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true,'tbl_detalle_compras')" class="btn btn-default">
                      <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                    </button>
                  </div>
                  <div class="col-xs-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false,'tbl_detalle_compras')" class="btn btn-default">
                      <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                    </button>
                  </div>
                </div>
              </div>
            </div> --}}
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_3">
            @include('contable.ats.anulados')
            {{-- <b>Comprobantes Anulados:</b>
                                <div id="contenedor">
                                  <div id="tbl_comp_anula_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                      <div class="row">
                                        <div class="table-responsive col-lg-12">
                                          <table id="tbl_comp_anula" class="table table-sm table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_comp_anula_info">
                                            <thead>
                                              <tr class="well-dark">
                                                <th width="10%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                                                <th width="10%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Comp.</th>
                                                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Est.</th>
                                                <th width="10%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Pto. Emi.</th>
                                                <th width="15%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia D</th>
                                                <th width="15%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia Ha</th>
                                                <th width="30%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                                                <th width="10%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Anulación</th>
                                              </tr>
                                            </thead>
                                            <tbody id="tbl_detalle_comp_anul" name="tbl_detalle_comp_anul">
                                            </tbody>
                                          </table>
                                        </div>
                                      </div>
                                  </div>
                                </div> --}}
          </div>
          <div class="tab-pane" id="tab_4">
            @include('contable.ats.generados')
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- nav-tabs-custom -->
  </div>
  <!-- /.col -->
  </div>
  <!-- /.row -->
  </div>
  </div>
  </div>
  </div>
  </div>
  </div>
  </div>
</section>
<input type="hidden" name="filperiodo" id="filperiodo" value="{{$periodo}}">
</form>
<form method="POST" id="print_reporte_master" action="{{ route('ats.show') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filtroperiodo" id="filtroperiodo" value="{{$periodo}}">
  <input type="hidden" name="id_ats" id="id_ats" value="">
  <input type="hidden" name="xls" id="xls" value="0">
  <input type="hidden" name="xml" id="xml" value="0">
</form>
<!-- /.content -->
<!--<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>-->
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#tbl_ventas').DataTable({
      'paging': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      "scrollY": 500,
      "scrollX": true,
      'scrollCollapse': true,
    });
    $('#tbl_compras').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      "scrollY": 500,
      "scrollX": true,
      'scrollCollapse': true,
    });


    $('#tbl_comp_anula').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'scrollCollapse': true,
    });

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
    $('#periodo').datetimepicker({
      format: 'YYYY/MM',

    });
    $("#periodo").on("dp.change", function(e) {
      //buscar();
    });

  });


  $("#btn_guardar").click(function() {

    $("#store_reporte_master").submit();
  });

  // $("#btn_exportar_xml").click(function() {
  //     $("#filperiodo").val($("#periodo").val());
  //     $("#xls").val(0);
  //     $("#xml").val(1);
  //     $("#print_reporte_master" ).submit();
  // });

  function seleccionar_todo(checked, tablename) {
    var miTabla = document.getElementById(tablename);
    for (i = 0; i < miTabla.rows.length; i++) {
      miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
    }
  }

  function exportarxls(id) {
    $("#id_ats").val(id);
    $("#filperiodo").val($("#periodo").val());
    $("#xls").val(1);
    $("#xml").val(0);
    $("#print_reporte_master").submit();
  }

  function exportarxml(id) {
    $("#id_ats").val(id);
    $("#filperiodo").val($("#periodo").val());
    $("#xls").val(0);
    $("#xml").val(1);
    $("#print_reporte_master").submit();
  }
</script>
@endsection