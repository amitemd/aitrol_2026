<style>
    input[type=checkbox] {
        vertical-align: middle;
        position: relative;
        bottom: 1px;
        scale: 1.8;
    }

    th {
        text-align: center;
    }

    .valores {
        text-align: right;
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
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
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
                        <input type="hidden" id="editar_comprobante" value="{{ isset(config('data.comprobante')->id_proveedor) ? config('data.comprobante')->id_proveedor : '' }}">
                        <input type="hidden" id="editar_forma_pago" value="{{ isset(config('data.comprobante')->id_pago) ? config('data.comprobante')->id_pago : '' }}">
                        <input type="hidden" id="editar_sucursal" value="{{ isset(config('data.edit_sucursal')->id) ? config('data.edit_sucursal')->id : '' }}">
                        <input type="hidden" id="editar_banco" value="{{ isset(config('data.edit_banco')->id) ? config('data.edit_banco')->id : '' }}">
                        <input type="hidden" id="editar_fechacheque" value="{{ isset(config('data.comprobante')->fecha_cheque) ? config('data.comprobante')->fecha_cheque : '' }}">
                        <input type="hidden" id="editar_numerocheque" value="{{ isset(config('data.comprobante')->no_cheque) ? config('data.comprobante')->no_cheque : '' }}">
                        <input type="hidden" id="editar_valor" value="{{ isset(config('data.comprobante')->valor) ? config('data.comprobante')->valor : '' }}">
                        <form id="frm_comprobante">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.comprobante')->id) ? config('data.comprobante')->id : '' }}">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarComprobante" />
                            <input type="hidden" id="d" name="d" />
                            <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                            <input type="hidden" name="total_favor" id="total_favor">
                            <input type="hidden" name="superavit" id="superavit" value="0">
                            <input type="hidden" name="comprobarx" id="comprobarx" value="0">
                            <input class="form-control" type="hidden" name="total_suma" id="total_suma">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Información Acreedor</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="acreedores">Acreedor</label>
                                                    <select class="form-control select2" style="width: 100%;" id="nombre_acreedores" name="nombre_acreedores">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="direccion">Dirección</label>
                                                    <input type="text" class="form-control col-md-12 validar" name="direccion" id="direccion">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="ruc">Ruc</label>
                                                    <input type="text" class="form-control col-md-12 validar" name="ruc" id="ruc" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="ruc">Girado a</label>
                                                    <input type="text" class="form-control col-md-12 validar" name="girado" id="girado">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal">Fecha</label>
                                                    <input type="date" class="form-control col-md-12 validar" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" onchange="cambiarFecha(this)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Información Banco</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="formas_pago">Formas de Pago</label>
                                                    <select class="form-control select2" style="width: 100%;" id="forma_pago" name="forma_pago">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.formas_pago') != '' && count(config('data.formas_pago')) > 0) {
                                                            foreach (config('data.formas_pago') as $value) {
                                                                echo '<option ' . (isset(config('data.formas_pago')->id) ? (config('data.formas_pago')->id == $value->id ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->nombre . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="banco">Banco</label>
                                                    <select class="form-control select2" style="width: 100%;" id="banco" name="banco">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                            foreach (config('data.bancos') as $value) {
                                                                echo '<option ' . (isset(config('data.bancos')->catalogo) ? (config('data.bancos')->id == $value->id ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->nombre . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="divisas">Divisas</label>
                                                    <select class="form-control select2" style="width: 100%;" id="divisas" name="divisas">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                            foreach (config('data.divisas') as $value) {
                                                                echo '<option ' . (isset(config('data.divisas')->id) ? (config('data.divisas')->id == $value->id ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="fecha_cheque">Fecha Cheque</label>
                                                    <input type="date" class="form-control col-md-12 validar" name="fecha_cheque" id="fecha_cheque" value="<?= date('Y-m-d') ?>">
                                                </div>
                                                <!-- <div class="col-md-3">
                                                    <label class="font-normal" for="cambio">Cambio</label>
                                                    <input type="text" class="form-control col-md-12 validar valores" name="cambio" id="cambio" value="1.00" readonly>
                                                </div> -->
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="num_cheque">Num Cheque</label>
                                                    <input type="number" class="form-control col-md-12 validar text-right" name="num_cheque" id="num_cheque">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="valor">Valor</label>
                                                    <input type="text" class="form-control col-md-12 validar" name="valor" id="valor" onchange="aplicarDeuda()" onkeydown="changeStyle()" onkeypress="return onlyNumberKey(event)">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" class="cheque_entregado" style="display: block">Cheque entregado</label>
                                                    <input type="checkbox" name="cheque_entregado" id="cheque_entregado" @if (isset(config('data.comprobante')->check)) @if (config('data.comprobante')->check == 1) checked @endif
                                                    @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Información General</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="sucursal">Sucursal</label>
                                                    <select class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal" onchange="obtener_caja()">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        foreach (config('data.sucursales') as $row) {
                                                            echo '<option value="' . $row->id . '" >' . $row->codigo_sucursal . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="font-normal" class="concepto">Concepto </label>
                                                    <input id="concepto" name="concepto" class="form-control" placeholder="Ingrese el concepto" value="{{ isset(config('data.comprobante')->descripcion) ? config('data.comprobante')->descripcion : '' }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <input type="hidden" name="contador_total" id="contador_total" value="0">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th colspan="7" style="text-align: center">
                                                    ANTICIPOS REALIZADOS DE PROVEEDORES
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px;">Fecha</th>
                                                <th style="width: 300px;">Tipo </th>
                                                <th style="width: 160px;">Número</th>
                                                <th style="width: 160px;">Concepto</th>
                                                <th style="width: 160px;">Saldo</th>
                                                <th style="width: 160px;">Saldo Base</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mostrar_anticipos"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table id="detalleDeudasProveedor" class="table table-striped table-bordered table-hover dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th colspan="8" style="text-align: center">
                                                    Detalle de deudas del Proveedor
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px;">Vence</th>
                                                <th style="width: 150px;">Tipo</th>
                                                <th style="width: 200px;">Número</th>
                                                <!-- <th style="width: 200px;">Concepto</th> -->
                                                <th style="width: 200px;">Total</th>
                                                <th style="width: 150px;">Retención</th>
                                                <th style="width: 150px;">Saldo</th>
                                                <th style="width: 150px;">Abono</th>
                                                <th style="width: 150px;">Saldo Base</th>
                                                <th style="width: 200px;">Valor Retenciones Asumidas</th>
                                                <th style="width: 150px;">Retenciones Asumidas</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalleFactura">
                                            @if (!is_null(config('data.detalle')))
                                            @foreach (config('data.detalle') as $value)
                                            @php
                                            $suma_retenciones = 0;
                                            if(isset($value->compras->retenciones)){
                                            for ($i=0; $i < count($value->compras->retenciones) ; $i++) {
                                                $suma_retenciones += $value->compras->retenciones[$i]->total;
                                                }
                                                }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ config('data.comprobante')->fecha_comprobante }}
                                                    </td>
                                                    <td> COM-FA </td>
                                                    <td>
                                                        {{ $value->id_secuencia }}
                                                    </td>
                                                    <td>
                                                        @if(isset($value->compras)) {{ $value->compras->total_final }} @endif
                                                    </td>
                                                    <td>
                                                        {{$suma_retenciones}}
                                                    </td>
                                                    <td>
                                                        {{$value->saldo_base}}
                                                    </td>
                                                    <td>
                                                        {{$value->abono}}
                                                    </td>
                                                    <td>
                                                        {{$value->saldo_base - $value->abono}}
                                                    </td>
                                                    <td>
                                                        0.00
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" name="ret_asumidas" id="ret_asumidas" disabled>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                            @if (!isset(config('data.comprobante')->id))
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="font-normal" for="nota">Nota</label>
                                    <textarea id="nota" name="nota" class="form-control" >{{ isset(config('data.comprobante')->nota) ? config('data.comprobante')->nota : '' }}</textarea>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal" for="saldo_favor">Saldo a Favor</label>
                                    <input type="text" class="form-control col-md-12 validar valores" name="saldo_favor" id="saldo_favor" value="0.00">
                                </div>
                            </div>
                            @endif
                            <div style="margin-top: 2%">
                            </div>
                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="goBack('comprobantedeegreso')"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar</button>
                                <button type="button" class="btn btn-success" onclick="guardar()" id="btnguardarComprobante"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>