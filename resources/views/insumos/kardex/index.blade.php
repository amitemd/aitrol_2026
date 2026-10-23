@extends('contable.balance_general.base')
@section('action-content')

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css"/>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('winsumos_reportes.insumos')}}</a></li>
      <li class="breadcrumb-item"><a href="../">{{trans('winsumos_reportes.kardex')}}</a></li>
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
        <form method="POST" id="frm_consulta" action="{{ route('insumos.kardex.show') }}" >
          {{ csrf_field() }}
          <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.codigo_producto')}}:</label>
            <div class="col-md-9">
                <select id="codigo_producto" name="codigo_producto"  class="form-control select2_cuentas" style="width: 100%;">
                    <option> </option>
                    @foreach($productos as $value)
                        <option value="{{$value->id}}"> {{$value->codigo}}</option>
                    @endforeach
                </select>
            </div>
          </div>
          <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.productos')}}:</label>
            <div class="col-md-9">
                <select id="id_producto" name="id_producto"  class="form-control select2_cuentas" style="width: 100%;">
                  <option> </option>
                    @foreach($productos as $key => $value)
                        <option  @if($key == 0) selected @endif value="{{$value->id}}"> {{$value->nombre}}</option>
                    @endforeach
                </select>
            </div>
          </div>

          <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.bodegas')}}:</label>
            <div class="col-md-9">
                <select id="id_bodega" name="id_bodega"  class="form-control select2_cuentas" style="width: 100%;"> 
                    @foreach($bodegas as $key => $value)
                        <option @if($key == 0) selected @endif value="{{$value->id}}"> {{$value->nombre}} @if(isset($value->empresa))- {{$value->empresa->nombrecomercial}} @endif</option>
                    @endforeach
                </select>
            </div>
          </div>

          <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('winsumos.fecha_desde')}}:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="{{$fecha_desde}}" required autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('winsumos.fecha_hasta')}}:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta"  value="{{$fecha_hasta}}" required autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>


          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <button type="button" class="btn btn-primary" onclick="cargar_kardex()">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('winsumos.Buscar')}}
            </button>
            <!--button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('winsumos_reportes.exportar')}}
            </button-->
          </div>
        </form>
        <form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
          {{ csrf_field() }}
          <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
          <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
          <input type="hidden" name="filid_producto" id="filid_producto" value="{{@$id_producto}}">
          <input type="hidden" name="filid_producto" id="filid_bodega" value="{{@$id_bodega}}">
          <input type="hidden" name="exportar" id="exportar" value="0">
        </form>
        <div class="form-group col-md-12" id="table">
        </div>

        <div class="form-group col-md-12" id="table_usos">
        </div>
        
      </div>
      <!-- /.box-body -->
    </div>


  </section>
  <!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>


<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
        });

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

    $( "#btn_exportar" ).click(function() { //alert();
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filid_producto").val($("#id_producto").val());
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
          title: "{{trans('winsumos.error')}}",
          text: "{{trans('winsumos.verifique_rango_fechas')}}"
      });
    }
  }
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function cargar_kardex() {
    //$("#table").empty();
    $.ajax({
      type: 'post',
      url:"{{route('insumos.kardex.show')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

      datatype: 'json',
      data: $("#frm_consulta").serialize(),
      beforeSend: function(){
        Swal.fire({
            text: "Cargando...",
            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
            imageWidth: 200,
            imageHeight: 200,
            imageAlt: 'Custom image',
        })
      },
      success: function(data){
        // console.log(data);
        Swal.close();
        //$("#table").append(data); VER###A PARA EL QUE HIZO ESTO
        $("#table").empty().html(data);
        //reloadtable();////////////////////////////////////////////////////////////////////////////////////////////////////
        // $('#tbl_kardex').data.reload();
        // if(data.msj=='error'){
        // Swal.close();
        //   Swal.fire('Error!','Ocurrio un error al guardar el ingreso del pedido. <br> '+data.error ,'error');
        // }

      },
      error: function(data){
          Swal.close();
          console.log(data);
      }
    })

    cargar_kardex_por_usos();
  }

  function cargar_kardex_por_usos(){
    $.ajax({
      type: 'post',
      url:"{{route('insumos.kardex.vtindex_kardex_usos_series')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

      datatype: 'json',
      data: $("#frm_consulta").serialize(),
      beforeSend: function(){
        
      },
      success: function(data){
        
        $("#table_usos").empty().html(data);
        
      },
      error: function(data){
          Swal.close();
          console.log(data);
      }
    })  
  }

  function cargar_usos( serie ){
    $.ajax({
      type: 'post',
      url:"{{url('vt_kardex_usos/index/por_serie')}}/"+serie,
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

      datatype: 'json',
      data: $("#frm_consulta").serialize(),
      beforeSend: function(){
        
      },
      success: function(data){
        
        $("#mostrar_serie"+serie).empty().html(data);
        
      },
      error: function(data){
          Swal.close();
          console.log(data);
      }
    })  
  }
  


  function reloadtable() {
    $('#tbl_kardex').DataTable({
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
            className: 'btn btn-success'
          },
          {
            extend:    'pdfHtml5',
            text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            titleAttr: "{{trans('winsumos_reportes.exportar_pdf')}}",
            className: 'btn btn-danger'
          },
          {
            extend:    'print',
            text:      '<i class="fa fa-print"></i> ',
            titleAttr: "{{trans('winsumos_reportes.imprimir')}}",
            className: 'btn btn-info'
          },
        ]
    });
  }

  $(document).ready(function() {

    /*$('#tbl_kardex').DataTable({
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
            className: 'btn btn-success'
          },
          {
            extend:    'pdfHtml5',
            text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            titleAttr: "{{trans('winsumos_reportes.exportar_pdf')}}",
            className: 'btn btn-danger'
          },
          {
            extend:    'print',
            text:      '<i class="fa fa-print"></i> ',
            titleAttr: "{{trans('winsumos_reportes.imprimir')}}",
            className: 'btn btn-info'
          },
        ]
    });*/

    tinymce.init({
      selector: '#hc'
    });


  });
</script>
@endsection
