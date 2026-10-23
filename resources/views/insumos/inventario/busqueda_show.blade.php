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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css"/>
 
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Insumos</a></li>
      <li class="breadcrumb-item"><a href="../">Busqueda por Serie</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('insumos.inventario.busqueda.serie') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Serie:</label>
          <div class="col-md-9">
            <input type="text" class="form-control" id="serie" name="serie" value="{{ $serie }}" />
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Descripci&oacute;n:</label>
          <div class="col-md-9">
             <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $descripcion }}" />
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Pedido:</label>
          <div class="col-md-9">
            <input type="text" class="form-control input-sm" name="pedido" id="pedido" value="{{$pedido}}">
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
      <div id="tbl_serie_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="tbl_serie" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_serie_info">
              <thead>
                <tr class="well-dark">
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ID</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Marca</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Producto</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Bodega</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Lote</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vence</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Pedido</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cant</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Documento</th>
                  <th width="50%" tabindex="0" aria-controls="tbl_serie" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Observación</th>
                </tr>
              </thead>
              <tbody>
            @php @endphp
                @if(!is_null($inventario) || count($inventario)>0)
                  @foreach ($inventario as $value)
                      <tr>
                        <td> {{ $value->cabecera->id }} </td>
                        <td> {{ $value->cabecera->created_at }} </td>
                        <td> {{ $value->producto->marca->nombre }} </td>
                        <td> {{ $value->producto->codigo }} </td>
                        <td> {{ $value->producto->nombre }} </td>
                        <td> @if(isset($value->cabecera->bodega_destino)){{ $value->cabecera->bodega_destino->nombre }} @elseif(isset($value->cabecera->bodega_origen)) {{ $value->cabecera->bodega_origen->nombre }} @endif  </td>
                        <td class="td_der"> {{ $value->serie}}- </td>
                        <td class="td_center"> {{ $value->lote}} </td>
                        <td class="td_center"> {{ $value->fecha_vence}} </td>
                        @php 
                          $movimiento = \Sis_medico\Movimiento::where('serie',$value->serie)->orderBy('id', 'asc')->first(); 
                          $pedido = null;
                          if (isset($movimiento->pedido)) {
                            $pedido = $movimiento->pedido;  
                          }
                        @endphp
                        <td>  @if(isset($pedido->id)){{ $pedido->pedido }} @endif </td>
                        <td class="td_center"> {{ $value->cantidad}} </td>
                        <td> @if(isset($value->cabecera->documento_bodega)) {{ $value->cabecera->documento_bodega->abreviatura_documento }} - {{ strtoupper($value->cabecera->documento_bodega->documento) }} @endif </td>
                        <td> @if(isset($value->cabecera)) {{ $value->cabecera->observacion }} @endif </td>
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

        $('#tbl_serie').DataTable({
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
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success',
                customize: function(doc) {
                    var sheet = doc.xl.worksheets['sheet1.xml'];
                    $('row c[r^="F"]', sheet).attr( 's', '0' );
                }
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
    </script>
  </div>
</section>

<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  } 
</script>
@endsection