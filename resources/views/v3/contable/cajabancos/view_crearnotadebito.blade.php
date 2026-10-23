<style>
    .center {
        text-align: center
    }

    .colorselect {
        background-color: #A5A4A4 !important;
        color: white;
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.titulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_varios.inicio') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.titulo'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body hero-factura">
                        <form id="frm_depositoBancario">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.registroGu')->id)? config('data.registroGu')->id : '' }}">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarDepositoBancario" />
                            <input type="hidden" id="d" name="d" />
                            <div class="row hero-datosfactura">
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.tipo')}}</label>
                                    <input type="text" class="form-control" value="BAN-NC" readonly name="tipo" id="tipo">
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                    <input type="date" class="form-control" name="fecha_asiento" id="fecha_asiento" value="{{ isset(config('data.registroGu')->fecha_asiento) ? config('data.registroGu')->fecha_asiento : date('Y-m-d')}}">
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                    <input type="text" class="form-control" name="concepto" id="concepto" value="{{ isset(config('data.registroGu')->concepto) ? config('data.registroGu')->concepto : ''}}">
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.sucursal')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="sucursal_empresa" name="sucursal_empresa">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        if (config('data.sucursales') != null && count(config('data.sucursales')) > 0) {
                                            foreach (config('data.sucursales') as $row) {
                                                echo '<option value="' . $row->id . '">' . $row->codigo_sucursal . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div style="margin-top: 2%;">
                            </div>
                            <div class="row hero-datosfactura">
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.cuentaOrigen')}}</label>
                                    <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" readonly>
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        foreach (config('data.cuentas') as $row) {
                                            echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.nota')}}</label>
                                    <input type="text" class="form-control" name="nota" id="nota" value="{{ isset(config('data.registroGu')->nota) ? config('data.registroGu')->nota : ''}}">
                                </div>
                                <div class="col-md-3">
                                    <label for="id_cuenta_destino" class="font-normal">{{trans('v3/new_tabla.cuentaDestino')}}</label>
                                    <select name="id_cuenta_destino" id="id_cuenta_destino" class="form-control select2" style="height:100%">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        foreach (config('data.cajaBanco') as $row) {
                                            echo '<option ' . (isset(config('data.registroGu')->id_cuenta_destino) && config('data.registroGu')->id_cuenta_destino == $row->cuenta_mayor ? 'selected ' : '') . 'value="' . $row->cuenta_mayor . '">' . $row->nombre . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.marcarTodos')}}</label>
                                    <input type="text" class="form-control" name="valor1" id="valor1" step="0.01" onkeypress="return isNumber(event)" value="{{ isset(config('data.registroGu')->valor_destino) ? config('data.registroGu')->valor_destino : ''}}">
                                </div>
                            </div>
                            <div style="margin-top: 2%;"> </div>
                            <div class="row hero-datosfactura" style="gap: 10px">
                                <label class="btn btn-danger">
                                    <input type="checkbox" id="marcarTodo" onclick="seleccionar_todo(true, 'tbl_detalles')">
                                    {{trans('v3/new_tabla.totalEfectivo')}}
                                </label>
                                <label class="btn btn-primary">
                                    <input type="checkbox" id="desmarcarTodo" onclick="seleccionar_todo(false, 'tbl_detalles')">{{trans('v3/new_tabla.desenmarcarTodos')}}
                                </label>
                            </div>
                            <div style="margin-top:2%">
                            </div>
                            <div class="row">
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-10" style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{trans('v3/new_tabla.totalEfectivo')}}</th>
                                                <th>{{trans('v3/new_tabla.totalPapeletadeDepósito')}}</th>
                                                <th>{{trans('v3/new_tabla.totalcheques')}}</th>
                                                <th>{{trans('v3/new_tabla.totalTarjetas')}}</th>
                                                <th>{{trans('v3/new_tabla.totalDatalink')}}</th>
                                                <th>{{trans('v3/new_tabla.totalCruceCuentas')}}</th>
                                                <th>{{trans('v3/new_tabla.totalDeposito')}}</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input id="total_efectivo" type="text" class="form-control" style="text-align: right;" name="total_efectivo" value="{{ isset(config('data.registroGu')->total_efectivo) ? number_format(config('data.registroGu')->total_efectivo, 2, '.', '') : '' }}" readonly="true">
                                                </td>
                                                <td>
                                                    <input id="total_pap_deposito" type="text" class="form-control" style="text-align: right;" name="total_pap_deposito" readonly="true" value="{{ isset(config('data.registroGu')->total_pap_deposito) ? number_format(config('data.registroGu')->total_pap_deposito, 2, '.', '') : '' }}">
                                                </td>
                                                <td>
                                                    <input id="total_cheques" type="text" class="form-control" style="text-align: right;" name="total_cheques" readonly="true" value="{{ isset(config('data.registroGu')->total_cheque) ? number_format(config('data.registroGu')->total_cheque, 2, '.', '') : '0.00' }}">
                                                </td>
                                                <td>
                                                    <input id="total_tarjetas" type="text" class="form-control" style="text-align: right;" name="total_tarjetas" readonly="true" value="{{ isset(config('data.registroGu')->total_tarjeta) ? number_format(config('data.registroGu')->total_tarjeta, 2, '.', '') : '0.00' }}">
                                                </td>
                                                <td>
                                                    <input type="text" id="total_datalink" class="form-control" style="text-align: right;" name="total_datalink" readonly="true" value="0.00">
                                                </td>
                                                <td>
                                                    <input type="text" id="total_crucecuentas" class="form-control" style="text-align: right;" name="total_crucecuentas" readonly="true" value="0.00">
                                                </td>
                                                <td>
                                                    <input id="total_deposito" type="text" class="form-control" style="text-align: right;" name="total_deposito" readonly="true" value="{{ isset(config('data.registroGu')->total_deposito) ? number_format(config('data.registroGu')->total_deposito, 2, '.', '') : '0.00' }}">
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div style="margin-top:1%">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="center">
                                                    {{trans('v3/new_tabla.detalledeValoresRecibidosaDepositar')}}
                                                </th>
                                            <tr>
                                                <th class="center">{{trans('v3/new_tabla.fechaDesde')}}</th>
                                                <th class="center">{{trans('v3/new_tabla.filtrarHasta')}}</th>
                                                <th class="center">{{trans('v3/new_tabla.filtrarpor')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <td>
                                                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" onchange="buscar_forma_pago()" value="{{ date('Y-m-d') }}">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" onchange="buscar_forma_pago()" value="{{ date('Y-m-d') }}">
                                            </td>
                                            <td>
                                                <select class="js-example-basic-multiple" style="width:100%;" name="id_forma_pago[]" multiple="multiple" id="id_forma_pago" {{ isset(config('data.registroGu')->id) ? 'disabled' : '' }} onchange="buscar_forma_pago()">
                                                    <option value="">{{trans('v3/new_tabla.identificacion')}}</option>
                                                    <option value="T">{{trans('v3/new_tabla.todos')}}</option>
                                                    <option value="-1">{{trans('v3/new_tabla.general')}}</option>

                                                    <?php
                                                    foreach (config('data.formaPago') as $row) {
                                                        echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                                    }
                                                    ?>

                                                </select>
                                            </td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table id="tbl_ventas" class="table table-striped table-bordered table-hover dataTables-example" width="100%" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class="well-dark">
                                                <th>&nbsp;</th>
                                                <th>{{trans('v3/new_tabla.identificacion')}}</th>
                                                <th>{{trans('v3/new_tabla.tipodecomprobante')}}</th>
                                                <th>{{trans('v3/new_tabla.tipo')}}</th>
                                                <th>{{trans('v3/new_tabla.tipo')}}</th>
                                                <th>{{trans('v3/new_tabla.factura')}}</th>
                                                <th>{{trans('v3/new_tabla.referencia')}}</th>
                                                <th>{{trans('v3/new_tabla.banco')}}</th>
                                                <th>{{trans('v3/new_tabla.cuenta')}}</th>
                                                <th>{{trans('v3/new_tabla.cliente')}}</th>
                                                <th>{{trans('v3/new_tabla.divisa')}}</th>
                                                <th>{{trans('v3/new_tabla.importe')}}</th>
                                                <th>{{trans('v3/new_tabla.valor')}}</th>
                                                <th>{{trans('v3/new_tabla.valorBase')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_detalles" name="tbl_detalles">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="history.back()">
                                    <i class="fa fa-long-arrow-left" aria-hidden="true"></i>{{trans('v3/new_tabla.regresar')}}
                                </button>
                                <button type="button" class="btn btn-success" id="guardarDepositoBancario">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>{{trans('v3/new_tabla.guardar')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>