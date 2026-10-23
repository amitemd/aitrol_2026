@extends('contable.balance_general.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
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
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Insumos</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('insumos.inventario.egresoprocedimiento.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
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
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
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
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
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
                <tr>
                  <th>Serie</th>
                  <th>Codigo</th>
                  <th>Producto</th>
                  <th>Marca</th>
                  <th>Tipo</th>
                  <th>Fecha</th>
                  <th>Pedido</th>
                  <th>Procedimiento</th>
                  <th>Ci</th>
                  <th>Paciente</th>
                  <th>Costo</th>
                </tr>
              </thead>
              <tbody>
                @if(!is_null($movimiento) || count($movimiento)>0)
                  @foreach ($movimiento as $value) 
                      <tr>
                        <td> {{ $value->movimiento->serie}} </td>
                        <td> {{ $value->movimiento->producto->codigo}} </td>
                        <td> {{ $value->movimiento->producto->nombre}} </td> 
                        <td> {{ $value->movimiento->producto->marca->nombre}} </td> 
                        <td> INSUMOS - MEDICAMENTOS  </td>
                       
                        <td> {{ date('d/m/Y H:i', strtotime($value->created_at)) }} </td> 
                        <td> {{ $value->movimiento->pedido->pedido }} </td> 
                        <td>
                          @if(isset($value->hc_procedimientos->hc_procedimiento_final))  
                            @if (isset(($value->hc_procedimientos->hc_procedimiento_final)))
                            @if (isset(($value->hc_procedimientos->hc_procedimiento_final->procedimiento)))
                              {{($value->hc_procedimientos->hc_procedimiento_final->procedimiento->nombre)}}
                            @endif
                            @endif
                          @endif </td> 
                        <td> @if(isset($value->hc_procedimientos->historia->paciente)){{ ($value->hc_procedimientos->historia->paciente->id) }}@endif </td>
                        <td> @if(isset($value->hc_procedimientos->historia->paciente)){{ ($value->hc_procedimientos->historia->paciente->nombre1) }}
                          {{ ($value->hc_procedimientos->historia->paciente->nombre2) }}
                          {{ ($value->hc_procedimientos->historia->paciente->apellido1) }}
                          {{ ($value->hc_procedimientos->historia->paciente->apellido2) }}
                          @endif 
                        </td>
                        <td class="td_der">{{ number_format($value->movimiento->precio,2) }}</td>
                      </tr>
                  @endforeach


                @endif

                @if(!is_null($equipo) || count($equipo)>0)
                  @foreach ($equipo as $value) 
                  
                      <tr>
                        <td> {{ $value->equipo->serie }} </td>
                        <td> {{ $value->equipo->modelo }} </td>
                        <td> {{ $value->equipo->nombre }} </td> 
                        <td> {{ $value->equipo->marca }} </td> 
                        <td> EQUIPOS </td>
                        <td> {{ date('d/m/Y H:i', strtotime($value->created_at)) }} </td> 
                        <td>   </td> 
                        <td> @if(isset($value->historia->hc_procedimientos->hc_procedimiento_final->procedimiento))
                              {{$value->historia->hc_procedimientos->hc_procedimiento_final->procedimiento->nombre}} 
                            @endif
                        </td> 
                        <td> @if(isset($value->historia->paciente)) {{$value->historia->paciente->id}} @endif </td>
                        <td> @if(isset($value->historia->paciente))
                          {{ ($value->historia->paciente->nombre1) }}
                          {{ ($value->historia->paciente->nombre2) }}
                          {{ ($value->historia->paciente->apellido1) }}
                          {{ ($value->historia->paciente->apellido2) }}
                          @endif 
                        </td>
                        <td class="td_der"></td>
                      </tr>
                  @endforeach

                  
                @endif
              </tbody>
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
          'ordering': true,
          'info': false,
          'autoWidth': false,
          "scrollY": 300,
          "scrollX": true,
          'scrollCollapse': true,
          "order": [[ 4 , "asc"]],
          responsive:'true',
          dom: 'Bfrtilp',
          buttons:[ 
              {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success'
              },
              {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger'
              },
              {
                extend:    'print',
                text:      '<i class="fa fa-print"></i> ',
                titleAttr: 'Imprimir',
                className: 'btn btn-info'
              },
            ]	
        });

        tinymce.init({
          selector: '#hc'
        });


      });

      // $('#tbl_compras').DataTable({
      //       'paging': false,
      //       dom: 'lBrtip',
      //       'lengthChange': false,
      //       'searching': true,
      //       'ordering': false,
      //       'responsive': true,
      //       'info': false,
      //       'autoWidth': true,
      //       'columnDefs': [
      //           { "width": "5%", "targets": 0 },
      //           { "width": "5%", "targets": 2 },
      //           { "width": "10%", "targets": 6 },
      //           { "width": "5%", "targets": 8 }
      //       ],
      //       language: {
      //           zeroRecords: " "
      //       },
      //       buttons: [{
      //       extend: 'copyHtml5',
      //       footer: true
      //       },
            
      //       {
      //       extend: 'excelHtml5',
      //       footer: true,
      //       title: 'PEDIDO'
      //       },
      //       {
      //       extend: 'csvHtml5',
      //       footer: true
      //       },
      //       {
      //       extend: 'pdfHtml5',
      //       orientation: 'landscape',
      //       pageSize: 'LEGAL',
      //       footer: true,
      //       title: 'PEDIDO',
      //       customize: function(doc) {
      //           doc.styles.title = {
      //           color: 'black',
      //           fontSize: '17',
      //           alignment: 'center'
      //           }
      //       }
      //       }
      //   ],
      //   });
    </script>
  </div>
</section>

<form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
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