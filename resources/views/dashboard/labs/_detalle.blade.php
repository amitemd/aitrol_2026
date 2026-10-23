@php
    $titulos = [
        'facturado' => 'Órdenes facturadas',
        'emitido' => 'Órdenes emitidas pendientes de facturar',
        'cotizacion' => 'Órdenes en cotización',
        'inconsistencias' => 'Órdenes con inconsistencias',
        'todos' => 'Detalle consolidado de órdenes',
    ];
    $parametros = request()->query();
@endphp

<div class="labs-detail-toolbar">
    <form class="form-inline" id="labs-detail-search">
        <div class="form-group">
            <label class="sr-only" for="labs-busqueda">Buscar</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="search" class="form-control" id="labs-busqueda" name="busqueda" value="{{ request('busqueda') }}" placeholder="Orden, paciente, seguro, doctor o factura">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <a class="btn btn-success js-detail-export" href="{{ route('dashboard.labs.exportar', $parametros) }}"><i class="fa fa-file-excel-o"></i> Exportar CSV</a>
    </form>
    <div class="labs-detail-summary">
        <strong>{{ isset($titulos[$tipoDetalle]) ? $titulos[$tipoDetalle] : $titulos['todos'] }}</strong>
        <span>{{ number_format($ordenes->total()) }} órdenes encontradas</span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover labs-detail-table">
        <thead>
            <tr>
                @foreach([
                    'fecha' => 'Fecha', 'orden' => 'Orden', 'paciente' => 'Paciente', 'seguro' => 'Seguro',
                    'doctor' => 'Doctor', 'subtotal' => 'Subtotal', 'descuento' => 'Descuento',
                    'total' => 'Total', 'factura' => 'Factura', 'estado' => 'Estado', 'creador' => 'Creador'
                ] as $columna => $titulo)
                    @php
                        $ordenParams = $parametros;
                        $ordenParams['orden'] = $columna;
                        $ordenParams['direccion'] = $ordenActual === $columna && $direccionActual === 'asc' ? 'desc' : 'asc';
                        unset($ordenParams['page']);
                    @endphp
                    <th><a href="{{ route('dashboard.labs.detalle', $ordenParams) }}">{{ $titulo }} @if($ordenActual === $columna)<i class="fa fa-sort-{{ $direccionActual === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                @endforeach
                <th>Control de calidad</th>
            </tr>
        </thead>
        <tbody>
        @forelse($ordenes as $orden)
            <tr class="{{ $orden->inconsistencia !== '' ? 'has-issue' : '' }}">
                <td class="nowrap">{{ date('d/m/Y H:i', strtotime($orden->fecha_orden)) }}</td>
                <td><strong>#{{ $orden->id }}</strong></td>
                <td>{{ $orden->paciente }}<small>{{ $orden->identificacion }}</small></td>
                <td>{{ $orden->seguro }}</td>
                <td>{{ $orden->doctor }}</td>
                <td class="text-right nowrap">${{ number_format($orden->subtotal, 2) }}</td>
                <td class="text-right nowrap">${{ number_format($orden->descuento, 2) }}</td>
                <td class="text-right nowrap"><strong>${{ number_format($orden->total, 2) }}</strong></td>
                <td>{{ $orden->factura }}</td>
                <td><span class="labs-state state-{{ strtolower($orden->estado_texto) }}">{{ $orden->estado_texto }}</span></td>
                <td>{{ $orden->creador }}</td>
                <td>
                    @if($orden->inconsistencia !== '')
                        <span class="labs-issue"><i class="fa fa-exclamation-triangle"></i> {{ $orden->inconsistencia }}</span>
                    @else
                        <span class="labs-ok"><i class="fa fa-check"></i> Conforme</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="12" class="labs-empty">No existen órdenes para los filtros seleccionados.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="labs-pagination">
    <div>Mostrando {{ $ordenes->firstItem() ?: 0 }} a {{ $ordenes->lastItem() ?: 0 }} de {{ $ordenes->total() }}</div>
    <div>{{ $ordenes->links() }}</div>
</div>
