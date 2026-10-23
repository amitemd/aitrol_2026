@extends('contable.pedidos.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 12px;
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
    margin-left: 40px;
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

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }

  .secundario {
    left: 10px;
  }

  .table {
    margin-bottom: -10px;
  }

  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
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
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .hidden-paginator {

    display: none;

  }

  .removethe {
    display: none;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
      <li class="breadcrumb-item"><a href="#">Pedidos</a></li>
    </ol>
  </nav>

  <input type="hidden" id="token" value="{{ csrf_token() }}">

  <div class="box dobra" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 header">
        <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body ">
      <form method="POST" id="reporte_master" action="{{ route('PedidoController.pedidos_insumos') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
             <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{ $fecha_desde }}">

            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
             <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{ $fecha_hasta }}">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>

        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_proveedor[]" id="id_proveedor" class="form-control select2_proveedor" style="width: 100%;" multiple="multiple">
            <option value="">{{trans('contableM.seleccione')}}...</option>
            <option> </option>
          </select>
        </div>

        <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center; width:100%;">
          <thead>
            <tr>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Id</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Factura</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Numero de Documento</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Nombre Comercial</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Fecha</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Nombre</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">cantidad</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">valor unitario</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">subtotal</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Iva</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Descuento</th>
              <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">total</th>
            </tr>
          </thead>

          <tbody>
            @foreach($resultados as $x)
            <tr>
              <td>{{$x->id}}</td>
              <td>{{$x->factura}}</td>
              <td>{{$x->numero_documento}}</td>
              <td>{{$x->nombrecomercial}}</td>
              <td style="text-align: left;">{{$x->fecha}}</td>
              <td style="text-align: left;">{{$x->nombre}}</td>
              <td style="text-align: left;">{{$x->cantidad}}</td>
              <td>{{$x->valor_unitario}}</td>
              <td>{{$x->subtotal}}</td>
              <td style="text-align: left;">{{$x->iva}}</td>
              <td>{{$x->descuento}}</td>
              <td>{{$x->total}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </form>
    </div>
  </div>
</section>
</div>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">

  $(function() {
    $('.infinite-scroll').jscroll({
      autoTrigger: true,
      loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
      padding: 0,
      nextSelector: '.pagination li.active + li a',
      contentSelector: 'div.infinite-scroll',
      callback: function() {
        $('div.paginationLinks').remove();

      }
    });
  });

  
  // WORKS AT NOVEMBER 23 2020
  
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });
  $('#example2').DataTable({
    'paging': true,
    dom: 'lBrtip',
    'lengthChange': false,
    'searching': true,
    "iDisplayLength": 20,
    'ordering': false,
    'responsive': true,
    'info': false,
    'autoWidth': true,
    buttons: [{
        extend: 'copyHtml5',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        title: `Excel_pedidos_insumos` + '{{date("d/m/Y")}}',
      },
      {
        extend: 'csvHtml5',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        pageSize: 'TABLOID',
        footer: true,
        title: `Excel_pedidos_insumos` + '{{date("d/m/Y")}}',
        customize: function(doc) {
          doc.styles.title = {
            color: 'black',
            fontSize: '16',
            alignment: 'center'
          }
        }
      }
    ],
  })

  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit(); 
  });

  function excel() {
    $("#print_reporte_master").submit();
  }
  $("#nombre_proveedor").autocomplete({
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
  $('#fact_contable_check').on('ifChanged', function(event) {
    //aqui funciona si cambio el input time
    if ($(this).prop("checked")) {
      $("#esfac_contable").val(1);
      $("#es_fact_dos").val(1);
    } else {
      $("#esfac_contable").val(0);
    }

  });

  $(document).ready(function() {
    $(".select2_proveedor").select2({
      width: "100%",
      allowClear: true,
      noResults: function() {
        return "No hay resultado";
      },
      searching: function() {
        return "Buscando..";
      },
      placeholder: "--Seleccione--",
      minimumInputLength: 3,
      ajax: {
        url: "{{route('PedidoController.buscar_proveedor')}}",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        dataType: "json",
        delay: 250,
  
        data: function(params) {
          return {
            term: params.term
          };
        },
        processResults: function(response) {
          return {
            results: response
          };
        },
        error(error) {
          console.log(error);
        },
        cache: true,
      },
    });
  })



  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

 

</script>
@endsection