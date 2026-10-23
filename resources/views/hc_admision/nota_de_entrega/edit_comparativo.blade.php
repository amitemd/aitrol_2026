@extends('contable.balance_comprobacion.base')
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

  .text-left {
    text-align: left;
  }

  .green {
    color: #000;
    background-color:  #000;
    height: 10px;
    font-weight: bold;
  }

  .red {
    color: #a93d3d;
    background-color: #a93d3d;
    height: 10px;
    font-weight: bold;
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
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
    </ol>
  </nav>

  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
    </div>
    <!-- /.box-body -->
    <form method="POST" class="forpm" id="post_master" target="_blank">
      {{ csrf_field() }}




      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
              </div>

              <div class="box-body">

                <div id="imprimir">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4">
                      <dl>
                      <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                      <dd>&nbsp; {{$empresa->id}}</dd>
                    </dl>
                      </div>
                      <div class="col-md-8" style="text-align: right;">
                          <button class="btn btn-primary" type="button" onclick="return window.history.back();"> <i class="fa fa-arrow-left"></i> </button>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-12" style="text-align: center;">
                    <h4 style="text-align: center;">REPORTE COMPARATIVO</h4>

                    <h5 style="text-align: center;"> {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>

                  </div>
                  @php $acumulador=0; $acumulador2=0; $total1=0; $total2=0; @endphp
                  <div class="col-md-12">
                    {{--<b>Plantilla:</b>
                    @php
                    //dd($planilla->planilla);
                    @endphp
                    <p>{{$planilla->planilla->nombre}}</p>--}}
                  </div>
                  <div class="col-md-12">
                    <b>Codigo: </b>
                    <p>{{$planilla->planilla->codigo}}</p>
                  </div>
                  <div class="col-md-12 ">
                    <label>Código de Transacción</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" class="form-control" style="width: 100%;" placeholder="Ingrese codigo de transacción" name="codigo" value="{{date('YmdHis')}}">
                    <input type="hidden" value="{{$planilla->id}}" name="id_planilla">
                  </div>
                  <div class="col-md-12">
                    &nbsp;
                  </div>
                  <div class="col-md-12">
                    <label> Paciente </label> <span>@if(isset($planilla->agenda->paciente)) {{$planilla->agenda->paciente->apellido1}} {{$planilla->agenda->paciente->apellido2}} {{$planilla->agenda->paciente->nombre1}} {{$planilla->agenda->paciente->nombre2}} @endif</span>
                  </div>
                  <div class="col-md-12">
                    <label> Fecha : </label> <span>@if(isset($planilla->agenda)) Inicio {{$planilla->agenda->fechaini}} - Fin {{$planilla->agenda->fechafin}} @endif </span>
                  </div>
                  <div class="col-md-12">
                    <label> Procedimiento : </label> @foreach($planilla->procedimiento->hc_procedimiento_f as $value) <span>{{$value->procedimiento->nombre}}</span> @endforeach
                  </div>
                  <div class="col-md-12">
                    &nbsp;
                  </div>

                  <div class="col-md-12">

                  </div>
                  <div class="col-md-12">


                    <div class="panel-group">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Ver Planilla</a>
                          </h4>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse">
                          <div class="panel-body">
                            <div class="col-md-12 table-responsive">
                              <table id="items" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead class="thead-dark">
                                  <tr class='well-darks'>
                                    <th width="40%" tabindex="0">Items</th>
                                    <th width="10%" tabindex="0">Total</th>

                                  </tr>
                                </thead>
                                <tbody>
                                  @if(isset($planilla->planilla))
                                  @if(isset($planilla->planilla->detalles))
                                  @foreach($planilla->planilla->detalles as $value)
                                  @if(!is_null($value))
                                  <tr class="fila-fija">
                                    <td>
                                      <input type="hidden" name="id_item[]" class="id_item" value="{{$value->id_producto}}" /><input required name="item_id[]" id="item_id" onchange="agregar_item(this)" class=" buscador_items form-control" style="height:25px;width:90%;" value="@if(isset($value->producto)){{$value->producto->nombre}}@endif" readonly name="producto[]" />
                                    </td>
                                    <!--td>
                                    <input type="number" name="item_cant[]" id="item_cant" class="form-control" style="height:25px;width:75%;" value="{{$value->cantidad}}">
                                    </td-->
                                    <td>
                                      <input type="hidden" name="item_cant[]" class="form-control" style="height:25px;width:75%;" value="{{$value->cantidad}}">
                                      <input type="number" name="orden[]" class="form-control" style="height:25px;width:75%;" readonly value="{{$value->total}}">
                                    </td>

                                  </tr>
                                  @endif
                                  @endforeach
                                  @endif
                                  @endif
                                </tbody>
                              </table>
                            </div>
                          </div>

                        </div>
                      </div>

                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-1 green">

                        </div>
                        <div class="col-md-1">
                          <b>Usado</b>
                        </div>
                        <div class="col-md-1 red">

                        </div>
                        <div class="col-md-1">
                          <b>Faltantes</b>
                        </div>
                        @if($planilla->aprobado == "0")
                        <a class="btn btn-primary" onclick="reprocesar()" >
                          Reprocesar Insumos
                        </a>
                        @endif
                      </div>
                    </div>
                    <table id="example2" class="display compact" style="font-size: 12px; width: 100%;">
                      <thead>
                        <tr>
                          <th tabindex="0" aria-controls="example2" rowspan="1">#</th>
                          <th tabindex="0" aria-controls="example2" rowspan="1">Codigo</th>
                          <th tabindex="0" aria-controls="example2" rowspan="1">Cantidad</th>
                          <th>Nombre</th>
                          <th tabindex="0" aria-controls="example2" rowspan="1">Serie</th>
                          <th tabindex="0" aria-controls="example2" colspan="1">Lote</th>
			                    <th tabindex="0" aria-controls="example2" colspan="1">Bodega</th>
                          <th tabindex="0" aria-controls="example2" colspan="1">Acccion</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                          $agroup= Sis_medico\Contable::groupBy($planilla->detalles->where('estado',1)->toArray(),'codigo');
                        @endphp
                        @foreach($planilla->detalles as $value)
                        @php //dd($planilla->detalles);
                        $producto= Sis_medico\Producto::where('codigo',$value->codigo)->first();

                        @endphp
                        @if($value->estado!=0 and $value->cantidad > 0)
                        <tr @if($value->check==1) class="green" @else class="red" @endif>
                          <td>{{$value->id}}</td>
                          <td>{{$value->codigo}} <input type="hidden" name="movimiento[]" value="{{$value->id}}"></td>
                          <td>
                            <p class="cantidad_control">{{$value->cantidad}} &nbsp; &nbsp;
                          </td>
                          <td>@if(($producto)!=null) {{$producto->nombre}} @endif</td>
                          <td>{{$value->serie}} </td>
                          <td>{{$value->lote}}</td>
                          <td>@if (isset($value->bodega)) {{$value->bodega->nombre}} @else {{$value->id_bodega}}@endif</td>

                          <td> <input type="checkbox" class="checkmovimiento" name="check[]" @if($planilla->aprobado==1) disabled @endif @if($value->check==1) checked @endif value="{{$value->id}}"></td>
                          <td>@if($planilla->aprobado == 0)<button type="button" class="btn btn-danger"   onclick="eliminar_detalle('{{$value->serie}}','{{$value->id}}')"> <i class="fa fa-trash"></i> </button>@endif</td>
                        </tr>
                        @endif
                        @endforeach
                      </tbody>

                    </table>
                    <div class="col-md-12" style="top:10px;">
                      <label>OBSERVACION</label>
                    </div>
                    <div class="col-md-12">
                      &nbsp;
                    </div>
                    <div class="col-md-12">
                      <textarea class="form-control" rows="3" name="observacion" cols="150" placeholder="Ingrese Observación">{{$planilla->observacion}}</textarea>
                    </div>
                    <div class="col-md-12">
                      &nbsp;
                    </div>
                    <div class="col-md-12">
                      <div class="panel-group">
                        <div class="panel panel-info">
                          <div class="panel-heading">
                            <h4 class="panel-title">
                              <a data-toggle="collapse" href="#collapse2">Detalle Productos</a>
                            </h4>
                          </div>
                          <div id="collapse2" class="panel-collapse collapse">
                            <div class="panel-body">
                                <table id="example3" class="display compact" style="font-size: 12px; width: 100%;">
                                  <thead>
                                    <tr>
                                      <th>Codigo</th>
                                      <th>Nombre</th>
                                      <th>Cantidad</th>
                                    </tr>
                                  </thead>
                                  <tbody>

                                  @foreach($agroup as $key=>$value)

                                    @php
                                    $producto= Sis_medico\Producto::where('codigo',$key)->first();
                                    $cantidad=0;
                                    foreach($value as $p){
                                      $cantidad+=$p['cantidad'];
                                    }
                                    @endphp
                                    <tr>
                                      <td>{{$key}}</td>
                                      <td>@if(!is_null($producto)){{$producto->nombre}} @endif</td>
                                      <td>{{$cantidad}}</td>
                                    </tr>
                                  @endforeach 
                                  </tbody>
                                </table>

                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      &nbsp;
                    </div>
                    
                    @if($planilla->aprobado==0)
                    <div class="col-md-12" style="text-align: center;">
                      <button class="btn btn-success btn-gray" type="button" onclick="saveData(this)"> <i class="fa fa-check"> </i> </button>
                    </div>
                    @endif
                  </div>


                </div>
              </div>
            </div>
    </form>
    <!-- /.box -->
  </div>
  </div>


  </div>

  </div>
</section>

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
  function saveData(e) {
    e.style.display = 'none';
    console.log(e)
    Swal.fire({
      title: 'Desea aprobar el comparativo?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Enviar',
      denyButtonText: 'No Enviar',
      showLoaderOnConfirm: true,
    }).then((result) => {

      if (result.isConfirmed) {
        $.ajax({
          type: 'post',
          url: "{{route('nota_entrega.storeAprobado')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          beforeSend: function() {
                Swal.fire({
                    //title: "{{trans('winsumos.procesando')}}",
                    text: "Cargando...",
                    imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
          },
          data: $('#post_master').serialize(),
          success: function(data) {
            Swal.close();
            console.log(data);
            if(data.status === 'error'){
              Swal.fire({
                icon: 'error',
                title: 'Error...',
                html: data.message
              });
              e.style.display = 'initial';

              return ;
            }
            //location.href = "{{route('productos.comparar.index')}}";
            Swal.fire({
              icon: 'success',
              title: `{{trans('contableM.exito')}}!!`,
              text: `{{trans('contableM.regisguarconexi')}}`
            });

          },
          error: function(data) {
            Swal.close();
            console.log(data);
          }
        });

      }else{
        e.style.display = 'initial';
      }
    });
  }

  function eliminar_detalle(e, id) {
    Swal.fire({
      title: 'Desea eliminar el producto con serie: ' + e + ' ?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Enviar',
      denyButtonText: 'No Enviar',
      showLoaderOnConfirm: true,
    }).then((result) => {

      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{route('productos.anular_detalle')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            'serie': e,
            'id': id
          },
          success: function(data) {
            console.log(data);

            if(data.status == 'success') {
              setTimeout(function() {
                Swal.fire({icon: 'success',title: `{{trans('contableM.exito')}}!!`,text: `{{trans('contableM.regisguarconexi')}}`});
                location.reload(true)
              }, 1500)
            }else{
              Swal.fire({icon: 'error',title: `Error...`,text: data.msj});

            }

            // ;

          },
          error: function(data) {
            console.log(data);
          }
        });

      }
    });
  }
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });

  });

  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': true,
    'ordering': false,
    responsive: true,
    'info': false,
    'autoWidth': true,


  })
  $('#example3').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': true,
    'ordering': false,
    responsive: true,
    'info': false,
    'autoWidth': true,


  })

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

  function editar(e) {
    $(e).parent().parent().find('.cantidad_control').addClass('oculto');
    $(e).parent().parent().find('.as').show();
  }

  function editar2(e) {
    $(e).parent().parent().find('.cantidad_control').removeClass('oculto');
    $(e).parent().parent().find('.as').hide();
  }
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
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit();
  });
  $(".checkmovimiento").click(function() {
    var check = 0;
    var valor = $(this).val();
    //alert('aqui');
    console.log('aqui');
    if ($(this).prop('checked')) {
      //alert('checkeado')
      //$(this).val(1);
      check = 1;
      console.log($(this).parent().parent().html())
      $(this).parent().parent().removeClass('red');
      $(this).parent().parent().addClass('green');

    } else {
      //alert('sin check');
      //$(this).val(0);
      console.log($(this).parent().parent().find('tr'))
      $(this).parent().parent().removeClass('green');
      $(this).parent().parent().addClass('red');
    }

    $.ajax({
      type: 'post',
      url: "{{route('productos.storecheck')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'check': check,
        'id': valor
      },
      success: function(data) {
        console.log(data);

      },
      error: function(data) {
        console.log(data);
      }
    });
    // document.getElementById("print_reporte_master").submit();
  });
  $(document).ready(function() {

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });

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

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#id_proveedor2').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#id_proveedor2').val("");
          $('#direccion_proveedor').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

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

  function printDiv(nombreDiv) {
    var contenido = document.getElementById("imprimir").innerHTML;
    var contenidoOriginal = document.body.innerHTML;

    document.body.innerHTML = contenido;

    window.print();

    document.body.innerHTML = contenidoOriginal;
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function tipo_factura() {
    var tipo = $("#tipo").val();
    if (isNaN(tipo)) {
      tipo = 0;
    }
    $("#tipo2").val(tipo);
  }

  function observacion() {
    var observacion = $("#concepto").val();

    $("#observacion2").val(observacion);
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }

  function reprocesar(){
    Swal.fire({
      title: 'Esta Seguro de Reprocesar?',
      text:  'Al Reprocesar los productos solo cogeremos lo que esten en STOCK, y que tengan seleccionado Codigo Siempre en Producto',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      denyButtonText: 'No Enviar',
      showLoaderOnConfirm: true,
    }).then((result) => {

      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{route('producto.reprocesar_serie', ['id' => $planilla->id])}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          success: function(data) {
            if(data.estado == "error"){
              if(data.respuesta == 'Faltan Datos'){
                mostrar = "";
                for(var k in data.valores) {
                  mostrar = mostrar+" <br> <b>Producto:</b> "+data.valores[k].producto+"  <span class='label label-danger'> <b >Error:</b> "+data.valores[k].error+' </span> ';

                  //console.log(k, data.valores[k]);
                }
                Swal.fire({
                  icon: 'error',
                  title: data.respuesta,
                  html: mostrar
                });
              }else{
                Swal.fire({
                  icon: 'error',
                  title: data.estado,
                  text: data.respuesta
                });
              }
              
            }else{
              console.log(data);
              Swal.fire({
                icon: 'success',
                title: 'Éxito!!',
                text: 'Registro guardado con éxito'
              });
              location.reload(true);
            }
            
          },
          error: function(data) {
            Swal.fire({
              icon: 'error',
              title: 'Error al reprocesar',
              text: 'Por favor comunicarse con Soporte!'
            });
            console.log(data);
          }
        });

      }
    });
  }
</script>
@endsection
