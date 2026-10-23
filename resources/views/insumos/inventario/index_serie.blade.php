@extends('contable.balance_general.base')
@section('action-content')

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Insumos</a></li>
      <li class="breadcrumb-item"><a href="../">Existencias Serie</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('insumos.inventario_serie.show') }}">
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
              <option value="{{$value->id}}"> {{$value->nombre}} - @if(isset($value->empresa)){{$value->empresa->nombrecomercial}} @endif</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Descripci&oacute;n:</label>
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
      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
    </button>
    <!--<button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>-->
  </div>
  </form>
  </div>
  <!-- /.box-body -->
  </div>
</section>

<form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
  <input type="hidden" name="filid_producto" id="filid_producto" value="{{@$id_producto}}">
  <input type="hidden" name="filid_producto" id="filid_bodega" value="{{@$id_bodega}}">
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

    $('#id_producto').val({
      //{
        $id_producto
      //}
    });
    $('#id_producto').select2().trigger('change');

    $('#id_bodega').val({
      //{
        $id_bodega
      //}
    });
    $('#id_bodega').select2().trigger('change');

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

  $("#btn_exportar").click(function() { //alert();
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filid_producto").val($("#id_producto").val());
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function() {

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
</script>
@endsection