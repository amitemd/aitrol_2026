@extends('contable.retenciones.base')
@section('action-content')
<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
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
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Retenciones de Clientes</a></li>
      <li class="breadcrumb-item active" aria-current="page">Registro de Retenciones</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">Comprobante de Retenciones</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('retenciones.clientes2')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Retenciones
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE RETENCIONES</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('retenciones.clientes.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">Id: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['ct_cliente_retencion.id']}}@endif" placeholder="Ingrese Id..." />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="nombre_proveedor">Cliente: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control select2_cuentas" style="width: 100%;" name="id_cliente" id="id_cliente">
              <option value="">Seleccione...</option>
             
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">Detalle: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($searchingVals)){{$searchingVals['ct_cliente_retencion.descripcion']}}@endif" placeholder="Ingrese detalle..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="nro_comprobaante">Nro Factura: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nro_comprobaante" name="nro_comprobante" value="@if(isset($searchingVals)){{$searchingVals['v.nro_comprobante']}}@endif" placeholder="Ingrese # comprobante..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia_f">Secuencia: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['ct_cliente_retencion.secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['ct_cliente_retencion.fecha']}}@endif">
          </div>
        </div>
<div class="container-fluid"></div>

        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id_usuariocrea">Creo la factura: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control select3_cuentas" style="width: 100%;" name="fac_crea" id="fac_crea">
            <option value="">Seleccione...</option>
       
        </select>
          <!--<input class="form-control" type="text" id="fac_crea1" name="fac_crea1" value="" placeholder="Ingrese quien creo la factura..." />-->
        </div>
        <div class="col-md-offset-11 col-xs-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO DE RETENCIONES</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th> Descripción</th>
                        <th>Nro comprobante</th>
                        <th>Secuencia Factura</th>
                        <th>Total RFIR</th>
                        <th>Total RFIVA</th>
                        <th>Total</th>
                        <th>Creado por</th>
                        <th>Estado</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tbody style="background-color: white;">
                      @foreach($retenciones as $value)
                      
                      @php
                        //dd($value);
                        $usuario="";
                        $user = Sis_medico\User::where('id', $value->id_usuariocrea)->first();
                        //dd($user);
                        if(isset($value->id_usuariocrea)){
                          $usuario = $user->nombre1 . " " . $user->apellido1;
                        }
                      @endphp
                      <tr>
                        <td>{{$value->id}}</td>
                        <td>{{date('d-m-Y',strtotime($value->fecha))}}</td>
                        <td>{{$value->nombre}}</td>
                        <td>{{$value->descripcion}}</td>
                        <td>{{$value->nro_comprobante}}</td>
                        <td>@if(isset($value->ventas)){{$value->ventas->nro_comprobante}}@else {{$value->nro_comprobante}} @endif</td>
                        <td>{{$value->valor_fuente}}</td>
                        <td>{{$value->valor_iva}}</td>
                        <td>@php $total= $value->valor_fuente+$value->valor_iva; @endphp {{$total}}</td>
                        <td>{{$usuario}}</td>
                        <td>@if(($value->estado)==1) ACTIVO @elseif(($value->estado)==0) INACTIVO @endif</td>
                        <td>
                           <a class="btn btn-danger btn-gray" target="_blank" href="{{route('pdf.comprobante.retenciones.clientes',['id'=>$value->id])}}"> <i class="fa fa-file-pdf-o"></i> </a>
                          @if(($value->estado)== 1 )
                           <a style="width: 10px;" class="btn btn-danger btn-gray" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                          @endif
                           <a class="btn btn-danger btn-gray" href="{{route('retenciones.clientes.edit',['id'=>$value->id])}}"><i class="fa fa-eye"></i></a> 
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($retenciones->currentPage() - 1) * $retenciones->perPage())}} / {{count($retenciones) + (($retenciones->currentPage() - 1) * $retenciones->perPage())}} de {{$retenciones->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $retenciones->appends(Request::only(['id', 'id_cliente', 'detalle','secuencia_f','fecha','nro_comprobante','fac_crea']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  let CONTROLLLER = "{{route('retenciones.autcom_fc')}}";
  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });

    $('.select3_cuentas').select2({
                placeholder: "Escriba el nombre del Usuario",
                 allowClear: true,
                ajax: {
                    url: '{{route("retencion.buscaridUser")}}',
                    data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                    },
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        console.log(data);
                        return {
                            results: data
                        };
                    }
                }
            });

    $('.select2_cuentas').select2({
                placeholder: "Escriba el nombre del cliente",
                 allowClear: true,
                ajax: {
                    url: '{{route("venta.clientesearch")}}',
                    data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                    },
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        console.log(data);
                        return {
                            results: data
                        };
                    }
                }
            });

           

  });

  function prueba() {
    $.ajax({
      type: 'post',
      url: "{{route('retenciones.autcom_fc')}}",
      data: '',
      success: function(respuesta) {


      }
    });
  }
  //metodo post retenciones.buscar_proveedor 
  function buscar_proveedor() {
    var proveedor = $("#nombre_proveedor").val();
    if (proveedor != "") {
      $.ajax({
        type: 'get',
        url: "{{route('retenciones.buscar_proveedor')}}",
        datatype: 'html',
        data: $("#buscador_form").serialize(),
        success: function(datahtml) {
          //console.log(datahtml);
          $("#resultados").html(datahtml);
          //alert("dsada");
          $("#resultados").show();
          $("#contenedor").hide();

        },
        error: function(data) {
          console.log(data);

        }
      });
    } else {
      $("#resultados").hide();
      $("#contenedor").show();

    }
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
          //console.log(data)
          response(data);
        }
      });
    },
    minLength: 1,
  });

  function secuencia_factura() {

  }

  $("#buscarnombre").autocomplete({
    source: function(request, response) {
      $.ajax({
        method: 'GET',
        url: "{{route('retenciones.autocompletar.cliente')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 1,
    change: function(event, ui) {}
  });

  $('.select2').select2({
    tags: false
  });
  $('.select3').select2({
    tags: false
  });

  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta factura?',
      text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";

        $.ajax({
          type: 'get',
          url: "{{ route('ventas.verificar')}}",
          datatype: 'json',
          data: {
            'verificacion': '3',
            'id_venta': id
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            console.log(data);
              console.log("entra aqui" + id);

              if(data.respuesta == "si"){
                //alert("hola");
                let enlace = `<a target="_blank" href="{{url('contable/comprobante/ingreso/buscar?id=${data.id}')}}"><b>${data.modulo}</b></a>`;
                let mensaje = `Existe ${enlace} generado con esta factura`;
                Swal.fire({
                  icon: 'error',
                  title: 'Error...',
                  html: mensaje,
                })

              }else{
                test(id);
              }

              
            
          },
          error: function(data) {
            console.log(data);
          }
        });

      }
    })
  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/client/retenciones/anular_factura/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text,
          },
          success: function(data) {
            if(data == "ok"){
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            location.href = "{{route('retencion.cliente')}}";
            }
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>

@endsection