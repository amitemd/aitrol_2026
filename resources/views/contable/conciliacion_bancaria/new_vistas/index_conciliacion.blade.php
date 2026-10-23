@extends('contable.conciliacion_bancaria.new_vistas.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="col-12">
            <div class="box">
                <form id="frm_">
                    <input type="hidden" id="idMes" value="{{ $idMes ?? '' }}">
                    <div class="box-header">
                        <h3 class="panel-title">Nueva conciliación</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="{{route('newconciliacionbancaria.index')}}" class="btn btn-danger btn-sm" onclick=""><i class="fa fa-undo"></i> <span class="botones">Volver</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body hero-factura">
                        <div class="ibox-content">
                            {{ csrf_field() }}
                            {{-- <input type="hidden" id="action" value="{{route('newconciliacionbancaria.conciliacion_store')}}" /> --}}
                            <input type="hidden" id="d" name="d" />
                            <div class="row hero-datosfactura">
                                <div class="col-md-12">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ trans('v3/new_titulos.buscador') }}</h3>
                                        </div>
                                        <div id="panelBuscadorBody">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <!-- Banco -->
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{ trans('v3/new_varios.institucion') }}</label>
                                                        <input type="hidden" id="id_caja_banco" value="@if($idMes != null){{$parametros->id_caja_banco}}|{{$parametros->nombre}}|{{$parametros->numero_cuenta}}|{{$parametros->anio}}|{{$parametros->mes}}@endif" />
                                                        <select class="form-control select2" style="width: 100%;" id="instituciones" name="instituciones">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            @foreach($bancos as $value)
                                                            <option @if($value->id == $banco) selected @endif value="{{$value->id}}">
                                                                {{ $value->nombre }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- Periodo -->
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{ trans('v3/new_varios.periodo') }}</label>
                                                        <input type="month" id="fecha_periodo" name="fecha_periodo" class="form-control"
                                                            placeholder="aaaa-mm" value="{{ $fecha_periodo }}">
                                                    </div>

                                                    <!-- Botones -->
                                                    <div class="col-md-6">
                                                        <label class="font-normal">&nbsp;</label>
                                                        <div class="input-group">
                                                            <a href="javascript:;" class="btn btn-primary" onclick="buscarDatosConciliacion()">
                                                                <i class="fa fa-search"></i>
                                                                <span class="botones">{{ trans('v3/new_tabla.buscar') }}</span>
                                                            </a>
                                                            &nbsp;
                                                            <a href="javascript:;" class="btn btn-danger ocultar"
                                                                onclick="pdfDatosConciliacion()" style="display: none;">
                                                                <i class="fa fa-file-pdf-o"></i> PDF
                                                            </a>
                                                            &nbsp;
                                                            <a href="javascript:;" class="btn btn-success ocultar"
                                                                onclick="excelDatosConciliacion()" style="display: none;">
                                                                <i class="fa fa-file-excel-o"></i> Excel
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosdepositos">
                                <div class="col-md-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ trans('v3/new_varios.periodo') }}</h3>
                                        </div>
                                        <div id="panelPendientesBody">
                                            <div class="panel-body">
                                                <div class="row" id="htmlPendientes"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosresumen">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ trans('v3/new_titulos.saldoLibros') }}</h3>
                                        </div>
                                        <div id="panelResumenLibrosBody">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.saldoanterior') }}</label>
                                                        <input type="text" id="saldoAnteriorResumenLibros" name="saldoAnteriorResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.depositos') }}</label>
                                                        <input type="text" id="depositoResumenLibros" name="depositoResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.valoresacreditados') }}</label>
                                                        <input type="text" id="acreditadosResumenLibros" name="acreditadosResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.chequesgirados') }}</label>
                                                        <input type="text" id="chequesGiradosResumenLibros" name="chequesGiradosResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.valoresdebitados') }}</label>
                                                        <input type="text" id="debitadosResumenLibros" name="debitadosResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.saldoactual') }}</label>
                                                        <input type="text" id="saldoActualResumenLibros" name="saldoActualResumenLibros"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosresumen">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ trans('v3/new_titulos.saldoBancos') }}</h3>
                                        </div>
                                        <div id="panelResumenBancosBody">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.saldoanterior') }}</label>
                                                        <input type="text" id="saldoAnteriorResumenBancos" name="saldoAnteriorResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.depositos') }}</label>
                                                        <input type="text" id="depositoResumenBancos" name="depositoResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.valoresacreditados') }}</label>
                                                        <input type="text" id="acreditadosResumenBancos" name="acreditadosResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.chequesgirados') }}</label>
                                                        <input type="text" id="chequesGiradosResumenBancos" name="chequesGiradosResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.valoresdebitados') }}</label>
                                                        <input type="text" id="debitadosResumenBancos" name="debitadosResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{ trans('v3/new_titulos.saldoactual') }}</label>
                                                        <input type="text" id="saldoActualResumenBancos" name="saldoActualResumenBancos"
                                                            class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosfactura">
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ trans('v3/new_titulos.informaciongeneral') }}</h3>
                                        </div>
                                        <div id="panelDatosFacturaBody">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <!-- Columna izquierda 9 -->
                                                    <div class="col-md-9" style="text-align: center;">
                                                        <table style="width: 80%; text-align: center; margin: 0 auto;">
                                                            <tr>
                                                                <td colspan="2">
                                                                    <table style="width: 100%; text-align: center;">
                                                                        <tr>
                                                                            <!-- Saldo Inicial -->
                                                                            <td style="text-align: center; font-size: 30px; display: flex; gap: 10px; justify-content: center; align-items: center;">
                                                                                $ <input type="text" style="width: 150px; font-size: 20px;"
                                                                                    id="lblSaldoInicial" onblur="calcularDatosConciliacion();"
                                                                                    class="form-control text-right" readonly value="0.00" />
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <!-- Total Egresos -->
                                                                            <td style="text-align: center; font-size: 30px;">
                                                                                <span id="lblTotalEgresos">$ 0.00</span>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <!-- Total Ingresos -->
                                                                            <td style="text-align: center; font-size: 30px;">
                                                                                <span id="lblTotalIngresos">$ 0.00</span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center; font-size: 18px;">
                                                                                {{ trans('v3/new_tabla.saldoinicial') }}
                                                                            </td>
                                                                            <td style="text-align: center; font-size: 30px;">-</td>

                                                                            <td style="text-align: center; font-size: 18px;">
                                                                                {{ trans('v3/new_tabla.egresos') }}
                                                                            </td>
                                                                            <td style="text-align: center; font-size: 30px;">+</td>

                                                                            <td style="text-align: center; font-size: 18px;">
                                                                                {{ trans('v3/new_tabla.ingresos') }}
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <!-- Columna derecha 3 -->
                                                    <div class="col-md-3">
                                                        <table style="width: 100%;">
                                                            <tr>
                                                                <td>
                                                                    <a class="btn btn-primary" href="javascript:;" onclick="finalizarConciliacion()">
                                                                        <i class="fa fa-floppy-o"></i>
                                                                        {{ trans('v3/new_tabla.finalizar') }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align: center;">
                                                                    <table style="width: 100%;">
                                                                        <tr>
                                                                            <td style="text-align: center; font-size: 30px;">
                                                                                <i class="fa fa-exclamation-triangle fa-2x" style="color: orange;"></i>
                                                                                <span id="lblDiferencia">$ 0.00</span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center; font-size: 18px; text-decoration: underline; text-decoration-style: dotted;">
                                                                                {{ trans('v3/new_tabla.diferencia') }}
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosdepositos">
                                <div class="col-md-12">
                                    <div class="panel panel-success">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.depositos') }}
                                            </h3>
                                        </div>
                                        <div id="panelDepositosBody">
                                            <div class="panel-body">
                                                <div class="row" id="depositos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosvaloresacreditados">
                                <div class="col-md-12">
                                    <div class="panel panel-success">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.transferenciasacreditadas') }}
                                            </h3>
                                        </div>

                                        <div id="panelTransferenciasAcreditadasBody">
                                            <div class="panel-body">
                                                <div class="row" id="transferenciasacreditadas"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosvaloresdebitados">
                                <div class="col-md-12">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.transferenciasadebitadas') }}
                                            </h3>
                                        </div>
                                        <div id="panelTransferenciasDebitadasBody">
                                            <div class="panel-body">
                                                <div class="row" id="transferenciasadebitadas"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosdebitos">
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.notasdedebito') }}
                                            </h3>

                                        </div>

                                        <div id="panelDebitosBody">
                                            <div class="panel-body">
                                                <div class="row" id="debitos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datoscredito">
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.notasdecredito') }}
                                            </h3>
                                        </div>
                                        <div id="panelCreditosBody">
                                            <div class="panel-body">
                                                <div class="row" id="creditos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosdebitosbancarios">
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.debitosbancarios') }}
                                            </h3>
                                        </div>
                                        <div id="panelDebitosBancariosBody">
                                            <div class="panel-body">
                                                <div class="row" id="debitoBancario"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosegresos">
                                <div class="col-md-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.egresos') }}
                                            </h3>
                                        </div>

                                        <div id="panelEgresosBody">
                                            <div class="panel-body">
                                                <div class="row" id="egresos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosegresosvarios">
                                <div class="col-md-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.egresosvarios') }}
                                            </h3>
                                        </div>
                                        <div id="panelEgresosVariosBody">
                                            <div class="panel-body">
                                                <div class="row" id="egresosVarios"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-datosegrsosmasivos">
                                <div class="col-md-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.egresosmasivos') }}
                                            </h3>
                                        </div>
                                        <div id="panelEgresosMasivosBody">
                                            <div class="panel-body">
                                                <div class="row" id="egresosMasivos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hero-pendientesagregados">
                                <div class="col-md-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left" style="font-size:16px;">
                                                {{ trans('v3/new_titulos.pendientesAgregados') }}
                                            </h3>
                                        </div>

                                        <div id="panelPendientesAgregadosBody">
                                            <div class="panel-body">
                                                <div class="row" id="htmlPendientesAgregados"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        if ($('#idMes').val() !== undefined && $('#idMes').val() != '') {
            if ($('#id_caja_banco').val() != '') {
                var datosBanco = $('#id_caja_banco').val().split('|');
                console.log(datosBanco)
                $('#instituciones')
                    .html('<option value="' + datosBanco[0] + '">' + datosBanco[1] + '</option>')
                    .trigger('change');
                $('#fecha_periodo').val(datosBanco[3] + '-' + datosBanco[4]);
                buscarDatosConciliacion();
                $('.cheDepositos').prop('disabled', true);
                $('.cheTransferenciasAcreditadas').prop('disabled', true);
                $('.cheTransferenciasDebitadas').prop('disabled', true);
                $('.cheNotaDebito').prop('disabled', true);
                $('.cheNotasCredito').prop('disabled', true);
                $('.cheNotaDebitoBancaria').prop('disabled', true);
                $('.cheEgresos').prop('disabled', true);
                $('.cheEgresosVarios').prop('disabled', true);
                $('.cheEgresosMasivos').prop('disabled', true);
            }
        }
    });

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function buscarDatosConciliacion() {
        if ($('#instituciones').val() == '') {
            return alertas('error', 'Error', 'Seleccione un banco o institución financiera');
        }
        var data = {
            banco: $('#instituciones').val(),
            documento: $('#tipo_documento').val(),
            estado: $('#estado').val(),
            periodo: $('#fecha_periodo').val(),
        };

        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.infoConciliacion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                var html = response.split('¬');
                $('#lblSaldoInicial').attr('readonly', false);
                $('#depositos').html(html[0]);
                $('#transferenciasacreditadas').html(html[1]);
                $('#debitos').html(html[2]);
                $('#creditos').html(html[3]);
                $('#debitoBancario').html(html[4]);
                $('#egresos').html(html[5]);
                $('#egresosVarios').html(html[6]);
                $('#egresosMasivos').html(html[7]);
                $('#lblSaldoInicial').val(html[10]);
                $('#saldoAnteriorResumenLibros').val(html[9]);
                $('#htmlPendientes').html(html[12]);
                $('#htmlPendientesAgregados').html(html[13]);
                $('#transferenciasadebitadas').html(html[14]);
                var totalesLibros = html[11].split('|');
                conciliado = totalesLibros[8];
                $('#depositoResumenLibros').val(totalesLibros[0]);
                $('#acreditadosResumenLibros').val(parseFloat(parseFloat(totalesLibros[1]) + parseFloat(totalesLibros[3])).toFixed(2));
                $('#chequesGiradosResumenLibros').val(parseFloat(parseFloat(totalesLibros[5]) + parseFloat(totalesLibros[6])).toFixed(2));
                $('#debitadosResumenLibros').val(parseFloat(parseFloat(totalesLibros[2]) + parseFloat(totalesLibros[4]) + parseFloat(totalesLibros[7]) + parseFloat(totalesLibros[9])).toFixed(2));
                $('#saldoActualResumenLibros').val(parseFloat(parseFloat(html[9]) + parseFloat(totalesLibros[0]) + parseFloat(totalesLibros[1]) + parseFloat(totalesLibros[3]) - (parseFloat(totalesLibros[2]) + parseFloat(totalesLibros[4]) + parseFloat(totalesLibros[7])) - (parseFloat(totalesLibros[5]) + parseFloat(totalesLibros[6]) + parseFloat(totalesLibros[9]))).toFixed(2));
                calcularDatosConciliacion();
                if (conciliado == 1) {
                    $('.cheDepositos').prop('disabled', true);
                    $('.cheTransferenciasAcreditadas').prop('disabled', true);
                    $('.cheTransferenciasDebitadas').prop('disabled', true);
                    $('.cheNotaDebito').prop('disabled', true);
                    $('.cheNotasCredito').prop('disabled', true);
                    $('.cheNotaDebitoBancaria').prop('disabled', true);
                    $('.cheEgresos').prop('disabled', true);
                    $('.cheEgresosVarios').prop('disabled', true);
                    $('.cheEgresosMasivos').prop('disabled', true);
                    $('.chePendientesAgregados').prop('disabled', true);
                }
                $('.ocultar').css('display', 'initial');
            },
            error: function(data) {}
        })
    }

    function calcularDatosConciliacion() {
        var saldoInicialBancos = $('#lblSaldoInicial').val();
        var totalPendientes = $('#totalPendientes').html();
        var totalPendientesAgregados = $('#totalPendientesAgregados').html();
        var totalDepositos = $('#totalDepositos').html();
        var totalNotaDebito = $('#totalNotaDebito').html();
        var totalNotaCredito = $('#totalNotaCredito').html();
        var totalTransferenciasAcreditadas = $('#totalTransferenciasAcreditadas').html();
        var totalTransferenciasDebitadas = $('#totalTransferenciasDebitadas').html();
        var totalDebitoBancario = $('#totalDebitoBancario').html();
        var totalEgresos = $('#totalEgresos').html();
        var totalEgresosVarios = $('#totalEgresosVarios').html();
        var totalEgresosMasivos = $('#totalEgresosMasivos').html();
        var ingresos = (parseFloat(parseFloat(totalDepositos) + parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito)));
        var egresos = parseFloat(parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresos) + parseFloat(totalEgresosVarios) + parseFloat(totalEgresosMasivos) + parseFloat(totalTransferenciasDebitadas));
        $('#lblTotalIngresos').html('$ ' + parseFloat(ingresos).toFixed(2));
        $('#lblTotalEgresos').html('$ ' + parseFloat(egresos).toFixed(2));
        var diferencia = 0;
        $('#saldoAnteriorResumenBancos').val(parseFloat(saldoInicialBancos).toFixed(2));
        $('#depositoResumenBancos').val(parseFloat(totalDepositos).toFixed(2));
        $('#acreditadosResumenBancos').val(parseFloat(parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito)).toFixed(2));
        $('#chequesGiradosResumenBancos').val(parseFloat(parseFloat(totalEgresos) + parseFloat(totalEgresosVarios)).toFixed(2));
        $('#debitadosResumenBancos').val(parseFloat(parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresosMasivos) + parseFloat(totalTransferenciasDebitadas)).toFixed(2));
        $('#saldoActualResumenBancos').val(parseFloat(parseFloat(saldoInicialBancos) + parseFloat(totalDepositos) + parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito) - (parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresosMasivos)) - (parseFloat(totalTransferenciasDebitadas)) - (parseFloat(totalEgresos) + parseFloat(totalPendientes) + parseFloat(totalPendientesAgregados) + parseFloat(totalEgresosVarios))).toFixed(2));

        diferencia = (parseFloat($('#saldoActualResumenBancos').val()) - parseFloat($('#saldoActualResumenLibros').val()).toFixed(2));

        $('#lblDiferencia').html('');
        var parent1 = '';
        var parent2 = '';
        if (diferencia != 0) {
            $('.fa-check-circle')
                .removeClass('fa-check-circle')
                .addClass('fa-exclamation-triangle')
                .css({
                    color: 'orange'
                });
        } else {
            $('.fa-exclamation-triangle')
                .removeClass('fa-exclamation-triangle')
                .addClass('fa-check-circle')
                .css({
                    color: 'green'
                });
        }
        if (diferencia < 0) {
            parent1 = '(';
            parent2 = ')';
            diferencia = diferencia * -1;
        }
        $('#lblDiferencia').html('$ ' + parent1 + parseFloat(diferencia).toFixed(2) + parent2);
    }

    function calcularTransferenciasAcreditadas(id, valor) {
        var totalTransferenciasAcreditadas = $('#totalTransferenciasAcreditadas').html();
        if ($('#chetransferencia_' + id).is(':checked')) {
            totalTransferenciasAcreditadas = parseFloat(totalTransferenciasAcreditadas) + parseFloat(valor);
        } else {
            totalTransferenciasAcreditadas = parseFloat(totalTransferenciasAcreditadas) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };

        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionTransferencia')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalTransferenciasAcreditadas < 0)
                    totalTransferenciasAcreditadas = 0;
                $('#totalTransferenciasAcreditadas').html(parseFloat(totalTransferenciasAcreditadas).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularDepositos(id, valor) {
        var totalDepositos = $('#totalDepositos').html();
        if ($('#chedeposito_' + id).is(':checked')) {
            totalDepositos = parseFloat(totalDepositos) + parseFloat(valor);
        } else {
            totalDepositos = parseFloat(totalDepositos) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionDeposito')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalDepositos < 0)
                    totalDepositos = 0;
                $('#totalDepositos').html(parseFloat(totalDepositos).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularTransferenciasDebitadas(id, valor, tipo) {
        var totalTransferenciasDebitadas = $('#totalTransferenciasDebitadas').html();
        if ($('#chetransferencia_' + id).is(':checked')) {
            totalTransferenciasDebitadas = parseFloat(totalTransferenciasDebitadas) + parseFloat(valor);
        } else {
            totalTransferenciasDebitadas = parseFloat(totalTransferenciasDebitadas) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val(),
            tipo: tipo
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionTransferencia')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalTransferenciasDebitadas < 0)
                    totalTransferenciasDebitadas = 0;
                $('#totalTransferenciasDebitadas').html(parseFloat(totalTransferenciasDebitadas).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularNotaDebito(id, valor) {
        var totalNotaDebito = $('#totalNotaDebito').html();
        if ($('#chenotaDebito_' + id).is(':checked')) {
            totalNotaDebito = parseFloat(totalNotaDebito) + parseFloat(valor);
        } else {
            totalNotaDebito = parseFloat(totalNotaDebito) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionNotaDebito')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalNotaDebito < 0)
                    totalNotaDebito = 0;
                $('#totalNotaDebito').html(parseFloat(totalNotaDebito).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularNotaCredito(id, valor) {
        var totalNotaCredito = $('#totalNotaCredito').html();
        if ($('#chenotaCredito_' + id).is(':checked')) {
            totalNotaCredito = parseFloat(totalNotaCredito) + parseFloat(valor);
        } else {
            totalNotaCredito = parseFloat(totalNotaCredito) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionNotaCredito')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalNotaCredito < 0)
                    totalNotaCredito = 0;
                $('#totalNotaCredito').html(parseFloat(totalNotaCredito).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularDebitoBancario(id, valor) {
        var totalDebitoBancario = $('#totalDebitoBancario').html();
        if ($('#chedebitobancario_' + id).is(':checked')) {
            totalDebitoBancario = parseFloat(totalDebitoBancario) + parseFloat(valor);
        } else {
            totalDebitoBancario = parseFloat(totalDebitoBancario) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionDebitoBancario')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalDebitoBancario < 0)
                    totalDebitoBancario = 0;
                $('#totalDebitoBancario').html(parseFloat(totalDebitoBancario).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularEgresos(id, valor) {
        var totalEgresos = $('#totalEgresos').html();
        if ($('#cheEgresos_' + id).is(':checked')) {
            totalEgresos = parseFloat(totalEgresos) + parseFloat(valor);
        } else {
            totalEgresos = parseFloat(totalEgresos) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionEgresos')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalEgresos < 0)
                    totalEgresos = 0;
                $('#totalEgresos').html(parseFloat(totalEgresos).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularEgresosVarios(id, valor) {
        var totalEgresosVarios = $('#totalEgresosVarios').html();
        if ($('#cheEgresosVarios_' + id).is(':checked')) {
            totalEgresosVarios = parseFloat(totalEgresosVarios) + parseFloat(valor);
        } else {
            totalEgresosVarios = parseFloat(totalEgresosVarios) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionEgresosVarios')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalEgresosVarios < 0)
                    totalEgresosVarios = 0;
                $('#totalEgresosVarios').html(parseFloat(totalEgresosVarios).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularEgresosMasivos(id, valor) {
        var totalEgresosMasivos = $('#totalEgresosMasivos').html();
        if ($('#cheEgresosMasivos_' + id).is(':checked')) {
            totalEgresosMasivos = parseFloat(totalEgresosMasivos) + parseFloat(valor);
        } else {
            totalEgresosMasivos = parseFloat(totalEgresosMasivos) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionEgresosMasivos')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalEgresosMasivos < 0)
                    totalEgresosMasivos = 0;
                $('#totalEgresosMasivos').html(parseFloat(totalEgresosMasivos).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularPendientesAgregados(id, valor) {
        var totalPendientes = $('#totalPendientesAgregados').html();
        if ($('#chePendientesAgregados_' + id).is(':checked')) {
            totalPendientes = parseFloat(totalPendientes) + parseFloat(valor);
        } else {
            totalPendientes = parseFloat(totalPendientes) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionPendientes')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalPendientes < 0)
                    totalPendientes = 0;
                $('#totalPendientesAgregados').html(parseFloat(totalPendientes).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function calcularPendientes(id, valor) {
        var totalPendientes = $('#totalPendientes').html();
        if ($('#chePendientes_' + id).is(':checked')) {
            totalPendientes = parseFloat(totalPendientes) + parseFloat(valor);
        } else {
            totalPendientes = parseFloat(totalPendientes) - parseFloat(valor);
        }
        var data = {
            id: id,
            fecha_periodo: $('#fecha_periodo').val(),
            id_banco: $('#instituciones').val()
        };
        $.ajax({
            type: 'get',
            url: "{{route('newconciliacionbancaria.verificarConciliacionPendientes')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'html',
            data: data,
            success: function(response) {
                if (totalPendientes < 0)
                    totalPendientes = 0;
                $('#totalPendientesAgregados').html(parseFloat(totalPendientes).toFixed(2));
                calcularDatosConciliacion();
            },
            error: function(data) {}
        })
    }

    function finalizarConciliacion() {
        if ($('#instituciones').val() == '') {
            return alertas('info', '', 'Debe seleccionar la institución...');
        } else if ($('#fecha_periodo').val() == '') {
            return alertas('info', '', 'Debe seleccionar la el periodo...');
        }
        $.ajax({
            type: 'post',
            url: "{{route('newconciliacionbancaria.conciliacion_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#frm_').serialize(),
            success: function(data) {
                console.log(data.status);
                alertas(data.status, data.status, data.message);
                if (data.status == 'success') {
                    setTimeout(function() {
                        location.reload();
                    }, 200);
                }
            },
            error: function(data) {
                alertas(data.status, data.status, data.message);
            }
        });
    }
</script>

@endsection