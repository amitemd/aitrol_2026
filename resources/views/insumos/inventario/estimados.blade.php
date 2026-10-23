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
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Compras</a></li>
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
        <form method="POST" id="reporte_master" action="{{ route('kardex.show') }}" >
        {{ csrf_field() }}

        <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Producto:</label>
          <div class="col-md-9">
              <select id="producto_id" name="producto_id"  class="form-control select2_cuentas" style="width: 100%;" required>
                  <option> </option>
 
              </select>
          </div>
        </div>

        <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if(isset($fecha_desde)) {{@$fecha_desde}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta"  value="@if(isset($fecha_hasta)) {{@$fecha_hasta}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>
      </form>
      </div>
      <!-- /.box-body -->
        <div class="box-body">
            @foreach($estimados as $key=>$control)
            @php 
              $cantidad=0;
              $precio=0;
            @endphp 
            <div class="panel-default" style="text-align: center;">
             <div class="panel-heading">
             <span class="panel-title" style="font-weight: bold;" >  {{$key}}  </span>

             </div>
             
            <div class="panel-body">
              <table class=" example2 col-md-12 table table-bordered table-hover dataTable noacti" style="width: 100%;">
                  <thead>
                      <tr class="well-dark">
                          <th>Codigo</th>
                          <th>Pedido</th>
                          <th>Fecha</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th>Observacion</th>
                      </tr>
                  </thead>
                  <tbody> 
                      @foreach($control as $fetch)
                          @php  
                            $cantidad+=$fetch['cantidad'];
                            $precio+=$fetch['total'];
                          @endphp 
                          <tr>
                            <td>{{$fetch['codigo']}}</td>
                            <td>{{$fetch['pedido']}}</td>
                            <td>{{date('d-m-Y',strtotime($fetch['fecha']))}}</td>
                            <td>{{$fetch['cantidad']}}</td>
                            <td>{{ number_format($fetch['total'],2)}}</td>
                            <td>{{$fetch['observacion']}}</td>
                          </tr>
                      @endforeach
                  </tbody>
              </table>
              Existente: <span style="font-weight: bold;">  {{$cantidad}} </span> <br>
              Precio F: <span style="font-weight: bold"> {{$precio}} </span>
            </div>

            </div>
            
            @endforeach
        </div>
    </div>
  </section>

 
  <!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
        });
        $('.example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        "scrollY": 200,
        "scrollX": true,
            'scrollCollapse': true,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });
       
        $('#producto_id').select2().trigger('change');

    });

    $('#seguimiento').on('hidden.bs.modal', function(){
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

    $( "#btn_imprimir" ).click(function() {
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filcuentas_detalle").val($("#cuentas_detalle").val());
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());
        $("#print_reporte_master" ).submit();
    });

    $( "#btn_exportar" ).click(function() { alert();
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filproducto_id").val($("#producto_id").val());
        $("#exportar").val(1);
        $("#print_reporte_master" ).submit();
    });

    $(document).ready(function(){

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
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
  function verifica_fechas(){
    if(Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>
@endsection
