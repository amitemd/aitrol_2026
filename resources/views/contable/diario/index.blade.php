@extends('contable.diario.base')
@section('action-content')
<!-- Ventana modal editar -->
<style type="text/css">
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



  /* .cabecera{
            background-color: #3c8dbc;
            border-radius: 8px;
        } */
  .color_texto {
    color: #fff;
  }
</style>
@php
$tipo_rol = Auth::user()->id_tipo_usuario;
@endphp
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">
  <div class="box" style=" background-color: white;">
    @if($tipo_rol != '7')
    <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
      <h3 class="box-title">Listado de Asientos del Libro Diario</h3>
      <button type="button" onclick="location.href='{{route('librodiario.crear')}}'" class="btn btn-sm btn-success btn-gray pull-right">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Asiento Manualmente
      </button>
      <button type="button" onclick="location.href='{{route('librodiario.cierre')}}'" style="margin-right: 10px;" class="btn btn-sm btn-success btn-gray pull-right">
        <i class="fa fa-tree"></i> Cierre de año
      </button>
      <a type="button" href="{{route('librodiario.problemasAsientos')}}" style="margin-right: 10px;" class="btn btn-sm btn-danger pull-right">
        Problema Asiento
      </a>
      @if(Auth::user()->id_tipo_usuario = 1)
      <a type="button" href="{{route('librodiario.planConfiguraciones')}}" style="margin-right: 10px;" class="btn btn-sm btn-success pull-right">
        Config Cuentas
      </a>
      @endif
    </div>
    @endif
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('librodiario.index') }}">
        {{ csrf_field() }}
        <div class="panel panel-default col-md-12">
          <div class="panel-heading">
            <label>{{trans('contableM.Buscadores')}}</label>
          </div>
          <div class="panel-body">
            <div class="form-row">
              <div class="form-group row col-md-4">
                <label for="buscar_plan_cuenta" class="col-sm-6 col-form-label">No asiento </label>
                <div class="col-md-6 container-4">
                  <input class="form-control " type="text" id="id" name="id" value="@if(isset($request->id)) {{$request->id}} @endif" placeholder="Buscar por numero de asiento..." />
                </div>
              </div>
              <div class="form-group row col-md-4">
                <label for="d" class="col-sm-6 col-form-label">{{trans('contableM.detalle')}}</label>
                <div class="col-md-6 container-4">
                  <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($request->detalle)) {{$request->detalle}} @endif" placeholder="Ingrese el detalle..." />
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group row col-md-4">
                <label for="buscar_factura" class="col-sm-6 col-form-label">{{trans('contableM.Numerodefactura')}}</label>
                <div class="col-md-6 container-4">
                  <input class="form-control " type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($request->secuencia_f)) {{$request->secuencia_f}} @endif" placeholder="Buscar por número factura..." />
                </div>
              </div>
              <div class="form-group row col-md-4">
                <label for="buscar_secueencia" class="col-sm-6 col-form-label">Creo asiento: </label>
                <div class="col-md-6 container-4">
                  <input class="form-control " type="text" id="id_usuariocrea" name="id_usuariocrea" value="@if(isset($request->id_usuariocrea)) {{$request->id_usuariocrea}} @endif" placeholder="Ingrese CI..." />
                </div>
              </div>
              <div class="form-group row col-md-4">
                <label for="fecha" class="col-sm-6 col-form-label">{{trans('contableM.fecha')}}: </label>
                <div class="col-md-6 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($request->fecha)) {{$request->fecha}} @endif">
                  </div>
                </div>
              </div>
              <div class="form-group row col-md-4">
                <label for="fecha" class="col-sm-6 col-form-label">Fecha Hasta: </label>
                <div class="col-md-6 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="fecha_hasta" class="form-control fecha" id="fecha_hasta" value="@if(isset($request->fecha_hasta)) {{$request->fecha_hasta}} @endif">
                  </div>
                </div>
              </div>
              <div class="form-group row col-md-4">
                <label for="fecha" class="col-sm-6 col-form-label">Aparece SRI: </label>
                <div class="col-md-6 container-4">
                  <select name="sri" class="form-control">
                    <option selected="selected" value="">Seleccione...</option>
                    <option @if(isset($request->sri)) @if($request->sri==1) selected="selected" @endif @endif value="1">Si</option>
                    <option @if(isset($request->sri)) @if($request->sri==0) selected="selected" @endif @endif value="0">No</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group row col-md-6">
                <button style="margin-left: 14px;" type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
      <div class="form-group col-md-12 cabecera">
        <label class="color_texto" for="title_table">ASIENTOS DIARIOS</label>
      </div>
      <div class="form-group col-md-12">
        <div class="table table-responsive">
          <table id="examples3" class="display compact cell-border responsive nowrap">
            <thead>
              <tr>
                <th>N°Asiento</th>
                <th>{{trans('contableM.fecha')}}</th>
                <th>Detalle</th>
                <th>{{trans('contableM.valor')}}</th>
                <th>Información</th>
                <th>{{trans('contableM.accion')}}</th>
              </tr>
            </thead>
            <tbody>
              @php
              $total=0
              @endphp
              @foreach ($registros as $value)
              @php
              $total+=$value->valor;
              @endphp
              <tr>
                <td>{{ $value->id }}</td>
                <td>{{ date('d/m/Y',strtotime($value->fecha_asiento)) }}</td>
                <td>{{ $value->observacion }}</td>
                <td style="text-align: right;">{{ $value->valor }}</td>
                <td style="text-align:center;">
                  @php
                  $log = Sis_medico\Log_Contable::where ('id_ant', $value->id) -> orWhere ('id_referencia', $value->id)->first();
                  @endphp
                  <label class='label label-success'>ACTIVO</label><br>
                  @if(!is_null($log))
                  @if($log->id_ant == $value->id)
                  @php
                  $total-=$value->valor;
                  @endphp
                  <label class='label label-danger'>ANULADO Ref: {{$log->id_referencia}}</label>
                  @elseif($log->id_referencia == $value->id)
                  <label class='label label-info'>Ref: {{$log->id_ant}}</label>
                  @endif
                  @endif
                </td>
                <td>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <a href="{{ route('librodiario.revisar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                  </a>
                  @if($tipo_rol != '7')
                  @if($value->tipo==1)
                  <a class="btn btn-danger btn-gray" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                  @endif
                  <a href="{{ route('librodiario.edit', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                    <i class="fa fa-edit" aria-hidden="true"></i>
                  </a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td style="text-align: right;">{{$total}}</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
  <div id="response" style="display: none;">
    <img src="https://i.imgur.com/4yT15sl.gif" alt="this slowpoke moves" width="250" style=" position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);" />
  </div>
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{trans('contableM.detalle')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <span id="modulo"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });
  $(document).ready(function() {
    $('#examples3_wrapper').removeClass('dataTables_wrapper');
  });
  $('#examples3').DataTable({
    'paging': true,
    dom: 'Bfrtip',
    'lengthChange': true,
    'searching': true,
    'ordering': false,
    'info': true,
    language: {
      "decimal": "",
      "emptyTable": "No hay información",
      "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
      "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
      "infoFiltered": "(Filtrado de _MAX_ total entradas)",
      "infoPostFix": "",
      "thousands": ",",
      "lengthMenu": "Mostrar _MENU_ Entradas",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "search": "Buscar:",
      "zeroRecords": "Sin resultados encontrados",
      "paginate": {
        "first": "Primero",
        "last": "Ultimo",
        "next": "Siguiente",
        "previous": "Anterior"
      }
    },
    'autoWidth': true,
    buttons: [{
        extend: 'copyHtml5',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}'
      },
      {
        extend: 'csvHtml5',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        pageSize: 'LEGAL',
        footer: true,
        title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}',
        customize: function(doc) {
          doc.styles.title = {
            color: 'black',
            fontSize: '17',
            alignment: 'center'
          }
        }
      }
    ],
  });
  function autocompletarceros() {
    var variable = $("#buscar_secuencia").val();
    var secuencia = 0;
    if ((variable.length) >= 1) {
      var longitud = parseInt(variable.length);
      if (longitud > 10) {
        alert('Máximo 10 caracteres');
        $("#buscar_secuencia").val('');
      } else {
        switch (longitud) {

          case 1:
            secuencia = '000000000';
            break;
          case 2:
            secuencia = '00000000';
            break;
          case 3:
            secuencia = '0000000';
            break;
          case 4:
            secuencia = '000000';
            break;
          case 5:
            secuencia = '00000';
            break;
          case 6:
            secuencia = '0000';
            break;
          case 7:
            secuencia = '000';
            break;
          case 8:
            secuencia = '00';
            break;
          case 9:
            secuencia = '0';
        }
        $('#buscar_secuencia').val(secuencia + variable);
        obtener_tabla()

      }


    } else {
      //aqui devuelvo los originales
      $("#resultados").hide();
      $("#contenedor").show();

    }
  }
  function obtener_tabla() {
    var valor = $("#buscar_secuencia").val();
    var fechaini = $("#fecha").val();
    $("#nombre_proveedor").val('');
    var fecha_hasta = $("#fecha_hasta").val();
    $.ajax({
      type: 'get',
      url: "{{route('librodiario.buscador')}}",
      datatype: 'html',
      data: {
        'nombre': valor,
        'fechaini': fechaini,
        'fecha_hasta': fecha_hasta
      },
      success: function(datahtml) {
        $("#resultados").html(datahtml);
        //alert("dsada");
        $("#resultados").show();
        $("#contenedor").hide();
      },
      error: function(data) {
        console.log(data);

      }
    })
  }
  function anular(id) {
    let array = {
      'DEBITOAC-A': "{{url('contable/acreedores/nota/debito')}}",
      'COMPRAS': "{{url('contable/compras')}}"
    };
    Swal.fire({
      title: '¿Desea anular este Asiento?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{route('librodiario.revisar_modulo')}}",
          datatype: 'json',
          data: {
            'id': id,
          },
          beforeSend: function() {
            $('#response').show()
          },
          success: function(data) {
            console.log(data);
            $('#response').hide();
            if (data.data != '') {
              $("#exampleModal").modal("show");
              const selectedUsers = [];
              selectedUsers.push(data.data.module);
              const filteredUsers = Object.keys(array)
                .filter(key => selectedUsers.includes(key))
                .reduce((obj, key) => {
                  obj[key] = array[key];
                  return obj;
                }, {});
              const el = document.createElement('a');
              el.innerHTML = `${Object.values(filteredUsers)}`;
              el.href = `${Object.values(filteredUsers)}`;
              console.log(data.data.module);
              let text = '';
              if (el === null) {
                text = data.data.module;
              }
              Swal.fire({
                icon: 'error',
                title: 'Error...',
                text: `El modulo se encuentra registrado en ${data.data.module}!`,
                html: `${text}`,
              })
            } else {
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
        title: 'Ingresa tu contraseña',
        input: 'password',
        inputPlaceholder: 'Ingrese contraseña',
        inputAttributes: {
          maxlength: 18,
          autocapitalize: 'off',
          autocorrect: 'off'
        },
        showCancelButton: true
      })
      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ route('librodiario.checkpass')}}",
          datatype: 'json',
          data: {
            'userpass': text,
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            console.log(data);
            //console.log(acumulate);
            if (data == 'ok') {
              comentario_anulacion(id);
            } else {
              Swal.fire("Mensaje", "Error contraseña incorrecta, intente de nuevo...", "error");
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
  async function comentario_anulacion(id) {
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
        //alert(text);
        $.ajax({
          type: 'get',
          url: "{{ url('contable/contabilidad/libro/diario/anular/asiento/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.reload();
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


  function obtener_con_fecha() {

    var fechaini = $("#fecha").val();
    $("#nombre_proveedor").val('');
    var fecha_hasta = $("#fecha_hasta").val();
    $.ajax({
      type: 'get',
      url: "{{route('librodiario.buscador_fecha')}}",
      datatype: 'html',
      data: {
        'fechaini': fechaini,
        'fecha_hasta': fecha_hasta
      },
      success: function(datahtml) {
        $("#resultados").html(datahtml);
        //alert("dsada");
        $("#resultados").show();
        $("#contenedor").hide();
      },
      error: function(data) {
        console.log(data);

      }
    })
  }

  function buscar_proveedor() {
    var nombre_proveedor = $("#nombre_proveedor").val();
    $("#buscar_secuencia").val('');
    if ((nombre_proveedor) != '') {
      $.ajax({
        type: 'get',
        url: "{{route('librodiario.buscar_proveedor')}}",
        datatype: 'html',
        data: $("#reporte_master").serialize(),
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
  $("#empresa").autocomplete({
    source: function(request, response) {
      $.ajax({
        method: 'GET',
        url: "{{route('librodiario.buscar_empresa')}}",
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

  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $('#fecha_hasta').datetimepicker({
    format: 'YYYY-MM-DD',
  });
</script>
@endsection