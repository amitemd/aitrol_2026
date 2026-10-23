@extends('contable.conciliacion_bancaria.new_vistas.base')
@section('action-content')
<section>
    <div class="box">
        <div class="box-body">

            <div style="background-color: white; border-radius: 8px; padding: 15px">
                <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                    <h3 class="text-3xl font-semibold text-gray-800">Agregar Conciliación</h3>

                    <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
                        <div class="text-center md:w-auto w-full">
                            Nueva Conciliación
                        </div>
                        <div class="text-center md:w-auto w-full">

                        </div>
                        <div class="text-end md:w-auto w-full">
                        </div>
                    </div>
                </div>
                <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="col-md-12 mt-4">
                    <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
                </div>
                <form method="POST" action="{{route('activosfijos.index_transacciones')}}" id="complaint_buscador">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="form-group col-md-4 col-xs-6">
                            <label for="fecha" class="col-md-12 control-label">{{trans('contableM.institucion')}}:</label>
                            <div class="col-md-12">
                                <select name="id_banco" id="id_banco" class="form-control">
                                    <option value="">Seleccione</option>
                                    @foreach($bancos as $banco)
                                    <option value="{{$banco->id}}">{{$banco->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-xs-6">
                            <label for="fecha_hasta" class="col-md-12 control-label">{{trans('contableM.periodo')}}:</label>
                            <div class="col-md-12">
                                <input type="month" class="form-control" id="fecha_periodo" name="fecha_periodo" value="" placeholder="aaaa-mm" />
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-xs-6">
                            <div class="col-md-12">
                                <button type="submit" class="bg-blue-400 py-[6px] px-3 transition-all duration-300  hover:bg-blue-600 text-white rounded w-full md:w-auto" id="boton_buscar">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('laboratorio.buscar')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div>
                    <div style="background-color: #e34164ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">{{trans('Kconciliacion.PendientesdeConciliar')}}</p>
                    </div>
                    <table id="tbl_pendientes" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="5%">&nbsp;</th>
                                <th width="5%">{{trans('Kconciliacion.Fecha')}}</th>
                                <th width="10%">{{trans('Kconciliacion.tipo')}}</th>
                                <th width="10%">{{trans('contableM.Idasiento')}}</th>
                                <th width="35%">{{trans('Kconciliacion.Detalle')}}</th>
                                <th width="10%">{{trans('Kconciliacion.Valor')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <table width="100%" class='table table-bordered table-striped table-hover dataTable'>
                            <thead>
                                <tr>
                                    <th colspan="6">SALDO LIBROS</td>
                                </tr>
                                <tr>
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
                                    <td><input id="saldo_ant" style="text-align: right; width: 100%" type="text" class="form-control" value="" name="saldo_anterior" readonly></td>
                                    <td><input id="depositos" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="depositos" readonly></td>
                                    <td><input id="valor_acreditado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_acreditado" readonly></td>
                                    <td><input id="cheques_pag" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="cheques_pag" readonly></td>
                                    <td><input id="valor_debitado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_debitado" readonly></td>
                                    <td><input id="saldo_act" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="saldo_actual" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <table width="100%" class='table table-bordered table-striped table-hover dataTable'>
                            <thead>
                                <tr>
                                    <th colspan="6">SALDO BANCOS</td>
                                </tr>
                                <tr>
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
                                    <td><input id="saldo_ant" style="text-align: right; width: 100%" type="text" class="form-control" value="" name="saldo_anterior" readonly></td>
                                    <td><input id="depositos" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="depositos" readonly></td>
                                    <td><input id="valor_acreditado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_acreditado" readonly></td>
                                    <td><input id="cheques_pag" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="cheques_pag" readonly></td>
                                    <td><input id="valor_debitado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_debitado" readonly></td>
                                    <td><input id="saldo_act" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="saldo_actual" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Depositos Bancarios</p>
                    </div>
                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <td style="text-align:center;">...</td>
                                <th>{{trans('Kconciliacion.Fecha')}}</th>
                                <th>{{trans('Kconciliacion.tipo')}}</th>
                                <th>{{trans('contableM.Idasiento')}}</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_det_deposito" name="tbl_detalles">
                            @php $depositoTotal = 0; @endphp
                            @if ($deposito_bancario->count() > 0)
                            @foreach ($deposito_bancario as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $depositoTotal += $d->valor_destino;
                            }
                            @endphp
                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" class="cheDepositos" onchange="calcularDepositos('{{ $d->id }}', '{{ $d->valor_destino }}')" id="chedeposito_{{ $d->id }}" {{ $d->check_ }}>
                                        <label for="chedeposito_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_asiento)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor_destino, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalDepositos">{{ number_format($depositoTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Transferencias Acreditadas</p>
                    </div>
                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableTransferencia">
                            @php $transferenciaTotal = 0; @endphp
                            @if ($transferenciasAcreditadas->count() > 0)
                            @foreach ($transferenciasAcreditadas as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $transferenciaTotal += $d->valor_destino;
                            }
                            @endphp
                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheTransferenciasAcreditadas"
                                            onchange="calcularTransferenciasAcreditadas('{{ $d->id }}', '{{ $d->valor_destino }}')"
                                            id="chetransferencia_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="chetransferencia_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_asiento)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor_destino, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalTransferenciasAcreditadas">{{ number_format($transferenciaTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Transferencias Debitadas</p>
                    </div>
                    @php $transferenciaTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tableTransferencia">
                            @if ($transferenciasDebitadas->count() > 0)
                            @foreach ($transferenciasDebitadas as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $transferenciaTotal += $d->valor_destino;
                            }
                            @endphp
                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheTransferenciasDebitadas"
                                            onchange="calcularTransferenciasDebitadas('{{ $d->id }}', '{{ $d->valor_destino }}')"
                                            id="chetransferencia_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="chetransferencia_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_asiento)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor_destino, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalTransferenciasDebitadas">{{ number_format($transferenciaTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Notas de Debito</p>
                    </div>
                    @php $notaDebitoTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableNotaDebito">
                            @if ($notasDebito->count() > 0)
                            @foreach ($notasDebito as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $notaDebitoTotal += $d->valor;
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheNotaDebito"
                                            onchange="calcularNotaDebito('{{ $d->id }}', '{{ $d->valor }}')"
                                            id="chenotaDebito_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="chenotaDebito_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalNotaDebito">{{ number_format($notaDebitoTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Notas de Credito</p>
                    </div>
                    @php $notaCreditoTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tableNotaCredito">
                            @if ($notaCredito->count() > 0)
                            @foreach ($notaCredito as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $notaCreditoTotal += $d->valor;
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheNotasCredito"
                                            onchange="calcularNotaCredito('{{ $d->id }}', '{{ $d->valor }}')"
                                            id="chenotaCredito_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="chenotaCredito_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalNotaCredito">{{ number_format($notaCreditoTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Notas de Debito bancaria</p>
                    </div>
                    @php $notaDebitoBancarioTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableNotaDebito">
                            @if ($notasDebitoBancaria->count() > 0)
                            @foreach ($notasDebitoBancaria as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $notaDebitoBancarioTotal += $d->valor;
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheNotaDebitoBancaria"
                                            onchange="calcularDebitoBancario('{{ $d->id }}', '{{ $d->valor }}')"
                                            id="chedebitobancario_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="chedebitobancario_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalDebitoBancario">{{ number_format($notaDebitoBancarioTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Egresos</p>
                    </div>
                    @php $egresoTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableEgresos">
                            @if ($egresos->count() > 0)
                            @foreach ($egresos as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $egresoTotal += $d->valor;
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheEgresos"
                                            onchange="calcularEgresos('{{ $d->id }}', '{{ $d->valor }}')"
                                            id="cheEgresos_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="cheEgresos_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_comprobante)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento_cabecera }}</td>
                                <td>{{ $d->descripcion }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalEgresos">{{ number_format($egresoTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Egresos Varios</p>
                    </div>
                    @php $egresosVariosTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableNotaDebito">
                            @if ($egresosVarios->count() > 0)
                            @foreach ($egresosVarios as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $egresosVariosTotal += $d->valor;
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheEgresosVarios"
                                            onchange="calcularEgresosVarios('{{ $d->id }}', '{{ $d->valor }}')"
                                            id="cheEgresosVarios_{{ $d->id }}"
                                            {{ $d->check_ }}>
                                        <label for="cheEgresosVarios_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_comprobante)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento_cabecera }}</td>
                                <td>{{ $d->descripcion }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalEgresosVarios">{{ number_format($egresosVariosTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <div style="background-color: #71dea0ff; border-radius: 5px; margin-bottom: 15px;" class="col-md-12">
                        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Egresos Masivos</p>
                    </div>
                    @php $egresosMasivosTotal = 0; @endphp

                    <table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
                        <thead>
                            <tr>
                                <th style="text-align:center;">...</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asiento</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                            </tr>
                        </thead>

                        <tbody id="tableNotaDebito">
                            @if ($egresosMasivos->count() > 0)
                            @foreach ($egresosMasivos as $d)
                            @php
                            if ($d->check_ == 'checked') {
                            $egresoMasivoDato = '';
                            $egresosMasivosTotal += $d->valor_pago;
                            } else {
                            $egresoMasivoDato = '<input type="hidden" id="datoEgresoMasivo_|' . $d->id . '|' . $d->id_asiento_cabecera . '|' . $d->fecha_comprobante . '|' . $d->secuencia . '|' . $d->beneficiario . '|' . $d->no_cheque . '" name="datoEgresoMasivo_|' . $d->id . '|' . $d->id_asiento_cabecera . '|' . $d->fecha_comprobante . '|' . $d->secuencia . '|' . $d->beneficiario . '|' . $d->no_cheque . '" />';
                            }
                            @endphp

                            <tr>
                                <td style="text-align:center;">
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="checkbox"
                                            class="cheEgresosMasivos"
                                            onchange="calcularEgresosMasivos('{{ $d->id }}', '{{ $d->valor_pago }}', '{{ $d->id_asiento_cabecera }}', '{{ $d->fecha_comprobante }}', '{{ $d->secuencia }}', '{{ $d->beneficiario }}', '{{ $d->no_cheque }}')"
                                            id="cheEgresosMasivos_{{ $d->id }}"
                                            {!! $d->check_ !!}
                                        >
                                        <label for="cheEgresosMasivos_{{ $d->id }}">{!! $egresoMasivoDato !!}</label>
                                    </div>
                                    <div id="datoEgresoMasivo_{{ $d->id }}"></div>
                                </td>
                                <td style="text-align:center;">{{ date('Y-m-d', strtotime($d->fecha_comprobante)) }}</td>
                                <td style="text-align:center;">{{ $d->tipo }}</td>
                                <td style="text-align:center;">{{ $d->id_asiento_cabecera }}</td>
                                <td>{{ $d->descripcion }}</td>
                                <td style="text-align:right;">{{ number_format($d->valor_pago, 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right; font-weight:bold;">
                                    Total: <span id="totalEgresosMasivos">{{ number_format($egresosMasivosTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">

                </div>
            </div>
        </div>
    </div>

</section>
@endsection