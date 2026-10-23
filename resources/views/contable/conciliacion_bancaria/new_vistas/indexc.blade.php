@extends('contable.debito_bancario.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.banco')}}</a></li>
      <li class="breadcrumb-item active">{{trans('Kconciliacion.ConciliacionBancaria')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">{{trans('Kconciliacion.ConciliarCuentaBancaria')}}</h3>
      </div>
      <div class="col-md-1 text-right">

      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('Kconciliacion.Buscador')}} {{trans('Kconciliacion.ConciliacionBancaria')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('conciliacion.index_conciliacion') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('Kconciliacion.caja')}}/{{trans('Kconciliacion.banco')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control" name="id_banco" id="id_banco">
            <option value="">{{trans('Kconciliacion.todos')}}</option>
            @foreach($bancos as $value)
            <option @if($value->id == $banco) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('Kconciliacion.Periodo')}} </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input type="month" id="fecha_periodo" name="fecha_periodo" placeholder="aaaa-mm" class="form-control" value="{{$fecha}}">
        </div>
        <div class="col-md-2">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>{{trans('Kconciliacion.Buscar')}}
          </button>
        </div>
        <div class="col-md-2">
          <button id="btn_guardar" {{--@if(!is_null($consulta_mes)) disabled @endif --}} class="btn btn-success" onclick="guardar_mes(event);">{{trans('Kconciliacion.Guardar')}}</button>
        </div>
    </div>
    </form>
    <br>
  </div>
  <div class="row head-title">
    <div class="col-md-12 cabecera">
      <label class="color_texto">{{trans('Kconciliacion.ConciliacionBancaria')}}</label>
    </div>
  </div>
  <div class="box-body dobra">
    <div class="form-group col-md-12">
      <div class="form-row">
        <div class="col-md-12">
          <div class="col-md-6">
            <label>{{trans('Kconciliacion.SaldoLibros')}} </label>
          </div>
          <table width="100%">
            <thead>
              <tr class="well-dark">
                <th style="text-align: center;">{{trans('Kconciliacion.SaldoAnterior')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.Depositos')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.ValorAcreditado')}}</th>
                <th style="text-align: center;">Cheques Girados</th>
                <th style="text-align: center;">{{trans('Kconciliacion.ValoresDebitados')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.SaldoActual')}}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <input type="hidden" name="tipo_mes" id="tipo_mes" value="1">
                <td><input id="saldo_ant" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['saldo_anterior'], 2, '.', '') }}" name="saldo_anterior" readonly></td>
                <td><input id="depositos" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['depositos'], 2, '.', '') }}" name="depositos" readonly></td>
                <td><input id="valor_acreditado" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['valor_acreditado'], 2, '.', '') }}" name="valor_acreditado" readonly></td>
                <td><input id="cheques_pag" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['cheques_girados'], 2, '.', '') }}" name="cheques_pag" readonly></td>
                <td><input id="valor_debitado" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['valores_debitados'], 2, '.', '') }}" name="valor_debitado" readonly></td>
                <td><input id="saldo_act" style="text-align: right; width: 100%" type="text" class="form-control" value="{{ number_format($tblsaldo_lib['saldo_actual'], 2, '.', '') }}" name="saldo_actual" readonly></td>
              </tr>
            </tbody>
          </table>
        </div>
        <br>
        <div class="col-md-12">
          <div class="col-md-6">
            <label>{{trans('Kconciliacion.SaldoBanco')}} </label>
          </div>
          <table width="100%">
            <thead>
              <tr class="well-dark">
                <th style="text-align: center;">{{trans('Kconciliacion.SaldoAnterior')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.Depositos')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.ValorAcreditado')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.ChequesPagados')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.ValoresDebitados')}}</th>
                <th style="text-align: center;">{{trans('Kconciliacion.SaldoActual')}}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <input type="hidden" name="tipo" id="tipo" value="2">
                <td><input id="saldo_ant" style="text-align: right; width: 100%" type="text" class="form-control" value="{{number_format($salBa, 2, '.', '')}}" name="saldo_anterior" readonly></td>
                <td><input id="depositos" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="depositos" readonly></td>
                <td><input id="valor_acreditado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_acreditado" readonly></td>
                <td><input id="cheques_pag" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="cheques_pag" readonly></td>
                <td><input id="valor_debitado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_debitado" readonly></td>
                <td><input id="saldo_act" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="saldo_actual" readonly></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="resultados">
        </div>
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <form id="tabla_conciliacion">
              {{ csrf_field() }}
              <div class="row">
                {{--@php
                $anio = date('Y', strtotime($fecha_desde));
                $mes = date('m', strtotime($fecha_hasta));
                $consulta_mes = Sis_medico\Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('id_caja_banco', $banco)->where('tipo', 1)->first();
                @endphp--}}
              </div>
              <br>

              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tbl_saldo_libro" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th width="5%">&nbsp;</th>
                        <th width="10%">{{trans('Kconciliacion.Fecha')}}</th>
                        <th width="10%">{{trans('Kconciliacion.tipo')}}</th>
                        <th width="10%">{{trans('contableM.Idasiento')}}</th>
                        <th width="55%">{{trans('Kconciliacion.Detalle')}}</th>
                        <th width="10%">{{trans('Kconciliacion.Valor')}}</th>
                      </tr>
                    </thead>
                    <tbody id="tbl_detalles" name="tbl_detalles">
                      @php $tipo_debito = array('BAN-ND'); @endphp
                      @foreach ($registros as $value)
                      <tr class="well">
                        <td><input type="checkbox" id="id_consiliacion_{{ $value['id_consiliacion'] }}" class="form-check-input" name="id_consiliacion_{{ $value['id_consiliacion'] }}" onchange="actualizar(this, `{{$value['valor']}}`, `{{$value['detalle']}}`);select_check(`{{$value['id_consiliacion']}}`);" value="{{ $value['id_consiliacion'] }}" @if(@$value['conciliado']=="1" ) checked @endif>
                          <input type="hidden" name="check_conc[]" id="check_conc{{ $value['id_consiliacion'] }}" @if(@$value['conciliado']=="1" ) value="1" @else value="0" @endif>
                          <input type="hidden" name="id_conc[]" id="id_conc{{ $value['id_consiliacion'] }}" value="{{$value['id_consiliacion']}}">
                        </td>
                        <td> @if(!is_null($value['fecha'])) {{ date('d/m/Y',strtotime($value['fecha'])) }} @endif
                          <input type="hidden" name="fecha_b[]" id="fecha_b{{ $value['id_consiliacion'] }}" value="{{$value['fecha']}}">
                          <input type="hidden" name="banco" id="banco" value="{{$banco}}">
                        </td>
                        <td> @if(!is_null($value['tipo'])) {{ $value['tipo'] }} @endif
                          <input type="hidden" name="tip[]" class="form-control tipoc " id="tip{{ $value['id_consiliacion'] }}" value="@if(isset($value['tipo'])){{$value['tipo']}}@endif">
                        </td>
                        <td>
                          <a href="{{ url('contable/contabilidad/libro/edit') }}/{{$value['id_asiento']}}" target="_blank">@if(!is_null($value['id_asiento'])) {{$value['id_asiento']}} @endif</a>
                          <input type="hidden" name="id_asiento[]" class="form-control" id="id_asiento{{ $value['id_consiliacion'] }}" value="@if(isset($value['id_asiento'])){{$value['id_asiento']}}@endif">
                        </td>
                        <td>{{ $value['detalle'] }}
                          <input type="hidden" name="det[]" id="det{{ $value['id_consiliacion'] }}" value="{{$value['detalle']}}">
                        </td>
                        <td>$ {{ $value['valor'] }}
                          <input type="hidden" name="val" class="form-control valorc {{$value['tipo']}}" id="val" value="@if(isset($value['valor'])){{$value['valor']}}@endif">
                          <input type="hidden" name="valor_con[]" id="valor_con{{$value['id_consiliacion'] }}" value="{{$value['valor']}}">
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="table-responsive col-md-12">
                  <div class="row" style="background: indianred;">
                    <div class="col-md-12 cabecera">
                      <label class="color_texto" for="title">{{trans('Kconciliacion.PendientesdeConciliar')}}</label>
                    </div>
                  </div>
                  <table id="tbl_pendientes" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th>&nbsp;</th>
                        <th>{{trans('Kconciliacion.Fecha')}}</th>
                        <th>{{trans('Kconciliacion.tipo')}}</th>
                        <th>{{trans('contableM.Idasiento')}}</th>
                        <th>{{trans('Kconciliacion.Detalle')}}</th>
                        <th>{{trans('Kconciliacion.Valor')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($pendientes as $pend)
                      <tr>
                        <td>
                          <input type="checkbox" id="id_consiliacion_{{ $pend['id_concilia'] }}" class="form-check-input" name="id_consiliacion_{{ $pend['id_concilia'] }}" onchange="actualizar(this, `{{$pend['valor']}}`, `{{$pend['detalle']}}`);update_pendiente(`{{$pend['id']}}`);" value="{{ $pend['id_concilia'] }}" @if(@$pend['conciliado']=="1" ) checked @endif>
                          <input type="hidden" name="id_concp[]" id="id_concp{{ $pend['id_concilia'] }}" value="{{$pend['id_consiliacion']}}">
                        </td>
                        <td>{{$pend->fecha}}
                          <input type="hidden" name="fecha_bp[]" id="fecha_bp{{ $pend['id_concilia'] }}" value="{{$pend['fecha']}}">
                        </td>
                        <td>{{$pend->tipo}}
                          <input type="hidden" name="tipp[]" class="form-control tipoc " id="tipp{{ $pend['id_concilia'] }}" value="@if(isset($pend['tipo'])){{$pend['tipo']}}@endif">
                        </td>
                        <td>
                          {{$pend->id_asiento}}
                          <input type="hidden" name="id_asientop[]" class="form-control " id="id_asientop{{ $pend['id_concilia'] }}" value="@if(isset($pend['idasiento'])){{$pend['idasiento']}}@endif">
                        </td>
                        <td>{{$pend->detalle}}
                          <input type="hidden" name="detp[]" id="detp{{ $pend['id_concilia'] }}" value="{{$pend['detalle']}}">
                        </td>
                        <td>{{$pend->valor}}
                          <input type="hidden" name="valor_conp[]" id="valor_conp{{$pend['id_concilia'] }}" value="{{$pend['valor']}}">
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

            </form>
          </div>
        </div>

        <br><br>
      </div>
    </div>
  </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#tbl_pendientes').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
    });
    //calcular_mov();
  });

  $('#tbl_saldo_libro').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': false,
  });

  function validar_campos() {
    let campo = document.querySelectorAll(".validar")
    let validar = false;
    for (let i = 0; i < campo.length; i++) {
      if (campo[i].value.trim() <= 0) {
        campo[i].style.border = '2px solid #CD6155';
        campo[i].style.borderRadius = '4px';
        validar = true;
      } else {
        campo[i].style.border = '1px solid #d2d6de';
        campo[i].style.borderRadius = '0px';
      }
    }
    return validar;
  }

  function alertas(icon, title, msj) {
    Swal.fire({
      icon: icon,
      title: title,
      html: msj
    })
  }

  function actualizar(obj, valor, detalle) {
    var fecha_concilia = $('#fecha_conciliacion').val();
    var tipo = $('#tipo_mes').val();
    var fecha_hasta = $('#fecha_hasta').val();
    var banco = $('#banco').val();
    var error = '';
    if (banco == '') {
      error = error + `Seleccione un banco`;
    }
    if (error == '') {
      $.ajax({
        type: 'get',
        url: "{{route('conciliacionbancaria.actualizar')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
          'id': obj.value,
          'fecha': fecha_concilia,
          'valor': valor,
          'detalle': detalle,
          'tipo': tipo,
          'fecha_hasta': fecha_hasta,
          'banco': banco,
        },
        success: function(data) {
          console.log(data.msj);
          if (data.respuesta == 'success') {

          } else {
            alertas('error', data.titulos, data.msj);
            document.getElementById("id_consiliacion_" + obj.value).checked = false;
          }
        },
        error: function(data) {}
      })
    } else {
      alertas('error', 'error', error);
      document.getElementById("id_consiliacion_" + obj.value).checked = false;
    }
  }







  function update_pendiente(id) {
    if (!validar_campos()) {
      $.ajax({
        type: 'get',
        url: "{{url('contable/conciliacion/update_pendiente')}}/" + id,
        success: function(data) {
          setTimeout(function() {
            location.reload();
          }, 1500)
        },
        error: function(data) {

        }
      })
    }
  }

  function _actualizar(obj) {
    console.log(obj.value)
    $.post("{{route('conciliacionbancaria.actualizar')}}", {
      id: obj.value,
      _token: "{{ csrf_token() }}"
    });
    calcular_todo();
  }

  function actualizarmasivo(obj, accion = "") {
    $.post("{{route('conciliacionbancaria.actualizarmasivo')}}", {
      id: obj.value,
      accion: accion,
      _token: "{{ csrf_token() }}"
    });
  }

  function seleccionar_todo(checked) {
    var miTabla = document.getElementById('tbl_detalles');
    var acumdebito = 0;
    var acumcerdito = 0;
    for (i = 0; i < miTabla.rows.length; i++) {
      miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
      actualizarmasivo(miTabla.rows[i].getElementsByTagName("input")[0], checked);
      if (miTabla.rows[i].getElementsByTagName("input")[0].checked == true) {
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-NC") {
          acumcerdito = parseFloat(acumcerdito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND-AC") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-TR") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
      }
    }
    $('#total_debito').val(redondeafinal(acumdebito, 4));
    $('#total_credito').val(redondeafinal(acumcerdito, 4));
    $('#saldo_final').val(redondeafinal(parseFloat($('#total_credito').val()) - parseFloat($('#total_debito').val()), 4));

  }

  function calcular_todo() {
    var miTabla = document.getElementById('tbl_detalles');
    var acumdebito = 0;
    var acumcerdito = 0;
    for (i = 0; i < miTabla.rows.length; i++) {
      if (miTabla.rows[i].getElementsByTagName("input")[0].checked == true) {
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-NC") {
          acumcerdito = parseFloat(acumcerdito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND-AC") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-TR") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
        }
      }
    }
    $('#total_debito').val(redondeafinal(acumdebito, 4));
    $('#total_credito').val(redondeafinal(acumcerdito, 4));
    $('#saldo_final').val(redondeafinal(parseFloat($('#total_credito').val()) - parseFloat($('#total_debito').val()), 4));
  }

  function calcular_mov() {
    let banco = $('#banco').val();
    if (banco != '') {
      let eg = document.querySelectorAll(".EG");
      let totalEG = sumaTotal(eg);
      let egv = document.querySelectorAll(".EGV");
      let totalEGV = sumaTotal(egv);
      let egm = document.querySelectorAll(".EGM");
      let totalEGM = sumaTotal(egm);
      let ban_tr = document.querySelectorAll(".BAN-TR");
      let totalBAN_TR = sumaTotal(ban_tr);
      let ban_tr_db = document.querySelectorAll(".BAN-TR-DB");
      let totalBAN_TR_db = sumaTotal(ban_tr_db);
      let ban_dp = document.querySelectorAll(".BAN-DP");
      let totalBAN_DP = sumaTotal(ban_dp);
      let ban_nc = document.querySelectorAll(".BAN-NC");
      let totalBAN_NC = sumaTotal(ban_nc);
      let ban_nctr = document.querySelectorAll(".BAN-TRANF");
      let totalBAN_TRANF = sumaTotal(ban_nctr);
      let ban_nd = document.querySelectorAll(".BAN-ND");
      let totalBAN_ND = sumaTotal(ban_nd);
      let ban_nd_ac = document.querySelectorAll(".BAN-ND-AC");
      let totalBAN_NC_AC = sumaTotal(ban_nd_ac);
      let saldo_anterior = document.getElementById("saldo_ant").value;
      let cheques = totalEG + totalEGV;
      let acreditados = totalBAN_TR + totalBAN_NC + totalBAN_TRANF;
      let debitados = totalBAN_ND + totalBAN_NC_AC + totalEGM + totalBAN_TR_db;
      let saldo_act = (totalBAN_DP + acreditados - cheques - debitados) + parseFloat(saldo_anterior);

      document.getElementById("cheques_pag").value = parseFloat(cheques).toFixed(2);
      document.getElementById("valor_acreditado").value = parseFloat(acreditados).toFixed(2);
      document.getElementById("depositos").value = parseFloat(totalBAN_DP).toFixed(2);
      document.getElementById("valor_debitado").value = parseFloat(debitados).toFixed(2);
      document.getElementById("saldo_act").value = parseFloat(saldo_act).toFixed(2);
    }
  }
  const sumaTotal = data => {
    let total = 0;
    for (let i = 0; i < data.length; i++) {
      total += parseFloat(data[i].value);
    }
    return total;
  }

  function devuelvefloat(cant, decimales) {
    var tmp = null;
    $.ajax({
      url: "{{route('transferenciabancaria.devuelvefloat')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'POST',
      datatype: 'json',
      async: false,
      data: {
        cantidad: cant,
        decimales: decimales
      },
      success: function(data) {
        tmp = data.valor;
      },
      error: function(data) {
        console.error(data.responseText);
      }
    });
    return tmp;
  }

  function redondeafinal(value, decimales = 3) {
    value = +value;
    if (isNaN(value)) return NaN; // Shift 
    value = value.toString().split('e');
    value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
    value = value.toString().split('e');
    return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
  }

  function guardar_mes(e) {
    e.preventDefault();
    $('#btn_guardar').prop("disabled", true);
    var saldo_anterior = $('#saldo_ant').val();
    var valor_depositos = $('#depositos').val();
    var valor_acreditado = $('#valor_acreditado').val();
    var valor_cheques = $('#cheques_pag').val();
    var valor_debitado = $('#valor_debitado').val();
    var tipo = $('#tipo_mes').val();
    var saldo_actual = $('#saldo_act').val();
    var fecha_hasta = $('#fecha_hasta').val();
    var banco = $('#banco').val();
    var error = '';
    if (banco == '') {
      error = error + `Seleccione un banco`;
    }
    if (error == '') {
      $.ajax({
        type: 'post',
        url: "{{route('conciliacionbancaria.guardar_mes')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        beforeSend: function() {
          Swal.fire({
            text: "Cargando...",
            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
            imageWidth: 200,
            imageHeight: 200,
            imageAlt: 'Custom image',
          })
        },
        data: {
          'saldo_anterior': saldo_anterior,
          'depositos': valor_depositos,
          'valor_acreditado': valor_acreditado,
          'cheques_pag': valor_cheques,
          'valor_debitado': valor_debitado,
          'saldo_actual': saldo_actual,
          'tipo': tipo,
          'fecha_hasta': fecha_hasta,
          'banco': banco
        },
        success: function(data) {
          if (data.respuesta == 'success') {
            guardar_pendientes(event);
          }
        },
        error: function(data) {}
      });
    } else {
      alertas('error', 'error', error);
    }
  }

  function guardar_pendientes(e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      url: "{{route('conciliacionbancaria.pendientes')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $('#tabla_conciliacion').serialize(),
      success: function(data) {
        Swal.close();
        alertas(data.respuesta, data.titulos, data.msj);
      },
      error: function(data) {}
    });
  }

  function select_check(id) {
    var check = document.getElementById("id_consiliacion_" + id);
    var check_c = document.getElementById("check_conc" + id);
    if (check.checked) {
      check_c.value = 1;
    } else {
      check_c.value = 0;
    }
  }
</script>
@endsection