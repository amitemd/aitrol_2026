@extends('insumos.transito.base')
@section('action-content')
<style type="text/css">
  .ui-corner-all {
    -moz-border-radius: 4px 4px 4px 4px;
  }

  .ui-widget {
    font-family: Verdana, Arial, sans-serif;
    font-size: 15px;
  }

  .ui-menu {
    display: block;
    float: left;
    list-style: none outside none;
    margin: 0;
    padding: 2px;
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

  .ui-menu .ui-menu-item {
    clear: left;
    float: left;
    margin: 0;
    padding: 0;
    width: 100%;
  }

  .ui-menu .ui-menu-item a {
    display: block;
    padding: 3px 3px 3px 3px;
    text-decoration: none;
    cursor: pointer;
    background-color: #ffffff;
  }

  .ui-menu .ui-menu-item a:hover {
    display: block;
    padding: 3px 3px 3px 3px;
    text-decoration: none;
    color: White;
    cursor: pointer;
    background-color: #006699;
  }

  .ui-widget-content a {
    color: #222222;
  }

  .colorB {
    background-color: #1DE9B6;
    border-radius: 10px;
    margin-right: 10px !important;
    width: 20%;
    text-align: center;
    color: white;
  }

  .colorA {
    background-color: #82B1FF;
    border-radius: 10px;
    width: 20%;
    margin-right: 10px !important;
    text-align: center;
    color: white;
  }

  .colorC {
    background-color: #A5D6A7;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorE {
    background-color: #DD2C00;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorD {
    background-color: #80CBC4;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorB1 {
    background-color: #1DE9B6;

    margin-right: 10px !important;
    font-weight: bold;
    text-align: center;
    color: white;
  }

  .colorA1 {
    background-color: #82B1FF;

    font-weight: bold;
    margin-right: 10px !important;
    text-align: center;
    color: white;
  }

  .colorC1 {
    background-color: #A5D6A7;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }

  .colorE1 {
    background-color: #DD2C00;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }

  .colorD1 {
    background-color: #80CBC4;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }
</style>
<!-- Ventana modal editar -->
<div class="modal fade" id="agregarproductos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="detalles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <form action="{{route('transito.searching')}}" method="POST">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-sm-9">
            <h3 class="box-title">Ingreso a Bodega de Productos</h3>
          </div>

          <div class="col-md-2 ">
            <a class="btn btn-primary btn-gray" href="{{ route('transito.create') }}" data-toggle="modal" data-target="#agregarproductos" style="width: 100%;">Agregar Producto a Transito</a>
          </div>
          <div class="col-md-1 ">
            <a class="btn btn-primary btn-gray col-md-1" href="{{ route('transito_excel') }}"  target="_blank"   style="width: 100%;"> <i class="fa fa-file-excel-o"></i> </a>
          </div>
        </div>
      </form>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="box">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Bodega </th>
                    <th>Bodega en Transito</th>
                    <th>Visualizar</th>
                    <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->
                   
                  </tr>
                </thead>
                <tbody>
                  @foreach ($productos as $value)
                 
                  <tr>
                    <td>{{$value->codigo}}</td>
                    <td>{{ $value->nombre }}</td>
                    @php 
                      $sp= $value->transito+$value->ingreso;
                    @endphp
                    <td>{{$sp}}</td>
                    <td style="text-align: center;">{{$value->ingreso}}</td>
                    <td @if($value->transito<=0) class="colorE1" @else style="text-align: center;" @endif>{{$value->transito}}</td>
                    <td> <a class="btn btn-primary btn-gray btn-xs" href="{{ route('transito.modals',['id'=>$value->id_producto]) }}" data-toggle="modal" data-target="#detalles" style="width: 100%;"> <i class="fa fa-eye"></i> </a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"></div>
            </div>
            <div class="col-sm-7" style="padding-right: 30px">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- /.content -->


<!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#agregarproductos').on('hidden.bs.modal', function() {
    $(this).removeData('  bs.modal');
  });
  $('#detalles').on('hidden.bs.modal', function() {
    $(this).removeData('  bs.modal');
  });
  $(document).ready(function() {
    $('#fecha').datetimepicker({
      useCurrent: false,
      format: 'YYYY/MM/DD',
      //Important! See issue #1075

    });
    $('#vencimiento').datetimepicker({
      useCurrent: false,
      format: 'YYYY/MM/DD',
      //Important! See issue #1075

    });

  });


  function confirmarSalida() {
    return "Va a abandonar esta página. Cualquier cambio no guardado se perderá";
  }

  function beforeVoid() {}

  function valida(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if (tecla == 8) {
      return true;
    }

    // Patron de entrada, en este caso solo acepta numeros
    patron = /[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  }

  function eliminardato(valor) {
    var nombre1 = "dato" + valor;
    var nombre2 = 'visibilidad' + valor;
    document.getElementById(nombre1).style.display = 'none';
    document.getElementById(nombre2).value = 0;

  }

  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': false
  })
  $(function() {
    $('#fecha_desde').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
    });
    $('#fecha_hasta').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

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
</script>
@endsection