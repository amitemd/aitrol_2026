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
                        <form id="frm_cruceValores">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.comprobante')->id) ? config('data.comprobante')->id : '' }}">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarCruceValores" />
                            <input type="hidden" id="d" name="d" />
                            <input type="hidden" name="total_suma_a" id="total_suma_a">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <input type="hidden" name="saldoax" id="saldoax">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">CRUCE DE VALORES A FAVOR</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="fecha">Fecha</label>
                                                    <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="proveedor">Acreedor</label>
                                                    <select class="form-control select2" style="width: 100%;" id="proveedor" name="proveedor" onchange="cambiar_nombre_proveedor()">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.proveedores') != '' && count(config('data.proveedores')) > 0) {
                                                            foreach (config('data.proveedores') as $value) {
                                                                echo '<option ' . (isset(config('data.cruce')->id_proveedor) ? (config('data.cruce')->id_proveedor == $value->id ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->razonsocial . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="concepto">Concepto</label>
                                                    <input type="text" name="concepto" id="concepto" class="form-control" value="{{ isset(config('data.cruce')->detalle) ? config('data.cruce')->detalle : '' }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="sucursalc">Sucursal</label>
                                                    <select class="form-control select2" style="width: 100%;" id="sucursalc" name="sucursalc">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        foreach (config('data.sucursales') as $row) {
                                                            echo '<option ' . (isset(config('data.cruce')->sucursal) ? (config('data.cruce')->sucursal == $row->id ? 'selected' : '') : '') . ' value="' . $row->id . '">' . $row->codigo_sucursal . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="margin-top: 2%">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="overflow-x: auto;">
                                                    <input type="hidden" name="contador_total" id="contador_total" value="0">
                                                    <table id="tablaValores" class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="7" style="text-align: center">
                                                                    DETALLE DE VALORES A FAVOR
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th style="width: 150px;">Emisión</th>
                                                                <th style="width: 300px;">Tipo</th>
                                                                <th style="width: 160px;">Número</th>
                                                                <th style="width: 160px;">Concepto</th>
                                                                <th style="width: 160px;">Div</th>
                                                                <th style="width: 160px;">Saldo</th>
                                                                <th style="width: 160px;">Abono</th>
                                                                <th style="width: 160px;">Nuevo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="mostrar_detalles_valor">
                                                            @if (isset(config('data.cruce')->detallePagoCruceProv))
                                                            @foreach (config('data.cruce')->detallePagoCruceProv as $value)
                                                            <tr>
                                                                <td>{{ $value->fecha }}</td>
                                                                <td><b>ACR-EG</b></td>
                                                                <td>{{ $value->numero }}</td>
                                                                <td>{{ $value->concepto }}</td>
                                                                <td>$</td>
                                                                <td>{{ $value->valor_ant }}</td>
                                                                <td>{{ $value->valor }}</td>
                                                                @php $nuevo_saldo= $value->valor_ant-$value->valor; @endphp
                                                                <td>{{ number_format($nuevo_saldo, 2) }}</td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div style="margin-top:2%">
                                            </div>
                                            <div style="display: flex;justify-content:end">
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="valores">Total</label>
                                                    <input type="text" class="form-control col-md-12 validar valores" name="valores" id="valores" value="{{ isset(config('data.cruce')->total_disponible) ? config('data.cruce')->total_disponible : '0.00' }}" readonly>
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
                                    <input type="hidden" name="contador" id="contador" value="0">
                                    <table id="tableDeudasProveedor" class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th colspan="9" style="text-align: center">
                                                    DETALLE DE DEUDAS DEL PROVEEDOR
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px;">Vence</th>
                                                <th style="width: 150px;">Tipo</th>
                                                <th style="width: 200px;">Número</th>
                                                <th style="width: 200px;">Concepto</th>
                                                <th style="width: 200px;">Div</th>
                                                <th style="width: 150px;">Saldo</th>
                                                <th style="width: 150px;">Abono</th>
                                                <th style="width: 150px;">Saldo Base</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalleDeudasProveedor">
                                            @if (isset(config('data.cruce')->detalleCruce))
                                            @foreach (config('data.cruce')->detalleCruce as $values)
                                            <tr>
                                                <td>{{ $values->fecha }}</td>
                                                <td> <b>{{ $values->tipo }}</b></td>
                                                <td> {{ $values->secuencia_factura }}</b> </td>
                                                <td> {{ $values->observaciones }}</td>
                                                <td> $ </td>
                                                <td> {{ $values->total_factura }} </td>
                                                <td> {{ $values->total }} </td>
                                                @php $total = $values->total_factura - $values->total @endphp
                                                <td> {{ $total }} </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            @php $cont=0; @endphp
                                            @foreach (range(1, 1) as $i)
                                            <tr>
                                                <td> <input class="form-control" type="text" name="vence{{ $cont }}" id="vence{{ $cont }}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="tipo{{ $cont }}" id="tipo{{ $cont }}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="numero{{ $cont }}" id="numero{{ $cont }}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="concepto{{ $cont }}" id="concepto{{ $cont }}" readonly> </td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{ $cont }}" id="div{{ $cont }}" value="$" readonly></td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="saldo{{ $cont }}" id="saldo{{ $cont }}" readonly> </td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{ $cont }}" id="abono{{ $cont }}" readonly></td>
                                                <td> <input class="form-control" style="text-align: left;" type="text" name="nuevo_saldo{{ $cont }}" id="nuevo_saldo{{ $cont }}" readonly></td>
                                            </tr>
                                            @php $cont = $cont +1; @endphp
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                            <div class="row" style="display:flex;justify-content:end">
                                <div class="col-md-8" style="overflow-x: auto;">
                                    <table id="tablaDetalleCalculos" class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 150px;">Total Deudas</th>
                                                <th style="width: 200px;">Total Abonos</th>
                                                <th style="width: 200px;">Nuevo Saldo</th>
                                                <th style="width: 200px;">Déficit de ingresos</th>
                                                <th style="width: 150px;">Superavit a favor</th>
                                            </tr>
                                        </thead>
                                        <tbody id="calculosDetalle">
                                            <tr>
                                                <td>
                                                    <input class="form-control" type="text" name="total_deudas" id="total_deudas" value="0.00" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="total_abono" id="total_abono" value="0.00" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="nuevos_saldo" id="nuevos_saldo" value="0.00" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="deficit_ingreso" id="deficit_ingreso" style="color: red" value="0.00" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="supervit_afavor" id="supervit_afavor" style="color: blue" value="0.00" readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="regresar()"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar</button>
                                @if (!isset(config('data.cruce')->id))
                                <button type="button" class="btn btn-success" id="guardarCruceValores"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>